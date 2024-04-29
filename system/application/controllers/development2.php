<?php
include "base.php";
require_once APPPATH."/third_party/Classes/PHPExcel.php";

class Development2 extends Base
{
    function __construct()
    {
        parent::Base();
        $this->load->model("dashboardmodel");
        $this->load->model("m_operational");
        $this->load->model("m_development");
        $this->load->model("gpsmodel");
        $this->load->model("m_poipoolmaster");
        $this->load->model("m_securityevidence");
        $this->load->model("m_dashboardberau");
    }

    /*
      DAFTAR ISI DEVELOPMENT
      1. FUEL REPORT
      2. WIM
      3. OPERATOR ITWS OTHER PORT
      4. Overspeed Report
      5. Quick Count BIB NEW
      6. History Under BIB
      7. Violation historikal / violation table
      8. Violation Historikal Report -> violation_historikalreport
      9. Fuel Sensor History report -> fuelsensorhistory
      10. Dashboard Kuota -> dashboardkuota
      11. Dashboard Streaming POC BERAU -> livemonitoring
      12. Dashboard Post Event -> dashboardpostevent
      13. Dashboard Intervention - versi control room -> dashboardintervention
      14. MDVR ANOMALI -> mdvranomalilist
      15. MDVR ANOMALI -> mdvranomali
      16. livemonitoring -> Dashboard live monitoring
      17. manualevidence -> Report Manual Input Evidence saat Streeaming
      18. dashboardinterventionexisting -> dashboardinterventionexisting
    */

    function mdvranomalilist(){
      ini_set('max_execution_time', '300');
      set_time_limit(300);
      if (! isset($this->sess->user_type))
      {
        redirect(base_url());
      }

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

      $companyid                   = $this->sess->user_company;
      $user_dblive                 = $this->sess->user_dblive;
      $mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
      $violationmaster             = $this->m_development->getviolationmaster();

      $datafix                     = array();
      $deviceidygtidakada          = array();
      $statusvehicle['engine_on']  = 0;
      $statusvehicle['engine_off'] = 0;

      for ($i=0; $i < sizeof($mastervehicle); $i++) {
        $jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
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

      // GET ROM ROAD
      $romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
      $this->params['rom_road'] = $romRoad;

      // echo "<pre>";
      // var_dump($romRoad);die();
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

      $this->params['vehicledata']  = $datafix;
      $this->params['vehicletotal'] = sizeof($mastervehicle);
      $this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
      $getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
      // echo "<pre>";
      // var_dump($getvehicle_byowner);die();
      // echo "<pre>";
      $totalmobilnya                = sizeof($getvehicle_byowner);
      if ($totalmobilnya == 0) {
        $this->params['name']         = "0";
        $this->params['host']         = "0";
      }else {
        $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
        $this->params['name']         = $arr[0];
        $this->params['host']         = $arr[1];
      }

      $this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
      $this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
      $this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
      $this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
      $this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
      $this->params['violationmaster'] = $violationmaster;

      // KONDISI UNTUK MDVR ANOMALI
      $alarm_mdvranomali              = $this->m_development->get_alarm_mdvranomali();
      $this->params["mdvr_anomali_list"] = $alarm_mdvranomali;


      // echo "<pre>";
      // var_dump($this->params['mdvr_anomali_list']);die();
      // echo "<pre>";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

        if ($privilegecode == 1) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        }elseif ($privilegecode == 2) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        }elseif ($privilegecode == 3) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        }elseif ($privilegecode == 4) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        }elseif ($privilegecode == 5) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        }elseif ($privilegecode == 6) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        }else {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvr_anomali_list', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function analytich(){
        
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

        $company = $this->dashboardmodel->getcompany_byowner($privilegecode);
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

        $this->params['url_code_view']  = "1";
        $this->params['code_view_menu'] = "monitor";
        $this->params['maps_code']      = "morehundred";

        $this->params['engine_on']      = $statusvehicle['engine_on'];
        $this->params['engine_off']     = $statusvehicle['engine_off'];

        $jabatan_search = array("Driver DT", "Operator");

        $data_karyawan_bc            = $this->m_development->data_karyawan_bymitra_jabatan($company[0]->company_sid_code, $jabatan_search);
        $this->params['data_driver'] = $data_karyawan_bc;

        // echo "<pre>";
            // var_dump($data_karyawan_bc);die();
            // echo "<pre>";

        $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

        $datastatus                     = explode("|", $rstatus);
        $this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
        $this->params['total_vehicle']  = $datastatus[3];
        $this->params['total_offline']  = $datastatus[2];

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        }elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard2/v_dashboard_postevent', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        }elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        }elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        }elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        }elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        }else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/dashboard2/v_dashboard_analytich', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function list_historikal_mdvr_anomali(){
      date_default_timezone_set("Asia/Jakarta");
      $limitnya	 								     = $_POST['limit_show_data'];
      $simultantype 								 = $_POST['simultantype'];
      $last_time_violation 					 = $_POST['last_time_violation'];
      $contractor 				           = $_POST['contractor'];
      $violationmasterselect 				 = $_POST['violationmaster'];
      $vehicle                       = "all";
      $street_onduty                 = $this->config->item("street_register");

      $nowtime          = date("Y-m-d H:i:s");
      $nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
      $last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-120 Minutes"));

      $sdate        = $last_fiveminutes;
      $edate          = $nowtime_wita;
      // $sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
      // $edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

      // CHOOSE DBTABLE
      $current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
      $m1                = date("F", strtotime($current_date));
      $year              = date("Y", strtotime($current_date));
      $dbtable           = "";
      $dbtable_overspeed = "";
      $report            = "historikal_mdvranomali_";

      switch ($m1)
      {
        case "January":
              $dbtable           = $report."januari_".$year;
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

      $user_level      = $this->sess->user_level;
      $user_parent     = $this->sess->user_parent;
      $user_company    = $this->sess->user_company;
      $user_subcompany = $this->sess->user_subcompany;
      $user_group      = $this->sess->user_group;
      $user_subgroup   = $this->sess->user_subgroup;
      $user_dblive 	   = $this->sess->user_dblive;
      $privilegecode 	 = $this->sess->user_id_role;
      $user_id_fix     = $this->sess->user_id;

      if($privilegecode == 1){
        $contractor = $contractor;
      }else if($privilegecode == 2){
        $contractor = $contractor;
      }else if($privilegecode == 3){
        $contractor = $contractor;
      }else if($privilegecode == 4){
        $contractor = $contractor;
      }else if($privilegecode == 5){
        $contractor = $user_company;
      }else if($privilegecode == 6){
        $contractor = $user_company;
      }else if($privilegecode == 0){
        $contractor = $contractor;
      }else{
        $contractor = $contractor;
      }

      // echo "<pre>";
      // var_dump($dbtable.'-'. $sdate.'-'. $edate.'-'. $contractor.'-'. $vehicle.'-'. $violationmasterselect);die();
      // echo "<pre>";

      $get_mdvr_anomali = $this->m_development->data_mdvr_anomali($dbtable, $sdate, $edate, $contractor, $vehicle, $violationmasterselect);
      $data_array_alert = array();
      if (sizeof($get_mdvr_anomali) > 0) {
          for ($j=0; $j < sizeof($get_mdvr_anomali); $j++) {
            if ($get_mdvr_anomali[$j]['mdvr_anomali_json'] != "") {
              $json_fatigue            = json_decode($get_mdvr_anomali[$j]['mdvr_anomali_json']);
              $forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
              $forcheck_gps_time       = $json_fatigue[0]->gps_time;
              $checkthis               = $this->m_development->getfrommaster($forcheck_vehicledevice);
              $jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
              // $jalurname            = $jsonautocheck->auto_last_road;
              $jalurname               = $get_mdvr_anomali[$j]['mdvr_anomali_jalur'];
              $positionforfilter       = $get_mdvr_anomali[$j]['mdvr_anomali_position'];

                if ($positionforfilter != "") {
                  $alarmreportnamefix = $json_fatigue[0]->gps_alert;

                    // echo "<pre>";
                    // var_dump($checkthis);die();
                    // echo "<pre>";

                    array_push($data_array_alert, array(
                       "isfatigue"            => "yes",
                       "jalur_name"           => $jalurname,
                       "vehicle_no"           => $json_fatigue[0]->vehicle_no,
                       "vehicle_name"         => $json_fatigue[0]->vehicle_name,
                       "vehicle_company"      => $json_fatigue[0]->vehicle_company,
                       "vehicle_company_name" => $json_fatigue[0]->vehicle_company_name,
                       "vehicle_device"       => $json_fatigue[0]->vehicle_device,
                       "vehicle_mv03"         => $json_fatigue[0]->vehicle_mv03,
                       "gps_alert"            => $alarmreportnamefix,
                       "violation_type" 		  => "not_overspeed",
                       "datetime"             => $json_fatigue[0]->gps_time,
                       "gps_latitude_real"    => $json_fatigue[0]->gps_latitude_real,
                       "gps_longitude_real"   => $json_fatigue[0]->gps_longitude_real,
                       "position"             => $json_fatigue[0]->position,
                       "gps_speed"            => $json_fatigue[0]->gps_speed,
                    ));
                }
          }
        }
      }
    // }

    // $params['data']         = $data_array_alert;

    // echo "<pre>";
    // var_dump($data_array_alert);die();
    // echo "<pre>";


          echo json_encode(array(
            "msg"                         => "success",
            "code"                        => 200,
            "showper"                     => $limitnya,
            "simultantype"                => 1,
            "violationmix"                => $data_array_alert,
          ));
    }

    // HISTORIKAL ANOMALI MDVR START
    function mdvranomali()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode              = $this->sess->user_id_role;
        $rows                       = $this->m_development->getdevice();
        $rows_company               = $this->get_company();
        $alarm_mdvranomali          = $this->m_development->get_alarm_mdvranomali();
        $alarm_mdvranomali_nonadmin = $this->config->item("mdvr_anomali_alarm");


        // echo "<pre>";
        // var_dump($alarm_mdvranomali);die();
        // echo "<pre>";

        $this->params["vehicles"]          = $rows;
        $this->params["rcompany"]          = $rows_company;
        $this->params["mdvr_anomali_list"] = $alarm_mdvranomali;
        $this->params["non_admin"]         = $alarm_mdvranomali_nonadmin;
        $this->params['code_view_menu']    = "report";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvranomali', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvranomali', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvranomali', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvranomali', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvranomali', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/report/mdvranomali/v_home_mdvranomali', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search_mdvr_anomali()
      {
      ini_set('display_errors', 1);
      $company      = $this->input->post("company");
      $vehicle      = $this->input->post("vehicle");
      $anomali_type = $this->input->post("anomali_type");
      $startdate    = $this->input->post("startdate").' '.$this->input->post("shour");
      $enddate      = $this->input->post("enddate").' '.$this->input->post("ehour");
      $periode      = $this->input->post("periode");

      $sdate2 = date("Y-m-d H:i:s", strtotime($startdate));
      $edate2 = date("Y-m-d H:i:s", strtotime($startdate));

      $nowdate   = date("Y-m-d");
      $nowday    = date("d");
      $nowmonth  = date("m");
      $nowyear   = date("Y");
      $lastday   = date("t");

        if($periode == "custom"){
          $sdate = date("Y-m-d H:i:s", strtotime($startdate));
          $edate = date("Y-m-d H:i:s", strtotime($enddate));
        }else if($periode == "yesterday"){
          $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
          $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }else if($periode == "last7"){
          $sdate = date("Y-m-d 00:00:00", strtotime($sdate2 . "-7days"));
          $edate = date("Y-m-d 23:59:59", strtotime($startdate));
        }else if($periode == "last30"){
          $sdate = date("Y-m-01 00:00:00", strtotime($startdate));
          $edate = date("Y-m-d 23:59:59", strtotime($startdate));
        }else if($periode == "today"){
          $sdate1 = date("Y-m-d");
          $sdate2 = "00:00:00";

          $edate1 = date("Y-m-d");
          $edate2 = "23:59:59";

          $sdate = $sdate1.' '.$sdate2;
          $edate = $edate1.' '.$edate2;
        }else{
          $sdate = date("Y-m-d H:i:s", strtotime($startdate));
          $edate = date("Y-m-d H:i:s", strtotime($enddate));
        }

        // echo "<pre>";
        // var_dump($sdate.'-'.$edate.'-'.$anomali_type);die();
        // echo "<pre>";

        // CHOOSE DBTABLE
        $m1        = date("F", strtotime($sdate));
        $year      = date("Y", strtotime($sdate));
        $dbtable   = "";
        $report    = "historikal_mdvranomali_";

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

      // if ($company == "" || $company == 0)
      // {
      // 	$error = "- Invalid Contractor. Silahkan Pilih salah satu Contractor! \n";
      // 	$callback['error'] = true;
      // 	$callback['message'] = $error;
      //
      // 	echo json_encode($callback);
      // 	return;
      // }else{
        $get_mdvr_anomali = $this->m_development->data_mdvr_anomali($dbtable, $sdate, $edate, $company, $vehicle, $anomali_type);
        $data_array_alert = array();
        if (sizeof($get_mdvr_anomali) > 0) {
            for ($j=0; $j < sizeof($get_mdvr_anomali); $j++) {
              if ($get_mdvr_anomali[$j]['mdvr_anomali_json'] != "") {
                $json_fatigue            = json_decode($get_mdvr_anomali[$j]['mdvr_anomali_json']);
                $forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
                $forcheck_gps_time       = $json_fatigue[0]->gps_time;
                $checkthis               = $this->m_development->getfrommaster($forcheck_vehicledevice);
                $jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
                // $jalurname            = $jsonautocheck->auto_last_road;
                $jalurname               = $get_mdvr_anomali[$j]['mdvr_anomali_jalur'];
                $positionforfilter       = $get_mdvr_anomali[$j]['mdvr_anomali_position'];

                  if ($positionforfilter != "") {
                    $alarmreportnamefix = $json_fatigue[0]->gps_alert;

                      // echo "<pre>";
                      // var_dump($checkthis);die();
                      // echo "<pre>";

                      array_push($data_array_alert, array(
                         "isfatigue"            => "yes",
                         "jalur_name"           => $jalurname,
                         "vehicle_no"           => $json_fatigue[0]->vehicle_no,
                         "vehicle_name"         => $json_fatigue[0]->vehicle_name,
                         "vehicle_company"      => $json_fatigue[0]->vehicle_company,
                         "vehicle_company_name" => $json_fatigue[0]->vehicle_company_name,
                         "vehicle_device"       => $json_fatigue[0]->vehicle_device,
                         "vehicle_mv03"         => $json_fatigue[0]->vehicle_mv03,
                         "gps_alert"            => $alarmreportnamefix,
                         "violation_type" 		  => "not_overspeed",
                         "datetime"             => $json_fatigue[0]->gps_time,
                         "gps_latitude_real"    => $json_fatigue[0]->gps_latitude_real,
                         "gps_longitude_real"   => $json_fatigue[0]->gps_longitude_real,
                         "position"             => $json_fatigue[0]->position,
                         "gps_speed"            => $json_fatigue[0]->gps_speed,
                      ));
                  }
            }
          }
        }
      // }

      // echo "<pre>";
      // var_dump($data_array_alert);die();
      // echo "<pre>";

      $params['data']         = $data_array_alert;
      $params['sdate']        = $sdate;
      $params['edate']        = $edate;
      $html = $this->load->view("newdashboard/development/report/mdvranomali/v_mdvranomali_result", $params, true);

      $callback['error'] = false;
      $callback['html'] = $html;
      echo json_encode($callback);
    }
    // HISTORIKAL ANOMALI MDVR END

function otherport(){
  ini_set('display_errors', 1);

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
  $user_id_fix     = 4408;

  $this->db->select("vehicle.*, user_name");
  $this->db->order_by("vehicle_no", "asc");
  $this->db->where("vehicle_status <>", 3);

  if ($privilegecode == 0) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  } else if ($privilegecode == 1) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 2) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 3) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 4) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 5) {
    $this->db->where("vehicle_company", $user_company);
  } else if ($privilegecode == 6) {
    $this->db->where("vehicle_company", $user_company);
  } else if ($privilegecode == 7) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  } else if ($privilegecode == 8) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  }else if ($privilegecode == 11) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  }else {
    $this->db->where("vehicle_no", 99999);
  }

  $this->db->join("user", "vehicle_user_id = user_id", "left outer");
  $q = $this->db->get("vehicle");

  if ($q->num_rows() == 0) {
    redirect(base_url());
  }

  $rows          = $q->result_array();
  $rows_company  = $this->get_company_bylevel();

  $dataClient    = $this->m_development->allDataClient();
  $dataMaterial  = $this->m_development->allDataMaterial();
  $streetRom     = $this->m_development->getstreet_now(3);
  $streetPort    = $this->m_development->getstreet_now(4);
  $alldriveritws = $this->m_development->alldriveritws();

  $data_rom = array();
  for ($i=0; $i < sizeof($streetRom); $i++) {
    if ($streetRom[$i]['street_type'] == 3) {
      array_push($data_rom, array(
        "street_id"   => $streetRom[$i]['street_id'],
        "street_name" => str_replace(",", "", $streetRom[$i]['street_name']),
      ));
    }
  }

  $data_port = array();
  for ($i=0; $i < sizeof($streetPort); $i++) {
    if ($streetPort[$i]['street_type'] == 4) {
      array_push($data_port, array(
        "street_id"   => $streetPort[$i]['street_id'],
        "street_name" => str_replace(",", "", $streetPort[$i]['street_name']),
      ));
    }
  }

  // echo "<pre>";
  // var_dump($data_port);die();
  // echo "<pre>";

  $this->params["vehicles"]        = $rows;
  $this->params["rcompany"]        = $rows_company;
  $this->params["data_client"]     = $dataClient;
  $this->params["data_material"]   = $dataMaterial;
  $this->params["data_rom"]			   = $data_rom;
  $this->params["data_port"]			 = $data_port;
  $this->params["datadriveritws"]	 = $alldriveritws;

  //$this->params["data"] 		    = $result;
  $this->params['code_view_menu'] = "monitoring";

  $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  if ($privilegecode == 11 || $user_id == 5024) {
    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
    $this->params["content"]        = $this->load->view('newdashboard/development/wim/v_home_otherport', $this->params, true);
    $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
  }else {
    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('newdashboard/development/wim/v_home_otherport', $this->params, true);
    $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }
}

function fuelreport()
{
    if (!isset($this->sess->user_type)) {
        redirect('dashboard');
    }

    $privilegecode   = $this->sess->user_id_role;

    // echo "<pre>";
    // var_dump("masuk");die();
    // echo "<pre>";

    $rows                           = $this->get_vehicle_pjo();

    $rows_company                   = $this->get_company();
    $this->params["vehicles"]       = $rows;
    $this->params["rcompany"]       = $rows_company;
    $this->params['code_view_menu'] = "report";

    $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/v_dev_fuelreport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    } elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/v_dev_fuelreport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    } elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/v_dev_fuelreport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    } elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/v_dev_fuelreport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    } elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/v_dev_fuelreport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    } else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/v_dev_fuelreport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    }
}

function get_vehicle_pjo()
{
    $user_company    = $this->sess->user_company;
    $user_parent     = $this->sess->user_parent;
    $privilegecode   = $this->sess->user_id_role;
    $user_id         = $this->sess->user_id;
    $user_id_fix     = "";

    if ($user_id == "1445") {
        $user_id_fix = $user_id;
    } else {
        $user_id_fix = $this->sess->user_id;
    }

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no", "asc");

    if ($privilegecode == 0) {
        $this->db->where("vehicle_user_id", $user_id_fix);
    } else if ($privilegecode == 1) {
        $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 2) {
        $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 3) {
        $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 4) {
        $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 5) {
        $this->db->where("vehicle_company", $user_company);
    } else if ($privilegecode == 6) {
        $this->db->where("vehicle_company", $user_company);
    } else {
        $this->db->where("vehicle_no", 99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_typeunit", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
}

function get_company()
{
    if (!isset($this->sess->user_type)) {
        redirect(base_url());
    }

    $privilegecode = $this->sess->user_id_role;


    $this->db->order_by("company_name", "asc");
    if ($privilegecode == 0) {
        $this->db->where("company_created_by", $this->sess->user_id);
    } elseif ($privilegecode == 1) {
        $this->db->where("company_created_by", $this->sess->user_parent);
    } elseif ($privilegecode == 2) {
        $this->db->where("company_created_by", $this->sess->user_parent);
    } elseif ($privilegecode == 3) {
        $this->db->where("company_created_by", $this->sess->user_parent);
    } elseif ($privilegecode == 4) {
        $this->db->where("company_created_by", $this->sess->user_parent);
    } elseif ($privilegecode == 5) {
        $this->db->where("company_id", $this->sess->user_company);
    } elseif ($privilegecode == 6) {
        $this->db->where("company_id", $this->sess->user_company);
    }

    $this->db->where("company_flag", 0);
    $qd = $this->db->get("company");
    $rd = $qd->result();

    return $rd;
}

function fuelreport_search()
{
    $company            = $this->input->post('company');
    $vehicle            = $this->input->post('vehicle');
    $datein             = $this->input->post('date');
    $interval           = $this->input->post('interval');
    $potensiallossvalue = $this->input->post('potensiallossvalue');
    $date               = date('Y-m-d', strtotime($datein));
    $shift              = $this->input->post('shift');
    $month              = date("F", strtotime($date));
    $year               = date("Y", strtotime($date));
    $report_location    = "location_";
    $datatablemode      = array();

    if (date("Y-m-d") < $date) {
        echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "total" => 0, "data" => array()));
        exit();
    }
    $lastdate = date("Y-m-t", strtotime($datein));
    $monthn   = date("m", strtotime($datein));
    $day      = date('d', strtotime($datein));

    $day++;
    $jmlday = strlen($day);
    if ($jmlday == 1) {
        $day = "0" . $day;
    }
    $next = $year . "-" . $monthn . "-" . $day;

    if ($next > $lastdate) {
        if ($monthn == 12) {
            $y = $year + 1;
            $next = $y . "-01-01";
        } else {
            $m = $monthn + 1;
            $jmlmonth = strlen($m);
            if ($jmlmonth == 1) {
                $m = "0" . $m;
            }
            $next = $year . "-" . $m . "-01";
        }
    }

    $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

    switch ($month) {
        case "January":
            $dbtable_location = $report_location . "januari_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "februari_" . $year;
            }
            break;
        case "February":
            $dbtable_location = $report_location . "februari_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "maret_" . $year;
            }
            break;
        case "March":
            $dbtable_location = $report_location . "maret_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "april_" . $year;
            }
            break;
        case "April":
            $dbtable_location = $report_location . "april_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "mei_" . $year;
            }
            break;
        case "May":
            $dbtable_location = $report_location . "mei_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "juni_" . $year;
            }
            break;
        case "June":
            $dbtable_location = $report_location . "juni_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "juli_" . $year;
            }
            break;
        case "July":
            $dbtable_location = $report_location . "juli_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "agustus_" . $year;
            }
            break;
        case "August":
            $dbtable_location = $report_location . "agustus_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "september_" . $year;
            }
            break;
        case "September":
            $dbtable_location = $report_location . "september_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "oktober_" . $year;
            }
            break;
        case "October":
            $dbtable_location = $report_location . "oktober_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "november_" . $year;
            }
            break;
        case "November":
            $dbtable_location = $report_location . "november_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "desember_" . $year;
            }
            break;
        case "December":
            $dbtable_location = $report_location . "desember_" . $year;
            if ($date == $lastdate) {
                $year++;
                $dbtable_location2 = $report_location . "januari_" . $year;
            }
            break;
    }

    $input = array(
        "location_report_vehicle_company" => $company,
        "location_report_vehicle_device"  => $vehicle,
        "date"                            => $arraydate,
        "table_name"                      => $dbtable_location
    );
    if (isset($dbtable_location2)) {
        $input["table_name_2"] = $dbtable_location2;
    }


    $data_fix = array();

    if ($shift == 0) {
        if ($date == $lastdate) {
            //beda tabel
            $startdateallshift = $date . " 06:00:00";
            $enddateallshift = $date . " 23:59:59";
            $rows_loc1 = $this->getfuelQuery($vehicle, $startdateallshift, $enddateallshift, $dbtable_location);

            $startdateallshift = $next . " 00:00:00";
            $enddateallshift = $next . " 05:59:59";
            $rows_loc2 = $this->getfuelQuery($vehicle, $startdateallshift, $enddateallshift, $dbtable_location2);
            $rows_loc = array_merge($rows_loc1, $rows_loc2);
        } else {
            $startdateallshift = $date . " 06:00:00";
            $enddateallshift = $next . " 05:59:59";
            $rows_loc = $this->getfuelQuery($vehicle, $startdateallshift, $enddateallshift, $dbtable_location);
        }
    } else if ($shift == 1) {
        $startdateshift1 = $date . " 06:00:00";
        $enddateshift1 = $date . " 17:59:59";
        $rows_loc = $this->getfuelQuery($vehicle, $startdateshift1, $enddateshift1, $dbtable_location);
    } else {
        if ($date == $lastdate) {
            //beda tabel
            $startdateshift2 = $date . " 18:00:00";
            $enddateshift2 = $date . " 23:59:59";
            $rows_loc1 = $this->getfuelQuery($vehicle, $startdateshift2, $enddateshift2, $dbtable_location);

            $startdateshift2 = $next . " 00:00:00";
            $enddateshift2 = $next . " 05:59:59";
            $rows_loc2 = $this->getfuelQuery($vehicle, $startdateshift2, $enddateshift2, $dbtable_location2);
            $rows_loc = array_merge($rows_loc1, $rows_loc2);
        } else {
            $startdateshift2 = $date . " 18:00:00";
            $enddateshift2 = $next . " 05:59:59";
            $rows_loc = $this->getfuelQuery($vehicle, $startdateshift2, $enddateshift2, $dbtable_location);
        }
    }

    $total_loc = count($rows_loc);

    if ($total_loc < 1) {

        $callback["error"]      = true;
        $callback["message"] = "Data empty.";

        echo json_encode($callback);
    } else {
        if ($interval == 1) {
            //interval 1 jam
            $lastdata = 0;
            for ($x = 0; $x < $total_loc; $x++) {
                $nosort = $x + 1;

                //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
                if ($nosort == $total_loc) {
                    //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
                } else {
                    //$hour_only = date("H", strtotime($rows_loc[$x]->gps_time));
                    $hour_only = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                    if (!isset($data_fix[$hour_only])) {
                        //data dari gps time, jam pertama
                        $data_fix[$hour_only] = $rows_loc[$x]->gps_mvd;
                        $prev = $hour_only - 1;
                        if ($x == 0) {
                            //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                            $lastdata = $data_fix[$hour_only];
                        } else {
                            if ($prev < 10) {
                                $dt = (string)$prev;
                                $prev = "0" . $dt;
                            } else {
                                $prev = (string)$prev;
                            }
                            if ($prev == (-1)) {
                                $prev = "23";
                            }
                            // $test[] = $data_fix[$prev] - $data_fix[$hour_only];


                            if (isset($data_fix[$prev])) {
                                //jika sebelumnya ada data bandingkan.
                                $delta_cons = $data_fix[$hour_only] - $data_fix[$prev];
                                if ($delta_cons > 15) {
                                    //asumsi isi bbm selalu lebih dari 15 liter
                                } else {
                                    if (($delta_cons > 0) && ($delta_cons < 15)) {
                                        //asumsi data invalid
                                        //maka data disamakan dengan data jam sebelumnya
                                        $data_fix[$hour_only] = $data_fix[$prev];
                                    }
                                }
                            } else {
                                //jika sebelumnya tidak ada data, bandingkan dengan data terakhir
                                $delta_cons = $data_fix[$hour_only] - $lastdata;
                                if ($delta_cons > 15) {
                                    //asumsi isi bbm selalu lebih dari 15 liter
                                } else {
                                    if (($delta_cons > 0) && ($delta_cons < 15)) {
                                        //asumsi data invalid, maka data disamakan dengan data terakhir
                                        $data_fix[$hour_only] = $lastdata;
                                    }
                                }
                            }
                        }
                        //simpan data terakhir
                        $lastdata = $data_fix[$hour_only];
                    }
                }
            }
        }elseif ($interval == 3) {
          $lastdata = 0;
          $i = 0;
          for ($x = 0; $x < $total_loc; $x++) {
              $nosort = $x + 1;

              //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
              if ($nosort == $total_loc) {
                  //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
              } else {
                  //$hour = date("H", strtotime($rows_loc[$x]->gps_time));
                  //$minute = date("i", strtotime($rows_loc[$x]->gps_time));
                  $position = "";
                  if ($x < 1) {
                    $gpsmvdbefore = round($rows_loc[$x]->gps_mvd,0);
                    $gpsmvdcurrent = round($rows_loc[$x]->gps_mvd,0);
                  }else {
                    $gpsmvdbefore = round($rows_loc[$x-1]->gps_mvd,0);
                    $gpsmvdcurrent = round($rows_loc[$x]->gps_mvd,0);
                  }

                  $hour   = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                  $minute = date("i", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                  $second = date("s", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                  //data dari gps time, jam pertama
                  $data_fix[$i] =  array(
                      "hour"   => $hour,
                      "minute" => $minute,
                      "second" => $second,
                      "fuel"   => round($rows_loc[$x]->gps_mvd,0)
                  );
                  $prev = $i - 1;
                  if ($prev < 0) {
                      //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                      $lastdata = $data_fix[$i];
                      $i++;
                  } else {
                      $i++;
                  }
              }
          }
        }elseif ($interval == 4) {
          // echo "<pre>";
          // var_dump("Potensial Loss masuk");die();
          // echo "<pre>";

          $lastdata = 0;
          $i = 0;
          for ($x = 0; $x < $total_loc; $x++) {
              $nosort = $x + 1;

              //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
              if ($nosort == $total_loc) {
                  //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
              } else {
                  //$hour = date("H", strtotime($rows_loc[$x]->gps_time));
                  //$minute = date("i", strtotime($rows_loc[$x]->gps_time));
                  $position = "";
                  if ($x < 1) {
                    $gpsmvdbefore = round($rows_loc[$x]->gps_mvd,0);
                    $gpsmvdcurrent = round($rows_loc[$x]->gps_mvd,0);
                  }else {
                    $gpsmvdbefore = round($rows_loc[$x-1]->gps_mvd,0);
                    $gpsmvdcurrent = round($rows_loc[$x]->gps_mvd,0);
                  }

                  $hour   = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                  $minute = date("i", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                  $second = date("s", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                  //data dari gps time, jam pertama
                  $data_fix[$i] =  array(
                      "hour"   => $hour,
                      "minute" => $minute,
                      "second" => $second,
                      "fuel"   => round($rows_loc[$x]->gps_mvd,0)
                  );
                  $prev = $i - 1;
                  if ($prev < 0) {
                      //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                      $lastdata = $data_fix[$i];
                      $i++;
                  } else {
                      $i++;
                  }
              }
          }
          $callback["potensiallossvalue"]   = $potensiallossvalue;
        } else {
            //interval 30 menit
            $lastdata = 0;
            $i = 0;
            for ($x = 0; $x < $total_loc; $x++) {
                $nosort = $x + 1;

                //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
                if ($nosort == $total_loc) {
                    //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
                } else {
                    //$hour = date("H", strtotime($rows_loc[$x]->gps_time));
                    //$minute = date("i", strtotime($rows_loc[$x]->gps_time));

                    $hour = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                    $minute = date("i", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                    //data dari gps time, jam pertama
                    $data_fix[$i] =  array(
                        "hour" => $hour,
                        "minute" => $minute,
                        "fuel" => round($rows_loc[$x]->gps_mvd,0)
                    );
                    $prev = $i - 1;
                    if ($prev < 0) {
                        //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                        $lastdata = $data_fix[$i];
                        $i++;
                    } else {
                        $i++;
                    }
                }
            }
        }

        // echo "<pre>";
        // var_dump($datatablemode);die();
        // echo "<pre>";
        $callback["input"]           = $input;
        $callback["total data"]      = $total_loc;
        $callback["data"]            = $data_fix;
        $callback["data dari table"] = $rows_loc;
        $callback["datatablemode"]   = $datatablemode;
        echo json_encode($callback);
    }
}

function fuelreport_search_tablemode()
{
    $company         = $this->input->post('company');
    $vehicle         = $this->input->post('vehicle');
    $datein          = $this->input->post('date');
    $interval        = $this->input->post('interval');
    $date            = date('Y-m-d', strtotime($datein));
    $shift           = $this->input->post('shift');
    $month           = date("F", strtotime($date));
    $year            = date("Y", strtotime($date));
    $report_location = "location_";
    $datatablemode   = array();

    if (date("Y-m-d") < $date) {
        echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "total" => 0, "data" => array()));
        exit();
    }
    $lastdate = date("Y-m-t", strtotime($datein));
    $monthn   = date("m", strtotime($datein));
    $day      = date('d', strtotime($datein));

    $day++;
    $jmlday = strlen($day);
    if ($jmlday == 1) {
        $day = "0" . $day;
    }
    $next = $year . "-" . $monthn . "-" . $day;

    if ($next > $lastdate) {
        if ($monthn == 12) {
            $y = $year + 1;
            $next = $y . "-01-01";
        } else {
            $m = $monthn + 1;
            $jmlmonth = strlen($m);
            if ($jmlmonth == 1) {
                $m = "0" . $m;
            }
            $next = $year . "-" . $m . "-01";
        }
    }

    $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

    switch ($month) {
        case "January":
            $dbtable_location = $report_location . "januari_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "februari_" . $year;
            }
            break;
        case "February":
            $dbtable_location = $report_location . "februari_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "maret_" . $year;
            }
            break;
        case "March":
            $dbtable_location = $report_location . "maret_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "april_" . $year;
            }
            break;
        case "April":
            $dbtable_location = $report_location . "april_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "mei_" . $year;
            }
            break;
        case "May":
            $dbtable_location = $report_location . "mei_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "juni_" . $year;
            }
            break;
        case "June":
            $dbtable_location = $report_location . "juni_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "juli_" . $year;
            }
            break;
        case "July":
            $dbtable_location = $report_location . "juli_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "agustus_" . $year;
            }
            break;
        case "August":
            $dbtable_location = $report_location . "agustus_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "september_" . $year;
            }
            break;
        case "September":
            $dbtable_location = $report_location . "september_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "oktober_" . $year;
            }
            break;
        case "October":
            $dbtable_location = $report_location . "oktober_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "november_" . $year;
            }
            break;
        case "November":
            $dbtable_location = $report_location . "november_" . $year;
            if ($date == $lastdate) {
                $dbtable_location2 = $report_location . "desember_" . $year;
            }
            break;
        case "December":
            $dbtable_location = $report_location . "desember_" . $year;
            if ($date == $lastdate) {
                $year++;
                $dbtable_location2 = $report_location . "januari_" . $year;
            }
            break;
    }

    $input = array(
        "location_report_vehicle_company" => $company,
        "location_report_vehicle_device"  => $vehicle,
        "date"                            => $arraydate,
        "table_name"                      => $dbtable_location
    );
    if (isset($dbtable_location2)) {
        $input["table_name_2"] = $dbtable_location2;
    }


    $data_fix = array();

    if ($shift == 0) {
        if ($date == $lastdate) {
            //beda tabel
            $startdateallshift = $date . " 06:00:00";
            $enddateallshift = $date . " 23:59:59";
            $rows_loc1 = $this->getfuelQuery($vehicle, $startdateallshift, $enddateallshift, $dbtable_location);

            $startdateallshift = $next . " 00:00:00";
            $enddateallshift = $next . " 05:59:59";
            $rows_loc2 = $this->getfuelQuery($vehicle, $startdateallshift, $enddateallshift, $dbtable_location2);
            $rows_loc = array_merge($rows_loc1, $rows_loc2);
        } else {
            $startdateallshift = $date . " 06:00:00";
            $enddateallshift = $next . " 05:59:59";
            $rows_loc = $this->getfuelQuery($vehicle, $startdateallshift, $enddateallshift, $dbtable_location);
        }
    } else if ($shift == 1) {
        $startdateshift1 = $date . " 06:00:00";
        $enddateshift1 = $date . " 17:59:59";
        $rows_loc = $this->getfuelQuery($vehicle, $startdateshift1, $enddateshift1, $dbtable_location);
    } else {
        if ($date == $lastdate) {
            //beda tabel
            $startdateshift2 = $date . " 18:00:00";
            $enddateshift2 = $date . " 23:59:59";
            $rows_loc1 = $this->getfuelQuery($vehicle, $startdateshift2, $enddateshift2, $dbtable_location);

            $startdateshift2 = $next . " 00:00:00";
            $enddateshift2 = $next . " 05:59:59";
            $rows_loc2 = $this->getfuelQuery($vehicle, $startdateshift2, $enddateshift2, $dbtable_location2);
            $rows_loc = array_merge($rows_loc1, $rows_loc2);
        } else {
            $startdateshift2 = $date . " 18:00:00";
            $enddateshift2 = $next . " 05:59:59";
            $rows_loc = $this->getfuelQuery($vehicle, $startdateshift2, $enddateshift2, $dbtable_location);
        }
    }

    $total_loc = count($rows_loc);

    if ($total_loc < 1) {

        $callback["error"]      = true;
        $callback["message"] = "Data empty.";

        echo json_encode($callback);
    } else {
        if ($interval == 1) {
            //interval 1 jam
            $lastdata = 0;
            for ($x = 0; $x < $total_loc; $x++) {
                $nosort = $x + 1;

                //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
                if ($nosort == $total_loc) {
                    //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
                } else {
                    //$hour_only = date("H", strtotime($rows_loc[$x]->gps_time));
                    $hour_only = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                    if (!isset($data_fix[$hour_only])) {
                        //data dari gps time, jam pertama
                        $data_fix[$hour_only] = $rows_loc[$x]->gps_mvd;
                        $prev = $hour_only - 1;
                        if ($x == 0) {
                            //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                            $lastdata = $data_fix[$hour_only];
                        } else {
                            if ($prev < 10) {
                                $dt = (string)$prev;
                                $prev = "0" . $dt;
                            } else {
                                $prev = (string)$prev;
                            }
                            if ($prev == (-1)) {
                                $prev = "23";
                            }
                            // $test[] = $data_fix[$prev] - $data_fix[$hour_only];


                            if (isset($data_fix[$prev])) {
                                //jika sebelumnya ada data bandingkan.
                                $delta_cons = $data_fix[$hour_only] - $data_fix[$prev];
                                if ($delta_cons > 15) {
                                    //asumsi isi bbm selalu lebih dari 15 liter
                                } else {
                                    if (($delta_cons > 0) && ($delta_cons < 15)) {
                                        //asumsi data invalid
                                        //maka data disamakan dengan data jam sebelumnya
                                        $data_fix[$hour_only] = $data_fix[$prev];
                                    }
                                }
                            } else {
                                //jika sebelumnya tidak ada data, bandingkan dengan data terakhir
                                $delta_cons = $data_fix[$hour_only] - $lastdata;
                                if ($delta_cons > 15) {
                                    //asumsi isi bbm selalu lebih dari 15 liter
                                } else {
                                    if (($delta_cons > 0) && ($delta_cons < 15)) {
                                        //asumsi data invalid, maka data disamakan dengan data terakhir
                                        $data_fix[$hour_only] = $lastdata;
                                    }
                                }
                            }
                        }
                        //simpan data terakhir
                        $lastdata = $data_fix[$hour_only];
                    }
                }
            }
        }elseif ($interval == 3) {
          $lastdata = 0;
          $i = 0;
          for ($x = 0; $x < $total_loc; $x++) {
              $nosort = $x + 1;

              //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
              if ($nosort == $total_loc) {
                  //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
              } else {
                  //$hour = date("H", strtotime($rows_loc[$x]->gps_time));
                  //$minute = date("i", strtotime($rows_loc[$x]->gps_time));
                  $position = "";
                  if ($x < 1) {
                    $gpsmvdbefore = round($rows_loc[$x]->gps_mvd,0);
                    $gpsmvdcurrent = round($rows_loc[$x]->gps_mvd,0);
                  }else {
                    $gpsmvdbefore = round($rows_loc[$x-1]->gps_mvd,0);
                    $gpsmvdcurrent = round($rows_loc[$x]->gps_mvd,0);
                  }

                    if ($gpsmvdcurrent != $gpsmvdbefore) {
                      $positionalert     = $this->gpsmodel->GeoReverse($rows_loc[$x]->gps_latitude_real, $rows_loc[$x]->gps_longitude_real);
                      if ($positionalert->display_name != "Unknown Location!") {
                        $positionexplode = explode(",", $positionalert->display_name);
                        $position = $positionexplode[0];
                      }else {
                        $position = $positionalert->display_name;
                      }
                    }

                  array_push($datatablemode, array(
                    "gps_mvd"            => round($rows_loc[$x]->gps_mvd,0),
                    "gps_date"           => date("d-m-Y", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))),
                    "gps_time"           => date("H:i:s", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))),
                    "gps_latitude_real"  => $rows_loc[$x]->gps_latitude_real,
                    "gps_longitude_real" => $rows_loc[$x]->gps_longitude_real,
                    "position"           => $position
                  ));

                  // echo "<pre>";
                  // var_dump("Position : ".$position);die();
                  // echo "<pre>";

                  $hour   = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                  $minute = date("i", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                  $second = date("s", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                  //data dari gps time, jam pertama
                  $data_fix[$i] =  array(
                      "hour"   => $hour,
                      "minute" => $minute,
                      "second" => $second,
                      "fuel"   => round($rows_loc[$x]->gps_mvd,0)
                  );
                  $prev = $i - 1;
                  if ($prev < 0) {
                      //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                      $lastdata = $data_fix[$i];
                      $i++;
                  } else {
                      $i++;
                  }
              }
          }
        } else {
            //interval 30 menit
            $lastdata = 0;
            $i = 0;
            for ($x = 0; $x < $total_loc; $x++) {
                $nosort = $x + 1;

                //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
                if ($nosort == $total_loc) {
                    //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
                } else {
                    //$hour = date("H", strtotime($rows_loc[$x]->gps_time));
                    //$minute = date("i", strtotime($rows_loc[$x]->gps_time));

                    $hour = date("H", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita
                    $minute = date("i", strtotime("+7 hour", strtotime($rows_loc[$x]->gps_time))); //wita

                    //data dari gps time, jam pertama
                    $data_fix[$i] =  array(
                        "hour" => $hour,
                        "minute" => $minute,
                        "fuel" => round($rows_loc[$x]->gps_mvd,0)
                    );
                    $prev = $i - 1;
                    if ($prev < 0) {
                        //lewatkan pengecekan di data pertama dan simpan juga ke data terakhir
                        $lastdata = $data_fix[$i];
                        $i++;
                    } else {
                        $i++;
                    }
                }
            }
        }

        // echo "<pre>";
        // var_dump($datatablemode);die();
        // echo "<pre>";
        $params["data"]              = $datatablemode;
        $html                        = $this->load->view("newdashboard/development/v_dev_fuelreportresult", $params, true);

    		$callback['error']           = false;
    		$callback['html']            = $html;
        $callback["input"]           = $input;
        $callback["total data"]      = $total_loc;
        $callback["data"]            = $data_fix;
        $callback["data dari table"] = $rows_loc;
        $callback["datatablemode"]   = $datatablemode;
        echo json_encode($callback);
    }
}

