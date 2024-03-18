<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<title><?=$this->sess->user_name;?>@<?=$this->config->item("APPNAME"); ?></title>
	<meta name="description" content="<?=isset($metakeywords) ? $metakeywords : $this->config->item("APPDESCRIPTION"); ?>" />
	<meta name="keywords" content="<?=isset($metadescription) ? $metadescription : $this->config->item("APPKEYWORDS"); ?>" /> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 		
	<meta name="robots" content="index, follow" />
	<meta name="googlebot" content="index,follow" />
	<meta name="msnbot" content="index,follow" />
	<meta name="author" content="adilahsoft.com" />
	<meta name="copyright" content="2010 adilahsoft.com" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 		
	
    <link href="<?=base_url();?>/assets/farrasindo/images/favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?=base_url();?>/assets/farrasindo/images/favicon.ico" type="image/x-icon" rel="shortcut icon">
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/farrasindo/css/commonv1.css" />
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

        #pup {
    font-size: 10px;
    font-weight: bold;
    width:48px;
    height:33px;
    color:black;
    text-align:center;
    background-repeat:no-repeat;
    background-image: url('<?=base_url();?>assets/images/pup.png');
}
    .olControlLayerSwitcher {
    position: relative;
    top: 90px;
    right: 0px;
    width: 20em;
    font-family: sans-serif;
    font-weight: bold;
    margin-top: 90px;
    margin-left: 3px;
    margin-bottom: 3px;
    font-size: smaller;
    color: white;
    background-color: transparent;
}

.olLayerGoogleCopyright {
    left: 2px;
    bottom: 2px;
    display: none;  
}
.olLayerGooglePoweredBy {
    left: 2px;
    bottom: 15px;   
    display: none;
}
        
    </style> 
 
    </head> 
<body style="margin-top: 0px;"> 
	<?php echo $content; ?>
</body> 
</html>
