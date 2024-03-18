	<?php
include "base.php";

class Pbi_report extends Base {
	var $otherdb;

	function Pbi_report()
	{
		parent::Base();
		$this->load->helper('common_helper');
    $this->load->helper('common');
		$this->load->model("gpsmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
	}
	//not
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('report/mn_inout_geofence', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function mn_dataoperational()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

			// $this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$q    = $this->db->get("vehicle");

		$rows = $q->result();

		// echo "<pre>";
		// var_dump($this->params["loop"]);die();
		// echo "<pre>";

		// FOR GEOFENCE
		$this->db->select("geofence_vehicle, geofence_name, geofence_type");
		$this->db->where("geofence_status", "1");
		$this->db->where("geofence_user", $this->sess->user_id);
		$this->db->group_by("geofence_name");
		$this->db->order_by("geofence_name", "asc");

		$q_geofence = $this->db->get("webtracking_geofence");
		$row_geofence = $q_geofence->result();
		// echo "<pre>";
		// var_dump($row_geofence);die();
		// echo "<pre>";

		$this->params["geofence_name"] = $row_geofence;
		$this->params["vehicles"]      = $rows;
		$this->params["loop"]          = sizeof($rows);
		$this->params["content"] = $this->load->view('report/mn_operational', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function dataoperational()
	{
		//ini_set('display_errors', 1);

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle   = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate   = $this->input->post("enddate");
		$engine    = $this->input->post("engine");
		$location  = $this->input->post("location");
		$startdur  = $this->input->post("s_minute");
		$enddur    = $this->input->post("e_minute");
		$report    = "operasional_";

		if($startdur != "" && $enddur != ""){
			$startdur = $startdur * 60;
			$enddur = $enddur * 60;
		}

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		switch ($m1)
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

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}

		//get vehicle
		$this->db->limit(1);
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
		//$rv = $qv->result();
		$rv = $qv->row();
		//end get vehicle

		//get data operasional

			$this->dbtrip = $this->load->database("powerblock_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->group_by("trip_mileage_start_time");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			if($location != ""){
				$this->dbtrip->like("trip_mileage_location_start", $location);
				$this->dbtrip->like("trip_mileage_location_end", $location);
			}
			if($startdur != "" && $enddur != ""){
				$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
				$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
			}
			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
				//$rows = $q->result();
				//print_r(count($rows));exit();
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("powerblock_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->group_by("trip_mileage_start_time");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				if($location != ""){
					$this->dbtrip->like("trip_mileage_location_start", $location);
					$this->dbtrip->like("trip_mileage_location_end", $location);
				}
				if($startdur != "" && $enddur != ""){
					$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
					$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
				}
				$q2 = $this->dbtrip->get($dbtable2);

				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}

				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}

					$this->dbtrip = $this->load->database("powerblock_report",true);
					$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->group_by("trip_mileage_start_time");
					$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					if($engine != ""){
						$this->dbtrip->where("trip_mileage_engine", $engine);
					}
					if($location != ""){
						$this->dbtrip->like("trip_mileage_location_start", $location);
						$this->dbtrip->like("trip_mileage_location_end", $location);
					}
					if($startdur != "" && $enddur != ""){
						$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
						$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
					}
					$q2 = $this->dbtrip->get($dbtable2);
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}


