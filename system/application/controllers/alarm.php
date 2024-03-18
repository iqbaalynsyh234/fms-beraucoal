<?php
include "base.php";

class Alarm extends Base {

	function Alarm()
	{
		parent::Base();	
		
		$this->load->model("gpsmodel");
	}
	
	function getcount()
	{		
		$t = mktime() - 24*3600;
		
		$_POST['period1'] = date("d/m/Y", $t);
		$_POST['hperiod1'] = date("G", $t);
		$_POST['mperiod1'] = date("i", $t);
		$_POST['speriod1'] = date("s", $t);

		$_POST['period2'] = date("d/m/Y");
		$_POST['hperiod2'] = date("G");
		$_POST['mperiod2'] = date("i");
		$_POST['speriod2'] = date("s");
		
		/* $totalgeofence = $this->search(0, true, "geofence");
		$totalparkir = $this->search(0, true, "parkir");
		$totalspeed = $this->search(0, true, "speed"); */
			
		$callback['geofencelink'] = sprintf('<a href="%salarm/index/geofence/%d/%d">%s (%d)</a>', base_url(), $t, mktime(), $this->lang->line("lgeofence_alert"), $totalgeofence);	
		$callback['parklink'] = sprintf('<a href="%salarm/index/parkir/%d/%d">%s (%d)</a>', base_url(), $t, mktime(), $this->lang->line("lpark_alert"), $totalparkir);	
		$callback['speedlink'] = sprintf('<a href="%salarm/index/speed/%d/%d">%s (%d)</a>', base_url(), $t, mktime(), $this->lang->line("lspeed_alert"), $totalspeed);	
				
		echo json_encode($callback);
	}
	
