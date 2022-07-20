<?php

    use Form\PurchaseItemForm;
    use Services\OrderService;

    load(['PurchaseItemForm'], APPROOT.DS.'Form');
    load(['OrderService'], APPROOT.DS.'services');
    class TransactionController extends Controller
    {
        
        public function __construct()
        {
            $this->model = model('OrderItemModel');
        }

        /**
         * purchasing action
         */
        public function purchase() {
            $request = request()->inputs();

            if (isSubmitted()) {
                $res = $this->model->addOrUpdatePurchaseItem($request);
                return request()->return();
            }

            $items = $this->model->getCurrentSession();
            
            $this->data['purchase_item_form'] = new PurchaseItemForm();
            $this->data['items'] = $items;
            $this->data['session'] = OrderService::getPurchaseSession();
            return $this->view('transaction/purchase',$this->data);
        }

        public function purchaseResetSession(){
            csrfValidate();
            $this->model->resetPurchaseSession();
            return redirect(_route('transaction:purchase'));
        }
    }