		//totaldur
		//total cumm km
		$totalcumm = 0;
		$totalcumm_on = 0;
		$totalcumm_off = 0;
		$totaldur = 0;
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
		else{
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

			}

		}

		$totalcummulative = $totalcumm;
		$totalcummulative_on = $totalcumm_on;
		$totalcummulative_off = $totalcumm_off;
		$totalduration = $totaldur;

		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}

		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;

		$html = $this->load->view("report/result_operational", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;

	}

	function dataoperational_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = $this->input->post("engine");
		$location = $this->input->post("location");
		$startdur = $this->input->post("s_minute");
		$enddur = $this->input->post("e_minute");
		$report = "operasional_";
		if($startdur != "" && $enddur != ""){
			$startdur = $startdur * 60;
			$enddur = $enddur * 60;
		}

		$offset = 0;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		switch ($m1)
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

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
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

		$rv = $qv->result();
		//end get vehicle

		//get data operasional
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("powerblock_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			if($location != ""){
				$this->dbtrip->like("trip_mileage_location_start", $location);
				$this->dbtrip->like("trip_mileage_location_end", $location);
			}
			if($startdur != "" && $enddur != ""){
				$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
				$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
			}
			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("powerblock_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);

				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				if($location != ""){
					$this->dbtrip->like("trip_mileage_location_start", $location);
					$this->dbtrip->like("trip_mileage_location_end", $location);
				}
				if($startdur != "" && $enddur != ""){
					$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
					$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
				}
				$q2 = $this->dbtrip->get($dbtable2);

				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}

				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}

					$this->dbtrip = $this->load->database("powerblock_report",true);
					$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					if($engine != ""){
						$this->dbtrip->where("trip_mileage_engine", $engine);
					}
					if($location != ""){
						$this->dbtrip->like("trip_mileage_location_start", $location);
						$this->dbtrip->like("trip_mileage_location_end", $location);
					}
					if($startdur != "" && $enddur != ""){
						$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
						$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
					}
					$q2 = $this->dbtrip->get($dbtable2);
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		//totaldur
		//total cumm km
		$totalcumm = 0;
		$totalcumm_on = 0;
		$totalcumm_off = 0;
		$totaldur = 0;
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
		else{
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

			}

		}

		$totalcummulative = $totalcumm;
		$totalcummulative_on = $totalcumm_on;
		$totalcummulative_off = $totalcumm_off;
		$totalduration = $totaldur;

		if($m1 != $m2)
		{
			$data = $rowsall;
		}
		else
		{
			$data = $rows;
		}

		$total = count($data);

		//get vehicle name
		$this->db->order_by("vehicle_id","asc");
		$this->db->select("vehicle_no, vehicle_name");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle);
		$this->db->limit(1);
		$q_name = $this->db->get("vehicle");
		$r_name = $q_name->row();
		if ($q_name->num_rows>0){
			$vehicle_name = $r_name->vehicle_name;
			$vehicle_no = $r_name->vehicle_no;
		}else{
			$vehicle_name = "-";
			$vehicle_no = "-";
		}

		/** PHPExcel */
		include 'class/PHPExcel.php';

		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Operational Data Report");
		$objPHPExcel->getProperties()->setSubject("Operational Data Report Lacak-mobil.com");
		$objPHPExcel->getProperties()->setDescription("Operational Data Report Lacak-mobil.com");

		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'OPERATIONAL DATA REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C3', $startdate." "."-"." ".$enddate);
		$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('F3', "Vehicle :");
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('G3', $vehicle_name." ".$vehicle_no);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);

		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Start Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'End Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Engine');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Duration');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Location End');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Coordinate Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Coordinate End');
		$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Trip Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Cumulative Mileage');

		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$i=1;
		$k=0;
		for($j=0;$j<count($data);$j++)
		{
			$k = $k + $data[$j]->trip_mileage_trip_mileage;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $data[$j]->trip_mileage_start_time);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $data[$j]->trip_mileage_end_time);

			if($data[$j]->trip_mileage_engine == 0){
				$engine = "OFF";
			}else{
				$engine = "ON";
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $engine);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $data[$j]->trip_mileage_duration);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$geofence_start = strlen($data[$j]->trip_mileage_geofence_start);
			if (strlen($geofence_start == 1)){
				$geofence_start_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_start_name."  ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_start > 1)){
				$geofence_start_name = $data[$j]->trip_mileage_geofence_start;
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_start_name.", ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}

			$geofence_end = strlen($data[$j]->trip_mileage_geofence_end);

			if (strlen($geofence_end == 1)){
				$geofence_end_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i),$geofence_end_name."  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_end > 1)){
				$geofence_end_name = $data[$j]->trip_mileage_geofence_end;
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i),$geofence_end_name.",  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getFont()->setSize(8);
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $data[$j]->trip_mileage_coordinate_start);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $data[$j]->trip_mileage_coordinate_end);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $data[$j]->trip_mileage_trip_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $k." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
		}

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), 'Total Mileage');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(6+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('I'.(6+$i).':'.'K'.(6+$i));
			//$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $totalcummulative_on.' '.'KM');
			if(isset($k) && $k > 0){
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $k.' '.'KM');
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), ' ');
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $k.' '.'KM');

			$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), 'Total Duration');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('I'.(7+$i).':'.'K'.(7+$i));
			if (isset($totalduration))
									{
										$conval = $totalduration;
										$seconds = $conval;

										// extract hours
										$hours = floor($seconds / (60 * 60));
										$h_duration = "";
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
										$m_duration = "";
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);

										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$h_duration = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$h_duration = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$m_duration = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$m_duration = $minutes." "."Minutes"." ";
											}
										}
										 /* if(isset($seconds) && $seconds > 0)
										{
											$s_duration =  $seconds." "."Detik"." ";
										} */
									}

			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(7+$i), $h_duration." ".$m_duration);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(7+$i))->getFont()->setBold(true);


		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(7+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(7+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(7+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('operational_data');

		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "operational_".$vehicle_no."_".$filedate.".xls";

		$objWriter->save("assets/media/report/".$filecreatedname);

		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
	}

		// FUNGSI UNTUK GOTO HISTORY MAPS
	function newhistorymaps(){
		//ini_set('display_errors', 1);

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle      = $this->input->post("vehicle");
		$startdate    = $this->input->post("startdate");
		$enddate      = $this->input->post("enddate");
		$engine       = $this->input->post("engine");
		$location     = $this->input->post("location");
		$startdur     = $this->input->post("s_minute");
		$enddur       = $this->input->post("e_minute");
		$report       = "operasional_";
		$user_company = $this->sess->user_company;
		// echo "<pre>";
		// var_dump($user_company);die();
		// echo "<pre>";

		// if($startdur != "" && $enddur != ""){
		// 	$startdur = $startdur * 60;
		// 	$enddur = $enddur * 60;
		// }

		// echo "<pre>";
		// var_dump($startdur);die();
		// echo "<pre>";

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		switch ($m1)
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

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
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
		//$rv = $qv->result();
		$rv = $qv->row();
		//end get vehicle

		//get data operasional

			$this->dbtrip = $this->load->database("powerblock_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			$this->dbtrip->where("trip_mileage_engine", "0");


			// if($location != ""){
			// 	$this->dbtrip->like("trip_mileage_location_start", $location);
			// 	$this->dbtrip->like("trip_mileage_location_end", $location);
			// }

			$this->dbtrip->where("trip_mileage_duration_sec >", "120");


			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("powerblock_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				if($user_company == "48"){
				$this->dbtrip->where("trip_mileage_engine", "0");
				}else{
					$this->dbtrip->where("trip_mileage_engine", "1");
				}
				// if($location != ""){
				// 	$this->dbtrip->like("trip_mileage_location_start", $location);
				// 	$this->dbtrip->like("trip_mileage_location_end", $location);
				// }
				$this->dbtrip->where("trip_mileage_duration_sec >", "120");

				$q2 = $this->dbtrip->get($dbtable2);

				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}

				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}

					$this->dbtrip = $this->load->database("powerblock_report",true);
					$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->where("trip_mileage_vehicle_id", $rv->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					if($user_company == "48"){
					$this->dbtrip->where("trip_mileage_engine", "0");
					}else{
						$this->dbtrip->where("trip_mileage_engine", "1");
					}
					// if($location != ""){
					// 	$this->dbtrip->like("trip_mileage_location_start", $location);
					// 	$this->dbtrip->like("trip_mileage_location_end", $location);
					// }
					$this->dbtrip->where("trip_mileage_duration_sec >", "120");

					$q2 = $this->dbtrip->get($dbtable2);
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}


		//totaldur
		//total cumm km
		$totalcumm = 0;
		$totalcumm_on = 0;
		$totalcumm_off = 0;
		$totaldur = 0;
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
		else{
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

			}

		}

		$totalcummulative = $totalcumm;
		$totalcummulative_on = $totalcumm_on;
		$totalcummulative_off = $totalcumm_off;
		$totalduration = $totaldur;

		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}

		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;

		$html              = $this->load->view("report/historymaps", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
	}

	function getdataoperationalbygeofence(){
	//ini_set('display_errors', 1);

	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$vehicle      = $this->input->post("vehicle");
	$thisgeofence = $this->input->post("thisgeofence");
	$startdate    = $this->input->post("startdate");
	$enddate      = $this->input->post("enddate");
	$engine       = $this->input->post("engine");
	$location     = $this->input->post("location");
	$startdur     = $this->input->post("s_minute");
	$enddur       = $this->input->post("e_minute");
	$shour        = $this->input->post("shour");
	$ehour        = $this->input->post("ehour");
	$report       = "operasional_";

	// echo "<pre>";
	// var_dump($thisgeofence);die();
	// echo "<pre>";

	if($startdur != ""){
		$startdur = $startdur * 60;
	}else {
		$startdur = "0";
	}

	if ($enddur != "") {
		$enddur = $enddur * 60;
	}else {
		$enddur = "9999999999";
	}



	$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour.":00"));
	$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour.":59"));

	$m1 = date("F", strtotime($startdate));
	$m2 = date("F", strtotime($enddate));
	$year = date("Y", strtotime($startdate));
	$year2 = date("Y", strtotime($enddate));
	$rows = array();
	$rows2 = array();
	$total_q = 0;
	$total_q2 = 0;

	switch ($m1)
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

	switch ($m2)
	{
		case "January":
					$dbtable2 = $report."januari_".$year;
		break;
		case "February":
					$dbtable2 = $report."februari_".$year;
		break;
		case "March":
					$dbtable2 = $report."maret_".$year;
		break;
		case "April":
					$dbtable2 = $report."april_".$year;
		break;
		case "May":
					$dbtable2 = $report."mei_".$year;
		break;
		case "June":
					$dbtable2 = $report."juni_".$year;
		break;
		case "July":
					$dbtable2 = $report."juli_".$year;
		break;
		case "August":
					$dbtable2 = $report."agustus_".$year;
		break;
		case "September":
					$dbtable2 = $report."september_".$year;
		break;
		case "October":
					$dbtable2 = $report."oktober_".$year;
		break;
		case "November":
					$dbtable2 = $report."november_".$year;
		break;
		case "December":
					$dbtable2 = $report."desember_".$year;
		break;
	}


		$this->dbtrip = $this->load->database("powerblock_report",true);
		$this->dbtrip->order_by("trip_mileage_vehicle_no","asc");
		$this->dbtrip->order_by("trip_mileage_start_time","asc");
		$this->dbtrip->where("trip_mileage_geofence_start", $thisgeofence);
		$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
		$this->dbtrip->where("trip_mileage_end_time <=", $edate);
		if($engine != ""){
			$this->dbtrip->where("trip_mileage_engine", $engine);
		}
		if($location != ""){
			$this->dbtrip->like("trip_mileage_location_start", $location);
			$this->dbtrip->like("trip_mileage_location_end", $location);
		}
		if($startdur !=""){
			$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
		}else {
			$this->dbtrip->where("trip_mileage_duration_sec >=", "0");
		}

		if ($enddur !="") {
			$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
		}else {
			$this->dbtrip->where("trip_mileage_duration_sec <=", "9999999999");
		}



		$q = $this->dbtrip->get($dbtable);

		if ($q->num_rows>0)
		{
			$rows = array_merge($rows, $q->result());
		}



		if($m1 != $m2)
		{
			$this->dbtrip = $this->load->database("powerblock_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_no","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_geofence_start", $thisgeofence);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			if($location != ""){
				$this->dbtrip->like("trip_mileage_location_start", $location);
				$this->dbtrip->like("trip_mileage_location_end", $location);
			}
			if($startdur !=""){
				$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
			}else {
				$this->dbtrip->where("trip_mileage_duration_sec >=", "0");
			}

			if ($enddur !="") {
				$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
			}else {
				$this->dbtrip->where("trip_mileage_duration_sec <=", "9999999999");
			}


			$q2 = $this->dbtrip->get($dbtable2);

			if ($q2->num_rows>0)
			{
				$rows2 = array_merge($rows2, $q2->result());
			}

			if($year != $year2)
			{
				switch ($m2)
				{
					case "January":
					$dbtable2 = $report."januari_".$year2;
					break;
					case "February":
					$dbtable2 = $report."februari_".$year2;
					break;
					case "March":
					$dbtable2 = $report."maret_".$year2;
					break;
					case "April":
					$dbtable2 = $report."april_".$year2;
					break;
					case "May":
					$dbtable2 = $report."mei_".$year2;
					break;
					case "June":
					$dbtable2 = $report."juni_".$year2;
					break;
					case "July":
					$dbtable2 = $report."juli_".$year2;
					break;
					case "August":
					$dbtable2 = $report."agustus_".$year2;
					break;
					case "September":
					$dbtable2 = $report."september_".$year2;
					break;
					case "October":
					$dbtable2 = $report."oktober_".$year2;
					break;
					case "November":
					$dbtable2 = $report."november_".$year2;
					break;
					case "December":
					$dbtable2 = $report."desember_".$year2;
					break;
				}

				$this->dbtrip = $this->load->database("powerblock_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_no","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_geofence_start", $thisgeofence);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				if($location != ""){
					$this->dbtrip->like("trip_mileage_location_start", $location);
					$this->dbtrip->like("trip_mileage_location_end", $location);
				}
				if($startdur != "" && $enddur != ""){
					$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
					$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
				}
				$q2 = $this->dbtrip->get($dbtable2);
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}

			}
		}
		if($m1 != $m2)
		{
			$rowsall = array_merge($rows, $rows2);
		}


	//totaldur
	//total cumm km
	$totalcumm = 0;
	$totalcumm_on = 0;
	$totalcumm_off = 0;
	$totaldur = 0;
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
	else{
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

		}

	}

	$totalcummulative = $totalcumm;
	$totalcummulative_on = $totalcumm_on;
	$totalcummulative_off = $totalcumm_off;
	$totalduration = $totaldur;

	if($m1 != $m2)
	{
		$params['data'] = $rowsall;
	}
	else
	{
		$params['data'] = $rows;
	}

	$params['totalduration'] = $totalduration;
	$params['totalcummulative'] = $totalcummulative;
	$params['totalcummulative_on'] = $totalcummulative_on;
	$params['totalcummulative_off'] = $totalcummulative_off;

	$html = $this->load->view("report/result_operational_bygeofence", $params, true);
	$callback['error'] = false;
	$callback['html'] = $html;
	echo json_encode($callback);
	return;

}

