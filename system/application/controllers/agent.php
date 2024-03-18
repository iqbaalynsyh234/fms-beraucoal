<?php
include "base.php";

class Agent extends Base {

	function Agent()
	{
		parent::Base();	
		$this->load->model("gpsmodel");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		if ($this->sess->user_type != 1)
		{
			redirect(base_url());
		}
		
	}
	
	function search($offset=0)
	{
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "agent_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		
		$this->db->order_by($sortby, $orderby);		
		$this->db->where("agent_name LIKE ", '%'.$keyword.'%');
		$q = $this->db->get("agent", $this->config->item("limit_records"), $offset);
		$rows = $q->result();		
		
		$this->db->where("agent_name LIKE ", '%'.$keyword.'%');
		$total = $this->db->count_all_results("agent");
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;		
		
		$callback['html'] = $this->load->view('agent/listresult', $this->params, true);	
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function index()
	{
		$this->params['sortby'] = "agent_name";
		$this->params['orderby'] = "asc";

		$this->params['title'] = $this->lang->line('lagent_list');
		$this->params["content"] = $this->load->view('agent/list', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function add($id=0)
	{
		if ($id)
		{
			$this->db->where("agent_id", $id);
			$q = $this->db->get("agent");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}
			
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = $this->lang->line('lagent_edit');
		}
		else
		{
			$this->params['title'] = $this->lang->line('lagent_add');
		}
		
		$this->params["content"] = $this->load->view('agent/form', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function save()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['agent']) ? trim($_POST['agent']) : "";
		$site = isset($_POST['site']) ? trim($_POST['site']) : "";
		$canedit_vactive = isset($_POST['canedit_vactive']) ? trim($_POST['canedit_vactive']) : 0;		
		$agent_alert_pulsa = isset($_POST['agent_alert_pulsa']) ? trim($_POST['agent_alert_pulsa']) : 0;
		$agent_payment_periode = isset($_POST['agent_payment_periode']) ? trim($_POST['agent_payment_periode']) : 0;
		
		$agent_payment_amount = isset($_POST['agent_payment_amount']) ? trim($_POST['agent_payment_amount']) : 0;
		$agent_payment_amount = str_replace(",", "", $agent_payment_amount);
		
		if (strlen($name) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_agent_name");
			
			echo json_encode($callback);
			return;
		}
		
		$this->db->where("agent_name", $name);
		$q = $this->db->get("agent");
		
		if ($q->num_rows() > 0)
		{
			$row = $q->row();
			if ($row->agent_id != $id)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lexit_agent_name");
				
				echo json_encode($callback);

				return;
			}
		}
		
		if (strlen($agent_payment_periode) > 0)
		{
			if ((! is_numeric($agent_payment_periode)) || ($agent_payment_periode < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_payment_period');
				
				echo json_encode($callback);
				return;			
			}
		}
		
		
		if (strlen($agent_payment_amount) > 0)
		{
			if ((! is_numeric($agent_payment_amount)) || ($agent_payment_amount < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_payment_amount');
				
				echo json_encode($callback);
				return;			
			}		
		}
		
		unset($data);
		
		$data['agent_name'] = $name;
		$data['agent_site'] = $site;
		$data['agent_canedit_vactive'] = $canedit_vactive;		
		$data['agent_payment_periode'] = $agent_payment_periode;
		$data['agent_payment_amount'] = $agent_payment_amount;
		$data['agent_alert_pulsa'] = $agent_alert_pulsa;
		
		if ($id)
		{
			$mydb = $this->load->database("master", TRUE);
			
			$mydb->where("agent_id", $id);
			$mydb->update("agent", $data);
			
			$this->db->cache_delete_all();
			
			$callback['error'] = false;
			$callback['message'] = $this->lang->line("lagent_updated");
			$callback['redirect'] = base_url()."agent";
			
			echo json_encode($callback);
			
			return;
		}
		
		$data['agent_status'] = 1;
		
		$mydb = $this->load->database("master", TRUE);
		$mydb->insert("agent", $data);
		
		$this->db->cache_delete_all();
		
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lagent_added");
		$callback['redirect'] = base_url()."agent";
		
		echo json_encode($callback);
		return;	
	}
	
	function remove($id)
	{
		$mydb = $this->load->database("master", TRUE);

		$mydb->where("agent_id", $id);
		$mydb->delete("agent");
		
		$this->db->cache_delete_all();
		
		redirect(base_url()."agent");
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
