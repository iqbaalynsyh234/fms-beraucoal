<?php
include "base.php";

class Safetyboard extends Base {

	function __construct()
	{
		parent::Base();
		//$this->load->model("safetydashboardmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_securityevidence");
	}

	//new summary
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

		$rows                = $this->dashboardmodel->getvehicle_report();
		$rows_company        = $this->dashboardmodel->get_company_bylevel();
		$dateyesterdaystart  = date('Y-m-d 00:00:00', strtotime("-1 days"));
		$dateyesterdayend    = date('Y-m-d 23:59:59', strtotime("-1 days"));
		$report           = "alarm_";
		$m1 							= date("F", strtotime($dateyesterdaystart));
		$m2 							= date("F", strtotime($dateyesterdayend));
		$year             = date("Y");

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
		$table            = strtolower($dbtable);

		$getdatayesterday = $this->dashboardmodel->getyesterdaydata($table, $dateyesterdaystart, $dateyesterdayend);
		$arrayadas = array();
		$arraydsm  = array();
		for ($i=0; $i < sizeof($getdatayesterday); $i++) {
			$getalarmgroup = $this->m_securityevidence->cekalarmgroup($getdatayesterday[$i]['report_type']);
			// DSM
				if ($getalarmgroup[0]['alarm_group'] == 1) {
					array_push($arraydsm, array(
						"report_type" => $getdatayesterday[$i]['report_type'],
						"report_name" => $getdatayesterday[$i]['report_name'],
						"jumlah"      => $getdatayesterday[$i]['jumlah'],
						"group"       => $getalarmgroup[0]['alarm_group'],
					));
				}elseif ($getalarmgroup[0]['alarm_group'] == 2) {
					// ADAS
					array_push($arrayadas, array(
						"report_type" => $getdatayesterday[$i]['report_type'],
						"report_name" => $getdatayesterday[$i]['report_name'],
						"jumlah"      => $getdatayesterday[$i]['jumlah'],
						"group"       => $getalarmgroup[0]['alarm_group'],
					));
				}
		}

		// echo "<pre>";
		// var_dump($arrayadas);die();
		// echo "<pre>";

		$data_dump_1                    = "id";
		$data_dump_2                    = "name";