function getdataoperationalbynotgeofence(){
//ini_set('display_errors', 1);

if (! isset($this->sess->user_type))
{
	redirect(base_url());
}

$vehicle      = $this->input->post("vehicle");
$thisgeofence = $this->input->post("thisgeofence");
$startdate    = $this->input->post("startdate");
$enddate      = $this->input->post("enddate");
$engine       = $this->input->post("engine");
$location     = $this->input->post("location");
$startdur     = $this->input->post("s_minute");
$enddur       = $this->input->post("e_minute");
$report       = "operasional_";

// echo "<pre>";
// var_dump($thisgeofence);die();
// echo "<pre>";

if($startdur != ""){
	$startdur = $startdur * 60;
}else {
	$startdur = "0";
}

if ($enddur != "") {
	$enddur = $enddur * 60;
}else {
	$enddur = "9999999999";
}



$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

$m1 = date("F", strtotime($startdate));
$m2 = date("F", strtotime($enddate));
$year = date("Y", strtotime($startdate));
$year2 = date("Y", strtotime($enddate));
$rows = array();
$rows2 = array();
$total_q = 0;
$total_q2 = 0;

switch ($m1)
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

switch ($m2)
{
	case "January":
				$dbtable2 = $report."januari_".$year;
	break;
	case "February":
				$dbtable2 = $report."februari_".$year;
	break;
	case "March":
				$dbtable2 = $report."maret_".$year;
	break;
	case "April":
				$dbtable2 = $report."april_".$year;
	break;
	case "May":
				$dbtable2 = $report."mei_".$year;
	break;
	case "June":
				$dbtable2 = $report."juni_".$year;
	break;
	case "July":
				$dbtable2 = $report."juli_".$year;
	break;
	case "August":
				$dbtable2 = $report."agustus_".$year;
	break;
	case "September":
				$dbtable2 = $report."september_".$year;
	break;
	case "October":
				$dbtable2 = $report."oktober_".$year;
	break;
	case "November":
				$dbtable2 = $report."november_".$year;
	break;
	case "December":
				$dbtable2 = $report."desember_".$year;
	break;
}


	$this->dbtrip = $this->load->database("powerblock_report",true);
	$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
	$this->dbtrip->order_by("trip_mileage_start_time","asc");
	$this->dbtrip->where("trip_mileage_geofence_start", "0");
	$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
	$this->dbtrip->where("trip_mileage_end_time <=", $edate);
	if($engine != ""){
		$this->dbtrip->where("trip_mileage_engine", $engine);
	}
	if($location != ""){
		$this->dbtrip->like("trip_mileage_location_start", $location);
		$this->dbtrip->like("trip_mileage_location_end", $location);
	}
	if($startdur !=""){
		$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
	}else {
		$this->dbtrip->where("trip_mileage_duration_sec >=", "0");
	}

	if ($enddur !="") {
		$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
	}else {
		$this->dbtrip->where("trip_mileage_duration_sec <=", "9999999999");
	}



	$q = $this->dbtrip->get($dbtable);

	if ($q->num_rows>0)
	{
		$rows = array_merge($rows, $q->result());
	}



	if($m1 != $m2)
	{
		$this->dbtrip = $this->load->database("powerblock_report",true);
		$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
		$this->dbtrip->order_by("trip_mileage_start_time","asc");
		$this->dbtrip->where("trip_mileage_geofence_start", "0");
		$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
		$this->dbtrip->where("trip_mileage_end_time <=", $edate);
		if($engine != ""){
			$this->dbtrip->where("trip_mileage_engine", $engine);
		}
		if($location != ""){
			$this->dbtrip->like("trip_mileage_location_start", $location);
			$this->dbtrip->like("trip_mileage_location_end", $location);
		}
		if($startdur !=""){
			$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
		}else {
			$this->dbtrip->where("trip_mileage_duration_sec >=", "0");
		}

		if ($enddur !="") {
			$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
		}else {
			$this->dbtrip->where("trip_mileage_duration_sec <=", "9999999999");
		}


		$q2 = $this->dbtrip->get($dbtable2);

		if ($q2->num_rows>0)
		{
			$rows2 = array_merge($rows2, $q2->result());
		}

		if($year != $year2)
		{
			switch ($m2)
			{
				case "January":
				$dbtable2 = $report."januari_".$year2;
				break;
				case "February":
				$dbtable2 = $report."februari_".$year2;
				break;
				case "March":
				$dbtable2 = $report."maret_".$year2;
				break;
				case "April":
				$dbtable2 = $report."april_".$year2;
				break;
				case "May":
				$dbtable2 = $report."mei_".$year2;
				break;
				case "June":
				$dbtable2 = $report."juni_".$year2;
				break;
				case "July":
				$dbtable2 = $report."juli_".$year2;
				break;
				case "August":
				$dbtable2 = $report."agustus_".$year2;
				break;
				case "September":
				$dbtable2 = $report."september_".$year2;
				break;
				case "October":
				$dbtable2 = $report."oktober_".$year2;
				break;
				case "November":
				$dbtable2 = $report."november_".$year2;
				break;
				case "December":
				$dbtable2 = $report."desember_".$year2;
				break;
			}

			$this->dbtrip = $this->load->database("powerblock_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_geofence_start", $thisgeofence);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			if($location != ""){
				$this->dbtrip->like("trip_mileage_location_start", $location);
				$this->dbtrip->like("trip_mileage_location_end", $location);
			}
			if($startdur != "" && $enddur != ""){
				$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
				$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
			}
			$q2 = $this->dbtrip->get($dbtable2);
			if ($q2->num_rows>0)
			{
				$rows2 = array_merge($rows2, $q2->result());
			}

		}
	}
	if($m1 != $m2)
	{
		$rowsall = array_merge($rows, $rows2);
	}


//totaldur
//total cumm km
$totalcumm = 0;
$totalcumm_on = 0;
$totalcumm_off = 0;
$totaldur = 0;
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
else{
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

	}

}

