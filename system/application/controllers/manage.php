<?php
include "base.php";

class Manage extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->model('dashboardmodel');
		$this->load->model('dashboardmanagemodel');
	}
	
	function vehicle(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}
		
		if($this->sess->user_level == 1){
			$parent_id = $this->sess->user_id;
		}else{
			$parent_id = $this->sess->user_parent;
		}
		
		//Get company
		$row_company = $this->dashboardmanagemodel->getcompany_byowner($parent_id);
		$this->params['rcompany'] = $row_company;
		
		$companyall = $this->dashboardmanagemodel->getcompanyall($parent_id);
		$this->params['companyall'] = $companyall;
		
		$subcompanyall = $this->dashboardmanagemodel->getsubcompanyall($parent_id);
		$this->params['subcompanyall'] = $subcompanyall;
		
		$groupall = $this->dashboardmanagemodel->getgroupall($parent_id);
		$this->params['groupall'] = $groupall;
		
		$this->params["header"] =  $this->load->view('dashboard/header', $this->params, true);	
		$this->params["sidebar"] =  $this->load->view('dashboard/sidebar', $this->params, true);	
		$this->params["chatsidebar"] =  $this->load->view('dashboard/chatsidebar', $this->params, true);	
		$this->params["content"] =  $this->load->view('dashboard/manage/vehicle_list', $this->params, true);				
		$this->load->view("dashboard/template_dashboard", $this->params);	
	}
	
	function searchvehicle(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "parent_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$vehicle_company = isset($_POST['vehicle_company']) ? $_POST['vehicle_company'] : "";
		
		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_name,vehicle_no,vehicle_company,vehicle_subcompany,vehicle_group,
						   vehicle_subgroup,vehicle_device,vehicle_card_no");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		
		switch($field)
		{
			case "vehicle_no":
				$this->db->where("vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "vehicle_name":
				$this->db->where("vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "vehicle_company":
				if($vehicle_company !="")
				$this->db->where("vehicle_company", $vehicle_company);				
			break;
			case "vehicle_subcompany":
				if($vehicle_subcompany !="")
				$this->db->where("vehicle_subcompany", $vehicle_subcompany);				
			break;
			case "vehicle_group":
				if($vehicle_group !="")
				$this->db->where("vehicle_group", $vehicle_group);				
			break;
			case "vehicle_subgroup":
				if($vehicle_subgroup !="")
				$this->db->where("vehicle_subgroup", $vehicle_subgroup);				
			break;
			
		}
	
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		
		//Hitung total
		$this->db->select("count(*) as total");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		switch($field)
		{
			case "vehicle_no":
				$this->db->where("vehicle_no LIKE '%".$keyword."%'", null);				
			break;
			case "vehicle_name":
				$this->db->where("vehicle_name LIKE '%".$keyword."%'", null);				
			break;
			case "vehicle_company":
				if($vehicle_company !="")
				$this->db->where("vehicle_company", $vehicle_company);				
			break;
			case "vehicle_subcompany":
				if($vehicle_subcompany !="")
				$this->db->where("vehicle_subcompany", $vehicle_subcompany);				
			break;
			case "vehicle_group":
				if($vehicle_group !="")
				$this->db->where("vehicle_group", $vehicle_group);				
			break;
			case "vehicle_subgroup":
				if($vehicle_subgroup !="")
				$this->db->where("vehicle_subgroup", $vehicle_subgroup);				
			break;
			
		}
		
		$qt = $this->db->get("vehicle");
		$rt = $qt->row();
		$total = $rt->total;
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		
		if($this->sess->user_level == 1){
			$parent_id = $this->sess->user_id;
		}else{
			$parent_id = $this->sess->user_parent;
		}
		
		//Get company
		$row_company = $this->dashboardmanagemodel->getcompany_byowner($parent_id);
		$this->params['rcompany'] = $row_company;
		
		$companyall = $this->dashboardmanagemodel->getcompanyall($parent_id);
		$this->params['companyall'] = $companyall;
		
		$subcompanyall = $this->dashboardmanagemodel->getsubcompanyall($parent_id);
		$this->params['subcompanyall'] = $subcompanyall;
		
		$groupall = $this->dashboardmanagemodel->getgroupall($parent_id);
		$this->params['groupall'] = $groupall;
		
		$subgroupall = $this->dashboardmanagemodel->getsubgroupall($parent_id);
		$this->params['subgroupall'] = $subgroupall;
		
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$html = $this->load->view('dashboard/manage/vehicle_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function editvehicle(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}
		
		$id = $this->uri->segment(3);
		
		if($this->sess->user_level == 1){
			$parent_id = $this->sess->user_id;
		}else{
			$parent_id = $this->sess->user_parent;
		}
		
		$this->db->select("vehicle_id,vehicle_name,vehicle_no,vehicle_company,vehicle_subcompany,vehicle_group,vehicle_subgroup");
		$this->db->where("vehicle_id", $id);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$qr = $this->db->get("vehicle");
		$row = $qr->row();
		
		if(count($row) == 0){
			redirect(base_url()."manage/vehicle");
		}
		
		$this->params['row'] = $row;
		
		//Get company
		$row_company = $this->dashboardmanagemodel->getcompany_byowner($parent_id);
		$this->params['rcompany'] = $row_company;
		
		$companyall = $this->dashboardmanagemodel->getcompanyall($parent_id);
		$this->params['companyall'] = $companyall;
		
		$subcompanyall = $this->dashboardmanagemodel->getsubcompanyall($parent_id);
		$this->params['subcompanyall'] = $subcompanyall;
		
		$groupall = $this->dashboardmanagemodel->getgroupall($parent_id);
		$this->params['groupall'] = $groupall;
		
		$subgroupall = $this->dashboardmanagemodel->getsubgroupall($parent_id);
		$this->params['subgroupall'] = $subgroupall;
		
		
		$this->params["header"] =  $this->load->view('dashboard/header', $this->params, true);	
		$this->params["sidebar"] =  $this->load->view('dashboard/sidebar', $this->params, true);	
		$this->params["chatsidebar"] =  $this->load->view('dashboard/chatsidebar', $this->params, true);	
		$this->params["content"] =  $this->load->view('dashboard/manage/vehicle_edit', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}
	
	function savevehicle(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$vehicle_name = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : "";
		$company = isset($_POST['company']) ? trim($_POST['company']) : 0;
		$subcompany = isset($_POST['subcompany']) ? trim($_POST['subcompany']) : 0;
		$group = isset($_POST['group']) ? trim($_POST['group']) : 0;
		$subgroup = isset($_POST['subgroup']) ? trim($_POST['subgroup']) : 0;
		
		$error = "";
	
		if ($vehicle_name == "")
		{
			$error .= "- Please fill Vehicle Name ! \n";	
		}
		
		if ($company == "")
		{
			$error .= "- Please Select Area ! \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		
		$data['vehicle_name'] = $vehicle_name;
		$data['vehicle_company'] = $company;
		$data['vehicle_subcompany'] = $subcompany;
		$data['vehicle_group'] = $group;
		$data['vehicle_subgroup'] = $subgroup;
		
		if ($id > 0)
		{
			$this->db->limit(1);
			$this->db->where("vehicle_id",$id);
			$this->db->update("vehicle",$data);
			
			$callback['error'] = false;
			$callback['message'] = "Edit Data Success";
			$callback["redirect"] = base_url()."manage/vehicle";
			echo json_encode($callback);
				
			return;
		}else{
				
			$callback['error'] = true;	
			$callback['message'] = "No Data Vehicle!";	
			$callback["redirect"] = base_url()."manage/vehicle";
			
			echo json_encode($callback);			
			return;
		}

	}
	
	function get_company_byarea($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_id", $id);
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_vehicle_byarea($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_device,vehicle_no,vehicle_name");
		$this->db->where("vehicle_company", $id);
		$this->db->where("vehicle_status <>", 3);
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_company_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $this->sess->user_id);
		$qd = $this->db->get("company");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function company_onchange($id){
		$rows = $this->dashboardmanagemodel->getsubcompany_byparent($id);
		if(count($rows) > 0){
			$options = "<option value='0' selected='selected' >--Select Sub Company--</option>";
			foreach($rows as $obj){
				$options .= "<option value='". $obj->subcompany_id . "'>". $obj->subcompany_name."</option>";
			}
			
			echo $options;
			return;
		}
		
		return $options;
	}
	
	function subcompany_onchange($id){
		$rows = $this->dashboardmanagemodel->getgroup_byparent($id);
		if(count($rows) > 0){
			$options = "<option value='0' selected='selected' >--Select Group--</option>";
			foreach($rows as $obj){
				$options .= "<option value='". $obj->group_id . "'>". $obj->group_name."</option>";
			}
			
			echo $options;
			return;
		}
		
		return $options;
	}
	
	function group_onchange($id){
		$rows = $this->dashboardmanagemodel->getsubgroup_byparent($id);
		if(count($rows) > 0){
			$options = "<option value='0' selected='selected' >--Select Sub Group--</option>";
			foreach($rows as $obj){
				$options .= "<option value='". $obj->subgroup_id . "'>". $obj->subgroup_name."</option>";
			}
			
			echo $options;
			return;
		}
		
		return $options;
	}
	
}