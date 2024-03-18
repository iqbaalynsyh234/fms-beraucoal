<?php
//ob_start();
include "base.php";

class Bib_cronjob extends Base {

	function Bib_cronjob()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_securityevidence");
		$this->load->model("m_violation");
		$this->load->model("m_poipoolmaster");
		$this->load->model("log_model");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}

	function insertThisData($table, $data){
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	function getfromDBLIVE($dblive, $gpsname, $gphost){
		$this->dblive = $this->load->database($dblive, TRUE);
		$this->dblive->where("gps_name", $gpsname);
		$this->dblive->where("gps_host", $gphost);
		return $this->dblive->get("gps")->result();
	}

	function getAllVehicle($userid){
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		// $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function getAllVehicle_mdvronly_hazard($userid){
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_user_id", $userid);
		// $this->db->where("vehicle_device", "869926046502486@VT200");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_mv03 != ", 0000);
		// $this->db->where("vehicle_company", 1959);
    // $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function getAllVehicle_mdvronly($userid){
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_mv03 != ", 0000);
		// $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function getAllVehicle_mdvronly_2($userid){
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_mv03 != ", 0000);
		// $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();
		return $result;
	}

	function getAllVehicle_mdvronly_testing($userid){
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		// $this->db->where("vehicle_device", "867717046702602@VT200");
		$this->db->where("vehicle_company", 1959);
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_mv03 != ", 0000);
		// $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function getAllVehicle_fuelsensoronly($companyid, $userid){
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_company", $companyid);
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_sensor != ", "No");
		// $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function getAllVehicleByVCompanytesting($companyid, $userid){
		$this->db     = $this->load->database("default", true);
		$this->db->select("vehicle_mv03, vehicle_no, vehicle_name, vehicle_company, vehicle_device, vehicle_autocheck");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_company", $companyid);
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		// $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function loginforcheckdsmadasison($username, $password){
		$url_login   = "http://172.16.1.2/808gps/StandardApiAction_login.action?account=".$username."&password=".$password;
		$_h = curl_init();
		curl_setopt($_h, CURLOPT_HEADER, 1);
		curl_setopt($_h, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($_h, CURLOPT_HTTPGET, 1);
		curl_setopt($_h, CURLOPT_URL, $url_login );
		curl_setopt($_h, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
		curl_setopt($_h, CURLOPT_DNS_CACHE_TIMEOUT, 2 );

		var_dump(curl_exec($_h));
		var_dump(curl_getinfo($_h));
		var_dump(curl_error($_h));
		die();

		// $ch = curl_init($url_login);
		//
		// // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		// curl_setopt($ch, CURLOPT_POST, true);
		// // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// $result = curl_exec($ch);
		//
		// if ($result === FALSE) {
		// 		die("Login failed: " . curL_error($ch) . " \r\n");
		// }
		//
		// curl_close($ch);
		//
		// $obj = json_decode($result); //print_r($obj);exit();
		// return $obj;
	}

	function getAllVehicleByVCompany($companyid, $userid){
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_company", $companyid);
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		// $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result();
		return $result;
	}

	function getStreetkm($userid){
		$this->db     = $this->load->database("default", true);
		$this->db->order_by("street_name", "ASC");
		$this->db->select("street_name");
		$this->db->like("street_name", "KM");
		$this->db->where("street_creator", $userid);
		$q       = $this->db->get("street");
		$result  = $q->result();
		return $result;
	}

	function getStreetrom($userid){
		$this->db     = $this->load->database("default", true);
		$this->db->order_by("street_name", "ASC");
		$this->db->select("street_name");
		$this->db->like("street_name", "rom");
		$this->db->where("street_creator", $userid);
		$q       = $this->db->get("street");
		$result  = $q->result();
		return $result;
	}

	function getStreetpool($userid){
		$this->db     = $this->load->database("default", true);
		$this->db->order_by("street_name", "ASC");
		$this->db->select("street_name");
		$this->db->like("street_name", "pool");
		$this->db->where("street_creator", $userid);
		$q       = $this->db->get("street");
		$result  = $q->result();
		return $result;
	}

	function checkData($id){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
		$this->dbkalimantan->where("master_portal_id", $id);
		return $this->dbkalimantan->get("bib_master_portal")->result();
	}

	function checkDataPortal($id){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->where("master_portal_id", $id);
		return $this->dbts->get("ts_bib_master_portal")->result();
	}

	function insertData($data){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
		return $this->dbkalimantan->insert("bib_master_portal", $data);
	}

	function insertDataPortal($data){
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert("ts_bib_master_portal", $data);
	}

	function insertDataPortalUmum($table, $data){
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	function insertDataToken($data){
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert("ts_wa_token", $data);
	}

	function insertDataUmum($database, $table, $data){
		$this->dbtensor = $this->load->database($database, true);
		return $this->dbtensor->insert($table, $data);
	}

	function updateDataUmum($database, $table, $vdevice, $date, $datafix){
		$this->dbkalimantan = $this->load->database($database, true);
		$this->dbkalimantan->where("fuelcheck_vehicle_device", $vdevice);
		$this->dbkalimantan->where("fuelcheck_date_real", $date);
		return $this->dbkalimantan->update($table, $datafix);
	}

	function update_data_violationbackdate($database, $table, $violationid, $datafix){
		$this->dbkalimantan = $this->load->database($database, true);
		$this->dbkalimantan->where("violation_id", $violationid);
		return $this->dbkalimantan->update($table, $datafix);
	}

	function updateData($where, $id, $datafix){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
		$this->dbkalimantan->where($where, $id);
		return $this->dbkalimantan->update("bib_master_portal", $datafix);
	}

	function updateDataSummaryMDVR($db, $table, $where, $wherenya, $date, $data){
		$this->dbkalimantan = $this->load->database($db, true);
		$this->dbkalimantan->where($where, $wherenya);
		$this->dbkalimantan->where("devicestatus_summary_submited_date", $date);
		return $this->dbkalimantan->update($table, $data);
	}

	function updateDataSummaryMDVRadminonly($db, $table, $where, $wherenya, $date, $data){
		$this->dbkalimantan = $this->load->database($db, true);
		$this->dbkalimantan->where($where, $wherenya);
		$this->dbkalimantan->where("devicestatus_summary_isformitra", 1);
		$this->dbkalimantan->where("devicestatus_summary_submited_date", $date);
		return $this->dbkalimantan->update($table, $data);
	}

	function updateDataPortal($where, $id, $datafix){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->where($where, $id);
		return $this->dbts->update("ts_bib_master_portal", $datafix);
	}

	function getinfo_fromportal($nolambung){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->order_by("master_portal_updateddate_new","desc");
		$this->dbts->where("master_portal_nolambung", $nolambung);
		$q_portal = $this->dbts->get("ts_bib_master_portal");
		$rows_portal = $q_portal->row();

		return $rows_portal;
	}

	function getinfo_fromportal_status($nolambung,$statusname){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->order_by("master_portal_expired","desc");
		$this->dbts->where("master_portal_nolambung", $nolambung);
		$this->dbts->where("master_portal_status", $statusname);
		$q_portal = $this->dbts->get("ts_bib_master_portal");
		$rows_portal = $q_portal->row();

		return $rows_portal;
	}

	function getinfo_fromwim($nolambung,$statusname){
		$this->dbreport = $this->load->database("tensor_report", true);
		$this->dbreport->order_by("integrationwim_penimbanganFinishLocal","desc");
		$this->dbreport->where("integrationwim_truckID", $nolambung);
		$this->dbreport->where("integrationwim_status", $statusname);
		$this->dbreport->limit(1);
		$q_portal = $this->dbreport->get("historikal_integrationwim_unit");
		$rows_portal = $q_portal->row();

		return $rows_portal;
	}

	function getFromLocationReport($dbtable, $vehicle, $sdate){
		$this->dbtrip = $this->load->database("tensor_report",true);
		$query = "SELECT hour(location_report_gps_time) as hour, location_report_id, location_report_vehicle_user_id, location_report_vehicle_device,
							location_report_vehicle_no, location_report_vehicle_name, location_report_vehicle_company, location_report_speed,
							location_report_fuel_data, location_report_fuel_data_fix, location_report_fuel_liter, location_report_fuel_liter_fix,
							location_report_gps_time, hour(location_report_gps_time) FROM $dbtable
							where location_report_vehicle_device = '$vehicle' and
							-- where location_report_vehicle_device = '869926046535742@VT200' and
							location_report_fuel_data > 0 and
							DATE(location_report_gps_time) = '$sdate'
							group by hour(location_report_gps_time)
							order by hour(location_report_gps_time) ASC ";
		// $this->dbtrip->order_by("location_report_gps_time","asc");
		// $this->dbtrip->where("location_report_vehicle_device", $vehicle);
		//
		// $this->dbtrip->where("DATE(location_report_gps_time)",$sdate);

		$q = $this->dbtrip->query($query);
		$rows = $q->result();
		return $rows;
	}

	function check_data_fuelsensor($database, $table, $vdevice, $date){
		$this->dbtensor = $this->load->database($database, true);
		$this->dbtensor->select("*");
		$this->dbtensor->where("fuelcheck_vehicle_device", $vdevice);
		$this->dbtensor->where("fuelcheck_date_real", $date);
		$q        = $this->dbtensor->get($table);
		return  $q->result_array();
	}

	function mirroring_portal($limit=1){
		ini_set('memory_limit','3048M');
		$starttime = date("Y-m-d H:i:s");
		$totalunit = 0;
		$token = "";
		//from email eky january 2022
		//FMS - Production :  eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo
		//FMS - Development : eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2Mzc2Mzc5OTZ9.rO1QXXzMcyLn7sljo827KbF82JZzWAnzPygXqY47PEI

		$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo";


		printf("==CRON SYNCRONIZE UNIT START : %s \r\n", $starttime);
		//$urlGetToken = "https://esbportal.borneo-indobara.com/external/vehiclelist/getToken"."?"; //DEV
		$urlGetToken = "https://esbportal.borneo-indobara.com/external/getToken"."?"; //PROD sudah live
		$urlGetSync  = "https://esbportal.borneo-indobara.com/external/vehiclelist/getAll?size=".$limit;

		// GET TOKEN
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
					 "Accept: application/json",
					 "Authorization: Bearer " .$clientkey,
				);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_URL, $urlGetToken);
		$result = curl_exec($ch);
		curl_close($ch);

		$obj = json_decode($result); //print_r($obj);exit();
		//printf("==RESULT :  %s \r\n",$obj);
		printf("==TOKEN :  %s \r\n",$obj->token);
		printf("-------------------------- \r\n");

			if (isset($obj->token)) {
				printf("==GET TOKEN SUCCESS \r\n");
				printf("==TRY TO SYNC NOW \r\n");

				$curl = curl_init($urlGetSync);
				curl_setopt($curl, CURLOPT_URL, $urlGetSync);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				$headers = array(
					 "Accept: application/json",
					 "Authorization: Bearer " .$obj->token,
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$resp = curl_exec($curl);
				curl_close($curl);

					if ($resp)
					{

							$truckunit = json_decode($resp);
							//print_r($truckunit);exit();
								$totalunit = sizeof($truckunit->vehicledata);
								printf("==TOTAL UNIT : %s \r\n", $totalunit);
								/*
								sample result  (prid jan 2022)

								[id] => 2403
								[type] => Dump Truck
								[merk] => Hino
								[model] =>
								[series] => FM 260JD
								[no_lambung] => GEC 287-904
								[nomor_mesin] => J08EUFJ-62091
								[nomor_rangka] => MJEFM8JNKEJM-42159
								[expired] => 2019-11-14
								[tahun_pembuatan] => 2014
								[mitra_kerja] => CV Golden Energy Cemerlang
								[department] => Coal Mining & Handling
								[no_rfid] =>
								[status] => Unit Changes
								[created_date] => 2018-05-16T05:43:06.000Z
								[updated_date] => 2019-06-24T01:28:13.000Z

								*/
								for ($i=0; $i < sizeof($truckunit->vehicledata); $i++) {
									usleep(500);
									$new = $i+1;
									$checkData = $this->checkDataPortal($truckunit->vehicledata[$i]->id);
									$updatedate_ex = explode("T", $truckunit->vehicledata[$i]->updated_date);
									$sdate = date("Y-m-d", strtotime($updatedate_ex[0]));
									$stime = date("H:i:s", strtotime(substr($updatedate_ex[1],0,8)));
									//print_r($updatedate_ex);
									//print_r($stime);exit();
									$updated_date_new = date("Y-m-d H:i:s", strtotime($sdate." ".$stime));
									printf("==PROCESS DATA : %s %s of %s \r\n", $truckunit->vehicledata[$i]->no_lambung, $new, $totalunit);
										if (sizeof($checkData) > 0) {

											// UPDATE
											$datafix = array(
												"master_portal_id"	           => $truckunit->vehicledata[$i]->id,
												"master_portal_type"	         => $truckunit->vehicledata[$i]->type,
												"master_portal_merk"	         => $truckunit->vehicledata[$i]->merk,
												"master_portal_model"	         => $truckunit->vehicledata[$i]->model,
												"master_portal_series"	       => $truckunit->vehicledata[$i]->series,
												"master_portal_nolambung"	     => $truckunit->vehicledata[$i]->no_lambung,
												"master_portal_nomesin"	       => $truckunit->vehicledata[$i]->nomor_mesin,
												"master_portal_norangka"	     => $truckunit->vehicledata[$i]->nomor_rangka,
												"master_portal_expired"	       => $truckunit->vehicledata[$i]->expired,
												"master_portal_thnpembuatan"	 => $truckunit->vehicledata[$i]->tahun_pembuatan,
												"master_portal_mitrakerja"	   => $truckunit->vehicledata[$i]->mitra_kerja,
												"master_portal_department"	   => $truckunit->vehicledata[$i]->department,
												"master_portal_norfid_sys"	       => $truckunit->vehicledata[$i]->no_rfid,
												"master_portal_status"	       => $truckunit->vehicledata[$i]->status,
												"master_portal_createddate"	   => $truckunit->vehicledata[$i]->created_date,
												"master_portal_updateddate"	   => $truckunit->vehicledata[$i]->updated_date,
												"master_portal_updateddate_new"	   => $updated_date_new
											);
											$updateNow = $this->updateDataPortal("master_portal_id", $truckunit->vehicledata[$i]->id, $datafix);
												if ($updateNow) {
													$datafix = array();
														printf("==SUCCESS UPDATE DATA \r\n");
												}else {
													printf("==FAILED UPDATE DATA \r\n");
												}
										}else {
											// INSERT
											$datafix = array(
											"master_portal_id"	           => $truckunit->vehicledata[$i]->id,
												"master_portal_type"	         => $truckunit->vehicledata[$i]->type,
												"master_portal_merk"	         => $truckunit->vehicledata[$i]->merk,
												"master_portal_model"	         => $truckunit->vehicledata[$i]->model,
												"master_portal_series"	       => $truckunit->vehicledata[$i]->series,
												"master_portal_nolambung"	     => $truckunit->vehicledata[$i]->no_lambung,
												"master_portal_nomesin"	       => $truckunit->vehicledata[$i]->nomor_mesin,
												"master_portal_norangka"	     => $truckunit->vehicledata[$i]->nomor_rangka,
												"master_portal_expired"	       => $truckunit->vehicledata[$i]->expired,
												"master_portal_thnpembuatan"	 => $truckunit->vehicledata[$i]->tahun_pembuatan,
												"master_portal_mitrakerja"	   => $truckunit->vehicledata[$i]->mitra_kerja,
												"master_portal_department"	   => $truckunit->vehicledata[$i]->department,
												"master_portal_norfid_sys"	   => $truckunit->vehicledata[$i]->no_rfid,
												"master_portal_status"	       => $truckunit->vehicledata[$i]->status,
												"master_portal_createddate"	   => $truckunit->vehicledata[$i]->created_date,
												"master_portal_updateddate"	   => $truckunit->vehicledata[$i]->updated_date,
											"master_portal_updateddate_new"	   => $updated_date_new
											);
											$insertNow = $this->insertDataPortal($datafix);
												if ($insertNow) {
													$datafix = array();
														printf("==SUCCESS INSERT DATA \r\n");
												}else {
													printf("==FAILED INSERT DATA \r\n");
												}
										}
								}
					}
			}

		$endtime = date("Y-m-d H:i:s");
		printf("==SELESAI : %s \r\n", $endtime);

		$contentlog = "Mirroring Unit Portal Success. Total Sync: ".$totalunit;
		$insertlog = $this->log_model->insertlog("SYS", $contentlog, "SYNC", "SYSTEM");

		printf("========================================================= \r\n");
	}

	function mirroring_portal_driver($limit=1){
		ini_set('memory_limit','3048M');
		$starttime = date("Y-m-d H:i:s");
		$totalunit = 0;
		$token = "";
		//from email eky january 2022
		//FMS - Production :  eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo
		//FMS - Development : eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2Mzc2Mzc5OTZ9.rO1QXXzMcyLn7sljo827KbF82JZzWAnzPygXqY47PEI

		//$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2Mzc2Mzc5OTZ9.rO1QXXzMcyLn7sljo827KbF82JZzWAnzPygXqY47PEI"; //DEV
		$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo";


		printf("==CRON SYNCRONIZE UNIT START : %s \r\n", $starttime);
		//$urlGetToken = "https://esbportal.borneo-indobara.com/external/vehiclelist/getToken"."?"; //DEV
		$urlGetToken = "https://devgcpesb.borneo-indobara.com/external/getToken?"."?"; //DEV
		$urlGetSync  = "https://devgpcpesb.borneo-indobara.com/external/simperlist/getAll?size=".$limit;

		// GET TOKEN
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
					 "Accept: application/json",
					 "Authorization: Bearer " .$clientkey,
				);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_URL, $urlGetToken);
		$result = curl_exec($ch);
		curl_close($ch);

		$obj = json_decode($result); //print_r($obj);exit();
		//printf("==RESULT :  %s \r\n",$obj);
		printf("==TOKEN :  %s \r\n",$obj->token);
		printf("-------------------------- \r\n");
		//exit();
			if (isset($obj->token)) {
				printf("==GET TOKEN SUCCESS \r\n");
				printf("==TRY TO SYNC NOW \r\n");

				$curl = curl_init($urlGetSync);
				curl_setopt($curl, CURLOPT_URL, $urlGetSync);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				$headers = array(
					 "Accept: application/json",
					 "Authorization: Bearer " .$obj->token,
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$resp = curl_exec($curl);
				curl_close($curl);


					if ($resp)
					{
							$truckunit = json_decode($resp);
							print_r($truckunit);exit();
								$totalunit = sizeof($truckunit->truckunit);
								printf("==TOTAL UNIT : %s \r\n", $totalunit);
								/*
								sample result  (prid jan 2022)

								[id] => 2400
								[type] => Dump Truck
								[merk] => Hino
								[model] =>
								[series] => FM 260JD
								[no_lambung] => GEC 339-901
								[nomor_mesin] => J08EUFJ-46837
								[nomor_rangka] => MJEFM8JNKCJM-36031
								[expired] => 2020-09-11
								[tahun_pembuatan] => 2014
								[mitra_kerja] => CV Golden Energy Cemerlang
								[department] => Coal Mining & Handling
								[no_rfid] =>
								[status] => Approved
								[created_date] => 2018-05-16T02:22:50.000Z
								[updated_date] => 2020-09-13T01:04:42.000Z

								*/
								for ($i=0; $i < sizeof($truckunit->truckunit); $i++) {
									usleep(500);
									$new = $i+1;
									$checkData = $this->checkDataPortal($truckunit->truckunit[$i]->id);
									$updatedate_ex = explode("T", $truckunit->truckunit[$i]->updated_date);
									$sdate = date("Y-m-d", strtotime($updatedate_ex[0]));
									$stime = date("H:i:s", strtotime(substr($updatedate_ex[1],0,8)));
									//print_r($updatedate_ex);
									//print_r($stime);exit();
									$updated_date_new = date("Y-m-d H:i:s", strtotime($sdate." ".$stime));
									printf("==PROCESS DATA : %s %s of %s \r\n", $truckunit->truckunit[$i]->no_lambung, $new, $totalunit);
										if (sizeof($checkData) > 0) {

											// UPDATE
											$datafix = array(
												"master_portal_id"	           => $truckunit->truckunit[$i]->id,
												"master_portal_type"	         => $truckunit->truckunit[$i]->type,
												"master_portal_merk"	         => $truckunit->truckunit[$i]->merk,
												"master_portal_model"	         => $truckunit->truckunit[$i]->model,
												"master_portal_series"	       => $truckunit->truckunit[$i]->series,
												"master_portal_nolambung"	     => $truckunit->truckunit[$i]->no_lambung,
												"master_portal_nomesin"	       => $truckunit->truckunit[$i]->nomor_mesin,
												"master_portal_norangka"	     => $truckunit->truckunit[$i]->nomor_rangka,
												"master_portal_expired"	       => $truckunit->truckunit[$i]->expired,
												"master_portal_thnpembuatan"	 => $truckunit->truckunit[$i]->tahun_pembuatan,
												"master_portal_mitrakerja"	   => $truckunit->truckunit[$i]->mitra_kerja,
												"master_portal_department"	   => $truckunit->truckunit[$i]->department,
												"master_portal_norfid_sys"	       => $truckunit->truckunit[$i]->no_rfid,
												"master_portal_status"	       => $truckunit->truckunit[$i]->status,
												"master_portal_createddate"	   => $truckunit->truckunit[$i]->created_date,
												"master_portal_updateddate"	   => $truckunit->truckunit[$i]->updated_date,
												"master_portal_updateddate_new"	   => $updated_date_new
											);
											$updateNow = $this->updateDataPortal("master_portal_id", $truckunit->truckunit[$i]->id, $datafix);
												if ($updateNow) {
													$datafix = array();
														printf("==SUCCESS UPDATE DATA \r\n");
												}else {
													printf("==FAILED UPDATE DATA \r\n");
												}
										}else {
											// INSERT
											$datafix = array(
											"master_portal_id"	           => $truckunit->truckunit[$i]->id,
												"master_portal_type"	         => $truckunit->truckunit[$i]->type,
												"master_portal_merk"	         => $truckunit->truckunit[$i]->merk,
												"master_portal_model"	         => $truckunit->truckunit[$i]->model,
												"master_portal_series"	       => $truckunit->truckunit[$i]->series,
												"master_portal_nolambung"	     => $truckunit->truckunit[$i]->no_lambung,
												"master_portal_nomesin"	       => $truckunit->truckunit[$i]->nomor_mesin,
												"master_portal_norangka"	     => $truckunit->truckunit[$i]->nomor_rangka,
												"master_portal_expired"	       => $truckunit->truckunit[$i]->expired,
												"master_portal_thnpembuatan"	 => $truckunit->truckunit[$i]->tahun_pembuatan,
												"master_portal_mitrakerja"	   => $truckunit->truckunit[$i]->mitra_kerja,
												"master_portal_department"	   => $truckunit->truckunit[$i]->department,
												"master_portal_norfid_sys"	   => $truckunit->truckunit[$i]->no_rfid,
												"master_portal_status"	       => $truckunit->truckunit[$i]->status,
												"master_portal_createddate"	   => $truckunit->truckunit[$i]->created_date,
												"master_portal_updateddate"	   => $truckunit->truckunit[$i]->updated_date,
											"master_portal_updateddate_new"	   => $updated_date_new
											);
											$insertNow = $this->insertDataPortal($datafix);
												if ($insertNow) {
													$datafix = array();
														printf("==SUCCESS INSERT DATA \r\n");
												}else {
													printf("==FAILED INSERT DATA \r\n");
												}
										}
								}
					}
			}

		$endtime = date("Y-m-d H:i:s");
		printf("==SELESAI : %s \r\n", $endtime);

		$contentlog = "Mirroring Unit Portal Success. Total Sync: ".$totalunit;
		$insertlog = $this->log_model->insertlog("SYS", $contentlog, "SYNC", "SYSTEM");

		printf("========================================================= \r\n");
	}

	//mirroring master data unit dari portal
	function sync_unit(){
		date_default_timezone_set("Asia/Jakarta");

			$nowtime = date('Y-m-d H:i:s');
			printf("===STARTING SYNC UNIT: %s \r\n", $nowtime);
			$userlist = array('4408');
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("master_portal_nolambung","asc");
			//$this->db->where("master_portal_nolambung", $nolambung);
			$this->db->where("master_portal_flag", 0);
			$q = $this->db->get("master_portal");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("===TOTAL DATA : %s \r\n", $totalvehicle);
			$total_sync = 0;
			$total_unsync = 0;
			for ($i=0;$i<count($rows);$i++)
			{		usleep(500);
					$j = $i+1;
					//get detail from mirroring unit portal
					printf("===Get DETAIL INFO from UNIT PORTAL %s (%d/%d) \r\n", $rows[$i]->master_portal_nolambung, $j, $totalvehicle);
					$data_portal = $this->getinfo_fromportal($rows[$i]->master_portal_nolambung);
					$sync = 0;
					$unsync = 0;
					if(count($data_portal)>0){

						$master_portal_id = $data_portal->master_portal_id;
						$master_portal_nolambung = $data_portal->master_portal_nolambung;
						$master_portal_type = $data_portal->master_portal_type;
						$master_portal_merk = $data_portal->master_portal_merk;
						$master_portal_model = $data_portal->master_portal_model;
						$master_portal_series = $data_portal->master_portal_series;
						$master_portal_nomesin = $data_portal->master_portal_nomesin;
						$master_portal_norangka = $data_portal->master_portal_norangka;
						$master_portal_expired = $data_portal->master_portal_expired;
						$master_portal_thnpembuatan = $data_portal->master_portal_thnpembuatan;
						$master_portal_mitrakerja = $data_portal->master_portal_mitrakerja;
						$master_portal_department = $data_portal->master_portal_department;
						$master_portal_norfid_sys = $data_portal->master_portal_norfid_sys;
						$master_portal_status = $data_portal->master_portal_status;
						$master_portal_createddate = $data_portal->master_portal_createddate;
						$master_portal_updateddate = $data_portal->master_portal_updateddate;
						$master_portal_updateddate_new = $data_portal->master_portal_updateddate_new;

						//update detail info master unit portal
						unset($dataupdate);

						$dataupdate["master_portal_id"] = $master_portal_id;
						$dataupdate["master_portal_nolambung"] = $master_portal_nolambung;
						$dataupdate["master_portal_type"] = $master_portal_type;
						$dataupdate["master_portal_merk"] = $master_portal_merk;
						$dataupdate["master_portal_model"] = $master_portal_model;
						$dataupdate["master_portal_series"] = $master_portal_series;
						$dataupdate["master_portal_nomesin"] = $master_portal_nomesin;
						$dataupdate["master_portal_norangka"] = $master_portal_norangka;
						$dataupdate["master_portal_expired"] = $master_portal_expired;
						$dataupdate["master_portal_thnpembuatan"] = $master_portal_thnpembuatan;
						$dataupdate["master_portal_mitrakerja"] = $master_portal_mitrakerja;
						$dataupdate["master_portal_department"] = $master_portal_department;
						$dataupdate["master_portal_norfid_sys"] = $master_portal_norfid_sys;
						$dataupdate["master_portal_status"] = $master_portal_status;
						$dataupdate["master_portal_createddate"] = $master_portal_createddate;
						$dataupdate["master_portal_updateddate"] = $master_portal_updateddate;
						$dataupdate["master_portal_updateddate_new"] = $master_portal_updateddate_new;

						$this->db->where("master_portal_nolambung", $master_portal_nolambung);
						$this->db->where("master_portal_flag",0);
						$this->db->limit(1);
						$this->db->update("master_portal",$dataupdate);

						printf("===UPDATE MASTER DATA UNIT OKE \r\n ");
						$total_sync = $total_sync + 1;

					}else{
						$total_unsync = $total_unsync + 1;
						printf("===NO DATA IN PORTAL : %s \r\n", $rows[$i]->master_portal_nolambung);
					}

			}
			$endtime = date('Y-m-d H:i:s');
			$contentlog = "Sync Master Data Unit Success. Total: ".$totalvehicle." Total Sync: ".$total_sync." "."Total Unsync: ".$total_unsync;
			$insertlog = $this->log_model->insertlog("SYS", $contentlog, "SYNC", "SYSTEM");
			printf("===FINISH SYNC UNIT: %s \r\n", $endtime);

	}

	//mirroring master data unit dari portal
	function sync_device_to_portal(){
		date_default_timezone_set("Asia/Jakarta");

			$nowtime = date('Y-m-d H:i:s');
			printf("===STARTING master Device: %s \r\n", $nowtime);
			$userlist = array('4408');
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where_in("vehicle_user_id",$userlist);
			$this->db->where("vehicle_status <>", 3);
			//$this->db->limit(10);
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("===TOTAL DATA : %s \r\n", $totalvehicle);
			$total_sync = 0;
			$total_unsync = 0;
			for ($i=0;$i<count($rows);$i++)
			{		usleep(500);
					$j = $i+1;
					//get detail from mirroring unit portal
					printf("===Get DETAIL INFO from UNIT PORTAL %s (%d/%d) \r\n", $rows[$i]->vehicle_no, $j, $totalvehicle);

					$data_portal = $this->getinfo_fromportal_status($rows[$i]->vehicle_no,"Approved");
					$data_wim = $this->getinfo_fromwim($rows[$i]->vehicle_no,"ACTUAL");
					$sync = 0;
					$unsync = 0;
					//update detail info master unit portal
					unset($dataupdate);

					if(count($data_portal)>0){

						if(count($data_wim)>0){

							$master_portal_tare = $data_wim->integrationwim_tare;
							$dataupdate["vehicle_portal_tare"] = $master_portal_tare;
							printf("===Last TARE : %s \r\n", $master_portal_tare);
						}else{

							printf("X==NO DATA ACTUAL WIM BY NO LAMBUNG \r\n ");
						}

						$master_portal_id = $data_portal->master_portal_id;
						$master_portal_nomesin = $data_portal->master_portal_nomesin;
						$master_portal_norangka = $data_portal->master_portal_norangka;
						$master_portal_expired = $data_portal->master_portal_expired;
						$master_portal_status = $data_portal->master_portal_status;
						$master_portal_norfid_sys = $data_portal->master_portal_norfid_sys;
						$master_portal_norfid_wim = $data_portal->master_portal_norfid_wim;


						$dataupdate["vehicle_portal_mesin"] = $master_portal_nomesin;
						$dataupdate["vehicle_portal_rangka"] = $master_portal_norangka;
						$dataupdate["vehicle_portal_rfid_spi"] = $master_portal_norfid_sys;
						$dataupdate["vehicle_portal_rfid_wim"] = $master_portal_norfid_wim;

						$this->db->where("vehicle_id", $rows[$i]->vehicle_id);
						$this->db->limit(1);
						$this->db->update("vehicle",$dataupdate);

						printf("===UPDATE MASTER DEVICE TO PORTAL OKE \r\n ");
						$total_sync = $total_sync + 1;

					}else{
						$total_unsync = $total_unsync + 1;
						printf("X==NO DATA VALID IN PORTAL : %s \r\n", $rows[$i]->vehicle_no);
					}

			}
			$endtime = date('Y-m-d H:i:s');
			$contentlog = "Sync Device To Portal Success. Total: ".$totalvehicle." Total Sync: ".$total_sync." "."Total Unsync: ".$total_unsync;
			$insertlog = $this->log_model->insertlog("SYS", $contentlog, "SYNC DEVICE TO PORTAL", "SYSTEM");
			printf("===FINISH SYNC DEVICE TO PORTAL: %s \r\n", $endtime);

	}

	function minidashboardcron($userid){
		$datavehicleall = $this->getAllVehicle($userid);
		$dataStreetKm   = $this->getStreetkm($userid);
		$dataStreetRom  = $this->getStreetrom($userid);
		$dataStreetPool = $this->getStreetpool($userid);

		// echo "<pre>";
		// var_dump($dataStreetPool);die();
		// echo "<pre>";

		$dataJumlahInKmMuatan['jumlah_km0']  = 0;
		$dataJumlahInKmMuatan['jumlah_km1']  = 0;
		$dataJumlahInKmMuatan['jumlah_km2']  = 0;
		$dataJumlahInKmMuatan['jumlah_km3']  = 0;
		$dataJumlahInKmMuatan['jumlah_km4']  = 0;
		$dataJumlahInKmMuatan['jumlah_km5']  = 0;
		$dataJumlahInKmMuatan['jumlah_km6']  = 0;
		$dataJumlahInKmMuatan['jumlah_km7']  = 0;
		$dataJumlahInKmMuatan['jumlah_km8']  = 0;
		$dataJumlahInKmMuatan['jumlah_km9']  = 0;
		$dataJumlahInKmMuatan['jumlah_km10'] = 0;
		$dataJumlahInKmMuatan['jumlah_km11'] = 0;
		$dataJumlahInKmMuatan['jumlah_km12'] = 0;
		$dataJumlahInKmMuatan['jumlah_km13'] = 0;
		$dataJumlahInKmMuatan['jumlah_km14'] = 0;
		$dataJumlahInKmMuatan['jumlah_km15'] = 0;
		$dataJumlahInKmMuatan['jumlah_km16'] = 0;
		$dataJumlahInKmMuatan['jumlah_km17'] = 0;
		$dataJumlahInKmMuatan['jumlah_km18'] = 0;
		$dataJumlahInKmMuatan['jumlah_km19'] = 0;
		$dataJumlahInKmMuatan['jumlah_km20'] = 0;
		$dataJumlahInKmMuatan['jumlah_km21'] = 0;
		$dataJumlahInKmMuatan['jumlah_km22'] = 0;
		$dataJumlahInKmMuatan['jumlah_km23'] = 0;
		$dataJumlahInKmMuatan['jumlah_km24'] = 0;
		$dataJumlahInKmMuatan['jumlah_km25'] = 0;
		$dataJumlahInKmMuatan['jumlah_km26'] = 0;
		$dataJumlahInKmMuatan['jumlah_km27'] = 0;
		$dataJumlahInKmMuatan['jumlah_km28'] = 0;
		$dataJumlahInKmMuatan['jumlah_km29'] = 0;
		$dataJumlahInKmMuatan['jumlah_km30'] = 0;

		$dataJumlahInKmKosongan['jumlah_km0']  = 0;
		$dataJumlahInKmKosongan['jumlah_km1']  = 0;
		$dataJumlahInKmKosongan['jumlah_km2']  = 0;
		$dataJumlahInKmKosongan['jumlah_km3']  = 0;
		$dataJumlahInKmKosongan['jumlah_km4']  = 0;
		$dataJumlahInKmKosongan['jumlah_km5']  = 0;
		$dataJumlahInKmKosongan['jumlah_km6']  = 0;
		$dataJumlahInKmKosongan['jumlah_km7']  = 0;
		$dataJumlahInKmKosongan['jumlah_km8']  = 0;
		$dataJumlahInKmKosongan['jumlah_km9']  = 0;
		$dataJumlahInKmKosongan['jumlah_km10'] = 0;
		$dataJumlahInKmKosongan['jumlah_km11'] = 0;
		$dataJumlahInKmKosongan['jumlah_km12'] = 0;
		$dataJumlahInKmKosongan['jumlah_km13'] = 0;
		$dataJumlahInKmKosongan['jumlah_km14'] = 0;
		$dataJumlahInKmKosongan['jumlah_km15'] = 0;
		$dataJumlahInKmKosongan['jumlah_km16'] = 0;
		$dataJumlahInKmKosongan['jumlah_km17'] = 0;
		$dataJumlahInKmKosongan['jumlah_km18'] = 0;
		$dataJumlahInKmKosongan['jumlah_km19'] = 0;
		$dataJumlahInKmKosongan['jumlah_km20'] = 0;
		$dataJumlahInKmKosongan['jumlah_km21'] = 0;
		$dataJumlahInKmKosongan['jumlah_km22'] = 0;
		$dataJumlahInKmKosongan['jumlah_km23'] = 0;
		$dataJumlahInKmKosongan['jumlah_km24'] = 0;
		$dataJumlahInKmKosongan['jumlah_km25'] = 0;
		$dataJumlahInKmKosongan['jumlah_km26'] = 0;
		$dataJumlahInKmKosongan['jumlah_km27'] = 0;
		$dataJumlahInKmKosongan['jumlah_km28'] = 0;
		$dataJumlahInKmKosongan['jumlah_km29'] = 0;
		$dataJumlahInKmKosongan['jumlah_km30'] = 0;

		$dataPort['port_bbc'] = 0;
		$dataPort['port_bib'] = 0;
		$dataPort['port_bir'] = 0;
		$dataPort['port_tia'] = 0;

		$dataRom['rom_1']        = 0;
		$dataRom['rom_1_2_road'] = 0;
		$dataRom['rom_2']        = 0;
		$dataRom['rom_3']        = 0;
		$dataRom['rom_3_4_road'] = 0;
		$dataRom['rom_4']        = 0;
		$dataRom['rom_6']        = 0;
		$dataRom['rom_6_road']   = 0;
		$dataRom['rom_7']        = 0;
		$dataRom['rom_7_8_road'] = 0;
		$dataRom['rom_8']        = 0;

		$dataPool['pool_bbs']         = 0;
		$dataPool['pool_bka']         = 0;
		$dataPool['pool_bsl']         = 0;
		$dataPool['pool_gecl']        = 0;
		$dataPool['pool_kusan_bawah'] = 0;
		$dataPool['pool_kusan']       = 0;
		$dataPool['pool_mks']         = 0;
		$dataPool['pool_ram']         = 0;
		$dataPool['pool_rbt']         = 0;
		$dataPool['pool_stli']        = 0;
		$dataPool['ws_gecl']          = 0;
		$dataPool['ws_kmb']           = 0;
		$dataPool['ws_mks']           = 0;
		$dataPool['ws_rbt']           = 0;

		$dataoutofhauling['outofhauling']           = 0;


		$datafixmuatan       = array();
		$datafixkosongan     = array();
		$datafixport         = array();
		$datafixrom          = array();
		$datafixpool         = array();
		$datafixoutofhauling = array();
		$dataexpired         = array();

		for ($i=0; $i < sizeof($datavehicleall); $i++) {
			usleep(500);
			$arr = explode("@", $datavehicleall[$i]->vehicle_device);
			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";

			$datafromdblive  = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]->vehicle_dbname_live, $devices[0], $devices[1]);
			$datagps         = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, date("Y-m-d H:i:s"), $datavehicleall[$i]->vehicle_type);
			$datagpsterakhir = sizeof($datagps);
			// echo "<pre>";
			// var_dump($datafromdblive);die();
			// echo "<pre>";
			// $datagpsfix      = $datagps[$datagpsterakhir-1];

				if ($datafromdblive) {
					$vehicle_autocheck = json_decode($datafromdblive[0]->vehicle_autocheck);
					$jalur             = $vehicle_autocheck->auto_last_road;
					$datakm 					 = explode(",", $datagps->georeverse->display_name);

					// echo "<pre>";
					// var_dump($jalur.'-'.$datakm);die();
					// echo "<pre>";

					// printf("DATA KM : ".$datakm[0]." \r\n");
						if ($jalur == "muatan") {
							// echo "JALUR MUATAN ".$datakm[0]."\r\n";
									if ($datakm[0] == "KM 0" || $datakm[0] == "KM 0.5") {
										$dataJumlahInKmMuatan['jumlah_km0'] += 1;
									}elseif ($datakm[0] == "KM 1" || $datakm[0] == "KM 1.5") {
										$dataJumlahInKmMuatan['jumlah_km1'] += 1;
									}elseif ($datakm[0] == "KM 2" || $datakm[0] == "KM 2.5") {
										$dataJumlahInKmMuatan['jumlah_km2'] += 1;
									}elseif ($datakm[0] == "KM 3" || $datakm[0] == "KM 3.5") {
										$dataJumlahInKmMuatan['jumlah_km3'] += 1;
									}elseif ($datakm[0] == "KM 4" || $datakm[0] == "KM 4.5") {
										$dataJumlahInKmMuatan['jumlah_km4'] += 1;
									}elseif ($datakm[0] == "KM 5" || $datakm[0] == "KM 5.5") {
										$dataJumlahInKmMuatan['jumlah_km5'] += 1;
									}elseif ($datakm[0] == "KM 6" || $datakm[0] == "KM 6.5") {
										$dataJumlahInKmMuatan['jumlah_km6'] += 1;
									}elseif ($datakm[0] == "KM 7" || $datakm[0] == "KM 7.5") {
										$dataJumlahInKmMuatan['jumlah_km7'] += 1;
									}elseif ($datakm[0] == "KM 8" || $datakm[0] == "KM 8.5") {
										$dataJumlahInKmMuatan['jumlah_km8'] += 1;
									}elseif ($datakm[0] == "KM 9" || $datakm[0] == "KM 9.5") {
										$dataJumlahInKmMuatan['jumlah_km9'] += 1;
									}elseif ($datakm[0] == "KM 10" || $datakm[0] == "KM 10.5") {
										$dataJumlahInKmMuatan['jumlah_km10'] += 1;
									}elseif ($datakm[0] == "KM 11" || $datakm[0] == "KM 11.5") {
										$dataJumlahInKmMuatan['jumlah_km11'] += 1;
									}elseif ($datakm[0] == "KM 12" || $datakm[0] == "KM 12.5") {
										$dataJumlahInKmMuatan['jumlah_km12'] += 1;
									}elseif ($datakm[0] == "KM 13" || $datakm[0] == "KM 13.5") {
										$dataJumlahInKmMuatan['jumlah_km13'] += 1;
									}elseif ($datakm[0] == "KM 14" || $datakm[0] == "KM 14.5") {
										$dataJumlahInKmMuatan['jumlah_km14'] += 1;
									}elseif ($datakm[0] == "KM 15" || $datakm[0] == "KM 15.5") {
										$dataJumlahInKmMuatan['jumlah_km15'] += 1;
									}elseif ($datakm[0] == "KM 16" || $datakm[0] == "KM 16.5") {
										$dataJumlahInKmMuatan['jumlah_km16'] += 1;
									}elseif ($datakm[0] == "KM 17" || $datakm[0] == "KM 17.5") {
										$dataJumlahInKmMuatan['jumlah_km17'] += 1;
									}elseif ($datakm[0] == "KM 18" || $datakm[0] == "KM 18.5") {
										$dataJumlahInKmMuatan['jumlah_km18'] += 1;
									}elseif ($datakm[0] == "KM 19" || $datakm[0] == "KM 19.5") {
										$dataJumlahInKmMuatan['jumlah_km19'] += 1;
									}elseif ($datakm[0] == "KM 20" || $datakm[0] == "KM 20.5") {
										$dataJumlahInKmMuatan['jumlah_km20'] += 1;
									}elseif ($datakm[0] == "KM 21" || $datakm[0] == "KM 21.5") {
										$dataJumlahInKmMuatan['jumlah_km21'] += 1;
									}elseif ($datakm[0] == "KM 22" || $datakm[0] == "KM 22.5") {
										$dataJumlahInKmMuatan['jumlah_km22'] += 1;
									}elseif ($datakm[0] == "KM 23" || $datakm[0] == "KM 23.5") {
										$dataJumlahInKmMuatan['jumlah_km23'] += 1;
									}elseif ($datakm[0] == "KM 24" || $datakm[0] == "KM 24.5") {
										$dataJumlahInKmMuatan['jumlah_km24'] += 1;
									}elseif ($datakm[0] == "KM 25" || $datakm[0] == "KM 25.5") {
										$dataJumlahInKmMuatan['jumlah_km25'] += 1;
									}elseif ($datakm[0] == "KM 26" || $datakm[0] == "KM 26.5") {
										$dataJumlahInKmMuatan['jumlah_km26'] += 1;
									}elseif ($datakm[0] == "KM 27" || $datakm[0] == "KM 27.5") {
										$dataJumlahInKmMuatan['jumlah_km27'] += 1;
									}elseif ($datakm[0] == "KM 28" || $datakm[0] == "KM 28.5") {
										$dataJumlahInKmMuatan['jumlah_km28'] += 1;
									}elseif ($datakm[0] == "KM 29" || $datakm[0] == "KM 29.5") {
										$dataJumlahInKmMuatan['jumlah_km29'] += 1;
									}elseif ($datakm[0] == "KM 30" || $datakm[0] == "KM 30.5") {
										$dataJumlahInKmMuatan['jumlah_km30'] += 1;
									}
									printf($datavehicleall[$i]->vehicle_no." " . $datavehicleall[$i]->vehicle_name." Muatan \r\n");
									// echo "JALUR MUATAN : ". $datakm[0]." ".$datavehicleall[$i]->vehicle_no." " . $datavehicleall[$i]->vehicle_name."\r\n"; //. " - COMPARE : ".$datakmfromstreet[0]."\r\n";
						}else {
							// echo "JALUR KOSONGAN ".$datakm[0]."\r\n";
									if ($datakm[0] == "KM 0" || $datakm[0] == "KM 0.5") {
										$dataJumlahInKmKosongan['jumlah_km0'] += 1;
									}elseif ($datakm[0] == "KM 1" || $datakm[0] == "KM 1.5") {
										$dataJumlahInKmKosongan['jumlah_km1'] += 1;
									}elseif ($datakm[0] == "KM 2" || $datakm[0] == "KM 2.5") {
										$dataJumlahInKmKosongan['jumlah_km2'] += 1;
									}elseif ($datakm[0] == "KM 3" || $datakm[0] == "KM 3.5") {
										$dataJumlahInKmKosongan['jumlah_km3'] += 1;
									}elseif ($datakm[0] == "KM 4" || $datakm[0] == "KM 4.5") {
										$dataJumlahInKmKosongan['jumlah_km4'] += 1;
									}elseif ($datakm[0] == "KM 5" || $datakm[0] == "KM 5.5") {
										$dataJumlahInKmKosongan['jumlah_km5'] += 1;
									}elseif ($datakm[0] == "KM 6" || $datakm[0] == "KM 6.5") {
										$dataJumlahInKmKosongan['jumlah_km6'] += 1;
									}elseif ($datakm[0] == "KM 7" || $datakm[0] == "KM 7.5") {
										$dataJumlahInKmKosongan['jumlah_km7'] += 1;
									}elseif ($datakm[0] == "KM 8" || $datakm[0] == "KM 8.5") {
										$dataJumlahInKmKosongan['jumlah_km8'] += 1;
									}elseif ($datakm[0] == "KM 9" || $datakm[0] == "KM 9.5") {
										$dataJumlahInKmKosongan['jumlah_km9'] += 1;
									}elseif ($datakm[0] == "KM 10" || $datakm[0] == "KM 10.5") {
										$dataJumlahInKmKosongan['jumlah_km10'] += 1;
									}elseif ($datakm[0] == "KM 11" || $datakm[0] == "KM 11.5") {
										$dataJumlahInKmKosongan['jumlah_km11'] += 1;
									}elseif ($datakm[0] == "KM 12" || $datakm[0] == "KM 12.5") {
										$dataJumlahInKmKosongan['jumlah_km12'] += 1;
									}elseif ($datakm[0] == "KM 13" || $datakm[0] == "KM 13.5") {
										$dataJumlahInKmKosongan['jumlah_km13'] += 1;
									}elseif ($datakm[0] == "KM 14" || $datakm[0] == "KM 14.5") {
										$dataJumlahInKmKosongan['jumlah_km14'] += 1;
									}elseif ($datakm[0] == "KM 15" || $datakm[0] == "KM 15.5") {
										$dataJumlahInKmKosongan['jumlah_km15'] += 1;
									}elseif ($datakm[0] == "KM 16" || $datakm[0] == "KM 16.5") {
										$dataJumlahInKmKosongan['jumlah_km16'] += 1;
									}elseif ($datakm[0] == "KM 17" || $datakm[0] == "KM 17.5") {
										$dataJumlahInKmKosongan['jumlah_km17'] += 1;
									}elseif ($datakm[0] == "KM 18" || $datakm[0] == "KM 18.5") {
										$dataJumlahInKmKosongan['jumlah_km18'] += 1;
									}elseif ($datakm[0] == "KM 19" || $datakm[0] == "KM 19.5") {
										$dataJumlahInKmKosongan['jumlah_km19'] += 1;
									}elseif ($datakm[0] == "KM 20" || $datakm[0] == "KM 20.5") {
										$dataJumlahInKmKosongan['jumlah_km20'] += 1;
									}elseif ($datakm[0] == "KM 21" || $datakm[0] == "KM 21.5") {
										$dataJumlahInKmKosongan['jumlah_km21'] += 1;
									}elseif ($datakm[0] == "KM 22" || $datakm[0] == "KM 22.5") {
										$dataJumlahInKmKosongan['jumlah_km22'] += 1;
									}elseif ($datakm[0] == "KM 23" || $datakm[0] == "KM 23.5") {
										$dataJumlahInKmKosongan['jumlah_km23'] += 1;
									}elseif ($datakm[0] == "KM 24" || $datakm[0] == "KM 24.5") {
										$dataJumlahInKmKosongan['jumlah_km24'] += 1;
									}elseif ($datakm[0] == "KM 25" || $datakm[0] == "KM 25.5") {
										$dataJumlahInKmKosongan['jumlah_km25'] += 1;
									}elseif ($datakm[0] == "KM 26" || $datakm[0] == "KM 26.5") {
										$dataJumlahInKmKosongan['jumlah_km26'] += 1;
									}elseif ($datakm[0] == "KM 27" || $datakm[0] == "KM 27.5") {
										$dataJumlahInKmKosongan['jumlah_km27'] += 1;
									}elseif ($datakm[0] == "KM 28" || $datakm[0] == "KM 28.5") {
										$dataJumlahInKmKosongan['jumlah_km28'] += 1;
									}elseif ($datakm[0] == "KM 29" || $datakm[0] == "KM 29.5") {
										$dataJumlahInKmKosongan['jumlah_km29'] += 1;
									}elseif ($datakm[0] == "KM 30" || $datakm[0] == "KM 30.5") {
										$dataJumlahInKmKosongan['jumlah_km30'] += 1;
									}

									printf($datavehicleall[$i]->vehicle_no." " . $datavehicleall[$i]->vehicle_name." Kosongan \r\n");
									// echo "JALUR KOSONGAN : ". $datakm[0]." ".$datavehicleall[$i]->vehicle_no." " . $datavehicleall[$i]->vehicle_name."\r\n"; //. " - COMPARE : ".$datakmfromstreet[0]."\r\n";
						}

							if ($datakm[0] == "PORT BBC") {
								$dataPort['port_bbc']  += 1;
							}elseif ($datakm[0] == "PORT BIB") {
								$dataPort['port_bib']  += 1;
							}elseif ($datakm[0] == "PORT BIR") {
								$dataPort['port_bir']  += 1;
							}elseif ($datakm[0] == "PORT TIA") {
								$dataPort['port_tia']  += 1;
							}elseif ($datakm[0] == "ROM 01") {
								$dataRom['rom_1']  += 1;
							}elseif ($datakm[0] == "ROM 01/02 ROAD") {
								$dataRom['rom_1_2_road']  += 1;
							}elseif ($datakm[0] == "ROM 02") {
								$dataRom['rom_2']  += 1;
							}elseif ($datakm[0] == "ROM 03") {
								$dataRom['rom_3']  += 1;
							}elseif ($datakm[0] == "ROM 03/04 ROAD") {
								$dataRom['rom_3_4_road']  += 1;
							}elseif ($datakm[0] == "ROM 04") {
								$dataRom['rom_4']  += 1;
							}elseif ($datakm[0] == "ROM 06 ROAD") {
								$dataRom['rom_6_road']  += 1;
							}elseif ($datakm[0] == "ROM 06") {
								$dataRom['rom_6']  += 1;
							}elseif ($datakm[0] == "ROM 07") {
								$dataRom['rom_7']  += 1;
							}elseif ($datakm[0] == "ROM 07/08 ROAD") {
								$dataRom['rom_7_8_road']  += 1;
							}elseif ($datakm[0] == "ROM 08") {
								$dataRom['rom_8']  += 1;
							}elseif ($datakm[0] == "POOL BBS") {
								$dataPool['pool_bbs'] += 1;
							}elseif ($datakm[0] == "POOL BKA") {
								$dataPool['pool_bka'] += 1;
							}elseif ($datakm[0] == "POOL BSL") {
								$dataPool['pool_bsl'] += 1;
							}elseif ($datakm[0] == "POOL GECL") {
								$dataPool['pool_gecl'] += 1;
							}elseif ($datakm[0] == "POOL KUSAN BAWAH") {
								$dataPool['pool_kusan_bawah'] += 1;
							}elseif ($datakm[0] == "POOL KUSAN") {
								$dataPool['pool_kusan'] += 1;
							}elseif ($datakm[0] == "POOL MKS") {
								$dataPool['pool_mks'] += 1;
							}elseif ($datakm[0] == "POOL RAM") {
								$dataPool['pool_ram'] += 1;
							}elseif ($datakm[0] == "POOL RBT") {
								$dataPool['pool_rbt'] += 1;
							}elseif ($datakm[0] == "POOL STLI") {
								$dataPool['pool_stli'] += 1;
							}elseif ($datakm[0] == "WS GECL") {
								$dataPool['ws_gecl'] += 1;
							}elseif ($datakm[0] == "WS KMB") {
								$dataPool['ws_kmb'] += 1;
							}elseif ($datakm[0] == "WS MKS") {
								$dataPool['ws_mks'] += 1;
							}elseif ($datakm[0] == "WS RBT") {
								$dataPool['ws_rbt'] += 1;
							}else {
								printf($datavehicleall[$i]->vehicle_no." " . $datavehicleall[$i]->vehicle_name." Out Of Hauling \r\n");
								$dataoutofhauling['outofhauling'] += 1;
							}
				}
			}

			// echo "<pre>";
			// var_dump($dataJumlahInKmMuatan); "<br>";
			// var_dump($dataJumlahInKmKosongan); "<br>";
			// var_dump($dataPool); "<br>";
			// var_dump($dataRom); "<br>";
			// var_dump($dataPool); "<br>";
			// var_dump("SELESAI");die();
			// echo "<pre>";

			$datafixmuatan = array(
				"minidashboard_type"         => "muatan",
				"minidashboard_json"         => json_encode($dataJumlahInKmMuatan),
				"minidashboard_user_id"      => $userid,
				"minidashboard_created_date" => date("Y-m-d H:i:s")
			);

			$datafixkosongan = array(
				"minidashboard_type"         => "kosongan",
				"minidashboard_json"         => json_encode($dataJumlahInKmKosongan),
				"minidashboard_user_id"      => $userid,
				"minidashboard_created_date" => date("Y-m-d H:i:s")
			);

			$datafixport = array(
				"minidashboard_type"         => "port",
				"minidashboard_json"         => json_encode($dataPort),
				"minidashboard_user_id"      => $userid,
				"minidashboard_created_date" => date("Y-m-d H:i:s")
			);

			$datafixrom = array(
				"minidashboard_type"         => "rom",
				"minidashboard_json"         => json_encode($dataRom),
				"minidashboard_user_id"      => $userid,
				"minidashboard_created_date" => date("Y-m-d H:i:s")
			);

			$datafixpool = array(
				"minidashboard_type"         => "pool_ws",
				"minidashboard_json"         => json_encode($dataPool),
				"minidashboard_user_id"      => $userid,
				"minidashboard_created_date" => date("Y-m-d H:i:s")
			);

			$datafixoutofhauling = array(
				"minidashboard_type"         => "outofhauling",
				"minidashboard_json"         => json_encode($dataoutofhauling),
				"minidashboard_user_id"      => $userid,
				"minidashboard_created_date" => date("Y-m-d H:i:s")
			);

			// echo "<pre>";
			// var_dump($datafixpool);
			// echo "<pre>";
			//
			// echo "<pre>";
			// var_dump($datafixrom);die();
			// echo "<pre>";


		$insertDataMuatan       = $this->insertThisData("ts_minidashboard", $datafixmuatan);
		$insertDataKosongan     = $this->insertThisData("ts_minidashboard", $datafixkosongan);
		$insertDataPort         = $this->insertThisData("ts_minidashboard", $datafixport);
		$insertDataRom          = $this->insertThisData("ts_minidashboard", $datafixrom);
		$insertDataPool         = $this->insertThisData("ts_minidashboard", $datafixpool);
		$insertDataOutOfHauling = $this->insertThisData("ts_minidashboard", $datafixoutofhauling);

			if ($insertDataMuatan) {
				printf("== INSERT DATA MUATAN SUCCESS \r\n");
			}else {
				printf("== FAILED INSERT DATA MUATAN \r\n");
			}

			if ($insertDataMuatan) {
				printf("== INSERT DATA KOSONGAN SUCCESS \r\n");
			}else {
				printf("== FAILED INSERT DATA MUATAN \r\n");
			}

			if ($insertDataPort) {
				printf("== INSERT DATA POOL SUCCESS \r\n");
			}else {
				printf("== FAILED INSERT DATA POOL \r\n");
			}

			if ($insertDataRom) {
				printf("== INSERT DATA ROM SUCCESS \r\n");
			}else {
				printf("== FAILED INSERT DATA ROM \r\n");
			}

			if ($insertDataPool) {
				printf("== INSERT DATA POOL SUCCESS \r\n");
			}else {
				printf("== FAILED INSERT DATA POOL \r\n");
			}

			if ($insertDataOutOfHauling) {
				printf("== INSERT DATA OUT OF HAULING SUCCESS \r\n");
			}else {
				printf("== FAILED INSERT DATA OUT OF HAULING \r\n");
			}

		$endtime = date("Y-m-d H:i:s");
		printf("==SELESAI : %s \r\n", $endtime);

		$contentlog = "Cron Get Jumlah Kendaraan Per KM, Port, Pool, ROM & Out of Hauling Success.";
		$insertlog = $this->log_model->insertlog("SYS", $contentlog, "CRON", "SYSTEM");

		printf("========================================================= \r\n");
	}

	function violationmonitoring($userid){
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON START : ". $cronstartdate . "\r\n");
		$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
		print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");

		$datafix           = array();
		$masterdatavehicle = $this->getAllVehicle($userid);
		$violationmaster   = $this->m_violation->getviolationmaster();

		// GET ALARM MASTER
			for ($j=0; $j < sizeof($violationmaster); $j++) {
				usleep(500);
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
				for ($k=0; $k < sizeof($alarmbymaster); $k++) {
					usleep(500);
					$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
				}
			}

		// CONVERT ARRAY ALARM KE STRING
		$alarmtypefix = implode (",", $alarmtypefromaster);

		// LOGIN API
		$username        = "DEMOPOC";
		$password        = "000000";
		$loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
		$loginbarudecode = json_decode($loginbaru);
		$jsession        = $loginbarudecode->jsession;

		// echo "<pre>";
		// var_dump($loginbarudecode);die();
		// echo "<pre>";

		$date       = date("Y-m-d");
		$time       = date("H:i:s", strtotime("-2hours"));
		$startdate  = $date.'%20'.$time;

		$edate      = date("Y-m-d");
		$etime      = date("H:i:s");
		$enddate    = $edate.'%20'.$etime;
		$updatetime = "2020-07-23%2023:59:59";

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				usleep(500);
				// for ($i=0; $i < 1; $i++) {
				$autocheck                 = json_decode($masterdatavehicle[$i]->vehicle_autocheck);
				$isinhauling               = $autocheck->auto_last_hauling;
				$lastcourse 							 = $autocheck->auto_last_course;
				$jalur_namefix 				     = $this->m_securityevidence->get_jalurname($lastcourse);
				// {"":"P","auto_last_update":"2022-05-24 14:24:06","auto_last_check":"2022-05-24 00:00:00","auto_last_position":"EST ROAD  BANJAR KALIMANTAN SELATAN","auto_last_lat":"-3.502445","auto_last_long":"115.593888","auto_last_engine":"ON","auto_last_gpsstatus":"OK","auto_last_speed":"15","auto_last_course":"128","auto_last_road":"muatan","auto_last_hauling":"out","auto_last_rom_name":"ROM A1","auto_last_rom_time":"2022-05-22 08:53:55","auto_last_port_name":"PORT BIB","auto_last_port_time":"2022-05-24 12:44:37","auto_flag":0,"vehicle_gotohistory":0,"auto_change_engine_status":"ON","auto_change_engine_datetime":"2022-05-24 14:23:33","auto_change_position":"KM 19.5,  TANAH BUMBU KALIMANTAN SELATAN","auto_change_coordinate":"-3.57265,115.655123"}"

					if ($isinhauling == "in") {
						print_r(($i+1). " OF " .sizeof($masterdatavehicle). "\r\n");
						print_r("===================================================== \r\n");
						print_r(" Vehicle : " .$masterdatavehicle[$i]->vehicle_no.'-'.$masterdatavehicle[$i]->vehicle_name. "\r\n");
						// $deviceID = "819058546530";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1207
						// $deviceID = "819058611680";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
						// $deviceID = "819058587559";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
						$deviceID = $masterdatavehicle[$i]->vehicle_mv03; //"819058587559" 142045031243;//$masterdatavehicle[$i]->vehicle_mv03; BKA
							if ($deviceID != 0000) {
								$datafromapi = file_get_contents("http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=1000&toMap=1&checkend=0&updatetime=".$updatetime."&language=en");
									if ($datafromapi) {
										$createdurl = "http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=5&toMap=1&checkend=0&updatetime=".$updatetime."&language=en";
										$fromapidecode     = json_decode($datafromapi);
										// print_r(" URL CREATED " .$createdurl. "\r\n");

										if (isset($fromapidecode->alarms) == NULL) {
											print_r(" NO DATA ALARM \r\n");
											$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehicle[$i]->vehicle_device);
											$company   = $this->dashboardmodel->getcompany_idforevidence($masterdatavehicle[$i]->vehicle_company);
											if (sizeof($checkindb) < 1) {
												$todatabase = array(
													"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
													"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
													"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
													"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
													"violation_status" 			 	             => 0,
													"violation_vehicle_companyid" 		 	   => "",
													"violation_vehicle_companyname" 		 	 => "",
													"violation_type" 	 	 	                 => "",
													"violation_type_id" 	 	 	             => "",
													"violation_position" 		 	             => "",
													"violation_jalur" 		 	               => "",
													"violation_fatigue" 		               => "",
													"violation_update" 				             => date("Y-m-d H:i:s")
												);
												$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
													if ($insert) {
														print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
													}else {
														print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
													}
											}else {
												$todatabase = array(
													"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
													"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
													"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
													"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
													"violation_status" 			 	             => 0,
													"violation_status_tele" 			 	       => 0,
													"violation_vehicle_companyid" 		 	   => "",
													"violation_vehicle_companyname" 		 	 => "",
													"violation_type" 	 	 	                 => "",
													"violation_type_id" 	 	 	             => "",
													"violation_position" 		 	             => "",
													"violation_jalur" 		 	               => "",
													"violation_fatigue" 		               => "",
													"violation_update" 				             => date("Y-m-d H:i:s")
												);
												$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehicle[$i]->vehicle_device, $todatabase);
													if ($update) {
														print_r("DATA UPDATE FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
													}else {
														print_r("DATA UPDATE FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
													}
											}
										}else {
											print_r(" ===== FATIGUE FOUNDED =====\r\n");
											print_r(" URL CREATED " .$createdurl. "\r\n");
											$alarmcallback     = $fromapidecode->alarms;
											$totaldatacallback = sizeof($alarmcallback);

											// echo "<pre>";
											// var_dump($alarmcallback);die();
											// echo "<pre>";

											for ($j=0; $j < 1; $j++) {
												usleep(500);
												print_r(" ALERT ID : " .$fromapidecode->alarms[$totaldatacallback-1]->atp. "\r\n");
												print_r(" ALERT NAME : " .$fromapidecode->alarms[$totaldatacallback-1]->atpStr. "\r\n");

												// echo "<pre>";
												// var_dump($fromapidecode->alarms);die();
												// echo "<pre>";

												if (in_array($fromapidecode->alarms[$totaldatacallback-1]->atp, $alarmtypefromaster)){
													$position = "";
													$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($fromapidecode->alarms[$totaldatacallback-1]->smlng, $fromapidecode->alarms[$totaldatacallback-1]->smlat, $masterdatavehicle[$i]->vehicle_user_id, "webtracking_gps_temanindobara_live");
													if ($geofence_location) {
														$geofence_type = $geofence_location[0]->geofence_type;
														$geofence_name = $geofence_location[0]->geofence_name;

															if ($geofence_type == "road") {
																$geofencefix   = $geofence_location[0]->geofence_name;
																$current_speed = $autocheck->auto_last_speed;
																if ($jalur_namefix == "kosongan") {
																	$speedlimitforcount = $geofence_location[0]->geofence_speed;
																	$speedlimitforview  = $geofence_location[0]->geofence_speed_alias;
																}elseif ($jalur_namefix == "muatan") {
																	$speedlimitforcount = $geofence_location[0]->geofence_speed_muatan;
																	$speedlimitforview  = $geofence_location[0]->geofence_speed_muatan_alias;
																}

																	$positionalert     = $this->gpsmodel->GeoReverse($fromapidecode->alarms[$totaldatacallback-1]->smlat, $fromapidecode->alarms[$totaldatacallback-1]->smlng);
																	if ($positionalert->display_name != "Unknown Location!") {
																		$positionexplode = explode(",", $positionalert->display_name);
																		$position = $positionexplode[0];
																	}else {
																		$position = $positionalert->display_name;
																	}
															}
														}

														print_r(" POSITION : " .$position. "\r\n");
														$atpStr = "";
														  if ($fromapidecode->alarms[$totaldatacallback-1]->atp == 618) {
														    $atpStr = "Fatigue Driving Alarm Level One Start";
														  }elseif ($fromapidecode->alarms[$totaldatacallback-1]->atp == 619) {
														    $atpStr = "Fatigue Driving Alarm Level Two Start";
														  }else {
														    $atpStr = $fromapidecode->alarms[$totaldatacallback-1]->atpStr;
														  }

													array_push($datafix, array(
														"isfatigue" 		     => "yes",
														"vehicle_user_id"    => $masterdatavehicle[$i]->vehicle_user_id,
														"vehicle_no"         => $masterdatavehicle[$i]->vehicle_no,
														"vehicle_name"       => $masterdatavehicle[$i]->vehicle_name,
														"vehicle_company"    => $masterdatavehicle[$i]->vehicle_company,
														"vehicle_device"     => $masterdatavehicle[$i]->vehicle_device,
														"vehicle_mv03"       => $masterdatavehicle[$i]->vehicle_mv03,
														"gps_alertid"        => $fromapidecode->alarms[$totaldatacallback-1]->atp,
														"gps_alert"          => $atpStr,
														"gps_time"           => $fromapidecode->alarms[$totaldatacallback-1]->bTimeStr,
														"gps_latitude_real"  => $fromapidecode->alarms[$totaldatacallback-1]->smlat,
														"gps_longitude_real" => $fromapidecode->alarms[$totaldatacallback-1]->smlng,
														"position"           => $position,
														"gps_speed"          => ($fromapidecode->alarms[$totaldatacallback-1]->ssp)/10,
													));

													$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehicle[$i]->vehicle_device);
													$company   = $this->dashboardmodel->getcompany_idforevidence($masterdatavehicle[$i]->vehicle_company);

													if (sizeof($checkindb) < 1) {
														$todatabase = array(
															"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
															"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
															"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
															"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
															"violation_status" 			 	             => 1,
															"violation_vehicle_companyid" 		 	   => $company->company_id,
															"violation_vehicle_companyname" 		 	 => $company->company_name,
															"violation_type" 	 	 	                 => "fatigue",
															"violation_type_id" 	 	 	             => $datafix[0]['gps_alertid'],
															"violation_position" 		 	             => $position,
															"violation_jalur" 		 	               => $jalur_namefix,
															"violation_fatigue" 		               => json_encode($datafix),
															"violation_update" 				             => date("Y-m-d H:i:s", strtotime($datafix[0]['gps_time'])), //date("Y-m-d H:i:s")
															"violation_server_datetime" 				   => date("Y-m-d H:i:s")
														);
														$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
															if ($insert) {
																print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}else {
																print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}
													}else {
														$todatabase = array(
															"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
															"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
															"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
															"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
															"violation_status" 			 	             => 1,
															"violation_status_tele" 			 	       => 0,
															"violation_vehicle_companyid" 		 	   => $company->company_id,
															"violation_vehicle_companyname" 		 	 => $company->company_name,
															"violation_type" 	 	 	                 => "fatigue",
															"violation_type_id" 	 	 	             => $datafix[0]['gps_alertid'],
															"violation_position" 		 	             => $position,
															"violation_jalur" 		 	               => $jalur_namefix,
															"violation_fatigue" 		               => json_encode($datafix),
															"violation_update" 				             => date("Y-m-d H:i:s", strtotime($datafix[0]['gps_time'])),
															"violation_server_datetime" 				   => date("Y-m-d H:i:s")
														);
														$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehicle[$i]->vehicle_device, $todatabase);
															if ($update) {
																print_r("DATA UPDATE FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}else {
																print_r("DATA UPDATE FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}
													}
													// KOSONGKAN ARRAY
													$datafix = array();
												}
											}
										}
										print_r("===================================================== \r\n");
									}
							}
					}
			}
			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$message =  urlencode(
						"VIOLATION MONITORING CAM \n".
						"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			$sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			printf("===SENT TELEGRAM OK\r\n");
	}

	function violationmonitoringhistorikal($userid){
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON START : ". $cronstartdate . "\r\n");
		$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
		print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON Violation Historikal Start WITA           : ". $nowtime_wita . "\r\n");
		print_r("CRON Violation Historikal Start WIB          : ". $cronstartdate . "\r\n");

		$datafix           = array();
		$masterdatavehicle = $this->getAllVehicle_mdvronly_testing($userid);
		$violationmaster   = $this->m_violation->getviolationmaster();

		// echo "<pre>";
		// var_dump($violationmaster);die();
		// echo "<pre>";

		// GET ALARM MASTER
		$alarmtypefromaster = array();
			for ($j=0; $j < sizeof($violationmaster); $j++) {
				usleep(500);
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
				for ($k=0; $k < sizeof($alarmbymaster); $k++) {
					usleep(500);
					$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
				}
			}

		// CONVERT ARRAY ALARM KE STRING
		$alarmtypefix = implode (",", $alarmtypefromaster);

		// echo "<pre>";
		// var_dump($alarmtypefix);die();
		// echo "<pre>";

		// LOGIN API
		$username        = "DEMOPOC";
		$password        = "000000";
		// $loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
		$loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
		$loginbarudecode = json_decode($loginbaru);
		$jsession        = $loginbarudecode->jsession;

		// echo "<pre>";
		// var_dump($loginbaru);die();
		// echo "<pre>";

		$date          = date("Y-m-d", strtotime($nowtime_wita));
		// $time       = date("H:i:s", strtotime("-2hours"));
		$time          = date("H:i:s", strtotime($nowtime_wita . "-90 Minutes"));
		$startdate     = $date.'%20'.$time;

		$edate         = date("Y-m-d", strtotime($nowtime_wita));
		$edate_nexday  = date("Y-m-d", strtotime($nowtime_wita . "+1 Day"));
		$etime         = date("H:i:s", strtotime($nowtime_wita ."+1 Hour"));
		$etime_nextday = date("H", strtotime($nowtime_wita ."+1 Hour"));
			if ($etime_nextday == "00") {
				$enddate    = $edate_nexday.'%20'.$etime;
			}else {
				$enddate    = $edate.'%20'.$etime;
			}
		$updatetime = "2020-07-23%2023:59:59";
		print_r("RANGE DATETIME SEARCH : ". $startdate ." s/d " . $enddate . "\r\n");

		// $startdate = "2022-12-31%2009:40:00";
		// $enddate   = "2022-12-31%2010:50:00";
		// "2022-12-31%2010:24:21-2022-12-31%2011:34:21";

		// echo "<pre>";
		// var_dump($startdate.'-'.$enddate);die();
		// echo "<pre>";

		// CHOOSE DBTABLE
		$m1        = date("F", strtotime($nowtime_wita));
		$year      = date("Y", strtotime($nowtime_wita));
		$dbtable   = "";
		$report    = "historikal_violation_";

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

		// echo "<pre>";
		// var_dump($masterdatavehicle);die();
		// echo "<pre>";
		$totaldata_baru = 0;
			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				usleep(500);
				// for ($i=0; $i < 1; $i++) {
				$autocheck                 = json_decode($masterdatavehicle[$i]->vehicle_autocheck);
				$isinhauling               = $autocheck->auto_last_hauling;
				$lastcourse 							 = $autocheck->auto_last_course;
				// $isinhauling               = "in"; // NANTI GANTI YG ATAS KLO AUTOCHECK UDAH JADI
				// $lastcourse 							 = 90; // NANTI GANTI YG ATAS KLO AUTOCHECK UDAH JADI
				$jalur_namefix 				     = $this->m_securityevidence->get_jalurname($lastcourse);
				// {"":"P","auto_last_update":"2022-05-24 14:24:06","auto_last_check":"2022-05-24 00:00:00","auto_last_position":"EST ROAD  BANJAR KALIMANTAN SELATAN","auto_last_lat":"-3.502445","auto_last_long":"115.593888","auto_last_engine":"ON","auto_last_gpsstatus":"OK","auto_last_speed":"15","auto_last_course":"128","auto_last_road":"muatan","auto_last_hauling":"out","auto_last_rom_name":"ROM A1","auto_last_rom_time":"2022-05-22 08:53:55","auto_last_port_name":"PORT BIB","auto_last_port_time":"2022-05-24 12:44:37","auto_flag":0,"vehicle_gotohistory":0,"auto_change_engine_status":"ON","auto_change_engine_datetime":"2022-05-24 14:23:33","auto_change_position":"KM 19.5,  TANAH BUMBU KALIMANTAN SELATAN","auto_change_coordinate":"-3.57265,115.655123"}"

					// if ($isinhauling == "in") {
						print_r(($i+1). " OF " .sizeof($masterdatavehicle). "\r\n");
						print_r("===================================================== \r\n");
						print_r(" Vehicle : " .$masterdatavehicle[$i]->vehicle_no.'-'.$masterdatavehicle[$i]->vehicle_name. "\r\n");
						// $deviceID = "819058546530";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1207
						// $deviceID = "819058611680";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
						// $deviceID = "819058587559";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
						$deviceID = $masterdatavehicle[$i]->vehicle_mv03; //"819058587559" 142045031243;//$masterdatavehicle[$i]->vehicle_mv03; BKA
							if ($deviceID != 0000) {

								// $datafromapi = file_get_contents("http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=1000&toMap=1&checkend=0&updatetime=".$updatetime."&language=en");
								$datafromapi = file_get_contents("http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=1000&toMap=1&checkend=0&updatetime=".$updatetime."&language=en");
									if ($datafromapi) {
										// $createdurl = "http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=5&toMap=1&checkend=0&updatetime=".$updatetime."&language=en";
										$createdurl = "http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=5&toMap=1&checkend=0&updatetime=".$updatetime."&language=en";
										$fromapidecode     = json_decode($datafromapi);
										// print_r(" URL CREATED " .$createdurl. "\r\n");

										// echo "<pre>";
										// var_dump($fromapidecode);die();
										// // var_dump($startdate.'-'.$enddate);die();
										// echo "<pre>";

										if (isset($fromapidecode->alarms) != NULL) {
											print_r(" NO DATA ALARM \r\n");
											print_r(" ===== FATIGUE FOUNDED =====\r\n");
											print_r(" URL CREATED " .$createdurl. "\r\n");
											$alarmcallback     = $fromapidecode->alarms;
											$totaldatacallback = sizeof($alarmcallback);

											// if ($totaldatacallback > 1) {
											// 	echo "<pre>";
											// 	var_dump($totaldatacallback);die();
											// 	echo "<pre>";
											// }

											for ($j=0; $j < $totaldatacallback; $j++) {
												usleep(500);
												print_r(" ALERT ID : " .$fromapidecode->alarms[$j]->atp. "\r\n");
												print_r(" ALERT NAME : " .$fromapidecode->alarms[$j]->atpStr. "\r\n");

												if (in_array($fromapidecode->alarms[$j]->atp, $alarmtypefromaster)){
													// $geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($fromapidecode->alarms[$j]->smlng, $fromapidecode->alarms[$j]->smlat, $masterdatavehicle[$i]->vehicle_user_id, "webtracking_gps_temanindobara_live");
													$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($fromapidecode->alarms[$j]->smlng, $fromapidecode->alarms[$j]->smlat, $masterdatavehicle[$i]->vehicle_user_id, $masterdatavehicle[$i]->vehicle_dbname_live);
													// echo "<pre>";
													// var_dump($geofence_location);die();
													// echo "<pre>";
													if ($geofence_location) {
														$geofence_type = $geofence_location[0]->geofence_type;
														$geofence_name = $geofence_location[0]->geofence_name;

															if ($geofence_type == "road") {
																$geofencefix   = $geofence_location[0]->geofence_name;
																$current_speed = $autocheck->auto_last_speed;
																// $current_speed = 0; // NANTI GANTI YG ATAS KLO AUTOCHECK UDAH JADI
																if ($jalur_namefix == "kosongan") {
																	$speedlimitforcount = $geofence_location[0]->geofence_speed;
																	$speedlimitforview  = $geofence_location[0]->geofence_speed_alias;
																}elseif ($jalur_namefix == "muatan") {
																	$speedlimitforcount = $geofence_location[0]->geofence_speed_muatan;
																	$speedlimitforview  = $geofence_location[0]->geofence_speed_muatan_alias;
																}
															}
														}

														$position = "";
														$latitude_data = $fromapidecode->alarms[$j]->smlat;
														$longitude_data = $fromapidecode->alarms[$j]->smlng;
															// if (strpos($latitude_data, "-") === false) {
															// 	$latitude_data = "-".$latitude_data;
															// }else {
															// 	$latitude_data = $fromapidecode->alarms[$j]->smlat;
															// }

															// echo "<pre>";
															// var_dump($fromapidecode->alarms[$j]->smlat.'||'.$latitude_data);die();
															// echo "<pre>";

															$hour_server = date("H");
															$hour_alert  = $fromapidecode->alarms[$j]->bTimeStr;

															// echo "<pre>";
															// var_dump($hour_server.'-'.$hour_alert);die();
															// echo "<pre>";

														$positionalert     = $this->gpsmodel->GeoReverse($latitude_data,$longitude_data);
														if ($positionalert->display_name != "Unknown Location!") {
															$positionexplode = explode(",", $positionalert->display_name);
															$position = $positionexplode[0];
														}else {
															$position = $positionalert->display_name;
														}

														print_r(" POSITION : " .$position. "\r\n");
														$atpStr = "";
															if ($fromapidecode->alarms[$j]->atp == 618) {
																$atpStr = "Fatigue Driving Alarm Level One Start";
															}elseif ($fromapidecode->alarms[$j]->atp == 619) {
																$atpStr = "Fatigue Driving Alarm Level Two Start";
															}else {
																$atpStr = $fromapidecode->alarms[$j]->atpStr;
															}

													array_push($datafix, array(
														"isfatigue" 		     => "yes",
														"vehicle_user_id"    => $masterdatavehicle[$i]->vehicle_user_id,
														"vehicle_no"         => $masterdatavehicle[$i]->vehicle_no,
														"vehicle_name"       => $masterdatavehicle[$i]->vehicle_name,
														"vehicle_company"    => $masterdatavehicle[$i]->vehicle_company,
														"vehicle_device"     => $masterdatavehicle[$i]->vehicle_device,
														"vehicle_mv03"       => $masterdatavehicle[$i]->vehicle_mv03,
														"gps_alertid"        => $fromapidecode->alarms[$j]->atp,
														"gps_alert"          => $atpStr,
														"gps_time"           => date("Y-m-d H:i:s", strtotime($fromapidecode->alarms[$j]->bTimeStr  . '+1 Hour')),
														"gps_latitude_real"  => $latitude_data,
														"gps_longitude_real" => $longitude_data,
														"position"           => $position,
														"gps_speed"          => ($fromapidecode->alarms[$j]->ssp)/10,
													));

													$checkindb = $this->m_securityevidence->checktodbviolationtensor($dbtable, $masterdatavehicle[$i]->vehicle_device, date("Y-m-d H:i:s", strtotime($fromapidecode->alarms[$j]->bTimeStr  . '+1 Hour')));
													$company   = $this->dashboardmodel->getcompany_idforevidence($masterdatavehicle[$i]->vehicle_company);

													if (sizeof($checkindb) < 1) {
														$todatabase = array(
															"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
															"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
															"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
															"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
															"violation_status" 			 	             => 1,
															"violation_vehicle_companyid" 		 	   => $company->company_id,
															"violation_vehicle_companyname" 		 	 => $company->company_name,
															"violation_type" 	 	 	                 => "fatigue",
															"violation_type_id" 	 	 	             => $datafix[0]['gps_alertid'],
															"violation_position" 		 	             => $position,
															"violation_jalur" 		 	               => $jalur_namefix,
															"violation_fatigue" 		               => json_encode($datafix),
															"violation_update" 				             => date("Y-m-d H:i:s", strtotime($datafix[0]['gps_time'])), //date("Y-m-d H:i:s")
															"violation_server_datetime" 				   => date("Y-m-d H:i:s", strtotime("+1 Hour"))
														);
														$insert = $this->m_securityevidence->insertviolationtensor($dbtable, $todatabase);
															if ($insert) {
																$totaldata_baru += 1;
																print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}else {
																print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}
													}
													// KOSONGKAN ARRAY
													$datafix = array();
												}
											}
										}else {
											print_r(" ===== FATIGUE NOT FOUNDED =====\r\n");
										}
										print_r("===================================================== \r\n");
									}
							}
					// }
			}
			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"VIOLATION MONITORING HISTORIKAL FMS DEMO (BERAU) \n".
						"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						"Total Data Baru: ".$totaldata_baru." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			// $sendtelegram = $this->telegram_direct_local("-577190673",$message); // FMS VIOLATION CRON
			$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
			printf("===SENT TELEGRAM OK\r\n");
	}

	function violationmonitoringhistorikal_perday($userid){
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON START : ". $cronstartdate . "\r\n");
		$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
		print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON Violation Historikal Perday Start WITA           : ". $nowtime_wita . "\r\n");
		print_r("CRON Violation Historikal Perday Start WIB          : ". $cronstartdate . "\r\n");

		// echo "<pre>";
		// var_dump("DONE");die();
		// echo "<pre>";

		$datafix           = array();
		// $masterdatavehicle = $this->getAllVehicle_mdvronly_testing($userid);
		$masterdatavehicle = $this->getAllVehicle_mdvronly($userid);
		$violationmaster   = $this->m_violation->getviolationmaster();

		// GET ALARM MASTER
		$alarmtypefromaster = array();
			for ($j=0; $j < sizeof($violationmaster); $j++) {
				usleep(500);
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
				for ($k=0; $k < sizeof($alarmbymaster); $k++) {
					usleep(500);
					$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
				}
			}

		// CONVERT ARRAY ALARM KE STRING
		$alarmtypefix = implode (",", $alarmtypefromaster);

		// echo "<pre>";
		// var_dump($masterdatavehicle);die();
		// echo "<pre>";

		// LOGIN API
		$username        = "DEMOPOC";
		$password        = "000000";
		// $loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
		$loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
		$loginbarudecode = json_decode($loginbaru);
		$jsession        = $loginbarudecode->jsession;

		// echo "<pre>";
		// var_dump($loginbaru);die();
		// echo "<pre>";

		$date       = date("Y-m-d", strtotime("-1 Day"));
		// $time    = date("H:i:s", strtotime("-2hours"));
		$time       = "00:00:00";
		$startdate  = $date.'%20'.$time;

		$edate      = date("Y-m-d", strtotime("-1 Day"));
		$etime      = "23:59:59";
		$enddate    = $edate.'%20'.$etime;
		$updatetime = "2020-07-23%2023:59:59";
		print_r("RANGE DATETIME SEARCH : ". $startdate ." s/d " . $enddate . "\r\n");

		// echo "<pre>";
		// var_dump("DONE");die();
		// echo "<pre>";

		// $startdate = "2022-12-31%2009:40:00";
		// $enddate   = "2022-12-31%2010:50:00";
		// "2022-12-31%2010:24:21-2022-12-31%2011:34:21";

		// echo "<pre>";
		// var_dump($startdate.'-'.$enddate);die();
		// echo "<pre>";

		// CHOOSE DBTABLE
		$m1        = date("F", strtotime($nowtime_wita));
		$year      = date("Y", strtotime($nowtime_wita));
		$dbtable   = "";
		$report    = "historikal_violation_";

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

		// echo "<pre>";
		// var_dump($dbtable);die();
		// echo "<pre>";
		$totaldata_baru = 0;
			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				usleep(500);
				// for ($i=0; $i < 1; $i++) {
				$autocheck                 = json_decode($masterdatavehicle[$i]->vehicle_autocheck);
				$isinhauling               = $autocheck->auto_last_hauling;
				$lastcourse 							 = $autocheck->auto_last_course;
				$jalur_namefix 				     = $this->m_securityevidence->get_jalurname($lastcourse);
				// {"":"P","auto_last_update":"2022-05-24 14:24:06","auto_last_check":"2022-05-24 00:00:00","auto_last_position":"EST ROAD  BANJAR KALIMANTAN SELATAN","auto_last_lat":"-3.502445","auto_last_long":"115.593888","auto_last_engine":"ON","auto_last_gpsstatus":"OK","auto_last_speed":"15","auto_last_course":"128","auto_last_road":"muatan","auto_last_hauling":"out","auto_last_rom_name":"ROM A1","auto_last_rom_time":"2022-05-22 08:53:55","auto_last_port_name":"PORT BIB","auto_last_port_time":"2022-05-24 12:44:37","auto_flag":0,"vehicle_gotohistory":0,"auto_change_engine_status":"ON","auto_change_engine_datetime":"2022-05-24 14:23:33","auto_change_position":"KM 19.5,  TANAH BUMBU KALIMANTAN SELATAN","auto_change_coordinate":"-3.57265,115.655123"}"

					// if ($isinhauling == "in") {
						print_r(($i+1). " OF " .sizeof($masterdatavehicle). "\r\n");
						print_r("===================================================== \r\n");
						print_r(" Vehicle : " .$masterdatavehicle[$i]->vehicle_no.'-'.$masterdatavehicle[$i]->vehicle_name. "\r\n");
						// $deviceID = "819058546530";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1207
						// $deviceID = "819058611680";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
						// $deviceID = "819058587559";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
						$deviceID = $masterdatavehicle[$i]->vehicle_mv03; //"819051864088";//$masterdatavehicle[$i]->vehicle_mv03; //"819058587559" 142045031243;//$masterdatavehicle[$i]->vehicle_mv03; BKA
							if ($deviceID != 0000) {

								$datafromapi = file_get_contents("http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=1000&toMap=1&checkend=0&updatetime=".$updatetime."&language=en");
									if ($datafromapi) {
										$createdurl = "http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=5&toMap=1&checkend=0&updatetime=".$updatetime."&language=en";
										$fromapidecode     = json_decode($datafromapi);
										// print_r(" URL CREATED " .$createdurl. "\r\n");

										// echo "<pre>";
										// var_dump($datafromapi);die();
										// // var_dump($startdate.'-'.$enddate);die();
										// echo "<pre>";

										if (isset($fromapidecode->alarms) != NULL) {
											print_r(" NO DATA ALARM \r\n");
											print_r(" ===== FATIGUE FOUNDED =====\r\n");
											print_r(" URL CREATED " .$createdurl. "\r\n");
											$alarmcallback     = $fromapidecode->alarms;
											$totaldatacallback = sizeof($alarmcallback);

											// if ($totaldatacallback > 1) {
											// 	echo "<pre>";
											// 	var_dump($totaldatacallback);die();
											// 	echo "<pre>";
											// }

											for ($j=0; $j < $totaldatacallback; $j++) {
												usleep(500);
												print_r(" ALERT ID : " .$fromapidecode->alarms[$j]->atp. "\r\n");
												print_r(" ALERT NAME : " .$fromapidecode->alarms[$j]->atpStr. "\r\n");

												// echo "<pre>";
												// var_dump($fromapidecode->alarms);die();
												// echo "<pre>";

												if (in_array($fromapidecode->alarms[$j]->atp, $alarmtypefromaster)){
													// $geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($fromapidecode->alarms[$j]->smlng, $fromapidecode->alarms[$j]->smlat, $masterdatavehicle[$i]->vehicle_user_id, "webtracking_gps_temanindobara_live");
													$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($fromapidecode->alarms[$j]->smlng, $fromapidecode->alarms[$j]->smlat, $masterdatavehicle[$i]->vehicle_user_id, $masterdatavehicle[$i]->vehicle_dbname_live);
													if ($geofence_location) {
														$geofence_type = $geofence_location[0]->geofence_type;
														$geofence_name = $geofence_location[0]->geofence_name;

															if ($geofence_type == "road") {
																$geofencefix   = $geofence_location[0]->geofence_name;
																$current_speed = $autocheck->auto_last_speed;
																if ($jalur_namefix == "kosongan") {
																	$speedlimitforcount = $geofence_location[0]->geofence_speed;
																	$speedlimitforview  = $geofence_location[0]->geofence_speed_alias;
																}elseif ($jalur_namefix == "muatan") {
																	$speedlimitforcount = $geofence_location[0]->geofence_speed_muatan;
																	$speedlimitforview  = $geofence_location[0]->geofence_speed_muatan_alias;
																}
															}
														}

														$position = "";
														$latitude_data = $fromapidecode->alarms[$j]->smlat;
														$longitude_data = $fromapidecode->alarms[$j]->smlng;
															// if (strpos($latitude_data, "-") === false) {
															// 	$latitude_data = "-".$latitude_data;
															// }else {
															// 	$latitude_data = $fromapidecode->alarms[$j]->smlat;
															// }

															// echo "<pre>";
															// var_dump($fromapidecode->alarms[$j]->smlat.'||'.$latitude_data);die();
															// echo "<pre>";

														$positionalert     = $this->gpsmodel->GeoReverse($latitude_data,$longitude_data);
														if ($positionalert->display_name != "Unknown Location!") {
															$positionexplode = explode(",", $positionalert->display_name);
															$position = $positionexplode[0];
														}else {
															$position = $positionalert->display_name;
														}

														print_r(" POSITION : " .$position. "\r\n");
														$atpStr = "";
															if ($fromapidecode->alarms[$j]->atp == 618) {
																$atpStr = "Fatigue Driving Alarm Level One Start";
															}elseif ($fromapidecode->alarms[$j]->atp == 619) {
																$atpStr = "Fatigue Driving Alarm Level Two Start";
															}else {
																$atpStr = $fromapidecode->alarms[$j]->atpStr;
															}

													array_push($datafix, array(
														"isfatigue" 		     => "yes",
														"vehicle_user_id"    => $masterdatavehicle[$i]->vehicle_user_id,
														"vehicle_no"         => $masterdatavehicle[$i]->vehicle_no,
														"vehicle_name"       => $masterdatavehicle[$i]->vehicle_name,
														"vehicle_company"    => $masterdatavehicle[$i]->vehicle_company,
														"vehicle_device"     => $masterdatavehicle[$i]->vehicle_device,
														"vehicle_mv03"       => $masterdatavehicle[$i]->vehicle_mv03,
														"gps_alertid"        => $fromapidecode->alarms[$j]->atp,
														"gps_alert"          => $atpStr,
														"gps_time"           => date("Y-m-d H:i:s", strtotime($fromapidecode->alarms[$j]->bTimeStr . "+1 Hour")),
														"gps_latitude_real"  => $latitude_data,
														"gps_longitude_real" => $longitude_data,
														"position"           => $position,
														"gps_speed"          => ($fromapidecode->alarms[$j]->ssp)/10,
													));

													$checkindb = $this->m_securityevidence->checktodbviolationtensor($dbtable, $masterdatavehicle[$i]->vehicle_device, $fromapidecode->alarms[$j]->bTimeStr);
													$company   = $this->dashboardmodel->getcompany_idforevidence($masterdatavehicle[$i]->vehicle_company);

													if (sizeof($checkindb) < 1) {
														$todatabase = array(
															"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
															"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
															"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
															"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
															"violation_status" 			 	             => 1,
															"violation_status_tele" 			 	       => 1,
															"violation_vehicle_companyid" 		 	   => $company->company_id,
															"violation_vehicle_companyname" 		 	 => $company->company_name,
															"violation_type" 	 	 	                 => "fatigue",
															"violation_type_id" 	 	 	             => $datafix[0]['gps_alertid'],
															"violation_position" 		 	             => $position,
															"violation_jalur" 		 	               => $jalur_namefix,
															"violation_fatigue" 		               => json_encode($datafix),
															"violation_update" 				             => date("Y-m-d H:i:s", strtotime($datafix[0]['gps_time'])), //date("Y-m-d H:i:s")
															"violation_server_datetime" 				   => date("Y-m-d H:i:s", strtotime("+1 Hour"))
														);
														$insert = $this->m_securityevidence->insertviolationtensor($dbtable, $todatabase);
															if ($insert) {
																$totaldata_baru += 1;
																print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}else {
																print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
															}
													}
													// KOSONGKAN ARRAY
													$datafix = array();
												}
											}
										}else {
											print_r(" ===== FATIGUE NOT FOUNDED =====\r\n");
										}
										print_r("===================================================== \r\n");
									}
							}
					// }
			}
			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"VIOLATION MONITORING HISTORIKAL COLLECTING YESTERDAY DATA (POC BC) \n".
						"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						"Total Data Baru: ".$totaldata_baru." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			// $sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
			printf("===SENT TELEGRAM OK\r\n");
	}

	function telegram_direct_local($groupid,$message)
		{
				error_reporting(E_ALL);
				ini_set('display_errors', 1);

				//$url = "http://lacak-mobil.com/telegram/telegram_directpost";
				//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
				// $url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
				$url = $this->config->item("url_send_telegram");
				// $url = "http://183.81.153.28/telegram/telegram_directpost";
				//$url = "http://admin.abditrack.com/telegram/telegram_directpost";


				$data = array("id" => $groupid, "message" => $message);
				$data_string = json_encode($data);

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
				$result = curl_exec($ch);

				if ($result === FALSE) {
						die("Curl failed: " . curL_error($ch));
				}
				echo $result;
				echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

		}

	function telegram_direct($groupid,$message)
		{
				error_reporting(E_ALL);
				ini_set('display_errors', 1);

				//$url = "http://lacak-mobil.com/telegram/telegram_directpost";
				//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
				// $url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
				$url = $this->config->item("url_send_telegram");
				// $url = "http://183.81.153.28/telegram/telegram_directpost";
				//$url = "http://admin.abditrack.com/telegram/telegram_directpost";


				$data = array("id" => $groupid, "message" => $message);
				$data_string = json_encode($data);

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
				$result = curl_exec($ch);

				if ($result === FALSE) {
						die("Curl failed: " . curL_error($ch));
				}
				echo $result;
				echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

		}

		function telegram_directcheckmdvr($groupid,$message)
			{
					error_reporting(E_ALL);
					ini_set('display_errors', 1);

					//$url = "http://lacak-mobil.com/telegram/telegram_directpost";
					//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
					// $url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
					$url = $this->config->item("url_send_telegram");

					// echo "<pre>";
					// var_dump($url);die();
					// echo "<pre>";
					// $url = "https://fmspoc.abditrack.com/telegram/telegram_directpost";
					// $url = "http://admintib2.abditrack.com/telegram/telegram_directpost";


					$data = array("id" => $groupid, "message" => $message);
					$data_string = json_encode($data);

					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
					$result = curl_exec($ch);

					if ($result === FALSE) {
							die("Curl failed: " . curL_error($ch));
					}
					echo $result;
					echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

			}

		function violationmonitoringtest($userid){
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON START : ". $cronstartdate . "\r\n");
			$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
			print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");

			$datafix           = array();
			$masterdatavehicle = $this->getAllVehicle($userid);
			$violationmaster   = $this->m_violation->getviolationmaster();

			// GET ALARM MASTER
				for ($j=0; $j < sizeof($violationmaster); $j++) {
					usleep(500);
					$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
					for ($k=0; $k < sizeof($alarmbymaster); $k++) {
						$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
					}
				}

			// CONVERT ARRAY ALARM KE STRING
			$alarmtypefix = implode (",", $alarmtypefromaster);

			// LOGIN API
			$username        = "DEMOPOC";
			$password        = "000000";
			$loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
			$loginbarudecode = json_decode($loginbaru);
			$jsession        = $loginbarudecode->jsession;

			// echo "<pre>";
			// var_dump($loginbarudecode);die();
			// echo "<pre>";

			$date       = date("Y-m-d");
			$time       = date("H:i:s", strtotime("-2hours"));
			$startdate  = $date.'%20'.$time;

			$edate      = date("Y-m-d");
			$etime      = date("H:i:s");
			$enddate    = $edate.'%20'.$etime;
			$updatetime = "2020-07-23%2023:59:59";

				for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
					usleep(500);
					// for ($i=0; $i < 1; $i++) {
					$autocheck                 = json_decode($masterdatavehicle[$i]->vehicle_autocheck);
					$isinhauling               = $autocheck->auto_last_hauling;
					$lastcourse 							 = $autocheck->auto_last_course;
					$jalur_namefix 				     = $this->m_securityevidence->get_jalurname($lastcourse);
					// {"":"P","auto_last_update":"2022-05-24 14:24:06","auto_last_check":"2022-05-24 00:00:00","auto_last_position":"EST ROAD  BANJAR KALIMANTAN SELATAN","auto_last_lat":"-3.502445","auto_last_long":"115.593888","auto_last_engine":"ON","auto_last_gpsstatus":"OK","auto_last_speed":"15","auto_last_course":"128","auto_last_road":"muatan","auto_last_hauling":"out","auto_last_rom_name":"ROM A1","auto_last_rom_time":"2022-05-22 08:53:55","auto_last_port_name":"PORT BIB","auto_last_port_time":"2022-05-24 12:44:37","auto_flag":0,"vehicle_gotohistory":0,"auto_change_engine_status":"ON","auto_change_engine_datetime":"2022-05-24 14:23:33","auto_change_position":"KM 19.5,  TANAH BUMBU KALIMANTAN SELATAN","auto_change_coordinate":"-3.57265,115.655123"}"

						if ($isinhauling == "in") {
							print_r(($i+1). " OF " .sizeof($masterdatavehicle). "\r\n");
							print_r("===================================================== \r\n");
							print_r(" Vehicle : " .$masterdatavehicle[$i]->vehicle_no.'-'.$masterdatavehicle[$i]->vehicle_name. "\r\n");
							// $deviceID = "819058546530";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1207
							// $deviceID = "819058611680";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
							// $deviceID = "819058587559";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
							$deviceID = $masterdatavehicle[$i]->vehicle_mv03; //"819058587559" 142045031243;//$masterdatavehicle[$i]->vehicle_mv03; BKA
								if ($deviceID != 0000) {
									$datafromapi = file_get_contents("http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=1000&toMap=1&checkend=0&updatetime=".$updatetime."&language=en");
										if ($datafromapi) {
											$createdurl = "http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=5&toMap=1&checkend=0&updatetime=".$updatetime."&language=en";
											$fromapidecode     = json_decode($datafromapi);
											// print_r(" URL CREATED " .$createdurl. "\r\n");

											if (isset($fromapidecode->alarms) == NULL) {
												print_r(" NO DATA ALARM \r\n");
												$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehicle[$i]->vehicle_device);
												$company   = $this->dashboardmodel->getcompany_idforevidence($masterdatavehicle[$i]->vehicle_company);
												if (sizeof($checkindb) < 1) {
													$todatabase = array(
														"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
														"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
														"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
														"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
														"violation_status" 			 	             => 0,
														"violation_vehicle_companyid" 		 	   => "",
														"violation_vehicle_companyname" 		 	 => "",
														"violation_type" 	 	 	                 => "",
														"violation_type_id" 	 	 	             => "",
														"violation_position" 		 	             => "",
														"violation_jalur" 		 	               => "",
														"violation_fatigue" 		               => "",
														"violation_update" 				             => date("Y-m-d H:i:s")
													);
													$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
														if ($insert) {
															print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														}else {
															print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														}
												}else {
													$todatabase = array(
														"violation_status" 			 	             => 0,
														"violation_vehicle_companyid" 		 	   => "",
														"violation_vehicle_companyname" 		 	 => "",
														"violation_type" 	 	 	                 => "",
														"violation_type_id" 	 	 	             => "",
														"violation_position" 		 	             => "",
														"violation_jalur" 		 	               => "",
														"violation_fatigue" 		               => "",
														"violation_update" 				             => date("Y-m-d H:i:s")
													);
													$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehicle[$i]->vehicle_device, $todatabase);
														if ($update) {
															print_r("DATA UPDATE FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														}else {
															print_r("DATA UPDATE FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														}
												}
											}else {
												print_r(" ===== FATIGUE FOUNDED =====\r\n");
												print_r(" URL CREATED " .$createdurl. "\r\n");
												$alarmcallback     = $fromapidecode->alarms;
												$totaldatacallback = sizeof($alarmcallback);

												// echo "<pre>";
												// var_dump($alarmcallback);die();
												// echo "<pre>";

												for ($j=0; $j < 1; $j++) {
													usleep(500);
													print_r(" ALERT ID : " .$fromapidecode->alarms[$totaldatacallback-1]->atp. "\r\n");
													print_r(" ALERT NAME : " .$fromapidecode->alarms[$totaldatacallback-1]->atpStr. "\r\n");

													// echo "<pre>";
													// var_dump($fromapidecode->alarms);die();
													// echo "<pre>";

													if (in_array($fromapidecode->alarms[$totaldatacallback-1]->atp, $alarmtypefromaster)){
														$position = "";
														$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($fromapidecode->alarms[$totaldatacallback-1]->smlng, $fromapidecode->alarms[$totaldatacallback-1]->smlat, $masterdatavehicle[$i]->vehicle_user_id, "webtracking_gps_temanindobara_live");
														if ($geofence_location) {
															$geofence_type = $geofence_location[0]->geofence_type;
															$geofence_name = $geofence_location[0]->geofence_name;

																if ($geofence_type == "road") {
																	$geofencefix   = $geofence_location[0]->geofence_name;
																	$current_speed = $autocheck->auto_last_speed;
																	if ($jalur_namefix == "kosongan") {
																		$speedlimitforcount = $geofence_location[0]->geofence_speed;
																		$speedlimitforview  = $geofence_location[0]->geofence_speed_alias;
																	}elseif ($jalur_namefix == "muatan") {
																		$speedlimitforcount = $geofence_location[0]->geofence_speed_muatan;
																		$speedlimitforview  = $geofence_location[0]->geofence_speed_muatan_alias;
																	}

																		$positionalert     = $this->gpsmodel->GeoReverse($fromapidecode->alarms[$totaldatacallback-1]->smlat, $fromapidecode->alarms[$totaldatacallback-1]->smlng);
																		if ($positionalert->display_name != "Unknown Location!") {
																			$positionexplode = explode(",", $positionalert->display_name);
																			$position = $positionexplode[0];
																		}else {
																			$position = $positionalert->display_name;
																		}
																}
															}

															print_r(" POSITION : " .$position. "\r\n");
															print_r("JAM DARI API : " . $fromapidecode->alarms[$totaldatacallback-1]->bTimeStr . "\r\n");
															$atpStr = "";
															  if ($fromapidecode->alarms[$totaldatacallback-1]->atp == 618) {
															    $atpStr = "Fatigue Driving Alarm Level One Start";
															  }elseif ($fromapidecode->alarms[$totaldatacallback-1]->atp == 619) {
															    $atpStr = "Fatigue Driving Alarm Level Two Start";
															  }else {
															    $atpStr = $fromapidecode->alarms[$totaldatacallback-1]->atpStr;
															  }

																$jamdariAPI          = $fromapidecode->alarms[$totaldatacallback-1]->bTimeStr;
																$jamdariAPIFIX       = date("Y-m-d H:i:s", strtotime($jamdariAPI . "-1 hours"));
																$jamdariAPIWITA 		 = date("Y-m-d H:i:s", strtotime($jamdariAPI . "+1 hours"));
																$jamserverwib        = date("Y-m-d H:i:s", strtotime("-1 hours"));
																$jamserverasli       = date("Y-m-d H:i:s");



																$d1               = strtotime($jamdariAPI);
																$d2               = strtotime($jamserverwib);
																$totalSecondsDiff = $d1-$d2;
																$totalMinutesDiff = round($totalSecondsDiff/60);
																$totalHoursDiff   = round($totalSecondsDiff/60/60);
																$totalDaysDiff    = round($totalSecondsDiff/60/60/24);

																if ($jamdariAPI > $jamserverwib) {
																	if ($totalMinutesDiff < 60) {
																	print_r("=====MASUK SIKON=====". "\r\n");
																	print_r("JAM DARI API : " . $jamdariAPI . "\r\n");
																	print_r("JAM SERVER ASLI : " . date("Y-m-d H:i:s") . "\r\n");
																	print_r("JAM SERVER WIB : " . $jamserverwib . "\r\n");
																	print_r("SELISIH : " . $totalMinutesDiff . "\r\n");

																	// print_r("JAM DARI API FIX : " . $jamdariAPIFIX . "\r\n");
																	// print_r("JAM SERVER WIB : " . $jamserverwib . "\r\n");
																	// print_r("JAM API WITA : " . $jamdariAPIWITA . "\r\n");
																	print_r("=============================================". "\r\n");

																	// echo "<pre>";
																	// var_dump("======================================");
																	// echo "<pre>";
																	}
																}
																// else {
																// 	print_r("JAM SERVER ASLI : " . date("Y-m-d H:i:s") . "\r\n");
																// 	print_r("JAM SERVER WIB : " . $jamserverwib . "\r\n");
																// 	print_r("JAM DARI API : " . $jamdariAPI . "\r\n");
																// 	// print_r("JAM DARI API FIX : " . $jamdariAPIFIX . "\r\n");
																// 	// print_r("JAM API WITA : " . $jamdariAPIWITA . "\r\n");
																// }

																// echo "<pre>";
																// var_dump("DONE");die();
																// echo "<pre>";




														// array_push($datafix, array(
														// 	"isfatigue" 		     => "yes",
														// 	"vehicle_user_id"    => $masterdatavehicle[$i]->vehicle_user_id,
														// 	"vehicle_no"         => $masterdatavehicle[$i]->vehicle_no,
														// 	"vehicle_name"       => $masterdatavehicle[$i]->vehicle_name,
														// 	"vehicle_company"    => $masterdatavehicle[$i]->vehicle_company,
														// 	"vehicle_device"     => $masterdatavehicle[$i]->vehicle_device,
														// 	"vehicle_mv03"       => $masterdatavehicle[$i]->vehicle_mv03,
														// 	"gps_alertid"        => $fromapidecode->alarms[$totaldatacallback-1]->atp,
														// 	"gps_alert"          => $atpStr,
														// 	"gps_time"           => $fromapidecode->alarms[$totaldatacallback-1]->bTimeStr,
														// 	"gps_latitude_real"  => $fromapidecode->alarms[$totaldatacallback-1]->smlat,
														// 	"gps_longitude_real" => $fromapidecode->alarms[$totaldatacallback-1]->smlng,
														// 	"position"           => $position,
														// 	"gps_speed"          => ($fromapidecode->alarms[$totaldatacallback-1]->ssp)/10,
														// ));
														//
														// echo "<pre>";
														// var_dump($datafix);die();
														// echo "<pre>";
														//
														// $checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehicle[$i]->vehicle_device);
														// $company   = $this->dashboardmodel->getcompany_idforevidence($masterdatavehicle[$i]->vehicle_company);
														//
														// if (sizeof($checkindb) < 1) {
														// 	$todatabase = array(
														// 		"violation_vehicle_no"                 => $masterdatavehicle[$i]->vehicle_no,
														// 		"violation_vehicle_name"               => $masterdatavehicle[$i]->vehicle_name,
														// 		"violation_vehicle_device"             => $masterdatavehicle[$i]->vehicle_device,
														// 		"violation_vehicle_mv03" 	             => $masterdatavehicle[$i]->vehicle_mv03,
														// 		"violation_status" 			 	             => 1,
														// 		"violation_vehicle_companyid" 		 	   => $company->company_id,
														// 		"violation_vehicle_companyname" 		 	 => $company->company_name,
														// 		"violation_type" 	 	 	                 => "fatigue",
														// 		"violation_type_id" 	 	 	             => $datafix[0]['gps_alertid'],
														// 		"violation_position" 		 	             => $position,
														// 		"violation_jalur" 		 	               => $jalur_namefix,
														// 		"violation_fatigue" 		               => json_encode($datafix),
														// 		"violation_update" 				             => date("Y-m-d H:i:s", strtotime($datafix[0]['gps_time'])) //date("Y-m-d H:i:s")
														// 	);
														// 	$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
														// 		if ($insert) {
														// 			print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														// 		}else {
														// 			print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														// 		}
														// }else {
														// 	$todatabase = array(
														// 		"violation_status" 			 	             => 1,
														// 		"violation_vehicle_companyid" 		 	   => $company->company_id,
														// 		"violation_vehicle_companyname" 		 	 => $company->company_name,
														// 		"violation_type" 	 	 	                 => "fatigue",
														// 		"violation_type_id" 	 	 	             => $datafix[0]['gps_alertid'],
														// 		"violation_position" 		 	             => $position,
														// 		"violation_jalur" 		 	               => $jalur_namefix,
														// 		"violation_fatigue" 		               => json_encode($datafix),
														// 		"violation_update" 				             => date("Y-m-d H:i:s", strtotime($datafix[0]['gps_time'])) //date("Y-m-d H:i:s")
														// 	);
														// 	$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehicle[$i]->vehicle_device, $todatabase);
														// 		if ($update) {
														// 			print_r("DATA UPDATE FATIGUE SUCCESS : ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														// 		}else {
														// 			print_r("DATA UPDATE FATIGUE FAILED: ". $masterdatavehicle[$i]->vehicle_no.' - '.$masterdatavehicle[$i]->vehicle_name . "\r\n");
														// 		}
														// }
														// KOSONGKAN ARRAY
														$datafix = array();
													}
												}
											}
											print_r("===================================================== \r\n");
										}
								}
						}
				}
				print_r("CRON START : ". $cronstartdate . "\r\n");
				print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
				$message =  urlencode(
							"VIOLATION MONITORING CAM \n".
							"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
							"Start: ".$cronstartdate." \n".
							"Finish: ".date("Y-m-d H:i:s")." \n"
							);

				$sendtelegram = $this->telegram_direct("-742300146",$message);
				printf("===SENT TELEGRAM OK\r\n");
		}

		function checkdsmadasison($userid){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			$nowtime_wita  = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($cronstartdate)));
			print_r("CRON START           : ". $nowtime_wita . "\r\n");

			$m1        = date("F", strtotime($cronstartdate));
			$year      = date("Y", strtotime($cronstartdate));
			$dbtable   = "";
			$report    = "report_device_status_";

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

			$company             = $this->getAllCompanycheckmdvr($userid);
			$totalcompany        = count($company);
			printf("===TOTAL COMPANY %s \r\n", $totalcompany);

			// LOGIN API
			$username                         = "DEMOPOC";
			$password                         = "000000";
			// $loginbaru                        = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
			$loginbaru                        = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);

			// echo "<pre>";
			// var_dump($loginbaru);die();
			// echo "<pre>";

			$loginbarudecode                  = json_decode($loginbaru);
			$jsession                         = $loginbarudecode->jsession;

			$datavehicleonline                = array();
			$total_eng_on_plus_status_offline = 0;

			for ($i=0; $i < sizeof($company); $i++) {
				usleep(500);
				// for ($i=0; $i < 1; $i++) {
				$company_id          = $company[$i]->company_id; //1947; //$company[$i]->company_id;
				$masterdatavehicle   = $this->getAllVehicleByVCompany($company_id, $userid);
					if (sizeof($masterdatavehicle) > 0) {
						$dataforsenttelegram = array();
						$text_info           = "";
							for ($j=0; $j < sizeof($masterdatavehicle); $j++) {
								usleep(500);
								$vehiclemv03       = $masterdatavehicle[$j]->vehicle_mv03;
								$vehicle_no        = $masterdatavehicle[$j]->vehicle_no;
								$vehicle_name      = $masterdatavehicle[$j]->vehicle_name;
								$vehicle_company   = $masterdatavehicle[$j]->vehicle_company;
								$vehicle_device    = $masterdatavehicle[$j]->vehicle_device;

								if ($company_id == $vehicle_company) {
									if ($vehiclemv03 != 0000) {
											// $dataonline = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceStatus.action?jsession=".$jsession."&devIdno=".$vehiclemv03."&toMap=1&driver=0&language=en");
											$dataonline = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceStatus.action?jsession=".$jsession."&devIdno=".$vehiclemv03."&toMap=1&driver=0&language=en");
												if ($dataonline) {
													$datadecode = json_decode($dataonline);

														if (isset($datadecode->result)) {
															if ($datadecode->result == 0) {
																$vehicle_autocheck = json_decode($masterdatavehicle[$j]->vehicle_autocheck);
																$auto_last_engine  = $vehicle_autocheck->auto_last_engine;
																$auto_last_update  = $vehicle_autocheck->auto_last_update;

																// echo "<pre>";
																// var_dump($vehicle_autocheck);die();
																// echo "<pre>";

																		// print_r("JSESSION : ". $jsession . "\r\n");
																		$speedfix = ($datadecode->status[0]->sp)/10;
																		$onlinestatus = $datadecode->status[0]->ol;
																			if ($onlinestatus == 1) {
																				$onlinestatus = "online";
																			}else {
																				$onlinestatus = "offline";
																			}
																		$storage_type = $datadecode->status[0]->dt;
																			if ($storage_type == 1) {
																				$storage_type = "SD Card";
																			}elseif ($storage_type == 2) {
																				$storage_type = "HDD";
																			}elseif ($storage_type == 3) {
																				$storage_type = "SSD";
																			}

																		$networkfix = $datadecode->status[0]->net;
																			if ($networkfix == 0) {
																				$networkfix = "3G";
																			}elseif ($networkfix == 1) {
																				$networkfix = "WIFI";
																			}elseif ($networkfix == 2) {
																				$networkfix = "WIRED";
																			}elseif ($networkfix == 3) {
																				$networkfix = "4G";
																			}elseif ($networkfix == 4) {
																				$networkfix = "5G";
																			}

																			// if ($auto_last_engine == "ON" && $onlinestatus == "offline") {
																		// 		// if ($vehicle_no == "BKA 1280") {
																			printf("================================= \r\n");
																			// print_r("TOTAL : ". ($total_eng_on_plus_status_offline += 1) ."\r\n");
																			print_r($j . " From (". sizeof($masterdatavehicle) .")\r\n");
																			print_r("VEHICLE MV03 : ". $vehiclemv03 . "\r\n");
																			printf("== LAST UPDATE GPS : " . $auto_last_update . " \r\n");
																			printf("== LAST UPDATE MDVR : " . $datadecode->status[0]->gt . " \r\n");
																			printf("== STATUS NAME : " . $onlinestatus . " \r\n");
																			printf("== LAST ENGINE : " . $auto_last_engine . " \r\n");
																			printf("== VEHICLE NO : " . $vehicle_no . " \r\n");
																			printf("== VEHICLE NAME : " . $vehicle_name . " \r\n");
																			printf("== NETWORK TYPE : " . $networkfix . " \r\n");
																			printf("== S1 : " . $datadecode->status[0]->s1 . " \r\n");
																			printf("== S2 : " . $datadecode->status[0]->s2 . " \r\n");
																			printf("== S3 : " . $datadecode->status[0]->s3 . " \r\n");
																			printf("== S4 : " . $datadecode->status[0]->s4 . " \r\n");
																		//
																			// echo "<pre>";
																			// var_dump("DONE");die();
																			// echo "<pre>";
																		// }

																			if ($auto_last_engine == "ON" && $onlinestatus == "offline") {
																				$datavehicleonline = array(
																					"devicestatus_last_updategps"         => $auto_last_update,
																					"devicestatus_last_updatemdvr"        => $datadecode->status[0]->gt,
																					"devicestatus_name"                   => $onlinestatus,
																					"devicestatus_last_engine"            => $auto_last_engine,
																					"devicestatus_vehicle_no"             => $vehicle_no,
																					"devicestatus_vehicle_name"           => $vehicle_name,
																					"devicestatus_vehicle_vehicle_device" => $vehicle_device,
																					"devicestatus_vehicle_company"        => $vehicle_company,
																					"devicestatus_mv03"                   => $vehiclemv03,
																					"devicestatus_speed"                  => $speedfix,
																					"devicestatus_network_type"           => $networkfix,
																					"devicestatus_server_number"          => $datadecode->status[0]->gw,
																					"devicestatus_submited_date"          => date("Y-m-d H:i:s")
																				);

																				$this->dbtensor = $this->load->database("tensor_report", true);
																				$insertNow = $this->dbtensor->insert($dbtable, $datavehicleonline);
																					if ($insertNow) {
																							printf("==SUCCESS INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");

																							$info = $vehicle_no." - "."Engine : ".$auto_last_engine.", CAM : ".$onlinestatus." \n";

																							array_push($dataforsenttelegram,$info);
																							printf("================================= \r\n");
																					}else {
																						printf("==FAILED INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
																						printf("================================= \r\n");
																					}
																			}else {
																				$datavehicleonline = array(
																					"devicestatus_last_updategps"         => $auto_last_update,
																					"devicestatus_last_updatemdvr"        => $datadecode->status[0]->gt,
																					"devicestatus_name"                   => $onlinestatus,
																					"devicestatus_last_engine"            => $auto_last_engine,
																					"devicestatus_vehicle_no"             => $vehicle_no,
																					"devicestatus_vehicle_name"           => $vehicle_name,
																					"devicestatus_vehicle_vehicle_device" => $vehicle_device,
																					"devicestatus_vehicle_company"        => $vehicle_company,
																					"devicestatus_mv03"                   => $vehiclemv03,
																					"devicestatus_speed"                  => $speedfix,
																					"devicestatus_network_type"           => $networkfix,
																					"devicestatus_server_number"          => $datadecode->status[0]->gw,
																					"devicestatus_submited_date"          => date("Y-m-d H:i:s")
																				);

																				$this->dbtensor = $this->load->database("tensor_report", true);
																				$insertNow = $this->dbtensor->insert($dbtable, $datavehicleonline);
																					if ($insertNow) {
																						printf("==SUCCESS INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
																						printf("================================= \r\n");
																					}else {
																						printf("==FAILED INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
																						printf("================================= \r\n");
																					}
															}
														}
												}
										}
									}
								}
							}
							// FOR SENT TELEGRAM
							if (count($dataforsenttelegram) > 0) {
								for ($k=0;$k<count($dataforsenttelegram);$k++)
								{
									$no_urut = $k+1;
									$text_info .= $no_urut.". ".$dataforsenttelegram[$k];

								}

								$total_offline = count($dataforsenttelegram);

								//send telegram
								$title_name = "CONTRACTOR ".$company[$i]->company_name;
									$message = urlencode(
											"".$title_name." \n".
											"TANGGAL INFORMASI: ".$cronstartdate." WITA"." \n".
											"DATA MDVR OFFLINE: \n".$text_info." \n".
											"TOTAL MDVR OFFLINE: ".$total_offline." \n"
										);

										// echo "<pre>";
										// var_dump($message);die();
										// echo "<pre>";

								sleep(2);
								// $sendtelegram = $this->telegram_directcheckmdvr("-661635963",$message); //MDVR STATUS
								$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
								printf("===SENT TELEGRAM OK\r\n");
							}
					}
			}

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"CRON CHECK MDVR STATUS (POC BC) \n".
						"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						// "Total Data Baru: ".$totaldata_baru." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			// $sendtelegram = $this->telegram_directcheckmdvr("-577190673",$message); // FMS VIOLATION CRON
			$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
			printf("===SENT TELEGRAM OK\r\n");
		}

		function testingsenttelewithip(){
			$message =  urlencode(
						"TESTING SEN TELE WITH IP \n".
						// "Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						// "Total Data Baru: ".$totaldata_baru." \n".
						// "Start: ".$cronstartdate." \n".
						// "Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: Terima Kasih \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			$sendtelegram = $this->telegram_directcheckmdvr("-577190673",$message); // FMS VIOLATION CRON
			printf("===SENT TELEGRAM OK\r\n");
		}

		function checkdsmadasisontesting($userid){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			$nowtime_wita  = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($cronstartdate)));
			print_r("CRON START           : ". $nowtime_wita . "\r\n");

			$m1        = date("F", strtotime($cronstartdate));
			$year      = date("Y", strtotime($cronstartdate));
			$dbtable   = "";
			$report    = "report_device_status_";

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

			$company             = $this->getAllCompany($userid);
			$totalcompany        = count($company);
			printf("===TOTAL COMPANY %s \r\n", $totalcompany);

			// LOGIN API
			$username                         = "DEMOPOC";
			$password                         = "000000";
			// $loginbaru                        = file_get_contents("http://172.16.1.2/808gps/StandardApiAction_login.action?account=".$username."&password=".$password);
			$loginbaru                        = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
			// $loginbaru 												= $this->loginforcheckdsmadasison($username, $password);
			$loginbarudecode                  = json_decode($loginbaru);
			$jsession                         = $loginbarudecode->jsession;

			// echo "<pre>";
			// var_dump($loginbaru);die();
			// echo "<pre>";

			$datavehicleonline                = array();
			$total_eng_on_plus_status_offline = 0;

			// for ($i=0; $i < sizeof($company); $i++) {
				for ($i=0; $i < 1; $i++) {
				$company_id          = 1945; //$company[$i]->company_id;
				$masterdatavehicle   = $this->getAllVehicleByVCompanytesting($company_id, $userid);
				// echo "<pre>";
				// var_dump($masterdatavehicle);die();
				// echo "<pre>";
					if (sizeof($masterdatavehicle) > 0) {
						$dataforsenttelegram = array();
						$text_info           = "";
							for ($j=0; $j < sizeof($masterdatavehicle); $j++) {
								usleep(500);
								$vehiclemv03       = $masterdatavehicle[$j]->vehicle_mv03;
								$vehicle_no        = $masterdatavehicle[$j]->vehicle_no;
								$vehicle_name      = $masterdatavehicle[$j]->vehicle_name;
								$vehicle_company   = $masterdatavehicle[$j]->vehicle_company;
								$vehicle_device    = $masterdatavehicle[$j]->vehicle_device;
								printf("===JSESSION " . $jsession . "\r\n");
								printf("===VEHICLE NO " . $vehicle_no . "\r\n");
								printf("===VEHICLE MV03 " . $vehiclemv03 . "\r\n");

									if ($vehiclemv03 != 0000) {
											$dataonline = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceStatus.action?jsession=".$jsession."&devIdno=".$vehiclemv03."&toMap=1&driver=0&language=en");
												if ($dataonline) {
													$datadecode = json_decode($dataonline);

														if (isset($datadecode->result)) {
															if ($datadecode->result == 0) {
																$vehicle_autocheck = json_decode($masterdatavehicle[$j]->vehicle_autocheck);
																$auto_last_engine  = $vehicle_autocheck->auto_last_engine;
																$auto_last_update  = $vehicle_autocheck->auto_last_update;

																// echo "<pre>";
																// var_dump($vehicle_autocheck);die();
																// echo "<pre>";

																		// print_r("JSESSION : ". $jsession . "\r\n");
																		$speedfix = ($datadecode->status[0]->sp)/10;
																		$onlinestatus = $datadecode->status[0]->ol;
																			if ($onlinestatus == 1) {
																				$onlinestatus = "online";
																			}else {
																				$onlinestatus = "offline";
																			}
																		$storage_type = $datadecode->status[0]->dt;
																			if ($storage_type == 1) {
																				$storage_type = "SD Card";
																			}elseif ($storage_type == 2) {
																				$storage_type = "HDD";
																			}elseif ($storage_type == 3) {
																				$storage_type = "SSD";
																			}

																		$networkfix = $datadecode->status[0]->net;
																			if ($networkfix == 0) {
																				$networkfix = "3G";
																			}elseif ($networkfix == 1) {
																				$networkfix = "WIFI";
																			}elseif ($networkfix == 2) {
																				$networkfix = "WIRED";
																			}elseif ($networkfix == 3) {
																				$networkfix = "4G";
																			}elseif ($networkfix == 4) {
																				$networkfix = "5G";
																			}

																			// if ($auto_last_engine == "ON" && $onlinestatus == "offline") {
																		// 		// if ($vehicle_no == "BKA 1280") {
																			printf("================================= \r\n");
																			// print_r("TOTAL : ". ($total_eng_on_plus_status_offline += 1) ."\r\n");
																			print_r($j . " From (". sizeof($masterdatavehicle) .")\r\n");
																			print_r("VEHICLE MV03 : ". $vehiclemv03 . "\r\n");
																			printf("== LAST UPDATE GPS : " . $auto_last_update . " \r\n");
																			printf("== LAST UPDATE MDVR : " . $datadecode->status[0]->gt . " \r\n");
																			printf("== STATUS NAME : " . $onlinestatus . " \r\n");
																			printf("== LAST ENGINE : " . $auto_last_engine . " \r\n");
																			printf("== VEHICLE NO : " . $vehicle_no . " \r\n");
																			printf("== VEHICLE NAME : " . $vehicle_name . " \r\n");
																			printf("== NETWORK TYPE : " . $networkfix . " \r\n");
																			printf("== S1 : " . $datadecode->status[0]->s1 . " \r\n");
																			printf("== S2 : " . $datadecode->status[0]->s2 . " \r\n");
																			printf("== S3 : " . $datadecode->status[0]->s3 . " \r\n");
																			printf("== S4 : " . $datadecode->status[0]->s4 . " \r\n");
																		//
																			// echo "<pre>";
																			// var_dump("DONE");die();
																			// echo "<pre>";
																		// }

															// 				if ($auto_last_engine == "ON" && $onlinestatus == "offline") {
															// 					$datavehicleonline = array(
															// 						"devicestatus_last_updategps"         => $auto_last_update,
															// 						"devicestatus_last_updatemdvr"        => $datadecode->status[0]->gt,
															// 						"devicestatus_name"                   => $onlinestatus,
															// 						"devicestatus_last_engine"            => $auto_last_engine,
															// 						"devicestatus_vehicle_no"             => $vehicle_no,
															// 						"devicestatus_vehicle_name"           => $vehicle_name,
															// 						"devicestatus_vehicle_vehicle_device" => $vehicle_device,
															// 						"devicestatus_vehicle_company"        => $vehicle_company,
															// 						"devicestatus_mv03"                   => $vehiclemv03,
															// 						"devicestatus_speed"                  => $speedfix,
															// 						"devicestatus_network_type"           => $networkfix,
															// 						"devicestatus_server_number"          => $datadecode->status[0]->gw,
															// 						"devicestatus_submited_date"          => date("Y-m-d H:i:s")
															// 					);
															//
															// 					$this->dbtensor = $this->load->database("tensor_report", true);
															// 					$insertNow = $this->dbtensor->insert($dbtable, $datavehicleonline);
															// 						if ($insertNow) {
															// 								printf("==SUCCESS INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
															//
															// 								$info = $vehicle_no." - "."Engine : ".$auto_last_engine.", CAM : ".$onlinestatus." \n";
															//
															// 								array_push($dataforsenttelegram,$info);
															// 								printf("================================= \r\n");
															// 						}else {
															// 							printf("==FAILED INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
															// 							printf("================================= \r\n");
															// 						}
															// 				}else {
															// 					$datavehicleonline = array(
															// 						"devicestatus_last_updategps"         => $auto_last_update,
															// 						"devicestatus_last_updatemdvr"        => $datadecode->status[0]->gt,
															// 						"devicestatus_name"                   => $onlinestatus,
															// 						"devicestatus_last_engine"            => $auto_last_engine,
															// 						"devicestatus_vehicle_no"             => $vehicle_no,
															// 						"devicestatus_vehicle_name"           => $vehicle_name,
															// 						"devicestatus_vehicle_vehicle_device" => $vehicle_device,
															// 						"devicestatus_vehicle_company"        => $vehicle_company,
															// 						"devicestatus_mv03"                   => $vehiclemv03,
															// 						"devicestatus_speed"                  => $speedfix,
															// 						"devicestatus_network_type"           => $networkfix,
															// 						"devicestatus_server_number"          => $datadecode->status[0]->gw,
															// 						"devicestatus_submited_date"          => date("Y-m-d H:i:s")
															// 					);
															//
															// 					$this->dbtensor = $this->load->database("tensor_report", true);
															// 					$insertNow = $this->dbtensor->insert($dbtable, $datavehicleonline);
															// 						if ($insertNow) {
															// 							printf("==SUCCESS INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
															// 							printf("================================= \r\n");
															// 						}else {
															// 							printf("==FAILED INSERT DATA ENGINE ".$auto_last_engine." CAM ".$onlinestatus." \r\n");
															// 							printf("================================= \r\n");
															// 						}
															// }
														}
												}
										}
									}
							}
							// FOR SENT TELEGRAM
							// if (count($dataforsenttelegram) > 0) {
							// 	for ($k=0;$k<count($dataforsenttelegram);$k++)
							// 	{
							// 		$no_urut = $k+1;
							// 		$text_info .= $no_urut.". ".$dataforsenttelegram[$k];
							//
							// 	}
							//
							// 	$total_offline = count($dataforsenttelegram);
							//
							// 	//send telegram
							// 	$title_name = "CONTRACTOR ".$company[$i]->company_name;
							// 		$message = urlencode(
							// 				"".$title_name." \n".
							// 				"TANGGAL INFORMASI: ".$cronstartdate." WITA"." \n".
							// 				"DATA MDVR OFFLINE: \n".$text_info." \n".
							// 				"TOTAL MDVR OFFLINE: ".$total_offline." \n"
							// 			);
							//
							// 			// echo "<pre>";
							// 			// var_dump($message);die();
							// 			// echo "<pre>";
							//
							// 	sleep(2);
							// 	$sendtelegram = $this->telegram_direct("-661635963",$message); //MDVR STATUS
							// 	//$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
							// 	printf("===SENT TELEGRAM OK\r\n");
							// }
					}
			}

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			// $finishtime   = date("Y-m-d H:i:s");
			// $start_1      = dbmaketime($cronstartdate);
			// $end_1        = dbmaketime($finishtime);
			// $duration_sec = $end_1 - $start_1;
			// $message =  urlencode(
			// 			"CRON CHECK MDVR STATUS \n".
			// 			"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
			// 			// "Total Data Baru: ".$totaldata_baru." \n".
			// 			"Start: ".$cronstartdate." \n".
			// 			"Finish: ".date("Y-m-d H:i:s")." \n".
			// 			"Latency: ".$duration_sec." s"." \n"
			// 			);
			//
			// // $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			// $sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			// printf("===SENT TELEGRAM OK\r\n");
		}

		function violationtextmode($userid){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON VIOLATION TEXT MODE START WITA           : ". $nowtime_wita . "\r\n");
			print_r("CRON VIOLATION TEXT MODE START WIB          : ". $cronstartdate . "\r\n");

			// CHOOSE DBTABLE
			$current_date = date("Y-m-d H:i:s", strtotime("+1 Hour"));
			$m1           = date("F", strtotime($current_date));
			$year         = date("Y", strtotime($current_date));
			$dbtable      = "";
			$report       = "historikal_violation_";

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

			$street_onduty             = $this->config->item("street_onduty_autocheck");

			$alarmtypefromaster            = array();
			$dataoverspeed 								 = array();
			$datafatigue                   = array();
			$dataKmMuatanFix               = array();
			$dataKmKosonganFix             = array();
			$violationmix                  = array();
			$sdate                         = date("Y-m-d H:i:s", strtotime("-4 minutes", strtotime($nowtime_wita)));
			// $sdate                         = date("Y-m-d H:i:s", strtotime("-12 Hour", strtotime($nowtime_wita)));
			$edate 												 = $nowtime_wita;
			print_r("==STARTDATE YANG DICARI : ". $sdate . "\r\n");

			// $wa_token = $this->getWAToken($userid);
			// printf("===GET TOKEN WA : %s \n", $wa_token->sess_value);

			// echo "<pre>";
			// var_dump($sdate.'-'.$edate);die();
			// echo "<pre>";

			$totaltelesend = 0;

			$masterviolation = $this->m_violation->getviolationforcrontele($dbtable, $sdate, $edate);

			// echo "<pre>";
			// var_dump($masterviolation);die();
			// echo "<pre>";

			$master_tele_violataion_mitra = array(
				"1834" => "-677846513",
				"1835" => "-623474283",
				"1837" => "-603940790",
				"1839" => "-1001531051791",
				"1926" => "-682379686",
				"1945" => "-770153853",
				"1946" => "-739893793",
				"1947" => "-676926970",
				"1948" => "-1001615972607",
				"1959" => "-516795045",
			);

			$array_unit_LMO = array("CT-182", "FDT-261", "FDT-740");

			for ($i=0; $i < sizeof($masterviolation); $i++) {
				// if (in_array($masterviolation[$i]['violation_position'], $street_onduty)) {

					// if(count($telegram_group_dt)>0){
					// 	$telegram_group = $telegram_group_dt->company_telegram_speed;
					// 	// $telegram_group = "-495868829"; //testing anything
					// }else{
					// 	$telegram_group = "-495868829"; //testing anything
					// }

					$company_id     = $masterviolation[$i]['violation_vehicle_companyid'];
					$telegram_group = $master_tele_violataion_mitra[$company_id];
					printf("===CONTRACTOR ID : ".$company_id."\r\n");
					printf("===CONTRACTOR NAME : ".$masterviolation[$i]['violation_vehicle_companyname']."\r\n");
					printf("===TELEGRAM GROUP BELUM DIPAKAI : ".$telegram_group."\r\n");
					$data_json = json_decode($masterviolation[$i]['violation_fatigue'], true);
					$gps_time   = $data_json[0]['gps_time'];

					// print_r($masterviolation);exit();

						$alarmreportnamefix = "";
						$alarmreporttype = $data_json[0]['gps_alertid'];

							// if ($alarmreporttype == 624) {
							// 	$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							// }elseif ($alarmreporttype == 625) {
							// 	$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							// }

							if ($alarmreporttype == 626) {
								$alarmreportnamefix = "DRIVER UNDETECTED ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 627) {
								$alarmreportnamefix = "DRIVER UNDETECTED ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 622) {
								$alarmreportnamefix = "SMOKING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 623) {
								$alarmreportnamefix = "SMOKING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 604) {
								$alarmreportnamefix = "CAR DISTANCE NEAR ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 605) {
								$alarmreportnamefix = "CAR DISTANCE NEAR ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 618) {
								$alarmreportnamefix = "FATIGUE DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 619) {
								$alarmreportnamefix = "FATIGUE DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 620) {
								$alarmreportnamefix = "CALL TO CALL THE ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 621) {
								$alarmreportnamefix = "CALL TO CALL THE ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 633) {
								$alarmreportnamefix = "REAR APPROACH ALARM START";
							}elseif ($alarmreporttype == 683) {
								$alarmreportnamefix = "REAR APPROACH ALARM END";
							}elseif ($alarmreporttype == 710) {
								$alarmreportnamefix = "HANDS OFF WHEEL LEVEL ONE";
							}elseif ($alarmreporttype == 711) {
								$alarmreportnamefix = "HANDS OFF WHEEL LEVEL TWO";
							}elseif ($alarmreporttype == 706) {
								$alarmreportnamefix = "UNFASTENED SEAT BELT LEVEL ONE";
							}elseif ($alarmreporttype == 707) {
								$alarmreportnamefix = "UNFASTENED SEAT BELT LEVEL TWO";
							}elseif ($alarmreporttype == 702) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 703) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 752) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 753) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}

							// else {
							// 	$alarmreportnamefix = $data_json[0]['gps_alert'];
							// }

						$coordinate = $data_json[0]['gps_latitude_real'].",".$data_json[0]['gps_longitude_real'];
						$url        = "https://www.google.com/maps/search/?api=1&query=".$coordinate;

						$message = urlencode(
							"".$alarmreportnamefix." (text mode)\n".
							"Time: ".$data_json[0]['gps_time']." \n".
							"Vehicle No: ".$masterviolation[$i]['violation_vehicle_no']." ".$masterviolation[$i]['violation_vehicle_name']." \n".
							"Driver: "." \n".
							"Position: ".$masterviolation[$i]['violation_position']." \n".
							"Speed (kph): ".$data_json[0]['gps_speed']." \n".
							"Coordinate: ".$url." \n"
							);

							sleep(2);
							// $sendtelegram = $this->telegram_direct($telegram_group,$message); //FMS MITRA HAULING // AKTIFKAN SAAT LIVE
							// $sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
								// if (in_array($masterviolation[$i]['violation_vehicle_no'], $array_unit_LMO)) {
								// 	$sendtelegram = $this->telegram_direct("-953824895",$message); //POC LMO
								// }
							sleep(2);
							$sendtelegram = $this->telegram_direct("-890634600",$message); //FMS POC BERAU COAL NOTIFICATION
							printf("===SENT TELEGRAM GROUP FMS TESTING OK\r\n");
							$totaltelesend += 1;
							$violation_id = $masterviolation[$i]['violation_id'];
								$update_status_tele = array(
									"violation_status_tele" => 1
								);
								 // AKTIFKAN SAAT LIVE DIBAWAH INI
								$update_tele = $this->m_violation->updateTeleStatusHistorikal($dbtable, $violation_id, $update_status_tele);
								if ($update_tele) {
									printf("===STATUS TELE BERHASIL DIUPDATE\r\n");
								}else {
									printf("===STATUS TELE GAGAL DIUPDATE\r\n");
								}

								// NOTIF WHATSAPP START
								// $data_for_watoken = array(
								// 	"wa_token"     => $wa_token,
								// 	"title_name"   => $alarmreportnamefix,
								// 	"gps_time"     => $data_json[0]['gps_time'],
								// 	"vehicle_no"   => $masterviolation[$i]['violation_vehicle_no']." ".$masterviolation[$i]['violation_vehicle_name'],
								// 	"driver" 		   => "",
								// 	"position" 	   => $masterviolation[$i]['violation_position'],
								// 	"coordinate"   => $url,
								// 	"speed" 		   => $data_json[0]['gps_speed'],
								// 	"data_company" => $telegram_group_dt
								// );

								// $notif_wa_ovspeed = $this->sendnotif_wa_mdvr($data_for_watoken, "mitra");
								// NOTIF WHATSAPP END

								// SENT TO BIB VIOLATION
								$title_name = strtoupper($alarmreportnamefix);
								$alert_level = "";

								$search_level_2 = 'TWO';
								if(preg_match("/{$search_level_2}/i", $title_name)) {
									$alert_level = "2";
								}

								// if($alert_level == "2"){
									// if ($alarmreporttype == 618 || $alarmreporttype == 619 || $alarmreporttype == 620 || $alarmreporttype == 621) {
									// 	sleep(2);
										// $sendtelegram = $this->telegram_direct("-542787721",$message); //telegram BIB VIOLATION  // AKTIFKAN SAAT LIVE
										// printf("===SENT TELEGRAM BIB OK\r\n");
										// $notif_wa_ovspeed = $this->sendnotif_wa_mdvr($data_for_watoken, "bib");
										// printf("===SENT WHATSAPP NOTIF BIB OK\r\n");
									// }
								// }
				// }
			}

			print_r("==STARTDATE PENCARIAN : ". $sdate . "\r\n");
			print_r("==CRON START : ". $nowtime_wita . "\r\n");
			print_r("==CRON FINISH : ". date("Y-m-d H:i:s", strtotime("+1 Hour")) . " \r\n");
			$finishtime   = date("Y-m-d H:i:s", strtotime("+1 Hour"));
			$start_1      = dbmaketime($nowtime_wita);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"VIOLATION MONITORING TEXT MODE (POC BC) \n".
						"Total Send Alert: ".$totaltelesend." \n".
						"Startdate Pencarian: ".$sdate." \n".
						"Start: ".$nowtime_wita." \n".
						"Finish: ".date("Y-m-d H:i:s", strtotime("+1 Hour"))." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			// $sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
			printf("\r\n===SENT TELEGRAM GROUP FMS AUTOCHECK OK\r\n");
		}

		function violationtextmode_oldwithoutwhatsapp($userid){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON VIOLATION TEXT MODE START WITA           : ". $nowtime_wita . "\r\n");
			print_r("CRON VIOLATION TEXT MODE START WIB          : ". $cronstartdate . "\r\n");

			// CHOOSE DBTABLE
			$current_date = date("Y-m-d H:i:s", strtotime("+1 Hour"));
			$m1           = date("F", strtotime($current_date));
			$year         = date("Y", strtotime($current_date));
			$dbtable      = "";
			$report       = "historikal_violation_";

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

			$street_onduty             = $this->config->item("street_onduty_autocheck");

			$alarmtypefromaster            = array();
			$dataoverspeed 								 = array();
			$datafatigue                   = array();
			$dataKmMuatanFix               = array();
			$dataKmKosonganFix             = array();
			$violationmix                  = array();
			// $title_name 									 = "FATIGUE TEXT MODE VERSION";
			$sdate                         = date("Y-m-d H:i:s", strtotime("-4 minutes", strtotime($nowtime_wita)));
			$edate 												 = $nowtime_wita;
			print_r("==STARTDATE YANG DICARI : ". $sdate . "\r\n");
			// $violationmaster               = $this->m_violation->getviolationmaster();
			//
			// for ($j=0; $j < sizeof($violationmaster); $j++) {
			// 	$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
			// 	for ($k=0; $k < sizeof($alarmbymaster); $k++) {
			// 		$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
			// 	}
			// }

			// CONVERT ARRAY ALARM KE STRING
			// $alarmtypefix = implode (",", $alarmtypefromaster);

			$totaltelesend = 0;

			$masterviolation = $this->m_violation->getviolationforcrontele($dbtable, $sdate, $edate);

			$master_tele_violataion_mitra = array(
				"1834" => "-677846513",
				"1835" => "-623474283",
				"1837" => "-603940790",
				"1839" => "-1001531051791",
				"1926" => "-682379686",
				"1945" => "-770153853",
				"1946" => "-739893793",
				"1947" => "-676926970",
				"1948" => "-1001615972607",
				"1959" => "-516795045",
			);

			for ($i=0; $i < sizeof($masterviolation); $i++) {
				if (in_array($masterviolation[$i]['violation_position'], $street_onduty)) {
					$company_id     = $masterviolation[$i]['violation_vehicle_companyid'];
					$telegram_group = $master_tele_violataion_mitra[$company_id];
					printf("===CONTRACTOR ID : ".$company_id."\r\n");
					printf("===CONTRACTOR NAME : ".$masterviolation[$i]['violation_vehicle_companyname']."\r\n");
					printf("===TELEGRAM GROUP BELUM DIPAKAI : ".$telegram_group."\r\n");
					$data_json = json_decode($masterviolation[$i]['violation_fatigue'], true);
					$gps_time   = $data_json[0]['gps_time'];

					// print_r($masterviolation);exit();

						$alarmreportnamefix = "";
						$alarmreporttype = $data_json[0]['gps_alertid'];

							if ($alarmreporttype == 624) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 625) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 626) {
								$alarmreportnamefix = "DRIVER UNDETECTED ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 627) {
								$alarmreportnamefix = "DRIVER UNDETECTED ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 622) {
								$alarmreportnamefix = "SMOKING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 623) {
								$alarmreportnamefix = "SMOKING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 604) {
								$alarmreportnamefix = "CAR DISTANCE NEAR ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 605) {
								$alarmreportnamefix = "CAR DISTANCE NEAR ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 618) {
								$alarmreportnamefix = "FATIGUE DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 619) {
								$alarmreportnamefix = "FATIGUE DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 620) {
								$alarmreportnamefix = "CALL TO CALL THE ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 621) {
								$alarmreportnamefix = "CALL TO CALL THE ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 702) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 703) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 752) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 753) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}else {
								$alarmreportnamefix = $data_json[0]['gps_alert'];
							}

						$coordinate = $data_json[0]['gps_latitude_real'].",".$data_json[0]['gps_longitude_real'];
						$url        = "https://www.google.com/maps/search/?api=1&query=".$coordinate;

						$message = urlencode(
							"".$alarmreportnamefix." (text mode)\n".
							"Time: ".$data_json[0]['gps_time']." \n".
							"Vehicle No: ".$masterviolation[$i]['violation_vehicle_no']." ".$masterviolation[$i]['violation_vehicle_name']." \n".
							"Driver: "." \n".
							"Position: ".$masterviolation[$i]['violation_position']." \n".
							"Speed (kph): ".$data_json[0]['gps_speed']." \n".
							"Coordinate: ".$url." \n"
							);

							sleep(2);
							$sendtelegram = $this->telegram_direct($telegram_group,$message); //FMS MITRA HAULING
							// $sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
							printf("===SENT TELEGRAM GROUP FMS TESTING OK\r\n");
							$totaltelesend += 1;
							$violation_id = $masterviolation[$i]['violation_id'];
								$update_status_tele = array(
									"violation_status_tele" => 1
								);
								$update_tele = $this->m_violation->updateTeleStatusHistorikal($dbtable, $violation_id, $update_status_tele);
								if ($update_tele) {
									printf("===STATUS TELE BERHASIL DIUPDATE\r\n");
								}else {
									printf("===STATUS TELE GAGAL DIUPDATE\r\n");
								}

								// SENT TO BIB VIOLATION
								$title_name = strtoupper($alarmreportnamefix);
								$alert_level = "";

								$search_level_2 = 'TWO';
								if(preg_match("/{$search_level_2}/i", $title_name)) {
									$alert_level = "2";
								}

								if($alert_level == "2"){
									sleep(2);
									$sendtelegram = $this->telegram_direct("-542787721",$message); //telegram BIB VIOLATION
									printf("===SENT TELEGRAM BIB OK\r\n");
								}
				}
			}

			print_r("==STARTDATE PENCARIAN : ". $sdate . "\r\n");
			print_r("==CRON START : ". $nowtime_wita . "\r\n");
			print_r("==CRON FINISH : ". date("Y-m-d H:i:s", strtotime("+1 Hour")) . " \r\n");
			$finishtime   = date("Y-m-d H:i:s", strtotime("+1 Hour"));
			$start_1      = dbmaketime($nowtime_wita);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"VIOLATION MONITORING TEXT MODE \n".
						"Total Send Alert: ".$totaltelesend." \n".
						"Startdate Pencarian: ".$sdate." \n".
						"Start: ".$nowtime_wita." \n".
						"Finish: ".date("Y-m-d H:i:s", strtotime("+1 Hour"))." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS AUTOCHECK
			$sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			printf("\r\n===SENT TELEGRAM GROUP FMS AUTOCHECK OK\r\n");
		}

		function getAllCompany($userid){
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("company_name","asc");
			$this->db->select("company_name,company_id, company_telegram_speed");
			$this->db->where("company_flag", 0);
			$this->db->where("company_created_by", $userid);
			$q = $this->db->get("company");
			return  $q->result();
		}

		function getAllCompanycheckmdvr($userid){
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("company_name","asc");
			$this->db->select("company_name,company_id, company_telegram_speed");
			$this->db->where("company_flag", 0);
			$this->db->where("company_created_by", $userid);
			// $this->db->where("company_id <>", 1959);
			$q = $this->db->get("company");
			return  $q->result();
		}

		function get_telegramgroup_overspeed($company_id){
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_speed,company_hp");
			$this->db->where("company_id",$company_id);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			// if(count($rcompany)>0){
			// 	$telegram_group = $rcompany->company_telegram_speed;
			// }else{
			// 	$telegram_group = 0;
			// }
			$this->db->close();
			$this->db->cache_delete_all();
			return $rcompany;
		}

		function violationhistorikalskipcondition($userid){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON START : ". $cronstartdate . "\r\n");
			$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
			print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON Violation Historikal Start WITA           : ". $nowtime_wita . "\r\n");
			print_r("CRON Violation Historikal Start WIB          : ". $cronstartdate . "\r\n");

			$datafix           = array();
			$masterdatavehicle = $this->getAllVehicle_mdvronly($userid);
			// CONVERT ARRAY ALARM KE STRING
			// $alarmtypefix      = implode (",", $alarmtypefromaster);

			$date              = date("Y-m-d", strtotime($nowtime_wita));
			// $time           = date("H:i:s", strtotime("-2hours"));
			// $time              = date("H:i:s", strtotime($nowtime_wita . "-12 Hour"));
			$time              = date("H:i:s", strtotime($nowtime_wita . "-15 Minutes"));
			// $startdate         = $date.'%20'.$time;
			$startdate         = $date.' '.$time;

			$edate             = date("Y-m-d", strtotime($nowtime_wita));
			$etime             = date("H:i:s", strtotime($nowtime_wita ."+1 Hour"));
			// $enddate           = $edate.'%20'.$etime;
			$enddate           = $nowtime_wita;
			// $updatetime        = "2020-07-23%2023:59:59";

			// $data_violation_historikal = $this->m_violation->getViolationHistorikal()

			// echo "<pre>";
			// var_dump($startdate.'-'.$enddate);die();
			// echo "<pre>";

			// CHOOSE DBTABLE
			$m1        = date("F", strtotime($nowtime_wita));
			$year      = date("Y", strtotime($nowtime_wita));
			$dbtable   = "";
			$report    = "historikal_violation_";

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

			// $wa_token = $this->getWAToken($userid);
			// printf("===GET TOKEN WA : %s \n", $wa_token->sess_value);

			$master_tele_violataion_mitra = array(
				"1834" => "-677846513",
				"1835" => "-623474283",
				"1837" => "-603940790",
				"1839" => "-1001531051791",
				"1926" => "-682379686",
				"1945" => "-770153853",
				"1946" => "-739893793",
				"1947" => "-676926970",
				"1948" => "-1001615972607",
				"1959" => "-516795045",
			);

			$array_unit_LMO = array("CT-182", "FDT-261", "FDT-740");

			$totaltelesend = 0;
			$street_onduty             = $this->config->item("street_onduty_autocheck");
			$data_violation_historikal = $this->m_violation->getviolationhistorikal_type3($dbtable, $startdate, $enddate);
			printf("===Total Data Belum Dikirim : ".sizeof($data_violation_historikal)."\r\n");

			for ($i=0; $i < sizeof($data_violation_historikal); $i++) {
				usleep(500);
				// if(in_array($data_violation_historikal[$i]["violation_position"], $street_onduty)) {
					// $telegram_group_dt = $this->get_telegramgroup_overspeed($data_violation_historikal[$i]['violation_vehicle_companyid']);
					//
					// if(count($telegram_group_dt)>0){
					// 	$telegram_group = $telegram_group_dt->company_telegram_speed;
					// 	// $telegram_group = "-495868829"; //testing anything
					// }else{
					// 	$telegram_group = "-495868829"; //testing anything
					// }

					$company_id     = $data_violation_historikal[$i]["violation_vehicle_companyid"];
					$telegram_group = $master_tele_violataion_mitra[$company_id];
					printf("===CONTRACTOR ID : ".$company_id."\r\n");
					printf("===CONTRACTOR NAME : ".$data_violation_historikal[$i]['violation_vehicle_companyname']."\r\n");
					printf("===TELEGRAM GROUP BELUM DIPAKAI : ".$telegram_group."\r\n");
					$data_json = json_decode($data_violation_historikal[$i]['violation_fatigue'], true);
					$gps_time   = $data_json[0]['gps_time'];

					// print_r($masterviolation);exit();

						$alarmreportnamefix = "";
						$alarmreporttype = $data_json[0]['gps_alertid'];

							// if ($alarmreporttype == 624) {
							// 	$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							// }elseif ($alarmreporttype == 625) {
							// 	$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							// }

							if ($alarmreporttype == 626) {
								$alarmreportnamefix = "DRIVER UNDETECTED ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 627) {
								$alarmreportnamefix = "DRIVER UNDETECTED ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 622) {
								$alarmreportnamefix = "SMOKING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 623) {
								$alarmreportnamefix = "SMOKING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 604) {
								$alarmreportnamefix = "CAR DISTANCE NEAR ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 605) {
								$alarmreportnamefix = "CAR DISTANCE NEAR ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 618) {
								$alarmreportnamefix = "FATIGUE DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 619) {
								$alarmreportnamefix = "FATIGUE DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 620) {
								$alarmreportnamefix = "CALL TO CALL THE ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 621) {
								$alarmreportnamefix = "CALL TO CALL THE ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 633) {
								$alarmreportnamefix = "REAR APPROACH ALARM START";
							}elseif ($alarmreporttype == 683) {
								$alarmreportnamefix = "REAR APPROACH ALARM END";
							}elseif ($alarmreporttype == 710) {
								$alarmreportnamefix = "HANDS OFF WHEEL LEVEL ONE";
							}elseif ($alarmreporttype == 711) {
								$alarmreportnamefix = "HANDS OFF WHEEL LEVEL TWO";
							}elseif ($alarmreporttype == 706) {
								$alarmreportnamefix = "UNFASTENED SEAT BELT LEVEL ONE";
							}elseif ($alarmreporttype == 707) {
								$alarmreportnamefix = "UNFASTENED SEAT BELT LEVEL TWO";
							}elseif ($alarmreporttype == 702) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 703) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}elseif ($alarmreporttype == 752) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL ONE";
							}elseif ($alarmreporttype == 753) {
								$alarmreportnamefix = "DISTRACTED DRIVING ALARM LEVEL TWO";
							}

							// else {
							// 	$alarmreportnamefix = $data_json[0]['gps_alert'];
							// }

						$coordinate = $data_json[0]['gps_latitude_real'].",".$data_json[0]['gps_longitude_real'];
						$url        = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
						printf("===Kirim Data Ke : ".($i+1)."\r\n");
						printf("===".$alarmreportnamefix." (text mode) \r\n");
						printf("===TIME : ".$data_json[0]['gps_time']."\r\n");
						printf("===Vehicle No : ".$data_violation_historikal[$i]['violation_vehicle_no']." ".$data_violation_historikal[$i]['violation_vehicle_name']."\r\n");
						printf("===Driver : \r\n");
						printf("===Position : ".$data_violation_historikal[$i]['violation_position']."\r\n");
						printf("===Speed (Kph) : ".$data_json[0]['gps_speed']."\r\n");
						printf("===Coordinate : ".$url."\r\n");

						$message = urlencode(
							"".$alarmreportnamefix." (text mode) \n".
							"Time: ".$data_json[0]['gps_time']." \n".
							"Vehicle No: ".$data_violation_historikal[$i]['violation_vehicle_no']." ".$data_violation_historikal[$i]['violation_vehicle_name']." \n".
							"Driver: "." \n".
							"Position: ".$data_violation_historikal[$i]['violation_position']." \n".
							"Speed (kph): ".$data_json[0]['gps_speed']." \n".
							"Coordinate: ".$url." \n"
						);

							sleep(2);
							// $sendtelegram = $this->telegram_direct($telegram_group,$message); //FMS MITRA HAULING
							// $sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
							// if (in_array($data_violation_historikal[$i]['violation_vehicle_no'], $array_unit_LMO)) {
							// 	$sendtelegram = $this->telegram_direct("-953824895",$message); //POC LMO
							// }
							sleep(2);
							$sendtelegram = $this->telegram_direct("-890634600",$message); //FMS POC BERAU COAL NOTIFICATION
							printf("===SENT TELEGRAM GROUP FMS TESTING OK\r\n");
							$totaltelesend += 1;
							$violation_id = $data_violation_historikal[$i]['violation_id'];
								$update_status_tele = array(
									"violation_status_tele" => 1
								);
								$update_tele = $this->m_violation->updateTeleStatusHistorikal($dbtable, $violation_id, $update_status_tele);
								if ($update_tele) {
									printf("===STATUS TELE BERHASIL DIUPDATE\r\n");
								}else {
									printf("===STATUS TELE GAGAL DIUPDATE\r\n");
								}
								printf("============================================================\r\n");

								// NOTIF WHATSAPP START
								// $data_for_watoken = array(
								// 	"wa_token"     => $wa_token,
								// 	"title_name"   => $alarmreportnamefix,
								// 	"gps_time"     => $data_json[0]['gps_time'],
								// 	"vehicle_no"   => $data_violation_historikal[$i]['violation_vehicle_no']." ".$data_violation_historikal[$i]['violation_vehicle_name'],
								// 	"driver" 		   => "",
								// 	"position" 	   => $data_violation_historikal[$i]['violation_position'],
								// 	"coordinate"   => $url,
								// 	"speed" 		   => $data_json[0]['gps_speed'],
								// 	"data_company" => $telegram_group_dt
								// );
								//
								// $notif_wa_ovspeed = $this->sendnotif_wa_mdvr($data_for_watoken, "mitra");
								// NOTIF WHATSAPP END

								// SENT TO BIB VIOLATION
								$title_name = strtoupper($alarmreportnamefix);
								$alert_level = "";

								$search_level_2 = 'TWO';
								if(preg_match("/{$search_level_2}/i", $title_name)) {
									$alert_level = "2";
								}

								// if($alert_level == "2"){
									// if ($alarmreporttype == 618 || $alarmreporttype == 619 || $alarmreporttype == 620 || $alarmreporttype == 621) {
									// 	sleep(2);
									// 	$sendtelegram = $this->telegram_direct("-542787721",$message); //telegram BIB VIOLATION
									// 	printf("===SENT TELEGRAM BIB OK\r\n");
									// 	// $notif_wa_ovspeed = $this->sendnotif_wa_mdvr($data_for_watoken, "bib");
									// 	// printf("===SENT WHATSAPP NOTIF BIB OK\r\n");
									// }
								// }
				// }
			}

			// echo "<pre>";
			// var_dump($data_violation_historikal);die();
			// echo "<pre>";

			$totaldata_baru = $totaltelesend;

				print_r("CRON START : ". $cronstartdate . "\r\n");
				print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
				$finishtime   = date("Y-m-d H:i:s");
				$start_1      = dbmaketime($cronstartdate);
				$end_1        = dbmaketime($finishtime);
				$duration_sec = $end_1 - $start_1;
				$message =  urlencode(
							"VIOLATION MONITORING HISTORIKAL SKIP CONDITION (POC BC) \n".
							"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
							"Total Data Resent : ".$totaldata_baru." \n".
							"Start: ".$cronstartdate." \n".
							"Finish: ".date("Y-m-d H:i:s")." \n".
							"Latency: ".$duration_sec." s"." \n"
							);

				// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS Autocheck
				// $sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
				$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
				printf("===SENT TELEGRAM OK\r\n");
		}

		// CRON UNTUK MDVR STATUS START
		function mdvrfrekuensitablemitra($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON START : ". $cronstartdate . "\r\n");
			$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
			print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON FREKUENSI ANOMALI MDVR Start WITA           : ". $nowtime_wita . "\r\n");
			print_r("CRON FREKUENSI ANOMALI MDVR Start WIB          : ". $cronstartdate . "\r\n");

			$cron_starting_range = "";
				if ($startdate == "") {
					$start_range = date("Y-m-d");
					$m1            = date("F", strtotime($nowtime_wita));
					$year          = date("Y", strtotime($nowtime_wita));
				}else {
					$start_range = $startdate;
					$m1            = date("F", strtotime($start_range));
					$year          = date("Y", strtotime($start_range));
				}

			// CHOOSE DBTABLE

			$dbtable       = "";
			$dbsummary     = "";
			$report        = "report_device_status_";
			$summary_table = "report_device_status_summary_";

			switch ($m1)
			{
				case "January":
							$dbtable = $report."januari_".$year;
							$dbsummary = $summary_table."januari_".$year;
				break;
				case "February":
							$dbtable = $report."februari_".$year;
							$dbsummary = $summary_table."februari_".$year;
				break;
				case "March":
							$dbtable = $report."maret_".$year;
							$dbsummary = $summary_table."maret_".$year;
				break;
				case "April":
							$dbtable = $report."april_".$year;
							$dbsummary = $summary_table."april_".$year;
				break;
				case "May":
							$dbtable = $report."mei_".$year;
							$dbsummary = $summary_table."mei_".$year;
				break;
				case "June":
							$dbtable = $report."juni_".$year;
							$dbsummary = $summary_table."juni_".$year;
				break;
				case "July":
							$dbtable = $report."juli_".$year;
							$dbsummary = $summary_table."juli_".$year;
				break;
				case "August":
							$dbtable = $report."agustus_".$year;
							$dbsummary = $summary_table."agustus_".$year;
				break;
				case "September":
							$dbtable = $report."september_".$year;
							$dbsummary = $summary_table."september_".$year;
				break;
				case "October":
							$dbtable = $report."oktober_".$year;
							$dbsummary = $summary_table."oktober_".$year;
				break;
				case "November":
							$dbtable = $report."november_".$year;
							$dbsummary = $summary_table."november_".$year;
				break;
				case "December":
							$dbtable = $report."desember_".$year;
							$dbsummary = $summary_table."desember_".$year;
				break;
			}



			$masterdatavehicle    = $this->getAllVehicle_mdvronly_2($userid);
			// echo "<pre>";
			// var_dump(sizeof($masterdatavehicle));die();
			// echo "<pre>";

			$data_array_frekuensi = array();
			$total_insert         = 0;
			$total_update         = 0 ;
				for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
					usleep(500);
					// for ($i=0; $i < 20; $i++) {
					$vehicle_device             = $masterdatavehicle[$i]['vehicle_device']; //"869926046529570@VT200";
					$vehicle_no		              = $masterdatavehicle[$i]['vehicle_no'];
					$total_frekuensi_normal     = $this->getFrekuensi_normal($dbtable, $vehicle_device, $start_range);
					$total_frekuensi_normal_2   = $this->getFrekuensi_normal_2($dbtable, $vehicle_device, $start_range);
					$get_data_by_range          = $this->getFrekuensi_anomali($dbtable, $vehicle_device, $start_range);
					$total_frekuensi_pengecekan = $this->getFrekuensi_total($dbtable, $vehicle_device, $start_range);
					$jumlah_anomali 					  = 0;
					$total_frekuensi_normal_fix = (sizeof($total_frekuensi_normal)+sizeof($total_frekuensi_normal_2));
					$total_frekuensi_anomali_fix = (sizeof($get_data_by_range)-sizeof($total_frekuensi_normal_fix));

					// echo "<pre>";
					// var_dump(sizeof($total_frekuensi_pengecekan).'-'.$total_frekuensi_anomali_fix.'-'.$total_frekuensi_normal_fix);die();
					// echo "<pre>";

					if (sizeof($total_frekuensi_pengecekan) > 0) {
						if (sizeof($get_data_by_range) > 0) {
							$jsonautocheck = json_decode($masterdatavehicle[$i]['vehicle_autocheck'], TRUE);
							if ($jsonautocheck) {
									$auto_status = $jsonautocheck['auto_status'];
									if ($auto_status == "M") {
										$jumlah_anomali = sizeof($total_frekuensi_pengecekan);
									}else {
										$jumlah_anomali = sizeof($get_data_by_range);
									}
							}else {
								$jumlah_anomali = sizeof($get_data_by_range);
							}
							print_r("===DATA DITEMUKAN \r\n");
							print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										$data_array_frekuensi = array(
											"devicestatus_summary_vehicle_device"    => $get_data_by_range[0]["devicestatus_vehicle_vehicle_device"],
											"devicestatus_summary_vehicle_company"   => $get_data_by_range[0]["devicestatus_vehicle_company"],
											"devicestatus_summary_vehicle_no" 	     => $get_data_by_range[0]["devicestatus_vehicle_no"],
											"devicestatus_summary_vehicle_name"      => $get_data_by_range[0]["devicestatus_vehicle_name"],
											"devicestatus_summary_devicemv03"        => $get_data_by_range[0]["devicestatus_mv03"],
											"devicestatus_summary_frekuensi_normal"  => $total_frekuensi_normal_fix,
											"devicestatus_summary_frekuensi_anomali" => $jumlah_anomali,
											"devicestatus_summary_total_frekuensi"   => sizeof($total_frekuensi_pengecekan),
											"devicestatus_summary_network_type"      => $get_data_by_range[0]["devicestatus_network_type"],
											"devicestatus_summary_submited_date"     => date("Y-m-d", strtotime($get_data_by_range[0]["devicestatus_submited_date"])),
										);
						}else {
							$jsonautocheck = json_decode($masterdatavehicle[$i]['vehicle_autocheck'], TRUE);
							if ($jsonautocheck) {
									$auto_status = $jsonautocheck['auto_status'];
										if ($auto_status == "M") {
											$jumlah_anomali = sizeof($total_frekuensi_pengecekan);
										}else {
											$jumlah_anomali = sizeof($get_data_by_range);
										}
								}else {
									$jumlah_anomali = sizeof($get_data_by_range);
								}

							print_r("===DATA DITEMUKAN \r\n");
							print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										$data_array_frekuensi = array(
											"devicestatus_summary_vehicle_device"    => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_vehicle_device"],
											"devicestatus_summary_vehicle_company"   => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_company"],
											"devicestatus_summary_vehicle_no" 	     => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_no"],
											"devicestatus_summary_vehicle_name"      => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_name"],
											"devicestatus_summary_devicemv03"        => $total_frekuensi_pengecekan[0]["devicestatus_mv03"],
											"devicestatus_summary_frekuensi_normal"  => $total_frekuensi_normal_fix,
											"devicestatus_summary_frekuensi_anomali" => $jumlah_anomali,
											"devicestatus_summary_total_frekuensi"   => sizeof($total_frekuensi_pengecekan),
											"devicestatus_summary_network_type"      => $total_frekuensi_pengecekan[0]["devicestatus_network_type"],
											"devicestatus_summary_submited_date"     => date("Y-m-d", strtotime($total_frekuensi_pengecekan[0]["devicestatus_submited_date"])),
										);
						}

						$vehicle_device = $data_array_frekuensi['devicestatus_summary_vehicle_device'];
						$vehicle_no     = $data_array_frekuensi['devicestatus_summary_vehicle_no'];
						$submited_date  = $data_array_frekuensi['devicestatus_summary_submited_date'];
						$check_data     = $this->check_data_in_summarytable($dbsummary, $vehicle_device, $submited_date);
							if (sizeof($check_data) < 1) {
								// INSERT DATA
								$data_insert = array(
									"devicestatus_summary_vehicle_device"    => $data_array_frekuensi['devicestatus_summary_vehicle_device'],
									"devicestatus_summary_vehicle_company"   => $data_array_frekuensi['devicestatus_summary_vehicle_company'],
									"devicestatus_summary_vehicle_no"        => $data_array_frekuensi['devicestatus_summary_vehicle_no'],
									"devicestatus_summary_vehicle_name"      => $data_array_frekuensi['devicestatus_summary_vehicle_name'],
									"devicestatus_summary_devicemv03"        => $data_array_frekuensi['devicestatus_summary_devicemv03'],
									"devicestatus_summary_frekuensi_normal"  => $data_array_frekuensi['devicestatus_summary_frekuensi_normal'],
									"devicestatus_summary_frekuensi_anomali" => $data_array_frekuensi['devicestatus_summary_frekuensi_anomali'],
									"devicestatus_summary_total_frekuensi"   => $data_array_frekuensi['devicestatus_summary_total_frekuensi'],
									"devicestatus_summary_network_type"      => $data_array_frekuensi['devicestatus_summary_network_type'],
									"devicestatus_summary_submited_date"     => $data_array_frekuensi['devicestatus_summary_submited_date'],
								);

								$insertNow = $this->dbtensor->insert($dbsummary, $data_insert);
									if ($insertNow) {
										$total_insert += 1;
										// print_r("==========================================\r\n");
										print_r("Insert Data Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}else {
										print_r("FAILED INSERT DATA Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}
							}else {
								// UPDATE DATA
								$data_update = array(
									"devicestatus_summary_frekuensi_normal"  => $data_array_frekuensi['devicestatus_summary_frekuensi_normal'],
									"devicestatus_summary_frekuensi_anomali" => $data_array_frekuensi['devicestatus_summary_frekuensi_anomali'],
									"devicestatus_summary_total_frekuensi" => $data_array_frekuensi['devicestatus_summary_total_frekuensi'],
									// "devicestatus_summary_submited_date"     => $data_array_frekuensi['devicestatus_summary_submited_date'],
								);

								$updateNow = $this->updateDataSummaryMDVR("tensor_report", $dbsummary, "devicestatus_summary_vehicle_device", $vehicle_device, $start_range, $data_update);
									if ($updateNow) {
										$total_update += 1;
										// print_r("==========================================\r\n");
										print_r("Update Data Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}else {
										print_r("FAILED UPDATE DATA Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}
							}
					}
				}

			// echo "<pre>";
			// var_dump($data_array_frekuensi);die();
			// echo "<pre>";

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"CRON MDVR STATUS FREKUENSI TABLE \n".
						"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						"Total Row Diinsert : ".$total_insert." \n".
						"Total Row Diupdate : ".$total_update." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS Autocheck
			// $sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
			printf("===SENT TELEGRAM OK\r\n");
		}

		function mdvrfrekuensitableadminonly($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON START : ". $cronstartdate . "\r\n");
			$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-60 minutes"));
			print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON FREKUENSI ANOMALI MDVR Start WITA           : ". $nowtime_wita . "\r\n");
			print_r("CRON FREKUENSI ANOMALI MDVR Start WIB          : ". $cronstartdate . "\r\n");

			$cron_starting_range = "";
				if ($startdate == "") {
					$start_range = date("Y-m-d");
					$m1            = date("F", strtotime($nowtime_wita));
					$year          = date("Y", strtotime($nowtime_wita));
				}else {
					$start_range = $startdate;
					$m1            = date("F", strtotime($start_range));
					$year          = date("Y", strtotime($start_range));
				}

			// CHOOSE DBTABLE

			$dbtable       = "";
			$dbsummary     = "";
			$report        = "report_device_status_";
			$summary_table = "report_device_status_summary_";

			switch ($m1)
			{
				case "January":
							$dbtable = $report."januari_".$year;
							$dbsummary = $summary_table."januari_".$year;
				break;
				case "February":
							$dbtable = $report."februari_".$year;
							$dbsummary = $summary_table."februari_".$year;
				break;
				case "March":
							$dbtable = $report."maret_".$year;
							$dbsummary = $summary_table."maret_".$year;
				break;
				case "April":
							$dbtable = $report."april_".$year;
							$dbsummary = $summary_table."april_".$year;
				break;
				case "May":
							$dbtable = $report."mei_".$year;
							$dbsummary = $summary_table."mei_".$year;
				break;
				case "June":
							$dbtable = $report."juni_".$year;
							$dbsummary = $summary_table."juni_".$year;
				break;
				case "July":
							$dbtable = $report."juli_".$year;
							$dbsummary = $summary_table."juli_".$year;
				break;
				case "August":
							$dbtable = $report."agustus_".$year;
							$dbsummary = $summary_table."agustus_".$year;
				break;
				case "September":
							$dbtable = $report."september_".$year;
							$dbsummary = $summary_table."september_".$year;
				break;
				case "October":
							$dbtable = $report."oktober_".$year;
							$dbsummary = $summary_table."oktober_".$year;
				break;
				case "November":
							$dbtable = $report."november_".$year;
							$dbsummary = $summary_table."november_".$year;
				break;
				case "December":
							$dbtable = $report."desember_".$year;
							$dbsummary = $summary_table."desember_".$year;
				break;
			}



			$masterdatavehicle    = $this->getAllVehicle_mdvronly_2($userid);
			// echo "<pre>";
			// var_dump(sizeof($masterdatavehicle));die();
			// echo "<pre>";

			$data_array_frekuensi = array();
			$total_insert         = 0;
			$total_update         = 0 ;
				for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
					// for ($i=0; $i < 20; $i++) {
					usleep(500);
					$vehicle_device             = $masterdatavehicle[$i]['vehicle_device']; //"869926046529570@VT200";
					$vehicle_no		              = $masterdatavehicle[$i]['vehicle_no'];
					$total_frekuensi_normal     = $this->getFrekuensi_normal($dbtable, $vehicle_device, $start_range);
					$total_frekuensi_normal_2   = $this->getFrekuensi_normal_2($dbtable, $vehicle_device, $start_range);
					$get_data_by_range          = $this->getFrekuensi_anomali($dbtable, $vehicle_device, $start_range);
					$total_frekuensi_pengecekan = $this->getFrekuensi_total($dbtable, $vehicle_device, $start_range);
					$jumlah_anomali 					  = 0;
					// INI KONDISI YANG DIRUBAH
					// $total_frekuensi_normal_fix = (sizeof($total_frekuensi_normal)+sizeof($total_frekuensi_normal_2));
					// $total_frekuensi_anomali_fix = (sizeof($get_data_by_range)-sizeof($total_frekuensi_normal_fix));
					$total_frekuensi_normal_fix = sizeof($total_frekuensi_normal_2);
					$total_frekuensi_anomali_fix = ((sizeof($total_frekuensi_normal)+sizeof($get_data_by_range))-sizeof($total_frekuensi_normal_fix));

					// echo "<pre>";
					// var_dump(sizeof($total_frekuensi_pengecekan).'-'.$total_frekuensi_anomali_fix.'-'.$total_frekuensi_normal_fix);die();
					// echo "<pre>";

					if (sizeof($total_frekuensi_pengecekan) > 0) {
						if (sizeof($get_data_by_range) > 0) {
							$jsonautocheck = json_decode($masterdatavehicle[$i]['vehicle_autocheck'], TRUE);
							if ($jsonautocheck) {
									$auto_status = $jsonautocheck['auto_status'];
									if ($auto_status == "M") {
										$jumlah_anomali = sizeof($total_frekuensi_pengecekan);
									}else {
										$jumlah_anomali = (sizeof($get_data_by_range)+sizeof($total_frekuensi_normal));
									}
							}else {
								$jumlah_anomali = (sizeof($get_data_by_range)+sizeof($total_frekuensi_normal));
							}
							print_r("===DATA DITEMUKAN \r\n");
							print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										$data_array_frekuensi = array(
											"devicestatus_summary_vehicle_device"    => $get_data_by_range[0]["devicestatus_vehicle_vehicle_device"],
											"devicestatus_summary_vehicle_company"   => $get_data_by_range[0]["devicestatus_vehicle_company"],
											"devicestatus_summary_vehicle_no" 	     => $get_data_by_range[0]["devicestatus_vehicle_no"],
											"devicestatus_summary_vehicle_name"      => $get_data_by_range[0]["devicestatus_vehicle_name"],
											"devicestatus_summary_devicemv03"        => $get_data_by_range[0]["devicestatus_mv03"],
											"devicestatus_summary_frekuensi_normal"  => $total_frekuensi_normal_fix,
											"devicestatus_summary_frekuensi_anomali" => $jumlah_anomali,
											"devicestatus_summary_total_frekuensi"   => sizeof($total_frekuensi_pengecekan),
											"devicestatus_summary_isformitra" 		   => 1,
											"devicestatus_summary_network_type"      => $get_data_by_range[0]["devicestatus_network_type"],
											"devicestatus_summary_submited_date"     => date("Y-m-d", strtotime($get_data_by_range[0]["devicestatus_submited_date"])),
										);
						}else {
							$jsonautocheck = json_decode($masterdatavehicle[$i]['vehicle_autocheck'], TRUE);
							if ($jsonautocheck) {
									$auto_status = $jsonautocheck['auto_status'];
										if ($auto_status == "M") {
											$jumlah_anomali = sizeof($total_frekuensi_pengecekan);
										}else {
											$jumlah_anomali = (sizeof($get_data_by_range)+sizeof($total_frekuensi_normal));
										}
								}else {
									$jumlah_anomali = (sizeof($get_data_by_range)+sizeof($total_frekuensi_normal));
								}

							print_r("===DATA DITEMUKAN \r\n");
							print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										$data_array_frekuensi = array(
											"devicestatus_summary_vehicle_device"    => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_vehicle_device"],
											"devicestatus_summary_vehicle_company"   => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_company"],
											"devicestatus_summary_vehicle_no" 	     => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_no"],
											"devicestatus_summary_vehicle_name"      => $total_frekuensi_pengecekan[0]["devicestatus_vehicle_name"],
											"devicestatus_summary_devicemv03"        => $total_frekuensi_pengecekan[0]["devicestatus_mv03"],
											"devicestatus_summary_frekuensi_normal"  => $total_frekuensi_normal_fix,
											"devicestatus_summary_frekuensi_anomali" => $jumlah_anomali,
											"devicestatus_summary_total_frekuensi"   => sizeof($total_frekuensi_pengecekan),
											"devicestatus_summary_isformitra" 		   => 1,
											"devicestatus_summary_network_type"      => $total_frekuensi_pengecekan[0]["devicestatus_network_type"],
											"devicestatus_summary_submited_date"     => date("Y-m-d", strtotime($total_frekuensi_pengecekan[0]["devicestatus_submited_date"])),
										);
						}

						$vehicle_device = $data_array_frekuensi['devicestatus_summary_vehicle_device'];
						$vehicle_no     = $data_array_frekuensi['devicestatus_summary_vehicle_no'];
						$submited_date  = $data_array_frekuensi['devicestatus_summary_submited_date'];
						$check_data     = $this->check_data_in_summarytableadminonly($dbsummary, $vehicle_device, $submited_date);
							if (sizeof($check_data) < 1) {
								// INSERT DATA
								$data_insert = array(
									"devicestatus_summary_vehicle_device"    => $data_array_frekuensi['devicestatus_summary_vehicle_device'],
									"devicestatus_summary_vehicle_company"   => $data_array_frekuensi['devicestatus_summary_vehicle_company'],
									"devicestatus_summary_vehicle_no"        => $data_array_frekuensi['devicestatus_summary_vehicle_no'],
									"devicestatus_summary_vehicle_name"      => $data_array_frekuensi['devicestatus_summary_vehicle_name'],
									"devicestatus_summary_devicemv03"        => $data_array_frekuensi['devicestatus_summary_devicemv03'],
									"devicestatus_summary_frekuensi_normal"  => $data_array_frekuensi['devicestatus_summary_frekuensi_normal'],
									"devicestatus_summary_frekuensi_anomali" => $data_array_frekuensi['devicestatus_summary_frekuensi_anomali'],
									"devicestatus_summary_total_frekuensi"   => $data_array_frekuensi['devicestatus_summary_total_frekuensi'],
									"devicestatus_summary_isformitra"   		 => $data_array_frekuensi['devicestatus_summary_isformitra'],
									"devicestatus_summary_network_type"      => $data_array_frekuensi['devicestatus_summary_network_type'],
									"devicestatus_summary_submited_date"     => $data_array_frekuensi['devicestatus_summary_submited_date'],
								);

								$insertNow = $this->dbtensor->insert($dbsummary, $data_insert);
									if ($insertNow) {
										$total_insert += 1;
										// print_r("==========================================\r\n");
										print_r("Insert Data Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}else {
										print_r("FAILED INSERT DATA Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}
							}else {
								// UPDATE DATA
								$data_update = array(
									"devicestatus_summary_frekuensi_normal"  => $data_array_frekuensi['devicestatus_summary_frekuensi_normal'],
									"devicestatus_summary_frekuensi_anomali" => $data_array_frekuensi['devicestatus_summary_frekuensi_anomali'],
									"devicestatus_summary_total_frekuensi" => $data_array_frekuensi['devicestatus_summary_total_frekuensi'],
									// "devicestatus_summary_submited_date"     => $data_array_frekuensi['devicestatus_summary_submited_date'],
								);

								$updateNow = $this->updateDataSummaryMDVRadminonly("tensor_report", $dbsummary, "devicestatus_summary_vehicle_device", $vehicle_device, $start_range, $data_update);
									if ($updateNow) {
										$total_update += 1;
										// print_r("==========================================\r\n");
										print_r("Update Data Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}else {
										print_r("FAILED UPDATE DATA Ke : ". $i . " Dari (".sizeof($masterdatavehicle).")\r\n");
										print_r("VEHICLE NO : ". $vehicle_no . "\r\n");
										print_r("VEHICLE DEVICE : ". $vehicle_device . "\r\n");
										printf("================================= \r\n");
									}
							}
					}
				}

			// echo "<pre>";
			// var_dump($data_array_frekuensi);die();
			// echo "<pre>";

			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$message =  urlencode(
						"CRON MDVR STATUS FREKUENSI TABLE  VERSI ADMIN ONLY \n".
						"Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						"Total Row Diinsert : ".$total_insert." \n".
						"Total Row Diupdate : ".$total_update." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			// $sendtelegram = $this->telegram_direct("-742300146",$message); // FMS Autocheck
			// $sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			$sendtelegram = $this->telegram_directcheckmdvr("-657527213",$message); //FMS TESTING
			printf("===SENT TELEGRAM OK\r\n");
		}

		function getFrekuensi_normal($table, $vdevice, $date){
			$this->dbtensor = $this->load->database("tensor_report", true);
	    $this->dbtensor->select("*");
	    $this->dbtensor->where("devicestatus_vehicle_vehicle_device", $vdevice);
			$this->dbtensor->where("devicestatus_name", "offline");
			$this->dbtensor->where("devicestatus_last_engine", "OFF");
			$this->dbtensor->where("DATE(devicestatus_submited_date)", $date);
	    $this->dbtensor->order_by("devicestatus_submited_date", "ASC");
	    $q        = $this->dbtensor->get($table);
	    return  $q->result_array();
		}

		function getFrekuensi_normal_2($table, $vdevice, $date){
			$this->dbtensor = $this->load->database("tensor_report", true);
	    $this->dbtensor->select("*");
	    $this->dbtensor->where("devicestatus_vehicle_vehicle_device", $vdevice);
			$this->dbtensor->where("devicestatus_name", "online");
			$this->dbtensor->where("devicestatus_last_engine", "ON");
			$this->dbtensor->where("DATE(devicestatus_submited_date)", $date);
	    $this->dbtensor->order_by("devicestatus_submited_date", "ASC");
	    $q        = $this->dbtensor->get($table);
	    return  $q->result_array();
		}

		function getFrekuensi_anomali($table, $vdevice, $date){
			$this->dbtensor = $this->load->database("tensor_report", true);
	    $this->dbtensor->select("*");
	    $this->dbtensor->where("devicestatus_vehicle_vehicle_device", $vdevice);
			$this->dbtensor->where("devicestatus_name", "offline");
			$this->dbtensor->where("devicestatus_last_engine", "ON");
			$this->dbtensor->where("DATE(devicestatus_submited_date)", $date);
	    $this->dbtensor->order_by("devicestatus_submited_date", "ASC");
	    $q        = $this->dbtensor->get($table);
	    return  $q->result_array();
		}

		function getFrekuensi_total($table, $vdevice, $date){
			$this->dbtensor = $this->load->database("tensor_report", true);
	    $this->dbtensor->select("*");
	    $this->dbtensor->where("devicestatus_vehicle_vehicle_device", $vdevice);
			// $this->dbtensor->where("devicestatus_name", "offline");
			// $this->dbtensor->where("devicestatus_last_engine", "ON");
			$this->dbtensor->where("DATE(devicestatus_submited_date)", $date);
	    $this->dbtensor->order_by("devicestatus_submited_date", "ASC");
	    $q        = $this->dbtensor->get($table);
	    return  $q->result_array();
		}

		function check_data_in_summarytable($table, $vdevice, $date){
			$this->dbtensor = $this->load->database("tensor_report", true);
	    $this->dbtensor->select("*");
	    $this->dbtensor->where("devicestatus_summary_vehicle_device", $vdevice);
			$this->dbtensor->where("DATE(devicestatus_summary_submited_date)", $date);
	    $q        = $this->dbtensor->get($table);
	    return  $q->result_array();
		}

		function check_data_in_summarytableadminonly($table, $vdevice, $date){
			$this->dbtensor = $this->load->database("tensor_report", true);
	    $this->dbtensor->select("*");
			$this->dbtensor->where("devicestatus_summary_isformitra", 1);
	    $this->dbtensor->where("devicestatus_summary_vehicle_device", $vdevice);
			$this->dbtensor->where("DATE(devicestatus_summary_submited_date)", $date);
	    $q        = $this->dbtensor->get($table);
	    return  $q->result_array();
		}
		// CRON UNTUK MDVR STATUS END

		function sendnotif_wa_mdvr($datafornotif, $tujuan){

			$mytoken       = $datafornotif['wa_token']->sess_value;

			// echo "<pre>";
			// var_dump($mytoken);die();
			// echo "<pre>";

			$authorization = "token: Bearer ".$mytoken;
			$url           = $this->config->item('WA_URL_POST_MESSAGE');

			$DataToUpload = array();
			unset($DataToUpload);

			$namespace       = $this->config->item('WA_NAMESPACE');
			$nametemplate    = $this->config->item('WA_TEMPLATE_VIOLATION'); //8 param
				if ($tujuan == "mitra") {
					$recipient_dt    = $datafornotif['data_company'];
				}else {
					$recipient_dt    = $this->config->item('WA_NOMOR_USER_BIB');
				}
			$total_recipient = count($recipient_dt);

			if($total_recipient>0){
				if ($tujuan == "mitra") {
					$recipient_dt_ex = explode(";",$recipient_dt->company_hp);

					$total_recipient_ex = count($recipient_dt_ex);
				}else {
					$recipient_dt_ex    = $this->config->item('WA_NOMOR_USER_BIB');
					$total_recipient_ex = count($recipient_dt_ex);
				}

			}

			for($w=0;$w<$total_recipient_ex;$w++){

				usleep(500);
				$DataToUpload->to = $recipient_dt_ex[$w];
				$DataToUpload->type = "template";
				$DataToUpload->template['namespace'] = $namespace;
				$DataToUpload->template['language']['policy'] = "deterministic";
				$DataToUpload->template['language']['code'] = "en";
				$DataToUpload->template['name'] = $nametemplate;
				$DataToUpload->template['components'][0]->type = "body";
				$DataToUpload->template['components'][0]->parameters[0]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[0]['text'] = $datafornotif["title_name"];
				$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[1]['text'] = date("d-m-Y H:i:s", strtotime($datafornotif["gps_time"]));
				$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[2]['text'] = $datafornotif["vehicle_no"];
				$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[3]['text'] = "-";
				$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[4]['text'] = $datafornotif["position"];
				$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[5]['text'] = $datafornotif["speed"];
				$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[6]['text'] = $datafornotif["coordinate"];
				$DataToUpload->template['components'][0]->parameters[7]['type'] = "text";
				$DataToUpload->template['components'][0]->parameters[7]['text'] = "Text Mode";

				$content = json_encode($DataToUpload);

				// echo "<pre>";
				// var_dump($content);die();
				// echo "<pre>";

				// print_r($content);exit();

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				$headers = array(
							"Content-Type: application/json",
							"Authorization: Bearer " .$mytoken,
							);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$result = curl_exec($curl);
				echo $result;


			}


			printf("-------- \r\n");
		}

		function getWAToken($usid)
		{
			$status = false;
			$this->dbts = $this->load->database("webtracking_ts", TRUE);
			$this->dbts->order_by("sess_expired","desc");
			$this->dbts->select("sess_value");
			$this->dbts->where("sess_user",$usid);
			$this->dbts->where("sess_status",1);
			$this->dbts->limit(1);
			$q = $this->dbts->get("ts_wa_token");
			$data = $q->row();

			$this->dbts->close();
			$this->dbts->cache_delete_all();

			return $data;
		}

		function getTokenWaAPI($userid){
			$username = $this->config->item("WA_USERNAME_BIB");
			$password = $this->config->item("WA_PASSWORD_BIB");

			$urlGetToken = $this->config->item("WA_URL_GET_TOKEN");

			// GET TOKEN
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$urlGetToken);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			$result = curl_exec($ch);
			curl_close($ch);

			$obj = json_decode($result);
			// echo "<pre>";
			// var_dump($obj->users);die();
			// echo "<pre>";

				if (isset($obj->users[0]->token)) {
					printf("==TOKEN : ".$obj->users[0]->token."\r\n");
					$data_array = array(
						"sess_value"        => $obj->users[0]->token,
						"sess_type"         => "token",
						"sess_lastmodified" => date("Y-m-d H:i:s"),
						"sess_expired" 		  => $obj->users[0]->expires_after,
						"sess_user"         => 4408,
						"sess_status"       => 1,
					);

					$insertNow = $this->insertDataToken($data_array);
						if ($insertNow) {
							$datafix = array();
								printf("==SUCCESS INSERT DATA TOKEN\r\n");
						}else {
							printf("==FAILED INSERT DATA TOKEN\r\n");
						}

					// echo "<pre>";
					// var_dump($result);die();
					// echo "<pre>";
				}else {
					printf("==GAGAL GET DATA TOKEN\r\n");
				}
		}

		function fuelsensorcheck($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON FUELSENSOR CHECK IS START : ". $cronstartdate . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON FUELSENSOR CHECK Start WIB          : ". $cronstartdate . "\r\n");
			print_r("CRON FUELSENSOR CHECK Start WITA           : ". $nowtime_wita . "\r\n");

			if ($startdate == "") {
				$isbackdate = 0;
				$start_range = date("Y-m-d");
				$m1            = date("F", strtotime($nowtime_wita));
				$year          = date("Y", strtotime($nowtime_wita));
			}else {
				$isbackdate = 1;
				$start_range = $startdate;
				$m1            = date("F", strtotime($start_range));
				$year          = date("Y", strtotime($start_range));
			}

			// CHOOSE DBTABLE
			$dbtable        = "";
			$dbsummary      = "";
			$report         = "fuelsensor_history_";
			$summary_table  = "fuelsensor_history_summary_";
			$backdate_table = "location_";

			switch ($m1)
			{
				case "January":
							$dbtable = $report."januari_".$year;
							$dbsummary = $summary_table."januari_".$year;
							$dbbackdate = $backdate_table."januari_".$year;
				break;
				case "February":
							$dbtable = $report."februari_".$year;
							$dbsummary = $summary_table."februari_".$year;
							$dbbackdate = $backdate_table."februari_".$year;
				break;
				case "March":
							$dbtable = $report."maret_".$year;
							$dbsummary = $summary_table."maret_".$year;
							$dbbackdate = $backdate_table."maret_".$year;
				break;
				case "April":
							$dbtable = $report."april_".$year;
							$dbsummary = $summary_table."april_".$year;
							$dbbackdate = $backdate_table."april_".$year;
				break;
				case "May":
							$dbtable = $report."mei_".$year;
							$dbsummary = $summary_table."mei_".$year;
							$dbbackdate = $backdate_table."mei_".$year;
				break;
				case "June":
							$dbtable = $report."juni_".$year;
							$dbsummary = $summary_table."juni_".$year;
							$dbbackdate = $backdate_table."juni_".$year;
				break;
				case "July":
							$dbtable = $report."juli_".$year;
							$dbsummary = $summary_table."juli_".$year;
							$dbbackdate = $backdate_table."juli_".$year;
				break;
				case "August":
							$dbtable = $report."agustus_".$year;
							$dbsummary = $summary_table."agustus_".$year;
							$dbbackdate = $backdate_table."agustus_".$year;
				break;
				case "September":
							$dbtable = $report."september_".$year;
							$dbsummary = $summary_table."september_".$year;
							$dbbackdate = $backdate_table."september_".$year;
				break;
				case "October":
							$dbtable = $report."oktober_".$year;
							$dbsummary = $summary_table."oktober_".$year;
							$dbbackdate = $backdate_table."oktober_".$year;
				break;
				case "November":
							$dbtable = $report."november_".$year;
							$dbsummary = $summary_table."november_".$year;
							$dbbackdate = $backdate_table."november_".$year;
				break;
				case "December":
							$dbtable = $report."desember_".$year;
							$dbsummary = $summary_table."desember_".$year;
							$dbbackdate = $backdate_table."desember_".$year;
				break;
			}

			if ($startdate == "") {
				printf("===START TODAY CRON \n");
				$datafix             = array();
				$dataforsenttelegram = array();

				$company             = $this->getAllCompany($userid);
				$totalcompany        = count($company);
				printf("===TOTAL COMPANY %s \r\n", $totalcompany);

					for ($i=0; $i < sizeof($company); $i++) {
						usleep(500);
						$company_id          = $company[$i]->company_id;
						$masterdatavehicle   = $this->getAllVehicle_fuelsensoronly($company_id, $userid);
							if (sizeof($masterdatavehicle) > 0) {
								$dataforsenttelegram = array();
								$text_info           = "";
								$datafuelsensorok = array();
								for ($j=0; $j < sizeof($masterdatavehicle); $j++) {
									print_r("VEHICLE NO           : ". $masterdatavehicle[$j]->vehicle_no . "\r\n");
									$device          = explode("@", $masterdatavehicle[$j]->vehicle_device);
									$device0         = $device[0];
									$device1         = $device[1];
									$vehicle_dblive  = $masterdatavehicle[$j]->vehicle_dbname_live;
									$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $vehicle_dblive, $device0);
										if (sizeof($getdatalastinfo) > 0) {
											$jsonnya      = json_decode($getdatalastinfo[0]['vehicle_autocheck']);

											// echo "<pre>";
											// var_dump($jsonnya);die();
											// echo "<pre>";

											if (isset($jsonnya->auto_last_mvd)) {
												$autolastfuel = $jsonnya->auto_last_mvd;
											}else {
												$autolastfuel = "";
											}

											if ($autolastfuel != "") {
												if ($autolastfuel > 0 && $jsonnya->auto_last_speed == 0) {
													$fuelstatus = "OK";
												}else {
													$fuelstatus = "NOT OK";
												}
											}else {
												$fuelstatus = "NOT OK";
											}

											$vehicle_no = $masterdatavehicle[$j]->vehicle_no;

											$datafix = array(
												 "fuelcheck_vehicle_id"          => $masterdatavehicle[$j]->vehicle_id,
												 "fuelcheck_vehicle_device"      => $masterdatavehicle[$j]->vehicle_device,
												 "fuelcheck_vehicle_no"          => $masterdatavehicle[$j]->vehicle_no,
												 "fuelcheck_vehicle_name"        => $masterdatavehicle[$j]->vehicle_name,
												 "fuelcheck_vehicle_companyid"   => $masterdatavehicle[$j]->vehicle_company,
												 "fuelcheck_vehicle_companyname" => $company[$i]->company_name,
												 "fuelcheck_status"              => $fuelstatus,
												 "fuelcheck_speed"               => $jsonnya->auto_last_speed,
												 "fuelcheck_liter"               => $autolastfuel,
												 "fuelcheck_date_real" 			 		=> date("Y-m-d H:i:s", strtotime("+1 Hour")), // WITA
												 "fuelcheck_submitted_date"      => date("Y-m-d H:i:s", strtotime("+1 Hour")), // WITA
											);

											$insert = $this->insertDataUmum("tensor_report", $dbtable, $datafix);
												if ($insert) {
													print_r("BERHASIL INSERT DATA FUEL SENSOR STATUS " . $masterdatavehicle[$j]->vehicle_no . " \r\n");
													if ($fuelstatus == "NOT OK") {
														// $info = $vehicle_no." - "."Status : ".$fuelstatus.", Liter : ".$autolastfuel." \n";
														$info = $vehicle_no." - "."Status : ".$fuelstatus." \n";


														array_push($dataforsenttelegram,$info);
														printf("================================= \r\n");
													}
												}else {
													print_r("FAILED INSERT DATA FUEL SENSOR STATUS " . $masterdatavehicle[$i]['vehicle_no'] . " \r\n");
												}
									}
								}

								// FOR SENT TELEGRAM
								if (count($dataforsenttelegram) > 0) {
									for ($k=0;$k<count($dataforsenttelegram);$k++)
									{	usleep(500);
										$no_urut = $k+1;
										$text_info .= $no_urut.". ".$dataforsenttelegram[$k];
									}

									$total_fuelsensor = count($dataforsenttelegram);

									//send telegram
									$title_name = "CONTRACTOR ".$company[$i]->company_name;
										$message = urlencode(
												"".$title_name." \n".
												"TOTAL UNIT W/ FUEL SENSOR : ".sizeof($masterdatavehicle)." \n".
												"TANGGAL INFORMASI : ".$nowtime_wita." WITA"." \n".
												"DATA NOT OK : \n".$text_info." \n".
												"TOTAL NOT OK : ".$total_fuelsensor." \n"
											);

											// echo "<pre>";
											// var_dump($message);die();
											// echo "<pre>";

									sleep(2);
									$sendtelegram = $this->telegram_direct("-597763457",$message); // FMS FUEL SENSOR HISTORY CRON
									printf("===SENT TELEGRAM OK\r\n");
								}
							}
					}
			}else {
				printf("===START BACKDATE CRON \n");
				$rows = array();
				$rows2 = array();
				$total_q = 0;
				$total_q2 = 0;

				$error = "";
				$rows_summary = "";

				$location_list = array("location","location_off","location_idle");

				$company             = $this->getAllCompany($userid);
				$totalcompany        = count($company);
				printf("===TOTAL COMPANY %s \r\n", $totalcompany);
				$sdate = $startdate;

					// for ($i=0; $i < sizeof($company); $i++) {
						for ($i=0; $i < 1; $i++) {
						usleep(500);
						$company_id   = $company[$i]->company_id;
						$company_name = $company[$i]->company_name;
						$company_id      = 1947;
						$company_name    = "RBT";
						$masterdatavehicle   = $this->getAllVehicle_fuelsensoronly($company_id, $userid);
							if (sizeof($masterdatavehicle) > 0) {
								$dataforsenttelegram = array();
								$text_info           = "";
								$datafuelsensorok = array();
								for ($j=0; $j < sizeof($masterdatavehicle); $j++) {
									usleep(500);
									$data_array_before_insert = array();
									$datafix             = array();
									// for ($j=0; $j < 1; $j++) {
									print_r("VEHICLE NO           : ". $masterdatavehicle[$j]->vehicle_no . "\r\n");
									$vehicle                = $masterdatavehicle[$j]->vehicle_device;
									$dataFromLocationReport = $this->getFromLocationReport($dbbackdate, $vehicle, $sdate);

										if (sizeof($dataFromLocationReport) > 0) {
											for ($k=0; $k < sizeof($dataFromLocationReport); $k++) {
												usleep(500);
												$autolastspeed = $dataFromLocationReport[$k]->location_report_speed;
												$autolastfuel  = $dataFromLocationReport[$k]->location_report_fuel_data;

												if ($autolastfuel != "") {
													if ($autolastfuel > 0 && $autolastspeed == 0) {
														$fuelstatus = "OK";
													}else {
														$fuelstatus = "NOT OK";
													}
												}else {
													$fuelstatus = "NOT OK";
												}

												$vehicle_no = $masterdatavehicle[$j]->vehicle_no;

												array_push($data_array_before_insert, array(
													"fuelcheck_hour_check" 			    => $dataFromLocationReport[$k]->hour, // WITA
													"fuelcheck_vehicle_id"          => $masterdatavehicle[$j]->vehicle_id,
													"fuelcheck_vehicle_device"      => $masterdatavehicle[$j]->vehicle_device,
													"fuelcheck_vehicle_no"          => $masterdatavehicle[$j]->vehicle_no,
													"fuelcheck_vehicle_name"        => $masterdatavehicle[$j]->vehicle_name,
													"fuelcheck_vehicle_companyid"   => $masterdatavehicle[$j]->vehicle_company,
													"fuelcheck_vehicle_companyname" => $company_name,
													"fuelcheck_status"              => $fuelstatus,
													"fuelcheck_speed"               => $autolastspeed,
													"fuelcheck_liter"               => $autolastfuel,
													"fuelcheck_is_match" 			      => 0,
													"fuelcheck_date_real" 			    => $dataFromLocationReport[$k]->location_report_gps_time, // WITA
													"fuelcheck_submitted_date"      => date("Y-m-d H:i:s", strtotime("+1 Hour")), // WITA
												));

											}
										}

										$totaldataarray = sizeof($data_array_before_insert);

										if (sizeof($data_array_before_insert) < 24) {
											$hourindata = array();
											foreach ($data_array_before_insert as $childArray)
											{
												usleep(500);
											    $hourindata[] = $childArray['fuelcheck_hour_check'];
											}
											for ($x=0; $x < 24; $x++) {
												usleep(500);
												if (!in_array($x, $hourindata)) {
														if ($x == 0) {
															$datetimenya = $sdate." 00:00:00";
														}else {
															if ($x < 10) {
																$datetimenya = $sdate." 0".$x.":00:00";
															}else {
																$datetimenya = $sdate." ".$x.":00:00";
															}
														}
													array_push($data_array_before_insert, array(
														"fuelcheck_vehicle_id"          => $masterdatavehicle[$j]->vehicle_id,
														"fuelcheck_vehicle_device"      => $masterdatavehicle[$j]->vehicle_device,
														"fuelcheck_vehicle_no"          => $masterdatavehicle[$j]->vehicle_no,
														"fuelcheck_vehicle_name"        => $masterdatavehicle[$j]->vehicle_name,
														"fuelcheck_vehicle_companyid"   => $masterdatavehicle[$j]->vehicle_company,
														"fuelcheck_vehicle_companyname" => $company_name,
														"fuelcheck_status"              => "NOT OK",
														"fuelcheck_speed"               => 0,
														"fuelcheck_liter"               => 0,
														"fuelcheck_date_real" 			    => $datetimenya, // WITA
														"fuelcheck_is_match" 			      => 1,
														"fuelcheck_submitted_date"      => date("Y-m-d H:i:s", strtotime("+1 Hour")), // WITA
													));
												}
											}
										}

										for ($y=0; $y < sizeof($data_array_before_insert); $y++) {
											usleep(500);
											$datafix = array(
												"fuelcheck_vehicle_id"          => $data_array_before_insert[$y]['fuelcheck_vehicle_id'],
												"fuelcheck_vehicle_device"      => $data_array_before_insert[$y]['fuelcheck_vehicle_device'],
												"fuelcheck_vehicle_no"          => $data_array_before_insert[$y]['fuelcheck_vehicle_no'],
												"fuelcheck_vehicle_name"        => $data_array_before_insert[$y]['fuelcheck_vehicle_name'],
												"fuelcheck_vehicle_companyid"   => $data_array_before_insert[$y]['fuelcheck_vehicle_companyid'],
												"fuelcheck_vehicle_companyname" => $data_array_before_insert[$y]['fuelcheck_vehicle_companyname'],
												"fuelcheck_status"              => $data_array_before_insert[$y]['fuelcheck_status'],
												"fuelcheck_speed"               => $data_array_before_insert[$y]['fuelcheck_speed'],
												"fuelcheck_liter"               => $data_array_before_insert[$y]['fuelcheck_liter'],
												"fuelcheck_date_real" 			    => $data_array_before_insert[$y]['fuelcheck_date_real'],
												"fuelcheck_is_match" 			      => $data_array_before_insert[$y]['fuelcheck_is_match'],
												"fuelcheck_submitted_date"      => $data_array_before_insert[$y]['fuelcheck_submitted_date'],
											);

											$check_data = $this->check_data_fuelsensor("tensor_report", $dbtable, $data_array_before_insert[$y]['fuelcheck_vehicle_device'], $data_array_before_insert[$y]['fuelcheck_date_real']);
												if (sizeof($check_data) < 1) {
													$insert = $this->insertDataUmum("tensor_report", $dbtable, $datafix);
														if ($insert) {
															print_r("BERHASIL INSERT DATA FUEL SENSOR STATUS " . $data_array_before_insert[$y]['fuelcheck_vehicle_no'] . " \r\n");
														}else {
															print_r("FAILED INSERT DATA FUEL SENSOR STATUS " . $data_array_before_insert[$y]['fuelcheck_vehicle_no'] . " \r\n");
														}
												}else {
													$update = $this->updateDataUmum("tensor_report", $dbtable, $data_array_before_insert[$y]['fuelcheck_vehicle_device'], $data_array_before_insert[$y]['fuelcheck_date_real'], $datafix);
														if ($update) {
															print_r("BERHASIL UPDATE DATA BACKDATE " . $data_array_before_insert[$y]['fuelcheck_vehicle_no'] . " \r\n");
														}else {
															print_r("FAILED UPDATE DATA BACKDATE " . $data_array_before_insert[$y]['fuelcheck_vehicle_no'] . " \r\n");
														}
												}
										}

								}
								// FOR SENT TELEGRAM
								// if (count($dataforsenttelegram) > 0) {
								// 	for ($k=0;$k<count($dataforsenttelegram);$k++)
								// 	{
								// 		$no_urut = $k+1;
								// 		$text_info .= $no_urut.". ".$dataforsenttelegram[$k];
								// 	}
								//
								// 	$total_fuelsensor = count($dataforsenttelegram);
								//
								// 	//send telegram
								// 	$title_name = "CONTRACTOR ".$company_name;
								// 		$message = urlencode(
								// 				"".$title_name." \n".
								// 				"TOTAL UNIT W/ FUEL SENSOR : ".sizeof($masterdatavehicle)." \n".
								// 				"TANGGAL INFORMASI : ".$nowtime_wita." WITA"." \n".
								// 				"DATA NOT OK : \n".$text_info." \n".
								// 				"TOTAL NOT OK : ".$total_fuelsensor." \n"
								// 			);
								//
								// 			// echo "<pre>";
								// 			// var_dump($message);die();
								// 			// echo "<pre>";
								//
								// 	sleep(2);
								// 	$sendtelegram = $this->telegram_direct("-597763457",$message); // FMS FUEL SENSOR HISTORY CRON
								// 	printf("===SENT TELEGRAM OK\r\n");
								// }
							}
					}


			}

				// echo "<pre>";
				// var_dump($datafuelsensorok);die();
				// echo "<pre>";

				print_r("CRON START : ". $cronstartdate . "\r\n");
				print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
				$finishtime   = date("Y-m-d H:i:s");
				$start_1      = dbmaketime($cronstartdate);
				$end_1        = dbmaketime($finishtime);
				$duration_sec = $end_1 - $start_1;
				if ($isbackdate == 0) {
					$judul = 	"CRON CHECK FUEL SENSOR STATUS \n";
				}else {
					$judul = 	"CRON CHECK FUEL SENSOR STATUS (BACKDATE) \n";
				}
				$message =  urlencode(
							$judul.
							// "Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
							// "Total Data Baru: ".$totaldata_baru." \n".
							"Start: ".$cronstartdate." \n".
							"Finish: ".date("Y-m-d H:i:s")." \n".
							"Latency: ".$duration_sec." s"." \n"
							);

				$sendtelegram = $this->telegram_direct("-597763457",$message); // FMS FUEL SENSOR HISTORY CRON
				printf("===SENT TELEGRAM OK\r\n");
		}

		// GET DATA VIOLATION HISTORIKAL BACKDATE
		function violationhistorikal_backdate($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON VIOLATION HISTORIKAL BACKDATE CHECK IS START : ". $cronstartdate . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON VIOLATION HISTORIKAL BACKDATE Start WIB          : ". $cronstartdate . "\r\n");
			print_r("CRON VIOLATION HISTORIKAL BACKDATE Start WITA           : ". $nowtime_wita . "\r\n");

			if ($startdate == "") {
				$isbackdate  = 0;
				$start_range = date("Y-m-d");
				$m1          = date("F", strtotime($nowtime_wita));
				$year        = date("Y", strtotime($nowtime_wita));
			}else {
				$isbackdate  = 1;
				$start_range = $startdate;
				$m1          = date("F", strtotime($start_range));
				$year        = date("Y", strtotime($start_range));
			}

			$dbtable   = "";
			$report    = "historikal_violation_";

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

			$violationbackdate = $this->m_violation->violationbackdate($dbtable, $startdate);

				for ($i=0; $i < sizeof($violationbackdate); $i++) {
					usleep(500);
					// for ($i=0; $i < 2; $i++) {
					$violation_id     = $violationbackdate[$i]['violation_id'];
					$data_json        = json_decode($violationbackdate[$i]['violation_fatigue'], true);
					// $data_latitude    = str_replace("-","",$data_json[0]["gps_latitude_real"]);
					$data_latitude    = $data_json[0]["gps_latitude_real"];
					$data_longitude   = $data_json[0]["gps_longitude_real"];


					// if (strpos($data_latitude, "-") !== true) {
					// 	$data_latitude = "-".$data_latitude;
					// }

					$positionalert     = $this->gpsmodel->GeoReverse($data_latitude, $data_longitude);
					if ($positionalert->display_name != "Unknown Location!") {
						$positionexplode = explode(",", $positionalert->display_name);
						$position = $positionexplode[0];
					}else {
						$position = $positionalert->display_name;
					}

					$data_forupdate = array(
						"violation_position"    => $position,
						"violation_status_tele" => 1,
					);

					$update = $this->update_data_violationbackdate("tensor_report", $dbtable, $violation_id, $data_forupdate);
						if ($update) {
							print_r("SUCCESS UPDATE VIOLATION ID : ". $violation_id . "\r\n");
						}else {
							print_r("FAILED UPDATE VIOLATION ID : ". $violation_id . "\r\n");
						}
				}

				sleep(2);
				print_r("CRON START : ". $cronstartdate . "\r\n");
				print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
				$finishtime   = date("Y-m-d H:i:s");
				$start_1      = dbmaketime($cronstartdate);
				$end_1        = dbmaketime($finishtime);
				$duration_sec = $end_1 - $start_1;
				$judul = 	"VIOLATION MONITORING HISTORIKAL (BACKDATE) \n";
				$message =  urlencode(
							$judul.
							// "Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
							// "Total Data Baru: ".$totaldata_baru." \n".
							"Start: ".$cronstartdate." \n".
							"Finish: ".date("Y-m-d H:i:s")." \n".
							"Latency: ".$duration_sec." s"." \n"
							);

				$sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
				printf("===SENT TELEGRAM OK\r\n");
		}

		function violationhistorikalcollectyesterday($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON VIOLATION HISTORIKAL H-1 CHECK IS START : ". $cronstartdate . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON VIOLATION HISTORIKAL H-1 Start WIB          : ". $cronstartdate . "\r\n");
			print_r("CRON VIOLATION HISTORIKAL H-1 Start WITA           : ". $nowtime_wita . "\r\n");

			$datafix           = array();
			$masterdatavehicle = $this->getAllVehicle_mdvronly($userid);
			$violationmaster   = $this->m_violation->getviolationmaster();

			// GET ALARM MASTER
			$alarmtypefromaster = array();
				for ($j=0; $j < sizeof($violationmaster); $j++) {
					usleep(500);
					$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
					for ($k=0; $k < sizeof($alarmbymaster); $k++) {
						$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
					}
				}

			// CONVERT ARRAY ALARM KE STRING
			$alarmtypefix = implode (",", $alarmtypefromaster);

			// echo "<pre>";
			// var_dump($alarmtypefix);die();
			// echo "<pre>";

			// LOGIN API
			$username        = "DEMOPOC";
			$password        = "000000";
			$loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
			$loginbarudecode = json_decode($loginbaru);
			$jsession        = $loginbarudecode->jsession;

			// echo "<pre>";
			// var_dump($loginbaru);die();
			// echo "<pre>";

			$date       = date("Y-m-d", strtotime($startdate . "-1 Days"));
			// $time       = date("H:i:s", strtotime("-2hours"));
			$time       = "00:00:00";
			$startdate  = $date.'%20'.$time;

			$edate      = date("Y-m-d", strtotime($startdate . "-1 Days"));
			$etime      = "23:59:59";
			$enddate    = $edate.'%20'.$etime;
			$updatetime = "2020-07-23%2023:59:59";

			// $startdate = "2022-12-31%2009:40:00";
			// $enddate   = "2022-12-31%2010:50:00";
			// "2022-12-31%2010:24:21-2022-12-31%2011:34:21";

			// echo "<pre>";
			// var_dump($startdate.'-'.$enddate);die();
			// echo "<pre>";

			// CHOOSE DBTABLE
			$m1        = date("F", strtotime($startdate));
			$year      = date("Y", strtotime($startdate));
			$dbtable   = "";
			$report    = "historikal_violation_";

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

			// echo "<pre>";
			// var_dump($dbtable);die();
			// echo "<pre>";
			$totaldata_baru = 0;
				for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
					usleep(500);
					// for ($i=0; $i < 1; $i++) {
					$autocheck                 = json_decode($masterdatavehicle[$i]->vehicle_autocheck);
					$isinhauling               = $autocheck->auto_last_hauling;
					$lastcourse 							 = $autocheck->auto_last_course;
					$jalur_namefix 				     = $this->m_securityevidence->get_jalurname($lastcourse);
					// {"":"P","auto_last_update":"2022-05-24 14:24:06","auto_last_check":"2022-05-24 00:00:00","auto_last_position":"EST ROAD  BANJAR KALIMANTAN SELATAN","auto_last_lat":"-3.502445","auto_last_long":"115.593888","auto_last_engine":"ON","auto_last_gpsstatus":"OK","auto_last_speed":"15","auto_last_course":"128","auto_last_road":"muatan","auto_last_hauling":"out","auto_last_rom_name":"ROM A1","auto_last_rom_time":"2022-05-22 08:53:55","auto_last_port_name":"PORT BIB","auto_last_port_time":"2022-05-24 12:44:37","auto_flag":0,"vehicle_gotohistory":0,"auto_change_engine_status":"ON","auto_change_engine_datetime":"2022-05-24 14:23:33","auto_change_position":"KM 19.5,  TANAH BUMBU KALIMANTAN SELATAN","auto_change_coordinate":"-3.57265,115.655123"}"

						if ($isinhauling == "in") {
							print_r(($i+1). " OF " .sizeof($masterdatavehicle). "\r\n");
							print_r("===================================================== \r\n");
							print_r(" Vehicle : " .$masterdatavehicle[$i]->vehicle_no.'-'.$masterdatavehicle[$i]->vehicle_name. "\r\n");
							// $deviceID = "819058546530";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1207
							// $deviceID = "819058611680";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
							// $deviceID = "819058587559";//$masterdatavehicle[$i]->vehicle_mv03; BBS 1209
							$deviceID = $masterdatavehicle[$i]->vehicle_mv03; //"819058587559" 142045031243;//$masterdatavehicle[$i]->vehicle_mv03; BKA
								if ($deviceID != 0000) {
									$datafromapi = file_get_contents("http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=1000&toMap=1&checkend=0&updatetime=".$updatetime."&language=en");
										if ($datafromapi) {
											$createdurl = "http://172.16.1.2/StandardApiAction_queryAlarmDetail.action"."?jsession=".$jsession."&devIdno=".$deviceID."&begintime=".$startdate."&endtime=".$enddate."&armType=".$alarmtypefix."&handle=0&currentPage=1&pageRecords=5&toMap=1&checkend=0&updatetime=".$updatetime."&language=en";
											$fromapidecode     = json_decode($datafromapi);
											// print_r(" URL CREATED " .$createdurl. "\r\n");

											if (isset($fromapidecode->alarms) != NULL) {
												print_r(" NO DATA ALARM \r\n");
												print_r(" ===== FATIGUE FOUNDED =====\r\n");
												print_r(" URL CREATED " .$createdurl. "\r\n");
												$alarmcallback     = $fromapidecode->alarms;
												$totaldatacallback = sizeof($alarmcallback);

												echo "<pre>";
												var_dump($alarmcallback[0]);die();
												echo "<pre>";
											}
										}
									}
								}
							}
			print_r(" CRON FINISH \r\n");
		}
		// GET DATA VIOLATION HISTORIKAL BACKDATE

		// CRON SUBMIT DATA HAZARD START
		function submithazard_1($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON SUBMIT HAZARD TIPE 1 IS START : ". $cronstartdate . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON SUBMIT HAZARD TIPE 1 Start WIB          : ". $cronstartdate . "\r\n");
			print_r("CRON SUBMIT HAZARD TIPE 1 Start WITA           : ". $nowtime_wita . "\r\n");

			$status_dev = 200;
			// $login_hazard = $this->loginforhazard(); // AKTIFKAN SAAT LIVE

			// print_r("TOKEN AFTER LOGIN : ". $login_hazard->data->token . "\r\n"); // AKTIFKAN SAAT LIVE
			//
			// if ($login_hazard->status == 200) { // AKTIFKAN SAAT LIVE
				if ($status_dev == 200) {
				print_r("===== LOGIN BERHASIL \r\n");

				// $token_after_login = $login_hazard->data->token; // AKTIFKAN SAAT LIVE
				$token_after_login = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiRElDS1kgSVJXQU5UTyIsImlkIjo2ODM0LCJpc2FmZV9ubyI6IkI0NDEyMyIsInJvbGVJZCI6MSwiaWF0IjoxNjc3NTcxOTA3LCJleHAiOjE2ODAxNjM5MDd9.XVPekBApsH4Q3KKhcRWBP_BaqyeN8ZPK8WaJuei1wdY";

				if ($startdate == "") {
					$sdate = date("Y-m-d 18:00:00", strtotime("-1 Days"));
					$edate = date("Y-m-d 06:00:00");
				}else {
					$sdate = date("Y-m-d 18:00:00", strtotime("-1 Days" . $startdate));
					$edate = date("Y-m-d 06:00:00", strtotime($startdate));
				}

				print_r("TANGGAL YANG DICARI START           : ". $sdate . "\r\n");
				print_r("TANGGAL YANG DICARI END           : ". $edate . "\r\n");

				$report          = "alarm_evidence_";
				$reportoverspeed = "overspeed_";
				$report_sum      = "summary_";
				$m1              = date("F", strtotime($sdate));
				$m2              = date("F", strtotime($edate));
				$year            = date("Y", strtotime($sdate));
				$year2           = date("Y", strtotime($edate));
				$rows            = array();
				$total_q         = 0;

				switch ($m1)
				{
					case "January":
		            $dbtable = $report."januari_".$year;
								$dbtableoverspeed = $reportoverspeed."januari_".$year;
					break;
					case "February":
		            $dbtable = $report."februari_".$year;
								$dbtableoverspeed = $reportoverspeed."februari_".$year;
					break;
					case "March":
		            $dbtable = $report."maret_".$year;
								$dbtableoverspeed = $reportoverspeed."maret_".$year;
					break;
					case "April":
		            $dbtable = $report."april_".$year;
								$dbtableoverspeed = $reportoverspeed."april_".$year;
					break;
					case "May":
		            $dbtable = $report."mei_".$year;
								$dbtableoverspeed = $reportoverspeed."mei_".$year;
					break;
					case "June":
		            $dbtable = $report."juni_".$year;
								$dbtableoverspeed = $reportoverspeed."juni_".$year;
					break;
					case "July":
		            $dbtable = $report."juli_".$year;
								$dbtableoverspeed = $reportoverspeed."juli_".$year;
					break;
					case "August":
		            $dbtable = $report."agustus_".$year;
								$dbtableoverspeed = $reportoverspeed."agustus_".$year;
					break;
					case "September":
		            $dbtable = $report."september_".$year;
								$dbtableoverspeed = $reportoverspeed."september_".$year;
					break;
					case "October":
		            $dbtable = $report."oktober_".$year;
								$dbtableoverspeed = $reportoverspeed."oktober_".$year;
					break;
					case "November":
		            $dbtable = $report."november_".$year;
								$dbtableoverspeed = $reportoverspeed."november_".$year;
					break;
					case "December":
		            $dbtable = $report."desember_".$year;
								$dbtableoverspeed = $reportoverspeed."desember_".$year;
					break;
				}

				// GET ALARM MASTER
				$violationmaster   = $this->m_violation->getviolationmaster();
				$alarmtypefromaster = array();
					for ($j=0; $j < sizeof($violationmaster); $j++) {
						usleep(500);
						$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
						for ($k=0; $k < sizeof($alarmbymaster); $k++) {
							$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
						}
					}

				// CONVERT ARRAY ALARM KE STRING
				$alarmtypefix_1    = $this->config->item('submit_hazard_register_1');
				$total_alarm_type1 = sizeof($alarmtypefix_1);
				$alarmtypefix_2    = $this->config->item('submit_hazard_register_2');
				$total_alarm_type2 = sizeof($alarmtypefix_2);

				$street_register 	 = $this->config->item('street_register');
				$datafix           = array();
				$masterdatavehicle = $this->getAllVehicle_mdvronly_hazard($userid);
				$total_mdvr_only   = sizeof($masterdatavehicle);

				// echo "<pre>";
				// var_dump($total_mdvr_only);die();
				// echo "<pre>";

				for ($i=0; $i < $total_mdvr_only; $i++) {
					usleep(500);
					$vehicle_imei   = $masterdatavehicle[$i]->vehicle_imei;
					// KONDISI UNTUK GET DATA FATIGUE
					$array_data_hazard = array();
						for ($j=0; $j < $total_alarm_type1; $j++) {
							usleep(500);
							print_r("==================================== \r\n");
							print_r("===== DATA LEVEL 2 DICARI \r\n");
							print_r("===== SEARCH VEHICLE ID : " . $vehicle_imei . " \r\n");
							print_r("===== SEARCH VEHICLE NO : " . $masterdatavehicle[$i]->vehicle_no . " ALERT : " . $alarmtypefix_2[$j] . " \r\n");
							$get_data_evidence     = $this->do_get_hazard_evidence($dbtable, $vehicle_imei, $sdate, $edate, $alarmtypefix_2[$j]);
							$data_evidence         = array();
								if (sizeof($get_data_evidence) > 0) {
									print_r("===== DATA LEVEL 2 DITEMUKAN \r\n");
									$data_evidence = $get_data_evidence;
								}else {
									print_r("===== DATA LEVEL 1 DICARI \r\n");
									print_r("===== SEARCH VEHICLE NO : " . $masterdatavehicle[$i]->vehicle_no . " ALERT : " . $alarmtypefix_1[$j] . " \r\n");
									$get_data_evidence = $this->do_get_hazard_evidence($dbtable, $vehicle_imei, $sdate, $edate, $alarmtypefix_1[$j]);
										if (sizeof($get_data_evidence) > 0) {
											print_r("===== DATA LEVEL 1 DITEMUKAN \r\n");
											$data_evidence = $get_data_evidence;
										}else {
											print_r("===== DATA LEVEL 1 & 2 TIDAK DITEMUKAN \r\n");
										}
								}

								if (sizeof($data_evidence) > 0) {
									// echo "<pre>";
									// var_dump($data_evidence);die();
									// echo "<pre>";
									$dataforsubmitevidence = array();
								  $coordinate_exp     = explode(",",$data_evidence[0]['alarm_report_coordinate_start']);
								  $alert_name_exp     = explode("Start", $data_evidence[0]['alarm_report_name']);
									$reportdetaildecode = explode("|", $data_evidence[0]['alarm_report_gpsstatus']);
									$speedgps           = number_format($reportdetaildecode[4]/10, 1, '.', '');
									print_r("===== TITLE YANG AKAN DIKIRIM ".$data_evidence[0]['alarm_report_vehicle_no'].' '.$alert_name_exp[0].date("d-m-Y H:i:s", strtotime($data_evidence[0]['alarm_report_start_time']))."\r\n");

								  $dataforsubmitevidence = array(
								    "id"              => null,
										// "title" 					=> "Test #".rand(100,1000),
										// "title"           => "Testing 28022023",
										"title"           => $data_evidence[0]['alarm_report_vehicle_no'].' '.$alert_name_exp[0].date("d-m-Y H:i:s", strtotime($data_evidence[0]['alarm_report_start_time'])),
								    "description"     => "There's violation happened within Hauling Operation with following detail; DateTime Event: ".$data_evidence[0]['alarm_report_start_time']."; Vehicle No: ".$data_evidence[0]['alarm_report_vehicle_no']." ".$data_evidence[0]['alarm_report_vehicle_name']."; Driver Name: -; Position/Location Name: ".$data_evidence[0]['alarm_report_location_start']."; Speed (Kph) : ".$speedgps."; Coordinate: https://www.google.com/maps/search/?api=1&query=".$data_evidence[0]['alarm_report_coordinate_start']."; Evidence: ".$data_evidence[0]['alarm_report_name'],
										// "description" 		=> "There's violation happened within Hauling Operation with following detail; DateTime Event: 18-01-2023 12:04:51; Vehicle No: BBS 1221 Hino 500; Driver Name: -; Position/Location Name:  KM 20; Speed (Kph) :  48; Coordinate:  https://www.google.com/maps/search/?api=1&query=-3.566814,115.654956; Evidence: Text Mode",
								    "ketidaksesuaian" => "TTA",
								    "id_category"     => 24,
								    "subcategory"     => "tes",
								    "id_location"     => 200,
								    "id_sublocation"  => 1489,
								    "detail"          => "tes detail",
								    "photos"          => array("image20_1589542177477_V6PUj.jpg"),
								    "alat_pelaku"     => null,
								    "risk"            => 1,
								    "created_at"      => strtotime($data_evidence[0]['alarm_report_start_time']),
								    "updated_at"      => strtotime($data_evidence[0]['alarm_report_start_time']),
								    "status"          => "WAITING",
								    "reason"          => null,
								    "nc"              => array(),
								    "assign_to"       => "B44123",
								    "longitude"       => $coordinate_exp[1],
								    "latitude"        => $coordinate_exp[0]
								  );

									$data_json          = json_encode($dataforsubmitevidence);
									sleep(5);
								  $submit_this_hazard = $this->submit_hazard($token_after_login, $dataforsubmitevidence);
								  // echo "<pre>";
								  // var_dump($dataforsubmitevidence);die();
								  // echo "<pre>";
									print_r("===== RESPONSE STATUS ".$submit_this_hazard->status."\r\n");
									print_r("===== RESPONSE MESSAGE ".$submit_this_hazard->message."\r\n");
									// print_r("===== TITLE YANG DIKIRIM ".$dataforsubmitevidence['title']."\r\n");
									// exit();

								  if ($submit_this_hazard->status == 201) {
										print_r("BERHASIL SUBMIT DATA TO ISAFE \r\n");
								    $save_tohistorikal = array(
								      // "hazard_id"              => $dataforsubmitevidence['id'],
								      "hazard_title"           => $dataforsubmitevidence['title'],
								      "hazard_ketidaksesuaian" => $dataforsubmitevidence['ketidaksesuaian'],
								      "hazard_id_category"     => $dataforsubmitevidence['id_category'],
								      "hazard_subcategory"     => $dataforsubmitevidence['subcategory'],
								      "hazard_id_location"     => $dataforsubmitevidence['id_location'],
								      "hazard_id_sublocation"  => $dataforsubmitevidence['id_sublocation'],
								      "hazard_status"          => $dataforsubmitevidence['status'],
								      "hazard_json"            => $data_json,
								      "hazard_result"          => json_encode($submit_this_hazard),
								      "hazard_created_date"    => date("Y-m-d H:i:s", strtotime("+1 Hour")),
								    );
								    $insert = $this->insertDataUmum("tensor_report", "historikal_hazard_submit", $save_tohistorikal);
								    if ($insert) {
								      print_r("BERHASIL INSERT DATA TO HISTORIKAL \r\n");
								    }else {
								      print_r("FAILED INSERT DATA TO HISTORIKAL \r\n");
								    }
								  }elseif($submit_this_hazard->status != 200 && $submit_this_hazard->status != 201) {
										print_r("FAILED SUBMIT DATA TO ISAFE ".$submit_this_hazard." \r\n");
								  }
								}
						}
				}
				print_r("CRON START : ". $cronstartdate . "\r\n");
				print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
				$finishtime   = date("Y-m-d H:i:s");
				$start_1      = dbmaketime($cronstartdate);
				$end_1        = dbmaketime($finishtime);
				$duration_sec = $end_1 - $start_1;
				$judul = 	"CRON SUBMIT HAZARD TIPE 1 \n";
				$message =  urlencode(
							$judul.
							// "Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
							// "Total Data Baru: ".$totaldata_baru." \n".
							"Start: ".$cronstartdate." \n".
							"Finish: ".date("Y-m-d H:i:s")." \n".
							"Latency: ".$duration_sec." s"." \n"
							);

				$sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
				printf("===SENT TELEGRAM OK\r\n");
				$this->submithazard_overspeed($userid, $sdate, $edate, $token_after_login);
			}else {
				print_r("===== LOGIN GAGAL CODE : " . $login_hazard->status ." \r\n");
				print_r("===== LOGIN GAGAL MESSAGE : " . $login_hazard->message ." \r\n");
			}
		}

		function submithazard_2($userid, $startdate = ""){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON SUBMIT HAZARD TIPE 2 IS START : ". $cronstartdate . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON SUBMIT HAZARD TIPE 2 Start WIB          : ". $cronstartdate . "\r\n");
			print_r("CRON SUBMIT HAZARD TIPE 2 Start WITA           : ". $nowtime_wita . "\r\n");

			$status_dev = 200;
			$login_hazard = $this->loginforhazard(); // AKTIFKAN SAAT LIVE

			print_r("TOKEN AFTER LOGIN : ". $login_hazard->data->token . "\r\n"); // AKTIFKAN SAAT LIVE

			if ($login_hazard->status == 200) { // AKTIFKAN SAAT LIVE
				// if ($status_dev == 200) {
				print_r("===== LOGIN BERHASIL \r\n");

				$token_after_login = $login_hazard->data->token; // AKTIFKAN SAAT LIVE
				// $token_after_login = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiRElDS1kgSVJXQU5UTyIsImlkIjo2ODM0LCJpc2FmZV9ubyI6IkI0NDEyMyIsInJvbGVJZCI6MSwiaWF0IjoxNjc3NTcxOTA3LCJleHAiOjE2ODAxNjM5MDd9.XVPekBApsH4Q3KKhcRWBP_BaqyeN8ZPK8WaJuei1wdY";

				if ($startdate == "") {
					$sdate = date("Y-m-d 06:00:00");
					$edate = date("Y-m-d 18:00:00");
				}else {
					$sdate = date("Y-m-d 06:00:00", strtotime($startdate));
					$edate = date("Y-m-d 18:00:00", strtotime($startdate));
				}

				print_r("TANGGAL YANG DICARI START           : ". $sdate . "\r\n");
				print_r("TANGGAL YANG DICARI END           : ". $edate . "\r\n");

				$report          = "alarm_evidence_";
				$reportoverspeed = "overspeed_";
				$report_sum      = "summary_";
				$m1              = date("F", strtotime($sdate));
				$m2              = date("F", strtotime($edate));
				$year            = date("Y", strtotime($sdate));
				$year2           = date("Y", strtotime($edate));
				$rows            = array();
				$total_q         = 0;

				switch ($m1)
				{
					case "January":
		            $dbtable = $report."januari_".$year;
								$dbtableoverspeed = $reportoverspeed."januari_".$year;
					break;
					case "February":
		            $dbtable = $report."februari_".$year;
								$dbtableoverspeed = $reportoverspeed."februari_".$year;
					break;
					case "March":
		            $dbtable = $report."maret_".$year;
								$dbtableoverspeed = $reportoverspeed."maret_".$year;
					break;
					case "April":
		            $dbtable = $report."april_".$year;
								$dbtableoverspeed = $reportoverspeed."april_".$year;
					break;
					case "May":
		            $dbtable = $report."mei_".$year;
								$dbtableoverspeed = $reportoverspeed."mei_".$year;
					break;
					case "June":
		            $dbtable = $report."juni_".$year;
								$dbtableoverspeed = $reportoverspeed."juni_".$year;
					break;
					case "July":
		            $dbtable = $report."juli_".$year;
								$dbtableoverspeed = $reportoverspeed."juli_".$year;
					break;
					case "August":
		            $dbtable = $report."agustus_".$year;
								$dbtableoverspeed = $reportoverspeed."agustus_".$year;
					break;
					case "September":
		            $dbtable = $report."september_".$year;
								$dbtableoverspeed = $reportoverspeed."september_".$year;
					break;
					case "October":
		            $dbtable = $report."oktober_".$year;
								$dbtableoverspeed = $reportoverspeed."oktober_".$year;
					break;
					case "November":
		            $dbtable = $report."november_".$year;
								$dbtableoverspeed = $reportoverspeed."november_".$year;
					break;
					case "December":
		            $dbtable = $report."desember_".$year;
								$dbtableoverspeed = $reportoverspeed."desember_".$year;
					break;
				}

				// GET ALARM MASTER
				$violationmaster   = $this->m_violation->getviolationmaster();
				$alarmtypefromaster = array();
					for ($j=0; $j < sizeof($violationmaster); $j++) {
						usleep(500);
						$alarmbymaster = $this->m_violation->getalarmbytype($violationmaster[$j]['alarmmaster_id']);
						for ($k=0; $k < sizeof($alarmbymaster); $k++) {
							$alarmtypefromaster[] = $alarmbymaster[$k]['alarm_type'];
						}
					}

				// CONVERT ARRAY ALARM KE STRING
				$alarmtypefix_1    = $this->config->item('submit_hazard_register_1');
				$total_alarm_type1 = sizeof($alarmtypefix_1);
				$alarmtypefix_2    = $this->config->item('submit_hazard_register_2');
				$total_alarm_type2 = sizeof($alarmtypefix_2);

				$street_register 	 = $this->config->item('street_register');
				$datafix           = array();
				$masterdatavehicle = $this->getAllVehicle_mdvronly_hazard($userid);
				$total_mdvr_only   = sizeof($masterdatavehicle);

				// echo "<pre>";
				// var_dump($total_mdvr_only);die();
				// echo "<pre>";

				for ($i=0; $i < $total_mdvr_only; $i++) {
					usleep(500);
					$vehicle_imei   = $masterdatavehicle[$i]->vehicle_imei;
					// KONDISI UNTUK GET DATA FATIGUE
					$array_data_hazard = array();
						for ($j=0; $j < $total_alarm_type1; $j++) {
							usleep(500);
							print_r("==================================== \r\n");
							print_r("===== DATA LEVEL 2 DICARI \r\n");
							print_r("===== SEARCH VEHICLE ID : " . $vehicle_imei . " \r\n");
							print_r("===== SEARCH VEHICLE NO : " . $masterdatavehicle[$i]->vehicle_no . " ALERT : " . $alarmtypefix_2[$j] . " \r\n");
							$get_data_evidence     = $this->do_get_hazard_evidence($dbtable, $vehicle_imei, $sdate, $edate, $alarmtypefix_2[$j]);
							$data_evidence         = array();
								if (sizeof($get_data_evidence) > 0) {
									print_r("===== DATA LEVEL 2 DITEMUKAN \r\n");
									$data_evidence = $get_data_evidence;
								}else {
									print_r("===== DATA LEVEL 1 DICARI \r\n");
									print_r("===== SEARCH VEHICLE NO : " . $masterdatavehicle[$i]->vehicle_no . " ALERT : " . $alarmtypefix_1[$j] . " \r\n");
									$get_data_evidence = $this->do_get_hazard_evidence($dbtable, $vehicle_imei, $sdate, $edate, $alarmtypefix_1[$j]);
										if (sizeof($get_data_evidence) > 0) {
											print_r("===== DATA LEVEL 1 DITEMUKAN \r\n");
											$data_evidence = $get_data_evidence;
										}else {
											print_r("===== DATA LEVEL 1 & 2 TIDAK DITEMUKAN \r\n");
										}
								}

								if (sizeof($data_evidence) > 0) {
									// echo "<pre>";
									// var_dump($data_evidence);die();
									// echo "<pre>";
									$dataforsubmitevidence = array();
								  $coordinate_exp     = explode(",",$data_evidence[0]['alarm_report_coordinate_start']);
								  $alert_name_exp     = explode("Start", $data_evidence[0]['alarm_report_name']);
									$reportdetaildecode = explode("|", $data_evidence[0]['alarm_report_gpsstatus']);
									$speedgps           = number_format($reportdetaildecode[4]/10, 1, '.', '');
									print_r("===== TITLE YANG AKAN DIKIRIM ".$data_evidence[0]['alarm_report_vehicle_no'].' '.$alert_name_exp[0].date("d-m-Y H:i:s", strtotime($data_evidence[0]['alarm_report_start_time']))."\r\n");

								  $dataforsubmitevidence = array(
								    "id"              => null,
										// "title" 					=> "Test #".rand(100,1000),
										// "title"           => "Testing 28022023",
										"title"           => $data_evidence[0]['alarm_report_vehicle_no'].' '.$alert_name_exp[0].date("d-m-Y H:i:s", strtotime($data_evidence[0]['alarm_report_start_time'])),
								    "description"     => "There's violation happened within Hauling Operation with following detail; DateTime Event: ".$data_evidence[0]['alarm_report_start_time']."; Vehicle No: ".$data_evidence[0]['alarm_report_vehicle_no']." ".$data_evidence[0]['alarm_report_vehicle_name']."; Driver Name: -; Position/Location Name: ".$data_evidence[0]['alarm_report_location_start']."; Speed (Kph) : ".$speedgps."; Coordinate: https://www.google.com/maps/search/?api=1&query=".$data_evidence[0]['alarm_report_coordinate_start']."; Evidence: ".$data_evidence[0]['alarm_report_name'],
										// "description" 		=> "There's violation happened within Hauling Operation with following detail; DateTime Event: 18-01-2023 12:04:51; Vehicle No: BBS 1221 Hino 500; Driver Name: -; Position/Location Name:  KM 20; Speed (Kph) :  48; Coordinate:  https://www.google.com/maps/search/?api=1&query=-3.566814,115.654956; Evidence: Text Mode",
								    "ketidaksesuaian" => "TTA",
								    "id_category"     => 1,
								    "subcategory"     => "tes",
								    "id_location"     => 200,
								    "id_sublocation"  => 1489,
								    "detail"          => "tes detail",
								    "photos"          => array("image20_1589542177477_V6PUj.jpg"),
								    "alat_pelaku"     => null,
								    "risk"            => 1,
								    "created_at"      => strtotime($data_evidence[0]['alarm_report_start_time']),
								    "updated_at"      => strtotime($data_evidence[0]['alarm_report_start_time']),
								    "status"          => "WAITING",
								    "reason"          => null,
								    "nc"              => array(),
								    "assign_to"       => "B44123",
								    "longitude"       => $coordinate_exp[1],
								    "latitude"        => $coordinate_exp[0]
								  );

									$data_json          = json_encode($dataforsubmitevidence);
									sleep(5);
								  $submit_this_hazard = $this->submit_hazard($token_after_login, $dataforsubmitevidence);
								  // echo "<pre>";
								  // var_dump($dataforsubmitevidence);die();
								  // echo "<pre>";
									print_r("===== RESPONSE STATUS ".$submit_this_hazard->status."\r\n");
									print_r("===== RESPONSE MESSAGE ".$submit_this_hazard->message."\r\n");
									// print_r("===== TITLE YANG DIKIRIM ".$dataforsubmitevidence['title']."\r\n");
									// exit();

								  if ($submit_this_hazard->status == 201) {
										print_r("BERHASIL SUBMIT DATA TO ISAFE \r\n");
								    $save_tohistorikal = array(
								      // "hazard_id"              => $dataforsubmitevidence['id'],
								      "hazard_title"           => $dataforsubmitevidence['title'],
								      "hazard_ketidaksesuaian" => $dataforsubmitevidence['ketidaksesuaian'],
								      "hazard_id_category"     => $dataforsubmitevidence['id_category'],
								      "hazard_subcategory"     => $dataforsubmitevidence['subcategory'],
								      "hazard_id_location"     => $dataforsubmitevidence['id_location'],
								      "hazard_id_sublocation"  => $dataforsubmitevidence['id_sublocation'],
								      "hazard_status"          => $dataforsubmitevidence['status'],
								      "hazard_json"            => $data_json,
								      "hazard_result"          => json_encode($submit_this_hazard),
								      "hazard_created_date"    => date("Y-m-d H:i:s", strtotime("+1 Hour")),
								    );
								    $insert = $this->insertDataUmum("tensor_report", "historikal_hazard_submit", $save_tohistorikal);
								    if ($insert) {
								      print_r("BERHASIL INSERT DATA TO HISTORIKAL \r\n");
								    }else {
								      print_r("FAILED INSERT DATA TO HISTORIKAL \r\n");
								    }
								  }elseif($submit_this_hazard->status != 200 && $submit_this_hazard->status != 201) {
										print_r("FAILED SUBMIT DATA TO ISAFE ".$submit_this_hazard." \r\n");
								  }
								}
						}
				}
				print_r("CRON START : ". $cronstartdate . "\r\n");
				print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
				$finishtime   = date("Y-m-d H:i:s");
				$start_1      = dbmaketime($cronstartdate);
				$end_1        = dbmaketime($finishtime);
				$duration_sec = $end_1 - $start_1;
				$judul = 	"CRON SUBMIT HAZARD TIPE 2 \n";
				$message =  urlencode(
							$judul.
							// "Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
							// "Total Data Baru: ".$totaldata_baru." \n".
							"Start: ".$cronstartdate." \n".
							"Finish: ".date("Y-m-d H:i:s")." \n".
							"Latency: ".$duration_sec." s"." \n"
							);

				$sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
				printf("===SENT TELEGRAM OK\r\n");
				$this->submithazard_overspeed($userid, $sdate, $edate, $token_after_login);
			}else {
				print_r("===== LOGIN GAGAL CODE : " . $login_hazard->status ." \r\n");
				print_r("===== LOGIN GAGAL MESSAGE : " . $login_hazard->message ." \r\n");
			}
		}

		function submithazard_overspeed($userid, $sdate, $edate, $token_after_login){
			date_default_timezone_set("Asia/Jakarta");
			$cronstartdate = date("Y-m-d H:i:s");
			print_r("CRON SUBMIT HAZARD OVERSPEED IS START : ". $cronstartdate . "\r\n");
			$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
			print_r("CRON SUBMIT HAZARD OVERSPEED Start WIB          : ". $cronstartdate . "\r\n");
			print_r("CRON SUBMIT HAZARD OVERSPEED Start WITA           : ". $nowtime_wita . "\r\n");

			// if ($startdate == "") {
			// 	$sdate = date("Y-m-d 18:00:00", strtotime("-1 Days"));
			// 	$edate = date("Y-m-d 06:00:00");
			// }else {
			// 	$sdate = date("Y-m-d 18:00:00", strtotime("-1 Days" . $startdate));
			// 	$edate = date("Y-m-d 06:00:00", strtotime($startdate));
			// }

			$reportoverspeed = "overspeed_hour_";
			$report_sum      = "summary_";
			$m1              = date("F", strtotime($sdate));
			$m2              = date("F", strtotime($edate));
			$year            = date("Y", strtotime($sdate));
			$year2           = date("Y", strtotime($edate));
			$rows            = array();
			$total_q         = 0;

			switch ($m1)
			{
				case "January":
							$dbtableoverspeed = $reportoverspeed."januari_".$year;
				break;
				case "February":
							$dbtableoverspeed = $reportoverspeed."februari_".$year;
				break;
				case "March":
							$dbtableoverspeed = $reportoverspeed."maret_".$year;
				break;
				case "April":
							$dbtableoverspeed = $reportoverspeed."april_".$year;
				break;
				case "May":
							$dbtableoverspeed = $reportoverspeed."mei_".$year;
				break;
				case "June":
							$dbtableoverspeed = $reportoverspeed."juni_".$year;
				break;
				case "July":
							$dbtableoverspeed = $reportoverspeed."juli_".$year;
				break;
				case "August":
							$dbtableoverspeed = $reportoverspeed."agustus_".$year;
				break;
				case "September":
							$dbtableoverspeed = $reportoverspeed."september_".$year;
				break;
				case "October":
							$dbtableoverspeed = $reportoverspeed."oktober_".$year;
				break;
				case "November":
							$dbtableoverspeed = $reportoverspeed."november_".$year;
				break;
				case "December":
							$dbtableoverspeed = $reportoverspeed."desember_".$year;
				break;
			}

			$masterdatavehicle = $this->getAllVehicle($userid);
			$totalvehicle      = sizeof($masterdatavehicle);

			// KONDISI UNTUK GET DATA OVERSPEED
			for ($i=0; $i < $totalvehicle; $i++) {
				usleep(500);
				$vehicle_device = $masterdatavehicle[$i]->vehicle_device;
				$getoverspeedalarm = $this->getoverspeedreport($dbtableoverspeed, $vehicle_device, $sdate, $edate);
				if (sizeof($getoverspeedalarm) > 0) {
						$jalur = $getoverspeedalarm[0]['overspeed_report_jalur'];

							$coordstart = $getoverspeedalarm[0]['overspeed_report_coordinate'];
								if (strpos($coordstart, '-') !== false) {
									$coordstart  = $coordstart;
								}else {
									$coordstart  = "-".$coordstart;
								}

							$coord       = explode(",", $coordstart);
							$position    = $this->gpsmodel->GeoReverse($coord[0], $coord[1]);
								if ($position->display_name != "Unknown Location!") {
									$positionexplode = explode(",", $position->display_name);
									$position = $positionexplode[0];
								}else {
									$position = $positionalert->display_name;
								}

						$speedgps           = $getoverspeedalarm[0]['overspeed_report_speed'];
						$coordinate_exp     = explode(",",$getoverspeedalarm[0]['overspeed_report_coordinate']);

						$dataforsubmitevidence = array(
							"id"              => null,
							// "title" 					=> "Test #".rand(100,1000),
							// "title"           => "Testing 28022023",
							"title"           => $getoverspeedalarm[0]['overspeed_report_vehicle_no'].' Overspeed Level '.$getoverspeedalarm[0]['overspeed_report_level'].' '.date("d-m-Y H:i:s", strtotime($getoverspeedalarm[0]['overspeed_report_gps_time'])),
							"description"     => "There's violation happened within Hauling Operation with following detail; DateTime Event: ".$getoverspeedalarm[0]['overspeed_report_gps_time']."; Vehicle No: ".$getoverspeedalarm[0]['overspeed_report_vehicle_no']." ".$getoverspeedalarm[0]['overspeed_report_vehicle_name']."; Driver Name: -; Position/Location Name: ".$position."; Speed (Kph) : ".$speedgps."; Coordinate: https://www.google.com/maps/search/?api=1&query=".$getoverspeedalarm[0]['overspeed_report_coordinate']."; Evidence: Overspeed Level ".$getoverspeedalarm[0]['overspeed_report_level'],
							// "description" 		=> "There's violation happened within Hauling Operation with following detail; DateTime Event: 18-01-2023 12:04:51; Vehicle No: BBS 1221 Hino 500; Driver Name: -; Position/Location Name:  KM 20; Speed (Kph) :  48; Coordinate:  https://www.google.com/maps/search/?api=1&query=-3.566814,115.654956; Evidence: Text Mode",
							"ketidaksesuaian" => "TTA",
							"id_category"     => 24,
							"subcategory"     => "tes",
							"id_location"     => 200,
							"id_sublocation"  => 1489,
							"detail"          => "tes detail",
							"photos"          => array("image20_1589542177477_V6PUj.jpg"),
							"alat_pelaku"     => null,
							"risk"            => 1,
							"created_at"      => strtotime($getoverspeedalarm[0]['overspeed_report_gps_time']),
							"updated_at"      => strtotime($getoverspeedalarm[0]['overspeed_report_gps_time']),
							"status"          => "WAITING",
							"reason"          => null,
							"nc"              => array(),
							"assign_to"       => "B44123",
							"longitude"       => $coordinate_exp[1],
							"latitude"        => $coordinate_exp[0]
						);

						$data_json          = json_encode($dataforsubmitevidence);
						sleep(5);
						$submit_this_hazard = $this->submit_hazard($token_after_login, $dataforsubmitevidence);

						// echo "<pre>";
						// var_dump($dataforsubmitevidence);die();
						// echo "<pre>";
						print_r("===== RESPONSE STATUS ".$submit_this_hazard->status."\r\n");
						print_r("===== RESPONSE MESSAGE ".$submit_this_hazard->message."\r\n");
						// print_r("===== TITLE YANG DIKIRIM ".$dataforsubmitevidence['title']."\r\n");
						// exit();

						if ($submit_this_hazard->status == 201) {
							print_r("BERHASIL SUBMIT DATA TO ISAFE \r\n");
							$save_tohistorikal = array(
								// "hazard_id"              => $dataforsubmitevidence['id'],
								"hazard_title"           => $dataforsubmitevidence['title'],
								"hazard_ketidaksesuaian" => $dataforsubmitevidence['ketidaksesuaian'],
								"hazard_id_category"     => $dataforsubmitevidence['id_category'],
								"hazard_subcategory"     => $dataforsubmitevidence['subcategory'],
								"hazard_id_location"     => $dataforsubmitevidence['id_location'],
								"hazard_id_sublocation"  => $dataforsubmitevidence['id_sublocation'],
								"hazard_status"          => $dataforsubmitevidence['status'],
								"hazard_json"            => $data_json,
								"hazard_result"          => json_encode($submit_this_hazard),
								"hazard_created_date"    => date("Y-m-d H:i:s", strtotime("+1 Hour")),
							);
							$insert = $this->insertDataUmum("tensor_report", "historikal_hazard_submit", $save_tohistorikal);
							if ($insert) {
								print_r("BERHASIL INSERT DATA TO HISTORIKAL \r\n");
							}else {
								print_r("FAILED INSERT DATA TO HISTORIKAL \r\n");
							}
						}elseif($submit_this_hazard->status != 200 && $submit_this_hazard->status != 201) {
							print_r("FAILED SUBMIT DATA TO ISAFE ".$submit_this_hazard." \r\n");
						}
				}
			}
			print_r("CRON START : ". $cronstartdate . "\r\n");
			print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
			$finishtime   = date("Y-m-d H:i:s");
			$start_1      = dbmaketime($cronstartdate);
			$end_1        = dbmaketime($finishtime);
			$duration_sec = $end_1 - $start_1;
			$judul = 	"CRON SUBMIT HAZARD OVERSPEED \n";
			$message =  urlencode(
						$judul.
						// "Total Data Dicheck: ".sizeof($masterdatavehicle)." \n".
						// "Total Data Baru: ".$totaldata_baru." \n".
						"Start: ".$cronstartdate." \n".
						"Finish: ".date("Y-m-d H:i:s")." \n".
						"Latency: ".$duration_sec." s"." \n"
						);

			$sendtelegram = $this->telegram_direct("-577190673",$message); // FMS VIOLATION CRON
			printf("===SENT TELEGRAM OK\r\n");
		}

		function loginforhazard(){
			$url_login   = "https://gcp-devapi.borneo-indobara.com/api/v4/login";
			$token 		   = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJrZXkiOiJleUpoYkdjaU9pSklVekkxTmlJc0luUjVjQ0k2SWtwWFZDSjkuZXlKaGNIQnpYMjVoYldVaU9pSm1iWE1pTENKcFlYUWlPakUyTnpNNE5qRTBORFY5LmRqalQ3S1A5YzFrdTZpMTRiZktaOXoxZk12Ni0tMUZFREo2RG90XzE0UmsiLCJpYXQiOjE2NzY5NTA2MDYsImV4cCI6MTY3NzAzNzAwNn0.1DIdXVMrLx1KfzbEwInSAXeS3cjGN53tSyOz4bjojug";
			$data        = array("username" => "B44123", "password" => "12345678");
			$data_param  = json_encode($data);

			$ch = curl_init($url_login);
			$headers = array(
						 "Accept: application/json",
						 "Content-Type: application/json",
						 "Authorization: Bearer " .$token,
					);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$result = curl_exec($ch);

			if ($result === FALSE) {
					die("Login failed: " . curL_error($ch) . " \r\n");
			}

			curl_close($ch);

			$obj = json_decode($result); //print_r($obj);exit();
			return $obj;
		}

		function submit_hazard($token, $dataforsent){
			$url_submit = "https://gcp-devapi.borneo-indobara.com/api/v4/hr/submit";
			$token 		  = $token;
			$data_param = json_encode($dataforsent);

			print_r($data_param." \r\n");

			// echo "<pre>";
			// var_dump($data_param);die();
			// echo "<pre>";

			$ch = curl_init($url_submit);
			$headers = array(
						 "Accept: application/json",
						 "Content-Type: application/json",
						 "Authorization: Bearer " .$token,
					);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$result = curl_exec($ch);

			if ($result === FALSE) {
					die("Submit Hazard failed: " . curL_error($ch). " \r\n");
			}

			curl_close($ch);

			$obj = json_decode($result);
			print_r($obj); //exit();
			print_r(" \r\n");
			return $obj;
			// return "";
		}

		function do_get_hazard_evidence($dbtable, $vehicleid, $sdate, $edate, $alarmtype){
			$street_register 	 = $this->config->item('street_register');

			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->where("alarm_report_vehicle_user_id", 4408);
			$this->dbtrip->where("alarm_report_vehicle_id", $vehicleid);
			$this->dbtrip->where("alarm_report_media", 0); //photo
			$this->dbtrip->where("alarm_report_update_time >=", $sdate);
			$this->dbtrip->where("alarm_report_update_time <=", $edate);
			$this->dbtrip->where('alarm_report_type', $alarmtype);
			$this->dbtrip->where("alarm_report_gpsstatus !=","");
			$this->dbtrip->where_in('alarm_report_location_start', $street_register);
			$this->dbtrip->order_by("alarm_report_update_time", "DESC");
			$this->dbtrip->limit(1);
			$q = $this->dbtrip->get($dbtable)->result_array();
			return $q;
		}

		function getoverspeedreport($table, $vehicledevice, $date, $edate){
      // SELECT * FROM overspeed_oktober_2020 where overspeed_report_vehicle_device = '69969039493669@TK510' and overspeed_report_gps_time >=  '2020-10-07 19:12:00' and overspeed_report_gps_time <= '2020-10-07 19:13:00' and overspeed_report_speed_status = 1 order by overspeed_report_gps_time desc limit 1;
      $datefixstart = date("Y-m-d H:i:s", strtotime($date)+60*60);
      $datefixend   = date("Y-m-d H:i:s", strtotime($edate)+60*60);

      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("overspeed_report_vehicle_device", $vehicledevice);
      $this->dbalarm->where("overspeed_report_gps_time >=", date("Y-m-d H:i:s", strtotime($datefixstart)));
      $this->dbalarm->where("overspeed_report_gps_time <=", date("Y-m-d H:i:s", strtotime($datefixend)));
      $this->dbalarm->where("overspeed_report_speed_status", 1);
			$this->dbalarm->order_by("overspeed_report_level", "desc");
      $this->dbalarm->order_by("overspeed_report_gps_time", "desc");
      $this->dbalarm->limit(1);
      $q        = $this->dbalarm->get($table);
      return  $q->result_array();

      // echo "<pre>";
  		// var_dump($datefixstart.'-'.$datefixend);die();
  		// echo "<pre>";
    }
		// CRON SUBMIT DATA HAZARD END

		// CRON SYNC MASTER DATA ISAFE START
		function generate_token_isafe(){
			$url_login   = "https://devgcpesb.borneo-indobara.com/external/getToken";
			$token 		   = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NzM4NjE0NDV9.djjT7KP9c1ku6i14bfKZ9z1fMv6--1FEDJ6Dot_14Rk";

			$ch = curl_init($url_login);
			$headers = array(
						 "Accept: application/json",
						 "Content-Type: application/json",
						 "Authorization: Bearer " .$token,
					);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$result = curl_exec($ch);

			if ($result === FALSE) {
					die("Login failed: " . curL_error($ch) . " \r\n");
			}

			curl_close($ch);

			$obj = json_decode($result); //print_r($obj);exit();
			return $obj;
		}

		function loginisafe($token){
			$url_login   = "https://devgcpesb.borneo-indobara.com/user/login";
			$token 		   = $token;
			$data        = array("isafeNo" => "B44123", "password" => "12345678");
			$data_param  = json_encode($data);

			$ch = curl_init($url_login);
			$headers = array(
						 "Accept: application/json",
						 "Content-Type: application/json",
						 "Authorization: Bearer " .$token,
					);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$result = curl_exec($ch);

			if ($result === FALSE) {
					die("Login failed: " . curL_error($ch) . " \r\n");
			}

			curl_close($ch);

			$obj = json_decode($result); //print_r($obj);exit();
			return $obj;
		}

		function getmasterdataisafe_kategori($token, $modul){
			$url_login   = "https://devgcpesb.borneo-indobara.com/external/master?modul=".$modul;
			$token 		   = $token;
			$data        = array("limit" => "5");
			$data_param  = json_encode($data);

			$ch = curl_init($url_login);
			$headers = array(
						 "Accept: application/json",
						 "Content-Type: application/json",
						 "Authorization: Bearer " .$token,
					);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$result = curl_exec($ch);

			if ($result === FALSE) {
					die("Login failed: " . curL_error($ch) . " \r\n");
			}

			curl_close($ch);

			$obj = json_decode($result); //print_r($obj);exit();
			return $obj;
		}

			function syncmasterdataisafe($userid, $modul){
				date_default_timezone_set("Asia/Jakarta");
				$cronstartdate = date("Y-m-d H:i:s");
				print_r("CRON SYNC MASTER DATA ISAFE START : ". $cronstartdate . "\r\n");
				$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
				print_r("CRON SYNC MASTER DATA ISAFE  WIB : ". $cronstartdate . "\r\n");
				print_r("CRON SYNC MASTER DATA ISAFE  WITA : ". $nowtime_wita . "\r\n");

				$status_dev  = 200;
				// $token_isafe = $this->generate_token_isafe(); // AKTIFKAN SAAT LIVE
				// $loginisafe  = $this->loginisafe($token_isafe->token); // AKTIFKAN SAAT LIVE

				// echo "<pre>";
				// var_dump($loginisafe);die();
				// echo "<pre>";
				// $tokenafterloginsafe = $loginisafe->token; // AKTIFKAN SAAT LIVE
				$tokenafterloginsafe = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJrZXkiOiJleUpoYkdjaU9pSklVekkxTmlJc0luUjVjQ0k2SWtwWFZDSjkuZXlKaGNIQnpYMjVoYldVaU9pSm1iWE1pTENKcFlYUWlPakUyTnpNNE5qRTBORFY5LmRqalQ3S1A5YzFrdTZpMTRiZktaOXoxZk12Ni0tMUZFREo2RG90XzE0UmsiLCJpYXQiOjE2Nzc2NTkyMzcsImV4cCI6MTY3Nzc0NTYzN30.zNCnTL4P3sX_Y7zx99UE63y3ub2zXzYNFdxY1bKLEIo";
				print_r("TOKEN AFTER LOGIN : ". $tokenafterloginsafe . "\r\n"); // AKTIFKAN SAAT LIVE
				// if ($loginisafe->token != "") { // AKTIFKAN SAAT LIVE
					if ($status_dev == 200) {
						if ($modul == "kategori") {
							$get_masterdata = $this->getmasterdataisafe_kategori($tokenafterloginsafe, "kategori");

						if (isset($get_masterdata->dataMaster)) {
							if (sizeof($get_masterdata->dataMaster) > 0) {
								$dataMaster = $get_masterdata->dataMaster;
							}else {
								print_r("===== DATA TIDAK ADA\r\n");
							}
						}else {
							print_r("===== DATA TIDAK ADA\r\n");
						}

						for ($i=0; $i < sizeof($dataMaster); $i++) {
							usleep(500);
							$created_at    = $dataMaster[$i]->created_at;
							$updated_at    = $dataMaster[$i]->updated_at;

							$data_array = array(
								 "isafekategori_id"							 => $dataMaster[$i]->id,
								 "isafekategori_cause"           => $dataMaster[$i]->cause,
								 "isafekategori_sourceofcause"   => $dataMaster[$i]->source_of_cause,
								 "isafekategori_created_at"      => $created_at,
								 "isafekategori_created_by"      => null,
								 "isafekategori_updated_at"      => $updated_at,
								 "isafekategori_updated_by"      => null,
								 "isafekategori_deleted_by"      => null
							);

							$insert = $this->insertDataPortalUmum("ts_isafe_master_kategori", $data_array);
								if ($insert) {
									print_r("===== BERHASIL INSERT MASTER DATA KATEGORI \r\n");
								}else {
									print_r("===== GAGAL INSERT MASTER DATA KATEGORI\r\n");
								}
						}
					}
				}
			}
		// CRON SYNC MASTER DATA ISAFE END




















}
