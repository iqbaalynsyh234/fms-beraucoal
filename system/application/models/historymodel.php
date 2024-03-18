<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Historymodel extends Model
{

	function Historymodel()
	{
		parent::Model();
	}

	function all($table, $name, $host, $t1, $t2, $limit = 0, $offset = 0, $order = "desc")
	{

		// echo "<pre>";
		// var_dump(date("Y-m-d H:i:s", $t2));die();
		// echo "<pre>";

		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		//ITB, DAMAS
		$appfan = $this->config->item("fan_app");
		if (($appfan && $appfan == 1) || $this->sess->user_id == "1554" || $this->sess->user_id == "1032") {

			$this->db->select("gps_id, gps_time, gps_msg_ori, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status, gps_latitude_real, gps_longitude_real, gps_course, gps_mvd");
		} else {

			$this->db->select("gps_id, gps_time, gps_msg_ori, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status, gps_latitude_real, gps_longitude_real, gps_course, gps_mvd");
		}

		$this->db->order_by("gps_time", $order);
		$this->db->where("gps_name", $name);
		$this->db->where("gps_host", $host);

		$this->db->where("gps_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("gps_time <=", date("Y-m-d H:i:s", $t2));
		$this->db->group_by("gps_time");

		// $this->db->where("gps_time >=", $t1);
		// $this->db->where("gps_time <=", $t2);

		if ($limit == -1) {
			return $this->db->count_all_results($table);
		}

		$q = $this->db->get($table);

		return $q->result();
	}

	function khusus638($table, $name, $host, $t1, $t2, $limit = 0, $offset = 0, $order = "desc")
	{

		// echo "<pre>";
		// var_dump(date("Y-m-d H:i:s", $t2));die();
		// echo "<pre>";

		$this->db638 			 = $this->load->database("GPS_VT200_TIB_17008", TRUE);
		if ($limit > 0) {
			$this->db638->limit($limit, $offset);
		}

		//ITB, DAMAS
		$appfan = $this->config->item("fan_app");
		if (($appfan && $appfan == 1) || $this->sess->user_id == "1554" || $this->sess->user_id == "1032") {

			$this->db638->select("gps_id, gps_time, gps_msg_ori, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status, gps_latitude_real, gps_longitude_real, gps_course, gps_mvd");
		} else {

			$this->db638->select("gps_id, gps_time, gps_msg_ori, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status, gps_latitude_real, gps_longitude_real, gps_course, gps_mvd");
		}

		$this->db638->order_by("gps_time", $order);
		$this->db638->where("gps_name", $name);
		$this->db638->where("gps_host", $host);

		$this->db638->where("gps_time >=", date("Y-m-d H:i:s", $t1));
		$this->db638->where("gps_time <=", date("Y-m-d H:i:s", $t2));
		$this->db638->group_by("gps_time");

		// $this->db638->where("gps_time >=", $t1);
		// $this->db638->where("gps_time <=", $t2);

		if ($limit == -1) {
			return $this->db638->count_all_results($table);
		}

		$q = $this->db638->get($table);

		return $q->result();
	}

	function alltest($table = '', $name = '', $host = '', $t1 = '', $t2 = '')
	{

		// $this->db->limit(200, 0);
		// $this->db->select("gps_time,gps_speed,gps_latitude_real, gps_longitude_real");
		// $this->db->select("gps_id, gps_time, gps_msg_ori, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status, gps_latitude_real, gps_longitude_real, gps_course");
		$this->db->select("*");


		$this->db->order_by("gps_time", "asc");
		$this->db->where("gps_name", $name);
		$this->db->where("gps_host", $host);
		$this->db->where("gps_time >=", $t1);
		$this->db->where("gps_time <=", $t2);



		$q = $this->db->get($table);
		// $q = $this->db->query("SELECT * FROM '869926046501785@vt200_gps' where gps_name = '869926046501785' and gps_time >= '2022-01-19 10:00:00' and gps_time <= '2022-01-19 11:00:00'");

		return $q->result();
	}

	function allinfotest($table, $name, $host, $t1, $t2)
	{

		// $this->db->limit(200, 0);
		$this->db->order_by("gps_info_time", "asc");
		// $this->db->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
		$this->db->select("*");
		$this->db->where("gps_info_device", $name . "@" . $host);
		$this->db->where("gps_info_time >=", $t1);
		$this->db->where("gps_info_time <=", $t2);

		$q = $this->db->get($table);

		return $q->result();
		// $q = array('table' => $table, 'name' => $name, 'host' => $host, 'time1' => $t1, 'time2' => $t2);
		// return $q;
	}

	function allinfo($table, $name, $host, $t1, $t2, $limit = 0, $offset = 0, $wheres = array(), $order = "desc")
	{
		// echo "<pre>";
		// var_dump(date("Y-m-d H:i:s", $t1));die();
		// echo "<pre>";

		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$this->db->order_by("gps_info_time", $order);
		$this->db->group_by("gps_info_time");
		$this->db->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
		$this->db->where("gps_info_device", $name . "@" . $host);
		$this->db->where("gps_info_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $t2));

		// $this->db->where("gps_info_time >=", $t1);
		// $this->db->where("gps_info_time <=", $t2);

		if (is_array($wheres) && count($wheres)) {
			foreach ($wheres as $where) {
				$this->db->where($where, null);
			}
		}

		if ($limit == -1) {
			return $this->db->count_all_results($table);
		}

		$q = $this->db->get($table);

		return $q->result();
	}

	function allinfo638($table, $name, $host, $t1, $t2, $limit = 0, $offset = 0, $wheres = array(), $order = "desc")
	{
		// echo "<pre>";
		// var_dump(date("Y-m-d H:i:s", $t1));die();
		// echo "<pre>";
		$this->db638 			 = $this->load->database("GPS_VT200_TIB_17008", TRUE);
		if ($limit > 0) {
			$this->db638->limit($limit, $offset);
		}

		$this->db638->order_by("gps_info_time", $order);
		$this->db638->group_by("gps_info_time");
		$this->db638->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
		$this->db638->where("gps_info_device", $name . "@" . $host);
		$this->db638->where("gps_info_time >=", date("Y-m-d H:i:s", $t1));
		$this->db638->where("gps_info_time <=", date("Y-m-d H:i:s", $t2));

		// $this->db638->where("gps_info_time >=", $t1);
		// $this->db638->where("gps_info_time <=", $t2);

		if (is_array($wheres) && count($wheres)) {
			foreach ($wheres as $where) {
				$this->db638->where($where, null);
			}
		}

		if ($limit == -1) {
			return $this->db638->count_all_results($table);
		}

		$q = $this->db638->get($table);

		return $q->result();
	}

	function overspeed($table, $name, $host, $maxspeed, $t1, $t2, $limit = 0, $offset = 0)
	{
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$this->db->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns");
		$this->db->order_by("gps_time", "DESC");
		$this->db->where("gps_name", $name);
		$this->db->where("gps_host", $host);
		$this->db->where("gps_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("gps_time <=", date("Y-m-d H:i:s", $t2));
		$this->db->where("gps_speed >=", $maxspeed / 1.852);

		if ($limit == -1) {
			return $this->db->count_all_results($table);
		}

		$q = $this->db->get($table);

		return $q->result();
	}

	function geofence($table, $name, $host, $geostatus, $t1, $t2, $limit = 0, $offset = 0)
	{
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		if ($geostatus) {
			$this->db->where("geoalert_direction", $_POST['geostatus']);
		}

		$this->db->order_by("geoalert_time", "DESC");
		$this->db->where("geoalert_vehicle", $name . '@' . $host);
		$this->db->where("geoalert_time >=", date("Y-m-d H:i:s", $t1));
		$this->db->where("geoalert_time <=", date("Y-m-d H:i:s", $t2));
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "left outer");

		if ($limit == -1) {
			return $this->db->count_all_results("geofence_alert");
		}

		$q = $this->db->get("geofence_alert");
		return $q->result();
	}
}