$totalcummulative = $totalcumm;
$totalcummulative_on = $totalcumm_on;
$totalcummulative_off = $totalcumm_off;
$totalduration = $totaldur;

if($m1 != $m2)
{
	$params['data'] = $rowsall;
}
else
{
	$params['data'] = $rows;
}

$params['totalduration'] = $totalduration;
$params['totalcummulative'] = $totalcummulative;
$params['totalcummulative_on'] = $totalcummulative_on;
$params['totalcummulative_off'] = $totalcummulative_off;

$html = $this->load->view("report/result_operational_bygeofence", $params, true);
$callback['error'] = false;
$callback['html'] = $html;
echo json_encode($callback);
return;

}

function getdatabygeofencinpbi(){
	//ini_set('display_errors', 1);

	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$vehicle       = $this->input->post("vehicle");
	$geofenceinpbi = $this->input->post("geofenceinpbi");
	$startdate     = $this->input->post("startdate");
	$enddate       = $this->input->post("enddate");
	$engine        = $this->input->post("engine");
	$location      = $this->input->post("location");
	$startdur      = $this->input->post("s_minute");
	$enddur        = $this->input->post("e_minute");
	$shour         = $this->input->post("shour");
	$ehour         = $this->input->post("ehour");
	$report        = "operasional_";

	// echo "<pre>";
	// var_dump($geofenceinpbi);die();
	// echo "<pre>";

	if($startdur != ""){
		$startdur = $startdur * 60;
	}else {
		$startdur = "0";
	}

	if ($enddur != "") {
		$enddur = $enddur * 60;
	}else {
		$enddur = "9999999999";
	}

	$arrayinpbi = array(
		"AREA POWER BOND", "AREA BENGKEL PBI", "AREA LOADING LOGISTIK", "AREA LOADING PLANT 3", "AREA PARKIR UTAMA", "AREA PKU", "AREA PLANT 5 PBU", "Kawasan Area PT.Powerblock"
	);

	// echo "<pre>";
	// var_dump($arrayinpbi);die();
	// echo "<pre>";



	$sdate    = date("Y-m-d H:i:s", strtotime($startdate." ".$shour.":00"));
	$edate    = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour.":59"));

	$m1       = date("F", strtotime($startdate));
	$m2       = date("F", strtotime($enddate));
	$year     = date("Y", strtotime($startdate));
	$year2    = date("Y", strtotime($enddate));
	$rows     = array();
	$rows2    = array();
	$total_q  = 0;
	$total_q2 = 0;

	switch ($m1)
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

	switch ($m2)
	{
		case "January":
					$dbtable2 = $report."januari_".$year;
		break;
		case "February":
					$dbtable2 = $report."februari_".$year;
		break;
		case "March":
					$dbtable2 = $report."maret_".$year;
		break;
		case "April":
					$dbtable2 = $report."april_".$year;
		break;
		case "May":
					$dbtable2 = $report."mei_".$year;
		break;
		case "June":
					$dbtable2 = $report."juni_".$year;
		break;
		case "July":
					$dbtable2 = $report."juli_".$year;
		break;
		case "August":
					$dbtable2 = $report."agustus_".$year;
		break;
		case "September":
					$dbtable2 = $report."september_".$year;
		break;
		case "October":
					$dbtable2 = $report."oktober_".$year;
		break;
		case "November":
					$dbtable2 = $report."november_".$year;
		break;
		case "December":
					$dbtable2 = $report."desember_".$year;
		break;
	}


		$this->dbtrip = $this->load->database("powerblock_report",true);
		// $this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
		$this->dbtrip->order_by("trip_mileage_start_time","asc");
		$this->dbtrip->where_in("trip_mileage_geofence_start", $arrayinpbi);
		$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
		$this->dbtrip->where("trip_mileage_end_time <=", $edate);
		if($engine != ""){
			$this->dbtrip->where("trip_mileage_engine", $engine);
		}
		if($location != ""){
			$this->dbtrip->like("trip_mileage_location_start", $location);
			$this->dbtrip->like("trip_mileage_location_end", $location);
		}
		if($startdur !=""){
			$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
		}else {
			$this->dbtrip->where("trip_mileage_duration_sec >=", "0");
		}

		if ($enddur !="") {
			$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
		}else {
			$this->dbtrip->where("trip_mileage_duration_sec <=", "9999999999");
		}



		$q = $this->dbtrip->get($dbtable);

		if ($q->num_rows>0)
		{
			$rows = array_merge($rows, $q->result());
		}



		if($m1 != $m2)
		{
			$this->dbtrip = $this->load->database("powerblock_report",true);
			// $this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where_in("trip_mileage_geofence_start", $arrayinpbi);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			if($location != ""){
				$this->dbtrip->like("trip_mileage_location_start", $location);
				$this->dbtrip->like("trip_mileage_location_end", $location);
			}
			if($startdur !=""){
				$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
			}else {
				$this->dbtrip->where("trip_mileage_duration_sec >=", "0");
			}

			if ($enddur !="") {
				$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
			}else {
				$this->dbtrip->where("trip_mileage_duration_sec <=", "9999999999");
			}


			$q2 = $this->dbtrip->get($dbtable2);

			if ($q2->num_rows>0)
			{
				$rows2 = array_merge($rows2, $q2->result());
			}

			if($year != $year2)
			{
				switch ($m2)
				{
					case "January":
					$dbtable2 = $report."januari_".$year2;
					break;
					case "February":
					$dbtable2 = $report."februari_".$year2;
					break;
					case "March":
					$dbtable2 = $report."maret_".$year2;
					break;
					case "April":
					$dbtable2 = $report."april_".$year2;
					break;
					case "May":
					$dbtable2 = $report."mei_".$year2;
					break;
					case "June":
					$dbtable2 = $report."juni_".$year2;
					break;
					case "July":
					$dbtable2 = $report."juli_".$year2;
					break;
					case "August":
					$dbtable2 = $report."agustus_".$year2;
					break;
					case "September":
					$dbtable2 = $report."september_".$year2;
					break;
					case "October":
					$dbtable2 = $report."oktober_".$year2;
					break;
					case "November":
					$dbtable2 = $report."november_".$year2;
					break;
					case "December":
					$dbtable2 = $report."desember_".$year2;
					break;
				}

				$this->dbtrip = $this->load->database("powerblock_report",true);
				// $this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where_in("trip_mileage_geofence_start", $arrayinpbi);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				if($location != ""){
					$this->dbtrip->like("trip_mileage_location_start", $location);
					$this->dbtrip->like("trip_mileage_location_end", $location);
				}
				if($startdur != "" && $enddur != ""){
					$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
					$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
				}
				$q2 = $this->dbtrip->get($dbtable2);
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}

			}
		}
		if($m1 != $m2)
		{
			$rowsall = array_merge($rows, $rows2);
		}


	//totaldur
	//total cumm km
	$totalcumm = 0;
	$totalcumm_on = 0;
	$totalcumm_off = 0;
	$totaldur = 0;
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
	else{
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

		}

	}

	$totalcummulative     = $totalcumm;
	$totalcummulative_on  = $totalcumm_on;
	$totalcummulative_off = $totalcumm_off;
	$totalduration        = $totaldur;

	if($m1 != $m2)
	{
		$params['data'] = $rowsall;
	}
	else
	{
		$params['data'] = $rows;
	}

	$params['totalduration']        = $totalduration;
	$params['totalcummulative']     = $totalcummulative;
	$params['totalcummulative_on']  = $totalcummulative_on;
	$params['totalcummulative_off'] = $totalcummulative_off;

	$html              = $this->load->view("report/result_operational_bygeofence", $params, true);
	$callback['error'] = false;
	$callback['html']  = $html;
	echo json_encode($callback);
	return;
}




}
