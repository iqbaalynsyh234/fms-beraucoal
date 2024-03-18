<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_wimreport extends Model {

	function M_wimreport ()
	{
		parent::Model();
	}

	function getreportnow($table, $vehicle, $sdate, $edate){
		$this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
			if ($vehicle != "all") {
				$this->dbkalimantan->where("integrationwim_TruckID", $vehicle);
			}
		$this->dbkalimantan->where("integrationwim_PenimbanganStartLocal >= ", $sdate);
		$this->dbkalimantan->where("integrationwim_PenimbanganFinishLocal <= ", $edate);
		return $this->dbkalimantan->get($table)->result_array();
	}












}