		$this->params["adas"]       		= $arrayadas;
		$this->params["dsm"]        		= $arraydsm;
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";
		$this->params["id"]             = $data_dump_1;
		$this->params["name"]           = $data_dump_2;

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/report/vsafetyboard2', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search(){
		ini_set('display_errors', 1);
		/*$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$enddate          = $this->input->post("enddate");
		$shour            = $this->input->post("shour");
		$ehour            = $this->input->post("ehour");
		$speedfilterbrake = $this->input->post("speedfilterbrake");

		$mapview          = 0;
		$tableview        = 1;

		$vehicle_no       = "-";
		$vehicle_odometer = 0;
		$vehicle_type     = "-";
		$vehicle_user_id  = 0;
		$error            = "";


		if ($vehicle == "" || $vehicle == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}else{
			$datavehicle = explode("@", $vehicle);
			$name        = $datavehicle[0];
			$host        = $datavehicle[1];

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name."@".$host);
			$this->db->where("vehicle_status <>", 3);
			$q           = $this->db->get("vehicle");
			$rowvehicle  = $q->row();

			if(count($rowvehicle)>0){

				$vehicle_no       = $rowvehicle->vehicle_no;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type     = $rowvehicle->vehicle_type;
				$vehicle_user_id  = $rowvehicle->vehicle_user_id;

				if (isset($rowvehicle->vehicle_type) && (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))) {
					$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
					$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
				}else{
					$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate." ".$shour)));
					$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate." ".$ehour)));
				}
			}
		}

		if ($startdate == "" || $enddate == "")
		{
			$error = "- Invalid Vehicle. Silahkan Tanggal Report yang ingin ditampilkan \n";
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

		if ($mapview == "1")
		{
			if ($startdate != $enddate)
			{
				$error = "- Untuk menampilkan History Map, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		if ($tableview == "1")
		{
			if ($startdate != $enddate)
			{
				$error = "- Untuk menampilkan Table, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
				//PORT Only
				if (isset($rowvehicle->vehicle_info))
				{
					$json = json_decode($rowvehicle->vehicle_info);
					if (isset($json->vehicle_ip) && isset($json->vehicle_port))
					{
						$databases = $this->config->item('databases');
						if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
						{
							$database      = $databases[$json->vehicle_ip][$json->vehicle_port];
							$table         = $this->config->item("external_gpstable");
							$tableinfo     = $this->config->item("external_gpsinfotable");
							$this->dbhist  = $this->load->database($database, TRUE);
							$this->dbhist2 = $this->load->database("gpshistory",true);
						}
						else
						{
							$table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
							$tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
							$this->dbhist  = $this->load->database("default", TRUE);
							$this->dbhist2 = $this->load->database("gpshistory",true);
						}

						$vehicle_device = explode("@", $rowvehicle->vehicle_device);
						$vehicle_no     = $rowvehicle->vehicle_no;
						$vehicle_dev    = $rowvehicle->vehicle_device;
						$vehicle_name   = $rowvehicle->vehicle_name;
						$vehicle_type   = $rowvehicle->vehicle_type;

						if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE")
						{
							$tablehist     = $vehicle_device[0]."@t5_gps";
							$tablehistinfo = $vehicle_device[0]."@t5_info";
						}
						else
						{
							$tablehist     = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
							$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
						}


							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,gps_cs,
												   gps_info_device,gps_info_io_port,gps_info_distance");
							$this->dbhist->where("gps_info_device", $rowvehicle->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);
							$this->dbhist->where("gps_cs",53);

								if ($speedfilterbrake == 0) {
									$this->dbhist->where("gps_speed =",0);
								}else {
									$this->dbhist->where("gps_speed >=",0);
									$this->dbhist->where("gps_speed <=",2);
								}

							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->group_by("gps_time");
							$this->dbhist->limit(3000);
							$this->dbhist->from($table);
							$q     = $this->dbhist->get();
							$rows1 = $q->result();


							$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,gps_cs,
												   gps_info_device,gps_info_io_port,gps_info_distance");
							$this->dbhist2->where("gps_info_device", $rowvehicle->vehicle_device);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);
							$this->dbhist2->where("gps_cs",53);
								if ($speedfilterbrake == 0) {
									$this->dbhist->where("gps_speed =",0);
								}else {
									$this->dbhist->where("gps_speed >=",0);
									$this->dbhist->where("gps_speed <=",2);
								}
							$this->dbhist2->order_by("gps_time","asc");
							$this->dbhist2->group_by("gps_time");
							$this->dbhist2->limit(3000);
							$this->dbhist2->from($tablehist);
							$q2    = $this->dbhist2->get();
							$rows2 = $q2->result();

							$rows  = array_merge($rows1, $rows2); //limit data rows = 3000
							$trows = count($rows);

							if($trows > 3000){

								$error               = "- Tidak dapat menampilkan Report. Silahkan kurangi tanggal Report yang dipilih! \n";
								$callback['error']   = true;
								$callback['message'] = $error;

								echo json_encode($callback);
								return;
							}


							//$data = json_encode($data);
							//print_r($data);exit();
					}

				}


		if ($error != "")
		{
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$datafix = array();
		for ($x=0; $x < sizeof($rows); $x++) {
			$position = $this->gpsmodel->GeoReverse($rows[$x]->gps_latitude_real, $rows[$x]->gps_longitude_real);

				array_push($datafix, array(
					"gps_id"             => $rows[$x]->gps_id,
			    "gps_name"           => $rows[$x]->gps_name,
			    "gps_host"           => $rows[$x]->gps_host,
			    "gps_speed"          => $rows[$x]->gps_speed,
			    "gps_status"         => $rows[$x]->gps_status,
			    "gps_latitude_real"  => $rows[$x]->gps_latitude_real,
			    "gps_longitude_real" => $rows[$x]->gps_longitude_real,
			    "gps_time"           => $rows[$x]->gps_time,
			    "gps_cs"             => $rows[$x]->gps_cs,
			    "gps_info_device"    => $rows[$x]->gps_info_device,
			    "gps_info_io_port"   => $rows[$x]->gps_info_io_port,
			    "gps_info_distance"  => $rows[$x]->gps_info_distance,
					"gps_position"       => $position->display_name
				));
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		$params['tableview']        = $tableview;
		$params['vehicle_no']       = $vehicle_no;
		$params['vehicle_odometer'] = $vehicle_odometer;
		$params['vehicle_type']     = $vehicle_type;
		$params['vehicle_user_id']  = $vehicle_user_id;
		$params['totalgps']         = $trows;
		$params['data']             = $datafix;
		$params['sdate']            = $sdate;
		$params['edate']            = $edate;


		*/
		//$html = $this->load->view("dashboard/report/vsafetyboard_result", $params, true);
		$html = $this->load->view("dashboard/report/vsafetyboard_result", true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);

    }

}
