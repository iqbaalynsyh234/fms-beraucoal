<?php
include "base.php";

class Lalin extends Base {
    
    function __construct() {
        parent::Base();
    }
    
    function info() {
        /*
         $consumer_key = 'k4hyseNYCGzz1KEjJbQ';
         $consumer_secret = 'EmfBSgOfsU44gwAipldMwrWhrOYHhpMwideXDRDII';
         $token = '374322475-EECgTDz0OKt02pik0Sbhb6XRbIRNOcxerCpl1uLT';
         $secret = 'kQPdlv2y0hsi8RxuVbmAFXt2mO6DUloYNhmfRRjP2U';
         
         $params = array('consumer_key'=>$consumer_key,
         'consumer_secret'=>$consumer_secret,
         'token'=>$token,
         'secret'=>$secret
         );
         
         $this->load->library("MY_Twitter", $params);
         $home_timeline = $this->my_twitter->get('/statuses/home_timeline.json');
         */
        include 'class/EpiCurl.php';
        include 'class/EpiOAuth.php';
        include 'class/EpiTwitter.php';
        $consumer_key = 'k4hyseNYCGzz1KEjJbQ';
        $consumer_secret = 'EmfBSgOfsU44gwAipldMwrWhrOYHhpMwideXDRDII';
        $token = '374322475-EECgTDz0OKt02pik0Sbhb6XRbIRNOcxerCpl1uLT';
        $secret = 'kQPdlv2y0hsi8RxuVbmAFXt2mO6DUloYNhmfRRjP2U';
        $twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $token, $secret);
        
        $home_timeline = $twitterObj->get('/statuses/home_timeline.json');
		//print_r($home_timeline);
		$params['timeline'] = $home_timeline;
		$html = $this->load->view("lalin/view", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;	
		
		echo json_encode($callback);
		return;
    }
	
	function search(){
		$q = isset($_POST['q']) ? $_POST['q'] : "";
		
		
		
		include 'class/EpiCurl.php';
        include 'class/EpiOAuth.php';
        include 'class/EpiTwitter.php';
        $consumer_key = 'k4hyseNYCGzz1KEjJbQ';
        $consumer_secret = 'EmfBSgOfsU44gwAipldMwrWhrOYHhpMwideXDRDII';
        $token = '374322475-EECgTDz0OKt02pik0Sbhb6XRbIRNOcxerCpl1uLT';
        $secret = 'kQPdlv2y0hsi8RxuVbmAFXt2mO6DUloYNhmfRRjP2U';
        $twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $token, $secret);
        
		$html = "";
		
		if($q == ""){
			$resp = $twitterObj->get('/statuses/home_timeline.json');
			
			foreach ($resp->response as $status) {
		    	$html .= '<div class="user">';
			    $html .= '<img src="'.$status['user']['profile_image_url'].'" class="twitter_image">';
			    $html .= '<a href="http://www.twitter.com/'.$status['user']['screen_name'].'" target="_blank">'.$status['user']['screen_name'].'</a>: ';
			    $html .= twitterify($status['text']);
			    $html .= '<br/>';
			    $html .= '<div class="twitter_posted_at">Posted at:'.date("d/m/Y H:i", strtotime($status['created_at'])).'</div>';
			    $html .= '<div class="clear"></div>';
				$html .= '</div>';
		    }
		}else{
			$resp = $twitterObj->get('/search.json',  array('q' => $q));
			$response = $resp->response;
			$results = $response['results'];
			
			foreach ($results as $status) {
			
		    	$html .= '<div class="user">';
			    $html .= '<img src="'.$status['profile_image_url'].'" class="twitter_image">';
			    $html .= '<a href="http://www.twitter.com/'.$status['from_user'].'" target="_blank">'.$status['from_user'].'</a>: ';
			    $html .= twitterify($status['text']);
			    $html .= '<br/>';
			    $html .= '<div class="twitter_posted_at">Posted at:'.date("d/m/Y H:i", strtotime($status['created_at'])).'</div>';
			    $html .= '<div class="clear"></div>';
				$html .= '</div>';
			
    		}
		}
       
		
		
		$callback['html'] = $html;
		$callback['error'] = false;	
		
		echo json_encode($callback);
		return;
		
	}
    
   
}
