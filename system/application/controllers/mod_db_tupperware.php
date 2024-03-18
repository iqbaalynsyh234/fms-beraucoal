<?php
include "base.php";

class Mod_db_tupperware extends Base {

	function __construct() 
	{ 
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
	}
	function index(){} 
	function dbtupper()
	{
		$myname = $this->uri->segment("3");
		$mydr = $this->uri->segment("4");
		$myso = $this->uri->segment("5");
		$mydb = $this->uri->segment("6");
		$host = $this->uri->segment("8");
		//echo "Vehicle :".$myname.","."DR:".$mydr.","."SO:".$myso.","."DB:".$mydb.","."Host:".$host;
		if(!$myname){ echo "Data Expired [name] !"; exit; }
		if(!$mydr){ echo "Data Expired [dr]!"; exit; }
		if(!$myso){ echo "Data Expired [so]!"; exit; }
		if(!$mydb){ echo "Data Expired [dbcode]!"; exit; }
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_group",422);
		$this->db->where("vehicle_device", $myname.'@'.$host);
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			echo "Tupperware Delivery Support - DB Vehicle Monitoring";
			echo "<br />";
			echo "Data Expired"." "."[".date("d-m-Y")."]"." "."!"; 
			exit;
		}
		$row = $q->row();
		$this->params['title'] = "Tupperware Application";
		$this->params["ishistory"] = "off";		
		$this->params["zoom"] = $this->config->item("zoom_realtime");
		$this->params["data"] = $row;
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["updateinfo"] = $this->load->view('updateinfo_db_tupperware', $this->params, true);
		$this->load->view("mapdb", $this->params);	
	}
	
	function lastinfo()
	{
		$device = isset($_POST['device']) ? $_POST['device'] : "";
		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime'] : 0;
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $device);		
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}
		$row = $q->row();
		// cek expire 
		if ($row->vehicle_active_date2 && ($row->vehicle_active_date2 < date("Ymd")))
		{
			$row->vehicle_active_date2_fmt = inttodate($row->vehicle_active_date2);
			$json = json_decode($row->vehicle_info);
			echo json_encode(array("info"=>"expired", "vehicle"=>$row));
			return;
		}
		$arr = explode("@", $device);
		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";
		$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);
		if ($this->gpsmodel->fromsocket)
		{
			$datainfo = $this->gpsmodel->datainfo;
			$fromsocket = $this->gpsmodel->fromsocket;			
		}
		$gtps = $this->config->item("vehicle_gtp");		
		if (! in_array(strtoupper($row->vehicle_type), $gtps))
		{			
			$row->status = "-";
			$taktif = dbintmaketime($row->vehicle_active_date, 0);
			$json = json_decode($row->vehicle_info);
		}
		else
		{			
			if (isset($row->gps) && $row->gps && date("Ymd", $row->gps->gps_timestamp) >= date("Ymd"))
			{
				if (! isset($fromsocket))
				{
					$tables = $this->gpsmodel->getTable($row);
					$this->db = $this->load->database($tables["dbname"], TRUE);
				}

			}
			else
			if (! isset($fromsocket))
			{	
				$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
				$this->db = $this->load->database("gpshistory", TRUE);
			}
			// ambil informasi di gps_info
			if (! isset($datainfo))
			{			
				$this->db->order_by("gps_info_time", "DESC");
				$this->db->where("gps_info_device", $device);
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
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
				$row->pulse = "-";
			}
			else
			{															
				$ioport = $rowinfo->gps_info_io_port;
				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
				$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				$row->status = $row->status2 || $row->status1 || $row->status3;
				$pulses = $this->config->item("vehicle_pulse");
				if (! in_array(strtoupper($row->vehicle_type), $pulses))
				{
					$json = json_decode($row->vehicle_info);
				}
				else
				{				
					//$rowinfo->gps_info_ad_input = "00B0742177";
					$pulsa = number_format(hexdec(substr($rowinfo->gps_info_ad_input, 0, 5)), 0, "", ".");					
					$aktif = hexdec(substr($rowinfo->gps_info_ad_input, 5));
					$taktif = dbintmaketime1($aktif, 0);
					if (date("Y", $taktif) < 2000)
					{
						$row->pulse = false;
					}
					else
					{									
						$row->pulse = sprintf("Rp %s", $pulsa);
						$row->masaaktif = date("d/m/Y", $taktif);
					}
				}
				$row->totalodometer = round(($rowinfo->gps_info_distance+$row->vehicle_odometer*1000)/1000);
			}
		}
		$t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date1_fmt = date("M, jS Y", $t);
		$t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date2_fmt = date("M, jS Y", $t);
		$arr = explode("@", $device);
		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";
		$row->vehicle_device_name = $devices[0];
		$row->vehicle_device_host = $devices[1];
		$params["vehicle"] = $row;
		if (! $row->gps)
		{
			echo json_encode(array("info"=>"", "vehicle"=>$row));
			return;
		}
		$delayresatrt = mktime() - $row->gps->gps_timestamp;
		$kdelayrestart = $this->config->item("restart_delay")*60;
		if (true)
		{
			$restart = $this->smsmodel->restart($row->vehicle_type, $row->vehicle_operator);
			$row->restartcommand = $restart;					
		}
		else
		{
			$row->restartcommand = "";
		}
		
		//get geofence location
		$row->geofence_location = $this->getGeofence_location($row->gps->gps_longitude, $row->gps->gps_ew, $row->gps->gps_latitude, $row->gps->gps_ns, $row->vehicle_device);   
		//Transporter Tupperware
			$row->id_booking = $this->get_id_booking($row->vehicle_device);
			if (isset($row->id_booking) && $row->id_booking != "")
			{
				$row->noso = $this->get_noso($row->vehicle_device);
				//$row->slcars = $this->get_slcars($row->id_booking);
			}
			if (isset($row->id_booking) && $row->id_booking != "")
			{
				$row->nodr = $this->get_nodr($row->vehicle_device);
			}
			if (isset($row->noso) && $row->noso != "")
			{
				if (isset($row->nodr) && $row->nodr != "")
				{
					$row->dbcode = $this->get_dbcode($row->vehicle_device);
				}
			}
		
		if (isset($showoff))
		{
			$row->startoff = $showoff;
			$row->offduration = $showduration;
		}
		if (isset($showon))
		{
			$row->starton = $showon;
			$row->onduration = $showdurationon;
		}
		$params["devices"] = $devices;
		$params["data"] = $row->gps;
		$info = $this->load->view("map/info_db_tupperware", $params, TRUE);				
		echo json_encode(array("info"=>$info, "vehicle"=>$row));				
	}
	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_device) 
	{
		$this->db = $this->load->database("default", TRUE);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);
		$geo_name = "''";
		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
                            AND (geofence_name <> %s)
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
                            AND (geofence_vehicle = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $geo_name, $lng, $lat, $vehicle_device);
		//print_r($sql);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			$row = $q->row();
			$data = $row->geofence_name;
			return $data;
		}
		else
		{
			return false;
		}
	}
	function get_id_booking($v)
	{
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->order_by("id","desc");
		$this->dbtrans->select("booking_id");
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status",1);
		$this->dbtrans->where("booking_vehicle",$v);
		$this->dbtrans->limit(2);
		$q = $this->dbtrans->get("id_booking");
		$data = $q->result();
		$mydata = "";
		if ($q->num_rows > 0)
		{
			for ($i=0;$i<count($data);$i++)
			{
			$mydata .= $data[$i]->booking_id;
			$mydata .= "|";
			}
				
			$this->dbtrans->close();
			return $mydata;
		}
		else
		{
		$this->dbtrans->close();
		return false;
		}
	}
	function get_noso($v)
	{
		$mydr = "";
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->select("booking_id");
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status",1);
		$this->dbtrans->where("booking_vehicle",$v);
		$q = $this->dbtrans->get("id_booking");
		$rows = $q->result();
	
		if (isset($rows))
		{
			for ($i=0;$i<count($rows);$i++)
			{
			$this->dbtrans->where("transporter_dr_booking_id",$rows[$i]->booking_id);
			if (isset($this->sess->dist_code))
			{
			$this->dbtrans->where("transporter_db_code",$this->sess->dist_code);
			}
			$qdr = $this->dbtrans->get("tupper_dr");
				$rdr = $qdr->result();
					if (isset($rdr) && count($rdr)>0)
					{
					for ($i=0;$i<count($rdr);$i++)
					{
					$mydr .= $rdr[$i]->transporter_dr_so;
					$mydr .= "|";
					//$mydr .= $rdr->transporter_dr_dr;
					//$mydr .= "|";
					}
					}
					}
						
					$this->dbtrans->close();
					return $mydr;
		}
		else
		{
		$this->dbtrans->close();
		return false;
		}
	}
	function get_nodr($v)
	{
		$mydr = "";
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->select("booking_id");
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status",1);
		$this->dbtrans->where("booking_vehicle",$v);
		$q = $this->dbtrans->get("id_booking");
		$rows = $q->result();
	
		if (isset($rows))
		{
			for ($i=0;$i<count($rows);$i++)
			{
			$this->dbtrans->where("transporter_dr_booking_id",$rows[$i]->booking_id);
			if (isset($this->sess->dist_code))
			{
			$this->dbtrans->where("transporter_db_code",$this->sess->dist_code);
			}
			$qdr = $this->dbtrans->get("tupper_dr");
				$rdr = $qdr->result();
					if (isset($rdr) && count($rdr)>0)
					{
					for ($i=0;$i<count($rdr);$i++)
					{
					//$mydr .= $rdr[$i]->transporter_dr_so;
						//$mydr .= "|";
						$mydr .= $rdr[$i]->transporter_dr_dr;
						$mydr .= "|";
					}
					}
					}
					$this->dbtrans->close();
					return $mydr;
		}
		else
		{
		$this->dbtrans->close();
		return false;
		}
	}
	function get_dbcode($v)
	{
		$mydbcode = "";
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->select("booking_id");
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status",1);
		$this->dbtrans->where("booking_vehicle",$v);
		$q = $this->dbtrans->get("id_booking");
		$rows = $q->result();
	
		if (isset($rows))
		{
			for ($i=0;$i<count($rows);$i++)
			{
			$this->dbtrans->select("transporter_db_code, dist_name");
			$this->dbtrans->where("transporter_dr_booking_id",$rows[$i]->booking_id);
			$this->dbtrans->where("transporter_dr_status",1);
			$this->dbtrans->join("transporter_dist_tupper","dist_code = transporter_db_code","left_outer");
					if (isset($this->sess->dist_code))
					{
					$this->dbtrans->where("transporter_db_code",$this->sess->dist_code);
					}
					$qdr = $this->dbtrans->get("tupper_dr");
							$rdr = $qdr->result();
	
							if (isset($rdr) && count($rdr)>0)
							{
							for($j=0;$j<count($rdr);$j++)
							{
							if (isset($rdr[$j]->transporter_db_code) && $rdr[$j]->transporter_db_code != 0)
							{
							$mydbcode .= $rdr[$j]->transporter_db_code.",".$rdr[$j]->dist_name;
							$mydbcode .= "|";
							}
							}
							}
			}
				
			$this->dbtrans->close();
			return $mydbcode;
		}
		else
		{
			$this->dbtrans->close();
			return false;
		}
	}
	
}