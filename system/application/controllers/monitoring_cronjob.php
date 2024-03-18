<?php
include "base.php";

class Monitoring_cronjob extends Base {
	var $otherdb;
	function Monitoring_cronjob()
	{
		parent::Base();
		$this->load->model("gpsmodel_autosetting");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
		$this->load->model("gpsmodel");
	}

	function gps_offline($userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));

		// START FOR DEFINE TABLE
		$cronstartdate = date("Y-m-d H:i:s");
		$dbtable    = "report_gps_status_historikal";

		printf("===STARTING AUTOCHECK GPS OFFLINE %s WIB %s WITA\r\n", $nowtime, $nowtime_wita);
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
			$this->db->order_by("vehicle_no","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_mv03,vehicle_type,vehicle_autocheck");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			$total_offline = 0;
			$offline = 0;
			$dataoffline = array();
			$dataarrayforinsert = array();
			$text_info = "";
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$j2 = $x+1;

				//printf("===PROCESS VEHICLE %s of %s, %s, %s, %s of %s\r\n", $j2, $totalvehicle, $rowvehicle[$x]->vehicle_no, $rows[$i]->company_name, $j, $totalcompany);
				$json            = json_decode($rowvehicle[$x]->vehicle_autocheck);
				$auto_status     = $json->auto_status;
				$auto_lastupdate = $json->auto_last_update;
				$vno             = $rowvehicle[$x]->vehicle_no;
				$vname           = $rowvehicle[$x]->vehicle_name;
				$vdevice         = $rowvehicle[$x]->vehicle_device;
				$vcompany        = $rowvehicle[$x]->vehicle_company;

				$info = $vno." - "."(".$auto_lastupdate.")"." \n";
				//print_r($json);
				if($auto_status == 'M'){

					array_push($dataoffline,$info);
					$dataarrayforinsert = array(
						"gpsoffline_vehicle_no"          => $vno,
						"gpsoffline_vehicle_name"        => $vname,
						"gpsoffline_vehicle_device"      => $vdevice,
						"gpsoffline_vehicle_companyid"   => $vcompany,
						"gpsoffline_vehicle_companyname" => $rows[$i]->company_name,
						"gpsoffline_lastupdate"          => $auto_lastupdate,
						"gpsoffline_status" 	           => "OFFLINE",
						"gpsoffline_data_submited" 	     => date('Y-m-d H:i:s', strtotime('+1 hours')),
					);

					if (sizeof($dataarrayforinsert) > 0) {
						$this->dbtensor = $this->load->database("tensor_report", true);
						$insertNow = $this->dbtensor->insert($dbtable, $dataarrayforinsert);
							if ($insertNow) {
									printf("==SUCCESS INSERT DATA GPS OFFLINE \r\n");
									// printf("================================= \r\n");
							}else {
								printf("==FAILED INSERT DATA GPS OFFLINE \r\n");
								// printf("================================= \r\n");
							}
					}

					printf("===GPS OFFLINE %s %s  \r\n", $vno,$auto_lastupdate);

				}
			}

			for ($j=0;$j<count($dataoffline);$j++)
			{
				$no_urut = $j+1;
				$text_info .= $no_urut.". ".$dataoffline[$j];
			}

			$total_offline = count($dataoffline);

			//send telegram
			$title_name = "CONTRACTOR ".$rows[$i]->company_name;
				$message = urlencode(
						"".$title_name." \n".
						"TANGGAL INFORMASI: ".$nowtime_wita." WITA"." \n".
						"DATA OFFLINE: \n".$text_info." \n".
						"TOTAL GPS OFFLINE: ".$total_offline." \n"
					);
			sleep(2);
			$sendtelegram = $this->telegram_direct("-637068985",$message); //GPS OFFLINE
			//$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING

			printf("===SENT TELEGRAM OK\r\n");



		}
		$finishtime = date("Y-m-d H:i:s");


		$this->db->close();
		$this->db->cache_delete_all();


		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK HOUR DATA %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}

	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        //$url = "http://lacak-mobil.com/telegram/telegram_directpost";
		//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
		//$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
		$url = "http://admin.abditrack.com/telegram/telegram_directpost";

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
