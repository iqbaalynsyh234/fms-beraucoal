<?php
include "base.php";

class Balrich_request extends Base {

	function Balrich_request()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->library('ftp');
		$this->load->library('email');

	}
	
	function cekreq_balrich()
	{
		//cek req balrich
		$database_request = "balrich_request";
		$this->db_req = $this->load->database($database_request, TRUE);
		$nowdate = date('Y-m-d H:i:s');
		
		$this->db_req->order_by("id_request","asc");
		$this->db_req->where("rec_id", 0);
		$q_req = $this->db_req->get("trc_req_data");
		if ($q_req->num_rows() == 0)
		{
			printf("No Request at %s !! \r\n", $nowdate);
			return;
		}
		$rows_req = $q_req->result();
		$total_req = count($rows_req);
		
		$j = 1;
		for ($i=0;$i<count($rows_req);$i++)
		{
			printf("Process No Req %s, For %s (%d/%d)\n", $rows_req[$i]->id_request, $rows_req[$i]->Police_Number, $j, $total_req);
			printf("execute %s\r\n", $rows_req[$i]->Police_Number);
			printf("======================================\r\n");
			//cari mobil di database
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_name,vehicle_device,vehicle_no");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", 1032);//user balrich
			$this->db->where("vehicle_no like", '%'.$rows_req[$i]->Police_Number.'%');
			$this->db->limit(1);
			$q = $this->db->get("vehicle");
			
			//jika tidak ada kendaraan di DB master
			if ($q->num_rows() == 0)
			{
				printf("No Data Vehicles !!\r\n");
				//insert lokasi tidak ditemukan
				$lokasi = "Tidak Ada Lokasi";
				$tanggal_gps = date("Y-m-d H:i:s",strtotime("2001-01-01 00:00:00"));
				
				//update status rec_id = 1
				unset($data);
				$data["id_request"] = $rows_req[$i]->id_request;
				$data["Police_Number"] = $rows_req[$i]->Police_Number;
				$data["lokasi"] = $lokasi;
				$data["tgl_gps"] = date("Y-m-d H:i:s", strtotime($tanggal_gps));
								
				$this->db_req->insert("trc_val_data",$data);
								
				//update status rec
				unset($data_status);
				$data_status["rec_id"] = 1;
					
				$this->db_req->where("id_request", $rows_req[$i]->id_request);
				$this->db_req->update("trc_req_data", $data_status);
				
				//return;
			}
			
			//jika ada kendaraan di DB Master
			//looping request
			if ($q->num_rows() > 0)
			{
				$row = $q->row();
				$vehicle_device = $row->vehicle_device;
				$vehicle_no = $row->vehicle_no;
			
				//cari per mobil
				printf("Run Cron Check Last Info at %s %s \r\n", $vehicle_no, $vehicle_device);
				printf("======================================\r\n");
				
						//cek posisi terakhir
							$vehicledevice = $row->vehicle_device;
							
							$this->db->where("vehicle_status", 1);
							$this->db->where("vehicle_device", $vehicledevice);
							$qv = $this->db->get("vehicle");
						
							if ($qv->num_rows() == 0)
							{
								printf("No Data Vehicle in Database \r\n");
							}
						
							$rowvehicle = $qv->row();
							$rowvehicles = $qv->result();
							
							$t = $rowvehicle->vehicle_active_date2;
							$now = date("Ymd");
							
							if ($t < $now)
							{
								printf("Mobil Expired \r\n");
							}
							
							list($name, $host) = explode("@", $rowvehicle->vehicle_device);

							$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);
			/*stop temp*/ //print_r($gps);exit();
							if ($this->gpsmodel->fromsocket)
							{
								$datainfo = $this->gpsmodel->datainfo;
								$fromsocket = $this->gpsmodel->fromsocket;			
							}
									
							if (! $gps)
							{
								printf("Gps Belum Aktif \r\n");
							}

							$gtps = $this->config->item("vehicle_gtp");

							//$dir = $gps->direction-1;
							$dirs = $this->config->item("direction");

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
								}
								else
								{
									$rowinfo = isset($datainfo) ? $datainfo : $q->row();					
									$ioport = $rowinfo->gps_info_io_port;
										
									$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
									$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
									$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
										
									$engine = $status1 ? "ON" : "OFF";
		
								}			

							}

							$this->db = $this->load->database("default", TRUE);
						
							if(isset($gps->gps_time)){
								printf("===GPS DETECTED=== \r\n");
								$lokasi = $gps->georeverse->display_name;
								$tanggal_gps_old = $gps->gps_time;
								$tanggal_gps = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($tanggal_gps_old)));
								
							}else{
								printf("===NO DATA GPS=== \r\n");	
								$lokasi = "Tidak Ada Lokasi";
								$tanggal_gps = date("Y-m-d H:i:s",strtotime("2001-01-01 00:00:00"));
							}
							
							//print_r($tanggal_gps." ".$lokasi);exit();
							
								//insert lokasi tabel hasil
								
								unset($data);
								$data["id_request"] = $rows_req[$i]->id_request;
								$data["Police_Number"] = $rows_req[$i]->Police_Number;
								$data["lokasi"] = $lokasi;
								$data["tgl_gps"] = date("Y-m-d H:i:s", strtotime($tanggal_gps));
								
								$this->db_req->insert("trc_val_data",$data);
								
								//update status rec
								unset($data_status);
								$data_status["rec_id"] = 1;
					
								$this->db_req->where("id_request", $rows_req[$i]->id_request);
								$this->db_req->update("trc_req_data", $data_status);
								
			}
			
			$j++;
		}
						
		$this->db_req->close();
		$this->db_req->cache_delete_all();
		$this->db->close();
		$this->db->cache_delete_all();
		
		$enddate = date('Y-m-d H:i:s');
		printf("FINISH Cek Req Balrich at %s \r\n", $enddate);
		printf("============================== \r\n");

	}
	
	//for all
    function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);
        
        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");    
        $georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
        
        return $georeverse;
    }
	
	

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
