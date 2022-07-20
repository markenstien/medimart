<?php

    use Services\OrderService;
    load(['OrderService'], SERVICES);

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

        public function addOrUpdatePurchaseItem($orderItemData, $id = null) {
            $orderItemData = parent::getFillablesOnly($orderItemData);

            if($this->_validateEntry($orderItemData))
                return false;

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
    }