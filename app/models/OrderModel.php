<?php

    class OrderModel extends Model
    {
        public $table = 'orders';

        public function start($sessionId, $staffId) {

            return parent::store([
                'reference' => 'ORDR-' . random_number(10),
                'session_id' => $sessionId,
                'staff_id' => $staffId
            ]);
        }

        public function getBySession($sessionId) {
            return parent::single([
                'session_id' => $sessionId
            ]);
        }
    }