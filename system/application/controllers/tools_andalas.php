<?php
include "base.php";

class Tools_andalas extends Base {

	function Tools_andalas()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->model("smsmodel");
		
		$this->load->library('email');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('validation');
	}
	
	function alert_job_andalas($offset = 0, $i = 0)
	{
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbsms = $this->load->database("smscolo", true);
	
		$start_time = date("Y-m-d H:i:s");
		//$tomorrow = date("Y-m-d", strtotime("tomorrow"));
		$nowtime = date("Y-m-d H:i:s");
		
		printf("ALERT SCHEDULE %s \r\n", $start_time);
		//$company = 411; //galena
		$company = 415; //karya marga
		$this->dbtransporter->order_by("config_id", "asc");
		$this->dbtransporter->where("config_flag", 0);
		$this->dbtransporter->where("config_status", 1);
		$this->dbtransporter->where("config_company", $company);
		$qconfig = $this->dbtransporter->get("andalas_config");
		$rowsconfig = $qconfig->row();
		
		//print_r($rowsconfig);exit();
		
		if (isset($rowsconfig))
		{
		
			if ($rowsconfig->config_second == 7200)
			{ 
				//mengurangi interval 120+10(delay) menit
				$dateinterval = new DateTime($nowtime);
				$dateinterval->add(new DateInterval('PT120M'));
				$sendingtime = $dateinterval->format('Y-m-d H:i:s');
			}
			if ($rowsconfig->config_second == 3600)
			{ 
				//mengurangi interval 60+10(delay) menit
				$dateinterval = new DateTime($nowtime);
				$dateinterval->add(new DateInterval('PT60M'));
				$sendingtime = $dateinterval->format('Y-m-d H:i:s');
			}
		}
		
		$this->dbtransporter->order_by("job_sch_date", "asc");
		$this->dbtransporter->where("job_flag", 0);
		$this->dbtransporter->where("job_status", 1);
		$this->dbtransporter->where("job_alert_status", 0);
		$this->dbtransporter->where("job_sch_date >=", $nowtime);
		$this->dbtransporter->where("job_sch_date <=", $sendingtime);
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$this->dbtransporter->join("mobil", "mobil_id = job_mobil_id", "left");
		$this->dbtransporter->join("andalas_customer_company", "customer_company_id = job_customer_company", "left");
		$q = $this->dbtransporter->get("andalas_job");
		$rows = $q->result();
		$total = count($rows);
		//print_r($rows);exit();
		
		printf("GET TOTAL ALERT SCHEDULE : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER : %s \r\n", ++$i." of ".$total);
			printf("PROCESS ALERT SCHEDULE : %s \r\n", $row->job_sch_date. " ". $row->job_sch_date); 
			
				//send sms alert schedule
				//params
				$sch_date = date("d-F-Y",strtotime($row->job_sch_date));
				$vehicle = $row->job_mobil_name." ".$row->job_mobil_no;
				$driver = $row->driver_name;
				
				$jobnumber = $row->job_number;
				$muatan = $row->job_items;
				$berat = $row->job_weight;
				$dimensi = $row->job_dimensi_p." ".$row->job_dimensi_l." ".$row->job_dimensi_t;
				
				$startfrom = $row->job_from;
				$destination = $row->job_to;
				$customercompany = $row->job_customer_company;
				$nowdate = date("Y-m-d H:i:s");

				// send sms ke customer
				//membuat objek datetime baru
				/* $dateinterval = new DateTime($nowdate_req);
				//menambahkan interval 5 menit
				$dateinterval->add(new DateInterval('PT5M'));
				$sendingdispatcher = $dateinterval->format('Y-m-d H:i:s'); */
				
				$this->dbtransporter->where('customer_flag', 0);
				$this->dbtransporter->where('customer_status', 1);		
				$this->dbtransporter->where('customer_company_group', $customercompany);
				$qcust = $this->dbtransporter->get('andalas_customer');
				$rowcust = $qcust->result();
				if (count($rowcust)>0)
				{
					for ($i=0;$i<count($rowcust);$i++)
					{
						if ($rowcust[$i]->customer_alert_sms == 1)
						{
							// sms posisi
							$vehicleno = nomobil(trim($row->job_mobil_no));
							
							$this->db->where("vehicle_status", 1);
							$this->db->where("REPLACE(REPLACE(vehicle_no, ' ', ''), '.', '') = '".mysql_real_escape_string($vehicleno)."'", null);
							$this->db->join("user", "vehicle_user_id = user_id");
							$this->db->join("agent", "user_agent = agent_id");
							$q = $this->db->get("vehicle");
						
							if ($q->num_rows() == 0)
							{
								printf("No Data \r\n");
							}
						
							$rowvehicle = $q->row();
							$rowvehicles = $q->result();
							
							foreach($rowvehicles as $rowvehicle1)
							{
								$owners[] = $rowvehicle1->user_id;
							}
							
							$t = $rowvehicle->vehicle_active_date2;
							$now = date("Ymd");
							
							if ($t < $now)
							{
								printf("Mobil Expired \r\n");
							}
							
							list($name, $host) = explode("@", $rowvehicle->vehicle_device);

							$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);
							if ($this->gpsmodel->fromsocket)
							{
								$datainfo = $this->gpsmodel->datainfo;
								$fromsocket = $this->gpsmodel->fromsocket;			
							}
									
							if (! $gps)
							{
								printf("Belum Aktif \r\n");
							}
							
							if ($rowvehicle->agent_msite)
							{
								$agentsite = $rowvehicle->agent_msite;
							}
							else
							{
								$agentsite = "m.lacak-mobil.com";
							}
							
							if (! $agentsite)
							{
								if ($rowvehicle->user_agent == 3)
								{
									$agentsite = "m.gpsandalas.com";
								}
								else
								{
									$agentsite = "m.lacak-mobil.com";
								}
							}
							
							
							$mapurl = sprintf("http://%s/map.php?%s=%s", $agentsite, date("YmdHis", $gps->gps_timestamp), urlencode($rowvehicle->vehicle_no));						
							$abbreviation = $this->smsmodel->abbreviation($mapurl);
							$mapurl = sprintf("http://%s/%s", $agentsite, $abbreviation);		
							
							$gtps = $this->config->item("vehicle_gtp");
							$gtpdoors = $this->config->item("vehicle_gtp_door");
							
							$dir = $gps->direction-1;
							$dirs = $this->config->item("direction");
							
							if ($dir < 0)
							{
								$sdir = $gps->gps_course."°";
							}
							else
							if ($dir >= count($dirs))
							{
								$sdir = $gps->gps_course."°";
							}
							else
							{
								$sdir = $dirs[$dir]."(".$gps->gps_course."°)";
							}
							
							if (in_array(strtoupper($rowvehicle->vehicle_type), $gtps))
							{
								if (! isset($datainfo))
								{
									if (isset($gps) && $gps && date("Ymd", $gps->gps_timestamp) >= date("Ymd"))
									{
										$tables = $this->gpsmodel->getTable($rowvehicle);
										$this->db = $this->load->database($tables["dbname"], TRUE);

									}
									else
									{	
										$devices = explode("@", $rowvehicle->vehicle_device);
										$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
										$this->db = $this->load->database("gpshistory", TRUE);
									}
									
									// ambil informasi di gps_info
									
									$this->db->order_by("gps_info_time", "DESC");
									$this->db->where("gps_info_device", $rowvehicle->vehicle_device);
									$q = $this->db->get($tables['info'], 1, 0);
								}
									
								if ((! isset($datainfo)) && ($q->num_rows() == 0))
								{
									$engine = "OFF";
									$door = "CLOSED";
								}
								else
								{
									$rowinfo = isset($datainfo) ? $datainfo : $q->row();					
									$ioport = $rowinfo->gps_info_io_port;
										
									$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
									$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
									$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
										
									$engine = $status1 ? "ON" : "OFF";
									$door = $status3 ? "OPENED" : "CLOSED";
								}			
									
								if (in_array(strtoupper($rowvehicle->vehicle_type), $gtpdoors))
								{
									$reply = sprintf("%s\n%s\n%s %s\n%s %s\n%s\nEng:%s Door:%s", date("d/m/Y H:i", $gps->gps_timestamp), $gps->georeverse->display_name, $gps->gps_latitude_real_fmt, $gps->gps_longitude_real_fmt, $gps->gps_speed_fmt."kph", $sdir, ($gps->gps_status != "V") ? "OK" : "NO", $engine, $door);
								}
								else
								{
									$reply = sprintf("%s\n%s\n%s %s\n%s %s\n%s\nEng:%s", date("d/m/Y H:i", $gps->gps_timestamp), $gps->georeverse->display_name, $gps->gps_latitude_real_fmt, $gps->gps_longitude_real_fmt, $gps->gps_speed_fmt."kph", $sdir, ($gps->gps_status != "V") ? "OK" : "NO", $engine);
								}
							}
							else
							{
								$reply = sprintf("%s\n%s\n%s %s\n%s %s\n%s", date("d/m/Y H:i", $gps->gps_timestamp), $gps->georeverse->display_name, $gps->gps_latitude_real_fmt, $gps->gps_longitude_real_fmt, $gps->gps_speed_fmt."kph", $sdir, ($gps->gps_status != "V") ? "OK" : "NO");
							}
							
							$this->db = $this->load->database("default", TRUE);
							
							$delta = mktime() - $gps->gps_timestamp;
							if ($delta >= 1800)
							{
								printf("=========== >= 1800 =========== \r\n"); 
							} 
							
							if ($delta >= 24*3600)
							{
								printf("Kendaraan tidak update \r\n");		
							}
							
							unset($sms_alert_schedule_to_cust);
							$sms_alert_schedule_to_cust['SenderNumber'] = $rowcust[$i]->customer_mobile;
							$sms_alert_schedule_to_cust['ReceivingDateTime'] = date('Y-m-d H:i:s');
							$sms_alert_schedule_to_cust['TextDecoded'] = 
																	"ALERT"."\n".
																	"Kendaraan:"." ".$vehicle."\n".
																	"Driver:"." ".$driver."\n".
																	"Tanggal:"." ".$sch_date."\n".
																	"Muat:"." ".$muatan." "."\n".
																	"Tujuan:"." ".$destination.""."\n".
																	"Posisi:"." ".$reply.""
																	;
							$sms_alert_schedule_to_cust['RecipientID'] = "order";
							printf("INSERT ALERT TO DB SMS  : %s \r\n", $row->job_number.": ".$row->job_sch_date); 
							$this->dbsms->insert('inbox', $sms_alert_schedule_to_cust);
							printf("SEND SMS OK \r\n");
							printf("====================== \r\n"); 
						}
						else {
							printf("Alert SMS Customer Off \r\n");
							printf("====================== \r\n"); 
						}
						
						if ($rowcust[$i]->customer_alert_email == 1)
						{
							if ($rowcust[$i]->customer_email != "")
							{	
								unset($mail);
								$contentmail = $this->message_format_alert($row, $reply);
								$mail['subject'] =  " Job Alert";
								$mail['message'] = $contentmail;
								$mail['dest'] = $rowcust[$i]->customer_email;
								$mail['bcc'] = "buddiyanto@gmail.com";

								$mail['sender'] = "no-reply@gpsandalas.com";
								lacakmobilmail($mail);
								printf("SEND EMAIL OK \r\n"); 
								printf("====================== \r\n"); 
							}
						}
						else {
							printf("Alert Email Customer Off \r\n");
							printf("====================== \r\n"); 
						}
			
					}
					
					//update alert status
					unset($alertstatus);
					$sent = "1";
				
					$alertstatus['job_alert_status'] = $sent;
					
					$this->dbtransporter->where("job_id", $row->job_id);
					$this->dbtransporter->update("andalas_job", $alertstatus);
					printf("UPDATE STATUS ALERT OK \r\n");
					printf("====================== \r\n");
					
				}

		}
		
		$this->dbtransporter->close();
		$this->dbtransporter->cache_delete_all();
		$this->dbsms->close();
		$this->dbsms->cache_delete_all();
		$this->db->close();
		$this->db->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH ALERT SCHEDULE !!"." ".$finish_time. "\r\n");
		printf("====================== \r\n"); 
	}
	
	function message_format_alert($row, $reply)
	{
		
		$msg = 
"Kepada Yth Bpk/Ibu dari ". $row->customer_company_name . ",

Dengan ini kami informasikan jadwal waktu angkut kendaraan sebagai berikut:

Kendaraan   : ".$row->mobil_name." ". $row->mobil_no."
Pengemudi   : ".$row->driver_name."

Job Number  : ".$row->job_number."
No. Po 	    : ".$row->job_po."
Waktu Muat  : ".date("d-F-Y H:i", strtotime($row->job_sch_date))."
Muatan      : ". $row->job_items."
Destination : ". $row->job_to."

Posisi Truck : "

.$reply."

Terima Kasih
Admin Dispatcher
http://transporter.gpsandalas.com/
";
				
		return $msg;
	}
	
	function alert_unjob_andalas_daily($offset = 0, $i = 0)
	{
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		//$this->dbsms = $this->load->database("smscolo", true);
		
		$nowdate = date('Y-m-d');
		///$company = 411; //company galena perkasa
		$company = 415; //company karya marga
		
		printf("==== SEARCH VEHICLE ====\r\n");
		$this->dbtransporter->order_by("mobil_device","asc");
		$this->dbtransporter->where("mobil_company ", $company);
		$q = $this->dbtransporter->get("mobil");
		$rows = $q->result();
		$total = count($rows);
		printf("GET VEHICLE FROM COMPANY ID : %s \r\n", $total); 
		//print_r($rows);exit();
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			
			unset($data);
			
			$data["daily_alert_vehicle_no"] = $row->mobil_no;
			$data["daily_alert_vehicle_name"] = $row->mobil_name;
			$data["daily_alert_vehicle_device"] = $row->mobil_device;
			$data["daily_alert_vehicle_company"] = $row->mobil_company;
			$data["daily_alert_date"] = $nowdate;
			
			$this->dbtransporter->order_by("job_sch_date", "asc");
			$this->dbtransporter->where("job_flag", 0);
			$this->dbtransporter->where("job_status", 1);
			$this->dbtransporter->where("job_alert_status", 0);
			$this->dbtransporter->where("job_date", $nowdate);
			$this->dbtransporter->where("job_mobil_device", $row->mobil_device);
			
			$qjob = $this->dbtransporter->get("andalas_job");
			$rjob = $qjob->row();
			//print_r($rjob);exit();
			if (count($rjob)>0)
			{
				printf("KENDARAAN SUDAH ADA JOB : %s \r\n", $row->mobil_no." ".$row->mobil_name); 
				
			}
			else
			{
				printf("KENDARAAN BELUM ADA JOB : %s \r\n", $row->mobil_no." ".$row->mobil_name); 
				$this->dbtransporter->insert("andalas_daily_alert",$data);
				printf("INSERT OK \r\n"); 
				printf("====================== \r\n"); 
			}

			//finish select vehicle
			
		}
	
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "daily_alert_vehicle_no";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		
		printf("SEARCH VEHICLE STAND BY \r\n");
		$this->dbtransporter->order_by("daily_alert_id","desc");
		$this->dbtransporter->order_by("daily_alert_vehicle_no","asc");
		$this->dbtransporter->where("daily_alert_flag", 0);
		$this->dbtransporter->where("daily_alert_date ", $nowdate);
		$this->dbtransporter->where("daily_alert_vehicle_company", $company);
		//$this->dbtransporter->limit(5);
		$qv = $this->dbtransporter->get("andalas_daily_alert");
		$rows_unjob = $qv->result();
		$total_unjob = count($rows_unjob);
		if (count($rows_unjob)>0)
		{
			printf("==== CREATE EXCEL ==== \r\n");
			
			/** PHPExcel */
			include 'class/PHPExcel.php';
				
			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';
				
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			$domain_server = "http://202.129.190.194/";
			$report_path = "/home/transporter/public_html/assets/media/report/";
			$pub_path = "assets/media/report/";
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("transporter.gpsandalas.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("transporter.gpsandalas.com");
			$objPHPExcel->getProperties()->setTitle("SUMMARY DAILY REPORT");
			$objPHPExcel->getProperties()->setSubject("SUMMARY DAILY REPORT");
			$objPHPExcel->getProperties()->setDescription("SUMMARY DAILY REPORT");
			
			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(30);			
			
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'SUMMARY DAILY REPORT');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			
			
			if($startdate){
				
				$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
				$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->SetCellValue('C3', date("d-m-Y",strtotime($startdate)));
			}
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Total Vehicle: ');
			$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', $total_unjob);
			$objPHPExcel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
			
			
			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Status');
			
			$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i = 1;
			for ($j=0;$j<count($rows_unjob);$j++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows_unjob[$j]->daily_alert_vehicle_no);
				$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows_unjob[$j]->daily_alert_vehicle_name);
				$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				//status
				if (isset($rows_unjob[$j]->daily_alert_flag))
				{
					
					if ($rows_unjob[$j]->daily_alert_flag == 0)
					{
						$status = "STAND BY";
						
					}
					if ($rows_unjob[$j]->daily_alert_flag == 1)
					{
						$status = "NOT AVAILABLE";
						
					}
					
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $status);
				$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				}
				
				$i++;
			}
			
			$styleArray = array(
					  'borders' => array(
						'allborders' => array(
						  'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					  )
					);
				
				$objPHPExcel->getActiveSheet()->getStyle('A7:D'.(6+$i))->applyFromArray($styleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A7:D'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$objPHPExcel->getActiveSheet()->getStyle('A7:D'.(6+$i))->getAlignment()->setWrapText(true);
				
				// Rename sheet
				$objPHPExcel->getActiveSheet()->setTitle('SUMMARY DAILY REPORT');
				printf("==== CREATE FILE ===== \r\n");
				// Save Excel
				$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
				@mkdir($report_path, DIR_WRITE_MODE);
				
				if($startdate){
					$filedate = date("dmY",strtotime($startdate));
				}
				else{
					$filedate = date("dmY");
				}
				$filecreatedname = "summary_daily_report_".$filedate.".xls";
				
				$objWriter->save($report_path.$filecreatedname); 
				printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
				$public_path = $domain_server.$pub_path.$filecreatedname;

				/* $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
				
				echo $output;
				return; */
				
					printf("===== SEND EMAIL ===== \r\n");
					$this->dbtransporter->where('alert_dispatcher_company', $company);	
					$this->dbtransporter->where('alert_dispatcher_flag', 0);
					$this->dbtransporter->where('alert_dispatcher_status', 1);		
					$qdispatc = $this->dbtransporter->get('andalas_info_alert');
					$rowdispatch = $qdispatc->result();
					//$total_vehicle = count($rowdispatch);
					
					if (count($rowdispatch)>0)
					{
						for ($i=0;$i<count($rowdispatch);$i++)
						{
							
							if ($rowdispatch[$i]->alert_dispatcher_config_email == 1)
							{
								if ($rowdispatch[$i]->alert_dispatcher_email != "")
								{
									
									unset($mail);
									$contentmail = $this->message_daily_alert();
									$this->load->library('email');
									$this->email->set_newline('\r\n');
									$this->email->clear();
									$this->email->from('no-reply@gpsandalas.com');
									$this->email->to($rowdispatch[$i]->alert_dispatcher_email);
									$this->email->cc('');
									$this->email->subject('DAILY SUMMARY REPORT');
									$this->email->message($contentmail);
									$this->email->attach($report_path.$filecreatedname);

									if($this->email->send())
									{
										printf("SEND EMAIL OK \r\n"); 
										printf("====================== \r\n");
									}
								 
									else
									{
										show_error($this->email->print_debugger());
									}
			  
								}
							}
							else {
								printf("Alert Email Dispatcher Off \r\n");
								printf("====================== \r\n"); 
							}
		
						}
		
					}	
				
				//update alert status
				unset($alertstatus);
				$flag = "1";
			
				$alertstatus['daily_alert_flag'] = $flag;
				
				$this->dbtransporter->where("daily_alert_date", $nowdate);
				$this->dbtransporter->update("andalas_daily_alert", $alertstatus);
				printf("UPDATE STATUS ALERT OK \r\n");
				printf("====================== \r\n");
		}
		else
		{
			printf("NO DATA \r\n"); 
			printf("====================== \r\n"); 
		}
				
		$this->db->cache_delete_all();
        $this->dbtransporter->cache_delete_all();	
		printf(" ======== FINISH ======== \r\n"); 
	}
	
	function message_daily_alert()
	{
		$nowdate = date("d F Y H:i");
		//$nowtime = date("H:i");
		$msg = 
"
Dengan ini kami informasikan kendaraan yang sedang stand by 
pada tanggal ".$nowdate." ( attached ) .


Terima Kasih
http://transporter.gpsandalas.com/
";
				
		return $msg;
	}
	


	

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
