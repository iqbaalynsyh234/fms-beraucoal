<?php
include "base.php";

class Pbimanage extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->model("dashboardmanagemodel");
	}

	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		if ($this->sess->user_level == 0)
		{
			redirect(base_url());
		}
		//get vehicle
		$data = $this->dashboardmanagemodel->getsj_byadmin($this->sess->user_id);
		$companydata = $this->dashboardmodel->getcompany_id($this->sess->user_id);
		$this->params['code_view_menu'] = "report";

		$this->params['companydata']    = $companydata;
		$this->params['data']           = $data;
		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/manage/sj_list', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);

	}

	function dataoperational($id)
	{
		//get detail sj
		$data = $this->dashboardmanagemodel->getsj_byid($id);
		$vehicle = 0;
		$sdate = "";
		$edate = "";
		$sj_no = "";
		$sj_di_no = "";
		$sj_driver = "";
		$sj_category = "";
		$sj_item = "";
		$vehicle_no = 0;

		if(count($data)>0){
			$sj_no = $data->sj_sj_no;
			$sj_di_no = $data->sj_di_no;
			$sj_driver = $data->sj_driver;
			$sj_category = $data->sj_category;
			$sj_item = $data->sj_item;
			$vehicle = $data->sj_vehicle_device;
			$vehicle_no = $data->sj_vehicle_no;
			$sdate = date("Y-m-d H:i:s", strtotime($data->sj_api_modified));
			$edate = date("Y-m-d H:i:s", strtotime($data->sj_api_completed));
		}

		$report = "operasional_";
		$report_sum = "summary_";

		$totalduration = 0;
		$totalcummulative = 0;
		$totalcummulative_on = 0;
		$totalcummulative_off = 0;
		$totaldatagps = 0;

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		$error = "";
		$rows_summary = "";

		$engine = "";
		$location_start = "";
		$location_end = "";
		$startdur = "";
		$enddur = "";
		$km_start = "";
		$km_end = "";
		$type_location = 0;
		$type_km = 0;
		$type_duration = 0;


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
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		$rv = $qv->row();
		//end get vehicle

		//get data operasional
		if(count($rv)>0){
				$this->dbtrip = $this->load->database("powerblock_report",true);
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
					//$totaldatagps = $rows[$i]->trip_mileage_totaldata;
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

		$sj_no = $data->sj_sj_no;
		$sj_di_no = $data->sj_di_no;
		$sj_driver = $data->sj_driver;
		$sj_category = $data->sj_category;
		$sj_item = $data->sj_item;
		$vehicle = $data->sj_vehicle_device;
		$vehicle_no = $data->sj_vehicle_no;

		$params['vehicle'] = $rv;
		$params['dbtable'] = $dbtable;
		$params['dbtable_sum'] = $dbtable_sum;

		$params['startdate'] = $sdate;
		$params['enddate'] = $edate;

		$params['sj_no'] = $sj_no;
		$params['sj_di_no'] = $sj_di_no;
		$params['sj_driver'] = $sj_driver;
		$params['sj_category'] = $sj_category;
		$params['sj_item'] = $sj_item;
		$params['vehicle_no'] = $vehicle_no;

		$params['km_start'] = $km_start;
		$params['km_end'] = $km_end;

		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
		//$params['totaldatagps'] = $totaldatagps;

		$html = $this->load->view("dashboard/report/voperational_report_result_on_sjlist", $params, true);

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


}
