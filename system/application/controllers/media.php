<?php
include "base.php";

class Media extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->helper('common');
		$this->load->helper('email');
	}
	
	//operational by location
	function download($type="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '3G');
        printf("DOWNLOAD MEDIA SECURITY EVIDENCE >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "alarm";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "alarm_";
        $report_source = "alarm_";
		
		if($type == "today"){
			
			$startdate = date("Y-m-d 00:00:00");
            $datefilename = date("Ymd");
			$month = date("F");
			$year = date("Y");
			$enddate = date("Y-m-d 23:59:59");
		}
		else
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
			
		}
        
		$orderby = "asc";
       
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_source = $report_source."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_source = $report_source."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_source = $report_source."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_source = $report_source."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_source = $report_source."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_source = $report_source."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_source = $report_source."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_source = $report_source."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_source = $report_source."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_source = $report_source."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_source = $report_source."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_source = $report_source."desember_".$year;
			break;
		}
        
		$sdate = date("Y-m-d H:i:s", strtotime($startdate)); //sudh wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate)); //sudah wita
        $z =0;
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->order_by("alarm_report_id", $orderby);
		$this->dbreport->where("alarm_report_start_time >=", $sdate);
		$this->dbreport->where("alarm_report_end_time <=", $edate);
		$this->dbreport->where("alarm_report_local_status", 0);
		//$this->dbreport->where("alarm_report_media", 1);//video
		$q = $this->dbreport->get($dbtable);
        $rows = $q->result();
        
        $total_process = count($rows);
        printf("TOTAL PROSES DATA : %s \r\n",$total_process);
        printf("============================================ \r\n"); 
		
		
        for($x=0;$x<count($rows);$x++)
        {
            printf("PROSES VEHICLE : %s MEDIA : %s (%d/%d) \r\n",$rows[$x]->alarm_report_vehicle_no, $rows[$x]->alarm_report_media, ++$z, $total_process);
			$id_report = $rows[$x]->alarm_report_id;
			$imei =  $rows[$x]->alarm_report_imei;
			$datetime = $rows[$x]->alarm_report_start_time;  
			$mediatype = $rows[$x]->alarm_report_media;
			$title = trim($rows[$x]->alarm_report_name);
			$location = $rows[$x]->alarm_report_location_start;
			$jalur = $rows[$x]->alarm_report_jalur;
			$alarmtype = $rows[$x]->alarm_report_type;
			$alarmtype_ex = $this->getalarmname($alarmtype);
			
			$dataalarm = explode("|",$alarmtype_ex);
			$dataalarm_name = $dataalarm[0];
			
			$wita_ex = strtotime($datetime . "+1hours");
			$wita_datetime = date("Y-m-d H:i:s", $wita_ex);
			
			//video
			if($mediatype == 1){
				$file_url = $rows[$x]->alarm_report_downloadurl;
				$file_type = ".mp4";
			}else{
				$file_url = $rows[$x]->alarm_report_fileurl;
				$file_type = ".jfif";
			}
			
			$nowdate = date("Ymd", strtotime($wita_datetime));
			$nowtime = date("His", strtotime($wita_datetime));
			$nowyear = date("Y",strtotime($wita_datetime));
			$m1 = date("F", strtotime($wita_datetime));
			switch ($m1)
			{
				case "January":
				$nowmonth = "januari";
				break;
				case "February":
				$nowmonth = "februari";
				break;
				case "March":
				$nowmonth = "maret";
				break;
				case "April":
				$nowmonth = "april";
				break;
				case "May":
				$nowmonth = "mei";
				break;
				case "June":
				$nowmonth = "juni";
				break;
				case "July":
				$nowmonth = "juli";
				break;
				case "August":
				$nowmonth = "agustus";
				break;
				case "September":
				$nowmonth = "september";
				break;
				case "October":
				$nowmonth = "oktober";
				break;
				case "November":
				$nowmonth = "november";
				break;
				case "December":
				$nowmonth = "desember";
				break;
			}
			
			printf("GET CONTENT FROM : %s .... \r\n",$file_url);
			
			$content = file_get_contents($file_url);
			$save_to = "/home/lacakmobil/public_html/teman.borneo-indobara/public/assets/media/mv03/".$nowyear."/".$nowmonth."/";
			$domain_file = "https://teman.borneo-indobara.com/assets/media/mv03/".$nowyear."/".$nowmonth."/";
			$name_file = $imei."_".$nowdate."_".$nowtime."_".$dataalarm_name."_".$location."_".$jalur."_".$id_report.$file_type;
			file_put_contents($save_to.$name_file, $content);
			
			//update url local
			unset($dataupdate);
			if($mediatype == 1){
				$dataupdate["alarm_report_downloadurl_local"] = $domain_file.$name_file;
			}else{
				$dataupdate["alarm_report_fileurl_local"] = $domain_file.$name_file;
			}
			$dataupdate["alarm_report_local_status"] = 1;
								
			$this->dbreport = $this->load->database("tensor_report",TRUE);
			$this->dbreport->where("alarm_report_id", $id_report);
			$this->dbreport->limit(1);
			$this->dbreport->update($dbtable,$dataupdate);
			printf("PUT CONTENT OKE \r\n",$file_url);
		}
		
		$this->dbreport->close();
		
		$finish_time = date("Y-m-d H:i:s");
        printf("DOWNLOAD MEDIA SECURITY EVIDENCE : DONE %s s/d %s\r\n",$startproses,$finish_time);
	
	}
    
	function getalarmname($id){
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
	
	function curl_download($file_source, $file_target) {
		$rh = fopen($file_source, 'rb');
		$wh = fopen($file_target, 'w+b');
		if (!$rh || !$wh) {
			return false;
		}

		while (!feof($rh)) {
			if (fwrite($wh, fread($rh, 4096)) === FALSE) {
				return false;
			}
			echo '.';
			flush();
		}

		fclose($rh);
		fclose($wh);

		return true;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
