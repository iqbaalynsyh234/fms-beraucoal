<?php
include "base.php";

class Cron_alert extends Base {

	function Cron_alert()
	{
		parent::Base();	
	}
	
	function geo_alert()
	{
		//1= Masuk 2=Keluar
		$this->db->select("vehicle_device");			
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			return;
		}
		$vdevice = $q->result();
		//print_r($vdevice);exit;
		foreach ($vdevice as $valert)
		{
			$this->db->order_by("geoalert_id", "desc");
			$this->db->where("geoalert_vehicle", $valert->vehicle_device);
			$this->db->limit(1);
			$qalert = $this->db->get("geofence_alert");
			$row_alert[] = $qalert->row();
		}
		
		//print_r($row_alert);exit;
		$trow = count($row_alert);
		$this->dbtransporter = $this->load->database("transporter", true);
		for($i=0;$i<$trow;$i++)
		{
			$this->dbtransporter->select("alert_geo_id, alert_geo_vehicle");
			$this->dbtransporter->order_by("alert_geo_id", "desc");
			$this->dbtransporter->limit(1);
			$this->dbtransporter->where("alert_geo_id", $row_alert[$i]->geoalert_id);
			$q_alert = $this->dbtransporter->get("geo_alert");
			$row_q_alert = $q_alert->row();
			if (count($row_q_alert)==0) 
			{
				unset($new_alert);
				$new_alert["alert_geo_id"] = $row_alert[$i]->geoalert_id;
				$new_alert["alert_geo_vehicle"] = $row_alert[$i]->geoalert_vehicle;
				$new_alert["alert_geo_direction"] = $row_alert[$i]->geoalert_direction;
				$new_alert["alert_geo_geofence"] = $row_alert[$i]->geoalert_geofence;
				$new_alert["alert_geo_time"] = $row_alert[$i]->geoalert_time;
				$new_alert["alert_geo_user_id"] = $this->sess->user_id;
				$this->dbtransporter->insert("geo_alert", $new_alert);
			}
		}
		return;
	}
	
	function geo_alert_show()
	{
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("alert_geo_id", "asc");
		$this->dbtransporter->where("alert_geo_user_id", $this->sess->user_id);
		$this->dbtransporter->where("alert_geo_show", "0");
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("geo_alert");
		$rows = $q->row();
		//print_r(count($rows));exit;
		if ($q->num_rows > 0) {
                
                for ($i=0;$i<count($rows);$i++) {
                    
					$data_geofence = $rows->alert_geo_geofence;
					$dvehicle = $rows->alert_geo_vehicle;
					
					$geo_location = $this->getGeofence_location($data_geofence);
					$data_vehicle = $this->getVehicle($dvehicle);
					
					$callback["data"] = $rows;
					$callback["total"] = count($rows);
					$callback["geo_location"] = $geo_location;
					$callback["vehicle"] = $data_vehicle;
					//print_r($callback);exit;
					unset($data_update);
					$data_update["alert_geo_show"] = 1;
					$this->dbtransporter->where("alert_geo_id", $rows->alert_geo_id);
					$this->dbtransporter->update("geo_alert", $data_update);
					echo json_encode($callback);
                    return;
                    }
                    
            }
            else {
            }
	}
	
	function getGeofence_location($id) 
	{
		$nodata = "No Location";
		if ($id)
		{
			$this->db->where("geofence_id", $id);
			$this->db->limit(1);
			$q_geo = $this->db->get("geofence");
			$row_geo = $q_geo->row();
			if (count($row_geo)>0)
			{
				return $row_geo->geofence_name;
			}
			else
			{
				return $nodata;
			}
		}
		else
		{
			return $nodata;
		}
		
	}
	
	function getVehicle($id)
	{
		$novehicle = "No Vehicle";
		if ($id)
		{
			$this->db->where("vehicle_device", $id);
			$this->db->limit(1);
			$q_vehicle = $this->db->get("vehicle");
			$row_vehicle = $q_vehicle->row();
			if (count($row_vehicle)>0)
			{
				return $row_vehicle->vehicle_no;
			}
			else
			{
				return $nodata;
			}
		}
		else
		{
			return $nodata;
		}
		
	}
	
	function get_kumis_alert()
	{
	
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("alert_flag", 0);
		$this->dbtransporter->where("alert_status", 0);
		$this->dbtransporter->where("alert_view_status", 0);
		$qt = $this->dbtransporter->get("kumis_alert");
		$rt = $qt->row();
		$total = $rt->total;

		if ($total > 0)
		{
			$html = '<b><a href="'.base_url().'kumis_alert">Warning Alarm ('.$total.')</a></b>';
		}
		else
		{
			$html = '<a href="'.base_url().'kumis_alert">Warning Alarm !!</a>';
		}
						
		echo json_encode(array("total"=>$total, "notification"=>$html));
	}
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */