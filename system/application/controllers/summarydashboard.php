<?php
include "base.php";

class Summarydashboard extends Base
{
    function __construct()
    {
        parent::Base();
        $this->load->model("dashboardmodel");
        $this->load->model("m_operational");
    }

    function index()
    {
        if (!isset($this->sess->user_type)) {
            redirect('dashboard');
        }

        $privilegecode   = $this->sess->user_id_role;




        $rows                           = $this->get_vehicle_pjo();

        $rows_company                   = $this->get_company();
        $this->params["vehicles"]       = $rows;
        $this->params["rcompany"]       = $rows_company;
        $this->params['code_view_menu'] = "report";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_summary_dashboard', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_summary_dashboard', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_summary_dashboard', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_summary_dashboard', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_summary_dashboard', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_summary_dashboard', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search()
    {
        $company = $this->input->post('company');
        $vehicle = $this->input->post('vehicle');
        $datein = $this->input->post('date');
        $interval = $this->input->post('interval');
        $date = date('Y-m-d', strtotime($datein));
        $shift = $this->input->post('shift');
        $month = date("F", strtotime($date));
        $year = date("Y", strtotime($date));
        $report_location = "location_";

        if (date("Y-m-d") < $date) {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "total" => 0, "data" => array()));
            exit();
        }
        $lastdate = date("Y-m-t", strtotime($datein));
        $monthn = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
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
            "location_report_vehicle_device" => $vehicle,
            "date" => $arraydate,
            "table_name" => $dbtable_location
        );
        if (isset($dbtable_location2)) {
            $input["table_name_2"] = $dbtable_location2;
        }


        $data_fix = array();

        /* if ($shift == 0) {
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

        $total_loc = count($rows_loc); */

        
            //$callback["input"] = $input;
           //$callback["total data"] = $total_loc;
            $callback["data"] = $data_fix;
            //$callback["data dari table"] = $rows_loc;

            echo json_encode($callback);
        
    
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
                    $database = $databases[$json->vehicle_ip][$json->vehicle_port];
                    $table = $this->config->item("external_gpstable");
                    $tableinfo = $this->config->item("external_gpsinfotable");
                    $this->dbhist = $this->load->database($database, TRUE);
                    $this->dbhist2 = $this->load->database("gpshistory", true);
                } else {
                    $table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
                    $tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
                    $this->dbhist = $this->load->database("default", TRUE);
                    $this->dbhist2 = $this->load->database("gpshistory", true);
                }

                $vehicle_device = explode("@", $rowvehicle->vehicle_device);
                $vehicle_no = $rowvehicle->vehicle_no;
                $vehicle_dev = $rowvehicle->vehicle_device;
                $vehicle_name = $rowvehicle->vehicle_name;
                $vehicle_type = $rowvehicle->vehicle_type;

                if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
                    $tablehist = $vehicle_device[0] . "@t5_gps";
                    $tablehistinfo = $vehicle_device[0] . "@t5_info";
                } else {
                    $tablehist = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
                    $tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
                }


                $this->dbhist->select("gps_time,gps_mvd");
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


                $this->dbhist2->select("gps_time,gps_mvd");
                $this->dbhist2->where("gps_name", $vehicle_device[0]);
                $this->dbhist2->where("gps_speed", 0);
                $this->dbhist2->where("gps_time >=", $sdate);
                $this->dbhist2->where("gps_time <=", $edate);
                $this->dbhist2->where("gps_mvd >", 0);
                $this->dbhist2->order_by("gps_time", "asc");
                $this->dbhist2->group_by("gps_time");

                $this->dbhist2->from($tablehist);
                $q2 = $this->dbhist2->get();
                $rows2 = $q2->result();

                $rows = array_merge($rows1, $rows2);
                $trows = count($rows);

                $totaldata = $trows;
                $data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);

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

    function getDataFuel_report($vehicle, $dbtable_location, $startdate, $enddate)
    {

        $total_cons = 0;

        $this->dbreport = $this->load->database("tensor_report", true);
        $this->dbreport->select("location_report_id,location_report_fuel_data,location_report_gps_time");
        $this->dbreport->order_by("location_report_gps_time", "asc");
        $this->dbreport->group_by("location_report_gps_time");
        $this->dbreport->where("location_report_vehicle_device", $vehicle);
        $this->dbreport->where("location_report_gps_time >=", $startdate);
        $this->dbreport->where("location_report_gps_time <=", $enddate);
        $this->dbreport->where("location_report_fuel_data >", 0);
        $this->dbreport->where("location_report_speed", 0);
        $this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
        $total_loc = count($rows_loc);
        //print_r($total_loc);exit();
        for ($x = 0; $x < $total_loc; $x++) {
            $nosort = $x + 1;

            //printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
            if ($nosort == $total_loc) {
                //printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
            } else {
                $first_data = $rows_loc[$x]->location_report_fuel_data;
                $next_data = $rows_loc[$x + 1]->location_report_fuel_data;
                $delta_cons = $first_data - $next_data;

                $first_time = $rows_loc[$x]->location_report_gps_time;
                $next_time = $rows_loc[$x + 1]->location_report_gps_time;

                $first_time_sec = strtotime($first_time);
                $next_time_sec = strtotime($next_time);
                $delta_time_sec = $next_time_sec - $first_time_sec;

                if ($delta_cons != 0) {
                    if ($next_data > $first_data) {
                        //printf("===Pengisian: %s  %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time);
                    } else {

                        if ($delta_cons > 15) {
                            //printf("===Asumsi invalid: %s %s delta min %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time,$delta_time_sec/60);

                        } else {
                            //printf("===Konsumsi BBM: %s %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time);
                            $total_cons = $total_cons + $delta_cons;
                        }
                    }
                }
            }
        }


        printf("===TOTAL CONS: %s  \r\n", $total_cons);
        /* if($total_cons > 0){

			exit();
		} */
        $this->dbreport->close();
        $this->dbreport->cache_delete_all();

        return $total_cons;
    }


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
            $options = "<option value='0' selected='selected' >--Select Vehicle--</option>";
            $i = 1;
            foreach ($rd as $obj) {
                $options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
                $i++;
            }

            echo $options;
            return;
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
}
