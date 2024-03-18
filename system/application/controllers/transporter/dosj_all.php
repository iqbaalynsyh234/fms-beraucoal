<?php
include "base.php";

class Dosj_all extends Base {
	
		var $period1;
		var $period2;
		var $tblhist;
		var $tblinfohist;
		var $otherdb;
	
	function Dosj_all()
	{
		parent::Base();
		
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");		
		$this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('file');
		$this->load->helper('download');
		
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}		
		
	}
	
	function index($field="all", $keyword="all", $offset=0)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->params['sortby'] = "dosj_id";
		$this->params['orderby'] = "desc";

		$customer = $this->get_customer();
		$this->params["customer"] = $customer;
		$this->params["content"] = $this->load->view('dosj_all/list.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search($field="all", $keyword="all", $offset=0)
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$customer = isset($_POST['customer']) ? $_POST['customer'] : "";
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$dosj_company = $this->sess->user_company;
		
		if (!$dosj_company){
			redirect(base_url());
		}
		
		switch($field){
		case "dosj_no":
			$this->dbtransporter->where("dosj_no like", '%'.$keyword.'%');
		break;
		case "customer":
			$this->dbtransporter->where("dosj_customer_id", $customer);
		break;
		}
		
		$this->dbtransporter->where("dosj_flag", 0);
		$this->dbtransporter->where("dosj_company", $dosj_company);
		$this->dbtransporter->order_by("dosj_id","desc");
		$q = $this->dbtransporter->get("dosj_all", 20, $offset);
		$rows = $q->result();
		
		switch($field){
		case "do_no":
			$this->dbtransporter->where("dosj_no like", '%'.$keyword.'%');
		break;
		case "customer":
			$this->dbtransporter->where("dosj_customer_id", $customer);
		break;
		}
		
		$this->dbtransporter->where("dosj_flag", 0);
		$this->dbtransporter->where("dosj_company", $dosj_company);
		$this->dbtransporter->order_by("dosj_id","desc");
		$qtotal = $this->dbtransporter->get("dosj_all");
		$rowstotal = $qtotal->result();
		
		$total = count($rowstotal);
		
		//Get Customer
		$customer = $this->get_customer();
		
        $this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["customer"] = $customer;
		$this->params["title"] = "Manage SO / SJ";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("dosj_all/listresult.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function add()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$driver = $this->get_driver();
		$customer = $this->get_customer();
		$vehicle = $this->get_vehicle();
		$cost = $this->get_cost();
		
		$this->params["driver"] = $driver;
		$this->params["customer"] = $customer;
		$this->params["vehicle"] = $vehicle;
		$this->params["cost"] = $cost;
		$this->params["title"] = "Manage SO - ADD";		
		$this->params['content'] = $this->load->view("dosj_all/add", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save() 
	{
	
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
	$this->dbtransporter = $this->load->database("transporter", true);
	
	$dosj_type = isset($_POST['dosj_type']) ? $_POST['dosj_type'] : "";
    
	$dosj_no_block = isset($_POST['dosj_no_block']) ? $_POST['dosj_no_block'] : "";
    $dosj_no_mortar = isset($_POST['dosj_no_mortar']) ? $_POST['dosj_no_mortar'] : "";
    $dosj_no = $dosj_no_block.$dosj_no_mortar;
    
	$dosj_company = $this->sess->user_company;
	$dosj_customer_id = isset($_POST['customer']) ? $_POST['customer'] : 0;
	$post_vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
	$dosj_driver_id = isset($_POST['driver']) ? $_POST['driver'] : 0;
	$dosj_item_desc = isset($_POST['item_desc']) ? $_POST['item_desc'] : "";
	$dosj_item_size = isset($_POST['item_size']) ? $_POST['item_size'] : 0;
	$dosj_item_panjang = isset($_POST['item_panjang']) ? $_POST['item_panjang'] : 0;
	$dosj_item_lebar = isset($_POST['item_lebar']) ? $_POST['item_lebar'] : 0;
	$dosj_item_tinggi = isset($_POST['item_tinggi']) ? $_POST['item_tinggi'] : 0;
	
    $dosj_item_quantity = isset($_POST['item_quantity']) ? $_POST['item_quantity'] : 0;
	$dosj_item_quantity_mortar = isset($_POST['item_quantity_mortar']) ? $_POST['item_quantity_mortar'] : 0;
    
    $dosj_item_onship = $dosj_item_quantity;
    $dosj_item_onship_mortar = $dosj_item_quantity_mortar;
	//$dosj_item_onship = isset($_POST['item_onship']) ? $_POST['item_onship'] : 0;
	
    $dosj_item_unit = isset($_POST['item_unit']) ? $_POST['item_unit'] : "";
	$dosj_item_unit_mortar = isset($_POST['item_unit_mortar']) ? $_POST['item_unit_mortar'] : "";
    
    $dosj_item_shipdate = isset($_POST['ship_date']) ? $_POST['ship_date'] : 0;
    
    $dosj_block_no = isset($_POST['block_no']) ? $_POST['block_no'] : 0;
	$dosj_mortar_no = isset($_POST['mortar_no']) ? $_POST['mortar_no'] : 0;
	
    $do_delivered_cost = isset($_POST['cost']) ? $_POST['cost'] : 0;
	$dosj_note = isset($_POST['note']) ? $_POST['note'] : 0;
	
	$mysql_date_format  = date("Y-m-d", strtotime($dosj_item_shipdate));
	
	//Seleksi Data
	//*********************************************************************************
	//*********************************************************************************
	//*********************************************************************************
	
		//DO Type tidak boleh kosong
		if ($dosj_type == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Your DO Type !";
		
			echo json_encode($callback);
			return;
		}
		
		//No DO tidak boleh kosong
		if ($dosj_no == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Your DO Number !";
		
			echo json_encode($callback);
			return;
		}
	
		//Customer Tidak boleh kosong
		if ($dosj_customer_id == "" || $dosj_customer_id == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Your Customer !";
		
			echo json_encode($callback);
			return;
		}
	
		//Vehicle Tidak boleh kosong
		if ($post_vehicle == "" || $post_vehicle == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Vehicle No!";
		
			echo json_encode($callback);
			return;
		}
		
		//Driver Tidak boleh kosong
		if ($dosj_driver_id == "" || $dosj_driver_id == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Driver!";
		
			echo json_encode($callback);
			return;
		}
		
		//Get Vehicle Information
		$ve_explode = explode("#", $post_vehicle);
		$dosj_vehicle_id = $ve_explode[0];
		$dosj_vehicle_device = $ve_explode[1];
		$dosj_vehicle_name = $ve_explode[2];
		$dosj_vehicle_no = $ve_explode[3];
		
		//Item Description Tidak Boleh Kosong
		if ($dosj_item_desc == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Item Description!";
			
			echo json_encode($callback);
			return;
		}
	
		//Size Panjang Boleh Kosong
		/* if ($dosj_item_panjang == "" || $dosj_item_panjang == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input size P!";
		
			echo json_encode($callback);
			return;
		} */
	
		//Size Lebar Tidak Boleh Kosong
		/* if ($dosj_item_lebar == "" || $dosj_item_lebar == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input size L!";
		
			echo json_encode($callback);
			return;
		} */
	
		//Size Tinggi Tidak Boleh Kosong
		/* if ($dosj_item_tinggi == "" || $dosj_item_tinggi == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input size T!";
		
			echo json_encode($callback);
			return;
		} */
	
		//Item Quantity Tidak Boleh Kosong
		//if ($dosj_item_quantity == "" || $dosj_item_quantity == 0)
		//{
			//$callback['error'] = true;
			//$callback['message'] = "Please Input Item Quantity!";
		
			//echo json_encode($callback);
			//return;
		//}
	
		//Item Quantity Tidak Boleh Kosong
		/* if ($dosj_item_onship == "" || $dosj_item_onship == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Item On Ship!";
		
			echo json_encode($callback);
			return;
		} */
	
		//Item On Ship tidak boleh lebih dari Item Quantiy
		if (isset($dosj_item_onship) && isset($dosj_item_quantity))
		{
			if ($dosj_item_onship > $dosj_item_quantity)
			{
				$callback['error'] = true;
				$callback['message'] = "Total OnShip  >  Total Order Quantity, Please Check your input value ! ";
		
				echo json_encode($callback);
				return;
			}
		}
	
		//Unit Tidak Boleh Kosong
		/* if ($dosj_item_unit == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Unit Item!";
		
			echo json_encode($callback);
			return;
		} */
	
		//Ship Date tidak boleh kosong
		if ($dosj_item_shipdate == "" || $dosj_item_shipdate == 0)
		{	
			$callback['error'] = true;
			$callback['message'] = "Please Check Your Ship Date!";
		
			echo json_encode($callback);
			return;
		}
	
		//No Mortar tidak boleh kosong
		/* if ($dosj_mortar_no == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Check Your Mortar Number!";
		
			echo json_encode($callback);
			return;
		} */
		
		//Cost tidak boleh kosong
		if ($do_delivered_cost == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Cost On Delivery!";
		
			echo json_encode($callback);
			return;
		}
	
	//**************************** Finish Seleksi data inputan *********************
	//******************************************************************************
	//******************************************************************************
	
	//Cek apakah No DO Sudah ada
	//Jika Ada maka proses di stop
	$this->dbtransporter->where('dosj_flag',0);
	$this->dbtransporter->where('dosj_no',$dosj_no);
    $this->dbtransporter->or_where('dosj_no_block',$dosj_no_block);
    $this->dbtransporter->or_where('dosj_no_mortar',$dosj_no_mortar);
    
	$this->dbtransporter->limit(1);
	$qdo = $this->dbtransporter->get('dosj_all');
	$rdo = $qdo->row();
	$cdo = count($rdo);
	if ($cdo > 0)
	{
		$callback['error'] = true;
		$callback['message'] = "Duplicate SO Number, Please Check Your SO Number !";
		
		echo json_encode($callback);
		return;
	}
	
	$error = "";
	unset($data);
	
    $data['dosj_type'] = $dosj_type;
	$data['dosj_no'] = $dosj_no;
    $data['dosj_no_block'] = $dosj_no_block; 
    $data['dosj_no_mortar'] = $dosj_no_mortar; 
	$data['dosj_company'] = $dosj_company;
	$data['dosj_customer_id'] = $dosj_customer_id;
	$data['dosj_item_desc'] = $dosj_item_desc;
	$data['dosj_item_size'] = $dosj_item_size;
	$data['dosj_item_panjang'] = $dosj_item_panjang;
	$data['dosj_item_lebar'] = $dosj_item_lebar;
	$data['dosj_item_tinggi'] = $dosj_item_tinggi;
    
	$data['dosj_item_quantity'] = $dosj_item_quantity;
    $data['dosj_item_quantity_mortar'] = $dosj_item_quantity_mortar;
	
    $data['dosj_item_unit'] = $dosj_item_unit;
    $data['dosj_item_unit_mortar'] = $dosj_item_unit_mortar;
    
	$data['dosj_ship_date'] = $mysql_date_format;
    
	$data['dosj_block_no'] = $dosj_block_no;
    $data['dosj_mortar_no'] = $dosj_mortar_no;
    
	$data['dosj_note'] = $dosj_note;
	
	if ($dosj_item_quantity == $dosj_item_onship):
		$data['dosj_delivery_status'] = 1;
	endif;
	
	
	unset($data_delivered);
    
	$date_delivered_format = date("Y-m-d H:i:s");
	$data_delivered['do_delivered_do_type'] = $dosj_type;
	$data_delivered['do_delivered_do_number'] = $dosj_no;
    $data_delivered['do_delivered_do_block'] = $dosj_no_block;
    $data_delivered['do_delivered_do_mortar'] = $dosj_no_mortar;
    
	$data_delivered['do_delivered_quantity'] = $dosj_item_onship;
    $data_delivered['do_delivered_quantity_mortar'] = $dosj_item_quantity_mortar;
    
	$data_delivered['do_delivered_company'] = $dosj_company;
	$data_delivered['do_delivered_vehicle'] = $dosj_vehicle_device;
	$data_delivered['do_delivered_driver'] = $dosj_driver_id;
	$data_delivered['do_delivered_cost'] = $do_delivered_cost;
	$data_delivered['do_delivered_date'] = $mysql_date_format;
	$data_delivered['do_delivered_created'] = $date_delivered_format;
	
	unset($data_hist_driver);
	$data_hist_driver["driver_hist_company"] = $dosj_company;
	$data_hist_driver["driver_hist_vehicle"] = $dosj_vehicle_id;
	$data_hist_driver["driver_hist_vehicle_name"] = $dosj_vehicle_name;
	$data_hist_driver["driver_hist_vehicle_no"] = $dosj_vehicle_no;
	$data_hist_driver["driver_hist_driver"] = $dosj_driver_id;
	$data_hist_driver["driver_hist_date"] =  $dosj_item_shipdate;
	
	
	//Insert to table transporter_dosj
	$this->dbtransporter->insert("dosj_all", $data);
	
	//Insert to table transporter_dosj_delivered
	$this->dbtransporter->insert("dosj_delivered_all", $data_delivered);
	
	//Insert to table transporter_hist_driver
	$this->dbtransporter->insert("hist_driver", $data_hist_driver);
	
	$callback["error"] = false;
	$callback["message"] = "Add SO Success";
	$callback["redirect"] = base_url()."transporter/dosj_all";
	
	echo json_encode($callback);
	$this->dbtransporter->close();
	return;
	
	}
	
	function dosj_history()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->order_by("do_delivered_id","asc");
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_do_number", $id);
		$this->dbtransporter->join("dosj_all","dosj_no = do_delivered_do_number", "left");
		$this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
		$this->dbtransporter->join("cost_all","cost_id = do_delivered_cost", "left");
		$q = $this->dbtransporter->get("dosj_delivered_all");
		$rows = $q->result();
		//print_r($rows);exit;
		
		//Get 1 For Data
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_do_number", $id);
		$this->dbtransporter->join("dosj_all","dosj_no = do_delivered_do_number", "left");
		$this->dbtransporter->limit(1);
		$q1 = $this->dbtransporter->get("dosj_delivered_all");
		$rows1 = $q1->row();
		//print_r($rows1);exit;
		//Get Customer
		$vehicle = $this->get_vehicle();
		
		$params["data1"] = $rows1;
		$params["data"] = $rows;
		$params["vehicle"] = $vehicle;
		$html = $this->load->view('dosj_all/dosj_delivered_history', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
	}
	
	function mn_manage_do()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->params['sortby'] = "do_delivered_id";
		$this->params['orderby'] = "asc";

		$this->params["content"] = $this->load->view('dosj_all/list_manage_do.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function cost()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$destination = $this->get_destination();
		
		$this->params['sortby'] = "cost_id";
		$this->params['orderby'] = "asc";
		$this->params['destination'] = $destination;
		$this->params["content"] = $this->load->view('dosj_all/list_cost.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function destination()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->params['sortby'] = "destination_name";
		$this->params['orderby'] = "asc";
		$this->params["content"] = $this->load->view('dosj_all/list_destination.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function cost_add()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$destination = $this->get_destination();
		
		$this->params['destination'] = $destination;
		$this->params["title"] = "Manage Cost - ADD";		
		$this->params['content'] = $this->load->view("dosj_all/add_cost", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function cost_add_destination()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$destination = $this->get_destination();
		
		$this->params['destination'] = $destination;
		$this->params["title"] = "Manage Cost - ADD";		
		$this->params['content'] = $this->load->view("dosj_all/add_cost_destination", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function cost_edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->where("cost_company", $my_company);
		$this->dbtransporter->where("cost_id", $id);
		$q = $this->dbtransporter->get("cost_all");
		$row = $q->row();
		//print_r($row);exit;
		
		$destination = $this->get_destination();
		
		$params["data"] = $row;
		$params["destination"] = $destination;
		
		$html = $this->load->view('dosj_all/cost_edit', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function destination_edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->where("destination_company", $my_company);
		$this->dbtransporter->where("destination_id", $id);
		$q = $this->dbtransporter->get("destination_all");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('dosj_all/destination_edit', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	//delete cost
	function cost_delete(){
		
		//$id = $this->uri->segment(4);
		$id = $this->input->post('id');
		
		//print_r($id);exit;
		
		$this->dbtransporter = $this->load->database('transporter',true);
		
		if ($id)
		{
			unset($data);
			$data['cost_status'] = 2;
			$this->dbtransporter->where("cost_id", $id);
			$this->dbtransporter->update("cost_all",$data);
			
			//$this->dbtransporter->where("cost_id",$id);
			//$this->dbtransporter->set("cost_status", 2);
			//$this->dbtransporter->update("cost");
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
			
		}
		else
		{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
			
		echo json_encode($callback);
	}
	
	//delete destination
	function delete_destination(){
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter',true);
		
		if ($id)
		{
			unset($data);
			$data['destination_status'] = 2;
			$this->dbtransporter->where("destination_id", $id);
			$this->dbtransporter->update("destination_all",$data);
		
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
			
		}
		else
		{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
			
		echo json_encode($callback);
	}
	
	function save_cost()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$cost_destination = isset($_POST['cost_destination']) ? $_POST['cost_destination'] : 0;
		$cost_vehicle_type = isset($_POST['cost_vehicle_type']) ? $_POST['cost_vehicle_type'] : "";
		$cost = isset($_POST['cost']) ? $_POST['cost'] : 0;
		$cost_company = $my_company;
		
		//Destination
		if ($cost_destination == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Select Destination !";
		
			echo json_encode($callback);
			return;
		}
		
		unset($data);
		$data['cost_destination'] = $cost_destination;
		$data['cost_vehicle_type'] = $cost_vehicle_type;
		$data['cost'] = $cost;
		$data['cost_company'] = $cost_company;
		
		//Insert to table transporter_hist_driver
		$this->dbtransporter->insert("cost_all", $data);
	
		$callback["error"] = false;
		$callback["message"] = "Add Cost Success";
		$callback["redirect"] = base_url()."transporter/dosj_all/cost";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	}
	
	//add destination
	function save_destination()
	{
	
		if(! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$destination_name = isset($_POST['cost_dest']) ? $_POST['cost_dest'] : 0;
		$destination_company = $my_company;
		$destination_status = 1;
		
		unset($data);
		$data['destination_status'] = $destination_status;
		$data['destination_name'] = $destination_name;
		$data['destination_company'] = $destination_company;
		
		$this->dbtransporter->insert("destination_all", $data);
	
		$callback["error"] = false;
		$callback["message"] = "Add Destination Success";
		$callback["redirect"] = base_url()."transporter/dosj_all/destination";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	}
	
	function saveedit_cost()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$cost_id = isset($_POST['cost_id']) ? $_POST['cost_id'] : 0;
		$cost_destination  = isset($_POST['cost_destination']) ? $_POST['cost_destination'] : 0;
		$cost_vehicle_type  = isset($_POST['cost_vehicle_type']) ? $_POST['cost_vehicle_type'] : 0;
		$cost = isset($_POST['cost']) ? $_POST['cost'] : "";
		$cost_company = $my_company;
	
		unset($data);
		$data['cost_destination'] = $cost_destination;
		$data['cost_vehicle_type'] = $cost_vehicle_type;
		$data['cost'] = $cost;
		$data['cost_company'] = $cost_company;
		
		$this->dbtransporter->where('cost_id', $cost_id);
		$this->dbtransporter->update('cost_all', $data);
		
		$callback["error"] = false;
		$callback["message"] = "Edit Cost Success";
		$callback["redirect"] = base_url()."transporter/dosj_all/cost";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function saveedit_destination()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$destination_id = isset($_POST['destination_id']) ? $_POST['destination_id'] : 0;
		$destination_name  = isset($_POST['destination_name']) ? $_POST['destination_name'] : "";
		$destination_company = $my_company;
		$destination_status = 1;
	
		unset($data);
		$data['destination_name'] = $destination_name;
		$data['destination_status'] = $destination_status;
		$data['destination_company'] = $destination_company;
		
		$this->dbtransporter->where('destination_id', $destination_id);
		$this->dbtransporter->update('destination_all', $data);
		
		$callback["error"] = false;
		$callback["message"] = "Edit Destination Success";
		$callback["redirect"] = base_url()."transporter/dosj_all/destination";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function mn_driver_hist_dosj()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->params['sortby'] = "do_delivered_id";
		$this->params['orderby'] = "desc";
		
		$driver = $this->get_driver();
		$vehicle = $this->get_vehicle();
		
		$this->params["driver"] = $driver;
		$this->params["vehicle"] = $vehicle;
		$this->params["content"] = $this->load->view('dosj_all/list_driver_hist.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function mn_ritase_driver()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->params['sortby'] = "do_delivered_id";
		$this->params['orderby'] = "desc";
		
		$driver = $this->get_driver();
		
		$this->params["driver"] = $driver;
		$this->params["content"] = $this->load->view('dosj_all/list_ritase_driver.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function mn_driver_performance()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->params['sortby'] = "do_delivered_id";
		$this->params['orderby'] = "asc";
		
		$driver = $this->get_driver();
		
		$this->params["driver"] = $driver;
		$this->params["content"] = $this->load->view('dosj_all/list_driver_performance.php', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function mn_upload_dosj()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$this->params["content"] = $this->load->view('dosj_all/upload_form_dosj', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function mn_download_dosj_template()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$data = file_get_contents("./temp_upload/SampleDOSJ.xls"); // Read the file's contents
		$name = 'SampleDOSJ.xls';

		force_download($name, $data); 
		return;
	}
	
	function do_upload_dosj()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$config['upload_path'] = './temp_upload/';
		$config['allowed_types'] = 'xls';
                
		$this->load->library('upload', $config);
                

		if ( ! $this->upload->do_upload())
		{
			$data = array('error' => $this->upload->display_errors());
			
		}
		else
		{
            $data = array('error' => false);
			$upload_data = $this->upload->data();

            $this->load->library('excel_reader');
			$this->excel_reader->setOutputEncoding('CP1251');

			$file =  $upload_data['full_path'];
			$this->excel_reader->read($file);
			error_reporting(E_ALL ^ E_NOTICE);

			// Sheet 1
			$data = $this->excel_reader->sheets[0] ;
                        $dataexcel = Array();
			for ($i = 1; $i <= $data['numRows']; $i++) {

                            if($data['cells'][$i][1] == '') break;
                            $dataexcel[$i-1]['dosj_no'] = $data['cells'][$i][1];
                            //$dataexcel[$i-1]['dosj_customer_tmp'] = $data['cells'][$i][2];
							$dataexcel[$i-1]['dosj_item_desc'] = $data['cells'][$i][3];
							$dataexcel[$i-1]['dosj_item_panjang'] = $data['cells'][$i][4];
							$dataexcel[$i-1]['dosj_item_lebar'] = $data['cells'][$i][5];
							$dataexcel[$i-1]['dosj_item_tinggi'] = $data['cells'][$i][6];
							$dataexcel[$i-1]['dosj_item_quantity'] = $data['cells'][$i][7];
							$dataexcel[$i-1]['dosj_item_unit'] = $data['cells'][$i][8];
							$dataexcel[$i-1]['dosj_ship_date'] = $data['cells'][$i][9];
							$dataexcel[$i-1]['dosj_mortar_no'] = $data['cells'][$i][10];
							$dataexcel[$i-1]['dosj_note'] = $data['cells'][$i][11];

			}
                        
                        
            delete_files($upload_data['file_path']);
            $this->load->model('Dosj_model');
            $this->Dosj_model->tambahdosj($dataexcel);
		}
        
		redirect(base_url()."transporter/dosj", "refresh");
	}
	
	function search_manage_do()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
		$edate = isset($_POST['edate']) ? $_POST['edate'] : "";
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$dosj_company = $this->sess->user_company;
		
		if (!$dosj_company){
			redirect(base_url());
		}
		
		switch($field)
		{	
			case "dosj_no":
				
				$this->dbtransporter->where("do_delivered_do_number like", '%'.$keyword.'%');
				
			break;
		}
		
		if ($sdate != "" && $edate != "")
		{
			$fm_sdate = date("Y-m-d", strtotime($sdate));
			$fm_edate = date("Y-m-d", strtotime($edate));
			$this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
			$this->dbtransporter->where("do_delivered_date <=", $fm_edate);
		}
		$this->dbtransporter->where("do_delivered_flag",0);
		$this->dbtransporter->where("do_delivered_company", $dosj_company);
		$this->dbtransporter->order_by("do_delivered_id","desc");
		$this->dbtransporter->order_by("do_delivered_do_number","asc");
		$this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
		$this->dbtransporter->join("cost_all","cost_id = do_delivered_cost", "left");
		$q = $this->dbtransporter->get("dosj_delivered_all", 20, $offset);
		$rows = $q->result();
		
		switch($field)
		{
			case "dosj_no":
			$this->dbtransporter->where("do_delivered_do_number like", '%'.$keyword.'%');
			break;
		}
		
		if ($sdate != "" && $edate != "")
		{
			$fm_sdate = date("Y-m-d", strtotime($sdate));
			$fm_edate = date("Y-m-d", strtotime($edate));
			$this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
			$this->dbtransporter->where("do_delivered_date <=", $fm_edate);
		}
		$this->dbtransporter->where("do_delivered_flag",0);
		$this->dbtransporter->where("do_delivered_company", $dosj_company);
		$qtotal = $this->dbtransporter->get("dosj_delivered_all");
		$rowstotal = $qtotal->result();
		
		$total = count($rowstotal);
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		
		$this->pagination1->initialize($config);
		
		//Get Customer
		$vehicle = $this->get_vehicle();
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["vehicle"] = $vehicle;
		$this->params["title"] = "Manage SO";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("dosj_all/listresult_manage_do.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function search_cost()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$cost_destination = isset($_POST['cost_destination']) ? $_POST['cost_destination'] : 0;
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		if (!$my_company){
			redirect(base_url());
		}
		
		switch($field)
		{
			case "cost_destination":
				$this->dbtransporter->where("cost_destination", $cost_destination);
			break;
		}
		
		
		$this->dbtransporter->where("cost_company", $my_company);
		$this->dbtransporter->where("cost_status <> ", 2);
		$this->dbtransporter->order_by("cost_id","asc");
		$this->dbtransporter->join("destination_all","destination_id = cost_destination", "left");
		$q = $this->dbtransporter->get("cost_all", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		switch($field)
		{
			case "cost_destination":
				$this->dbtransporter->where("cost_destination", $cost_destination);
			break;
		}
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("cost_company", $my_company);
		$this->dbtransporter->where("cost_status <> ", 2);
		$qtotal = $this->dbtransporter->get("cost_all");
		$rowstotal = $qtotal->row();
		
		$total = $rowstotal->total;
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		//Get Customer
		$vehicle = $this->get_vehicle();
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["vehicle"] = $vehicle;
		$this->params["title"] = "Manage Cost";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("dosj_all/listresult_cost.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function search_destination()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "destination_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		if (!$my_company){
			redirect(base_url());
		}
		
		$this->dbtransporter->order_by($sortby,$orderby);
		$this->dbtransporter->where("destination_status",1);
		$this->dbtransporter->where("destination_company", $my_company);
		switch($field)
		{
			case "destination_name":
				$this->dbtransporter->where("destination_name like", '%'.$keyword.'%');
			break;
			case "destination_status":
				$this->dbtransporter->where("destination_status",$destination_status);
			break;
		}
		$q = $this->dbtransporter->get("destination_all", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		//total
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by("destination_id","asc");
		$this->dbtransporter->where("destination_status",1);
		$this->dbtransporter->where("destination_company", $my_company);
		switch($field)
		{
			case "destination_name":
				$this->dbtransporter->where("destination_name like", '%'.$keyword.'%');
			break;
			case "destination_status":
				$this->dbtransporter->where("destination_status",$destination_status);
			break;
		}
		$qtotal = $this->dbtransporter->get("destination_all");
		$rowstotal = $qtotal->row();
		
		$total = $rowstotal->total;
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "Manage Destination";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("dosj_all/listresult_destination.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function search_hist_driver()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;		
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : "";
		$sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
		$edate = isset($_POST['edate']) ? $_POST['edate'] : "";
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$dosj_company = $this->sess->user_company;
		
		if (!$dosj_company){
			redirect(base_url());
		}
		
		switch($field)
		{
			case "dosj_no":
				$this->dbtransporter->where("do_delivered_do_number LIKE '%".$keyword."%'", null);
			break;
			case "driver":
				$this->dbtransporter->where("do_delivered_driver", $driver);
			break;
			case "vehicle":
				$this->dbtransporter->where("do_delivered_vehicle", $vehicle);
			break;
		}
		
		if ($sdate != "" && $edate != "")
		{
			$fm_sdate = date("Y-m-d", strtotime($sdate));
			$fm_edate = date("Y-m-d", strtotime($edate));
			$this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
			$this->dbtransporter->where("do_delivered_date <=", $fm_edate);
		}
		
		$this->dbtransporter->where("do_delivered_company", $dosj_company);
		$this->dbtransporter->order_by("do_delivered_id","desc");
		$this->dbtransporter->order_by("do_delivered_do_number","asc");
		$this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
		$this->dbtransporter->join("dosj","dosj_no = do_delivered_do_number", "left");
		$this->dbtransporter->join("cost","cost_id = do_delivered_cost", "left");
		$q = $this->dbtransporter->get("dosj_delivered_all", 20, $offset);
		$rows = $q->result();
		
		switch($field)
		{
			case "dosj_no":
				$this->dbtransporter->where("do_delivered_do_number LIKE '%".$keyword."%'", null);
			break;
			case "driver":
				$this->dbtransporter->where("do_delivered_driver", $driver);
			break;
			case "vehicle":
				$this->dbtransporter->where("do_delivered_vehicle", $vehicle);
			break;
		}
		
		if ($sdate != "" && $edate != "")
		{
			$fm_sdate = date("Y-m-d", strtotime($sdate));
			$fm_edate = date("Y-m-d", strtotime($edate));
			$this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
			$this->dbtransporter->where("do_delivered_date <=", $fm_edate);
		}
		
		$this->dbtransporter->where("do_delivered_company", $dosj_company);
		$qtotal = $this->dbtransporter->get("dosj_delivered_all");
		$rowstotal = $qtotal->result();
		
		$total = count($rowstotal);
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		
		$this->pagination1->initialize($config);
		
		//Get Vehicle
		$vehicle = $this->get_vehicle();
		
		//Get Customer
		$customer = $this->get_customer();
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["vehicle"] = $vehicle;
		$this->params["customer"] = $customer;
		$this->params["title"] = "Driver Hist";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("dosj_all/listresult_driver_hist.php", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function search_ritase_driver()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;		
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
		$edate = isset($_POST['edate']) ? $_POST['edate'] : "";
                $dt_driver[] = array();
                $total_rit = 0;
                $total_row_rit = 0;
			
		$this->dbtransporter = $this->load->database('transporter', true);
		$driver_company = $this->sess->user_company;
		
		if (!$driver_company){
			redirect(base_url());
		}
		
		switch($field)
		{
			case "driver":
				$this->dbtransporter->where("driver_id", $driver);
			break;
			
		}
		
		
		$this->dbtransporter->where("driver_company", $driver_company);
		$this->dbtransporter->order_by("driver_name","asc");
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		
        unset($dt_driver);
        for($i=0;$i<count($rows);$i++)
        {
            if ($sdate != "" && $edate != "")
            {
                $fm_sdate = date("Y-m-d", strtotime($sdate));
                $fm_edate = date("Y-m-d", strtotime($edate));
                $this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
                $this->dbtransporter->where("do_delivered_date <=", $fm_edate);
                }
                
                $this->dbtransporter->where("do_delivered_driver",$rows[$i]->driver_id);
                $qd = $this->dbtransporter->get("dosj_delivered_all");
                $rd = $qd->result();
                $total_row_rit = count($rd);  
                $dt_driver[] = $rows[$i]->driver_name."|".$total_row_rit;
                
        }
        //print_r($dt_driver);exit;
		$this->params["title"] = "Driver Hist";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["data"] = $dt_driver;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("dosj_all/listresult_ritase_driver.php", $this->params, true);
		
		echo json_encode($callback);
	}
	
	function add_manage_do()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$driver = $this->get_driver();
		$dosj = $this->get_dosj_incomplete();
		$vehicle = $this->get_vehicle();
		$cost = $this->get_cost();
		
		$this->params["dosj"] = $dosj;
		$this->params["driver"] = $driver;
		$this->params["vehicle"] = $vehicle;
		$this->params["cost"] = $cost;
		$this->params["title"] = "Manage SO - ADD";		
		$this->params['content'] = $this->load->view("dosj_all/add_manage_do", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save_manage_do() 
	{
	
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
	$this->dbtransporter = $this->load->database("transporter", true);
	
	$dosj_no = $this->input->post("dosj_no");
	$dosj_company = $this->sess->user_company;
	$post_vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
	$dosj_driver_id = isset($_POST['driver']) ? $_POST['driver'] : 0;
	$dosj_item_onship = isset($_POST['quantity']) ? $_POST['quantity'] : 0;
	$dosj_item_shipdate = isset($_POST['ship_date']) ? $_POST['ship_date'] : 0;
	$do_delivered_cost = isset($_POST['cost']) ? $_POST['cost'] : 0;
	
	$mysql_date_format  = date("Y-m-d", strtotime($dosj_item_shipdate));
	
	//Seleksi Data
	//*********************************************************************************
	//*********************************************************************************
	//*********************************************************************************
	
		//No DO tidak boleh kosong
		if ($dosj_no == "0")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Your DO Number !";
		
			echo json_encode($callback);
			return;
		}
	
		//Vehicle Tidak boleh kosong
		if ($post_vehicle == "" || $post_vehicle == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Vehicle No!";
		
			echo json_encode($callback);
			return;
		}
		
		//Driver Tidak boleh kosong
		if ($dosj_driver_id == "" || $dosj_driver_id == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Driver!";
		
			echo json_encode($callback);
			return;
		}
		
		//Get Vehicle Information
		$ve_explode = explode("#", $post_vehicle);
		$dosj_vehicle_id = $ve_explode[0];
		$dosj_vehicle_device = $ve_explode[1];
		$dosj_vehicle_name = $ve_explode[2];
		$dosj_vehicle_no = $ve_explode[3];
		
	
		//Item Quantity Tidak Boleh Kosong
		if ($dosj_item_onship == "" || $dosj_item_onship == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Item On Ship!";
		
			echo json_encode($callback);
			return;
		}
	
		//Ship Date tidak boleh kosong
		if ($dosj_item_shipdate == "" || $dosj_item_shipdate == 0)
		{	
			$callback['error'] = true;
			$callback['message'] = "Please Check Your Ship Date!";
		
			echo json_encode($callback);
			return;
		}
	
		
		//Cost tidak boleh kosong
		if ($do_delivered_cost == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Cost On Delivery!";
		
			echo json_encode($callback);
			return;
		}
	
	//**************************** Finish Seleksi data inputan *********************
	//******************************************************************************
	//******************************************************************************
	
	
	//Cek apakah Total Pengiriman Melebihi Total Order DO
	//Jika Melebihi Maka akan di cancel
	//Jima sama ( total semua yang dikirim = total order do ) maka DO Delivery Status akan di set menjadi Complete
	//*******************************************************************************
	
	$tot_quantity = 0;
	$tot_quantity_ship = 0;
	$tot_all = 0;
	unset($update_dosj);
	
	$this->dbtransporter->select("dosj_item_quantity");
	$this->dbtransporter->where('dosj_no',$dosj_no);
	$this->dbtransporter->where("dosj_company",$dosj_company);
	$this->dbtransporter->limit(1);
	$qdo = $this->dbtransporter->get('dosj_all');
	$rdo = $qdo->row();
	$cdo = count($rdo);
	if ($cdo == 0)
	{
		$callback['error'] = true;
		$callback['message'] = "No DO Tidak Di Temukan !";
		
		echo json_encode($callback);
		return;
	}
	else
	{
		$tot_quantity = $rdo->dosj_item_quantity;
	}
	
	$this->dbtransporter->where("do_delivered_do_number",$dosj_no);
	$this->dbtransporter->where("do_delivered_company",$dosj_company);
	$qship = $this->dbtransporter->get("dosj_delivered_all");
	$rship = $qship->result();
	if (count($rship)>0)
	{
		for($x=0;$x<count($rship);$x++)
		{
			$tot_quantity_ship = $tot_quantity_ship + $rship[$x]->do_delivered_quantity;
		}
	}
	else
	{
		$tot_quantity_ship = 0;
	}
	
	$tot_all = $tot_quantity_ship + $dosj_item_onship;
	
	if ($tot_all > $tot_quantity)
	{
		$callback['error'] = true;
		$callback['message'] = "Total Pengiriman Melebihi Total Order !";
		
		echo json_encode($callback);
		return;
	}
	
	if ($tot_all == $tot_quantity)
	{
		$update_dosj["dosj_delivery_status"] = 1; //complete
		$this->dbtransporter->where('dosj_no',$dosj_no);
		$this->dbtransporter->update('dosj_all', $update_dosj);
	}
	
	//****************************************************************************
	//****************************************************************************
	//****************************************************************************
	
	unset($data_delivered);
	$date_delivered_format = date("Y-m-d H:i:s");
	$data_delivered['do_delivered_do_number'] = $dosj_no;
	$data_delivered['do_delivered_quantity'] = $dosj_item_onship;
	$data_delivered['do_delivered_company'] = $dosj_company;
	$data_delivered['do_delivered_vehicle'] = $dosj_vehicle_device;
	$data_delivered['do_delivered_driver'] = $dosj_driver_id;
	$data_delivered['do_delivered_cost'] = $do_delivered_cost;
	$data_delivered['do_delivered_date'] = $mysql_date_format;
	$data_delivered['do_delivered_created'] = $date_delivered_format;
	
	unset($data_hist_driver);
	$data_hist_driver["driver_hist_company"] = $dosj_company;
	$data_hist_driver["driver_hist_vehicle"] = $dosj_vehicle_id;
	$data_hist_driver["driver_hist_vehicle_name"] = $dosj_vehicle_name;
	$data_hist_driver["driver_hist_vehicle_no"] = $dosj_vehicle_no;
	$data_hist_driver["driver_hist_driver"] = $dosj_driver_id;
	$data_hist_driver["driver_hist_date"] =  $dosj_item_shipdate;

	//Insert to table transporter_dosj_delivered
	$this->dbtransporter->insert("dosj_delivered_all", $data_delivered);
	
	//Insert to table transporter_hist_driver
	$this->dbtransporter->insert("hist_driver", $data_hist_driver);
	
	$callback["error"] = false;
	$callback["message"] = "Add SO Success";
	$callback["redirect"] = base_url()."transporter/dosj_all";
	
	echo json_encode($callback);
	$this->dbtransporter->close();
	return;
	
	}
	
	function dosj_this_day()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this_day = date("Y-m-d");
		
		$this->dbtransporter->order_by("do_delivered_id","asc");
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_driver", $id);
		$this->dbtransporter->where("do_delivered_date", $this_day);
		$this->dbtransporter->join("dosj_all","dosj_no = do_delivered_do_number", "left");
		$this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
		$this->dbtransporter->join("cost_all","cost_id = do_delivered_cost", "left");
		$q = $this->dbtransporter->get("dosj_delivered_all");
		$rows = $q->result();
		//print_r($rows);exit;
		
		$customer = $this->get_customer();
		$vehicle = $this->get_vehicle();
		
		$params["data"] = $rows;
		$params["customer"] = $customer;
		$params["vehicle"] = $vehicle;
		
		$html = $this->load->view('dosj_all/dosj_this_day', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
	}
	
	function dosj_edit()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->order_by("dosj_id","asc");
		$this->dbtransporter->where("dosj_company", $my_company);
		$this->dbtransporter->where("dosj_no", $id);
		$q = $this->dbtransporter->get("dosj_all");
		$row = $q->row();
		//print_r($row);exit;
		
		$customer = $this->get_customer();
		
		
		$params["data"] = $row;
		$params["customer"] = $customer;
		
		
		$html = $this->load->view('dosj_all/dosj_edit', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function dosj_hist_edit()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		$this->dbtransporter->order_by("do_delivered_id","asc");
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_id", $id);
		$this->dbtransporter->join("dosj_all","dosj_no = do_delivered_do_number", "left");
		$this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
		$q = $this->dbtransporter->get("dosj_delivered_all");
		$row = $q->row();
		
		$driver = $this->get_driver();
		$vehicle = $this->get_vehicle();
		$cost = $this->get_cost();
		
		$params["data"] = $row;
		$params["driver"] = $driver;
		$params["vehicle"] = $vehicle;
		$params["cost"] = $cost;
		
		$html = $this->load->view('dosj_all/dosj_hist_edit', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function dosj_delete()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("dosj_company", $my_company);
		$this->dbtransporter->where("dosj_no", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("dosj_all");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('dosj_all/dosj_delete', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function dosj_hist_delete()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("dosj_delivered_all");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('dosj_all/dosj_hist_delete', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
	}
	
	function delete_dosj()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$dosj_no = $this->input->post('dosj_no');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		//Cari terlebih dahulu di dosj delivered, apakah masih ada no DO yang akan dihapus
		//Jika masih ada -> Proses Fail !
		
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_do_number", $dosj_no);
		$q = $this->dbtransporter->get("dosj_delivered_all");
		$rows = $q->result();
		
		if (count($rows)>0)
		{
			$callback['error'] = true;
			$callback['message'] = "No SO"." ".$dosj_no.", masih te-Record di Data SO History, Hapus terlebih dahulu di Menu SO History !";
			echo json_encode($callback);
			return;
		}
		else
		{
			unset($data);
			$data['dosj_flag'] = 1;
			$this->dbtransporter->where("dosj_no", $dosj_no);
			$this->dbtransporter->update("dosj_all",$data);
			$this->dbtransporter->close();
			
			$callback['error'] = false;
			$callback['message'] = "Delete Record Complete";
			$callback["redirect"] = base_url()."transporter/dosj_all";
			echo json_encode($callback);
			return;
		}
		
	}
	
	function delete_dosj_hist()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$dosj_id = $this->input->post('dosj_id');
		$dosj_no = $this->input->post('dosj_no');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		//rubah flag
		unset($statusdelivered);
		$statusdelivered["do_delivered_flag"] = 1;
		$this->dbtransporter->where("do_delivered_id", $dosj_id);
		$this->dbtransporter->update("dosj_delivered_all", $statusdelivered);
		
		/*$this->dbtransporter->where("do_delivered_id", $dosj_id);
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->delete("dosj_delivered_all");*/
		
		//update status dosj jadi incomplete
		unset($status);
		$status["dosj_delivery_status"] = 1;
		$this->dbtransporter->where("dosj_no", $dosj_no);
		$this->dbtransporter->where("dosj_company", $my_company);
		$this->dbtransporter->update("dosj_all", $status);
		
		$callback['error'] = false;
		$callback['message'] = "Delete Record Complete";
		$callback["redirect"] = base_url()."transporter/dosj_all/mn_manage_do";
		echo json_encode($callback);
		return;
		
	}
	
	function saveedit_dosj()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$dosj_id = $this->input->post("dosj_id");
		
        $dosj_no_awal = $this->input->post("dosj_no_awal");
        $dosj_no_block_awal = $this->input->post("dosj_no_block_awal");
        $dosj_no_mortar_awal = $this->input->post("dosj_no_mortar_awal");
        
		$dosj_quantity_awal = $this->input->post("dosj_quantity_awal");
        $dosj_quantity_mortar_awal = $this->input->post("dosj_quantity_mortar_awal");
		
        $dosj_no_block = $this->input->post("dosj_no_block");
        $dosj_no_mortar = $this->input->post("dosj_no_mortar");
        $dosj_no = $dosj_no_block.$dosj_no_mortar;
        
		$dosj_customer = $this->input->post("dosj_customer");
		//$dosj_customer_tmp = $this->input->post("dosj_customer_tmp");
		$dosj_item_desc = $this->input->post("dosj_item_desc");
		$dosj_item_size = $this->input->post("dosj_item_size");
		$dosj_item_panjang = $this->input->post("dosj_item_panjang");
		$dosj_item_lebar = $this->input->post("dosj_item_lebar");
		$dosj_item_tinggi = $this->input->post("dosj_item_tinggi");
		
        $dosj_item_unit = $this->input->post("dosj_item_unit");
        $dosj_item_unit_mortar = $this->input->post("dosj_item_unit_mortar");
		
        $dosj_quantity = $this->input->post("dosj_quantity");
        $dosj_quantity_mortar = $this->input->post("dosj_quantity_mortar");
        
		$dosj_block_no = $this->input->post("dosj_block_no");
        $dosj_mortar_no = $this->input->post("dosj_mortar_no");
        
		$dosj_note = $this->input->post("dosj_note");
		$tot_delivery = 0;
		
		//Check Input
		//******************************************************************
		if ($dosj_no == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Your SO Number !";
			echo json_encode($callback);
			return;
		endif;
		
		if ($dosj_item_desc == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Item Description !";
			echo json_encode($callback);
			return;
		endif;
		
		/* if ($dosj_item_panjang == "" || $dosj_item_lebar == "" || $dosj_item_tinggi == "" || $dosj_item_unit == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Item Detail (Size && Unit) !";
			echo json_encode($callback);
			return;
		endif; */
		
		/*if ($dosj_quantity == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Total Quantity ( Project Quantity ) !";
			echo json_encode($callback);
			return;
		endif;
        */
		
		/* if ($dosj_mortar_no == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Mortar No !";
			echo json_encode($callback);
			return;
		endif; */
		//*************************************************************************
		//*************************************************************************
			$this->dbtransporter->where("dosj_flag",0);
			$this->dbtransporter->where("dosj_company", $my_company);
			$this->dbtransporter->where("dosj_no", $dosj_no);
            
			$this->dbtransporter->limit(1);
			$qdo = $this->dbtransporter->get("dosj_all");
			$rowdo = $qdo->row();
			
		//Cek Apakah No DO Yang baru sudah ada didatabase
		//Jika Sudah ada error true
		if ($dosj_no_awal != $dosj_no)
		{
			
			if (count($rowdo)>0)
			{
				$callback['error'] = true;
				$callback['message'] = "Duplicate SO Number, Please Check Your SO Number";
				echo json_encode($callback);
				return;
			}
		}
		//*****************************************************************************
		
		//Jika ada perubahan di total Quantity, maka cek total quantity yang sudah di delivery
		//Jika total quantity yang sudah didelivery > total quantity baru maka proses fail !
		//Jika total quantity yang sudah didelivery < total quantity baru maka cek dosj_delivery_status
		//Jika dosj_delivery_status == 1 (Complete), maka di update menjadi 0 (Incomplete)
		//Jika total quantity yang sudah didelivery == total quantity baru maka cek dosj_delivery_status
		//Jika status blm complete maka di set menjadi complete
		/* if ($dosj_quantity_awal != $dosj_quantity)
		{
			$this->dbtransporter->where("do_delivered_do_number", $dosj_no_awal);
			$this->dbtransporter->where("do_delivered_company", $my_company);
			$qdo_hist = $this->dbtransporter->get("dosj_delivered_all");
			$rowdo_hist = $qdo_hist->result();
			if(count($rowdo_hist)>0)
			{
				for($t=0;$t<count($rowdo_hist);$t++)
				{
					$tot_delivery = $tot_delivery + $rowdo_hist[$t]->do_delivered_quantity;
				}
			}
			else
			{
				$tot_delivery = 0;
			}
			if ($tot_delivery > $dosj_quantity)
			{
				$callback['error'] = true;
				$callback['message'] = "Total Unit Yang Sudah di Delivery > New Total Quantity";
				echo json_encode($callback);
				return;
			}
			if ($tot_delivery < $dosj_quantity)
			{
				if ($rowdo->dosj_delivery_status == 1)
				{
					unset($update_stt_del);
					$update_stt_del["dosj_delivery_status"] = 0;
					$this->dbtransporter->where('dosj_id', $dosj_id);
					$this->dbtransporter->update('dosj', $update_stt_del);
				}
			}
			if ($tot_delivery == $dosj_quantity)
			{
				if ($rowdo->dosj_delivery_status == 0)
				{
					unset($update_stt_del);
					$update_stt_del["dosj_delivery_status"] = 1;
					$this->dbtransporter->where('dosj_id', $dosj_id);
					$this->dbtransporter->update('dosj', $update_stt_del);
				}
			}
		} */
		//*********************************************************************************
		//*********************************************************************************
		
		//Update
		unset($newdata);
        
		$newdata["dosj_no"] = $dosj_no;
        $newdata["dosj_no_block"] = $dosj_no_block;
        $newdata["dosj_no_mortar"] = $dosj_no_mortar;
        
		$newdata["dosj_customer_id"] = $dosj_customer;
		//$newdata["dosj_customer_tmp"] = $dosj_customer_tmp;
		$newdata["dosj_item_desc"] = $dosj_item_desc;
		$newdata["dosj_item_panjang"] = $dosj_item_panjang;
		$newdata["dosj_item_lebar"] = $dosj_item_lebar;
		$newdata["dosj_item_tinggi"] = $dosj_item_tinggi;
                $newdata["dosj_item_size"] = $dosj_item_size;
        
		$newdata["dosj_item_quantity"] = $dosj_quantity;
        $newdata["dosj_item_quantity_mortar"] = $dosj_quantity_mortar;
		
        $newdata["dosj_item_unit"] = $dosj_item_unit;
        $newdata["dosj_item_unit_mortar"] = $dosj_item_unit_mortar;
		
        $newdata["dosj_block_no"] = $dosj_block_no; 
		$newdata["dosj_mortar_no"] = $dosj_mortar_no;
		$newdata["dosj_note"] = $dosj_note;
		
		$this->dbtransporter->where('dosj_id', $dosj_id);
		$this->dbtransporter->update('dosj_all', $newdata);
		
		if ($dosj_no_awal != $dosj_no)
		{
			unset($new_do);
			$new_do["do_delivered_do_number"] = $dosj_no;
            $new_do["do_delivered_do_block"] = $dosj_no_block;
            $new_do["do_delivered_do_mortar"] = $dosj_no_mortar;
            
			$this->dbtransporter->where('do_delivered_do_number', $dosj_no_awal);
			$this->dbtransporter->update('dosj_delivered_all', $new_do);
		}
		
		if ($dosj_quantity_awal != $dosj_quantity || $dosj_quantity_mortar_awal != $dosj_quantity_mortar)
		{
			unset($new_del);
			$new_del["do_delivered_quantity"] = $dosj_quantity;
            $new_del["do_delivered_quantity_mortar"] = $dosj_quantity_mortar;
            
			$this->dbtransporter->where('do_delivered_do_number', $dosj_no_awal);
			$this->dbtransporter->update('dosj_delivered_all', $new_del);
		}
		
		$callback["error"] = false;
		$callback["message"] = "Edit SO Success";
		$callback["redirect"] = base_url()."transporter/dosj_all";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	
	}
	
	function saveedit_hist_do()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		$tot_delivery = 0;
		$tot_now = 0;
		$update_status = false;
		
		//Read Post Data
		$do_delivered_id = $this->input->post("do_delivered_id");
		$do_delivered_do_number = $this->input->post("do_delivered_do_number");
		$quantity_awal = $this->input->post("quantity_awal");
		$total_quantity = $this->input->post("total_quantity");
		//------------------------------------------------------------------
		$do_delivered_quantity = $this->input->post("do_delivered_quantity");
		$do_delivered_vehicle = $this->input->post("do_delivered_vehicle");
		$do_delivered_driver = $this->input->post("do_delivered_driver");
		$do_delivered_cost = $this->input->post("do_delivered_cost");
		$dev_date = $this->input->post("dosj_delivered_date");
		$do_delivered_status = $this->input->post("do_delivered_status");
		
		//Cek Post Data
		/*if ($do_delivered_quantity == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Quantity !";
			echo json_encode($callback);
			return;
		endif; */
		if ($do_delivered_vehicle == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Select Vehicle !";
			echo json_encode($callback);
			return;
		endif;
		if ($do_delivered_driver == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Select Driver !";
			echo json_encode($callback);
			return;
		endif;
		if ($do_delivered_cost == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Cost Delivery !";
			echo json_encode($callback);
			return;
		endif;
		if ($dev_date == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Input Delivery Date !";
			echo json_encode($callback);
			return;
		endif;
		if ($do_delivered_quantity > $total_quantity):
			$callback['error'] = true;
			$callback['message'] = "Total Delivery > Total Project, Proses Fail !";
			echo json_encode($callback);
			return;
		endif;
		if ($do_delivered_quantity  == $total_quantity):
			$update_status = true;
		endif;
		if ($do_delivered_status  == ""):
			$callback['error'] = true;
			$callback['message'] = "Please Select Delivery Status !";
			echo json_encode($callback);
		endif;
		//---------------------------------------------------------------------
		//----------------------------------------------------------------------
		
		
		$this->dbtransporter->where("dosj_company", $my_company);
		$this->dbtransporter->where("dosj_no", $do_delivered_do_number);
		$this->dbtransporter->limit(1);
		$qdo = $this->dbtransporter->get("dosj_all");
		$rowdo = $qdo->row();
		//print_r($rowdo);exit;
			
		if ($quantity_awal != $do_delivered_quantity)
		{
			$this->dbtransporter->where("do_delivered_do_number", $do_delivered_do_number);
			$this->dbtransporter->where("do_delivered_company", $my_company);
			$qdo_hist = $this->dbtransporter->get("dosj_delivered_all");
			$rowdo_hist = $qdo_hist->result();
			/* print_r($rowdo_hist);exit;
			return; */
			
			if(count($rowdo_hist)>0)
			{
				for($t=0;$t<count($rowdo_hist);$t++)
				{
					$tot_now = $tot_now + $rowdo_hist[$t]->do_delivered_quantity;
				}
				$tot_delivery = ($tot_now + $do_delivered_quantity) - $quantity_awal;
				
			}
			else
			{
				$tot_delivery = 0;
			}
			
			if ($tot_delivery > $total_quantity)
			{
				$callback['error'] = true;
				$callback['message'] = "Total Unit Yang Sudah di Delivery > New Total Quantity";
				echo json_encode($callback);
				return;
			}
			
			if ($tot_delivery < $total_quantity)
			{
				
				if ($rowdo->dosj_delivery_status == 1)
				{
					unset($update_stt_del);
					$update_stt_del["dosj_delivery_status"] = 0;
					$this->dbtransporter->where('dosj_no', $do_delivered_do_number);
					$this->dbtransporter->update('dosj_all', $update_stt_del);
				}
			}
			
			if ($tot_delivery == $total_quantity)
			{
				if ($rowdo->dosj_delivery_status == 0)
				{
					unset($update_stt_del);
					$update_stt_del["dosj_delivery_status"] = 1;
					$this->dbtransporter->where('dosj_no', $do_delivered_do_number);
					$this->dbtransporter->update('dosj', $update_stt_del);
				}
			}
		}
		
		$do_delivered_date = date("Y-m-d",strtotime($dev_date));
		
		unset($new_data);
		$new_data["do_delivered_quantity"] = $do_delivered_quantity;
		$new_data["do_delivered_vehicle"] = $do_delivered_vehicle;
		$new_data["do_delivered_driver"] = $do_delivered_driver;
		$new_data["do_delivered_cost"] = $do_delivered_cost;
		$new_data["do_delivered_date"] = $do_delivered_date;
		$new_data["do_delivered_status"] = $do_delivered_status;
		
		$this->dbtransporter->where('do_delivered_id', $do_delivered_id);
		$this->dbtransporter->update('dosj_delivered_all', $new_data);
		
		//set status 2 to table dosj all
		if(isset($do_delivered_status) && ($do_delivered_status == 1)){
			$dosj_status = 2;			
		}else{
			$dosj_status = 1;
		}
		unset($new_data2);
		$new_data2["dosj_delivery_status"] = $dosj_status;
		$this->dbtransporter->where('dosj_no', $do_delivered_do_number);
		$this->dbtransporter->update('dosj_all', $new_data2);
		
		
		$callback["error"] = false;
		$callback["message"] = "Edit SO Delivery Success";
		$callback["redirect"] = base_url()."transporter/dosj_all/mn_manage_do";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	}
	
	function get_driver()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$driver_company = $this->sess->user_company;
		$nodata = 0;
		
		$this->dbtransporter->order_by('driver_name', 'asc');
		$this->dbtransporter->where('driver_company', $driver_company);
		$q_driver = $this->dbtransporter->get('driver');
		$rows_driver = $q_driver->result();
		
		if (count($rows_driver)>0)
		{
			return $rows_driver;
		}
		else
		{
			return $nodata;
		}
	}
	
	function get_customer()
	{
		$driver_company = $this->sess->user_company;
		$nodata = 0;
		
		$this->db->where('group_status', 1);
		$this->db->where('group_company', $driver_company);
		$q_cust = $this->db->get('group');
		$rows_cust = $q_cust->result();
		
		if (count($rows_cust)>0)
		{
			return $rows_cust;
		}
		else
		{
			return $nodata;
		}
	}
	
	function get_vehicle()
	{
		$user_id = $this->sess->user_id;
		$user_company = $this->sess->user_company;
		$user_group = $this->sess->user_group;
		
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $user_id);
		if (isset($user_company) || isset($user_group))
		{
			if ($user_company > 0)
			{
				$this->db->or_where('vehicle_company', $user_company);
			}
			
			if ($user_group > 0)
			{
				$this->db->or_where('vehicle_group', $user_group);
			}
		}
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		//$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		return $rv;
	}
	
	function get_dosj_incomplete()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("dosj_no","asc");
		$this->dbtransporter->where("dosj_delivery_status","0");
		$this->dbtransporter->where("dosj_company", $my_company);
		$q = $this->dbtransporter->get("dosj_all");
		$rows = $q->result();
		return $rows;
		
	}
	
	function get_destination()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("destination_name","asc");
		$this->dbtransporter->where("destination_status","1");
		$this->dbtransporter->where("destination_company", $my_company);
		$q = $this->dbtransporter->get("destination_all");
		$rows = $q->result();
		return $rows;
		
	}
	
	function get_cost()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("cost_id","asc");
		$this->dbtransporter->where("cost_company", $my_company);
		$this->dbtransporter->join("destination_all","destination_id = cost_destination", "left");
		$q = $this->dbtransporter->get("cost_all");
		$rows = $q->result();
		return $rows;
		
	}

	function driver_searchoverspeed()
	{
	
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		$offset = 0;
		$id = "";
		$action = 0;
		$driver = $this->input->post("driver");
		$speed_limit = $this->input->post("speed_limit");
		$date_v = $this->input->post("period1");
		//print_r($date_v);exit;
		
		if ($date_v == "" )
		{
			$date_v = date("d-m-Y");
		}
		
		$dtdb = date("Y-m-d", strtotime($date_v));
		//print_r($dtdb);exit;
		
		$this->dbtransporter->where("do_delivered_driver", $driver);
		$this->dbtransporter->where("do_delivered_company", $my_company);
		$this->dbtransporter->where("do_delivered_date", $dtdb);
		$this->dbtransporter->limit(1);
		$q_v = $this->dbtransporter->get("dosj_delivered_all");
		$r_v = $q_v->row();
		//print_r($r_v);exit;
		
		if(count($r_v)>0)
		{
			$exv = explode("@", $r_v->do_delivered_vehicle);
			$name = $exv[0];
			$host = $exv[1];
		}
		else
		{
			$name = "0";
			$host = "0";
		}
		
		$limit = (isset($_POST['limit']) && $_POST['limit']) ? $_POST['limit'] : $this->config->item('history_limit_records');
		
		// tentukan tanggal 
		
		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();	
		
		//print_r($rowvehicle);exit;
		
		$vehicle_nopol = $rowvehicle->vehicle_no;

		$tables = $this->gpsmodel->getTable($rowvehicle);
		
		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		$yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
/* 		
		$date_v1 = $date_v." "."00:00:00";
		$date_v2 = $date_v." "."23:59:59";
		
		$t1 = date("d-m-Y H:i:s", strtotime("-7 hour", strtotime($date_v)));
		$t2 = date("d-m-Y H:i:s", strtotime("-7 hour", strtotime($date_v)));
		 */
		$t1 = $date_v - 7*3600;
		$t2 = $date_v - 7*3600;
		
		//print_r($t2);exit;

		if ($t1 > $yesterday)
		{
			$rows = $this->historymodel->overspeed($tables["gps"], $name, $host, $speed_limit, $t1, $t2, $action, $limit, $offset);
			$total = $this->historymodel->overspeed($tables["gps"], $name, $host, $speed_limit, $t1, $t2, -1);
		}
		else
		{
			$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			
			if ($t2 > $yesterday)
			{	
				//mix			
				
				$rows = $this->historymodel->overspeed($tables["gps"], $name, $host, $speed_limit, $yesterday+1, $t2, 0);
				
				$this->db = $this->load->database("gpshistory", TRUE);
				$rowshist = $this->historymodel->overspeed($tablehist, $name, $host, $speed_limit, $t1, $yesterday, 0);
				
				$rows = array_merge($rows, $rowshist);
				
				$total = count($rows);
				if ($_POST['act'] != "export")
				{
					$rows = array_slice($rows, $offset, $limit);
				}
				
			}
			else
			{
				
				$this->db = $this->load->database("gpshistory", TRUE);							
				
				$rows = $this->historymodel->overspeed($tablehist, $name, $host, $speed_limit, $t1, $t2, $action,  $limit, $offset);
				$total = $this->historymodel->overspeed($tablehist, $name, $host, $speed_limit, $t1, $t2, -1);
				
			}
		}

		$this->db = $this->load->database($tables["dbname"], TRUE);
		
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
			$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");		

			$rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
			$rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			
			$rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			if (isset($positions[$rows[$i]->gps_longitude_real_fmt][$rows[$i]->gps_longitude_real_fmt]))
			{
				$rows[$i]->georeverse = $positions[$rows[$i]->gps_longitude_real_fmt][$rows[$i]->gps_longitude_real_fmt];
			}
			else
			{
				$rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);
			}
			
			$positions[$rows[$i]->gps_longitude_real_fmt][$rows[$i]->gps_longitude_real_fmt] = $rows[$i]->georeverse;
			
		}	
		
		$this->load->library('pagination1');

		$config['total_rows'] = $total;
		$config['uri_segment'] = 6;
		$config['per_page'] = $limit; 
		$config['num_links'] = floor($total/$limit);

		$this->pagination1->initialize($config); 

		$params['paging'] = $this->pagination1->create_links();		
		$params['gps_name'] = $name;
		$params['gps_host'] = $host;
		$params['offset'] = $offset;
		$params['data'] = $rows;
		//$params['id'] = $id;
		$html = $this->load->view("dosj/listresult_driver_performance", $params, true);
		
		$callback['title'] = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error'] = false;
		$callback['html'] = $html;
			
		echo json_encode($callback);		
	}
        
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */