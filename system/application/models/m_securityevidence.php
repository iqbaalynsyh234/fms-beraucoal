<?php
class M_securityevidence extends Model {

    function getallvehicleforviolation(){
      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->order_by("vehicle_no","asc");

      // if ($type == 1) {
        // $this->db->where("vehicle_device", "869926046521882@VT200");
      // }
      // $this->db->where_in("vehicle_type", array("MV03"));
      $this->db->where("vehicle_user_id", 4408);
      $this->db->where("vehicle_status <>", 3);
      $q       = $this->db->get("vehicle");
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

    function getalarmcategory(){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("webtracking_alarmcategory_flag", 1);
      $this->dbalarm->order_by("webtracking_alarmcategory_name","asc");
      $q        = $this->dbalarm->get("webtracking_ts_alarmcategory");
      return  $q->result_array();
    }

    function getalarmsubcategory($id){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
        if ($id != "All") {
          $this->dbalarm->where("webtracking_alarmsubcategory_categoryid", $id);
          $this->dbalarm->where("webtracking_alarmsubcategory_flag", 1);
        }else {
          $this->dbalarm->where("webtracking_alarmsubcategory_flag", 1);
        }
      $this->dbalarm->order_by("webtracking_alarmsubcategory_name","asc");
      $q        = $this->dbalarm->get("webtracking_ts_alarmsubcategory");
      return  $q->result_array();
    }

    function getalarmchild($id){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
        if ($id != "All") {
          $this->dbalarm->where("alarm_subcategory_id", $id);
          $this->dbalarm->where("alarm_status", 1);
        }else {
          $this->dbalarm->where("alarm_status", 1);
        }
      $this->dbalarm->order_by("alarm_name","asc");
      $q        = $this->dbalarm->get("webtracking_ts_alarm");
      return  $q->result_array();
    }

    function getalarmmaster(){
      $this->dbalarm = $this->load->database("webtracking_ts", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("alarmmaster_flag", 0);
      $this->dbalarm->order_by("alarmmaster_name","asc");
      $q        = $this->dbalarm->get("webtracking_ts_alarmmaster");
      return  $q->result_array();
    }

    function getalarmbytypeforevidence(){
      $this->dbalarm = $this->load->database("webtracking_ts", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where_not_in("alarm_master_id", array("", "6"));
      $q        = $this->dbalarm->get("webtracking_ts_alarm");
      return  $q->result_array();
    }

    function getalarmbytype($alarmtype){
      $this->dbalarm = $this->load->database("webtracking_ts", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("alarm_master_id", $alarmtype);
      $q        = $this->dbalarm->get("webtracking_ts_alarm");
      return  $q->result_array();
    }

    function getalarmtype(){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("alarm_status", 2);
      $this->dbalarm->order_by("alarm_name","asc");
      $q        = $this->dbalarm->get("webtracking_ts_alarm");
      return  $q->result_array();
    }

    function detailalert($typealert){
      if ($typealert[0] == 0) {
        $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
        $this->dbalarm->select("alarm_name");
        $this->dbalarm->where("alarm_status", 2);
        $this->dbalarm->where_in("alarm_type", $typealert);
        $q        = $this->dbalarm->get("webtracking_ts_alarm");
        return  $q->result_array();
      }else {
        $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
        $this->dbalarm->select("alarm_name");
        $this->dbalarm->where("alarm_status", 2);
        $this->dbalarm->where_in("alarm_type", $typealert);
        $q        = $this->dbalarm->get("webtracking_ts_alarm");
        return  $q->result_array();
      }
    }

    function searchthisreport($table, $vehicle, $startdatefix, $enddatefix, $alarmtype){
      // $vehicle.'-'.$startdate.'-'.$shour.'-'.$enddate.'-'.$ehour.'-'.$alarmtype
      //
  		// echo "<pre>";
  		// var_dump($alarmtype);die();
  		// echo "<pre>";
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->where("alarm_report_vehicle_id", $vehicle);
      $this->dbalarm->where("alarm_report_media", 0);
      $this->dbalarm->where("alarm_report_start_time >=", $startdatefix);
      $this->dbalarm->where("alarm_report_end_time <=", $enddatefix);
        if ($alarmtype != "ALL") {
          $this->dbalarm->where_in('alarm_report_type', $alarmtype);
        }
      // $this->dbalarm->where("alarm_status","2");
      $this->dbalarm->order_by("alarm_report_start_time","desc");
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getdetailreport($table, $alertid, $sdate){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
      $this->dbalarm->where("alarm_report_start_time", $sdate);
      $this->dbalarm->where("alarm_report_media", 0);
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getdetailreportvideo($table, $alertid, $sdate){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("alarm_report_downloadurl, alarm_report_id");
      $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
      $this->dbalarm->where("alarm_report_start_time", $sdate);
      $this->dbalarm->where("alarm_report_media", 1);
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getdetailreport_intervention($table, $alertid, $imei, $sdate){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      // $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
      $this->dbalarm->where("alarm_report_imei", $imei);
      $this->dbalarm->where("alarm_report_start_time", $sdate);
      $this->dbalarm->where("alarm_report_media", 0);
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getdetailreportvideo_intervention($table, $alertid, $imei, $sdate){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("alarm_report_downloadurl, alarm_report_id");
      // $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
      $this->dbalarm->where("alarm_report_imei", $imei);
      $this->dbalarm->where("alarm_report_start_time", $sdate);
      $this->dbalarm->where("alarm_report_media", 1);
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getdetailreportvideo_intervention_pushalert($table, $alertid, $imei, $sdate){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      // $this->dbalarm->select("*");
      $this->dbalarm->select("alarm_report_id, alarm_report_video_link, alarm_report_image_link");
      // $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
      $this->dbalarm->where("alarm_report_imei", $imei);
      $this->dbalarm->where("alarm_report_start_time", $sdate);
      $this->dbalarm->where("alarm_report_media", 0);
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getImageByID($id, $table){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("alarm_report_id", $id);
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getVideoByID($id, $table){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("alarm_report_id", $id);
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function getsession(){
      $this->dbalarm = $this->load->database("webtracking_ts", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("sess_type", "LOGIN");
      $this->dbalarm->order_by("sess_lastmodified", "desc");
      $this->dbalarm->limit(1);
      $q        = $this->dbalarm->get("webtracking_ts_sess");
      return  $q->result_array();
    }

    function searchthisreportall($table, $vehicle, $startdatefix, $enddatefix){
      // $vehicle.'-'.$startdate.'-'.$shour.'-'.$enddate.'-'.$ehour.'-'.$alarmtype
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->where("alarm_report_vehicle_id", $vehicle);
      $this->dbalarm->where("alarm_report_media", 0);
      $this->dbalarm->where("alarm_report_start_time >=", $startdatefix);
      $this->dbalarm->where("alarm_report_end_time <=", $enddatefix);
      $this->dbalarm->order_by("alarm_report_start_time","desc");
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
      return  $q->result_array();
    }

    function searchthisreportjoin($table, $vehicle, $startdatefix, $enddatefix, $alarmchild){
      $sql = "SELECT alarm_report_type, alarm_report_name, webtracking_ts_alarm.alarm_subcategory_id as subcatid,
              webtracking_ts_alarmsubcategory.webtracking_alarmsubcategory_name as subcatname,
              webtracking_ts_alarmcategory.webtracking_alarmcategory_id as catid,
              webtracking_ts_alarmcategory.webtracking_alarmcategory_name as catname
              FROM $table
              join webtracking_ts_alarm on $table.alarm_report_type = webtracking_ts_alarm.alarm_type
              join webtracking_ts_alarmsubcategory on webtracking_ts_alarm.alarm_subcategory_id = webtracking_ts_alarmsubcategory.webtracking_alarmsubcategory_id
              join webtracking_ts_alarmcategory on webtracking_ts_alarmsubcategory.webtracking_alarmsubcategory_categoryid = webtracking_ts_alarmcategory.webtracking_alarmcategory_id
              where $table.alarm_report_vehicle_id = '$vehicle'
              and $table.alarm_report_media = '0'
              and alarm_report_start_time between alarm_report_start_time >= '$startdatefix' and alarm_report_end_time <= '$enddatefix'
              and $table.alarm_report_type = '$alarmchild'";
    }

    function cekalarmgroup($report_type){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->where("alarm_type", $report_type);
      $q             = $this->dbalarm->get("webtracking_ts_alarm");
      return  $q->result_array();
    }

    function getvehiclebydev($table, $name, $host){
      $this->db = $this->load->database("default", true);
  		//GET DATA FROM DB
  		$this->db->select("vehicle_id, vehicle_device");
      $this->db->where("vehicle_device", $name.'@'.$host);
  		$this->db->where("vehicle_status <>", 3);
  		$q = $this->db->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }

    function getdriverhist($table, $vehicleid, $date){
      $this->dbtransporter = $this->load->database("transporter", true);
  		//GET DATA FROM DB
  		$this->dbtransporter->select("driver_history_driver_id");
      $this->dbtransporter->where("driver_history_vehicle_id", $vehicleid);
      $this->dbtransporter->where("driver_history_tanggal_submit <= ", $date);
      $this->dbtransporter->order_by("driver_history_tanggal_submit", "desc");
      $this->dbtransporter->limit(1);
  		$q = $this->dbtransporter->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }

    function getdriver($table, $driverid){
      $this->dbtransporter = $this->load->database("transporter", true);
  		//GET DATA FROM DB
  		$this->dbtransporter->select("*");
      $this->dbtransporter->where("driver_id", $driverid);
  		$q = $this->dbtransporter->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }

    function getdriverimage($table, $driverid){
      $this->dbtransporter = $this->load->database("transporter", true);
  		//GET DATA FROM DB
  		$this->dbtransporter->select("*");
      $this->dbtransporter->where("driver_image_driver_id", $driverid);
  		$q = $this->dbtransporter->get($table);
  		$rows = $q->result_array();
  		return $rows;
    }

    function getoverspeed($table, $vehicledevice, $date){
      // SELECT * FROM overspeed_oktober_2020 where overspeed_report_vehicle_device = '69969039493669@TK510' and overspeed_report_gps_time >=  '2020-10-07 19:12:00' and overspeed_report_gps_time <= '2020-10-07 19:13:00' and overspeed_report_speed_status = 1 order by overspeed_report_gps_time desc limit 1;
      $datefixstart = date("Y-m-d H:i:s", strtotime($date)+60*60);
      $datefixend   = date("Y-m-d H:i:s", strtotime($date)+120*60);

      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where("overspeed_report_vehicle_device", $vehicledevice);
      $this->dbalarm->where("overspeed_report_gps_time >=", date("Y-m-d H:i:s", strtotime($datefixstart)));
      $this->dbalarm->where("overspeed_report_gps_time <=", date("Y-m-d H:i:s", strtotime($datefixend)));
      $this->dbalarm->where("overspeed_report_speed_status", 1);
      $this->dbalarm->order_by("overspeed_report_gps_time", "desc");
      $this->dbalarm->limit(1);
      $q        = $this->dbalarm->get($table);
      return  $q->result_array();

      // echo "<pre>";
  		// var_dump($datefixstart.'-'.$datefixend);die();
  		// echo "<pre>";
    }

    function getalertnow($dbnamelive, $limitalert, $lasttimealert){
      $this->dbtemanindobara = $this->load->database($dbnamelive, true);
      $this->dbtemanindobara->select("gps_id, gps_name, gps_host, gps_status, gps_latitude_real, gps_longitude_real, gps_speed, gps_speed_limit, gps_alert, gps_time, gps_course");
      $this->dbtemanindobara->where("gps_alert", "Speeding alarm");
      $this->dbtemanindobara->where("gps_speed >=", 11.3);
      $this->dbtemanindobara->where("gps_latitude_real !=", 0);
      $this->dbtemanindobara->where("gps_longitude_real !=", 0);
      $this->dbtemanindobara->where("gps_time >= ", $lasttimealert);
      // $this->dbtemanindobara->where("gps_time > ", $lasttimealert);
      $this->dbtemanindobara->order_by("gps_time", "DESC");
      $this->dbtemanindobara->group_by("gps_time");
      $this->dbtemanindobara->limit($limitalert);
      $q = $this->dbtemanindobara->get("gps_alert");
      return $q->result_array();
    }

    function getevidencealert($table, $limitalert, $lasttimealert, $alarmtypefromaster){
      // $table.'-'. $limitalert.'-'. $lasttimealert.'-'.
      // echo "<pre>";
      // var_dump($alarmtypefromaster);die();
      // echo "<pre>";
      $this->dbalarm = $this->load->database("tensor_report", true);
      $this->dbalarm->select("*");
      $this->dbalarm->where_in("alarm_report_type", $alarmtypefromaster);
      $this->dbalarm->where("alarm_report_media", 0);
      $this->dbalarm->where("alarm_report_gpsstatus !=","");
      // $this->dbalarm->where_in("alarm_report_type", array(604, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627));
      // $this->dbalarm->where("alarm_report_start_time >= ", $lasttimealert);
      $this->dbalarm->where("alarm_report_start_time > ", $lasttimealert);
      $this->dbalarm->order_by("alarm_report_start_time", "DESC");
      $this->dbalarm->group_by("alarm_report_start_time");
      $this->dbalarm->limit(1);
      $q = $this->dbalarm->get($table);
      return $q->result_array();
    }

    function getdevicebydevID($vdevice){
      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->where("vehicle_device", $vdevice);
      $this->db->where("vehicle_typeunit", 0);
      $q       = $this->db->get("vehicle");
      return  $q->result_array();
    }

    function get_jalurname($direction){
  		$arah = "";
  		//utara
  		$ruas1 = 0;
  		$ruas2 = 90;
  		$ruas3 = 360-90;

  		//selatan
  		$ruas4 = 180-90;
  		$ruas5 = 180+90;
  		$ruas6 = 180;

  		if($direction >= $ruas1 && $direction <= $ruas2){ //0 - 90
  			$arah = "utara";
  			$jalur = "kosongan";
  		}else if($direction >= $ruas3 && $direction <= 360){ // 360-90 s/d 360
  			$arah = "utara";
  			$jalur = "kosongan";
  		}else if($direction >= $ruas6 && $direction <= $ruas5){ // 180 s/d 180+90
  			$arah = "selatan";
  			$jalur = "muatan";
  		}else if($direction >= $ruas4 && $direction <= $ruas6){ // 180-90 s/d 180
  			$arah = "selatan";
  			$jalur = "muatan";
  		}else{
  			$arah = $direction;
  			$jalur = "-";
  		}

  		return $jalur;
	}

  function violation_overspeed($name, $host, $lasttimealert){
    $this->dbtemanindobara = $this->load->database("webtracking_gps_temanindobara_live", true);
    $this->dbtemanindobara->select("gps_id, gps_name, gps_host, gps_status, gps_latitude_real, gps_longitude_real, gps_speed, gps_speed_limit, gps_alert, gps_time, gps_course");
    $this->dbtemanindobara->where("gps_alert", "Speeding alarm");
    $this->dbtemanindobara->where("gps_host", $host);
    $this->dbtemanindobara->where("gps_name", $name);
    $this->dbtemanindobara->where("gps_speed >=", 11.3);
    $this->dbtemanindobara->where("gps_latitude_real !=", 0);
    $this->dbtemanindobara->where("gps_longitude_real !=", 0);
    $this->dbtemanindobara->where("gps_time >= ", $lasttimealert);
    // $this->dbtemanindobara->where("gps_time > ", $lasttimealert);
    $this->dbtemanindobara->order_by("gps_time", "DESC");
    $this->dbtemanindobara->group_by("gps_time");
    $this->dbtemanindobara->limit(1);
    $q = $this->dbtemanindobara->get("gps_alert");
    return $q->result_array();
  }

  function violation_fatigue($table, $vehicle_mv03, $lasttimealert, $alarmtypefromaster){
    // $table.'-'. $limitalert.'-'. $lasttimealert.'-'.
    // echo "<pre>";
    // var_dump($alarmtypefromaster);die();
    // echo "<pre>";
    $this->dbalarm = $this->load->database("tensor_report", true);
    $this->dbalarm->select("*");
    $this->dbalarm->where_in("alarm_report_type", $alarmtypefromaster);
    $this->dbalarm->where("alarm_report_imei", $vehicle_mv03);
    $this->dbalarm->where("alarm_report_media", 0);
    $this->dbalarm->where("alarm_report_gpsstatus !=","");
    // $this->dbalarm->where_in("alarm_report_type", array(604, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627));
    // $this->dbalarm->where("alarm_report_start_time >= ", $lasttimealert);
    $this->dbalarm->where("alarm_report_start_time > ", $lasttimealert);
    $this->dbalarm->order_by("alarm_report_start_time", "DESC");
    $this->dbalarm->group_by("alarm_report_start_time");
    $this->dbalarm->limit(1);
    $q = $this->dbalarm->get($table);
    return $q->result_array();
  }

  function checktodbviolation($table, $vehicle_device){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");
    $this->dbalarm->where("violation_vehicle_device", $vehicle_device);
    $q        = $this->dbalarm->get($table);
    return  $q->result_array();
  }

  function checktodbviolationtensor($table, $vehicle_device, $violation_update){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");
    $this->dbtensor->where("violation_vehicle_device", $vehicle_device);
    $this->dbtensor->where("violation_update", $violation_update);
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function insertviolation($table, $data){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    return $this->dbalarm->insert($table, $data);
  }

  function insertviolationtensor($table, $data){
    $this->dbtensor = $this->load->database("tensor_report", true);
    return $this->dbtensor->insert($table, $data);
  }

  function updateviolation($table, $where, $wherenya, $data){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->where($where, $wherenya);
    return $this->dbalarm->update($table, $data);
  }

  function update_post_event($table, $where, $wherenya, $data){
    $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
    $this->dbalarm->where($where, $wherenya);
    return $this->dbalarm->update($table, $data);
  }

  function inserttooffsidetime($data){
    $this->dbts = $this->load->database("webtracking_ts", true);
    return $this->dbts->insert("ts_offsidetimealert", $data);
  }

  function getmastervehicle(){
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

}
?>
