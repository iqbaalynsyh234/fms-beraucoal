<?php
include "base.php";

class Customer_powerblock extends Base {

	function Customer_powerblock()
	{
		parent::Base();	
		$this->load->model("gpsmodel");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
			
	}
	
	function search($offset=0)
	{
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "customer_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		
		//list group
		$this->dbtrans = $this->load->database('transporter',true);
		$this->dbtrans->order_by("customer_name", $orderby);		
		$this->dbtrans->where("customer_status", 1);
		$this->dbtrans->where("customer_name LIKE ", '%'.$keyword.'%');
		$q = $this->dbtrans->get("powerblock_customer", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		
		$this->dbtrans->where("customer_name LIKE ", '%'.$keyword.'%');
		$this->dbtrans->where("customer_status", 1);
		$total = $this->dbtrans->count_all_results("powerblock_customer");
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;		
		
		$callback['html'] = $this->load->view('group/listresult_powerblock', $this->params, true);	
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function index()
	{
		$this->params['sortby'] = "group_name";
		$this->params['orderby'] = "asc";

		$this->params['title'] = $this->lang->line('lgroup_list');
		$this->params["content"] = $this->load->view('group/list_powerblock', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function add($id=0)
	{
		if ($id)
		{
			$this->db->where("company_id", $this->sess->user_company);
			$this->db->join("company", "company_id = group_company");
			$this->db->where("group_id", $id);
			$q = $this->db->get("group");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}
			
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = $this->lang->line('lgroup_edit');
			$def = $row->group_parent;
		}
		else
		{
			$this->params['title'] = $this->lang->line('lgroup_add');
			$def = 0;
		}
		
		$this->db->where("company_id", $this->sess->user_company);
		$q = $this->db->get("company");
		
		$rows = $q->result();
		
		$this->params['rows'] = $rows;
		
		$options = "";
		$this->getParentTreeOptions(&$options, 0, 0, $def);
		
		$this->params['parentoptions'] = $options;
		
		$this->params["content"] = $this->load->view('group/form', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function save()
	{
		$usersite = $this->sess->user_company;
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['groupname']) ? trim($_POST['groupname']) : "";
		$parent = isset($_POST['parent']) ? $_POST['parent'] : 0;
	
		if (strlen($name) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_group_name");
			
			echo json_encode($callback);
			return;
		}
		
		$this->db->where("group_company", $usersite);
		$this->db->where("group_name", $name);
		$q = $this->db->get("group");
		
		if ($q->num_rows() > 0)
		{
			$row = $q->row();
			if ($row->group_id != $id)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lexist_group_name");
				
				echo json_encode($callback);

				return;
			}
		}
		
		unset($data);
		
		$data['group_parent'] = $parent;
		$data['group_name'] = $name;
		$data['group_company'] = $usersite;
		
		if ($id)
		{
			$mydb = $this->load->database("master", TRUE);
			
			$mydb->where("group_id", $id);
			$mydb->update("group", $data);
			
			$this->db->cache_delete_all();
			
			$callback['error'] = false;
			$callback['message'] = $this->lang->line("lgroup_updated");
			$callback['redirect'] = base_url()."group";
			
			echo json_encode($callback);
			
			return;
		}
		
		$data['group_status'] = 1;
		$data['group_parent'] = 0;
		$data['group_creator'] = $this->sess->user_id;
	
		$mydb = $this->load->database("master", TRUE);
		$mydb->insert("group", $data);
		
		$this->db->cache_delete_all();
		
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lgroup_added");
		$callback['redirect'] = base_url()."group";
		
		echo json_encode($callback);
		return;	
	}
	
	function remove($id)
	{
		$mydb = $this->load->database("master", TRUE);

		$update['group_status'] = 2;

		$mydb->where("group_id", $id);
		$mydb->update("group", $update);
		
		$this->db->cache_delete_all();
		
		redirect(base_url()."group");
	}

	function options($id=0)
	{
		$usersite = isset($_POST['usersite']) ? trim($_POST['usersite']) : "";
		$isshowadmin = isset($_POST['showadmin']) ? $_POST['showadmin'] : true;

		/* if (! $usersite)
		{
			$callback['empty'] = true;
			echo json_encode($callback);

			return;
		} */

		$this->db->where("group_status", 1);
		$this->db->where("group_company", $this->sess->user_company);
		$this->db->order_by("group_name", "asc");
		$q = $this->db->get("group");

		if ($q->num_rows() == 0)
		{
			$callback['empty'] = true;
			echo json_encode($callback);

			return;
		}

		$params['isshowadmin'] = $isshowadmin;
		$params['selected'] = $id;
		$params['rows'] = $q->result();
		$html = $this->load->view("group/options", $params, true);

		$callback['empty'] = false;
		$callback['html'] = $html;
	
		echo json_encode($callback);
	}
	
	function getParentTreeOptions($s, $parent, $level=0, $def=0)
	{
		$this->db->order_by("group_name", "asc");
		$this->db->where("group_company", $this->config->item("cust_company"));
		$this->db->where("group_parent", $parent);
		$q = $this->db->get("group");
		
		if ($q->result() == 0) return;

		$res = $q->result();
		for($i=0; $i < count($res); $i++)
		{
			$s .= "<option value='".$res[$i]->group_id."'".(($def == $res[$i]->group_id) ? " selected" : "").">";
			for($j=0; $j <= $level; $j++)
			{
				$s .= "---";
			}		
			
			$s .= " ".$res[$i]->group_name;
			$s .= "</option>";
			
			$this->getParentTreeOptions(&$s, $res[$i]->group_id, $def, $level+1);
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
