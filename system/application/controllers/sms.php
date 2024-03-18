<?php
include "base.php";

class SMS extends Base {
	var $configs;

	function SMS()
	{
		parent::Base();	
		$this->load->model("configmodel");
		$this->load->model("smsmodel");
		$this->load->model("gpsmodel");		
		$this->load->model("agenmodel");
		
		$this->load->helper('file');
		
		$configs = $this->configmodel->get();
		$this->params['configs'] = $configs;
	}
	
	function index()
	{
		$this->checksess();
		$this->home();
	}
	
	function checksess()
	{
		if (! isset($this->sess))
		{
			redirect(base_url()."sms/login");
		}
		
		if (! $this->sess)
		{
			redirect(base_url()."sms/login");
		}		
	}
	
	function welcome()
	{
		if (! $this->config->item("SMS_WELCOME")) return;		
		if (! isset($_POST['userid'])) return;
		
		$userid = $_POST['userid'];
				
		$this->db->where("user_id", $userid);		
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0) return;
		
		$row = $q->row();
		
		if ($row->user_type == 2)
		{
		
			$this->db->limit(1, 0);
			$this->db->where("user_id", $row->user_id);	
			$this->db->join("vehicle", "vehicle_user_id = user_id");
			$q = $this->db->get("user");
			
			if ($q->num_rows() == 0) return;
		}
		else
		if ($row->user_type == 3)
		{
			$this->db->limit(1, 0);
			$this->db->where("user_agent", $row->user_agent);	
			$this->db->join("vehicle", "vehicle_user_id = user_id");
			$q = $this->db->get("user");
			
			if ($q->num_rows() == 0) return;
		}
		else
		{
			$this->db->limit(1, 0);
			$this->db->join("vehicle", "vehicle_user_id = user_id");
			$q = $this->db->get("user");
			
			if ($q->num_rows() == 0) return;
		}
		
