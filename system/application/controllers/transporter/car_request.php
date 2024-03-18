<?php
include "base.php";

class Car_request extends Base {

	function Car_request()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}		
	}
    
    function getlist($offset=0) 
    {
        
        $field = isset($_POST["field"]) ? $_POST["field"] : "";
        $keyword = isset($_POST["keyword"]) ? $_POST["keyword"] : "";
        $status = isset($_POST["status"]) ? $_POST["status"] : "";
        
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("car_request");
        $this->dbtransporter->order_by('request_create_date',"DESC");
        
        if ($this->sess->user_group == 0 || $this->sess->user_group == "" || $this->sess->user_group == null)
        {
            $this->dbtransporter->where("request_company",$this->sess->user_company);    
        }
        
        if ($this->sess->user_group != 0 && $this->sess->user_group != "" &&  $this->sess->user_group != null)
        {
            $this->dbtransporter->where("request_company",$this->sess->user_company);
            $this->dbtransporter->where("request_group_company",$this->sess->user_group);
        }
        
        switch($field)
        {
            case "company":
            $this->dbtransporter->where("request_group_name LIKE '%".$keyword."%' ",null);
            break;
            
            case "vehicle_no":
            $this->dbtransporter->where("request_vehicle_no LIKE '%".$keyword."%' ",null);
            break;
            
            case "status":
            $this->dbtransporter->where("request_status", $status);
            break;
        }
        
        $q = $this->dbtransporter->get('',$this->config->item("limit_records"), $offset);
        $rows = $q->result();
        $total = count($rows);
        $request_status = $this->getrequest_status();
        $trip_purpose = $this->get_trip_purpose();
        
        $config['uri_segment'] = 5;
		$config['base_url'] = base_url()."transporter/car_request/getlist/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination->initialize($config);
        
        $this->params["title"] = "Manage Request Order";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
        $this->params["trip_purpose"] = $trip_purpose;
		$this->params["data"] = $rows;
        $this->params["request_status"] = $request_status;
		$this->params["content"] = $this->load->view("transporter/car_request/result.php", $this->params, true);
		$this->load->view("templatesess", $this->params);
		
		$this->dbtransporter->close();
        
    }
    
    function add_request()
    {
        if (!isset($this->sess->user_group))
        {
		  redirect(base_url());
		}	
        
        $data_group = $this->get_group_by_id();
        $trip_purpose = $this->get_trip_purpose();
        
        $this->params["trip_purpose"] = $trip_purpose;
        $this->params["group"] = $data_group;
        $this->params["title"] = "Request Form";		
		$this->params['content'] = $this->load->view("transporter/car_request/add", $this->params, true);
		$this->load->view("templatesess", $this->params);
    }
    
    function save_by_cust()
    {
        if (!isset($this->sess->user_group))
        {
		  redirect(base_url());
		}
        
        $this->dbtransporter = $this->load->database('transporter', true);
        $request_company = isset($_POST["request_company"]) ? $_POST["request_company"]: 0;
        $request_group_company = isset($_POST["request_group_company"]) ? $_POST["request_group_company"]: 0;
        $request_group_name = isset($_POST["request_group_name"]) ? $_POST["request_group_name"]: 0;
        $request_user_id = isset($_POST["request_user_id"]) ? $_POST["request_user_id"]: 0;
        $request_start_date = isset($_POST["start_date"]) ? $_POST["start_date"] : 0;
        $request_end_date = isset($_POST["end_date"]) ? $_POST["end_date"] : "";
        $request_purpose = isset($_POST["request_purpose"]) ? $_POST["request_purpose"] : 0;
        
        //PIC DATA
        $request_pic_name = isset($_POST["request_pic_name"]) ? $_POST["request_pic_name"]:"";
        $request_pic_mobile = isset($_POST["request_pic_mobile"]) ? $_POST["request_pic_mobile"]:"";
        $request_pic_phone = isset($_POST["request_pic_phone"]) ? $_POST["request_pic_phone"]:"";
        $request_pic_email = isset($_POST["request_pic_email"]) ? $_POST["request_pic_email"]:"";
        $request_pic_address = isset($_POST["request_pic_address"]) ? $_POST["request_pic_address"]:"";
        
        $request_status = $this->config->item("new_order");
        $request_create_date = date("d-m-Y");
        
        $error = "";
        unset($data);
        
        if ($request_pic_name == "")
        {
            echo json_encode(array("error"=>"true", "message"=>"Please Input Your PIC Name"));
            return;
        }
        if ($request_pic_mobile == "")
        {
            echo json_encode(array("error"=>"true", "message"=>"Please Input Your PIC Mobile"));
            return;
        }
  
        $data["request_company"] = $request_company;
        $data["request_group_company"] = $request_group_company;
        $data["request_group_name"] = $request_group_name;
        $data["request_user_id"] = $request_user_id;
        $data["request_start_date"] = $request_start_date;
        $data["request_end_date"] = $request_end_date;
        $data["request_pic_name"] = $request_pic_name;
        $data["request_pic_mobile"] = $request_pic_mobile;
        $data["request_pic_phone"] = $request_pic_phone;
        $data["request_pic_email"] = $request_pic_email;
        $data["request_pic_address"] = $request_pic_address;
        $data["request_purpose"] = $request_purpose;
        $data["request_status"] = $this->config->item('new_order');
        $data["request_create_date"] = $request_create_date;
        //print_r($data);exit;
        $this->dbtransporter->insert("car_request",$data);
        $callback["error"] = false;
        $callback["message"] = "Request Complete, Please Wait for approval.. Thanks";
        $callback["redirect"] = base_url()."transporter/car_request/getlist";
        echo json_encode($callback);
        
        $this->dbtransporter->close();
        return;
    }
    
    function getdetail()
    {
        $request_id = $this->input->post('id');
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("car_request");
        $this->dbtransporter->where("request_id", $request_id);
        $this->dbtransporter->limit(1);
        $q = $this->dbtransporter->get();
        $rows = $q->row();
        
        $this->dbtransporter->close();
        
        $request_status = $this->getrequest_status();
        $trip_purpose = $this->get_trip_purpose();
        
        $params["data"] = $rows;
        $params["request_status"] = $request_status;
        $params["trip_purpose"] = $trip_purpose;
        $html = $this->load->view("transporter/car_request/detail", $params, true);
        $callback["error"] = false;
        $callback["html"] = $html;
        echo json_encode($callback);
    }
    
    function confirm_request()
    {
        $request_id = $this->input->post('id');
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("car_request");
        $this->dbtransporter->where("request_id", $request_id);
        $this->dbtransporter->limit(1);
        $q = $this->dbtransporter->get();
        $rows = $q->row();
        $this->dbtransporter->close();
        
        $request_status = $this->getrequest_status();
        $available_vehicle = $this->get_available_vehicle();
        
        $params["data"] = $rows;
        $params["available_vehicle"] = $available_vehicle;
        $params["request_status"] = $request_status;
        $html = $this->load->view("transporter/car_request/confirm_request", $params, true);
        $callback["error"] = false;
        $callback["html"] = $html;
        echo json_encode($callback);
    }
    
    function order_complete_dialog()
    {
        $request_id = $this->input->post('id');
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("car_request");
        $this->dbtransporter->where("request_id", $request_id);
        $this->dbtransporter->limit(1);
        $q = $this->dbtransporter->get();
        $rows = $q->row();
        $this->dbtransporter->close();
        
        $request_status = $this->getrequest_status();
        $trip_purpose = $this->get_trip_purpose();
        
        $params["data"] = $rows;
        $params["request_status"] = $request_status;
        $params["trip_purpose"] = $trip_purpose;
        $html = $this->load->view("transporter/car_request/order_complete", $params, true);
        $callback["error"] = false;
        $callback["html"] = $html;
        echo json_encode($callback);
    }
    
    function save_complete_order()
    {
        $request_id = $this->input->post('request_id');
        $v_id = $this->input->post('request_vehicle');
        
        unset($data_update_request);
        $this->dbtransporter = $this->load->database('transporter', true);
        $data_update_request["request_status"] = $this->config->item("order_complete");
        
        unset($data_update_vehicle);
        $data_update_vehicle["vehicle_company"] = 0;
        $data_update_vehicle["vehicle_group"] = 0;
        
        //print_r($data_update_request);
        //print_r($data_update_vehicle);
        //exit;
        
        $this->dbtransporter->where('request_id', $request_id);
        $this->dbtransporter->update('car_request', $data_update_request);
        $this->dbtransporter->close();
        
        //update vehicle database master
        $this->db->where('vehicle_id', $v_id);
        $this->db->update('vehicle', $data_update_vehicle);
        
        $callback["error"] = false;
        $callback["message"] = "Order Complete";
        $callback["redirect"] = base_url()."transporter/car_request/getlist";
        echo json_encode($callback);
        return;
    }
    
    function save_confirm()
    {
        $this->dbtransporter = $this->load->database('transporter', true);
        
        $request_id = $this->input->post('request_id');
        $request_status = $this->input->post('request_status');
        $request_vehicle = $this->input->post('vehicle_id');
        $request_company = $this->input->post('request_company');
        $request_group_company = $this->input->post('request_group_company');
        $request_confirm = date("d-m-Y");        
        
        if ($request_status == "blank")
        {
            echo json_encode(array("error"=>"true", "message"=>"Confrim Can't Process, Please contact your Programmer" ));
            return;
        }
        
        if ($request_status == $this->config->item('booked') && $request_vehicle == "blank")
        {
            echo json_encode(array("error"=>"true", "message"=>"Confrim Can't Process, Please contact your Programmer" ));
            return;
        }
        
        $data_vehicle = explode("+", $request_vehicle);
        $v_id = $data_vehicle[0];
        $v_name = $data_vehicle[1];
        $v_no = $data_vehicle[2];
        
        unset($data_update);
        $data_update["request_vehicle"] = $v_id;
        $data_update["request_vehicle_name"] = $v_name;
        $data_update["request_vehicle_no"] = $v_no;
        $data_update["request_status"] = $request_status;
        $data_update["request_confirm_date"] = $request_confirm;
        //print_r($data_update);exit;
        
        $this->dbtransporter->where('request_id', $request_id);
        $this->dbtransporter->update('car_request', $data_update);
        $this->dbtransporter->close();
        
        unset($data_update_vehicle);
        $data_update_vehicle["vehicle_company"] = $request_company;
        $data_update_vehicle["vehicle_group"] = $request_group_company;
        
        //update vehicle database master
        $this->db->where('vehicle_id', $v_id);
        $this->db->update('vehicle', $data_update_vehicle);
        
        $callback["error"] = false;
        $callback["message"] = "Confirmation Complete.";
        $callback["redirect"] = base_url()."transporter/car_request/getlist";
        echo json_encode($callback);
        return;
       
    }
    
    function cancel_by_customer()
    {
        $request_id = $this->input->post('id');
        if ($request_id)
        {
            unset($data);
            $data["request_status"] = $this->config->item('cancel_by_customer');
            $this->dbtransporter = $this->load->database('transporter', true);
            $this->dbtransporter->select("*");
            $this->dbtransporter->from("car_request");
            $this->dbtransporter->where("request_id", $request_id);
            $this->dbtransporter->limit(1);
            $q = $this->dbtransporter->get();
            $row = $q->row();
            if (count($row)>0)
            {
                if ($row->request_status != $this->config->item('new_order'))
                {
                    $callback["error"] = true;
                    $callback["message"] = "This Request Has Been Confrim, Cancellation Fail!";
                    $callback["redirect"] = base_url()."transporter/car_request/getlist";
                    echo json_encode($callback);
                    $this->dbtransporter->close();
                    return;
                }
                else
                {
                    $this->dbtransporter->where("request_id", $request_id);
                    $this->dbtransporter->update("car_request", $data);
                    $this->dbtransporter->close();
                    $callback["error"] = false;
                    $callback["message"] = "Cancellation Succses";
                    $callback["redirect"] = base_url()."transporter/car_request/getlist";
                    echo json_encode($callback);
                    return;        
                }
            }
            else
            {
                $this->dbtransporter->close();
                redirect (base_url());
            }
        }
        else
        {
            $this->dbtransporter->close();
            redirect (base_url());
        }
    }
    
    function getrequest_status()
    {
        $nodata = "";
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("request_status");
        $q = $this->dbtransporter->get();
        $rows = $q->result();
        
        if (count($rows)>0)
        {
            $this->dbtransporter->close();
            return $rows;    
        }
        else
        {
            $this->dbtransporter->close();
            return $nodata;
        }
    }
    
    function get_trip_purpose()
    {
        $nodata = "";
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->from("trip_purpose");
        $q = $this->dbtransporter->get();
        $rows = $q->result();
        
        if (count($rows)>0)
        {
            $this->dbtransporter->close();
            //print_r($rows);exit;
            return $rows;    
        }
        else
        {
            $this->dbtransporter->close();
            return $nodata;
        }
    }
    
    function get_group_by_id()
    {
        $this->dbtransporter = $this->load->database('transporter', true);
        $nodata = "";
        $this->db->where("group_id", $this->sess->user_group);
        $this->db->limit(1);
        $q = $this->db->get("group");
        $row = $q->row();
        
        if (count($row)>0)
        {
            $this->dbtransporter->close();
            return $row->group_name;
        }
        else
        {
            $this->dbtransporter->close();
            return $nodata;
        }
    }
    
    function get_available_vehicle()
    {
        $nodata = "";
        $this->db->where("vehicle_user_id", $this->sess->user_id);
        $this->db->where("vehicle_group", "0");
        $this->db->where("vehicle_active_date2 >=", date("Ymd"));
        $q = $this->db->get("vehicle");
        $rows = $q->result();
        if (count($rows)>0)
        {
            return $rows;    
        }
        else
        {
            return $nodata;
        }
        
    }
    
    function get_notification()
    {
        $nodata = 0;
        $this->dbtransporter = $this->load->database('transporter', true);
        $this->dbtransporter->select("*");
        $this->dbtransporter->where("request_status", $this->config->item("new_order"));
        $this->dbtransporter->from("car_request");
        $q = $this->dbtransporter->get();
        $rows = $q->result();
        $total = count($rows);
        
        if ($total != 0)
        {
            $callback["total"] = $total;
            
        }
        else
        {
            $callback["total"] = $nodata;
        }
        
        $this->dbtransporter->close();
        echo json_encode($callback);
        return;
    }
	
}

/* End of file branchoffice.php */
/* Location: ./system/application/controllers/transporter/car_request.php */