<?php
include "base.php";
// require_once APPPATH."/third_party/Classes/PHPExcel.php";

class Simcard extends Base
{
    function __construct()
    {
        parent::Base();
        $this->load->model("dashboardmodel");
        $this->load->model("m_operational");
        $this->load->model("m_development");
        $this->load->model("gpsmodel");
        $this->load->model("m_poipoolmaster");
        $this->load->model("m_simcard");
    }


    function index(){
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
      $companyid                   = $this->sess->user_company;
      $user_dblive                 = $this->sess->user_dblive;

      $this->params['data_simcard'] = $this->m_simcard->getAllData("simcard");

      // echo "<pre>";
      // var_dump($this->params['data_simcard']);die();
      // echo "<pre>";

      $this->params['url_code_view']  = "1";
      $this->params['code_view_menu'] = "monitoradminonly";
      $this->params['maps_code']      = "morehundred";
      // echo "<pre>";
      // var_dump($getvehicle_byowner);die();
      // echo "<pre>";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      }elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      }elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      }elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      }elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      }elseif ($privilegecode == 6) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
      }else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_home_simcard', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function addSimcard(){
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

      $datafix                     = array();
      $deviceidygtidakada          = array();

      for ($i=0; $i < sizeof($mastervehicle); $i++) {
        $jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
        $auto_status   = $jsonautocheck->auto_status;

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

      // echo "<pre>";
      // var_dump($company);die();
      // echo "<pre>";

      $this->params['url_code_view']  = "1";
      $this->params['code_view_menu'] = "monitoradminonly";
      $this->params['maps_code']      = "morehundred";

      $this->params['vehicles']       = $datafix;
      $this->params['vehicletotal']   = sizeof($mastervehicle);

      // echo "<pre>";
      // var_dump($this->params['vehicles']);die();
      // echo "<pre>";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      }elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      }elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      }elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      }elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      }elseif ($privilegecode == 6) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
      }else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/simcard/v_add_data', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function save_data_simcard(){
      $simcard_vehicle        = explode("|", $this->input->post("simcard_vehicle_no"));
      $simcard_vehicle_id     = $simcard_vehicle[0];
      $simcard_vehicle_device = $simcard_vehicle[1];
      $simcard_vehicle_no     = $simcard_vehicle[2];
      $simcard_vehicle_name   = $simcard_vehicle[3];
      $simcard_number         = $this->input->post("simcard_number");
      $simcard_type           = $this->input->post("simcard_type");
      $simcard_last_topup     = $this->input->post("simcard_last_topup");
      $simcard_aps            = $this->input->post("simcard_aps");
      $simcard_remark         = $this->input->post("simcard_remark");

      if ($simcard_vehicle == 0) {
        $error               = "Please choose vehicle";
  			$callback['error']   = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
      }

      if ($simcard_number == "" || $simcard_number == 0) {
        $error               = "Please fill simcard number";
  			$callback['error']   = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
      }

      if ($simcard_last_topup == "" || $simcard_last_topup == 0) {
        $error               = "Please fill last top up date";
  			$callback['error']   = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
      }

      $checksimcard = $this->m_simcard->chekcthissimcard($simcard_number);

      if (sizeof($checksimcard) > 0) {
        $error               = "Simcard number already in database";
  			$callback['error']   = true;
  			$callback['message'] = $error;

  			echo json_encode($callback);
  			return;
      }

      // echo "<pre>";
      // var_dump($checksimcard);die();
      // echo "<pre>";

      $data = array(
        "simcard_user_id"        => $this->sess->user_id,
        "simcard_user_name"      => $this->sess->user_name,
        "simcard_vehicle_id"     => $simcard_vehicle_id,
        "simcard_vehicle_device" => $simcard_vehicle_device,
        "simcard_vehicle_no"     => $simcard_vehicle_no,
        "simcard_vehicle_name"   => $simcard_vehicle_name,
        "simcard_number"         => $simcard_number,
        "simcard_type"           => $simcard_type,
        "simcard_last_topup"     => $simcard_last_topup,
        "simcard_expired"        => date("Y-m-d", strtotime($simcard_last_topup."+30 Day")),
        "simcard_aps"            => $simcard_aps,
        "simcard_remark"         => $simcard_remark,
        "simcard_created"        => date("Y-m-d H:i:s")
      );

      // echo "<pre>";
      // var_dump($data);die();
      // echo "<pre>";

      $insert = $this->m_simcard->insertData("simcard", $data);
        if ($insert) {
          $data_insert_historikal = array(
            "historikal_simcard_vehicleid"     => $simcard_vehicle_id,
            "historikal_simcard_vehicledevice" => $simcard_vehicle_device,
            "historikal_simcard_vehicle_no"    => $simcard_vehicle_no,
            "historikal_simcard_vehicle_name"  => $simcard_vehicle_name,
            "historikal_simcard_number"        => $simcard_number,
            "historikal_simcard_type"          => $simcard_type,
            "historikal_simcard_lasttopup"     => $simcard_last_topup,
            "historikal_simcard_expired"       => $data['simcard_expired'],
            "historikal_simcard_createddate"   => date("Y-m-d H:i:s"),
            "historikal_simcard_action"        => "ADD"
          );
          $insert_historikal = $this->m_simcard->insertDatatoTensor("historikal_simcard", $data_insert_historikal);
            if ($insert_historikal) {
              $error               = "Successfully insert data simcard";
        			$callback['error']   = false;
        			$callback['message'] = $error;

        			echo json_encode($callback);
            }else {
              $error               = "Failed insert data simcard";
        			$callback['error']   = true;
        			$callback['message'] = $error;

        			echo json_encode($callback);
            }
        }
    }

    function delete_simcard(){
      $simcard_id = $this->input->post("id");

      $data = array(
        "simcard_flag" => 1
      );

      // echo "<pre>";
      // var_dump($simcard_id);die();
      // echo "<pre>";

      $update = $this->m_simcard->deletethissimcard($simcard_id, $data);
        if ($update) {
          $error               = "Successfully delete data";
          $callback['error']   = false;
          $callback['message'] = $error;

          echo json_encode($callback);
        }else {
          $error               = "Failed delete data";
          $callback['error']   = true;
          $callback['message'] = $error;

          echo json_encode($callback);
        }
    }






































}
