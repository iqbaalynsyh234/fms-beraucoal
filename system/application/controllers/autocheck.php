<?php
include "base.php";

class Autocheck extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->library('email');
	}
	
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->select("company_id,company_name");	
		$this->db->order_by("company_name", "asc");	
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $this->sess->user_id);
		$qcompany = $this->db->get("company");
		$rows_company = $qcompany->result();
		
		$this->params['rcompany'] = $rows_company;
		$this->params['sortby'] = "auto_vehicle_no";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Vehicle Autocheck List";
				
		$this->params["content"] =  $this->load->view('transporter/autocheck/autocheck_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search(){
		ini_set('display_errors', 1);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "auto_vehicle_no";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		
		$this->db->order_by($sortby, $orderby);	
		$this->db->where("auto_flag", 0);
		$this->db->where("auto_user_id", $this->sess->user_id);
		switch($field)
		{
			case "auto_vehicle_no":
				$this->db->where("auto_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "auto_vehicle_name":
				$this->db->where("auto_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "auto_vehicle_company":
				$this->db->where("auto_vehicle_company", $company);				
			break;
			case "auto_status":
				$this->db->where("auto_status", $status);				
			break;
			
		}
		$q = $this->db->get("vehicle_autocheck");
		$rows = $q->result();
		//$total = count($rows);
		
		//hitung total
		$this->db->select("count(*) as total");
		$this->db->where("auto_flag", 0);
		$this->db->where("auto_user_id", $this->sess->user_id);
		$this->db->order_by($sortby, $orderby);	
		switch($field)
		{
			case "auto_vehicle_no":
				$this->db->where("auto_vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "auto_vehicle_name":
				$this->db->where("auto_vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "auto_vehicle_company":
				$this->db->where("auto_vehicle_company", $company);				
			break;
			case "auto_status":
				$this->db->where("auto_status", $status);				
			break;
			
		}
		$qt = $this->db->get("vehicle_autocheck");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->db->select("company_id,company_name");	
		$this->db->order_by("company_name", "asc");	
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $this->sess->user_id);
		$qcompany = $this->db->get("company");
		$rows_company = $qcompany->result();
		
		$this->db->select("user_id,user_name");	
		$this->db->order_by("user_name", "asc");	
		$this->db->where("user_status", 1);
		$quser = $this->db->get("user");
		$rows_user = $quser->result();
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		$this->params['rcompany'] = $rows_company;
		$this->params['ruser'] = $rows_user;
		
		$html = $this->load->view('transporter/autocheck/autocheck_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function view_rekap()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->db->where("auto_flag", 0);
		$this->db->where("auto_user_id", $id);
		$this->db->where("auto_status", "P");
		$qp = $this->db->get("vehicle_autocheck");
		$rowp = $qp->result();
		$params['rowp'] = $rowp;
		
		$this->db->where("auto_flag", 0);
		$this->db->where("auto_user_id", $id);
		$this->db->where("auto_status", "K");
		$qk = $this->db->get("vehicle_autocheck");
		$rowk = $qk->result();
		$params['rowk'] = $rowk;
		
		$this->db->where("auto_flag", 0);
		$this->db->where("auto_user_id", $id);
		$this->db->where("auto_status", "M");
		$qm = $this->db->get("vehicle_autocheck");
		$rowm = $qm->result();
		$params['rowm'] = $rowm;
		
		$this->db->where("company_flag",0);
		$qcompany = $this->db->get("company");
		$rowcompany = $qcompany->result();
		$params['rcompany'] = $rowcompany;
		
		$callback['html'] = $this->load->view("transporter/autocheck/view_rekap", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}

		
}