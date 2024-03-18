<?php
include "base.php";

class Pbi_ritase extends Base {
	var $otherdb;

	function Pbi_ritase()
	{
		parent::Base();
    $this->load->library('email');
    $this->load->helper('common_helper');
    $this->load->helper('common');
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
	}

  function index()
  {
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

    // $this->db->order_by("vehicle_name", "asc");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->where("vehicle_status <>", 3);

    if ($this->sess->user_type == 2)
    {
        $this->db->where("vehicle_user_id", $this->sess->user_id);
        $this->db->or_where("vehicle_company", $this->sess->user_company);
        $this->db->where("vehicle_active_date2 >=", date("Ymd"));
    }

    $q_vehicle = $this->db->get("vehicle");
    $row_vehicle = $q_vehicle->result();
        //print_r($row_vehicle);exit;

    // $this->db->cache_delete_all();

    $this->db->select("geofence_vehicle, geofence_name, geofence_type");
    $this->db->where("geofence_status", "1");
    $this->db->where("geofence_user", $this->sess->user_id);
    $this->db->group_by("geofence_name");
    $this->db->order_by("geofence_name", "asc");

    $q_geofence = $this->db->get("webtracking_geofence");
    $row_geofence = $q_geofence->result();
    // echo "<pre>";
    // var_dump($row_geofence);die();
    // echo "<pre>";

    $this->db->cache_delete_all();

    $this->params["vehicle"]       = $row_vehicle;
    $this->params["geofence_name"] = $row_geofence;
    $this->params["user_company"]  = $this->sess->user_company;
  	$this->params['code_view_menu'] = "report";

    $this->params["header"]        = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]       = $this->load->view('powerblock/dashboard/sidebar', $this->params, true);
    $this->params["content"]       = $this->load->view('powerblock/dashboard/report/v_home_ritasereport', $this->params, true);
    $this->load->view("dashboard/template_dashboard_report", $this->params);
  }

  function new_ritase_report(){
		$finaldata      = array();
		$vehicle_device = $this->input->post("vehicle");
		$shour          = $this->input->post("shour");
		$ehour          = $this->input->post("ehour");

		$startdate      = $this->input->post("startdate");
		$sdate          = date("Y-m-d H:i:s", strtotime($startdate." ".$shour.":00"));

		$enddate        = $this->input->post("enddate");
		$edate          = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour.":59"));

		$ritase         = $this->input->post("ritase");
		$ritaseout      = $this->input->post("ritaseout");
		$user_id        = $this->sess->user_id;

		$month          = date("F", strtotime($startdate));
		$year           = date("Y", strtotime($startdate));
		$report         = "operasional_";
		$rows           = "";
		$rows3          = "";

    // echo "<pre>";
		// var_dump($sdate.'-'.$edate.'-'.$ritase.'-'.$ritaseout.'-'.$user_id.'-'.$vehicle_device);die();
		// echo "<pre>";

		switch ($month)
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


		// print_r($vehicle_device."-".$ritase."-".$ritaseout."-".$sdate."-".$edate."-".$shour."-".$ehour."-".$user_id);exit();
		// print_r($dbtable);exit();

		// REGULER
		$this->powerblock_report = $this->load->database("powerblock_report", true);
		$this->powerblock_report->select("trip_mileage_vehicle_id, trip_mileage_vehicle_id, trip_mileage_vehicle_no, trip_mileage_vehicle_name, trip_mileage_geofence_start, trip_mileage_geofence_end, trip_mileage_start_time, trip_mileage_end_time,
						trip_mileage_duration, trip_mileage_trip_mileage, trip_mileage_engine");
		$this->powerblock_report->where("trip_mileage_vehicle_id", $vehicle_device);
		$this->powerblock_report->where("trip_mileage_geofence_start",$ritase);
		$this->powerblock_report->where("trip_mileage_geofence_end", $ritaseout);
		$this->powerblock_report->where("trip_mileage_start_time >=", $sdate);
		$this->powerblock_report->where("trip_mileage_end_time <=", $edate);
		$this->powerblock_report->order_by("trip_mileage_start_time", "asc");
		$q          = $this->powerblock_report->get($dbtable);
		$row        = $q->result();
		$size = sizeof($row);

		$params['data'] = "";
			if (sizeof($row) > 0) {
				for ($i=0; $i < $size; $i++) {
					array_push($finaldata, array(
						"trip_mileage_duration" 			=> $row[$i]->trip_mileage_duration,
						"trip_mileage_geofence_start" => $row[$i]->trip_mileage_geofence_start,
						"trip_mileage_geofence_end"   => $row[$i]->trip_mileage_geofence_end,
						"trip_mileage_start_time"     => $row[$i]->trip_mileage_start_time,
						"trip_mileage_end_time"       => $row[$i]->trip_mileage_end_time,
						"trip_mileage_trip_mileage"   => $row[$i]->trip_mileage_trip_mileage,
						"trip_mileage_trip_mileage2"  => $row[$i]->trip_mileage_trip_mileage,
						"trip_mileage_engine"         => $row[$i]->trip_mileage_engine,
						"kondisi1"										=> "1"
					));
				}
				$params['data'] = $finaldata;
			}else {
				$this->powerblock_report = $this->load->database("powerblock_report", true);
				$this->powerblock_report->select("trip_mileage_duration, trip_mileage_geofence_start, trip_mileage_start_time, trip_mileage_trip_mileage, trip_mileage_engine");
				$this->powerblock_report->where("trip_mileage_vehicle_id", $vehicle_device);
				$this->powerblock_report->where("trip_mileage_geofence_start", $ritase);
				$this->powerblock_report->where("trip_mileage_geofence_end", "0");
				$this->powerblock_report->where("trip_mileage_start_time >=", $sdate);
				$this->powerblock_report->where("trip_mileage_end_time <=", $edate);
				$this->powerblock_report->order_by("trip_mileage_start_time", "asc");
				$this->powerblock_report->limit("1");
				$q2         = $this->powerblock_report->get($dbtable);
				$rows       = $q2->result();

				//CARI DATA RITASE YG SESUAI DAN AMBIL ROW PERTAMANYA SAJA
				$this->powerblock_report = $this->load->database("powerblock_report", true);
				$this->powerblock_report->select("trip_mileage_duration, trip_mileage_geofence_end, trip_mileage_end_time, trip_mileage_trip_mileage");
				$this->powerblock_report->where("trip_mileage_vehicle_id", $vehicle_device);
				$this->powerblock_report->where("trip_mileage_geofence_start", "0");
				$this->powerblock_report->where("trip_mileage_geofence_end", $ritaseout);
				$this->powerblock_report->where("trip_mileage_start_time >=", $sdate);
				$this->powerblock_report->where("trip_mileage_end_time <=", $edate);
				$this->powerblock_report->limit("1");
				$q3         = $this->powerblock_report->get($dbtable);
				$rows3       = $q3->result();
				$kondisi = "sikon 2";
					if (sizeof($rows) !=0 && sizeof($rows3) !=0) {
						array_push($finaldata, array(
							"trip_mileage_duration" 			=> $rows[0]->trip_mileage_duration,
							"trip_mileage_geofence_start" => $rows[0]->trip_mileage_geofence_start,
							"trip_mileage_geofence_end"   => $rows3[0]->trip_mileage_geofence_end,
							"trip_mileage_start_time"     => $rows[0]->trip_mileage_start_time,
							"trip_mileage_end_time"       => $rows3[0]->trip_mileage_end_time,
							"trip_mileage_trip_mileage"   => $rows[0]->trip_mileage_trip_mileage,
							"trip_mileage_trip_mileage2"  => $rows3[0]->trip_mileage_trip_mileage,
							"trip_mileage_engine"         => $rows[0]->trip_mileage_engine,
							"kondisi1"										=> "2"
						));
					}else {
						 $finaldata = "empty";
					}
			}

			$params['data'] = $finaldata;
		// echo "<pre>";
		// var_dump($params['data']);die();
		// echo "<pre>";
		// $html              = $this->load->view("ritase/new_ritase_report", $params, true);
    $html       = $this->load->view('powerblock/dashboard/report/v_result_ritasereport', $params, true);
		$callback["error"] = false;
		$callback["html"]  = $html;
		echo json_encode($callback);
	}

}
