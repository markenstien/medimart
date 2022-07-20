<?php

    use Services\StockService;
    load(['StockService'], SERVICES);

    class StockModel extends Model
    {
        public $table = 'stocks';
        public $_fillables = [
            'item_id',
            'quantity',
            'remarks',
            'date',
            'purchase_order_id',
            'entry_origin',
            'entry_type',
            'created_by'
        ];
        public function createOrUpdate($stockData, $id = null) {
            $_fillables = $this->getFillablesOnly($stockData);
            $quantity = $this->_convertQuantity($_fillables);

            if(!$quantity) return false;

            $_fillables['quantity'] = $quantity;

            if (!is_null($id)) {
                return parent::update($_fillables, $id);
            }
            return parent::store($_fillables);
        }

        private function _convertQuantity($entryData) {
            $quantity = $entryData['quantity'];
            if ($entryData['quantity'] <= 0) {
                $this->addError("Invalid Quantity Amount");
                return false;
            }
            if ($entryData['entry_type'] == StockService::ENTRY_DEDUCT) {
                $quantity = $entryData['quantity'] * (-1);
            }

            return $quantity;
        }

        public function getProductLogs($itemId,$params = []) {
            
            $params['condition']['item_id'] = $itemId;
            return parent::all($params['condition'], $params['order_by'] ?? 'id desc', $params['limit'] ?? null);
        }
    }