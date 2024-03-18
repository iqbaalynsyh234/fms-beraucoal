<?php
include "base.php";

class ContactUs extends Base {

	function ContactUs()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->helper("email");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}				
	}
	function search()
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		$limit = $this->config->item("limit_records");
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$dest = isset($_POST['dest']) ? $_POST['dest'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "created";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";								
				
		$mydb = $this->load->database("joomla", TRUE);

		if ($dest)
		{	
			$mydb->where("catid", $dest);
			$q = $mydb->get("contact_details");
			$rows = $q->result();
			
			$mails = array("");
			for($i=0; $i < count($rows); $i++)
			{
				$mails[] = $rows[$i]->email_to;
			}
		}
		
		switch($field)
		{
			case "name":
			case "email":
				$mydb->where($field." LIKE '%".$keyword."%'", null);				
			break;			
			case "dest":
				if ($dest)
				{										
					$mydb->where_in("dest", $mails);		
				}
			break;
			case "status":
				if ($status)
				{
					$mydb->where("status", $status);		
				}
			break;
		}

		$uniqid = uniqid();
		$mydb->where("'".$uniqid."'='".$uniqid."'", null, false);		
		$mydb->order_by($sortby, $orderby);		
		$q = $mydb->get("contacts", $limit, $offset);
		$rows = $q->result();	
				
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->created_fmt = dbmaketime($rows[$i]->created);
		}				
				
		switch($field)
		{
			case "name":
			case "email":
				$mydb->where($field." LIKE '%".$keyword."%'", null);				
			break;			
			case "dest":
				if ($dest)
				{										
					$mydb->where_in("dest", $mails);		
				}
			break;
			case "status":
				if ($status)
				{
					$mydb->where("status", $status);		
				}
			break;
		}		
		
		$mydb->where("'".$uniqid."'='".$uniqid."'", null, false);
		$total = $mydb->count_all_results("contacts");		
		
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
		
		$html = $this->load->view('contactus/result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function index($offset=0)
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		$mydb = $this->load->database("joomla", TRUE);
		
		$mydb->order_by("title", "asc");
		$mydb->where("section", "com_contact_details");
		$q = $mydb->get("categories");
		$categories = $q->result();

		$this->params['categories'] = $categories;
		$this->params['sortby'] = "created";
		$this->params['orderby'] = "asc";
		
		$this->params["content"] = $this->load->view('contactus/list', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function remove($id)
	{
		$mydb = $this->load->database("joomla", TRUE);
		
		$mydb->where("id", $id);
		$mydb->delete("contacts");
		
		redirect(base_url()."contactus");
	}
	
	function status($id)
	{
		$mydb = $this->load->database("joomla", TRUE);

		$mydb->where("id", $id);
		$q = $mydb->get("contacts");
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$row = $q->row();
		
		unset($update);
		$update['status'] = ($row->status == 1) ? 2 : 1;
		
		$mydb->where("id", $id);
		$mydb->update("contacts", $update);
		
		redirect(base_url()."contactus");
	}	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
