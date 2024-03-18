<?php
include "base.php";

class Mdvr_cronjob extends Base {
	function __construct()
	{
		parent::__construct();

		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->model("m_securityevidence");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');

	}
	
	function getOneTimetokenAPI($username,$password,$userid,$domain)
	{

		$feature = array();

		$dataJson = file_get_contents($domain.""."StandardApiAction_login.action?account=".$username."&password=".$password."");

		$data = json_decode($dataJson,true);
		$result = $data["result"];
		$response = "";
		if($result == 0){

			$session_id = $data["JSESSIONID"];
			printf("===LOGIN SUCCESS: %s \r\n", $session_id);
		}else{
			$err_message = $data["message"];
			printf("===LOGIN FAILED: %s \r\n", $err_message);
		}

		$response = $session_id;
		return $response;

	}
	
	function devicestatusapi($sess_id,$imei,$vno,$vcompany,$domain)
	{

		$feature = array();
		$host = "MV03";
		$dataJson = file_get_contents($domain.""."StandardApiAction_getDeviceStatus.action?jsession=".$sess_id."&devIdno=".$imei."&toMap=1&driver=0&language=zh"); 
		$txt = $domain.""."StandardApiAction_getDeviceStatus.action?jsession=".$sess_id."&devIdno=".$imei."&toMap=1&driver=0&language=zh";
		$data = json_decode($dataJson,true);
		$result = $data['result'];
		$status = $data['status'][0];
		
		//print_r($txt);
		if($result == 0)
		{

			/*

			[0] => Array
                (
                    [id] => 020200360002 		= imei
                    [lc] => 0					= milleage (meter)
                    [dt] => 2					= hard type (1: sd card, 2: hardisk, 3 ssd card)
                    [vid] => 020200360002		= plate number
                    [pt] => 6  					= protocol type
                    [jn] =>						= driver work number
                    [dn] =>						= driver name
                    [sp] => 0					= speed (km/h) (dibagi 10)
                    [abbr] =>
                    [lid] => 0					= bus use, line number
                    [ct] =>						= Valid when lg=2,Compartment temperature (0x06)(Refer to the 808-2019 agreement)
                    [ft] => 0					= factory type
                    [dst] =>					= Front vehicle/pedestrian distance (100ms).
                    [fl] =>						= Road marker identification type
                    [ps] => 22.533209,113.945436= Geographical Position
                    [adas2] =>
                    [rft] =>
                    [dsm1] =>
                    [cet] =>
                    [pss] =>
                    [tsp] => 0					= Bus use, Site status 0- station 1- next stop
                    [rt] =>
                    [bsd1] =>
                    [es] =>
                    [yn] =>
                    [wc] =>
                    [rfd] =>
                    [ls] =>
                    [net] => 3					= Network Type 1 means 3G, 2 means WIFI
                    [adas1] =>
                    [fvs] =>
                    [dsm2] =>
                    [lg] =>
                    [lt] => 0					=Login type:0-linux, 1-windows, 2-web, 3-Android, 4-ios
                    [ios] =>
                    [aq] =>
                    [sn] =>
                    [bsd2] =>
                    [dvt] =>
                    [pk] => 4856				= Parking Time (sec)
                    [mlng] => 113.945436		= map lng
                    [ac] =>						= Audio Type
                    [s3] => 0					= Status 3
                    [t2] => 0					= Temp Sensor 2
                    [lng] => 113945436			= lng
                    [mlat] => 22.533209			= map lng
                    [yl] => 0					= Fuel Unit: L, you must first use divided by 100.
                    [gt] => 2020-09-09 14:59:09.0	= gps time (WIB)
                    [po] =>
                    [sv] =>
                    [sfg] => 0					= Bus use, Site sign 0- site 1- station yard
                    [ol] => 1					= online status (1:online, else offline
                    [snm] => 0					= site index
                    [sst] => 0					= Site status 1 site 0 station
                    [s1] => -2147469949			= status1 :
                    [hx] => 0					= Direction North direction is 0 degrees, clockwise increases, the maximum value of 360 degrees.
                    [t1] => 0					= Temp Sensor 1
                    [or] => 0					= OBD collects engine speed
                    [hv] =>
                    [os] => 0					= OBD capture engine speed
                    [ov] => 0					= OBD collecting battery voltage
                    [ojt] => 0					= OBD collecting battery voltage
                    [t4] => 0					= Temp Sensor 4
                    [lat] => 22533209			= lat
                    [s4] => 0					= status4 : 0= Positioning Type
                    [s2] => 262144				= status2 :
                    [t3] => 0					= Temp Sensor 3
                    [fdt] => 3					= Factory Subtype
                    [drid] => 0					= driver id
                    [dct] => 0					= Line direction 0 Up 1 Down
                    [gw] => G1					= Gateway Server Number
                    [ust] =>
                    [glat] =>
                    [p10] => 0					= ?
                    [glng] =>
                    [ef] => 0					= Additional information flag 0-Bus OBD 1-Video Department 2-UAE School Bus
                    [p7] => 0					= ef=2:Humidity 3 sensor
                    [p8] => 0					= ?
                    [ost] => 0					= OBD acquisition status
                    [p5] => 0			 		= ef=1:Fatigue degree, ef=2: humidity 1 sensor
                    [p6] => 0					= ef=2:Humidity 2 sensor
                    [p9] => 0					= ?
                    [ojm] => 0					= OBD capture throttle position
                    [imei] =>
                    [imsi] =>
                    [p4] => 0					= ef=1:Abnormal driving flag, ef=2: Hard disk 4 type 1sd, 2hd, 3ssd
                    [tp] =>
                    [p1] => 0					= ef=1:video loss flag, ef=2: hard disk 3 status 0 is invalid, 1 exists, 2 does not exist
                    [p3] => 0					= ef=1:Disk error flag, ef=2: Hard disk 4 status 0 is invalid, 1 exists, 2 does not exist
                    [p2] => 0					= ef=1:Video occlusion flag, ef=2: Hard disk 3 type 1sd, 2hd, 3ssd


			*/
					$driIMEI = $status['id'];
					//$drid = $status['drid'];
					//$dn = $status['dn'];
					//$jn = $status['jn'];
					$driJn = $status['driJn'];
					$driSw = $status['driSw'];
					$driSwStr = $status['driSwStr'];

					///* x */print(date("Y-m-d H:i:s")." ".$imei." drid : ".$drid."\n");
					///* x */print(date("Y-m-d H:i:s")." ".$imei." dn: ".$dn."\n");
					///* x */print(date("Y-m-d H:i:s")." ".$imei." jn : ".$jn."\n");
					/* x */print(date("Y-m-d H:i:s")." ".$imei." driJn : ".trim($driJn)."\n");
					/* x */print(date("Y-m-d H:i:s")." ".$imei." driSw : ".$driSw."\n");
					/* x */print(date("Y-m-d H:i:s")." ".$imei." driSwStr : ".$driSwStr."\n");


							$change_imei = trim($driIMEI);
							$change_driver_id = trim($driJn);

							if($driSwStr == ""){
								$change_driver_time = $driSwStr;
							}else{
								$change_driver_time = date("Y-m-d H:i:s", strtotime($driSwStr." "."+1 hours")); // device RCM = WIB
								//$change_driver_time = date("Y-m-d H:i:s", strtotime($driSwStr));
							}

							/* x */print(date("Y-m-d H:i:s")." ".$imei." change_driver_time : ".$change_driver_time."\n");

							$company_name = $this->get_company_byid($vcompany);
							$driver_name = $this->get_simper_name(strtoupper($change_driver_id)); //belum pakai SID karena data tidak lengkap

							unset($datadriver);
							$datadriver["change_imei"] = $change_imei;
							$datadriver["change_driver_id"] = $change_driver_id;
							$datadriver["change_driver_time"] = $change_driver_time;
							$datadriver["change_driver_name"] = $driver_name;
							$datadriver["change_driver_vehicle_no"] = $vno;
							$datadriver["change_driver_company"] = $vcompany;
							$datadriver["change_driver_company_name"] = $company_name;
							$datadriver["change_driver_flag"] = 0;

							//CHECK last data
							$this->dbreport = $this->load->database("webtracking_ts",TRUE);
							$this->dbreport->where("change_imei", $change_imei);
							$this->dbreport->where("change_driver_id", $change_driver_id);
							$this->dbreport->where("change_driver_time",$change_driver_time);
							$q_report = $this->dbreport->get("ts_driver_change_new");
							$rows_report = $q_report->row();
							$total_report = count($rows_report);

							//jika sudah ada update
							if($total_report > 0){
								$this->dbreport->where("change_imei", $change_imei);
								$this->dbreport->where("change_driver_id", $change_driver_id);
								$this->dbreport->where("change_driver_time",$change_driver_time);
								$this->dbreport->update("ts_driver_change_new",$datadriver);
								printf("===UPDATE DATA !! \r\n ");
							}
							//jika tidak ada insert
							else
							{
								$this->dbreport->insert("ts_driver_change_new",$datadriver);
								printf("===NEW DRIVER !! \r\n");
								printf("====== ======\r\n");
							}


			$feature["Message"] = "OK";
			$feature["StatusCode"] = "SUCCESS";
		}else{
			$err_message = $data["message"];
			$feature["Message"] = $err_message;
			$feature["StatusCode"] = "FAILED";
		}

		$response = json_encode($feature);

		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

		return $response;

	}

	function laststatus_mdvr($userid=0, $mdvruser="",$mdvrpass="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$interval = 5; //sec
		$type_list = array("MV03");
		$this->db = $this->load->database("default",true);

		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");

			$this->db->select("vehicle_id,vehicle_name,vehicle_no,vehicle_company,vehicle_mv03,vehicle_server_mdvr,company_license_code,company_license_unique");
			$this->db->order_by("vehicle_id","asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_mv03 !=", "0000");
			$this->db->where("vehicle_server_mdvr !=", ""); //harus terisi
			$this->db->where("vehicle_onprem", 0); //tidak on prem
			$this->db->join("company", "vehicle_company = company_id", "left");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows); 

			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
					//get one time token (sementara taruh di loop device sampai semua pindah)
					$session_id = $this->getOneTimetokenAPI($mdvruser,$mdvrpass,$userid,$rows[$i]->vehicle_server_mdvr);
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					
					$imei = $rows[$i]->vehicle_mv03;
					