		$row = $q->row();
		$this->smsmodel->welcome($row);
	}
	
	function newvehicle($userid, $no)
	{
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
		if ($q->num_rows() == 0)
		{
			echo "user is not found";
			return;
		}
		
		$row = $q->row();
		
		$hp = valid_mobile($row->user_mobile);
		
		$this->params['dest'] = array();
		if ($hp)
		{
			$this->params['dest'][] = $hp;
		}		
		
		$this->db->where("agent_id", $row->user_agent);
		$this->db->where("user_type", 3);
		$this->db->join("user", "user_agent = agent_id");
		$q = $this->db->get("agent");
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$hp = valid_mobile($rows[$i]->user_mobile);	
			if (! $hp) continue;
			
			$this->params['dest'][] = $hp;
		}
		
		if ($row->user_agent == 3)
		{
			$this->params['dest'] = array_merge($this->params['dest'], $this->config->item("SMS_GPSANDALAS"));
		}
		else
		{
			$this->params['dest'] = array_merge($this->params['dest'], $this->config->item("SMS_LACAKMOBIL"));
		}
		
		$message = sprintf($this->config->item("SMS_NEW_VEHICLE"), $this->agenmodel->getLicense($row), $row->user_login, $no);
		
		$this->params['dest'] = array_unique($this->params['dest']);
		$this->params['content'] = $message;
		$xml = $this->load->view("sms/send", $this->params, true);

		echo $this->sendsms($xml);
	}

	function test()
	{
		$this->params['dest'] = array("085717019778");
		$this->params['content'] = "test";
		$xml = $this->load->view("sms/send", $this->params, true);

		$this->sendsms($xml);
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url()."sms/login");
	}
	
	function login($code="")
	{
		$this->params['error'] = strlen($code) > 0;
		$this->params['errormsg'] = $this->smsmodel->getMessage($code);
		$this->params['content'] = $this->load->view("sms/login", $this->params, true);
		
		$this->load->view("sms/template", $this->params);
	}
	
	function dologin()
	{
		$username = isset($_POST['username']) ? trim($_POST['username']) : "";
		$userpass = isset($_POST['userpass']) ? $_POST['userpass'] : "";

		if (strlen($username) == 0)
		{
			redirect(base_url()."sms/login/ue");
		}

		if (strlen($userpass) == 0)
		{
			redirect(base_url()."sms/login/pe");
		}
		
		$this->db->where("user_login", $username);
		$this->db->where("user_pass = PASSWORD('".$userpass."')", NULL, FALSE);
		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url()."sms/login/il");
		}
		
		$row = $q->row();
		if ($row->user_type == 1)
		{
			if ($row->user_login != "owner")
			{
				redirect(base_url()."sms/login/il");
			}
		}
		else
		if ($row->user_type == 2)
		{
			if ($row->agent_pascabayar == 1)
			{
				redirect(base_url()."sms/login/il");
			}			
		}
		
		unset($row->agent_sms_1_expired);
		unset($row->agent_sms_n_expired);
		
		$this->session->set_userdata($this->config->item('session_name'), serialize($row));
		redirect(base_url()."sms/home");
	}
	
	function home()
	{		
		$this->checksess();				

		$this->params['title'] = $this->lang->line("lhome");
		$this->params['content'] = $this->load->view("sms/home", $this->params, true);
		$this->load->view("sms/template", $this->params);		
	}
	
	function deposit($flag="", $arg="")
	{		
		$this->checksess();

		$this->db->where("bank_agent", 14);
		$q = $this->db->get("bank");
		$this->params['banks'] = $q->result();

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->agent_id);
		}
		
		$this->db->where("agent_pascabayar <>", 1);
		$this->db->where("user_type", 2);
		$this->db->order_by("user_name", "asc");
		$this->db->join("agent", "user_agent = agent_id");
		$q = $this->db->get("user");			
		$this->params['users'] = $q->result();	
		
		$this->params['flag'] = $flag;
		$this->params['arg'] = $arg;
		$this->params['title'] = $this->lang->line("ldeposit");
		$this->params['content'] = $this->load->view("sms/deposit", $this->params, true);
		$this->load->view("sms/template", $this->params);					
	}	
	
	function savedeposit()
	{
		$this->checksess();
		
		$user = isset($_POST['user']) ? $_POST['user'] : 0;
		$transfermethod = isset($_POST['transfermethod']) ? $_POST['transfermethod'] : 0;
		$bankdest = isset($_POST['bankdest']) ? $_POST['bankdest'] : 0;
		$amount = isset($_POST['amount']) ? trim($_POST['amount']) : "";
		$paymentdate = isset($_POST['paymentdate']) ? trim($_POST['paymentdate']) : "";
		$transfercode = isset($_POST['transfercode']) ? trim($_POST['transfercode']) : "";
		$sendername = isset($_POST['sendername']) ? trim($_POST['sendername']) : "";
		
		$amount = str_replace(".", "", $amount);
		$amount = str_replace("Rp", "", $amount);
		$amount = str_replace("RP", "", $amount);
		$amount = str_replace("rp", "", $amount);		
		$amount = trim($amount);
		
		$tpaymentdate = formmaketime($paymentdate." 00:00:00");
						
		if (! $bankdest)
		{
			redirect(base_url()."sms/deposit/e/eba");
		}
		
		if ($amount == "")
		{
			redirect(base_url()."sms/deposit/e/eam");
		}		

		if (! is_numeric($amount))
		{
			redirect(base_url()."sms/deposit/e/iam");
		}

		if (date("d/m/Y", $tpaymentdate) != $paymentdate)
		{
			redirect(base_url()."sms/deposit/e/epd");
		}
		
		if ($transfermethod == "cash")
		{
			if ($transfercode == "")
			{
				redirect(base_url()."sms/deposit/e/eca");
			}
		}
		else
		if ($transfercode != "0000")
		{
			redirect(base_url()."sms/deposit/e/enca");
		}
		
		if ($transfermethod == "cash")
		{
			if (strcasecmp($sendername, "TUNAI"))
			{
				redirect(base_url()."sms/deposit/e/ese");
			}
		}
		else
		if ($sendername == "")
		{
			redirect(base_url()."sms/deposit/e/ese");
		}
		
		unset($insert);
		
		if ($this->sess->user_type == 1)
		{
			$insert['smspayment_user'] = $user;
			$insert['smspayment_agent'] = 0;
		}
		else
		if ($this->sess->user_type == 2)
		{
			$insert['smspayment_user'] = $this->sess->user_id;
			$insert['smspayment_agent'] = 0;			
		}
		else
		if ($this->sess->agent_pascabayar == 1)
		{
			$insert['smspayment_user'] = 0;
			$insert['smspayment_agent'] = $this->sess->user_agent;						
		}
		else
		{
			$insert['smspayment_user'] = $this->sess->user_id;
			$insert['smspayment_agent'] = 0;			
		}
		
		$insert['smspayment_creator'] = $this->sess->user_id;
		$insert['smspayment_method'] = $transfermethod;
		$insert['smspayment_bank'] = $bankdest;
		$insert['smspayment_amount'] = $amount;
		$insert['smspayment_date'] = date("Y-m-d H:i:s", $tpaymentdate);
		$insert['smspayment_validation'] = $transfercode;
		$insert['smspayment_name'] = $sendername;
		$insert['smspayment_created'] = date("Y-m-d H:i:s");
		$insert['smspayment_status'] = 1;
		
		$this->db->insert("smspayment", $insert);
		
		$body = "name: ".$this->sess->user_name;
		$body .= "<br />\r\n"."ID: ".$this->db->insert_id();
		
		foreach($insert as $key=>$val)
		{
			$body .= "<br />\r\n".$key."=".$val;
		}
		
		maillocalhost("[sms] konfirmasi deposit", $body, "support@adilahsoft.com");
		
		redirect(base_url()."sms/sdeposit/");
	}
	
	function sdeposit()
	{
		$this->checksess();
		
		$this->params['message'] = $this->lang->line("lsms_iuran_success");
		$this->params['title'] = $this->lang->line("ldeposit");
		$this->params['content'] = $this->load->view("sms/message", $this->params, true);
		$this->load->view("sms/template", $this->params);
	}
	
	function help($back="")
	{
		$this->params['showback'] = $back;
		$this->params['title'] = $this->lang->line("lhelp");
		$this->params['content'] = $this->load->view("sms/rule", $this->params, true);
		$this->load->view("sms/template", $this->params);					
	}
	
	
	function balance($offset=0)
	{
		$this->checksess();
		
		$limit = 10;
		
		if ($this->sess->user_type == 2)
		{
			$this->db->where("smsbalance_user", $this->sess->user_id);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("smsbalance_agent", $this->sess->user_agent);
		}
		
		$this->db->order_by("smsbalance_created", "desc");
		$this->db->limit($limit, $offset);
		$q = $this->db->get("smsbalance");
		$rows = $q->result();
		
		
		if ($this->sess->user_type == 2)
		{
			$this->db->where("smsbalance_user", $this->sess->user_id);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("smsbalance_agent", $this->sess->user_agent);
		}
		
		$total = $this->db->count_all_results("smsbalance");
		
		$this->load->library('pagination');
		
		$config['base_url'] = base_url()."sms/balance";
		$config['total_rows'] = $total;
		$config['per_page'] = $limit; 
		
		$this->pagination->initialize($config); 
		
		$this->params['paging'] = $this->pagination->create_links();
		$this->params['offset'] = $offset;
		$this->params['rows'] = $rows;
		$this->params['title'] = $this->lang->line("lbalance");
		$this->params['content'] = $this->load->view("sms/balance1", $this->params, true);
		$this->load->view("sms/template", $this->params);
	}
		
	function payment($offset=0)
	{
		$this->checksess();
		
		$limit = 10;
		
		$where = "";
		if ($this->sess->user_type == 2)
		{
			$sql1 = "SELECT t1.*, t3.*, t2.user_name kreditor
					FROM 				".$this->db->dbprefix."smspayment t1
							INNER JOIN 	".$this->db->dbprefix."user t2 ON user_id = smspayment_user 
							INNER JOIN	".$this->db->dbprefix."bank t3 ON bank_id = smspayment_bank 	
					WHERE	user_id = ".$this->sess->user_id."		
			";			
		}
		else
		if ($this->sess->user_type == 3)
		{
			$sql1 = "SELECT t1.*, t3.*, t2.agent_name kreditor
					FROM 				".$this->db->dbprefix."smspayment t1
							INNER JOIN 	".$this->db->dbprefix."agent t2 ON agent_id = smspayment_agent 
							INNER JOIN	".$this->db->dbprefix."bank t3 ON bank_id = smspayment_bank 
					WHERE 	agent_id = ".$this->sess->agent_id."
					UNION
					SELECT t1.*, t3.*, t2.user_name kreditor
					FROM 				".$this->db->dbprefix."smspayment t1
							INNER JOIN 	".$this->db->dbprefix."user t2 ON user_id = smspayment_user 
							INNER JOIN	".$this->db->dbprefix."bank t3 ON bank_id = smspayment_bank 			
					WHERE 	user_id = ".$this->sess->user_id."
			";			
		}
		else
		{
			$sql1 = "SELECT t1.*, t3.*, t2.agent_name kreditor
					FROM 				".$this->db->dbprefix."smspayment t1
							INNER JOIN 	".$this->db->dbprefix."agent t2 ON agent_id = smspayment_agent 
							INNER JOIN	".$this->db->dbprefix."bank t3 ON bank_id = smspayment_bank 
					UNION
					SELECT t1.*, t3.*, t2.user_name kreditor
					FROM 				".$this->db->dbprefix."smspayment t1
							INNER JOIN 	".$this->db->dbprefix."user t2 ON user_id = smspayment_user 
							INNER JOIN	".$this->db->dbprefix."bank t3 ON bank_id = smspayment_bank 			
			";			
		}		
		
		$sql = "SELECT * FROM ( ".$sql1." ) t4 ORDER BY t4.smspayment_date DESC LIMIT ".$limit." OFFSET ".$offset;					
		
		$q = $this->db->query($sql);
		$rows = $q->result();
				
		$sql = "SELECT COUNT(*) tot FROM ( ".$sql1." ) t4 ";
		$q = $this->db->query($sql);		
		$row = $q->row();
		
		$total = $row->tot;
		
		$this->load->library('pagination');
		
		$config['base_url'] = base_url()."sms/balance";
		$config['total_rows'] = $total;
		$config['per_page'] = $limit; 
		
		$this->pagination->initialize($config); 
		
		$this->params['paging'] = $this->pagination->create_links();
		$this->params['offset'] = $offset;
		$this->params['rows'] = $rows;
		$this->params['title'] = $this->lang->line("lreport_payment");
		$this->params['content'] = $this->load->view("sms/balance", $this->params, true);
		$this->load->view("sms/template", $this->params);
	}
	
	function sendall($flag="", $arg="")
	{
		$this->db->order_by("agent_name", "asc");
		$q = $this->db->get("agent");
		
		$this->params['agents'] = $q->result();
		$this->params['flag'] = $flag;
		$this->params['arg'] = $arg;
		
		$this->params['title'] = $this->lang->line("lsendallusers");
		$this->params['content'] = $this->load->view("sms/sendall", $this->params, true);
		$this->load->view("sms/template", $this->params);	
	}
	
	function saveautorefill()
	{
		$vehicles = isset($_POST['vehicle']) ? $_POST['vehicle'] : "";
		$centangsemua == isset($_POST['centangsemua']) ? $_POST['centangsemua'] : 0;
		$scentangsemua = $centangsemua ? "centangsemua" : "no";
		
		if (! is_array($vehicles))
		{
			redirect(base_url()."sms/autorefill/".$scentangsemua."/e/ve");
		}
		
		// uncheck dulu

		unset($update);	
		$update['vehicle_autorefill'] = 0;
		
		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->update("vehicle", $update);
			$this->db->cache_delete_all();
		}
		else
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
			
			$this->db->where_in("vehicle_user_id", $users);
			$this->db->update("vehicle", $update);
			$this->db->cache_delete_all();
		}
		else
		{
			$this->db->update("vehicle", $update);
			$this->db->cache_delete_all();
		}
		
		
		// check semua yang terdefinisi
		
		if (count($vehicles) == 0)
		{
			redirect(base_url()."sms/autorefill/".$scentangsemua."/s");
		}

		unset($update);	
		$update['vehicle_autorefill'] = 1;
		
		$this->db->where_in("vehicle_id", $vehicles);
		$this->db->update("vehicle", $update);
		
		$this->db->cache_delete_all();
				
		redirect(base_url()."sms/autorefill/".$scentangsemua."/s");
	}
	
	function autorefill($centangall=false, $flag="", $arg="")
	{
		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->agent_id);
			$this->db->join("user", "user_id = vehicle_user_id");
		}
		
		$this->db->order_by("vehicle_no", "asc");
		$q = $this->db->get("vehicle");
		
		$this->params['vehicles'] = $q->result();
		$this->params['centangall'] = $centangall == "centangsemua";
		$this->params['flag'] = $flag;
		$this->params['arg'] = $arg;

		$this->params['title'] = $this->lang->line("lauto_refill");
		$this->params['content'] = $this->load->view("sms/autorefill", $this->params, true);
		$this->load->view("sms/template", $this->params);		
	}
	
	function paymentcancel($id)
	{
		$this->checksess();
		
		if ($this->sess->user_type != 1)
		{
			redirect(base_url());
		}
		
		unset($update);
		$update['smspayment_status'] = 3;
		$update['smspayment_cancelled_user'] = $this->sess->user_id;
		$update['smspayment_cancelled_time'] = date("Y-m-d H:i:s");
		
		$this->db->where("smspayment_id", $id);
		$this->db->update("smspayment", $update);
		
		$this->db->cache_delete_all();
		
		redirect(base_url()."sms/balance");
	}
	
	function paymentapprove($id)
	{
		$this->checksess();
		
		if ($this->sess->user_type != 1)
		{
			redirect(base_url());
		}
		
		$this->db->where("smspayment_id", $id);
		$q = $this->db->get("smspayment");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$row = $q->row();
		
		if ($row->smspayment_status != 1)
		{
			redirect(base_url());
		}
		
		unset($update);
		$update['smspayment_status'] = 2;
		$update['smspayment_approved_user'] = $this->sess->user_id;
		$update['smspayment_approved_time'] = date("Y-m-d H:i:s");
		
		$this->db->where("smspayment_id", $id);
		$this->db->update("smspayment", $update);
		
		$this->db->cache_delete_all();
		
		// add to balance
		
		unset($insert);
		
		if ($row->smspayment_user)
		{
			$insert['smsbalance_user'] = $row->smspayment_user;
			$insert['smsbalance_agent'] = 0;
			$insert['smsbalance_saldo'] = $this->smsmodel->getSaldoUser($row->smspayment_agent)+$row->smspayment_amount;
		}
		else
		{
			$insert['smsbalance_agent'] = $row->smspayment_agent;
			$insert['smsbalance_user'] = 0;
			$insert['smsbalance_saldo'] = $this->smsmodel->getSaldoAgent($row->smspayment_agent)+$row->smspayment_amount;
		}
		
		$insert['smsbalance_debet'] = 0;
		$insert['smsbalance_kredit'] = $row->smspayment_amount;		
		$insert['smsbalance_desc'] = "deposit";
		$insert['smsbalance_created'] = date("Y-m-d H:i:s");
		$insert['smsbalance_creator'] = $this->sess->user_id;
		
		$this->db->insert("smsbalance", $insert);
		
		redirect(base_url()."sms/payment");
	}	
	
	function contactus($flag="", $arg="")
	{
		$this->params['flag'] = $flag;
		$this->params['arg'] = $arg;		
		$this->params['title'] = $this->lang->line("lcontactus");
		$this->params['content'] = $this->load->view("sms/contactus", $this->params, true);
		$this->load->view("sms/template", $this->params);									
	}
	
	function savecontactus()
	{
		$msg = isset($_POST['msg']) ? trim($_POST['msg']) : "";
		
		if (! $msg)
		{
			redirect(base_url()."sms/contactus/e/co");
		}
		
		maillocalhost("[sms] contact us", $msg, "support@adilahsoft.com");
		redirect(base_url()."sms/contactus/s");				
	}
	
	function status()
	{
		if ($this->sess->user_type != 1)
		{
			$this->db->where("agent_id", $this->sess->user_agent);
		}
		
		$this->db->order_by("agent_name", "asc");
		$q = $this->db->get("agent");
			
		$this->params["rows"] = $q->result();
		$this->params['title'] = $this->lang->line("lstatus");
		$this->params['content'] = $this->load->view("sms/status", $this->params, true);
		$this->load->view("sms/template", $this->params);							
	}
	
	function report()
	{
	}
	
	function send()
	{
		$this->db->join("user", "user_agent = agent_id");
		$this->db->join("vehicle", "vehicle_user_id = user_id");
		$q = $this->db->get("agent");		
				
		if ($q->num_rows() == 0)
		{
			$this->appendlog("tidak ada agent yang mengaktifkan feature sms");
			return;
		}
		
		$rows = $q->result();		
		for($i=0; $i < count($rows); $i++)
		{
			if (isset($sms[$rows[$i]->vehicle_id])) continue;
			if (! $rows[$i]->user_mobile) 
			{
				$this->appendlog("no hp tidak terdefinisi: ".$rows[$i]->user_name);
				continue;
			}
			
			// ambil posisi terakhir
			
			$this->send1($rows[$i], array($rows[$i]->user_mobile));
			
			sleep(30);
			exit;
		}
	}
	
	function send2($vehicle, $km, $no)
	{
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) <= 1) return;
		
		$update['vehicle_maxspeed'] = $km;
		$this->db->where("vehicle_id", $vehicle->vehicle_id);
		$this->db->update("vehicle", $update);
		
		$this->db->cache_delete_all();
		
		$this->params['dest'] = $no;
		$this->params['content'] = sprintf("Setting alert maksimum kecepatan %s telah berhasil. Terima kasih.", $vehicle->vehicle_no);
		$xml = $this->load->view("sms/send", $this->params, true);

		return $this->sendsms($xml);
		
	}

	function send3($vehicle, $park, $no)
	{
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) <= 1) return;
		
		$update['vehicle_maxparking'] = $park;
		$this->db->where("vehicle_id", $vehicle->vehicle_id);
		$this->db->update("vehicle", $update);
		
		$this->db->cache_delete_all();
		
		$this->params['dest'] = $no;
		$this->params['content'] = sprintf("Setting alert maksimum lama parkir %s telah berhasil. Terima kasih.", $vehicle->vehicle_no);
		$xml = $this->load->view("sms/send", $this->params, true);

		return $this->sendsms($xml);
		
	}
	
	function send1($vehicle, $no)
	{		
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) <= 1) return;

		$gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, $vehicle->vehicle_type);					
		if (! $gps)
		{
			$format = sprintf("%s belum aktif. Silahkan hub agen Anda.", $vehicle->vehicle_no);
		}
		else
		{		
			$gtps = $this->config->item("vehicle_gtp");
			$gtpdoors = $this->config->item("vehicle_gtp_door");
			if (in_array(strtoupper($vehicle->vehicle_type), $gtps))
			{
				$uniqid = uniqid();
				$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);			
				$this->db->order_by("gps_info_time", "DESC");
				$this->db->where("gps_info_device", $vehicle->vehicle_device);
				$q = $this->db->get($this->gpsmodel->getGPSInfoTable($vehicle->vehicle_type), 1, 0);
				
				if ($q->num_rows() == 0)
				{
					$engine = "OFF";
					$door = "CLOSED";
				}
				else
				{
					$rowinfo = $q->row();					
					$ioport = $rowinfo->gps_info_io_port;
					
					$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
					$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
					$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
					
					$engine = $status1 ? "ON" : "OFF";
					$door = $status3 ? "OPENED" : "CLOSED";
				}
				
				if (in_array(strtoupper($vehicle->vehicle_type), $gtpdoors))
				{
					$format = sprintf("%s\n%s\n%s\n%s %s\n%s\n%s\nEng:%s Door:%s", $vehicle->vehicle_no, date("d/m/Y H:i", $gps->gps_timestamp), $gps->georeverse->display_name, $gps->gps_latitude_real_fmt, $gps->gps_longitude_real_fmt, $gps->gps_speed_fmt."kph", ($gps->gps_status == "A") ? "OK" : "NO", $engine, $door);
				}
				else
				{
					$format = sprintf("%s\n%s\n%s\n%s %s\n%s\n%s\nEng:%s", $vehicle->vehicle_no, date("d/m/Y H:i", $gps->gps_timestamp), $gps->georeverse->display_name, $gps->gps_latitude_real_fmt, $gps->gps_longitude_real_fmt, $gps->gps_speed_fmt."kph", ($gps->gps_status == "A") ? "OK" : "NO", $engine);
				}
			}
			else
			{
				$format = sprintf("%s\n%s\n%s\n%s %s\n%s\n%s", $vehicle->vehicle_no, date("d/m/Y H:i", $gps->gps_timestamp), $gps->georeverse->display_name, $gps->gps_latitude_real_fmt, $gps->gps_longitude_real_fmt, $gps->gps_speed_fmt."kph", ($gps->gps_status == "A") ? "OK" : "NO");
			}
		}
				
		$this->params['dest'] = $no;
		$this->params['content'] = $format;
		$xml = $this->load->view("sms/send", $this->params, true);

		return $this->sendsms($xml);
	}
	
	function sendsms($xml)
	{
		$smsserver = $this->smsmodel->getSMSServer();
		switch($smsserver)
		{
			case "mondial":
				return $this->sendsmsmondial($xml);
			break;
			default:
				return $this->sendsmslocalhost($xml);
		}
	}
	
	function sendsmslocalhost($xml)
	{
		$xmls = explode("\1", $xml);
		
		$smsdb = $this->load->database("smscolo", TRUE);
		
		foreach(explode("|", $xmls[0]) as $hp)
		{
			unset($insert);
			
			$insert["SenderNumber"] = $hp;
			$insert["ReceivingDateTime"] = date("Y-m-d H:i:s");
			$insert["TextDecoded"] = $xmls[1];

			$smsdb->insert("inbox", $insert);
		}		
		
		$this->load->database("default", TRUE);
		
		return true;
	}
	
	function sendsmsmondial($xml)
	{
		$try = 0;
		while (1)
		{
			if (++$try > 3)
			{
				return false;
			}
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $this->config->item("SMS_API_URL"));
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);	
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
			$c = curl_exec($ch);	
			$err = curl_errno($ch);				
	
			if ($err) 
			{
				curl_close($ch);
				return false;
			}
			
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
			curl_close($ch);		
			
			$this->appendlog($code."\r\n---".$xml."\r\n".$c."\r\n---end of log\r\n");
			
			if ($code == 200)
			{
				return true;
			}		
			
			$this->appendlog("retry");
		}
	}

	function failed($psmsid)
	{
		if (strlen($psmsids) == 0) return;
		
		$psmsids = explode("&", $psmsid);
		for($i=0; $i < count($psmsids); $i++)
		{
			$pairs = explode("=", $psmsids[$i], 2);
			
			if ($pairs[0] == "id")
			{
				$smsid = isset($pairs[1]) ? $pairs[1] : 0;
				$smslimit = 1;
				break;
			}
		}
		
		if (! isset($smsid)) return;
		
		unset($update);
		
		$update['smsreceive_reply'] = 0;
		
		$this->db->where("smsreceive_id", $smsid);
		$this->db->update("smsreceive", $update);
		
		$this->receive($smsid);
	}
	
	function receive($psmsid=0)
	{	
		if (is_numeric($psmsid) && ($psmsid > 0))
		{
			$smsid = $psmsid;
			$smslimit = 1;
		}
		else
		if ($psmsid)
		{
			$psmsids = explode("&", $psmsid);
			for($i=0; $i < count($psmsids); $i++)
			{
				$pairs = explode("=", $psmsids[$i], 2);
				
				if ($pairs[0] == "id")
				{
					$smsid = isset($pairs[1]) ? $pairs[1] : 0;
					$smslimit = 1;
					break;
				}
			}
		}
		
		if (! isset($smsid))
		{
			$uniqid = uniqid();
			
			$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);		
			$this->db->limit(1, 0);
			$this->db->where("smsreceive_reply", 0);
			$this->db->order_by("smsreceive_id", "asc");
			$q = $this->db->get("smsreceive");
			
			if ($q->num_rows() == 0) 
			{
				// get id terakhir
			
				$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
				$this->db->limit(1, 0);
				$this->db->order_by("smsreceive_id", "desc");
				$q = $this->db->get("smsreceive");
				
				if ($q->num_rows() == 0) 
				{
					$smsid = 1;				
				}
				else
				{
					$row = $q->row();
					$smsid = $row->smsreceive_id;				
				}			
			}
			else
			{
				$row = $q->row();
				$smsid = $row->smsreceive_id;
			}
			
			$smslimit = $this->config->item("SMS_LIMIT");
		}
		
		$q = "apikey=".$this->config->item("SMS_API_KEY")."&startid=".$smsid."&limit=".$smslimit;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->config->item("SMS_API_URL")."?".$q);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: text/xml"));
		$xml = curl_exec($ch);	
		
		curl_close($ch);
		
		// insert
		
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");
		xml_parse($xml_parser, $xml);
		xml_parser_free($xml_parser);
	}
	
	function appendlog($log)
	{
		$fout = fopen(BASEPATH."../assets/upload/sms".date("Ymd").".log", "a");
		fwrite($fout, '['.date("Ymd H:i:s").'] '.$log."\r\n");
		fclose($fout);
	}
	
	function sendusers()
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}
		
		$message = isset($_POST["msg"]) ? $_POST['msg'] : "";
		$sendto = isset($_POST["sendto"]) ? $_POST['sendto'] : "";

		if (! $message) 
		{
			redirect(base_url()."sms/sendall/e/ms");
		}
		
		if ($this->sess->user_type != 1)
		{
			$this->db->where("agent_id", $this->sess->user_agent);
		}
		
		if ($sendto)
		{
			$this->db->where("agent_id", $sendto);
		}
		
		$this->db->join("agent", "user_agent = agent_id");
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url()."sms/sendall/e/ag");
		}
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$no = valid_mobile($rows[$i]->user_mobile);
			if (! $no) continue;
			if (in_array($no, $this->config->item("SMS_SKIP_NO"))) continue;
			
			$dest[] = $no;
		}
		
		if (! isset($dest)) 
		{
			redirect(base_url()."sms/sendall/e/de");
		}
		
		$this->params['dest'] = array_unique($dest);
		$this->params['content'] = $message;
		$xml = $this->load->view("sms/send", $this->params, true);

		$send = $this->sendsms($xml);		
		if (! $send) 
		{
			redirect(base_url()."sms/sendall/e/ma");
		}
		
		redirect(base_url()."sms/sendall/s");
	}
	
	function send2owner()
	{
		$this->params['dest'] = array('081703559911', '081317884830', '08123281232', '02197878136', '081231164447');
		$this->params['content'] = "Yth boss :)  ";
		$xml = $this->load->view("sms/send", $this->params, true);

		$this->sendsms($xml);
	}
	
	function sendphonebook()
	{
		$this->db->distinct();
		
		$this->db->where("smsreceive_received <=", date("Y-m-d H:i:s"));		
		$this->db->distinct();
		$this->db->select("smsreceive_from");
		$q = $this->db->get("smsreceive");
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$no = valid_mobile($rows[$i]->smsreceive_from);
			if (! $no) continue;
			if (in_array($no, $this->config->item("SMS_SKIP_NO"))) continue;
		
			echo "send sms to ".$no."\r\n";
		
			$this->params['dest'] = array($no);
			//$this->params['content'] = "Yth pelanggan, apabila ada yang melihat Kijang LGX W530NG dan Xenia L1844WE karena status mobil tersebut digelapkan oleh penyewa";
			$this->params['content'] = "Yth pelanggan, alhamdulillah kini ada sms server alternatif di 087777797920. Silahkan kirim sms request posisi ke no tsb. ";
			$xml = $this->load->view("sms/send", $this->params, true);

			$this->sendsms($xml);				
			sleep(30);
		}		
	}
	
	function sendtoallclient()
	{
		$this->db->distinct();
		$this->db->select("user_mobile, user_agent");
		$q = $this->db->get("user");
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$no = valid_mobile($rows[$i]->user_mobile);
			if (! $no) continue;
			
			if ($rows[$i]->user_agent == $this->config->item("GPSANDALASID"))
			{
				$ttd = "www.gpsandalas.com";
			}
			else
			{
				$ttd = "www.lacak-mobil.com";
			}			
		
			echo "send sms to ".$no."\r\n";
		
			$this->params['dest'] = array($no);
			$this->params['content'] = sprintf("Mohon maaf pd 28/03/2011 16:00-23:59 gps data service mengalami gangguan, shg web & layanan sms error. Kini sudah berjalan dengan normal. %s", $ttd);
			$xml = $this->load->view("sms/send", $this->params, true);

			$this->sendsms($xml);				
			sleep(30);
		}		
	}	
}

