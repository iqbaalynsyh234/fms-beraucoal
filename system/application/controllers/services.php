<?php
include "base.php";

class Services extends Base {
	function __construct()
	{
		parent::__construct();	
		
	}

	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        //$url = "http://lacak-mobil.com/telegram/telegram_directpost";
		//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
        //$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
		//$url = "http://admin.abditrack.com/telegram/telegram_directpost";
		$url = $this->config->item('url_send_telegram');
		
        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);                                                           
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);	//new
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));  
        $result = curl_exec($ch);
        
        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
    }

	function check_port_tcp($servername="")
	{
		$start_time = date("Y-m-d H:i:s");
		$telegram_group = $this->config->item('TELEGRAM_SERVICE_ALERT');
		$protocol = "TCP";
		$title = "";
		$sleep_time = 20; //sec
		$output  = array();
		$options = ( strtolower( trim( @PHP_OS ) ) === 'linux' ) ? '-atn' : '-an';

		ob_start();
		system( 'netstat '.$options );

		foreach( explode( "\n", ob_get_clean() ) as $line )
		{
			$line  = trim( preg_replace( '/\s\s+/', ' ', $line ) );
			$parts = explode( ' ', $line );
			
			if( count( $parts ) > 3 )
			{
				$state   = strtolower( array_pop( $parts ) );
				$foreign = array_pop( $parts );
				$local   = array_pop( $parts );

				if( !empty( $state ) && !empty( $local ) )
				{
					$final = explode( ':', $local );
					$port  = array_pop( $final );

					if( is_numeric( $port ) )
					{
						$output[ $state ][ $port ] = $port;
					}
				}
			}
		}
		$listen_port = $output['listen'];
		
		//get master data port
		$this->db->distinct();
		$this->db->order_by("config_port_value","asc");
		$this->db->select("config_server,config_port_value,config_stop_service,config_start_service,config_protocol_type");
		$this->db->where("config_server", $servername);
		$this->db->where("config_protocol",$protocol);
		//$this->db->where("config_port_value",17018);
		$this->db->where("config_active",1);
		$q = $this->db->get("config_port");
		$data = $q->result();
		if(count($data)>0){
			for($i=0;$i<count($data);$i++)
			{
				$port = $data[$i]->config_port_value;
				$port_type = $data[$i]->config_protocol_type;
				$command_stop = $data[$i]->config_stop_service;
				$command_start = $data[$i]->config_start_service;
				//$command_new = "nohup php -c /etc/ /home/lacakmobil/supersocket/VT200_TIB_17018/VT200_TIB_17018.php >> /home/lacakmobil/supersocket/VT200_TIB_17018/VT200_TIB_17018.log &";
				$title = "";
				printf("DATA %s of %s : %s %s %s \n",$i+1,count($data), $servername, $protocol, $port);
				
				if (!in_array(strtoupper($port), $listen_port)){
					$title = "SERVICE MATI";
					printf("SERVICE MATI \r\n");
					if($command_stop != "" || $command_start != "")
					{
						system($command_stop);
						sleep($sleep_time);
						system($command_start);
						printf("AUTO RESTART SERVICE\r\n");
						
						/* exec($command_new);
						printf("START SERVICES \r\n"); */
						
						$message = urlencode(
									"".$title." \n".
									"PORT : ".$port." ".$protocol." \n".
									"LOKASI : ".$servername." \n".
									"NOTE : SUDAH DI RESTART OTOMATIS \n".
									"TOLONG CEK DI WEB \n"
									);
						
						$sendtelegram = $this->telegram_direct($telegram_group,$message);
						printf("===SENT TELEGRAM OK\r\n");
					}
					else
					{
						printf("TIDAK ADA COMMAND RESTART \r\n");
						$message = urlencode(
									"".$title." \n".
									"PORT : ".$port." ".$protocol." \n".
									"LOKASI : ".$servername." \n".
									"NOTE : TIDAK ADA CONFIG COMMAND RESTART \n".
									"TOLONG JALANIN MANUAL \n"
									);
						
						$sendtelegram = $this->telegram_direct($telegram_group,$message);
						printf("===SENT TELEGRAM OK\r\n");
					}
				}
				else
				{
					printf("SERVICE HIDUP \r\n");
				}
				printf("=================== \r\n");
				
				
			}
			
				$end_time = date("Y-m-d H:i:s");
				printf("FINISH %s to %s \n", $start_time, $end_time);
				printf("=================== \r\n");
			
		}
	}
	
	function check_port_tcp_new($servername="")
	{
		$start_time = date("Y-m-d H:i:s");
		$telegram_group = $this->config->item('TELEGRAM_SERVICE_ALERT');
		$protocol = "TCP";
		$title = "";
		$sleep_time = 20; //sec
		$output  = array();
		$options = ( strtolower( trim( @PHP_OS ) ) === 'linux' ) ? '-atn' : '-an';

		ob_start();
		system( 'netstat '.$options );

		foreach( explode( "\n", ob_get_clean() ) as $line )
		{
			$line  = trim( preg_replace( '/\s\s+/', ' ', $line ) );
			$parts = explode( ' ', $line );
			
			if( count( $parts ) > 3 )
			{
				$state   = strtolower( array_pop( $parts ) );
				$foreign = array_pop( $parts );
				$local   = array_pop( $parts );

				if( !empty( $state ) && !empty( $local ) )
				{
					$final = explode( ':', $local );
					$port  = array_pop( $final );

					if( is_numeric( $port ) )
					{
						$output[ $state ][ $port ] = $port;
					}
				}
			}
		}
		$listen_port = $output['listen'];
		
		//get master data port
		$this->db->distinct();
		$this->db->order_by("config_port_value","asc");
		$this->db->select("config_server,config_port_value,config_stop_service,config_start_service,config_protocol_type");
		$this->db->where("config_server", $servername);
		$this->db->where("config_protocol",$protocol);
		//$this->db->where("config_port_value",17013);
		$this->db->where("config_active",1);
		$q = $this->db->get("config_port");
		$data = $q->result();
		if(count($data)>0){
			for($i=0;$i<count($data);$i++)
			{
				$port = $data[$i]->config_port_value;
				$port_type = $data[$i]->config_protocol_type;
				$command_stop = $data[$i]->config_stop_service;
				$command_start = $data[$i]->config_start_service;
				//$command_start = "php -c /etc/ /home/lacakmobil/supersocket/VT200_TIB_17013/VT200_TIB_17013.php >> /home/lacakmobil/supersocket/VT200_TIB_17013/VT200_TIB_17013.log &";
				$title = "";
				printf("DATA %s of %s : %s %s %s \n",$i+1,count($data), $servername, $protocol, $port);
				
				if (!in_array(strtoupper($port), $listen_port)){
					$title = "SERVICE MATI";
					printf("SERVICE MATI \r\n");
					if($command_stop != "" || $command_start != "")
					{
						shell_exec('sudo '.$command_stop);
						sleep($sleep_time);
						//system('sudo '.$command_start);
						shell_exec('sudo '.$command_start);
						printf("AUTO RESTART SERVICE\r\n");
						
						printf("START SERVICES \r\n");
						
						$message = urlencode(
									"".$title." \n".
									"PORT : ".$port." ".$protocol." \n".
									"LOKASI : ".$servername." \n".
									"NOTE : SUDAH DI RESTART OTOMATIS \n".
									"TOLONG CEK DI WEB \n"
									);
						
						if($port == '5100'){
							$sendtelegram = $this->telegram_direct("-438080733",$message);
						}else{
							$sendtelegram = $this->telegram_direct($telegram_group,$message);
						}
						
						printf("===SENT TELEGRAM OK\r\n");
					}
					else
					{
						printf("TIDAK ADA COMMAND RESTART \r\n");
						$message = urlencode(
									"".$title." \n".
									"PORT : ".$port." ".$protocol." \n".
									"LOKASI : ".$servername." \n".
									"NOTE : TIDAK ADA CONFIG COMMAND RESTART \n".
									"TOLONG JALANIN MANUAL \n"
									);
						
						if($port == '5100'){
							$sendtelegram = $this->telegram_direct("-438080733",$message);
						}else{
							$sendtelegram = $this->telegram_direct($telegram_group,$message);
						}
						printf("===SENT TELEGRAM OK\r\n");
					}
				}
				else
				{
					printf("SERVICE HIDUP \r\n");
				}
				printf("=================== \r\n");
				
				
			}
			
				$end_time = date("Y-m-d H:i:s");
				printf("FINISH %s to %s \n", $start_time, $end_time);
				printf("=================== \r\n");
			
		}
	}
	
	function check_port_udp($servername="")
	{
		$telegram_group = $this->config->item('TELEGRAM_SERVICE_ALERT');
		$protocol = "UDP";
		$title = "";
		$sleep_time = 20; //sec
		$output = array();
		$options = ( strtolower( trim( @PHP_OS ) ) === 'linux' ) ? '-aun' : '-an';

		ob_start();
		system( 'netstat '.$options );
		$i = 0;
		foreach( explode( "\n", ob_get_clean() ) as $line )
		{
			$line  = trim( preg_replace( '/\s\s+/', ' ', $line ) );
			$parts = explode( ' ', $line );
			
			if( count( $parts ) == 5 )
			{
				$i = $i+1; 
				if( !empty( $parts ) && !empty( $parts ) )
				{
					$final = explode( ':', $parts[3] );
					$port  = array_pop( $final );
					
					if( is_numeric( $port ) )
					{
						$output[$i] = $port;
					}
				}
				
			}
		}
		$listen_port = $output;
		
		//get master data port
		$this->db->distinct();
		$this->db->order_by("config_port_value","asc");
		$this->db->select("config_server,config_port_value,config_stop_service,config_start_service,config_protocol_type");
		$this->db->where("config_server", $servername);
		$this->db->where("config_protocol",$protocol);
		$this->db->where("config_active",1);
		$q = $this->db->get("config_port");
		$data = $q->result();
		if(count($data)>0){
			for($i=0;$i<count($data);$i++)
			{
				$port = $data[$i]->config_port_value;
				$port_type = $data[$i]->config_protocol_type;
				$command_stop = $data[$i]->config_stop_service;
				$command_start = $data[$i]->config_start_service;
				
				$title = "";
				printf("DATA %s of %s : %s %s %s \n",$i+1,count($data), $servername, $protocol, $port);
				
				if (!in_array(strtoupper($port), $listen_port)){
					$title = "SERVICE MATI";
					printf("SERVICE MATI \r\n");
					if($command_stop != "" || $command_start != "")
					{
						system($command_stop);
						sleep($sleep_time);
						system($command_start);
						
						printf("AUTO RESTART SERVICE\r\n");
						$message = urlencode(
									"".$title." \n".
									"PORT : ".$port." ".$protocol." \n".
									"LOKASI : ".$servername." \n".
									"NOTE : SUDAH DI RESTART OTOMATIS \n".
									"TOLONG CEK DI WEB \n"
									);
						
						$sendtelegram = $this->telegram_direct($telegram_group,$message);
						printf("===SENT TELEGRAM OK\r\n");
					}
					else
					{
						printf("TIDAK ADA COMMAND RESTART \r\n");
						$message = urlencode(
									"".$title." \n".
									"PORT : ".$port." ".$protocol." \n".
									"LOKASI : ".$servername." \n".
									"NOTE : TIDAK ADA CONFIG COMMAND RESTART \n".
									"TOLONG JALANIN MANUAL \n"
									);
						
						$sendtelegram = $this->telegram_direct($telegram_group,$message);
						printf("===SENT TELEGRAM OK\r\n");
					}
					
				}
				else
				{
					printf("SERVICE HIDUP \r\n");
				}
				printf("=================== \r\n");
				
			}
			
		}
		
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
