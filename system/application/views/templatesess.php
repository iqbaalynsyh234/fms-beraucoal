<!doctype html>
<head> 
	<title><?=$this->sess->user_name;?>@<?=$_SERVER['SERVER_NAME']; ?><?php if (isset($title)) echo ": ".$title; ?></title>
	<meta name="description" content="<?=isset($metakeywords) ? $metakeywords : $this->config->item("APPDESCRIPTION"); ?>" />
	<meta name="keywords" content="<?=isset($metadescription) ? $metadescription : $this->config->item("APPKEYWORDS"); ?>" /> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 		
	<meta name="robots" content="index, follow" />
	<meta name="googlebot" content="index,follow" />
	<meta name="msnbot" content="index,follow" />
	<meta name="author" content="adilahsoft.com" />
	<meta name="copyright" content="2010 adilahsoft.com" />
	<?php if ($this->config->item("favicon")) { ?>
	<link rel="shortcut icon" href="<?=base_url().$this->config->item("favicon");?>" />
	<link rel="icon" href="<?=base_url().$this->config->item("favicon");?>" />
	<?php } else  { ?>
	<link rel="shortcut icon" href="<?=base_url();?>assets/images/favicon_lacakmobil.ico" />
	<link rel="icon" href="<?=base_url();?>assets/images/favicon_lacakmobil.ico" />
	<?php } ?>
    <!--
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/commonv1.css" /> 		
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/default.css" />
    -->
    
    <link href="<?php echo base_url();?>assets/newfarrasindo/css/mini3537.css?files=reset,common,form,standard,960.gs.fluid,simple-lists,block-lists,planning,table,calendars,wizard,gallery" rel="stylesheet" type="text/css" />
		
	<link rel="stylesheet" href="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/themes/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css" media="all" /> 
	<link rel="stylesheet" href="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/themes/ui-lightness/ui.theme.css" type="text/css" media="all" />
	
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script> 
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script> 
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/ui/ui.datepicker.js" type="text/javascript"></script> 
	
	<script type="text/javascript" src="<?=base_url();?>assets/js/dropdownHover.js"></script>
    <script src="<?php echo base_url();?>assets/newfarrasindo/js/libs/modernizr.custom.min.js"></script> 
	
					
    <style> 
        .olControlPanZoomBar 
        {
            margin-top: 50px;
        }
 
        .maximizeDiv 
        {
            margin-top: 20px;
        }
        
        #pup 
        {
		    font-size: 10px;
		    font-weight: bold;
		    width:48px;
		    height:33px;
		    color:black;
		    text-align:center;
		    background-repeat:no-repeat;
		    background-image: url('<?=base_url();?>assets/images/pup.png');
		}

		
    </style> 
 <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20131355-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
    </head> 
<body style="margin-top: 0px;"> 
	<?php echo $content; ?>
</body> 
</html>
