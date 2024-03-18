<?php
include "base.php";

class Newmobile extends Base {

	function Newmobile()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");		
	}
	function dologin()
	{
		header('Access-Control-Allow-Origin:*');
		
		$username = isset($_POST['username']) ? trim($_POST['username']) : "";
		$userpass = isset($_POST['password']) ? trim($_POST['password']) : "";
		
		$this->db->select("user_id,user_name,user_agent,user_company,user_group");
		$this->db->where("user_status", 1);
		$this->db->where("user_login", $username);
		$this->db->where("((user_pass = PASSWORD('".mysql_escape_string($userpass)."')))", NULL, FALSE);
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>$this->lang->line('lerror_invalid_login'))));
			return;	
		}
		
		$data = $q->row();
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function sendmessage()
	{
		header('Access-Control-Allow-Origin:*');
		$ismail = array("info@lacak-moibl.com","prastgtx@gmail.com");
		//$ismail = array("prastgtx@gmail.com");
		$isname = isset($_POST['ContactName']) ? trim($_POST['ContactName']) : "";
		$isemail = isset($_POST['ContactEmail']) ? trim($_POST['ContactEmail']) : "";
		$ismessage = isset($_POST['ContactComment']) ? trim($_POST['ContactComment']) : "";
		
		unset($mail);
		$contentmail = "";
		$contentmail .= "<center>";
		$contentmail .= "<hr />";
		$contentmail .= "NEW MESSAGE FROM LACAK MOBIL - ANDROID";
		$contentmail .= "<hr />";
		$contentmail .= "</center>";
		$contentmail .= "<br />";
		$contentmail .= "NAME :"." ".$isname."<br />";
		$contentmail .= "EMAIL :"." ".$isemail."<br />";
		$contentmail .= "DATE :"." ".date("d M Y H:i:s")."<br />";
		$contentmail .= "MESSAGE :"." ".$ismessage."<br />";
		$contentmail .= "<br />";
		$contentmail .= "<br />";
		$contentmail .= "<hr />";
		
		$subject =  "New Message From Lacak Mobil Android";
		$message = $contentmail;
		$sender = $isemail;
		foreach($ismail as $m)
		{
			$pmail = $this->sendmaildata($sender,$m,$subject,$message);
		}
		
		exit(json_encode(array("m"=>"Terima Kasih telah menghubungi kami, Kami akan segera merespon email dari anda")));
		return;
		
	}
	
	function sendmaildata($from,$to,$subject,$msg)
	{
		$headers = "";
		$headers  .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";		
		$headers .=  'From: '.$from. "\r\n" . 'X-Mailer: PHP/' . phpversion();
		mail($to, $subject, $msg, $headers);
		return true;
	}
	
	function searchdriver()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		
		$this->db->where("vehicle_user_id",$userid);
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$vehicle = $q->result();
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("driver_name","asc");
		$this->dbtransporter->where("driver_company", $companyid);
		$this->dbtransporter->where("driver_status", 1);
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Vehicle Not Available !","data"=>$data)));
			return;	
		}
		$data = $q->result();
		exit(json_encode(array("data"=>$data,"vehicle"=>$vehicle)));
		return;
	}
	
	function searchpenyewa()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		
		$this->dbcar = $this->load->database('rentcar', true);
		$this->dbcar->where("customer_company", $companyid);
		$this->dbcar->where("customer_flag", 0);
		$q = $this->dbcar->get("rentcar_customer");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Penyewa Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("datapenyewa"=>$data)));
		return;
	}
	
	function searchcosttype()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		$mv = array();
		
		$this->db->select("user_id,user_name,user_agent,user_company,user_group");
		$this->db->where("user_status", 1);
		$this->db->where("user_id",$userid);
		$q = $this->db->get("user");
		$user = $q->row();
		if($user->user_company > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_company",$user->user_company);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		if($user->user_group > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_group",$user->user_group);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		$this->db->where("vehicle_user_id",$user->user_id);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		if($q->num_rows > 0)
		{
			$a = $q->result();
			foreach($a as $v)
			{
				$mv[] = $v->vehicle_id;
			}
		}
		$this->db->order_by("vehicle_isred", "asc");	
		$this->db->order_by("vehicle_type", "asc");	
		$this->db->order_by("vehicle_no", "asc");	
		$this->db->order_by("vehicle_company", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_id",$mv);
		$q = $this->db->get("vehicle");
		$data = $q->result();
		$vehicle = $data;
		
		$this->dbcar = $this->load->database('rentcar', true);
		$this->dbcar->where('costs_type_company',$companyid);
		$this->dbcar->where('costs_type_flag',0);
		$q = $this->dbcar->get("rentcar_costs_type");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data CostType Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("datacosttype"=>$data,"vehicle"=>$vehicle)));
		return;
	}
	
	function searchblacklist()
	{	
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		$black = 0;
		
		$this->dbcar = $this->load->database('rentcar', true);
		$this->dbcar->where("customer_status", $black);
		$this->dbcar->where("customer_flag", 0);
		$q = $this->dbcar->get("rentcar_customer");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Blacklist Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("datablacklist"=>$data)));
		return;
	}
	
	function searchsurveyor()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		
		$this->dbcar = $this->load->database('rentcar', true);
		$this->dbcar->where("surveyor_company", $companyid);
		$this->dbcar->where("surveyor_status", 1);
		$q = $this->dbcar->get("surveyor");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Blacklist Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("datasurveyor"=>$data)));
		return;
	}
	
	function searchschedule()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		$mv = array();
		
		$this->db->select("user_id,user_name,user_agent,user_company,user_group");
		$this->db->where("user_status", 1);
		$this->db->where("user_id",$userid);
		$q = $this->db->get("user");
		$user = $q->row();
		if($user->user_company > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_company",$user->user_company);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		if($user->user_group > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_group",$user->user_group);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		$this->db->where("vehicle_user_id",$user->user_id);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		if($q->num_rows > 0)
		{
			$a = $q->result();
			foreach($a as $v)
			{
				$mv[] = $v->vehicle_id;
			}
		}
		$this->db->order_by("vehicle_isred", "asc");	
		$this->db->order_by("vehicle_type", "asc");	
		$this->db->order_by("vehicle_no", "asc");	
		$this->db->order_by("vehicle_company", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_id",$mv);
		$q = $this->db->get("vehicle");
		$data = $q->result();
		$vehicle = $data;
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("driver_name","asc");
		$this->dbtransporter->where("driver_company", $companyid);
		$this->dbtransporter->where("driver_status", 1);
		$q = $this->dbtransporter->get("driver");
		$data = $q->result();
		$driver = $data;
		
		$this->dbcar = $this->load->database('rentcar', true);
		$this->dbcar->where("surveyor_company", $companyid);
		$this->dbcar->where("surveyor_status", 1);
		$q = $this->dbcar->get("surveyor");
		$data = $q->result();
		$surveyor = $data;
		
		$this->dbcar = $this->load->database('rentcar', true);
		$this->dbcar->where("vehicle_status", 0);
		$this->dbcar->where("settenant_flag", 0);
		$this->dbcar->where("settenant_company", $companyid);
		$this->dbcar->join("rentcar_customer", "customer_id = settenant_name", "left");	
		$q = $this->dbcar->get("rentcar_settenant_vehicle");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Schedule Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("dataschedule"=>$data,"surveyor"=>$surveyor,"vehicle"=>$vehicle,"driver"=>$driver)));
		return;
	}
	
	function searchservice()
	{	
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);

		$this->dbtransporter->join('workshop','workshop_id = service_workshop','left_outer');
		$this->dbtransporter->join('mobil','mobil_id = service_mobil','left_outer');
		$this->dbtransporter->where('service_company', $companyid);
		//$this->dbtransporter->where('service_status', 1);
		$q = $this->dbtransporter->get('service');
		$dataservice = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Service Not Available !","data"=>$dataservice)));
			return;	
		}
		exit(json_encode(array("dataservice"=>$dataservice)));
		return;
	}
	function getsubdata_service()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where('mobil_status',1);
		$this->dbtransporter->where('mobil_company',$companyid);
		$q = $this->dbtransporter->get('mobil');
		$datamobil = $q->result();
		
		$this->dbtransporter->where('workshop_company',$companyid);
		$this->dbtransporter->where('workshop_status',1);
		$q = $this->dbtransporter->get('workshop');
		$dataworkshop = $q->result();
		
		$this->dbtransporter->where('driver_company',$companyid);
		$this->dbtransporter->where('driver_status',1);
		$q = $this->dbtransporter->get('driver');
		$datadriver = $q->result();
		
		$this->dbtransporter->where('mechanic_company',$companyid);
		$this->dbtransporter->where('mechanic_status',1);
		$q = $this->dbtransporter->get('mechanic');
		$datamechanic = $q->result();
		
		exit(json_encode(array("datamobil"=>$datamobil,"dataworkshop"=>$dataworkshop,"datadriver"=>$datadriver,"datamechanic"=>$datamechanic)));
		return;
	}
	
	function searchvehicle()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		
		$this->dbtransporter = $this->load->database('transporter',true);
		$this->dbtransporter->where('mobil_status', 1);
		$this->dbtransporter->where('mobil_company', $companyid);
		$qmobil = $this->dbtransporter->get('mobil');
		$rowmobil = $qmobil->result();
		$data = $rowmobil;
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function getvehicle()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		
		$this->dbtransporter = $this->load->database('transporter',true);
		$this->dbtransporter->select('mobil_device');	
		$this->dbtransporter->where('mobil_company', $companyid);
		$this->dbtransporter->where('mobil_status', 1);
		$qmobil = $this->dbtransporter->get('mobil');
		$rowmobil = $qmobil->result();
		$this->db->order_by("vehicle_no", "asc");
		if (isset($rowmobil) && count($rowmobil)>0)
		{
			for ($i=0;$i<count($rowmobil);$i++)
			{
				$data[] = $rowmobil[$i]->mobil_device;  
			}
			$this->db->where_not_in("vehicle_device", $data);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		exit(json_encode(array("data"=>$rv)));
		return;
	}
	function getvehicle_fordriver()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $userid);
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		exit(json_encode(array("data"=>$rv)));
		return;
	}
	function searchmechanic()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("mechanic_name","asc");
		$this->dbtransporter->where("mechanic_company", $companyid);
		$this->dbtransporter->where("mechanic_status", 1);
		$q = $this->dbtransporter->get("mechanic");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Mechanic Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function searchworkshop()
	{
		header('Access-Control-Allow-Origin:*');
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("workshop_name","asc");
		$this->dbtransporter->where("workshop_company", $companyid);
		$this->dbtransporter->where("workshop_status", 1);
		$q = $this->dbtransporter->get("workshop");
		$data = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Workshop Not Available !","data"=>$data)));
			return;	
		}
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function getdriverbyid()
	{
		header('Access-Control-Allow-Origin:*');
		$driverid = isset($_POST['driverid']) ? trim($_POST['driverid']) : "";
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		$this->db->where("vehicle_user_id",$userid);
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$vehicle = $q->result();
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("driver_name","asc");
		$this->dbtransporter->where("driver_id", $driverid);
		$this->dbtransporter->where("driver_status", 1);
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Vehicle Not Available !")));
			return;	
		}
		$data = $rows;
		exit(json_encode(array("data"=>$data,"vehicle"=>$vehicle)));
		return;
	}
	function getworkshopbyid()
	{
		header('Access-Control-Allow-Origin:*');
		$workshopid = isset($_POST['workshopid']) ? trim($_POST['workshopid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("workshop_name","asc");
		$this->dbtransporter->where("workshop_id", $workshopid);
		$this->dbtransporter->where("workshop_status", 1);
		$q = $this->dbtransporter->get("workshop");
		$rows = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Vehicle Not Available !")));
			return;	
		}
		$data = $rows;
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function getmechanicbyid()
	{
		header('Access-Control-Allow-Origin:*');
		$mechanic_id = isset($_POST['mechanic_id']) ? trim($_POST['mechanic_id']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->orderby("mechanic_name","asc");
		$this->dbtransporter->where("mechanic_id", $mechanic_id);
		$q = $this->dbtransporter->get("mechanic");
		$rows = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Mechanic Not Available !")));
			return;	
		}
		$data = $rows;
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function getvehiclebyid()
	{
		header('Access-Control-Allow-Origin:*');
		$mobilid = isset($_POST['mobilid']) ? trim($_POST['mobilid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("mobil_id", $mobilid);
		$this->dbtransporter->where("mobil_status", 1);
		$q = $this->dbtransporter->get("mobil");
		$rows = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Vehicle Not Available !")));
			return;	
		}
		$data = $rows;
		exit(json_encode(array("data"=>$data)));
		return;
	}
	function getservicebyid()
	{
		header('Access-Control-Allow-Origin:*');
		$serviceid = isset($_POST['serviceid']) ? trim($_POST['serviceid']) : "";
		$companyid = isset($_POST['companyid']) ? trim($_POST['companyid']) : "";
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("service_id", $serviceid);
		$q = $this->dbtransporter->get("service");
		$rows = $q->result();
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Service Not Available !")));
			return;	
		}
		$data = $rows;
		
		$this->dbtransporter->where('mobil_status',1);
		$this->dbtransporter->where('mobil_company',$companyid);
		$q = $this->dbtransporter->get('mobil');
		$datamobil = $q->result();
		
		$this->dbtransporter->where('workshop_company',$companyid);
		$this->dbtransporter->where('workshop_status',1);
		$q = $this->dbtransporter->get('workshop');
		$dataworkshop = $q->result();
		
		$this->dbtransporter->where('driver_company',$companyid);
		$this->dbtransporter->where('driver_status',1);
		$q = $this->dbtransporter->get('driver');
		$datadriver = $q->result();
		
		$this->dbtransporter->where('mechanic_company',$companyid);
		$this->dbtransporter->where('mechanic_status',1);
		$q = $this->dbtransporter->get('mechanic');
		$datamechanic = $q->result();
		
		exit(json_encode(array("datamobil"=>$datamobil,"dataworkshop"=>$dataworkshop,"datadriver"=>$datadriver,"datamechanic"=>$datamechanic,"data"=>$data)));
		return;
	}
	function savedriver()
	{
		header('Access-Control-Allow-Origin:*');
		$driver_id = $this->input->post('driverid');
		$driver_name = isset($_POST['driver_name']) ? $_POST['driver_name'] : "";
		$driver_phone = isset($_POST['driver_phone']) ? $_POST['driver_phone'] : 0;
		$driver_licence_no = isset($_POST['driver_licence_no']) ? $_POST['driver_licence_no'] : "";
		$driver_vehicle = isset($_POST['initvehicle_driver']) ? $_POST['initvehicle_driver'] : "";
		unset($data);
		$data['driver_name'] = $driver_name;
		$data['driver_phone'] = $driver_phone;
		$data['driver_licence_no'] = $driver_licence_no;
		$data['driver_vehicle'] = $driver_vehicle;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('driver_id', $driver_id);
		$this->dbtransporter->update('driver', $data);
		exit(json_encode(array("m"=>"Update Driver Success!")));
		return;	
	}
	function savevehicle()
	{
		header('Access-Control-Allow-Origin:*');
		$mobilid = $this->input->post('mobilid');
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : 0;
		unset($data);
		$data['mobil_name'] = $mobil_name;
		$data['mobil_no'] = $mobil_no;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('mobil_id', $mobilid);
		$this->dbtransporter->update('mobil', $data);
		exit(json_encode(array("m"=>"Update Vehicle Success!")));
		return;	
	}
	function saveservice()
	{
		header('Access-Control-Allow-Origin:*');
		$serviceid = $this->input->post('serviceid');
		$service_mobil = isset($_POST['initvehicle_service_edit']) ? $_POST['initvehicle_service_edit'] : 0;
		$service_driver = isset($_POST['initdriver_service_edit']) ? $_POST['initdriver_service_edit'] : 0;
		$service_workshop = isset($_POST['initworkshop_service_edit']) ? $_POST['initworkshop_service_edit'] : 0;
		$service_mechanic = isset($_POST['initdriver_service']) ? $_POST['initdriver_service'] : 0;
		$service_invoice = isset($_POST['invoice_no']) ? $_POST['invoice_no'] : "";
		$service_cost = isset($_POST['service_cost']) ? $_POST['service_cost'] : 0;
		$service_note = isset($_POST['service_note']) ? $_POST['service_note'] : 0;
		unset($data);
		$data['service_mobil'] = $service_mobil;
		$data['service_driver'] = $service_driver;
		$data['service_workshop'] = $service_workshop;
		$data['service_mechanic'] = $service_mechanic;
		$data['service_invoice'] = $service_invoice;
		$data['service_cost'] = $service_cost;
		$data['service_note'] = $service_note;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('service_id', $serviceid);
		$this->dbtransporter->update('service',$data);
		exit(json_encode(array("m"=>"Update Service Success!")));
		return;	
	}
	function saveworkshop()
	{
		header('Access-Control-Allow-Origin:*');
		$workshopid = $this->input->post('workshopid');
		$workshop_name = isset($_POST['workshop_name']) ? $_POST['workshop_name'] : "";
		$workshop_telp = isset($_POST['workshop_telp']) ? $_POST['workshop_telp'] : 0;
		$workshop_fax = isset($_POST['workshop_fax']) ? $_POST['workshop_fax'] : "";
		$workshop_address = isset($_POST['workshop_address']) ? $_POST['workshop_address'] : "";
		unset($data);
		$data['workshop_name'] = $workshop_name;
		$data['workshop_telp'] = $workshop_telp;
		$data['workshop_fax'] = $workshop_fax;
		$data['workshop_address'] = $workshop_address;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('workshop_id', $workshopid);
		$this->dbtransporter->update('workshop', $data);
		exit(json_encode(array("m"=>"Update Workshop Success!")));
		return;	
	}
	function savemechanic()
	{
		header('Access-Control-Allow-Origin:*');
		$mechanic_id = isset($_POST['edit_mechanic_id']) ? $_POST['edit_mechanic_id'] : "";
		$mechanic_name = isset($_POST['edit_mechanic_name']) ? $_POST['edit_mechanic_name'] : "";
		$mechanic_phone = isset($_POST['edit_mechanic_phone']) ? $_POST['edit_mechanic_phone'] : 0;
		$mechanic_mobile = isset($_POST['edit_mechanic_mobile']) ? $_POST['edit_mechanic_mobile'] : 0;
		$mechanic_address = isset($_POST['edit_mechanic_address']) ? $_POST['edit_mechanic_address'] : "";
		unset($data);
		$data['mechanic_name'] = $mechanic_name;
		$data['mechanic_phone'] = $mechanic_phone;
		$data['mechanic_mobile'] = $mechanic_mobile;
		$data['mechanic_address'] = $mechanic_address;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('mechanic_id', $mechanic_id);
		$this->dbtransporter->update('mechanic',$data);
		exit(json_encode(array("m"=>"Update Mechanic Success!")));
		return;	
	}
	function savenewdriver()
	{
		header('Access-Control-Allow-Origin:*');
		$driver_company = isset($_POST['adddrivercompany']) ? $_POST['adddrivercompany'] : "";
		$driver_name = isset($_POST['add_driver_name']) ? $_POST['add_driver_name'] : "";
		$driver_phone = isset($_POST['add_driver_phone']) ? $_POST['add_driver_phone'] : 0;
		$driver_licence_no = isset($_POST['add_driver_license_no']) ? $_POST['add_driver_license_no'] : "";
		$driver_vehicle = isset($_POST['initvehicle_foradddriver']) ? $_POST['initvehicle_foradddriver'] : "";
		unset($data);
		$data['driver_name'] = $driver_name;
		$data['driver_phone'] = $driver_phone;
		$data['driver_licence_no'] = $driver_licence_no;
		$data['driver_company'] = $driver_company;
		$data['driver_vehicle'] = $driver_vehicle;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->insert('driver', $data);
		exit(json_encode(array("m"=>"Add New Driver Success!")));
		return;	
	}
	function savenewservice()
	{
		header('Access-Control-Allow-Origin:*');
		$iscompany = isset($_POST['addservicecompany']) ? $_POST['addservicecompany'] : "";
		$service_mobil = isset($_POST['initvehicle_service']) ? $_POST['initvehicle_service'] : 0;
		$service_driver = isset($_POST['initdriver_service']) ? $_POST['initdriver_service'] : 0;
		$service_workshop = isset($_POST['initworkshop_service_edit']) ? $_POST['initworkshop_service_edit'] : 0;
		$service_mechanic = isset($_POST['initmechanic_service_edit']) ? $_POST['initmechanic_service_edit'] : 0;
		$service_invoice = isset($_POST['invoice_no']) ? $_POST['invoice_no'] : "";
		$service_cost = isset($_POST['service_cost']) ? $_POST['service_cost'] : 0;
		$service_note = isset($_POST['service_note']) ? $_POST['service_note'] : 0;
		unset($data);
		$data['service_mobil'] = $service_mobil;
		$data['service_driver'] = $service_driver;
		$data['service_workshop'] = $service_workshop;
		$data['service_mechanic'] = $service_mechanic;
		$data['service_invoice'] = $service_invoice;
		$data['service_cost'] = $service_cost;
		$data['service_note'] = $service_note;
		$data['service_company'] = $iscompany;
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$this->dbtransporter->insert('service',$data);
		exit(json_encode(array("m"=>"Add New Service Vehicle Success!")));
		return;	
		
	}
	function savenewvehicle()
	{
		header('Access-Control-Allow-Origin:*');
		$mobil_device = isset($_POST['initvehicle']) ? $_POST['initvehicle'] : "";
		$mobil_name = isset($_POST['add_mobil_name']) ? $_POST['add_mobil_name'] : "";
		$mobil_no = isset($_POST['add_mobil_no']) ? $_POST['add_mobil_no'] : "";
		$mobil_company = isset($_POST['addvehiclecompany']) ? $_POST['addvehiclecompany'] : "";
		unset($data);
		$data['mobil_device'] = $mobil_device;
		$data['mobil_name'] = $mobil_name;
		$data['mobil_no'] = $mobil_no;
		$data['mobil_company'] = $mobil_company;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->insert('mobil', $data);
		exit(json_encode(array("m"=>"Add New Initializing Vehicle Success!")));
		return;	
	}
	function savenewworkshop()
	{
		header('Access-Control-Allow-Origin:*');
		$workshop_company = isset($_POST['addworkshopcompany']) ? $_POST['addworkshopcompany'] : "";
		$workshop_name = isset($_POST['add_workshop_name']) ? $_POST['add_workshop_name'] : "";
		$workshop_telp = isset($_POST['add_workshop_telp']) ? $_POST['add_workshop_telp'] : 0;
		$workshop_fax = isset($_POST['add_workshop_fax']) ? $_POST['add_workshop_fax'] : "";
		$workshop_address = isset($_POST['add_workshop_telp']) ? $_POST['add_workshop_address'] : 0;
		unset($data);
		$data['workshop_name'] = $workshop_name;
		$data['workshop_telp'] = $workshop_telp;
		$data['workshop_fax'] = $workshop_fax;
		$data['workshop_address'] = $workshop_address;
		$data['workshop_company'] = $workshop_company;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->insert('workshop', $data);
		exit(json_encode(array("m"=>"Add New Workshop Success!")));
		return;	
	}
	
	function savenewmechanic()
	{
		header('Access-Control-Allow-Origin:*');
		$addmechaniccompany = isset($_POST['addmechaniccompany']) ? $_POST['addmechaniccompany'] : "";
		$mechanic_name = isset($_POST['mechanic_name']) ? $_POST['mechanic_name'] : "";
		$mechanic_phone = isset($_POST['mechanic_phone']) ? $_POST['mechanic_phone'] : 0;
		$mechanic_mobile = isset($_POST['mechanic_mobile']) ? $_POST['mechanic_mobile'] : "";
		$mechanic_address = isset($_POST['mechanic_address']) ? $_POST['mechanic_address'] : "";
		unset($data);
		$data['mechanic_name'] = $mechanic_name;
		$data['mechanic_phone'] = $mechanic_phone;
		$data['mechanic_mobile'] = $mechanic_mobile;
		$data['mechanic_address'] = $mechanic_address;
		$data['mechanic_company'] = $addmechaniccompany;
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->insert('mechanic', $data);
		exit(json_encode(array("m"=>"Add New Mechanic Success!")));
		return;	
	}
	function searchinvoice()
	{
		header('Access-Control-Allow-Origin:*');
		$userid = isset($_POST['userid']) ? trim($_POST['userid']) : "";
		
		$mv = array();
		$this->db->select("user_id,user_name,user_agent,user_company,user_group");
		$this->db->where("user_status", 1);
		$this->db->where("user_id",$userid);
		$q = $this->db->get("user");
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Not Available !")));
			return;	
		}
		$user = $q->row();
		if($user->user_company > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_company",$user->user_company);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		if($user->user_group > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_group",$user->user_group);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		
		$this->db->select("vehicle_id, vehicle_no, vehicle_name, vehicle_payment_amount");
		$this->db->where("vehicle_user_id",$user->user_id);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		if($q->num_rows > 0)
		{
			$a = $q->result();
			foreach($a as $v)
			{
				$mv[] = $v->vehicle_id;
			}
		}
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 <",date("Ymd"));
		$this->db->where_in("vehicle_id",$mv);
		$q = $this->db->get("vehicle");
		$data = $q->result();
		
		exit(json_encode(array("data"=>$data)));
		return;
	}
	
	/*function getdevices()
	{
		header('Access-Control-Allow-Origin:*');
		$userid = isset($_POST['id']) ? trim($_POST['id']) : "";
		$this->db->select("user_id,user_name,user_agent,user_company,user_group");
		$this->db->where("user_status", 1);
		$this->db->where("user_id",$userid);
		$q = $this->db->get("user");
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Not Available !")));
			return;	
		}
		$user = $q->row();
		
		$this->db->order_by("vehicle_isred", "asc");	
		$this->db->order_by("vehicle_type", "asc");	
		$this->db->order_by("vehicle_no", "asc");	
		$this->db->order_by("vehicle_company", "asc");
		
		$this->db->where("vehicle_user_id",$user->user_id);
		
		//if($user->user_company > 0) { $this->db->where_in("vehicle_company",$user->user_company);}
		if($user->user_group > 0) { $this->db->or_where("vehicle_group",$user->user_group);}
		
		$this->db->where("vehicle_status <>", 3);
		
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Vehicle Not Available !")));
			return;	
		}
		$data = $q->result();
		exit(json_encode(array("data"=>$data)));
		return;
		
	}*/
	
	function getdevices()
	{
		header('Access-Control-Allow-Origin:*');
		$userid = isset($_POST['id']) ? trim($_POST['id']) : "";
		$mv = array();
		
		
		
		$this->db->select("user_id,user_name,user_agent,user_company,user_group");
		$this->db->where("user_status", 1);
		$this->db->where("user_id",$userid);
		$q = $this->db->get("user");
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Not Available !")));
			return;	
		}
		$user = $q->row();
		
		if($user->user_company > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_company",$user->user_company);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		
		
		
		if($user->user_group > 0)
		{
			$this->db->select("vehicle_id");
			$this->db->where("vehicle_group",$user->user_group);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			if($q->num_rows > 0)
			{
				$a = $q->result();
				foreach($a as $v)
				{
					$mv[] = $v->vehicle_id;
				}
			}
		}
		
		$this->db->where("vehicle_user_id",$user->user_id);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		if($q->num_rows > 0)
		{
			$a = $q->result();
			foreach($a as $v)
			{
				$mv[] = $v->vehicle_id;
			}
		}
		
		
		
		$this->db->order_by("vehicle_isred", "asc");	
		//$this->db->order_by("vehicle_type", "asc");	
		$this->db->order_by("vehicle_no", "asc");	
		//$this->db->order_by("vehicle_company", "asc");
		$this->db->where("vehicle_status <>", 3);
		
			$this->db->where_in("vehicle_id",$mv);
		
		$q = $this->db->get("vehicle");
		
		
		if ($q->num_rows() == 0)
		{
			exit(json_encode(array("m"=>"Data Vehicle Not Available !")));
			return;	
		}
		
		
		$data = $q->result();
		exit(json_encode(array("data"=>$data)));
		return;
		
	}
	
	function positions()
	{
		header('Access-Control-Allow-Origin:*');
		$device = isset($_POST['device']) ? $_POST['device'] : "";
		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime'] : 0;
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_id", $device);		
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			exit (json_encode(array("info"=>"", "data"=>"")));
			return;
		}
		$row = $q->row();
		
		if ($row->vehicle_active_date2 && ($row->vehicle_active_date2 < date("Ymd")))
		{
			$row->vehicle_active_date2_fmt = inttodate($row->vehicle_active_date2);
			$row->masaaktif = "Masa Aktif Layanan Sudah Habis!";
			$json = json_decode($row->vehicle_info);
			exit (json_encode(array("info"=>"expired", "data"=>$row)));
			return;
		}
		
		$datakosong = array();
		
		
		$arr = explode("@", $row->vehicle_device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";
		
		$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);
		
		//Tidak ada data GPS
		if(!isset($row))
		{
			exit (json_encode(array("info"=>"GO TO HISTORY", "data"=>$datakosong)));
			return;
		}
		
		if ($this->gpsmodel->fromsocket)
		{
			$datainfo = $this->gpsmodel->datainfo;
			$fromsocket = $this->gpsmodel->fromsocket;			
		}
		$gtps = $this->config->item("vehicle_gtp");		
		
		
		
		if (! in_array(strtoupper($row->vehicle_type), $gtps))
		{			
			$row->status = "-";

			$taktif = dbintmaketime($row->vehicle_active_date, 0);
			
			$json = json_decode($row->vehicle_info);
			if (isset($json->sisapulsa))
			{
				if (strlen($json->masaaktif) == 6)
				{
					$taktif = dbintmaketime1($json->masaaktif, 0);
				}
				else
				{
					$taktif = dbintmaketime2($json->masaaktif, 0);
				}
				
				if (date("Y", $taktif) < 2000)
				{
					$row->pulse = false;
				}
				else
				{
					$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
					$row->masaaktif = date("d/m/Y", $taktif);
				}
			}
			else
			{			
				$row->pulse = false;
			}

		}
		else
		{			
			if (isset($row->gps) && $row->gps && date("Ymd", $row->gps->gps_timestamp) >= date("Ymd"))
			{
				if (! isset($fromsocket))
				{
					$tables = $this->gpsmodel->getTable($row);
					$this->db = $this->load->database($tables["dbname"], TRUE);
				}

			}
			else
			if (! isset($fromsocket))
			{	
				$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
				$this->db = $this->load->database("gpshistory", TRUE);
			}
			
			// ambil informasi di gps_info
			
			if (! isset($datainfo))
			{			
				$this->db->order_by("gps_info_time", "DESC");
				$this->db->where("gps_info_device", strtolower($devices[0])."@".strtolower($devices[1]));
				$q = $this->db->get($tables['info'], 1, 0);
				$totalinfo = $q->num_rows();
				if ($totalinfo)
				{
					$rowinfo = $q->row();
				}
			}
			else
			{
				$rowinfo = $datainfo;
				$totalinfo = 1;
			}
			
			if ($totalinfo == 0)
			{
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
				$row->pulse = "-";
			}
			else
			{															
				$ioport = $rowinfo->gps_info_io_port;
				
				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
				$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1));
				
				//dimatikan sementara, case ENGINE ON nol Kph di app jadi ENG OFF
				/*if(isset($devices[1]) && ($devices[1] == "GT06" || $devices[1] == "A13" || $devices[1] == "TK309" || $devices[1] == "TK309PTO" || $devices[1] == "GT06PTO"))
				{					
					if(isset($row->gps->gps_speed_fmt) && $row->gps->gps_speed_fmt < 3)
					{
						$row->status1 = false;
					}
				}*/ 
				$row->status = $row->status2 || $row->status1 || $row->status3;
				if(!$row->status1)
				{
					$row->gps->gps_speed_fmt = 0;
				}
				
				
				
				$pulses = $this->config->item("vehicle_pulse");
				if (! in_array(strtoupper($row->vehicle_type), $pulses))
				{
					$json = json_decode($row->vehicle_info);
					if (isset($json->sisapulsa))
					{
						if (strlen($json->masaaktif) == 6)
						{
							$taktif = dbintmaketime1($json->masaaktif, 0);
						}
						else
						{
							$taktif = dbintmaketime2($json->masaaktif, 0);
						}

						if (date("Y", $taktif) < 2000)
						{
							$row->pulse = false;
						}
						else
						{
							$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
							$row->masaaktif = date("d/m/Y", $taktif);
						}
					}
					else
					{			
						$row->pulse = false;
					}
				}
				else
				{				
					//$rowinfo->gps_info_ad_input = "00B0742177";
					
					$pulsa = number_format(hexdec(substr($rowinfo->gps_info_ad_input, 0, 5)), 0, "", ".");					
					$aktif = hexdec(substr($rowinfo->gps_info_ad_input, 5));
					
					$taktif = dbintmaketime1($aktif, 0);
							
					if (date("Y", $taktif) < 2000)
					{
						$row->pulse = false;
					}
					else
					{									
						$row->pulse = sprintf("Rp %s", $pulsa);
						$row->masaaktif = date("d/m/Y", $taktif);
					}
				}

				$fuels = $this->config->item("vehicle_fuel");
				if (! in_array(strtoupper($row->vehicle_type), $fuels))
				{
					$row->fuel = false;
				}
				else
				{	
					$row->fuel = "-";
					if($rowinfo->gps_info_ad_input != ""){
						if($rowinfo->gps_info_ad_input != 'FFFFFF' || $rowinfo->gps_info_ad_input != '999999' || $rowinfo->gps_info_ad_input != 'YYYYYY'){
							$fuel_1 = hexdec(substr($rowinfo->gps_info_ad_input, 0, 4));
							$fuel_2 = (hexdec(substr($rowinfo->gps_info_ad_input, 0, 2))) * 0.1;
														
							$fuel = $fuel_1 + $fuel_2;
							//print_r($fuel);exit;
							//Deteksi Fuel Capacity
							
							$this->db = $this->load->database("default", TRUE);
							
							if ($row->vehicle_fuel_capacity != 0 && $row->vehicle_fuel_capacity == 300)
							{
								
								$sql = "SELECT * FROM ( 
										(
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` <= ". $fuel ." 
											ORDER BY fuel_led_resistance DESC 
											LIMIT 1
										)
									) tbldummy";
							}		
							else
							{
								$sql = "SELECT * FROM ( 
										(
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` >= ". $fuel ." 
											ORDER BY fuel_led_resistance ASC 
											LIMIT 1
										) UNION (
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` <= ". $fuel ." 
											ORDER BY fuel_led_resistance DESC 
											LIMIT 1
										) 
									) tbldummy";
							}
							$qfuel = $this->db->query($sql);
							if ($qfuel->num_rows() > 0){
   								$rfuel = $qfuel->result();
								
								if ($qfuel->num_rows() == 1){
									$row->blink = false;
									$row->fuel_scale = $rfuel[0]->fuel_gas_scale * 10;
									$row->fuel = $rfuel[0]->fuel_volume . "L";
								}else{
									$row->blink = true;
									$row->fuel_scale = $rfuel[1]->fuel_gas_scale * 10;
									$row->fuel = $rfuel[0]->fuel_volume . "L - " . $rfuel[1]->fuel_volume . "L";
								}
							}
							
							
						}
					}
					
				}
				$row->totalodometer = round(($rowinfo->gps_info_distance+$row->vehicle_odometer*1000)/1000);
			}
			
		}
		
		if($row->vehicle_type == "TJAM")
			{
				$parse = explode(",",$row->gps->gps_msg_ori);
				if(isset($parse[13]))
				{
					$row->battery = $parse[13];
					if(isset($row->gps->georeverse->display_name))
					{
						$row->gps->georeverse->display_name = $row->gps->georeverse->display_name."<br />"."Battery :"." ".$row->battery."%";
						
					}
				}
			}
			
		$xd = date("Y-m-d",strtotime($row->vehicle_active_date2));
		$xn = date("Y-m-d");
		$dd1 = new DateTime($xd);
		$dd2 = new DateTime($xn);
		$sdd = $dd1->diff($dd2)->format('%a');
		$dnote = "MASA LAYANAN"." ".$sdd." "."HARI LAGI";
		$row->masaaktif = $dnote;
			
		$t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date1_fmt = date("M, jS Y", $t);

		$t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date2_fmt = date("M, jS Y", $t);
		
		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";
		
		
		$row->vehicle_device_name = $devices[0];
		$row->vehicle_device_host = $devices[1];
		
		$params["vehicle"] = $row;

		if (! $row->gps)
		{
			echo json_encode(array("info"=>"GO TO HISTORY", "vehicle"=>$row, "data"=>$datakosong));
			return;
		}
		$delayresatrt = mktime() - $row->gps->gps_timestamp;
		$kdelayrestart = $this->config->item("restart_delay")*60;
		
		if (true)
		{
			$restart = $this->smsmodel->restart($row->vehicle_type, $row->vehicle_operator);
			$row->restartcommand = $restart;					
		}
		else
		{
			$row->restartcommand = "";
		}
		
		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_T1")))
		{
			$row->checkpulsa = $this->smsmodel->checkpulse($row->vehicle_operator);
		}
		else
		{
			$row->checkpulsa = "";
		}
		
		$row->geofence_location = $this->getGeofence_location($row->gps->gps_longitude, $row->gps->gps_ew, $row->gps->gps_latitude, $row->gps->gps_ns, $row->vehicle_device);   
		
		if ($row->vehicle_type == "T5DOOR" || $row->vehicle_type == "T5FAN" || $row->vehicle_type == "T5PTO")
		{
			$row->fan = $this->getFanStatus($row->gps->gps_msg_ori);
		}
		
		
		
		if(isset($row->gps))
		{
			exit (json_encode(array("info"=>"", "data"=>$row)));	
		}
		else
		{
			exit (json_encode(array("info"=>"GO TO HISTORY", "data"=>$datakosong)));
		}
		
		
		return;
	}
	function gethistory()
	{
		header('Access-Control-Allow-Origin:*');
		$devhist = isset($_POST['devhist']) ? $_POST['devhist'] : "";
		$sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
		$stime = isset($_POST['stime']) ? $_POST['stime'] : "";
		$etime = isset($_POST['etime']) ? $_POST['etime'] : "";
		$limit = 100;
		$offset = 0;
		$sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
		$edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":00"));
		
		new DateTime('2014-01-03');
		
		$this->db->where("vehicle_id", $devhist);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		
		$sdatefmt = new DateTime($sdatefmt);
		$edatefmt = new DateTime($edatefmt);
		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309")	
		{
			$sdatefmt->modify('-7 hour');
			$edatefmt->modify('-7 hour');
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
		}
		else
		{
			$sdatefmt->modify('-0 hour');
			$edatefmt->modify('-0 hour');
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
		}
		$sdatefmt = $sdatefmt->format('Y-m-d H:i:s');
		$edatefmt = $edatefmt->format('Y-m-d H:i:s');
		
		$isdate = date("Y-m-d",strtotime($sdatefmt));
		
		$ex = explode("@",$rowvehicle->vehicle_device);
		$name = $ex[0];
		$host = $ex[1];
		
		$json = json_decode($rowvehicle->vehicle_info);
		
		$tables = $this->gpsmodel->getTable($rowvehicle);		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		$params['vehicle'] = $rowvehicle;
		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));
		
		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
		
		$totalodometer = 0;
		$totalodometer1 = 0;
		
		if($isdate == date("Y-m-d"))
		{
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
			$this->db->order_by("gps_info_time", "asc");
			$this->db->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
			$this->db->where("gps_info_device", $ex[0]."@".$ex[1]);
			$this->db->where("gps_info_time >=", $sdatefmt);
			$this->db->where("gps_info_time <=", $edatefmt);	
			$q = $this->db->get($tables["info"]);
			$rowlastinfos = $q->result();
		}
		else
		{
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tablehist);
			$rowshist = $q->result();
			
			$rows = array_merge($rows, $rowshist);
			
			$total = count($rows);
			/*
			if (count($rowlastinfos))
			{					
				$totalodometer = $rowlastinfos[0]->gps_info_distance;
				$this->db->order_by("gps_info_time", "asc");
				$this->db->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->db->where("gps_info_device", $ex[0]."@".$ex[1]);
				$this->db->where("gps_info_time >=", $sdatefmt);
				$this->db->where("gps_info_time <=", $edatefmt);	
				$q = $this->db->get($tables["info"]);
				$rowlastinfos = $q->result();
			}*/
		}
		
		for($i=count($rows)-1; $i >= 0; $i--)
		{
			if (($i+1) >= count($rows))
			{
				$rowsummary[] = $rows[$i];
				continue;
			}
			$latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
			$lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
			$latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			$lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
			{
				$rowsummary[] = $rows[$i];
				continue;
			}
			if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
			{
				$rowsummary[] = $rows[$i];
				continue;
			}
		}
		
		$rows = array();
		$total = 0;
		if (isset($rowsummary))
		{
			$rowsummary = array_reverse($rowsummary);			
			$total = count($rowsummary);
			$rows = array_splice($rowsummary, $offset, $limit);							
		}
		
		unset($map_params);
		$ismove = false;
		$lastcoord = false;
		
		for($i=0; $i < count($rows); $i++)
		{
		
			if ($i == 0)
			{
				// ambil info
				
				$tinfo2 = dbmaketime($rows[0]->gps_time);
				$tinfo1 = dbmaketime($rows[count($rows)-1]->gps_time);
								
				if ($tinfo1 > $yesterday)
				{
					if (isset($json->vehicle_ws))
					{
						if ($tinfo1 > $yesterday)
						{
							$this->db = $this->load->database("gpshistory2", TRUE);							
						}
						else
						{
							$istbl_history = $this->config->item("dbhistory_default");
							if($this->config->item("is_dbhistory") == 1)
							{
								$istbl_history = $rowvehicle->vehicle_dbhistory_name;
							}
							$this->db = $this->load->database($istbl_history, TRUE);
						}
						
						$rowinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0);
					}
					else
					{	
						$this->db = $this->load->database($tables["dbname"], TRUE);	
						$rowinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $tinfo1, $tinfo2,  0);
					}
				}
				else
				if ($tinfo2 <= $yesterday)
				{	
					
					if (!isset($json->vehicle_ws))
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
					}
					else
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos1 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
					
						$this->db = $this->load->database("gpshistory2", TRUE);
						$rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
						$rowinfos = array_merge($rowinfos1, $rowinfos2);
					}
				}
				else
				{
					
					if ((!isset($json->vehicle_ws)))
					{
						$this->db = $this->load->database($tables["dbname"], TRUE);	
						$rowinfos1 = $this->historymodel->allinfo($tables["info"], $name, $host, $yesterday, $tinfo2,  0);
					
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
					}
					else
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos1 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0);
					
						$this->db = $this->load->database("gpshistory2", TRUE);
						$rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0);
					}
					
					$rowinfos = array_merge($rowinfos1, $rowinfos2);
				}
								
				for($j=0; $j < count($rowinfos); $j++)
				{
					$infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
				}
			}
			
			$rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
						
			// T6 Invalid condition
			if ($rowvehicle->vehicle_type == "T6" && $rows[$i]->gps_status == "V")
			{
				$tables = $this->gpsmodel->getTable($rowvehicle);
				$this->db = $this->load->database($tables["dbname"], TRUE);
				
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_time <=", date("Y-m-d H:i:s"));
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$this->db->where("gps_latitude <>", 0);
				$this->db->where("gps_longitude <>", 0);
				$this->db->where("gps_status", "A");
				$q_lastvalid = $this->db->get($tables['gps']);
					
				if ($q_lastvalid->num_rows() == 0) 
				{
					$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
						
					$this->db->limit(1);
					$this->db->order_by("gps_time", "desc");
					$this->db->where("gps_name", $name);
					$this->db->where("gps_host", $host);
					$this->db->where("gps_latitude <>", 0);
					$this->db->where("gps_longitude <>", 0);
					$this->db->where("gps_status", "A");
					$q_lastvalid = $this->db->get($tablehist);
						
					if ($q_lastvalid->num_rows() == 0) return;
				}
					
				$row_lastvalid = $q_lastvalid->row();
				$rows[$i]->gps_longitude_real = getLongitude($row_lastvalid->gps_longitude, $row_lastvalid->gps_ew);
				$rows[$i]->gps_latitude_real = getLatitude($row_lastvalid->gps_latitude, $row_lastvalid->gps_ns);	
			}
			else
			{
				if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309")
				{
					$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
					$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
			}
			
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");		
			
			if ($i == 0)
			{
				$lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
			}
			else
			{
				if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
				{
					$ismove = true;
				}
			}
			
			
			if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309")
			{
				$rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
				$rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			}
			else
			{
				$rows[$i]->gps_date_fmt = date("d/m/Y", strtotime($rows[$i]->gps_time));
				$rows[$i]->gps_time_fmt = date("H:i:s", strtotime($rows[$i]->gps_time));
			}

			$rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			$rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
			
			if (isset($infos[$rows[$i]->gps_timestamp]))
			{
				$ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;				
				if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK309")
				{
					if($rows[$i]->gps_speed_fmt > 0)
					{
						$rows[$i]->status1 = $this->lang->line('lon');
					}
					else
					{
						$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
					}
				}
				else
				{
					$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
				}
				$rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle->vehicle_odometer*1000)/1000), 0, "", ","); 
			}
			else
			{
				$rows[$i]->status1 = "-";
				$rows[$i]->odometer = "-";
			}
									
			$rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);			
			
			$rows[$i]->gpsindex = $i+1;
			
			if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309")
			{
				$rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
				$rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			}
			else
			{
				$rows[$i]->gpsdate = date("d/m/Y", strtotime($rows[$i]->gps_time));
				$rows[$i]->gpstime = date("H:i:s", strtotime($rows[$i]->gps_time));
			}
			
			$rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
			$rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
			$rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
			
			//Fan || Dooe
			$appfan = $this->config->item("fan_app");
			if ($rowvehicle->vehicle_type == "T5FAN" || $rowvehicle->vehicle_type == "T5DOOR" || $rowvehicle->vehicle_type == "T5PTO")
			{
				$rows[$i]->fan = $this->getFanStatus($rows[$i]->gps_msg_ori);
			}
		}
		
		
		exit (json_encode(array("data"=>$rows)));	
		return;
		
	}
	
	function getoverspeed()
	{
		header('Access-Control-Allow-Origin:*');
		$devhist = isset($_POST['devhist_overspeed']) ? $_POST['devhist_overspeed'] : "";
		
		$sdate = isset($_POST['sdate_overspeed']) ? $_POST['sdate_overspeed'] : "";
		
		$stime = isset($_POST['stime_overspeed']) ? $_POST['stime_overspeed'] : "";
		$etime = isset($_POST['etime_overspeed']) ? $_POST['etime_overspeed'] : "";
		$speed = isset($_POST['speed']) ? $_POST['speed'] : 0;
		$limit = 100;
		$offset = 0;
		$sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
		$edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":00"));
		
		new DateTime('2014-01-03');
		
		$sdatefmt = new DateTime($sdatefmt);
		$sdatefmt->modify('-7 hour');
		$sdatefmt = $sdatefmt->format('Y-m-d H:i:s');
		
		$edatefmt = new DateTime($edatefmt);
		$edatefmt->modify('-7 hour');
		$edatefmt = $edatefmt->format('Y-m-d H:i:s');
		
		$isdate = date("Y-m-d",strtotime($sdatefmt));
		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
		
		$this->db->where("vehicle_id", $devhist);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		
		$ex = explode("@",$rowvehicle->vehicle_device);
		$name = $ex[0];
		$host = $ex[1];
		
		$json = json_decode($rowvehicle->vehicle_info);
		
		$tables = $this->gpsmodel->getTable($rowvehicle);		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		$params['vehicle'] = $rowvehicle;
		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));
		
		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
		
		if($isdate == date("Y-m-d"))
		{
			$this->db->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns");
			$this->db->order_by("gps_time", "DESC");
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$this->db->where("gps_speed >=", $speed/1.852);
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
		}
		else
		{
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$this->db->where("gps_speed >=", $speed/1.852);
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$this->db->where("gps_speed >=", $speed/1.852);
			$q = $this->db->get($tablehist);
			$rowshist = $q->result();
			$rows = array_merge($rows, $rowshist);
		}
		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
			$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");		

			$rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
			$rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			
			$rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			if (isset($positions[$rows[$i]->gps_longitude_real_fmt][$rows[$i]->gps_longitude_real_fmt]))
			{
				$rows[$i]->georeverse = $positions[$rows[$i]->gps_longitude_real_fmt][$rows[$i]->gps_longitude_real_fmt];
			}
			else
			{
				$rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);
			}
			
			$positions[$rows[$i]->gps_longitude_real_fmt][$rows[$i]->gps_longitude_real_fmt] = $rows[$i]->georeverse;
			
			$rows[$i]->gpsindex = $i+1;
			$rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp);
			$rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp);
			$rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
		}
		exit (json_encode(array("data"=>$rows)));	
		return;	
	}
	
	function getparkingtime()
	{
		header('Access-Control-Allow-Origin:*');
		$devhist = isset($_POST['devhist_parkingtime']) ? $_POST['devhist_parkingtime'] : "";
		
		$sdate = isset($_POST['sdate_parkingtime']) ? $_POST['sdate_parkingtime'] : "";
		$stime = isset($_POST['stime_parkingtime']) ? $_POST['stime_parkingtime'] : "";
		$etime = isset($_POST['etime_parkingtime']) ? $_POST['etime_parkingtime'] : "";
		//$max = isset($_POST['parkingtime']) ? $_POST['parkingtime'] : 0;
		//$max = $max*60;
		
		$limit = 100;
		$offset = 0;
		$sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
		$edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":00"));
		
		new DateTime('2014-01-03');
		
		$sdatefmt = new DateTime($sdatefmt);
		$sdatefmt->modify('-7 hour');
		$sdatefmt = $sdatefmt->format('Y-m-d H:i:s');
		
		$edatefmt = new DateTime($edatefmt);
		$edatefmt->modify('-7 hour');
		$edatefmt = $edatefmt->format('Y-m-d H:i:s');
		
		$isdate = date("Y-m-d",strtotime($sdatefmt));
		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
		
		$this->db->where("vehicle_id", $devhist);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		
		$ex = explode("@",$rowvehicle->vehicle_device);
		$name = $ex[0];
		$host = $ex[1];
		
		$json = json_decode($rowvehicle->vehicle_info);
		
		$tables = $this->gpsmodel->getTable($rowvehicle);		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		$params['vehicle'] = $rowvehicle;
		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));
		
		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
		
		if($isdate == date("Y-m-d"))
		{
			$this->db->order_by("gps_time", "DESC");
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
		}
		else
		{
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tablehist);
			$rowshist = $q->result();
			$rows = array_merge($rows, $rowshist);
		}
		
		
		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		$lastlng = "";
		$lastlat = "";
		$vehicles = array();
		$j = -1;
		
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
			$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");		

			$rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
			$rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			
			$rows[$i]->geofence = $this->getGeofence_location($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_device);
			//print_r($rows[$i]->geofence);exit;
			
			$speed = $rows[$i]->gps_speed*1.852;
			
			if ($speed > 1)
			{
				if ($j == -1) continue;
				
				$idx = count($vehicles)-1;
				
				$vehicles[$idx]->parkingtime = $rows[$i]->gps_timestamp - $rows[$j]->gps_timestamp;
				$vehicles[$idx]->parkingtime_fmt = sprintf("%d:%02d:%02d", floor($vehicles[$idx]->parkingtime/3600), floor(($vehicles[$idx]->parkingtime%3600)/60), floor(($vehicles[$idx]->parkingtime%3600)%60));
				
				$j = -1;
				continue;	
			}
			
			if ($j != -1) continue;
			
			$j = $i;
			$rows[$i]->parkingtime = 0;
			$vehicles[] = $rows[$i];
		}
		
		
		if ($j != -1)
		{
			$idx = count($vehicles)-1;
			$vehicles[$idx]->parkingtime = $rows[count($rows)-1]->gps_timestamp - $rows[$j]->gps_timestamp;
			$vehicles[$idx]->parkingtime_fmt = sprintf("%d:%02d:%02d", floor($vehicles[$idx]->parkingtime/3600), floor(($vehicles[$idx]->parkingtime%3600)/60), floor(($vehicles[$idx]->parkingtime%3600)%60));
		}
		
		$max = $_POST['parkingtime']*60;;
				
		$temp = array();		
		for($i=0; $i < count($vehicles); $i++)
		{
			if ($vehicles[$i]->parkingtime < $max) continue;
									
			$temp[] = $vehicles[$i];
		}		
		
		$vehicles = $temp;
		for($i=0; $i < count($vehicles); $i++)
		{
			$vehicles[$i]->georeverse = $this->gpsmodel->GeoReverse($vehicles[$i]->gps_latitude_real_fmt, $vehicles[$i]->gps_longitude_real_fmt);
			$vehicles[$i]->gpsindex = $i+1;
			$vehicles[$i]->gpsdate = date("d/m/Y", $vehicles[$i]->gps_timestamp+7*3600);
			$vehicles[$i]->gpstime = date("H:i:s", $vehicles[$i]->gps_timestamp+7*3600);
			$vehicles[$i]->gpsaddress = $vehicles[$i]->georeverse->display_name;
			$vehicles[$i]->gpscoord = "(".$vehicles[$i]->gps_longitude_real_fmt." ".$vehicles[$i]->gps_latitude_real_fmt.")";
		}
		print_r($vehicles);exit;
		exit (json_encode(array("data"=>$vehicles)));	
		return;	
		
	}
	
	function gettripmileage()
	{
		header('Access-Control-Allow-Origin:*');
		$devhist = isset($_POST['devhist_tripmileage']) ? $_POST['devhist_tripmileage'] : "";
		
		$sdate = isset($_POST['sdate_tripmileage']) ? $_POST['sdate_tripmileage'] : "";
		$stime = isset($_POST['stime_tripmileage']) ? $_POST['stime_tripmileage'] : "";
		$etime = isset($_POST['etime_tripmileage']) ? $_POST['etime_tripmileage'] : "";
		
		$limit = 100;
		$offset = 0;
		$sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
		$edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":00"));
		
		new DateTime('2014-01-03');
		
		$sdatefmt = new DateTime($sdatefmt);
		$sdatefmt->modify('-7 hour');
		$sdatefmt = $sdatefmt->format('Y-m-d H:i:s');
		
		$edatefmt = new DateTime($edatefmt);
		$edatefmt->modify('-7 hour');
		$edatefmt = $edatefmt->format('Y-m-d H:i:s');
		
		$isdate = date("Y-m-d",strtotime($sdatefmt));
		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
		
		$this->db->where("vehicle_id", $devhist);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		
		$ex = explode("@",$rowvehicle->vehicle_device);
		$name = $ex[0];
		$host = $ex[1];
		
		$json = json_decode($rowvehicle->vehicle_info);
		
		$tables = $this->gpsmodel->getTable($rowvehicle);		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		$params['vehicle'] = $rowvehicle;
		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));
		
		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
		
		if($isdate == date("Y-m-d"))
		{
			$this->db->join("gps_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
		}
		else
		{
			$this->db->join("gps_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$this->db->join("_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tablehist);
			$rowshist = $q->result();
			$rows = array_merge($rows, $rowshist);
			$total = count($rows);
		}
		
		$data = array();
		$nopol = "";
		$on = false;
		$trows = count($rows);
		
		for($i=0;$i<$trows;$i++)
		{
			if($nopol != $rowvehicle->vehicle_no)
			{ //new vehicle
				if($on && $i!=0){ 
					$data["nopol"][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data["nopol"][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
					$data["nopol"][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
					$data["nopol"][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){								
					$trip_no = 1;					
					$data["nopol"][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data["nopol"][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
					$data["nopol"][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					$data["nopol"][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data["nopol"][$trip_no-1]['vehicle_name'] =$rowvehicle->vehicle_name;
					$on = true;
					
					if($i==$trows-1){												
						$data["nopol"][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data["nopol"][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data["nopol"][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data["nopol"][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;
										
				}
			}
			else
			{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){	
												
						$data["nopol"][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data["nopol"][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data["nopol"][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data["nopol"][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data["nopol"][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
					}
					
					$on = true;	
					if($i==$trows-1 && $on){												
						$data["nopol"][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data["nopol"][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data["nopol"][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data["nopol"][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}									
				}else{			
					if($on){
						$data["nopol"][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));	
						$data["nopol"][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data["nopol"][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data["nopol"][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;
													
				}
			}
			$nopol = $rowvehicle->vehicle_no;
		}		
		if(isset($data["nopol"]))
		{
			$data = json_decode(json_encode($data["nopol"]));
		}
		else
		{
			$data = array();
		}
		exit (json_encode(array("data"=>$data)));	
		return;	
	}
	
	function getplayback()
	{
		header('Access-Control-Allow-Origin:*');
		$devhist = isset($_POST['devhist_playback']) ? $_POST['devhist_playback'] : "";
		
		$sdate = isset($_POST['sdate_playback']) ? $_POST['sdate_playback'] : "";
		$stime = isset($_POST['stime_playback']) ? $_POST['stime_playback'] : "";
		$etime = isset($_POST['etime_playback']) ? $_POST['etime_playback'] : "";
		
		$limit = 100;
		$offset = 0;
		$sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
		$edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":00"));
		
		new DateTime('2014-01-03');
		
		$sdatefmt = new DateTime($sdatefmt);
		$sdatefmt->modify('-7 hour');
		$sdatefmt = $sdatefmt->format('Y-m-d H:i:s');
		
		$edatefmt = new DateTime($edatefmt);
		$edatefmt->modify('-7 hour');
		$edatefmt = $edatefmt->format('Y-m-d H:i:s');
		
		$isdate = date("Y-m-d",strtotime($sdatefmt));
		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
		
		$this->db->where("vehicle_id", $devhist);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		
		$ex = explode("@",$rowvehicle->vehicle_device);
		$name = $ex[0];
		$host = $ex[1];
		
		$json = json_decode($rowvehicle->vehicle_info);
		
		$tables = $this->gpsmodel->getTable($rowvehicle);		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		$params['vehicle'] = $rowvehicle;
		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));
		
		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
		
		if($isdate == date("Y-m-d"))
		{
			$this->db->join("gps_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
		}
		else
		{
			$this->db->join("gps_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tables["gps"]);
			$rows = $q->result();
			
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$this->db->join("_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $ex[0]);
			$this->db->where("gps_host", $ex[1]);
			$this->db->where("gps_time >=", $sdatefmt);
			$this->db->where("gps_time <=", $edatefmt);	
			$q = $this->db->get($tablehist);
			$rowshist = $q->result();
			$rows = array_merge($rows, $rowshist);
			$total = count($rows);
		}
		
		//print_r($rows);exit;
		$data = array(); // initialization variable
		$vehicle_device = "";
		$engine = "";
		$nopol = "";
		
		foreach($rows as $obj)
		{
			if($vehicle_device != $rowvehicle->vehicle_device)
			{
				$no=0;
				$no_data = 1;
			}
			//engine ON
			if(substr($obj->gps_info_io_port, 4, 1) == 1)
			{ 
				if($engine != "ON") 
				{
					$no++;
					$no_data = 1;
				}
				if($no == 0) $no++;
				if($no_data == 1)
				{
					$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['start'] = $obj;
				}
				else
				{
					$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['end'] = $obj;
				}
				$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['engine'] = "ON";
				$no_data++;
				$engine = "ON";
			}
			//engine OFF
			if(substr($obj->gps_info_io_port, 4, 1) == 0)
			{ 
				if($engine != "OFF") 
				{
					$no++;
					$no_data = 1;
				}
				if($no == 0) $no++;
				if($no_data == 1)
				{
					$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['start'] = $obj;
				}
				else
				{
					$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['end'] = $obj;
				}
				$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['engine'] = "OFF";
				$no_data++;
				$engine = "OFF";
			}
			$vehicle_device = $rowvehicle->vehicle_device;
		}
		
		foreach($data as $vehicles=>$value_vehicles)
		{
			foreach($value_vehicles as $number=>$value_number)
			{
				foreach($value_number as $engine=>$report)
				{
					if(!isset($report['end']))
					{
						$report['end'] = $report['start'];
					}
					$data_report[$vehicles][$number][$engine]['start'] = $report['start'];
					$data_report[$vehicles][$number][$engine]['end'] = $report['end'];
					$data_report[$vehicles][$number][$engine]['status'] = $report['engine'];
					
					$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
					$show_duration = "";
					if($duration[0]!=0)
					{
						$show_duration .= $duration[0] ." Day ";
					}
					if($duration[1]!=0)
					{
						$show_duration .= $duration[1] ." Hour ";
					}
					if($duration[2]!=0)
					{
						$show_duration .= $duration[2] ." Min";
					}
					if($show_duration == "")
					{
						$show_duration .= "0 Min";
					}
					$data_report[$vehicles][$number][$engine]['duration'] = $show_duration;
					$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
					$data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);
					$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
					$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
					$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
					$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
					$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
					$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
					$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
					$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
				}
			}
		}
		
		//print_r($data_report[$vehicles]);exit;
		if(isset($data_report[$vehicles]))
		{
			$data = json_decode(json_encode($data_report[$vehicles]));
		}
		else
		{
			$data = array();
		}
		exit (json_encode(array("data"=>$data)));	
		return;	
	}
	function getgeofence()
	{
		header('Access-Control-Allow-Origin:*');
		$devhist = isset($_POST['devhist_geofence']) ? $_POST['devhist_geofence'] : "";
		
		$sdate = isset($_POST['sdate_geofence']) ? $_POST['sdate_geofence'] : "";
		$stime = isset($_POST['stime_geofence']) ? $_POST['stime_geofence'] : "";
		$etime = isset($_POST['etime_geofence']) ? $_POST['etime_geofence'] : "";
		
		$limit = 100;
		$offset = 0;
		$sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
		$edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":00"));
		
		new DateTime('2014-01-03');
		
		$this->db->where("vehicle_id", $devhist);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		
		$this->db->order_by("geoalert_id","desc");
		$this->db->join("geofence","geofence_id = geoalert_geofence","left_outer");
		$this->db->select("geofence_name, geoalert_id,geoalert_vehicle,geoalert_direction,geoalert_time,geoalert_geofence");
		$this->db->where("geoalert_time >=",$sdatefmt);
		$this->db->where("geoalert_time <=",$edatefmt);
		$this->db->where("geoalert_vehicle",$rowvehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		$rows = $q->result();
		
		if(isset($rows) && count($rows)>0)
		{
			$data = json_decode(json_encode($rows));
		}
		else
		{
			$data = array();
		}
		exit (json_encode(array("data"=>$data)));	
		return;	
		
	}
	
	
	function getGeofence_location($longitude, $ew, $latitude, $ns) 
	{
		$this->db = $this->load->database("default", TRUE);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);
        $geo_name = "''";
		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
                            AND (geofence_name <> %s)
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $geo_name, $lng, $lat);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{			
			$row = $q->row();
            $data = $row->geofence_name;
            return $data;
		}
		else { return false; }
	}
	function getGeofence_location2($longitude, $latitude, $vehicle_device) {
		$this->db = $this->load->database("default", TRUE);
		
		$lng = $longitude;
		$lat = $latitude;
        $geo_name = "''";

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
                            AND (geofence_name <> %s)
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
                            AND (geofence_vehicle = '%s' ) 
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $geo_name, $lng, $lat, $vehicle_device);
        //print_r($sql);
		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{			
			$row = $q->row();
		
            $data = $row->geofence_name;
			
            return $data;
            
            
		}else
        {
            return false;
        }

	}
	function getPosition($longitude, $ew, $latitude, $ns)
	{
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);
					
		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");	
					
		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
		
		return $georeverse;
	}
	function getFanStatus($val)
	{
		$totstring = strlen($val);
		$value = substr($val, 79, 1);
		return($value);
	}
}
