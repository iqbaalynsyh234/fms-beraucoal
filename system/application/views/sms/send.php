<?php 
		$smsserver = $this->smsmodel->getSMSServer(); 
		if ($smsserver == "mondial") {
?>
<?php echo "<?"; ?>xml version="1.0"?>
<smses>
	<apikey><?php echo $this->config->item("SMS_API_KEY"); ?></apikey>
	<sms>
		<destination>
			<?php foreach($dest as $hp) { ?>
			<to><?php echo $hp; ?></to>
			<?php } ?>
		</destination>	
		<message><![CDATA[<?php echo $content; ?>]]></message>
	</sms>
</smses>
<?php 	} 
		else 		
		{ 
			printf("%s\1%s", implode("|", $dest), $content);
		}
