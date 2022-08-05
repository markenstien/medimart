<?php
	use Services\OrderService;
	load(['OrderService'],SERVICES);
	class DashboardController extends Controller
	{
		public function __construct()
		{
			$this->user_model = model('UserModel');
		}

		public function index()
		{
			$this->data['page_title'] = 'Dashboard';
			
			$orderService = new OrderService();
			$orderAmountTotal = $orderService->getServiceOver30days(date('Y-m-d'));
			$this->data['orderAmountTotal'] = $orderAmountTotal;
			return $this->view('dashboard/index', $this->data);
		}
	}