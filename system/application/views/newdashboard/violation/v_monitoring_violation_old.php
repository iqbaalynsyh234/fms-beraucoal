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
  height: 465px;
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
              <h5>MONITORING VIOLATION DEVELOPMENT</h5>
            </header>
            <div class="tools">
              <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <!-- <a class="t-close btn-color fa fa-times" href="javascript:;"></a> -->
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-2">
                <select class="form-control select2" name="contractor" id="contractor" onchange="onchangefilter()">
                </select>
              </div>

              <div class="col-lg-2 col-md-2">
                <select id="violationmasterselect" name="violationmasterselect" class="form-control select2" onchange="onchangefilter()">
                    <option value="0">--All Violation</option>
                    <?php
                    for ($i = 0; $i < sizeof($violationmaster); $i++) {?>
                      <option value=<?php echo $violationmaster[$i]["alarmmaster_id"] ?>><?php echo $violationmaster[$i]["alarmmaster_name"] ?></option>
                    <?php } ?>
                </select>
              </div>

              <div class="col-md-2">
                <img id="loader2" style="display:none;" src="<?php echo base_url();?>assets/images/anim_wait.gif" />
              </div>

              <!-- <?php
              $privilegecode = $this->sess->user_id_role;
                if ($privilegecode == 0 || $privilegecode == 1) {?>
                  <div class="col-md-3">
                    <button type="button" name="button" class="btn btn-danger btn-md" id="mapSetting" style="margin-left:2%;" onclick="customMymodal('modalMapSetting');">
                      <span class="fa fa-cogs"></span>
                    </button>
                  </div>
                <?php }?> -->
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

            <div id="tableShowMuatan" style="width: 100%; height: 560px; display:none;">
              <!-- display:none; -->
              <input type="text" name="simultantype" id="simultantype" hidden>
              <input type="text" name="lasttime_violation" id="lasttime_violation" hidden>


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

              <div class="row" style="margin-top:-3%; margin-left: -5%; height: 500px; overflow-y:hidden; overflow-x:auto;">
                <div class="col-md-2">
                  <ul class="timeline">
                    <?php for ($i=30; $i > 25; $i--) {?>
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
                    <?php for ($i=25; $i > 20; $i--) {?>
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
                    <?php for ($i=20; $i > 15; $i--) {?>
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
                    <?php for ($i=15; $i > 10; $i--) {?>
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
                        <?php
                          if ($i == 1) {?>
                            <div id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">
                              <p style="margin-left: -30%; margin-top: -30%; font-weight: bold;">
                                KM 1
                              </p>
                            </div>
                          <?php }else {?>
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                          <?php }?>
                        <div class="timeline-panel"></div>
                      </li>

                        <?php
                          if ($i == 1) {?>
                            <!-- <li class="timeline-inverted">
                              <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                              <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                              <div class="timeline-panel"></div>
                            </li>

                            <li class="timeline-inverted">
                              <div class="timeline-badge2 btn btn-danger btn-md btn-circle" id="vehicleonkosongantest1" style="margin-top:1%;">15</div>
                              <div class="timeline-badge3 btn btn-warning btn-md btn-circle" id="vehicleonmuatantest2" style="margin-top:1%;">5</div>
                              <div class="timeline-panel"></div>
                            </li> -->

                            <li class="timeline-inverted">
                              <span style="float:left; margin-left:30px; margin-top:-7px;">BIB</span>
                              <span style="float:left; margin-left:-20px; margin-top:33px;">BIR</span>
                              <div class=""  id="port_bib_kosongan"></div>
                              <div class=""  id="port_bib_muatan"></div>
                              <div class=""  id="port_bir_kosongan" style="margin-top:1%;"></div>
                              <div class="" id="port_bir_muatan" style="margin-top:1%;"></div>
                              <div class="timeline-panel"></div>
                            </li>

                            <!-- <li class="timeline-inverted">
                              <div class="timeline-badge8 btn btn-danger btn-md btn-circle" id="vehicleonkosongantest1">20</div>
                              <div class="timeline-badge9 btn btn-danger btn-md btn-circle" id="vehicleonmuatantest2">13</div>
                              <div class="timeline-badge10 btn btn-danger btn-md btn-circle" id="vehicleonkosongantest1" style="margin-top:1%;">15</div>
                              <div class="timeline-badge11 btn btn-warning btn-md btn-circle" id="vehicleonmuatantest2" style="margin-top:1%;">5</div>
                              <div class="timeline-panel"></div>
                            </li> -->
                          <?php }else {?>
                            <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                            </li>
                          <?php }?>
                    <?php } ?>
                    <!-- <li class="timeline-inverted">
                      <div class="" id="vehicleonkosongantest1">12</div>
                      <div class="" id="vehicleonmuatantest2">33</div>
                      <div class="timeline-panel"></div>
                    </li> -->
                  </ul>
                </div>
              </div>
              <!-- <div class="row">
                <div class="col-md-12">
                  <div id="listrominquickcount" style="font-weight: bold;"> -->
                    <!-- ROM A1 : 150 | ROM B1 : 4 | ROM B2 : 0 | ROM B3 : 8 | ROM EST : 0 -->
                  <!-- </div>
                </div> -->

                <!-- <div class="col-md-12">
                  <div id="listportinquickcount" style="font-weight: bold;"> -->
                    <!-- PORT BIB : 12 | PORT BIR : 10 -->
                  <!-- </div>
                </div>
              </div>
            </div>
        </div> -->
      </div>
    </div>



    </div>
  </div>
</div>

<div id="modalState" class="modal">
  <div class="modal-content-state">
    <div class="row">
      <div class="col-md-10">
        <p class="modalTitleforAll" id="modalStateTitle">
        </p>
        <div id="contractorinlocation" style="font-size:14px; color:black"></div>
        <div id="lastcheckpoolws" style="font-size:12px; color:black"></div>
      </div>
      <div class="col-md-2">
        <div class="closethismodalall btn btn-danger btn-sm">X</div>
      </div>
    </div>
      <div id="modalStateContent"></div>
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

<script>
  $(document).ready(function() {
    setTimeout(function(){
      appendthecontractorlist();
      showTableMuatan();
    }, 3000);

    function appendthecontractorlist(){
      $.post("<?php echo base_url() ?>maps/getdatacontractor", {}, function(response){
        // console.log("response : ", response);
        var data = response.data;
        var html = "";

            html += '<option value="0">--All Contractor</option>';
            for (var i = 0; i < data.length; i++) {
              html += '<option value="'+data[i].company_id+'">'+data[i].company_name+'</option>';
            }
          $("#contractor").html(html);
      },"json");
    }
  });

  var middle_limit = 10;
  var top_limit    = 16;
  var intervalkmlist;
  intervalkmlist   = setInterval(showTableMuatan, 15000);

  function showTableMuatan(){
    $("#cardShowMap").show();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#summarymapsshowhide").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").hide();
    $("#tableShowPool").hide();
    $("#tableShowPoolNew").hide();
    $("#tableShowOutOfHauling").hide();
    $("#tableShowVehicleOffline").hide();
    $("#tableShowMuatan").show();
    var simultantype       = $("#simultantype").val();
    var lasttime_violation = $("#lasttime_violation").val();
    var contractor         = $("#contractor").val();
    var violationmaster    = $("#violationmasterselect").val();
    soundisactive          = 0;
    jQuery("#loader2").show();

    var datanya = {
      lasttime_violation : lasttime_violation,
      simultantype : simultantype,
      contractor : contractor,
      violationmaster : violationmaster
    };

    // console.log("datanya : ", datanya);

    $.post("<?php echo base_url() ?>violation/km_quickcount_new", datanya, function(response){
      jQuery("#loader2").hide();
      $("#tableShowMuatan").show();
          $("#simultantype").val(1);
          $("#lasttime_violation").val(response.lasttime);
            if (response.violationmix.length > 0) {
              console.log("response km_quickcount_new : ", response);
              console.log("response lasttime : ", response.lasttime);

              var datamuatan         = response.dataMuatan;
              var datakosongan       = response.dataKosongan;
              var datafixlimit       = response.datafixlimit;
              var arraydatamuatan    = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
              var arraydatakosongan  = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
              var dataMuatan2        = response.dataMuatan2;
              var dataKosongan2      = response.dataKosongan2;
              var arraydatamuatan2   = Object.keys(dataMuatan2).map((key) => [String(key), dataMuatan2[key]]);
              var arraydatakosongan2 = Object.keys(dataKosongan2).map((key) => [String(key), dataKosongan2[key]]);
              var sizemuatan         = arraydatamuatan.length;
              var sizekosongan       = arraydatakosongan.length;
              var sizemuatan2        = arraydatamuatan2.length;
              var sizekosongan2      = arraydatakosongan2.length;
              var totalMuatan        = 0;
              var totalKosongan      = 0;
              var totalMuatan2       = 0;
              var totalKosongan2     = 0;
              var portbibkosongan    = 0;
              var portbirkosongan    = 0;
              var portbibmuatan      = 0;
              var portbirmuatan      = 0;

                  // SIMPANG BAYAH KOSONGAN PORT BIB & BIR
                  for (var i = 0; i < sizekosongan2; i++) {
                    var kmlist         = arraydatakosongan2[i][0];
                    var jumlahfix      = arraydatakosongan2[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                    totalKosongan2 += jumlahfix;

                    if (kmlist == "gb0_port_bib_kosongan_1") {
                      portbibkosongan += jumlahfix;
                    }else if (kmlist == "gb1_port_bib_kosongan_2") {
                      portbibkosongan += jumlahfix;
                    }else if (kmlist == "gb2_port_bir_kosongan_1") {
                      portbirkosongan += jumlahfix;
                    }else if (kmlist == "gb3_port_bir_kosongan_2") {
                      portbirkosongan += jumlahfix;
                    }else if (kmlist == "gb4_simpang_bayah_kosongan") {
                      portbibkosongan += jumlahfix;
                    }

                    console.log("portbibkosongan : ", portbibkosongan);

                    $("#port_bib_kosongan").html(portbibkosongan);
                    $("#port_bir_kosongan").html(portbirkosongan);

                    if (portbibkosongan > 0) {
                      $("#port_bib_kosongan").removeClass();
                      $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#port_bib_kosongan").removeClass();
                      $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-success btn-md btn-circle');
                    }

                    if (portbirkosongan > 0) {
                      $("#port_bir_kosongan").removeClass();
                      $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#port_bir_kosongan").removeClass();
                      $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-success btn-md btn-circle');
                    }
                  }

                  // KM 2 - 30 KOSONGAN
                  for (var i = 0; i < sizekosongan; i++) {
                    var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                    totalKosongan += jumlahfix;

                    if (jumlahfix > 0) {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonkosongan"+i).removeClass();
                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonkosongan"+i).removeClass();
                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
                    }

                  $("#vehicleonkosongan"+i).html(jumlahfix);
                }

               // SIMPANG BAYAH MUATAN PORT BIB & BIR
                for (var i = 0; i < sizemuatan2; i++) {
                  var kmlist    = arraydatamuatan2[i][0];
                  var jumlahfix = arraydatamuatan2[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                  totalMuatan2 += jumlahfix;

                  if (kmlist == "gb5_port_bib_antrian") {
                    portbibmuatan += jumlahfix;
                  }else if (kmlist == "gb6_port_bir_antrian_wb") {
                    portbirmuatan += jumlahfix;
                  }

                  console.log("portbibmuatan : ", portbibmuatan);

                  $("#port_bib_muatan").html(portbibmuatan);
                  $("#port_bir_muatan").html(portbirmuatan);

                  if (portbibmuatan > 0) {
                    $("#port_bib_muatan").removeClass();
                    $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#port_bib_muatan").removeClass();
                    $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
                  }

                  if (portbirmuatan > 0) {
                    $("#port_bir_muatan").removeClass();
                    $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#port_bir_muatan").removeClass();
                    $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
                  }
                }

                // KM 2 - 30 MUATAN
                for (var i = 0; i < sizemuatan; i++) {
                  var jumlahfix        = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                  totalMuatan += jumlahfix;

                    // LOOP FOR ALL KM MUATAN EXCEPT KM 1
                    if (jumlahfix > 0) {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonmuatan"+i).removeClass();

                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonmuatan"+i).removeClass();

                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
                    }

                    $("#vehicleonmuatan"+i).html(jumlahfix);
                }

                  $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2) + " || Last Check : " + response.lasttime) ;
                  $("#lastupdateconsolidated").show();
                  // intervalkmlist = setInterval(km_quickcount_simultan, 15000);
            }else {
              console.log("Data is Empty");
              console.log("response lasttime : ", response.lasttime);
            }
        },"json");
  }

  function listVehicleOnKm(idkm){
    console.log("idkm : ", idkm);
    var contractor         = $("#contractor").val();
    var lasttime_violation = $("#lasttime_violation").val();
    $.post("<?php echo base_url() ?>violation/getlistinkm", {idkm : idkm, contractor : contractor, lasttime_violation:lasttime_violation}, function(response){
      console.log("response By KM Quick Count : ", response);
      var dataKosonganfix = response.dataKosongan;
      var dataMuatanfix   = response.dataMuatan;

      console.log("data kosongan : ", dataKosonganfix.length);
      console.log("data muatan : ", dataMuatanfix.length);

      var htmlkosongan = "";
      var htmlmuatan   = "";
      if (dataKosonganfix.length == 0) {
        $("#modalStateContentKosongan").html("Tidak ada data");
      }else {
        var lastcheckKmListQuickCount = "Last Check : "+dataKosonganfix[0].auto_last_update + " WITA";
        $("#modalKmListQuickCountTitle").html(response.kmsent);
        $("#lastcheckKmListQuickCount").html(lastcheckKmListQuickCount);

        htmlkosongan += '<table class="table table-striped">';
          htmlkosongan += '<thead>';
            htmlkosongan += '<tr>';
            htmlkosongan += '<th>No</th>';
              htmlkosongan += '<th>Vehicle</th>';
              htmlkosongan += '<th>Position</th>';
              htmlkosongan += '<th>Violation</th>';
              // htmlkosongan += '<th align="center">Engine</th>';
              htmlkosongan += '<th align="center">Speed (Kph)</th>';
              // htmlkosongan += '<th>Coord</th>';
              htmlkosongan += '<th>Time</th>';
            htmlkosongan += '</tr>';
          htmlkosongan += '</thead>';
        for (var i = 0; i < dataKosonganfix.length; i++) {
          var enginekmfix = dataKosonganfix[i].auto_last_engine;
          var speedkmfix = "";
            if (enginekmfix == "OFF") {
              speedkmfix = 0;
            }else {
              // speedkmfix = dataKosonganfix[i].auto_last_speed;
                if (dataKosonganfix[i].violation_type == "overspeed") {
                  speedkmfix = "Speed : "+dataKosonganfix[i].auto_last_speed + "<br>" + "Limit : " + dataKosonganfix[i].auto_speed_limit + "<br>" + "Overspeed : " + (dataKosonganfix[i].auto_last_speed - dataKosonganfix[i].auto_speed_limit);
                }else {
                  speedkmfix = dataKosonganfix[i].auto_last_speed;
                }
            }

              htmlkosongan += '<tr>';
                htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].vehicle_no+ " " +dataKosonganfix[i].vehicle_name+'</span>';
                htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_positionfix+ '</span>';
                htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].violation+ '</span>';
                // htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_engine+'</span>';
                htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedkmfix+'</span>';
                htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_update+'</span>';
                // htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_lat+ "," +dataKosonganfix[i].auto_last_long+'</span>';
              htmlkosongan += '</tr>';
        }
        htmlkosongan += '</table>';
        $("#modalStateContentKosongan").html(htmlkosongan);
      }

      if (dataMuatanfix.length == 0) {
        $("#modalStateContentMuatan").html("Tidak ada data");
      }else {
        var lastcheckKmListQuickCount = "Last Check : "+dataMuatanfix[0].auto_last_update + " WITA";
        $("#modalKmListQuickCountTitle").html(response.kmsent);
        $("#lastcheckKmListQuickCount").html(lastcheckKmListQuickCount);

        htmlmuatan += '<table class="table table-striped">';
          htmlmuatan += '<thead>';
            htmlmuatan += '<tr>';
            htmlmuatan += '<th>No</th>';
              htmlmuatan += '<th>Vehicle</th>';
              htmlmuatan += '<th>Position</th>';
              htmlmuatan += '<th>Violation</th>';
              // htmlmuatan += '<th align="center">Engine</th>';
              htmlmuatan += '<th align="center">Speed (Kph)</th>';
              // htmlmuatan += '<th>Coord</th>';
              htmlmuatan += '<th>Time</th>';
            htmlmuatan += '</tr>';
          htmlmuatan += '</thead>';
        for (var i = 0; i < dataMuatanfix.length; i++) {
          var enginekmfix = dataMuatanfix[i].auto_last_engine;

          var speedkmfix = "";
            if (enginekmfix == "OFF") {
              speedkmfix = 0;
            }else {
              // speedkmfix = dataMuatanfix[i].auto_last_speed;
              if (dataMuatanfix[i].violation_type == "overspeed") {
                speedkmfix   = "Speed : "+dataMuatanfix[i].auto_last_speed + "<br>" + "Limit : " + dataMuatanfix[i].auto_speed_limit + "<br>" + "Overspeed : " + (dataMuatanfix[i].auto_last_speed - dataMuatanfix[i].auto_speed_limit);
              }else {
                speedkmfix = dataMuatanfix[i].auto_last_speed;
              }
            }

              htmlmuatan += '<tr>';
                htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].vehicle_no+ " " +dataMuatanfix[i].vehicle_name+'</span>';
                htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_positionfix+'</span>';
                htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].violation+ '</span>';
                // htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_engine+'</span>';
                htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedkmfix+'</span>';
                htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_update+'</span>';
                // htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_lat+ "," +dataMuatanfix[i].auto_last_long+'</span>';
              htmlmuatan += '</tr>';
        }
        htmlmuatan += '</table>';
        $("#modalStateContentMuatan").html(htmlmuatan);
      }

        modalKmFromMasterData('modalKmListQuickCount');

    }, "json");
  }

  function onchangefilter(){
    clearInterval(intervalkmlist);
    $("#cardShowMap").show();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#summarymapsshowhide").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").hide();
    $("#tableShowPool").hide();
    $("#tableShowPoolNew").hide();
    $("#tableShowOutOfHauling").hide();
    $("#tableShowVehicleOffline").hide();
    $("#tableShowMuatan").show();
    var simultantype       = $("#simultantype").val();
    var lasttime_violation = $("#lasttime_violation").val();
    var contractor         = $("#contractor").val();
    var violationmaster    = $("#violationmasterselect").val();
    soundisactive          = 0;
    jQuery("#loader2").show();

    var datanya = {
      lasttime_violation : lasttime_violation,
      simultantype : simultantype,
      contractor : contractor,
      violationmaster : violationmaster
    };

    $.post("<?php echo base_url() ?>violation/km_quickcount_new", datanya, function(response){
      jQuery("#loader2").hide();
      $("#tableShowMuatan").show();
          $("#simultantype").val(1);
          $("#lasttime_violation").val(response.lasttime);
            if (response.violationmix.length > 0) {
              console.log("response km_quickcount_new : ", response);
              console.log("response lasttime : ", response.lasttime);

              var datamuatan         = response.dataMuatan;
              var datakosongan       = response.dataKosongan;
              var datafixlimit       = response.datafixlimit;
              var arraydatamuatan    = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
              var arraydatakosongan  = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
              var dataMuatan2        = response.dataMuatan2;
              var dataKosongan2      = response.dataKosongan2;
              var arraydatamuatan2   = Object.keys(dataMuatan2).map((key) => [String(key), dataMuatan2[key]]);
              var arraydatakosongan2 = Object.keys(dataKosongan2).map((key) => [String(key), dataKosongan2[key]]);
              var sizemuatan         = arraydatamuatan.length;
              var sizekosongan       = arraydatakosongan.length;
              var sizemuatan2        = arraydatamuatan2.length;
              var sizekosongan2      = arraydatakosongan2.length;
              var totalMuatan        = 0;
              var totalKosongan      = 0;
              var totalMuatan2       = 0;
              var totalKosongan2     = 0;
              var portbibkosongan    = 0;
              var portbirkosongan    = 0;
              var portbibmuatan      = 0;
              var portbirmuatan      = 0;

                  // SIMPANG BAYAH KOSONGAN PORT BIB & BIR
                  for (var i = 0; i < sizekosongan2; i++) {
                    var kmlist         = arraydatakosongan2[i][0];
                    var jumlahfix      = arraydatakosongan2[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                    totalKosongan2 += jumlahfix;

                    if (kmlist == "gb0_port_bib_kosongan_1") {
                      portbibkosongan += jumlahfix;
                    }else if (kmlist == "gb1_port_bib_kosongan_2") {
                      portbibkosongan += jumlahfix;
                    }else if (kmlist == "gb2_port_bir_kosongan_1") {
                      portbirkosongan += jumlahfix;
                    }else if (kmlist == "gb3_port_bir_kosongan_2") {
                      portbirkosongan += jumlahfix;
                    }else if (kmlist == "gb4_simpang_bayah_kosongan") {
                      portbibkosongan += jumlahfix;
                    }

                    console.log("portbibkosongan : ", portbibkosongan);

                    $("#port_bib_kosongan").html(portbibkosongan);
                    $("#port_bir_kosongan").html(portbirkosongan);

                    if (portbibkosongan > 0) {
                      $("#port_bib_kosongan").removeClass();
                      $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#port_bib_kosongan").removeClass();
                      $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-success btn-md btn-circle');
                    }

                    if (portbirkosongan > 0) {
                      $("#port_bir_kosongan").removeClass();
                      $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#port_bir_kosongan").removeClass();
                      $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-success btn-md btn-circle');
                    }
                  }

                  // KM 2 - 30 KOSONGAN
                  for (var i = 0; i < sizekosongan; i++) {
                    var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                    totalKosongan += jumlahfix;

                    if (jumlahfix > 0) {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonkosongan"+i).removeClass();
                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonkosongan"+i).removeClass();
                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
                    }

                  $("#vehicleonkosongan"+i).html(jumlahfix);
                }

               // SIMPANG BAYAH MUATAN PORT BIB & BIR
                for (var i = 0; i < sizemuatan2; i++) {
                  var kmlist    = arraydatamuatan2[i][0];
                  var jumlahfix = arraydatamuatan2[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                  totalMuatan2 += jumlahfix;

                  if (kmlist == "gb5_port_bib_antrian") {
                    portbibmuatan += jumlahfix;
                  }else if (kmlist == "gb6_port_bir_antrian_wb") {
                    portbirmuatan += jumlahfix;
                  }

                  console.log("portbibmuatan : ", portbibmuatan);


                  $("#port_bib_muatan").html(portbibmuatan);
                  $("#port_bir_muatan").html(portbirmuatan);

                  if (portbibmuatan > 0) {
                    $("#port_bib_muatan").removeClass();
                    $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#port_bib_muatan").removeClass();
                    $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
                  }

                  if (portbirmuatan > 0) {
                    $("#port_bir_muatan").removeClass();
                    $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#port_bir_muatan").removeClass();
                    $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
                  }
                }

                // KM 2 - 30 MUATAN
                for (var i = 0; i < sizemuatan; i++) {
                  var jumlahfix        = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                  totalMuatan += jumlahfix;

                    // LOOP FOR ALL KM MUATAN EXCEPT KM 1
                    if (jumlahfix > 0) {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonmuatan"+i).removeClass();

                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
                    }else {
                      $("#labelvehicleonmuatan"+i).removeClass();
                      $("#vehicleonmuatan"+i).removeClass();

                      $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                      $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
                    }

                    $("#vehicleonmuatan"+i).html(jumlahfix);
                }

                  $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2) + " || Last Check : " + response.lasttime) ;
                  $("#lastupdateconsolidated").show();
                  intervalkmlist = setInterval(showTableMuatan, 15000);
            }else {
              console.log("Data is Empty");
              console.log("response lasttime : ", response.lasttime);
              // SIMPANG BAYAH KOSONGAN PORT BIB & BIR
              for (var i = 0; i < 0; i++) {
                  $("#port_bib_kosongan").html(1);
                  $("#port_bir_kosongan").html(1);

                  $("#port_bib_kosongan").removeClass();
                  $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-success btn-md btn-circle');

                  $("#port_bir_kosongan").removeClass();
                  $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-success btn-md btn-circle');
              }

              // KM 2 - 30 KOSONGAN
              for (var i = 0; i < 32; i++) {
                $("#labelvehicleonmuatan"+i).removeClass();
                $("#vehicleonkosongan"+i).removeClass();
                $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
                $("#vehicleonkosongan"+i).html(0);
              }

           // SIMPANG BAYAH MUATAN PORT BIB & BIR
            for (var i = 0; i < sizemuatan2; i++) {
              $("#port_bib_muatan").html(0);
              $("#port_bir_muatan").html(0);

              $("#port_bib_muatan").removeClass();
              $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');

              $("#port_bir_muatan").removeClass();
              $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
            }

            // KM 2 - 30 MUATAN
            for (var i = 0; i < 32; i++) {
                // LOOP FOR ALL KM MUATAN EXCEPT KM 1
                  $("#labelvehicleonmuatan"+i).removeClass();
                  $("#vehicleonmuatan"+i).removeClass();

                  $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                  $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
                  $("#labelvehicleonmuatan"+i).removeClass();
                  $("#vehicleonmuatan"+i).removeClass();

                  $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                  $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');

                $("#vehicleonmuatan"+i).html(0);
            }

              $("#lastupdateconsolidated").html("Kosongan : " + (0) +" || Muatan : " + (0) + " || Total : " + (0) + " || Last Check : " + response.lasttime) ;
              $("#lastupdateconsolidated").show();
              intervalkmlist = setInterval(showTableMuatan, 15000);
            }
        },"json");
  }
</script>
