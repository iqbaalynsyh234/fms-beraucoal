<?php
include "base.php";

class Sariroti_new extends Base {
	//function __construct()
	function Sariroti_new()
	{
		//parent::__construct();	
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
		$this->load->helper('xml');
		$this->load->helper('download');
		//$this->load->library('ftp');
		/*$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');*/
		
	}
	
	function getota()
	{
		$dom = xml_dom();
		$ReportOTA = xml_add_child($dom, 'ReportOTA');
		
		$start = $this->input->get('DateFrom',true);
		$end = $this->input->get('DateTo',true);
		$plant = $this->input->get('PlantCode',true);
		
		$this->dbtransporter = $this->load->database("transporter_balrich",true);
		$this->dbreport = $this->load->database("balrich_report",true);
		$userid = 1032;
		
		if(!isset($start) || $start=="")
		{
			/*$feature["data"] = "Invalid Start Time";
			$content = json_encode($feature);
			echo $content;*/
			exit;
		}
		if(!isset($end) || $end=="")
		{
			/*$feature["data"] = "Invalid End Time";
			$content = json_encode($feature);
			echo $content;*/
			exit;
		}
		if(!isset($plant) || $plant=="")
		{
			/*$feature["data"] = "Invalid Plant Code";
			$content = json_encode($feature);
			echo $content;*/
			exit;
		}
		
		$start = date("Y-m-d",strtotime($start));
		$end = date("Y-m-d",strtotime($end));
		
		$month_start = date("F", strtotime($start));
		$month_end = date("F", strtotime($end));
		
		$year_start = date("Y", strtotime($start));
		$year_end = date("Y", strtotime($end));
		
		if(!isset($month_start) || $month_start != $month_end)
		{
			/*$feature["data"] = "Invalid Month";
			$content = json_encode($feature);
			echo $content;*/
			exit;
		}
		
		if(!isset($year_start) || $year_start != $year_end)
		{
			/*$feature["data"] = "Invalid Year";
			$content = json_encode($feature);
			echo $content;*/
			exit;
		}
		
		//print_r($start." ".$end." ".$plant);exit();
		//identify date
		$m1 = date("F", strtotime($start)); 
		$month = date("m", strtotime($start)); 
		$year = date("Y", strtotime($start));
		$sdate = $start;
		$edate = $end;
		$sdate_only = date("d", strtotime($sdate));
		$sdate_zone = $start;
		
		// get data monthly report
		$report = "inout_geofence_";
		$data = $this->get_monthly_report($sdate,$edate);
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$month_name = "Januari";
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$month_name = "Februari";
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$month_name = "Maret";
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$month_name = "April";
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$month_name = "Mei";
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$month_name = "Juni";
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$month_name = "Juli";
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$month_name = "Agustus";
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$month_name = "September";
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$month_name = "Oktober";
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$month_name = "November";
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$month_name = "Desember";
			break;
		}
		
		//select table ota report
		$this->dbtransporter->select("droppoint_id,droppoint_name,droppoint_code_real,droppoint_koord,
									  distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,
									  distrep_sat_status,distrep_sat_distrep_code,distrep_sat_distrep_name,distrep_sat_outlet_code,distrep_sat_outlet_name,
									  plant_code_real,plant_code
									");
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("droppoint_creator",$userid);
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("plant_code_real",$plant);
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
		$this->dbtransporter->join("droppoint_plant", "distrep_plant = plant_id", "left");
		$this->dbtransporter->limit(10);
		$q_droppoint = $this->dbtransporter->get("droppoint");
		$droppoint = $q_droppoint->result();
		
		//total data ota
		if($q_droppoint->num_rows == 0)
		{
			/*$feature["data"] = "No Data Droppoint!";
			$content = json_encode($feature);
			echo $content;*/
			exit;
		}
		
		//select ota berdasarkan master droppoint
		for($i=0; $i < count($droppoint); $i++){ 
			
			$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
			$q_report = $this->dbreport->get($dbtable);
			$row_report = $q_report->row();
				
				//jika toko sat
				if(isset($droppoint) && ($droppoint[$i]->distrep_sat_status == 1)){
					$OutletCode = $droppoint[$i]->distrep_sat_outlet_code;
					$DistrepCode = $droppoint[$i]->distrep_sat_distrep_code;
					$DistrepName = $droppoint[$i]->distrep_sat_distrep_name;
					$OutletName = $droppoint[$i]->distrep_sat_outlet_name;
					$SubOutletName = $droppoint[$i]->droppoint_name;
				}
				else
				{
					$OutletCode = $droppoint[$i]->droppoint_code_real;
					$DistrepCode = $droppoint[$i]->distrep_code;
					$DistrepName = $droppoint[$i]->distrep_name;
					$OutletName = $droppoint[$i]->droppoint_name;
					$SubOutletName = " ";
				}
				
				$ota = xml_add_child($ReportOTA, 'ota');
				xml_add_child($ota, 'PlantCode', $droppoint[$i]->plant_code_real);
				xml_add_child($ota, 'PlantName', $droppoint[$i]->plant_code);
				xml_add_child($ota, 'OutletCode', $OutletCode);
				xml_add_child($ota, 'DistrepCode', $DistrepCode);
				xml_add_child($ota, 'DistrepName', $DistrepName);
				xml_add_child($ota, 'OutletName', $OutletName);
				xml_add_child($ota, 'SubOutletName', $SubOutletName);
				xml_add_child($ota, 'Month', strtoupper($month_name));
				xml_add_child($ota, 'Transporter', "BALRICH");
				xml_add_child($ota, 'Coordinat', $droppoint[$i]->droppoint_koord);
				
				$TargetOta = " ";
				$this->dbtransporter->limit(1);
				$this->dbtransporter->order_by("target_startdate", "asc");							
				$this->dbtransporter->select("target_startdate,target_enddate,target_time");
				$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
				$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
				$this->dbtransporter->where("target_startdate >=",$sdate);
				$this->dbtransporter->where("target_creator",$userid);
				$this->dbtransporter->where("target_flag",0);
				$q_target2 = $this->dbtransporter->get("droppoint_target");
				$target2 = $q_target2->row();
				$total_target2 = count($target2);
				
				if($total_target2 == 0){
					$this->dbtransporter->limit(1);
					$this->dbtransporter->order_by("target_startdate", "desc");
					$this->dbtransporter->select("target_time");
					$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
					$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
					$this->dbtransporter->where("target_month",$month);
					$this->dbtransporter->where("target_year",$year);
					$this->dbtransporter->where("target_flag",0);
					$q_target2 = $this->dbtransporter->get("droppoint_target");
					$target2 = $q_target2->row();
				}	
				if(isset($target2) && (count($target2) > 0)){
					$TargetOta = date("H:i", strtotime($target2->target_time));
				}
				
				xml_add_child($ota, 'TargetOTA', $TargetOta);
				
				//daily ota
				for($j=0; $j < count($data); $j++){
							$georeport_time_alert = "";
							$georeport_time_alert_print = "";
							$georeport_time_alert_vehicle = "";
							$georeport_status = "";
							$georeport_comment = "";
							$georeport_km = "";
							$droppoint_target = "";
							$total_target_time = 0;
							$detik_perdata = 0;
							$additional_status = 0;
							$active_comment = 0;
							$mobil_valid = "";
							$plus24jam = 0;
							
							//SCHEDULE JWK
							$limitview = date("Y-m-d", strtotime("yesterday"));
							$sdate_zone = $data[$j]->monthly_date;
							$sdate_only = date("d", strtotime($data[$j]->monthly_date));
							$sdate_month = date("m", strtotime($data[$j]->monthly_date));
							$sdate_day = $data[$j]->monthly_day;
							$sdate_year = $data[$j]->monthly_year;
							$field_time = "georeport_date_".$sdate_only;
							$field_vehicle = "georeport_vehicle_".$sdate_only;
							$field_status = "georeport_status_".$sdate_only;
							$field_time_manual = "georeport_manual_date_".$sdate_only;
							$field_status_manual = "georeport_manual_status_".$sdate_only;
							$field_vehicle_manual = "georeport_manual_vehicle_".$sdate_only;
							$field_comment = "georeport_comment_".$sdate_only;
							$field_km = "georeport_km_".$sdate_only;
							$field_km_manual = "georeport_km_manual_".$sdate_only;
							
							//$reportdate = $sdate_day.", ".$sdate_only." ".$month_name." ".$sdate_year;
							$reportdate = $sdate_day."-".date("d-m-Y", strtotime($sdate_zone));
							//print_r($reportdate);exit();
						
							$sdate_type = $data[$j]->monthly_type; //ganjil //genap //ods
							
							//$post_data = $droppoint[$i]->droppoint_id."|".$dbtable."|".$field_time_manual."|".$field_status_manual."|".$field_vehicle_manual."|".$field_km_manual;
							
							$sdate_type_rabu_sabtu = $data[$j]->monthly_type_crb; //khusus RO cirebon (rabu - sabtu) - kode 3
							$sdate_type_selasa_jumat = $data[$j]->monthly_type_crb; //khusus RO cirebon (selasa jumat) - kode 4
							$sdate_type_senin_kamis = $data[$j]->monthly_type_crb; //khusus RO cirebon (senin kamis) - kode 5
							
							$sdate_type_senin_rabu_jumat = $data[$j]->monthly_type_ckd; //khusus RO cikande (Senin, Rabu , Jumat) - kode 6
							$sdate_type_selasa_kamis = $data[$j]->monthly_type_ckd; //khusus RO cikande (Selasa & Kamis) - kode 7
							$sdate_type_sabtu = $data[$j]->monthly_type_ckd; //khusus RO cikande (Sabtu) - kode 8
							$sdate_type_minggu = $data[$j]->monthly_type_ckd; //khusus RO cikande (Minggu) - kode 9
							$sdate_type_senin_kamis_sabtu = $data[$j]->monthly_type_crb2; //khusus RO Cirebon NEw - kode 10 (senin, kamis, sabtu)
							
							$sdate_type_senin_rabu_jumat_minggu = $data[$j]->monthly_type_sby; //khusus RO SBY 1-3-5-7
							$sdate_type_selasa_kamis_sabtu = $data[$j]->monthly_type_sby; //khusus RO SBY 2-4-6
						
							/*if($norule_date_option == "yes"){
								
								if(($sdate_zone >= $norule_sdate) && ($sdate_zone <= $norule_edate)){ 
									//masuk ods search report (ditampilkan setiap harinya)
									$additional_status = 1;
									
								}
							}*/
							
							//jika ods
							if($droppoint[$i]->distrep_report_status == 0){
								$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
								$q_report = $this->dbreport->get($dbtable);
								$row_report = $q_report->row();
								if(isset($row_report) && (count($row_report) > 0)){
									//print_r($row_report->$field_time);exit();
									if(($row_report->$field_time == "00:00:00" || $row_report->$field_time == 0)  && ($row_report->$field_status_manual != "M")){
										$georeport_time_alert = "";
										if($row_report->$field_status_manual == "Tidak Ada Kiriman"){
											$georeport_time_alert_print = "TIDAK ADA KIRIMAN";
										}else{
											if($sdate_zone <= $limitview){
												$georeport_time_alert_print = "NO DATA";
											}else{
												$georeport_time_alert_print = "";
											}
											
										}
										
										$georeport_time_alert_vehicle = "";
										$georeport_status = "";
										$georeport_comment = "";
										$georeport_km = "";
										$detik_perdata = "";
										
									}
									
									else
									{
										if($row_report->$field_status_manual == "M" && ($row_report->$field_status == "" || $row_report->$field_status == 0)){
											$field_time_new = $field_time_manual;
											$field_vehicle_new = $field_vehicle_manual;
											$field_status_new = $field_status_manual;
											$field_comment_new = $field_comment;
											$field_km_new = $field_km_manual;
											
											
										}else{
											$field_time_new = $field_time;
											$field_vehicle_new = $field_vehicle;
											$field_status_new = $field_status;
											$field_comment_new = $field_comment;
											$field_km_new = $field_km;
												
										}
										
											//mobil valid gps
											/*$mobil_valid = $row_report->$field_vehicle_new;
														
											$this->db->select("vehicle_device");
											$this->db->where("vehicle_no",$row_report->$field_vehicle_new);
											$q_mobil_valid = $this->db->get("vehicle");
											$row_mobil_valid = $q_mobil_valid->row();
											
											if(count($row_mobil_valid)>0){
												$mobil_valid_device = $row_mobil_valid->vehicle_device;
											}*/
										
										$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
										$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
										$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
										$georeport_status = $row_report->$field_status_new;
										$georeport_comment = $row_report->$field_comment_new;
										$georeport_km = $row_report->$field_km_new;
										
										//$georeport_time_alert_datetime = date("Y-m-d H:i:s", strtotime($sdate_zone." ".$georeport_time_alert)); tes
										
										/*$this->dbtransporter->limit(1);
										$this->dbtransporter->order_by("target_startdate", "asc");							
										$this->dbtransporter->select("target_startdate,target_enddate,target_time");
										$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
										$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
										$this->dbtransporter->where("target_startdate >=",$sdate);
										$this->dbtransporter->where("target_creator",1032);
										$this->dbtransporter->where("target_flag",0);
										$q_target_time = $this->dbtransporter->get("droppoint_target");
										$target_time = $q_target_time->row();
										$total_target_time = count($target_time);
										
										if($total_target_time == 0){
											//cek target per tanggal
											$this->dbtransporter->limit(1);
											$this->dbtransporter->order_by("target_startdate", "desc");
											$this->dbtransporter->select("target_time");
											$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
											$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
											$this->dbtransporter->where("target_month",$month);
											$this->dbtransporter->where("target_year",$year);
											$this->dbtransporter->where("target_flag",0);
											$q_target_time = $this->dbtransporter->get("droppoint_target");
										}
										$target_time = $q_target_time->row();*/
										
										//cek target achive
										/*if($distrep_name->distrep_next_day == "0"){
											if(isset($target_time) && (count($target_time) > 0) ){
											$droppoint_target = date("H:i", strtotime($target_time->target_time));
												// cek jika lebih dari target
												if($georeport_time_alert_print > $droppoint_target){
													$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
													$total_red = $total_red + 1;
													$active_comment = 1;
												}
										
											}else{
												$droppoint_target = "nodata";
											}
										}else{
											//cek target achive jika beda hari (4 jam selisih)
											if(isset($target_time) && (count($target_time) > 0)){
												//date plus 1 jika jam ota dari 00:00:00 sampai jam 03.00.00
												$target_ota = $target_time->target_time;
												
												if(($georeport_time_alert_print >= "00:00" && $georeport_time_alert_print <= "03:00:00") && ($target_ota >= "01:30:00")){
													$date = new DateTime($sdate_zone);
													$date->add(new DateInterval('P1D'));
													$sdate_zone_report = $date->format('Y-m-d');
													//$plus24jam = 86400; //detik 1 hari;
													$plus24jam = 0;
													
												}else{
													$sdate_zone_report = $sdate_zone;
												}
												
												//target + 1 menit
												$droppoint_target_tgl_def = date("Y-m-d H:i", strtotime($sdate_zone." ".$target_time->target_time));
												$date = new DateTime($droppoint_target_tgl_def);
												$date->add(new DateInterval('PT60S'));
												$droppoint_target_tgl = $date->format('Y-m-d H:i');
												
												//target + limit (4jam)
												$date = new DateTime($droppoint_target_tgl_def);
												$date->add(new DateInterval('PT8H'));
												$droppoint_target_tgl_limit = $date->format('Y-m-d H:i');
												
												//target + limit time
												$awal_target_limit  = strtotime($droppoint_target_tgl);
												$akhir_target_limit = strtotime($droppoint_target_tgl_limit);
												//detik
												$diff_target_limit  = $akhir_target_limit - $awal_target_limit;
												
												//ota time
												$georeport_time_alert_print_tgl = date("Y-m-d H:i", strtotime($sdate_zone_report." ".$georeport_time_alert_print));
												$time_ota = strtotime($georeport_time_alert_print_tgl);
												//detik
												$diff_time_ota  = $time_ota - $awal_target_limit;
												 
												/*print_r("targetplus: ".$droppoint_target_tgl." "."Limit: ".$droppoint_target_tgl_limit." "."OTA: ".$georeport_time_alert_print_tgl." ! ");
												print_r("D Ota: "." ".$diff_time_ota." "."D Limit: ".$diff_target_limit);exit();*/
												
												//cek achive	
												/*if ($diff_time_ota >= 0 && $diff_time_ota <= $diff_target_limit)
												{
													//$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print." | ".$georeport_time_alert_print_tgl." | ".$diff_time_ota."</font>";
													$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
													$total_red = $total_red + 1;
													$active_comment = 1;
												}
												
											}else{
												$droppoint_target = "nodata";
											}
											
										}*/
										
										/*$total_pengiriman = $total_pengiriman + 1;
										
										$jam_perdata = $georeport_time_alert_print.":"."00";
										$jam_konvert = date_parse($jam_perdata);
										$detik_perdata = $jam_konvert['hour'] * 3600 + $jam_konvert['minute'] * 60 + $jam_konvert['second'] + $plus24jam;
										$total_detik = $total_detik + $detik_perdata;*/
										
									}
									
									
								}
								
							}
							
							//custom & tds
							else
							{   
									// custom view report
									if($droppoint[$i]->distrep_report_status == "3"){ //khusus RO cirebon - Rabu Sabtu
										$sdate_type = $sdate_type_rabu_sabtu;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "4"){ //khusus RO cirebon - Selasa Jumat
										$sdate_type = $sdate_type_selasa_jumat;
										
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "5"){ //khusus RO cirebon - Senin Kamis
										$sdate_type = $sdate_type_senin_kamis;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "6"){ //khusus RO cikande - Senin Rabu Jumat
										$sdate_type = $sdate_type_senin_rabu_jumat;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "7"){ //khusus RO cikande - Selasa Kamis
										$sdate_type = $sdate_type_selasa_kamis;
										
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "8"){ //khusus RO Cikande - Sabtu
										$sdate_type = $sdate_type_sabtu;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "9"){ //khusus RO Cikande - Minggu
										$sdate_type = $sdate_type_minggu;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "10"){ //khusus RO Cirebon New - Senin Kamis Sabtu (new)
										$sdate_type = $sdate_type_senin_kamis_sabtu;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "11"){ //khusus RO SBY - 1-3-5-7
										$sdate_type = $sdate_type_senin_rabu_jumat_minggu;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "12"){ //khusus RO SBY - 2-4-6
										$sdate_type = $sdate_type_selasa_kamis_sabtu;
									}
									
									//jika tds // Custom JWK
									if($sdate_type == $droppoint[$i]->distrep_report_status){
										$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
										$q_report = $this->dbreport->get($dbtable);
										$row_report = $q_report->row();
										if(isset($row_report) && (count($row_report) > 0)){
											
											if(($row_report->$field_time == "00:00:00" || $row_report->$field_time == 0)  && ($row_report->$field_status_manual != "M")){
												$georeport_time_alert = "";
												if($row_report->$field_status_manual == "Tidak Ada Kiriman"){
													$georeport_time_alert_print = "TIDAK ADA KIRIMAN";
												}else{
													if($sdate_zone <= $limitview){
														$georeport_time_alert_print = "NO DATA";
													}else{
														$georeport_time_alert_print = "";
													}
													
												}
												
												$georeport_time_alert_vehicle = "";
												$georeport_status = "";
												$georeport_comment = "";
												$georeport_km = "";
												$detik_perdata = "";
												
											}
											
											else
											{
												if($row_report->$field_status_manual == "M" && ($row_report->$field_status == "" || $row_report->$field_status == 0)){
													$field_time_new = $field_time_manual;
													$field_vehicle_new = $field_vehicle_manual;
													$field_status_new = $field_status_manual;
													$field_comment_new = $field_comment;
													$field_km_new = $field_km_manual;
													
													
												}else{
													$field_time_new = $field_time;
													$field_vehicle_new = $field_vehicle;
													$field_status_new = $field_status;
													$field_comment_new = $field_comment;
													$field_km_new = $field_km;
													
												}
												
													//mobil valid gps
													/*$mobil_valid = $row_report->$field_vehicle_new;
														
													$this->db->select("vehicle_device");
													$this->db->where("vehicle_no",$row_report->$field_vehicle_new);
													$q_mobil_valid = $this->db->get("vehicle");
													$row_mobil_valid = $q_mobil_valid->row();
													
													if(count($row_mobil_valid)>0){
														$mobil_valid_device = $row_mobil_valid->vehicle_device;
													}*/
												
												$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
												$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
												$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
												$georeport_status = $row_report->$field_status_new;
												$georeport_comment = $row_report->$field_comment_new;
												$georeport_km = $row_report->$field_km_new;
										
												//$georeport_time_alert_datetime = date("Y-m-d H:i:s", strtotime($sdate_zone." ".$georeport_time_alert));
												
												/*$this->dbtransporter->limit(1);
												$this->dbtransporter->order_by("target_startdate", "asc");							
												$this->dbtransporter->select("target_startdate,target_enddate,target_time");
												$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
												$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
												$this->dbtransporter->where("target_startdate >=",$sdate);
												$this->dbtransporter->where("target_creator",1032);
												$this->dbtransporter->where("target_flag",0);
												$q_target_time = $this->dbtransporter->get("droppoint_target");
												$target_time = $q_target_time->row();
												$total_target_time = count($target_time);
												
												if($total_target_time == 0){
													//cek target per tanggal
													$this->dbtransporter->limit(1);
													$this->dbtransporter->order_by("target_startdate", "desc");
													$this->dbtransporter->select("target_time");
													$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
													$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
													$this->dbtransporter->where("target_month",$month);
													$this->dbtransporter->where("target_year",$year);
													$this->dbtransporter->where("target_flag",0);
													$q_target_time = $this->dbtransporter->get("droppoint_target");
												}
												$target_time = $q_target_time->row();
												
												if(isset($target_time) && (count($target_time) > 0) ){
													$droppoint_target = date("H:i", strtotime($target_time->target_time));
													// cek jika lebih dari target
													if($georeport_time_alert_print > $droppoint_target){
														$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
														$total_red = $total_red + 1;
														$active_comment = 1;
													}
												
												}else{
													$droppoint_target = "nodata";
												}
												$total_pengiriman = $total_pengiriman + 1;
												
												$jam_perdata = $georeport_time_alert_print.":"."00";
												$jam_konvert = date_parse($jam_perdata);
												$detik_perdata = $jam_konvert['hour'] * 3600 + $jam_konvert['minute'] * 60 + $jam_konvert['second'];
												$total_detik = $total_detik + $detik_perdata;
												*/
												
											}
											
											
										}
									}
								
							}
							
						//print daily ota
						//print_r($georeport_time_alert_print);exit();
						//$feature["data"][$i]->$reportdate = $georeport_time_alert_print;
						xml_add_child($ota, $reportdate, $georeport_time_alert_print);
						//xml_add_child($ota, $reportdate, $georeport_time_alert_print);
						
				}//for daily ota
				
		}
		
		
		xml_download($dom); 
		
	}
	
