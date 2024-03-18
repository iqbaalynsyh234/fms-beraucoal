<?php
include "base.php";

class Powerblock extends Base {
	
		var $period1;
		var $period2;
		var $tblhist;
		var $tblinfohist;
		var $otherdb;
	
	function Powerblock()
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
	
	function mn_tupperware()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/menu", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function mn_slcars()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/slcars_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function mn_download_barcode()
	{
		$barcode = $this->get_barcode();
		$this->params['barcode'] = $barcode;
		$this->params['content'] = $this->load->view("mod_tupperware/download_barcode", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function mn_dr()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/dr_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function booking_id()
	{
		
		if ($this->config->item("app_tupperware"))
		{
			$vehicle = $this->get_all_vehicle();
			$driver = $this->get_all_driver();
			$all_company = $this->get_all_company();
			$this->params['company'] = $all_company;
		}
		else
		{
			$vehicle = $this->get_vehicle();
			$driver = $this->get_driver();
		}
		
		$this->params['vehicle'] = $vehicle;
		$this->params['driver'] = $driver;
		$this->params['content'] = $this->load->view("mod_tupperware/id_booking_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function loading_list()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/loading_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function search_id_booking()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "booking_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		$booking_vehicle = isset($_POST['booking_vehicle']) ? $_POST['booking_vehicle'] : "";
		$booking_driver = isset($_POST['booking_driver']) ? $_POST['booking_driver'] : "";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$startdate_loading = isset($_POST['startdate_loading']) ? $_POST['startdate_loading'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$enddate_loading = isset($_POST['enddate_loading']) ? $_POST['enddate_loading'] : "";
		$booking_delivery_status = isset($_POST["booking_delivery_status"]) ? $_POST["booking_delivery_status"] : "";
		$starttime = isset($_POST["starttime"]) ? $_POST["starttime"] : "";
		$starttime_loading = isset($_POST["starttime_loading"]) ? $_POST["starttime_loading"] : "";
		$endtime = isset($_POST["endtime"]) ? $_POST["endtime"] : "";
		$endtime_loading = isset($_POST["endtime_loading"]) ? $_POST["endtime_loading"] : "";
		$booking_loading = isset($_POST["booking_loading"]) ? $_POST["booking_loading"] : 0;
		
		$startdate_fmt = date("Y-m-d",strtotime($startdate));
		$enddate_fmt = date("Y-m-d",strtotime($enddate));
		
		$startdate_fmt_loading = date("Y-m-d",strtotime($startdate_loading));
		$enddate_fmt_loading = date("Y-m-d",strtotime($enddate_loading));
		
		$t1 = date("Y-m-d H:i:s",strtotime($startdate." ".$starttime.":00"));
		$t2 = date("Y-m-d H:i:s",strtotime($enddate." ".$endtime.":00"));
		
		$t1_loading = date("Y-m-d H:i:s",strtotime($startdate_loading." ".$starttime_loading.":00"));
		$t2_loading = date("Y-m-d H:i:s",strtotime($enddate_loading." ".$endtime_loading.":00"));
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		if ($this->config->item("app_tupperware"))
		{
			$vehicle = $this->get_all_vehicle();
			$driver = $this->get_all_driver();
			$all_company = $this->get_all_company();
			$this->params['company'] = $all_company;
		}
		else
		{
			$vehicle = $this->get_vehicle();
			$driver = $this->get_driver();
		}
		
		$this->dbtransporter->order_by("booking_id","desc");
		
		
		switch($field)
		{
			case "booking_id":
				$this->dbtransporter->where("booking_id like", '%'.$keyword.'%');
			break;
			case "booking_destination":
				$this->dbtransporter->where("booking_destination like", '%'.$keyword.'%');
			break;
			case "booking_armada_type":
				$this->dbtransporter->where("booking_armada_type like", '%'.$keyword.'%');
			break;
			case "booking_cbm_loading":
				$this->dbtransporter->where("booking_cbm_loading like", '%'.$keyword.'%');
			break;
			case "booking_vehicle":
				$this->dbtransporter->where("booking_vehicle like", '%'.$booking_vehicle.'%');
			break;
			case "booking_driver":
				$this->dbtransporter->where("booking_driver like", '%'.$booking_driver.'%');
			break;
			case "booking_date_in":
				$this->dbtransporter->where("booking_date_in >= ", $startdate_fmt);
				$this->dbtransporter->where("booking_date_in <= ", $enddate_fmt);
			break;
			case "booking_time_in":
				$this->dbtransporter->where("booking_time_in like", '%'.$keyword.'%');
			break;
			case "booking_warehouse":
				$this->dbtransporter->where("booking_warehouse like", '%'.$keyword.'%');
			break;
			case "booking_delivery_status":
				$this->dbtransporter->where("booking_delivery_status",$booking_delivery_status);
			break;
			case "booking_datetime_in":
				$this->dbtransporter->where("booking_datetime_in >=",$t1);
				$this->dbtransporter->where("booking_datetime_in <=",$t2);
			break;
			case "booking_loading":
				$this->dbtransporter->where("booking_loading",$booking_loading);
			break;
			case "booking_loading_date":
				$this->dbtransporter->where("booking_loading_date >=",$t1_loading);
				$this->dbtransporter->where("booking_loading_date <=",$t2_loading);
			break;
		}
		
		if (!$this->config->item("app_tupperware"))
		{
			$this->dbtransporter->where("booking_company", $my_company);
		}
		
		$this->dbtransporter->where("booking_status", 1);
		$this->dbtransporter->join("tupper_barcode","transporter_barcode = booking_id","left_outer");
		$q = $this->dbtransporter->get("id_booking", 50, $offset);
		$rows = $q->result();
		
		
		$this->dbtransporter->select("count(*) as total");
		
		
		switch($field)
		{
			case "booking_id":
				$this->dbtransporter->where("booking_id like", '%'.$keyword.'%');
			break;
			case "booking_destination":
				$this->dbtransporter->where("booking_destination like", '%'.$keyword.'%');
			break;
			case "booking_armada_type":
				$this->dbtransporter->where("booking_armada_type like", '%'.$keyword.'%');
			break;
			case "booking_cbm_loading":
				$this->dbtransporter->where("booking_cbm_loading like", '%'.$keyword.'%');
			break;
			case "booking_vehicle":
				$this->dbtransporter->where("booking_vehicle like", '%'.$booking_vehicle.'%');
			break;
			case "booking_driver":
				$this->dbtransporter->where("booking_driver like", '%'.$booking_driver.'%');
			break;
			case "booking_date_in":
				$this->dbtransporter->where("booking_date_in >= ", $startdate_fmt);
				$this->dbtransporter->where("booking_date_in <= ", $enddate_fmt);
			break;
			case "booking_time_in":
				$this->dbtransporter->where("booking_time_in like", '%'.$keyword.'%');
			break;
			case "booking_warehouse":
				$this->dbtransporter->where("booking_warehouse like", '%'.$keyword.'%');
			break;
			case "booking_delivery_status":
				$this->dbtransporter->where("booking_delivery_status",$booking_delivery_status);
			break;
			case "booking_datetime_in":
				$this->dbtransporter->where("booking_datetime_in >=",$t1);
				$this->dbtransporter->where("booking_datetime_in <=",$t2);
			break;
			case "booking_loading":
				$this->dbtransporter->where("booking_loading",$booking_loading);
			break;
			case "booking_loading_date":
				$this->dbtransporter->where("booking_loading_date >=",$t1_loading);
				$this->dbtransporter->where("booking_loading_date <=",$t2_loading);
			break;
		}
		
		if (!$this->config->item("app_tupperware"))
		{
			$this->dbtransporter->where("booking_company", $my_company);
		}
		
		$this->dbtransporter->where("booking_status", 1);
		$this->dbtransporter->join("tupper_barcode","transporter_barcode = booking_id","left_outer");
		$qt = $this->dbtransporter->get("id_booking");
		$rt = $qt->row();
		$total = $rt->total;
		
        $typearmada = $this->get_typearmada();
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
		
        $this->params["typearmada"] = $typearmada;
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["driver"] = $driver;
		$this->params["vehicle"] = $vehicle;
		$this->params["title"] = "ID Booking";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_tupperware/id_booking_list_result", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	
	function search_dr()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "transporter_dr_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		
		$startdate_fmt = date("Y-m-d",strtotime($startdate));
		$enddate_fmt = date("Y-m-d",strtotime($enddate));
		
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->order_by("transporter_dr_id","desc");
		
		switch($field)
		{
			case "transporter_dr_so":
				$this->dbtransporter->where("transporter_dr_so like", '%'.$keyword.'%');
			break;
			case "transporter_dr_dr":
				$this->dbtransporter->where("transporter_dr_dr like", '%'.$keyword.'%');
			break;
			case "dist_code":
				$this->dbtransporter->where("dist_code like", '%'.$keyword.'%');
			break;
			case "delivered":
				$this->dbtransporter->where("booking_delivery_status",2);
			break;
			case "booking_date_in":
				$this->dbtransporter->where("booking_date_in >= ", $startdate_fmt);
				$this->dbtransporter->where("booking_date_in <= ", $enddate_fmt);
			break;
		}
		
		
		if ($field == "delivered" || $field == "booking_date_in")
		{
			$this->dbtransporter->join("id_booking","booking_id = transporter_dr_booking_id","left_outer");
		}
		else
		{
			$this->dbtransporter->join("dist_tupper","dist_id = transporter_db_code","left_outer");
		}
		
		$this->dbtransporter->where("transporter_dr_status", 1);
		$q = $this->dbtransporter->get("tupper_dr", 50, $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "transporter_dr_so":
				$this->dbtransporter->where("transporter_dr_so like", '%'.$keyword.'%');
			break;
			case "transporter_dr_dr":
				$this->dbtransporter->where("transporter_dr_dr like", '%'.$keyword.'%');
			break;
			case "dist_code":
				$this->dbtransporter->where("dist_code like", '%'.$keyword.'%');
			break;
			case "delivered":
				$this->dbtransporter->where("booking_delivery_status",2);
			break;
			case "booking_date_in":
				$this->dbtransporter->where("booking_date_in >= ", $startdate_fmt);
				$this->dbtransporter->where("booking_date_in <= ", $enddate_fmt);
			break;
		}
		
		
		if ($field == "delivered" || $field == "booking_date_in")
		{
			$this->dbtransporter->join("id_booking","booking_id = transporter_dr_booking_id","left_outer");
		}
		else
		{
			$this->dbtransporter->join("dist_tupper","dist_id = transporter_db_code","left_outer");
		}
		
		$this->dbtransporter->where("transporter_dr_status", 1);
		$qt = $this->dbtransporter->get("tupper_dr");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "DR/SO";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_tupperware/dr_list_result", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function add_id_booking()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$slcars = $this->get_slcars();
		
        $this->params["slcars"] = $slcars;
		$this->params["title"] = "ID Booking - ADD";		
		$this->params['content'] = $this->load->view("mod_tupperware/add_id_booking", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save_id_booking() 
	{
	
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : "";
		$booking_company = $my_company;
		$booking_destination = isset($_POST['booking_destination']) ? $_POST['booking_destination'] : "";
		$booking_armada_type = isset($_POST['booking_armada_type']) ? $_POST['booking_armada_type'] : "";
		$booking_cbm_loading = isset($_POST['booking_cbm_loading']) ? $_POST['booking_cbm_loading'] : "";
		$booking_vehicle = isset($_POST['booking_vehicle']) ? $_POST['booking_vehicle'] : "";
		$booking_driver = isset($_POST['booking_driver']) ? $_POST['booking_driver'] : 0;
		$booking_date_in = isset($_POST['booking_date_in']) ? $_POST['booking_date_in'] : "";
		$booking_time_in = isset($_POST['booking_time_in']) ? $_POST['booking_time_in'] : "";
		$booking_warehouse = isset($_POST['booking_warehouse']) ? $_POST['booking_warehouse'] : "";
		$booking_notes = isset($_POST['booking_notes']) ? $_POST['booking_notes'] : "";
		$booking_dbtype = isset($_POST['dbtype']) ? $_POST['dbtype'] : "";
		$booking_date_in_fmt  = date("Y-m-d", strtotime($booking_date_in));
		$booking_datetime_in = date("Y-m-d H:i:s", strtotime($booking_date_in." ".$booking_time_in.":00"));
		$booking_create_date = date("Y-m-d H:i:s");
		
		if ($booking_id == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input ID Booking !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_destination == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Tujuan/Destination !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_armada_type == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Type Armada !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_vehicle == "")
		{
			
			$callback['error'] = true;
			$callback['message'] = "Please Input Vehicle !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_driver == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Driver !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_cbm_loading == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input CBM Loading !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_date_in == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Tanggal Masuk Gudang!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_time_in == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Jam Masuk Gudang !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_warehouse == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Tujuan Gudang !";
		
			echo json_encode($callback);
			return;
		}
                
                
                //Compare Typearmada & CBM Loading
                $this->dbtransporter->where("typearmada_status",1);
                $this->dbtransporter->where("typearmada_id",$booking_armada_type);
                $this->dbtransporter->limit(1);
                $qt = $this->dbtransporter->get("typearmada");
                $rt = $qt->row();
                
                $myvolume = $rt->typearmada_volume;
                
                if ($booking_cbm_loading > $myvolume)
                {
                    $callback['error'] = true;
                    $callback['message'] = "Type Armada Tidak Sesuai !";
                    
                    echo json_encode($callback);
                    return;
                }
                
		//Cek apakah No ID Booking Sudah ada
		//Jika Ada maka proses di stop
		$this->dbtransporter->where('booking_id',$booking_id);
		$this->dbtransporter->where('booking_company',$my_company);
		$this->dbtransporter->limit(1);
		$qid = $this->dbtransporter->get('id_booking');
		$rid = $qid->row();
		$cid = count($rid);
		if ($cid > 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Duplicate ID Booking, Please Check Your ID Booking !";
		
			echo json_encode($callback);
			return;
		}
	
		$error = "";
		unset($data);
	
		$data['booking_id'] = $booking_id;
		$data['booking_company'] = $booking_company;
		$data['booking_destination'] = $booking_destination; 
		$data['booking_armada_type'] = $booking_armada_type; 
		$data['booking_cbm_loading'] = $booking_cbm_loading;
		$data['booking_vehicle'] = $booking_vehicle;
		$data['booking_driver'] = $booking_driver;
		$data['booking_date_in'] = $booking_date_in_fmt;
		$data['booking_time_in'] = $booking_time_in;
		$data['booking_datetime_in'] = $booking_datetime_in;
		$data['booking_warehouse'] = $booking_warehouse;
		$data['booking_create_date'] = $booking_create_date;
		$data['booking_notes'] = $booking_notes;
		$data['booking_dbtype'] = $booking_dbtype;
	
		//Change Vehicle Images
		unset ($dataimage);
		$dataimage["vehicle_image"] = "car_hijau";
		$dataimage["vehicle_group"] = 422;
		$this->db->where("vehicle_device", $booking_vehicle);
		$this->db->where("vehicle_user_id",$this->sess->user_id); //Khusus Mobil Masing2
		$this->db->update("vehicle",$dataimage);
		
		//Insert to table transporter_id_booking
		$this->dbtransporter->insert("id_booking", $data);
		
		//Update Table Driver
		//*******************
		$this->db->where("vehicle_device", $booking_vehicle);
		$qdr = $this->db->get("vehicle");
		$rdr = $qdr->row();
		
		unset($datadriver);
		$datadriver["driver_vehicle"] = $rdr->vehicle_id;
		
		$this->dbtransporter->where("driver_id",$booking_driver);
		$this->dbtransporter->update("driver",$datadriver);
		//********************
		//Finish Update Driver
		
		//Update data barcode
		unset($databarcode);
		$databarcode["transporter_barcode_status"] = 2;
		
		$this->dbtransporter->where("transporter_barcode",$booking_id);
		$this->dbtransporter->update("tupper_barcode",$databarcode);
		//*******************
		
		$callback["error"] = false;
		$callback["message"] = "Add ID Booking Success";
		$callback["redirect"] = base_url()."transporter/tupperware/booking_id";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function id_booking_edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("id_booking");
		$row = $q->row();
		
		$driver = $this->get_driver();
		$vehicle = $this->get_vehicle();
		$timecontrol = $this->get_timecontrol();
                $typearmada = $this->get_typearmada();
		
                $params["typearmada"] = $typearmada;
		$params["driver"] = $driver;
		$params["vehicle"] = $vehicle;
		$params["timecontrol"] = $timecontrol;
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/edit_id_booking', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
	
	function saveedit_id_booking()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$my_company = $this->sess->user_company;
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		
		$booking_id_bf = isset($_POST['booking_id_bf']) ? $_POST['booking_id_bf'] : "";
		$booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : "";
		$booking_company = $my_company;
		$booking_destination = isset($_POST['booking_destination']) ? $_POST['booking_destination'] : "";
		$booking_armada_type = isset($_POST['booking_armada_type']) ? $_POST['booking_armada_type'] : "";
		$booking_cbm_loading = isset($_POST['booking_cbm_loading']) ? $_POST['booking_cbm_loading'] : "";
		$booking_vehicle = isset($_POST['booking_vehicle']) ? $_POST['booking_vehicle'] : 0;
		$booking_vehicle_bf = isset($_POST['booking_vehicle_bf']) ? $_POST['booking_vehicle_bf'] : 0;
		$booking_driver = isset($_POST['booking_driver']) ? $_POST['booking_driver'] : 0;
		$booking_driver_bf = isset($_POST['booking_driver_bf']) ? $_POST['booking_driver_bf'] : 0;
		$booking_date_in = isset($_POST['booking_date_in']) ? $_POST['booking_date_in'] : "";
		$booking_time_in = isset($_POST['booking_time_in']) ? $_POST['booking_time_in'] : "";
		$booking_warehouse = isset($_POST['booking_warehouse']) ? $_POST['booking_warehouse'] : "";
		$booking_notes = isset($_POST['booking_notes']) ? $_POST['booking_notes'] : "";
		
		$booking_date_in_fmt  = date("Y-m-d", strtotime($booking_date_in));
		$booking_datetime_in = date("Y-m-d H:i:s", strtotime($booking_date_in." ".$booking_time_in.":00"));
		$booking_create_date = date("Y-m-d H:i:s");
		
		if ($booking_id == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input ID Booking !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_destination == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Tujuan/Destination !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_armada_type == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Type Armada !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_vehicle == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Vehicle !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_driver == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Driver !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_cbm_loading == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input CBM Loading !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_date_in == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Tanggal Masuk Gudang!";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_time_in == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Jam Masuk Gudang !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($booking_warehouse == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Tujuan Gudang !";
		
			echo json_encode($callback);
			return;
		}
                
                //Compare Typearmada & CBM Loading
                $this->dbtransporter->where("typearmada_status",1);
                $this->dbtransporter->where("typearmada_id",$booking_armada_type);
                $this->dbtransporter->limit(1);
                $qt = $this->dbtransporter->get("typearmada");
                $rt = $qt->row();
                
                $myvolume = $rt->typearmada_volume;
                
                if ($booking_cbm_loading > $myvolume)
                {
                    $callback['error'] = true;
                    $callback['message'] = "Type Armada Tidak Sesuai !";
                    
                    echo json_encode($callback);
                    return;
                }
		
		//Ganti vehicle
		if ($booking_vehicle_bf != $booking_vehicle)
		{
			if ($booking_notes == "")
			{
				$callback['error'] = true;
				$callback['message'] = "Terjadi Perubahan Vehicle/Kendaraam, Please Input Notes !";
		
				echo json_encode($callback);
				return;
			}
			
			unset($dataoldvehicle);
			unset($datavehicle);
			
			$dataoldvehicle["vehicle_image"] = "car";
			$dataoldvehicle["vehicle_group"] = 0;
			
			$datavehicle["vehicle_image"] = "car_hijau";
			$datavehicle["vehicle_group"] = 422; //422 group Tupperware
			
			$this->db->where("vehicle_device",$booking_vehicle_bf);
			$this->db->update("vehicle",$dataoldvehicle);
			
			$this->db->where("vehicle_device",$booking_vehicle);
			$this->db->update("vehicle",$datavehicle);
		}
		
		//Ganti Driver
		if ($booking_driver_bf != $booking_driver)
		{
			$this->db->where("vehicle_device", $booking_vehicle);
			$qdr = $this->db->get("vehicle");
			$rdr = $qdr->row();
		
			unset($datadriver);
			$datadriver["driver_vehicle"] = $rdr->vehicle_id;
		
			$this->dbtransporter->where("driver_id",$booking_driver);
			$this->dbtransporter->update("driver",$datadriver);
		}
		
		//Jika ada perubahan ID Booking, Cek apakah No ID Booking Sudah ada
		if ($booking_id_bf != $booking_id)
		{
			$this->dbtransporter->where('booking_id',$booking_id);
			$this->dbtransporter->where('booking_company',$my_company);
			$this->dbtransporter->limit(1);
			$qid = $this->dbtransporter->get('id_booking');
			$rid = $qid->row();
			$cid = count($rid);
			if ($cid > 0)
			{
				$callback['error'] = true;
				$callback['message'] = "Duplicate ID Booking, Please Check Your ID Booking !";
		
				echo json_encode($callback);
				return;
			}
		}
		
		$error = "";
		unset($data);
	
		$data['booking_id'] = $booking_id;
		$data['booking_company'] = $booking_company;
		$data['booking_destination'] = $booking_destination; 
		$data['booking_armada_type'] = $booking_armada_type; 
		$data['booking_cbm_loading'] = $booking_cbm_loading;
		$data['booking_vehicle'] = $booking_vehicle;
		$data['booking_driver'] = $booking_driver;
		$data['booking_date_in'] = $booking_date_in_fmt;
		$data['booking_time_in'] = $booking_time_in;
		$data['booking_datetime_in'] = $booking_datetime_in;
		$data['booking_warehouse'] = $booking_warehouse;
		$data['booking_create_date'] = $booking_create_date;
		$data['booking_notes'] = $booking_notes;
	
		//update
		$this->dbtransporter->where("id",$id);
		$this->dbtransporter->update("id_booking", $data);

		$callback["error"] = false;
		$callback["message"] = "Update ID Booking Success";
		$callback["redirect"] = base_url()."transporter/tupperware/booking_id";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	
	}
	
	function id_booking_detail()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("id_booking");
		$row = $q->row();
		
		if ($this->config->item("app_tupperware"))
		{
			$driver = $this->get_driver_transporter($row->booking_driver);
			$vehicle = $this->get_vehicle_transporter($row->booking_vehicle);
		}
		else
		{
			$driver = $this->get_driver();
			$vehicle = $this->get_vehicle();
		}
		
                $typearmada = $this->get_typearmada();
		$timecontrol = $this->get_timecontrol();
		
                $params["typearmada"] = $typearmada;
		$params["driver"] = $driver;
		$params["vehicle"] = $vehicle;
		$params["timecontrol"] = $timecontrol;
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/detail_id_booking', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
	}
	
	function id_so_detail_bond()
	{
		
		$id = $this->input->post('id');
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;		
		$this->dbtransporter->where("suratjalan_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("powerblock_suratjalan");
		$row = $q->row();

		
		
		$params["data"] = $row;
		$html = $this->load->view('mod_powerblock/detail_id_so_bond', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);

	}
	
	function id_so_detail2()
	{
		$id = $this->input->post('id');
		
		//$sj = explode(";", $id);
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;		
		$this->dbtransporter->where("suratjalan_id", $id);
		//$this->dbtransporter->or_where("suratjalan_sales_order_bond LIKE '%".$id."'",null);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("powerblock_suratjalan");
		$row = $q->row();

		/*
		if ($this->config->item("app_powerblock"))
		{
			$driver = $this->get_driver_transporter($row->booking_driver);
			$vehicle = $this->get_vehicle_transporter($row->booking_vehicle);
		}
		else
		{
			$driver = $this->get_driver();
			$vehicle = $this->get_vehicle();
		}
		
		$typearmada = $this->get_typearmada();
		$timecontrol = $this->get_timecontrol();
                
		$params["typearmada"] = $typearmada;
		$params["driver"] = $driver;
		$params["vehicle"] = $vehicle;
		$params["timecontrol"] = $timecontrol;
		*/
		
		$params["data"] = $row;
		$html = $this->load->view('mod_powerblock/detail_id_so', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
	}
	
	function delete_id_booking($id)
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		$data["booking_status"] = 2;
		
		$this->dbtransporter->where("id", $id);
		if($this->dbtransporter->update("id_booking", $data))
		{
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
	
	function get_driver_transporter($v)
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$nodata = 0;
		
		$this->dbtransporter->order_by('driver_name', 'asc');
		$this->dbtransporter->where('driver_id', $v);
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
	
	function get_timecontrol()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("time_status",1);
		$qtime = $this->dbtransporter->get("timecontrol");
		$rtime = $qtime->result();
		return $rtime;
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
	
	function get_vehicle_transporter($v)
	{
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $v);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		return $rv;
	}
	
	function get_destination()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("destination_name","asc");
		$this->dbtransporter->where("destination_status","1");
		$this->dbtransporter->where("destination_company", $my_company);
		$q = $this->dbtransporter->get("destination");
		$rows = $q->result();
		return $rows;
		
	}
	
	function mn_set_delivered()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("id_booking");
		$row = $q->row();
		
		$timecontrol = $this->get_timecontrol();
		
		$params["timecontrol"] = $timecontrol;
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/set_delivered', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
	}
	
	function mn_set_loading()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("id_booking");
		$row = $q->row();
		
		$timecontrol = $this->get_timecontrol();
		
		$params["timecontrol"] = $timecontrol;
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/set_loading', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
	}
	
	function set_delivered()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : "";
		$del_date = isset($_POST['set_delivered']) ? $_POST['set_delivered'] : "" ;
		$del_time = isset($_POST['delivered_time']) ? $_POST['delivered_time'] : "" ;
		
		if ($del_date == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Date Delivered !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($del_time == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Time Delivered !";
		
			echo json_encode($callback);
			return;
		}
		
		$dt = date("Y-m-d H:i:s",strtotime($del_date." ".$del_time.":00"));
		
		unset($data);
		$data["booking_delivery_status"] = 2;
		$data["booking_delivered_datetime"] = $dt;
		$data["booking_set_delivered_datetime"] = $dt;
		
		$this->dbtransporter->where("id",$id);
		$this->dbtransporter->limit(1);
		$this->dbtransporter->update("id_booking",$data);
		
		//Change Vehicle Images
		$this->dbtransporter->where("id",$id);
		$this->dbtransporter->limit(1);
		$mq = $this->dbtransporter->get("id_booking");
		$rq = $mq->row();
		
		//Cek Apakah Masih ada Pengiriman atau booking ID pada vehicle yang sama
		$this->dbtransporter->where("booking_vehicle",$rq->booking_vehicle);
		$this->dbtransporter->where("booking_delivery_status",1);
		$this->dbtransporter->where("booking_status",1);
		$idq = $this->dbtransporter->get("id_booking");
		$irq = $idq->result();
		$tirq = count($irq);
		if ($tirq > 0)
		{
		
		}
		else
		{
			unset ($dataimage);
			$dataimage["vehicle_image"] = "car";
			$dataimage["vehicle_group"] = 0;
			$this->db->where("vehicle_device", $rq->booking_vehicle);
			$this->db->update("vehicle",$dataimage);
		}
		
		$callback["error"] = false;
		$callback["message"] = "ID"." ".$booking_id." "."Set To Delivered, Success";
		$callback["redirect"] = base_url()."transporter/tupperware/booking_id";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	}
	
	function set_loading()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : "";
		$del_date = isset($_POST['set_loading']) ? $_POST['set_loading'] : "" ;
		$del_time = isset($_POST['loading_time']) ? $_POST['loading_time'] : "" ;
		
		if ($del_date == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Loading Date !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($del_time == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Loading Time !";
		
			echo json_encode($callback);
			return;
		}
		
		$dt = date("Y-m-d H:i:s",strtotime($del_date." ".$del_time.":00"));
		
		unset($data);
		$data["booking_loading"] = 1;
		$data["booking_loading_date"] = $dt;
		
		$this->dbtransporter->where("id",$id);
		$this->dbtransporter->limit(1);
		$this->dbtransporter->update("id_booking",$data);
		
		//Change Vehicle Images
		$this->dbtransporter->where("id",$id);
		$this->dbtransporter->limit(1);
		$mq = $this->dbtransporter->get("id_booking");
		$rq = $mq->row();
		
		unset ($dataimage);
		$dataimage["vehicle_image"] = "car_cokelat";
		$dataimage["vehicle_group"] = 422;
		$this->db->where("vehicle_device", $rq->booking_vehicle);
		$this->db->update("vehicle",$dataimage);
		
		$callback["error"] = false;
		$callback["message"] = "ID"." ".$booking_id." "."Set To Loading, Success";
		$callback["redirect"] = base_url()."transporter/tupperware/booking_id";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function get_all_vehicle()
	{
		$user_id = $this->sess->user_id;
		$user_company = $this->sess->user_company;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		$total = count($rows);
		
		if ($q->num_rows > 0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$booking_vehicle[] = $rows[$i]->booking_vehicle;
			}
		}
		
		$this->db->order_by("vehicle_no", "asc");
		if (isset($booking_vehicle))
		{
			$this->db->where_in("vehicle_device",$booking_vehicle);
		}
		$this->db->or_where("vehicle_user_id", $user_id);
		
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
		$this->db->where("vehicle_status <>", 3);
		
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		return $rv;
			
	}
	
	function get_all_driver()
	{
		$driver_company = $this->sess->user_company;
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		$total = count($rows);
		
		if ($q->num_rows > 0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$booking_driver[] = $rows[$i]->booking_driver;
			}
		}
		
		$nodata = 0;
		$this->dbtransporter->order_by('driver_name', 'asc');
		if (isset($booking_driver))
		{
			$this->dbtransporter->where_in('driver_id', $booking_driver);
		}
		$this->dbtransporter->or_where('driver_company', $driver_company);
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
	
	function get_all_company()
	{
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		$total = count($rows);
		
		if ($q->num_rows > 0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$booking_company[] = $rows[$i]->booking_company;
			}
		}
		
		$this->db->order_by("company_name","asc");
		if (isset($booking_company))
		{
			$this->db->where_in("company_id",$booking_company);
		}
		$this->db->or_where("company_id",$my_company);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
		
	}
	
	function mn_distributor()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/distributor_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function mn_type_armada()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/typearmada_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
    }
    
	function mn_barcode()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/upload_barcode", $this->params, true);
		$this->load->view("templatesess", $this->params);
    }
	
	function search_distributor()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "booking_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("dist_name","asc");
		
		switch($field)
		{
			case "dist_code":
				$this->dbtransporter->where("dist_code like", '%'.$keyword.'%');
			break;
			case "dist_name":
				$this->dbtransporter->where("dist_name like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("dist_status", 1);
		$q = $this->dbtransporter->get("dist_tupper", 50, $offset);
		$rows = $q->result();
		
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "dist_code":
				$this->dbtransporter->where("dist_code like", '%'.$keyword.'%');
			break;
			case "dist_name":
				$this->dbtransporter->where("dist_name like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("dist_status", 1);
		$qt = $this->dbtransporter->get("dist_tupper");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
			
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "ID Booking";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_tupperware/distributor_list_result", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
        function search_typearmada()
        {
        $field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "typearmada_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("typearmada_name","asc");
		
		switch($field)
		{
			case "typearmada_name":
				$this->dbtransporter->where("typearmada_name like", '%'.$keyword.'%');
			break;
			case "typearmada_volume":
				$this->dbtransporter->where("typearmada_volume like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("typearmada_status", 1);
		$q = $this->dbtransporter->get("typearmada", 50, $offset);
		$rows = $q->result();
		
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "typearmada_name":
				$this->dbtransporter->where("typearmada_name like", '%'.$keyword.'%');
			break;
			case "typearmada_volume":
				$this->dbtransporter->where("typearmada_volume like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("typearmada_status", 1);
		$qt = $this->dbtransporter->get("typearmada");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
			
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "Type Armada";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_tupperware/typearmada_list_result", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
        }
	
	function search_slcars()
	{
        $field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "slcars_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->order_by("slcars_id","desc");
		
		switch($field)
		{
			case "slcars_code":
				$this->dbtransporter->where("slcars_code like", '%'.$keyword.'%');
			break;
			case "slcars_name":
				$this->dbtransporter->where("slcars_name like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("slcars_status", 1);
		$q = $this->dbtransporter->get("tupper_slcars", 50, $offset);
		$rows = $q->result();
		
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "slcars_code":
				$this->dbtransporter->where("slcars_code like", '%'.$keyword.'%');
			break;
			case "slcars_name":
				$this->dbtransporter->where("slcars_name like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("slcars_status", 1);
		$qt = $this->dbtransporter->get("tupper_slcars");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
			
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "Data Transporter";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_tupperware/slcars_listresult", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
    }
        
	function distributor_edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("dist_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("dist_tupper");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/edit_distributor', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
		
	}
        
        function typearmada_edit()
        {
                $id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("typearmada_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("typearmada");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/edit_typearmada', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
        }
        
	function saveedit_distributor()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		
		$dist_code_bf = isset($_POST['dist_code_bf']) ? $_POST['dist_code_bf'] : "";
		$dist_code = isset($_POST['dist_code']) ? $_POST['dist_code'] : "";
		$dist_name = isset($_POST['dist_name']) ? $_POST['dist_name'] : "";
		$dist_username = isset($_POST['dist_username']) ? $_POST['dist_username'] : "";
		$dist_username_bf = isset($_POST['dist_username_bf']) ? $_POST['dist_username_bf'] : "";
		$dist_password = isset($_POST['dist_password']) ? $_POST['dist_password'] : "";
		$dist_email = isset($_POST['dist_email']) ? $_POST['dist_email'] : "";
		$dist_wh_coverage = isset($_POST['dist_wh_coverage']) ? $_POST['dist_wh_coverage'] : "";
		$dist_leadday_wh_origin = isset($_POST['dist_leadday_wh_origin']) ? $_POST['dist_leadday_wh_origin'] : "";
		$dist_leadday_wh_jkt = isset($_POST['dist_leadday_wh_jkt']) ? $_POST['dist_leadday_wh_jkt'] : "";
		$dist_leadday_wh_medan = isset($_POST['dist_leadday_wh_medan']) ? $_POST['dist_leadday_wh_medan'] : "";
		$dist_leadday_wh_sby = isset($_POST['dist_leadday_wh_sby']) ? $_POST['dist_leadday_wh_sby'] : "";
		$dist_customer_type = isset($_POST['dist_customer_type']) ? $_POST['dist_customer_type'] : "";
		$dist_schedule = isset($_POST['dist_schedule']) ? $_POST['dist_schedule'] : "";
		$dist_ship_zone = isset($_POST['dist_ship_zone']) ? $_POST['dist_ship_zone'] : "";
		$dist_schedule_priority = isset($_POST['dist_schedule_priority']) ? $_POST['dist_schedule_priority'] : "";
		
		
		if ($dist_code == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Distributor Code !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($dist_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Distributor Name !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($dist_username!="")
		{
			if ($dist_username != $dist_username_bf)
			{
				$this->dbtransporter->where("dist_username",$dist_username);
				$qu = $this->dbtransporter->get('dist_tupper');
				$ru = $qu->row();
				$tu = count($ru);
				if ($tu > 0)
				{
					$callback['error'] = true;
					$callback['message'] = "Username Already Exist, Please Check Your Username !";
		
					echo json_encode($callback);
					return;
				}
			}
		}
		
		//Ganti Distributor Code Code
		if ($dist_code_bf != $dist_code)
		{
			$this->dbtransporter->where('dist_code',$dist_code);
			$this->dbtransporter->limit(1);
			$qid = $this->dbtransporter->get('dist_tupper');
			$rid = $qid->row();
			$cid = count($rid);
			if ($cid > 0)
			{
				$callback['error'] = true;
				$callback['message'] = "Duplicate Customer Code, Please Check Your Customer Code !";
		
				echo json_encode($callback);
				return;
			}
		}
		
		$error = "";
		unset($data);
	
		$data['dist_code'] = $dist_code;
		$data['dist_name'] = $dist_name;
		$data['dist_username'] = $dist_username;
		$data['dist_password'] = $dist_password;
		$data['dist_email'] = $dist_email;
		$data['dist_wh_coverage'] = $dist_wh_coverage;
		$data['dist_leadday_wh_origin'] = $dist_leadday_wh_origin;
		$data['dist_leadday_wh_jkt'] = $dist_leadday_wh_jkt;
		$data['dist_leadday_wh_medan'] = $dist_leadday_wh_medan;
		$data['dist_leadday_wh_sby'] = $dist_leadday_wh_sby;
		$data['dist_customer_type'] = $dist_customer_type;
		$data['dist_schedule'] = $dist_schedule;
		$data['dist_ship_zone'] = $dist_ship_zone;
		$data['dist_schedule_priority'] = $dist_schedule_priority;
	
		//update
		$this->dbtransporter->where("dist_id",$id);
		$this->dbtransporter->update("dist_tupper", $data);

		$callback["error"] = false;
		$callback["message"] = "Update Distributor Success";
		$callback["redirect"] = base_url()."transporter/tupperware/mn_distributor";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
		
	
	}
	
        function saveedit_typearmada()
        {
            if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
                $typearmada_id = isset($_POST['id']) ? $_POST['id'] : "";
		$typearmada_name = isset($_POST['typearmada_name']) ? $_POST['typearmada_name'] : "";
                $typearmada_description = isset($_POST['typearmada_description']) ? $_POST['typearmada_description'] : "";
		$typearmada_volume = isset($_POST['typearmada_volume']) ? $_POST['typearmada_volume'] : "";
		
		if ($typearmada_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Type Armada !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($typearmada_volume == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Volume !";
		
			echo json_encode($callback);
			return;
		}
		
		$error = "";
		unset($data);
	
		$data['typearmada_name'] = $typearmada_name;
                $data['typearmada_description'] = $typearmada_description;
		$data['typearmada_volume'] = $typearmada_volume;
		
                $this->dbtransporter->where("typearmada_id",$typearmada_id);
		$this->dbtransporter->update("typearmada", $data);

		$callback["error"] = false;
		$callback["message"] = "Update Data Success";
		$callback["redirect"] = base_url()."transporter/tupperware/mn_type_armada";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
        }
        
	function distributor_detail()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("dist_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("dist_tupper");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/detail_distributor', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
	}
	
        function typearmada_detail()
        {
                $id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("typearmada_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("typearmada");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/detail_typearmada', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
        }
        
	function delete_distributor($id)
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		$data["dist_status"] = 2;
		
		$this->dbtransporter->where("dist_id", $id);
		if($this->dbtransporter->update("dist_tupper", $data))
		{
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
	
        function delete_typearmada($id)
        {
                $this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		$data["typearmada_status"] = 2;
		
		$this->dbtransporter->where("typearmada_id", $id);
		if($this->dbtransporter->update("typearmada", $data))
		{
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
        
	function delete_slcars($id)
	{
        $this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		
		$this->dbtransporter->where("slcars_id", $id);
		if($this->dbtransporter->delete("tupper_slcars"))
		{
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
        
		
	function add_distributor()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->params["title"] = "Distributor - ADD";		
		$this->params['content'] = $this->load->view("mod_tupperware/add_distributor", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
        function add_typearmada()
        {
            if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->params["title"] = "Type Armada - ADD";		
		$this->params['content'] = $this->load->view("mod_tupperware/add_typearmada", $this->params, true);
		$this->load->view("templatesess", $this->params);
        }
    
	function add_slcars()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		//Get User Trans Tupper
		$this->db->where("user_trans_tupper",1);
		$q = $this->db->get("user");
		$trans = $q->result();
		
		$this->params["title"] = "Data Transporter - ADD";		
		$this->params["trans"] = $trans;
		$this->params['content'] = $this->load->view("mod_tupperware/add_slcars", $this->params, true);
		$this->load->view("templatesess", $this->params);
    }
		
	function save_distributor() 
	{
	
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$dist_code = isset($_POST['dist_code']) ? $_POST['dist_code'] : "";
		$dist_name = isset($_POST['dist_name']) ? $_POST['dist_name'] : "";
		$dist_username = isset($_POST['dist_username']) ? $_POST['dist_username'] : "";
		$dist_password = isset($_POST['dist_password']) ? $_POST['dist_password'] : "";
		
		$dist_wh_coverage = isset($_POST['dist_wh_coverage']) ? $_POST['dist_wh_coverage'] : "";
		$dist_leadday_wh_origin = isset($_POST['dist_leadday_wh_origin']) ? $_POST['dist_leadday_wh_origin'] : "";
		$dist_leadday_wh_jkt = isset($_POST['dist_leadday_wh_jkt']) ? $_POST['dist_leadday_wh_jkt'] : "";
		$dist_leadday_wh_medan = isset($_POST['dist_leadday_wh_medan']) ? $_POST['dist_leadday_wh_medan'] : "";
		$dist_leadday_wh_sby = isset($_POST['dist_leadday_wh_sby']) ? $_POST['dist_leadday_wh_sby'] : "";
		$dist_customer_type = isset($_POST['dist_customer_type']) ? $_POST['dist_customer_type'] : "";
		$dist_schedule = isset($_POST['dist_schedule']) ? $_POST['dist_schedule'] : "";
		$dist_ship_zone = isset($_POST['dist_ship_zone']) ? $_POST['dist_ship_zone'] : "";
		$dist_schedule_priority = isset($_POST['dist_schedule_priority']) ? $_POST['dist_schedule_priority'] : "";
		
		if ($dist_code == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Distributor Code !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($dist_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Distributor Name !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($dist_username!="")
		{
			$this->dbtransporter->where("dist_username",$dist_username);
			$qu = $this->dbtransporter->get('dist_tupper');
			$ru = $qu->row();
			$tu = count($ru);
			if ($tu > 0)
			{
				$callback['error'] = true;
				$callback['message'] = "Username Already Exist, Please Check Your Username !";
		
				echo json_encode($callback);
				return;
			}
		}
		
		//Cek apakah No Distributor Code Sudah ada
		//Jika Ada maka proses di stop
		$this->dbtransporter->where('dist_code',$dist_code);
		$this->dbtransporter->limit(1);
		$qid = $this->dbtransporter->get('dist_tupper');
		$rid = $qid->row();
		$cid = count($rid);
		if ($cid > 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Duplicate Distributor Code, Please Check Your Distributor !";
		
			echo json_encode($callback);
			return;
		}
	
		$error = "";
		unset($data);
	
		$data['dist_code'] = $dist_code;
		$data['dist_name'] = $dist_name;
		$data['dist_username'] = $dist_username;
		$data['dist_password'] = $dist_password;
		
		$data['dist_wh_coverage'] = $dist_wh_coverage;
		$data['dist_leadday_wh_origin'] = $dist_leadday_wh_origin;
		$data['dist_leadday_wh_jkt'] = $dist_leadday_wh_jkt;
		$data['dist_leadday_wh_medan'] = $dist_leadday_wh_medan;
		$data['dist_leadday_wh_sby'] = $dist_leadday_wh_sby;
		$data['dist_customer_type'] = $dist_customer_type;
		$data['dist_schedule'] = $dist_schedule;
		$data['dist_ship_zone'] = $dist_ship_zone;
		$data['dist_schedule_priority'] = $dist_schedule_priority;
		
		//Insert to table transporter_id_booking
		$this->dbtransporter->insert("dist_tupper", $data);

		$callback["error"] = false;
		$callback["message"] = "Add Distributor Success";
		$callback["redirect"] = base_url()."transporter/tupperware/mn_distributor";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
        function save_typearmada()
        {
            if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$typearmada_name = isset($_POST['typearmada_name']) ? $_POST['typearmada_name'] : "";
                $typearmada_description = isset($_POST['typearmada_description']) ? $_POST['typearmada_description'] : "";
		$typearmada_volume = isset($_POST['typearmada_volume']) ? $_POST['typearmada_volume'] : "";
		
		if ($typearmada_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Type Armada !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($typearmada_volume == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Volume !";
		
			echo json_encode($callback);
			return;
		}
		
		$error = "";
		unset($data);
	
		$data['typearmada_name'] = $typearmada_name;
                $data['typearmada_description'] = $typearmada_description;
		$data['typearmada_volume'] = $typearmada_volume;
		
		$this->dbtransporter->insert("typearmada", $data);

		$callback["error"] = false;
		$callback["message"] = "Add Data Success";
		$callback["redirect"] = base_url()."transporter/tupperware/mn_type_armada";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
        }
    
	
	function save_slcars()
	{
        if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$slcars_lacak_code = isset($_POST['slcars_lacak_code']) ? $_POST['slcars_lacak_code'] : "";
		$slcars_name = isset($_POST['slcars_name']) ? $_POST['slcars_name'] : "";
		$slcars_code = isset($_POST['slcars_code']) ? $_POST['slcars_code'] : "";
		
		if ($slcars_lacak_code == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Transporter Lacak-Mobil Code !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($slcars_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Transporter Name !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($slcars_code == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input SLCARS Code !";
		
			echo json_encode($callback);
			return;
		}
		
		//Cek Apakah SLCARS sudah Ada
		$this->dbtransporter->where("slcars_lacak_code", $slcars_lacak_code);
		$this->dbtransporter->or_where("slcars_code", $slcars_code);
		$q = $this->dbtransporter->get("tupper_slcars");
		$row = $q->row();
		if (count($row)>0)
		{
			$callback['error'] = true;
			$callback['message'] = "Lacak-Mobil Code/SLCARS Sudah Ada !";
		
			echo json_encode($callback);
			return;
		}
		
		$error = "";
		unset($data);
	
		$data['slcars_lacak_code'] = $slcars_lacak_code;
		$data['slcars_name'] = $slcars_name;
		$data['slcars_code'] = $slcars_code;
		
		$this->dbtransporter->insert("tupper_slcars", $data);

		$callback["error"] = false;
		$callback["message"] = "Add Data Success";
		$callback["redirect"] = base_url()."transporter/tupperware/mn_slcars";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
    }
	
	function slcars_edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("slcars_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("tupper_slcars");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('mod_tupperware/edit_slcars', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
    }
	
	function saveedit_slcars()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
        $slcars_id = isset($_POST['slcars_id']) ? $_POST['slcars_id'] : "";
		$slcars_name = isset($_POST['slcars_name']) ? $_POST['slcars_name'] : "";
		$slcars_code = isset($_POST['slcars_code']) ? $_POST['slcars_code'] : "";
		$slcars_code_bf = isset($_POST['slcars_code_bf']) ? $_POST['slcars_code_bf'] : "";
		
		if ($slcars_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Transporter Name !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($slcars_code == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input SLCARS Code !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($slcars_code_bf != $slcars_code)
		{
			//Cek Apakah SLCARS sudah Ada
			$this->dbtransporter->where("slcars_code", $slcars_code);
			$q = $this->dbtransporter->get("tupper_slcars");
			$row = $q->row();
			if (count($row)>0)
			{
				$callback['error'] = true;
				$callback['message'] = "Lacak-Mobil Code/SLCARS Sudah Ada !";
		
				echo json_encode($callback);
				return;
			}
		}
		
		$error = "";
		unset($data);
		$data['slcars_name'] = $slcars_name;
		$data['slcars_code'] = $slcars_code;
		
		$this->dbtransporter->where("slcars_id",$slcars_id);
		$this->dbtransporter->update("tupper_slcars",$data);
		
		$callback["error"] = false;
		$callback["message"] = "Update Data Success";
		$callback["redirect"] = base_url()."transporter/tupperware/mn_slcars";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
        }
        
        function get_typearmada()
        {
            $this->dbtransporter = $this->load->database('transporter', true);
            $this->dbtransporter->where("typearmada_status",1);
            $q = $this->dbtransporter->get("typearmada");
            $rows = $q->result();
            return $rows;
        }
		
	function upload_barcode()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		 
		$config['upload_path'] = './temp_upload/';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$this->params['error'] = $this->upload->display_errors();
			$this->params['content'] = $this->load->view("mod_tupperware/upload_error", $this->params, true);
			$this->load->view("templatesess", $this->params);
		}
		else
		{
			$data = $this->upload->data();
			$file_name = $data["file_name"];
			
			$config['upload_path'] = './temp_upload/';
			$config['allowed_types'] = 'csv';
			
			$this->load->library('csvreader');
			$filePath = './temp_upload/'.$file_name;
			
			$datacsv = $this->csvreader->parse_file($filePath);
			$total_data  = count($datacsv);
			
			foreach ($datacsv as $v=>$key)
			{
				unset($insert_csv);
				$insert_csv["transporter_barcode"] = $key["BarCode_No"];
				$insert_csv["transporter_barcode_schedule_date"] = date("Y-m-d",strtotime($key["Schedule_Date"]));
				$insert_csv["transporter_barcode_time"] = $key["Time"];
				$insert_csv["transporter_barcode_wh"] = $key["WH"];
				$insert_csv["transporter_barcode_db_type"] = $key["DB_Type"];
				$insert_csv["transporter_barcode_destination"] = $key["Destination"];
				$insert_csv["transporter_barcode_slcars"] = $key["SLCARS"];
				$insert_csv["transporter_barcode_expedition_name"] = $key["Expedition_Name"];
				$insert_csv["transporter_barcode_fleet_type"] = $key["Fleet_Type"];
				$insert_csv["transporter_barcode_fleet_cbm"] = $key["Fleet_CBM"];
				
				$this->dbtransporter->where("transporter_barcode",$key["BarCode_No"]);
				$q = $this->dbtransporter->get("tupper_barcode");
				$row = $q->row();
				
				if (count($row)>0)
				{
					$this->params['error'] = "Error !, Duplicate Barcode No. ";
					$this->params['content'] = $this->load->view("mod_tupperware/upload_error", $this->params, true);
					$this->load->view("templatesess", $this->params);
					return;
				}
				
				$this->dbtransporter->insert("tupper_barcode",$insert_csv);
				
				$this->params['content'] = $this->load->view("mod_tupperware/upload_success", $this->params, true);
				$this->load->view("templatesess", $this->params);
			}
		}

	}
	
	function barcode_list()
	{
		$this->params['content'] = $this->load->view("mod_tupperware/barcode_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function search_barcode_list()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "transporter_barcode_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->order_by("transporter_barcode_id","desc");
		
		switch($field)
		{
			case "transporter_barcode":
				$this->dbtransporter->where("transporter_barcode like", '%'.$keyword.'%');
			break;
			case "transporter_barcode_slcars":
				$this->dbtransporter->where("transporter_barcode_slcars like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("transporter_barcode_status", 1);
		$q = $this->dbtransporter->get("tupper_barcode", 50, $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "transporter_barcode":
				$this->dbtransporter->where("transporter_barcode like", '%'.$keyword.'%');
			break;
			case "transporter_barcode_slcars":
				$this->dbtransporter->where("transporter_barcode_slcars like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("transporter_barcode_status", 1);
		$qt = $this->dbtransporter->get("tupper_barcode");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
			
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "Barcode List";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_tupperware/barcode_listresult", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function get_slcars()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("slcars_status",1);
		$this->dbtransporter->where("slcars_lacak_code",$this->sess->user_id);
		$q = $this->dbtransporter->get("tupper_slcars");
		$row = $q->row();
		
		//Get Barcode
		if (isset($row->slcars_code))
		{
			$this->dbtransporter->order_by("transporter_barcode_id","desc");
			$this->dbtransporter->where("transporter_barcode_slcars",$row->slcars_code);
			$this->dbtransporter->where("transporter_barcode_status",1);
			$q = $this->dbtransporter->get("tupper_barcode");
			$rows = $q->result();
		
			return $rows;
		}
		
	}
	
	function barcode_options()
	{
		//Barcode ID
		$booking_id = isset($_POST['booking_id']) ? trim($_POST['booking_id']) : "";
		
		if (! $booking_id)
		{
			$callback['empty'] = true;
			echo json_encode($callback);

			return;
		}
		
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->where("transporter_barcode_status",1);
		$this->dbtransporter->where("transporter_barcode",$booking_id);
		$q = $this->dbtransporter->get("tupper_barcode");
		
		if ($q->num_rows() == 0)
		{
			$callback['empty'] = true;
			echo json_encode($callback);

			return;
		}
		
		$driver = $this->get_driver();
		$vehicle = $this->get_vehicle();
		$timecontrol = $this->get_timecontrol();
        $typearmada = $this->get_typearmada();
		
		$params["typearmada"] = $typearmada;
		$params["driver"] = $driver;
		$params["vehicle"] = $vehicle;
		$params["timecontrol"] = $timecontrol;
		$params['data'] = $q->row();
		$html = $this->load->view("mod_tupperware/barcodedetail", $params, true);

		$callback['empty'] = false;
		$callback['html'] = $html;
	
		echo json_encode($callback);
	}

	function get_barcode()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->order_by("booking_id","asc");
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		return $rows;
		
	}

	function downloadbarcode()
	{
		$barcode = isset($_POST['barcode']) ? $_POST['barcode'] : "";
		$sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
		$edate = isset($_POST['edate']) ? $_POST['edate'] : "";
		
		if ($sdate != "")
		{
			$startdate = date("Y-m-d",strtotime($sdate));
		}
		
		if ($edate != "")
		{
			$enddate = date("Y-m-d",strtotime($edate));
		}
		
		$this->dbtransporter = $this->load->database("transporter",true);
		$this->dbtransporter->join("driver","driver_id = booking_driver","left_outer");
		$this->dbtransporter->join("typearmada","typearmada_id = booking_armada_type","left_outer");
		$this->dbtransporter->join("tupper_barcode","transporter_barcode = booking_id","left_outer");
		$this->dbtransporter->where("booking_id",$barcode);
		
		
		if (isset($startdate))
		{
			$this->dbtransporter->where("booking_date_in >=",$startdate);
		}
		
		if (isset($enddate))
		{
			$this->dbtransporter->where("booking_date_in >=",$enddate);
		}
		
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		
		header("Content-type: application/vnd.ms-excel");
		if (isset($startdate))
		{
			header("Content-Disposition: attachment; filename=\"barcode_".$startdate.".csv\"");
		}
		else
		{
			header("Content-Disposition: attachment; filename=\"barcode_".$barcode.".csv\"");
		}
		
		echo "\"BarCode_No\"";
		echo ",";
		echo "\"Schedule_Date\"";
		echo ",";
		echo "\"Time\"";
		echo ",";
		echo "\"WH\"";
		echo ",";
		echo "\"DB_Type\"";
		echo ",";
		echo "\"Destination\"";
		echo ",";
		echo "\"SLCARS\"";
		echo ",";
		echo "\"Expedition_Name\"";
		echo ",";
		echo "\"Fleet_Type\"";
		echo ",";
		echo "\"Fleet_CBM\"";
		echo ",";
		echo "\"Vehicle\"";
		echo ",";
		echo "\"Driver\"";
		echo ",";
		echo "\"Driver_Mobile\"";
		echo "\r\n";		
		
		for($i=0; $i < count($rows); $i++)
		{
			//get vehicle 
			$this->db->where("vehicle_device",$rows[$i]->booking_vehicle);
			$this->db->limit(1);
			$v = $this->db->get("vehicle");
			$rv = $v->row();
			
			if (isset($rv->vehicle_no))
			{
				$a = $rv->vehicle_no;
			}
			else
			{
				$a = "-";
			}
			
			echo "\"".$rows[$i]->booking_id."\"";
			echo ",";
			echo "\"".date("d/m/Y",strtotime($rows[$i]->booking_date_in))."\"";
			echo ",";
			echo "\"".$rows[$i]->booking_time_in.":00"."\"";
			echo ",";
			echo "\"".$rows[$i]->booking_warehouse."\"";
			echo ",";
			echo "\"".$rows[$i]->booking_dbtype."\"";
			echo ",";
			echo "\"".$rows[$i]->booking_destination."\"";
			echo ",";
			echo "\"".$rows[$i]->transporter_barcode_slcars."\"";
			echo ",";
			echo "\"".$rows[$i]->transporter_barcode_expedition_name."\"";
			echo ",";
			echo "\"".$rows[$i]->typearmada_name."\"";
			echo ",";
			echo "\"".$rows[$i]->booking_cbm_loading."\"";
			echo ",";
			echo "\"".$a."\"";
			echo ",";
			echo "\"".$rows[$i]->driver_name."\"";
			echo ",";
			if (isset($rows[$i]->driver_mobile))
			{
				echo "\"".$rows[$i]->driver_mobile."\"";
			}
			else
			{
				echo "\"-"."\"";
			}
			echo "\r\n";
		}
		
		return;
	}
	
	function delete_upload_barcode($id)
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		$data["transporter_barcode_status"] = 2;
		
		$this->dbtransporter->where("transporter_barcode_id", $id);
		if($this->dbtransporter->update("tupper_barcode",$data))
		{
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
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */