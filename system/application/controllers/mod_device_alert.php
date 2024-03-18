<?php
include "base.php";

class Mod_device_alert extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
	}
	//table target
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->params['sortby'] = "alert_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Config Target Time";
		
		/*$nowdate = date("Y-m-d");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("alert_vehicle_no","asc");
		$this->dbtransporter->where("alert_create >=", $nowdate_start);
		$this->dbtransporter->where("alert_create <=", $nowdate_end);
		$this->dbtransporter->where("alert_flag", 0);
		$qd = $this->dbtransporter->get("device_alert");
		$rd = $qd->result();
		$this->params['data'] = $rd;*/
				
		$this->params["content"] =  $this->load->view('mod_device_alert/device_alert_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "alert_create";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("alert_id","desc");
		$this->dbtransporter->group_by("alert_vehicle_device");
		$this->dbtransporter->where("alert_flag", 0);
		
		if($this->sess->user_group > 0){
			$this->dbtransporter->where("alert_vehicle_group", $this->sess->user_group);
		}
		else if($this->sess->user_company > 0){
			$this->dbtransporter->where("alert_vehicle_company", $this->sess->user_company);
		}
		else{
			$this->dbtransporter->where("alert_vehicle_user", $this->sess->user_id);
		}
		
		
		switch($field)
		{
			case "alert_vehicle_no":
				$this->dbtransporter->where("alert_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "alert_vehicle_name":
				$this->dbtransporter->where("alert_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			/*case "droppoint_distrep":
				if($distrep2 != "")
					$this->dbtransporter->where("droppoint_distrep", $distrep2);				
			break;*/
			
		}
		
		if($searchdate == "periode"){
			if(isset($startdate) && $startdate != "")
			{
				$this->dbtransporter->where("alert_create >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
			}
			if(isset($enddate) && $enddate != "")
			{
				$this->dbtransporter->where("alert_create <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
			}
		}
		
		/*if($searchdate == "all"){
			//search awal & akhir bulan
			$month = date('m');
			$year = date('Y');
			
			$startdate = date('Y-m-d', strtotime($year."-".$month."-"."01"));
			$enddate = date('Y-m-t', strtotime($startdate));
			
			if(isset($startdate) && $startdate != "")
			{
				$this->dbtransporter->where("target_startdate >=",date("Y-m-d",strtotime($startdate)));
			}
			if(isset($enddate) && $enddate != "")
			{
				$this->dbtransporter->where("target_enddate <=",date("Y-m-d",strtotime($enddate)));
			}
		}*/
		
		//$q = $this->dbtransporter->get("device_alert", $this->config->item('limit_records'), $offset);
		$q = $this->dbtransporter->get("device_alert");
		$rows = $q->result();
		//print_r(date("Y-m-d ",strtotime($startdate." "."23:59:59"))." ".date("Y-m-d",strtotime($enddate." "."23:59:59")));exit();
		//hitung total
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by("alert_id","desc");
		$this->dbtransporter->group_by("alert_vehicle_device");		
		$this->dbtransporter->where("alert_flag", 0);
		if($this->sess->user_group > 0){
			$this->dbtransporter->where("alert_vehicle_group", $this->sess->user_group);
		}
		else if($this->sess->user_company > 0){
			$this->dbtransporter->where("alert_vehicle_company", $this->sess->user_company);
		}
		else{
			$this->dbtransporter->where("alert_vehicle_user", $this->sess->user_id);
		}
		
		
		switch($field)
		{
			case "alert_vehicle_no":
				$this->dbtransporter->where("alert_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "alert_vehicle_name":
				$this->dbtransporter->where("alert_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			/*case "droppoint_distrep":
				if($distrep2 != "")
					$this->dbtransporter->where("droppoint_distrep", $distrep2);				
			break;*/
			
		}
		
		if($searchdate == "periode"){
			if(isset($startdate) && $startdate != "")
			{
				$this->dbtransporter->where("alert_create >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
			}
			if(isset($enddate) && $enddate != "")
			{
				$this->dbtransporter->where("alert_create <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
			}
		}
		
		/*if($searchdate == "all"){
			//search awal & akhir bulan
			$month = date('m');
			$year = date('Y');
			
			$startdate = date('Y-m-d', strtotime($year."-".$month."-"."01"));
			$enddate = date('Y-m-t', strtotime($startdate));
			
			if(isset($startdate) && $startdate != "")
			{
				$this->dbtransporter->where("target_startdate >=",date("Y-m-d",strtotime($startdate)));
			}
			if(isset($enddate) && $enddate != "")
			{
				$this->dbtransporter->where("target_enddate <=",date("Y-m-d",strtotime($enddate)));
			}
		}*/
		
		$qt = $this->dbtransporter->get("device_alert");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		//startdate //enddate
		$this->params['startdate'] = $startdate;
		$this->params['enddate'] = $enddate;
		
		//Get company
		$row_company = $this->get_company();
		$this->params['rcompany'] = $row_company;
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		
		$html = $this->load->view('mod_device_alert/device_alert_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function delete($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$data["alert_flag"] = 1;
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("alert_id", $id);
		if($this->dbtransporter->update("device_alert", $data)){
		
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		echo json_encode($callback);
	}
	
	function get_device_alert_show(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$nowdate = date("Y-m-d");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("alert_id","asc");
		$this->dbtransporter->group_by("alert_vehicle_device");
		$this->dbtransporter->select("alert_id");
		$this->dbtransporter->where("alert_create >=",date("Y-m-d H:i:s",strtotime($nowdate." "."00:00:00")));
		$this->dbtransporter->where("alert_create <=",date("Y-m-d H:i:s",strtotime($nowdate." "."23:59:59")));
		if($this->sess->user_group > 0){
			$this->dbtransporter->where("alert_vehicle_group", $this->sess->user_group);
		}
		else if($this->sess->user_company > 0){
			$this->dbtransporter->where("alert_vehicle_company", $this->sess->user_company);
		}
		else{
			$this->dbtransporter->where("alert_vehicle_user", $this->sess->user_id);
		}
		$this->dbtransporter->where("alert_flag", 0);
		$qd = $this->dbtransporter->get("device_alert");
		$rd = $qd->result();
		$total_alert = count($rd);
		
		if($total_alert > 0)
		{
			$callback['color'] = "red";
		}
		else
		{
			$callback['color'] = "black";
		}
		$callback['total'] = "Device Alert ("." ". $total_alert . " "." )";
		echo json_encode($callback);
	}
	
	function get_service_alert_show()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("service_alert_vehicle_company", $my_company);
		$this->dbtransporter->where("service_alert_vehicle_view", "0");
		$q = $this->dbtransporter->get("service_alert");
		$row = $q->result();
		$total_alert = count($row);
		
		if($total_alert > 0)
		{
			$callback['color'] = "red";
		}
		else
		{
			$callback['color'] = "black";
		}
		$callback['total'] = "Service Alert ("." ". $total_alert . " "." )";
		echo json_encode($callback);
	}
	
	function get_company(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->select("company_id,company_name");
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();
		
		return $rd;
	}
}