function startElement($parser, $name, $attrs) 
{
	global $mysms, $idx, $lastID;

	if (strcasecmp($name, "MESSAGES") == 0)
	{
		return;
	}
	
	if (strcasecmp($name, "SMS") == 0)
	{
		unset($mysms);

		$lastID = $attrs["ID"];
		return;
	}	
	
	$idx = $name;
}

function endElement($parser, $name) 
{
	global $mysms, $lastID;

	if (strcasecmp($name, "SMS") == 0)
	{
		$mysms['smsreceive_id'] = $lastID;
		$CI =& get_instance();
		
		$no = valid_mobile($mysms['smsreceive_from']);


		$uniqid = uniqid();
		$CI->db->where("'".$uniqid."'='".$uniqid."'", null, false);		
		$CI->db->where("smsreceive_id", $lastID);
		$q = $CI->db->get("smsreceive");

		if ($q->num_rows() == 0)
		{
			$CI->db->insert("smsreceive", $mysms);	
			$reply = 1;
		}
		else
		{	
			$row = $q->row();			
			if ($row->smsreceive_reply == 0)
			{
				$reply = 1;
			}
		}
		
		if (! isset($reply)) 
		{
			return;
		}
		
		$no = valid_mobile($mysms['smsreceive_from']);
		if (! $no) 
		{
			updatestatus($lastID, 2);
			return;
		}
		if (in_array($no, $CI->config->item("SMS_SKIP_NO"))) 
		{
			updatestatus($lastID, 2);
			return;
		}
		
		$message = strtoupper(trim($mysms['smsreceive_message']));
				
		// cek posisi
		$posisies = $CI->config->item("SMS_COMMAND_POSISI");
		foreach($posisies as $posisi)
		{
			if (strlen($message) == 0) continue;

			$messages = explode(" ", $message);
			$id = $messages[0];
			if ($id == $posisi)
			{
				sendcommandposisi($no, $id, $mysms);
				return;
			}
		}
		
		// cek reg
		$regs = $CI->config->item("SMS_COMMAND_REG");
		foreach($regs as $reg)
		{
			$id = substr($message, 0, strlen($reg));
			if ($id == $reg)
			{
				switch(strtoupper($id))
				{
					case "KM":
						sendcommandkm($no, $id, $mysms);
					break;
					case "PARK":
						sendcommandpark($no, $id, $mysms);
					break;
					case "RESTART":
						sendcommandrestart($no, $id, $mysms);
					break;
					case "PSSSEMUA":
						sendcommandposisisemua($no, $id, $mysms);
					break;
					case "AKTIVASIUSER":
						sendcommandaktivasi($no, $id, $mysms, "login");
					break;
					case "AKTIVASIMOBIL":
						sendcommandaktivasi($no, $id, $mysms, "vehicle_no");
					break;
					case "NOTIFIKASI":
						sendcommandnotifikasi($no, $id, $mysms);
					break;
					case "GEOFENCE":
						sendcommandgeofence($no, $id, $mysms);
					break;
					case "BROADCAST":
						sendbroadcast($no, $id, $mysms);
					break;
				}
				return;
			}
		}
		
		// cek apakah sms aki putus
		
		$pos = strpos($message, "MAIN POWER OFF ALARM");
		if ($pos !== FALSE)
		{
			sendpoweroff($no);
		}
	
		updatestatus($lastID, 2);	
		//sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDCOMMAND_MESSAGE"));
	}	
}

function sendpoweroff($no)
{
	$uniqid = uniqid();
	
	$CI =& get_instance();
	
	$no1 = "0".substr($no, 2);
	$CI->db->where("vehicle_card_no = '".$no."' OR vehicle_card_no = '".$no1."'", null);
	$CI->db->join("user", "user_id = vehicle_user_id");
	$q = $CI->db->get("vehicle");
	
	if ($q->num_rows() == 0) return;
	
	$row = $q->row();

	$CI->db->where("'".$uniqid."'='".$uniqid."'", null, false);	
	$CI->db->where("smsannouncement_user", $row->user_id);
	$CI->db->where("DATEDIFF(smsannouncement_send, '".date("Y-m-d")."') = 0", null); 
	$CI->db->where("smsannouncement_content", sprintf("Main Power Off Alarm %d", $row->vehicle_id));
	
	$total = $CI->db->count_all_results("smsannouncement");
	
	if ($total) return;
	
	if ($row->user_agent == $CI->config->item("GPSANDALASID"))
	{
		$mobiles = $CI->config->item("SMS_GPSANDALAS");
	}
	else
	{
		$mobiles = $CI->config->item("SMS_LACAKMOBIL");
	}

	$hp = valid_mobile($row->user_mobile);
	if ($hp) 
	{
		$mobiles[] = $hp;
	}
	
	$CI->params['dest'] = $mobiles;
	$CI->params['content'] = sprintf("Arus listrik kend '%s' terputus, mohon dicek. U/ monitor posisi sms PSS %s %s", $row->vehicle_no, $row->user_login, $row->vehicle_no);
	$xml = $CI->load->view("sms/send", $CI->params, true);
	
	if (! $CI->smsmodel->sendsms($xml)) return;
	
	unset($insert);
	
	$insert['smsannouncement_user'] = $row->user_id;
	$insert['smsannouncement_send'] = date("Y-m-d H:i:s");
	$insert['smsannouncement_content'] = sprintf("Main Power Off Alarm %d", $row->vehicle_id);
	
	$CI->db->insert("smsannouncement", $insert);
}

