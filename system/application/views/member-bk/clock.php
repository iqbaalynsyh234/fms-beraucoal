<?php
	$months = $this->lang->line('lmonth');
?>
<script>
	jQuery(document).ready(
		function()
		{
			<?php foreach($months as $month) { ?>
				gmonths.push("<?=$month?>");		
			<?php } ?>			
			
			gclock.setDate(<?=date('j')?>);
			gclock.setMonth(<?=date('n')?>-1);
			gclock.setFullYear(<?=date('Y')?>);
			
			gclock.setHours(<?=date('G')?>);
			gclock.setMinutes(<?=date('i')?>);
			gclock.setSeconds(<?=date('s')?>);			

			runclock();
		}
	);					
	
</script>
<div style="font-size: 10pt; color: #ffffff; "><?=$this->lang->line('llogin_as')?> <b><?=$this->sess->user_name?></b> | <span id="myclock""></span></div>
