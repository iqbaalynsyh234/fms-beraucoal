<?php
include "base.php";

class Balrichsmu_conn extends Base {

	function Balrichsmu_conn()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->helper('xml');
        $this->load->model("historymodel");
		$this->load->library('ftp');
		$this->load->library('email');
		$this->load->helper('download');

	}
	
	function index()
	{
		$dom = xml_dom();
		$vehicles = xml_add_child($dom, 'vehicles');
		
		
		$nowdate = date('Y-m-d H:i:s');
		$balrich_id = 1032;
		$offset = 0; 
		$i = 0;
		//printf("SMU CONNECTION START !! \r\n");
		//edited select mobil smu balrich
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("smu_vehicle_no","asc");
		$this->dbtransporter->where("smu_flag", 0);
		$q_v = $this->dbtransporter->get("balrich_smu");
		
		if ($q_v->num_rows() == 0)
		{
			//printf("No Data Vehicle !! \r\n");
			return;
		}
		
		$rows_v = $q_v->result();
		$total_vehicle = count($rows_v);
		
		$nowdate = date("Y-m-d H:i:s");
		$nowdate_gps = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($nowdate)));
		$dateinterval = new DateTime($nowdate_gps);
		$dateinterval->sub(new DateInterval('PT3M'));
		$mindate_gps = $dateinterval->format('Y-m-d H:i:s');
		
		$limitdate = "2016-02-11 00:00:00";
		$limitdate_gps = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($limitdate)));
	
		for ($p=0;$p<count($rows_v);$p++)
		{	
			$this->db = $this->load->database("default", TRUE);
			$this->db->where("vehicle_id", $rows_v[$p]->smu_vehicle_id);
			$q_vehicle = $this->db->get("vehicle");
			$rowvehicle = $q_vehicle->row();
			
			$vehicle_front = substr($rowvehicle->vehicle_no,0,1);
			$vehicle_mid = substr($rowvehicle->vehicle_no,1,4);
			$vehicle_end = substr($rowvehicle->vehicle_no,5,3); 
			
			//printf("PROCESS : %s,%s\r\n", $rowvehicle->vehicle_no, $rowvehicle->vehicle_device);
			
			$json = json_decode($rowvehicle->vehicle_info);
			
			if ($rowvehicle->vehicle_info)
			{	
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			if (! isset($table))
			{		
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
			
			//printf("SEARCH DATA  : %s\r\n", $database);
			
			if($table){
				
				$this->db->select("	gps_id,gps_name,gps_status,gps_time,gps_latitude_real,gps_longitude_real,
									gps_speed,gps_course,gps_odometer,gps_sent,
									gps_info_id,gps_info_device,gps_info_time,gps_info_io_port,gps_info_distance");
				$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
				$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
				$this->db->where("gps_time <=", $mindate_gps);
				$this->db->where("gps_time >=", $limitdate_gps);
				$this->db->where("gps_sent",0);
				$this->db->order_by("gps_time","desc");
				$this->db->from($table);
				$q = $this->db->get();
				$this->db->flush_cache();
				$rows = $q->result();
				
				if(count($rows) > 0){
					for ($g=0;$g<count($rows);$g++)
					{
						//print data
						$gpsid = $rows[$g]->gps_id;
						$gpsinfoid = $rows[$g]->gps_info_id;
						$license_num = $vehicle_front." ".$vehicle_mid." ".$vehicle_end;			
						$time = date("Y-m-d H:i:s");
						$validgpstime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$g]->gps_time)));
						$longitude = $rows[$g]->gps_longitude_real;
						$latitude = $rows[$g]->gps_latitude_real;
						$speed = number_format($rows[$g]->gps_speed*1.852, 0, "", ".");
						$direct = $rows[$g]->gps_course;
						//$odometer = round(($rows[$g]->gps_info_distance+$rowvehicle->vehicle_odometer*1000)/1000);
						$odometer = round(($rows[$g]->gps_info_distance)/1000);
						$eng = $rows[$g]->gps_info_io_port;
						if($eng){
							if($eng == "0000100000"){
								$engine = "true";
							}else{
								$engine = "false";
							}
						}
						
						$vehicle = xml_add_child($vehicles, 'vehicle');
						xml_add_child($vehicle, 'LICENSE_NUM', $license_num);
						xml_add_child($vehicle, 'LONGITUDE', $longitude);
						xml_add_child($vehicle, 'LATITUDE', $latitude);
						xml_add_child($vehicle, 'TIME', $time);
						xml_add_child($vehicle, 'VALIDGPSTIME', $validgpstime);
						xml_add_child($vehicle, 'SPEED', $speed);
						xml_add_child($vehicle, 'DIRECT', $direct);
						xml_add_child($vehicle, 'ODOMETER', ($odometer+$rowvehicle->vehicle_odometer));
						xml_add_child($vehicle, 'ENGINEON', $engine);
		
						///printf("UPDATE STATUS GPS ID: %s\r\n", $gpsid);
						///printf("UPDATE STATUS GPS INFO ID: %s\r\n", $gpsinfoid);
						
						//update info sent
						unset($dataupdate);
						$dataupdate["gps_sent"] = 1;
						$this->db->where("gps_id",$gpsid);
						$this->db->update($table,$dataupdate);
						
						unset($datainfoupdate);
						$datainfoupdate["gps_info_sent"] = 1;
						$this->db->where("gps_info_id",$gpsinfoid);
						$this->db->update($tableinfo,$datainfoupdate);
						
						$this->db->flush_cache();
						$this->db->close();
						$this->db->cache_delete_all();
					}
				}else{
					//printf("NO DATA !! \r\n");
				}
			
			}
				//printf("================ \r\n" );
			//print_r($license_num." ".$time." ".$validgpstime." ".$longitude." ".$latitude." ".$speed." ".$direct." ".$odometer." ".$engine. " - ".$gpsid." ".$gpsinfoid);exit();
		}

			//printf("FINISH !!\r\n" );
			xml_download($dom); 
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
