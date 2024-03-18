<?php
include "base.php";

class Productionplan extends Base
{

	function __construct()
	{
		parent::Base();
		// $this->load->helper('common_helper');
		// $this->load->helper('email');
		// $this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->model("m_production");
		// $this->load->model("log_model");
		// $this->load->helper('common');
	}

	// USER FUNCTION START
	function index()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$privilegecode = $this->sess->user_id_role;


		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params['code_view_menu'] = "configuration";

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/productionplan/v_production_plan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/productionplan/v_production_plan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/productionplan/v_production_plan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/productionplan/v_production_plan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/productionplan/v_production_plan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/productionplan/v_production_plan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}


	function searchbydate()
	{
		$date = $this->input->post('date');
		$where['date'] = date("Y-m-d", strtotime($date));
		$getproductionplan = $this->m_production->getproductionplan($where);
		echo json_encode(array("code" => 200, "msg" => "success", "data" => $getproductionplan, "date" => $where['date']));
	}

	function save()
	{
		$date = $this->input->post('plan_date');
		$query_update = $this->input->post('query_update');
		$plan_created = date('Y-m-d H:i:s');
		$plan_user = $this->sess->user_id;

		$datafix = array();

		if ($query_update == 0) {
			//insert
			for ($i = 0; $i < 24; $i++) {
				$data = array(
					'plan_name' => $this->input->post('plan_name' . $i),
					'plan_value' => $this->input->post('plan_value' . $i),
					'plan_user' => $plan_user,
					'plan_ton' => $this->input->post('plan_ton' . $i),
					'plan_rit' => $this->input->post('plan_rit' . $i),
					'plan_type' => 1,
					'plan_date' => $date,
					'plan_created' => $plan_created
				);
				array_push($datafix, $data);
			}

			for ($i = 0; $i < 24; $i++) {
				$this->m_production->insertproductionplan($datafix[$i]);
			}
			echo json_encode(array("code" => 200, "msg" => "success insert", "date" => $date, "query_update" => $query_update, "data" => $datafix));
		} else {
			//update
			for ($i = 0; $i < 24; $i++) {
				$data = array(
					'plan_id' => $this->input->post('plan_id' . $i),
					'plan_user' => $plan_user,
					'plan_ton' => $this->input->post('plan_ton' . $i),
					'plan_rit' => $this->input->post('plan_rit' . $i)
				);
				array_push($datafix, $data);
			}

			for ($i = 0; $i < 24; $i++) {
				$this->m_production->updateproductionplan($datafix[$i], $datafix[$i]['plan_id']);
			}
			echo json_encode(array("code" => 200, "msg" => "success update", "date" => $date, "query_update" => $query_update));
		}
	}
}
