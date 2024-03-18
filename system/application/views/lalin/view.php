<style>
.twitter_container {color: #444;font-size: 12px;width: 600px;margin: 0 auto;}
.twitter_container a {color: #0066CC;font-weight: bold;}
.twitter_status {height: 60px;padding: 6px;border-bottom: solid 1px #DEDEDE;}
.twitter_image {float: left;margin-right: 14px;border: solid 2px #DEDEDE;width: 50px;height: 50px;}
.twitter_posted_at {font-size: 11px;padding-top: 4px;color: #999;}
.twitter_thumb{float:left;margin-right:20px;margin-bottom:0px;}
.user{background-color:#c5e5f0;margin-bottom:10px;border-bottom:;padding:10px;}
.clear{clear:both;}
#search{padding:5px 15px;background-color:#1a1a1a;margin-bottom:10px;}
</style>
<script>
function frmlalin_onsubmit()
{
	jQuery("#result").html("");
	jQuery("#result").html("<?=$this->lang->line('lwait_loading_data');?>");
	jQuery.post("<?=base_url()?>lalin/search", jQuery("#frmsearchlalin").serialize(),
	function(r)
	{
			jQuery("#result").html(r.html);			
	}
	, "json"
	);
						
	return false;
}
</script>
<div class='twitter_container'>

<div id="search">
<form id="frmsearchlalin" onsubmit="javascript: return frmlalin_onsubmit()">
  <img src="<?=base_url();?>assets/images/SearchInfoLalin.png" /> 
  <input type="text" name="q" id="searchbox" />
  <input type="submit" name="submit" id="submit" value=" Search " />
</form>
</div>
<div id="result">
<?php
    foreach ($timeline->response as $status) {
    	echo '<div class="user">';
	    echo '<img src="'.$status['user']['profile_image_url'].'" class="twitter_image">';
	    echo '<a href="http://www.twitter.com/'.$status['user']['screen_name'].'" target="_blank">'.$status['user']['screen_name'].'</a>: ';
	    echo twitterify($status['text']);
	    echo '<br/>';
	    echo '<div class="twitter_posted_at">Posted at:'.date("d/m/Y H:i", strtotime($status['created_at'])).'</div>';
	    echo '<div class="clear"></div>';
		echo '</div>';
    }
?>
</div>
</div>