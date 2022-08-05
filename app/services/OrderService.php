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

        public function getServiceOver30days($endDate) {
            $startDate30Days = date('Y-m-d',strtotime($endDate.'-30 days'));
            $orderModel = model('OrderModel');
            $orderItemModel = model('OrderItemModel');

            $orders = $orderModel->all([
                'created_at' => [
                    'condition' => 'between',
                    'value' => [$startDate30Days, $endDate]
                ]
            ]);

            if(!$orders) {
                return 0;
            }
            $orderIds = [];
            foreach($orderIds as $key => $row) {
                $orderIds[] = $row->id;
            }

            $orderItems = $orderItemModel->all([
                'order_id' =>[
                    'condition' => 'in',
                    'value' => $orderIds
                ]
            ]);
            $summary = $orderItemModel->getItemSummary($orderItems);
            return $summary['netAmount'];
        }
    }