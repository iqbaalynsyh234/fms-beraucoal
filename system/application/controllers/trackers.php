<?php
include "base.php";

class Trackers extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Trackers()
	{
		parent::Base();

		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("m_projectschedule");
	}

	function index()
	{

		$field           = isset($_POST['field']) ? $_POST['field']:                       "";
		$keyword         = isset($_POST['keyword']) ? $_POST['keyword']:                   "";
		$vtype           = isset($_POST['vehicle_type']) ? $_POST['vehicle_type']:         "";
		$sortby          = isset($_POST['sortby']) ? $_POST['sortby']:                     "user_name";
		$orderby         = isset($_POST['orderby']) ? $_POST['orderby']:                   "asc";
		$companyid       = isset($_POST['company']) ? $_POST['company']:                   0;
		$groupid         = isset($_POST['group']) ? $_POST['group']:                       0;
		$server          = isset($_POST['server']) ? $_POST['server']:                     "";
		$membership      = isset($_POST['membership']) ? $_POST['membership']:             "";
		$branch          = isset($_POST['branch_office']) ? $_POST['branch_office']:       "";
		$booking_loading = isset($_POST['booking_loading']) ? $_POST['booking_loading']:   0;
		$destination     = isset($_POST['destination']) ? $_POST['destination']:           "";
		$gpsstatus       = isset($_POST['gpsstatus_select']) ? $_POST['gpsstatus_select']: 0;
		$garasinya       = isset($_POST['listgarasinya']) ? $_POST['listgarasinya']: 0;


		// echo "<pre>";
		// var_dump($this->sess);die();
		// echo "<pre>";

		if(! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		// ->user_app.'-'.$this->sess->user_level.'-'.$this->sess->user_dblive

		// echo "<pre>";
		// var_dump($this->sess->user_id_role);die();
		// echo "<pre>";


		// REDIRECT JIKA TERDAFTAR SEBAGAI USER NEW TRANSPORTER
		if ($this->sess->user_app == 2020 && $this->sess->user_level != 0 && $this->sess->user_dblive != "0") {
			// redirect(base_url()."maps/heatmap");
			if ($this->sess->user_id_role == 0) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 1) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 2) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 3) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 4) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 5 && $this->sess->user_excavator == 0) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 5 && $this->sess->user_excavator == 1) {
				redirect(base_url()."exca");
			}elseif ($this->sess->user_id_role == 6 && $this->sess->user_excavator == 0) {
				redirect(base_url()."maps/heatmap");
			}elseif ($this->sess->user_id_role == 6 && $this->sess->user_excavator == 1) {
				redirect(base_url()."exca");
			}elseif ($this->sess->user_id_role == 7) {
				redirect(base_url()."wim");
			}elseif ($this->sess->user_id_role == 8) {
				redirect(base_url()."wim");
			}elseif ($this->sess->user_id_role == 9) {
				redirect(base_url()."driver");
			}elseif ($this->sess->user_id_role == 10) {
				redirect(base_url()."exca");
			}elseif ($this->sess->user_id_role == 11) {
				redirect(base_url()."wim/otherport");
			}else {
				redirect(base_url()."maps/heatmap");
			}
		}

		// REDIRECT LANGSUNG KE PAGE TMS
		if ($this->sess->user_id == "4098") {
			redirect(base_url()."tms/");
		}

		if ($field == "location")
		{
			$bylocation = $this->vehicle_location($field, $keyword, $sortby, $orderby);
		}

		if ($field == "id_booking")
		{
			$bybooking = $this->get_id_booking($keyword);
		}

		if ($field == "destination")
        {
            $bydestination = $this->by_destination($keyword);
        }

		if ($field == "noso")
		{
			$bynoso = $this->get_noso($keyword);
		}

		if ($field == "nodr")
		{
			$bynodr = $this->get_nodr($keyword);
		}

		if ($field == "booking_loading")
		{
			$bybooking_loading = $this->get_booking_loading($booking_loading);
		}





		if (substr($field, 0, strlen("delayed")) == "delayed")
		{
			$delayed = substr($field, strlen("delayed"));
			$delayeds = explode("_", $delayed);
			$field = "delayed";

			$vdelayeds = $this->getVehiclesDelayed($delayeds[0]*60, $delayeds[1]*60);
		}

		if ($field == "user_company")
		{
			if ($groupid)
			{
				$groups[] = $groupid;
				$groupids = $this->vehiclemodel->getChildIds($groupid, $groups);
			}
		}

		if (($this->sess->user_type != 2) || (($this->sess->user_type == 2) && ($this->sess->user_company > 0) && ($this->sess->user_group == 0)))
		{
			$this->db->order_by("company_name", "asc");
			if ($this->sess->user_type == 3)
			{
				$this->db->where("company_agent", $this->sess->user_agent);
			}

			$q = $this->db->get("company");
			$this->params['companies'] = $q->result();
		}

		$this->db->order_by("vehicle_isred", "asc");
		$this->db->order_by("vehicle_no", "asc");

		switch($this->sess->user_type)
		{
			case 2:
				if (isset($this->vehicleids))
				{
					if ($field != "vehicletrans" && $field != "id_booking" && $field != "booking_loading" && $field != "noso" && $field != "nodr")
					{
						$this->db->where_in("vehicle_id", $this->vehicleids);
					}

					if ($this->config->item("app_tupperware"))
					{
						if ($keyword == "" || $field == "vehicletrans")
						{
							$this->db->or_where("vehicle_group",422);
						}
					}
				}
				else
				{
					if ($field != "vehicletrans" && $field != "id_booking" && $field != "booking_loading" && $field != "noso" && $field != "nodr")
					{
						$this->db->where("vehicle_user_id", $this->sess->user_id);
					}

					if ($this->config->item("app_tupperware"))
					{
						if ($keyword == "" || $field == "vehicletrans")
						{
							$this->db->or_where("vehicle_group",422);
						}
					}
				}
			break;
			case 3:
				$this->db->where("user_agent", $this->sess->user_agent);
			break;
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->order_by("vehicle_user_id", "desc");
		}

		$this->db->order_by("vehicle_type", "asc");
		$this->db->order_by("vehicle_company", "asc");


		if ($sortby == "user_name")
		{
			$this->db->order_by($sortby, $orderby);
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
		}
		else
		if ($sortby == "vehicle_name")
		{
			$this->db->order_by("vehicle_name", $orderby);
			$this->db->order_by("vehicle_no", $orderby);
			$this->db->order_by("user_name", $orderby);
		}
		else
		{
			$this->db->order_by($sortby, $orderby);
		}


		if ($this->sess->user_type == 2)
		{
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}

		$ssi_app = $this->config->item("ssi_app");
		if (isset($ssi_app) && ($ssi_app == 1))
		{
			if($this->sess->user_type == 5 && $this->sess->user_id == 2288){
				//1933 = user PT SSI
				// 2288 = user all area monitoring ssi
				$this->db->where("vehicle_user_id", 1933);
			}
		}

		switch ($field)
		{
			case "user_company":
				if ($groupid)
				{
					$this->db->where_in("vehicle_group", $groups);
				}
				else
				{
					$this->db->where("vehicle_company", $companyid);
				}
			break;
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
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
			case "vehicletrans":
				$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
			break;
			case "device":
				$this->db->where("vehicle_device like", '%'.$keyword.'%');
			break;
			case "delayed":
				$this->db->where_in("vehicle_id", $vdelayeds);
			break;
			case "server":
				$this->db->where("vehicle_info LIKE '%\"vehicle_ip\":\"".$server."\"%'", null);
			break;
			case "location":
				$this->db->where_in("vehicle_device", $bylocation);
			break;
			case "branch":
				$this->db->where("vehicle_company", $branch);
			break;
			case "gpsstatus":
				$this->db->where("vehicle_isred", $gpsstatus);
			break;
			case "destination":
                $this->db->where_in("vehicle_id", $bydestination);
            break;
			case "membership":
				$this->db->where("user_agent", "1");
				if ($membership == 1)
				{
					$this->db->where("user_payment_type", $membership);
					$this->db->where("user_payment_period <", "12");
				}
				else
				{
					$this->db->where("user_payment_period >=", "12");
				}

			break;
			case "vehicle_type":
				$vtypes[] = $vtype;

				$vreplaces = $this->config->item('vehicle_type_replace');
				if (isset($vreplaces[$vtype]))
				{
					//$vtypes[] = $vreplaces[$vtype];
				}

				if ($vtype == 'T1')
				{
					//$vtypes[] = "";
				}

				$this->db->where_in("vehicle_type", $vtypes);
			break;
			case "id_booking":
				if ($bybooking == 0)
				{
					$this->db->where("vehicle_device","xxxxxxxxxxxxxxxxxx");
				}
				else
				{
					$this->db->where_in("vehicle_device",$bybooking);
				}
			break;
			case "booking_loading":
				if ($bybooking_loading == 0)
				{
					$this->db->where("vehicle_device","xxxxxxxxxxxxxxxxxx");
				}
				else
				{
					$this->db->where_in("vehicle_device",$bybooking_loading);
				}
			break;
			case "noso":
				if ($bynoso == 0)
				{
					$this->db->where("vehicle_device","xxxxxxxxxxxxxxxxxx");
				}
				else
				{
					$this->db->where_in("vehicle_device",$bynoso);
				}
			break;
			case "nodr":
				if ($bynodr == 0)
				{
					$this->db->where("vehicle_device","xxxxxxxxxxxxxxxxxx");
				}
				else
				{
					$this->db->where_in("vehicle_device",$bynodr);
				}
			break;
			default:
				if ($this->sess->user_type == 1)
				{
					if (! isset($_POST['btnsearch']))
					{
						$this->db->where("1 = 0", null, false);
					}
				}

				if ($field)
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				$app_tupperware = $this->config->item("app_tupperware");
				if (isset($app_tupperware) && ($app_tupperware == 1))
				{
					//422 = Group Tupperware
					$this->db->or_where("vehicle_group",422);
				}
		}



		$this->db->select("user_id,user_name,vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_card_no,vehicle_type,vehicle_active_date2");
		$this->db->join("user", "vehicle_user_id = user_id");

		if ($field == "user_agent")
		{
			$this->db->join("agent", "user_agent = agent_id");
		}

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$allowed_vtype = $this->config->item('allowed_vtype');
		if ($allowed_vtype && is_array($allowed_vtype) && count($allowed_vtype))
		{
			$this->db->where_in("vehicle_type", $allowed_vtype);
		}

		$site = $this->config->item('site');
		if ($site)
		{
			$this->db->where("vehicle_site", $site);
		}



		$this->db->where("vehicle_status <>", 3);

		$q = $this->db->get("vehicle");

		$rows = $q->result();
		//print_r($rows);exit;

		for($i=0; $i < count($rows); $i++)
		{
			if (isset($vehicles[$rows[$i]->vehicle_device]))
			{
				if ($rows[$i]->vehicle_id < $vehicles[$rows[$i]->vehicle_device])
				{
					$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
					continue;
				}
			}
			$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
		}

		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);

			$rows[$i]->vehicle_id = $vehicles[$rows[$i]->vehicle_device];
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
		}

		if ($field)
		{
			$this->params['title'] = $this->lang->line('lvehicle_tracker')." (".$field."=".$keyword.") ";
		}
		else
		{
			$this->params['title'] = $this->lang->line('lvehicle_tracker');
		}

		//Get Group
		if (isset($this->sess->user_company)&&($this->sess->user_group == 0))
		{
			$branch_off = $this->sess->user_id;
			$this->db->where("company_created_by",$branch_off);
			$q_comp = $this->db->get("company");
			$row_comp = $q_comp->result();
			$this->params['branch'] = $row_comp;

		}

		//dosj app
		$app_dosj_all = $this->config->item("app_dosj_all");
		if (isset($app_dosj_all) && ($app_dosj_all == 1))
		{
			$this->params['app_dosj_all'] = $app_dosj_all;
		}

		$ssi_app = $this->config->item("ssi_app");
		if (isset($ssi_app) && ($ssi_app == 1))
		{
			$this->db->where("vehicle_isred",1);
			$this->db->where("vehicle_status <>", 3);
			if($this->sess->user_id == "1933")
			{
				$this->db->where("vehicle_user_id",1933);
				if($branch != 0)
				{
					$this->db->where("vehicle_group",$branch);
				}
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
				if($branch != 0)
				{
					$this->db->where("vehicle_group",$branch);
				}
				else
				{
					$this->db->where("vehicle_group",$this->sess->user_group);
				}
			}
			$q = $this->db->get("vehicle");
			$isred = $q->result();
			$totalmerah = count($isred);
			$this->params['totalmerah'] = $totalmerah;
		}

		if($branch != 0)
		{
			$this->params['isbranch'] = $branch;
		}
		$total_vehicle = count($rows);

		//start autocheck //

		//get vehicle master autocheck
		$view_autocheck = 0;
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->where("vehicle_status <>", 3);
		$q_autocheck = $this->db->get("vehicle");
		$rows_autocheck = $q_autocheck->result();
		$total_vehicle_autocheck = count($rows_autocheck);

		/*if($this->sess->user_company > 0 && $this->sess->user_group == 0){
			$this->db->where("auto_vehicle_company",$this->sess->user_company);
		}
		else if($this->sess->user_company > 0 && $this->sess->user_group > 0){
			$this->db->where("auto_vehicle_group",$this->sess->user_group);
		}
		else{
			$this->db->where("auto_user_id",$this->sess->user_id);
		}*/

		//get from table autocheck
		$this->db->order_by("auto_vehicle_no","asc");
		if($this->sess->user_id == "1445"){
			$this->db->where("auto_user_id",631);
		}
		else
		{
			$this->db->where("auto_user_id",$this->sess->user_id);
		}

		$this->db->where("auto_flag",0);
		$q_auto = $this->db->get("vehicle_autocheck");
		$row_auto = $q_auto->result();
		$total_auto = count($row_auto);
		if($total_auto > 0){
			$view_autocheck = 1;
			$total_k = 0;
			$total_m = 0;
			$total_p = 0;

			for($i=0;$i<count($row_auto);$i++){
				if($row_auto[$i]->auto_status == "K"){
					$total_k = $total_k + 1;
				}
				if($row_auto[$i]->auto_status == "M"){
					$total_m = $total_m + 1;
				}
				if($row_auto[$i]->auto_status == "P"){
					$total_p = $total_p + 1;
				}
				$last_checked = date("d-m-Y H:i", strtotime($row_auto[$i]->auto_last_check));

			}
				/*$this->db->select("auto_last_check");
				$this->db->order_by("auto_last_check","desc");
				$this->db->where("auto_user_id",$rows[$i]->user_id);
				$this->db->where("auto_flag",0);
				$this->db->limit(1);
				$q_last_check = $this->db->get("vehicle_autocheck");
				$row_last_check = $q_last_check->row();
				if(count($row_last_check)>0){
					$last_checked = date("d-m-Y H:i", strtotime($row_last_check->auto_last_check));
				}*/

			$this->params['last_checked'] = $last_checked;
			$this->params['total_auto'] = $total_auto;
			$this->params['total_k'] = $total_k;
			$this->params['total_m'] = $total_m;
			$this->params['total_p'] = $total_p;
			$this->params['total_vehicle'] = $total_vehicle;
			$this->params['total_vehicle_autocheck'] = $total_vehicle_autocheck;

		}
		//end auto check //
		if (in_array(strtoupper($this->sess->user_id), $this->config->item("user_view_pto"))) {
			if ($this->sess->user_id == "389") {
				$this->params['dataproject'] = $this->m_projectschedule->getprojectdilist("project_schedule", "project_user_id", $this->sess->user_id);
			}else {
				$this->params['dataproject'] = $this->m_projectschedule->getprojectdilist("project_schedule", "project_vehicle_company", $this->sess->user_company);
			}
		}

		// echo "<pre>";
		// var_dump($this->params['dataproject']);die();
		// echo "<pre>";

		$this->params['view_autocheck'] = $view_autocheck;
		$this->params['sortby']         = $sortby;
		$this->params['orderby']        = $orderby;
		$this->params["data"]           = $rows;
		$this->params["data_autocheck"] = $rows_autocheck;
		$this->params["initmap"]        = $this->load->view('initmap', $this->params, true);
		$this->params["updateinfo"]     = $this->load->view('updateinfo', $this->params, true);
		$this->params["content"]        = $this->load->view('trackers/list', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function smartview()
	{

		$field      = isset($_POST['field']) ? $_POST['field']:                 "";
		$keyword    = isset($_POST['keyword']) ? $_POST['keyword']:             "";
		$vtype      = isset($_POST['vehicle_type']) ? $_POST['vehicle_type']:   "";
		$sortby     = isset($_POST['sortby']) ? $_POST['sortby']:               "user_name";
		$orderby    = isset($_POST['orderby']) ? $_POST['orderby']:             "asc";
		$companyid  = isset($_POST['company']) ? $_POST['company']:             0;
		$groupid    = isset($_POST['group']) ? $_POST['group']:                 0;
		$server     = isset($_POST['server']) ? $_POST['server']:               "";
		$membership = isset($_POST['membership']) ? $_POST['membership']:       "";
		$branch     = isset($_POST['branch_office']) ? $_POST['branch_office']: "";

		if(! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		if ($field == "location")
		{
			$bylocation = $this->vehicle_location($field, $keyword, $sortby, $orderby);
		}

		if ($field == "id_booking")
		{
			$bybooking = $this->get_id_booking($keyword);
		}

		if (substr($field, 0, strlen("delayed")) == "delayed")
		{
			$delayed = substr($field, strlen("delayed"));
			$delayeds = explode("_", $delayed);
			$field = "delayed";

			$vdelayeds = $this->getVehiclesDelayed($delayeds[0]*60, $delayeds[1]*60);
		}

		if ($field == "user_company")
		{
			if ($groupid)
			{
				$groups[] = $groupid;
				$groupids = $this->vehiclemodel->getChildIds($groupid, $groups);
			}
		}

		if (($this->sess->user_type != 2) || (($this->sess->user_type == 2) && ($this->sess->user_company > 0) && ($this->sess->user_group == 0)))
		{
			$this->db->order_by("company_name", "asc");
			if ($this->sess->user_type == 3)
			{
				$this->db->where("company_agent", $this->sess->user_agent);
			}

			$q = $this->db->get("company");
			$this->params['companies'] = $q->result();
		}


		switch($this->sess->user_type)
		{
			case 2:
				if (isset($this->vehicleids))
				{
					if ($field != "vehicletrans" && $field != "id_booking")
					{
						$this->db->where_in("vehicle_id", $this->vehicleids);
					}

					if ($this->config->item("app_tupperware"))
					{
						if ($keyword == "" || $field == "vehicletrans")
						{
							$this->db->or_where("vehicle_group",422);
						}
					}
				}
				else
				{
					if ($field != "vehicletrans" && $field != "id_booking")
					{
						$this->db->where("vehicle_user_id", $this->sess->user_id);
					}

					if ($this->config->item("app_tupperware"))
					{
						if ($keyword == "" || $field == "vehicletrans")
						{
							$this->db->or_where("vehicle_group",422);
						}
					}
				}
			break;
			case 3:
				$this->db->where("user_agent", $this->sess->user_agent);
			break;
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->order_by("vehicle_user_id", "desc");
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->order_by("vehicle_company", "asc");


		if ($sortby == "user_name")
		{
			$this->db->order_by($sortby, $orderby);
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
		}
		else
		if ($sortby == "vehicle_name")
		{
			$this->db->order_by("vehicle_name", $orderby);
			$this->db->order_by("vehicle_no", $orderby);
			$this->db->order_by("user_name", $orderby);
		}
		else
		{
			$this->db->order_by($sortby, $orderby);
		}


		if ($this->sess->user_type == 2)
		{
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}

		$ssi_app = $this->config->item("ssi_app");
		if (isset($ssi_app) && ($ssi_app == 1))
		{
			if($this->sess->user_type == 5 && $this->sess->user_id == 2288){
				//1933 = user PT SSI
				// 2288 = user all area monitoring ssi
				$this->db->where("vehicle_user_id", 1933);
			}
		}

		switch ($field)
		{
			case "user_company":
				if ($groupid)
				{
					$this->db->where_in("vehicle_group", $groups);
				}
				else
				{
					$this->db->where("vehicle_company", $companyid);
				}
			break;
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
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
			case "vehicletrans":
				$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
			break;
			case "device":
				$this->db->where("vehicle_device like", '%'.$keyword.'%');
			break;
			case "delayed":
				$this->db->where_in("vehicle_id", $vdelayeds);
			break;
			case "server":
				$this->db->where("vehicle_info LIKE '%\"vehicle_ip\":\"".$server."\"%'", null);
			break;
			case "location":
				$this->db->where_in("vehicle_device", $bylocation);
			break;
			case "branch":
				$this->db->where("vehicle_company", $branch);
			break;
			case "membership":
				$this->db->where("user_agent", "1");
				if ($membership == 1)
				{
					$this->db->where("user_payment_type", $membership);
					$this->db->where("user_payment_period <", "12");
				}
				else
				{
					$this->db->where("user_payment_period >=", "12");
				}

			break;
			case "vehicle_type":
				$vtypes[] = $vtype;

				$vreplaces = $this->config->item('vehicle_type_replace');
				if (isset($vreplaces[$vtype]))
				{
					//$vtypes[] = $vreplaces[$vtype];
				}

				if ($vtype == 'T1')
				{
					//$vtypes[] = "";
				}

				$this->db->where_in("vehicle_type", $vtypes);
			break;
			case "id_booking":
				if ($bybooking == 0)
				{
					$this->db->where("vehicle_device",$bybooking);
				}
				else
				{
					$this->db->where_in("vehicle_device",$bybooking);
				}
			break;
			default:
				if ($this->sess->user_type == 1)
				{
					if (! isset($_POST['btnsearch']))
					{
						$this->db->where("1 = 0", null, false);
					}
				}

				if ($field)
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				$app_tupperware = $this->config->item("app_tupperware");
				if (isset($app_tupperware) && ($app_tupperware == 1))
				{
					//422 = Group Tupperware
					$this->db->or_where("vehicle_group",422);
				}
		}


		$this->db->join("user", "vehicle_user_id = user_id");

		if ($field == "user_agent")
		{
			$this->db->join("agent", "user_agent = agent_id");
		}

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$allowed_vtype = $this->config->item('allowed_vtype');
		if ($allowed_vtype && is_array($allowed_vtype) && count($allowed_vtype))
		{
			$this->db->where_in("vehicle_type", $allowed_vtype);
		}

		$site = $this->config->item('site');
		if ($site)
		{
			$this->db->where("vehicle_site", $site);
		}



		$this->db->where("vehicle_status <>", 3);

		$q = $this->db->get("vehicle");

		$rows = $q->result();
		//print_r($rows);exit;


		for($i=0; $i < count($rows); $i++)
		{
			if (isset($vehicles[$rows[$i]->vehicle_device]))
			{
				if ($rows[$i]->vehicle_id < $vehicles[$rows[$i]->vehicle_device])
				{
					$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
					continue;
				}
			}
			$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
		}

		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);

			$rows[$i]->vehicle_id = $vehicles[$rows[$i]->vehicle_device];
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
		}

		if ($field)
		{
			$this->params['title'] = $this->lang->line('lvehicle_tracker')." (".$field."=".$keyword.") ";
		}
		else
		{
			$this->params['title'] = $this->lang->line('lvehicle_tracker');
		}

		//Get Group
		if (isset($this->sess->user_company)&&($this->sess->user_group == 0))
		{
			$branch_off = $this->sess->user_id;
			$this->db->where("company_created_by",$branch_off);
			$q_comp = $this->db->get("company");
			$row_comp = $q_comp->result();
			$this->params['branch'] = $row_comp;

		}

		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		$this->params["data"] = $rows;
		$this->params["initmap"] = $this->load->view('initmapsmartview', $this->params, true);
		$this->params["updateinfo"] =  $this->load->view('updateinfosmartview', $this->params, true);
		$this->params["content"] = $this->load->view('trackers/listsmartview', $this->params, true);
		$this->load->view("templatesesssmartview", $this->params);
	}


	// len dalam detik
	function getVehiclesDelayed($bound1, $bound2)
	{
		$vdelayeds = array(0);

		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
			$this->db->join("user", "user_id = vehicle_user_id");
		}

		$this->db->where("vehicle_status", 1);
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return $vdelayeds;

		$rows = $q->result();
		$totalvehicle = count($rows);
		for($i=0; $i < $totalvehicle; $i++)
		{
			$devices = explode("@", $rows[$i]->vehicle_device);
			if (count($devices) == 1) continue;

			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');

					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");

						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
			}

			$sql = "
							SELECT *
							FROM
							(
									SELECT  *
									FROM    `".$this->db->dbprefix.$table."`
									WHERE   1
													AND (`gps_name` =  '".$devices[0]."')
													AND (`gps_host` =  '".$devices[1]."')
									) t1
							WHERE	1
							ORDER BY        gps_time DESC
							LIMIT 1 OFFSET 0
			";

			$q = $this->db->query($sql);
			if ($q->num_rows() == 0)
			{
				if ($bound1 < 24*3600) continue;

                $vdelayeds[] = $rows[$i]->vehicle_id;
				continue;
			}

			$row = $q->row();
			$tv = dbmaketime($row->gps_time)+7*3600;

			$d = mktime() - $tv;
			if ($d < $bound1) continue;
			if (($bound2 > 0) && ($d > $bound2)) continue;

			$vdelayeds[] = $rows[$i]->vehicle_id;
		}

		$this->db = $this->load->database("default", TRUE);

		return $vdelayeds;
	}

	function search($id, $name, $host)
	{

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		switch($id)
		{
			case "overspeed":
				if (! $this->validate_overspeed()) return;
				$this->searchoverspeed($id, $name, $host, $offset);
			break;
			case "parkingtime":
				if (! $this->validate_parkingtime()) return;
				$this->searchparkingtime($id, $name, $host, $offset);
			break;
			case "history":
				if (! $this->validate_history()) return;
				$this->searchhistory($id, $name, $host, $offset);
			break;
			case "pulse":
				if (! $this->validate_history()) return;
				$this->searchpulse($id, $name, $host, $offset);
			break;
			case "fuel":
				if (! $this->validate_history()) return;
				$this->searchhistory($id, $name, $host, $offset);
			break;
			case "odometer":
				if (! $this->validate_history()) return;
				$this->searchhistory($id, $name, $host, $offset);
			break;
			case "workhour":
				if (! $this->validate_history()) return;
				$this->searchworkhour($id, $name, $host, $offset);
			break;
			case "engine":
				if (! $this->validate_history()) return;
				$this->searchengine($id, $name, $host, $offset);
			break;
			case "door":
				if (! $this->validate_history()) return;
				$this->searchdoor($id, $name, $host, $offset);
			break;
			case "geofence":
				if (! $this->validate_history()) return;
				$geoname = $this->input->post('geo_name');
				$this->searchgeofence($id, $name, $host, $offset, $geoname);
			break;
			case "alarm":
				if (! $this->validate_history()) return;
				$this->searchalarm($id, $name, $host, $offset);
			break;

		}

	}

	function searchalarm($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}

		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');
		$type = isset($_POST['alarmtype']) ? $_POST['alarmtype'] : "";

		$order = $this->config->item("orderhist") ? $this->config->item("orderhist") : "desc";

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$tables = $this->gpsmodel->getTable($rowvehicle);

		$this->db = $this->load->database($tables["dbname"], TRUE);

		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;

		$t1 = $this->period1 - 7*3600;
		$t2 = $this->period2 - 7*3600;

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"alarm_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo "no";
			echo $this->config->item('csv_separator');
			echo "date";
			echo $this->config->item('csv_separator');
			echo "time";
			echo $this->config->item('csv_separator');
			echo "alarm type";
			echo $this->config->item('csv_separator');
			echo "data";
			echo "\r\n";
		}

		$alarms = $this->config->item("ALARMS");
		$wheres = array();

		if ($type)
		{
			$wheres[] = sprintf("gps_info_alarm_alert = '%s'", $type);
		}
		else
		{
			$cond = "(FALSE ";

			foreach(array_keys($alarms) as $alarmtype)
			{
				$cond .= sprintf(" OR (gps_info_alarm_alert = '%s') ", $alarmtype);
			}

			$cond .= ")";

			$wheres[] = $cond;
		}

		if ($t1 > $yesterday)
		{
			$rows = $this->historymodel->allinfo($tables['info'], $name, $host, $t1, $t2, $limit, $offset, $wheres, $order);
			$total = $this->historymodel->allinfo($tables['info'], $name, $host, $t1, $t2, -1, 0, $wheres);
		}
		else
		{
			$tablehist = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			if ($t2 > $yesterday)
			{
					// mix

				$rows1 = $this->historymodel->allinfo($tables['info'], $name, $host, $yesterday, $t2, 0, $offset, $wheres, $order);

				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $rowvehicle->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);

				$rows2 = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $yesterday, 0, $offset, $wheres, $order);

				if ($order == "desc")
				{
					$rows = array_merge($rows2, $rows1);
				}
				else
				{
					$rows = array_merge($rows1, $rows2);
				}

				$total = count($rows);
				$rows = array_slice($rows, $offset, $limit);

			}
			else
			{
				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $rowvehicle->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);

				$rows = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $t2, $limit, $offset, $wheres, $order);
				$total = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $t2, -1, $offset, $wheres);
			}
		}

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_info_time_t = dbmaketime($rows[$i]->gps_info_time);
			$rows[$i]->gps_info_alarm_alert_name = isset($alarms[$rows[$i]->gps_info_alarm_alert]) ? $alarms[$rows[$i]->gps_info_alarm_alert] : $rows[$i]->gps_info_alarm_alert;

			if ($_POST['act'] == "export")
			{
				echo $i+1;
				echo $this->config->item('csv_separator').date("d/m/Y", $rows[$i]->gps_info_time_t);
				echo $this->config->item('csv_separator').date("H:i:s", $rows[$i]->gps_info_time_t);
				echo $this->config->item('csv_separator').$rows[$i]->gps_info_alarm_alert_name;
				echo $this->config->item('csv_separator').$rows[$i]->gps_info_alarm_data;
				echo "\r\n";
			}
		}

		if ($_POST['act'] == "export")
		{
			return;
		}

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();
		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $rows;
		$params['id'] = $id;
		$html = $this->load->view("trackers/listsearchalarm", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);
	}

	function searchhistory($id, $name, $host, $offset)
	{

		ini_set("memory_limit","3G");

		include 'class/PHPExcel.php';
		include 'class/PHPExcel/Writer/Excel2007.php';
		$objPHPExcel = new PHPExcel();

		$isanimate = isset($_POST['isanimate']) && ($_POST['isanimate'] == 1);

		if (isset($_POST['format']) && $_POST['format'] != "kml") //edited
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;
			}
		}

		$limit    = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit']: $this->config->item('history_limit_records');
		$datatype = isset($_POST['data']) ? $_POST['data']:                        1;

		$order = $this->config->item("orderhist") ? $this->config->item("orderhist") : "desc";

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q             = $this->db->get("vehicle");
		$rowvehicle    = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		$json          = json_decode($rowvehicle->vehicle_info);

		$tyesterday = mktime();//-24*3600;
		//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
		{

			$tyesterday = mktime(-7, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));
			$yesterday  = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
			$t1         = $this->period1 - 7*3600;
			$t2         = $this->period2 - 7*3600;
		}
		else
		{
			$tyesterday = mktime(-0, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));
			$yesterday  = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
			$t1         = $this->period1 - 0*3600;
			$t2         = $this->period2 - 0*3600;
		}
		// print_r($t1.'-'.$t2);exit();

		$tables            = $this->gpsmodel->getTable($rowvehicle);
		$this->db          = $this->load->database($tables["dbname"], TRUE);

		$params['vehicle'] = $rowvehicle;

		$isgtp          = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));
		$isgtp_portable = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_odometer_portable"));

		if ($_POST['act'] == "export" && $_POST['format'] != "kml") //edited
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"history_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo "Periode: ".date("d/m/Y H:i:s", $this->period1)." to ".date("d/m/Y H:i:s", $this->period2)."\r\n\r\n";

			$header = "no;date;time;position;coordinate";
			if (($id == "history") || ($id == "odometer"))
			{
				if ($id == "history")
				{
					$header .= ";status";
				}

				$header .= ";speed";
			}

			if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp")))
			{
				if (($id == "history") || ($id == "odometer"))
				{
					if ($id == "history")
					{
						$header .= ";engine";
					}

					$header .= ";odometer";
				}
			}

			if ($id == "fuel")
			{
				$header .= ";fuel";
			}

			csvheader($header, $this->config->item('csv_separator'));
		}

		$tablehist      = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo  = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

		$totalodometer  = 0;
		$totalodometer1 = 0;

		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			$rows = $this->historymodel->all($tables["gps"], $name, $host, $t1, $t2, (($_POST['act'] != "export") && ($datatype != 2)) ? $limit : 0, $offset);
			if ($_POST['act'] != "export")
			{
				$total = $this->historymodel->all($tables["gps"], $name, $host, $t1, $t2, -1);
			}

			$rowlastinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $t1, $t2,  1);

			if (count($rowlastinfos) > 0)
			{
				$totalodometer = $rowlastinfos[0]->gps_info_distance;

				$rowfirstinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $t1, $t2,  1, 0, array(), "ASC");

				if (count($rowfirstinfos))
				{
					$totalodometer1 = $totalodometer-$rowfirstinfos[0]->gps_info_distance;
				}
			}
		}
		else
		{
				//mix
				if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
				{
					$rows = $this->historymodel->all($tables["gps"], $name, $host, $yesterday+1, $t2, 0);
					$rowlastinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $yesterday, $t2,  1);

					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
					$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $yesterday, 0);
				}
				else if($t2 > $yesterday && (isset($json->vehicle_ws)))
				{
					$this->db = $this->load->database("gpshistory2", TRUE);
					$rows = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
					$rowlastinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $yesterday, $t2,  1);

					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
					$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $yesterday, 0);
				}
				else
				{
					$this->db = $this->load->database("gpshistory2", TRUE);
					$rows = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
					$rowlastinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $t1, $t2,  1);

					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
					$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
				}

				$rows = array_merge($rows, $rowshist);

				$total = count($rows);
				if (($_POST['act'] != "export") && ($datatype != 2))
				{
					$rows = array_slice($rows, $offset, $limit);
				}

				if (count($rowlastinfos))
				{
					$totalodometer = $rowlastinfos[0]->gps_info_distance;
					$rowfirstinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $t1, $yesterday,  1, 0, array(), "ASC");

					if (count($rowfirstinfos))
					{
						$totalodometer1 = $totalodometer-$rowfirstinfos[0]->gps_info_distance;
					}
				}
		}

		if ($datatype == 2 || $isanimate)
		{
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

				if ($_POST['act'] == "export")
				{
					$rows = $rowsummary;
				}
				else
				{
					$rows = array_splice($rowsummary, $offset, $limit);
				}
			}
		}


		//get header KML
		if($_POST['format'] == "kml"){

			$kml = array('<?xml version="1.0" encoding="UTF-8"?>');
			$kml[] = '<kml xmlns="http://earth.google.com/kml/2.1">';
			$kml[] = ' <Document>';

			$kml[] = ' <name>' . "tes" . '</name>';
			$kml[] = ' <description>' . "test" . '</description>';


			$kml[] = ' <Style id="style3">';
			$kml[] = ' <linestyle>';
			$kml[] = ' <color>'."73FF0000".'</color>';
			$kml[] = ' <width>'."5".'</width>';
			$kml[] = ' </linestyle>';
			$kml[] = ' </Style>';



			$kml[] = ' <Style id="exampleBalloonStyle">';
			$kml[] = ' <IconStyle>';
			$kml[] = ' <Icon>';
			//$kml[] = ' <href>http://tcontinent.lacak-mobil.com/assets/images/pup2.png</href>';
			$kml[] = ' <href>http://tcontinent.lacak-mobil.com/assets/images/car/car_front.png</href>';
			$kml[] = ' <scale>1.0</scale>';
			$kml[] = ' </Icon>';
			$kml[] = ' </IconStyle>';
			//new
			$kml[] = '<BalloonStyle>';
			$kml[] = '<bgColor>'."00664422".'</bgColor>';
			$kml[] = '</BalloonStyle>';
			//end

			$kml[] = ' </Style>';

		}

		$a = "";


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

			if ($id == "fuel")
			{
				$resistance = "";
				if(isset($infos[$rows[$i]->gps_timestamp]) && ($infos[$rows[$i]->gps_timestamp]->gps_info_ad_input != ""))
				{
					$ad_input = $infos[$rows[$i]->gps_timestamp]->gps_info_ad_input;

					if ($ad_input != 'FFFFFF' || $ad_input != '999999' || $ad_input != 'YYYYYY')
					{
						$res_1 = hexdec(substr($ad_input, 0, 4));
						$res_2 = (hexdec(substr($ad_input, 0, 2))) * 0.1;

						$resistance = $res_1 + $res_2;

					}
				}

				$rows[$i]->fuel = $this->get_fuel($resistance, $rowvehicle->vehicle_fuel_capacity);
			}

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
				//print_r($row_lastvalid);exit();
				$rows[$i]->gps_longitude_real = getLongitude($row_lastvalid->gps_longitude, $row_lastvalid->gps_ew);
				$rows[$i]->gps_latitude_real = getLatitude($row_lastvalid->gps_latitude, $row_lastvalid->gps_ns);
			}
			else
			{
				//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
				if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
				{
					if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate")))
					{
						$rows[$i]->gps_longitude_real = $rows[$i]->gps_longitude_real;
						$rows[$i]->gps_latitude_real = $rows[$i]->gps_latitude_real;
					}
					else
					{
						$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
						$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					}
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

			//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
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
				if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315")
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

			//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
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

			if (($id == "history") && ($_POST['act'] != "export"))
			{
				$map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
			}

			//Fan || Dooe
			$appfan = $this->config->item("fan_app");
			if ($rowvehicle->vehicle_type == "T5FAN" || $rowvehicle->vehicle_type == "T5DOOR" || $rowvehicle->vehicle_type == "T5PTO")
			{

				$rows[$i]->fan = $this->getFanStatus($rows[$i]->gps_msg_ori);

			}

			if ($rowvehicle->vehicle_type == "TK315DOOR")
			{

				$rows[$i]->fan = $this->getDoorStatus($rows[$i]->gps_msg_ori);

			}


			if($_POST['format'] == "kml"){

				//$kml[] = ' <Placemark id="point'.$rowvehicle->vehicle_id.'" >';
				$kml[] = ' <Placemark>';
				$kml[] = ' <name>'.$i.'</name>';
				$kml[] = ' <description>'.$rows[$i]->gpsaddress.'</description>';
				$kml[] = ' <styleUrl>#exampleBalloonStyle</styleUrl>';
				$kml[] = ' <Point>';
				$kml[] = ' <coordinates>' . $rows[$i]->gps_longitude_real_fmt . ','  . $rows[$i]->gps_latitude_real_fmt . '</coordinates>';
				$kml[] = ' </Point>';
				$kml[] = ' </Placemark>';


				 	if($i == (count($rows) - 1)){

						$kml[] = ' <Placemark id="point'.$rowvehicle->vehicle_id.'" >';
						$kml[] = ' <styleUrl>#style3</styleUrl>';
						$kml[] = ' <LineString>';
						$kml[] = ' <tessellate>'."1".'</tessellate>';
						$kml[] = ' <coordinates>';

					}

					$a .= $rows[$i]->gps_longitude_real_fmt . ','  . $rows[$i]->gps_latitude_real_fmt." ";


					if($i == (count($rows) - 1)){

						$kml[] = $a;
						$kml[] = ' </coordinates>';
						$kml[] = ' </LineString>';
						$kml[] = ' </Placemark>';

					}

			}
		}


		if($_POST['format'] == "kml"){

			//End XML file
			$kml[] = ' </Document>';
			$kml[] = '</kml>';
			$kmlOutput = join("\n", $kml);
			header('Content-type: application/vnd.google-earth.kml+xml kml');
			header("Content-Disposition: attachment; filename=\"history_".date("Ymd_His")."_to_".date("Ymd_His").".kml\"");
			echo $kmlOutput;

		}


		if ($_POST['act'] == "export" && $_POST['format'] != "kml")
		{
			$fields = array("gpsindex", "gpsdate", "gpstime", "gpsaddress", "gpscoord");

			if (($id == "history") || ($id == "odometer"))
			{
				if ($id == "history")
				{
					$fields[] = "gpstatus";
				}

				$fields[] = "gps_speed_fmt";
			}

			if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp")))
			{
				if (($id == "history") || ($id == "odometer"))
				{
					if ($id == "history")
					{
						$fields[] = "status1";
					}

					$fields[] = "odometer";
				}
			}

			if ($id == "fuel")
			{
				$fields[] = "fuel";
			}

			csvcontents($fields, $rows, $this->config->item('csv_separator'));
			return;
		}

		if (($id == "history") && isset($map_params))
		{
			$uniqid = md5( uniqid() );
			$this->db = $this->load->database("default", TRUE);
			unset($insert);

			$insert['log_created'] = date("Y-m-d H:i:s");
			$insert['log_creator'] = $this->sess->user_id;
			$insert['log_type'] = 'mapparams'.$uniqid;
			$insert['log_ip'] = "";
			$insert['log_data'] = json_encode($map_params);
			$insert['log_version'] = "desktop";
			$insert['log_target'] = "";
			$this->db->insert("log", $insert);
		}

		if ($isanimate)
		{
			$this->animate($rows, $rowvehicle, $t1+7*3600, $t2+7*3600);
			return;
		}


		$this->load->library('pagination1');

		$config['total_rows']  = $total;
		$config['uri_segment'] = 6;
		$config['per_page']    = $limit;
		$config['num_links']   = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['uniqid']         = isset($uniqid) ? $uniqid: "";
		$params['isgtp']          = $isgtp;
		$params['isgtp_portable'] = $isgtp_portable;
		$params['totalodometer']  = round(($totalodometer+$rowvehicle->vehicle_odometer*1000)/1000);
		$params['totalodometer1'] = number_format(round($totalodometer1/1000), 0, ".", ",");
		$params['paging']         = $this->pagination1->create_links();
		$params['gps_name']       = $name;
		$params['gps_host']       = $host;
		$params['offset']         = $offset;
		$params['data']           = $rows;
		$params['id']             = $id;
		$params['ismove']         = $ismove;
		$params['gps_type']       = $rowvehicle->vehicle_type;
		$html                     = $this->load->view("trackers/listsearch", $params, true);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html']  = $html;

		//kembalikan DB ke semula
		$this->db          = $this->load->database("default", TRUE);

		if ($_POST['format'] != "kml"){
			echo json_encode($callback);
		}
	}

	function searchpulse($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}

		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');
		$order = $this->config->item("orderhist") ? $this->config->item("orderhist") : "desc";

		$tyesterday = mktime();//-24*3600;
		$tyesterday = mktime(-7, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$tblhists = $this->config->item("table_hist_info");
		$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];

		$tblhistinfos = $this->config->item("table_hist_info");
		$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];

		$params['vehicle'] = $rowvehicle;

		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));

		$t1 = mktime(date("G", $this->period1)-7, date("i", $this->period1), date("s", $this->period1), date("n", $this->period1), date("d", $this->period1), date("Y", $this->period1));
		$t2 = mktime(date("G", $this->period2)-7, date("i", $this->period2), date("s", $this->period2), date("n", $this->period2), date("d", $this->period2), date("Y", $this->period2));

		$this->db->where("gps_info_device", $name.'@'.$host);
		$this->db->where("gps_info_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $t2));

		if ($_POST['act'] == "export")
		{
			if ($this->period1 >= $tyesterday)
			{
				// hari ini

				$this->db->order_by("gps_info_time", $order);
				$q = $this->db->get($this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type));
			}
			else
			{
				$this->db->from($this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type));
				$sql1 = $this->db->_compile_select();
				$sql2 = str_replace($this->db->dbprefix.$this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type), $this->db->dbprefix.$this->tblhist, $sql1);

				$m_sql = "FROM ( ".$sql1." UNION ".$sql2." ) tbl1 ORDER BY gps_info_time DESC ";
				$sql = "SELECT * ".$m_sql;

				$q = $this->db->query($sql);
			}

			if ($_POST['act'] == "export")
			{
				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=\"pulse_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

				echo "Periode: ".date("d/m/Y H:i:s", $this->period1)." to ".date("d/m/Y H:i:s", $this->period2)."\r\n\r\n";

				echo "no";
				echo $this->config->item('csv_separator');
				echo "date";
				echo $this->config->item('csv_separator');
				echo "time";
				echo $this->config->item('csv_separator');
				echo "pulse";
				echo "\r\n";
			}
		}
		else
		{
			if ($this->period1 >= $tyesterday)
			{
				// hari ini

				$this->db->order_by("gps_info_time", $order);
				$q = $this->db->get($this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type));
			}
			else
			{
				$this->db->from($this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type));
				$sql1 = $this->db->_compile_select();
				$sql2 = str_replace($this->db->dbprefix.$this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type), $this->db->dbprefix.$this->tblhist, $sql1);

				$m_sql = "FROM ( ".$sql1." UNION ".$sql2." ) tbl1 ORDER BY gps_info_time DESC ";
				$sql = "SELECT * ".$m_sql;

				$q = $this->db->query($sql);
			}
		}

		$rows = $q->result();
		for($i=count($rows)-1; $i >= 0; $i--)
		{
			$rows[$i]->pulse = hexdec(substr($rows[$i]->gps_info_ad_input, 0, 5));
			if (($i+1) >= count($rows))
			{
				$rowsummary[] = $rows[$i];
				continue;
			}

			if ($rows[$i]->pulse == $rows[$i+1]->pulse) continue;

			$rowsummary[] = $rows[$i];
		}

		$rows = array();
		$total = 0;
		if (isset($rowsummary))
		{
			//$rows = array_reverse($rowsummary);

			$rows = $rowsummary;
			$total_pulse = 0;

			for($i=1; $i < count($rows); $i++)
			{
				$delta = $rows[$i-1]->pulse - $rows[$i]->pulse;
				if ($delta < 0) continue;

				$total_pulse += $delta;
			}

			$total = count($rows);

			$rows = array_splice($rows, $offset, $limit);
		}

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_info_time_t = dbmaketime($rows[$i]->gps_info_time)+7*3600;

			if ($_POST['act'] == "export")
			{
				echo $i+1;
				echo $this->config->item('csv_separator').date("d/m/Y", $rows[$i]->gps_info_time_t);
				echo $this->config->item('csv_separator').date("H:i:s", $rows[$i]->gps_info_time_t);
				echo $this->config->item('csv_separator').number_format($rows[$i]->pulse, 0, "", ".");
				echo "\r\n";
			}
		}

		if ($_POST['act'] == "export")
		{
			return;
		}

		// paging

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['total_pulse'] = abs($total_pulse);
		$params['paging'] = $this->pagination1->create_links();
		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $rows;
		$html = $this->load->view("trackers/listsearchpulse", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);
	}

	function searchworkhour($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}

		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$json = json_decode($rowvehicle->vehicle_info);

		$tables = $this->gpsmodel->getTable($rowvehicle);

		$this->db = $this->load->database($tables["dbname"], TRUE);


		//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
		{
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
			$t1 = $this->period1 - 7*3600;
			$t2 = $this->period2 - 7*3600;
		}
		else
		{
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
			$t1 = $this->period1;
			$t2 = $this->period2;
		}


		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			$rows = $this->historymodel->allinfo($tables['info'], $name, $host, $t1, $t2, 0);
		}
		else
		{
			$tablehist = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
			{
					// mix

				$rows1 = $this->historymodel->allinfo($tables['info'], $name, $host, $yesterday, $t2, 0);

				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $rowvehicle->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);
				$rows2 = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $yesterday, 0);
				$rows = array_merge($rows1, $rows2);
			}
			else if ($t2 > $yesterday && (isset($json->vehicle_ws)))
			{
				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows1 = $this->historymodel->allinfo($tablehist, $name, $host, $yesterday, $t2, 0);

				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $rowvehicle->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);
				$rows2 = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $yesterday, 0);

				$rows = array_merge($rows1, $rows2);
			}
			else
			{
				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $rowvehicle->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);
				$rows = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $t2, 0);
			}
		}

		$onoffs = array();
		$i = 0;
		$totalt = 0;
		while(1)
		{
			if ($i >= count($rows)) break;

			unset($iperiod1);
			unset($iperiod2);

			// maju sampe status on
			while(1)
			{
				if ($i >= count($rows)) break;
				$ioport = $rows[$i]->gps_info_io_port;

				if ($ioport[4] == 1)
				{
					$iperiod1 = $i++;
					break;
				}
				$i++;
			}

			//maju sampe status off
			while(1)
			{
				if ($i >= count($rows)) break;
				$ioport = $rows[$i]->gps_info_io_port;

				if ($ioport[4] == 0)
				{
					$iperiod2 = $i++;
					break;
				}
				$i++;
			}

			if (! isset($iperiod1)) continue;
			if (isset($iperiod2))
			{
				$t1 = dbmaketime($rows[$iperiod1]->gps_info_time);
				$t2 = dbmaketime($rows[$iperiod2]->gps_info_time);

				//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
				if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
				{
					$t1 = mktime(date('G', $t1)+7, date('i', $t1), date('s', $t1), date('n', $t1), date('j', $t1), date('Y', $t1));
					$t2 = mktime(date('G', $t2)+7, date('i', $t2), date('s', $t2), date('n', $t2), date('j', $t2), date('Y', $t2));
				}
				else
				{
					$t1 = mktime(date('G', $t1)+0, date('i', $t1), date('s', $t1), date('n', $t1), date('j', $t1), date('Y', $t1));
					$t2 = mktime(date('G', $t2)+0, date('i', $t2), date('s', $t2), date('n', $t2), date('j', $t2), date('Y', $t2));
				}

				$dt = $t1-$t2;

				$totalt += $dt;

				$onoffs[] = array($t1, $t2, $dt, sprintf("%d:%02d:%02d", floor($dt)/3600, floor(($dt%3600)/60), ($dt%3600)%60));
				continue;
			}

			// ini pasti terakhir, artinya posisi terakhir dalam posisi ON

			$t1 = dbmaketime($rows[$iperiod1]->gps_info_time);
			//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
				$t1 = mktime(date('G', $t1)+7, date('i', $t1), date('s', $t1), date('n', $t1), date('j', $t1), date('Y', $t1));
			}
			else
			{
				$t1 = mktime(date('G', $t1)+0, date('i', $t1), date('s', $t1), date('n', $t1), date('j', $t1), date('Y', $t1));
			}

			$t2 = dbmaketime($rows[count($rows)-1]->gps_info_time);

			//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TK315DOOR_NEW" && $rowvehicle->vehicle_type != "TK315_NEW" && $rowvehicle->vehicle_type != "TK309_NEW")
			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
				$t2 = mktime(date('G', $t2)+7, date('i', $t2), date('s', $t2), date('n', $t2), date('j', $t2), date('Y', $t2));
			}
			else
			{
				$t2 = mktime(date('G', $t2)+0, date('i', $t2), date('s', $t2), date('n', $t2), date('j', $t2), date('Y', $t2));
			}

			$dt = $t1-$t2;
			$totalt += $dt;

			$onoffs[] = array($t1, $t2, $dt, sprintf("%d:%02d:%02d", floor($dt)/3600, floor(($dt%3600)/60), ($dt%3600)%60));
		}

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"workhour_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo $this->lang->line("llongtimetotal").": ".sprintf("%d:%02d:%02d", floor($totalt)/3600, floor(($totalt%3600)/60), ($totalt%3600)%60)." \r\n";
			echo "no";
			echo $this->config->item('csv_separator');
			echo "period";
			echo $this->config->item('csv_separator');
			echo "long time";
			echo "\r\n";

			for($i=0; $i < count($onoffs); $i++)
			{
				echo $i+1+$offset;
				echo $this->config->item('csv_separator').date('M, jS Y H:i:s ', $onoffs[$i][0]);
				echo $this->config->item('csv_separator').date('M, jS Y H:i:s ', $onoffs[$i][1]);
				echo $this->config->item('csv_separator').$onoffs[$i][3];
				echo "\r\n";
			}

			return;
		}

		$total = count($onoffs);
		$onoffs = array_slice($onoffs, $offset, $limit);

		// paging

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();

		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $onoffs;
		$params['longtime'] = sprintf("%d:%02d:%02d", floor($totalt)/3600, floor(($totalt%3600)/60), ($totalt%3600)%60);
		$html = $this->load->view("trackers/listsearchworkhour", $params, true);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

	}

	function totalengine($vehicle, $t1, $t2, $status, $engine1, $engine2)
	{
		if ($vehicle->vehicle_info)
		{
			$json = json_decode($vehicle->vehicle_info);

			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');

				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];

					$table = $this->config->item("external_gpstable");
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->tblhist = $this->config->item("external_gpstable_history");
					$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
					$tblinfohist = $this->tblinfohist;

					$this->db = $this->load->database($database, TRUE);
				}
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($vehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($vehicle->vehicle_device));
				$this->tblhist = $table;
				$this->tblinfohist = $tableinfo;
				$this->db = $this->load->database("gpshistory2", TRUE);

				$post_time1 = date("Y-m-d H:i:s", $t1);
				$post_time2 = date("Y-m-d H:i:s", $t2);

				$now = date("Y-m-d H:i:s");

				if ($post_time1 < $now)
				{
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $vehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
				}
				if ($post_time2 > $now)
				{
					$this->db = $this->load->database("gpshistory2", TRUE);
				}
			}
		}

		if (! isset($tableinfo))
		{
			$tableinfo = $this->gpsmodel->getGPSInfoTable($vehicle->vehicle_type);
		}

		switch($status)
		{
			case "on":
				$this->db->where("gps_info_io_port REGEXP '^.{4}1'", null);
			break;
			case "off":
				$this->db->where("gps_info_io_port REGEXP '^.{4}0'", null);
			break;
			case "hold":
				$this->db->where("gps_info_io_port REGEXP '^.{3}1'", null);
			break;
			case "release":
				$this->db->where("gps_info_io_port REGEXP '^.{3}0'", null);
			break;
			case "opened":
				$this->db->where("gps_info_io_port REGEXP '^.1'", null);
			break;
			case "closed":
				$this->db->where("gps_info_io_port REGEXP '^.0'", null);
			break;
		}

		switch($engine1)
		{
			case "on":
				$this->db->where("gps_info_io_port REGEXP '^.{4}1'", null);
			break;
			case "off":
				$this->db->where("gps_info_io_port REGEXP '^.{4}0'", null);
			break;
		}

		switch($engine2)
		{
			case "on":
				$this->db->where("gps_info_io_port REGEXP '^.{3}1'", null);
			break;
			case "off":
				$this->db->where("gps_info_io_port REGEXP '^.{3}0'", null);
			break;
		}

		$this->db->where("gps_info_device", $vehicle->vehicle_device);
		$this->db->where("gps_info_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $t2));

		$csql = $this->db->_compile_select();

		$this->db->from($tableinfo);
		$total = $this->db->count_all_results();

		$tyesterday = mktime();//-24*3600;
		$tyesterday = mktime(-7, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));

		/* if ($this->period1 < $tyesterday)
		{
			$sql = str_replace("SELECT *", "SELECT COUNT(*) total FROM ".$this->db->dbprefix.$this->tblinfohist, $csql);

			$q = $this->db->query($sql);
			$row = $q->row();
			$total += $row->total;

		} */

		return $total;
	}

	function searchengine($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}


		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');

		$engine1 = isset($_POST['engine1']) ? $_POST['engine1'] : '';
		$engine2 = isset($_POST['engine2']) ? $_POST['engine2'] : '';

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$json = json_decode($rowvehicle->vehicle_info);

		$tables = $this->gpsmodel->getTable($rowvehicle);

		$this->db = $this->load->database($tables["dbname"], TRUE);

		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;

		$t1 = $this->period1 - 7*3600;
		$t2 = $this->period2 - 7*3600;

		$wheres = array();

		switch($engine1)
		{
			case "on":
				$wheres[] = "gps_info_io_port REGEXP '^.{4}1'";
			break;
			case "off":
				$wheres[] = "gps_info_io_port REGEXP '^.{4}0'";
			break;
		}

		switch($engine2)
		{
			case "on":
				$wheres[] = "gps_info_io_port REGEXP '^.{3}1'";
			break;
			case "off":
				$wheres[] = "gps_info_io_port REGEXP '^.{3}0'";
			break;
		}

		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			$rows = $this->historymodel->allinfo($tables['info'], $name, $host, $t1, $t2, $limit, $offset, $wheres);
			$total = $this->historymodel->allinfo($tables['info'], $name, $host, $t1, $t2, -1, 0, $wheres);
		}
		else
		{
			$tablehist = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
			{
					// mix

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rows1 = $this->historymodel->allinfo($tablehist, $name, $host, $yesterday, $t2, 0, $offset, $wheres);
				$rows2 = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $yesterday, 0, $offset, $wheres);

				$rows = array_merge($rows1, $rows2);

				$total = count($rows);
				$rows = array_slice($rows, $offset, $limit);

				$this->db = $this->load->database("default", TRUE);

			}
			else if ($t2 > $yesterday && (isset($json->vehicle_ws)))
			{
				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows1 = $this->historymodel->allinfo($tablehist, $name, $host, $yesterday, $t2, 0, $offset, $wheres);

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rows2 = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $yesterday, 0, $offset, $wheres);

				$rows = array_merge($rows1, $rows2);

				$total = count($rows);
				$rows = array_slice($rows, $offset, $limit);

				$this->db = $this->load->database("default", TRUE);
			}
			else
			{

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rows = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $t2, $limit, $offset, $wheres);
				$total = $this->historymodel->allinfo($tablehist, $name, $host, $t1, $t2, -1, $offset, $wheres);

				$this->db = $this->load->database("default", TRUE);
			}
		}

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_info_time_t = dbmaketime($rows[$i]->gps_info_time);

			$ioport = $rows[$i]->gps_info_io_port;

			$rows[$i]->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1));
			$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1));
			$rows[$i]->status = $rows[$i]->status2 || $rows[$i]->status1;
		}

		$totalengine_on = $this->totalengine($rowvehicle, $t1, $t2, 'on', $engine1, $engine2);
		$totalengine_off = $this->totalengine($rowvehicle, $t1, $t2, 'off', $engine1, $engine2);

		$totalengine_hold = $this->totalengine($rowvehicle, $t1, $t2, 'hold', $engine1, $engine2);
		$totalengine_release = $this->totalengine($rowvehicle, $t1, $t2, 'release', $engine1, $engine2);

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"engine_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo $this->lang->line("lengine_1")." \r\n";
			echo $this->lang->line("lon").$this->config->item('csv_separator').$totalengine_on."\r\n";
			echo $this->lang->line("loff").$this->config->item('csv_separator').$totalengine_off."\r\n";

			echo $this->lang->line("lengine_2")." \r\n";
			echo $this->lang->line("lrelease").$this->config->item('csv_separator').$totalengine_hold."\r\n";
			echo $this->lang->line("lunrelease").$this->config->item('csv_separator').$totalengine_release."\r\n";

			echo "no";
			echo $this->config->item('csv_separator');
			echo "period";
			echo $this->config->item('csv_separator');
			echo "engine";
			echo $this->config->item('csv_separator');
			echo "engine 1";
			echo "\r\n";

			for($i=0; $i < count($rows); $i++)
			{
				echo $i+1+$offset;
				echo $this->config->item('csv_separator').date('D M, jS Y H:i:s ', $rows[$i]->gps_info_time_t);
				echo $this->config->item('csv_separator').(($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff'));
				echo $this->config->item('csv_separator').(($rows[$i]->status2) ? $this->lang->line('lrelease') : $this->lang->line('lunrelease'));
				echo "\r\n";
			}

			return;
		}

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();

		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['totalengine_on'] = $totalengine_on;
		$params['totalengine_off'] = $totalengine_off;
		$params['totalengine_hold'] = $totalengine_hold;
		$params['totalengine_release'] = $totalengine_release;
		$params['rows'] = $rows;
		$html = $this->load->view("trackers/listsearchengine", $params, true);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

	}

	function searchdoor($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}

		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');
		$status = isset($_POST['status']) ? $_POST['status'] : '';

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$tblhists = $this->config->item("table_hist_info");
		$this->tblinfohist = $tblhists[strtoupper($rowvehicle->vehicle_type)];

		$tyesterday = mktime();//-24*3600;
		$tyesterday = mktime(-7, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));

		$t1 = mktime(date("G", $this->period1)-7, date("i", $this->period1), date("s", $this->period1), date("n", $this->period1), date("d", $this->period1), date("Y", $this->period1));
		$t2 = mktime(date("G", $this->period2)-7, date("i", $this->period2), date("s", $this->period2), date("n", $this->period2), date("d", $this->period2), date("Y", $this->period2));

		switch($status)
		{
			case "opened":
				$this->db->where("gps_info_io_port REGEXP '^.1'", null);
			break;
			case "closed":
				$this->db->where("gps_info_io_port REGEXP '^.0'", null);
			break;
		}

		$this->db->where("gps_info_device", $name.'@'.$host);
		$this->db->where("gps_info_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $t2));
		$this->db->from($this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type));


		if ($this->period1 >= $tyesterday)
		{
			$this->db->order_by("gps_info_time", "ASC");

			$m_sql = $this->db->_compile_select();
			$m_sqltotal = str_replace("SELECT *", "SELECT COUNT(*) total", $m_sql);

			if ($_POST['act'] != "export")
			{
				$this->db->limit($limit, $offset);
			}

			$q = $this->db->get();
		}
		else
		{
			$sql1 = $this->db->_compile_select();
			$this->db->_reset_select();
			$sql2 = str_replace($this->db->dbprefix.$this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type), $this->db->dbprefix.$this->tblinfohist, $sql1);

			$sql = "SELECT * FROM ( ".$sql1." UNION ".$sql2." ) tbl1 ORDER BY gps_info_time ASC ";
			$m_sqltotal = "SELECT COUNT(*) total FROM ( ".$sql1." UNION ".$sql2." ) tbl1 ORDER BY gps_info_time ASC ";

			if ($_POST['act'] != "export")
			{
				$sql = "SELECT * FROM ( ".$sql1." UNION ".$sql2." ) tbl1 ORDER BY gps_info_time ASC  LIMIT ".$limit." OFFSET ".$offset;
			}

			$q = $this->db->query($sql);
		}

		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_info_time_t = dbmaketime($rows[$i]->gps_info_time)+7*3600;

			$ioport = $rows[$i]->gps_info_io_port;

			$rows[$i]->status1 = ((strlen($ioport) > 1) && ($ioport[1] == 1));
			$rows[$i]->status = $rows[$i]->status1;
		}

		$totalengine_opened = $this->totalengine($rowvehicle, $t1, $t2, 'opened', "", "");
		$totalengine_closed = $this->totalengine($rowvehicle, $t1, $t2, 'closed', "", "");

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"door_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo $this->lang->line("lengine_1")." \r\n";
			echo $this->lang->line("lopened").$this->config->item('csv_separator').$totalengine_opened."\r\n";
			echo $this->lang->line("lclosed").$this->config->item('csv_separator').$totalengine_closed."\r\n";

			echo "no";
			echo $this->config->item('csv_separator');
			echo "period";
			echo $this->config->item('csv_separator');
			echo "status";
			echo "\r\n";

			for($i=0; $i < count($rows); $i++)
			{
				echo $i+1+$offset;
				echo $this->config->item('csv_separator').date('D M, jS Y H:i:s ', $rows[$i]->gps_info_time_t);
				echo $this->config->item('csv_separator').(($rows[$i]->status1) ? $this->lang->line('lopened') : $this->lang->line('lclosed'));
				echo "\r\n";
			}

			return;
		}

		$q = $this->db->query($m_sqltotal);
		$row = $q->row();
		$total = $row->total;

		// paging

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();

		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['totalengine_opened'] = $totalengine_opened;
		$params['totalengine_closed'] = $totalengine_closed;
		$params['rows'] = $rows;
		$html = $this->load->view("trackers/listsearchdoor", $params, true);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

	}

	function searchparkingtime($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}

        if (isset($_POST['format']) && ($_POST['format'] == "excell" || $_POST['format'] == "pdf" ))
		{
            $export_type = $_POST['format'];
			$this->parkingtimereport_toexcell($id, $name, $host, $offset, $export_type);
			return;
		}

		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$json = json_decode($rowvehicle->vehicle_info);

		$tables = $this->gpsmodel->getTable($rowvehicle);
		$this->db = $this->load->database($tables["dbname"], TRUE);
		/*
		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;

		$t1 = $this->period1 - 7*3600;
		$t2 = $this->period2 - 7*3600;
		*/
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
		{
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
			$t1 = $this->period1 - 7*3600;
			$t2 = $this->period2 - 7*3600;
		}
		else
		{
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
			$t1 = $this->period1;
			$t2 = $this->period2;
		}

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"parkingtime_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo "Parking time: ".$_POST['hparkingtime']." h ".$_POST['mparkingtime']." m\r\n";
			echo "Periode: ".date("d/m/Y H:i:s", $this->period1)." to ".date("d/m/Y H:i:s", $this->period2)."\r\n\r\n";

			csvheader("no;date;time;position;coordinate;parkingtime", $this->config->item('csv_separator'));
		}

		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			$rows = $this->historymodel->all($tables["gps"], $name, $host, $t1, $t2, 0, 0, "ASC");
		}
		else
		{
			$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
			{

				//mix
				$rows = $this->historymodel->all($tables["gps"], $name, $host, $yesterday+1, $t2, 0, 0, "ASC");


				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);


				$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");

				$rows = array_merge($rows, $rowshist);
			}
			/* else if ($t2 > $yesterday && (isset($json->vehicle_ws)))
			{

				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows = $this->historymodel->all($tablehist, $name, $host, $yesterday+1, $t2, 0, 0, "ASC");

				$this->db = $this->load->database("gpshistory", TRUE);
				$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");
				$rows = array_merge($rows,$rowshist);

			} */
			else
			{
				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);


				$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");

				$rows = array_merge($rowshist,$rows);
			}
		}

		$this->db = $this->load->database($tables["dbname"], TRUE);

		$lastlng = "";
		$lastlat = "";

		$vehicles = array();
		$j = -1;
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);

			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
				$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
				$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				$speed = $rows[$i]->gps_speed*1.852;
			}else{
				$rows[$i]->gps_longitude_real = $rows[$i]->gps_longitude;
				$rows[$i]->gps_latitude_real = $rows[$i]->gps_latitude;
				$speed = $rows[$i]->gps_speed;
			}
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");

			$rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
			$rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);

			$rows[$i]->geofence = $this->getGeofence_location($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_device);
			//print_r($rows[$i]->geofence);exit;

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

		$max = $_POST['hparkingtime']*3600 + $_POST['mparkingtime']*60;;

		$temp = array();
		for($i=0; $i < count($vehicles); $i++)
		{
			if ($vehicles[$i]->parkingtime < $max) continue;

			$temp[] = $vehicles[$i];
		}

		if ($_POST['act'] != "export")
		{
			$total = count($temp);
			$vehicles = array_slice($temp, $offset, $limit);
		}
		else
		{
			$vehicles = $temp;
		}

		for($i=0; $i < count($vehicles); $i++)
		{
			$vehicles[$i]->georeverse = $this->gpsmodel->GeoReverse($vehicles[$i]->gps_latitude_real_fmt, $vehicles[$i]->gps_longitude_real_fmt);

			$vehicles[$i]->gpsindex = $i+1;
			$vehicles[$i]->gpsdate = date("d/m/Y", $vehicles[$i]->gps_timestamp+7*3600);
			$vehicles[$i]->gpstime = date("H:i:s", $vehicles[$i]->gps_timestamp+7*3600);
			$vehicles[$i]->gpsaddress = $vehicles[$i]->georeverse->display_name;
			$vehicles[$i]->gpscoord = "(".$vehicles[$i]->gps_longitude_real_fmt." ".$vehicles[$i]->gps_latitude_real_fmt.")";

		}

		if ($_POST['act'] == "export")
		{
			csvcontents(array("gpsindex", "gpsdate", "gpstime", "gpsaddress", "gpscoord", "parkingtime_fmt"), $vehicles, $this->config->item('csv_separator'));

			return;
		}

		// paging

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();

		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $vehicles;
		$params['id'] = $id;
		$html = $this->load->view("trackers/listsearch", $params, true);

		unset($callback);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

	}

	function searchoverspeed($id, $name, $host, $offset)
	{
		if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;

			}
		}


		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');

		// tentukan tanggal

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$json = json_decode($rowvehicle->vehicle_info);

		$tables = $this->gpsmodel->getTable($rowvehicle);

		$this->db = $this->load->database($tables["dbname"], TRUE);

		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
		{
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
			$t1 = $this->period1 - 7*3600;
			$t2 = $this->period2 - 7*3600;
		}
		else
		{
			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
			$t1 = $this->period1;
			$t2 = $this->period2;
		}

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"overspeed_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");

			echo "Speed limit: ".$_POST['speedlimit']." kph\r\n";
			echo "Periode: ".date("d/m/Y H:i:s", $this->period1)." to ".date("d/m/Y H:i:s", $this->period2)."\r\n\r\n";

			csvheader("no;date;time;position;coordinate;speed", $this->config->item('csv_separator'));
		}

		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			$rows = $this->historymodel->overspeed($tables["gps"], $name, $host, $_POST['speedlimit'], $t1, $t2, ($_POST['act'] == "export") ? 0 : $limit, $offset);
			if ($_POST['act'] != "export")
			{
				$total = $this->historymodel->overspeed($tables["gps"], $name, $host, $_POST['speedlimit'], $t1, $t2, -1);
			}
		}
		else
		{
			$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));

			if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
			{
				//mix

				$rows = $this->historymodel->overspeed($tables["gps"], $name, $host, $_POST['speedlimit'], $yesterday+1, $t2, 0);

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rowshist = $this->historymodel->overspeed($tablehist, $name, $host, $_POST['speedlimit'], $t1, $yesterday, 0);

				$rows = array_merge($rows, $rowshist);

				$total = count($rows);
				if ($_POST['act'] != "export")
				{
					$rows = array_slice($rows, $offset, $limit);
				}

			}
			else if ($t2 > $yesterday && (isset($json->vehicle_ws)))
			{
				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows = $this->historymodel->overspeed($tablehist, $name, $host, $_POST['speedlimit'], $yesterday+1, $t2, 0);

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rowshist = $this->historymodel->overspeed($tablehist, $name, $host, $_POST['speedlimit'], $t1, $yesterday, 0);

				$rows = array_merge($rows, $rowshist);

				$total = count($rows);
				if ($_POST['act'] != "export")
				{
					$rows = array_slice($rows, $offset, $limit);
				}
			}
			else
			{

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rows = $this->historymodel->overspeed($tablehist, $name, $host, $_POST['speedlimit'], $t1, $t2, ($_POST['act'] == "export") ? 0 : $limit, $offset);
				if ($_POST['act'] != "export")
				{
					$total = $this->historymodel->overspeed($tablehist, $name, $host, $_POST['speedlimit'], $t1, $t2, -1);
				}
			}
		}

		$this->db = $this->load->database($tables["dbname"], TRUE);

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
				$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
				$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}else{
				$rows[$i]->gps_longitude_real = $rows[$i]->gps_longitude;
				$rows[$i]->gps_latitude_real = $rows[$i]->gps_latitude;
			}
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");

			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
				$rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
				$rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
				$rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			}
			else
			{
				$rows[$i]->gps_date_fmt = date("d/m/Y", strtotime($rows[$i]->gps_time));
				$rows[$i]->gps_time_fmt = date("H:i:s", strtotime($rows[$i]->gps_time));
				$rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed, 0, "", ".");
			}
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

		if ($_POST['act'] == "export")
		{
			csvcontents(array("gpsindex", "gpsdate", "gpstime", "gpsaddress", "gps_speed_fmt"), $rows, $this->config->item('csv_separator'));
			return;
		}

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();
		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $rows;
		$params['id'] = $id;
		$html = $this->load->view("trackers/listsearch", $params, true);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);
	}

	function validate_overspeed()
	{
		$speedlimit = trim($_POST['speedlimit']);

		if (strlen($speedlimit) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_speed_limit");

			echo json_encode($callback);
			return false;
		}

		if (! is_numeric($speedlimit))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_speed_limit");

			echo json_encode($callback);
			return false;
		}

		$st1 = $_POST['period1']." ".sprintf("%02d", $_POST['hperiod1']).":".sprintf("%02d", $_POST['mperiod1']).":".sprintf("%02d", $_POST['speriod1']);
		$t1 = formmaketime($st1);

		// echo "<pre>";
    // var_dump(date("d/m/Y H:i:s", $t1).'||'.$st1);die();
    // echo "<pre>";

		if (date("d/m/Y H:i:s", $t1) != $st1)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_period_start");

			echo json_encode($callback);
			return false;
		}

		$this->period1 = $t1;

		$st2 = $_POST['period2']." ".sprintf("%02d", $_POST['hperiod2']).":".sprintf("%02d", $_POST['mperiod2']).":".sprintf("%02d", $_POST['speriod2']);
		$t2 = formmaketime($st2);

		if (date("d/m/Y H:i:s", $t2) != $st2)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_period_end");

			echo json_encode($callback);
			return false;
		}

		$this->period2 = $t2;

		$maxhist = $this->configmodel->getMaxHistory();
		$maxtime = mktime(0, 0, 0, date("n", $t1)+$maxhist, date('j', $t1), date('Y', $t1));

		if ($maxtime < $t2)
		{
			$callback['error'] = true;
			$callback['message'] = sprintf($this->lang->line("linvalid_max_history"), $maxhist);

			echo json_encode($callback);
			return false;
		}

		return true;
	}

	function validate_history()
	{
		$st1 = $_POST['period1']." ".sprintf("%02d", $_POST['hperiod1']).":".sprintf("%02d", $_POST['mperiod1']).":".sprintf("%02d", $_POST['speriod1']);
		$t1 = formmaketime($st1);

		if (date("d/m/Y H:i:s", $t1) != $st1)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_period_start");

			echo json_encode($callback);
			return false;
		}

		$this->period1 = $t1;

		$st2 = $_POST['period2']." ".sprintf("%02d", $_POST['hperiod2']).":".sprintf("%02d", $_POST['mperiod2']).":".sprintf("%02d", $_POST['speriod2']);
		$t2 = formmaketime($st2);

		if (date("d/m/Y H:i:s", $t2) != $st2)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_period_end");

			echo json_encode($callback);
			return false;
		}

		$this->period2 = $t2;

		$maxhist = $this->configmodel->getMaxHistory();
		$maxtime = mktime(0, 0, 0, date("n", $t1)+$maxhist, date('j', $t1), date('Y', $t1));

		if ($maxtime < $t2)
		{
			$callback['error'] = true;
			$callback['message'] = sprintf($this->lang->line("linvalid_max_history"), $maxhist);

			echo json_encode($callback);
			return false;
		}

		return true;
	}

	function validate_parkingtime()
	{
		$hparkingtime = trim($_POST['hparkingtime']);
		$mparkingtime = trim($_POST['mparkingtime']);

		if ((strlen($hparkingtime) > 0) &&  (! is_numeric($hparkingtime)))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_parkingtime_hour");

			echo json_encode($callback);
			return false;
		}

		if ((strlen($mparkingtime) > 0) &&  (! is_numeric($mparkingtime)))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_parkingtime_minute");

			echo json_encode($callback);
			return false;
		}

		$st1 = $_POST['period1']." ".sprintf("%02d", $_POST['hperiod1']).":".sprintf("%02d", $_POST['mperiod1']).":".sprintf("%02d", $_POST['speriod1']);
		$t1 = formmaketime($st1);

		if (date("d/m/Y H:i:s", $t1) != $st1)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_period_start");

			echo json_encode($callback);
			return false;
		}

		$this->period1 = $t1;

		$st2 = $_POST['period2']." ".sprintf("%02d", $_POST['hperiod2']).":".sprintf("%02d", $_POST['mperiod2']).":".sprintf("%02d", $_POST['speriod2']);
		$t2 = formmaketime($st2);

		if (date("d/m/Y H:i:s", $t2) != $st2)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("linvalid_period_end");

			echo json_encode($callback);
			return false;
		}

		$this->period2 = $t2;

		$maxhist = $this->configmodel->getMaxHistory();
		$maxtime = mktime(0, 0, 0, date("n", $t1)+$maxhist, date('j', $t1), date('Y', $t1));

		if ($maxtime < $t2)
		{
			$callback['error'] = true;
			$callback['message'] = sprintf($this->lang->line("linvalid_max_history"), $maxhist);

			echo json_encode($callback);
			return false;
		}

		return true;
	}

	function overspeed($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['speedlimit'] = isset($_POST['speedlimit']) ? $_POST['speedlimit'] : 80;

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		//$this->db->where("vehicle_device", $name.'@'.$host);
		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if (!$this->config->item("app_tupperware"))
		{
			if ($this->sess->user_type == 2)
			{
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $this->vehicleids);
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}

				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_group",422);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('loverspeed_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function parkingtime($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['hparkingtime'] = isset($_POST['hparkingtime']) ? $_POST['hparkingtime'] : "";
		$_POST['mparkingtime'] = isset($_POST['mparkingtime']) ? $_POST['mparkingtime'] : 30;

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		//$this->db->where("vehicle_device", $name.'@'.$host);

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if (!$this->config->item("app_tupperware"))
		{
			if ($this->sess->user_type == 2)
			{
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $this->vehicleids);
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}

				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_group",422);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lparking_time_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function workhour($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		//$this->db->where("vehicle_device", $name.'@'.$host);
		$this->db->where("vehicle_status <>", 3);

		if (!$this->config->item("app_tupperware"))
		{
			if ($this->sess->user_type == 2)
			{
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $this->vehicleids);
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}

				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_group",422);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lworkhour_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function door($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		//$this->db->where("vehicle_device", $name.'@'.$host);
		$this->db->where("vehicle_status <>", 3);
		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('ldoor_status').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function alarm($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_device", $name.'@'.$host);
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lalarm').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function engine($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		//$this->db->where("vehicle_device", $name.'@'.$host);
		$this->db->where("vehicle_status <>", 3);

		if (!$this->config->item("app_tupperware"))
		{
			if ($this->sess->user_type == 2)
			{
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $this->vehicleids);
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}

				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_group",422);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lengine_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function history($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		//$this->db->where("vehicle_device", $name.'@'.$host);

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if (!$this->config->item("app_tupperware"))
		{
			if ($this->sess->user_type == 2)
			{
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $this->vehicleids);
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}

				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_group",422);
		}


		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lhistory_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function pulse($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		//$this->db->where("vehicle_device", $name.'@'.$host);

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$this->db->where_in("vehicle_type", $this->config->item("vehicle_pulse"));
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lpulse_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function odometer($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		//$this->db->where("vehicle_device", $name.'@'.$host);

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lhistory_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function geofence($name, $host)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->distinct();
		$this->db->where("geofence_name <>", '');
		$this->db->where("geofence_vehicle", $name.'@'.$host);
		$q = $this->db->get("geofence");

		$rowgeofencenames = $q->result();

		$this->params['geofencenames'] = $rowgeofencenames;

		//$this->db->where("vehicle_device", $name.'@'.$host);

		$this->db->distinct();
		$this->db->select("geofence_vehicle");
		$q = $this->db->get("geofence");
		$rows = $q->result();

		$vdevices[] = "";
		for($i=0; $i < count($rows); $i++)
		{
			$vdevices[] = $rows[$i]->geofence_vehicle;
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_device", $vdevices);

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
				//break;
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lgeofence_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function searchgeofence($id, $name, $host, $offset, $geoname)
	{
		$geoname = $this->input->post('geoname');

        if (isset($_POST['format']))
		{
			switch($_POST['format'])
			{
				case "csv,":
					$this->config->config['csv_separator'] = ",";
				break;
				case "csv;":
					$this->config->config['csv_separator'] = ";";
				break;
			}
		}

		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');
		$geostatus = (isset($_POST['geostatus']) && $_POST['geostatus']) ? $_POST['geostatus'] : "";

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$tables = $this->gpsmodel->getTable($rowvehicle);

		//$this->db = $this->load->database($tables["dbname"], TRUE);
		$this->db = $this->load->database("default", TRUE);

		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;

		$t1 = $this->period1 - 7*3600;
		$t2 = $this->period2 - 7*3600;

		if ($_POST['act'] == "export")
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"geofence_".date("Ymd_His", $this->period1)."_to_".date("Ymd_His", $this->period1).".csv\"");
			if ($geostatus)
			{
				echo "Status: ".($_POST['geostatus'] == 2 ? $this->lang->line('lout') : $this->lang->line('lin'))."\r\n";
			}

			echo "Periode: ".date("d/m/Y H:i:s", $this->period1)." to ".date("d/m/Y H:i:s", $this->period2)."\r\n\r\n";

			echo "no";
			echo $this->config->item('csv_separator');
			echo "date";
			echo $this->config->item('csv_separator');
			echo "time";
			echo $this->config->item('csv_separator');
			echo "status";
			echo $this->config->item('csv_separator');
			echo "geofence";
			echo "\r\n";
		}


		//if ($t1 > $yesterday)
		if (true)
		{
			$rows = $this->historymodel->geofence($tables["gps"], $name, $host, $geostatus, $t1, $t2, $limit, $offset);
			$total = $this->historymodel->geofence($tables["gps"], $name, $host, $geostatus, $t1, $t2, -1);
		}
		else
		{
			$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));

			if ($t2 > $yesterday)
			{
				//mix

				$rows = $this->historymodel->geofence($tables["gps"], $name, $host, $geostatus, $yesterday+1, $t2, 0);

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
				$rowshist = $this->historymodel->geofence($tablehist, $name, $host, $geostatus, $t1, $yesterday, 0);

				$rows = array_merge($rows, $rowshist);

				$total = count($rows);
				if ($_POST['act'] != "export")
				{
					$rows = array_slice($rows, $offset, $limit);
				}

			}
			else
			{

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);

				$rows = $this->historymodel->geofence($tablehist, $name, $host, $geostatus, $t1, $t2, $limit, $offset);
				$total = $this->historymodel->geofence($tablehist, $name, $host, $geostatus, $t1, $t2, -1);
			}
		}

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);

			if ($_POST['act'] == "export" && ($_POST['format'] == "csv," || $_POST['format'] == "csv;"))
			{
				echo $i+1;
				echo $this->config->item('csv_separator').date("d/m/Y", $rows[$i]->geoalert_time_t);
				echo $this->config->item('csv_separator').date("H:i:s", $rows[$i]->geoalert_time_t);
				echo $this->config->item('csv_separator').($rows[$i]->geoalert_direction == 2 ? $this->lang->line('lout') : $this->lang->line('lin'));
				if ($rows[$i]->geofence_name)
				{
					echo $this->config->item('csv_separator').$rows[$i]->geofence_name;
				}
				else
				{
					echo $this->config->item('csv_separator').$rows[$i]->geofence_coordinate;
				}

				echo "\r\n";
			}
		}

		// paging

		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit;
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config);

		$params['paging'] = $this->pagination1->create_links();
		$params['geoname'] = $geoname;
		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $rows;
		$params['id'] = $id;
		$params['vehicle'] = $rowvehicle;

		$html = $this->load->view("trackers/listsearchgeofence", $params, true);

		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

	}

	function mangeofence($host, $name)
	{
		redirect(base_url()."geofence/manage/".$host."/".$name);
	}

	function animate($rows, $vehicle, $t1, $t2)
	{
		// select distinct

		$rows1 = array();

		$i = 0;
		while(1)
		{
			if ($i >= count($rows))
			{
				break;
			}

			$lng = $rows[$i]->gps_longitude_real_fmt;
			$lat = $rows[$i]->gps_latitude_real_fmt;

			if ($i == 0)
			{
				$latcenter = $lat;
				$lngcenter = $lng;
			}

			$rows1[$lat][$lng] = $rows[$i];

			$j = $i+1;
			while (1)
			{
				$i = $j;

				if ($j >= count($rows))
				{
					break;
				}

				if ($rows[$j]->gps_longitude_real_fmt != $lng)
				{
					$j++;
					break;
				}

				if ($rows[$j]->gps_latitude_real_fmt != $lat)
				{
					$j++;
					break;
				}

				$j++;
			}
		}


		$this->params["starttime"] = $t1;
		$this->params["endtime"] = $t2;
		$this->params["vehicle"] = $vehicle;
		$this->params["data"] = $rows1;
		$this->params["center"] = array($latcenter, $lngcenter);
		$this->params["content"] = $this->load->view("trackers/animate", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function menu($id, $name, $host)
	{
		$this->db->where("vehicle_device", $name."@".$host);
		$q = $this->db->get("vehicle");

		$row = $q->row();

		$this->params['vehicle'] = $row;
		$this->params["pid"] = $id;

		$html = $this->load->view("trackers/menu", $this->params, true);

		$callback['html'] = $html;

		echo json_encode($callback);
	}

	function fuel($name, $host){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$_POST['period1'] = isset($_POST['period1']) ? $_POST['period1'] : date("d/m/Y");
		$_POST['period2'] = isset($_POST['period2']) ? $_POST['period2'] : date("d/m/Y");

		$_POST['hperiod1'] = isset($_POST['hperiod1']) ? $_POST['hperiod1'] : 0;
		$_POST['mperiod1'] = isset($_POST['mperiod1']) ? $_POST['mperiod1'] : 0;
		$_POST['speriod1'] = isset($_POST['speriod1']) ? $_POST['speriod1'] : 1;

		$_POST['hperiod2'] = isset($_POST['hperiod2']) ? $_POST['hperiod2'] : 23;
		$_POST['mperiod2'] = isset($_POST['mperiod2']) ? $_POST['mperiod2'] : 59;
		$_POST['speriod2'] = isset($_POST['speriod2']) ? $_POST['speriod2'] : 59;

		//$this->db->where("vehicle_device", $name.'@'.$host);

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->vehicle_device1 = str_replace("@", "/", $rows[$i]->vehicle_device);
			if ($rows[$i]->vehicle_device == sprintf("%s@%s", $name, $host))
			{
				$row = $rows[$i];
			}
		}

		if (! isset($row))
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lfuel_report').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["vehicle"] = $row;
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('trackers/overspeed', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function get_fuel($resistance, $capacity){
		if ($resistance=="")
		{
			return $resistance;
		}

		$this->db = $this->load->database("default", true);

		if ($capacity == 300)
		{
			$this->db->limit(1);
			$this->db->order_by("fuel_led_resistance", "DESC");
			$this->db->where("fuel_tank_capacity", $capacity);
			$this->db->where("fuel_led_resistance <=", $resistance);
			$q = $this->db->get("fuel");

			if ($q->num_rows() == 0) return "";

			$row = $q->row();
			return sprintf("%sL - %sL", $row->fuel_volume, $row->fuel_volume);
		}

		$this->db->limit(1);
		$this->db->order_by("fuel_led_resistance", "ASC");
		$this->db->where("fuel_tank_capacity", $capacity);
		$this->db->where("fuel_led_resistance >=", $resistance);
		$q = $this->db->get("fuel");

		if ($q->num_rows() > 0)
		{
			$row = $q->row();
			return sprintf("%sL - %sL", $row->fuel_volume, $row->fuel_volume);
		}

		$this->db->limit(1);
		$this->db->order_by("fuel_led_resistance", "DESC");
		$this->db->where("fuel_tank_capacity", $capacity);
		$this->db->where("fuel_led_resistance <=", $resistance);
		$q = $this->db->get("fuel");

		if ($q->num_rows() == 0) return "";

		$row = $q->row();
		return sprintf("%sL - %sL", $row->fuel_volume, $row->fuel_volume);
	}

	function vehicle_location($field, $keyword, $sortby, $orderby)
	{
		if ($keyword != "")
		{
			$keyword = strtoupper($keyword);
		}
		else
		{
			$keyword = "KIM";
		}

		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		//print_r($rows);exit;

		//Get Location
		foreach($rows as $row)
		{
			$device = $row->vehicle_device;
			$lasttime = 0;
			$arr = explode("@", $device);

			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";

			//Seleksi Database;
			if ($row->vehicle_info)
			{
				$json = json_decode($row->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($row->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($row->vehicle_type);
			}

			// ambil informasi di gps_info
			$this->db->order_by("gps_info_time", "DESC");
			$this->db->where("gps_info_device", $device);
			$q = $this->db->get($tableinfo, 1, 0);

			$arr = explode("@", $device);

			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";

			$v_location = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);
			if(isset($v_location->georeverse->display_name))
			{
				$get_location = $v_location->georeverse->display_name;
			}
			else
			{
				$get_location = "";
			}
			//print_r($v_location->georeverse->display_name);exit;
			$pos = strpos($get_location, $keyword);
			if ($pos == true)
			{

				$data[] = $row->vehicle_device;
			}
		}
		if (count($data >= 0))
		{
			return $data;
		}
		else
		{
			$data = 0;
			return $data;
		}
	}

    function getGeofence_location($longitude, $latitude, $vehicle_device) {
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

	function get_id_booking($v)
	{
		if ($v == "")
		{
			$data = 0;
			return $data;
		}

		$this->dbtrans = $this->load->database("transporter",true);
		$mycompany = $this->sess->user_company;
		$this->dbtrans->select("booking_vehicle");
		$this->dbtrans->where("booking_id LIKE '%".$v."%'",null);

		if ($this->sess->user_trans_tupper == 1)
		{
			$this->dbtrans->where("booking_company",$mycompany);
		}

		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status <>",3);
		$qb = $this->dbtrans->get("id_booking");
		$rb = $qb->result();
		$total = count($rb);

		if ($total>0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$data[] = $rb[$i]->booking_vehicle;
			}
			return $data;
		}

		if ($total==0)
		{
			$data = 0;
			return $data;
		}


	}

	function get_booking_loading($v)
	{
		if ($v == "")
		{
			$data = 0;
			return $data;
		}

		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->select("booking_vehicle");
		$this->dbtrans->where("booking_loading",$v);

		if ($this->sess->user_trans_tupper == 1)
		{
			$this->dbtrans->where("booking_company",$mycompany);
		}

		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status <>",3);
		$qb = $this->dbtrans->get("id_booking");
		$rb = $qb->result();
		$total = count($rb);

		if ($total>0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$data[] = $rb[$i]->booking_vehicle;
			}
			return $data;
		}

		if ($total==0)
		{
			$data = 0;
			return $data;
		}


	}

	function get_noso($v)
	{
		if ($v == "")
		{
			$data = 0;
			return $data;
		}

		$this->dbtrans = $this->load->database("transporter",true);
		$mycompany = $this->sess->user_company;

		$this->dbtrans->join("id_booking","booking_id = transporter_dr_booking_id", "left_outer");
		$this->dbtrans->where("transporter_dr_so LIKE '%".$v."%'",null);
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status <>",3);
		$qb = $this->dbtrans->get("tupper_dr");
		$rb = $qb->result();

		$total = count($rb);

		if ($total>0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$data[] = $rb[$i]->booking_vehicle;
			}
			return $data;
		}

		if ($total==0)
		{
			$data = 0;
			return $data;
		}
	}

	function get_nodr($v)
	{
		if ($v == "")
		{
			$data = 0;
			return $data;
		}

		$this->dbtrans = $this->load->database("transporter",true);
		$mycompany = $this->sess->user_company;

		$this->dbtrans->join("id_booking","booking_id = transporter_dr_booking_id", "left_outer");
		$this->dbtrans->where("transporter_dr_dr LIKE '%".$v."%'",null);
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status <>",3);
		$qb = $this->dbtrans->get("tupper_dr");
		$rb = $qb->result();

		$total = count($rb);

		if ($total>0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$data[] = $rb[$i]->booking_vehicle;
			}
			return $data;
		}

		if ($total==0)
		{
			$data = 0;
			return $data;
		}
	}

	function smartviewtest()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$vtype = isset($_POST['vehicle_type']) ? $_POST['vehicle_type'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "user_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$companyid = isset($_POST['company']) ? $_POST['company'] : 0;
		$groupid = isset($_POST['group']) ? $_POST['group'] : 0;
		$server = isset($_POST['server']) ? $_POST['server'] : "";
		$membership = isset($_POST['membership']) ? $_POST['membership'] : "";
		$branch = isset($_POST['branch_office']) ? $_POST['branch_office'] : "";
		$booking_loading = isset($_POST['booking_loading']) ? $_POST['booking_loading'] : 0;

		if(! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		if ($field == "location")
		{
			$bylocation = $this->vehicle_location($field, $keyword, $sortby, $orderby);
		}

		if ($field == "id_booking")
		{
			$bybooking = $this->get_id_booking($keyword);
		}

		if ($field == "booking_loading")
		{
			$bybooking_loading = $this->get_booking_loading($booking_loading);
		}


		if (substr($field, 0, strlen("delayed")) == "delayed")
		{
			$delayed = substr($field, strlen("delayed"));
			$delayeds = explode("_", $delayed);
			$field = "delayed";

			$vdelayeds = $this->getVehiclesDelayed($delayeds[0]*60, $delayeds[1]*60);
		}

		if ($field == "user_company")
		{
			if ($groupid)
			{
				$groups[] = $groupid;
				$groupids = $this->vehiclemodel->getChildIds($groupid, $groups);
			}
		}

		if (($this->sess->user_type != 2) || (($this->sess->user_type == 2) && ($this->sess->user_company > 0) && ($this->sess->user_group == 0)))
		{
			$this->db->order_by("company_name", "asc");
			if ($this->sess->user_type == 3)
			{
				$this->db->where("company_agent", $this->sess->user_agent);
			}

			$q = $this->db->get("company");
			$this->params['companies'] = $q->result();
		}


		switch($this->sess->user_type)
		{
			case 2:
				if (isset($this->vehicleids))
				{
					if ($field != "vehicletrans" && $field != "id_booking" && $field != "booking_loading")
					{
						$this->db->where_in("vehicle_id", $this->vehicleids);
					}

					if ($this->config->item("app_tupperware"))
					{
						if ($keyword == "" || $field == "vehicletrans")
						{
							$this->db->or_where("vehicle_group",422);
						}
					}
				}
				else
				{
					if ($field != "vehicletrans" && $field != "id_booking" && $field != "booking_loading")
					{
						$this->db->where("vehicle_user_id", $this->sess->user_id);
					}

					if ($this->config->item("app_tupperware"))
					{
						if ($keyword == "" || $field == "vehicletrans")
						{
							$this->db->or_where("vehicle_group",422);
						}
					}
				}
			break;
			case 3:
				$this->db->where("user_agent", $this->sess->user_agent);
			break;
		}

		if ($this->config->item("app_tupperware"))
		{
			$this->db->order_by("vehicle_user_id", "desc");
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->order_by("vehicle_company", "asc");


		if ($sortby == "user_name")
		{
			$this->db->order_by($sortby, $orderby);
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
		}
		else
		if ($sortby == "vehicle_name")
		{
			$this->db->order_by("vehicle_name", $orderby);
			$this->db->order_by("vehicle_no", $orderby);
			$this->db->order_by("user_name", $orderby);
		}
		else
		{
			$this->db->order_by($sortby, $orderby);
		}


		if ($this->sess->user_type == 2)
		{
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}

		$ssi_app = $this->config->item("ssi_app");
		if (isset($ssi_app) && ($ssi_app == 1))
		{
			if($this->sess->user_type == 5 && $this->sess->user_id == 2288){
				//1933 = user PT SSI
				// 2288 = user all area monitoring ssi
				$this->db->where("vehicle_user_id", 1933);
			}
		}

		switch ($field)
		{
			case "user_company":
				if ($groupid)
				{
					$this->db->where_in("vehicle_group", $groups);
				}
				else
				{
					$this->db->where("vehicle_company", $companyid);
				}
			break;
			case "user_agent":
				$this->db->where("agent_name LIKE '%".$keyword."%'", null);
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
			case "vehicletrans":
				$this->db->where("(UPPER(vehicle_no) LIKE '%".strtoupper($keyword)."%' OR UPPER(vehicle_name) LIKE '%".strtoupper($keyword)."%')", null);
			break;
			case "device":
				$this->db->where("vehicle_device like", '%'.$keyword.'%');
			break;
			case "delayed":
				$this->db->where_in("vehicle_id", $vdelayeds);
			break;
			case "server":
				$this->db->where("vehicle_info LIKE '%\"vehicle_ip\":\"".$server."\"%'", null);
			break;
			case "location":
				$this->db->where_in("vehicle_device", $bylocation);
			break;
			case "branch":
				$this->db->where("vehicle_company", $branch);
			break;
			case "membership":
				$this->db->where("user_agent", "1");
				if ($membership == 1)
				{
					$this->db->where("user_payment_type", $membership);
					$this->db->where("user_payment_period <", "12");
				}
				else
				{
					$this->db->where("user_payment_period >=", "12");
				}

			break;
			case "vehicle_type":
				$vtypes[] = $vtype;

				$vreplaces = $this->config->item('vehicle_type_replace');
				if (isset($vreplaces[$vtype]))
				{
					//$vtypes[] = $vreplaces[$vtype];
				}

				if ($vtype == 'T1')
				{
					//$vtypes[] = "";
				}

				$this->db->where_in("vehicle_type", $vtypes);
			break;
			case "id_booking":
				if ($bybooking == 0)
				{
					$this->db->where("vehicle_device","xxxxxxxxxxxxxxxxxx");
				}
				else
				{
					$this->db->where_in("vehicle_device",$bybooking);
				}
			break;
			case "booking_loading":
				if ($bybooking_loading == 0)
				{
					$this->db->where("vehicle_device","xxxxxxxxxxxxxxxxxx");
				}
				else
				{
					$this->db->where_in("vehicle_device",$bybooking_loading);
				}
			break;
			default:
				if ($this->sess->user_type == 1)
				{
					if (! isset($_POST['btnsearch']))
					{
						$this->db->where("1 = 0", null, false);
					}
				}

				if ($field)
				{
					$this->db->where("UPPER(".$field.") LIKE", "%".strtoupper($keyword)."%");
				}
				$app_tupperware = $this->config->item("app_tupperware");
				if (isset($app_tupperware) && ($app_tupperware == 1))
				{
					//422 = Group Tupperware
					$this->db->or_where("vehicle_group",422);
				}
		}

		if (isset($_POST['vehicle']))
		{
			$this->db->where_in("vehicle_id", $_POST['vehicle']);
		}

		$this->db->join("user", "vehicle_user_id = user_id");

		if ($field == "user_agent")
		{
			$this->db->join("agent", "user_agent = agent_id");
		}

		if ($this->config->item('vehicle_type_fixed'))
		{
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$allowed_vtype = $this->config->item('allowed_vtype');
		if ($allowed_vtype && is_array($allowed_vtype) && count($allowed_vtype))
		{
			$this->db->where_in("vehicle_type", $allowed_vtype);
		}

		$site = $this->config->item('site');
		if ($site)
		{
			$this->db->where("vehicle_site", $site);
		}



		$this->db->where("vehicle_status <>", 3);

		$q = $this->db->get("vehicle");

		$rows = $q->result();
		//print_r($rows);exit;


		for($i=0; $i < count($rows); $i++)
		{
			if (isset($vehicles[$rows[$i]->vehicle_device]))
			{
				if ($rows[$i]->vehicle_id < $vehicles[$rows[$i]->vehicle_device])
				{
					$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
					continue;
				}
			}
			$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
		}

		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);

			$rows[$i]->vehicle_id = $vehicles[$rows[$i]->vehicle_device];
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
		}

		if ($field)
		{
			$this->params['title'] = $this->lang->line('lvehicle_tracker')." (".$field."=".$keyword.") ";
		}
		else
		{
			$this->params['title'] = $this->lang->line('lvehicle_tracker');
		}

		//Get Group
		if (isset($this->sess->user_company)&&($this->sess->user_group == 0))
		{
			$branch_off = $this->sess->user_id;
			$this->db->where("company_created_by",$branch_off);
			$q_comp = $this->db->get("company");
			$row_comp = $q_comp->result();
			$this->params['branch'] = $row_comp;

		}

		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		$this->params["data"] = $rows;
		$this->params["initmap"] = $this->load->view('initmapsmartview', $this->params, true);
		$this->params["updateinfo"] =  $this->load->view('updateinfosmartview', $this->params, true);
		$callback["html"] = $this->load->view('trackers/listsmart', $this->params, true);

		echo json_encode($callback);

	}

	function followme()
	{
		if (! $this->sess)
		{
			redirect(base_url());
		}

		$vehicle = isset($_POST['slfollow']) ? $_POST['slfollow'] : "";
		$this->db->where("vehicle_id",$vehicle);
		$qmy = $this->db->get("vehicle");
		$rmy = $qmy->row();

		$myvehicle = $rmy->vehicle_device;
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $myvehicle);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		$this->params['title'] = $this->lang->line('ltracker').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["ishistory"] = "off";
		$this->params["zoom"] = $this->config->item("zoom_realtime");
		$this->params["data"] = $row;
		$this->params["initmap"] = $this->load->view('initmapsmartview', $this->params, true);
		$this->params["updateinfo_realtime"] = $this->load->view('updateinfosmartview_realtime', $this->params, true);

		$callback["html"] = $this->load->view('map/realtimesmartview', $this->params, true);

		echo json_encode($callback);
	}

	function getFanStatus($val)
	{
		//$val = "(000000001271BP05000000000001271120804A0617.4940S10657.9536E000.004514179.73001100000L00000000";
		$totstring = strlen($val);
		$value = substr($val, 79, 1);
		//print_r($value);
		return($value);
	}

	function getDoorStatus($val)
	{
		//0 = close, else open
		$val_new = json_decode($val);
		$value = hexdec($val_new[9]);

		return($value);
	}

	function parkingtimereport_toexcell ($id, $name, $host, $offset, $export_type) {

        $limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
        $vehicle_nopol = $rowvehicle->vehicle_no;

		$json = json_decode($rowvehicle->vehicle_info);

		$tables = $this->gpsmodel->getTable($rowvehicle);
		$this->db = $this->load->database($tables["dbname"], TRUE);

		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;

		$t1 = $this->period1 - 7*3600;
		$t2 = $this->period2 - 7*3600;

		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			$rows = $this->historymodel->all($tables["gps"], $name, $host, $t1, $t2, 0, 0, "ASC");
		}
		else
		{
			$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
			{
				//mix
				$rows = $this->historymodel->all($tables["gps"], $name, $host, $yesterday+1, $t2, 0, 0, "ASC");

				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
				$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");

				$rows = array_merge($rows, $rowshist);
			}
			/* else if ($t2 > $yesterday && (isset($json->vehicle_ws)))
			{

				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows = $this->historymodel->all($tablehist, $name, $host, $yesterday+1, $t2, 0, 0, "ASC");

				$this->db = $this->load->database("gpshistory", TRUE);
				$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");
				$rows = array_merge($rows,$rowshist);

			} */
			else
			{
				$this->db = $this->load->database("gpshistory2", TRUE);
				$rows = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");
				$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
				$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0, 0, "ASC");

				$rows = array_merge($rowshist,$rows);
			}
		}

		$this->db = $this->load->database($tables["dbname"], TRUE);

		$lastlng = "";
		$lastlat = "";

        //Export
        /** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

            if ($export_type == "excell") {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment:filename="ParkingTime_'.date("Ymd_His").'.xls"');
            } else {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment:filename="ParkingTime_'.date("Ymd_His").'.pdf"');
            }
            header('Cache-Control: no-cache, must-revalidate');

			// Set properties
			$objPHPExcel->getProperties()->setTitle("ParkingTime Report");
			$objPHPExcel->getProperties()->setSubject("ParkingTime Report");
			$objPHPExcel->getProperties()->setDescription("ParkingTime Report");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);


			//Header

            $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ParkingTime' . " " . $vehicle_nopol);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);

            if ($export_type == "excell") {
			//$objPHPExcel->getActiveSheet()->SetCellValue('E3', 'Total Record :');
			//$objPHPExcel->getActiveSheet()->SetCellValue('F3', count($rows));
            }

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Parking Time');
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


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

		$max = $_POST['hparkingtime']*3600 + $_POST['mparkingtime']*60;;

		$temp = array();
		for($i=0; $i < count($vehicles); $i++)
		{
			if ($vehicles[$i]->parkingtime < $max) continue;

			$temp[] = $vehicles[$i];
		}

			$total = count($temp);
			$vehicles = array_slice($temp, $offset, $limit);


		for($i=0; $i < count($vehicles); $i++)
		{
			$vehicles[$i]->georeverse = $this->gpsmodel->GeoReverse($vehicles[$i]->gps_latitude_real_fmt, $vehicles[$i]->gps_longitude_real_fmt);

            //excell value
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $vehicles[$i]->gps_timestamp+7*3600));
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $vehicles[$i]->gps_timestamp+7*3600));
			if (isset($vehicles[$i]->geofence))
			{
				$a = "Geofence :"." ".$vehicles[$i]->geofence.","." ".$vehicles[$i]->georeverse->display_name;
			}
			else
			{
				$a = $vehicles[$i]->georeverse->display_name;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $a);

			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $vehicles[$i]->gps_latitude_real_fmt .",".$vehicles[$i]->gps_longitude_real_fmt);

			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $vehicles[$i]->parkingtime_fmt);
		}

		$styleArray = array(
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_THIN
						    )
						  )
						);

        $styleArray_pdf = array(
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_THICK
						    )
						  )
						);

        if ($export_type == "excell") {

        $objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->getAlignment()->setWrapText(true);
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

        } else {

        $objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->applyFromArray($styleArray_pdf);
        $objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->getAlignment()->setWrapText(true);
        $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
        }

        $objWriter->save('php://output');
        return;

    } //end function  parkingtime report to excel

	//destination
	function by_destination($value)
    {
        $this->dbtrans = $this->load->database("transporter",true);
        $this->dbtrans->where("destination_name1 LIKE '%".$value."%'", null);
        $qdestin = $this->dbtrans->get('destination_reksa');
        $rowdestination = $qdestin->result();

        if(count($rowdestination)>0)
        {
            for($i=0;$i<count($rowdestination);$i++)
            {
                $vid = $rowdestination[$i]->destination_vehicle;
                $this->db->select("vehicle_id, vehicle_device");
                $this->db->where("vehicle_id",$vid);
                $this->db->limit(1);
                $qv = $this->db->get('vehicle');
                $rv = $qv->row();
                if(count($rv)>0)
                {
                    $data[] = $rv->vehicle_id;
                }
            }
        }
        if (count($data > 0))
        {
            return $data;
        }
        else
        {
            $data = 0;
            return $data;
        }
    }
	//end


}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
