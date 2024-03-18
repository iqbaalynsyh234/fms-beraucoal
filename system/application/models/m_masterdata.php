<?php
class M_masterdata extends Model {

  function getAllGeofence($table, $userid){
    $this->bib_live_mysterio      = $this->load->database("bib_live_mysterio", true);
    $this->bib_live_mysterio->select("*");
    $this->bib_live_mysterio->where_in("geofence_type", array("site", "port"));
    // $this->bib_live_mysterio->where("geofence_user", $userid);
    $this->bib_live_mysterio->order_by("geofence_name", "ASC");
		$q             = $this->bib_live_mysterio->get($table);
		return $result = $q->result_array();
  }

  function getAllDumping($table){
    $this->db      = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("dumping_flag", 0);
    $this->db->order_by("dumping_id", "ASC");
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function getAllMaterial($table){
    $this->db      = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("material_flag", 0);
    $this->db->order_by("material_id", "ASC");
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function getMaterialByID($table, $material_no){
    $this->db      = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("material_no", $material_no);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function totalmastermaterial($table){
    $this->db      = $this->load->database("default", true);
    $this->db->order_by("material_no", "desc");
    $this->db->select("material_no");
    // $this->db->where("material_flag", 0);
    $this->db->limit(1, 0);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function getstreet_now($type)
  {
    $this->db->select("street_id, street_name, street_type, street_order");
      if ($type == 3) {
        $this->db->where_in("street_type", array(3, 5));
      }elseif ($type == 1) {
        $this->db->where("street_type", $type);
      }elseif ($type == 4) {
        $this->db->where_in("street_type", array(4, 7, 8));
      }else {
        $this->db->where("street_type", $type);
      }
    $this->db->order_by("street_order", "ASC");
    $this->db->where("street_creator", 4408);
    $this->db->where("street_flag", 1);
    $q = $this->db->get("street");
    $rows = $q->result_array();
    return $rows;
  }

  function geofenceforempty($table, $select_rom, $data){
    $this->db      = $this->load->database("default", true);
    $this->db->where("material_geofence", $select_rom);
    return $this->db->update($table, $data);
  }

  function updatethisgeofence($table, $material_no, $data){
    $this->db      = $this->load->database("default", true);
    $this->db->where("material_no", $material_no);
    return $this->db->update($table, $data);
  }

  function cekMaterialByMaterialID($table, $material_id){
    $this->db = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("material_id", $material_id);
    // $this->db->or_where("material_name", $material_id);
		$q                  = $this->db->get($table);
		return $result      = $q->result_array();
  }

  function cekClientByName($table, $add_client){
    $this->db = $this->load->database("default", true);
    $this->db->select("*");
    // $this->db->where("client_flag", 0);
    $this->db->where("client_id", $add_client);
		$q                  = $this->db->get($table);
		return $result      = $q->result_array();
  }

  function totalmasterclient($table){
    $this->db      = $this->load->database("default", true);
    $this->db->order_by("client_no", "desc");
    $this->db->select("client_no");
    // $this->db->where("material_flag", 0);
    $this->db->limit(1, 0);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function cekDumpingByName($table, $add_dumping){
    $this->db = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("dumping_id", $add_dumping);
    // $this->db->or_where("dumping_name", $add_id_dumping);
		$q                  = $this->db->get($table);
		return $result      = $q->result_array();
  }

  function totalmasterdumping($table){
    $this->db      = $this->load->database("default", true);
    $this->db->order_by("dumping_no", "desc");
    $this->db->select("dumping_no");
    // $this->db->where("material_flag", 0);
    $this->db->limit(1, 0);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function getDumpingByID($table, $dumping_no){
    $this->db      = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("dumping_no", $dumping_no);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function insertData($table, $data){
    return $this->db->insert($table, $data);
  }

  function getAllClient($table){
    $this->db      = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("client_flag", 0);
    $this->db->order_by("client_id", "ASC");
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function getClientByID($table, $client_no){
    $this->db      = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("client_no", $client_no);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }

  function updateData($table, $where, $wherenya, $data){
    $this->db      = $this->load->database("default", true);
    $this->db->where($where, $wherenya);
    return $this->db->update($table, $data);
  }

  function getFromPortal(){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->where_in("master_portal_type", array("Dump Truck","Dump Truck Coal Hauling","DT Hin FM260 JD"));
    $this->dbts->order_by("master_portal_id", "ASC");
    return $this->dbts->get("ts_bib_master_portal")->result_array();
  }

  function getFromPortalSimper(){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("portal_image_status, portal_register_number, portal_id_number, portal_name, portal_position, portal_id_position, portal_departmen, portal_company, portal_depkon_id, portal_date_of_hire, portal_exp_date, portal_blood_type,
portal_gender, portal_religion, portal_date_of_birth, portal_place_of_birth, portal_address, portal_tribe, portal_citizen, portal_emergency_contact,
portal_id_card_type, portal_port_access, portal_zone_access, portal_counting_pengajuan, portal_counting_gagal, portal_sim_type, portal_sim_number,
portal_sim_exp_date, portal_sim_scan, portal_issued_at, portal_status, portal_is_vvip, portal_verification_status, portal_no_ktp,
portal_ktp_scan, portal_atasan_langsung, portal_jabatan, portal_contact, portal_email, portal_mcu_date, portal_mcu_location, portal_mcu_file,
portal_mcu_description,portal_mcu_status,portal_status_karyawan, portal_violation, portal_violation_date,portal_license_file, portal_license_exp,
portal_inspection_point_target, portal_observation_point_target, portal_safety_talk_point_target, portal_hazard_report_point_target, portal_commisioning_point_target, portal_created_at, portal_updated_at, portal_created_by,
portal_updated_by, portal_nik, portal_deleted_by, portal_rfid_tag, portal_isafe_no, portal_default_isafe_password, portal_date_of_birth_string, portal_isafe_password,
portal_special_notes,portal_roleId, portal_coaching_point_target, portal_isERT, portal_vaksinasi, portal_akun_peduli, portal_tanggal_vaksin, portal_status_vaksin, portal_jenis_vaksin,
portal_tanggal_v1, portal_tanggal_v2, portal_verifikator_vaksin, portal_terakhir_verifikasi, portal_id, portal_submited_at, master_portal_updateddate_new");
    // $this->dbts->where_in("master_portal_type", array("Dump Truck","Dump Truck Coal Hauling","DT Hin FM260 JD"));
    $this->dbts->order_by("portal_id_number", "ASC");
	$this->dbts->where("portal_jabatan", "DRIVER");
    return $this->dbts->get("ts_bib_master_portal_simper")->result_array();
  }

  function getdataimage($table, $portal_id_number){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("portal_image");
	  $this->dbts->where("portal_id_number", $portal_id_number);
    return $this->dbts->get($table)->result_array();
  }

  function getFromMasterUnit(){
    $this->db      = $this->load->database("default", true);
    $this->db->where("master_portal_flag",0);
    $this->db->order_by("master_portal_nolambung", "ASC");
    return $this->db->get("master_portal")->result_array();
  }

  function alldriveritws(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->where("driveritws_flag", 0);
    $q       = $this->db->get("webtracking_driver_itws");
    return  $q->result_array();
  }

  function getallspeedlevel(){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->order_by("level_name", "asc");
    $this->dbts->where("level_flag", 0);
    $q       = $this->dbts->get("webtracking_ts_speed_level");
    return  $q->result_array();
  }

  function getallmdvrlevel(){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->order_by("level_name", "asc");
    $this->dbts->where("level_flag", 0);
    $q       = $this->dbts->get("webtracking_ts_mdvr_level");
    return  $q->result_array();
  }

  function insertDatadbts($table, $data){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    return $this->dbts->insert($table, $data);
  }

  function updateDatadbts($table, $where, $wherenya, $data){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->where($where, $wherenya);
    return $this->dbts->update($table, $data);
  }

  function cekSpeedLevelByName($table, $where, $level_name){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("level_name", $level_name);
    $q       = $this->dbts->get($table);
    return  $q->result_array();
  }

  function getOvSpeedLevel($table, $levelid){
    $this->dbts      = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("level_id", $levelid);
    $q       = $this->dbts->get($table);
    return  $q->result_array();
  }


















}
