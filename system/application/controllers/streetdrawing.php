<?php
include "base.php";

class Streetdrawing extends Base
{

	function streetdrawing()
	{
		parent::Base();

		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');

		$segment2 = $this->uri->segment(2);

		if (in_array($segment2, array("sms"))) {
			$token = trim($this->uri->segment(5));

			if (strlen($token) == 0) {
				redirect(base_url());
				return;
			}

			$this->db->where("session_id", $token);
			$this->db->join("user", "session_user = user_id");
			$this->db->join("agent", "agent_id = user_agent", "left outer");
			$q = $this->db->get("session");

			if ($q->num_rows() == 0) {
				redirect(base_url());
				return;
			}

			$row = $q->row();

			$this->session->set_userdata($this->config->item('session_name'), serialize($row));
			return;
		}

		if (!isset($this->sess->user_type)) {
			//redirect(base_url());
		}
	}

	public function get_geofence_json($geofenceId)
	{
		// Load the model that handles geofence data (replace 'Your_model' with your actual model name)
		$this->load->model('gpsmodel');

		// Get the geofence data based on the provided ID
		$geofenceData = $this->gpsmodel->get_geofence_data($geofenceId);

		// Send the geofence data as JSON
		header('Content-Type: application/json');
		echo json_encode($geofenceData);
	}

