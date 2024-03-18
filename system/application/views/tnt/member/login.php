<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Welcome to TNT's Vehicle Tracking System</title>
        <link type="image/x-icon" href="http://www.tnt.com/__images/favicon.ico" rel="shortcut icon">
        <link media="screen" href="http://www.tnt.com/__css/screen.css" type="text/css" rel="stylesheet">
        <link media="print" href="http://www.tnt.com/__css/print.css" type="text/css" rel="stylesheet">
        <link media="handheld, braille, embossed, tty, tv" href="http://www.tnt.com/__css/handheld.css" type="text/css" rel="stylesheet">
        <link media="screen" href="http://www.tnt.com/__css/gb_styles.css" type="text/css" rel="stylesheet">
        <link media="screen" href="http://www.tnt.com/__css/newco_inner-style.css" type="text/css" rel="stylesheet">
        <link href="http://www.tnt.com/__css/newco_shadowbox.css" type="text/css" rel="stylesheet">
        <link media="screen" href="http://www.tnt.com/__css/newco_sifr.css" type="text/css" rel="stylesheet">
        <link media="screen" href="http://www.tnt.com/__css/tt.css" type="text/css" rel="stylesheet">
		
		<!-- login css -->
		<link media="screen" href="<?=base_url()?>assets/css/style_login.css" type="text/css" rel="stylesheet">
		<!-- end login css -->
        <!--[if IE]>
            <link href="http://www.tnt.com/__css/newco_ie.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->
        <!--[if IE 7]>
            <link href="http://www.tnt.com/__css/newco_ie7.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->
        <!--[if IE 6]>
            <link href="http://www.tnt.com/__css/newco_ie6.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->
        <link type="text/css" rel="stylesheet" href="http://webfonts.fontslive.com/css/8099e8cc-9d1f-4d8e-9495-04751a877bde.css">
        </script>
        <script src="http://www.tnt.com/__js/jquery.js" type="text/javascript">
        </script>
        <script src="http://www.tnt.com/__js/jquery-ui.js" type="text/javascript">
        </script>
        <script src="http://www.tnt.com/__js/cookie.js" type="text/javascript">
        </script>
        <script src="http://www.tnt.com/__js/shadowbox.js" type="text/javascript">
        </script>
        <script src="http://www.tnt.com/__js/tt.js" type="text/javascript">
        </script>
		<script>
			function frmlogin_onsubmit()
				{
					jQuery("#wait").show();
					jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
						function(r)
						{
							jQuery("#wait").hide();
							if (r.error)
							{
								alert(r.message);
								return;
							}

							location = r.redirect;
						}
						, "json"
					);
					return false;
				}
		</script>
    </head>
    <body class="ptStandard">
        <div id="page">
            <div style="padding-top: 11px; padding-left: 9px;" id="header">
                <a href="http://www.tnt.com"><img border="0" title="TNT - Sure we Can" alt="TNT Portal" src="http://www.tnt.com/content/dam/corporate/images/logo/tnt_logo_tcm178-384991.gif"></a>
            </div><hr class="hide">
            <div id="pageBody">
                <div style="height: 37px;">
                </div>
                <div class="tt_landing-page">
                    <!-- END TECHTRIBE -->	
					&nbsp;
					
					<!-- left side -->
					<div style="float:left;">				   
				    <div class="block small center login">
                        <div class="block_head">
                            <div class="bheadl">
                            </div>
                            <div class="bheadr">
                            </div>
                            <h2>Login</h2>
                           	<ul>
								<li class="nobg"><i>Vehicle Tracking System</i></li>
							</ul>
                        </div>
                        <!-- .block_head ends -->
                        <div class="block_content">
                            
                            <form id="frmlogin" onSubmit="javascript: return frmlogin_onsubmit(this)">
                                <p>
                                    <label>
                                        Username:
                                    </label>
                                    <br>
                                    <input type="text" id="username" name="username" value="" class="text">
                                </p>
                                <p>
                                    <label>
                                        Password:
                                    </label>
                                    <br>
                                    <input type="password" name="userpass" id="userpass" value="" class="text">
                                </p>
                                <p>
                                    <input type="submit" value="Login" class="submit">
                                    <img id="wait" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                                </p>
                            </form>
                        </div>
                        <!-- .block_content ends -->
                        <div class="bendl">
                        </div>
                        <div class="bendr">
                        </div>
                    </div>
					</div>
					<!-- end left side -->
					<div>
                   <div class="worldmap"> </div>
				   </div>
                    <!-- end template -->
                </div>
                <hr class="hide">
                <div class="clr">
                </div>
                <div id="footer">
                    <ul>
                        <li>
                            <a title="" href="http://www.tnt.com/corporate/en/site/help/privacypolicy.html" target="_blank">privacy policy</a>
                        </li>
                        <li>
                            <a title="" href="http://www.tnt.com/corporate/en/site/help/termsofuse.html" target="_blank">terms of use</a>
                        </li>
                    </ul>
                    <span>Intellectual and other property rights to the information contained in this site are held by TNT Holdings B.V. with all rights reserved &copy; 2011 </span>
                </div>
            </div>
        </div>
    </body>
</html>
