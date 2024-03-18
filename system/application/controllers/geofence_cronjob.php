<?php
include "base.php";

class Geofence_cronjob extends Base
{
    function __construct()
    {
        parent::__construct();

        $this->load->model("gpsmodel");
        $this->load->model("configmodel");
        $this->load->library('email');
        $this->load->helper('email');
        $this->load->helper('common');
    }

    // operasional location = location , location_idle, location_off -> operational_bylocation, compare_km, speed_avg_from_operasional
    // operasional breakdown = location_breakdown_view (inc getOperational_bylocation_idle, getOperational_bylocation_move)  
    //  					-> operational_bylocation_idle_breakdown , operational_bylocation_move_breakdown

    //http://jsfiddle.net/izothep/myork5sa/  
    //http://semantia.com.au/articles/highcharts-drill-down-stacked-columns/
    //http://jsfiddle.net/bge14m3a/1/
	
	function all_report($userid="",$orderby="",$startdate="",$enddate=""){
		$this->data_perunit($userid,$orderby,$startdate,$enddate);
		$this->ritase($userid,$orderby,$startdate,$enddate);
	}

    function data_perunit($userid = "", $orderby = "", $startdate = "", $enddate = "")
    {
        $startproses = date("Y-m-d H:i:s");
        $report = "georeport_";
        $report_location = "location_";
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
            $year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "") {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
            $month = date("F", strtotime($startdate));
            $year = date("Y", strtotime($startdate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
            $year = date("Y", strtotime("yesterday"));
        }

        if ($enddate != "") {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $month = date("F", strtotime($enddate));
            $year = date("Y", strtotime($enddate));
        }
        if ($orderby == "") {
            $orderby = "asc";
        }

        //print_r($startdate." ".$enddate);exit();
        switch ($month) {
            case "January":
                $dbtable = $report . "januari_" . $year;
                $dbtable_location = $report_location . "januari_" . $year;
                break;
            case "February":
                $dbtable = $report . "februari_" . $year;
                $dbtable_location = $report_location . "februari_" . $year;
                break;
            case "March":
                $dbtable = $report . "maret_" . $year;
                $dbtable_location = $report_location . "maret_" . $year;
                break;
            case "April":
                $dbtable = $report . "april_" . $year;
                $dbtable_location = $report_location . "april_" . $year;
                break;
            case "May":
                $dbtable = $report . "mei_" . $year;
                $dbtable_location = $report_location . "mei_" . $year;
                break;
            case "June":
                $dbtable = $report . "juni_" . $year;
                $dbtable_location = $report_location . "juni_" . $year;
                break;
            case "July":
                $dbtable = $report . "juli_" . $year;
                $dbtable_location = $report_location . "juli_" . $year;
                break;
            case "August":
                $dbtable = $report . "agustus_" . $year;
                $dbtable_location = $report_location . "agustus_" . $year;
                break;
            case "September":
                $dbtable = $report . "september_" . $year;
                $dbtable_location = $report_location . "september_" . $year;
                break;
            case "October":
                $dbtable = $report . "oktober_" . $year;
                $dbtable_location = $report_location . "oktober_" . $year;
                break;
            case "November":
                $dbtable = $report . "november_" . $year;
                $dbtable_location = $report_location . "november_" . $year;
                break;
            case "December":
                $dbtable = $report . "desember_" . $year;
                $dbtable_location = $report_location . "desember_" . $year;
                break;
        }

        printf("===STARTING REPORT %s to %s \r\n", $startdate, $enddate);
        $this->db = $this->load->database("default", true);
        $this->db->order_by("vehicle_id", $orderby);
        $this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor,company_name");
        $this->db->where("vehicle_user_id", $userid);
        // $this->db->where("vehicle_no", "BSL 251");

        // $this->db->where("vehicle_user_id", 4408);
        $this->db->where("vehicle_status <>", 3);
       
        $this->db->join("company", "company_id = vehicle_company", 'left');

        //$this->db->limit(3);
        $this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
        //print_r($rows);exit();


        $street_PORT_ROM_list = $this->getAllStreetPort_ROM($userid); //print_r($street_PORT_ROM_list);exit();
        // $street_PORT_ROM_list = $this->getAllStreetPort_ROM(4408);

        $total_rows = count($rows);
        if ($total_rows > 0) {
            printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
            for ($i = 0; $i < $total_rows; $i++) {
                $nourut = $i + 1;
                printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $rows[$i]->vehicle_no, $nourut, $total_rows);

                unset($datainsert);
                $datainsert["georeport_vehicle_user_id"] = $rows[$i]->vehicle_user_id;
                $datainsert["georeport_vehicle_id"] = $rows[$i]->vehicle_id;
                $datainsert["georeport_vehicle_device"] = $rows[$i]->vehicle_device;
                $datainsert["georeport_vehicle_mv03"] = $rows[$i]->vehicle_mv03;
                $datainsert["georeport_vehicle_no"] = $rows[$i]->vehicle_no;
                $datainsert["georeport_vehicle_name"] = $rows[$i]->vehicle_name;
                $datainsert["georeport_vehicle_type"] = $rows[$i]->vehicle_type;
                $datainsert["georeport_company_id"] = $rows[$i]->vehicle_company;
                $datainsert["georeport_company_name"] = $rows[$i]->company_name;
                $datainsert["georeport_created"] = $startproses;
                $datainsert["georeport_flag"] = 0;

                $this->getLocation_report($datainsert, $startdate, $enddate, $dbtable, $dbtable_location, $street_PORT_ROM_list);
                // if ($ok) {
                //     printf("===INSERT OK \r\n");
                // }
            }

            // $this->dbts->close();
            //$this->dbts->cache_delete_all();
        } else {
            printf("===========TIDAK ADA DATA VEHICLE======== \r\n");
        }
        //send telegram 
        // $cron_name = "GEOFENCE REPORT";
        // $finish_time = date("Y-m-d H:i:s");
        // $message =  urlencode(
        //     "" . $cron_name . " \n" .
        //         "Periode: " . $startdate . " to " . $enddate . " \n" .
        //         "Start: " . $startproses . " \n" .
        //         "Finish: " . $finish_time . " \n"
        // );

        // $sendtelegram = $this->telegram_direct("-495868829", $message);
        // printf("===SENT TELEGRAM OK\r\n");

        printf("===========SELESAI======== \r\n");

        $this->db->close();
        $this->db->cache_delete_all();
    }
    
