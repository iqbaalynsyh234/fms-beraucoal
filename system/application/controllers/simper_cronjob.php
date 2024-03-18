<?php
//ob_start();
include "base.php";

class Simper_cronjob extends Base {

	function Simper_cronjob()
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

	function mirroring_simper($limit=1){
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
		$urlGetToken = "https://esbportal.borneo-indobara.com/external/getToken"."?"; //PROD sudah live
		//$urlGetToken = "https://devgcpesb.borneo-indobara.com/external/getToken?"."?"; //DEV
		//$urlGetSync  = "https://devgpcpesb.borneo-indobara.com/external/simperlist/getAll?size=".$limit;
		$urlGetSync  = "https://esbportal.borneo-indobara.com/external/simperlist/getAll?size=".$limit; //PROD


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
							$datajson   = json_decode($resp);
							$datasimper = $datajson->simper;
							/*
							[register_number] => BIB/ERC/2022/I/004222
							[id_number] => 2201004222
							[name] => YUDHA HES
							[position] => Environment & Reclamation Dept. Head
							[id_position] => 33
							[department] => Environment & Reclamation
							[company] => PT Borneo Indobara
							[depkon_id] => 1346
							[date_of_hire] => 2017-09-29
							[exp_date] => 2022-08-19
							[blood_type] => O
							[gender] => L
							[religion] => ISLAM
							[date_of_birth] => 1979-08-19
							[place_of_birth] => RAMA GUNAWAN
							[address] => KP.LIMUS NUNGGAL RT/RW 019/009 KEL/DESA CIBENTANG KECAMATAN GUNUNG GURUH
							[tribe] => SUNDA
							[citizen] => INDONESIA
							[emergency_contact] => 6285217389331
							[id_card_type] => SIMPER
							[port_access] => Yes
							[zone_access] => Z2
							[counting_pengajuan] => 3
							[counting_gagal] =>
							[sim_type] => A
							[sim_number] => 790813260724
							[sim_exp_date] =>
							[issued_at] => 2022-01-19
							[status] => 0
							[is_vvip] =>
							[verification_status] => 7
							[no_ktp] => 3202271908790001
							[atasan_langsung] => 4408
							[jabatan] => MANAGER
							[contact] => 62811508070
							[email] => yudha.hes@borneo-indobara.com
							[mcu_date] =>
							[mcu_location] =>
							[mcu_description] =>
							[mcu_status] =>
							[status_karyawan] => 0
							[violation] =>
							[violation_date] =>
							[license_file] =>
							[license_exp] =>
							[inspection_point_target] =>
							[observation_point_target] =>
							[safety_talk_point_target] =>
							[hazard_report_point_target] =>
							[commisioning_point_target] =>
							[created_at] => 2017-09-29T01:56:46.000Z
							[updated_at] => 2022-04-15T07:17:09.000Z
							[created_by] => Septiani Sri Utami
							[updated_by] => PUTU ARMIYANTI
							[nik] => 06101002
							[deleted_by] =>
							[rfid_tag] => E200001962040157124085A2
							[isafe_no] => B2D99E
							[date_of_birth_string] =>
							[special_notes] =>
							[roleId] => 1
							[coaching_point_target] =>
							[isERT] => 0
							[vaksinasi] =>
							[akun_peduli] => YUDHA HES
							[tanggal_vaksin] => 2021-06-05
							[status_vaksin] => 3
							[jenis_vaksin] => 1
							[tanggal_v1] =>
							[tanggal_v2] =>
							[verifikator_vaksin] => RPA JAKI
							[terakhir_verifikasi] => 2022-07-08T01:23:13.000Z
							[id] => 4222
							[submited_at] => 2021-11-04T02:47:24.000Z
						)


							
							*/
							//print_r($datajson);
								$totalsimper = sizeof($datasimper);
								printf("==TOTAL SIMPER : %s \r\n", $totalsimper);

								for ($i=0; $i < $totalsimper; $i++) {
									$new = $i+1;

									// print_r();exit();

									$checkData = $this->checkDataPortal($datasimper[$i]->id_number);

									$sdate         = date("Y-m-d");
									$stime         = date("H:i:s");
									//print_r($updatedate_ex);
									//print_r($stime);exit();
									$updated_date_new = date("Y-m-d H:i:s", strtotime($sdate." ".$stime));
									printf("==PROCESS DATA : %s %s of %s \r\n", $datasimper[$i]->id_number, $new, $totalsimper);
										if (sizeof($checkData) > 0) {

											// UPDATE
											$datafix = array(
												"portal_register_number"	 	           => $datasimper[$i]->register_number,
												"portal_id_number"	 	                 => $datasimper[$i]->id_number,
												"portal_name"	 		                     => $datasimper[$i]->name,
												"portal_position"	                     => $datasimper[$i]->position,
												"portal_id_position"	 	               => $datasimper[$i]->id_position,
												"portal_departmen"	                   => $datasimper[$i]->department,
												"portal_company"	 	                   => $datasimper[$i]->company,
												"portal_depkon_id"	                   => $datasimper[$i]->depkon_id,
												"portal_date_of_hire"	 	               => $datasimper[$i]->date_of_hire,
												"portal_exp_date"	                     => $datasimper[$i]->exp_date,
												"portal_blood_type"	                   => $datasimper[$i]->blood_type,
												"portal_gender"	                       => $datasimper[$i]->gender,
												"portal_religion"	 	                   => $datasimper[$i]->religion,
												"portal_date_of_birth"	               => $datasimper[$i]->date_of_birth,
												"portal_place_of_birth"	 	             => $datasimper[$i]->place_of_birth,
												"portal_address"	                     => $datasimper[$i]->address,

												"portal_tribe"	                       => $datasimper[$i]->tribe,
												"portal_citizen"	 	                   => $datasimper[$i]->citizen,
												"portal_emergency_contact"	           => $datasimper[$i]->emergency_contact,
												"portal_id_card_type"	                 => $datasimper[$i]->id_card_type,
												"portal_port_access"	 	               => $datasimper[$i]->port_access,
												"portal_zone_access"	                 => $datasimper[$i]->zone_access,
												"portal_counting_pengajuan"	 	         => $datasimper[$i]->counting_pengajuan,
												"portal_counting_gagal"	               => $datasimper[$i]->counting_gagal,
												"portal_sim_type"	 	                   => $datasimper[$i]->sim_type,
												"portal_sim_number"	                   => $datasimper[$i]->sim_number,
												"portal_sim_exp_date"	                 => $datasimper[$i]->sim_exp_date,
												//"portal_sim_scan"	                     => $datasimper[$i]->sim_scan,
												"portal_issued_at"	 	                 => $datasimper[$i]->issued_at,
												"portal_status"	                       => $datasimper[$i]->status,
												"portal_is_vvip"	 	                   => $datasimper[$i]->is_vvip,
												"portal_verification_status"	         => $datasimper[$i]->verification_status,


												"portal_no_ktp"	                       => $datasimper[$i]->no_ktp,
												//"portal_ktp_scan"	 	                   => $datasimper[$i]->ktp_scan,
												"portal_atasan_langsung"	             => $datasimper[$i]->atasan_langsung,
												"portal_jabatan"	                     => $datasimper[$i]->jabatan,
												"portal_contact"	 	                   => $datasimper[$i]->contact,
												"portal_email"	                       => $datasimper[$i]->email,
												"portal_mcu_date"	 	                   => $datasimper[$i]->mcu_date,
												"portal_mcu_location"	                 => $datasimper[$i]->mcu_location,
												//"portal_mcu_file"	 	                   => $datasimper[$i]->mcu_file,
												"portal_mcu_description"	             => $datasimper[$i]->mcu_description,
												"portal_mcu_status"	                   => $datasimper[$i]->mcu_status,
												"portal_status_karyawan"	             => $datasimper[$i]->status_karyawan,
												"portal_violation"	 	                 => $datasimper[$i]->violation,
												"portal_violation_date"	               => $datasimper[$i]->violation_date,
												"portal_license_file"	 	               => $datasimper[$i]->license_file,
												"portal_license_exp"	                 => $datasimper[$i]->license_exp,

												"portal_inspection_point_target"	     => $datasimper[$i]->inspection_point_target,
												"portal_observation_point_target"	 	   => $datasimper[$i]->observation_point_target,
												"portal_safety_talk_point_target"	     => $datasimper[$i]->safety_talk_point_target,
												"portal_hazard_report_point_target"	   => $datasimper[$i]->hazard_report_point_target,
												"portal_commisioning_point_target"	 	 => $datasimper[$i]->commisioning_point_target,
												"portal_created_at"	                   => $datasimper[$i]->created_at,
												"portal_updated_at"	 	                 => $datasimper[$i]->updated_at,
												"portal_created_by"	                   => $datasimper[$i]->created_by,
												"portal_updated_by"	 	                 => $datasimper[$i]->updated_by,
												"portal_nik"	                         => $datasimper[$i]->nik,
												"portal_deleted_by"	                   => $datasimper[$i]->deleted_by,
												"portal_rfid_tag"	                     => $datasimper[$i]->rfid_tag,
												"portal_isafe_no"	 	                   => $datasimper[$i]->isafe_no,
												//"portal_default_isafe_password"	       => $datasimper[$i]->default_isafe_password,
												"portal_date_of_birth_string"	 	       => $datasimper[$i]->date_of_birth_string,
												//"portal_isafe_password"	               => $datasimper[$i]->isafe_password,

												"portal_special_notes"	               => $datasimper[$i]->special_notes,
												"portal_roleId"	 	                     => $datasimper[$i]->roleId,
												"portal_coaching_point_target"	       => $datasimper[$i]->coaching_point_target,
												"portal_isERT"	                       => $datasimper[$i]->isERT,
												"portal_vaksinasi"	 	                 => $datasimper[$i]->vaksinasi,
												"portal_akun_peduli"	                 => $datasimper[$i]->akun_peduli,
												"portal_tanggal_vaksin"	 	             => $datasimper[$i]->tanggal_vaksin,
												"portal_status_vaksin"	               => $datasimper[$i]->status_vaksin,
												"portal_jenis_vaksin"	 	               => $datasimper[$i]->jenis_vaksin,
												"portal_tanggal_v1"	                   => $datasimper[$i]->tanggal_v1,
												"portal_tanggal_v2"	                   => $datasimper[$i]->tanggal_v2,
												"portal_verifikator_vaksin"	           => $datasimper[$i]->verifikator_vaksin,
												"portal_terakhir_verifikasi"	 	       => $datasimper[$i]->terakhir_verifikasi,
												"portal_id"	 	                         => $datasimper[$i]->id,
												"portal_submited_at"	                 => $datasimper[$i]->submited_at,
												"master_portal_updateddate_new"	       => $updated_date_new
											);
											$updateNow = $this->updateDataPortal("portal_id_number", $datasimper[$i]->id_number, $datafix);
												if ($updateNow) {
													$datafix = array();
														printf("==SUCCESS UPDATE DATA \r\n");
												}else {
													printf("==FAILED UPDATE DATA \r\n");
												}
										}else {
											// INSERT
											$sdate         = date("Y-m-d");
											$stime         = date("H:i:s");
											//print_r($updatedate_ex);
											//print_r($stime);exit();
											$updated_date_new = date("Y-m-d H:i:s", strtotime($sdate." ".$stime));

														$datafix = array(
															"portal_register_number"	 	           => $datasimper[$i]->register_number,
															"portal_id_number"	 	                 => $datasimper[$i]->id_number,
															"portal_name"	 		                     => $datasimper[$i]->name,
															"portal_position"	                     => $datasimper[$i]->position,
															"portal_id_position"	 	               => $datasimper[$i]->id_position,
															"portal_departmen"	                   => $datasimper[$i]->department,
															"portal_company"	 	                   => $datasimper[$i]->company,
															"portal_depkon_id"	                   => $datasimper[$i]->depkon_id,
															"portal_date_of_hire"	 	               => $datasimper[$i]->date_of_hire,
															"portal_exp_date"	                     => $datasimper[$i]->exp_date,
															"portal_blood_type"	                   => $datasimper[$i]->blood_type,
															"portal_gender"	                       => $datasimper[$i]->gender,
															"portal_religion"	 	                   => $datasimper[$i]->religion,
															"portal_date_of_birth"	               => $datasimper[$i]->date_of_birth,
															"portal_place_of_birth"	 	             => $datasimper[$i]->place_of_birth,
															"portal_address"	                     => $datasimper[$i]->address,

															"portal_tribe"	                       => $datasimper[$i]->tribe,
															"portal_citizen"	 	                   => $datasimper[$i]->citizen,
															"portal_emergency_contact"	           => $datasimper[$i]->emergency_contact,
															"portal_id_card_type"	                 => $datasimper[$i]->id_card_type,
															"portal_port_access"	 	               => $datasimper[$i]->port_access,
															"portal_zone_access"	                 => $datasimper[$i]->zone_access,
															"portal_counting_pengajuan"	 	         => $datasimper[$i]->counting_pengajuan,
															"portal_counting_gagal"	               => $datasimper[$i]->counting_gagal,
															"portal_sim_type"	 	                   => $datasimper[$i]->sim_type,
															"portal_sim_number"	                   => $datasimper[$i]->sim_number,
															"portal_sim_exp_date"	                 => $datasimper[$i]->sim_exp_date,
															//"portal_sim_scan"	                     => $datasimper[$i]->sim_scan,
															"portal_issued_at"	 	                 => $datasimper[$i]->issued_at,
															"portal_status"	                       => $datasimper[$i]->status,
															"portal_is_vvip"	 	                   => $datasimper[$i]->is_vvip,
															"portal_verification_status"	         => $datasimper[$i]->verification_status,


															"portal_no_ktp"	                       => $datasimper[$i]->no_ktp,
															//"portal_ktp_scan"	 	                   => $datasimper[$i]->ktp_scan,
															"portal_atasan_langsung"	             => $datasimper[$i]->atasan_langsung,
															"portal_jabatan"	                     => $datasimper[$i]->jabatan,
															"portal_contact"	 	                   => $datasimper[$i]->contact,
															"portal_email"	                       => $datasimper[$i]->email,
															"portal_mcu_date"	 	                   => $datasimper[$i]->mcu_date,
															"portal_mcu_location"	                 => $datasimper[$i]->mcu_location,
															//"portal_mcu_file"	 	                   => $datasimper[$i]->mcu_file,
															"portal_mcu_description"	             => $datasimper[$i]->mcu_description,
															"portal_mcu_status"	                   => $datasimper[$i]->mcu_status,
															"portal_status_karyawan"	             => $datasimper[$i]->status_karyawan,
															"portal_violation"	 	                 => $datasimper[$i]->violation,
															"portal_violation_date"	               => $datasimper[$i]->violation_date,
															"portal_license_file"	 	               => $datasimper[$i]->license_file,
															"portal_license_exp"	                 => $datasimper[$i]->license_exp,

															"portal_inspection_point_target"	     => $datasimper[$i]->inspection_point_target,
															"portal_observation_point_target"	 	   => $datasimper[$i]->observation_point_target,
															"portal_safety_talk_point_target"	     => $datasimper[$i]->safety_talk_point_target,
															"portal_hazard_report_point_target"	   => $datasimper[$i]->hazard_report_point_target,
															"portal_commisioning_point_target"	 	 => $datasimper[$i]->commisioning_point_target,
															"portal_created_at"	                   => $datasimper[$i]->created_at,
															"portal_updated_at"	 	                 => $datasimper[$i]->updated_at,
															"portal_created_by"	                   => $datasimper[$i]->created_by,
															"portal_updated_by"	 	                 => $datasimper[$i]->updated_by,
															"portal_nik"	                         => $datasimper[$i]->nik,
															"portal_deleted_by"	                   => $datasimper[$i]->deleted_by,
															"portal_rfid_tag"	                     => $datasimper[$i]->rfid_tag,
															"portal_isafe_no"	 	                   => $datasimper[$i]->isafe_no,
															//"portal_default_isafe_password"	       => $datasimper[$i]->default_isafe_password,
															"portal_date_of_birth_string"	 	       => $datasimper[$i]->date_of_birth_string,
															//"portal_isafe_password"	               => $datasimper[$i]->isafe_password,

															"portal_special_notes"	               => $datasimper[$i]->special_notes,
															"portal_roleId"	 	                     => $datasimper[$i]->roleId,
															"portal_coaching_point_target"	       => $datasimper[$i]->coaching_point_target,
															"portal_isERT"	                       => $datasimper[$i]->isERT,
															"portal_vaksinasi"	 	                 => $datasimper[$i]->vaksinasi,
															"portal_akun_peduli"	                 => $datasimper[$i]->akun_peduli,
															"portal_tanggal_vaksin"	 	             => $datasimper[$i]->tanggal_vaksin,
															"portal_status_vaksin"	               => $datasimper[$i]->status_vaksin,
															"portal_jenis_vaksin"	 	               => $datasimper[$i]->jenis_vaksin,
															"portal_tanggal_v1"	                   => $datasimper[$i]->tanggal_v1,
															"portal_tanggal_v2"	                   => $datasimper[$i]->tanggal_v2,
															"portal_verifikator_vaksin"	           => $datasimper[$i]->verifikator_vaksin,
															"portal_terakhir_verifikasi"	 	       => $datasimper[$i]->terakhir_verifikasi,
															"portal_id"	 	                         => $datasimper[$i]->id,
															"portal_submited_at"	                 => $datasimper[$i]->submited_at,
															"master_portal_updateddate_new"	       => $updated_date_new
											);
											// echo "<pre>";
											// var_dump($datafix);die();
											// echo "<pre>";
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
	
	function images_simper($limit="")
	{
		$startproses = date("Y-m-d H:i:s");
		$dbtable = "webtracking_ts_bib_master_portal_simper";
		
		printf("===STARTING REPORT %s \r\n", $startproses);
		$this->dbts = $this->load->database("webtracking_ts",true); 
		$this->dbts->order_by("portal_id_number","asc");
		$this->dbts->where("portal_image_status", 0);
		$this->dbts->where("portal_jabatan", "DRIVER");
		$this->dbts->limit($limit);
		$this->dbts->from($dbtable);
        $q = $this->dbts->get();
        $rows = $q->result();
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH DATA SIMPER : %s \r\n", $total_rows);
			
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$name = $rows[$i]->portal_name;
				$company = $rows[$i]->portal_company;
				$idnumber = trim($rows[$i]->portal_id_number);
				printf("===PROCESS : %s %s (%s of %s) \r\n", $name, $idnumber, $nourut, $total_rows);
				$imagedata = $this->simper_getimages($idnumber);
				//print_r($imagedata);exit();
				
					//exit();
				
					unset($datainsert);
					$datainsert["portal_image"] = $imagedata;
					$datainsert["portal_image_status"] = 1;
					
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("portal_id_number", $idnumber);
					$this->dbts->limit(1);
					$q_last = $this->dbts->get($dbtable);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($imagedata !=""){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("portal_id_number", $idnumber);
						$this->dbts->limit(1);
						$this->dbts->update($dbtable,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						printf("!==UPDATE FAILED NO DATA ID NUMBER \r\n ");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA SIMPER======== \r\n"); 
		}
		
		$endprocess = date("Y-m-d H:i:s");
		printf("===========SELESAI %s %s =========== \r\n", $startproses, $endprocess);
	
	
	}
	
	function simper_getimages($idnumber){
		ini_set('memory_limit','3048M');
		$starttime = date("Y-m-d H:i:s");
		$totalunit = 0;
		$token = "";
		$result = ""; 
		//$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2Mzc2Mzc5OTZ9.rO1QXXzMcyLn7sljo827KbF82JZzWAnzPygXqY47PEI"; //DEV
		$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo"; //PROD


		
		printf("==GET IMAGE ID NUMBER : %s \r\n", $idnumber);
		$urlGetToken = "https://esbportal.borneo-indobara.com/external/getToken"."?"; //PROD
		//$urlGetToken = "https://devgcpesb.borneo-indobara.com/external/getToken?"."?"; //DEV
		
		$urlGetSync  = "https://esbportal.borneo-indobara.com/external/simperlist/getImages?id_number=".$idnumber; //PROD
		//$urlGetSync  = "https://devgcpesb.borneo-indobara.com/external/simperlist/getImages?id_number=".$idnumber; //DEV

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

		$obj = json_decode($result); 
		
		if(isset($obj)){
			
			printf("==TOKEN :  %s \r\n",$obj->token);
			printf("-------------------------- \r\n");
			//exit();
				if (isset($obj->token)) {
					printf("==GET TOKEN SUCCESS \r\n");
					printf("==TRY TO GET IMG\r\n");

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
								$datajson   = json_decode($resp);
								$result = $datajson->result;
								

						}
				}
		}else{
			
			printf("X==INVALID TOKEN \r\n");
		}
		
		
		

		return $result;
		printf("======== \r\n");
	}

	function simper_getfilter($param="", $value=""){
		ini_set('memory_limit','3048M');
		$starttime = date("Y-m-d H:i:s");
		$totalunit = 0;
		$token = "";
		
		//$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2Mzc2Mzc5OTZ9.rO1QXXzMcyLn7sljo827KbF82JZzWAnzPygXqY47PEI"; //DEV
		$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo"; //PROD


		//printf("==CRON SYNCRONIZE UNIT START : %s \r\n", $starttime);
		printf("==GET IMAGE ID NUMBER : %s %s \r\n", $param, $value);
		$urlGetToken = "https://esbportal.borneo-indobara.com/external/getToken"."?"; //PROD
		//$urlGetToken = "https://devgcpesb.borneo-indobara.com/external/getToken?"."?"; //DEV
		
		$urlGetSync  = "https://esbportal.borneo-indobara.com/external/simperlist/getAll?".$param."=".$value.""; //PROD
		//$urlGetSync  = "https://devgcpesb.borneo-indobara.com/external/simperlist/getAll?".$param."=".$value.""; //DEV
		//print_r($urlGetSync);
		printf("==URL :  %s \r\n",$urlGetSync);
		
		
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
							$datajson   = json_decode($resp);
							//$result = $datajson->result;
							print_r($datajson);exit();	

					}
			}

		$endtime = date("Y-m-d H:i:s");
		printf("==SELESAI : %s \r\n", $endtime);

		//$contentlog = "Mirroring Unit Portal Success. Total Sync: ".$totalunit;
		//$insertlog = $this->log_model->insertlog("SYS", $contentlog, "SYNC", "SYSTEM");

		printf("========================================================= \r\n");
	}
	
	function checkData($id){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
		$this->dbkalimantan->where("portal_id_number", $id);
		return $this->dbkalimantan->get("ts_bib_master_portal_simper")->result();
	}

	function checkDataPortal($id){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->where("portal_id_number", $id);
		return $this->dbts->get("ts_bib_master_portal_simper")->result();
	}

	function insertData($data){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
		return $this->dbkalimantan->insert("ts_bib_master_portal_simper", $data);
	}

	function insertDataPortal($data){
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert("ts_bib_master_portal_simper", $data);
	}

	function updateData($where, $id, $datafix){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
		$this->dbkalimantan->where($where, $id);
		return $this->dbkalimantan->update("ts_bib_master_portal_simper", $datafix);
	}

	function updateDataPortal($where, $id, $datafix){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->where($where, $id);
		return $this->dbts->update("ts_bib_master_portal_simper", $datafix);
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
			{
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

	function getinfo_fromportal($nolambung){
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->order_by("master_portal_updateddate_new","desc");
		$this->dbts->where("master_portal_nolambung", $nolambung);
		$q_portal = $this->dbts->get("ts_bib_master_portal");
		$rows_portal = $q_portal->row();

		return $rows_portal;
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
		$this->db->order_by("vehicle_name","asc");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
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

	function violationmonitoring_old_method_from_gps_alert(){
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON START : ". $cronstartdate . "\r\n");
		$masterdatavehiclefix = $this->m_securityevidence->getallvehicleforviolation();
		// $datavehiclefatigue   = $this->m_securityevidence->getallvehicleforviolation(1);
		$datetimefix          = date("Y-m-d H:i:s", strtotime("-7 hour"));
		$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-70 minutes"));
		print_r("Cron Date OVERSPEED: ". $datetimefix . "\r\n");
		print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");

		// $datetimefix       = date("Y-m-d H:i:s", strtotime($datetime) - 5*60 );
		$datafixoverspeed    = array();
		$datafixfatigue      = array();
		$datamix 						 = array();
		$alldatafixoverspeed = array();
		$alldatafixfatigue   = array();

		$m1                   = date("F");
		$year                 = date("Y");

		$report     = "alarm_evidence_";

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

				$alarmbymaster = $this->m_securityevidence->getalarmbytypeforevidence();
				$alarmtypefromaster = array();
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}

		// DATA LOOP START
		for ($i=0; $i < sizeof($masterdatavehiclefix); $i++) {
			print_r(($i+1). " OF " .sizeof($masterdatavehiclefix). "\r\n");
			print_r("===================================================== \r\n");

			$vdevice       = explode("@", $masterdatavehiclefix[$i]['vehicle_device']);
			$name          = $vdevice[0];
			$host          = $vdevice[1];

			$overspeeddata = $this->m_securityevidence->violation_overspeed($name, $host, $datetimefix);
			print_r("OVERSPEED CHECK STARTED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");

			// GET COMPANY
			$company = $this->dashboardmodel->getcompany_idforevidence($masterdatavehiclefix[$i]['vehicle_company']);

			// DATA FOR OVERSPEED FIELD
			// JIKA TERDAPAT OVERSPEED
				if (sizeof($overspeeddata) > 0) {
					print_r("========================OVERSPEED FOUNDED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
					$jalur_name        = $this->m_securityevidence->get_jalurname($overspeeddata[0]['gps_course']);
					$positionalert     = $this->gpsmodel->GeoReverse($overspeeddata[0]['gps_latitude_real'], $overspeeddata[0]['gps_longitude_real']);
					$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($overspeeddata[0]['gps_longitude_real'], $overspeeddata[0]['gps_latitude_real'], $masterdatavehiclefix[$i]['vehicle_user_id'], "webtracking_gps_temanindobara_live");
					$speedlimitfix 		 = "";

					if ($geofence_location) {
						$geofencefix = $geofence_location[0]->geofence_name;

						if ($jalur_name == "kosongan") {
							$speedlimitfix = $geofence_location[0]->geofence_speed_alias;
						}elseif ($jalur_name == "muatan") {
							$speedlimitfix = $geofence_location[0]->geofence_speed_muatan_alias;
						}

					}else {
						$geofencefix = "Out Of Geofence";
					}

					if ($positionalert->display_name != "Unknown Location!") {
						$positionexplode = explode(",", $positionalert->display_name);
						$position = $positionexplode[0];
					}else {
						$position = $positionalert->display_name;
					}

					if ((round($overspeeddata[0]['gps_speed']*1.853) - $speedlimitfix) < 0 || $speedlimitfix == "" || $speedlimitfix == 0) {

					}else {
						$datafixoverspeed = array(
							"isfatigue" 				 => "no",
							"jalur_name"				 => $jalur_name,
							"vehicle_no"         => $masterdatavehiclefix[$i]['vehicle_no'],
							"vehicle_name"       => $masterdatavehiclefix[$i]['vehicle_name'],
							"vehicle_device"     => $masterdatavehiclefix[$i]['vehicle_device'],
							"vehicle_mv03"    	 => $masterdatavehiclefix[$i]['vehicle_mv03'],
							"gps_id"             => $overspeeddata[0]['gps_id'],
							"gps_name"           => $overspeeddata[0]['gps_name'],
							"gps_host"           => $overspeeddata[0]['gps_host'],
							"gps_status"         => $overspeeddata[0]['gps_status'],
							"gps_latitude_real"  => $overspeeddata[0]['gps_latitude_real'],
							"gps_longitude_real" => $overspeeddata[0]['gps_longitude_real'],
							"gps_speed"          => round($overspeeddata[0]['gps_speed']*1.853),
							"gps_speed_limit"    => $speedlimitfix,
							"gps_alert"          => "Overspeed",
							"gps_time"           => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($overspeeddata[0]['gps_time']))),
							"geofence"           => $geofencefix,
							"position" 					 => $position
						);

						$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);
							if (sizeof($checkindb) < 1) {
								$todatabase = array(
									"violation_vehicle_no"                 => $masterdatavehiclefix[$i]['vehicle_no'],
									"violation_vehicle_name"               => $masterdatavehiclefix[$i]['vehicle_name'],
									"violation_vehicle_device"             => $masterdatavehiclefix[$i]['vehicle_device'],
									"violation_vehicle_mv03" 	             => $masterdatavehiclefix[$i]['vehicle_mv03'],
									"violation_status" 			 	             => 1,
									"violation_vehicle_companyid" 		 	   => $company->company_id,
									"violation_vehicle_companyname" 		 	 => $company->company_name,
									"violation_position" 		 	             => $position,
									"violation_jalur" 		 	               => $jalur_name,
									"violation_type_id" 	 	 	             => 9999,
									"violation_type" 	 	 	                 => "overspeed",
									"violation_overspeed" 		             => json_encode($datafixoverspeed),
									"violation_update" 				             => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($overspeeddata[0]['gps_time']))) //date("Y-m-d H:i:s")
								);
								$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
									if ($insert) {
										print_r("DATA INSERT OVERSPEED SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									}else {
										print_r("DATA INSERT OVERSPEED FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									}
							}else {
								$todatabase = array(
									"violation_status" 			 	             => 1,
									"violation_position" 		 	             => $position,
									"violation_jalur" 		 	               => $jalur_name,
									"violation_vehicle_companyid" 		 	   => $company->company_id,
									"violation_vehicle_companyname" 		 	 => $company->company_name,
									"violation_type" 	    	 	             => "overspeed",
									"violation_type_id" 	 	 	             => 9999,
									"violation_overspeed" 		             => json_encode($datafixoverspeed),
									"violation_update" 				             => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($overspeeddata[0]['gps_time']))) //date("Y-m-d H:i:s")
								);
								$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
									if ($update) {
										print_r("DATA UPDATE OVERSPEED SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									}else {
										print_r("DATA UPDATE OVERSPEED FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									}
							}
					}
				}else{
					// JIKA TIDAK TERDAPAT OVERSPEED
					$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);
						if (sizeof($checkindb) < 1) {
							$todatabase = array(
								"violation_vehicle_no"                 => $masterdatavehiclefix[$i]['vehicle_no'],
								"violation_vehicle_name"              => $masterdatavehiclefix[$i]['vehicle_name'],
								"violation_vehicle_device"             => $masterdatavehiclefix[$i]['vehicle_device'],
								"violation_vehicle_mv03" 	             => $masterdatavehiclefix[$i]['vehicle_mv03'],
								"violation_status" 			 	             => 0,
								"violation_vehicle_companyid" 		 	   => $company->company_id,
								"violation_vehicle_companyname" 		 	 => $company->company_name,
								"violation_position" 		 	             => "",
								"violation_jalur" 		 	               => "",
								"violation_overspeed" 		             => "",
								"violation_update" 				             => "",
								"violation_type" 	    	 	             => "",
								"violation_type_id" 	 	 	             => "",
							);
							$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
								if ($insert) {
									print_r("DATA INSERT OVERSPEED VIOLATION STATUS = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
								}else {
									print_r("DATA INSERT OVERSPEED VIOLATION STATUS = 0 SUCCESS FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
								}
						}else {
							$todatabase = array(
								"violation_status" 			 	             => 0,
								"violation_vehicle_companyid" 		 	   => $company->company_id,
								"violation_vehicle_companyname" 		 	 => $company->company_name,
								"violation_position" 		 	             => "",
								"violation_jalur" 		 	               => "",
								"violation_overspeed" 		             => "",
								"violation_update" 				             => "",
								"violation_type" 	    	 	             => "",
								"violation_type_id" 	 	 	             => "",
							);
							$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
								if ($update) {
									print_r("DATA UPDATE OVERSPEED VIOLATION STATUS = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
								}else {
									print_r("DATA UPDATE OVERSPEED VIOLATION STATUS = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
								}
						}

					// CEK FATIGUE VIOLATION
					if($masterdatavehiclefix[$i]['vehicle_mv03'] != "0000") {
						print_r("FATIGUE CHECK STARTED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . $masterdatavehiclefix[$i]['vehicle_mv03'] . "\r\n");
					    $evidence      = $this->m_securityevidence->violation_fatigue($dbtable, $masterdatavehiclefix[$i]['vehicle_mv03'], $datatimeforevidence, $alarmtypefromaster);
								// JIKA TERDAPAT FATIGUE
					      if (sizeof($evidence) > 0) {
					        print_r("========================FATIGUE FOUNDED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
					        $vdevice                 = $evidence[0]['alarm_report_vehicle_id']."@".$evidence[0]['alarm_report_vehicle_type'];
					        $datavehicle             = $this->m_securityevidence->getdevicebydevID($vdevice);
					        $reportdetaildecode      = explode("|", $evidence[0]['alarm_report_gpsstatus']);
					        $speedgps                = number_format($reportdetaildecode[4]/10, 0, '.', '');
									$forcheck_vehicledevice  = $vdevice;
									$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
									$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
									$jalur_name              = $jsonautocheck->auto_last_road;

									// echo "<pre>";
									// var_dump($evidence[0]);die();
									// echo "<pre>";

					        $datafixfatigue = array(
					          "isfatigue" 				 => "yes",
					          "vehicle_no"         => $masterdatavehiclefix[$i]['vehicle_no'],
					          "vehicle_name"       => $masterdatavehiclefix[$i]['vehicle_name'],
					          "vehicle_device"     => $masterdatavehiclefix[$i]['vehicle_device'],
					          "vehicle_mv03"       => $masterdatavehiclefix[$i]['vehicle_mv03'],
										"gps_alertid"        => $evidence[0]['alarm_report_type'],
					          "gps_alert"          => $evidence[0]['alarm_report_name'],
					          "gps_time"           => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time']) + 1 * 3600),
					          "gps_latitude_real"  => $evidence[0]['alarm_report_coordinate_start'],
					          "gps_longitude_real" => $evidence[0]['alarm_report_coordinate_start'],
					          "position"           => $evidence[0]['alarm_report_location_start'],
					          "gps_speed"          => $speedgps,
					        );

					        $checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);

					          if (sizeof($checkindb) < 1) {
					            $todatabase = array(
					              "violation_vehicle_no"                 => $masterdatavehiclefix[$i]['vehicle_no'],
					              "violation_vehicle_name"               => $masterdatavehiclefix[$i]['vehicle_name'],
					              "violation_vehicle_device"             => $masterdatavehiclefix[$i]['vehicle_device'],
					              "violation_vehicle_mv03" 	             => $masterdatavehiclefix[$i]['vehicle_mv03'],
					              "violation_status" 			 	             => 1,
												"violation_vehicle_companyid" 		 	   => $company->company_id,
												"violation_vehicle_companyname" 		 	 => $company->company_name,
												"violation_type" 	 	 	                 => "fatigue",
												"violation_type_id" 	 	 	             => $evidence[0]['alarm_report_type'],
					              "violation_position" 		 	             => $evidence[0]['alarm_report_location_start'],
												"violation_jalur" 		 	               => $jalur_name,
					              "violation_fatigue" 		               => json_encode($datafixfatigue),
					              "violation_update" 				             => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time']) + 1 * 3600) //date("Y-m-d H:i:s")
					            );
					            $insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
					              if ($insert) {
					                print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
					              }else {
					                print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
					              }
					          }else {
					            $todatabase = array(
					              "violation_status" 			 	             => 1,
												"violation_vehicle_companyid" 		 	   => $company->company_id,
												"violation_vehicle_companyname" 		 	 => $company->company_name,
												"violation_type" 	    	 	             => "fatigue",
												"violation_type_id" 	 	 	             => $evidence[0]['alarm_report_type'],
					              "violation_position" 		 	             => $evidence[0]['alarm_report_location_start'],
												"violation_jalur" 		 	               => $jalur_name,
					              "violation_fatigue" 		               => json_encode($datafixfatigue),
												"violation_update" 				             => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time']) + 1 * 3600) //date("Y-m-d H:i:s")
					            );
					            $update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
					              if ($update) {
					                print_r("DATA UPDATE FATIGUE SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
					              }else {
					                print_r("DATA UPDATE FATIGUE FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
					              }
					          }
					      }else {
									$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);

									 if (sizeof($checkindb) < 1) {
										 $todatabase = array(
											 "violation_vehicle_no"      => $masterdatavehiclefix[$i]['vehicle_no'],
											 "violation_vehicle_name"    => $masterdatavehiclefix[$i]['vehicle_name'],
											 "violation_vehicle_device"  => $masterdatavehiclefix[$i]['vehicle_device'],
											 "violation_vehicle_mv03" 	 => $masterdatavehiclefix[$i]['vehicle_mv03'],
											 "violation_overspeed" 		   => "",
											 "violation_fatigue" 		     => "",
											 "violation_status" 			 	 => 0,
											 "violation_position" 		 	 => "",
											 "violation_jalur" 		 	     => "",
											 "violation_update" 				 => "",
											 "violation_type" 	 	 	     => "",
											 "violation_type_id" 	 	 	   => "",
										 );
										 $insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
											 if ($insert) {
												 print_r("DATA INSERT VIOLATION ON FATIGUE = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											 }else {
												 print_r("DATA INSERT VIOLATION ON FATIGUE = 0 FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											 }
									 }else {
										 $todatabase = array(
											 "violation_vehicle_no"                => $masterdatavehiclefix[$i]['vehicle_no'],
											 "violation_vehicle_name"              => $masterdatavehiclefix[$i]['vehicle_name'],
											 "violation_vehicle_device"            => $masterdatavehiclefix[$i]['vehicle_device'],
											 "violation_vehicle_mv03" 	           => $masterdatavehiclefix[$i]['vehicle_mv03'],
											 "violation_overspeed" 		             => "",
											 "violation_fatigue" 		               => "",
											 "violation_status" 			 	           => 0,
											 "violation_vehicle_companyid" 		 	   => $company->company_id,
		 									 "violation_vehicle_companyname" 		 	 => $company->company_name,
											 "violation_position" 		 	           => "",
											 "violation_jalur" 		 	               => "",
											 "violation_update" 				           => "",
											 "violation_type" 	 	 	               => "",
											 "violation_type_id" 	 	 	             => "",
										 );
										 $update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
											 if ($update) {
												 print_r("DATA UPDATE VIOLATION ON FATIGUE = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											 }else {
												 print_r("DATA UPDATE VIOLATION ON FATIGUE = 0 FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											 }
									 }
					      }
					}
				}
				print_r("===================================================== \r\n");
		}

		// datafixoverspeed
		// datafixfatigue
		// echo "<pre>";
		// var_dump("FINIDONE");die();
		// echo "<pre>";

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
	}

	function violationmonitoring(){
		// function violationmonitoringwithgeofence(){
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON START : ". $cronstartdate . "\r\n");
		$masterdatavehiclefix = $this->m_securityevidence->getallvehicleforviolation();
		// $datavehiclefatigue   = $this->m_securityevidence->getallvehicleforviolation(1);
		$datetimefix          = date("Y-m-d H:i:s", strtotime("-7 hour"));
		$datatimeforevidence  = date("Y-m-d H:i:s", strtotime("-70 minutes"));
		print_r("Cron Date OVERSPEED: ". $datetimefix . "\r\n");
		print_r("Cron Date FATIGUE: ". $datatimeforevidence . "\r\n");

		// $datetimefix       = date("Y-m-d H:i:s", strtotime($datetime) - 5*60 );
		$datafixoverspeed    = array();
		$datafixfatigue      = array();
		$datamix 						 = array();
		$alldatafixoverspeed = array();
		$alldatafixfatigue   = array();

		$m1                   = date("F");
		$year                 = date("Y");

		$report     = "alarm_evidence_";

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

				$alarmbymaster = $this->m_securityevidence->getalarmbytypeforevidence();
				$alarmtypefromaster = array();
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}

		// DATA LOOP START
		for ($i=0; $i < sizeof($masterdatavehiclefix); $i++) {
			print_r(($i+1). " OF " .sizeof($masterdatavehiclefix). "\r\n");
			print_r("===================================================== \r\n");

			$vdevice                     = explode("@", $masterdatavehiclefix[$i]['vehicle_device']);
			$name                        = $vdevice[0];
			$host                        = $vdevice[1];
			$autocheckfrommaster         = json_decode($masterdatavehiclefix[$i]['vehicle_autocheck']);
			$lastcourse 							   = $autocheckfrommaster->auto_last_course;
			$jalur_namefix 				       = $this->m_securityevidence->get_jalurname($lastcourse);
			$frommaster_auto_last_lat    = $autocheckfrommaster->auto_last_lat;
			$frommaster_auto_last_long   = $autocheckfrommaster->auto_last_long;
			$frommaster_auto_last_update = $autocheckfrommaster->auto_last_update;

			// echo "<pre>";
			// var_dump($autocheckfrommaster);die();
			// echo "<pre>";

			// GET COMPANY
			$company           = $this->dashboardmodel->getcompany_idforevidence($masterdatavehiclefix[$i]['vehicle_company']);

			// GET OVERSPEED FROM GEOFENCE
			$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($frommaster_auto_last_long, $frommaster_auto_last_lat, $masterdatavehiclefix[$i]['vehicle_user_id'], "webtracking_gps_temanindobara_live");
			print_r("OVERSPEED CHECK STARTED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . $masterdatavehiclefix[$i]['vehicle_mv03'] . "\r\n");
			if ($geofence_location) {
				$geofence_type = $geofence_location[0]->geofence_type;
				$geofence_name = $geofence_location[0]->geofence_name;

					if ($geofence_type == "road") {
						$geofencefix   = $geofence_location[0]->geofence_name;
						$current_speed = $autocheckfrommaster->auto_last_speed;
						if ($jalur_namefix == "kosongan") {
							$speedlimitforcount = $geofence_location[0]->geofence_speed;
							$speedlimitforview  = $geofence_location[0]->geofence_speed_alias;
						}elseif ($jalur_namefix == "muatan") {
							$speedlimitforcount = $geofence_location[0]->geofence_speed_muatan;
							$speedlimitforview  = $geofence_location[0]->geofence_speed_muatan_alias;
						}

						if ($current_speed >= $speedlimitforcount) {
							print_r("========================OVERSPEED FOUNDED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
							$positionalert     = $this->gpsmodel->GeoReverse($frommaster_auto_last_lat, $frommaster_auto_last_long);

							if ($positionalert->display_name != "Unknown Location!") {
								$positionexplode = explode(",", $positionalert->display_name);
								$position = $positionexplode[0];
							}else {
								$position = $positionalert->display_name;
							}

								$datafixoverspeed = array(
									"isfatigue" 				 => "no",
									"jalur_name"				 => $jalur_namefix,
									"vehicle_no"         => $masterdatavehiclefix[$i]['vehicle_no'],
									"vehicle_name"       => $masterdatavehiclefix[$i]['vehicle_name'],
									"vehicle_device"     => $masterdatavehiclefix[$i]['vehicle_device'],
									"vehicle_mv03"    	 => $masterdatavehiclefix[$i]['vehicle_mv03'],
									"gps_latitude_real"  => $frommaster_auto_last_lat,
									"gps_longitude_real" => $frommaster_auto_last_long,
									"gps_speed"          => $current_speed,
									"gps_speed_limit"    => $speedlimitforview,
									"gps_alert"          => "Overspeed",
									// "gps_time"           => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($overspeeddata[0]['gps_time']))),
									"gps_time"           => date("Y-m-d H:i:s", strtotime($frommaster_auto_last_update)),
									"geofence"           => $geofencefix,
									"position" 					 => $position
								);

								if ($position == "Simpang Bayah - Kosongan" && $jalur_namefix == "muatan") {
									$jalur_namefix = "Kosongan";
								}

								$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);
									if (sizeof($checkindb) < 1) {
										$todatabase = array(
											"violation_vehicle_no"                 => $masterdatavehiclefix[$i]['vehicle_no'],
											"violation_vehicle_name"               => $masterdatavehiclefix[$i]['vehicle_name'],
											"violation_vehicle_device"             => $masterdatavehiclefix[$i]['vehicle_device'],
											"violation_vehicle_mv03" 	             => $masterdatavehiclefix[$i]['vehicle_mv03'],
											"violation_status" 			 	             => 1,
											"violation_vehicle_companyid" 		 	   => $company->company_id,
											"violation_vehicle_companyname" 		 	 => $company->company_name,
											"violation_position" 		 	             => $position,
											"violation_jalur" 		 	               => $jalur_namefix,
											"violation_type_id" 	 	 	             => 9999,
											"violation_type" 	 	 	                 => "overspeed",
											"violation_overspeed" 		             => json_encode($datafixoverspeed),
											"violation_update"   					         => date("Y-m-d H:i:s", strtotime($frommaster_auto_last_update))
											// "violation_update" 				             => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($overspeeddata[0]['gps_time']))) //date("Y-m-d H:i:s")
										);
										$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
											if ($insert) {
												print_r("DATA INSERT OVERSPEED SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											}else {
												print_r("DATA INSERT OVERSPEED FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											}
									}else {
										$todatabase = array(
											"violation_status" 			 	             => 1,
											"violation_position" 		 	             => $position,
											"violation_jalur" 		 	               => $jalur_namefix,
											"violation_vehicle_companyid" 		 	   => $company->company_id,
											"violation_vehicle_companyname" 		 	 => $company->company_name,
											"violation_type" 	    	 	             => "overspeed",
											"violation_type_id" 	 	 	             => 9999,
											"violation_overspeed" 		             => json_encode($datafixoverspeed),
											"violation_update"   					         => date("Y-m-d H:i:s", strtotime($frommaster_auto_last_update))
											// "violation_update" 				             => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($overspeeddata[0]['gps_time']))) //date("Y-m-d H:i:s")
										);
										$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
											if ($update) {
												print_r("DATA UPDATE OVERSPEED SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											}else {
												print_r("DATA UPDATE OVERSPEED FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
											}
									}
							// echo "<pre>";
							// var_dump("OVERSPEED : " . $geofence_name . ' -> ' . $current_speed . ' : ' . $speedlimitforcount . ' : ' . $speedlimitforview);die();
							// // var_dump($geofence_location);die();
							// echo "<pre>";
						}else {
							// JIKA TIDAK TERDAPAT OVERSPEED
							$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);
								if (sizeof($checkindb) < 1) {
									$todatabase = array(
										"violation_vehicle_no"                 => $masterdatavehiclefix[$i]['vehicle_no'],
										"violation_vehicle_name"               => $masterdatavehiclefix[$i]['vehicle_name'],
										"violation_vehicle_device"             => $masterdatavehiclefix[$i]['vehicle_device'],
										"violation_vehicle_mv03" 	             => $masterdatavehiclefix[$i]['vehicle_mv03'],
										"violation_status" 			 	             => 0,
										"violation_vehicle_companyid" 		 	   => $company->company_id,
										"violation_vehicle_companyname" 		 	 => $company->company_name,
										"violation_position" 		 	             => "",
										"violation_jalur" 		 	               => "",
										"violation_overspeed" 		             => "",
										"violation_update" 				             => "",
										"violation_type" 	    	 	             => "",
										"violation_type_id" 	 	 	             => "",
									);
									$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
										if ($insert) {
											print_r("DATA INSERT OVERSPEED VIOLATION STATUS = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}else {
											print_r("DATA INSERT OVERSPEED VIOLATION STATUS = 0 SUCCESS FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}
								}else {
									$todatabase = array(
										"violation_status" 			 	             => 0,
										"violation_vehicle_companyid" 		 	   => $company->company_id,
										"violation_vehicle_companyname" 		 	 => $company->company_name,
										"violation_position" 		 	             => "",
										"violation_jalur" 		 	               => "",
										"violation_overspeed" 		             => "",
										"violation_update" 				             => "",
										"violation_type" 	    	 	             => "",
										"violation_type_id" 	 	 	             => "",
									);
									$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
										if ($update) {
											print_r("DATA UPDATE OVERSPEED VIOLATION STATUS = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}else {
											print_r("DATA UPDATE OVERSPEED VIOLATION STATUS = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}
								}
								print_r("===================================================== \r\n");
						}
					}
			}

			// CEK FATIGUE VIOLATION
			if($masterdatavehiclefix[$i]['vehicle_mv03'] != "0000") {
				print_r("FATIGUE CHECK STARTED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . $masterdatavehiclefix[$i]['vehicle_mv03'] . "\r\n");
					$evidence      = $this->m_securityevidence->violation_fatigue($dbtable, $masterdatavehiclefix[$i]['vehicle_mv03'], $datatimeforevidence, $alarmtypefromaster);
						// JIKA TERDAPAT FATIGUE
						if (sizeof($evidence) > 0) {
							print_r("========================FATIGUE FOUNDED  : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
							$vdevice                 = $evidence[0]['alarm_report_vehicle_id']."@".$evidence[0]['alarm_report_vehicle_type'];
							$datavehicle             = $this->m_securityevidence->getdevicebydevID($vdevice);
							$reportdetaildecode      = explode("|", $evidence[0]['alarm_report_gpsstatus']);
							$speedgps                = number_format($reportdetaildecode[4]/10, 0, '.', '');
							$forcheck_vehicledevice  = $vdevice;
							$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
							$jalur_name              = $jsonautocheck->auto_last_road;

							// echo "<pre>";
							// var_dump($evidence[0]);die();
							// echo "<pre>";

							$datafixfatigue = array(
								"isfatigue" 				 => "yes",
								"vehicle_no"         => $masterdatavehiclefix[$i]['vehicle_no'],
								"vehicle_name"       => $masterdatavehiclefix[$i]['vehicle_name'],
								"vehicle_device"     => $masterdatavehiclefix[$i]['vehicle_device'],
								"vehicle_mv03"       => $masterdatavehiclefix[$i]['vehicle_mv03'],
								"gps_alertid"        => $evidence[0]['alarm_report_type'],
								"gps_alert"          => $evidence[0]['alarm_report_name'],
								"gps_time"           => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time']) + 1 * 3600),
								"gps_latitude_real"  => $evidence[0]['alarm_report_coordinate_start'],
								"gps_longitude_real" => $evidence[0]['alarm_report_coordinate_start'],
								"position"           => $evidence[0]['alarm_report_location_start'],
								"gps_speed"          => $speedgps,
							);

							$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);

							if ($evidence[0]['alarm_report_location_start'] == "Simpang Bayah - Kosongan" && $jalur_name == "muatan") {
								$jalur_name = "Kosongan";
							}

								if (sizeof($checkindb) < 1) {
									$todatabase = array(
										"violation_vehicle_no"                 => $masterdatavehiclefix[$i]['vehicle_no'],
										"violation_vehicle_name"               => $masterdatavehiclefix[$i]['vehicle_name'],
										"violation_vehicle_device"             => $masterdatavehiclefix[$i]['vehicle_device'],
										"violation_vehicle_mv03" 	             => $masterdatavehiclefix[$i]['vehicle_mv03'],
										"violation_status" 			 	             => 1,
										"violation_vehicle_companyid" 		 	   => $company->company_id,
										"violation_vehicle_companyname" 		 	 => $company->company_name,
										"violation_type" 	 	 	                 => "fatigue",
										"violation_type_id" 	 	 	             => $evidence[0]['alarm_report_type'],
										"violation_position" 		 	             => $evidence[0]['alarm_report_location_start'],
										"violation_jalur" 		 	               => $jalur_name,
										"violation_fatigue" 		               => json_encode($datafixfatigue),
										"violation_update" 				             => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time']) + 1 * 3600) //date("Y-m-d H:i:s")
									);
									$insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
										if ($insert) {
											print_r("DATA INSERT FATIGUE SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}else {
											print_r("DATA INSERT FATIGUE FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}
								}else {
									$todatabase = array(
										"violation_status" 			 	             => 1,
										"violation_vehicle_companyid" 		 	   => $company->company_id,
										"violation_vehicle_companyname" 		 	 => $company->company_name,
										"violation_type" 	    	 	             => "fatigue",
										"violation_type_id" 	 	 	             => $evidence[0]['alarm_report_type'],
										"violation_position" 		 	             => $evidence[0]['alarm_report_location_start'],
										"violation_jalur" 		 	               => $jalur_name,
										"violation_fatigue" 		               => json_encode($datafixfatigue),
										"violation_update" 				             => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time']) + 1 * 3600) //date("Y-m-d H:i:s")
									);
									$update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
										if ($update) {
											print_r("DATA UPDATE FATIGUE SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}else {
											print_r("DATA UPDATE FATIGUE FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
										}
								}
						}else {
							$checkindb = $this->m_securityevidence->checktodbviolation("ts_violation", $masterdatavehiclefix[$i]['vehicle_device']);

							 if (sizeof($checkindb) < 1) {
								 $todatabase = array(
									 "violation_vehicle_no"      => $masterdatavehiclefix[$i]['vehicle_no'],
									 "violation_vehicle_name"    => $masterdatavehiclefix[$i]['vehicle_name'],
									 "violation_vehicle_device"  => $masterdatavehiclefix[$i]['vehicle_device'],
									 "violation_vehicle_mv03" 	 => $masterdatavehiclefix[$i]['vehicle_mv03'],
									 "violation_overspeed" 		   => "",
									 "violation_fatigue" 		     => "",
									 "violation_status" 			 	 => 0,
									 "violation_position" 		 	 => "",
									 "violation_jalur" 		 	     => "",
									 "violation_update" 				 => "",
									 "violation_type" 	 	 	     => "",
									 "violation_type_id" 	 	 	   => "",
								 );
								 $insert = $this->m_securityevidence->insertviolation("ts_violation", $todatabase);
									 if ($insert) {
										 print_r("DATA INSERT VIOLATION ON FATIGUE = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									 }else {
										 print_r("DATA INSERT VIOLATION ON FATIGUE = 0 FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									 }
							 }else {
								 $todatabase = array(
									 "violation_vehicle_no"                => $masterdatavehiclefix[$i]['vehicle_no'],
									 "violation_vehicle_name"              => $masterdatavehiclefix[$i]['vehicle_name'],
									 "violation_vehicle_device"            => $masterdatavehiclefix[$i]['vehicle_device'],
									 "violation_vehicle_mv03" 	           => $masterdatavehiclefix[$i]['vehicle_mv03'],
									 "violation_overspeed" 		             => "",
									 "violation_fatigue" 		               => "",
									 "violation_status" 			 	           => 0,
									 "violation_vehicle_companyid" 		 	   => $company->company_id,
									 "violation_vehicle_companyname" 		 	 => $company->company_name,
									 "violation_position" 		 	           => "",
									 "violation_jalur" 		 	               => "",
									 "violation_update" 				           => "",
									 "violation_type" 	 	 	               => "",
									 "violation_type_id" 	 	 	             => "",
								 );
								 $update = $this->m_securityevidence->updateviolation("ts_violation", "violation_vehicle_device", $masterdatavehiclefix[$i]['vehicle_device'], $todatabase);
									 if ($update) {
										 print_r("DATA UPDATE VIOLATION ON FATIGUE = 0 SUCCESS : ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									 }else {
										 print_r("DATA UPDATE VIOLATION ON FATIGUE = 0 FAILED: ". $masterdatavehiclefix[$i]['vehicle_no'].' - '.$masterdatavehiclefix[$i]['vehicle_name'] . "\r\n");
									 }
							 }
							 print_r("===================================================== \r\n");
						}
			}
		}

		// datafixoverspeed
		// datafixfatigue
		// echo "<pre>";
		// var_dump("FINIDONE");die();
		// echo "<pre>";

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
	}













}
