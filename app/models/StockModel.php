<?php

    use Services\StockService;
    load(['StockService'], SERVICES);

    class StockModel extends Model
    {
        public $table = 'stocks';
        public $_fillables = [
            'supply_order_id',
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

        public function getItemStock($itemid) {
            $this->db->query(
                "SELECT sum(quantity) as total_stock
                    FROM {$this->table}
                    WHERE item_id = '{$itemid}'
                    GROUP BY item_id"
            );
            return $this->db->single()->total_stock ?? 0;
        }

        public function getStocks($params = []) {
            $this->db->query(
                "SELECT item.* , stock.total_stock,
                    CASE
                        WHEN stock.total_stock <= item.min_stock
                            THEN 'LOW STOCK LEVEL'
                        WHEN stock.total_stock >= item.max_stock
                            THEN 'HIGH STOCK LEVEL'
                        ELSE 'NORMAL STOCK LEVEL'
                    END AS stock_level
                FROM items as item
                LEFT JOIN (
                    SELECT ifnull(sum(quantity),0) as total_stock, item_id
                        FROM stocks
                        GROUP BY item_id
                ) as stock
                ON stock.item_id = item.id
                "
            );

            return $this->db->resultSet();
        }
    }