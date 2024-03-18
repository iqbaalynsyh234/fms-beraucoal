<?php
include "base.php";

class Branchoffice extends Base {

	function Branchoffice()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}

    function index($field="all", $keyword="all", $offset=0)
    {

        $rows_branch = $this->getbranch();

        $this->db->where("company_created_by", $this->sess->user_id);
		$this->db->where("company_flag", 0);
        $total_company = $this->db->count_all_results("company");

        switch($field)
        {

            case "branch_name":
            $this->db->where("company_name LIKE '%".$keyword."%'");
            break;

        }

    $this->db->where("company_created_by", $this->sess->user_id);
    $q = $this->db->get("company");
    $rows = $q->result();
    $total = count($rows);

    $config['uri_segment'] = 5;
		$config['base_url'] = base_url()."transporter/brancoffice/index/".$field."/".$keyword;
		$config['total_rows'] = $total_company;
		$config['per_page'] = $this->config->item("limit_records");
		$this->pagination->initialize($config);

    $this->params["paging"] = $this->pagination->create_links();
    $this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
    $this->params["branch"] = $rows_branch;
		$this->params["content"] = $this->load->view("transporter/branch/result.php", $this->params, true);
		$this->load->view("templatesess", $this->params);
    }

    function add()
    {
        if (! isset($this->sess->user_company)){
		redirect(base_url());
		}

        $this->params["content"] = $this->load->view("transporter/branch/add.php", $this->params, true);
		$this->load->view("templatesess", $this->params);
    }

    function save()
    {
        if (! isset($this->sess->user_company)){
		redirect(base_url());
		}

        $company_name = isset($_POST["branch_name"]) ? $_POST["branch_name"] : "";
        $company_agent = $this->config->item("transporter_agent");
        $company_created_by = $this->sess->user_id;
		$company_telegram_sos = isset($_POST["company_telegram_sos"]) ? $_POST["company_telegram_sos"] : "";
		$company_telegram_parkir = isset($_POST["company_telegram_parkir"]) ? $_POST["company_telegram_parkir"] : "";
		$company_telegram_speed = isset($_POST["company_telegram_speed"]) ? $_POST["company_telegram_speed"] : "";
		$company_telegram_geofence = isset($_POST["company_telegram_geofence"]) ? $_POST["company_telegram_geofence"] : "";

        if($company_name == "")
        {
            $callback["error"] = true;
            $callback["message"] = "Please Fill Company Name";
            echo json_encode($callback);
            return;
        }

        unset($data);
        $data["company_name"] = $company_name;
        $data["company_agent"] = $company_agent;
        $data["company_created_by"] = $company_created_by;
		$data["company_telegram_sos"] = $company_telegram_sos;
		$data["company_telegram_parkir"] = $company_telegram_parkir;
		$data["company_telegram_speed"] = $company_telegram_speed;
		$data["company_telegram_geofence"] = $company_telegram_geofence;

        $this->db->insert("company", $data);

        $data_new_company = $this->select_new_company();

        $branch_company_id = $data_new_company->company_id;
        $branch_name = $data_new_company->company_name;
        $branch_address = isset($_POST["branch_address"]) ? $_POST["branch_address"] : "";
        $branch_city = isset($_POST["branch_city"]) ? $_POST["branch_city"] :"";
        $branch_telp = isset($_POST["branch_tlp"]) ? $_POST["branch_tlp"]:"";
        $branch_fax = isset($_POST["branch_fax"]) ? $_POST["branch_fax"]:"";
        $branch_created_by = $data_new_company->company_created_by;
        $branch_created_date = date("d/m/Y");

        unset($data_branch);
        $data_branch["branch_company_id"] = $branch_company_id;
        $data_branch["branch_name"] = $branch_name;
        $data_branch["branch_address"] = $branch_address;
        $data_branch["branch_city"] = $branch_city;
        $data_branch["branch_telp"] = $branch_telp;
        $data_branch["branch_fax"] = $branch_fax;
        $data_branch["branch_created_by"] = $branch_created_by;
        $data_branch["branch_created_date"] = $branch_created_date;

        $this->dbtransporter = $this->load->database("transporter", true);
        $this->dbtransporter->insert("transporter_branch", $data_branch);

        $this->dbtransporter->close();

        $callback["error"] = false;
        $callback["message"] = "Success Add Branch Office";
        $callback["redirect"] = base_url()."transporter/branchoffice/index";
        echo json_encode($callback);
        return;

    }

    function edit()
    {
        $id = $this->uri->segment(4);

        if (!$id)
        {
            return;
        }

        $this->db->where("company_id", $id);
        $this->db->limit(1);
        $q = $this->db->get("company");
        $row = $q->row();

        $this->dbtransporter = $this->load->database("transporter", true);

        $this->dbtransporter->select("*");
        $this->dbtransporter->from("transporter_branch");
        $this->dbtransporter->where("branch_company_id", $id);
        $this->dbtransporter->limit(1);
        $q_branch = $this->dbtransporter->get();
        $row_branch = $q_branch->row();

        $this->params["data"] = $row;
        $this->params["data_branch"] = $row_branch;
        $this->params["content"] = $this->load->view("transporter/branch/edit.php", $this->params, true);
		$this->load->view("templatesess", $this->params);

    }

    function update()
    {
        if (! isset($this->sess->user_company)){
		redirect(base_url());
		}

        $company_id = $this->input->post("company_id");
        $company_name = isset($_POST["company_name"]) ? $_POST["company_name"] : "";
		$company_telegram_sos = isset($_POST["company_telegram_sos"]) ? $_POST["company_telegram_sos"] : "";
		$company_telegram_parkir = isset($_POST["company_telegram_parkir"]) ? $_POST["company_telegram_parkir"] : "";
		$company_telegram_speed = isset($_POST["company_telegram_speed"]) ? $_POST["company_telegram_speed"] : "";
		$company_telegram_geofence = isset($_POST["company_telegram_geofence"]) ? $_POST["company_telegram_geofence"] : "";

       if($company_name == "")
        {
            $callback["error"] = true;
            $callback["message"] = "Please Fill Company Name";
            echo json_encode($callback);
            return;
        }

        unset($data);
        $data["company_name"] = $company_name;
		$data["company_telegram_sos"] = $company_telegram_sos;
		$data["company_telegram_parkir"] = $company_telegram_parkir;
		$data["company_telegram_speed"] = $company_telegram_speed;
		$data["company_telegram_geofence"] = $company_telegram_geofence;
        $this->db->where("company_id", $company_id);
        $this->db->update("company", $data);

        $branch_address = isset($_POST["branch_address"]) ? $_POST["branch_address"] :"";
        $branch_city = isset($_POST["branch_city"]) ? $_POST["branch_city"] :"";
        $branch_telp = isset($_POST["branch_telp"]) ? $_POST["branch_telp"] :"";
        $branch_fax = isset($_POST["branch_fax"]) ? $_POST["branch_fax"] :"";

        unset($data_branch);
        $data_branch["branch_company_id"] = $company_id;
        $data_branch["branch_name"] = $company_name;
        $data_branch["branch_address"] = $branch_address;
        $data_branch["branch_city"] = $branch_city;
        $data_branch["branch_telp"] = $branch_telp;
        $data_branch["branch_fax"] = $branch_fax;

        $this->dbtransporter = $this->load->database("transporter",true);
        $this->dbtransporter->where("branch_company_id", $company_id);
        $this->dbtransporter->update("branch", $data_branch);

        $callback["error"] = false;
        $callback["message"] = "Success Update Data";
        $callback["redirect"] = base_url()."transporter/branchoffice/index";

        echo json_encode($callback);
        return;



    }

    function getbranch()
    {
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("branch");
        $this->dbtransporter->where("branch_created_by", $this->sess->user_id);
        $qbranch = $this->dbtransporter->get();
        $rows_branch = $qbranch->result();
        return $rows_branch;
    }

    function select_new_company()
    {
        $this->db->order_by("company_created", "desc");
        $this->db->where("company_created_by", $this->sess->user_id);
        $this->db->limit(1);
        $q = $this->db->get("company");
        $row = $q->row();
        return $row;
    }




}

/* End of file branchoffice.php */
/* Location: ./system/application/controllers/transporter/branchoffice.php */
