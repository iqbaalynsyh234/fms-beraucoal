<?php
include "base.php";

class Cronpcl extends Base {

	function Cronpcl()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->library('email');
		$this->load->helper('email');
	}
    
	function index(){}
	function postdata()
	{
		$apiurl = $this->config->item("url_api_dhl");
		
		$tokenkey = "3LCK190"; 
		printf("Start PostData \r\n");	
		
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_user_id",3238);
		$this->db->where_in("vehicle_device",$this->config->item("vehicle_pcl_dhl"));
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			printf("Data Vehicle Kosong! \r\n");	
			exit;
		}
		$rows = $q->result();
		printf("Total Vehicle %s \r\n",count($rows));	
		for($i=0;$i<count($rows);$i++)
		{
			printf("Proses Vehicle %s :  %s \r\n",$i+1, $rows[$i]->vehicle_no);	
			if ($rows[$i]->vehicle_active_date2 && ($rows[$i]->vehicle_active_date2 < date("Ymd")))
			{
				$rows[$i]->vehicle_active_date2 = inttodate($rows[$i]->vehicle_active_date2);
				printf("Vehicle %s - Expired %s\r\n",$rows[$i]->vehicle_no, $rows[$i]->vehicle_active_date2);	
			}
			else
			{
				$arr = explode("@", $rows[$i]->vehicle_device);
				$devices[0] = (count($arr) > 0) ? $arr[0] : "";
				$devices[1] = (count($arr) > 1) ? $arr[1] : "";
				$gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, $rows[$i]->vehicle_type);
				
				
				$tgl = floor($gps->gps_utc_date/10000);
				$bln = floor(($gps->gps_utc_date%10000)/100);
				$thn = (($gps->gps_utc_date%10000)%100)+2000;
		
				$jam = floor($gps->gps_utc_coord/10000);
				$min = floor(($gps->gps_utc_coord%10000)/100);
				$det = ($gps->gps_utc_coord%10000)%100;
	
				if(isset($rows[$i]->vehicle_type) && ($rows[$i]->vehicle_type != "GT06" && $rows[$i]->vehicle_type != "A13" && $rows[$i]->vehicle_type != "TK309" && $rows[$i]->vehicle_type != "TK309PTO" && $rows[$i]->vehicle_type != "GT06PTO"))
				{
					$mtime = mktime($jam+7,$min, $det, $bln, $tgl, $thn);
				}
				else
				{
					$mtime = mktime($jam+0,$min, $det, $bln, $tgl, $thn);
				}
				
				$gps->gps_date_fmt = date("Y-m-d", $mtime);
				$gps->gps_time_fmt = date("H:i:s", $mtime);
				
				if ($this->gpsmodel->fromsocket)
				{
					$datainfo = $this->gpsmodel->datainfo;
					$fromsocket = $this->gpsmodel->fromsocket;			
				}
				if (isset($gps) && $gps && date("Ymd", $gps->gps_timestamp) >= date("Ymd"))
				{
					if (! isset($fromsocket))
					{
						$tables = $this->gpsmodel->getTable($rows[$i]);
						$this->db = $this->load->database($tables["dbname"], TRUE);
					}
				}
				else
				if (! isset($fromsocket))
				{	
					$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rows[$i]->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
				}
				
				if (! isset($datainfo))
				{			
					$this->db->order_by("gps_info_time", "DESC");
					$this->db->where("gps_info_device", $rows[$i]->vehicle_device);
					$q = $this->db->get($tables['info'], 1, 0);
					$totalinfo = $q->num_rows();
					if ($totalinfo)
					{
						$rowinfo = $q->row();
					}
				}
				else
				{
					$rowinfo = $datainfo;
					$totalinfo = 1;
				}
				if ($totalinfo == 0)
				{
					$rows[$i]->status = "-";
					$rows[$i]->status1 = false;
					$rows[$i]->status2 = false;
					$rows[$i]->status3 = false;
					$rows[$i]->pulse = "-";
					
				}
				else
				{															
					$ioport = $rowinfo->gps_info_io_port;
					$rows[$i]->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
					$rows[$i]->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
					$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
					
					if(isset($devices[1]) && ($devices[1] == "GT06" || $devices[1] == "A13" || $devices[1] == "TK309" || $devices[1] == "TK309PTO" || $devices[1] == "GT06PTO"))
					{
						if(isset($gps->gps_speed_fmt) && $gps->gps_speed_fmt > 0)
						{
							$row[$i]->status1 = true;
						}
						else
						{
							$row[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
						}
					}
					else
					{
						$row[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
					}
					$rows[$i]->status = $rows[$i]->status2 || $rows[$i]->status1 || $rows[$i]->status3;
					$rows[$i]->totalodometer = round(($rowinfo->gps_info_distance+$rows[$i]->vehicle_odometer*1000)/1000);
					
				}
				
				$rows[$i]->driver = $this->getdriver($rows[$i]->vehicle_id);
				
				$postdata["DEVICEID"] = (string) $devices[0];
				$postdata["GPSTIME"] = $gps->gps_date_fmt." ".$gps->gps_time_fmt;
				$postdata["SPEED"] = $gps->gps_speed;
				$postdata["DIRECT"] = number_format($gps->gps_course, 0, '.', '.');
				$postdata["LONGITUDE"] = $gps->gps_longitude_real_fmt;
				$postdata["LATITUDE"] = $gps->gps_latitude_real_fmt;
				if($rows[$i]->status1)
				{
					$postdata["ENGINE"] = "ON";
				}
				else
				{
					$postdata["ENGINE"] = "OFF";
				}
				$postdata["SENSOR1"] = "OFF";
				$postdata["SENSOR2"] = "OFF";
				if(isset($rows[$i]->driver) && $rows[$i]->driver != "" && $rows[$i]->driver != false)
				{
					$postdata["DRIVER"] = $rows[$i]->driver;
				}
				else
				{
					$postdata["DRIVER"] = "";
				}
				$postdata["FUEL"] = 0;
				if($rows[$i]->totalodometer > 0)
				{
					$postdata["ODO"] = $rows[$i]->totalodometer;
				}
				else
				{
					$postdata["ODO"] = 0;
				}
				
				$a = "sTokenKey=".$tokenkey."&sData="."[".json_encode($postdata)."]";
				printf("Data Post :  %s \r\n",$a);
				
				//Proses Post Data
				
				if($gps->gps_status == "A")
				{
					printf("Proses Post Data : %s \r\n",date("Y-m-d H:i:s"));
					$ch = curl_init($this->config->item("url_api_dhl"));
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
					curl_setopt($ch, CURLOPT_POSTFIELDS, $a);                                                                  
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
					curl_setopt($ch, CURLOPT_HTTPHEADER, array
					(                                                                          
						'Content-Type: application/x-www-form-urlencoded',                                                                                
						'Content-Length: ' . strlen($a))                                                                       
					);                                                                                                                   
					$result = curl_exec($ch);
					printf("Status :  %s \r\n",$result);
				}
				printf("========================================================= \r\n");
			}
		}
		printf("Finish PostData \r\n");	
		exit;
	}
	
	function getdriver($driver_vehicle) {
	
	$this->dbtransporter = $this->load->database('transporter',true);
	$this->dbtransporter->select("*");
	$this->dbtransporter->from("driver");
	$this->dbtransporter->order_by("driver_update_date","desc");
	$this->dbtransporter->where("driver_vehicle", $driver_vehicle);
	$this->dbtransporter->limit(1);
	$q = $this->dbtransporter->get();
	
	if ($q->num_rows > 0 ){
		$row = $q->row();
		$data = $row->driver_name;
		return $data;
		$this->dbtransporter->close();
	}
	else {
	$this->dbtransporter->close();
	return false;
	}
	
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
