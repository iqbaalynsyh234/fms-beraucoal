<style media="screen">
/* ////////////////////////////////////////////////////////////////////////////////////////// */
/* CSS PORT VIEW */
:root {
  --level-1: #8dccad;
  --level-2: #f5cc7f;
  --level-3: #389AF0;
  --level-4: #389AF0;
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
/* ////////////////////////////////////////////////////////////////////////////////////////// */
  @media only screen and (max-width: 400px) {
    #contentrom{
      overflow-x: auto;
      overflow-y: auto;
      max-height: 300px;
    }

    #contentport{
      overflow-x: auto;
      overflow-y: auto;
      max-height: 300px;
    }

    #contentpool{
      overflow-x: auto;
      overflow-y: auto;
      max-height: 300px;
    }

    #contentpoolnew{
      overflow-x: auto;
      overflow-y: auto;
      max-height: 300px;
    }
  }

  #contentrom{
    margin-left:0%;
  }

  #contentport{
    margin-left:0%;
    padding-bottom: 50px;
  }

  #contentpool{
    margin-left:0%;
    padding-bottom: 50px;
  }

  #contentpoolnew{
    margin-left:0%;
    padding-bottom: 50px;
  }

button.gm-ui-hover-effect {
    visibility: hidden;
}

.mapsClass1{
  width: 100%;
  height: 400px;
}

.mapsClass2{
  width: 100%;
  height: 300px;
}

.mapsClass3{
  width: 100%;
  height: 340px;
  padding-bottom: 50px;
}

/* Medium devices (landscape tablets, 768px and up) */
@media only screen and (min-width: 768px) {
  .timeline {
    list-style: none;
    padding: 20px 0 20px;
    position: relative;
  }

  .timeline:before {
    top: 30px;
    bottom: 30px;
    position: absolute;
    content: " ";
    width: 5px;
    background-color: #000000;
    left: 65%;
    /* margin-left: -1.5px; */
  }

  .timeline > li {
    margin-bottom: 2px;
    position: relative;
  }

  .timeline > li:before,
  .timeline > li:after {
    content: " ";
    display: table;
  }

  .timeline > li:after {
    clear: both;
  }

  .timeline > li:before,
  .timeline > li:after {
    content: " ";
    display: table;
  }

  .timeline > li:after {
    clear: both;
  }

  .timeline > li > .timeline-panel {
    width: 46%;
    float: left;
    /* border: 1px solid #d4d4d4; */
    /* border-radius: 2px; */
    padding: 20px;
    position: relative;
    /* -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
    /* box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
  }

  .timeline > li > .timeline-badge {
    color: #fff;
    width: 100px;
    height: 25px;
    line-height: 2px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 50%;
    margin-left: -25px;
    background-color: green;
    z-index: 1;
    /* border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%; */
  }

  .timeline > li > .timeline-badge2 {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 25px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 50%;
    margin-left: -25px;
    top: -20%;
    padding: 7px;
    background-color: green;
    z-index: 1;
    border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge3 {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 25px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 92%;
    /* margin-left: -25px; */
    top: -20%;
    padding: 7px;
    background-color: green;
    z-index: 1;
    border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge4 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 52%;
      margin-left: -25px;
      top: -29%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge5 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 75%;
      /* margin-left: -25px; */
      top: -29%;
      padding: 7px;
      background-color: purple;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge6 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 52%;
      margin-left: -25px;
      top: 50%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge7 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 75%;
      /* margin-left: -25px; */
      top: 50%;
      padding: 7px;
      background-color: purple;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge8 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 45%;
      margin-left: -25px;
      top: -29%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge9 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 65%;
      /* margin-left: -25px; */
      top: -29%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge10 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 85%;
      margin-left: -25px;
      top: -31%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge11 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 65%;
      /* margin-left: -25px; */
      top: -30%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li.timeline-inverted > .timeline-panel {
    float: center;
  }

  .timeline > li.timeline-inverted > .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
  }

  .timeline > li.timeline-inverted > .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
  }

  .timeline-heading {
    width: 70%;
  }
  .timeline-clock {
    width: 25%;
  }

  .timeline-title {
    margin-top: 0;
    color: inherit;
  }

  .timeline-body > p,
  .timeline-body > ul {
    margin-bottom: 0;
  }

  .timeline-body > p + p {
    margin-top: 5px;
  }
}

