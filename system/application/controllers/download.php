<?php
include "base.php";

class Download extends Base
{
	function download()
	{
		parent::Base();
		$this->load->helper('download');
    $this->load->model("gpsmodel");
    $this->load->library('zip');

		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
	}

	function tutorial()
	{

		switch($_SERVER['SERVER_NAME'])
		{
			case "lacak-mobil.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualLacakMobil.pdf');
			$name = 'ManualLacakMobil.pdf';
			break;

			case "app.oto-track.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualOtoTrack.pdf');
			$name = 'ManualOtoTrack.pdf';
			break;

			case "tracker.gpsandalas.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualGPSandalas.pdf');
			$name = 'ManualGPSandalas.pdf';
			break;

			case "app.nusa-track.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualNusaTrack.pdf');
			$name = 'ManualNusaTrack.pdf';
			break;

			case "pgn.gpsandalas.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualGPSandalas.pdf');
			$name = 'ManualGPSandalas.pdf';
			break;

			case "lacaktranslog.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualBookTransporter2020.pdf');
			$name = 'ManualBookTransporter2020.pdf';
			break;

			case "powerblocktrans.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualBookTransporter2020.pdf');
			$name = 'ManualBookTransporter2020.pdf';
			break;

			case "tagtrans.com":
			$contents = file_get_contents(base_url().'assets/downloads/ManualBookTransporter2020.pdf');
			$name = 'ManualBookTransporter2020.pdf';
			break;

			default :
			$contents = file_get_contents(base_url().'assets/downloads/ManualLacakMobil.pdf');
			$name = 'ManualLacakMobil.pdf';

		}

		force_download($name, $contents);
	}

	function smsCommand()
	{
		$contents = file_get_contents(base_url().'assets/downloads/SMSCommand.pdf');
		$name = "SMSCommand.pdf";
		force_download($name, $contents);
	}

    function mn_download()
    {
        if (! isset($this->sess->user_company)) {
            redirect(base_url());
        }

        $this->params["title"] = "Download Daily Report";
        $this->params['content'] = $this->load->view("download/mn_download", $this->params, true);
        $this->load->view("templatesess", $this->params);
    }

    function downloadlist()
    {
         if (! isset($this->sess->user_company))
         {
            redirect(base_url());
         }

         $report_type = isset($_POST['field']) ? $_POST['field'] : "" ;
         $my_vehicle = $this->get_vehicle();
         $this->params['vehicle'] = $my_vehicle;
         //print_r($my_vehicle);exit;

         if (isset($report_type) && $report_type != "")
         {
            switch ($report_type)
            {
               case "trip_mileage" :
                $this->params['content'] = $this->load->view("download/mn_download_trip", $this->params, true);
               break;

               case "history" :
                $this->params['content'] = $this->load->view("download/mn_download_history", $this->params, true);
               break;

               case "playback" :
                $this->params['content'] = $this->load->view("download/mn_download_playback", $this->params, true);
               break;

            }

            $this->load->view("templatesess", $this->params);

         }
         else
         {
            redirect(base_url());
         }
    }