function getfuelQuery($vehicle, $startdate, $enddate, $dbtable_location)
{
    //print_r($startdate." ".$enddate." ");
    $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
    $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));  //wita
    //print_r($sdate." ".$edate);exit();
    $rowvehicle = $this->getvehicle($vehicle);

    //PORT Only
    if (isset($rowvehicle->vehicle_info)) {
        $json = json_decode($rowvehicle->vehicle_info);
        if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
            $databases = $this->config->item('databases');
            if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
                $database      = $databases[$json->vehicle_ip][$json->vehicle_port];
                $table         = $this->config->item("external_gpstable");
                $tableinfo     = $this->config->item("external_gpsinfotable");
                $this->dbhist  = $this->load->database($database, TRUE);
                $this->dbhist2 = $this->load->database("gpshistory", true);
            } else {
                $table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
                $tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
                $this->dbhist  = $this->load->database("default", TRUE);
                $this->dbhist2 = $this->load->database("gpshistory", true);
            }

            $vehicle_device = explode("@", $rowvehicle->vehicle_device);
            $vehicle_no     = $rowvehicle->vehicle_no;
            $vehicle_dev    = $rowvehicle->vehicle_device;
            $vehicle_name   = $rowvehicle->vehicle_name;
            $vehicle_type   = $rowvehicle->vehicle_type;

            if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
                $tablehist     = $vehicle_device[0] . "@t5_gps";
                $tablehistinfo = $vehicle_device[0] . "@t5_info";
            } else {
                $tablehist     = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
                $tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
            }


            $this->dbhist->select("gps_time,gps_mvd, gps_latitude_real, gps_longitude_real");
            $this->dbhist->where("gps_name", $vehicle_device[0]);
            $this->dbhist->where("gps_speed", 0);
            $this->dbhist->where("gps_time >=", $sdate);
            $this->dbhist->where("gps_time <=", $edate);
            $this->dbhist->where("gps_mvd >", 0);
            $this->dbhist->order_by("gps_time", "asc");
            $this->dbhist->group_by("gps_time");

            $this->dbhist->from($table);
            $q = $this->dbhist->get();
            $rows1 = $q->result();


            $this->dbhist2->select("gps_time,gps_mvd, gps_latitude_real, gps_longitude_real");
            $this->dbhist2->where("gps_name", $vehicle_device[0]);
            $this->dbhist2->where("gps_speed", 0);
            $this->dbhist2->where("gps_time >=", $sdate);
            $this->dbhist2->where("gps_time <=", $edate);
            $this->dbhist2->where("gps_mvd >", 0);
            $this->dbhist2->order_by("gps_time", "asc");
            $this->dbhist2->group_by("gps_time");

            $this->dbhist2->from($tablehist);
            $q2        = $this->dbhist2->get();
            $rows2     = $q2->result();

            $rows      = array_merge($rows1, $rows2);
            $trows     = count($rows);

            $totaldata = $trows;
            $data      = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);

            return $data;
            //print_r($data);exit();
        }
    }
}

function getvehicle($vehicle_device)
{

    $this->db = $this->load->database("default", true);
    $this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_company,vehicle_dbname_live,vehicle_info");
    $this->db->order_by("vehicle_id", "asc");
    $this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_device", $vehicle_device);
    $q = $this->db->get("vehicle");
    $rows = $q->row();
    $total_rows = count($rows);

    if ($total_rows > 0) {
        $data_vehicle = $rows;
        return $data_vehicle;
    } else {
        return false;
    }
}

function wim()
{
  ini_set('display_errors', 1);

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
  $user_id_fix     = 4408;

  $this->db->select("vehicle.*, user_name");
  $this->db->order_by("vehicle_no", "asc");
  $this->db->where("vehicle_status <>", 3);

  if ($privilegecode == 0) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  } else if ($privilegecode == 1) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 2) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 3) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 4) {
    $this->db->where("vehicle_user_id", $user_parent);
  } else if ($privilegecode == 5) {
    $this->db->where("vehicle_company", $user_company);
  } else if ($privilegecode == 6) {
    $this->db->where("vehicle_company", $user_company);
  } else if ($privilegecode == 7) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  } else if ($privilegecode == 8) {
    $this->db->where("vehicle_user_id", $user_id_fix);
  }else {
    $this->db->where("vehicle_no", 99999);
  }

  $this->db->join("user", "vehicle_user_id = user_id", "left outer");
  $q = $this->db->get("vehicle");

  if ($q->num_rows() == 0) {
    redirect(base_url());
  }

  $rows          = $q->result_array();
  $rows_company  = $this->get_company_bylevel();

  $dataClient    = $this->m_development->allDataClient();
  $dataMaterial  = $this->m_development->allDataMaterial();
  $streetRom     = $this->m_development->getstreet_now(3);
  $alldriveritws = $this->m_development->alldriveritws();

  $data_rom = array();
  for ($i=0; $i < sizeof($streetRom); $i++) {
    if ($streetRom[$i]['street_type'] == 3) {
      array_push($data_rom, array(
        "street_id"   => $streetRom[$i]['street_id'],
        "street_name" => str_replace(",", "", $streetRom[$i]['street_name']),
      ));
    }
  }

  // echo "<pre>";
  // var_dump($alldriveritws);die();
  // echo "<pre>";

  $this->params["vehicles"]        = $rows;
  $this->params["rcompany"]        = $rows_company;
  $this->params["data_client"]     = $dataClient;
  $this->params["data_material"]   = $dataMaterial;
  $this->params["data_rom"]			   = $data_rom;
  $this->params["datadriveritws"]	 = $alldriveritws;

  //$this->params["data"] 		    = $result;
  $this->params['code_view_menu'] = "wimmenu";

  $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  if ($privilegecode == 7) {
    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
    $this->params["content"]        = $this->load->view('newdashboard/development/wim/v_wim_list', $this->params, true);
    $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
  }elseif ($privilegecode == 8) {
    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
    $this->params["content"]        = $this->load->view('newdashboard/development/wim/v_wim_list', $this->params, true);
    $this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
  }else {
    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('newdashboard/development/wim/v_wim_list', $this->params, true);
    $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }
}

function search_report_otherport(){
	$company          = $this->input->post("company");
	$vehicle          = $this->input->post("vehicle");
	$startdate        = $this->input->post("startdate");
	$shour            = "00:00:00";
	$enddate          = $this->input->post("enddate");
	$ehour            = "23:59:59";
	$statuswim        = $this->input->post("statuswim");
	$modewim          = $this->input->post("modewim");
	$periode          = $this->input->post("periode");
	$first_load       = $this->input->post("first_load");
	$transactionid    = $this->input->post("transactionid_select");

	// KONDISI TRANSACTION ID  START
	if ($transactionid != "") {
		$findthissymbol = array(".","-","_","!");
		$symbolfounded  = (str_replace($findthissymbol, '', $transactionid) != $transactionid);
		if ($symbolfounded) {
			$transactionidfix    = 0;
			$callback['error']   = true;
			$callback['message'] = "Gunakan koma sebagai pembatas untuk mencari lebih dari satu Transaction ID";
			echo json_encode($callback);
			return;
		}else {
			$transactionidfix = $transactionid;
		}
	}else {
		$transactionidfix = 0;
	}

	// echo "<pre>";
	// var_dump($transactionidfix);die();
	// echo "<pre>";
	// KONDISI TRANSACTION ID END


	/* $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
	$edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1); */

	$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

	$nowdate   = date("Y-m-d");
	$nowday    = date("d");
	$nowmonth  = date("m");
	$nowyear   = date("Y");
	$lastday   = date("t");

	// if ($first_load == 1) {
		$sdate1 = date("Y-m-d");
		$sdate2 = "00:00:00";

		$edate1 = date("Y-m-d");
		$edate2 = "23:59:59";

		$sdate = $sdate1." ".$sdate2;
		$edate = $edate1." ".$edate2;
	// }else {
	// 	if($periode == "custom"){
	// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
	// 	}else if($periode == "yesterday"){
	// 		$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
	// 		$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
	// 	}else if($periode == "last7"){
	// 		/* $nowday = $nowday - 1;
	// 		$firstday = $nowday - 7;
	// 		if($nowday <= 7){
	// 			$firstday = 1;
	// 		}
	// */
	// 		/* $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59")); */
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  //
	// 	}else if($periode == "last30"){
	// 		/* $firstday = "1";
	// 		$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59")); */
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  //
	// 	}else if($periode == "today"){
	// 		$sdate1 = date("Y-m-d");
	// 		$sdate2 = "00:00:00";
  //
	// 		$edate1 = date("Y-m-d");
	// 		$edate2 = "23:59:59";
  //
	// 		$sdate = $sdate1." ".$sdate2;
	// 		$edate = $edate1." ".$edate2;
	// 	}else{
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
	// 	}
	// }

	$m1      = date("F", strtotime($sdate));
	$year    = date("Y", strtotime($sdate));
	$report = "historikal_integrationwim_unit_";

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

	//print_r($sdate." ".$edate);exit();
	$dbtable = "historikal_integrationwim_unit";
	//$getreport            = $this->m_wimreport->getreportnow($dbtable, $vehicle, $statuswim, $sdate, $edate);

		$this->dbreport = $this->load->database("tensor_report", true);

    // if ($first_load == 1) {
      $this->dbreport->limit(100, 0);
    // }else {
    //   if ($transactionidfix != 0) {
    //     $this->dbreport->where_in("integrationwim_transactionID", $transactionidfix);
    //   }else {
    //     if ($vehicle != "all") {
    //       $this->dbreport->where("integrationwim_TruckID", $vehicle);
    //     }
    //
    //     if ($modewim != "all") {
    //       $this->dbreport->where("integrationwim_status", $modewim);
    //     }
    //
    //     if ($statuswim != "all") {
    //       $this->dbreport->where("integrationwim_operator_status", $statuswim);
    //     }
    //
    //     $this->dbreport->where("integrationwim_PenimbanganStartLocal >= ", $sdate);
    //     $this->dbreport->where("integrationwim_PenimbanganFinishLocal <= ", $edate);
    //   }
    // }

		//$this->dbreport->where("integrationwim_flag", 0);//bukan data dihapus
		// $this->dbreport->order_by("integrationwim_operator_status", 0); // INI DIAKTIFKAN SESUAI PERMINTAAN
    $this->dbreport->where("integrationwim_dumping_fms_port !=", "");//bukan data dihapus
    $this->dbreport->where("integrationwim_dumping_fms_port !=", "PORT BIB");//bukan data dihapus
		$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
		$q = $this->dbreport->get($dbtable);
		$getreport = $q->result_array();

		//print_r($sdate." ".$edate." ".$dbtable);

	$this->params['data'] = $getreport;

	//print_r($getreport);exit();
	// $dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate
	// echo "<pre>";
	// var_dump($getreport);die();
	// echo "<pre>";

	$html = $this->load->view("newdashboard/development/wim/v_wim_result", $this->params, true);
	$callback['error'] = false;
	$callback['html']  = $html;
	$callback['data']  = $getreport;
	echo json_encode($callback);
}

function search_report(){
	$company          = $this->input->post("company");
	$vehicle          = $this->input->post("vehicle");
	$startdate        = $this->input->post("startdate");
	$shour            = "00:00:00";
	$enddate          = $this->input->post("enddate");
	$ehour            = "23:59:59";
	$statuswim        = $this->input->post("statuswim");
	$modewim          = $this->input->post("modewim");
	$periode          = $this->input->post("periode");
	$first_load       = $this->input->post("first_load");
	$transactionid    = $this->input->post("transactionid_select");

	// KONDISI TRANSACTION ID  START
	if ($transactionid != "") {
		$findthissymbol = array(".","-","_","!");
		$symbolfounded  = (str_replace($findthissymbol, '', $transactionid) != $transactionid);
		if ($symbolfounded) {
			$transactionidfix    = 0;
			$callback['error']   = true;
			$callback['message'] = "Gunakan koma sebagai pembatas untuk mencari lebih dari satu Transaction ID";
			echo json_encode($callback);
			return;
		}else {
			$transactionidfix = $transactionid;
		}
	}else {
		$transactionidfix = 0;
	}

	// echo "<pre>";
	// var_dump($transactionidfix);die();
	// echo "<pre>";
	// KONDISI TRANSACTION ID END


	/* $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
	$edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1); */

	$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

	$nowdate   = date("Y-m-d");
	$nowday    = date("d");
	$nowmonth  = date("m");
	$nowyear   = date("Y");
	$lastday   = date("t");

	// if ($first_load == 1) {
		$sdate1 = date("Y-m-d");
		$sdate2 = "00:00:00";

		$edate1 = date("Y-m-d");
		$edate2 = "23:59:59";

		$sdate = $sdate1." ".$sdate2;
		$edate = $edate1." ".$edate2;
	// }else {
	// 	if($periode == "custom"){
	// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
	// 	}else if($periode == "yesterday"){
	// 		$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
	// 		$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
	// 	}else if($periode == "last7"){
	// 		/* $nowday = $nowday - 1;
	// 		$firstday = $nowday - 7;
	// 		if($nowday <= 7){
	// 			$firstday = 1;
	// 		}
	// */
	// 		/* $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59")); */
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  //
	// 	}else if($periode == "last30"){
	// 		/* $firstday = "1";
	// 		$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59")); */
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  //
	// 	}else if($periode == "today"){
	// 		$sdate1 = date("Y-m-d");
	// 		$sdate2 = "00:00:00";
  //
	// 		$edate1 = date("Y-m-d");
	// 		$edate2 = "23:59:59";
  //
	// 		$sdate = $sdate1." ".$sdate2;
	// 		$edate = $edate1." ".$edate2;
	// 	}else{
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
	// 	}
	// }

	$m1      = date("F", strtotime($sdate));
	$year    = date("Y", strtotime($sdate));
	$report = "historikal_integrationwim_unit_";

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

	//print_r($sdate." ".$edate);exit();
	$dbtable = "historikal_integrationwim_unit";
	//$getreport            = $this->m_wimreport->getreportnow($dbtable, $vehicle, $statuswim, $sdate, $edate);

		$this->dbreport = $this->load->database("tensor_report", true);

    // if ($first_load == 1) {
      $this->dbreport->limit(100, 0);
    // }else {
    //   if ($transactionidfix != 0) {
    //     $this->dbreport->where_in("integrationwim_transactionID", $transactionidfix);
    //   }else {
    //     if ($vehicle != "all") {
    //       $this->dbreport->where("integrationwim_TruckID", $vehicle);
    //     }
    //
    //     if ($modewim != "all") {
    //       $this->dbreport->where("integrationwim_status", $modewim);
    //     }
    //
    //     if ($statuswim != "all") {
    //       $this->dbreport->where("integrationwim_operator_status", $statuswim);
    //     }
    //
    //     $this->dbreport->where("integrationwim_PenimbanganStartLocal >= ", $sdate);
    //     $this->dbreport->where("integrationwim_PenimbanganFinishLocal <= ", $edate);
    //   }
    // }

		//$this->dbreport->where("integrationwim_flag", 0);//bukan data dihapus
		// $this->dbreport->order_by("integrationwim_operator_status", 0); // INI DIAKTIFKAN SESUAI PERMINTAAN
		$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
		$q = $this->dbreport->get($dbtable);
		$getreport = $q->result_array();

		//print_r($sdate." ".$edate." ".$dbtable);

	$this->params['data'] = $getreport;

	//print_r($getreport);exit();
	// $dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate
	// echo "<pre>";
	// var_dump($getreport);die();
	// echo "<pre>";

	$html = $this->load->view("newdashboard/development/wim/v_wim_result", $this->params, true);
	$callback['error'] = false;
	$callback['html']  = $html;
	$callback['data']  = $getreport;
	echo json_encode($callback);
}

function getVehicleByInput(){
  $keyword     = $_POST['keyword'];
  $datavehicle = $this->m_development->getDataVehicle($keyword);

  $datafix = array();
  if (sizeof($datavehicle) > 0) {
    $datafix = array_map('current', $datavehicle);
  }
  echo json_encode(array("data" => $datafix));
}

function getClientByInput(){
  $keyword     = $_POST['keyword'];
  $data = $this->m_development->getDataClient($keyword);

  $datafix = array();
  if (sizeof($data) > 0) {
    $datafix = array_map('current', $data);
  }
  echo json_encode(array("data" => $datafix));
}

function getDriverItws(){
  $keyword     = $_POST['keyword'];
  $data = $this->m_development->getDataDriverItws($keyword);

  $datafix = array();
  if (sizeof($data) > 0) {
    $datafix = array_map('current', $data);
  }
  echo json_encode(array("data" => $datafix));
}

function getMaterialByInput(){
  $keyword     = $_POST['keyword'];
  $data = $this->m_development->getDataMaterial($keyword);

  $datafix = array();
  if (sizeof($data) > 0) {
    $datafix = array_map('current', $data);
  }
  echo json_encode(array("data" => $datafix));
}

function getMaterialValue(){
  $keyword = $_POST['materialid'];
  $data    = $this->m_development->getThisMaterial($keyword);

  if (sizeof($data) > 0) {
    echo json_encode(array("data" => $data));
  }else {
    echo json_encode(array("data" => 0));
  }
}

function getDriverItwsValue(){
  $keyword = $_POST['driveritwsid'];
  $data    = $this->m_development->getThisDriverItws($keyword);

  if (sizeof($data) > 0) {
    echo json_encode(array("data" => $data));
  }else {
    echo json_encode(array("data" => 0));
  }
}

function recallThisVehicle() {
  $keyword     = $_POST['vehicleno'];
  $dataRecall  = $this->m_development->recallToLast($keyword);
  $company     = $dataRecall[0]['integrationwim_haulingContractor'];
  $company_fix = "";

  if ($company == "HTM") {
    $company_fix = "MKS";
  }elseif ($company == "RAM") {
    $company_fix = "RAMB";
  }elseif ($company == "STL") {
    $company_fix = "STLI";
  }elseif ($company == "GEC") {
    $company_fix = "GECL";
  }else {
    $company_fix = $company;
  }

  $alldriveritws = $this->m_development->driveritwsbycompany($company_fix);

  // echo "<pre>";
  // var_dump($alldriveritws);die();
  // echo "<pre>";

  if (sizeof($dataRecall) > 0) {
    $dataforprocess  = $dataRecall[0];
    echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_driver_itws" => $alldriveritws));
  }else {
    echo json_encode(array("code" => 400));
  }

  // if (sizeof($dataRecall) > 0 && sizeof($dataRecall) > 1) {
  //   $dataforprocess  = $dataRecall[0];
  //   $datalastrecall  = $dataRecall[1];
  //   // $datalastrecall2 = $dataRecall[2];
  //   // echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall, "data_lastrecall2" => $datalastrecall2));
  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall));
  // }elseif (sizeof($dataRecall) == 1) {
  //   $dataforprocess  = $dataRecall[0];
  //
  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => array()));
  // }else {
  //   echo json_encode(array("code" => 400));
  // }
}

function recallThisVehicleotherport() {
  $keyword     = $_POST['vehicleno'];
  $dataRecall  = $this->m_development->recallToLastOtherPort($keyword);
  $company     = $dataRecall[0]['integrationwim_haulingContractor'];
  $company_fix = "";

  if ($company == "HTM") {
    $company_fix = "MKS";
  }elseif ($company == "RAM") {
    $company_fix = "RAMB";
  }elseif ($company == "STL") {
    $company_fix = "STLI";
  }elseif ($company == "GEC") {
    $company_fix = "GECL";
  }else {
    $company_fix = $company;
  }

  $alldriveritws = $this->m_development->driveritwsbycompany($company_fix);

  // echo "<pre>";
  // var_dump($alldriveritws);die();
  // echo "<pre>";

  if (sizeof($dataRecall) > 0) {
    $dataforprocess  = $dataRecall[0];
    echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_driver_itws" => $alldriveritws));
  }else {
    echo json_encode(array("code" => 400));
  }

  // if (sizeof($dataRecall) > 0 && sizeof($dataRecall) > 1) {
  //   $dataforprocess  = $dataRecall[0];
  //   $datalastrecall  = $dataRecall[1];
  //   // $datalastrecall2 = $dataRecall[2];
  //   // echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall, "data_lastrecall2" => $datalastrecall2));
  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall));
  // }elseif (sizeof($dataRecall) == 1) {
  //   $dataforprocess  = $dataRecall[0];
  //
  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => array()));
  // }else {
  //   echo json_encode(array("code" => 400));
  // }
}

function itwsotherport_update_data(){
  $itws_transID = $this->input->post('itws_transID');
  $itws_gross_manual   = $this->input->post('itws_gross_manual');

  if ($itws_gross_manual == "" || $itws_gross_manual == "0") {
    $callback['error'] = true;
    $callback['message'] = "Harap mengisi Gross terlebih dahulu";
    echo json_encode($callback);
    return;
  }

  $dataforupdate = array(
    "integrationwim_gross_manual"        => $itws_gross_manual,
    "integrationwim_otherport_status"    => 1,
    "integrationwim_otherport_user_id"   => $this->sess->user_id,
    "integrationwim_otherport_user_name" => $this->sess->user_name,
    "integrationwim_otherport_datetime"  => date("Y-m-d H:i:s")
  );

  // echo "<pre>";
  // var_dump($dataforupdate);die();
  // echo "<pre>";

  $update = $this->m_development->updateitwsnow($itws_transID, $dataforupdate);
    if ($update) {
      echo json_encode(array("code" => 200, "message" => "Successfully update data"));
    }else {
      echo json_encode(array("code" => 400, "message" => "Failed update data"));
    }
}

function itws_update_data(){
  $itws_transID       = $this->input->post('itws_transID');
  $itws_nolambung     = $this->input->post('itws_nolambung');
  $itws_rom           = $this->input->post('itws_rom');
  $itws_driver        = explode("|", $this->input->post('itws_driver'));
  $driver_id_cron     = $this->input->post('driver_id_cron');
  $driver_name_cron   = $this->input->post('driver_name_cron');
  $itws_client        = $this->input->post('itws_client');
  $itws_material      = $this->input->post('itws_material');
  $itws_hauling       = $this->input->post('itws_hauling');
  $itws_coal          = $this->input->post('itws_coal');

  if ($itws_transID == "" || $itws_nolambung == "") {
    $callback['error'] = true;
    $callback['message'] = "Transaction ID / No Lambung tidak boleh kosong";
    echo json_encode($callback);
    return;
  }

  if ($itws_rom == "" || $itws_rom == "0") {
    $callback['error'] = true;
    $callback['message'] = "Harap memilih ROM terlebih dahulu";
    echo json_encode($callback);
    return;
  }

  $dataforupdate = array(
    "integrationwim_last_rom"           => $itws_rom,
    "integrationwim_client_id"          => $itws_client,
    "integrationwim_material_id"        => $itws_material,
    "integrationwim_hauling_id"         => $itws_hauling,
    "integrationwim_itws_coal"          => $itws_coal,
    "integrationwim_operator_status"    => 1,
    "integrationwim_driver_iditws"      => $itws_driver[0],
    "integrationwim_driver_nameitws"    => $itws_driver[1],
    "integrationwim_driver_id"          => $driver_id_cron,
    "integrationwim_driver_name"        => $driver_name_cron,
    "integrationwim_operator_user_id"   => $this->sess->user_id,
    "integrationwim_operator_user_name" => $this->sess->user_name,
    "integrationwim_operator_datetime"  => date("Y-m-d H:i:s", strtotime("+1 hour")),
  );

  // echo "<pre>";
  // var_dump($dataforupdate);die();
  // echo "<pre>";

  $update = $this->m_development->updateitwsnow($itws_transID, $dataforupdate);
    if ($update) {
      echo json_encode(array("code" => 200, "message" => "Successfully update data"));
    }else {
      echo json_encode(array("code" => 400, "message" => "Failed update data"));
    }
}

