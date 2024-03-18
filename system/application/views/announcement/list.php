<script>
		jQuery(document).ready(
			function()
			{
				location = "#topannouncement";
			}
		);	
</script>
<a href="#topannouncement"></a>
<ol>
	<?php for($i=0; $i < count($rows); $i++) { ?>		 
	<li>
		<?php 
			$rows[$i]->t = dbmaketime($rows[$i]->announcement_created);
			echo date("d/m/Y H:i:s", $rows[$i]->t); 
		?>
		<br />
		<?php echo nl2br($rows[$i]->announcement_message); ?>
		<br />
	</li>
	<?php } ?>
</ol>