    function trip_mileage_available()
    {
        $this->dbtrans = $this->load->database("transporter",true);

        $field = isset($_POST['field']) ? $_POST['field'] : "";
        $vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $data_sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
        $data_edate = isset($_POST['edate']) ? $_POST['edate'] : "";
        //print_r($offset);exit;
        $my_id = $this->sess->user_id;
        $my_company = $this->sess->user_company;
        $my_vehicle =  $this->get_vehicle();

        $this->dbtrans->order_by("autoreport_id","desc");
        $this->dbtrans->where("autoreport_user_id",$my_id);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari, 1168 = Userid Takari
		if ($this->sess->user_company == "247")
		{
			$this->dbtrans->or_where("autoreport_user_id","1168");
		}

        if (isset($vehicle) && $vehicle != 0)
        {
            $this->dbtrans->where("autoreport_vehicle_id",$vehicle);

        }
		else
		{
			$this->dbtrans->where("autoreport_company",$my_company);

			//Khusus PGN, 247 = Company PGN, 250 = Company Takari
			if ($this->sess->user_company == "247")
			{
				$this->dbtrans->or_where("autoreport_company","250");
			}
		}

        if (isset($data_sdate) && $data_sdate != "")
        {
            $data_sdate = date("Y-m-d H:i:s", strtotime($data_sdate." "."00:00:00"));
            $this->dbtrans->where("autoreport_data_startdate >= ",$data_sdate);
        }

        if (isset($data_edate) && $data_edate != "")
        {
            $data_edate = date("Y-m-d H:i:s", strtotime($data_edate." "."23:59:59"));
            $this->dbtrans->where("autoreport_data_enddate <= ",$data_edate);
        }

        $q = $this->dbtrans->get("autoreport_tripmileage", 20, $offset);
        $rows = $q->result();

        //**********************************************************************

        $this->dbtrans->select("count(*) as total");
        $this->dbtrans->where("autoreport_user_id",$my_id);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari, 1168 = Userid Takari
		if ($this->sess->user_company == "247")
		{
			$this->dbtrans->or_where("autoreport_user_id","1168");
		}


        if (isset($vehicle) && $vehicle != 0)
        {
            $this->dbtrans->where("autoreport_vehicle_id",$vehicle);
        }
		else
		{
			$this->dbtrans->where("autoreport_company",$my_company);
			//Khusus PGN, 247 = Company PGN, 250 = Company Takari
			if ($this->sess->user_company == "247")
			{
				$this->dbtrans->or_where("autoreport_company","250");
			}
		}

        if (isset($data_sdate) && $data_sdate != "")
        {
            $data_sdate = date("Y-m-d H:i:s", strtotime($data_sdate." "."00:00:00"));
            $this->dbtrans->where("autoreport_data_startdate >= ",$data_sdate);
        }

        if (isset($data_edate) && $data_edate != "")
        {
            $data_edate = date("Y-m-d H:i:s", strtotime($data_edate." "."23:59:59"));
            $this->dbtrans->where("autoreport_data_enddate <= ",$data_edate);
        }

        $qt = $this->dbtrans->get("autoreport_tripmileage");
		$rt = $qt->row();
		$total = $rt->total;

        $this->load->library("pagination1");
        $config['uri_segment'] = 3;
        $config['total_rows'] = $total;
        $config['per_page'] = 20;

        $this->pagination1->initialize($config);

        $this->params["paging"] = $this->pagination1->create_links();
        $this->params["offset"] = $offset;
        $this->params["total"] = $total;
        $this->params["data"] = $rows;
        $this->params["vehicle"] = $my_vehicle;

        $callback['html'] = $this->load->view('download/list_download_trip', $this->params, true);
        $callback['total'] = $total;

        echo json_encode($callback);

    }

    function history_report_available($offset=0)
    {
        $this->dbtrans = $this->load->database("transporter",true);

        $field = isset($_POST['field']) ? $_POST['field'] : "";
        $vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
        $data_sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
        $data_edate = isset($_POST['edate']) ? $_POST['edate'] : "";

        $my_id = $this->sess->user_id;
        $my_company = $this->sess->user_company;

        $this->dbtrans->where("autoreport_user_id",$my_id);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari, 1168 = Userid Takari
		if ($this->sess->user_company == "247")
		{
			$this->dbtrans->or_where("autoreport_user_id","1168");
		}

        if (isset($vehicle) && $vehicle != 0)
        {
            $this->dbtrans->where("autoreport_vehicle_id",$vehicle);
        }
		else
		{
			$this->dbtrans->where("autoreport_company",$my_company);
			//Khusus PGN
			// 247 = Company PGN
			// 250 = Company Takari
			if ($this->sess->user_company == "247")
			{
				$this->dbtrans->or_where("autoreport_company","250");
			}
		}

        if (isset($data_sdate) && $data_sdate != "")
        {
            $data_sdate = date("Y-m-d H:i:s", strtotime($data_sdate." "."00:00:00"));
            $this->dbtrans->where("autoreport_data_startdate >= ",$data_sdate);
        }

        if (isset($data_edate) && $data_edate != "")
        {
            $data_edate = date("Y-m-d H:i:s", strtotime($data_edate." "."23:59:59"));
            $this->dbtrans->where("autoreport_data_enddate <= ",$data_edate);
        }

        $q = $this->dbtrans->get("autoreport_history", 20, $offset);
        $rows = $q->result();

        //**********************************************************************


        $this->dbtrans->where("autoreport_user_id",$my_id);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari, 1168 = Userid Takari
		if ($this->sess->user_company == "247")
		{
			$this->dbtrans->or_where("autoreport_user_id","1168");
		}

        if (isset($vehicle) && $vehicle != 0)
        {
            $this->dbtrans->where("autoreport_vehicle_id",$vehicle);
        }
		else
		{
			$this->dbtrans->where("autoreport_company",$my_company);
		}

        if (isset($data_sdate) && $data_sdate != "")
        {
            $data_sdate = date("Y-m-d H:i:s", strtotime($data_sdate." "."00:00:00"));
            $this->dbtrans->where("autoreport_data_startdate >= ",$data_sdate);
        }

        if (isset($data_edate) && $data_edate != "")
        {
            $data_edate = date("Y-m-d H:i:s", strtotime($data_edate." "."23:59:59"));
            $this->dbtrans->where("autoreport_data_enddate <= ",$data_edate);
        }

        $qtot = $this->dbtrans->get("autoreport_history");
        $rowtot = $q->result();
        $total = count($rowtot);

        //print_r($rows);exit;

        $this->load->library("pagination1");
        $config['uri_segment'] = 3;
        $config['total_rows'] = $total;
        $config['per_page'] = 20;

        $this->pagination1->initialize($config);

        $this->params["paging"] = $this->pagination1->create_links();
        $this->params["offset"] = $offset;
        $this->params["total"] = $total;
        $this->params["data"] = $rows;

        $callback['html'] = $this->load->view('download/list_download_history_report', $this->params, true);
        $callback['total'] = $total;

        echo json_encode($callback);

    }

