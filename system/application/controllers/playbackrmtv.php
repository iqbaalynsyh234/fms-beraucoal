<?php
include "base.php";

class Playbackrmtv extends Base {

	function Playbackrmtv()
	{
		parent::Base();
		//$this->load->model("gpsmodel");
		//$this->load->model("vehiclemodel");
		//$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
	}

	function index()
	{
		$userid = 4408;
		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_mv03 !=", "0000");
		$this->db->where("vehicle_user_id", $userid);
		
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");
		//print_r($q);exit();
		/* if ($q->num_rows() == 0)
		{
			redirect(base_url());
		} */

		$rows = $q->result();
		

		$this->params["vehicles"] = $rows;
		
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"] = $this->load->view('newdashboard/playbackvideo/vplaybackremote_report', $this->params, true);
		
		$this->load->view("newdashboard/partial/template_dashboard_attachment", $this->params);
	}

	function search()
	{
		ini_set('display_errors', 1);
		ini_set('memory_limit', '2G');
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("startdate");
		/* $shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour"); */
		$shour = 0;
		$ehour = 86399;
		//$ehour = 3600;
		$channel = $this->input->post("channel");
		$verifcode = $this->input->post("verifcode");
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		
		$shour_sec = strtotime($shour);
		$ehour_sec = strtotime($ehour);

		$m1 = date("m", strtotime($startdate)); 
		$d1 = date("d", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		$error = "";
		$rows_summary = "";
		
		$secretcode = $this->config->item('playbackremote_code');
		

		if ($vehicle == "" || $vehicle == 0)
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		
		
		if ($verifcode != $secretcode)
		{
			$error .= "- Invalid Playback Code! Please contact your Administrator \n";
		}
		
		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		
		//print_r($startdate." ".$enddate." ".$shour." ".$ehour." ".$shour_sec." ".$ehour_sec); exit();
		
		/* $vehicleinfo = $this->get_detail_vehicle($vehicle);
		$vehiclemv03 = $vehicleinfo->vehicle_mv03;
		$vehicle_no = $vehicleinfo->vehicle_no;
		 */
		
		$imei = $vehicle;
		$jsession = $this->get_lastsession(4408);
		//print_r($year." ".$m1." ".$d1);exit();
		
		//example : http://gpsdvr.lacak-mobil.com/StandardApiAction_getVideoFileInfo.action?DevIDNO=142045257806&LOC=1&CHN=1&YEAR=2022&MON=01&DAY=26&RECTYPE=0&FILEATTR=2&BEG=0&END=86399&ARM1=0&ARM2=0&RES=0&STREAM=0&STORE=0&jsession=9DA9503DD08650AA7F15E7224768360C
		$url_api = 'http://gpsdvr.lacak-mobil.com/StandardApiAction_getVideoFileInfo.action?';
		$url_playvideo = $url_api."DevIDNO=".$imei."&LOC=1"."&CHN=".$channel."&YEAR=".$year."&MON=".$m1."&DAY=".$d1."&RECTYPE=0"."&FILEATTR=2"."&FILEATTR=2&BEG=".$shour.""."&END=".$ehour."&ARM1=0&ARM2=0&RES=0&STREAM=0&STORE=0"."&jsession=".$jsession;
		
		$dataJson = file_get_contents($url_playvideo);
		$data = json_decode($dataJson,true);
		
		$error = "";
		
		if(isset($data['files'])){
			
			$result = $data['files'];
		}
		else
		{
			$error .= "- Tidak Ada Data Video! \n";
		}
		
		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$params['data'] = $result;
		//$params['vehicle_no'] = $vehicle_no;
		$html = $this->load->view("newdashboard/playbackvideo/vplaybackremotevideo_result", $params, true);
		
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;
		
		/*
				(
                    [DownTaskUrl] => http://103.253.107.212:80/StandardApiAction_addDownloadTask.action?jsession=&did=142045257806&fbtm=2022-01-26 00:00:25&fetm=2022-01-26 00:01:25&sbtm=2022-01-26 00:00:25&setm=2022-01-26 00:01:25&lab=&fph=;0;0;22;1;26;25;85;0;0_0_0_0&vtp=0&len=7625213&chn=0&dtp=2
                    [DownUrl] => http://103.253.107.212:6604/3/5?DownType=3&jsession=&DevIDNO=142045257806&FILELOC=1&FLENGTH=7625213&FOFFSET=0&MTYPE=1&FPATH=;0;0;22;1;26;25;85;0;0_0_0_0&SAVENAME=
                    [PlaybackUrl] => http://103.253.107.212:6604/3/5?DownType=5&jsession=&DevIDNO=142045257806&FILELOC=1&FILESVR=0&FILECHN=0&FILEBEG=25&FILEEND=85&PLAYIFRM=0&PLAYFILE=;0;0;22;1;26;25;85;0;0_0_0_0&PLAYBEG=0&PLAYEND=0&PLAYCHN=0&YEAR=22&MON=1&DAY=26
                    [PlaybackUrlWs] => ws://103.253.107.212:6604/3/5?DownType=5&jsession=&DevIDNO=142045257806&FILELOC=1&FILESVR=0&FILECHN=0&FILEBEG=25&FILEEND=85&PLAYIFRM=0&PLAYFILE=;0;0;22;1;26;25;85;0;0_0_0_0&PLAYBEG=0&PLAYEND=0&PLAYCHN=0&YEAR=22&MON=1&DAY=26
                    [arm] => 0
                    [arm1] => 0
                    [arm2] => 0
                    [beg] => 25
                    [chn] => 0
                    [chnMask] => 0
                    [clientIp] => 103.253.107.212
                    [clientIp2] => 103.253.107.212
                    [clientIp3] => 
                    [clientPort] => 6604
                    [day] => 26
                    [devIdno] => 142045257806
                    [end] => 85
                    [file] => ;0;0;22;1;26;25;85;0;0_0_0_0
                    [lanip] => 127.0.0.1
                    [len] => 7625213
                    [loc] => 1
                    [mon] => 1
                    [mulChn] => 0
                    [mulPlay] => 0
                    [recing] => 0
                    [res] => 0
                    [store] => 0
                    [stream] => 1
                    [streamType] => 0
                    [svr] => 0
                    [type] => 0
                    [year] => 22
                )
		
		
		*/

	}
	
	function get_company_bylevel(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		/*if($this->sess->user_level == "1"){
			$this->db->where("company_created_by", $this->sess->user_id);
		}*/
		$this->db->where("company_created_by", $this->sess->user_id);
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}
	
	function get_detail_vehicle($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->where("vehicle_id", $id);
		$qd = $this->db->get("vehicle");
		$rd = $qd->row();

		return $rd;
	}
	
	function get_lastsession($id){
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->order_by("sess_id", "desc");
		$this->dbts->where("sess_user", $id);
		$this->dbts->where("sess_type", "LOGIN");
		$qd = $this->dbts->get("ts_sess");
		$rd = $qd->row();
		
		if(count($rd)>0){
			$jsession_fix = $rd->sess_value;
		}

		return $jsession_fix;
	}
}
