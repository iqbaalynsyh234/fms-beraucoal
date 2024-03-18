<?php
include "base.php";

class Payment extends Base {

	function Payment()
	{
		parent::Base();			
	}
		
	function confirmation($id=0)
	{		
		$this->db->where("vehicle_id", $id);
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->join("bank", "user_agent = bank_agent", "left outer");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) 
		{
			redirect(base_url());
			exit;
		}
		
		$row = $q->row();

		if (! $row->bank_id)
		{
			$this->db->where("bank_agent IS NULL OR bank_agent = 0", NULL);
		}		
		else
		{
			$this->db->where("bank_id", $row->bank_id);
		}
		
		$this->db->order_by("bank_order", "asc");		
		$q = $this->db->get("bank");
		
		$banks = $q->result();
		
		$params['banks'] = $banks;
		$params['vehicle'] = $row;
		$callback['html'] = $this->load->view("payment/confirmation", $params, true);
		$callback['title'] = $this->lang->line("lpayment_confirmation_for")." ".$row->vehicle_device;
		
		echo json_encode($callback);
	}

	function allconfirmation($id=0)
	{		
		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		$this->db->order_by("vehicle_active_date2", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->join("bank", "user_agent = bank_agent", "left outer");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) 
		{
			redirect(base_url());
			exit;
		}
		
		$rows = $q->result();

		if (! $rows[0]->bank_id)
		{
			$this->db->where("bank_agent IS NULL OR bank_agent = 0", NULL);
		}		
		else
		{
			$this->db->where("bank_id", $rows[0]->bank_id);
		}
		
		$this->db->order_by("bank_order", "asc");		
		$q = $this->db->get("bank");
		
		$banks = $q->result();
		
		$params['banks'] = $banks;
		$params['vehicles'] = $rows;
		$callback['html'] = $this->load->view("payment/allconfirmation", $params, true);
		$callback['title'] = $this->lang->line("lpayment_confirmation");
		
		echo json_encode($callback);
	}
	
	function saveconfirmation()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : "";
		$transfermethod = isset($_POST['transfermethod']) ? trim($_POST['transfermethod']) : "";
		$bankdest = isset($_POST['bankdest']) ? trim($_POST['bankdest']) : "";
		$amount = isset($_POST['amount']) ? trim($_POST['amount']) : "";
		$paymentdate = isset($_POST['paymentdate']) ? trim($_POST['paymentdate']) : "";
		$transfercode = isset($_POST['transfercode']) ? trim($_POST['transfercode']) : "";
		$sendername = isset($_POST['sendername']) ? trim($_POST['sendername']) : "";
		
		if (strlen($id) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Access denied";
			
			echo json_encode($callback);
			return;
		}
		
		if (strlen($transfermethod) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_transfermethod");
			
			echo json_encode($callback);
			return;
		}
		
		if (strlen($bankdest) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_bankdest");
			
			echo json_encode($callback);
			return;
		}
		
		if (strlen($amount) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_amount");
			
			echo json_encode($callback);
			return;
		}
		
		$paymenttime = formmaketime($paymentdate." 00:00:00");
		if (date("d/m/Y", $paymenttime) != $paymentdate)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_payment_date");
			
			echo json_encode($callback);
			return;
		}
		
		$amount = str_replace(".", "", $amount);
		if (! is_numeric($amount))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_amount");

			echo json_encode($callback);
			return;
			
		}
		
		if (strlen($transfercode) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_transfercode");
			
			echo json_encode($callback);
			return;
		}
		
		if (strlen($sendername) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_sendername");
			
			echo json_encode($callback);
			return;
		}		
		
		$this->db->where("vehicle_id", $id);
		$this->db->join("user", "vehicle_user_id = user_id");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) 
		{
			redirect(base_url());
			exit;
		}
		
		$row = $q->row();				
		$params['vehicle'] = $row;
		
		$this->db->where("bank_id", $bankdest);
		$q = $this->db->get("bank");
		
		if ($q->num_rows() == 0) 
		{
			redirect(base_url());
			exit;
		}
		
		$row = $q->row();				
		$params['bank'] = $row;		
		$params['post'] = $_POST;
					
		$mail['sender'] = $params['vehicle']->user_login;
		$mail['format'] = "html";
		$mail['subject'] = sprintf("payment confirmation for: %s %s %s", $params['vehicle']->user_login, $params['vehicle']->vehicle_name, $params['vehicle']->vehicle_no);
		$mail['message'] = $this->load->view("payment/mailconfirmation", $params, true);
		$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
		
		lacakmobilmail($mail);
		
		
		unset($insert);
		
		$insert['payment_vehicle'] = $id;
		$insert['payment_method'] = $transfermethod;
		$insert['payment_accdest'] = $bankdest;
		$insert['payment_amount'] = $amount;
		$insert['payment_date'] = date("Y-m-d H:i:s", $paymenttime);
		$insert['payment_transfer_code'] = $transfercode;
		$insert['payment_name'] = $sendername;
		$insert['payment_creator'] = $this->sess->user_id;
		$insert['payment_created'] = date("Y-m-d H:i:s");
		$insert['payment_status'] = 1;
		$insert['payment_mail'] = 1;
		
		$this->db->insert('payment', $insert);
		
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lpayment_confirmation_success");
		
		echo json_encode($callback);
		return;		
	}
	
	function index($offset=0)
	{
		if (! $this->sess)
		{
			redirect(base_url());
		}
		
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("bank_agent", $this->sess->agent_id);
		}		
		
		$this->db->order_by("bank_order", "asc");		
		$q = $this->db->get("bank");
		
		$banks = $q->result();
		
		$this->params['sortby'] = "payment_created";
		$this->params['orderby'] = "desc";
		$this->params['banks'] = $banks;
		
		$this->params["content"] = $this->load->view('payment/list', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
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
		$transfermethod = isset($_POST['transfermethod']) ? $_POST['transfermethod'] : "";
		$bankdest = isset($_POST['bankdest']) ? $_POST['bankdest'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "payment_created";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";								
		
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->agent_id);
			$q = $this->db->get("user");
			$rows = $q->result();
			
			$users[] = 0;
			for($i=0; $i < count($rows); $i++)
			{
				$users[] = $rows[$i]->user_id;
			}												
		}	
		
		if (isset($users))
		{
			if ($this->sess->user_type != 1)
			{
				$this->db->where_in("payment_creator", $users);
			}
		}
					
		switch($field)
		{
			case "name":
				$this->db->where("payment_name LIKE '%".$keyword."%'", null);
			break;
			case "vehicle":
				$this->db->where("vehicle_no LIKE '%".$keyword."%' OR vehicle_name LIKE '%".$keyword."%'", null);
			break;			
			case "transfermethod":
				$this->db->where("payment_method", $transfermethod);
			break;	
			case "bankdest":
				$this->db->where("payment_accdest", $bankdest);
			break;					
		}
			
		$this->db->order_by($sortby, $orderby);		
		$this->db->join("vehicle", "vehicle_id = payment_vehicle");
		$this->db->join("bank", "payment_accdest = bank_id", "left outer");
		$q = $this->db->get("payment", $limit, $offset);
		$this->db->flush_cache();
		$rows = $q->result();	

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->payment_created_fmt = dbmaketime($rows[$i]->payment_created);
		}				

		if (isset($users))
		{
			if ($this->sess->user_type != 1)
			{
				$this->db->where_in("payment_creator", $users);
			}
		}				
		switch($field)
		{
			case "name":
				$this->db->where("payment_name LIKE '%".$keyword."%'", null);
			break;
			case "vehicle":
				$this->db->where("vehicle_no LIKE '%".$keyword."%' OR vehicle_name LIKE '%".$keyword."%'", null);
			break;			
			case "transfermethod":
				$this->db->where("payment_method", $transfermethod);
			break;	
			case "bankdest":
				$this->db->where("payment_accdest", $bankdest);
			break;					
		}
						
		$total = $this->db->count_all_results("payment");		
		
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
		
		$html = $this->load->view('payment/result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
		
	function approved($id)
	{
		$this->changestatus($id, 2);
		redirect(base_url()."payment");
	}

	function cancelled($id)
	{
		$this->changestatus($id, 3);
		redirect(base_url()."payment");
	}		
		
	function changestatus($id, $status)
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
			
			$this->db->where("user_agent", $this->sess->agent_id);
			$q = $this->db->get("user");
			$rows = $q->result();
			
			$users[] = 0;
			for($i=0; $i < count($rows); $i++)
			{
				$users[] = $rows[$i]->user_id;
			}
			
			$this->db->where_in("payment_creator", $users);
		}
		
		$this->db->where("payment_id", $id);
		$this->db->join("vehicle", "vehicle_id = payment_vehicle");
		$q = $this->db->get("payment");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}			
		
		$row = $q->row();
		
		
		unset($update);
		
		$update['payment_status'] = $status;
		$this->db->where("payment_id", $id);
		$this->db->update("payment", $update);				
		
		if ($status == 2)
		{
			if ($row->vehicle_active_date2)
			{
				$t = dbintmaketime($row->vehicle_active_date2, 0);				
				$t = mktime(0, 0, 0, date('n', $t), date('j', $t), date('Y', $t)+1);
								
				unset($update);
				$update['vehicle_active_date2'] = date("Ymd", $t);
				
				$this->db->where("vehicle_id", $row->vehicle_id);
				$this->db->update("vehicle", $update);
				$this->db->cache_delete_all();
			}
			
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
