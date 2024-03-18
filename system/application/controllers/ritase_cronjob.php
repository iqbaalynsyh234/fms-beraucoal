<?php
include "base.php";

class Ritase_cronjob extends Base {
	function __construct()
	{
		parent::__construct();

		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');

	}
	
	function hour($userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		$startdate = date("Y-m-d");
		$starthour = "00:00:00";
		$enddate = date("Y-m-d");
		$endhour = "23:59:59";

		$report = "location_";
		$report_ritase = "ritase_hour_";
		
		$days = date("d", strtotime($startdate));
		$month = date("F", strtotime($startdate));
		$year = date("Y", strtotime($startdate));
		$before_status = 0;
		
		$year_before = date('Y',strtotime('-1 year',strtotime($year)));
	
		//jika first date maka ambil data dari table before
		if($days == "01"){
			$before_status = 1;
		}
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			$dbtable_before = $report."desember_".$year_before;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			$dbtable_before = $report."januari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			$dbtable_before = $report."februari_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			$dbtable_before = $report."maret_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			$dbtable_before = $report."april_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			$dbtable_before = $report."mei_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			$dbtable_before = $report."juni_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			$dbtable_before = $report."juli_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			$dbtable_before = $report."agustus_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			$dbtable_before = $report."september_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			$dbtable_before = $report."oktober_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			$dbtable_before = $report."november_".$year;
			break;
		}
		
		
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate." ".$starthour))); //wita
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate." ".$endhour)));  //wita
		
		printf("===STARTING RITASE HOUR SNAPSHOT %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_name","asc");
		$this->db->select("company_name,company_id");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $userid);
		$q = $this->db->get("company");
		$rows = $q->result();
		$totalcompany = count($rows);
		printf("===TOTAL COMPANY %s \r\n", $totalcompany);
		
		for ($i=0;$i<$totalcompany;$i++)
		{
			$j = $i+1;
			$total_unit = 0;
			$total_duty = 0;
			$total_duty_persen = 0;
			$total_idle = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_mv03,vehicle_type,vehicle_dbname_live");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$j2 = $x+1;
				printf("===SDATE %s EDATE %s \r\n", $sdate, $edate);
				printf("===PROCESS VEHICLE %s of %s, %s, %s, %s of %s\r\n", $j2, $totalvehicle, $rowvehicle[$x]->vehicle_no, $rows[$i]->company_name, $j, $totalcompany);
						$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);
						
						$this->dblive = $this->load->database($rowvehicle[$x]->vehicle_dbname_live,true);
						$this->dblive->order_by("ritase_gpstime","asc");
						$this->dblive->group_by("ritase_last_site_datetime");
						//$this->dblive->distinct("ritase_last_site_datetime");
                        $this->dblive->where("ritase_device", $vehicle_device[0]);
                        $this->dblive->where("ritase_last_site_datetime >=", $sdate);
                        $this->dblive->where("ritase_last_site_datetime <=", $edate);
						$this->dblive->from("ritase");
                        $q_live = $this->dblive->get();
                        $rows_live = $q_live->result();
						$trows_live = count($rows_live);
						printf("TOTAL DATA DBLIVE : %s \r\n",$trows_live);
						
						//detail data
						if ($trows_live > 0)
						{
							for($z=0;$z<$trows_live;$z++)
							{
								$position_start_name =  $rows_live[$z]->ritase_last_site;
								$geofence_start_name =  $rows_live[$z]->ritase_last_site;

								$position_end_name = $rows_live[$z]->ritase_last_dest;
								$geofence_end_name = $rows_live[$z]->ritase_last_dest;

								$ritase_report_vehicle_user_id = $rowvehicle[$x]->vehicle_user_id;
								$ritase_report_vehicle_id = $rowvehicle[$x]->vehicle_id;
								$ritase_report_vehicle_device = $rowvehicle[$x]->vehicle_device;
								$ritase_report_vehicle_no = $rowvehicle[$x]->vehicle_no;
								$ritase_report_vehicle_name = $rowvehicle[$x]->vehicle_name;
								$ritase_report_vehicle_type = $rowvehicle[$x]->vehicle_type;
								//$ritase_report_imei = $rowvehicle[$x]->vehicle_mv03;
								$ritase_report_vehicle_company = $rowvehicle[$x]->vehicle_company;
								$ritase_report_company_name = $rows[$i]->company_name;
								$ritase_report_type = 0;
								$ritase_report_name = "ritase";

								$ritase_report_start_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows_live[$z]->ritase_last_site_datetime))); //sudah wita
								$ritase_report_start_location = $position_start_name;
								$ritase_report_start_geofence = $geofence_start_name;
								$ritase_report_start_coordinate = "";
								
								$ritase_report_start_date = date("Y-m-d", strtotime($ritase_report_start_time));
								$ritase_report_start_hour = date("H", strtotime($ritase_report_start_time)).":00:00";

								$ritase_report_end_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows_live[$z]->ritase_gpstime))); //sudah wita
								$ritase_report_end_location = $position_end_name;
								$ritase_report_end_geofence = $geofence_end_name;
								$ritase_report_end_coordinate = "";

								
								$ritase_report_end_date = date("Y-m-d", strtotime($ritase_report_end_time));
								$ritase_report_end_hour = date("H", strtotime($ritase_report_end_time)).":00:00";
							
								
								$ritase_report_driver = 0;
								$ritase_report_driver_name = "";
								
								$duration = get_time_difference($ritase_report_start_time, $ritase_report_end_time);

									$start_1 = dbmaketime($ritase_report_start_time);
									$end_1 = dbmaketime($ritase_report_end_time);
									$duration_sec = $end_1 - $start_1;

                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }

								$ritase_report_duration = $show;
								$ritase_report_duration_sec = $duration_sec;

								$ritase_report_port_start_time = $ritase_report_end_time;
								$ritase_report_port_end_time = $ritase_report_end_time;
								$ritase_report_port_duration_sec = 0;
								$ritase_report_port_duration = "";
								$ritase_report_wim_start_time = "";
								$ritase_report_wim2_start_time = "";
								$ritase_report_rom_start_time = $ritase_report_start_time;
								$ritase_report_rom_end_time = $ritase_report_start_time;
								$ritase_report_rom_duration_sec = 0;
								$ritase_report_rom_duration = "";
							
								//check shift 
								$this->dbts = $this->load->database("webtracking_ts", TRUE);
								$this->dbts->select("hour_shift,hour_diff");
								$this->dbts->order_by("hour_name","asc");
								$this->dbts->where("hour_flag",0);
								$this->dbts->where("hour_time",$ritase_report_end_hour);
								$qhour = $this->dbts->get("ts_hour_shift");
								$rowshour = $qhour->row();
								$totalhour = count($rowshour);
								if($totalhour> 0 ){
									$shift_name = $rowshour->hour_shift;
									$date_diff = $rowshour->hour_diff;
									if($rowshour->hour_shift == 2)
									{
										$shift_name = "NS";
									}else{
										$shift_name = "DS";
									}
								}else{
									$shift_name = "";
									$date_diff = 0;
								}
								
								$this->dbts->close();
								
								$ritase_report_shift_name = $shift_name;
								$ritase_report_shift_date = date("Y-m-d H:i:s", strtotime($ritase_report_end_date ." - ".$date_diff." "."days"));
								
								//INSERT TO RITASE HOUR
							
								$datainsert["ritase_report_vehicle_user_id"] = $ritase_report_vehicle_user_id;
								$datainsert["ritase_report_vehicle_id"] = $ritase_report_vehicle_id;
								$datainsert["ritase_report_vehicle_device"] = $ritase_report_vehicle_device;
								$datainsert["ritase_report_vehicle_no"] = $ritase_report_vehicle_no;
								$datainsert["ritase_report_vehicle_name"] = $ritase_report_vehicle_name;
								$datainsert["ritase_report_vehicle_type"] = $ritase_report_vehicle_type;
								$datainsert["ritase_report_vehicle_company"] = $ritase_report_vehicle_company;
								$datainsert["ritase_report_company_name"] = $ritase_report_company_name;
								
								$datainsert["ritase_report_type"] = $ritase_report_type;
								$datainsert["ritase_report_name"] = $ritase_report_name;
								
								//rom
								$datainsert["ritase_report_start_time"] = $ritase_report_start_time;
								$datainsert["ritase_report_start_location"] = $ritase_report_start_location;
								$datainsert["ritase_report_start_geofence"] = $ritase_report_start_location;
								$datainsert["ritase_report_start_coordinate"] = $ritase_report_start_coordinate;
								
								$datainsert["ritase_report_start_date"] = $ritase_report_start_date;
								$datainsert["ritase_report_start_hour"] = $ritase_report_start_hour;
								
								//port
								$datainsert["ritase_report_end_time"] = $ritase_report_port_start_time;
								$datainsert["ritase_report_end_location"] = $ritase_report_end_location;
								$datainsert["ritase_report_end_geofence"] = $ritase_report_end_geofence;
								$datainsert["ritase_report_end_coordinate"] = $ritase_report_end_coordinate;
								
								$datainsert["ritase_report_end_date"] = $ritase_report_end_date;
								$datainsert["ritase_report_end_hour"] = $ritase_report_end_hour;

								$datainsert["ritase_report_driver"] = $ritase_report_driver;
								$datainsert["ritase_report_driver_name"] = $ritase_report_driver_name;
								$datainsert["ritase_report_duration"] = $ritase_report_duration;
								$datainsert["ritase_report_duration_sec"] = $ritase_report_duration_sec;
								
								//new info
								$datainsert["ritase_report_port_start_time"] = $ritase_report_port_start_time;
								$datainsert["ritase_report_port_end_time"] = $ritase_report_port_end_time;
								$datainsert["ritase_report_port_duration_sec"] = $ritase_report_port_duration_sec;
								$datainsert["ritase_report_port_duration"] = $ritase_report_port_duration;
								$datainsert["ritase_report_wim_start_time"] = $ritase_report_wim_start_time;
								$datainsert["ritase_report_wim_end_time"] = $ritase_report_wim_start_time; //same
								$datainsert["ritase_report_wim2_start_time"] = $ritase_report_wim2_start_time;
								$datainsert["ritase_report_wim2_end_time"] = $ritase_report_wim2_start_time; //same
								$datainsert["ritase_report_rom_start_time"] = $ritase_report_rom_start_time;
								$datainsert["ritase_report_rom_end_time"] = $ritase_report_rom_end_time;
								$datainsert["ritase_report_rom_duration_sec"] = $ritase_report_rom_duration_sec;
								$datainsert["ritase_report_rom_duration"] = $ritase_report_rom_duration;
								$datainsert["ritase_report_shift_name"] = $ritase_report_shift_name;
								$datainsert["ritase_report_shift_date"] = $ritase_report_shift_date;
								
														
								//get last data
								$this->dbreport = $this->load->database("tensor_report",true); 
								$this->dbreport->select("ritase_report_id,ritase_report_shift_date,ritase_report_start_time,ritase_report_end_time,ritase_report_start_location,ritase_report_end_location,ritase_report_end_hour");
								$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
								$this->dbreport->where("ritase_report_start_time",$ritase_report_rom_end_time); //tes ganti endtime di port , issue EST beda port tidak tercover
								//$this->dbreport->where("ritase_report_end_time",$ritase_report_port_start_time); //berdasarkan jam di port yg sama (disable) abnormal durati muncul kalau yg dari port time
								$this->dbreport->where("ritase_report_shift_date",$ritase_report_shift_date); // tanggal shift yg sama
								$this->dbreport->where("ritase_report_end_hour",$ritase_report_end_hour); // jam shift yg sama
								//$this->dbreport->where("ritase_report_end_location",$ritase_report_end_location); //lokasi port yg sama  (disable) abnormal durati muncul kalau yg dari port time
								$this->dbreport->limit(1);
								$q_last = $this->dbreport->get($dbtable_ritase);
								$row_last = $q_last->row();
								$total_last = count($row_last);
															
								if($total_last>0){
									
									/* $this->dbreport = $this->load->database("tensor_report",true); 
									$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
									$this->dbreport->where("ritase_report_start_time",$ritase_report_rom_end_time);
									$this->dbreport->where("ritase_report_end_time",$ritase_report_port_start_time);
									//$this->dbreport->limit(1);
									$this->dbreport->update($dbtable_ritase,$datainsert); */
									//printf("===UPDATE OK \r\n ");
									printf("===ALREADY EXISTS \r\n ");
									//print_r($row_last);
								}
								else
								{
									//get last endtime di tanggal shift yg sama
									$this->dbreport = $this->load->database("tensor_report",true); 
									$this->dbreport->select("ritase_report_id,ritase_report_shift_date,ritase_report_start_time,ritase_report_end_time,ritase_report_start_location,ritase_report_end_location,ritase_report_end_hour");
									$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
									//$this->dbreport->where("ritase_report_shift_date",$ritase_report_shift_date);  //tidak valid jika cron jalan double
									$this->dbreport->where("ritase_report_end_time <",$ritase_report_port_start_time); 
									$this->dbreport->order_by("ritase_report_end_time","desc"); 
									$this->dbreport->limit(1); 
									$q_last2 = $this->dbreport->get($dbtable_ritase);
									$row_last2 = $q_last2->row();
									$total_last2 = count($row_last2);
									print_r($row_last2);
									
									if($total_last2 > 0)
									{
										//untuk case beda jam masih di port yg sama
										$limit_ritase_endtime = 30*60; // default 30 menit;
										$last_ritase_endtime = strtotime($row_last2->ritase_report_end_time);
										$now_ritase_endtime = strtotime($ritase_report_port_start_time);
										
										$delta_ritase_endtime = $now_ritase_endtime - $last_ritase_endtime;
										
										if( ($delta_ritase_endtime > 0 ) && ($delta_ritase_endtime < $limit_ritase_endtime) )
										{
											printf("X==SUDAH ADA DI 30 MENIT SEBELUMNYA, DELTA : %s s \r\n ", $delta_ritase_endtime);
										}
										else
										{
											$this->dbreport->insert($dbtable_ritase,$datainsert);
											printf("===INSERT OK, NEW DATA IN SHIFT HOUR \r\n");
										}
									}
									else
									{
										$this->dbreport->insert($dbtable_ritase,$datainsert);
										printf("===INSERT OK, NEW DATA IN DATE PERIODE \r\n");
									}
														
									
								}
								
									/* printf("PREPARE INSERT TO TBL AUTOREPORT \r\n");
									$data_insert["autoreport_vehicle_id"] = $vehicle_id;
									$data_insert["autoreport_vehicle_device"] = $vehicle_device;
									$data_insert["autoreport_vehicle_no"] = $vehicle_no;
									$data_insert["autoreport_user_id"] = $userid;
									$data_insert["autoreport_company"] = $vehicle_company;
									$data_insert["autoreport_data_startdate"] = $sdate;
									$data_insert["autoreport_data_enddate"] = $edate;
									$data_insert["autoreport_type"] = $report_type_config;
									$data_insert["autoreport_process_date"] = $process_date;
									$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
									$this->dbreport->insert("autoreport_ritase",$data_insert);

									printf("INSERT CONFIG OK \r\n"); */
									printf("============================================ \r\n");
								
									$this->dbreport->close();
									$this->dbreport->cache_delete_all();

							}

							printf("============================================ \r\n");

						}
						else
						{
							printf("NO DATA RITASE \r\n");
						}
				
				
			}
			
			
		}
		$finishtime = date("Y-m-d H:i:s");
		
			//total data ritase
			$this->dbreport = $this->load->database("tensor_report",true); 
			$this->dbreport->select("ritase_report_id");
			$this->dbreport->where("ritase_report_start_date >=",$startdate); 
			$this->dbreport->where("ritase_report_end_date <=",$startdate);
			$q_total = $this->dbreport->get($dbtable_ritase);
			$row_total = $q_total->result();
			$totaldata = count($row_total);
			
			$finishtime = date('Y-m-d H:i:s');
			printf("===FINISH RITASE PER HOUR %s to %s \r\n", $nowtime, $finishtime);
			printf("============================== \r\n");
			
			//send telegram
			$title_name = "RITASE HOUR SNAPSHOT";
			$message = urlencode(
						"".$title_name." \n".
						"Periode: ".date("d F Y", strtotime($startdate))." \n".
						"Total Data: ".$totaldata." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);		
			$sendtelegram = $this->telegram_direct("-888562991",$message); // FMS RITASE SNAPSHOT
			
			
			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbts->close();
			$this->dbts->cache_delete_all();
			$this->dbreport->close();
			$this->dbreport->cache_delete_all();
			printf("===SENT TELEGRAM OK\r\n");
		
	}
	
	// New rule KM 13 TIA & kondisi awal bulan & status API LOC
	function generate($userid="", $order="asc", $date="", $shift="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		printf("===STARTING RITASE PER HOUR %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		
		if($date == "now")
		{
			$startdate = date("Y-m-d");
			$enddate = date("Y-m-d");
		}
		else if($date == "yesterday")
		{
			$startdate = date("Y-m-d", strtotime("yesterday"));
			$enddate = date("Y-m-d", strtotime("yesterday"));
		}
		else if($date == "2dayago")
		{
			
			$startdate = date('Y-m-d',strtotime('-2 days',strtotime($nowdate)));
			$enddate = date('Y-m-d',strtotime('-2 days',strtotime($nowdate)));
			
		}
		else
		{
			$startdate = date("Y-m-d", strtotime($date));
			$enddate = date("Y-m-d", strtotime($date));
		}
		
		$report = "location_";
		$report_ritase = "ritase_full_";
		
		$days = date("d", strtotime($startdate));
		$month = date("F", strtotime($startdate));
		$year = date("Y", strtotime($startdate));
		$before_status = 0;
		
		$year_before = date('Y',strtotime('-1 year',strtotime($year)));
		
		/* print_r($startdate." ".$enddate." ");
		print_r($days);
		exit(); */
		
		//jika first date maka ambil data dari table before
		if($days == "01"){
			$before_status = 1;
		}
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			$dbtable_before = $report."desember_".$year_before;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			$dbtable_before = $report."januari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			$dbtable_before = $report."februari_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			$dbtable_before = $report."maret_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			$dbtable_before = $report."april_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			$dbtable_before = $report."mei_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			$dbtable_before = $report."juni_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			$dbtable_before = $report."juli_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			$dbtable_before = $report."agustus_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			$dbtable_before = $report."september_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			$dbtable_before = $report."oktober_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			$dbtable_before = $report."november_".$year;
			break;
		}
		
		//Get Loc report Status
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($startdate." "."23:59:59"));
		
	
		//$loc_result = $this->getStatusLocReport("4408","all",$sdate,$edate);
		//by pass dahulu versi POC
		$loc_result = "DONE";
		
		if($loc_result == "ON PROCESS"){
			
			$finishtime = date('Y-m-d H:i:s');
			printf("===FINISH LOC REPORT NOT READY %s to %s \r\n", $nowtime, $finishtime);
			printf("============================== \r\n");
			//send telegram
			$title_name = "RITASE BY LOC REPORT";
				$message = urlencode(
						"".$title_name." \n".
						"Periode: ".date("d F Y", strtotime($startdate))." \n".
						"Shift: ".$shift." \n".
						"Status: "."LOC REPORT NOT READY"." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);		
			$sendtelegram = $this->telegram_direct("-993884493",$message); // POC LOC REPORT
			printf("===SENT TELEGRAM OK\r\n");
			
		}
		else
		{
			$datavehicle = $this->getVehicle($userid,$order);
			$totalvehicle = count($datavehicle);
			
			//simulasi check point
			$config_portbib = array("BMO 1 CPP KM 1","ROM K1-2"); 
			$config_rombib = array("ROM K1-2","ROM P3","ROM 1");
			$config_beaching = array("Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1");
			
			//only test 
			$beaching_point_B = array("Beaching Point B1","Beaching Point B2","Beaching Point B3");
			$beaching_point_P = array("Beaching Point P2","Beaching Point P3","Beaching Point P1");
			$beaching_point_river = array("KELAY RIVER");
			
			for ($z=0;$z<$totalvehicle;$z++)
			{
				$norut = $z+1;
				printf("===PROCESS VEHICLE %s %s of %s \r\n", $datavehicle[$z]->vehicle_no, $norut, $totalvehicle);
					
				$company_name = $this->getCompanyName($datavehicle[$z]->vehicle_company);
				
				$this->dbts = $this->load->database("webtracking_ts", TRUE);
				$this->dbts->order_by("hour_name","asc");
				$this->dbts->where("hour_flag",0);
				if($shift != "all"){
					$this->dbts->where("hour_shift", $shift);
				}
				$qhour = $this->dbts->get("ts_hour_shift");
				$rowshour = $qhour->result();
				$totalhour = count($rowshour);
				
				
				for ($i=0;$i<$totalhour;$i++)
				{	
					$vehicle_id = $datavehicle[$z]->vehicle_id;
					$vehicle_no = $datavehicle[$z]->vehicle_no;
					$vehicle_name = $datavehicle[$z]->vehicle_name;
					$vehicle_type = $datavehicle[$z]->vehicle_type;
					$vehicle_device = $datavehicle[$z]->vehicle_device;
					$vehicle_company = $datavehicle[$z]->vehicle_company;
					$date_diff = "-".$rowshour[$i]->hour_diff;
					$report_type_config = $rowshour[$i]->hour_name;
					$process_date = date("Y-m-d H:i:s");
					$sdate = date("Y-m-d H:i:s",strtotime($startdate." ".$rowshour[$i]->hour_time));
					$edate = date("Y-m-d H:i:s",strtotime($startdate." ".$rowshour[$i]->hour_name.":59:59"));
					
					printf("===CHECKING HOUR %s DATE %s : %s %s of %s \r\n", $rowshour[$i]->hour_name, $date, $vehicle_no, $norut, $totalvehicle);
							
					//cek apakah sudah pernah ada filenya
					/* $this->dbreport = $this->load->database("tensor_report",true); 
					$this->dbreport->select("autoreport_vehicle_id");
					$this->dbreport->where("autoreport_user_id",$userid);
					$this->dbreport->where("autoreport_vehicle_id",$vehicle_id);
					$this->dbreport->where("autoreport_data_startdate",$sdate);
					$this->dbreport->limit(1);
					$qrpt = $this->dbreport->get("autoreport_ritase");
					$rrpt = $qrpt->row();

					if (count($rrpt) > 0)
					{
						printf("===VEHICLE %s DATE %s: SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $vehicle_no, $sdate);
						printf("------------------------------------------------------------- \r\n");
					}
					else
					{ */
					
						printf("===STARTDATE %s, ENDDATE %s \r\n", $sdate, $edate);
						
						//get data in PORT
						$data_inport = $this->getLocationReport_inPORT($userid,$vehicle_id,$sdate,$edate,$dbtable,$config_portbib);
						$data_inport_ex = explode("|", $data_inport);
						$data_inport_status = $data_inport_ex[0];
						
						if($data_inport_status == 1)
						{
							$ritase_report_vehicle_user_id = $userid;
							$ritase_report_vehicle_id = $vehicle_id;
							$ritase_report_vehicle_device = $vehicle_device; 
							$ritase_report_vehicle_no = $vehicle_no;
							$ritase_report_vehicle_name = $vehicle_name;
							$ritase_report_vehicle_type = $vehicle_type;
							$ritase_report_vehicle_company = $vehicle_company;
							$ritase_report_type = 0;
							$ritase_report_name = "ritase";
							
							$ritase_report_driver = 0;
							$ritase_report_driver_name = "";
							
							$ritase_report_port_start_time = $data_inport_ex[1];
							$ritase_report_port_end_time = $data_inport_ex[2];
				
							$ritase_report_end_location = $data_inport_ex[3];
							$ritase_report_end_geofence = $ritase_report_end_location;
							$ritase_report_end_coordinate = $data_inport_ex[4];
							 
							$ritase_report_end_date = date("Y-m-d", strtotime($ritase_report_port_start_time));
							$ritase_report_end_hour = date("H", strtotime($ritase_report_port_end_time)).":00:00";
							
							$port_time_diff = $this->getDuration_show($ritase_report_port_start_time,$ritase_report_port_end_time);
							$port_time_diff_ex =  explode("|", $port_time_diff);
							
							$ritase_report_port_duration = $port_time_diff_ex[0];
							$ritase_report_port_duration_sec = $port_time_diff_ex[1];
							
							if($rowshour[$i]->hour_shift == 2){
								$shift_name = "NS";
							}else{
								$shift_name = "DS";
							}
							
							$ritase_report_shift_name = $shift_name;
							$ritase_report_shift_date = date("Y-m-d H:i:s", strtotime($ritase_report_end_date .$date_diff." "."days"));
							$ritase_report_company_name = $company_name;
							
							
							// data in ROM
							$data_inrom = $this->getLocationReport_lastROM_new($userid,$vehicle_id,$ritase_report_port_start_time,$dbtable,$config_rombib,$dbtable_before);
							$data_inrom_ex = explode("|", $data_inrom);
							$data_inrom_status = $data_inrom_ex[0];
							
							if($data_inrom_status == 1){
								$ritase_report_rom_start_time = $data_inrom_ex[1];
								$ritase_report_rom_end_time = $data_inrom_ex[2];
					
								$ritase_report_start_location = $data_inrom_ex[3];
								$ritase_report_start_geofence = $ritase_report_start_location;
								$ritase_report_start_coordinate = $data_inrom_ex[4];
								 
								$ritase_report_start_date = date("Y-m-d", strtotime($ritase_report_rom_end_time));
								$ritase_report_start_hour = date("H", strtotime($ritase_report_rom_end_time)).":00:00";
								
								$rom_time_diff = $this->getDuration_show($ritase_report_rom_start_time,$ritase_report_rom_end_time);
								$rom_time_diff_ex =  explode("|", $rom_time_diff);
								
								$ritase_report_rom_duration = $rom_time_diff_ex[0];
								$ritase_report_rom_duration_sec = $rom_time_diff_ex[1];
								
								//periode rom to port
								$periode_time_diff = $this->getDuration_show($ritase_report_rom_end_time,$ritase_report_port_start_time);
								$periode_time_diff_ex =  explode("|", $periode_time_diff);
								
								$ritase_report_duration = $periode_time_diff_ex[0];
								$ritase_report_duration_sec = $periode_time_diff_ex[1];
														
							}
							else
							{
								$ritase_report_rom_start_time = "";
								$ritase_report_rom_end_time = "";
					
								$ritase_report_start_location = "";
								$ritase_report_start_geofence = "";
								$ritase_report_start_coordinate = 0;
								 
								$ritase_report_start_date = "";
								$ritase_report_start_hour = "";
								
								
								$ritase_report_rom_duration = "";
								$ritase_report_rom_duration_sec = 0;
								
								$ritase_report_duration = "";
								$ritase_report_duration_sec = 0;
							}
							
							
							// data in WIM -> digantikan dengan beaching Point
							/* $ritase_report_wim_start_time = "";
							$ritase_report_wim2_start_time = "";
							
							if($data_inrom_status == 1)
							{
								
								$data_inwim = $this->getLocationReport_lastWIM($userid,$vehicle_id,$ritase_report_rom_end_time,$ritase_report_port_start_time,$dbtable,"KM 13.5");
								$data_inwim_ex = explode("|", $data_inwim);
								$data_inwim_status = $data_inwim_ex[0];
								
								if($data_inwim_status == 1){
									$ritase_report_wim_start_time = $data_inwim_ex[1];
									
								}
								
								$data_inwim2 = $this->getLocationReport_lastWIM($userid,$vehicle_id,$ritase_report_rom_end_time,$ritase_report_port_start_time,$dbtable,"TIA KM 13.5");
								$data_inwim2_ex = explode("|", $data_inwim2);
								$data_inwim2_status = $data_inwim2_ex[0];
								
								if($data_inwim2_status == 1){
									$ritase_report_wim2_start_time = $data_inwim2_ex[1];
									
								}
							} */
							
							// data in BEACHING POINT -> diambil setelah dari asal ROM 
							$data_inbeach = $this->getLocationReport_lastBeaching($userid,$vehicle_id,$ritase_report_rom_start_time,$dbtable,$config_beaching,$dbtable_before);
							$data_inbeach_ex = explode("|", $data_inbeach);
							$data_inbeach_status = $data_inbeach_ex[0];
							
							if($data_inbeach_status == 1){
																
								$ritase_report_beach_start_time = $data_inbeach_ex[1];
								$ritase_report_beach_end_time = $data_inbeach_ex[2];
					
								$ritase_report_beach_start_location = $data_inbeach_ex[3];
								$ritase_report_beach_start_coord = $data_inbeach_ex[4];
								$ritase_report_beach_end_coord = $data_inbeach_ex[5];
								$ritase_report_beach_end_location = $data_inbeach_ex[6];
								 
								$beach_time_diff = $this->getDuration_show($ritase_report_beach_start_time,$ritase_report_beach_end_time);
								$beach_time_diff_ex =  explode("|", $beach_time_diff);
								
								$ritase_report_beach_duration = $beach_time_diff_ex[0];
								$ritase_report_beach_duration_sec = $beach_time_diff_ex[1];
								
								//periode Beaching point start s/d beaching point end
								$periode_time_diff = $this->getDuration_show($ritase_report_beach_start_time,$ritase_report_beach_end_time);
								$periode_time_diff_ex =  explode("|", $periode_time_diff);
								
								$ritase_report_beach_duration = $periode_time_diff_ex[0];
								$ritase_reprot_beach_duration_sec = $periode_time_diff_ex[1];
								
							
														
							}
							else
							{
								$ritase_report_beach_start_time = "";
								$ritase_report_beach_end_time = "";
					
								$ritase_report_beach_start_location = "";
								$ritase_report_beach_end_location = "";
								$ritase_report_beach_start_coord = 0;
								$ritase_report_beach_end_coord = 0;
								 
								$ritase_report_beach_duration = "";
								$ritase_report_beach_duration_sec = 0;
								
							
							}
							
							
							//INSERT TO RITASE FULL
							
							$datainsert["ritase_report_vehicle_user_id"] = $ritase_report_vehicle_user_id;
							$datainsert["ritase_report_vehicle_id"] = $ritase_report_vehicle_id;
							$datainsert["ritase_report_vehicle_device"] = $ritase_report_vehicle_device;
							$datainsert["ritase_report_vehicle_no"] = $ritase_report_vehicle_no;
							$datainsert["ritase_report_vehicle_name"] = $ritase_report_vehicle_name;
							$datainsert["ritase_report_vehicle_type"] = $ritase_report_vehicle_type;
							$datainsert["ritase_report_vehicle_company"] = $ritase_report_vehicle_company;
							$datainsert["ritase_report_company_name"] = $ritase_report_company_name;
							
							$datainsert["ritase_report_type"] = $ritase_report_type;
							$datainsert["ritase_report_name"] = $ritase_report_name;
							
							//rom
							$datainsert["ritase_report_start_time"] = $ritase_report_rom_end_time;
							$datainsert["ritase_report_start_location"] = $ritase_report_start_location;
							$datainsert["ritase_report_start_geofence"] = $ritase_report_start_geofence;
							$datainsert["ritase_report_start_coordinate"] = $ritase_report_start_coordinate;
							
							$datainsert["ritase_report_start_date"] = $ritase_report_start_date;
							$datainsert["ritase_report_start_hour"] = $ritase_report_start_hour;
							
							//port
							$datainsert["ritase_report_end_time"] = $ritase_report_port_start_time;
							$datainsert["ritase_report_end_location"] = $ritase_report_end_location;
							$datainsert["ritase_report_end_geofence"] = $ritase_report_end_geofence;
							$datainsert["ritase_report_end_coordinate"] = $ritase_report_end_coordinate;
							
							$datainsert["ritase_report_end_date"] = $ritase_report_end_date;
							$datainsert["ritase_report_end_hour"] = $ritase_report_end_hour;

							$datainsert["ritase_report_driver"] = $ritase_report_driver;
							$datainsert["ritase_report_driver_name"] = $ritase_report_driver_name;
							$datainsert["ritase_report_duration"] = $ritase_report_duration;
							$datainsert["ritase_report_duration_sec"] = $ritase_report_duration_sec;
							
							//beach
							$datainsert["ritase_report_beach_start_location"] = $ritase_report_beach_start_location;
							$datainsert["ritase_report_beach_start_time"] = $ritase_report_beach_start_time;
							$datainsert["ritase_report_beach_start_coord"] = $ritase_report_beach_start_coord;
							$datainsert["ritase_report_beach_end_location"] = $ritase_report_beach_end_location;
							$datainsert["ritase_report_beach_end_time"] = $ritase_report_beach_end_time;
							$datainsert["ritase_report_beach_end_coord"] = $ritase_report_beach_end_coord;
							$datainsert["ritase_report_beach_duration"] = $ritase_report_beach_duration;
							$datainsert["ritase_report_beach_duration_sec"] = $ritase_report_beach_duration_sec;
							
							//new info
							$datainsert["ritase_report_port_start_time"] = $ritase_report_port_start_time;
							$datainsert["ritase_report_port_end_time"] = $ritase_report_port_end_time;
							$datainsert["ritase_report_port_duration_sec"] = $ritase_report_port_duration_sec;
							$datainsert["ritase_report_port_duration"] = $ritase_report_port_duration;
							/* $datainsert["ritase_report_wim_start_time"] = $ritase_report_wim_start_time;
							$datainsert["ritase_report_wim_end_time"] = $ritase_report_wim_start_time; //same
							$datainsert["ritase_report_wim2_start_time"] = $ritase_report_wim2_start_time;
							$datainsert["ritase_report_wim2_end_time"] = $ritase_report_wim2_start_time; //same */
							$datainsert["ritase_report_rom_start_time"] = $ritase_report_rom_start_time;
							$datainsert["ritase_report_rom_end_time"] = $ritase_report_rom_end_time;
							$datainsert["ritase_report_rom_duration_sec"] = $ritase_report_rom_duration_sec;
							$datainsert["ritase_report_rom_duration"] = $ritase_report_rom_duration;
							$datainsert["ritase_report_shift_name"] = $ritase_report_shift_name;
							$datainsert["ritase_report_shift_date"] = $ritase_report_shift_date;
							
													
							//get last data
							$this->dbreport = $this->load->database("tensor_report",true); 
							$this->dbreport->select("ritase_report_id,ritase_report_shift_date,ritase_report_start_time,ritase_report_end_time,ritase_report_start_location,ritase_report_end_location,ritase_report_end_hour");
							$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
							$this->dbreport->where("ritase_report_start_time",$ritase_report_rom_end_time); //tes ganti endtime di port , issue EST beda port tidak tercover
							//$this->dbreport->where("ritase_report_end_time",$ritase_report_port_start_time); //berdasarkan jam di port yg sama (disable) abnormal durati muncul kalau yg dari port time
							$this->dbreport->where("ritase_report_shift_date",$ritase_report_shift_date); // tanggal shift yg sama
							$this->dbreport->where("ritase_report_end_hour",$ritase_report_end_hour); // jam shift yg sama
							//$this->dbreport->where("ritase_report_end_location",$ritase_report_end_location); //lokasi port yg sama  (disable) abnormal durati muncul kalau yg dari port time
							$this->dbreport->limit(1);
							$q_last = $this->dbreport->get($dbtable_ritase);
							$row_last = $q_last->row();
							$total_last = count($row_last);
														
							if($total_last>0){
								
								/* $this->dbreport = $this->load->database("tensor_report",true); 
								$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
								$this->dbreport->where("ritase_report_start_time",$ritase_report_rom_end_time);
								$this->dbreport->where("ritase_report_end_time",$ritase_report_port_start_time);
								//$this->dbreport->limit(1);
								$this->dbreport->update($dbtable_ritase,$datainsert); */
								//printf("===UPDATE OK \r\n ");
								printf("===ALREADY EXISTS \r\n ");
								//print_r($row_last);
							}
							else
							{
								//get last endtime di tanggal shift yg sama
								$this->dbreport = $this->load->database("tensor_report",true); 
								$this->dbreport->select("ritase_report_id,ritase_report_shift_date,ritase_report_start_time,ritase_report_end_time,ritase_report_start_location,ritase_report_end_location,ritase_report_end_hour");
								$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
								//$this->dbreport->where("ritase_report_shift_date",$ritase_report_shift_date);  //tidak valid jika cron jalan double
								$this->dbreport->where("ritase_report_end_time <",$ritase_report_port_start_time); 
								$this->dbreport->order_by("ritase_report_end_time","desc"); 
								$this->dbreport->limit(1); 
								$q_last2 = $this->dbreport->get($dbtable_ritase);
								$row_last2 = $q_last2->row();
								$total_last2 = count($row_last2);
								print_r($row_last2);
								
								if($total_last2 > 0)
								{
									//untuk case beda jam masih di port yg sama
									$limit_ritase_endtime = 30*60; // default 30 menit;
									$last_ritase_endtime = strtotime($row_last2->ritase_report_end_time);
									$now_ritase_endtime = strtotime($ritase_report_port_start_time);
									
									$delta_ritase_endtime = $now_ritase_endtime - $last_ritase_endtime;
									
									if( ($delta_ritase_endtime > 0 ) && ($delta_ritase_endtime < $limit_ritase_endtime) )
									{
										printf("X==SUDAH ADA DI 30 MENIT SEBELUMNYA, DELTA : %s s \r\n ", $delta_ritase_endtime);
									}
									else
									{
										$this->dbreport->insert($dbtable_ritase,$datainsert);
										printf("===INSERT OK, NEW DATA IN SHIFT HOUR \r\n");
									}
								}
								else
								{
									$this->dbreport->insert($dbtable_ritase,$datainsert);
									printf("===INSERT OK, NEW DATA IN DATE PERIODE \r\n");
								}
													
								
							}
							
								/* printf("PREPARE INSERT TO TBL AUTOREPORT \r\n");
								$data_insert["autoreport_vehicle_id"] = $vehicle_id;
								$data_insert["autoreport_vehicle_device"] = $vehicle_device;
								$data_insert["autoreport_vehicle_no"] = $vehicle_no;
								$data_insert["autoreport_user_id"] = $userid;
								$data_insert["autoreport_company"] = $vehicle_company;
								$data_insert["autoreport_data_startdate"] = $sdate;
								$data_insert["autoreport_data_enddate"] = $edate;
								$data_insert["autoreport_type"] = $report_type_config;
								$data_insert["autoreport_process_date"] = $process_date;
								$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
								$this->dbreport->insert("autoreport_ritase",$data_insert);

								printf("INSERT CONFIG OK \r\n"); */
								printf("============================================ \r\n");
							
								$this->dbreport->close();
								$this->dbreport->cache_delete_all();
						}
						else
						{
							
							printf("===SKIP! NO DATA IN PORT \r\n");
						}
					
					
						printf("-----------END HOUR--------- \r\n ");
					
					//}
					
				}
				
				printf("============================== \r\n");
			
			}
			
			//total data ritase
			$this->dbreport = $this->load->database("tensor_report",true); 
			$this->dbreport->select("ritase_report_id");
			$this->dbreport->where("ritase_report_start_date >=",$startdate); 
			$this->dbreport->where("ritase_report_end_date <=",$startdate);
			$q_total = $this->dbreport->get($dbtable_ritase);
			$row_total = $q_total->result();
			$totaldata = count($row_total);
			
			$finishtime = date('Y-m-d H:i:s');
			printf("===FINISH RITASE PER HOUR %s to %s \r\n", $nowtime, $finishtime);
			printf("============================== \r\n");
			
			//send telegram
			$title_name = "RITASE BY LOC REPORT";
				$message = urlencode(
						"".$title_name." \n".
						"Periode: ".date("d F Y", strtotime($startdate))." \n".
						"Shift: ".$shift." \n".
						"Total Data: ".$totaldata." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);		
			$sendtelegram = $this->telegram_direct("-993884493",$message); // POC LOC REPORT
			printf("===SENT TELEGRAM OK\r\n");
			
			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbts->close();
			$this->dbts->cache_delete_all();
			$this->dbreport->close();
			$this->dbreport->cache_delete_all();
		}
		
		
		
	}
	
	// New rule KM 13 TIA & kondisi awal bulan & status API LOC (source data DB PORT)
	function generate_dbport($userid="", $order="asc", $date="now", $hour_option="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		printf("===STARTING RITASE PER HOUR %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		
		if($date == "now")
		{
			$startdate = date("Y-m-d");
			$enddate = date("Y-m-d");
		}
		else if($date == "yesterday")
		{
			$startdate = date("Y-m-d", strtotime("yesterday"));
			$enddate = date("Y-m-d", strtotime("yesterday"));
		}
		else if($date == "2dayago")
		{
			
			$startdate = date('Y-m-d',strtotime('-2 days',strtotime($nowdate)));
			$enddate = date('Y-m-d',strtotime('-2 days',strtotime($nowdate)));
			
		}
		else
		{
			$startdate = date("Y-m-d", strtotime($date));
			$enddate = date("Y-m-d", strtotime($date));
		}
		
		
		$report = "location_";
		$report_ritase = "ritase_trial_";
		
		$days = date("d", strtotime($startdate));
		$month = date("F", strtotime($startdate));
		$year = date("Y", strtotime($startdate));
		$before_status = 0;
		
		$year_before = date('Y',strtotime('-1 year',strtotime($year)));
		
		//jika first date maka ambil data dari table before
		if($days == "01"){
			$before_status = 1;
		}
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			$dbtable_before = $report."desember_".$year_before;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			$dbtable_before = $report."januari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			$dbtable_before = $report."februari_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			$dbtable_before = $report."maret_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			$dbtable_before = $report."april_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			$dbtable_before = $report."mei_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			$dbtable_before = $report."juni_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			$dbtable_before = $report."juli_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			$dbtable_before = $report."agustus_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			$dbtable_before = $report."september_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			$dbtable_before = $report."oktober_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			$dbtable_before = $report."november_".$year;
			break;
		}
		
		//Get Loc report Status
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($startdate." "."23:59:59"));
		
		if($hour_option == "")
		{
			$now_hour_txt = date("H:i:s"); //WIB
			$last_hour = date('H:i:s',strtotime('+1 hour',strtotime($now_hour_txt))); //default -0
			$hour_name_txt = date('H', strtotime($last_hour));
			$hour_time = date("H:i:s", strtotime($hour_name_txt.":"."00".":"."00"));
			
		}
		else
		{
			$hour_time = date("H:i:s",strtotime($hour_option));
			
		}
		
		$nowtime_wita_for_report = date("Y-m-d H:i:s", strtotime($startdate." ".$hour_time));
		
		printf("===PROCEESING DATA %s HOUR TIME %s WITA \r\n", $nowtime_wita_for_report, $hour_time); 
		
		//$loc_result = $this->getStatusLocReport("4408","all",$sdate,$edate);
		$loc_result = "OK"; //bypass
		
		if($loc_result == "ON PROCESS")
		{
			
			$finishtime = date('Y-m-d H:i:s');
			printf("===FINISH LOC REPORT NOT READY %s to %s \r\n", $nowtime, $finishtime);
			printf("============================== \r\n");
			//send telegram
			$title_name = "RITASE REPORT HOURLY";
				$message = urlencode(
						"".$title_name." \n".
						"Periode: ".date("d F Y", strtotime($startdate))." \n".
						"Shift: ".$shift." \n".
						"Status: "."LOC REPORT NOT READY"." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);		
			$sendtelegram = $this->telegram_direct("-866565294",$message); // FMS RITASE DB PORT
			printf("===SENT TELEGRAM OK\r\n");
			
		}
		else
		{
			//$datavehicle = $this->getVehicle_test($userid,$order); //ubah master data process disini
			$datavehicle = $this->getVehicle_all($userid,$order); //all vehicle in temanindobara
			$totalvehicle = count($datavehicle);
			$config_portbib = $this->config->item('port_register_autocheck_comma');
			$config_rombib = $this->config->item('rombib_register_autocheck_comma');
			
			for ($z=0;$z<$totalvehicle;$z++)
			{
				$norut = $z+1;
				printf("===PROCESS VEHICLE %s %s of %s \r\n", $datavehicle[$z]->vehicle_no, $norut, $totalvehicle);
					
				$company_name = $this->getCompanyName($datavehicle[$z]->vehicle_company);
				
				$this->dbts = $this->load->database("webtracking_ts", TRUE);
				$this->dbts->order_by("hour_name","asc");
				$this->dbts->where("hour_flag",0);
				$this->dbts->where("hour_time", $hour_time);
				$qhour = $this->dbts->get("ts_hour_shift");
				$rowshour = $qhour->result();
				$totalhour = count($rowshour);
			
				
				for ($i=0;$i<$totalhour;$i++)
				{	
					$vehicle_id = $datavehicle[$z]->vehicle_id;
					$vehicle_no = $datavehicle[$z]->vehicle_no;
					$vehicle_name = $datavehicle[$z]->vehicle_name;
					$vehicle_type = $datavehicle[$z]->vehicle_type;
					$vehicle_device = $datavehicle[$z]->vehicle_device;
					$vehicle_company = $datavehicle[$z]->vehicle_company;
					$date_diff = "-".$rowshour[$i]->hour_diff;
					$report_type_config = $rowshour[$i]->hour_name;
					$process_date = date("Y-m-d H:i:s");
					$sdate = date("Y-m-d H:i:s",strtotime($startdate." ".$rowshour[$i]->hour_time));
					$edate = date("Y-m-d H:i:s",strtotime($startdate." ".$rowshour[$i]->hour_name.":59:59"));
					
					printf("===CHECKING HOUR %s DATE %s : %s %s of %s \r\n", $rowshour[$i]->hour_name, $date, $vehicle_no, $norut, $totalvehicle);
							

						printf("===STARTDATE %s, ENDDATE %s \r\n", $sdate, $edate);
						
						//get data in PORT
						$data_inport = $this->getLocation_inPORT_fromGPSPORT($userid,$datavehicle[$z],$sdate,$edate,$config_portbib);
						$data_inport_ex = explode("|", $data_inport);
						$data_inport_status = $data_inport_ex[0];
						
						if($data_inport_status == 1)
						{
							$ritase_report_vehicle_user_id = $userid;
							$ritase_report_vehicle_id = $vehicle_id;
							$ritase_report_vehicle_device = $vehicle_device; 
							$ritase_report_vehicle_no = $vehicle_no;
							$ritase_report_vehicle_name = $vehicle_name;
							$ritase_report_vehicle_type = $vehicle_type;
							$ritase_report_vehicle_company = $vehicle_company;
							$ritase_report_type = 0;
							$ritase_report_name = "ritase";
							
							$ritase_report_driver = 0;
							$ritase_report_driver_name = "";
							
							$ritase_report_port_start_time = $data_inport_ex[1];
							$ritase_report_port_end_time = $data_inport_ex[2];
				
							$ritase_report_end_location = $data_inport_ex[3];
							$ritase_report_end_geofence = $ritase_report_end_location;
							$ritase_report_end_coordinate = $data_inport_ex[4];
							 
							$ritase_report_end_date = date("Y-m-d", strtotime($ritase_report_port_start_time));
							$ritase_report_end_hour = date("H", strtotime($ritase_report_port_end_time)).":00:00";
							
							$port_time_diff = $this->getDuration_show($ritase_report_port_start_time,$ritase_report_port_end_time);
							$port_time_diff_ex =  explode("|", $port_time_diff);
							
							$ritase_report_port_duration = $port_time_diff_ex[0];
							$ritase_report_port_duration_sec = $port_time_diff_ex[1];
							
							if($rowshour[$i]->hour_shift == 2){
								$shift_name = "NS";
							}else{
								$shift_name = "DS";
							}
							
							$ritase_report_shift_name = $shift_name;
							$ritase_report_shift_date = date("Y-m-d H:i:s", strtotime($ritase_report_end_date .$date_diff." "."days"));
							$ritase_report_company_name = $company_name;
							
							
							// data in ROM
							$data_inrom = $this->getLocation_lastROM_new_fromGPSPORT($userid,$datavehicle[$z],$ritase_report_port_start_time,$config_rombib);
							$data_inrom_ex = explode("|", $data_inrom);
							$data_inrom_status = $data_inrom_ex[0];
							
							if($data_inrom_status == 1){
								$ritase_report_rom_start_time = $data_inrom_ex[1];
								$ritase_report_rom_end_time = $data_inrom_ex[2];
					
								$ritase_report_start_location = $data_inrom_ex[3];
								$ritase_report_start_geofence = $ritase_report_start_location;
								$ritase_report_start_coordinate = $data_inrom_ex[4];
								 
								$ritase_report_start_date = date("Y-m-d", strtotime($ritase_report_rom_end_time));
								$ritase_report_start_hour = date("H", strtotime($ritase_report_rom_end_time)).":00:00";
								
								$rom_time_diff = $this->getDuration_show($ritase_report_rom_start_time,$ritase_report_rom_end_time);
								$rom_time_diff_ex =  explode("|", $rom_time_diff);
								
								$ritase_report_rom_duration = $rom_time_diff_ex[0];
								$ritase_report_rom_duration_sec = $rom_time_diff_ex[1];
								
								//periode rom to port
								$periode_time_diff = $this->getDuration_show($ritase_report_rom_end_time,$ritase_report_port_start_time);
								$periode_time_diff_ex =  explode("|", $periode_time_diff);
								
								$ritase_report_duration = $periode_time_diff_ex[0];
								$ritase_report_duration_sec = $periode_time_diff_ex[1];
														
							}
							else
							{
								$ritase_report_rom_start_time = "";
								$ritase_report_rom_end_time = "";
					
								$ritase_report_start_location = "";
								$ritase_report_start_geofence = "";
								$ritase_report_start_coordinate = 0;
								 
								$ritase_report_start_date = "";
								$ritase_report_start_hour = "";
								
								
								$ritase_report_rom_duration = "";
								$ritase_report_rom_duration_sec = 0;
								
								$ritase_report_duration = "";
								$ritase_report_duration_sec = 0;
							}
							
							
							// data in WIM
							$ritase_report_wim_start_time = "";
							$ritase_report_wim2_start_time = "";
							
							if($data_inrom_status == 1)
							{
								
								$data_inwim = $this->getLocation_lastWIM_fromGPSPORT($userid,$datavehicle[$z],$ritase_report_rom_end_time,$ritase_report_port_start_time,"KM 13.5,");
								$data_inwim_ex = explode("|", $data_inwim);
								$data_inwim_status = $data_inwim_ex[0];
								
								if($data_inwim_status == 1){
									$ritase_report_wim_start_time = $data_inwim_ex[1];
									
								}
								
								$data_inwim2 = $this->getLocation_lastWIM_fromGPSPORT($userid,$datavehicle[$z],$ritase_report_rom_end_time,$ritase_report_port_start_time,"TIA KM 13.5,");
								$data_inwim2_ex = explode("|", $data_inwim2);
								$data_inwim2_status = $data_inwim2_ex[0];
								
								if($data_inwim2_status == 1){
									$ritase_report_wim2_start_time = $data_inwim2_ex[1];
									
								}
							}
							
							
							
							//INSERT TO RITASE FULL
							
							$datainsert["ritase_report_vehicle_user_id"] = $ritase_report_vehicle_user_id;
							$datainsert["ritase_report_vehicle_id"] = $ritase_report_vehicle_id;
							$datainsert["ritase_report_vehicle_device"] = $ritase_report_vehicle_device;
							$datainsert["ritase_report_vehicle_no"] = $ritase_report_vehicle_no;
							$datainsert["ritase_report_vehicle_name"] = $ritase_report_vehicle_name;
							$datainsert["ritase_report_vehicle_type"] = $ritase_report_vehicle_type;
							$datainsert["ritase_report_vehicle_company"] = $ritase_report_vehicle_company;
							$datainsert["ritase_report_company_name"] = $ritase_report_company_name;
							
							$datainsert["ritase_report_type"] = $ritase_report_type;
							$datainsert["ritase_report_name"] = $ritase_report_name;
							
							//rom
							$datainsert["ritase_report_start_time"] = $ritase_report_rom_end_time;
							$datainsert["ritase_report_start_location"] = $ritase_report_start_location;
							$datainsert["ritase_report_start_geofence"] = $ritase_report_start_geofence;
							$datainsert["ritase_report_start_coordinate"] = $ritase_report_start_coordinate;
							
							$datainsert["ritase_report_start_date"] = $ritase_report_start_date;
							$datainsert["ritase_report_start_hour"] = $ritase_report_start_hour;
							
							//port
							$datainsert["ritase_report_end_time"] = $ritase_report_port_start_time;
							$datainsert["ritase_report_end_location"] = $ritase_report_end_location;
							$datainsert["ritase_report_end_geofence"] = $ritase_report_end_geofence;
							$datainsert["ritase_report_end_coordinate"] = $ritase_report_end_coordinate;
							
							$datainsert["ritase_report_end_date"] = $ritase_report_end_date;
							$datainsert["ritase_report_end_hour"] = $ritase_report_end_hour;

							$datainsert["ritase_report_driver"] = $ritase_report_driver;
							$datainsert["ritase_report_driver_name"] = $ritase_report_driver_name;
							$datainsert["ritase_report_duration"] = $ritase_report_duration;
							$datainsert["ritase_report_duration_sec"] = $ritase_report_duration_sec;
							
							//new info
							$datainsert["ritase_report_port_start_time"] = $ritase_report_port_start_time;
							$datainsert["ritase_report_port_end_time"] = $ritase_report_port_end_time;
							$datainsert["ritase_report_port_duration_sec"] = $ritase_report_port_duration_sec;
							$datainsert["ritase_report_port_duration"] = $ritase_report_port_duration;
							$datainsert["ritase_report_wim_start_time"] = $ritase_report_wim_start_time;
							$datainsert["ritase_report_wim_end_time"] = $ritase_report_wim_start_time; //same
							$datainsert["ritase_report_wim2_start_time"] = $ritase_report_wim2_start_time;
							$datainsert["ritase_report_wim2_end_time"] = $ritase_report_wim2_start_time; //same
							$datainsert["ritase_report_rom_start_time"] = $ritase_report_rom_start_time;
							$datainsert["ritase_report_rom_end_time"] = $ritase_report_rom_end_time;
							$datainsert["ritase_report_rom_duration_sec"] = $ritase_report_rom_duration_sec;
							$datainsert["ritase_report_rom_duration"] = $ritase_report_rom_duration;
							$datainsert["ritase_report_shift_name"] = $ritase_report_shift_name;
							$datainsert["ritase_report_shift_date"] = $ritase_report_shift_date;
							
													
							//get last data
							$this->dbreport = $this->load->database("tensor_report",true); 
							$this->dbreport->select("ritase_report_id,ritase_report_shift_date,ritase_report_start_time,ritase_report_end_time,ritase_report_start_location,ritase_report_end_location,ritase_report_end_hour");
							$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
							$this->dbreport->where("ritase_report_start_time",$ritase_report_rom_end_time); //tes ganti endtime di port , issue EST beda port tidak tercover
							//$this->dbreport->where("ritase_report_end_time",$ritase_report_port_start_time); //berdasarkan jam di port yg sama (disable) abnormal durati muncul kalau yg dari port time
							$this->dbreport->where("ritase_report_shift_date",$ritase_report_shift_date); // tanggal shift yg sama
							$this->dbreport->where("ritase_report_end_hour",$ritase_report_end_hour); // jam shift yg sama
							//$this->dbreport->where("ritase_report_end_location",$ritase_report_end_location); //lokasi port yg sama  (disable) abnormal durati muncul kalau yg dari port time
							$this->dbreport->limit(1);
							$q_last = $this->dbreport->get($dbtable_ritase);
							$row_last = $q_last->row();
							$total_last = count($row_last);
														
							if($total_last>0){
								
								/* $this->dbreport = $this->load->database("tensor_report",true); 
								$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
								$this->dbreport->where("ritase_report_start_time",$ritase_report_rom_end_time);
								$this->dbreport->where("ritase_report_end_time",$ritase_report_port_start_time);
								//$this->dbreport->limit(1);
								$this->dbreport->update($dbtable_ritase,$datainsert); */
								//printf("===UPDATE OK \r\n ");
								printf("===ALREADY EXISTS \r\n ");
								//print_r($row_last);
							}
							else
							{
								//get last endtime di tanggal shift yg sama
								$this->dbreport = $this->load->database("tensor_report",true); 
								$this->dbreport->select("ritase_report_id,ritase_report_shift_date,ritase_report_start_time,ritase_report_end_time,ritase_report_start_location,ritase_report_end_location,ritase_report_end_hour");
								$this->dbreport->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
								//$this->dbreport->where("ritase_report_shift_date",$ritase_report_shift_date);  //tidak valid jika cron jalan double
								$this->dbreport->where("ritase_report_end_time <",$ritase_report_port_start_time); 
								$this->dbreport->order_by("ritase_report_end_time","desc"); 
								$this->dbreport->limit(1); 
								$q_last2 = $this->dbreport->get($dbtable_ritase);
								$row_last2 = $q_last2->row();
								$total_last2 = count($row_last2);
								print_r($row_last2);
								
								if($total_last2 > 0)
								{
									//untuk case beda jam masih di port yg sama
									$limit_ritase_endtime = 30*60; // default 30 menit;
									$last_ritase_endtime = strtotime($row_last2->ritase_report_end_time);
									$now_ritase_endtime = strtotime($ritase_report_port_start_time);
									
									$delta_ritase_endtime = $now_ritase_endtime - $last_ritase_endtime;
									
									if( ($delta_ritase_endtime > 0 ) && ($delta_ritase_endtime < $limit_ritase_endtime) )
									{
										printf("X==SUDAH ADA DI 30 MENIT SEBELUMNYA, DELTA : %s s \r\n ", $delta_ritase_endtime);
									}
									else
									{
										$this->dbreport->insert($dbtable_ritase,$datainsert);
										printf("===INSERT OK, NEW DATA IN SHIFT HOUR \r\n");
									}
								}
								else
								{
									$this->dbreport->insert($dbtable_ritase,$datainsert);
									printf("===INSERT OK, NEW DATA IN DATE PERIODE \r\n");
								}
													
								
							}
							
								printf("============================================ \r\n");
							
								$this->dbreport->close();
								$this->dbreport->cache_delete_all();
						}
						else
						{
							
							printf("===SKIP! NO DATA IN PORT \r\n");
						}
					
					
						printf("-----------END HOUR--------- \r\n ");
					
				
					
				}
				
				printf("============================== \r\n");
			
			}
			
			//total data ritase
			$this->dbreport = $this->load->database("tensor_report",true); 
			$this->dbreport->select("ritase_report_id");
			$this->dbreport->where("ritase_report_start_date >=",$startdate); 
			$this->dbreport->where("ritase_report_end_date <=",$startdate);
			$q_total = $this->dbreport->get($dbtable_ritase);
			$row_total = $q_total->result();
			$totaldata = count($row_total);
			
			$finishtime = date('Y-m-d H:i:s');
			printf("===FINISH RITASE PER HOUR %s to %s \r\n", $nowtime, $finishtime);
			printf("============================== \r\n");
			
			//send telegram
			$title_name = "RITASE REPORT HOURLY (GPSPORT)";
				$message = urlencode(
						"".$title_name." \n".
						"Periode: ".date("d F Y", strtotime($startdate))." \n".
						"Hour : ".$hour_time." \n".
						"Total Data: ".$totaldata." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);		
			$sendtelegram = $this->telegram_direct("-866565294",$message); // FMS RITASE DBPORT
			printf("===SENT TELEGRAM OK\r\n");
			
			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbts->close();
			$this->dbts->cache_delete_all();
			$this->dbreport->close();
			$this->dbreport->cache_delete_all();
		}
		
			
		
	}
	
	function getStatusLocReport($userid,$vdevice,$sdate,$edate)
	{
		
		if ($vdevice == 'all') {
			$ReportTypeArray = array("LOCATION ALL", "LOCATION IDLE ALL", "LOCATION OFF ALL");
		}else {
			$ReportTypeArray = array("location", "location_off", "location_idle");
		}

		$content = $this->getthisrulelocationreport($ReportTypeArray, $vdevice, $sdate, $edate);

		$data_1 = 0;
		$data_2 = 0;
		$data_3 = 0;
		$data_array_rpoerttype = array();
		$data_content = array_map('current', $content);

					if (in_array($ReportTypeArray[0], $data_content)) {
						$data_1 += 1;
					}

					if (in_array($ReportTypeArray[1], $data_content)) {
						$data_2 += 1;
					}

					if (in_array($ReportTypeArray[2], $data_content)) {
						$data_3 += 1;
					}

			$total_data_fix = ($data_1 + $data_2 + $data_3);

		
			if ($total_data_fix == 3) {
				$result = "DONE";
			}else {
				$result = "ON PROCESS";
			}
			
			return $result;
		}
	
	function getthisrulelocationreport($reportype, $vehicleid, $starttime, $endtime)
	{
		$this->dbtrip = $this->load->database("tensor_report",true);
		$this->dbtrip->order_by("autoreport_data_startdate","asc");

		$this->dbtrip->select("autoreport_type");
		if($vehicleid != "all"){
			$this->dbtrip->where("autoreport_vehicle_device", $vehicleid);
		}

		$this->dbtrip->where_in("autoreport_type", $reportype);
		$this->dbtrip->where("autoreport_data_startdate >=", $starttime);
		$this->dbtrip->where("autoreport_data_enddate <=", $endtime);
		$q = $this->dbtrip->get("autoreport_new")->result_array();
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $q;
	}
	
	function getLocationReport_inPORT($userid,$vehicle_id,$sdate_location,$edate_location,$dbtable_location,$config_portbib)
	{
		$dataloc_in_port = array();
		$dataloc_in_port_time = array();
		
		$last_port_status = 0;
		$min_port_datetime_wita = "";
		$max_port_datetime_wita = "";
		$last_port_name = "";
		$loc_gps_coord = "";
		

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_geofence_name,location_report_coordinate,location_report_location");
		$this->dbreport->order_by("location_report_gps_time","asc");
		//$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time >=", $sdate_location);
		$this->dbreport->where("location_report_gps_time <=", $edate_location);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_geofence_name", $config_portbib); //diubah ambil dari geofence street, issue geofence belum valid / sync
		$this->dbreport->where_in("location_report_location", $config_portbib);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->result();
		$total_datapos = count($datapos);
		if($total_datapos > 0)
		{
			for($x=0;$x<count($datapos);$x++)
			{
				$loc_position_name = $datapos[$x]->location_report_location;
				$loc_gps_time = $datapos[$x]->location_report_gps_time;
				$loc_gps_coord =  $datapos[$x]->location_report_coordinate;
				
				$info_name = $loc_position_name;
				$info_time = $loc_gps_time;
											
				//printf("===Lokasi PORT & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
				array_push($dataloc_in_port, $info_name);
				array_push($dataloc_in_port_time, $info_time);
			}
			
							//rekap data in PORT yg speed nol
							if(count($dataloc_in_port)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($dataloc_in_port as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama port maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_port_status = 1;
									$last_port_name = $dataloc_in_port[0];
									printf("===DATA IN PORT : %s \r\n",$last_port_name);
								
								}
								//kondisi jika didapat > 1 nama PORT
								else
								{
									
									printf("===LEBIH DARI 2 PORT dengan SPEED NOL \r\n");
									$last_port_status = 1;
									$last_port_name = $dataloc_in_port[count($dataloc_in_port) - 1];
									printf("===DATA IN PORT : %s \r\n",$last_port_name);
									
									
								}
							
							
							}
							else
							{
								printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE PORT \r\n");
								
							}
							
							//rekap duration in PORT
							if(count($dataloc_in_port_time)>0)
							{
								usort($dataloc_in_port_time, function($a, $b) {
								$dateTimestamp1 = strtotime($a);
								$dateTimestamp2 = strtotime($b);

								return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
								});
								
								$min_port_datetime = $dataloc_in_port_time[0];
								$max_port_datetime = $dataloc_in_port_time[count($dataloc_in_port_time) - 1];
								printf("===DATA IN PORT : min %s max %s \r\n",$min_port_datetime,$max_port_datetime);
								
								$min_port_datetime_wita = date("Y-m-d H:i:s", strtotime($min_port_datetime . "+0hours"));
								$max_port_datetime_wita = date("Y-m-d H:i:s", strtotime($max_port_datetime . "+0hours"));
								
							}
							else
							{
								printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE PORT \r\n");
								
							}
							
							 
		}
		else
		{
			//printf("===NO DATA LOCATION PORT \r\n ");
			
		}
		
		$data_in_port = $last_port_status."|".$min_port_datetime_wita."|".$max_port_datetime_wita."|".$last_port_name."|".$loc_gps_coord;
			
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $data_in_port;
		
	}
	
	function getLocationReport_lastROM($userid,$vehicle_id,$sdate_port,$dbtable_location,$config_rombib)
	{
		$dataloc_in_rom = array();
		$dataloc_in_rom_time = array();
		
		$last_rom_status = 0;
		$min_rom_datetime_wita = "";
		$max_rom_datetime_wita = "";
		$last_rom_name = "";
		$loc_gps_coord = "";
		
		printf("===SEARCH ROM < %s \r\n", $sdate_port);

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
		$this->dbreport->order_by("location_report_gps_time","desc");
		//$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time <", $sdate_port);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_location", $config_rombib);
		$this->dbreport->limit(15);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->result();
		$total_datapos = count($datapos);
		if($total_datapos > 0)
		{
			for($x=0;$x<count($datapos);$x++)
			{
				$loc_position_name = $datapos[$x]->location_report_location;
				$loc_gps_time = $datapos[$x]->location_report_gps_time;
				$loc_gps_coord =  $datapos[$x]->location_report_coordinate;
								
				$info_name = $loc_position_name;
				$info_time = $loc_gps_time;
											
				//printf("===Lokasi ROM & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
				array_push($dataloc_in_rom, $info_name);
				array_push($dataloc_in_rom_time, $info_time);
			}
			
							//rekap data in ROM yg speed nol
							if(count($dataloc_in_rom)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($dataloc_in_rom as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama rom maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_rom_status = 1;
									$last_rom_name = $dataloc_in_rom[0];
									printf("===DATA IN ROM : %s \r\n",$last_rom_name);
								
								}
								//kondisi jika didapat > 1 nama ROM
								else
								{
									//analisa
									printf("LEBIH DARI 2 ROM dengan SPEED NOL \r\n");
									$last_rom_status = 1;
									$last_rom_name = $dataloc_in_rom[count($dataloc_in_rom) - 1];
									printf("===DATA IN ROM : %s \r\n",$last_rom_name);
									
								}
							
							
							}
							else
							{
								printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE ROM \r\n");
								
							}
							
							//rekap duration in ROM
							if(count($dataloc_in_rom_time)>0)
							{
								usort($dataloc_in_rom_time, function($a, $b) {
								$dateTimestamp1 = strtotime($a);
								$dateTimestamp2 = strtotime($b);

								return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
								});
								
								$min_rom_datetime = $dataloc_in_rom_time[0];
								$max_rom_datetime = $dataloc_in_rom_time[count($dataloc_in_rom_time) - 1];
								printf("===DATA IN ROM : min %s max %s \r\n",$min_rom_datetime,$max_rom_datetime);
								
								$min_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($min_rom_datetime . "+0hours"));
								$max_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($max_rom_datetime . "+0hours"));
								
							}
							else
							{
								printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE ROM \r\n");
								
							}
							
							 
		}
		else
		{
			printf("===NO DATA LOCATION ROM \r\n ");
			
		}
		
		$data_in_rom = $last_rom_status."|".$min_rom_datetime_wita."|".$max_rom_datetime_wita."|".$last_rom_name."|".$loc_gps_coord;
			
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $data_in_rom;
		
	}
	
	function getLocationReport_lastROM_new($userid,$vehicle_id,$sdate_port,$dbtable_location,$config_rombib,$dbtable_location_before)
	{
		$dataloc_in_rom = array();
		$dataloc_in_rom_time = array();
		
		$last_rom_status = 0;
		$min_rom_datetime_wita = "";
		$max_rom_datetime_wita = "";
		$last_rom_name = "";
		$loc_gps_coord = "";
		
		printf("===SEARCH ROM < %s \r\n", $sdate_port);

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
		$this->dbreport->order_by("location_report_gps_time","desc");
		//$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time <", $sdate_port);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_location", $config_rombib);
		$this->dbreport->limit(15);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->result();
		$total_datapos = count($datapos);
		
		
		if($total_datapos > 0)
		{
			for($x=0;$x<count($datapos);$x++)
			{
				$loc_position_name = $datapos[$x]->location_report_location;
				$loc_gps_time = $datapos[$x]->location_report_gps_time;
				$loc_gps_coord =  $datapos[$x]->location_report_coordinate;
								
				$info_name = $loc_position_name;
				$info_time = $loc_gps_time;
											
				//printf("===Lokasi ROM & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
				array_push($dataloc_in_rom, $info_name);
				array_push($dataloc_in_rom_time, $info_time);
			}
			
							//rekap data in ROM yg speed nol
							if(count($dataloc_in_rom)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($dataloc_in_rom as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama rom maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_rom_status = 1;
									$last_rom_name = $dataloc_in_rom[0];
									printf("===DATA IN ROM : %s \r\n",$last_rom_name);
								
								}
								//kondisi jika didapat > 1 nama ROM
								else
								{
									//analisa
									printf("LEBIH DARI 2 ROM dengan SPEED NOL \r\n");
									$last_rom_status = 1;
									$last_rom_name = $dataloc_in_rom[count($dataloc_in_rom) - 1];
									printf("===DATA IN ROM : %s \r\n",$last_rom_name);
									
								}
							
							
							}
							else
							{
								printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE ROM \r\n");
								
							}
							
							//rekap duration in ROM
							if(count($dataloc_in_rom_time)>0)
							{
								usort($dataloc_in_rom_time, function($a, $b) {
								$dateTimestamp1 = strtotime($a);
								$dateTimestamp2 = strtotime($b);

								return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
								});
								
								$min_rom_datetime = $dataloc_in_rom_time[0];
								$max_rom_datetime = $dataloc_in_rom_time[count($dataloc_in_rom_time) - 1];
								printf("===DATA IN ROM : min %s max %s \r\n",$min_rom_datetime,$max_rom_datetime);
								
								$min_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($min_rom_datetime . "+0hours"));
								$max_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($max_rom_datetime . "+0hours"));
								
							}
							else
							{
								printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE ROM \r\n");
								
							}
							
							 
		}
		else
		{
			printf("===NO DATA LOCATION ROM \r\n ");
			
			printf("===SEARCH ROM BEFORE MONTH < %s \r\n", $sdate_port);

			$this->dbreport = $this->load->database("tensor_report",true);
			$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
			$this->dbreport->order_by("location_report_gps_time","desc");
			//$this->dbreport->group_by("location_report_gps_time");
			$this->dbreport->where("location_report_gps_time <", $sdate_port);
			$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
			$this->dbreport->where("location_report_speed", 0);
			$this->dbreport->where_in("location_report_location", $config_rombib);
			$this->dbreport->limit(15);
			$this->dbreport->from($dbtable_location_before);
			$q_loc = $this->dbreport->get();
			$datapos = $q_loc->result();
			$total_datapos = count($datapos);
			
			if($total_datapos > 0)
			{
				for($x=0;$x<count($datapos);$x++)
				{
					$loc_position_name = $datapos[$x]->location_report_location;
					$loc_gps_time = $datapos[$x]->location_report_gps_time;
					$loc_gps_coord =  $datapos[$x]->location_report_coordinate;
									
					$info_name = $loc_position_name;
					$info_time = $loc_gps_time;
												
					//printf("===Lokasi ROM & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
					array_push($dataloc_in_rom, $info_name);
					array_push($dataloc_in_rom_time, $info_time);
				}
				
								//rekap data in ROM yg speed nol
								if(count($dataloc_in_rom)>0)
								{
									$city_counts = array();                            // create an array to hold the counts
									foreach ($dataloc_in_rom as $city_object) {                 // loop over the array of city objects
									// checking isset will prevent undefined index notices    
									if (isset($city_counts[$city_object])) {            
											$city_counts[$city_object]++;        // increment the count for the city
										} else {
											$city_counts[$city_object] = 1;      // initialize the count for the city
										}
									}
									
									
									//jika hanya ada 1 nama rom maka langsung ambil
									if(count($city_counts) == 1)
									{
										$last_rom_status = 1;
										$last_rom_name = $dataloc_in_rom[0];
										printf("===DATA IN ROM : %s \r\n",$last_rom_name);
									
									}
									//kondisi jika didapat > 1 nama ROM
									else
									{
										//analisa
										printf("LEBIH DARI 2 ROM dengan SPEED NOL \r\n");
										$last_rom_status = 1;
										$last_rom_name = $dataloc_in_rom[count($dataloc_in_rom) - 1];
										printf("===DATA IN ROM : %s \r\n",$last_rom_name);
										
									}
								
								
								}
								else
								{
									printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE ROM \r\n");
									
								}
								
								//rekap duration in ROM
								if(count($dataloc_in_rom_time)>0)
								{
									usort($dataloc_in_rom_time, function($a, $b) {
									$dateTimestamp1 = strtotime($a);
									$dateTimestamp2 = strtotime($b);

									return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
									});
									
									$min_rom_datetime = $dataloc_in_rom_time[0];
									$max_rom_datetime = $dataloc_in_rom_time[count($dataloc_in_rom_time) - 1];
									printf("===DATA IN ROM : min %s max %s \r\n",$min_rom_datetime,$max_rom_datetime);
									
									$min_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($min_rom_datetime . "+0hours"));
									$max_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($max_rom_datetime . "+0hours"));
									
								}
								else
								{
									printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE ROM \r\n");
									
								}
								
								 
			}
			else
			{
				
				printf("===NO DATA LOCATION ROM IN TABLE BEFORE \r\n ");
			}
			
			
		}
		
		$data_in_rom = $last_rom_status."|".$min_rom_datetime_wita."|".$max_rom_datetime_wita."|".$last_rom_name."|".$loc_gps_coord;
			
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $data_in_rom;
		
	}
	
	function getLocationReport_lastBeaching($userid,$vehicle_id,$sdate_port,$dbtable_location,$config_beaching,$dbtable_location_before)
	{
		$dataloc_in_beach = array();
		$dataloc_in_beach_time = array();
		
		$last_beach_status = 0;
		$min_beach_datetime_wita = "";
		$max_beach_datetime_wita = "";
		$last_beach_name = "";
		$loc_gps_coord = "";
		$loc_gps_coord_end = "";
		$last_beach_name_end = "";
		
		printf("===SEARCH BEACH < %s from PORT Time \r\n", $sdate_port);

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
		$this->dbreport->order_by("location_report_gps_time","desc");
		//$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time <", $sdate_port);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_location", $config_beaching);
		$this->dbreport->limit(15);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->result();
		$total_datapos = count($datapos);
		
		
		if($total_datapos > 0)
		{
			for($x=0;$x<count($datapos);$x++)
			{
				$loc_position_name = $datapos[$x]->location_report_location;
				$loc_gps_time = $datapos[$x]->location_report_gps_time;
				$loc_gps_coord =  $datapos[$x]->location_report_coordinate;
				$loc_gps_coord_end =  $datapos[0]->location_report_coordinate;
				$last_beach_name_end = $datapos[0]->location_report_location;
				
				$info_name = $loc_position_name;
				$info_time = $loc_gps_time;
											
				printf("===Lokasi BEACH & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
				array_push($dataloc_in_beach, $info_name);
				array_push($dataloc_in_beach_time, $info_time);
			}
			
							//rekap data in ROM yg speed nol
							if(count($dataloc_in_beach)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($dataloc_in_beach as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama rom maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_beach_status = 1;
									$last_beach_name = $dataloc_in_beach[0];
									printf("===DATA IN BEACH : %s \r\n",$last_rom_name);
								
								}
								//kondisi jika didapat > 1 nama ROM
								else
								{
									//analisa
									printf("LEBIH DARI 2 BEACH dengan SPEED NOL \r\n");
									$last_beach_status = 1;
									$last_beach_name = $dataloc_in_beach[count($dataloc_in_beach) - 1];
									printf("===DATA IN BEACH : %s \r\n",$last_beach_name);
									
								}
							
							
							}
							else
							{
								printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE BEACH \r\n");
								
							}
							
							//rekap duration in ROM
							if(count($dataloc_in_beach_time)>0)
							{
								usort($dataloc_in_beach_time, function($a, $b) {
								$dateTimestamp1 = strtotime($a);
								$dateTimestamp2 = strtotime($b);

								return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
								});
								
								$min_beach_datetime = $dataloc_in_beach_time[0];
								$min_beach_datetime = $dataloc_in_beach_time[count($dataloc_in_beach_time) - 1];
								printf("===DATA IN ROM : min %s max %s \r\n",$min_beach_datetime,$min_beach_datetime);
								
								$min_beach_datetime_wita = date("Y-m-d H:i:s", strtotime($min_beach_datetime . "+0hours"));
								$max_beach_datetime_wita = date("Y-m-d H:i:s", strtotime($min_beach_datetime . "+0hours"));
							
							}
							else
							{
								printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE BEACH \r\n");
								
							}
							
							 
		}
		else
		{
			printf("===NO DATA LOCATION ROM \r\n ");
			
			printf("===SEARCH BEACH BEFORE MONTH < %s \r\n", $sdate_port);

			$this->dbreport = $this->load->database("tensor_report",true);
			$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
			$this->dbreport->order_by("location_report_gps_time","desc");
			//$this->dbreport->group_by("location_report_gps_time");
			$this->dbreport->where("location_report_gps_time <", $sdate_port);
			$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
			$this->dbreport->where("location_report_speed", 0);
			$this->dbreport->where_in("location_report_location", $config_beaching);
			$this->dbreport->limit(15);
			$this->dbreport->from($dbtable_location_before);
			$q_loc = $this->dbreport->get();
			$datapos = $q_loc->result();
			$total_datapos = count($datapos);
			
			if($total_datapos > 0)
			{
				for($x=0;$x<count($datapos);$x++)
				{
					$loc_position_name = $datapos[$x]->location_report_location;
					$loc_gps_time = $datapos[$x]->location_report_gps_time;
					$loc_gps_coord =  $datapos[$x]->location_report_coordinate;
					$loc_gps_coord_end =  $datapos[0]->location_report_coordinate;
					$last_beach_name_end = $datapos[0]->location_report_location;
									
					$info_name = $loc_position_name;
					$info_time = $loc_gps_time;
												
					printf("===Lokasi BEACH & 0 Speed: %s %s (BEFORE MONTH) \r\n", $loc_position_name, $loc_gps_time);
					array_push($dataloc_in_beach, $info_name);
					array_push($dataloc_in_beach_time, $info_time);
				}
				
								//rekap data in ROM yg speed nol
								if(count($dataloc_in_beach)>0)
								{
									$city_counts = array();                            // create an array to hold the counts
									foreach ($dataloc_in_beach as $city_object) {                 // loop over the array of city objects
									// checking isset will prevent undefined index notices    
									if (isset($city_counts[$city_object])) {            
											$city_counts[$city_object]++;        // increment the count for the city
										} else {
											$city_counts[$city_object] = 1;      // initialize the count for the city
										}
									}
									
									
									//jika hanya ada 1 nama BEACH maka langsung ambil
									if(count($city_counts) == 1)
									{
										$last_beach_status = 1;
										$last_beach_name = $dataloc_in_beach[0];
										printf("===DATA IN BEACH : %s \r\n",$last_beach_name);
									
									}
									//kondisi jika didapat > 1 nama ROM
									else
									{
										//analisa
										printf("LEBIH DARI 2 BEACH dengan SPEED NOL \r\n");
										$last_beach_status = 1;
										$last_beach_name = $dataloc_in_beach[count($dataloc_in_beach) - 1];
										printf("===DATA IN BEACH : %s \r\n",$last_beach_name);
										
									}
								
								
								}
								else
								{
									printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE BEACH \r\n");
									
								}
								
								//rekap duration in BEACH
								if(count($dataloc_in_beach_time)>0)
								{
									usort($dataloc_in_beach_time, function($a, $b) {
									$dateTimestamp1 = strtotime($a);
									$dateTimestamp2 = strtotime($b);

									return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
									});
									
									$min_beach_datetime = $dataloc_in_beach_time[0];
									$max_beach_datetime = $dataloc_in_beach_time[count($dataloc_in_beach_time) - 1];
									printf("===DATA IN BEACH : min %s max %s \r\n",$min_beach_datetime,$max_beach_datetime);
									
									$min_beach_datetime_wita = date("Y-m-d H:i:s", strtotime($min_beach_datetime . "+0hours"));
									$max_beach_datetime_wita = date("Y-m-d H:i:s", strtotime($max_beach_datetime . "+0hours"));
									
								}
								else
								{
									printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE BEACH \r\n");
									
								}
								
								 
			}
			else
			{
				
				printf("===NO DATA LOCATION BEACH IN TABLE BEFORE \r\n ");
			}
			
			
		}
		
		$data_in_beach = $last_beach_status."|".$min_beach_datetime_wita."|".$max_beach_datetime_wita."|".$last_beach_name."|".$loc_gps_coord."|".$loc_gps_coord_end."|".$last_beach_name_end;
			
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $data_in_beach;
		
	}
	
	function getLocationReport_lastWIM($userid,$vehicle_id,$edate_rom,$sdate_port,$dbtable_location,$wim_location)
	{
		$last_wim_status = 0;
		$wim_datetime = "";
		
		printf("===SEARCH WIM (KM 13.5) >= %s  <= %s \r\n", $edate_rom, $sdate_port);

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
		$this->dbreport->order_by("location_report_gps_time","desc");
		//$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time >=", $edate_rom);
		$this->dbreport->where("location_report_gps_time <=", $sdate_port);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_location", $wim_location);
		$this->dbreport->limit(1);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->row();
		$total_datapos = count($datapos);
		if($total_datapos > 0)
		{
			$last_wim_status = 1;
			$wim_datetime = $datapos->location_report_gps_time;
			printf("===DATA IN WIM %s \r\n", $wim_datetime);
		}
		else
		{
			printf("===NO DATA IN WIM \r\n");
		}
		
		
		$data_in_wim = $last_wim_status."|".$wim_datetime;
			
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $data_in_wim;
		
	}
	
	//NEW METHOD FROM DBPORT
	function getLocation_inPORT_fromGPSPORT($userid,$vehicle_data,$sdate,$edate,$config_portbib)
	{
		//prepare setup DB GPS PORT
		$vdevice = explode("@", $vehicle_data->vehicle_device);
		$json = json_decode($vehicle_data->vehicle_info);
        if (isset($json->vehicle_ip) && isset($json->vehicle_port))
        {

			$databases = $this->config->item('databases');
			if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
			{
				$database = $databases[$json->vehicle_ip][$json->vehicle_port];
				$table = $this->config->item("external_gpstable");
				$tableinfo = $this->config->item("external_gpsinfotable");
				$this->dbhist = $this->load->database($database, TRUE);
			}
		}
		else
		{
			$table = "";
			$tableinfo = "";
		}
		
		//raw gps time
		$sdate_utc = date('Y-m-d H:i:s',strtotime('-7 hours',strtotime($sdate)));
		$edate_utc = date('Y-m-d H:i:s',strtotime('-7 hours',strtotime($edate)));
		
		//print_r($sdate_utc." ".$edate_utc);
			
		$dataloc_in_port = array();
		$dataloc_in_port_time = array();
		
		$last_port_status = 0;
		$min_port_datetime_wita = "";
		$max_port_datetime_wita = "";
		$last_port_name = "";
		$loc_gps_coord = "";
		
		/* $this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_geofence_name,location_report_coordinate,location_report_location");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time >=", $sdate_location);
		$this->dbreport->where("location_report_gps_time <=", $edate_location);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_geofence_name", $config_portbib); //diubah ambil dari geofence street, issue geofence belum valid / sync
		$this->dbreport->where_in("location_report_location", $config_portbib);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->result();
		$total_datapos = count($datapos); */
		
		$this->dbhist->order_by("gps_time","asc");
		$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real,gps_street_name");
		$this->dbhist->where("gps_name", $vdevice[0]);
        $this->dbhist->where("gps_time >=", $sdate_utc);
        $this->dbhist->where("gps_time <=", $edate_utc);
		$this->dbhist->where("gps_speed", 0);
		$this->dbhist->where_in("gps_street_name", $config_portbib);
        $this->dbhist->from($table);
        $q_loc = $this->dbhist->get();
        $datapos = $q_loc->result(); 
		$total_datapos = count($datapos);
		
		$this->dbhist->close();
		$this->dbhist->cache_delete_all();
		
		if($total_datapos > 0)
		{
			for($x=0;$x<count($datapos);$x++)
			{
				$loc_position_data = explode(",",$datapos[$x]->gps_street_name);
				$loc_position_name = $loc_position_data[0];
				$loc_gps_time = date('Y-m-d H:i:s',strtotime('+7 hours',strtotime($datapos[$x]->gps_time)));
				$loc_gps_coord =  $datapos[$x]->gps_latitude_real.",".$datapos[$x]->gps_longitude_real;
				
				$info_name = $loc_position_name;
				$info_time = $loc_gps_time;
											
				//printf("===Lokasi PORT & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
				array_push($dataloc_in_port, $info_name);
				array_push($dataloc_in_port_time, $info_time);
			}
			
							//rekap data in PORT yg speed nol
							if(count($dataloc_in_port)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($dataloc_in_port as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama port maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_port_status = 1;
									$last_port_name = $dataloc_in_port[0];
									printf("===DATA IN PORT : %s \r\n",$last_port_name);
								
								}
								//kondisi jika didapat > 1 nama PORT
								else
								{
									
									printf("===LEBIH DARI 2 PORT dengan SPEED NOL \r\n");
									$last_port_status = 1;
									$last_port_name = $dataloc_in_port[count($dataloc_in_port) - 1];
									printf("===DATA IN PORT : %s \r\n",$last_port_name);
									
									
								}
							
							
							}
							else
							{
								printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE PORT \r\n");
								
							}
							
							//rekap duration in PORT
							if(count($dataloc_in_port_time)>0)
							{
								usort($dataloc_in_port_time, function($a, $b) {
								$dateTimestamp1 = strtotime($a);
								$dateTimestamp2 = strtotime($b);

								return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
								});
								
								$min_port_datetime = $dataloc_in_port_time[0];
								$max_port_datetime = $dataloc_in_port_time[count($dataloc_in_port_time) - 1];
								printf("===DATA IN PORT : min %s max %s \r\n",$min_port_datetime,$max_port_datetime);
								
								$min_port_datetime_wita = date("Y-m-d H:i:s", strtotime($min_port_datetime . "+0hours"));
								$max_port_datetime_wita = date("Y-m-d H:i:s", strtotime($max_port_datetime . "+0hours"));
								
							}
							else
							{
								printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE PORT \r\n");
								
							}
							
							 
		}
		else
		{
			//printf("===NO DATA LOCATION PORT \r\n ");
			
		}
		
		$data_in_port = $last_port_status."|".$min_port_datetime_wita."|".$max_port_datetime_wita."|".$last_port_name."|".$loc_gps_coord;
		
		
		/* $this->dbreport->close();
		$this->dbreport->cache_delete_all(); */
			
		return $data_in_port;
		
	}
	
	function getLocation_lastROM_new_fromGPSPORT($userid,$vehicle_data,$sdate,$config_rombib)
	{
		//prepare setup DB GPS PORT
		$vdevice = explode("@", $vehicle_data->vehicle_device);
		$json = json_decode($vehicle_data->vehicle_info);
        if (isset($json->vehicle_ip) && isset($json->vehicle_port))
        {

			$databases = $this->config->item('databases');
			if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
			{
				$database = $databases[$json->vehicle_ip][$json->vehicle_port];
				$table = $this->config->item("external_gpstable");
				$tableinfo = $this->config->item("external_gpsinfotable");
				$this->dbhist = $this->load->database($database, TRUE);
			}
		}
		else
		{
			$table = "";
			$tableinfo = "";
		}
		
		//raw gps time
		$sdate_utc = date('Y-m-d H:i:s',strtotime('-7 hours',strtotime($sdate)));
		
		$dataloc_in_rom = array();
		$dataloc_in_rom_time = array();
		
		$last_rom_status = 0;
		$min_rom_datetime_wita = "";
		$max_rom_datetime_wita = "";
		$last_rom_name = "";
		$loc_gps_coord = "";
		
		printf("===SEARCH ROM < %s \r\n", $sdate);

		/* $this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
		$this->dbreport->order_by("location_report_gps_time","desc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time <", $sdate_port);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_location", $config_rombib);
		$this->dbreport->limit(15);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->result();
		$total_datapos = count($datapos); */
		
		$this->dbhist->order_by("gps_time","desc");
		$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real,gps_street_name");
		$this->dbhist->where("gps_name", $vdevice[0]);
        $this->dbhist->where("gps_time <", $sdate_utc);
		$this->dbhist->where("gps_speed", 0);
		$this->dbhist->limit(15); //default 15
		$this->dbhist->where_in("gps_street_name", $config_rombib);
        $this->dbhist->from($table);
        $q_loc = $this->dbhist->get();
        $datapos = $q_loc->result(); 
		$total_datapos = count($datapos);
		
		$this->dbhist->close();
		$this->dbhist->cache_delete_all();
		
		
		if($total_datapos > 0)
		{
			for($x=0;$x<count($datapos);$x++)
			{
				$loc_position_data = explode(",",$datapos[$x]->gps_street_name);
				$loc_position_name = $loc_position_data[0];
				$loc_gps_time = date('Y-m-d H:i:s',strtotime('+7 hours',strtotime($datapos[$x]->gps_time)));
				$loc_gps_coord =  $datapos[$x]->gps_latitude_real.",".$datapos[$x]->gps_longitude_real;
								
				$info_name = $loc_position_name;
				$info_time = $loc_gps_time;
											
				//printf("===Lokasi ROM & 0 Speed: %s %s \r\n", $loc_position_name, $loc_gps_time);
				array_push($dataloc_in_rom, $info_name);
				array_push($dataloc_in_rom_time, $info_time);
			}
			
							//rekap data in ROM yg speed nol
							if(count($dataloc_in_rom)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($dataloc_in_rom as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama rom maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_rom_status = 1;
									$last_rom_name = $dataloc_in_rom[0];
									printf("===DATA IN ROM : %s \r\n",$last_rom_name);
								
								}
								//kondisi jika didapat > 1 nama ROM
								else
								{
									//analisa
									printf("LEBIH DARI 2 ROM dengan SPEED NOL \r\n");
									$last_rom_status = 1;
									$last_rom_name = $dataloc_in_rom[count($dataloc_in_rom) - 1];
									printf("===DATA IN ROM : %s \r\n",$last_rom_name);
									
								}
							
							
							}
							else
							{
								printf("===TIDAK ADA DATA SPEED NOL DI GEOFENCE ROM \r\n");
								
							}
							
							//rekap duration in ROM
							if(count($dataloc_in_rom_time)>0)
							{
								usort($dataloc_in_rom_time, function($a, $b) {
								$dateTimestamp1 = strtotime($a);
								$dateTimestamp2 = strtotime($b);

								return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
								});
								
								$min_rom_datetime = $dataloc_in_rom_time[0];
								$max_rom_datetime = $dataloc_in_rom_time[count($dataloc_in_rom_time) - 1];
								printf("===DATA IN ROM : min %s max %s \r\n",$min_rom_datetime,$max_rom_datetime);
								
								$min_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($min_rom_datetime . "+0hours"));
								$max_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($max_rom_datetime . "+0hours"));
								
							}
							else
							{
								printf("===TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE ROM \r\n");
								
							}
							
							 
		}
		//disable untuk cari dari bulan lalu
		/* if( ){
			
		} */
		else
		{
			//printf("===NO DATA LOCATION ROM \r\n ");
			
		}
		
		$data_in_rom = $last_rom_status."|".$min_rom_datetime_wita."|".$max_rom_datetime_wita."|".$last_rom_name."|".$loc_gps_coord;
		
		/* $this->dbreport->close();
		$this->dbreport->cache_delete_all(); */
			
		return $data_in_rom;
		
	}
	
	function getLocation_lastWIM_fromGPSPORT($userid,$vehicle_data,$edate,$sdate,$wim_location)
	{
		//prepare setup DB GPS PORT
		$vdevice = explode("@", $vehicle_data->vehicle_device);
		$json = json_decode($vehicle_data->vehicle_info);
        if (isset($json->vehicle_ip) && isset($json->vehicle_port))
        {

			$databases = $this->config->item('databases');
			if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
			{
				$database = $databases[$json->vehicle_ip][$json->vehicle_port];
				$table = $this->config->item("external_gpstable");
				$tableinfo = $this->config->item("external_gpsinfotable");
				$this->dbhist = $this->load->database($database, TRUE);
			}
		}
		else
		{
			$table = "";
			$tableinfo = "";
		}
		
		//raw gps time
		$sdate_utc = date('Y-m-d H:i:s',strtotime('-7 hours',strtotime($sdate)));
		$edate_utc = date('Y-m-d H:i:s',strtotime('-7 hours',strtotime($edate)));
		
		
		$last_wim_status = 0;
		$wim_datetime = "";
		
		printf("===SEARCH WIM (KM 13.5) >= %s  <= %s \r\n", $edate, $sdate);

		/* $this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_vehicle_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_speed,location_report_location,location_report_coordinate");
		$this->dbreport->order_by("location_report_gps_time","desc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time >=", $edate_rom);
		$this->dbreport->where("location_report_gps_time <=", $sdate_port);
		$this->dbreport->where("location_report_vehicle_id", $vehicle_id);
		$this->dbreport->where("location_report_location", $wim_location);
		$this->dbreport->limit(1);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $datapos = $q_loc->row();
		$total_datapos = count($datapos); */
		
		$this->dbhist->order_by("gps_time","desc");
		$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real,gps_street_name");
		$this->dbhist->where("gps_name", $vdevice[0]);
        $this->dbhist->where("gps_time >=", $edate_utc);
        $this->dbhist->where("gps_time <=", $sdate_utc);
		$this->dbhist->where("gps_street_name", $wim_location);
		$this->dbhist->limit(1);
        $this->dbhist->from($table);
        $q_loc = $this->dbhist->get();
        $datapos = $q_loc->row(); 
		$total_datapos = count($datapos);
		$this->dbhist->close();
		$this->dbhist->cache_delete_all();
		
		
		if($total_datapos > 0)
		{
			$last_wim_status = 1;
			//$wim_datetime = $datapos->location_report_gps_time;
			$wim_datetime = date('Y-m-d H:i:s',strtotime('+7 hours',strtotime($datapos->gps_time)));
			printf("===DATA IN WIM %s \r\n", $wim_datetime);
		}
		else
		{
			printf("===NO DATA IN WIM \r\n");
		}
		
		
		$data_in_wim = $last_wim_status."|".$wim_datetime;
		
		//print_r($data_in_wim);exit();	
		/* $this->dbreport->close();
		$this->dbreport->cache_delete_all(); */
			
		return $data_in_wim;
		
	}
	
	//END NEW METHOD FROM DB PORT
	function getDuration_show($starttime,$endtime)
	{
		$show = "";
		$duration_sec = 0;
		$duration = get_time_difference($starttime, $endtime);

		$start_1 = dbmaketime($starttime);
		$end_1 = dbmaketime($endtime);
		$duration_sec = $end_1 - $start_1;

		$show = "";
		if($duration[0]!=0)
		{
			$show .= $duration[0] ." Day ";
		}
		if($duration[1]!=0)
		{
			$show .= $duration[1] ." Hour ";
		}
		if($duration[2]!=0)
		{
			$show .= $duration[2] ." Min ";
		}
		if($show == "")
		{
			$show .= "0 Min";
		}
		
		$radius_report_duration = $show;
		$ritase_report_port_duration_sec = $duration_sec;
		
		return $show."|".$duration_sec;
	
		
	}
	
	function getVehicle($iduser,$sortir)
	{
		//$nondt_company = array("1962","1961","1963");
		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_company,vehicle_info");	
		$this->db->order_by("vehicle_id", $sortir);
		$this->db->where("vehicle_user_id", $iduser);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where_not_in("vehicle_company", $nondt_company);
		$this->db->where("vehicle_no", "DTH-018");
		
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		
		$this->db->close();
		$this->db->cache_delete_all();
		
		return $rows;

	}
	
	function getVehicle_test($iduser,$sortir)
	{
		$nondt_company = array("1962","1961","1963");
		$dt_list = array
				  (
					"BMT 3940","BMT 3111","BMT 3109","RBT 0035","RBT 0036","RBT 0500","RBT 0540","RBT 4100","RBT 6930","RBT 6870",
					"GEC 9007","GEC 9010","GEC 9001","GEC 9000","GEC 9134","KMB 1026","BKA 1070","BKA 1540","BHS 7560","BHS 7300"
				  );
		
		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_company,vehicle_info");	
		$this->db->order_by("vehicle_id", $sortir);
		$this->db->where("vehicle_user_id", $iduser);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_company", $nondt_company);
		$this->db->where_in("vehicle_no", $dt_list); //test only
		
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		
		$this->db->close();
		$this->db->cache_delete_all();
		
		return $rows;

	}
	
	function getVehicle_all($iduser,$sortir)
	{
		$nondt_company = array("1962","1961","1963");
		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_company,vehicle_info");	
		$this->db->order_by("vehicle_id", $sortir);
		$this->db->where("vehicle_user_id", $iduser);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where_not_in("vehicle_company", $nondt_company);
		//$this->db->where("vehicle_no", "BMT 3110"); //test only
		
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		
		$this->db->close();
		$this->db->cache_delete_all();
		
		return $rows;

	}
	function getCompanyName($companyid)
	{
		
		$this->db = $this->load->database("default",true);
		$this->db->select("company_name");	
		$this->db->order_by("company_name", "desc");
		$this->db->where("company_id ", $companyid);
		$q = $this->db->get("company");
		$rows = $q->row();
		$total_rows = count($rows);
		
		if($total_rows > 0){
			$company_name = $rows->company_name;
		}else{
			$company_name = "";
		}
		
		$this->db->close();
		$this->db->cache_delete_all();
		
		return $company_name;
	
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
