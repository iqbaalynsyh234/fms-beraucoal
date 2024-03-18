<?php
include "base.php";

class Invoice extends Base {

	function Invoice()
	{
		parent::Base();	
		
		$this->load->helper("common");
		$this->load->model("invoicemodel");
		$this->load->helper("email");
	}

	function create($nexpired=7)
	{
		$now = mktime()+$nexpired*24*3600;
		
		$this->db->where("vehicle_status", 1);
		
		$this->db->where("((user_payment_type = 1) OR (agent_payment_amount > 0))", NULL);
		$this->db->where("user_agent <>", $this->config->item("GPSANDALASID"));
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 <=",  date("Ymd", $now));
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->join("agent", "user_agent = agent_id");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{			
			unset($mail);
			
			$mail['sender'] = "support@lacak-mobil.com";
			$mail['format'] = "html";
			$mail['subject'] = "Invoice Job";
			$mail['message'] = sprintf("Tidak ada kendaraan yang %d hari lagi expired\n", $nexpired); ;				
			$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 

			lacakmobilmail($mail);			
			
			return;
		}
				
		$rows = $q->result();

		$erpdb = $this->load->database("erp", TRUE);
		
		for($i=0; $i < count($rows); $i++)
		{
			if ($rows[$i]->user_payment_type == 2) continue;			
			if ($rows[$i]->agent_payment_amount > 0)
			{
				$rows[$i]->user_payment_amount = $rows[$i]->agent_payment_amount;
				$rows[$i]->user_payment_period = $rows[$i]->agent_payment_periode;
			}
			
			printf("process kendaraan %s %s\n", $rows[$i]->user_login, $rows[$i]->vehicle_no);
			
			$erpdb->limit(1);
			$erpdb->order_by("invoice_date", "desc");
			$erpdb->join("invoice_vehicle", "invoice_id = invoice_vehicle_invoice");
			$erpdb->where("invoice_vehicle_vehicle", $rows[$i]->vehicle_id);						
			$q = $erpdb->get("invoice");
			
			if ($q->num_rows()) 
			{
				$rowinvoice = $q->row();
				
				$t_nextexpired = dbmaketime($rowinvoice->invoice_period2);
				if ($now < $t_nextexpired) 
				{
					printf("--- invoice sudah ada\n");
					continue;
				}
				
				$t_period1 = $t_nextexpired;
			}
			else
			{
				$t_period1 = dbintmaketime($rows[$i]->vehicle_active_date2, 0);
			}
			
			printf("--- invoice OK\n");
			
			$t_period2 = mktime(0, 0, 0, date("n", $t_period1)+$rows[$i]->user_payment_period, date("j", $t_period1), date("Y", $t_period1));
			$t_invoice_expired = mktime(0, 0, 0, date("n", $now)+$rows[$i]->user_payment_period, date("j", $now), date("Y", $now));
			
			$rows[$i]->t_period2 = $t_period2;
			$rows[$i]->t_period1 = $t_period1;
			
			$invoices[$rows[$i]->user_login][$t_period1][] = $rows[$i];
		}
		
		if (! isset($invoices)) return;
		
		foreach($invoices as $login=>$invoice)
		{
			foreach($invoice as $expiredate=>$vehicles)
			{
				$sessionid = md5(uniqid());
				$invoiceno = $this->invoicemodel->getInvoiceNo($erpdb);
				
				$params["vehicles"] = $vehicles;
				$params['invoiceno'] = $invoiceno;
				$params['session'] = $sessionid;
				$printed = $this->load->view("invoice/invoice", $params, true);
				
				unset($insert);
				
				$insert["invoice_no"] = $invoiceno;
				$insert["invoice_status"] = 1;
				$insert["invoice_date"] = date("Y-m-d H:i:s");
				$insert["invoice_vehicle_id"] = $vehicles[0]->vehicle_id;
				$insert["invoice_amount"] = $vehicles[0]->user_payment_amount;
				$insert["invoice_period1"] = date("Y-m-d H:i:s", $vehicles[0]->t_period1);
				$insert["invoice_period2"] = date("Y-m-d H:i:s", $vehicles[0]->t_period2);
				$insert['invoice_print'] = $printed;
				
				$erpdb->insert("invoice", $insert);
				$invoiceid = $erpdb->insert_id();
				
				foreach($vehicles as $vehicle)
				{
					unset($insert);
					
					$insert['invoice_vehicle_invoice'] = $invoiceid;
					$insert['invoice_vehicle_vehicle'] = $vehicle->vehicle_id;
					
					$erpdb->insert("invoice_vehicle", $insert);
				}
				
				unset($insert);
				
				$insert['session_id'] = $sessionid;
				$insert['session_user'] = $vehicles[0]->vehicle_user_id;
				$insert['session_referer'] = "payment";
				
				$this->db->insert("session", $insert);
				
				$mail['sender'] = "billing@lacak-mobil.com";
				$mail['format'] = "html";
				$mail['subject'] = sprintf("Invoice %s", $invoiceno);
				$mail['message'] = $printed;
				
				if ($vehicles[0]->agent_payment_amount == 0)
				{
					$emails = get_valid_emails($vehicles[0]->user_mail));
					if ($emails !== FALSE)
					{
						foreach($emails as $email)
						{
							$mail['dest'][] = $email;
						}
						$mail['bcc'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
					}
					else
					{
						$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
					}
				}
				else
				{
					$this->db->where("user_agent", $vehicles[0]->user_agent);
					$this->db->where("user_type", 3);
					$q = $this->db->get("user");
					if ($q->num_rows() == 0)
					{
						$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
					} 
					else
					{
						$mailagent = "";
						foreach($q->result() as $roagent)
						{
							if (! valid_email($rowagent->user_email)) continue;
							
							if (strlen($mailagent) > 0)
							{
								$mailagent .= ",";
							}
							
							$mailagent .= $rowagent->user_email;
						}
						
						if (strlen($mailagent) == 0) 
						{
							$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
						}
						else
						{
							$mail['dest'] = $mailagent;
							$mail['bcc'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
						}
					}
				}
			
				lacakmobilmail($mail);							
			}
		}
		
	}

	function createflat($nexpired=7, $skipcheckdate=0)
	{
		$erpdb = $this->load->database("erp", TRUE);
		
		$now = mktime()+$nexpired*24*3600;
		
		if (! $skipcheckdate)
		{
			if (date("j", $now) != 1) return;
		}
		
		$erpdb->where("user_payment_type", 2);
		$erpdb->where("user_agent", 1);
		$q = $erpdb->get("user");
		
		if ($q->num_rows() == 0)
		{
			printf("tidak ada user dengan sistem pembayaran flat\n"); 
			return;
		}
				
		$rows = $q->result();		
		
		for($i=0; $i < count($rows); $i++)
		{
			$erpdb->limit(1);
			$erpdb->where("vehicle_status", 1);
			$erpdb->where("vehicle_user_id", $rows[$i]->user_id);
			$q = $erpdb->get("vehicle");
			
			if ($q->num_rows() == 0) continue;
			
			$vehicle = $q->row();
			
			$erpdb->limit(1);
			$erpdb->order_by("invoice_date", "desc");
			$erpdb->join("invoice_vehicle", "invoice_id = invoice_vehicle_invoice");
			$erpdb->where("invoice_vehicle_vehicle", $vehicle->vehicle_id);						
			$q = $erpdb->get("invoice");
			
			if ($q->num_rows()) 
			{
				$rowinvoice = $q->row();
				
				$t_nextexpired = dbmaketime($rowinvoice->invoice_period2);
				if ($now < $t_nextexpired) 
				{
					printf("--- invoice sudah ada\n");
					continue;
				}
				
				$t_period1 = $t_nextexpired;				
			}
			else
			{
				$t_period1 = mktime(0, 0, 0, date('n')+1, 1, date("Y"));
			}
			
			$t_period2 = mktime(0, 0, 0, date("n", $t_period1)+$rows[$i]->user_payment_period, date("j", $t_period1), date("Y", $t_period1));
			
			// langsung create
			
			$sessionid = md5(uniqid());
			$invoiceno = $this->invoicemodel->getInvoiceNo($erpdb);
			
			$params["user"] = $rows[$i];
			$params['invoiceno'] = $invoiceno;
			$params['session'] = $sessionid;
			$params['expiredate1'] = $t_period1;
			$params['expiredate2'] = $t_period2;
			
			$printed = $this->load->view("invoice/invoiceflat", $params, true);						
			
			unset($insert);
			
			$insert["invoice_no"] = $invoiceno;
			$insert["invoice_status"] = 1;
			$insert["invoice_date"] = date("Y-m-d H:i:s");
			$insert["invoice_vehicle_id"] = $vehicle->vehicle_id;
			$insert["invoice_amount"] = $rows[$i]->user_payment_amount;
			$insert["invoice_period1"] = date("Y-m-d H:i:s", $t_period1);
			$insert["invoice_period2"] = date("Y-m-d H:i:s", $t_period2);
			$insert['invoice_print'] = $printed;
			$insert['invoice_archive'] = 1;
			
			$erpdb->insert("invoice", $insert);
			
			unset($insert);
			
			$insert['session_id'] = $sessionid;
			$insert['session_user'] = $vehicle->vehicle_user_id;
			$insert['session_referer'] = "payment";
			
			$this->db->insert("session", $insert);
			
			$mail['sender'] = "billing@lacak-mobil.com";
			$mail['format'] = "html";
			$mail['subject'] = sprintf("Invoice %s", $invoiceno);
			$mail['message'] = $printed;
			
			$emails = get_valid_emails($rows[$i]->user_mail));
			if ($emails !== FALSE)
			{
				foreach($emails as $email)
				{
					$mail['dest'][] = $email;
				}
				$mail['bcc'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
			}
			else
			{
				//$mail['dest'] = "owner@adilahsoft.com";
				$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
			}
			
		
			lacakmobilmail($mail);												
		}
		
		
	}
	
	function index($act="", $id=0)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}				
		
