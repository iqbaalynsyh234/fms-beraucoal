<?php
include "base.php";

class Joborder extends Base {
	var $otherdb;

	function Joborder()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->model("smsmodel");
		$this->load->model("m_joborder");
	}

	function index(){
		$device     = $this->m_joborder->getdevice();
		$port_list  = array("PORT BIB","PORT BIR","PORT TIA");
		$rom_list   = array("ROM 01","ROM 02","ROM 03","ROM 06","ROM 07","ROM 08","PIT KUSAN");
		$alldatajob = $this->m_joborder->alldatajob();
		// echo "<pre>";
		// var_dump($alldatajob);die();
		// echo "<pre>";
    $this->params['code_view_menu']  = "configuration";
		$this->params['vehicle'] 				 = $device;
		$this->params['datajob'] 				 = $alldatajob;
		$this->params['rom'] 				     = $rom_list;
		$this->params['port'] 				   = $port_list;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/joborder/v_joborder', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }

	function savejoborder(){
		$vehicle       = $this->input->post('vehicle');
		$datavehicle   = $this->m_joborder->getdevicebyvdevice($vehicle);
		$orderdate     = date("Y-m-d", strtotime($this->input->post('orderdate')));
		$ordertime     = $this->input->post('ordertime');
		$orderdatetime = $orderdate.' '.$ordertime.':00';
		$romorder      = $this->input->post('romorder');
		$etatorom      = $this->input->post('etatorom');
		$portorder     = $this->input->post('portorder');
		$etatoport     = $this->input->post('etatoport');

		$ordervoice = "Tugas baru. Anda harus sampai ke, ".$romorder.". pukul ".$etatorom.", dan sampai ke, ".$portorder." pukul ".$etatoport.".";
		// "Tugas baru. Anda harus sampai ke ROM 02. pukul 15:00. dan sampai ke. PORT BIR pukul 16:00."

		$data = array(
			"order_vehicle_device"  => $vehicle,
			"order_vehicle_id"      => $datavehicle[0]['vehicle_id'],
			"order_vehicle_no"      => $datavehicle[0]['vehicle_no'],
			"order_vehicle_name"    => $datavehicle[0]['vehicle_name'],
			"order_vehicle_company" => $datavehicle[0]['vehicle_company'],
			"order_imei_mdt"        => $datavehicle[0]['vehicle_mdt'],
			"order_datetime"        => $orderdatetime,
			"order_to_rom"          => $romorder,
			"order_eta_rom"         => $orderdate.' '.$etatorom.':00',
			"order_to_port"         => $portorder,
			"order_eta_port"        => $orderdate.' '.$etatoport.':00',
			"order_voice"           => $ordervoice,
			"order_submit"          => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_joborder->insertdata("ts_order", $data);
			if ($insert) {
				echo json_encode(array("code" => "200"));
			}else {
				echo json_encode(array("code" => "400"));
			}
	}

}
