<?php
include "base.php";

class Streetdata extends Base {

	function Streetdata()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		$this->load->model("gpsmodel");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	}

	function index()
	{
		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		
		$this->db = $this->load->database("master_inuyasha", TRUE);
		$this->db->order_by("street_created", "desc");
		if ($this->sess->user_type == 2)
		{
			if ($privilegecode == 1) {
				$this->db->where("street_creator", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("street_creator", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("street_creator", $user_parent);
			}else {
				$this->db->where("street_creator", $user_id);
			}
		}
		$q    = $this->db->get("street"); // $this->config->item("limit_records"), 0
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++){
			$data     = json_decode($rows[$i]->street_serialize);
			$geometry = $data->geometry->coordinates;
			$polygon  = $geometry[0];
			$points   = "";

			for($j=0; $j < count($polygon); $j++)
			{
				if ($j > 0)
				{
					$points .= ", ";
				}

				$points .= $polygon[$j][0]." ".$polygon[$j][1];
			}

			$poly = "POLYGON((".$points."))";

			$rows[$i]->street_polygon = $poly;
			$rows[$i]->updated = ($this->sess->user_type != 2) || ($rows[$i]->street_creator == $this->sess->user_id);
		}

		if ($this->sess->user_type == 2){
			$this->db->where("street_creator", $this->sess->user_id );
		}
		$total = $this->db->count_all_results("street");

		$this->params['title']          = $this->lang->line('lstreet_list');
		$this->params["total"]          = $total;
		$this->params["data"]           = $rows;
		$this->params['code_view_menu'] = "configuration";
		$this->params["privilegecode"]  = $privilegecode;

    // echo "<pre>";
    // var_dump($total);die();
    // echo "<pre>";

		$this->params["initmap"]       = $this->load->view('newdashboard/initmap', $this->params, true);
		$this->params["header"]        = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]   = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]       = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["contentstreet"] = $this->load->view('newdashboard/street/v_streetdata', $this->params, true);
			$this->params["content"]       = $this->load->view('newdashboard/street/v_mainlist', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]       = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["contentstreet"] = $this->load->view('newdashboard/street/v_streetdata', $this->params, true);
			$this->params["content"]       = $this->load->view('newdashboard/street/v_mainlist', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]       = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["contentstreet"] = $this->load->view('newdashboard/street/v_streetdata', $this->params, true);
			$this->params["content"]       = $this->load->view('newdashboard/street/v_mainlist', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]       = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["contentstreet"] = $this->load->view('newdashboard/street/v_streetdata', $this->params, true);
			$this->params["content"]       = $this->load->view('newdashboard/street/v_mainlist', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function add($id=0)
	{
		$this->db = $this->load->database("default", TRUE);
		if (isset($vdevices)){
			$this->db->where_in("vehicle_device", $vdevices);
		}

		if ($this->sess->user_type == 2){
			if ($this->sess->user_company){
				$this->db->where_in("vehicle_id", $this->vehicleids);
			}else{
				$this->db->where("user_id", $this->sess->user_id);
			}
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}elseif($this->sess->user_type == 3){
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		if ($this->config->item('vehicle_type_fixed')) {
			$this->db->where("vehicle_type",  $this->config->item('vehicle_type_fixed'));
		}

		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_no", "asc");

		$this->db->where("vehicle_status <>", 3);
		$this->db->join("vehicle", "vehicle_user_id = user_id");
		$q = $this->db->get("user");

		$rowsv = $q->result();
	    for($i=0; $i < count($rowsv); $i++){
				$arr = explode("@", $rowsv[$i]->vehicle_device);

				$rowsv[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
				$rowsv[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
			}

        //print_r($rows);
		$rows = array();
		$this->params['vehicles']       = $rowsv;
		$this->params['title']          = $this->lang->line('ladd_street');
		$this->params["id"]             = $id;
		$this->params["streets"]        = $rows;
		$this->params["zoom"]           = $this->config->item("zoom_realtime");
		$this->params['code_view_menu'] = "configuration";
    // echo "<pre>";
    // var_dump($this->params["vehicles"]);die();
    // echo "<pre>";

		$this->params["initmap"]       = $this->load->view('newdashboard/initmap', $this->params, true);
		$this->params["header"]        = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]       = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]   = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["contentstreet"] = $this->load->view('newdashboard/street/v_form', $this->params, true);
		$this->params["content"]       = $this->load->view('newdashboard/street/v_main', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function save()
	{
		$address = isset($_POST['address']) ? trim($_POST['address']) : "";

		if (strlen($address) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line("lempty_address");

			echo json_encode($callback);
			return;
		}

		$json = isset($_POST['json']) ? $_POST['json'] : "";
		$data = json_decode($json);
		$geometry = $data->geometry->coordinates;

		$insert['street_name'] = $address;
		$insert['street_line'] = '%s';
		$insert['street_creator'] = $this->sess->user_id;
		$insert['street_created'] = date("Y-m-d H:i:s");
		$insert['street_serialize'] = $json;

		
		//$mydb = $this->load->database("master", TRUE);
		$mydb = $this->load->database("master_inuyasha", TRUE);
		$sql = $mydb->insert_string("street", $insert);

		$polygon = $geometry[0];
		$points = "";

		for($j=0; $j < count($polygon); $j++)
		{
			if ($j > 0)
			{
				$points .= ", ";
			}

			$points .= $polygon[$j][0]." ".$polygon[$j][1];
		}

		$poly = "PolygonFromText('POLYGON((".$points."))')";

		$sql = str_replace("'%s'", $poly, $sql);

		$mydb->query($sql);

		$this->db->cache_delete_all();

		$callback['error'] = false;
		echo json_encode($callback);
	}

	function edit($id=0){
		$html = "";
		if($id != 0){
			$this->db = $this->load->database("master_inuyasha", TRUE);
			$this->db->where("street_id", $id);
			$q = $this->db->get("street");

			if($q->num_rows() > 0){
				$row = $q->row();

				$callback['street_id']   = $row->street_id;
				$callback['street_name'] = $row->street_name;
				// $html = $this->load->view('street/edit', $this->params, true);
			}
		}
		echo json_encode($callback);
	}

	function update(){
		$street_id = isset($_POST['street_id']) ? $_POST['street_id'] : 0;
		$street_name = isset($_POST['street_name']) ? trim($_POST['street_name']) : "";

		$data['street_name'] = $street_name;
		$this->db = $this->load->database("master_inuyasha", TRUE);
		$this->db->where('street_id', $street_id);
		if($this->db->update('street', $data)){
			$callback['error'] = false;
			$callback['message'] = "Data successfully updated";
		}else{
			$callback['error'] = true;
			$callback['message'] = "Failed update data";
		}



		echo json_encode($callback);
	}
	function remove($id)
	{
		$this->db = $this->load->database("master_inuyasha", TRUE);
		$this->db->where("street_id", $id);
		$q = $this->db->get("street");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		if ($this->sess->user_type == 2)
		{
			if ($row->street_creator != $this->sess->user_id)
			{
				redirect(base_url());
			}
		}

		//$mydb = $this->load->database("master", TRUE);
		$mydb = $this->load->database("master_inuyasha", TRUE);

		$mydb->where("street_id", $id);
		$mydb->delete("street");

		$this->db->cache_delete_all();

		redirect(base_url()."streetdata");
	}

	function docenter($id)
	{
		$this->db = $this->load->database("master_inuyasha", TRUE);
		$this->db->where("street_id", $id);
		$q = $this->db->get("street");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		$data = json_decode($row->street_serialize);
		$geometry = $data->geometry->coordinates;
		$polygon = $geometry[0];

		$callback['lng'] = $polygon[0][0];
		$callback['lat'] = $polygon[0][1];

		echo json_encode($callback);
	}

    function getstreet_location()
    {
        $coord = $this->input->post('coord');

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
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
