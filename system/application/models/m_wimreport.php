<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_wimreport extends Model {

	function M_wimreport ()
	{
		parent::Model();
	}

	function getreportnow($table, $company, $vehicle, $statuswim, $sdate, $edate){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
			// if ($company != "all") {
			// 	$this->dbkalimantan->where("integrationwim_TruckID", $vehicle);
			// }

			if ($vehicle != "all") {
				$this->dbkalimantan->where("integrationwim_TruckID", $vehicle);
			}

			if ($statuswim != "all") {
				$this->dbkalimantan->where("integrationwim_operator_status", $statuswim);
			}

		$this->dbkalimantan->where("integrationwim_PenimbanganStartLocal >= ", $sdate);
		$this->dbkalimantan->where("integrationwim_PenimbanganFinishLocal <= ", $edate);
		// $this->dbkalimantan->where("integrationwim_operator_status",1);//sudah di process
		$this->dbkalimantan->where("integrationwim_flag", 0);//bukan data dihapus
		$this->dbkalimantan->order_by("integrationwim_PenimbanganStartLocal", 'desc');
		// $this->dbkalimantan->order_by("integrationwim_PenimbanganStartLocal", "DESC");
		return $this->dbkalimantan->get($table)->result_array();
	}

	function getTransByID($transID,$reporttable){
		//GET DATA FROM DB
		$this->dbtensor     = $this->load->database("tensor_report", true);
		$this->dbtensor->select("*");
		$this->dbtensor->where("integrationwim_id ", $transID);
		$q = $this->dbtensor->get($reporttable);
		return $q->result_array();
	}

	function updatedatawim($databaseload, $table, $id, $data){
		$this->dbts     = $this->load->database($databaseload, true);
		$this->dbts->where("integrationwim_id ", $id);
		return $this->dbts->update($table, $data);
	}

	function dataactual($table, $filterdatevalue, $startdate, $month, $year, $limit, $contractor, $vehicle){
		$this->dbtensor     = $this->load->database("tensor_report", true);
		// $this->dbtensor->select("integrationwim_truckID, integrationwim_status, integrationwim_penimbanganStartLocal, integrationwim_penimbanganFinishLocal, integrationwim_created_date, TIMEDIFF(integrationwim_created_date, integrationwim_penimbanganFinishLocal) as Selisih");

		if ($filterdatevalue == 0) {
			$this->dbtensor->select("integrationwim_id, integrationwim_transactionID, integrationwim_gross, integrationwim_gross_manual, integrationwim_tare,
			 					integrationwim_tare_manual, integrationwim_netto, integrationwim_gross_manual, integrationwim_truckID, integrationwim_truckType,
								integrationwim_providerId, integrationwim_haulingContractor, integrationwim_material_name, integrationwim_client_name,
								integrationwim_approval_status, integrationwim_dumping_name, integrationwim_hauling_name, integrationwim_other_text1,
								integrationwim_other_text1, integrationwim_flag, integrationwim_itws_coal,
								integrationwim_status, integrationwim_penimbanganStartLocal,
								integrationwim_penimbanganFinishLocal, integrationwim_created_date");

				if ($contractor != "all") {
					$this->dbtensor->where("integrationwim_haulingContractor", $contractor);
				}

				if ($vehicle != "all") {
					$this->dbtensor->where("integrationwim_truckID", $vehicle);
				}

				$this->dbtensor->where("DATE(integrationwim_penimbanganStartLocal)", $startdate);
				$this->dbtensor->where("integrationwim_status", "ACTUAL");

				$this->dbtensor->where("integrationwim_replacement_status", "0");
			// $this->dbtensor->order_by("Selisih", "DESC");
		}else {
			$this->dbtensor->select("integrationwim_id, integrationwim_transactionID, integrationwim_gross, integrationwim_gross_manual, integrationwim_tare,
								integrationwim_tare_manual, integrationwim_netto, integrationwim_gross_manual, integrationwim_truckID, integrationwim_truckType,
								integrationwim_providerId, integrationwim_haulingContractor, integrationwim_material_name, integrationwim_client_name,
								integrationwim_approval_status, integrationwim_dumping_name, integrationwim_hauling_name, integrationwim_other_text1,
								integrationwim_other_text1, integrationwim_flag, integrationwim_itws_coal,
								integrationwim_status, integrationwim_penimbanganStartLocal,
								integrationwim_penimbanganFinishLocal, integrationwim_created_date,");

				if ($contractor != "all") {
					$this->dbtensor->where("integrationwim_haulingContractor", $contractor);
				}

				if ($vehicle != "all") {
					$this->dbtensor->where("integrationwim_truckID", $vehicle);
				}

			$this->dbtensor->where("MONTH(integrationwim_penimbanganStartLocal)", $month);
			$this->dbtensor->where("YEAR(integrationwim_penimbanganStartLocal)", $year);
			$this->dbtensor->where("integrationwim_status", "ACTUAL");
			$this->dbtensor->where("integrationwim_replacement_status", "0");
			// $this->dbtensor->order_by("Selisih", "DESC");
			$this->dbtensor->limit($limit);
		}

		$q = $this->dbtensor->get($table);
		return $q->result_array();
	}

	function dataaverage($table, $filterdatevalue, $startdate, $month, $year, $limit, $contractor, $vehicle){
		// -- TIMEDIFF(integrationwim_created_date,integrationwim_penimbanganFinishLocal) as Selisih
		// -- TIMEDIFF(integrationwim_created_date,integrationwim_penimbanganFinishLocal) as Selisih
		$limitfix = ($limit+100);

		$this->dbtensor     = $this->load->database("tensor_report", true);

		if ($filterdatevalue == 0) {
			$this->dbtensor->select("integrationwim_id, integrationwim_transactionID, integrationwim_gross, integrationwim_gross_manual, integrationwim_tare,
								integrationwim_tare_manual, integrationwim_netto, integrationwim_gross_manual, integrationwim_truckID, integrationwim_truckType,
								integrationwim_providerId, integrationwim_haulingContractor, integrationwim_material_name, integrationwim_client_name,
								integrationwim_approval_status, integrationwim_dumping_name, integrationwim_hauling_name, integrationwim_other_text1,
								integrationwim_other_text1, integrationwim_flag, integrationwim_itws_coal,
								integrationwim_status, integrationwim_penimbanganStartLocal,
								integrationwim_penimbanganFinishLocal, integrationwim_created_date");

				if ($contractor != "all") {
					$this->dbtensor->where("integrationwim_haulingContractor", $contractor);
				}

				if ($vehicle != "all") {
					$this->dbtensor->where("integrationwim_truckID", $vehicle);
				}

			$this->dbtensor->where("DATE(integrationwim_penimbanganStartLocal)", $startdate);
			$this->dbtensor->where("integrationwim_status", "AVERAGE FMS");
			$this->dbtensor->where("integrationwim_replacement_status", "0");
			$this->dbtensor->order_by("integrationwim_created_date", "DESC");
		}else {
			$this->dbtensor->select("integrationwim_id, integrationwim_transactionID, integrationwim_gross, integrationwim_gross_manual, integrationwim_tare,
								integrationwim_tare_manual, integrationwim_netto, integrationwim_gross_manual, integrationwim_truckID, integrationwim_truckType,
								integrationwim_providerId, integrationwim_haulingContractor, integrationwim_material_name, integrationwim_client_name,
								integrationwim_approval_status, integrationwim_dumping_name, integrationwim_hauling_name, integrationwim_other_text1,
								integrationwim_other_text1, integrationwim_flag, integrationwim_itws_coal,
								integrationwim_status, integrationwim_penimbanganStartLocal,
								integrationwim_penimbanganFinishLocal, integrationwim_created_date");

			if ($contractor != "all") {
				$this->dbtensor->where("integrationwim_haulingContractor", $contractor);
			}

			if ($vehicle != "all") {
				$this->dbtensor->where("integrationwim_truckID", $vehicle);
			}

			$this->dbtensor->where("MONTH(integrationwim_penimbanganStartLocal)", $month);
			$this->dbtensor->where("YEAR(integrationwim_penimbanganStartLocal)", $year);
			$this->dbtensor->where("integrationwim_status", "AVERAGE FMS");
			$this->dbtensor->where("integrationwim_replacement_status", "0");
			$this->dbtensor->order_by("integrationwim_created_date", "DESC");
		}

		$q = $this->dbtensor->get($table);
		return $q->result_array();
	}

	function getthistransactionid($table, $id){
		$this->dbtensor     = $this->load->database("tensor_report", true);

		$this->dbtensor->select("*");
		$this->dbtensor->where("integrationwim_id", $id);

		$q = $this->dbtensor->get($table);
		return $q->result_array();
	}

	function updatereplacement($table, $where, $wherenya, $data){
		$this->dbts     = $this->load->database("tensor_report", true);
		$this->dbts->where($where, $wherenya);
		return $this->dbts->update($table, $data);
	}

	function insert_historikal_replacement($table, $data){
		$this->dbts     = $this->load->database("tensor_report", true);
		return $this->dbts->insert($table, $data);
	}

	function insert_historikal_adminupdate($table, $data){
		$this->dbts     = $this->load->database("tensor_report", true);
		return $this->dbts->insert($table, $data);
	}

	function getAllTransactionID($transactionID){
			$this->dbreport = $this->load->database("tensor_report", true);
			$this->dbreport->select("integrationwim_transactionID");
			$this->dbreport->like("integrationwim_transactionID", $transactionID);

			$this->dbreport->order_by("integrationwim_transactionID", "ASC");
			$q = $this->dbreport->get("historikal_integrationwim_unit");
			return $q->result_array();
	}

	function dataReplacementReport($table, $company, $vehicle, $sdate, $edate){
		$this->dbreport = $this->load->database("tensor_report", true);

			if ($company != "all") {
				$this->dbreport->where("hist_replacementwim_companypengganti", $company);
			}

			if ($vehicle != "all") {
				$this->dbreport->where("hist_replacementwim_vehiclenopengganti", $vehicle);
			}

		$this->dbreport->where("hist_replacementwim_created_date >= ", $sdate);
		$this->dbreport->where("hist_replacementwim_created_date <= ", $edate);

		$this->dbreport->order_by("hist_replacementwim_created_date", "DESC");
		$q = $this->dbreport->get($table);
		return $q->result_array();
	}

	// MODEL KHUSUS ADMIN & OPERATOR ITWS
	function allDataClient(){
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("client_shortcut", "asc");
		$this->db->where("client_parent_id", 4408);
		$q       = $this->db->get("master_client");
		return  $q->result_array();
	}

	function allDataMaterial(){
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("material_shortcut", "asc");
		$this->db->where("material_parent_id", 4408);
		$q       = $this->db->get("master_material");
		return  $q->result_array();
	}

	function getDataVehicle($keyword){
		$this->db->select("vehicle_no");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->like("vehicle_no", $keyword);

		$this->db->where("vehicle_status <>", 3);
		return $this->db->get("vehicle")->result_array();
	}

	function getDataClient($keyword){
		$this->db->select("client_id");
		$this->db->order_by("client_id", "asc");
		$this->db->like("client_shortcut", $keyword);

		return $this->db->get("master_client")->result_array();
	}

	function getDataMaterial($keyword){
		$this->db->select("material_id");
		$this->db->order_by("material_id", "asc");
		$this->db->like("material_shortcut", $keyword);

		return $this->db->get("master_material")->result_array();
	}

	function getThisMaterial($keyword){
		$this->db->select("*");
		$this->db->order_by("material_id", "asc");
		$this->db->like("material_id", $keyword);

		return $this->db->get("master_material")->result_array();
	}

	function recallToLast($keyword){
		$dbtable        = "historikal_integrationwim_unit";
		$this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->limit(1, 0);
    $this->dbreport->like("integrationwim_truckID", $keyword);
		// $this->dbreport->where("integrationwim_operator_status", 1);
		$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
		$q = $this->dbreport->get($dbtable);
		return $q->result_array();
	}

	function updateitwsnow($transID, $data){
		$dbtable        = "historikal_integrationwim_unit";
		$this->dbreport = $this->load->database("tensor_report", true);
		$this->dbreport->where("integrationwim_transactionID", $transID);
		return $this->dbreport->update($dbtable, $data);
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

	function driveritwsbycompany($company_name){
    $this->db     = $this->load->database("default", true);
    $this->db->select("driveritws_id_driver, driveritws_driver_name");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->where("driveritws_flag", 0);
    $this->db->where("driveritws_company_name", $company_name);
    $q       = $this->db->get("webtracking_driver_itws");
    return  $q->result_array();
  }

	function alldriveritws(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->where("driveritws_flag", 0);
    $q       = $this->db->get("webtracking_driver_itws");
    return  $q->result_array();
  }

	function getDataDriverItws($keyword){
    $this->db->select("driveritws_driver_name");
    $this->db->order_by("driveritws_id_driver", "asc");
    $this->db->like("driveritws_id_driver", $keyword);

    return $this->db->get("driver_itws")->result_array();
  }

	function getThisDriverItws($keyword){
    $this->db->select("*");
    $this->db->order_by("driveritws_driver_name", "asc");
    $this->db->like("driveritws_driver_name", $keyword);

    return $this->db->get("driver_itws")->result_array();
  }

	function recallToLastOtherPort($keyword){
    $dbtable        = "historikal_integrationwim_unit";
		$this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->limit(1, 0);
    $this->dbreport->like("integrationwim_truckID", $keyword);
    $this->dbreport->where("integrationwim_dumping_fms_port !=", "");//bukan data dihapus
    $this->dbreport->where("integrationwim_dumping_fms_port !=", "PORT BIB");
		// $this->dbreport->where("integrationwim_operator_status", 1);
		$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
		$q = $this->dbreport->get($dbtable);
		return $q->result_array();
  }













}
