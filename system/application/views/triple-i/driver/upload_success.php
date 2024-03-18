<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="main" style="margin: 20px;"><br />

	<h3>Your file was successfully uploaded!</h3>

	<ul>
		<?php foreach($upload_data as $item => $value):?>
		<li><?php echo $item;?>: <?php echo $value;?></li>
		<?php endforeach; ?>
	</ul>

	</div>
</div>