<?php
include "base.php";

class Dashboard extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
	}

	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}

		//get vehicle
		$data = $this->dashboardmodel->getvehicle_byowner();

		//get company
		$company  = $this->dashboardmodel->getcompany_byowner();
		$rcompany = $this->dashboardmodel->getcompany_name();
		$rstatus  = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$rspeed   = $this->dashboardmodel->gettotalspeed($this->sess->user_id);

		$this->params['data']           = $data;
		$this->params['company']        = $company;
		$this->params['rcompany']       = $rcompany;
		$this->params['rstatus']        = $rstatus;
		$this->params['rspeed']         = $rspeed;
		$this->params['code_view_menu'] = "monitor";

		// echo "<pre>";
		// var_dump($this->params['resultexpired']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/vdashboard', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	//map
	function area()
	{redirect(base_url());
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}
		$id = $this->uri->segment(3);

		//get vehicle
		$data = $this->dashboardmodel->getvehicle_bycompany($id);
		$companydata = $this->dashboardmodel->getcompany_id($id);
		$rcompany = $this->dashboardmodel->getcompany_name();

		$this->params['companydata'] = $companydata;
		$this->params['rcompany']    = $rcompany;
		$this->params['data']        = $data;
		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/map/area_detail', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);

	}

	function maparea()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}
		$id = $this->uri->segment(3);
		$sortby = "vehicle_no";
		$orderby = "asc";

		$companydata = $this->dashboardmodel->getcompany_id($id);
		$rcompany = $this->dashboardmodel->getcompany_name();

		//
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_company", $id);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");

		$rows = $q->result();

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

		$this->params['title'] = "SHOW MAP";
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		$this->params["data"] = $rows;
		$this->params['companydata'] = $companydata;

		$this->params["header"] =  $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] =  $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] =  $this->load->view('dashboard/chatsidebar', $this->params, true);

		//$this->params["content"] = $this->load->view('dashboard/map/vmap', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/map/vmap_area', $this->params, true);
		$this->params["initmap"] = $this->load->view('dashboard/map/initmapdashboard', $this->params, true);
		$this->params["updateinfo"] =  $this->load->view('dashboard/map/updateinfofollowme', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);

	}

	function maparea_page($area)
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$vtype = isset($_POST['vehicle_type']) ? $_POST['vehicle_type'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_no";
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
		$this->db->order_by("vehicle_no", "asc");

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


		$this->db->where("vehicle_company", $area);
		$this->db->where("vehicle_status <>", 3);

		$q = $this->db->get("vehicle");

		$rows = $q->result();

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
		$this->params["initmap"] = $this->load->view('dashboard/map/initmapdashboard', $this->params, true);
		$this->params["updateinfo"] =  $this->load->view('dashboard/map/updateinfofollowme', $this->params, true);
		$callback["html"] = $this->load->view('dashboard/map/listmaparea', $this->params, true);

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
		$this->params["initmap"] = $this->load->view('dashboard/map/initmapdashboard', $this->params, true);
		$this->params["updateinfo_realtime"] = $this->load->view('dashboard/map/updateinfofollowme', $this->params, true);
		$callback["html"] = $this->load->view('dashboard/map/followmeview', $this->params, true);

		echo json_encode($callback);
	}

	//report
	function summary(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}
		$id = $this->uri->segment(3);
		if($id == "all99"){
			$data = $this->dashboardmodel->getvehicle_byowner();
		}else{
			$data = $this->dashboardmodel->getvehicle_bycompany($id);
		}
		$rcompany = $this->dashboardmodel->getcompany_name();
		$companydata = $this->dashboardmodel->getcompany_id($id);

		$first = "01";
		$month_name = date("F");
		$month = date("m");
		$year = date("Y");
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$lastday = date("Y-m-t");
		$report = "operasional_";

		switch ($month_name)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}

		$firstdatetime = date("Y-m-d H:i:s",strtotime($year."-".$month."-".$first." ".$shour));
		$lastdatetime = date("Y-m-t H:i:s",strtotime($lastday." ".$ehour));

		$this->params['sdate'] = $firstdatetime;
		$this->params['edate'] = $lastdatetime;
		$this->params['rcompany'] = $rcompany;
		$this->params['companydata'] = $companydata;
		$this->params['dbtable'] = $dbtable;

		$this->params['data'] = $data;
		$this->params["header"] =  $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] =  $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] =  $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"] =  $this->load->view('dashboard/report/vsummary_thismonth', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);

	}

	function operational()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level != 1)
		{
			redirect(base_url());
		}

		$rows = $this->dashboardmodel->getvehicle_report();
		$rows_company = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/voperational_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function dataoperational()
	{
		ini_set('display_errors', 1);

		//print_r("DISINI");exit();
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$engine = $this->input->post("engine");
		$view_map = $this->input->post("view_map");
		$location_start = $this->input->post("location_start");
		$location_end = $this->input->post("location_end");
		$startdur = $this->input->post("s_minute");
		$enddur = $this->input->post("e_minute");
		$km_start = $this->input->post("km_start");
		$km_end = $this->input->post("km_end");

		$type_location = $this->input->post("type_location");
		$type_duration = $this->input->post("type_duration");
		$type_km = $this->input->post("type_km");

		if($startdur != "" && $enddur != ""){
			$startdur = $startdur * 60;
			$enddur = $enddur * 60;
		}

		$report = "operasional_";
		$report_sum = "summary_";

		$totalduration = 0;
		$totalcummulative = 0;
		$totalcummulative_on = 0;
		$totalcummulative_off = 0;
		$totaldatagps = 0;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		$error = "";
		$rows_summary = "";


		if ($vehicle == "" || $vehicle == 0)
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_sum = $report_sum."desember_".$year;
			break;
		}

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			$dbtable2_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			$dbtable2_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			$dbtable2_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			$dbtable2_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			$dbtable2_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			$dbtable2_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			$dbtable2_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			$dbtable2_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			$dbtable2_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			$dbtable2_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			$dbtable2_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			$dbtable2_sum = $report_sum."desember_".$year;
			break;
		}

		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		$rv = $qv->row();
		//end get vehicle

		//get data operasional
		if(count($rv)>0){
				$this->dbtrip = $this->load->database("operational_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				if($type_location == "1"){
					if($location_start != ""){
						$this->dbtrip->like("trip_mileage_location_start", $location_start);
					}
					if($location_end != ""){
						$this->dbtrip->like("trip_mileage_location_end", $location_end);
					}
				}
				if($type_duration == "1"){
					if($startdur != "" && $enddur != ""){
						$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
						$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
					}
				}
				if($type_km == "1"){
					if($km_start != "" && $km_end != ""){
						$this->dbtrip->where("trip_mileage_trip_mileage >=", $km_start);
						$this->dbtrip->where("trip_mileage_trip_mileage <=", $km_end);
					}
				}

				$q = $this->dbtrip->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
				}



			//totaldur
			//total cumm km
			$totalcumm = 0;
			$totalcumm_on = 0;
			$totalcumm_off = 0;
			$totaldur = 0;
			$totaldatagps = "-";
			if($m1 != $m2)
			{
				for($i=0; $i < count($rowsall); $i++)
				{
					if($rowsall[$i]->trip_mileage_engine == 1 ){
						$totalcumm_on += $rowsall[$i]->trip_mileage_trip_mileage;
					}
					if($rowsall[$i]->trip_mileage_engine == 0 ){
						$totalcumm_off += $rowsall[$i]->trip_mileage_trip_mileage;
					}

					$totalcumm += $rowsall[$i]->trip_mileage_trip_mileage;
					$totaldur += $rowsall[$i]->trip_mileage_duration_sec;

				}
			}
			else
			{
				for($i=0; $i < count($rows); $i++)
				{
					if($rows[$i]->trip_mileage_engine == 1 ){
						$totalcumm_on += $rows[$i]->trip_mileage_trip_mileage;
						$totaldur += $rows[$i]->trip_mileage_duration_sec;
					}
					if($rows[$i]->trip_mileage_engine == 0 ){
						$totalcumm_off += $rows[$i]->trip_mileage_trip_mileage;
					}

					$totalcumm += $rows[$i]->trip_mileage_trip_mileage;
					$totaldatagps = $rows[$i]->trip_mileage_totaldata;
				}

			}

			$totalcummulative = $totalcumm;
			$totalcummulative_on = $totalcumm_on;
			$totalcummulative_off = $totalcumm_off;
			$totalduration = $totaldur;

		}


		if($m1 != $m2)
		{
			$params['data'] = $rowsall;

		}
		else
		{
			$params['data'] = $rows;

		}

		$params['vehicle'] = $rv;
		$params['dbtable'] = $dbtable;
		$params['dbtable_sum'] = $dbtable_sum;

		$params['startdate'] = $startdate;
		$params['enddate'] = $enddate;

		$params['km_start'] = $km_start;
		$params['km_end'] = $km_end;

		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
		$params['totaldatagps'] = $totaldatagps;

			$html = $this->load->view("dashboard/report/voperational_report_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

	}

	function dataoperational_map()
	{
		ini_set('display_errors', 1);

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$engine = "1";
		$view_map = $this->input->post("view_map");
		$location_start = $this->input->post("location_start");
		$location_end = $this->input->post("location_end");
		$startdur = $this->input->post("s_minute");
		$enddur = $this->input->post("e_minute");
		$km_start = 0.2;
		$km_end = $this->input->post("km_end");

		$type_location = $this->input->post("type_location");
		$type_duration = $this->input->post("type_duration");
		$type_km = $this->input->post("type_km");

		if($startdur != "" && $enddur != ""){
			$startdur = $startdur * 60;
			$enddur = $enddur * 60;
		}

		$report = "operasional_";
		$report_sum = "summary_";

		$totalduration = 0;
		$totalcummulative = 0;
		$totalcummulative_on = 0;
		$totalcummulative_off = 0;
		$totaldatagps = 0;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		$error = "";
		$rows_summary = "";


		if ($vehicle == "" || $vehicle == 0)
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_sum = $report_sum."desember_".$year;
			break;
		}

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			$dbtable2_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			$dbtable2_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			$dbtable2_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			$dbtable2_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			$dbtable2_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			$dbtable2_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			$dbtable2_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			$dbtable2_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			$dbtable2_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			$dbtable2_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			$dbtable2_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			$dbtable2_sum = $report_sum."desember_".$year;
			break;
		}

		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		$rv = $qv->row();
		//end get vehicle


		//get data operasional
		if(count($rv)>0){
				$this->dbtrip = $this->load->database("operational_report",true);
				$this->dbtrip->limit(20);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				$this->dbtrip->where("trip_mileage_engine", $engine); // on
				$this->dbtrip->where("trip_mileage_trip_mileage >=", $km_start); // >= 1KM

				$q = $this->dbtrip->get($dbtable);
				if ($q->num_rows>0)
				{
					$rows = $q->result();
				}

			/*
			//print_r(count($rows));exit();
			//totaldur
			//total cumm km
			$totalcumm = 0;
			$totalcumm_on = 0;
			$totalcumm_off = 0;
			$totaldur = 0;
			$totaldatagps = "-";
			if($m1 != $m2)
			{
				for($i=0; $i < count($rowsall); $i++)
				{
					if($rowsall[$i]->trip_mileage_engine == 1 ){
						$totalcumm_on += $rowsall[$i]->trip_mileage_trip_mileage;
					}
					if($rowsall[$i]->trip_mileage_engine == 0 ){
						$totalcumm_off += $rowsall[$i]->trip_mileage_trip_mileage;
					}

					$totalcumm += $rowsall[$i]->trip_mileage_trip_mileage;
					$totaldur += $rowsall[$i]->trip_mileage_duration_sec;

				}
			}
			else
			{
				for($i=0; $i < count($rows); $i++)
				{
					if($rows[$i]->trip_mileage_engine == 1 ){
						$totalcumm_on += $rows[$i]->trip_mileage_trip_mileage;
						$totaldur += $rows[$i]->trip_mileage_duration_sec;
					}
					if($rows[$i]->trip_mileage_engine == 0 ){
						$totalcumm_off += $rows[$i]->trip_mileage_trip_mileage;
					}

					$totalcumm += $rows[$i]->trip_mileage_trip_mileage;
					$totaldatagps = $rows[$i]->trip_mileage_totaldata;
				}

			}

			$totalcummulative = $totalcumm;
			$totalcummulative_on = $totalcumm_on;
			$totalcummulative_off = $totalcumm_off;
			$totalduration = $totaldur;
			*/
		}

		$params['data'] = $rows;
		$params['vehicle'] = $rv;
		$params['dbtable'] = $dbtable;
		$params['dbtable_sum'] = $dbtable_sum;

		$params['startdate'] = $startdate;
		$params['enddate'] = $enddate;

		$params['km_start'] = $km_start;
		$params['km_end'] = $km_end;

		/*$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
		$params['totaldatagps'] = $totaldatagps;*/

		$html = $this->load->view("dashboard/report/voperational_report_map", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

	}

	function get_vehicle_by_company($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		if($this->sess->user_group > 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>",3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if($qd->num_rows() > 0){
			$options = "<option value='0' selected='selected' >--Select Vehicle--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_device . "'>". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
			}

			echo $options;
			return;
		}
	}

	function get_vehicle_by_company_with_numberorder($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name, vehicle_mv03");
			if ($id != "all") {
				$this->db->where("vehicle_company", $id);
			}
		if($this->sess->user_group > 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>",3);
		$this->db->where("vehicle_mv03 !=", "0000");
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if($qd->num_rows() > 0){
			$options = "<option value='all' selected='selected' >--Select Vehicle--</option>";
			$i = 1;
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_mv03 . "'>".$i.". ". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
				$i++;
			}

			echo $options;
			return;
		}
	}

}