function sendcommandnotifikasi($no, $id, $mysms)
{
	$CI =& get_instance();

	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = trim($message, ".");
	
	if ((strcasecmp($message, "OFF") != 0) && (strcasecmp($message, "ON") != 0))
	{
		updatestatus($mysms['smsreceive_id'], 2);
		return;
	}
	
	if (substr($no, 0, 2) == "62")
	{
		$no = "0".substr($no, 2);
	}		
	
	unset($update);
	
	if (strcasecmp($message, "OFF") == 0)
	{
		$update['user_sms_notifikasi'] = 2;
		$CI->params['content'] = sprintf("No Anda telah di-nonaktif-kan u/ terima notifikasi. U/ mengaktifkan kembali kirim sms NOTIFIKASI ON");
	}
	else
	{
		$update['user_sms_notifikasi'] = 1;
		$CI->params['content'] = sprintf("No Anda telah di-aktif-kan u/ terima notifikasi. U/ berhenti kirim sms NOTIFIKASI OFF");
	}
	
	$CI->db->where("user_mobile", $no);		
	$CI->db->update("user", $update);

	$CI->params['dest'] = array($no);
	
	$xml = $CI->load->view("sms/send", $CI->params, true);
	
	if (! $CI->smsmodel->sendsms($xml)) return;
	
	updatestatus($mysms['smsreceive_id'], 1);
}

