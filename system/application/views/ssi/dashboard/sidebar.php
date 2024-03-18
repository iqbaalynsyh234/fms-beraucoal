<?php
$company = $this->dashboardmodel->getcompany_byowner();
?>
<div class="sidemenu-container navbar-collapse collapse fixed-menu">
	                <div id="remove-scroll">
	                    <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
	                        <li class="sidebar-toggler-wrapper hide">
	                            <div class="sidebar-toggler">
	                                <span></span>
	                            </div>
	                        </li>
	                        <li class="sidebar-user-panel">
	                            <div class="user-panel">
	                                <div class="row">
                                            <div class="sidebar-userpic">
                                                <img src="<?php echo base_url();?>assets/dashboard/assets/img/dp.gif" class="img-responsive" alt=""> </div>
                                        </div>
                                        <div class="profile-usertitle">
                                            <div class="sidebar-userpic-name"><?=$this->sess->user_name;?></div>
                                            <!--<div class="profile-usertitle-job"> Manager </div>-->
                                        </div>
                                        <div class="sidebar-userpic-btn">
									        <a class="tooltips" href="<?=base_url()?>user/add/<?=$this->sess->user_id;?>" data-placement="top" data-original-title="Profile" target="_blank">
									        	<i class="material-icons">person_outline</i>
									        </a>
									        <!--<a class="tooltips" href="email_inbox.html" data-placement="top" data-original-title="Mail">
									        	<i class="material-icons">mail_outline</i>
									        </a>
									        <a class="tooltips" href="chat.html" data-placement="top" data-original-title="Chat">
									        	<i class="material-icons">chat</i>
									        </a>-->
									        <a class="tooltips" href="<?=base_url()?>member/logout" data-placement="top" data-original-title="Logout">
									        	<i class="material-icons">input</i>
									        </a>
									    </div>
	                            </div>
	                        </li>
	                        <li class="menu-heading">
			                	<span>-- Menu</span>
			                </li>
	                        <li class="nav-item start">
	                            <a href="<?=base_url();?>dashboard" class="nav-link nav-toggle">
	                                <i class="material-icons">dashboard</i>
	                                <span class="title">Dashboard</span>
                                	
	                            </a>
	                            
	                        </li>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="material-icons">desktop_mac</i>
	                                <span class="title">Realtime Monitoring</span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item">
	                                    <a href="<?=base_url();?>trackers" class="nav-link " target="_blank"> <span class="title">View All</span>
	                                    </a>
	                                </li>
	                                
	                            </ul>
	                        </li>
	                         <li class="nav-item">
	                            <a href="javascript:;" class="nav-link nav-toggle">
	                                <i class="material-icons">local_taxi</i>
	                                <span class="title">Vehicles </span>
	                                <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item">
	                                    <a href="javascript:;" class="nav-link nav-toggle">
	                                        <i class="fa fa-globe"></i>Area
	                                        <span class="arrow"></span>
	                                    </a>
	                                    <ul class="sub-menu">
	                                        <!--<li class="nav-item">
	                                            <a href="<?=base_url();?>dashboard/summary/all" class="nav-link">All</a>
	                                        </li>-->
											<?php 
											if (isset($company) && (count($company)>0)){
												for ($i=0;$i<count($company);$i++){ ?>
												<li class="nav-item">
													<a href="<?=base_url();?>dashboard/area/<?=$company[$i]->company_id;?>" class="nav-link"><?=$company[$i]->company_name;?></a>
												</li>
											<?php }} ?>
	                                       
	                                        
	                                    </ul>
	                                </li>
	                                
	                            </ul>
	                        </li>
	                        <!--<li class="menu-heading m-t-20">
			                	<span>-- Layout, Apps &amp; Widget</span>
			                </li>-->
	                        
	                        <li class="nav-item">
	                            <a href="javascript:;" class="nav-link nav-toggle">
	                                <i class="material-icons">subtitles</i>
	                                <span class="title">Report </span>
	                                <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <!--<li class="nav-item">
	                                    <a href="<?=base_url();?>pbi_report/mn_dataoperational" class="nav-link ">
	                                        <span class="title">Operational Report</span>
	                                    </a>
	                                </li>-->
									<li class="nav-item">
										<a href="<?=base_url();?>tripreport/history" class="nav-link">
											<span class="label label-rouded label-menu label-danger">new</span>
	                                        <span class="title">History Map</span>
											
	                                    </a>
	                                </li>
	                            </ul>
	                        </li>
	                        
	                    </ul>
	                </div>
                </div>