<?php
include "base.php";

class Mod_vehicle_maintenance extends Base {

	function __construct()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
	}
	
	function index(){
		
		/* $app_route = $this->config->item("app_route");
		if (isset($app_route) && ($app_route == 1))
		{
			$get_route = $this->get_route();
			$this->params["my_route"] = $get_route;
		} */
		
		$this->params['sortby'] = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Initializing Vehicle";

		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_list', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_vehicle()
	{
	
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "mobil_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$route = isset($_POST['route']) ? $_POST['route'] : 0;
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("mobil_company", $my_company);
		
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
		
		$q = $this->dbtransporter->get("mobil", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("mobil_company", $my_company);
		
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
		
		$qt = $this->dbtransporter->get("mobil");
		$rt = $qt->row();
		$total = $rt->total;
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

		$html = $this->load->view('mod_vehicle_maintenance/vehicle_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function add_vehicle()
	{
		$get_vehicle = $this->get_vehicle();
		$get_fuel_type = $this->get_fuel_type();
		$get_insurance_type = $this->get_insurance_type();
		
		//Kumis Logistics
		/* $app_route = $this->config->item("app_route");
		if (isset($app_route) && ($app_route == 1))
		{
			$get_route = $this->get_route();
			$this->params["my_route"] = $get_route;
		} */
		
		$this->params["vehicle"] = $get_vehicle;
		$this->params["fuel_type"] = $get_fuel_type;
		$this->params["insurance_type"] = $get_insurance_type;
		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_add', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function save()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$mobil_device = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		if ($mobil_device == 0 || $mobil_device == "")
		{
			$callback['error'] = true;	
			$callback['message'] = "Please Select Vehicle !";
			
			echo json_encode($callback);			
			return;
		}
		$data['mobil_device'] = $mobil_device;
		
		$route = isset($_POST["route"]) ? $_POST["route"] : 0;
		$data['mobil_route'] = $route;
		
		//Select vehicle di DB Master
		$this->db->where("vehicle_device", $mobil_device);
		$this->db->limit(1);
		$qv = $this->db->get('vehicle');
		$rowv = $qv->row();
		
		if (count($rowv) > 0)
		{
			$mobil_name = $rowv->vehicle_name;
			$mobil_no = $rowv->vehicle_no;
		}
		else
		{
			$callback['error'] = true;	
			$callback['message'] = "Can't Select Vehicle !";
			
			echo json_encode($callback);			
			return;
		}
		
		$data['mobil_name'] = $mobil_name;
		$data['mobil_no'] = $mobil_no;
		
		$mobil_company = $my_company;
		$data['mobil_company'] = $mobil_company;
		
		$mobil_model = isset($_POST['mobil_model']) ? $_POST['mobil_model'] : "";
		$data['mobil_model'] = $mobil_model;
		
		$mobil_merk = isset($_POST['mobil_merk']) ? $_POST['mobil_merk'] : "";
		$data['mobil_merk'] = $mobil_merk;
		
		$mobil_engine_capacity = isset($_POST['mobil_engine_capacity']) ? $_POST['mobil_engine_capacity'] : "";
		$data['mobil_engine_capacity'] = $mobil_engine_capacity;
		
		$mobil_year = isset($_POST['mobil_year']) ? $_POST['mobil_year'] : "";
		$data['mobil_year'] = $mobil_year;
		
		$registration_date = isset($_POST['registration_date']) ? $_POST['registration_date'] : "";
		if ($registration_date != "")
		{
			$mobil_registration_date = date("Y-m-d", strtotime($registration_date));
		}
		else
		{
			$mobil_registration_date = $registration_date;
		}
		$data['mobil_registration_date'] = $mobil_registration_date;
		
		$mobil_fuel_type = isset($_POST['mobil_fuel_type']) ? $_POST['mobil_fuel_type'] : 0;
		$data['mobil_fuel_type'] = $mobil_fuel_type ;
		
		$mobil_insurance_no = isset($_POST['mobil_insurance_no']) ? $_POST['mobil_insurance_no'] : "";
		$data['mobil_insurance_no'] = $mobil_insurance_no;
		
		$insurance_expired_date = isset($_POST['mobil_insurance_expired_date']) ? $_POST['mobil_insurance_expired_date'] : "";
		if ($insurance_expired_date != "")
		{
			$mobil_insurance_expired_date = date("Y-m-d",strtotime($insurance_expired_date));
		}
		else
		{
			$mobil_insurance_expired_date = $insurance_expired_date;
		}
		$data['mobil_insurance_expired_date'] = $mobil_insurance_expired_date;
		
		$mobil_fuel_consumption = isset($_POST['mobil_fuel_consumption']) ? $_POST['mobil_fuel_consumption'] : "";
		$data['mobil_fuel_consumption'] = $mobil_fuel_consumption;
		
		$mobil_no_kir = isset($_POST['mobil_no_kir']) ? $_POST['mobil_no_kir'] : "";
		$data['mobil_no_kir'] = $mobil_no_kir;
		
		$kir_active_date = isset($_POST['mobil_kir_active_date']) ? $_POST['mobil_kir_active_date'] : "";
		if ($kir_active_date != "")
		{
			$mobil_kir_active_date = date("Y-m-d",strtotime($kir_active_date));
		}
		else
		{
			$mobil_kir_active_date = $kir_active_date;
		}
		$data['mobil_kir_active_date'] = $mobil_kir_active_date;
		
		$service_date = isset($_POST['mobil_service_date']) ? $_POST['mobil_service_date'] : "";
		if ($service_date != "")
		{
			$mobil_service_date = date("Y-m-d",strtotime($service_date));
		}
		else
		{
			$mobil_service_date = $service_date;
		}
		$data['mobil_service_date'] = $mobil_service_date;
		
		$warranty_service_bydate = isset($_POST['mobil_warranty_service_bydate']) ? $_POST['mobil_warranty_service_bydate'] : "";
		if ($warranty_service_bydate != "")
		{
			$mobil_warranty_service_bydate = date("Y-m-d",strtotime($warranty_service_bydate));
		}
		else
		{
			$mobil_warranty_service_bydate = $warranty_service_bydate;
		}
		$data['mobil_warranty_service_bydate'] = $mobil_warranty_service_bydate;
		
		$mobil_warranty_service_bykm = isset($_POST['mobil_warranty_service_bykm']) ? $_POST['mobil_warranty_service_bykm'] : "";
		$data['mobil_warranty_service_bykm'] = $mobil_warranty_service_bykm;
		
		$mobil_last_service_bykm = isset($_POST['mobil_last_service_bykm']) ? $_POST['mobil_last_service_bykm'] : "";
		$data['mobil_last_service_bykm'] = $mobil_last_service_bykm;
		
		$next_service_date = isset($_POST['mobil_next_service_date']) ? $_POST['mobil_next_service_date'] : "";
		if ($next_service_date != "")
		{
			$mobil_next_service_date = date("Y-m-d",strtotime($next_service_date));
		}
		else
		{
			$mobil_next_service_date = $next_service_date;
		}
		$data['mobil_next_service_date'] = $mobil_next_service_date;
		
		$mobil_alert_service = isset($_POST['mobil_alert_service']) ? $_POST['mobil_alert_service'] : 0;
		$data['mobil_alert_service'] = $mobil_alert_service;
		
		$mobil_note = isset($_POST['note']) ? $_POST['note'] : "";
		$data['mobil_note'] = $mobil_note;
		
		$mobil_stnk_no = isset($_POST['mobil_stnk_no']) ? $_POST['mobil_stnk_no'] : "";
		$data['mobil_stnk_no'] = $mobil_stnk_no;
		
		$stnk_expired = isset($_POST['mobil_stnk_expired']) ? $_POST['mobil_stnk_expired'] : "";
		if ($stnk_expired != "")
		{
			$mobil_stnk_expired =  date("Y-m-d",strtotime($stnk_expired));
		}
		else
		{
			$mobil_stnk_expired = $stnk_expired;
		}
		$data['mobil_stnk_expired'] = $mobil_stnk_expired;
		
		$mobil_no_rangka = isset($_POST['mobil_no_rangka']) ? $_POST['mobil_no_rangka'] : "";
		$data['mobil_no_rangka'] = $mobil_no_rangka;
		
		$mobil_no_mesin = isset($_POST['mobil_no_mesin']) ? $_POST['mobil_no_mesin'] : "";
		$data['mobil_no_mesin'] = $mobil_no_mesin;
		
		$mobil_insurance_type = isset($_POST['mobil_insurance_type']) ? $_POST['mobil_insurance_type'] : "";
		$data['mobil_insurance_type'] = $mobil_insurance_type;
		
		$mobil_no_sipa = isset($_POST['mobil_no_sipa']) ? $_POST['mobil_no_sipa'] : "";
		$data['mobil_no_sipa'] = $mobil_no_sipa;
		
		
		$sipa_expired = isset($_POST['mobil_sipa_expired']) ? $_POST['mobil_sipa_expired'] : "";
		if ($sipa_expired != "")
		{
			$mobil_sipa_expired = date("Y-m-d",strtotime($sipa_expired));
		}
		else
		{
			$mobil_sipa_expired = $sipa_expired;
		}
		$data['mobil_sipa_expired'] = $mobil_sipa_expired;
		
		
		$mobil_no_ibm = isset($_POST['mobil_no_ibm']) ? $_POST['mobil_no_ibm'] : "";
		$data['mobil_no_ibm'] = $mobil_no_ibm;
		
		$ibm_expired = isset($_POST['mobil_ibm_expired']) ? $_POST['mobil_ibm_expired'] : "";
		if ($ibm_expired != "")
		{
			$mobil_ibm_expired = date("Y-m-d",strtotime($ibm_expired));
		}
		else
		{
			$mobil_ibm_expired = $ibm_expired;
		}
		$data['mobil_ibm_expired'] = $mobil_ibm_expired;
		
		
		
		//Insert to Database
		$this->dbtransporter->insert('mobil',$data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Initializing Vehicle Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance";
			
		echo json_encode($callback);
		return;
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
	
	function mn_mechanic_edit()
	{
		$id = $this->uri->segment(4);
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('mechanic_id',$id);
			$this->dbtransporter->where('mechanic_company',$my_company);
			$this->dbtransporter->limit(1);
			$qm = $this->dbtransporter->get('mechanic');
			$rm = $qm->row();
			
			$params['row'] = $rm;			
			
			$html = $this->load->view("mod_vehicle_maintenance/vehicle_mechanic_edit", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/mod_vehicle_maintenance/mechanic");
		}
	}
	
	function mn_workshop_edit()
	{
		$id = $this->uri->segment(4);
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('workshop_id',$id);
			$this->dbtransporter->where('workshop_company',$my_company);
			$this->dbtransporter->limit(1);
			$qm = $this->dbtransporter->get('workshop');
			$rm = $qm->row();
			
			$params['data'] = $rm;			
			
			$html = $this->load->view("mod_vehicle_maintenance/vehicle_workshop_edit", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/mod_vehicle_maintenance/workshop");
		}
	}
	
	function mn_service_edit()
	{
		$id = $this->uri->segment(4);
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('service_id',$id);
			$this->dbtransporter->where('service_company',$my_company);
			$this->dbtransporter->limit(1);
			$qm = $this->dbtransporter->get('service');
			$rm = $qm->row();
			
			$vehicle = $this->get_mobil();
			$workshop = $this->get_workshop();
			$driver = $this->get_driver();
			//$service_type = $this->get_service_type();
			$mechanic = $this->get_mechanic();
			$service_model = $this->get_service_model();
			
			$params["vehicle"] = $vehicle;
			$params["workshop"] = $workshop;
			//$params["service_type"] = $service_type;
			$params["service_model"] = $service_model;
			$params["driver"] = $driver;
			$params["mechanic"] = $mechanic;
			$params['data'] = $rm;			
			
			$html = $this->load->view("mod_vehicle_maintenance/vehicle_service_edit", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/mod_vehicle_maintenance/service");
		}
	}
	
	function service_model_detail()
	{
		$id = $this->uri->segment(4);
		
		if (isset($id))
		{
			$this->dbtransporter = $this->load->database('transporter',true);
			$my_company = $this->sess->user_company;
			
			$this->dbtransporter->where('service_model_id',$id);
			//$this->dbtransporter->where('service_model_company',$my_company);
			$this->dbtransporter->limit(1);
			$q = $this->dbtransporter->get("service_model");
			$row = $q->row();
			
			$params['data'] = $row;			
			
			$html = $this->load->view("mod_vehicle_maintenance/vehicle_service_model", $params, true);		
		
			$callback['html'] = $html;
			$callback['error'] = false;
		
			$this->dbtransporter->cache_delete_all();
			$this->dbtransporter->close();
			
			echo json_encode($callback);
		}
		else
		{
			redirect(base_url() . "transporter/mod_vehicle_maintenance/service");
		}
	}
	
	function workshop()
	{
		$this->params['sortby'] = "workshop_id";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Manage Workshop";

		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_workshop_list', $this->params, true);
		$this->load->view("templatesess", $this->params);	
	}
	
	function mechanic()
	{
		$this->params['sortby'] = "mechanic_id";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Manage Mechanic";

		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_mechanic_list', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function add_workshop()
	{
		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_add_workshop', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function add_mechanic()
	{
		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_add_mechanic', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function service()
	{
		$vehicle = $this->get_mobil();
		$workshop = $this->get_workshop();
		$driver = $this->get_driver();
		//$service_type = $this->get_service_type();
		$service_model = $this->get_service_model();
		
		$this->params["workshop"] = $workshop;
		//$this->params["service_type"] = $service_type;
		$this->params["driver"] = $driver;
		$this->params["service_model"] = $service_model;
		$this->params["vehicle"] = $vehicle;
		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_service_list', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function add_service()
	{
		$vehicle = $this->get_mobil();
		$workshop = $this->get_workshop();
		$driver = $this->get_driver();
		//$service_type = $this->get_service_type();
		$mechanic = $this->get_mechanic();
		$service_model = $this->get_service_model();
		
		$this->params["service_model"] = $service_model;
		$this->params["vehicle"] = $vehicle;
		$this->params["workshop"] = $workshop;
		//$this->params["service_type"] = $service_type;
		$this->params["driver"] = $driver;
		$this->params["mechanic"] = $mechanic;
		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_add_service', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function save_mechanic()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$mechanic_name = isset($_POST['mechanic_name']) ? $_POST['mechanic_name'] : "";
		$mechanic_phone = isset($_POST['mechanic_phone']) ? $_POST['mechanic_phone'] : "";
		$mechanic_mobile = isset($_POST['mechanic_mobile']) ? $_POST['mechanic_mobile'] : "";
		$mechanic_fax = isset($_POST['mechanic_fax']) ? $_POST['mechanic_fax'] : "";
		$mechanic_address = isset($_POST['mechanic_address']) ? $_POST['mechanic_address'] : 0;
		$mechanic_company = $my_company;
		
		$data['mechanic_name'] = $mechanic_name;
		$data['mechanic_phone'] = $mechanic_phone;
		$data['mechanic_mobile'] = $mechanic_mobile;
		$data['mechanic_fax'] = $mechanic_fax;
		$data['mechanic_address'] = $mechanic_address;
		$data['mechanic_company'] = $mechanic_company;
		
		//Insert
		$this->dbtransporter->insert('mechanic',$data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Mechanic Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance/mechanic";
			
		echo json_encode($callback);
		return;
	}
	
	function save_workshop()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$workshop_name = isset($_POST['workshop_name']) ? $_POST['workshop_name'] : "";
		$workshop_telp = isset($_POST['workshop_telp']) ? $_POST['workshop_telp'] : "";
		$workshop_fax = isset($_POST['workshop_fax']) ? $_POST['workshop_fax'] : "";
		$workshop_address = isset($_POST['workshop_address']) ? $_POST['workshop_address'] : "";
		$workshop_company = $my_company;
		
		$data['workshop_name'] = $workshop_name;
		$data['workshop_telp'] = $workshop_telp;
		$data['workshop_fax'] = $workshop_fax;
		$data['workshop_address'] = $workshop_address;
		$data['workshop_company'] = $workshop_company;
		
		//Insert
		$this->dbtransporter->insert('workshop',$data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Workshop Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance/workshop";
			
		echo json_encode($callback);
		return;
	}
	
	function save_service()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$service_mobil = isset($_POST['service_mobil']) ? $_POST['service_mobil'] : 0;
		$service_driver = isset($_POST['service_driver']) ? $_POST['service_driver'] : 0;
		$service_workshop = isset($_POST['service_workshop']) ? $_POST['service_workshop'] : 0;
		$service_mechanic = isset($_POST['service_mechanic']) ? $_POST['service_mechanic'] : 0;
		$svc_date = isset($_POST['service_date']) ? $_POST['service_date'] : "";
		$service_type = isset($_POST['service_type']) ? $_POST['service_type'] : 0;
		$service_invoice = isset($_POST['service_invoice']) ? $_POST['service_invoice'] : "";
		$service_cost = isset($_POST['service_cost']) ? $_POST['service_cost'] : "";
		$service_company = $my_company;
		$service_note = isset($_POST['service_note']) ? $_POST['service_note'] : "";
		
		if ($service_mobil == 0)
		{
			$callback['error'] = true;	
			$callback['message'] = "Please Select Vehicle !";
			
			echo json_encode($callback);			
			return;
		}
		
		if ($service_driver == 0)
		{
			$callback['error'] = true;	
			$callback['message'] = "Please Select Driver !";
			
			echo json_encode($callback);			
			return;
		}
		
		if ($service_type == 0)
		{
			$callback['error'] = true;	
			$callback['message'] = "Please Select Service Type !";
			
			echo json_encode($callback);			
			return;
		}
		
		if ($svc_date != "")
		{
			$service_date = date("Y-m-d", strtotime($svc_date));
		}
		else
		{
			$service_date = $svc_date;
		}
		
		$data['service_mobil'] = $service_mobil;
		$data['service_driver'] = $service_driver;
		$data['service_workshop'] = $service_workshop;
		$data['service_mechanic'] = $service_mechanic;
		$data['service_date'] = $service_date;
		$data['service_type'] = $service_type;
		$data['service_invoice'] = $service_invoice;
		$data['service_cost'] = $service_cost;
		$data['service_company'] = $service_company;
		$data['service_note'] = $service_note;
		
		//Insert
		$this->dbtransporter->insert('service',$data);
		$this->dbtransporter->close();
		
		$callback['error'] = false;
		$callback['message'] = "Add Service Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance/service";
			
		echo json_encode($callback);
		return;
	}
	
	function mechanic_update()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$mechanic_id = isset($_POST['mechanic_id']) ? $_POST['mechanic_id'] : 0;
		
		$data['mechanic_name'] = isset($_POST['mechanic_name']) ? $_POST['mechanic_name'] : "";
		$data['mechanic_phone'] = isset($_POST['mechanic_phone']) ? $_POST['mechanic_phone'] : 0;
		$data['mechanic_mobile'] = isset($_POST['mechanic_mobile']) ? $_POST['mechanic_mobile'] : 0;
		$data['mechanic_fax'] = isset($_POST['mechanic_fax']) ? $_POST['mechanic_fax'] : 0;
		$data['mechanic_address'] = isset($_POST['mechanic_address']) ? $_POST['mechanic_address'] : "";
		$data['mechanic_company'] = $my_company;
		
		$this->dbtransporter->where('mechanic_id', $mechanic_id);
		$this->dbtransporter->update('mechanic',$data);
		
		$callback['error'] = false;
		$callback['message'] = "Update Mechanic Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance/mechanic";
			
		echo json_encode($callback);
		return;
		
	}
	
	function workshop_update()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		unset($data);
		
		$workshop_id = isset($_POST['workshop_id']) ? $_POST['workshop_id'] : 0;
		$workshop_name = isset($_POST['workshop_name']) ? $_POST['workshop_name'] : "";
		$workshop_telp = isset($_POST['workshop_telp']) ? $_POST['workshop_telp'] : "";
		$workshop_fax = isset($_POST['workshop_fax']) ? $_POST['workshop_fax'] : "";
		$workshop_address = isset($_POST['workshop_address']) ? $_POST['workshop_address'] : "";
		
		$data['workshop_name'] = $workshop_name;
		$data['workshop_telp'] = $workshop_telp;
		$data['workshop_fax'] = $workshop_fax;
		$data['workshop_address'] = $workshop_address;
		
		$this->dbtransporter->where('workshop_id', $workshop_id);
		$this->dbtransporter->update('workshop',$data);
		
		$callback['error'] = false;
		$callback['message'] = "Update Workshop Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance/workshop";
			
		echo json_encode($callback);
		return;
		
	}
	
	function service_update()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		
		$service_id = isset($_POST['service_id']) ? $_POST['service_id'] : 0;
		
		$data['service_mobil'] = isset($_POST['service_mobil']) ? $_POST['service_mobil'] : "";
		$data['service_driver'] = isset($_POST['service_driver']) ? $_POST['service_driver'] : 0;
		$data['service_workshop'] = isset($_POST['service_workshop']) ? $_POST['service_workshop'] : 0;
		$data['service_mechanic'] = isset($_POST['service_mechanic']) ? $_POST['service_mechanic'] : 0;
		$service_date = isset($_POST['service_date']) ? $_POST['service_date'] : "";
		$data['service_date'] = date("Y-m-d", strtotime($service_date));
		$data['service_invoice'] = isset($_POST['service_invoice']) ? $_POST['service_invoice'] : "";
		$data['service_cost'] = isset($_POST['service_cost']) ? $_POST['service_cost'] : "";
		$data['service_note'] = isset($_POST['service_note']) ? $_POST['service_note'] : "";
		
		$this->dbtransporter->where('service_id', $service_id);
		$this->dbtransporter->update('service',$data);
		
		$callback['error'] = false;
		$callback['message'] = "Update Service Successfully Submitted";
		$callback['redirect'] = base_url()."transporter/mod_vehicle_maintenance/service";
			
		echo json_encode($callback);
		return;
		
	}
	
	function get_service_alert()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where('mobil_alert_service',1);
		$this->dbtransporter->where('mobil_company',$my_company);
		$q = $this->dbtransporter->get('mobil');
		$rows = $q->result();
		//print_r($rows);exit;
		
		if (count($rows)>0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$lastodo = $this->get_last_odometer($rows[$i]->mobil_device);
				$last_service_odo = $rows[$i]->mobil_last_service_bykm;
				$warranty_service_odo = $rows[$i]->mobil_warranty_service_bykm;
				$total = $last_service_odo + $warranty_service_odo;
				$tglskrg = date("Y-m-d");
				
				if (isset($lastodo) && ($lastodo != "" || $lastodo != 0))
				{
					if($total > $lastodo)
					{
						//cek sudah ada alert vehicle blm
						$this->dbtransporter->where("service_alert_vehicle", $rows[$i]->mobil_device);
						$this->dbtransporter->where("service_alert_vehicle_date", $tglskrg);
						$this->dbtransporter->limit(1);
						$qnow = $this->dbtransporter->get("service_alert");
						$rownow = $qnow->row();
						if (count($rownow) == 0)
						{
							unset($data);
							$data_date = date("Y-m-d H:i:s");
							$data["service_alert_vehicle"] = $rows[$i]->mobil_device;
							$data["service_alert_vehicle_company"] = $this->sess->user_company;
							$data["service_alert_vehicle_create"] = $data_date;
							$data["service_alert_vehicle_date"] = $tglskrg;
							$this->dbtransporter->insert("service_alert", $data);
						}
						else
						{}
					}
				}
			}
		}
			
	}
	
	function get_service_alert_show()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("service_alert_vehicle_company", $my_company);
		$this->dbtransporter->where("service_alert_vehicle_view", "0");
		$q = $this->dbtransporter->get("service_alert");
		$row = $q->result();
		$total_alert = count($row);
		
		if($total_alert > 0)
		{
			$callback['color'] = "red";
		}
		else
		{
			$callback['color'] = "black";
		}
		$callback['total'] = "Service Alert ("." ". $total_alert . " "." )";
		echo json_encode($callback);
	}
	
	function showalert_service()
	{
		$vehicle = $this->get_mobil();
		
		//Update status Alert View
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->where("service_alert_vehicle_company", $my_company);
		$this->dbtransporter->where("service_alert_vehicle_view", "0");
		$q = $this->dbtransporter->get("service_alert");
		$rows = $q->result();
		
		if (count($rows)>0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				unset($data);
				$data["service_alert_vehicle_view"] = 1;
				$this->dbtransporter->where("service_alert_id", $rows[$i]->service_alert_id);
				$this->dbtransporter->update("service_alert", $data);
			}
		}
		
		$this->params['sortby'] = "service_alert_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Service Alert";
		$this->params["vehicle"] = $vehicle;
		$this->params["content"] =  $this->load->view('mod_vehicle_maintenance/vehicle_service_alert_list', $this->params, true);
		$this->load->view("templatesess", $this->params);	
	}
	
	//delete service
	function service_delete(){
		
		//$id = $this->uri->segment(4);
		$id = $this->input->post('id');
		
		
		$this->dbtransporter = $this->load->database('transporter',true);
		
		if ($id)
		{
			unset($data);
			//$data['cost_status'] = 2;
			$this->dbtransporter->where("service_alert_id", $id);
			$this->dbtransporter->delete("service_alert");
			
			//$this->dbtransporter->where("cost_id",$id);
			//$this->dbtransporter->set("cost_status", 2);
			//$this->dbtransporter->update("cost");
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
	//end
	
	function search_alert_service()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$bymobil = isset($_POST['bymobil']) ? $_POST['bymobil'] : 0;
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "service_alert_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter->order_by("service_alert_id", "desc");	
		$this->dbtransporter->where("service_alert_vehicle_company", $my_company);
		
		switch($field)
		{
			case "service_alert_vehicle":
				$this->dbtransporter->where("service_alert_vehicle LIKE '%".$keyword."%'", null);				
			break;		
			case "bymobil":
				$this->dbtransporter->where("service_alert_vehicle", $bymobil);				
			break;
			
		}
		
		$q = $this->dbtransporter->get("service_alert");
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "service_alert_vehicle":
				$this->dbtransporter->where("service_alert_vehicle LIKE '%".$keyword."%'", null);				
			break;		
			case "bymobil":
				$this->dbtransporter->where("service_alert_vehicle", $bymobil);				
			break;
		}
		
		$qt = $this->dbtransporter->get("service_alert");
		$rt = $qt->row();
		$total = $rt->total;
		
		$mobil = $this->get_mobil();
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["mobil"] = $mobil;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('mod_vehicle_maintenance/vehicle_service_alert_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function search_workshop()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "workshop_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";		
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("workshop_company", $my_company);
		$this->dbtransporter->where("workshop_status", "1");
		
		switch($field)
		{
			case "workshop_name":
				$this->dbtransporter->where("workshop_name LIKE '%".$keyword."%'", null);				
			break;			
			
		}
		
		$q = $this->dbtransporter->get("workshop", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("workshop_company", $my_company);
		$this->dbtransporter->where("workshop_status", "1");
		
		switch($field)
		{
			case "workshop_name":
				$this->dbtransporter->where("workshop_name LIKE '%".$keyword."%'", null);				
			break;			
			
		}
		
		$qt = $this->dbtransporter->get("workshop");
		$rt = $qt->row();
		$total = $rt->total;
			
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('mod_vehicle_maintenance/vehicle_workshop_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function search_mechanic()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "mechanic_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";		
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("mechanic_company", $my_company);
		
		switch($field)
		{
			case "mechanic_name":
				$this->dbtransporter->where("mechanic_name LIKE '%".$keyword."%'", null);				
			break;			
			
		}
		
		$q = $this->dbtransporter->get("mechanic", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		
		switch($field)
		{
			case "mechanic_name":
				$this->dbtransporter->where("mechanic_name LIKE '%".$keyword."%'", null);				
			break;			
			
		}
		
		$qt = $this->dbtransporter->get("mechanic");
		$rt = $qt->row();
		$total = $rt->total;
			
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('mod_vehicle_maintenance/vehicle_mechanic_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function search_service()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$bymobil = isset($_POST['bymobil']) ? $_POST['bymobil'] : 0;
		$bydriver = isset($_POST['bydriver']) ? $_POST['bydriver'] : 0;
		$byworkshop = isset($_POST['byworkshop']) ? $_POST['byworkshop'] : 0;
		$byservicetype = isset($_POST['byservicetype']) ? $_POST['byservicetype'] : 0;
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "service_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";		
		
	
		$this->dbtransporter->where("service_company", $my_company);
		
		switch($field)
		{
			case "service_invoice":
				$this->dbtransporter->where("service_invoice LIKE '%".$keyword."%'", null);				
			break;
			case "bymobil":
				$this->dbtransporter->where("service_mobil", $bymobil);				
			break;
			case "bydriver":
				$this->dbtransporter->where("service_driver", $bydriver);				
			break;
			case "byworkshop":
				$this->dbtransporter->where("service_workshop", $byworkshop);				
			break;
			case "byservicetype":
				$this->dbtransporter->where("service_type", $byservicetype);				
			break;
		}
		
		$q = $this->dbtransporter->get("service", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "service_invoice":
				$this->dbtransporter->where("service_invoice LIKE '%".$keyword."%'", null);				
			break;
			case "bymobil":
				$this->dbtransporter->where("service_mobil", $bymobil);				
			break;
			case "bydriver":
				$this->dbtransporter->where("service_driver", $bydriver);				
			break;
			case "byworkshop":
				$this->dbtransporter->where("service_workshop", $byworkshop);				
			break;
			case "byservicetype":
				$this->dbtransporter->where("service_type", $byservicetype);				
			break;
		}
		
		$qt = $this->dbtransporter->get("service");
		$rt = $qt->row();
		$total = $rt->total;
			
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$mobil = $this->get_mobil();
		$driver = $this->get_driver();
		$workshop = $this->get_workshop();
		//$service_type = $this->get_service_type();
		$mechanic = $this->get_mechanic();
		$service_model = $this->get_service_model();
		
		$this->params["mobil"] = $mobil;
		$this->params["driver"] = $driver;
		$this->params["workshop"] = $workshop;
		$this->params["mechanic"] = $mechanic;
		//$this->params["service_type"] = $service_type;
		$this->params["service_model"] = $service_model;
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('mod_vehicle_maintenance/vehicle_service_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function get_detail()
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
			
			$lastodo = $this->get_last_odometer($rm->mobil_device);
			$inisialodo = $this->get_inisialisasi_odometer($rm->mobil_device);
			
			$app_route = $this->config->item("app_route");
			if (isset($app_route) && ($app_route == 1))
			{	
				$get_route = $this->get_route();
				$params["my_route"] = $get_route;
			}
			
			$params['lastodo'] = $lastodo;
			$params['inisalodo'] = $inisialodo;
			$params['row'] = $rm;			
			$params['fuel'] = $fuel_type;
			
			$html = $this->load->view("mod_vehicle_maintenance/vehicle_detail", $params, true);		
		
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
	
	function delete_driver($id)
	{
		
		$data["driver_flag"] = 1;		
		$this->DB2->where("driver_id", $id);
		if($this->DB2->update("tbl_driver", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		
		
		echo json_encode($callback);
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
        
        
	function delete_mechanic()
	{
                $id = $this->uri->segment(4);
		$this->dbtransporter = $this->load->database('transporter',true);
		$data["mechanic_status"] = 1;		
		$this->dbtransporter->where("mechanic_id", $id);
                $this->dbtransporter->delete("mechanic");		
                $callback['message'] = "Data has been deleted";
                $callback['error'] = false;	
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
	
	function delete_service()
	{
        $id = $this->uri->segment(4);
		$this->dbtransporter = $this->load->database('transporter',true);		
		$this->dbtransporter->where("service_id", $id);
		$this->dbtransporter->delete("service");		
		$callback['message'] = "Data has been deleted";
		$callback['error'] = false;	
		echo json_encode($callback);
	}
	
}