<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<title><?=$this->sess->user_name;?>@<?=$this->config->item("APPNAME"); ?></title>
	<meta name="description" content="<?=$this->config->item("APPDESCRIPTION"); ?>" />
	<meta name="keywords" content="<?=$this->config->item("APPKEYWORDS"); ?>" /> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 		
	
    <link href="<?=base_url();?>assets/images/favicon.ico" type="image/x-icon" rel="icon" />
    <link href="<?=base_url();?>assets/images/favicon.ico" type="image/x-icon" rel="shortcut icon" />     
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/commonv1.css" /> 		
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/farrasindo/css/default.css" />
		
	<link rel="stylesheet" href="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/themes/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css" media="all" /> 
	<link rel="stylesheet" href="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/themes/ui-lightness/ui.theme.css" type="text/css" media="all" />
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script> 
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script> 
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/ui/ui.datepicker.js" type="text/javascript"></script> 
	<script type="text/javascript" src="<?=base_url();?>assets/js/dropdownHover.js"></script>  
	
					
    <style> 
        .olControlPanZoomBar {
            margin-top: 50px;
        }
 
        .maximizeDiv {
            margin-top: 20px;
        }
        
    </style> 
 
    </head> 
<body style="margin-top: 0px;"> 
	<?php echo $content; ?>
</body> 
</html>
