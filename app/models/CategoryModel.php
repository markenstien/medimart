<?php

    class CategoryModel extends Model
    {
        public $table = 'categories';

        public $_fillables = [
            'name',
            'category',
            'active'
        ];

        public function createOrUpdate($categoryData, $id = null) {
            $_fillables = parent::getFillablesOnly($categoryData);
            if (!is_null($id)) {
                $this->addMessage(parent::$MESSAGE_UPDATE_SUCCESS);
                return parent::update($_fillables, $id);
            } else {
                $_fillables['active'] = true;
                $this->addMessage(parent::$MESSAGE_CREATE_SUCCESS);
                return parent::store($_fillables);
            }
        }
    }