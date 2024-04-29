<?php
include "base.php";
require_once APPPATH."/third_party/Classes/PHPExcel.php";

class Dashboardberau extends Base
{
    function __construct()
    {
        parent::Base();
        $this->load->model("dashboardmodel");
        $this->load->model("gpsmodel");
        $this->load->model("m_dashboardberau");
        $this->load->model("m_development");
        $this->load->model("m_securityevidence");
        $this->load->model("m_dashboard_intervensi_postevent");
    }

    // DASHBOARD POST EVENT START
    function dashboardpostevent(){
      if(! isset($this->sess->user_type)){
        redirect('dashboard');
      }

      $this->params['data']           = $this->m_dashboardberau->getdevice();
      $this->params['alarmtype']      = $this->m_dashboardberau->getalarmmaster();

      // echo "<pre>";
      // var_dump($this->params['data']);die();
      // echo "<pre>";

      $rows_company                   = $this->dashboardmodel->get_company_bylevel();
      $this->params["rcompany"]       = $rows_company;
      $this->params['code_view_menu'] = "monitor";

      $user_id       = $this->sess->user_id;
  		$user_parent   = $this->sess->user_parent;
  		$privilegecode = $this->sess->user_id_role;
  		$user_company  = $this->sess->user_company;

  		if($privilegecode == 0){
  			$user_id_fix = $user_id;
  		}elseif ($privilegecode == 1) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 2) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 3) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 4) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 5) {
  			$user_id_fix = $user_id;
  		}elseif ($privilegecode == 6) {
  			$user_id_fix = $user_id;
  		}else{
  			$user_id_fix = $user_id;
  		}

  		$companyid                       = $this->sess->user_company;
  		$user_dblive                     = $this->sess->user_dblive;
  		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

  		$datafix                         = array();
  		$deviceidygtidakada              = array();
  		$statusvehicle['engine_on']  = 0;
  		$statusvehicle['engine_off'] = 0;

  		for ($i=0; $i < sizeof($mastervehicle); $i++) {
  			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
  			if (isset($jsonautocheck->auto_status)) {
  				// code...
  			$auto_status   = $jsonautocheck->auto_status;

  			if ($privilegecode == 5 || $privilegecode == 6) {
  				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
  					if ($jsonautocheck->auto_last_engine == "ON") {
  						$statusvehicle['engine_on'] += 1;
  					}else {
  						$statusvehicle['engine_off'] += 1;
  					}
  				}
  			}else {
  				if ($jsonautocheck->auto_last_engine == "ON") {
  					$statusvehicle['engine_on'] += 1;
  				}else {
  					$statusvehicle['engine_off'] += 1;
  				}
  			}

  				if ($auto_status != "M") {
  					array_push($datafix, array(
  						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
  						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
  						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
  						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
  						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
  						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
  						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
  						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
  						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
  					));
  				}
  			}
  		}

  		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
  			if ($company) {

  					$datavehicleandcompany    = array();
  					$datavehicleandcompanyfix = array();

  						for ($d=0; $d < sizeof($company); $d++) {
  							$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
  							// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
  							$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
  							$totalengine       = explode("|", $totaldata);
  								array_push($datavehicleandcompany, array(
  									"company_id"   => $company[$d]->company_id,
  									"company_name" => $company[$d]->company_name,
  									"totalmobil"   => $totalengine[2],
  									"vehicle"      => $vehicledata[$d]
  								));
  						}
  				$this->params['company']   = $company;
  				$this->params['companyid'] = $companyid;
  				$this->params['vehicle']   = $datavehicleandcompany;
  			}else {
  				$this->params['company']   = 0;
  				$this->params['companyid'] = 0;
  				$this->params['vehicle']   = 0;
  			}

  		// echo "<pre>";
  		// var_dump($company);die();
  		// echo "<pre>";


  		$this->params['url_code_view']  = "1";
  		$this->params['code_view_menu'] = "monitor";
  		$this->params['maps_code']      = "morehundred";

  		$this->params['engine_on']      = $statusvehicle['engine_on'];
  		$this->params['engine_off']     = $statusvehicle['engine_off'];


  		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

  		$datastatus                     = explode("|", $rstatus);
  		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
  		$this->params['total_vehicle']  = $datastatus[3];
  		$this->params['total_offline']  = $datastatus[2];

      $user_license_id = $this->sess->user_license_id;
      if ($user_license_id == "" || $user_license_id == NULL) {
        $sid_where = "all";
      }else {
        // $sid_where = $this->sess->user_name;
        $sid_where = "all";
      }
      $company_id_beats = $this->sess->user_company_id_beats;

      $data_karyawan_bc                    = $this->m_dashboardberau->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
      $this->params['data_karyawan_bc']    = $data_karyawan_bc;

      $data_karyawan_bc            = $this->m_dashboardberau->data_karyawan_bymitra_jabatan($company[0]->company_sid_code, "Operator");
      $this->params['data_driver'] = $data_karyawan_bc;

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      }elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      }elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      }elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      }elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      }elseif ($privilegecode == 6) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
      }else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function getalarmsubcat(){
      $subcategoryid                = $this->input->post("id");
      $callback['alarmsubcategory'] = $this->m_dashboardberau->getalarmsubcategory($subcategoryid);

      echo json_encode($callback);
    }

    function getalarmchild(){
      $alarmchildid           = $this->input->post("id");
      $callback['alarmchild'] = $this->m_dashboardberau->getalarmchild($alarmchildid);

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

  		$company           = $this->input->post("company");
  		$vehicle           = $this->input->post("vehicle");
      $driver            = $this->input->post("data_driver");
  		$startdate         = $this->input->post("startdate");
  		$enddate           = $this->input->post("enddate");
  		$shour             = $this->input->post("shour");
  		$ehour             = $this->input->post("ehour");
  		$alarmtype         = $this->input->post("alarmtype");
  		$periode           = $this->input->post("periode");
  		$km                = $this->input->post("km");
      $filter_true_false = $this->input->post("filter_true_false");

  		// $reporttype = $this->input->post("reporttype");
  		$reporttype = 0;
  		$alarmtypefromaster = array();

  		if ($alarmtype != "All") {
  			$alarmbymaster = $this->m_dashboardberau->getalarmbytype($alarmtype);
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

  		$report           = "alarm_evidence_";
  		$report_sum       = "summary_";
      $report_overspeed = "overspeed_hour_";


  		// print_r($periode);exit();

  		if($periode == "custom"){
  			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($startdate." ".$shour)));
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}elseif ($periode == "today") {
  			// $sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
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

  		// print_r($sdate." ".$edate);exit();

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
        $dbtable_overspeed = $report_overspeed."januari_".$year;
  			break;
  			case "February":
              $dbtable = $report."februari_".$year;
  			$dbtable_sum = $report_sum."februari_".$year;
        $dbtable_overspeed = $report_overspeed."februari_".$year;
  			break;
  			case "March":
              $dbtable = $report."maret_".$year;
  			$dbtable_sum = $report_sum."maret_".$year;
        $dbtable_overspeed = $report_overspeed."maret_".$year;
  			break;
  			case "April":
              $dbtable = $report."april_".$year;
  			$dbtable_sum = $report_sum."april_".$year;
        $dbtable_overspeed = $report_overspeed."april_".$year;
  			break;
  			case "May":
              $dbtable = $report."mei_".$year;
  			$dbtable_sum = $report_sum."mei_".$year;
        $dbtable_overspeed = $report_overspeed."mei_".$year;
  			break;
  			case "June":
              $dbtable = $report."juni_".$year;
  			$dbtable_sum = $report_sum."juni_".$year;
        $dbtable_overspeed = $report_overspeed."juni_".$year;
  			break;
  			case "July":
              $dbtable = $report."juli_".$year;
  			$dbtable_sum = $report_sum."juli_".$year;
        $dbtable_overspeed = $report_overspeed."juli_".$year;
  			break;
  			case "August":
              $dbtable = $report."agustus_".$year;
  			$dbtable_sum = $report_sum."agustus_".$year;
        $dbtable_overspeed = $report_overspeed."agustus_".$year;
  			break;
  			case "September":
              $dbtable = $report."september_".$year;
  			$dbtable_sum = $report_sum."september_".$year;
        $dbtable_overspeed = $report_overspeed."september_".$year;
  			break;
  			case "October":
              $dbtable = $report."oktober_".$year;
  			$dbtable_sum = $report_sum."oktober_".$year;
        $dbtable_overspeed = $report_overspeed."oktober_".$year;
  			break;
  			case "November":
              $dbtable = $report."november_".$year;
  			$dbtable_sum = $report_sum."november_".$year;
        $dbtable_overspeed = $report_overspeed."november_".$year;
  			break;
  			case "December":
              $dbtable = $report."desember_".$year;
  			$dbtable_sum = $report_sum."desember_".$year;
        $dbtable_overspeed = $report_overspeed."desember_".$year;
  			break;
  		}


      // GET DATA ALERT OVERSPEED
      $data_array_alert = array();
  		$data_overspeed   = $this->m_dashboardberau->get_overspeed_intensor_intervention($dbtable_overspeed, $vehicle, $company, $sdate, $edate, $filter_true_false);

      // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

        // echo "<pre>";
        // var_dump($data_overspeed);die();
        // // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
        // echo "<pre>";

        if ($alarmtype == 999999 || $alarmtype == "All" || $alarmtype == "0") {
          for ($i=0; $i < sizeof($data_overspeed); $i++) {

            if (isset($data_overspeed[$i]['overspeed_report_id_cr'])) {
              $overspeed_report_id_cr =  $data_overspeed[$i]['overspeed_report_id_cr'];
            }else {
              $overspeed_report_id_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_name_cr'])) {
              $overspeed_report_name_cr =  $data_overspeed[$i]['overspeed_report_name_cr'];
            }else {
              $overspeed_report_name_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_sid_cr'])) {
              $overspeed_report_sid_cr =  $data_overspeed[$i]['overspeed_report_sid_cr'];
            }else {
              $overspeed_report_sid_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_statusintervention_cr'])) {
              $overspeed_report_statusintervention_cr =  $data_overspeed[$i]['overspeed_report_statusintervention_cr'];
            }else {
              $overspeed_report_statusintervention_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_intervention_category_cr'])) {
              $overspeed_report_intervention_category_cr =  $data_overspeed[$i]['overspeed_report_intervention_category_cr'];
            }else {
              $overspeed_report_intervention_category_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_fatiguecategory_cr'])) {
              $overspeed_report_fatiguecategory_cr =  $data_overspeed[$i]['overspeed_report_fatiguecategory_cr'];
            }else {
              $overspeed_report_fatiguecategory_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_note_cr'])) {
              $overspeed_report_note_cr =  $data_overspeed[$i]['overspeed_report_note_cr'];
            }else {
              $overspeed_report_note_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_datetime_cr'])) {
              $overspeed_report_datetime_cr =  $data_overspeed[$i]['overspeed_report_datetime_cr'];
            }else {
              $overspeed_report_datetime_cr = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_truefalse_up'])) {
              $overspeed_report_truefalse_up =  $data_overspeed[$i]['overspeed_report_truefalse_up'];
            }else {
              $overspeed_report_truefalse_up = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
              $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
            }else {
              $overspeed_report_note_up = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_idsync_up2'])) {
              $overspeed_report_idsync_up2 =  $data_overspeed[$i]['overspeed_report_idsync_up2'];
            }else {
              $overspeed_report_idsync_up2 = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_sid_up2'])) {
              $overspeed_report_sid_up2 =  $data_overspeed[$i]['overspeed_report_sid_up2'];
            }else {
              $overspeed_report_sid_up2 = "";
            }

            if (isset($data_overspeed[$i]['overspeed_report_name_up2'])) {
              $overspeed_report_name_up2 =  $data_overspeed[$i]['overspeed_report_name_up2'];
            }else {
              $overspeed_report_name_up2 = "";
            }

      			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
            if ($data_overspeed[$i]['overspeed_report_event_location'] != "") {
              array_push($data_array_alert, array(
        				"isfatigue"                                => "no",
                "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
                "alarm_report_vehicle_id"                  => $data_overspeed[$i]['overspeed_report_vehicle_id'],
                "alarm_report_vehicle_no"                  => $data_overspeed[$i]['overspeed_report_vehicle_no'],
                "alarm_report_vehicle_name"                => $data_overspeed[$i]['overspeed_report_vehicle_name'],
                "alarm_report_type"                        => "Overspeed",
                "alarm_report_name"                        => "Overspeed " . $data_overspeed[$i]['overspeed_report_level_alias'],
                "alarm_report_start_time"                  => $data_overspeed[$i]['overspeed_report_gps_time'],
                "alarm_report_end_time"                    => $data_overspeed[$i]['overspeed_report_gps_time'],
                "alarm_report_coordinate_start"            => $data_overspeed[$i]['overspeed_report_coordinate'],
                "alarm_report_coordinate_end"              => $data_overspeed[$i]['overspeed_report_coordinate'],
                "alarm_report_location_start"              => $data_overspeed[$i]['overspeed_report_event_location'],
                "alarm_report_speed" 			                 => $data_overspeed[$i]['overspeed_report_speed'],
                "alarm_report_geofence" 	                 => $data_overspeed[$i]['overspeed_report_geofence_name'],
                "alarm_report_speed_limit"                 => ($data_overspeed[$i]['overspeed_report_geofence_limit']-3),
                "alarm_report_speed_time" 		             => "",
                "alarm_report_speed_status" 	             => "",
                "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
                "alarm_report_id_cr"                       => $overspeed_report_id_cr,
                "alarm_report_name_cr"                     => $overspeed_report_name_cr,
                "alarm_report_sid_cr"                      => $overspeed_report_sid_cr,
                "alarm_report_spvsid_cr" 	                 => $data_overspeed[$i]['overspeed_report_spvsid_cr'],
                "alarm_report_spvname_cr" 	               => $data_overspeed[$i]['overspeed_report_spvname_cr'],
                "alarm_report_sid_up" 	                   => $data_overspeed[$i]['overspeed_report_sid_up'],
                "alarm_report_postevent_name_up" 	         => $data_overspeed[$i]['overspeed_report_postevent_name_up'],
                "alarm_report_statusintervention_cr" 	     => $overspeed_report_statusintervention_cr,
                "alarm_report_intervention_category_cr" 	 => $overspeed_report_intervention_category_cr,
                "alarm_report_fatiguecategory_cr" 	       => "0",
                "alarm_report_note_cr" 	                   => $overspeed_report_note_cr,
                "alarm_report_datetime_cr" 	               => $overspeed_report_datetime_cr,
                "alarm_report_truefalse_up" 	             => $overspeed_report_truefalse_up,
                "alarm_report_note_up" 	                   => $overspeed_report_note_up,
                "alarm_report_sid_up" 	                   => $data_overspeed[$i]['overspeed_report_sid_up'],
                "alarm_report_postevent_name_up" 	         => $data_overspeed[$i]['overspeed_report_postevent_name_up'],
                "alarm_report_status_sendhazard"           => $data_overspeed[$i]['overspeed_report_status_sendhazard'],
                "alarm_report_datetime_sendhazard"         => $data_overspeed[$i]['overspeed_report_datetime_sendhazard'],
                "alarm_report_status_sendrecord"           => $data_overspeed[$i]['overspeed_report_status_sendrecord'],
                "alarm_report_datetime_sendrecord"         => $data_overspeed[$i]['overspeed_report_datetime_sendrecord'],
                "alarm_report_idsync_up2"                  => $overspeed_report_idsync_up2,
                "alarm_report_sid_up2"                     => $overspeed_report_sid_up2,
                "alarm_report_name_up2"                    => $overspeed_report_name_up2,
        			));
            }
      		}
        }

      // echo "<pre>";
      // var_dump($alarmtypefromaster);die();
      // echo "<pre>";


      if ($alarmtype != 999999) {
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
              // echo "<pre>";
              // var_dump($user_company);die();
              // echo "<pre>";
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

          if ($filter_true_false != "All") {
            $this->dbtrip->where('alarm_report_truefalse_up', $filter_true_false); //$alarmtype $alarmbymaster[0]['alarm_type']
          }

          if ($driver != "all") {
            $this->dbtrip->where('alarm_report_sid_cr', $driver); //$alarmtype $alarmbymaster[0]['alarm_type']
          }

        $this->dbtrip->where("alarm_report_media", 0); //photo
        $this->dbtrip->where("alarm_report_start_time >=", $sdate);

        // $nowday            = date("d");
        // $end_day_fromEdate = date("d", strtotime($edate));
        //
        // if ($nowday == $end_day_fromEdate) {
        //   $edate = date("Y-m-d H:i:s");
        // }

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
        $this->dbtrip->where("alarm_report_gpsstatus !=","pushalert");
        // $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
        $this->dbtrip->order_by("alarm_report_start_time","asc");
        $this->dbtrip->group_by("alarm_report_start_time");
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

            if (isset($thisreport[$j]['alarm_report_id_cr'])) {
              $alarm_report_id_cr =  $thisreport[$j]['alarm_report_id_cr'];
            }else {
              $alarm_report_id_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_name_cr'])) {
              $alarm_report_name_cr =  $thisreport[$j]['alarm_report_name_cr'];
            }else {
              $alarm_report_name_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_sid_cr'])) {
              $alarm_report_sid_cr =  $thisreport[$j]['alarm_report_sid_cr'];
            }else {
              $alarm_report_sid_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_statusintervention_cr'])) {
              $alarm_report_statusintervention_cr =  $thisreport[$j]['alarm_report_statusintervention_cr'];
            }else {
              $alarm_report_statusintervention_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_intervention_category_cr'])) {
              $alarm_report_intervention_category_cr =  $thisreport[$j]['alarm_report_intervention_category_cr'];
            }else {
              $alarm_report_intervention_category_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_fatiguecategory_cr'])) {
              $alarm_report_fatiguecategory_cr =  $thisreport[$j]['alarm_report_fatiguecategory_cr'];
            }else {
              $alarm_report_fatiguecategory_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_note_cr'])) {
              $alarm_report_note_cr =  $thisreport[$j]['alarm_report_note_cr'];
            }else {
              $alarm_report_note_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_datetime_cr'])) {
              $alarm_report_datetime_cr =  $thisreport[$j]['alarm_report_datetime_cr'];
            }else {
              $alarm_report_datetime_cr = "";
            }

            if (isset($thisreport[$j]['alarm_report_truefalse_up'])) {
              $alarm_report_truefalse_up =  $thisreport[$j]['alarm_report_truefalse_up'];
            }else {
              $alarm_report_truefalse_up = "";
            }

            if (isset($thisreport[$j]['alarm_report_note_up'])) {
              $alarm_report_note_up =  $thisreport[$j]['alarm_report_note_up'];
            }else {
              $alarm_report_note_up = "";
            }

            array_push($data_array_alert, array(
              "alarm_report_id"                       => $thisreport[$j]['alarm_report_id'],
              "alarm_report_vehicle_id"               => $thisreport[$j]['alarm_report_vehicle_id'],
              "alarm_report_vehicle_no"               => $thisreport[$j]['alarm_report_vehicle_no'],
              "alarm_report_vehicle_name"             => $thisreport[$j]['alarm_report_vehicle_name'],
              "alarm_report_name"                     => $alarmreportnamefix,
              "alarm_report_type"                     => $thisreport[$j]['alarm_report_type'],
              "alarm_report_start_time"               => $thisreport[$j]['alarm_report_start_time'],
              "alarm_report_end_time"                 => $thisreport[$j]['alarm_report_end_time'],
              "alarm_report_coordinate_start"         => $thisreport[$j]['alarm_report_coordinate_start'],
              "alarm_report_coordinate_end"           => $thisreport[$j]['alarm_report_coordinate_end'],
              "alarm_report_location_start"           => $thisreport[$j]['alarm_report_location_start'],
              "alarm_report_speed" 			              => $thisreport[$j]['alarm_report_speed'],
              "alarm_report_geofence" 	              => "",
              "alarm_report_speed_limit"              => "",
              "alarm_report_speed_time" 		          => $thisreport[$j]['alarm_report_speed_time'],
              "alarm_report_speed_status" 	          => $thisreport[$j]['alarm_report_speed_status'],
              "alarm_report_jalur" 	                  => $thisreport[$j]['alarm_report_jalur'],
              "alarm_report_id_cr"                    => $alarm_report_id_cr,
              "alarm_report_name_cr"                  => $alarm_report_name_cr,
              "alarm_report_sid_cr"                   => $alarm_report_sid_cr,
              "alarm_report_spvsid_cr" 	              => $thisreport[$j]['alarm_report_spvsid_cr'],
              "alarm_report_spvname_cr" 	            => $thisreport[$j]['alarm_report_spvname_cr'],
              "alarm_report_sid_up" 	                => $thisreport[$j]['alarm_report_sid_up'],
              "alarm_report_postevent_name_up" 	      => $thisreport[$j]['alarm_report_postevent_name_up'],
              "alarm_report_statusintervention_cr"    => $alarm_report_statusintervention_cr,
              "alarm_report_intervention_category_cr" => $alarm_report_intervention_category_cr,
              "alarm_report_fatiguecategory_cr"       => $alarm_report_fatiguecategory_cr,
              "alarm_report_note_cr"                  => $alarm_report_note_cr,
              "alarm_report_datetime_cr"              => $alarm_report_datetime_cr,
              "alarm_report_truefalse_up"             => $alarm_report_truefalse_up,
              "alarm_report_note_up"                  => $alarm_report_note_up,
              "alarm_report_status_sendhazard"        => $thisreport[$j]['alarm_report_status_sendhazard'],
              "alarm_report_datetime_sendhazard"      => $thisreport[$j]['alarm_report_datetime_sendhazard'],
              "alarm_report_status_sendrecord"        => $thisreport[$j]['alarm_report_status_sendrecord'],
              "alarm_report_datetime_sendrecord"      => $thisreport[$j]['alarm_report_datetime_sendrecord'],
              "alarm_report_idsync_up2"               => $thisreport[$j]['alarm_report_idsync_up2'],
              "alarm_report_sid_up2"                  => $thisreport[$j]['alarm_report_sid_up2'],
              "alarm_report_name_up2"                 => $thisreport[$j]['alarm_report_name_up2'],
            ));
        }
      }

  		// echo "<pre>";
  		// var_dump($vehicle.'-'.$company.'-'.$privilegecode);die();
  		// echo "<pre>";

      usort($data_array_alert, function($a, $b) {
         return strtotime($b['alarm_report_start_time']) - strtotime($a['alarm_report_start_time']);
     });

     $data_array_fix = array();
     if($alarmtype == 999999) {
       for ($i=0; $i < sizeof($data_array_alert); $i++) {
       $violation_type = $data_array_alert[$i]['alarm_report_type'];
         if ($violation_type == "Overspeed") {
           array_push($data_array_fix, array(
             "isfatigue"                               => "no",
             "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
             "alarm_report_vehicle_id"                 => $data_array_alert[$i]['alarm_report_vehicle_id'],
             "alarm_report_vehicle_no"                 => $data_array_alert[$i]['alarm_report_vehicle_no'],
             "alarm_report_vehicle_name"               => $data_array_alert[$i]['alarm_report_vehicle_name'],
             "alarm_report_type"                       => $data_array_alert[$i]['alarm_report_type'],
             "alarm_report_name"                       => $data_array_alert[$i]['alarm_report_name'],
             "alarm_report_start_time"                 => $data_array_alert[$i]['alarm_report_start_time'],
             "alarm_report_end_time"                   => $data_array_alert[$i]['alarm_report_end_time'],
             "alarm_report_coordinate_start"           => $data_array_alert[$i]['alarm_report_coordinate_start'],
             "alarm_report_coordinate_end"             => $data_array_alert[$i]['alarm_report_coordinate_end'],
             "alarm_report_location_start"             => $data_array_alert[$i]['alarm_report_location_start'],
             "alarm_report_speed" 			               => $data_array_alert[$i]['alarm_report_speed'],
             "alarm_report_geofence" 			             => $data_array_alert[$i]['alarm_report_geofence'],
             "alarm_report_speed_limit" 			         => $data_array_alert[$i]['alarm_report_speed_limit'],
             "alarm_report_jalur" 			               => $data_array_alert[$i]['alarm_report_jalur'],
             "alarm_report_speed_time" 		             => $data_array_alert[$i]['alarm_report_speed_time'],
             "alarm_report_speed_status" 	             => $data_array_alert[$i]['alarm_report_speed_status'],
             "alarm_report_jalur" 	                   => $data_array_alert[$i]['alarm_report_jalur'],
             "alarm_report_id_cr" 	                   => $data_array_alert[$i]['alarm_report_id_cr'],
             "alarm_report_name_cr" 	                 => $data_array_alert[$i]['alarm_report_name_cr'],
             "alarm_report_sid_cr" 	                   => $data_array_alert[$i]['alarm_report_sid_cr'],
             "alarm_report_spvsid_cr" 	               => $data_array_alert[$i]['alarm_report_spvsid_cr'],
             "alarm_report_spvname_cr" 	               => $data_array_alert[$i]['alarm_report_spvname_cr'],
             "alarm_report_sid_up" 	                   => $data_array_alert[$i]['alarm_report_sid_up'],
             "alarm_report_postevent_name_up" 	       => $data_array_alert[$i]['alarm_report_postevent_name_up'],
             "alarm_report_statusintervention_cr" 	   => $data_array_alert[$i]['alarm_report_statusintervention_cr'],
             "alarm_report_intervention_category_cr" 	 => $data_array_alert[$i]['alarm_report_intervention_category_cr'],
             "alarm_report_fatiguecategory_cr" 	       => $data_array_alert[$i]['alarm_report_fatiguecategory_cr'],
             "alarm_report_note_cr" 	                 => $data_array_alert[$i]['alarm_report_note_cr'],
             "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
             "alarm_report_truefalse_up" 	             => $data_array_alert[$i]['alarm_report_truefalse_up'],
             "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
             "alarm_report_status_sendhazard"          => $data_array_alert[$i]['alarm_report_status_sendhazard'],
             "alarm_report_datetime_sendhazard"        => $data_array_alert[$i]['alarm_report_datetime_sendhazard'],
             "alarm_report_status_sendrecord"          => $data_array_alert[$i]['alarm_report_status_sendrecord'],
             "alarm_report_datetime_sendrecord"        => $data_array_alert[$i]['alarm_report_datetime_sendrecord'],
             "alarm_report_idsync_up2"                 => $data_array_alert[$i]['alarm_report_idsync_up2'],
             "alarm_report_sid_up2"                    => $data_array_alert[$i]['alarm_report_sid_up2'],
             "alarm_report_name_up2"                   => $data_array_alert[$i]['alarm_report_name_up2'],
           ));
         }
       }
     }elseif($alarmtype != "All") {
       for ($i=0; $i < sizeof($data_array_alert); $i++) {
         $violation_type = $data_array_alert[$i]['alarm_report_name'];
           if ($violation_type != "Overspeed") {
             array_push($data_array_fix, array(
               "isfatigue"                               => "yes",
               "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
               "alarm_report_vehicle_id"                 => $data_array_alert[$i]['alarm_report_vehicle_id'],
               "alarm_report_vehicle_no"                 => $data_array_alert[$i]['alarm_report_vehicle_no'],
               "alarm_report_vehicle_name"               => $data_array_alert[$i]['alarm_report_vehicle_name'],
               "alarm_report_type"                       => $data_array_alert[$i]['alarm_report_type'],
               "alarm_report_name"                       => $data_array_alert[$i]['alarm_report_name'],
               "alarm_report_start_time"                 => $data_array_alert[$i]['alarm_report_start_time'],
               "alarm_report_end_time"                   => $data_array_alert[$i]['alarm_report_end_time'],
               "alarm_report_coordinate_start"           => $data_array_alert[$i]['alarm_report_coordinate_start'],
               "alarm_report_coordinate_end"             => $data_array_alert[$i]['alarm_report_coordinate_end'],
               "alarm_report_location_start"             => $data_array_alert[$i]['alarm_report_location_start'],
               "alarm_report_speed" 			               => $data_array_alert[$i]['alarm_report_speed'],
               "alarm_report_geofence" 			             => $data_array_alert[$i]['alarm_report_geofence'],
               "alarm_report_speed_limit" 			         => $data_array_alert[$i]['alarm_report_speed_limit'],
               "alarm_report_jalur" 			               => $data_array_alert[$i]['alarm_report_jalur'],
               "alarm_report_speed_time" 		             => $data_array_alert[$i]['alarm_report_speed_time'],
               "alarm_report_speed_status" 	             => $data_array_alert[$i]['alarm_report_speed_status'],
               "alarm_report_jalur" 	                   => $data_array_alert[$i]['alarm_report_jalur'],
               "alarm_report_id_cr" 	                   => $data_array_alert[$i]['alarm_report_id_cr'],
               "alarm_report_name_cr" 	                 => $data_array_alert[$i]['alarm_report_name_cr'],
               "alarm_report_sid_cr" 	                   => $data_array_alert[$i]['alarm_report_sid_cr'],
               "alarm_report_spvsid_cr" 	               => $data_array_alert[$i]['alarm_report_spvsid_cr'],
               "alarm_report_spvname_cr" 	               => $data_array_alert[$i]['alarm_report_spvname_cr'],
               "alarm_report_sid_up" 	                   => $data_array_alert[$i]['alarm_report_sid_up'],
               "alarm_report_postevent_name_up" 	       => $data_array_alert[$i]['alarm_report_postevent_name_up'],
               "alarm_report_statusintervention_cr" 	   => $data_array_alert[$i]['alarm_report_statusintervention_cr'],
               "alarm_report_intervention_category_cr" 	 => $data_array_alert[$i]['alarm_report_intervention_category_cr'],
               "alarm_report_fatiguecategory_cr" 	       => $data_array_alert[$i]['alarm_report_fatiguecategory_cr'],
               "alarm_report_note_cr" 	                 => $data_array_alert[$i]['alarm_report_note_cr'],
               "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
               "alarm_report_truefalse_up" 	             => $data_array_alert[$i]['alarm_report_truefalse_up'],
               "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
               "alarm_report_status_sendhazard"          => $data_array_alert[$i]['alarm_report_status_sendhazard'],
               "alarm_report_datetime_sendhazard"        => $data_array_alert[$i]['alarm_report_datetime_sendhazard'],
               "alarm_report_status_sendrecord"          => $data_array_alert[$i]['alarm_report_status_sendrecord'],
               "alarm_report_datetime_sendrecord"        => $data_array_alert[$i]['alarm_report_datetime_sendrecord'],
               "alarm_report_idsync_up2"                 => $data_array_alert[$i]['alarm_report_idsync_up2'],
                "alarm_report_sid_up2"                    => $data_array_alert[$i]['alarm_report_sid_up2'],
                "alarm_report_name_up2"                   => $data_array_alert[$i]['alarm_report_name_up2'],
             ));
           }
       }
     }else {
       $data_array_fix = $data_array_alert;
     }

      // echo "<pre>";
      // var_dump($data_array_fix);die();
      // echo "<pre>";

  		$this->params['content']   = $data_array_fix;
      $this->params['alarmtype'] = $alarmtype;
  		$html                      = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent_result', $this->params, true);
  		$callback["html"]          = $html;
  		$callback["report"]        = $data_array_fix;

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

  		$reportdetail               = $this->m_dashboardberau->getdetailreport($table, $alert_id, $sdate);
  		$reportdetailvideo          = $this->m_dashboardberau->getdetailreportvideo($table, $alert_id, $sdate);
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
  		$html                                 = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent_infodetail', $this->params, true);
  		$callback["html"]                     = $html;
  		$callback["report"]                   = $reportdetail;
  		echo json_encode($callback);
  	}

    function getinfodetail_new_controlroom(){
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

  		$reportdetail               = $this->m_dashboardberau->getdetailreport($table, $alert_id, $sdate);
  		$reportdetailvideo          = $this->m_dashboardberau->getdetailreportvideo($table, $alert_id, $sdate);
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
  		$html                                 = $this->load->view('newdashboard/dashboardberau/intervention/v_postevent_infodetail', $this->params, true);
  		$callback["html"]                     = $html;
  		$callback["report"]                   = $reportdetail;
  		echo json_encode($callback);
  	}

    function postevent_detail(){
  		$alert_id        = $this->input->post("alert_id");
  		$sdate           = $this->input->post("sdate");
      $alarm_report_id = $this->input->post("alarm_report_id");
      $alarmtype       = $this->input->post("alarmtype");
  		$report          = "alarm_evidence_";
  		$reportoverspeed = "overspeed_hour_";
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

      // echo "<pre>";
      // var_dump($alarmtype);die();
      // echo "<pre>";

      if ($alarmtype == "Overspeed") {
        $data_array_alert = array();
    		$data_overspeed   = $this->m_dashboardberau->get_overspeed_intensor_intervention_detail($dbtableoverspeed, $alarm_report_id, $sdate);

          // echo "<pre>";
          // var_dump($data_overspeed);die();
          // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$sdate);die();
          // echo "<pre>";

    		for ($i=0; $i < sizeof($data_overspeed); $i++) {

          if (isset($data_overspeed[$i]['overspeed_report_spvidsync_cr'])) {
            $overspeed_report_spvidsync_cr =  $data_overspeed[$i]['overspeed_report_spvidsync_cr'];
          }else {
            $overspeed_report_spvidsync_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_spvname_cr'])) {
            $overspeed_report_spvname_cr =  $data_overspeed[$i]['overspeed_report_spvname_cr'];
          }else {
            $overspeed_report_spvname_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_spvsid_cr'])) {
            $overspeed_report_spvsid_cr =  $data_overspeed[$i]['overspeed_report_spvsid_cr'];
          }else {
            $overspeed_report_spvsid_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_statusintervention_cr'])) {
            $overspeed_report_statusintervention_cr =  $data_overspeed[$i]['overspeed_report_statusintervention_cr'];
          }else {
            $overspeed_report_statusintervention_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_judgement_cr'])) {
            $overspeed_report_judgement_cr =  $data_overspeed[$i]['overspeed_report_judgement_cr'];
          }else {
            $overspeed_report_judgement_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_id_lokasi'])) {
            $overspeed_report_id_lokasi =  $data_overspeed[$i]['overspeed_report_id_lokasi'];
          }else {
            $overspeed_report_id_lokasi = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_intervention_category_cr'])) {
            $overspeed_report_intervention_category_cr =  $data_overspeed[$i]['overspeed_report_intervention_category_cr'];
          }else {
            $overspeed_report_intervention_category_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_fatiguecategory_cr'])) {
            $overspeed_report_fatiguecategory_cr =  $data_overspeed[$i]['overspeed_report_fatiguecategory_cr'];
          }else {
            $overspeed_report_fatiguecategory_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_note_cr'])) {
            $overspeed_report_note_cr =  $data_overspeed[$i]['overspeed_report_note_cr'];
          }else {
            $overspeed_report_note_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_datetime_cr'])) {
            $overspeed_report_datetime_cr =  $data_overspeed[$i]['overspeed_report_datetime_cr'];
          }else {
            $overspeed_report_datetime_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
            $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
          }else {
            $overspeed_report_note_up = "";
          }

    			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
    			array_push($data_array_alert, array(
    				"isfatigue"                                => "no",
            "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
            "alarm_report_vehicle_id"                  => $data_overspeed[$i]['overspeed_report_vehicle_id'],
            "alarm_report_vehicle_no"                  => $data_overspeed[$i]['overspeed_report_vehicle_no'],
            "alarm_report_vehicle_name"                => $data_overspeed[$i]['overspeed_report_vehicle_name'],
            "alarm_report_type"                        => "Overspeed",
            "alarm_report_name"                        => "Overspeed",
            "alarm_report_start_time"                  => $data_overspeed[$i]['overspeed_report_gps_time'],
            "alarm_report_end_time"                    => $data_overspeed[$i]['overspeed_report_gps_time'],
            "alarm_report_coordinate_start"            => $data_overspeed[$i]['overspeed_report_coordinate'],
            "alarm_report_coordinate_end"              => $data_overspeed[$i]['overspeed_report_coordinate'],
            "alarm_report_location_start"              => $data_overspeed[$i]['overspeed_report_event_location'],
            "alarm_report_speed" 			                 => $data_overspeed[$i]['overspeed_report_speed'],
            "alarm_report_speed_limit"                 => ($data_overspeed[$i]['overspeed_report_geofence_limit']-3),
            "overspeed_report_level_alias"             => $data_overspeed[$i]['overspeed_report_level_alias'],
            "alarm_report_speed_time" 		             => "",
            "alarm_report_speed_status" 	             => "",
            "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
            "alarm_report_id_cr" 	                     => $overspeed_report_spvidsync_cr,
            "alarm_report_name_cr" 	                   => $overspeed_report_spvname_cr,
            "alarm_report_sid_cr" 	                   => $overspeed_report_spvsid_cr,
            "alarm_report_sid_up" 	                   => $data_overspeed[$i]['overspeed_report_sid_up'],
            "alarm_report_postevent_name_up" 	         => $data_overspeed[$i]['overspeed_report_postevent_name_up'],
            "alarm_report_judgement_cr"                => $overspeed_report_judgement_cr,
            "alarm_report_id_lokasi"                   => $overspeed_report_id_lokasi,
            "alarm_report_statusintervention_cr" 	     => $overspeed_report_statusintervention_cr,
            "alarm_report_intervention_category_cr" 	 => $overspeed_report_intervention_category_cr,
            "alarm_report_fatiguecategory_cr" 	       => "",
            "alarm_report_note_cr" 	                   => $overspeed_report_note_cr,
            "alarm_report_datetime_cr" 	               => $overspeed_report_datetime_cr,
            "alarm_report_note_up" 	                   => $overspeed_report_note_up,
    			));
    		}
        // echo "<pre>";
        // var_dump($data_array_alert);die();
        // echo "<pre>";

        $data_site                       = $this->m_development->master_site_bc_all("ts_bc_master_site");
        $this->params['data_site']       = $data_site;

        $type_intervention                   = $this->m_development->get_type_intervention();
        // echo "<pre>";
        // var_dump($type_intervention);die();
        // echo "<pre>";
        $this->params['type_intervention']   = $type_intervention;

        $type_note                           = $this->m_development->get_type_note(1);
        $this->params['type_note']           = $type_note;

        $user_license_id = $this->sess->user_license_id;
        if ($user_license_id == "" || $user_license_id == NULL) {
          $sid_where = "all";
        }else {
          // $sid_where = $this->sess->user_name;
          $sid_where = "all";
        }
        $company_id_beats = $this->sess->user_company_id_beats;

        $data_karyawan_bc                    = $this->m_dashboardberau->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
        $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        // $data_karyawan_bc                    = $this->m_development->check_data_karyawan();
        // $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        // GET DATA PENGAWAS FROM TABLE CONFIG
        $shift_1_start = date("Y-m-d H:i:s", strtotime("06:00:00"));
        $shift_1_end   = date("Y-m-d H:i:s", strtotime("18:00:00"));
        $shift_2_start = date("Y-m-d H:i:s", strtotime("18:00:00"));
        $shift_2_end   = date("Y-m-d H:i:s", strtotime("06:00:00"));

        $current_time = date("Y-m-d H:i:s", strtotime("+1 Hours"));
        // $current_time = "2023-10-18 20:00:00";
        if ($current_time >= $shift_1_start && $current_time <= $shift_1_end) {
          $shift = 1;
          $sdate_config = $shift_1_start;
          $edate_config = $shift_1_end;
        }else {
          $shift = 2;
          $sdate_config = $shift_2_start;
          $edate_config = $shift_2_end;
        }

        $user_company = $this->sess->user_company;

        $sid_pengawas_from_config                 = $this->m_dashboard_intervensi_postevent->check_intervention_config_2($user_company, $sdate_config, $edate_config);
        $this->params['sid_pengawas_from_config'] = $sid_pengawas_from_config;

        $this->params['content']             = $data_array_alert;
        $this->params['alert_id']            = $alarm_report_id;
        $this->params['alarmtype']           = $alarmtype;
        $this->params['tablenya']            = $dbtableoverspeed;

        $this->params['monthforparam'] 			 = $monthforparam;
        $this->params['year'] 			         = $year;
        $this->params['user_id_role'] 			 = $this->sess->user_id_role;
        // echo "<pre>";
        // var_dump($this->params['content']);die();
        // echo "<pre>";
        $html                                = $this->load->view('newdashboard/dashboardberau/postevent/v_postevent_modal_overspeed', $this->params, true);
        $callback["report"]                  = $data_array_alert;
      }else {
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

    			$speedgps                          = number_format($reportdetaildecode[4]/10, 1, '.', '');
    			//$speedgps                        = $reportdetail[0]['alarm_report_speed']; //by speed gps TK510

    			$alarm_report_coordinate_start     = $reportdetail[0]['alarm_report_coordinate_start'];

          $type_intervention                 = $this->m_development->get_type_intervention();
          $this->params['type_intervention'] = $type_intervention;

          $type_note                         = $this->m_development->get_type_note(1);
          $this->params['type_note']         = $type_note;

          // $data_karyawan_bc                         = $this->m_development->check_data_karyawan();
          // $this->params['data_karyawan_bc']         = $data_karyawan_bc;

          $user_license_id = $this->sess->user_license_id;
          if ($user_license_id == "" || $user_license_id == NULL) {
            $sid_where = "all";
          }else {
            // $sid_where = $this->sess->user_name;
            $sid_where = "all";
          }
          $company_id_beats = $this->sess->user_company_id_beats;

          $data_karyawan_bc                    = $this->m_dashboardberau->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
          $this->params['data_karyawan_bc']    = $data_karyawan_bc;

          // GET DATA PENGAWAS FROM TABLE CONFIG
          $shift_1_start = date("Y-m-d H:i:s", strtotime("06:00:00"));
          $shift_1_end   = date("Y-m-d H:i:s", strtotime("18:00:00"));
          $shift_2_start = date("Y-m-d H:i:s", strtotime("18:00:00"));
          $shift_2_end   = date("Y-m-d H:i:s", strtotime("06:00:00"));

          $current_time = date("Y-m-d H:i:s", strtotime("+1 Hours"));
          // $current_time = "2023-10-18 20:00:00";
          if ($current_time >= $shift_1_start && $current_time <= $shift_1_end) {
            $shift = 1;
            $sdate_config = $shift_1_start;
            $edate_config = $shift_1_end;
          }else {
            $shift = 2;
            $sdate_config = $shift_2_start;
            $edate_config = $shift_2_end;
          }

          $user_company = $this->sess->user_company;

          $sid_pengawas_from_config                 = $this->m_dashboard_intervensi_postevent->check_intervention_config_2($user_company, $sdate_config, $edate_config);
          $this->params['sid_pengawas_from_config'] = $sid_pengawas_from_config;

    		// echo "<pre>";
    		// var_dump($sid_pengawas_from_config);die();
    		// echo "<pre>";

    		$this->params['content']              = $reportdetail;
        $this->params['alert_id']             = $alarm_report_id;
        $this->params['alarmtype']            = $alarmtype;
        $this->params['tablenya']             = $table;
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
    		$html                                 = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent_modal', $this->params, true);
        $callback["report"]                   = $reportdetail;
      }


  		$callback["html"]                     = $html;
  		echo json_encode($callback);
  	}

    function submit_postevent_overspeed(){
      $alarmtype          = $_POST['alarmtype'];
      $user_id            = $_POST['user_id'];
      $user_name          = $_POST['user_name'];
      $alert_id           = $_POST['alert_id'];
      $tablenya           = $_POST['tablenya'];
      $postevent_date     = $_POST['postevent_date'];
      $fatigue_category   = $_POST['fatigue_category'];
      $postevent_name     = $_POST['postevent_name'];
      $postevent_sid      = $_POST['postevent_sid'];
      $alarm_true_false   = $_POST['alarm_true_false'];
      $postevent_alarm    = $_POST['postevent_alarm'];
      $intervention_note  = $_POST['intervention_note'];
      $postevent_date     = $_POST['postevent_date'];

      $alarm_true_false_fix  = 0;
      $intervention_alarm_fix = 0;

      if ($alarm_true_false == 1) {
        $alarm_true_false_fix = 1;
      }else {
        $alarm_true_false_fix = 2;
      }

      if ($postevent_alarm == 1) {
        $intervention_alarm_fix = 1;
      }else {
        $intervention_alarm_fix = 2;
      }

      // $data_karyawan          = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $postevent_sid);

      $data = array(
        "overspeed_report_id_up"                    => $this->sess->user_id,
        // "overspeed_report_name_up"                  => $data_karyawan[0]['karyawan_bc_name'],
        "overspeed_report_name_up"                  => $this->sess->user_name,
        "overspeed_report_statusintervention_up"    => $alarm_true_false_fix,
        "overspeed_report_truefalse_up"             => $intervention_alarm_fix,
        "overspeed_report_note_up"                  => $intervention_note,
        "overspeed_report_datetime_up"              => $postevent_date,
        // "overspeed_report_sid_up"                   => $postevent_sid.'|'.$data_karyawan[0]['karyawan_bc_name'],
        "overspeed_report_sid_up"                   => $this->sess->user_id.'|'.$this->sess->user_name,
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_securityevidence->update_post_event($tablenya, "overspeed_report_id", $alert_id, $data);
        if ($update) {
          $callback["error"]   = false;
          $callback["message"] = "Success Submit Post Event";

          echo json_encode($callback);
        }else {
          $callback["error"]   = true;
          $callback["message"] = "Failed Submit Post Event";

          echo json_encode($callback);
        }
    }

    function post_event_detail(){
  		$alert_id        = $this->input->post("alert_id");
  		$sdate           = $this->input->post("sdate");
      $alarm_report_id = $this->input->post("alarm_report_id");
      $alarmtype       = $this->input->post("alarmtype");
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

  		$reportdetail               = $this->m_dashboardberau->getdetailreport($table, $alert_id, $sdate);
  		$reportdetailvideo          = $this->m_dashboardberau->getdetailreportvideo($table, $alert_id, $sdate);
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

        $type_intervention                 = $this->m_dashboardberau->get_type_intervention();
        $this->params['type_intervention'] = $type_intervention;

        $type_note                         = $this->m_dashboardberau->get_type_note(1);
        $this->params['type_note']         = $type_note;

        $data_karyawan_bc                         = $this->m_dashboardberau->check_data_karyawan();
        $this->params['data_karyawan_bc']         = $data_karyawan_bc;


  		// echo "<pre>";
  		// var_dump($alarm_report_coordinate_start);die();
  		// echo "<pre>";

  		$this->params['content']              = $reportdetail;
      $this->params['alert_id']             = $alarm_report_id;
      $this->params['alarmtype']            = $alarmtype;
      $this->params['tablenya']             = $table;
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
  		$html                                 = $this->load->view('newdashboard/dashboardberau/postevent/v_dashboard_postevent_modal', $this->params, true);
  		$callback["html"]                     = $html;
  		$callback["report"]                   = $reportdetail;
  		echo json_encode($callback);
  	}

    function submit_intervention(){
      $alarmtype         = $_POST['alarmtype'];
      $user_id           = $_POST['user_id'];
      $user_name         = $_POST['user_name'];
      $alert_id          = $_POST['alert_id'];
      $tablenya          = $_POST['tablenya'];
      $intervention_date = $_POST['intervention_date'];
      $fatigue_category  = $_POST['fatigue_category'];
      $itervention_name  = $_POST['itervention_name'];
      $itervention_sid   = $_POST['itervention_sid'];
      $alarm_true_false  = $_POST['alarm_true_false'];
      $itervention_alarm = $_POST['itervention_alarm'];
      $intervention_note = $_POST['intervention_note'];
      $alarm_true_false_fix  = 0;
      $itervention_alarm_fix = 0;

      $data_karyawan          = $this->m_dashboardberau->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_login);

      // echo "<pre>";
      // var_dump($data_karyawan);die();
      // echo "<pre>";

      if ($alarm_true_false == 1) {
        $alarm_true_false_fix = 1;
      }else {
        $alarm_true_false_fix = 2;
      }

      if ($itervention_alarm == 1) {
        $itervention_alarm_fix = 1;
      }else {
        $itervention_alarm_fix = 2;
      }

      $postevent_explode = explode("|", $itervention_sid);
      $postevent_sid     = $postevent_explode[0];
      $postevent_name    = $postevent_explode[1];

      $data                                  = array(
        "alarm_report_idsync_up2"            => $data_karyawan[0]['karyawan_bc_id_sync'],
        "alarm_report_sid_up2"               => $data_karyawan[0]['karyawan_bc_sid'],
        "alarm_report_name_up2"              => $data_karyawan[0]['karyawan_bc_name'],
        "alarm_report_id_up"                 => $user_id,
        "alarm_report_name_up"               => $user_name,
        "alarm_report_statusintervention_up" => $itervention_alarm_fix,
        "alarm_report_truefalse_up"          => $alarm_true_false_fix,
        "alarm_report_fatiguecategory_up"    => $fatigue_category,
        "alarm_report_note_up"               => $intervention_note,
        "alarm_report_datetime_up"           => $intervention_date,
        "alarm_report_datetime_up2"          => $intervention_date,
        // "alarm_report_sid_up"             => $postevent_sid,
        // "alarm_report_postevent_name_up"  => $postevent_name,
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_dashboardberau->update_post_event($tablenya, "alarm_report_id", $alert_id, $data);
        if ($update) {
          $callback["error"]   = false;
          $callback["message"] = "Success Submit Post Event";

          echo json_encode($callback);
        }else {
          $callback["error"]   = true;
          $callback["message"] = "Failed Submit Post Event";

          echo json_encode($callback);
        }

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";
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

    function getdatacontractor(){
    	$user_id      = $this->sess->user_id;
    	$user_company = $this->sess->user_company;
    	$user_parent  = $this->sess->user_parent;
    	$user_id_role = $this->sess->user_id_role;

    		if ($user_id_role == 0) {
    			$this->db->where("company_created_by", $user_id);
    		}elseif ($user_id_role == 1) {
    			$this->db->where("company_created_by", $user_parent);
    		}elseif ($user_id_role == 2) {
    			$this->db->where("company_created_by", $user_parent);
    		}elseif ($user_id_role == 3) {
    			$this->db->where("company_created_by", $user_parent);
    		}elseif ($user_id_role == 4) {
    			$this->db->where("company_created_by", $user_parent);
    		}elseif ($user_id_role == 5) {
    			$this->db->where("company_id", $user_company);
    		}elseif ($user_id_role == 6) {
    			$this->db->where("company_id", $user_company);
    		}

    	$this->db->where("company_flag", 0);
    	$this->db->where_in("company_exca", array(0, 2));
    	$this->db->order_by("company_name", "ASC");
    	$q     = $this->db->get("company");
    	$rows  = $q->result_array();

    	// echo "<pre>";
    	// var_dump($rows);die();
    	// echo "<pre>";

    	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
    }
    // DASHBOARD POST EVENT End

    // DASHBOARD POST EVENT CONTROL ROOM VERSION START
    function posteventcontrolroom(){
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

  		$this->params['data']           = $this->m_dashboardberau->getdevice();
  		$this->params['alarmtype']      = $this->m_dashboardberau->getalarmmaster();

  		// echo "<pre>";
  		// var_dump($this->params['data']);die();
  		// echo "<pre>";

  		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
  		$this->params["rcompany"]       = $rows_company;

      $user_id       = $this->sess->user_id;
  		$user_parent   = $this->sess->user_parent;
  		$privilegecode = $this->sess->user_id_role;
  		$user_company  = $this->sess->user_company;

  		if($privilegecode == 0){
  			$user_id_fix = $user_id;
  		}elseif ($privilegecode == 1) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 2) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 3) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 4) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 5) {
  			$user_id_fix = $user_id;
  		}elseif ($privilegecode == 6) {
  			$user_id_fix = $user_id;
  		}else{
  			$user_id_fix = $user_id;
  		}

  		$companyid                       = $this->sess->user_company;
  		$user_dblive                     = $this->sess->user_dblive;
  		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

  		$datafix                         = array();
  		$deviceidygtidakada              = array();
  		$statusvehicle['engine_on']  = 0;
  		$statusvehicle['engine_off'] = 0;

  		for ($i=0; $i < sizeof($mastervehicle); $i++) {
  			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
  			if (isset($jsonautocheck->auto_status)) {
  				// code...
  			$auto_status   = $jsonautocheck->auto_status;

  			if ($privilegecode == 5 || $privilegecode == 6) {
  				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
  					if ($jsonautocheck->auto_last_engine == "ON") {
  						$statusvehicle['engine_on'] += 1;
  					}else {
  						$statusvehicle['engine_off'] += 1;
  					}
  				}
  			}else {
  				if ($jsonautocheck->auto_last_engine == "ON") {
  					$statusvehicle['engine_on'] += 1;
  				}else {
  					$statusvehicle['engine_off'] += 1;
  				}
  			}

  				if ($auto_status != "M") {
  					array_push($datafix, array(
  						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
  						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
  						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
  						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
  						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
  						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
  						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
  						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
  						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
  					));
  				}
  			}
  		}

  		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
  			if ($company) {

  					$datavehicleandcompany    = array();
  					$datavehicleandcompanyfix = array();

  						for ($d=0; $d < sizeof($company); $d++) {
  							$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
  							// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
  							$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
  							$totalengine       = explode("|", $totaldata);
  								array_push($datavehicleandcompany, array(
  									"company_id"   => $company[$d]->company_id,
  									"company_name" => $company[$d]->company_name,
  									"totalmobil"   => $totalengine[2],
  									"vehicle"      => $vehicledata[$d]
  								));
  						}
  				$this->params['company']   = $company;
  				$this->params['companyid'] = $companyid;
  				$this->params['vehicle']   = $datavehicleandcompany;
  			}else {
  				$this->params['company']   = 0;
  				$this->params['companyid'] = 0;
  				$this->params['vehicle']   = 0;
  			}

  		// echo "<pre>";
  		// var_dump($company);die();
  		// echo "<pre>";


  		$this->params['url_code_view']  = "1";
  		$this->params['code_view_menu'] = "monitor";
  		$this->params['maps_code']      = "morehundred";

  		$this->params['engine_on']      = $statusvehicle['engine_on'];
  		$this->params['engine_off']     = $statusvehicle['engine_off'];


  		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

  		$datastatus                     = explode("|", $rstatus);
  		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
  		$this->params['total_vehicle']  = $datastatus[3];
  		$this->params['total_offline']  = $datastatus[2];

  		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/dashboardberau/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
    }

    function searchreport_controlroom(){
  		ini_set('display_errors', 1);
  		//ini_set('memory_limit', '2G');
  		if (! isset($this->sess->user_type))
  		{
  			redirect(base_url());
  		}

  		$company           = $this->input->post("company");
  		$vehicle           = $this->input->post("vehicle");
  		$startdate         = $this->input->post("startdate");
  		$enddate           = $this->input->post("enddate");
  		$shour             = $this->input->post("shour");
  		$ehour             = $this->input->post("ehour");
  		$alarmtype         = $this->input->post("alarmtype");
  		$periode           = $this->input->post("periode");
  		$km                = $this->input->post("km");
      $filter_true_false = $this->input->post("filter_true_false");
  		// $reporttype = $this->input->post("reporttype");
  		$reporttype = 0;
  		$alarmtypefromaster = array();

      $alarmtypefromaster = array();

      if ($alarmtype == 999999) {
  			$alarmtypefromaster[] = 9999;
  		}else {
        if ($alarmtype != "All") {
          $alarmbymaster = $this->m_dashboardberau->getalarmbytype($alarmtype);
          $alarmtypefromaster = array();
          for ($i=0; $i < sizeof($alarmbymaster); $i++) {
            $alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
          }
        }
  		}
      //
  		// if ($alarmtype != "All") {
  		// 	$alarmbymaster = $this->m_dashboardberau->getalarmbytype($alarmtype);
  		// 	$alarmtypefromaster = array();
  		// 	for ($i=0; $i < sizeof($alarmbymaster); $i++) {
  		// 		$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
  		// 	}
  		// }

  		// echo "<pre>";
  		// var_dump($alarmtype);die();
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
      $report_overspeed = "overspeed_hour_";


  		// print_r($periode);exit();

  		if($periode == "custom"){
  			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($startdate." ".$shour)));
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}elseif ($periode == "today") {
        // if (date("d") == 01) {
          $sdate = date("Y-m-d 00:00:00");
        // }else {
        //   $sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
        // }
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

  		// print_r(date("d").'-'.$periode.'-'.$sdate." ".$edate);exit();

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
        $dbtable_overspeed = $report_overspeed."januari_".$year;
  			break;
  			case "February":
              $dbtable = $report."februari_".$year;
  			$dbtable_sum = $report_sum."februari_".$year;
        $dbtable_overspeed = $report_overspeed."februari_".$year;
  			break;
  			case "March":
              $dbtable = $report."maret_".$year;
  			$dbtable_sum = $report_sum."maret_".$year;
        $dbtable_overspeed = $report_overspeed."maret_".$year;
  			break;
  			case "April":
              $dbtable = $report."april_".$year;
  			$dbtable_sum = $report_sum."april_".$year;
        $dbtable_overspeed = $report_overspeed."april_".$year;
  			break;
  			case "May":
              $dbtable = $report."mei_".$year;
  			$dbtable_sum = $report_sum."mei_".$year;
        $dbtable_overspeed = $report_overspeed."mei_".$year;
  			break;
  			case "June":
              $dbtable = $report."juni_".$year;
  			$dbtable_sum = $report_sum."juni_".$year;
        $dbtable_overspeed = $report_overspeed."juni_".$year;
  			break;
  			case "July":
              $dbtable = $report."juli_".$year;
  			$dbtable_sum = $report_sum."juli_".$year;
        $dbtable_overspeed = $report_overspeed."juli_".$year;
  			break;
  			case "August":
              $dbtable = $report."agustus_".$year;
  			$dbtable_sum = $report_sum."agustus_".$year;
        $dbtable_overspeed = $report_overspeed."agustus_".$year;
  			break;
  			case "September":
              $dbtable = $report."september_".$year;
  			$dbtable_sum = $report_sum."september_".$year;
        $dbtable_overspeed = $report_overspeed."september_".$year;
  			break;
  			case "October":
              $dbtable = $report."oktober_".$year;
  			$dbtable_sum = $report_sum."oktober_".$year;
        $dbtable_overspeed = $report_overspeed."oktober_".$year;
  			break;
  			case "November":
              $dbtable = $report."november_".$year;
  			$dbtable_sum = $report_sum."november_".$year;
        $dbtable_overspeed = $report_overspeed."november_".$year;
  			break;
  			case "December":
              $dbtable = $report."desember_".$year;
  			$dbtable_sum = $report_sum."desember_".$year;
        $dbtable_overspeed = $report_overspeed."desember_".$year;
  			break;
  		}

      // GET DATA ALERT OVERSPEED
      $data_array_alert = array();
      if ($alarmtype == 999999 || $alarmtype == "All") {
        $data_overspeed   = $this->m_dashboardberau->get_overspeed_intensor_intervention($dbtable_overspeed, $vehicle, $company, $sdate, $edate, $filter_true_false);

        // echo "<pre>";
        // // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$company.'-'.$sdate.'-'.$edate.'-'.$filter_true_false);die();
        // var_dump($vehicle);die();
        // echo "<pre>";

    		for ($i=0; $i < sizeof($data_overspeed); $i++) {

          if (isset($data_overspeed[$i]['overspeed_report_spvsid_cr'])) {
            $overspeed_report_id_cr =  $data_overspeed[$i]['overspeed_report_spvsid_cr'];
          }else {
            $overspeed_report_id_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_spvname_cr'])) {
            $overspeed_report_name_cr =  $data_overspeed[$i]['overspeed_report_spvname_cr'];
          }else {
            $overspeed_report_name_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_spvsid_cr'])) {
            $overspeed_report_sid_cr =  $data_overspeed[$i]['overspeed_report_spvsid_cr'];
          }else {
            $overspeed_report_sid_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_statusintervention_cr'])) {
            $overspeed_report_statusintervention_cr =  $data_overspeed[$i]['overspeed_report_statusintervention_cr'];
          }else {
            $overspeed_report_statusintervention_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_intervention_category_cr'])) {
            $overspeed_report_intervention_category_cr =  $data_overspeed[$i]['overspeed_report_intervention_category_cr'];
          }else {
            $overspeed_report_intervention_category_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_fatiguecategory_cr'])) {
            $overspeed_report_fatiguecategory_cr =  $data_overspeed[$i]['overspeed_report_fatiguecategory_cr'];
          }else {
            $overspeed_report_fatiguecategory_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_note_cr'])) {
            $overspeed_report_note_cr =  $data_overspeed[$i]['overspeed_report_note_cr'];
          }else {
            $overspeed_report_note_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_datetime_cr'])) {
            $overspeed_report_datetime_cr =  $data_overspeed[$i]['overspeed_report_datetime_cr'];
          }else {
            $overspeed_report_datetime_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
            $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
          }else {
            $overspeed_report_note_up = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_truefalse_up'])) {
            $overspeed_report_truefalse_up =  $data_overspeed[$i]['overspeed_report_truefalse_up'];
          }else {
            $overspeed_report_truefalse_up = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_datetime_cr'])) {
            $overspeed_report_datetime_cr =  date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_datetime_cr'] . "+1 Hour"));
          }else {
            $overspeed_report_datetime_cr = "";
          }

    			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
            if ($data_overspeed[$i]['overspeed_report_event_location'] != "") {
              array_push($data_array_alert, array(
                "isfatigue"                                => "no",
                "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
                "alarm_report_vehicle_id"                  => $data_overspeed[$i]['overspeed_report_vehicle_id'],
                "alarm_report_vehicle_no"                  => $data_overspeed[$i]['overspeed_report_vehicle_no'],
                "alarm_report_vehicle_name"                => $data_overspeed[$i]['overspeed_report_vehicle_name'],
                "alarm_report_type"                        => "Overspeed",
                "alarm_report_name"                        => "Overspeed " . $data_overspeed[$i]['overspeed_report_level_alias'],
                "alarm_report_start_time"                  => $data_overspeed[$i]['overspeed_report_gps_time'],
                "alarm_report_end_time"                    => $data_overspeed[$i]['overspeed_report_gps_time'],
                "alarm_report_coordinate_start"            => $data_overspeed[$i]['overspeed_report_coordinate'],
                "alarm_report_coordinate_end"              => $data_overspeed[$i]['overspeed_report_coordinate'],
                "alarm_report_location_start"              => $data_overspeed[$i]['overspeed_report_event_location'],
                "alarm_report_speed" 			                 => $data_overspeed[$i]['overspeed_report_speed'],
                "alarm_report_datetime_cr" 			           => $overspeed_report_datetime_cr,
                "alarm_report_image_link" 			           => "",
                "alarm_report_video_link" 			           => "",
                "alarm_report_geofence" 	                 => $data_overspeed[$i]['overspeed_report_geofence_name'],
                "alarm_report_speed_limit"                 => ($data_overspeed[$i]['overspeed_report_geofence_limit']-3),
                "alarm_report_speed_time" 		             => "",
                "alarm_report_speed_status" 	             => "",
                "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
                "alarm_report_id_cr" 	                     => $overspeed_report_id_cr,
                "alarm_report_name_cr" 	                   => $overspeed_report_name_cr,
                "alarm_report_sid_cr" 	                   => $overspeed_report_sid_cr,
                "alarm_report_statusintervention_cr" 	     => $overspeed_report_statusintervention_cr,
                "alarm_report_intervention_category_cr" 	 => $overspeed_report_intervention_category_cr,
                "alarm_report_fatiguecategory_cr" 	       => "0",
                "alarm_report_note_cr" 	                   => $overspeed_report_note_cr,
                "alarm_report_datetime_cr" 	               => $overspeed_report_datetime_cr,
                "alarm_report_note_up" 	                   => $overspeed_report_note_up,
                "alarm_report_truefalse_up" 	             => $overspeed_report_truefalse_up,
              ));
            }
    		}
      }

  		// echo "<pre>";
  		// var_dump($data_array_alert);die();
  		// echo "<pre>";

      // GET DATA MDVR ALERT
      if ($alarmtype != 999999) {
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
            // echo "<pre>";
            // var_dump($user_company);die();
            // echo "<pre>";
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

  		// $nowday            = date("d");
  		// $end_day_fromEdate = date("d", strtotime($edate));
      //
  		// if ($nowday == $end_day_fromEdate) {
  		// 	$edate = date("Y-m-d H:i:s");
  		// }

  		$this->dbtrip->where("alarm_report_start_time <=", $edate);
  		if($km != ""){
  			$this->dbtrip->where("alarm_report_location_start", "KM ".$km);
  		}

  		if ($alarmtype != "All") {
  			$this->dbtrip->where_in('alarm_report_type', $alarmtypefromaster); //$alarmtype $alarmbymaster[0]['alarm_type']
  		}

      if ($filter_true_false != "All") {
        $this->dbtrip->where('alarm_report_truefalse_up', $filter_true_false); //$alarmtype $alarmbymaster[0]['alarm_type']
      }

  		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
  		//$this->dbtrip->where("alarm_report_speed_status",1);		//buka untuk trial evalia
  		//$this->dbtrip->like("alarm_report_location_start", "KM"); //buka untuk trial evalia
  		$this->dbtrip->where("alarm_report_gpsstatus !=","");
      $this->dbtrip->where("alarm_report_gpsstatus !=","pushalert");
  		// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
  		$this->dbtrip->order_by("alarm_report_start_time","asc");
  		// $this->dbtrip->group_by("alarm_report_start_time");
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

  		// $datafix = array();
  		for ($j=0; $j < sizeof($thisreport); $j++) {
    		$reportdetailvideo          = $this->m_dashboardberau->getdetailreportvideo($dbtable, $thisreport[$j]['alarm_report_vehicle_id'], $thisreport[$j]['alarm_report_start_time']);
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


          if (isset($thisreport[$j]['alarm_report_id_cr'])) {
            $alarm_report_id_cr =  $thisreport[$j]['alarm_report_id_cr'];
          }else {
            $alarm_report_id_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_name_cr'])) {
            $alarm_report_name_cr =  $thisreport[$j]['alarm_report_name_cr'];
          }else {
            $alarm_report_name_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_sid_cr'])) {
            $alarm_report_sid_cr =  $thisreport[$j]['alarm_report_sid_cr'];
          }else {
            $alarm_report_sid_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_statusintervention_cr'])) {
            $alarm_report_statusintervention_cr =  $thisreport[$j]['alarm_report_statusintervention_cr'];
          }else {
            $alarm_report_statusintervention_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_intervention_category_cr'])) {
            $alarm_report_intervention_category_cr =  $thisreport[$j]['alarm_report_intervention_category_cr'];
          }else {
            $alarm_report_intervention_category_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_fatiguecategory_cr'])) {
            $alarm_report_fatiguecategory_cr =  $thisreport[$j]['alarm_report_fatiguecategory_cr'];
          }else {
            $alarm_report_fatiguecategory_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_note_cr'])) {
            $alarm_report_note_cr =  $thisreport[$j]['alarm_report_note_cr'];
          }else {
            $alarm_report_note_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_datetime_cr'])) {
            $alarm_report_datetime_cr =  $thisreport[$j]['alarm_report_datetime_cr'];
          }else {
            $alarm_report_datetime_cr = "";
          }

          if (isset($thisreport[$j]['alarm_report_note_up'])) {
            $alarm_report_note_up =  $thisreport[$j]['alarm_report_note_up'];
          }else {
            $alarm_report_note_up = "";
          }

          if (isset($thisreport[$j]['alarm_report_truefalse_up'])) {
            $alarm_report_truefalse_up =  $thisreport[$j]['alarm_report_truefalse_up'];
          }else {
            $alarm_report_truefalse_up = "";
          }

          if (isset($thisreport[$j]['alarm_report_datetime_cr'])) {
            $alarm_report_datetime_cr =  date("Y-m-d H:i:s", strtotime($thisreport[$j]['alarm_report_datetime_cr'] . "+1 Hour"));
          }else {
            $alarm_report_datetime_cr = "";
          }

  				array_push($data_array_alert, array(
            "alarm_report_id"                          => $thisreport[$j]['alarm_report_id'],
  					"alarm_report_vehicle_id"                  => $thisreport[$j]['alarm_report_vehicle_id'],
  					"alarm_report_vehicle_no"                  => $thisreport[$j]['alarm_report_vehicle_no'],
  					"alarm_report_vehicle_name"                => $thisreport[$j]['alarm_report_vehicle_name'],
            "alarm_report_type"                        => $thisreport[$j]['alarm_report_type'],
  					"alarm_report_name"                        => $alarmreportnamefix,
            "alarm_report_datetime_cr"                 => $alarm_report_datetime_cr,
  					"alarm_report_start_time"                  => $thisreport[$j]['alarm_report_start_time'],
  					"alarm_report_end_time"                    => $thisreport[$j]['alarm_report_end_time'],
  					"alarm_report_coordinate_start"            => $thisreport[$j]['alarm_report_coordinate_start'],
  					"alarm_report_coordinate_end"              => $thisreport[$j]['alarm_report_coordinate_end'],
  					"alarm_report_location_start"              => $thisreport[$j]['alarm_report_location_start'],
  					"alarm_report_speed" 			                 => $thisreport[$j]['alarm_report_speed'],
            "alarm_report_image_link" 			           => $thisreport[$j]['alarm_report_fileurl'],
            "alarm_report_video_link" 			           => $urlvideofix,
            "alarm_report_geofence" 	                 => "",
            "alarm_report_speed_limit"                 => "",
  					"alarm_report_speed_time" 		             => $thisreport[$j]['alarm_report_speed_time'],
  					"alarm_report_speed_status" 	             => $thisreport[$j]['alarm_report_speed_status'],
  					"alarm_report_jalur" 	                     => $thisreport[$j]['alarm_report_jalur'],
            "alarm_report_id_cr" 	                     => $alarm_report_id_cr,
            "alarm_report_name_cr" 	                   => $alarm_report_name_cr,
            "alarm_report_sid_cr" 	                   => $alarm_report_sid_cr,
            "alarm_report_statusintervention_cr" 	     => $alarm_report_statusintervention_cr,
            "alarm_report_intervention_category_cr" 	 => $alarm_report_intervention_category_cr,
            "alarm_report_fatiguecategory_cr" 	       => $alarm_report_fatiguecategory_cr,
            "alarm_report_note_cr" 	                   => $alarm_report_note_cr,
            "alarm_report_datetime_cr" 	               => $alarm_report_datetime_cr,
            "alarm_report_note_up" 	                   => $alarm_report_note_up,
            "alarm_report_truefalse_up" 	             => $alarm_report_truefalse_up,
  				));
  		}
    }

      usort($data_array_alert, function($a, $b) {
         return strtotime($b['alarm_report_start_time']) - strtotime($a['alarm_report_start_time']);
     });

     $data_array_fix = array();
     if($alarmtype == 999999) {
       for ($i=0; $i < sizeof($data_array_alert); $i++) {
       $violation_type = $data_array_alert[$i]['alarm_report_type'];
         if ($violation_type == "Overspeed") {
           array_push($data_array_fix, array(
             "isfatigue"                               => "no",
             "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
             "alarm_report_vehicle_id"                 => $data_array_alert[$i]['alarm_report_vehicle_id'],
             "alarm_report_vehicle_no"                 => $data_array_alert[$i]['alarm_report_vehicle_no'],
             "alarm_report_vehicle_name"               => $data_array_alert[$i]['alarm_report_vehicle_name'],
             "alarm_report_type"                       => $data_array_alert[$i]['alarm_report_type'],
             "alarm_report_name"                       => $data_array_alert[$i]['alarm_report_name'],
             "alarm_report_datetime_cr"                => $data_array_alert[$i]['alarm_report_datetime_cr'],
             "alarm_report_start_time"                 => $data_array_alert[$i]['alarm_report_start_time'],
             "alarm_report_end_time"                   => $data_array_alert[$i]['alarm_report_end_time'],
             "alarm_report_coordinate_start"           => $data_array_alert[$i]['alarm_report_coordinate_start'],
             "alarm_report_coordinate_end"             => $data_array_alert[$i]['alarm_report_coordinate_end'],
             "alarm_report_location_start"             => $data_array_alert[$i]['alarm_report_location_start'],
             "alarm_report_speed" 			               => $data_array_alert[$i]['alarm_report_speed'],
             "alarm_report_geofence" 			             => $data_array_alert[$i]['alarm_report_geofence'],
             "alarm_report_speed_limit"                => $data_array_alert[$i]['alarm_report_speed_limit'],
             "alarm_report_speed_time" 		             => $data_array_alert[$i]['alarm_report_speed_time'],
             "alarm_report_speed_status" 	             => $data_array_alert[$i]['alarm_report_speed_status'],
             "alarm_report_jalur" 	                   => $data_array_alert[$i]['alarm_report_jalur'],
             "alarm_report_id_cr" 	                   => $data_array_alert[$i]['alarm_report_id_cr'],
             "alarm_report_name_cr" 	                 => $data_array_alert[$i]['alarm_report_name_cr'],
             "alarm_report_sid_cr" 	                   => $data_array_alert[$i]['alarm_report_sid_cr'],
             "alarm_report_statusintervention_cr" 	   => $data_array_alert[$i]['alarm_report_statusintervention_cr'],
             "alarm_report_intervention_category_cr" 	 => $data_array_alert[$i]['alarm_report_intervention_category_cr'],
             "alarm_report_fatiguecategory_cr" 	       => $data_array_alert[$i]['alarm_report_fatiguecategory_cr'],
             "alarm_report_note_cr" 	                 => $data_array_alert[$i]['alarm_report_note_cr'],
             "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
             "alarm_report_truefalse_up" 	             => $data_array_alert[$i]['alarm_report_truefalse_up'],
             "alarm_report_image_link" 	               => $data_array_alert[$i]['alarm_report_image_link'],
             "alarm_report_video_link" 	               => $data_array_alert[$i]['alarm_report_video_link'],
           ));
         }
       }
     }elseif($alarmtype != "All") {
       for ($i=0; $i < sizeof($data_array_alert); $i++) {
         $violation_type = $data_array_alert[$i]['alarm_report_type'];
           if ($violation_type != "Overspeed") {
             array_push($data_array_fix, array(
               "isfatigue"                               => "yes",
               "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
               "alarm_report_vehicle_id"                 => $data_array_alert[$i]['alarm_report_vehicle_id'],
               "alarm_report_vehicle_no"                 => $data_array_alert[$i]['alarm_report_vehicle_no'],
               "alarm_report_vehicle_name"               => $data_array_alert[$i]['alarm_report_vehicle_name'],
               "alarm_report_type"                       => $data_array_alert[$i]['alarm_report_type'],
               "alarm_report_name"                       => $data_array_alert[$i]['alarm_report_name'],
               "alarm_report_datetime_cr"                => $data_array_alert[$i]['alarm_report_datetime_cr'],
               "alarm_report_start_time"                 => $data_array_alert[$i]['alarm_report_start_time'],
               "alarm_report_end_time"                   => $data_array_alert[$i]['alarm_report_end_time'],
               "alarm_report_coordinate_start"           => $data_array_alert[$i]['alarm_report_coordinate_start'],
               "alarm_report_coordinate_end"             => $data_array_alert[$i]['alarm_report_coordinate_end'],
               "alarm_report_location_start"             => $data_array_alert[$i]['alarm_report_location_start'],
               "alarm_report_speed" 			               => $data_array_alert[$i]['alarm_report_speed'],
               "alarm_report_geofence" 			             => "",
               "alarm_report_speed_limit"                => "",
               "alarm_report_speed_time" 		             => $data_array_alert[$i]['alarm_report_speed_time'],
               "alarm_report_speed_status" 	             => $data_array_alert[$i]['alarm_report_speed_status'],
               "alarm_report_jalur" 	                   => $data_array_alert[$i]['alarm_report_jalur'],
               "alarm_report_id_cr" 	                   => $data_array_alert[$i]['alarm_report_id_cr'],
               "alarm_report_name_cr" 	                 => $data_array_alert[$i]['alarm_report_name_cr'],
               "alarm_report_sid_cr" 	                   => $data_array_alert[$i]['alarm_report_sid_cr'],
               "alarm_report_statusintervention_cr" 	   => $data_array_alert[$i]['alarm_report_statusintervention_cr'],
               "alarm_report_intervention_category_cr" 	 => $data_array_alert[$i]['alarm_report_intervention_category_cr'],
               "alarm_report_fatiguecategory_cr" 	       => $data_array_alert[$i]['alarm_report_fatiguecategory_cr'],
               "alarm_report_note_cr" 	                 => $data_array_alert[$i]['alarm_report_note_cr'],
               "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
               "alarm_report_truefalse_up" 	             => $data_array_alert[$i]['alarm_report_truefalse_up'],
               "alarm_report_image_link" 	               => $data_array_alert[$i]['alarm_report_image_link'],
               "alarm_report_video_link" 	               => $data_array_alert[$i]['alarm_report_video_link'],
             ));
           }
       }
     }else {
       $data_array_fix = $data_array_alert;
     }
     
     var_dump($data_array_fix);
     die();

  		$this->params['content']   = $data_array_fix;
      $this->params['alarmtype'] = $alarmtype;
  		$html                      = $this->load->view('newdashboard/dashboardberau/intervention/v_postevent_result', $this->params, true);
  		$callback["html"]          = $html;
  		$callback["report"]        = $data_array_alert;

  		echo json_encode($callback);
  	}

    function intervention_detail_controlroom(){
  		$alert_id        = $this->input->post("alert_id");
  		$sdate           = $this->input->post("sdate");
      $alarm_report_id = $this->input->post("alarm_report_id");
      $alarmtype       = $this->input->post("alarmtype");
  		$report          = "alarm_evidence_";
  		$reportoverspeed = "overspeed_hour_";
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

      if ($alarmtype == "Overspeed") {
        $data_array_alert = array();
    		$data_overspeed   = $this->m_dashboardberau->get_overspeed_intensor_intervention_detail($dbtableoverspeed, $alarm_report_id, $sdate);

          // echo "<pre>";
          // var_dump($data_overspeed);die();
          // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$sdate);die();
          // echo "<pre>";

    		for ($i=0; $i < sizeof($data_overspeed); $i++) {

          if (isset($data_overspeed[$i]['overspeed_report_spvidsync_cr'])) {
            $overspeed_report_id_cr =  $data_overspeed[$i]['overspeed_report_spvidsync_cr'];
          }else {
            $overspeed_report_id_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_spvname_cr'])) {
            $overspeed_report_name_cr =  $data_overspeed[$i]['overspeed_report_spvname_cr'];
          }else {
            $overspeed_report_name_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_spvsid_cr'])) {
            $overspeed_report_sid_cr =  $data_overspeed[$i]['overspeed_report_spvsid_cr'];
          }else {
            $overspeed_report_sid_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_statusintervention_cr'])) {
            $overspeed_report_statusintervention_cr =  $data_overspeed[$i]['overspeed_report_statusintervention_cr'];
          }else {
            $overspeed_report_statusintervention_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_judgement_cr'])) {
            $overspeed_report_judgement_cr =  $data_overspeed[$i]['overspeed_report_judgement_cr'];
          }else {
            $overspeed_report_judgement_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_id_lokasi'])) {
            $overspeed_report_id_lokasi =  $data_overspeed[$i]['overspeed_report_id_lokasi'];
          }else {
            $overspeed_report_id_lokasi = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_intervention_category_cr'])) {
            $overspeed_report_intervention_category_cr =  $data_overspeed[$i]['overspeed_report_intervention_category_cr'];
          }else {
            $overspeed_report_intervention_category_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_fatiguecategory_cr'])) {
            $overspeed_report_fatiguecategory_cr =  $data_overspeed[$i]['overspeed_report_fatiguecategory_cr'];
          }else {
            $overspeed_report_fatiguecategory_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_note_cr'])) {
            $overspeed_report_note_cr =  $data_overspeed[$i]['overspeed_report_note_cr'];
          }else {
            $overspeed_report_note_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_datetime_cr'])) {
            $overspeed_report_datetime_cr =  $data_overspeed[$i]['overspeed_report_datetime_cr'];
          }else {
            $overspeed_report_datetime_cr = "";
          }

          if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
            $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
          }else {
            $overspeed_report_note_up = "";
          }

    			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
    			array_push($data_array_alert, array(
    				"isfatigue"                                => "no",
            "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
            "alarm_report_vehicle_id"                  => $data_overspeed[$i]['overspeed_report_vehicle_id'],
            "alarm_report_vehicle_no"                  => $data_overspeed[$i]['overspeed_report_vehicle_no'],
            "alarm_report_vehicle_name"                => $data_overspeed[$i]['overspeed_report_vehicle_name'],
            "alarm_report_type"                        => "Overspeed",
            "alarm_report_name"                        => "Overspeed",
            "alarm_report_start_time"                  => $data_overspeed[$i]['overspeed_report_gps_time'],
            "alarm_report_end_time"                    => $data_overspeed[$i]['overspeed_report_gps_time'],
            "alarm_report_coordinate_start"            => $data_overspeed[$i]['overspeed_report_coordinate'],
            "alarm_report_coordinate_end"              => $data_overspeed[$i]['overspeed_report_coordinate'],
            "alarm_report_location_start"              => $data_overspeed[$i]['overspeed_report_event_location'],
            "alarm_report_speed" 			                 => $data_overspeed[$i]['overspeed_report_speed'],
            "alarm_report_geofence" 	                 => $data_overspeed[$i]['overspeed_report_geofence_name'],
            "alarm_report_speed_limit"                 => ($data_overspeed[$i]['overspeed_report_geofence_limit']-3),
            "overspeed_report_level_alias"             => $data_overspeed[$i]['overspeed_report_level_alias'],
            "alarm_report_speed_time" 		             => "",
            "alarm_report_speed_status" 	             => "",
            "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
            "alarm_report_id_cr" 	                     => $overspeed_report_id_cr,
            "alarm_report_name_cr" 	                   => $overspeed_report_name_cr,
            "alarm_report_sid_cr" 	                   => $overspeed_report_sid_cr,
            "alarm_report_judgement_cr"                => $overspeed_report_judgement_cr,
            "alarm_report_id_lokasi"                   => $overspeed_report_id_lokasi,
            "alarm_report_statusintervention_cr" 	     => $overspeed_report_statusintervention_cr,
            "alarm_report_intervention_category_cr" 	 => $overspeed_report_intervention_category_cr,
            "alarm_report_fatiguecategory_cr" 	       => "",
            "alarm_report_note_cr" 	                   => $overspeed_report_note_cr,
            "alarm_report_datetime_cr" 	               => $overspeed_report_datetime_cr,
            "alarm_report_note_up" 	                   => $overspeed_report_note_up,
    			));
    		}
        // echo "<pre>";
        // var_dump($data_array_alert);die();
        // echo "<pre>";

        $data_site                       = $this->m_dashboardberau->master_site_bc_all("ts_bc_master_site");
        $this->params['data_site']       = $data_site;

        $type_intervention                   = $this->m_dashboardberau->get_type_intervention();
        // echo "<pre>";
        // var_dump($type_intervention);die();
        // echo "<pre>";
        $this->params['type_intervention']   = $type_intervention;

        $type_note                           = $this->m_dashboardberau->get_type_note(1);
        $this->params['type_note']           = $type_note;

        $data_karyawan_bc                    = $this->m_dashboardberau->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_name);
        $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        $data_karyawan_bc                    = $this->m_dashboardberau->check_data_karyawan();
        $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        $this->params['content']             = $data_array_alert;
        $this->params['alert_id']            = $alarm_report_id;
        $this->params['alarmtype']           = $alarmtype;
        $this->params['tablenya']            = $dbtableoverspeed;

        $this->params['monthforparam'] 			 = $monthforparam;
        $this->params['year'] 			         = $year;
        $this->params['user_id_role'] 			 = $this->sess->user_id_role;
        // echo "<pre>";
        // var_dump($data_array_alert);die();
        // echo "<pre>";
        $html                                = $this->load->view('newdashboard/dashboardberau/intervention/v_intervention_modal_overspeed', $this->params, true);
        $callback["report"]                  = $data_array_alert;
        $callback["html"]                    = $html;
      }else {
        $reportdetail               = $this->m_dashboardberau->getdetailreport($table, $alert_id, $sdate);
    		$reportdetailvideo          = $this->m_dashboardberau->getdetailreportvideo($table, $alert_id, $sdate);
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

          $type_intervention                 = $this->m_dashboardberau->get_type_intervention();
          $this->params['type_intervention'] = $type_intervention;

          $type_note                         = $this->m_dashboardberau->get_type_note(1);
          $this->params['type_note']         = $type_note;

          $data_karyawan_bc                         = $this->m_dashboardberau->check_data_karyawan();
          $this->params['data_karyawan_bc']         = $data_karyawan_bc;


    		// echo "<pre>";
    		// var_dump($reportdetail);die();
    		// echo "<pre>";

    		$this->params['content']              = $reportdetail;
        $this->params['alert_id']             = $alarm_report_id;
        $this->params['alarmtype']            = $alarmtype;
        $this->params['tablenya']             = $table;
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
    		$html                                 = $this->load->view('newdashboard/dashboardberau/intervention/v_postevent_modal', $this->params, true);
    		$callback["html"]                     = $html;
    		$callback["report"]                   = $reportdetail;
      }
      echo json_encode($callback);
  	}

    function submit_intervention_controlroom_overspeed(){
      $user_id                   = $_POST['user_id'];
      $user_name                 = $_POST['user_name'];
      $alert_id                  = $_POST['alert_id'];
      // $alert_date                = $_POST['alert_date'];
      $alarm_report_vehicle_no   = $_POST['vehicle_no'];
      $alarm_report_vehicle_id   = $_POST['vehicle_id'];
      $tablenya                  = $_POST['tablenya'];
      $intervention_date         = $_POST['intervention_date'];
      // $intervention_category  = explode("|", $_POST['intervention_category']);
      $intervention_category     = $_POST['intervention_category'];
      // $itervention_sid        = explode("|", $_POST['itervention_sid']);
      $intervention_sid          = explode("|", $_POST['intervention_sid']);
      // $alarm_true_false       = $_POST['alarm_true_false'];
      // $itervention_alarm      = $_POST['itervention_alarm'];
      $intervention_note         = $_POST['intervention_note'];
      $fatigue_category          = $_POST['fatigue_category'];
      // $intervention_judgement = $_POST['intervention_judgement'];
      $intervention_supervisor   = explode("|", $_POST['intervention_supervisor']);
      // $id_lokasi                 = $_POST['id_lokasi']; // NANTI HARDCODE TUNGGU DARI BC
      // $intervention_working_type = $_POST['intervention_working_type'];

      $m1               = date("F", strtotime($intervention_date));
    	$year             = date("Y", strtotime($intervention_date));
    	$report           = "alarm_evidence_";
      $reportoverspeed  = "overspeed_hour_";
      $dbtable          = "";
      $dbtableoverspeed = "";

    	switch ($m1)
    	{
    		case "January":
    					$dbtable = $report."januari_".$year;
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

      $data_vehicle         = $this->m_dashboardberau->getDataVehicleById($alarm_report_vehicle_id);
      $data_evidence        = $this->m_dashboardberau->get_overspeed_intensor_intervention_detail_2($dbtableoverspeed, $alert_id);
      $table_driver_change  = "ts_driver_change_new";
      $data_driver_detected = $this->getdriverdetected($table_driver_change, $alarm_report_vehicle_no, $intervention_date);
      $remove_enter         = trim(preg_replace('/\s+/', ' ', $intervention_sid[0]));
      $intervention_fix     = str_replace(" ", "", $remove_enter);

      // echo "<pre>";
      // var_dump();die();
      // var_dump($string);die();
      // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$alert_date);die();
      // echo "<pre>";

        if (sizeof($data_driver_detected) > 0) {
          $driver_sid = $data_driver_detected[0]['change_driver_id'];
          $driver_name = $data_driver_detected[0]['change_driver_name'];
        }else {
          $driver_sid  = "";
          $driver_name = "";
        }

        $data_karyawan          = $this->m_dashboardberau->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_fix);
        $data_cr                = $this->m_dashboard_intervensi_postevent->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_supervisor[0]);
        // $data_site              = $this->m_dashboardberau->master_site_bc("ts_bc_master_site", $id_lokasi); // NANTI TUNGGU HARDCODE DARI BC
        $data_location          = $this->m_dashboardberau->master_location_bc("ts_bc_master_location", "114151");
        // $data_hse_object        = $this->m_dashboardberau->master_hse_object_bc("ts_bc_master_object"); // SEMENTARA BELUM DIGUNAKAN
        // $data_hse_object_detail = $this->m_dashboardberau->master_object_detail(); // SEMENTARA BELUM DIGUNAKAN

        // echo "<pre>";
        // var_dump($data_cr);die();
        // echo "<pre>";



        $data_category_type          = $this->m_development->master_category_type();
        $json_data_category_type     = json_decode($data_category_type);

        $data_pja              = $this->m_development->master_pja();
        $json_data_pja         = json_decode($data_pja, true);
        $rand_data_pja         = array_rand($json_data_pja, 1);

        $data_id_kategori      = array("999", "888");
        $rand_id_kategori      = array_rand($data_id_kategori, 1);

        $data_id_quick_action  = array("1", "2", "3");
        $rand_id_quickaction   = array_rand($data_id_quick_action, 1);

        $be_record_category    = array("2", "4");
        $rand_berecordcategory = array_rand($be_record_category, 1);

        $be_record_type        = array("2", "4");
        $rand_berecordcategory = array_rand($be_record_type, 1);

        $m_goldenrules         = array("2", "14");
        $rand_m_goldenrules    = array_rand($m_goldenrules, 1);

        // echo "<pre>";
        // var_dump($data_category_type);die();
        // echo "<pre>";

      $data = array(
        "overspeed_report_spvidsync_cr"             => $data_cr[0]['karyawan_bc_id_sync'],
        "overspeed_report_spvname_cr"               => $data_cr[0]['karyawan_bc_name'],
        "overspeed_report_spvsid_cr"                => $data_cr[0]['karyawan_bc_sid'],

        "overspeed_report_id_cr"                    => $data_karyawan[0]['karyawan_bc_id_sync'],
        "overspeed_report_name_cr"                  => $data_karyawan[0]['karyawan_bc_name'],
        "overspeed_report_sid_cr"                   => $data_karyawan[0]['karyawan_bc_sid'],
        "overspeed_report_statusintervention_cr"    => 1,
        "overspeed_report_intervention_category_cr" => $intervention_category,
        "overspeed_report_note_cr"                  => $intervention_note,
        // "overspeed_report_judgement_cr"             => $intervention_judgement,
        "overspeed_report_supervisor_cr"            => $intervention_supervisor[0].'|'.$intervention_supervisor[1],
        "overspeed_report_datetime_cr"              => $intervention_date,
        "overspeed_report_sid_driver"               => $driver_sid,
        "overspeed_report_driver_name"              => $driver_name,
        // "overspeed_report_working_type"             => $intervention_working_type,
        // "overspeed_report_master_site"              => $data_site[0]['master_site_id_sync'],
        "overspeed_report_tipe_unit"                => $data_vehicle[0]['vehicle_tipe_unit_for_integrasi'],
        // "overspeed_report_id_lokasi"                => $data_site[0]['master_site_id_sync'],
        // "overspeed_report_id_lokasi_detail"         => $data_location[0]['master_location_id_sync'],
        "overspeed_report_id_object"                => 3038, // Tingkah Laku Pekerja
        "overspeed_report_id_objectdetail"          => 30031228, // Tingkah Laku Pekerja
        "overspeed_report_goldenrule"               => 2,
        // "overspeed_report_id_perusahaan"            => $intervention_supervisor[0],
        // "overspeed_report_id_perusahaan"            => $id_lokasi, // NANTI HARDCODE TUNGGU DARI BC
        "overspeed_report_deskripsi"                => $data_evidence[0]['overspeed_report_vehicle_no']." melakukan pelanggaran Overspeed. Intervention Note : ".$intervention_note,
        "overspeed_report_lokasi_detail"            => "Terjadi pelanggaran Overspeed di lokasi ".$data_evidence[0]['overspeed_report_location'],
        "overspeed_report_id_kategori"              => $data_id_kategori[$rand_id_kategori],
        "overspeed_report_id_quick_action"          => $data_id_quick_action[$rand_id_quickaction],
        "overspeed_report_id_pja"                   => $json_data_pja[$rand_data_pja]['id'],
        "overspeed_report_id_pja_child"             => 1229,
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_dashboardberau->update_intervention($dbtableoverspeed, "overspeed_report_id", $alert_id, $data);
        if ($update) {
          $callback["error"]   = false;
          $callback["message"] = "Success Submit Intervention";

          echo json_encode($callback);
        }else {
          $callback["error"]   = true;
          $callback["message"] = "Failed Submit Intervention";

          echo json_encode($callback);
        }
    }

    function submit_intervention_controlroom(){
      $user_id                  = $_POST['user_id'];
      $user_name                = $_POST['user_name'];
      $alert_id                 = $_POST['alert_id'];
      $tablenya                 = $_POST['tablenya'];
      $intervention_date        = $_POST['intervention_date'];
      // $intervention_category = explode("|", $_POST['intervention_category']);
      $intervention_category    = $_POST['intervention_category'];
      $itervention_sid          = explode("|", $_POST['itervention_sid']);
      // $alarm_true_false      = $_POST['alarm_true_false'];
      // $itervention_alarm     = $_POST['itervention_alarm'];
      $intervention_note        = $_POST['intervention_note'];
      $fatigue_category         = $_POST['fatigue_category'];
      $intervention_judgement   = $_POST['intervention_judgement'];
      $intervention_supervisor  = $_POST['intervention_supervisor'];

      $data = array(
        "alarm_report_id_cr"                    => $user_id,
        "alarm_report_name_cr"                  => $itervention_sid[1],
        "alarm_report_sid_cr"                   => $itervention_sid[0],
        "alarm_report_statusintervention_cr"    => 1,
        "alarm_report_intervention_category_cr" => $intervention_category,
        "alarm_report_fatiguecategory_cr"       => $fatigue_category,
        "alarm_report_note_cr"                  => $intervention_note,
        "alarm_report_judgement_cr"             => $intervention_judgement,
        "alarm_report_supervisor_cr"            => $intervention_supervisor,
        "alarm_report_datetime_cr"              => $intervention_date,
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_dashboardberau->update_post_event($tablenya, "alarm_report_id", $alert_id, $data);
        if ($update) {
          $callback["error"]   = false;
          $callback["message"] = "Success Submit Intervention";

          echo json_encode($callback);
        }else {
          $callback["error"]   = true;
          $callback["message"] = "Failed Submit Intervention";

          echo json_encode($callback);
        }

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";
    }

    function data_intervention_note(){
      $intervention_type_id = $_POST['interv_type_id'];

      $data_type_note       = $this->m_dashboardberau->get_type_note($intervention_type_id);

      // echo "<pre>";
      // var_dump($data_type_note);die();
      // echo "<pre>";

      echo json_encode(array("data" => $data_type_note, "code" => 200));

    }

    function getdriverdetected($table, $vehicle_no, $sdate){
      $this->dbts = $this->load->database("webtracking_ts", true);
      $this->dbts->select("*");
      $this->dbts->where("change_driver_vehicle_no", $vehicle_no);
      $this->dbts->where("change_driver_time <= ", $sdate);

      $this->dbts->order_by("change_driver_time", "DESC");
      $this->dbts->limit(1);
  		$q          = $this->dbts->get($table);
  		return $q->result_array();
    }
    // DASHBOARD POST EVENT CONTROL ROOM VERSION END

}