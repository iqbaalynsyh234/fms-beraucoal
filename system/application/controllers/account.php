<?php
include "base.php";

class Account extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->model("log_model");
		$this->load->helper('common');
	}

	// USER FUNCTION START
	function index()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;
		$user_level		   = $this->sess->user_level;

		if ($privilegecode == 5 || $privilegecode == 6 || $privilegecode == 7 || $privilegecode == 8) {
			redirect(base_url());
		}

		$this->db = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->select("privilege_name, company_name");
			if ($privilegecode == 1) {
				$this->db->where("user_parent", $user_parent);
			}elseif ($privilegecode == 0) {
				$this->db->where("user_id_role >",0);
			}elseif ($privilegecode == 3) {
				$this->db->where("user_parent", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("user_parent", $user_parent);
			}else {
				$this->db->where("user_subgroup", $user_subgroup);
			}
		$this->db->where("user_status", 1);
		$this->db->where("user_id_role <>", 9);
		$this->db->join("masterdata_privilege", "privilege_id = user_id_role", "left");
		$this->db->join("company", "user_company = company_id", "left");
		$this->db->order_by("user_name", "asc");
		$q                               = $this->db->get("user");
		$data_user                       = $q->result_array();

		$this->params['branchoffice']    = $this->getcompany();

	  $this->params['data']            = $data_user;
		$this->params['code_view_menu']  = "masterdata";
		$this->params['privilegecode']   = $privilegecode;

    // echo "<pre>";
    // var_dump($this->params['branchoffice']);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function driver()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;
		$user_level		   = $this->sess->user_level;

		if ($privilegecode == 5 || $privilegecode == 6 || $privilegecode == 7 || $privilegecode == 8) {
			redirect(base_url());
		}

		$this->db = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->select("privilege_name, company_name");
		$this->db->where("user_id_role", 9); //driver
		$this->db->where("user_status", 1);
		$this->db->join("masterdata_privilege", "privilege_id = user_id_role", "left");
		$this->db->join("company", "user_company = company_id", "left");
		$this->db->order_by("user_name", "asc");
		$q                               = $this->db->get("user");
		$data_user                       = $q->result_array();

		$this->params['branchoffice']    = $this->getcompany();

		$this->params['data']            = $data_user;
		$this->params['code_view_menu']  = "configuration";
		$this->params['privilegecode']   = $privilegecode;

		// echo "<pre>";
		// var_dump($this->params['branchoffice']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	// function savepass($id)
	function savepass($id)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$iddelete = isset($_POST['iddelete']) ? trim($_POST['iddelete']): "";
		$oldpass  = isset($_POST['oldpass']) ? trim($_POST['oldpass']):   "";
		$pass     = isset($_POST['pass']) ? trim($_POST['pass']):         "";
		$cpass    = isset($_POST['cpass']) ? trim($_POST['cpass']):       "";

		// echo "<pre>";
		// var_dump($id.'-'.$oldpass.'-'.$pass.'-'.$cpass);die();
		// echo "<pre>";

		if ($this->sess->user_type == 2)
		{
			if (strlen($oldpass) == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lempty_olpassword');

				echo json_encode($callback);
				return;
			}

			$sql = "SELECT * FROM ".$this->db->dbprefix."user WHERE user_pass = PASSWORD('".$oldpass."') AND (user_id = '".$iddelete."')";
			$q = $this->db->query($sql);

			if ($q->num_rows() == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_olpassword');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($pass) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lerror_empty_userpass');

			echo json_encode($callback);
			return;
		}

		if (strlen($pass) < 12)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lpassword_too_short');

			echo json_encode($callback);
			return;
		}

		if(!preg_match("/[A-Z]/", $pass)) {
			$callback['error'] = true;
			$callback['message'] = "Password harus mengandung huruf besar";

			echo json_encode($callback);
			return;
    }
    if(!preg_match("/[a-z]/", $pass)) {
			$callback['error'] = true;
			$callback['message'] = "Password harus mengandung huruf kecil";

			echo json_encode($callback);
			return;
    }
    if(!preg_match('~[0-9]+~', $pass)) {
			$callback['error'] = true;
			$callback['message'] = "Password harus mengandung angka";

			echo json_encode($callback);
			return;
    }

		if ($pass != $cpass)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_cpass');

			echo json_encode($callback);
			return;
		}

		$sql = "UPDATE ".$this->db->dbprefix."user SET user_pass = PASSWORD('".$pass."') WHERE user_id = '".$iddelete."'";

		$callback['error'] = false;
		$callback['message'] = $this->lang->line('lchangepassword_success');

		$this->db->query($sql);
		echo json_encode($callback);
	}

	function changepass($id)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if (isset($this->sess->user_manage_password))
		{
			if (! $this->sess->user_manage_password)
			{
				redirect(base_url());
			}
		}

		$this->db->where("user_id", $id);
		$q = $this->db->get("user");

		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;
			echo json_encode($callback);
			return;
		}

		$row = $q->row();

		$params['row'] = $row;
		$html = $this->load->view("user/changepass", $params, true);

		$callback['html'] = $html;
		$callback['error'] = false;

		echo json_encode($callback);
	}

	function search()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_type == 2)
		{
			if (! $this->sess->user_company)
			{
				redirect(base_url());
			}

			if ($this->sess->user_group)
			{
				redirect(base_url());
			}
		}

		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}
		else
		{
			// cari user dengan company

			$vreplaces = $this->config->item('vehicle_type_replace');

			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_company >", 0);
			$q = $this->db->get("vehicle");

			$rowcompanyvehicles = $q->result();
			for($i=0; $i < count($rowcompanyvehicles); $i++)
			{
				$arr = explode("@", $rowcompanyvehicles[$i]->vehicle_device);

				$rowcompanyvehicles[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
				$rowcompanyvehicles[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
				$rowcompanyvehicles[$i]->isnewport = isset($vreplaces) && isset($vreplaces[$rowcompanyvehicles[$i]->vehicle_type]);

				if (! isset($vcompaniesid))
				{
					$vcompanies[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i];
				}
				else
				if (! isset($vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company]))
				{
					$vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i]->vehicle_id;
				}
				else
				if (! in_array($rowcompanyvehicles[$i]->vehicle_id, $vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company]))
				{
					$vcompanies[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i];
				}

				$vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i]->vehicle_id;
				if ($rowcompanyvehicles[$i]->vehicle_group == 0) continue;

				$vgroups[$rowcompanyvehicles[$i]->vehicle_company][$rowcompanyvehicles[$i]->vehicle_group][] = $rowcompanyvehicles[$i];
			}

			// groups

			$groups = array();
			$grpprocessed = array();

			$this->vehiclemodel->getAllGroups(0, &$groups, &$grpprocessed);

			$this->db->join("group", "group_id = user_group");
			$q = $this->db->get("user");

			$rowusers = $q->result();
			for($i=0; $i < count($rowusers); $i++)
			{
				$childs = array();
				$this->vehiclemodel->getChilds($groups, $rowusers[$i]->user_group, &$childs);

				for($j=0; $j < count($childs); $j++)
				{
					if (! isset($vgroups[$rowusers[$i]->group_company][$childs[$j]])) continue;

					foreach($vgroups[$rowusers[$i]->group_company][$childs[$j]] as $val)
					{
						$vgroups[$rowusers[$i]->group_company][$rowusers[$i]->user_group][] = $val;
					}
				}
			}
		}

		$offset  = isset($_POST['offset']) ? $_POST['offset']:             0;
		$field   = isset($_POST['field']) ? $_POST['field']:               "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword']:           "";
		$vtype   = isset($_POST['vehicle_type']) ? $_POST['vehicle_type']: "";
		$type    = isset($_POST['type']) ? $_POST['type']:                 "";
		$status  = isset($_POST['status']) ? $_POST['status']:             "";
		$sortby  = isset($_POST['sortby']) ? $_POST['sortby']:             "user_login";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby']:           "asc";
		$company = isset($_POST['company']) ? $_POST['company']:           0;
		$groupid = isset($_POST['group']) ? $_POST['group']:               0;

		$this->db->order_by($sortby, $orderby);

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		else
		if ($this->sess->user_type == 2)
		{
			$this->db->where("user_group <> '' OR user_id = ".$this->sess->user_id, null);
			$this->db->where("user_company", $this->sess->user_company);
		}

		switch($field)
		{
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
			break;
			case "user_status":
				$this->db->where("user_status", $status);
			break;
			case "user_type":
				$this->db->where("user_type", $type);
			break;
			case "user_company":
				if ($groupid)
				{
					$this->db->where("user_group", $groupid);
				}
				else
				{
					$this->db->where("user_company", $company);
				}
			break;
			default:
				if (($field != "vvisible") && ($field != "vehicle") && ($field != "device") && ($field != "vexpired") && ($field != "vactive") && ($field != "vehicle_type") && (! $this->config->item('vehicle_type_fixed')) && ($field != "vehicle_card_no"))
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				else
				{
					$this->db->distinct();
					$this->db->select("user.*, agent.*");

					if ($this->config->item('vehicle_type_fixed'))
					{
						$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
					}

					$this->db->join("vehicle", "user_id = vehicle_user_id");

					if ($field == "vexpired")
					{
							$this->db->where("vehicle_active_date2 <", date("Ymd"));
					}
					else
					if ($field == "vactive")
					{
							$this->db->where("vehicle_active_date2 >=", date("Ymd"));
					}
					else
					if ($field == "vehicle")
					{
						$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
					}
					else
					if ($field == "vehicle_type")
					{
						$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
					}
					else
					if ($field == "vvisible")
					{
						$this->db->where("vehicle_status", 1);
					}
					else
					if ($field == "vehicle_card_no")
					{
						$this->db->where("vehicle_card_no LIKE", '%'.$keyword.'%');
					}
					else
					{
						$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
					}
				}
		}

		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$q = $this->db->get("user", $this->config->item("limit_records"), $offset);
		$rows = $q->result();

		// jumlah all vehicle

		if ($this->sess->user_type == 2)
		{
			$this->db->where_in("vehicle_id", $vehicleids);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where_in("user_agent", $this->sess->user_agent);
		}

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		switch ($field)
		{
			case "user_company":
				if ($groupid)
				{
					$this->db->where("user_group", $groupid);
				}
				else
				{
					$this->db->where("user_company", $company);
				}
			break;
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
			break;
			case "user_compay":
				$this->db->where("user_compay", $company);
			break;
			case "vexpired":
				$this->db->where("vehicle_active_date2 <", date("Ymd"));
			break;
			case "vactive":
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			break;
			case "vehicle":
				$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
			break;
			case "device":
				$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
			break;
			case "vehicle_type":
				$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
			break;
			case "vvisible":
				$this->db->where("vehicle_status", 1);
			break;
			default:
				$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");

		}

		$this->db->distinct();
		$this->db->select("vehicle_device");
		$this->db->join("user", "vehicle_user_id = user_id");
		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$q = $this->db->get("vehicle");

		$totalvehicle = $q->num_rows();

		for($i=0; $i < count($rows); $i++)
		{
			if (! $rows[$i]->user_group) continue;
			if (! isset($vgroups[$rows[$i]->user_company][$rows[$i]->user_group])) continue;

			$totalvehicle += count($vgroups[$rows[$i]->user_company][$rows[$i]->user_group]);
		}

		// vehicle

		$userids = array(0);
		for($i=0; $i < count($rows); $i++)
		{
			$userids[] = $rows[$i]->user_id;
		}

		if ($field == "vexpired")
		{
				$this->db->where("vehicle_active_date2 <", date("Ymd"));
		}
		else
		if ($field == "vactive")
		{
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($field == "vehicle")
		{
			$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
		}
		else
		if ($field == "device")
		{
			$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
		}
		else
		if ($field == "vehicle_type")
		{
			$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
		}
		else
		if ($field == "vvisible")
		{
				$this->db->where("vehicle_status", 1);
		}
		else
		if ($field == "vehicle_card_no")
		{
			$this->db->where("vehicle_card_no LIKE", '%'.$keyword.'%');
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");

		$this->db->where_in("vehicle_user_id", $userids);

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$q = $this->db->get("vehicle");

		$vreplaces = $this->config->item('vehicle_type_replace');

		$rowvehicles = $q->result();
		for($i=0; $i < count($rowvehicles); $i++)
		{
			$arr = explode("@", $rowvehicles[$i]->vehicle_device);

			$rowvehicles[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rowvehicles[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
			$rowvehicles[$i]->isnewport = isset($vreplaces) && isset($vreplaces[$rowvehicles[$i]->vehicle_type]);

			$vehicles[$rowvehicles[$i]->vehicle_user_id][] = $rowvehicles[$i];
		}

		for($i=0; $i < count($rows); $i++)
		{
			if ($rows[$i]->user_company)
			{
				if ($rows[$i]->user_group)
				{
					$rows[$i]->vehicles = isset($vgroups[$rows[$i]->user_company][$rows[$i]->user_group]) ? $vgroups[$rows[$i]->user_company][$rows[$i]->user_group] : array();
				}
				else
				{
					$rows[$i]->vehicles = isset($vcompanies[$rows[$i]->user_company]) ? $vcompanies[$rows[$i]->user_company] : array();
				}
			}
			else
			{
				$rows[$i]->vehicles = isset($vehicles[$rows[$i]->user_id]) ? $vehicles[$rows[$i]->user_id] :array();
			}
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		else
		if ($this->sess->user_type == 2)
		{
			$this->db->where("user_group <> '' OR user_id = ".$this->sess->user_id, null);
			$this->db->where("user_company", $this->sess->user_company);
		}

		switch($field)
		{
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
			break;
			case "user_status":
				$this->db->where("user_status", $status);
			break;
			case "user_type":
				$this->db->where("user_type", $type);
			break;
			case "user_company":
				if ($groupid)
				{
					$this->db->where("user_group", $groupid);
				}
				else
				{
					$this->db->where("user_company", $company);
				}
			break;
			default:
				if (($field != "vvisible") && ($field != "vehicle") && ($field != "device") && ($field != "vexpired") && ($field != "vactive") && ($field != "vehicle_type") && (! $this->config->item('vehicle_type_fixed')) && ($field != "vehicle_card_no"))
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				else
				if ($this->config->item('vehicle_type_fixed'))
				{
					if ($this->config->item('vehicle_type_fixed'))
					{
						$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
					}
					$this->db->join("vehicle", "vehicle_user_id = user_id");
				}
				else
				{
					if ($field == "vexpired")
					{
							$this->db->where("vehicle_active_date2 <", date("Ymd"));
					}
					else
					if ($field == "vactive")
					{
							$this->db->where("vehicle_active_date2 >=", date("Ymd"));
					}
					else
					if ($field == "vehicle")
					{
						$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
					}
					else
					if ($field == "device")
					{
						$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
					}
					else
					if ($field == "vehicle_type")
					{
						$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
					}
					else
					if ($field == "vvisible")
					{
						$this->db->where("vehicle_status", 1);
					}
					else
					if ($field == "vehicle_card_no")
					{
						$this->db->where("vehicle_card_no LIKE", '%'.$keyword.'%');
					}

					$this->db->join("vehicle", "vehicle_user_id = user_id");
				}
		}

		if ($field == "user_agent")
		{
			$this->db->join("agent", "agent_id = user_agent", "left outer");
		}

		$this->db->distinct();
		$this->db->select("user_id");
		$q = $this->db->get("user");
		$total = $q->num_rows();

		$this->load->library("pagination1");

		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->pagination1->initialize($config);

		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('user/result', $this->params, true);

		$callback['html'] = $html;
		$callback['total'] = $total;
		$callback['totalvehicle'] = $totalvehicle;

		echo json_encode($callback);
	}

	function search_driver()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_type == 2)
		{
			if (! $this->sess->user_company)
			{
				redirect(base_url());
			}

			if ($this->sess->user_group)
			{
				redirect(base_url());
			}
		}

		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}
		else
		{
			// cari user dengan company

			$vreplaces = $this->config->item('vehicle_type_replace');

			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_company >", 0);
			$q = $this->db->get("vehicle");

			$rowcompanyvehicles = $q->result();
			for($i=0; $i < count($rowcompanyvehicles); $i++)
			{
				$arr = explode("@", $rowcompanyvehicles[$i]->vehicle_device);

				$rowcompanyvehicles[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
				$rowcompanyvehicles[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
				$rowcompanyvehicles[$i]->isnewport = isset($vreplaces) && isset($vreplaces[$rowcompanyvehicles[$i]->vehicle_type]);

				if (! isset($vcompaniesid))
				{
					$vcompanies[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i];
				}
				else
				if (! isset($vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company]))
				{
					$vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i]->vehicle_id;
				}
				else
				if (! in_array($rowcompanyvehicles[$i]->vehicle_id, $vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company]))
				{
					$vcompanies[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i];
				}

				$vcompaniesid[$rowcompanyvehicles[$i]->vehicle_company][] = $rowcompanyvehicles[$i]->vehicle_id;
				if ($rowcompanyvehicles[$i]->vehicle_group == 0) continue;

				$vgroups[$rowcompanyvehicles[$i]->vehicle_company][$rowcompanyvehicles[$i]->vehicle_group][] = $rowcompanyvehicles[$i];
			}

			// groups

			$groups = array();
			$grpprocessed = array();

			$this->vehiclemodel->getAllGroups(0, &$groups, &$grpprocessed);

			$this->db->join("group", "group_id = user_group");
			$q = $this->db->get("user");

			$rowusers = $q->result();
			for($i=0; $i < count($rowusers); $i++)
			{
				$childs = array();
				$this->vehiclemodel->getChilds($groups, $rowusers[$i]->user_group, &$childs);

				for($j=0; $j < count($childs); $j++)
				{
					if (! isset($vgroups[$rowusers[$i]->group_company][$childs[$j]])) continue;

					foreach($vgroups[$rowusers[$i]->group_company][$childs[$j]] as $val)
					{
						$vgroups[$rowusers[$i]->group_company][$rowusers[$i]->user_group][] = $val;
					}
				}
			}
		}

		$offset  = isset($_POST['offset']) ? $_POST['offset']:             0;
		$field   = isset($_POST['field']) ? $_POST['field']:               "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword']:           "";
		$vtype   = isset($_POST['vehicle_type']) ? $_POST['vehicle_type']: "";
		$type    = isset($_POST['type']) ? $_POST['type']:                 "";
		$status  = isset($_POST['status']) ? $_POST['status']:             "";
		$sortby  = isset($_POST['sortby']) ? $_POST['sortby']:             "user_login";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby']:           "asc";
		$company = isset($_POST['company']) ? $_POST['company']:           0;
		$groupid = isset($_POST['group']) ? $_POST['group']:               0;

		$this->db->order_by($sortby, $orderby);

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		else
		if ($this->sess->user_type == 2)
		{
			$this->db->where("user_group <> '' OR user_id = ".$this->sess->user_id, null);
			$this->db->where("user_company", $this->sess->user_company);
		}

		switch($field)
		{
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
			break;
			case "user_status":
				$this->db->where("user_status", $status);
			break;
			case "user_type":
				$this->db->where("user_type", $type);
			break;
			case "user_company":
				if ($groupid)
				{
					$this->db->where("user_group", $groupid);
				}
				else
				{
					$this->db->where("user_company", $company);
				}
			break;
			default:
				if (($field != "vvisible") && ($field != "vehicle") && ($field != "device") && ($field != "vexpired") && ($field != "vactive") && ($field != "vehicle_type") && (! $this->config->item('vehicle_type_fixed')) && ($field != "vehicle_card_no"))
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				else
				{
					$this->db->distinct();
					$this->db->select("user.*, agent.*");

					if ($this->config->item('vehicle_type_fixed'))
					{
						$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
					}

					$this->db->join("vehicle", "user_id = vehicle_user_id");

					if ($field == "vexpired")
					{
							$this->db->where("vehicle_active_date2 <", date("Ymd"));
					}
					else
					if ($field == "vactive")
					{
							$this->db->where("vehicle_active_date2 >=", date("Ymd"));
					}
					else
					if ($field == "vehicle")
					{
						$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
					}
					else
					if ($field == "vehicle_type")
					{
						$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
					}
					else
					if ($field == "vvisible")
					{
						$this->db->where("vehicle_status", 1);
					}
					else
					if ($field == "vehicle_card_no")
					{
						$this->db->where("vehicle_card_no LIKE", '%'.$keyword.'%');
					}
					else
					{
						$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
					}
				}
		}

		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$q = $this->db->get("user", $this->config->item("limit_records"), $offset);
		$rows = $q->result();

		// jumlah all vehicle

		if ($this->sess->user_type == 2)
		{
			$this->db->where_in("vehicle_id", $vehicleids);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where_in("user_agent", $this->sess->user_agent);
		}

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		switch ($field)
		{
			case "user_company":
				if ($groupid)
				{
					$this->db->where("user_group", $groupid);
				}
				else
				{
					$this->db->where("user_company", $company);
				}
			break;
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
			break;
			case "user_compay":
				$this->db->where("user_compay", $company);
			break;
			case "vexpired":
				$this->db->where("vehicle_active_date2 <", date("Ymd"));
			break;
			case "vactive":
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			break;
			case "vehicle":
				$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
			break;
			case "device":
				$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
			break;
			case "vehicle_type":
				$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
			break;
			case "vvisible":
				$this->db->where("vehicle_status", 1);
			break;
			default:
				$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");

		}

		$this->db->distinct();
		$this->db->select("vehicle_device");
		$this->db->join("user", "vehicle_user_id = user_id");
		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$q = $this->db->get("vehicle");

		$totalvehicle = $q->num_rows();

		for($i=0; $i < count($rows); $i++)
		{
			if (! $rows[$i]->user_group) continue;
			if (! isset($vgroups[$rows[$i]->user_company][$rows[$i]->user_group])) continue;

			$totalvehicle += count($vgroups[$rows[$i]->user_company][$rows[$i]->user_group]);
		}

		// vehicle

		$userids = array(0);
		for($i=0; $i < count($rows); $i++)
		{
			$userids[] = $rows[$i]->user_id;
		}

		if ($field == "vexpired")
		{
				$this->db->where("vehicle_active_date2 <", date("Ymd"));
		}
		else
		if ($field == "vactive")
		{
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($field == "vehicle")
		{
			$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
		}
		else
		if ($field == "device")
		{
			$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
		}
		else
		if ($field == "vehicle_type")
		{
			$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
		}
		else
		if ($field == "vvisible")
		{
				$this->db->where("vehicle_status", 1);
		}
		else
		if ($field == "vehicle_card_no")
		{
			$this->db->where("vehicle_card_no LIKE", '%'.$keyword.'%');
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");

		$this->db->where_in("vehicle_user_id", $userids);

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$q = $this->db->get("vehicle");

		$vreplaces = $this->config->item('vehicle_type_replace');

		$rowvehicles = $q->result();
		for($i=0; $i < count($rowvehicles); $i++)
		{
			$arr = explode("@", $rowvehicles[$i]->vehicle_device);

			$rowvehicles[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rowvehicles[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
			$rowvehicles[$i]->isnewport = isset($vreplaces) && isset($vreplaces[$rowvehicles[$i]->vehicle_type]);

			$vehicles[$rowvehicles[$i]->vehicle_user_id][] = $rowvehicles[$i];
		}

		for($i=0; $i < count($rows); $i++)
		{
			if ($rows[$i]->user_company)
			{
				if ($rows[$i]->user_group)
				{
					$rows[$i]->vehicles = isset($vgroups[$rows[$i]->user_company][$rows[$i]->user_group]) ? $vgroups[$rows[$i]->user_company][$rows[$i]->user_group] : array();
				}
				else
				{
					$rows[$i]->vehicles = isset($vcompanies[$rows[$i]->user_company]) ? $vcompanies[$rows[$i]->user_company] : array();
				}
			}
			else
			{
				$rows[$i]->vehicles = isset($vehicles[$rows[$i]->user_id]) ? $vehicles[$rows[$i]->user_id] :array();
			}
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		else
		if ($this->sess->user_type == 2)
		{
			$this->db->where("user_group <> '' OR user_id = ".$this->sess->user_id, null);
			$this->db->where("user_company", $this->sess->user_company);
		}

		switch($field)
		{
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
			break;
			case "user_status":
				$this->db->where("user_status", $status);
			break;
			case "user_type":
				$this->db->where("user_type", $type);
			break;
			case "user_company":
				if ($groupid)
				{
					$this->db->where("user_group", $groupid);
				}
				else
				{
					$this->db->where("user_company", $company);
				}
			break;
			default:
				if (($field != "vvisible") && ($field != "vehicle") && ($field != "device") && ($field != "vexpired") && ($field != "vactive") && ($field != "vehicle_type") && (! $this->config->item('vehicle_type_fixed')) && ($field != "vehicle_card_no"))
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				else
				if ($this->config->item('vehicle_type_fixed'))
				{
					if ($this->config->item('vehicle_type_fixed'))
					{
						$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
					}
					$this->db->join("vehicle", "vehicle_user_id = user_id");
				}
				else
				{
					if ($field == "vexpired")
					{
							$this->db->where("vehicle_active_date2 <", date("Ymd"));
					}
					else
					if ($field == "vactive")
					{
							$this->db->where("vehicle_active_date2 >=", date("Ymd"));
					}
					else
					if ($field == "vehicle")
					{
						$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
					}
					else
					if ($field == "device")
					{
						$this->db->where("vehicle_device LIKE", '%'.$keyword.'%');
					}
					else
					if ($field == "vehicle_type")
					{
						$this->db->where("vehicle_type LIKE", '%'.$vtype.'%');
					}
					else
					if ($field == "vvisible")
					{
						$this->db->where("vehicle_status", 1);
					}
					else
					if ($field == "vehicle_card_no")
					{
						$this->db->where("vehicle_card_no LIKE", '%'.$keyword.'%');
					}

					$this->db->join("vehicle", "vehicle_user_id = user_id");
				}
		}

		if ($field == "user_agent")
		{
			$this->db->join("agent", "agent_id = user_agent", "left outer");
		}

		$this->db->distinct();
		$this->db->select("user_id");
		$q = $this->db->get("user");
		$total = $q->num_rows();

		$this->load->library("pagination1");

		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->pagination1->initialize($config);

		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('user/driver_result', $this->params, true);

		$callback['html'] = $html;
		$callback['total'] = $total;
		$callback['totalvehicle'] = $totalvehicle;

		echo json_encode($callback);
	}

	function add($id=0)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_type == 2)
		{
			if ($id != $this->sess->user_id)
			{
				redirect(base_url());
			}

			if ($this->sess->user_change_profile == 2)
			{
				redirect(base_url());
			}
		}

		if ($id)
		{

			$this->db->where("user_id", $id);
			$this->db->join("agent", "agent_id = user_agent", "left outer");
			$q = $this->db->get("user");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}

			$row = $q->row();

			if ($row->user_birth_date)
			{
				$t = dbintmaketime($row->user_birth_date, 0);
				$row->user_date_fmt = date("d/m/Y", $t);
			}
			else
			{
				$row->user_date_fmt = "";
			}

			$this->params['row'] = $row;

			// get vehicle

			$this->db->where("vehicle_user_id", $id);
			$q = $this->db->get("vehicle");

			$rowvehicles = $q->result();
			for($i=0; $i < count($rowvehicles); $i++)
			{
				$rowvehicles[$i]->expire_date1 = $rowvehicles[$i]->vehicle_active_date1 ? inttodate($rowvehicles[$i]->vehicle_active_date1) : "";
				$rowvehicles[$i]->expire_date2 = $rowvehicles[$i]->vehicle_active_date2 ? inttodate($rowvehicles[$i]->vehicle_active_date2) : "";
				$rowvehicles[$i]->expire_date = $rowvehicles[$i]->vehicle_active_date ? inttodate($rowvehicles[$i]->vehicle_active_date) : "";
			}

			$this->params['vehicles'] = $rowvehicles;
			$this->params['title'] = $this->lang->line('luser_edit').", ".$this->lang->line('lupdate_vehicle');
		}
		else
		{
			if (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 0))
			{
				redirect(base_url());
			}


			$this->params['title'] = $this->lang->line('luser_add').", ".$this->lang->line('ladd_vehicle');
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("agent_id", $this->sess->user_agent);
		}

		$this->db->order_by("agent_name");
		$q = $this->db->get("agent");

		$rowagents = $q->result();

		if ($this->sess->user_type == 2)
		{
			$this->db->where("company_id", $this->sess->user_company);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("company_agent", $this->sess->user_agent);
		}
		$this->db->order_by("company_name", "asc");
		$q = $this->db->get("company");

		$rowcompanies = $q->result();

		$this->params["companies"]      = $rowcompanies;
		$this->params["agents"]         = $rowagents;
		$this->params['code_view_menu'] = "configuration";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function edit($id=0)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($id)
		{
			$this->db->where("user_id", $id);
			$this->db->join("agent", "agent_id = user_agent", "left outer");
			$q = $this->db->get("user");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}

			$row = $q->row();

			if ($row->user_birth_date)
			{
				$t = dbintmaketime($row->user_birth_date, 0);
				$row->user_date_fmt = date("d/m/Y", $t);
			}
			else
			{
				$row->user_date_fmt = "";
			}

			$this->params['row'] = $row;

			$user_branchoffice_edit    = $row->user_company;
			$user_subbranchoffice_edit = $row->user_subcompany;
			$user_customer_edit        = $row->user_group;
			$user_subcustomer_edit     = $row->user_subgroup;

			// CURRENT BRANCH OFFICE
			if ($user_branchoffice_edit != 0) {
				$this->db->where("company_id", $user_branchoffice_edit);
				$q                                = $this->db->get("company");
				$this->params['cur_branchoffice'] = $q->result_array();
			}else {
				$this->params['cur_branchoffice'] = "Not Set";
			}

			// CURRENT SUB BRANCH OFFICE
			if ($user_subbranchoffice_edit != 0) {
				$this->db->where("subcompany_id", $user_subbranchoffice_edit);
				$q                                = $this->db->get("subcompany");
				$this->params['cur_subbranchoffice'] = $q->result_array();
			}else {
				$this->params['cur_subbranchoffice'][0]['subcompany_name'] = "Not Set";
				$this->params['cur_subbranchoffice'][0]['subcompany_id'] = "0";
			}

			// CURRENT CUSTOMER
			if ($user_customer_edit != 0) {
				$this->db->where("group_id", $user_customer_edit);
				$q                                = $this->db->get("group");
				$this->params['cur_customer'] = $q->result_array();
			}else {
				$this->params['cur_customer'][0]['group_name'] = "Not Set";
				$this->params['cur_customer'][0]['group_id'] = "0";
			}

			// CURRENT SUB CUSTOMER
			if ($user_subcustomer_edit != 0) {
				$this->db->where("subgroup_id", $user_subcustomer_edit);
				$q                                = $this->db->get("subgroup");
				$this->params['cur_subcustomer'] = $q->result_array();
			}else {
				$this->params['cur_subcustomer'][0]['subgroup_name'] = "Not Set";
				$this->params['cur_subcustomer'][0]['subgroup_id'] = "0";
			}

			// echo "<pre>";
			// var_dump($this->params['cur_subbranchoffice']);die();
			// echo "<pre>";

			// get vehicle

			$this->db->where("vehicle_user_id", $id);
			$q = $this->db->get("vehicle");

			$rowvehicles = $q->result();
			for($i=0; $i < count($rowvehicles); $i++)
			{
				$rowvehicles[$i]->expire_date1 = $rowvehicles[$i]->vehicle_active_date1 ? inttodate($rowvehicles[$i]->vehicle_active_date1) : "";
				$rowvehicles[$i]->expire_date2 = $rowvehicles[$i]->vehicle_active_date2 ? inttodate($rowvehicles[$i]->vehicle_active_date2) : "";
				$rowvehicles[$i]->expire_date = $rowvehicles[$i]->vehicle_active_date ? inttodate($rowvehicles[$i]->vehicle_active_date) : "";
			}

			$this->params['vehicles'] = $rowvehicles;
			$this->params['title'] = $this->lang->line('luser_edit').", ".$this->lang->line('lupdate_vehicle');
		}
		else
		{
			if (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 0))
			{
				redirect(base_url());
			}


			$this->params['title'] = $this->lang->line('luser_add').", ".$this->lang->line('ladd_vehicle');
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("agent_id", $this->sess->user_agent);
		}

		$this->db->order_by("agent_name");
		$q = $this->db->get("agent");

		$rowagents = $q->result();
		$this->db->where("company_id", $this->sess->user_company);
		$this->db->order_by("company_name", "asc");
		$q = $this->db->get("company");

		$rowcompanies = $q->result();

		$this->params["companies"]      = $rowcompanies;
		$this->params["agents"]         = $rowagents;
		// $this->params['code_view_menu'] = "configuration";
    // echo "<pre>";
    // var_dump($this->params["companies"]);die();
    // echo "<pre>";
    // $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
    // $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
    // $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
    // $this->params["content"]     = $this->load->view('dashboard/user/v_user_edit', $this->params, true);
    // $this->load->view("dashboard/template_dashboard_report", $this->params);

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function edituser($id=0)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$roleList = $this->dashboardmodel->getuseridrole();
		$pjolist  = $this->dashboardmodel->getallpjo();

		if ($id)
		{
			$this->db->where("user_id", $id);
			$this->db->join("agent", "agent_id = user_agent", "left outer");
			$q = $this->db->get("user");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}

			$row = $q->row();

			if ($row->user_birth_date)
			{
				$t = dbintmaketime($row->user_birth_date, 0);
				$row->user_date_fmt = date("d/m/Y", $t);
			}
			else
			{
				$row->user_date_fmt = "";
			}

			$this->params['row'] = $row;

			// echo "<pre>";
			// var_dump($row);die();
			// echo "<pre>";

			$user_branchoffice_edit    = $row->user_company;
			$user_subbranchoffice_edit = $row->user_subcompany;
			$user_customer_edit        = $row->user_group;
			$user_subcustomer_edit     = $row->user_subgroup;

			// CURRENT BRANCH OFFICE
			if ($user_branchoffice_edit != 0) {
				$this->db->where("company_id", $user_branchoffice_edit);
				$q                                = $this->db->get("company");
				$this->params['cur_branchoffice'] = $q->result_array();
			}else {
				$this->params['cur_branchoffice'] = "Not Set";
			}

			// CURRENT SUB BRANCH OFFICE
			if ($user_subbranchoffice_edit != 0) {
				$this->db->where("subcompany_id", $user_subbranchoffice_edit);
				$q                                = $this->db->get("subcompany");
				$this->params['cur_subbranchoffice'] = $q->result_array();
			}else {
				$this->params['cur_subbranchoffice'][0]['subcompany_name'] = "Not Set";
				$this->params['cur_subbranchoffice'][0]['subcompany_id'] = "0";
			}

			// CURRENT CUSTOMER
			if ($user_customer_edit != 0) {
				$this->db->where("group_id", $user_customer_edit);
				$q                                = $this->db->get("group");
				$this->params['cur_customer'] = $q->result_array();
			}else {
				$this->params['cur_customer'][0]['group_name'] = "Not Set";
				$this->params['cur_customer'][0]['group_id'] = "0";
			}

			// CURRENT SUB CUSTOMER
			if ($user_subcustomer_edit != 0) {
				$this->db->where("subgroup_id", $user_subcustomer_edit);
				$q                                = $this->db->get("subgroup");
				$this->params['cur_subcustomer'] = $q->result_array();
			}else {
				$this->params['cur_subcustomer'][0]['subgroup_name'] = "Not Set";
				$this->params['cur_subcustomer'][0]['subgroup_id'] = "0";
			}

			// echo "<pre>";
			// var_dump($this->params['cur_subbranchoffice']);die();
			// echo "<pre>";

			// get vehicle

			$this->db->where("vehicle_user_id", $id);
			$q = $this->db->get("vehicle");

			$rowvehicles = $q->result();
			for($i=0; $i < count($rowvehicles); $i++)
			{
				$rowvehicles[$i]->expire_date1 = $rowvehicles[$i]->vehicle_active_date1 ? inttodate($rowvehicles[$i]->vehicle_active_date1) : "";
				$rowvehicles[$i]->expire_date2 = $rowvehicles[$i]->vehicle_active_date2 ? inttodate($rowvehicles[$i]->vehicle_active_date2) : "";
				$rowvehicles[$i]->expire_date = $rowvehicles[$i]->vehicle_active_date ? inttodate($rowvehicles[$i]->vehicle_active_date) : "";
			}

			$this->params['vehicles'] = $rowvehicles;
			$this->params['title'] = $this->lang->line('luser_edit').", ".$this->lang->line('lupdate_vehicle');
		}
		else
		{
			if (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 0))
			{
				redirect(base_url());
			}


			$this->params['title'] = $this->lang->line('luser_add').", ".$this->lang->line('ladd_vehicle');
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("agent_id", $this->sess->user_agent);
		}

		$this->db->order_by("agent_name");
		$q = $this->db->get("agent");

		$rowagents = $q->result();
		$this->db->where("company_id", $this->sess->user_company);
		$this->db->order_by("company_name", "asc");
		$q = $this->db->get("company");

		$rowcompanies = $q->result();

		$this->params["companies"]      = $rowcompanies;
		$this->params["agents"]         = $rowagents;
		$this->params['role_list']      = $roleList;
		$this->params['pjo_list']       = $pjolist;
		// $this->params['code_view_menu'] = "configuration";
		// echo "<pre>";
		// var_dump($this->params["companies"]);die();
		// echo "<pre>";
		// $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		// $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		// $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		// $this->params["content"]     = $this->load->view('dashboard/user/v_user_edit', $this->params, true);
		// $this->load->view("dashboard/template_dashboard_report", $this->params);

		$privilegecode = $this->sess->user_id_role;
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}else {
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function editdriver($id=0)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$roleList = $this->getuseridrole_driver();
		$pjolist  = $this->dashboardmodel->getallpjo();

		if ($id)
		{
			$this->db->where("user_id", $id);
			$this->db->join("agent", "agent_id = user_agent", "left outer");
			$q = $this->db->get("user");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}

			$row = $q->row();

			if ($row->user_birth_date)
			{
				$t = dbintmaketime($row->user_birth_date, 0);
				$row->user_date_fmt = date("d/m/Y", $t);
			}
			else
			{
				$row->user_date_fmt = "";
			}

			$this->params['row'] = $row;

			$user_branchoffice_edit    = $row->user_company;
			$user_subbranchoffice_edit = $row->user_subcompany;
			$user_customer_edit        = $row->user_group;
			$user_subcustomer_edit     = $row->user_subgroup;

			// CURRENT BRANCH OFFICE
			if ($user_branchoffice_edit != 0) {
				$this->db->where("company_id", $user_branchoffice_edit);
				$q                                = $this->db->get("company");
				$this->params['cur_branchoffice'] = $q->result_array();
			}else {
				$this->params['cur_branchoffice'] = "Not Set";
			}

			// CURRENT SUB BRANCH OFFICE
			if ($user_subbranchoffice_edit != 0) {
				$this->db->where("subcompany_id", $user_subbranchoffice_edit);
				$q                                = $this->db->get("subcompany");
				$this->params['cur_subbranchoffice'] = $q->result_array();
			}else {
				$this->params['cur_subbranchoffice'][0]['subcompany_name'] = "Not Set";
				$this->params['cur_subbranchoffice'][0]['subcompany_id'] = "0";
			}

			// CURRENT CUSTOMER
			if ($user_customer_edit != 0) {
				$this->db->where("group_id", $user_customer_edit);
				$q                                = $this->db->get("group");
				$this->params['cur_customer'] = $q->result_array();
			}else {
				$this->params['cur_customer'][0]['group_name'] = "Not Set";
				$this->params['cur_customer'][0]['group_id'] = "0";
			}

			// CURRENT SUB CUSTOMER
			if ($user_subcustomer_edit != 0) {
				$this->db->where("subgroup_id", $user_subcustomer_edit);
				$q                                = $this->db->get("subgroup");
				$this->params['cur_subcustomer'] = $q->result_array();
			}else {
				$this->params['cur_subcustomer'][0]['subgroup_name'] = "Not Set";
				$this->params['cur_subcustomer'][0]['subgroup_id'] = "0";
			}

			// echo "<pre>";
			// var_dump($this->params['cur_subbranchoffice']);die();
			// echo "<pre>";

			// get vehicle

			$this->db->where("vehicle_user_id", $id);
			$q = $this->db->get("vehicle");

			$rowvehicles = $q->result();
			for($i=0; $i < count($rowvehicles); $i++)
			{
				$rowvehicles[$i]->expire_date1 = $rowvehicles[$i]->vehicle_active_date1 ? inttodate($rowvehicles[$i]->vehicle_active_date1) : "";
				$rowvehicles[$i]->expire_date2 = $rowvehicles[$i]->vehicle_active_date2 ? inttodate($rowvehicles[$i]->vehicle_active_date2) : "";
				$rowvehicles[$i]->expire_date = $rowvehicles[$i]->vehicle_active_date ? inttodate($rowvehicles[$i]->vehicle_active_date) : "";
			}

			$this->params['vehicles'] = $rowvehicles;
			$this->params['title'] = $this->lang->line('luser_edit').", ".$this->lang->line('lupdate_vehicle');
		}
		else
		{
			if (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 0))
			{
				redirect(base_url());
			}


			$this->params['title'] = $this->lang->line('luser_add').", ".$this->lang->line('ladd_vehicle');
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("agent_id", $this->sess->user_agent);
		}

		$this->db->order_by("agent_name");
		$q = $this->db->get("agent");

		$rowagents = $q->result();
		$this->db->where("company_id", $this->sess->user_company);
		$this->db->order_by("company_name", "asc");
		$q = $this->db->get("company");

		$rowcompanies = $q->result();

		$this->params["companies"]      = $rowcompanies;
		$this->params["agents"]         = $rowagents;
		$this->params['role_list']      = $roleList;
		$this->params['pjo_list']       = $pjolist;
		// $this->params['code_view_menu'] = "configuration";
		// echo "<pre>";
		// var_dump($this->params["companies"]);die();
		// echo "<pre>";
		// $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		// $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		// $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		// $this->params["content"]     = $this->load->view('dashboard/user/v_user_edit', $this->params, true);
		// $this->load->view("dashboard/template_dashboard_report", $this->params);

		$privilegecode = $this->sess->user_id_role;
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}else {
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_driver_edit_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function addnewuser(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$roleList = $this->dashboardmodel->getuseridrole();
		$pjolist  = $this->dashboardmodel->getallpjo();

		// echo "<pre>";
		// var_dump($pjolist);die();
		// echo "<pre>";

		$this->params['code_view_menu'] = "configuration";
		$this->params['role_list']      = $roleList;
		$this->params['pjo_list']       = $pjolist;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		$privilegecode = $this->sess->user_id_role;

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newuser', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newuser', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newuser', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newuser', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newuser', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newuser', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function addnewdriver(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$roleList = $this->getuseridrole_driver();
		$pjolist  = $this->dashboardmodel->getallpjo();

		// echo "<pre>";
		// var_dump($pjolist);die();
		// echo "<pre>";

		$this->params['code_view_menu'] = "configuration";
		$this->params['role_list']      = $roleList;
		$this->params['pjo_list']       = $pjolist;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		$privilegecode = $this->sess->user_id_role;

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newdriver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newdriver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newdriver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newdriver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newdriver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_add_newdriver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function savenewuser(){
		$name          = $this->input->post('name');
		$email         = $this->input->post('email');
		$sex           = $this->input->post('sex');
		$birthdate     = $this->input->post('birthdate');
		$address       = $this->input->post('address');
		$mobile        = $this->input->post('mobile');
		$privilege     = $this->input->post('privilege');
		$pjolist       = $this->input->post('pjolist');
		// $username   = $this->input->post('username');
		// $pass       = $this->input->post('pass');
		// $cpass      = $this->input->post('cpass');
		$locallogin    = $this->input->post('locallogin');
		$userexcavator = $this->input->post('userexcavator');

			if (!$locallogin) {
				$locallogin = 0;
			}else {
				$locallogin = 1;
			}

			if (!$userexcavator) {
				$userexcavator = 0;
			}else {
				$userexcavator = 1;
			}

		// echo "<pre>";
		// var_dump($locallogin);die();
		// echo "<pre>";


		if ($name == "") {
			$callback['error'] = true;
			$callback['message'] = "Username tidak boleh kosong";

			echo json_encode($callback);
			return;
		}

		if ($email == "") {
			$callback['error'] = true;
			$callback['message'] = "SID tidak boleh kosong";

			echo json_encode($callback);
			return;
		}

		$check_email = $this->checkthisemail("user", $email);
		$user_license_id = "";

		if (sizeof($check_email) > 0) {
			$callback['error'] = true;
			$callback['message'] = "SID sudah terdaftar, silahkan daftarkan SID lain";

			echo json_encode($callback);
			return;
		}

		if ($privilege == 5 || $privilege == 6 || $privilege == 10) {
			$pjolist     = explode("|", $pjolist);
			$user_company = $pjolist[0];
			$pjolistname = $pjolist[1];
				if ($userexcavator == 1) {
					$user_parent = $user_company;
				}else {
					$user_parent = 0;
				}
			//user_licensi_digunakan beats
			$user_license_id = "beats";
			
		}else {
			$pjolist     = 0;
			$user_company = $this->sess->user_company;
			$pjolistname = 0;
			$user_parent = 4408;
		}

		//setting default password
		if(($privilege == 1)||($privilege == 2)||($privilege == 3)||($privilege == 4) || ($privilege == 10)){
			$pass = "BerauCoal123";
		}else if(($privilege == 5)||($privilege == 6)){
			$pass = "BerauMitra".$pjolistname."123";
		}else if(($privilege == 7)||($privilege == 8)){
			$pass = "TemanIndobaraWIM123";
		}

		$data = array(
			"user_login"               => $email,
			"user_mail"                => $email,
			"user_pass"                => $pass,
			"user_name"                => $name,
			"user_sex"                 => $sex,
			"user_birth_date"          => $birthdate,
			"user_address"             => $address,
			"user_mobile"              => $mobile,
			"user_type"                => 2,
			"user_status"              => 1,
			"user_create_date"         => date("Ymd"),
			"user_agent_admin"         => 1,
			"user_engine"              => 1,
			"user_company"             => $user_company,
			"user_license_id"          => $user_license_id,
			"user_manage_password"     => 1,
			"user_change_profile"      => 1,
			"user_payment_type"        => 1,
			"user_payment_period"      => 1,
			"user_alert_geo_sms"       => 1,
			"user_alert_geo_email"     => 1,
			"user_alert_speed_sms"     => 1,
			"user_alert_speed_email"   => 1,
			"user_alert_parking_sms"   => 1,
			"user_alert_parking_email" => 1,
			"user_trans_tupper"        => 2,
			"user_sales"               => 1,
			"user_level"               => 1,
			"user_parent"              => $user_parent,
			"user_dblive"              => $this->sess->user_dblive,
			"user_app"                 => $this->sess->user_app,
			"user_id_role"             => $privilege,
			"user_local_login"         => $locallogin,
			"user_excavator"           => $userexcavator
		
		);
	
		$data["user_password_default"] = 1;
		
				$mydb   = $this->load->database("master", TRUE);
				$mydb->insert("user", $data);
				$userid = $mydb->insert_id();
				$sql    = "UPDATE ".$this->db->dbprefix."user SET user_pass = PASSWORD('".mysql_escape_string($pass)."') WHERE user_id = '".$userid."'";
				$this->db->query($sql);
				$this->db->cache_delete_all();

				$callback['error']    = false;
				$callback['message']  = $this->lang->line("luser_added");
				$callback['redirect'] = base_url()."account";

				echo json_encode($callback);
				return;
	}

	function savenewdriver(){
		$name        = $this->input->post('name');
		$email       = $this->input->post('email');
		$sex         = $this->input->post('sex');
		$birthdate   = $this->input->post('birthdate');
		$address     = $this->input->post('address');
		$mobile      = $this->input->post('mobile');
		$privilege   = $this->input->post('privilege');
		$pjolist     = $this->input->post('pjolist');
		// $username = $this->input->post('username');
		// $pass        = $this->input->post('pass');
		// $cpass       = $this->input->post('cpass');
		$locallogin  = $this->input->post('locallogin');
		$mail = $this->input->post('mail');


			if (!$locallogin) {
				$locallogin = 0;
			}else {
				$locallogin = 1;
			}

		// echo "<pre>";
		// var_dump($locallogin);die();
		// echo "<pre>";


		if ($name == "") {
			$callback['error'] = true;
			$callback['message'] = "Username tidak boleh kosong";

			echo json_encode($callback);
			return;
		}

		if ($email == "") {
			$callback['error'] = true;
			$callback['message'] = "Simper tidak boleh kosong";

			echo json_encode($callback);
			return;
		}


		if ($privilege == 5 || $privilege == 6 || $privilege == 9) {
			$pjolist     = explode("|", $pjolist);
			$user_company = $pjolist[0];
			$pjolistname = $pjolist[1];
			$user_parent = 0;
		}else {
			$pjolist     = 0;
			$user_company = $this->sess->user_company;
			$pjolistname = 0;
			$user_parent = 4408;
		}

		//setting default password
		if(($privilege == 1)||($privilege == 2)||($privilege == 3)||($privilege == 4)){
			$pass = "TemanIndobara123";
		}else if(($privilege == 5)||($privilege == 6)){
			$pass = "TemanPJO".$pjolistname."123";
		}else if(($privilege == 7)||($privilege == 8)){
			$pass = "TemanIndobaraWIM123";
		}else if($privilege == 9){
			//$pass = "temandriver123";
			$datasimper = $this->getDetail_info_from_isafe($email);


			if ($datasimper == "") {
				$callback['error'] = true;
				$callback['message'] = "No Data Simper. Mohon Cek Simper Anda di Portal iSafe";

				echo json_encode($callback);
				return;
			}

			$birthdate = $datasimper->date_of_birth;

			$pin_year = substr($birthdate,2,2);
			$pin_month = substr($birthdate,5,2);
			$pin_date = substr($birthdate,8,2);

			$pin_result = $pin_date."".$pin_month."".$pin_year;
			$pass = $pin_result;
			$mobile = $datasimper->contact;
			$address = $datasimper->address;
			$sex =  $datasimper->gender;
			$mail = $datasimper->email;


		}

		$is_duplicate = $this->check_duplicat_user($email); //email = simper



		if (count($is_duplicate) > 0) {
			$callback['error'] = true;
			$callback['message'] = "Account already exists!";

			echo json_encode($callback);
			return;
		}



		$data = array(
			"user_login"               => $email,
			"user_mail"                => $mail,
			"user_pass"                => $pass,
			"user_name"                => $name,
			"user_sex"                 => $sex,
			"user_birth_date"          => date("Ymd", strtotime($birthdate)),
			"user_address"             => $address,
			"user_mobile"              => $mobile,
			"user_type"                => 2,
			"user_status"              => 1,
			"user_create_date"         => date("Ymd"),
			"user_agent_admin"         => 1,
			"user_engine"              => 1,
			"user_company"             => $user_company,
			"user_manage_password"     => 1,
			"user_change_profile"      => 1,
			"user_payment_type"        => 1,
			"user_payment_period"      => 1,
			"user_alert_geo_sms"       => 1,
			"user_alert_geo_email"     => 1,
			"user_alert_speed_sms"     => 1,
			"user_alert_speed_email"   => 1,
			"user_alert_parking_sms"   => 1,
			"user_alert_parking_email" => 1,
			"user_trans_tupper"        => 2,
			"user_sales"               => 1,
			"user_level"               => 1,
			"user_parent"              => $user_parent,
			"user_dblive"              => $this->sess->user_dblive,
			"user_app"                 => $this->sess->user_app,
			"user_id_role"             => $privilege,
			"user_local_login"         => $locallogin
		);
		if($locallogin == 0){
			$data["user_password_default"] = 1;
		}

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

				$mydb   = $this->load->database("master", TRUE);
				$mydb->insert("user", $data);
				$userid = $mydb->insert_id();
				$sql    = "UPDATE ".$this->db->dbprefix."user SET user_pass = PASSWORD('".mysql_escape_string($pass)."') WHERE user_id = '".$userid."'";
				$this->db->query($sql);
				$this->db->cache_delete_all();

				//insert into Table transporter driver
				$insert_to_transporter = $this->insert_driver_DBTransporter($data,$pass,$datasimper);

				$callback['error']    = false;
				$callback['message']  = $this->lang->line("luser_added");
				$callback['redirect'] = base_url()."account/driver";

				echo json_encode($callback);
				return;

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";


	}

	function getDetail_info_from_isafe($idsimper){


		$isafe_data = "";

		$token = "";
		$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo";


		$urlGetToken = "https://esbportal.borneo-indobara.com/external/getToken"."?"; //PROD sudah live
		$urlGetSync  = "https://esbportal.borneo-indobara.com/external/simperlist/getAll?nik=C-".$idsimper; //PROD


		// GET TOKEN
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
				   "Accept: application/json",
				   "Authorization: Bearer " .$clientkey,
				);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_URL, $urlGetToken);
		$result = curl_exec($ch);
		curl_close($ch);

		$obj = json_decode($result);

		if (isset($obj->token)) {

				$curl = curl_init($urlGetSync);
				curl_setopt($curl, CURLOPT_URL, $urlGetSync);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				$headers = array(
				   "Accept: application/json",
				   "Authorization: Bearer " .$obj->token,
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$resp = curl_exec($curl);

				curl_close($curl);

				if ($resp)
				{
					$datajson   = json_decode($resp);
					$datasimper = $datajson->simper;
					if(count($datasimper)>0){
						$isafe_data = $datasimper[0];
					}



				}
		}

		return $isafe_data;

	}

	function insert_driver_DBTransporter($data,$pass,$datasimper){

		$result_base64 = "";
		$clientkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBzX25hbWUiOiJmbXMiLCJpYXQiOjE2NDE3ODM1MDB9.M6ICuT4jM_lfCZuZdosIjpLvRZYotyFpdIJMyZ3jSlo"; //PROD

		$urlGetToken = "https://esbportal.borneo-indobara.com/external/getToken"."?"; //PROD
		$urlGetImg  = "https://esbportal.borneo-indobara.com/external/simperlist/getImages?id_number=".$datasimper->id_number; //PROD

		// GET TOKEN
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
				   "Accept: application/json",
				   "Authorization: Bearer " .$clientkey,
				);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_URL, $urlGetToken);
		$result = curl_exec($ch);
		curl_close($ch);

		$obj = json_decode($result);

		if(isset($obj)){


				if (isset($obj->token)) {

					$curl = curl_init($urlGetImg);
					curl_setopt($curl, CURLOPT_URL, $urlGetImg);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

					$headers = array(
					   "Accept: application/json",
					   "Authorization: Bearer " .$obj->token,
					);
					curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
					//for debug only!
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					$resp = curl_exec($curl);
					curl_close($curl);

					if ($resp)
					{
						$datajson   = json_decode($resp);
						$result_base64 = $datajson->result;
					}
				}
		}


		unset($datadriver);
		$datadriver['driver_company'] = $data['user_company'];
		$datadriver['driver_name'] = $data['user_name'];
		$datadriver['driver_idcard'] = $data['user_login'];
		$datadriver['driver_login'] = $data['user_login'];
		$datadriver['driver_mobile'] = $data['user_mobile'];

		$datadriver['driver_mobile2'] = $datasimper->emergency_contact;
		$datadriver['driver_licence_no'] = $datasimper->sim_number;
		$datadriver['driver_licence_expired'] = $datasimper->sim_exp_date;
		$datadriver['driver_sex'] = $datasimper->gender;
		$datadriver['driver_joint_date'] = $datasimper->date_of_hire;
		$datadriver['driver_address'] = $datasimper->address;

		if($datasimper->status_vaksin == 1){
			$vaksin_text = "Sudah Vaksin Satu";
		}else if($datasimper->status_vaksin == 2){
			$vaksin_text = "Sudah Vaksin Kedua";
		}else if($datasimper->status_vaksin == 3){
			$vaksin_text = "Sudah Vaksin Ketiga/Booster";
		}else if($datasimper->status_vaksin == 4){
			$vaksin_text = "Sudah Vaksin Keempat/Booster Kedua";
		}else{
			$vaksin_text = "Tidak ada data vaksin di iSafe";
		}

		$datadriver['driver_vaksin_status'] = $vaksin_text;
		$datadriver['driver_image'] = $result_base64;

		$mydb = $this->load->database("transporter", TRUE);
		$mydb->insert("transporter_driver", $datadriver);
	}
	function check_duplicat_user($userlogin){
		$this->db = $this->load->database('default', true);
		$this->db->select("user_id,user_login");
		$this->db->where("user_login",$userlogin);
		$this->db->where("user_status",1);
		$q        = $this->db->get("user");
		$result_q = $q->row();

		return $result_q;
	}

	function adduser($id=0){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		if ($id){
			$this->db->where("user_id", $id);
			$this->db->join("agent", "agent_id = user_agent", "left outer");
			$q = $this->db->get("user");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}

			$row = $q->row();

			if ($row->user_birth_date){
				$t = dbintmaketime($row->user_birth_date, 0);
				$row->user_date_fmt = date("d/m/Y", $t);
			}else{
				$row->user_date_fmt = "";
			}

			$this->params['row'] = $row;

			// get vehicle
			$this->db->where("vehicle_user_id", $id);
			$q                   = $this->db->get("vehicle");

			$rowvehicles         = $q->result();
			for($i=0; $i < count($rowvehicles); $i++){
				$rowvehicles[$i]->expire_date1 = $rowvehicles[$i]->vehicle_active_date1 ? inttodate($rowvehicles[$i]->vehicle_active_date1): "";
				$rowvehicles[$i]->expire_date2 = $rowvehicles[$i]->vehicle_active_date2 ? inttodate($rowvehicles[$i]->vehicle_active_date2): "";
				$rowvehicles[$i]->expire_date  = $rowvehicles[$i]->vehicle_active_date ? inttodate($rowvehicles[$i]->vehicle_active_date):   "";
			}

			$this->params['vehicles'] = $rowvehicles;
			$this->params['title'] = $this->lang->line('luser_edit').", ".$this->lang->line('lupdate_vehicle');
		}else{
			if (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 0)){
				redirect(base_url());
			}
			$this->params['title'] = $this->lang->line('luser_add').", ".$this->lang->line('ladd_vehicle');
		}

		if ($this->sess->user_type == 3){
			$this->db->where("agent_id", $this->sess->user_agent);
		}

		$this->db->order_by("agent_name");
		$q         = $this->db->get("agent");

		$rowagents = $q->result();
		$this->db->where("company_id", $this->sess->user_company);
		$this->db->order_by("company_name", "asc");
		$q                              = $this->db->get("company");

		$rowcompanies                   = $q->result();

		$this->params["companies"]      = $rowcompanies;
		$this->params["agents"]         = $rowagents;
		$this->params['code_view_menu'] = "configuration";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		$privilegecode = $this->sess->user_id_role;

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/user/v_user_edit', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function saveuser()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$name = isset($_POST['license']) ? trim($_POST['name']) : "";
		$license = isset($_POST['license']) ? trim($_POST['license']) : "";
		$sex = isset($_POST['sex']) ? trim($_POST['sex']) : "";
		$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : "";
		$province = isset($_POST['province']) ? trim($_POST['province']) : "";
		$city = isset($_POST['city']) ? trim($_POST['city']) : "";
		$address = isset($_POST['address']) ? trim($_POST['address']) : "";
		$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : "";
		$phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";
		$email = isset($_POST['email']) ? trim($_POST['email']) : "";
		$agent_admin = isset($_POST['agent_admin']) ? trim($_POST['agent_admin']) : 0;

		if (strlen($email))
		{
			if (! valid_emails($email))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_email');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($birthdate) > 0)
		{
			$tbirthdate = formmaketime($birthdate." 00:00:00");

			if (date("d/m/Y", $tbirthdate) != $birthdate)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_birthdate');

				echo json_encode($callback);
				return;
			}

		}

		unset($data);

		$data['user_mail'] = $email;
		$data['user_name'] = $name;
		$data['user_license_id'] = $license;
		$data['user_license_type'] = 'A';
		$data['user_sex'] = $sex;
		$data['user_birth_date'] = isset($tbirthdate) ? date("Ymd", $tbirthdate) : 0;
		$data['user_province'] = $province;
		$data['user_city'] = $city;
		$data['user_address'] = $address;
		$data['user_mobile'] = $mobile;
		$data['user_phone'] = $phone;
		$data['user_agent_admin'] = $agent_admin;

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("user_id", $this->sess->user_id);
		$mydb->update("user", $data);

		$this->db->cache_delete_all();

		$callback['error'] = false;
		$callback['message'] = $this->lang->line("luser_updated");
		$callback['redirect'] = base_url()."account/add/".$this->sess->user_id;

		echo json_encode($callback);
	}

	function getuseridrole_driver(){
		$this->db = $this->load->database('default', true);
		$this->db->select("*");
		$this->db->order_by("privilege_id", "ASC");
		$this->db->where("privilege_id",9);
		$q        = $this->db->get("masterdata_privilege");
		return $q->result_array();
	}

	function save()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id                  = isset($_POST['id']) ? $_POST['id']:                         0;
		$username            = isset($_POST['username']) ? trim($_POST['username']):       "";
		$pass                = isset($_POST['pass']) ? trim($_POST['pass']):               "";
		$cpass               = isset($_POST['cpass']) ? trim($_POST['cpass']):             "";
		$type                = isset($_POST['type']) ? trim($_POST['type']):               "";
		$agent               = isset($_POST['agent']) ? trim($_POST['agent']):             "";
		$name                = isset($_POST['license']) ? trim($_POST['name']):            "";
		$license             = isset($_POST['license']) ? trim($_POST['license']):         "";
		$sex                 = isset($_POST['sex']) ? trim($_POST['sex']):                 "";
		$birthdate           = isset($_POST['birthdate']) ? trim($_POST['birthdate']):     "";
		$province            = isset($_POST['province']) ? trim($_POST['province']):       "";
		$city                = isset($_POST['city']) ? trim($_POST['city']):               "";
		$address             = isset($_POST['address']) ? trim($_POST['address']):         "";
		$mobile              = isset($_POST['mobile']) ? trim($_POST['mobile']):           "";
		$phone               = isset($_POST['phone']) ? trim($_POST['phone']):             "";
		$email               = isset($_POST['email']) ? trim($_POST['email']):             "";
		$usersite            = isset($_POST['usersite']) ? trim($_POST['usersite']):       0;
		$group               = isset($_POST['group']) ? trim($_POST['group']):             "";
		$agent_admin         = isset($_POST['agent_admin']) ? trim($_POST['agent_admin']): 0;
		$manengine           = isset($_POST['manengine']) ? trim($_POST['manengine']):     0;
		$manpasswd           = isset($_POST['manpasswd']) ? trim($_POST['manpasswd']):     0;
		$manprofile          = isset($_POST['manprofile']) ? trim($_POST['manprofile']):   1;
		$user_payment_type   = 0;
		$user_payment_period = 0;

		$user_payment_amount = 0;
		//$user_payment_amount = str_replace(",", "", $user_payment_amount);

		$user_payment_pulsa = 0;

		if (strlen($username) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_login");

			echo json_encode($callback);
			return;
		}

		if (preg_match("/\s+/", $username))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_login");

			echo json_encode($callback);
			return;
		}

		$this->db->where("user_login", $username);
		$q = $this->db->get("user");

		if ($q->num_rows() > 0)
		{
			$rowuser = $q->row();
			if ($rowuser->user_id != $id)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lexist_login");

				echo json_encode($callback);
				return;
			}
		}

		if (! $id)
		{

			if (strlen($pass) == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lempty_password");

				echo json_encode($callback);
				return;
			}

			if (strlen($pass) < 6)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lpassword_too_short");

				echo json_encode($callback);
				return;
			}

			if ($cpass != $pass)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("linvalid_cpass");

				echo json_encode($callback);
				return;
			}
		}

		// if ($type != 1)
		// {
		// 	if (strlen($agent) == 0)
		// 	{
		// 		$callback['error'] = true;
		// 		$callback['message'] = $this->lang->line("lempty_agent");
		//
		// 		echo json_encode($callback);
		// 		return;
		// 	}
		// }

		if (strlen($email))
		{
			if (! valid_email($email))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_email');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($birthdate) > 0)
		{
			$tbirthdate = formmaketime($birthdate." 00:00:00");

			if (date("d/m/Y", $tbirthdate) != $birthdate)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_birthdate');

				echo json_encode($callback);
				return;
			}

		}

		//  if ($type == 2){
		// 	 if (! $user_payment_type)
		// 	{
		// 		$callback['error'] = true;
		// 		$callback['message'] = $this->lang->line('lempty_payment_type');
		//
		// 		echo json_encode($callback);
		// 		return;
		// 	}
		//
		// 	if ((! is_numeric($user_payment_period)) || ($user_payment_period <= 0))
		// 	{
		// 		$callback['error'] = true;
		// 		$callback['message'] = $this->lang->line('linvalid_payment_period');
		//
		// 		echo json_encode($callback);
		// 		return;
		// 	}
		//
		// 	 if ((! is_numeric($user_payment_amount)) || ($user_payment_amount < 0))
		// 	{
		// 		$callback['error'] = true;
		// 		$callback['message'] = $this->lang->line('linvalid_payment_amount');
		//
		// 		echo json_encode($callback);
		// 		return;
		// 	}
		// }
		// USER LEVEL SETTING START
		// $user_levelawal = $this->sess->user_level;
		// if ($user_levelawal == 1) {
		// 	$user_level                  = $user_levelawal;
		// }else {
		$user_level                  = isset($_POST['user_level']) ? $_POST['user_level']:                           2;
		// }
		$branchoffice                = isset($_POST['branchoffice']) ? $_POST['branchoffice']:                       0;
		$subbranchoffice             = isset($_POST['subbranchoffice']) ? $_POST['subbranchoffice']:                 0;
		$customer                    = isset($_POST['customer']) ? $_POST['customer']:                               0;
		$subcustomer                 = isset($_POST['subcustomer']) ? $_POST['subcustomer']:                         0;

		$cur_user_level_old          = isset($_POST['cur_user_level_old']) ? $_POST['cur_user_level_old']:           0;
		$cur_branchoffice_old        = isset($_POST['cur_branchoffice_old']) ? $_POST['cur_branchoffice_old']:       0;
		$cur_subbranchoffice_old     = isset($_POST['cur_subbranchoffice_old']) ? $_POST['cur_subbranchoffice_old']: 0;
		$cur_customer_old            = isset($_POST['cur_customer_old']) ? $_POST['cur_customer_old']:               0;
		$cur_subcustomer_old         = isset($_POST['cur_subcustomer_old']) ? $_POST['cur_subcustomer_old']:         0;

		$userlevelfixforupdate       = "";
		$branchofficefixforupdate    = "";
		$subbranchofficefixforupdate = "";
		$customerfixforupdate        = "";
		$subcustomerfixforupdate     = "";

		// USER LEVEL SETTING END
		unset($data);
		$data['user_mail']            = $email;
		$data['user_name']            = $name;
		$data['user_license_id']      = $license;
		$data['user_license_type']    = 'A';
		$data['user_sex']             = $sex;
		$data['user_birth_date']      = isset($tbirthdate) ? date("Ymd", $tbirthdate): 0;
			// echo "<pre>";
			// var_dump($data['user_birth_date']);die();
			// echo "<pre>";
		$data['user_province']        = $province;
		$data['user_city']            = $city;
		$data['user_address']         = $address;
		$data['user_mobile']          = $mobile;
		$data['user_phone']           = $phone;
		$data['user_type']            = $type;
		$data['user_agent']           = ($type == 1) ? 0: $agent;
		$data['user_login']           = $username;
		$data['user_agent_admin']     = $agent_admin;
		$data['user_engine']          = $manengine;
		$data['user_manage_password'] = $manpasswd;
		$data['user_change_profile']  = $manprofile;
		$data['user_payment_type']    = $user_payment_type;
		$data['user_payment_period']  = $user_payment_period;
		$data['user_payment_amount']  = $user_payment_amount;
		$data['user_payment_pulsa']   = $user_payment_pulsa;
		$data['user_parent']         = $this->sess->user_id;


		if ($id)
		{
			// USER LEVEL 2 : USER BRANCH OFFICE
			// USER LEVEL 2 : YG HARUS DIPILIH ADALAH BRANCH OFFICENYA
			// SELAIN ITU HARUS DIPILIH EMPTY
			if ($user_level == 2 && $branchoffice == "") {
			 $callback['error']   = true;
 			 $callback['message'] = "Please select branch office";
 			 echo json_encode($callback);
 			 return;
			}

			if ($user_level == 2) {
				if ($subbranchoffice == "" || $subbranchoffice != "empty" || $customer == "" || $customer != "empty" || $subcustomer == "" || $subcustomer != "empty") {
					$callback['error']   = true;
	 			 $callback['message'] = "User level Branch Office, please select empty for all selection except Branch Office.";
	 			 echo json_encode($callback);
	 			 return;
				}
			}

			// USER LEVEL 3 : USER SUB BRANCH OFFICE
			// USER LEVEL 3 : YG HARUS DIPILIH ADALAH BRANCH, SUB BRANCH OFFICENYA
			// SELAIN ITU HARUS DIPILIH EMPTY
			if ($user_level == 3 && $branchoffice == "") {
			 $callback['error']   = true;
 			 $callback['message'] = "Please select branch office";
 			 echo json_encode($callback);
 			 return;
			}

			if ($user_level == 3) {
				if ($subbranchoffice == "" || $subbranchoffice == "empty") {
					$callback['error']   = true;
	  			 $callback['message'] = "Please select sub branch office";
	  			 echo json_encode($callback);
	  			 return;
				}
			}

			if ($user_level == 3) {
				if ($customer == "" || $customer != "empty" || $subcustomer == "" || $subcustomer != "empty") {
				 $callback['error']   = true;
				 $callback['message'] = "User level Sub Branch Office, please select empty for all selection except Branch Office and Sub Branch Office.";
				 echo json_encode($callback);
				 return;
				}
			}

			// USER LEVEL 4 : USER CUSTOMER
			// USER LEVEL 4 : YG HARUS DIPILIH ADALAH BRANCH, SUB BRANCH & CUSTOMER
			// SELAIN ITU HARUS DIPILIH EMPTY
			if ($user_level == 4 && $branchoffice == "") {
			 $callback['error']   = true;
 			 $callback['message'] = "Please select branch office";
 			 echo json_encode($callback);
 			 return;
			}

			if ($user_level == 4) {
				if ($subbranchoffice == "" || $subbranchoffice == "empty") {
					$callback['error']   = true;
	  			 $callback['message'] = "Please select sub branch office";
	  			 echo json_encode($callback);
	  			 return;
				}
			}

			if ($user_level == 4) {
				if ($customer == "" || $customer == "empty") {
					$callback['error']   = true;
	  			 $callback['message'] = "Please select customer";
	  			 echo json_encode($callback);
	  			 return;
				}
			}

			if ($user_level == 4) {
				if ($subcustomer == "" || $subcustomer != "empty") {
				 $callback['error']   = true;
				 $callback['message'] = "User level Customer, please select empty for all selection except Branch Office, Sub Branch Office and customer.";
				 echo json_encode($callback);
				 return;
				}
			}

			// USER LEVEL 5 : USER CUSTOMER
			// USER LEVEL 5 : YG HARUS DIPILIH ADALAH BRANCH, SUB BRANCH & CUSTOMER
			// SELAIN ITU HARUS DIPILIH EMPTY
			if ($user_level == 5 && $branchoffice == "") {
			 $callback['error']   = true;
			 $callback['message'] = "Please select branch office";
			 echo json_encode($callback);
			 return;
			}

			if ($user_level == 5) {
				if ($subbranchoffice == "" || $subbranchoffice == "empty") {
					$callback['error']   = true;
					 $callback['message'] = "Please select sub branch office";
					 echo json_encode($callback);
					 return;
				}
			}

			if ($user_level == 5) {
				if ($customer == "" || $customer == "empty") {
					$callback['error']   = true;
					 $callback['message'] = "Please select customer";
					 echo json_encode($callback);
					 return;
				}
			}

			if ($user_level == 5) {
				if ($subcustomer == "" || $subcustomer == "empty") {
				 $callback['error']   = true;
				 $callback['message'] = "User level Sub Customer, please select all selection.";
				 echo json_encode($callback);
				 return;
				}
			}
			//END

			if ($user_level == "") {
				$callback['error']   = true;
				$callback['message'] = "Please choose user level";
				echo json_encode($callback);
				return;
			}

			// JIKA PILIHAN KOSONG END
			if ($subbranchoffice == "empty") {
				// INPUT USER BRANCH OFFICE
				$data['user_company']    = $branchoffice;
				$data['user_subcompany'] = 0;
				$data['user_group']      = 0;
				$data['user_subgroup']   = 0;
			}elseif ($customer == "empty") {
				// INPUT USER CUSTOMER
				$data['user_company']    = $branchoffice;
				$data['user_subcompany'] = $subbranchoffice;
				$data['user_group']      = 0;
				$data['user_subgroup']   = 0;
			}else {
				// INPUT USER SUB CUSTOMER
				$data['user_company']    = $branchoffice;
				$data['user_subcompany'] = $subbranchoffice;
				$data['user_group']      = $customer;
				if ($subcustomer == "empty") {
					$data['user_subgroup']   = 0;
				}else {
					$data['user_subgroup']   = $subcustomer;
				}
			}

			$mydb = $this->load->database("master", TRUE);
			// JIKA USER LEVEL SAMA DGN YG LAMA
			if ($user_level == $cur_user_level_old || $user_level == "") {
				$userlevelfixforupdate = $cur_user_level_old;
			}else {
				$userlevelfixforupdate = $user_level;
			}
			$data['user_level']    				= $userlevelfixforupdate;

			// JIKA BRANCH OFFICE SAMA DGN YG LAMA
			if ($branchoffice == $cur_branchoffice_old || $branchoffice == "") {
				$branchofficefixforupdate = $cur_branchoffice_old;
			}else {
				$branchofficefixforupdate = $branchoffice;
			}

			// JIKA SUB BRANCH OFFICE SAMA DGN YG LAMA
			if ($subbranchoffice == $cur_subbranchoffice_old || $subbranchoffice == 0) {
				$subbranchofficefixforupdate = $cur_subbranchoffice_old;
			}else {
				$subbranchofficefixforupdate = $subbranchoffice;
			}

			// JIKA CUSTOMER SAMA DGN YG LAMA
			if ($customer == $cur_customer_old || $customer == 0) {
				$customerfixforupdate = $cur_customer_old;
			}else {
				$customerfixforupdate = $customer;
			}

			// JIKA SUB CUSTOMER SAMA DGN YG LAMA
			if ($subcustomer == $cur_subcustomer_old || $subcustomer == 0) {
				$subcustomerfixforupdate = $cur_subcustomer_old;
			}else {
				$subcustomerfixforupdate = $subcustomer;
			}

			if ($subbranchoffice == "empty") {
				// INPUT USER BRANCH OFFICE
				$data['user_company']    = $branchofficefixforupdate;
				$data['user_subcompany'] = 0;
				$data['user_group']      = 0;
				$data['user_subgroup']   = 0;
			}elseif ($customer == "empty") {
				// INPUT USER CUSTOMER
				$data['user_company']    = $branchofficefixforupdate;
				$data['user_subcompany'] = $subbranchofficefixforupdate;
				$data['user_group']      = 0;
				$data['user_subgroup']   = 0;
			}else {
				// INPUT USER SUB CUSTOMER
				$data['user_company']    = $branchofficefixforupdate;
				$data['user_subcompany'] = $subbranchofficefixforupdate;
				$data['user_group']      = $customerfixforupdate;
				if ($subcustomer == "empty") {
					$data['user_subgroup']   = 0;
				}else {
					$data['user_subgroup']   = $subcustomerfixforupdate;
				}
			}
			$data['user_dblive'] = $this->sess->user_dblive;
			$data['user_app']    = $this->sess->user_app;

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";
			$mydb->where("user_id", $id);
			$mydb->update("user", $data);

			/* //Update database lacakmobil
			$mydblacak = $this->load->database("masterlacak", TRUE);
			$mydblacak->where("user_id", $id);
			$mydblacak->update("user", $data); */

			$this->db->cache_delete_all();

			$callback['error']    = false;
			$callback['message']  = $this->lang->line("luser_updated");
			$callback['redirect'] = base_url()."account";

			echo json_encode($callback);

			return;
		}

		// USER LVL 2 : BRANCH OFFICE USER
		// get value dari branch office saja, sub branch office, customer, sub customer diisi dengan 0
		// JIKA PILIHAN KOSONG START
		$data['user_level']    				= $user_level;
		// USER LEVEL 2 : USER BRANCH OFFICE
		// USER LEVEL 2 : YG HARUS DIPILIH ADALAH BRANCH OFFICENYA
		// SELAIN ITU HARUS DIPILIH EMPTY
		if ($user_level == 2 && $branchoffice == "") {
		 $callback['error']   = true;
		 $callback['message'] = "Please select branch office";
		 echo json_encode($callback);
		 return;
		}

		if ($user_level == 2) {
			if ($subbranchoffice == "" || $subbranchoffice != "empty" || $customer == "" || $customer != "empty" || $subcustomer == "" || $subcustomer != "empty") {
				$callback['error']   = true;
			 $callback['message'] = "User level Branch Office, please select empty for all selection except Branch Office.";
			 echo json_encode($callback);
			 return;
			}
		}

		// USER LEVEL 3 : USER SUB BRANCH OFFICE
		// USER LEVEL 3 : YG HARUS DIPILIH ADALAH BRANCH, SUB BRANCH OFFICENYA
		// SELAIN ITU HARUS DIPILIH EMPTY
		if ($user_level == 3 && $branchoffice == "") {
		 $callback['error']   = true;
		 $callback['message'] = "Please select branch office";
		 echo json_encode($callback);
		 return;
		}

		if ($user_level == 3) {
			if ($subbranchoffice == "" || $subbranchoffice == "empty") {
				$callback['error']   = true;
				 $callback['message'] = "Please select sub branch office";
				 echo json_encode($callback);
				 return;
			}
		}

		if ($user_level == 3) {
			if ($customer == "" || $customer != "empty" || $subcustomer == "" || $subcustomer != "empty") {
			 $callback['error']   = true;
			 $callback['message'] = "User level Sub Branch Office, please select empty for all selection except Branch Office and Sub Branch Office.";
			 echo json_encode($callback);
			 return;
			}
		}

		// USER LEVEL 4 : USER CUSTOMER
		// USER LEVEL 4 : YG HARUS DIPILIH ADALAH BRANCH, SUB BRANCH & CUSTOMER
		// SELAIN ITU HARUS DIPILIH EMPTY
		if ($user_level == 4 && $branchoffice == "") {
		 $callback['error']   = true;
		 $callback['message'] = "Please select branch office";
		 echo json_encode($callback);
		 return;
		}

		if ($user_level == 4) {
			if ($subbranchoffice == "" || $subbranchoffice == "empty") {
				$callback['error']   = true;
				 $callback['message'] = "Please select sub branch office";
				 echo json_encode($callback);
				 return;
			}
		}

		if ($user_level == 4) {
			if ($customer == "" || $customer == "empty") {
				$callback['error']   = true;
				 $callback['message'] = "Please select customer";
				 echo json_encode($callback);
				 return;
			}
		}

		if ($user_level == 4) {
			if ($subcustomer == "" || $subcustomer != "empty") {
			 $callback['error']   = true;
			 $callback['message'] = "User level Customer, please select empty for all selection except Branch Office, Sub Branch Office and customer.";
			 echo json_encode($callback);
			 return;
			}
		}

		// USER LEVEL 5 : USER CUSTOMER
		// USER LEVEL 5 : YG HARUS DIPILIH ADALAH BRANCH, SUB BRANCH & CUSTOMER
		// SELAIN ITU HARUS DIPILIH EMPTY
		if ($user_level == 5 && $branchoffice == "") {
		 $callback['error']   = true;
		 $callback['message'] = "Please select branch office";
		 echo json_encode($callback);
		 return;
		}

		if ($user_level == 5) {
			if ($subbranchoffice == "" || $subbranchoffice == "empty") {
				$callback['error']   = true;
				 $callback['message'] = "Please select sub branch office";
				 echo json_encode($callback);
				 return;
			}
		}

		if ($user_level == 5) {
			if ($customer == "" || $customer == "empty") {
				$callback['error']   = true;
				 $callback['message'] = "Please select customer";
				 echo json_encode($callback);
				 return;
			}
		}

		if ($user_level == 5) {
			if ($subcustomer == "" || $subcustomer == "empty") {
			 $callback['error']   = true;
			 $callback['message'] = "User level Sub Customer, please select all selection.";
			 echo json_encode($callback);
			 return;
			}
		}
		//END

		// JIKA PILIHAN KOSONG END
		if ($subbranchoffice == "empty") {
			// INPUT USER BRANCH OFFICE
			$data['user_company']    = $branchoffice;
			$data['user_subcompany'] = 0;
			$data['user_group']      = 0;
			$data['user_subgroup']   = 0;
		}elseif ($customer == "empty") {
			// INPUT USER CUSTOMER
			$data['user_company']    = $branchoffice;
			$data['user_subcompany'] = $subbranchoffice;
			$data['user_group']      = 0;
			$data['user_subgroup']   = 0;
		}else {
			// INPUT USER SUB CUSTOMER
			$data['user_company']    = $branchoffice;
			$data['user_subcompany'] = $subbranchoffice;
			$data['user_group']      = $customer;
			if ($subcustomer == "empty") {
				$data['user_subgroup']   = 0;
			}else {
				$data['user_subgroup']   = $subcustomer;
			}
		}

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$data['user_pass']           = $pass;
		$data['user_dblive']         = $this->sess->user_dblive;
		$data['user_app']    				 = $this->sess->user_app;
		$data['user_parent']         = $this->sess->user_id;
		$data['user_lastlogin_date'] = 0;
		$data['user_lastlogin_time'] = 0;
		$data['user_photo']          = "";
		$data['user_zipcode']        = "";
		$data['user_status']         = 1;
		$data['user_create_date']    = date("Ymd");

		$mydb = $this->load->database("master", TRUE);

		$mydb->insert("user", $data);

		$userid = $mydb->insert_id();

		$sql = "UPDATE ".$this->db->dbprefix."user SET user_pass = PASSWORD('".mysql_escape_string($pass)."') WHERE user_id = '".$userid."'";

		$this->db->query($sql);

		$this->db->cache_delete_all();

		$callback['error'] = false;
		$callback['message'] = $this->lang->line("luser_added");
		$callback['redirect'] = base_url()."account";

		echo json_encode($callback);
		return;
	}

	function updateUser()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id             = isset($_POST['id']) ? $_POST['id']: 0;
		$username       = $this->input->post('name');
		$email          = $this->input->post('email');
		$sex            = $this->input->post('sex');
		$birthdate      = $this->input->post('birthdate');
		$address        = $this->input->post('address');
		$mobile         = $this->input->post('mobile');
		$privilege      = $this->input->post('privilege');
		$locallogin     = $this->input->post('locallogin');
		$userexcavator  = $this->input->post('userexcavator');
		$iscompany_exca = $this->input->post('iscompany_exca');

		// echo "<pre>";
		// var_dump($userexcavator.'-'.$iscompany_exca);die();
		// echo "<pre>";


		if($privilege == 5 || $privilege == 6 || $privilege == 10){
			$pjolist        = $this->input->post('pjolist');
			$pjolistexplode = explode("|", $pjolist);
		}else{
			$pjolist = 1922; //TEMAN INDOBARA
		}

			if (!$locallogin) {
				$locallogin     = 0;
			}else {
				$locallogin        = 1;
			}

			if (!$userexcavator) {
				$userexcavator  = 0;
			}else {
				$userexcavator  = 1;
			}

		$this->db->where("user_login", $email);
		$q = $this->db->get("user");

		if ($q->num_rows() > 0)
		{
			$rowuser = $q->row();
			if ($rowuser->user_id != $id)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lexist_login");

				echo json_encode($callback);
				return;
			}
		}
		
		//SID
		if($email == ""){
				
			$callback['error'] = true;
			$callback['message'] = "Please Input SID";

			echo json_encode($callback);
			return;	
		}
		
		if (strlen($birthdate) > 0)
		{
			$tbirthdate = formmaketime($birthdate." 00:00:00");

			if (date("d/m/Y", $tbirthdate) != $birthdate)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_birthdate');

				echo json_encode($callback);
				return;
			}

		}


		// FOR UPDATE
		unset($data);
		$data['user_login']       = $email;
		$data['user_name']        = $username;
		$data['user_mail']        = $email;
		$data['user_sex']         = $sex;
		$data['user_birth_date']  = isset($tbirthdate) ? date("Ymd", $tbirthdate): 0;
		$data['user_address']     = $address;
		$data['user_mobile']      = $mobile;
		$data['user_local_login'] = $locallogin;
		$data['user_id_role']     = $privilege;

		if ($iscompany_exca == 1) {
			$data['user_excavator']   = 1;
		}elseif ($iscompany_exca == 2) {
			$data['user_excavator']   = $userexcavator;
		}else {
			$data['user_excavator'] = 0;
		}


		if ($privilege == 5 || $privilege == 6 || $privilege == 10) {
			$data['user_company']     = $pjolistexplode[0];
			$data['user_parent']      = $pjolistexplode[0];
		}else {
			$data['user_company']   = $pjolist;
			$data['user_parent']    = 4408;
		}


		// echo "<pre>";
		// var_dump($data['user_excavator']);die();
		// echo "<pre>";

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("user_id", $id);
		$mydb->update("user", $data);

		$this->db->cache_delete_all();

		$callback['error']    = false;
		$callback['message']  = $this->lang->line("luser_updated");
		$callback['redirect'] = base_url()."account";		//
		echo json_encode($callback);
		return;
	}

	function updateUserDriver()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id         = isset($_POST['id']) ? $_POST['id']: 0;
		$username   = $this->input->post('name');
		$email      = $this->input->post('email');
		$sex        = $this->input->post('sex');
		$birthdate  = $this->input->post('birthdate');
		$address    = $this->input->post('address');
		$mobile     = $this->input->post('mobile');
		$privilege  = $this->input->post('privilege');
		$locallogin = $this->input->post('locallogin');

		if($privilege == 5 || $privilege == 6 || $privilege == 9) {
			$pjolist = $this->input->post('pjolist');
		}else{
			$pjolist = 1922; //TEMAN INDOBARA
		}

			if (!$locallogin) {
				$locallogin = 0;
			}else {
				$locallogin = 1;
			}

		$this->db->where("user_login", $email);
		$q = $this->db->get("user");

		if ($q->num_rows() > 0)
		{
			$rowuser = $q->row();
			if ($rowuser->user_id != $id)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line("lexist_login");

				echo json_encode($callback);
				return;
			}
		}

		if ($email == "") {
			$callback['error'] = true;
			$callback['message'] = "Simper tidak boleh kosong";

			echo json_encode($callback);
			return;
		}

		if (strlen($birthdate) > 0)
		{
			$tbirthdate = formmaketime($birthdate." 00:00:00");

			if (date("d/m/Y", $tbirthdate) != $birthdate)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_birthdate');

				echo json_encode($callback);
				return;
			}

		}


		// FOR UPDATE
		unset($data);
		$data['user_login']       = $email;
		$data['user_name']        = $username;
		$data['user_mail']        = $email;
		$data['user_sex']         = $sex;
		$data['user_birth_date']  = isset($tbirthdate) ? date("Ymd", $tbirthdate): 0;
		$data['user_address']     = $address;
		$data['user_mobile']      = $mobile;
		$data['user_local_login'] = $locallogin;
		$data['user_id_role']     = $privilege;

		$data['user_company']     = $pjolist;


		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("user_id", $id);
		$mydb->update("user", $data);

		$this->db->cache_delete_all();

		$callback['error']    = false;
		$callback['message']  = $this->lang->line("luser_updated");
		$callback['redirect'] = base_url()."account/driver";		//
		echo json_encode($callback);
		return;
	}

	function remove($id)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		$this->db->where("user_id", $id);
		$q = $this->db->get("user");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("user_id", $row->user_id);
		$mydb->delete("user");

		$this->db->cache_delete_all();

		redirect(base_url()."account");
	}

	function status($id)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		$this->db->where("user_id", $id);
		$q = $this->db->get("user");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		$status = ($row->user_status == 1) ? 2 : 1;

		unset($data);
		$data['user_status'] = $status;

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("user_id", $id);
		$mydb->update("user", $data);

		$this->db->cache_delete_all();

		redirect(base_url()."account/");
	}

	function savevehicle($isman=0)
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$vehicle_id = isset($_POST['vehicle_id']) ? trim($_POST['vehicle_id']) : "";

		if ($this->sess->user_type == 2)
		{
			if (! $this->sess->user_company)
			{
				redirect(base_url());
			}

			if ($this->sess->user_group)
			{
				redirect(base_url());
			}

			if (! $vehicle_id)
			{
				redirect(base_url());
			}

			$vehicleids = $this->vehiclemodel->getVehicleIds();

			if (! in_array($vehicle_id, $vehicleids))
			{
				redirect(base_url());
			}
		}

		$vehicle_user_id = isset($_POST['vehicle_user_id']) ? trim($_POST['vehicle_user_id']) : "";
		$vehicle_device = isset($_POST['vehicle_device']) ? trim($_POST['vehicle_device']) : "";
		$vehicle_type = isset($_POST['vehicle_type']) ? trim($_POST['vehicle_type']) : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? trim($_POST['vehicle_no']) : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : "";
		$vehicle_active_date1 = isset($_POST['vehicle_active_date1']) ? trim($_POST['vehicle_active_date1']) : "";
		$vehicle_active_date2 = isset($_POST['vehicle_active_date2']) ? trim($_POST['vehicle_active_date2']) : "";

		$vehicle_card_no = isset($_POST['vehicle_card_no']) ? trim($_POST['vehicle_card_no']) : "";
		$vehicle_card_no = str_replace(" ", "", $vehicle_card_no);

		$vehicle_operator = isset($_POST['vehicle_operator']) ? trim($_POST['vehicle_operator']) : "";
		$vehicle_active_date = isset($_POST['vehicle_active_date']) ? trim($_POST['vehicle_active_date']) : "";

		$vehicle_maxspeed = isset($_POST['vehicle_maxspeed']) ? trim($_POST['vehicle_maxspeed']) : "";
		$vehicle_maxspeed = str_replace(",", ".", $vehicle_maxspeed);

		$vehicle_maxparking = isset($_POST['vehicle_maxparking']) ? trim($_POST['vehicle_maxparking']) : "";
		$vehicle_maxparking = str_replace(",", ".", $vehicle_maxparking);

		$vehicle_odometer = isset($_POST['vehicle_odometer']) ? trim($_POST['vehicle_odometer']) : 0;
		$vehicle_odometer = str_replace(",", ".", $vehicle_odometer);

		$vehicle_image = isset($_POST['vehicle_image']) ? trim($_POST['vehicle_image']) : "";
		$vehicle_group = isset($_POST['group']) ? trim($_POST['group']) : "";
		$vehicle_company = isset($_POST['usersite']) ? trim($_POST['usersite']) : 0;
		$vehicle_ip = isset($_POST['vehicle_ip']) ? trim($_POST['vehicle_ip']) : "";

		if ($this->sess->user_type != 2)
		{
			if (strlen($vehicle_device) == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lempty_vehicle_device');

				echo json_encode($callback);
				return;
			}

			if ($vehicle_id)
			{
				$this->db->where("vehicle_id <>", $vehicle_id);
			}

			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_device", $vehicle_device);
			$total = $this->db->count_all_results("vehicle");

			if ($total)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lexist_vehicle_device');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_no) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_no');

			echo json_encode($callback);
			return;
		}

		if ($vehicle_id)
		{
			$this->db->where("vehicle_id <>", $vehicle_id);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_no", $vehicle_no);
		$total = $this->db->count_all_results("vehicle");
		if ($total)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lexist_vehicle_no');

			echo json_encode($callback);
			return;
		}

		if (strlen($vehicle_name) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_name');

			echo json_encode($callback);
			return;
		}

		if ($this->sess->user_type != 2)
		{
			if (strlen($vehicle_active_date1) > 0)
			{
				$t1 = formmaketime($vehicle_active_date1." 00:00:00");
				if (date("d/m/Y", $t1) != $vehicle_active_date1)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_start_expired_vehicle');

					echo json_encode($callback);
					return;
				}
			}

			if (strlen($vehicle_active_date2) > 0)
			{
				$t2 = formmaketime($vehicle_active_date2." 00:00:00");
				if (date("d/m/Y", $t2) != $vehicle_active_date2)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_end_expired_vehicle');

					echo json_encode($callback);
					return;
				}
			}

			if (isset($t1) && isset($t2))
			{
				if ($t1 > $t2)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_expired_vehicle');

					echo json_encode($callback);
					return;
				}
			}

			if (strlen($vehicle_active_date) > 0)
			{
				$t = formmaketime($vehicle_active_date." 00:00:00");
				if (date("d/m/Y", $t) != $vehicle_active_date)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_expired_card_no');

					echo json_encode($callback);
					return;
				}
			}

		}

		if (strlen($vehicle_odometer))
		{
			if ((! is_numeric($vehicle_odometer)) || ($vehicle_odometer < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_initialodometer');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_maxspeed))
		{
			if ((! is_numeric($vehicle_maxspeed)) || ($vehicle_maxspeed < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_maxspeed');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_maxparking))
		{
			if ((! is_numeric($vehicle_maxparking)) || ($vehicle_maxparking < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_maxparkingtime');

				echo json_encode($callback);
				return;
			}
		}

		if ($this->sess->user_type != 2)
		{
			if (strlen($vehicle_card_no) == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lvehicle_card_no_empty');

				echo json_encode($callback);
				return;
			}

			$this->db->where("vehicle_status", 1);
			$this->db->where("vehicle_card_no", $vehicle_card_no);
			$q = $this->db->get("vehicle");

			if ($q->num_rows() > 0)
			{
				$rowsimcard = $q->row();
				if ($rowsimcard->vehicle_id != $vehicle_id)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('lvehicle_card_no_exist');

					echo json_encode($callback);
					return;
				}
			}
		}

		unset($json);
		$json->vehicle_ip = $vehicle_ip;

		unset($data);

		if ($this->sess->user_type != 2)
		{
			$data['vehicle_device'] = $vehicle_device;
			$data['vehicle_active_date2'] = isset($t2) ? date("Ymd", $t2) : "1990-01-01 00:00:00";
			$data['vehicle_card_no'] = $vehicle_card_no;
			$data['vehicle_operator'] = $vehicle_operator;
			$data['vehicle_active_date'] = isset($t) ? date("Ymd", $t) : "1990-01-01 00:00:00";
			$data['vehicle_active_date1'] = isset($t1) ? date("Ymd", $t1) : "1990-01-01 00:00:00";
			$data['vehicle_status'] = 1;
			$data['vehicle_image'] = $vehicle_image;
			$data['vehicle_type'] = $vehicle_type;
			$data['vehicle_user_id'] = $vehicle_user_id;
			//$data['vehicle_group'] = $vehicle_group; //ada bug jadi nol
			$data['vehicle_company'] = $vehicle_company;
		}

		$data['vehicle_info'] = json_encode($json);
		$data['vehicle_no'] = $vehicle_no;
		$data['vehicle_name'] = $vehicle_name;
		$data['vehicle_maxspeed'] = $vehicle_maxspeed;
		$data['vehicle_maxparking'] = $vehicle_maxparking;
		$data['vehicle_odometer'] = $vehicle_odometer;

		if (! $vehicle_id)
		{
			if ($this->sess->user_type != 2)
			{
				$data['vehicle_created_date'] = date("Y-m-d H:i:s");
				$data['vehicle_autorefill'] = 0;

				$this->db->insert("vehicle", $data);
				$this->db->cache_delete_all();

				$url = sprintf("http://%s/sms/newvehicle/%s/%s", $this->config->item("georeverse_host"), $vehicle_user_id, $vehicle_no);
				$params['smsnotice'] = 1;
				curl_post_async($url, $params);

				$message = "";
				foreach($data as $key=>$val)
				{
					$message .= sprintf("%s = %s", $key, $val);
				}

				@mail("owner@adilahsoft.com, prastgtx@gmail.com, jaya@vilanishop.com", "new vehicle ".$vehicle_no, $message);

				$callback['message'] = $this->lang->line('lvehicle_added');
				$callback['error'] = false;
				echo json_encode($callback);
			}
			return;
		}

		$this->db->where("vehicle_id", $vehicle_id);
		$this->db->update("vehicle", $data);

		$this->db->cache_delete_all();

		$callback['message'] = $this->lang->line('lvehicle_updated');
		$callback['error'] = false;
		echo json_encode($callback);

	}

	function savevehicle_tag($isman=0)
	{

		$vehicle_id = isset($_POST['vehicle_id']) ? trim($_POST['vehicle_id']) : "";

		if ($this->sess->user_type == 2)
		{
			if (! $this->sess->user_company)
			{
				redirect(base_url());
			}

			if ($this->sess->user_group)
			{
				redirect(base_url());
			}

			if (! $vehicle_id)
			{
				redirect(base_url());
			}

			$vehicleids = $this->vehiclemodel->getVehicleIds();

			if (! in_array($vehicle_id, $vehicleids))
			{
				redirect(base_url());
			}
		}

		$vehicle_user_id = isset($_POST['vehicle_user_id']) ? trim($_POST['vehicle_user_id']) : "";
		$vehicle_device = isset($_POST['vehicle_device']) ? trim($_POST['vehicle_device']) : "";
		$vehicle_type = isset($_POST['vehicle_type']) ? trim($_POST['vehicle_type']) : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? trim($_POST['vehicle_no']) : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : "";
		$vehicle_active_date1 = isset($_POST['vehicle_active_date1']) ? trim($_POST['vehicle_active_date1']) : "";
		$vehicle_active_date2 = isset($_POST['vehicle_active_date2']) ? trim($_POST['vehicle_active_date2']) : "";

		$vehicle_card_no = isset($_POST['vehicle_card_no']) ? trim($_POST['vehicle_card_no']) : "";
		$vehicle_card_no = str_replace(" ", "", $vehicle_card_no);

		$vehicle_operator = isset($_POST['vehicle_operator']) ? trim($_POST['vehicle_operator']) : "";
		$vehicle_active_date = isset($_POST['vehicle_active_date']) ? trim($_POST['vehicle_active_date']) : "";

		$vehicle_maxspeed = isset($_POST['vehicle_maxspeed']) ? trim($_POST['vehicle_maxspeed']) : "";
		$vehicle_maxspeed = str_replace(",", ".", $vehicle_maxspeed);

		$vehicle_maxparking = isset($_POST['vehicle_maxparking']) ? trim($_POST['vehicle_maxparking']) : "";
		$vehicle_maxparking = str_replace(",", ".", $vehicle_maxparking);

		$vehicle_odometer = isset($_POST['vehicle_odometer']) ? trim($_POST['vehicle_odometer']) : 0;
		$vehicle_odometer = str_replace(",", ".", $vehicle_odometer);

		$vehicle_image = isset($_POST['vehicle_image']) ? trim($_POST['vehicle_image']) : "";
		$vehicle_group = isset($_POST['group']) ? trim($_POST['group']) : "";
		$vehicle_company = isset($_POST['usersite']) ? trim($_POST['usersite']) : 0;
		$vehicle_ip = isset($_POST['vehicle_ip']) ? trim($_POST['vehicle_ip']) : "";

		$vehicle_no_old = isset($_POST['vehicle_no_old']) ? trim($_POST['vehicle_no_old']) : "";
		$vehicle_name_old = isset($_POST['vehicle_name_old']) ? trim($_POST['vehicle_name_old']) : "";
		$vehicle_name_new = isset($_POST['vehicle_name_new']) ? trim($_POST['vehicle_name_new']) : "";
		$vehicle_lm_new = isset($_POST['vehicle_lm_new']) ? trim($_POST['vehicle_lm_new']) : "";
		$vehicle_no_new = isset($_POST['vehicle_no_new']) ? trim($_POST['vehicle_no_new']) : "";

		if ($this->sess->user_type != 2)
		{
			if (strlen($vehicle_device) == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lempty_vehicle_device');

				echo json_encode($callback);
				return;
			}

			if ($vehicle_id)
			{
				$this->db->where("vehicle_id <>", $vehicle_id);
			}

			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_device", $vehicle_device);
			$total = $this->db->count_all_results("vehicle");

			if ($total)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lexist_vehicle_device');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_no) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_no');

			echo json_encode($callback);
			return;
		}

		if ($vehicle_id)
		{
			$this->db->where("vehicle_id <>", $vehicle_id);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_no", $vehicle_no);
		$total = $this->db->count_all_results("vehicle");
		if ($total)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lexist_vehicle_no');

			echo json_encode($callback);
			return;
		}

		if (strlen($vehicle_name) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_name');

			echo json_encode($callback);
			return;
		}

		if ($this->sess->user_type != 2)
		{
			if (strlen($vehicle_active_date1) > 0)
			{
				$t1 = formmaketime($vehicle_active_date1." 00:00:00");
				if (date("d/m/Y", $t1) != $vehicle_active_date1)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_start_expired_vehicle');

					echo json_encode($callback);
					return;
				}
			}

			if (strlen($vehicle_active_date2) > 0)
			{
				$t2 = formmaketime($vehicle_active_date2." 00:00:00");
				if (date("d/m/Y", $t2) != $vehicle_active_date2)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_end_expired_vehicle');

					echo json_encode($callback);
					return;
				}
			}

			if (isset($t1) && isset($t2))
			{
				if ($t1 > $t2)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_expired_vehicle');

					echo json_encode($callback);
					return;
				}
			}

			if (strlen($vehicle_active_date) > 0)
			{
				$t = formmaketime($vehicle_active_date." 00:00:00");
				if (date("d/m/Y", $t) != $vehicle_active_date)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('linvalid_expired_card_no');

					echo json_encode($callback);
					return;
				}
			}

		}

		if (strlen($vehicle_odometer))
		{
			if ((! is_numeric($vehicle_odometer)) || ($vehicle_odometer < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_initialodometer');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_maxspeed))
		{
			if ((! is_numeric($vehicle_maxspeed)) || ($vehicle_maxspeed < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_maxspeed');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_maxparking))
		{
			if ((! is_numeric($vehicle_maxparking)) || ($vehicle_maxparking < 0))
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_maxparkingtime');

				echo json_encode($callback);
				return;
			}
		}

		if ($this->sess->user_type != 2)
		{
			if (strlen($vehicle_card_no) == 0)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lvehicle_card_no_empty');

				echo json_encode($callback);
				return;
			}

			$this->db->where("vehicle_status", 1);
			$this->db->where("vehicle_card_no", $vehicle_card_no);
			$q = $this->db->get("vehicle");

			if ($q->num_rows() > 0)
			{
				$rowsimcard = $q->row();
				if ($rowsimcard->vehicle_id != $vehicle_id)
				{
					$callback['error'] = true;
					$callback['message'] = $this->lang->line('lvehicle_card_no_exist');

					echo json_encode($callback);
					return;
				}
			}
		}

		unset($json);
		$json->vehicle_ip = $vehicle_ip;

		unset($data);

		if ($this->sess->user_type != 2)
		{
			$data['vehicle_device'] = $vehicle_device;
			$data['vehicle_active_date2'] = isset($t2) ? date("Ymd", $t2) : "1990-01-01 00:00:00";
			$data['vehicle_card_no'] = $vehicle_card_no;
			$data['vehicle_operator'] = $vehicle_operator;
			$data['vehicle_active_date'] = isset($t) ? date("Ymd", $t) : "1990-01-01 00:00:00";
			$data['vehicle_active_date1'] = isset($t1) ? date("Ymd", $t1) : "1990-01-01 00:00:00";
			$data['vehicle_status'] = 1;
			$data['vehicle_image'] = $vehicle_image;
			$data['vehicle_type'] = $vehicle_type;
			$data['vehicle_user_id'] = $vehicle_user_id;
			$data['vehicle_group'] = $vehicle_group;
			$data['vehicle_company'] = $vehicle_company;
		}

		$data['vehicle_info'] = json_encode($json);
		if($vehicle_no_new != ""){
			$data['vehicle_no'] = $vehicle_lm_new." ~ ".$vehicle_no_new;
		}else{
			$data['vehicle_no'] = $vehicle_no_old;
		}

		if($vehicle_name_new != ""){
			$data['vehicle_name'] = $vehicle_name_new;
		}else{
			$data['vehicle_name'] = $vehicle_name_old;
		}

		$data['vehicle_maxspeed'] = $vehicle_maxspeed;
		$data['vehicle_maxparking'] = $vehicle_maxparking;
		$data['vehicle_odometer'] = $vehicle_odometer;

		if (! $vehicle_id)
		{
			if ($this->sess->user_type != 2)
			{
				$data['vehicle_created_date'] = date("Y-m-d H:i:s");
				$data['vehicle_autorefill'] = 0;

				$this->db->insert("vehicle", $data);
				$this->db->cache_delete_all();

				$url = sprintf("http://%s/sms/newvehicle/%s/%s", $this->config->item("georeverse_host"), $vehicle_user_id, $vehicle_no);
				$params['smsnotice'] = 1;
				curl_post_async($url, $params);

				$message = "";
				foreach($data as $key=>$val)
				{
					$message .= sprintf("%s = %s", $key, $val);
				}

				@mail("owner@adilahsoft.com, prastgtx@gmail.com, jaya@vilanishop.com", "new vehicle ".$vehicle_no, $message);

				$callback['message'] = $this->lang->line('lvehicle_added');
				$callback['error'] = false;
				echo json_encode($callback);
			}
			return;
		}

		$this->db->where("vehicle_id", $vehicle_id);
		$this->db->update("vehicle", $data);

		$this->db->cache_delete_all();

		$callback['message'] = $this->lang->line('lvehicle_updated');
		$callback['error'] = false;
		echo json_encode($callback);

	}

	function formvehicle()
	{
		if ($this->sess->user_type == 2)
		{
			if (! $this->sess->user_company)
			{
				$callback['error'] = true;
				echo json_encode($callback);
				return;
			}

			if ($this->sess->user_group)
			{
				$callback['error'] = true;
				echo json_encode($callback);
				return;
			}

			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}

		$vid = isset($_POST['id']) ? $_POST['id'] : "";
		$uid = isset($_POST['uid']) ? $_POST['uid'] : "";

		$params['uid'] = $uid;

		if ($vid)
		{
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}
			else
			if ($this->sess->user_type == 2)
			{
				$this->db->where_in("vehicle_id", $vehicleids);
			}

			$this->db->where("vehicle_id", $vid);
			$this->db->join("user", "user_id = vehicle_user_id");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				$callback['error'] = true;
				echo json_encode($callback);
				return;
			}

			$row = $q->row();

			$row->vehicle_active_date1_t = dbintmaketime($row->vehicle_active_date1, 0);
			$row->vehicle_active_date2_t = dbintmaketime($row->vehicle_active_date2, 0);
			$row->vehicle_active_date_t = dbintmaketime($row->vehicle_active_date, 0);

			$json = json_decode($row->vehicle_info);
			$row->vehicle_ip = isset($json->vehicle_ip) ? $json->vehicle_ip : $this->config->item("ip_colo");


			$params['vehicle'] = $row;
			$params['owner'] = $row->vehicle_user_id;
		}
		else
		{
			$params['owner'] = $uid;
		}

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		else
		if ($this->sess->user_type == 2)
		{
			$this->db->where("user_id", $this->sess->user_id);
		}

		$this->db->order_by("user_name", "asc");
		$q = $this->db->get("user");

		$params["users"] = $q->result();

		if ($this->sess->user_type == 2)
		{
			$this->db->where("company_id", $this->sess->user_company);
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("company_agent", $this->sess->user_agent);
		}
		$this->db->order_by("company_name", "asc");
		$q = $this->db->get("company");

		$rowcompanies = $q->result();

		$params["companies"] = $rowcompanies;

		$this->db->distinct();
		$this->db->select("fuel_tank_capacity");
		$qfuel = $this->db->get("fuel");

		if($qfuel->num_rows()>0){
			$rfuel = $qfuel->result();

			$params['fuel'] = $rfuel;
		}

		if ($this->sess->user_type == 2)
		{
			$html = $this->load->view("user/formvehicle_byuser", $params, true);
		}
		else
		{
			$html = $this->load->view("user/formvehicle", $params, true);
		}

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function changeport($id)
	{
		$this->db->where("vehicle_id", $id);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Access denied.";
			echo json_encode($callback);
			return;
		}

		$row = $q->row();

		$vreplaces = $this->config->item('vehicle_type_replace');
		if (! isset($vreplaces[$row->vehicle_type]))
		{
			$callback['error'] = true;
			$callback['message'] = "Access denied.";
			echo json_encode($callback);
			return;
		}

		unset($update);

		$update['vehicle_type'] = $vreplaces[$row->vehicle_type];

		$mydb = $this->load->database("master", TRUE);
		$mydb->where("vehicle_id", $id);
		$mydb->update("vehicle", $update);

		$this->db->cache_delete_all();

		$callback['error'] = false;
		$callback['message'] = $this->lang->line('lchangeport_success');
		echo json_encode($callback);
	}

	function reqinfo()
	{

		$this->db->where("user_id", $this->sess->user_id);
		$q = $this->db->get("user");

		$row = $q->row();

		$this->params['row'] = $row;

		$lang = $this->config->item("session_lang") ? $this->config->item("session_lang") : $this->config->item("language");

		$this->params['header'] = $this->load->view('user/'.$lang.'/reqinfoheader', $this->params, true);
		$callback['title'] = $this->lang->line('lreq_info');
		$callback['html'] = $this->load->view('user/reqinfo', $this->params, true);

		echo json_encode($callback);
	}

	function cekreqinfo()
	{
		$this->db->where("user_id", $this->sess->user_id);
		$q = $this->db->get("user");

		$row = $q->row();

		$hp = valid_mobiles($row->user_mobile);
		$callback['iscomplete'] = (strlen($row->user_mail) > 0) && ($hp !== FALSE) && (strlen($row->user_address) > 0);

		echo json_encode($callback);
	}

	function savereqinfo()
	{
		$mail = isset($_POST['email']) ? trim($_POST['email']) : "";
		$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : "";
		$mobile = valid_mobiles($mobile);
		$address = isset($_POST['address']) ? trim($_POST['address']) : "";

		if (! $mail)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_email');
			echo json_encode($callback);

			return;
		}

		if (! valid_emails($mail))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_email');
			echo json_encode($callback);

			return;
		}

		if ($mobile === FALSE)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_mobile');
			echo json_encode($callback);

			return;
		}

		if (! $address)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_address');
			echo json_encode($callback);

			return;
		}

		unset($data);
		$data['user_mail'] = $mail;
		$data['user_mobile'] = $mobile;
		$data['user_address'] = $address;

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("user_id", $this->sess->user_id);
		$mydb->update("user", $data);

		$this->db->cache_delete_all();

		$callback['error'] = false;
		echo json_encode($callback);
	}
  // USER FUNCTION END

  // BRANCH FUNCTION START
  function branch(){
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 			 = $this->sess->user_id_role;

    $rows_branch         = $this->getbranch();

    // $total_company = $this->db->count_all_results("company");
			if ($privilegecode == 0) {
				$this->db->where("company_user_parent", $user_parent);
			}elseif ($privilegecode == 1) {
				$this->db->where("company_user_parent", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("company_user_parent", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("company_user_parent", $user_parent);
			}

		$this->db->where("company_flag", 0);
    $q     = $this->db->get("company");
    $rows  = $q->result();
    $total = count($rows);

    $this->params["total"]             = $total;
    $this->params["data"]              = $rows;
    $this->params["branch"]            = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "branchoffice";
		$this->params['privilegecode']     = $privilegecode;

    // echo "<pre>";
    // var_dump($rows);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/branch/v_branch', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/branch/v_branch', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/branch/v_branch', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/branch/v_branch', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/branch/v_branch', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

  function getbranch(){
		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;

    $this->dbtransporter = $this->load->database('transporter', true);
    $this->dbtransporter->select("*");
    $this->dbtransporter->from("branch");
			if ($privilegecode == 0) {
				$this->dbtransporter->where("branch_created_by", $user_id);
			}elseif ($privilegecode == 1) {
				$this->dbtransporter->where("branch_created_by", $user_parent);
			}
    $qbranch     = $this->dbtransporter->get();
    $rows_branch = $qbranch->result();
    return $rows_branch;
  }

	function savebranchoffice(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

			$company_name              = isset($_POST["branch_name"]) ? $_POST["branch_name"]:                             "";
			$company_agent             = $this->config->item("transporter_agent");
			$company_created_by        = $this->sess->user_id;
			$company_telegram_sos      = isset($_POST["company_telegram_sos"]) ? $_POST["company_telegram_sos"]:           "";
			$company_telegram_parkir   = isset($_POST["company_telegram_parkir"]) ? $_POST["company_telegram_parkir"]:     "";
			$company_telegram_speed    = isset($_POST["company_telegram_speed"]) ? $_POST["company_telegram_speed"]:       "";
			$company_telegram_geofence = isset($_POST["company_telegram_geofence"]) ? $_POST["company_telegram_geofence"]: "";

			if($company_name == "")
			{
					$callback["error"] = true;
					$callback["message"] = "Harap isi nama kontraktor";
					echo json_encode($callback);
					return;
			}

			$checkname = $this->dashboardmodel->checkbranchname($company_name);

			if (sizeof($checkname) > 0 ) {
				$callback["error"] = true;
				$callback["message"] = "Nama kontraktor sudah terdaftar";
				echo json_encode($callback);
				return;
			}else {
				$data = array(
					"company_name"            => $company_name,
					"company_agent"           => $company_agent,
					"company_user_parent"     => $this->sess->user_parent,
					"company_created_by"      => $company_created_by,
					"company_telegram_sos"    => $company_telegram_sos,
					"company_telegram_parkir" => $company_telegram_parkir,
					"company_telegram_speed"  => $company_telegram_speed
				);
				$insert = $this->dashboardmodel->insertData("company", $data);

				if ($insert) {
					$callback["error"]    = false;
					$callback["message"]  = "Berhasil menyimpan kontraktor";
					$callback["redirect"] = base_url()."account/branch";
					$contentlog           = "Master Data Contractor Successfuly Inserted";
					$insertlog            = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
					echo json_encode($callback);
					return;
				}else {
					$callback["error"]    = false;
					$callback["message"]  = "Gagal menyimpan kontraktor";
					$callback["redirect"] = base_url()."account/branch";
					$contentlog           = "Failed Update Master Data Contractor ";
					$insertlog            = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
					echo json_encode($callback);
					return;
				}
			}
			//
			// echo "<pre>";
			// var_dump($checkname);die();
			// echo "<pre>";
	}

	function editbranchoffice(){
		$id = $this->uri->segment(3);
		if (!$id)
		{
				return;
		}

		$this->db->where("company_id", $id);
		$this->db->limit(1);
		$q                   = $this->db->get("company");
		$row                 = $q->row();

		$this->dbtransporter = $this->load->database("transporter", true);

		$this->dbtransporter->select("*");
		$this->dbtransporter->from("transporter_branch");
		$this->dbtransporter->where("branch_company_id", $id);
		$this->dbtransporter->limit(1);
		$q_branch                    = $this->dbtransporter->get();
		$row_branch                  = $q_branch->row();

		$this->params["data"]        = $row;
		$this->params["data_branch"] = $row_branch;
		$this->params['code_view_menu']  = "configuration";

		// echo "<pre>";
		// var_dump($id);die();
		// echo "<pre>";

		// $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		// $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		// $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		// $this->params["content"]     = $this->load->view('dashboard/branch/v_branch_edit', $this->params, true);
		// $this->load->view("dashboard/template_dashboard", $this->params);

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/branch/v_branch_edit', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function updatebranchoffice(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

			$company_id                = $this->input->post("company_id");
			$company_name              = isset($_POST["company_name"]) ? $_POST["company_name"]: "";
			$company_name_old          = isset($_POST["company_name_old"]) ? $_POST["company_name_old"]: "";
			$company_telegram_sos      = isset($_POST["company_telegram_sos"]) ? $_POST["company_telegram_sos"]: "";
			$company_telegram_parkir   = isset($_POST["company_telegram_parkir"]) ? $_POST["company_telegram_parkir"]: "";
			$company_telegram_speed    = isset($_POST["company_telegram_speed"]) ? $_POST["company_telegram_speed"]: "";
			$company_telegram_geofence = isset($_POST["company_telegram_geofence"]) ? $_POST["company_telegram_geofence"]: "";

// $company_id.'-'.$company_name.'-'.$company_telegram_sos.'-'.$company_telegram_parkir.'-'.$company_telegram_speed.'-'.$company_telegram_geofence
			// echo "<pre>";
			// var_dump($company_name.'-'.$company_name_old);die();
			// echo "<pre>";

		 if($company_name == "")
			{
					$callback["error"]   = true;
					$callback["message"] = "Please Fill Company Name";
					echo json_encode($callback);
					return;
			}

			if ($company_name != $company_name_old) {
				$checkname = $this->dashboardmodel->checkbranchname($company_name);
					if (sizeof($checkname) > 0 ) {
						$callback["error"] = true;
						$callback["message"] = "Nama kontraktor sudah terdaftar";
						echo json_encode($callback);
						return;
					}
			}

				$data = array(
					"company_name"              => $company_name,
					"company_telegram_sos"      => $company_telegram_sos,
					"company_telegram_parkir"   => $company_telegram_parkir,
					"company_telegram_speed"    => $company_telegram_speed,
					"company_telegram_geofence" => $company_telegram_geofence
				);

				$update = $this->dashboardmodel->updateData("company", "company_id", $company_id, $data);

				if ($update) {
					$callback["error"]    = false;
					$callback["message"]  = "Perubahan data kontraktor berhasil disimpan ";
					$callback["redirect"] = base_url()."account/branch";
					echo json_encode($callback);
					$contentlog = "Master Data Contractor Successfuly Updated";
					$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
					return;
				}else {
					$callback["error"]    = false;
					$callback["message"]  = "Perubahan data kontraktor gagal disimpan";
					$callback["redirect"] = base_url()."account/branch";
					$contentlog = "Failed Update Master Data Contractor";
					$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
					echo json_encode($callback);
					return;
				}
	}

	function select_new_company(){
		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;

		$this->db->order_by("company_created", "desc");
			if ($privilegecode == 0) {
				$this->db->where("company_created_by", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("company_created_by", $user_parent);
			}
		$this->db->limit(1);
		$q = $this->db->get("company");
		$row = $q->row();
		return $row;
	}
  // BRANCH FUNCTION END

  // CUSTOMER FUNCTION START
	function customer()
	{
		// GET COMPANY\
		$getcustomer                      = $this->getcustomer();
		$getbranchoffice                  = $this->getcompany();

		// $this->params["data"]          = $rows;
		$this->params["datacustomer"]     = $getcustomer;
		$this->params["databranch"]       = $getbranchoffice;
		$this->params['code_view_menu']   = "configuration";

		// echo "<pre>";
		// var_dump($this->params["databranchoffice"]);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/customer/v_customer', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function customersave()
	{
		$usersite           = $this->sess->user_company;
		$id                 = isset($_POST['id']) ? $_POST['id']:                           0;
		$name               = isset($_POST['groupname']) ? trim($_POST['groupname']):       "";
		$parent             = isset($_POST['parent']) ? $_POST['parent']:                   0;
		$branchoffice       = isset($_POST['branchoffice']) ? $_POST['branchoffice']:       0;
		$subbranchoffice    = isset($_POST['subbranchoffice']) ? $_POST['subbranchoffice']: 0;
		$branchfix          = "";
		$subbranchofficefix = "";

		// FOR branchoffice
			if ($branchoffice == "empty") {
				$branchfix = 0;
			}else {
				$branchfix = $branchoffice;
			}

		// FOR branchoffice
			if ($subbranchoffice == "empty") {
				$subbranchofficefix = 0;
			}else {
				$subbranchofficefix = $subbranchoffice;
			}
		//echo "<pre>";
		//var_dump($id);die();
		//echo "<pre>";
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

		$data['group_parent']     = $parent;
		$data['group_company']    = $branchfix;
		$data['group_subcompany'] = $subbranchofficefix;
		$data['group_name']       = $name;
		// $data['group_company']    = $usersite;

		if ($id)
		{
			$mydb = $this->load->database("master", TRUE);

			$mydb->where("group_id", $id);
			$mydb->update("group", $data);

			$this->db->cache_delete_all();

			$callback['error'] = false;
			$callback['message'] = "Succesfully update customer";
			$callback['redirect'] = base_url()."account/customer";

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
		$callback['message'] = "Succesfully add customer";
		$callback['redirect'] = base_url()."account/customer";

		echo json_encode($callback);
		return;
	}

	function customerupdate(){
		$customerid         = isset($_POST['id']) ? $_POST['id']:0;
		$customername       = isset($_POST['groupname']) ? $_POST['groupname']:0;
		$curbranchoffice    = isset($_POST['curbranchoffice']) ? ($_POST['curbranchoffice']): 0;
		$cursubbranchoffice = isset($_POST['cursubbranchoffice']) ? $_POST['cursubbranchoffice']: 0;
		$nowbranchoffice    = isset($_POST['nowbranchoffice']) ? $_POST['nowbranchoffice']:       0;
		$nowsubbranchoffice = isset($_POST['nowsubbranchoffice']) ? $_POST['nowsubbranchoffice']: $cursubbranchoffice;
		$branchfix          = "";
		$subbranchofficefix = "";

		// JIKA BRANCH OFFICE LAMA & BARU SAMA
		if ($curbranchoffice == $nowbranchoffice) {
			$branchfix = $curbranchoffice;
		}else {
			$branchfix = $nowbranchoffice;
		}

		// JIKA BRANCH OFFICE LAMA & BARU SAMA
		if ($cursubbranchoffice == $nowsubbranchoffice) {
			$subbranchofficefix = $cursubbranchoffice;
		}else {
			$subbranchofficefix = $nowsubbranchoffice;
		}

		$data = array(
			"group_name"       => $customername,
			"group_company"    => $branchfix,
			"group_subcompany" => $subbranchofficefix
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$this->db->where("group_id", $customerid);
		$update = $this->db->update("group", $data);
			if ($update) {
				$callback['error'] = false;
				$callback['message'] = "Succesfully update customer";
				$callback['redirect'] = base_url()."account/customer";

				echo json_encode($callback);
				return;
			}else {
				$callback['error'] = false;
				$callback['message'] = "Failed update customer";
				$callback['redirect'] = base_url()."account/customer";

				echo json_encode($callback);
				return;
			}
	}

	function customeredit($id)
	{
		$datacurrentbranchoffice    = "";
		$datacurrentsubbranchoffice = "";
		// FOR GET THIS CUSTOMER
		$this->db->where("group_id", $id);
		$q                          = $this->db->get("group");
		$curcustomer                = $q->result_array();
		$group_company              = $curcustomer[0]['group_company'];
		$group_subcompany           = $curcustomer[0]['group_subcompany'];
		$getbranchoffice            = $this->getcompany();


		// GET BRANCH OFFICE BY ID
		if ($group_company == 0) {
			$datacurrentbranchoffice = 0;
		}else {
			$this->db->where("company_id", $group_company);
			$q                       = $this->db->get("company");
			$datacurrentbranchoffice = $q->result_array();
		}

		// GET SUB BRANCH OFFICE BY ID
		if ($group_subcompany == 0) {
			$datacurrentsubbranchoffice[0]['subcompany_name']	= "Not Set";
		}else {
			$this->db->where("subcompany_id", $group_subcompany);
			$q                          = $this->db->get("subcompany");
			$datacurrentsubbranchoffice = $q->result_array();
		}
		// $this->params['title']          = $this->lang->line('lgroup_edit');
		// $this->params['parentoptions']  = $options;
		$this->params['code_view_menu']     = "configuration";
		$this->params['curcustomer']        = $curcustomer;
		$this->params['curbranchoffice']    = $datacurrentbranchoffice;
		$this->params['cursubbranchoffice'] = $datacurrentsubbranchoffice;
		$this->params['allbranchoffice']    = $getbranchoffice;
		// echo "<pre>";
		// var_dump($this->params['cursubbranchoffice']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/customer/v_customer_edit', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
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

	function customerdelete($id)
	{
		$mydb = $this->load->database("master", TRUE);

		$update['group_status'] = 2;

		$mydb->where("group_id", $id);
		$mydb->update("group", $update);

		$this->db->cache_delete_all();

		redirect(base_url()."account/customer");
	}
  // CUSTOMER FUNCTION END

	// SUB COMPANY / SUB BRANCH START
	function subbranchoffice(){
		$rows_subbranch                 = $this->getsubbranch();
		$getcompany                     = $this->getcompany();

		$this->params["company"]        = $getcompany;
		$this->params["subcompany"]     = $rows_subbranch;
		$this->params['code_view_menu'] = "configuration";

    // echo "<pre>";
    // var_dump($this->params["company"]);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/branch/v_subbranch', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }

	function savesubbranchoffice(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

			$subcompany_name    = isset($_POST["subcompany_name"]) ? $_POST["subcompany_name"]:     "";
			$subcompany_created = date("Y-m-d H:i:s");
			$subcompany_creator = $this->sess->user_id;
			$subcompany_parent  = isset($_POST["subcompany_parent"]) ? $_POST["subcompany_parent"]: "";
			$subcompany_status  = 1;

			if($subcompany_name == "")
			{
				$callback["error"] = true;
				$callback["message"] = "Please Fill Company Name";
				echo json_encode($callback);
				return;
			}

			unset($data);
			$data["subcompany_name"]    = $subcompany_name;
			$data["subcompany_created"] = $subcompany_created;
			$data["subcompany_creator"] = $subcompany_creator;
			$data["subcompany_parent"]  = $subcompany_parent;
			$data["subcompany_status"]  = $subcompany_status;

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$this->db->insert("subcompany", $data);

			$callback["error"] = false;
			$callback["message"] = "Success Add Sub Branch Office";
			$callback["redirect"] = base_url()."account/subbranchoffice";
			echo json_encode($callback);
			return;
	}

	function editsubbranchoffice(){
		$id = $this->uri->segment(3);
		if (!$id)
		{
				return;
		}

		$this->db->where("subcompany_id", $id);
		$this->db->limit(1);
		$q                               = $this->db->get("subcompany");
		$row                             = $q->result_array();

		$this->db->select("*");
		$this->db->where("company_created_by", $row[0]['subcompany_creator']);
		$q_branch                        = $this->db->get("company");
		$company                         = $q_branch->result_array();

		$this->params["data_subcompany"] = $row;
		$this->params["data_company"]    = $company;
		$this->params['code_view_menu'] = "configuration";

		// echo "<pre>";
		// var_dump($this->params["data_company"]);die();
		// echo "<pre>";

		// $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		// $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		// $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		// $this->params["content"]     = $this->load->view('dashboard/branch/v_subbranchedit', $this->params, true);
		// $this->load->view("dashboard/template_dashboard_report", $this->params);

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/branch/v_subbranchedit', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function updatesubbranchoffice(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$subcompany_id     = isset($_POST["subcompany_id"]) ? $_POST["subcompany_id"]:         "";
		$subcompany_name   = isset($_POST["subcompany_name"]) ? $_POST["subcompany_name"]:     "";
		$curbranchoffice   = isset($_POST["curbranchoffice"]) ? $_POST["curbranchoffice"]:     "";
		$subcompany_parent = isset($_POST["subcompany_parent"]) ? $_POST["subcompany_parent"]: "";
		$subcompany_status = 1;
		$branchofficefix = "";

		if ($curbranchoffice == $subcompany_parent) {
			$branchofficefix = $curbranchoffice;
		}else {
			$branchofficefix = $subcompany_parent;
		}

		if($subcompany_name == "")
		{
			$callback["error"] = true;
			$callback["message"] = "Please Fill Company Name";
			echo json_encode($callback);
			return;
		}

		unset($data);
		$data["subcompany_name"]   = $subcompany_name;
		$data["subcompany_parent"] = $branchofficefix;
		$data["subcompany_status"] = $subcompany_status;

		$this->db = $this->load->database("default",true);
		$this->db->where("subcompany_id", $subcompany_id);
		$this->db->update("subcompany", $data);

		$callback["error"] = false;
		$callback["message"] = "Success Update Data";
		$callback["redirect"] = base_url()."account/subbranchoffice";
		echo json_encode($callback);
		return;
	}

	function getsubbranch(){
		$userlevel     = $this->sess->user_level;
		$user_id       = $this->sess->user_id;
		$user_company  = $this->sess->user_company;
		$user_group    = $this->sess->user_group;
		$user_parent   = $this->sess->user_parent;
		$user_id_role  = $this->sess->user_id_role;
		$user_subgroup = $this->sess->user_subgroup;

		// FOR WHERE
		$this->db       = $this->load->database('default', true);
		if ($user_id_role == 1) {
			$this->db->where("subcompany_creator", $user_id);
		}elseif ($user_id_role == 3) {
			$this->db->where("subcompany_creator", $user_parent);
		}elseif ($user_id_role == 4) {
			$this->db->where("subcompany_creator", $user_parent);
		}else {
			$this->db->where("subcompany_parent", $user_company);
		}
		$this->db->order_by("subcompany_name", "asc");
    $qsubcompany    = $this->db->get("subcompany");
    $row_subcompany = $qsubcompany->result();
    return $row_subcompany;
  }

	function getcompany(){
		$userlevel     = $this->sess->user_level;
		$user_id       = $this->sess->user_id;
		$user_company  = $this->sess->user_company;
		$user_group    = $this->sess->user_group;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_subgroup = $this->sess->user_subgroup;

		// FOR WHERE
		$this->db       = $this->load->database('default', true);
		if ($privilegecode == 1) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($privilegecode == 0) {
			$this->db->where("company_created_by", $user_id);
		}elseif ($privilegecode == 3) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($privilegecode == 4) {
			$this->db->where("company_created_by", $user_parent);
		}else {
			$this->db->where("company_created_by", $user_subgroup);
		}

		$this->db->where("company_flag", 0);
		$q             = $this->db->get("company");
		$rows          = $q->result();
    return $rows;
  }
	// SUB COMPANY / SUB BRANCH END

	// SUB CUSTOMER
	function subcustomer(){
    $row_subcustomer                = $this->getsubcustomer();
		$getcustomer                    = $this->getcustomer();
		$getcompany                     = $this->getcompany();
		$getsubbranch                   = $this->getsubbranch();

		$this->params["subcustomer"]    = $row_subcustomer;
		$this->params["customer"]       = $getcustomer;
		$this->params["company"]        = $getcompany;
		$this->params["subcompany"]     = $getsubbranch;
		$this->params['code_view_menu'] = "configuration";

    // echo "<pre>";
    // var_dump($this->params["customer"]);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/customer/v_subcustomer', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }

	function savesubcustomer(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$branchoffice       = isset($_POST['company']) ? trim($_POST['company']):                 "";
		$subbranchoffice    = isset($_POST['subcompany']) ? trim($_POST['subcompany']):           "";
		$customer           = isset($_POST['customer']) ? trim($_POST['customer']):               "";
		$subcustomername    = isset($_POST['subcustomername']) ? trim($_POST['subcustomername']): "";
		$branchfix          = "";
		$subbranchofficefix = "";

		// FOR branchoffice
			if ($branchoffice == "empty") {
				$branchfix = 0;
			}else {
				$branchfix = $branchoffice;
			}

		// FOR branchoffice
			if ($subbranchoffice == "empty") {
				$subbranchofficefix = 0;
			}else {
				$subbranchofficefix = $subbranchoffice;
			}

			$data = array(
				"subgroup_name"       => $subcustomername,
				"subgroup_created"    => date("Y-m-d H:i:s"),
				"subgroup_creator"    => $this->sess->user_id,
				"subgroup_company"    => $branchfix,
				"subgroup_subcompany" => $subbranchofficefix,
				"subgroup_customer"   => $customer
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$insert = $this->db->insert("subgroup", $data);
				if ($insert) {
					$callback["error"] = false;
					$callback["message"] = "Success Add Sub Customer";
					$callback["redirect"] = base_url()."account/subcustomer";
					echo json_encode($callback);
					return;
				}else {
					$callback["error"] = false;
					$callback["message"] = "Failed Add Sub Customer";
					$callback["redirect"] = base_url()."account/subcustomer";
					echo json_encode($callback);
					return;
				}
	}

	function editsubcustomer(){
		$id = $this->uri->segment(3);
		if (!$id)
		{
				return;
		}

		$datacurrentbranchoffice    = "";
		$datacurrentsubbranchoffice = "";
		$datacurrentcustomer			  = "";
		// FOR GET THIS CUSTOMER
		$this->db->where("subgroup_id", $id);
		$q                          = $this->db->get("subgroup");
		$cursubcustomer             = $q->result_array();
		$group_company              = $cursubcustomer[0]['subgroup_company'];
		$group_subcompany           = $cursubcustomer[0]['subgroup_subcompany'];
		$group_customer             = $cursubcustomer[0]['subgroup_customer'];
		$getbranchoffice            = $this->getcompany();

		// GET BRANCH OFFICE BY ID
		if ($group_company == 0) {
			$datacurrentbranchoffice = 0;
		}else {
			$this->db->where("company_id", $group_company);
			$q                       = $this->db->get("company");
			$datacurrentbranchoffice = $q->result_array();
		}

		// GET SUB BRANCH OFFICE BY ID
		if ($group_subcompany == 0) {
			$datacurrentsubbranchoffice[0]['subcompany_name']	= "Not Set";
		}else {
			$this->db->where("subcompany_id", $group_subcompany);
			$q                          = $this->db->get("subcompany");
			$datacurrentsubbranchoffice = $q->result_array();
		}

		// GET CUSTOMER BY ID
		if ($group_customer == 0) {
			$datacurrentcustomer[0]['group_name']	= "Not Set";
		}else {
			$this->db->where("group_id", $group_customer);
			$q                          = $this->db->get("group");
			$datacurrentcustomer				= $q->result_array();
		}

		// GET DATA FOR LOOP IN VIEW
		$getcompany                           = $this->getcompany();
		$getsubbranch                         = $this->getsubbranch();
		$getcustomer                          = $this->getcustomer();

		$this->params["branchoffice"]          = $getcompany;

		$this->params["datasubcustomer"]      = $cursubcustomer;
		$this->params["data_branchoffice"]    = $datacurrentbranchoffice;
		$this->params["data_subbranchoffice"] = $datacurrentsubbranchoffice;
		$this->params["data_customer"]        = $datacurrentcustomer;
		$this->params['code_view_menu']       = "configuration";

		// echo "<pre>";
		// var_dump($this->params["customer"]);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/customer/v_subcustomer_edit', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function updatesubcustomer(){
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}

		$subcustomer_id     = isset($_POST["subcustomer_id"]) ? $_POST["subcustomer_id"]:         "";

		$curbranchoffice    = isset($_POST["curbranchoffice"]) ? $_POST["curbranchoffice"]:       "";
		$cursubbranchoffice = isset($_POST["cursubbranchoffice"]) ? $_POST["cursubbranchoffice"]: "";
		$curcustomer        = isset($_POST["curcustomer"]) ? $_POST["curcustomer"]:               "";

		$nowbranchoffice    = isset($_POST["company"]) ? $_POST["company"]:                       "";
		$nowsubbranchoffice = isset($_POST["subcompany"]) ? $_POST["subcompany"]:                 "";
		$nowcustomer        = isset($_POST["customer"]) ? $_POST["customer"]:                     "";

		$subcustomername    = isset($_POST["subcustomername"]) ? $_POST["subcustomername"]:       "";

		$branchfix          = "";
		$subbranchfix       = "";
		$customerfix        = "";

		// JIKA BRANCH OFFICE LAMA & BARU SAMA
		if ($curbranchoffice == $nowbranchoffice) {
			$branchfix = $curbranchoffice;
		}else {
			$branchfix = $nowbranchoffice;
		}

		// JIKA BRANCH OFFICE LAMA & BARU SAMA
		if ($cursubbranchoffice == $nowsubbranchoffice) {
			$subbranchfix = $cursubbranchoffice;
		}else {
			$subbranchfix = $nowsubbranchoffice;
		}

		// JIKA CUSTOMER LAMA & BARU SAMA
		if ($curcustomer == $nowcustomer) {
			$customerfix = $curcustomer;
		}else {
			$customerfix = $nowcustomer;
		}

		$data = array(
			"subgroup_name"       => $subcustomername,
			"subgroup_company"    => $branchfix,
			"subgroup_subcompany" => $subbranchfix,
			"subgroup_customer"   => $customerfix,
			"subgroup_status" 	  => 1
		);

		$this->db->where("subgroup_id", $subcustomer_id);
		$update = $this->db->update("subgroup", $data);
			if ($update) {
				$callback['error'] = false;
				$callback['message'] = "Succesfully update subcustomer";
				$callback['redirect'] = base_url()."account/subcustomer";

				echo json_encode($callback);
				return;
			}else {
				$callback['error'] = false;
				$callback['message'] = "Failed update subcustomer";
				$callback['redirect'] = base_url()."account/subcustomer";

				echo json_encode($callback);
				return;
			}
	}

	function getsubcustomer(){
		$user_id      = $this->sess->user_id;
		$user_parent  = $this->sess->user_parent;
		$user_id_role = $this->sess->user_id_role;

    $this->db       = $this->load->database('default', true);
			if ($user_id_role == 1) {
				$this->db->where("subgroup_creator", $user_parent);
			}elseif ($user_id_role == 3) {
				$this->db->where("subgroup_creator", $user_parent);
			}elseif ($user_id_role == 4) {
				$this->db->where("subgroup_creator", $user_parent);
			}else {
				$this->db->where("subgroup_creator", $user_id);
			}
		$this->db->order_by("subgroup_name", "asc");
    $qsubcompany    = $this->db->get("subgroup");
    $row_subcompany = $qsubcompany->result();
    return $row_subcompany;
  }

	function getcustomer(){
		$userlevel     = $this->sess->user_level;
		$user_id       = $this->sess->user_id;
		$user_company  = $this->sess->user_company;
		$user_group    = $this->sess->user_group;
		$user_parent   = $this->sess->user_parent;
		$user_id_role  = $this->sess->user_id_role;
		$user_subgroup = $this->sess->user_subgroup;

		// FOR WHERE
		$this->db       = $this->load->database('default', true);
		if ($user_id_role == 1) {
			$this->db->where("group_creator", $user_id);
		}elseif ($user_id_role == 3) {
			$this->db->where("group_creator", $user_parent);
		}elseif ($user_id_role == 4) {
			$this->db->where("group_creator", $user_parent);
		}else {
			$this->db->where("group_subcompany", $user_group);
		}
		$this->db->order_by("group_name", "asc");
		$qcustomer    = $this->db->get("group");
		$row_customer = $qcustomer->result_array();
		return $row_customer;
  }
	// SUB CUSTOMER END

	// FOR SEARCHING DATA START
	function getsubcompanybyid(){
		$companyid = $_POST['id'];
		$userid = $this->sess->user_id;
		$this->db  = $this->load->database('default', true);
			if ($companyid == "empty") {
				$this->db->where("subcompany_creator", $userid);
			}else {
				$this->db->where("subcompany_parent", $companyid);
			}
		$this->db->where("subcompany_flag", 0);
		$this->db->order_by("subcompany_name", "asc");
		$q         = $this->db->get("subcompany");
		$rows      = $q->result();
			// echo "<pre>";
			// var_dump($rows);die();
			// echo "<pre>";
		echo json_encode(array("data" => $rows));

	}

	function getcustomerbysubcompanyid(){
		$subcompany = $_POST['id'];
		$userid     = $this->sess->user_id;
		$this->db  = $this->load->database('default', true);
			if ($subcompany == "empty") {
				$this->db->where("group_creator", $userid);
			}else {
				$this->db->where("group_subcompany", $subcompany);
			}
		$this->db->where("group_flag", 0);
		$this->db->order_by("group_name", "asc");
		$q         = $this->db->get("group");
		$rows      = $q->result();
		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";
		echo json_encode(array("data" => $rows));
	}

	function getsubcustomerbysubcompanyid(){
		$customerid = $_POST['id'];
		$userid     = $this->sess->user_id;
		$this->db   = $this->load->database('default', true);
			if ($customerid == "empty") {
				$this->db->where("subgroup_creator", $userid);
			}else {
				$this->db->where("subgroup_customer", $customerid);
			}
		$this->db->where("subgroup_flag", 0);
		$this->db->order_by("subgroup_name", "asc");
		$q         = $this->db->get("subgroup");
		$rows      = $q->result();
		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";
		echo json_encode(array("data" => $rows));
	}
	// FOR SEARCHING DATA END

	// KHUSUS CUSTOMER OPTIONS
	function options($id=0)
	{
		$getbranchoffice        = $this->getcompany();
		$params['term']         = 1;
		$params['branchoffice'] = $getbranchoffice;
		// echo "<pre>";
		// var_dump($idnya);die();
		// echo "<pre>";
		$html                   = $this->load->view('dashboard/customer/v_options', $params, true);
		// $html                = $this->load->view("group/options", $params, true);
		$callback['empty']      = false;
		$callback['html']       = $html;
		echo json_encode($callback);
	}

	function getdetailcompany(){
		$company_id = $_POST['company_id'];

		$this->db       = $this->load->database('default', true);
		$this->db->where("company_id", $company_id);
		$q             = $this->db->get("company");
		$rows          = $q->result();
		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$callback['data']       = $rows;
		echo json_encode($callback);
	}

	function checkthisemail($table, $email){
		$this->db       = $this->load->database('default', true);
		$this->db->where("user_mail", $email);
		$this->db->where("user_status", 1);
		$q    = $this->db->get($table)->result_array();
		return $q;
	}


}