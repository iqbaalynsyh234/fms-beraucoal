<?php
include "base.php";

class Announcement extends Base {

	function Announcement()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->helper("email");
		$this->load->helper("common");
	}
	
	function index()
	{
		if ($this->config->item('license'))
		{
			$this->db->where("announcement_owner", $this->config->item('license'));
		}
		else
		{
			$this->db->where("announcement_owner", "lacak-mobil.com");
		}
		
		$this->db->order_by("announcement_created", "desc");		
		$this->db->where("announcement_status", 1);
		$q = $this->db->get("announcement");
		$rows = $q->result();
		
		$params['rows'] = $rows;
		
		$callback['isempty'] = count($rows) == 0;
		$callback['html'] = $this->load->view("announcement/list", $params, true);
		$callback['title'] = $this->lang->line("lannouncement");
		
		echo json_encode($callback);
	}
	
	function status($id)
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}
		
		if ($this->sess->user_type == 3)
		{
			if ($this->sess->user_agent_admin != 1)
			{
				redirect(base_url());
			}
			
			if ($this->sess->agent_site != $_SERVER['SERVER_NAME'])
			{
				redirect(base_url());
			}
		}

		$this->db->where("announcement_id", $id);
		$q = $this->db->get("announcement");
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$row = $q->row();
		
		unset($update);
		$update['announcement_status'] = ($row->announcement_status == 1) ? 0 : 1;
		
		$this->db->where("announcement_id", $id);
		$this->db->update("announcement", $update);
		
		$this->db->cache_delete_all();
		
		redirect(base_url()."announcement/show");		
	}
	
	function add($id=0)
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}
		
		if ($this->sess->user_type == 3)
		{
			if ($this->sess->user_agent_admin != 1)
			{
				redirect(base_url());
			}
			
			if ($this->sess->agent_site != $_SERVER['SERVER_NAME'])
			{
				redirect(base_url());
			}
		}

		if ($id)	
		{
			$this->db->where("announcement_id", $id);
			$q = $this->db->get("announcement");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}
			
			$row = $q->row();
			$this->params['row'] = $row;
			
			$this->params['title'] = $this->lang->line('lannouncement_edit');
		}
		else
		{
			$this->params['title'] = $this->lang->line('lannouncement_add');
		}

		$this->params["content"] = $this->load->view('announcement/form', $this->params, true);		
		$this->load->view("templatesess", $this->params);					
	}
	
	function save()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$message = isset($_POST['message']) ? $_POST['message'] : 0;
		
		if (strlen($message) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_message");
			
			echo json_encode($callback);
			return;
		}
		
		unset($data);
		
		$data['announcement_message'] = $message;
		$data['announcement_owner'] = $this->config->item('license') ? $this->config->item('license') : "lacak-mobil.com";		
		
		if ($id)
		{
			$mydb = $this->load->database("master", TRUE);
			
			$mydb->where("announcement_id", $id);
			$mydb->update("announcement", $data);
			
			$this->db->cache_delete_all();
			
			$callback['error'] = false;
			$callback['message'] = $this->lang->line("lannouncement_updated");
			$callback['redirect'] = base_url()."announcement/show";
			
			echo json_encode($callback);
			
			return;
		}
		
		$data['announcement_creator'] = $this->sess->user_id;
		$data['announcement_created'] = date("Y-m-d H:i:s");		
		$data['announcement_status'] = 1;
		
		$mydb = $this->load->database("master", TRUE);
		$mydb->insert("announcement", $data);
		
		$this->db->cache_delete_all();
		
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lannouncement_added");
		$callback['redirect'] = base_url()."announcement/show";
		
		echo json_encode($callback);
		return;	
	}	
	
	function show()
	{
		$this->params['canedit'] = 0;
		
		if (! $this->sess)
		{
			redirect(base_url());
		}
		
		if ($this->sess->user_type == 1)
		{
			$this->params['canedit'] = 1;
		}
		else
		if ($this->sess->user_type == 3)
		{
			if (($this->sess->user_agent_admin == 1) && ($this->sess->agent_site == $_SERVER['SERVER_NAME']))
			{
				$this->params['canedit'] = 1;
			}
		}
				
		$this->params['sortby'] = "announcement_created";
		$this->params['orderby'] = "desc";
		$this->params['title'] = $this->lang->line('luser_list').", ".$this->lang->line('llist_trackers');
		
		$this->params["content"] = $this->load->view('announcement/mainlist', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function mailform($auth1, $auth2)
	{
		$params = array();
		
		if (isset($_POST['subject']))
		{
			$subject = isset($_POST['subject']) ? trim($_POST['subject']) : "";
			$message = isset($_POST['message']) ? trim($_POST['message']) : "";
			
			if (! $subject)
			{
				$err = true;
				$params['errmessage'][] = "Please input subject!";
			}

			if (! $message)
			{
				$err = true;
				$params['errmessage'][] = "Please input message!";
			}
			
			set_time_limit(0);
			
			$this->db->select("user_mail");
			$this->db->distinct();
			$q = $this->db->get("user");
			
			$rows = $q->result();
						
			for($i=0; $i < count($rows); $i++)
			{
				$emails = get_valid_emails($rows[$i]->user_mail);
				
				if ($emails === FALSE) continue;

				foreach($emails as $email)
				{
					
					maillocalhost($subject, $message, $email);
					
					unset($insert);				
					unset($content);
					
					$content['subject'] = '[www.lacak-mobil.com :: announcement] '.$subject;
					$content['message'] = nl2br($message);
					$content['to'] = $email;
					
					$insert['logs_type'] = 'announcement';
					$insert['logs_created'] = date("Y-m-d H:i:s");
					$insert['logs_content'] = json_encode($content);
					
					$mydb = $this->load->database("master", TRUE);
					$mydb->insert("logs", $insert);			
						
					sleep(1);
				}
			}
			
			if (! isset($err))
			{				
				redirect(base_url()."announcement/mailform/".date("Ymd")."/bismillaah/success/".uniqid());
				return;
			}						
		}
		
		if (date("Ymd") != $auth1)
		{
			redirect(base_url());
			return;
		}
		
		if ("bismillaah" != $auth2)
		{
			redirect(base_url());
			return;			
		}
		
		$this->load->view("announcement/mail", $params);
	}
	
	function search()
	{
		$this->params['canedit'] = 0;
		
		if ($this->sess->user_type == 1)
		{
			$this->params['canedit'] = 1;
		}
		else
		if ($this->sess->user_type == 3)
		{
			if (($this->sess->user_agent_admin == 1) && ($this->sess->agent_site == $_SERVER['SERVER_NAME']))
			{
				$this->params['canedit'] = 1;
			}
		}		
		$limit = $this->config->item("limit_records");
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "announcement_created";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";								
		
		switch($field)
		{
			case "announcement_message":
				$this->db->where($field." LIKE '%".$keyword."%'", null);				
			break;			
		}

		if ($this->config->item('license'))
		{
			$this->db->where("announcement_owner", $this->config->item('license'));
		}
		else
		{
			$this->db->where("announcement_owner", "lacak-mobil.com");
		}
		if ($this->sess->user_type == 2)
		{
			$this->db->where("announcement_status", 1);
		}
			
		$this->db->order_by($sortby, $orderby);		
		$q = $this->db->get("announcement", $limit, $offset);
		$rows = $q->result();	
				
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->announcement_created_fmt = dbmaketime($rows[$i]->announcement_created);
		}				

		if ($this->config->item('license'))
		{
			$this->db->where("announcement_owner", $this->config->item('license'));
		}
		else
		{
			$this->db->where("announcement_owner", "lacak-mobil.com");
		}
		switch($field)
		{
			case "announcement_message":
				$this->db->where($field." LIKE '%".$keyword."%'", null);				
			break;			
		}				
		if ($this->sess->user_type == 2)
		{
			$this->db->where("announcement_status", 1);
		}

		$total = $this->db->count_all_results("announcement");		
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $limit;
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		
		$html = $this->load->view('announcement/result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
