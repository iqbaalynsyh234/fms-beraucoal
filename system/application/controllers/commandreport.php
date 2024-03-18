<?php
include "base.php";

class Commandreport extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->model("dashboardmodel");
	}

	
	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		// if ($this->sess->user_level != 1)
		// {
		// 	redirect(base_url());
		// }

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/report/v_command_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search($name="", $host="", $startdate="", $shour="", $ehour="", $enddate="")
    {
		ini_set('display_errors', 1);
		$company = $this->input->post("company");
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$mapview = 0;
		$tableview = 1;

		$vehicle_no = "-";
		$vehicle_odometer = 0;
		$vehicle_type = "-";
		$vehicle_user_id = 0;
		$error = "";

		if ($vehicle == "" || $vehicle == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}else{
			$datavehicle = explode("@", $vehicle);
			$name = $datavehicle[0];
			$host = $datavehicle[1];

			$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_user_id");
			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name."@".$host);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			$rowvehicle = $q->row();

			if(count($rowvehicle)>0){

				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_name = $rowvehicle->vehicle_name;
				$vehicle_user_id = $rowvehicle->vehicle_user_id;
				$vehicle_device = $rowvehicle->vehicle_device;

				$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
				$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
			}
		}
		
		if ($startdate == "" || $enddate == "")
		{
			$error = "- Invalid Vehicle. Silahkan Pilih Tanggal Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($shour == "" || $ehour == "")
		{
			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		
		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		
		//search data
		$this->dblive = $this->load->database($this->sess->user_dblive, TRUE);
		$this->dblive->order_by("command_date", "asc");
		$this->dblive->where("command_device", $name);
		$this->dblive->where("command_date >=", $sdate);
		$this->dblive->where("command_date <=", $edate);
		$this->dblive->order_by("command_id","desc");
		$this->dblive->from("webtracking_command");
		$q = $this->dblive->get();
		$this->dblive->flush_cache();
		$rows = $q->result();
		
		$params['vehicle_no'] = $vehicle_no;
		$params['vehicle_name'] = $vehicle_name;
		$params['data'] = $rows;
		$params['sdate'] = $sdate;
		$params['edate'] = $edate;
		$html = $this->load->view("dashboard/report/v_command_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);

    }

}