	function getLocation_report($datainsert, $startdate, $enddate, $dbtable, $dbtable_location, $street_list)
    {
        $this->dbreport = $this->load->database("tensor_report", true);
        $this->dbreport->select("location_report_id,location_report_gps_time,location_report_geofence_name");
        $this->dbreport->order_by("location_report_gps_time", "asc");
        $this->dbreport->group_by("location_report_gps_time");
        $this->dbreport->where("location_report_vehicle_id", $datainsert["georeport_vehicle_id"]);
        $this->dbreport->where("location_report_gps_time >=", $startdate);
        $this->dbreport->where("location_report_gps_time <=", $enddate);
        // $this->dbreport->where("location_report_speed", 0);
        // $this->dbreport->where_in("location_report_name", $master_report);
        $this->dbreport->where_in("location_report_geofence_name", $street_list);
        $this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
        // print_r($rows_loc);
        // exit();
        $total_loc = count($rows_loc);
        printf("===Total Data Location : %s \r\n", $total_loc);
        $last_row = $total_loc - 1;
        $total_delta = 0;
        $last_time = "";
        $last_loc = "";
        if ($total_loc > 0) {
            for ($i = 0; $i < $total_loc; $i++) {
                if ($i == 0) {
                    //simpan data pertama
                    $last_time = $rows_loc[$i]->location_report_gps_time; //set last time
                    $last_loc = $rows_loc[$i]->location_report_geofence_name; //set last loc
                } else {
                    if ($i == $last_row) {
                        //data terakhir
                        $geofence = $rows_loc[$i]->location_report_geofence_name;
                        $current_time = $rows_loc[$i]->location_report_gps_time;
                        $total_delta = strtotime($current_time) - strtotime($last_time);
                        $duration = get_time_difference($last_time, $current_time);
                        $show = "";
                        if ($duration[0] != 0) {
                            $show .= $duration[0] . " Day ";
                        }
                        if ($duration[1] != 0) {
                            $show .= $duration[1] . " Hour ";
                        }
                        if ($duration[2] != 0) {
                            $show .= $duration[2] . " Min ";
                        }
                        if ($show == "") {
                            $show .= "0 Min";
                        }
                        //print_r($show);exit();
                        if (preg_match("/PORT/i", $geofence) == 1) {
                            $model_data = "PORT";
                        } else if (preg_match("/ROM/i", $geofence) == 1) {
                            $model_data = "ROM";
                        } else {
                            $model_data = "";
                        }
                        // printf("model data : %s \r\n", $model_data);
                        // exit();
                        $datainsert["georeport_model"] = $model_data;
                        $datainsert["georeport_starttime"] = $last_time;
                        $datainsert["georeport_endtime"] = $current_time;
                        $datainsert["georeport_geofence"] = $geofence;
                        $datainsert["georeport_duration_sec"] = $total_delta;
                        $datainsert["georeport_duration_text"] = $show; //get last data
                        $this->dbreport->distinct();
                        $this->dbreport->where("georeport_vehicle_id", $datainsert["georeport_vehicle_id"]);
                        $this->dbreport->where("georeport_starttime", $datainsert["georeport_starttime"]);
                        $this->dbreport->where("georeport_endtime", $datainsert["georeport_endtime"]);
                        $this->dbreport->where("georeport_flag", $datainsert["georeport_flag"]);
                        $q_last = $this->dbreport->get($dbtable);
                        $total_last = $q_last->num_rows();
                        if ($total_last > 0) {
                            $this->dbreport->distinct();
                            $this->dbreport->where("georeport_vehicle_id", $datainsert["georeport_vehicle_id"]);
                            $this->dbreport->where("georeport_starttime", $datainsert["georeport_starttime"]);
                            $this->dbreport->where("georeport_endtime", $datainsert["georeport_endtime"]);
                            $this->dbreport->where("georeport_flag", $datainsert["georeport_flag"]);
                            $this->dbreport->update($dbtable, $datainsert); //UPDATE
                            printf("===UPDATE OK : %s %s %s %s %s \r\n", $datainsert["georeport_vehicle_no"], $geofence, $last_time, $current_time, $show);
                        } else {
                            $this->dbreport->distinct();
                            $this->dbreport->insert($dbtable, $datainsert); // INSERT
                            printf("===INSERT OK : %s %s %s %s %s \r\n", $datainsert["georeport_vehicle_no"], $geofence, $last_time, $current_time, $show);
                            //exit();
                        }
                    } else {
                        if ($last_loc != $rows_loc[$i]->location_report_geofence_name) {
                            $prev = $i - 1;
                            $geofence = $rows_loc[$prev]->location_report_geofence_name;
                            $current_time = $rows_loc[$prev]->location_report_gps_time;
                            $total_delta = strtotime($current_time) - strtotime($last_time);
                            $duration = get_time_difference($last_time, $current_time);

                            $show = "";
                            if ($duration[0] != 0) {
                                $show .= $duration[0] . " Day ";
                            }
                            if ($duration[1] != 0) {
                                $show .= $duration[1] . " Hour ";
                            }
                            if ($duration[2] != 0) {
                                $show .= $duration[2] . " Min ";
                            }
                            if ($show == "") {
                                $show .= "0 Min";
                            }


                            //print_r($show);exit();
                            if (preg_match("/PORT/i", $geofence) == 1) {
                                $model_data = "PORT";
                            } else if (preg_match("/ROM/i", $geofence) == 1) {
                                $model_data = "ROM";
                            } else {
                                $model_data = "";
                            }
                            // printf("model data : %s \r\n", $model_data);
                            // exit();
                            $datainsert["georeport_model"] = $model_data;
                            $datainsert["georeport_starttime"] = $last_time;
                            $datainsert["georeport_endtime"] = $current_time;
                            $datainsert["georeport_geofence"] = $geofence;
                            $datainsert["georeport_duration_sec"] = $total_delta;
                            $datainsert["georeport_duration_text"] = $show;

                            //get last data
                            $this->dbreport->distinct();
                            $this->dbreport->where("georeport_vehicle_id", $datainsert["georeport_vehicle_id"]);
                            $this->dbreport->where("georeport_starttime", $datainsert["georeport_starttime"]);
                            $this->dbreport->where("georeport_endtime", $datainsert["georeport_endtime"]);
                            $this->dbreport->where("georeport_flag", $datainsert["georeport_flag"]);
                            $q_last = $this->dbreport->get($dbtable);
                            $total_last = $q_last->num_rows();
                            if ($total_last > 0) {
                                $this->dbreport->distinct();
                                $this->dbreport->where("georeport_vehicle_id", $datainsert["georeport_vehicle_id"]);
                                $this->dbreport->where("georeport_starttime", $datainsert["georeport_starttime"]);
                                $this->dbreport->where("georeport_endtime", $datainsert["georeport_endtime"]);
                                $this->dbreport->where("georeport_flag", $datainsert["georeport_flag"]);
                                $this->dbreport->update($dbtable, $datainsert); //UPDATE
                                printf("===UPDATE OK : %s %s %s %s %s \r\n", $datainsert["georeport_vehicle_no"], $geofence, $last_time, $current_time, $show);
                            } else {
                                $this->dbreport->distinct();
                                $this->dbreport->insert($dbtable, $datainsert); // INSERT
                                printf("===INSERT OK : %s %s %s %s %s \r\n", $datainsert["georeport_vehicle_no"], $geofence, $last_time, $current_time, $show);
                                //exit();
                            }
                            $last_time = $rows_loc[$i]->location_report_gps_time; //set last time
                            $last_loc = $rows_loc[$i]->location_report_geofence_name; //set last loc
							
                        }
                    }
					
                }
            }
			printf("======================== \r\n");
            // exit();
            return true;
        } else {
            printf("===No DATA LOCATION!! \r\n");
            return false;
        }
        
		
		$this->dbreport->close();
        $this->dbreport->cache_delete_all();
		
    }

