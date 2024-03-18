<?php
include "base.php";

class Company extends Base {

	function Company()
	{
		parent::Base();	
		$this->load->model("gpsmodel");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		if (!$this->sess->user_type == 1)
		{
		  redirect(base_url());
		}
		
	}
    
    function getlist($field='all', $keyword='all', $offset='0')
    {
        if (!$this->sess->user_type == 1)
		{
		  redirect(base_url());
		}
        
        switch($field)
        {
            case "company_name":
            $this->db->where("company_name LIKE '%".$keyword."%'", null );
            break;

        }
        
        $this->db->order_by("company_name", "asc");
        $q = $this->db->get("company", $this->config->item("limit_record"), $offset);
        $rows = $q->result();
        
        $total = count($rows);
        
        $this->load->library("pagination");
        
        $config['uri_segment'] = 5;
		$config['base_url'] = base_url()."company/getlist/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
        
        $this->pagination->initialize($config);
        $rows_agent = $this->get_agent();
        
        $this->db->cache_delete_all();
        
        $this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
        $this->params["data_agent"] = $rows_agent;			
        $this->params['title'] = $this->lang->line('lgroup_list');
		$this->params["content"] = $this->load->view('company/list', $this->params, true);
        $this->load->view("templatesess", $this->params);

    }
    
    function add()
    {
        if (!$this->sess->user_type == 1)
		{
		  redirect(base_url());
		}
        
        $rows_agent = $this->get_agent();
        
        $this->params["agent"] = $rows_agent;
        $this->params["content"] = $this->load->view('company/add', $this->params, true);
        $this->load->view("templatesess", $this->params);

    }
    
    function edit()
    {
        if (!$this->sess->user_type == 1)
		{
		  redirect(base_url());
		}
        
        $id = $this->uri->segment(3);
        $rows_agent = $this->get_agent();
        
        if ($id)
        {
            $this->db->where("company_id",$id);
            $q = $this->db->get("company");
            $rows = $q->row();
            
            $this->params["data"] = $rows;
            $this->params["data_agent"] = $rows_agent;			
            $this->params['title'] = "Edit Company";
		    $this->params["content"] = $this->load->view('company/edit', $this->params, true);
            $this->load->view("templatesess", $this->params);
            
        }
    }
    
    function save()
    {
        if (!$this->sess->user_type == 1)
		{
		  redirect(base_url());
		}
        
        $company_name = isset($_POST["company_name"]) ? $_POST["company_name"] : "";
        $company_agent = isset($_POST["company_agent"]) ? $_POST["company_agent"] : 0;
        
        if (!$company_name)
        {
            echo json_encode(array("error"=>true, "message"=>"Please Input Company Name"));
            return;
        }
        
        if ($company_agent == "0")
        {
            echo json_encode(array("error"=>true, "message"=>"Please Select Agent Name"));
            return;
        }
        
        unset($data);
        $data["company_name"] = $company_name;
        $data["company_agent"] = $company_agent;
        
        //print_r($data);exit;
        
        $this->db->insert("company", $data);
        $this->db->cache_delete_all();
        
        $callback["error"] = false;
        $callback["message"] = "Process Complete";
        $callback["redirect"] = base_url()."company/getlist";
        
        echo json_encode($callback);
        return;
        
    }
    
    function get_agent()
    {
        $this->db->select("*");
        $this->db->from("agent");
        $q = $this->db->get();
        $data_agent = $q->result();
        $this->db->cache_delete_all();
        return $data_agent;
        
    }
		
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/company.php */
