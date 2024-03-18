	<?php
include "base.php";

class History_hour_report extends Base {
	var $otherdb;
	
	function History_hour_report()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		
	}
	
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		
		$this->params["content"] = $this->load->view('transporter/report/mn_history_hour', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function search()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$checkdetail = $this->input->post("checkdetail");
		$report = "history_hour_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		
		$error = "";
		
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";	
		}
		
		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
		
		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}
		
		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != "")
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		
		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		if ($vehicle != "")
		{
			$rv = $qv->row();
		}else{
			$rv = $qv->result();
		}
		//end get vehicle
	
		//get report
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbreport = $this->load->database("pcl_report",true);
			$this->dbreport->order_by("history_hour_no","asc");
			$this->dbreport->order_by("history_hour_date","asc");
			if(isset($vehicle) && ($vehicle != ""))  {
				$this->dbreport->where("history_hour_device", $rv->vehicle_device);
			}
			$this->dbreport->where("history_hour_date >=",$sdate);
			$this->dbreport->where("history_hour_date <=", $edate);
			$this->dbreport->where("history_hour_user",$this->sess->user_id);
			$q = $this->dbreport->get($dbtable);
		
			if ($q->num_rows>0)
			{
				$rows = $q->result();
			}
		}
		
		//select config time
		$this->dbreport->order_by("time_start","asc");
		$qtime = $this->dbreport->get("history_hour_config_time");
		$rowstime = $qtime->result();

		$params['data'] = $rows;
		$params['vehicle'] = $rv;
		$params['sdate'] = $sdate;
		$params['edate'] = $edate;
		$params['checkdetail'] = $checkdetail;
		$params['configtime'] = $rowstime;
		
		$html = $this->load->view("transporter/report/list_result_history_hour", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;
		
	}
	
	
}