					printf("===Process Check Last Status For %s (%d/%d) \r\n", $rows[$i]->vehicle_no, $j, $totalvehicle);
					$get_lastposition = $this->devicestatusapi($session_id,$imei,$rows[$i]->vehicle_no,$rows[$i]->vehicle_company,$rows[$i]->vehicle_server_mdvr);
					$j++;
				
			}

		$this->db->close();
		$this->db->cache_delete_all();

		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Cron start %s to %s \r\n", $nowdate, $enddate);


	}
	
	function laststatus_mdvr_onprem($userid=0, $mdvruser="",$mdvrpass="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$interval = 5; //sec
		$type_list = array("MV03");
		$this->db = $this->load->database("default",true);

		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");

			$this->db->select("vehicle_id,vehicle_name,vehicle_no,vehicle_company,vehicle_mv03,vehicle_server_mdvr,company_license_code,company_license_unique");
			$this->db->order_by("vehicle_id","asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_mv03 !=", "0000");
			$this->db->where("vehicle_server_mdvr !=", ""); //harus terisi
			$this->db->where("vehicle_onprem", 1); //tidak on prem
			$this->db->join("company", "vehicle_company = company_id", "left");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows); 

			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
					//get one time token (sementara taruh di loop device sampai semua pindah)
					$session_id = $this->getOneTimetokenAPI($mdvruser,$mdvrpass,$userid,$rows[$i]->vehicle_server_mdvr);
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					
					$imei = $rows[$i]->vehicle_mv03;
					
					printf("===Process Check Last Status For %s (%d/%d) \r\n", $rows[$i]->vehicle_no, $j, $totalvehicle);
					$get_lastposition = $this->devicestatusapi($session_id,$imei,$rows[$i]->vehicle_no,$rows[$i]->vehicle_company,$rows[$i]->vehicle_server_mdvr);
					$j++;
				
			}

		$this->db->close();
		$this->db->cache_delete_all();

		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Cron start %s to %s \r\n", $nowdate, $enddate);


	}
	
	function get_company_byid($companyid)
	{
		$this->db->order_by("company_name", "asc");
		$this->db->select("company_id,company_name");
		$this->db->where("company_id", $companyid);
		$this->db->where("company_flag", 0);
		$q = $this->db->get("company");
        $rowscompany = $q->row();

		if(count($rowscompany)>0){
			$company_name = $rowscompany->company_name;
			return $company_name;
		}else{

			return false;
		}

	}

	function get_simper_name($simperid)
	{

		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("master_portal_updateddate_new", "desc");
		$this->dbts->select("portal_nik,portal_name");
		$this->dbts->where("portal_nik", $simperid);
		$q = $this->dbts->get("ts_master_portal_simper");
        $rowssimper = $q->row();

		$this->dbts->close();
		$this->dbts->cache_delete_all();

		$simper_name = "";
		if(count($rowssimper)>0){
			$simper_name = $rowssimper->portal_name;
			return $simper_name;
		}else{

			return $simper_name;
		}


	}
	
	function alarmevidence_photo($userid=0, $mdvruser="",$mdvrpass="", $orderby="", $type="", $startdate="", $enddate="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		//variable
		$nowdate = date('Y-m-d H:i:s');
		$interval = 30; //sec
		$type_list = array("MV03");
		$report_photo = "alarm_photo";
		$report_video = "alarm_video";
		$report = "alarm_evidence_";
		$nowtime = date("Y-m-d H:i:s");
		$speed_check = 0;

		//get (last alarm)
		$this->db = $this->load->database("webtracking_ts", TRUE);
		$this->db->order_by("config_lastcheck","asc");
		$this->db->where("config_name","ALARM_EVIDENCE");
		$this->db->where("config_status",1);
		$this->db->where("config_user",$userid);
		$qcfg = $this->db->get("ts_config");
		$rowcfg = $qcfg->row();
		$total_cfg = count($qcfg);
		if ($total_cfg == 0)
		{
			printf("==No Data Configuration \r\n");
			return;
		}else{
			$lastcheck = $rowcfg->config_lastcheck;
			if($type == "report")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
					$datefilename = date("Ymd", strtotime("yesterday"));
					$month = date("F", strtotime("yesterday"));
					$year = date("Y", strtotime("yesterday"));
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else if($type == "today")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00");
					$datefilename = date("Ymd");
					$month = date("F");
					$year = date("Y");
					$speed_check = 1;
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59");
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else
			{
				$sdate = date("Y-m-d", strtotime($lastcheck));
				$shour = date("H:i:s", strtotime($lastcheck));


				$edate = date("Y-m-d", strtotime($nowtime));
				$ehour = date("H:i:s", strtotime($nowtime));
			}

		}

		//firts param , jika masuk kondisi akan berubah
		$alarm_time = date("Y-m-d H:i:s", strtotime($sdate." ".$shour));

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		$this->db = $this->load->database("default",true);

		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");
		printf("===CONFIG DATETIME = %s \r\n", $sdate." ".$shour." s/d ".$edate." ".$ehour);

		switch ($month)
		{
			case "January":
            $dbtable_photo = $report_photo."januari_".$year;
			$dbtable_video = $report_video."januari_".$year;
			$dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable_photo = $report_photo."februari_".$year;
			$dbtable_video = $report_video."februari_".$year;
			$dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable_photo = $report_photo."maret_".$year;
			$dbtable_video = $report_video."maret_".$year;
			$dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable_photo = $report_photo."april_".$year;
			$dbtable_video = $report_video."april_".$year;
			$dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable_photo = $report_photo."mei_".$year;
			$dbtable_video = $report_video."mei_".$year;
			$dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable_photo = $report_photo."juni_".$year;
			$dbtable_video = $report_video."juni_".$year;
			$dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable_photo = $report_photo."juli_".$year;
			$dbtable_video = $report_video."juli_".$year;
			$dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable_photo = $report_photo."agustus_".$year;
			$dbtable_video = $report_video."agustus_".$year;
			$dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable_photo = $report_photo."september_".$year;
			$dbtable_video = $report_video."september_".$year;
			$dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable_photo = $report_photo."oktober_".$year;
			$dbtable_video = $report_video."oktober_".$year;
			$dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable_photo = $report_photo."november_".$year;
			$dbtable_video = $report_video."november_".$year;
			$dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable_photo = $report_photo."desember_".$year;
			$dbtable_video = $report_video."desember_".$year;
			$dbtable = $report."desember_".$year;
			break;
		}

			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id",$orderby);
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_mv03 != ","0000");
			$this->db->where("vehicle_mv03 != ","");
			//$this->db->where("vehicle_server_mdvr !=", ""); //harus terisi
			$this->db->like("vehicle_server_mdvr", "armordvr"); //khusus yg sudah pindah ke colo 2
			$this->db->where("vehicle_onprem", 0); //tidak on prem
			//$this->db->where("vehicle_no", "FDT-740");
			$this->db->join("company", "vehicle_company = company_id", "left");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);

			

			//insert db photo
			$j = 1;
			$datadiproses = 0;
			for ($i=0;$i<count($rows);$i++)
			{
					//get one time token (sementara taruh di loop device sampai semua pindah)
					$session_id = $this->getOneTimetokenAPI($mdvruser,$mdvrpass,$userid,$rows[$i]->vehicle_server_mdvr);
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					
					$mediatype = 0; //0 photo , 1: video
					$ex_device = explode("@",$rows[$i]->vehicle_device);
					$imei = $rows[$i]->vehicle_mv03;
					$vehicleid = $imei;
					$vehicleno = $rows[$i]->vehicle_no;
					$telegram_autocheck = $rows[$i]->company_telegram_autocheck;
					
					//Jika sudah pindah ke MDVR-02 (new format nolam)
					if($rows[$i]->vehicle_format_mdvr == 1)
					{
						$imei = str_replace(" ","-",$vehicleno);
					}
					
					if($type == "today")
					{

						$lastrunning = $this->getLastRunning($imei,$mediatype);
						if(count($lastrunning)>0)
						{
							$sdate = date("Y-m-d", strtotime($lastrunning->config_lastrunning));
							$shour = date("H:i:s", strtotime($lastrunning->config_lastrunning));
						}
						
					}


					printf("===Get media PHOTO Vehicle %s %s - %s %s (%d/%d) \r\n", $rows[$i]->vehicle_no, $rows[$i]->vehicle_mv03, $rows[$i]->company_name, $rows[$i]->vehicle_server_mdvr, $j, $totalvehicle);
					printf("===Periode %s %s s/d %s %s \r\n", $sdate,$shour,$edate,$ehour); 
					$get_lastalarm = $this->securityevidenceapi($session_id,$sdate,$shour,$edate,$ehour,$mediatype,$imei,$userid,$rows[$i]->vehicle_server_mdvr);
					//print_r($get_lastalarm);exit();
					$result_lastalarm = json_decode($get_lastalarm,true);
					$response         = $result_lastalarm["StatusCode"];

					if($response == "SUCCESS")
					{

						$infos = $result_lastalarm["Data"]["infos"];
						$total_infos = count($infos);

						for ($z=0;$z<$total_infos;$z++)
						{

							$infos            = $result_lastalarm["Data"]["infos"];
							if($infos[$z]['mediaType'] == 0){
								$ex_time = $infos[$z]['fileTime']; //datetime foto
							}else{
								$ex_time = $infos[$z]['fileSTime']; //datetime video
							}

							$alarm_time = date("Y-m-d H:i:s", (($ex_time/1000)));
							
							$dataalarm       = explode("|",$this->getalarmname($infos[$z]['alarmType']));
							$dataalarm_name  = $dataalarm[0];
							$dataalarm_level = $dataalarm[1];
							$dataalarm_group = $dataalarm[2];
								
							$nophoto = $z+1;

							//CHECK last data
							//printf("===PARAM ALERT %s, %s, %s, %s, %s, %s \r\n", $ex_device[0], $infos[$z]['devIdno'], $infos[$z]['alarmType'], $mediatype, $alarm_time, $dbtable);
							$check_row_report = $this->CheckExistingReport($ex_device[0],$infos[$z]['devIdno'],$infos[$z]['alarmType'],$mediatype,$alarm_time,$dbtable);

							//jika tidak ada proses lanjut
							if($check_row_report == 0){
								$return = 0;
								$datadiproses += 1;
								printf("===PROSES %s Alarm Time: %s - %s of %s \r\n", $dataalarm_name, $alarm_time, $nophoto, $total_infos);
							}
							//jika sudah ada skip
							else
							{
								$return = 1;
								printf("===SUDAH DIPROSES %s Alarm Time: %s - %s of %s \r\n", $dataalarm_name, $alarm_time, $nophoto, $total_infos);
							}

							if($return == 0)
							{
								//insert into report

								$geofence_type = "";
								if($infos[$z]['position'] != ""){
									$ex_coord = explode(",",$infos[$z]['position']);
									//$lat_coord = "-".$ex_coord[0];
									$lat_coord = $ex_coord[0];
									//$lat_coord = str_replace("--", "-", $lat_coord);
									$lng_coord = $ex_coord[1];

									$coord = $lat_coord.",".$lng_coord;
									$position = $this->getPosition_other($lng_coord, $lat_coord);
									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
									}

								}else{
									$lat_coord = "";
									$lng_coord = "";
									$geofence_start = "";
									$coord = "";
									$position_name = "";
								}

								if($infos[$z]['mediaType'] == 0){
									$ex_time = $infos[$z]['fileTime'];
									$ex_time2 = $ex_time;
									$duration_sec = $ex_time2 - $ex_time;
								}else{
									$ex_time = $infos[$z]['fileSTime'];
									$ex_time2 = $infos[$z]['fileETime'];
									$duration_sec = $ex_time2 - $ex_time;
								}

									unset($datainsert);
									$datainsert["alarm_report_imei"]             = $infos[$z]['devIdno'];
									$datainsert["alarm_report_vehicle_id"]       = $ex_device[0];
									$datainsert["alarm_report_vehicle_user_id"]  = $rows[$i]->vehicle_user_id;
									$datainsert["alarm_report_vehicle_no"]       = $rows[$i]->vehicle_no;
									$datainsert["alarm_report_vehicle_name"]     = $rows[$i]->vehicle_name;
									$datainsert["alarm_report_vehicle_type"]     = $rows[$i]->vehicle_type;
									$datainsert["alarm_report_vehicle_company"]  = $rows[$i]->vehicle_company;
									$datainsert["alarm_report_type"]             = $infos[$z]['alarmType'];
									$datainsert["alarm_report_name"]             = $dataalarm_name;
									$datainsert["alarm_report_level"]            = $dataalarm_level;
									$datainsert["alarm_report_group"]            = $dataalarm_group;
									$datainsert["alarm_report_media"]            = $infos[$z]['mediaType'];
									$datainsert["alarm_report_channel"]          = $infos[$z]['channel'];
									$datainsert["alarm_report_gpsstatus"]        = $infos[$z]['gpsstatus'];
									/* $datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour"));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour")); */
									$datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time));

									$datainsert["alarm_report_update_time"]    	 = date("Y-m-d H:i:s", (($infos[$z]['updateTime']/1000)));
									$datainsert["alarm_report_duration_sec"]     = $duration_sec;
									$datainsert["alarm_report_location_start"]   = $position_name;
									$datainsert["alarm_report_location_end"]     = $position_name;
									$datainsert["alarm_report_coordinate_start"] = $coord;
									$datainsert["alarm_report_coordinate_end"]   = $coord;
									$datainsert["alarm_report_size"]             = $infos[$z]['fileSize'];
									$datainsert["alarm_report_downloadurl"]      = $infos[$z]['downloadUrl'];
									$datainsert["alarm_report_path"]             = $infos[$z]['filePath'];
									$datainsert["alarm_report_fileurl"]          = $infos[$z]['fileUrl'];
									$datainsert["alarm_report_insert_time"]      = date("Y-m-d H:i:s");
									$datainsert["alarm_report_insert_type"]      = $type;

								//CHECK last data
								// idno, alarmtype,media,starttime
								//jika tidak ada insert
								if($check_row_report == 0){
									$this->dbreport->insert($dbtable,$datainsert);
									printf("===OK \r\n");
									printf("====== ======\r\n");
								}
								//jika sudah ada skip
								else
								{

									printf("===SKIP \r\n");
									printf("====== ======\r\n");


								}

							}//end return
						}//end master looping PHOTO

						//update last running berdasarkan last alarm time
						$update_lastrunning = $this->updateLastRunning($imei,$vehicleno,$userid,$mediatype,$alarm_time);
					}
					else
					{

						printf("===ERROR : %s \r\n", $result_lastalarm["Message"]);
					}
					$j++;
			}

			printf("\r\n");

			//jika bukan cron report
			if($type == "today")
			{
				//update config last check
				unset($datalastcheck);

				$datalastcheck["config_lastcheck"] = $nowtime;
				$this->db = $this->load->database("webtracking_ts",TRUE);
				$this->db->where("config_name", "ALARM_EVIDENCE");
				$this->db->where("config_user", $userid);
				$this->db->where("config_status",1);
				$this->db->update("ts_config",$datalastcheck);

				printf("UPDATE CONFIG TIME OKE \r\n ");

				$this->db->close();
				$this->db->cache_delete_all();

				$enddate = date('Y-m-d H:i:s');

			}

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$servername   = "ABDICOLO-02";
			$message =  urlencode(
						"ALARM EVIDENCE - GET PHOTO"." \n".
						"Total Device: ".$totalvehicle." \n".
						"Total Data: ".$datadiproses." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Server: ".$servername." \n".
						"Latency: ".$duration_sec." s"." \n"
						);


			$sendtelegram = $this->telegram_direct($telegram_autocheck,$message); //company telegram autocheck
			printf("===SENT TELEGRAM OK\r\n");
			printf("===FINISH Cron start %s to %s \r\n", $cronstartdate, date("Y-m-d H:i:s"));
	}

	function alarmevidence_video($userid=0, $mdvruser="",$mdvrpass="", $orderby="", $type="", $startdate="", $enddate="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		//variable
		$nowdate = date('Y-m-d H:i:s');
		$interval = 30; //sec
		$type_list = array("MV03");
		$report_photo = "alarm_photo";
		$report_video = "alarm_video";
		$report = "alarm_evidence_";
		$nowtime = date("Y-m-d H:i:s");
		$speed_check = 0;

		//get (last alarm)
		$this->db = $this->load->database("webtracking_ts", TRUE);
		$this->db->order_by("config_lastcheck","asc");
		$this->db->where("config_name","ALARM_EVIDENCE");
		$this->db->where("config_status",1);
		$this->db->where("config_user",$userid);
		$qcfg = $this->db->get("ts_config");
		$rowcfg = $qcfg->row();
		$total_cfg = count($qcfg);
		if ($total_cfg == 0)
		{
			printf("==No Data Configuration \r\n");
			return;
		}else{
			$lastcheck = $rowcfg->config_lastcheck;
			if($type == "report")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
					$datefilename = date("Ymd", strtotime("yesterday"));
					$month = date("F", strtotime("yesterday"));
					$year = date("Y", strtotime("yesterday"));
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else if($type == "today")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00");
					$datefilename = date("Ymd");
					$month = date("F");
					$year = date("Y");
					$speed_check = 1;
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59");
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else
			{
				$sdate = date("Y-m-d", strtotime($lastcheck));
				$shour = date("H:i:s", strtotime($lastcheck));


				$edate = date("Y-m-d", strtotime($nowtime));
				$ehour = date("H:i:s", strtotime($nowtime));
			}

		}

		//firts param , jika masuk kondisi akan berubah
		$alarm_time = date("Y-m-d H:i:s", strtotime($sdate." ".$shour));

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		$this->db = $this->load->database("default",true);

		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");
		printf("===CONFIG DATETIME = %s \r\n", $sdate." ".$shour." s/d ".$edate." ".$ehour);

		switch ($month)
		{
			case "January":
            $dbtable_photo = $report_photo."januari_".$year;
			$dbtable_video = $report_video."januari_".$year;
			$dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable_photo = $report_photo."februari_".$year;
			$dbtable_video = $report_video."februari_".$year;
			$dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable_photo = $report_photo."maret_".$year;
			$dbtable_video = $report_video."maret_".$year;
			$dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable_photo = $report_photo."april_".$year;
			$dbtable_video = $report_video."april_".$year;
			$dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable_photo = $report_photo."mei_".$year;
			$dbtable_video = $report_video."mei_".$year;
			$dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable_photo = $report_photo."juni_".$year;
			$dbtable_video = $report_video."juni_".$year;
			$dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable_photo = $report_photo."juli_".$year;
			$dbtable_video = $report_video."juli_".$year;
			$dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable_photo = $report_photo."agustus_".$year;
			$dbtable_video = $report_video."agustus_".$year;
			$dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable_photo = $report_photo."september_".$year;
			$dbtable_video = $report_video."september_".$year;
			$dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable_photo = $report_photo."oktober_".$year;
			$dbtable_video = $report_video."oktober_".$year;
			$dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable_photo = $report_photo."november_".$year;
			$dbtable_video = $report_video."november_".$year;
			$dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable_photo = $report_photo."desember_".$year;
			$dbtable_video = $report_video."desember_".$year;
			$dbtable = $report."desember_".$year;
			break;
		}

			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id",$orderby);
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_mv03 != ","0000");
			$this->db->where("vehicle_mv03 != ","");
			//$this->db->where("vehicle_server_mdvr !=", ""); //harus terisi
			$this->db->like("vehicle_server_mdvr", "armordvr"); //khusus yg sudah pindah ke colo 2
			$this->db->where("vehicle_onprem", 0); //tidak on prem
			//$this->db->where("vehicle_device", "865235053622222@VT200L");
			$this->db->join("company", "vehicle_company = company_id", "left");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);

			//insert db video
			$k = 1;
			$datadiproses = 0;
			for ($m=0;$m<count($rows);$m++)
			{
					//get one time token (sementara taruh di loop device sampai semua pindah)
					$session_id = $this->getOneTimetokenAPI($mdvruser,$mdvrpass,$userid,$rows[$m]->vehicle_server_mdvr);
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					
					$mediatype = 1; //0 photo , 1: video
					$ex_device = explode("@",$rows[$m]->vehicle_device);
					$imei = $rows[$m]->vehicle_mv03;
					$vehicleid = $imei;
					$vehicleno = $rows[$m]->vehicle_no;
					$telegram_autocheck = $rows[$m]->company_telegram_autocheck;
					
					//Jika sudah pindah ke MDVR-02 (new format nolam)
					if($rows[$m]->vehicle_format_mdvr == 1)
					{
						$imei = str_replace(" ","-",$vehicleno);
						
					}

					if($type == "today")
					{

						$lastrunning = $this->getLastRunning($imei,$mediatype);
						if(count($lastrunning)>0)
						{
							$sdate = date("Y-m-d", strtotime($lastrunning->config_lastrunning));
							$shour = date("H:i:s", strtotime($lastrunning->config_lastrunning));
						}
					}


					printf("\r\n");
					printf("===Get Media VIDEO Vehicle %s %s - %s %s (%d/%d) \r\n", $rows[$m]->vehicle_no, $rows[$m]->vehicle_device, $rows[$m]->company_name, $rows[$m]->vehicle_server_mdvr, $k, $totalvehicle);
					
					printf("===Periode %s %s s/d %s %s \r\n", $sdate,$shour,$edate,$ehour);
					$get_lastalarm = $this->securityevidenceapi($session_id,$sdate,$shour,$edate,$ehour,$mediatype,$imei,$userid,$rows[$m]->vehicle_server_mdvr);

					$result_lastalarm  = json_decode($get_lastalarm,true);
					//print_r($result_lastalarm);
					$response = $result_lastalarm["StatusCode"];

					if($response == "SUCCESS")
					{

						$infos = $result_lastalarm["Data"]["infos"];
						$total_infos = count($infos);

						for ($y=0;$y<$total_infos;$y++){

								if($infos[$y]['mediaType'] == 0){
									$ex_time = $infos[$y]['fileTime'];
								}else{
									$ex_time = $infos[$y]['fileSTime'];
								}

								$alarm_time = date("Y-m-d H:i:s", (($ex_time/1000)));
								
								$dataalarm_vid = explode("|",$this->getalarmname($infos[$y]['alarmType']));
								$dataalarm_name_vid = $dataalarm_vid[0];
								$dataalarm_level_vid = $dataalarm_vid[1];
								$dataalarm_group_vid = $dataalarm_vid[2];
								
								$novideo = $y+1;

								//CHECK last data
								// idno, alarmtype,media,starttime
								//printf("===PARAM ALERT %s, %s, %s, %s, %s, %s \r\n", $ex_device[0], $infos[$y]['devIdno'], $infos[$y]['alarmType'], $mediatype, $alarm_time, $dbtable);
								$check_row_report = $this->CheckExistingReport($ex_device[0],$infos[$y]['devIdno'],$infos[$y]['alarmType'],$mediatype,$alarm_time,$dbtable);

								if($check_row_report == 0){
									$return = 0;
									$datadiproses += 1;
									printf("===PROSES %s Alarm Time: %s - %s of %s \r\n", $dataalarm_name_vid, $alarm_time, $novideo, $total_infos);
								}
								//jika sudah ada skip
								else
								{
									$return = 1;
									printf("===SUDAH DIPROSES %s Alarm Time: %s - %s of %s \r\n", $dataalarm_name_vid, $alarm_time, $novideo, $total_infos);
								}

							if($return == 0)
							{


								//insert into report
								if($infos[$y]['position'] != ""){
									$ex_coord = explode(",",$infos[$y]['position']);
									//$lat_coord = "-".$ex_coord[0];
									$lat_coord = $ex_coord[0];
									//$lat_coord = str_replace("--", "-", $lat_coord);
									$lng_coord = $ex_coord[1];
									$coord = $lat_coord.",".$lng_coord;

									$position = $this->getPosition_other($lng_coord, $lat_coord);
									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
									}

								}else{
									$lat_coord = "";
									$lng_coord = "";
									$geofence_start = "";
									$coord = "";
									$position_name = "";
								}

								if($infos[$y]['mediaType'] == 0){
									$ex_time = $infos[$y]['fileTime'];
									$ex_time2 = $ex_time;
									$duration_sec = $ex_time2 - $ex_time;
								}else{
									$ex_time = $infos[$y]['fileSTime'];
									$ex_time2 = $infos[$y]['fileETime'];
									$duration_sec = $ex_time2 - $ex_time;
								}


									unset($datainsert);
									$datainsert["alarm_report_imei"] = $infos[$y]['devIdno'];
									$datainsert["alarm_report_vehicle_id"]       = $ex_device[0];
									$datainsert["alarm_report_vehicle_user_id"]  = $rows[$m]->vehicle_user_id;
									$datainsert["alarm_report_vehicle_no"]       = $rows[$m]->vehicle_no;
									$datainsert["alarm_report_vehicle_name"]     = $rows[$m]->vehicle_name;
									$datainsert["alarm_report_vehicle_type"]     = $rows[$m]->vehicle_type;
									$datainsert["alarm_report_vehicle_company"]  = $rows[$m]->vehicle_company;
									$datainsert["alarm_report_type"] = $infos[$y]['alarmType'];
									$datainsert["alarm_report_name"] = $dataalarm_name_vid;
									$datainsert["alarm_report_level"] = $dataalarm_level_vid;
									$datainsert["alarm_report_group"] = $dataalarm_group_vid;
									$datainsert["alarm_report_media"] = $infos[$y]['mediaType'];
									$datainsert["alarm_report_channel"] = $infos[$y]['channel'];
									$datainsert["alarm_report_gpsstatus"] = $infos[$y]['gpsstatus'];
									/* $datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour"));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour")); */
									$datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time));
									$datainsert["alarm_report_update_time"] = date("Y-m-d H:i:s", (($infos[$y]['updateTime']/1000)));
									$datainsert["alarm_report_duration_sec"] = $duration_sec/1000;
									$datainsert["alarm_report_location_start"] = $position_name;
									$datainsert["alarm_report_location_end"] = $position_name;
									$datainsert["alarm_report_coordinate_start"] = $coord;
									$datainsert["alarm_report_coordinate_end"] = $coord;
									$datainsert["alarm_report_size"] = $infos[$y]['fileSize'];
									$datainsert["alarm_report_downloadurl"] = $infos[$y]['downloadUrl'];
									$datainsert["alarm_report_path"] = $infos[$y]['filePath'];
									$datainsert["alarm_report_fileurl"] = $infos[$y]['fileUrl'];
									$datainsert["alarm_report_insert_time"]      = date("Y-m-d H:i:s");
									$datainsert["alarm_report_insert_type"]      = $type;


								//CHECK last data
								// idno, alarmtype,media,starttime
								//jika tidak ada insert
								if($check_row_report == 0){

									$this->dbreport = $this->load->database("tensor_report",TRUE);
									$this->dbreport->insert($dbtable,$datainsert);
									printf("===OK \r\n");
									printf("====== ======\r\n");
								}
								//jika sudah ada skip
								else
								{

									printf("===SKIP \r\n");
									printf("====== ======\r\n");
								}

							}

						} //end master looping VIDEO

						//update last running berdasarkan last alarm time
						$update_lastrunning = $this->updateLastRunning($imei,$vehicleno,$userid,$mediatype,$alarm_time);


					}else{

						printf("===ERROR : %s \r\n", $result_lastalarm["Message"]);
					}
					$k++;
			}

			printf("\r\n");

			//jika bukan cron report
			if($type == "today")
			{
				//update config last check
				unset($datalastcheck);

				$datalastcheck["config_lastcheck"] = $nowtime;
				$this->db = $this->load->database("webtracking_ts",TRUE);
				$this->db->where("config_name", "ALARM_EVIDENCE");
				$this->db->where("config_user", $userid);
				$this->db->where("config_status",1);
				$this->db->update("ts_config",$datalastcheck);

				printf("UPDATE CONFIG TIME OKE \r\n ");

				$this->db->close();
				$this->db->cache_delete_all();

				$enddate = date('Y-m-d H:i:s');

			}

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$servername   = "ABDICOLO-02";
			$message =  urlencode(
						"ALARM EVIDENCE - GET VIDEO \n".
						"Total Device: ".$totalvehicle." \n".
						"Total Data: ".$datadiproses." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Server: ".$servername." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			$sendtelegram = $this->telegram_direct($telegram_autocheck,$message); //company telegram autocheck
			printf("===SENT TELEGRAM OK\r\n");
			printf("===FINISH Cron start %s to %s \r\n", $cronstartdate, date("Y-m-d H:i:s"));
	}
	
	function alarmevidence_photo_onprem($userid=0, $mdvruser="",$mdvrpass="", $orderby="", $type="", $startdate="", $enddate="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		//variable
		$nowdate = date('Y-m-d H:i:s');
		$interval = 30; //sec
		$type_list = array("MV03");
		$report_photo = "alarm_photo";
		$report_video = "alarm_video";
		$report = "alarm_evidence_";
		$nowtime = date("Y-m-d H:i:s");
		$speed_check = 0;

		//get (last alarm)
		$this->db = $this->load->database("webtracking_ts", TRUE);
		$this->db->order_by("config_lastcheck","asc");
		$this->db->where("config_name","ALARM_EVIDENCE");
		$this->db->where("config_status",1);
		$this->db->where("config_user",$userid);
		$qcfg = $this->db->get("ts_config");
		$rowcfg = $qcfg->row();
		$total_cfg = count($qcfg);
		if ($total_cfg == 0)
		{
			printf("==No Data Configuration \r\n");
			return;
		}else{
			$lastcheck = $rowcfg->config_lastcheck;
			if($type == "report")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
					$datefilename = date("Ymd", strtotime("yesterday"));
					$month = date("F", strtotime("yesterday"));
					$year = date("Y", strtotime("yesterday"));
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else if($type == "today")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00");
					$datefilename = date("Ymd");
					$month = date("F");
					$year = date("Y");
					$speed_check = 1;
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59");
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else
			{
				$sdate = date("Y-m-d", strtotime($lastcheck));
				$shour = date("H:i:s", strtotime($lastcheck));


				$edate = date("Y-m-d", strtotime($nowtime));
				$ehour = date("H:i:s", strtotime($nowtime));
			}

		}

		//firts param , jika masuk kondisi akan berubah
		$alarm_time = date("Y-m-d H:i:s", strtotime($sdate." ".$shour));

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		$this->db = $this->load->database("default",true);

		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");
		printf("===CONFIG DATETIME = %s \r\n", $sdate." ".$shour." s/d ".$edate." ".$ehour);

		switch ($month)
		{
			case "January":
            $dbtable_photo = $report_photo."januari_".$year;
			$dbtable_video = $report_video."januari_".$year;
			$dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable_photo = $report_photo."februari_".$year;
			$dbtable_video = $report_video."februari_".$year;
			$dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable_photo = $report_photo."maret_".$year;
			$dbtable_video = $report_video."maret_".$year;
			$dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable_photo = $report_photo."april_".$year;
			$dbtable_video = $report_video."april_".$year;
			$dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable_photo = $report_photo."mei_".$year;
			$dbtable_video = $report_video."mei_".$year;
			$dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable_photo = $report_photo."juni_".$year;
			$dbtable_video = $report_video."juni_".$year;
			$dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable_photo = $report_photo."juli_".$year;
			$dbtable_video = $report_video."juli_".$year;
			$dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable_photo = $report_photo."agustus_".$year;
			$dbtable_video = $report_video."agustus_".$year;
			$dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable_photo = $report_photo."september_".$year;
			$dbtable_video = $report_video."september_".$year;
			$dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable_photo = $report_photo."oktober_".$year;
			$dbtable_video = $report_video."oktober_".$year;
			$dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable_photo = $report_photo."november_".$year;
			$dbtable_video = $report_video."november_".$year;
			$dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable_photo = $report_photo."desember_".$year;
			$dbtable_video = $report_video."desember_".$year;
			$dbtable = $report."desember_".$year;
			break;
		}

			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id",$orderby);
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_mv03 != ","0000");
			$this->db->where("vehicle_mv03 != ","");
			$this->db->where("vehicle_server_mdvr !=", ""); //harus terisi
			$this->db->where("vehicle_onprem", 1); //on prem
			//$this->db->where("vehicle_device", "865235053622222@VT200L");

			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);

			

			//insert db photo
			$j = 1;
			$datadiproses = 0;
			for ($i=0;$i<count($rows);$i++)
			{
					//get one time token (sementara taruh di loop device sampai semua pindah)
					$session_id = $this->getOneTimetokenAPI($mdvruser,$mdvrpass,$userid,$rows[$i]->vehicle_server_mdvr);
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					
					$mediatype = 0; //0 photo , 1: video
					$ex_device = explode("@",$rows[$i]->vehicle_device);
					$imei = $rows[$i]->vehicle_mv03;
					$vehicleid = $imei;
					$vehicleno = $rows[$i]->vehicle_no;
					
					//testing unique number:
					$imei_new = str_replace(" ","-",$vehicleno);

					if($type == "today")
					{

						$lastrunning = $this->getLastRunning($imei,$mediatype);
						if(count($lastrunning)>0)
						{
							$sdate = date("Y-m-d", strtotime($lastrunning->config_lastrunning));
							$shour = date("H:i:s", strtotime($lastrunning->config_lastrunning));
						}
						
					}
					
					


					printf("===Get media PHOTO Vehicle %s %s - (%d/%d) \r\n", $rows[$i]->vehicle_no, $rows[$i]->vehicle_mv03, $j, $totalvehicle);
					printf("===Periode %s %s s/d %s %s \r\n", $sdate,$shour,$edate,$ehour); 
					$get_lastalarm = $this->securityevidenceapi($session_id,$sdate,$shour,$edate,$ehour,$mediatype,$imei_new,$userid,$rows[$i]->vehicle_server_mdvr);
					//print_r($get_lastalarm);exit();
					$result_lastalarm = json_decode($get_lastalarm,true);
					$response         = $result_lastalarm["StatusCode"];

					if($response == "SUCCESS")
					{

						$infos = $result_lastalarm["Data"]["infos"];
						$total_infos = count($infos);

						for ($z=0;$z<$total_infos;$z++)
						{

							$infos            = $result_lastalarm["Data"]["infos"];
							if($infos[$z]['mediaType'] == 0){
								$ex_time = $infos[$z]['fileTime']; //datetime foto
							}else{
								$ex_time = $infos[$z]['fileSTime']; //datetime video
							}

							$alarm_time = date("Y-m-d H:i:s", (($ex_time/1000)));
							$nophoto = $z+1;

							//CHECK last data
							//printf("===PARAM ALERT %s, %s, %s, %s, %s, %s \r\n", $ex_device[0], $infos[$z]['devIdno'], $infos[$z]['alarmType'], $mediatype, $alarm_time, $dbtable);
							$check_row_report = $this->CheckExistingReport($ex_device[0],$infos[$z]['devIdno'],$infos[$z]['alarmType'],$mediatype,$alarm_time,$dbtable);

							//jika tidak ada proses lanjut
							if($check_row_report == 0){
								$return = 0;
								$datadiproses += 1;
								printf("===PROSES %s of %s & Alarm Time: %s \r\n", $nophoto, $total_infos, $alarm_time);
							}
							//jika sudah ada skip
							else
							{
								$return = 1;
								printf("===SUDAH DIPROSES %s of %s & Alarm Time: %s \r\n", $nophoto, $total_infos, $alarm_time);
							}

							if($return == 0)
							{
								//insert into report

								$geofence_type = "";
								if($infos[$z]['position'] != ""){
									$ex_coord = explode(",",$infos[$z]['position']);
									//$lat_coord = "-".$ex_coord[0];
									$lat_coord = $ex_coord[0];
									//$lat_coord = str_replace("--", "-", $lat_coord);
									$lng_coord = $ex_coord[1];

									$coord = $lat_coord.",".$lng_coord;
									$position = $this->getPosition_other($lng_coord, $lat_coord);
									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
									}

								}else{
									$lat_coord = "";
									$lng_coord = "";
									$geofence_start = "";
									$coord = "";
									$position_name = "";
								}

								if($infos[$z]['mediaType'] == 0){
									$ex_time = $infos[$z]['fileTime'];
									$ex_time2 = $ex_time;
									$duration_sec = $ex_time2 - $ex_time;
								}else{
									$ex_time = $infos[$z]['fileSTime'];
									$ex_time2 = $infos[$z]['fileETime'];
									$duration_sec = $ex_time2 - $ex_time;
								}

								$dataalarm       = explode("|",$this->getalarmname($infos[$z]['alarmType']));

								$dataalarm_name  = $dataalarm[0];
								$dataalarm_level = $dataalarm[1];
								$dataalarm_group = $dataalarm[2];


									unset($datainsert);
									$datainsert["alarm_report_imei"]             = $infos[$z]['devIdno'];
									$datainsert["alarm_report_vehicle_id"]       = $ex_device[0];
									$datainsert["alarm_report_vehicle_user_id"]  = $rows[$i]->vehicle_user_id;
									$datainsert["alarm_report_vehicle_no"]       = $rows[$i]->vehicle_no;
									$datainsert["alarm_report_vehicle_name"]     = $rows[$i]->vehicle_name;
									$datainsert["alarm_report_vehicle_type"]     = $rows[$i]->vehicle_type;
									$datainsert["alarm_report_vehicle_company"]  = $rows[$i]->vehicle_company;
									$datainsert["alarm_report_type"]             = $infos[$z]['alarmType'];
									$datainsert["alarm_report_name"]             = $dataalarm_name;
									$datainsert["alarm_report_level"]            = $dataalarm_level;
									$datainsert["alarm_report_group"]            = $dataalarm_group;
									$datainsert["alarm_report_media"]            = $infos[$z]['mediaType'];
									$datainsert["alarm_report_channel"]          = $infos[$z]['channel'];
									$datainsert["alarm_report_gpsstatus"]        = $infos[$z]['gpsstatus'];
									/* $datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour"));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour")); */
									$datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time));

									$datainsert["alarm_report_update_time"]    	 = date("Y-m-d H:i:s", (($infos[$z]['updateTime']/1000)));
									$datainsert["alarm_report_duration_sec"]     = $duration_sec;
									$datainsert["alarm_report_location_start"]   = $position_name;
									$datainsert["alarm_report_location_end"]     = $position_name;
									$datainsert["alarm_report_coordinate_start"] = $coord;
									$datainsert["alarm_report_coordinate_end"]   = $coord;
									$datainsert["alarm_report_size"]             = $infos[$z]['fileSize'];
									$datainsert["alarm_report_downloadurl"]      = $infos[$z]['downloadUrl'];
									$datainsert["alarm_report_path"]             = $infos[$z]['filePath'];
									$datainsert["alarm_report_fileurl"]          = $infos[$z]['fileUrl'];
									$datainsert["alarm_report_insert_time"]      = date("Y-m-d H:i:s");
									$datainsert["alarm_report_insert_type"]      = $type;

								//CHECK last data
								// idno, alarmtype,media,starttime
								//jika tidak ada insert
								if($check_row_report == 0){
									$this->dbreport->insert($dbtable,$datainsert);
									printf("===OK \r\n");
									printf("====== ======\r\n");
								}
								//jika sudah ada skip
								else
								{

									printf("===SKIP \r\n");
									printf("====== ======\r\n");


								}

							}//end return
						}//end master looping PHOTO

						//update last running berdasarkan last alarm time
						$update_lastrunning = $this->updateLastRunning($imei,$vehicleno,$userid,$mediatype,$alarm_time);
					}
					else
					{

						printf("===ERROR : %s \r\n", $result_lastalarm["Message"]);
					}
					$j++;
			}

			printf("\r\n");

			//jika bukan cron report
			if($type == "today")
			{
				//update config last check
				unset($datalastcheck);

				$datalastcheck["config_lastcheck"] = $nowtime;
				$this->db = $this->load->database("webtracking_ts",TRUE);
				$this->db->where("config_name", "ALARM_EVIDENCE");
				$this->db->where("config_user", $userid);
				$this->db->where("config_status",1);
				$this->db->update("ts_config",$datalastcheck);

				printf("UPDATE CONFIG TIME OKE \r\n ");

				$this->db->close();
				$this->db->cache_delete_all();

				$enddate = date('Y-m-d H:i:s');

			}

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$servername   = "ABDICOLO-02";
			$message =  urlencode(
						"ALARM EVIDENCE - PHOTO (ON PREM)"." \n".
						"Total Data: ".$datadiproses." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Server: ".$servername." \n".
						"Latency: ".$duration_sec." s"." \n"
						);


			$sendtelegram = $this->telegram_direct("-4003609680",$message); //POC AUTOCHECK
			printf("===SENT TELEGRAM OK\r\n");
			printf("===FINISH Cron start %s to %s \r\n", $cronstartdate, date("Y-m-d H:i:s"));
	}

	function alarmevidence_video_onprem($userid=0, $mdvruser="",$mdvrpass="", $orderby="", $type="", $startdate="", $enddate="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		//variable
		$nowdate = date('Y-m-d H:i:s');
		$interval = 30; //sec
		$type_list = array("MV03");
		$report_photo = "alarm_photo";
		$report_video = "alarm_video";
		$report = "alarm_evidence_";
		$nowtime = date("Y-m-d H:i:s");
		$speed_check = 0;

		//get (last alarm)
		$this->db = $this->load->database("webtracking_ts", TRUE);
		$this->db->order_by("config_lastcheck","asc");
		$this->db->where("config_name","ALARM_EVIDENCE");
		$this->db->where("config_status",1);
		$this->db->where("config_user",$userid);
		$qcfg = $this->db->get("ts_config");
		$rowcfg = $qcfg->row();
		$total_cfg = count($qcfg);
		if ($total_cfg == 0)
		{
			printf("==No Data Configuration \r\n");
			return;
		}else{
			$lastcheck = $rowcfg->config_lastcheck;
			if($type == "report")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
					$datefilename = date("Ymd", strtotime("yesterday"));
					$month = date("F", strtotime("yesterday"));
					$year = date("Y", strtotime("yesterday"));
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else if($type == "today")
			{
				if ($startdate == "") {
					$startdate = date("Y-m-d 00:00:00");
					$datefilename = date("Ymd");
					$month = date("F");
					$year = date("Y");
					$speed_check = 1;
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));
					$startdate = date("Y-m-d 00:00:00", strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
				}

				if ($enddate != "")
				{
					$enddate = date("Y-m-d 23:59:59", strtotime($enddate));
				}

				if ($enddate == "") {
					$enddate = date("Y-m-d 23:59:59");
				}

				$sdate = date("Y-m-d", strtotime($startdate));
				$shour = date("H:i:s", strtotime($startdate));

				$edate = date("Y-m-d", strtotime($enddate));
				$ehour = date("H:i:s", strtotime($enddate));

			}
			else
			{
				$sdate = date("Y-m-d", strtotime($lastcheck));
				$shour = date("H:i:s", strtotime($lastcheck));


				$edate = date("Y-m-d", strtotime($nowtime));
				$ehour = date("H:i:s", strtotime($nowtime));
			}

		}

		//firts param , jika masuk kondisi akan berubah
		$alarm_time = date("Y-m-d H:i:s", strtotime($sdate." ".$shour));

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		$this->db = $this->load->database("default",true);

		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");
		printf("===CONFIG DATETIME = %s \r\n", $sdate." ".$shour." s/d ".$edate." ".$ehour);

		switch ($month)
		{
			case "January":
            $dbtable_photo = $report_photo."januari_".$year;
			$dbtable_video = $report_video."januari_".$year;
			$dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable_photo = $report_photo."februari_".$year;
			$dbtable_video = $report_video."februari_".$year;
			$dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable_photo = $report_photo."maret_".$year;
			$dbtable_video = $report_video."maret_".$year;
			$dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable_photo = $report_photo."april_".$year;
			$dbtable_video = $report_video."april_".$year;
			$dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable_photo = $report_photo."mei_".$year;
			$dbtable_video = $report_video."mei_".$year;
			$dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable_photo = $report_photo."juni_".$year;
			$dbtable_video = $report_video."juni_".$year;
			$dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable_photo = $report_photo."juli_".$year;
			$dbtable_video = $report_video."juli_".$year;
			$dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable_photo = $report_photo."agustus_".$year;
			$dbtable_video = $report_video."agustus_".$year;
			$dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable_photo = $report_photo."september_".$year;
			$dbtable_video = $report_video."september_".$year;
			$dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable_photo = $report_photo."oktober_".$year;
			$dbtable_video = $report_video."oktober_".$year;
			$dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable_photo = $report_photo."november_".$year;
			$dbtable_video = $report_video."november_".$year;
			$dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable_photo = $report_photo."desember_".$year;
			$dbtable_video = $report_video."desember_".$year;
			$dbtable = $report."desember_".$year;
			break;
		}

			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id",$orderby);
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_mv03 != ","0000");
			$this->db->where("vehicle_mv03 != ","");
			$this->db->where("vehicle_server_mdvr !=", ""); //harus terisi
			$this->db->where("vehicle_onprem", 1); //on prem
			//$this->db->where("vehicle_device", "865235053622222@VT200L");

			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);

			//insert db video
			$k = 1;
			$datadiproses = 0;
			for ($m=0;$m<count($rows);$m++)
			{
					//get one time token (sementara taruh di loop device sampai semua pindah)
					$session_id = $this->getOneTimetokenAPI($mdvruser,$mdvrpass,$userid,$rows[$m]->vehicle_server_mdvr);
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					
					$mediatype = 1; //0 photo , 1: video
					$ex_device = explode("@",$rows[$m]->vehicle_device);
					$imei = $rows[$m]->vehicle_mv03;
					$vehicleid = $imei;
					$vehicleno = $rows[$m]->vehicle_no;
					$imei_new = str_replace(" ","-",$vehicleno);

					if($type == "today")
					{

						$lastrunning = $this->getLastRunning($imei,$mediatype);
						if(count($lastrunning)>0)
						{
							$sdate = date("Y-m-d", strtotime($lastrunning->config_lastrunning));
							$shour = date("H:i:s", strtotime($lastrunning->config_lastrunning));
						}
					}


					printf("\r\n");
					printf("===Get Media VIDEO Vehicle %s %s (%d/%d) \r\n", $rows[$m]->vehicle_no, $rows[$m]->vehicle_device, $k, $totalvehicle);
					printf("===Periode %s %s s/d %s %s \r\n", $sdate,$shour,$edate,$ehour);
					$get_lastalarm = $this->securityevidenceapi($session_id,$sdate,$shour,$edate,$ehour,$mediatype,$imei_new,$userid,$rows[$m]->vehicle_server_mdvr);

					$result_lastalarm  = json_decode($get_lastalarm,true);
					//print_r($result_lastalarm);
					$response = $result_lastalarm["StatusCode"];

					if($response == "SUCCESS")
					{

						$infos = $result_lastalarm["Data"]["infos"];
						$total_infos = count($infos);

						for ($y=0;$y<$total_infos;$y++){

								if($infos[$y]['mediaType'] == 0){
									$ex_time = $infos[$y]['fileTime'];
								}else{
									$ex_time = $infos[$y]['fileSTime'];
								}

								$alarm_time = date("Y-m-d H:i:s", (($ex_time/1000)));
								$novideo = $y+1;

								//CHECK last data
								// idno, alarmtype,media,starttime
								//printf("===PARAM ALERT %s, %s, %s, %s, %s, %s \r\n", $ex_device[0], $infos[$y]['devIdno'], $infos[$y]['alarmType'], $mediatype, $alarm_time, $dbtable);
								$check_row_report = $this->CheckExistingReport($ex_device[0],$infos[$y]['devIdno'],$infos[$y]['alarmType'],$mediatype,$alarm_time,$dbtable);

								//jika tidak ada insert
								if($check_row_report == 0){
									$return = 0;
									$datadiproses += 1;
									printf("===PROSES %s of %s & Alarm Time: %s \r\n", $novideo, $total_infos, $alarm_time);
								}
								//jika sudah ada skip
								else
								{
									$return = 1;
									printf("===SUDAH DIPROSES %s of %s & Alarm Time: %s \r\n", $novideo, $total_infos, $alarm_time);
								}

							if($return == 0)
							{


								//insert into report
								if($infos[$y]['position'] != ""){
									$ex_coord = explode(",",$infos[$y]['position']);
									//$lat_coord = "-".$ex_coord[0];
									$lat_coord = $ex_coord[0];
									//$lat_coord = str_replace("--", "-", $lat_coord);
									$lng_coord = $ex_coord[1];
									$coord = $lat_coord.",".$lng_coord;

									$position = $this->getPosition_other($lng_coord, $lat_coord);
									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
									}

								}else{
									$lat_coord = "";
									$lng_coord = "";
									$geofence_start = "";
									$coord = "";
									$position_name = "";
								}

								if($infos[$y]['mediaType'] == 0){
									$ex_time = $infos[$y]['fileTime'];
									$ex_time2 = $ex_time;
									$duration_sec = $ex_time2 - $ex_time;
								}else{
									$ex_time = $infos[$y]['fileSTime'];
									$ex_time2 = $infos[$y]['fileETime'];
									$duration_sec = $ex_time2 - $ex_time;
								}



								$dataalarm_vid = explode("|",$this->getalarmname($infos[$y]['alarmType']));
								$dataalarm_name_vid = $dataalarm_vid[0];
								$dataalarm_level_vid = $dataalarm_vid[1];
								$dataalarm_group_vid = $dataalarm_vid[2];

									unset($datainsert);
									$datainsert["alarm_report_imei"] = $infos[$y]['devIdno'];
									$datainsert["alarm_report_vehicle_id"]       = $ex_device[0];
									$datainsert["alarm_report_vehicle_user_id"]  = $rows[$m]->vehicle_user_id;
									$datainsert["alarm_report_vehicle_no"]       = $rows[$m]->vehicle_no;
									$datainsert["alarm_report_vehicle_name"]     = $rows[$m]->vehicle_name;
									$datainsert["alarm_report_vehicle_type"]     = $rows[$m]->vehicle_type;
									$datainsert["alarm_report_vehicle_company"]  = $rows[$m]->vehicle_company;
									$datainsert["alarm_report_type"] = $infos[$y]['alarmType'];
									$datainsert["alarm_report_name"] = $dataalarm_name_vid;
									$datainsert["alarm_report_level"] = $dataalarm_level_vid;
									$datainsert["alarm_report_group"] = $dataalarm_group_vid;
									$datainsert["alarm_report_media"] = $infos[$y]['mediaType'];
									$datainsert["alarm_report_channel"] = $infos[$y]['channel'];
									$datainsert["alarm_report_gpsstatus"] = $infos[$y]['gpsstatus'];
									/* $datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour"));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time . "+1 Hour")); */
									$datainsert["alarm_report_start_time"]     	 = date("Y-m-d H:i:s", strtotime($alarm_time));
									$datainsert["alarm_report_end_time"]       	 = date("Y-m-d H:i:s", strtotime($alarm_time));
									$datainsert["alarm_report_update_time"] = date("Y-m-d H:i:s", (($infos[$y]['updateTime']/1000)));
									$datainsert["alarm_report_duration_sec"] = $duration_sec/1000;
									$datainsert["alarm_report_location_start"] = $position_name;
									$datainsert["alarm_report_location_end"] = $position_name;
									$datainsert["alarm_report_coordinate_start"] = $coord;
									$datainsert["alarm_report_coordinate_end"] = $coord;
									$datainsert["alarm_report_size"] = $infos[$y]['fileSize'];
									$datainsert["alarm_report_downloadurl"] = $infos[$y]['downloadUrl'];
									$datainsert["alarm_report_path"] = $infos[$y]['filePath'];
									$datainsert["alarm_report_fileurl"] = $infos[$y]['fileUrl'];
									$datainsert["alarm_report_insert_time"]      = date("Y-m-d H:i:s");
									$datainsert["alarm_report_insert_type"]      = $type;


								//CHECK last data
								// idno, alarmtype,media,starttime
								//jika tidak ada insert
								if($check_row_report == 0){

									$this->dbreport = $this->load->database("tensor_report",TRUE);
									$this->dbreport->insert($dbtable,$datainsert);
									printf("===OK \r\n");
									printf("====== ======\r\n");
								}
								//jika sudah ada skip
								else
								{

									printf("===SKIP \r\n");
									printf("====== ======\r\n");
								}

							}

						} //end master looping VIDEO

						//update last running berdasarkan last alarm time
						$update_lastrunning = $this->updateLastRunning($imei,$vehicleno,$userid,$mediatype,$alarm_time);


					}else{

						printf("===ERROR : %s \r\n", $result_lastalarm["Message"]);
					}
					$k++;
			}

			printf("\r\n");

			//jika bukan cron report
			if($type == "today")
			{
				//update config last check
				unset($datalastcheck);

				$datalastcheck["config_lastcheck"] = $nowtime;
				$this->db = $this->load->database("webtracking_ts",TRUE);
				$this->db->where("config_name", "ALARM_EVIDENCE");
				$this->db->where("config_user", $userid);
				$this->db->where("config_status",1);
				$this->db->update("ts_config",$datalastcheck);

				printf("UPDATE CONFIG TIME OKE \r\n ");

				$this->db->close();
				$this->db->cache_delete_all();

				$enddate = date('Y-m-d H:i:s');

			}

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$servername   = "ABDISERVER";
			$message =  urlencode(
						"ALARM EVIDENCE - VIDEO (ON PREM) \n".
						"Total Data: ".$datadiproses." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Server: ".$servername." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			//sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
			$sendtelegram = $this->telegram_direct("-4003609680",$message); //POC AUTOCHECK
			printf("===SENT TELEGRAM OK\r\n");
			printf("===FINISH Cron start %s to %s \r\n", $cronstartdate, date("Y-m-d H:i:s"));
	}

	//Security Evidence Inquiry
	function securityevidenceapi($sessid,$sdate,$shour,$edate,$ehour,$mediatype,$vehicleid,$userid,$domain)
	{

		$alarmtype_list = $this->getalarmtype();

		// $alarmtype_list = "603";
		// $alarmtype_list = "190,191,401,451,600,601,650,651,606,607,656,657"; //190, 191, 401, 451, 600, 601, 650, 651, 606, 607, 656, 657

		$feature = array();
		$dataJson = file_get_contents($domain.""."StandardApiAction_performanceReportPhotoListSafe.action?jsession=".$sessid."&begintime=".$sdate."%20".$shour."&endtime=".$edate."%20".$ehour."&alarmType=".$alarmtype_list."&mediaType=".$mediatype."&toMap=1&vehiIdno=".$vehicleid."&currentPage=&pageRecords=");
		$data = json_decode($dataJson,true);
		$result = $data["result"];
		//print_r($result); 
		//$txt = $domain.""."StandardApiAction_performanceReportPhotoListSafe.action?jsession=".$sessid."&begintime=".$sdate."%20".$shour."&endtime=".$edate."%20".$ehour."&alarmType=".$alarmtype_list."&mediaType=".$mediatype."&toMap=1&vehiIdno=".$vehicleid."&currentPage=&pageRecords=";
		//print_r($txt);


		if($result == 0){
			$result_info = $data["infos"];
			//print_r($data); exit();//for check log print this
			printf("===Total DATA %s \r\n", count($result_info));
			$feature["Data"] = $data;
			$feature["Message"] = "OK";
			$feature["StatusCode"] = "SUCCESS";
			printf("===GET Security Evidence SUCCESS\r\n");
		}else{

			$err_message = $data["message"];
			$result = $data["result"];

			if($err_message == "Session does not exist!")
			{

				
			}

			$feature['result'] = $result;
			$feature["Message"] = $err_message;
			$feature["StatusCode"] = "FAILED";
			printf("===GET Security Evidence FAILED: %s \r\n", $err_message);
		}

		$response = json_encode($feature);

		return $response;

	}

	function getLastRunning($imeicam,$mediatype)
	{

		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("config_imei,config_lastrunning");
		$this->dbts->order_by("config_id", "desc");
		$this->dbts->where("config_status", 1);
		$this->dbts->where("config_media", $mediatype);
		$this->dbts->where("config_imei", $imeicam);
		$q = $this->dbts->get("ts_last_alarmevidence");
		$rows = $q->row();
		$total_rows = count($rows);


		$this->dbts->close();
		$this->dbts->cache_delete_all();

		return $rows;

	}

	function updateLastRunning($imeicam,$vno,$vuserid,$mediatype,$alarmtime)
	{

		//update config last running
		unset($datalastcheck);

		//for update
		$dataupdate["config_lastrunning"] = $alarmtime;
		$dataupdate["config_lastupdate"] = date("Y-m-d H:i:s");

		//for insert
		$datainsert["config_imei"] = $imeicam;
		$datainsert["config_vehicle"] = $vno;
		$datainsert["config_lastrunning"] = $alarmtime;
		$datainsert["config_media"] = $mediatype;
		$datainsert["config_user"] = $vuserid;
		$datainsert["config_status"] = 1;
		$datainsert["config_lastupdate"] = date("Y-m-d H:i:s");

		$this->dbts = $this->load->database("webtracking_ts",TRUE);
		$this->dbts->where("config_imei", $imeicam);
		//$this->dbts->where("config_vehicle", $vno);
		$this->dbts->where("config_user", $vuserid);
		$this->dbts->where("config_media", $mediatype);
		$this->dbts->where("config_status",1);
		$q_report = $this->dbts->get("ts_last_alarmevidence");
		$rows_report = $q_report->row();
		$total_report = count($rows_report);

		if($total_report > 0){
			$this->dbts->where("config_imei", $imeicam);
			//$this->dbts->where("config_vehicle", $vno);
			$this->dbts->where("config_user", $vuserid);
			$this->dbts->where("config_media", $mediatype);
			$this->dbts->where("config_status",1);
			$this->dbts->update("ts_last_alarmevidence",$dataupdate);
			printf("===UPDATE LAST RUNNING OKE \r\n ");
		}else{
			$this->dbts->insert("ts_last_alarmevidence",$datainsert);
			printf("===INSERT NEW LAST RUNNING OKE \r\n ");
		}

		$this->dbts->close();
		$this->dbts->cache_delete_all();

		return;

	}

	function CheckExistingReport($vdevice,$imeicam,$alarmtype,$mediatype,$alarmtime,$dbtable)
	{
		//$alarmtime_wita = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($alarmtime))); //dijadikan WITA ketika compare (isi table WITA)
		$alarmtime_wib = date("Y-m-d H:i:s", strtotime($alarmtime)); //dijadikan WITA ketika compare (isi table WITA) //WIB TIMEZONE
		//printf("===CHECKIING WITA IN TABLE... %s, %s, %s, %s, %s, %s \r\n", $vdevice, $imeicam, $alarmtype, $mediatype, $alarmtime_wita, $dbtable);

		// idno, alarmtype,media,starttime
		$this->dbreport = $this->load->database("tensor_report",TRUE);
		//$this->dbreport->select("alarm_report_id");
		$this->dbreport->where("alarm_report_vehicle_id", $vdevice);
		$this->dbreport->where("alarm_report_imei", $imeicam);
		$this->dbreport->where("alarm_report_type",$alarmtype);
		$this->dbreport->where("alarm_report_media",$mediatype);
		$this->dbreport->where("alarm_report_start_time",$alarmtime_wib);
		$q_report = $this->dbreport->get($dbtable);
		$rows_report = $q_report->row();
		$total_report = count($rows_report);

		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

		return $total_report;

	}
	
	//get new alarm / send telegram
	function alarmevidence_telegram($userid="",$timezone="",$company="")
	{
		date_default_timezone_set("Asia/Jakarta");
		printf("==GET NEW Security Evidence >> START \r\n");
		$cronstartdate = date("Y-m-d H:i:s");
		$nowdate = date("Y-m-d H:i:s");
		if($timezone == "WITA")
		{
			$nowtime_selected = date("Y-m-d H:i:s", strtotime($nowdate . "+1hours"));
		}
		else
		{
			$nowtime_selected = $nowdate;
		}
		
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_selected . "-15 Minutes"));
	
		$this->db = $this->load->database("default", TRUE);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$qvehicle          = $this->db->get("vehicle");
		$rowvehicles       = $qvehicle->result();
		$total_rowvehicles = count($rowvehicles);

		// echo "<pre>";
		// var_dump($array_unit_LMO);die();
		// echo "<pre>";

		$report = "alarm_evidence_";
		$report_sum = $report;
		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", $userid);
		$q            = $this->db->get("user");
		$row          = $q->row();
		$total_row    = count($row);

		/* $startdate = date("Y-m-d 00:00:00");
		$enddate      = date("Y-m-d 23:59:59"); */

		$startdate    = $last_fiveminutes;
		$enddate      = $nowtime_selected;

		$sdate        = $startdate;
		$edate        = $enddate;

		// print_r($sdate." ".$edate);exit();

		printf("===Periode :  %s %s \r\n", $sdate, $edate);

		$m1 = date("F", strtotime($sdate));
		$m2 = date("m", strtotime($sdate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_sum = $report_sum."desember_".$year;
			break;
		}

		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		$alarm_list = array(
							"Smoking Alarm Level One Start","Smoking Alarm Level Two Start",
							"Fatigue Driving Alarm Level One Start","Fatigue Driving Alarm Level Two Start",
							"Call to Call The Alarm Level One Start","Call to Call The Alarm Level Two Start",
							"Car Distance Near Alarm Level One Start","Car Distance Near Alarm Level Two Start",
							"Driver Abnormal Alarm Level One Start","Driver Abnormal Alarm Level Two Start",
							"Lane Deviation Alarm Level One Start", "Lane Deviation Alarm Level Two Start",
							"Forward Collision Alarm Level One Start", "Forward Collision Alarm Level Two Start"
							);

							// "Unfastened Seat Belt Level One Start",
							// "Unfastened Seat Belt Level One End", "Unfastened Seat Belt Level Two Start", "Unfastened Seat Belt Level Two End",
							// "Hands Off Wheel Level One Start",
							// "Distracted Driving Alarm Level One Start","Distracted Driving Level Two Start",
							// "Rear Approach Alarm Start", "Rear Approach Alarm End",
							// "Pedestrian Collision Alarm Level One Start", "Pedestrian Collision Alarm Level Two Start",
		// print_r($dbtable);exit();

		$street_onduty = $this->config->item('street_onduty_autocheck');

		$this->dbalert = $this->load->database("tensor_report", TRUE);

		$this->dbalert->order_by("alarm_report_start_time","desc");
		$this->dbalert->where_in("alarm_report_name", $alarm_list);
		$this->dbalert->where("alarm_report_media", 0); //photo
		$this->dbalert->where("alarm_report_notif", 0); //belum ke send
		$this->dbalert->where("alarm_report_start_time >=", $sdate);
		$this->dbalert->where("alarm_report_start_time <=", $edate);
		$this->dbalert->where("alarm_report_vehicle_company", $company);
		//$this->dbalert->group_by("alarm_report_start_time");
		// $this->dbalert->where_in("alarm_report_location_start", $street_onduty);
		//$this->dbalert->where("alarm_report_id", 626204); //rbt anom 440 17.55.29
		$this->dbalert->limit(100);
		$q           = $this->dbalert->get($dbtable);
		$rows        = $q->result();
		$total_alert = count($rows);
		// print_r($rows);exit();

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		if($total_alert >0)
		{
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				$title_text = str_replace("Start","",$rows[$i]->alarm_report_name);
				$title_name = strtoupper($title_text);
				$alert_level = "";

				$search_level_1 = 'ONE';
				if(preg_match("/{$search_level_1}/i", $title_name)) {
					$alert_level = "1";
				}

				$search_level_2 = 'TWO';
				if(preg_match("/{$search_level_2}/i", $title_name)) {
					$alert_level = "2";
				}


				$vehicle_id       = $rows[$i]->alarm_report_vehicle_id;
				$vehicle_no       = $rows[$i]->alarm_report_vehicle_no;
				$vehicle_name     = $rows[$i]->alarm_report_vehicle_name;
				$vehicle_company  = $rows[$i]->alarm_report_vehicle_company;
				$telegram_group   = $this->get_telegramgroup_overspeed($vehicle_company);
				//$telegram_group = "-657527213,"; //FMS Testing
				$driver           = false;
				//$driver = $this->getdriver($vehicle_id);
				if($driver == false){
					$driver_name = "";
				}else{
					$driver_ex = explode("-",$driver);
					$driver_name = $driver_ex[1];
				}

				printf("===Process Alarm ID: %s Level:  %s Unit: %s (%d/%d) \r\n", $title_name, $alert_level, $vehicle_no, $j, $total_alert);

				$position_name  = $rows[$i]->alarm_report_location_start;
				$gps_time       = date("d-m-Y H:i:s", strtotime($rows[$i]->alarm_report_start_time)); //as is
				$coordinate     = $rows[$i]->alarm_report_coordinate_start;
				$url            = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
				$jalur          = $rows[$i]->alarm_report_jalur;
				$image_link     = $rows[$i]->alarm_report_fileurl;
				$urlimagefix    = "<a href=".$image_link.">IMAGE</a>";
				$alertvehicleid = $rows[$i]->alarm_report_vehicle_id;
				$sdate_alert    = $rows[$i]->alarm_report_start_time;

				$nowtime        = date("Y-m-d H:i:s");
				if($timezone == "WITA")
				{
					$selected_time       = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($nowtime))); //wita
				}else{
					$selected_time       = $nowtime; //wib
				}

				$selected_time_sec = strtotime($selected_time);
				$gps_time_sec   = strtotime($rows[$i]->alarm_report_start_time);
				$delta          = $selected_time_sec - $gps_time_sec;

				printf("===TIME GPS CAM: %s \r\n", $rows[$i]->alarm_report_start_time);
				printf("===TIME SELECTED: %s %s DELTA %s \r\n", $timezone, $selected_time, $delta);

				if($delta < 0 ){
					$skip = 1;
				}else{
					$skip = 0;

				}

				$reportdetailvideo = $this->getvideo_alarmevidence($dbtable, $alertvehicleid, $sdate_alert);
				//print_r($reportdetailvideo);exit();

				$imagealertid = $rows[$i]->alarm_report_id;
				if(count($reportdetailvideo) > 0){
					$videoalertid = $reportdetailvideo[0]['alarm_report_id'];
				}else{
					$videoalertid = 0;

				}

				$monthforparam = $m2;
				$user_id_role = 2;

				$attachmentlink = "https://fmsppa.abditrack.com/attachment/".$videoalertid.'/'.$imagealertid.'/'.$monthforparam.'/'.$year.'/'.$user_id_role.'/'.$userid;

				$reportdetaildecode = explode("|", $rows[$i]->alarm_report_gpsstatus);
				$gpsspeed_kph = number_format($reportdetaildecode[4]/10, 0, '.', '');
				//printf("===Jalur : %s \r\n", $jalur);
				//print_r($attachmentlink);exit();

				$message = urlencode(
							"".$title_name." \n".
							"Time: ".$gps_time." \n".
							"Vehicle No: ".$vehicle_no." \n".
							"Driver: ".$driver_name." \n".
							"Position: ".$position_name." \n".
							"Speed (kph) : ".$gpsspeed_kph." \n".
							"Coordinate: ".$url." \n".
							"Link: ".$attachmentlink." \n"
					);

				//printf("===Message : %s \r\n", $message);

				if($videoalertid > 0){
					sleep(4);

					if($skip == 0 ){
						$sendtelegram = $this->telegram_direct($telegram_group,$message);
						printf("===SENT TELEGRAM OK\r\n");
					}else{
						//$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram FMS TESTING
						printf("X==SKIP TELEGRAM OK\r\n");
					}
				}

				if($skip == 0){
					//update notif status == 1
					unset($datanotif);
					$datanotif["alarm_report_notif"] = 1;
					$this->dbalert->where("alarm_report_id", $rows[$i]->alarm_report_id);
					$this->dbalert->limit(1);
					$this->dbalert->update($dbtable,$datanotif);
					printf("===UPDATE NOTIF OKE \r\n ");
				}
				$j++;
			}

		}else{
			printf("NO DATA SECURITY EVIDENCE \r\n");
		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		$server_name = "ABDICOLO 02";
		$message =  urlencode(
					"SEND MDVR ALERT \n".
					"Start: ".$cronstartdate." \n".
					"Finish: ".date("Y-m-d H:i:s")." \n".
					"Location: ".$server_name." \n".
					"Latency: ".$duration_sec." s"." \n"
					);

		$sendtelegram = $this->telegram_direct("-4003609680",$message); //FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");

			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbalert->close();
			$this->dbalert->cache_delete_all();

			$enddate = date("Y-m-d H:i:s");
			printf("===FINISH %s to %s \r\n", $nowdate, $enddate);
	}
	
	function getalarmtype()
	{
		//ini_set('memory_limit', '2G');
		$this->db = $this->load->database("webtracking_ts", TRUE);
		$this->db->select("alarm_type");
		$this->db->order_by("alarm_type", "asc");
		$this->db->where("alarm_status", 1);
		$q = $this->db->get("ts_alarm");
		$rows = $q->result();
		$total_rows = count($rows);
		$alarm_code = "";
		$alarm_list = 1;
		if(count($rows)>0){
			for ($i=1;$i<$total_rows;$i++){
				$new_code = $rows[$i]->alarm_type;
				$alarm_list .= $alarm_code.",".$new_code;
			}
		}else{
			$alarm_list = 0;
		}

		return $alarm_list;

	}
	
	function getalarmname($id)
	{
		$this->db = $this->load->database("tensor_report", TRUE);
		$this->db->select("alarm_name,alarm_level,alarm_group");
		$this->db->order_by("alarm_id","desc");
		$this->db->where("alarm_type", $id);
		$q = $this->db->get("webtracking_ts_alarm");
		$row = $q->row();
		$total_row = count($row);
		$alarm_name = "-";

		if(count($row)>0){
			$alarm_name = $row->alarm_name."|".$row->alarm_level."|".$row->alarm_group;
		}

		return $alarm_name;

	}
	
	function getPosition_other($longitude, $latitude)
	{
		//$api = $this->config->item('GOOGLE_MAP_API_KEY');
		//$api = "AIzaSyCGr6BW7vPItrWq95DxMvL292Kf6jHNA5c"; //lacaktranslog prem
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		//$georeverse = $this->gpsmodel->getLocation_byGeoCode($latitude, $longitude, $api);

		return $georeverse;
	}
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

		$url = $this->config->item('url_send_telegram');

        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);	//new
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
