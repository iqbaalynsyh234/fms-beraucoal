<?php
include "base.php";
class Ssi_team extends Base {

	function __construct()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}
	
	function index(){
		
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		
		/* //get vehicle
		$rows_mobil = $this->get_mobil();
		$this->params["rmobil"] = $rows_mobil; */
		
		$this->params['sortby'] = "team_date";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Team Replenishment";
				
		$this->params["content"] =  $this->load->view('transporter/team/team_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search(){
	
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "team_date";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$scheduledate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$driver_npp = isset($_POST['driver_npp']) ? $_POST['driver_npp'] : "";
		$staff = isset($_POST['staff']) ? $_POST['staff'] : "";
		$staff_npp = isset($_POST['staff_npp']) ? $_POST['staff_npp'] : "";
		$pengaman1 = isset($_POST['pengaman1']) ? $_POST['pengaman1'] : "";
		$pengaman1_nrp = isset($_POST['pengaman1_nrp']) ? $_POST['pengaman1_nrp'] : "";
		$pengaman2 = isset($_POST['pengaman2']) ? $_POST['pengaman2'] : "";
		$pengaman2_nrp = isset($_POST['pengaman2_nrp']) ? $_POST['pengaman2_nrp'] : "";
		$pengaman3 = isset($_POST['pengaman3']) ? $_POST['pengaman3'] : "";
		$pengaman3_nrp = isset($_POST['pengaman3_nrp']) ? $_POST['pengaman3_nrp'] : "";
		
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_mobil = $this->get_mobil();
		$this->params["rmobil"] = $row_mobil;
		
		$this->dbtransporter->order_by("team_date", "desc");
		$this->dbtransporter->order_by("team_time", "desc");
		$this->dbtransporter->order_by("team_vehicle_no", "asc");
		$this->dbtransporter->where("team_flag", 0);
		if($this->sess->user_group <> 0){
			$this->dbtransporter->where("team_creator", $this->sess->user_id);
		}
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("team_date >=", $startdate);
					}
					if($startdate != ""){
						$this->dbtransporter->where("team_date <=", $startdate);
					}
			}
			
		switch($field)
		{	
			case "team_vehicle_no":
				$this->dbtransporter->where("team_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "team_driver":
				$this->dbtransporter->where("team_driver LIKE '%".$keyword."%'", null);				
			break;
			case "team_staff":
				$this->dbtransporter->where("team_staff LIKE '%".$keyword."%'", null);				
			break;	
			case "team_status":
				$this->dbcar->where("team_status", $status);				
			break;			
		}
		$q = $this->dbtransporter->get("ssi_team", $this->config->item("limit_records"), $offset);
		$rows = $q->result();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("team_date", "desc");
		$this->dbtransporter->order_by("team_time", "desc");
		$this->dbtransporter->order_by("team_vehicle_no", "asc");
		$this->dbtransporter->where("team_flag", 0);
		if($this->sess->user_group <> 0){
			$this->dbtransporter->where("team_creator", $this->sess->user_id);
		}
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("team_date >=", $startdate);
					}
					if($startdate != ""){
						$this->dbtransporter->where("team_date <=", $startdate);
					}
			}
			
		switch($field)
		{	
			case "team_vehicle_no":
				$this->dbtransporter->where("team_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "team_driver":
				$this->dbtransporter->where("team_driver LIKE '%".$keyword."%'", null);				
			break;
			case "team_staff":
				$this->dbtransporter->where("team_staff LIKE '%".$keyword."%'", null);				
			break;
			case "team_status":
				$this->dbcar->where("team_status", $status);				
			break;			
		}

		$qt = $this->dbtransporter->get("ssi_team");
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
	
		$html = $this->load->view('transporter/team/team_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add()
	{
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_mobil = $this->get_mobil();
		$this->params["rmobil"] = $rows_mobil;
	
		$this->params["content"] = $this->load->view('transporter/team/team_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save()
	{
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
	
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$endtime = isset($_POST['endtime']) ? $_POST['endtime'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$company = isset($_POST['company']) ? $_POST['company'] : 0;
		$group = isset($_POST['group']) ? $_POST['group'] : 0;
		$schedulestart = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$scheduleend = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$driver_npp = isset($_POST['driver_npp']) ? $_POST['driver_npp'] : "";
		$staff = isset($_POST['staff']) ? $_POST['staff'] : "";
		$staff_npp = isset($_POST['staff_npp']) ? $_POST['staff_npp'] : "";
		$pengaman1 = isset($_POST['pengaman1']) ? $_POST['pengaman1'] : "";
		$pengaman1_nrp = isset($_POST['pengaman1_nrp']) ? $_POST['pengaman1_nrp'] : "";
		$pengaman2 = isset($_POST['pengaman2']) ? $_POST['pengaman2'] : "";
		$pengaman2_nrp = isset($_POST['pengaman2_nrp']) ? $_POST['pengaman2_nrp'] : "";
		$pengaman3 = isset($_POST['pengaman3']) ? $_POST['pengaman3'] : "";
		$pengaman3_nrp = isset($_POST['pengaman3_nrp']) ? $_POST['pengaman3_nrp'] : "";
		
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$creator = isset($_POST['creator']) ? $_POST['creator'] : 0;
		$shift = isset($_POST['shift']) ? $_POST['shift'] : 0;
	
		$error = "";
		if ($mobil_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}else{
			
			$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no");
			$this->db->where("vehicle_id", $mobil_id);
			$qm = $this->db->get("vehicle");
			$row_m = $qm->row();
			if($qm->num_rows() > 0){
				$mobil_name = $row_m->vehicle_name;
				$mobil_no = $row_m->vehicle_no;
				$mobil_device	= $row_m->vehicle_device;
			}else{
				$error .= "- No Data Vehicle \n";	
			}
			
		}
		
		if ($startdate == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		
		//kondisi shift
		if($this->sess->user_group == 1224){ // khusus ssi.mandiri
			if($shift == 1){
				$starttime = "07:00";
				$schedulestart = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
				$plus = date_create($schedulestart);
				date_add($plus, date_interval_create_from_date_string('9 hours'));
				
				$endtime = date_format($plus, 'H:i:s');
				$enddate = date_format($plus, 'Y-m-d');
				$scheduleend = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
	
			}else if($shift == 2){
				$starttime = "15:00";
				$schedulestart = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
				$plus = date_create($schedulestart);
				date_add($plus, date_interval_create_from_date_string('8 hours'));
				
				$endtime = date_format($plus, 'H:i:s');
				$enddate = date_format($plus, 'Y-m-d');
				$scheduleend = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
			}else if($shift == 3){
				$starttime = "22:00";
				$schedulestart = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
				$plus = date_create($schedulestart);
				date_add($plus, date_interval_create_from_date_string('8 hours'));
				
				$endtime = date_format($plus, 'H:i:s');
				$enddate = date_format($plus, 'Y-m-d');
				$scheduleend = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
			}else{
			
				$error .= "- Please Select Shift \n";
			}
		}else{
			if ($starttime == "")
			{
				$error .= "- Please Select Start Time \n";	
			}
			if ($enddate == "")
			{
				$error .= "- Please Select End Date \n";	
			}
			if ($endtime == "")
			{
				$error .= "- Please Select End Time \n";	
			}
		}
		
		//kondisi time
		if($starttime)
		{
			$this->dbtransporter->select("team_vehicle_no, team_date, team_time, team_enddate, team_endtime");
			$this->dbtransporter->where("team_vehicle_id", $mobil_id);
			$this->dbtransporter->where("team_sch_start >", $schedulestart);
			$this->dbtransporter->where("team_sch_end <", $scheduleend);
			$this->dbtransporter->where("team_flag", 0);
			$qv = $this->dbtransporter->get("ssi_team");
			$row_v = $qv->row();
			
			if($qv->num_rows() > 0 && $id== 0){
				$error .= "- Schedule ".$row_v->team_vehicle_no." "." already exist at " .$row_v->team_date." ".$row_v->team_time." to ".$row_v->team_enddate." ".$row_v->team_endtime. "\n";
			
			}
		}
		if ($schedulestart > $scheduleend)
		{
			$error .= "- Invalid Schedule Please Check Your Schedule Date \n";	
		}
		/*if ($mobil_no == "")
		{
			$error .= "- Invalid, Vehicle No = 0 \n";	
		}
		if ($mobil_name == "")
		{
			$error .= "- Invalid, Vehicle Name = 0 \n";	
		}
		if ($mobil_device == "")
		{
			$error .= "- Invalid, Vehicle Device = 0 \n";	
		} */
		if ($driver == "")
		{
			$error .= "- Please Input Driver \n";	
		}
		if ($staff == "")
		{
			$error .= "- Please Input Staff \n";	
		}
		
		if ($pengaman1 == "")
		{
			$error .= "- Please Input Pengaman 1 \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
			
		unset($data);
			
            $data['team_vehicle_id'] = $mobil_id;
			$data['team_vehicle_name'] = $mobil_name;
            $data['team_vehicle_device'] = $mobil_device;
			$data['team_vehicle_no'] = $mobil_no;
			$data['team_driver'] = strtoupper($driver);
			$data['team_driver_npp'] = $driver_npp;
			$data['team_staff'] = strtoupper($staff);
			$data['team_staff_npp'] = $staff_npp;
			
			$data['team_pengaman1'] = strtoupper($pengaman1);
			$data['team_pengaman1_nrp'] = $pengaman1_nrp;
			$data['team_pengaman2'] = strtoupper($pengaman2);
			$data['team_pengaman2_nrp'] = $pengaman2_nrp;
			$data['team_pengaman3'] = strtoupper($pengaman3);
			$data['team_pengaman3_nrp'] = $pengaman3_nrp;
			
			$data['team_date'] = $startdate;
			$data['team_time'] = $starttime;
			$data['team_enddate'] = $enddate;
			$data['team_endtime'] = $endtime;
			$data['team_sch_start'] = $schedulestart;
			$data['team_sch_end'] = $scheduleend;
			$data['team_company'] = $company;
			$data['team_group'] = $group;
			$data['team_creator'] = $creator;
			$data['team_note'] = $note;
			$data['team_shift'] = $shift;
			
            $this->dbtransporter->insert("ssi_team", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Add Team Successfully Submitted";
			$callback["redirect"]= base_url()."ssi_team";
			echo json_encode($callback);
			return;
	}
	
	function edit(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		$this->dbtransporter->where("team_id", $id);
		$q = $this->dbtransporter->get("ssi_team");
			
		if ($q->num_rows() == 0)
		{
			redirect(base_url() . "ssi_team/");
		}
		
		//get vehicle
		$rows_mobil = $this->get_mobil();
		$params["rmobil"] = $rows_mobil;
		
		$row = $q->row();
		$params['row'] = $row;			
		$html = $this->load->view("transporter/team/team_edit", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;
		
		$this->db->cache_delete_all();
		echo json_encode($callback);
	}
	
	function update(){
	
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
	
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate2 = isset($_POST['startdate2']) ? $_POST['startdate2'] : "";
		$starttime2 = isset($_POST['starttime2']) ? $_POST['starttime2'] : "";
		$enddate2 = isset($_POST['enddate2']) ? $_POST['enddate2'] : "";
		$endtime2 = isset($_POST['endtime2']) ? $_POST['endtime2'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$company = isset($_POST['company']) ? $_POST['company'] : 0;
		$group = isset($_POST['group']) ? $_POST['group'] : 0;
		$schedulestart = date("Y-m-d H:i:s", strtotime($startdate2 . " " . $starttime2));
		$scheduleend = date("Y-m-d H:i:s", strtotime($enddate2 . " " . $endtime2));
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$driver_npp = isset($_POST['driver_npp']) ? $_POST['driver_npp'] : "";
		$staff = isset($_POST['staff']) ? $_POST['staff'] : "";
		$staff_npp = isset($_POST['staff_npp']) ? $_POST['staff_npp'] : "";
		$pengaman1 = isset($_POST['pengaman1']) ? $_POST['pengaman1'] : "";
		$pengaman1_nrp = isset($_POST['pengaman1_nrp']) ? $_POST['pengaman1_nrp'] : "";
		$pengaman2 = isset($_POST['pengaman2']) ? $_POST['pengaman2'] : "";
		$pengaman2_nrp = isset($_POST['pengaman2_nrp']) ? $_POST['pengaman2_nrp'] : "";
		$pengaman3 = isset($_POST['pengaman3']) ? $_POST['pengaman3'] : "";
		$pengaman3_nrp = isset($_POST['pengaman3_nrp']) ? $_POST['pengaman3_nrp'] : "";
		
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$creator = isset($_POST['creator']) ? $_POST['creator'] : 0;
		$shift = isset($_POST['shift']) ? $_POST['shift'] : 0;
	
		$error = "";
		if ($mobil_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}else{
			
			$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no");
			$this->db->where("vehicle_id", $mobil_id);
			$qm = $this->db->get("vehicle");
			$row_m = $qm->row();
			if($qm->num_rows() > 0){
				$mobil_name = $row_m->vehicle_name;
				$mobil_no = $row_m->vehicle_no;
				$mobil_device	= $row_m->vehicle_device;
			}else{
				$error .= "- No Data Vehicle \n";	
			}
			
		}
		if ($startdate2 == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		
		//kondisi shift edit team kondisi
		if($this->sess->user_group == 1224){ // khusus ssi.mandiri
			if($shift == 1){
				$starttime2 = "07:00";
				$schedulestart = date("Y-m-d H:i:s", strtotime($startdate2 . " " . $starttime2));
				$plus = date_create($schedulestart);
				date_add($plus, date_interval_create_from_date_string('9 hours'));
				
				$endtime2 = date_format($plus, 'H:i:s');
				$enddate2 = date_format($plus, 'Y-m-d');
				$scheduleend = date("Y-m-d H:i:s", strtotime($enddate2 . " " . $endtime2));
	
			}else if($shift == 2){
				$starttime2 = "15:00";
				$schedulestart = date("Y-m-d H:i:s", strtotime($startdate2 . " " . $starttime2));
				$plus = date_create($schedulestart);
				date_add($plus, date_interval_create_from_date_string('8 hours'));
				
				$endtime2 = date_format($plus, 'H:i:s');
				$enddate2 = date_format($plus, 'Y-m-d');
				$scheduleend = date("Y-m-d H:i:s", strtotime($enddate2 . " " . $endtime2));
			}else if($shift == 3){
				$starttime2 = "22:00";
				$schedulestart = date("Y-m-d H:i:s", strtotime($startdate2 . " " . $starttime2));
				$plus = date_create($schedulestart);
				date_add($plus, date_interval_create_from_date_string('8 hours'));
				
				$endtime2 = date_format($plus, 'H:i:s');
				$enddate2 = date_format($plus, 'Y-m-d');
				$scheduleend = date("Y-m-d H:i:s", strtotime($enddate2 . " " . $endtime2));
			}else{
			
				$error .= "- Please Select Shift \n";
			}
		}else{
			if ($starttime2 == "")
			{
				$error .= "- Please Select Start Time \n";	
			}
			if ($enddate2 == "")
			{
				$error .= "- Please Select End Date \n";	
			}
			if ($endtime2 == "")
			{
				$error .= "- Please Select End Time \n";	
			}
		}
		
		//kondisi time
		if($starttime2)
		{
			$this->dbtransporter->select("team_vehicle_no, team_date, team_time, team_enddate, team_endtime");
			$this->dbtransporter->where("team_vehicle_id", $mobil_id);
			$this->dbtransporter->where("team_sch_start >", $schedulestart);
			$this->dbtransporter->where("team_sch_end <", $scheduleend);
			$this->dbtransporter->where("team_flag", 0);
			$qv = $this->dbtransporter->get("ssi_team");
			$row_v = $qv->row();
			
			if($qv->num_rows() > 0){
				$error .= "- Schedule ".$row_v->team_vehicle_no." "." already exist at " .$row_v->team_date." ".$row_v->team_time." to ".$row_v->team_enddate." ".$row_v->team_endtime. "\n";
			
			}
		}
		if ($schedulestart > $scheduleend)
		{
			$error .= "- Invalid Schedule Please Check Your Schedule Date \n";	
		}
		/*if ($mobil_no == "")
		{
			$error .= "- Invalid, Vehicle No = 0 \n";	
		}
		if ($mobil_name == "")
		{
			$error .= "- Invalid, Vehicle Name = 0 \n";	
		}
		if ($mobil_device == "")
		{
			$error .= "- Invalid, Vehicle Device = 0 \n";	
		} */
		if ($driver == "")
		{
			$error .= "- Please Input Driver \n";	
		}
		if ($staff == "")
		{
			$error .= "- Please Input Staff \n";	
		}
		
		if ($pengaman1 == "")
		{
			$error .= "- Please Input Pengaman 1 \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
			
		unset($data);
			
            $data['team_vehicle_id'] = $mobil_id;
			$data['team_vehicle_name'] = $mobil_name;
            $data['team_vehicle_device'] = $mobil_device;
			$data['team_vehicle_no'] = $mobil_no;
			$data['team_driver'] = strtoupper($driver);
			$data['team_driver_npp'] = $driver_npp;
			$data['team_staff'] = strtoupper($staff);
			$data['team_staff_npp'] = $staff_npp;
			
			$data['team_pengaman1'] = strtoupper($pengaman1);
			$data['team_pengaman1_nrp'] = $pengaman1_nrp;
			$data['team_pengaman2'] = strtoupper($pengaman2);
			$data['team_pengaman2_nrp'] = $pengaman2_nrp;
			$data['team_pengaman3'] = strtoupper($pengaman3);
			$data['team_pengaman3_nrp'] = $pengaman3_nrp;
			
			$data['team_date'] = $startdate2;
			$data['team_time'] = $starttime2;
			$data['team_enddate'] = $enddate2;
			$data['team_endtime'] = $endtime2;
			$data['team_sch_start'] = $schedulestart;
			$data['team_sch_end'] = $scheduleend;
			$data['team_company'] = $company;
			$data['team_group'] = $group;
			$data['team_creator'] = $creator;
			$data['team_note'] = $note;
			$data['team_shift'] = $shift;
			
			if($id){
				$this->dbtransporter->where("team_id", $id);
				$this->dbtransporter->update("ssi_team", $data);
				$this->dbtransporter->close();
			
				$callback['error'] = false;
				$callback['message'] = "Update Team Successfully Updated";
				$callback['redirect'] = base_url()."ssi_team";
				echo json_encode($callback);
				return;
			}
	}
	
	function delete($id)
	{
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$data["team_flag"] = 1;		
		$this->dbtransporter->where("team_id", $id);
		if($this->dbtransporter->update("ssi_team", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function get_mobil(){
	
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_no", "asc");
		if($this->sess->user_group <> 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}else
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$row_mobil = $q->result();
		$data_mobil = $row_mobil;
        $this->db->cache_delete_all();
        return $data_mobil;
	}
	 	  
}
