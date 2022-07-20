<?php
    use Form\StockForm;
    load(['StockForm'], APPROOT.DS.'form');

    class StockController extends Controller
    {
        public function __construct()
        {
            $this->data['stock_form'] = new StockForm();
            $this->model = model('StockModel');
        }
        public function index() {

        }

        public function addStock() {
            $request = request()->inputs();

            if(isSubmitted()) {
                $res = $this->model->createOrUpdate($request);

                if($res) {
                    Flash::set("Stock added");
                    return redirect(_route('item:show', $request['item_id']));
                } else {
                    Flash::set($this->model->getErrorString(), 'danger');
                    return request()->return();
                }
            }

            //required fields
            
            if(!isset($request['item_id'])) {
                Flash::set("Invalid Request",'danger');
                csrfValidate();
            }


            $this->data['stock_form']->setValue('item_id', $request['item_id']);
            return $this->view('stock/add_stock', $this->data);
        }
    }