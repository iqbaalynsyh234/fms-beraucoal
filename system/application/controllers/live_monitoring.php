<?php
include "base.php";
require_once APPPATH."/third_party/Classes/PHPExcel.php";

class Live_monitoring extends Base
{
    function __construct()
    {
        parent::Base();
        $this->load->model("dashboardmodel");
        $this->load->model("m_operational");
        $this->load->model("m_live_monitoring");
        $this->load->model("gpsmodel");
        $this->load->model("m_poipoolmaster");
        $this->load->model("m_securityevidence");
        $this->load->model("m_dashboardberau");
    }


    // STREAMING DASHBOARD START
    function index(){
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
        $master_type_unit                = $this->m_live_monitoring->get_type_unit();
        $mastervehicle                   = $this->m_live_monitoring->getmastervehicleforheatmap();

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
        $this->params['poolmaster']   = $this->m_live_monitoring->getalldata("webtracking_poi_poolmaster");
        $getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
        // echo "<pre>";
        // var_dump($datafix);die();
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

        $user_license_id = $this->sess->user_license_id;

        if ($user_license_id == "" || $user_license_id == NULL) {
          $sid_where = "all";
        }else {
          // $sid_where = $this->sess->user_login;
          $sid_where = "all";
        }
        $company_id_beats = $this->sess->user_company_id_beats;

        $data_karyawan_bc                    = $this->m_live_monitoring->check_data_karyawan_by_sid2("ts_karyawan_beraucoal", $sid_where, $company_id_beats);
        $this->params['data_karyawan_bc']    = $data_karyawan_bc;

        // TYPE INTERVENTION
        $type_intervention                   = $this->m_live_monitoring->get_type_intervention();
        $this->params['type_intervention']   = $type_intervention;
        // NOTES TYPE
        $type_note                           = $this->m_live_monitoring->get_type_note(1);
        $this->params['type_note']           = $type_note;

        $this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
        $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
        $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
        $this->params['mapsetting']     = $this->m_live_monitoring->getmapsetting();
        $this->params['poolmaster']     = $this->m_live_monitoring->getalldata("webtracking_poi_poolmaster");

        $this->params['data_vehicle']   = $this->m_live_monitoring->getvehicle_bycompany_master($user_company);
        $this->params['vehicle_site']   = $this->m_live_monitoring->vehicle_site_by_company($user_company);

        // GET MASTER SITE BC FROM WEBTRACKING SITE
        $data_site_bc  = $this->m_live_monitoring->data_site_bc();
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
        // var_dump($this->params['vehicledata']);die();
        // echo "<pre>";

          $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
          // $this->params["header"]         = $this->load->view('newdashboard/partial/headernew_livemonitoring', $this->params, true);
          $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

          if ($privilegecode == 1) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
          } elseif ($privilegecode == 2) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
          } elseif ($privilegecode == 3) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
          } elseif ($privilegecode == 4) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
          } elseif ($privilegecode == 5) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
          }elseif ($privilegecode == 6) {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
              $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
          } else {
              $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
              $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_dashboard_livemonitoring', $this->params, true);
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
      $masterdatavehicle = $this->m_live_monitoring->vehicle_by_mitra($id_mitra, $user_id_parent, $privilegecode, $user_parent, $vehicle, $filter_unit, $site_option);
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
      $masterdatavehicle = $this->m_live_monitoring->vehicle_by_mitra($id_mitra, $user_id_parent, $privilegecode, $user_parent, $vehicle, $filter_unit, $site_option);
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

      $html                               = $this->load->view('newdashboard/dashboardberau/livemonitoring/v_page_streaming_permitra', $this->params, true);
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

      $mastervehicle     = $this->m_live_monitoring->searchmasterdata("webtracking_vehicle", $keyfix, $filter_unit, $site_option);
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

        $masterdevice = $this->m_live_monitoring->getDataVehicleByDevice2($intervention_vehicle);

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

        $update = $this->m_live_monitoring->insert_manual_intervention($tablenya, $data);
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
    // STREAMING DASHBOARD END

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

      $this->params['data']           = $this->m_live_monitoring->getdevice();
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
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      }elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      }elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      }elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      }elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      }elseif ($privilegecode == 6) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
      }else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/dashboardberau/manualevidence/v_dashboard_postevent', $this->params, true);
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

          if ($thisreport[$j]['alarm_report_supervisor_cr'] != "") {
            $supervisor = explode("|", $thisreport[$j]['alarm_report_supervisor_cr']);
            $supervisor_sid = $supervisor[0];
            $supervisor_name = $supervisor[1];
          }else {
            $supervisor_sid = "";
            $supervisor_name = "";
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
            "alarm_report_supervisor_sid"              => $supervisor_sid,
            "alarm_report_supervisor_name"             => $supervisor_name,
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
      $html                      = $this->load->view('newdashboard/dashboardberau/manualevidence/v_postevent_result', $this->params, true);
      $callback["html"]          = $html;
      $callback["report"]        = $data_array_alert;

      echo json_encode($callback);
    }

    function get_info_manualevidence(){
      $alert_id = $this->input->post("alert_id");
      $sdate    = $this->input->post("sdate");
      $table    = "alarm_evidence_manual";

      $reportdetail               = $this->m_live_monitoring->getdetailreport($table, $alert_id, $sdate);

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
      $html                                 = $this->load->view('newdashboard/dashboardberau/manualevidence/v_postevent_infodetail', $this->params, true);
      $callback["html"]                     = $html;
      $callback["report"]                   = $reportdetail;
      echo json_encode($callback);
    }

    function getalarmsubcat(){
  		$subcategoryid                = $this->input->post("id");
  		$callback['alarmsubcategory'] = $this->m_securityevidence->getalarmsubcategory($subcategoryid);

  		echo json_encode($callback);
  	}









}
