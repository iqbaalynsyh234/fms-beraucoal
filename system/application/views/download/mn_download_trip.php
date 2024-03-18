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
    
	jQuery(document).ready(
		function()
		{
            jQuery("#sdate").datepicker(
                {
                            dateFormat: 'dd-mm-yy'
                        ,     showOn: 'button'
                        ,     buttonImage: '<?=base_url()?>assets/images/calendar.gif'
                        ,     buttonImageOnly: true
                        ,    beforeShow: 
                                function() 
                                {    
                                    jQuery('#ui-datepicker-div').maxZIndex();
                                }
                }
                );    
            
            jQuery("#edate").datepicker(
                {
                            dateFormat: 'dd-mm-yy'
                        ,     showOn: 'button'
                        ,     buttonImage: '<?=base_url()?>assets/images/calendar.gif'
                        ,     buttonImageOnly: true
                        ,    beforeShow: 
                                function() 
                                {    
                                    jQuery('#ui-datepicker-div').maxZIndex();
                                }
                }
                );
                
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
		
        jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
        jQuery.post("<?=base_url()?>download/trip_mileage_available/"+p, jQuery("#frmsearch").serialize(), 
            function(r)
            {
                jQuery("#listresult").html(r.html);
                jQuery("#total").html(r.total);                
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
		<div class="block-border">
		    <form class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">	
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
                <h1><?php echo "Trip Mileage Download Available :";?> (<span id="total"></span>)</h1>
                <h2><?=$this->lang->line("lsearch"); ?></h2>    
                <table cellpadding="10" class="tablelist">
                    <tr>
                        <td><?php echo "Search By"; ?></td>
                        <td>&nbsp;</td>
                        <td>
                            <select name="vehicle" id="vehicle">
                                <option value="all">- Select Vehicle -</option>
                                <?php
                                    if (isset($vehicle) && count($vehicle)>0)
                                    {
                                        for ($i=0;$i<count($vehicle);$i++)
                                        {
                                ?>
                                        <option value="<?php echo $vehicle[$i]->vehicle_id; ?>">
                                            <?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no; ?>
                                        </option>
                                <?php
                                        }
                                    }
                                ?>
                                <option value="vehicle">Vehicle</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Date :</td>
                        <td>&nbsp;</td>
                        <td>
                            <input size="10" maxlength="10" type="text" name="sdate" id="sdate" class="date-pick" />
                            s/d 
                            <input size="10" maxlength="10" type="text" name="edate" id="edate" class="date-pick" />
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><input class="button red" type="submit" value="Search Available" /></td>
                    </tr>
                </table>
            </form>
                <div id="listresult"></div>
		</div>
	</div>
</div>
			

			