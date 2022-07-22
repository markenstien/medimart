<?php

    use Form\PaymentForm;
    use Form\PurchaseItemForm;
    use Services\OrderService;

    load(['PurchaseItemForm','PaymentForm'], APPROOT.DS.'Form');
    load(['OrderService'], APPROOT.DS.'services');
    class TransactionController extends Controller
    {
        
        public function __construct()
        {
            $this->model = model('OrderItemModel');
            $this->paymentForm = new PaymentForm();
        }

        /**
         * purchasing action
         */
        public function purchase() {
            $request = request()->inputs();

            if (isSubmitted()) {
                $res = $this->model->addOrUpdatePurchaseItem($request, $request['id'] ?? null);
                Flash::set("Item added");
                return redirect(_route('transaction:purchase'));
            }

            $items = $this->model->getCurrentSession();
            
            $purchaseItemForm = new PurchaseItemForm();

            if (isset($request['action'], $request['id'])) {
                if ($request['action'] == 'edit_item') {
                    $item = $this->model->get($request['id']);
                }
                $purchaseItemForm->setValueObject($item);
                $purchaseItemForm->addItem($item->item_id);
            }
            $this->data['items'] = $items;
            $this->data['session'] = OrderService::getPurchaseSession();
            //get total
            $totalAmountToPay = 0;
            foreach($items as $key => $row) {
                $totalAmountToPay += $row->sold_price;
            }
            $this->paymentForm->setValue('amount',$totalAmountToPay);
            $this->data['totalAmountToPay'] = $totalAmountToPay;
            $this->data['purchase_item_form'] = $purchaseItemForm;
            $this->data['paymentForm'] = $this->paymentForm;

            return $this->view('transaction/purchase',$this->data);
        }

        public function purchaseResetSession(){
            csrfValidate();
            $this->model->resetPurchaseSession();
            return redirect(_route('transaction:purchase'));
        }


        public function savePayment() {
            $request = request()->inputs();
            if(isSubmitted()) {
                dd($request);
            }
        }
    }