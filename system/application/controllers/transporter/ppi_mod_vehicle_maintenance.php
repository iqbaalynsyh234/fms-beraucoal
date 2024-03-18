<?php
include "base.php";

class Ppi_mod_vehicle_maintenance extends Base {

	function __construct()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
	}
	
	function vehicle_update()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$mobil_id = $this->input->post('mobil_id');
		$data['mobil_model'] = $this->input->post('mobil_model');
		$data['mobil_engine_capacity'] = $this->input->post('mobil_engine_capacity');
		$data['mobil_year'] = $this->input->post('mobil_year');
		$data['mobil_registration_date'] = date("Y-m-d", strtotime($this->input->post('mobil_registration_date')));
		$data['mobil_fuel_type'] = $this->input->post('mobil_fuel_type');
		$data['mobil_insurance_no'] = $this->input->post('mobil_insurance_no');
		$data['mobil_insurance_expired_date'] = date("Y-m-d", strtotime($this->input->post('mobil_insurance_expired_date')));
		$data['mobil_fuel_consumption'] = $this->input->post('mobil_fuel_consumption');
		$data['mobil_stnk_no'] = $this->input->post('mobil_stnk_no');
		$data['mobil_stnk_expired'] = date("Y-m-d", strtotime($this->input->post('mobil_stnk_expired')));
		$data['mobil_no_rangka'] = $this->input->post('mobil_no_rangka');
		$data['mobil_no_mesin'] = $this->input->post('mobil_no_mesin');
		$data['mobil_no_kir'] = $this->input->post('mobil_no_kir');
		$data['mobil_kir_active_date'] = date("Y-m-d", strtotime($this->input->post('mobil_kir_active_date')));
		$data['mobil_service_date'] = date("Y-m-d", strtotime($this->input->post('mobil_service_date')));
		$data['mobil_warranty_service_bydate'] = date("Y-m-d", strtotime($this->input->post('mobil_warranty_service_bydate')));
		$data['mobil_last_service_bykm'] = $this->input->post('mobil_last_service_bykm');
		$data['mobil_warranty_service_bykm'] = $this->input->post('mobil_warranty_service_bykm');
		$data['mobil_next_service_date'] = date("Y-m-d", strtotime($this->input->post('mobil_next_service_date')));
		$data['mobil_insurance_type'] = $this->input->post('mobil_insurance_type');
		$data['mobil_no_sipa'] = $this->input->post('mobil_no_sipa');
		$data['mobil_sipa_expired'] = date("Y-m-d", strtotime($this->input->post('mobil_sipa_expired')));
		$data['mobil_no_ibm'] = $this->input->post('mobil_no_ibm');
		$data['mobil_ibm_expired'] = date("Y-m-d", strtotime($this->input->post('mobil_ibm_expired')));
		$data['mobil_merk'] = $this->input->post('mobil_merk');
		$data['mobil_route'] = $this->input->post('mobil_route');
		
		$this->dbtransporter->where("mobil_id", $mobil_id);
		$this->dbtransporter->update("mobil",$data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Update Success";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance";
			
		echo json_encode($callback);
		return;
	}
	
	function mn_vehicle_edit()
	{
		$id = $this->uri->segment(4);
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('mobil_id',$id);
			$this->dbtransporter->where('mobil_company',$my_company);
			$this->dbtransporter->limit(1);
			$qm = $this->dbtransporter->get('mobil');
			$rm = $qm->row();
			
			$fuel_type = $this->get_fuel_type();
			$get_insurance_type = $this->get_insurance_type();
			
			//Kumis Logistics
			/* $app_route = $this->config->item("app_route");
			if (isset($app_route) && ($app_route == 1))
			{
				$get_route = $this->get_route();
				$params["my_route"] = $get_route;
			} */
		
			$params['row'] = $rm;			
			$params['fuel'] = $fuel_type;
			$params['insurance_type'] = $get_insurance_type;
			
			$html = $this->load->view("mod_vehicle_maintenance/vehicle_edit", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/mod_vehicle_maintenance");
		}
	}
	
	function get_vehicle()
	{
		$user_id = $this->sess->user_id;
		$user_company = $this->sess->user_company;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database('transporter',true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->select('mobil_device');	
		$this->dbtransporter->where('mobil_company', $my_company);
		$qmobil = $this->dbtransporter->get('mobil');
		$rowmobil = $qmobil->result();
		
		$this->db->order_by("vehicle_no", "asc");
		if (isset($rowmobil) && count($rowmobil)>0)
		{
			for ($i=0;$i<count($rowmobil);$i++)
			{
				$data[] = $rowmobil[$i]->mobil_device;  
			}
			$this->db->where_not_in("vehicle_device", $data);
		}
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $user_id);
		
		/*
 		if (isset($user_company) || isset($user_group))
		{
			if ($user_company > 0)
			{
				$this->db->or_where('vehicle_company', $user_company);
			}
			
			if ($user_group > 0)
			{
				$this->db->or_where('vehicle_group', $user_group);
			}
		} */
		
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		return $rv;
	}
	
	function get_mobil()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where('mobil_company',$my_company);
		$qmobil = $this->dbtransporter->get('mobil');
		$rmobil = $qmobil->result();
		return $rmobil;
		
	}
	
	function get_fuel_type()
	{
		$this->dbtransporter = $this->load->database('transporter',true);
		$this->dbtransporter->where('fuel_status',1);
		$qfuel = $this->dbtransporter->get('fuel_type');
		$rowfuel = $qfuel->result();
		return $rowfuel;
		
	}

	function get_workshop()
	{
		$this->dbtransporter = $this->load->database('transporter',true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where('workshop_company',$my_company);
		$qshop = $this->dbtransporter->get('workshop');
		$rowshop = $qshop->result();
		return $rowshop;
	}

	function get_driver()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$driver_company = $this->sess->user_company;
		$nodata = 0;
		
		$this->dbtransporter->order_by('driver_name', 'asc');
		$this->dbtransporter->where('driver_company', $driver_company);
		$q_driver = $this->dbtransporter->get('driver');
		$rows_driver = $q_driver->result();
		
		if (count($rows_driver)>0)
		{
			return $rows_driver;
		}
		else
		{
			return $nodata;
		}
	}

	function get_mechanic()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$nodata = 0;
		
		$this->dbtransporter->order_by('mechanic_id', 'asc');
		$this->dbtransporter->where('mechanic_company', $my_company);
		$q = $this->dbtransporter->get('mechanic');
		$rows = $q->result();
		
		if (count($rows)>0)
		{
			return $rows;
		}
		else
		{
			return $nodata;
		}
	}
	
	function get_service_type()
	{
		$this->dbtransporter = $this->load->database('transporter',true);
		$this->dbtransporter->where('service_type_status', 1);
		$qs = $this->dbtransporter->get('service_type');
		$rs = $qs->result();
		return $rs;
	}

	function get_service_model()
	{
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("service_model_status", "1");
		//$this->dbtransporter->where("service_model_company", $my_company);
		$q = $this->dbtransporter->get("service_model");
		$rows = $q->result();
		return $rows;
		
	}
	
	function get_last_odometer($v)
	{
		$device = $v;
		$this->db = $this->load->database("default",true);
		$this->db->where("vehicle_device", $device);		
		$q = $this->db->get("vehicle");
		$row = $q->row();
		
		$gtps = $this->config->item("vehicle_gtp");		
		
		if (! in_array(strtoupper($row->vehicle_type), $gtps))
		{
			return;
		}
		else
		{		
			$tables = $this->gpsmodel->getTable($row);
			$this->db = $this->load->database($tables["dbname"], TRUE);
			
			// ambil informasi di gps_info
			$this->db->order_by("gps_info_time", "DESC");
			$this->db->where("gps_info_device", $device);
			$q = $this->db->get($tables['info'], 1, 0);
			$rowinfo = $q->row();
			
			if ($q->num_rows() == 0)
			{
				return;
			}
			else
			{
				$row->totalodometer = round(($rowinfo->gps_info_distance+$row->vehicle_odometer*1000)/1000);
				return $row->totalodometer;
			}
		}
	}

	function get_inisialisasi_odometer($v)
	{
		$this->db = $this->load->database("default",true);
		$device = $v;
		$this->db->where("vehicle_device", $device);		
		$q = $this->db->get("vehicle");
		$row = $q->row();
		
		return $row->vehicle_odometer;
	}
	
	function get_insurance_type()
	{
		$this->dbtransporter = $this->load->database('transporter',true);
		$this->dbtransporter->where('insurance_type_status', 1);
		$q = $this->dbtransporter->get('insurance_type');
		$rows = $q->result();
		return $rows;
	}
	
	function delete_mobil()
	{
		$id = $this->uri->segment(4);
		$this->dbtransporter = $this->load->database('transporter',true);
		if ($id)
		{
			$this->dbtransporter->where("mobil_id",$id);
			$this->dbtransporter->delete("mobil");
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
			
		}
		else
		{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
			
		echo json_encode($callback);
	}
	
	function delete_workshop()
	{
		$id = $this->uri->segment(4);
		$this->dbtransporter = $this->load->database('transporter',true);
		if ($id)
		{
			$this->dbtransporter->where("workshop_id",$id);
			$this->dbtransporter->delete("workshop");
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
			
		}
		else
		{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
			
		echo json_encode($callback);
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
	
	function stnk_expired(){
		
		/* $app_route = $this->config->item("app_route");
		if (isset($app_route) && ($app_route == 1))
		{
			$get_route = $this->get_route();
			$this->params["my_route"] = $get_route;
		} */
		
		$this->params['sortby'] = "mobil_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Initializing Vehicle";

		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/stnk_list', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_stnk_expired()
	{
	
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "mobil_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$route = isset($_POST['route']) ? $_POST['route'] : 0;
		$end_stnk_expired = date("Y-m-d",strtotime("+14 days"));
		
		//expired
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("mobil_company", $my_company);
		$this->dbtransporter->where("mobil_stnk_expired <", date("Y-m-d"));
		switch($field)
		{
			case "mobil_no":
				$this->dbtransporter->where("mobil_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$q1 = $this->dbtransporter->get("mobil");
		$rows1 = $q1->result();
		
		//will expired ( 2 minggu )
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("mobil_company", $my_company);
		$this->dbtransporter->where("mobil_stnk_expired >=", date("Y-m-d"));
		$this->dbtransporter->where("mobil_stnk_expired <=", $end_stnk_expired);
		switch($field)
		{
			case "mobil_no":
				$this->dbtransporter->where("mobil_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$q2 = $this->dbtransporter->get("mobil");
		$rows2 = $q2->result();
		
		$rows = array_merge($rows2, $rows1);
		
		
		//total
		//expired
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_stnk_expired <", date("Y-m-d"));
		switch($field)
		{
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;
				case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$qt1 = $this->dbtransporter->get("mobil");
		$rt1 = $qt1->row();
		$total1 = $rt1->total;
		
		//will expired ( 2 minggu )
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_stnk_expired >=", date("Y-m-d"));
		$this->dbtransporter->where("mobil_stnk_expired <=", $end_stnk_expired);
		switch($field)
		{
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;
				case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$qt2 = $this->dbtransporter->get("mobil");
		$rt2 = $qt2->row();
		$total2 = $rt2->total;
		
		$total = $total1 + $total2;
		$limit = $this->config->item("limit_records");
		$this->load->library("pagination1");
		
		$app_route = $this->config->item("app_route");
		if (isset($app_route) && ($app_route == 1))
		{
			$get_route = $this->get_route();
			$this->params["my_route"] = $get_route;
		}
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		$config['num_links'] = floor($total/$limit);
		
		$this->pagination1->initialize($config);
		
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('mod_vehicle_maintenance/stnk_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function mn_stnk_edit()
	{
		$id = $this->uri->segment(4);
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('mobil_id',$id);
			$this->dbtransporter->where('mobil_company',$my_company);
			$this->dbtransporter->limit(1);
			$qm = $this->dbtransporter->get('mobil');
			$rm = $qm->row();
			
			$fuel_type = $this->get_fuel_type();
			$get_insurance_type = $this->get_insurance_type();
			
			//Kumis Logistics
			/* $app_route = $this->config->item("app_route");
			if (isset($app_route) && ($app_route == 1))
			{
				$get_route = $this->get_route();
				$params["my_route"] = $get_route;
			} */
		
			$params['row'] = $rm;			
			$params['fuel'] = $fuel_type;
			$params['insurance_type'] = $get_insurance_type;
			
			$html = $this->load->view("mod_vehicle_maintenance/stnk_edit", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/mod_vehicle_maintenance");
		}
	}
	
	function stnk_update()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$mobil_id = $this->input->post('mobil_id');
		
		$data['mobil_stnk_expired'] = date("Y-m-d", strtotime($this->input->post('mobil_stnk_expired')));
		$this->dbtransporter->where("mobil_id", $mobil_id);
		$this->dbtransporter->update("mobil", $data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Update STNK Success";
		$callback['redirect'] = base_url()."transporter/ppi_mod_vehicle_maintenance/stnk_expired/";
			
		echo json_encode($callback);
		return;
	}
	
	function kir_expired(){
		
		/* $app_route = $this->config->item("app_route");
		if (isset($app_route) && ($app_route == 1))
		{
			$get_route = $this->get_route();
			$this->params["my_route"] = $get_route;
		} */
		
		$this->params['sortby'] = "mobil_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Initializing Vehicle";

		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/kir_list', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_kir_expired()
	{
	
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "mobil_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$route = isset($_POST['route']) ? $_POST['route'] : 0;
		$end_kir_expired = date("Y-m-d",strtotime("+14 days"));
		
		//expired
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("mobil_company", $my_company);
		$this->dbtransporter->where("mobil_kir_active_date <", date("Y-m-d"));
		switch($field)
		{
			case "mobil_no":
				$this->dbtransporter->where("mobil_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$q1 = $this->dbtransporter->get("mobil");
		$rows1 = $q1->result();
		
		//will expired ( 2 minggu )
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("mobil_company", $my_company);
		$this->dbtransporter->where("mobil_kir_active_date >=", date("Y-m-d"));
		$this->dbtransporter->where("mobil_kir_active_date <=", $end_kir_expired);
		switch($field)
		{
			case "mobil_no":
				$this->dbtransporter->where("mobil_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$q2 = $this->dbtransporter->get("mobil");
		$rows2 = $q2->result();
		
		$rows = array_merge($rows2, $rows1);
		
		
		//total
		//expired
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_kir_active_date <", date("Y-m-d"));
		switch($field)
		{
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;
				case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$qt1 = $this->dbtransporter->get("mobil");
		$rt1 = $qt1->row();
		$total1 = $rt1->total;
		
		//will expired ( 2 minggu )
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_kir_active_date >=", date("Y-m-d"));
		$this->dbtransporter->where("mobil_kir_active_date <=", $end_kir_expired);
		switch($field)
		{
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);				
			break;		
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);				
			break;	
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);				
			break;
				case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);				
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);				
			break;
			case "route":
				$this->dbtransporter->where("mobil_route", $route);				
			break;
			
		}
		$qt2 = $this->dbtransporter->get("mobil");
		$rt2 = $qt2->row();
		$total2 = $rt2->total;
		
		$total = $total1 + $total2;
		$limit = $this->config->item("limit_records");
		$this->load->library("pagination1");
		
		$app_route = $this->config->item("app_route");
		if (isset($app_route) && ($app_route == 1))
		{
			$get_route = $this->get_route();
			$this->params["my_route"] = $get_route;
		}
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		$config['num_links'] = floor($total/$limit);
		
		$this->pagination1->initialize($config);
		
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('mod_vehicle_maintenance/kir_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function mn_kir_edit()
	{
		$id = $this->uri->segment(4);
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('mobil_id',$id);
			$this->dbtransporter->where('mobil_company',$my_company);
			$this->dbtransporter->limit(1);
			$qm = $this->dbtransporter->get('mobil');
			$rm = $qm->row();
			
			$fuel_type = $this->get_fuel_type();
			$get_insurance_type = $this->get_insurance_type();
			
			//Kumis Logistics
			/* $app_route = $this->config->item("app_route");
			if (isset($app_route) && ($app_route == 1))
			{
				$get_route = $this->get_route();
				$params["my_route"] = $get_route;
			} */
		
			$params['row'] = $rm;			
			$params['fuel'] = $fuel_type;
			$params['insurance_type'] = $get_insurance_type;
			
			$html = $this->load->view("mod_vehicle_maintenance/kir_edit", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/ppi_mod_vehicle_maintenance");
		}
	}
	
	function kir_update()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$mobil_id = $this->input->post('mobil_id');
		
		$data['mobil_kir_active_date'] = date("Y-m-d", strtotime($this->input->post('mobil_kir_active_date')));
		$this->dbtransporter->where("mobil_id", $mobil_id);
		$this->dbtransporter->update("mobil", $data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Update KIR Success";
		$callback['redirect'] = base_url()."transporter/ppi_mod_vehicle_maintenance/kir_expired/";
			
		echo json_encode($callback);
		return;
	}
}