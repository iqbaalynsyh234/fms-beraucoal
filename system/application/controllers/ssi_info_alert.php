<?php
include "base.php";

class Ssi_info_alert extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->library('email');
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}

	function index(){
		
		if (!$this->sess->user_company){
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		//Get group
		$row_group = $this->get_group();
		$this->params['group'] = $row_group;
		
		$this->params['sortby'] = "info_alert_group_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Info Alert List";
		
		$this->params["content"] =  $this->load->view('transporter/info_alert/info_alert_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
		
	}
	
	function search_info_alert(){
		
		if (!$this->sess->user_company){
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "info_alert_group_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";		
		$group = isset($_POST['group']) ? $_POST['group'] : "";
		$info_alert_group = isset($_POST['info_alert_group']) ? $_POST['info_alert_group'] : "";
		
		//Get group
		$row_group = $this->get_group();
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("info_alert_flag", 0);	
		
		switch($field)
		{
			case "info_alert_name":
				$this->dbtransporter->where("info_alert_name LIKE '%".$keyword."%'", null);				
			break;			
			case "info_alert_mobile":
				$this->dbtransporter->where("info_alert_mobile LIKE '%".$keyword."%'", null);				
			break;
			case "info_alert_group":
				$this->db->where("info_alert_group", $group);	//print_r($group);exit();			
			break;
			
		}
		
		if($this->sess->user_group <> 0){
			$this->dbtransporter->where("info_alert_group", $this->sess->user_group);	
		}	
		$q = $this->dbtransporter->get("ssi_info_alert", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("info_alert_flag", 0);
		switch($field)
		{
			case "info_alert_name":
				$this->dbtransporter->where("info_alert_name LIKE '%".$keyword."%'", null);				
			break;			
			case "info_alert_mobile":
				$this->dbtransporter->where("info_alert_mobile LIKE '%".$keyword."%'", null);				
			break;
			case "info_alert_group":
				$this->db->where("info_alert_group", $group);				
			break;
				
		}
		if($this->sess->user_group <> 0){
			$this->dbtransporter->where("info_alert_group", $this->sess->user_group);	
		}
		$qt = $this->dbtransporter->get("ssi_info_alert");
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
		$this->params["group"] = $row_group;
		
		$html = $this->load->view('transporter/info_alert/info_alert_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function add_info_alert()
	{
		if (!$this->sess->user_company){
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$id = $this->uri->segment(3);
		$group = $this->get_group();

		if ($id)
		{
			$this->dbtransporter->where("info_alert_id", $id);
			$q = $this->dbtransporter->get("info_alert");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url() . "info_alert");
			}
			
			$row = $q->row();		
			
			$this->params['row'] = $row;
			$this->params['rgroup'] = $group;		
			$this->params['title'] = "Edit Info Alert";
		}
		else
		{
			$this->params['rgroup'] = $group;
			$this->params['title'] = "Add Info Alert";
		}
		
		
		$this->params["content"] = $this->load->view('transporter/info_alert/info_alert_add', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function info_alert_save()
	{
		if (!$this->sess->user_company){
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$id = isset($_POST['info_alert_id']) ? $_POST['info_alert_id'] : 0;
		$info_alert_name = isset($_POST['info_alert_name']) ? trim($_POST['info_alert_name']) : "";
		$info_alert_mobile = isset($_POST['info_alert_mobile']) ? trim($_POST['info_alert_mobile']) : "";
		$info_alert_email = isset($_POST['info_alert_email']) ? trim($_POST['info_alert_email']) : "";
		$info_alert_company = isset($_POST['info_alert_company']) ? trim($_POST['info_alert_company']) : 0;
		$info_alert_group = isset($_POST['info_alert_group']) ? trim($_POST['info_alert_group']) : 0;
		$info_alert_group_name = isset($_POST['info_alert_group_name']) ? trim($_POST['info_alert_group_name']) : "";
		$info_alert_info_mobile = isset($_POST['info_alert_info_mobile']) ? trim($_POST['info_alert_info_mobile']) : 0;
		$info_alert_info_email = isset($_POST['info_alert_info_email']) ? trim($_POST['info_alert_info_email']) : 0;
		$info_alert_config_mobile = isset($_POST['info_alert_config_mobile']) ? trim($_POST['info_alert_config_mobile']) : 0;
		$info_alert_config_email = isset($_POST['info_alert_config_email']) ? trim($_POST['info_alert_config_email']) : 0;
		$info_alert_status = isset($_POST['info_alert_status']) ? trim($_POST['info_alert_status']) : 1;
		
		$error = "";
		
		if ($info_alert_name == "")
		{
			$error .= "- Please fill name \n";	
		}
		if ($info_alert_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($info_alert_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		}
		if ($info_alert_group == "")
		{
			$error .= "- Please select area \n";	
		}else{
			$this->db->order_by("group_name", "asc");
			$this->db->select("group_id, group_name");
			$this->db->where("group_id", $info_alert_group);
			$qGroup = $this->db->get("group");
			$row_group = $qGroup->row();
	
			if(count($row_group) > 0){
				$info_alert_group_name = $row_group->group_name;
			}else{
				$info_alert_group_name = "";
			}
		}
		if ($info_alert_config_mobile == "")
		{
			$error .= "- Please select alert by mobile phone \n";	
		}
		if ($info_alert_status == "")
		{
			$error .= "- Please select status \n";
		}
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		$data['info_alert_name'] = $info_alert_name;
		$data['info_alert_mobile'] = $info_alert_mobile;
		$data['info_alert_email'] = $info_alert_email;
		$data['info_alert_company'] = $info_alert_company;
		$data['info_alert_group'] = $info_alert_group;
		$data['info_alert_group_name'] = $info_alert_group_name;
		$data['info_alert_config_email'] = $info_alert_config_email;
		$data['info_alert_config_mobile'] = $info_alert_config_mobile;
		$data['info_alert_status'] = $info_alert_status;

		if ($id)
		{
			$this->dbtransporter->where("info_alert_id", $id);
			$this->dbtransporter->update("ssi_info_alert", $data);			
			
			$callback['error'] = false;
			$callback['message'] = "Info Alert Successfully Updated";
			$callback['redirect'] = base_url()."ssi_info_alert";
			
			echo json_encode($callback);
			
			return;
		}else{
			
			$this->dbtransporter->insert("ssi_info_alert", $data);
			$id = $this->dbtransporter->insert_id();
		
			
			$callback['error'] = false;
			$callback['message'] = "Info Alert Successfully Submitted";
			$callback['redirect'] = base_url()."ssi_info_alert";
			
			echo json_encode($callback);
			
			return;
		}
	}
	
	function edit_info_alert(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		$this->dbtransporter->where("info_alert_id", $id);
		$q = $this->dbtransporter->get("ssi_info_alert");
			
		if ($q->num_rows() == 0)
		{
			redirect(base_url() . "ssi_info_alert/");
		}
		
		//Get group
		$row_group = $this->get_group();
		$params['rgroup'] = $row_group;	
		
		$row = $q->row();
		$params['row'] = $row;			
		$html = $this->load->view("transporter/info_alert/info_alert_edit", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;
		
		$this->db->cache_delete_all();
		echo json_encode($callback);
	}
	
	function update_info_alert()
	{
		if (!$this->sess->user_company){
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$info_alert_name = isset($_POST['info_alert_name']) ? trim($_POST['info_alert_name']) : "";
		$info_alert_mobile = isset($_POST['info_alert_mobile']) ? trim($_POST['info_alert_mobile']) : "";
		$info_alert_email = isset($_POST['info_alert_email']) ? trim($_POST['info_alert_email']) : "";
		$info_alert_company = isset($_POST['info_alert_company']) ? trim($_POST['info_alert_company']) : 0;
		$info_alert_group = isset($_POST['info_alert_group']) ? trim($_POST['info_alert_group']) : 0;
		$info_alert_group_name = isset($_POST['info_alert_group_name']) ? trim($_POST['info_alert_group_name']) : "";
		$info_alert_info_mobile = isset($_POST['info_alert_info_mobile']) ? trim($_POST['info_alert_info_mobile']) : 0;
		$info_alert_info_email = isset($_POST['info_alert_info_email']) ? trim($_POST['info_alert_info_email']) : 0;
		$info_alert_config_mobile = isset($_POST['info_alert_config_mobile']) ? trim($_POST['info_alert_config_mobile']) : 0;
		$info_alert_config_email = isset($_POST['info_alert_config_email']) ? trim($_POST['info_alert_config_email']) : 0;
		$info_alert_status = isset($_POST['info_alert_status']) ? trim($_POST['info_alert_status']) : 1;
		
		$error = "";
		
		if ($info_alert_name == "")
		{
			$error .= "- Please fill name \n";	
		}
		if ($info_alert_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($info_alert_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		}
		if ($info_alert_group == "")
		{
			$error .= "- Please select area \n";	
		}else{
			$this->db->order_by("group_name", "asc");
			$this->db->select("group_id, group_name");
			$this->db->where("group_id", $info_alert_group);
			$qGroup = $this->db->get("group");
			$row_group = $qGroup->row();
	
			if(count($row_group) > 0){
				$info_alert_group_name = $row_group->group_name;
			}else{
				$info_alert_group_name = "";
			}
		}
		if ($info_alert_config_mobile == "")
		{
			$error .= "- Please select alert by mobile phone \n";	
		}
		if ($info_alert_status == "")
		{
			$error .= "- Please select status \n";
		}
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		$data['info_alert_name'] = $info_alert_name;
		$data['info_alert_mobile'] = $info_alert_mobile;
		$data['info_alert_email'] = $info_alert_email;
		$data['info_alert_company'] = $info_alert_company;
		$data['info_alert_group'] = $info_alert_group;
		$data['info_alert_group_name'] = $info_alert_group_name;
		$data['info_alert_config_email'] = $info_alert_config_email;
		$data['info_alert_config_mobile'] = $info_alert_config_mobile;
		$data['info_alert_status'] = $info_alert_status;
		
		if($id){
				$this->dbtransporter->where("info_alert_id", $id);
				$this->dbtransporter->update("ssi_info_alert", $data);
				$this->dbtransporter->close();
			
				$callback['error'] = false;
				$callback['message'] = "Info Alert Successfully Updated";
				$callback['redirect'] = base_url()."ssi_info_alert";
				echo json_encode($callback);
				return;
			}
	}
	
	function get_group()
	{
		$this->db->order_by("group_name", "asc");
		$this->db->select("group_id, group_name");
		$this->db->where("group_status", 1);
		$this->db->where("group_company", 417); //gm op 1
		$this->db->or_where("group_company", 418); //gm op2
		$qGroup = $this->db->get("group");
		$row_group = $qGroup->result();
		return $row_group;
	}
	
	function delete_info_alert($id)
	{
		if (!$this->sess->user_company){
			redirect(base_url());
		}
	
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$data["info_alert_flag"] = 1;		
		$this->dbtransporter->where("info_alert_id", $id);
		if($this->dbtransporter->update("ssi_info_alert", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function info_vehicle_comment(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		
		$vid = isset($_POST['id']) ? $_POST['id'] : "";	
		
		$this->db->select("vehicle_id, vehicle_device, vehicle_name, vehicle_no");
		$this->db->where("vehicle_id", $vid);
		$qv = $this->db->get("vehicle");	
		$rowv = $qv->row();
		$params['rowv'] = $rowv;	
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		$this->dbtransporter->select("comment_id, comment_title");
		$this->dbtransporter->order_by("comment_datetime", "desc");
		$this->dbtransporter->where("comment_flag", 0);
		$this->dbtransporter->where("comment_status", 0);
		$this->dbtransporter->where("comment_vehicle_id", $vid);
		$q = $this->dbtransporter->get("ssi_vehicle_comment");		
		$row = $q->row();
		$params['row'] = $row;		
		
		$html = $this->load->view("transporter/info_alert/info_comment", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;
		
		$this->db->cache_delete_all();
		echo json_encode($callback);
	}
	
	function save_comment()
	{
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$vid = isset($_POST['vid']) ? trim($_POST['vid']) : "";
		$vname = isset($_POST['vname']) ? trim($_POST['vname']) : "";
		$vno = isset($_POST['vno']) ? trim($_POST['vno']) : "";
		$vdevice = isset($_POST['vdevice']) ? trim($_POST['vdevice']) : "";
		
		$title = isset($_POST['title']) ? trim($_POST['title']) : "";
		$status = isset($_POST['status']) ? trim($_POST['status']) : 0;
		$datetime = date("Y-m-d H:i:s");

		$error = "";
		
		if ($title == "")
		{
			$error .= "- Please fill Comment \n";	
		}
		if ($vid == "")
		{
			$error .= "- No data Vehicle ID ! \n";	
		}
		if ($vname == "")
		{
			$error .= "- No data Vehicle Name ! \n";	
		}
		if ($vno == "")
		{
			$error .= "- No data Vehicle No ! \n";	
		}
		if ($vdevice == "")
		{
			$error .= "- No data Vehicle Device ! \n";	
		}
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		$data['comment_vehicle_id'] = $vid;
		$data['comment_vehicle_name'] = $vname;
		$data['comment_vehicle_no'] = $vno;
		$data['comment_vehicle_device'] = $vdevice;
		$data['comment_title'] = $title;
		$data['comment_datetime'] = $datetime;
		$data['comment_status'] = $status;
		
			if($vid){
				$this->dbtransporter->select("comment_id, comment_vehicle_id");
				$this->dbtransporter->order_by("comment_datetime","desc");
				$this->dbtransporter->where("comment_vehicle_id", $vid);
				$this->dbtransporter->where("comment_flag", 0);
				$this->dbtransporter->where("comment_status", 0);
				$this->dbtransporter->limit(1);
				$q = $this->dbtransporter->get("ssi_vehicle_comment");		
				$row = $q->row();

				if(count($row) > 0){
		
					$this->dbtransporter->select("comment_id, comment_vehicle_id");
					$this->dbtransporter->order_by("comment_datetime","desc");
					$this->dbtransporter->where("comment_vehicle_id", $vid);
					$this->dbtransporter->where("comment_flag", 0);
					$this->dbtransporter->where("comment_status", 0);
					$this->dbtransporter->limit(1);
					$this->dbtransporter->update("ssi_vehicle_comment", $data);

					$callback['error'] = false;
					$callback['message'] = "Comment Successfully Updated";
					$callback['redirect'] = base_url()."trackers";
					echo json_encode($callback);
					
					return;
				}
				
				else{
					$this->dbtransporter->insert("ssi_vehicle_comment", $data);
		
					$callback['error'] = false;
					$callback['message'] = "Comment Successfully Submitted";
					$callback['redirect'] = base_url()."trackers";
					echo json_encode($callback);
					
					return;
				}
				
			}
	}

}