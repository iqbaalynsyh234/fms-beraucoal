<?php
include "base.php";
class Kumis_alert extends Base {

	function __construct()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}
	
	function index(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		unset($data);
		$data['alert_view_status'] = 1;
		$this->dbtransporter->update("kumis_alert", $data);

		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		$this->params["row_vehicle"] = $row_vehicle;
		
		$this->params['sortby'] = "alert_is";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Warning Alarm List";
				
		$this->params["content"] =  $this->load->view('kumis/alert/alert_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "alert_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$viewstatus = isset($_POST['viewstatus']) ? $_POST['viewstatus'] : "";
		
		//search by date
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		$this->params["row_vehicle"] = $row_vehicle;
		
		$this->dbtransporter->order_by("alert_id", "desc");
		$this->dbtransporter->where("alert_flag", 0);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("alert_datetime >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("alert_datetime <=", $enddate);
					}
			}
		$q = $this->dbtransporter->get("kumis_alert", $this->config->item("limit_records"), $offset);
		$rows = $q->result();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by("alert_id", "desc");
		$this->dbtransporter->where("alert_flag", 0);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("alert_datetime >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("alert_datetime <=", $enddate);
					}
			}
		$qt = $this->dbtransporter->get("kumis_alert");
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
	
		$html = $this->load->view('kumis/alert/alert_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function get_vehicle(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_user_id", $this->sess->user_id );
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$row_vehicle = $q->result();
        
		$data_mobil = $row_vehicle;
        $this->db->cache_delete_all();
        return $data_mobil;
	}
	
}
