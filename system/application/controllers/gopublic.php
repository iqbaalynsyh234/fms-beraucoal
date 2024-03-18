<?php
include "base.php";

class Gopublic extends Base {

	function Gopublic()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function lastposition()
	{
		header("Content-Type: application/json");
		
		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($now ." " ."23:59:59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($now ." " . "23:59:59")));
		
		//all vehicle		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", 3212); //3212 Test TAG
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
		//print_r($rm);exit;
			
		foreach($rm as $v)
		{
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
			$rowv[] = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->where("gps_time <=",$fdate);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			$rows[] = $q->row();
		}
		
		
		
		$trows = count($rows);
		
		for($i=0;$i<$trows;$i++)
		{
			
			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}
			
			
			//Find Vehicle Odometer
			foreach($rowv as $vodo)
			{
				if (isset($rows[$i]->gps_name))
				{
					if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$vodometer = $vodo->vehicle_odometer;
						$rows[$i]->vehicle_no = $vodo->vehicle_no;
						$rows[$i]->vehicle_name = $vodo->vehicle_name;
					}
				}
			}
			
			if (isset($rows[$i]->gps_info_distance))
			{
				$rows[$i]->result_gps_odometer = round(($rows[$i]->gps_info_distance+$vodometer*1000)/1000);
			}
			
			if (isset($rows[$i]->gps_info_io_port))
			{
				$ioport = $rows[$i]->gps_info_io_port;
				$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
			}
			
			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			}
			
			if (isset($rows[$i]->gps_latitude))
			{
				$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}
			
			if (isset($rows[$i]->gps_longitude_real))
			{
				$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			}
			
			if (isset($rows[$i]->gps_latitude_real))
			{
				$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
			}
			

			foreach($rowv as $vodo)
			{
				if (isset($rows[$i]->gps_name))
				{
					if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$vdevice = $vodo->vehicle_device;
					}
				}
			}
			
			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->geofence_location = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vodo->vehicle_device);
			}
			
		}
		
		echo json_encode( $rows );

	}
	
	function getPosition($longitude, $ew, $latitude, $ns){
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);
					
		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");	
					
		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
		
		return $georeverse;
	}
	
	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_device) {
		
		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_device);

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{			
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }
            
		}else
        {
            return false;
        }

	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
