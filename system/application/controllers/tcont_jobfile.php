<?php
include "base.php";
class Tcont_jobfile extends Base {
	function Tcont_jobfile()
	{
			parent::Base();	
			$this->load->model("gpsmodel");
			$this->load->model("vehiclemodel");
			$this->load->model("configmodel");
			$this->load->helper('common_helper');
			$this->load->helper('kopindosat');
			
	}
	
	function index(){
		
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->params['sortby'] = "job_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Data Hourmeter List";
				
		$this->params["content"] =  $this->load->view('jobfile/jobfile_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_jobfile(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$datetime = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobfile = isset($_POST['jobfile']) ? $_POST['jobfile'] : "";
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : 0;
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : 0;
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : 0;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$client = isset($_POST['client']) ? $_POST['client'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		$this->params["vehicle"] = $row_vehicle;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		$this->dbtransporter->order_by("transporter_job_id", "desc");
		$this->dbtransporter->where("transporter_job_flag", 0);
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("transporter_job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("transporter_job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "transporter_job_number":
				$this->dbtransporter->where("transporter_job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "transporter_job_po":
				$this->dbtransporter->where("transporter_job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "transporter_job_vehicle_no":
				$this->dbtransporter->where("transporter_job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "transporter_job_status":
				$this->dbcar->where("transporter_job_status", $status);				
			break;			
	
		}
		
		$this->dbtransporter->join("driver", "driver_id = transporter_job_driver", "left");
		$q = $this->dbtransporter->get("tcont_jobfile", $this->config->item("limit_records_tcont"), $offset);
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("transporter_job_id", "desc");
		$this->dbtransporter->where("transporter_job_flag", 0);
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("transporter_job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("transporter_job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "transporter_job_number":
				$this->dbtransporter->where("transporter_job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "transporter_job_po":
				$this->dbtransporter->where("transporter_job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "transporter_job_vehicle_no":
				$this->dbtransporter->where("transporter_job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "transporter_job_status":
				$this->dbcar->where("transporter_job_status", $status);				
			break;				
	
		}
		
		$qt = $this->dbtransporter->get("tcont_jobfile");
		$rt = $qt->row();
		$total = $rt->total;
			
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
	
		$html = $this->load->view('jobfile/jobfile_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add_jobfile()
	{
        $company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;

		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
	
		$this->params["content"] = $this->load->view('jobfile/jobfile_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_jobfile()
	{
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$datetime = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobfile = isset($_POST['jobfile']) ? $_POST['jobfile'] : "";
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : "0";
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : "0";
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : "0";
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$client = isset($_POST['client']) ? $_POST['client'] : "";
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		
		$error = "";
		if ($jobfile == "")
		{
			$error .= "- Please Select Job File \n";	
		}else{
			
			$this->dbtransporter->where("transporter_job_number", $jobfile);
			$this->dbtransporter->where("transporter_job_flag", 0);
			$qj = $this->dbtransporter->get("tcont_jobfile");
			if($qj->num_rows() > 0 && $id== 0){
				$error .= "- Job File already exist \n";
			
			}
		}
		if ($startdate == "")
		{
			$error .= "- Please Select Date \n";	
		}
		if ($nopo == "")
		{
			$error .= "- Please Input No. PO \n";	
		}
		/* if ($vehicle_id == "")
		{
			$error .= "- Your Vehicle ID is null \n";	
		}
		if ($vehicle_device == "")
		{
			$error .= "- Your Vehicle Device is null \n";	
		}
		if ($vehicle_name == "")
		{
			$error .= "- Your VEhicle Name is null \n";	
		} */
		if ($driver == "")
		{
			$error .= "- Please Input Driver \n";	
		}
		if ($area == "")
		{
			$error .= "- Please Select Area \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $vehicle_id);
			$qv = $this->db->get("vehicle");
			$vehicles = $qv->row();
			$this->db->close();
			
		unset($data);
			
            $data['transporter_job_vehicle_id'] = $vehicle_id;
			$data['transporter_job_vehicle_name'] = $vehicles->vehicle_name;
            $data['transporter_job_vehicle_device'] = $vehicles->vehicle_device;
			$data['transporter_job_vehicle_no'] = $vehicles->vehicle_no;
			$data['transporter_job_driver'] = $driver;
			$data['transporter_job_number'] = $jobfile;
			$data['transporter_job_po'] = $nopo;
            $data['transporter_job_dimensi_p'] = $dimensi_p;
			$data['transporter_job_dimensi_l'] = $dimensi_l;
			$data['transporter_job_dimensi_t'] = $dimensi_t;
			$data['transporter_job_weight'] = $weight;
			$data['transporter_job_date'] = $startdate;
			$data['transporter_job_time'] = $starttime;
			$data['transporter_job_from'] = $from;
			$data['transporter_job_to'] = $to;
			$data['transporter_job_area'] = $area;
			$data['transporter_job_client'] = $client;
            $this->dbtransporter->insert("tcont_jobfile", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Add New Jobfile Successfully Submitted";
			$callback["redirect"]= base_url()."tcont_jobfile";
			echo json_encode($callback);
			return;
	}
	
	function edit_jobfile(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		
		if ($id) {
		    $this->dbtransporter->where("transporter_job_id", $id);
            $q = $this->dbtransporter->get("tcont_jobfile");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit Job File";
				$this->params['content'] = $this->load->view("jobfile/jobfile_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."tcont_jofile");
		}
	}
	
	function update_jobfile(){
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$datetime = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobfile = isset($_POST['jobfile']) ? $_POST['jobfile'] : "";
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : "0";
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : "0";
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : "0";
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$client = isset($_POST['client']) ? $_POST['client'] : "";
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		
		$error = "";
		if ($jobfile == "")
		{
			$error .= "- Please Select Job File \n";	
		}/* else{
			
			$this->dbtransporter->where("transporter_job_number", $jobfile);
			$this->dbtransporter->where("transporter_job_flag", 0);
			$qj = $this->dbtransporter->get("tcont_jobfile");
			if($qj->num_rows() > 0 && $id== 0){
				$error .= "- Job File already exist \n";
			
			}
		} */
		if ($startdate == "")
		{
			$error .= "- Please Select Date \n";	
		}
		if ($nopo == "")
		{
			$error .= "- Please Input No. PO \n";	
		}
		/* if ($vehicle_id == "")
		{
			$error .= "- Your Vehicle ID is null \n";	
		}
		if ($vehicle_device == "")
		{
			$error .= "- Your Vehicle Device is null \n";	
		}
		if ($vehicle_name == "")
		{
			$error .= "- Your VEhicle Name is null \n";	
		} */
		if ($driver == "")
		{
			$error .= "- Please Input Driver \n";	
		}
		if ($area == "")
		{
			$error .= "- Please Select Area \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $vehicle_id);
			$qv = $this->db->get("vehicle");
			$vehicles = $qv->row();
			$this->db->close();
			
		unset($data);

			$data['transporter_job_vehicle_id'] = $vehicle_id;
			$data['transporter_job_vehicle_name'] = $vehicles->vehicle_name;
            $data['transporter_job_vehicle_device'] = $vehicles->vehicle_device;
			$data['transporter_job_vehicle_no'] = $vehicles->vehicle_no;
			$data['transporter_job_driver'] = $driver;
			$data['transporter_job_number'] = $jobfile;
			$data['transporter_job_po'] = $nopo;
            $data['transporter_job_dimensi_p'] = $dimensi_p;
			$data['transporter_job_dimensi_l'] = $dimensi_l;
			$data['transporter_job_dimensi_t'] = $dimensi_t;
			$data['transporter_job_weight'] = $weight;
			$data['transporter_job_date'] = $startdate;
			$data['transporter_job_time'] = $starttime;
			$data['transporter_job_from'] = $from;
			$data['transporter_job_to'] = $to;
			$data['transporter_job_area'] = $area;
			$data['transporter_job_client'] = $client;
			
			$this->dbtransporter->where("transporter_job_id", $id);
            $this->dbtransporter->update("tcont_jobfile", $data);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Job File Successfully Updated";
			$callback['redirect'] = base_url()."tcont_jobfile";
			echo json_encode($callback);
			return;
	}
	
	function export_to_excel()
	{
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$datetime = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobfile = isset($_POST['jobfile']) ? $_POST['jobfile'] : "";
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : 0;
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : 0;
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : 0;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$client = isset($_POST['client']) ? $_POST['client'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		$this->params["vehicle"] = $row_vehicle;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		$this->dbtransporter->order_by("transporter_job_id", "desc");
		$this->dbtransporter->where("transporter_job_flag", 0);
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("transporter_job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("transporter_job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "transporter_job_number":
				$this->dbtransporter->where("transporter_job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "transporter_job_po":
				$this->dbtransporter->where("transporter_job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "transporter_job_vehicle_no":
				$this->dbtransporter->where("transporter_job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "transporter_job_status":
				$this->dbcar->where("transporter_job_status", $status);				
			break;			
	
		}
		
		$this->dbtransporter->join("driver", "driver_id = transporter_job_driver", "left");
		$q = $this->dbtransporter->get("tcont_jobfile");
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("transporter_job_id", "desc");
		$this->dbtransporter->where("transporter_job_flag", 0);
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("transporter_job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("transporter_job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "transporter_job_number":
				$this->dbtransporter->where("transporter_job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "transporter_job_po":
				$this->dbtransporter->where("transporter_job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "transporter_job_vehicle_no":
				$this->dbtransporter->where("transporter_job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "transporter_job_status":
				$this->dbcar->where("transporter_job_status", $status);				
			break;				
	
		}
		
		$qt = $this->dbtransporter->get("tcont_jobfile");
		$rt = $qt->row();
		$total = $rt->total;
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("transporter.lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("transporter.lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Job File Report");
		$objPHPExcel->getProperties()->setSubject("Job File Report");
		$objPHPExcel->getProperties()->setDescription("Job File Report");
		
		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'JOB FILE REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		
		
		if($startdate || $enddate){
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', date("d-m-Y",strtotime($startdate))." "."to"." ".date("d-m-Y",strtotime($enddate)));
		}
		
	
		//tambahan costs
		
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Total Record: ');
		$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', $total);
		$objPHPExcel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
		
		/* $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Total Order : ');
		$objPHPExcel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', $total);
		$objPHPExcel->getActiveSheet()->getStyle('E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true); */
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Job Number');
		$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'No PO');
		$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Vehicle No');
		$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Vehicle Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Driver');
		$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Area');
		$objPHPExcel->getActiveSheet()->SetCellValue('H7', 'Datetime');
		$objPHPExcel->getActiveSheet()->SetCellValue('I7', 'Start From');
		$objPHPExcel->getActiveSheet()->SetCellValue('J7', 'Destination');
		$objPHPExcel->getActiveSheet()->SetCellValue('K7', 'Client');
		$objPHPExcel->getActiveSheet()->SetCellValue('L7', 'Dimension');
		$objPHPExcel->getActiveSheet()->SetCellValue('M7', 'Status');
		//$objPHPExcel->getActiveSheet()->SetCellValue('N7', 'Notes');
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A7:M7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A7:M7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i = 1;
		for ($j=0;$j<count($rows);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows[$j]->transporter_job_number);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows[$j]->transporter_job_po);
			$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $rows[$j]->transporter_job_vehicle_no);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), $rows[$j]->transporter_job_vehicle_name);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(7+$i), $rows[$j]->driver_name);
			$objPHPExcel->getActiveSheet()->getStyle('F'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $rows[$j]->transporter_job_area);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(7+$i), date("d-m-Y H:i",strtotime($rows[$j]->transporter_job_date." ".$rows[$j]->transporter_job_time)));
			$objPHPExcel->getActiveSheet()->getStyle('H'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(7+$i), $rows[$j]->transporter_job_from);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(7+$i), $rows[$j]->transporter_job_to);
			$objPHPExcel->getActiveSheet()->getStyle('J'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(7+$i), $rows[$j]->transporter_job_client);
			$objPHPExcel->getActiveSheet()->getStyle('K'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.(7+$i), $rows[$j]->transporter_job_dimensi_p." cm x "." ".$rows[$j]->transporter_job_dimensi_l." cm x "." ".$rows[$j]->transporter_job_dimensi_t." cm ");
			$objPHPExcel->getActiveSheet()->getStyle('L'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			//status
			if (isset($rows[$j]->transporter_job_status))
			{
				
				if ($rows[$j]->transporter_job_status == 1)
				{
					$status = "On Going";
					
				}
				if ($rows[$j]->transporter_job_status == 2)
				{
					$status = "Delivered";
					
				}
				
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.(7+$i), $status);
			$objPHPExcel->getActiveSheet()->getStyle('M'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			}
			
			$i++;
		}
		
		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
			$objPHPExcel->getActiveSheet()->getStyle('A7:M'.(6+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:M'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:M'.(6+$i))->getAlignment()->setWrapText(true);
			
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('JOB FILE REPORT');
			 
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			
			if($startdate || $enddate){
				$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
			}
			else{
				$filedate = date("Ymd");
			}
			$filecreatedname = "jobfile_(".$filedate.")".".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
		
		
	}
	
	
	function delete_jobfile($id)
	{
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$data["transporter_job_flag"] = 1;		
		$this->dbtransporter->where("transporter_job_id", $id);
		if($this->dbtransporter->update("tcont_jobfile", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function get_vehicle(){
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->db->order_by("vehicle_name", "asc");
		if ($type_admin != 1){
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		} 
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");
		$row_vehicle = $q->result();
        
		$data_vehicle = $row_vehicle;
		//print_r($data_vehicle);exit();
        $this->db->cache_delete_all();
        return $data_vehicle;
	}
	
	function get_driver(){
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter->order_by("driver_name", "asc");
		if ($type_admin != 1){
			$this->dbtransporter->where("driver_company", $company);
		} 
		//$this->dbtransporter->where("driver_status <>", 2);
		$q = $this->dbtransporter->get("driver");
		$row_driver = $q->result();
        
		$data_driver = $row_driver;
	
        $this->dbtransporter->cache_delete_all();
        return $data_driver;
	}
	
	function set_delivered()
	{
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$this->dbtransporter->where("transporter_job_id", $id);
		$this->dbtransporter->join("driver", "driver_id = transporter_job_driver", "left");
		$qr = $this->dbtransporter->get("tcont_jobfile");
				
		$row = $qr->row();
		$params['row'] = $row;
				
		$callback['html'] = $this->load->view("jobfile/set_delivered", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function save_delivered()
	{
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$job_id = isset($_POST['job_id']) ? $_POST['job_id'] : 0;
		$date = isset($_POST['date']) ? $_POST['date'] : "";
		$time = isset($_POST['time']) ? $_POST['time'] : "";
		$datetime = isset($_POST['datetime']) ? $_POST['datetime'] : "";
		
		//Update Status Delivered (value:2) and datetime
		$status["transporter_job_status"] = 2;
		$status["transporter_job_deliv_date"] = $date;
		$status["transporter_job_deliv_time"] = $time;
		
		$this->dbtransporter->where("transporter_job_id", $job_id);
		$this->dbtransporter->update("tcont_jobfile", $status);
		$this->dbtransporter->cache_delete_all();
			
		$callback['error'] = false;
		$callback['message'] = "Success Update Status";
		echo json_encode($callback);
			
		return;
		
	}
	
	function detail_jobfile() {
		
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$id = $this->input->post("id");
		$this->load->helper(array('form'));

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("transporter_job_id", $id);
		$this->dbtransporter->join("driver", "driver_id = transporter_job_driver", "left");
		$q = $this->dbtransporter->get("tcont_jobfile");
		$row = $q->row();

		$this->dbtransporter->close();
		
		$params['row'] = $row;
		$params["title"] = "Detail Job File";		
		$params["transporter_job_id"] = $id;
		$html = $this->load->view("jobfile/jobfile_detail", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;		
		echo json_encode($callback);
		
	}
		 	  
}