    function getAllStreetPort_ROM($userid)
    {

        $feature = array();
        $street_type_list = array("8", "7", "4", "3"); //PORT + CP + ANTRIAN BLC , ROM = 3
        $this->dbmaster = $this->load->database("default", true);
        $this->dbmaster->select("street_name,street_alias,street_type");
        $this->dbmaster->order_by("street_name", "asc");
        $this->dbmaster->group_by("street_name");
        $this->dbmaster->where("street_creator", $userid);
        $this->dbmaster->where_in("street_type", $street_type_list);
        $this->dbmaster->where("street_name !=", "PORT BBC,");
        $this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
        $total = count($rows);
        for ($x = 0; $x < $total; $x++) {
            $street_name = str_replace(",", "", $rows[$x]->street_name);
            $feature[$x] = $street_name;
        }

        //print_r($feature);exit();
        $result = $feature;

        return $result;
    }
	
	//ritase report 2
	function ritase($userid="", $orderby="", $startdate = "", $enddate = ""){
		$startproses = date("Y-m-d H:i:s");
        $report = "ritase2_";
        $report_location = "georeport_";
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
            $year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "") {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
            $month = date("F", strtotime($startdate));
            $year = date("Y", strtotime($startdate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
            $year = date("Y", strtotime("yesterday"));
        }

        if ($enddate != "") {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $month = date("F", strtotime($enddate));
            $year = date("Y", strtotime($enddate));
        }
        if ($orderby == "") {
            $orderby = "asc";
        }

        //print_r($startdate." ".$enddate);exit();
        switch ($month) {
            case "January":
                $dbtable = $report . "januari_" . $year;
                $dbtable_location = $report_location . "januari_" . $year;
                break;
            case "February":
                $dbtable = $report . "februari_" . $year;
                $dbtable_location = $report_location . "februari_" . $year;
                break;
            case "March":
                $dbtable = $report . "maret_" . $year;
                $dbtable_location = $report_location . "maret_" . $year;
                break;
            case "April":
                $dbtable = $report . "april_" . $year;
                $dbtable_location = $report_location . "april_" . $year;
                break;
            case "May":
                $dbtable = $report . "mei_" . $year;
                $dbtable_location = $report_location . "mei_" . $year;
                break;
            case "June":
                $dbtable = $report . "juni_" . $year;
                $dbtable_location = $report_location . "juni_" . $year;
                break;
            case "July":
                $dbtable = $report . "juli_" . $year;
                $dbtable_location = $report_location . "juli_" . $year;
                break;
            case "August":
                $dbtable = $report . "agustus_" . $year;
                $dbtable_location = $report_location . "agustus_" . $year;
                break;
            case "September":
                $dbtable = $report . "september_" . $year;
                $dbtable_location = $report_location . "september_" . $year;
                break;
            case "October":
                $dbtable = $report . "oktober_" . $year;
                $dbtable_location = $report_location . "oktober_" . $year;
                break;
            case "November":
                $dbtable = $report . "november_" . $year;
                $dbtable_location = $report_location . "november_" . $year;
                break;
            case "December":
                $dbtable = $report . "desember_" . $year;
                $dbtable_location = $report_location . "desember_" . $year;
                break;
        }

        printf("===STARTING REPORT %s to %s \r\n", $startdate, $enddate);
        $this->db = $this->load->database("default", true);
        $this->db->order_by("vehicle_no", $orderby);
        $this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
        $this->db->where("vehicle_user_id", $userid);
        // $this->db->where("vehicle_no", "BSL 251");
        $this->db->where("vehicle_status <>", 3);
        /* if ($imei != "" && $vtype != "") {
            $this->db->where("vehicle_device", $imei . "@" . $vtype);
        } */
 
        //$this->db->limit(3);
        $this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
       // print_r($rows);exit();
		
		$street_PORT_ROM_list = $this->getAllStreetPort_ROM($userid);
		

        $total_rows = count($rows);
        if ($total_rows > 0) {
            printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
            for ($i = 0; $i < $total_rows; $i++) {
                $nourut = $i + 1;
                printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $rows[$i]->vehicle_no, $nourut, $total_rows);

                unset($datainsert);
                $datainsert["ritase_report_vehicle_user_id"] = $rows[$i]->vehicle_user_id;
                $datainsert["ritase_report_vehicle_id"] = $rows[$i]->vehicle_id;
                $datainsert["ritase_report_vehicle_device"] = $rows[$i]->vehicle_device;
                $datainsert["ritase_report_vehicle_no"] = $rows[$i]->vehicle_no;
                $datainsert["ritase_report_vehicle_name"] = $rows[$i]->vehicle_name;
                $datainsert["ritase_report_vehicle_type"] = $rows[$i]->vehicle_type;
                $datainsert["ritase_report_vehicle_company"] = $rows[$i]->vehicle_company;
               
				$datainsert["ritase_report_type"] = 0;
				$datainsert["ritase_report_name"] = "ritase";

                $this->getGeo_report($datainsert, $startdate, $enddate, $dbtable, $dbtable_location, $street_PORT_ROM_list);
                
            }

        } else {
            printf("===========TIDAK ADA DATA VEHICLE======== \r\n");
        }
        //send telegram 
        // $cron_name = "GEOFENCE REPORT";
        // $finish_time = date("Y-m-d H:i:s");
        // $message =  urlencode(
        //     "" . $cron_name . " \n" .
        //         "Periode: " . $startdate . " to " . $enddate . " \n" .
        //         "Start: " . $startproses . " \n" .
        //         "Finish: " . $finish_time . " \n"
        // );

        // $sendtelegram = $this->telegram_direct("-495868829", $message);
        // printf("===SENT TELEGRAM OK\r\n");

        printf("===========SELESAI======== \r\n");

        $this->db->close();
        $this->db->cache_delete_all();
		
	}
	
