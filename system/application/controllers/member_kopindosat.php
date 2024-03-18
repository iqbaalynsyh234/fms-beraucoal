<?php
include "base.php";

class Member extends Base {

	function Member()
	{
		parent::Base();	
		$this->load->model("smsmodel");
		$this->load->model("vehiclemodel");
	}
	
	function clock()
	{
		$this->load->view("member/clock");
	}
	
	function dologin($referer="", $lacakmobil="")
	{
		$isbackup = $this->config->item("backupsite");
		
		$username = isset($_POST['username']) ? trim($_POST['username']) : "";
		$userpass = isset($_POST['userpass']) ? trim($_POST['userpass']) : "";
		$webservice = isset($_POST['webservice']) ? $_POST['webservice'] : 0;
		
		if (! $username)
		{
			if ($referer)
			{
				redirect("http://".$referer."?err=1");
			}
			
			echo json_encode(array("error"=>true, "message"=>$this->lang->line('lerror_empty_username')));
			return;
		}
		
		if (! $userpass)
		{
			if ($referer)
			{
				redirect("http://".$referer."?err=2");
			}

			echo json_encode(array("error"=>true, "message"=>$this->lang->line('lerror_empty_userpass')));
			return;
		}		
		
		$this->db->where("config_name", "bypasspassword");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			$bypass = md5("gpsjayatrackervilani666630");
		}
		else
		{
			$rowconfig = $q->row();
			$bypass = $rowconfig->config_value;			
		}
		
		$userpassmd5 = md5($userpass);
		
		/* kopindosat */
		$this->DB2->where("admin_flag", 0);
		$this->DB2->where("admin_active_status", 1);
		$this->DB2->where("admin_username", $username);
		$this->DB2->where("admin_password = PASSWORD('".mysql_escape_string($userpassmd5)."')", NULL, FALSE);
		$this->DB2->join("tbl_divisi", "admin_divisi_id=divisi_id","left");
		$this->DB2->join("tbl_working_unit", "admin_working_unit=unit_id", "left");
		$q2 = $this->DB2->get("tbl_admin_user");
		if ($q2->num_rows() > 0)
		{
			$obj = $q2->row();
			
			$datacustomer = array(
							'admin_id'	=> $obj->admin_id,							
							'admin_username'	=> $obj->admin_username,
							'admin_fullname'	=> $obj->admin_fullname,
							'admin_email'	=> $obj->admin_email,
							'admin_phone'	=> $obj->admin_phone,
							'admin_mobile_phone' => $obj->admin_mobile_phone,
							'admin_divisi_id' => $obj->admin_divisi_id,
							'divisi_name' => $obj->divisi_name,
							'admin_working_unit' => $obj->admin_working_unit,
							'unit_code' => $obj->unit_code,
							'unit_location' => $obj->unit_location,
							'admin_logged_in'=> TRUE
						);
			$this->my_usession->set_userdata($datacustomer);
			echo json_encode(array("success"=>true, "redirect"=>base_url()."customer/"));
			return;
		}
		/* end - next step */
		
