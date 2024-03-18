<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Turing extends Base {
	function Turing()
	{
		parent::Base();
		// DASHBOARD START
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		// DASHBOARD END

	}
	
	function index(){
	
		if(isset($this->sess->user_id_role))
		{
			
			$privilegecode   = $this->sess->user_id_role;
			$this->params['privilegecode']        	= $privilegecode;
			$this->params["header"]         		= $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["content"]     		    = $this->load->view('newdashboard/turing/turingview', $this->params, true);
			
				if ($privilegecode == 1) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
					
					$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
				}elseif ($privilegecode == 2) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
					
					$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
				}elseif ($privilegecode == 3) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
					
					$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
				}elseif ($privilegecode == 4) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);

					$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
				}elseif ($privilegecode == 5) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				
					$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
				}elseif ($privilegecode == 6) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			
					$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
				}else {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				
					$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
				}
		}
		else
		{
			
			
			//$this->params["chatsidebar"]    		= $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			$this->params["header"]         		= $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/turing/turingview', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
			
		}
		
		
		
		
	
		
	}

}