    function playback_report_available($offset=0)
    {
        $this->dbtrans = $this->load->database("transporter",true);

        $field = isset($_POST['field']) ? $_POST['field'] : "";
        $vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
        $data_sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
        $data_edate = isset($_POST['edate']) ? $_POST['edate'] : "";

        $my_id = $this->sess->user_id;
        $my_company = $this->sess->user_company;
        $my_vehicle =  $this->get_vehicle();

        $this->dbtrans->order_by("autoreport_id","desc");
        $this->dbtrans->where("autoreport_user_id",$my_id);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari, 1168 = Userid Takari
		if ($this->sess->user_company == "247")
		{
			$this->dbtrans->or_where("autoreport_user_id","1168");
		}

        if (isset($vehicle) && $vehicle != 0)
        {
            $this->dbtrans->where("autoreport_vehicle_id",$vehicle);
        }
		else
		{
			$this->dbtrans->where("autoreport_company",$my_company);
			//Khusus PGN
			// 247 = Company PGN
			// 250 = Company Takari
			if ($this->sess->user_company == "247")
			{
				$this->dbtrans->or_where("autoreport_company","250");
			}
		}

        if (isset($data_sdate) && $data_sdate != "")
        {
            $data_sdate = date("Y-m-d H:i:s", strtotime($data_sdate." "."00:00:00"));
            $this->dbtrans->where("autoreport_data_startdate >= ",$data_sdate);
        }

        if (isset($data_edate) && $data_edate != "")
        {
            $data_edate = date("Y-m-d H:i:s", strtotime($data_edate." "."23:59:59"));
            $this->dbtrans->where("autoreport_data_enddate <= ",$data_edate);
        }

        $q = $this->dbtrans->get("autoreport_playback", 20, $offset);
        $rows = $q->result();

        //**********************************************************************


        $this->dbtrans->where("autoreport_user_id",$my_id);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari, 1168 = Userid Takari
		if ($this->sess->user_company == "247")
		{
			$this->dbtrans->or_where("autoreport_user_id","1168");
		}

        if (isset($vehicle) && $vehicle != 0)
        {
            $this->dbtrans->where("autoreport_vehicle_id",$vehicle);
        }
		else
		{
			$this->dbtrans->where("autoreport_company",$my_company);
		}


        if (isset($data_sdate) && $data_sdate != "")
        {
            $data_sdate = date("Y-m-d H:i:s", strtotime($data_sdate." "."00:00:00"));
            $this->dbtrans->where("autoreport_data_startdate >= ",$data_sdate);
        }

        if (isset($data_edate) && $data_edate != "")
        {
            $data_edate = date("Y-m-d H:i:s", strtotime($data_edate." "."23:59:59"));
            $this->dbtrans->where("autoreport_data_enddate <= ",$data_edate);
        }

        $total = $this->dbtrans->count_all_results("autoreport_playback");

        //print_r($rows);exit;

        $this->load->library("pagination1");
        $config['uri_segment'] = 3;
        $config['total_rows'] = $total;
        $config['per_page'] = 20;

        $this->pagination1->initialize($config);

        $this->params["paging"] = $this->pagination1->create_links();
        $this->params["offset"] = $offset;
        $this->params["total"] = $total;
        $this->params["data"] = $rows;
        $this->params["vehicle"] = $my_vehicle;

        $callback['html'] = $this->load->view('download/list_download_playback', $this->params, true);
        $callback['total'] = $total;

        echo json_encode($callback);
    }
    function download_trip()
    {
        $id = $this->uri->segment(3);
        //print_r($id);exit;

        $this->dbtrans = $this->load->database("transporter",true);
        $this->dbtrans->select("autoreport_vehicle_device, autoreport_type, autoreport_filename");
        $this->dbtrans->where("autoreport_id",$id);
        $this->dbtrans->limit(1);
        $q = $this->dbtrans->get("autoreport_tripmileage");
        $row = $q->row();

        $vehicle =  explode("@",$row->autoreport_vehicle_device);
        $download_path = "./assets/media/autoreport/";
        $filename = $row->autoreport_filename;
        $report_type = $row->autoreport_type;
        $separator = "/";

        $download_url = $download_path.$report_type.$separator.$vehicle[0].$separator.$filename;
        //print_r($download_url);exit;

        $contents = file_get_contents($download_url);
        $name = $row->autoreport_filename;

        force_download($name, $contents);

    }

