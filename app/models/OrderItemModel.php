<?php

    use Services\OrderService;
    use Services\StockService;

    load(['OrderService','StockService'], SERVICES);

    class OrderItemModel extends Model
    {
        public $table = 'order_items';

        public $_fillables = [
            'order_id',
            'item_id',
            'quantity',
            'price',
            'sold_price',
            'discount_price',
            'remarks'
        ];

        public function __construct()
        {
            parent::__construct();
            $this->order = model('OrderModel');
        }

        public function deleteItem($id) {
            return parent::delete($id);
        }
        public function addOrUpdatePurchaseItem($orderItemData, $id = null) {
            $orderItemData = parent::getFillablesOnly($orderItemData);

            if($this->_validateEntry($orderItemData))
                return false;

            if($this->checkItemStock($orderItemData['item_id'], $orderItemData['quantity']) === FALSE) {
                return false;
            }

            if(is_null($id)) {
                $purchaseSession = OrderService::getPurchaseSession();
                if (empty($purchaseSession)) {
                    $purchaseSession = OrderService::startPurchaseSession();
                    $order_id = $this->order->start($purchaseSession, whoIs('id'));
                } else {
                    $order_id = $this->order->getBySession($purchaseSession)->id ?? '';
                    if(!$order_id) {
                        $order_id = $this->order->start($purchaseSession, whoIs('id'));
                    }
                }

                $orderItemData['sold_price'] = $this->_calculateSoldPrice($orderItemData);
                $orderItemData['order_id'] = $order_id;

                return parent::store($orderItemData);
            } else {
                $orderItemData['sold_price'] = $this->_calculateSoldPrice($orderItemData);
                return parent::update($orderItemData, $id);
            }
        }

        private function _validateEntry($orderItemData) {
            if(empty($orderItemData['item_id'])){
                $this->addError("Item should not be empty");
                return false;
            }
        }

        private function _calculateSoldPrice($orderItemData) {
            $soldPrice = $orderItemData['quantity'] * $orderItemData['price'];

            if($orderItemData['discount_price']) {
                $soldPrice = $soldPrice - $orderItemData['discount_price'];
            }

            return $soldPrice;
        }

        public function getCurrentSession() {
            $purchaseSession = OrderService::getPurchaseSession();
            $this->db->query(
                "SELECT item.name, item.sku, oi.*
                    FROM {$this->table} as oi 

                    LEFT JOIN items as item 
                    ON item.id = oi.item_id
                    
                    WHERE oi.order_id = 
                    (SELECT id from orders where session_id = '{$purchaseSession}')"
            );

            return $this->db->resultSet();
        }

        public function resetPurchaseSession() {
            $purchaseSession = OrderService::endPurchaseSession();
            OrderService::startPurchaseSession();
            // $this->order->reset();
        }

        public function getItemSummary($items = []) {
            $retVal = [
                'discountAmount' => 0,
                'grossAmount' => 0,
                'netAmount' =>  0
            ];

            if (!empty($items)) {
                foreach ($items as $key => $row) {
                    $retVal['discountAmount'] += $row->discount_price;
                    $retVal['grossAmount'] += $row->price;
                    $retVal['netAmount'] += $row->sold_price;
                }
            }
            return $retVal;
        }

        public function getItemTotal($items) {
            return $this->getItemSummary($items)['netAmount'];
        }

        public function getCurrentSessionId() {
            $purchaseSession = OrderService::getPurchaseSession();

            if(!$purchaseSession)
                return false;
            
            return $this->order->single([
                'session_id' => $purchaseSession
            ])->id ?? null;
        }

        public function getOrderItems($id) {
            $this->db->query(
                "SELECT item.name, item.sku, oi.*
                    FROM {$this->table} as oi 

                    LEFT JOIN items as item 
                    ON item.id = oi.item_id
                    
                    WHERE oi.order_id = '{$id}'"
            );
            return $this->db->resultSet();
        }

        public function checkItemStock($itemId, $quantity) {
            $this->stock = model('StockModel');
            $stockTotal = $this->stock->getItemStock($itemId);
            if ($stockTotal < $quantity) {
                $this->addError("No stock available");
                return false;
            }
            return true;
        }

        public function deductStock($itemId, $quantity) {
            $this->stock = model('StockModel');
            $this->stock->createOrUpdate([
                'item_id' => $itemId,
                'quantity' => $quantity,
                'remarks'  => 'Sales item deduction',
                'date' => now(),
                'entry_type' => StockService::ENTRY_DEDUCT,
                'entry_origin' => StockService::SALES
            ]);
        }
    }