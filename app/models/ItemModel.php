<?php
    load(['CategoryService'],SERVICES);
    use Services\CategoryService;

    class ItemModel extends Model
    {
        public $table = 'items';
        public $_fillables = [
            'name',
            'sku',
            'barcode',
            'cost_price',
            'sell_price',
            'min_stock',
            'max_stock',
            'category_id',
            'variant',
            'remarks',
            'is_visible'
        ];
        
        public function createOrUpdate($itemData, $id = null) {
            $retVal = null;
            $_fillables = $this->getFillablesOnly($itemData);
            $item = $this->getItemByUniqueKey($itemData['sku'], $itemData['name']);

            if (!is_null($id)) {
                if($item && ($item->id != $id)) {
                    $this->addError("SKU Or Name Already exists");
                    return false;
                }
                $retVal = parent::update($_fillables, $id);
            } else {
                if($item) {
                    $this->addError("SKU Or Name Already exists");
                    return false;
                }
                $retVal = parent::store($_fillables);
            }

            return $retVal;
        }

        public function getImages($id) {
            $attachModel = model('AttachmentModel');
            return $attachModel->all([
                'global_id' => $id,
                'global_key' => CategoryService::ITEM
            ]);
        }


        private function getItemByUniqueKey($sku,$name) {
            return parent::single([
                'sku' => [
                    'condition' => 'equal',
                    'value' => $sku,
                    'concatinator' => 'OR'
                ],
                'name' => [
                    'condition' => 'equal',
                    'value' => $name
                ],
            ]);
        }

    }