    function download_playback()
    {
        $id = $this->uri->segment(3);

        $this->dbtrans = $this->load->database("transporter",true);
        $this->dbtrans->select("autoreport_vehicle_device, autoreport_download_path");
        $this->dbtrans->where("autoreport_id",$id);
        $this->dbtrans->limit(1);
        $q = $this->dbtrans->get("autoreport_playback");
        $row = $q->row();

        $ex = explode("/",$row->autoreport_download_path);
        $sep = "/";

        //"./assets/media/autoreport/";

        $download_url = ".".$sep.$ex[4].$sep.$ex[5].$sep.$ex[6].$sep.$ex[7].$sep.
                        $ex[8].$sep.$ex[9].$sep.$ex[10].$sep.$ex[11];
        $filename = $ex[11];

        $contents = file_get_contents($download_url);
        $name = $filename;

        force_download($name, $contents);
    }

    function download_history_report()
    {
        $id = $this->uri->segment(3);

        $this->dbtrans = $this->load->database("transporter",true);
        $this->dbtrans->select("autoreport_vehicle_device, autoreport_download_path");
        $this->dbtrans->where("autoreport_id",$id);
        $this->dbtrans->limit(1);
        $q = $this->dbtrans->get("autoreport_history");
        $row = $q->row();

        $ex = explode("/",$row->autoreport_download_path);
        $sep = "/";

        //"./assets/media/autoreport/";

        $download_url = ".".$sep.$ex[4].$sep.$ex[5].$sep.$ex[6].$sep.$ex[7].$sep.
                        $ex[8].$sep.$ex[9].$sep.$ex[10].$sep.$ex[11];
        $filename = $ex[11];

        $contents = file_get_contents($download_url);
        $name = $filename;

        force_download($name, $contents);

    }

    function download_checklist_trip()
    {
        $zip = new ZipArchive();
        $this->dbtrans = $this->load->database("transporter",true);

        $id = $_POST['vid'];
        $myzip = "tripmileage".date("dmY").date("His").".zip";
        $zipdir = "/home/transporter/public_html/assets/media/autoreport/trip_mileage_zip/";
        $filezip = $zipdir.$myzip;
        $report_type = "trip_mileage_zip";
        $separator = "/";

        $download_path = $this->config->item("download_report_path");
        $download_url = $download_path.$report_type.$separator.$myzip;
        //print_r($download_url);exit;

        if ($zip->open($filezip, ZIPARCHIVE::CREATE)!==TRUE)
        {
            exit("cannot open <$filezip>\n");
        }

        if (isset($id) && count($id)>0)
        {
                $this->dbtrans->select("autoreport_filename, autoreport_download_path");
                $this->dbtrans->where_in("autoreport_vehicle_id",$id);
                $q = $this->dbtrans->get("autoreport_tripmileage");
                $rows = $q->result();
                //print_r($rows);exit;

                foreach($rows as $row)
                {
                    $zip->addFile($row->autoreport_download_path,$row->autoreport_filename);
                }

                $zip->close($filezip);

                $contents = file_get_contents($download_url);
                $name = $myzip;
        }

        force_download($name, $contents);

    }


