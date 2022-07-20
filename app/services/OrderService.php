<?php
    namespace Services;

use Session;
    class OrderService {
        public static function startPurchaseSession(){
            $token = get_token_random_char(20);
            Session::set('purchase', $token);
            return $token;
        }

        public static function endPurchaseSession(){
            Session::remove('purchase');
        }

        public static function getPurchaseSession(){
            return Session::get('purchase');
        }
    }