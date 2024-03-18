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
			showclock();
            field_onchange();
		}
	);
    
    function field_onchange()
    {
        var v = jQuery("#field").val();

        jQuery("#note_tripmileage").hide();
        jQuery("#note_history").hide();
        jQuery("#note_playback").hide();

        switch(v)
        {
            case "trip_mileage":
                jQuery("#note_tripmileage").show();
            break; 
            case "history":
                jQuery("#note_history").show();
            break;           
            case "playback":
                jQuery("#note_playback").show();
            break;           
        }
    }
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<h1>DAILY REPORT</h1>
            <br />
            <small>
                Daily Report merupakan Report yang di proses dengan menggunakan sistem cron dan dalam bentuk file (Excel) *.xls, dimana report yang
                dapat didownload adalah report yang sudah diproses sebelumnya dan maksimal satu hari sebelumnya dari tanggal sistem yang berjalan (hari ini).
            </small>
            <hr />
            
            <div class="block-border">
                <form class="block-content form" method="post" action="<?=base_url()?>download/downloadlist">
                    <h1>SELECT DAILY REPORT</h1>
                    <br /><br /> 
                    <fieldset>
                        <legend>Report List</legend>
                        <select name="field" id="field" onchange="javascript:field_onchange()">
                            <option value="trip_mileage">Trip Mileage</option>
                            <option value="playback">Rekapitulasi Data Vehicle/Day ( Playback ) Report</option>
                            <!--<option value="history">History Report ( Summary )</option>-->
                        </select>
                        <br /><br />
                         <span id="note_tripmileage" style="display: none;">
                            <small>
                            Informasi Perjalanan Vehicle, Total Jarak Yang Di Tempuh, Waktu Perjalanan,
                            Informasi Awal dan Tujuan Perjalanan.
                         </small>
                         </span>
                         <span id="note_history" style="display: none;">
                            <small>
                            Report Summary Vehicle per-interval tertentu. Apabila vehicle berada pada posisi yang sama pada
                            menit berikutnya maka data yang akan ditampilkan adalah data menit sebelumya pada posisi yang sama
                            ( Summary )
                            <br />
                            Report ini meliputi : Posisi Vehicle, Status Engine, Odometer, Speed
                         </small>
                         </span>
                          <span id="note_playback" style="display: none;">
                            <small>
                            Report Playbak merupakan Rekapitulasi data vehicle per hari,  
                            <br />
                            Report ini meliputi : Start Off, Start On, End Off, End On,
                            Off Duration, On Duration, Off Position, On Position, Trip Mileage, Cummulative Trip Mileage
                         </small>
                         </span>
                         <br /><br />
                         <input class="button red" type="submit" value="Vehicle List" />
                    </fieldset>
                </form>
            </div>
		</div>
	</div>
</div>
			

			