    function create_zip()
    {
          $zip = new ZipArchive();
          $filename = "/home/transporter/public_html/assets/media/autoreport/trip_mileage_zip/test112.zip";
          $thisdir = "/home/transporter/public_html/assets/media/autoreport/trip_mileage/002100001980";

          if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
            }

            $zip->addFromString("testfilephp.txt" . time(), "#1 This is a test string added as testfilephp.txt.\n");
            $zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
            $zip->addFile($thisdir . "/TripMileage_B9650UXR_20121224.xls","/testfromfile.php");
            echo "numfiles: " . $zip->numFiles . "\n";
            echo "status:" . $zip->status . "\n";
            $zip->close();
    }

    function get_vehicle()
    {
        if (! isset($this->sess->user_company))
        {
            redirect(base_url());
        }

        $my_id = $this->sess->user_id;

        $this->db->order_by("vehicle_name", "asc");
        $this->db->where("vehicle_active_date2 >=", date("Ymd"));
        $this->db->where("vehicle_status <>", 3);
        $this->db->where("vehicle_user_id", $my_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);

		//Khusus PGN, 247 = Company PGN, 250 = Company Takari
		if ($this->sess->user_company == "247")
		{
			$this->db->or_where("vehicle_user_id","1168");
		}

        $q = $this->db->get("vehicle");
        $rows = $q->result();

        return $rows;

    }

	function historycsv(){

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('download/mn_download_history_csv', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function download_historycsv()
	{

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$userid = $this->input->post("userid");
		$usercompany = $this->input->post("usercompany");
		$offset = 0;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->order_by("autoreport_vehicle_no","asc");
        $this->dbtrans->select("autoreport_id,autoreport_vehicle_no,autoreport_vehicle_name,autoreport_filename,autoreport_data_startdate,autoreport_download_path");
		if($vehicle != "all"){
			$this->dbtrans->where("autoreport_vehicle_device",$vehicle);
		}
		$this->dbtrans->where("autoreport_type", "csv");
		$this->dbtrans->where("autoreport_user_id", $userid);

		$this->dbtrans->where("autoreport_data_startdate >=", $sdate);
		$this->dbtrans->where("autoreport_data_enddate <=", $edate);
		$q = $this->dbtrans->get("autoreport_csv");
        $rows = $q->result();


		$params['data'] = $rows;
		$params['offset'] = $offset;
		$html = $this->load->view("download/list_download_history_csv", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

		return;
	}

	function histcsv(){

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu']  = "report";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/download/v_download_history', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);

		// $this->params["content"] = $this->load->view('download/mn_download_history_csv', $this->params, true);
		// $this->load->view("templatesess", $this->params);
	}

	function download_histcsv()
	{

		$vehicle     = $this->input->post("vehicle");
		$startdate   = $this->input->post("startdate");
		$enddate     = $this->input->post("enddate");
		$shour       = $this->input->post("shour");
		$ehour       = $this->input->post("ehour");
		$userid      = $this->input->post("userid");
		$usercompany = $this->input->post("usercompany");
		$offset      = 0;

		$sdate       = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate       = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->order_by("autoreport_vehicle_no","asc");
        $this->dbtrans->select("autoreport_id,autoreport_vehicle_no,autoreport_vehicle_name,autoreport_filename,autoreport_data_startdate,autoreport_download_path");
		if($vehicle != "all"){
			$this->dbtrans->where("autoreport_vehicle_device",$vehicle);
		}
		$this->dbtrans->where("autoreport_type", "csv");
		$this->dbtrans->where("autoreport_user_id", $userid);

		$this->dbtrans->where("autoreport_data_startdate >=", $sdate);
		$this->dbtrans->where("autoreport_data_enddate <=", $edate);
		$q = $this->dbtrans->get("autoreport_csv");
        $rows = $q->result();


		$params['data']    = $rows;
		$params['offset']  = $offset;
		$html              = $this->load->view("dashboard/download/list_download_history_csv", $params, true);
		$callback['error'] = false;
		$callback['html']  = $html;

		echo json_encode($callback);

		return;
	}

}