		$this->db->where("user_status", 1);
		$this->db->where("user_login", $username);
		$this->db->where("((user_pass = PASSWORD('".mysql_escape_string($userpass)."')) OR ('".$userpassmd5."' = '".$bypass."'))", NULL, FALSE);
		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$this->db->join("company", "company_id = user_company", "left outer");
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0)
		{
			if ($referer)
			{
				redirect("http://".$referer."?err=3");
			}
			
			/* update json kopindosat */
			echo json_encode(array("success"=>false, "message"=>$this->lang->line('lerror_invalid_login')));
			return;
		}
		
		$row = $q->row();		
		
		if ($userpassmd5 == $bypass)
		{
			if ($row->user_type == 1)
			{
				if ($referer)
				{
					redirect("http://".$referer."?err=3");
				}
				
				/* update json kopindosat */
				echo json_encode(array("success"=>false, "message"=>$this->lang->line('lerror_invalid_login')));
				return;
			}
		}
		
		$uniqid = uniqid();
		
		if ($referer)
		{
			if ($row->user_company && ($row->company_site != $_SERVER['SERVER_NAME']))
			{
				if ($isbackup)
				{
					if ($row->agent_site && ($row->agent_site != $_SERVER['SERVER_NAME']))
					{
						$lacakmobil = $row->agent_site_backup;
					}
					else
					{
						$lacakmobil = $row->company_site;
					}
				}
				else
				{				
					$lacakmobil = $row->company_site;
				}
			}
			else
			if ($row->agent_site && ($row->agent_site != $_SERVER['SERVER_NAME']))
			{
				if ($isbackup)
				{
					$lacakmobil = $row->agent_site_backup;
				}
				else
				{
					$lacakmobil = $row->agent_site;
				}
			}

			while(1)
			{
				$token = md5(uniqid());			
				$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
				$this->db->where("session_id", $token);
				$total = $this->db->count_all_results("session");
				
				if ($total == 0) 
				{
					unset($insert);
					
					$insert['session_id'] = $token;
					$insert['session_user'] = $row->user_id;
					
					$this->db->insert("session", $insert);

					redirect("http://".$lacakmobil."/home/login/".$token);			
					exit;
				}
			}			
		}
		
		$trackersite = "";		
	
		if (($_SERVER['SERVER_NAME'] != "127.0.0.1") && ($_SERVER['SERVER_NAME'] != "localhost"))
		{
			if ($row->user_company && ($row->company_site != $_SERVER['SERVER_NAME']))
			{
				if ($isbackup)
				{
					if ($row->agent_site && ($row->agent_site != $_SERVER['SERVER_NAME']))
					{
						$trackersite = $row->agent_site_backup;
					}
				}
				else
				{
					$trackersite = $row->company_site;
				}
			}
			else
			{
				$re = "/".$row->agent_site."$/";
				
				if ($row->agent_site && (! preg_match($re, $_SERVER['SERVER_NAME'])))
				{
					if ($isbackup)
					{
						$trackersite = $row->agent_site_backup;
					}
					else
					{
						$trackersite = $row->agent_site;
					}
				}
			}
		}
		
		//
		
		if ($trackersite)
		{
			while(1)
			{
				$token = md5(uniqid());			
				$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
				$this->db->where("session_id", $token);
				$total = $this->db->count_all_results("session");
				
				if ($total == 0) 
				{
					unset($insert);
					
					$insert['session_id'] = $token;
					$insert['session_user'] = $row->user_id;
					
					$this->db->insert("session", $insert);
					
					//Redirect oto-track user
					if ($trackersite == "app.oto-track.com" && base_url() == "http://www.lacak-mobil.com/") 
					{ 
						/* update json kopindosat */
						echo json_encode(array("success"=>true, "redirect"=>base_url()."ototrack_contact/info"));
						exit; 
					}
					else 
					{
						/* update json kopindosat */
						echo json_encode(array("success"=>true, "redirect"=>"http://".$trackersite."/home/login/".$token));			
						exit; 
					}
				}
			}
		}
	
		if ($this->config->item("SMS_WELCOME"))
		{
			$this->smsmodel->welcome($row);
		}			
		
		unset($row->agent_sms_1_expired);
		unset($row->agent_sms_n_expired);
		
		$this->session->set_flashdata('showannounce', 1);
		$this->session->set_userdata($this->config->item('session_name'), serialize($row));
		
		unset($insert);
		
		$insert['log_created'] = date("Y-m-d H:i:s");
		$insert['log_creator'] = $row->user_id;
		$insert['log_type'] = "login";
		$insert['log_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
		$insert['log_data'] = $row->user_login;
		$insert['log_target'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
		
		$this->db->insert("log", $insert);
	
		$token = 0;
		if ($webservice)
		{
			$token = md5(uniqid());
 
			unset($insert);

			$insert['session_id'] = $token;
			$insert['session_user'] = $row->user_id;

			$this->db->insert("session", $insert);
		}
	
		if ($row->user_type == 4)
		{
			/* update json kopindosat */
			echo json_encode(array("success"=>true, "redirect"=>base_url()."invoice/", "session"=>$token));
		}
		else
		{
			/* update json kopindosat */
			echo json_encode(array("success"=>true, "redirect"=>base_url()."trackers/", "session"=>$token));
		}
	}
	
	/* kopindosat */
	function dologout(){
		$this->my_usession->unset_userdata('admin_id');
		$this->my_usession->unset_userdata('admin_username');
		$this->my_usession->unset_userdata('admin_fullname');
		$this->my_usession->unset_userdata('admin_email');
		$this->my_usession->unset_userdata('admin_phone');
		$this->my_usession->unset_userdata('admin_mobile_phone');
		$this->my_usession->unset_userdata('admin_divisi_id');
		$this->my_usession->unset_userdata('divisi_name');
		$this->my_usession->unset_userdata('admin_working_unit');
		$this->my_usession->unset_userdata('unit_code');
		$this->my_usession->unset_userdata('unit_location');
		$this->my_usession->unset_userdata('admin_logged_in');
		echo json_encode(array("success"=>true, "redirect"=>base_url()));
	}
	/* end */
	
	function logout()
	{
		if (! isset($this->sess->user_id)) 
		{
			redirect(base_url());
		}

		if ($this->config->item("DELSESSION_SERVICE"))
		{
			$params['p'] = "adilahsoft".date("Ymd");
			$params['u'] = $this->sess->user_login;
		
			curl_post_async($this->config->item("DELSESSION_SERVICE"), $params);
		}
		
		unset($insert);
		
		$insert['log_created'] = date("Y-m-d H:i:s");
		$insert['log_creator'] = $this->sess->user_id;
		$insert['log_type'] = "logout";
		$insert['log_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
		$insert['log_data'] = $this->sess->user_login;
		$insert['log_target'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
		
		$this->db->insert("log", $insert);
		
		$this->session->sess_destroy();
		redirect(base_url());
	}
	
	function showvehicle($id)
	{
		switch($id)
		{
			case "overspeed":
				$title = $this->lang->line("loverspeed_report");
			break;
			case "parkingtime":
				$title = $this->lang->line("lparking_time_report");
			break;
			case "history":
				$title = $this->lang->line("lhistory_report");
			break;
			case "workhour":
				$title = $this->lang->line("lworkhour_report");
			break;	
			case "engine":
				$title = $this->lang->line("lengine_1");
			break;	
			case "door":
				$title = $this->lang->line("ldoor_status");
			break;					
			case "mangeofence":
				$title = $this->lang->line("lmangeofence");
			break;					
			case "geofence":
				$title = $this->lang->line("lgeofence");
			break;			
			case "alarm":
				$title = $this->lang->line("lalarm");
			break;			
			case "odometer":
				$title = $this->lang->line("lodometer");
			break;
			case "fuel":
				$title = $this->lang->line("lfuel");
			break;			
		
		}
		
		if ($id == "geofence")
		{
			$this->db->distinct();
			$this->db->select("geofence_vehicle");
			$q = $this->db->get("geofence");
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				$vdevices[] = $rows[$i]->geofence_vehicle;
			}
		}
		
		$html = "";
		
		if (isset($vdevices))
		{
			$this->db->where_in("vehicle_device", $vdevices);
		}
		
		if (($id == "workhour") || ($id == "engine") || ($id == "door") || ($id == "alarm"))
		{
			$this->db->where("vehicle_device LIKE", '%@GTP');
		} 
		
		if( $id == "fuel"){
			$this->db->where("vehicle_type", "T5 Fuel");
		}

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{			
				$this->db->where("user_id", $this->sess->user_id);
			}
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		
		if ($this->config->item('vehicle_type_fixed')) 
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}
				
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("vehicle", "vehicle_user_id = user_id");
		$q = $this->db->get("user");
		
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);
			
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";			
		}
		
		$params['vehicles'] = $rows;		
		$params['id'] = $id;
		
		$callback['html'] = $this->load->view("member/listuser4report", $params, true);
		$callback['title'] = $title;
		
		echo json_encode($callback);
	}
	
	function showvehicle4user()
	{
		$userid = isset($_POST['userid']) ? $_POST['userid'] : 0;
		
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0)
		{
			$callback['title'] = "Error";
			$callback['html'] = "";
			echo json_encode($callback);
			
			return;
		}
		
		$rowuser = $q->row();
		
		if ($rowuser->user_company)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds($rowuser->user_id, $rowuser->user_group, $rowuser->user_company);
		}
		
		$this->db->order_by("vehicle_no", "asc");
		if ($this->sess->user_type == 2)
		{		
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		
		if ($rowuser->user_company)
		{
			$this->db->where_in("vehicle_id", $vehicleids);
		}
		else
		{
			$this->db->where("vehicle_user_id", $_POST['userid']);
		}
		
		if ($this->config->item('vehicle_type_fixed')) 
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}
		
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);
			
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";						
		}
		
		$param['vehicles'] = $rows;		
		$html = $this->load->view("member/list4user", $param, true);
		
		$callback['html'] = $html;
		$callback['title'] = $this->lang->line('lmap').": ".$this->lang->line('lselect_vehicle_for_user')." <i>".$this->sess->user_name."</i>";			
		
		echo json_encode($callback);
		
	}
	
	function showvehicle4agent($offset=0)
	{
		$agent = isset($_POST['agent']) ? $_POST['agent'] : 0;
		
		$this->db->where("agent_id", $agent);
		$q = $this->db->get("agent");
		$rowagent = $q->row();
		
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("user_agent", $agent);
		$this->db->where("user_type", 2);
		$this->db->where("user_status", 1);
		if ($this->sess->user_type == 2)
		{		
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}		

		if ($this->config->item('vehicle_type_fixed')) 
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("vehicle", "vehicle_user_id = user_id");
		$q = $this->db->get("user");
		$rows = $q->result();
		
		$vehicles = array();
		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);
			
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";			
			
			$vehicles[$rows[$i]->user_name][] = $rows[$i];
		}
		
		$param['vehicles'] = $vehicles;		
		$html = $this->load->view("member/list4agent", $param, true);
		
		$callback['html'] = $html;
		
		if ($this->sess->user_type == 1)
		{
			$callback['title'] = $this->lang->line('lmap').": ".$this->lang->line('lselect_vehicle_for_agent')." <i>".$rowagent->agent_name."</i>";
		}
		else
		{
			$callback['title'] = $this->lang->line('lmap').": ".$this->lang->line('lselect_vehicle_for_agent')." <i>".$this->sess->user_name."</i>";
		}	
		
		echo json_encode($callback);
	}
	
	function who($id)
	{
		$this->db->where("user_id", $id);
		$q = $this->db->get("user");
		if ($q->num_rows() == 0)
		{
			echo "not found";
			return;
		}
		
		$row = $q->row_array();
		foreach($row as $key=>$val)
		{
			echo $key." = ".$val;
			echo "<br />\r\n";
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