		if (($this->sess->user_type == 2) && ($this->sess->user_payment_type == 0))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("agent_name", "asc");
		$q = $this->db->get("agent");
		
		$this->params['agents'] = $q->result();
		$this->params['title'] = $this->lang->line("linvoice_list");
		$this->params['act'] = $act;
		$this->params['id'] = $id;

		$this->params["content"] = $this->load->view('invoice/search', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
		
	}

	function totalperstatus($status)
	{
		$erpdb = $this->load->database("erp", TRUE);
		
		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);			
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);			
		}
		
		$erpdb->where("invoice_status", $status);
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");		
		$total = $erpdb->count_all_results("invoice");

		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);			
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);			
		}
		
		$erpdb->select_sum("invoice_amount");		
		$erpdb->where("invoice_status", $status);
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");		
		$q = $erpdb->get("invoice");
		
		$totalamount = $q->row()->invoice_amount;

		return sprintf("%d / Rp. %s", $total, number_format($totalamount, 0, "", "."));
	}
	
	function search($offset=0)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}				

		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$agent = isset($_POST['agent']) ? $_POST['agent'] : "";
		
		$periode1 = (isset($_POST['period1']) && $_POST['period1']) ? $_POST['period1'] : "01/01/1900";
		$periode2 = (isset($_POST['period2']) && $_POST['period2']) ? $_POST['period2'] : date("d/m/Y");
		
		$tperiode1 = formmaketime($periode1." 00:00:00");
		$tperiode2 = formmaketime($periode2." 23:59:59");
		
		$erpdb = $this->load->database("erp", TRUE);

	
		$totalnotpaid = $this->totalperstatus(1);		
		$json['totalnotpaid'] = $totalnotpaid;

		$totalprocessed = $this->totalperstatus(2);		
		$json['totalprocessed'] = $totalprocessed;

		$totalpaid = $this->totalperstatus(3);		
		$json['totalpaid'] = $totalpaid;
		
		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);			
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);			
		}
		
		switch($field)
		{
			case "invoice_no":
				$erpdb->where("invoice_no", trim($_POST['keyword']));
			break;
			case "user_login":
				$erpdb->where("user_login LIKE", '%'.trim($_POST['keyword']).'%');
			break;
			case "invoice_status":
				$erpdb->where("invoice_status", $status);
			break;
			case "agent":
				$erpdb->where("user_agent", $agent);
			break;
			case "date":
				$erpdb->where("invoice_date >=", date("Y-m-d H:i:s", $tperiode1));
				$erpdb->where("invoice_date <=", date("Y-m-d H:i:s", $tperiode2));
			break;
		}
		
		$erpdb->where("vehicle_status", 1);
		$erpdb->limit($this->config->item("limit_records"), $offset);
		$erpdb->order_by("invoice_date", "desc");
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");
		$q = $erpdb->get("invoice");
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$invoiceids[] = $rows[$i]->invoice_id;
		}	
		
		if (isset($invoiceids))
		{
			$erpdb->order_by("payment_created", "asc");
			$erpdb->where_in("payment_invoice", $invoiceids);
			$erpdb->join("bank", "payment_accdest = bank_id", "left outer");		
			$q = $erpdb->get("payment");
			
			$rowpayments = $q->result();
			for($i=0; $i < count($rowpayments); $i++)
			{
				$payments[$rowpayments[$i]->payment_invoice][] = $rowpayments[$i];
			}
			
			$erpdb->where_in("invoice_vehicle_invoice", $invoiceids);
			$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_vehicle");
			$q = $erpdb->get("invoice_vehicle");
			$rowvehicles = $q->result();

			for($i=0; $i < count($rowvehicles); $i++)
			{
				$vehicles[$rowvehicles[$i]->invoice_vehicle_invoice][] = $rowvehicles[$i];
			}

		}
		
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->payments = isset($payments[$rows[$i]->invoice_id]) ? $payments[$rows[$i]->invoice_id] : array();
			$rows[$i]->vehicles = isset($vehicles[$rows[$i]->invoice_id]) ? $vehicles[$rows[$i]->invoice_id] : array();
		}

		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);			
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);			
		}		

		switch($field)
		{
			case "invoice_no":
				$erpdb->where("invoice_no", trim($_POST['keyword']));
			break;
			case "user_login":
				$erpdb->where("user_login LIKE", '%'.trim($_POST['keyword']).'%');
			break;
			case "invoice_status":
				$erpdb->where("invoice_status", $status);
			break;
			case "agent":
				$erpdb->where("user_agent", $agent);
			break;
			case "date":
				$erpdb->where("invoice_date >=", date("Y-m-d H:i:s", $tperiode1));
				$erpdb->where("invoice_date <=", date("Y-m-d H:i:s", $tperiode2));
			break;
		}

		$erpdb->where("vehicle_status", 1);
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");
		$total = $erpdb->count_all_results("invoice");

		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);			
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);			
		}		

		switch($field)
		{
			case "invoice_no":
				$erpdb->where("invoice_no", trim($_POST['keyword']));
			break;
			case "user_login":
				$erpdb->where("user_login LIKE", '%'.trim($_POST['keyword']).'%');
			break;
			case "invoice_status":
				$erpdb->where("invoice_status", $status);
			break;
			case "agent":
				$erpdb->where("user_agent", $agent);
			break;
			case "date":
				$erpdb->where("invoice_date >=", date("Y-m-d H:i:s", $tperiode1));
				$erpdb->where("invoice_date <=", date("Y-m-d H:i:s", $tperiode2));
			break;			
		}

		$erpdb->select_sum("invoice_amount");
		$erpdb->where("vehicle_status", 1);
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");
		$q = $erpdb->get("invoice");
		$totalamount = $q->row()->invoice_amount;
				
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();				
		$this->params['offset'] = $offset;
		$this->params['invoices'] = $rows;
		$this->params['totalinvoices'] = $total;
		$this->params['totalamount'] = $totalamount;
		
		$json['html'] = $this->load->view('invoice/list', $this->params, true);
		
		echo json_encode($json);
	}
	
	function bayar($id)
	{
		$this->index("loadconfirmation", $id);
	}
	
	function confirmation()
	{		
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}				

		$erpdb = $this->load->database("erp", TRUE);
		
		$erpdb->where("invoice_no", $_POST['id']);
		$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");
		$erpdb->join("bank", "user_agent = bank_agent", "left outer");				
		$q = $erpdb->get("invoice");
		
		if ($q->num_rows() == 0) 
		{
			redirect(base_url());
			exit;
		}
		
		$rowinvoice = $q->row();
		
		if ($rowinvoice->invoice_archive == 0)
		{				
			$erpdb->where("invoice_no", $_POST['id']);		
			$erpdb->join("invoice_vehicle", "invoice_vehicle_invoice = invoice_id");
			$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_vehicle");
			$erpdb->join("user", "user_id = vehicle_user_id");
			$erpdb->join("bank", "user_agent = bank_agent", "left outer");				
			$q = $erpdb->get("invoice");
		
			if ($q->num_rows() == 0) 
			{
				redirect(base_url());
				exit;
			}
			
			$rows = $q->result();
		}
		else
		{
			$rows = array(0=>$rowinvoice);
		}
		
		

		if (! $rows[0]->bank_id)
		{
			$erpdb->where("bank_agent IS NULL OR bank_agent = 0", NULL);
		}		
		else
		{
			$erpdb->where("bank_id", $rows[0]->bank_id);
		}
		
		$erpdb->order_by("bank_order", "asc");		
		$q = $erpdb->get("bank");
		
		$banks = $q->result();
		
		$params['banks'] = $banks;
		$params['vehicles'] = $rows;
		$callback['html'] = $this->load->view("invoice/confirmation", $params, true);
		$callback['title'] = $this->lang->line("lpayment_confirmation_for")." #".$rows[0]->invoice_no;
		
		echo json_encode($callback);
	}
	
	function saveconfirmation($issms="")
	{
		$erpdb = $this->load->database("erp", TRUE);
		
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

		$erpdb->where("invoice_status <>", 4);
		$erpdb->where("invoice_id", $id);
		$q = $erpdb->get("invoice");
		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Access denied";
			
			echo json_encode($callback);
			return;
		}
		
		$rowinvoice = $q->row();
		if ($rowinvoice->invoice_status == 3)
		{
			$callback['error'] = true;
			$callback['message'] = "Invoice telah dibayar";
			
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
		
		if (! $issms)		
		{
			if ($this->sess->user_type == 2)
			{
				$erpdb->where("user_id", $this->sess->user_id);
			}
		}
		$erpdb->where("invoice_id", $_POST['id']);
		$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");
		$q = $erpdb->get("invoice");
				
		if ($q->num_rows() == 0) 
		{
			redirect(base_url());
			exit;
		}
		
		$row = $q->row();				
		$params['vehicle'] = $row;
		
		if ($bankdest)
		{		
			$erpdb->where("bank_id", $bankdest);
			$q = $erpdb->get("bank");
			
			if ($q->num_rows() == 0) 
			{
				redirect(base_url());
				exit;
			}
			
			$row = $q->row();				
			$params['bank'] = $row;
		}
				
		$params['post'] = $_POST;
					
		$mail['sender'] = $params['vehicle']->user_login;
		$mail['format'] = "html";
		if ($issms)
		{
			$mail['subject'] = sprintf("[SMS Server] payment confirmation for invoice #%s", $params['vehicle']->invoice_no);
		}
		else
		{
			$mail['subject'] = sprintf("payment confirmation for invoice #%s", $params['vehicle']->invoice_no);
		}
		$mail['message'] = $this->load->view("invoice/mailconfirmation", $params, true);
		$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
		//$mail['dest'] = "owner@adilahsoft.com"; 
		
		lacakmobilmail($mail);
		
		
		unset($update);
		
		$update['invoice_status'] = 2;
		
		$erpdb->where("invoice_id", $id);
		$erpdb->update('invoice', $update);
		
		unset($insert);
		
		$insert['payment_invoice'] = $id;
		$insert['payment_method'] = $transfermethod;
		$insert['payment_accdest'] = $bankdest;
		$insert['payment_amount'] = $amount;
		$insert['payment_date'] = date("Y-m-d H:i:s", $paymenttime);
		$insert['payment_transfer_code'] = $transfercode;
		$insert['payment_name'] = $sendername;
		if ($issms)
		{
			$insert['payment_creator'] = 0;
		}
		else
		{
			$insert['payment_creator'] = $this->sess->user_id;
		}
		$insert['payment_created'] = date("Y-m-d H:i:s");
		$insert['payment_status'] = 1;
		
		$erpdb->insert('payment', $insert);		
		
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lpayment_confirmation_success");
		
		echo json_encode($callback);
		return;		
	}
	
	function show($id)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}				

		$erpdb = $this->load->database("erp", TRUE);
		
		if ($this->sess->user_type == 2)
		{
			$erpdb->where("vehicle_user_id", $this->sess->user_id);
		}
		
		$erpdb->where("invoice_id", $id);
		$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_id");
		$q = $erpdb->get("invoice");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$row = $q->row();
		
		$erpdb->where("payment_status <>", 3);
		$erpdb->order_by("payment_date", "asc");
		$erpdb->where("payment_invoice", $row->invoice_id);
		$q = $erpdb->get("payment");
		
		$rows = $q->result();
		
		$params["payments"] = $rows;
		
		$payment = $this->load->view("invoice/payment", $params, true);		
		echo str_replace("<!-- transaction-list -->", $payment, $row->invoice_print);
		
	}
	
	function changestatus()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}				

		if (($this->sess->user_type != 1) && ($this->sess->user_type != 4))
		{
			redirect(base_url());
		}				

		$erpdb = $this->load->database("erp", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : "";
		
		if (! $id)
		{
			redirect(base_url());
		}
		
		$erpdb->where("payment_id", $id);
		$erpdb->join("invoice", "payment_invoice = invoice_id");
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "vehicle_user_id = user_id");
		$q = $erpdb->get("payment");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$rowpayment = $q->row();
		
		if ($_POST['status'] == 3)
		{
			unset($update);
			
			$update['payment_status'] = 3;
			$erpdb->where("payment_id", $id);
			$erpdb->update("payment", $update);						
			
			//cek apakah ada payment yg new
			
			$erpdb->where("payment_status", 1);
			$erpdb->where("payment_invoice", $rowpayment->payment_invoice);
			$total = $erpdb->count_all_results("payment");
			
			if ($total == 0)
			{
				// jika tidak ada konfirmasi lain, ubah status invoice dari prossed menjadi new kembali
				unset($update);			
				$update['invoice_status'] = 1;
				
				$erpdb->where("invoice_id", $rowpayment->payment_invoice);
				$erpdb->update("invoice", $update);
			}

			$json['error'] = false;
			echo json_encode($json);
			
			return;

		}

		// jika approve maka semua konfirmasi pada invoice yg sama menjadi approve

		unset($update);
		
		$update['payment_status'] = 2;
		
		$erpdb->where("payment_status", 1);
		$erpdb->where("payment_invoice", $rowpayment->payment_invoice);
		$erpdb->update("payment", $update);						
		
		unset($update);			
		$update['invoice_status'] = 3;
		
		$erpdb->where("invoice_id", $rowpayment->payment_invoice);
		$erpdb->update("invoice", $update);			
		
		$erpdb->where("invoice_vehicle_invoice", $rowpayment->payment_invoice);
		$q = $erpdb->get("invoice_vehicle");
		$rowvehicles = $q->result();

		$vehicleids[] = 0;
		for($i=0; $i < count($rowvehicles); $i++)
		{
			$vehicleids[] = $rowvehicles[$i]->invoice_vehicle_vehicle;
		}

		// cek apakah ada invoice yang pending untuk kendaraan yang akan diperpanjang
					
		$erpdb->where("((invoice_status = 1) or (invoice_status = 2))", null);
		$erpdb->where_in("invoice_vehicle_id", $vehicleids);
		$total = $erpdb->count_all_results("invoice");
		
		if ($total > 0)
		{
			$json['error'] = false;
			echo json_encode($json);		
			return;
		}
		
		// jika tidak ada invoice yang pending, perpanjang kendaraan
		
		if ($rowpayment->invoice_archive == 0)
		{		
			unset($update);
			
			$t_nextexpired = dbmaketime($rowpayment->invoice_period2);
			
			$update['vehicle_active_date2'] = date("Ymd", $t_nextexpired);
			
			$this->db->where_in("vehicle_id", $vehicleids);
			$this->db->update("vehicle", $update);		
		}
		
		// mail 
		
		$params['payment'] = $rowpayment;		
		$message = $this->load->view("invoice/mailpaid", $params, true);
		
		unset($mail);
		
		$mail['sender'] = "billing@lacak-mobil.com";
		$mail['format'] = "html";
		$mail['subject'] = sprintf("Resi pembayaran invoice# %s", $rowpayment->invoice_no);
		$mail['message'] = $message;
		
		$emails = get_valid_emails($rowpayment->user_mail));
		if ($emails !== FALSE)
		{
			foreach($emails as $email)
			{
				$mail['dest'][] = $email;
			}
			$mail['bcc'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
		}
		else
		{
			$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
		}
		
	
		lacakmobilmail($mail);							
		

		$json['error'] = false;
		echo json_encode($json);		
	}
	
	function getTotal()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}	
		
		$erpdb = $this->load->database("erp", TRUE);
		
		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);
		}		
				
		$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_id");		
		$erpdb->join("user", "vehicle_user_id = user_id");
		$totalinvoice = $erpdb->count_all_results("invoice");		
		
		
		if ($this->sess->user_type == 2)
		{
			$erpdb->where("user_id", $this->sess->user_id);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$erpdb->where("user_agent", $this->sess->user_agent);
		}			
		
		if (($this->sess->user_type == 1) || ($this->sess->user_type == 4))
		{
			$erpdb->where("invoice_status", 2);
		}
		else
		{
			$erpdb->where("invoice_status", 1);
		}
		
		$erpdb->join("vehicle", "vehicle_id = invoice_vehicle_id");		
		$erpdb->join("user", "vehicle_user_id = user_id");
		$total = $erpdb->count_all_results("invoice");		
		
		if ($this->sess->user_type == 4) 
		{
			$json['html'] = sprintf("<a href=\"%sinvoice\">%s (%d/%d)</a>", base_url(), $this->lang->line("linvoice"), $total, $totalinvoice);
		}
		else 
		{
			$json['html'] = sprintf("<a href=\"%sinvoice\">%s (%d/%d)</a>", base_url(), $this->lang->line("linvoice"), $total, $totalinvoice);
		}
		$json['total'] = $total;
		echo json_encode($json);
	}
	
	function getinfo()
	{
		$invoiceno = isset($_POST['invoiceno']) ? $_POST['invoiceno'] : "";
		
		$erpdb = $this->load->database("erp", TRUE);
		
		$erpdb->where("invoice_no", $invoiceno);
		$q = $erpdb->get("invoice");
		
		if ($q->num_rows() == 0) return;
		
		$row = $q->row();
		
		$json['invoice'] = $row;
		echo json_encode($json);
	}
	
	function approved()
	{
		$referer = isset($_POST['referer']) ? $_POST['referer'] : "";
		
		if ($referer != "BOSS") return;

		$erpdb = $this->load->database("erp", TRUE);
		
		$invoiceid = isset($_POST['invoiceid']) ? $_POST['invoiceid'] : "";		
		if (! $invoiceid) return;
		
		$erpdb->where("invoice_status <", 3);
		$erpdb->where("invoice_id", $invoiceid);
		$erpdb->join("vehicle", "invoice_vehicle_id = vehicle_id");
		$erpdb->join("user", "user_id = vehicle_user_id");
		$q = $erpdb->get("invoice");
		
		if ($q->num_rows() == 0) return;
		
		$row = $q->row();
		
		unset($update);
		
		$update['payment_status'] = 2;
		
		$erpdb->where("payment_status", 1);
		$erpdb->where("payment_invoice", $row->invoice_id);
		$erpdb->update("payment", $update);						
		
		unset($update);			
		$update['invoice_status'] = 3;
		
		$erpdb->where("invoice_id", $row->invoice_id);
		$erpdb->update("invoice", $update);			
		
		$erpdb->where("invoice_vehicle_invoice", $row->invoice_id);
		$q = $erpdb->get("invoice_vehicle");
		$rowvehicles = $q->result();

		$vehicleids[] = 0;
		for($i=0; $i < count($rowvehicles); $i++)
		{
			$vehicleids[] = $rowvehicles[$i]->invoice_vehicle_vehicle;
		}
		
		// jika tidak ada invoice yang pending, perpanjang kendaraan
		
		if ($row->invoice_archive == 0)
		{		
			unset($update);
			
			$t_nextexpired = dbmaketime($row->invoice_period2);
			
			$update['vehicle_active_date2'] = date("Ymd", $t_nextexpired);
			
			$this->db->where_in("vehicle_id", $vehicleids);
			$this->db->update("vehicle", $update);		
		}
		
		// mail 
		
		$row->payment_amount = $row->invoice_amount;
		
		$params['payment'] = $row;		
		$message = $this->load->view("invoice/mailpaid", $params, true);
		
		unset($mail);
		
		$mail['sender'] = "billing@lacak-mobil.com";
		$mail['format'] = "html";
		$mail['subject'] = sprintf("Resi pembayaran invoice# %s", $row->invoice_no);
		$mail['message'] = $message;
		
		$emails = get_valid_emails($row->user_mail));
		foreach($emails as $email)
		{
			$mail['dest'][] = $email;
			$mail['bcc'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
		}
		else
		{
			$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com"; 
		}
		
		lacakmobilmail($mail);							
		
		$json['error'] = false;
		echo json_encode($json);		
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
