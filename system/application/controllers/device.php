<?php
include "base.php";

class Device extends Base {

	function Device()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_devicepanel");
		$this->load->model("gpsmodel");
	}

  function index(){
    $getdata                        = $this->m_devicepanel->device();
    // echo "<pre>";
    // var_dump($getdata);die();
    // echo "<pre>";
    $this->params['code_view_menu'] = "configuration";
    $this->params['vehicle']        = $getdata;
    $this->params['vehicletotal']   = sizeof($getdata);

		$this->params["header"]           = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]          = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]      = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]          = $this->load->view('dashboard/device/v_home_device', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
  }

  function armingsave(){
    $userdblive      		= $this->sess->user_dblive;
    $device          		= $this->input->post("armingvehicle");
    $deviceexplode   		= explode("@", $device);
		$deviceforexplode   = explode(".", $device);
		$deviceforexplode2  = explode("@", $deviceforexplode[0]);
    $devicefix       		= $deviceexplode[0];
		$host               = $deviceforexplode2[1];
    $commandtext     		= "ARM";
    $commandstatus   		= 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

    $data = array(
      "command_device" => $devicefix,
      "command_text"   => $commandtext,
      "command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
      "command_date"   => date("Y-m-d H:i:s")
    );

    // echo "<pre>";
    // var_dump($userdblive);die();
    // echo "<pre>";

    $insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
      if ($insert) {
        echo json_encode(array("msg" => "success", "code" => 200));
      }else {
        echo json_encode(array("msg" => "failed", "code" => 400));
      }
  }

	function disarmingsave(){
		$userdblive        	= $this->sess->user_dblive;
    $device          		= $this->input->post("disarmingvehicle");
    $deviceexplode   		= explode("@", $device);
		$deviceforexplode  	= explode(".", $device);
		$deviceforexplode2 	= explode("@", $deviceforexplode[0]);
		$devicefix         	= $deviceexplode[0];
		$host              	= $deviceforexplode2[1];
    $devicefix       		= $deviceexplode[0];
    $commandtext     		= "DISARM";
    $commandstatus   		= 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

    $data = array(
      "command_device" => $devicefix,
      "command_text"   => $commandtext,
      "command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
      "command_date"   => date("Y-m-d H:i:s")
    );

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
      if ($insert) {
        echo json_encode(array("msg" => "success", "code" => 200));
      }else {
        echo json_encode(array("msg" => "failed", "code" => 400));
      }
	}

	function rfidregister(){
		$userdblive        = $this->sess->user_dblive;
    $rfidregvehicle  	 = $this->input->post("rfidregvehicle");
		$rfidnumber        = str_replace(" ", "", $this->input->post("rfidnumber"));
    $deviceexplode   	 = explode("@", $rfidregvehicle);
		$deviceforexplode  = explode(".", $rfidregvehicle);
		$deviceforexplode2 = explode("@", $deviceforexplode[0]);
		$devicefix         = $deviceexplode[0];
		$host              = $deviceforexplode2[1];
    $commandtext     	 = "RFREG";
    $commandstatus   	 = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
      "command_device" => $devicefix,
      "command_text"   => $commandtext,
      "command_status" => $commandstatus,
			"command_hexa"   => $rfidnumber,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
      "command_date"   => date("Y-m-d H:i:s")
    );

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
      if ($insert) {
        echo json_encode(array("msg" => "success", "code" => 200));
      }else {
        echo json_encode(array("msg" => "failed", "code" => 400));
      }
	}

	function rfidcheck(){
		$userdblive        = $this->sess->user_dblive;
    $rfidregvehicle    = $this->input->post("rfidcheckvehicle");
		$deviceexplode   	 = explode("@", $rfidregvehicle);
		$deviceforexplode  = explode(".", $rfidregvehicle);
		$deviceforexplode2 = explode("@", $deviceforexplode[0]);
		$devicefix         = $deviceexplode[0];
		$host              = $deviceforexplode2[1];
    $commandtext    	 = "RFCHK";
    $commandstatus  	 = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
      if ($insert) {
        echo json_encode(array("msg" => "success", "code" => 200));
      }else {
        echo json_encode(array("msg" => "failed", "code" => 400));
      }
	}

	function speedsetting(){
		$userdblive            = $this->sess->user_dblive;
    $speedsettingvehicle   = $this->input->post("speedsettingvehicle");
		$second 	             = $this->input->post("speedsettingsecond");
    $deviceexplode         = explode("@", $speedsettingvehicle);
		$deviceforexplode  		 = explode(".", $speedsettingvehicle);
		$deviceforexplode2 		 = explode("@", $deviceforexplode[0]);
		$devicefix         		 = $deviceexplode[0];
		$host              		 = $deviceforexplode2[1];
    $commandtext           = "SPD";
    $commandstatus         = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_status" => $commandstatus,
			"command_value"  => ($second / 10),
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
      if ($insert) {
        echo json_encode(array("msg" => "success", "code" => 200));
      }else {
        echo json_encode(array("msg" => "failed", "code" => 400));
      }
	}

	function snap(){
		$userdblive        = $this->sess->user_dblive;
    $snapvehicle     	 = $this->input->post("snapvehicle");
    $deviceexplode   	 = explode("@", $snapvehicle);
		$deviceforexplode  = explode(".", $snapvehicle);
		$deviceforexplode2 = explode("@", $deviceforexplode[0]);
		$devicefix         = $deviceexplode[0];
		$host              = $deviceforexplode2[1];
    $commandtext     	 = "SNAP";
    $commandstatus   	 = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
      if ($insert) {
        echo json_encode(array("msg" => "success", "code" => 200));
      }else {
        echo json_encode(array("msg" => "failed", "code" => 400));
      }
	}

	function odometer(){
		$userdblive        = $this->sess->user_dblive;
		$odometervehicle   = $this->input->post("odometervehicle");
		$odometervalue     = $this->input->post("odometervalue");
		$deviceexplode     = explode("@", $odometervehicle);
		$deviceforexplode  = explode(".", $odometervehicle);
		$deviceforexplode2 = explode("@", $deviceforexplode[0]);
		$devicefix         = $deviceexplode[0];
		$host              = $deviceforexplode2[1];
		$commandtext       = "ODO";
		$commandstatus     = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_value"  => $odometervalue,
			"command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
			if ($insert) {
				echo json_encode(array("msg" => "success", "code" => 200));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
	}

	function reboot(){
		$userdblive        = $this->sess->user_dblive;
		$rbtvehicle 	     = $this->input->post("rebootvehicle");
		$deviceexplode     = explode("@", $rbtvehicle);
		$deviceforexplode  = explode(".", $rbtvehicle);
		$deviceforexplode2 = explode("@", $deviceforexplode[0]);
		$devicefix         = $deviceexplode[0];
		$host              = $deviceforexplode2[1];
		$commandtext       = "RBT";
		$commandstatus     = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
			if ($insert) {
				echo json_encode(array("msg" => "success", "code" => 200));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
	}

	function intervalonoff(){
		$userdblive           = $this->sess->user_dblive;
		$intervalonoffvehicle = $this->input->post("intervalonoffvehicle");
		$commandtext 			    = $this->input->post("intervalonofftype");
		$intervalonoffvalue   = $this->input->post("intervalonoffvalue");
		$deviceexplode        = explode("@", $intervalonoffvehicle);
		$deviceforexplode     = explode(".", $intervalonoffvehicle);
		$deviceforexplode2    = explode("@", $deviceforexplode[0]);
		$devicefix            = $deviceexplode[0];
		$host                 = $deviceforexplode2[1];
		$commandstatus        = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_status" => $commandstatus,
			"command_value"  => ($intervalonoffvalue / 10),
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
			if ($insert) {
				echo json_encode(array("msg" => "success", "code" => 200));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
	}

	function positioning(){
		$userdblive           = $this->sess->user_dblive;
		$intervalonoffvehicle = $this->input->post("positioningvehicle");
		$deviceexplode        = explode("@", $intervalonoffvehicle);
		$deviceforexplode     = explode(".", $intervalonoffvehicle);
		$deviceforexplode2    = explode("@", $deviceforexplode[0]);
		$devicefix            = $deviceexplode[0];
		$host                 = $deviceforexplode2[1];
		$commandtext 					= "POS";
		$commandstatus        = 0;

		$vehicle        = $this->m_devicepanel->getdatavehicle($deviceforexplode[0]);
		$json        		= json_decode($vehicle[0]['vehicle_info']);

		$data = array(
			"command_device" => $devicefix,
			"command_text"   => $commandtext,
			"command_status" => $commandstatus,
			"command_host"   => $host,
			"command_ip"     => $vehicle[0]['vehicle_server'],
			"command_port"   => $json->vehicle_port,
			"command_date"   => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_devicepanel->insertdata("webtracking_command", $userdblive, $data);
			if ($insert) {
				echo json_encode(array("msg" => "success", "code" => 200));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
	}


}