	function getGeo_report($datainsert, $startdate, $enddate, $dbtable, $dbtable_location, $street_list)
    {
	
        $this->dbreport = $this->load->database("tensor_report", true);
        $this->dbreport->select("georeport_id,georeport_starttime,georeport_endtime,georeport_vehicle_device,georeport_vehicle_name,georeport_vehicle_no,georeport_geofence ");
        $this->dbreport->order_by("georeport_starttime", "asc");
        $this->dbreport->group_by("georeport_starttime");
        $this->dbreport->where("georeport_vehicle_id", $datainsert["ritase_report_vehicle_id"]);
        $this->dbreport->where("georeport_starttime >=", $startdate);
        $this->dbreport->where("georeport_endtime <=", $enddate);
        $this->dbreport->where_in("georeport_geofence", $street_list);
		$this->dbreport->where("georeport_model", "PORT");
		$this->dbreport->where("georeport_flag", 0);
        $this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
       // print_r($rows_loc);
        //exit();
        $total_loc = count($rows_loc);
        printf("===Total Data Ritase : %s \r\n", $total_loc);
        $last_row = $total_loc - 1;
        $total_delta = 0;
        $last_time = "";
        $last_loc = "";
        if ($total_loc > 0) {
            for ($i = 0; $i < $total_loc; $i++) {
				$skip = 0;
				$beforetime = $rows_loc[$i]->georeport_starttime;
				//looping data ritase
				$getLastROM = $this->getGeo_ROM_report($datainsert, $beforetime, $dbtable, $dbtable_location, $street_list);
				//jika tidak ada data ROM maka kosong 
				if(count($getLastROM)>0 ){
					
					$position_start_name = $getLastROM->georeport_geofence;
					$geofence_start_name = $getLastROM->georeport_geofence;
					$geofence_start_time = $getLastROM->georeport_starttime;
					
								$position_end_name = $rows_loc[$i]->georeport_geofence;
								$geofence_end_name = $rows_loc[$i]->georeport_geofence;
								$geofence_end_time	= $rows_loc[$i]->georeport_endtime;
								
								
								$ritase_report_start_time = date("Y-m-d H:i:s", strtotime($geofence_start_time)); //sudah wita
								$ritase_report_start_location = $position_start_name;
								$ritase_report_start_geofence = $geofence_start_name;
								$ritase_report_start_coordinate = "";
								
								$ritase_report_end_time = date("Y-m-d H:i:s", strtotime($geofence_end_time)); //sudah wita
								$ritase_report_end_location = $position_end_name;
								$ritase_report_end_geofence = $geofence_end_name;
								$ritase_report_end_coordinate = "";
								
								$ritase_report_driver = 0;
								$ritase_report_driver_name = "";
								
								$duration = get_time_difference($ritase_report_start_time, $ritase_report_end_time);
									
									$start_1 = dbmaketime($ritase_report_start_time);
									$end_1 = dbmaketime($ritase_report_end_time);
									$duration_sec = $end_1 - $start_1;
									
                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }
									
								$ritase_report_duration = $show;
								$ritase_report_duration_sec = $duration_sec;
								
								
								$datainsert["ritase_report_start_time"] = $ritase_report_start_time;
								$datainsert["ritase_report_start_location"] = $ritase_report_start_location;
								$datainsert["ritase_report_start_geofence"] = $ritase_report_start_geofence;
								$datainsert["ritase_report_start_coordinate"] = $ritase_report_start_coordinate;
								$datainsert["ritase_report_end_time"] = $ritase_report_end_time;
								$datainsert["ritase_report_end_location"] = $ritase_report_end_location;
								$datainsert["ritase_report_end_geofence"] = $ritase_report_end_geofence;
								$datainsert["ritase_report_end_coordinate"] = $ritase_report_end_coordinate;
								
								$datainsert["ritase_report_driver"] = $ritase_report_driver;
								$datainsert["ritase_report_driver_name"] = $ritase_report_driver_name;
								$datainsert["ritase_report_duration"] = $ritase_report_duration;
								$datainsert["ritase_report_duration_sec"] = $ritase_report_duration_sec;
								
								$this->dbreport->distinct();
								$this->dbreport->where("ritase_report_vehicle_id", $datainsert["ritase_report_vehicle_id"]);
								$this->dbreport->where("ritase_report_start_time", $datainsert["ritase_report_start_time"]);
								$this->dbreport->where("ritase_report_end_time", $datainsert["ritase_report_end_time"]);
								$q_last = $this->dbreport->get($dbtable);
								$total_last = $q_last->num_rows();
								if ($total_last > 0) {
									$this->dbreport->distinct();
									$this->dbreport->where("ritase_report_vehicle_id", $datainsert["ritase_report_vehicle_id"]);
									$this->dbreport->where("ritase_report_start_time", $datainsert["ritase_report_start_time"]);
									$this->dbreport->where("ritase_report_end_time", $datainsert["ritase_report_end_time"]);
									$this->dbreport->update($dbtable, $datainsert); //UPDATE
									printf("===UPDATE OK : %s %s to %s - %s \r\n", $datainsert["ritase_report_vehicle_no"], $geofence_start_name, $geofence_end_name,  $ritase_report_duration);
								} else {
									$this->dbreport->distinct();
									$this->dbreport->insert($dbtable, $datainsert); // INSERT
									printf("===INSERT OK : %s %s to %s -  %s \r\n", $datainsert["ritase_report_vehicle_no"], $geofence_start_name, $geofence_end_name,  $ritase_report_duration);
									//exit();
								}
							
				}else{
					
					$position_start_name =  "";
					$geofence_start_name =  "";
					$ritase_report_start_time =  "";
					$skip = 1;
					  printf("===SKIP INVALID LAST ROM!! \r\n");
					
				}
								
                
            }
			printf("======================== \r\n");
           // exit();
            return true;
        } else {
            printf("===No DATA RITASE!! \r\n");
            return false;
        }
        
		
		$this->dbreport->close();
        $this->dbreport->cache_delete_all();
		
    }
	
	function getGeo_ROM_report($datainsert, $beforetime, $dbtable, $dbtable_location, $street_list)
    {
        $this->dbreport = $this->load->database("tensor_report", true);
        $this->dbreport->select("georeport_id,georeport_starttime,georeport_endtime,georeport_vehicle_device,georeport_vehicle_name,georeport_vehicle_no,georeport_geofence ");
        $this->dbreport->order_by("georeport_starttime", "desc");
        $this->dbreport->group_by("georeport_starttime");
        $this->dbreport->where("georeport_vehicle_id", $datainsert["ritase_report_vehicle_id"]);
        $this->dbreport->where("georeport_starttime <", $beforetime);
        $this->dbreport->where("georeport_model", "ROM");
		$this->dbreport->where("georeport_duration_sec >", 120); //diatas 1 menit
		$this->dbreport->where("georeport_flag", 0);
		$this->dbreport->limit(1);
        $this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_rom = $q_loc->row();
        
        $total_rom = count($rows_rom);
        printf("===Total Data ROM : %s \r\n", $total_rom);
      
        return $rows_rom;
        
		
		$this->dbreport->close();
        $this->dbreport->cache_delete_all();
		
    }

    function telegram_direct($groupid, $message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $url = "http://lacak-mobil.com/telegram/telegram_directpost";

        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
