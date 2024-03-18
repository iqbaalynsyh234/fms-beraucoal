<?php
include "base.php";

class Pgn_carpool extends Base {
	
		var $period1;
		var $period2;
		var $tblhist;
		var $tblinfohist;
		var $otherdb;
	
	function Pgn_carpool()
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
	
	function mn_request()
	{
		$this->params['content'] = $this->load->view("mod_pgn_carpooling/request_list", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function search_car_request()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "request_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->order_by("request_id","desc");
		
		switch($field)
		{
			case "request_id":
				$this->dbtransporter->where("request_id like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("request_status", 1);
		$q = $this->dbtransporter->get("pgn_car_request", 50, $offset);
		$rows = $q->result();
		
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "request_id":
				$this->dbtransporter->where("request_id like", '%'.$keyword.'%');
			break;
		}
		
		$this->dbtransporter->where("request_status", 1);
		$qt = $this->dbtransporter->get("pgn_car_request");
		$rt = $qt->row();
		$total = $rt->total;
		
		//Get Vehicle
		$this->dbtransporter->where("pgn_vehicle_status",1);
		$qv = $this->dbtransporter->get("pgn_vehicle");
		$vehicle = $qv->result();
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
			
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["title"] = "Car Request";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["vehicle"] = $vehicle;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_pgn_carpooling/request_list_result", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function add_request()
	{
		if (! isset($this->sess->user_id)) 
		{
			redirect(base_url());
		}
		//Get Standby Vehilce
		$this->dbtrans = $this->load->database('transporter', true);
		$this->dbtrans->where("pgn_vehicle_status",1);
		$this->dbtrans->where("pgn_vehicle_book_status",0);
		$q = $this->dbtrans->get("pgn_vehicle");
		$vehicle = $q->result();
		$this->params["title"] = "Car Request - ADD";		
		$this->params["myvehicle"] = $vehicle;
		$this->params['content'] = $this->load->view("mod_pgn_carpooling/add_request", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save_request() 
	{
	
		if (! isset($this->sess->user_id)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		$request_nik = isset($_POST['request_nik']) ? $_POST['request_nik'] : "";
		$request_name = isset($_POST['request_name']) ? $_POST['request_name'] : "";
		$request_date = isset($_POST['request_date']) ? $_POST['request_date'] : "";
		$request_passenger = isset($_POST['request_passenger']) ? $_POST['request_passenger'] : "";
		$request_time = isset($_POST['request_time']) ? $_POST['request_time'] : "";
		$request_destination = isset($_POST['request_destination']) ? $_POST['request_destination'] : "";
		$request_note = isset($_POST['request_note']) ? $_POST['request_note'] : "";
		
		if ($request_nik == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input User NIk !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Name !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_date == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Date !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_time == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Time !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_destination == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Destination !";
		
			echo json_encode($callback);
			return;
		}
		
		$error = "";
		unset($data);
	
		$data['request_nik'] = $request_nik;
		$data['request_name'] = $request_name;
		$data['request_date'] = date("Y-m-d",strtotime($request_date));
		$data['request_passenger'] = $request_passenger;
		$data['request_time'] = $request_time;
		$data['request_destination'] = $request_destination;
		$data['request_note'] = $request_note;
		
		$this->dbtransporter->insert("pgn_car_request", $data);

		$callback["error"] = false;
		$callback["message"] = "Add Request Success";
		$callback["redirect"] = base_url()."transporter/pgn_carpool/mn_request";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function request_edit()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("request_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("pgn_car_request");
		$row = $q->row();
		
		$params["data"] = $row;
		
		$html = $this->load->view('mod_pgn_carpooling/edit_request', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
    }
	
	function saveedit_request()
	{
		if (! isset($this->sess->user_id)) 
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$request_id = isset($_POST['request_id']) ? $_POST['request_id'] : "";
		$request_nik = isset($_POST['request_nik']) ? $_POST['request_nik'] : "";
		$request_name = isset($_POST['request_name']) ? $_POST['request_name'] : "";
		$request_date = isset($_POST['request_date']) ? $_POST['request_date'] : "";
		$request_passenger = isset($_POST['request_passenger']) ? $_POST['request_passenger'] : "";
		$request_time = isset($_POST['request_time']) ? $_POST['request_time'] : "";
		$request_destination = isset($_POST['request_destination']) ? $_POST['request_destination'] : "";
		$request_note = isset($_POST['request_note']) ? $_POST['request_note'] : "";
		
		if ($request_nik == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input User NIk !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_name == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Name !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_date == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Date !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_time == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Time !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_destination == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Request Destination !";
		
			echo json_encode($callback);
			return;
		}
		
		$error = "";
		unset($data);
	
		$data['request_nik'] = $request_nik;
		$data['request_name'] = $request_name;
		$data['request_date'] = date("Y-m-d",strtotime($request_date));
		$data['request_passenger'] = $request_passenger;
		$data['request_time'] = $request_time;
		$data['request_destination'] = $request_destination;
		$data['request_note'] = $request_note;
		
		$this->dbtransporter->where("request_id",$request_id);
		$this->dbtransporter->update("pgn_car_request", $data);

		$callback["error"] = false;
		$callback["message"] = "Edit Request Success";
		$callback["redirect"] = base_url()."transporter/pgn_carpool/mn_request";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function request_detail()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("request_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("pgn_car_request");
		$row = $q->row();
		
		//Get Vehicle
		$this->dbtransporter->where("pgn_vehicle_status",1);
		$qv = $this->dbtransporter->get("pgn_vehicle");
		$vehicle = $qv->result();
		
		$params["data"] = $row;
		$params["vehicle"] = $vehicle;
		
		$html = $this->load->view('mod_pgn_carpooling/detail_request', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
    }

	function delete_request($id)
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("request_id", $id);
		if($this->dbtransporter->delete("pgn_car_request"))
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
	
	function process_request()
	{
		$id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->where("request_id", $id);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("pgn_car_request");
		$row = $q->row();
		
		$this->dbtransporter->where("pgn_vehicle_status",1);
		$this->dbtransporter->where("pgn_vehicle_book_status",0);
		$q = $this->dbtransporter->get("pgn_vehicle");
		$vehicle = $q->result();
		$total = count($vehicle);
		
		$params["data"] = $row;
		$params["vehicle"] = $vehicle;
		$params["total"] = $total;
		
		$html = $this->load->view('mod_pgn_carpooling/process_request', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
	}
	
	function save_process_request()
	{
		$this->dbtransporter = $this->load->database("transporter", true);
		$request_id = isset($_POST['request_id']) ? $_POST['request_id'] : "";
		$request_car = isset($_POST['request_car']) ? $_POST['request_car'] : 0;
		$request_driver = isset($_POST['request_driver']) ? $_POST['request_driver'] : "";
		$request_process_status = 1;
		
		if ($request_car == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Please Select Available Car !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($request_driver == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input Driver Name !";
		
			echo json_encode($callback);
			return;
		}
		
		$error = "";
		unset($data);
	
		$data['request_car'] = $request_car;
		$data['request_driver'] = $request_driver;
		$data['request_process_status'] = $request_process_status;
		
		$this->dbtransporter->where("request_id",$request_id);
		$this->dbtransporter->update("pgn_car_request", $data);
		
		//Update car status
		unset($data);
		$data['pgn_vehicle_book_status'] = 1;
		$this->dbtransporter->where("pgn_vehicle_device",$request_car);
		$this->dbtransporter->update("pgn_vehicle", $data);
		
		$callback["error"] = false;
		$callback["message"] = "Process Request Success";
		$callback["redirect"] = base_url()."transporter/pgn_carpool/mn_request";
	
		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}
	
	function complete_request($id)
	{
		unset($data);
		$data['request_complete_status'] = 1;
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("request_id", $id);
		$this->dbtransporter->update("pgn_car_request", $data);
		
		//Update car status
		$this->dbtransporter->where("request_id", $id);
		$q = $this->dbtransporter->get("pgn_car_request");
		$row = $q->row();
		$vehicle = $row->request_car;
		
		unset($data);
		$data['pgn_vehicle_book_status'] = 0;
		$this->dbtransporter->where("pgn_vehicle_device",$vehicle);
		$this->dbtransporter->update("pgn_vehicle", $data);
		$callback['message'] = "Request has been Complete";
		$callback['error'] = false;	
		echo json_encode($callback);	
	}
	
	function total_request_pending()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}
        $this->dbtrans = $this->load->database('transporter', true);
		$dn = date("Y-m-d");
		$this->dbtrans->where("request_date",$dn);
		$this->dbtrans->where("request_process_status",0);
		$qp = $this->dbtrans->get("pgn_car_request");
		$rp = $qp->result();
		$total = count($rp);
        if ($total != 0)
        {
            $callback["total"] = $total;
            
        }
        else
        {
            $callback["total"] = 0;
        }
        echo json_encode($callback);
        return;
	}
	
	function total_request_confirm()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}
        $this->dbtrans = $this->load->database('transporter', true);
		$dn = date("Y-m-d");
		$this->dbtrans->where("request_date",$dn);
		$this->dbtrans->where("request_process_status",1);
		$qp = $this->dbtrans->get("pgn_car_request");
		$rp = $qp->result();
		$total = count($rp);
        if ($total != 0)
        {
            $callback["total"] = $total;
            
        }
        else
        {
            $callback["total"] = 0;
        }
        echo json_encode($callback);
        return;
	}
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */