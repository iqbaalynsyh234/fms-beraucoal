<?php
include "base.php";

class Snapreport extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->model("dashboardmodel");
	}

	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vsnap_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$vehicle_ex = explode("@", $vehicle);
		$imei = $vehicle_ex[0];
		$table = "picture";
		
		$error = "";
		if ($vehicle == "" || $vehicle == 0)
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";	
		}
		if($startdate == "")
		{
			$error .= "- Please Select Start Date! \n";	
		}
		if($enddate == "")
		{
			$error .= "- Please Select End Date! \n";	
		}
		
		if($startdate != ""){
			
			$startdate_ex = strtotime($startdate);
			$enddate_ex = strtotime($enddate);
			$datediff = $enddate_ex - $startdate_ex;
			$on_calculation = $datediff / (60 * 60 * 24);
		}
		
		$limit_app = $this->config->item("limit_snap_report");
		if($on_calculation > $limit_app)
		{
			$error .= "- Batas Report yang dapat dipilih selama ".$limit_app." hari!"."\n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		
			$this->db = $this->load->database($this->sess->user_dblive, TRUE);
			$this->db->order_by("picture_datetime","asc");
			$this->db->where("picture_imei", $imei);
			$this->db->where("picture_datetime >=", $sdate);
			$this->db->where("picture_datetime <=", $edate);
			$this->db->where("picture_flag", 0);
			$this->db->from($table);
			$q = $this->db->get();
			$rows = $q->result();

		$params['data'] = $rows;
		$params['vehicle'] = $rowvehicle;
		$html = $this->load->view("dashboard/report/vsnap_result", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;

	}

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user) {

		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }

		}else
        {
            return false;
        }

	}

	function getGeofence_location_other($longitude, $latitude, $vehicle_user) {

		$this->db = $this->load->database("default", true);
		$lng = $longitude;
		$lat = $latitude;

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }

		}else
        {
            return false;
        }

	}

	function getPosition($longitude, $ew, $latitude, $ns){
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);

		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");

		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);

		return $georeverse;
	}

	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		return $georeverse;
	}

	function get_vehicle_by_company($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		if($this->sess->user_group > 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>",3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if($qd->num_rows() > 0){
			$options = "<option value='0' selected='selected' >--Select Vehicle--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_device . "'>". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
			}

			echo $options;
			return;
		}
	}


}
