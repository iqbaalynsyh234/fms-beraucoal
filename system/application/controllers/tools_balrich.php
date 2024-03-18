<?php
include "base.php";

class Tools_balrich extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	function sync_geoalert($duration="")
	{
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		
		if($duration == ""){
			$duration = "-1";
		}
		$report = "inout_geofence_";
		
		/*$nowdate = date('Y-m-d');
		$limitdate = strtotime ($duration.' day' , strtotime ($nowdate." "."00:00:00"));
		$newdate = date('Y-m-d H:i:s' , $limitdate);*/
		
		$newdate = date("Y-m-d H:i:s", strtotime("2017-05-16 00:00:00"));
		$newdate2 = date("Y-m-d H:i:s", strtotime("2017-05-20 23:59:59"));
		
		$m1 = date("F", strtotime($newdate)); 
		$year = date("Y", strtotime($newdate));
		
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
		
		printf("PROSES SELECT GEOFENCE ALERT %s DAY \r\n", $duration);
		$this->db->order_by("geoalert_id","asc");
		$this->db->where("geoalert_time >=", $newdate);
		$this->db->where("geoalert_time <=", $newdate2);
		$this->db->where("geoalert_direction", 1);//hanya masuk area
		$q = $this->db->get("geofence_alert_balrich");
		$rows = $q->result();
		$total = count($rows);
		printf("GET GEO ALERT : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row->geoalert_geofence, $row->geoalert_id, $row->geoalert_time); 
			$this->dbreport = $this->load->database("balrich_report", true);
		
			unset($data);
			$data["geoalert_id"] = $row->geoalert_id;
			$data["geoalert_vehicle"] = $row->geoalert_vehicle;
			$data["geoalert_direction"] = $row->geoalert_direction; 
			$data["geoalert_door"] = $row->geoalert_door;
			$data["geoalert_date"] = date("Y-m-d", strtotime ($row->geoalert_time));
			$data["geoalert_time"] = date("H:i:s", strtotime ($row->geoalert_time));
			$data["geoalert_geofence"] = $row->geoalert_geofence;
			$data["geoalert_geofence_name"] = $row->geoalert_geofence_name;
			
			$this->dbreport->select("geoalert_id");
			$this->dbreport->where("geoalert_id", $row->geoalert_id);
			$qu = $this->dbreport->get($dbtable);
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE GEOALERT IN DB REPORT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row->geoalert_geofence, $row->geoalert_id, $row->geoalert_time); 
				$this->dbreport->where("geoalert_id", $row->geoalert_id);	
				$this->dbreport->update($dbtable,$data);
			}
			else
			{
				printf("INSERT GEOFEALERT IN DB REPORT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row->geoalert_geofence, $row->geoalert_id, $row->geoalert_time); 
				$this->dbreport->insert($dbtable,$data);
			}
			
			printf("FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row->geoalert_geofence, $row->geoalert_id, $row->geoalert_time); 
			printf("=============================================== \r\n"); 
			
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC GEOALERT REPORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,ryana@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	function sync_droppoint($droppoint="",$month="",$year="",$date="")
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		$report = "inout_geofence_";
		
		if($droppoint == ""){
			$droppoint = "";
		}
		
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		
		$m1 = date("F", strtotime($newdate)); 
		$year = date("Y", strtotime($newdate));
		
		
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
		
		printf("PROSES SELECT DROPPOINT %s \r\n", $droppoint);
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("droppoint_id","desc");
		$this->dbtransporter->where("droppoint_flag",0);
		if($droppoint != ""){
			$this->dbtransporter->where("droppoint_id",$droppoint);	
		}
		$q = $this->dbtransporter->get("droppoint");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DROPPOINT : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DROPPOINT : %s %s \r\n", $row->droppoint_id, $row->droppoint_name);
			
			unset($data);
			$data["georeport_droppoint"] = $row->droppoint_id;
			$data["georeport_geofence_name"] = $row->droppoint_geofence;
			$data["georeport_geofence_id"] = $row->droppoint_geofence_id;
			
			$this->dbreport = $this->load->database("balrich_report", true);
			$this->dbreport->select("georeport_droppoint");
			$this->dbreport->where("georeport_droppoint", $row->droppoint_id);
			$qu = $this->dbreport->get($dbtable);
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE DROPPOINT IN DB : %s, %s, %s \r\n", $row->droppoint_id, $row->droppoint_geofence, $dbtable); 
				$this->dbreport->where("georeport_droppoint", $row->droppoint_id);	
				$this->dbreport->update($dbtable,$data);
			}
			else
			{
				printf("INSERT DROPPOINT IN DB : %s, %s, %s \r\n", $row->droppoint_id, $row->droppoint_geofence, $dbtable); 
				$this->dbreport->insert($dbtable,$data);
			}
			
			printf("FINISH SYNC DROPPOINT : ID GEOFENCE %s, %s \r\n", $row->droppoint_id, $row->droppoint_geofence); 
			printf("=============================================== \r\n"); 
			
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC DROPPOINT TO DB GEOREPORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Data: ".$total." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return;   
		
	}
	
	function sync_droppoint_month($month="",$year="")
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		$report = "inout_geofence_";
		$date = "";
		$droppoint = "";
		
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		
		$m1 = date("F", strtotime($newdate)); 
		$year = date("Y", strtotime($newdate));
		
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
		
		printf("PROSES SELECT DROPPOINT %s \r\n", $droppoint);
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("droppoint_id","desc");
		$this->dbtransporter->where("droppoint_flag",0);
		$q = $this->dbtransporter->get("droppoint");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DROPPOINT : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DROPPOINT : %s %s \r\n", $row->droppoint_id, $row->droppoint_name);
			
			unset($data);
			$data["georeport_droppoint"] = $row->droppoint_id;
			$data["georeport_geofence_name"] = $row->droppoint_geofence;
			$data["georeport_geofence_id"] = $row->droppoint_geofence_id;
			
			$this->dbreport = $this->load->database("balrich_report", true);
			$this->dbreport->select("georeport_droppoint");
			$this->dbreport->where("georeport_droppoint", $row->droppoint_id);
			$qu = $this->dbreport->get($dbtable);
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE DROPPOINT IN DB : %s, %s , %s \r\n", $row->droppoint_id, $row->droppoint_geofence, $dbtable); 
				$this->dbreport->where("georeport_droppoint", $row->droppoint_id);	
				$this->dbreport->update($dbtable,$data);
			}
			else
			{
				printf("INSERT DROPPOINT IN DB : %s, %s , %s \r\n", $row->droppoint_id, $row->droppoint_geofence, $dbtable); 
				$this->dbreport->insert($dbtable,$data);
			}
			
			printf("FINISH SYNC DROPPOINT : ID GEOFENCE %s, %s \r\n", $row->droppoint_id, $row->droppoint_geofence); 
			printf("=============================================== \r\n"); 
			
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC DROPPOINT PER MONTH TO DB GEOREPORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,ryana@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	function targettime_nextmonth_ota($type="", $date="",$month="",$year="")
	{
		//default = akan dicari 1 bulan sebelum, kemudian di insert untuk data bulan sekarang
		//custom = 0 01 08 2017 -> berati dicari 1 bulan sebelum tanggal custom, kemudian di cari next month nya untuk di update
		
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		if($month == ""){
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = "01";
		}
		if($type == ""){
			$type = "0";
		}
		
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date)); //awal bulan baru
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date != ""){
			$date = "01";
		}
		
		//1 bulan sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1M');
		$date->sub($interval);
		$awal_date = $date->format('Y-m-d') . "\n";
		$akhir_date = date("Y-m-t", strtotime($awal_date));
		
		//bulan sekarang
		$awal_newdate = date("Y-m-d", strtotime($newdate));
		$akhir_newdate = date("Y-m-t", strtotime($awal_newdate));
		
		//print_r($awal_date." ~ ".$akhir_date." | ".$awal_newdate." ~ ".$akhir_newdate);
		
		printf("PROSES SELECT DROPPOINT \r\n");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("droppoint_id","asc");
		$this->dbtransporter->where("droppoint_flag",0);
		$q = $this->dbtransporter->get("droppoint");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DROPPOINT : %s \r\n", $total); 
	
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DROPPOINT : %s %s \r\n", $row->droppoint_id, $row->droppoint_name);
			
			//select per droppoint berdasarkan periode awal - akhir bulan & tipe regular / combine
			$this->dbtransporter->order_by("target_startdate","desc");
			$this->dbtransporter->where("target_type",$type);
			$this->dbtransporter->where("target_flag",0);
			$this->dbtransporter->where("target_startdate >=",$awal_date);
			$this->dbtransporter->where("target_startdate <=",$akhir_date);
			$this->dbtransporter->where("target_droppoint",$row->droppoint_id);
			$q_target = $this->dbtransporter->get("droppoint_target");
			$row_target = $q_target->row();
			$total_row_target = count($row_target);
			
			
			//jika ada droppoint
			if($total_row_target > 0){
				//print_r($awal_date." ".$akhir_date." ".$row_target->target_time);exit();
				//jika ada data droppoint
				//prepare data
				unset($data);
				$data["target_type"] = $row_target->target_type;
				$data["target_droppoint"] = $row_target->target_droppoint;
				$data["target_company"] = $row_target->target_company;
				$data["target_startdate"] = $awal_newdate;
				$data["target_enddate"] = $akhir_newdate;
				$data["target_month"] = date("m", strtotime($awal_newdate));
				$data["target_year"] = date("Y", strtotime($awal_newdate));
				$data["target_time"] = $row_target->target_time;
				$data["target_creator"] = 0;
				$data["target_creator_datetime"] = date("Y-m-d H:i:s");
				$data["target_flag"] = 0;
			
				//select per droppoint berdasarkan periode awal - akhir bulan baru & tipe regular / combine
				$this->dbtransporter->order_by("target_startdate","desc");
				$this->dbtransporter->where("target_type",$type);
				$this->dbtransporter->where("target_flag",0);
				$this->dbtransporter->where("target_startdate >=",$awal_newdate);
				$this->dbtransporter->where("target_startdate <=",$akhir_newdate);
				$this->dbtransporter->where("target_droppoint",$row_target->target_droppoint);
				$q_target2 = $this->dbtransporter->get("droppoint_target");
				$row_target2 = $q_target2->row();
				
				//jika sudah ada data di bulan baru 
				if (count($row_target2)>0)
				{
					printf("UPDATE TARGET TIME : %s, %s, %s, %s, %s \r\n", $row->droppoint_name, $row_target->target_startdate, $row_target->target_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->where("target_id", $row_target2->target_id);	
					$this->dbtransporter->update("droppoint_target",$data);
					
				}
				else
				{
					printf("INSERT NEW TARGET TIME : %s, %s, %s, %s, %s \r\n", $row->droppoint_name, $row_target->target_startdate, $row_target->target_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->insert("droppoint_target",$data);
				}
			}else{
				
				printf("NO DATA TARGET : DROPPOINT %s \r\n",$row->droppoint_name); 
				printf("=============================================== \r\n"); 
			}
			
			printf("FINISH SYNC TARGET NEXT MONTH : ID DROPPOINT %s, %s \r\n", $row->droppoint_id, $row->droppoint_name); 
			printf("=============================================== \r\n"); 
		
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC TARGET TIME NEXT MONTH OTA";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,ryana@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	function targettime_nextmonth_ota_perdistrep($distrep="", $date="",$month="",$year="" ,$type="")
	{
		//default = akan dicari 1 bulan sebelum, kemudian di insert untuk data bulan sekarang
		//custom = 0 01 08 2017 -> berati dicari 1 bulan sebelum tanggal custom, kemudian di cari next month nya untuk di update
		
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		if($month == ""){
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = "01";
		}
		if($type == ""){
			$type = "0";
		}
		if($distrep == ""){
			$distrep = "0";
		}
		
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date)); //awal bulan baru
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date != ""){
			$date = "01";
		}
		
		//1 bulan sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1M');
		$date->sub($interval);
		$awal_date = $date->format('Y-m-d') . "\n";
		$akhir_date = date("Y-m-t", strtotime($awal_date));
		
		//bulan sekarang
		$awal_newdate = date("Y-m-d", strtotime($newdate));
		$akhir_newdate = date("Y-m-t", strtotime($awal_newdate));
		
		//print_r($awal_date." ~ ".$akhir_date." | ".$awal_newdate." ~ ".$akhir_newdate);
		
		printf("PROSES SELECT DROPPOINT \r\n");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("droppoint_id","asc");
		$this->dbtransporter->where("droppoint_distrep",$distrep);
		$this->dbtransporter->where("droppoint_flag",0);
		$q = $this->dbtransporter->get("droppoint");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DROPPOINT : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DROPPOINT : %s %s \r\n", $row->droppoint_id, $row->droppoint_name);
			
			//select per droppoint berdasarkan periode awal - akhir bulan & tipe regular / combine
			$this->dbtransporter->order_by("target_startdate","desc");
			$this->dbtransporter->where("target_type",$type);
			$this->dbtransporter->where("target_flag",0);
			$this->dbtransporter->where("target_startdate >=",$awal_date);
			$this->dbtransporter->where("target_startdate <=",$akhir_date);
			$this->dbtransporter->where("target_droppoint",$row->droppoint_id);
			$q_target = $this->dbtransporter->get("droppoint_target");
			$row_target = $q_target->row();
			$total_row_target = count($row_target);
			
			
			//jika ada droppoint
			if($total_row_target > 0){
				
				//jika ada data droppoint
				//prepare data
				unset($data);
				$data["target_type"] = $row_target->target_type;
				$data["target_droppoint"] = $row_target->target_droppoint;
				$data["target_company"] = $row_target->target_company;
				$data["target_startdate"] = $awal_newdate;
				$data["target_enddate"] = $akhir_newdate;
				$data["target_month"] = date("m", strtotime($awal_newdate));
				$data["target_year"] = date("Y", strtotime($awal_newdate));
				$data["target_time"] = $row_target->target_time;
				$data["target_creator"] = 0;
				$data["target_creator_datetime"] = date("Y-m-d H:i:s");
				$data["target_flag"] = 0;
			
				//select per droppoint berdasarkan periode awal - akhir bulan baru & tipe regular / combine
				$this->dbtransporter->order_by("target_startdate","desc");
				$this->dbtransporter->where("target_type",$type);
				$this->dbtransporter->where("target_flag",0);
				$this->dbtransporter->where("target_startdate >=",$awal_newdate);
				$this->dbtransporter->where("target_startdate <=",$akhir_newdate);
				$this->dbtransporter->where("target_droppoint",$row_target->target_droppoint);
				$q_target2 = $this->dbtransporter->get("droppoint_target");
				$row_target2 = $q_target2->row();
				
				//jika sudah ada data di bulan baru 
				if (count($row_target2)>0)
				{
					printf("UPDATE TARGET TIME : %s, %s, %s, %s, %s \r\n", $row->droppoint_name, $row_target->target_startdate, $row_target->target_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->where("target_id", $row_target2->target_id);	
					$this->dbtransporter->update("droppoint_target",$data);
					
				}
				else
				{
					printf("INSERT NEW TARGET TIME : %s, %s, %s, %s, %s \r\n", $row->droppoint_name, $row_target->target_startdate, $row_target->target_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->insert("droppoint_target",$data);
				}
			}else{
				
				printf("NO DATA TARGET : DROPPOINT %s \r\n",$row->droppoint_name); 
				printf("=============================================== \r\n"); 
			}
			
			printf("FINISH SYNC TARGET NEXT MONTH : ID DROPPOINT %s, %s \r\n", $row->droppoint_id, $row->droppoint_name); 
			printf("=============================================== \r\n"); 
		
		}
		
		$finish_time = date("d-m-Y H:i:s");
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	function targettime_nextmonth_otl($date="",$month="",$year="")
	{
		//default = akan dicari 1 bulan sebelum, kemudian di insert untuk data bulan sekarang
		//custom = 01 08 2017 -> berati dicari 1 bulan sebelum tanggal custom, kemudian di cari next month nya untuk di update
		
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		$type = "OTL";
		
		if($month == ""){
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = "01";
		}
		
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date)); //awal bulan baru
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date != ""){
			$date = "01";
		}
		
		//1 bulan sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1M');
		$date->sub($interval);
		$awal_date = $date->format('Y-m-d') . "\n";
		$akhir_date = date("Y-m-t", strtotime($awal_date));
		
		//bulan sekarang
		$awal_newdate = date("Y-m-d", strtotime($newdate));
		$akhir_newdate = date("Y-m-t", strtotime($awal_newdate));
		
		//print_r($awal_date." ~ ".$akhir_date." | ".$awal_newdate." ~ ".$akhir_newdate);exit();
		
		printf("PROSES SELECT DISTREP \r\n");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","asc");
		$this->dbtransporter->where("distrep_flag",0);
		$q = $this->dbtransporter->get("droppoint_distrep");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DISTREP : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DISTREP : %s %s \r\n", $row->distrep_id, $row->distrep_name);
			
			//select per droppoint berdasarkan periode awal - akhir bulan & tipe regular / combine
			$this->dbtransporter->order_by("target_loading_startdate","desc");
			$this->dbtransporter->where("target_loading_type",$type);
			$this->dbtransporter->where("target_loading_flag",0);
			$this->dbtransporter->where("target_loading_startdate >=",$awal_date);
			$this->dbtransporter->where("target_loading_startdate <=",$akhir_date);
			$this->dbtransporter->where("target_loading_distrep",$row->distrep_id);
			$q_target = $this->dbtransporter->get("droppoint_target_loading");
			$row_target = $q_target->row();
			$total_row_target = count($row_target);
			
			//jika ada droppoint
			if($total_row_target > 0){
				//print_r($awal_date." ".$akhir_date." ".$row_target->target_loading_time);exit();
				//jika ada data droppoint
				//prepare data
				unset($data);
				$data["target_loading_type"] = $row_target->target_loading_type;
				$data["target_loading_distrep"] = $row_target->target_loading_distrep;
				$data["target_loading_company"] = $row_target->target_loading_company;
				$data["target_loading_startdate"] = $awal_newdate;
				$data["target_loading_enddate"] = $akhir_newdate;
				$data["target_loading_month"] = date("m", strtotime($awal_newdate));
				$data["target_loading_year"] = date("Y", strtotime($awal_newdate));
				$data["target_loading_time"] = $row_target->target_loading_time;
				$data["target_loading_creator"] = 0;
				$data["target_loading_creator_datetime"] = date("Y-m-d H:i:s");
				$data["target_loading_flag"] = 0;
			
				//select per distrep berdasarkan periode awal - akhir bulan baru & tipe regular / combine
				$this->dbtransporter->order_by("target_loading_startdate","desc");
				$this->dbtransporter->where("target_loading_type",$type);
				$this->dbtransporter->where("target_loading_flag",0);
				$this->dbtransporter->where("target_loading_startdate >=",$awal_newdate);
				$this->dbtransporter->where("target_loading_startdate <=",$akhir_newdate);
				$this->dbtransporter->where("target_loading_distrep",$row_target->target_loading_distrep);
				$q_target2 = $this->dbtransporter->get("droppoint_target_loading");
				$row_target2 = $q_target2->row();
				
				//jika sudah ada data di bulan baru 
				if (count($row_target2)>0)
				{
					printf("UPDATE TARGET TIME OTL : %s, %s, %s, %s, %s \r\n", $row->distrep_name, $row_target->target_loading_startdate, $row_target->target_loading_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->where("target_loading_id", $row_target2->target_loading_id);	
					$this->dbtransporter->update("droppoint_target_loading",$data);
					
				}
				else
				{
					printf("INSERT NEW TARGET TIME OTL : %s, %s, %s, %s, %s \r\n", $row->distrep_name, $row_target->target_loading_startdate, $row_target->target_loading_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->insert("droppoint_target_loading",$data);
					
				}
			}else{
				
				printf("NO DATA TARGET : DISTREP %s \r\n",$row->distrep_name); 
				printf("=============================================== \r\n"); 
			}
			
			printf("FINISH SYNC TARGET NEXT MONTH OTL : ID DISTREP %s, %s \r\n", $row->distrep_id, $row->distrep_name); 
			printf("=============================================== \r\n"); 
		
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC TARGET TIME NEXT MONTH OTL";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,ryana@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	function targettime_nextmonth_otd($date="",$month="",$year="")
	{
		//default = akan dicari 1 bulan sebelum, kemudian di insert untuk data bulan sekarang
		//custom = 01 08 2017 -> berati dicari 1 bulan sebelum tanggal custom, kemudian di cari next month nya untuk di update
		
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		$type = "OTD";
		
		if($month == ""){
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = "01";
		}
		
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date)); //awal bulan baru
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date != ""){
			$date = "01";
		}
		
		//1 bulan sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1M');
		$date->sub($interval);
		$awal_date = $date->format('Y-m-d') . "\n";
		$akhir_date = date("Y-m-t", strtotime($awal_date));
		
		//bulan sekarang
		$awal_newdate = date("Y-m-d", strtotime($newdate));
		$akhir_newdate = date("Y-m-t", strtotime($awal_newdate));
		
		//print_r($awal_date." ~ ".$akhir_date." | ".$awal_newdate." ~ ".$akhir_newdate);exit();
		
		printf("PROSES SELECT DISTREP \r\n");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","asc");
		$this->dbtransporter->where("distrep_flag",0);
		$q = $this->dbtransporter->get("droppoint_distrep");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DISTREP : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DISTREP : %s %s \r\n", $row->distrep_id, $row->distrep_name);
			
			//select per droppoint berdasarkan periode awal - akhir bulan & tipe regular / combine
			$this->dbtransporter->order_by("target_loading_startdate","desc");
			$this->dbtransporter->where("target_loading_type",$type);
			$this->dbtransporter->where("target_loading_flag",0);
			$this->dbtransporter->where("target_loading_startdate >=",$awal_date);
			$this->dbtransporter->where("target_loading_startdate <=",$akhir_date);
			$this->dbtransporter->where("target_loading_distrep",$row->distrep_id);
			$q_target = $this->dbtransporter->get("droppoint_target_loading");
			$row_target = $q_target->row();
			$total_row_target = count($row_target);
			
			//jika ada droppoint
			if($total_row_target > 0){
				//print_r($awal_date." ".$akhir_date." ".$row_target->target_loading_time);exit();
				//jika ada data droppoint
				//prepare data
				unset($data);
				$data["target_loading_type"] = $row_target->target_loading_type;
				$data["target_loading_distrep"] = $row_target->target_loading_distrep;
				$data["target_loading_company"] = $row_target->target_loading_company;
				$data["target_loading_startdate"] = $awal_newdate;
				$data["target_loading_enddate"] = $akhir_newdate;
				$data["target_loading_month"] = date("m", strtotime($awal_newdate));
				$data["target_loading_year"] = date("Y", strtotime($awal_newdate));
				$data["target_loading_time"] = $row_target->target_loading_time;
				$data["target_loading_creator"] = 0;
				$data["target_loading_creator_datetime"] = date("Y-m-d H:i:s");
				$data["target_loading_flag"] = 0;
			
				//select per distrep berdasarkan periode awal - akhir bulan baru & tipe regular / combine
				$this->dbtransporter->order_by("target_loading_startdate","desc");
				$this->dbtransporter->where("target_loading_type",$type);
				$this->dbtransporter->where("target_loading_flag",0);
				$this->dbtransporter->where("target_loading_startdate >=",$awal_newdate);
				$this->dbtransporter->where("target_loading_startdate <=",$akhir_newdate);
				$this->dbtransporter->where("target_loading_distrep",$row_target->target_loading_distrep);
				$q_target2 = $this->dbtransporter->get("droppoint_target_loading");
				$row_target2 = $q_target2->row();
				
				//jika sudah ada data di bulan baru 
				if (count($row_target2)>0)
				{
					printf("UPDATE TARGET TIME OTD : %s, %s, %s, %s, %s \r\n", $row->distrep_name, $row_target->target_loading_startdate, $row_target->target_loading_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->where("target_loading_id", $row_target2->target_loading_id);	
					$this->dbtransporter->update("droppoint_target_loading",$data);
					
				}
				else
				{
					printf("INSERT NEW TARGET TIME OTD : %s, %s, %s, %s, %s \r\n", $row->distrep_name, $row_target->target_loading_startdate, $row_target->target_loading_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->insert("droppoint_target_loading",$data);
					
				}
			}else{
				
				printf("NO DATA TARGET : DISTREP %s \r\n",$row->distrep_name); 
				printf("=============================================== \r\n"); 
			}
			
			printf("FINISH SYNC TARGET NEXT MONTH OTL : ID DISTREP %s, %s \r\n", $row->distrep_id, $row->distrep_name); 
			printf("=============================================== \r\n"); 
		
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC TARGET TIME NEXT MONTH OTD";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,ryana@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	function targettime_beforemonth_perdistrep($type="", $date="",$month="",$year="", $distrep="")
	{
		//default = akan dicari 1 bulan sebelum, kemudian di insert untuk data bulan sekarang
		//custom = 0 01 08 2017 -> berati dicari 1 bulan sebelum tanggal custom, kemudian di cari next month nya untuk di update
		
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
		$newdate = date("Y-m-d");
		
		if($month == ""){
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = "01";
		}
		if($type == ""){
			$type = "0";
		}
		
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date)); //awal bulan baru
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date != ""){
			$date = "01";
		}
		
		//1 bulan sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1M');
		$date->sub($interval);
		
		//ditukar bulan (pengisian mundur 1 bulan)
		/*$awal_date = $date->format('Y-m-d') . "\n";
		$akhir_date = date("Y-m-t", strtotime($awal_date));*/
		
		$awal_newdate = $date->format('Y-m-d') . "\n";
		$akhir_newdate = date("Y-m-t", strtotime($awal_newdate));
		
		//bulan sekarang 
		/*$awal_newdate = date("Y-m-d", strtotime($newdate));
		$akhir_newdate = date("Y-m-t", strtotime($awal_newdate));)*/
		
		$awal_date = date("Y-m-d", strtotime($awal_date));
		$akhir_date = date("Y-m-t", strtotime($akhir_date));
		
		print_r($awal_date." ~ ".$akhir_date." | ".$awal_newdate." ~ ".$akhir_newdate);exit();
		
		printf("PROSES SELECT DROPPOINT \r\n");
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("droppoint_id","asc");
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("droppoint_distrep",$distrep);
		$q = $this->dbtransporter->get("droppoint");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DROPPOINT : %s \r\n", $total); 
		exit();
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);
			printf("PROSES DROPPOINT : %s %s \r\n", $row->droppoint_id, $row->droppoint_name);
			
			//select per droppoint berdasarkan periode awal - akhir bulan & tipe regular / combine
			$this->dbtransporter->order_by("target_startdate","desc");
			$this->dbtransporter->where("target_type",$type);
			$this->dbtransporter->where("target_flag",0);
			$this->dbtransporter->where("target_startdate >=",$awal_date);
			$this->dbtransporter->where("target_startdate <=",$akhir_date);
			$this->dbtransporter->where("target_droppoint",$row->droppoint_id);
			$q_target = $this->dbtransporter->get("droppoint_target");
			$row_target = $q_target->row();
			$total_row_target = count($row_target);
			
			
			//jika ada droppoint
			if($total_row_target > 0){
				//print_r($awal_date." ".$akhir_date." ".$row_target->target_time);exit();
				//jika ada data droppoint
				//prepare data
				unset($data);
				$data["target_type"] = $row_target->target_type;
				$data["target_droppoint"] = $row_target->target_droppoint;
				$data["target_company"] = $row_target->target_company;
				$data["target_startdate"] = $awal_newdate;
				$data["target_enddate"] = $akhir_newdate;
				$data["target_month"] = date("m", strtotime($awal_newdate));
				$data["target_year"] = date("Y", strtotime($awal_newdate));
				$data["target_time"] = $row_target->target_time;
				$data["target_creator"] = 0;
				$data["target_creator_datetime"] = date("Y-m-d H:i:s");
				$data["target_flag"] = 0;
			
				//select per droppoint berdasarkan periode awal - akhir bulan baru & tipe regular / combine
				$this->dbtransporter->order_by("target_startdate","desc");
				$this->dbtransporter->where("target_type",$type);
				$this->dbtransporter->where("target_flag",0);
				$this->dbtransporter->where("target_startdate >=",$awal_newdate);
				$this->dbtransporter->where("target_startdate <=",$akhir_newdate);
				$this->dbtransporter->where("target_droppoint",$row_target->target_droppoint);
				$q_target2 = $this->dbtransporter->get("droppoint_target");
				$row_target2 = $q_target2->row();
				
				//jika sudah ada data di bulan baru 
				if (count($row_target2)>0)
				{
					printf("UPDATE TARGET TIME : %s, %s, %s, %s, %s \r\n", $row->droppoint_name, $row_target->target_startdate, $row_target->target_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->where("target_id", $row_target2->target_id);	
					$this->dbtransporter->update("droppoint_target",$data);
					
				}
				else
				{
					printf("INSERT NEW TARGET TIME : %s, %s, %s, %s, %s \r\n", $row->droppoint_name, $row_target->target_startdate, $row_target->target_time, $awal_newdate, $akhir_newdate); 
					$this->dbtransporter->insert("droppoint_target",$data);
				}
			}else{
				
				printf("NO DATA TARGET : DROPPOINT %s \r\n",$row->droppoint_name); 
				printf("=============================================== \r\n"); 
			}
			
			printf("FINISH SYNC TARGET NEXT MONTH : ID DROPPOINT %s, %s \r\n", $row->droppoint_id, $row->droppoint_name); 
			printf("=============================================== \r\n"); 
		
		}
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - SYNC TARGET TIME BEFORE MONTH DISTREP";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total."
End Data   : "."( ".$i." / ".$total." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,ryana@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
		
	}
	
	//this new OTA REPORT
	function ota_report_new($date="",$month="",$year="",$distrep="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		//print_r($newdatetime." ".$newdatetime2." ".$m1);exit();
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","asc");
		$this->dbtransporter->where("distrep_creator",1032);
		$this->dbtransporter->where("distrep_flag",0);
		if($distrep != ""){
			$this->dbtransporter->where("distrep_id",$distrep);	
		}
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","asc");
			$this->dbtransporter->where("droppoint_flag",0);
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			$geoalert_km = "";
			$geoalert_koord = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
				
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end,trip_mileage_cummulative_mileage,trip_mileage_coordinate_end");
							$this->dbreport->order_by("trip_mileage_start_time",$row_drop->droppoint_sort);
							$this->dbreport->where("trip_mileage_vehicle_id",$row_distrep->distrep_vehicle_device);
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
							$this->dbreport->limit(1);
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->row();
							$total_row_report = count($row_report);
							if($total_row_report > 0){
								printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
								$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
								$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
								$geoalert_name = $row_report->trip_mileage_geofence_end;
								$geoalert_status = "OPR";
								$geoalert_km = $row_report->trip_mileage_cummulative_mileage;
								$geoalert_koord = $row_report->trip_mileage_coordinate_end;
								
								/*if($geoalert_koord != ""){
									unset($data_koord);
									//update master droppoint
									$data_koord["droppoint_koord"] = $geoalert_koord;
									
									printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
									$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
									$this->dbtransporter->limit(1);	
									$this->dbtransporter->update("droppoint",$data_koord);
								}else{
									printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
								}*/
					
							}else{
								//select per tanggal dari table DOOR REPORT
								//cari berdasarkan DOOR OPEN di area droppoint (ambil start_time nya) (search by door) search level 2
								$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start,door_cumm_mileage,door_coordinate_start");
								$this->dbreport->order_by("door_start_time",$row_drop->droppoint_sort);
								$this->dbreport->where("door_status","OPEN");
								$this->dbreport->where("door_vehicle_device",$row_distrep->distrep_vehicle_device);
								$this->dbreport->where("door_start_time >=",$newdatetime);
								$this->dbreport->where("door_start_time <=",$newdatetime2);
								$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
								$this->dbreport->limit(1);
								$q_report = $this->dbreport->get($dbtable_door);
								$row_report = $q_report->row();
								$total_row_report = count($row_report);
								if($total_row_report > 0){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 2 (DOOR OPEN) !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
									$geoalert_vehicle = $row_report->door_vehicle_no;
									$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
									$geoalert_name = $row_report->door_geofence_start;
									$geoalert_status = "DOOR";
									$geoalert_km = $row_report->door_cumm_mileage;
									$geoalert_koord = $row_report->door_coordinate_start;
									
									/*if($geoalert_koord != ""){
										unset($data_koord);
										//update master droppoint
										$data_koord["droppoint_koord"] = $geoalert_koord;
										
										printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
										$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
										$this->dbtransporter->limit(1);	
										$this->dbtransporter->update("droppoint",$data_koord);
									}else{
										printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
									} */
								}else{
									
									//jika tidak ada cari random mobil yg open di droppoint // random mobil - search level 3
									$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start,door_cumm_mileage,door_coordinate_start");
									$this->dbreport->order_by("door_start_time",$row_drop->droppoint_sort);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_start_time >=",$newdatetime);
									$this->dbreport->where("door_start_time <=",$newdatetime2);
									$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
									$this->dbreport->limit(1);
									$q_report = $this->dbreport->get($dbtable_door);
									$row_report = $q_report->row();
									$total_row_report = count($row_report);
									
									if($total_row_report > 0){
										printf("3==GEOALERT - ADA GEOALERT LEVEL 3 (DOOR OPEN) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
										$geoalert_vehicle = $row_report->door_vehicle_no;
										$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
										$geoalert_name = $row_report->door_geofence_start;
										$geoalert_status = "DOOR";
										$geoalert_km = $row_report->door_cumm_mileage;
										$geoalert_koord = $row_report->door_coordinate_start;
										
										/*if($geoalert_koord != ""){
											unset($data_koord);
											//update master droppoint
											$data_koord["droppoint_koord"] = $geoalert_koord;
											
											printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
											$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
											$this->dbtransporter->limit(1);	
											$this->dbtransporter->update("droppoint",$data_koord);
										}else{
											printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
										} */
								
									}else{
										//jika tidak ada cari random mobil yg off di droppoint // random mobil - search level 4
										$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end,trip_mileage_cummulative_mileage,trip_mileage_coordinate_end");
										$this->dbreport->order_by("trip_mileage_start_time",$row_drop->droppoint_sort);
										$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
										$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
										$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
										$this->dbreport->limit(1);
										$q_report = $this->dbreport->get($dbtable_opr);
										$row_report = $q_report->row();
										$total_row_report = count($row_report);
										
										if($total_row_report > 0){
											printf("3==GEOALERT - ADA GEOALERT LEVEL 4 (ENGINE OFF) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
											$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
											$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
											$geoalert_name = $row_report->trip_mileage_geofence_end;
											$geoalert_status = "OPR";
											$geoalert_km = $row_report->trip_mileage_cummulative_mileage;
											$geoalert_koord = $row_report->trip_mileage_coordinate_end;
											
											/*if($geoalert_koord != ""){
												unset($data_koord);
												//update master droppoint
												$data_koord["droppoint_koord"] = $geoalert_koord;
												
												printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
												$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
												$this->dbtransporter->limit(1);	
												$this->dbtransporter->update("droppoint",$data_koord);
											}else{
												printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
											} */
											
										}else{
											//tidak ada semuanya
											printf("3X==TIDAK ADA SEMUA GEOALERT !!!"); 
											$geoalert_vehicle = "";
											$geoalert_time = date("H:i:s", strtotime("00:00:00"));
											$geoalert_name = "TIDAK ADA SEMUA GEOALERT - RESET";
											$geoalert_status = "";
											$geoalert_km = "";
										}
									}
									
								}
								
							}
					
					$field_date = "georeport_date_".$date;
					
					//INSERT KE TABLE REPORT PER TANGGAL REPORT CRON
					unset($data);
					//opr
					$data["georeport_date_".$date] = $geoalert_time;
					$data["georeport_vehicle_".$date] = $geoalert_vehicle;
					$data["georeport_status_".$date] = $geoalert_status;
					$data["georeport_km_".$date] =$geoalert_km;
					
					//door
					/*$data["georeport_door_date_".$date] = $geoalert_door_time;
					$data["georeport_door_vehicle_".$date] = $geoalert_door_vehicle;
					$data["georeport_door_status_".$date] = $geoalert_door_status;*/
					
					printf("3==GEOALERT - UPDATE GEOREPORT : %s %s \r\n", $geoalert_name, $geoalert_door_name); 
					$this->dbreport->where("georeport_droppoint", $row_drop->droppoint_id);	
					$this->dbreport->update($dbtable,$data);
					
				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - OTA REPORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_distrep."
End Distrep   : "."( ".$i." / ".$total_distrep." )"."

Total Droppoint : ".$total_drop."
End Droppoint   : "."( ".$j." / ".$total_drop." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
		$this->db->select("company_id,company_telegram_cron");
		$this->db->where("company_id",$company_username);
		$qcompany = $this->db->get("company");
		$rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Droppoint: ".$total_drop." \n".
					"Total Distrep: ".$total_distrep." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		 
		
	}
	
	//this new OTA REPORT OTHERS
	function ota_report_others($date="",$month="",$year="", $distrep="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		$id_balrich = 1032;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","asc");
		$this->dbtransporter->where("distrep_creator !=",$id_balrich);
		$this->dbtransporter->where("distrep_flag",0);
		if($distrep != ""){
			$this->dbtransporter->where("distrep_id",$distrep); //custom
		}
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","asc");
			$this->dbtransporter->where("droppoint_flag",0);
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			$geoalert_km = "";
			$geoalert_koord = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
							//khusus untuk ambil data dari jam desc (ex: IDM MEDAN)
							if($row_drop->droppoint_sort == "desc"){ 
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."15:00:00"));
							}else{
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
							}
							
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end,trip_mileage_cummulative_mileage,trip_mileage_coordinate_end,trip_mileage_vehicle_user");
							$this->dbreport->order_by("trip_mileage_start_time","asc");
							$this->dbreport->where("trip_mileage_vehicle_id",$row_distrep->distrep_vehicle_device);
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->where("trip_mileage_vehicle_user <>",$id_balrich);
							$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
							$this->dbreport->limit(1);
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->row();
							$total_row_report = count($row_report);
							if($total_row_report > 0){
								printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
								$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
								$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
								$geoalert_name = $row_report->trip_mileage_geofence_end;
								$geoalert_status = "OPR";
								$geoalert_km = $row_report->trip_mileage_cummulative_mileage;
								$geoalert_koord = $row_report->trip_mileage_coordinate_end;
								
								/*if($geoalert_koord != ""){
									unset($data_koord);
									//update master droppoint
									$data_koord["droppoint_koord"] = $geoalert_koord;
									
									printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
									$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
									$this->dbtransporter->limit(1);	
									$this->dbtransporter->update("droppoint",$data_koord);
								}else{
									printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
								}*/
					
							}else{
								//select per tanggal dari table DOOR REPORT
								//cari berdasarkan DOOR OPEN di area droppoint (ambil start_time nya) (search by door) search level 2
								$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start,door_cumm_mileage,door_coordinate_start,door_vehicle_user");
								$this->dbreport->order_by("door_start_time","asc");
								$this->dbreport->where("door_status","OPEN");
								$this->dbreport->where("door_vehicle_device",$row_distrep->distrep_vehicle_device);
								$this->dbreport->where("door_start_time >=",$newdatetime);
								$this->dbreport->where("door_start_time <=",$newdatetime2);
								$this->dbreport->where("door_vehicle_user <>",$id_balrich);
								$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
								$this->dbreport->limit(1);
								$q_report = $this->dbreport->get($dbtable_door);
								$row_report = $q_report->row();
								$total_row_report = count($row_report);
								if($total_row_report > 0){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 2 (DOOR OPEN) !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
									$geoalert_vehicle = $row_report->door_vehicle_no;
									$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
									$geoalert_name = $row_report->door_geofence_start;
									$geoalert_status = "DOOR";
									$geoalert_km = $row_report->door_cumm_mileage;
									$geoalert_koord = $row_report->door_coordinate_start;
									
									/*if($geoalert_koord != ""){
										unset($data_koord);
										//update master droppoint
										$data_koord["droppoint_koord"] = $geoalert_koord;
										
										printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
										$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
										$this->dbtransporter->limit(1);	
										$this->dbtransporter->update("droppoint",$data_koord);
									}else{
										printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
									} */
								}else{
									
									//jika tidak ada cari random mobil yg open di droppoint // random mobil - search level 3
									$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start,door_cumm_mileage,door_coordinate_start,door_vehicle_user");
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_start_time >=",$newdatetime);
									$this->dbreport->where("door_start_time <=",$newdatetime2);
									$this->dbreport->where("door_vehicle_user <>",$id_balrich);
									$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
									$this->dbreport->limit(1);
									$q_report = $this->dbreport->get($dbtable_door);
									$row_report = $q_report->row();
									$total_row_report = count($row_report);
									
									if($total_row_report > 0){
										printf("3==GEOALERT - ADA GEOALERT LEVEL 3 (DOOR OPEN) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
										$geoalert_vehicle = $row_report->door_vehicle_no;
										$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
										$geoalert_name = $row_report->door_geofence_start;
										$geoalert_status = "DOOR";
										$geoalert_km = $row_report->door_cumm_mileage;
										$geoalert_koord = $row_report->door_coordinate_start;
										
										/*if($geoalert_koord != ""){
											unset($data_koord);
											//update master droppoint
											$data_koord["droppoint_koord"] = $geoalert_koord;
											
											printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
											$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
											$this->dbtransporter->limit(1);	
											$this->dbtransporter->update("droppoint",$data_koord);
										}else{
											printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
										} */
								
									}else{
										//jika tidak ada cari random mobil yg off di droppoint // random mobil - search level 4
										$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end,trip_mileage_cummulative_mileage,trip_mileage_coordinate_end,trip_mileage_vehicle_user");
										$this->dbreport->order_by("trip_mileage_start_time","asc");
										$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
										$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
										$this->dbreport->where("trip_mileage_vehicle_user <>",$id_balrich);
										$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
										$this->dbreport->limit(1);
										$q_report = $this->dbreport->get($dbtable_opr);
										$row_report = $q_report->row();
										$total_row_report = count($row_report);
										
										if($total_row_report > 0){
											printf("3==GEOALERT - ADA GEOALERT LEVEL 4 (ENGINE OFF) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
											$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
											$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
											$geoalert_name = $row_report->trip_mileage_geofence_end;
											$geoalert_status = "OPR";
											$geoalert_km = $row_report->trip_mileage_cummulative_mileage;
											$geoalert_koord = $row_report->trip_mileage_coordinate_end;
											
											/*if($geoalert_koord != ""){
												unset($data_koord);
												//update master droppoint
												$data_koord["droppoint_koord"] = $geoalert_koord;
												
												printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
												$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
												$this->dbtransporter->limit(1);	
												$this->dbtransporter->update("droppoint",$data_koord);
											}else{
												printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
											} */
											
										}else{
											//tidak ada semuanya
											printf("3X==TIDAK ADA SEMUA GEOALERT !!!"); 
											$geoalert_vehicle = "";
											$geoalert_time = date("H:i:s", strtotime("00:00:00"));
											$geoalert_name = "TIDAK ADA SEMUA GEOALERT - RESET";
											$geoalert_status = "";
											$geoalert_km = "";
										}
									}
									
								}
								
							}
					
					$field_date = "georeport_date_".$date;
					
					//INSERT KE TABLE REPORT PER TANGGAL REPORT CRON
					unset($data);
					//opr
					$data["georeport_date_".$date] = $geoalert_time;
					$data["georeport_vehicle_".$date] = $geoalert_vehicle;
					$data["georeport_status_".$date] = $geoalert_status;
					$data["georeport_km_".$date] =$geoalert_km;
					
					//door
					/*$data["georeport_door_date_".$date] = $geoalert_door_time;
					$data["georeport_door_vehicle_".$date] = $geoalert_door_vehicle;
					$data["georeport_door_status_".$date] = $geoalert_door_status;*/
					
					printf("3==GEOALERT - UPDATE GEOREPORT : %s %s \r\n", $geoalert_name, $geoalert_door_name); 
					$this->dbreport->where("georeport_droppoint", $row_drop->droppoint_id);	
					$this->dbreport->update($dbtable,$data);
					
				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "LACAKTRANSPRO - OTA REPORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_distrep."
End Distrep   : "."( ".$i." / ".$total_distrep." )"."

Total Droppoint : ".$total_drop."
End Droppoint   : "."( ".$j." / ".$total_drop." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
		$this->db->select("company_id,company_telegram_cron");
		$this->db->where("company_id",$company_username);
		$qcompany = $this->db->get("company");
		$rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Droppoint: ".$total_drop." \n".
					"Total Distrep: ".$total_distrep." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		 
		
	}
	
	function droppoint_report_new_distrep($date="",$month="",$year="",$distrep="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		//print_r($newdatetime." ".$newdatetime2." ".$m1);exit();
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","desc");
		$this->dbtransporter->where("distrep_flag",0);
		if($distrep != ""){
			$this->dbtransporter->where("distrep_id", $distrep);
		}
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","desc");
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
				
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end");
							$this->dbreport->order_by("trip_mileage_start_time","asc");
							$this->dbreport->where("trip_mileage_vehicle_id",$row_distrep->distrep_vehicle_device);
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
							$this->dbreport->limit(1);
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->row();
							$total_row_report = count($row_report);
							if($total_row_report > 0){
								printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
								$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
								$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
								$geoalert_name = $row_report->trip_mileage_geofence_end;
								$geoalert_status = "OPR";
							}else{
								//select per tanggal dari table DOOR REPORT
								//cari berdasarkan DOOR OPEN di area droppoint (ambil start_time nya) (search by door) search level 2
								$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start");
								$this->dbreport->order_by("door_start_time","asc");
								$this->dbreport->where("door_status","OPEN");
								$this->dbreport->where("door_vehicle_device",$row_distrep->distrep_vehicle_device);
								$this->dbreport->where("door_start_time >=",$newdatetime);
								$this->dbreport->where("door_start_time <=",$newdatetime2);
								$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
								$this->dbreport->limit(1);
								$q_report = $this->dbreport->get($dbtable_door);
								$row_report = $q_report->row();
								$total_row_report = count($row_report);
								if($total_row_report > 0){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 2 (DOOR OPEN) !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
									$geoalert_vehicle = $row_report->door_vehicle_no;
									$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
									$geoalert_name = $row_report->door_geofence_start;
									$geoalert_status = "DOOR";
								}else{
									
									//jika tidak ada cari random mobil yg open di droppoint // random mobil - search level 3
									$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start");
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_start_time >=",$newdatetime);
									$this->dbreport->where("door_start_time <=",$newdatetime2);
									$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
									$this->dbreport->limit(1);
									$q_report = $this->dbreport->get($dbtable_door);
									$row_report = $q_report->row();
									$total_row_report = count($row_report);
									
									if($total_row_report > 0){
										printf("3==GEOALERT - ADA GEOALERT LEVEL 3 (DOOR OPEN) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
										$geoalert_vehicle = $row_report->door_vehicle_no;
										$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
										$geoalert_name = $row_report->door_geofence_start;
										$geoalert_status = "DOOR";
									}else{
										//jika tidak ada cari random mobil yg off di droppoint // random mobil - search level 4
										$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end");
										$this->dbreport->order_by("trip_mileage_start_time","asc");
										$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
										$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
										$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
										$this->dbreport->limit(1);
										$q_report = $this->dbreport->get($dbtable_opr);
										$row_report = $q_report->row();
										$total_row_report = count($row_report);
										
										if($total_row_report > 0){
											printf("3==GEOALERT - ADA GEOALERT LEVEL 4 (ENGINE OFF) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
											$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
											$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
											$geoalert_name = $row_report->trip_mileage_geofence_end;
											$geoalert_status = "OPR";
										}else{
											//tidak ada semuanya
											printf("3X==TIDAK ADA SEMUA GEOALERT !!!"); 
											$geoalert_vehicle = "";
											$geoalert_time = date("H:i:s", strtotime("00:00:00"));
											$geoalert_name = "TIDAK ADA SEMUA GEOALERT - RESET";
											$geoalert_status = "";
										}
									}
									
								}
								
							}
					
					$field_date = "georeport_date_".$date;
					
					//INSERT KE TABLE REPORT PER TANGGAL REPORT CRON
					unset($data);
					//opr
					$data["georeport_date_".$date] = $geoalert_time;
					$data["georeport_vehicle_".$date] = $geoalert_vehicle;
					$data["georeport_status_".$date] = $geoalert_status;
					
					//door
					/*$data["georeport_door_date_".$date] = $geoalert_door_time;
					$data["georeport_door_vehicle_".$date] = $geoalert_door_vehicle;
					$data["georeport_door_status_".$date] = $geoalert_door_status;*/
					
					printf("3==GEOALERT - UPDATE GEOREPORT : %s %s \r\n", $geoalert_name, $geoalert_door_name); 
					$this->dbreport->where("georeport_droppoint", $row_drop->droppoint_id);	
					$this->dbreport->update($dbtable,$data);
					

				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		
		return;   
		
	}
	
	function droppoint_report_new_group($date="",$month="",$year="",$parent="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		//print_r($newdatetime." ".$newdatetime2." ".$m1);exit();
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","desc");
		$this->dbtransporter->where("distrep_flag",0);
		if($parent != ""){
			$this->dbtransporter->where("distrep_parent", $parent);
		}
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","desc");
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
				
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end");
							$this->dbreport->order_by("trip_mileage_start_time","asc");
							$this->dbreport->where("trip_mileage_vehicle_id",$row_distrep->distrep_vehicle_device);
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
							$this->dbreport->limit(1);
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->row();
							$total_row_report = count($row_report);
							if($total_row_report > 0){
								printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
								$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
								$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
								$geoalert_name = $row_report->trip_mileage_geofence_end;
								$geoalert_status = "OPR";
							}else{
								//select per tanggal dari table DOOR REPORT
								//cari berdasarkan DOOR OPEN di area droppoint (ambil start_time nya) (search by door) search level 2
								$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start");
								$this->dbreport->order_by("door_start_time","asc");
								$this->dbreport->where("door_status","OPEN");
								$this->dbreport->where("door_vehicle_device",$row_distrep->distrep_vehicle_device);
								$this->dbreport->where("door_start_time >=",$newdatetime);
								$this->dbreport->where("door_start_time <=",$newdatetime2);
								$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
								$this->dbreport->limit(1);
								$q_report = $this->dbreport->get($dbtable_door);
								$row_report = $q_report->row();
								$total_row_report = count($row_report);
								if($total_row_report > 0){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 2 (DOOR OPEN) !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
									$geoalert_vehicle = $row_report->door_vehicle_no;
									$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
									$geoalert_name = $row_report->door_geofence_start;
									$geoalert_status = "DOOR";
								}else{
									
									//jika tidak ada cari random mobil yg open di droppoint // random mobil - search level 3
									$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start");
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_start_time >=",$newdatetime);
									$this->dbreport->where("door_start_time <=",$newdatetime2);
									$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
									$this->dbreport->limit(1);
									$q_report = $this->dbreport->get($dbtable_door);
									$row_report = $q_report->row();
									$total_row_report = count($row_report);
									
									if($total_row_report > 0){
										printf("3==GEOALERT - ADA GEOALERT LEVEL 3 (DOOR OPEN) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
										$geoalert_vehicle = $row_report->door_vehicle_no;
										$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
										$geoalert_name = $row_report->door_geofence_start;
										$geoalert_status = "DOOR";
									}else{
										//jika tidak ada cari random mobil yg off di droppoint // random mobil - search level 4
										$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end");
										$this->dbreport->order_by("trip_mileage_start_time","asc");
										$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
										$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
										$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
										$this->dbreport->limit(1);
										$q_report = $this->dbreport->get($dbtable_opr);
										$row_report = $q_report->row();
										$total_row_report = count($row_report);
										
										if($total_row_report > 0){
											printf("3==GEOALERT - ADA GEOALERT LEVEL 4 (ENGINE OFF) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
											$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
											$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
											$geoalert_name = $row_report->trip_mileage_geofence_end;
											$geoalert_status = "OPR";
										}else{
											//tidak ada semuanya
											printf("3X==TIDAK ADA SEMUA GEOALERT !!!"); 
											$geoalert_vehicle = "";
											$geoalert_time = date("H:i:s", strtotime("00:00:00"));
											$geoalert_name = "TIDAK ADA SEMUA GEOALERT - RESET";
											$geoalert_status = "";
										}
									}
									
								}
								
							}
					
					$field_date = "georeport_date_".$date;
					
					//INSERT KE TABLE REPORT PER TANGGAL REPORT CRON
					unset($data);
					//opr
					$data["georeport_date_".$date] = $geoalert_time;
					$data["georeport_vehicle_".$date] = $geoalert_vehicle;
					$data["georeport_status_".$date] = $geoalert_status;
					
					//door
					/*$data["georeport_door_date_".$date] = $geoalert_door_time;
					$data["georeport_door_vehicle_".$date] = $geoalert_door_vehicle;
					$data["georeport_door_status_".$date] = $geoalert_door_status;*/
					
					printf("3==GEOALERT - UPDATE GEOREPORT : %s %s \r\n", $geoalert_name, $geoalert_door_name); 
					$this->dbreport->where("georeport_droppoint", $row_drop->droppoint_id);	
					$this->dbreport->update($dbtable,$data);
					

				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		
		return;   
		
	}
	
	//khusus DC tipe 2 from OTA // to all DC
	function otl_otd_report_dc2_from_ota($date="",$month="",$year="",$base="")
	{
		$offset = 0;
		$offset_base = 0;
		$offset_dist = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "loading_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$report_ota = "inout_geofence_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		$loading_report_date = $newdate;
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			$dbtable_ota = $report_ota."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			$dbtable_ota = $report_ota."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			$dbtable_ota = $report_ota."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			$dbtable_ota = $report_ota."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			$dbtable_ota = $report_ota."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			$dbtable_ota = $report_ota."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			$dbtable_ota = $report_ota."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			$dbtable_ota = $report_ota."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			$dbtable_ota = $report_ota."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			$dbtable_ota = $report_ota."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			$dbtable_ota = $report_ota."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			$dbtable_ota = $report_ota."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select BASE 
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("base_id","desc");
		if($base != ""){
			$this->dbtransporter->where("base_id", $base);
		}
		$this->dbtransporter->where("base_cron_active",3);
		$this->dbtransporter->where("base_company",24);
		$this->dbtransporter->where("base_flag",0);
		$qd = $this->dbtransporter->get("droppoint_base");
		$rows_base = $qd->result();
		$total_base = count($rows_base);
		printf("1==BASE - TOTAL BASE : %s \r\n", $total_base); 
		
		foreach($rows_base as $row_base)
		{
			
			if (($i+1) < $offset_base)
			{
				$i++;
				continue;
			}
			
			printf("1==BASE - PROCESS NUMBER BASE : %s \r\n", ++$i." of ".$total_base);
			printf("1==BASE - PROSES DISTREP   : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			
			printf("1==BASE - GET DISTREP BY BASE : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			$this->dbtransporter->order_by("distrep_id","asc");
			$this->dbtransporter->where("distrep_otl_base", $row_base->base_geofence);
			$q_dist = $this->dbtransporter->get("droppoint_distrep");
			$rows_dist = $q_dist->result();
			$total_dist = count($rows_dist);
			printf("1==BASE - TOTAL DISTREP : %s \r\n", $total_dist);
			printf("=============================================== \r\n");
			$j=0;
				
			foreach($rows_dist as $row_dist)
			{
				
				if (($j+1) < $offset_dist)
				{
					$j++;
					continue;
				}
				
				printf("2==DISTREP - PROCESS NUMBER DISTREP : %s %s %s \r\n", ++$j." of ".$total_dist , "distrep: ". $i." of ".$total_base, $newdate);
				printf("2==DISTREP - PROSES DISTREP : %s %s \r\n", $row_dist->distrep_id, $row_dist->distrep_name); 
					
					//default
					$total_row_report_otl = 0;
					$total_row_report_otd = 0;
					
					$loading_vehicle_id = "";
					$loading_vehicle_device = "";
					$loading_vehicle_no = "";
					$loading_vehicle_name = "";
					$loading_vehicle_type = "";
					
					$ota_vehicle_id = "";
					$ota_vehicle_valid = "";
					$ota_vehicle_no = "";
					$ota_vehicle_name = "";
					$ota_vehicle_type = "";
				
					$loading_type = "";
					$loading_plant = 0;
					$loading_company_custom = 0;
					$loading_report_base = "";
					$loading_otl_base = "";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_otl_start_date = "";
					$loading_otl_start_time = "";
					$loading_otl_end_date = "";
					$loading_otl_end_time = "";
					$loading_otl_location_start = "";
					$loading_otl_location_end = "";
					$loading_otl_coordinate_start = "";
					$loading_otl_coordinate_end = "";
					$loading_otl_geofence_start = "";
					$loading_otl_geofence_end = "";
					$loading_otl_duration = "";
					$loading_otl_duration_sec = "";
					$loading_otl_report_status = "-";
					
					$loading_otd_base = "";
					$loading_otd_distrep = 0;
					$loading_otd_distrep_name = 0;
					$loading_otd_start_date = "";
					$loading_otd_start_time = "";
					$loading_otd_end_date = "";
					$loading_otd_end_time = "";
					$loading_otd_location_start = "";
					$loading_otd_location_end = "";
					$loading_otd_coordinate_start = "";
					$loading_otd_coordinate_end = "";
					$loading_otd_geofence_start = "";
					$loading_otd_geofence_end = "";
					$loading_otd_duration = "";
					$loading_otd_duration_sec = "";
					$loading_otd_km_start = "";
					$loading_otd_km_end = "";
					$loading_otd_unloading_time = "";
					$loading_otd_report_status = "-";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_distance = 0;
					$loading_distance_count = 0;
				
					$loading_report_base = $row_base->base_geofence;
					$loading_type = $row_base->base_type;
					$loading_plant = $row_base->base_plant;
					
					$ota_nopol = 0;
					$ota_nopol_total = 0;
					
					$this->dbtransporter->order_by("parent_id","desc");
					$this->dbtransporter->select("parent_company");
					$this->dbtransporter->where("parent_id",$row_dist->distrep_parent);
					$this->dbtransporter->where("parent_flag",0);
					$q_parent = $this->dbtransporter->get("droppoint_parent");
					$row_parent = $q_parent->row();
					$total_parent = count($row_parent);
					if($total_parent > 0){
						$loading_company_custom = $row_parent->parent_company;
					}
					
					//tipe random (default)
					$cek_random_status = 0;
					
							//cek dari ota report nopol valid 
							//config field ota
							$field_date = "georeport_date_".$date; // date time
							$field_vehicle = "georeport_vehicle_".$date; // NOPOL
							$field_status = "georeport_status_".$date; //OPR / DOOR
							
							$field_select = $field_date.", ".$field_vehicle.", ".$field_status;
							
							//search 1 dropoint asc by name
							$this->dbtransporter->order_by("droppoint_id","asc");
							$this->dbtransporter->select("droppoint_distrep,droppoint_name,droppoint_id");
							$this->dbtransporter->where("droppoint_distrep",$row_dist->distrep_id);
							$this->dbtransporter->where("droppoint_flag",0);
							$q_master_droppoint = $this->dbtransporter->get("droppoint");
							$row_master_droppoint = $q_master_droppoint->result();
							$total_master_droppoint = count($row_master_droppoint);
							
							if($total_master_droppoint > 0){
								$ota_nopol_total = 0;
								for($d=0;$d<$total_master_droppoint; $d++){
									$master_droppoint_id = $row_master_droppoint[$d]->droppoint_id;
									//search report OTA 
									$this->dbreport->order_by("georeport_id","desc");
									$this->dbreport->select($field_select);
									$this->dbreport->where("georeport_droppoint",$master_droppoint_id);
									$q_report_ota = $this->dbreport->get($dbtable_ota);
									$row_report_ota = $q_report_ota->row();
									$total_row_report_ota = count($row_report_ota);
									
									if($total_row_report_ota > 0){
										$ota_nopol_cek = $row_report_ota->$field_vehicle;
										$ota_status = $row_report_ota->$field_status;
										$ota_date	= $row_report_ota->$field_date;
										if($ota_nopol_cek != ""){
											$ota_nopol = $ota_nopol_cek;
											//printf("2==OTA - ADA 1 DROPPOINT !! : %s ID %s \r\n", $row_master_droppoint[$d]->droppoint_name, $row_master_droppoint[$d]->droppoint_id); 
											//printf("2==OTA - ADA 1 NOPOL !! : %s ID %s \r\n", $ota_nopol, $ota_status); 
											$ota_nopol_total = $ota_nopol_total + 1;
										}
										
									}else{
										//tidak ada report OTA
										printf("2==OTA - TIDAK ADA REPORT OTA!!!! \r\n"); 
									}
								}
								
							}else{
								//tidak ada 1 droppoint pun
								printf("2==OTA - TIDAK ADA SATUPUN DROPPOINT !!!! \r\n"); 
								
							}
							printf("2==OTA - BERDASARKAN TOTAL DATA OTA : %s NOPOL : %s \r\n", $ota_nopol_total, $ota_nopol); 
							//cek valid nopol
							if($ota_nopol_total >= 3){
								$this->db->order_by("vehicle_id", "asc");
								$this->db->select("vehicle_device,vehicle_id,vehicle_name,vehicle_no,vehicle_type");
								$this->db->limit(1);
								$this->db->where("vehicle_no",$ota_nopol);
								$this->db->where("vehicle_user_id","1032");//user balrich
								$this->db->where("vehicle_status <>",3);//only aktif
								$q_vehicle_valid = $this->db->get("vehicle");
								$row_vehicle_valid = $q_vehicle_valid->row();
								$total_vehicle_valid = count($row_vehicle_valid);
								if($total_vehicle_valid > 0){
									$cek_random_status = 1;
									$ota_vehicle_valid = $row_vehicle_valid->vehicle_device;
									$ota_vehicle_name = $row_vehicle_valid->vehicle_name;
									$ota_vehicle_no = $row_vehicle_valid->vehicle_no;
									$ota_vehicle_type = $row_vehicle_valid->vehicle_type;
									$ota_vehicle_id = $row_vehicle_valid->vehicle_id;
									
									printf("2==OTA - BERDASARKAN OTA NOPOL : %s %s \r\n", $ota_nopol, $ota_vehicle_valid); 
									
									//OTL
									//select START LOADING & UNLOADING (all type)
									//dari report DOOR Status, status OPEN di geofence BASE lokasi START dan END, limit 1 order by START TIME desc (per nopol) -- Search Level 2
									$this->dbreport->order_by("door_start_time","desc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("door_vehicle_device",$row_dist->distrep_vehicle_device);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end",$row_base->base_geofence);
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",10); //open lebih dari 10 detik
									$q_report_otl = $this->dbreport->get($dbtable_door);
									$row_report_otl = $q_report_otl->row();
									$total_row_report_otl = count($row_report_otl);
											
									//jika ada ada alert report OTL (dari DOOR)
									if($total_row_report_otl > 0){
										printf("3==OTL - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $row_report_otl->door_vehicle_no, $row_report_otl->door_start_time); 
										
										$loading_vehicle_id = $row_report_otl->door_vehicle_id;
										$loading_vehicle_device = $row_report_otl->door_vehicle_device;
										$loading_vehicle_no = $row_report_otl->door_vehicle_no;
										$loading_vehicle_name = $row_report_otl->door_vehicle_name;
										$loading_vehicle_type = $row_report_otl->door_vehicle_type;
										
										$loading_otl_base = $row_report_otl->door_geofence_start;
										$loading_otl_start_date = date("Y-m-d", strtotime($row_report_otl->door_start_time));
										$loading_otl_start_time = date("H:i:s", strtotime($row_report_otl->door_start_time));
										$loading_otl_end_date = date("Y-m-d", strtotime($row_report_otl->door_end_time));
										$loading_otl_end_time = date("H:i:s", strtotime($row_report_otl->door_end_time));
										$loading_otl_location_start = $row_report_otl->door_location_start;
										$loading_otl_location_end = $row_report_otl->door_location_end;
										$loading_otl_coordinate_start = $row_report_otl->door_coordinate_start;
										$loading_otl_coordinate_end = $row_report_otl->door_coordinate_end;
										$loading_otl_geofence_start = $row_report_otl->door_geofence_start;
										$loading_otl_geofence_end = $row_report_otl->door_geofence_end;
										$loading_otl_duration =  $row_report_otl->door_duration;
										$loading_otl_duration_sec =  $row_report_otl->door_duration_sec;
										$loading_otl_report_status = "DOOR";
										
										if($row_base->base_type == "POOL"){
											//search actual OTL POOL (arrival)
											//all status engine yag geofence END nya di nama Base
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$row_dist->distrep_vehicle_device);
											$this->dbreport->where("trip_mileage_geofence_end",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
											$q_report_otl_actual = $this->dbreport->get($dbtable_opr);
											$row_report_otl_actual = $q_report_otl_actual->row();
											$total_row_report_otl_actual = count($row_report_otl_actual);
											if($total_row_report_otl_actual > 0){
												$loading_otl_arrival_date = date("Y-m-d", strtotime($row_report_otl_actual->trip_mileage_end_time));
												$loading_otl_arrival_time = date("H:i:s", strtotime($row_report_otl_actual->trip_mileage_end_time));
												printf("3!==OTL (POOL) - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
											}
										}
										//jika DC atau Tipe lain sama dengan start loading
										else
										{ 
											$loading_otl_arrival_date = $loading_otl_start_date;
											$loading_otl_arrival_time = $loading_otl_start_time;
											printf("3==OTL (DC) - SAMA DENGAN REPORT DOOR !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
										}
									
									}
									
									//OTD
									//select OTD Start End time  //berdasarkan nopol yg dapat dari OTL
									$mobil_distance = 0;
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di mana saja yg penting bukan GEOFENCE DROPPOINT, 
									//limit 1 order by START TIME asc
									$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("trip_mileage_vehicle_id",$loading_vehicle_device);
									$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd_time = $this->dbreport->get($dbtable_opr);
									$row_report_otd_time = $q_report_otd_time->row();
									$total_row_report_otd_time = count($row_report_otd_time);		
									
									if($total_row_report_otd_time > 0){
										
										$loading_otd_start_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_start_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_end_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_end_time));
										$loading_otd_end_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_end_time));
										
									}
							
									//search droppoint ID
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di GEOFENCE DROPPOINT, limit 1 order by START TIME asc
									//dirubah ke Report Door Open di toko
									
									/*$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("trip_mileage_vehicle_id",$loading_vehicle_device);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=","0");
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd = $this->dbreport->get($dbtable_opr);
									*/
									
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_start !=","0");
									$this->dbreport->where("door_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end !=","0");
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",3); //open lebih dari 3 detik
									$q_report_otd = $this->dbreport->get($dbtable_door);
									
									$row_report_otd = $q_report_otd->row();
									$total_row_report_otd = count($row_report_otd);		
									
									//jika ada alert report OTD
									if($total_row_report_otd > 0){
										printf("4==OTD - ADA REPORT OTD level 1 - DOOR !!!! : %s %s %s \r\n", $row_report_otd->door_vehicle_no,$row_report_otd->door_vehicle_device,$row_report_otd->door_start_time); 
										
										/*$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
										$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
										$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
										$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
										$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
										$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "OPR";
										$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;*/
										
										$loading_otd_base = $row_report_otd->door_geofence_start;
										$loading_otd_location_start = $row_report_otd->door_location_start;
										$loading_otd_location_end = $row_report_otd->door_location_end;
										$loading_otd_coordinate_start = $row_report_otd->door_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->door_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->door_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->door_geofence_end;
										$loading_otd_duration =  $row_report_otd->door_duration;
										$loading_otd_duration_sec =  $row_report_otd->door_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "DOOR";
										$mobil_distance = $row_report_otd->door_vehicle_device;
										
									}
									else
									{
											//search level 2 //cari semua geofence report operasional
											//select OTD report yaitu (tipe DC) 
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
											$this->dbreport->where("trip_mileage_geofence_start !=",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_geofence_end !=","0");
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
											$q_report_otd = $this->dbreport->get($dbtable_opr);
											$row_report_otd = $q_report_otd->row();
											$total_row_report_otd = count($row_report_otd);
											//	print_r($total_row_report_otd." "." 1"." ".$loading_vehicle_device);exit();
											
											if($total_row_report_otd > 0){
												printf("4==OTD - ADA REPORT OTD level 2 - OPR !!!! : %s %s %s \r\n", $row_report_otd->trip_mileage_vehicle_no,$row_report_otd->trip_mileage_vehicle_id,$row_report_otd->trip_mileage_start_time); 
												$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
												
												$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
												$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
												$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
												$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
												$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
												$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
												$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
												$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
												$loading_otd_distrep = 0;
												$loading_otd_distrep_name = "-";
												$loading_otd_report_status = "OPR";
												$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;
												
												
											}else{
												printf("4X==OTD - TIDAK ADA OTD !!!! : \r\n"); 
											}
										
									}
									printf("4==OTD - GEOFENCE NAME !!!! : %s \r\n", $loading_otd_geofence_end);
									if($loading_otd_geofence_end != ""){
										//select distrep berdasarkan geofence END 
										$this->dbtransporter->order_by("droppoint_id","desc");
										$this->dbtransporter->select("droppoint_distrep,distrep_name,company_plant,distrep_otl_base");
										$this->dbtransporter->where("droppoint_flag",0);
										$this->dbtransporter->where("droppoint_geofence",$loading_otd_geofence_end);
										$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
										$this->dbtransporter->join("droppoint_parent", "distrep_parent = parent_id", "left");
										$this->dbtransporter->join("droppoint_company_custom", "parent_company = company_id", "left");
										$q_droppoint = $this->dbtransporter->get("droppoint");
										$row_droppoint = $q_droppoint->row();
										$total_droppoint = count($row_droppoint);
										if($total_droppoint > 0){
											printf("4==OTD - ADA ID DISTREP !!!! : %s \r\n", $row_droppoint->droppoint_distrep); 
											$loading_otd_distrep = $row_droppoint->droppoint_distrep;
											$loading_plant = $row_droppoint->company_plant;
											$loading_otd_distrep_name = $row_droppoint->distrep_name;
										}
									}
									$loading_distance = 0;
									$loading_distance_count = 0;
									
									if($mobil_distance){
										//search distance by GPS //berdasarkan nopol dari OTL  //nilai km terakhir
										$this->dbreport->order_by("trip_mileage_id","asc");
										$this->dbreport->select("trip_mileage_trip_mileage");
										$this->dbreport->where("trip_mileage_vehicle_id",$mobil_distance);
										$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
										$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
										$q_report_distance = $this->dbreport->get($dbtable_opr);
										$row_report_distance = $q_report_distance->result();
										$total_row_report_distance = count($row_report_distance);
										if($total_row_report_distance > 0){
											for($t=0;$t<$total_row_report_distance; $t++){
												$loading_distance_count = $loading_distance_count + $row_report_distance[$t]->trip_mileage_trip_mileage;
											}
										}
										
									}
									
										//$loading_distance = floatval($loading_distance_count);
										$loading_distance = $loading_distance_count;
										printf("4==DISTANCE %s at %s %s \r\n", $loading_distance,
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd)),
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd)) ); 
									
									
								}
								
								
								unset($data);
								if($total_row_report_otl > 0){
									$data["loading_vehicle_id"] = $loading_vehicle_id;
									$data["loading_vehicle_device"] = $loading_vehicle_device;
									$data["loading_vehicle_no"] = $loading_vehicle_no;
									$data["loading_vehicle_name"] = $loading_vehicle_name;
									$data["loading_vehicle_type"] = $loading_vehicle_type;
								}else{
									$data["loading_vehicle_id"] = $ota_vehicle_id;
									$data["loading_vehicle_device"] = $ota_vehicle_valid;
									$data["loading_vehicle_no"] = $ota_vehicle_no;
									$data["loading_vehicle_name"] = $ota_vehicle_name;
									$data["loading_vehicle_type"] = $ota_vehicle_type;
								}
								
								$data["loading_type"] = $loading_type;
								$data["loading_report_base"] = $loading_report_base;
								$data["loading_report_date"] = $loading_report_date;
								$data["loading_plant"] = $loading_plant;
								$data["loading_company_custom"] = $loading_company_custom;
								
								$data["loading_otl_base"] = $loading_otl_base;
								$data["loading_otl_start_date"] = $loading_otl_start_date;
								$data["loading_otl_start_time"] = $loading_otl_start_time;
								$data["loading_otl_end_date"] = $loading_otl_end_date;
								$data["loading_otl_end_time"] = $loading_otl_end_time;
								$data["loading_otl_location_start"] = $loading_otl_location_start;
								$data["loading_otl_location_end"] = $loading_otl_location_end;
								$data["loading_otl_coordinate_start"] = $loading_otl_coordinate_start;
								$data["loading_otl_coordinate_end"] = $loading_otl_coordinate_end;
								$data["loading_otl_geofence_start"] = $loading_otl_geofence_start;
								$data["loading_otl_geofence_end"] = $loading_otl_geofence_end;
								$data["loading_otl_duration"] = $loading_otl_duration;
								$data["loading_otl_duration_sec"] = $loading_otl_duration_sec;
								$data["loading_otl_report_status"] = $loading_otl_report_status;
								
								$data["loading_otd_base"] = $loading_otd_base;
								$data["loading_otd_distrep"] = $loading_otd_distrep;
								$data["loading_otd_distrep_name"] = $loading_otd_distrep_name;
								$data["loading_otd_start_date"] = $loading_otd_start_date;
								$data["loading_otd_start_time"] = $loading_otd_start_time;
								$data["loading_otd_end_date"] = $loading_otd_end_date;
								$data["loading_otd_end_time"] = $loading_otd_end_time;
								$data["loading_otd_location_start"] = $loading_otd_location_start;
								$data["loading_otd_location_end"] = $loading_otd_location_end;
								$data["loading_otd_coordinate_start"] = $loading_otd_coordinate_start;
								$data["loading_otd_coordinate_end"] = $loading_otd_coordinate_end;
								$data["loading_otd_geofence_start"] = $loading_otd_geofence_start;
								$data["loading_otd_geofence_end"] = $loading_otd_geofence_end;
								$data["loading_otd_duration"] = $loading_otd_duration;
								$data["loading_otd_duration_sec"] = $loading_otd_duration_sec;
								$data["loading_otd_km_start"] = $loading_otd_km_start;
								$data["loading_otd_km_end"] = $loading_otd_km_end;
								$data["loading_otd_report_status"] = $loading_otd_report_status;
								$data["loading_otl_arrival_date"] = $loading_otl_arrival_date;
								$data["loading_otl_arrival_time"] = $loading_otl_arrival_time;
								$data["loading_distance"] = $loading_distance;
								
								//SELECT REPORT DI TANGGAL REPORT CRON
								$this->dbreport->limit(1);
								$this->dbreport->where("loading_vehicle_device",$ota_vehicle_valid);
								$this->dbreport->where("loading_report_base", $loading_report_base);
								$this->dbreport->where("loading_report_date",$loading_report_date);
								$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);
								$q_report_loading = $this->dbreport->get($dbtable);
								$row_report_loading = $q_report_loading->row();
								$total_row_report_loading = count($row_report_loading);
								
								if($total_row_report_loading > 0){
									//UPDATE JIKA SUDAH DA DATA MOBIL DI TANGGAL REPORT CRON
									printf("5==DATABASE - UPDATE REPORT OTL & OTD : %s %s \r\n", $loading_vehicle_device, $loading_report_date); 
									$this->dbreport->where("loading_vehicle_device", $ota_vehicle_valid);
									$this->dbreport->where("loading_report_base", $loading_report_base);
									$this->dbreport->where("loading_report_date", $loading_report_date);
									//$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);
										
									//CEK JIKA DISTREP DILUAR AREA BASE MAKA TIDAK DIINSERT
									if($row_base->base_geofence == $row_droppoint->distrep_otl_base){
										//INSERT JIKA BELUM ADA DATA MOBIL DI TANGGAL REPORT CRON
										printf("5==DATABASE - UPDATE REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date);
										$this->dbreport->update($dbtable,$data);
									}else{
										printf("5X==DATABASE - INVALID BASE SKIP UPDATE : %s %s \r\n", $ota_nopol, $loading_report_date);
									}									
									
								}else{
									if(($total_row_report_otl > 0) || ($total_row_report_otd > 0)){
										if($loading_vehicle_device != ""){
											//ADA DOOR OPEN DI BASE
											printf("5==DATABASE - KENDARAAN ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date);
										}else{
											//TIDAK ADA DOOR OPEN ( TIDAK ADA OTL )
											printf("5==DATABASE - KENDARAN TIDAK ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date); 
										}
										
										//CEK JIKA DISTREP DILUAR AREA BASE MAKA TIDAK DIINSERT
										if($row_base->base_geofence == $row_droppoint->distrep_otl_base){
											//INSERT JIKA BELUM ADA DATA MOBIL DI TANGGAL REPORT CRON
											printf("5==DATABASE - INSERT REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date);
											$this->dbreport->insert($dbtable,$data);
										}else{
											printf("5X==DATABASE - INVALID BASE SKIP INSERT \r\n");
										}
										
									}else{
										//TIDAK ADA DATA REPORT OTL & OTD
										printf("5X==SKIP - TIDAK ADA REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date); 
									}
									
								}
								
							}else{
								$ota_vehicle_valid = "0";
								printf("2==OTA - TIDAK ADA NOPOL VALID  \r\n"); 
							}
						
				printf("=============================================== \r\n");
				
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - OTL OTD DC TIPE 2 FROM OTA";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_dist."
End Distrep   : "."( ".$i." / ".$total_dist." )"."

Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Distrep: ".$total_dist." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		
	}
	
	//khusus POOl from OTA
	function otl_otd_report_pool_from_ota($date="",$month="",$year="",$base="",$distrep="")
	{
		$offset = 0;
		$offset_base = 0;
		$offset_dist = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "loading_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$report_ota = "inout_geofence_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		$loading_report_date = $newdate;
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			$dbtable_ota = $report_ota."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			$dbtable_ota = $report_ota."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			$dbtable_ota = $report_ota."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			$dbtable_ota = $report_ota."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			$dbtable_ota = $report_ota."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			$dbtable_ota = $report_ota."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			$dbtable_ota = $report_ota."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			$dbtable_ota = $report_ota."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			$dbtable_ota = $report_ota."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			$dbtable_ota = $report_ota."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			$dbtable_ota = $report_ota."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			$dbtable_ota = $report_ota."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select BASE 
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("base_id","asc");
		if($base != ""){
			$this->dbtransporter->where("base_id", $base);
		}
		$this->dbtransporter->where("base_cron_active",2);
		$this->dbtransporter->where("base_company",24); //balrich
		$this->dbtransporter->where("base_flag",0);
		$qd = $this->dbtransporter->get("droppoint_base");
		$rows_base = $qd->result();
		$total_base = count($rows_base);
		printf("1==BASE - TOTAL BASE : %s \r\n", $total_base); 
		
		foreach($rows_base as $row_base)
		{
			
			if (($i+1) < $offset_base)
			{
				$i++;
				continue;
			}
			
			printf("1==BASE - PROCESS NUMBER BASE : %s \r\n", ++$i." of ".$total_base);
			printf("1==BASE - PROSES DISTREP   : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			
			printf("1==BASE - GET DISTREP BY BASE : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			$this->dbtransporter->order_by("distrep_id","asc");
			$this->dbtransporter->where("distrep_otl_base", $row_base->base_geofence);
			if($distrep != ""){
				$this->dbtransporter->where("distrep_id", $distrep);
			}
			$q_dist = $this->dbtransporter->get("droppoint_distrep");
			$rows_dist = $q_dist->result();
			$total_dist = count($rows_dist);
			printf("1==BASE - TOTAL DISTREP : %s \r\n", $total_dist);
			printf("=============================================== \r\n");
			$j=0;
				
			foreach($rows_dist as $row_dist)
			{
				
				if (($j+1) < $offset_dist)
				{
					$j++;
					continue;
				}
				
				printf("2==DISTREP - PROCESS NUMBER DISTREP : %s %s %s \r\n", ++$j." of ".$total_dist , "distrep: ". $i." of ".$total_base, $newdate);
				printf("2==DISTREP - PROSES DISTREP : %s %s \r\n", $row_dist->distrep_id, $row_dist->distrep_name); 
					
					//default
					$total_row_report_otl = 0;
					$total_row_report_otd = 0;
					
					$loading_vehicle_id = "";
					$loading_vehicle_device = "";
					$loading_vehicle_no = "";
					$loading_vehicle_name = "";
					$loading_vehicle_type = "";
					
					$ota_vehicle_id = "";
					$ota_vehicle_valid = "";
					$ota_vehicle_no = "";
					$ota_vehicle_name = "";
					$ota_vehicle_type = "";
				
					$loading_type = "";
					$loading_plant = 0;
					$loading_company_custom = 0;
					$loading_report_base = "";
					$loading_otl_base = "";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_otl_start_date = "";
					$loading_otl_start_time = "";
					$loading_otl_end_date = "";
					$loading_otl_end_time = "";
					$loading_otl_location_start = "";
					$loading_otl_location_end = "";
					$loading_otl_coordinate_start = "";
					$loading_otl_coordinate_end = "";
					$loading_otl_geofence_start = "";
					$loading_otl_geofence_end = "";
					$loading_otl_duration = "";
					$loading_otl_duration_sec = "";
					$loading_otl_report_status = "-";
					
					$loading_otd_base = "";
					$loading_otd_distrep = 0;
					$loading_otd_distrep_name = 0;
					$loading_otd_start_date = "";
					$loading_otd_start_time = "";
					$loading_otd_end_date = "";
					$loading_otd_end_time = "";
					$loading_otd_location_start = "";
					$loading_otd_location_end = "";
					$loading_otd_coordinate_start = "";
					$loading_otd_coordinate_end = "";
					$loading_otd_geofence_start = "";
					$loading_otd_geofence_end = "";
					$loading_otd_duration = "";
					$loading_otd_duration_sec = "";
					$loading_otd_km_start = "";
					$loading_otd_km_end = "";
					$loading_otd_unloading_time = "";
					$loading_otd_report_status = "-";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_distance = 0;
					$loading_distance_count = 0;
				
					$loading_report_base = $row_base->base_geofence;
					$loading_type = $row_base->base_type;
					$loading_plant = $row_base->base_plant;
					
					$ota_nopol = 0;
					$ota_nopol_total = 0;
					
					$this->dbtransporter->order_by("parent_id","desc");
					$this->dbtransporter->select("parent_company");
					$this->dbtransporter->where("parent_id",$row_dist->distrep_parent);
					$this->dbtransporter->where("parent_flag",0);
					$q_parent = $this->dbtransporter->get("droppoint_parent");
					$row_parent = $q_parent->row();
					$total_parent = count($row_parent);
					if($total_parent > 0){
						$loading_company_custom = $row_parent->parent_company;
					}
					
					//tipe random (default)
					$cek_random_status = 0;
					
							//cek dari ota report nopol valid 
							//config field ota
							$field_date = "georeport_date_".$date; // date time
							$field_vehicle = "georeport_vehicle_".$date; // NOPOL
							$field_status = "georeport_status_".$date; //OPR / DOOR
							
							$field_select = $field_date.", ".$field_vehicle.", ".$field_status;
							
							//search 1 dropoint asc by name
							$this->dbtransporter->order_by("droppoint_id","desc");//khusus gt cikande tes all
							$this->dbtransporter->select("droppoint_distrep,droppoint_name,droppoint_id");
							$this->dbtransporter->where("droppoint_distrep",$row_dist->distrep_id);
							$this->dbtransporter->where("droppoint_flag",0);
							$q_master_droppoint = $this->dbtransporter->get("droppoint");
							$row_master_droppoint = $q_master_droppoint->result();
							$total_master_droppoint = count($row_master_droppoint);
							
							if($total_master_droppoint > 0){
								$ota_nopol_total = 0;
								for($d=0;$d<$total_master_droppoint; $d++){
									$master_droppoint_id = $row_master_droppoint[$d]->droppoint_id;
									//search report OTA 
									$this->dbreport->order_by("georeport_id","desc");
									$this->dbreport->select($field_select);
									$this->dbreport->where("georeport_droppoint",$master_droppoint_id);
									$q_report_ota = $this->dbreport->get($dbtable_ota);
									$row_report_ota = $q_report_ota->row();
									$total_row_report_ota = count($row_report_ota);
									
									if($total_row_report_ota > 0){
										$ota_nopol_cek = $row_report_ota->$field_vehicle;
										$ota_status = $row_report_ota->$field_status;
										$ota_date	= $row_report_ota->$field_date;
										if($ota_nopol_cek != ""){
											$ota_nopol = $ota_nopol_cek;
											//printf("2==OTA - ADA 1 DROPPOINT !! : %s ID %s \r\n", $row_master_droppoint[$d]->droppoint_name, $row_master_droppoint[$d]->droppoint_id); 
											//printf("2==OTA - ADA 1 NOPOL !! : %s ID %s \r\n", $ota_nopol, $ota_status); 
											$ota_nopol_total = $ota_nopol_total + 1;
										}
										
									}else{
										//tidak ada report OTA
										printf("2==OTA - TIDAK ADA REPORT OTA!!!! \r\n");
									}
								}
								
							}else{
								//tidak ada 1 droppoint pun
								printf("2==OTA - TIDAK ADA SATUPUN DROPPOINT !!!! \r\n"); 
								
							}
							printf("2==OTA - BERDASARKAN TOTAL DATA OTA : %s NOPOL : %s \r\n", $ota_nopol_total, $ota_nopol); 
							//cek valid nopol
							if($ota_nopol_total >= $row_dist->distrep_nopol_valid){
								$this->db->order_by("vehicle_id", "asc");
								$this->db->select("vehicle_device,vehicle_id,vehicle_name,vehicle_no,vehicle_type");
								$this->db->limit(1);
								$this->db->where("vehicle_no",$ota_nopol);
								$this->db->where("vehicle_user_id","1032");//user balrich
								$this->db->where("vehicle_status <>",3);//only aktif
								$q_vehicle_valid = $this->db->get("vehicle");
								$row_vehicle_valid = $q_vehicle_valid->row();
								$total_vehicle_valid = count($row_vehicle_valid);
								printf("2==OTA - TOTAL VALID NOPOL : %s \r\n", $total_vehicle_valid);
								if($total_vehicle_valid > 0){
									$cek_random_status = 1;
									$ota_vehicle_valid = $row_vehicle_valid->vehicle_device;
									$ota_vehicle_name = $row_vehicle_valid->vehicle_name;
									$ota_vehicle_no = $row_vehicle_valid->vehicle_no;
									$ota_vehicle_type = $row_vehicle_valid->vehicle_type;
									$ota_vehicle_id = $row_vehicle_valid->vehicle_id;
									
									printf("2==OTA - BERDASARKAN DATA OTA NOPOL : %s %s \r\n", $ota_nopol, $ota_vehicle_valid); 
									
									//OTL
									//select START LOADING & UNLOADING (all type)
									//dari report DOOR Status, status OPEN di geofence BASE lokasi START dan END, limit 1 order by START TIME desc (per nopol) -- Search Level 2
									$this->dbreport->order_by("door_start_time","desc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("door_vehicle_device",$row_dist->distrep_vehicle_device);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end",$row_base->base_geofence);
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",10); //open lebih dari 10 detik
									$q_report_otl = $this->dbreport->get($dbtable_door);
									$row_report_otl = $q_report_otl->row();
									$total_row_report_otl = count($row_report_otl);
											
									//jika ada ada alert report OTL (dari DOOR)
									if($total_row_report_otl > 0){
										printf("3==OTL - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $row_report_otl->door_vehicle_no, $row_report_otl->door_start_time); 
										
										$loading_vehicle_id = $row_report_otl->door_vehicle_id;
										$loading_vehicle_device = $row_report_otl->door_vehicle_device;
										$loading_vehicle_no = $row_report_otl->door_vehicle_no;
										$loading_vehicle_name = $row_report_otl->door_vehicle_name;
										$loading_vehicle_type = $row_report_otl->door_vehicle_type;
										
										$loading_otl_base = $row_report_otl->door_geofence_start;
										$loading_otl_start_date = date("Y-m-d", strtotime($row_report_otl->door_start_time));
										$loading_otl_start_time = date("H:i:s", strtotime($row_report_otl->door_start_time));
										$loading_otl_end_date = date("Y-m-d", strtotime($row_report_otl->door_end_time));
										$loading_otl_end_time = date("H:i:s", strtotime($row_report_otl->door_end_time));
										$loading_otl_location_start = $row_report_otl->door_location_start;
										$loading_otl_location_end = $row_report_otl->door_location_end;
										$loading_otl_coordinate_start = $row_report_otl->door_coordinate_start;
										$loading_otl_coordinate_end = $row_report_otl->door_coordinate_end;
										$loading_otl_geofence_start = $row_report_otl->door_geofence_start;
										$loading_otl_geofence_end = $row_report_otl->door_geofence_end;
										$loading_otl_duration =  $row_report_otl->door_duration;
										$loading_otl_duration_sec =  $row_report_otl->door_duration_sec;
										$loading_otl_report_status = "DOOR";
										
										if($row_base->base_type == "POOL"){
											//search actual OTL POOL (arrival)
											//all status engine yag geofence END nya di nama Base
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
											$this->dbreport->where("trip_mileage_geofence_end",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
											$q_report_otl_actual = $this->dbreport->get($dbtable_opr);
											$row_report_otl_actual = $q_report_otl_actual->row();
											$total_row_report_otl_actual = count($row_report_otl_actual);
											if($total_row_report_otl_actual > 0){
												
												$loading_otl_arrival_date = date("Y-m-d", strtotime($row_report_otl_actual->trip_mileage_end_time));
												$loading_otl_arrival_time = date("H:i:s", strtotime($row_report_otl_actual->trip_mileage_end_time));
												printf("3!==OTL (POOL) LEVEL 1 - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
											}
											
											//jika tidak dapat otd (OPR) sampai di lokasi BASE maka ambil dari data door sebelumnya
											if($loading_otl_arrival_time == ""){
												$loading_otl_arrival_date = date("Y-m-d", strtotime($row_report_otl->door_start_time));
												$loading_otl_arrival_time = date("H:i:s", strtotime($row_report_otl->door_start_time));
												printf("3!==OTL (POOL) LEVEL 2 - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time);
											}
										}
										
										//jika DC atau Tipe lain sama dengan start loading
										else
										{ 
											
											$loading_otl_arrival_date = $loading_otl_start_date;
											$loading_otl_arrival_time = $loading_otl_start_time;
											printf("3==OTL (DC) - SAMA DENGAN REPORT DOOR !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
										}
										
										
										
									}
									//actial OTL diambil dari OPR (door tidak kena / tidak fungsi)
									else
									{
										$this->dbreport->order_by("trip_mileage_start_time","asc");
										$this->dbreport->limit(1);
										$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
										$this->dbreport->where("trip_mileage_geofence_end",$row_base->base_geofence);
										$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
										$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
										$q_report_otl_actual_lev3 = $this->dbreport->get($dbtable_opr);
										$row_report_otl_actual_lev3 = $q_report_otl_actual_lev3->row();
										$total_row_report_otl_actual_lev3 = count($row_report_otl_actual_lev3);
										if($total_row_report_otl_actual_lev3 > 0){
												
											$loading_otl_base = $row_report_otl_actual_lev3->trip_mileage_geofence_end;
											$loading_otl_arrival_date = date("Y-m-d", strtotime($row_report_otl_actual_lev3->trip_mileage_end_time));
											$loading_otl_arrival_time = date("H:i:s", strtotime($row_report_otl_actual_lev3->trip_mileage_end_time));
											printf("3!==OTL (POOL) LEVEL 3 - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
										}
											
									}	
								
									//OTD
									//select OTD Start End time  //berdasarkan nopol yg dapat dari OTL
									$mobil_distance = 0;
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di mana saja yg penting bukan GEOFENCE DROPPOINT, 
									//limit 1 order by START TIME asc
									$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence_pool);
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd_time = $this->dbreport->get($dbtable_opr);
									$row_report_otd_time = $q_report_otd_time->row();
									$total_row_report_otd_time = count($row_report_otd_time);		
									
									if($total_row_report_otd_time > 0){
										printf("4==OTD - ADA REPORT OTD level Time 1 - OPR !!!! : %s %s \r\n", $row_report_otd_time->trip_mileage_vehicle_no,$row_report_otd_time->trip_mileage_start_time); 
										$loading_otd_start_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_start_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_end_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_end_time));
										$loading_otd_end_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_end_time));
										
									}
									else
									{
										//khusus kondisi keluar pool / base berhenti di geofence atau random tempat
										$this->dbreport->order_by("trip_mileage_start_time","asc");
										$this->dbreport->limit(1);
										$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
										//$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
										$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
										$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence_pool);
										$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
										$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
										$q_report_otd_time2 = $this->dbreport->get($dbtable_opr);
										$row_report_otd_time2 = $q_report_otd_time2->row();
										$total_row_report_otd_time2 = count($row_report_otd_time2);		
										if($total_row_report_otd_time2 > 0){
											printf("4==OTD - ADA REPORT OTD level Time 2 - OPR !!!! : %s %s \r\n", $row_report_otd_time2->trip_mileage_vehicle_no,$row_report_otd_time2->trip_mileage_start_time); 
											$loading_otd_start_date = date("Y-m-d", strtotime($row_report_otd_time2->trip_mileage_start_time));
											$loading_otd_start_time = date("H:i:s", strtotime($row_report_otd_time2->trip_mileage_start_time));
											$loading_otd_end_date = date("Y-m-d", strtotime($row_report_otd_time2->trip_mileage_end_time));
											$loading_otd_end_time = date("H:i:s", strtotime($row_report_otd_time2->trip_mileage_end_time));
										}
										
									
									}
									
									//search droppoint ID
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di GEOFENCE DROPPOINT, limit 1 order by START TIME asc
									//dirubah ke Report Door Open di toko
									
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_start !=",$row_base->base_geofence_pool);
									$this->dbreport->where("door_geofence_start !=","0");
									$this->dbreport->where("door_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end !=",$row_base->base_geofence_pool);
									$this->dbreport->where("door_geofence_end !=","0");
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$this->dbreport->where("door_duration_sec >",3); //open lebih dari 3 detik
									$q_report_otd = $this->dbreport->get($dbtable_door);
									
									$row_report_otd = $q_report_otd->row();
									$total_row_report_otd = count($row_report_otd);		
									
									//jika ada alert report OTD
									if($total_row_report_otd > 0){
										printf("4==OTD - ADA REPORT OTD level 1 - DOOR !!!! : %s %s %s \r\n", $row_report_otd->door_vehicle_no,$row_report_otd->door_vehicle_device,$row_report_otd->door_start_time); 
										
										$loading_otd_base = $row_report_otd->door_geofence_start;
										$loading_otd_location_start = $row_report_otd->door_location_start;
										$loading_otd_location_end = $row_report_otd->door_location_end;
										$loading_otd_coordinate_start = $row_report_otd->door_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->door_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->door_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->door_geofence_end;
										$loading_otd_duration =  $row_report_otd->door_duration;
										$loading_otd_duration_sec =  $row_report_otd->door_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "DOOR";
										$mobil_distance = $row_report_otd->door_vehicle_device;
										
									}
									else
									{ 
											//search level 2 //cari semua geofence report operasional
											//select OTD report yaitu (tipe DC) 
											
											$ignore = array($row_base->base_geofence, $row_base->base_geofence_pool,"0");
											
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
											//$this->dbreport->where("trip_mileage_geofence_start !=",$row_base->base_geofence);
											//$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
											//$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence_pool);
											//$this->dbreport->where("trip_mileage_geofence_end !=","0");
											$this->dbreport->where_not_in("trip_mileage_geofence_end", $ignore);
											
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
											$q_report_otd = $this->dbreport->get($dbtable_opr);
											$row_report_otd = $q_report_otd->row();
											$total_row_report_otd = count($row_report_otd);
											//	print_r($total_row_report_otd." "." 1"." ".$loading_vehicle_device);exit();
											
											if($total_row_report_otd > 0){
												printf("4==OTD - ADA REPORT OTD level 2 - OPR !!!! : %s %s %s \r\n", $row_report_otd->trip_mileage_vehicle_no,$row_report_otd->trip_mileage_vehicle_id,$row_report_otd->trip_mileage_start_time); 
												$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
												
												$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
												$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
												$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
												$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
												$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
												$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
												$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
												$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
												$loading_otd_distrep = 0;
												$loading_otd_distrep_name = "-";
												$loading_otd_report_status = "OPR";
												$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;
												
												//jika belum ada di otd pertama
												if($total_row_report_otd_time == 0){
													$loading_otd_start_date = date("Y-m-d", strtotime($row_report_otd->trip_mileage_start_time));
													$loading_otd_start_time = date("H:i:s", strtotime($row_report_otd->trip_mileage_start_time));
													$loading_otd_end_date = date("Y-m-d", strtotime($row_report_otd->trip_mileage_end_time));
													$loading_otd_end_time = date("H:i:s", strtotime($row_report_otd->trip_mileage_end_time));
												}
												
												
											}else{
												printf("4X==OTD - TIDAK ADA OTD !!!! : \r\n"); 
											}
										
									}
									printf("4==OTD - GEOFENCE NAME !!!! : %s \r\n", $loading_otd_geofence_end);
									if($loading_otd_geofence_end != ""){
										//select distrep berdasarkan geofence END 
										$this->dbtransporter->order_by("droppoint_id","desc");
										$this->dbtransporter->select("droppoint_distrep,distrep_name,company_plant,distrep_otl_base");
										$this->dbtransporter->where("droppoint_flag",0);
										$this->dbtransporter->where("droppoint_geofence",$loading_otd_geofence_end);
										$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
										$this->dbtransporter->join("droppoint_parent", "distrep_parent = parent_id", "left");
										$this->dbtransporter->join("droppoint_company_custom", "parent_company = company_id", "left");
										$q_droppoint = $this->dbtransporter->get("droppoint");
										$row_droppoint = $q_droppoint->row();
										$total_droppoint = count($row_droppoint);
										if($total_droppoint > 0){
											printf("4==OTD - ADA ID DISTREP !!!! : %s %s \r\n", $row_droppoint->droppoint_distrep, $row_droppoint->distrep_name); 
											$loading_otd_distrep = $row_droppoint->droppoint_distrep;
											$loading_plant = $row_droppoint->company_plant;
											$loading_otd_distrep_name = $row_droppoint->distrep_name;
										}
									}
									$loading_distance = 0;
									$loading_distance_count = 0;
									
									if($mobil_distance){
										//search distance by GPS //berdasarkan nopol dari OTL  //nilai km terakhir
										$this->dbreport->order_by("trip_mileage_id","asc");
										$this->dbreport->select("trip_mileage_trip_mileage");
										$this->dbreport->where("trip_mileage_vehicle_id",$mobil_distance);
										$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
										$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
										$q_report_distance = $this->dbreport->get($dbtable_opr);
										$row_report_distance = $q_report_distance->result();
										$total_row_report_distance = count($row_report_distance);
										if($total_row_report_distance > 0){
											for($t=0;$t<$total_row_report_distance; $t++){
												$loading_distance_count = $loading_distance_count + $row_report_distance[$t]->trip_mileage_trip_mileage;
											}
										}
										
									}
									
										//$loading_distance = floatval($loading_distance_count);
										$loading_distance = $loading_distance_count;
										printf("4==DISTANCE %s at %s %s \r\n", $loading_distance,
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd)),
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd)) ); 
									
									
								}
								
								unset($data);
								if($total_row_report_otl > 0){
									$data["loading_vehicle_id"] = $loading_vehicle_id;
									$data["loading_vehicle_device"] = $loading_vehicle_device;
									$data["loading_vehicle_no"] = $loading_vehicle_no;
									$data["loading_vehicle_name"] = $loading_vehicle_name;
									$data["loading_vehicle_type"] = $loading_vehicle_type;
								}else{
									$data["loading_vehicle_id"] = $ota_vehicle_id;
									$data["loading_vehicle_device"] = $ota_vehicle_valid;
									$data["loading_vehicle_no"] = $ota_vehicle_no;
									$data["loading_vehicle_name"] = $ota_vehicle_name;
									$data["loading_vehicle_type"] = $ota_vehicle_type;
								}
								
								$data["loading_type"] = $loading_type;
								$data["loading_report_base"] = $loading_report_base;
								$data["loading_report_date"] = $loading_report_date;
								$data["loading_plant"] = $loading_plant;
								$data["loading_company_custom"] = $loading_company_custom;
								
								$data["loading_otl_base"] = $loading_otl_base;
								$data["loading_otl_start_date"] = $loading_otl_start_date;
								$data["loading_otl_start_time"] = $loading_otl_start_time;
								$data["loading_otl_end_date"] = $loading_otl_end_date;
								$data["loading_otl_end_time"] = $loading_otl_end_time;
								$data["loading_otl_location_start"] = $loading_otl_location_start;
								$data["loading_otl_location_end"] = $loading_otl_location_end;
								$data["loading_otl_coordinate_start"] = $loading_otl_coordinate_start;
								$data["loading_otl_coordinate_end"] = $loading_otl_coordinate_end;
								$data["loading_otl_geofence_start"] = $loading_otl_geofence_start;
								$data["loading_otl_geofence_end"] = $loading_otl_geofence_end;
								$data["loading_otl_duration"] = $loading_otl_duration;
								$data["loading_otl_duration_sec"] = $loading_otl_duration_sec;
								$data["loading_otl_report_status"] = $loading_otl_report_status;
								
								$data["loading_otd_base"] = $loading_otd_base;
								$data["loading_otd_distrep"] = $loading_otd_distrep;
								$data["loading_otd_distrep_name"] = $loading_otd_distrep_name;
								$data["loading_otd_start_date"] = $loading_otd_start_date;
								$data["loading_otd_start_time"] = $loading_otd_start_time;
								$data["loading_otd_end_date"] = $loading_otd_end_date;
								$data["loading_otd_end_time"] = $loading_otd_end_time;
								$data["loading_otd_location_start"] = $loading_otd_location_start;
								$data["loading_otd_location_end"] = $loading_otd_location_end;
								$data["loading_otd_coordinate_start"] = $loading_otd_coordinate_start;
								$data["loading_otd_coordinate_end"] = $loading_otd_coordinate_end;
								$data["loading_otd_geofence_start"] = $loading_otd_geofence_start;
								$data["loading_otd_geofence_end"] = $loading_otd_geofence_end;
								$data["loading_otd_duration"] = $loading_otd_duration;
								$data["loading_otd_duration_sec"] = $loading_otd_duration_sec;
								$data["loading_otd_km_start"] = $loading_otd_km_start;
								$data["loading_otd_km_end"] = $loading_otd_km_end;
								$data["loading_otd_report_status"] = $loading_otd_report_status;
								$data["loading_otl_arrival_date"] = $loading_otl_arrival_date;
								$data["loading_otl_arrival_time"] = $loading_otl_arrival_time;
								$data["loading_distance"] = $loading_distance;
								
								//SELECT REPORT DI TANGGAL REPORT CRON
								$this->dbreport->limit(1);
								$this->dbreport->where("loading_vehicle_device",$ota_vehicle_valid);
								$this->dbreport->where("loading_report_base", $loading_report_base);
								$this->dbreport->where("loading_report_date",$loading_report_date);
								$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);
								$q_report_loading = $this->dbreport->get($dbtable);
								$row_report_loading = $q_report_loading->row();
								$total_row_report_loading = count($row_report_loading);
								
								if($total_row_report_loading > 0){
									printf("5!==DATABASE - SKIP INSERT DATA SUDAH ADA : %s %s \r\n", $ota_vehicle_valid, $loading_report_date);
									//UPDATE JIKA SUDAH DA DATA MOBIL DI TANGGAL REPORT CRON
									/*printf("5==DATABASE - UPDATE REPORT OTL & OTD : %s %s \r\n", $loading_vehicle_device, $loading_report_date); 
									$this->dbreport->where("loading_vehicle_device", $ota_vehicle_valid);
									$this->dbreport->where("loading_report_base", $loading_report_base);
									$this->dbreport->where("loading_report_date", $loading_report_date);
									$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);	//new open			
									$this->dbreport->update($dbtable,$data);*/
								}else{
									if(($total_row_report_otl > 0) || ($total_row_report_otd > 0)){
										if($loading_vehicle_device != ""){
											//ADA DOOR OPEN DI BASE
											printf("5==DATABASE - KENDARAAN ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date);
										}else{
											//TIDAK ADA DOOR OPEN ( TIDAK ADA OTL )
											printf("5==DATABASE - KENDARAN TIDAK ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date); 
										}
										
										//CEK JIKA DISTREP DILUAR AREA BASE MAKA TIDAK DIINSERT
										if($row_base->base_geofence == $row_droppoint->distrep_otl_base){
											//INSERT JIKA BELUM ADA DATA MOBIL DI TANGGAL REPORT CRON
											printf("5==DATABASE - INSERT REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date);
											$this->dbreport->insert($dbtable,$data);
										}else{
											
											printf("5X==DATABASE - INVALID BASE SKIP INSERT : %s %s \r\n", $ota_nopol, $loading_report_date);
										}
										
									}else{
										//TIDAK ADA DATA REPORT OTL & OTD
										printf("5X==SKIP - TIDAK ADA REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date); 
									}
									
								}
								
							}
							else
							{
								$ota_vehicle_valid = "0";
								printf("2==OTA - TIDAK ADA NOPOL VALID  \r\n"); 
							}
					
				printf("=============================================== \r\n");
				
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - OTL OTD POOL FROM OTA";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_dist."
End Distrep   : "."( ".$i." / ".$total_dist." )"."

Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Distrep: ".$total_dist." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		
		
		return; 
		
	}
	
	//khusus POOl from OTA (others RAI KJL)
	function otl_otd_report_dc2_from_ota_others($date="",$month="",$year="",$base="")
	{
		$offset = 0;
		$offset_base = 0;
		$offset_dist = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "loading_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$report_ota = "inout_geofence_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		$loading_report_date = $newdate;
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			$dbtable_ota = $report_ota."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			$dbtable_ota = $report_ota."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			$dbtable_ota = $report_ota."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			$dbtable_ota = $report_ota."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			$dbtable_ota = $report_ota."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			$dbtable_ota = $report_ota."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			$dbtable_ota = $report_ota."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			$dbtable_ota = $report_ota."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			$dbtable_ota = $report_ota."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			$dbtable_ota = $report_ota."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			$dbtable_ota = $report_ota."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			$dbtable_ota = $report_ota."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select BASE 
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("base_id","desc");
		if($base != ""){
			$this->dbtransporter->where("base_id", $base);
		}
		$this->dbtransporter->where("base_cron_active",3);
		$this->dbtransporter->where("base_company <>",24); //others bukan balrich
		$this->dbtransporter->where("base_flag",0);
		$qd = $this->dbtransporter->get("droppoint_base");
		$rows_base = $qd->result();
		$total_base = count($rows_base);
		printf("1==BASE - TOTAL BASE : %s \r\n", $total_base); 
		
		foreach($rows_base as $row_base)
		{
			
			if (($i+1) < $offset_base)
			{
				$i++;
				continue;
			}
			
			printf("1==BASE - PROCESS NUMBER BASE : %s \r\n", ++$i." of ".$total_base);
			printf("1==BASE - PROSES DISTREP   : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			
			printf("1==BASE - GET DISTREP BY BASE : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			$this->dbtransporter->order_by("distrep_id","asc");
			$this->dbtransporter->where("distrep_otl_base", $row_base->base_geofence);
			$q_dist = $this->dbtransporter->get("droppoint_distrep");
			$rows_dist = $q_dist->result();
			$total_dist = count($rows_dist);
			printf("1==BASE - TOTAL DISTREP : %s \r\n", $total_dist);
			printf("=============================================== \r\n");
			$j=0;
				
			foreach($rows_dist as $row_dist)
			{
				
				if (($j+1) < $offset_dist)
				{
					$j++;
					continue;
				}
				
				printf("2==DISTREP - PROCESS NUMBER DISTREP : %s %s %s \r\n", ++$j." of ".$total_dist , "distrep: ". $i." of ".$total_base, $newdate);
				printf("2==DISTREP - PROSES DISTREP : %s %s \r\n", $row_dist->distrep_id, $row_dist->distrep_name); 
					
					//default
					$total_row_report_otl = 0;
					$total_row_report_otd = 0;
					
					$loading_vehicle_id = "";
					$loading_vehicle_device = "";
					$loading_vehicle_no = "";
					$loading_vehicle_name = "";
					$loading_vehicle_type = "";
					
					$ota_vehicle_id = "";
					$ota_vehicle_valid = "";
					$ota_vehicle_no = "";
					$ota_vehicle_name = "";
					$ota_vehicle_type = "";
				
					$loading_type = "";
					$loading_plant = 0;
					$loading_company_custom = 0;
					$loading_report_base = "";
					$loading_otl_base = "";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_otl_start_date = "";
					$loading_otl_start_time = "";
					$loading_otl_end_date = "";
					$loading_otl_end_time = "";
					$loading_otl_location_start = "";
					$loading_otl_location_end = "";
					$loading_otl_coordinate_start = "";
					$loading_otl_coordinate_end = "";
					$loading_otl_geofence_start = "";
					$loading_otl_geofence_end = "";
					$loading_otl_duration = "";
					$loading_otl_duration_sec = "";
					$loading_otl_report_status = "-";
					
					$loading_otd_base = "";
					$loading_otd_distrep = 0;
					$loading_otd_distrep_name = 0;
					$loading_otd_start_date = "";
					$loading_otd_start_time = "";
					$loading_otd_end_date = "";
					$loading_otd_end_time = "";
					$loading_otd_location_start = "";
					$loading_otd_location_end = "";
					$loading_otd_coordinate_start = "";
					$loading_otd_coordinate_end = "";
					$loading_otd_geofence_start = "";
					$loading_otd_geofence_end = "";
					$loading_otd_duration = "";
					$loading_otd_duration_sec = "";
					$loading_otd_km_start = "";
					$loading_otd_km_end = "";
					$loading_otd_unloading_time = "";
					$loading_otd_report_status = "-";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_distance = 0;
					$loading_distance_count = 0;
				
					$loading_report_base = $row_base->base_geofence;
					$loading_type = $row_base->base_type;
					$loading_plant = $row_base->base_plant;
					
					$ota_nopol = 0;
					$ota_nopol_total = 0;
					
					$this->dbtransporter->order_by("parent_id","desc");
					$this->dbtransporter->select("parent_company");
					$this->dbtransporter->where("parent_id",$row_dist->distrep_parent);
					$this->dbtransporter->where("parent_flag",0);
					$q_parent = $this->dbtransporter->get("droppoint_parent");
					$row_parent = $q_parent->row();
					$total_parent = count($row_parent);
					if($total_parent > 0){
						$loading_company_custom = $row_parent->parent_company;
					}
					
					//tipe random (default)
					$cek_random_status = 0;
					
							//cek dari ota report nopol valid 
							//config field ota
							$field_date = "georeport_date_".$date; // date time
							$field_vehicle = "georeport_vehicle_".$date; // NOPOL
							$field_status = "georeport_status_".$date; //OPR / DOOR
							
							$field_select = $field_date.", ".$field_vehicle.", ".$field_status;
							
							//search 1 dropoint asc by name
							$this->dbtransporter->order_by("droppoint_id","asc");
							$this->dbtransporter->select("droppoint_distrep,droppoint_name,droppoint_id");
							$this->dbtransporter->where("droppoint_distrep",$row_dist->distrep_id);
							$this->dbtransporter->where("droppoint_flag",0);
							$q_master_droppoint = $this->dbtransporter->get("droppoint");
							$row_master_droppoint = $q_master_droppoint->result();
							$total_master_droppoint = count($row_master_droppoint);
							
							if($total_master_droppoint > 0){
								$ota_nopol_total = 0;
								for($d=0;$d<$total_master_droppoint; $d++){
									$master_droppoint_id = $row_master_droppoint[$d]->droppoint_id;
									//search report OTA 
									$this->dbreport->order_by("georeport_id","desc");
									$this->dbreport->select($field_select);
									$this->dbreport->where("georeport_droppoint",$master_droppoint_id);
									$q_report_ota = $this->dbreport->get($dbtable_ota);
									$row_report_ota = $q_report_ota->row();
									$total_row_report_ota = count($row_report_ota);
									
									if($total_row_report_ota > 0){
										$ota_nopol_cek = $row_report_ota->$field_vehicle;
										$ota_status = $row_report_ota->$field_status;
										$ota_date	= $row_report_ota->$field_date;
										if($ota_nopol_cek != ""){
											$ota_nopol = $ota_nopol_cek;
											//printf("2==OTA - ADA 1 DROPPOINT !! : %s ID %s \r\n", $row_master_droppoint[$d]->droppoint_name, $row_master_droppoint[$d]->droppoint_id); 
											//printf("2==OTA - ADA 1 NOPOL !! : %s ID %s \r\n", $ota_nopol, $ota_status); 
											$ota_nopol_total = $ota_nopol_total + 1;
										}
										
									}else{
										//tidak ada report OTA
										printf("2==OTA - TIDAK ADA REPORT OTA!!!! \r\n"); 
									}
								}
								
							}else{
								//tidak ada 1 droppoint pun
								printf("2==OTA - TIDAK ADA SATUPUN DROPPOINT !!!! \r\n"); 
								
							}
							printf("2==OTA - BERDASARKAN TOTAL DATA OTA : %s NOPOL : %s \r\n", $ota_nopol_total, $ota_nopol); 
							//cek valid nopol
							if($ota_nopol_total >= 3){
								$this->db->order_by("vehicle_id", "asc");
								$this->db->select("vehicle_device,vehicle_id,vehicle_name,vehicle_no,vehicle_type");
								$this->db->limit(1);
								$this->db->where("vehicle_no",$ota_nopol);
								//$this->db->where("vehicle_user_id","1032");//user balrich
								$this->db->where("vehicle_status <>",3);//only aktif
								$q_vehicle_valid = $this->db->get("vehicle");
								$row_vehicle_valid = $q_vehicle_valid->row();
								$total_vehicle_valid = count($row_vehicle_valid);
								if($total_vehicle_valid > 0){
									$cek_random_status = 1;
									$ota_vehicle_valid = $row_vehicle_valid->vehicle_device;
									$ota_vehicle_name = $row_vehicle_valid->vehicle_name;
									$ota_vehicle_no = $row_vehicle_valid->vehicle_no;
									$ota_vehicle_type = $row_vehicle_valid->vehicle_type;
									$ota_vehicle_id = $row_vehicle_valid->vehicle_id;
									
									printf("2==OTA - BERDASARKAN OTA NOPOL : %s %s \r\n", $ota_nopol, $ota_vehicle_valid); 
									
									//OTL
									//select START LOADING & UNLOADING (all type)
									//dari report DOOR Status, status OPEN di geofence BASE lokasi START dan END, limit 1 order by START TIME desc (per nopol) -- Search Level 2
									$this->dbreport->order_by("door_start_time","desc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("door_vehicle_device",$row_dist->distrep_vehicle_device);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end",$row_base->base_geofence);
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",10); //open lebih dari 10 detik
									$q_report_otl = $this->dbreport->get($dbtable_door);
									$row_report_otl = $q_report_otl->row();
									$total_row_report_otl = count($row_report_otl);
											
									//jika ada ada alert report OTL (dari DOOR)
									if($total_row_report_otl > 0){
										printf("3==OTL - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $row_report_otl->door_vehicle_no, $row_report_otl->door_start_time); 
										
										$loading_vehicle_id = $row_report_otl->door_vehicle_id;
										$loading_vehicle_device = $row_report_otl->door_vehicle_device;
										$loading_vehicle_no = $row_report_otl->door_vehicle_no;
										$loading_vehicle_name = $row_report_otl->door_vehicle_name;
										$loading_vehicle_type = $row_report_otl->door_vehicle_type;
										
										$loading_otl_base = $row_report_otl->door_geofence_start;
										$loading_otl_start_date = date("Y-m-d", strtotime($row_report_otl->door_start_time));
										$loading_otl_start_time = date("H:i:s", strtotime($row_report_otl->door_start_time));
										$loading_otl_end_date = date("Y-m-d", strtotime($row_report_otl->door_end_time));
										$loading_otl_end_time = date("H:i:s", strtotime($row_report_otl->door_end_time));
										$loading_otl_location_start = $row_report_otl->door_location_start;
										$loading_otl_location_end = $row_report_otl->door_location_end;
										$loading_otl_coordinate_start = $row_report_otl->door_coordinate_start;
										$loading_otl_coordinate_end = $row_report_otl->door_coordinate_end;
										$loading_otl_geofence_start = $row_report_otl->door_geofence_start;
										$loading_otl_geofence_end = $row_report_otl->door_geofence_end;
										$loading_otl_duration =  $row_report_otl->door_duration;
										$loading_otl_duration_sec =  $row_report_otl->door_duration_sec;
										$loading_otl_report_status = "DOOR";
										
										if($row_base->base_type == "POOL"){
											//search actual OTL POOL (arrival)
											//all status engine yag geofence END nya di nama Base
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$row_dist->distrep_vehicle_device);
											$this->dbreport->where("trip_mileage_geofence_end",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
											$q_report_otl_actual = $this->dbreport->get($dbtable_opr);
											$row_report_otl_actual = $q_report_otl_actual->row();
											$total_row_report_otl_actual = count($row_report_otl_actual);
											if($total_row_report_otl_actual > 0){
												$loading_otl_arrival_date = date("Y-m-d", strtotime($row_report_otl_actual->trip_mileage_end_time));
												$loading_otl_arrival_time = date("H:i:s", strtotime($row_report_otl_actual->trip_mileage_end_time));
												printf("3!==OTL (POOL) - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
											}
										}
										//jika DC atau Tipe lain sama dengan start loading
										else
										{ 
											$loading_otl_arrival_date = $loading_otl_start_date;
											$loading_otl_arrival_time = $loading_otl_start_time;
											printf("3==OTL (DC) - SAMA DENGAN REPORT DOOR !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
										}
									
									}
									
									//OTD
									//select OTD Start End time  //berdasarkan nopol yg dapat dari OTL
									$mobil_distance = 0;
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di mana saja yg penting bukan GEOFENCE DROPPOINT, 
									//limit 1 order by START TIME asc
									$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("trip_mileage_vehicle_id",$loading_vehicle_device);
									$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd_time = $this->dbreport->get($dbtable_opr);
									$row_report_otd_time = $q_report_otd_time->row();
									$total_row_report_otd_time = count($row_report_otd_time);		
									
									if($total_row_report_otd_time > 0){
										
										$loading_otd_start_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_start_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_end_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_end_time));
										$loading_otd_end_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_end_time));
										
									}
							
									//search droppoint ID
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di GEOFENCE DROPPOINT, limit 1 order by START TIME asc
									//dirubah ke Report Door Open di toko
									
									/*$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("trip_mileage_vehicle_id",$loading_vehicle_device);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=","0");
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd = $this->dbreport->get($dbtable_opr);
									*/
									
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_start !=","0");
									$this->dbreport->where("door_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end !=","0");
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",3); //open lebih dari 3 detik
									$q_report_otd = $this->dbreport->get($dbtable_door);
									
									$row_report_otd = $q_report_otd->row();
									$total_row_report_otd = count($row_report_otd);		
									
									//jika ada alert report OTD
									if($total_row_report_otd > 0){
										printf("4==OTD - ADA REPORT OTD level 1 - DOOR !!!! : %s %s %s \r\n", $row_report_otd->door_vehicle_no,$row_report_otd->door_vehicle_device,$row_report_otd->door_start_time); 
										
										/*$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
										$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
										$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
										$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
										$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
										$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "OPR";
										$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;*/
										
										$loading_otd_base = $row_report_otd->door_geofence_start;
										$loading_otd_location_start = $row_report_otd->door_location_start;
										$loading_otd_location_end = $row_report_otd->door_location_end;
										$loading_otd_coordinate_start = $row_report_otd->door_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->door_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->door_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->door_geofence_end;
										$loading_otd_duration =  $row_report_otd->door_duration;
										$loading_otd_duration_sec =  $row_report_otd->door_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "DOOR";
										$mobil_distance = $row_report_otd->door_vehicle_device;
										
									}
									else
									{
											//search level 2 //cari semua geofence report operasional
											//select OTD report yaitu (tipe DC) 
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
											$this->dbreport->where("trip_mileage_geofence_start !=",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_geofence_end !=","0");
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
											$q_report_otd = $this->dbreport->get($dbtable_opr);
											$row_report_otd = $q_report_otd->row();
											$total_row_report_otd = count($row_report_otd);
											//	print_r($total_row_report_otd." "." 1"." ".$loading_vehicle_device);exit();
											
											if($total_row_report_otd > 0){
												printf("4==OTD - ADA REPORT OTD level 2 - OPR !!!! : %s %s %s \r\n", $row_report_otd->trip_mileage_vehicle_no,$row_report_otd->trip_mileage_vehicle_id,$row_report_otd->trip_mileage_start_time); 
												$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
												
												$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
												$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
												$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
												$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
												$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
												$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
												$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
												$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
												$loading_otd_distrep = 0;
												$loading_otd_distrep_name = "-";
												$loading_otd_report_status = "OPR";
												$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;
												
												
											}else{
												printf("4X==OTD - TIDAK ADA OTD !!!! : \r\n"); 
											}
										
									}
									printf("4==OTD - GEOFENCE NAME !!!! : %s \r\n", $loading_otd_geofence_end);
									if($loading_otd_geofence_end != ""){
										//select distrep berdasarkan geofence END 
										$this->dbtransporter->order_by("droppoint_id","desc");
										$this->dbtransporter->select("droppoint_distrep,distrep_name,company_plant");
										$this->dbtransporter->where("droppoint_flag",0);
										$this->dbtransporter->where("droppoint_geofence",$loading_otd_geofence_end);
										$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
										$this->dbtransporter->join("droppoint_parent", "distrep_parent = parent_id", "left");
										$this->dbtransporter->join("droppoint_company_custom", "parent_company = company_id", "left");
										$q_droppoint = $this->dbtransporter->get("droppoint");
										$row_droppoint = $q_droppoint->row();
										$total_droppoint = count($row_droppoint);
										if($total_droppoint > 0){
											printf("4==OTD - ADA ID DISTREP !!!! : %s \r\n", $row_droppoint->droppoint_distrep); 
											$loading_otd_distrep = $row_droppoint->droppoint_distrep;
											$loading_plant = $row_droppoint->company_plant;
											$loading_otd_distrep_name = $row_droppoint->distrep_name;
										}
									}
									$loading_distance = 0;
									$loading_distance_count = 0;
									
									if($mobil_distance){
										//search distance by GPS //berdasarkan nopol dari OTL  //nilai km terakhir
										$this->dbreport->order_by("trip_mileage_id","asc");
										$this->dbreport->select("trip_mileage_trip_mileage");
										$this->dbreport->where("trip_mileage_vehicle_id",$mobil_distance);
										$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
										$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
										$q_report_distance = $this->dbreport->get($dbtable_opr);
										$row_report_distance = $q_report_distance->result();
										$total_row_report_distance = count($row_report_distance);
										if($total_row_report_distance > 0){
											for($t=0;$t<$total_row_report_distance; $t++){
												$loading_distance_count = $loading_distance_count + $row_report_distance[$t]->trip_mileage_trip_mileage;
											}
										}
										
									}
									
										//$loading_distance = floatval($loading_distance_count);
										$loading_distance = $loading_distance_count;
										printf("4==DISTANCE %s at %s %s \r\n", $loading_distance,
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd)),
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd)) ); 
									
									
								}
								
								
								unset($data);
								if($total_row_report_otl > 0){
									$data["loading_vehicle_id"] = $loading_vehicle_id;
									$data["loading_vehicle_device"] = $loading_vehicle_device;
									$data["loading_vehicle_no"] = $loading_vehicle_no;
									$data["loading_vehicle_name"] = $loading_vehicle_name;
									$data["loading_vehicle_type"] = $loading_vehicle_type;
								}else{
									$data["loading_vehicle_id"] = $ota_vehicle_id;
									$data["loading_vehicle_device"] = $ota_vehicle_valid;
									$data["loading_vehicle_no"] = $ota_vehicle_no;
									$data["loading_vehicle_name"] = $ota_vehicle_name;
									$data["loading_vehicle_type"] = $ota_vehicle_type;
								}
								
								$data["loading_type"] = $loading_type;
								$data["loading_report_base"] = $loading_report_base;
								$data["loading_report_date"] = $loading_report_date;
								$data["loading_plant"] = $loading_plant;
								$data["loading_company_custom"] = $loading_company_custom;
								
								$data["loading_otl_base"] = $loading_otl_base;
								$data["loading_otl_start_date"] = $loading_otl_start_date;
								$data["loading_otl_start_time"] = $loading_otl_start_time;
								$data["loading_otl_end_date"] = $loading_otl_end_date;
								$data["loading_otl_end_time"] = $loading_otl_end_time;
								$data["loading_otl_location_start"] = $loading_otl_location_start;
								$data["loading_otl_location_end"] = $loading_otl_location_end;
								$data["loading_otl_coordinate_start"] = $loading_otl_coordinate_start;
								$data["loading_otl_coordinate_end"] = $loading_otl_coordinate_end;
								$data["loading_otl_geofence_start"] = $loading_otl_geofence_start;
								$data["loading_otl_geofence_end"] = $loading_otl_geofence_end;
								$data["loading_otl_duration"] = $loading_otl_duration;
								$data["loading_otl_duration_sec"] = $loading_otl_duration_sec;
								$data["loading_otl_report_status"] = $loading_otl_report_status;
								
								$data["loading_otd_base"] = $loading_otd_base;
								$data["loading_otd_distrep"] = $loading_otd_distrep;
								$data["loading_otd_distrep_name"] = $loading_otd_distrep_name;
								$data["loading_otd_start_date"] = $loading_otd_start_date;
								$data["loading_otd_start_time"] = $loading_otd_start_time;
								$data["loading_otd_end_date"] = $loading_otd_end_date;
								$data["loading_otd_end_time"] = $loading_otd_end_time;
								$data["loading_otd_location_start"] = $loading_otd_location_start;
								$data["loading_otd_location_end"] = $loading_otd_location_end;
								$data["loading_otd_coordinate_start"] = $loading_otd_coordinate_start;
								$data["loading_otd_coordinate_end"] = $loading_otd_coordinate_end;
								$data["loading_otd_geofence_start"] = $loading_otd_geofence_start;
								$data["loading_otd_geofence_end"] = $loading_otd_geofence_end;
								$data["loading_otd_duration"] = $loading_otd_duration;
								$data["loading_otd_duration_sec"] = $loading_otd_duration_sec;
								$data["loading_otd_km_start"] = $loading_otd_km_start;
								$data["loading_otd_km_end"] = $loading_otd_km_end;
								$data["loading_otd_report_status"] = $loading_otd_report_status;
								$data["loading_otl_arrival_date"] = $loading_otl_arrival_date;
								$data["loading_otl_arrival_time"] = $loading_otl_arrival_time;
								$data["loading_distance"] = $loading_distance;
								
								//SELECT REPORT DI TANGGAL REPORT CRON
								$this->dbreport->limit(1);
								$this->dbreport->where("loading_vehicle_device",$ota_vehicle_valid);
								$this->dbreport->where("loading_report_base", $loading_report_base);
								$this->dbreport->where("loading_report_date",$loading_report_date);
								$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);
								$q_report_loading = $this->dbreport->get($dbtable);
								$row_report_loading = $q_report_loading->row();
								$total_row_report_loading = count($row_report_loading);
								
								if($total_row_report_loading > 0){
									//UPDATE JIKA SUDAH DA DATA MOBIL DI TANGGAL REPORT CRON
									printf("5==DATABASE - UPDATE REPORT OTL & OTD : %s %s \r\n", $loading_vehicle_device, $loading_report_date); 
									$this->dbreport->where("loading_vehicle_device", $ota_vehicle_valid);
									$this->dbreport->where("loading_report_base", $loading_report_base);
									$this->dbreport->where("loading_report_date", $loading_report_date);
									//$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);						
									$this->dbreport->update($dbtable,$data);
								}else{
									if(($total_row_report_otl > 0) || ($total_row_report_otd > 0)){
										if($loading_vehicle_device != ""){
											//ADA DOOR OPEN DI BASE
											printf("5==DATABASE - KENDARAAN ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date);
										}else{
											//TIDAK ADA DOOR OPEN ( TIDAK ADA OTL )
											printf("5==DATABASE - KENDARAN TIDAK ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date); 
										}
										
										//INSERT JIKA BELUM ADA DATA MOBIL DI TANGGAL REPORT CRON
										printf("5==DATABASE - INSERT REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date);
										$this->dbreport->insert($dbtable,$data);
										
									}else{
										//TIDAK ADA DATA REPORT OTL & OTD
										printf("5X==SKIP - TIDAK ADA REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date); 
									}
									
								}
								
							}else{
								$ota_vehicle_valid = "0";
								printf("2==OTA - TIDAK ADA NOPOL VALID  \r\n"); 
							}
						
				printf("=============================================== \r\n");
				
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "LACAKTRANSPRO - OTL OTD DC TIPE 2 FROM OTA";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_dist."
End Distrep   : "."( ".$i." / ".$total_dist." )"."

Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Distrep: ".$total_dist." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		
	}
	
	//khusus POOl from OTA (others RAI KJL)
	function otl_otd_report_pool_from_ota_others($date="",$month="",$year="",$base="")
	{
		$offset = 0;
		$offset_base = 0;
		$offset_dist = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "loading_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$report_ota = "inout_geofence_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		$loading_report_date = $newdate;
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			$dbtable_ota = $report_ota."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			$dbtable_ota = $report_ota."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			$dbtable_ota = $report_ota."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			$dbtable_ota = $report_ota."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			$dbtable_ota = $report_ota."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			$dbtable_ota = $report_ota."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			$dbtable_ota = $report_ota."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			$dbtable_ota = $report_ota."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			$dbtable_ota = $report_ota."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			$dbtable_ota = $report_ota."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			$dbtable_ota = $report_ota."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			$dbtable_ota = $report_ota."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select BASE 
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("base_id","desc");
		if($base != ""){
			$this->dbtransporter->where("base_id", $base);
		}
		$this->dbtransporter->where("base_cron_active",2);
		$this->dbtransporter->where("base_flag",0);
		$this->dbtransporter->where("base_company <>",24); //others bukan balrich
		$qd = $this->dbtransporter->get("droppoint_base");
		$rows_base = $qd->result();
		$total_base = count($rows_base);
		printf("1==BASE - TOTAL BASE : %s \r\n", $total_base); 
		
		foreach($rows_base as $row_base)
		{
			
			if (($i+1) < $offset_base)
			{
				$i++;
				continue;
			}
			
			printf("1==BASE - PROCESS NUMBER BASE : %s \r\n", ++$i." of ".$total_base);
			printf("1==BASE - PROSES DISTREP   : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			
			printf("1==BASE - GET DISTREP BY BASE : %s %s \r\n", $row_base->base_id, $row_base->base_geofence); 
			$this->dbtransporter->order_by("distrep_id","asc");
			$this->dbtransporter->where("distrep_otl_base", $row_base->base_geofence);
			$q_dist = $this->dbtransporter->get("droppoint_distrep");
			$rows_dist = $q_dist->result();
			$total_dist = count($rows_dist);
			printf("1==BASE - TOTAL DISTREP : %s \r\n", $total_dist);
			printf("=============================================== \r\n");
			$j=0;
				
			foreach($rows_dist as $row_dist)
			{
				
				if (($j+1) < $offset_dist)
				{
					$j++;
					continue;
				}
				
				printf("2==DISTREP - PROCESS NUMBER DISTREP : %s %s %s \r\n", ++$j." of ".$total_dist , "distrep: ". $i." of ".$total_base, $newdate);
				printf("2==DISTREP - PROSES DISTREP : %s %s \r\n", $row_dist->distrep_id, $row_dist->distrep_name); 
					
					//default
					$total_row_report_otl = 0;
					$total_row_report_otd = 0;
					
					$loading_vehicle_id = "";
					$loading_vehicle_device = "";
					$loading_vehicle_no = "";
					$loading_vehicle_name = "";
					$loading_vehicle_type = "";
					
					$ota_vehicle_id = "";
					$ota_vehicle_valid = "";
					$ota_vehicle_no = "";
					$ota_vehicle_name = "";
					$ota_vehicle_type = "";
				
					$loading_type = "";
					$loading_plant = 0;
					$loading_company_custom = 0;
					$loading_report_base = "";
					$loading_otl_base = "";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_otl_start_date = "";
					$loading_otl_start_time = "";
					$loading_otl_end_date = "";
					$loading_otl_end_time = "";
					$loading_otl_location_start = "";
					$loading_otl_location_end = "";
					$loading_otl_coordinate_start = "";
					$loading_otl_coordinate_end = "";
					$loading_otl_geofence_start = "";
					$loading_otl_geofence_end = "";
					$loading_otl_duration = "";
					$loading_otl_duration_sec = "";
					$loading_otl_report_status = "-";
					
					$loading_otd_base = "";
					$loading_otd_distrep = 0;
					$loading_otd_distrep_name = 0;
					$loading_otd_start_date = "";
					$loading_otd_start_time = "";
					$loading_otd_end_date = "";
					$loading_otd_end_time = "";
					$loading_otd_location_start = "";
					$loading_otd_location_end = "";
					$loading_otd_coordinate_start = "";
					$loading_otd_coordinate_end = "";
					$loading_otd_geofence_start = "";
					$loading_otd_geofence_end = "";
					$loading_otd_duration = "";
					$loading_otd_duration_sec = "";
					$loading_otd_km_start = "";
					$loading_otd_km_end = "";
					$loading_otd_unloading_time = "";
					$loading_otd_report_status = "-";
					$loading_otl_arrival_date = "";
					$loading_otl_arrival_time = "";
					$loading_distance = 0;
					$loading_distance_count = 0;
				
					$loading_report_base = $row_base->base_geofence;
					$loading_type = $row_base->base_type;
					$loading_plant = $row_base->base_plant;
					
					$ota_nopol = 0;
					$ota_nopol_total = 0;
					
					$this->dbtransporter->order_by("parent_id","desc");
					$this->dbtransporter->select("parent_company");
					$this->dbtransporter->where("parent_id",$row_dist->distrep_parent);
					$this->dbtransporter->where("parent_flag",0);
					$q_parent = $this->dbtransporter->get("droppoint_parent");
					$row_parent = $q_parent->row();
					$total_parent = count($row_parent);
					if($total_parent > 0){
						$loading_company_custom = $row_parent->parent_company;
					}
					
					//tipe random (default)
					$cek_random_status = 0;
					
							//cek dari ota report nopol valid 
							//config field ota
							$field_date = "georeport_date_".$date; // date time
							$field_vehicle = "georeport_vehicle_".$date; // NOPOL
							$field_status = "georeport_status_".$date; //OPR / DOOR
							
							$field_select = $field_date.", ".$field_vehicle.", ".$field_status;
							
							//search 1 dropoint asc by name
							$this->dbtransporter->order_by("droppoint_id","asc");
							$this->dbtransporter->select("droppoint_distrep,droppoint_name,droppoint_id");
							$this->dbtransporter->where("droppoint_distrep",$row_dist->distrep_id);
							$this->dbtransporter->where("droppoint_flag",0);
							$q_master_droppoint = $this->dbtransporter->get("droppoint");
							$row_master_droppoint = $q_master_droppoint->result();
							$total_master_droppoint = count($row_master_droppoint);
							
							if($total_master_droppoint > 0){
								$ota_nopol_total = 0;
								for($d=0;$d<$total_master_droppoint; $d++){
									$master_droppoint_id = $row_master_droppoint[$d]->droppoint_id;
									//search report OTA 
									$this->dbreport->order_by("georeport_id","desc");
									$this->dbreport->select($field_select);
									$this->dbreport->where("georeport_droppoint",$master_droppoint_id);
									$q_report_ota = $this->dbreport->get($dbtable_ota);
									$row_report_ota = $q_report_ota->row();
									$total_row_report_ota = count($row_report_ota);
									
									if($total_row_report_ota > 0){
										$ota_nopol_cek = $row_report_ota->$field_vehicle;
										$ota_status = $row_report_ota->$field_status;
										$ota_date	= $row_report_ota->$field_date;
										if($ota_nopol_cek != ""){
											$ota_nopol = $ota_nopol_cek;
											//printf("2==OTA - ADA 1 DROPPOINT !! : %s ID %s \r\n", $row_master_droppoint[$d]->droppoint_name, $row_master_droppoint[$d]->droppoint_id); 
											//printf("2==OTA - ADA 1 NOPOL !! : %s ID %s \r\n", $ota_nopol, $ota_status); 
											$ota_nopol_total = $ota_nopol_total + 1;
										}
										
									}else{
										//tidak ada report OTA
										printf("2==OTA - TIDAK ADA REPORT OTA!!!! \r\n");
									}
								}
								
							}else{
								//tidak ada 1 droppoint pun
								printf("2==OTA - TIDAK ADA SATUPUN DROPPOINT !!!! \r\n"); 
								
							}
							printf("2==OTA - BERDASARKAN TOTAL DATA OTA : %s NOPOL : %s \r\n", $ota_nopol_total, $ota_nopol); 
							//cek valid nopol
							if($ota_nopol_total >= 3){
								$this->db->order_by("vehicle_id", "asc");
								$this->db->select("vehicle_device,vehicle_id,vehicle_name,vehicle_no,vehicle_type");
								$this->db->limit(1);
								$this->db->where("vehicle_no",$ota_nopol);
								//$this->db->where("vehicle_user_id","1032");//user balrich
								$this->db->where("vehicle_status <>",3);//only aktif
								$q_vehicle_valid = $this->db->get("vehicle");
								$row_vehicle_valid = $q_vehicle_valid->row();
								$total_vehicle_valid = count($row_vehicle_valid);
								printf("2==OTA - TOTAL VALID NOPOL : %s \r\n", $total_vehicle_valid);
								if($total_vehicle_valid > 0){
									$cek_random_status = 1;
									$ota_vehicle_valid = $row_vehicle_valid->vehicle_device;
									$ota_vehicle_name = $row_vehicle_valid->vehicle_name;
									$ota_vehicle_no = $row_vehicle_valid->vehicle_no;
									$ota_vehicle_type = $row_vehicle_valid->vehicle_type;
									$ota_vehicle_id = $row_vehicle_valid->vehicle_id;
									
									printf("2==OTA - BERDASARKAN DATA OTA NOPOL : %s %s \r\n", $ota_nopol, $ota_vehicle_valid); 
									
									//OTL
									//select START LOADING & UNLOADING (all type)
									//dari report DOOR Status, status OPEN di geofence BASE lokasi START dan END, limit 1 order by START TIME desc (per nopol) -- Search Level 2
									$this->dbreport->order_by("door_start_time","desc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("door_vehicle_device",$row_dist->distrep_vehicle_device);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end",$row_base->base_geofence);
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",10); //open lebih dari 10 detik
									$q_report_otl = $this->dbreport->get($dbtable_door);
									$row_report_otl = $q_report_otl->row();
									$total_row_report_otl = count($row_report_otl);
											
									//jika ada ada alert report OTL (dari DOOR)
									if($total_row_report_otl > 0){
										printf("3==OTL - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $row_report_otl->door_vehicle_no, $row_report_otl->door_start_time); 
										
										$loading_vehicle_id = $row_report_otl->door_vehicle_id;
										$loading_vehicle_device = $row_report_otl->door_vehicle_device;
										$loading_vehicle_no = $row_report_otl->door_vehicle_no;
										$loading_vehicle_name = $row_report_otl->door_vehicle_name;
										$loading_vehicle_type = $row_report_otl->door_vehicle_type;
										
										$loading_otl_base = $row_report_otl->door_geofence_start;
										$loading_otl_start_date = date("Y-m-d", strtotime($row_report_otl->door_start_time));
										$loading_otl_start_time = date("H:i:s", strtotime($row_report_otl->door_start_time));
										$loading_otl_end_date = date("Y-m-d", strtotime($row_report_otl->door_end_time));
										$loading_otl_end_time = date("H:i:s", strtotime($row_report_otl->door_end_time));
										$loading_otl_location_start = $row_report_otl->door_location_start;
										$loading_otl_location_end = $row_report_otl->door_location_end;
										$loading_otl_coordinate_start = $row_report_otl->door_coordinate_start;
										$loading_otl_coordinate_end = $row_report_otl->door_coordinate_end;
										$loading_otl_geofence_start = $row_report_otl->door_geofence_start;
										$loading_otl_geofence_end = $row_report_otl->door_geofence_end;
										$loading_otl_duration =  $row_report_otl->door_duration;
										$loading_otl_duration_sec =  $row_report_otl->door_duration_sec;
										$loading_otl_report_status = "DOOR";
										
										if($row_base->base_type == "POOL"){
											//search actual OTL POOL (arrival)
											//all status engine yag geofence END nya di nama Base
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$row_dist->distrep_vehicle_device);
											$this->dbreport->where("trip_mileage_geofence_end",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
											$q_report_otl_actual = $this->dbreport->get($dbtable_opr);
											$row_report_otl_actual = $q_report_otl_actual->row();
											$total_row_report_otl_actual = count($row_report_otl_actual);
											if($total_row_report_otl_actual > 0){
												$loading_otl_arrival_date = date("Y-m-d", strtotime($row_report_otl_actual->trip_mileage_end_time));
												$loading_otl_arrival_time = date("H:i:s", strtotime($row_report_otl_actual->trip_mileage_end_time));
												printf("3!==OTL (POOL) - ADA REPORT OTL PER NOPOL !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
											}
										}
										//jika DC atau Tipe lain sama dengan start loading
										else
										{ 
											$loading_otl_arrival_date = $loading_otl_start_date;
											$loading_otl_arrival_time = $loading_otl_start_time;
											printf("3==OTL (DC) - SAMA DENGAN REPORT DOOR !!!! : %s %s \r\n", $loading_otl_arrival_date, $loading_otl_arrival_time); 
										}
									
									}
									
									//OTD
									//select OTD Start End time  //berdasarkan nopol yg dapat dari OTL
									$mobil_distance = 0;
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di mana saja yg penting bukan GEOFENCE DROPPOINT, 
									//limit 1 order by START TIME asc
									$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									//$this->dbreport->where("trip_mileage_vehicle_id",$loading_vehicle_device);
									$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence_pool);
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd_time = $this->dbreport->get($dbtable_opr);
									$row_report_otd_time = $q_report_otd_time->row();
									$total_row_report_otd_time = count($row_report_otd_time);		
									
									if($total_row_report_otd_time > 0){
										
										$loading_otd_start_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_start_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_start_time));
										$loading_otd_end_date = date("Y-m-d", strtotime($row_report_otd_time->trip_mileage_end_time));
										$loading_otd_end_time = date("H:i:s", strtotime($row_report_otd_time->trip_mileage_end_time));
										
									}
							
									//search droppoint ID
									//dari report OPERASIONAL, status ENGIN ON di geofence BASE lokasi START dan END di GEOFENCE DROPPOINT, limit 1 order by START TIME asc
									//dirubah ke Report Door Open di toko
									
									/*$this->dbreport->order_by("trip_mileage_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("trip_mileage_vehicle_id",$loading_vehicle_device);
									$this->dbreport->where("trip_mileage_engine","1");
									$this->dbreport->where("trip_mileage_geofence_start",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("trip_mileage_geofence_end !=","0");
									$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
									$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
									$q_report_otd = $this->dbreport->get($dbtable_opr);
									*/
									
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->limit(1);
									$this->dbreport->where("door_vehicle_device",$ota_vehicle_valid);
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_geofence_start !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_start !=",$row_base->base_geofence_pool);
									$this->dbreport->where("door_geofence_start !=","0");
									$this->dbreport->where("door_geofence_end !=",$row_base->base_geofence);
									$this->dbreport->where("door_geofence_end !=",$row_base->base_geofence_pool);
									$this->dbreport->where("door_geofence_end !=","0");
									$this->dbreport->where("door_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otl))); 
									$this->dbreport->where("door_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otl))); 
									$this->dbreport->where("door_duration_sec >",3); //open lebih dari 3 detik
									$q_report_otd = $this->dbreport->get($dbtable_door);
									
									$row_report_otd = $q_report_otd->row();
									$total_row_report_otd = count($row_report_otd);		
									
									//jika ada alert report OTD
									if($total_row_report_otd > 0){
										printf("4==OTD - ADA REPORT OTD level 1 - DOOR !!!! : %s %s %s \r\n", $row_report_otd->door_vehicle_no,$row_report_otd->door_vehicle_device,$row_report_otd->door_start_time); 
										
										/*$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
										$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
										$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
										$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
										$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
										$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "OPR";
										$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;*/
										
										$loading_otd_base = $row_report_otd->door_geofence_start;
										$loading_otd_location_start = $row_report_otd->door_location_start;
										$loading_otd_location_end = $row_report_otd->door_location_end;
										$loading_otd_coordinate_start = $row_report_otd->door_coordinate_start;
										$loading_otd_coordinate_end = $row_report_otd->door_coordinate_end;
										$loading_otd_geofence_start = $row_report_otd->door_geofence_start;
										$loading_otd_geofence_end = $row_report_otd->door_geofence_end;
										$loading_otd_duration =  $row_report_otd->door_duration;
										$loading_otd_duration_sec =  $row_report_otd->door_duration_sec;
										$loading_otd_distrep = 0;
										$loading_otd_distrep_name = "-";
										$loading_otd_report_status = "DOOR";
										$mobil_distance = $row_report_otd->door_vehicle_device;
										
									}
									else
									{
											//search level 2 //cari semua geofence report operasional
											//select OTD report yaitu (tipe DC) 
											$this->dbreport->order_by("trip_mileage_start_time","asc");
											$this->dbreport->limit(1);
											$this->dbreport->where("trip_mileage_vehicle_id",$ota_vehicle_valid);
											//$this->dbreport->where("trip_mileage_geofence_start !=",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence);
											$this->dbreport->where("trip_mileage_geofence_end !=",$row_base->base_geofence_pool);
											$this->dbreport->where("trip_mileage_geofence_end !=","0");
											
											$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
											$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
											$q_report_otd = $this->dbreport->get($dbtable_opr);
											$row_report_otd = $q_report_otd->row();
											$total_row_report_otd = count($row_report_otd);
											//	print_r($total_row_report_otd." "." 1"." ".$loading_vehicle_device);exit();
											
											if($total_row_report_otd > 0){
												printf("4==OTD - ADA REPORT OTD level 2 - OPR !!!! : %s %s %s \r\n", $row_report_otd->trip_mileage_vehicle_no,$row_report_otd->trip_mileage_vehicle_id,$row_report_otd->trip_mileage_start_time); 
												$loading_otd_base = $row_report_otd->trip_mileage_geofence_start;
												
												$loading_otd_location_start = $row_report_otd->trip_mileage_location_start;
												$loading_otd_location_end = $row_report_otd->trip_mileage_location_end;
												$loading_otd_coordinate_start = $row_report_otd->trip_mileage_coordinate_start;
												$loading_otd_coordinate_end = $row_report_otd->trip_mileage_coordinate_end;
												$loading_otd_geofence_start = $row_report_otd->trip_mileage_geofence_start;
												$loading_otd_geofence_end = $row_report_otd->trip_mileage_geofence_end;
												$loading_otd_duration =  $row_report_otd->trip_mileage_duration;
												$loading_otd_duration_sec =  $row_report_otd->trip_mileage_duration_sec;
												$loading_otd_distrep = 0;
												$loading_otd_distrep_name = "-";
												$loading_otd_report_status = "OPR";
												$mobil_distance = $row_report_otd->trip_mileage_vehicle_id;
												
												
											}else{
												printf("4X==OTD - TIDAK ADA OTD !!!! : \r\n"); 
											}
										
									}
									printf("4==OTD - GEOFENCE NAME !!!! : %s \r\n", $loading_otd_geofence_end);
									if($loading_otd_geofence_end != ""){
										//select distrep berdasarkan geofence END 
										$this->dbtransporter->order_by("droppoint_id","desc");
										$this->dbtransporter->select("droppoint_distrep,distrep_name,company_plant");
										$this->dbtransporter->where("droppoint_flag",0);
										$this->dbtransporter->where("droppoint_geofence",$loading_otd_geofence_end);
										$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
										$this->dbtransporter->join("droppoint_parent", "distrep_parent = parent_id", "left");
										$this->dbtransporter->join("droppoint_company_custom", "parent_company = company_id", "left");
										$q_droppoint = $this->dbtransporter->get("droppoint");
										$row_droppoint = $q_droppoint->row();
										$total_droppoint = count($row_droppoint);
										if($total_droppoint > 0){
											printf("4==OTD - ADA ID DISTREP !!!! : %s %s \r\n", $row_droppoint->droppoint_distrep, $row_droppoint->distrep_name); 
											$loading_otd_distrep = $row_droppoint->droppoint_distrep;
											$loading_plant = $row_droppoint->company_plant;
											$loading_otd_distrep_name = $row_droppoint->distrep_name;
										}
									}
									$loading_distance = 0;
									$loading_distance_count = 0;
									
									if($mobil_distance){
										//search distance by GPS //berdasarkan nopol dari OTL  //nilai km terakhir
										$this->dbreport->order_by("trip_mileage_id","asc");
										$this->dbreport->select("trip_mileage_trip_mileage");
										$this->dbreport->where("trip_mileage_vehicle_id",$mobil_distance);
										$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd))); 
										$this->dbreport->where("trip_mileage_end_time <=",date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd))); 
										$q_report_distance = $this->dbreport->get($dbtable_opr);
										$row_report_distance = $q_report_distance->result();
										$total_row_report_distance = count($row_report_distance);
										if($total_row_report_distance > 0){
											for($t=0;$t<$total_row_report_distance; $t++){
												$loading_distance_count = $loading_distance_count + $row_report_distance[$t]->trip_mileage_trip_mileage;
											}
										}
										
									}
									
										//$loading_distance = floatval($loading_distance_count);
										$loading_distance = $loading_distance_count;
										printf("4==DISTANCE %s at %s %s \r\n", $loading_distance,
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_start_otd)),
											   date("Y-m-d H:i:s", strtotime($newdate." ".$row_dist->distrep_est_end_otd)) ); 
									
									
								}
								
								
								unset($data);
								if($total_row_report_otl > 0){
									$data["loading_vehicle_id"] = $loading_vehicle_id;
									$data["loading_vehicle_device"] = $loading_vehicle_device;
									$data["loading_vehicle_no"] = $loading_vehicle_no;
									$data["loading_vehicle_name"] = $loading_vehicle_name;
									$data["loading_vehicle_type"] = $loading_vehicle_type;
								}else{
									$data["loading_vehicle_id"] = $ota_vehicle_id;
									$data["loading_vehicle_device"] = $ota_vehicle_valid;
									$data["loading_vehicle_no"] = $ota_vehicle_no;
									$data["loading_vehicle_name"] = $ota_vehicle_name;
									$data["loading_vehicle_type"] = $ota_vehicle_type;
								}
								
								$data["loading_type"] = $loading_type;
								$data["loading_report_base"] = $loading_report_base;
								$data["loading_report_date"] = $loading_report_date;
								$data["loading_plant"] = $loading_plant;
								$data["loading_company_custom"] = $loading_company_custom;
								
								$data["loading_otl_base"] = $loading_otl_base;
								$data["loading_otl_start_date"] = $loading_otl_start_date;
								$data["loading_otl_start_time"] = $loading_otl_start_time;
								$data["loading_otl_end_date"] = $loading_otl_end_date;
								$data["loading_otl_end_time"] = $loading_otl_end_time;
								$data["loading_otl_location_start"] = $loading_otl_location_start;
								$data["loading_otl_location_end"] = $loading_otl_location_end;
								$data["loading_otl_coordinate_start"] = $loading_otl_coordinate_start;
								$data["loading_otl_coordinate_end"] = $loading_otl_coordinate_end;
								$data["loading_otl_geofence_start"] = $loading_otl_geofence_start;
								$data["loading_otl_geofence_end"] = $loading_otl_geofence_end;
								$data["loading_otl_duration"] = $loading_otl_duration;
								$data["loading_otl_duration_sec"] = $loading_otl_duration_sec;
								$data["loading_otl_report_status"] = $loading_otl_report_status;
								
								$data["loading_otd_base"] = $loading_otd_base;
								$data["loading_otd_distrep"] = $loading_otd_distrep;
								$data["loading_otd_distrep_name"] = $loading_otd_distrep_name;
								$data["loading_otd_start_date"] = $loading_otd_start_date;
								$data["loading_otd_start_time"] = $loading_otd_start_time;
								$data["loading_otd_end_date"] = $loading_otd_end_date;
								$data["loading_otd_end_time"] = $loading_otd_end_time;
								$data["loading_otd_location_start"] = $loading_otd_location_start;
								$data["loading_otd_location_end"] = $loading_otd_location_end;
								$data["loading_otd_coordinate_start"] = $loading_otd_coordinate_start;
								$data["loading_otd_coordinate_end"] = $loading_otd_coordinate_end;
								$data["loading_otd_geofence_start"] = $loading_otd_geofence_start;
								$data["loading_otd_geofence_end"] = $loading_otd_geofence_end;
								$data["loading_otd_duration"] = $loading_otd_duration;
								$data["loading_otd_duration_sec"] = $loading_otd_duration_sec;
								$data["loading_otd_km_start"] = $loading_otd_km_start;
								$data["loading_otd_km_end"] = $loading_otd_km_end;
								$data["loading_otd_report_status"] = $loading_otd_report_status;
								$data["loading_otl_arrival_date"] = $loading_otl_arrival_date;
								$data["loading_otl_arrival_time"] = $loading_otl_arrival_time;
								$data["loading_distance"] = $loading_distance;
								
								//SELECT REPORT DI TANGGAL REPORT CRON
								$this->dbreport->limit(1);
								$this->dbreport->where("loading_vehicle_device",$ota_vehicle_valid);
								$this->dbreport->where("loading_report_base", $loading_report_base);
								$this->dbreport->where("loading_report_date",$loading_report_date);
								$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);
								$q_report_loading = $this->dbreport->get($dbtable);
								$row_report_loading = $q_report_loading->row();
								$total_row_report_loading = count($row_report_loading);
								
								if($total_row_report_loading > 0){
									//UPDATE JIKA SUDAH DA DATA MOBIL DI TANGGAL REPORT CRON
									printf("5==DATABASE - UPDATE REPORT OTL & OTD : %s %s \r\n", $loading_vehicle_device, $loading_report_date); 
									$this->dbreport->where("loading_vehicle_device", $ota_vehicle_valid);
									$this->dbreport->where("loading_report_base", $loading_report_base);
									$this->dbreport->where("loading_report_date", $loading_report_date);
									//$this->dbreport->where("loading_otd_distrep", $loading_otd_distrep);						
									$this->dbreport->update($dbtable,$data);
								}else{
									if(($total_row_report_otl > 0) || ($total_row_report_otd > 0)){
										if($loading_vehicle_device != ""){
											//ADA DOOR OPEN DI BASE
											printf("5==DATABASE - KENDARAAN ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date);
										}else{
											//TIDAK ADA DOOR OPEN ( TIDAK ADA OTL )
											printf("5==DATABASE - KENDARAN TIDAK ADA DOOR OPEN DI BASE : %s %s \r\n", $ota_nopol, $loading_report_date); 
										}
										
										//INSERT JIKA BELUM ADA DATA MOBIL DI TANGGAL REPORT CRON
										printf("5==DATABASE - INSERT REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date);
										$this->dbreport->insert($dbtable,$data);
										
									}else{
										//TIDAK ADA DATA REPORT OTL & OTD
										printf("5X==SKIP - TIDAK ADA REPORT OTL & OTD : %s %s \r\n", $ota_nopol, $loading_report_date); 
									}
									
								}
								
							}
							else
							{
								$ota_vehicle_valid = "0";
								printf("2==OTA - TIDAK ADA NOPOL VALID  \r\n"); 
							}
						
				printf("=============================================== \r\n");
				
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "LACAKTRANSPRO - OTL OTD POOL FROM OTA";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_dist."
End Distrep   : "."( ".$i." / ".$total_dist." )"."

Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Distrep: ".$total_dist." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		
	}
	
	//others RAI KJL MULTI
	function ota_report_multi_others($date="",$month="",$year="", $distrep="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		$id_balrich = 1032;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_multi_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","desc");
		if((isset($distrep)) && ($distrep != "")){
			$this->dbtransporter->where("distrep_id",$distrep);
		}
		$this->dbtransporter->where("distrep_multi",1); // multi
		$this->dbtransporter->where("distrep_creator <>",$id_balrich); //others
		$this->dbtransporter->where("distrep_flag",0);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","desc");
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$this->dbtransporter->where("droppoint_multi", 1); //multi
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			$total_row_master_report = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
							//khusus untuk ambil data dari jam desc (ex: IDM MEDAN)
							if($row_drop->droppoint_sort == "desc"){ 
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."15:00:00"));
								$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
							}else{
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
								$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
							}
							//khusus idm medan rit 2 // yg diambil kiriman pagi saja 
							if($row_drop->droppoint_id == "10168"){
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
								$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."15:00:00"));
							}
								//print_r($newdatetime." ".$newdatetime2);exit();
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end");
							$this->dbreport->order_by("trip_mileage_start_time","desc");
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->where("trip_mileage_vehicle_user <>",$id_balrich);
							$this->dbreport->where("trip_mileage_geofence_end",$row_drop->droppoint_geofence);
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->result();
							$total_row_report = count($row_report);
							//print_r($row_report);exit();
							/*if($row_drop->droppoint_id == 9830){
									
									print_r($newdatetime." ".$newdatetime2." ".$total_row_report);exit();
								} */
							if($total_row_report > 0){
								for($d=0;$d<$total_row_report; $d++){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report[$d]->trip_mileage_geofence_end, $row_report[$d]->trip_mileage_end_time); 
									$geoalert_vehicle_no = $row_report[$d]->trip_mileage_vehicle_no;
									$geoalert_vehicle_device = $row_report[$d]->trip_mileage_vehicle_id;
									
									$geoalert_time = date("H:i:s", strtotime($row_report[$d]->trip_mileage_end_time));
									$geoalert_name = $row_report[$d]->trip_mileage_geofence_end;
									$geoalert_status = "";
									
									$geoalert_droppoint = $row_drop->droppoint_id;
									$geoalert_droppoint_name = $row_drop->droppoint_geofence;
									$geoalert_distrep = $row_distrep->distrep_id;
									$geoalert_distrep_name = $row_distrep->distrep_name;
									
									//PREPARE INSERT/UPDATE
									//cek data yg sudah diinsert
									$this->dbreport = $this->load->database("balrich_report", true);
									$this->dbreport->where("multi_droppoint", $geoalert_droppoint);	
									$this->dbreport->where("multi_report_date", $newdate);	
									$this->dbreport->where("multi_vehicle_device", $geoalert_vehicle_device);	
									$q_master_report = $this->dbreport->get($dbtable);
									$row_master_report = $q_master_report->row();
									$total_row_master_report = count($row_master_report);
									
									//INSERT/UPDATE KE TABLE REPORT PER TANGGAL REPORT CRON
									unset($data);
									$data["multi_report_date"] = $newdate;
									$data["multi_report_time"] = $geoalert_time;
									$data["multi_vehicle_device"] = $geoalert_vehicle_device;
									$data["multi_vehicle_no"] = $geoalert_vehicle_no;
									$data["multi_report_status"] = $geoalert_status;
									$data["multi_droppoint"] = $geoalert_droppoint;
									$data["multi_droppoint_name"] = $geoalert_droppoint_name;
									$data["multi_distrep"] = $geoalert_distrep;
									$data["multi_distrep_name"] = $geoalert_distrep_name;
									
									if($total_row_master_report > 0){
										printf("4==UPDATE - DATA SUDAH ADA - UPDATE REPORT : %s %s %s \r\n", $geoalert_name, $geoalert_vehicle_no, $geoalert_vehicle_device); 
										$this->dbreport->where("multi_droppoint", $geoalert_droppoint);	
										$this->dbreport->where("multi_report_date", $newdate);	
										$this->dbreport->where("multi_vehicle_device", $geoalert_vehicle_device);	
										$this->dbreport->update($dbtable,$data);
									}else{
										printf("4==INSERT - DATA BELUM ADA - INSERT REPORT : %s %s %s \r\n", $geoalert_name, $geoalert_vehicle_no, $geoalert_vehicle_device); 
										$this->dbreport->insert($dbtable,$data);
									}
								
								}
								
							}
				
				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "LACAKTRANSPRO - OTA REPORT MULTI";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_distrep."
End Distrep   : "."( ".$i." / ".$total_distrep." )"."

Total Droppoint : ".$total_drop."
End Droppoint   : "."( ".$j." / ".$total_drop." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Droppoint: ".$total_drop." \n".
					"Total Distrep: ".$total_distrep." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		 
		
	}
	
	//this new OTA REPORT MULTI
	function ota_report_multi($date="",$month="",$year="", $distrep="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_multi_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","desc");
		if((isset($distrep)) && ($distrep != "")){
			$this->dbtransporter->where("distrep_id",$distrep);
		}
		$this->dbtransporter->where("distrep_multi",1); // multi
		$this->dbtransporter->where("distrep_flag",0);
		$this->dbtransporter->where("distrep_creator",1032); //khusus balrich
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","desc");
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$this->dbtransporter->where("droppoint_multi", 1); //multi
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			$total_row_master_report = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
							//khusus untuk ambil data dari jam desc (ex: IDM JEMBER)
							if($row_drop->droppoint_sort == "desc"){ 
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."03:00:00"));
								$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
							}else{
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
								$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
							}
							
							//khusus dc sat rembang / pool rembang(SMRG)
							if($row_drop->droppoint_id == 9849 || $row_drop->droppoint_id == 12211){
								$vehicle_cdd_smrg = array("002100005919@T5","002100005843@T5","002100005739@T5","002100005918@T5","002100005742@T5",
														  "002100005792@T5","002100004634@T5","002100004429@T5","006100001793@T5");
								$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."12:00:00"));
								$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
							}
							
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end");
							$this->dbreport->order_by("trip_mileage_start_time","desc");
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->where("trip_mileage_geofence_end",$row_drop->droppoint_geofence);
							if($row_drop->droppoint_id == 9849 || $row_drop->droppoint_id == 12211){ //khusus dc sat rembang // pool rembang
								printf("2!==KHUSUS DC SAT REMBANG \r\n");
								$this->dbreport->where_in("trip_mileage_vehicle_id",$vehicle_cdd_smrg);
							}
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->result();
							$total_row_report = count($row_report);
							//print_r($row_report);exit();
							if($total_row_report > 0){
								for($d=0;$d<$total_row_report; $d++){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report[$d]->trip_mileage_geofence_end, $row_report[$d]->trip_mileage_end_time); 
									$geoalert_vehicle_no = $row_report[$d]->trip_mileage_vehicle_no;
									$geoalert_vehicle_device = $row_report[$d]->trip_mileage_vehicle_id;
									
									$geoalert_time = date("H:i:s", strtotime($row_report[$d]->trip_mileage_end_time));
									$geoalert_name = $row_report[$d]->trip_mileage_geofence_end;
									$geoalert_status = "";
									
									$geoalert_droppoint = $row_drop->droppoint_id;
									$geoalert_droppoint_name = $row_drop->droppoint_geofence;
									$geoalert_distrep = $row_distrep->distrep_id;
									$geoalert_distrep_name = $row_distrep->distrep_name;
									
									//PREPARE INSERT/UPDATE
									//cek data yg sudah diinsert
									$this->dbreport = $this->load->database("balrich_report", true);
									$this->dbreport->where("multi_droppoint", $geoalert_droppoint);	
									$this->dbreport->where("multi_report_date", $newdate);	
									$this->dbreport->where("multi_vehicle_device", $geoalert_vehicle_device);	
									$q_master_report = $this->dbreport->get($dbtable);
									$row_master_report = $q_master_report->row();
									$total_row_master_report = count($row_master_report);
									
									//INSERT/UPDATE KE TABLE REPORT PER TANGGAL REPORT CRON
									unset($data);
									$data["multi_report_date"] = $newdate;
									$data["multi_report_time"] = $geoalert_time;
									$data["multi_vehicle_device"] = $geoalert_vehicle_device;
									$data["multi_vehicle_no"] = $geoalert_vehicle_no;
									$data["multi_report_status"] = $geoalert_status;
									$data["multi_droppoint"] = $geoalert_droppoint;
									$data["multi_droppoint_name"] = $geoalert_droppoint_name;
									$data["multi_distrep"] = $geoalert_distrep;
									$data["multi_distrep_name"] = $geoalert_distrep_name;
									
									if($total_row_master_report > 0){
										printf("4==UPDATE - DATA SUDAH ADA - UPDATE REPORT : %s %s %s \r\n", $geoalert_name, $geoalert_vehicle_no, $geoalert_vehicle_device); 
										$this->dbreport->where("multi_droppoint", $geoalert_droppoint);	
										$this->dbreport->where("multi_report_date", $newdate);	
										$this->dbreport->where("multi_vehicle_device", $geoalert_vehicle_device);	
										$this->dbreport->update($dbtable,$data);
									}else{
										printf("4==INSERT - DATA BELUM ADA - INSERT REPORT : %s %s %s \r\n", $geoalert_name, $geoalert_vehicle_no, $geoalert_vehicle_device); 
										$this->dbreport->insert($dbtable,$data);
									}
								
								}
								
							}
				
				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - OTA REPORT MULTI";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$newdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$newdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Distrep : ".$total_distrep."
End Distrep   : "."( ".$i." / ".$total_distrep." )"."

Total Droppoint : ".$total_drop."
End Droppoint   : "."( ".$j." / ".$total_drop." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$newdate." \n".
					"Total Droppoint: ".$total_drop." \n".
					"Total Distrep: ".$total_distrep." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		return; 
		 
		
	}
	
	function all_ota_report_balrich($date="",$month="",$year="",$base="",$distrep="")
	{
		$this->ota_report_new($date,$month,$year);
		$this->ota_report_multi($date,$month,$year,$distrep);
		$this->create_xml($date,$month,$year);
	}
	
	function all_otl_otd_balrich()
	{
		$this->otl_otd_report_dc2_from_ota();
		$this->otl_otd_report_pool_from_ota();
	}
	
	function all_ota_report_others($date="",$month="",$year="")
	{
		$this->ota_report_others($date,$month,$year);
		$this->otl_otd_report_pool_from_ota_others($date,$month,$year,$base);
		$this->otl_otd_report_dc2_from_ota_others($date,$month,$year,$base);
		$this->ota_report_multi_others($date,$month,$year,$distrep);
		$this->create_xml_others($date,$month,$year);
	}
	
	function target_nextmonth_all()
	{
		ini_set('memory_limit', '3G');
		$now = date("Y-m-d");
		$nowtime = date("Y-m-d", (strtotime($now)));
		$nowday = date("d", (strtotime($nowtime)));
		
		//manual
		/*$nowtime = date("Y-m-d", (strtotime("2020-04-01")));
		$nowday = date("d", (strtotime("2020-04-01")));*/
		
		if($nowday != "01"){
			printf("===BUKAN TANGGAL 01:  %s \r\n", $nowtime); 
			return;
		}
		
		$this->targettime_nextmonth_ota($type="", $date="",$month="",$year="");
		$this->targettime_nextmonth_otl($date="",$month="",$year="");
		$this->targettime_nextmonth_otd($date="",$month="",$year="");
	}
	
	function sync_geofence($source, $target, $user, $limit, $lastid)
	{
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //webtracking_balrich
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by("geofence_id","asc");
		$this->db->where("geofence_id >", $lastid);
		$this->db->where("geofence_user",$user);
		//$this->db->where("geofence_status",1);
		$this->db->limit($limit);
		$q = $this->db->get("geofence");
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d["geofence_id"];
			$this->dbtarget->insert("geofence", $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_geofence_id($source, $target, $sourcetable, $targettable, $id)
	{
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //webtracking_balrich
		
		//php -c /etc/ /home/lacakmobil/public_html/balrich.lacak-mobil.com/public/index.php tools_balrich sync_geofence_id webtracking_balrich webtracking geofence geofence 599032
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by("geofence_id","asc");
		$this->db->where("geofence_id",$id);
		$this->db->where("geofence_status",1);
		$this->db->limit(1);
		$q = $this->db->get($sourcetable);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d["geofence_id"];
			$this->dbtarget->insert($targettable, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_geofence_vehicle($source, $target, $sourcetable, $targettable, $device, $host, $userid, $limit, $lastid)
	{
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //webtracking_balrich
		
		//php -c /etc/ /home/lacakmobil/public_html/balrich.lacak-mobil.com/public/index.php tools_balrich sync_geofence_vehicle webtracking_balrich webtracking geofence geofence 002100005919 T5 1032 100 0
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by("geofence_id","asc");
		$this->db->where("geofence_id >", $lastid);
		$this->db->where("geofence_status",1);
		$this->db->where("geofence_user",$userid);
		$this->db->where("geofence_vehicle",$device."@".$host);
		$this->db->limit($limit);
		$q = $this->db->get($sourcetable);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d["geofence_id"];
			$this->dbtarget->insert($targettable, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_geofence_custom($source, $target, $sourcetable, $targettable, $userid, $limit, $lastid)
	{
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //webtracking_balrich
		
		//php -c /etc/ /home/lacakmobil/public_html/balrich.lacak-mobil.com/public/index.php tools_balrich sync_geofence_custom webtracking_balrich webtracking geofence geofence 1032 100 0
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by("geofence_id","asc");
		$this->db->where("geofence_id >", $lastid);
		$this->db->where("geofence_status",1);
		$this->db->where("geofence_user",$userid);
		$this->db->where("geofence_vehicle","002100005843@T5");
		$this->db->limit($limit);
		$q = $this->db->get($sourcetable);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total); 
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d["geofence_id"];
			$this->dbtarget->insert($targettable, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_table($source, $target, $source_table, $target_table, $fieldid, $limit, $lastid)
	{
		ini_set('memory_limit', '5G');
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //dblocation_balrich
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by($fieldid,"asc");
		$this->db->where($fieldid." >", $lastid);
		$this->db->limit($limit);
		$q = $this->db->get($source_table);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d[$fieldid];
			$this->dbtarget->insert($target_table, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_table_desc($source, $target, $source_table, $target_table, $fieldid, $limit, $lastid)
	{
		ini_set('memory_limit', '3G');
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //dblocation_balrich
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by($fieldid,"desc");
		$this->db->where($fieldid." <", $lastid);
		$this->db->limit($limit);
		$q = $this->db->get($source_table);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d[$fieldid];
			$this->dbtarget->insert($target_table, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_table_byid($source, $target, $source_table, $target_table, $fieldid, $id)
	{
		ini_set('memory_limit', '3G');
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //dblocation_balrich
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by($fieldid,"asc");
		$this->db->where($fieldid, $id);
		$this->db->limit(1);
		$q = $this->db->get($source_table);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d[$fieldid];
			$this->dbtarget->insert($target_table, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	//send email target history
	function sendmail_targethist()
	{	
		$start_time = date("Y-m-d H:i:s");
		
		printf("PROSES TARGET OTA HISTORY \r\n"); 
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$nowdate = date("Y-m-d");
		$this->dbtransporter->order_by("email_hist_id", "asc");
		$this->dbtransporter->where("email_hist_flag", 0);
		$this->dbtransporter->where("email_hist_notif", 0);
		$q = $this->dbtransporter->get("droppoint_target_history_email");
		$rows = $q->result();
		$total = count($rows);
		printf("GET DATA : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			printf("PROSES DATA : %s \r\n", $row->email_hist_name);
			
		//Send Email
		$id = $row->email_hist_id;
		$title = $row->email_hist_name;
		$note = $row->email_hist_note;
		$distrep_name = $row->email_hist_distrep_name;
		$company_email = $row->email_hist_company_email;
		$company_cc = $row->email_hist_company_cc;
		$creator_name = $row->email_hist_creator_name;
		$creator_time = date("d-m-Y H:i:s", strtotime($row->email_hist_creator_time));
		$periode = date("d-m-Y", strtotime($row->email_hist_startdate))." ".date("d-m-Y", strtotime($row->email_hist_enddate));
		
		unset($mail);
		$mail['subject'] =  $title;
		$mail['message'] = 
"

Berikut informasi perubahan target OTA :

Distrep	: ".$distrep_name."
Periode : ".$periode."
Dibuat oleh : ".$creator_name."
Waktu : ".$creator_time."


Terima Kasih
Admin System
http://www.balrichlog.com/

";
			$mail['dest'] = $company_email;
			$mail['bcc'] = $company_cc;
			$mail['sender'] = "no-reply@balrichlog.com";
			lacakmobilmail($mail);
		
			printf("SEND OK  : %s \r\n", $title); 
			
			//update status alert
			$this->dbtransporter->select("email_hist_id");
			$this->dbtransporter->limit(1);
			$this->dbtransporter->where('email_hist_id', $id);
			$q = $this->dbtransporter->get('droppoint_target_history_email');
			$row = $q->row();
			
			if (count($row)>0)
			{
				$data_status["email_hist_notif"] = 1;
				$this->dbtransporter->where("email_hist_id", $id);
				$this->dbtransporter->update("droppoint_target_history_email", $data_status);
			  
			}
			printf("==================DONE============== \r\n"); 
			
		}
		
		$this->dbtransporter->cache_delete_all();
		$finish_time = date("d-m-Y H:i:s");
		printf("==============SELESAI=================="." ".$finish_time);
		
	}
	
	function sync_vehicle($user="")
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("d-m-Y H:i:s");
	
		
		printf("PROSES SYNC VEHICLE \r\n");
		$this->dbtransporter_balrich = $this->load->database("transporter_balrich", TRUE); //transporter_balrich
		$this->dbtransporter_balrich->order_by("sync_id","asc");
		$this->dbtransporter_balrich->where("sync_flag",0);
		if($user != ""){
			$this->dbtransporter_balrich->where("sync_user",$user);	
		}
		$q = $this->dbtransporter_balrich->get("sync_vehicle_user");
		$rows = $q->result();
		$total = count($rows);
		printf("GET USER SYNC : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
			
			printf("PROCESS NUMBER : %s \r\n", ++$i." of ".$total);
			printf("GET USER : %s %s \r\n", $row->sync_user, $row->sync_user_name);
			printf("------------------------------------------------- \r\n"); 
			
			$offset_sync = 0;
			$j = 0;
			$this->dbwebtracking_ultron = $this->load->database("webtracking_ultron", true);
			$this->dbwebtracking_ultron->order_by("vehicle_id","desc");
			$this->dbwebtracking_ultron->where("vehicle_user_id",$row->sync_user);
			//$this->dbwebtracking_ultron->limit(1);
			$q_vehicle = $this->dbwebtracking_ultron->get("vehicle");
			$rows_vehicle = $q_vehicle->result();
			$total_vehicle = count($rows_vehicle);
			//print_r($rows_vehicle);
			foreach($rows_vehicle as $row_vehicle)
			{
			
				if (($j+1) < $offset_sync)
				{
					$j++;
					continue;
				}
				
				printf("PROCESS VEHICLE	 : %s \r\n", ++$j." of ".$total_vehicle);
				printf("GET VEHICLE  : %s %s %s %s \r\n", $row_vehicle->vehicle_id, $row_vehicle->vehicle_device, $row_vehicle->vehicle_no, $row->sync_user_name);
				
				unset($data);
				$this->dbwebtracking_balrich = $this->load->database("webtracking_balrich", true);
				$this->dbwebtracking_balrich->order_by("vehicle_id", "desc");
				$this->dbwebtracking_balrich->where("vehicle_device", $row_vehicle->vehicle_device);
				$this->dbwebtracking_balrich->where("vehicle_user_id", $row_vehicle->vehicle_user_id);
				$this->dbwebtracking_balrich->limit(1);
				$q_vehicle_exists = $this->dbwebtracking_balrich->get("vehicle");
				$row_vehicle_exists = $q_vehicle_exists->row();
				$datas = $q_vehicle_exists->result_array();
				
				
				//jika sudah ada, hanya update port (vehicle info)
				if (count($row_vehicle_exists)>0)
				{
					printf("UPDATE VEHICLE IN DB MASTER BALRICH : %s, %s, %s, %s \r\n", $row_vehicle->vehicle_id, $row_vehicle->vehicle_device, $row_vehicle->vehicle_no, $row->sync_user_name); 
					
					if($row->sync_update_type == 1){
						printf("VEHICLE USER UPDATE SEMUA \r\n"); 
						$data["vehicle_no"] = $row_vehicle->vehicle_no;
						$data["vehicle_name"] = $row_vehicle->vehicle_name;
						$data["vehicle_status"] = $row_vehicle->vehicle_status;
					}
					$data["vehicle_active_date2"] = $row_vehicle->vehicle_active_date2;
					$data["vehicle_card_no"] = $row_vehicle->vehicle_card_no;
					$data["vehicle_operator"] = $row_vehicle->vehicle_operator;
					$data["vehicle_active_date"] = $row_vehicle->vehicle_active_date;
					$data["vehicle_active_date1"] = $row_vehicle->vehicle_active_date1;
					
					$data["vehicle_image"] = $row_vehicle->vehicle_image;
					$data["vehicle_created_date"] = $row_vehicle->vehicle_created_date;
					$data["vehicle_type"] = $row_vehicle->vehicle_type;
					$data["vehicle_autorefill"] = $row_vehicle->vehicle_autorefill;
					$data["vehicle_maxspeed"] = $row_vehicle->vehicle_maxspeed;
					$data["vehicle_maxparking"] = $row_vehicle->vehicle_maxparking;
					$data["vehicle_odometer"] = $row_vehicle->vehicle_odometer;
					
					$data["vehicle_payment_type"] = $row_vehicle->vehicle_payment_type;
					$data["vehicle_payment_amount"] = $row_vehicle->vehicle_payment_amount;
					$data["vehicle_fuel_capacity"] = $row_vehicle->vehicle_fuel_capacity;
					$data["vehicle_info"] = $row_vehicle->vehicle_info;
					$data["vehicle_sales"] = $row_vehicle->vehicle_sales;
					$data["vehicle_teknisi_id"] = $row_vehicle->vehicle_teknisi_id;
					
					$data["vehicle_tanggal_pasang"] = $row_vehicle->vehicle_tanggal_pasang;
					$data["vehicle_imei"] = $row_vehicle->vehicle_imei;
					$data["vehicle_dbhistory"] = $row_vehicle->vehicle_dbhistory;
					$data["vehicle_dbhistory_name"] = $row_vehicle->vehicle_dbhistory_name;
					$data["vehicle_dbname_live"] = $row_vehicle->vehicle_dbname_live;
					$data["vehicle_isred"] = $row_vehicle->vehicle_isred;
					$data["vehicle_modem"] = $row_vehicle->vehicle_modem;
					
					$this->dbwebtracking_balrich->where("vehicle_device", $row_vehicle->vehicle_device);
					$this->dbwebtracking_balrich->where("vehicle_user_id", $row_vehicle->vehicle_user_id);
					$this->dbwebtracking_balrich->limit(1);
					$this->dbwebtracking_balrich->update("vehicle",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT VEHICLE IN DB MASTER BALRICH : %s, %s, %s, %s \r\n", $row_vehicle->vehicle_id, $row_vehicle->vehicle_device, $row_vehicle->vehicle_no, $row->sync_user_name); 
					
					//only insert 
					if($row->sync_update_type == 1){
						$data["vehicle_id"] = $row_vehicle->vehicle_id;
					}
					$data["vehicle_user_id"] = $row_vehicle->vehicle_user_id;
					$data["vehicle_device"] = $row_vehicle->vehicle_device;
					$data["vehicle_no"] = $row_vehicle->vehicle_no;
					$data["vehicle_name"] = $row_vehicle->vehicle_name;
					$data["vehicle_group"] = $row_vehicle->vehicle_group;
					$data["vehicle_company"] = $row_vehicle->vehicle_company;
					$data["vehicle_status"] = $row_vehicle->vehicle_status;
				
					$data["vehicle_active_date2"] = $row_vehicle->vehicle_active_date2;
					$data["vehicle_card_no"] = $row_vehicle->vehicle_card_no;
					$data["vehicle_operator"] = $row_vehicle->vehicle_operator;
					$data["vehicle_active_date"] = $row_vehicle->vehicle_active_date;
					$data["vehicle_active_date1"] = $row_vehicle->vehicle_active_date1;
					
					$data["vehicle_image"] = $row_vehicle->vehicle_image;
					$data["vehicle_created_date"] = $row_vehicle->vehicle_created_date;
					$data["vehicle_type"] = $row_vehicle->vehicle_type;
					$data["vehicle_autorefill"] = $row_vehicle->vehicle_autorefill;
					$data["vehicle_maxspeed"] = $row_vehicle->vehicle_maxspeed;
					$data["vehicle_maxparking"] = $row_vehicle->vehicle_maxparking;
					$data["vehicle_odometer"] = $row_vehicle->vehicle_odometer;
					
					$data["vehicle_payment_type"] = $row_vehicle->vehicle_payment_type;
					$data["vehicle_payment_amount"] = $row_vehicle->vehicle_payment_amount;
					$data["vehicle_fuel_capacity"] = $row_vehicle->vehicle_fuel_capacity;
					$data["vehicle_info"] = $row_vehicle->vehicle_info;
					$data["vehicle_sales"] = $row_vehicle->vehicle_sales;
					$data["vehicle_teknisi_id"] = $row_vehicle->vehicle_teknisi_id;
					
					$data["vehicle_tanggal_pasang"] = $row_vehicle->vehicle_tanggal_pasang;
					$data["vehicle_imei"] = $row_vehicle->vehicle_imei;
					$data["vehicle_dbhistory"] = $row_vehicle->vehicle_dbhistory;
					$data["vehicle_dbhistory_name"] = $row_vehicle->vehicle_dbhistory_name;
					$data["vehicle_dbname_live"] = $row_vehicle->vehicle_dbname_live;
					$data["vehicle_isred"] = $row_vehicle->vehicle_isred;
					$data["vehicle_modem"] = $row_vehicle->vehicle_modem;
					
					$this->dbwebtracking_balrich->insert("vehicle", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			}
		}
		
		$this->dbwebtracking_ultron->close();
		$this->dbwebtracking_ultron->cache_delete_all();
		
		$this->dbwebtracking_balrich->close();
		$this->dbwebtracking_balrich->cache_delete_all();
		
		$this->dbtransporter_balrich->close();
		$this->dbtransporter_balrich->cache_delete_all();
		
		$finish_time = date("d-m-Y H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		return;   
		
	}
	
	//update coordinate by cron ota_report
	function coordinate_from_ota($distrep="", $date="",$month="",$year="")
	{
		$offset = 0;
		$offset_distrep = 0;
		$offset_drop = 0;
		$total_drop = 0;
		
		$i = 0;
		$j = 0;
		$report = "inout_geofence_";
		$report_opr = "operasional_";
		$report_door = "door_";
		$start_time = date("d-m-Y H:i:s");
		
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		//print_r($newdatetime." ".$newdatetime2." ".$m1);exit();
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_opr = $report_opr."januari_".$year;
			$dbtable_door = $report_door."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_opr = $report_opr."februari_".$year;
			$dbtable_door = $report_door."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_opr = $report_opr."maret_".$year;
			$dbtable_door = $report_door."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_opr = $report_opr."april_".$year;
			$dbtable_door = $report_door."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_opr = $report_opr."mei_".$year;
			$dbtable_door = $report_door."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_opr = $report_opr."juni_".$year;
			$dbtable_door = $report_door."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_opr = $report_opr."juli_".$year;
			$dbtable_door = $report_door."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_opr = $report_opr."agustus_".$year;
			$dbtable_door = $report_door."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_opr = $report_opr."september_".$year;
			$dbtable_door = $report_door."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_opr = $report_opr."oktober_".$year;
			$dbtable_door = $report_door."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_opr = $report_opr."november_".$year;
			$dbtable_door = $report_door."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_opr = $report_opr."desember_".$year;
			$dbtable_door = $report_door."desember_".$year;
			break;
		}
		
		$this->dbreport = $this->load->database("balrich_report", true);
		printf("0==START CRON \r\n"); 
		//select distrep
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_id","asc");
		$this->dbtransporter->where("distrep_creator",1032);
		$this->dbtransporter->where("distrep_flag",0);
		$this->dbtransporter->where("distrep_id",$distrep);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rows_distrep = $qd->result();
		$total_distrep = count($rows_distrep);
		printf("1==DISTREP - TOTAL DISTREP : %s \r\n", $total_distrep); 
		
		foreach($rows_distrep as $row_distrep)
		{
			
			if (($i+1) < $offset_distrep)
			{
				$i++;
				continue;
			}
			
			printf("1==DISTREP - PROCESS NUMBER DISTREP	 : %s \r\n", ++$i." of ".$total_distrep);
			printf("1==DISTREP - PROSES DISTREP   : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			
			printf("1==DISTREP - GET DROPPOINT BY DISTREP : %s %s \r\n", $row_distrep->distrep_id, $row_distrep->distrep_name); 
			$this->dbtransporter->order_by("droppoint_id","asc");
			$this->dbtransporter->where("droppoint_flag",0);
			$this->dbtransporter->where("droppoint_distrep", $row_distrep->distrep_id);
			$q_drop = $this->dbtransporter->get("droppoint");
			$rows_drop = $q_drop->result();
			$total_drop = count($rows_drop);
			printf("1==DISTREP - TOTAL DROPPOINT : %s \r\n", $total_drop);
			printf("=============================================== \r\n");
			$j=0;
			$geoalert_vehicle = "";
			$geoalert_time = "";
			$geoalert_date = "";
			$geoalert_status = "";
			$geoalert_km = "";
			$geoalert_koord = "";
			
			$geoalert_door_vehicle = "";
			$geoalert_door_time = "";
			$geoalert_door_name = "";
			$geoalert_door_status = "";
			
			$total_georeport = 0;
			$total_geoalert = 0;
			
			
			foreach($rows_drop as $row_drop)
			{
				
				if (($j+1) < $offset_drop)
				{
					$j++;
					continue;
				}
				
				printf("2==DROPPOINT - PROCESS NUMBER DROPPOINT : %s %s %s \r\n", ++$j." of ".$total_drop , "distrep: ". $i." of ".$total_distrep, $newdate);
				printf("2==DROPPOINT - PROSES DROPPOINT : %s %s \r\n", $row_drop->droppoint_id, $row_drop->droppoint_name); 
				
							//select per tanggal dari table operasional report
				
							//select berdasarkan like nama droppoint di tanggal yg dipilih (search level 1)
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end,trip_mileage_cummulative_mileage,trip_mileage_coordinate_end");
							$this->dbreport->order_by("trip_mileage_start_time","asc");
							$this->dbreport->where("trip_mileage_vehicle_id",$row_distrep->distrep_vehicle_device);
							$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
							$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
							$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
							$this->dbreport->limit(1);
							$q_report = $this->dbreport->get($dbtable_opr);
							$row_report = $q_report->row();
							$total_row_report = count($row_report);
							if($total_row_report > 0){
								printf("3==GEOALERT - ADA GEOALERT LEVEL 1 (ENGINE OFF) !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
								$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
								$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
								$geoalert_name = $row_report->trip_mileage_geofence_end;
								$geoalert_status = "OPR";
								$geoalert_km = $row_report->trip_mileage_cummulative_mileage;
								$geoalert_koord = $row_report->trip_mileage_coordinate_end;
								
								if($geoalert_koord != ""){
									unset($data_koord);
									//update master droppoint
									$data_koord["droppoint_koord"] = $geoalert_koord;
									
									printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
									$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
									$this->dbtransporter->limit(1);	
									$this->dbtransporter->update("droppoint",$data_koord);
								}else{
									printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
								}
					
							}else{
								//select per tanggal dari table DOOR REPORT
								//cari berdasarkan DOOR OPEN di area droppoint (ambil start_time nya) (search by door) search level 2
								$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start,door_cumm_mileage,door_coordinate_start");
								$this->dbreport->order_by("door_start_time","asc");
								$this->dbreport->where("door_status","OPEN");
								$this->dbreport->where("door_vehicle_device",$row_distrep->distrep_vehicle_device);
								$this->dbreport->where("door_start_time >=",$newdatetime);
								$this->dbreport->where("door_start_time <=",$newdatetime2);
								$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
								$this->dbreport->limit(1);
								$q_report = $this->dbreport->get($dbtable_door);
								$row_report = $q_report->row();
								$total_row_report = count($row_report);
								if($total_row_report > 0){
									printf("3==GEOALERT - ADA GEOALERT LEVEL 2 (DOOR OPEN) !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
									$geoalert_vehicle = $row_report->door_vehicle_no;
									$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
									$geoalert_name = $row_report->door_geofence_start;
									$geoalert_status = "DOOR";
									$geoalert_km = $row_report->door_cumm_mileage;
									$geoalert_koord = $row_report->door_coordinate_start;
									
									if($geoalert_koord != ""){
										unset($data_koord);
										//update master droppoint
										$data_koord["droppoint_koord"] = $geoalert_koord;
										
										printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
										$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
										$this->dbtransporter->limit(1);	
										$this->dbtransporter->update("droppoint",$data_koord);
									}else{
										printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
									}
								}else{
									
									//jika tidak ada cari random mobil yg open di droppoint // random mobil - search level 3
									$this->dbreport->select("door_vehicle_no,door_vehicle_id,door_vehicle_device,door_start_time,door_geofence_start,door_cumm_mileage,door_coordinate_start");
									$this->dbreport->order_by("door_start_time","asc");
									$this->dbreport->where("door_status","OPEN");
									$this->dbreport->where("door_start_time >=",$newdatetime);
									$this->dbreport->where("door_start_time <=",$newdatetime2);
									$this->dbreport->like("door_geofence_start",$row_drop->droppoint_name);
									$this->dbreport->limit(1);
									$q_report = $this->dbreport->get($dbtable_door);
									$row_report = $q_report->row();
									$total_row_report = count($row_report);
									
									if($total_row_report > 0){
										printf("3==GEOALERT - ADA GEOALERT LEVEL 3 (DOOR OPEN) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->door_geofence_start, $row_report->door_start_time); 
										$geoalert_vehicle = $row_report->door_vehicle_no;
										$geoalert_time = date("H:i:s", strtotime($row_report->door_start_time));
										$geoalert_name = $row_report->door_geofence_start;
										$geoalert_status = "DOOR";
										$geoalert_km = $row_report->door_cumm_mileage;
										$geoalert_koord = $row_report->door_coordinate_start;
										
										if($geoalert_koord != ""){
											unset($data_koord);
											//update master droppoint
											$data_koord["droppoint_koord"] = $geoalert_koord;
											
											printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
											$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
											$this->dbtransporter->limit(1);	
											$this->dbtransporter->update("droppoint",$data_koord);
										}else{
											printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
										}
								
									}else{
										//jika tidak ada cari random mobil yg off di droppoint // random mobil - search level 4
										$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_vehicle_id,trip_mileage_end_time,trip_mileage_geofence_end,trip_mileage_cummulative_mileage,trip_mileage_coordinate_end");
										$this->dbreport->order_by("trip_mileage_start_time","asc");
										$this->dbreport->where("trip_mileage_start_time >=",$newdatetime);
										$this->dbreport->where("trip_mileage_start_time <=",$newdatetime2);
										$this->dbreport->like("trip_mileage_geofence_end",$row_drop->droppoint_name);
										$this->dbreport->limit(1);
										$q_report = $this->dbreport->get($dbtable_opr);
										$row_report = $q_report->row();
										$total_row_report = count($row_report);
										
										if($total_row_report > 0){
											printf("3==GEOALERT - ADA GEOALERT LEVEL 4 (ENGINE OFF) - NOPOL RANDOM !!!! : %s %s \r\n", $row_report->trip_mileage_geofence_end, $row_report->trip_mileage_end_time); 
											$geoalert_vehicle = $row_report->trip_mileage_vehicle_no;
											$geoalert_time = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
											$geoalert_name = $row_report->trip_mileage_geofence_end;
											$geoalert_status = "OPR";
											$geoalert_km = $row_report->trip_mileage_cummulative_mileage;
											$geoalert_koord = $row_report->trip_mileage_coordinate_end;
											
											if($geoalert_koord != ""){
												unset($data_koord);
												//update master droppoint
												$data_koord["droppoint_koord"] = $geoalert_koord;
												
												printf("3+==GEOALERT - UPDATE KOORDINATE DROPPOINT : %s %s \r\n", $geoalert_name, $geoalert_koord); 
												$this->dbtransporter->where("droppoint_id", $row_drop->droppoint_id);	
												$this->dbtransporter->limit(1);	
												$this->dbtransporter->update("droppoint",$data_koord);
											}else{
												printf("3+==GEOALERT - KOORDINATE KOSONG : %s %s \r\n", $geoalert_name, $geoalert_koord); 
											}
											
										}else{
											//tidak ada semuanya
											printf("3X==TIDAK ADA SEMUA GEOALERT !!!"); 
											$geoalert_vehicle = "";
											$geoalert_time = date("H:i:s", strtotime("00:00:00"));
											$geoalert_name = "TIDAK ADA SEMUA GEOALERT - RESET";
											$geoalert_status = "";
											$geoalert_km = "";
										}
									}
									
								}
								
							}
					
					
				printf("=============================================== \r\n"); 
				
			}
			
				//printf("4==FINISH SYNC GEO ALERT : ID GEOFENCE %s, ID GEOALERT %s, GEOALERT TIME %s \r\n", $row_geoalert->geoalert_geofence, $row_geoalert->geoalert_id, $row_geoalert->geoalert_time); 
				printf("4==FINISH PER DISTREP "); 
				printf("============================================ \r\n"); 
			
		}
		
		printf("5==DONE !!! "); 
		printf("============================================ \r\n"); 
		
		$finish_time = date("d-m-Y H:i:s");
		
		return; 
		 
		
	}
	
	function create_xml($date="",$month="",$year="")
	{
		$start_time = date("d-m-Y H:i:s");
		printf("==START : %s \r\n", $start_time); 
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		//print_r($newdatetime." ".$newdatetime2." ".$m1);exit();
		
		$this->dbtransporter = $this->load->database("transporter_balrich",true);
		$this->dbreport = $this->load->database("balrich_report",true);
		$userid = 1032;
	
		$sdate_only = date("d", strtotime($newdate));
		$sdate_zone = date("Y-m-d", strtotime($newdate));
		
		// get data monthly report
		$report = "inout_geofence_";
		$report_xml = "xml_";
		
		$data = $this->get_monthly_report($sdate_zone,$sdate_zone);
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_xml = $report_xml."januari_".$year;
			$month_name = "Januari";
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_xml = $report_xml."februari_".$year;
			$month_name = "Februari";
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_xml = $report_xml."maret_".$year;
			$month_name = "Maret";
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_xml = $report_xml."april_".$year;
			$month_name = "April";
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_xml = $report_xml."mei_".$year;
			$month_name = "Mei";
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_xml = $report_xml."juni_".$year;
			$month_name = "Juni";
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_xml = $report_xml."juli_".$year;
			$month_name = "Juli";
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_xml = $report_xml."agustus_".$year;
			$month_name = "Agustus";
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_xml = $report_xml."september_".$year;
			$month_name = "September";
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_xml = $report_xml."oktober_".$year;
			$month_name = "Oktober";
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_xml = $report_xml."november_".$year;
			$month_name = "November";
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_xml = $report_xml."desember_".$year;
			$month_name = "Desember";
			break;
		}
		
		//select table ota report
		$this->dbtransporter->select("droppoint_id,droppoint_name,droppoint_code_real,droppoint_koord,droppoint_km,
									  distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,distrep_time,
									  distrep_sat_status,distrep_sat_distrep_code,distrep_sat_distrep_name,distrep_sat_outlet_code,distrep_sat_outlet_name,
									  plant_code_real,plant_code
									");
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("droppoint_creator",$userid);
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("distrep_flag",0);
		$this->dbtransporter->where("droppoint_distrep <>",0);
		//$this->dbtransporter->where("plant_code_real",$plant);
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
		$this->dbtransporter->join("droppoint_plant", "distrep_plant = plant_id", "left");
		//$this->dbtransporter->limit(100);
		$q_droppoint = $this->dbtransporter->get("droppoint");
		$droppoint = $q_droppoint->result();
		
		//total data ota
		if($q_droppoint->num_rows == 0)
		{
			print_r("No Data Droppoint MASTER");
			exit;
		}
		$total_droppoint = count($droppoint);
		printf("==TOTAL DROPPOINT : %s \r\n", count($droppoint));
		
		//select ota berdasarkan master droppoint
		for($i=0; $i < count($droppoint); $i++){ 
			
			$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
			$q_report = $this->dbreport->get($dbtable);
			$row_report = $q_report->row();
			
				unset($data_xml);
				$data_xml["DroppointID"] = $droppoint[$i]->droppoint_id;
				$data_xml["Creator"] = $userid;
				$data_xml["PlantCode"] = $droppoint[$i]->plant_code_real;
				$data_xml["PlantName"] = $droppoint[$i]->plant_code;
				
				//jika toko sat
				if(isset($droppoint) && ($droppoint[$i]->distrep_sat_status == 1)){
					$data_xml["OutletCode"] = $droppoint[$i]->distrep_sat_outlet_code;
					$data_xml["DistrepCode"] = $droppoint[$i]->distrep_sat_distrep_code;
					$data_xml["DistrepName"] = $droppoint[$i]->distrep_sat_distrep_name;
					$data_xml["OutletName"] = $droppoint[$i]->distrep_sat_outlet_name;
					$data_xml["SubOutletName"] = $droppoint[$i]->droppoint_name;
					$data_xml["SubOutletCode"] = $droppoint[$i]->droppoint_code_real;
				}
				else
				{
					$data_xml["OutletCode"] = $droppoint[$i]->droppoint_code_real;
					$data_xml["DistrepCode"] = $droppoint[$i]->distrep_code;
					$data_xml["DistrepName"] = $droppoint[$i]->distrep_name;
					$data_xml["OutletName"] = $droppoint[$i]->droppoint_name;
					$data_xml["SubOutletName"] = "-";
					$data_xml["SubOutletCode"] = "-";
				}
				
				$data_xml["Month"] = strtoupper($month_name);
				$data_xml["Year"] = $year;
				$data_xml["Transporter"] = "BALRICH";
				$data_xml["Coordinat"] = $droppoint[$i]->droppoint_koord;
				if($droppoint[$i]->droppoint_km != ""){
					$droppoint_km = round(($droppoint[$i]->droppoint_km/1000), 2, PHP_ROUND_HALF_UP);
				}else{
					$droppoint_km = 0;
				}
				$data_xml["KM"] = $droppoint_km;
				
				$TargetOta = "-";
				$this->dbtransporter->limit(1);
				$this->dbtransporter->order_by("target_startdate", "asc");							
				$this->dbtransporter->select("target_startdate,target_enddate,target_time");
				$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
				$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
				$this->dbtransporter->where("target_startdate >=",$newdate);
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
				
				$data_xml["TargetOta"] = $TargetOta;
				
				//daily ota
				for($j=0; $j < count($data); $j++){
							$georeport_time_alert = "";
							$georeport_time_alert_print = "-";
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
							
							$distrep_time = $droppoint[$i]->distrep_time;
							
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
							//$reportdate = $sdate_day."-".date("d-m-Y", strtotime($sdate_zone));
							$reportdate = date("d-m-Y", strtotime($sdate_zone));
							
						
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
							
							$sdate_type_selasa_jumat_minggu = $data[$j]->monthly_type_257; //khusus RO cirebon (selasa jumat sabtu) - kode 20
							//new from jwk medan
							$sdate_type_mdn = $data[$j]->monthly_type_mdn; //khusus Medan
							$sdate_type_mdn2 = $data[$j]->monthly_type_mdn2; //khusus Medan
							$sdate_type_3d = $data[$j]->monthly_type_3d; //khusus Medan (Jwk 3 hari sekali)
							
							$sdate_type_67 = $data[$j]->monthly_type_67; //khusus sabtu minggu - kode 22
						
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
												$georeport_time_alert_print = "-";
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
										
										//edit config time zone WITA / WIT
										if($distrep_time > 0){
											$time_zone = new DateTime($georeport_time_alert);
											$time_zone->add(new DateInterval('PT'.$distrep_time.'H'));
											$time_zone_ota = $time_zone->format('H:i:s');
													
											$georeport_time_alert = date("H:i:s", strtotime($time_zone_ota));
											$georeport_time_alert_print = date("H:i", strtotime($time_zone_ota));
										}
										
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
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "13"){ //3D - A
										$sdate_type = $sdate_type_3d;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "14"){ //3D - B
										$sdate_type = $sdate_type_3d;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "15"){ //3D - C
										$sdate_type = $sdate_type_3d;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "20"){ //khusus RO Cirebon NEW
										$sdate_type = $sdate_type_selasa_jumat_minggu;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "22"){ //khusus 67
										$sdate_type = $sdate_type_67;
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
														$georeport_time_alert_print = "-";
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
												
												//edit config time zone WITA / WIT
												if($distrep_time > 0){
													$time_zone = new DateTime($georeport_time_alert);
													$time_zone->add(new DateInterval('PT'.$distrep_time.'H'));
													$time_zone_ota = $time_zone->format('H:i:s');
															
													$georeport_time_alert = date("H:i:s", strtotime($time_zone_ota));
													$georeport_time_alert_print = date("H:i", strtotime($time_zone_ota));
												}
												
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
							
							$ReportDate = date("Y-m-d", strtotime($reportdate));
							$TimeOTA = $georeport_time_alert_print;
							
							$data_xml["ReportDate"] = $ReportDate;
							$data_xml["TimeOTA"] = $TimeOTA;
							$data_xml["Modified"] = date("Y-m-d H:i:s");
							
							//cek data OTA XML
							$this->dbreport->limit(1);
							$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
							$this->dbreport->where("ReportDate",$ReportDate);
							$this->dbreport->where("Creator",$userid);
							$q_reportxml = $this->dbreport->get($dbtable_xml);
							$row_reportxml = $q_reportxml->row();
							$total_row_reportxml = count($row_reportxml);
							
							//update
							if($total_row_reportxml > 0){
								$this->dbreport->limit(1);
								$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
								$this->dbreport->where("ReportDate",$ReportDate);
								$this->dbreport->where("Creator",$userid);
								$this->dbreport->update($dbtable_xml,$data_xml);
								printf("==UPDATE - OK : %s of %s, %s, %s %s \r\n", $i, $total_droppoint, $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 
							}else{
							//insert
								$this->dbreport->insert($dbtable_xml,$data_xml);
								printf("==INSERT - OK : %s of %s, %s, %s %s \r\n", $i, $total_droppoint, $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 	
							}
							
							
							
				
				}
				
				
		}	
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "BALRICH - OTA REPORT XML";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$ReportDate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$ReportDate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Droppoint : ".$total_droppoint."
End Droppoint   : "."( ".$i." / ".$total_droppoint." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$ReportDate." \n".
					"Total Droppoint: ".$total_droppoint." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		printf("==FINISH : %s \r\n", date("d-m-Y H:i:s")); 
		
		
	}
	
	function create_xml_others($date="",$month="",$year="")
	{
		$start_time = date("d-m-Y H:i:s");
		printf("==START : %s \r\n", $start_time); 
		$newdate = date("Y-m-d", strtotime("yesterday"));
		
		if((isset($distrep)) && ($distrep == "")){
			$distrep = "";
		}
		if((isset($parent))  && ($parent == "")){
			$parent = "";
		}
		if($month == ""){
			$m1 = date("F", strtotime($newdate));
			$month = date("m", strtotime($newdate));
		}
		if($year == ""){
			$year = date("Y", strtotime($newdate));
		}
		if($date == ""){
			$date = date("d", strtotime($newdate));
		}
		$newdate = date("Y-m-d", strtotime($year."-".$month."-".$date));
		
		if($month != ""){
			$m1 = date("F", strtotime($newdate));
		}
		if($year != ""){
			$year = date("Y", strtotime($newdate));
		}
		
		$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
		$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
		//print_r($newdatetime." ".$newdatetime2." ".$m1);exit();
		
		$this->dbtransporter = $this->load->database("transporter_balrich",true);
		$this->dbreport = $this->load->database("balrich_report",true);
		$userid = 1032;
	
		$sdate_only = date("d", strtotime($newdate));
		$sdate_zone = date("Y-m-d", strtotime($newdate));
		
		// get data monthly report
		$report = "inout_geofence_";
		$report_xml = "xml_";
		
		$data = $this->get_monthly_report($sdate_zone,$sdate_zone);
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_xml = $report_xml."januari_".$year;
			$month_name = "Januari";
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_xml = $report_xml."februari_".$year;
			$month_name = "Februari";
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_xml = $report_xml."maret_".$year;
			$month_name = "Maret";
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_xml = $report_xml."april_".$year;
			$month_name = "April";
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_xml = $report_xml."mei_".$year;
			$month_name = "Mei";
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_xml = $report_xml."juni_".$year;
			$month_name = "Juni";
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_xml = $report_xml."juli_".$year;
			$month_name = "Juli";
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_xml = $report_xml."agustus_".$year;
			$month_name = "Agustus";
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_xml = $report_xml."september_".$year;
			$month_name = "September";
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_xml = $report_xml."oktober_".$year;
			$month_name = "Oktober";
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_xml = $report_xml."november_".$year;
			$month_name = "November";
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_xml = $report_xml."desember_".$year;
			$month_name = "Desember";
			break;
		}
		
		//select table ota report
		$this->dbtransporter->select("droppoint_id,droppoint_name,droppoint_code_real,droppoint_koord,droppoint_km,droppoint_creator,
									  distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,distrep_time,
									  distrep_sat_status,distrep_sat_distrep_code,distrep_sat_distrep_name,distrep_sat_outlet_code,distrep_sat_outlet_name,
									  plant_code_real,plant_code
									");
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("droppoint_creator <> ",$userid);
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("distrep_flag",0);
		$this->dbtransporter->where("droppoint_distrep <>",0);
		//$this->dbtransporter->where("plant_code_real",$plant);
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
		$this->dbtransporter->join("droppoint_plant", "distrep_plant = plant_id", "left");
		//$this->dbtransporter->limit(100);
		$q_droppoint = $this->dbtransporter->get("droppoint");
		$droppoint = $q_droppoint->result();
		
		//total data ota
		if($q_droppoint->num_rows == 0)
		{
			print_r("No Data Droppoint MASTER");
			exit;
		}
		$total_droppoint = count($droppoint);
		printf("==TOTAL DROPPOINT : %s \r\n", count($droppoint));
		
		//select ota berdasarkan master droppoint
		for($i=0; $i < count($droppoint); $i++){ 
			
			$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
			$q_report = $this->dbreport->get($dbtable);
			$row_report = $q_report->row();
			
				unset($data_xml);
				$data_xml["DroppointID"] = $droppoint[$i]->droppoint_id;
				$data_xml["Creator"] = $droppoint[$i]->droppoint_creator;
				$data_xml["PlantCode"] = $droppoint[$i]->plant_code_real;
				$data_xml["PlantName"] = $droppoint[$i]->plant_code;
				
				//jika toko sat
				if(isset($droppoint) && ($droppoint[$i]->distrep_sat_status == 1)){
					$data_xml["OutletCode"] = $droppoint[$i]->distrep_sat_outlet_code;
					$data_xml["DistrepCode"] = $droppoint[$i]->distrep_sat_distrep_code;
					$data_xml["DistrepName"] = $droppoint[$i]->distrep_sat_distrep_name;
					$data_xml["OutletName"] = $droppoint[$i]->distrep_sat_outlet_name;
					$data_xml["SubOutletName"] = $droppoint[$i]->droppoint_name;
					$data_xml["SubOutletCode"] = $droppoint[$i]->droppoint_code_real;
				}
				else
				{
					$data_xml["OutletCode"] = $droppoint[$i]->droppoint_code_real;
					$data_xml["DistrepCode"] = $droppoint[$i]->distrep_code;
					$data_xml["DistrepName"] = $droppoint[$i]->distrep_name;
					$data_xml["OutletName"] = $droppoint[$i]->droppoint_name;
					$data_xml["SubOutletName"] = "-";
					$data_xml["SubOutletCode"] = "-";
				}
				
				$data_xml["Month"] = strtoupper($month_name);
				$data_xml["Year"] = $year;
				if($droppoint[$i]->droppoint_creator == "3495"){
					$data_xml["Transporter"] = "KJL";
				}else if($droppoint[$i]->droppoint_creator == "3499"){
					$data_xml["Transporter"] = "RAI";
				}else if($droppoint[$i]->droppoint_creator == "3712"){
					$data_xml["Transporter"] = "LFK";
				}else if($droppoint[$i]->droppoint_creator == "1032"){
					$data_xml["Transporter"] = "BALRICH";
				}else if($droppoint[$i]->droppoint_creator == "3999"){
					$data_xml["Transporter"] = "LSA";
				}else{
					$data_xml["Transporter"] = "NO DATA USER";
				}
				
				$data_xml["Coordinat"] = $droppoint[$i]->droppoint_koord;
				if($droppoint[$i]->droppoint_km != ""){
					$droppoint_km = round(($droppoint[$i]->droppoint_km/1000), 2, PHP_ROUND_HALF_UP);
				}else{
					$droppoint_km = 0;
				}
				$data_xml["KM"] = $droppoint_km;
				
				$TargetOta = "-";
				$this->dbtransporter->limit(1);
				$this->dbtransporter->order_by("target_startdate", "asc");							
				$this->dbtransporter->select("target_startdate,target_enddate,target_time");
				$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
				$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
				$this->dbtransporter->where("target_startdate >=",$newdate);
				$this->dbtransporter->where("target_creator",$droppoint[$i]->droppoint_creator);
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
				
				$data_xml["TargetOta"] = $TargetOta;
				
				//daily ota
				for($j=0; $j < count($data); $j++){
							$georeport_time_alert = "";
							$georeport_time_alert_print = "-";
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
							
							$distrep_time = $droppoint[$i]->distrep_time;
							
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
							//$reportdate = $sdate_day."-".date("d-m-Y", strtotime($sdate_zone));
							$reportdate = date("d-m-Y", strtotime($sdate_zone));
							
						
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
							
							$sdate_type_selasa_jumat_minggu = $data[$j]->monthly_type_257; //khusus RO cirebon (selasa jumat sabtu) - kode 20
							//new from jwk medan
							$sdate_type_mdn = $data[$j]->monthly_type_mdn; //khusus Medan
							$sdate_type_mdn2 = $data[$j]->monthly_type_mdn2; //khusus Medan
							$sdate_type_3d = $data[$j]->monthly_type_3d; //khusus Medan (Jwk 3 hari sekali)
							
							$sdate_type_67 = $data[$j]->monthly_type_67; //khusus sabtu minggu - kode 22
						
						
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
												$georeport_time_alert_print = "-";
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
										
										//edit config time zone WITA / WIT
											if($distrep_time > 0){
												$time_zone = new DateTime($georeport_time_alert);
												$time_zone->add(new DateInterval('PT'.$distrep_time.'H'));
												$time_zone_ota = $time_zone->format('H:i:s');
												
												$georeport_time_alert = date("H:i:s", strtotime($time_zone_ota));
												$georeport_time_alert_print = date("H:i", strtotime($time_zone_ota));
												
											}
											
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
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "13"){ //3D - A
										$sdate_type = $sdate_type_3d;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "14"){ //3D - B
										$sdate_type = $sdate_type_3d;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "15"){ //3D - C
										$sdate_type = $sdate_type_3d;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "20"){ //khusus RO Cirebon NEW
										$sdate_type = $sdate_type_selasa_jumat_minggu;
									}
									
									// custom view report
									if($droppoint[$i]->distrep_report_status == "22"){ //khusus 67
										$sdate_type = $sdate_type_67;
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
														$georeport_time_alert_print = "-";
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
												
												//edit config time zone WITA / WIT
												if($distrep_time > 0){
													$time_zone = new DateTime($georeport_time_alert);
													$time_zone->add(new DateInterval('PT'.$distrep_time.'H'));
													$time_zone_ota = $time_zone->format('H:i:s');
															
													$georeport_time_alert = date("H:i:s", strtotime($time_zone_ota));
													$georeport_time_alert_print = date("H:i", strtotime($time_zone_ota));
												}
									
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
							
							$ReportDate = date("Y-m-d", strtotime($reportdate));
							$TimeOTA = $georeport_time_alert_print;
							
							$data_xml["ReportDate"] = $ReportDate;
							$data_xml["TimeOTA"] = $TimeOTA;
							$data_xml["Modified"] = date("Y-m-d H:i:s");
							
							//cek data OTA XML
							$this->dbreport->limit(1);
							$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
							$this->dbreport->where("ReportDate",$ReportDate);
							$this->dbreport->where("Creator",$droppoint[$i]->droppoint_creator);
							$q_reportxml = $this->dbreport->get($dbtable_xml);
							$row_reportxml = $q_reportxml->row();
							$total_row_reportxml = count($row_reportxml);
							
							//update
							if($total_row_reportxml > 0){
								$this->dbreport->limit(1);
								$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
								$this->dbreport->where("ReportDate",$ReportDate);
								$this->dbreport->where("Creator",$droppoint[$i]->droppoint_creator);
								$this->dbreport->update($dbtable_xml,$data_xml);
								printf("==UPDATE - OK : %s of %s, %s, %s %s \r\n", $i, $total_droppoint, $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 
							}else{
							//insert
								$this->dbreport->insert($dbtable_xml,$data_xml);
								printf("==INSERT - OK : %s of %s, %s, %s %s \r\n", $i, $total_droppoint, $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 	
							}
							
							
							
				
				}
				
				
		}	
		$finish_time = date("d-m-Y H:i:s");
		
		//Send Email
		$cron_name = "LACAKTRANSPRO - OTA REPORT XML OTHERS";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$ReportDate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$ReportDate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Droppoint : ".$total_droppoint."
End Droppoint   : "."( ".$i." / ".$total_droppoint." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		//get telegram group by company
		$company_username = $this->config->item('COMPANY_OTA_TELEGRAM_ALERT');
		$this->db = $this->load->database("webtracking_ultron",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$ReportDate." \n".
					"Total Droppoint: ".$total_droppoint." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		printf("==FINISH : %s \r\n", date("d-m-Y H:i:s")); 
		
		
	}
	
	function sync_xml()
	{
		$start_time = date("d-m-Y H:i:s");
		printf("==START : %s \r\n", $start_time); 
		
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbreport = $this->load->database("balrich_report",true);
		$userid = 1032;
		// get data monthly report
		$report = "inout_geofence_";
		$report_xml = "xml_";
		
		$this->dbtransporter->where("ota_check",0);
		$this->dbtransporter->where("ota_flag",0);
		$this->dbtransporter->where("ota_date !=","1970-01-01");
		$q_config = $this->dbtransporter->get("droppoint_ota_manual");
		$r_config = $q_config->result();
		$total_config = count($r_config);
		
		
		if($total_config > 0){
			
			for($z=0; $z < count($r_config); $z++){ 
			
				$newdate = $r_config[$z]->ota_date;
				$droppointid = $r_config[$z]->ota_droppoint_id;
				$droppointname = $r_config[$z]->ota_droppoint_name;
				
				$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
				$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
				
				$sdate_only = date("d", strtotime($newdate));
				$sdate_zone = date("Y-m-d", strtotime($newdate));
				$date = date("d", strtotime($newdate));
				$month = date("m", strtotime($newdate));
				$m1 = date("F", strtotime($newdate));
				$year = date("Y", strtotime($newdate));
				$data = $this->get_monthly_report($sdate_zone,$sdate_zone);
				
				printf("==PROCESS %s : %s of %s \r\n", $droppointname, $z+1, $total_config); 
				
				switch ($m1)
				{
					case "January":
					$dbtable = $report."januari_".$year;
					$dbtable_xml = $report_xml."januari_".$year;
					$month_name = "Januari";
					break;
					case "February":
					$dbtable = $report."februari_".$year;
					$dbtable_xml = $report_xml."februari_".$year;
					$month_name = "Februari";
					break;
					case "March":
					$dbtable = $report."maret_".$year;
					$dbtable_xml = $report_xml."maret_".$year;
					$month_name = "Maret";
					break;
					case "April":
					$dbtable = $report."april_".$year;
					$dbtable_xml = $report_xml."april_".$year;
					$month_name = "April";
					break;
					case "May":
					$dbtable = $report."mei_".$year;
					$dbtable_xml = $report_xml."mei_".$year;
					$month_name = "Mei";
					break;
					case "June":
					$dbtable = $report."juni_".$year;
					$dbtable_xml = $report_xml."juni_".$year;
					$month_name = "Juni";
					break;
					case "July":
					$dbtable = $report."juli_".$year;
					$dbtable_xml = $report_xml."juli_".$year;
					$month_name = "Juli";
					break;
					case "August":
					$dbtable = $report."agustus_".$year;
					$dbtable_xml = $report_xml."agustus_".$year;
					$month_name = "Agustus";
					break;
					case "September":
					$dbtable = $report."september_".$year;
					$dbtable_xml = $report_xml."september_".$year;
					$month_name = "September";
					break;
					case "October":
					$dbtable = $report."oktober_".$year;
					$dbtable_xml = $report_xml."oktober_".$year;
					$month_name = "Oktober";
					break;
					case "November":
					$dbtable = $report."november_".$year;
					$dbtable_xml = $report_xml."november_".$year;
					$month_name = "November";
					break;
					case "December":
					$dbtable = $report."desember_".$year;
					$dbtable_xml = $report_xml."desember_".$year;
					$month_name = "Desember";
					break;
				}
				
				//select table ota report
				$this->dbtransporter->select("droppoint_id,droppoint_name,droppoint_code_real,droppoint_koord,droppoint_km,
											  distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,
											  distrep_sat_status,distrep_sat_distrep_code,distrep_sat_distrep_name,distrep_sat_outlet_code,distrep_sat_outlet_name,
											  plant_code_real,plant_code
											");
				$this->dbtransporter->order_by("distrep_name","asc");
				$this->dbtransporter->where("droppoint_id",$droppointid);
				$this->dbtransporter->where("droppoint_flag",0);
				$this->dbtransporter->where("distrep_flag",0);
				$this->dbtransporter->where("droppoint_distrep <>",0);
				$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
				$this->dbtransporter->join("droppoint_plant", "distrep_plant = plant_id", "left");
				$q_droppoint = $this->dbtransporter->get("droppoint");
				$droppoint = $q_droppoint->result();
				
				//total data ota
				if($q_droppoint->num_rows == 0)
				{
					//update config
					unset($data_config);
					$data_config["ota_check"] = 1;
									
					$this->dbtransporter->where("ota_droppoint_id",$droppointid);
					$this->dbtransporter->where("ota_date",$newdate);
					$this->dbtransporter->update("droppoint_ota_manual",$data_config);
					print_r("No Data Droppoint OTA --update \r\n");
					
				}
				$total_droppoint = count($droppoint);
				printf("==TOTAL DROPPOINT : %s \r\n", count($droppoint));
				
				//select ota berdasarkan master droppoint
				for($i=0; $i < count($droppoint); $i++){ 
					
					$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
					$q_report = $this->dbreport->get($dbtable);
					$row_report = $q_report->row();
					
						unset($data_xml);
						$data_xml["DroppointID"] = $droppoint[$i]->droppoint_id;
						$data_xml["Creator"] = $userid;
						$data_xml["PlantCode"] = $droppoint[$i]->plant_code_real;
						$data_xml["PlantName"] = $droppoint[$i]->plant_code;
						
						//jika toko sat
						if(isset($droppoint) && ($droppoint[$i]->distrep_sat_status == 1)){
							$data_xml["OutletCode"] = $droppoint[$i]->distrep_sat_outlet_code;
							$data_xml["DistrepCode"] = $droppoint[$i]->distrep_sat_distrep_code;
							$data_xml["DistrepName"] = $droppoint[$i]->distrep_sat_distrep_name;
							$data_xml["OutletName"] = $droppoint[$i]->distrep_sat_outlet_name;
							$data_xml["SubOutletName"] = $droppoint[$i]->droppoint_name;
							$data_xml["SubOutletCode"] = $droppoint[$i]->droppoint_code_real;
						}
						else
						{
							$data_xml["OutletCode"] = $droppoint[$i]->droppoint_code_real;
							$data_xml["DistrepCode"] = $droppoint[$i]->distrep_code;
							$data_xml["DistrepName"] = $droppoint[$i]->distrep_name;
							$data_xml["OutletName"] = $droppoint[$i]->droppoint_name;
							$data_xml["SubOutletName"] = "-";
							$data_xml["SubOutletCode"] = "-";
						}
						
						$data_xml["Month"] = strtoupper($month_name);
						$data_xml["Year"] = $year;
						$data_xml["Transporter"] = "BALRICH";
						$data_xml["Coordinat"] = $droppoint[$i]->droppoint_koord;
						if($droppoint[$i]->droppoint_km != ""){
							$droppoint_km = round(($droppoint[$i]->droppoint_km/1000), 2, PHP_ROUND_HALF_UP);
						}else{
							$droppoint_km = 0;
						}
						$data_xml["KM"] = $droppoint_km;
						
						$TargetOta = "-";
						$this->dbtransporter->limit(1);
						$this->dbtransporter->order_by("target_startdate", "asc");							
						$this->dbtransporter->select("target_startdate,target_enddate,target_time");
						$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
						$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
						$this->dbtransporter->where("target_startdate >=",$newdate);
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
						
						$data_xml["TargetOta"] = $TargetOta;
						
						//daily ota
						for($j=0; $j < count($data); $j++){
									$georeport_time_alert = "";
									$georeport_time_alert_print = "-";
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
									
									$reportdate = date("d-m-Y", strtotime($sdate_zone));
									
								
									$sdate_type = $data[$j]->monthly_type; //ganjil //genap //ods
									
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
														$georeport_time_alert_print = "-";
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
												
													
												$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
												$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
												$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
												$georeport_status = $row_report->$field_status_new;
												$georeport_comment = $row_report->$field_comment_new;
												$georeport_km = $row_report->$field_km_new;
												
												
												
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
																$georeport_time_alert_print = "-";
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
														
														$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
														$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
														$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
														$georeport_status = $row_report->$field_status_new;
														$georeport_comment = $row_report->$field_comment_new;
														$georeport_km = $row_report->$field_km_new;
												
													
														
													}
													
													
												}
											}
										
									}
									
									$ReportDate = date("Y-m-d", strtotime($reportdate));
									$TimeOTA = $georeport_time_alert_print;
									
									$data_xml["ReportDate"] = $ReportDate;
									$data_xml["TimeOTA"] = $TimeOTA;
									$data_xml["Modified"] = date("Y-m-d H:i:s");
									
									//cek data OTA XML
									$this->dbreport->limit(1);
									$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
									$this->dbreport->where("ReportDate",$ReportDate);
									$this->dbreport->where("Creator",$userid);
									$q_reportxml = $this->dbreport->get($dbtable_xml);
									$row_reportxml = $q_reportxml->row();
									$total_row_reportxml = count($row_reportxml);
									
									//update
									if($total_row_reportxml > 0){
										$this->dbreport->limit(1);
										$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
										$this->dbreport->where("ReportDate",$ReportDate);
										$this->dbreport->where("Creator",$userid);
										$this->dbreport->update($dbtable_xml,$data_xml);
										printf("==UPDATE - OK : %s, %s %s \r\n", $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 
									}else{
									//insert
										$this->dbreport->insert($dbtable_xml,$data_xml);
										printf("==INSERT - OK : %s, %s %s \r\n", $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 	
									}
									
									//update config
									unset($data_config);
									$data_config["ota_check"] = 1;
									
									$this->dbtransporter->where("ota_droppoint_id",$droppoint[$i]->droppoint_id);
									$this->dbtransporter->where("ota_date",$ReportDate);
									$this->dbtransporter->update("droppoint_ota_manual",$data_config);
								
						}
						
						
				}	
				
			}
			
		}
		
		
		$finish_time = date("d-m-Y H:i:s");
		printf("==FINISH : %s \r\n", date("d-m-Y H:i:s")); 
		
	}
	
	function sync_xml_all()
	{
		$start_time = date("d-m-Y H:i:s");
		printf("==START : %s \r\n", $start_time); 
		
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbreport = $this->load->database("balrich_report",true);
		$userid = 1032;
		// get data monthly report
		$report = "inout_geofence_";
		$report_xml = "xml_";
		
		$this->dbtransporter->where("ota_check",0);
		$this->dbtransporter->where("ota_flag",0);
		$this->dbtransporter->where("ota_date !=","1970-01-01");
		$q_config = $this->dbtransporter->get("droppoint_ota_manual");
		$r_config = $q_config->result();
		$total_config = count($r_config);
		
		
		if($total_config > 0){
			
			for($z=0; $z < count($r_config); $z++){ 
			
				$newdate = $r_config[$z]->ota_date;
				$droppointid = $r_config[$z]->ota_droppoint_id;
				$droppointname = $r_config[$z]->ota_droppoint_name;
				
				$newdatetime = date("Y-m-d H:i:s", strtotime($newdate."00:00:00"));
				$newdatetime2 = date("Y-m-d H:i:s", strtotime($newdate."23:59:59"));
				
				$sdate_only = date("d", strtotime($newdate));
				$sdate_zone = date("Y-m-d", strtotime($newdate));
				$date = date("d", strtotime($newdate));
				$month = date("m", strtotime($newdate));
				$m1 = date("F", strtotime($newdate));
				$year = date("Y", strtotime($newdate));
				$data = $this->get_monthly_report($sdate_zone,$sdate_zone);
				
				printf("==PROCESS %s : %s of %s \r\n", $droppointname, $z+1, $total_config); 
				
				switch ($m1)
				{
					case "January":
					$dbtable = $report."januari_".$year;
					$dbtable_xml = $report_xml."januari_".$year;
					$month_name = "Januari";
					break;
					case "February":
					$dbtable = $report."februari_".$year;
					$dbtable_xml = $report_xml."februari_".$year;
					$month_name = "Februari";
					break;
					case "March":
					$dbtable = $report."maret_".$year;
					$dbtable_xml = $report_xml."maret_".$year;
					$month_name = "Maret";
					break;
					case "April":
					$dbtable = $report."april_".$year;
					$dbtable_xml = $report_xml."april_".$year;
					$month_name = "April";
					break;
					case "May":
					$dbtable = $report."mei_".$year;
					$dbtable_xml = $report_xml."mei_".$year;
					$month_name = "Mei";
					break;
					case "June":
					$dbtable = $report."juni_".$year;
					$dbtable_xml = $report_xml."juni_".$year;
					$month_name = "Juni";
					break;
					case "July":
					$dbtable = $report."juli_".$year;
					$dbtable_xml = $report_xml."juli_".$year;
					$month_name = "Juli";
					break;
					case "August":
					$dbtable = $report."agustus_".$year;
					$dbtable_xml = $report_xml."agustus_".$year;
					$month_name = "Agustus";
					break;
					case "September":
					$dbtable = $report."september_".$year;
					$dbtable_xml = $report_xml."september_".$year;
					$month_name = "September";
					break;
					case "October":
					$dbtable = $report."oktober_".$year;
					$dbtable_xml = $report_xml."oktober_".$year;
					$month_name = "Oktober";
					break;
					case "November":
					$dbtable = $report."november_".$year;
					$dbtable_xml = $report_xml."november_".$year;
					$month_name = "November";
					break;
					case "December":
					$dbtable = $report."desember_".$year;
					$dbtable_xml = $report_xml."desember_".$year;
					$month_name = "Desember";
					break;
				}
				
				//select table ota report
				$this->dbtransporter->select("droppoint_id,droppoint_name,droppoint_code_real,droppoint_koord,droppoint_km,droppoint_creator,
											  distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,
											  distrep_sat_status,distrep_sat_distrep_code,distrep_sat_distrep_name,distrep_sat_outlet_code,distrep_sat_outlet_name,
											  distrep_time,plant_code_real,plant_code
											");
				$this->dbtransporter->order_by("distrep_name","asc");
				$this->dbtransporter->where("droppoint_id",$droppointid);
				$this->dbtransporter->where("droppoint_flag",0);
				$this->dbtransporter->where("distrep_flag",0);
				$this->dbtransporter->where("droppoint_distrep <>",0);
				$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
				$this->dbtransporter->join("droppoint_plant", "distrep_plant = plant_id", "left");
				$q_droppoint = $this->dbtransporter->get("droppoint");
				$droppoint = $q_droppoint->result();
				
				//total data ota
				if($q_droppoint->num_rows == 0)
				{
					//update config
					unset($data_config);
					$data_config["ota_check"] = 1;
									
					$this->dbtransporter->where("ota_droppoint_id",$droppointid);
					$this->dbtransporter->where("ota_date",$newdate);
					$this->dbtransporter->update("droppoint_ota_manual",$data_config);
					print_r("No Data Droppoint OTA --update \r\n");
					
				}
				$total_droppoint = count($droppoint);
				printf("==TOTAL DROPPOINT : %s \r\n", count($droppoint));
				
				//select ota berdasarkan master droppoint
				for($i=0; $i < count($droppoint); $i++){ 
					
					$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
					$q_report = $this->dbreport->get($dbtable);
					$row_report = $q_report->row();
					
						unset($data_xml);
						$data_xml["DroppointID"] = $droppoint[$i]->droppoint_id;
						$data_xml["Creator"] = $droppoint[$i]->droppoint_creator;
						$data_xml["PlantCode"] = $droppoint[$i]->plant_code_real;
						$data_xml["PlantName"] = $droppoint[$i]->plant_code;
						
						//jika toko sat
						if(isset($droppoint) && ($droppoint[$i]->distrep_sat_status == 1)){
							$data_xml["OutletCode"] = $droppoint[$i]->distrep_sat_outlet_code;
							$data_xml["DistrepCode"] = $droppoint[$i]->distrep_sat_distrep_code;
							$data_xml["DistrepName"] = $droppoint[$i]->distrep_sat_distrep_name;
							$data_xml["OutletName"] = $droppoint[$i]->distrep_sat_outlet_name;
							$data_xml["SubOutletName"] = $droppoint[$i]->droppoint_name;
							$data_xml["SubOutletCode"] = $droppoint[$i]->droppoint_code_real;
						}
						else
						{
							$data_xml["OutletCode"] = $droppoint[$i]->droppoint_code_real;
							$data_xml["DistrepCode"] = $droppoint[$i]->distrep_code;
							$data_xml["DistrepName"] = $droppoint[$i]->distrep_name;
							$data_xml["OutletName"] = $droppoint[$i]->droppoint_name;
							$data_xml["SubOutletName"] = "-";
							$data_xml["SubOutletCode"] = "-";
						}
						
						$data_xml["Month"] = strtoupper($month_name);
						$data_xml["Year"] = $year;
						if($droppoint[$i]->droppoint_creator == "3495"){
							$data_xml["Transporter"] = "KJL";
						}else if($droppoint[$i]->droppoint_creator == "3499"){
							$data_xml["Transporter"] = "RAI";
						}else if($droppoint[$i]->droppoint_creator == "3712"){
							$data_xml["Transporter"] = "LFK";
						}else if($droppoint[$i]->droppoint_creator == "1032"){
							$data_xml["Transporter"] = "BALRICH";
						}else if($droppoint[$i]->droppoint_creator == "3999"){
							$data_xml["Transporter"] = "LSA";
						}else{
							$data_xml["Transporter"] = "NO DATA USER";
						}
						$data_xml["Coordinat"] = $droppoint[$i]->droppoint_koord;
						if($droppoint[$i]->droppoint_km != ""){
							$droppoint_km = round(($droppoint[$i]->droppoint_km/1000), 2, PHP_ROUND_HALF_UP);
						}else{
							$droppoint_km = 0;
						}
						$data_xml["KM"] = $droppoint_km;
						
						$TargetOta = "-";
						$this->dbtransporter->limit(1);
						$this->dbtransporter->order_by("target_startdate", "asc");							
						$this->dbtransporter->select("target_startdate,target_enddate,target_time");
						$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
						$this->dbtransporter->where("target_type",$droppoint[$i]->distrep_type);
						$this->dbtransporter->where("target_startdate >=",$newdate);
						$this->dbtransporter->where("target_creator",$droppoint[$i]->droppoint_creator);
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
						
						$data_xml["TargetOta"] = $TargetOta;
						
						//daily ota
						for($j=0; $j < count($data); $j++){
									$georeport_time_alert = "";
									$georeport_time_alert_print = "-";
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
									
									$distrep_time = $droppoint[$i]->distrep_time;
									
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
									
									$reportdate = date("d-m-Y", strtotime($sdate_zone));
									
									$sdate_type = $data[$j]->monthly_type; //ganjil //genap //ods
									
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
									
									$sdate_type_selasa_jumat_minggu = $data[$j]->monthly_type_257; //khusus RO cirebon (selasa jumat sabtu) - kode 20
									//new from jwk medan
									$sdate_type_mdn = $data[$j]->monthly_type_mdn; //khusus Medan
									$sdate_type_mdn2 = $data[$j]->monthly_type_mdn2; //khusus Medan
									$sdate_type_3d = $data[$j]->monthly_type_3d; //khusus Medan (Jwk 3 hari sekali)
									$sdate_type_67 = $data[$j]->monthly_type_67; //khusus sabtu minggu - kode 22
									
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
														$georeport_time_alert_print = "-";
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
												
													
												$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
												$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
												
												//edit config time zone WITA / WIT
												if($distrep_time > 0){
													$time_zone = new DateTime($georeport_time_alert);
													$time_zone->add(new DateInterval('PT'.$distrep_time.'H'));
													$time_zone_ota = $time_zone->format('H:i:s');
															
													$georeport_time_alert = date("H:i:s", strtotime($time_zone_ota));
													$georeport_time_alert_print = date("H:i", strtotime($time_zone_ota));
												}
												
												$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
												$georeport_status = $row_report->$field_status_new;
												$georeport_comment = $row_report->$field_comment_new;
												$georeport_km = $row_report->$field_km_new;
												
												
												
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
											
											// custom view report
											if($droppoint[$i]->distrep_report_status == "13"){ //3D - A
												$sdate_type = $sdate_type_3d;
											}
											
											// custom view report
											if($droppoint[$i]->distrep_report_status == "14"){ //3D - B
												$sdate_type = $sdate_type_3d;
											}
											
											// custom view report
											if($droppoint[$i]->distrep_report_status == "15"){ //3D - C
												$sdate_type = $sdate_type_3d;
											}
											
											// custom view report
											if($droppoint[$i]->distrep_report_status == "20"){ //khusus RO Cirebon NEW
												$sdate_type = $sdate_type_selasa_jumat_minggu;
											}
											
											// custom view report
											if($droppoint[$i]->distrep_report_status == "22"){ //khusus 67
												$sdate_type = $sdate_type_67;
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
																$georeport_time_alert_print = "-";
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
														
														$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time_new));
														$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time_new));
														
														//edit config time zone WITA / WIT
														if($distrep_time > 0){
															$time_zone = new DateTime($georeport_time_alert);
															$time_zone->add(new DateInterval('PT'.$distrep_time.'H'));
															$time_zone_ota = $time_zone->format('H:i:s');
															
															$georeport_time_alert = date("H:i:s", strtotime($time_zone_ota));
															$georeport_time_alert_print = date("H:i", strtotime($time_zone_ota));
															
														}
														
														$georeport_time_alert_vehicle = $row_report->$field_vehicle_new;
														$georeport_status = $row_report->$field_status_new;
														$georeport_comment = $row_report->$field_comment_new;
														$georeport_km = $row_report->$field_km_new;
												
													
														
													}
													
													
												}
											}
										
									}
									
									$ReportDate = date("Y-m-d", strtotime($reportdate));
									$TimeOTA = $georeport_time_alert_print;
									
									$data_xml["ReportDate"] = $ReportDate;
									$data_xml["TimeOTA"] = $TimeOTA;
									$data_xml["Modified"] = date("Y-m-d H:i:s");
									
									//cek data OTA XML
									$this->dbreport->limit(1);
									$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
									$this->dbreport->where("ReportDate",$ReportDate);
									$this->dbreport->where("Creator",$droppoint[$i]->droppoint_creator);
									$q_reportxml = $this->dbreport->get($dbtable_xml);
									$row_reportxml = $q_reportxml->row();
									$total_row_reportxml = count($row_reportxml);
									
									//update
									if($total_row_reportxml > 0){
										$this->dbreport->limit(1);
										$this->dbreport->where("DroppointID",$droppoint[$i]->droppoint_id);
										$this->dbreport->where("ReportDate",$ReportDate);
										$this->dbreport->where("Creator",$droppoint[$i]->droppoint_creator);
										$this->dbreport->update($dbtable_xml,$data_xml);
										printf("==UPDATE - OK : %s, %s %s \r\n", $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 
									}else{
									//insert
										$this->dbreport->insert($dbtable_xml,$data_xml);
										printf("==INSERT - OK : %s, %s %s \r\n", $ReportDate, $droppoint[$i]->droppoint_id, $droppoint[$i]->droppoint_name); 	
									}
									
									//update config
									unset($data_config);
									$data_config["ota_check"] = 1;
									
									$this->dbtransporter->where("ota_droppoint_id",$droppoint[$i]->droppoint_id);
									$this->dbtransporter->where("ota_date",$ReportDate);
									$this->dbtransporter->update("droppoint_ota_manual",$data_config);
								
						}
						
						
				}	
				
			}
			
		}
		
		
		$finish_time = date("d-m-Y H:i:s");
		printf("==FINISH : %s \r\n", date("d-m-Y H:i:s")); 
		
	}
	
	function create_monthly_report($newdate="")
	{
		$start_time = date("d-m-Y H:i:s");
		printf("==START : %s \r\n", $start_time); 
		
		if($newdate == ""){
			$newdate = date("Y-m-d", strtotime("tomorrow"));
		}
		
		//print_r($newdate);exit();
		$this->dbtransporter = $this->load->database("transporter",true);
		
		$m1 = date("F", strtotime($newdate));
		$month = date("m", strtotime($newdate));
		$d1 = date("D", strtotime($newdate));
		$monthly_year = date("Y", strtotime($newdate));
		
		switch ($m1)
		{
			case "January":
			$month_name = "Januari";
			break;
			case "February":
			$month_name = "Februari";
			break;
			case "March":
            $month_name = "Maret";
			break;
			case "April":
            $month_name = "April";
			break;
			case "May":
			$month_name = "Mei";
			break;
			case "June":
			$month_name = "Juni";
			break;
			case "July":
            $month_name = "Juli";
			break;
			case "August":
            $month_name = "Agustus";
			break;
			case "September":
			$month_name = "September";
			break;
			case "October":
            $month_name = "Oktober";
			break;
			case "November":
			$month_name = "November";
			break;
			case "December":
			$month_name = "Desember";
			break;
		}
		
		switch ($d1)
		{
			case "Mon":
			$day = "Senin";
			break;
			case "Tue":
			$day = "Selasa";
			break;
			case "Wed":
            $day = "Rabu";
			break;
			case "Thu":
            $day = "Kamis";
			break;
			case "Fri":
			$day = "Jumat";
			break;
			case "Sat":
			$day = "Sabtu";
			break;
			case "Sun":
            $day = "Minggu";
			break;
		}
		
		//search day - 1
		//1 hari sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1D');
		$date->sub($interval);
		$olddate = $date->format('Y-m-d');
		
		//print_r($newdate." | ".$olddate);exit();
		
		//cek master config
		$monthly_type_crb = 0;
		$monthly_type_ckd = 0;
		$monthly_type_crb2 = 0;
		$monthly_type_sby = 0;
		$monthly_type_mdn = 0;
		$monthly_type_mdn2 = 0;
		$monthly_type_257 = 0;
		$monthly_type_123457 = 0;
		$monthly_type_67 = 0;
		
		$monthly_type_134 = 0;
		$monthly_type_457 = 0;
		$monthly_type_2457 = 0;
		$monthly_type_2356 = 0;
		$monthly_type_47 = 0;
		$monthly_type_247 = 0;
		
		$this->dbtransporter->where("master_status",1);
		$this->dbtransporter->where("master_day",$day);
		$q_master = $this->dbtransporter->get("config_monthly_master");
		$data_master = $q_master->row();
		
		if((isset($data_master)) && (count($data_master)>0)){
			$monthly_type_crb = $data_master->master_monthly_type_crb;
			$monthly_type_ckd = $data_master->master_monthly_type_ckd;
			$monthly_type_crb2 = $data_master->master_monthly_type_crb2;
			$monthly_type_sby = $data_master->master_monthly_type_sby;
			$monthly_type_mdn = $data_master->master_monthly_type_mdn;
			$monthly_type_mdn2 = $data_master->master_monthly_type_mdn2;
			$monthly_type_257 = $data_master->master_monthly_type_257;
			$monthly_type_123457 = $data_master->master_monthly_type_123457;
			$monthly_type_67 = $data_master->master_monthly_type_67;
			
			//new
			$monthly_type_134 = $data_master->master_monthly_type_134;
			$monthly_type_457 = $data_master->master_monthly_type_457;
			$monthly_type_2457 = $data_master->master_monthly_type_2457;
			$monthly_type_2356 = $data_master->master_monthly_type_2356;
			$monthly_type_47 = $data_master->master_monthly_type_47;
			$monthly_type_247 = $data_master->master_monthly_type_247;
			
		}
		
				//cek yesterday (for tds & 3ds)
				$this->dbtransporter->select("monthly_type,monthly_type_3d");
				$this->dbtransporter->where("monthly_date",$olddate);
				$q_configreport_old = $this->dbtransporter->get("config_monthly_report");
				$configreport_old = $q_configreport_old->row();
				
				if((isset($configreport_old) && (count($configreport_old)>0))){
					$monthly_type_yesterday = $configreport_old->monthly_type;
					$monthly_type_3d_yesterday = $configreport_old->monthly_type_3d;
					
					//jika tds
					if($monthly_type_yesterday == 1){
						$monthly_type = 2;
					}
					else if($monthly_type_yesterday == 2){
						$monthly_type = 1;
					}
					else{
						$monthly_type = 0;
					}
					
					//jika 3ds -> urutan 15, 13, 14
					if($monthly_type_3d_yesterday == 15){
						$monthly_type_3d = 13;
					}
					else if($monthly_type_3d_yesterday == 13){
						$monthly_type_3d = 14;
					}
					else if($monthly_type_3d_yesterday == 14){
						$monthly_type_3d = 15;
					}
					else{
						$monthly_type_3d = 0;
					}
				}
				
				
				unset($data);
				$data["monthly_date"] = $newdate;
				$data["monthly_day"] = $day;
				$data["monthly_name"] = $month_name;
				$data["monthly_year"] = $monthly_year;
				
				//tds
				$data["monthly_type"] = $monthly_type;
				//3ds
				$data["monthly_type_3d"] = $monthly_type_3d;
				
				$data["monthly_type_crb"] = $monthly_type_crb;
				$data["monthly_type_ckd"] = $monthly_type_ckd;
				$data["monthly_type_crb2"] = $monthly_type_crb2;
				$data["monthly_type_sby"] = $monthly_type_sby;
				$data["monthly_type_mdn"] = $monthly_type_mdn;
				$data["monthly_type_mdn2"] = $monthly_type_mdn2;
				$data["monthly_type_257"] = $monthly_type_257;
				$data["monthly_type_123457"] = $monthly_type_123457;
				$data["monthly_type_67"] = $monthly_type_67;
				
				//
				$data["monthly_type_134"] = $monthly_type_134;
				$data["monthly_type_457"] = $monthly_type_457;
				$data["monthly_type_2457"] = $monthly_type_2457;
				$data["monthly_type_2356"] = $monthly_type_2356;
				$data["monthly_type_47"] = $monthly_type_47;
				$data["monthly_type_247"] = $monthly_type_247;
				
				//cek data exists				
				$this->dbtransporter->limit(1);
				$this->dbtransporter->where("monthly_date",$newdate);
				$q_configreport_exists = $this->dbtransporter->get("config_monthly_report");
				$configreport_exists = $q_configreport_exists->row();
				$total_configreport_exists = count($configreport_exists);
				
				//update
				if($total_configreport_exists > 0){
					$this->dbtransporter->limit(1);
					$this->dbtransporter->where("monthly_date",$newdate);
					$this->dbtransporter->update("config_monthly_report",$data);
					printf("==UPDATE - OK : %s \r\n", $newdate); 
				}else{
					//insert
					$this->dbtransporter->insert("config_monthly_report",$data);
					printf("==INSERT - OK : %s \r\n", $newdate); 
				}
				
		$finish_time = date("d-m-Y H:i:s");
		printf("==FINISH : %s \r\n", date("d-m-Y H:i:s")); 
	}
	
	function get_monthly_report($sdate,$edate){
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("monthly_date","asc");
		$this->dbtransporter->where("monthly_date >=", $sdate);
		$this->dbtransporter->where("monthly_date <=", $edate);
		$qd = $this->dbtransporter->get("config_monthly_report");
		$rd = $qd->result();
		
		return $rd;
	}
	
	//get MASTER JARAK
	function master_km($distrep="")
	{
		printf("PROSES GET DROPPOINT \r\n");
		$now = date("Y-m-d");
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->order_by("droppoint_id","desc");
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("droppoint_distrep >",0);
		$this->dbtransporter->where("droppoint_koord <>","");
		$this->dbtransporter->where("droppoint_km_check",0); //belum dicek
		if($distrep != ""){
			$this->dbtransporter->where("droppoint_distrep",$distrep);
		}
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep = distrep_id", "left");
		$q = $this->dbtransporter->get("droppoint");
		$data = $q->result();
		$total_data = count($data);
		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				printf("CEK DROPPOINT : %s, %s of %s \r\n", $data[$i]->droppoint_name, $i+1, $total_data);
					$base_koord = "";
					$duration_text = "";
					$duration_value = 0;
					$distance_text = "";
					$distance_value = 0;
							
					//Cek Base
					$this->dbtransporter->where("base_geofence",$data[$i]->distrep_otl_base);
					$this->dbtransporter->where("base_flag",0);
					$qb = $this->dbtransporter->get("droppoint_base");
					$rbase = $qb->row();
					$total_base = count($rbase);
					if($total_base > 0){
						$base_koord = $rbase->base_coordinate;
					}
					
					if($base_koord != ""){
						//origin (base)
						$coordinate_origin = explode(" ", $base_koord);
						$latitude1 = trim($coordinate_origin[0]);
						$longitude1 = trim($coordinate_origin[1]);
						
						//destination
						$coordinate_dest = explode(" ", $data[$i]->droppoint_koord);
						$latitude2 = trim($coordinate_dest[0]);
						$longitude2 = trim($coordinate_dest[1]);
								
						//Apigoogle
						//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
						$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0"; //kim
						//$apikey = "AIzaSyAdxIZsEjDmrrkNEW9tujeaiYen5aGoUXE"; //balrich
						
						$eta_data = $this->distancematrix($latitude1, $longitude1, $latitude2, $longitude2, $apikey);
						printf("ETA MASTER KOORD: %s \r\n", $eta_data);
						
						if(isset($eta_data['rows'][0]['elements'][0]['distance']['value'])){
							$duration_text = $eta_data['rows'][0]['elements'][0]['duration']['text'];
							$duration_value = $eta_data['rows'][0]['elements'][0]['duration']['value'];
							$distance_text = $eta_data['rows'][0]['elements'][0]['distance']['text'];
							$distance_value = $eta_data['rows'][0]['elements'][0]['distance']['value'];
							
							unset($master_eta);
							$master_eta["droppoint_km"] = $distance_value;
							if(($distance_value > 0) && ($distance_value > 0)){
								$master_eta["droppoint_km_check"] = 1; //sudah di cek
							}
							
							$this->dbtransporter->limit(1);
							$this->dbtransporter->where("droppoint_id",$data[$i]->droppoint_id);
							$this->dbtransporter->update("droppoint",$master_eta);
							printf("UPDATE KM DROPPOINT MASTER %s , %s \r\n", $data[$i]->droppoint_id, $data[$i]->droppoint_name);
							
						}
						
					}
					else
					{
						printf("NO DATA BASE KOORD \r\n");
					}
					
			}
				
		}
			
		printf("PROSES ETA MASTER DROPPOINT FINISH \r\n");
	}
	
	function distancematrix($latitude1, $longitude1, $latitude2, $longitude2, $apikey){
        $dataJson = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$latitude1.",".$longitude1."&destinations=".$latitude2.",".$longitude2."&key=".$apikey."");
		printf("JSON API %s \r\n", $dataJson);
		$data = json_decode($dataJson,true);
		/*$api_status = $data['rows'][0]['elements'][0]['status']['value'];
		$eta = "";
		
		if($api_status == "O"){
			$duration_sec = $data['rows'][0]['elements'][0]['duration']['value'];
			$dateinterval = new DateTime($lastgpstime);
			$dateinterval->add(new DateInterval('PT'.$duration_sec.'S'));
			$eta = $dateinterval->format('Y-m-d H:i:s');
		}
		printf("ETA %s \r\n", $eta);*/
        //return $eta;
		return $data;
		
    }
	
	function get_location_name($dbname="", $selected="")
	{
		printf("PROSES GET KOORDINAT \r\n");
		$now = date("Y-m-d");
		$this->dbmaster = $this->load->database($dbname,true);
		$this->dbmaster->order_by("location_id","desc");
		if($selected == "null"){
			$this->dbmaster->where("location_address is null",null,false);
		}
		if($selected == "-"){
			$this->dbmaster->where("location_address","-");
		}
		if($selected == "unknown"){
			$this->dbmaster->where("location_address","Unknown address");
		}
		if($selected == ""){
			$this->dbmaster->where("location_address","xxxxxx");
		}
		$this->dbmaster->limit(500);
		$q = $this->dbmaster->get("location");
		$data = $q->result();
		$total_data = count($data);
		
		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				printf("CEK KOORDINAT : %s %s, %s of %s \r\n", $data[$i]->location_lat, $data[$i]->location_lng, $i+1, $total_data);
						$location_name = "-";
						//Apigoogle
						//$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0"; //kim
						//$apikey = "AIzaSyAdxIZsEjDmrrkNEW9tujeaiYen5aGoUXE"; //balrich
						//$apikey = "AIzaSyDgDxL_3CpFInoeSmGy-oZElFJeKtgEUWA";
						$apikey = "AIzaSyB6IF8SgmcExwBNjJ1nZ0jIeyagUr8i-zo"; //ab cargo
						
						$location_data = $this->geocode($data[$i]->location_lat, $data[$i]->location_lng, $apikey);
						printf("LOCATION MASTER KOORD: %s \r\n", $location_data['results'][0]['formatted_address']);
						
						if(isset($location_data['results'][0]['formatted_address'])){
							$location_name = $location_data['results'][0]['formatted_address'];
						}
						
						unset($location_data);
						$location_data["location_address"] = $location_name;
							
						$this->dbmaster->limit(1);
						$this->dbmaster->where("location_id",$data[$i]->location_id);
						$this->dbmaster->update("location",$location_data);
						printf("UPDATE LOCATION NAME \r\n");
						printf("==================== \r\n");
						
			}
				
		}
		
		$this->dbmaster->close();
		$this->dbmaster->cache_delete_all();
			
		printf("PROSES LOCATION FINISH \r\n");
	}
	
	function get_location_name_api($dbname="", $selected="")
	{
		printf("PROSES GET KOORDINAT \r\n");
		$now = date("Y-m-d");
		$this->dbmaster = $this->load->database($dbname,true);
		$this->dbmaster->order_by("location_id","asc");
		if($selected == "null"){
			$this->dbmaster->where("location_address is null",null,false);
		}
		if($selected == "-"){
			$this->dbmaster->where("location_address","-");
		}
		if($selected == "unknown"){
			$this->dbmaster->where("location_address","Unknown address");
		}
		if($selected == ""){
			$this->dbmaster->where("location_address","xxxxxx");
		}
		$this->dbmaster->limit(500);
		$q = $this->dbmaster->get("location");
		$data = $q->result();
		$total_data = count($data);
		
		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				printf("CEK KOORDINAT : %s %s, %s of %s \r\n", $data[$i]->location_lat, $data[$i]->location_lng, $i+1, $total_data);
						$location_name = "-";
						//Apigoogle
						$apikey = "AIzaSyB6IF8SgmcExwBNjJ1nZ0jIeyagUr8i-zo"; //ab cargo
						
						$token = "BkaW5kNGhraTR0OnAwNXRkNHQ0a2k0dA16B";
						$authorization = "Authorization:".$token;
						$url = "http://balrich.lacak-mobil.com/customapi/geocode";
						$feature = array();
						
						$feature["lat"] = $data[$i]->location_lat;
						$feature["long"] = $data[$i]->location_lng;
						$feature["apikey"] = $apikey;
						
						$content = json_encode($feature);
						$total_content = count($content);
						
						//printf("Data JSON : %s \r \n",$content);
											
						$curl = curl_init($url);
						curl_setopt($curl, CURLOPT_HEADER, false);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
						curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
						
						$json_response = curl_exec($curl);
						echo $json_response;
						echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
						printf("-------------------------- \r\n");
						
						$data_json = json_decode($json_response,true);
						//printf("Hasil : %s \r \n",$data_json["location"]);
						
						unset($location_data);
						$location_data["location_address"] = $data_json["location"];
							
						$this->dbmaster->limit(1);
						$this->dbmaster->where("location_id",$data[$i]->location_id);
						$this->dbmaster->update("location",$location_data);
						printf("UPDATE LOCATION NAME \r\n");
						printf("==================== \r\n");
						
			}
				
		}
		
		$this->dbmaster->close();
		$this->dbmaster->cache_delete_all();
			
		printf("PROSES LOCATION FINISH \r\n");
	}
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $url = "http://lacak-mobil.com/telegram/telegram_directpost";
        
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
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
