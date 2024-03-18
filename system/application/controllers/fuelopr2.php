	<?php
include "base.php";

class Fuelopr2 extends Base {
	var $otherdb;

	function Fuelopr2()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
	}

	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_fuel_volt >", 0);
		$this->db->where("vehicle_type <>", "TJAM");

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		$this->params['code_view_menu'] = "report";


		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/voprfuel_report2', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_operational()
	{
		ini_set('display_errors', 1);
//		ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
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
		$geotype = $this->input->post("geotype");

		$type_location = $this->input->post("type_location");
		$type_duration = $this->input->post("type_duration");
		$type_km = $this->input->post("type_km");
		$statusname = $this->input->post("statusname");
		
		if($startdur != "" && $enddur != ""){
			$startdur = $startdur * 60;
			$enddur = $enddur * 60;
		}

		//$report = "operasional_";
		$report = "fuel_location_clean_"; // new report
		$report_sum = "summary_";

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
		
		$rom_list = array("ROM 01","ROM 02","ROM 03","ROM 04","ROM 05","ROM 06","ROM 07","ROM 08");
		$port_list = array("PORT BIB","PORT BIR","PORT TIA","PORT BBC");
		$datastatus = array("CONS","FILL","OFF","FILL2");
		if ($vehicle == "")
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih Kendaraan! \n";
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

			$this->dbtrip = $this->load->database("tensor_report",true);
			//$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			if($vehicle != "all"){
				$this->dbtrip->where("trip_mileage_vehicle_id", $vehicle);
			}
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			$this->dbtrip->where("trip_mileage_engine <>", 1); //bukan move
			if($type_location == "1"){
				if($location_start != ""){
					$this->dbtrip->like("trip_mileage_location_start", $location_start);
				}
				if($location_end != ""){
					$this->dbtrip->like("trip_mileage_location_end", $location_end);
				}
			}
			
			$this->dbtrip->where("trip_mileage_duration_sec >=", 60);
			$this->dbtrip->where("trip_mileage_km >=", 0);
			$this->dbtrip->where_in("trip_mileage_status", $datastatus);
			
			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				//$rows = array_merge($rows, $q->result());
				$rows = $q->result();
			}

			

		//}

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

		$params['view_map'] = $view_map;

		if($m1 != $m2)
		{
			$params['data'] = $rowsall;

		}
		else
		{
			$params['data'] = $rows;

		}

		//print_r($rows_summary);exit();

		//$params['vehicle'] = $rv;
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

		$html = $this->load->view("dashboard/report/voprfuel_result2", $params, true);
		

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

	}

	function gotoanimation()
	{
		$rows = 0;
		exit (json_encode(array("data_summary"=>$rows)));
		return;
	}

	function summary()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type <>", "TJAM");

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/v_operationalreport_summary', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function result_summary()
	{
		ini_set('display_errors', 1);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle      = $this->input->post("vehicle");
		$startdate    = $this->input->post("startdate");
		$enddate      = $this->input->post("startdate"); //sama
		$shour        = "00:00:00";
		$ehour        = "23:59:59";
		$engine       = $this->input->post("engine");
		$company      = $this->input->post("company");

		$report       = "operasional_";
		$report_sum   = "summary_";

		$sdate        = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate        = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));

		$m1           = date("F", strtotime($startdate));
		$m2           = date("F", strtotime($enddate));
		$year         = date("Y", strtotime($startdate));
		$year2        = date("Y", strtotime($enddate));
		$rows         = array();
		$rows2        = array();
		$total_q      = 0;
		$total_q2     = 0;

		$error        = "";
		$rows_summary = "";


		if ($company == "" || $company == 0)
		{
			$error .= "- Silahkan Pilih Cabang ! \n";
		}
		/*if ($vehicle == "" || $vehicle == 0)
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		} */
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

		//get vehicle
		$this->db->from("vehicle");
		$this->db->where("vehicle_company", $company);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		$rv = $qv->result();
		//end get vehicle

		$datacompany        = $this->get_company_all();

		$params['dbtable']  = $dbtable;
		$params['data']     = $rv;
		$params['sdate']    = $sdate;
		$params['edate']    = $edate;
		$params['rcompany'] = $datacompany;

		// $html            = $this->load->view("transporter/report/result_operational_summary", $params, true);
		$html               = $this->load->view('dashboard/report/v_result_operationalsummary', $params, true);

		$callback['error']  = false;
		$callback['html']   = $html;
		echo json_encode($callback);
	}

	function door()
	{
		ini_set('display_errors', 1);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$selected = array("T5DOOR","TK315DOOR","T5SILVERDOOR");

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where_in("vehicle_type", $selected);
		$this->db->where("vehicle_type <>", "3");

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vopr_door_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_door()
	{

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$door = $this->input->post("door");
		$location_start = $this->input->post("location_start");
		$location_end = $this->input->post("location_end");
		$checkdetail = $this->input->post("checkdetail");
		$type_location = $this->input->post("type_location");

		$report = "door_";
		$report_sum = "";

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

		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		$rv = $qv->row();
		//end get vehicle

		$totaldatagps = 0;

		if(count($rv)>0){

			$this->dbtrip = $this->load->database("tensor_report",true);
			$this->dbtrip->order_by("door_vehicle_id","asc");
			$this->dbtrip->order_by("door_start_time","asc");
			if($checkdetail == ""){
				$this->dbtrip->where("door_duration_sec >", 60);
			}
			$this->dbtrip->where("door_vehicle_device", $rv->vehicle_device);
			$this->dbtrip->where("door_start_time >=",$sdate);
			$this->dbtrip->where("door_end_time <=", $edate);
			if($door != ""){
				$this->dbtrip->where("door_status", $door);
			}
			if($type_location == "1"){
				if($location_start != ""){
					$this->dbtrip->like("door_location_start", $location_start);
				}
				if($location_end != ""){
					$this->dbtrip->like("door_location_end", $location_end);
				}
			}

			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				$rows = $q->result();
				$totaldatagps = $rows[0]->door_totaldata;
			}
		}

		$params['data'] = $rows;
		$params['vehicle'] = $rv;
		$params['dbtable'] = $dbtable;
		$params['startdate'] = $startdate;
		$params['enddate'] = $enddate;
		$params['totaldatagps'] = $totaldatagps;
		$html = $this->load->view("dashboard/report/vopr_door_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

	}

	function pto()
	{
		ini_set('display_errors', 1);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$selected = array("T5PTO","X3_PTO","GT08PTO","GT08SPTO","TK510PTO");

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where_in("vehicle_type", $selected);
		$this->db->where("vehicle_type <>", "3");

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vopr_pto_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_pto()
	{

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$pto = $this->input->post("pto");
		$location_start = $this->input->post("location_start");
		$location_end = $this->input->post("location_end");
		$checkdetail = $this->input->post("checkdetail");
		$type_location = $this->input->post("type_location");

		$report = "pto_";
		$report_sum = "";

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

		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		$rv = $qv->row();
		//end get vehicle

		$totaldatagps = 0;
		$totaldur = 0;
		if(count($rv)>0){

			$this->dbtrip = $this->load->database("tensor_report",true);
			$this->dbtrip->order_by("pto_vehicle_id","asc");
			$this->dbtrip->order_by("pto_start_time","asc");
			if($checkdetail == ""){
				$this->dbtrip->where("pto_duration_sec >", 60);
			}
			$this->dbtrip->where("pto_vehicle_device", $rv->vehicle_device);
			$this->dbtrip->where("pto_start_time >=",$sdate);
			$this->dbtrip->where("pto_end_time <=", $edate);
			if($pto != ""){
				$this->dbtrip->where("pto_status", $pto);
			}
			if($type_location == "1"){
				if($location_start != ""){
					$this->dbtrip->like("pto_location_start", $location_start);
				}
				if($location_end != ""){
					$this->dbtrip->like("pto_location_end", $location_end);
				}
			}

			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				$rows = $q->result();

				for($i=0; $i < count($rows); $i++)
				{
					if($rows[$i]->pto_status == "ON"){
						$totaldur += $rows[$i]->pto_duration_sec;
					}
				}
				$totaldatagps = $rows[0]->pto_totaldata;
			}
		}
		$totalduration = $totaldur;

		$params['data'] = $rows;
		$params['vehicle'] = $rv;
		$params['dbtable'] = $dbtable;
		$params['startdate'] = $startdate;
		$params['enddate'] = $enddate;
		$params['totaldatagps'] = $totaldatagps;
		$params['totalduration'] = $totalduration;
		$html = $this->load->view("dashboard/report/vopr_pto_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

	}

	function map()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id = $this->uri->segment(3);
		//$dbtable = $this->uri->segment(4);

			$this->dbtrip = $this->load->database("tensor_report",true);
			$this->dbtrip->order_by("trip_mileage_id","asc");
			$this->dbtrip->where("trip_mileage_id", $id);
			$q = $this->dbtrip->get("operasional_mei_2019");
			$row = $q->row();

			if(count($row)>0){
				$data = json_decode($row->trip_mileage_coordinate_list);
				$gps = $data;
				$total_data  = count($gps);

				/*foreach ($data as $v=>$key)
				{
					$gps[$i]->gps_latitude_real = $key["Lat"];
					$gps[$i]->gps_longitude_real = $key["Lng"];
					$map_params[] = array($gps[$i]->gps_longitude_real, $gps[$i]->gps_latitude_real);
					$i++;
				}*/

				//get data operasional
				for($i=0;$i<$total_data;$i++)
				{
					//$gps[$i]->gps_latitude_real = $key["Lat"];
					//$gps[$i]->gps_longitude_real = $key["Lng"];
					$map_params[] = array($gps[$i]->gps_longitude_real, $gps[$i]->gps_latitude_real);
					//$i++;
				}

				$uniqid = md5( uniqid() );

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

				$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
				$this->params['uniqid'] = isset($uniqid) ? $uniqid : "";
				$this->params['data'] = $gps;
				$this->params['row'] = $row;
				$this->params['content'] = $this->load->view("transporter/report/result_operational_map", $this->params, true);
				$this->load->view("templatesess", $this->params);
			}

	}

	function getDistanceBetween($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi')
	{
		$theta = $longitude1 - $longitude2;
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))  + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
		$distance = acos($distance);
		$distance = rad2deg($distance);
		$distance = $distance * 60 * 1.1515;
		switch($unit)
		{
			case 'Mi': break;
			case 'Km' : $distance = $distance * 1.609344;
		}
		return (round($distance,2));
	}

	function get_company_all(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}
	function get_company_bylevel(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		/*if($this->sess->user_level == "1"){
			$this->db->where("company_created_by", $this->sess->user_id);
		}*/
		$this->db->where("company_created_by", $this->sess->user_id);
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function get_company_bycustom(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();

		return $rd;
	}

	function get_company_byarea($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_id", $id);
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();

		return $rd;
	}
	function get_supercompany_byarea($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_super_company", $id);
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();

		return $rd;
	}

	function get_plant_byadmin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("plant_flag", 0);
		$this->dbtransporter->where("plant_company", 24); //khusus company balrich
		$qd = $this->dbtransporter->get("droppoint_plant");
		$rd = $qd->result();

		return $rd;
	}

	function get_plant_bycompany($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("company_id", $id);
		$this->dbtransporter->join("droppoint_plant", "company_plant = plant_id", "left");
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
		return $rd;
	}
	function get_plant_bysupercompany($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("plant_super_company", $id);
		$qd = $this->dbtransporter->get("droppoint_plant");
		$rd = $qd->result();
		return $rd;
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
			$options = "<option value='0' selected='selected'>--Select Vehicle--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_device . "'>". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
			}

			echo $options;
			return;
		}
	}

	function get_vehicle_door_by_company($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$selected = array("T5DOOR","TK315DOOR","T5SILVERDOOR");

		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		$this->db->where_in("vehicle_type", $selected);
		if($this->sess->user_group > 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>",3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if($qd->num_rows() > 0){
			$options = "<option value='0' selected='selected'>--Select Vehicle--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_device . "'>". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
			}

			echo $options;
			return;
		}
	}

	function get_vehicle_pto_by_company($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$selected = array("T5PTO","X3_PTO");

		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		$this->db->where_in("vehicle_type", $selected);
		if($this->sess->user_group > 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>",3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if($qd->num_rows() > 0){
			$options = "<option value='0' selected='selected'>--Select Vehicle--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_device . "'>". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
			}

			echo $options;
			return;
		}
	}

	function get_company_by_plant($plant){

		$byadmin = 0;
		$idcompany = 0;
		$super = 0;
		//balrich dan sariroti pusat
		if($this->sess->user_id == "1032" || $this->sess->user_id == "2974"){
			$byadmin = 1;
		}else{
			//rembang
			if($this->sess->user_company == "1508" || $this->sess->user_company == "1507"){
				$idcompany = 1508;
			}
			//cikarang
			if($this->sess->user_company == "65" || $this->sess->user_company == "612"){
				$idcompany = 65;
			}
			//cikande
			if($this->sess->user_company == "397" || $this->sess->user_company == "613"){
				$idcompany = 397;
			}
			//palembang
			if($this->sess->user_company == "258" || $this->sess->user_company == "614"){
				$idcompany = 258;
			}
			//lampung
			if($this->sess->user_company == "66" || $this->sess->user_company == "615"){
				$idcompany = 66;
			}
			//pasuruan
			if($this->sess->user_company == "98" || $this->sess->user_company == "616"){
				$idcompany = 98;
			}
			//medan
			if($this->sess->user_company == "556" || $this->sess->user_company == "620"){
				$idcompany = 556;
			}
			//makassar
			if($this->sess->user_company == "398" || $this->sess->user_company == "621"){
				$idcompany = 398;
			}
			//cileungsi
			if($this->sess->user_company == "68" || $this->sess->user_company == "622"){
				$idcompany = 68;
			}
			//parung
			if($this->sess->user_company == "432" || $this->sess->user_company == "623"){
				$idcompany = 432;
			}
			//keradenan
			if($this->sess->user_company == "433" || $this->sess->user_company == "624"){
				$idcompany = 433;
			}
			//semarang
			if($this->sess->user_company == "470" || $this->sess->user_company == "625"){
				$idcompany = 470;
			}
			//purwakarta
			if($this->sess->user_company == "630" || $this->sess->user_company == "638"){
				$idcompany = 630;
			}
			//balaraja
			if($this->sess->user_company == "656" || $this->sess->user_company == "657"){
				$idcompany = 656;
			}
			//cianjur
			if($this->sess->user_company == "680" || $this->sess->user_company == "682"){
				$idcompany = 680;
			}
			//cirebon
			if($this->sess->user_company == "681" || $this->sess->user_company == "638"){
				$idcompany = 681;
			}

			//cikarang (plant) - sariroti
			if($this->sess->user_company == "1439"){
				$idcompany = 1439;
				$super = 1;
			}
			//cibitung (plant) - sariroti
			if($this->sess->user_company == "1441"){
				$idcompany = 1441;
				$super = 1;
			}
			//cikande (plant) - sariroti
			if($this->sess->user_company == "1442"){
				$idcompany = 1442;
				$super = 1;
			}
			//purwakarta (plant) - sariroti
			if($this->sess->user_company == "1443"){
				$idcompany = 1443;
				$super = 1;
			}
			//semarang (plant) - sariroti
			if($this->sess->user_company == "1445"){
				$idcompany = 1445;
				$super = 1;
			}
			//pasuruan (plant) - sariroti
			if($this->sess->user_company == "1448"){
				$idcompany = 1448;
				$super = 1;
			}
			//palembang (plant) - sariroti
			if($this->sess->user_company == "1444"){
				$idcompany = 1444;
				$super = 1;
			}
			//medan (plant) - sariroti
			if($this->sess->user_company == "1447"){
				$idcompany = 1447;
				$super = 1;
			}
			//makassar (plant) - sariroti
			if($this->sess->user_company == "1446"){
				$idcompany = 1446;
				$super = 1;
			}
			//kalimantan + plant
			if($this->sess->user_company == "1644" || $this->sess->user_company == "1647"){
				$idcompany = 1644; //khusus kendaran kalimantan hanya ada di pool kalimantan
				$super = 1;
			}
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_flag", 0);
		if($byadmin == "1"){
			$this->dbtransporter->where("company_plant", $plant);
		}else{
			if($super == "1"){
				$this->dbtransporter->where("company_super_company", $idcompany);
			}else{
				$this->dbtransporter->where("company_id", $idcompany);
			}
		}
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();

		if($qd->num_rows() > 0){
			$options = "<option value='' selected='selected'>--Select Pool--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->company_id . "'>". $obj->company_name ."</option>";
			}

			echo $options;
			return;
		}
	}
}
