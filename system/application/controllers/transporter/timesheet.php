<?php
include "base.php";

class Timesheet extends Base {

	function Timesheet()
	{
		parent::Base();
		if (! isset($this->sess->user_company))
		{
			redirect(base_url());
		}		
		
	}
	
	function index($field="all", $keyword="all", $offset=0)
	{
		$route = $this->get_route();
		$vehicle = $this->get_all_vehicle();
		
		$this->params['vehicle'] = $vehicle;
		$this->params['route'] = $route;
		$this->params['sortby'] = "timesheet_id";
		$this->params['orderby'] = "asc";
		$this->params["content"] = $this->load->view('timesheet/list.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$timesheet_route = isset($_POST['timesheet_route']) ? $_POST['timesheet_route'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "timesheet_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		if (!$my_company)
		{
			redirect(base_url());
		}
		
		$this->dbtransporter->order_by("timesheet_id","asc");
		
		switch($field)
		{
			case "timesheet_name":
				$this->dbtransporter->where("timesheet_geo_name LIKE '%".$keyword."%'", null);
			break;
			case "timesheet_route":
				$this->dbtransporter->where("timesheet_route", $timesheet_route);
			break;
			case "vehicle":
				$this->dbtransporter->where("timesheet_vehicle", $vehicle);
			break;
		}
		
		$this->dbtransporter->where("timesheet_status","1");
		$this->dbtransporter->where("timesheet_company", $my_company);
		$q = $this->dbtransporter->get("timesheet", 50, $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by("timesheet_id","asc");
		
		switch($field)
		{
			case "timesheet_name":
				$this->dbtransporter->where("timesheet_geo_name LIKE '%".$keyword."%'", null);
			break;
			case "timesheet_route":
				$this->dbtransporter->where("timesheet_route", $timesheet_route);
			break;
			case "vehicle":
				$this->dbtransporter->where("timesheet_vehicle", $vehicle);
			break;
		}
		
		$this->dbtransporter->where("timesheet_company", $my_company);
		$this->dbtransporter->where("timesheet_status","1");
		$qtotal = $this->dbtransporter->get("timesheet");
		$rowstotal = $qtotal->row();
		
		$total = $rowstotal->total;
		$limit = 50;
		$this->load->library("pagination1");
		
		$route = $this->get_route();
		$vehicle = $this->get_all_vehicle();
		$driver = $this->get_all_driver();
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["driver"] = $driver;
		$this->params["vehicle"] = $vehicle;
		$this->params["route"] = $route;
		$this->params["title"] = "Manage Timesheet";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("timesheet/listresult.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function add()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$my_geofence = $this->get_geofence();
		$my_route = $this->get_route();
		$timecontrol = $this->get_timecontrol();
		$my_driver = $this->get_driver();
		$my_vehicle = $this->get_vehicle();
		
		$this->params["my_vehicle"] = $my_vehicle;
		$this->params["my_driver"] = $my_driver;
		$this->params["my_geofence"] = $my_geofence;
		$this->params["my_route"] = $my_route;
		$this->params["timecontrol"] = $timecontrol;
		$this->params["title"] = "Manage Timesheet - ADD";		
		$this->params['content'] = $this->load->view("timesheet/add", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$route = $this->input->post("route");
		$timesheet = $this->input->post("timesheet");
		$timeplan = $this->input->post("timeplan");
		$timeplan_out = $this->input->post("timeplan_out");
		$vehicle = $this->input->post("vehicle");
		$driver = $this->input->post("driver");
		$cycle = $this->input->post("cycle");
		$my_company = $this->sess->user_company;
		$user_id = $this->sess->user_id;
		
		if ($route == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Route!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($timesheet == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Timesheet!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($timeplan == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Time Plan!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($timeplan_out == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Time Plan Out!";
		
			echo json_encode($callback);
			return;
		}
		
		$sheet_time = date("H:i:s",strtotime($timeplan.":00"));
		$sheet_time_out = date("H:i:s",strtotime($timeplan_out.":00"));
		
		unset($data);
		$data["timesheet_company"] = $my_company;
		$data["timesheet_route"] = $route;
		$data["timesheet_user"] = $user_id;
		$data["timesheet_geo_name"] = $timesheet;
		$data["timesheet_time"] = $sheet_time;
		$data["timesheet_time_out"] = $sheet_time_out;
		$data["timesheet_vehicle"] = $vehicle;
		$data["timesheet_driver"] = $driver;
		$data["timesheet_cycle"] = $cycle;
		
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->insert("timesheet",$data);
		
		if (isset($driver) && $driver != 0)
		{
			if (isset($vehicle) && $vehicle != 0)
			{
				$this->db->select("vehicle_id");
				$this->db->where("vehicle_device",$vehicle);
				$this->db->limit(1);
				$qv = $this->db->get("vehicle");
				$rv = $qv->row();
				
				if (count($rv)>0)
				{
					unset($data_update);
					$data_update["driver_vehicle"] = $rv->vehicle_id;
					$this->dbtransporter->where("driver_id",$driver);
					$this->dbtransporter->update("driver",$data_update);
				}
			}
		}
		
		$this->db->cache_delete_all();
		$this->dbtransporter->cache_delete_all();
		
		$callback["error"] = false;
		$callback["message"] = "Add Timesheet Success";
		$callback["redirect"] = base_url()."transporter/timesheet";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	}
	
	function edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->where("timesheet_company", $my_company);
		$this->dbtransporter->where("timesheet_id", $id);
		$q = $this->dbtransporter->get("timesheet");
		$row = $q->row();
		
		$my_geofence = $this->get_geofence();
		$my_route = $this->get_route();
		$timecontrol = $this->get_timecontrol();
		$vehicle = $this->get_all_vehicle();
		$driver = $this->get_all_driver();
		
		$params["driver"] = $driver;
		$params["vehicle"] = $vehicle;
		$params["my_geofence"] = $my_geofence;
		$params["my_route"] = $my_route;
		$params["timecontrol"] = $timecontrol;
		$params["data"] = $row;
		
		$html = $this->load->view('timesheet/edit', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function update()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$timesheet_id = isset($_POST['timesheet_id']) ? $_POST['timesheet_id'] : 0;
		$route = isset($_POST['route']) ? $_POST['route'] : 0;
		$timesheet = isset($_POST['timesheet']) ? $_POST['timesheet'] : 0;
		$timeplan = isset($_POST['timeplan']) ? $_POST['timeplan'] : 0;
		$timeplan_out = isset($_POST['timeplan_out']) ? $_POST['timeplan_out'] : 0;
		$driver_old = isset($_POST['driver_old']) ? $_POST['driver_old'] : 0;
		$vehicle_old = isset($_POST['vehicle_old']) ? $_POST['vehicle_old'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$driver = isset($_POST['driver']) ? $_POST['driver'] : 0;
		$cycle = isset($_POST['cycle']) ? $_POST['cycle'] : 0;
		
		if ($route == "" || $route == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Route!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($timesheet == "" || $timesheet == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Timesheet!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($timeplan == "" || $timeplan == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Set Timeplan!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($timeplan_out == "" || $timeplan_out == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Set Timeplan Out!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($driver != 0 && ($driver_old != $driver))
		{
			$this->dbtransporter->select("timesheet_id");
			$this->dbtransporter->where("timesheet_driver",$driver);
			$this->dbtransporter->limit(1);
			$qd = $this->dbtransporter->get("timesheet");
			$rd = $qd->row();
			if (count($rd)>0)
			{
				$callback['error'] = true;
				$callback['message'] = "Driver Masih Aktif Di Timesheet YG Lain";
				echo json_encode($callback);
				return;
			}
		}
		
		if ($vehicle != 0 && ($vehicle_old != $vehicle))
		{
			$this->dbtransporter->select("timesheet_id");
			$this->dbtransporter->where("timesheet_vehicle",$vehicle);
			$this->dbtransporter->limit(1);
			$qd = $this->dbtransporter->get("timesheet");
			$rd = $qd->row();
			if (count($rd)>0)
			{
				$callback['error'] = true;
				$callback['message'] = "Vehicle Masih Aktif Di Timesheet YG Lain";
				echo json_encode($callback);
				return;
			}
		}
		
		
		unset($data);
		$data['timesheet_route'] = $route;
		$data['timesheet_geo_name'] = $timesheet;
		$data['timesheet_time'] = $timeplan;
		$data['timesheet_time_out'] = $timeplan_out;
		$data['timesheet_vehicle'] = $vehicle;
		$data['timesheet_driver'] = $driver;
		$data['timesheet_cycle'] = $cycle;
		
		$this->dbtransporter->where('timesheet_id', $timesheet_id);
		$this->dbtransporter->update('timesheet', $data);
		
		
		//ganti driver
		if ($driver_old != 0)
		{
			if ($driver_old != $driver)
			{
				
				unset($driverold);
				$driverold["driver_vehicle"] = 0;
				$this->dbtransporter->where("driver_id",$driver);
				$this->dbtransporter->update("driver",$driverold);
				
				$this->dbtransporter->where("driver_id",$driver_old);
				$this->dbtransporter->update("driver",$driverold);
				
			}
		}
		
		//tidak memakai driver
		if ($driver == 0)
		{
			unset($driverold);
			$driverold["driver_vehicle"] = 0;
			$this->dbtransporter->where("driver_id",$driver_old);
			$this->dbtransporter->update("driver",$driverold);
		}
		
		if ($driver != 0 && $vehicle != 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_device",$vehicle);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rv = $qv->row();
				
			if (count($rv)>0)
			{
				unset($data_update);
				$data_update["driver_vehicle"] = $rv->vehicle_id;
				$this->dbtransporter->where("driver_id",$driver);
				$this->dbtransporter->update("driver",$data_update);
			}
		}
		
		$callback["error"] = false;
		$callback["message"] = "Edit Timesheet Success";
		$callback["redirect"] = base_url()."transporter/timesheet";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	
	}
	
	function delete()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("timesheet_company", $my_company);
		$this->dbtransporter->where("timesheet_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("timesheet");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('timesheet/delete', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function delete_data()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$timesheet_id = isset($_POST['timesheet_id']) ? $_POST['timesheet_id'] : 0;
		$driver = isset($_POST['timesheet_driver']) ? $_POST['timesheet_driver'] : 0;
		
		unset($data);
		$data['timesheet_status'] = 0;
		
		$this->dbtransporter->where('timesheet_id', $timesheet_id);
		$this->dbtransporter->update('timesheet', $data);
		
		if (isset($driver) && $driver != 0)
		{
			unset($driver_update);
			$driver_update["driver_vehicle"] = 0;
			$this->dbtransporter->where("driver_id",$driver);
			$this->dbtransporter->update("driver",$driver_update);
		}
		
		$this->dbtransporter->cache_delete_all();
		
		$callback["error"] = false;
		$callback["message"] = "Delete Timesheet Success";
		$callback["redirect"] = base_url()."transporter/timesheet";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	
	}
	
	function get_geofence()
	{
		$this->db->order_by("geofence_name", "asc");
		$this->db->select("geofence_name");
		$this->db->where("geofence_status", "1");
		$this->db->where("geofence_name !=", "");
		$this->db->where("geofence_user", $this->sess->user_id);
		$q = $this->db->get("geofence");
		$rows = $q->result();
		
		for ($i=0;$i<count($rows);$i++)
		{
			if (isset($rows[$i+1]->geofence_name))
			{
				if ($rows[$i]->geofence_name != $rows[$i+1]->geofence_name)
				{
					$geo[] = $rows[$i]->geofence_name;
				}
			}
			else
			{
				$geo[] = $rows[$i]->geofence_name;
			}
			
		}
		return $geo;
	}
	
	function get_route()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("route_id","asc");
		$this->dbtransporter->select("route_id,route_name");
		$this->dbtransporter->where("route_company", $my_company);
		$this->dbtransporter->where("route_status",1);
		$q = $this->dbtransporter->get("route");
		$rows = $q->result();
		return $rows;
	}
	
	function get_timecontrol()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		
		$this->dbtransporter->order_by("time_id","asc");
		$this->dbtransporter->select("time");
		$this->dbtransporter->where("time_status",1);
		$q = $this->dbtransporter->get("timecontrol");
		$rows = $q->result();
		return $rows;
	}
	
	function get_driver()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->select("timesheet_driver");
		$this->dbtransporter->where("timesheet_status",1);
		$this->dbtransporter->where("timesheet_driver <>",0);
		$this->dbtransporter->where("timesheet_company",$my_company);
		$qsheet = $this->dbtransporter->get('timesheet');
		$rowsheet = $qsheet->result();
		
		if (isset($rowsheet) && count($rowsheet)>0)
		{
			for ($i=0;$i<count($rowsheet);$i++)
			{
				$data[] = $rowsheet[$i]->timesheet_driver;  
			}
			$this->dbtransporter->where_not_in("driver_id", $data);
		}
		
		$this->dbtransporter->order_by("driver_name","asc");
		$this->dbtransporter->select("driver_id,driver_name");
		$this->dbtransporter->where("driver_company", $my_company);
		$this->dbtransporter->where("driver_vehicle", 0);
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		return $rows;
	}
	
	function get_all_driver()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("driver_name","asc");
		$this->dbtransporter->select("driver_id,driver_name");
		$this->dbtransporter->where("driver_company", $my_company);
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		return $rows;
	}
	
	function get_vehicle()
	{
		$user_id = $this->sess->user_id;
		$this->dbtransporter = $this->load->database('transporter',true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->select("timesheet_vehicle");
		$this->dbtransporter->where("timesheet_status",1);
		$this->dbtransporter->where("timesheet_vehicle <>",0);
		$this->dbtransporter->where("timesheet_company",$my_company);
		$qsheet = $this->dbtransporter->get('timesheet');
		$rowsheet = $qsheet->result();
		
		if (isset($rowsheet) && count($rowsheet)>0)
		{
			for ($i=0;$i<count($rowsheet);$i++)
			{
				$data[] = $rowsheet[$i]->timesheet_vehicle;  
			}
			$this->db->where_not_in("vehicle_device", $data);
		}
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $user_id);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get("vehicle");
		
		$rv = $qv->result();
		return $rv;
	}
	
	function get_all_vehicle()
	{
		$user_id = $this->sess->user_id;
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $user_id);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		return $rv;
	}
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */