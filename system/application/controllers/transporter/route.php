<?php
include "base.php";

class Route extends Base {

	function Route()
	{
		parent::Base();
		if (! isset($this->sess->user_company))
		{
			redirect(base_url());
		}		
		
	}
	
	function index($field="all", $keyword="all", $offset=0)
	{
		$this->params['sortby'] = "route_id";
		$this->params['orderby'] = "asc";
		$this->params["content"] = $this->load->view('route/list.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search($field="all", $keyword="all", $offset=0)
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		if (!$my_company)
		{
			redirect(base_url());
		}
		
		$this->dbtransporter->order_by("route_name","asc");
		
		switch($field)
		{
			case "route_name":
				$this->dbtransporter->where("route_name LIKE '%".$keyword."%'", null);
			break;
		}
		
		$this->dbtransporter->where("route_status","1");
		$this->dbtransporter->where("route_company", $my_company);
		
		$q = $this->dbtransporter->get("route", 50, $offset);
		$rows = $q->result();
		
		$this->dbtransporter->order_by("route_name","asc");
		
		switch($field)
		{
			case "route_name":
				$this->dbtransporter->where("route_name LIKE '%".$keyword."%'", null);
			break;
		}
		
		$this->dbtransporter->where("route_company", $my_company);
		
		$this->dbtransporter->where("route_status","1");
		$qtotal = $this->dbtransporter->get("route");
		$rowstotal = $qtotal->result();
		
		$total = count($rowstotal);
		
		$this->params["title"] = "Manage Route";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("route/listresult.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function add()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->params["title"] = "Manage Route - ADD";		
		$this->params['content'] = $this->load->view("route/add", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$route_name = $this->input->post("route_name");
		$route_note = $this->input->post("route_note");
		$my_company = $this->sess->user_company;
		$user_id = $this->sess->user_id;
		
		if ($route_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Route Name!";
		
			echo json_encode($callback);
			return;
		}
		
		unset($data);
		$data["route_name"] = $route_name;
		$data["route_note"] = $route_note;
		$data["route_company"] = $my_company;
		$data["route_created"] = $user_id;
		
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->insert("route",$data);
		
		$callback["error"] = false;
		$callback["message"] = "Add Route Success";
		$callback["redirect"] = base_url()."transporter/route";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	}
	
	function edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->where("route_company", $my_company);
		$this->dbtransporter->where("route_id", $id);
		$q = $this->dbtransporter->get("route");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('route/edit', $params, true);
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
		
		$route_id = isset($_POST['route_id']) ? $_POST['route_id'] : 0;
		$route_name = isset($_POST['route_name']) ? $_POST['route_name'] : "";
		$route_note = isset($_POST['route_note']) ? $_POST['route_note'] : "";
		
		if ($route_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Route Name!";
		
			echo json_encode($callback);
			return;
		}
		
		unset($data);
		$data['route_name'] = $route_name;
		$data['route_note'] = $route_note;
		
		$this->dbtransporter->where('route_id', $route_id);
		$this->dbtransporter->update('route', $data);
		
		$callback["error"] = false;
		$callback["message"] = "Edit Route Success";
		$callback["redirect"] = base_url()."transporter/route";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	
	}
	
	function delete()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("route_company", $my_company);
		$this->dbtransporter->where("route_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("route");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('route/delete', $params, true);
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
		
		$route_id = isset($_POST['route_id']) ? $_POST['route_id'] : 0;
		
		unset($data);
		$data['route_status'] = 0;
		
		$this->dbtransporter->where('route_id', $route_id);
		$this->dbtransporter->update('route', $data);
		
		$callback["error"] = false;
		$callback["message"] = "Delete Route Success";
		$callback["redirect"] = base_url()."transporter/route";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	
	}
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */