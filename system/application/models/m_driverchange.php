<?php
class M_driverchange extends Model{

  function getFromPortalSimper(){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("portal_nik, portal_name, portal_position, portal_id_position");
    // $this->dbts->where_in("master_portal_type", array("Dump Truck","Dump Truck Coal Hauling","DT Hin FM260 JD"));
    $this->dbts->order_by("portal_name", "ASC");
    $this->dbts->where("portal_jabatan", "DRIVER");
    return $this->dbts->get("ts_master_portal_simper")->result_array();
  }

  function getthisdata($dbtable, $company, $vehicle, $driver, $sdate, $edate, $user_privilege, $user_company){
    $this->dbreport = $this->load->database("webtracking_ts", true);

    if ($user_privilege == 5 || $user_privilege == 6) {
      $this->dbreport->where("change_driver_company", $user_company);
    }

    if ($company != "all") {
      $this->dbreport->where("change_driver_company", $company);
    }

    if ($vehicle != "all") {
      $this->dbreport->where("change_driver_vehicle_no", $vehicle);
    }

    if ($driver != "all") {
      $this->dbreport->where("change_driver_id", $driver);
    }

    $this->dbreport->where("change_driver_time >= ", $sdate);
    $this->dbreport->where("change_driver_time <= ", $edate);

    $this->dbreport->order_by("change_driver_time", "DESC");
    $q = $this->dbreport->get($dbtable);
    return $q->result_array();
  }
















}
