<?php

class Base extends Controller {
	var $sess;
	var $params;
	var $vehicleids;

	function Base()
	{
		parent::Controller();	
		
		$this->load->library('session');
		
		if ($this->config->item('template'))
		{
			$mysess = $this->session->userdata($this->config->item("session_name"));
			$umysess = ($mysess) ? unserialize($mysess) : false;
			
			if (($umysess === false) || ($umysess->user_type != 4))
			{
				$this->load->_ci_view_path .= $this->config->item('template');
			}
		}
		
		$this->load->helper("url");
		$this->load->helper("common");
		
		$this->lang->load('error', $this->config->item('session_lang'));
		$this->lang->load('info', $this->config->item('session_lang'));
		
		$this->load->database();		
		$this->load->library('pagination');
		
		$this->load->model("vehiclemodel");
		
		$sess = $this->session->userdata($this->config->item("session_name"));
		$this->sess = ($sess) ? unserialize($sess) : false;
		
		$controller = $this->uri->segment(1);
		if ($this->sess !== false)
		{
			if ($this->sess->user_type == 4)
			{				
				if (! in_array($controller, array("invoice", "member")))
				{
					exit;
				}
				//echo "haa: ". exit;
			}
		}
		
		$skipnavigation = $this->config->item("skipnavigation");
		
		$controller = $this->uri->segment(1);
		$method = $this->uri->segment(2);
		
		if (! isset($skipnavigation[$controller][$method]))
		{		
			$this->navigation();	
		}
		if ($this->sess)
		{
			$this->loadconfigapp();					
		}
	}
	
	function loadconfigapp()
	{
		$q = $this->db->get("config");
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			$this->params[$rows[$i]->config_name] = $rows[$i]->config_value;
		}
	}
	
	function index()
	{
		$this->load->view('login');
	}
	
	function navigation()
	{
		if (! isset($this->sess->user_type))
		{
			return;
		}
		
		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->vehicleids = $this->vehiclemodel->getVehicleIds();
			}
		}

		if ($this->sess->user_type == 2)
		{
			$this->db->where("company_id", $this->sess->user_company);
		}			
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("company_agent", $this->sess->user_agent);
		}		
		$ncompany = $this->db->count_all_results("company");

		$this->params['ncompany'] = $ncompany;
				
		if ($this->sess->user_type == 1)
		{
			$this->db->order_by("agent_name", "asc");
			$q = $this->db->get("agent");

			$rows = $q->result();			
			$this->params['agents'] = $rows;
			
			if ($this->config->item('vehicle_type_fixed') == 'pln')
			{
				$this->db->where("vehicle_type", $this->config->item('vehicle_type_fixed'));
			}
			$this->db->where_in("vehicle_type", $this->config->item('vehicle_gtp'));
			$totgtp = $this->db->count_all_results("vehicle");			
		}
		else
		if ($this->sess->user_type == 2)
		{
			$this->db->order_by("vehicle_no", "asc");
			
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}
			
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			
			if ($this->config->item('vehicle_type_fixed')) 
			{
				$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
			}
			
			$q = $this->db->get("vehicle", 10);

			$rows = $q->result();			
			
			for($i=0; $i < count($rows); $i++)
			{
				$arr = explode("@", $rows[$i]->vehicle_device);
				
				$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
				$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";				
			}

			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			
			if ($this->config->item('vehicle_type_fixed')) 
			{
				$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
			}
			
			$total = $this->db->count_all_results("vehicle");
						
			
			if ($this->config->item('vehicle_type_fixed') == 'pln')
			{
				$this->db->where("vehicle_type", $this->config->item('vehicle_type_fixed'));
			}
			
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}
			
			$this->db->where_in("vehicle_type", $this->config->item('vehicle_gtp'));
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));			
			$this->db->join("user", "user_id = vehicle_user_id");
			$totgtp = $this->db->count_all_results("vehicle");
						
			$this->params['total'] = $total;
			$this->params['vehicles'] = $rows;			
		}
		else
		{
			$this->db->order_by("user_name", "asc");
			$this->db->where("user_agent", $this->sess->user_agent);
			$this->db->where("user_type", 2);
			$q = $this->db->get("user", 10);

			$rows = $q->result();			
			$this->params['users'] = $rows;
			
			$this->db->where("user_agent", $this->sess->user_agent);
			$this->db->where("user_type", 2);
			$total = $this->db->count_all_results("user");
			
			$this->params['total'] = $total;

			if ($this->config->item('vehicle_type_fixed') == 'pln')
			{
				$this->db->where("vehicle_type", $this->config->item('vehicle_type_fixed'));
			}
			$this->db->where("user_agent", $this->sess->user_agent);
			$this->db->where_in("vehicle_type", $this->config->item('vehicle_gtp'));
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			$this->db->join("user", "user_id = vehicle_user_id");
			$this->db->join("agent", "user_agent = agent_id");
			$totgtp = $this->db->count_all_results("vehicle");
			
		}
		
		$this->params['loaddialog'] = ($this->uri->segment(1) != "invoice")	|| ($this->uri->segment(2) != "bayar");
		$this->params['totalGTP'] = $totgtp;
		$this->params["globaljs"] = $this->load->view("globaljs", $this->params, true);
		$this->params["navigation"] = $this->load->view('navigation', $this->params, true);		
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
