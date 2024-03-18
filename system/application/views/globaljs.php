<script language="JavaScript">
	function cutoffengine(id, status)
	{
		showdialog();
		
		jQuery.post("<?php echo base_url(); ?>vehicle/cutoffengine", {id: id, status: status}, 
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return;
				}	
		
				showdialog(r.html, "<?php echo $this->lang->line("lcutoffengine"); ?>", 0, 0, "", true);
			}
			, "json"
		);
	}
</script>
