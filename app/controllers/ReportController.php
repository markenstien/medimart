<?php

	use Classes\Report\SalesReport;
	load(['SalesReport'],CLASSES.DS.'Report');
	class ReportController extends Controller
	{

		public function __construct()
		{
			parent::__construct();
			$this->orderItemModel = model('OrderItemModel');
		}

		public function salesReport() {

			$salesReport = new SalesReport();

			$saleItems = $this->orderItemModel->getItemsByParam();
			$highestSellingInQuantity = $this->orderItemModel->getLowestOrHighest(
				null, OrderItemModel::CATEGORY_QUANTITY,'desc'
			);
			$lowestSellingInQuantity = $this->orderItemModel->getLowestOrHighest(
				null, OrderItemModel::CATEGORY_QUANTITY,'asc'
			);
			$highestSellingInAmount = $this->orderItemModel->getLowestOrHighest(
				null, OrderItemModel::CATEGORY_AMOUNT,'desc'
			);
			$lowestSellingInAmount = $this->orderItemModel->getLowestOrHighest(
				null, OrderItemModel::CATEGORY_AMOUNT,'asc'
			);

			$salesReport->setItems($saleItems);
			$salesSummary = $salesReport->getSummary();

			$this->data['page_title'] = 'Sales Report';

			$this->data['reportData'] = [
				'saleItems' => $saleItems,
				'highestSellingInQuantity' => $highestSellingInQuantity,
				'lowestSellingInQuantity' => $lowestSellingInQuantity,
				'highestSellingInAmount' => $highestSellingInAmount,
				'lowestSellingInAmount' => $lowestSellingInAmount,
				'salesSummary' => $salesSummary,
				'today' => now(),
				'user'  => whoIs(['firstname','lastname'])
			];
			return $this->view('report/sales_report', $this->data);
		}

		public function stocksReport() {
			$this->data['page_title'] = 'Sales Report';
			return $this->view('report/stock_report', $this->data);
		}

		public function pettyCashReport() {
			$this->data['page_title'] = 'Petty Cash Report';
			return $this->view('report/petty_cash', $this->data);
		}

		public function ledgerReport() {
			$this->data['page_title'] = 'Petty Cash Report';
			return $this->view('report/ledger_report', $this->data);
		}
		

		public function create()
		{	

			return $this->view('report/index');
			$request_params = request()->inputs();

			$report_sections = $request_params['report_sections'] ?? [];


			if( !empty($request_params) )
			{
				if(empty($report_sections))
				{
					Flash::set("You must select atleast one report section");
					return request()->return();
				}

				$start_date = $request_params['start_date'];
				$end_date = $request_params['end_date'];

				if( empty($start_date) || empty($end_date) )
				{
					Flash::set("Start date and End date must not be empty" , 'danger');
					return request()->return();
				}
				$ret_val = [
					'total_miled_cases' => 0,
					'total_moderate_cases' => 0,
					'total_severe_cases' => 0,
					'total_deployed_cases' => 0,
					'total_record'   => 0,

					'recovered_cases' => 0,
					'total_death'  => 0,
					'number_of_hospital_quarantine' => 0,
					'number_of_home_quarantine' => 0,
				];

				$patient_record_model = model('PatientRecordModel');

				$deploy_model = model('DeployModel');

				$db = Database::getInstance();

				$db->query(
					"SELECT * FROM laboratory_results
						WHERE date_requested between '{$start_date}' and '{$end_date}' "
				);

				$laboratories = $db->resultSet();

				foreach($laboratories as $lab) {

					if( isEqual($lab->severity , 'mild') )
						$ret_val['total_miled_cases']++;

					if( isEqual($lab->severity , 'moderate') )
						$ret_val['total_moderate_cases']++;

					if( isEqual($lab->severity , 'severe') )
						$ret_val['total_severe_cases']++;
				}

				$records = $patient_record_model->getAll([

					'where' => [
						'date' => [
							'condition' => 'between',
							'value'     => [$start_date , $end_date]
						],
						'first_name' => [
							'condition' => 'not null'
						]
					]
					
				]);

				$types = [];

				if( isEqual('home_quarantine' , $report_sections))
					$types [] = 'home-quarantine';
					

				if( isEqual('hospital_qurantine' , $report_sections) )
					$types [] = 'hospital';


				$where_types = null;


				$where_param = [
					'deployment_date' => [
						'condition' => 'between',
						'value'     => [$start_date , $end_date],
						'concatinator' => ' AND '
					]
				];

				if( !empty($types) )
				{
					$where_types = [
						'deploy.type' => [
							'condition' => 'in',
							'value' => $types
						]
					];

					$where_param = array_merge($where_param,$where_types);
				}

				$deployments = $deploy_model->getAll([
					'where' => $where_param
				]);

				foreach($deployments as $key => $row) 
				{
					if( isEqual($row->release_remarks , 'recovered') ){
						$ret_val['recovered_cases']++;
					}else if( isEqual($row->release_remarks, 'deceased')) {
						$ret_val['total_death']++;
					}


					if( !is_null($row->hospital_id) ){
						$ret_val['number_of_hospital_quarantine']++;
					}else{
						$ret_val['number_of_home_quarantine']++;
					}
				}

				if($deployments)
					$ret_val['total_deployed_cases'] = count($deployments);

				if($records)
					$ret_val['total_record'] = count($records);

				$this->data['items'] = [
					'laboratories' => $laboratories,
					'records' => $records,
					'deployments' => $deployments,
					'summary' => $ret_val
				];
			}

			$this->data['title'] = 'Report';

			$this->data['report_sections'] = [
				'number_of_cases' => 'Number of Cases',
				'summary_of_severity' => 'Summary of Severity',
				'summary_of_quarantine' => 'Summary of Quarantine',
				'recovered_cases' => 'Recovered Cases',
				'deceased_cases' => 'Deceased Cases',
				'home_quarantine' => 'Home Quarantine',
				'hospital_quarantine' => 'Hospital Quarantine',
				'patients' => 'Patients'
			];

			$this->data['report_section_selected'] = $report_sections ?? [];

			return $this->view('report/create' , $this->data);
		}
	}