<?php
include "base.php";

class Ritasehour extends Base
{

    function __construct()
    {
        parent::Base();
        // $this->load->helper('common_helper');
        // $this->load->helper('email');
        // $this->load->library('email');
        $this->load->model("dashboardmodel");
        // $this->load->model("m_production");
        // $this->load->model("log_model");
        // $this->load->helper('common');
    }

    // USER FUNCTION START
    function index()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();
        $this->params["rcompany"]       = $rows_company;

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 7) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
        }else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_ritase_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search()
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

		$port_list = array("PORT BIB","PORT BIR","PORT TIA");

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
        $input = array(
            "company" => $company,
            "date" => $arraydate,
            "shift" => $shift
        );

        $this->dbts = $this->load->database("webtracking_ts", true);
        if ($shift == 1) {
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $shift = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);

            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $shift1 = array("18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift1);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $this->dbts->where("ritase_report_gps_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift2);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $shift1 = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift1);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $this->dbts->where("ritase_report_gps_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift2);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }


        if ($nr > 0) {
            echo json_encode(array("code" => 200, "error" => false, "msg" => "success", "input" => $input, "data" => $data, "total" => $nr));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty"));
        }
    }

	function board()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]      = $this->get_location();
        $this->params['code_view_menu'] = "dashboard";


        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        }elseif ($privilegecode == 7) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_ritase_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search_board_bk()
    {
        $company = $this->input->post('company'); //port
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

		$port_list = array("PORT BIB","PORT BIR","PORT TIA");

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

        $this->dbts->order_by("ritase_report_to", "asc");
       /*  if ($location == "STREET.0") {
			$this->dbts->where("ritase_report_to", "PORT BIB");

        } else if ($location == "STREET.1") {
			$this->dbts->where("ritase_report_to", "PORT BIR");

        } else if ($location == "STREET.2") {
			$this->dbts->where("ritase_report_to", "PORT TIA");

        } */


		if ($location == "0") {
           $this->dbts->where_in("ritase_report_to", array("PORT BIB", "PORT BIR", "PORT TIA"));
        } else {
            $exp = explode(" ", $location);
            if ($exp[0] == "ROM") {
                //lokasi ROM
                $this->dbts->where("ritase_report_from", $location);
            } else if ($exp[0] == "PORT") {
                //lokasi PORT
                $this->dbts->where("ritase_report_to", $location);
                //$this->dbts->where("ritase_report_to LIKE ", "%" . $exp[1] . "%");
            }
        }

        if ($shift == 1) {
            for ($s = 0; $s < count($shift1); $s++) {
                $shift1[$s] .= ":00:00";
            }
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_from,ritase_report_to");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift1);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            for ($s = 0; $s < count($shift21); $s++) {
                $shift21[$s] .= ":00:00";
            }
            for ($s = 0; $s < count($shift22); $s++) {
                $shift22[$s] .= ":00:00";
            }
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_from,ritase_report_to");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift21);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();

            $this->dbts->distinct();

			$this->dbts->order_by("ritase_report_to", "asc");
			if ($location == "0") {
			$this->dbts->where_in("ritase_report_to", array("PORT BIB", "PORT BIR", "PORT TIA"));
			} else {
				$exp = explode(" ", $location);
				if ($exp[0] == "ROM") {
					//lokasi ROM
					$this->dbts->where("ritase_report_from", $location);
				} else if ($exp[0] == "PORT") {
					//lokasi PORT
					$this->dbts->where("ritase_report_to", $location);
					//$this->dbts->where("ritase_report_to LIKE ", "%" . $exp[1] . "%");
				}
			}
            /*if ($location == "STREET.0") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "kosongan");
            } else if ($location == "STREET.1") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "muatan");
            } else if ($location == "0") {
                $this->dbts->where_in("ritase_report_group", array("STREET", "ROM", "PORT"));
            } else {
                $exp = explode(" ", $location);
                if ($exp[0] == "ROM") {
                    //lokasi ROM
                    $this->dbts->where("ritase_report_location", $location);
                } else if ($exp[0] == "PORT") {
                    //lokasi PORT
                    $this->dbts->where("ritase_report_group", "PORT");
                    $this->dbts->where("ritase_report_location LIKE ", "%" . $exp[1] . "%");
                }
            } */


            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_from,ritase_report_to");
            $this->dbts->where("ritase_report_gps_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift22);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
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
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_from,ritase_report_to");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $allshift1);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->order_by("ritase_report_to", "asc");
			if ($location == "0") {
			$this->dbts->where_in("ritase_report_to", array("PORT BIB", "PORT BIR", "PORT TIA"));
			} else {
				$exp = explode(" ", $location);
				if ($exp[0] == "ROM") {
					//lokasi ROM
					$this->dbts->where("ritase_report_from", $location);
				} else if ($exp[0] == "PORT") {
					//lokasi PORT
					$this->dbts->where("ritase_report_to", $location);
					//$this->dbts->where("ritase_report_to LIKE ", "%" . $exp[1] . "%");
				}
			}

            /* if ($location == "STREET.0") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "kosongan");
            } else if ($location == "STREET.1") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "muatan");
            } else if ($location == "0") {
                $this->dbts->where_in("ritase_report_group", array("STREET", "ROM", "PORT"));
            } else {
                $exp = explode(" ", $location);
                if ($exp[0] == "ROM") {
                    //lokasi ROM
                    $this->dbts->where("ritase_report_location", $location);
                } else if ($exp[0] == "PORT") {
                    //lokasi PORT
                    $this->dbts->where("ritase_report_group", "PORT");
                    $this->dbts->where("ritase_report_location LIKE ", "%" . $exp[1] . "%");
                }
            } */


            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_from,ritase_report_to");
            $this->dbts->where("ritase_report_gps_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $allshift2);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
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
                $exp = explode(":", $data[$i]['ritase_report_gps_hour']);

                if (!isset($hour[$exp[0]])) {
                    $hour[$exp[0]] = 1;
                    $dhour[] = $exp[0];
                }
                if (!isset($c[$data[$i]['ritase_report_company_name']])) {

                    $c[$data[$i]['ritase_report_company_name']] = $data[$i]['ritase_report_company_name'];
                    $dcompany[] = $c[$data[$i]['ritase_report_company_name']];
                }
                if (!isset($l[$data[$i]['ritase_report_to']])) {

                    $l[$data[$i]['ritase_report_to']] = $data[$i]['ritase_report_to'];
                    $dlocation[] = $l[$data[$i]['ritase_report_to']];
                }
                // $data_fix[$exp[0]][$data[$i]['ritase_report_company_name']][$data[$i]['ritase_report_to']] = array("vehicle" => $data[$i]['ritase_report_vehicle_no']);


				if (!isset($data_fix[$exp[0]][$data[$i]['ritase_report_to']])) {
                    $data_fix[$exp[0]][$data[$i]['ritase_report_to']] = array();
                    $d = array(
                        "company" => $data[$i]['ritase_report_company_name'],
                        "vehicle" => $data[$i]['ritase_report_vehicle_no'],
                        "location" => $data[$i]['ritase_report_to'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['ritase_report_to']], $d);
                } else {
                    $d = array(
                        "company" => $data[$i]['ritase_report_company_name'],
                        "vehicle" => $data[$i]['ritase_report_vehicle_no'],
                        "location" => $data[$i]['ritase_report_to'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['ritase_report_to']], $d);
                }
                if (!isset($unit_location[$data[$i]['ritase_report_vehicle_no']])) {
                    $unit_location[$data[$i]['ritase_report_vehicle_no']] = $data[$i]['ritase_report_vehicle_no'];
                }
                $data[$i]['hour'] = $exp[0];
            }

			//print_r($data_fix);exit();

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
				"length_data" => count($data), //data all
                "length_company" => count($dcompany), //jumlah kontraktor
                "length_location" => count($dlocation), //jumlah lokasi
                "length_hour" => count($dhour) //jumlah jam
            ));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "data" => $data, "total" => $nr));
        }
    }
	
	//new source table ritase_hour
	function search_board()
    {
        $company = $this->input->post('company'); //port
        $location = $this->input->post('location');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        if (date("Y-m-d") < $date) {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "total" => 0, "data" => array()));
            exit();
        }
        $lastdate = date("Y-m-t", strtotime($date));
        $year = date("Y", strtotime($date));
        $month = date("m", strtotime($date));
        $day = date('d', strtotime($date));
		$endofday = date('d', strtotime($lastdate));
		$yesterdate    = date("Y-m-d", strtotime("yesterday"));
		
		$diff_month = 0;
		//1 = merge dengan Before month data
		if($day == "01"){
			$diff_month = 1;
		}
		
		//2 = merge dengan Next month data
		if($day == $endofday){
			$diff_month = 2;
		}
		
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

		$port_all_list = $this->config->item("port_register_autocheck");
		$port_bib_list = array("PORT BIB","BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8","BIB WB 1","BIB WB 2");
		$port_bir_list = array("PORT BIR","BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2");
		$port_tia_list = array("PORT TIA");
		
		$days_report = date("d", strtotime($date));
		$month_report = date("F", strtotime($date));
		$year_report = date("Y", strtotime($date));
		$before_status = 0;
		$year_before = date('Y',strtotime('-1 year',strtotime($year_report)));
		$year_next = date('Y',strtotime('+1 year',strtotime($year_report)));
		
		$nowdate = date("Y-m-d");
		//$selected_date = date("Y-m-d", strtotime($date))
		
		if($date == $nowdate){
			$report = "ritase_hour_";
			$report_ritase = "ritase_hour_";
		}else{
			$report = "ritase_full_";
			$report_ritase = "ritase_full_";
		}
		
		$sdate_loc = date("Y-m-d H:i:s", strtotime($date." "."00:00:00"));
		$edate_loc = date("Y-m-d H:i:s", strtotime($date." "."23:59:59"));
		
		if($date == $yesterdate){
			$loc_result = $this->getStatusLocReport("4408","all",$sdate_loc,$edate_loc);
			if($loc_result == "ON PROCESS")
			{
				$report = "ritase_hour_";
				$report_ritase = "ritase_hour_";
			}
		}
		
		switch ($month_report)
		{
			case "January":
            $dbtable = $report."januari_".$year_report;
			$dbtable_ritase = $report_ritase."januari_".$year_report;
			$dbtable_before = $report."desember_".$year_before;
			$dbtable_next = $report."februari_".$year_report;
			break;
			case "February":
            $dbtable = $report."februari_".$year_report;
			$dbtable_ritase = $report_ritase."februari_".$year_report;
			$dbtable_before = $report."januari_".$year_report;
			$dbtable_next = $report."maret_".$year_report;
			break;
			case "March":
            $dbtable = $report."maret_".$year_report;
			$dbtable_ritase = $report_ritase."maret_".$year_report;
			$dbtable_before = $report."februari_".$year_report;
			$dbtable_next = $report."april_".$year_report;
			break;
			case "April":
            $dbtable = $report."april_".$year_report;
			$dbtable_ritase = $report_ritase."april_".$year_report;
			$dbtable_before = $report."maret_".$year_report;
			$dbtable_next = $report."mei_".$year_report;
			break;
			case "May":
            $dbtable = $report."mei_".$year_report;
			$dbtable_ritase = $report_ritase."mei_".$year_report;
			$dbtable_before = $report."april_".$year_report;
			$dbtable_next = $report."juni_".$year_report;
			break;
			case "June":
            $dbtable = $report."juni_".$year_report;
			$dbtable_ritase = $report_ritase."juni_".$year_report;
			$dbtable_before = $report."mei_".$year_report;
			$dbtable_next = $report."juli_".$year_report;
			break;
			case "July":
            $dbtable = $report."juli_".$year_report;
			$dbtable_ritase = $report_ritase."juli_".$year_report;
			$dbtable_before = $report."juni_".$year_report;
			$dbtable_next = $report."agustus_".$year_report;
			break;
			case "August":
            $dbtable = $report."agustus_".$year_report;
			$dbtable_ritase = $report_ritase."agustus_".$year_report;
			$dbtable_before = $report."juli_".$year_report;
			$dbtable_next = $report."september_".$year_report;
			break;
			case "September":
            $dbtable = $report."september_".$year_report;
			$dbtable_ritase = $report_ritase."september_".$year_report;
			$dbtable_before = $report."agustus_".$year_report;
			$dbtable_next = $report."oktober_".$year_report;
			break;
			case "October":
            $dbtable = $report."oktober_".$year_report;
			$dbtable_ritase = $report_ritase."oktober_".$year_report;
			$dbtable_before = $report."september_".$year_report;
			$dbtable_next = $report."november_".$year_report;
			break;
			case "November":
            $dbtable = $report."november_".$year_report;
			$dbtable_ritase = $report_ritase."november_".$year_report;
			$dbtable_before = $report."oktober_".$year_report;
			$dbtable_next = $report."desember_".$year_report;
			break;
			case "December":
            $dbtable = $report."desember_".$year_report;
			$dbtable_ritase = $report_ritase."desember_".$year_report;
			$dbtable_before = $report."november_".$year_report;
			$dbtable_next = $report."januari_".$year_next;
			break;
		}
		
		//print_r($nowdate." ".$datein." ".$dbtable_ritase);exit();

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
     	$this->dbts = $this->load->database("tensor_report", true);
        $this->dbts->order_by("ritase_report_end_time", "asc");
      
		if ($location == "0") {
           $this->dbts->where_in("ritase_report_end_location", $port_all_list);
		} else if ($location == "BIB_GROUP"){
            $this->dbts->where_in("ritase_report_end_location", $port_bib_list);
        } else if ($location == "BIR_GROUP"){
			$this->dbts->where_in("ritase_report_end_location", $port_bir_list);
		} else if ($location == "TIA_GROUP"){
			$this->dbts->where_in("ritase_report_end_location", $port_tia_list);
		} else {
			$this->dbts->where_in("ritase_report_end_location", $port_all_list);
		}

        if ($shift == 1) {
            for ($s = 0; $s < count($shift1); $s++) {
                $shift1[$s] .= ":00:00";
            }
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_end_date,ritase_report_end_hour,ritase_report_start_location,ritase_report_end_location");
            $this->dbts->where("ritase_report_end_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_end_location", $port_all_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_end_hour", $shift1);
            $this->dbts->order_by("ritase_report_end_time", "asc");
            //$this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get($dbtable);
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            for ($s = 0; $s < count($shift21); $s++) {
                $shift21[$s] .= ":00:00";
            }
            for ($s = 0; $s < count($shift22); $s++) {
                $shift22[$s] .= ":00:00";
            }
			$this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_end_date,ritase_report_end_hour,ritase_report_start_location,ritase_report_end_location");
            $this->dbts->where("ritase_report_end_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_end_location", $port_all_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_end_hour", $shift21);
            $this->dbts->order_by("ritase_report_end_time", "asc");
            //$this->dbts->order_by("ritase_report_company_name", "asc");
			$result = $this->dbts->get($dbtable);
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();

            $this->dbts->distinct();

			$this->dbts->order_by("ritase_report_end_time", "asc");
			if ($location == "0") {
			   $this->dbts->where_in("ritase_report_end_location", $port_all_list);
			} else if ($location == "BIB_GROUP"){
				$this->dbts->where_in("ritase_report_end_location", $port_bib_list);
			} else if ($location == "BIR_GROUP"){
				$this->dbts->where_in("ritase_report_end_location", $port_bir_list);
			} else if ($location == "TIA_GROUP"){
				$this->dbts->where_in("ritase_report_end_location", $port_tia_list);
			} else {
				$this->dbts->where_in("ritase_report_end_location", $port_all_list);
			}

            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_end_date,ritase_report_end_hour,ritase_report_start_location,ritase_report_end_location");
            $this->dbts->where("ritase_report_end_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_end_location", $port_all_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_end_hour", $shift22);
            $this->dbts->order_by("ritase_report_end_time", "asc");
            //$this->dbts->order_by("ritase_report_company_name", "asc");
           
			if($diff_month == 2)
			{
				$result = $this->dbts->get($dbtable_next);
			}else{
				
				$result = $this->dbts->get($dbtable);
			}
				
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
            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_end_date,ritase_report_end_hour,ritase_report_start_location,ritase_report_end_location");
            $this->dbts->where("ritase_report_end_date", $date);
			//$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_end_location", $port_all_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_end_hour", $allshift1);
            $this->dbts->order_by("ritase_report_end_time", "asc");
            //$this->dbts->order_by("ritase_report_company_name", "asc");
			$result = $this->dbts->get($dbtable);
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->order_by("ritase_report_end_time", "asc");
			
			if ($location == "0") {
			   $this->dbts->where_in("ritase_report_end_location", $port_all_list);
			} else if ($location == "BIB_GROUP"){
				$this->dbts->where_in("ritase_report_end_location", $port_bib_list);
			} else if ($location == "BIR_GROUP"){
				$this->dbts->where_in("ritase_report_end_location", $port_bir_list);
			} else if ($location == "TIA_GROUP"){
				$this->dbts->where_in("ritase_report_end_location", $port_tia_list);
			} else {
				$this->dbts->where_in("ritase_report_end_location", $port_all_list);
			}

            $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_end_date,ritase_report_end_hour,ritase_report_start_location,ritase_report_end_location");
            $this->dbts->where("ritase_report_end_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_end_location", $port_all_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_end_hour", $allshift2);
            $this->dbts->order_by("ritase_report_end_time", "asc");
            //$this->dbts->order_by("ritase_report_company_name", "asc");
           
			if($diff_month == 2)
			{
				$result = $this->dbts->get($dbtable_next);
			}else{
					
				$result = $this->dbts->get($dbtable);
			}
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
                $exp = explode(":", $data[$i]['ritase_report_end_hour']);

                if (!isset($hour[$exp[0]])) {
                    $hour[$exp[0]] = 1;
                    $dhour[] = $exp[0];
                }
                if (!isset($c[$data[$i]['ritase_report_company_name']])) {

                    $c[$data[$i]['ritase_report_company_name']] = $data[$i]['ritase_report_company_name'];
                    $dcompany[] = $c[$data[$i]['ritase_report_company_name']];
                }
                if (!isset($l[$data[$i]['ritase_report_end_location']])) {

                    $l[$data[$i]['ritase_report_end_location']] = $data[$i]['ritase_report_end_location'];
                    $dlocation[] = $l[$data[$i]['ritase_report_end_location']];
                }
                // $data_fix[$exp[0]][$data[$i]['ritase_report_company_name']][$data[$i]['ritase_report_end_location']] = array("vehicle" => $data[$i]['ritase_report_vehicle_no']);


				if (!isset($data_fix[$exp[0]][$data[$i]['ritase_report_end_location']])) {
                    $data_fix[$exp[0]][$data[$i]['ritase_report_end_location']] = array();
                    $d = array(
                        "company" => $data[$i]['ritase_report_company_name'],
                        "vehicle" => $data[$i]['ritase_report_vehicle_no'],
                        "location" => $data[$i]['ritase_report_end_location'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['ritase_report_end_location']], $d);
                } else {
                    $d = array(
                        "company" => $data[$i]['ritase_report_company_name'],
                        "vehicle" => $data[$i]['ritase_report_vehicle_no'],
                        "location" => $data[$i]['ritase_report_end_location'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['ritase_report_end_location']], $d);
                }
                if (!isset($unit_location[$data[$i]['ritase_report_vehicle_no']])) {
                    $unit_location[$data[$i]['ritase_report_vehicle_no']] = $data[$i]['ritase_report_vehicle_no'];
                }
                $data[$i]['hour'] = $exp[0];
            }

			//print_r($data_fix);exit();

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
				"length_data" => count($data), //data all
                "length_company" => count($dcompany), //jumlah kontraktor
                "length_location" => count($dlocation), //jumlah lokasi
                "length_hour" => count($dhour) //jumlah jam
            ));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "data" => $data, "total" => $nr));
        }
    }

    function get_company()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;
		$company_id_list = array('0','2');

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
        }

        $this->db->where("company_flag", 0);
		$this->db->where_in("company_exca", $company_id_list);
        $qd = $this->db->get("company");
        $rd = $qd->result();

        return $rd;
    }

    function get_company_pjo()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;
		$company_id_list = array('0','2');

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
		$this->db->where_in("company_exca", $company_id_list);
        $qd = $this->db->get("company");
        $rd = $qd->result();

        return $rd;
    }

	function get_location()
    {
        $this->db->select("street_alias");
        $this->db->order_by("street_alias", "asc");
        $this->db->where_in("street_type", array(4));
		$this->db->where("street_alias <>", "PORT BBC,");
        $q = $this->db->get("street");

        return $q->result();
    }
	
	function getStatusLocReport($userid,$vdevice,$sdate,$edate)
	{
		
		if ($vdevice == 'all') {
			$ReportTypeArray = array("LOCATION ALL", "LOCATION IDLE ALL", "LOCATION OFF ALL");
		}else {
			$ReportTypeArray = array("location", "location_off", "location_idle");
		}

		$content = $this->getthisrulelocationreport($ReportTypeArray, $vdevice, $sdate, $edate);

		$data_1 = 0;
		$data_2 = 0;
		$data_3 = 0;
		$data_array_rpoerttype = array();
		$data_content = array_map('current', $content);

					if (in_array($ReportTypeArray[0], $data_content)) {
						$data_1 += 1;
					}

					if (in_array($ReportTypeArray[1], $data_content)) {
						$data_2 += 1;
					}

					if (in_array($ReportTypeArray[2], $data_content)) {
						$data_3 += 1;
					}

			$total_data_fix = ($data_1 + $data_2 + $data_3);

		
			if ($total_data_fix == 3) {
				$result = "DONE";
			}else {
				$result = "ON PROCESS";
			}
	
			return $result;
	}
	
	function getthisrulelocationreport($reportype, $vehicleid, $starttime, $endtime)
	{
		$this->dbtrip = $this->load->database("tensor_report",true);
		$this->dbtrip->order_by("autoreport_data_startdate","asc");

		$this->dbtrip->select("autoreport_type");
		if($vehicleid != "all"){
			$this->dbtrip->where("autoreport_vehicle_device", $vehicleid);
		}

		$this->dbtrip->where_in("autoreport_type", $reportype);
		$this->dbtrip->where("autoreport_data_startdate >=", $starttime);
		$this->dbtrip->where("autoreport_data_enddate <=", $endtime);
		$q = $this->dbtrip->get("autoreport_new")->result_array();
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $q;
	}
}