/* Extra large devices (large laptops and desktops, 1200px and up) */
@media only screen and (min-width: 992px) {
  .timeline {
    list-style: none;
    padding: 20px 0 20px;
    position: relative;
  }

  .timeline:before {
    top: 30px;
    bottom: 30px;
    position: absolute;
    content: " ";
    width: 5px;
    background-color: #000000;
    left: 60%;
    /* margin-left: -1.5px; */
  }

  .timeline > li {
    margin-bottom: 2px;
    position: relative;
  }

  .timeline > li:before,
  .timeline > li:after {
    content: " ";
    display: table;
  }

  .timeline > li:after {
    clear: both;
  }

  .timeline > li:before,
  .timeline > li:after {
    content: " ";
    display: table;
  }

  .timeline > li:after {
    clear: both;
  }

  .timeline > li > .timeline-panel {
    width: 46%;
    float: left;
    /* border: 1px solid #d4d4d4; */
    /* border-radius: 2px; */
    padding: 20px;
    position: relative;
    /* -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
    /* box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
  }

  .timeline > li > .timeline-badge {
    color: #fff;
    width: 100px;
    height: 25px;
    line-height: 2px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 50%;
    margin-left: -25px;
    background-color: green;
    z-index: 1;
    /* border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%; */
  }

  .timeline > li > .timeline-badge2 {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 25px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 50%;
    margin-left: -25px;
    top: -20%;
    padding: 7px;
    background-color: green;
    z-index: 1;
    border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge3 {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 25px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 65%;
    /* margin-left: -25px; */
    top: -20%;
    padding: 7px;
    background-color: green;
    z-index: 1;
    border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge4 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 52%;
      margin-left: -25px;
      top: -20%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge5 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 68%;
      /* margin-left: -25px; */
      top: -20%;
      padding: 7px;
      background-color: purple;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge6 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 52%;
      margin-left: -25px;
      top: 50%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge7 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 68%;
      /* margin-left: -25px; */
      top: 50%;
      padding: 7px;
      background-color: purple;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge8 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 45%;
      margin-left: -25px;
      top: -29%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge9 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 65%;
      /* margin-left: -25px; */
      top: -29%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge10 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 85%;
      margin-left: -25px;
      top: -31%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li > .timeline-badge11 {
    color: #fff;
      width: 30px;
      height: 30px;
      line-height: 16px;
      font-size: 12px;
      text-align: center;
      position: absolute;
      left: 65%;
      /* margin-left: -25px; */
      top: -30%;
      padding: 7px;
      background-color: green;
      z-index: 1;
      border-top-right-radius: 50%;
      border-top-left-radius: 50%;
      border-bottom-right-radius: 50%;
      border-bottom-left-radius: 50%;
  }

  .timeline > li.timeline-inverted > .timeline-panel {
    float: center;
  }

  .timeline > li.timeline-inverted > .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
  }

  .timeline > li.timeline-inverted > .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
  }

  .timeline-heading {
    width: 70%;
  }
  .timeline-clock {
    width: 25%;
  }

  .timeline-title {
    margin-top: 0;
    color: inherit;
  }

  .timeline-body > p,
  .timeline-body > ul {
    margin-bottom: 0;
  }

  .timeline-body > p + p {
    margin-top: 5px;
  }
}

  #valueTitle{
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
  }

  #valueonsite{
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
  }

  .custom-map-control-button {
    margin : 10px;
    height: 40px;
    cursor: pointer;
    direction: ltr;
    overflow: hidden;
    text-align: center;
    position: relative;
    color: rgb(0, 0, 0);
    font-family: "Roboto", Arial, sans-serif;
    -webkit-user-select: none;
    font-size: 18px !important;
    background-color: rgb(255, 255, 255);
    padding: 1px 6px;
    border-bottom-left-radius: 2px;
    border-top-left-radius: 2px;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.14902);
    -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;
    box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;
    min-width: 100px;
    font-weight: 500;
  }
