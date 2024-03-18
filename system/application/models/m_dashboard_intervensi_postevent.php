<?php
class m_dashboard_intervensi_postevent extends Model{

  function data_karyawan_bymitra_jabatan($company_id, $jabatan){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($company_id != "all") {
        $this->dbts->where("karyawan_bc_company_id", $company_id);
      }

      if ($jabatan != "all") {
        $this->dbts->where_in("karyawan_bc_position", $jabatan);
      }
    $this->dbts->order_by("karyawan_bc_name", "ASC");
    $result = $this->dbts->get("ts_karyawan_beraucoal")->result_array();
    return $result;
  }

  function data_karyawan_bymitra_layer2up($company_id){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($company_id != "all") {
        $this->dbts->where("karyawan_bc_company_id", $company_id);
      }

    $this->dbts->where("karyawan_bc_position !=", "Operator");
    $this->dbts->order_by("karyawan_bc_name", "ASC");
    $result = $this->dbts->get("ts_karyawan_beraucoal")->result_array();
    return $result;
  }

  function master_site_bc_byName($table, $name){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("master_site_name", $name);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function data_site_bc_name($name){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("site_name", $name);
    $q       = $this->db->get("webtracking_site");
    return  $q->result_array();
  }

  function update_post_event($table, $where, $wherenya, $data){
    $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
    $this->dbalarm->where($where, $wherenya);
    return $this->dbalarm->update($table, $data);
  }

  function check_intervention_config($user_company, $sdate, $edate){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("config_company", $user_company);
    $this->db->where("config_start_shift >= ", $sdate);
    $this->db->where("config_end_shift <= ", $edate);
    $q       = $this->db->get("intervention_config");
    return  $q->result_array();
  }

  function check_intervention_config_2($user_company, $sdate, $edate){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("config_company", $user_company);
    $this->db->where("config_start_shift", $sdate);
    $this->db->where("config_end_shift", $edate);
    // $this->db->limit(1);
    $q       = $this->db->get("intervention_config");
    return  $q->result_array();
  }

  function insert_intervention_config($data){
    $this->db     = $this->load->database("default", true);
    return $this->db->insert("intervention_config", $data);
  }

  function get_alarm_mdvranomali(){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");
    $this->dbalarm->where("alarm_isanomali_mdvr", 1);
    $q        = $this->dbalarm->get("webtracking_ts_alarm");
    return  $q->result_array();
  }

  function data_mdvr_anomali($table, $sdate, $edate, $company, $vehicle, $type){
    $privilegecode = $this->sess->user_id_role;
    $user_company = $this->sess->user_company;

    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if($privilegecode == 5 || $privilegecode == 6){
      $this->dbtensor->where("mdvr_anomali_vehicle_companyid", $user_company);
    }else{
      if ($company != "all") {
        $this->dbtensor->where("mdvr_anomali_vehicle_companyid", $company);
      }
    }

      if ($vehicle != "all") {
        $this->dbtensor->where("mdvr_anomali_vehicle_device", $vehicle);
      }

      if ($type != "all") {
        $this->dbtensor->where("mdvr_anomali_type_id", $type);
      }

    $this->dbtensor->where("mdvr_anomali_datetime >=", $sdate);
    $this->dbtensor->where("mdvr_anomali_datetime <=", $edate);
    $this->dbtensor->order_by("mdvr_anomali_datetime", "DESC");
    $this->dbtensor->order_by("mdvr_anomali_vehicle_no", "DESC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function master_site_bc_all($table){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->order_by("master_site_shortname", "ASC");
    $q          = $this->dbts->get($table);
    return $q->result_array();
  }

  function getDataSite($site_veryshortname){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("master_site_veryshortname_tambahan", $site_veryshortname);
    $q          = $this->dbts->get("ts_bc_master_site");
    return $q->result_array();
  }

  function master_location_bc_all($table){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->order_by("master_location_parent_short_name", "ASC");
    $q          = $this->dbts->get($table);
    return $q->result_array();
  }

  function master_site_bc($table, $id_lokasi){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("master_site_id", $id_lokasi);
    $q          = $this->dbts->get($table);
    return $q->result_array();
  }

  function master_location_bc($table, $id_lokasi_detail){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("master_location_id_sync", $id_lokasi_detail);
    $q          = $this->dbts->get($table);
    return $q->result_array();
  }

  function allDataClient(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("client_shortcut", "asc");
    $this->db->where("client_parent_id", 4408);
    $q       = $this->db->get("master_client");
    return  $q->result_array();
  }

  function allDataMaterial(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("material_shortcut", "asc");
    $this->db->where("material_parent_id", 4408);
    $q       = $this->db->get("master_material");
    return  $q->result_array();
  }

  function getDataVehicle($keyword){
    $this->db->select("vehicle_no");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->like("vehicle_no", $keyword);
    $this->db->where("vehicle_status <>", 3);
    return $this->db->get("vehicle")->result_array();
  }

  function getDataVehicleByDevice($vehicle_device){
    $this->db->select("vehicle_no, vehicle_site, vehicle_tipe_unit_for_integrasi");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->like("vehicle_device", $vehicle_device);
    return $this->db->get("vehicle")->result_array();
  }

  function getDataVehicleById($vehicle_id){
    $this->db->select("vehicle_no, vehicle_site, vehicle_tipe_unit_for_integrasi, vehicle_name");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->like("vehicle_id", $vehicle_id);
    return $this->db->get("vehicle")->result_array();
  }

  function getDataVehicleById_2($mv03){
    $this->db->select("vehicle_no, vehicle_site, vehicle_tipe_unit_for_integrasi, vehicle_name");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->where("vehicle_mv03", $mv03);
    $this->db->where("vehicle_status <>", 3);
    return $this->db->get("vehicle")->result_array();
  }

  function getDataClient($keyword){
    $this->db->select("client_id");
    $this->db->order_by("client_id", "asc");
    $this->db->like("client_shortcut", $keyword);

    return $this->db->get("master_client")->result_array();
  }

  function getDataDriverItws($keyword){
    $this->db->select("driveritws_driver_name");
    $this->db->order_by("driveritws_id_driver", "asc");
    $this->db->like("driveritws_id_driver", $keyword);

    return $this->db->get("driver_itws")->result_array();
  }

  function getDataMaterial($keyword){
    $this->db->select("material_id");
    $this->db->order_by("material_id", "asc");
    $this->db->like("material_shortcut", $keyword);

    return $this->db->get("master_material")->result_array();
  }

  function getThisMaterial($keyword){
    $this->db->select("*");
    $this->db->order_by("material_id", "asc");
    $this->db->like("material_id", $keyword);

    return $this->db->get("master_material")->result_array();
  }

  function getThisDriverItws($keyword){
    $this->db->select("*");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->like("driveritws_driver_name", $keyword);

    return $this->db->get("driver_itws")->result_array();
  }

  function recallToLast($keyword){
    $dbtable        = "historikal_integrationwim_unit";
    $this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->limit(1, 0);
    $this->dbreport->like("integrationwim_truckID", $keyword);
    // $this->dbreport->where("integrationwim_operator_status", 1);
    $this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
    $q = $this->dbreport->get($dbtable);
    return $q->result_array();
  }

  function recallToLastOtherPort($keyword){
    $dbtable        = "historikal_integrationwim_unit";
    $this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->limit(1, 0);
    $this->dbreport->like("integrationwim_truckID", $keyword);
    $this->dbreport->where("integrationwim_dumping_fms_port !=", "");//bukan data dihapus
    $this->dbreport->where("integrationwim_dumping_fms_port !=", "PORT BIB");
    // $this->dbreport->where("integrationwim_operator_status", 1);
    $this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
    $q = $this->dbreport->get($dbtable);
    return $q->result_array();
  }

  function updateitwsnow($transID, $data){
    $dbtable        = "historikal_integrationwim_unit";
    $this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->where("integrationwim_transactionID", $transID);
    return $this->dbreport->update($dbtable, $data);
  }

  function getstreet_now($type)
  {
    $this->db->select("street_id, street_name, street_type, street_order");
      if ($type == 3) {
        $this->db->where_in("street_type", array(3, 5));
      }elseif ($type == 1) {
        $this->db->where("street_type", $type);
      }elseif ($type == 4) {
        $this->db->where_in("street_type", array(4, 7, 8));
      }else {
        $this->db->where("street_type", $type);
      }
    $this->db->order_by("street_order", "ASC");
    $this->db->where("street_creator", 4408);
    $this->db->where("street_flag", 1);
    $q = $this->db->get("street");
    $rows = $q->result_array();
    return $rows;
  }

  function alldriveritws(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->where("driveritws_flag", 0);
    $q       = $this->db->get("webtracking_driver_itws");
    return  $q->result_array();
  }

  function driveritwsbycompany($company_name){
    $this->db     = $this->load->database("default", true);
    $this->db->select("driveritws_id_driver, driveritws_driver_name");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->where("driveritws_flag", 0);
    $this->db->where("driveritws_company_name", $company_name);
    $q       = $this->db->get("webtracking_driver_itws");
    return  $q->result_array();
  }

  function similarkminstreet($data){
    $this->db     = $this->load->database("default", true);
    $this->db->select("street_name, street_group");
    $this->db->like("street_name", $data);
    $this->db->where("street_creator", 4408);
    $this->db->where_in("street_type", array(1, 9));
      if (strpos($data, "TIA") !== FALSE) {
        $this->db->where("street_flag", 1);
      }else {
        $this->db->where("street_flag", 0);
      }
    $this->db->order_by("street_name", "asc");
    $q       = $this->db->get("webtracking_street");
    return  $q->result_array();
  }

  function savetotempovspeed($data){
    $this->dbreport = $this->load->database("tensor_report", true);
    return $this->dbreport->insert("overspeed_report_temp", $data);
  }

  function gettemp_data($temp_code){
    $this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->where("overspeed_temp_code", $temp_code);
    return $this->dbreport->get("overspeed_report_temp")->result();
  }

  function getdatakmstreet(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("street_name, street_group");
    // $this->db->like("street_name", $data);
    $this->db->where_in("street_type", array(1, 9));
    $this->db->where("street_order != ", "");
    $this->db->where("street_creator", 4408);
    $this->db->order_by("street_order", "asc");
    // $this->db->group_by("street_group");
    $q       = $this->db->get("webtracking_street");
    return  $q->result_array();
  }

  // MODEL KHUSUS QUICK COUNT BIB START
  function getmastervehicleforheatmap(){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_dblive 	   = $this->sess->user_dblive;
    $user_parent 	   = $this->sess->user_parent;
    $user_id_role 	 = $this->sess->user_id_role;
    $user_id_fix     = $user_id;

    // echo "<pre>";
    // var_dump($user_id_role.'-'.$user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
    // echo "<pre>";

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

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
      $this->db->where("vehicle_user_id", 4408);
    }else if($user_id_role == 6){
      $this->db->where("vehicle_user_id", 4408);
    }else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
    }else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_typeunit", 0);
    $this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getcompanybyID($id)
  {
    $this->db->select("*");
    $this->db->where("company_id",$id);
    $this->db->where("company_flag",0);
    $this->db->order_by("company_name","asc");
    $q = $this->db->get("company");
    $rows = $q->result();
    return $rows;
  }

  function getcompany_byowner($privilegecode)
  {
    $this->db->order_by("company_name","asc");

      if ($privilegecode == 0) {
        $this->db->where("company_created_by", $this->sess->user_id);
      }elseif($privilegecode == 1) {
        $this->db->where("company_created_by", $this->sess->user_parent);
      }elseif($privilegecode == 2){
        $this->db->where("company_created_by",$this->sess->user_parent);
      }elseif($privilegecode == 3){
        $this->db->where("company_created_by",$this->sess->user_parent);
      }elseif($privilegecode == 4){
        $this->db->where("company_created_by",$this->sess->user_parent);
      }elseif($privilegecode == 5){
        // $this->db->where("company_created_by",$this->sess->user_company);
          $this->db->where("company_id", $this->sess->user_company);
      }elseif($privilegecode == 6){
        // $this->db->where("company_created_by",$this->sess->user_company);
          $this->db->where("company_id", $this->sess->user_company);
      }elseif($privilegecode == 10){
          $this->db->where("company_id", $this->sess->user_parent);
      }elseif($this->sess->user_level == 3){
        $this->db->where("company_id",$this->sess->user_company);
      }else{
        $this->db->where("company_id",0);
      }

    $this->db->where("company_flag",0);
    $q = $this->db->get("company");
    $rows = $q->result();
    return $rows;
  }

  function vehicle_by_mitra($id, $vehicle_user_id)
  {
    // echo "<pre>";
    // var_dump($id);die();
    // echo "<pre>";
    $this->db     = $this->load->database("default", true);
    $this->db->order_by("vehicle_no","asc");
    $this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_active_date2,vehicle_mv03, vehicle_status");
      if ($id != "all") {
        $this->db->where("vehicle_company",$id);
      }
    $this->db->where("vehicle_status <>",3);
    $this->db->where("vehicle_user_id",$vehicle_user_id);
    $q = $this->db->get("vehicle");
    $rows = $q->result_array();
    return $rows;
  }

  function getvehicle_bycompany_master($id)
  {
    $this->db->order_by("vehicle_no","asc");
    $this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_active_date2,vehicle_mv03");
    $this->db->where("vehicle_company",$id);
    $this->db->where("vehicle_status <>",3);
    $q = $this->db->get("vehicle");
    $rows = $q->result();
    return $rows;
  }

  function gettotalengine($companyid)
  {
    $this->db->order_by("vehicle_id","asc");
    $this->db->select("vehicle_id,vehicle_company,vehicle_autocheck");
    $this->db->where("vehicle_company",$companyid);
    $this->db->where("vehicle_status <>", 3);
    $q = $this->db->get("vehicle");
    $rows = $q->result();

    $total_on = 0;
    $total_off = 0;
    $total_nodata = 0;

    for($i=0; $i < count($rows); $i++)
    {
      $json = json_decode($rows[$i]->vehicle_autocheck);
      if(isset($json)){
        if (isset($json->auto_last_engine)) {
          if($json->auto_last_engine == "OFF" ){
            $total_off = $total_off + 1;
          }
          if($json->auto_last_engine == "ON" ){
            $total_on = $total_on + 1;
          }
          if($json->auto_last_engine == "NO DATA" ){
            $total_nodata = $total_nodata + 1;
          }
        }
      }

    }
    return $total_off."|".$total_on."|".count($rows)."|".$total_nodata;
  }

  function gettotalstatus($userid)
  {
    $this->db->order_by("vehicle_id","asc");
    $this->db->select("vehicle_id,vehicle_autocheck");

    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $user_id_fix     = $this->sess->user_id;
    $user_excavator  = $this->sess->user_excavator;
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_id","asc");

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
    }else if($user_id_role == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    if ($user_excavator == 1) {
      $this->db->where("vehicle_typeunit", 1);
    }

    $this->db->where("vehicle_status <>", 3);
    //$this->db->where("vehicle_gotohistory", 0);
    //$this->db->where("vehicle_autocheck is not NULL");
    $q = $this->db->get("vehicle");
    $rows = $q->result();

    $total_p = 0;
    $total_k = 0;
    $total_m = 0;

    $total_on = 0;
    $total_off = 0;
    $total_nodata = 0;

    for($i=0; $i < count($rows); $i++)
    {

      $json = json_decode($rows[$i]->vehicle_autocheck);
      if(isset($json->auto_status)){
        if($json->auto_status == "P" ){
          $total_p = $total_p + 1;
        }
        if($json->auto_status == "K" ){
          $total_k = $total_k + 1;
        }
        if($json->auto_status == "M" ){
          $total_m = $total_m + 1;
        }
        if($json->auto_last_engine == "ON" ){
          $total_on = $total_on + 1;
        }
        if($json->auto_last_engine == "OFF" ){
          $total_off = $total_off + 1;
        }
        if($json->auto_last_engine == "NO DATA" ){
          $total_nodata = $total_nodata + 1;
        }
      }

    }
    return $total_p."|".$total_k."|".$total_m."|".count($rows)."|".$total_off."|".$total_on."|".$total_nodata;
    }

  function getalldata($table){
    $this->db->where("poi_creator_id", "4408");
    $this->db->where("poi_flag", 0);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function vehicleactive(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $user_id_fix     = $this->sess->user_id;
    // ACTIVE DEVICE
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

    $this->db->where("vehicle_status <>", 3);
    $q            = $this->db->get("vehicle");
    return $q->result_array();
  }

  function vehicleexpired(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $user_id_fix 		 = $this->sess->user_id;

    // EXPIRED DEVICE
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
    $datenow       = date("Ymd");
    $this->db->where("vehicle_active_date2 <", $datenow);
    $q2            = $this->db->get("vehicle");
    return $q2->result_array();
  }

  function totaldevice(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $user_id_fix 		 = $this->sess->user_id;

    // TOTAL DEVICE
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
    $q3             = $this->db->get("vehicle");
    return $q3->result_array();
  }

  function getmapsetting(){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getAlertAlias($alert_name){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");
    $this->dbalarm->where("alarm_name", $alert_name);
    $q        = $this->dbalarm->get("webtracking_ts_alarm");
    return  $q->result_array();
  }

  function getallastreetkm(){
    $this->db->select("street_name, street_is_romroad, street_group");
    $this->db->where("street_type", 1);
    $this->db->where("street_creator", 4408);
    $this->db->order_by("street_order", "ASC");
    $this->db->group_by("street_group");
    $q = $this->db->get("street");
    $rows = $q->result_array();
    return $rows;
  }

  function getkmgroup($street_group){
    $this->db->select("street_name, street_is_romroad, street_group");
    $this->db->where("street_type", 1);
    $this->db->where("street_is_romroad", 1);
    $this->db->where("street_group", $street_group);
    $this->db->where("street_creator", 4408);
    $this->db->order_by("street_order", "ASC");
    $q = $this->db->get("street");
    $rows = $q->result_array();
    return $rows;
  }
  // MODEL KHUSUS QUICK COUNT BIB END

  // MODEL KHUSUS POC IN ROM START
  function vehicleinrom_gps_temp($dbtable, $starttime, $wherenya){
    $this->vehicledblive     = $this->load->database($dbtable, true);
    $this->vehicledblive->select("*");
    $this->vehicledblive->where("gps_tmp_geofence", $wherenya);
    $this->vehicledblive->where("gps_tmp_time >= ", $starttime);
    $this->vehicledblive->order_by("gps_tmp_time", "DESC");
    $q    = $this->vehicledblive->get("webtracking_gps_temp");
    $rows = $q->result_array();
    return $rows;
  }

  function vehicleinrom_gps_temp_bydeviceexca($dbtable, $name, $host, $starttime, $wherenya){
    $this->vehicledblive     = $this->load->database($dbtable, true);
    $this->vehicledblive->select("*");
    $this->vehicledblive->where("gps_name", $name);
    $this->vehicledblive->where("gps_host", $host);
    // $this->vehicledblive->where("gps_geofence", $wherenya);
    $this->vehicledblive->where("gps_time >= ", $starttime);
    $this->vehicledblive->order_by("gps_time", "DESC");
    $q    = $this->vehicledblive->get("webtracking_gps");
    $rows = $q->result_array();
    return $rows;
  }

  function vehicleinrom_gps_temp_bydevice($dbtable, $name, $host, $starttime, $wherenya){
    $this->vehicledblive     = $this->load->database($dbtable, true);
    $this->vehicledblive->select("*");
    $this->vehicledblive->where("gps_tmp_name", $name);
    $this->vehicledblive->where("gps_tmp_host", $host);
    $this->vehicledblive->where("gps_tmp_geofence", $wherenya);
    $this->vehicledblive->where("gps_tmp_time >= ", $starttime);
    $this->vehicledblive->order_by("gps_tmp_time", "DESC");
    $q    = $this->vehicledblive->get("webtracking_gps_temp");
    $rows = $q->result_array();
    return $rows;
  }

  function getthisfrommastervehicle($vehicledevice){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("vehicle_device", $vehicledevice);
    $this->db->where("vehicle_status <>", 3);
    // $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getmastervehicleformapsexca(){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_dblive 	   = $this->sess->user_dblive;
    $user_parent 	   = $this->sess->user_parent;
    $user_id_role 	 = $this->sess->user_id_role;
    $user_id_fix     = $user_id;

    // echo "<pre>";
    // var_dump($user_id_role.'-'.$user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
    // echo "<pre>";

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

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
      $this->db->where("vehicle_user_id", 4408);
    }else if($user_id_role == 6){
      $this->db->where("vehicle_user_id", 4408);
    }else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
    }else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
    }else if($user_id_role == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_company", 1961);
    $this->db->where("vehicle_status <>", 3);
    $this->db->where_in("vehicle_typeunit", array(1));
    // $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getmastervehicleformapsexcaforclear(){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_dblive 	   = $this->sess->user_dblive;
    $user_parent 	   = $this->sess->user_parent;
    $user_id_role 	 = $this->sess->user_id_role;
    $user_id_fix     = $user_id;

    // echo "<pre>";
    // var_dump($user_id_role.'-'.$user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
    // echo "<pre>";

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

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
      $this->db->where("vehicle_user_id", 4408);
    }else if($user_id_role == 6){
      $this->db->where("vehicle_user_id", 4408);
    }else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
    }else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
    }else if($user_id_role == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    // $this->db->where("vehicle_company", 1961);
    $this->db->where("vehicle_status <>", 3);
    $this->db->where_in("vehicle_typeunit", array(0,1));
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getmastervehiclebycontractor($companyid){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
    $user_parent     = $this->sess->user_parent;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_dblive 	   = $this->sess->user_dblive;
    $privilegecode 	 = $this->sess->user_id_role;
    $user_id_fix     = $user_id;
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

    if($privilegecode == 1){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 2){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 3){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 4){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 5){
      $this->db->where("vehicle_company", $user_company);
    }else if($privilegecode == 6){
      $this->db->where("vehicle_company", $user_company);
    }else if($privilegecode == 0){
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else if($privilegecode == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $this->db->where_in("vehicle_typeunit", array(0,1));

    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getunitbyvdevice($vdevice){
    $this->db     = $this->load->database("default", true);
    $this->db->where("vehicle_device", $vdevice);
    $this->db->where("vehicle_status <>", 3);
    $this->db->order_by("vehicle_id", "asc");
    return $this->db->get("vehicle")->row();
  }
  // MODEL KHUSUS POC IN ROM END

  function getviolationmaster()
  {
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("alarmmaster_id, alarmmaster_name");
    $q        = $this->dbalarm->get("webtracking_ts_alarmmaster");
    return  $q->result_array();
  }

  function getalarmbytype($alarmtype){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");
    $this->dbalarm->where("alarm_master_id", $alarmtype);
    $q        = $this->dbalarm->get("webtracking_ts_alarm");
    return  $q->result_array();
  }

  function get_overspeed_intensor($table, $limit, $contractor, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("overspeed_report_vehicle_company", $contractor);
    }

    // $this->dbtensor->where("violation_status", 1);
    $this->dbtensor->where("overspeed_report_gps_time >=", $sdate);
    $this->dbtensor->where("overspeed_report_gps_time <=", $edate);
    $this->dbtensor->where("overspeed_report_speed_status", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    // $this->dbtensor->group_by("violation_update");
    $this->dbtensor->limit($limit);
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function get_overspeed_intensor_historikal($table, $vehicle, $contractor, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("overspeed_report_vehicle_company", $contractor);
    }

    if ($vehicle != 0) {
      $this->dbtensor->where("overspeed_report_vehicle_device", $vehicle);
    }

    $this->dbtensor->where("overspeed_report_gps_time >=", $sdate);
    $this->dbtensor->where("overspeed_report_gps_time <=", $edate);
    $this->dbtensor->where("overspeed_report_speed_status", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function get_overspeed_intensor_intervention($table, $vehicle, $contractor, $sdate, $edate){
    $privilegecode = $this->sess->user_id_role;
    $user_company = $this->sess->user_company;

    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("overspeed_report_vehicle_company", $contractor);
    }

    if ($vehicle != 0) {
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
          // echo "<pre>";
          // var_dump($user_company);die();
          // echo "<pre>";
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else if($privilegecode == 6){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else{
          $this->dbtrip->where("overspeed_report_vehicle_company",99999);
        }
    }else{
      $this->dbtensor->where("overspeed_report_vehicle_device", $vehicle);
      // $this->dbtrip->where("overspeed_report_imei", $vehicle);
    }

    $this->dbtensor->where("overspeed_report_gps_time >=", $sdate);
    $this->dbtensor->where("overspeed_report_gps_time <=", $edate);
    $this->dbtensor->where("overspeed_report_speed_status", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function get_overspeed_intensor_intervention_detail($table, $alert_id, $sdate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");
    $this->dbtensor->where("overspeed_report_id", $alert_id);
    $this->dbtensor->where("overspeed_report_gps_time", $sdate);
    $this->dbtensor->where("overspeed_report_speed_status", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getviolationhistorikal_type2($table, $limit, $contractor, $alarmtypefromaster, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("violation_vehicle_companyid", $contractor);
    }

    if (sizeof($alarmtypefromaster) != 0) {
      $this->dbtensor->where_in("violation_type_id", $alarmtypefromaster);
    }

    // $this->dbtensor->where("violation_status", 1);
    $this->dbtensor->where("violation_update >=", $sdate);
    $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->group_by("violation_vehicle_no");
    $this->dbtensor->group_by("violation_update");
    $this->dbtensor->limit($limit);
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getviolationhistorikal_type2_report($table, $vehicle, $contractor, $alarmtypefromaster, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($vehicle != 0) {
      $this->dbtensor->where("violation_vehicle_device", $vehicle);
    }

    if ($contractor != 0) {
      $this->dbtensor->where("violation_vehicle_companyid", $contractor);
    }

    if (sizeof($alarmtypefromaster) != 0) {
      $this->dbtensor->where_in("violation_type_id", $alarmtypefromaster);
    }

    $this->dbtensor->where("violation_status", 1);
    $this->dbtensor->where("violation_position != ", "");
    $this->dbtensor->where("violation_update >=", $sdate);
    $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->group_by("violation_vehicle_no");
    $this->dbtensor->group_by("violation_update");
    $q        = $this->dbtensor->get($table);
    // $q        = $this->dbtensor->get("historikal_violation_desember_2022_bckp_15122022");
    return  $q->result_array();
  }

  function getfrommaster($vdevice){
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("vehicle_device", $vdevice);
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getthisvehicle($company, $vehicle)
  {
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no", "asc");

    if ($company != "all") {
      $this->db->where("vehicle_company", $company);
    }

    if ($vehicle != "all") {
      $this->db->where("vehicle_device", $vehicle);
    }

    $this->db->where("vehicle_mv03 !=", "0000");
    // $this->db->where_in("vehicle_type", array("MV03"));
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getallreport($dbtable, $sdate, $edate, $vehicle){
    $this->dbreport = $this->load->database("tensor_report", true);

    $this->dbreport->where("devicestatus_vehicle_vehicle_device", $vehicle);

    $this->dbreport->where("devicestatus_submited_date >=", $sdate);
    $this->dbreport->where("devicestatus_submited_date <=", $edate);
    return $this->dbreport->get($dbtable)->result_array();
  }

  function getdatasummarymdvr($table, $company, $vehicle, $frekuensianomali, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

      if ($company != "all") {
        $this->dbtensor->where("devicestatus_summary_vehicle_company", $company);
      }

      if ($vehicle != "all") {
        $this->dbtensor->where("devicestatus_summary_vehicle_device", $vehicle);
      }

      if ($frekuensianomali != "all") {
        $this->dbtensor->where("devicestatus_summary_frekuensi_anomali >=", $frekuensianomali);
      }
    $this->dbtensor->where("devicestatus_summary_isformitra", 1);
    $this->dbtensor->where("devicestatus_summary_submited_date >=", $sdate);
    $this->dbtensor->where("devicestatus_summary_submited_date <=", $edate);
    $this->dbtensor->order_by("devicestatus_summary_frekuensi_anomali", "ASC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getdevice(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $privilegecode   = $this->sess->user_id_role;
    $user_id         = $this->sess->user_id;
    $user_id_fix     = "";

    if($user_id == "1445"){
      $user_id_fix = $user_id;
    }else{
      $user_id_fix = $this->sess->user_id;
    }

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

    if($privilegecode == 0){
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else if($privilegecode == 1){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 2){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 3){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 4){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 5){
      $this->db->where("vehicle_company", $user_company);
    }else if($privilegecode == 6){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_mv03 !=", "0000");
    // $this->db->where_in("vehicle_type", array("MV03"));
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getAllVehicle_fuelsensoronly($companyid, $userid){
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");
      if ($companyid != "all") {
        $this->db->where("vehicle_company", $companyid);
      }
    $this->db->where("vehicle_user_id", $userid);
    $this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_sensor != ", "No");
    // $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    $result  = $q->result_array();
    return $result;
  }

  function getdatafuelsensorhistory($table, $vehicle, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

      // if ($company != "all") {
      //   $this->dbtensor->where("fuelcheck_vehicle_companyid", $company);
      // }

      if ($vehicle != "all") {
        $this->dbtensor->where("fuelcheck_vehicle_device", $vehicle);
      }

      // if ($frekuensianomali != "all") {
      //   $this->dbtensor->where("devicestatus_summary_frekuensi_anomali >=", $frekuensianomali);
      // }

    $this->dbtensor->where("fuelcheck_date_real >=", $sdate);
    $this->dbtensor->where("fuelcheck_date_real <=", $edate);
    $this->dbtensor->order_by("fuelcheck_date_real", "ASC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function get_type_intervention(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("intervention_type_id", "asc");
    $q       = $this->db->get("type_intervention");
    return  $q->result_array();
  }

  function get_type_note($parent){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("type_note_parent", $parent);
    $this->db->order_by("type_note_name", "asc");
    $q       = $this->db->get("type_note");
    return  $q->result_array();
  }

  function check_data_karyawan(){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->order_by("karyawan_bc_name", "ASC");
    $result = $this->dbts->get("ts_karyawan_beraucoal")->result_array();
    return $result;
  }

  function data_karyawan_bymitra($company_id){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($company_id != "all") {
        $this->dbts->where("karyawan_bc_company_id", $company_id);
      }
    $this->dbts->order_by("karyawan_bc_name", "ASC");
    $result = $this->dbts->get("ts_karyawan_beraucoal")->result_array();
    return $result;
  }

  function check_data_karyawan_by_sid($table, $sid){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("karyawan_bc_sid", $sid);
    $result = $this->dbts->get($table)->result_array();
    return $result;
  }
















}