	function getota_json()
	{
		print_r("connection close");exit();
		header("Content-Type: application/json");
		$start = $this->input->get('DateFrom',true);
		$end = $this->input->get('DateTo',true);
		$plant = $this->input->get('PlantCode',true);
		$feature = array();
		$this->dbtransporter = $this->load->database("transporter_balrich",true);
		$this->dbreport = $this->load->database("balrich_report",true);
		$userid = 1032;
		
		if(!isset($start) || $start=="")
		{
			$feature["data"] = "Invalid Start Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		if(!isset($end) || $end=="")
		{
			$feature["data"] = "Invalid End Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		if(!isset($plant) || $plant=="")
		{
			$feature["data"] = "Invalid Plant Code";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		$start = date("Y-m-d",strtotime($start));
		$end = date("Y-m-d",strtotime($end));
		
		$month_start = date("F", strtotime($start));
		$month_end = date("F", strtotime($end));
		
		$year_start = date("Y", strtotime($start));
		$year_end = date("Y", strtotime($end));
		
		if(!isset($month_start) || $month_start != $month_end)
		{
			$feature["data"] = "Invalid Month";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		if(!isset($year_start) || $year_start != $year_end)
		{
			$feature["data"] = "Invalid Year";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		//print_r($start." ".$end." ".$plant);exit();
		//identify date
		$m1 = date("F", strtotime($start)); 
		$month = date("m", strtotime($start)); 
		$year = date("Y", strtotime($start));
		$sdate = $start;
		$edate = $end;
		$sdate_only = date("d", strtotime($sdate));
		$sdate_zone = $start;
		
		// get data monthly report
		$report = "inout_geofence_";
		$data = $this->get_monthly_report($sdate,$edate);
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$month_name = "Januari";
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$month_name = "Februari";
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$month_name = "Maret";
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$month_name = "April";
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$month_name = "Mei";
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$month_name = "Juni";
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$month_name = "Juli";
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$month_name = "Agustus";
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$month_name = "September";
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$month_name = "Oktober";
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$month_name = "November";
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$month_name = "Desember";
			break;
		}
		
		//select table ota report
		$this->dbtransporter->select("droppoint_id,droppoint_name,droppoint_code_real,droppoint_koord,
									  distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,
									  distrep_sat_status,distrep_sat_distrep_code,distrep_sat_distrep_name,distrep_sat_outlet_code,distrep_sat_outlet_name,
									  plant_code_real,plant_code
									");
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("droppoint_creator",$userid);
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("plant_code_real",$plant);
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
		$this->dbtransporter->join("droppoint_plant", "distrep_plant = plant_id", "left");
		//$this->dbtransporter->limit(100);
		$q_droppoint = $this->dbtransporter->get("droppoint");
		$droppoint = $q_droppoint->result();
		
		//total data ota
		if($q_droppoint->num_rows == 0)
		{
			$feature["data"] = "No Data Droppoint!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		//select ota berdasarkan master droppoint
		for($i=0; $i < count($droppoint); $i++){ 
			
			$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
			$q_report = $this->dbreport->get($dbtable);
			$row_report = $q_report->row();
				
				//jika toko sat
				if(isset($droppoint) && ($droppoint[$i]->distrep_sat_status == 1)){
					$OutletCode = $droppoint[$i]->distrep_sat_outlet_code;
					$DistrepCode = $droppoint[$i]->distrep_sat_distrep_code;
					$DistrepName = $droppoint[$i]->distrep_sat_distrep_name;
					$OutletName = $droppoint[$i]->distrep_sat_outlet_name;
					$SubOutletName = $droppoint[$i]->droppoint_name;
				}
				else
				{
					$OutletCode = $droppoint[$i]->droppoint_code_real;
					$DistrepCode = $droppoint[$i]->distrep_code;
					$DistrepName = $droppoint[$i]->distrep_name;
					$OutletName = $droppoint[$i]->droppoint_name;
					$SubOutletName = "";
				}
			
				$feature["data"][$i]->PlantCode = $droppoint[$i]->plant_code_real;
				$feature["data"][$i]->PlantName = $droppoint[$i]->plant_code;
				$feature["data"][$i]->OutletCode = $OutletCode;
				$feature["data"][$i]->DistrepCode = $DistrepCode;
				$feature["data"][$i]->DistrepName = $DistrepName;
				$feature["data"][$i]->OutletName = $OutletName;
				$feature["data"][$i]->SubOutletName = $SubOutletName;
				$feature["data"][$i]->Month = strtoupper($month_name);
				$feature["data"][$i]->Transporter = "BALRICH";
				$feature["data"][$i]->Coordinat = $droppoint[$i]->droppoint_koord;
				
				//search target ota per month
				$TargetOta = "";
				$this->dbtransporter->limit(1);
				$this->dbtransporter->order_by("target_startdate", "asc");							
				$this->dbtransporter->select("target_startdate,target_enddate,target_time");
				$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
				$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
				$this->dbtransporter->where("target_startdate >=",$sdate);
				$this->dbtransporter->where("target_creator",$userid);
				$this->dbtransporter->where("target_flag",0);
				$q_target2 = $this->dbtransporter->get("droppoint_target");
				$target2 = $q_target2->row();
				$total_target2 = count($target2);
				
				if($total_target2 == 0){
					$this->dbtransporter->limit(1);
					$this->dbtransporter->order_by("target_startdate", "desc");
					$this->dbtransporter->select("target_time");
					$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
					$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
					$this->dbtransporter->where("target_month",$month);
					$this->dbtransporter->where("target_year",$year);
					$this->dbtransporter->where("target_flag",0);
					$q_target2 = $this->dbtransporter->get("droppoint_target");
					$target2 = $q_target2->row();
				}	
				if(isset($target2) && (count($target2) > 0)){
					$TargetOta = date("H:i", strtotime($target2->target_time));
				}
				$feature["data"][$i]->TargetOTA = $TargetOta;
				
				
				//daily ota
				for($j=0; $j < count($data); $j++){
							$georeport_time_alert = "";
							$georeport_time_alert_print = "";
							$georeport_time_alert_vehicle = "";
							$georeport_status = "";
							$georeport_comment = "";
							$georeport_km = "";
							$droppoint_target = "";
							$total_target_time = 0;
							$detik_perdata = 0;
							$additional_status = 0;
							$active_comment = 0;
							$mobil_valid = "";
							$plus24jam = 0;
							
							//SCHEDULE JWK
							$limitview = date("Y-m-d", strtotime("yesterday"));
							$sdate_zone = $data[$j]->monthly_date;
							$sdate_only = date("d", strtotime($data[$j]->monthly_date));
							$sdate_day = $data[$j]->monthly_day;
							$sdate_year = $data[$j]->monthly_year;
							$field_time = "georeport_date_".$sdate_only;
							$field_vehicle = "georeport_vehicle_".$sdate_only;
							$field_status = "georeport_status_".$sdate_only;
							$field_time_manual = "georeport_manual_date_".$sdate_only;
							$field_status_manual = "georeport_manual_status_".$sdate_only;
							$field_vehicle_manual = "georeport_manual_vehicle_".$sdate_only;
							$field_comment = "georeport_comment_".$sdate_only;
							$field_km = "georeport_km_".$sdate_only;
							$field_km_manual = "georeport_km_manual_".$sdate_only;
							
							$reportdate = $sdate_day.", ".$sdate_only." ".$month_name." ".$sdate_year;
							
							$sdate_type = $data[$j]->monthly_type; //ganjil //genap //ods
							
							//$post_data = $droppoint[$i]->droppoint_id."|".$dbtable."|".$field_time_manual."|".$field_status_manual."|".$field_vehicle_manual."|".$field_km_manual;
							
							$sdate_type_rabu_sabtu = $data[$j]->monthly_type_crb; //khusus RO cirebon (rabu - sabtu) - kode 3
							$sdate_type_selasa_jumat = $data[$j]->monthly_type_crb; //khusus RO cirebon (selasa jumat) - kode 4
							$sdate_type_senin_kamis = $data[$j]->monthly_type_crb; //khusus RO cirebon (senin kamis) - kode 5
							
							$sdate_type_senin_rabu_jumat = $data[$j]->monthly_type_ckd; //khusus RO cikande (Senin, Rabu , Jumat) - kode 6
							$sdate_type_selasa_kamis = $data[$j]->monthly_type_ckd; //khusus RO cikande (Selasa & Kamis) - kode 7
							$sdate_type_sabtu = $data[$j]->monthly_type_ckd; //khusus RO cikande (Sabtu) - kode 8
							$sdate_type_minggu = $data[$j]->monthly_type_ckd; //khusus RO cikande (Minggu) - kode 9
							$sdate_type_senin_kamis_sabtu = $data[$j]->monthly_type_crb2; //khusus RO Cirebon NEw - kode 10 (senin, kamis, sabtu)
							
							$sdate_type_senin_rabu_jumat_minggu = $data[$j]->monthly_type_sby; //khusus RO SBY 1-3-5-7
							$sdate_type_selasa_kamis_sabtu = $data[$j]->monthly_type_sby; //khusus RO SBY 2-4-6
						
							/*if($norule_date_option == "yes"){
								
								if(($sdate_zone >= $norule_sdate) && ($sdate_zone <= $norule_edate)){ 
									//masuk ods search report (ditampilkan setiap harinya)
									$additional_status = 1;
									
								}
							}*/
							
							//jika ods
							if($droppoint[$i]->distrep_report_status == 0){
								$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
								$q_report = $this->dbreport->get($dbtable);
								$row_report = $q_report->row();
								if(isset($row_report) && (count($row_report) > 0)){
									//print_r($row_report->$field_time);exit();
									if(($row_report->$field_time == "00:00:00" || $row_report->$field_time == 0)  && ($row_report->$field_status_manual != "M")){
										$georeport_time_alert = "";
										if($row_report->$field_status_manual == "Tidak Ada Kiriman"){
											$georeport_time_alert_print = "TIDAK ADA KIRIMAN";
										}else{
											if($sdate_zone <= $limitview){
												$georeport_time_alert_print = "NO DATA";
											}else{
												$georeport_time_alert_print = "";
											}
											
										}
										
										$georeport_time_alert_vehicle = "";
										$georeport_status = "";
										$georeport_comment = "";
										$georeport_km = "";
										$detik_perdata = "";
										
									}
									
									else
									{
										if($row_report->$field_status_manual == "M" && ($row_report->$field_status == "" || $row_report->$field_status == 0)){
											$field_time_new = $field_time_manual;
											$field_vehicle_new = $field_vehicle_manual;
											$field_status_new = $field_status_manual;
											$field_comment_new = $field_comment;
											$field_km_new = $field_km_manual;
											
											
										}else{
											$field_time_new = $field_time;
											$field_vehicle_new = $field_vehicle;
											$field_status_new = $field_status;
											$field_comment_new = $field_comment;
											$field_km_new = $field_km;
												
										}
										
											//mobil valid gps
											/*$mobil_valid = $row_report->$field_vehicle_new;
														
											$this->db->select("vehicle_device");
											$this->db->where("vehicle_no",$row_report->$field_vehicle_new);
											$q_mobil_valid = $this->db->get("vehicle");
											$row_mobil_valid = $q_mobil_valid->row();
											
											if(count($row_mobil_valid)>0){
												$mobil_valid_device = $row_mobil_valid->vehicle_device;
											}*/
										
										$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
										$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
										$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
										$georeport_status = $row_report->$field_status_new;
										$georeport_comment = $row_report->$field_comment_new;
										$georeport_km = $row_report->$field_km_new;
										
										//$georeport_time_alert_datetime = date("Y-m-d H:i:s", strtotime($sdate_zone." ".$georeport_time_alert)); tes
										
										/*$this->dbtransporter->limit(1);
										$this->dbtransporter->order_by("target_startdate", "asc");							
										$this->dbtransporter->select("target_startdate,target_enddate,target_time");
										$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
										$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
										$this->dbtransporter->where("target_startdate >=",$sdate);
										$this->dbtransporter->where("target_creator",1032);
										$this->dbtransporter->where("target_flag",0);
										$q_target_time = $this->dbtransporter->get("droppoint_target");
										$target_time = $q_target_time->row();
										$total_target_time = count($target_time);
										
										if($total_target_time == 0){
											//cek target per tanggal
											$this->dbtransporter->limit(1);
											$this->dbtransporter->order_by("target_startdate", "desc");
											$this->dbtransporter->select("target_time");
											$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
											$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
											$this->dbtransporter->where("target_month",$month);
											$this->dbtransporter->where("target_year",$year);
											$this->dbtransporter->where("target_flag",0);
											$q_target_time = $this->dbtransporter->get("droppoint_target");
										}
										$target_time = $q_target_time->row();*/
										
										//cek target achive
										/*if($distrep_name->distrep_next_day == "0"){
											if(isset($target_time) && (count($target_time) > 0) ){
											$droppoint_target = date("H:i", strtotime($target_time->target_time));
												// cek jika lebih dari target
												if($georeport_time_alert_print > $droppoint_target){
													$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
													$total_red = $total_red + 1;
													$active_comment = 1;
												}
										
											}else{
												$droppoint_target = "nodata";
											}
										}else{
											//cek target achive jika beda hari (4 jam selisih)
											if(isset($target_time) && (count($target_time) > 0)){
												//date plus 1 jika jam ota dari 00:00:00 sampai jam 03.00.00
												$target_ota = $target_time->target_time;
												
												if(($georeport_time_alert_print >= "00:00" && $georeport_time_alert_print <= "03:00:00") && ($target_ota >= "01:30:00")){
													$date = new DateTime($sdate_zone);
													$date->add(new DateInterval('P1D'));
													$sdate_zone_report = $date->format('Y-m-d');
													//$plus24jam = 86400; //detik 1 hari;
													$plus24jam = 0;
													
												}else{
													$sdate_zone_report = $sdate_zone;
												}
												
												//target + 1 menit
												$droppoint_target_tgl_def = date("Y-m-d H:i", strtotime($sdate_zone." ".$target_time->target_time));
												$date = new DateTime($droppoint_target_tgl_def);
												$date->add(new DateInterval('PT60S'));
												$droppoint_target_tgl = $date->format('Y-m-d H:i');
												
												//target + limit (4jam)
												$date = new DateTime($droppoint_target_tgl_def);
												$date->add(new DateInterval('PT8H'));
												$droppoint_target_tgl_limit = $date->format('Y-m-d H:i');
												
												//target + limit time
												$awal_target_limit  = strtotime($droppoint_target_tgl);
												$akhir_target_limit = strtotime($droppoint_target_tgl_limit);
												//detik
												$diff_target_limit  = $akhir_target_limit - $awal_target_limit;
												
												//ota time
												$georeport_time_alert_print_tgl = date("Y-m-d H:i", strtotime($sdate_zone_report." ".$georeport_time_alert_print));
												$time_ota = strtotime($georeport_time_alert_print_tgl);
												//detik
												$diff_time_ota  = $time_ota - $awal_target_limit;
												 
												/*print_r("targetplus: ".$droppoint_target_tgl." "."Limit: ".$droppoint_target_tgl_limit." "."OTA: ".$georeport_time_alert_print_tgl." ! ");
												print_r("D Ota: "." ".$diff_time_ota." "."D Limit: ".$diff_target_limit);exit();*/
												
												//cek achive	
												/*if ($diff_time_ota >= 0 && $diff_time_ota <= $diff_target_limit)
												{
													//$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print." | ".$georeport_time_alert_print_tgl." | ".$diff_time_ota."</font>";
													$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
													$total_red = $total_red + 1;
													$active_comment = 1;
												}
												
											}else{
												$droppoint_target = "nodata";
											}
											
										}*/
										
										/*$total_pengiriman = $total_pengiriman + 1;
										
										$jam_perdata = $georeport_time_alert_print.":"."00";
										$jam_konvert = date_parse($jam_perdata);
										$detik_perdata = $jam_konvert['hour'] * 3600 + $jam_konvert['minute'] * 60 + $jam_konvert['second'] + $plus24jam;
										$total_detik = $total_detik + $detik_perdata;*/
										
									}
									
									
								}
								
							}
							
							//custom & tds
							else
							{   
									// custom view report
									if($droppoint[$i]->distrep_report_status == "3"){ //khusus RO cirebon - Rabu Sabtu
										$sdate_type = $sdate_type_rabu_sabtu;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "4"){ //khusus RO cirebon - Selasa Jumat
										$sdate_type = $sdate_type_selasa_jumat;
										
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "5"){ //khusus RO cirebon - Senin Kamis
										$sdate_type = $sdate_type_senin_kamis;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "6"){ //khusus RO cikande - Senin Rabu Jumat
										$sdate_type = $sdate_type_senin_rabu_jumat;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "7"){ //khusus RO cikande - Selasa Kamis
										$sdate_type = $sdate_type_selasa_kamis;
										
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "8"){ //khusus RO Cikande - Sabtu
										$sdate_type = $sdate_type_sabtu;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "9"){ //khusus RO Cikande - Minggu
										$sdate_type = $sdate_type_minggu;
									}
									// custom view report
									if($droppoint[$i]->distrep_report_status == "10"){ //khusus RO Cirebon New - Senin Kamis Sabtu (new)
										$sdate_type = $sdate_type_senin_kamis_sabtu;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "11"){ //khusus RO SBY - 1-3-5-7
										$sdate_type = $sdate_type_senin_rabu_jumat_minggu;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "12"){ //khusus RO SBY - 2-4-6
										$sdate_type = $sdate_type_selasa_kamis_sabtu;
									}
									
									//jika tds // Custom JWK
									if($sdate_type == $droppoint[$i]->distrep_report_status){
										$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
										$q_report = $this->dbreport->get($dbtable);
										$row_report = $q_report->row();
										if(isset($row_report) && (count($row_report) > 0)){
											
											if(($row_report->$field_time == "00:00:00" || $row_report->$field_time == 0)  && ($row_report->$field_status_manual != "M")){
												$georeport_time_alert = "";
												if($row_report->$field_status_manual == "Tidak Ada Kiriman"){
													$georeport_time_alert_print = "TIDAK ADA KIRIMAN";
												}else{
													if($sdate_zone <= $limitview){
														$georeport_time_alert_print = "NO DATA";
													}else{
														$georeport_time_alert_print = "";
													}
													
												}
												
												$georeport_time_alert_vehicle = "";
												$georeport_status = "";
												$georeport_comment = "";
												$georeport_km = "";
												$detik_perdata = "";
												
											}
											
											else
											{
												if($row_report->$field_status_manual == "M" && ($row_report->$field_status == "" || $row_report->$field_status == 0)){
													$field_time_new = $field_time_manual;
													$field_vehicle_new = $field_vehicle_manual;
													$field_status_new = $field_status_manual;
													$field_comment_new = $field_comment;
													$field_km_new = $field_km_manual;
													
													
												}else{
													$field_time_new = $field_time;
													$field_vehicle_new = $field_vehicle;
													$field_status_new = $field_status;
													$field_comment_new = $field_comment;
													$field_km_new = $field_km;
													
												}
												
													//mobil valid gps
													/*$mobil_valid = $row_report->$field_vehicle_new;
														
													$this->db->select("vehicle_device");
													$this->db->where("vehicle_no",$row_report->$field_vehicle_new);
													$q_mobil_valid = $this->db->get("vehicle");
													$row_mobil_valid = $q_mobil_valid->row();
													
													if(count($row_mobil_valid)>0){
														$mobil_valid_device = $row_mobil_valid->vehicle_device;
													}*/
												
												$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
												$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
												$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
												$georeport_status = $row_report->$field_status_new;
												$georeport_comment = $row_report->$field_comment_new;
												$georeport_km = $row_report->$field_km_new;
										
												//$georeport_time_alert_datetime = date("Y-m-d H:i:s", strtotime($sdate_zone." ".$georeport_time_alert));
												
												/*$this->dbtransporter->limit(1);
												$this->dbtransporter->order_by("target_startdate", "asc");							
												$this->dbtransporter->select("target_startdate,target_enddate,target_time");
												$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
												$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
												$this->dbtransporter->where("target_startdate >=",$sdate);
												$this->dbtransporter->where("target_creator",1032);
												$this->dbtransporter->where("target_flag",0);
												$q_target_time = $this->dbtransporter->get("droppoint_target");
												$target_time = $q_target_time->row();
												$total_target_time = count($target_time);
												
												if($total_target_time == 0){
													//cek target per tanggal
													$this->dbtransporter->limit(1);
													$this->dbtransporter->order_by("target_startdate", "desc");
													$this->dbtransporter->select("target_time");
													$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
													$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
													$this->dbtransporter->where("target_month",$month);
													$this->dbtransporter->where("target_year",$year);
													$this->dbtransporter->where("target_flag",0);
													$q_target_time = $this->dbtransporter->get("droppoint_target");
												}
												$target_time = $q_target_time->row();
												
												if(isset($target_time) && (count($target_time) > 0) ){
													$droppoint_target = date("H:i", strtotime($target_time->target_time));
													// cek jika lebih dari target
													if($georeport_time_alert_print > $droppoint_target){
														$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
														$total_red = $total_red + 1;
														$active_comment = 1;
													}
												
												}else{
													$droppoint_target = "nodata";
												}
												$total_pengiriman = $total_pengiriman + 1;
												
												$jam_perdata = $georeport_time_alert_print.":"."00";
												$jam_konvert = date_parse($jam_perdata);
												$detik_perdata = $jam_konvert['hour'] * 3600 + $jam_konvert['minute'] * 60 + $jam_konvert['second'];
												$total_detik = $total_detik + $detik_perdata;
												*/
												
											}
											
											
										}
									}
								
							}
							
						//print daily ota
						$feature["data"][$i]->$reportdate = $georeport_time_alert_print;
						
				}//for daily ota
				
		}
		
		if(count($feature) == 0)
		{
			$feature["data"] = "Data Not Avaliable!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		echo json_encode($feature);
		exit;
	}
	
	function get_monthly_report($sdate,$edate){
		$this->dbtransporter = $this->load->database("transporter_balrich", TRUE);
		$this->dbtransporter->order_by("monthly_date","asc");
		$this->dbtransporter->where("monthly_date >=", $sdate);
		$this->dbtransporter->where("monthly_date <=", $edate);
		$qd = $this->dbtransporter->get("config_monthly_report");
		$rd = $qd->result();
		
		return $rd;
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