</style>

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
              <h5>QUICK COUNT - PMO DEVELOPMENT </h5>
            </header>
            <div class="tools">
              <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <!-- <a class="t-close btn-color fa fa-times" href="javascript:;"></a> -->
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- <div class="col-md-3">
                <select class="form-control select2" name="master_site" id="master_site" onchange="mapsOptions();">
                  <?php
                    for ($i=0; $i < sizeof($master_site); $i++) {?>
                      <option value="<?php echo $master_site[$i]['site_id'] ?>"><?php echo $master_site[$i]['site_name'] ?></option>
                    <?php }
                   ?>
                </select>
              </div> -->

              <div class="col-md-3">
                <select class="form-control select2" name="contractor" id="contractor" onchange="mapsOptions();">
                  <option value="all">-- All Mitra</option>
                  <?php
                    for ($i=0; $i < sizeof($company); $i++) {?>
                      <option value="<?php echo $company[$i]->company_id ?>"><?php echo $company[$i]->company_name ?></option>
                    <?php }
                   ?>
                </select>
              </div>

              <div class="col-md-2">
                <img id="loader2" style="display:none;" src="<?php echo base_url();?>assets/images/anim_wait.gif" />
              </div>

            </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card" id="cardShowMap">
        <div class="card-body">
          <div class="row">
            <div id="tableShowMuatan" style="width: 100%; height: 600px; display:none;">
              <!-- display:none; -->

              <div class="row">
                <div class="col-md-6">
                  <p style="margin-left: 1%; font-size:12px;">Kosongan || Muatan</p>
                </div>

                <div class="col-md-6">
                  <p style="position: absolute;
                    position: absolute;
                    margin-left: -5%;"
                    id="lastupdateconsolidated">
                  </p>
                </div>
              </div>

              <div class="row" style="margin-top:-2%; height: 580px; overflow-y:hidden; overflow-x:auto;">
                <div class="col-md-6">
                  <div class="row">
                    <?php
                      for ($i=0; $i < sizeof($beaching_point); $i++) {
                        $bp_name        = explode(",", $beaching_point[$i]['street_name']);
                        $bp_name_format = str_replace(" ", "_", $bp_name[0]);
                        ?>
                        <div class="col-md-6">
                          <h5 class="level-3 info-box bg-blue" onclick="listVehicleOnPort(street_id)" style="cursor : pointer;">
                            <span class="info-box-icon push-bottom" style="margin-top:0%">
                              <img src="<?php echo base_url();?>/assets/bib/images/portbib.png" style="font-size:50px;">
                            </span>
                            <div class="info-box-content" style="color:white;">
                              <span class="info-box-number" style="font-size:20px;"><?php echo $bp_name[0] ?></span>
                              <?php if ($i == sizeof($beaching_point)-1) {?>
                                <span class="info-box-text" style="font-size:30px;" id="<?php echo $bp_name_format ?>"></b></span> <br>
                              <?php }else {?>
                                <span class="info-box-text" style="font-size:30px;" id="<?php echo $bp_name_format ?>"></b></span>
                              <?php } ?>
                            </div>
                          </h5>
                        </div>
                      <?php } ?>
                  </div>

                </div>

                <div class="col-md-2">
                  <ul class="timeline">
                    <?php for ($i=14; $i > 10; $i--) {?>
                      <li class="timeline-inverted">
                        <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                        <div class="timeline-panel"></div>
                      </li>
                      <li class="timeline-inverted">
                        <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                        <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                        <div class="timeline-panel"></div>
                      </li>
                    <?php } ?>
                  </ul>
                </div>

                <div class="col-md-2">
                  <ul class="timeline">
                    <?php for ($i=10; $i > 5; $i--) {?>
                      <li class="timeline-inverted">
                        <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                        <div class="timeline-panel"></div>
                      </li>
                      <li class="timeline-inverted">
                        <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                        <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                        <div class="timeline-panel"></div>
                      </li>
                    <?php } ?>
                  </ul>
                </div>

                <div class="col-md-2">
                  <ul class="timeline">
                    <?php for ($i=5; $i > 0; $i--) {?>
                      <li class="timeline-inverted">
                        <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                        <div class="timeline-panel"></div>
                      </li>
                      <li class="timeline-inverted">
                        <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                        <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                        <div class="timeline-panel"></div>
                      </li>
                    <?php } ?>
                  </ul>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div id="listrominquickcount" style="font-weight: bold;">
                    <!-- ROM A1 : 150 | ROM B1 : 4 | ROM B2 : 0 | ROM B3 : 8 | ROM EST : 0 -->
                  </div>
                </div>

                <div class="col-md-12">
                  <div id="listportinquickcount" style="font-weight: bold;">
                    <!-- PORT BIB : 12 | PORT BIR : 10 -->
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

