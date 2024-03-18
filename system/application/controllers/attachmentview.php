<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Attachmentview extends Base {
	function Attachmentview()
	{
		parent::Base();
		// DASHBOARD START
    $this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
    // DASHBOARD END
		$this->load->model("gpsmodel");
    $this->load->model("m_securityevidence");
		$this->load->model("m_poipoolmaster");
		$this->load->model("m_dashboardview");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
	}

	// DAFTAR ISI
	// 1. ATTACHMENT VIEW SECURITY EVIDENCE
	// 2. ATTACHMENT VIEW PLAYBACK HISTORY MAP

  function index(){
    echo "You don't have permission to access this page. Please contact your Administrator";
  }

	function evidence($videoID, $imageID, $month, $year, $privilegecode, $userid){
		$this->db->dbdefault = $this->load->database("default", true);
		$this->db->dbdefault->select("*");
		$this->db->dbdefault->where("user_id", $userid);
		$datauser            = $this->db->dbdefault->get("user")->result_array();

		$report          = "alarm_evidence_";
		$reportoverspeed = "overspeed_";
		$jalur           = "";

		switch ($month)
		{
			case "01":
						$dbtable    = $report."januari_".$year;
			break;
			case "02":
						$dbtable = $report."februari_".$year;
			break;
			case "03":
						$dbtable = $report."maret_".$year;
			break;
			case "04":
						$dbtable = $report."april_".$year;
			break;
			case "05":
						$dbtable = $report."mei_".$year;
			break;
			case "06":
						$dbtable = $report."juni_".$year;
			break;
			case "07":
						$dbtable = $report."juli_".$year;
			break;
			case "08":
						$dbtable = $report."agustus_".$year;
			break;
			case "09":
						$dbtable = $report."september_".$year;
			break;
			case "10":
						$dbtable = $report."oktober_".$year;
			break;
			case "11":
						$dbtable = $report."november_".$year;
			break;
			case "12":
						$dbtable = $report."desember_".$year;
			break;
		}
		$table      = strtolower($dbtable);

		$videodata          = $this->m_securityevidence->getVideoByID($videoID, $table);
		$imagedata          = $this->m_securityevidence->getImageByID($imageID, $table);
		$reportdetaildecode = explode("|", $imagedata[0]['alarm_report_gpsstatus']);

		if (sizeof($videodata) > 0) {
			$videourl = $videodata[0]['alarm_report_downloadurl'];
		}else {
			$videourl = 0;
		}

		if (sizeof($videodata) > 0) {
			$imageurl = $imagedata[0]['alarm_report_fileurl'];
		}else {
			$imageurl = 0;
		}

		if ($imagedata[0]['alarm_report_coordinate_start'] != "") {
			$coordstart = $imagedata[0]['alarm_report_coordinate_start'];
				if (strpos($coordstart, '-') !== false) {
					$coordstart  = $coordstart;
				}else {
					$coordstart  = "-".$coordstart;
				}

			$coord       = explode(",", $coordstart);
			$position    = $this->gpsmodel->GeoReverse($coord[0], $coord[1]);
			$rowgeofence = $this->getGeofence_location_live($coord[1], $coord[0], $datauser[0]['user_dblive']);

			// echo "<pre>";
	    // var_dump($videourl);die();
	    // echo "<pre>";

			if($rowgeofence == false){
				$geofence_id           = 0;
				$geofence_name         = "";
				$geofence_speed        = 0;
				$geofence_speed_muatan = "";
				$geofence_type         = "";
				$geofence_speed_limit  = 0;
			}else{
				$geofence_id           = $rowgeofence->geofence_id;
				$geofence_name         = $rowgeofence->geofence_name;
				$geofence_speed        = $rowgeofence->geofence_speed;
				$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
				$geofence_type         = $rowgeofence->geofence_type;

				if($jalur == "muatan"){
					$geofence_speed_limit = $geofence_speed_muatan;
				}else if($jalur == "kosongan"){
					$geofence_speed_limit = $geofence_speed;
				}else{
					$geofence_speed_limit = 0;
				}
			}
		}

		$speedgps = number_format($reportdetaildecode[4]/10, 1, '.', '');

		//http://182.253.236.246:6611/3/5?DownType=3&DevIDNO=142045144822&FLENGTH=1292101&FOFFSET=0&MTYPE=1&FPATH=E%3A%2FgStorage%2FRECORD_FILE%2F142045144822%2F2023-03-15%2F02_65_6501_01_00142045144822230315230007000200.mp4&SAVENAME=02_65_6501_01_00142045144822230315230007000200.mp4&jsession=764b4248e63e428295a805f512fe66a0
		//http://182.253.236.246:6611/3/5?DownType=3&DevIDNO=142045144822&FLENGTH=113169&FOFFSET=925739079&MTYPE=1&FPATH=E%3A%2FgStorage%2FJPEG_FILE%2F2023-03-15%2F20230315-202619_DS1.picfile&jsession=764b4248e63e428295a805f512fe66a0

		/* $videourl = str_replace("http://182.253.236.246:6611/","https://attachment.borneo-indobara.com/",$videourl);
		$imageurl = str_replace("http://182.253.236.246:6611/","https://attachment.borneo-indobara.com/",$imageurl);
		 */

		/* print_r($videourl." XX ");
		print_r($imageurl);exit(); */

		$this->params['videourl']             = $videourl;
		$this->params['imageurl']             = $imageurl;
		$this->params['privilegecode']        = $privilegecode;
		$this->params['contentdata']          = $imagedata;
		$this->params['geofence_speed_limit'] = $geofence_speed_limit;
		$this->params['speed']                = $speedgps;
		$this->params['position']             = $position->display_name;

		// echo "<pre>";
    // var_dump($videourl);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}else {
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}
	}

	function getGeofence_location_live($longitude, $latitude, $vehicle_dblive) {
		$this->db = $this->load->database($vehicle_dblive, true);
		$lng      = $longitude;
		$lat      = $latitude;
		$geo_name = "''";
		$sql      = sprintf("SELECT geofence_name,geofence_id,geofence_speed,geofence_speed_muatan,geofence_type
												FROM webtracking_geofence
												WHERE TRUE
												AND (geofence_name <> %s)
												AND geofence_type = 'ROAD'
												AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
												AND (geofence_status = 1)
												ORDER BY geofence_id DESC LIMIT 1 OFFSET 0", $geo_name, $lng, $lat);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0){
			$row = $q->row();
				/*$total = $q->num_rows();
				for ($i=0;$i<$total;$i++){
				$data = $row[$i]->geofence_name;
				$data = $row;
				return $data;
			}*/
			$data = $row;
			return $data;
		}else{
			$data = false;
			return $data;
		}
	}

	function playbackhistory($vehicleid, $sdate, $edate){
		  // $vehicleidtest = "72153401";
			// $date1         = date("2022-09-18 00:00:00"); // strtotime -> 1663434000
			// $date2         = date("2022-09-18 23:59:00"); // strtotime -> 1663520340
			// $date1convert  = strtotime($date1);
			// $date2convert  = strtotime($date2);

			$vehicleidfix = $vehicleid;
			$date1        = date("Y-m-d H:i:s", $sdate);
			$date2        = date("Y-m-d H:i:s", $edate);
			$day1         = date("d", strtotime($date1));
			$day2         = date("d", strtotime($date2));

			// echo "<pre>";
			// var_dump($vehicleidfix.'-'.$date1.'-'.$date2);die();
			// echo "<pre>";

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_id", $vehicleidfix);
			$this->db->where("vehicle_status <>", 3);
			$q          = $this->db->get("vehicle");
			$rowvehicle = $q->row();

			// echo "<pre>";
			// var_dump($vehicleidfix.'-'.$date1.'-'.$date2);die();
			// echo "<pre>";

			if (count($rowvehicle) > 0) {

				$vehicle_no       = $rowvehicle->vehicle_no;
				$vehicle_name     = $rowvehicle->vehicle_name;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type     = $rowvehicle->vehicle_type;
				$vehicle_user_id  = $rowvehicle->vehicle_user_id;

				$sdate = date("Y-m-d H:i:s", strtotime($date1)- 7*3600); //wita
				$edate = date("Y-m-d H:i:s", strtotime($date2)- 7*3600);    //wita
			}

			if (isset($rowvehicle->vehicle_info)) {
				$json = json_decode($rowvehicle->vehicle_info);

				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');

					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {

						$database = $databases[$json->vehicle_ip][$json->vehicle_port];

						$table         = $this->config->item("external_gpstable");
						$tableinfo     = $this->config->item("external_gpsinfotable");


						$this->dbhist  = $this->load->database($database, TRUE);
						$this->dbhist2 = $this->load->database("gpshistory", true);
					} else {
						$table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$this->dbhist  = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory", true);
					}

					$vehicle_device = explode("@", $rowvehicle->vehicle_device);
					$vehicle_no     = $rowvehicle->vehicle_no;
					$vehicle_dev    = $rowvehicle->vehicle_device;
					$vehicle_name   = $rowvehicle->vehicle_name;
					$vehicle_type   = $rowvehicle->vehicle_type;

					if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
						$tablehist     = $vehicle_device[0] . "@t5_gps";
						$tablehistinfo = $vehicle_device[0] . "@t5_info";
					} else {
						$tablehist     = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
						$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
					}

					$rows1 = array();
					$rows2 = array();
					$rows3 = array();
					$rows4 = array();

					// echo "<pre>";
					// var_dump($sdate.'-'.$edate);die();
					// echo "<pre>";

					if ($day1 == $day2)
					{

						if ($rowvehicle->vehicle_imei = "869926046501587")
						{
							//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																			 gps_longitude,gps_latitude,gps_ew,gps_ns");
							$this->dbhist->where("gps_name", $vehicle_device[0]);
							$this->dbhist->where("gps_host", $vehicle_device[1]);
							// $this->dbhist->where("gps_speed >", 0);
							// $this->dbhist2->where("gps_status", "A");
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);
							$this->dbhist->order_by("gps_time", "asc");
							$this->dbhist->group_by("gps_time");
							//$this->dbhist->limit(6000);
							// $this->dbhist->from($table);
							$q     = $this->dbhist->get($table);
							$rows1 = $q->result();

							// KONDISI KHUSUS RBT BEP 638
							// $this->db638  = $this->load->database("GPS_VT200_TIB_17008", TRUE);
							// $this->db638->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
							//       								   gps_longitude,gps_latitude,gps_ew,gps_ns");
							// $this->db638->where("gps_name", $vehicle_device[0]);
							// $this->db638->where("gps_host", $vehicle_device[1]);
							// // $this->db638->where("gps_speed >", 0);
							// // $this->db6382->where("gps_status", "A");
							// $this->db638->where("gps_time >=", $sdate);
							// $this->db638->where("gps_time <=", $edate);
							// $this->db638->order_by("gps_time", "asc");
							// $this->db638->group_by("gps_time");
							// //$this->db638->limit(6000);
							// // $this->db638->from($table);
							// $q       = $this->db638->get("gps");
							// $rows1638 = $q->result();

							// $sdate.'-'.$edate

							// echo "<pre>";
							// var_dump($rows638);die();
							// echo "<pre>";

							//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																			 gps_longitude,gps_latitude,gps_ew,gps_ns");
							$this->dbhist2->where("gps_name", $vehicle_device[0]);
							$this->dbhist2->where("gps_host", $vehicle_device[1]);
							// $this->dbhist2->where("gps_speed >", 0);
							// $this->dbhist22->where("gps_status", "A");
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);
							$this->dbhist2->order_by("gps_time", "asc");
							$this->dbhist2->group_by("gps_time");
							$q2    = $this->dbhist2->get($tablehist);
							$rows2 = $q2->result();

							// KONDISI KHUSUS RBT BEP 638
							// $this->db6382  = $this->load->database("gpshistory", TRUE);
							// $this->db6382->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
							//       								   gps_longitude,gps_latitude,gps_ew,gps_ns");
							// $this->db6382->where("gps_name", $vehicle_device[0]);
							// $this->db6382->where("gps_host", $vehicle_device[1]);
							// // $this->db6382->where("gps_speed >", 0);
							// // $this->db63822->where("gps_status", "A");
							// $this->db6382->where("gps_time >=", $sdate);
							// $this->db6382->where("gps_time <=", $edate);
							// $this->db6382->order_by("gps_time", "asc");
							// $this->db6382->group_by("gps_time");
							// $q2       = $this->db6382->get($tablehist);
							// $rows2638 = $q2->result();

							// $vehicle_device[0].'-'.$tablehist

							$rows  = array_merge($rows1, $rows2);
							// $rows  = array_merge($rows1, $rows2, $rows1638, $rows2638);
							$trows = count($rows);

							// echo "<pre>";
							// var_dump($rows2638);die();
							// echo "<pre>";

							$totaldata = $trows;
							$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
						}
						else
						{
							//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																			 gps_longitude,gps_latitude,gps_ew,gps_ns");
							$this->dbhist->where("gps_name", $vehicle_device[0]);
							$this->dbhist->where("gps_host", $vehicle_device[1]);
							// $this->dbhist->where("gps_speed >", 0);
							// $this->dbhist2->where("gps_status", "A");
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);
							$this->dbhist->order_by("gps_time", "asc");
							$this->dbhist->group_by("gps_time");
							//$this->dbhist->limit(6000);
							// $this->dbhist->from($table);
							$q     = $this->dbhist->get($table);
							$rows1 = $q->result();

							// $sdate.'-'.$edate

							// echo "<pre>";
							// var_dump($rows1);die();
							// echo "<pre>";

							//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																			 gps_longitude,gps_latitude,gps_ew,gps_ns");
							$this->dbhist2->where("gps_name", $vehicle_device[0]);
							$this->dbhist2->where("gps_host", $vehicle_device[1]);
							// $this->dbhist2->where("gps_speed >", 0);
							// $this->dbhist22->where("gps_status", "A");
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);
							$this->dbhist2->order_by("gps_time", "asc");
							$this->dbhist2->group_by("gps_time");
							$q2    = $this->dbhist2->get($tablehist);
							$rows2 = $q2->result();

							// $vehicle_device[0].'-'.$tablehist

							$rows  = array_merge($rows1, $rows2);
							$trows = count($rows);

							// echo "<pre>";
							// var_dump($tablehist);die();
							// echo "<pre>";

							$totaldata = $trows;
							$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
						}
					}
					else
					{
						$sdate1 = $sdate;
						$edate1 = $edate;

						$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																			 gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist->where("gps_name", $vehicle_device[0]);
						// $this->dbhist->where("gps_speed >", 0);
						//$this->dbhist2->where("gps_status", "A");
						$this->dbhist->where("gps_time >=", $sdate1);
						$this->dbhist->where("gps_time <=", $edate1);
						$this->dbhist->where("gps_longitude_real <>", "11.0000");
						$this->dbhist->order_by("gps_time", "asc");
						$this->dbhist->group_by("gps_time");
						//$this->dbhist->limit(6000);
						$this->dbhist->from($table);
						$q = $this->dbhist->get();
						$rows1 = $q->result();

						$this->dbhist->distinct();

						$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																					gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist->where("gps_name", $vehicle_device[0]);
						// $this->dbhist->where("gps_speed >", 0);
						//$this->dbhist2->where("gps_status", "A");
						$this->dbhist->where("gps_time >=", $sdate1);
						$this->dbhist->where("gps_time <=", $edate1);
						$this->dbhist->where("gps_longitude_real <>", "11.0000");
						$this->dbhist->order_by("gps_time", "asc");
						$this->dbhist->group_by("gps_time");
						//$this->dbhist->limit(6000);
						$this->dbhist->from($table);
						$q2 = $this->dbhist->get();
						$rows2 = $q2->result();


						//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																		 gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist2->where("gps_name", $vehicle_device[0]);
						// $this->dbhist2->where("gps_speed >", 0);
						//$this->dbhist2->where("gps_status", "A");
						$this->dbhist2->where("gps_time >=", $sdate1);
						$this->dbhist2->where("gps_time <=", $edate1);
						$this->dbhist2->where("gps_longitude_real <>", "11.0000");
						$this->dbhist2->order_by("gps_time", "asc");
						$this->dbhist2->group_by("gps_time");
						//$this->dbhist2->limit(6000);
						$this->dbhist2->from($tablehist);
						$q3 = $this->dbhist2->get();
						$rows3 = $q3->result();

						$this->dbhist2->distinct();

						$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
																		 gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist2->where("gps_name", $vehicle_device[0]);
						// $this->dbhist2->where("gps_speed >", 0);
						//$this->dbhist2->where("gps_status", "A");
						$this->dbhist2->where("gps_time >=", $sdate1);
						$this->dbhist2->where("gps_time <=", $edate1);
						$this->dbhist2->where("gps_longitude_real <>", "11.0000");
						$this->dbhist2->order_by("gps_time", "asc");
						$this->dbhist2->group_by("gps_time");
						//$this->dbhist2->limit(6000);
						$this->dbhist2->from($tablehist);
						$q4 = $this->dbhist2->get();
						$rows4 = $q4->result();



						$rows = array_merge($rows1, $rows2, $rows3, $rows4); //limit data rows = 3000
						$trows = count($rows);

						$totaldata = $trows;
						$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
					}
				}
			}

			$datafixhistory = array();
			if (sizeof($data) > 0) {
				$before = 0;

				for ($i=0; $i < sizeof($data); $i++) {
							array_push($datafixhistory, array(
			 				 "gps_id"             => $data[$i]->gps_id,
			 				 "gps_name"           => $data[$i]->gps_name,
			 				 "gps_host"           => $data[$i]->gps_host,
			 				 "gps_speed"          => $data[$i]->gps_speed,
			 				 "gps_status"         => $data[$i]->gps_status,
			 				 "gps_latitude_real"  => $data[$i]->gps_latitude_real,
			 				 "gps_longitude_real" => $data[$i]->gps_longitude_real,
			 				 "gps_time"           => $data[$i]->gps_time,
			 				 "gps_longitude"      => $data[$i]->gps_longitude,
			 				 "gps_latitude"       => $data[$i]->gps_latitude,
			 				 "gps_ew"             => $data[$i]->gps_ew,
			 				 "gps_ns"             => $data[$i]->gps_ns
			 			 ));
				}

				// echo "<pre>";
				// var_dump($datafixhistory);die();
				// echo "<pre>";

				$this->params['vehicle_no']       = $vehicle_no;
				$this->params['vehicle_name']     = $vehicle_name;
				$this->params['vehicle_odometer'] = $vehicle_odometer;
				$this->params['vehicle_type']     = $vehicle_type;
				$this->params['vehicle_user_id']  = $vehicle_user_id;
				$this->params['totalgps']         = $trows;
				$this->params['datacoordinate']   = json_encode($datafixhistory);
				$this->params['totaldata']        = $totaldata;
				$this->params['sdate']            = $date1;
				$this->params['edate']            = $date2;
				$this->params['error']            = false;
			}else {
				$this->params['error']            = true;
				$this->params['message']          = "Data is empty";
			}

			// $this->params['contentdata']          = $imagedata;
			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/attachment/playbackhistory/v_home_attachment', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
		}

		// FOR DASHBOARD LIVE MONITORING START
		function livemonitoring($user_id){
	      // ini_set('max_execution_time', '300');
	      // set_time_limit(300);

		    $this->db     = $this->load->database("default", true);
		    $this->db->select("*");
		    $this->db->where("user_id", $user_id);
		    $data_user       = $this->db->get("user")->result();

	      $user_id       = $data_user[0]->user_id;
	      $user_parent   = $data_user[0]->user_parent;
	      $privilegecode = $data_user[0]->user_id_role;
	      $user_company  = $data_user[0]->user_company;

	      if($privilegecode == 0){
	        $user_id_fix = $user_id;
	      }elseif ($privilegecode == 1) {
	        $user_id_fix = $user_parent;
	      }elseif ($privilegecode == 2) {
	        $user_id_fix = $user_parent;
	      }elseif ($privilegecode == 3) {
	        $user_id_fix = $user_parent;
	      }elseif ($privilegecode == 4) {
	        $user_id_fix = $user_parent;
	      }elseif ($privilegecode == 5) {
	        $user_id_fix = $user_id;
	      }elseif ($privilegecode == 6) {
	        $user_id_fix = $user_id;
	      }else{
	        $user_id_fix = $user_id;
	      }

	      $companyid                       = $data_user[0]->user_company;
	      $user_dblive                     = $data_user[0]->user_dblive;
	      $mastervehicle                   = $this->m_dashboardview->mastervehicle_livemonitoring($user_id);

				// echo "<pre>";
				// var_dump($mastervehicle);die();
				// echo "<pre>";

	      $datafix                         = array();
	      $deviceidygtidakada              = array();
	      $statusvehicle['engine_on']  = 0;
	      $statusvehicle['engine_off'] = 0;

	      for ($i=0; $i < sizeof($mastervehicle); $i++) {
	        $jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
	        if (isset($jsonautocheck->auto_status)) {
	          // code...
	        $auto_status   = $jsonautocheck->auto_status;

	        if ($privilegecode == 5 || $privilegecode == 6) {
	          if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
	            if ($jsonautocheck->auto_last_engine == "ON") {
	              $statusvehicle['engine_on'] += 1;
	            }else {
	              $statusvehicle['engine_off'] += 1;
	            }
	          }
	        }else {
	          if ($jsonautocheck->auto_last_engine == "ON") {
	            $statusvehicle['engine_on'] += 1;
	          }else {
	            $statusvehicle['engine_off'] += 1;
	          }
	        }

	          if ($auto_status != "M") {
	            array_push($datafix, array(
	              "vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
	              "vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
	              "vehicle_company"      => $mastervehicle[$i]['vehicle_company'],
	              "vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
	              "vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
	              "vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
	              "vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
	              "vehicle_is_share"     => $mastervehicle[$i]['vehicle_is_share'],
	              "vehicle_id_shareto"   => $mastervehicle[$i]['vehicle_id_shareto'],
	              "auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
	              "auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
	            ));
	          }
	        }
	      }

	      $company                  = $this->m_dashboardview->getcompany_byowner($data_user);

				// echo "<pre>";
				// var_dump($company);die();
				// echo "<pre>";
	        if ($company) {

	            $datavehicleandcompany    = array();
	            $datavehicleandcompanyfix = array();

	              for ($d=0; $d < sizeof($company); $d++) {
	                $vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
	                // $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
	                $totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
	                $totalengine       = explode("|", $totaldata);
	                  array_push($datavehicleandcompany, array(
	                    "company_id"   => $company[$d]->company_id,
	                    "company_name" => $company[$d]->company_name,
	                    "totalmobil"   => $totalengine[2],
	                    "vehicle"      => $vehicledata[$d]
	                  ));
	              }
	          $this->params['company']   = $company;
	          $this->params['companyid'] = $companyid;
	          $this->params['vehicle']   = $datavehicleandcompany;
	        }else {
	          $this->params['company']   = 0;
	          $this->params['companyid'] = 0;
	          $this->params['vehicle']   = 0;
	        }

	      // echo "<pre>";
	      // var_dump($company);die();
	      // echo "<pre>";


	      $this->params['url_code_view']  = "1";
	      $this->params['code_view_menu'] = "monitor";
	      $this->params['maps_code']      = "morehundred";

	      $this->params['engine_on']      = $statusvehicle['engine_on'];
	      $this->params['engine_off']     = $statusvehicle['engine_off'];


	      $rstatus                        = $this->m_dashboardview->gettotalstatus($data_user);

	      $datastatus                     = explode("|", $rstatus);
	      $this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	      $this->params['total_vehicle']  = $datastatus[3];
	      $this->params['total_offline']  = $datastatus[2];

	      $this->params['vehicles']  	  = $mastervehicle;
	      $this->params['vehicledata']  = $datafix;
	      $this->params['vehicletotal'] = sizeof($mastervehicle);
	      $this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	      $getvehicle_byowner           = $this->m_dashboardview->getvehicle_byownerforheatmap($data_user);

	      $totalmobilnya                = sizeof($getvehicle_byowner);
	      if ($totalmobilnya == 0) {
	        $this->params['name']         = "0";
	        $this->params['host']         = "0";
	      }else {
	        $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
	        $this->params['name']         = $arr[0];
	        $this->params['host']         = $arr[1];
	      }

	      $this->params['resultactive']   = $this->m_dashboardview->vehicleactive($data_user);
	      $this->params['resultexpired']  = $this->m_dashboardview->vehicleexpired($data_user);
	      $this->params['resulttotaldev'] = $this->m_dashboardview->totaldevice($data_user);
	      // $this->params['mapsetting']  = $this->m_poipoolmaster->getmapsetting();
	      // $this->params['poolmaster']  = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
				$this->params['user_id']        = $data_user[0]->user_id;
				$this->params['data_user']      = $data_user;

	      // echo "<pre>";
	      // var_dump($this->params['resulttotaldev']);die();
	      // echo "<pre>";

	        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

	        if ($privilegecode == 1) {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
	        } elseif ($privilegecode == 2) {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
	        } elseif ($privilegecode == 3) {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
	        } elseif ($privilegecode == 4) {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
	        } elseif ($privilegecode == 5) {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	        }elseif ($privilegecode == 6) {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	        } else {
	            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
	            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	        }
		}


}
