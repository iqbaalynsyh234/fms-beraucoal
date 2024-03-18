<?php
include "base.php";

class Poweroffreport extends Base {
	var $otherdb;

	function Poweroffreport()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}

	function index(){

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/list_poweroff', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function search_old()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$type = $this->input->post("type");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}
		
		
		$list_1 = array('T5','T5SILVER','T5PULSE','T5DOOR','T5');
		$list_2 = array('GT06', 'GT06PTO', 'GT06N', 'TK303', 'TK309', 'TK309N', 'TK315N', 'TK309PTO', 'TK315DOOR', 'A13', 'TK315_NEW', 'TK309_NEW', 'TK315DOOR_NEW','TK315','GT06_NEW','AT5','X3','X3_DOOR');
		
		if (count($rowvehicle) > 0 ){
			//get table port
			$vehicle_ex = explode("@", $rowvehicle->vehicle_device);
			$name = $vehicle_ex[0];
			$host = $vehicle_ex[1];
			if (in_array(strtoupper($rowvehicle->vehicle_type), $list_1)){
				$alert_code = "BO010";
				$tables = $this->gpsmodel->getTable($rowvehicle);
				
				$this->db = $this->load->database($tables["dbname"], TRUE);
				$this->db->select("gps_name,gps_alert,gps_time,gps_latitude_real,gps_longitude_real,gps_longitude,gps_latitude,gps_ew,gps_ns");
				$this->db->order_by("gps_id", "asc");
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$this->db->where("gps_alert", $alert_code);
				$this->db->where("gps_time >=", $sdate);
				$this->db->where("gps_time <=", $edate);
				$qalert = $this->db->get("gps_alert");
				$rowalert = $qalert->result();
				
			}
			else if (in_array(strtoupper($rowvehicle->vehicle_type), $list_2)){
				$alert_code = "dt";
				$tables = $this->gpsmodel->getTable($rowvehicle);
			
				$this->db = $this->load->database($tables["dbname"], TRUE);
				$this->db->select("device,datetime,item,longitude,latitude");
				$this->db->order_by("id", "asc");
				$this->db->where("device", $rowvehicle->vehicle_device);
				$this->db->where("item", "dt");
				$this->db->where("datetime >=", $sdate);
				$this->db->where("datetime <=", $edate);
				$qalert = $this->db->get("gps_alert");
				$rowalert = $qalert->result();
			}
		}
		
		$params['data'] = $rowalert;
		$params['vehicle'] = $rowvehicle;
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
		{
			$html = $this->load->view("transporter/report/result_poweroff", $params, true);	
		}
		else
		{
			$html = $this->load->view("transporter/report/result_poweroff_other", $params, true);	
		}
		
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

		return;
	}
	
	function search()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$type = $this->input->post("type");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		$user_dblive = $this->sess->user_dblive;
		
		
			//get table port
			$vehicle_ex = explode("@", $rowvehicle->vehicle_device);
			$name = $vehicle_ex[0];
			$host = $vehicle_ex[1];
			
				$alert_code = array('BO010','dt');
				$tables = $this->gpsmodel->getTable($rowvehicle);
				
				$this->db = $this->load->database($user_dblive, TRUE);
				$this->db->select("gps_name,gps_alert,gps_time,gps_latitude_real,gps_longitude_real,gps_longitude,gps_latitude,gps_ew,gps_ns");
				$this->db->order_by("gps_id", "asc");
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$this->db->where_in("gps_alert", $alert_code);
				$this->db->where("gps_time >=", $sdate);
				$this->db->where("gps_time <=", $edate);
				$qalert = $this->db->get("gps_alert");
				$rowalert = $qalert->result();
			
		
		$params['data'] = $rowalert;
		$params['vehicle'] = $rowvehicle;
		$html = $this->load->view("transporter/report/result_poweroff", $params, true);	
		
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

		return;
	}

}
