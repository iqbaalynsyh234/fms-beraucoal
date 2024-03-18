	<?php
	include "base.php";

	class Maintenancemanagement extends Base {
		var $period1;
		var $period2;
		var $tblhist;
		var $tblinfohist;
		var $otherdb;
		function Maintenancemanagement()
		{
			parent::Base();

			$this->load->model("gpsmodel");
	    $this->load->model("m_maintenance");
		}

		function index()
		{

	    // $user_id                 = $this->sess->user_id;
	    // $sql                     = "SELECT * FROM `webtracking_vehicle` where vehicle_user_id = '$user_id' ORDER BY `vehicle_no` ASC ";
	    // $q                       = $this->db->query($sql);
	    // $result                  = $q->result_array();
	    // $this->params['vehicle'] = $result;

	    $this->params['sortby'] = "mobil_id";
			$this->params['orderby'] = "asc";
	    $this->params['title'] = "Maintenance Management";

	    $this->params["content"] = $this->load->view('transporter/maintenance/v_maintenance', $this->params, true);
			$this->load->view("templatesess", $this->params);
	  }

		function oogreport(){
			$this->params['sortby'] = "mobil_id";
		  $this->params['orderby'] = "asc";
		  $this->params['title'] = "Powerblock Out Of Geofence";

			$data = array(
				"transporter_isread" => "1"
			);

			$changeisread = $this->m_maintenance->changethisisread("powerblock_alert", $data);

		  $this->params["content"] = $this->load->view('transporter/maintenance/v_oogpbi', $this->params, true);
		  $this->load->view("templatesess", $this->params);
		}

	  function showvehicleonfirstpage(){
	    $user_id                 = $this->sess->user_id;
			$user_company                 = $this->sess->user_company;
	    $sql                     = "SELECT * FROM `webtracking_vehicle` where vehicle_user_id = '$user_id' and vehicle_status != 3 ORDER BY `vehicle_no` ASC ";
	    $q                       = $this->db->query($sql);
	    $result                  = $q->result_array();
	    $finaldata = array();
			$getvehicleconfig = $this->m_maintenance->vehicleconfig("maintenance_configuration", $user_company);
	    // echo "<pre>";
	    // var_dump($finaldata);die();
	    // echo "<pre>";
	    $this->params['vehicle'] 		= $result;
			$this->params['vehicle_config'] 		= $getvehicleconfig;

			$html = $this->load->view('transporter/maintenance/v_maintenance_list', $this->params, true);
			$callback['html'] = $html;
			echo json_encode($callback);
	  }

		function showdataoog(){
	    $user_id    	= $this->sess->user_id;
			$user_company = $this->sess->user_company;
			$this->dbtransporter = $this->load->database("transporter", true);
	    $sql        	= "SELECT * FROM `transporter_powerblock_alert` where transporter_alert_vehicleuserid = '$user_id' and transporter_isread = 1 ORDER BY `transporter_alert_vehicleno` ASC ";
	    $q          	= $this->dbtransporter->query($sql);
	    $result     	= $q->result_array();
	    $finaldata = array();
	    // echo "<pre>";
	    // var_dump($finaldata);die();
	    // echo "<pre>";
	    $this->params['dataoog'] 		= $result;

			$html = $this->load->view('transporter/maintenance/v_oogpbilist', $this->params, true);
			$callback['html'] = $html;
			echo json_encode($callback);
	  }



	  function forsetservicess(){
			$vehicle_id        = $this->input->post('id');
	    $user_id         	 = $this->sess->user_id;
			$user_company      = $this->sess->user_company;

			$getservicetype    = $this->m_maintenance->gogetservicetype("service_type");
			$resultservicetype = $getservicetype->result_array();

	    $sql             	 = "SELECT * FROM `webtracking_vehicle` where vehicle_id = '$vehicle_id' and vehicle_user_id = '$user_id' ORDER BY `vehicle_no` ASC ";
	    $q               	 = $this->db->query($sql);
	    $result          	 = $q->result_array();
			$cekvehiclenonya   = $this->m_maintenance->cekvehiclenodbtransporter("maintenance_configuration", $result[0]['vehicle_no'])->result_array();
			$valueafterchcking = sizeof($cekvehiclenonya);

			$getworkshop 			= $this->m_maintenance->g_all("workshop", "workshop_company", $user_company, "workshop_name", "asc");
			// echo "<pre>";
	    // var_dump($getworkshop);die();
	    // echo "<pre>";
			$this->params['data']                  = $resultservicetype;
			$this->params['dataconfigmaintenance'] = $cekvehiclenonya;
			$this->params['sizeconfig']            = $valueafterchcking;
			$this->params['workshop']              = $getworkshop;
			$this->params['vehicledata']           = $result;
			$html                                  = $this->load->view('transporter/maintenance/v_forshowsetservicess', $this->params, true);
			$callback['html']                      = $html;
			echo json_encode($callback);
	  }

	  function forconfigservicess(){
	    $vehicle_id = $this->input->post('id');
	    $user_id = $this->sess->user_id;

	    $user_id                 = $this->sess->user_id;
	    $sql                     = "SELECT * FROM `webtracking_vehicle` where vehicle_id = '$vehicle_id' and vehicle_user_id = '$user_id' ORDER BY `vehicle_no` ASC ";
	    $q                       = $this->db->query($sql);
	    $result                  = $q->result_array();

	    $cekvehiclenonya = $this->m_maintenance->cekvehiclenodbtransporter("maintenance_configuration", $result[0]['vehicle_no'])->result_array();
	    $valueafterchcking = sizeof($cekvehiclenonya);
	    // echo "<pre>";
	    // var_dump($valueafterchcking);die();
	    // echo "<pre>";
	      if ($valueafterchcking == 0) {
	        // GX ADA ISINYA
	        $this->params['vehicle'] = $result;
	        $this->params['row'] = $valueafterchcking;

	    		$html = $this->load->view('transporter/maintenance/v_forshowconfigservicess', $this->params, true);
	    		$callback['html'] = $html;
	        $callback['isirow'] = $valueafterchcking;
	    		echo json_encode($callback);
	      }else {
	        // ADA ISINYA
	        $this->params['vehicle'] = $result;
	        $this->params['row'] = $valueafterchcking;
	        $this->params['data'] = $cekvehiclenonya;

	    		$html = $this->load->view('transporter/maintenance/v_forshowconfigservicess', $this->params, true);
	    		$callback['html'] = $html;
	        $callback['isirow'] = $valueafterchcking;
	    		echo json_encode($callback);
	      }

	  }

	  function savethisconfiguration(){
	    $vehicle_no      = $this->input->post('vehicle_no');
	    $vehicle_name    = $this->input->post('vehicle_name');
	    $vehicle_type    = $this->input->post('vehicle_type');
	    $vehicle_year    = $this->input->post('vehicle_year');
	    $no_rangka       = $this->input->post('no_rangka');
	    $no_mesin        = $this->input->post('no_mesin');
	    $stnk_no         = $this->input->post('stnk_no');
	    $stnkexpdate     = $this->input->post('stnkexpdatefix');
	    $kir_no          = $this->input->post('kir_no');
	    $kirexpdate      = $this->input->post('kirexpdatefix');
	    $servicedby      = $this->input->post('servicedby');
	    $valueservicedby = $this->input->post('valueservicedby');
			$vehicle_device   = $this->input->post('vehicle_device');
			$vehicle_type_gps = $this->input->post('vehicle_type_gps');
			$alertlimit       = $this->input->post('alertlimit');

	    // CEK VEHICLE NO
	    $cekvehiclenonya = $this->m_maintenance->cekvehiclenodbtransporter("maintenance_configuration", $vehicle_no)->result_array();
	    $valueafterchcking = sizeof($cekvehiclenonya);
	    // echo "<pre>";
	    // var_dump($valueafterchcking);die();
	    // echo "<pre>";
	      if ($valueafterchcking == 0) {
	        // DATA TIDAK ADA MAKA INPUT
	        $data = array(
	          "maintenance_conf_vehicle_user_company" => $this->sess->user_company,
	          "maintenance_conf_vehicle_no"           => $vehicle_no,
	          "maintenance_conf_vehicle_name"         => $vehicle_name,
	          "maintenance_conf_vehicle_type"         => $vehicle_type,
	          "maintenance_conf_vehicle_year"         => $vehicle_year,
	          "maintenance_conf_no_rangka"            => $no_rangka,
	          "maintenance_conf_no_mesin"             => $no_mesin,
	          "maintenance_conf_stnk_no"              => $stnk_no,
	          "maintenance_conf_stnkexpdate"          => $stnkexpdate,
	          "maintenance_conf_kir_no"               => $kir_no,
	          "maintenance_conf_kirexpdate"           => $kirexpdate,
	          "maintenance_conf_servicedby"           => $servicedby,
	          "maintenance_conf_valueservicedby"      => $valueservicedby,
						"maintenance_conf_vehicle_device"       => $vehicle_device,
						"maintenance_conf_vehicle_type_gps"     => $vehicle_type_gps,
						"maintenance_conf_alertlimit"           => $alertlimit
	        );

	        $insert = $this->m_maintenance->insertDataDbTransporter("maintenance_configuration", $data);
	          if ($insert) {
	            $status = "success";
	          }else {
	            $status = "failed";
	          }
	        $this->params['data'] = $data;
	        $html = $this->load->view('transporter/maintenance/v_forshowconfigservicess', $this->params, true);
	        $callback['html'] = $html;
	        $callback['status'] = $status;
	        $callback['msg'] = "Configuration Inserted";
	        echo json_encode($callback);
	      }else {
	        // DATA ADA MAKA UPDATE
	        $data = array(
	          "maintenance_conf_vehicle_no"      			=> $vehicle_no,
	          "maintenance_conf_vehicle_name"    			=> $vehicle_name,
	          "maintenance_conf_vehicle_type"    			=> $vehicle_type,
	          "maintenance_conf_vehicle_year"    			=> $vehicle_year,
	          "maintenance_conf_no_rangka"       			=> $no_rangka,
	          "maintenance_conf_no_mesin"        			=> $no_mesin,
	          "maintenance_conf_stnk_no"         			=> $stnk_no,
	          "maintenance_conf_stnkexpdate"     			=> $stnkexpdate,
	          "maintenance_conf_kir_no"          			=> $kir_no,
	          "maintenance_conf_kirexpdate"      			=> $kirexpdate,
	          "maintenance_conf_servicedby"      			=> $servicedby,
	          "maintenance_conf_valueservicedby" 			=> $valueservicedby,
						"maintenance_conf_vehicle_device"       => $vehicle_device,
						"maintenance_conf_vehicle_type_gps"     => $vehicle_type_gps,
						"maintenance_conf_alertlimit"           => $alertlimit
	        );

	        $update = $this->m_maintenance->updateDataDbTransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $vehicle_no, $data);
	          if ($update) {
	            $status = "success";
	          }else {
	            $status = "failed";
	          }
	        $this->params['data'] = $data;
	        $html = $this->load->view('transporter/maintenance/v_forshowconfigservicess', $this->params, true);
	        $callback['html'] = $html;
	        $callback['status'] = $status;
	        $callback['msg'] = "Configuration Updated";
	        echo json_encode($callback);
	      }
	  }


		function savetomaintenancehistory(){
			date_default_timezone_set("Asia/Bangkok");
			$user_id        = $this->sess->user_id;
			$user_company   = $this->sess->user_company;
			$tipeservice    = $this->input->post('tipeservice');
			$vehicle_device = $this->input->post('vehicle_device');
			$data = array();

			if ($tipeservice == 2) {
				// KIR
				$v_kirno_setservicess          = $this->input->post('v_kirno_setservicess');
				$v_kirdate_setservicess        = $this->input->post('v_kirdate_setservicess');
				$v_kir_exp_date_setservicess   = $this->input->post('v_kir_exp_date_setservicess');
				$v_kirnote_setservicess        = $this->input->post('v_kirnote_setservicess');
				$v_kirvehicle_no               = $this->input->post('v_kirvehicle_no');
				$v_kirvehicle_name             = $this->input->post('v_kirvehicle_name');
				$v_kir_pelaksana               = $this->input->post('v_kir_pelaksana');
				$v_kir_biaya                   = $this->input->post('v_kir_biaya');
				$v_work_agenc_kir_setservicess = $this->input->post('work_agenc_kir_setservicess');

				$data = array(
					"servicess_tipeservice"    => $tipeservice,
					"servicess_name"           => "KIR",
					"servicess_vehicle_device" => $vehicle_device,
					"servicess_vehicle_no"     => $v_kirvehicle_no,
					"servicess_vehicle_name"   => $v_kirvehicle_name,
					"servicess_nol"            => $v_kirno_setservicess,
					"servicess_date"           => $v_kirdate_setservicess." 00:00:00",
					"servicess_pelaksana"      => $v_kir_pelaksana,
					"servicess_biaya"          => $v_kir_biaya,
					"servicess_note"           => $v_kirnote_setservicess,
					"servicess_work_agencies"  => $v_work_agenc_kir_setservicess,
					"servicess_user_company"   => $user_company
				);

				$dataforupdate = array(
					"maintenance_conf_kir_extendsdate" => $v_kirdate_setservicess,
					"maintenance_conf_kirexpdate"      => $v_kir_exp_date_setservicess
				);
				$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_kirvehicle_no, $dataforupdate);
					if ($update) {
						$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
							if ($insert) {
								$status = "success";
							}else {
								$status = "failed";
							}
							$callback['status'] = $status;
							$callback['msg']    = "Data Succesfully Inserted To Servicess History";
							echo json_encode($callback);
					}else {
						$status = "failed";
						$callback['status'] = $status;
						$callback['msg']    = "Data Succesfully Inserted To Servicess History";
						echo json_encode($callback);
					}
			}elseif ($tipeservice == 3) {
				// PERPANJANG STNK
				$v_perpstnk_vehicle_no           = $this->input->post('v_perpstnk_vehicle_no');
				$v_perpstnk_vehicle_name         = $this->input->post('v_perpstnk_vehicle_name');
				$v_perpstnk_no_setservicess      = $this->input->post('v_perpstnk_no_setservicess');
				$v_perpstnk_date_setservicess    = $this->input->post('v_perpstnk_date_setservicess');
				$v_perpstnk_expdate_setservicess = $this->input->post('v_perpstnk_expdate_setservicess');
				$v_perpstnk_pelaksana            = $this->input->post('v_perpstnk_pelaksana');
				$v_perpstnk_biaya                = $this->input->post('v_perpstnk_biaya');
				$v_perpstnk_note_setservicess    = $this->input->post('v_perpstnk_note_setservicess');
				$work_agenc_stnk_setservicess    = $this->input->post('work_agenc_stnk_setservicess');

				$data = array(
					"servicess_tipeservice"   => $tipeservice,
					"servicess_name"          => "PERPANJANG STNK",
					"servicess_vehicle_device" => $vehicle_device,
					"servicess_vehicle_no"    => $v_perpstnk_vehicle_no,
					"servicess_vehicle_name"  => $v_perpstnk_vehicle_name,
					"servicess_nol"           => $v_perpstnk_no_setservicess,
					"servicess_date"          => $v_perpstnk_date_setservicess." 00:00:00",
					"servicess_pelaksana"     => $v_perpstnk_pelaksana,
					"servicess_biaya"         => $v_perpstnk_biaya,
					"servicess_note"          => $v_perpstnk_note_setservicess,
					"servicess_work_agencies" => $work_agenc_stnk_setservicess,
					"servicess_user_company"  => $user_company
				);

				$dataforupdate = array(
					"maintenance_conf_stnk_extendsdate" => $v_perpstnk_date_setservicess,
					"maintenance_conf_stnkexpdate"      => $v_perpstnk_expdate_setservicess
				);
				$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_perpstnk_vehicle_no, $dataforupdate);
					if ($update) {
						$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
							if ($insert) {
								$status = "success";
							}else {
								$status = "failed";
							}
							$callback['status'] = $status;
							$callback['msg']    = "Data Succesfully Inserted To Servicess History";
							echo json_encode($callback);
					}else {
						$status = "failed";
						$callback['status'] = $status;
						$callback['msg']    = "Data Succesfully Inserted To Servicess History";
						echo json_encode($callback);
					}
			}else {
				// SERVICE
				$v_service_vehicle_no        = $this->input->post('v_service_vehicle_no');
				$v_service_vehicle_name      = $this->input->post('v_service_vehicle_name');
				$v_service_date_setservicess = $this->input->post('v_service_date_setservicess');
				$v_service_pelaksana         = $this->input->post('v_service_pelaksana');
				$v_service_biaya             = $this->input->post('v_service_biaya');
				$v_service_lastodometer      = $this->input->post('v_service_lastodometer');
				$v_service_note_setservicess = $this->input->post('v_service_note_setservicess');
				$work_agenc_setservicess     = $this->input->post('work_agenc_setservicess');

				$data = array(
					"servicess_tipeservice"    => $tipeservice,
					"servicess_name"           => "MAINTENANCE SERVICE",
					"servicess_vehicle_device" => $vehicle_device,
					"servicess_vehicle_no"     => $v_service_vehicle_no,
					"servicess_vehicle_name"   => $v_service_vehicle_name,
					"servicess_nol"            => $v_service_lastodometer,
					"servicess_date"           => $v_service_date_setservicess." 00:00:00",
					"servicess_pelaksana"      => $v_service_pelaksana,
					"servicess_biaya"          => $v_service_biaya,
					"servicess_note"           => $v_service_note_setservicess,
					"servicess_work_agencies"  => $work_agenc_setservicess,
					"servicess_user_company"   => $user_company
			);

			$getconfigbyvehicle_no = $this->m_maintenance->g_all("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, "maintenance_conf_vehicle_no", "asc");
				if ($getconfigbyvehicle_no[0]['maintenance_conf_servicedby'] == "permonth") {
					// JIKA ALERT PER MONTH
					$dataforupdate = array(
						"maintenance_conf_lastodometer" => $v_service_lastodometer,
						"maintenance_conf_last_service" => $v_service_date_setservicess." 00:00:00"
					);
					$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, $dataforupdate);
						if ($update) {
							$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
								if ($insert) {
									$status = "success";
								}else {
									$status = "failed";
								}
								$callback['status'] = $status;
								$callback['msg']    = "Data Succesfully Inserted To Servicess History";
								echo json_encode($callback);
						}else {
							$status = "failed";
							$callback['status'] = $status;
							$callback['msg']    = "Data Succesfully Inserted To Servicess History";
							echo json_encode($callback);
						}
				}else {
					// ALERT PER KM
					$dataforupdate = array(
						"maintenance_conf_lastodometer" => $v_service_lastodometer,
						"maintenance_conf_last_service" => $v_service_date_setservicess." 00:00:00"
					);
					$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, $dataforupdate);
						if ($update) {
							$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
								if ($insert) {
									$status = "success";
								}else {
									$status = "failed";
								}
								$callback['status'] = $status;
								$callback['msg']    = "Data Succesfully Inserted To Servicess History";
								echo json_encode($callback);
						}else {
							$status = "failed";
							$callback['status'] = $status;
							$callback['msg']    = "Data Succesfully Inserted To Servicess History";
							echo json_encode($callback);
						}
				}
		}
	}

	function maintenanceshistory(){
		$getservicetype    = $this->m_maintenance->gogetservicetype("service_type");
		$resultservicetype = $getservicetype->result_array();
		$user_id           = $this->sess->user_id;

		$sql               = "SELECT * FROM `webtracking_vehicle` where vehicle_user_id = '$user_id' ORDER BY `vehicle_no` ASC ";
		$q                 = $this->db->query($sql);
		$result            = $q->result_array();

		$this->params['vehicle']     = $result;
		$this->params['sortby']      = "mobil_id";
		$this->params['orderby']     = "asc";
		$this->params['title']       = "Maintenance History";
		$this->params['servicetype'] = $resultservicetype;
		// echo "<pre>";
		// var_dump($resultservicetype);die();
		// echo "<pre>";

		$this->params["content"] = $this->load->view('transporter/maintenance/v_maintenance_history', $this->params, true);
		$this->load->view("templatesess", $this->params);

	}

	function showmaintenancehistory(){
		$user_company    = $this->sess->user_company;
		$selectservicess = $this->input->post('selectservicess');
		$selectvehicle   = $this->input->post('selectvehicle');
		$date            = $this->input->post('date');
		$enddate         = $this->input->post('enddate');
		$gethistory      = $this->m_maintenance->getformaintenancehistory("servicess_history", $user_company, $selectvehicle, $selectservicess, $date, $enddate);

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$this->params['data']       = $gethistory;
		// $this->params["start_date"] = $date;
		// $this->params["end_date"]   = $enddate;

		$html                       = $this->load->view('transporter/maintenance/v_forshowmaintenancehistory', $this->params, true);
		$callback['error']          = false;
		$callback['html']           = $html;
		echo json_encode($callback);
	}

	function getfornotif(){
		date_default_timezone_set("Asia/Bangkok");
		$datanotifstnk    = array();
		$datanotifkir     = array();
		$datanotifservice = array();
		$user_company     = $this->sess->user_company;



		// GET STNK EXP DATE
		$getstnkexpdate = $this->m_maintenance->getstnkexpdate("maintenance_configuration", $user_company);
		for ($i=0; $i < sizeof($getstnkexpdate); $i++) {
			array_push($datanotifstnk, array(
				"vehicle_no"          => $getstnkexpdate[$i]['maintenance_conf_vehicle_no'],
				"vehicle_name"        => $getstnkexpdate[$i]['maintenance_conf_vehicle_name'],
				"vehicle_type"        => $getstnkexpdate[$i]['maintenance_conf_vehicle_type'],
				"vehicle_stnkno"      => $getstnkexpdate[$i]['maintenance_conf_stnk_no'],
				"vehicle_stnkexpdate" => $getstnkexpdate[$i]['maintenance_conf_stnkexpdate']
			));
		}

		// GET STNK EXP DATE
		$getkirexpdate = $this->m_maintenance->getkirexpdate("maintenance_configuration", $user_company);
		for ($j=0; $j < sizeof($getkirexpdate); $j++) {
			array_push($datanotifkir, array(
				"vehicle_no"         => $getkirexpdate[$j]['maintenance_conf_vehicle_no'],
				"vehicle_name"       => $getkirexpdate[$j]['maintenance_conf_vehicle_name'],
				"vehicle_type"       => $getkirexpdate[$j]['maintenance_conf_vehicle_type'],
				"vehicle_kirno"      => $getkirexpdate[$j]['maintenance_conf_kir_no'],
				"vehicle_kirexpdate" => $getkirexpdate[$j]['maintenance_conf_kirexpdate']
			));
		}

		// GET SERVICE SCHEDULE
		$finaldata    = array();
		$finaldatafix = array();
		$servicebykm  = array();
		$getservicescheduleperkm = $this->m_maintenance->getservicescheduleperkm("maintenance_configuration", $user_company);
		for ($i=0; $i < sizeof($getservicescheduleperkm); $i++) {
			$lasttime           = 0;
			$device             = $getservicescheduleperkm[$i]['maintenance_conf_vehicle_device'];
			$type_gps           = $getservicescheduleperkm[$i]['maintenance_conf_vehicle_type_gps'];
			$arr                = explode("@", $device);
			$devices[0]         = (count($arr) > 0) ? $arr[0]: "";
			$devices[1]         = (count($arr) > 1) ? $arr[1]: "";
			$v_location         = $this->m_maintenance->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $type_gps);
			$getvehicleodometer = $this->m_maintenance->getodobyvehicledevice("webtracking_vehicle", $getservicescheduleperkm[$i]['maintenance_conf_vehicle_device']);

			array_push($finaldata, array(
				"data"             => $v_location,
				"vehicle_odometer" => $getvehicleodometer
			));

			// get alertvalue
			// sisaodometer = (lastodometerfromgps - lastodometerfrominput)
			// jika sisaodometer mendekati atau melebihi alertvalue maka munculkan alert
			// jika tidak alert tidak muncul
			array_push($finaldatafix, array(
				"maintenance_conf_vehicle_no"      => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_no'],
				"maintenance_conf_vehicle_name"    => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_name'],
				"device"                           => $device,
				"type_gps"                         => $type_gps,
				"maintenance_conf_servicedby"      => $getservicescheduleperkm[$i]['maintenance_conf_servicedby'],
				"lastodometerfromgps"              => round(($finaldata[$i]['data'][0]['gps_info_distance'])/1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer']),
				"maintenance_conf_valueservicedby" => $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby'],
				"maintenance_conf_lastodometer"    => $getservicescheduleperkm[$i]['maintenance_conf_lastodometer'],
				"maintenance_conf_last_service"    => $getservicescheduleperkm[$i]['maintenance_conf_last_service'],
				"finalodometer"                    => round(($getservicescheduleperkm[$i]['maintenance_conf_lastodometer'] + $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']) - $getservicescheduleperkm[$i]['maintenance_conf_alertlimit']),
			));

			$odometerforservice = "";
			if (round($getservicescheduleperkm[$i]['maintenance_conf_lastodometer']) == "") {
				$odometerforservice = round(($finaldata[$i]['data'][0]['gps_info_distance'])/1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer'] +  $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']);
			}else {
				$odometerforservice = round(($getservicescheduleperkm[$i]['maintenance_conf_lastodometer'] + $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']));
			}

			if ($finaldatafix[$i]['lastodometerfromgps'] >= $finaldatafix[$i]['finalodometer']) {
				array_push($servicebykm, array(
					"kondisi"               => "1",
					"vehicle_no"            => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_no'],
					"vehicle_name"          => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_name'],
					"device"                => $device,
					"type_gps"              => $type_gps,
					"servicedby"            => $getservicescheduleperkm[$i]['maintenance_conf_servicedby'],
					"lastodometerfromgps"   => round(($finaldata[$i]['data'][0]['gps_info_distance'])/1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer']),
					"alertperkm"            => $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby'],
					"lastodometerfrominput" => $getservicescheduleperkm[$i]['maintenance_conf_lastodometer'],
					"last_service"          => $getservicescheduleperkm[$i]['maintenance_conf_last_service'],
					"odometerforservice"    => $odometerforservice,
				));
			}
		}

		$getserviceschedulepermonth = $this->m_maintenance->getserviceschedulepermonth("maintenance_configuration", $user_company);
		$sizepermont                = sizeof($getserviceschedulepermonth);
		$servicedbymonth            = array();
		for ($b=0; $b < $sizepermont; $b++) {
			if (date("Y-m-d") >= date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service']."+".$getserviceschedulepermonth[$b]['maintenance_conf_alertlimit']."Month"))) {
				array_push($servicedbymonth, array(
					"kondisi" 	 	 	 => "2",
					"vehicle_no"     => $getserviceschedulepermonth[$b]['maintenance_conf_vehicle_no'],
					"vehicle_name"   => $getserviceschedulepermonth[$b]['maintenance_conf_vehicle_name'],
					"service_setiap" => $getserviceschedulepermonth[$b]['maintenance_conf_valueservicedby'],
					"servicedby"     => $getserviceschedulepermonth[$b]['maintenance_conf_servicedby'],
					"last_service"   => date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service'])),
					"next_service"   => date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service']."+".$getserviceschedulepermonth[$b]['maintenance_conf_valueservicedby']."Month")),
					"current_date"   => date("Y-m-d")
				));
			}
		}

		// IF USERID == POWERBLOCK
		$user_id                 = $this->sess->user_id;
		if ($user_id == "1147") {
			$getfromtable = $this->m_maintenance->getalerttable("powerblock_alert", "transporter_isread", "0");
			$callback['total_oogpbi']               = sizeof($getfromtable);
			$callback['data_oogpbi']                = $getfromtable;
		}


		// echo "<pre>";
		// var_dump($getfromtable);die();
		// echo "<pre>";
		$callback['total_stnkexpdate']          = sizeof($datanotifstnk);
		$callback['data_notifstnk']             = $datanotifstnk;
		$callback['total_kirexpdate']           = sizeof($datanotifkir);
		$callback['data_notifkir']              = $datanotifkir;
		$callback['total_notifserviceperkm']    = sizeof($servicebykm);
		$callback['data_notifserviceperkm']     = $servicebykm;
		$callback['total_notifservicepermonth'] = sizeof($servicedbymonth);
		$callback['data_notifservicepermonth']  = $servicedbymonth;
		echo json_encode($callback);
	}


































}