	function index()
	{
		ini_set('display_errors', 1);

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;

		if ($this->sess->user_id == "1445") {
			$user_id = $this->sess->user_id; //tag
		} else {
			$user_id = $this->sess->user_id;
		}

		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no", "asc");

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();

		// GET ASSIGNED VEHICLE STATUS
		$this->params["datavehicle"] 	   = $result;
		$this->params['code_view_menu']  = "configuration";
		$this->params["privilegecode"] 	 = $privilegecode;

		// echo "<pre>";
		// var_dump($this->params["datavehicle"]);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_geofence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_geofence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_geofence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_geofence', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function manage($host, $name, $showlabel = "")
	{
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;
		
		$this->params['showlabel'] = $showlabel == "label";

		$this->db->where("vehicle_device", $host . '@' . $name);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
			return;
		}

		$row = $q->row();

		$this->db = $this->load->database("default", TRUE);
		//$this->db->where("street_flag", 0);
		$this->db->where("street_creator", 4408);
		$q = $this->db->get("street");
		$rows = $q->result();

		// list kendaraan
		/* $this->db = $this->load->database("default", TRUE);
		if ($this->sess->user_type == 2) {
			if ($this->sess->user_company) {
				$this->db->where_in("vehicle_id", $this->vehicleids);
			} else {
				$this->db->where("user_id", $this->sess->user_id);
			}
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		} else
		if ($this->sess->user_type == 3) {
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		if ($this->config->item('vehicle_type_fixed')) {
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_no", "asc");

		$this->db->where("vehicle_status <>", 3);
		$this->db->join("vehicle", "vehicle_user_id = user_id");
		$this->db->select("user_name, vehicle_device, vehicle_name, vehicle_no");
		$this->db->distinct();
		$q = $this->db->get("user");

		$rowvehicles = $q->result(); */
		
		$rowvehicles = array();


		$this->params['vehicles']        = $rowvehicles;
		$this->params['title']           = "Manage Street";
		$this->params["zoom"]            = $this->config->item("zoom_realtime");
		$this->params['geofence']        = $rows;
		$this->params['vehicle']         = $row;
		$this->params['code_view_menu']  = "masterdata";

		$this->params["initmap"]         = $this->load->view('newdashboard/initmap', $this->params, true);
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["contentgeofence"] = $this->load->view('newdashboard/streetdrawing/v_form', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/streetdrawing/v_main', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}



	}

	function managestreet($host, $name, $showlabel = "")
	{
		$this->params['showlabel'] = $showlabel == "label";

		$this->db->where("vehicle_device", $host . '@' . $name);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
			return;
		}

		$row = $q->row();

		$this->db = $this->load->database("default", TRUE);
		$this->db->where("geofence_vehicle", $host . '@' . $name);
		$q = $this->db->get("webtracking_geofence");

		$rows = $q->result();

		// list kendaraan
		$this->db = $this->load->database("default", TRUE);
		if ($this->sess->user_type == 2) {
			if ($this->sess->user_company) {
				$this->db->where_in("vehicle_id", $this->vehicleids);
			} else {
				$this->db->where("user_id", $this->sess->user_id);
			}
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		} else
		if ($this->sess->user_type == 3) {
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		if ($this->config->item('vehicle_type_fixed')) {
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_no", "asc");

		$this->db->where("vehicle_status <>", 3);
		$this->db->join("vehicle", "vehicle_user_id = user_id");
		$this->db->select("user_name, vehicle_device, vehicle_name, vehicle_no");
		$this->db->distinct();
		$q = $this->db->get("user");

		$rowvehicles = $q->result();


		$this->params['vehicles']        = $rowvehicles;
		$this->params['title']           = $this->lang->line('lmangeofence') . " " . $row->vehicle_name . "-" . $row->vehicle_no;
		$this->params["zoom"]            = $this->config->item("zoom_realtime");
		$this->params['geofence']        = $rows;
		$this->params['vehicle']         = $row;
		$this->params['code_view_menu']  = "configuration";

		$this->params["initmap"]         = $this->load->view('newdashboard/initmap', $this->params, true);
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["contentgeofence"] = $this->load->view('newdashboard/geofencedrawing/v_form_street', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/geofencedrawing/v_main_street', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);

		// $this->params["contentgeofence"] = $this->load->view('geofencedrawing/form', $this->params, true);
		// $this->params["content"]         = $this->load->view('geofencedrawing/main', $this->params, true);
		// $this->load->view("templatesess", $this->params);
	}

	function listallgeofence($id = 0, $vid = 0, $field = "all", $keyword = "all", $offset = 0)
	{

		$id = $this->uri->segment(3);
		$this->db->where("vehicle_user_id", $id);

		switch ($field) {
			case "vehicle":
				$this->db->where("vehicle_device LIKE '%" . $vid . "%'", null);
				break;
		}


		$q = $this->db->get("vehicle");
		$rows = $q->result();

		foreach ($rows as $v) {
			$vids[] = $v->vehicle_device;
		}

		//$this->db->where("street_flag", 0);
		$this->db->where("street_creator", 4408);
		//$this->db->where_in("geofence_vehicle", $vids);

		switch ($field) {
			case "street_name":
				$this->db->where("street_name LIKE '%" . $keyword . "%'", null);
				break;
		}

		$q_geo = $this->db->get("street", 20, $offset);
		$row_geo = $q_geo->row();
		$rows_geo = $q_geo->result();
		$total = count($rows_geo);

		$config["uri_segment"] = 4;
		$config["base_url"] = base_url() . "streetdrawing/listallstreetdrawing/" . $field . "/" . $keyword;
		$config["total_rows"] = $total;
		$config["per_page"] = 20;
		$this->pagination->initialize($config);

		if (isset($row_geo->geofence_id) && $row_geo->geofence_id != "") {
			$this->params['sourceid'] = $row_geo->geofence_id;
		} else {
			$this->params['sourceid'] = "";
		}

		$this->params['id'] = $id;
		$this->params['offset'] = $offset;
		$this->params['paging'] = $this->pagination->create_links();
		$this->params['vehicle'] = $rows;
		$this->params['data_geofence'] = $rows_geo;
		$this->params['total_list'] = $total;
		$this->params['navigation'] = $this->load->view('navigation', $this->params, true);
		$this->params['content'] = $this->load->view('streetdrawing/listallgeofence', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}


	function deleteallbyid()
	{
		if (!isset($_POST['geoid'])) {
			$json['message'] = "NO Street Selected";
			$json['error'] = true;

			echo json_encode($json);
			return;
		}

		$i = 0;
		$geoid = $_POST['geoid'];
		$mydb = $this->load->database("default", TRUE);
		//$mydb->where('street_flag', 0);

		foreach ($geoid as $x[]) {
			$gid[] = $x[$i];
			$i++;
		}

		$mydb->where_in('street_id', $gid);
		$mydb->delete("street");

		$this->db->cache_delete_all();
		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lgeofence_deleted");

		echo json_encode($callback);
		return;
	}

	function geocode2()
	{
		$coord = $this->input->post('lokasi');

		if (!$coord) return;

		$coord1 = explode(",", $coord);
		$lat = $coord1[0];
		$lng = $coord1[1];

		if (!$lat) return;
		if (!$lng) return;

		$callback['lat'] = $lat;
		$callback['lng'] = $lng;

		echo json_encode($callback);
	}

	function removebyid_test($id)
	{
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$this->db->where("geofence_id", $id);
		$this->db->join("geofence", "geofence_vehicle = vehicle_device");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			$callback['error'] = true;
			$callback['message'] = "Invalid vehicle";

			echo json_encode($callback);
			return;
		}


		$mydb = $this->load->database("master", TRUE);

		$mydb->where("geofence_id", $id);
		$mydb->delete("geofence");

		$this->db->cache_delete_all();

		$callback['error'] = false;
		$callback['message'] = $this->lang->line("lgeofence_deleted");

		echo json_encode($callback);
	}

	function remove_test($host, $name)
	{
		$this->db->where("vehicle_device", $host . '@' . $name);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			$callback['error'] = true;
			$callback['message'] = "Invalid vehicle";

			echo json_encode($callback);
			return;
		}

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("geofence_status", 1);
		$mydb->where("geofence_vehicle", $host . '@' . $name);
		$mydb->delete("geofence");

		$this->db->cache_delete_all();

		$callback['error'] = false;
		echo json_encode($callback);
	}

	function getlist()
	{
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : "";
		if (!$vehicle) {
			$callback['error'] = true;
			echo json_encode($callback);
			return;
		}

		$this->dbts->where("street_creator", 4408);
		//$this->dbts->where("street_flag", 0);
		$q = $this->dbts->get("street");

		$rows = $q->result();
		foreach ($rows as $row_geo) {
			$data_geo[] = $row_geo->street_serialize;
			$data_geo_label[] = $row_geo->street_name;
		}

		$callback['error'] = false;
		$callback['geofence'] = $data_geo;
		$callback['geofence_label'] = $data_geo_label;
		//print_r($callback);
		echo json_encode($callback);
	}

	function convertArrayKeysToUtf8(array $array)
	{
		$convertedArray = array();
		foreach ($array as $key => $value) {
			if (!mb_check_encoding($key, 'UTF-8')) $key = utf8_encode($key);
			if (is_array($value)) $value = $this->convertArrayKeysToUtf8($value);
			$convertedArray[$key] = $value;
		}
		return $convertedArray;
	}

	function label()
	{

		$devid = isset($_POST['deviceid']) ? $_POST['deviceid'] : "";
		if (strlen($devid) == 0) {
			$callback['response'] = array(
				'status' => 'error',
				'message' => 'Access denied'
			);
			$callback['title'] = "Street Label";
			echo json_encode($callback);
			return;
		}
		
		$this->dbmaster = $this->load->database("default", TRUE);
		
		$this->dbmaster->select("street_name, street_serialize");
		//$this->dbmaster->where("street_flag", 0);
		$this->dbmaster->where("street_creator", 4408);
		$this->dbmaster->order_by("street_created", "desc");
		$q = $this->dbmaster->get("street");
		
		$data = $q->result();

		$koordinat_array = array();

		foreach ($data as $row) {
			$jsonDecode = json_decode($row->street_serialize, true);
			$outputArray = array();

			
			$stringJson = array();

			foreach ($jsonDecode["geometry"]["coordinates"][0] as $key => $value) {
				
					$in0 = $value[0];
					$in1 = $value[1];
					$stringJson[] = array("${in0},${in1}");
				
			}
	
			foreach ($stringJson as $item) {
				$outputArray[] = $item[0];
				}

			$coordinatesArray = array();
			$geofence_name = $row->street_name;

		
		foreach ($outputArray as $key => $value) {
			
				$coordinates = explode(',', $value);
				$geofence_name = $row->street_name;

				$lng = $coordinates[0];
				$lat = $coordinates[1];

				$coordinatesArray[] = array(
					'street_name' => ($geofence_name),
					'lat' => floatval($lat),
					'lng' => floatval($lng)
				);
			
        }

    	  $koordinat_array[] = $coordinatesArray;
        }
				
		$callback['koordinat_array_all'] = $koordinat_array;  //koordinat all //
		
		echo json_encode($callback);
		
	}
	
	function save()
	{
		$this->db = $this->load->database("default", TRUE);
		$jsons = isset($_POST['json']) ? $_POST['json'] : "";
		$listPolygonName = $this->input->post('geofenceName');
		$listCoordinates = $this->input->post('listCreatePolygon');

		foreach ($listPolygonName as $i => $value) {
			// Membagi string menjadi pasangan koordinat
			$coordinatePairs = explode(" ", $listCoordinates[$i]);


			// Menukar posisi elemen pertama dan elemen kedua di setiap pasangan
			$modifiedCoordinates = array_map(function ($pair) {
				list($lat, $lng) = explode(",", $pair);
				return "$lng,$lat";
			}, $coordinatePairs);

			// Menggabungkan kembali hasilnya menjadi string
			$outputString = implode(" ", $modifiedCoordinates);

			// Menyiapkan data tambahan
			$geofence_vehicle = "869622050211011@VT200L";

			// Query SQL untuk memasukkan data
			$sql = "INSERT INTO `webtracking_street` (`street_name`, `street_creator`, `street_flag`, `street_created`, `street_line`, `street_serialize`) VALUES (?,?,?,?,GEOMFROMTEXT(?),?)";

			// Assuming $jsons is the GeoJSON data you provided

			foreach ($modifiedCoordinates as $coordinate) {
				list($lon, $lat) = explode(',', $coordinate);
				$polygonCoordinates[] = "$lon $lat";
			}

			// Pastikan koordinat pertama ada di akhir untuk membentuk poligon yang tertutup
			$polygonCoordinates[] = $polygonCoordinates[0];

			$convertedPolygon = 'POLYGON((' . implode(', ', $polygonCoordinates) . '))';

			// GeoJSON awal
			$geojson = '{"type":"Feature","properties":{},"geometry":{"type":"Polygon","coordinates":[[[117.43493730793,2.1939218338494],[117.43521625766,2.1938226648584],[117.43514115581,2.1934661924852],[117.43484074841,2.1935412393077],[117.43493730793,2.1939218338494]]]},"crs":{"type":"name","properties":{"name":"EPSG:900913"}}}';

			// Decode GeoJSON menjadi array
			$geojsonArray = json_decode($geojson, true);

			// Ganti koordinat di bagian "coordinates"
			$geojsonArray['geometry']['coordinates'] = array(array());

			foreach ($modifiedCoordinates as $coordinate) {
				list($lng, $lat) = explode(',', $coordinate);
				$geojsonArray['geometry']['coordinates'][0][] = array((float) $lng, (float) $lat);
			}

			// Encode kembali ke format GeoJSON
			$newGeojson = json_encode($geojsonArray);

			$insert = array(
				//$outputString,
				$listPolygonName[$i],
				$this->sess->user_id,
				0,
				date("Y-m-d H:i:s", mktime() - 7 * 3600),
				//$geofence_vehicle,
				$convertedPolygon,
				$newGeojson
			);



			$query = $this->db->query($sql, $insert);

		}

		if (!$query) {
			$callback['error'] = false;
			$callback['message'] = "Data gagal saved";
			echo json_encode($callback);
		} else {
			$callback['error'] = false;
			$callback['message'] = "Data saved successfully!";
			echo json_encode($callback);
		}


	}


	function get($id)
	{
		$this->db = $this->load->database("default", TRUE);
		$this->db->where("street_id", $id);
		$q = $this->db->get("street");

		if ($q->num_rows() == 0) {
			return;
		}

		$row = $q->row();

		$geos = explode(" ", $row->street_serialize);

		$callback['point'] = explode(",", $geos[0]);;
		echo json_encode($callback);
		return;
	}

	function savelabel()
	{
		$this->db = $this->load->database("default", TRUE);
		$ids = isset($_POST['ids']) ? $_POST['ids'] : array();
		$names = isset($_POST['names']) ? $_POST['names'] : array();

		for ($i = 0; $i < count($ids); $i++) {
			unset($update);

			$update['street_name'] = $names[$i];

			$this->db->where("street_id", $ids[$i]);
			$this->db->update("street", $update);
		}

		$callback['error'] = false;
		echo json_encode($callback);
		return;
	}

	// public function saveData()
	// {
	// 	try {
	// 		$json_data = $this->input->raw_input_stream;
	// 		$data = json_decode($json_data, true);

	// 		if (!isset($data['polygons']) || !is_array($data['polygons'])) {
	// 			$this->output->set_status_header(400);
	// 			echo json_encode(['error' => 'Invalid data format']);
	// 			return;
	// 		}

	// 		$polygons = $data['polygons'];

	// 		$this->load->model('GeofenceModel');

	// 		foreach ($polygons as $polygon) {
	// 			$this->GeofenceModel->insert(array(
	// 				'polygon_data' => $polygon,
	// 			));
	// 		}

	// 		echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
	// 	} catch (Exception $e) {
	// 		$this->output->set_status_header(500);
	// 		echo json_encode(['error' => 'An error occurred while processing the data']);
	// 	}
	// }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
