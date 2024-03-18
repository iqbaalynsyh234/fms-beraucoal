<style>
table2 {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 50%;
}

table td, #customers th {
    border: 1px solid #ddd;
    padding: 8px;
}

table tr:nth-child(even){background-color: #f2f2f2;}

table tr:hover {background-color: #ddd;}

table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}
</style>
<script>	

jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}

var cek_request = 300000;	 //10 detik = 10.000 -> 5 menit = 5*60000 = 300.000
setInterval("page(0);", cek_request);
	
	
jQuery(document).ready(
		function()
		{
			showclock();
			page(0);			
		}
	);
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>fibdetail/search/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				total_slide = r.total_slide_real;
				
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);		
				jQuery("#total_white").html(r.total_white);
				jQuery("#total_green").html(r.total_green);
				jQuery("#total_blue").html(r.total_blue);
				jQuery("#total_yellow").html(r.total_yellow);
				jQuery("#total_red").html(r.total_red);
				
				window.localStorage.removeItem("slideaktif");
				
			}
			, "json"
		);
	}
	
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		
		<center><h1>FLEET INFORMATION BOARD (DETAIL)</h1></center>
		
	</form>
		<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		<div id="result"></div>
	</div>
</div>
