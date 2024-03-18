<?php
include "base.php";

class Vehicle extends Base {

	function Vehicle()
	{
		parent::Base();	

		$this->load->model("smsmodel");
		$this->load->model("vehiclemodel");

	}

	function cutoffengine($act="")
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;

		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}

		switch($this->sess->user_type)
		{
			case 2:
				if ($this->sess->user_company)
				{
						$this->db->where_in("vehicle_id", $vehicleids);
				}
				else
				{
						$this->db->where("vehicle_user_id", $this->sess->user_id);
				}
			break;
			case 3:
				$this->db->where("user_agent", $this->sess->user_agent);
			break;
		}

		$this->db->where("user_engine", 1);
		$this->db->where("vehicle_id", $id);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Access denied. Please relogin!";
			
			echo json_encode($callback);
			return;
		}

		$row = $q->row();

		$vehiclegsm = valid_mobile($row->vehicle_card_no);
		
		if (! $vehiclegsm)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_vehicle_card_no");
			
			echo json_encode($callback);
			return;
		}

		$row->vehicle_card_no = $vehiclegsm;

		if ($act == "did")
		{
			$password = isset($_POST['password']) ? $_POST['password'] : "";
			if (! $password)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lempty_password");
			
				echo json_encode($callback);
				return;
			}

			$this->db->where("user_id", $this->sess->user_id);
			$this->db->where("user_pass = PASSWORD('".mysql_escape_string($password)."')");
			$q = $this->db->get("user");

			if ($q->num_rows() == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("linvalid_password");
			
				echo json_encode($callback);
				return;
			}
		

			$aggree = isset($_POST['aggree']) ? $_POST['aggree'] : 0;

			if (! $aggree)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("linvalid_aggree_cutoffengine_desclimer");
			
				echo json_encode($callback);
				return;
			}

			$command = ($_POST['status'] == 1) ? $this->smsmodel->resumeengine($row->vehicle_type) : $this->smsmodel->cutoffengine($row->vehicle_type);
			unset($insert);
			//$vehiclegsm = "085717019778";

			$insert["log_created"] = date("Y-m-d H:i:s");
			$insert["log_creator"] = $this->sess->user_id;
			$insert["log_type"] = ($_POST['status'] == 1) ? "resumeengine" : "cutoffengine";
			$insert["log_ip"] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
			$insert["log_data"] = $row->vehicle_device.";".$vehiclegsm.";".$command;
			$insert["log_version"] = "desktop";
			$insert["log_target"] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";

			if (	0 
				|| (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_t1")))
				|| (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_t3")))
				|| (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_t5")))
				|| (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_gtp")))
			) {
				$message = sprintf("request cut off/resume engine %s %s %s", $row->vehicle_device, $vehiclegsm, $row->user_login);
				if ($row->user_agent == 3)
				{
					$this->smsmodel->sendsms1($this->config->item("SMS_GPSANDALAS"), $message);
				}
				else
				{
					$this->smsmodel->sendsms1($this->config->item("SMS_LACAKMOBIL"), $message);
				}
			}

			if (! $this->smsmodel->sendsms1(array($vehiclegsm), $command))
			{
				$callback['error'] = true;
				$callback['message'] = ($_POST['status'] == 1) ? $this->lang->line("lresumeengine_failed") : $this->lang->line("lcutoffengine_failed");
			
				echo json_encode($callback);
				return;
			}

			$this->db->insert("log", $insert);

			$callback['error'] = false;
			$callback['message'] = ($_POST['status'] == 1) ? $this->lang->line("lresumeengine_success") : $this->lang->line("lcutoffengine_success");
			
			echo json_encode($callback);
			return;
		}

		$this->db->where("config_name", "cutoffengine");
		$q = $this->db->get("config");

		if ($q->num_rows() == 0)
		{
			$desclimer = $this->config->item('CUTOFFENGINEDESCLIMER');
		}
		else
		{
			$rowconfig = $q->row();
			$desclimer = $rowconfig->config_value;
		}

		$this->params["vehicle"] = $row;
		$this->params["desclimer"] = $desclimer;

		$callback['html'] = $this->load->view("vehicle/cutoffengine", $this->params, true);
		$callback['error'] = false;

		echo json_encode($callback);
		return;
	}
	
	function getimage()
	{
		$images = array_keys($this->config->item('vehicle_image'));
		
		$vimage = isset($_POST['vimage']) ? trim($_POST['vimage']): $images[0];
		
		if (! $vimage)
		{
			$callback['message'] = 'Access denied';
			$callback['error'] = true;
			
			echo json_encode($callback);
			return;
		}
		
		$folder = BASEPATH."../assets/images/".$vimage;
		
		if (! is_dir($folder))
		{
			$callback['message'] = 'Access denied';
			$callback['error'] = true;
			
			echo json_encode($callback);
			return;
		}

		$this->params['vimage'] = $vimage;
		
		$callback['html'] = $this->load->view("vehicle/image", $this->params, true);		
		$callback['error'] = false;
		
		echo json_encode($callback);
	}
	
	function renew($id)
	{
		$this->db->where("vehicle_id", $id);
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			$callback['html'] = "Access denied";
			$callback['title'] = $this->lang->line("lvehicle_activate");
		
			echo json_encode($callback);	
			return;
		}
		
		$row = $q->row();
		
		$row->vehicle_active_date1_fmt = inttodate($row->vehicle_active_date1);
		$row->vehicle_active_date2_fmt = inttodate($row->vehicle_active_date2);
		
		$this->params['vehicle'] = $row;
		$callback['html'] = $this->load->view("vehicle/renew", $this->params, true);	
		$callback['title'] = $this->lang->line("lvehicle_activate").": ".$row->vehicle_name." ".$row->vehicle_no;
		echo json_encode($callback);
		
	}
	
	function  activate()
	{
		$device = isset($_POST['dev']) ? $_POST['dev'] : "";
		$exp1 = isset($_POST['exp1']) ? $_POST['exp1'] : "";
		$exp2 = isset($_POST['exp2']) ? $_POST['exp2'] : "";
		
		if (strlen($device) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Access denied";
			
			echo json_encode($callback);
		}
		
		$t1 = formmaketime($exp1." 00:00:00");
		if (date("d/m/Y", $t1) != $exp1)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_start_expired_vehicle");
			
			echo json_encode($callback);
		}
		
		$t2 = formmaketime($exp2." 00:00:00");
		if (date("d/m/Y", $t2) != $exp2)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_end_expired_vehicle");
			
			echo json_encode($callback);
		}	
		
		if ($t1 > $t2)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_expired_vehicle");
			
			echo json_encode($callback);
		}	
		
		unset($update);
		
		$update['vehicle_active_date1'] = date("Ymd", $t1);
		$update['vehicle_active_date2'] = date("Ymd", $t2);
		
		$this->db->where("vehicle_device", $device);
		$this->db->update("vehicle", $update);
		
		$this->db->cache_delete_all();
		
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lvehicle_activate_updated");
			
		echo json_encode($callback);		
	}
	
	function status($host, $name, $redirect="user")
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}
		
		$v = $host.'@'.$name;
		
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
				
		$this->db->where("vehicle_device", $v);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q =$this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$row = $q->row();
		
		$newstatus = ($row->vehicle_status == 3) ? 1 : 3;
		
		unset($update);
		
		$update["vehicle_status"] = $newstatus;
		
		$this->db->where("vehicle_id", $row->vehicle_id);
		$this->db->update("vehicle", $update);
		
		$this->db->cache_delete_all();
		
		if ($redirect == 'user')
		{
			redirect(base_url()."user");
		}
		
		
	}
	
	function formtype()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : "";
		
		if (! $id)
		{
			$callback['error'] = true;
			$callback['message'] = "Invalid vehicle!";
			
			echo json_encode($callback);
			return;
		}
		
		$this->db->where("vehicle_id", $id);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Invalid vehicle!";
			
			echo json_encode($callback);
			return;
		}
		
		$row = $q->row();
		
		$this->params['vehicle'] = $row;
		$html = $this->load->view("vehicle/formtype", $this->params, TRUE);

		$callback['error'] = false;
		$callback['html'] = $html;
		
		echo json_encode($callback);
		return;

	}
	
	function savetype()
	{
		$id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$type = isset($_POST['vehicle_type']) ? $_POST['vehicle_type'] : "";
		
		$this->db->where("vehicle_id", $id);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Invalid vehicle!";
			
			echo json_encode($callback);
			return;
		}

		$row = $q->row();
		
		unset($vehicle_type);
		
		$update['vehicle_type'] = $type;
		
		$this->db->where("vehicle_id", $id);
		$this->db->update("vehicle", $update);
		
		$this->db->cache_delete_all();

		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lupdated_vehicle_type");
		
		echo json_encode($callback);

	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