<div id="modalKmListQuickCount" class="modalkmlist">
  <div class="modal-content-kmlist">
    <div class="row">
      <div class="col-md-10">
        <p class="modalTitleforAll" id="modalKmListQuickCountTitle">
        </p>
        <div id="lastcheckKmListQuickCount" style="font-size:12px; color:black"></div>
      </div>
      <div class="col-md-2">
        <div class="closethismodalkm btn btn-danger btn-sm">X</div>
      </div>
    </div>
    <div class="row" id="modalKmAll">
      <div class="col-md-6">
        <p style="color: black; font-style: bold;">
          Kosongan
        </p>
        <div id="modalStateContentKosongan"></div>
      </div>

      <div class="col-md-6">
        <p style="color: black; font-style: bold;">
          Muatan
        </p>
        <div id="modalStateContentMuatan"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
  intervalkmlist = setInterval(showTableMuatan, 3000);
});

  function mapsOptions(){
    clearInterval(intervalkmlist);
    showTableMuatan();
  }

  intervalkmlist = setInterval(showTableMuatan, 45000);

  // $("#btnmaptable").show();
  $("#showtable").hide();
  $("#modallistvehicle").hide();
  $("#modalfivereport").hide();
  $("#mapshowfix").addClass('col-md-8');

  var datafixnya        = "";
  var dataposition      = [];
  var overlaystatus     = 0;
  var overlaysarray     = [];
  var arraypointheatmap = [];
  var marker            = [];
  var markernya         = [];
  var markers           = [];
  var markerss          = [];
  var markerpools       = [];
  var intervalstart, intervalkmlist, intervalromlist;
  var intervalportlist, intervalpoollist, intervalofflinevehicle;
  var infowindowkedua, infowindow, infowindow2, infowindowonsimultan;
  var intervaloutofhauling;
  var camdevices        = ["TK510CAMDOOR", "TK510CAM", "GT08", "GT08DOOR", "GT08CAM", "GT08CAMDOOR"];
  var bibarea           = ["KM", "POOL", "ST", "ROM", "PIT", "PORT", "POOl", "WS", "WB", "PT.BIB"];
  var objmapsstandard;
  var objmapsstandardpoolmasterfix;
  var objmapsstandardsimultan;
  var objmapsstandardpoolmasterfixsimultan;
  var intervalmapsstandard;

  var middle_limit = '<?php echo $mapsetting[0]['mapsetting_middle_limit'] ?>';
  var top_limit    = '<?php echo $mapsetting[0]['mapsetting_top_limit'] ?>';

  function showTableMuatan(){
    clearInterval(intervalkmlist);
    $("#cardShowMap").show();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").hide();
    $("#tableShowPool").hide();
    $("#tableShowPoolNew").hide();
    $("#tableShowOutOfHauling").hide();
    $("#tableShowVehicleOffline").hide();
    $("#tableShowMuatan").show();
    soundisactive = 0;
    var companyid = $("#contractor").val();
    jQuery("#loader2").show();

    $.post("<?php echo base_url() ?>maps_development/km_quickcount_new", {companyid : companyid}, function(response){
      jQuery("#loader2").hide();
          console.log("response km_quickcount_new : ", response);
          var databeaching_point     = response.beaching_point;
          var datamuatan             = response.dataMuatan;
          var datakosongan           = response.dataKosongan;
          var datafixlimit           = response.datafixlimit;
          var arraydatamuatan        = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
          var arraydatakosongan      = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
          var arraydatabeachingpoint = Object.keys(databeaching_point).map((key) => [String(key), databeaching_point[key]]);
          var sizemuatan             = arraydatamuatan.length;
          var sizekosongan           = arraydatakosongan.length;
          var sizebeachingpoint      = arraydatabeachingpoint.length;
          var totalMuatan            = 0;
          var totalKosongan          = 0;
          var totalMuatan2           = 0;
          var totalKosongan2         = 0;
          var portbibkosongan        = 0;
          var portbirkosongan        = 0;
          var portbibmuatan          = 0;
          var portbirmuatan          = 0;

          // KONDISI BEACHING POINT START
          for (var i = 0; i < sizebeachingpoint; i++) {
            // console.log("beaching point name : ", arraydatabeachingpoint[i][0]);
            // console.log("beaching point value : ", arraydatabeachingpoint[i][1]);
            $("#"+arraydatabeachingpoint[i][0]).html(arraydatabeachingpoint[i][1]);
          }
          // KONDISI BEACHING POINT END

              // KM 2 - 30 KOSONGAN
              var dataallkmkosongan  = response.datafixlimitperkmallkosongan;
              var totalkmallkosongan = response.datafixlimitperkmallkosongan.length;
              // console.log("dataallkmkosongan : ", dataallkmkosongan);
              // console.log("totalkmallkosongan : ", totalkmallkosongan);
              for (var i = 0; i < sizekosongan; i++) {
                var kmlist         = arraydatakosongan[i][0];
                var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                totalKosongan += jumlahfix;
                // console.log("kmlist kosongan " + i + " : " + kmlist);
                // console.log("jumlahfix kosongan " + i + " : " + jumlahfix);

                // for (var j = 0; j < totalkmallkosongan; j++) {
                  // var mapsetting_name = dataallkmkosongan[j].mapsetting_name;
                  //   if (kmlist == mapsetting_name) {
                  //     var mapsetting_middle_limit_allkmkosongan = dataallkmkosongan[j].mapsetting_middle_limit;
                  //     var mapsetting_top_limit_allkmkosongan    = dataallkmkosongan[j].mapsetting_top_limit;
                  //   }else {
                  //     var mapsetting_middle_limit_allkmkosongan = middle_limit;
                  //     var mapsetting_top_limit_allkmkosongan    = top_limit;
                  //   }
                  //
                    if (jumlahfix > 0) {
                    $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                    $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                    $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
                  }

                  $("#vehicleonkosongan"+i).html(jumlahfix);
                  // }
            }


            // KM 2 - 30 MUATAN
            var dataallkmmuatan  = response.datafixlimitperkmallmuatan;
            var totalkmallmuatan = response.datafixlimitperkmallmuatan.length;
            // console.log("dataallkmmuatan : ", dataallkmmuatan);
            // console.log("totalkmallmuatan : ", totalkmallmuatan);

            for (var i = 0; i < sizemuatan; i++) {
              var kmlist           = arraydatamuatan[i][0];
              var jumlahfix        = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
              totalMuatan += jumlahfix;

              // console.log("kmlist muatan " + i + " : " + kmlist);
              // console.log("jumlahfix muatan " + i + " : " + jumlahfix);

                // LOOP FOR ALL KM MUATAN EXCEPT KM 1
                // for (var j = 0; j < totalkmallmuatan; j++) {
                  // var mapsetting_name = dataallkmmuatan[j].mapsetting_name;
                  //   if (kmlist == mapsetting_name) {
                  //     var mapsetting_middle_limit_allkmmuatan = dataallkmmuatan[j].mapsetting_middle_limit;
                  //     var mapsetting_top_limit_allkmmuatan    = dataallkmmuatan[j].mapsetting_top_limit;
                  //   }else {
                  //     var mapsetting_middle_limit_allkmmuatan = middle_limit;
                  //     var mapsetting_top_limit_allkmmuatan    = top_limit;
                  //   }
                  //
                    if (jumlahfix > 0) {
                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
                    }

                    $("#vehicleonmuatan"+i).html(jumlahfix);
                // }
            }

              $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan) +" || Muatan : " + (totalMuatan) + " || Total : " + (totalKosongan + totalMuatan) + " || Last Refresh : " + response.lastcheck) ;
              $("#lastupdateconsolidated").show();
        },"json");
  }
</script>