function get_company_bylevel()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode 						= $this->sess->user_id_role;

		$this->db->order_by("company_name", "asc");
		if ($privilegecode == 0) {
			$this->db->where("company_created_by", $this->sess->user_id);
		} elseif ($privilegecode == 1) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 2) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 3) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 4) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 5) {
			$this->db->where("company_created_by", $this->sess->user_company);
		}elseif ($privilegecode == 6) {
			$this->db->where("company_created_by", $this->sess->user_company);
		}elseif ($privilegecode == 7) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		}elseif ($privilegecode == 8) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		}

		$this->db->where_in("company_exca", array(0,2));
    $this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}


  function overspeedreport()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$user_id             = $this->sess->user_id;
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role        = $this->sess->user_id_role;
		$privilegecode			 = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows                           = $q->result();
		$rows_company                   = $this->get_company_bylevel();
		$rows_geofence                  = $this->get_geofence_bydblive($user_dblive);//print_r($rows_geofence);exit();

    $dataKMStreet = $this->m_development->getdatakmstreet();
    $datakmfix    = array();
    $datakmtiafix = array();

      for ($i=0; $i < sizeof($dataKMStreet); $i++) {
        $street_name = $dataKMStreet[$i]['street_name'];
          if (strpos($street_name, "TIA") !== FALSE) {
            if (strpos($street_name, ".") == FALSE) {
              $streetnameexpl2 = explode(",", $street_name);
                  array_push($datakmtiafix, array(
                    "street_name" => $streetnameexpl2[0]
                  ));
            }
          }else {
            $streetnameexpl2 = explode(",", $street_name);
              array_push($datakmfix, array(
                "street_name" => $streetnameexpl2[0]
              ));
          }
          $datastreetfix = array_merge($datakmfix, $datakmtiafix);
      }

    // echo "<pre>";
    // var_dump($datastreetfix);die();
    // echo "<pre>";

		$this->params["vehicles"]       = $rows;
    $this->params["datastreetfix"]  = $datastreetfix;
		$this->params["rcompany"]       = $rows_company;
		$this->params["rgeofence"]      = $rows_geofence;
		$this->params['code_view_menu'] = "report";


		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/development/report/overspeed/v_home_ovspeed', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

  function search_overspeedreport_localmethod()
  {
    ini_set('display_errors', 1);
    ini_set('memory_limit', '6G');
		ini_set('max_execution_time', '1000');
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
    $jalur             = $this->input->post("jalur");
    $geofence          = $this->input->post("geofence");
    $rambu             = $this->input->post("rambu");
    $periode           = $this->input->post("periode");

    $km_checkbox       = $this->input->post("km_checkbox");
    $kmselected_select = $this->input->post("kmselected_select");

    $kmstart           = $this->input->post("kmstart");
    $kmend             = $this->input->post("kmend");

    // echo "<pre>";
    // var_dump($kmstart);die();
    // echo "<pre>";

    $datakm = array();
    if ($km_checkbox == 1) {
      // KONDISI RANGE KM OVERSPEED UI BARU
        if ($kmstart == "all") {
          $datakm = array("KM ", "TIA KM ");

          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              if (strpos($datakm[$j], "TIA") !== FALSE) {
                $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                  for ($k=0; $k < sizeof($get_similar_km); $k++) {
                    $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                  }
              }else {
                $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                  for ($k=0; $k < sizeof($get_similar_km); $k++) {
                    $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                  }
              }
            }
            $datakm = array_merge($datakmfix);
        }elseif ($kmstart == "allkm") {
          $datakm = array("KM ");

          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
            $datakm = array_merge($datakmfix);
        }elseif ($kmstart == "alltia") {
          $datakm = array("TIA KM ");
          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
            $datakm = array_merge($datakmfix);
        }else {
          $kmendfix = preg_replace('/[^0-9]/', '', $kmend);

          if ($kmendfix == 0) {
            if (strpos($kmstart, "TIA") !== FALSE) {
              for ($i=$kmstart; $i <= $kmstart; $i++) {
                $datakm[] = "TIA KM " . $i;
              }
            }else {
              for ($i=$kmstart; $i <= $kmstart; $i++) {
                $datakm[] = "KM " . $i;
              }
            }
          }else {
            $kmstartfix = preg_replace('/[^0-9]/', '', $kmstart);
            if (strpos($kmstart, "TIA") !== FALSE) {
              for ($i=$kmstartfix; $i <= $kmendfix; $i ++) {
                $datakm[] = "TIA KM " . $i;
              }
            }else {
              for ($i=$kmstartfix; $i <= $kmendfix; $i ++) {
                $datakm[] = "KM " . $i;
              }
            }
          }
        }

        // echo "<pre>";
        // var_dump($datakm);die();
        // echo "<pre>";

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
            for ($k=0; $k < sizeof($get_similar_km); $k++) {
              $street_group = $get_similar_km[$k]['street_group'];
                if ($street_group == $datakm[$j]) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($datakm, $datakmfix);
    }else {
      // KONDISI SELECTED KM OVERSPEED UI BARU
      $data_selected_kmfix = array();
      if ($kmselected_select == false) {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "all") {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "allkm") {
        $datakm = array("KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "alltia") {
        $datakm = array("TIA KM ");
        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }else {
        for ($k=0; $k < sizeof($kmselected_select); $k++) {
            $data_selected_kmfix[] = $kmselected_select[$k];
        }
        // $data_merge = array_merge($data_selected_kmfix, $increase_selected_km);
        $datakm = $data_selected_kmfix;

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $streetnameexpl = explode("TIA ", $datakm[$j]);
                  $streetnamefix  = $streetnameexpl[1];
                  $street_group   = $get_similar_km[$k]['street_group'];
                    if ($street_group == $streetnamefix) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $street_group = $get_similar_km[$k]['street_group'];
                    if ($street_group == $datakm[$j]) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }
    }


      // echo "<pre>";
      // var_dump($datakm);die();
      // // var_dump($kmstart.'-'.$kmend);die();
      // echo "<pre>";

    // $km 				= $this->input->post("km");


    $nowdate    = date("Y-m-d");
    $nowday     = date("d");
    $nowmonth   = date("m");
    $nowyear    = date("Y");
    $lastday    = date("t");

    $report     = "overspeed_";
    $report_sum = "summary_";

    if($periode == "custom"){
      $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
    }else if($periode == "yesterday"){

      $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
      $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

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

    if($vehicle == "0"){
      if($privilegecode == 0){
        $dataprivilege = $user_id_fix;
      }else if($privilegecode == 1){
        $dataprivilege = $user_parent;
      }else if($privilegecode == 2){
        $dataprivilege = $user_parent;
      }else if($privilegecode == 3){
        $dataprivilege = $user_parent;
      }else if($privilegecode == 4){
        $dataprivilege = $user_parent;
      }else if($privilegecode == 5){
        $dataprivilege = $user_company;
      }else if($privilegecode == 6){
        $dataprivilege = $user_company;
      }else{
        $dataprivilege = 99999;
      }
    }else{
      $dataprivilege = $vehicle;
    }

    $url = "https://temansharing.borneo-indobara.com/development/overspeedreport_from_nms";
		$dataforsent = array(
      "privilegecode" => $privilegecode,
      "dataprivilege" => $dataprivilege,
      "vehicle"       => $vehicle,
      "sdate"         => $sdate,
      "edate"         => $edate,
      "status"        => 1,
      "tipe"          => "road",
      "company"       => $company,
      "jalur"         => $jalur,
      "geofence"      => $geofence,
      "rambu"         => $rambu,
      "datakm"        => $datakm,
      "dbtable"       => $dbtable,
    );

    // echo "<pre>";
    // var_dump($vehicle);die();
    // echo "<pre>";

				$content = json_encode($dataforsent);

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPHEADER,
				        array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

				$json_response = curl_exec($curl);

				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // if ( $status != 201 ) {
				//     die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
				// }

				// echo $json_response;

			echo "<pre>";
			var_dump($json_response);die();
			echo "<pre>";
  }

  function search_overspeedreport()
  {
    ini_set('display_errors', 1);
    ini_set('memory_limit', '6G');
		ini_set('max_execution_time', '1000');
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
    $jalur             = $this->input->post("jalur");
    $geofence          = $this->input->post("geofence");
    $rambu             = $this->input->post("rambu");
    $periode           = $this->input->post("periode");

    $km_checkbox       = $this->input->post("km_checkbox");
    $kmselected_select = $this->input->post("kmselected_select");

    $kmstart           = $this->input->post("kmstart");
    $kmend             = $this->input->post("kmend");

    // echo "<pre>";
    // var_dump($kmstart);die();
    // echo "<pre>";

    $datakm = array();
    if ($km_checkbox == 1) {
      // KONDISI RANGE KM OVERSPEED UI BARU
        if ($kmstart == "all") {
          $datakm = array("KM ", "TIA KM ");

          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              if (strpos($datakm[$j], "TIA") !== FALSE) {
                $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                  for ($k=0; $k < sizeof($get_similar_km); $k++) {
                    $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                  }
              }else {
                $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                  for ($k=0; $k < sizeof($get_similar_km); $k++) {
                    $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                  }
              }
            }
            $datakm = array_merge($datakmfix);
        }elseif ($kmstart == "allkm") {
          $datakm = array("KM ");

          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
            $datakm = array_merge($datakmfix);
        }elseif ($kmstart == "alltia") {
          $datakm = array("TIA KM ");
          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
            $datakm = array_merge($datakmfix);
        }else {
          $kmendfix = preg_replace('/[^0-9]/', '', $kmend);

          if ($kmendfix == 0) {
            if (strpos($kmstart, "TIA") !== FALSE) {
              for ($i=$kmstart; $i <= $kmstart; $i++) {
                $datakm[] = "TIA KM " . $i;
              }
            }else {
              for ($i=$kmstart; $i <= $kmstart; $i++) {
                $datakm[] = "KM " . $i;
              }
            }
          }else {
            $kmstartfix = preg_replace('/[^0-9]/', '', $kmstart);
            if (strpos($kmstart, "TIA") !== FALSE) {
              for ($i=$kmstartfix; $i <= $kmendfix; $i ++) {
                $datakm[] = "TIA KM " . $i;
              }
            }else {
              for ($i=$kmstartfix; $i <= $kmendfix; $i ++) {
                $datakm[] = "KM " . $i;
              }
            }
          }
        }

        // echo "<pre>";
        // var_dump($datakm);die();
        // echo "<pre>";

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
            for ($k=0; $k < sizeof($get_similar_km); $k++) {
              $street_group = $get_similar_km[$k]['street_group'];
                if ($street_group == $datakm[$j]) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($datakm, $datakmfix);
    }else {
      // KONDISI SELECTED KM OVERSPEED UI BARU
      $data_selected_kmfix = array();
      if ($kmselected_select == false) {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "all") {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "allkm") {
        $datakm = array("KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "alltia") {
        $datakm = array("TIA KM ");
        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }else {
        for ($k=0; $k < sizeof($kmselected_select); $k++) {
            $data_selected_kmfix[] = $kmselected_select[$k];
        }
        // $data_merge = array_merge($data_selected_kmfix, $increase_selected_km);
        $datakm = $data_selected_kmfix;

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $streetnameexpl = explode("TIA ", $datakm[$j]);
                  $streetnamefix  = $streetnameexpl[1];
                  $street_group   = $get_similar_km[$k]['street_group'];
                    if ($street_group == $streetnamefix) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $street_group = $get_similar_km[$k]['street_group'];
                    if ($street_group == $datakm[$j]) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }
    }


      // echo "<pre>";
      // var_dump($datakm);die();
      // // var_dump($kmstart.'-'.$kmend);die();
      // echo "<pre>";

    // $km 				= $this->input->post("km");


    $nowdate    = date("Y-m-d");
    $nowday     = date("d");
    $nowmonth   = date("m");
    $nowyear    = date("Y");
    $lastday    = date("t");

    $report     = "overspeed_";
    $report_sum = "summary_";

    if($periode == "custom"){
      $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
    }else if($periode == "yesterday"){

      $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
      $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

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

    // echo "<pre>";
    // var_dump($dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate.'-'.$reporttype.'-'.$company.'-'.$jalur.'-'.$geofence.'-'.$km);die();
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

      $this->dbtrip = $this->load->database("webtracking_kalimantan",true);
      //$this->dbtrip->order_by("overspeed_report_vehicle_no","asc");
      $this->dbtrip->order_by("overspeed_report_gps_time","asc");
      if($vehicle == "0"){
        if($privilegecode == 0){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id_fix);
        }else if($privilegecode == 1){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 2){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 3){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 4){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 5){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else if($privilegecode == 6){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else{
          $this->dbtrip->where("overspeed_report_vehicle_company",99999);
        }
        $this->dbtrip->where("overspeed_report_vehicle_id <>",72150933); //jika pilih all bukan mobil trial
      }else{
        $this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
      }

      $this->dbtrip->where("overspeed_report_gps_time >=",$sdate);
      $this->dbtrip->where("overspeed_report_gps_time <=", $edate);
      $this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
      $this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
      // $this->dbtrip->where("overspeed_report_type", $reporttype); //data fix (default) = 0
      $this->dbtrip->where("overspeed_report_type", 0); //data fix (default) = 0


      if($company != "all"){
        $this->dbtrip->where("overspeed_report_vehicle_company", $company);
      }

      if($jalur != "all"){
        $this->dbtrip->where("overspeed_report_jalur", $jalur);
      }

      if($geofence != "all"){
        $this->dbtrip->where("overspeed_report_geofence_name", $geofence);
      }

      if($rambu != "all"){
        $this->dbtrip->where("overspeed_report_geofence_limit >= ", $rambu);
      }

      // if($km != ""){
      // 	$this->dbtrip->where_in("overspeed_report_location", "KM ".$km);
      // }

      // if($datakm[0] != "KM "){
        $this->dbtrip->where_in("overspeed_report_location", $datakm);
      // }
      $this->dbtrip->where("overspeed_report_event_status",1);
      $q = $this->dbtrip->get($dbtable);

      if ($q->num_rows>0)
      {
        $rows = $q->result();
      }else{
        $error .= "- No Data Overspeed ! \n";
      }

    if ($error != "")
    {
      $callback['error'] = true;
      $callback['message'] = $error;

      echo json_encode($callback);
      return;
    }

    // $code_temp = "";
    // if (sizeof($rows) > 0) {
    //   $code_temp = strtotime(date("Y-m-d H:i:s"));
    //   $data_temp = json_encode($rows);
    //
    //   $datanya = array(
    //     "overspeed_temp_code"     => $code_temp,
    //     "overspeed_temp_data"     => $data_temp,
    //     "overspeed_temp_submited" => date("Y-m-d H:i:s"),
    //   );
    //
    //   // echo "<pre>";
    //   // var_dump(json_decode($datanya['overspeed_temp_data'], true));die();
    //   // echo "<pre>";
    //
    //   $insertto_temp_table = $this->m_development->savetotempovspeed($datanya);
    // }

    $params['data']      = $rows;
    $params['dbtable']   = $dbtable;
    $params['startdate'] = $sdate;
    $params['enddate']   = $edate;

    $html = $this->load->view("newdashboard/development/report/overspeed/v_ovspeed_result", $params, true);

    $callback['error']     = false;
    $callback['html']      = $html;
    $callback['data']      = $rows;
    // $callback['code_temp'] = $code_temp;
    echo json_encode($callback);
    //return;

  }

  function search_overspeedreportphpexcel(){
    $company           = $this->input->post("company");
    $vehicle           = $this->input->post("vehicle");
    $startdate         = $this->input->post("startdate");
    $enddate           = $this->input->post("enddate");
    $shour             = $this->input->post("shour");
    $ehour             = $this->input->post("ehour");
    $jalur             = $this->input->post("jalur");
    $geofence          = $this->input->post("geofence");
    $rambu             = $this->input->post("rambu");
    $periode           = $this->input->post("periode");

    $km_checkbox       = $this->input->post("km_checkbox");
    $kmselected_select = $this->input->post("kmselected_select");

    $kmstart           = $this->input->post("kmstart");
    $kmend             = $this->input->post("kmend");

    $datakm = array();
    if ($km_checkbox == 1) {
      // KONDISI RANGE KM OVERSPEED UI BARU
        if ($kmstart == "all") {
          $datakm = array("KM ", "TIA KM ");

          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              if (strpos($datakm[$j], "TIA") !== FALSE) {
                $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                  for ($k=0; $k < sizeof($get_similar_km); $k++) {
                    $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                  }
              }else {
                $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                  for ($k=0; $k < sizeof($get_similar_km); $k++) {
                    $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                  }
              }
            }
            $datakm = array_merge($datakmfix);
        }elseif ($kmstart == "allkm") {
          $datakm = array("KM ");

          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
            $datakm = array_merge($datakmfix);
        }elseif ($kmstart == "alltia") {
          $datakm = array("TIA KM ");
          $datakmfix = array();
            for ($j=0; $j < sizeof($datakm); $j++) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
            $datakm = array_merge($datakmfix);
        }else {
          $kmendfix = preg_replace('/[^0-9]/', '', $kmend);

          if ($kmendfix == 0) {
            if (strpos($kmstart, "TIA") !== FALSE) {
              for ($i=$kmstart; $i <= $kmstart; $i++) {
                $datakm[] = "TIA KM " . $i;
              }
            }else {
              for ($i=$kmstart; $i <= $kmstart; $i++) {
                $datakm[] = "KM " . $i;
              }
            }
          }else {
            $kmstartfix = preg_replace('/[^0-9]/', '', $kmstart);
            if (strpos($kmstart, "TIA") !== FALSE) {
              for ($i=$kmstartfix; $i <= $kmendfix; $i ++) {
                $datakm[] = "TIA KM " . $i;
              }
            }else {
              for ($i=$kmstartfix; $i <= $kmendfix; $i ++) {
                $datakm[] = "KM " . $i;
              }
            }
          }
        }

        // echo "<pre>";
        // var_dump($datakm);die();
        // echo "<pre>";

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
            for ($k=0; $k < sizeof($get_similar_km); $k++) {
              $street_group = $get_similar_km[$k]['street_group'];
                if ($street_group == $datakm[$j]) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($datakm, $datakmfix);
    }else {
      // KONDISI SELECTED KM OVERSPEED UI BARU
      $data_selected_kmfix = array();
      if ($kmselected_select == false) {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "all") {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "allkm") {
        $datakm = array("KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "alltia") {
        $datakm = array("TIA KM ");
        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }else {
        for ($k=0; $k < sizeof($kmselected_select); $k++) {
            $data_selected_kmfix[] = $kmselected_select[$k];
        }
        // $data_merge = array_merge($data_selected_kmfix, $increase_selected_km);
        $datakm = $data_selected_kmfix;

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $streetnameexpl = explode("TIA ", $datakm[$j]);
                  $streetnamefix  = $streetnameexpl[1];
                  $street_group   = $get_similar_km[$k]['street_group'];
                    if ($street_group == $streetnamefix) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }else {
              $get_similar_km = $this->m_development->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $street_group = $get_similar_km[$k]['street_group'];
                    if ($street_group == $datakm[$j]) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }
    }


      // echo "<pre>";
      // var_dump($datakm);die();
      // // var_dump($kmstart.'-'.$kmend);die();
      // echo "<pre>";

    // $km 				= $this->input->post("km");


    $nowdate    = date("Y-m-d");
    $nowday     = date("d");
    $nowmonth   = date("m");
    $nowyear    = date("Y");
    $lastday    = date("t");

    $report     = "overspeed_";
    $report_sum = "summary_";

    if($periode == "custom"){
      $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
    }else if($periode == "yesterday"){

      $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
      $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

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

    // echo "<pre>";
    // var_dump($dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate.'-'.$reporttype.'-'.$company.'-'.$jalur.'-'.$geofence.'-'.$km);die();
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

      $this->dbtrip = $this->load->database("webtracking_kalimantan",true);
      //$this->dbtrip->order_by("overspeed_report_vehicle_no","asc");
      $this->dbtrip->order_by("overspeed_report_gps_time","asc");
      if($vehicle == "0"){
        if($privilegecode == 0){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id_fix);
        }else if($privilegecode == 1){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 2){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 3){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 4){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 5){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else if($privilegecode == 6){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else{
          $this->dbtrip->where("overspeed_report_vehicle_company",99999);
        }
        $this->dbtrip->where("overspeed_report_vehicle_id <>",72150933); //jika pilih all bukan mobil trial
      }else{
        $this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
      }

      $this->dbtrip->where("overspeed_report_gps_time >=",$sdate);
      $this->dbtrip->where("overspeed_report_gps_time <=", $edate);
      $this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
      $this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
      // $this->dbtrip->where("overspeed_report_type", $reporttype); //data fix (default) = 0
      $this->dbtrip->where("overspeed_report_type", 0); //data fix (default) = 0


      if($company != "all"){
        $this->dbtrip->where("overspeed_report_vehicle_company", $company);
      }

      if($jalur != "all"){
        $this->dbtrip->where("overspeed_report_jalur", $jalur);
      }

      if($geofence != "all"){
        $this->dbtrip->where("overspeed_report_geofence_name", $geofence);
      }

      if($rambu != "all"){
        $this->dbtrip->where("overspeed_report_geofence_limit >= ", $rambu);
      }

      // if($km != ""){
      // 	$this->dbtrip->where_in("overspeed_report_location", "KM ".$km);
      // }

      if($datakm[0] != "KM "){
        $this->dbtrip->where_in("overspeed_report_location", $datakm);
      }
      $this->dbtrip->where("overspeed_report_event_status",1);
      $q = $this->dbtrip->get($dbtable);

      if ($q->num_rows>0)
      {
        $rows = $q->result();
      }else{
        $error .= "- No Data Overspeed ! \n";
      }

    if ($error != "")
    {
      $callback['error'] = true;
      $callback['message'] = $error;

      echo json_encode($callback);
      return;
    }

    $objectexcel = new PHPExcel();
    $objectexcel->setActiveSheetIndex(0);
    $table_columns = array(
      "No", "Driver", "Date", "Time", "Shift", "Vehicle","Vehicle Name","Alarm Name","Position","Lat", "Lng",
      "GPS Speed", "Geofence", "Speed Limit", "Jalur"
    );
    $column = 0;
    foreach($table_columns as $field)
    {
     $objectexcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
     $column++;
    }

    $excel_row = 2;
      for($i = 0; $i < sizeof($rows); $i++){
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i+1);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, "");
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, date("d-m-Y", strtotime($rows[$i]->overspeed_report_gps_time)));
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("H:i:s", strtotime($rows[$i]->overspeed_report_gps_time)));

       $shiftfix = "-";
         $timeforshift = date("H:i:s", strtotime($rows[$i]->overspeed_report_gps_time));
           if ($timeforshift >= "06:00:00" && $timeforshift <= "17:59:59") {
             $shiftfix = 1;
           }else {
             $shiftfix = 2;
           }
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $shiftfix);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows[$i]->overspeed_report_vehicle_no);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows[$i]->overspeed_report_vehicle_name);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $rows[$i]->overspeed_report_name);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows[$i]->overspeed_report_location);
         $coordexplode = explode(",", $rows[$i]->overspeed_report_coordinate);
         $coordLat     = $coordexplode[0];
         $coordLong    = $coordexplode[1];
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $coordLat);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $coordLong);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $rows[$i]->overspeed_report_speed);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $rows[$i]->overspeed_report_geofence_name);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $rows[$i]->overspeed_report_geofence_limit);
       $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $rows[$i]->overspeed_report_jalur);
       $excel_row++;
      }

    $object_writer = PHPExcel_IOFactory::createWriter($objectexcel, 'Excel5');
    ob_start();
    $object_writer->save("php://output");
    $xlsData = ob_get_contents();
    ob_end_clean();

    $response =  array(
            'op' => 'ok',
            'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
        );

    die(json_encode($response));
  }

  function search_overspeedreportphpexceltempcode()
  {
    ini_set('display_errors', 1);
    ini_set('memory_limit', '3G');
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

    // PHP EXCEL START
    // include_once('third_party/Classes/PHPExcel.php');
    $objectexcel = new PHPExcel();
    $objectexcel->setActiveSheetIndex(0);
    // PHP EXCEL END

    $tempcode = $_POST['temp_code'];
    $datajson = $this->m_development->gettemp_data($tempcode);
    // echo "<pre>";
    // var_dump();die();
    // echo "<pre>";
      if (sizeof($datajson) > 0) {
        $data = json_decode($datajson[0]->overspeed_temp_data, TRUE);

        // echo "<pre>";
        // var_dump($data);die();
        // echo "<pre>";
        $table_columns = array(
          "No", "Driver", "Date", "Time", "Shift", "Vehicle","Vehicle Name","Alarm Name","Position","Lat", "Lng",
          "GPS Speed", "Geofence", "Speed Limit", "Jalur"
        );
        $column = 0;
        foreach($table_columns as $field)
        {
         $objectexcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
         $column++;
        }

        $excel_row = 2;
          for($i = 0; $i < sizeof($data); $i++){
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i+1);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, "");
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, date("d-m-Y", strtotime($data[$i]['overspeed_report_gps_time'])));
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("H:i:s", strtotime($data[$i]['overspeed_report_gps_time'])));

           $shiftfix = "-";
             $timeforshift = date("H:i:s", strtotime($data[$i]['overspeed_report_gps_time']));
               if ($timeforshift >= "06:00:00" && $timeforshift <= "17:59:59") {
                 $shiftfix = 1;
               }else {
                 $shiftfix = 2;
               }
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $shiftfix);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $data[$i]['overspeed_report_vehicle_no']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $data[$i]['overspeed_report_vehicle_name']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $data[$i]['overspeed_report_name']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $data[$i]['overspeed_report_location']);
             $coordexplode = explode(",", $data[$i]['overspeed_report_coordinate']);
             $coordLat     = $coordexplode[0];
             $coordLong    = $coordexplode[1];
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $coordLat);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $coordLong);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $data[$i]['overspeed_report_speed']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $data[$i]['overspeed_report_geofence_name']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $data[$i]['overspeed_report_geofence_limit']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $data[$i]['overspeed_report_jalur']);
           $excel_row++;
          }

        $object_writer = PHPExcel_IOFactory::createWriter($objectexcel, 'Excel5');
        ob_start();
        $object_writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );

        die(json_encode($response));
      }
  }

  function create_half_loop(){
    $kmstart    = $_POST['kmstart'];
    $kmstartfix = preg_replace('/[^0-9]/', '', $kmstart);
    $kmendhtml = "<option value='0'>--Select KM End</option>";
      if (strpos($kmstart, "TIA KM ") !== FALSE) {
        for ($i=($kmstartfix+1); $i <= 30; $i++) {
           $kmendhtml .= "<option value='TIA KM ".$i."'>TIA KM ".$i."</option>";
         }
      }elseif ($kmstart == "all") {

      }elseif ($kmstart == "allkm") {

      }elseif ($kmstart == "alltia") {

      }else{
        for ($i=($kmstartfix+1); $i <= 35; $i++) {
           $kmendhtml .= "<option value='KM ".$i."'>KM ".$i."</option>";
         }
      }

     echo $kmendhtml;
 		return;
  }

  function get_geofence_bydblive($dblive){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

    $this->dblive = $this->load->database($dblive,true);
    $this->dblive->select("geofence_name");
    $this->dblive->order_by("geofence_name","asc");
    // $this->dblive->where("geofence_user", 4203); //khusus bib
    $this->dblive->where("geofence_user", 4408); //khusus bib
    $this->dblive->where("geofence_status", 1);
    $this->dblive->where("geofence_type", "road");
    $qd = $this->dblive->get("geofence");
    $rd = $qd->result();

    return $rd;
  }

  function quickcountbibnew(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

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
		$mastervehicle                   = $this->m_development->getmastervehicleforheatmap();

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

				if ($auto_status == "P") {
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

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];

		$rstatus                        = $this->m_development->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_development->getalldata("webtracking_poi_poolmaster");

		$totalmobilnya                = sizeof($mastervehicle);
		if ($totalmobilnya == 0) {
			$this->params['name']         = "0";
			$this->params['host']         = "0";
		}else {
			$arr          = explode("@", $mastervehicle[0]['vehicle_device']);
			$this->params['name']         = $arr[0];
			$this->params['host']         = $arr[1];
		}

		$this->params['resultactive']   = $this->m_development->vehicleactive();
		$this->params['resultexpired']  = $this->m_development->vehicleexpired();
		$this->params['resulttotaldev'] = $this->m_development->totaldevice();
		$this->params['mapsetting']     = $this->m_development->getmapsetting();

    // GET STREET KM AND GROUP BY IT
    $allstreetkm        = $this->m_development->getallastreetkm();
    $dataallstreetgroup = array();

      for ($x=0; $x < sizeof($allstreetkm); $x++) {
        $street_group = $allstreetkm[$x]['street_group'];
          $datagroupkm = $this->m_development->getkmgroup($street_group);
          if ($datagroupkm) {
            array_push($dataallstreetgroup, array(
              "street_group"      => $street_group,
              "street_is_romroad" => $datagroupkm[0]['street_is_romroad'],
              "jumlah_total"      => sizeof($datagroupkm),
              "data_group"        => $datagroupkm
            ));
          }else {
            array_push($dataallstreetgroup, array(
              "street_group"      => $street_group,
              "street_is_romroad" => 0,
              "jumlah_total"      => 1,
              "data_group"        => 0
            ));
          }
      }
      $this->params['street_matic']     = $dataallstreetgroup;

		// echo "<pre>";
		// var_dump($dataallstreetgroup);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/trackers/v_quickcountbib_new', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}


  // POC IN ROM DEVELOPMENT
  function pocinrommapsexca(){
  		ini_set('max_execution_time', '300');
  		set_time_limit(300);
  		if (! isset($this->sess->user_type))
  		{
  			redirect(base_url());
  		}

  		$user_id       = $this->sess->user_id;
  		$user_parent   = $this->sess->user_parent;
  		$privilegecode = $this->sess->user_id_role;
  		$user_company  = $this->sess->user_company;
			$user_excavator  = $this->sess->user_excavator;

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
  		$mastervehicle                   = $this->m_development->getmastervehicleformapsexca();

  		$datafix                         = array();
			$dataexca                        = array();
  		$deviceidygtidakada              = array();
  		$statusvehicle['engine_on']  = 0;
  		$statusvehicle['engine_off'] = 0;
      // LOOP EXCA
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

  				if ($mastervehicle[$i]['vehicle_typeunit'] == 1) {
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

      // echo "<pre>";
      // var_dump($vehicleinromfix);die();
      // echo "<pre>";

      // CGET COMPANY
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
  		$this->params['code_view_menu'] = "monitorexca";
  		$this->params['maps_code']      = "morehundred";

  		$this->params['engine_on']      = $statusvehicle['engine_on'];
  		$this->params['engine_off']     = $statusvehicle['engine_off'];


  		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

  		$datastatus                     = explode("|", $rstatus);
  		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
  		$this->params['total_vehicle']  = $datastatus[3];
  		$this->params['total_offline']  = $datastatus[2];

  		$this->params['vehicledata']  = $datafix;
  		$this->params['vehicletotal'] = sizeof($mastervehicle);
  		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
  		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
  		// echo "<pre>";
  		// var_dump($getvehicle_byowner);die();
  		// echo "<pre>";
  		$totalmobilnya                = sizeof($getvehicle_byowner);
  		if ($totalmobilnya == 0) {
  			$this->params['name']         = "0";
  			$this->params['host']         = "0";
  		}else {
  			$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
  			$this->params['name']         = $arr[0];
  			$this->params['host']         = $arr[1];
  		}

  		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
  		$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
  		$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
  		$this->params['mapsetting']     = $this->m_development->getmapsetting();
  		$this->params['poolmaster']     = $this->m_development->getalldata("webtracking_poi_poolmaster");

  		// echo "<pre>";
  		// var_dump($this->params['mapsetting']);die();
  		// echo "<pre>";

  		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  			if ($privilegecode == 1) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  			}elseif ($privilegecode == 2) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  			}elseif ($privilegecode == 3) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  			}elseif ($privilegecode == 4) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  			}elseif ($privilegecode == 5 && $user_excavator == 0) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
				}elseif ($privilegecode == 5 && $user_excavator == 1) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
				}elseif ($privilegecode == 6 && $user_excavator == 0) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
				}elseif ($privilegecode == 6 && $user_excavator == 1) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
				}elseif ($privilegecode == 10) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
				}else {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/development/dashboardrom/v_home_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  			}
  	}

    function mapsexcasimultan(){
      // GET UNIT IN ROM A2
      // RULES :
      // JAM SEKARANG - 7. HASILNYA DIKURANGIN 15 MENIT. ITU DIJADIKAN STARTTIME
      $starttime    = date("Y-m-d H:i:s", strtotime("-7 Hour"));
      $starttimefix = date("Y-m-d H:i:s", strtotime("-15 minutes", strtotime($starttime)));
      $wherenya     = "ROM A2";
      $forclearmaps = $this->m_development->getmastervehicleformapsexcaforclear();
      $dataexca     = $this->m_development->getmastervehicleformapsexca();
      $data_inrom = array(
  								"ROM A2",
  							);

      // GET UNIT EXCA
      $dataexcafix = array();
      for ($i=0; $i < sizeof($dataexca); $i++) {
        if ($dataexca[$i]['vehicle_typeunit'] == 1) {
  			$arr           = explode("@", $dataexca[$i]['vehicle_device']);
  			$devices[0]    = (count($arr) > 0) ? $arr[0] : "";
  			$devices[1]    = (count($arr) > 1) ? $arr[1] : "";
  			$jsonautocheck = json_decode($dataexca[$i]['vehicle_autocheck']);
  			$auto_status   = $jsonautocheck->auto_status;

  			$typeunitfix = "";
  			$typeunit    = $dataexca[$i]['vehicle_typeunit'];

  			if ($typeunit == 1) {
  				$typeunitfix   = "EXCA";
  				// $lastinfofix 	 = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, "");
          $lastinfofix = $this->m_development->vehicleinrom_gps_temp_bydeviceexca("webtracking_gps_temanindobara_live", $devices[0], $devices[1], $starttimefix, $wherenya);

          // echo "<pre>";
          // var_dump($lastinfofix);die();
          // echo "<pre>";
  				$gps_pto       = $lastinfofix[0]['gps_cs'];
  				// if ($auto_status != "M") {
  							array_push($dataexcafix, array(
  								"vehicle_typeunitname" => $typeunitfix,
  								"vehicle_typeunit"     => $dataexca[$i]['vehicle_typeunit'],
  								"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
  								"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
  								"vehicle_id"           => $dataexca[$i]['vehicle_id'],
  								"vehicle_user_id"      => $dataexca[$i]['vehicle_user_id'],
  								"vehicle_device"       => $dataexca[$i]['vehicle_device'],
  								"vehicle_no"           => $dataexca[$i]['vehicle_no'],
  								"vehicle_name"         => $dataexca[$i]['vehicle_name'],
  								"vehicle_active_date2" => $dataexca[$i]['vehicle_active_date2'],
  								"auto_last_road"       => $jsonautocheck->auto_last_road,
  								"auto_last_engine"     => $jsonautocheck->auto_last_engine,
  								"auto_last_speed"      => $jsonautocheck->auto_last_speed,
  								"auto_last_course"     => $jsonautocheck->auto_last_course,
  								"gps_pto"              => $gps_pto,
  							));
  					// }
  				}
  			}
  		}

      // echo "<pre>";
      // var_dump($dataexcafix);die();
      // echo "<pre>";

      $dblive_count     = 1;
      $datavehicleinrom = array();

        for ($i=0; $i < 6; $i++) {
          $data_from_gps_temp = $this->m_development->vehicleinrom_gps_temp("webtracking_gps_temanindobara_live_".$dblive_count, $starttimefix, $wherenya);

            if (sizeof($data_from_gps_temp) > 0) {
              for ($j=0; $j < sizeof($data_from_gps_temp); $j++) {
                array_push($datavehicleinrom, array(
                  "vehicle_device"         => $data_from_gps_temp[$j]['gps_tmp_name'].'@'.$data_from_gps_temp[$j]['gps_tmp_host'],
                  "vehicle_tmp_timereal"   => $data_from_gps_temp[$j]['gps_tmp_time'],
                  "vehicle_tmp_timeformat" => date("Y-m-d H:i:s", strtotime("+7 Hour", strtotime($data_from_gps_temp[$j]['gps_tmp_time']))),
                  "vehicle_tmp_lat"        => $data_from_gps_temp[$j]['gps_tmp_latitude_real'],
                  "vehicle_tmp_lng"        => $data_from_gps_temp[$j]['gps_tmp_longitude_real'],
                  "gps_tmp_road_type"      => $data_from_gps_temp[$j]['gps_tmp_road_type'],
  								"auto_last_speed"        => $data_from_gps_temp[$j]['gps_tmp_speed'],
  								"auto_last_course"       => $data_from_gps_temp[$j]['gps_tmp_course'],
  								"gps_pto"                => $data_from_gps_temp[$j]['gps_tmp_cs'],
                ));
              }
            }
            $dblive_count++;
        }

        // GATHERING DATA TO MASTER VEHICLE
        $vehicleinromfix = array();
        for ($k=0; $k < sizeof($datavehicleinrom); $k++) {
          $getthis = $this->m_development->getthisfrommastervehicle($datavehicleinrom[$k]['vehicle_device']);
            if (sizeof($getthis) > 0) {
              $jsonautocheck = json_decode($getthis[0]['vehicle_autocheck']);
              array_push($vehicleinromfix, array(
                "vehicle_typeunitname"   => "DT",
                "vehicle_no"             => $getthis[0]['vehicle_no'],
                "vehicle_name"           => $getthis[0]['vehicle_name'],
                "vehicle_typeunit"       => $getthis[0]['vehicle_typeunit'],
                "vehicle_device"         => $datavehicleinrom[$k]['vehicle_device'],
                "vehicle_tmp_timereal"   => $datavehicleinrom[$k]['vehicle_tmp_timereal'],
                "vehicle_tmp_timeformat" => $datavehicleinrom[$k]['vehicle_tmp_timeformat'],
                "auto_last_lat"          => $datavehicleinrom[$k]['vehicle_tmp_lat'],
                "auto_last_long"         => $datavehicleinrom[$k]['vehicle_tmp_lng'],
                "gps_tmp_road_type"      => $datavehicleinrom[$k]['gps_tmp_road_type'],
                "auto_last_speed"        => $datavehicleinrom[$k]['auto_last_speed'],
                "auto_last_course"       => $datavehicleinrom[$k]['auto_last_course'],
                "auto_last_engine"       => $jsonautocheck->auto_last_engine,
                "gps_pto"                => $datavehicleinrom[$k]['gps_pto'],
              ));
            }
        }

        $datamerge = array_merge($dataexcafix, $vehicleinromfix);

        echo json_encode(array("msg" => "success", "data" => $datamerge, "alldataforclearmaps" => $forclearmaps));
    }

    function getradius()
  	{
      header("Content-Type: application/json");
      // $postdata         = json_decode(file_get_contents("php://input"));

      $search1      = $_POST['search_1'];
      $search2      = $_POST['search_2'];
      $starttime    = date("Y-m-d H:i:s", strtotime("-7 Hour"));
      $starttimefix = date("Y-m-d H:i:s", strtotime("-15 minutes", strtotime($starttime)));
      $wherenya     = "ROM A2";

      $array_unit = array(
        // $search_1[0], $search_1[1], $search_2[0], $search_2[1]
        $search1, $search2
      );

      $data_array_coordinate = array();
      for ($i=0; $i < 2; $i++) {
        $vdevice            = explode("@", $array_unit[$i]);
        $getthis            = $this->m_development->getthisfrommastervehicle($array_unit[$i]);
        $vehicle_dblive     = $getthis[0]['vehicle_dbname_live'];
        $data_from_gps_temp = $this->m_development->vehicleinrom_gps_temp_bydevice($vehicle_dblive, $vdevice[0], $vdevice[1], $starttimefix, $wherenya);

          array_push($data_array_coordinate, array(
            "vehicle_unit" => $getthis[0]['vehicle_no'],
            "gps_time"     => $data_from_gps_temp[0]['gps_tmp_time'],
            "coordinate"   => $data_from_gps_temp[0]['gps_tmp_latitude_real'].','.$data_from_gps_temp[0]['gps_tmp_longitude_real']
          ));
      }

      $coord1     = explode(",", $data_array_coordinate[0]['coordinate']);
      $coord2     = explode(",", $data_array_coordinate[1]['coordinate']);
      $unit_1     = $data_array_coordinate[0]['vehicle_unit'];
      $unit_2     = $data_array_coordinate[1]['vehicle_unit'];
      $gps_time_1 = date("Y-m-d H:i:s", strtotime("+7 Hour", strtotime($data_array_coordinate[0]['gps_time'])));
      $gps_time_2 = date("Y-m-d H:i:s", strtotime("+7 Hour", strtotime($data_array_coordinate[1]['gps_time'])));


      $latitude1  = $coord1[0];
      $longitude1 = $coord1[1];
      $latitude2  = $coord2[0];
      $longitude2 = $coord2[1];

      // echo "<pre>";
      // var_dump($data_array_coordinate);die();
      // echo "<pre>";

  	  $earth_radius = 6371000;

  	  $dLat = deg2rad($latitude2 - $latitude1);
  	  $dLon = deg2rad($longitude2 - $longitude1);

  	  $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
  	  $c = 2 * asin(sqrt($a));
  	  $d = number_format(($earth_radius * $c), 0, '.', '');

      echo json_encode(
        array(
          "msg"        => "success",
          "unit_1"     => $unit_1,
          "gps_time_1" => $gps_time_1,
          "unit_2"     => $unit_2,
          "gps_time_2" => $gps_time_2,
          "coord_1"    => $latitude1.','.$longitude1,
          "coord_2"    => $latitude2.','.$longitude2,
          "meter"      => $d
        ));
  	}

    function mapsstandard(){ // maps with overlay seperti di history map
    	if (! isset($this->sess->user_type))
    	{
    		redirect(base_url());
    	}

    	$user_id       = $this->sess->user_id;
    	$user_parent   = $this->sess->user_parent;
    	$privilegecode = $this->sess->user_id_role;

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
    	}elseif ($privilegecode == 10) {
    		$user_id_fix = $user_id;
    	}else{
    		$user_id_fix = $user_id;
    	}

    	$companyid                       = $this->sess->user_company;
    	$user_dblive                     = $this->sess->user_dblive;
    	$companyid 											 = $_POST['companyid'];
    	$forclearmaps                    = $this->m_development->getmastervehicleformapsexca();
    	$mastervehicle                   = $this->m_development->getmastervehiclebycontractor($companyid);

			// echo "<pre>";
			// var_dump($mastervehicle);die();
			// echo "<pre>";

    	$datafix            = array();
			$dataexca           = array();
    	$deviceidygtidakada = array();

			$data_inrom = array(
									"ROM A2",
								);

    	for ($i=0; $i < sizeof($mastervehicle); $i++) {
    		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
    		$auto_status   = $jsonautocheck->auto_status;
        $arr           = explode("@", $mastervehicle[$i]['vehicle_device']);
  			$devices[0]    = (count($arr) > 0) ? $arr[0] : "";
  			$devices[1]    = (count($arr) > 1) ? $arr[1] : "";


				$typeunitfix = "";
				$typeunit    = $mastervehicle[$i]['vehicle_typeunit'];

					if ($typeunit == 1) {
            $typeunitfix   = "EXCA";
						$lastinfofix 	 = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, "");
		  			$gps_pto       = $lastinfofix->gps_cs;

						// if ($auto_status != "M") {
							array_push($datafix, array(
								"vehicle_typeunitname" => $typeunitfix,
								"vehicle_typeunit"     => $mastervehicle[$i]['vehicle_typeunit'],
								"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
								"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
								"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
								"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
								"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
								"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
								"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
								"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
								"auto_last_road"       => $jsonautocheck->auto_last_road,
								"auto_last_engine"     => $jsonautocheck->auto_last_engine,
								"auto_last_speed"      => $jsonautocheck->auto_last_speed,
								"auto_last_course"     => $jsonautocheck->auto_last_course,
								"gps_pto"              => $gps_pto,
							));
						// }
    	}
    }

    	// echo "<pre>";
    	// var_dump($datafix);die();
    	// echo "<pre>";

    	echo json_encode(array("code" => "success", "msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
    }

    // HISTORY REPORT WITH RADIUS START
    function historyradius(){
      // ini_set('max_execution_time', '300');
      // set_time_limit(300);
      if (! isset($this->sess->user_type))
      {
        redirect(base_url());
      }

      $privilegecode = $this->sess->user_id_role;

      $mastervehicle = $this->m_development->getmastervehicleformapsexca();
      $allunit       = $this->m_development->getmastervehicleformapsexcaforclear();



      $datafix                         = array();
      $dataexca                        = array();
      $deviceidygtidakada              = array();
      $statusvehicle['engine_on']  = 0;
      $statusvehicle['engine_off'] = 0;
      // LOOP EXCA
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

          if ($mastervehicle[$i]['vehicle_typeunit'] == 1) {
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

        // echo "<pre>";
        // var_dump($datafix);die();
        // echo "<pre>";

        $rows                           = $this->dashboardmodel->getvehicle_report();
        $rows_company                   = $this->get_company_bylevel();
        $this->params["unit_exca"]      = $datafix;
        $this->params["vehicles"]       = $allunit;
        $this->params["rcompany"]       = $rows_company;
        $this->params['code_view_menu'] = "monitoradminonly";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

        if ($privilegecode == 1) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/report/history/v_home_history', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
      }

    function search_history_withplayback($name = "", $host = "", $startdate = "", $shour = "", $ehour = "", $enddate = "")
  	{
  		ini_set('display_errors', 1);
      $unit_1              = $this->input->post("unit_1");
      $unit_2              = $this->input->post("unit_2");
      $unit_1_starttime    = $this->input->post("unit_1_starttime");
      $unit_1_hour         = $this->input->post("unit_1_hour");
      $unit_1_minutes      = $this->input->post("unit_1_minutes");
      $unit_2_starttime    = $this->input->post("unit_2_starttime");
      $unit_2_hour         = $this->input->post("unit_2_hour");
      $unit_2_minutes      = $this->input->post("unit_2_minutes");

      $unit_1_starttimenew = date("Y-m-d H:i:s", strtotime($unit_1_starttime.' '.$unit_1_hour.":".$unit_1_minutes.":00")-7*3600); //WITA
      $unit_1_endtimenew   = date("Y-m-d H:i:s", strtotime($unit_1_starttime.' '.$unit_1_hour.":".$unit_1_minutes.":59")-7*3600); //WITA

      $unit_2_starttimenew = date("Y-m-d H:i:s", strtotime($unit_2_starttime.' '.$unit_2_hour.":".$unit_2_minutes.":00")-7*3600); //WITA
      $unit_2_endtimenew   = date("Y-m-d H:i:s", strtotime($unit_2_starttime.' '.$unit_2_hour.":".$unit_2_minutes.":59")-7*3600); //WITA
      $vehicle_inarray     = array($unit_1, $unit_2);
      $unit_time_inarray   = array($unit_1_starttimenew, $unit_1_endtimenew, $unit_2_starttimenew, $unit_2_endtimenew);

      // echo "<pre>";
      // var_dump($unit_time_inarray);die();
      // echo "<pre>";

  		$mapview          = 1;
  		$tableview        = ""; //disable sementara

  		$vehicle_no       = "-";
  		$vehicle_odometer = 0;
  		$vehicle_type     = "-";
  		$vehicle_user_id  = 0;
  		$error            = "";

      if ($unit_1_starttime == "" || $unit_2_starttime == "") {
  			$error = "- Invalid Vehicle. Silahkan Pilih Tanggal Report yang ingin ditampilkan \n";
  			$callback['error'] = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
  		}

  		if ($unit_1_hour == "" || $unit_2_hour == "") {
  			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
  			$callback['error'] = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
  		}

  		if ($unit_1 == "" || $unit_1 == 0 || $unit_2 == "" || $unit_2 == 0) {
  			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
  			$callback['error'] = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
  		} else {
        $datavehiclenya = array();
        $dataresultnya  = array();
        for ($i=0; $i < sizeof($vehicle_inarray); $i++) {
          $rowvehicle = $this->m_development->getunitbyvdevice($vehicle_inarray[$i]);

          if (count($rowvehicle) > 0) {

            $vehicle_no       = $rowvehicle->vehicle_no;
            $vehicle_odometer = $rowvehicle->vehicle_odometer;
            $vehicle_type     = $rowvehicle->vehicle_type;
            $vehicle_user_id  = $rowvehicle->vehicle_user_id;

              if ($i == 0) {
                $sdate = $unit_time_inarray[0];
                $edate = $unit_time_inarray[1];
              }else {
                $sdate = $unit_time_inarray[2];
                $edate = $unit_time_inarray[3];
              }

              $err = false;
          		$diff = strtotime($enddate) - strtotime($startdate);
          		if ($diff < 0) {
          			$error = " Tanggal tidak benar \n";
          			$err = true;
          		} else if ($diff > 86400) {
          			$error = " maksimal tanggal yang dipilih hanya boleh dua hari \n";
          			$err = true;
          		}
          		if ($err) {
          			$callback['error'] = true;
          			$callback['message'] = $error;

          			echo json_encode($callback);
          			return;
          		}

              if (isset($rowvehicle->vehicle_info)) {
          			$json = json_decode($rowvehicle->vehicle_info);

          			if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
          				$databases = $this->config->item('databases');

          				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {

          					$database = $databases[$json->vehicle_ip][$json->vehicle_port];

          					$table         = $this->config->item("external_gpstable");
          					$tableinfo     = $this->config->item("external_gpsinfotable");


          					$this->dbhist  = $this->load->database($database, TRUE);
          					$this->dbhist2 = $this->load->database("gpshistory", true);
          				} else {
          					$table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
          					$tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
          					$this->dbhist  = $this->load->database("default", TRUE);
          					$this->dbhist2 = $this->load->database("gpshistory", true);
          				}

          				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
          				$vehicle_no     = $rowvehicle->vehicle_no;
          				$vehicle_dev    = $rowvehicle->vehicle_device;
          				$vehicle_name   = $rowvehicle->vehicle_name;
          				$vehicle_type   = $rowvehicle->vehicle_type;

                    array_push($datavehiclenya, array(
                      "vehicle_no" => $vehicle_no
                    ));

          				if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
          					$tablehist     = $vehicle_device[0] . "@t5_gps";
          					$tablehistinfo = $vehicle_device[0] . "@t5_info";
          				} else {
          					$tablehist     = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
          					$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
          				}

          				$rows1 = array();
          				$rows2 = array();
          				$rows3 = array();
          				$rows4 = array();

          				// echo "<pre>";
          				// var_dump($sdate.'-'.$edate);die();
          				// echo "<pre>";

          				// if ($unit_1_starttimenew == $unit_2_starttimenew)
          				// {
          				// 	if ($rowvehicle->vehicle_imei = "869926046501587")
          				// 	{
                  //     // echo "<pre>";
          				// 		// var_dump("sikon 0");die();
          				// 		// echo "<pre>";
          				// 		//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
          				// 		$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
          	      //           								   gps_longitude,gps_latitude,gps_ew,gps_ns");
          				// 		$this->dbhist->where("gps_name", $vehicle_device[0]);
          				// 		$this->dbhist->where("gps_host", $vehicle_device[1]);
          				// 		// $this->dbhist->where("gps_speed >", 0);
          				// 		// $this->dbhist2->where("gps_status", "A");
          				// 		$this->dbhist->where("gps_time >=", $sdate);
          				// 		$this->dbhist->where("gps_time <=", $edate);
          				// 		$this->dbhist->order_by("gps_time", "asc");
          				// 		$this->dbhist->group_by("gps_time");
          				// 		$this->dbhist->limit(1);
          				// 		// $this->dbhist->from($table);
          				// 		$q     = $this->dbhist->get($table);
          				// 		$rows1 = $q->result();
                  //
          				// 		$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
          	      //           								   gps_longitude,gps_latitude,gps_ew,gps_ns");
          				// 		$this->dbhist2->where("gps_name", $vehicle_device[0]);
          				// 		$this->dbhist2->where("gps_host", $vehicle_device[1]);
          				// 		// $this->dbhist2->where("gps_speed >", 0);
          				// 		// $this->dbhist22->where("gps_status", "A");
          				// 		$this->dbhist2->where("gps_time >=", $sdate);
          				// 		$this->dbhist2->where("gps_time <=", $edate);
          				// 		$this->dbhist2->order_by("gps_time", "asc");
          				// 		$this->dbhist2->group_by("gps_time");
                  //     $this->dbhist->limit(1);
          				// 		$q2    = $this->dbhist2->get($tablehist);
          				// 		$rows2 = $q2->result();
                  //
          				// 		$rows  = array_merge($rows1, $rows2);
          				// 		// $rows  = array_merge($rows1, $rows2, $rows1638, $rows2638);
          				// 		$trows = count($rows);
                  //
          				// 		// echo "<pre>";
          				// 		// var_dump($rows2638);die();
          				// 		// echo "<pre>";
                  //
          				// 		$totaldata = $trows;
          				// 		$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
          				// 	}
          				// 	else
          				// 	{
                  //     // echo "<pre>";
          				// 		// var_dump("sikon 1");die();
          				// 		// echo "<pre>";
          				// 		//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
          				// 		$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
          	      //           								   gps_longitude,gps_latitude,gps_ew,gps_ns");
          				// 		$this->dbhist->where("gps_name", $vehicle_device[0]);
          				// 		$this->dbhist->where("gps_host", $vehicle_device[1]);
          				// 		// $this->dbhist->where("gps_speed >", 0);
          				// 		// $this->dbhist2->where("gps_status", "A");
          				// 		$this->dbhist->where("gps_time >=", $sdate);
          				// 		$this->dbhist->where("gps_time <=", $edate);
          				// 		$this->dbhist->order_by("gps_time", "asc");
          				// 		$this->dbhist->group_by("gps_time");
          				// 		$this->dbhist->limit(1);
          				// 		// $this->dbhist->from($table);
          				// 		$q     = $this->dbhist->get($table);
          				// 		$rows1 = $q->result();
                  //
          				// 		// $sdate.'-'.$edate
                  //
          				// 		// echo "<pre>";
          				// 		// var_dump($rows1);die();
          				// 		// echo "<pre>";
                  //
          				// 		//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
          				// 		$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
          	      //           								   gps_longitude,gps_latitude,gps_ew,gps_ns");
          				// 		$this->dbhist2->where("gps_name", $vehicle_device[0]);
          				// 		$this->dbhist2->where("gps_host", $vehicle_device[1]);
          				// 		// $this->dbhist2->where("gps_speed >", 0);
          				// 		// $this->dbhist22->where("gps_status", "A");
          				// 		$this->dbhist2->where("gps_time >=", $sdate);
          				// 		$this->dbhist2->where("gps_time <=", $edate);
                  //     $this->dbhist->limit(1);
          				// 		$this->dbhist2->order_by("gps_time", "asc");
          				// 		$this->dbhist2->group_by("gps_time");
          				// 		$q2    = $this->dbhist2->get($tablehist);
          				// 		$rows2 = $q2->result();
                  //
          				// 		$rows  = array_merge($rows1, $rows2);
          				// 		$trows = count($rows);
                  //
          				// 		// echo "<pre>";
          				// 		// var_dump($tablehist);die();
          				// 		// echo "<pre>";
                  //
          				// 		$totaldata = $trows;
          				// 		$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
          				// 	}
                  //
          				// }
          				// else
          				// {
                    // echo "<pre>";
                    // var_dump("sikon 2");die();
                    // echo "<pre>";
          					// $edate1 = date("Y-m-d 23:59:59", strtotime($sdate));
          					// $sdate1 = date("Y-m-d 00:00:00", strtotime($edate));

          					$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                              							   gps_longitude,gps_latitude,gps_ew,gps_ns");
          					$this->dbhist->where("gps_name", $vehicle_device[0]);
          					// $this->dbhist->where("gps_speed >", 0);
          					//$this->dbhist2->where("gps_status", "A");
          					$this->dbhist->where("gps_time >=", $sdate);
          					$this->dbhist->where("gps_time <=", $edate);
          					$this->dbhist->where("gps_longitude_real <>", "11.0000");
          					$this->dbhist->order_by("gps_time", "desc");
          					$this->dbhist->group_by("gps_time");
          					$this->dbhist->limit(1);
          					$this->dbhist->from($table);
          					$q = $this->dbhist->get();
          					$rows1 = $q->result();

          					$this->dbhist->distinct();

          					$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                                                  gps_longitude,gps_latitude,gps_ew,gps_ns");
          					$this->dbhist->where("gps_name", $vehicle_device[0]);
          					// $this->dbhist->where("gps_speed >", 0);
          					//$this->dbhist2->where("gps_status", "A");
          					$this->dbhist->where("gps_time >=", $sdate);
          					$this->dbhist->where("gps_time <=", $edate);
          					$this->dbhist->where("gps_longitude_real <>", "11.0000");
          					$this->dbhist->order_by("gps_time", "desc");
          					$this->dbhist->group_by("gps_time");
          					$this->dbhist->limit(1);
          					$this->dbhist->from($table);
          					$q2 = $this->dbhist->get();
          					$rows2 = $q2->result();


          					//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
          					$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                          								   gps_longitude,gps_latitude,gps_ew,gps_ns");
          					$this->dbhist2->where("gps_name", $vehicle_device[0]);
          					// $this->dbhist2->where("gps_speed >", 0);
          					//$this->dbhist2->where("gps_status", "A");
          					$this->dbhist2->where("gps_time >=", $sdate);
          					$this->dbhist2->where("gps_time <=", $edate);
          					$this->dbhist2->where("gps_longitude_real <>", "11.0000");
          					$this->dbhist2->order_by("gps_time", "desc");
          					$this->dbhist2->group_by("gps_time");
          					$this->dbhist2->limit(1);
          					$this->dbhist2->from($tablehist);
          					$q3 = $this->dbhist2->get();
          					$rows3 = $q3->result();

          					$this->dbhist2->distinct();

          					$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                          								   gps_longitude,gps_latitude,gps_ew,gps_ns");
          					$this->dbhist2->where("gps_name", $vehicle_device[0]);
          					// $this->dbhist2->where("gps_speed >", 0);
          					//$this->dbhist2->where("gps_status", "A");
          					$this->dbhist2->where("gps_time >=", $sdate);
          					$this->dbhist2->where("gps_time <=", $edate);
          					$this->dbhist2->where("gps_longitude_real <>", "11.0000");
          					$this->dbhist2->order_by("gps_time", "desc");
          					$this->dbhist2->group_by("gps_time");
          					$this->dbhist2->limit(1);
          					$this->dbhist2->from($tablehist);
          					$q4 = $this->dbhist2->get();
          					$rows4 = $q4->result();

          					$rows = array_merge($rows1, $rows2, $rows3, $rows4); //limit data rows = 3000
          					$trows = count($rows);

          					$totaldata = $trows;
          					$data[] = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
          				// }
          			}
          		}
          }
        }

        // echo "<pre>";
        // var_dump($data);die();
        // echo "<pre>";
        if (!isset($data[0][0]->gps_latitude_real)) {
          $callback['error'] = true;
          $callback['message'] = "History " . $datavehiclenya[0]['vehicle_no'] . " tidak ditemukan";

          echo json_encode($callback);
          return;
        }

        if (!isset($data[1][0]->gps_latitude_real)) {
          $callback['error'] = true;
          $callback['message'] = "History " . $datavehiclenya[1]['vehicle_no'] . " tidak ditemukan";

          echo json_encode($callback);
          return;
        }

        $latitude1  = $data[0][0]->gps_latitude_real;
        $longitude1 = $data[0][0]->gps_longitude_real;
        $latitude2  = $data[1][0]->gps_latitude_real;
        $longitude2 = $data[1][0]->gps_longitude_real;

    	  $earth_radius = 6371000;

    	  $dLat = deg2rad($latitude2 - $latitude1);
    	  $dLon = deg2rad($longitude2 - $longitude1);

    	  $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
    	  $c = 2 * asin(sqrt($a));
    	  $d = number_format(($earth_radius * $c), 0, '.', '');

        $unit_1_no       = $datavehiclenya[0]['vehicle_no'];
        $unit_2_no       = $datavehiclenya[1]['vehicle_no'];
        $unit_1_datetime = $data[0][0]->gps_time;
        $unit_2_datetime = $data[1][0]->gps_time;
        $unit_1_coord    = $latitude1.','.$longitude1;
        $unit_2_coord    = $latitude2.','.$longitude2;

        $dataradius = array(
          "unit_1"     => $unit_1_no,
          "gps_time_1" => date("Y-m-d H:i:s", strtotime("+7 Hour", strtotime($unit_1_datetime))),
          "unit_2"     => $unit_2_no,
          "gps_time_2" => date("Y-m-d H:i:s", strtotime("+7 Hour", strtotime($unit_2_datetime))),
          "coord_1"    => $unit_1_coord,
          "coord_2"    => $unit_2_coord,
          "meter"      => $d
        );

        $datafixhistory = array();
    		if (sizeof($data) > 0) {
    			$before = 0;

    			for ($i=0; $i < sizeof($data); $i++) {
    						array_push($datafixhistory, array(
    		 				 "gps_id"             => $data[$i][0]->gps_id,
    		 				 "gps_name"           => $data[$i][0]->gps_name,
    		 				 "gps_host"           => $data[$i][0]->gps_host,
    		 				 "gps_speed"          => $data[$i][0]->gps_speed,
    		 				 "gps_status"         => $data[$i][0]->gps_status,
    		 				 "gps_latitude_real"  => $data[$i][0]->gps_latitude_real,
    		 				 "gps_longitude_real" => $data[$i][0]->gps_longitude_real,
    		 				 "gps_time"           => $data[$i][0]->gps_time,
    		 				 "gps_longitude"      => $data[$i][0]->gps_longitude,
    		 				 "gps_latitude"       => $data[$i][0]->gps_latitude,
    		 				 "gps_ew"             => $data[$i][0]->gps_ew,
    		 				 "gps_ns"             => $data[$i][0]->gps_ns
    		 			 ));
    			}

    			// echo "<pre>";
    			// var_dump($datafixhistory);die();
    			// echo "<pre>";

    			$params['mapview']          = $mapview;
    			$params['tableview']        = $tableview;
    			$params['vehicle_no']       = $vehicle_no;
    			$params['vehicle_name']     = $vehicle_name;
    			$params['vehicle_odometer'] = $vehicle_odometer;
    			$params['vehicle_type']     = $vehicle_type;
    			$params['vehicle_user_id']  = $vehicle_user_id;
    			$params['totalgps']         = $trows;
    			$params['datacoordinate']   = json_encode($datafixhistory);
          $params['totalgps']         = $trows;
    			$params['dataradius']       = json_encode($dataradius);
          $params['dataradiustext']   = $dataradius;
    			$params['sdate']            = $sdate;
    			$params['edate']            = $edate;
    			$html                       = $this->load->view("newdashboard/development/report/history/v_result_history", $params, true);

    			$callback['sdate']          = $sdate;
    			$callback['edate']          = $edate;
    			$callback['table']          = $table;
    			$callback['tablehist']      = $tablehist;
          $callback['dataradius']       = $dataradius;
    			$callback['error']          = false;
    			$callback['html']           = $html;
    			echo json_encode($callback);
    		}else {
    			$callback['error']   = true;
    			$callback['message'] = "Data is empty";
    			echo json_encode($callback);
    		}

  		}
  	}

    // HISTORY REPORT WITH RADIUS END

    // HISTORY UNDER BIB START
    function historydata(){
      // ini_set('max_execution_time', '300');
      // set_time_limit(300);
      if (! isset($this->sess->user_type))
      {
        redirect(base_url());
      }

      $privilegecode = $this->sess->user_id_role;

      $mastervehicle       = $this->m_development->getmastervehicleformapsexcaforclear();

      // echo "<pre>";
      // var_dump($mastervehicle);die();
      // echo "<pre>";

      $datafix                         = array();
      $dataexca                        = array();
      $deviceidygtidakada              = array();
      $statusvehicle['engine_on']  = 0;
      $statusvehicle['engine_off'] = 0;
      // LOOP EXCA
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

          if ($mastervehicle[$i]['vehicle_typeunit'] == 0) {
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

      // echo "<pre>";
      // var_dump($datafix);die();
      // echo "<pre>";

      $rows                           = $this->dashboardmodel->getvehicle_report();
      $rows_company                   = $this->get_company_bylevel();
      $this->params["vehicles"]       = $datafix;
      $this->params["rcompany"]       = $rows_company;
      $this->params['code_view_menu'] = "monitoradminonly";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      } elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      } elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      } elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      } elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      } elseif ($privilegecode == 6) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
      } else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/development/report/historyunderbib/v_home_history', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function searchhistory()
  	{

  		include 'class/PHPExcel.php';
  		include 'class/PHPExcel/Writer/Excel2007.php';
      $id                   = $this->input->post("history");
      $vehicledeviceexplode = explode("@", $this->input->post("vehicle"));
      $name                 = $vehicledeviceexplode[0];
      $host                 = $vehicledeviceexplode[1];
      $offset               = 100;

      $startdate = $this->input->post("startdate");
      $shour = $this->input->post("shour");
      $ehour = $this->input->post("ehour");

      $period1 = $startdate." ".$shour.":00";
      $period2 = $startdate." ".$ehour.":59";

      echo "<pre>";
      var_dump($period1.'|'.$period2);die();
      echo "<pre>";



  		$objPHPExcel = new PHPExcel();

  		$isanimate = isset($_POST['isanimate']) && ($_POST['isanimate'] == 1);

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
  		$datatype = isset($_POST['data']) ? $_POST['data'] : 1;

  		$order = $this->config->item("orderhist") ? $this->config->item("orderhist") : "desc";

  		$this->db->where("vehicle_device", $name.'@'.$host);
  		$q = $this->db->get("vehicle");
  		$rowvehicle = $q->row();
  		$vehicle_nopol = $rowvehicle->vehicle_no;
  		$json = json_decode($rowvehicle->vehicle_info);

  		$tyesterday = mktime();//-24*3600;
  		//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK309N")
  		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
  		{

  			$tyesterday = mktime(-7, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));
  			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
  			$t1 = $this->period1 - 7*3600;
  			$t2 = $this->period2 - 7*3600;
  		}
  		else
  		{
  			$tyesterday = mktime(-0, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));
  			$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
  			$t1 = $this->period1 - 0*3600;
  			$t2 = $this->period2 - 0*3600;
  		}

  		$tables = $this->gpsmodel->getTable($rowvehicle);
  		$this->db = $this->load->database($tables["dbname"], TRUE);

  		$params['vehicle'] = $rowvehicle;

  		$isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));

  		if ($_POST['act'] == "export")
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

  		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
  		$tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

  		$totalodometer = 0;
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

  		if ($datatype == 2)
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

  			//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309"&& $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK309N")
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
  		}

  		if ($_POST['act'] == "export")
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

  		$config['total_rows'] = $total;
  		$config['uri_segment'] = 6;
  		$config['per_page'] = $limit;
  		$config['num_links'] = floor($total/$limit);

  		$this->pagination1->initialize($config);

  		$params['uniqid']         = isset($uniqid) ? $uniqid : "";
  		$params['isgtp']          = $isgtp;
  		$params['totalodometer']  = round(($totalodometer+$rowvehicle->vehicle_odometer*1000)/1000);
  		$params['totalodometer1'] = number_format(round($totalodometer1/1000), 0, ".", ",");
  		$params['paging']         = $this->pagination1->create_links();
  		$params['gps_name']       = $name;
  		$params['gps_host']       = $host;
  		$params['offset']         = $offset;
  		$params['data']           = $rows;
  		$params['id']             = $id;
  		$params['ismove']         = $ismove;
  		$html                     = $this->load->view("newdashboard/report/historyunderbib/v_result_history", $params, true);

  		$callback['title']        = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
  		$callback['error']        = false;
  		$callback['html']         = $html;

  		//kembalikan DB ke semula
  		$this->db = $this->load->database("default", TRUE);
  		echo json_encode($callback);


  	}
    // HISTORY UNDER BIB END

    function violation_historikal(){
  		ini_set('max_execution_time', '300');
    	set_time_limit(300);
    	if (! isset($this->sess->user_type))
    	{
    		redirect(base_url());
    	}

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

    	$companyid                   = $this->sess->user_company;
    	$user_dblive                 = $this->sess->user_dblive;
    	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
  		$violationmaster             = $this->m_development->getviolationmaster();

    	$datafix                     = array();
    	$deviceidygtidakada          = array();
    	$statusvehicle['engine_on']  = 0;
    	$statusvehicle['engine_off'] = 0;

    	for ($i=0; $i < sizeof($mastervehicle); $i++) {
    		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
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

  		// GET ROM ROAD
  		$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
  		$this->params['rom_road'] = $romRoad;

  		// echo "<pre>";
    	// var_dump($romRoad);die();
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

    	$this->params['vehicledata']  = $datafix;
    	$this->params['vehicletotal'] = sizeof($mastervehicle);
    	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
    	// echo "<pre>";
    	// var_dump($getvehicle_byowner);die();
    	// echo "<pre>";
    	$totalmobilnya                = sizeof($getvehicle_byowner);
    	if ($totalmobilnya == 0) {
    		$this->params['name']         = "0";
    		$this->params['host']         = "0";
    	}else {
    		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
    		$this->params['name']         = $arr[0];
    		$this->params['host']         = $arr[1];
    	}

    	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
    	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
    	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
    	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
    	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
  		$this->params['violationmaster'] = $violationmaster;

    	// echo "<pre>";
    	// var_dump($this->params['violationmaster']);die();
    	// echo "<pre>";

    	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    		if ($privilegecode == 1) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    		}elseif ($privilegecode == 2) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    		}elseif ($privilegecode == 3) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    		}elseif ($privilegecode == 4) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    		}elseif ($privilegecode == 5) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    		}elseif ($privilegecode == 6) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    		}else {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_tablehistorikal', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    		}
  	}

    function getdatalisthistorikalnew(){
      date_default_timezone_set("Asia/Jakarta");
  		$limitnya	    								 = $_POST['limit_show_data'];
  		$simultantype 								 = $_POST['simultantype'];
  		$last_time_violation 					 = $_POST['last_time_violation'];
  		$contractor 				           = $_POST['contractor'];
  		$violationmasterselect 				 = $_POST['violationmaster'];
  		$alarmtypefromaster            = array();
  		$dataoverspeed 								 = array();
  		$datafatigue                   = array();
  		$dataKmMuatanFix               = array();
  		$dataKmKosonganFix             = array();
  		$violationmix                  = array();

  		$street_onduty = $this->config->item("street_onduty_autocheck");

  		// $street_onduty = array(
  		// 						// "PORT BIB","PORT BIR","PORT TIA",
  		// 						//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
  		// 						// "ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
  		// 						// "ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
  		// 						"ROM B3 ROAD",
  		// 						//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
  		// 						//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",
  		//
  		// 						"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
  		// 						"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
  		// 						"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
  		// 						"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5",
  		//
  		// 						// "BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
  		// 						// "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
  		// 						"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
  		// 						"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
  		// 						"PORT BIB - Antrian","Port BIB - Antrian"
  		// 					);

  		if ($violationmasterselect == 6) {
  			$alarmtypefromaster[] = 9999;
  		}else {
  			if ($violationmasterselect != "0") {
  				$alarmbymaster = $this->m_development->getalarmbytype($violationmasterselect);
  				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
  					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
  				}
  			}
  		}

  		$this->db = $this->load->database("default", TRUE);
  		$this->db->select("user_id, user_dblive");
  		$this->db->order_by("user_id","asc");
  		$this->db->where("user_id", 4408);
  		$q         = $this->db->get("user");
  		$row       = $q->row();
  		$total_row = count($row);

  		$nowtime          = date("Y-m-d H:i:s");
  		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
  		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-30 Minutes"));

  		$sdate        = $last_fiveminutes;
  		$edate          = $nowtime_wita;
  		// $sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
  		// $edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

  		//print_r($sdate." ".$edate);exit();
  		if(count($row)>0){
  			$user_dblive = $row->user_dblive;
  		}

  		// CHOOSE DBTABLE
  		$current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
  		$m1                = date("F", strtotime($current_date));
  		$year              = date("Y", strtotime($current_date));
  		$dbtable           = "";
  		$dbtable_overspeed = "";
  		$report            = "historikal_violation_";
  		$report_overspeed  = "overspeed_hour_";

  		switch ($m1)
  		{
  			case "January":
  						$dbtable           = $report."januari_".$year;
  						$dbtable_overspeed = $report_overspeed."januari_".$year;
  			break;
  			case "February":
  						$dbtable = $report."februari_".$year;
  						$dbtable_overspeed = $report_overspeed."februari_".$year;
  			break;
  			case "March":
  						$dbtable = $report."maret_".$year;
  						$dbtable_overspeed = $report_overspeed."maret_".$year;
  			break;
  			case "April":
  						$dbtable = $report."april_".$year;
  						$dbtable_overspeed = $report_overspeed."april_".$year;
  			break;
  			case "May":
  						$dbtable = $report."mei_".$year;
  						$dbtable_overspeed = $report_overspeed."mei_".$year;
  			break;
  			case "June":
  						$dbtable = $report."juni_".$year;
  						$dbtable_overspeed = $report_overspeed."juni_".$year;
  			break;
  			case "July":
  						$dbtable = $report."juli_".$year;
  						$dbtable_overspeed = $report_overspeed."juli_".$year;
  			break;
  			case "August":
  						$dbtable = $report."agustus_".$year;
  						$dbtable_overspeed = $report_overspeed."agustus_".$year;
  			break;
  			case "September":
  						$dbtable = $report."september_".$year;
  						$dbtable_overspeed = $report_overspeed."september_".$year;
  			break;
  			case "October":
  						$dbtable = $report."oktober_".$year;
  						$dbtable_overspeed = $report_overspeed."oktober_".$year;
  			break;
  			case "November":
  						$dbtable = $report."november_".$year;
  						$dbtable_overspeed = $report_overspeed."november_".$year;
  			break;
  			case "December":
  						$dbtable = $report."desember_".$year;
  						$dbtable_overspeed = $report_overspeed."desember_".$year;
  			break;
  		}

  		$user_level      = $this->sess->user_level;
  		$user_parent     = $this->sess->user_parent;
  		$user_company    = $this->sess->user_company;
  		$user_subcompany = $this->sess->user_subcompany;
  		$user_group      = $this->sess->user_group;
  		$user_subgroup   = $this->sess->user_subgroup;
  		$user_dblive 	   = $this->sess->user_dblive;
  		$privilegecode 	 = $this->sess->user_id_role;
  		$user_id_fix     = $this->sess->user_id;

  		if($privilegecode == 1){
  			$contractor = $contractor;
  		}else if($privilegecode == 2){
  			$contractor = $contractor;
  		}else if($privilegecode == 3){
  			$contractor = $contractor;
  		}else if($privilegecode == 4){
  			$contractor = $contractor;
  		}else if($privilegecode == 5){
  			$contractor = $user_company;
  		}else if($privilegecode == 6){
  			$contractor = $user_company;
  		}else if($privilegecode == 0){
  			$contractor = $contractor;
  		}else{
  			$contractor = $contractor;
  		}

  		$data_array_alert = array();
  		// $limit            = ($limitnya/2);
  		$data_overspeed   = $this->m_development->get_overspeed_intensor($dbtable_overspeed, $limitnya, $contractor, $sdate, $edate);

      // echo "<pre>";
      // var_dump($data_overspeed);die();
      // echo "<pre>";

  		for ($i=0; $i < sizeof($data_overspeed); $i++) {
  			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
  			array_push($data_array_alert, array(
  				"isfatigue"          => "no",
  				"jalur_name"         => $data_overspeed[$i]['overspeed_report_jalur'],
  				"vehicle_no"         => $data_overspeed[$i]['overspeed_report_vehicle_no'],
  				"vehicle_name"       => $data_overspeed[$i]['overspeed_report_vehicle_name'],
  				"vehicle_company"    => $data_overspeed[$i]['overspeed_report_vehicle_company'],
  				"vehicle_device"     => $data_overspeed[$i]['overspeed_report_vehicle_device'],
  				"vehicle_mv03"       => "",
  				"gps_alert" 				 => "Overspeed",
  				"violation" 				 => "Overspeed",
  				"violation_level" 	 => $data_overspeed[$i]['overspeed_report_level_alias'],
  				"violation_type" 		 => "overspeed",
  				"gps_latitude_real"  => $coordinate[0],
  				"gps_longitude_real" => $coordinate[1],
  				"gps_speed"          => $data_overspeed[$i]['overspeed_report_speed'],
  				"gps_speed_limit"    => $data_overspeed[$i]['overspeed_report_geofence_limit'],
  				"gps_time"           => date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_gps_time'])),
  				"geofence"           => $data_overspeed[$i]['overspeed_report_geofence_name'],
  				"position"           => $data_overspeed[$i]['overspeed_report_location'],
  			));
  		}

  			// echo "<pre>";
  			// var_dump($data_array_alert);die();
  			// echo "<pre>";

  				$nowtime      = date("Y-m-d H:i:s");
  				$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
  				$sdate        = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-30 Minutes"));
  				// $limit        = ($limitnya/2);

  		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
  		$masterviolation   = $this->m_development->getviolationhistorikal_type2($dbtable, $limitnya, $contractor, $alarmtypefromaster, $sdate, $nowtime_wita);

      // echo "<pre>";
      // var_dump($dbtable.'-'.$limitnya.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$nowtime_wita);die();
      // echo "<pre>";

  			if (sizeof($masterviolation) > 0) {
  					for ($j=0; $j < sizeof($masterviolation); $j++) {
  						if ($masterviolation[$j]['violation_fatigue'] != "") {
  							$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
  							$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
  							$forcheck_gps_time       = $json_fatigue[0]->gps_time;
  							$checkthis               = $this->m_development->getfrommaster($forcheck_vehicledevice);
  							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
  							// $jalurname               = $jsonautocheck->auto_last_road;
  							$jalurname               = $masterviolation[$j]['violation_jalur'];

  							$positionforfilter = $masterviolation[$j]['violation_position'];
  								if ($positionforfilter != "") {
  									// UNTUK UMUM START
  									if (in_array($positionforfilter, $street_onduty)){
  										$alarmreportnamefix = "";
  										$alarmreporttype = $json_fatigue[0]->gps_alertid;
  											if ($alarmreporttype == 626) {
  												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
  											}elseif ($alarmreporttype == 627) {
  												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
  											}else {
  												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
  											}

  											if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
  												$violation_split = explode("Level", $alarmreportnamefix);
  												$violationlevel  = str_replace("Start", "", $violation_split[1]);
  												if ($violationlevel == "One") {
  													$violationfix = "1";
  												}else {
  													$violationfix = "2";
  												}

  												// echo "<pre>";
  												// var_dump($violation_split);die();
  												// echo "<pre>";

  												array_push($data_array_alert, array(
  													 "isfatigue"               => "yes",
  													 "jalur_name"              => $jalurname,
  													 "vehicle_no"              => $json_fatigue[0]->vehicle_no,
  													 "vehicle_name"            => $json_fatigue[0]->vehicle_name,
  													 "vehicle_company"         => $json_fatigue[0]->vehicle_company,
  													 "vehicle_device"          => $json_fatigue[0]->vehicle_device,
  													 "vehicle_mv03"            => $json_fatigue[0]->vehicle_mv03,
  													 "gps_alert"               => $alarmreportnamefix,
  													 "violation" 				       => $violation_split[0],
  													 "violation_level" 				 => "Level " . $violationfix,
  													 "violation_type" 		     => "not_overspeed",
  													 "gps_time"                => $json_fatigue[0]->gps_time,
  													 "auto_last_update"        => $jsonautocheck->auto_last_update,
  													 "auto_last_check"         => $jsonautocheck->auto_last_check,
  													 "gps_latitude_real"       => $json_fatigue[0]->gps_latitude_real,
  													 "gps_longitude_real"      => $json_fatigue[0]->gps_longitude_real,
  													 "position"                => $masterviolation[$j]['violation_position'],
  													 "auto_last_position"      => $jsonautocheck->auto_last_position,
  													 "gps_speed"               => $json_fatigue[0]->gps_speed,
  												));
  											}
  									}
  									// UNTUK UMUM END
  								}
  					}
  				}

  				$lasttime     = $masterviolation[0]['violation_update'];
  				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
  			}else {
  				$lasttime = $sdate;
  			}

  			 usort($data_array_alert, function($a, $b) {
  			    return strtotime($b['gps_time']) - strtotime($a['gps_time']);
  			});

  			// $violationmix = $this->aasort($data_array_alert, "gps_time");
  			$data_array_fix = array();
  			if($violationmasterselect == 6) {
  				for ($i=0; $i < sizeof($data_array_alert); $i++) {
  				$violation_type = $data_array_alert[$i]['violation_type'];
  					if ($violation_type == "overspeed") {
  						array_push($data_array_fix, array(
  							"isfatigue"                => $data_array_alert[$i]['isfatigue'],
  							"jalur_name"               => $data_array_alert[$i]['jalur_name'],
  							"vehicle_no"               => $data_array_alert[$i]['vehicle_no'],
  							"vehicle_name"             => $data_array_alert[$i]['vehicle_name'],
  							"vehicle_company"          => $data_array_alert[$i]['vehicle_company'],
  							"vehicle_device"           => $data_array_alert[$i]['vehicle_device'],
  							"vehicle_mv03"             => $data_array_alert[$i]['vehicle_mv03'],
  							"gps_alert" 				       => $data_array_alert[$i]['gps_alert'],
  							"violation" 				       => $data_array_alert[$i]['violation'],
  							"violation_level" 				 => $data_array_alert[$i]['violation_level'],
  							"violation_type" 		       => $data_array_alert[$i]['violation_type'],
  							"gps_latitude_real"        => $data_array_alert[$i]['gps_latitude_real'],
  							"gps_longitude_real"       => $data_array_alert[$i]['gps_longitude_real'],
  							"gps_speed"                => $data_array_alert[$i]['gps_speed'],
  							"gps_speed_limit"          => $data_array_alert[$i]['gps_speed_limit'],
  							"gps_time"                 => $data_array_alert[$i]['gps_time'],
  							"geofence"                 => $data_array_alert[$i]['geofence'],
  							"position"                 => $data_array_alert[$i]['position'],
  						));
  					}
  				}
  			}elseif($violationmasterselect != "0") {
  				for ($i=0; $i < sizeof($data_array_alert); $i++) {
  					$violation_type = $data_array_alert[$i]['violation_type'];
  						if ($violation_type == "not_overspeed") {
  							array_push($data_array_fix, array(
  								"isfatigue"          => $data_array_alert[$i]['isfatigue'],
  								"jalur_name"         => $data_array_alert[$i]['jalur_name'],
  								"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
  								"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
  								"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
  								"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
  								"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
  								"gps_alert"          => $data_array_alert[$i]['gps_alert'],
  								"violation" 				 => $data_array_alert[$i]['violation'],
  								"violation_level" 	 => $data_array_alert[$i]['violation_level'],
  								"violation_type" 		 => $data_array_alert[$i]['violation_type'],
  								"gps_time"           => $data_array_alert[$i]['gps_time'],
  								"auto_last_update"   => $data_array_alert[$i]['auto_last_update'],
  								"auto_last_check"    => $data_array_alert[$i]['auto_last_check'],
  								"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
  								"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
  								"position"           => $data_array_alert[$i]['position'],
  								"auto_last_position" => $data_array_alert[$i]['auto_last_position'],
  								"gps_speed"          => $data_array_alert[$i]['gps_speed'],
  							));
  						}
  				}
  			}else {
  				$data_array_fix = $data_array_alert;
  			}

  			// echo "<pre>";
  			// var_dump($data_array_alert);die();
  			// echo "<pre>";

  				echo json_encode(array(
  					"msg"                         => "success",
  					"code"                        => 200,
            "showper"                     => $limitnya,

  					// "lasttime"                 => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
  					// "lasttime"                 => $startdate,
  					"simultantype"                => 1,
  					"violationmix"                => $data_array_fix,
  					"alarmtypefromaster" 			    => sizeof($alarmtypefromaster)
  					// "total_ov"			            => $totaldataoverspeedfix,
  					// "tv_call"                  => $tv_call,
  					// "tv_cardistance"           => $tv_cardistance,
  					// "tv_distracted"            => $tv_distracted,
  					// "tv_fatigue"               => $tv_fatigue,
  					// "tv_smoking"               => $tv_smoking,
  					// "tv_driverabnormal"        => $tv_driverabnormal,
  					// "total_violationall"	      => $total_violationall,
  					// "violation_call"           => $violation_call,
  					// "violation_cardistance"    => $violation_cardistance,
  					// "violation_distracted"     => $violation_distracted,
  					// "violation_fatigue"        => $violation_fatigue,
  					// "violation_smoking"        => $violation_smoking,
  					// "violation_driverabnormal" => $violation_driverabnormal,
  					// "dataRomFix"               => $dataRomFix
  				));
    }

    // DEVELOPMENT HISTORI MDVR START
    function mdvrreport(){
      $privilegecode   = $this->sess->user_id_role;

      $rows           = $this->m_development->getdevice();

      // echo "<pre>";
    	// var_dump($rows);die();
    	// echo "<pre>";

      $rows_company                   = $this->get_company();
      $this->params["vehicles"]       = $rows;
      $this->params["vehicledata"]       = $rows;
      $this->params["rcompany"]       = $rows_company;
      $this->params['code_view_menu'] = "report";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/v_home_historimdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      } elseif ($privilegecode == 2) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/v_home_historimdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      } elseif ($privilegecode == 3) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/v_home_historimdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      } elseif ($privilegecode == 4) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/v_home_historimdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      } elseif ($privilegecode == 5) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/v_home_historimdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      } else {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/v_home_historimdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function search_devicereport(){
      $privilegecode   = $this->sess->user_id_role;
        if ($privilegecode == 5 || $privilegecode == 6) {
          $company = $this->sess->user_company;
        }else {
          $company          = $this->input->post("company");
        }

      $vehicle          = $this->input->post("vehicle");
      $frekuensianomali = $this->input->post("frekuensianomali");
      $startdate        = $this->input->post("startdate");
      // $shour         = "00:00:00";
      $enddate          = $this->input->post("enddate");
      // $ehour         = "23:59:59";
      $periode          = $this->input->post("periode");

      // $sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      // $edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

      $sdate2 = date("Y-m-d H:i:s", strtotime($startdate));
      $edate2 = date("Y-m-d H:i:s", strtotime($startdate));

      $nowdate   = date("Y-m-d");
      $nowday    = date("d");
      $nowmonth  = date("m");
      $nowyear   = date("Y");
      $lastday   = date("t");

        if($periode == "custom"){
          $sdate = date("Y-m-d", strtotime($startdate));
          $edate = date("Y-m-d", strtotime($enddate));
        }else if($periode == "yesterday"){
          $sdate = date("Y-m-d", strtotime("yesterday"));
          $edate = date("Y-m-d", strtotime("yesterday"));
        }else if($periode == "last7"){
          $sdate = date("Y-m-d", strtotime($sdate2 . "-7days"));
          $edate = date("Y-m-d", strtotime($startdate));
        }else if($periode == "last30"){
          $sdate = date("Y-m-01", strtotime($startdate));
          $edate = date("Y-m-d", strtotime($startdate));
        }else if($periode == "today"){
          $sdate1 = date("Y-m-d");
          $sdate2 = "00:00:00";

          $edate1 = date("Y-m-d");
          $edate2 = "23:59:59";

          $sdate = $sdate1;
          $edate = $edate1;
        }else{
          $sdate = date("Y-m-d", strtotime($startdate));
          $edate = date("Y-m-d", strtotime($enddate));
        }

      $m1     = date("F", strtotime($sdate));
      $year   = date("Y", strtotime($sdate));
      $report = "report_device_status_summary_";

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

        $data_company = $this->get_company_bylevel();

      // echo "<pre>";
      // var_dump($dbtable.'-'.$company.'-'.$vehicle.'-'.$frekuensianomali.'-'.$sdate.'-'.$edate);die();
      // echo "<pre>";

      $data_summary = $this->m_development->getdatasummarymdvr($dbtable, $company, $vehicle, $frekuensianomali, $sdate, $edate);
      // $dbtable.'-'.$company.'-'.$vehicle.'-'.$frekuensianomali.'-'.$sdate.'-'.$edate
      // echo "<pre>";
      // var_dump($data_company);die();
      // echo "<pre>";

      $this->params['data']         = $data_summary;
      $this->params['rcompany'] = $data_company;

      $html = $this->load->view("newdashboard/development/v_historimdvr_result", $this->params, true);
      $callback['error'] = false;
      $callback['html']  = $html;
      $callback['data']  = $data_summary;
      echo json_encode($callback);
    }
    // DEVELOPMENT HISTORI MDVR END

    function get_vehicle_by_company_with_numberorder($id)
  	{
  		if (!isset($this->sess->user_type)) {
  			redirect(base_url());
  		}

  		$this->db->order_by("vehicle_no", "asc");
  		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
  		$this->db->where("vehicle_company", $id);
  		if ($this->sess->user_group > 0) {
  			$this->db->where("vehicle_group", $this->sess->user_group);
  		}
  		$this->db->where("vehicle_status <>", 3);
  		$this->db->join("company", "vehicle_company = company_id", "left");
  		$qd = $this->db->get("vehicle");
  		$rd = $qd->result();

  		if ($qd->num_rows() > 0) {
  			$options = "<option value='all' selected='selected' >--All Vehicle--</option>";
  			$i = 1;
  			foreach ($rd as $obj) {
  				$options .= "<option value='" . $obj->vehicle_no . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
  				$i++;
  			}

  			echo $options;
  			return;
  		}
  	}

    function get_vehicle_by_company_with_vdevice($id)
  	{
  		if (!isset($this->sess->user_type)) {
  			redirect(base_url());
  		}

  		$this->db->order_by("vehicle_no", "asc");
  		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
      $this->db->where("vehicle_company", $id);
  		if ($this->sess->user_group > 0) {
  			$this->db->where("vehicle_group", $this->sess->user_group);
  		}
  		$this->db->where("vehicle_status <>", 3);
  		$this->db->join("company", "vehicle_company = company_id", "left");
  		$qd = $this->db->get("vehicle");
  		$rd = $qd->result();

  		if ($qd->num_rows() > 0) {
  			$options = "<option value='all' selected='selected' >--All Vehicle--</option>";
  			$i = 1;
  			foreach ($rd as $obj) {
  				$options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
  				$i++;
  			}

  			echo $options;
  			return;
  		}
  	}

    function get_vehicle_by_company_with_vdevice_2($id)
  	{
  		if (!isset($this->sess->user_type)) {
  			redirect(base_url());
  		}

      $this->db = $this->load->database("default", TRUE);
  		$this->db->order_by("vehicle_no", "asc");
  		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
      if ($id != 0 || $id != "All" || $id != "all") {
        $this->db->where("vehicle_company", $id);
      }
  		if ($this->sess->user_group > 0) {
  			$this->db->where("vehicle_group", $this->sess->user_group);
  		}
  		$this->db->where("vehicle_status <>", 3);
      if ($id != 0 || $id != "All" || $id != "all") {
        $this->db->join("company", "vehicle_company = company_id", "left");
      }
  		$qd = $this->db->get("vehicle");
  		$rd = $qd->result();

      // echo "<pre>";
      // var_dump($rd);die();
      // echo "<pre>";

  		if ($qd->num_rows() > 0) {
  			$options = "<option value='all' selected='selected' >--All Vehicle--</option>";
  			$i = 1;
  			foreach ($rd as $obj) {
  				$options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
  				$i++;
  			}

  			echo $options;
  			return;
  		}
  	}

    function vehicleByContractor(){
    	$user_id         = $this->sess->user_id;
    	$user_parent     = $this->sess->user_parent;
    	$privilegecode   = $this->sess->user_id_role;
    	$user_company    = $this->sess->user_company;
    	$companyid       = $this->input->post('companyid');
    	$valueMapsOption = $this->input->post('valuemapsoption');

    	$this->db->select("*");
    		if ($companyid == 0 || $companyid == "all") {
    			if ($privilegecode == 0) {
    				$this->db->where("vehicle_user_id", $user_id);
    			}elseif ($privilegecode == 1) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 2) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 3) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 4) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 5) {
    				$this->db->where("vehicle_company", $user_company);
    			}elseif ($privilegecode == 6) {
    				$this->db->where("vehicle_company", $user_company);
    			}
    		}else {
    			$this->db->where("vehicle_company", $companyid);
    		}

        $this->db->where("vehicle_mv03 !=", "0000");
        // $this->db->where_in("vehicle_type", array("MV03"));
        $this->db->where("vehicle_status <>", 3);
    	$this->db->order_by("vehicle_no", "ASC");
    	$q    = $this->db->get("vehicle");
    	$rows = $q->result_array();

    	if ($valueMapsOption == 1) {
    		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    		$datavehicle       = array();

    			for ($i=0; $i < sizeof($rows); $i++) {
    				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
    						array_push($datavehicle, array(
    							"vehicle_id"     => $rows[$i]['vehicle_id'],
    							"vehicle_no"     => $rows[$i]['vehicle_no'],
    							"vehicle_name"   => $rows[$i]['vehicle_name'],
    							"vehicle_device" => $rows[$i]['vehicle_device'],
    							"auto_last_lat"  => $autocheck->auto_last_lat,
    							"auto_last_long" => $autocheck->auto_last_long
    						));
    			}
    			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
    	}else {
    		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
    	}

    	// echo "<pre>";
    	// var_dump($datavehicle);die();
    	// echo "<pre>";

    }
    // DEVELOPMENT HISTORI MDVR END

    // VIOLATION TABLE HISTORIKAL REPORT START
    function violation_historikalreport(){
      ini_set('display_errors', 1);
      ini_set('memory_limit', '6G');
  		ini_set('max_execution_time', '3600');
    	if (! isset($this->sess->user_type))
    	{
    		redirect(base_url());
    	}

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
    		$user_id_fix = $user_company;
    	}elseif ($privilegecode == 6) {
    		$user_id_fix = $user_company;
    	}else{
    		$user_id_fix = $user_id;
    	}

    	$companyid                   = $this->sess->user_company;
    	$user_dblive                 = $this->sess->user_dblive;
    	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
  		$violationmaster             = $this->m_development->getviolationmaster();

    	$datafix                     = array();
    	$deviceidygtidakada          = array();
    	$statusvehicle['engine_on']  = 0;
    	$statusvehicle['engine_off'] = 0;

    	for ($i=0; $i < sizeof($mastervehicle); $i++) {
    		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);

        if (isset($jsonautocheck->auto_status)) {
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

  		// GET ROM ROAD
  		$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
  		$this->params['rom_road'] = $romRoad;

  		// echo "<pre>";
    	// var_dump($romRoad);die();
    	// echo "<pre>";

    	$this->params['url_code_view']  = "1";
    	$this->params['code_view_menu'] = "report";
    	$this->params['maps_code']      = "morehundred";

    	$this->params['engine_on']      = $statusvehicle['engine_on'];
    	$this->params['engine_off']     = $statusvehicle['engine_off'];


    	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

    	$datastatus                     = explode("|", $rstatus);
    	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
    	$this->params['total_vehicle']  = $datastatus[3];
    	$this->params['total_offline']  = $datastatus[2];

    	$this->params['vehicles']  = $datafix;
    	$this->params['vehicletotal'] = sizeof($mastervehicle);
    	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
    	// echo "<pre>";
    	// var_dump($getvehicle_byowner);die();
    	// echo "<pre>";
    	$totalmobilnya                = sizeof($getvehicle_byowner);
    	if ($totalmobilnya == 0) {
    		$this->params['name']         = "0";
    		$this->params['host']         = "0";
    	}else {
    		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
    		$this->params['name']         = $arr[0];
    		$this->params['host']         = $arr[1];
    	}

    	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
    	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
    	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
    	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
    	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
  		$this->params['violationmaster'] = $violationmaster;

    	// echo "<pre>";
    	// var_dump($this->params['vehicles']);die();
    	// echo "<pre>";

    	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    		if ($privilegecode == 1) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    		}elseif ($privilegecode == 2) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    		}elseif ($privilegecode == 3) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    		}elseif ($privilegecode == 4) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    		}elseif ($privilegecode == 5) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    		}elseif ($privilegecode == 6) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    		}else {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/violation/v_home_violationhistorikal_report', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    		}
  	}

    function search_violationtablehistorikal(){
      ini_set('display_errors', 1);
      ini_set('memory_limit', '6G');
  		ini_set('max_execution_time', '1000');
      $company               = $this->input->post("company");
      $vehicle               = $this->input->post("vehicle");
      $violationmasterselect = $this->input->post("violationmasterselect");
      $startdate             = $this->input->post("startdate");
    	$shour                 = "00:00:00";
    	$enddate               = $this->input->post("enddate");
    	$ehour                 = "23:59:59";
      $periode               = $this->input->post("periode");
      $alarmtypefromaster    = array();


      $nowdate    = date("Y-m-d");
      $nowday     = date("d");
      $nowmonth   = date("m");
      $nowyear    = date("Y");
      $lastday    = date("t");

      if($periode == "custom"){
        $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
        $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
      }else if($periode == "yesterday"){

        $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

      }else if($periode == "last7"){
        $nowday = $nowday - 1;
        $firstday = $nowday - 7;
        if($nowday <= 7){
          $firstday = 1;
        }

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

      $street_onduty = $this->config->item("street_onduty_autocheck");

      // $data_historikal = $this->m_development->get_data_historikal($dbtable, $company, $vehicle, $violationmasterselect, $sdate, $edate);
      if ($violationmasterselect == 6) {
  			$alarmtypefromaster[] = 9999;
  		}else {
  			if ($violationmasterselect != "all") {
  				$alarmbymaster = $this->m_development->getalarmbytype($violationmasterselect);
  				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
  					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
  				}
  			}
  		}

  		$this->db = $this->load->database("default", TRUE);
  		$this->db->select("user_id, user_dblive");
  		$this->db->order_by("user_id","asc");
  		$this->db->where("user_id", 4408);
  		$q         = $this->db->get("user");
  		$row       = $q->row();
  		$total_row = count($row);

  		//print_r($sdate." ".$edate);exit();
  		if(count($row)>0){
  			$user_dblive = $row->user_dblive;
  		}

  		// CHOOSE DBTABLE
  		// $current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
  		$m1                = date("F", strtotime($sdate));
  		$year              = date("Y", strtotime($sdate));
  		$dbtable           = "";
  		$dbtable_overspeed = "";
  		$report            = "historikal_violation_";
  		$report_overspeed  = "overspeed_hour_";

  		switch ($m1)
  		{
  			case "January":
  						$dbtable           = $report."januari_".$year;
  						$dbtable_overspeed = $report_overspeed."januari_".$year;
  			break;
  			case "February":
  						$dbtable = $report."februari_".$year;
  						$dbtable_overspeed = $report_overspeed."februari_".$year;
  			break;
  			case "March":
  						$dbtable = $report."maret_".$year;
  						$dbtable_overspeed = $report_overspeed."maret_".$year;
  			break;
  			case "April":
  						$dbtable = $report."april_".$year;
  						$dbtable_overspeed = $report_overspeed."april_".$year;
  			break;
  			case "May":
  						$dbtable = $report."mei_".$year;
  						$dbtable_overspeed = $report_overspeed."mei_".$year;
  			break;
  			case "June":
  						$dbtable = $report."juni_".$year;
  						$dbtable_overspeed = $report_overspeed."juni_".$year;
  			break;
  			case "July":
  						$dbtable = $report."juli_".$year;
  						$dbtable_overspeed = $report_overspeed."juli_".$year;
  			break;
  			case "August":
  						$dbtable = $report."agustus_".$year;
  						$dbtable_overspeed = $report_overspeed."agustus_".$year;
  			break;
  			case "September":
  						$dbtable = $report."september_".$year;
  						$dbtable_overspeed = $report_overspeed."september_".$year;
  			break;
  			case "October":
  						$dbtable = $report."oktober_".$year;
  						$dbtable_overspeed = $report_overspeed."oktober_".$year;
  			break;
  			case "November":
  						$dbtable = $report."november_".$year;
  						$dbtable_overspeed = $report_overspeed."november_".$year;
  			break;
  			case "December":
  						$dbtable = $report."desember_".$year;
  						$dbtable_overspeed = $report_overspeed."desember_".$year;
  			break;
  		}

  		$user_level      = $this->sess->user_level;
  		$user_parent     = $this->sess->user_parent;
  		$user_company    = $this->sess->user_company;
  		$user_subcompany = $this->sess->user_subcompany;
  		$user_group      = $this->sess->user_group;
  		$user_subgroup   = $this->sess->user_subgroup;
  		$user_dblive 	   = $this->sess->user_dblive;
  		$privilegecode 	 = $this->sess->user_id_role;
  		$user_id_fix     = $this->sess->user_id;

  		if($privilegecode == 1){
  			$contractor = $company;
  		}else if($privilegecode == 2){
  			$contractor = $company;
  		}else if($privilegecode == 3){
  			$contractor = $company;
  		}else if($privilegecode == 4){
  			$contractor = $company;
  		}else if($privilegecode == 5){
  			$contractor = $user_company;
  		}else if($privilegecode == 6){
  			$contractor = $user_company;
  		}else if($privilegecode == 0){
  			$contractor = $company;
  		}else{
  			$contractor = $company;
  		}

      // echo "<pre>";
      // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
      // echo "<pre>";

  		$data_array_alert = array();
  		$data_overspeed   = $this->m_development->get_overspeed_intensor_historikal($dbtable_overspeed, $vehicle, $contractor, $sdate, $edate);

      // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

        // echo "<pre>";
        // var_dump($data_overspeed);die();
        // // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
        // echo "<pre>";

  		for ($i=0; $i < sizeof($data_overspeed); $i++) {
  			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
  			array_push($data_array_alert, array(
  				"isfatigue"          => "no",
  				"jalur_name"         => $data_overspeed[$i]['overspeed_report_jalur'],
  				"vehicle_no"         => $data_overspeed[$i]['overspeed_report_vehicle_no'],
  				"vehicle_name"       => $data_overspeed[$i]['overspeed_report_vehicle_name'],
  				"vehicle_company"    => $data_overspeed[$i]['overspeed_report_vehicle_company'],
  				"vehicle_device"     => $data_overspeed[$i]['overspeed_report_vehicle_device'],
  				"vehicle_mv03"       => "",
          "status_sent_tele"   => "",
  				"gps_alert" 				 => "Overspeed",
  				"violation" 				 => "Overspeed",
  				"violation_level" 	 => $data_overspeed[$i]['overspeed_report_level_alias'],
  				"violation_type" 		 => "overspeed",
  				"gps_latitude_real"  => $coordinate[0],
  				"gps_longitude_real" => $coordinate[1],
  				"gps_speed"          => $data_overspeed[$i]['overspeed_report_speed'],
  				"gps_speed_limit"    => $data_overspeed[$i]['overspeed_report_geofence_limit'],
  				"gps_time"           => date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_gps_time'])),
  				"geofence"           => $data_overspeed[$i]['overspeed_report_geofence_name'],
  				"position"           => $data_overspeed[$i]['overspeed_report_location'],
  			));
  		}



        // $dbtable = "historikal_violation_desember_2022_testindex";
  			// echo "<pre>";
  			// // var_dump($data_array_alert);die();
        // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$edate);die();
        // // var_dump($alarmtypefromaster);die();
  			// echo "<pre>";

  		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
  		$masterviolation   = $this->m_development->getviolationhistorikal_type2_report($dbtable, $vehicle, $contractor, $alarmtypefromaster, $sdate, $edate);

      // echo "<pre>";
      // var_dump($masterviolation);die();
      // echo "<pre>";

  			if (sizeof($masterviolation) > 0) {
  					for ($j=0; $j < sizeof($masterviolation); $j++) {
  						if ($masterviolation[$j]['violation_fatigue'] != "") {
  							$positionforfilter = $masterviolation[$j]['violation_position'];
  								if ($positionforfilter != "") {
  									// UNTUK UMUM START
  									// if (in_array($positionforfilter, $street_onduty)){
                      $json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
                      $forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
                      $forcheck_gps_time       = $json_fatigue[0]->gps_time;
                      // $checkthis               = $this->m_development->getfrommaster($forcheck_vehicledevice);
                      // echo "<pre>";
                      // var_dump($json_fatigue);die();
                      // echo "<pre>";
                      // $jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
                      // $jalurname               = $jsonautocheck->auto_last_road;
                      $jalurname               = $masterviolation[$j]['violation_jalur'];
  										$alarmreportnamefix = "";
  										$alarmreporttype = $json_fatigue[0]->gps_alertid;
  											if ($alarmreporttype == 626) {
  												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
  											}elseif ($alarmreporttype == 627) {
  												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
  											}else {
  												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
  											}

  											// if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
  												// $violation_split = explode("Level", $alarmreportnamefix);
  												// $violationlevel  = str_replace("Start", "", $violation_split[1]);
  												// if ($violationlevel == "One") {
  												// 	$violationfix = "1";
  												// }else {
  												// 	$violationfix = "2";
  												// }

                          $violation_split = explode("Level", $alarmreportnamefix);
                          // $violationlevel  = explode("Alarm", "", $alarmreportnamefix);
                          if (strpos($alarmreportnamefix, "One")) {
                            $violationfix = "1";
                          }else {
                            $violationfix = "2";
                          }

  												// echo "<pre>";
  												// var_dump($violation_split);die();
  												// echo "<pre>";

  												array_push($data_array_alert, array(
  													 "isfatigue"               => "yes",
                             "status_sent_tele"        => $masterviolation[$j]['violation_status_tele'],
  													 "jalur_name"              => $jalurname,
  													 "vehicle_no"              => $json_fatigue[0]->vehicle_no,
  													 "vehicle_name"            => $json_fatigue[0]->vehicle_name,
  													 "vehicle_company"         => $json_fatigue[0]->vehicle_company,
  													 "vehicle_device"          => $json_fatigue[0]->vehicle_device,
  													 "vehicle_mv03"            => $json_fatigue[0]->vehicle_mv03,
  													 "gps_alert"               => $alarmreportnamefix,
  													 "violation" 				       => $violation_split[0],
  													 "violation_level" 				 => "Level " . $violationfix,
  													 "violation_type" 		     => "not_overspeed",
  													 "gps_time"                => $json_fatigue[0]->gps_time,
  													 "gps_latitude_real"       => $json_fatigue[0]->gps_latitude_real,
  													 "gps_longitude_real"      => $json_fatigue[0]->gps_longitude_real,
  													 "position"                => $masterviolation[$j]['violation_position'],
  													 // "auto_last_position"      => $jsonautocheck->auto_last_position,
  													 "gps_speed"               => $json_fatigue[0]->gps_speed,
  												));
  											// }
  									// }
  									// UNTUK UMUM END
  								}
  					}
  				}

  				$lasttime     = $masterviolation[0]['violation_update'];
  				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
  			}else {
  				$lasttime = $sdate;
  			}

  			 usort($data_array_alert, function($a, $b) {
  			    return strtotime($b['gps_time']) - strtotime($a['gps_time']);
  			});

  			// $violationmix = $this->aasort($data_array_alert, "gps_time");
  			$data_array_fix = array();
  			if($violationmasterselect == 6) {
  				for ($i=0; $i < sizeof($data_array_alert); $i++) {
  				$violation_type = $data_array_alert[$i]['violation_type'];
  					if ($violation_type == "overspeed") {
  						array_push($data_array_fix, array(
  							"isfatigue"                => $data_array_alert[$i]['isfatigue'],
                "status_sent_tele"         => $data_array_alert[$i]['status_sent_tele'],
  							"jalur_name"               => $data_array_alert[$i]['jalur_name'],
  							"vehicle_no"               => $data_array_alert[$i]['vehicle_no'],
  							"vehicle_name"             => $data_array_alert[$i]['vehicle_name'],
  							"vehicle_company"          => $data_array_alert[$i]['vehicle_company'],
  							"vehicle_device"           => $data_array_alert[$i]['vehicle_device'],
  							"vehicle_mv03"             => $data_array_alert[$i]['vehicle_mv03'],
  							"gps_alert" 				       => $data_array_alert[$i]['gps_alert'],
  							"violation" 				       => $data_array_alert[$i]['violation'],
  							"violation_level" 				 => $data_array_alert[$i]['violation_level'],
  							"violation_type" 		       => $data_array_alert[$i]['violation_type'],
  							"gps_latitude_real"        => $data_array_alert[$i]['gps_latitude_real'],
  							"gps_longitude_real"       => $data_array_alert[$i]['gps_longitude_real'],
  							"gps_speed"                => $data_array_alert[$i]['gps_speed'],
  							"gps_speed_limit"          => $data_array_alert[$i]['gps_speed_limit'],
  							"gps_time"                 => $data_array_alert[$i]['gps_time'],
  							"geofence"                 => $data_array_alert[$i]['geofence'],
  							"position"                 => $data_array_alert[$i]['position'],
  						));
  					}
  				}
  			}elseif($violationmasterselect != "0") {
  				for ($i=0; $i < sizeof($data_array_alert); $i++) {
  					$violation_type = $data_array_alert[$i]['violation_type'];
  						if ($violation_type == "not_overspeed") {
  							array_push($data_array_fix, array(
  								"isfatigue"          => $data_array_alert[$i]['isfatigue'],
                  "status_sent_tele"   => $data_array_alert[$i]['status_sent_tele'],
  								"jalur_name"         => $data_array_alert[$i]['jalur_name'],
  								"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
  								"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
  								"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
  								"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
  								"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
  								"gps_alert"          => $data_array_alert[$i]['gps_alert'],
  								"violation" 				 => $data_array_alert[$i]['violation'],
  								"violation_level" 	 => $data_array_alert[$i]['violation_level'],
  								"violation_type" 		 => $data_array_alert[$i]['violation_type'],
  								"gps_time"           => $data_array_alert[$i]['gps_time'],
  								// "auto_last_update"   => $data_array_alert[$i]['auto_last_update'],
  								// "auto_last_check"    => $data_array_alert[$i]['auto_last_check'],
  								"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
  								"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
  								"position"           => $data_array_alert[$i]['position'],
  								// "auto_last_position" => $data_array_alert[$i]['auto_last_position'],
  								"gps_speed"          => $data_array_alert[$i]['gps_speed'],
  							));
  						}
  				}
  			}else {
  				$data_array_fix = $data_array_alert;
  			}

      // echo "<pre>";
      // var_dump($data_array_fix);die();
      // echo "<pre>";

      $this->params['data']     = $data_array_fix;
      $rows_company             = $this->get_company();
      $this->params["rcompany"] = $rows_company;

      $html = $this->load->view("newdashboard/development/violation/v_violationhistorikal_reportresult", $this->params, true);
      $callback['error'] = false;
      $callback['html']  = $html;
      $callback['data']  = $data_array_fix;
      echo json_encode($callback);
    }

    function search_violationtablehistorikalphpexcel(){
      ini_set('display_errors', 1);
      ini_set('memory_limit', '6G');
  		ini_set('max_execution_time', '3600');
      $company               = $this->input->post("company");
      $vehicle               = $this->input->post("vehicle");
      $violationmasterselect = $this->input->post("violationmasterselect");
      $startdate             = $this->input->post("startdate");
    	$shour                 = "00:00:00";
    	$enddate               = $this->input->post("enddate");
    	$ehour                 = "23:59:59";
      $periode               = $this->input->post("periode");
      $alarmtypefromaster    = array();

      // echo "<pre>";
      // var_dump($company.'-'.$vehicle.'-'.$violationmasterselect.'-'.$startdate.'-'.$enddate.'-'.$periode);die();
      // echo "<pre>";


      $nowdate    = date("Y-m-d");
      $nowday     = date("d");
      $nowmonth   = date("m");
      $nowyear    = date("Y");
      $lastday    = date("t");

      if($periode == "custom"){
        $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
        $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
      }else if($periode == "yesterday"){

        $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

      }else if($periode == "last7"){
        $nowday = $nowday - 1;
        $firstday = $nowday - 7;
        if($nowday <= 7){
          $firstday = 1;
        }

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

      $street_onduty = $this->config->item("street_onduty_autocheck");

      // $data_historikal = $this->m_development->get_data_historikal($dbtable, $company, $vehicle, $violationmasterselect, $sdate, $edate);
      if ($violationmasterselect == 6) {
  			$alarmtypefromaster[] = 9999;
  		}else {
  			if ($violationmasterselect != "all") {
  				$alarmbymaster = $this->m_development->getalarmbytype($violationmasterselect);
  				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
  					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
  				}
  			}
  		}

  		$this->db = $this->load->database("default", TRUE);
  		$this->db->select("user_id, user_dblive");
  		$this->db->order_by("user_id","asc");
  		$this->db->where("user_id", 4408);
  		$q         = $this->db->get("user");
  		$row       = $q->row();
  		$total_row = count($row);

  		//print_r($sdate." ".$edate);exit();
  		if(count($row)>0){
  			$user_dblive = $row->user_dblive;
  		}

  		// CHOOSE DBTABLE
  		// $current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
  		$m1                = date("F", strtotime($sdate));
  		$year              = date("Y", strtotime($sdate));
  		$dbtable           = "";
  		$dbtable_overspeed = "";
  		$report            = "historikal_violation_";
  		$report_overspeed  = "overspeed_hour_";

  		switch ($m1)
  		{
  			case "January":
  						$dbtable           = $report."januari_".$year;
  						$dbtable_overspeed = $report_overspeed."januari_".$year;
  			break;
  			case "February":
  						$dbtable = $report."februari_".$year;
  						$dbtable_overspeed = $report_overspeed."februari_".$year;
  			break;
  			case "March":
  						$dbtable = $report."maret_".$year;
  						$dbtable_overspeed = $report_overspeed."maret_".$year;
  			break;
  			case "April":
  						$dbtable = $report."april_".$year;
  						$dbtable_overspeed = $report_overspeed."april_".$year;
  			break;
  			case "May":
  						$dbtable = $report."mei_".$year;
  						$dbtable_overspeed = $report_overspeed."mei_".$year;
  			break;
  			case "June":
  						$dbtable = $report."juni_".$year;
  						$dbtable_overspeed = $report_overspeed."juni_".$year;
  			break;
  			case "July":
  						$dbtable = $report."juli_".$year;
  						$dbtable_overspeed = $report_overspeed."juli_".$year;
  			break;
  			case "August":
  						$dbtable = $report."agustus_".$year;
  						$dbtable_overspeed = $report_overspeed."agustus_".$year;
  			break;
  			case "September":
  						$dbtable = $report."september_".$year;
  						$dbtable_overspeed = $report_overspeed."september_".$year;
  			break;
  			case "October":
  						$dbtable = $report."oktober_".$year;
  						$dbtable_overspeed = $report_overspeed."oktober_".$year;
  			break;
  			case "November":
  						$dbtable = $report."november_".$year;
  						$dbtable_overspeed = $report_overspeed."november_".$year;
  			break;
  			case "December":
  						$dbtable = $report."desember_".$year;
  						$dbtable_overspeed = $report_overspeed."desember_".$year;
  			break;
  		}

  		$user_level      = $this->sess->user_level;
  		$user_parent     = $this->sess->user_parent;
  		$user_company    = $this->sess->user_company;
  		$user_subcompany = $this->sess->user_subcompany;
  		$user_group      = $this->sess->user_group;
  		$user_subgroup   = $this->sess->user_subgroup;
  		$user_dblive 	   = $this->sess->user_dblive;
  		$privilegecode 	 = $this->sess->user_id_role;
  		$user_id_fix     = $this->sess->user_id;

  		if($privilegecode == 1){
  			$contractor = $company;
  		}else if($privilegecode == 2){
  			$contractor = $company;
  		}else if($privilegecode == 3){
  			$contractor = $company;
  		}else if($privilegecode == 4){
  			$contractor = $company;
  		}else if($privilegecode == 5){
  			$contractor = $user_company;
  		}else if($privilegecode == 6){
  			$contractor = $user_company;
  		}else if($privilegecode == 0){
  			$contractor = $company;
  		}else{
  			$contractor = $company;
  		}

      // echo "<pre>";
      // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
      // echo "<pre>";

  		$data_array_alert = array();
  		$data_overspeed   = $this->m_development->get_overspeed_intensor_historikal($dbtable_overspeed, $vehicle, $contractor, $sdate, $edate);

      // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

        // echo "<pre>";
        // // var_dump($data_overspeed);die();
        // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
        // echo "<pre>";

  		for ($i=0; $i < sizeof($data_overspeed); $i++) {
  			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
  			array_push($data_array_alert, array(
  				"isfatigue"          => "no",
  				"jalur_name"         => $data_overspeed[$i]['overspeed_report_jalur'],
  				"vehicle_no"         => $data_overspeed[$i]['overspeed_report_vehicle_no'],
  				"vehicle_name"       => $data_overspeed[$i]['overspeed_report_vehicle_name'],
  				"vehicle_company"    => $data_overspeed[$i]['overspeed_report_vehicle_company'],
  				"vehicle_device"     => $data_overspeed[$i]['overspeed_report_vehicle_device'],
  				"vehicle_mv03"       => "",
          "status_sent_tele"   => "",
  				"gps_alert" 				 => "Overspeed",
  				"violation" 				 => "Overspeed",
  				"violation_level" 	 => $data_overspeed[$i]['overspeed_report_level_alias'],
  				"violation_type" 		 => "overspeed",
  				"gps_latitude_real"  => $coordinate[0],
  				"gps_longitude_real" => $coordinate[1],
  				"gps_speed"          => $data_overspeed[$i]['overspeed_report_speed'],
  				"gps_speed_limit"    => $data_overspeed[$i]['overspeed_report_geofence_limit'],
  				"gps_time"           => date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_gps_time'])),
  				"geofence"           => $data_overspeed[$i]['overspeed_report_geofence_name'],
  				"position"           => $data_overspeed[$i]['overspeed_report_location'],
  			));
  		}



        $dbtable = "historikal_violation_desember_2022_testindex";
  			// echo "<pre>";
  			// // var_dump($data_array_alert);die();
        // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$edate);die();
        // // var_dump($alarmtypefromaster);die();
  			// echo "<pre>";

  		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
  		$masterviolation   = $this->m_development->getviolationhistorikal_type2_report($dbtable, $vehicle, $contractor, $alarmtypefromaster, $sdate, $edate);

      // echo "<pre>";
      // var_dump($masterviolation);die();
      // echo "<pre>";

  			if (sizeof($masterviolation) > 0) {
  					for ($j=0; $j < sizeof($masterviolation); $j++) {
  						if ($masterviolation[$j]['violation_fatigue'] != "") {
  							$positionforfilter = $masterviolation[$j]['violation_position'];
  								if ($positionforfilter != "") {
  									// UNTUK UMUM START
  									if (in_array($positionforfilter, $street_onduty)){
                      $json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
                      $forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
                      $forcheck_gps_time       = $json_fatigue[0]->gps_time;
                      // $checkthis               = $this->m_development->getfrommaster($forcheck_vehicledevice);
                      // echo "<pre>";
                      // var_dump($json_fatigue);die();
                      // echo "<pre>";
                      // $jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
                      // $jalurname               = $jsonautocheck->auto_last_road;
                      $jalurname               = $masterviolation[$j]['violation_jalur'];
  										$alarmreportnamefix = "";
  										$alarmreporttype = $json_fatigue[0]->gps_alertid;
  											if ($alarmreporttype == 626) {
  												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
  											}elseif ($alarmreporttype == 627) {
  												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
  											}else {
  												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
  											}

  											if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
  												$violation_split = explode("Level", $alarmreportnamefix);
  												$violationlevel  = str_replace("Start", "", $violation_split[1]);
  												if ($violationlevel == "One") {
  													$violationfix = "1";
  												}else {
  													$violationfix = "2";
  												}

  												// echo "<pre>";
  												// var_dump($violation_split);die();
  												// echo "<pre>";

  												array_push($data_array_alert, array(
  													 "isfatigue"               => "yes",
                             "status_sent_tele"        => $masterviolation[$j]['violation_status_tele'],
  													 "jalur_name"              => $jalurname,
  													 "vehicle_no"              => $json_fatigue[0]->vehicle_no,
  													 "vehicle_name"            => $json_fatigue[0]->vehicle_name,
  													 "vehicle_company"         => $json_fatigue[0]->vehicle_company,
  													 "vehicle_device"          => $json_fatigue[0]->vehicle_device,
  													 "vehicle_mv03"            => $json_fatigue[0]->vehicle_mv03,
  													 "gps_alert"               => $alarmreportnamefix,
  													 "violation" 				       => $violation_split[0],
  													 "violation_level" 				 => "Level " . $violationfix,
  													 "violation_type" 		     => "not_overspeed",
  													 "gps_time"                => $json_fatigue[0]->gps_time,
  													 "gps_latitude_real"       => $json_fatigue[0]->gps_latitude_real,
  													 "gps_longitude_real"      => $json_fatigue[0]->gps_longitude_real,
  													 "position"                => $masterviolation[$j]['violation_position'],
  													 // "auto_last_position"      => $jsonautocheck->auto_last_position,
  													 "gps_speed"               => $json_fatigue[0]->gps_speed,
  												));
  											}
  									}
  									// UNTUK UMUM END
  								}
  					}
  				}

  				$lasttime     = $masterviolation[0]['violation_update'];
  				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
  			}else {
  				$lasttime = $sdate;
  			}

  			 usort($data_array_alert, function($a, $b) {
  			    return strtotime($b['gps_time']) - strtotime($a['gps_time']);
  			});

  			// $violationmix = $this->aasort($data_array_alert, "gps_time");
  			$data_array_fix = array();
  			if($violationmasterselect == 6) {
  				for ($i=0; $i < sizeof($data_array_alert); $i++) {
  				$violation_type = $data_array_alert[$i]['violation_type'];
  					if ($violation_type == "overspeed") {
  						array_push($data_array_fix, array(
  							"isfatigue"                => $data_array_alert[$i]['isfatigue'],
                "status_sent_tele"         => $data_array_alert[$i]['status_sent_tele'],
  							"jalur_name"               => $data_array_alert[$i]['jalur_name'],
  							"vehicle_no"               => $data_array_alert[$i]['vehicle_no'],
  							"vehicle_name"             => $data_array_alert[$i]['vehicle_name'],
  							"vehicle_company"          => $data_array_alert[$i]['vehicle_company'],
  							"vehicle_device"           => $data_array_alert[$i]['vehicle_device'],
  							"vehicle_mv03"             => $data_array_alert[$i]['vehicle_mv03'],
  							"gps_alert" 				       => $data_array_alert[$i]['gps_alert'],
  							"violation" 				       => $data_array_alert[$i]['violation'],
  							"violation_level" 				 => $data_array_alert[$i]['violation_level'],
  							"violation_type" 		       => $data_array_alert[$i]['violation_type'],
  							"gps_latitude_real"        => $data_array_alert[$i]['gps_latitude_real'],
  							"gps_longitude_real"       => $data_array_alert[$i]['gps_longitude_real'],
  							"gps_speed"                => $data_array_alert[$i]['gps_speed'],
  							"gps_speed_limit"          => $data_array_alert[$i]['gps_speed_limit'],
  							"gps_time"                 => $data_array_alert[$i]['gps_time'],
  							"geofence"                 => $data_array_alert[$i]['geofence'],
  							"position"                 => $data_array_alert[$i]['position'],
  						));
  					}
  				}
  			}elseif($violationmasterselect != "0") {
  				for ($i=0; $i < sizeof($data_array_alert); $i++) {
  					$violation_type = $data_array_alert[$i]['violation_type'];
  						if ($violation_type == "not_overspeed") {
  							array_push($data_array_fix, array(
  								"isfatigue"          => $data_array_alert[$i]['isfatigue'],
                  "status_sent_tele"   => $data_array_alert[$i]['status_sent_tele'],
  								"jalur_name"         => $data_array_alert[$i]['jalur_name'],
  								"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
  								"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
  								"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
  								"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
  								"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
  								"gps_alert"          => $data_array_alert[$i]['gps_alert'],
  								"violation" 				 => $data_array_alert[$i]['violation'],
  								"violation_level" 	 => $data_array_alert[$i]['violation_level'],
  								"violation_type" 		 => $data_array_alert[$i]['violation_type'],
  								"gps_time"           => $data_array_alert[$i]['gps_time'],
  								// "auto_last_update"   => $data_array_alert[$i]['auto_last_update'],
  								// "auto_last_check"    => $data_array_alert[$i]['auto_last_check'],
  								"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
  								"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
  								"position"           => $data_array_alert[$i]['position'],
  								// "auto_last_position" => $data_array_alert[$i]['auto_last_position'],
  								"gps_speed"          => $data_array_alert[$i]['gps_speed'],
  							));
  						}
  				}
  			}else {
  				$data_array_fix = $data_array_alert;
  			}

        $rows_company = $this->get_company_bylevel();
        $rcompany     = $rows_company;

        // echo "<pre>";
        // var_dump($data_array_fix);die();
        // echo "<pre>";


        $objectexcel = new PHPExcel();
        $objectexcel->setActiveSheetIndex(0);
        $table_columns = array(
          "No", "Date", "Time", "Vehicle","Company", "Violation", "Location", "Shift", "Jalur", "Week",  "Month", "Coordinate",
          "Level"
        );
        $column = 0;
        foreach($table_columns as $field)
        {
         $objectexcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
         $column++;
        }

        $excel_row = 2;
          for($i = 0; $i < sizeof($data_array_fix); $i++){
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i+1);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, date("d-m-Y", strtotime($data_array_fix[$i]['gps_time'])));
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, date("H:i:s", strtotime($data_array_fix[$i]['gps_time'])));
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $data_array_fix[$i]['vehicle_no']);

            for ($j=0; $j < sizeof($rcompany); $j++){
               if ($rcompany[$j]->company_id == $data_array_fix[$i]['vehicle_company']) {
                 $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rcompany[$j]->company_name);
               }
             }

           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $data_array_fix[$i]['violation']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $data_array_fix[$i]['position']);


           $jam = date("H:i:s", strtotime($data_array_fix[$i]['gps_time']));
             if ($jam >= "06:00:00" && $jam <= "18:00:00") {
               $shiftfix = "Shift 1";
             }else {
               $shiftfix = "Shift 2";
             }

           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $shiftfix);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $data_array_fix[$i]['jalur_name']);

           $ddate = date("Y-m-d", strtotime($data_array_fix[$i]['gps_time']));
           $duedt = explode("-", $ddate);
             $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
             $week  = (int)date('W', $date);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $week);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date("F", strtotime($data_array_fix[$i]['gps_time'])));
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $data_array_fix[$i]['gps_latitude_real'].','.$data_array_fix[$i]['gps_longitude_real']);
           $objectexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $data_array_fix[$i]['violation_level']);

           $excel_row++;
          }

          // Set judul file excel nya
          // $excel->getActiveSheet(0)->setTitle("Laporan Data Siswa");
          // $excel->setActiveSheetIndex(0);
          // Proses file excel
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment; filename="Violation Historikal.xlsx"'); // Set nama file excel nya
          header('Cache-Control: max-age=0');
          $write = PHPExcel_IOFactory::createWriter($objectexcel, 'Excel2007');
          $write->save('php://output');

          // $object_writer = PHPExcel_IOFactory::createWriter($objectexcel, 'Excel5');
          // ob_start();
          // $object_writer->save("php://output");
          // $xlsData = ob_get_contents();
          // ob_end_clean();
          //
          // $response =  array(
          //         'op' => 'ok',
          //         'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
          //     );
          //
          // die(json_encode($response));



      // echo "<pre>";
      // var_dump($data_array_fix);die();
      // echo "<pre>";

      // $this->params['data']     = $data_array_fix;
      // $rows_company             = $this->get_company();
      // $this->params["rcompany"] = $rows_company;
      //
      // $html = $this->load->view("newdashboard/development/violation/v_violationhistorikal_reportresult", $this->params, true);
      // $callback['error'] = false;
      // $callback['html']  = $html;
      // $callback['data']  = $data_array_fix;
      // echo json_encode($callback);
    }
    // VIOLATION TABLE HISTORIKAL REPORT END


    // FUEL SENSOR HISTORY REPORT
    function fuelsensorhistory(){
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
      $user_id_fix     = 4408;

      $param2 = "";

      if ($privilegecode == 0) {
        $param2 = $user_id_fix;
      } else if ($privilegecode == 1) {
        $param2 = $user_parent;
      } else if ($privilegecode == 2) {
        $param2 = $user_parent;
      } else if ($privilegecode == 3) {
        $param2 = $user_parent;
      } else if ($privilegecode == 4) {
        $param2 = $user_parent;
      } else if ($privilegecode == 5) {
        $param2 = $user_company;
      } else if ($privilegecode == 6) {
        $param2 = $user_company;
      } else if ($privilegecode == 7) {
        $param2 = $user_id_fix;
      } else if ($privilegecode == 8) {
        $param2 = $user_id_fix;
      }else if ($privilegecode == 11) {
        $param2 = $user_id_fix;
      }else {
        $param2 = 99999;
      }

      $rows           = $this->m_development->getAllVehicle_fuelsensoronly("all", $param2);

      // echo "<pre>";
      // var_dump($rows);die();
      // echo "<pre>";

      $rows_company                   = $this->get_company();
      $this->params["vehicles"]       = $rows;
      $this->params["vehicledata"]    = $rows;
      $this->params["rcompany"]       = $rows_company;
      $this->params['code_view_menu'] = "report";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      } elseif ($privilegecode == 2) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      } elseif ($privilegecode == 3) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      } elseif ($privilegecode == 4) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      } elseif ($privilegecode == 5) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      }elseif ($privilegecode == 6) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      } else {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/development/fuelsensor/v_home_fuelsensorcheck', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function search_fuelsensor_history(){
      ini_set('display_errors', 1);
      ini_set('memory_limit', '6G');
  		ini_set('max_execution_time', '3600');
      $privilegecode   = $this->sess->user_id_role;
        if ($privilegecode == 5 || $privilegecode == 6) {
          $company = $this->sess->user_company;
        }else {
          $company          = $this->input->post("company");
        }

      $vehicle          = $this->input->post("vehicle");
      // $frekuensianomali = $this->input->post("frekuensianomali");
      $startdate        = $this->input->post("startdate");
      $shour         = "00:00:00";
      $enddate          = $this->input->post("enddate");
      $ehour         = "23:59:59";
      $periode          = $this->input->post("periode");

      // $sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      // $edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

      $sdate2 = date("Y-m-d H:i:s", strtotime($startdate));
      $edate2 = date("Y-m-d H:i:s", strtotime($startdate));

      $nowdate   = date("Y-m-d");
      $nowday    = date("d");
      $nowmonth  = date("m");
      $nowyear   = date("Y");
      $lastday   = date("t");

      if($periode == "custom"){
      		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
      	}else if($periode == "yesterday"){
      		$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
      		$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
      	}else if($periode == "last7"){
      		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
      		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

      	}else if($periode == "last30"){
      		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
      		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

      	}else if($periode == "today"){
      		$sdate1 = date("Y-m-d");
      		$sdate2 = "00:00:00";

      		$edate1 = date("Y-m-d");
      		$edate2 = "23:59:59";

      		$sdate = $sdate1." ".$sdate2;
      		$edate = $edate1." ".$edate2;
      	}else{
      		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
      	}

      $m1     = date("F", strtotime($sdate));
      $year   = date("Y", strtotime($sdate));
      $report = "fuelsensor_history_";

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

        $data_company = $this->get_company_bylevel();

      // echo "<pre>";
      // var_dump($periode.'-'.$dbtable.'-'.$company.'-'.$vehicle.'-'.$sdate.'-'.$edate);die();
      // echo "<pre>";

      $datavehicle = $this->m_development->getthisvehicle($company, $vehicle);
      // echo "<pre>";
      // var_dump($datavehicle);die();
      // echo "<pre>";
      $datafix = array();
      $getreport = "";
        for ($i=0; $i < sizeof($datavehicle); $i++) {
          $vdevice = $datavehicle[$i]['vehicle_device'];
          // $getreport = $this->m_devicereport->getallreport($dbtable, $sdate, $edate, $vehicleno);
          $getreport = $this->m_development->getdatafuelsensorhistory($dbtable, $vdevice, $sdate, $edate);
          array_push($datafix, array(
            "data" => $getreport
          ));
        }


      // $data_summary = $this->m_development->getdatafuelsensorhistory($dbtable, $company, $vehicle, $frekuensianomali, $sdate, $edate);
      // $datahistory = $this->m_development->getdatafuelsensorhistory($dbtable, $company, $vehicle, $sdate, $edate);

      // $dbtable.'-'.$company.'-'.$vehicle.'-'.$frekuensianomali.'-'.$sdate.'-'.$edate
      // $dbtable.'-'.$company.'-'.$vehicle.'-'.$sdate.'-'.$edate
      // echo "<pre>";
      // var_dump($datafix);die();
      // echo "<pre>";

      // $this->params['data']     = $data_summary;
      $this->params['data']     = $datafix;
      $this->params['rcompany'] = $data_company;

      $html = $this->load->view("newdashboard/development/fuelsensor/v_fuelsensorcheck_result", $this->params, true);
      $callback['error'] = false;
      $callback['html']  = $html;
      $callback['data']  = $datafix;
      echo json_encode($callback);
    }
    // FUEL SENSOR HISTORY REPORT

    // DASHBOARD KUOTA START
      function dashboardkuota(){
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
        $user_id_fix     = 4408;

        $rows                           = $this->m_development->getdevice();

        // echo "<pre>";
        // var_dump($rows);die();
        // echo "<pre>";

        $rows_company                   = $this->get_company();
        $this->params["data"]           = $rows;
        $this->params["rcompany"]       = $rows_company;
        $this->params['code_view_menu'] = "report";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        }elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/development/kuota/v_home_kuota', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
      }

      function searchforkuota(){
    		ini_set('display_errors', 1);
    		//ini_set('memory_limit', '2G');
    		if (! isset($this->sess->user_type))
    		{
    			redirect(base_url());
    		}

    		$company       = $this->input->post("company");
    		$vehicle       = $this->input->post("vehicle");
        $year          = date("Y");
        $curr_date     = date("d");
        $curr_month    = date("m");
          if ($curr_date > "06") {
            $startdate     = $year.':'.$curr_month.':'.$curr_date;
          }else {
            $startdate     = $year.':'.$curr_month.':'.$curr_date;
          }
    		$enddate       = $this->input->post("enddate");
    		$shour         = "00:00:00";
    		$ehour         = "23:59:59";
    		$alarmtype     = $this->input->post("alarmtype");
    		// $periode       = $this->input->post("periode");
    		// $km            = $this->input->post("km");
    		// $reporttype = $this->input->post("reporttype");
    		$reporttype = 0;

    		if ($alarmtype != "All") {
    			$alarmbymaster = $this->m_securityevidence->getalarmbytype($alarmtype);
    			$alarmtypefromaster = array();
    			for ($i=0; $i < sizeof($alarmbymaster); $i++) {
    				$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
    			}
    		}

    		// echo "<pre>";
    		// var_dump($startdate);die();
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

    		$black_list  = array("401","428","451","478","602","603","608","609","652","653","658","659",
    							  "600","601","650","651"); //lane deviation & forward collation

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
    			$sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
    			$edate = date("Y-m-d H:i:s");
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

    		$nowday            = date("d");
    		$end_day_fromEdate = date("d", strtotime($edate));

    		if ($nowday == $end_day_fromEdate) {
    			$edate = date("Y-m-d H:i:s");
    		}

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
    		$this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
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
    // DASHBOARD KUOTA END

    // STREAMING DASHBOARD START
    function livemonitoring(){
      ini_set('max_execution_time', '300');
      set_time_limit(300);
      if (! isset($this->sess->user_type))
      {
        redirect(base_url());
      }

      // AKTIFKAN JIKA NANTI PERLU DIADAKAN KONDISI TERTENTU UNTUK STREAMING
      // if ($user_id == 5174 || $user_id == 5168 || $user_id == 5172 || $user_id == 5167) {
      //
      // }else {
      //   redirect(base_url());
      // }

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
        $master_type_unit                = $this->m_development->get_type_unit();
        $mastervehicle                   = $this->m_development->getmastervehicleforheatmap();

        // echo "<pre>";
        // var_dump($master_type_unit);die();
        // echo "<pre>";

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



            // if ($auto_status != "M") {
              array_push($datafix, array(
                "vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
                "vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
                "vehicle_company"      => $mastervehicle[$i]['vehicle_company'],
                "vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
                "vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
                "vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
                "vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
                "vehicle_is_share"     => $mastervehicle[$i]['vehicle_is_share'],
                "vehicle_id_shareto"   => $mastervehicle[$i]['vehicle_id_shareto'],
                "auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
                "auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
              ));
            // }
          }else {
            array_push($datafix, array(
              "vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
              "vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
              "vehicle_company"      => $mastervehicle[$i]['vehicle_company'],
              "vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
              "vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
              "vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
              "vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
              "vehicle_is_share"     => $mastervehicle[$i]['vehicle_is_share'],
              "vehicle_id_shareto"   => $mastervehicle[$i]['vehicle_id_shareto'],
              "auto_last_lat"        => "",
              "auto_last_long"       => "",
            ));
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
        $this->params['filter_unit']    = $master_type_unit;


        // echo "<pre>";
        // var_dump($this->params['mitra_streaming_registered']);die();
        // echo "<pre>";


        $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

        $datastatus                     = explode("|", $rstatus);
        $this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
        $this->params['total_vehicle']  = $datastatus[3];
        $this->params['total_offline']  = $datastatus[2];

        $this->params['vehicles']  	  = $mastervehicle;
        $this->params['vehicledata']  = $datafix;
        $this->params['vehicletotal'] = sizeof($mastervehicle);
        $this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
        $getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
        // echo "<pre>";
        // var_dump($getvehicle_byowner);die();
        // echo "<pre>";
        $totalmobilnya                = sizeof($getvehicle_byowner);
        if ($totalmobilnya == 0) {
          $this->params['name']         = "0";
          $this->params['host']         = "0";
        }else {
          $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
          $this->params['name']         = $arr[0];
          $this->params['host']         = $arr[1];
        }

        // DATA KARYAWAN BC
        $user_license_id = $this->sess->user_license_id;

        if ($user_license_id == "" || $user_license_id == NULL) {
          $sid_where = "all";
        }else {
          // $sid_where = $this->sess->user_login;
          $sid_where = "all";
        }
        $company_id_beats = $this->sess->user_company_id_beats;

        $data_karyawan_bc                    = $this->m_development->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
        $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        // TYPE INTERVENTION
        $type_intervention                   = $this->m_development->get_type_intervention();
        $this->params['type_intervention']   = $type_intervention;
        // NOTES TYPE
        $type_note                           = $this->m_development->get_type_note(1);
        $this->params['type_note']           = $type_note;

        $this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
        $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
        $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
        $this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
        $this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
        $this->params['data_vehicle']   = $this->m_development->getvehicle_bycompany_master($user_company);
        $this->params['vehicle_site']   = $this->m_development->vehicle_site_by_company($user_company);

        // GET MASTER SITE BC FROM WEBTRACKING SITE
        $data_site_bc  = $this->m_development->data_site_bc();
        $data_site_fix = array();

        for ($i=0; $i < sizeof($data_site_bc); $i++) {
          $json_site = json_decode($data_site_bc[$i]['site_json_company']);
            if (sizeof($json_site) > 0) {
              if (in_array($user_company, $json_site)) {
                array_push($data_site_fix, array(
                  "site_id"   => $data_site_bc[$i]['site_id'],
                  "site_name" => $data_site_bc[$i]['site_name'],
                ));
              }
              // echo "<pre>";
              // var_dump($json_site);die();
              // echo "<pre>";
            }
        }
        $this->params['data_site_bc']           = $data_site_fix;

        // echo "<pre>";
        // var_dump($data_site_fix);die();
        // echo "<pre>";

          $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
          // $this->params["header"]         = $this->load->view('newdashboard/partial/headernew_livemonitoring', $this->params, true);
          $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

          if ($privilegecode == 1) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
          } elseif ($privilegecode == 2) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
          } elseif ($privilegecode == 3) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
          } elseif ($privilegecode == 4) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
          } elseif ($privilegecode == 5) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
          }elseif ($privilegecode == 6) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
          } else {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
          }
    }

    function get_stream_by_mitra(){
      $user_parent       = $this->sess->user_company;
      $privilegecode     = $this->sess->user_id_role;
      $user_id_parent    = 4408;
      $id_mitra          = $_POST['id_mitra'];
      $vehicle           = $_POST['vehicle'];
      $filter_unit       = $_POST['filter_unit'];
      $site_option       = $_POST['site_option'];
      $masterdatavehicle = $this->m_development->vehicle_by_mitra($id_mitra, $user_id_parent, $privilegecode, $user_parent, $vehicle, $filter_unit, $site_option);
      $username          = "DEMOPOC";
      $password          = "000000";
      $username_armor02  = "PT.BERAUCOAL";
      $password_armor02  = "000000";

      $chns              = $_POST['chanel'];
        if ($chns != "all") {
          $chanel = 1;
          $chns   = $chns;
        }else {
          $chanel = 3;
          $chns   = "0,1,2";
        }

      $datafix           = array();

      // echo "<pre>";
      // var_dump($masterdatavehicle);die();
      // echo "<pre>";
      $user_id     = $this->sess->user_id;

      // LOGIN API CMSV LAMA
      $loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
      $loginbarudecode = json_decode($loginbaru);
      $jsession        = $loginbarudecode->jsession;

      // LOGIN API ARMOR 02
      $loginbaru_armor02       = file_get_contents("http://armordvr-02.abditrack.com//StandardApiAction_login.action?account=".$username_armor02."&password=".$password_armor02);
      $loginbarudecode_armor02 = json_decode($loginbaru_armor02);
      $jsession_armor02        = $loginbarudecode_armor02->jsession;

      // echo "<pre>";
      // var_dump($masterdatavehicle);die();
      // echo "<pre>";

      for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
        // LOGIN API
        $user_id             = $this->sess->user_id;
        $vehicle_no          = $masterdatavehicle[$i]['vehicle_no'];
        $vehicle_no_fix      = str_replace(" ", "-", $masterdatavehicle[$i]['vehicle_no']);
        $vehicle_mv03        = $masterdatavehicle[$i]['vehicle_mv03'];
        $vehicle_server_mdvr = $masterdatavehicle[$i]['vehicle_server_mdvr'];
        $isonline            = 0;
        $urlvideofix         = "";

        if ($vehicle_server_mdvr != 0 || $vehicle_server_mdvr != "0") {
          if ($vehicle_server_mdvr == "http://182.253.236.246/") {
            // KONDISI JIKA MDVR SERVER LAMA
            // DEVICE STATUS
            $device_status    = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceOlStatus.action?jsession=".$jsession."&devIdno=".$vehicle_mv03);
            $devstatus_decode = json_decode($device_status);
            $online_data      = $devstatus_decode->onlines;
            $online_status    = $online_data[0]->online;
            if ($online_status == 1) {
              $isonline = 1;
            }

            if ($user_id == 5167 || $user_parent == 5167) {
              $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_mv03."&account=".$username."&password=".$password."&channel=".$chanel."&chns=".$chns."&close=60";
            }else {
              $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_mv03."&account=".$username."&password=".$password."&channel=".$chanel."&chns=".$chns;
            }
          }else {
            // KONDISI JIKA MDVR SERVER BARU : ARMOR02
            $device_status_replace = str_replace("http://182.253.236.246/","https://media222.abditrack.com/",$vehicle_server_mdvr);
            $url_device_status     = $vehicle_server_mdvr."StandardApiAction_getDeviceOlStatus.action?jsession=".$jsession."&devIdno=".$vehicle_mv03;
            $device_status         = file_get_contents($url_device_status);
            $devstatus_decode      = json_decode($device_status);

            if (isset($devstatus_decode->onlines)) {
              $online_data           = $devstatus_decode->onlines;
              $online_status         = $online_data[0]->online;
            }else {
              $online_status         = 0;
              $isonline              = 0;
            }

            // echo "<pre>";
            // var_dump($devstatus_decode);die();
            // echo "<pre>";

            if ($online_status == 1) {
              $isonline = 1;
            }else {
              $isonline = 0;
            }

            // $urlvideofix                     = "https://stream246.abditrack.com/video.html?lang=en&devIdno=".$mastervehicle[0]['vehicle_mv03']."&jsession=".$jsession;
            if ($user_id == 5167 || $user_parent == 5167) {
              $urlvideofix                        = "https://stream246.abditrack.com/video.html?lang=en&vehiIdno=".$vehicle_mv03."&account=".$username."&password=".$password."&channel=".$chanel."&chns=".$chns."&close=60";
            }else {
              //https://fmsppa.abditrack.com/attachment/33780/33778/01/2024/5/5178
              if ($vehicle_server_mdvr == "http://182.253.236.246/") {
                $videourl    = str_replace("http://182.253.236.246/","http://gpsdvr.pilartech.co.id/",$vehicle_server_mdvr);
              }else {
                $videourl    = $vehicle_server_mdvr;
                // $videourl = "http://gpsdvr.pilartech.co.id/";
              }

              $urlvideofix = $videourl."808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_no_fix."&account=".$username_armor02."&password=".$password_armor02."&channel=".$chanel."&chns=".$chns;
            }
            // echo "<pre>";
            // var_dump($urlvideofix);die();
            // echo "<pre>";
          }



            if (isset($urlvideofix)) {
              $isshowvideo = 1;
            }else {
              $isshowvideo = 0;
            }

            array_push($datafix, array(
              "isshowvideo" => $isshowvideo,
              "isonline"    => $isonline,
              "vehicle_no"  => $vehicle_no,
              "url"         => $urlvideofix,
            ));

        }
      }

      $this->params['data_stream_mitra'] = $datafix;
      $this->params['chanel'] = $chanel;

      // echo "<pre>";
      // var_dump($datafix);die();
      // echo "<pre>";

      $html                               = $this->load->view('newdashboard/development/dashboard/v_page_streaming_permitra', $this->params, true);

      $datafixarray = array(
        "isshowvideo"    => $isshowvideo,
        "datafix"        => $datafix,
        "livemonitoring" => $html
      );
      echo json_encode($datafixarray);
    }

    function get_stream_by_mitra_old(){
      $user_parent       = $this->sess->user_company;
      $privilegecode     = $this->sess->user_id_role;
      $user_id_parent    = 4408;
      $id_mitra          = $_POST['id_mitra'];
      $vehicle           = $_POST['vehicle'];
      $filter_unit       = $_POST['filter_unit'];
      $site_option       = $_POST['site_option'];
      $masterdatavehicle = $this->m_development->vehicle_by_mitra($id_mitra, $user_id_parent, $privilegecode, $user_parent, $vehicle, $filter_unit, $site_option);
      $username          = "DEMOPOC";
      $password          = "000000";
      $chns              = $_POST['chanel'];
        if ($chns != "all") {
          $chanel = 1;
          $chns   = $chns;
        }else {
          $chanel = 3;
          $chns   = "0,1,2";
        }

      $datafix           = array();

      // echo "<pre>";
      // var_dump($masterdatavehicle);die();
      // echo "<pre>";
      $user_id     = $this->sess->user_id;

      // LOGIN API
      $loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
      $loginbarudecode = json_decode($loginbaru);
      $jsession        = $loginbarudecode->jsession;



      for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
        // LOGIN API
        $user_id                            = $this->sess->user_id;
        $vehicle_no                         = $masterdatavehicle[$i]['vehicle_no'];
        $vehicle_mv03                       = $masterdatavehicle[$i]['vehicle_mv03'];

        // DEVICE STATUS
        $device_status    = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceOlStatus.action?jsession=".$jsession."&devIdno=".$vehicle_mv03);
        $devstatus_decode = json_decode($device_status);
        $online_data      = $devstatus_decode->onlines;
        $online_status    = $online_data[0]->online;
        $isonline         = 0;
        if ($online_status == 1) {
          $isonline = 1;
        }

        // $urlvideofix                     = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&devIdno=".$mastervehicle[0]['vehicle_mv03']."&jsession=".$jsession;
        if ($user_id == 5167 || $user_parent == 5167) {
          $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_mv03."&account=".$username."&password=".$password."&channel=".$chanel."&chns=".$chns."&close=60";
        }else {
          $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_mv03."&account=".$username."&password=".$password."&channel=".$chanel."&chns=".$chns;
          // $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_mv03."&jsession=".$jsession."&channel=".$chanel."&chns=".$chns;
        }

          if (isset($urlvideofix)) {
            $isshowvideo = 1;
          }else {
            $isshowvideo = 0;
          }

          array_push($datafix, array(
            "isshowvideo" => $isshowvideo,
            "isonline"    => $isonline,
            "vehicle_no"  => $vehicle_no,
            "url"         => $urlvideofix,
          ));
      }

      $this->params['data_stream_mitra'] = $datafix;
      $this->params['chanel'] = $chanel;

      // echo "<pre>";
      // var_dump($datafix);die();
      // echo "<pre>";

      $html                               = $this->load->view('newdashboard/development/dashboard/v_page_streaming_permitra', $this->params, true);
      $datafixarray = array(
        "isshowvideo"    => $isshowvideo,
        "datafix"        => $datafix,
        "livemonitoring" => $html
      );
      echo json_encode($datafixarray);
    }

      function forsearchvehicle(){
        $user_id        = $this->sess->user_id;
        $user_parent    = $this->sess->user_parent;
    		// $user_dblive = $this->sess->user_dblive;
    		$key            = $_POST['key'];
        $filter_unit    = $_POST['filter_unit'];
        $site_option    = $_POST['site_option'];
    		// $key         = "b 9442 wcb";
    		// $keyfix      = str_replace(" ", "", $key);
    		$keyfix         = $key;

    		// echo "<pre>";
    		// var_dump($keyfix);die();
    		// echo "<pre>";

    		$mastervehicle     = $this->m_development->searchmasterdata("webtracking_vehicle", $keyfix, $filter_unit, $site_option);
        $data_multi_select = array();
        $isshowvideo       = 1; // JIKA NANTI ADA KONDISI MAKA DIJADIKAN 0
        $datafix           = array();

    		if (sizeof($mastervehicle) < 1) {
    			echo json_encode(array("code" => "400"));
    		}else {
    			// echo "<pre>";
    			// var_dump($mastervehicle);die();
    			// echo "<pre>";
          $data_multi_select = array();
          for ($i=0; $i < sizeof($mastervehicle); $i++) {
            $dblive              = $mastervehicle[$i]['vehicle_dbname_live'];
            $vehicle_server_mdvr = $mastervehicle[$i]['vehicle_server_mdvr'];
            $device              = explode("@", $mastervehicle[$i]['vehicle_device']);
            $vehicle_no          = $mastervehicle[$i]['vehicle_no'];
            $vehicle_no_fix      = str_replace(" ", "-", $mastervehicle[$i]['vehicle_no']);
      			$device0             = $device[0];
      			$device1             = $device[1];
      			$getdatalastinfo     = $this->m_poipoolmaster->searchdblivedata("webtracking_gps", $dblive, $device0);
      			$lastinfofix         = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

      			// echo "<pre>";
      			// var_dump($lastinfofix);die();
      			// echo "<pre>";

      			$vehiclemv03 = $mastervehicle[$i]['vehicle_mv03'];

            $devicestatusfixnya = "";

          // DRIVER DETAIL START
          $drivername     = $this->getdriver($mastervehicle[$i]['vehicle_id']);

          if ($drivername) {
            $driverexplode  = explode("-", $drivername);
            $iddriver       = $driverexplode[0];
            $drivername     = $driverexplode[1];
            $getdriverimage = $this->getdriverdetail($iddriver);

            if (isset($getdriverimage[0]->driver_image_file_name)) {
              $driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
            }else {
              $driverimage = 0;
            }
          }else {
            $drivername  = "";
            $driverimage = 0;
          }


          // echo "<pre>";
          // var_dump($drivername);die();
          // echo "<pre>";
          // DRIVER DETAIL END

          $datafix = array();
          if (sizeof($getdatalastinfo) > 0) {
            $jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
              if (isset($jsonnya[0]->auto_last_snap)) {
                $snap     = $jsonnya[0]->auto_last_snap;
                $snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
              }else {
                $snap     = "";
                $snaptime = "";
              }

              if (isset($jsonnya[0]->auto_last_road)) {
                $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
              }else {
                $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
              }

              if (isset($jsonnya[0]->auto_last_ritase)) {
                $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
              }else {
                $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
              }

              array_push($datafix, array(
                 "drivername"            	=> $drivername,
                 "driverimage"            => $driverimage,
                 "vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
                 "vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
                 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
                 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
                 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
                 "vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
                 "vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
                 "vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
                 "vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
                 "vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
                 "vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
                 "vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
                 "vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
                 "vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
                 "vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
                 "vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
                 "vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
                 "vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
                 "vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
                 "vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
                 "vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
                 "vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
                 "vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
                 "vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
                 "vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
                 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
                 "vehicle_mv03" 		      => $mastervehicle[$i]['vehicle_mv03'],
                 // "vehicle_info"           => $result[$i]['vehicle_info'],
                 "vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
                 "vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
                 "vehicle_port_time"      => date("d-m-Y H:i:s", strtotime($mastervehicle[$i]['vehicle_port_time'])),
                 "vehicle_port_name"      => $mastervehicle[$i]['vehicle_port_name'],
                 "vehicle_rom_time"       => date("d-m-Y H:i:s", strtotime($mastervehicle[$i]['vehicle_rom_time'])),
                 "vehicle_rom_name"       => $mastervehicle[$i]['vehicle_rom_name'],
                 "vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
                 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
                 "vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
                 "vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
                 "vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
                 "vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
                 "vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
                 "vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
                 "devicestatusfixnya" 	  => $devicestatusfixnya,
                 "auto_last_road" 				=> $autolastroad,
                 "autolastritase" 				=> $autolastritase,
                 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
                 "auto_last_mvd"          => round($lastinfofix->gps_mvd),
                 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
                 "auto_last_check"        => $jsonnya[0]->auto_last_check,
                 "auto_last_snap"         => $snap,
                 "auto_last_snap_time"    => $snaptime,
                 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
                 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
                 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
                 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
                 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
                 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
                 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
                 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
              ));
          }else {
            $jsonnya[0] = json_decode($mastervehicle[$i]['vehicle_autocheck']);
              if (isset($jsonnya[0]->auto_last_snap)) {
                $snap     = $jsonnya[0]->auto_last_snap;
                $snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
              }else {
                $snap     = "";
                $snaptime = "";
              }

              if (isset($jsonnya[0]->auto_last_road)) {
                $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
              }else {
                $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
              }

              if (isset($jsonnya[0]->auto_last_ritase)) {
                $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
              }else {
                $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
              }

              if (isset($lastinfofix->gps_mvd)) {
                $gps_mvdfix = round($lastinfofix->gps_mvd);
              }else {
                $gps_mvdfix = 0;
              }

              array_push($datafix, array(
                 "drivername"            	=> $drivername,
                 "driverimage"            => $driverimage,
                 "vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
                 "vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
                 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
                 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
                 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
                 "vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
                 "vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
                 "vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
                 "vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
                 "vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
                 "vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
                 "vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
                 "vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
                 "vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
                 "vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
                 "vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
                 "vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
                 "vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
                 "vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
                 "vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
                 "vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
                 "vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
                 "vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
                 "vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
                 "vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
                 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
                 "vehicle_mv03" 		      => $mastervehicle[$i]['vehicle_mv03'],
                 // "vehicle_info"           => $result[$i]['vehicle_info'],
                 "vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
                 "vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
                 "vehicle_port_time"      => date("d-m-Y H:i:s", strtotime($mastervehicle[$i]['vehicle_port_time'])),
                 "vehicle_port_name"      => $mastervehicle[$i]['vehicle_port_name'],
                 "vehicle_rom_time"       => date("d-m-Y H:i:s", strtotime($mastervehicle[$i]['vehicle_rom_time'])),
                 "vehicle_rom_name"       => $mastervehicle[$i]['vehicle_rom_name'],
                 "vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
                 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
                 "vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
                 "vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
                 "vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
                 "vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
                 "vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
                 "vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
                 "devicestatusfixnya" 	  => $devicestatusfixnya,
                 "auto_last_road" 					=> $autolastroad,
                 "autolastritase" 				=> $autolastritase,
                 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
                 "auto_last_mvd"          => $gps_mvdfix,
                 "auto_last_update"       => $jsonnya[0]->auto_last_update,
                 "auto_last_check"        => $jsonnya[0]->auto_last_check,
                 "auto_last_snap"         => $snap,
                 "auto_last_snap_time"    => $snaptime,
                 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
                 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
                 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
                 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
                 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
                 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
                 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
                 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
              ));
          }

              if ($vehicle_server_mdvr != 0 || $vehicle_server_mdvr != "") {
                if ($vehicle_server_mdvr == "http://182.253.236.246/") {
                  // KONDISI JIKA CMSV LAMA

                // LOGIN API
            		$username        = "DEMOPOC";
            		$password        = "000000";
            		// $loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
            		$loginbaru       = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password);
            		$loginbarudecode = json_decode($loginbaru);
            		$jsession        = $loginbarudecode->jsession;

                $user_id         = $this->sess->user_id;
                $open_for_busbc  = "17:00:00";
                $close_for_busbc = "18:00:00";
                $current_time    = date("H:i:s", strtotime("+1 Hour"));
                $isshowvideo     = 1; // JIKA NANTI ADA KONDISI MAKA DIJADIKAN 0

                // AKAN DIAKTIFKAN KEMBALI JIKA PERLU DIADAKAN KONDISI
                  // if ($user_id == 5168) {
                  //   if ($current_time >= $open_for_busbc && $current_time <= $close_for_busbc) {
                  //     $isshowvideo = 1;
                  //   }
                  // }elseif ($user_id == 5174 || $user_id == 5172 || $user_id == 5167) {
                    // $isshowvideo = 1;
                  // }

                  // DEVICE STATUS
                  $device_status    = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceOlStatus.action?jsession=".$jsession."&devIdno=".$mastervehicle[$i]['vehicle_mv03']);
                  $devstatus_decode = json_decode($device_status);
                  $online_data      = $devstatus_decode->onlines;
                  $online_status    = $online_data[0]->online;
                  $isonline         = 0;
                  if ($online_status == 1) {
                    $isonline = 1;
                  }
                }else {
                  // LOGIN API
              		$username        = "PT.BERAUCOAL";
              		$password        = "000000";
                  $loginbaru       = file_get_contents("http://armordvr-02.abditrack.com//StandardApiAction_login.action?account=".$username."&password=".$password);
              		$loginbarudecode = json_decode($loginbaru);
              		$jsession        = $loginbarudecode->jsession;
                  // echo "<pre>";
                  // var_dump($loginbarudecode);die();
                  // echo "<pre>";
                  // KONDISI JIKA CMSV BARU ARMOR02
                  $device_status_replace = str_replace("http://182.253.236.246/","https://media222.abditrack.com/",$vehicle_server_mdvr);
                  $url_device_status     = $vehicle_server_mdvr."StandardApiAction_getDeviceOlStatus.action?jsession=".$jsession."&vehiIdno=".$vehicle_no_fix;
                  $device_status         = file_get_contents($url_device_status);
                  $devstatus_decode      = json_decode($device_status);

                  if (isset($devstatus_decode->onlines)) {
                    $online_data           = $devstatus_decode->onlines;
                    $online_status         = $online_data[0]->online;
                  }else {
                    $online_status         = 0;
                    $isonline              = 0;
                  }

                  // echo "<pre>";
                  // var_dump($device_status);die();
                  // echo "<pre>";

                  if ($online_status == 1) {
                    $isonline = 1;
                  }else {
                    $isonline = 0;
                  }

                  // $urlvideofix                     = "https://stream246.abditrack.com/video.html?lang=en&devIdno=".$mastervehicle[0]['vehicle_mv03']."&jsession=".$jsession;
                  if ($user_id == 5167 || $user_parent == 5167) {
                    $urlvideofix                        = "https://stream246.abditrack.com/video.html?lang=en&vehiIdno=".$vehicle_mv03."&account=".$username."&password=".$password."&channel=".$chanel."&chns=".$chns."&close=60";
                  }else {
                    //https://fmsppa.abditrack.com/attachment/33780/33778/01/2024/5/5178
                    if ($vehicle_server_mdvr == "http://182.253.236.246/") {
                      $videourl    = str_replace("http://182.253.236.246/","http://gpsdvr.pilartech.co.id/",$vehicle_server_mdvr);
                    }else {
                      $videourl    = $vehicle_server_mdvr;
                      // $videourl = "http://gpsdvr.pilartech.co.id/";
                    }

                    $urlvideofix = $videourl."808gps/open/player/video.html?lang=en&vehiIdno=".$vehicle_no_fix."&account=".$username."&password=".$password;
                  }
                }
              }

          // $urlvideofix                     = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&devIdno=".$mastervehicle[0]['vehicle_mv03']."&jsession=".$jsession;
                // if ($user_id == 5167 || $user_parent == 5167) {
                //   $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$mastervehicle[$i]['vehicle_mv03']."&account=".$username."&password=".$password."&close=60";
                // }else {
                //   $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&vehiIdno=".$mastervehicle[$i]['vehicle_mv03']."&account=".$username."&password=".$password;
                //   // $urlvideofix                        = "http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&devIdno=".$mastervehicle[0]['vehicle_mv03']."&jsession=".$jsession;
                // }

                array_push($data_multi_select, array(
                  "htmllivemonitoring" => $urlvideofix,
                  "isonline"           => $isonline,
                  "vehicle_no"         => $mastervehicle[$i]['vehicle_no'],
                ));

            // $urlvideofix = "http://gpsdvr.pilartech.co.id/808gps/open/hls/index.html?lang =en&vehiIdno =".$mastervehicle[0]['vehicle_mv03']."&account =".$username."&password =".$password."&channel =4&close =100";
            // $html        = $this->load->view('newdashboard/development/dashboard/v_show_livemonitoring', $this->params, true);

          }
    		}


        $this->params['data_streaming'] = $data_multi_select;
        // echo "<pre>";
        // var_dump($data_multi_select);die();
        // echo "<pre>";
        $html           = $this->load->view('newdashboard/development/dashboard/v_page_streaming', $this->params, true);

        if ($isshowvideo == 1) {
          $datafixarray = array(
            "isshowvideo"    => $isshowvideo,
            "datafix"        => $datafix,
            "url"            => $urlvideofix,
            "livemonitoring" => $html
          );
        }else {
          $datafixarray = array(
            "isshowvideo"    => $isshowvideo,
            "datafix"        => $datafix,
            "url"            => "",
            "livemonitoring" => "",
            "message"        => "Anda telah mencapai limit streaming perhari, silahkan coba kembali besok"
          );
        }

        // echo "<pre>";
        // var_dump("http://gpsdvr.pilartech.co.id/808gps/open/player/video.html?lang=en&devIdno=".$mastervehicle[0]['vehicle_mv03']."&jsession=".$jsession);die();
        // echo "<pre>";
        echo json_encode($datafixarray);
    	}

      function manual_upload_evidence(){
        /* Getting file name */
        $filename         = str_replace(" ", "_", $_FILES['file']['name']);
        $time             = strtotime(date("Y-m-d H:i:s"));
        /* Location */
        $location         = "assets/images/manualintervention/".$time.$filename;
        $uploadOk         = 1;
        $imageFileType    = pathinfo($location,PATHINFO_EXTENSION);
        // echo "<pre>";
        // var_dump($imageFileType);die();
        // echo "<pre>";
        /* Valid Extensions */
        $valid_extensions = array("jpg","jpeg","png");
        /* Check file extension */
        if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
           $uploadOk = 100;
        }

        if($uploadOk == 100){
           echo 100;
        }else{
           /* Upload file */
           if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
              echo $location;
           }else{
              echo 0;
           }
        }
      }

      function submit_manual_intervention(){
        $tablenya                = $_POST['tablenya'];
        $user_id                 = $_POST['user_id'];
        $user_name               = $_POST['user_name'];
        $intervention_sid        = explode("|", $_POST['intervention_sid']);
        $intervention_vehicle    = $_POST['intervention_vehicle'];
        $intervention_category   = $_POST['intervention_category'];
        $alert_name              = $_POST['alert_name'];
        $intervention_note       = $_POST['intervention_note'];
        $intervention_judgement  = $_POST['intervention_judgement'];
        $intervention_supervisor = $_POST['intervention_supervisor'];
        $intervention_date       = $_POST['intervention_date'];
        $manual_image_path       = $_POST['manual_image_path'];
        $manual_evidence_remarks = $_POST['manual_evidence_remarks'];

        $masterdevice = $this->m_development->getDataVehicleByDevice2($intervention_vehicle);

        $data = array(
          "alarm_report_vehicle_id"               => $masterdevice[0]['vehicle_id'],
          "alarm_report_imei"                     => $masterdevice[0]['vehicle_mv03'],
          "alarm_report_vehicle_no"               => $masterdevice[0]['vehicle_no'],
          "alarm_report_vehicle_name"             => $masterdevice[0]['vehicle_name'],
          "alarm_report_vehicle_type"             => $masterdevice[0]['vehicle_type'],
          "alarm_report_vehicle_user_id"          => $masterdevice[0]['vehicle_user_id'],
          "alarm_report_vehicle_company"          => $masterdevice[0]['vehicle_company'],
          "alarm_report_id_cr"                    => $user_id,
          "alarm_report_sid_cr"                   => $intervention_sid[0],
          "alarm_report_name_cr"                  => $intervention_sid[1],
          "alarm_report_supervisor_cr"            => $intervention_supervisor,
          "alarm_report_name"                     => $alert_name,
          "alarm_report_intervention_category_cr" => $intervention_category,
          "alarm_report_note_cr"                  => $intervention_note,
          "alarm_report_judgement_cr"             => $intervention_judgement,
          "alarm_report_supervisor_cr"            => $intervention_supervisor,
          "alarm_report_datetime_cr"              => $intervention_date,
          "alarm_report_image_link"               => $manual_image_path,
          "alarm_report_remark_manual"            => $manual_evidence_remarks,
        );

        // echo "<pre>";
        // var_dump($data);die();
        // echo "<pre>";

        $update = $this->m_development->insert_manual_intervention($tablenya, $data);
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

      function manualevidence(){
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

    		$this->params['data']           = $this->m_development->getdevice();
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
    		$this->params['code_view_menu'] = "report";
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
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    		}elseif ($privilegecode == 2) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    		}elseif ($privilegecode == 3) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    		}elseif ($privilegecode == 4) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    		}elseif ($privilegecode == 5) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    		}elseif ($privilegecode == 6) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    		}else {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/manualevidence/v_dashboard_postevent', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    		}
      }

      function search_manual_evidence(){
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
    		// $km                = $this->input->post("km");
        // $filter_true_false = $this->input->post("filter_true_false");
    		// $reporttype = $this->input->post("reporttype");
    		$reporttype = 0;
    		$alarmtypefromaster = array();

        $alarmtypefromaster = array();

        if ($alarmtype == 999999) {
    			$alarmtypefromaster[] = 9999;
    		}else {
          if ($alarmtype != "All") {
            // $alarmbymaster = $this->m_development->getalarmbytype($alarmtype);
            // $alarmtypefromaster = array();
            // for ($i=0; $i < sizeof($alarmbymaster); $i++) {
              $alarmtypefromaster[] = $alarmtype;
            // }
          }
    		}
        //
    		// if ($alarmtype != "All") {
    		// 	$alarmbymaster = $this->m_development->getalarmbytype($alarmtype);
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

        $dbtable = "alarm_evidence_manual";

        $data_array_alert = array();
        // GET DATA MDVR ALERT
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

    		$this->dbtrip->where("alarm_report_datetime_cr >=", $sdate);
    		$this->dbtrip->where("alarm_report_datetime_cr <=", $edate);

    		if ($alarmtype != "All") {
    			$this->dbtrip->where_in('alarm_report_name', $alarmtypefromaster); //$alarmtype $alarmbymaster[0]['alarm_type']
    		}

    		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
    		$this->dbtrip->order_by("alarm_report_datetime_cr","asc");
    		$q = $this->dbtrip->get($dbtable);

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

    				array_push($data_array_alert, array(
              "alarm_report_id"                          => $thisreport[$j]['alarm_report_id'],
    					"alarm_report_vehicle_id"                  => $thisreport[$j]['alarm_report_vehicle_id'],
    					"alarm_report_vehicle_no"                  => $thisreport[$j]['alarm_report_vehicle_no'],
    					"alarm_report_vehicle_name"                => $thisreport[$j]['alarm_report_vehicle_name'],
              "alarm_report_type"                        => $thisreport[$j]['alarm_report_type'],
    					"alarm_report_name"                        => $alarmreportnamefix,
    					"alarm_report_start_time"                  => $thisreport[$j]['alarm_report_datetime_cr'],
              "alarm_report_remark_manual"               => $thisreport[$j]['alarm_report_remark_manual'],
              "alarm_report_id_cr" 	                     => $alarm_report_id_cr,
              "alarm_report_name_cr" 	                   => $alarm_report_name_cr,
              "alarm_report_sid_cr" 	                   => $alarm_report_sid_cr,
              "alarm_report_statusintervention_cr" 	     => $alarm_report_statusintervention_cr,
              "alarm_report_intervention_category_cr" 	 => $alarm_report_intervention_category_cr,
              "alarm_report_fatiguecategory_cr" 	       => $alarm_report_fatiguecategory_cr,
              "alarm_report_note_cr" 	                   => $alarm_report_note_cr,
              "alarm_report_datetime_cr" 	               => $alarm_report_datetime_cr,
              "alarm_report_note_up" 	                   => $alarm_report_note_up,
    				));
    		}

        usort($data_array_alert, function($a, $b) {
           return strtotime($b['alarm_report_start_time']) - strtotime($a['alarm_report_start_time']);
       });

       // echo "<pre>";
       // var_dump($data_array_fix);die();
       // echo "<pre>";

    		$this->params['content']   = $data_array_alert;
        $this->params['alarmtype'] = $alarmtype;
    		$html                      = $this->load->view('newdashboard/development/dashboard/manualevidence/v_postevent_result', $this->params, true);
    		$callback["html"]          = $html;
    		$callback["report"]        = $data_array_alert;

    		echo json_encode($callback);
    	}

      function get_info_manualevidence(){
    		$alert_id = $this->input->post("alert_id");
    		$sdate    = $this->input->post("sdate");
				$table    = "alarm_evidence_manual";

    		$reportdetail               = $this->m_development->getdetailreport($table, $alert_id, $sdate);

    		// echo "<pre>";
    		// var_dump($reportdetailvideo);die();
    		// echo "<pre>";

    		$urlvideofix  = "";
    		$videoalertid = "";
    		$imagealertid = "";

    			if (sizeof($reportdetail) > 0) {
    				$imagealertid = $reportdetail[0]['alarm_report_image_link'];
    			}else {
    				$imagealertid = "0";
    			}

    		// echo "<pre>";
    		// var_dump($imagealertid);die();
    		// echo "<pre>";

    		$this->params['content']              = $reportdetail;
    		$this->params['imagealertid']         = $imagealertid;
    		$this->params['table'] 			          = $table;
    		$this->params['user_id_role'] 			  = $this->sess->user_id_role;
    		$html                                 = $this->load->view('newdashboard/development/dashboard/manualevidence/v_postevent_infodetail', $this->params, true);
    		$callback["html"]                     = $html;
    		$callback["report"]                   = $reportdetail;
    		echo json_encode($callback);
    	}

      function getdriver($driver_vehicle) {
      	$this->dbtransporter = $this->load->database('transporter',true);
      	$this->dbtransporter->select("*");
      	$this->dbtransporter->from("driver");
      	$this->dbtransporter->order_by("driver_update_date","desc");
      	$this->dbtransporter->where("driver_vehicle", $driver_vehicle);
      	$this->dbtransporter->limit(1);
      	$q = $this->dbtransporter->get();

      	if ($q->num_rows > 0 ){
      		$row = $q->row();
      		$data = $row->driver_id;
      		$data .= "-";
      		$data .= $row->driver_name;
      		return $data;
      		$this->dbtransporter->close();
      	}
      	else {
      	$this->dbtransporter->close();
      	return false;
      	}
      }

      function getdriverdetail($iddriver){
      	$this->dbtransporter = $this->load->database('transporter',true);
      	$this->dbtransporter->select("*");
      	$this->dbtransporter->from("driver_image");
      	$this->dbtransporter->where("driver_image_driver_id", $iddriver);
      	$q   = $this->dbtransporter->get();
      	return $q->result();
      }
    // STREAMING DASHBOARD END

    // DASHBOARD UNIT MONITORING START
    function dashboardunitmonitoring(){
    	ini_set('max_execution_time', '300');
    	set_time_limit(300);
    	if (! isset($this->sess->user_type))
    	{
    		redirect(base_url());
    	}

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
    					"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
    					"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
    					"vehicle_company"      => $mastervehicle[$i]['vehicle_company'],
    					"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
    					"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
    					"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
    					"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
    					"vehicle_is_share"     => $mastervehicle[$i]['vehicle_is_share'],
    					"vehicle_id_shareto"   => $mastervehicle[$i]['vehicle_id_shareto'],
    					"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
    					"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
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

    	$this->params['vehicles']  	  = $mastervehicle;
    	$this->params['vehicledata']  = $datafix;
    	$this->params['vehicletotal'] = sizeof($mastervehicle);
    	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
    	// echo "<pre>";
    	// var_dump($getvehicle_byowner);die();
    	// echo "<pre>";
    	$totalmobilnya                = sizeof($getvehicle_byowner);
    	if ($totalmobilnya == 0) {
    		$this->params['name']         = "0";
    		$this->params['host']         = "0";
    	}else {
    		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
    		$this->params['name']         = $arr[0];
    		$this->params['host']         = $arr[1];
    	}

    	$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
    	$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
    	$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
    	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
    	$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

    	// echo "<pre>";
    	// var_dump($this->params['mapsetting']);die();
    	// echo "<pre>";

    	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    		if ($privilegecode == 1) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    		}elseif ($privilegecode == 2) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    		}elseif ($privilegecode == 3) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    		}elseif ($privilegecode == 4) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    		}elseif ($privilegecode == 5) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    		}elseif ($privilegecode == 6) {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    		}else {
    			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
    			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_unitmonitoring', $this->params, true);
    			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    		}
    }
    // DASHBOARD UNIT MONITORING END

    // DASHBOARD POST EVENT START
    function dashboardpostevent(){
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

  		$this->params['url_code_view']  = "1";
  		$this->params['code_view_menu'] = "monitor";
  		$this->params['maps_code']      = "morehundred";

  		$this->params['engine_on']      = $statusvehicle['engine_on'];
  		$this->params['engine_off']     = $statusvehicle['engine_off'];

      $jabatan_search = array("Driver DT", "Operator");

      $data_karyawan_bc            = $this->m_development->data_karyawan_bymitra_jabatan($company[0]->company_sid_code, $jabatan_search);
      $this->params['data_driver'] = $data_karyawan_bc;

      // echo "<pre>";
  		// var_dump($data_karyawan_bc);die();
  		// echo "<pre>";

  		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

  		$datastatus                     = explode("|", $rstatus);
  		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
  		$this->params['total_vehicle']  = $datastatus[3];
  		$this->params['total_offline']  = $datastatus[2];

  		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
    }

    function getalarmsubcat(){
  		$subcategoryid                = $this->input->post("id");
  		$callback['alarmsubcategory'] = $this->m_securityevidence->getalarmsubcategory($subcategoryid);

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

    function searchreport_postevent(){
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
                "alarm_report_id_cr" 	                     => $overspeed_report_id_cr,
                "alarm_report_name_cr" 	                   => $overspeed_report_name_cr,
                "alarm_report_sid_cr" 	                   => $overspeed_report_sid_cr,
                "alarm_report_spvsid_cr" 	                 => $data_overspeed[$i]['overspeed_report_spvsid_cr'],
                "alarm_report_spvname_cr" 	               => $data_overspeed[$i]['overspeed_report_name_cr'],
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
      $html                      = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent_result', $this->params, true);
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
  		$html                                 = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent_infodetail', $this->params, true);
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
    		$data_overspeed   = $this->m_development->get_overspeed_intensor_intervention_detail($dbtableoverspeed, $alarm_report_id, $sdate);

          // echo "<pre>";
          // var_dump($data_overspeed);die();
          // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$sdate);die();
          // echo "<pre>";

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
            "alarm_report_name"                        => "Overspeed " . $data_overspeed[$i]['overspeed_report_level_alias'],
            "alarm_report_start_time"                  => $data_overspeed[$i]['overspeed_report_gps_time'],
            "alarm_report_end_time"                    => $data_overspeed[$i]['overspeed_report_gps_time'],
            "alarm_report_coordinate_start"            => $data_overspeed[$i]['overspeed_report_coordinate'],
            "alarm_report_coordinate_end"              => $data_overspeed[$i]['overspeed_report_coordinate'],
            "alarm_report_location_start"              => $data_overspeed[$i]['overspeed_report_event_location'],
            "alarm_report_speed" 			                 => $data_overspeed[$i]['overspeed_report_speed'],
            "alarm_report_geofence" 			             => $data_overspeed[$i]['overspeed_report_geofence_name'],
            "alarm_report_speed_limit" 			           => ($data_overspeed[$i]['overspeed_report_geofence_limit']-3),
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
          $sid_where = $this->sess->user_name;
        }
        $company_id_beats = $this->sess->user_company_id_beats;

        $data_karyawan_bc                    = $this->m_development->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
        $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        // $data_karyawan_bc                    = $this->m_development->check_data_karyawan();
        // $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        $this->params['content']             = $data_array_alert;
        $this->params['alert_id']            = $alarm_report_id;
        $this->params['alarmtype']           = $alarmtype;
        $this->params['tablenya']            = $dbtableoverspeed;

        $this->params['monthforparam'] 			 = $monthforparam;
        $this->params['year'] 			         = $year;
        $this->params['user_id_role'] 			 = $this->sess->user_id_role;
        // echo "<pre>";
        // var_dump($type_intervention[0]['intervention_type_id']);die();
        // echo "<pre>";
        $html                                = $this->load->view('newdashboard/development/dashboard/v_postevent_modal_overspeed', $this->params, true);
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
            $sid_where = $this->sess->user_name;
          }
          $company_id_beats = $this->sess->user_company_id_beats;

          $data_karyawan_bc                    = $this->m_development->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
          $this->params['data_karyawan_bc']    = $data_karyawan_bc;

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
    		$html                                 = $this->load->view('newdashboard/development/dashboard/v_dashboard_postevent_modal', $this->params, true);
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

      $data_karyawan          = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $postevent_sid);

      $data = array(
        "overspeed_report_id_up"                    => $this->sess->user_id,
        "overspeed_report_name_up"                  => $data_karyawan[0]['karyawan_bc_name'],
        "overspeed_report_statusintervention_up"    => $alarm_true_false_fix,
        "overspeed_report_truefalse_up"             => $intervention_alarm_fix,
        "overspeed_report_note_up"                  => $intervention_note,
        "overspeed_report_datetime_up"              => $postevent_date,
        "overspeed_report_sid_up"                   => $postevent_sid.'|'.$data_karyawan[0]['karyawan_bc_name'],
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

      $data_karyawan          = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_login);

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
        // "alarm_report_sid_up"             => $postevent_sid,
        // "alarm_report_postevent_name_up"  => $postevent_name,
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_development->update_post_event($tablenya, "alarm_report_id", $alert_id, $data);
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
    function dashboardintervention(){
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
  		// var_dump($type_intervention);die();
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

      $data_company = $this->m_development->getcompanybyID($companyid);

      // GET KARYAWAN KHUSUS MITRA
      if ($privilegecode == 5 || $privilegecode == 6) {
        $data_karyawan_bc                    = $this->m_development->data_karyawan_bymitra($data_company[0]->company_sid_code);
      }else {
        $data_karyawan_bc                    = $this->m_development->data_karyawan_bymitra("all");
      }
      $this->params['data_karyawan']    = $data_karyawan_bc;

  		// echo "<pre>";
  		// var_dump($data_company);die();
  		// echo "<pre>";

  		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
    }

    function submit_intervention_config(){
      $user_company   = $this->sess->user_company;
      $supervisorSID  = $_POST['supervisorSID'];
      $supervisorName = $_POST['supervisorName'];

      $shift_1_start = date("Y-m-d H:i:s", strtotime("06:00:00"));
      $shift_1_end   = date("Y-m-d H:i:s", strtotime("18:00:00"));
      $shift_2_start = date("Y-m-d H:i:s", strtotime("18:00:00"));
      $shift_2_end   = date("Y-m-d H:i:s", strtotime("06:00:00"));

      $current_time = date("Y-m-d H:i:s");
      if ($current_time >= $shift_1_start && $current_time <= $shift_1_end) {
        $config_shift = 1;
        $sdate_config = $shift_1_start;
        $edate_config = $shift_1_end;
      }else {
        $config_shift = 2;
        $sdate_config = $shift_2_start;
        $edate_config = $shift_2_end;
      }

      $data = array(
        "config_supervisor_id"   => $supervisorSID,
        "config_supervisor_name" => $supervisorName,
        "config_company"         => $user_company,
        "config_shift"           => $config_shift,
        "config_start_shift"     => $sdate_config,
        "config_end_shift"       => $edate_config
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $insert = $this->m_development->insert_intervention_config($data);
        if ($insert) {
          $callback["error"]          = false;
          $callback["msg"]          = "Berhasil input konfigurasi intervensi";

          echo json_encode($callback);
          return false;
        }else {
          $callback["error"]          = true;
          $callback["msg"]          = "Gagal input konfigurasi intervensi";

          echo json_encode($callback);
          return false;
        }
    }

    function check_isinput_intervention_config(){
      $user_company = $this->sess->user_company;

      $shift_1_start = date("Y-m-d H:i:s", strtotime("06:00:00"));
      $shift_1_end   = date("Y-m-d H:i:s", strtotime("18:00:00"));
      $shift_2_start = date("Y-m-d H:i:s", strtotime("18:00:00"));
      $shift_2_end   = date("Y-m-d H:i:s", strtotime("06:00:00"));

      $current_time = date("Y-m-d H:i:s");
      if ($current_time >= $shift_1_start && $current_time <= $shift_1_end) {
        $sdate_config = $shift_1_start;
        $edate_config = $shift_1_end;
      }else {
        $sdate_config = $shift_2_start;
        $edate_config = $shift_2_end;
      }

      $intervention_config = $this->m_development->check_intervention_config($user_company, $sdate_config, $edate_config);
        if (sizeof($intervention_config) > 0) {
          $callback["error"] = false;
          $callback["msg"]   = "Anda sudah menginput konfigurasi intervensi. Silahkan hubungi pengawas untuk melakukan perubahan!";

          echo json_encode($callback);
          return false;
        }else {
          $callback["error"] = true;
          $callback["msg"]   = "";

          echo json_encode($callback);
          return false;
        }
    }

    function searchreport_controlroom(){
    ini_set('display_errors', 1);
    //ini_set('memory_limit', '2G');
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

    $user_company = $this->sess->user_company;

    $shift_1_start = date("Y-m-d H:i:s", strtotime("06:00:00"));
    $shift_1_end   = date("Y-m-d H:i:s", strtotime("18:00:00"));
    $shift_2_start = date("Y-m-d H:i:s", strtotime("18:00:00"));
    $shift_2_end   = date("Y-m-d H:i:s", strtotime("06:00:00"));

    $current_time = date("Y-m-d H:i:s");
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

    $intervention_config = $this->m_development->check_intervention_config($user_company, $sdate_config, $edate_config);

    // echo "<pre>";
    // var_dump($intervention_config);die();
    // echo "<pre>";

    if (sizeof($intervention_config) < 1) {
      $msg = "Anda belum mengisi form Konfigurasi Intervensi Shift ". $shift;

      $this->params["error"]   = true;
      $this->params["message"] = $msg;
      $this->params['content']   = 0;

      // $html                      = $this->load->view('newdashboard/dashboardberau/intervention/part_1/v_intervention_result', $this->params, true);
      $html                      = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_result', $this->params, true);

      $callback["html"]          = $html;

      echo json_encode($callback);
      return false;
    }

    // echo "<pre>";
    // var_dump($intervention_config);die();
    // echo "<pre>";


    $company       = $_POST['company'];
    $vehicle       = $_POST['vehicle'];
    $startdate     = $_POST['startdate'];
    $enddate       = $_POST['enddate'];
    $shour         = $_POST['shour'];
    $ehour         = $_POST['ehour'];
    $alarmtype     = $_POST['alarmtype'];
    $periode       = $_POST['periode'];
    $km            = $_POST['km'];
    // $reporttype = $this->input->post("reporttype");
    $reporttype = 0;
    $alarmtypefromaster = array();

    if ($alarmtype == 999999) {
      $alarmtypefromaster[] = 9999;
    }else {
      if ($alarmtype != "All") {
        $alarmbymaster = $this->m_securityevidence->getalarmbytype($alarmtype);
        $alarmtypefromaster = array();
        for ($i=0; $i < sizeof($alarmbymaster); $i++) {
          $alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
        }
      }
    }

    // echo "<pre>";
    // var_dump($company.'-'.$vehicle.'-'.$periode);die();
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

    // echo "<pre>";
    // var_dump($vehicle.'-'.$company.'-'.$privilegecode);die();
    // echo "<pre>";

    // GET DATA ALERT OVERSPEED
    $data_array_alert = array();
    // $data_overspeed   = $this->m_development->get_overspeed_intensor_intervention($dbtable_overspeed, $vehicle, $company, $sdate, $edate); // LIVE
    $data_overspeed   = $this->m_development->get_overspeed_intensor_intervention($dbtable_overspeed, $vehicle, $company, "2023-12-24 00:00:00", $edate); // DEVELOPMENT

    // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

      // echo "<pre>";
      // var_dump($data_overspeed);die();
      // // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
      // echo "<pre>";

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

      if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
        $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
      }else {
        $overspeed_report_note_up = "";
      }

      $coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);

      if ($data_overspeed[$i]['overspeed_report_event_location'] != "") {
        array_push($data_array_alert, array(
          "isfatigue"                                => "no",
          "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
          "alarm_report_imei"                        => $data_overspeed[$i]['overspeed_report_imei'],
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
          "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
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
        ));
      }
    }


      // echo "<pre>";
      // var_dump($alarmtype);die();
      // // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$edate);die();
      // // var_dump($alarmtypefromaster);die();
      // echo "<pre>";


    // GET DATA ALERT MDVR
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
      $this->dbtrip->where("alarm_report_statusintervention_cr",0);
      $this->dbtrip->where("alarm_report_statusintervention_cr","");
      // $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
      $this->dbtrip->order_by("alarm_report_start_time","asc");
      $this->dbtrip->group_by("alarm_report_start_time");
      $q          = $this->dbtrip->get($dbtable);
      $rows       = $q->result_array();
      $thisreport = $rows;
      //
      // echo "<pre>";
      // var_dump($q->result_array());die();
      // echo "<pre>";

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

          if (isset($thisreport[$j]['alarm_report_note_up'])) {
            $alarm_report_note_up =  $thisreport[$j]['alarm_report_note_up'];
          }else {
            $alarm_report_note_up = "";
          }

          array_push($data_array_alert, array(
            "isfatigue"                                => "yes",
            "alarm_report_id"                          => $thisreport[$j]['alarm_report_id'],
            "alarm_report_imei"                        => $thisreport[$j]['alarm_report_imei'],
            "alarm_report_vehicle_id"                  => $thisreport[$j]['alarm_report_vehicle_id'],
            "alarm_report_vehicle_no"                  => $thisreport[$j]['alarm_report_vehicle_no'],
            "alarm_report_vehicle_name"                => $thisreport[$j]['alarm_report_vehicle_name'],
            "alarm_report_type"                        => $thisreport[$j]['alarm_report_type'],
            "alarm_report_name"                        => $alarmreportnamefix,
            "alarm_report_start_time"                  => $thisreport[$j]['alarm_report_start_time'],
            "alarm_report_end_time"                    => $thisreport[$j]['alarm_report_end_time'],
            "alarm_report_coordinate_start"            => $thisreport[$j]['alarm_report_coordinate_start'],
            "alarm_report_coordinate_end"              => $thisreport[$j]['alarm_report_coordinate_end'],
            "alarm_report_location_start"              => $thisreport[$j]['alarm_report_location_start'],
            "alarm_report_speed" 			                 => $thisreport[$j]['alarm_report_speed'],
            "alarm_report_geofence" 	                 => "",
            "alarm_report_speed_limit"                 => "",
            "alarm_report_jalur" 	                     => $thisreport[$j]['alarm_report_jalur'],
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
          ));
      }
    }

    usort($data_array_alert, function($a, $b) {
       return strtotime($b['alarm_report_start_time']) - strtotime($a['alarm_report_start_time']);
   });


   $data_array_fix = array();
   if($alarmtype == 999999) {
     for ($i=0; $i < sizeof($data_array_alert); $i++) {
     $violation_type = $data_array_alert[$i]['alarm_report_name'];
       if ($violation_type == "Overspeed") {
         array_push($data_array_fix, array(
           "isfatigue"                               => "no",
           "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
           "alarm_report_imei"                       => $data_array_alert[$i]['alarm_report_imei'],
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
           "alarm_report_statusintervention_cr" 	   => $data_array_alert[$i]['alarm_report_statusintervention_cr'],
           "alarm_report_intervention_category_cr" 	 => $data_array_alert[$i]['alarm_report_intervention_category_cr'],
           "alarm_report_fatiguecategory_cr" 	       => $data_array_alert[$i]['alarm_report_fatiguecategory_cr'],
           "alarm_report_note_cr" 	                 => $data_array_alert[$i]['alarm_report_note_cr'],
           "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
           "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
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
             "alarm_report_imei"                       => $data_array_alert[$i]['alarm_report_imei'],
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
             "alarm_report_statusintervention_cr" 	   => $data_array_alert[$i]['alarm_report_statusintervention_cr'],
             "alarm_report_intervention_category_cr" 	 => $data_array_alert[$i]['alarm_report_intervention_category_cr'],
             "alarm_report_fatiguecategory_cr" 	       => $data_array_alert[$i]['alarm_report_fatiguecategory_cr'],
             "alarm_report_note_cr" 	                 => $data_array_alert[$i]['alarm_report_note_cr'],
             "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
             "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
           ));
         }
     }
   }else {
     $data_array_fix             = $data_array_alert;
   }

   if (sizeof($data_array_fix) > 0) {
     $time_alert = $data_array_fix[0]['alarm_report_start_time'];
   }else {
     $time_alert = "";
   }

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

   $user_company             = $this->sess->user_company;
   $sid_pengawas_from_config = $this->m_development->check_intervention_config_2($user_company, $sdate_config, $edate_config);

  // echo "<pre>";
  // // var_dump($user_company.'-'.$sdate_config.'-'.$edate_config);die();
  // var_dump($sid_pengawas_from_config);die();
  // echo "<pre>";

    $this->params['content']   = $data_array_fix;
    $this->params['alarmtype'] = $alarmtype;
    $this->params['error']     = false;
    $this->params['message']   = "";
    // $html                      = $this->load->view('newdashboard/dashboardberau/intervention/part_1/v_intervention_result', $this->params, true);
    $html                      = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_result', $this->params, true);

    $callback["html"]          = $html;
    $callback["time_alert"]    = $time_alert;
    $callback["report"]        = $data_array_fix;
    $callback["pengawas_sid"]  = $sid_pengawas_from_config[0]['config_supervisor_id'];
    $callback["pengawas_name"] = $sid_pengawas_from_config[0]['config_supervisor_name'];
    $callback["last_refresh"]  = date("d-m-Y H:i:s");
    $callback["error"]         = false;

    echo json_encode($callback);
  }

    function searchreport_controlroom_old(){
  		ini_set('display_errors', 1);
  		//ini_set('memory_limit', '2G');
  		if (! isset($this->sess->user_type))
  		{
  			redirect(base_url());
  		}

      $user_company = $this->sess->user_company;

      $shift_1_start = date("Y-m-d H:i:s", strtotime("06:00:00"));
      $shift_1_end   = date("Y-m-d H:i:s", strtotime("18:00:00"));
      $shift_2_start = date("Y-m-d H:i:s", strtotime("18:00:00"));
      $shift_2_end   = date("Y-m-d H:i:s", strtotime("06:00:00"));

      $current_time = date("Y-m-d H:i:s");
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

      $intervention_config = $this->m_development->check_intervention_config($user_company, $sdate_config, $edate_config);

      // echo "<pre>";
      // var_dump($intervention_config);die();
      // echo "<pre>";

      if (sizeof($intervention_config) < 1) {
  			$msg = "Anda belum mengisi form Konfigurasi Intervensi Shift ". $shift;

        $this->params["error"]   = true;
        $this->params["message"] = $msg;
        $this->params['content']   = 0;

        $html                      = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_result', $this->params, true);
        $callback["html"]          = $html;

    		echo json_encode($callback);
        return false;
      }

      // echo "<pre>";
      // var_dump($intervention_config);die();
      // echo "<pre>";


  		$company       = $_POST['company'];
  		$vehicle       = $_POST['vehicle'];
  		$startdate     = $_POST['startdate'];
  		$enddate       = $_POST['enddate'];
  		$shour         = $_POST['shour'];
  		$ehour         = $_POST['ehour'];
  		$alarmtype     = $_POST['alarmtype'];
  		$periode       = $_POST['periode'];
  		$km            = $_POST['km'];
  		// $reporttype = $this->input->post("reporttype");
  		$reporttype = 0;
  		$alarmtypefromaster = array();

      if ($alarmtype == 999999) {
  			$alarmtypefromaster[] = 9999;
  		}else {
        if ($alarmtype != "All") {
          $alarmbymaster = $this->m_securityevidence->getalarmbytype($alarmtype);
          $alarmtypefromaster = array();
          for ($i=0; $i < sizeof($alarmbymaster); $i++) {
            $alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
          }
        }
  		}

  		// echo "<pre>";
  		// var_dump($company.'-'.$vehicle.'-'.$periode);die();
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
        // if (date("d") == 01) {
          $sdate = date("Y-m-d 00:00:00");
        // }else {
        //   $sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
        // }
  			$edate = date("Y-m-d H:i:s");
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

  		// echo "<pre>";
  		// var_dump($vehicle.'-'.$company.'-'.$privilegecode);die();
  		// echo "<pre>";

      // GET DATA ALERT OVERSPEED
      $data_array_alert = array();
  		$data_overspeed   = $this->m_development->get_overspeed_intensor_intervention($dbtable_overspeed, $vehicle, $company, $sdate, $edate, 0);

      // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

        // echo "<pre>";
        // var_dump($data_overspeed);die();
        // // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
        // echo "<pre>";

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

        if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
          $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
        }else {
          $overspeed_report_note_up = "";
        }

  			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
  			array_push($data_array_alert, array(
  				"isfatigue"                                => "no",
          "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
          "alarm_report_imei"                        => $data_overspeed[$i]['overspeed_report_imei'],
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
  			));
  		}


  			// echo "<pre>";
  			// var_dump($alarmtype);die();
        // // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$edate);die();
        // // var_dump($alarmtypefromaster);die();
  			// echo "<pre>";


      // GET DATA ALERT MDVR
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
    		// $this->dbtrip->where("alarm_report_start_time >=", $sdate); // LIVE
        $this->dbtrip->where("alarm_report_start_time >=", "2024-01-26 00:00:00"); // TEST

    		// $nowday            = date("d");
    		// $end_day_fromEdate = date("d", strtotime($edate));
        //
    		// if ($nowday == $end_day_fromEdate) {
    		// 	$edate = date("Y-m-d H:i:s");
    		// }

        // $this->dbtrip->where("alarm_report_start_time <=", $edate);// LIVE
        $this->dbtrip->where("alarm_report_start_time <=", "2024-01-26 08:59:59"); // TEST
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
        $this->dbtrip->where("alarm_report_statusintervention_cr",0);
        $this->dbtrip->where("alarm_report_statusintervention_cr","");
    		// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
    		$this->dbtrip->order_by("alarm_report_start_time","asc");
    		$this->dbtrip->group_by("alarm_report_start_time");
    		$q          = $this->dbtrip->get($dbtable);
        $rows       = $q->result_array();
        $thisreport = $rows;
    		//
    		// echo "<pre>";
    		// var_dump($q->result_array());die();
    		// echo "<pre>";

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

            if (isset($thisreport[$j]['alarm_report_note_up'])) {
              $alarm_report_note_up =  $thisreport[$j]['alarm_report_note_up'];
            }else {
              $alarm_report_note_up = "";
            }

    				array_push($data_array_alert, array(
              "isfatigue"                                => "yes",
              "alarm_report_id"                          => $thisreport[$j]['alarm_report_id'],
              "alarm_report_imei"                        => $thisreport[$j]['alarm_report_imei'],
    					"alarm_report_vehicle_id"                  => $thisreport[$j]['alarm_report_vehicle_id'],
    					"alarm_report_vehicle_no"                  => $thisreport[$j]['alarm_report_vehicle_no'],
    					"alarm_report_vehicle_name"                => $thisreport[$j]['alarm_report_vehicle_name'],
              "alarm_report_type"                        => $thisreport[$j]['alarm_report_type'],
    					"alarm_report_name"                        => $alarmreportnamefix,
    					"alarm_report_start_time"                  => $thisreport[$j]['alarm_report_start_time'],
    					"alarm_report_end_time"                    => $thisreport[$j]['alarm_report_end_time'],
    					"alarm_report_coordinate_start"            => $thisreport[$j]['alarm_report_coordinate_start'],
    					"alarm_report_coordinate_end"              => $thisreport[$j]['alarm_report_coordinate_end'],
    					"alarm_report_location_start"              => $thisreport[$j]['alarm_report_location_start'],
    					"alarm_report_speed" 			                 => $thisreport[$j]['alarm_report_speed'],
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
    				));
    		}
      }

      usort($data_array_alert, function($a, $b) {
         return strtotime($b['alarm_report_start_time']) - strtotime($a['alarm_report_start_time']);
     });


     $data_array_fix = array();
     if($alarmtype == 999999) {
       for ($i=0; $i < sizeof($data_array_alert); $i++) {
       $violation_type = $data_array_alert[$i]['alarm_report_name'];
         if ($violation_type == "Overspeed") {
           array_push($data_array_fix, array(
             "isfatigue"                               => "no",
             "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
             "alarm_report_imei"                       => $data_array_alert[$i]['alarm_report_imei'],
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
             "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
             "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
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
               "alarm_report_imei"                       => $data_array_alert[$i]['alarm_report_imei'],
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
               "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
               "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
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
      $this->params['error'] = false;
      $this->params['message'] = "";
  		$html                      = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_result', $this->params, true);
  		$callback["html"]          = $html;
  		$callback["report"]        = $data_array_fix;
      $callback["error"]        = false;

  		echo json_encode($callback);
  	}

    function intervention_detail_controlroom_p1(){
      $alert_id          = $this->input->post("alert_id");
      $sdate             = $this->input->post("sdate");
      $alarm_report_id   = $this->input->post("alarm_report_id");
      $alarm_report_imei = $this->input->post("alarm_report_imei");
      $alarmtype         = $this->input->post("alarmtype");
      // $alarmtype      = $this->input->post("alarmtype");
      $report            = "alarm_evidence_";
      $reportoverspeed   = "overspeed_hour_";
      $monthforparam     = date("m", strtotime($sdate));
      $m1                = date("F", strtotime($sdate));
      $year              = date("Y", strtotime($sdate));
      $jalur             = "";

      // echo "<pre>";
      // var_dump($alarm_report_imei);die();
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
      $privilegecode = $this->sess->user_id_role;
      $company       = $this->dashboardmodel->getcompany_byowner($privilegecode);

      $jabatan_search = array("Driver DT", "Operator");

      if ($alarmtype == "Overspeed") {
        $data_array_alert = array();
        $data_overspeed   = $this->m_development->get_overspeed_intensor_intervention_detail($dbtableoverspeed, $alarm_report_id, $sdate);

          // echo "<pre>";
          // var_dump($data_overspeed);die();
          // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$sdate);die();
          // echo "<pre>";

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

          if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
            $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
          }else {
            $overspeed_report_note_up = "";
          }

          $coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
          // if ($data_overspeed[$i]['overspeed_report_event_location'] != "") {
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
              "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
              "alarm_report_level_alias"                 => $data_overspeed[$i]['overspeed_report_level_alias'],
              "alarm_report_speed_time" 		             => "",
              "alarm_report_speed_status" 	             => "",
              "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
              "alarm_report_id_cr" 	                     => $overspeed_report_id_cr,
              "alarm_report_name_cr" 	                   => $overspeed_report_name_cr,
              "alarm_report_sid_cr" 	                   => $overspeed_report_sid_cr,
              "alarm_report_statusintervention_cr" 	     => $overspeed_report_statusintervention_cr,
              "alarm_report_intervention_category_cr" 	 => $overspeed_report_intervention_category_cr,
              "alarm_report_fatiguecategory_cr" 	       => "",
              "alarm_report_note_cr" 	                   => $overspeed_report_note_cr,
              "alarm_report_datetime_cr" 	               => $overspeed_report_datetime_cr,
              "alarm_report_note_up" 	                   => $overspeed_report_note_up,
            ));
          // }
        }
        // echo "<pre>";
        // var_dump($data_array_alert);die();
        // echo "<pre>";

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

        $sid_pengawas_from_config                 = $this->m_development->check_intervention_config_2($user_company, $sdate_config, $edate_config);
        $this->params['sid_pengawas_from_config'] = $sid_pengawas_from_config;

        $type_note                                = $this->m_development->get_type_note(1);
        $this->params['type_note']                = $type_note;

        $type_intervention                        = $this->m_development->get_type_intervention();
        $this->params['type_intervention']        = $type_intervention;

        $data_karyawan_bc                         = $this->m_development->data_karyawan_bymitra_jabatan($company[0]->company_sid_code, $jabatan_search);
        $this->params['data_karyawan_all']        = $data_karyawan_bc;

        $data_karyawan                            = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_login);
        $this->params['data_karyawan']            = $data_karyawan;

        $data_site_vehicle                        = $this->m_development->getDataVehicleById_2($data_overspeed[0]['overspeed_report_imei']);
        $data_vehicle_site                        = $this->m_development->getDataSite($data_site_vehicle[0]['vehicle_site']);
        $user_company = $this->sess->user_company;

        if ($user_company == 1963) {
          $location_id_sync = "114151";
        }elseif ($user_company == 1961) {
          $location_id_sync = "1172171933";
        }

        $data_location                            = $this->m_development->master_location_bc("ts_bc_master_location", $location_id_sync);

        // echo "<pre>";
        // // var_dump($user_company.'-'.$sdate_config.'-'.$edate_config);die();
        // var_dump($data_vehicle_site);die();
        // echo "<pre>";



        $this->params['content']             = $data_array_alert;
        $this->params['alert_id']            = $alarm_report_id;
        $this->params['alarmtype']           = $alarmtype;
        $this->params['tablenya']            = $dbtableoverspeed;
        $this->params['data_site']           = $data_vehicle_site;
        $this->params['data_location']       = $data_location;

        $this->params['monthforparam'] 			 = $monthforparam;
        $this->params['year'] 			         = $year;
        $this->params['user_id_role'] 			 = $this->sess->user_id_role;
        $html                                = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_modal_overspeed', $this->params, true);
        $callback["report"]                  = $data_array_alert;
      }else {
        $reportdetail               = $this->m_securityevidence->getdetailreport_intervention($table, $alert_id, $alarm_report_imei, $sdate);
        $reportdetailvideo          = $this->m_securityevidence->getdetailreportvideo_intervention($table, $alert_id, $alarm_report_imei, $sdate);
        $reportdetaildecode         = explode("|", $reportdetail[0]['alarm_report_gpsstatus']);

        // echo "<pre>";
        // var_dump($data_vehicle_site);die();
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

          if (isset($reportdetaildecode[4])) {
            $speedgps                          = number_format($reportdetaildecode[4]/10, 1, '.', '');
          }else {
            $speedgps = 0;
          }
          //$speedgps                        = $reportdetail[0]['alarm_report_speed']; //by speed gps TK510

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

          $sid_pengawas_from_config                 = $this->m_development->check_intervention_config_2($user_company, $sdate_config, $edate_config);
          $this->params['sid_pengawas_from_config'] = $sid_pengawas_from_config;

          $alarm_report_coordinate_start            = $reportdetail[0]['alarm_report_coordinate_start'];

          // $data_site                             = $this->m_development->master_site_bc_all("ts_bc_master_site");
          // $this->params['data_site']             = $data_site;

          $type_intervention                        = $this->m_development->get_type_intervention();
          $this->params['type_intervention']        = $type_intervention;

          $type_note                                = $this->m_development->get_type_note(1);
          $this->params['type_note']                = $type_note;

          $data_karyawan_bc                         = $this->m_development->data_karyawan_bymitra_jabatan($company[0]->company_sid_code, $jabatan_search);
          $this->params['data_karyawan_all']        = $data_karyawan_bc;

          $data_karyawan                            = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_login);
          $this->params['data_karyawan']            = $data_karyawan;

          $alarmreportnamefix                       = "";
          $alarmreporttype                          = $reportdetail[0]['alarm_report_type'];

          $data_site_vehicle                        = $this->m_development->getDataVehicleById_2($reportdetail[0]['alarm_report_imei']);
          $data_vehicle_site                        = $this->m_development->getDataSite($data_site_vehicle[0]['vehicle_site']);

          // echo "<pre>";
          // // var_dump($user_company.'-'.$sdate_config.'-'.$edate_config);die();
          // var_dump($data_karyawan);die();
          // echo "<pre>";

          $user_company = $this->sess->user_company;

          if ($user_company == 1963) {
            $location_id_sync = "114151";
          }elseif ($user_company == 1961) {
            $location_id_sync = "1172171933";
          }

          $data_location                            = $this->m_development->master_location_bc("ts_bc_master_location", $location_id_sync);

            // if ($alarmreporttype == 626) {
            // 	$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
            // }elseif ($alarmreporttype == 627) {
            // 	$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
            // }else

            if ($alarmreporttype == 702) {
              $alarmreportnamefix = "Distracted Driving Alarm Level One Start";
            }elseif ($alarmreporttype == 703) {
              $alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
            }elseif ($alarmreporttype == 752) {
              $alarmreportnamefix = "Distracted Driving Alarm Level One End";
            }elseif ($alarmreporttype == 753) {
              $alarmreportnamefix = "Distracted Driving Alarm Level Two End";
            }else {
              $alarmreportnamefix = $reportdetail[0]['alarm_report_name'];
            }

        // echo "<pre>";
        // // var_dump($user_company.'-'.$sdate_config.'-'.$edate_config);die();
        // var_dump($data_location);die();
        // echo "<pre>";

        $this->params['content']              = $reportdetail;
        $this->params['data_site']            = $data_vehicle_site;
        $this->params['data_location']        = $data_location;
        $this->params['alarm_name']           = $alarmreportnamefix;
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
    		$html                                 = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_modal_p1', $this->params, true);
        $callback["report"]                   = $reportdetail;
        $callback["sid_pengawas_from_config"] = $sid_pengawas_from_config;
        $callback["data_site"]                = $data_vehicle_site;
      }


      $callback["html"]                     = $html;
      echo json_encode($callback);
  	}

    function submit_intervention_controlroom_p1(){
      $user_company                = $this->sess->user_company;
      $user_id                     = $_POST['user_id'];
      $user_name                   = $_POST['user_name'];
      $alert_id                    = $_POST['alert_id'];
      $alert_name_report           = $_POST['alarm_name'];
      $image_url                   = $_POST['image_url'];
      $alarm_start_time            = $_POST['alarm_start_time'];
      $alarm_report_vehicle_no     = $_POST['alarm_report_vehicle_no'];
      $alarm_report_vehicle_device = $_POST['alarm_report_vehicle_device'];
      $tablenya                    = $_POST['tablenya'];
      $intervention_date           = $_POST['intervention_date'];
      $intervention_category       = $_POST['intervention_category'];
      $intervention_sid            = $_POST['intervention_sid'];
      $intervention_sid_cr         = $_POST['intervention_sid_cr'];
      $intervention_note           = $_POST['intervention_note'];

      $alarm_true_false            = $_POST['alarm_true_false'];
      $intervention_judgement      = $_POST['intervention_judgement'];
      $intervention_supervisor     = explode("|", $_POST['intervention_supervisor']);
      $intervention_working_type   = $_POST['intervention_working_type'];
      $fatigue_category         = $_POST['fatigue_category'];

      if ($intervention_sid == "") {
        $karyawan_bc_sid     = "";
        $karyawan_bc_name    = "";
        $karyawan_bc_id_sync = "";
      }else {
        $data_karyawan       = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_sid_cr);
        $data_operator       = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_sid);

        $karyawan_bc_sid     = $data_karyawan[0]['karyawan_bc_sid'];
        $karyawan_bc_name    = $data_karyawan[0]['karyawan_bc_name'];
        $karyawan_bc_id_sync = $data_karyawan[0]['karyawan_bc_id_sync'];
      }

      // echo "<pre>";
      // // var_dump($intervention_judgement.'-'.$intervention_supervisor.'-'.$intervention_working_type);die();
      // var_dump($data_operator);die();
      // echo "<pre>";

      $data_vehicle              = $this->m_development->getDataVehicleByDevice($alarm_report_vehicle_device);
      $data_evidence             = $this->getdataevidence($tablenya, $alert_id);
      $table_driver_change       = "ts_driver_change_new";
      $data_driver_detected      = $this->getdriverdetected($table_driver_change, $alarm_report_vehicle_no, $alarm_start_time);

      if (sizeof($data_driver_detected) > 0) {
          $driver_sid = $data_driver_detected[0]['change_driver_id'];
          $driver_name = $data_driver_detected[0]['change_driver_name'];
      }else {
        $driver_sid  = "";
        $driver_name = "";
      }

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

      $data_pja              = $this->m_development->master_pja();
      $json_data_pja         = json_decode($data_pja, true);
      $rand_data_pja         = array_rand($json_data_pja, 1);

      $alert_name = explode("Level", $data_evidence[0]->alarm_report_name);

      $data_company   = $this->m_development->getcompanybyID($user_company);

      if (isset($data_vehicle[0]['vehicle_tipe_unit_for_integrasi'])) {
        $tipe_unit_integrasi = $data_vehicle[0]['vehicle_tipe_unit_for_integrasi'];
      }else {
        $tipe_unit_integrasi = "pushalert";
      }

      if ($fatigue_category == 0) {
        $alert_alias = $this->m_development->getAlertAlias($alert_name_report);
      }else {
        $alert_alias = $this->m_development->getAlertAlias($fatigue_category);
      }

      if (sizeof($alert_alias) > 0) {
        $alarm_alias_fix = $alert_alias[0]['alarm_alias'];
      }else {
        $alarm_alias_fix = "";
      }

      $intervention_category_fix = explode("|", $intervention_category);

      // if ($alarm_true_false == 1) {
      //   $url_image = $image_url;
      //   // Get the image and convert into string
      //   $img = file_get_contents($url_image);
      //   // Encode the image string data into base64
      //   // $image_base64_fix = base64_encode($img);
      //   // $image = file_get_contents($url);
      //   if ($img !== false){
      //       $image_base64_fix = 'data:image/jpg;base64,'.base64_encode($img);
      //   }
      // }

      $data_site = $this->m_development->data_site_bc_name($data_vehicle[0]['vehicle_site']);
      $data_site_ts = $this->m_development->master_site_bc_byName("ts_bc_master_site", $data_vehicle[0]['vehicle_site']);

      // echo "<pre>";
      // // var_dump($fatigue_category.'-'.$alert_name_report.'-'.$fatigue_category);die();
      // var_dump($data_site_ts);die();
      // echo "<pre>";


      $data = array(
        "alarm_report_spvidsync_cr"             => $karyawan_bc_id_sync,
        "alarm_report_spvsid_cr"                => $karyawan_bc_sid,
        "alarm_report_spvname_cr"               => $karyawan_bc_name,
        "alarm_report_statusintervention_cr"    => 1,
        "alarm_report_intervention_category_cr" => $intervention_category_fix[1],
        "alarm_report_name_alias"               => $alarm_alias_fix,
        "alarm_report_fatiguecategory_cr"       => $fatigue_category,
        "alarm_report_note_cr"                  => $intervention_note,
        "alarm_report_datetime_cr"              => $intervention_date,
        "alarm_report_id_cr"                    => $data_operator[0]['karyawan_bc_id_sync'],
        "alarm_report_sid_cr"                   => $data_operator[0]['karyawan_bc_sid'],
        "alarm_report_name_cr"                  => $data_operator[0]['karyawan_bc_name'],
        "alarm_report_tipe_unit"                => $tipe_unit_integrasi,
        "alarm_report_deskripsi"                => $data_evidence[0]->alarm_report_vehicle_no." melakukan pelanggaran ".$alert_name[0].". Intervention Note : ".$intervention_note,
        "alarm_report_lokasi_detail"            => "Terjadi pelanggaran ".$alert_name[0]." di lokasi ".$data_evidence[0]->alarm_report_location_start,
        "alarm_report_id_kategori"              => $data_id_kategori[$rand_id_kategori],
        "alarm_report_id_quick_action"          => $data_id_quick_action[$rand_id_quickaction],
        "alarm_report_id_pja"                   => $json_data_pja[$rand_data_pja]['id'],
        "alarm_report_id_pja_child"             => 1229,
        "alarm_report_truefalse_up"             => $alarm_true_false,
        "alarm_report_judgement_cr"             => $intervention_judgement,
        // "alarm_report_supervisor_cr"         => $intervention_supervisor[0].'|'.$intervention_supervisor[1].'|'.$intervention_supervisor[2],
        "alarm_report_working_type"             => $intervention_working_type,
        "alarm_report_company_sidcode"          => $data_company[0]->company_sid_code,
        "alarm_report_id_lokasi"                => $data_site[0]['site_location_idsync'],
        "alarm_report_id_object"                => $data_site[0]['site_location_detail_idsync'],
        "alarm_report_master_site"              => $data_site_ts[0]['master_site_id_sync'],
        // "alarm_report_base64"                   => $image_base64_fix
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_development->update_post_event($tablenya, "alarm_report_id", $alert_id, $data);
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

    function submit_intervention_controlroom_overspeed(){
      $user_id                   = $_POST['user_id'];
      $user_name                 = $_POST['user_name'];
      $alert_id                  = $_POST['alert_id'];
      $alarm_start_time          = $_POST['alarm_start_time'];
      $alarm_report_vehicle_no   = $_POST['alarm_report_vehicle_no'];
      $alarm_report_vehicle_id   = $_POST['alarm_report_vehicle_id'];
      $tablenya                  = $_POST['tablenya'];
      $intervention_date         = $_POST['intervention_date'];
      // $intervention_category  = explode("|", $_POST['intervention_category']);
      $intervention_category     = $_POST['intervention_category'];
      // $itervention_sid        = explode("|", $_POST['itervention_sid']);
      $intervention_sid          = $_POST['intervention_sid'];
      $intervention_sid_cr       = $_POST['intervention_sid_cr'];
      // $alarm_true_false       = $_POST['alarm_true_false'];
      // $itervention_alarm      = $_POST['itervention_alarm'];
      $intervention_note         = $_POST['intervention_note'];
      $fatigue_category          = $_POST['fatigue_category'];
      $intervention_judgement    = $_POST['intervention_judgement'];
      $intervention_supervisor   = explode("|", $_POST['intervention_supervisor']);
      $id_lokasi                 = $_POST['id_lokasi'];
      $intervention_working_type = $_POST['intervention_working_type'];

      $m1               = date("F", strtotime($alarm_start_time));
      $year             = date("Y", strtotime($alarm_start_time));
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

      $data_vehicle         = $this->m_development->getDataVehicleById($alarm_report_vehicle_id);
      $data_evidence        = $this->m_development->get_overspeed_intensor_intervention_detail($dbtableoverspeed, $alert_id, $alarm_start_time);
      $table_driver_change  = "ts_driver_change_new";
      $data_driver_detected = $this->getdriverdetected($table_driver_change, $alarm_report_vehicle_no, $alarm_start_time);

      // echo "<pre>";
      // var_dump($data_driver_detected);die();
      // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$alarm_start_time);die();
      // echo "<pre>";

        if (sizeof($data_driver_detected) > 0) {
          $driver_sid = $data_driver_detected[0]['change_driver_id'];
          $driver_name = $data_driver_detected[0]['change_driver_name'];
        }else {
          $driver_sid  = "";
          $driver_name = "";
        }

        $data_operator          = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_sid);
        $data_cr                = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_sid_cr);
        $data_site              = $this->m_development->master_site_bc("ts_bc_master_site", $id_lokasi);
        $user_company = $this->sess->user_company;

        if ($user_company == 1963) {
          $location_id_sync = "114151";
        }elseif ($user_company == 1961) {
          $location_id_sync = "1172171933";
        }

        $data_location                            = $this->m_development->master_location_bc("ts_bc_master_location", $location_id_sync);
        // $data_hse_object        = $this->m_development->master_hse_object_bc("ts_bc_master_object"); // SEMENTARA BELUM DIGUNAKAN
        // $data_hse_object_detail = $this->m_development->master_object_detail(); // SEMENTARA BELUM DIGUNAKAN

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

        $data_site    = $this->m_development->data_site_bc_name($data_vehicle[0]['vehicle_site']);
        $data_site_ts = $this->m_development->master_site_bc_byName("ts_bc_master_site", $data_vehicle[0]['vehicle_site']);



        // echo "<pre>";
        // var_dump($data_site_ts);die();
        // echo "<pre>";

      $data = array(
        "overspeed_report_spvidsync_cr"             => $data_cr[0]['karyawan_bc_id_sync'],
        "overspeed_report_spvname_cr"               => $data_cr[0]['karyawan_bc_name'],
        "overspeed_report_spvsid_cr"                => $data_cr[0]['karyawan_bc_sid'],
        "overspeed_report_id_cr"                    => $data_operator[0]['karyawan_bc_id_sync'],
        "overspeed_report_sid_cr"                   => $data_operator[0]['karyawan_bc_sid'],
        "overspeed_report_name_cr"                  => $data_operator[0]['karyawan_bc_name'],
        "overspeed_report_statusintervention_cr"    => 1,
        "overspeed_report_intervention_category_cr" => $intervention_category,
        "overspeed_report_note_cr"                  => $intervention_note,
        "overspeed_report_judgement_cr"             => $intervention_judgement,
        // "overspeed_report_supervisor_cr"         => $intervention_supervisor[0].'|'.$intervention_supervisor[1].'|'.$intervention_supervisor[2],
        "overspeed_report_datetime_cr"              => $intervention_date,
        // "overspeed_report_sid_driver"               => $driver_sid,
        // "overspeed_report_driver_name"              => $driver_name,
        "overspeed_report_working_type"             => $intervention_working_type,
        "overspeed_report_tipe_unit"                => $data_vehicle[0]['vehicle_tipe_unit_for_integrasi'],
        // "overspeed_report_id_object"                => 3038, // Tingkah Laku Pekerja
        "overspeed_report_id_objectdetail"          => 30031228, // Tingkah Laku Pekerja
        "overspeed_report_goldenrule"               => 2,
        // "overspeed_report_id_perusahaan"         => $intervention_supervisor[0],
        "overspeed_report_id_perusahaan"            => $id_lokasi,
        "overspeed_report_deskripsi"                => $data_evidence[0]['overspeed_report_vehicle_no']." melakukan pelanggaran Overspeed. Intervention Note : ".$data_evidence[0]['overspeed_report_note_cr'],
        "overspeed_report_lokasi_detail"            => "Terjadi pelanggaran Overspeed di lokasi ".$data_evidence[0]['overspeed_report_location'],
        "overspeed_report_id_kategori"              => $data_id_kategori[$rand_id_kategori],
        "overspeed_report_id_quick_action"          => $data_id_quick_action[$rand_id_quickaction],
        "overspeed_report_id_pja"                   => $json_data_pja[$rand_data_pja]['id'],
        "overspeed_report_id_pja_child"             => 1229,
        "overspeed_report_id_lokasi"                => $data_site[0]['site_location_idsync'],
        "overspeed_report_id_object"                => $data_site[0]['site_location_detail_idsync'],
        "overspeed_report_master_site"              => $data_site_ts[0]['master_site_id_sync'],
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_securityevidence->update_post_event($dbtableoverspeed, "overspeed_report_id", $alert_id, $data);
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

    function data_intervention_note(){
      $intervention_type_id = $_POST['interv_type_id'];

      $data_type_note       = $this->m_development->get_type_note($intervention_type_id);

      // echo "<pre>";
      // var_dump($data_type_note);die();
      // echo "<pre>";

      echo json_encode(array("data" => $data_type_note, "code" => 200));

    }

    function getdataevidence($table, $alert_id){
      $this->dbtrip = $this->load->database("tensor_report", true);
      $this->dbtrip->select("*");
  		$this->dbtrip->where("alarm_report_id", $alert_id); //photo
  		$q = $this->dbtrip->get($table)->result();
      return $q;
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

    // DASHBOARD INTERVENTIOON PART 2 START
    function dashboardintervention_2(){
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
  		// var_dump($type_intervention);die();
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
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
    }

    function searchreport_controlroom_2(){
  		ini_set('display_errors', 1);
  		//ini_set('memory_limit', '2G');
  		if (! isset($this->sess->user_type))
  		{
  			redirect(base_url());
  		}

  		$company       = $this->input->post('company');
  		$vehicle       = $this->input->post('vehicle');
  		$startdate     = $this->input->post('startdate');
  		$enddate       = $this->input->post('enddate');
  		$shour         = $this->input->post('shour');
  		$ehour         = $this->input->post('ehour');
  		$alarmtype     = $this->input->post('alarmtype');
  		$periode       = $this->input->post('periode');
  		$km            = $this->input->post('km');
  		// $reporttype = $this->input->post("reporttype");
  		$reporttype = 0;
  		$alarmtypefromaster = array();

      if ($alarmtype == 999999) {
  			$alarmtypefromaster[] = 9999;
  		}else {
        if ($alarmtype != "All") {
          $alarmbymaster = $this->m_securityevidence->getalarmbytype($alarmtype);
          $alarmtypefromaster = array();
          for ($i=0; $i < sizeof($alarmbymaster); $i++) {
            $alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
          }
        }
  		}

  		// echo "<pre>";
  		// var_dump($company.'-'.$vehicle.'-'.$periode);die();
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
        // if (date("d") == 01) {
          $sdate = date("Y-m-d 00:00:00");
        // }else {
        //   $sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
        // }
  			$edate = date("Y-m-d H:i:s");
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

  		// echo "<pre>";
  		// var_dump($vehicle.'-'.$company.'-'.$privilegecode);die();
  		// echo "<pre>";

      // GET DATA ALERT OVERSPEED
      $data_array_alert = array();
  		$data_overspeed   = $this->m_development->get_overspeed_intensor_intervention($dbtable_overspeed, $vehicle, $company, $sdate, $edate);

      // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

        // echo "<pre>";
        // var_dump($data_overspeed);die();
        // // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
        // echo "<pre>";

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

        if (isset($data_overspeed[$i]['overspeed_report_note_up'])) {
          $overspeed_report_note_up =  $data_overspeed[$i]['overspeed_report_note_up'];
        }else {
          $overspeed_report_note_up = "";
        }

  			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
  			array_push($data_array_alert, array(
  				"isfatigue"                                => "no",
          "alarm_report_id"                          => $data_overspeed[$i]['overspeed_report_id'],
          "alarm_report_imei"                        => $data_overspeed[$i]['overspeed_report_imei'],
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
  			));
  		}


  			// echo "<pre>";
  			// var_dump($alarmtype);die();
        // // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$edate);die();
        // // var_dump($alarmtypefromaster);die();
  			// echo "<pre>";


      // GET DATA ALERT MDVR
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

    		$nowday            = date("d");
    		$end_day_fromEdate = date("d", strtotime($edate));

    		if ($nowday == $end_day_fromEdate) {
    			$edate = date("Y-m-d H:i:s");
    		}

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
        $this->dbtrip->where("alarm_report_statusintervention_cr != ", 0);
        $this->dbtrip->where("alarm_report_statusintervention_cr != ","");
    		// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
    		$this->dbtrip->order_by("alarm_report_start_time","asc");
    		$this->dbtrip->group_by("alarm_report_start_time");
    		$q          = $this->dbtrip->get($dbtable);
        $rows       = $q->result_array();
        $thisreport = $rows;

    		// echo "<pre>";
    		// var_dump($rows);die();
    		// echo "<pre>";

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

            if (isset($thisreport[$j]['alarm_report_note_up'])) {
              $alarm_report_note_up =  $thisreport[$j]['alarm_report_note_up'];
            }else {
              $alarm_report_note_up = "";
            }

    				array_push($data_array_alert, array(
              "isfatigue"                                => "yes",
              "alarm_report_id"                          => $thisreport[$j]['alarm_report_id'],
              "alarm_report_imei"                        => $thisreport[$j]['alarm_report_imei'],
    					"alarm_report_vehicle_id"                  => $thisreport[$j]['alarm_report_vehicle_id'],
    					"alarm_report_vehicle_no"                  => $thisreport[$j]['alarm_report_vehicle_no'],
    					"alarm_report_vehicle_name"                => $thisreport[$j]['alarm_report_vehicle_name'],
              "alarm_report_type"                        => $thisreport[$j]['alarm_report_type'],
    					"alarm_report_name"                        => $alarmreportnamefix,
    					"alarm_report_start_time"                  => $thisreport[$j]['alarm_report_start_time'],
    					"alarm_report_end_time"                    => $thisreport[$j]['alarm_report_end_time'],
    					"alarm_report_coordinate_start"            => $thisreport[$j]['alarm_report_coordinate_start'],
    					"alarm_report_coordinate_end"              => $thisreport[$j]['alarm_report_coordinate_end'],
    					"alarm_report_location_start"              => $thisreport[$j]['alarm_report_location_start'],
    					"alarm_report_speed" 			                 => $thisreport[$j]['alarm_report_speed'],
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
    				));
    		}
      }

      usort($data_array_alert, function($a, $b) {
         return strtotime($b['alarm_report_datetime_cr']) - strtotime($a['alarm_report_datetime_cr']);
     });


     $data_array_fix = array();
     if($alarmtype == 999999) {
       for ($i=0; $i < sizeof($data_array_alert); $i++) {
       $violation_type = $data_array_alert[$i]['alarm_report_name'];
         if ($violation_type == "Overspeed") {
           array_push($data_array_fix, array(
             "isfatigue"                               => "no",
             "alarm_report_id"                         => $data_array_alert[$i]['alarm_report_id'],
             "alarm_report_imei"                       => $data_array_alert[$i]['alarm_report_imei'],
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
             "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
             "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
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
               "alarm_report_imei"                       => $data_array_alert[$i]['alarm_report_imei'],
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
               "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
               "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
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
  		$html                      = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_intervention_result', $this->params, true);
  		$callback["html"]          = $html;
  		$callback["report"]        = $data_array_fix;

  		echo json_encode($callback);
  	}

    function intervention_detail_controlroom(){
  		$alert_id          = $this->input->post("alert_id");
  		$sdate             = $this->input->post("sdate");
      $alarm_report_id   = $this->input->post("alarm_report_id");
      $alarm_report_imei = $this->input->post("alarm_report_imei");
      $alarmtype         = $this->input->post("alarmtype");
      // $alarmtype      = $this->input->post("alarmtype");
  		$report            = "alarm_evidence_";
  		$reportoverspeed   = "overspeed_hour_";
  		$monthforparam     = date("m", strtotime($sdate));
  		$m1                = date("F", strtotime($sdate));
  		$year              = date("Y", strtotime($sdate));
  		$jalur             = "";

  		// echo "<pre>";
  		// var_dump($alarm_report_imei);die();
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
    		$data_overspeed   = $this->m_development->get_overspeed_intensor_intervention_detail($dbtableoverspeed, $alarm_report_id, $sdate);

          // echo "<pre>";
          // var_dump($data_overspeed);die();
          // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$sdate);die();
          // echo "<pre>";

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
            "overspeed_report_level_alias"             => $data_overspeed[$i]['overspeed_report_level_alias'],
            "alarm_report_speed_time" 		             => "",
            "alarm_report_speed_status" 	             => "",
            "alarm_report_jalur" 	                     => $data_overspeed[$i]['overspeed_report_jalur'],
            "alarm_report_id_cr" 	                     => $overspeed_report_id_cr,
            "alarm_report_name_cr" 	                   => $overspeed_report_name_cr,
            "alarm_report_sid_cr" 	                   => $overspeed_report_sid_cr,
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
        $this->params['type_intervention']   = $type_intervention;

        $type_note                           = $this->m_development->get_type_note(1);
        $this->params['type_note']           = $type_note;

        $data_karyawan_bc                    = $this->m_development->check_data_karyawan();
        $this->params['data_karyawan_all']    = $data_karyawan_bc;

        $data_karyawan          = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_name);
        $this->params['data_karyawan']    = $data_karyawan;


        $this->params['content']             = $data_array_alert;
        $this->params['alert_id']            = $alarm_report_id;
        $this->params['alarmtype']           = $alarmtype;
        $this->params['tablenya']            = $dbtableoverspeed;

        $this->params['monthforparam'] 			 = $monthforparam;
        $this->params['year'] 			         = $year;
        $this->params['user_id_role'] 			 = $this->sess->user_id_role;
        $html                                = $this->load->view('newdashboard/development/dashboard/controlroom/v_intervention_modal_overspeed', $this->params, true);
        $callback["report"]                  = $data_array_alert;
      }else {
        $reportdetail               = $this->m_securityevidence->getdetailreport_intervention($table, $alert_id, $alarm_report_imei, $sdate);
    		$reportdetailvideo          = $this->m_securityevidence->getdetailreportvideo_intervention($table, $alert_id, $alarm_report_imei, $sdate);
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

          if (isset($reportdetaildecode[4])) {
            $speedgps                          = number_format($reportdetaildecode[4]/10, 1, '.', '');
          }else {
            $speedgps = 0;
          }
    			//$speedgps                        = $reportdetail[0]['alarm_report_speed']; //by speed gps TK510

    			$alarm_report_coordinate_start     = $reportdetail[0]['alarm_report_coordinate_start'];

          $data_site                         = $this->m_development->master_site_bc_all("ts_bc_master_site");
          $this->params['data_site']         = $data_site;

          $type_intervention                 = $this->m_development->get_type_intervention();
          $this->params['type_intervention'] = $type_intervention;

          $type_note                         = $this->m_development->get_type_note(1);
          $this->params['type_note']         = $type_note;

          $data_karyawan_bc                  = $this->m_development->check_data_karyawan();
          $this->params['data_karyawan_all'] = $data_karyawan_bc;

          $data_karyawan                     = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $this->sess->user_name);
          $this->params['data_karyawan']     = $data_karyawan;

          $alarmreportnamefix = "";
    			$alarmreporttype = $reportdetail[0]['alarm_report_type'];

          if ($alarmreporttype == 702) {
            $alarmreportnamefix = "Distracted Driving Alarm Level One Start";
          }elseif ($alarmreporttype == 703) {
            $alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
          }elseif ($alarmreporttype == 752) {
            $alarmreportnamefix = "Distracted Driving Alarm Level One End";
          }elseif ($alarmreporttype == 753) {
            $alarmreportnamefix = "Distracted Driving Alarm Level Two End";
          }else {
            $alarmreportnamefix = $reportdetail[0]['alarm_report_name'];
          }


    		// echo "<pre>";
    		// var_dump($data_karyawan_bc);die();
    		// echo "<pre>";

    		$this->params['content']              = $reportdetail;
        $this->params['alarm_name']           = $alarmreportnamefix;
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
    		$html                                 = $this->load->view('newdashboard/development/dashboard/controlroom_2/v_intervention_modal', $this->params, true);
        $callback["report"]                   = $reportdetail;
      }


  		$callback["html"]                     = $html;
  		echo json_encode($callback);
  	}

    function submit_intervention_controlroom(){
      $user_id                     = $_POST['user_id'];
      $id_lokasi                   = $_POST['id_lokasi'];
      $user_name                   = $_POST['user_name'];
      $alert_id                    = $_POST['alert_id'];
      $alarm_start_time            = $_POST['alarm_start_time'];
      $alarm_report_vehicle_no     = $_POST['alarm_report_vehicle_no'];
      $alarm_report_vehicle_device = $_POST['alarm_report_vehicle_device'];
      $tablenya                    = $_POST['tablenya'];
      $intervention_date           = $_POST['intervention_date'];
      // $intervention_category    = explode("|", $_POST['intervention_category']);
      // $intervention_category       = $_POST['intervention_category'];
      // $itervention_sid             = explode("|", $_POST['itervention_sid']);
      $intervention_sid             = $_POST['intervention_sid'];
      // $alarm_true_false         = $_POST['alarm_true_false'];
      // $itervention_alarm        = $_POST['itervention_alarm'];
      // $intervention_note           = $_POST['intervention_note'];
      $fatigue_category            = $_POST['fatigue_category'];
      $intervention_judgement      = $_POST['intervention_judgement'];
      $intervention_supervisor     = explode("|", $_POST['intervention_supervisor']);
      $intervention_working_type   = $_POST['intervention_working_type'];

      // $m1      = date("F", strtotime($alarm_start_time));
    	// $year    = date("Y", strtotime($alarm_start_time));
    	// $report  = "alarm_evidence_";
      // $dbtable = "";
      //
    	// switch ($m1)
    	// {
    	// 	case "January":
    	// 				$dbtable = $report."januari_".$year;
    	// 	break;
    	// 	case "February":
    	// 				$dbtable = $report."februari_".$year;
    	// 	break;
    	// 	case "March":
    	// 				$dbtable = $report."maret_".$year;
    	// 	break;
    	// 	case "April":
    	// 				$dbtable = $report."april_".$year;
    	// 	break;
    	// 	case "May":
    	// 				$dbtable = $report."mei_".$year;
    	// 	break;
    	// 	case "June":
    	// 				$dbtable = $report."juni_".$year;
    	// 	break;
    	// 	case "July":
    	// 				$dbtable = $report."juli_".$year;
    	// 	break;
    	// 	case "August":
    	// 				$dbtable = $report."agustus_".$year;
    	// 	break;
    	// 	case "September":
    	// 				$dbtable = $report."september_".$year;
    	// 	break;
    	// 	case "October":
    	// 				$dbtable = $report."oktober_".$year;
    	// 	break;
    	// 	case "November":
    	// 				$dbtable = $report."november_".$year;
    	// 	break;
    	// 	case "December":
    	// 				$dbtable = $report."desember_".$year;
    	// 	break;
    	// }

      $data_karyawan             = $this->m_development->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_sid);
      $data_site                 = $this->m_development->master_site_bc("ts_bc_master_site", $id_lokasi);
      $user_company = $this->sess->user_company;

      if ($user_company == 1963) {
        $location_id_sync = "114151";
      }elseif ($user_company == 1961) {
        $location_id_sync = "1172171933";
      }

      $data_location                            = $this->m_development->master_location_bc("ts_bc_master_location", $location_id_sync);
      // $data_hse_object        = $this->m_development->master_hse_object_bc("ts_bc_master_object"); // SEMENTARA BELUM DIGUNAKAN
      // $data_hse_object_detail = $this->m_development->master_object_detail(); // SEMENTARA BELUM DIGUNAKAN

      $data_vehicle              = $this->m_development->getDataVehicleByDevice($alarm_report_vehicle_device);
      $data_evidence             = $this->getdataevidence($tablenya, $alert_id);
      $table_driver_change       = "ts_driver_change_new";
      $data_driver_detected      = $this->getdriverdetected($table_driver_change, $alarm_report_vehicle_no, $alarm_start_time);

      if (sizeof($data_driver_detected) > 0) {
          $driver_sid = $data_driver_detected[0]['change_driver_id'];
          $driver_name = $data_driver_detected[0]['change_driver_name'];
      }else {
        $driver_sid  = "";
        $driver_name = "";
      }

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

      $data_pja              = $this->m_development->master_pja();
      $json_data_pja         = json_decode($data_pja, true);
      $rand_data_pja         = array_rand($json_data_pja, 1);

      $alert_name = explode("Level", $data_evidence[0]->alarm_report_name);

      // echo "<pre>";
      // var_dump($data_evidence);die();
      // echo "<pre>";

      if (isset($data_vehicle[0]['vehicle_tipe_unit_for_integrasi'])) {
        $tipe_unit_integrasi = $data_vehicle[0]['vehicle_tipe_unit_for_integrasi'];
      }else {
        $tipe_unit_integrasi = "pushalert";
      }

      // echo "<pre>";
      // var_dump($fatigue_category);die();
      // echo "<pre>";

      $data = array(
        "alarm_report_id_cr"                    => $user_id,
        "alarm_report_name_cr"                  => $data_karyawan[0]['karyawan_bc_name'],
        "alarm_report_sid_cr"                   => $data_karyawan[0]['karyawan_bc_sid'],
        "alarm_report_statusintervention_cr"    => 1,
        "alarm_report_judgement_cr"             => $intervention_judgement,
        "alarm_report_supervisor_cr"            => $intervention_supervisor[0].'|'.$intervention_supervisor[1].'|'.$intervention_supervisor[2],
        "alarm_report_working_type"             => $intervention_working_type,
        "alarm_report_master_site"              => $data_site[0]['master_site_id_sync'],
        "alarm_report_tipe_unit"                => $tipe_unit_integrasi,
        "alarm_report_id_lokasi"                => $data_site[0]['master_site_id_sync'],
        "alarm_report_id_lokasi_detail"         => $data_location[0]['master_location_id_sync'],
        "alarm_report_id_object"                => 3038, // Tingkah Laku Pekerja
        "alarm_report_id_objectdetail"          => 30031228, // Tingkah Laku Pekerja
        "alarm_report_goldenrule"               => 2,
        "alarm_report_fatiguecategory_cr"       => $fatigue_category,
        // "alarm_report_id_perusahaan"            => $intervention_supervisor[0],
        "alarm_report_id_perusahaan"            => $id_lokasi, // INI NANTI DI HARDCODE
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $update = $this->m_securityevidence->update_post_event($tablenya, "alarm_report_id", $alert_id, $data);
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
    // DASHBOARD INTERVENSION PART 2 END

    // DASHBOARD INTERVENTION EXISTING START
    function intervention_development(){
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
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/intervention/v_dashboard_postevent', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
    }

    function search_intervention_existing(){
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
        // var_dump($data_overspeed);die();
        // echo "<pre>";

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
            "alarm_report_id_cr" 	                     => $overspeed_report_id_cr,
            "alarm_report_name_cr" 	                   => $overspeed_report_name_cr,
            "alarm_report_sid_cr" 	                   => $overspeed_report_sid_cr,
            "alarm_report_statusintervention_cr" 	     => $overspeed_report_statusintervention_cr,
            "alarm_report_intervention_category_cr" 	 => $overspeed_report_intervention_category_cr,
            "alarm_report_fatiguecategory_cr" 	       => "0",
            "alarm_report_note_cr" 	                   => $overspeed_report_note_cr,
            "alarm_report_datetime_cr" 	               => $overspeed_report_datetime_cr,
            "alarm_report_note_up" 	                   => $overspeed_report_note_up,
    			));
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

  				array_push($data_array_alert, array(
            "alarm_report_id"                          => $thisreport[$j]['alarm_report_id'],
  					"alarm_report_vehicle_id"                  => $thisreport[$j]['alarm_report_vehicle_id'],
  					"alarm_report_vehicle_no"                  => $thisreport[$j]['alarm_report_vehicle_no'],
  					"alarm_report_vehicle_name"                => $thisreport[$j]['alarm_report_vehicle_name'],
            "alarm_report_type"                        => $thisreport[$j]['alarm_report_type'],
  					"alarm_report_name"                        => $alarmreportnamefix,
  					"alarm_report_start_time"                  => $thisreport[$j]['alarm_report_start_time'],
  					"alarm_report_end_time"                    => $thisreport[$j]['alarm_report_end_time'],
  					"alarm_report_coordinate_start"            => $thisreport[$j]['alarm_report_coordinate_start'],
  					"alarm_report_coordinate_end"              => $thisreport[$j]['alarm_report_coordinate_end'],
  					"alarm_report_location_start"              => $thisreport[$j]['alarm_report_location_start'],
  					"alarm_report_speed" 			                 => $thisreport[$j]['alarm_report_speed'],
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
             "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
             "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
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
               "alarm_report_datetime_cr" 	             => $data_array_alert[$i]['alarm_report_datetime_cr'],
               "alarm_report_note_up" 	                 => $data_array_alert[$i]['alarm_report_note_up'],
             ));
           }
       }
     }else {
       $data_array_fix = $data_array_alert;
     }

     $total_alert_fix = sizeof($data_array_fix);
     $time_alert_fix  = $data_array_fix[0]['alarm_report_start_time'];

     // echo "<pre>";
     // var_dump($total_alert_fix.'|'.$time_alert_fix);die();
     // echo "<pre>";

  		$this->params['content']   = $data_array_fix;
      $this->params['alarmtype'] = $alarmtype;
  		$html                      = $this->load->view('newdashboard/development/dashboard/intervention/v_postevent_result', $this->params, true);
  		$callback["html"]          = $html;
  		$callback["report"]        = $data_array_alert;
      $callback["time_alert"]    = $time_alert_fix;

  		echo json_encode($callback);
  	}

    function getinfodetail_new_existing(){
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
  		$html                                 = $this->load->view('newdashboard/development/dashboard/intervention/v_postevent_infodetail', $this->params, true);
  		$callback["html"]                     = $html;
  		$callback["report"]                   = $reportdetail;
  		echo json_encode($callback);
  	}

    function intervention_detail_existing(){
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
        $html                                = $this->load->view('newdashboard/development/dashboard/intervention/v_intervention_modal_overspeed', $this->params, true);
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
    		// $html                                 = $this->load->view('newdashboard/dashboardberau/intervention/v_postevent_modal', $this->params, true);
        $html                                = $this->load->view('newdashboard/development/dashboard/intervention/v_postevent_modal', $this->params, true);
    		$callback["html"]                     = $html;
    		$callback["report"]                   = $reportdetail;
      }
      echo json_encode($callback);
  	}

    function submit_intervention_existing(){
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

    function data_note_existing(){
      $intervention_type_id = $_POST['interv_type_id'];

      $data_type_note       = $this->m_dashboardberau->get_type_note($intervention_type_id);

      // echo "<pre>";
      // var_dump($data_type_note);die();
      // echo "<pre>";

      echo json_encode(array("data" => $data_type_note, "code" => 200));

    }

    function submit_intervention_overspeed_existing(){
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

      // echo "<pre>";
      // var_dump($data_evidence);die();
      // // var_dump($dbtableoverspeed.'-'.$alert_id.'-'.$alert_date);die();
      // echo "<pre>";

        if (sizeof($data_driver_detected) > 0) {
          $driver_sid = $data_driver_detected[0]['change_driver_id'];
          $driver_name = $data_driver_detected[0]['change_driver_name'];
        }else {
          $driver_sid  = "";
          $driver_name = "";
        }

        $data_karyawan          = $this->m_dashboardberau->check_data_karyawan_by_sid("ts_karyawan_beraucoal", $intervention_sid[0]);
        // $data_site              = $this->m_dashboardberau->master_site_bc("ts_bc_master_site", $id_lokasi); // NANTI TUNGGU HARDCODE DARI BC
        $user_company = $this->sess->user_company;

        if ($user_company == 1963) {
          $location_id_sync = "114151";
        }elseif ($user_company == 1961) {
          $location_id_sync = "1172171933";
        }

        $data_location                            = $this->m_development->master_location_bc("ts_bc_master_location", $location_id_sync);
        // $data_hse_object        = $this->m_dashboardberau->master_hse_object_bc("ts_bc_master_object"); // SEMENTARA BELUM DIGUNAKAN
        // $data_hse_object_detail = $this->m_dashboardberau->master_object_detail(); // SEMENTARA BELUM DIGUNAKAN



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
    // DASHBOARD INTERVENTION EXISTING END























}