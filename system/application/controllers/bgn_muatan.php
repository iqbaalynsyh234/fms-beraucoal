<?php
include "base.php";
class Bgn_muatan extends Base {

	function __construct()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}
	
	function index(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		//get data muatan
		$rows_data_muatan = $this->get_data_muatan();
		$this->params["rdatamuatan"] = $rows_data_muatan;
		
		/* $this->dbtransporter->order_by("muatan_data_flag", "asc");
		$this->dbtransporter->where("muatan_data_company", $user_company);
		$this->dbtransporter->where("muatan_data_flag", 0);
		$this->dbtransporter->where("muatan_data_status", 1);
		$q = $this->dbtransporter->get("bangun_muatan_data");
		$rows_muatan = $q->result();
		$data_muatans = $rows_muatan;
		$this->params["rdatamuatan"] = $data_muatans; */
		
		$this->params['sortby'] = "muatan_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "List Muatan";
				
		$this->params["content"] =  $this->load->view('bangun_cilegon/muatan/muatan_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_muatan(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$datamuatan = isset($_POST['datamuatan']) ? $_POST['datamuatan'] : "";
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$dest = isset($_POST['dest']) ? $_POST['dest'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		
		//search by date
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_mobil = $this->get_mobil();
		$this->params["rmobil"] = $row_mobil;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		//get data muatan
		$rows_data_muatan = $this->get_data_muatan();
		$this->params["rdatamuatan"] = $rows_data_muatan;
		
		$this->dbtransporter->order_by("muatan_id", "desc");
		$this->dbtransporter->where("muatan_flag", 0);
		$this->dbtransporter->where("muatan_company", $user_company);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("muatan_startdate >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("muatan_startdate <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "muatan_vehicle_no":
				$this->dbtransporter->where("muatan_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_vehicle_name":
				$this->dbtransporter->where("muatan_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_dest":
				$this->dbtransporter->where("muatan_dest LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_note":
				$this->dbtransporter->where("muatan_note LIKE '%".$keyword."%'", null);				
			break;			
			case "muatan_data":
				$this->dbtransporter->where("muatan_data", $datamuatan);				
			break;
	
		}
		$this->dbtransporter->join("driver", "driver_id = muatan_driver", "left");
		$this->dbtransporter->join("bangun_muatan_data", "muatan_data_id = muatan_data", "left");
		$q = $this->dbtransporter->get("bangun_muatan", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("muatan_id", "desc");
		$this->dbtransporter->where("muatan_flag", 0);
		$this->dbtransporter->where("muatan_company", $user_company);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("muatan_startdate >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("muatan_startdate <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "muatan_vehicle_no":
				$this->dbtransporter->where("muatan_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_vehicle_name":
				$this->dbtransporter->where("muatan_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_dest":
				$this->dbtransporter->where("muatan_dest LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_note":
				$this->dbtransporter->where("muatan_note LIKE '%".$keyword."%'", null);				
			break;		
			case "muatan_data":
				$this->dbtransporter->where("muatan_data", $datamuatan);				
			break;		
	
		}
		
		$this->dbtransporter->join("driver", "driver_id = muatan_driver", "left");
		$this->dbtransporter->join("bangun_muatan_data", "muatan_data_id = muatan_data", "left");
		$qt = $this->dbtransporter->get("bangun_muatan");
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
	
		$html = $this->load->view('bangun_cilegon/muatan/muatan_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add_muatan()
	{
        if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_mobil = $this->get_mobil();
		$this->params["rvehicle"] = $rows_mobil;
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		//get data muatan
		$rows_data_muatan = $this->get_data_muatan();
		$this->params["rdatamuatan"] = $rows_data_muatan;
	
		$this->params["content"] = $this->load->view('bangun_cilegon/muatan/muatan_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_muatan()
	{
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$datamuatan = isset($_POST['datamuatan']) ? $_POST['datamuatan'] : "";
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$dest = isset($_POST['dest']) ? $_POST['dest'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
	
		$error = "";
		/* if ($job == "")
		{
			$error .= "- Please Select Job Number \n";	
		}else{
			
			$this->dbtransporter->where("job_number", $job);
			$this->dbtransporter->where("job_flag", 0);
			$qj = $this->dbtransporter->get("andalas_job");
			if($qj->num_rows() > 0 && $id== 0){
				$error .= "- Job Number already exist \n";
			
			}
		} */
		if ($startdate == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		if ($starttime == "")
		{
			$error .= "- Please Select Start Time \n";	
		}
		if ($mobil_id == "")
		{
			$error .= "- vehice id is null \n";	
		}
		/* if ($mobil_device == "")
		{
			$error .= "- vehicle device id null \n";	
		}
		if ($mobil_no == "")
		{
			$error .= "- vehicle no is null \n";	
		}
		if ($mobil_name == "")
		{
			$error .= "- vehicle name is null \n";	
		} */
		if ($driver == "")
		{
			$error .= "- Please select driver \n";	
		}
		if ($datamuatan == "")
		{
			$error .= "- Silahkan pilih jenis muatan \n";	
		}
		if ($dest == "")
		{
			$error .= "- Pleas fill Destination \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $mobil_id);
			$qv = $this->db->get("vehicle");
			$rmobil = $qv->row();
			//print_r($rmobil);exit();
			$this->db->close();
			
			unset($data);
			
            $data['muatan_vehicle_id'] = $mobil_id;
			$data['muatan_vehicle_name'] = $rmobil->vehicle_name;
            $data['muatan_vehicle_device'] = $rmobil->vehicle_device;
			$data['muatan_vehicle_no'] = $rmobil->vehicle_no;
			$data['muatan_driver'] = $driver;
			$data['muatan_startdate'] = $startdate;
			$data['muatan_starttime'] = $starttime;
			
			$data['muatan_data'] = $datamuatan;
			$data['muatan_weight'] = $weight;
			$data['muatan_dest'] = $dest;
			$data['muatan_note'] = $note;
			$data['muatan_company'] = $user_company;
			
            $this->dbtransporter->insert("bangun_muatan", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Success";
			$callback["redirect"]= base_url()."bgn_muatan";
			echo json_encode($callback);
			return;
	}
	
	function edit_muatan(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_mobil = $this->get_mobil();
		$this->params["rvehicle"] = $rows_mobil;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		//get data muatan
		$rows_data_muatan = $this->get_data_muatan();
		$this->params["rdatamuatan"] = $rows_data_muatan;
		
		
		if ($id) {
		    $this->dbtransporter->where("muatan_id", $id);
            $q = $this->dbtransporter->get("bangun_muatan");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit List Muatan";
				$this->params['content'] = $this->load->view("bangun_cilegon/muatan/muatan_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."bgn_muatan");
		}
	}
	
	function update_muatan(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$datamuatan = isset($_POST['datamuatan']) ? $_POST['datamuatan'] : "";
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$dest = isset($_POST['dest']) ? $_POST['dest'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		
		$error = "";
		/* if ($job == "")
		{
			$error .= "- Please Select Job Number \n";	
		}else{
			
			$this->dbtransporter->where("job_number", $job);
			$this->dbtransporter->where("job_flag", 0);
			$qj = $this->dbtransporter->get("andalas_job");
			if($qj->num_rows() > 0 && $id== 0){
				$error .= "- Job Number already exist \n";
			
			}
		} */
		if ($startdate == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		if ($starttime == "")
		{
			$error .= "- Please Select Start Time \n";	
		}
		if ($mobil_id == "")
		{
			$error .= "- vehice id is null \n";	
		}
		/* if ($mobil_device == "")
		{
			$error .= "- vehicle device id null \n";	
		}
		if ($mobil_no == "")
		{
			$error .= "- vehicle no is null \n";	
		}
		if ($mobil_name == "")
		{
			$error .= "- vehicle name is null \n";	
		} */
		if ($driver == "")
		{
			$error .= "- Please select driver \n";	
		}
		if ($datamuatan == "")
		{
			$error .= "- Silahkan pilih jenis muatan \n";	
		}
		if ($dest == "")
		{
			$error .= "- Pleas fill Destination \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $mobil_id);
			$qv = $this->db->get("vehicle");
			$rmobil = $qv->row();
			$this->db->close();
			
			unset($data);
			
            $data['muatan_vehicle_id'] = $mobil_id;
			$data['muatan_vehicle_name'] = $rmobil->vehicle_name;
            $data['muatan_vehicle_device'] = $rmobil->vehicle_device;
			$data['muatan_vehicle_no'] = $rmobil->vehicle_no;
			$data['muatan_driver'] = $driver;
			$data['muatan_startdate'] = $startdate;
			$data['muatan_starttime'] = $starttime;
			
			$data['muatan_data'] = $datamuatan;
			$data['muatan_weight'] = $weight;
			$data['muatan_dest'] = $dest;
			$data['muatan_note'] = $note;
			$data['muatan_company'] = $user_company;
			
			$this->dbtransporter->where("muatan_id", $id);
            $this->dbtransporter->update("bangun_muatan", $data);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Successfully Updated";
			$callback['redirect'] = base_url()."bgn_muatan";
			echo json_encode($callback);
			return;
	}
	
	//belum
	function export_to_excel()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$datamuatan = isset($_POST['datamuatan']) ? $_POST['datamuatan'] : "";
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$dest = isset($_POST['dest']) ? $_POST['dest'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		
		//search by date
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_mobil = $this->get_mobil();
		$this->params["rmobil"] = $row_mobil;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		//get data muatan
		$rows_data_muatan = $this->get_data_muatan();
		$this->params["rdatamuatan"] = $rows_data_muatan;
		
		$this->dbtransporter->order_by("muatan_id", "desc");
		$this->dbtransporter->where("muatan_flag", 0);
		$this->dbtransporter->where("muatan_company", $user_company);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("muatan_startdate >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("muatan_startdate <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "muatan_vehicle_no":
				$this->dbtransporter->where("muatan_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_vehicle_name":
				$this->dbtransporter->where("muatan_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_dest":
				$this->dbtransporter->where("muatan_dest LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_note":
				$this->dbtransporter->where("muatan_note LIKE '%".$keyword."%'", null);				
			break;			
			case "muatan_data":
				$this->dbtransporter->where("muatan_data", $datamuatan);				
			break;
	
		}
		$this->dbtransporter->join("driver", "driver_id = muatan_driver", "left");
		$this->dbtransporter->join("bangun_muatan_data", "muatan_data_id = muatan_data", "left");
		$q = $this->dbtransporter->get("bangun_muatan");
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("muatan_id", "desc");
		$this->dbtransporter->where("muatan_flag", 0);
		$this->dbtransporter->where("muatan_company", $user_company);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("muatan_startdate >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("muatan_startdate <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "muatan_vehicle_no":
				$this->dbtransporter->where("muatan_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_vehicle_name":
				$this->dbtransporter->where("muatan_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_dest":
				$this->dbtransporter->where("muatan_dest LIKE '%".$keyword."%'", null);				
			break;
			case "muatan_note":
				$this->dbtransporter->where("muatan_note LIKE '%".$keyword."%'", null);				
			break;		
			case "muatan_data":
				$this->dbtransporter->where("muatan_data", $datamuatan);				
			break;		
	
		}
		
		$this->dbtransporter->join("driver", "driver_id = muatan_driver", "left");
		$this->dbtransporter->join("bangun_muatan_data", "muatan_data_id = muatan_data", "left");
		$qt = $this->dbtransporter->get("bangun_muatan");
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
		$objPHPExcel->getProperties()->setTitle("Report Data Muatan");
		$objPHPExcel->getProperties()->setSubject("Report Data Muatan");
		$objPHPExcel->getProperties()->setDescription("Report Data Muatan");
		
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

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Data Muatan');
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
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Vehicle No');
		$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Vehicle Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Driver');
		$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Datetime');
		$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Muatan');
		$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Destination');
		$objPHPExcel->getActiveSheet()->SetCellValue('H7', 'Notes');
		
		$objPHPExcel->getActiveSheet()->getStyle('A7:H7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A7:H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i = 1;
		for ($j=0;$j<count($rows);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows[$j]->muatan_vehicle_no);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows[$j]->muatan_vehicle_name);
			$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $rows[$j]->driver_name);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), date("d-m-Y H:i",strtotime($rows[$j]->muatan_startdate." ".$rows[$j]->muatan_starttime)));
			$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(7+$i), $rows[$j]->muatan_data_name);
			$objPHPExcel->getActiveSheet()->getStyle('F'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $rows[$j]->muatan_dest);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(7+$i), $rows[$j]->muatan_note);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i++;
		}
		
		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
			$objPHPExcel->getActiveSheet()->getStyle('A7:H'.(6+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:H'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:H'.(6+$i))->getAlignment()->setWrapText(true);
			
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Report Data Muatan');
			 
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			
			if($startdate || $enddate){
				$filedate = date("dmY",strtotime($startdate))."_".date("dmY",strtotime($enddate));
			}
			else{
				$filedate = date("dmY");
			}
			$filecreatedname = "data_muatan_".$filedate."".".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}
	
	function delete_muatan($id)
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$data["muatan_flag"] = 1;		
		$this->dbtransporter->where("muatan_id", $id);
		if($this->dbtransporter->update("bangun_muatan", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function get_mobil(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_user_id", $this->sess->user_id );
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$row_mobil = $q->result();
        
		$data_mobil = $row_mobil;
		//print_r($data_vehicle);exit();
        $this->db->cache_delete_all();
        return $data_mobil;
	}
	
	function get_driver(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("driver_name", "asc");
		$this->dbtransporter->where("driver_company", $user_company);
		$this->dbtransporter->where("driver_status <>", 2);
		$q = $this->dbtransporter->get("driver");
		$row_driver = $q->result();
        
		$data_driver = $row_driver;
	
        $this->dbtransporter->cache_delete_all();
        return $data_driver;
	}
	
	function get_data_muatan(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("muatan_data_flag", "asc");
		$this->dbtransporter->where("muatan_data_company", $user_company);
		$this->dbtransporter->where("muatan_data_flag", 0);
		$this->dbtransporter->where("muatan_data_status", 1);
		
		$q = $this->dbtransporter->get("bangun_muatan_data");
		$row_muatan_data = $q->result();
        
		$data_muatan_data = $row_muatan_data;
        $this->dbtransporter->cache_delete_all();
        return $data_muatan_data;
	}
	
	/* function set_delivered()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$this->dbtransporter->where("job_id", $id);
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$this->dbtransporter->join("mobil", "mobil_id = muatan_vehicle_id", "left");
		$this->dbtransporter->join("andalas_customer_company", "customer_company_id = job_customer_company", "left");
		$qr = $this->dbtransporter->get("andalas_job");
				
		$row = $qr->row();
		$params['row'] = $row;
				
		$callback['html'] = $this->load->view("job/set_delivered", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function save_delivered()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
	
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$job_id = isset($_POST['job_id']) ? $_POST['job_id'] : 0;
		$date = isset($_POST['date']) ? $_POST['date'] : "";
		$time = isset($_POST['time']) ? $_POST['time'] : "";
		$deliv_datetime = date("Y-m-d H:i:s", strtotime($date . " " . $time));
		
		//Update Status Delivered (value:2) and datetime
		$status["job_status"] = 2;
		$status["job_deliv_date"] = $date;
		$status["job_deliv_time"] = $time;
		$status["job_deliv_datetime"] = $deliv_datetime;
		
		$this->dbtransporter->where("job_id", $job_id);
		$this->dbtransporter->update("andalas_job", $status);
		$this->dbtransporter->cache_delete_all();
			
		$callback['error'] = false;
		$callback['message'] = "Update Status Successfully Submitted";
		echo json_encode($callback);
			
		return;
		
	} */
	
	function detail_muatan() {
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->input->post("id");
		$this->load->helper(array('form'));

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("muatan_id", $id);
		$this->dbtransporter->join("driver", "driver_id = muatan_driver", "left");
		$this->dbtransporter->join("bangun_muatan_data", "muatan_data_id = muatan_data", "left");
		$q = $this->dbtransporter->get("bangun_muatan");
		$row = $q->row();

		$this->dbtransporter->close();
		
		$params['row'] = $row;
		$params["title"] = "Informasi Muatan";		
		$params["job_id"] = $id;
		
		$html = $this->load->view("bangun_cilegon/muatan/muatan_detail2", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		$this->dbtransporter->cache_delete_all();		
		echo json_encode($callback);
		
		
	}
	//manage data muatan
	function data(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->params['sortby'] = "muatan_data_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "List Data Muatan";
				
		$this->params["content"] =  $this->load->view('bangun_cilegon/muatan/muatan_data_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_data(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "muatan_data_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		
		$name = isset($_POST['name']) ? $_POST['name'] : "";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		
		$this->dbtransporter->order_by("muatan_data_id", "desc");
		$this->dbtransporter->where("muatan_data_flag", 0);
		$this->dbtransporter->where("muatan_data_company", $user_company);
			
		switch($field)
		{
			case "muatan_data_name":
				$this->dbtransporter->where("muatan_data_name LIKE '%".$keyword."%'", null);				
			break;			
			case "muatan_data_note":
				$this->dbtransporter->where("muatan_data_note LIKE '%".$keyword."%'", null);				
			break;		
	
		}
		$q = $this->dbtransporter->get("bangun_muatan_data", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("muatan_data_id", "desc");
		$this->dbtransporter->where("muatan_data_flag", 0);
		$this->dbtransporter->where("muatan_data_company", $user_company);
		switch($field)
		{
			case "muatan_data_name":
				$this->dbtransporter->where("muatan_data_name LIKE '%".$keyword."%'", null);				
			break;			
			case "muatan_data_note":
				$this->dbtransporter->where("muatan_data_note LIKE '%".$keyword."%'", null);				
			break;		
	
		}
		
		$qt = $this->dbtransporter->get("bangun_muatan_data");
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
	
		$html = $this->load->view('bangun_cilegon/muatan/muatan_data_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add_data()
	{
        if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
	
		$this->params["content"] = $this->load->view('bangun_cilegon/muatan/muatan_data_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_data()
	{
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "muatan_data_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		
		$name = isset($_POST['name']) ? $_POST['name'] : "";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
	
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		
		if ($company == "")
		{
			$error .= "- company is null \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			
			unset($data);
			
            $data['muatan_data_name'] = $name;
			$data['muatan_data_company'] = $company;
            $data['muatan_data_note'] = $note;
			$data['muatan_data_status'] = $status;
		
            $this->dbtransporter->insert("bangun_muatan_data", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Success";
			$callback["redirect"]= base_url()."bgn_muatan/data/";
			echo json_encode($callback);
			return;
	}
	
	function edit_data(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		if ($id) {
		    $this->dbtransporter->where("muatan_data_id", $id);
            $q = $this->dbtransporter->get("bangun_muatan_data");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit Data Muatan";
				$this->params['content'] = $this->load->view("bangun_cilegon/muatan/muatan_data_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."bgn_muatan/data/");
		}
	}
	
	function update_data(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "muatan_data_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		
		$name = isset($_POST['name']) ? $_POST['name'] : "";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		
		if ($company == "")
		{
			$error .= "- company is null \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			unset($data);
			
            $data['muatan_data_name'] = $name;
			$data['muatan_data_company'] = $company;
            $data['muatan_data_note'] = $note;
			$data['muatan_data_status'] = $status;
			
			$this->dbtransporter->where("muatan_data_id", $id);
            $this->dbtransporter->update("bangun_muatan_data", $data);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Successfully Updated";
			$callback['redirect'] = base_url()."bgn_muatan/data/";
			echo json_encode($callback);
			return;
	}
	
	function delete_data($id)
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$data["muatan_data_flag"] = 1;		
		$this->dbtransporter->where("muatan_data_id", $id);
		if($this->dbtransporter->update("bangun_muatan_data", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
}
