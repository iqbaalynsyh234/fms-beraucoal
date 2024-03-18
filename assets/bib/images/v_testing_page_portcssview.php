<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <div class="col-sm-12 col-md-4 col-lg-3">
      <button class="btn btn-info" id="notifdevicestatus" style="display:none;"></button>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-head">
            <header>
              <h5>DEVELOPMENT PORT VIEW CSS</h5>
            </header>
            <div class="tools">
              <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <!-- <a class="t-close btn-color fa fa-times" href="javascript:;"></a> -->
            </div>
          </div>
	
		  
		  
		  
		  
		  
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
				<div>
					<ol>
						<li>
							<ol>
								<li>
									<h3 class="level-3 info-box">
									<span class="info-box-icon push-bottom" style="margin-top:4%;">
										<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->
										<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">
									</span>
									<div class="info-box-content">
										<span class="info-box-text" style="font-size:15px;">PORT BIB</span>
										<span class="info-box-number" style="font-size:22px;"><b>15</b></span>
									</div>			
									</h3>
									<ol class="level-4-wrapper">
										<li>
										<h4 class="level-4 rectangle"><span class="info-box-icon push-bottom" style="margin-top:4%;">
										<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->
										<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">
									</span>
									<div class="info-box-content">
										<span class="info-box-text" style="font-size:15px;">PORT BIB</span>
										<span class="info-box-number" style="font-size:22px;"><b>15</b></span>
									</div></h4>
										</li>
										<li>
											<h4 class="level-4 rectangle">Person B</h4>
										</li>
										<li>
											<h4 class="level-4 rectangle">Person C</h4>
										</li>
										<li>
											<h4 class="level-4 rectangle">Person D</h4>
										</li>
									</ol>
								</li>
        
							</ol>
						</li>
    
					</ol>
				</div>


                CSS VIEW HERE
                <div class="col-xl-12 col-md-6 col-12" >
                  <div class="info-box bg-blue">
                    <span class="info-box-icon push-bottom" style="margin-top:4%;">
                      <i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>
                    </span>
                    <div class="info-box-content">
                      <span class="info-box-text" style="font-size:15px;">PORT BIB</span>
                      <span class="info-box-number" style="font-size:22px;"><b>15</b></span>
                    </div>
                  </div>
                </div>

                <div class="col-xl-12 col-md-6 col-12" >
                  <div class="info-box bg-blue">
                    <span class="info-box-icon push-bottom" style="margin-top:4%;">
                      <i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>
                    </span>
                    <div class="info-box-content">
                      <span class="info-box-text" style="font-size:15px;">PORT BIB</span>
                      <span class="info-box-number" style="font-size:22px;"><b>15</b></span>
                    </div>
                  </div>
                </div>

               
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>


<style>

:root {
  --level-1: #8dccad;
  --level-2: #f5cc7f;
  --level-3: #7b9fe0;
  --level-4: #7B9FE0;
  --black: black;
}

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

ol {
  list-style: none;
}



.container {
  max-width: 1000px;
  padding: 0 10px;
  margin: 0 auto;
}

.rectangle {
  position: relative;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
  border-radius: 20px;
}


/* LEVEL-1 STYLES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
.level-1 {
  width: 50%;
  margin: 0 auto 40px;
  background: var(--level-1);
}

.level-1::before {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  height: 20px;
  background: var(--black);
}


/* LEVEL-2 STYLES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
.level-2-wrapper {
  position: relative;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
}

.level-2-wrapper::before {
  content: "";
  position: absolute;
  top: -20px;
  left: 25%;
  width: 50%;
  height: 2px;
  background: var(--black);
}

.level-2-wrapper::after {
  display: none;
  content: "";
  position: absolute;
  left: -20px;
  bottom: -20px;
  width: calc(100% + 20px);
  height: 2px;
  background: var(--black);
}

.level-2-wrapper li {
  position: relative;
}

.level-2-wrapper > li::before {
  content: "";
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  height: 20px;
  background: var(--black);
}

.level-2 {
  width: 70%;
  margin: 0 auto 40px;
  background: var(--level-2);
}

.level-2::before {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  height: 20px;
  background: var(--black);
}

.level-2::after {
  display: none;
  content: "";
  position: absolute;
  top: 50%;
  left: 0%;
  transform: translate(-100%, -50%);
  width: 20px;
  height: 2px;
  background: var(--black);
}


/* LEVEL-3 STYLES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
.level-3-wrapper {
  position: relative;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-column-gap: 20px;
  width: 90%;
  margin: 0 auto;
}

.level-3-wrapper::before {
  content: "";
  position: absolute;
  top: -20px;
  left: calc(25% - 5px);
  width: calc(50% + 10px);
  height: 2px;
  background: var(--black);
}

.level-3-wrapper > li::before {
  content: "";
  position: absolute;
  top: 0;
  left: 50%;
  transform: translate(-50%, -100%);
  width: 2px;
  height: 20px;
  background: var(--black);
}

.level-3 {
  margin-bottom: 20px;
  background: var(--level-3);
}


/* LEVEL-4 STYLES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
.level-4-wrapper {
  position: relative;
  width: 80%;
  margin-left: auto;
}

.level-4-wrapper::before {
  content: "";
  position: absolute;
  top: -20px;
  left: -20px;
  width: 2px;
  height: calc(100% + -16px);
  background: var(--black);
}

.level-4-wrapper li + li {
  margin-top: 20px;
}

.level-4 {
  font-weight: normal;
  background: var(--level-4);
}

.level-4::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0%;
  transform: translate(-100%, -50%);
  width: 20px;
  height: 2px;
  background: var(--black);
}


/* MQ STYLES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
@media screen and (max-width: 700px) {
  .rectangle {
    padding: 20px 10px;
  }

  .level-1,
  .level-2 {
    width: 100%;
  }

  .level-1 {
    margin-bottom: 20px;
  }

  .level-1::before,
  .level-2-wrapper > li::before {
    display: none;
  }
  
  .level-2-wrapper,
  .level-2-wrapper::after,
  .level-2::after {
    display: block;
  }

  .level-2-wrapper {
    width: 90%;
    margin-left: 10%;
  }

  .level-2-wrapper::before {
    left: -20px;
    width: 2px;
    height: calc(100% + 40px);
  }

  .level-2-wrapper > li:not(:first-child) {
    margin-top: 50px;
  }
}


/* FOOTER
–––––––––––––––––––––––––––––––––––––––––––––––––– */
.page-footer {
  position: fixed;
  right: 0;
  bottom: 20px;
  display: flex;
  align-items: center;
  padding: 5px;
}

.page-footer a {
  margin-left: 4px;
}

</style>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
