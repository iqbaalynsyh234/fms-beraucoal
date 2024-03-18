<?php
include "base.php";

class Truck extends Base
{

    function __construct()
    {
        parent::Base();
        // $this->load->helper('common_helper');
        // $this->load->helper('email');
        // $this->load->library('email');
        $this->load->model("dashboardmodel");
        $this->load->model("m_production");
        // $this->load->model("log_model");
        // $this->load->helper('common');
    }

    function index()
    {
        $this->hour();
    }

    function hour()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]      = $this->get_location();

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search()
    {
        $company = $this->input->post('company');
        $location = $this->input->post('location');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        if (date("Y-m-d") < $date) {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "total" => 0, "data" => array()));
            exit();
        }
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

        $shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
        $shift21 = array("18", "19", "20", "21", "22", "23");
        $shift22 = array("00", "01", "02", "03", "04", "05");
        $allshift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
        $allshift2 = array("00", "01", "02", "03", "04", "05");



        //get vehicle info
        $this->db->select("vehicle_name,vehicle_no,company_name");
        $this->db->order_by("company_name", "asc");
        $this->db->where("vehicle_status <>", 3);

        if ($company != 0) {
            $this->db->where("vehicle_company", $company);
        }
		$this->db->where("vehicle_user_id", 4408);
        $this->db->join("company", "vehicle_company = company_id", "left");
        $qd = $this->db->get("vehicle");
        $total_unit = $qd->num_rows();
        $rd = $qd->result();
        $total_unit_percontractor = array();
        if ($company == 0) {
            for ($x = 0; $x < $total_unit; $x++) {
                if ($rd[$x]->company_name != null) {
                    if (!isset($total_unit_percontractor[$rd[$x]->company_name])) {
                        $total_unit_percontractor[$rd[$x]->company_name] = 1;
                    } else {
                        $jml = (int)$total_unit_percontractor[$rd[$x]->company_name] + 1;
                        $total_unit_percontractor[$rd[$x]->company_name] = $jml;
                    }
                }
            }
        }
        //end get vehicle info
        //location selected
        $this->dbts = $this->load->database("webtracking_ts", true);

        $this->db->order_by("location_report_location", "asc");
        if ($location == "STREET.0") {
            $this->dbts->where("location_report_group", "STREET");
            $this->dbts->where("location_report_jalur", "kosongan");
        } else if ($location == "STREET.1") {
            $this->dbts->where("location_report_group", "STREET");
            $this->dbts->where("location_report_jalur", "muatan");
        } else if ($location == "0") {
            $this->dbts->where_in("location_report_group", array("STREET", "ROM", "PORT"));
        } else {
            $exp = explode(" ", $location);
            if ($exp[0] == "ROM") {
                //lokasi ROM
                $this->dbts->where("location_report_location", $location);
            } else if ($exp[0] == "PORT") {
                //lokasi PORT
                $this->dbts->where("location_report_group", "PORT");
                $this->dbts->where("location_report_location LIKE ", "%" . $exp[1] . "%");
            }
        }

        if ($shift == 1) {
            for ($s = 0; $s < count($shift1); $s++) {
                $shift1[$s] .= ":00:00";
            }
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift1);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            for ($s = 0; $s < count($shift21); $s++) {
                $shift21[$s] .= ":00:00";
            }
            for ($s = 0; $s < count($shift22); $s++) {
                $shift22[$s] .= ":00:00";
            }
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift21);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->db->order_by("location_report_location", "asc");
            if ($location == "STREET.0") {
                $this->dbts->where("location_report_group", "STREET");
                $this->dbts->where("location_report_jalur", "kosongan");
            } else if ($location == "STREET.1") {
                $this->dbts->where("location_report_group", "STREET");
                $this->dbts->where("location_report_jalur", "muatan");
            } else if ($location == "0") {
                $this->dbts->where_in("location_report_group", array("STREET", "ROM", "PORT"));
            } else {
                $exp = explode(" ", $location);
                if ($exp[0] == "ROM") {
                    //lokasi ROM
                    $this->dbts->where("location_report_location", $location);
                } else if ($exp[0] == "PORT") {
                    //lokasi PORT
                    $this->dbts->where("location_report_group", "PORT");
                    $this->dbts->where("location_report_location LIKE ", "%" . $exp[1] . "%");
                }
            }
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group");
            $this->dbts->where("location_report_gps_date", $next);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift22);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {

            for ($s = 0; $s < count($allshift1); $s++) {
                $allshift1[$s] .= ":00:00";
            }
            for ($s = 0; $s < count($allshift2); $s++) {
                $allshift2[$s] .= ":00:00";
            }
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $allshift1);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->db->order_by("location_report_location", "asc");
            if ($location == "STREET.0") {
                $this->dbts->where("location_report_group", "STREET");
                $this->dbts->where("location_report_jalur", "kosongan");
            } else if ($location == "STREET.1") {
                $this->dbts->where("location_report_group", "STREET");
                $this->dbts->where("location_report_jalur", "muatan");
            } else if ($location == "0") {
                $this->dbts->where_in("location_report_group", array("STREET", "ROM", "PORT"));
            } else {
                $exp = explode(" ", $location);
                if ($exp[0] == "ROM") {
                    //lokasi ROM
                    $this->dbts->where("location_report_location", $location);
                } else if ($exp[0] == "PORT") {
                    //lokasi PORT
                    $this->dbts->where("location_report_group", "PORT");
                    $this->dbts->where("location_report_location LIKE ", "%" . $exp[1] . "%");
                }
            }
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group");
            $this->dbts->where("location_report_gps_date", $next);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $allshift2);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }

        if ($nr > 0) {
            $data_fix = array();
            $dhour = array();
            $dcompany = array();
            $dlocation = array();
            $c = array();
            $l = array();
            for ($i = 0; $i < $nr; $i++) {
                $exp = explode(":", $data[$i]['location_report_gps_hour']);

                if (!isset($hour[$exp[0]])) {
                    $hour[$exp[0]] = 1;
                    $dhour[] = $exp[0];
                }
                if (!isset($c[$data[$i]['location_report_company_name']])) {

                    $c[$data[$i]['location_report_company_name']] = $data[$i]['location_report_company_name'];
                    $dcompany[] = $c[$data[$i]['location_report_company_name']];
                }
                if (!isset($l[$data[$i]['location_report_location']])) {

                    $l[$data[$i]['location_report_location']] = $data[$i]['location_report_location'];
                    $dlocation[] = $l[$data[$i]['location_report_location']];
                }
                // $data_fix[$exp[0]][$data[$i]['location_report_company_name']][$data[$i]['location_report_location']] = array("vehicle" => $data[$i]['location_report_vehicle_no']);
                if (!isset($data_fix[$exp[0]][$data[$i]['location_report_company_name']])) {
                    $data_fix[$exp[0]][$data[$i]['location_report_company_name']] = array();
                    $d = array(
                        "company" => $data[$i]['location_report_company_name'],
                        "vehicle" => $data[$i]['location_report_vehicle_no'],
                        "location" => $data[$i]['location_report_location'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['location_report_company_name']], $d);
                } else {
                    $d = array(
                        "company" => $data[$i]['location_report_company_name'],
                        "vehicle" => $data[$i]['location_report_vehicle_no'],
                        "location" => $data[$i]['location_report_location'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['location_report_company_name']], $d);
                }
                if (!isset($unit_location[$data[$i]['location_report_vehicle_no']])) {
                    $unit_location[$data[$i]['location_report_vehicle_no']] = $data[$i]['location_report_vehicle_no'];
                }
                $data[$i]['hour'] = $exp[0];
            }

            echo json_encode(array(
                "code" => 200,
                "error" => false,
                "msg" => "success",
                "data" => $data, //data mentah dari source data untuk compare data fix
                "total_unit_location" => count($unit_location), //total unit dilokasi terpilih
                "total_unit" => $total_unit, //total unit semua kontraktor atau perkontraktor dipilih
                "total_unit_per_contractor" => $total_unit_percontractor, //total unit tiap kontraktor
                "data_hour" => $dhour, //data jam
                "data_company" => $dcompany, //data kontraktor
                "data_location" => $dlocation, //data lokasi
                "data_fix" => $data_fix, //data fix
                "length_company" => count($dcompany), //jumlah kontraktor
                "length_location" => count($dlocation), //jumlah lokasi
                "length_hour" => count($dhour) //jumlah jam
            ));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "data" => $data, "total" => $nr));
        }
    }

    function hourdev1()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]      = $this->get_location();

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    // USER FUNCTION START
    function hourv1()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;
        $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

        $rows_company                   = $this->get_company();
        // $datastatus                     = explode("|", $rstatus);
        // $this->params['total_online']   = $datastatus[0] + $datastatus[1]; //p + K
        // $this->params['total_vehicle']  = $datastatus[3];
        // $this->params['total_offline']  = $datastatus[2];
        $this->params["rcompany"]       = $rows_company;

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_hour_v1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }


    function searchv1()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

        $this->db->select("vehicle_name,vehicle_no,company_name");
        // $this->db->group_by("vehicle_company");
        $this->db->order_by("company_name", "asc");
        $this->db->where("vehicle_status <>", 3);

        if ($company != 0) {
            $this->db->where("vehicle_company", $company);
        }
        $this->db->join("company", "vehicle_company = company_id", "left");
        $qd = $this->db->get("vehicle");
        $total_unit = $qd->num_rows();
        $rd = $qd->result();
        $total_unit_percontractor = array();
        if ($company == 0) {
            for ($x = 0; $x < $total_unit; $x++) {
                if ($rd[$x]->company_name != null) {
                    if (!isset($total_unit_percontractor[$rd[$x]->company_name])) {
                        $total_unit_percontractor[$rd[$x]->company_name] = 1;
                    } else {
                        $jml = (int)$total_unit_percontractor[$rd[$x]->company_name] + 1;
                        $total_unit_percontractor[$rd[$x]->company_name] = $jml;
                    }
                }
            }
        }


        // $dateym = date('Y-m-', $date);
        // $tomorrow = date('d', $date) + 1;
        // $nextday = $dateym . $tomorrow;
        $this->dbts = $this->load->database("webtracking_ts", true);
        $this->dbts->select("*");
        if ($shift == 1) {
            $shift = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
            $this->dbts->where("autocheck_date", $date);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $shift1 = array("18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }
        // $vehicle = $this->dashboardmodel->getvehicle_report();
        // $totalvehicle = array();
        $company = array();
        $hour = array();
        $result = array();
        $c = array();
        // $cid = array();
        $h = array();
        if ($nr > 0) {
            for ($i = 0; $i < $nr; $i++) {
                if (!isset($company[$data[$i]['autocheck_company_name']])) {
                    $company[$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_company_name'];
                    $c[] = $data[$i]['autocheck_company_name'];
                }
                if (!isset($hour[$data[$i]['autocheck_hour']])) {
                    $hour[$data[$i]['autocheck_hour']] = $data[$i]['autocheck_hour'];
                    $h[] = $data[$i]['autocheck_hour'];
                }
                $result[$data[$i]['autocheck_hour']][$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_total_duty'] . " (" . $data[$i]['autocheck_total_duty_persen'] . "%)";
            }
        }
        // for ($i = 0; $i < $clength; $i++) {
        //     for($j=0; $j<count($vehicle); $i++){
        //         if (!isset($totalvehicle[$cid[$i]]));
        //     }
        // }

        // $total_unit_percontractor = $c;


        echo json_encode(array("code" => 200, "msg" => "success", "data" => $result, "company" => $c, "hour" => $h, "total" => $nr, "clength" => count($company), "hlength" => count($hour), "date" => $arraydate, "total_unit" => $total_unit, "vehicle" => $rd, "total_unit_contractor" => $total_unit_percontractor));
    }

    function pool()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;
        $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

        $rows_company                   = $this->get_company();
        // $datastatus                     = explode("|", $rstatus);
        // $this->params['total_online']   = $datastatus[0] + $datastatus[1]; //p + K
        // $this->params['total_vehicle']  = $datastatus[3];
        // $this->params['total_offline']  = $datastatus[2];
        $this->params["rcompany"]       = $rows_company;


        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search_on_pool()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

        $this->db->select("vehicle_name,vehicle_no,company_name");
        // $this->db->group_by("vehicle_company");
        $this->db->order_by("company_name", "asc");
        $this->db->where("vehicle_status <>", 3);
        if ($company != 0) {
            $this->db->where("vehicle_company", $company);
        }
        $this->db->join("company", "vehicle_company = company_id", "left");
        $qd = $this->db->get("vehicle");
        $total_unit = $qd->num_rows();
        $rd = $qd->result();
        $total_unit_percontractor = array();

        if ($company == 0) {
            for ($x = 0; $x < $total_unit; $x++) {
                if ($rd[$x]->company_name != null) {
                    if (!isset($total_unit_percontractor[$rd[$x]->company_name])) {
                        $total_unit_percontractor[$rd[$x]->company_name] = 1;
                    } else {
                        $jml = (int)$total_unit_percontractor[$rd[$x]->company_name] + 1;
                        $total_unit_percontractor[$rd[$x]->company_name] = $jml;
                    }
                }
            }
        }


        // $dateym = date('Y-m-', $date);
        // $tomorrow = date('d', $date) + 1;
        // $nextday = $dateym . $tomorrow;
        $this->dbts = $this->load->database("webtracking_ts", true);
        $this->dbts->select("*");
        if ($shift == 1) {
            $shift = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
            $this->dbts->where("autocheck_date", $date);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $shift1 = array("18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }
        // $vehicle = $this->dashboardmodel->getvehicle_report();
        // $totalvehicle = array();
        $company = array();
        $hour = array();
        $result = array();
        $c = array();
        // $cid = array();
        $h = array();
        if ($nr > 0) {
            for ($i = 0; $i < $nr; $i++) {
                if (!isset($company[$data[$i]['autocheck_company_name']])) {
                    $company[$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_company_name'];
                    $c[] = $data[$i]['autocheck_company_name'];
                }
                if (!isset($hour[$data[$i]['autocheck_hour']])) {
                    $hour[$data[$i]['autocheck_hour']] = $data[$i]['autocheck_hour'];
                    $h[] = $data[$i]['autocheck_hour'];
                }
                $result[$data[$i]['autocheck_hour']][$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_total_parkir'] . " (" . $data[$i]['autocheck_total_parkir_persen'] . "%)";
            }
        }

        echo json_encode(array("code" => 200, "msg" => "success", "data" => $result, "company" => $c, "hour" => $h, "total" => $nr, "clength" => count($company), "hlength" => count($hour), "date" => $arraydate, "total_unit" => $total_unit, "vehicle" => $rd, "total_unit_contractor" => $total_unit_percontractor));
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
        } elseif ($privilegecode == 4) {
            $this->db->where("company_created_by", 4408);
        } elseif ($privilegecode == 5) {
            $this->db->where("company_created_by", 4408);
        } elseif ($privilegecode == 6) {
            $this->db->where("company_created_by", 4408);
        }

        $this->db->where("company_flag", 0);
        $qd = $this->db->get("company");
        $rd = $qd->result();

        return $rd;
    }

    function get_company_pool()
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


    function get_location()
    {
        $this->db->select("street_alias");
        $this->db->order_by("street_alias", "asc");
        $this->db->where_in("street_type", array(3, 4));
        $q = $this->db->get("street");

        return $q->result();
    }
}
