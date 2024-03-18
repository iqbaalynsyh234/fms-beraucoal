<?php
include "base.php";

class Pbi_operationalreport extends Base {
var $otherdb;

  function Pbi_operationalreport(){
    parent::Base();
    $this->load->helper('common_helper');
    $this->load->helper('common');
		$this->load->model("gpsmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
  }

  function mn_dataoperational(){
    // print_r("masuk");die();
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
    $this->params['code_view_menu'] = "report";

    $this->params["header"]        = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]       = $this->load->view('powerblock/dashboard/sidebar', $this->params, true);
    // $this->params["chatsidebar"]   = $this->load->view('powerblock/dashboard/chatsidebar', $this->params, true);
    $this->params["content"]       = $this->load->view('powerblock/dashboard/report/v_home_operationalreport', $this->params, true);
    $this->load->view("dashboard/template_dashboard_report", $this->params);
    // $this->params["content"] = $this->load->view('report/mn_operational', $this->params, true);
    // $this->load->view("templatesess", $this->params);
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

  	// $html = $this->load->view("report/result_operational_bygeofence", $params, true);
    $html = $this->load->view("powerblock/dashboard/report/result_operational_bygeofence", $params, true);
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

  // $html = $this->load->view("report/result_operational_bygeofence", $params, true);
  $html = $this->load->view("powerblock/dashboard/report/result_operational_bygeofence", $params, true);
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

  	// $html              = $this->load->view("report/result_operational_bygeofence", $params, true);
    $html = $this->load->view("powerblock/dashboard/report/result_operational_bygeofence", $params, true);
  	$callback['error'] = false;
  	$callback['html']  = $html;
  	echo json_encode($callback);
  	return;
  }

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

		// $html              = $this->load->view("report/historymaps", $params, true);
    $html = $this->load->view("powerblock/dashboard/report/historymaps", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
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

		// $html = $this->load->view("report/result_operational", $params, true);
    $html = $this->load->view("powerblock/dashboard/report/result_operational", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
	}

}
?>
