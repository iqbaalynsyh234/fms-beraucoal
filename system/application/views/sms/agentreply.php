Berikut agen Anda: 
<?php for($i=0; $i < count($agents); $i++) { ?>
 <?php echo $agents[$i]->user_name; ?> <?php echo $agents[$i]->user_mobile; ?> <?php echo $agents[$i]->user_address; ?> <?php echo $agents[$i]->user_city; ?> <?php echo $agents[$i]->user_province; ?>
<?php } ?>
