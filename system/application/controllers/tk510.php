<?php
include "base.php";

class Tk510 extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->helper('common');
		$this->load->helper('email');
		
	}
	
	function create_picture_old($dblive="", $vdevice = "")
	{
		$this->db = $this->load->database($dblive, TRUE);
		
		$this->db->select("picture_id,picture_pack_seq_no,picture_device,picture_size_byte,picture_seq_total,picture_datetime");
		$this->db->order_by("picture_id","asc");
		$this->db->where("picture_pack_seq_no",0);//main data
		$this->db->where("picture_status",0);//belum di prosess
		//$this->db->limit(3);//belum di prosess
		$q = $this->db->get("webtracking_gps_picture");
		$rows = $q->result();
		$total = count($rows);
		printf("total master data %s \n", $total);
		
		for($x=0; $x<$total; $x++)
		{
					//get new status
					$new_status = $this->get_new_status($dblive,$rows[$x]->picture_id);
					if($new_status == 0){
						printf("process %s/%s  %s %s %s %s \n", $x+1 , $total, $rows[$x]->picture_device, $rows[$x]->picture_id, $rows[$x]->picture_size_byte, $rows[$x]->picture_datetime);	
						$imei = trim($rows[$x]->picture_device);
						$id = $rows[$x]->picture_id;
						$size = $rows[$x]->picture_size_byte;
						$seq_total = $rows[$x]->picture_seq_total;
						$datetime = $rows[$x]->picture_datetime;
						$pic_hexa = "";
						
						//get all seq data
						$pic_hexa = $this->get_all_data($dblive,$imei,$size,$seq_total,$datetime);
						
						//convert to PNG
						//printf("pic hexa %s \n", $pic_hexa);
						if($pic_hexa != ""){
							$binary = $this->hextobin(trim($pic_hexa));
							
							$nowdate = date("Ymd", strtotime($datetime));
							$nowtime = date("His", strtotime($datetime));
							$dir_img = "/home/lacakmobil/public_html/lacak-mobil.com/public/assets/snap/img/";
							$domain_img = "https://lacak-mobil.com/assets/snap/img/";
							$name_img = $imei."_".$nowdate."_".$nowtime."_".$id.".png";
							
							file_put_contents($dir_img.$name_img, $binary);
							$url_img = $domain_img."".$name_img;
							
							unset($insert);
			
							$insert['picture_imei'] = $imei;
							$insert['picture_size'] = $size;
							$insert['picture_datetime'] = $datetime;
							$insert['picture_created'] = date("Y-m-d H:i:s");
							$insert['picture_url'] = $url_img;
							$this->db->insert("webtracking_picture", $insert);
							
							printf("done create picture %s \n", $imei, $url_img);
							printf("============================= \n");
						}
						else
						{
							printf("skip hexa kosong %s \n", $imei);
							printf("============================= \n");
						}
					}
					else
					{
							printf("sudah pernah diproses %s \n", $imei);
							printf("============================= \n");
					}
			
					
					
		}
		
		$this->db->close();
	}
	
	function create_picture($groupname="")
	{
		$this->db = $this->load->database($dblive, TRUE);
		
		$this->db->select("picture_id,picture_pack_seq_no,picture_device,picture_size_byte,picture_seq_total,picture_datetime");
		$this->db->order_by("picture_id","asc");
		$this->db->where("picture_pack_seq_no",0);//main data
		$this->db->where("picture_status",0);//belum di prosess
		//$this->db->limit(3);//belum di prosess
		$q = $this->db->get("webtracking_gps_picture");
		$rows = $q->result();
		$total = count($rows);
		printf("total master data %s \n", $total);
		
		for($x=0; $x<$total; $x++)
		{
					//get new status
					$new_status = $this->get_new_status($dblive,$rows[$x]->picture_id);
					if($new_status == 0){
						printf("process %s/%s  %s %s %s %s \n", $x+1 , $total, $rows[$x]->picture_device, $rows[$x]->picture_id, $rows[$x]->picture_size_byte, $rows[$x]->picture_datetime);	
						$imei = trim($rows[$x]->picture_device);
						$id = $rows[$x]->picture_id;
						$size = $rows[$x]->picture_size_byte;
						$seq_total = $rows[$x]->picture_seq_total;
						$datetime = $rows[$x]->picture_datetime;
						$pic_hexa = "";
						
						//get all seq data
						$pic_hexa = $this->get_all_data($dblive,$imei,$size,$seq_total,$datetime);
						
						//convert to PNG
						//printf("pic hexa %s \n", $pic_hexa);
						if($pic_hexa != ""){
							$binary = $this->hextobin(trim($pic_hexa));
							
							$nowdate = date("Ymd", strtotime($datetime));
							$nowtime = date("His", strtotime($datetime));
							$dir_img = "/home/lacakmobil/public_html/lacak-mobil.com/public/assets/snap/img/";
							$domain_img = "https://lacak-mobil.com/assets/snap/img/";
							$name_img = $imei."_".$nowdate."_".$nowtime."_".$id.".png";
							
							file_put_contents($dir_img.$name_img, $binary);
							$url_img = $domain_img."".$name_img;
							
							unset($insert);
			
							$insert['picture_imei'] = $imei;
							$insert['picture_size'] = $size;
							$insert['picture_datetime'] = $datetime;
							$insert['picture_created'] = date("Y-m-d H:i:s");
							$insert['picture_url'] = $url_img;
							$this->db->insert("webtracking_picture", $insert);
							
							printf("done create picture %s \n", $imei, $url_img);
							printf("============================= \n");
						}
						else
						{
							printf("skip hexa kosong %s \n", $imei);
							printf("============================= \n");
						}
					}
					else
					{
							printf("sudah pernah diproses %s \n", $imei);
							printf("============================= \n");
					}
			
					
					
		}
		
		$this->db->close();
	}
	
	
	function delete_gpspicture($groupname="")
	{
		ini_set('memory_limit', '2G');
		$startdate = date("Y-m-d H:i:s");
		printf("=== START %s \n", $startdate);
		
		$yesterday = date('Y-m-d H:i:s', strtotime("-1 day", strtotime(date("Y-m-d 00:00:00"))));
		
		$this->db = $this->load->database("default",true);
		$this->db->where("port_status",1);
		$this->db->where("port_group",$groupname);
		$q = $this->db->get("cron_port_gpscamera");
		$data = $q->result();
		$total = count($data);
		
		for($i=0;$i<$total; $i++)
		{
			$table_name = $data[$i]->port_dblive;
			printf("=== DB LIVE : %s - %s \n", $table_name, $yesterday);
			$this->db = $this->load->database($table_name,true);
			$this->db->order_by("picture_id","asc");
			$this->db->where("picture_status",1);
			$this->db->where("picture_datetime <=", $yesterday);
			$q_hex = $this->db->get("gps_picture");
			$data_hex = $q_hex->result();
			$total_data_hex = count($data_hex);
			printf("=== TOTAL DATA : %s \n", $total_data_hex);
		
			if($q_hex->num_rows > 0)
			{
				printf("=== PROSES DELETE... %s ", $q_hex->num_rows);
				
				$this->db->order_by("picture_id","asc");
				$this->db->where("picture_status",1);
				$this->db->where("picture_datetime <=", $yesterday);
				$this->db->delete("gps_picture");
				printf("=== DELETE SUCCESS \n");
			}else{
				printf("=== NO DATA until yesterday \n");
			}
			
		}
				
		$enddate = date("Y-m-d H:i:s");
		printf("=== FINISH %s , %s \n", $startdate,$enddate);
	}
		
	function get_all_data($dblive,$imei,$totalsize,$totalseq,$starttime)
	{
		//mengumpulkan data hexa 0 - total seq
		$this->db = $this->load->database($dblive, TRUE);
		$this->db->order_by("picture_pack_seq_no","asc");
		$this->db->where("picture_device",$imei);
		$this->db->where("picture_size_byte",$totalsize);
		$this->db->where("picture_seq_total",$totalseq);
		$this->db->where("picture_datetime >=",$starttime);
		$this->db->where("picture_status",0);
		//$this->db->limit($totalseq);
		$q_seq = $this->db->get("webtracking_gps_picture");
		$rows_seq = $q_seq->result();
		$total_rows_seq = count($rows_seq);
		printf("total gps data %s total seq %s - %s \n", $total_rows_seq, $totalseq, $imei);
		$pic_data = "";
		
		if($total_rows_seq == $totalseq){
			for($j=0; $j<$total_rows_seq; $j++){
				$pic_data .= $rows_seq[$j]->picture_text;
				
				//update status
				unset($update);
				$update['picture_status'] = 1;
				$update['picture_respon'] = date("Y-m-d H:i:s");
				
				$this->db->where("picture_id", $rows_seq[$j]->picture_id);
				$this->db->update("webtracking_gps_picture", $update);
			}
		}
		else
		{
			printf("skip : total gps data %s <> total seq %s - %s \n",$total_rows_seq, $totalseq, $imei);
		}
		
		return $pic_data;
	}
	function get_new_status($dblive,$id)
	{
		
		$this->db = $this->load->database($dblive, TRUE);
		$this->db->select("picture_id, picture_status");
		$this->db->where("picture_id",$id);
		$q_st = $this->db->get("webtracking_gps_picture");
		$rows_st = $q_st->row();
		$total_rows_st = count($rows_st);
		$status = "";
		if($total_rows_st>0){
			$status = $rows_st->picture_status;
		}
		
		return $status;
	}
	function hextobin($hexstr)  
	{ 
        $n = strlen($hexstr); 
        $sbin="";   
        $i=0; 
        while($i < $n) {       
        $a =substr($hexstr,$i,2);           
            $c = pack("H*",$a); 
            if ($i == 0) {
                $sbin = $c;
            } else {
                $sbin .= $c;
            } 
            $i += 2; 
        } 
        return $sbin; 
    }
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