function sendcommandaktivasi($no, $id, $mysms, $keyword)
{
	$CI =& get_instance();

	if (! in_array($no, $CI->config->item("SMS_AKTIVASI")))
	{
		updatestatus($mysms['smsreceive_id'], 2);
		return;
	}
	
	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = str_replace("<spasi>", " ", $message);

	$datas = explode(" ", $message, 3);	
	if (count($datas) < 2)
	{
		updatestatus($mysms['smsreceive_id'], 2);
		return;
	}

	$nmonth = trim($datas[0]);
	$nmonth = str_replace('<', '', $nmonth);
	$nmonth = str_replace('>', '', $nmonth);

	if (! is_numeric($nmonth))
	{
		updatestatus($mysms['smsreceive_id'], 2);
		return;
	}
	
/*
 * 	if ($nmonth < 0)
	{
		updatestatus($mysms['smsreceive_id'], 2);
		return;
	}
	*/
	
	$data = trim($datas[1]);
	$data = str_replace('<', '', $data);
	$data = str_replace('>', '', $data);
		
	$CI->params['dest'] = array($no);
	
	switch($keyword)
	{
		case "login":
			
			$CI->db->select("vehicle.*");
			$CI->db->where("user_login", $data);
			$CI->db->join("vehicle", "vehicle_user_id = user_id");
			$q = $CI->db->get("user");
			if ($q->num_rows() == 0)
			{
				$CI->params['content'] = sprintf("Login %s tidak ditemukan", $data);
			}
			else
			{
				$rows = $q->result();

				for($i=0; $i < count($rows); $i++)
				{
					$d = smsdbintmaketime($rows[$i]->vehicle_active_date2, 0);
					$d1 = mktime(0, 0, 0, date('n', $d)+$nmonth, date('j', $d), date('Y', $d));

					unset($update);
					
					$update['vehicle_active_date2'] = date("Ymd", $d1);
					
					$CI->db->where("vehicle_id", $rows[$i]->vehicle_id);
					$CI->db->update("vehicle", $update);				
					$CI->db->cache_delete_all();	
				}
				
				$CI->params['content'] = sprintf("Kendaraan dgn login %s telah diperpanjang selama %s bulan dr sebelumnya", $data, $nmonth);
			}
		break;
		case "vehicle_no":
			$data = nomobil(trim($data));
		
			$CI->db->where("REPLACE(vehicle_no, ' ', '') = '".$data."'", null);
			$q = $CI->db->get("vehicle");
		
			if ($q->num_rows() == 0)
			{
				$CI->params['content'] = sprintf("No mobil %s tidak ditemukan", $data);
			}
			else
			{
				$row = $q->row();

				$d = smsdbintmaketime($row->vehicle_active_date2, 0);
				$d1 = mktime(0, 0, 0, date('n', $d)+$nmonth, date('j', $d), date('Y', $d));
				
				unset($update);

				$update['vehicle_active_date2'] = date("Ymd", $d1);
			
				$CI->db->where("vehicle_id", $row->vehicle_id);
				$CI->db->update("vehicle", $update);
				
				$CI->db->cache_delete_all();
				
				$CI->params['content'] = sprintf("Kendaraan dgn no mobil %s telah diperpanjang selama %s bulan dr sebelumnya", $data, $nmonth);
			}
		break;
	}

	$xml = $CI->load->view("sms/send", $CI->params, true);
	
	if (! $CI->smsmodel->sendsms($xml)) return;
	
	updatestatus($mysms['smsreceive_id'], 1);
}

function smsdbintmaketime($d, $t)
{
	$y = floor($d/10000);
	$m = floor(($d%10000)/100);
	$d = ($d%10000)%100;

	$j = floor($t/10000);
	$me = floor(($t%10000)/100);
	$de = ($t%10000)%100;
	
	return mktime($j, $me, $de, $m, $d, $y);

}

