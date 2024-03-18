<?php
include "base.php";

class Transtrackapi extends Base {

	function Transtrackapi()
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
	function lastposition()
	{
		$apiurl = $this->config->item("url_api_transtrack");
		
		printf("Start PostData \r\n");	
		
		$this->db->order_by("vehicle_id","asc");
		$this->db->group_by("vehicle_device");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_device",$this->config->item("vehicle_simarno_transtrack"));
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
				
				//use UTC
				if (!in_array(strtoupper($rows[$i]->vehicle_type), $this->config->item("vehicle_others")))
				{
			
					$mtime = mktime($jam, $min, $det, $bln, $tgl, $thn);
				}
				else
				{
					$mtime = mktime($jam-7,$min, $det, $bln, $tgl, $thn);
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
					
					if(isset($devices[1]) && ($devices[1] == "GT06" || $devices[1] == "A13" || $devices[1] == "TK309" || $devices[1] == "TK309PTO" || $devices[1] == "GT06PTO" || $devices[1] == "TK315" || $devices[1] == "TK309N" || $devices[1] == "TK315N" || $devices[1] == "TK315DOOR" || $devices[1] == "TK309_NEW"))
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
				
				$timestamp_ex = date("Y-m-d H:i:s", strtotime($gps->gps_date_fmt." ".$gps->gps_time_fmt));
				$timestamp_new = strtotime($timestamp_ex);
				
				$postdata["id"] = (string) $devices[0];
				$postdata["lon"] = $gps->gps_longitude_real_fmt;
				$postdata["lat"] = $gps->gps_latitude_real_fmt;
				$postdata["timestamp"] = $timestamp_new;
				$postdata["hdop"] = 0;
				$postdata["altitude"] = 0;
				$postdata["speed"] = $gps->gps_speed_fmt;
				//$postdata["DIRECT"] = number_format($gps->gps_course, 0, '.', '.');
				
				if($rows[$i]->status1)
				{
					$postdata["ignition"] = "true";
				}
				else
				{
					$postdata["ignition"] = "false";
				}
				
				if($rows[$i]->totalodometer > 0)
				{
					$postdata["odometer"] = $rows[$i]->totalodometer;
				}
				else
				{
					$postdata["odometer"] = 0;
				}
				
				//Proses Post Data
				
				if($gps->gps_status == "A")
				{
					//http://telematics.transtrack.id:6055?id=123456&lat=-6.12245&lon=107.19879&timestamp=1579235106&hdop=1&altitude=916&speed=20&ignition=true&odometer=120300
					//                                      id=002100004561&lat=106.7873&timestamp=1595220546&hdop=0&altitude=0&speed=0&ignition=OFF&odometer=186128
					
					
					$url = $apiurl;
					
					$myvars = 'id='.$postdata['id'].'&lat='.$postdata['lat'].'&lon='.$postdata['lon'].'&timestamp='.$postdata['timestamp'].'&hdop='.$postdata['hdop'].
					          '&altitude='.$postdata['altitude'].'&speed='.$postdata['speed'].'&ignition='.$postdata['ignition'].'&odometer='.$postdata['odometer'];
				
					printf("Data Post :  %s \r\n",$url.$myvars);
					$ch = curl_init( $url );
					curl_setopt( $ch, CURLOPT_POST, 1);
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt( $ch, CURLOPT_HEADER, 0);
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

					$response = curl_exec( $ch );
					printf("Status :  %s \r\n",$response);

					/*printf("Proses Post Data : %s \r\n",date("Y-m-d H:i:s"));
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
					*/
				}
				printf("========================================================= \r\n");
			}
		}
		printf("Finish PostData \r\n");	
		exit;
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
