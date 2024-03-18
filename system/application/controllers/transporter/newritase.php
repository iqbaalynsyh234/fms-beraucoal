<?php
include "base.php";

class Newritase extends Base {

	function Newritase()
	{
		parent::Base();
		$this->load->model("gpsmodel");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	}

	function index($field ='all', $keyword='all', $offset=0)
	{
		if (!isset($this->sess->user_company))
		{
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database("transporter",true);

		switch($field){
		case "ritase_geofence_name":
			$this->dbtransporter->where("ritase_geofence_name LIKE '%".$keyword."%'", null);
		break;
		}

		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		//$this->dbtransporter->where("ritase_status", 1);
		$q_ritase = $this->dbtransporter->get("ritase",10, $offset);
		$rows_ritase = $q_ritase->result();
		$total = count($rows_ritase);

		switch($field){
		case "ritase_geofence_name":
			$this->dbtransporter->where("ritase_geofence_name LIKE '%".$keyword."%'", null);
		break;
		}

		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		//$this->dbtransporter->where("ritase_status", 1);
		$qtotal = $this->dbtransporter->get("ritase");
		$rowstotal = $qtotal->result();
		$total = count($rowstotal);

		$config['uri_segment'] = 6;
		$config['base_url'] = base_url()."transporter/ritase/index/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->pagination->initialize($config);
		$this->params["paging"] = $this->pagination->create_links();
		/* foreach($rows_ritase as $row_ritase)
		{
			$ritase_geofence[] = $row_ritase->ritase_geofence_id;
		}

		$this->db->where("geofence_status", "1");
		$this->db->where_in("geofence_id", $ritase_geofence);
		$q = $this->db->get("geofence");
		$rows = $q->result(); */
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params['data'] = $rows_ritase;
		$this->params['total'] = $total;
		$this->params["content"] = $this->load->view('ritase/result', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

  function powerblock_ritase()
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
		$this->params["content"]       = $this->load->view('ritase/new_mn_ritase_report', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function add()
	{
		if (!isset($this->sess->user_company))
		{
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		$this->dbtransporter->where("ritase_status", 1);
		$q_ritase = $this->dbtransporter->get("ritase");
		$rows_ritase = $q_ritase->result();

		if (count($rows_ritase) > 0)
		{
			foreach($rows_ritase as $row_ritase)
			{
				$ritase_geofence_name[] = $row_ritase->ritase_geofence_name;
			}
		}

		$this->db->order_by("geofence_name", "asc");
		$this->db->where("geofence_status", "1");
		$this->db->where("geofence_name !=", "");
		$this->db->where("geofence_user", $this->sess->user_id);
		if (count($rows_ritase) > 0)
		{
			$this->db->where_not_in("geofence_name", $ritase_geofence_name);
		}
		$q = $this->db->get("geofence");
		$rows = $q->result();

		$this->params['data'] = $rows;
		$this->params["content"] = $this->load->view('ritase/add', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function save()
	{
		$this->dbtransporter = $this->load->database("transporter", true);

		$company = $this->sess->user_company;
		$name = isset($_POST['ritase_name']) ? trim($_POST['ritase_name']) : "";
		unset($data);

		$data['ritase_company'] = $company;
		$data['ritase_geofence_name'] = $name;
		$data['ritase_status'] = 1;

		$this->dbtransporter->insert("ritase", $data);

		$callback['error'] = false;
		$callback['message'] = "Add Ritase Seccess";
		$callback['redirect'] = base_url()."transporter/ritase";

		echo json_encode($callback);
		return;
	}

	function info_delete()
	{
		$id = $this->input->post("id");
		if ($id)
		{
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->where("ritase_id", $id);
			$this->dbtransporter->limit(1);
			$q = $this->dbtransporter->get("ritase");
			$row = $q->row();

			$params["row"] = $row;
			$html = $this->load->view("ritase/info_delete", $params, true);
			$callback["error"] = false;
			$callback["html"] = $html;

			echo json_encode($callback);

		}
	}


	function remove()
	{
		$id = $this->input->post("id_ritase");

		if ($id)
		{
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->where("ritase_id", $id);
			$this->dbtransporter->delete("ritase");
			$this->dbtransporter->cache_delete_all();
			redirect(base_url()."transporter/ritase");

		}
	}

	function menu_ritase_report()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

        $this->db->order_by("vehicle_name", "asc");
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

		$this->db->cache_delete_all();

		$this->dbtransporter = $this->load->database("transporter", true);

		$this->dbtransporter->order_by("ritase_geofence_name", "asc");
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		$this->dbtransporter->where("ritase_status", "1");

		$q_ritase = $this->dbtransporter->get("ritase");
		$row_ritase = $q_ritase->result();

		$this->dbtransporter->cache_delete_all();

		$this->params["vehicle"] = $row_vehicle;
		$this->params["ritase"] = $row_ritase;
		$this->params["content"] = $this->load->view('ritase/mn_ritase_report', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function gotohistorymaps(){
		$this->load->view("ritase/historymaps");
	}

	function new_ritase_report(){
		$finaldata      = array();
		$vehicle_device = $this->input->post("vehicle");
		$shour          = $this->input->post("shour");
		$ehour          = $this->input->post("ehour");

		$startdate      = $this->input->post("date");
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
		// var_dump($size);die();
		// echo "<pre>";
		$html              = $this->load->view("ritase/new_ritase_report", $params, true);

		$callback["error"] = false;
		$callback["html"]  = $html;

		echo json_encode($callback);
	}

	function ritase_report()
	{
		$vehicle_device = $this->input->post("vehicle");

		$startdate = $this->input->post("date");
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));

		$enddate = $this->input->post("enddate");
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$ritase          = $this->input->post("ritase");
		$exRitase        = explode(",", $ritase);
		$ritase_id       = $ritase[0];
		$ritase_name     = $exRitase[1];

		$ritaseout       = $this->input->post("ritaseout");
		$exRitaseout     = explode(",", $ritaseout);
		$ritase_id_out   = $exRitaseout[0];
		$ritase_name_out = $exRitaseout[1];
		$user_id 				 = $this->sess->user_id;
		// $this->db->where("geoalert_vehicle", $vehicle_device);
		// $this->db->where("geoalert_time >=", $sdate);
    // $this->db->where("geoalert_time <=", $edate);
		// $this->db->join("geofence", "geofence_id = geoalert_geofence");
		// $this->db->where("geofence_name", $ritase_name);
		// $this->db->order_by("geoalert_time", "asc");

			$sql1 = "SELECT * from webtracking_geofence_alert
				join webtracking_geofence on webtracking_geofence_alert.geoalert_geofence = webtracking_geofence.geofence_id
				and webtracking_geofence_alert.geoalert_direction = '1'
				and webtracking_geofence.geofence_name = '$ritase_name' and webtracking_geofence_alert.geoalert_vehicle = '$vehicle_device'
				and webtracking_geofence_alert.geoalert_time >= '$sdate' and webtracking_geofence_alert.geoalert_time <= '$edate'
				and webtracking_geofence.geofence_user = '$user_id'";

		$sql2 = "SELECT * from webtracking_geofence_alert
            join webtracking_geofence on webtracking_geofence_alert.geoalert_geofence = webtracking_geofence.geofence_id
            and webtracking_geofence_alert.geoalert_direction = '2'
						and webtracking_geofence.geofence_name = '$ritase_name_out' and webtracking_geofence_alert.geoalert_vehicle = '$vehicle_device'
						and webtracking_geofence_alert.geoalert_time >= '$sdate' and webtracking_geofence_alert.geoalert_time <= '$edate'
						and webtracking_geofence.geofence_user = '$user_id'";

		$q     = $this->db->query($sql1);
		$q2    = $this->db->query($sql2);

		$rows1 = $q->result();
		$rows2 = $q2->result();

		$size  = sizeof($rows1);
		$size2 = sizeof($rows2);


		$this->db->cache_delete_all();

		for ($i=0;$i<count($rows1);$i++)
		{
			$rows1[$i]->geoalert_time_t = dbmaketime($rows1[$i]->geoalert_time);
		}

		for ($x=0;$x<count($rows2);$x++)
		{
			$rows2[$x]->geoalert_time_t2 = dbmaketime($rows2[$x]->geoalert_time);
		}

		$params["data1"]      = $rows1;
		$params["data2"]      = $rows2;
		$params["size"]       = $size;
		$params["size2"]      = $size2;
		$params["start_date"] = $startdate;
		$params["end_date"]   = $enddate;

		$html                 = $this->load->view("ritase/ritase_report", $params, true);

		$callback["error"]    = false;
		$callback["html"]     = $html;

		echo json_encode($callback);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