function sendcommandgeofence($no, $id, $mysms)
{
	$CI =& get_instance();
	
	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = str_replace("<titik koma>", ";", $message);
	$message = trim($message, ";");

	$datas = explode(";", $message, 3);	
	if (count($datas) < 3)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDGEOFENCECOMMAND_MESSAGE"));
		return;
	}
	
	$kota = trim($datas[0]);
	$kota = str_replace('<', '', $kota);
	$kota = str_replace('>', '', $kota);
	$kota = strtoupper($kota);

	if (! $kota)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDGEOFENCECOMMAND_MESSAGE"));
		return;
	}

	$provinsi = trim($datas[1]);
	$provinsi = str_replace('<', '', $provinsi);
	$provinsi = str_replace('>', '', $provinsi);
	$provinsi = strtoupper($provinsi);

	if (! $provinsi)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDGEOFENCECOMMAND_MESSAGE"));
		return;
	}
	
	$nomobil = nomobil(trim($datas[2]));		
	
	$CI->db->where("vehicle_status", 1);
	$CI->db->where("REPLACE(vehicle_no, ' ', '') = '".$nomobil."'", null);
	$CI->db->join("user", "vehicle_user_id = user_id");
	$CI->db->join("agent", "agent_id = user_agent");
	$q = $CI->db->get("vehicle");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_NOTFOUND"), $nomobil));
		return;
	}
		
	$rowvehicle = $q->row();
	
	// list hp yang diijinkan
	
	$owners = $CI->config->item("SMS_OWNER");
	for($i=0; $i < count($owners); $i++)
	{
		$hps[] = intl_mobile($owners[$i]);
	}

	$lacaks = $CI->config->item("SMS_LACAKMOBIL");
	for($i=0; $i < count($lacaks); $i++)
	{
		$hps[] = intl_mobile($lacaks[$i]);
	}

	$andalass = $CI->config->item("SMS_GPSANDALAS");
	for($i=0; $i < count($andalass); $i++)
	{
		$hps[] = intl_mobile($andalass[$i]);
	}
	
	$hp = valid_mobile($rowvehicle->user_mobile);
	if ($hp)
	{
		$hps[] = intl_mobile($hp);
	}

	if (! in_array($no, $hps))
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_GEOFENCE_ACCESS_DENIED"), $nomobil));
		return;
	}
	
	if ($rowvehicle->user_agent == $CI->config->item("GPSANDALASID"))
	{
		$ownercoorp = "GPS Andalas Coorp.";
	}
	else
	{
		$ownercoorp = "www.lacak-mobil.com";
	}
	
	$CI->params['dest'] = array($no);
	$CI->params['content'] = sprintf($CI->config->item("SMS_GEOFENCE_THANKS"), $rowvehicle->user_login, $rowvehicle->vehicle_no, $ownercoorp);
	$xml = $CI->load->view("sms/send", $CI->params, true);

	$CI->sendsms($xml);

	$message  = "";
	$message .= "<br />SMS No: ".$no."\r\n";
	$message .= "<br />SMS content: ".$mysms['smsreceive_message']."\r\n";
	$message .= "<br />Vehicle ID: ".$rowvehicle->vehicle_id."\r\n";
	$message .= "<br />URL: ".base_url()."geofence/sms/".$rowvehicle->vehicle_id."/".$no;
	
	maillocalhost("[www.lacak-mobil.com] Request Geofence", $message, "support@adilahsoft.com", "http://www.lacak-mobil.com/cron/sendmail", "info@lacak-mobil.com", "info@lacak-mobil.com");
			
	unset($update);
	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);	
	
}

function sendcommandkm($no, $id, $mysms)
{
	$CI =& get_instance();
	
	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = str_replace("<spasi>", " ", $message);
	
	$datas = explode(" ", $message, 2);	
	if (count($datas) < 2)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDKMCOMMAND_MESSAGE"));
		return;
	}

	$km = trim($datas[0]);
	$km = str_replace('<', '', $km);
	$km = str_replace('>', '', $km);

	if (! is_numeric($km))
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDKMCOMMAND_MESSAGE"));
		return;
	}	
	
	$nomobil = nomobil(trim($datas[1]));		
	
	$CI->db->where("vehicle_status", 1);
	$CI->db->where("REPLACE(vehicle_no, ' ', '') = '".$nomobil."'", null);
	$CI->db->join("user", "vehicle_user_id = user_id");
	$CI->db->join("agent", "agent_id = user_agent");
	$q = $CI->db->get("vehicle");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_NOTFOUND"), $nomobil));
		return;
	}
	
	$rowvehicle = $q->row();
	
	// list hp yang diijinkan
	
	$owners = $CI->config->item("SMS_OWNER");
	for($i=0; $i < count($owners); $i++)
	{
		$hps[] = intl_mobile($owners[$i]);
	}

	$lacaks = $CI->config->item("SMS_LACAKMOBIL");
	for($i=0; $i < count($lacaks); $i++)
	{
		$hps[] = intl_mobile($lacaks[$i]);
	}

	$andalass = $CI->config->item("SMS_GPSANDALAS");
	for($i=0; $i < count($andalass); $i++)
	{
		$hps[] = intl_mobile($andalass[$i]);
	}
	
	$hp = valid_mobile($rowvehicle->user_mobile);
	if ($hp)
	{
		$hps[] = intl_mobile($hp);
	}

	if (! in_array($no, $hps))
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_KM_ACCESS_DENIED"), $nomobil));
		return;
	}

	$CI->send2($rowvehicle, $km, array($no));
	
	unset($update);
	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);	
}

function sendcommandrestart($no, $id, $mysms)
{
	$CI =& get_instance();

	if (! in_array($no, $CI->config->item("SMS_ADMIN")))
	{
		unset($update);
		$update['smsreceive_reply'] = 2;
		
		$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
		$CI->db->update("smsreceive", $update);	

		return;
	}
	
	$update['config_value'] = 1;
	$CI->db->where("config_name", "runhist"); 
	$CI->db->update("config", $update);

	system("c:\\xampp\\php\\php.exe c:\\www\\dmap\\tools\\kill.php");
	system("c:\\xampp\\php\\php.exe c:\\www\\dmap\\tools\\service.php");

	$update['config_value'] = 0;
	$CI->db->where("config_name", "runhist"); 
	$CI->db->update("config", $update);


	$CI->params['dest'] = array($no);
	$CI->params['content'] = "Service telah direstart. Silahkan lakukan pengecekan.";
	$xml = $CI->load->view("sms/send", $CI->params, true);
	
	$CI->smsmodel->sendsms($xml);
	
	unset($update);
	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);		
}

function sendcommandpark($no, $id, $mysms)
{
	$CI =& get_instance();
	
	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = str_replace("<spasi>", " ", $message);
	
	$datas = explode(" ", $message, 2);	
	if (count($datas) < 2)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDPARKCOMMAND_MESSAGE"));
		return;
	}

	$park = trim($datas[0]);
	$park = str_replace('<', '', $park);
	$park = str_replace('>', '', $park);

	if (! is_numeric($park))
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDPARKCOMMAND_MESSAGE"));
		return;
	}	
	
	$nomobil = nomobil(trim($datas[1]));		
	
	$CI->db->where("vehicle_status", 1);
	$CI->db->where("REPLACE(vehicle_no, ' ', '') = '".$nomobil."'", null);
	$CI->db->join("user", "vehicle_user_id = user_id");
	$CI->db->join("agent", "agent_id = user_agent");
	$q = $CI->db->get("vehicle");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_NOTFOUND"), $nomobil));
		return;
	}
	
	$rowvehicle = $q->row();
	
	// list hp yang diijinkan
	
	$owners = $CI->config->item("SMS_OWNER");
	for($i=0; $i < count($owners); $i++)
	{
		$hps[] = intl_mobile($owners[$i]);
	}

	$lacaks = $CI->config->item("SMS_LACAKMOBIL");
	for($i=0; $i < count($lacaks); $i++)
	{
		$hps[] = intl_mobile($lacaks[$i]);
	}

	$andalass = $CI->config->item("SMS_GPSANDALAS");
	for($i=0; $i < count($andalass); $i++)
	{
		$hps[] = intl_mobile($andalass[$i]);
	}
	
	$hp = valid_mobile($rowvehicle->user_mobile);
	if ($hp)
	{
		$hps[] = intl_mobile($hp);
	}

	if (! in_array($no, $hps))
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_PARK_ACCESS_DENIED"), $nomobil));
		return;
	}

	$CI->send3($rowvehicle, $park, array($no));
	
	unset($update);
	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);	
}

