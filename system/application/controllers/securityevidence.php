<?php
include "base.php";

class Securityevidence extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Securityevidence()
	{
		parent::Base();
    // DASHBOARD START
    $this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
    // DASHBOARD END
		$this->load->model("gpsmodel");
    $this->load->model("m_securityevidence");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
	}

	function index(){
		if(! isset($this->sess->user_type)){
			redirect('dashboard');
		}

		$user_id         = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$privilegecode   = $this->sess->user_id_role;
		$user_dblive 	   = $this->sess->user_dblive;

		$this->params['data']           = $this->m_securityevidence->getdevice();
		$this->params['alarmtype']      = $this->m_securityevidence->getalarmmaster();
		// $this->params['alarmtype']      = $this->m_securityevidence->getalarmtype();

		// echo "<pre>";
		// var_dump($this->params['data']);die();
		// echo "<pre>";

		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/securityevidence/v_securityevidence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function getalarmtype(){
      $this->dbalarm = $this->load->database("tensor_report", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("alarm_status", 2);
      $this->dbalarm->order_by("alarm_name","asc");
      $q        = $this->dbalarm->get("webtracking_ts_alarm");
      return  $q->result();


    }

	function getalarmsubcat(){
		$subcategoryid                = $this->input->post("id");
		$callback['alarmsubcategory'] = $this->m_securityevidence->getalarmsubcategory($subcategoryid);

		// $this->params['alarmsubcategory'] = $this->m_securityevidence->getalarmsubcategory($subcategoryid);
		// $html                             = $this->load->view('dashboard/securityevidence/v_alarmsubcategory', $this->params, true);
		// $callback['html'] 							 	= $html;

		// echo "<pre>";
		// var_dump($this->params['alarmsubcategory']);die();
		// echo "<pre>";

		echo json_encode($callback);
	}

	function getalarmchild(){
		$alarmchildid           = $this->input->post("id");
		$callback['alarmchild'] = $this->m_securityevidence->getalarmchild($alarmchildid);

		// echo "<pre>";
		// var_dump($callback['alarmchild']);die();
		// echo "<pre>";

		echo json_encode($callback);
	}

	function searchreport(){
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$company       = $this->input->post("company");
		$vehicle       = $this->input->post("vehicle");
		$startdate     = $this->input->post("startdate");
		$enddate       = $this->input->post("enddate");
		$shour         = $this->input->post("shour");
		$ehour         = $this->input->post("ehour");
		$alarmtype     = $this->input->post("alarmtype");
		$periode       = $this->input->post("periode");
		$km            = $this->input->post("km");
		// $reporttype = $this->input->post("reporttype");
		$reporttype = 0;
		$alarmtypefromaster = array();

		if ($alarmtype != "All") {
			$alarmbymaster = $this->m_securityevidence->getalarmbytype($alarmtype);
			$alarmtypefromaster = array();
			for ($i=0; $i < sizeof($alarmbymaster); $i++) {
				$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
			}
		}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		//get vehicle
		$user_id         = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$privilegecode   = $this->sess->user_id_role;
		$user_dblive 	   = $this->sess->user_dblive;
		$user_id_fix     = $user_id;

		// $black_list  = array("401","428","451","478","602","603","608","609","652","653","658","659",
		// 					  "600","601","650","651"); //lane deviation & forward collation

		$black_list  = array("401","451","478","608","609","652","653","658","659");

		$street_register = $this->config->item('street_register');

		$nowdate  = date("Y-m-d");
		$nowday   = date("d");
		$nowmonth = date("m");
		$nowyear  = date("Y");
		$lastday  = date("t");

		$report     = "alarm_evidence_";
		$report_sum = "summary_";

		// print_r($periode);exit();

		if($periode == "custom"){
			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($startdate." ".$shour)));
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}elseif ($periode == "today") {
			$sdate = date("Y-m-d 00:00:00");
			$edate = date("Y-m-d 23:59:59");
			$datein = date("d-m-Y", strtotime($sdate));
		}else if($periode == "yesterday"){

			$sdate1 = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($sdate1)));
			$sdate = date("Y-m-d H:i:s", strtotime($sdate1));
		}else if($periode == "last7"){
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59"));

		}
		else if($periode == "last30"){
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59"));
		}
		else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($vehicle == "")
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

		if ($alarmtype == "")
		{
			$error .= "- Please Select Alarm Type! \n";
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

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";

		$this->dbtrip = $this->load->database("tensor_report", true);

		if ($company != "all") {
			$this->dbtrip->where("alarm_report_vehicle_company", $company);
		}

			if($vehicle == "all"){
				if($privilegecode == 0){
					$this->dbtrip->where("alarm_report_vehicle_user_id", $user_id_fix);
				}else if($privilegecode == 1){
					$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 2){
					$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 3){
					$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 4){
					$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 5){
					$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
				}else if($privilegecode == 6){
					$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
				}else{
					$this->dbtrip->where("alarm_report_vehicle_company",99999);
				}
			}else{
				// $vehicledevice = explode("@", $vehicle);
				// echo "<pre>";
				// var_dump($vehicle);die();
				// echo "<pre>";
				$this->dbtrip->where("alarm_report_imei", $vehicle);
			}

		$this->dbtrip->where("alarm_report_media", 0); //photo
		$this->dbtrip->where("alarm_report_start_time >=", $sdate);

		/* $nowday            = date("d");
		$end_day_fromEdate = date("d", strtotime($edate));

		if ($nowday == $end_day_fromEdate) {
			$edate = date("Y-m-d H:i:s");
		} */

		$this->dbtrip->where("alarm_report_start_time <=", $edate);
		if($km != ""){
			$this->dbtrip->where("alarm_report_location_start", "KM ".$km);
		}

		if ($alarmtype != "All") {
			$this->dbtrip->where_in('alarm_report_type', $alarmtypefromaster); //$alarmtype $alarmbymaster[0]['alarm_type']
		}
		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
		//$this->dbtrip->where("alarm_report_speed_status",1);		//buka untuk trial evalia
		//$this->dbtrip->like("alarm_report_location_start", "KM"); //buka untuk trial evalia
		$this->dbtrip->where("alarm_report_gpsstatus !=","");
		// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
		$this->dbtrip->order_by("alarm_report_start_time","desc");
		//$this->dbtrip->group_by("alarm_report_start_time");
		$q = $this->dbtrip->get($dbtable);
		//
		// echo "<pre>";
		// var_dump($q->result_array());die();
		// echo "<pre>";

		if ($q->num_rows>0)
		{
			$rows = $q->result_array();
			$thisreport = $rows;
		}else{
			$error .= "- No Data Alarm ! \n";
		}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}



		$datafix = array();
		for ($j=0; $j < sizeof($thisreport); $j++) {
			$alarmreportnamefix = "";
			$alarmreporttype = $thisreport[$j]['alarm_report_type'];
				if ($alarmreporttype == 626) {
					$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
				}elseif ($alarmreporttype == 627) {
					$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
				}elseif ($alarmreporttype == 702) {
					$alarmreportnamefix = "Distracted Driving Alarm Level One Start";
				}elseif ($alarmreporttype == 703) {
					$alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
				}elseif ($alarmreporttype == 752) {
					$alarmreportnamefix = "Distracted Driving Alarm Level One End";
				}elseif ($alarmreporttype == 753) {
					$alarmreportnamefix = "Distracted Driving Alarm Level Two End";
				}else {
					$alarmreportnamefix = $thisreport[$j]['alarm_report_name'];
				}

				array_push($datafix, array(
					"alarm_report_vehicle_id"       => $thisreport[$j]['alarm_report_vehicle_id'],
					"alarm_report_vehicle_no"       => $thisreport[$j]['alarm_report_vehicle_no'],
					"alarm_report_vehicle_name"     => $thisreport[$j]['alarm_report_vehicle_name'],
					"alarm_report_name"             => $alarmreportnamefix,
					"alarm_report_start_time"       => $thisreport[$j]['alarm_report_start_time'],
					"alarm_report_end_time"         => $thisreport[$j]['alarm_report_end_time'],
					"alarm_report_coordinate_start" => $thisreport[$j]['alarm_report_coordinate_start'],
					"alarm_report_coordinate_end"   => $thisreport[$j]['alarm_report_coordinate_end'],
					"alarm_report_location_start"   => $thisreport[$j]['alarm_report_location_start'],
					"alarm_report_speed" 			      => $thisreport[$j]['alarm_report_speed'],
					"alarm_report_speed_time" 		  => $thisreport[$j]['alarm_report_speed_time'],
					"alarm_report_speed_status" 	  => $thisreport[$j]['alarm_report_speed_status'],
					"alarm_report_jalur" 	          => $thisreport[$j]['alarm_report_jalur']
				));
		}

		$this->params['content'] = $datafix;
		$html                    = $this->load->view('newdashboard/securityevidence/v_securityevidence_reportresult', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $datafix;

		echo json_encode($callback);
	}

	//19 oktober 2020
	function searchreport3(){
		$vehicle          = explode("@", $this->input->post("vehicle"));
		$startdate        = $this->input->post("startdate");
		$shour            = $this->input->post("shour");
		$startdatefix     = date("Y-m-d H:i:s", strtotime($startdate." ".$shour.":00"));
		$enddate          = $this->input->post("enddate");
		$ehour            = $this->input->post("ehour");
		$enddatefix       = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour.":00"));
		$alarmtype        = $this->input->post("alarmtype");
		$alarmfix 			  = $this->input->post("alarmfix");
		$alarmtypeexplode = explode(",", $alarmfix);
		$loopalarmtype    = "";
		$where            = array();
		$report           = "alarm_";
		$m1 							= date("F", strtotime($startdatefix));
		$m2 							= date("F", strtotime($enddatefix));
		$year             = date("Y");

		// JIKA MELEBIHI SATU BULAN MAKA NOTIF DITOLAK
		// print_r($m1.'-'.$m2);
		if ($m1 != $m2) {
			$callback["msg"]   = "Please select the same month period";
			$callback["error"] = true;
		}

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
		$table            = strtolower($dbtable);

		// $vehicle.'-'.$startdate.'-'.$shour.'-'.$enddate.'-'.$ehour.'-'.$alarmtype
		// echo "<pre>";
		// var_dump($alarmtypeexplode);die();
		// echo "<pre>";

		if ($alarmtype != "All") {
			$thisreport = $this->m_securityevidence->searchthisreport($table, $vehicle[0], $startdatefix, $enddatefix, $alarmtypeexplode);
		}else {
			$thisreport = $this->m_securityevidence->searchthisreport($table, $vehicle[0], $startdatefix, $enddatefix, "ALL");
		}

		$datacoordinate  = array();
		for ($i=0; $i < sizeof($thisreport); $i++) {
			if ($thisreport[$i]['alarm_report_coordinate_start'] != "") {
				$coordstart = $thisreport[$i]['alarm_report_coordinate_start'];
					if (strpos($coordstart, '-') !== false) {
						$coordstart  = $coordstart;
					}else {
						$coordstart  = "-".$coordstart;
					}

					array_push($datacoordinate, array(
						"coordinate" => $coordstart
					));
			}
		}

		$datafix = array();
		for ($j=0; $j < sizeof($datacoordinate); $j++) {
			$coordexplode = explode(",", $datacoordinate[$j]['coordinate']);

			$position     = $this->gpsmodel->GeoReverse($coordexplode[0], $coordexplode[1]);
				array_push($datafix, array(
					"alarm_report_vehicle_id"       => $thisreport[$j]['alarm_report_vehicle_id'],
					"alarm_report_vehicle_no"       => $thisreport[$j]['alarm_report_vehicle_no'],
					"alarm_report_vehicle_name"     => $thisreport[$j]['alarm_report_vehicle_name'],
					"alarm_report_name"             => $thisreport[$j]['alarm_report_name'],
					"alarm_report_start_time"       => $thisreport[$j]['alarm_report_start_time'],
					"alarm_report_end_time"         => $thisreport[$j]['alarm_report_end_time'],
					"alarm_report_coordinate_start" => $thisreport[$j]['alarm_report_coordinate_start'],
					"alarm_report_coordinate_end"   => $thisreport[$j]['alarm_report_coordinate_end'],
					"position"                      => $position->display_name
				));
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		$this->params['content'] = $datafix;
		$html                    = $this->load->view('newdashboard/securityevidence/v_securityevidence_reportresult', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $datafix;

		// echo "<pre>";
		// var_dump($getdata);die();
		// echo "<pre>";
		echo json_encode($callback);
	}

	function searchreport2(){
		$vehicle          = explode("@", $this->input->post("vehicle"));
		$startdate        = $this->input->post("startdate");
		$shour            = $this->input->post("shour");
		$startdatefix     = date("Y-m-d H:i:s", strtotime($startdate." ".$shour.":00"));
		$enddate          = $this->input->post("enddate");
		$ehour            = $this->input->post("ehour");
		$enddatefix       = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour.":00"));
		$alarmtype        = $this->input->post("alarmtype");
		$alarmfix         = $this->input->post("alarmfix");
		$alarmcategory    = isset($_POST['alarmcategory']) ? $_POST['alarmcategory']:                       "All";
		$alarmsubcategory = isset($_POST['alarmsubcategory']) ? $_POST['alarmsubcategory']:                       "All";
		$alarmchild       = isset($_POST['alarmchild']) ? $_POST['alarmchild']:                       "All";
		$alarmtypeexplode = explode(",", $alarmfix);
		$loopalarmtype    = "";
		$where            = array();
		$pratext          = "alarm_";
		$month            = date("F");
		$year             = date("Y");
		$table            = strtolower($pratext.$month.'_'.$year);
		$datafix 					= array();

		// $vehicle.'-'.$startdatefix.'-'.$enddatefix.'-'.$shour.'-'.$startdatefix.'-'.$enddatefix.'-'.$ehour.'-'.$alarmtype
		// echo "<pre>";
		// var_dump($alarmchild);die();
		// echo "<pre>";
		if ($alarmchild == "All") {
			$getalarmbysubcat = $this->m_securityevidence->getalarmchild($alarmsubcategory);
			for ($i=0; $i < sizeof($getalarmbysubcat); $i++) {
				$getdata = $this->m_securityevidence->searchthisreport($table, $vehicle[0], $startdatefix, $enddatefix, $getalarmbysubcat[$i]['alarm_type']);
					for ($j=0; $j < sizeof($getdata); $j++) {
						array_push($datafix, array(
							"alarm_report_id"               => $getdata[$j]['alarm_report_id'],
							"alarm_report_vehicle_id"       => $getdata[$j]['alarm_report_vehicle_id'],
							"alarm_report_vehicle_no"       => $getdata[$j]['alarm_report_vehicle_no'],
							"alarm_report_vehicle_name"     => $getdata[$j]['alarm_report_vehicle_name'],
							"alarm_report_vehicle_type"     => $getdata[$j]['alarm_report_vehicle_type'],
							"alarm_report_type"             => $getdata[$j]['alarm_report_type'],
							"alarm_report_name"             => $getdata[$j]['alarm_report_name'],
							"alarm_report_media"            => $getdata[$j]['alarm_report_media'],
							"alarm_report_channel"          => $getdata[$j]['alarm_report_channel'],
							"alarm_report_gpsstatus"        => $getdata[$j]['alarm_report_gpsstatus'],
							"alarm_report_start_time"       => $getdata[$j]['alarm_report_start_time'],
							"alarm_report_end_time"         => $getdata[$j]['alarm_report_end_time'],
							"alarm_report_update_time"      => $getdata[$j]['alarm_report_update_time'],
							"alarm_report_duration"         => $getdata[$j]['alarm_report_duration'],
							"alarm_report_duration_sec"     => $getdata[$j]['alarm_report_duration_sec'],
							"alarm_report_location_start"   => $getdata[$j]['alarm_report_location_start'],
							"alarm_report_location_end"     => $getdata[$j]['alarm_report_location_end'],
							"alarm_report_geofence_start"   => $getdata[$j]['alarm_report_geofence_start'],
							"alarm_report_geofence_end"     => $getdata[$j]['alarm_report_geofence_end'],
							"alarm_report_coordinate_start" => $getdata[$j]['alarm_report_coordinate_start'],
							"alarm_report_coordinate_end"   => $getdata[$j]['alarm_report_coordinate_end'],
							"alarm_report_size"             => $getdata[$j]['alarm_report_size'],
							"alarm_report_downloadurl"      => $getdata[$j]['alarm_report_downloadurl'],
							"alarm_report_path"             => $getdata[$j]['alarm_report_path'],
							"alarm_report_fileurl"          => $getdata[$j]['alarm_report_fileurl']
						));
					}
			}
		}else {
			$getdata = $this->m_securityevidence->searchthisreport($table, $vehicle[0], $startdatefix, $enddatefix, $alarmchild);
				for ($j=0; $j < sizeof($getdata); $j++) {
					array_push($datafix, array(
						"alarm_report_id"               => $getdata[$j]['alarm_report_id'],
						"alarm_report_vehicle_id"       => $getdata[$j]['alarm_report_vehicle_id'],
						"alarm_report_vehicle_no"       => $getdata[$j]['alarm_report_vehicle_no'],
						"alarm_report_vehicle_name"     => $getdata[$j]['alarm_report_vehicle_name'],
						"alarm_report_vehicle_type"     => $getdata[$j]['alarm_report_vehicle_type'],
						"alarm_report_type"             => $getdata[$j]['alarm_report_type'],
						"alarm_report_name"             => $getdata[$j]['alarm_report_name'],
						"alarm_report_media"            => $getdata[$j]['alarm_report_media'],
						"alarm_report_channel"          => $getdata[$j]['alarm_report_channel'],
						"alarm_report_gpsstatus"        => $getdata[$j]['alarm_report_gpsstatus'],
						"alarm_report_start_time"       => $getdata[$j]['alarm_report_start_time'],
						"alarm_report_end_time"         => $getdata[$j]['alarm_report_end_time'],
						"alarm_report_update_time"      => $getdata[$j]['alarm_report_update_time'],
						"alarm_report_duration"         => $getdata[$j]['alarm_report_duration'],
						"alarm_report_duration_sec"     => $getdata[$j]['alarm_report_duration_sec'],
						"alarm_report_location_start"   => $getdata[$j]['alarm_report_location_start'],
						"alarm_report_location_end"     => $getdata[$j]['alarm_report_location_end'],
						"alarm_report_geofence_start"   => $getdata[$j]['alarm_report_geofence_start'],
						"alarm_report_geofence_end"     => $getdata[$j]['alarm_report_geofence_end'],
						"alarm_report_coordinate_start" => $getdata[$j]['alarm_report_coordinate_start'],
						"alarm_report_coordinate_end"   => $getdata[$j]['alarm_report_coordinate_end'],
						"alarm_report_size"             => $getdata[$j]['alarm_report_size'],
						"alarm_report_downloadurl"      => $getdata[$j]['alarm_report_downloadurl'],
						"alarm_report_path"             => $getdata[$j]['alarm_report_path'],
						"alarm_report_fileurl"          => $getdata[$j]['alarm_report_fileurl']
					));
				}
		}

		// $thisreport = $this->m_securityevidence->searchthisreportall($table, $vehicle[0], $startdatefix, $enddatefix);


		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";


		$this->params['content'] = $datafix;
		$html                    = $this->load->view('newdashboard/securityevidence/v_securityevidence_reportresult', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $datafix;

		// echo "<pre>";
		// var_dump($thisreport);die();
		// echo "<pre>";
		echo json_encode($callback);
	}

	function getinfodetail_new(){
		$alert_id        = $this->input->post("alert_id");
		$sdate           = $this->input->post("sdate");
		$report          = "alarm_evidence_";
		$reportoverspeed = "overspeed_";
		$monthforparam   = date("m", strtotime($sdate));
		$m1              = date("F", strtotime($sdate));
		$year            = date("Y", strtotime($sdate));
		$jalur           = "";

		// echo "<pre>";
		// var_dump($monthforparam);die();
		// echo "<pre>";

		switch ($m1)
		{
			case "January":
						$dbtable    = $report."januari_".$year;
						$dbtableoverspeed = $reportoverspeed."januari_".$year;
			break;
			case "February":
						$dbtable = $report."februari_".$year;
						$dbtableoverspeed = $reportoverspeed."februari_".$year;
			break;
			case "March":
						$dbtable = $report."maret_".$year;
						$dbtableoverspeed = $reportoverspeed."maret_".$year;
			break;
			case "April":
						$dbtable = $report."april_".$year;
						$dbtableoverspeed = $reportoverspeed."april_".$year;
			break;
			case "May":
						$dbtable = $report."mei_".$year;
						$dbtableoverspeed = $reportoverspeed."mei_".$year;
			break;
			case "June":
						$dbtable = $report."juni_".$year;
						$dbtableoverspeed = $reportoverspeed."juni_".$year;
			break;
			case "July":
						$dbtable = $report."juli_".$year;
						$dbtableoverspeed = $reportoverspeed."juli_".$year;
			break;
			case "August":
						$dbtable = $report."agustus_".$year;
						$dbtableoverspeed = $reportoverspeed."agustus_".$year;
			break;
			case "September":
						$dbtable = $report."september_".$year;
						$dbtableoverspeed = $reportoverspeed."september_".$year;
			break;
			case "October":
						$dbtable = $report."oktober_".$year;
						$dbtableoverspeed = $reportoverspeed."oktober_".$year;
			break;
			case "November":
						$dbtable = $report."november_".$year;
						$dbtableoverspeed = $reportoverspeed."november_".$year;
			break;
			case "December":
						$dbtable = $report."desember_".$year;
						$dbtableoverspeed = $reportoverspeed."desember_".$year;
			break;
		}
		$table      = strtolower($dbtable);

		$reportdetail               = $this->m_securityevidence->getdetailreport($table, $alert_id, $sdate);
		$reportdetailvideo          = $this->m_securityevidence->getdetailreportvideo($table, $alert_id, $sdate);
		$reportdetaildecode         = explode("|", $reportdetail[0]['alarm_report_gpsstatus']);

		// echo "<pre>";
		// var_dump($reportdetailvideo);die();
		// echo "<pre>";

		$urlvideofix  = "";
		$videoalertid = "";
		$imagealertid = "";
			if (sizeof($reportdetailvideo) > 0) {
				$urlvideofix  = $reportdetailvideo[0]['alarm_report_downloadurl'];
				$videoalertid = $reportdetailvideo[0]['alarm_report_id'];
			}else {
				$urlvideofix  = "0";
				$videoalertid = "0";
			}

			if (sizeof($reportdetail) > 0) {
				$imagealertid = $reportdetail[0]['alarm_report_id'];
			}else {
				$imagealertid = "0";
			}

			if ($reportdetail[0]['alarm_report_coordinate_start'] != "") {
				$coordstart = $reportdetail[0]['alarm_report_coordinate_start'];
					if (strpos($coordstart, '-') !== false) {
						$coordstart  = $coordstart;
					}else {
						$coordstart  = "-".$coordstart;
					}

				$coord       = explode(",", $coordstart);
				$position    = $this->gpsmodel->GeoReverse($coord[0], $coord[1]);
				$rowgeofence = $this->getGeofence_location_live($coord[1], $coord[0], $this->sess->user_dblive);

				if($rowgeofence == false){
					$geofence_id           = 0;
					$geofence_name         = "";
					$geofence_speed        = 0;
					$geofence_speed_muatan = "";
					$geofence_type         = "";
					$geofence_speed_limit  = 0;
				}else{
					$geofence_id           = $rowgeofence->geofence_id;
					$geofence_name         = $rowgeofence->geofence_name;
					$geofence_speed        = $rowgeofence->geofence_speed;
					$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
					$geofence_type         = $rowgeofence->geofence_type;

					if($jalur == "muatan"){
						$geofence_speed_limit = $geofence_speed_muatan;
					}else if($jalur == "kosongan"){
						$geofence_speed_limit = $geofence_speed;
					}else{
						$geofence_speed_limit = 0;
					}
				}
			}

			$speedgps = number_format($reportdetaildecode[4]/10, 1, '.', '');
			//$speedgps = $reportdetail[0]['alarm_report_speed']; //by speed gps TK510

			$alarm_report_coordinate_start = $reportdetail[0]['alarm_report_coordinate_start'];


		// echo "<pre>";
		// var_dump($alarm_report_coordinate_start);die();
		// echo "<pre>";

		$this->params['content']              = $reportdetail;
		$this->params['coordinate']           = $alarm_report_coordinate_start;
		$this->params['position']             = $position->display_name;
		$this->params['urlvideo']             = $urlvideofix;

		$this->params['geofence_name']        = $geofence_name;
		$this->params['geofence_speed_limit'] = $geofence_speed_limit;
		$this->params['jalur']                = $jalur;
		$this->params['speed']                = $speedgps;
		$this->params['videoalertid']         = $videoalertid;
		$this->params['imagealertid']         = $imagealertid;
		$this->params['table'] 			          = $table;
		$this->params['monthforparam'] 			  = $monthforparam;
		$this->params['year'] 			          = $year;
		$this->params['user_id_role'] 			  = $this->sess->user_id_role;
		$html                                 = $this->load->view('newdashboard/securityevidence/v_securityevidence_informationdetail', $this->params, true);
		$callback["html"]                     = $html;
		$callback["report"]                   = $reportdetail;
		echo json_encode($callback);
	}

	function getinfodetail(){

		$alert_id        = $this->input->post("alert_id");
		$sdate           = $this->input->post("sdate");
		$report          = "alarm_evidence_";
		$reportoverspeed = "overspeed_";
		$m1              = date("F", strtotime($sdate));
		$year            = date("Y", strtotime($sdate));

		switch ($m1)
		{
			case "January":
            $dbtable    = $report."januari_".$year;
						$dbtableoverspeed = $reportoverspeed."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
						$dbtableoverspeed = $reportoverspeed."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
						$dbtableoverspeed = $reportoverspeed."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
						$dbtableoverspeed = $reportoverspeed."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
						$dbtableoverspeed = $reportoverspeed."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
						$dbtableoverspeed = $reportoverspeed."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
						$dbtableoverspeed = $reportoverspeed."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
						$dbtableoverspeed = $reportoverspeed."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
						$dbtableoverspeed = $reportoverspeed."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
						$dbtableoverspeed = $reportoverspeed."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
						$dbtableoverspeed = $reportoverspeed."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
						$dbtableoverspeed = $reportoverspeed."desember_".$year;
			break;
		}
		$table      = strtolower($dbtable);

		$reportdetail               = $this->m_securityevidence->getdetailreport($table, $alert_id, $sdate);
		$reportdetailvideo          = $this->m_securityevidence->getdetailreportvideo($table, $alert_id, $sdate);
		$reportdetaildecode         = explode("|", $reportdetail[0]['alarm_report_gpsstatus']);

		// echo "<pre>";
		// var_dump($reportdetailvideo);die();
		// echo "<pre>";

		// GET DETAIL DRIVER START
		// RULES
		// 1. CARI DATA DRIVER HISTORY BERDASARKAN TANGGAL YANG ADA DI INFORMATION DETAIL
		// 2. BANDINGKAN DENGAN TANGGAL SUBMIT PADA DRIVER HISTORY
		// 3. CARI DATA TANGGAL SUBMIT YANG <= TANGGAL DARI INFORMATION DETAIL
		$getvehicle                   = $this->m_securityevidence->getvehiclebydev("vehicle", $reportdetail[0]['alarm_report_vehicle_id'], $reportdetail[0]['alarm_report_vehicle_type']);
		$getdriverfromdriverhistory   = $this->m_securityevidence->getdriverhist("driver_history", $getvehicle[0]['vehicle_id'], $reportdetail[0]['alarm_report_start_time']);

		if(count($getdriverfromdriverhistory)>0){

			$getdriverfromdriverdata      = $this->m_securityevidence->getdriver("driver", $getdriverfromdriverhistory[0]['driver_history_driver_id']);
			$getdriverimagefromdriverdata = $this->m_securityevidence->getdriverimage("driver_image", $getdriverfromdriverhistory[0]['driver_history_driver_id']);

			$this->params['detaildriver']         = $getdriverfromdriverdata;
			$this->params['driverimage']          = $getdriverimagefromdriverdata;
		}else{
			//get new driver from driver change (MV03)
			$getvehicle 			                   = $this->getvehicle_mv03("vehicle", $reportdetail[0]['alarm_report_imei']);
			$getdriverfromdriverhistory_change 	 = $this->getdriverhist_change("ts_driver_change", $getvehicle[0]['vehicle_mv03'], $reportdetail[0]['alarm_report_start_time']);

			if(count($getdriverfromdriverhistory_change)>0){

				$getdriverfromdriverdata 			       = $this->getdriver_byidcard("driver", $getdriverfromdriverhistory_change[0]['change_driver_id']);
					if (sizeof($getdriverfromdriverdata) > 0 ) {
						$getdriverimagefromdriverdata 			 = $this->getdriverimage_byidcard("driver_image", $getdriverfromdriverdata[0]['driver_id']);

						$this->params['detaildriver']         = $getdriverfromdriverdata;
						$this->params['driverimage']          = $getdriverimagefromdriverdata;
					}else {
						$this->params['detaildriver']         = 0;
						$this->params['driverimage']          = 0;
					}
			}else{

				$getdriverfromdriverdata = array("");
				$getdriverimagefromdriverdata = array("");
			}
		}
		// GET DETAIL DRIVER END

		// GET OVERSPEED ALERT START
		$getoverspeedalarm = $this->m_securityevidence->getoverspeed($dbtableoverspeed, $getvehicle[0]['vehicle_device'], $reportdetail[0]['alarm_report_start_time']);
		if (sizeof($getoverspeedalarm)) {
			$jalur = $getoverspeedalarm[0]['overspeed_report_jalur'];
		}else {
			$jalur = "";
		}

		// GET OVERSPEED ALERT END

		// echo "<pre>";
		// var_dump($getvehicle[0]['vehicle_device'].' || '.$reportdetail[0]['alarm_report_start_time']);die();
		// echo "<pre>";

		$urlvideofix = "";
			if (sizeof($reportdetailvideo) > 0) {
				$urlvideofix = $reportdetailvideo[0]['alarm_report_downloadurl'];
			}else {
				$urlvideofix = "0";
			}

			if ($reportdetail[0]['alarm_report_coordinate_start'] != "") {
				$coordstart = $reportdetail[0]['alarm_report_coordinate_start'];
					if (strpos($coordstart, '-') !== false) {
						$coordstart  = $coordstart;
					}else {
						$coordstart  = "-".$coordstart;
					}

				$coord       = explode(",", $coordstart);
				$position    = $this->gpsmodel->GeoReverse($coord[0], $coord[1]);
				$rowgeofence = $this->getGeofence_location_live($coord[1], $coord[0], $this->sess->user_dblive);

				if($rowgeofence == false){
					$geofence_id           = 0;
					$geofence_name         = "";
					$geofence_speed        = 0;
					$geofence_speed_muatan = "";
					$geofence_type         = "";
					$geofence_speed_limit  = 0;
				}else{
					$geofence_id           = $rowgeofence->geofence_id;
					$geofence_name         = $rowgeofence->geofence_name;
					$geofence_speed        = $rowgeofence->geofence_speed;
					$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
					$geofence_type         = $rowgeofence->geofence_type;

					if($jalur == "muatan"){
						$geofence_speed_limit = $geofence_speed_muatan;
					}else if($jalur == "kosongan"){
						$geofence_speed_limit = $geofence_speed;
					}else{
						$geofence_speed_limit = 0;
					}
				}
			}

			$speedgps = number_format($reportdetaildecode[4]/10, 1, '.', '');
			//$speedgps = $reportdetail[0]['alarm_report_speed']; //by speed gps TK510


		// echo "<pre>";
		// var_dump($overspeeddata);die();
		// echo "<pre>";

		$this->params['content']              = $reportdetail;
		$this->params['position']             = $position->display_name;
		$this->params['urlvideo']             = $urlvideofix;

		$this->params['geofence_name']        = $geofence_name;
		$this->params['geofence_speed_limit'] = $geofence_speed_limit;
		$this->params['jalur']                = $jalur;
		$this->params['speed']                = $speedgps;
		$html                                 = $this->load->view('newdashboard/securityevidence/v_securityevidence_informationdetail', $this->params, true);
		$callback["html"]                     = $html;
		$callback["report"]                   = $reportdetail;
		echo json_encode($callback);
	}

	function getGeofence_location_live($longitude, $latitude, $vehicle_dblive) {
		$this->db = $this->load->database($vehicle_dblive, true);
		$lng      = $longitude;
		$lat      = $latitude;
		$geo_name = "''";
		$sql      = sprintf("SELECT geofence_name,geofence_id,geofence_speed,geofence_speed_muatan,geofence_type
												FROM webtracking_geofence
												WHERE TRUE
												AND (geofence_name <> %s)
												AND geofence_type = 'ROAD'
												AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
												AND (geofence_status = 1)
												ORDER BY geofence_id DESC LIMIT 1 OFFSET 0", $geo_name, $lng, $lat);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0){
			$row = $q->row();
        /*$total = $q->num_rows();
        for ($i=0;$i<$total;$i++){
				$data = $row[$i]->geofence_name;
				$data = $row;
				return $data;
      }*/
			$data = $row;
			return $data;
		}else{
			$data = false;
      return $data;
    }
	}

	function getsessionlogin(){
		$device          = $_POST['device'];
		$url             = $_POST['url'];
		$username        = "IND.LacakMobil";
		$password        = "000000";

		$getthissession  = $this->m_securityevidence->getsession();
		$urlfix          = $url.$getthissession[0]['sess_value'];

		// GET LOGIN DENGAN SESSION LAMA
		$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
			if ($loginlama) {
				$loginlamadecode = json_decode($loginlama);
				if ($loginlamadecode->message == "Session does not exist!") {
					$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
					$loginbarudecode = json_decode($loginbaru);
					$urlfix          = $url.$loginbarudecode->jsession;
				}
			}

			// echo "<pre>";
			// var_dump($urlfix);die();
			// echo "<pre>";

		$this->params['content'] = file_get_contents($urlfix);
		$this->params['urlfix']  = $urlfix;
		$html                    = $this->load->view('dashboard/livestream/v_livestream', $this->params, true);
		// echo "<pre>";
		// var_dump($this->params['content']);die();
		// echo "<pre>";
		echo json_encode(array("content" => $html, "urlfix" => $urlfix));
	}

	function securityevidence_realtimealert(){
		$user_id       = $this->sess->user_id;
		$privilegecode = $this->sess->user_id_role;
		$user_parent   = $this->sess->user_parent;
		$user_company  = $this->sess->user_company;

		$lasttimealert = $_POST['lasttimealert_evidence'];
		$limitalert    = $_POST['limitalert'];

		$m1            = date("F");
		$year          = date("Y");

		$report     = "alarm_evidence_";

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

				$alarmbymaster = $this->m_securityevidence->getalarmbytypeforevidence();
				$alarmtypefromaster = array();
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}

				// echo "<pre>";
				// var_dump($alarmtypefromaster);die();
				// echo "<pre>";

		if ($lasttimealert == "") {
			$lasttimealert = date("Y-m-d H:i:s", strtotime('-24 hour'));
			$evidence      = $this->m_securityevidence->getevidencealert($dbtable, 1, $lasttimealert, $alarmtypefromaster);
			$simultan 	   = "no";
		}else {
			// $lasttimealert = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($lasttimealert)));
			$evidence      = $this->m_securityevidence->getevidencealert($dbtable, 1, $lasttimealert, $alarmtypefromaster); // ganti dengan limit 1
			$simultan 	   = "yes";
		}

		// echo "<pre>";
		// var_dump($evidence);die();
		// echo "<pre>";

		if (sizeof($evidence) > 0) {
			$vdevice           = $evidence[0]['alarm_report_vehicle_id']."@".$evidence[0]['alarm_report_vehicle_type'];
			$datavehicle       = $this->m_securityevidence->getdevicebydevID($vdevice);

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($datavehicle[0]['vehicle_company'] == $user_company) {
					if (sizeof($evidence) > 0) {
						$reportdetaildecode = explode("|", $evidence[0]['alarm_report_gpsstatus']);
						$speedgps           = number_format($reportdetaildecode[4]/10, 0, '.', '');

						$datanya = array(
							"isfatigue"  				 => "yes",
							"vehicle_no"         => $evidence[0]['alarm_report_vehicle_no'],
							"gps_alert"          => $evidence[0]['alarm_report_name'],
							"gps_time"           => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time'])), //+ 1 * 3600
							"gps_latitude_real"  => $evidence[0]['alarm_report_coordinate_start'],
							"gps_longitude_real" => $evidence[0]['alarm_report_coordinate_start'],
							"position"           => $evidence[0]['alarm_report_location_start'],
							"gps_speed"          => $speedgps,
						);
						echo json_encode(array("code" => 200, "data" => $datanya, "simultan" => $simultan, "lasttime" => $evidence[0]['alarm_report_start_time']));
					}else {
						echo json_encode(array("code" => 400, "simultan" => $simultan, "lasttime" => $lasttimealert));
					}
				}
			}else {
				if (sizeof($evidence) > 0) {
					$reportdetaildecode = explode("|", $evidence[0]['alarm_report_gpsstatus']);
					$speedgps           = number_format($reportdetaildecode[4]/10, 0, '.', '');

					$datanya = array(
						"isfatigue" 				 => "yes",
						"vehicle_no"         => $evidence[0]['alarm_report_vehicle_no'],
						"gps_alert"          => $evidence[0]['alarm_report_name'],
						"gps_time"           => date("Y-m-d H:i:s", strtotime($evidence[0]['alarm_report_start_time'])), // + 1 * 3600
						"gps_latitude_real"  => $evidence[0]['alarm_report_coordinate_start'],
						"gps_longitude_real" => $evidence[0]['alarm_report_coordinate_start'],
						"position"           => $evidence[0]['alarm_report_location_start'],
						"gps_speed"          => $speedgps,
					);
					echo json_encode(array("code" => 200, "data" => $datanya, "simultan" => $simultan, "lasttime" => $evidence[0]['alarm_report_start_time']));
				}else {
					echo json_encode(array("code" => 400, "simultan" => $simultan, "lasttime" => $lasttimealert));
				}
			}
		}else {
			echo json_encode(array("code" => 400, "simultan" => $simultan, "lasttime" => $lasttimealert));
		}
	}

	function realtimealertnew(){
		$user_id       = $this->sess->user_id;
		$privilegecode = $this->sess->user_id_role;
		$user_parent   = $this->sess->user_parent;
		$user_company  = $this->sess->user_company;

		$street_onduty = array("PORT BIB","PORT BIR","PORT TIA",
								//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
								"ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
								"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
								//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
								//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",

								"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
								"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
								"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
								"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",

								"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
								"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
								"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
								"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
								"PORT BIB - Antrian","Port BIB - Antrian"
							);

		$lasttimealert = $_POST['lasttimealert'];
		$limitalert    = $_POST['limitalert'];
		$intervalke    = $_POST['intervalke'];
		$urutandblive  = 1;

		if ($intervalke > 6) {
			$urutandblive = 1;
		}else {
			$urutandblive = $intervalke;
		}

		// $dbnamelive = "webtracking_gps_temanindobara_live_".$urutandblive;
		$dbnamelive = "webtracking_gps_demo_live";

		if ($lasttimealert == 0) {
			$lasttimealert = date("Y-m-d H:i:s", strtotime('-24 hour'));
			$alert         = $this->m_securityevidence->getalertnow($dbnamelive, $limitalert, $lasttimealert);
			$simultan   	 = "no";
		}else {
			$lasttimealert = date("Y-m-d H:i:s", strtotime("-70 Minutes", strtotime($lasttimealert)));
			$alert         = $this->m_securityevidence->getalertnow($dbnamelive, 1, $lasttimealert); // ganti dengan limit 1
			$simultan   	 = "yes";
		}

		// echo "<pre>";
		// var_dump($alert);die();
		// echo "<pre>";

			if ($alert) {
				$urutandblive += 1;
				$datafix = array();
					for ($i=0; $i < sizeof($alert); $i++) {
						$vdevice           = $alert[$i]['gps_name']."@".$alert[$i]['gps_host'];
						$datavehicle       = $this->m_securityevidence->getdevicebydevID($vdevice);
							if ($datavehicle) {
								if ($datavehicle[0]['vehicle_typeunit'] == 0) {
									$jalur_name        = $this->m_securityevidence->get_jalurname($alert[$i]['gps_course']);
									$positionalert     = $this->gpsmodel->GeoReverse($alert[$i]['gps_latitude_real'], $alert[$i]['gps_longitude_real']);
									$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($alert[$i]['gps_longitude_real'], $alert[$i]['gps_latitude_real'], $datavehicle[0]['vehicle_user_id'], $datavehicle[0]['vehicle_dbname_live']);
									$speedlimitfix 		 = "";

									// echo "<pre>";
									// var_dump($geofence_location);die();
									// echo "<pre>";


									if ($geofence_location) {
										$geofencefix = $geofence_location[0]->geofence_name;

										if ($jalur_name == "kosongan") {
											$speedlimitfix = $geofence_location[0]->geofence_speed_alias;
										}elseif ($jalur_name == "muatan") {
											$speedlimitfix = $geofence_location[0]->geofence_speed_muatan_alias;
										}

									}else {
										$geofencefix = "Out Of Geofence";
									}

									if ($positionalert->display_name != "Unknown Location!") {
										$positionexplode = explode(",", $positionalert->display_name);
										$position = $positionexplode[0];
									}else {
										$position = $positionalert->display_name;
									}

									// if (in_array($position, $street_onduty)){
										if ($privilegecode == 5 || $privilegecode == 6) {
											if ($datavehicle[0]['vehicle_company'] == $user_company) {
												array_push($datafix, array(
													"jalur_name"				 => $jalur_name,
													"datasimultan" 			 => $simultan,
													"vehicle_no"         => $datavehicle[0]['vehicle_no'],
													"vehicle_name"       => $datavehicle[0]['vehicle_name'],
													"gps_id"             => $alert[$i]['gps_id'],
													"gps_name"           => $alert[$i]['gps_name'],
													"gps_host"           => $alert[$i]['gps_host'],
													"gps_status"         => $alert[$i]['gps_status'],
													"gps_latitude_real"  => $alert[$i]['gps_latitude_real'],
													"gps_longitude_real" => $alert[$i]['gps_longitude_real'],
													"gps_speed"          => (round($alert[$i]['gps_speed']*1.853) - 3),
													"gps_speed_limit"    => $speedlimitfix,
													"gps_alert"          => "Overspeed",
													"gps_time"           => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($alert[$i]['gps_time']))),
													"geofence"           => $geofencefix,
													"position" 					 => $position
												));
											}
										}else {
											array_push($datafix, array(
												"jalur_name"				 => $jalur_name,
												"datasimultan" 			 => $simultan,
												"vehicle_no"         => $datavehicle[0]['vehicle_no'],
												"vehicle_name"       => $datavehicle[0]['vehicle_name'],
												"gps_id"             => $alert[$i]['gps_id'],
												"gps_name"           => $alert[$i]['gps_name'],
												"gps_host"           => $alert[$i]['gps_host'],
												"gps_status"         => $alert[$i]['gps_status'],
												"gps_latitude_real"  => $alert[$i]['gps_latitude_real'],
												"gps_longitude_real" => $alert[$i]['gps_longitude_real'],
												"gps_speed"          => round($alert[$i]['gps_speed']*1.853),
												"gps_speed_limit"    => $speedlimitfix,
												"gps_alert"          => "Overspeed",
												"gps_time"           => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($alert[$i]['gps_time']))),
												"geofence"           => $geofencefix,
												"position" 					 => $position
											));
										}
									// }
								}

							}
					}
					// echo "<pre>";
					// var_dump($datafix);die();
					// echo "<pre>";
					$callback['code']       = "200";
					$callback['msg']        = "success";
						if ($lasttimealert == 0) {
							$callback['gps_time']   = date("Y-m-d H:i:s", strtotime("+24 hour", strtotime($lasttimealert)));
						}else {
							$callback['gps_time']   = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($lasttimealert)));
						}
					$callback['intervalke'] = $urutandblive;
					$callback['data']       = $datafix;
					echo json_encode($callback);
			}else {
				$urutandblive += 1;
				$callback['code']       = "400";
					// if ($lasttimealert == 0) {
					// 	$callback['gps_time']   = date("Y-m-d H:i:s", strtotime("+24 hour", strtotime($lasttimealert)));
					// }else {
					// 	$callback['gps_time']   = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($lasttimealert)));
					// }
				// $callback['intervalke'] = ($intervalke + 1);
				$callback['intervalke'] = $urutandblive;
				echo json_encode($callback);
			}


	}

	function realtimealert(){
		$user_id       = $this->sess->user_id;
		$privilegecode = $this->sess->user_id_role;
		$user_parent   = $this->sess->user_parent;
		$user_company  = $this->sess->user_company;

		$street_onduty = array("PORT BIB","PORT BIR","PORT TIA",
								//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
								"ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
								"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
								//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
								//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",

								"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
								"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
								"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
								"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",

								"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8",
								"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
								"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
								"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
								"PORT BIB - Antrian","Port BIB - Antrian"
							);

		$lasttimealert = $_POST['lasttimealert'];
		$limitalert    = $_POST['limitalert'];

		if ($lasttimealert == 0) {
			$lasttimealert = date("Y-m-d H:i:s", strtotime('-24 hour'));
			$alert         = $this->m_securityevidence->getalertnow("webtracking_gps_demo_live", $limitalert, $lasttimealert);
			$simultan   	 = "no";
		}else {
			$lasttimealert = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($lasttimealert)));
			$alert         = $this->m_securityevidence->getalertnow("webtracking_gps_demo_live", 1, $lasttimealert); // ganti dengan limit 1
			$simultan   	 = "yes";
		}

		// echo "<pre>";
		// var_dump($lasttimealert);die();
		// echo "<pre>";

			if ($alert) {
				$datafix = array();
					for ($i=0; $i < sizeof($alert); $i++) {
						$vdevice           = $alert[$i]['gps_name']."@".$alert[$i]['gps_host'];
						$datavehicle       = $this->m_securityevidence->getdevicebydevID($vdevice);
						$jalur_name        = $this->m_securityevidence->get_jalurname($alert[$i]['gps_course']);
						$positionalert     = $this->gpsmodel->GeoReverse($alert[$i]['gps_latitude_real'], $alert[$i]['gps_longitude_real']);
						$geofence_location = $this->m_poipoolmaster->getGeofence_location_other_live($alert[$i]['gps_longitude_real'], $alert[$i]['gps_latitude_real'], $datavehicle[0]['vehicle_user_id'], $datavehicle[0]['vehicle_dbname_live']);
						$speedlimitfix 		 = "";

						// echo "<pre>";
						// var_dump($geofence_location);die();
						// echo "<pre>";


						if ($geofence_location) {
							$geofencefix = $geofence_location[0]->geofence_name;

							if ($jalur_name == "kosongan") {
								$speedlimitfix = $geofence_location[0]->geofence_speed_alias;
							}elseif ($jalur_name == "muatan") {
								$speedlimitfix = $geofence_location[0]->geofence_speed_muatan_alias;
							}

						}else {
							$geofencefix = "Out Of Geofence";
						}

						if ($positionalert->display_name != "Unknown Location!") {
							$positionexplode = explode(",", $positionalert->display_name);
							$position = $positionexplode[0];
						}else {
							$position = $positionalert->display_name;
						}

						if (in_array($position, $street_onduty)){
							if ($privilegecode == 5 || $privilegecode == 6) {
								if ($datavehicle[0]['vehicle_company'] == $user_company) {
									array_push($datafix, array(
										"jalur_name"				 => $jalur_name,
										"datasimultan" 			 => $simultan,
										"vehicle_no"         => $datavehicle[0]['vehicle_no'],
										"vehicle_name"       => $datavehicle[0]['vehicle_name'],
										"gps_id"             => $alert[$i]['gps_id'],
										"gps_name"           => $alert[$i]['gps_name'],
										"gps_host"           => $alert[$i]['gps_host'],
										"gps_status"         => $alert[$i]['gps_status'],
										"gps_latitude_real"  => $alert[$i]['gps_latitude_real'],
										"gps_longitude_real" => $alert[$i]['gps_longitude_real'],
										"gps_speed"          => (round($alert[$i]['gps_speed']*1.853) - 3),
										"gps_speed_limit"    => $speedlimitfix,
										"gps_alert"          => "Overspeed",
										"gps_time"           => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($alert[$i]['gps_time']))),
										"geofence"           => $geofencefix,
										"position" 					 => $position
									));
								}
							}else {
								array_push($datafix, array(
									"jalur_name"				 => $jalur_name,
									"datasimultan" 			 => $simultan,
									"vehicle_no"         => $datavehicle[0]['vehicle_no'],
									"vehicle_name"       => $datavehicle[0]['vehicle_name'],
									"gps_id"             => $alert[$i]['gps_id'],
									"gps_name"           => $alert[$i]['gps_name'],
									"gps_host"           => $alert[$i]['gps_host'],
									"gps_status"         => $alert[$i]['gps_status'],
									"gps_latitude_real"  => $alert[$i]['gps_latitude_real'],
									"gps_longitude_real" => $alert[$i]['gps_longitude_real'],
									"gps_speed"          => round($alert[$i]['gps_speed']*1.853),
									"gps_speed_limit"    => $speedlimitfix,
									"gps_alert"          => "Overspeed",
									"gps_time"           => date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($alert[$i]['gps_time']))),
									"geofence"           => $geofencefix,
									"position" 					 => $position
								));
							}
						}
					}
					// echo "<pre>";
					// var_dump($datafix);die();
					// echo "<pre>";
					$callback['code'] = "200";
					$callback['msg']  = "success";
					$callback['data'] = $datafix;
					echo json_encode($callback);
			}else {
				$callback['code'] = "400";
				echo json_encode($callback);
			}
	}

	function livestream(){
		if(! isset($this->sess->user_type)){
			redirect('dashboard');
		}

    $this->params['code_view_menu'] = "report";

    // echo "<pre>";
		// var_dump($this->params['alarmtype']);die();
		// echo "<pre>";

    $this->params["header"]   = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]  = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]  = $this->load->view('dashboard/livestream/v_livestream', $this->params, true);
		$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
	}

	function apilogin(){
		$url     = $_POST['url'];
		$content = file_get_contents($url);
		// echo "<pre>";
		// var_dump($content);die();
		// echo "<pre>";
		echo json_encode($content);
	}

	function apigetvehicledata(){
		$url     = $_POST['url'];
		$content = file_get_contents($url);
		// echo "<pre>";
		// var_dump($content);die();
		// echo "<pre>";
		echo json_encode($content);
	}

	function vehiclelive(){
		$url                     = $_POST['url'];
		$this->params['content'] = file_get_contents($url);
		$html                    = $this->load->view('dashboard/livestream/v_vehiclelive', $this->params, true);
		$callback["html"]        = $html;
		// echo "<pre>";
		// var_dump($html);die();
		// echo "<pre>";
		echo json_encode($callback);
	}

	function getvehicle_mv03($table, $name){
		$this->db = $this->load->database("default", true);
  		//GET DATA FROM DB
  		$this->db->select("vehicle_id,vehicle_device,vehicle_mv03");
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_mv03", $name);
  		$this->db->where("vehicle_status <>", 3);
  		$q = $this->db->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }

	function getdriverhist_change($table, $vehicleid, $date){
		$this->dbtransporter = $this->load->database("webtracking_ts", true);
		$sdate_wita = date ("Y-m-d H:i:s", strtotime($date." "."+1 hours"));

		//GET DATA FROM DB
		$this->dbtransporter->select("change_driver_id,change_driver_time");
		$this->dbtransporter->where("change_imei", $vehicleid);
		$this->dbtransporter->where("change_driver_time <= ", $sdate_wita);
		$this->dbtransporter->order_by("change_driver_time", "desc");
		$this->dbtransporter->limit(1);
		$q    = $this->dbtransporter->get($table);
		$rows = $q->result_array();

		return $rows;
  }

	function getdriver_byidcard($table, $driverid){
      $this->dbtransporter = $this->load->database("transporter", true);
  		//GET DATA FROM DB
  		$this->dbtransporter->select("*");
			$this->dbtransporter->order_by("driver_id", "desc");
			$this->dbtransporter->where("driver_idcard", $driverid);
			$this->dbtransporter->where("driver_status", 1);
  		$q = $this->dbtransporter->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }

    function getdriverimage_byidcard($table, $driverid){
		$this->dbtransporter = $this->load->database("transporter", true);
  		//GET DATA FROM DB
  		$this->dbtransporter->select("*");
		$this->dbtransporter->order_by("driver_image_id", "desc");
		$this->dbtransporter->where("driver_image_driver_id", $driverid);
  		$q = $this->dbtransporter->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }


}