	function index($alerttype="", $t1=0, $t2=0)
	{
		if (! $this->sess->user_type)
		{
			redirect(base_url());
		}
		
		$this->params['period1'] = $t1;		
		$this->params['period2'] = $t2;
		$this->params["alerttype"] = $alerttype;
		$this->params["content"] = $this->load->view('alarm/list', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search($offset=0, $iscount=false, $myalerttype="")
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$alerttype = isset($_POST['alerttype']) ? $_POST['alerttype'] : $myalerttype;
		$geofence = isset($_POST['geofence']) ? $_POST['geofence'] : "";
		$speed = isset($_POST['speed']) ? $_POST['speed'] : "";
		$parkir = isset($_POST['parkir']) ? $_POST['parkir'] : "";
		$period1 = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$period2 = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");
		
		$hperiod1 = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$mperiod1 = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$speriod1 = isset($_POST['speriod1']) ? $_POST['speriod1'] : 0;

		$hperiod2 = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$mperiod2 = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$speriod2 = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;
		
		$d1 = sprintf("%s %02d:%02d:%02d", $period1, $hperiod1, $mperiod1, $speriod1);
		$t1 = formmaketime($d1)-7*3600;

		$d2 = sprintf("%s %02d:%02d:%02d", $period2, $hperiod2, $mperiod2, $speriod2);
		$t2 = formmaketime($d2)-7*3600;
		
		// cari vehicles
		
		$this->db->select("user_name, vehicle_no, vehicle_name, vehicle_device, geoalert_time alerttime, geofence_name data1, geoalert_direction data2, 'Geofence Alert' alerttype", false);
		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		
		switch ($field)
		{
			case "vehicle":
				$this->db->where("(vehicle_name LIKE '%".$_POST['keyword']."%' OR vehicle_no LIKE '%".$_POST['keyword']."%')");
			break;
			case "device":
				$this->db->where("vehicle_device LIKE '%".$_POST['keyword']."'");
			break;
		}
		
		switch($alerttype)
		{
			case "geofence":
				if ($geofence)
				{
					$this->db->where("geoalert_direction", $geofence);
				}
			break;
		}
		
		$this->db->where("geoalert_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("geoalert_time <=", date("Y-m-d H:i:s", $t2));
		$this->db->join("vehicle", "vehicle_device = geoalert_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->join("geofence", "geoalert_geofence = geofence_id", "left outer");
		$this->db->from("geofence_alert");
		$sqls['geofence'] = $this->db->_compile_select();
		$this->db->_reset_select();
		
		$this->db->select("user_name, vehicle_no, vehicle_name, vehicle_device, speed_alert_time alerttime, speed_alert_speed data1, speed_alert_max data2, 'Speed Alert' alerttype", false);
		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
				
		switch ($field)
		{
			case "vehicle":
				$this->db->where("(vehicle_name LIKE '%".$_POST['keyword']."%' OR vehicle_no LIKE '%".$_POST['keyword']."%')");
			break;
			case "device":
				$this->db->where("vehicle_device LIKE '%".$_POST['keyword']."'");
			break;
		}

		switch($alerttype)
		{
			case "speed":
				$this->db->where("speed_alert_max >=", $speed);
			break;
		}

		$this->db->where("speed_alert_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("speed_alert_time <=", date("Y-m-d H:i:s", $t2));	
		$this->db->join("vehicle", "vehicle_device = speed_alert_device ");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->from("speed_alert");
		$sqls['speed'] = $this->db->_compile_select();
		$this->db->_reset_select();

		$this->db->select("user_name, vehicle_no, vehicle_name, vehicle_device, parkir_alert_time alerttime, parkir_alert_length data1, parkir_alert_max data2, 'Parkir Alert' alerttype", false);
		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
				
		switch ($field)
		{
			case "vehicle":
				$this->db->where("(vehicle_name LIKE '%".$_POST['keyword']."%' OR vehicle_no LIKE '%".$_POST['keyword']."%')");
			break;
			case "device":
				$this->db->where("vehicle_device LIKE '%".$_POST['keyword']."'");
			break;
		}

		switch($alerttype)
		{
			case "parkir":
				$this->db->where("parkir_alert_max >=", $parkir);
			break;
		}

		$this->db->where("parkir_alert_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("parkir_alert_time <=", date("Y-m-d H:i:s", $t2));		
		$this->db->join("vehicle", "vehicle_device = parkir_alert_device ");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->from("parkir_alert");
		$sqls['parkir'] = $this->db->_compile_select();
		$this->db->_reset_select();		

		if (! $iscount)
		{
			if ($alerttype)		
			{
				$sql = sprintf("SELECT * FROM (%s) t1 ORDER BY alerttime ASC LIMIT %d OFFSET %d", $sqls[$alerttype], $this->config->item("limit_records"), $offset);		
			}
			else
			{
				$union = "";
				foreach($sqls as $sql)
				{
					if (strlen($union) > 0)
					{
						$union .= " UNION ";
					}
					
					$union .= $sql;
				}
				$sql = sprintf("SELECT * FROM (%s) t1 ORDER BY alerttime ASC LIMIT %d OFFSET %d", $union, $this->config->item("limit_records"), $offset);		
			}
					
			$q = $this->db->query($sql);
			
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				switch($rows[$i]->alerttype)
				{
						case "Geofence Alert":					
							if ($rows[$i]->data2 == 1)
							{
								$rows[$i]->alertdesc = $this->lang->line("lenter")." ".($rows[$i]->data1 ? $rows[$i]->data1 : "geofence");
							}
							else
							{
								$rows[$i]->alertdesc = $this->lang->line("lexit_from")." ".($rows[$i]->data1 ? $rows[$i]->data1 : "geofence");
							}
						break;
						case "Speed Alert":
							$rows[$i]->alertdesc = sprintf("%s (%d %s) >= %s (%d %s)", $this->lang->line("lspeed"), $rows[$i]->data1, $this->lang->line("lkph"), $this->lang->line("lmax_speed"), $rows[$i]->data2, $this->lang->line("lkph"));
						break;
						case "Parkir Alert":
							$rows[$i]->alertdesc = sprintf("%s (%d %s) >= %s (%d %s)", $this->lang->line("lparking_time"), $rows[$i]->data1, $this->lang->line("lminute"), $this->lang->line("lmax_parking_time"), $rows[$i]->data2, $this->lang->line("lminute"));
						break;
				}		
			}
		}
		
		if ($alerttype)		
		{
			$sql = sprintf("SELECT COUNT(*) tot FROM (%s) t1", $sqls[$alerttype]);		
		}
		else
		{
			$union = "";
			foreach($sqls as $sql)
			{
				if (strlen($union) > 0)
				{
					$union .= " UNION ";
				}
				
				$union .= $sql;
			}
			
			$sql = sprintf("SELECT COUNT(*) tot FROM (%s) t1", $union);		
		}
		
		$q = $this->db->query($sql);
		$row = $q->row();
		
		$total = $row->tot;
		
		if ($iscount) return $total;
		
		$this->params['data'] = $rows;
		$this->params['offset'] = $offset;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		

		$html = $this->load->view('alarm/result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;		
		
		echo json_encode($callback);
		
	}	
	
	function asread()
	{
		$t = mktime()-3600*7;
						
		unset($insert);
		$insert['alarm_user_id'] = $this->sess->user_id;
		$insert['alarm_gps_info_id'] = 0;
		$insert['alarm_created'] = date("Y-m-d H:i:s", $t);
		
		$mydb = $this->load->database("master", TRUE);		
		$mydb->insert("alarm", $insert);		
		
		$this->db->cache_delete_all();
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