function sendbroadcast($no, $id, $mysms)
{
	$CI =& get_instance();
	
	$sender = valid_mobile($no);
			
	if (in_array($sender, $CI->config->item("SMS_LACAKMOBIL")))
	{
		$CI->db->where("user_agent <>", $CI->config->item("GPSANDALASID"));
	}
	else
	if (in_array($sender, $CI->config->item("SMS_GPSANDALAS")))
	{
		$CI->db->where("user_agent", $CI->config->item("GPSANDALASID"));
	}
	else
	{
		unset($update);
		$update['smsreceive_reply'] = 2;
		
		$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
		$CI->db->update("smsreceive", $update);
		
		return;
	}
	
	$CI->db->distinct();
	$CI->db->select("user_mobile");
	$q = $CI->db->get("user");
	
	if ($q->num_rows() == 0) return;
	
	$rows = $q->result();
	$t = mktime();
	foreach($rows as $row)
	{
		$hp = valid_mobile($row->user_mobile);
		if (! $hp) continue;

		$hps[] = $hp;
	}

	if (! isset($hps))
	{
		unset($update);
		$update['smsreceive_reply'] = 2;
		
		$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
		$CI->db->update("smsreceive", $update);

		return;
	}

	unset($update);
	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);
	
	$contents = explode(" ", $mysms['smsreceive_message'], 2);
	
	$CI->params['dest'] = array_unique($hps);
	$CI->params['content'] = $contents[1];
	$xml = $CI->load->view("sms/send", $CI->params, true);

	$CI->sendsms($xml);			
	
}

function sendcommandposisisemua($no, $id, $mysms)
{
	$CI =& get_instance();
	
	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = str_replace("<spasi>", " ", $message);
	
	$login = trim($message);
	$login = str_replace('<', '', $login);
	$login = str_replace('>', '', $login);
	
	$CI->db->where("user_login", $login);
	$q = $CI->db->get("user");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALID_LOGIN_SEMUA"));
		return;
	}
	
	$rowuser = $q->row();
	if ($rowuser->user_type != 2)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_ACCESS_DENIED_PSS_SEMUA"));
		return;

	}
	
	$CI->db->where("user_id", $rowuser->user_id);	
	$CI->db->where("vehicle_status", 1);
	$CI->db->join("user", "vehicle_user_id = user_id");
	$CI->db->join("agent", "user_agent = agent_id");
	$q = $CI->db->get("vehicle");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_NOMOBIL_NOTFOUND_SEMUA"));
		return;
	}

	unset($update);
	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);

	$rowvehicles = $q->result();

	for($i=0; $i < count($rowvehicles); $i++)
	{	
		$rowvehicle = $rowvehicles[$i];
		// cek expired
	
		$t = $rowvehicle->vehicle_active_date2;
		$now = date("Ymd");
		if ($t < $now)
		{
			if ($rowvehicle->user_agent == $CI->config->item("GPSANDALASID"))
			{
				sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_GPSANDALAS"), $rowvehicle->vehicle_no));
				continue;
			}

			sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_LACAKMOBIL"), $rowvehicle->vehicle_no));
			continue;
		}
	
		if (! $CI->send1($rowvehicle, array($no)))
		{
			continue;
		}
	}
	
}
function sendcommandposisi($no, $id, $mysms)
{
	$CI =& get_instance();
	
	$message = trim($mysms['smsreceive_message']);		
	$message = trim(substr($message, strlen($id)));
	$message = str_replace("<spasi>", " ", $message);
	
	$datas = explode(" ", $message, 2);
	if (count($datas) <= 1)
	{
		sendinvalidcommand($no, $mysms, $CI->config->item("SMS_INVALIDCOMMAND_MESSAGE"));
		return;
	}
	
	$login = trim($datas[0]);
	$login = str_replace('<', '', $login);
	$login = str_replace('>', '', $login);
	
	$nomobil = nomobil(trim($datas[1]));		
	
	$CI->db->where("user_login", $login);
	$q = $CI->db->get("user");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_INVALID_LOGIN"), $nomobil));
		return;
	}
	
	$rowuser = $q->row();
		
	$CI->db->where("vehicle_status", 1);
	$CI->db->where("REPLACE(vehicle_no, ' ', '') = '".$nomobil."'", null);
	$CI->db->join("user", "vehicle_user_id = user_id");
	$CI->db->join("agent", "user_agent = agent_id");
	$q = $CI->db->get("vehicle");
	
	if ($q->num_rows() == 0)
	{
		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_NOTFOUND"), $nomobil));
		return;
	}
	
	$rowvehicle = $q->row();
	
	// inactive
	
	if ($rowvehicle->user_status != 1)
	{
		if ($rowvehicle->user_agent == $CI->config->item("GPSANDALASID"))
		{
			sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_GPSANDALAS"), $nomobil));
			return;
		}

		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_LACAKMOBIL"), $nomobil));
		return;		
	}

	if ($rowvehicle->vehicle_status == 3)
	{
		if ($rowvehicle->user_agent == $CI->config->item("GPSANDALASID"))
		{
			sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_GPSANDALAS"), $nomobil));
			return;
		}

		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_LACAKMOBIL"), $nomobil));
		return;		
	}
	
	// cek expired
	
	$t = $rowvehicle->vehicle_active_date2;
	$now = date("Ymd");
	if ($t < $now)
	{
		if ($rowvehicle->user_agent == $CI->config->item("GPSANDALASID"))
		{
			sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_GPSANDALAS"), $nomobil));
			return;
		}

		sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_NOMOBIL_EXPIRED_LACAKMOBIL"), $nomobil));
		return;
	}
	
	if ($rowuser->user_type == 2)
	{
		if ($rowuser->user_id != $rowvehicle->user_id)
		{
			sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_POSISI_ACCESS_DENIED"), $login, $nomobil));
			return;			
		}
	}
	else
	if ($rowuser->user_type == 3)
	{
		if ($rowuser->user_agent != $rowvehicle->user_agent)
		{
			sendinvalidcommand($no, $mysms, sprintf($CI->config->item("SMS_POSISI_ACCESS_DENIED"), $login, $nomobil));
			return;			
		}
	}
	
	// make sure, belum terkirim
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->where("smsreceive_reply", 0);
	$total = $CI->db->count_all_results("smsreceive");
	
	if ($total == 0) return;
	
	if (! $CI->send1($rowvehicle, array($no)))
	{
		return;
	}

	$update['smsreceive_reply'] = 1;
	
	$CI->db->where("smsreceive_id", $mysms['smsreceive_id']);
	$CI->db->update("smsreceive", $update);
}

function sendinvalidcommand($no, $mysms, $message)
{
	updatestatus($mysms['smsreceive_id'], 2);
	
	$CI =& get_instance();
	
	$CI->params['dest'] = array($no);
	$CI->params['content'] = $message;
	$xml = $CI->load->view("sms/send", $CI->params, true);

	$CI->sendsms($xml);		
}

function updatestatus($lastID, $status)
{
	unset($update);
	$update['smsreceive_reply'] = $status;
	
	$CI =& get_instance();
	
	$CI->db->where("smsreceive_id", $lastID);
	$CI->db->update("smsreceive", $update);			
}

function characterData($parser, $data) 
{
    global $mysms, $idx;
    
    switch(strtoupper($idx))
    {
    	case "FROM":
    		$mysms['smsreceive_from'] = $data;	
    	break;
    	case "SENT":
    		$mysms['smsreceive_sent'] = $data;	
    	break;
    	case "RECEIVED":
    		$mysms['smsreceive_received'] = $data;	
    	break;
    	case "MESSAGE":
    		$mysms['smsreceive_message'] = $data;	
    	break;    	    	    	
    }
    
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
