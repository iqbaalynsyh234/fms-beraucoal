<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Internalneeds extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Internalneeds()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
		$this->load->model("m_securityevidence");
		$this->load->model("gpsmodel");
	}

	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

    if($this->sess->user_id == "1445"){
			$user_id = $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_id_fix 										= $user_id;
    $companyid                      = $this->sess->user_company;
    $user_dblive                    = $this->sess->user_dblive;

    $company                  = $this->dashboardmodel->getcompany_byowner();
		$datavehicleandcompany    = array();
		$datavehicleandcompanyfix = array();

			for ($d=0; $d < sizeof($company); $d++) {
				$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
				// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
				$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
				$totalengine       = explode("|", $totaldata);
					array_push($datavehicleandcompany, array(
						"company_id"   => $company[$d]->company_id,
						"company_name" => $company[$d]->company_name,
						"totalmobil"   => $totalengine[2],
						"vehicle"      => $vehicledata[$d]
					));
			}

			// echo "<pre>";
			// var_dump($jsonnya);die();
			// echo "<pre>";

		$this->params['company']        = $company;
		$this->params['companyid']      = $companyid;
    $this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicle']      = $datavehicleandcompany;
    $this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);

    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('dashboard/internalneeds/maps_search', $this->params, true);
    $this->load->view("dashboard/template_dashboard_kalimantan", $this->params);

  }

}
