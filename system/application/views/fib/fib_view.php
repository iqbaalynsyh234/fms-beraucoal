<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
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
var total_slide = 0;
var xx = 1;
var yy = 0;
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

var cek_slide = <?=$time_slider;?>;	
//var cek_slide = 5000;	 //8 detik
setInterval("slide();", cek_slide);
	
	
jQuery(document).ready(
		function()
		{
			//showclock();
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
		jQuery.post("<?=base_url();?>fib/search/"+p, jQuery("#frmsearch").serialize(),
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
	
	
	function slide()
	{
		for(var i=1; i< total_slide; i++){
			var text_slide = "#slide_"+i;
			jQuery(text_slide).hide();
		}
		if(yy==total_slide)
		{
			yy=1;xx=1;
			var islide = "#slide_1";
			var islide_last = "#slide_"+total_slide;
			//alert(islide);
			jQuery(islide).show();
			jQuery(islide_last).hide();
			
		}
		else
		{
			yy = yy+1;
			var islide = "#slide_"+yy;
			//alert(islide);
			jQuery(islide).show();
			xx = xx+1;
		}
		
		
	}
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}
</script>

<!-- start sidebar menu -->
 			<div class="sidebar-container">
 				<?=$sidebar;?>
            </div>
			 <!-- end sidebar menu -->

<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    
                     <div class="row">
                    	<div class="col-sm-12">
                             <div class="card-box">
                                 <div class="card-body ">
                                 	<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		
										<center><h2>FLEET INFORMATION BOARD</h2></center>
										
									</form>
									<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
										<div id="result"></div>	
								 </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->