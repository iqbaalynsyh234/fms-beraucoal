<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

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

  /* edit style datepicker*/
  .datetimepicker {
    background: #D32E36;
  }

  .prev,
  .switch,
  .next,
  .today {
    background: #FFF;
  }

  .dow {
    color: #FFF;
    padding: 6px;
  }

  .table-condensed tbody tr td {
    color: #FFF;
  }

  .datetimepicker .datetimepicker-days table tbody tr td:hover {
    background-color: #000;
  }

  .datetimepicker .datetimepicker-years table tbody tr td span:hover {
    background-color: #000;
  }

  .datetimepicker .datetimepicker-months table tbody tr td span:hover {
    background-color: #000;
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
              <h5>HISTORICAL - DEVELOPMENT</h5>
            </header>
            <div class="tools">
              <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <!-- <a class="t-close btn-color fa fa-times" href="javascript:;"></a> -->
            </div>
          </div>
          <div class="card-body">
            <form class="form-horizontal" id="frmsearch" onsubmit="javascript:return frm_search();">
              <input type="hidden" name="idkmfix" id="idkmfix">
              <input type="hidden" name="idromfix" id="idromfix">
              <input type="hidden" name="idpoolfix" id="idpoolfix">
            <div class="row">
              <!-- <div class="col-md-1 col-sm-2">
                <p><b>Date : </b></p>
              </div> -->
              <div class="input-group date form_date col-md-2 col-sm-5" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                <input class="form-control" type="text" readonly name="date" id="startdate" value="<?= date('d-m-Y') ?>">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
              </div>

              <div class="col-md-2 col-sm-5">
                <select class="form-control select2" name="starttime" id="starttime">
                  <?php for ($i=0; $i < 24; $i++) {?>
                    <?php if ($i < 10) {?>
                      <option value="<?php echo "0".$i.":"."00"; ?>"><?php echo "0".$i.":"."00";?></option>
                    <?php }else{?>
                      <option value="<?php echo $i.":"."00"; ?>"><?php echo $i.":"."00";?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>


              <div class="col-md-2">
                <select class="form-control select2" name="contractor" id="contractor">
                  <!-- onchange="getVehicleByContractor()" -->
                  <option value="0">--All Contractor</option>
                  <?php for ($i=0; $i < sizeof($company); $i++) {?>
                    <option value="<?php echo $company[$i]->company_id ?>"><?php echo $company[$i]->company_name ?></option>
                  <?php } ?>
                </select>
              </div>

              <!-- <div class="col-md-3" id="showSearchNopol">
                <select class="form-control select2" name="searchnopol" id="searchnopol" onchange="forsearchinput()">
                  <?php $privilegecode = $this->sess->user_id_role; ?>
                  <?php $user_id       = $this->sess->user_id; ?>
                  <?php $user_company  = $this->sess->user_company; ?>
                    <?php if ($privilegecode == 5 || $privilegecode == 6) {?>
                      <option value="0">--Vehicle List</option>
                      <?php for ($i=0; $i < sizeof($vehicle); $i++) {?>
                        <?php $vCompany = $vehicle[$i]['vehicle_company'] ?>
                          <?php if ($vCompany == $user_company) {?>
                            <option value="<?php echo $vehicle[$i]['vehicle_no'] ?>"><?php echo $vehicle[$i]['vehicle_no'] ?></option>
                          <?php } ?>
                      <?php } ?>
                    <?php }else {?>
                      <option value="0">--Vehicle List</option>
                      <?php for ($i=0; $i < sizeof($vehicle); $i++) {?>
                            <option value="<?php echo $vehicle[$i]['vehicle_no'] ?>"><?php echo $vehicle[$i]['vehicle_no'] ?></option>
                      <?php } ?>
                    <?php } ?>
                </select>
              </div> -->

              <!-- <div class="col-md-2" id="showSearchNOutOfHauling" style="display:none;">
                <select class="form-control select2" name="forsearchoutofhauling" id="forsearchoutofhauling" onchange="forsearchoutofhauling()" style="width:100%;">
                </select>
              </div> -->

              <div class="col-md-2">
                <select class="form-control select2" name="mapsOptions" id="mapsOptions">
                  <!-- onchange="mapsOptions()" -->
                  <option value="0">--Maps Option</option>
                  <option value="showHeatmap">Maps</option>
                  <option value="showTableMuatan1">Quick Count Data</option>
                  <!-- <option value="showTableMuatan2">Consolidated Data</option> -->
                  <option value="showTableRom">ROM</option>
                  <option value="showTablePort">PORT</option>
                  <option value="showTablePool">POOL</option>
                  <option value="outofhauling">Out Of Hauling</option>
                  <!-- <option value="offlinevehicle">Offline Vehicle</option> -->
                  <option value="standardmaps">Maps Standard</option>
                </select>
              </div>

              <div class="col-md-2">
                <button type="submit" class="btn btn-success">Search</button>
              </div>

              <div class="col-md-2">
                <img id="loader2" style="display:none;" src="<?php echo base_url();?>assets/images/anim_wait.gif" />
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="result" style="display: none;">
    <div class="col-md-12">

      <!-- MAPS OPTION ROM -->
      <div id="tableShowRom" style="width: 100%; max-height: 400px; display:none;">
        <div class="row">
          <div class="col-md-12">
            <p style="position: absolute;
              right: 40px;"
              id="jumlahtotalInRom">
            </p>
          </div>
        </div> <br>
        <div class="row" id="contentrom"></div>
      </div>

      <div id="tableShowPort" style="width: 100%; max-height: 400px; display:none;">
        <div class="row">
          <div class="col-md-12">
            <p style="position: absolute;
              right: 40px;"
              id="jumlahtotalInPort">
            </p>
          </div>
        </div>
        <div class="row contentport" >
          <div class="col-md-3" id="contentportbib"></div>
          <div class="col-md-3" id="contentportbir"></div>
          <div class="col-md-3" id="contentportbbc"></div>
          <div class="col-md-3" id="contentporttia"></div>
        </div>
      </div>

      <div id="tableShowPool" style="width: 100%; max-height: 400px; display:none;">
        <div class="row">
          <div class="col-md-12">
            <p style="position: absolute;
              right: 40px;"
              id="jumlahtotalinpool">
            </p>
          </div>
        </div> <br>
        <div class="row" id="contentpool"></div>
      </div>

      <div id="tableShowPoolNew" style="width: 100%; max-height: 400px; display:none;">
        <div class="row">
          <div class="col-md-12">
            <button class="btn btn-warning btn-sm" style="position: absolute; left: 20px; display:none;" id="btnpoolback" onclick="showTablePool();">
              <span class="fa fa-angle-left"> </span>
              Back
            </button>
            <p style="position: absolute;
              right: 40px;"
              id="jumlahtotalinpoolnew">
            </p>
          </div>
        </div> <br><br>
        <input type="text" id="poolparentID" hidden>
        <div class="row" id="contentpoolnew"></div>
      </div>

      <div class="card" id="cardShowMap">
        <div class="card-body">

          <div id="tableShowOutOfHauling" style="width: 100%; max-height: 400px; display:none;">
            <div class="row" style="margin-top:0%; margin-left:0%;">
              <div class="col-md-3">
                <div class="btn-group text-center">
                  <div type="button" id="labelvehicleoutofhauling" style="font-size:16px;" onclick="listOutOfHauling();"></div>
                  <div type="button" id="vehicleoutofhauling" style="font-size:16px;" onclick="listOutOfHauling();"></div>
                </div>
              </div>

              <div class="col-md-9">
                <p style="position: absolute;
                  right: 40px;"
                  id="jumlahtotaloutofhauling">
                </p>
              </div>
            </div>
          </div>

          <div id="tableShowVehicleOffline" style="width: 100%; max-height: 400px; display:none;">
            <div class="row">
              <div class="col-md-12">
                <p style="position: absolute;
                  right: 40px;"
                  id="jumlahtotalOfflineVehicle">
                </p>
              </div>
            </div> <br>
            <div class="row" style="margin-top:0%; margin-left:0%;">
              <div class="col-md-2">
                <table class="table table-bordered" style="font-size:12px;">
                    <tr>
                      <td>
                        <div class="btn-group">
                          <div type="button" class="" id="labelvehicleoffline" onclick="modalPoolFromMasterData('modalState');"></div>
                          <div type="button" class="" id="vehicleoffline" onclick="modalPoolFromMasterData('modalState');"></div>
                        </div>
                      </td>
                    </tr>
                </table>
              </div>
            </div>
          </div>

          <div id="mapshowfix" style="display:none;">
            <input type="hidden" id="valueMode" value="0">
              <div id="mapShow">
                 <div id="mapsnya" class="mapsClass1"></div>
              </div>
          </div>

          <div id="tableShowMuatan" style="width: 100%; height: 560px; display:none;">
            <!-- display:none; -->

            <div class="row">
              <div class="col-md-9">
                <p style="margin-left: 1%; font-size:12px;">Kosongan || Muatan</p>
              </div>

              <div class="col-md-3">
                <p style="position: absolute;
                  position: absolute;
                  margin-right: 0;"
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

<script type="text/javascript">
  $(document).ready(function() {
    //edit datepicker
    $(".glyphicon-arrow-right").html(">>");
    $(".glyphicon-arrow-left").html("<<");
  });

  var markers  = [];
  var markerss = [];
  var heatmap  = [];
  var map;
  var camdevices        = ["TK510CAMDOOR", "TK510CAM", "GT08", "GT08DOOR", "GT08CAM", "GT08CAMDOOR"];
  var bibarea           = ["KM", "POOL", "ST", "ROM", "PIT", "PORT", "POOl", "WS", "WB", "PT.BIB"];

  var car = "M 2 2 C 2 1 3 0 5 0 H 19 C 21 0 22 1 22 2 V 17 H 2 Z M 3 2 C 3.6667 2.6667 4.3333 3.3333 5 4 H 19 C 19.6667 3.3333 20.3333 2.6667 21 2 C 21 1 20.3333 1.3333 20 1 H 4 V 1 C 3.6667 1.3333 3 1 3 2 M 19 5 V 13 C 19.6667 13.3333 20.3333 13.6667 21 14 V 4 Z M 5 5 H 5 C 4.3333 4.6667 3.6667 4.3333 3 4 V 14 C 3.6667 13.6667 4.3333 13.3333 5 13 Z M 6 16 H 18 V 15 H 6 Z M 7 8 V 13 V 13 H 8 V 8 Z M 10 8 V 13 H 11 V 8 M 17 8 H 16 V 13 H 17 Z M 13 8 V 13 V 13 V 13 H 14 V 8 Z M 0 4 C 0 4 0 3 1 3 H 2 V 4 Z M 22 4 V 3 V 3 H 23 C 24 3 24 4 24 4 H 24 Z M -1 19 H 3 V 18 H 4 V 17 H 20 V 18 H 21 H 21 V 19 H 25 V 61 H -1 Z Z M 1 21 V 54 C 1.6667 43.6667 2.3333 33.3333 2 23 H 22 C 21.6667 33.3333 22.3333 43.6667 23 54 V 21 V 21 Z Z M 5 27 V 53 H 6 V 27 Z M 19 27 H 18 V 53 V 53 H 19 Z M 15 27 H 14 V 53 V 53 V 53 H 15 Z M 9 27 V 53 H 10 V 27 Z";

  function initMap() {
    map = new google.maps.Map(document.getElementById("mapsnya"), {
      zoom: 14,
      center: { lat: parseFloat(-3.7288), lng: parseFloat(115.6452)},
      mapTypeId: "satellite",
      options: {
        gestureHandling: 'greedy'
      }
    });

  // TOOGEL BUTTON BIB MAP
    var toggleButton = document.createElement("button");
    toggleButton.textContent = "BIB Maps";
    toggleButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

     toggleButton.addEventListener("click", () => {
      addoverlay(map);
    });
}

  function frm_search(){
    jQuery("#loader2").show();
    $("#result").hide();
    $.post("<?php echo base_url() ?>historical/searchhistorical", $("#frmsearch").serialize(), function(response){
      jQuery("#loader2").hide();
      console.log("searchhistorical : ", response);
      var code       = response.code;

        if (response.code == 200) {
          var mapsoption = response.mapsoption;
          if (response.mapsoption == "showHeatmap") {
            $("#btnpoolback").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowRom").hide();

            var data       = response.data;
            showHeatmap(data);
            $("#mapshowfix").show();
          }else if (response.mapsoption == "showTableMuatan1") {
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
            $("#btnpoolback").hide();
            $("#mapshowfix").hide();

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

            var dataRom            = response.dataRominQuickCount;
            var dataRomfix         = Object.entries(dataRom);
            var jumlahDataInRom    = 0;

            var middle_limit = response.datafixlimitkm1muatan[0].mapsetting_middle_limit;
            var top_limit    = response.datafixlimitkm1muatan[0].mapsetting_top_limit;

            var htmlRominQuickCount = "";
                for (var i = 0; i < dataRomfix.length; i++) {
                  // console.log("dataRomfix : ", dataRomfix[i][0]);
                  htmlRominQuickCount += '<b>'+dataRomfix[i][0]+'</b>';
                  htmlRominQuickCount += '&nbsp;:&nbsp;';
                  if (i == (dataRomfix.length - 1)) {
                    jumlahDataInRom += dataRomfix[i][1];
                    htmlRominQuickCount += '<b>'+dataRomfix[i][1]+'&nbsp;</b>';
                  }else {
                    jumlahDataInRom += dataRomfix[i][1];
                    htmlRominQuickCount += '<b>'+dataRomfix[i][1]+'&nbsp;|&nbsp;</b>';
                  }
                }
              $("#listrominquickcount").html(htmlRominQuickCount + " | TOTAL : " + (jumlahDataInRom));

              var dataPort    = response.dataPortinQuickCount;
              var dataPortfix = Object.entries(dataPort);
              var jumlahDataInPortBIB = 0;
              var jumlahDataInPortBIR = 0;

              var htmlPortinQuickCount = "";
              htmlPortinQuickCount += '<table>';
                htmlPortinQuickCount += '<tr>';
                  for (var j = 0; j < dataPortfix.length; j++) {
                    // console.log("dataRomfix : ", dataRomfix[i][0]);
                    htmlPortinQuickCount += '<td><b>'+dataPortfix[j][0]+'</b><td>';
                    htmlPortinQuickCount += '<td>&nbsp;:&nbsp;<td>';
                    if (j == (dataPortfix.length - 1)) {
                        if (dataPortfix[j][0] == "PORT BIB") {
                          jumlahDataInPortBIB += dataPortfix[j][1];
                        }else {
                          jumlahDataInPortBIR += dataPortfix[j][1];
                        }
                      htmlPortinQuickCount += '<td><div id="jumlahportbibplusturunan'+j+'"><b>'+dataPortfix[j][1]+'&nbsp;|&nbsp;</b></div><td>';
                    }else {
                      if (dataPortfix[j][0] == "PORT BIB") {
                        jumlahDataInPortBIB += dataPortfix[j][1];
                      }else {
                        jumlahDataInPortBIR += dataPortfix[j][1];
                      }
                      htmlPortinQuickCount += '<td><div id="jumlahportbibplusturunan'+j+'"><b>'+dataPortfix[j][1]+'&nbsp;|&nbsp;</b></div><td>';
                    }
                  }
                  htmlPortinQuickCount += '<tr>';
                htmlPortinQuickCount += '</table>';
                $("#listportinquickcount").html(htmlPortinQuickCount);

                // JUMLAH DATA DI CP DITAMBAHKAN KE DALAM PORT BIB
                var dataPortCPBIB    = response.dataPortCPBIB;
                var dataPortCPBIBFIX = Object.entries(dataPortCPBIB);
                var jumlahDataInCP   = 0;
                for (var i = 0; i < dataPortCPBIBFIX.length; i++) {
                  jumlahDataInCP += dataPortCPBIBFIX[i][1];
                }
                $("#jumlahportbibplusturunan0").html('<div id="jumlahportbibplusturunan0"><b>' + (jumlahDataInPortBIB + jumlahDataInCP) + '&nbsp;|&nbsp;</b></div>');

                // JUMLAH DATA DI BIR ANT DITAMBAHKAN KE DALAM PORT BIR
                var dataPortANTBIR     = response.dataPortANTBIR;
                var dataPortANTBIRFIX  = Object.entries(dataPortANTBIR);
                var jumlahDataInANTBIR = 0;
                for (var i = 0; i < dataPortANTBIRFIX.length; i++) {
                  jumlahDataInANTBIR += dataPortANTBIRFIX[i][1];
                }
                $("#jumlahportbibplusturunan1").html('<div id="jumlahportbibplusturunan1"><b>' + (jumlahDataInPortBIR + jumlahDataInANTBIR) + '&nbsp;|&nbsp;</b> <b>TOTAL : '+ (jumlahDataInPortBIB + jumlahDataInCP + jumlahDataInPortBIR + jumlahDataInANTBIR) +'</b></div>');

                // console.log("Port BIB + CP : ", (jumlahDataInPortBIB + jumlahDataInCP));
                // console.log("Port BIR + ANT : ", (jumlahDataInPortBIR + jumlahDataInANTBIR));
                // console.log("middle_limit simultan : ", middle_limit);
                // console.log("top_limit simultan : ", top_limit);

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

                  $("#port_bib_kosongan").html(portbibkosongan);
                  $("#port_bir_kosongan").html(portbirkosongan);

                  if (portbibkosongan >= middle_limit && portbibkosongan < top_limit) {
                    $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-warning btn-md btn-circle');
                  }else if(portbibkosongan >= top_limit){
                    $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#port_bib_kosongan").addClass('timeline-badge4 btn btn-success btn-md btn-circle');
                  }

                  if (portbirkosongan >= middle_limit && portbirkosongan < top_limit) {
                    $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-warning btn-md btn-circle');
                  }else if(portbirkosongan >= top_limit){
                    $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-danger btn-md btn-circle');
                  }else {
                    $("#port_bir_kosongan").addClass('timeline-badge6 btn btn-success btn-md btn-circle');
                  }
                }

                // KM 2 - 30 KOSONGAN
                var dataallkmkosongan  = response.datafixlimitperkmallkosongan;
                var totalkmallkosongan = response.datafixlimitperkmallkosongan.length;
                // console.log("dataallkmkosongan : ", dataallkmkosongan);
                // console.log("totalkmallkosongan : ", totalkmallkosongan);
                for (var i = 0; i < sizekosongan; i++) {
                  var kmlist         = arraydatakosongan[i][0];
                  var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                  totalKosongan += jumlahfix;

                  for (var j = 0; j < totalkmallkosongan; j++) {
                    var mapsetting_name = dataallkmkosongan[j].mapsetting_name;
                      if (kmlist == mapsetting_name) {
                        var mapsetting_middle_limit_allkmkosongan = dataallkmkosongan[j].mapsetting_middle_limit;
                        var mapsetting_top_limit_allkmkosongan    = dataallkmkosongan[j].mapsetting_top_limit;
                      }else {
                        var mapsetting_middle_limit_allkmkosongan = middle_limit;
                        var mapsetting_top_limit_allkmkosongan    = top_limit;
                      }

                      if (jumlahfix >= mapsetting_middle_limit_allkmkosongan && jumlahfix < mapsetting_top_limit_allkmkosongan) {
                        $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                        $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
                      }else if(jumlahfix >= mapsetting_top_limit_allkmkosongan){
                        $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                        $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
                      }else {
                        $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                        $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
                      }

                    $("#vehicleonkosongan"+i).html(jumlahfix);
                    }
              }

             // SIMPANG BAYAH MUATAN PORT BIB & BIR
             var dataonlykm1muatan  = response.datafixlimitkm1muatan;
             var totalonlykm1muatan = response.datafixlimitkm1muatan.length;
              for (var i = 0; i < sizemuatan2; i++) {
                var kmlist    = arraydatamuatan2[i][0];
                var jumlahfix = arraydatamuatan2[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                totalMuatan2 += jumlahfix;

                if (kmlist == "gb5_port_bib_antrian") {
                  portbibmuatan += jumlahfix;
                }else if (kmlist == "gb6_port_bir_antrian_wb") {
                  portbirmuatan += jumlahfix;
                }

                $("#port_bib_muatan").html(portbibmuatan);
                $("#port_bir_muatan").html(portbirmuatan);

                // if (portbibmuatan >= middle_limit && portbibmuatan < top_limit) {
                //   $("#port_bib_muatan").addClass('timeline-badge5 btn btn-warning btn-md btn-circle');
                // }else if(portbibmuatan >= top_limit){
                //   $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
                // }else {
                //   $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
                // }
                //
                // if (portbirmuatan >= middle_limit && portbirmuatan < top_limit) {
                //   $("#port_bir_muatan").addClass('timeline-badge7 btn btn-warning btn-md btn-circle');
                // }else if(portbirmuatan >= top_limit){
                //   $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
                // }else {
                //   $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
                // }

                for (var j = 0; j < totalonlykm1muatan; j++) {
                    if (dataonlykm1muatan[0].mapsetting_bottom_limit != 0) {
                      var mapsetting_bottom_limit_allkmkosongan = dataonlykm1muatan[0].mapsetting_bottom_limit;
                    }else {
                      var mapsetting_middle_limit_allkmkosongan = 5;
                    }

                    if (dataonlykm1muatan[0].mapsetting_middle_limit != 0) {
                      var mapsetting_middle_limit_allkmkosongan = dataonlykm1muatan[0].mapsetting_middle_limit;
                    }else {
                      var mapsetting_middle_limit_allkmkosongan = 10;
                    }

                    if (dataonlykm1muatan[0].mapsetting_top_limit != 0) {
                      var mapsetting_top_limit_allkmkosongan    = dataonlykm1muatan[0].mapsetting_top_limit;
                    }else {
                      var mapsetting_top_limit_allkmkosongan    = 15;
                    }

                    if (portbibmuatan < mapsetting_bottom_limit_allkmkosongan) {
                      $("#port_bib_muatan").removeClass();
                      $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
                    }else if(portbibmuatan >= mapsetting_bottom_limit_allkmkosongan && portbibmuatan < mapsetting_middle_limit_allkmkosongan ){
                      $("#port_bib_muatan").removeClass();
                      $("#port_bib_muatan").addClass('timeline-badge5 btn btn-warning btn-md btn-circle');
                    }else if(portbibmuatan >= mapsetting_middle_limit_allkmkosongan && portbibmuatan < mapsetting_top_limit_allkmkosongan){
                      $("#port_bib_muatan").removeClass();
                      $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
                    }else {
                      $("#port_bib_muatan").removeClass();
                      $("#port_bib_muatan").addClass('timeline-badge5 btn btn-purple btn-md btn-circle');
                    }

                    if (portbirmuatan < mapsetting_bottom_limit_allkmkosongan) {
                      $("#port_bir_muatan").removeClass();
                      $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
                    }else if(portbirmuatan >= mapsetting_bottom_limit_allkmkosongan && portbirmuatan < mapsetting_middle_limit_allkmkosongan ){
                      $("#port_bir_muatan").removeClass();
                      $("#port_bir_muatan").addClass('timeline-badge7 btn btn-warning btn-md btn-circle');
                    }else if(portbirmuatan >= mapsetting_middle_limit_allkmkosongan && portbirmuatan < mapsetting_top_limit_allkmkosongan){
                      $("#port_bir_muatan").removeClass();
                      $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
                    }else {
                      $("#port_bir_muatan").removeClass();
                      $("#port_bir_muatan").addClass('timeline-badge7 btn btn-purple btn-md btn-circle');
                    }
                }
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

                  // LOOP FOR ALL KM MUATAN EXCEPT KM 1
                  for (var j = 0; j < totalkmallmuatan; j++) {
                    var mapsetting_name = dataallkmmuatan[j].mapsetting_name;
                      if (kmlist == mapsetting_name) {
                        var mapsetting_middle_limit_allkmmuatan = dataallkmmuatan[j].mapsetting_middle_limit;
                        var mapsetting_top_limit_allkmmuatan    = dataallkmmuatan[j].mapsetting_top_limit;
                      }else {
                        var mapsetting_middle_limit_allkmmuatan = middle_limit;
                        var mapsetting_top_limit_allkmmuatan    = top_limit;
                      }

                      if (jumlahfix >= mapsetting_middle_limit_allkmmuatan && jumlahfix < mapsetting_top_limit_allkmmuatan) {
                        $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                        $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
                      }else if(jumlahfix >= mapsetting_top_limit_allkmmuatan){
                        $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                        $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
                      }else {
                        $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                        $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
                      }

                      $("#vehicleonmuatan"+i).html(jumlahfix);
                  }
              }

            $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2)) ;
            $("#lastupdateconsolidated").show();
            $("#tableShowMuatan").show();
          }else if (response.mapsoption == "showTableRom") {
            $("#mapShow").hide();
            $("#cardShowMap").hide();
            $("#realtimealertshowhide").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowKosongan").hide();
            $("#tableShowPort").hide();
            $("#tableShowPool").hide();
            $("#tableShowPoolNew").hide();
            $("#tableShowOutOfHauling").hide();
            $("#tableShowVehicleOffline").hide();
            $("#showSearchNopol").show();
            $("#showSearchNOutOfHauling").hide();
            $("#tableShowRom").show();
            $("#btnpoolback").hide();
            $("#mapshowfix").hide();
            $("#tableShowPort").hide();

            var datainrom           = Object.entries(response.data);
            var totaldata           = datainrom.length;
            var allStreet           = response.allstreet;
            var jumlahtotalInRom    = 0;
            var htmlrom             = "";
            var dataMapSettingInRom = response.datafixlimit;

            for (var i = 0; i < totaldata; i++) {
              jumlahtotalInRom += datainrom[i][1];
                htmlrom += '<div class="col-xl-3 col-md-6 col-12" onclick="listVehicleOnRom('+allStreet[i].street_id+')" style="cursor : pointer;">';
                  for (var j = 0; j < dataMapSettingInRom.length; j++) {
                    if (datainrom[i][0] == dataMapSettingInRom[j].mapsetting_name_alias) {
                      var middle_limit_in_rom = dataMapSettingInRom[j].mapsetting_middle_limit;
                      var top_limit_in_rom    = dataMapSettingInRom[j].mapsetting_top_limit;

                      if (datainrom[i][1] > middle_limit_in_rom && datainrom[i][1] < top_limit_in_rom) {
                        htmlrom += '<div class="info-box bg-warning">';
                      }else if (datainrom[i][1] > top_limit_in_rom) {
                        htmlrom += '<div class="info-box bg-danger">';
                      }else {
                        htmlrom += '<div class="info-box bg-blue">';
                      }
                    }
                  }
                  htmlrom += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlrom += '<i class="material-icons" style="font-size:50px; padding-top:10px;">my_location</i>';
                  htmlrom += '</span>';
                  htmlrom += '<div class="info-box-content">';
                    htmlrom += '<span class="info-box-text" style="font-size:15px;">'+datainrom[i][0]+'</span>';
                    htmlrom += '<span class="info-box-number" style="font-size:22px;"><b>'+datainrom[i][1]+'</b></span>';
                  htmlrom += '</div>';
                htmlrom += '</div>';
              htmlrom += '</div>';
            }

            $("#contentrom").html(htmlrom);
            $("#jumlahtotalInRom").html("Total : " +jumlahtotalInRom);
            $("#jumlahtotalInRom").show();
            $("#tableShowRom").show();
          }else if (response.mapsoption == "showTablePort") {
            $("#btnpoolback").hide();
            $("#cardShowMap").hide();
            $("#mapShow").hide();
            $("#realtimealertshowhide").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowKosongan").hide();
            $("#tableShowRom").hide();
            $("#tableShowPool").hide();
            $("#tableShowPoolNew").hide();
            $("#tableShowOutOfHauling").hide();
            $("#tableShowVehicleOffline").hide();
            $("#tableShowPort").show();
            $("#mapshowfix").hide();

            var datainport        = Object.entries(response.data);
            var totaldata         = datainport.length;
            var allStreet         = response.allstreet;
            var jumlahtotalInPort           = 0;
            var jumlahtotalInPortBib        = 0;
            var jumlahtotalInPortBibsebaran = 0;

            var jumlahtotalInPortBir        = 0;
            var jumlahtotalInPortBirsebaran = 0;

            var jumlahtotalInPortBbc        = 0;
            var jumlahtotalInPortBbcsebaran = 0;

            var htmlportbib     = "";
            var htmlportbir     = "";
            var htmlportbbc     = "";
            var htmlporttia     = "";

            var dataLimitInPort = response.datafixlimit;

              // PORT BIB
                for (var i = 0; i < 8; i++) {
                  jumlahtotalInPortBib += datainport[i][1];
                }
                htmlportbib += '<div>';
                  htmlportbib += '<ol>';
                    htmlportbib += '<li>';
                      htmlportbib += '<ol>';
                        htmlportbib += '<li>';
                          for (var j = 0; j < dataLimitInPort.length; j++) {
                            if (datainport[0][0] == dataLimitInPort[j].mapsetting_name_alias) {
                              var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
                              var top_limit_in_port    = dataLimitInPort[j].mapsetting_top_limit;

                              if (jumlahtotalInPortBib > middle_limit_in_port && jumlahtotalInPortBib < top_limit_in_port) {
                                htmlportbib += '<h3 class="level-3 info-box bg-warning" onclick="listVehicleOnPort('+allStreet[0].street_id+')" style="cursor : pointer;">';
                              }else if (jumlahtotalInPortBib >= top_limit_in_port) {
                                htmlportbib += '<h3 class="level-3 info-box bg-danger" onclick="listVehicleOnPort('+allStreet[0].street_id+')" style="cursor : pointer;">';
                              }else {
                                htmlportbib += '<h3 class="level-3 info-box bg-blue" onclick="listVehicleOnPort('+allStreet[0].street_id+')" style="cursor : pointer;">';
                              }
                            }
                          }
                          htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:0%; background-color: red;">';
                            htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/portbib.png" style="font-size:50px;">';
                          htmlportbib += '</span>';
                          htmlportbib += '<div class="info-box-content" style="color:white;">';
                            htmlportbib += '<span class="info-box-number" style="font-size:20px;">'+datainport[0][0]+'</span>';
                            htmlportbib += '<span class="info-box-text" style="font-size:30px;"><b>'+jumlahtotalInPortBib+'</b></span>';
                          htmlportbib += '</div>';
                          htmlportbib += '</h3>';
                          htmlportbib += '<ol class="level-4-wrapper">';
                        for (var i = 1; i < 8; i++) {
                            htmlportbib += '<li>';
                            for (var j = 0; j < dataLimitInPort.length; j++) {
                                var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
                                if (datainport[i][0] == dataLimitInPort[j].mapsetting_name_alias) {
                                var top_limit_in_port    = dataLimitInPort[j].mapsetting_top_limit;

                                if (datainport[i][1] > middle_limit_in_port && datainport[i][1] < top_limit_in_port) {
                                  htmlportbib += '<h4 class="level-4 rectangle bg-warning" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                                }else if (datainport[i][1] >= top_limit_in_port) {
                                  htmlportbib += '<h4 class="level-4 rectangle bg-danger" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                                }else {
                                  htmlportbib += '<h4 class="level-4 rectangle bg-blue" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                                }
                              }
                            }
                                htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:0%;">';
                                  htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/bibcp'+i+'.png" style="font-size:50px;">';
                                htmlportbib += '</span>';
                                htmlportbib += '<div class="info-box-content" style="color:white;">';
                                // htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                                htmlportbib += '<span class="info-box-text text-center" style="font-size:30px;"><b>'+datainport[i][1]+'</b></span>';
                                htmlportbib += '<span class="info-box-number" style="font-size:15px;">&nbsp;</span>';
                                htmlportbib += '</div>';
                              htmlportbib += '</h4>';
                            htmlportbib += '</li>';
                        }
                      htmlportbib += '</ol>';
                    htmlportbib += '</li>';
                  htmlportbib += '</ol>';
                htmlportbib += '</li>';
              htmlportbib += '</ol>';
            htmlportbib += '</div> <br><br>';
            $("#contentportbib").html(htmlportbib);

            // PORT BIR
            for (var i = 8; i < 14; i++) {
              jumlahtotalInPortBir += datainport[i][1];
            }
                htmlportbir += '<div>';
                  htmlportbir += '<ol>';
                    htmlportbir += '<li>';
                      htmlportbir += '<ol>';
                        htmlportbir += '<li>';
                            for (var j = 0; j < dataLimitInPort.length; j++) {
                              if (datainport[0][0] == dataLimitInPort[j].mapsetting_name_alias) {
                                var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
                                var top_limit_in_port    = dataLimitInPort[j].mapsetting_top_limit;

                                if (jumlahtotalInPortBir > middle_limit_in_port && jumlahtotalInPortBir < top_limit_in_port) {
                                  htmlportbir += '<h3 class="level-3 info-box bg-warning" onclick="listVehicleOnPort('+allStreet[8].street_id+')" style="cursor : pointer;">';
                                }else if (jumlahtotalInPortBir >= top_limit_in_port) {
                                  htmlportbir += '<h3 class="level-3 info-box bg-danger" onclick="listVehicleOnPort('+allStreet[8].street_id+')" style="cursor : pointer;">';
                                }else {
                                  htmlportbir += '<h3 class="level-3 info-box bg-blue" onclick="listVehicleOnPort('+allStreet[8].street_id+')" style="cursor : pointer;">';
                                }
                              }
                            }
                          htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:0%;">';
                            htmlportbir += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                            htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/portbib.png" style="font-size:50px;">';
                          htmlportbir += '</span>';
                          htmlportbir += '<div class="info-box-content" style="color:white;">';
                            htmlportbir += '<span class="info-box-number" style="font-size:20px;">'+datainport[8][0]+'</span>';
                            htmlportbir += '<span class="info-box-text" style="font-size:30px;"><b>'+jumlahtotalInPortBir+'</b></span>';
                          htmlportbir += '</div>';
                          htmlportbir += '</h3>';
                          htmlportbir += '<ol class="level-4-wrapper">';
                              for (var i = 9; i < 14; i++) {
                                htmlportbir += '<li>';
                                for (var j = 0; j < dataLimitInPort.length; j++) {
                                  if (datainport[i][0] == dataLimitInPort[j].mapsetting_name_alias) {
                                    var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
                                    var top_limit_in_port    = dataLimitInPort[j].mapsetting_top_limit;

                                    if (datainport[i][1] > middle_limit_in_port && datainport[i][1] < top_limit_in_port) {
                                      htmlportbir += '<h4 class="level-4 rectangle bg-warning" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                                    }else if (datainport[i][1] >= top_limit_in_port) {
                                      htmlportbir += '<h4 class="level-4 rectangle bg-danger" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                                    }else {
                                      htmlportbir += '<h4 class="level-4 rectangle bg-blue" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                                    }
                                  }
                                }
                                htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:0%;">';
                                  htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/birls'+i+'.png" style="font-size:50px;">';
                                htmlportbir += '</span>';
                                htmlportbir += '<div class="info-box-content" style="color:white;">';
                                htmlportbir += '<span class="info-box-text text-center" style="font-size:30px;"><b>'+datainport[i][1]+'</b></span>';
                                htmlportbir += '<span class="info-box-number" style="font-size:15px;">&nbsp;</span>';
                                htmlportbir += '</div>';
                              htmlportbir += '</h4>';
                            htmlportbir += '</li>';
                        }
                      htmlportbir += '</ol>';
                    htmlportbir += '</li>';
                  htmlportbir += '</ol>';
                htmlportbir += '</li>';
              htmlportbir += '</ol>';
            htmlportbir += '</div>';
            $("#contentportbir").html(htmlportbir);

            // PORT BBC
            htmlportbbc += '<div>';
              htmlportbbc += '<ol>';
                htmlportbbc += '<li>';
                  htmlportbbc += '<ol>';
                    htmlportbbc += '<li>';
                    for (var j = 0; j < dataLimitInPort.length; j++) {
                      if (datainport[14][0] == dataLimitInPort[j].mapsetting_name_alias) {
                        var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
                        var top_limit_in_port    = dataLimitInPort[j].mapsetting_top_limit;

                        if (datainport[14][1] > middle_limit_in_port && datainport[14][1] < top_limit_in_port) {
                          htmlportbbc += '<h3 class="level-3 info-box bg-warning" onclick="listVehicleOnPort('+allStreet[14].street_id+')" style="cursor : pointer;">';
                        }else if (datainport[14][1] >= top_limit_in_port) {
                          htmlportbbc += '<h3 class="level-3 info-box bg-danger" onclick="listVehicleOnPort('+allStreet[14].street_id+')" style="cursor : pointer;">';
                        }else {
                          htmlportbbc += '<h3 class="level-3 info-box bg-blue" onclick="listVehicleOnPort('+allStreet[14].street_id+')" style="cursor : pointer;">';
                        }
                      }
                    }
                      htmlportbbc += '<span class="info-box-icon push-bottom" style="margin-top:0%;">';
                        htmlportbbc += '<img src="<?php echo base_url();?>/assets/bib/images/portbib.png" style="font-size:50px;">';
                      htmlportbbc += '</span>';
                      htmlportbbc += '<div class="info-box-content" style="color:white;">';
                        htmlportbbc += '<span class="info-box-number" style="font-size:20px;">'+datainport[14][0]+'</span>';
                        htmlportbbc += '<span class="info-box-text" style="font-size:30px;"><b>'+datainport[14][1]+'</b></span>';
                      htmlportbbc += '</div>';
                      htmlportbbc += '</h3>';
                      htmlportbbc += '</li>';
                    htmlportbbc += '</ol>';
                  htmlportbbc += '</li>';
                htmlportbbc += '</ol>';
              htmlportbbc += '</div>';
            $("#contentportbbc").html(htmlportbbc);

            // PORT TIA
            htmlporttia += '<div>';
              htmlporttia += '<ol>';
                htmlporttia += '<li>';
                  htmlporttia += '<ol>';
                    htmlporttia += '<li>';
                        for (var j = 0; j < dataLimitInPort.length; j++) {
                          if (datainport[15][0] == dataLimitInPort[j].mapsetting_name_alias) {
                            var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
                            var top_limit_in_port    = dataLimitInPort[j].mapsetting_top_limit;

                            if (datainport[15][1] > middle_limit_in_port && datainport[15][1] < top_limit_in_port) {
                              htmlporttia += '<h3 class="level-3 info-box bg-warning" onclick="listVehicleOnPort('+allStreet[15].street_id+')" style="cursor : pointer;">';
                            }else if (datainport[15][1] >= top_limit_in_port) {
                              htmlporttia += '<h3 class="level-3 info-box bg-danger" onclick="listVehicleOnPort('+allStreet[15].street_id+')" style="cursor : pointer;">';
                            }else {
                              htmlporttia += '<h3 class="level-3 info-box bg-blue" onclick="listVehicleOnPort('+allStreet[15].street_id+')" style="cursor : pointer;">';
                            }
                          }
                        }
                      htmlporttia += '<span class="info-box-icon push-bottom" style="margin-top:0%;">';
                        htmlporttia += '<img src="<?php echo base_url();?>/assets/bib/images/portbib.png" style="font-size:50px;">';
                      htmlporttia += '</span>';
                      htmlporttia += '<div class="info-box-content" style="color:white;">';
                        htmlporttia += '<span class="info-box-number" style="font-size:20px;">'+datainport[15][0]+'</span>';
                        htmlporttia += '<span class="info-box-text" style="font-size:30px;"><b>'+datainport[15][1]+'</b></span>';
                      htmlporttia += '</div>';
                      htmlporttia += '</h3>';
                      htmlporttia += '</li>';
                    htmlporttia += '</ol>';
                  htmlporttia += '</li>';
                htmlporttia += '</ol>';
              htmlporttia += '</div>';
            $("#contentporttia").html(htmlporttia);

            for (var i = 0; i < 16; i++) {
              jumlahtotalInPort += datainport[i][1];
            }

            $("#jumlahtotalInPort").html("Total : " +jumlahtotalInPort);
            $("#jumlahtotalInPort").show();
            $("#tableShowPort").show();
          }else if (response.mapsoption == "showTablePool") {
            $("#btnpoolback").hide();
            $("#cardShowMap").hide();
            $("#mapShow").hide();
            $("#realtimealertshowhide").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowKosongan").hide();
            $("#tableShowRom").hide();
            $("#tableShowPort").hide();
            $("#tableShowOutOfHauling").hide();
            $("#tableShowVehicleOffline").hide();
            $("#showSearchNopol").show();
            $("#showSearchNOutOfHauling").hide();
            $("#tableShowPool").hide();
            $("#tableShowPoolNew").show();
            $("#mapshowfix").show();

            $("#contentpoolnew").html("");
            var datainpool        = Object.entries(response.data);
            var totaldata         = datainpool.length;
            var allcompany        = response.allcompany;
            var jumlahtotalinpool = 0;

            var htmlpool          = "";
            // console.log("totaldata : ", totaldata);

            for (var i = 0; i < totaldata; i++) {
              jumlahtotalinpool += datainpool[i][1];
              htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="getPoolByParent('+allcompany[i].company_id+')" style="cursor : pointer;">';
                htmlpool += '<div class="info-box bg-blue">';
                  htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
                  htmlpool += '</span>';
                  htmlpool += '<div class="info-box-content">';
                    htmlpool += '<span class="info-box-text" style="font-size:15px;">POOL '+datainpool[i][0]+'</span>';
                    htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+datainpool[i][1]+'</b></span>';
                  htmlpool += '</div>';
                htmlpool += '</div>';
              htmlpool += '</div>';
            }

            htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="getPoolOther()" style="cursor : pointer;">';
              htmlpool += '<div class="info-box bg-blue">';
                htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                  htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
                htmlpool += '</span>';
                htmlpool += '<div class="info-box-content">';
                  htmlpool += '<span class="info-box-text" style="font-size:15px;">Others</span>';
                  htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+response.dataoutofhauling.outofhauling+'</b></span>';
                htmlpool += '</div>';
              htmlpool += '</div>';
            htmlpool += '</div>';

            $("#contentpoolnew").html(htmlpool);
            $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool);
            $("#jumlahtotalinpoolnew").show();
          }else if (response.mapsoption == "outofhauling") {
            $("#btnpoolback").hide();
            $("#cardShowMap").show();
            $("#mapshowfix").addClass('col-md-12');
            $("#mapsnya").removeClass();
            $("#mapsnya").addClass('mapsClass3');
            $("#realtimealertshowhide").hide();
            $("#tableShowKosongan").hide();
            $("#tableShowPort").hide();
            $("#tableShowRom").hide();
            $("#tableShowPool").hide();
            $("#tableShowPoolNew").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowVehicleOffline").hide();
            $("#showSearchNopol").hide();
            $("#showSearchNOutOfHauling").show();
            $("#mapShow").show();
            $("#tableShowOutOfHauling").show();
            $("#mapshowfix").show();

            var valuemode = $("#valueMode").val();

            if (valuemode == 1) {
              console.log("valuemode : ", valuemode);
              heatmap.setMap(null);
            }
            // arraypointheatmap    = [];
            var dataoutofhauling = response.data.outofhauling;
            // console.log("arraydataoutofhauling : ", arraydataoutofhauling[0]);
            // console.log("sizekosongan : ", sizekosongan);

            var labelvehicleoutofhauling        = "Out Of Hauling";
            var vehicleoutofhauling             = dataoutofhauling;
              $("#labelvehicleoutofhauling").html(labelvehicleoutofhauling);
              $("#vehicleoutofhauling").html(vehicleoutofhauling);
                if (vehicleoutofhauling > 500 && vehicleoutofhauling < 500) {
                  $("#labelvehicleoutofhauling").addClass('btn btn-warning btn-sm');
                  $("#vehicleoutofhauling").addClass('btn btn-warning btn-sm');
                }else if(vehicleoutofhauling > 500){
                  $("#labelvehicleoutofhauling").addClass('btn btn-danger btn-sm');
                  $("#vehicleoutofhauling").addClass('btn btn-danger btn-sm');
                }else {
                  $("#labelvehicleoutofhauling").addClass('btn btn-primary btn-sm');
                  $("#vehicleoutofhauling").addClass('btn btn-primary btn-sm');
                }
                $("#vehicleoutofhauling").html(vehicleoutofhauling);

            // SHOW MAPS OUT OF HAULING
            var vehicle                      = response.dataoutofhaulingmaps;
            var poolmaster                   = response.poolmaster;
            var alldataforclearmaps          = response.alldataforclearmaps;

            var bounds                       = new google.maps.LatLngBounds();
            var boundspool                   = new google.maps.LatLngBounds();

            var objoutofhauling              = vehicle;
            var objpoolmasterfixoutofhauling = poolmaster;
            console.log("obj outofhauling fix : ", objoutofhauling);
            console.log("alldataforclearmaps : ", alldataforclearmaps);

            // console.log("objpoolmasterfixoutofhauling outofhauling: ", objpoolmasterfixoutofhauling);

            for (var j = 0; j < alldataforclearmaps.length; j++) {
              // console.log("alldataforclearmaps : " + j);
              DeleteMarkerspertama(alldataforclearmaps[j].location_report_vehicle_device);
              DeleteMarkers(alldataforclearmaps[j].location_report_vehicle_device);
              DeleteMarkerspertama(alldataforclearmaps[j].location_report_vehicle_id);
              DeleteMarkers(alldataforclearmaps[j].location_report_vehicle_id);
            }

            // CHANGE VEHICLE LIST
            // $("#forsearchoutofhauling").html("");
            //
            //     var htmlchangeVList = document.getElementById('forsearchoutofhauling');
            //     htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List Out Of Hauling', "0");
            //     for (var i = 0; i < vehicle.length; i++) {
            //       htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
            //     }

            // Add multiple markers to map
             infowindow      = new google.maps.InfoWindow();
             infowindow2     = new google.maps.InfoWindow();
             infowindowkedua = new google.maps.InfoWindow();
             infowindowgif = new google.maps.InfoWindow();

             for (i = 0; i < vehicle.length; i++) {
               var position = new google.maps.LatLng(parseFloat(objoutofhauling[i].auto_last_lat), parseFloat(objoutofhauling[i].auto_last_long));
               bounds.extend(position);

               var icon = {
                 path: car,
                 scale: .5,
                 strokeColor: 'white',
                 strokeWeight: .10,
                 fillOpacity: 1,
                 fillColor: '#800080',
                 offset: '5%'
               };

               marker = new google.maps.Marker({
                 position: position,
                 map: map,
                 icon: icon,
                 title: objoutofhauling[i].vehicle_no,
                 id: objoutofhauling[i].vehicle_device
               });
               // console.log("obj di marker : ", obj);
               icon.rotation = Math.ceil(objoutofhauling[i].auto_last_course);
               marker.setIcon(icon);
               markers.push(marker);

               google.maps.event.addListener(marker, 'click', (function(marker, i) {
                 return function() {
                   var data = {device_id : marker.id};
                   console.log("Out of hauling marker clicked ", data);
                     infowindowkedua.close();

                     var center = {lat : parseFloat(objoutofhauling[i].auto_last_lat), lng: parseFloat(objoutofhauling[i].auto_last_long)};
                     var num         = Number(objoutofhauling[i].auto_last_speed);
                     var roundstring = num.toFixed(0);
                     var rounded     = Number(roundstring);

                     var addresssplit = objoutofhauling[i].auto_last_position.split(" ");
                     var inarea       = objoutofhauling[i].auto_last_position.split(",");
                     var addressfix   = bibarea.includes(addresssplit[0]);
                     if (addressfix) {
                       var addressfix = inarea[0];
                     }else {
                       var addressfix = objoutofhauling[i].auto_last_position;
                     }

                     // var ritasefix = 0;
                     //   if (response[0].autolastritase != 0) {
                     //     ritasefix = response[0].auto_last_ritase;
                     //   }else {
                     //     ritasefix = 0;
                     //   }

                     var string = objoutofhauling[i].vehicle_no + ' - ' + objoutofhauling[i].vehicle_name + "<br>" +
                       "GPS Time : " + objoutofhauling[i].auto_last_update + "<br>Position : " + addressfix + "<br>"+
                       "Coord : <a href='https://www.google.com/maps/search/?api=1&query="+objoutofhauling[i].auto_last_lat+ ',' +objoutofhauling[i].auto_last_long+"' target='_blank'>"+objoutofhauling[i].auto_last_lat+ ',' +objoutofhauling[i].auto_last_long+"</a></span></br>"+
                       // "Coord : " + objoutofhauling[i].auto_last_lat + ", " + objoutofhauling[i].auto_last_long + "<br>" +
                       "Engine : " + objoutofhauling[i].auto_last_engine + "<br>" +
                       // "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
                       "Speed : " + rounded + " kph" + "</br>" +
                       "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";

                      infowindowkedua = new google.maps.InfoWindow({
                       content: string,
                       maxWidth: 300
                     });

                     infowindowkedua.open(map, marker);
                     map.setCenter(center);
                 };
               })(marker, i));
             }
          }else if (response.mapsoption == "offlinevehicle") {
            $("#btnpoolback").hide();
            $("#cardShowMap").show();
            $("#mapShow").hide();
            $("#realtimealertshowhide").hide();
            $("#tableShowKosongan").hide();
            $("#tableShowPort").hide();
            $("#tableShowRom").hide();
            $("#tableShowPool").hide();
            $("#tableShowPoolNew").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowOutOfHauling").hide();
            $("#tableShowVehicleOffline").show();

            var sizeofflinevehicle = response.data.length;

            var labelvehicleoffline        = "Offline Vehicle";
            var vehicleoffline             = sizeofflinevehicle;
              $("#labelvehicleoffline").html(labelvehicleoffline);
              $("#vehicleoffline").html(vehicleoffline);
              $("#labelvehicleoffline").addClass('btn btn-danger btn-lg');
              $("#vehicleoffline").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
              $("#vehicleoffline").html(vehicleoffline);

              var datafix           = response.data;
              var totalrow          = datafix.length;
              var datacontractor    = response.jumlah_contractor;
              var datacontractorfix = Object.entries(datacontractor);

              var htmlcontractor = "";
              htmlcontractor += '<table>';
                htmlcontractor += '<tr>';
                var looplist = 0;
                  for (var i = 0; i < datacontractorfix.length; i++) {
                    if (datacontractorfix[i][1] != 0) {
                      looplist += 1;
                      // console.log("datacontractorfix : ", datacontractorfix[i][0]);

                        if (looplist > 1) {
                          htmlcontractor += '<td><b>|&nbsp;'+datacontractorfix[i][0]+'</b><td>';
                          htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                          htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;|&nbsp;</b><td>';
                        }else {
                          htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
                          htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                          htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;</b><td>';
                        }
                    }
                  }
                  htmlcontractor += '<tr>';
                htmlcontractor += '</table>';
                $("#contractorinlocation").html(htmlcontractor);

              var htmlofflinevehicle = "";
                if (datafix.length > 0) {
                  // var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
                  $("#modalStateTitle").html("Offline Vehicle" + " (" + totalrow + ")");
                  // $("#lastcheckpoolws").html(lastcheckpoolws);
                  htmlofflinevehicle += '<table class="table table-striped">';
                    htmlofflinevehicle += '<thead>';
                      htmlofflinevehicle += '<tr>';
                        htmlofflinevehicle += '<th>No</th>';
                        htmlofflinevehicle += '<th>Vehicle</th>';
                        // htmlofflinevehicle += '<th align="center">Engine</th>';
                        // htmlofflinevehicle += '<th align="center">Speed (Kph)</th>';
                        htmlofflinevehicle += '<th>Last Update</th>';
                        htmlofflinevehicle += '<th>Position</th>';
                        htmlofflinevehicle += '<th>Coord</th>';
                      htmlofflinevehicle += '</tr>';
                    htmlofflinevehicle += '</thead>';
                  for (var i = 0; i < datafix.length; i++) {
                    var lastupdatecetak = response.data[i].auto_last_update;
                      if (lastupdatecetak == "01-01-1970 07:00:00") {
                        lastupdatecetak = "";
                      }

                      htmlofflinevehicle += '<tr>';
                        htmlofflinevehicle += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                        htmlofflinevehicle += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                        // htmlofflinevehicle += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                        htmlofflinevehicle += '<td style="font-size:12px;color:black"><span style="color:black;">'+lastupdatecetak+'</span>';
                        htmlofflinevehicle += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_positionfix+'</span>';
                        htmlofflinevehicle += '<td style="font-size:12px;color:black"><span style="color:black;"><a href="https://maps.google.com/?q='+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'" target="_blank">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</a></span>';
                      htmlofflinevehicle += '</tr>';
                  }
                  htmlofflinevehicle += '</table>';
                  $("#modalStateContent").html(htmlofflinevehicle);
                }else {
                  alert("Data Tidak Ada");
                }
          }else if (response.mapsoption == "standardmaps") {
            $("#btnpoolback").hide();
            $("#mapshowfix").removeClass();
            $("#mapshowfix").addClass('col-md-12');
            $("#mapsnya").removeClass();
            $("#mapsnya").addClass('mapsClass1');
            $("#cardShowMap").show();
            $("#mapShow").show();
            $("#mapshowfix").show();
            $("#realtimealertshowhide").hide();
            $("#tableShowMuatan").hide();
            $("#tableShowKosongan").hide();
            $("#tableShowPort").hide();
            $("#tableShowRom").hide();
            $("#tableShowPool").hide();
            $("#tableShowPoolNew").hide();
            $("#tableShowOutOfHauling").hide();
            $("#tableShowVehicleOffline").hide();
            $("#showSearchNopol").show();
            $("#showSearchNOutOfHauling").hide();

            var valuemode = $("#valueMode").val();

            if (valuemode == 1) {
              console.log("valuemode : ", valuemode);
              heatmap.setMap(null);
            }

            var vehicle             = response.data;
            var alldataforclearmaps = response.alldataforclearmaps;
            var bounds              = new google.maps.LatLngBounds();
            objmapsstandard         = vehicle;
            infowindow              = new google.maps.InfoWindow();

            for (var j = 0; j < alldataforclearmaps.length; j++) {
              // console.log("mapsstandard : " + j);
              DeleteMarkerspertama(alldataforclearmaps[j].location_report_vehicle_device);
              DeleteMarkers(alldataforclearmaps[j].location_report_vehicle_device);
              DeleteMarkerspertama(alldataforclearmaps[j].location_report_vehicle_id);
              DeleteMarkers(alldataforclearmaps[j].location_report_vehicle_id);
            }

            for (i = 0; i < objmapsstandard.length; i++) {
              var position = new google.maps.LatLng(parseFloat(objmapsstandard[i].auto_last_lat), parseFloat(objmapsstandard[i].auto_last_long));
              bounds.extend(position);

              var nums         = Number(objmapsstandard[i].auto_last_speed);
              var roundstrings = nums.toFixed(0);
              var roundedspeed = Number(roundstrings);

              if (objmapsstandard[i].auto_last_road == "muatan") {
                if (roundedspeed == 0 && objmapsstandard[i].auto_last_engine == "ON") {
                  // ICON UNGU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ffff00',
                    offset: '5%'
                  };
                }else if (roundedspeed > 0 && objmapsstandard[i].auto_last_engine == "ON") {
                  // ICON HIJAU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#00b300',
                    offset: '5%'
                  };
                }else {
                  // ICON BIRU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ff0040',
                    offset: '5%'
                  };
                }
              }else {
                if (roundedspeed > 0 && objmapsstandard[i].auto_last_engine == "ON") {
                  // ICON HIJAU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#0000FF',
                    offset: '5%'
                  };
                }else if (roundedspeed == 0 && objmapsstandard[i].auto_last_engine == "ON") {
                  // ICON UNGU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ffff00',
                    offset: '5%'
                  };
                }else {
                  // ICON BIRU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ff0040',
                    offset: '5%'
                  };
                }
              }


                  marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    icon: icon,
                    title: objmapsstandard[i].vehicle_no,
                    id: objmapsstandard[i].vehicle_device
                  });

              icon.rotation = Math.ceil(objmapsstandard[i].auto_last_course);
              marker.setIcon(icon);
              markers.push(marker);

              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                  var data = {device_id : marker.id};
                    console.log("Maps standard marker onclik : ", response);
                    DeleteMarkers(marker.id);
                    DeleteMarkerspertama(marker.id);

                    var num         = Number(objmapsstandard[i].auto_last_speed);
                    var roundstring = num.toFixed(0);
                    var rounded     = Number(roundstring);

                    if (objmapsstandard[i].auto_last_road) {
                      if (objmapsstandard[i].auto_last_road == "muatan") {
                        if (rounded == 0 && objmapsstandard[i].auto_last_engine == "ON") {
                          laststatus = 'GPS Online';
                          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
                          // ICON UNGU
                          var icon = {
                            path: car,
                            scale: .5,
                            strokeColor: 'white',
                            strokeWeight: .10,
                            fillOpacity: 1,
                            fillColor: '#ffff00',
                            offset: '5%'
                          };
                        }else if (rounded > 0 && objmapsstandard[i].auto_last_engine == "ON") {
                          laststatus = 'GPS Online';
                          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
                          // ICON HIJAU
                          var icon = {
                            path: car,
                            scale: .5,
                            strokeColor: 'white',
                            strokeWeight: .10,
                            fillOpacity: 1,
                            fillColor: '#00b300',
                            offset: '5%'
                          };
                        }else {
                          laststatus = 'GPS Online';
                          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
                          // ICON BIRU
                          var icon = {
                            path: car,
                            scale: .5,
                            strokeColor: 'white',
                            strokeWeight: .10,
                            fillOpacity: 1,
                            fillColor: '#ff0040',
                            offset: '5%'
                          };
                        }
                      }else {
                        if (rounded == 0 && objmapsstandard[i].auto_last_engine == "ON") {
                          // ICON UNGU
                          var icon = {
                            path: car,
                            scale: .5,
                            strokeColor: 'white',
                            strokeWeight: .10,
                            fillOpacity: 1,
                            fillColor: '#ffff00',
                            offset: '5%'
                          };
                        }else if (rounded > 0 && objmapsstandard[i].auto_last_engine == "ON") {
                          // ICON HIJAU
                          var icon = {
                            path: car,
                            scale: .5,
                            strokeColor: 'white',
                            strokeWeight: .10,
                            fillOpacity: 1,
                            fillColor: '#0000FF',
                            offset: '5%'
                          };
                        }else {
                          // ICON BIRU
                          var icon = {
                            path: car,
                            scale: .5,
                            strokeColor: 'white',
                            strokeWeight: .10,
                            fillOpacity: 1,
                            fillColor: '#ff0040',
                            offset: '5%'
                          };
                        }
                      }
                    }

                    var center            = {lat : parseFloat(objmapsstandard[i].auto_last_lat), lng: parseFloat(objmapsstandard[i].auto_last_long)};

                    marker = new google.maps.Marker({
                      map: map,
                      icon: icon,
                      position: new google.maps.LatLng(parseFloat(objmapsstandard[i].auto_last_lat), parseFloat(objmapsstandard[i].auto_last_long)),
                      title: objmapsstandard[i].vehicle_no,
                      id: objmapsstandard[i].vehicle_device
                    });
                    icon.rotation = Math.ceil(objmapsstandard[i].auto_last_course);
                    marker.setIcon(icon);
                    markers.push(marker);

                    var sisaliterbensin, numfuel, roundfuel;
                    if (objmapsstandard[i].vehicle_mv03 != 0000) {
                      var numfuel     = Number(objmapsstandard[i].fuel_liter_fix);
                      var roundfuel   = numfuel.toFixed(2);
                      sisaliterbensin = Number(roundfuel);
                    }else {
                      sisaliterbensin = "";
                    }

                    var addresssplit = objmapsstandard[i].auto_last_position.split(" ");
                    var inarea       = objmapsstandard[i].auto_last_position.split(",");
                    var addressfix   = bibarea.includes(addresssplit[0]);
                    if (addressfix) {
                      var addressfix = inarea[0];
                    }else {
                      var addressfix = objmapsstandard[i].auto_last_position;
                    }

                    var string = objmapsstandard[i].vehicle_no + ' - ' + objmapsstandard[i].vehicle_name + "<br>" +
                      "GPS Time : " + objmapsstandard[i].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + objmapsstandard[i].auto_last_road + "<br>"+
                      "Coord : " + objmapsstandard[i].auto_last_lat + ", " + objmapsstandard[i].auto_last_long + "<br>" +
                      "Engine : " + objmapsstandard[i].auto_last_engine + "<br>" +
                      "Fuel : " + sisaliterbensin + " Ltr<br>" +
                      // "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
                      "Speed : " + rounded + " kph" + "</br>" +
                      "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";

                     infowindowkedua = new google.maps.InfoWindow({
                      content: string,
                      maxWidth: 300
                    });

                    infowindowkedua.open(map, marker);
                    map.setCenter(center);
                    marker.setPosition(center);

                    google.maps.event.addListener(marker, 'click', function(evt){
                      infowindowkedua.close();
                      infowindow.close();

                      var sisaliterbensin, numfuel, roundfuel;
                      if (objmapsstandard[i].vehicle_mv03 != 0000) {
                        var numfuel     = Number(objmapsstandard[i].fuel_liter_fix);
                        var roundfuel   = numfuel.toFixed(2);
                        sisaliterbensin = Number(roundfuel);
                      }else {
                        sisaliterbensin = "";
                      }

                      var num         = Number(objmapsstandard[i].auto_last_speed);
                      var roundstring = num.toFixed(0);
                      var rounded     = Number(roundstring);

                      var addresssplit = objmapsstandard[i].auto_last_position.split(" ");
                      var inarea       = objmapsstandard[i].auto_last_position.split(",");
                      var addressfix   = bibarea.includes(addresssplit[0]);
                      if (addressfix) {
                        var addressfix = inarea[0];
                      }else {
                        var addressfix = objmapsstandard[i].auto_last_position;
                      }

                      var string = objmapsstandard[i].vehicle_no + ' - ' + objmapsstandard[i].vehicle_name + "<br>" +
                        "GPS Time : " + objmapsstandard[i].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + objmapsstandard[i].auto_last_road + "<br>"+
                        "Coord : " + objmapsstandard[i].auto_last_lat + ", " + objmapsstandard[i].auto_last_long + "<br>" +
                        "Engine : " + objmapsstandard[i].auto_last_engine + "<br>" +
                        "Fuel : " + sisaliterbensin + " Ltr<br>" +
                        // "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
                        "Speed : " + rounded + " kph" + "</br>" +
                        "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";

                       infowindowkedua = new google.maps.InfoWindow({
                        content: string,
                        maxWidth: 300
                      });
                      // DeleteMarkers(response[0].vehicle_device);
                      // DeleteMarkerspertama(response[0].vehicle_device);

                        var center = {lat : parseFloat(objmapsstandard[i].auto_last_lat), lng: parseFloat(objmapsstandard[i].auto_last_long)};
                        infowindowkedua.setContent(string);
                        map.setCenter(marker.position);
                        marker.setPosition(marker.position);
                        infowindowkedua.open(map, this);
                    });
                };
              })(marker, i));
            }
          }
          $("#result").show();
        }

    }, "json");
    return false;
  }

  function showTablePool(){
    $("#btnpoolback").hide();
    $("#cardShowMap").hide();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#tableShowMuatan").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowRom").hide();
    $("#tableShowPort").hide();
    $("#tableShowOutOfHauling").hide();
    $("#tableShowVehicleOffline").hide();
    $("#showSearchNopol").show();
    $("#showSearchNOutOfHauling").hide();
    $("#tableShowPool").hide();
    $("#tableShowPoolNew").show();
    soundisactive = 0;
    var companyid = $("#contractor").val();

      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>historical/searchhistorical", $("#frmsearch").serialize(), function(response){
        console.log("response getpoolnew : ", response);
        jQuery("#loader2").hide();

        $("#contentpoolnew").html("");
        var datainpool        = Object.entries(response.data);
        var totaldata         = datainpool.length;
        var allcompany        = response.allcompany;
        var jumlahtotalinpool = 0;

        var htmlpool          = "";
        // console.log("totaldata : ", totaldata);

        for (var i = 0; i < totaldata; i++) {
          jumlahtotalinpool += datainpool[i][1];
          htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="getPoolByParent('+allcompany[i].company_id+')" style="cursor : pointer;">';
            htmlpool += '<div class="info-box bg-blue">';
              htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
              htmlpool += '</span>';
              htmlpool += '<div class="info-box-content">';
                htmlpool += '<span class="info-box-text" style="font-size:15px;">POOL '+datainpool[i][0]+'</span>';
                htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+datainpool[i][1]+'</b></span>';
              htmlpool += '</div>';
            htmlpool += '</div>';
          htmlpool += '</div>';
        }

        htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="getPoolOther()" style="cursor : pointer;">';
          htmlpool += '<div class="info-box bg-blue">';
            htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
              htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
            htmlpool += '</span>';
            htmlpool += '<div class="info-box-content">';
              htmlpool += '<span class="info-box-text" style="font-size:15px;">Others</span>';
              htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+response.dataoutofhauling.outofhauling+'</b></span>';
            htmlpool += '</div>';
          htmlpool += '</div>';
        htmlpool += '</div>';

        $("#contentpoolnew").html(htmlpool);
        $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool);
        $("#jumlahtotalinpoolnew").show();
      },"json");
  }

  function getPoolByParent(id){
    jQuery("#loader2").show();
    $("#poolparentID").val(id);
    var startdate = $("#startdate").val();
    var starttime = $("#starttime").val();
    var contractor = $("#contractor").val();
    $.post("<?php echo base_url() ?>historical/getChildPool", {
      poolparent : id,
      startdate : startdate,
      starttime : starttime,
      contractor : contractor
    }, function(response){
      console.log("response getPoolByParent : ", response);
      jQuery("#loader2").hide();

      $("#contentpoolnew").html("");
      var datainpool        = Object.entries(response.data);
      var totaldata         = datainpool.length;
      var allStreet         = response.allStreet;
      var jumlahtotalinpool = 0;

      var htmlpool          = "";
      // console.log("totaldata : ", totaldata);

      for (var i = 0; i < totaldata; i++) {
        jumlahtotalinpool += datainpool[i][1];
        htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="getVehicleByPool('+allStreet[i].street_id+')" style="cursor : pointer;">';
          htmlpool += '<div class="info-box bg-blue">';
            htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
              htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
            htmlpool += '</span>';
            htmlpool += '<div class="info-box-content">';
              htmlpool += '<span class="info-box-text" style="font-size:15px;">'+datainpool[i][0]+'</span>';
              htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+datainpool[i][1]+'</b></span>';
            htmlpool += '</div>';
          htmlpool += '</div>';
        htmlpool += '</div>';
      }

      $("#btnpoolback").show();

      $("#contentpoolnew").html(htmlpool);
      $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool);
      $("#jumlahtotalinpoolnew").show();
    }, "json");
  }

  function getPoolOther(){
    var companyid = $("#contractor").val();
    var startdate = $("#startdate").val();
    var starttime = $("#starttime").val();
    jQuery("#loader2").show();
    $.post("<?php echo base_url() ?>historical/getPoolOther", {
      companyid : companyid,
      startdate : startdate,
      starttime : starttime
    }, function(response){
      jQuery("#loader2").hide();

      console.log("response : ", response);
      var datainpool         = Object.entries(response.data);
      var totaldataothers    = datainpool.length;
      var datacompany        = response.company;
      var jumlahtotalinpool  = 0;

      var htmlpool = "";
      for (var i = 0; i < totaldataothers; i++) {
        jumlahtotalinpool += datainpool[i][1];
        htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="listOutOfHaulingByContractor('+datacompany[i].company_id+')" style="cursor : pointer;">';
          htmlpool += '<div class="info-box bg-blue">';
            htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
              htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
            htmlpool += '</span>';
            htmlpool += '<div class="info-box-content">';
              htmlpool += '<span class="info-box-text" style="font-size:15px;">'+datainpool[i][0]+'</span>';
              htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+datainpool[i][1]+'</b></span>';
            htmlpool += '</div>';
          htmlpool += '</div>';
        htmlpool += '</div>';
      }

      $("#btnpoolback").show();

      $("#contentpoolnew").html(htmlpool);
      $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool);
      $("#jumlahtotalinpoolnew").show();

    }, "json");
  }

  function listOutOfHaulingByContractor(id){
    var startdate = $("#startdate").val();
    var starttime = $("#starttime").val();

    $.post("<?php echo base_url() ?>historical/getlistoutofhaulingByContractor", {
      contractor : id,
      startdate : startdate,
      starttime : starttime
    }, function(response){
      console.log("response Pool Others : ", response);
      var datafix           = response.data;
      var totalrow          = datafix.length;
      var datacontractor    = response.jumlah_contractor;
      var datacontractorfix = Object.entries(datacontractor);

      var htmlcontractor = "";
      htmlcontractor += '<table>';
        htmlcontractor += '<tr>';
        var looplist = 0;
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              looplist += 1;
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);

                if (looplist > 1) {
                  htmlcontractor += '<td><b>|&nbsp;'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;|&nbsp;</b><td>';
                }else {
                  htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;</b><td>';
                }
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlpool = "";
        if (datafix.length > 0) {
          var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html("Others "+  " (" + totalrow + ")");
          $("#lastcheckpoolws").html(lastcheckpoolws);
          htmlpool += '<table class="table table-striped">';
            htmlpool += '<thead>';
              htmlpool += '<tr>';
                htmlpool += '<th>No</th>';
                htmlpool += '<th>Vehicle</th>';
                htmlpool += '<th align="center">Engine</th>';
                htmlpool += '<th align="center">Speed (Kph)</th>';
                htmlpool += '<th>Position</th>';
                htmlpool += '<th>Coord</th>';
              htmlpool += '</tr>';
            htmlpool += '</thead>';
            for (var i = 0; i < datafix.length; i++) {
              var enginefix = datafix[i].auto_last_engine;
              var speedfix = "";
                if (enginefix == "OFF") {
                  speedfix = 0;
                }else {
                  speedfix = datafix[i].auto_last_speed;
                }
                htmlpool += '<tr>';
                  htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                  htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                  htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                  htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedfix+'</span>';
                  htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_positionfix+'</span>';
                  htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;"><a href="https://maps.google.com/?q='+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'" target="_blank">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</a></span>';
                htmlpool += '</tr>';
            }
          htmlpool += '</table>';
          $("#modalStateContent").html(htmlpool);
          modalPoolFromMasterData('modalState');
        }else {
          alert("Data Tidak Ada");
        }
      },"json");
  }

  function getVehicleByPool(id){
    $("#idpoolfix").val(id);
    $.post("<?php echo base_url() ?>historical/vehicleonpool", $("#frmsearch").serialize(), function(response){
      console.log("response By Pool : ", response);
      var datafix           = response.data;
      var totalrow          = datafix.length;
      var datacontractor    = response.jumlah_contractor;
      var datacontractorfix = Object.entries(datacontractor);

      var htmlcontractor = "";
      htmlcontractor += '<table>';
        htmlcontractor += '<tr>';
        var looplist = 0;
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              looplist += 1;
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);

                if (looplist > 1) {
                  htmlcontractor += '<td><b>|&nbsp;'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;|&nbsp;</b><td>';
                }else {
                  htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;</b><td>';
                }
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlpool = "";
        if (datafix.length > 0) {
          // var lastcheckpoolws = "Last Check : "+response.lastcheck + " WITA";
          $("#modalStateTitle").html(response.statesent + " (" + totalrow + ")");
          // $("#lastcheckpoolws").html(lastcheckpoolws);
          htmlpool += '<table class="table table-striped">';
            htmlpool += '<thead>';
              htmlpool += '<tr>';
              htmlpool += '<th>No</th>';
                htmlpool += '<th>Vehicle</th>';
                htmlpool += '<th align="center">Engine</th>';
                htmlpool += '<th align="center">Speed (Kph)</th>';
                htmlpool += '<th>Coord</th>';
                htmlpool += '<th>GPS Time</th>';
              htmlpool += '</tr>';
            htmlpool += '</thead>';
          for (var i = 0; i < datafix.length; i++) {
              htmlpool += '<tr>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_update+'</span>';
              htmlpool += '</tr>';
          }
          htmlpool += '</table>';
          $("#modalStateContent").html(htmlpool);
          modalPoolFromMasterData('modalState');
        }else {
          alert("Data Tidak Ada");
        }

    }, "json");
  }

  function listOutOfHauling(){
    $.post("<?php echo base_url() ?>historical/getlistoutofhauling", $("#frmsearch").serialize(), function(response){
      console.log("response By List Out Of Hauling : ", response);
      var datafix           = response.data;
      var totalrow          = datafix.length;
      var datacontractor    = response.jumlah_contractor;
      var datacontractorfix = Object.entries(datacontractor);

      var htmlcontractor = "";
      htmlcontractor += '<table>';
        htmlcontractor += '<tr>';
        var looplist = 0;
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              looplist += 1;
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);

                if (looplist > 1) {
                  htmlcontractor += '<td><b>|&nbsp;'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;|&nbsp;</b><td>';
                }else {
                  htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;</b><td>';
                }
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlpool = "";
        if (datafix.length > 0) {
          // var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html("Out Of Hauling" + " (" + totalrow + ")");
          // $("#lastcheckpoolws").html(lastcheckpoolws);
          htmlpool += '<table class="table table-striped">';
            htmlpool += '<thead>';
              htmlpool += '<tr>';
                htmlpool += '<th>No</th>';
                htmlpool += '<th>Vehicle</th>';
                htmlpool += '<th align="center">Engine</th>';
                htmlpool += '<th align="center">Speed (Kph)</th>';
                htmlpool += '<th>Position</th>';
                htmlpool += '<th>Coord</th>';
                htmlpool += '<th>GPS Time</th>';
              htmlpool += '</tr>';
            htmlpool += '</thead>';
          for (var i = 0; i < datafix.length; i++) {
            var enginefix = datafix[i].auto_last_engine;
            var speedfix = "";
              if (enginefix == "OFF") {
                speedfix = 0;
              }else {
                speedfix = datafix[i].auto_last_speed;
              }
              htmlpool += '<tr>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedfix+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_positionfix+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;"><a href="https://maps.google.com/?q='+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'" target="_blank">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</a></span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_update+'</span>';
              htmlpool += '</tr>';
          }
          htmlpool += '</table>';
          $("#modalStateContent").html(htmlpool);
          modalPoolFromMasterData('modalState');
        }else {
          alert("Data Tidak Ada");
        }
      },"json");
  }

  function closeWindowOnMarkerOOH(){
    $("#nopolforcheck").val("0");
    $("#nopolforhide").val("0");
    infowindowkedua.close();
  }

  function showHeatmap(data){
    $("#mapshowfix").removeClass();
    $("#mapshowfix").addClass('col-md-12');
    $("#mapsnya").removeClass();
    $("#mapsnya").addClass('mapsClass1');
    $("#cardShowMap").show();
    $("#mapShow").show();
    $("#mapshowfix").show();
    $("#realtimealertshowhide").show();
    $("#summarymapsshowhide").show();
    $("#tableShowMuatan").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").hide();
    $("#tableShowPool").hide();
    $("#tableShowPoolNew").hide();
    $("#tableShowOutOfHauling").hide();
    $("#tableShowVehicleOffline").hide();
    $("#showSearchNopol").show();
    $("#showSearchNOutOfHauling").hide();

    var valuemode = $("#valueMode").val();

    if (valuemode == 1) {
      console.log("valuemode : ", valuemode);
      heatmap.setMap(null);
    }

      // SHOW MAPS AFTER
      var vehicle           = data;
      var bounds            = new google.maps.LatLngBounds();
      var boundspool        = new google.maps.LatLngBounds();
      var arraypointheatmap = [];

      console.log("showHeatmap : ", vehicle);

      for (var j = 0; j < vehicle.length; j++) {
        // console.log("heatmap : " + j);
        DeleteMarkerspertama(vehicle[j].location_report_vehicle_device);
        DeleteMarkers(vehicle[j].location_report_vehicle_device);
        DeleteMarkerspertama(vehicle[j].location_report_vehicle_id);
        DeleteMarkers(vehicle[j].location_report_vehicle_id);
      }

      for (var i = 0; i < vehicle.length; i++) {
        arraypointheatmap.push(new google.maps.LatLng(vehicle[i].location_report_latitude, vehicle[i].location_report_longitude));
      }

      var gradientdefault = ["rgba(102, 255, 0, 0)",
                            "rgba(102, 255, 0, 1)",
                            "rgba(147, 255, 0, 1)",
                            "rgba(193, 255, 0, 1)",
                            "rgba(238, 255, 0, 1)",
                            "rgba(244, 227, 0, 1)",
                            "rgba(249, 198, 0, 1)",
                            "rgba(255, 170, 0, 1)",
                            "rgba(255, 113, 0, 1)",
                            "rgba(255, 57, 0, 1)",
                            "rgba(255, 0, 0, 1)"];

    heatmap = new google.maps.visualization.HeatmapLayer({
      data: arraypointheatmap,
      opacity: 50,
      maxIntensity: 8
    });
    heatmap.setMap(map);
    $("#valueMode").val(1);
  }

  function DeleteMarkers(id) {
    for (var i = 0; i < markerss.length; i++) {
      if (markerss[i].id == id) {
        console.log("DeleteMarkers masuk : "+id);
        markerss[i].setMap(null);
        markerss.splice(i, 1);
        return;
      }
    }
  }

  function DeleteMarkerspertama(id) {
    for (var i = 0; i < markers.length; i++) {
      if (markers[i].id == id) {
        console.log("DeleteMarkerspertama masuk : "+id);
        markers[i].setMap(null);
        markers.splice(i, 1);
        return;
      }
    }
  }

  function listVehicleOnKm(idkm){
    $("#idkmfix").val(idkm);
    $.post("<?php echo base_url() ?>historical/getlistinkm", $("#frmsearch").serialize(), function(response){
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
        // var lastcheckKmListQuickCount = "Last Check : "+dataKosonganfix[0].auto_last_update + " WITA";
        $("#modalKmListQuickCountTitle").html(response.kmsent);
        // $("#lastcheckKmListQuickCount").html(lastcheckKmListQuickCount);
        htmlkosongan += '<table class="table table-striped">';
          htmlkosongan += '<thead>';
            htmlkosongan += '<tr>';
            htmlkosongan += '<th>No</th>';
              htmlkosongan += '<th>Vehicle</th>';
              htmlkosongan += '<th>Position</th>';
              htmlkosongan += '<th align="center">Engine</th>';
              htmlkosongan += '<th align="center">Speed (Kph)</th>';
              htmlkosongan += '<th>Coord</th>';
              htmlkosongan += '<th>GPS Time</th>';
            htmlkosongan += '</tr>';
          htmlkosongan += '</thead>';
        for (var i = 0; i < dataKosonganfix.length; i++) {
          var enginekmfix = dataKosonganfix[i].auto_last_engine;
          var speedkmfix = "";
            if (enginekmfix == "OFF") {
              speedkmfix = 0;
            }else {
              speedkmfix = dataKosonganfix[i].auto_last_speed;
            }

            htmlkosongan += '<tr>';
              htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].vehicle_no+ " " +dataKosonganfix[i].vehicle_name+'</span>';
              htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_positionfix+ '</span>';
              htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_engine+'</span>';
              htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedkmfix+'</span>';
              htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_lat+ "," +dataKosonganfix[i].auto_last_long+'</span>';
              htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_update+ '</span>';
            htmlkosongan += '</tr>';
        }
        htmlkosongan += '</table>';
        $("#modalStateContentKosongan").html(htmlkosongan);
      }

      if (dataMuatanfix.length == 0) {
        $("#modalStateContentMuatan").html("Tidak ada data");
      }else {
        // var lastcheckKmListQuickCount = "Last Check : "+dataMuatanfix[0].auto_last_update + " WITA";
        $("#modalKmListQuickCountTitle").html(response.kmsent);
        // $("#lastcheckKmListQuickCount").html(lastcheckKmListQuickCount);
        htmlmuatan += '<table class="table table-striped">';
          htmlmuatan += '<thead>';
            htmlmuatan += '<tr>';
            htmlmuatan += '<th>No</th>';
              htmlmuatan += '<th>Vehicle</th>';
              htmlmuatan += '<th>Position</th>';
              htmlmuatan += '<th align="center">Engine</th>';
              htmlmuatan += '<th align="center">Speed (Kph)</th>';
              htmlmuatan += '<th>Coord</th>';
              htmlmuatan += '<th>GPS Time</th>';
            htmlmuatan += '</tr>';
          htmlmuatan += '</thead>';
        for (var i = 0; i < dataMuatanfix.length; i++) {
          var enginekmfix = dataMuatanfix[i].auto_last_engine;
          var speedkmfix = "";
            if (enginekmfix == "OFF") {
              speedkmfix = 0;
            }else {
              speedkmfix = dataMuatanfix[i].auto_last_speed;
            }

            htmlmuatan += '<tr>';
              htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].vehicle_no+ " " +dataMuatanfix[i].vehicle_name+'</span>';
              htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_positionfix+'</span>';
              htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_engine+'</span>';
              htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedkmfix+'</span>';
              htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_lat+ "," +dataMuatanfix[i].auto_last_long+'</span>';
              htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_update+ '</span>';
            htmlmuatan += '</tr>';
        }
        htmlmuatan += '</table>';
        $("#modalStateContentMuatan").html(htmlmuatan);
      }

        modalKmFromMasterData('modalKmListQuickCount');

    }, "json");
  }

  function listVehicleOnRom(id){
    $("#idromfix").val(id);
    $.post("<?php echo base_url() ?>historical/getlistinrom", $("#frmsearch").serialize(), function(response){
      console.log("response By ROM LIST : ", response);
      var datafix           = response.data;
      var totalrow          = datafix.length;
      var datacontractor    = response.jumlah_contractor;
      var datacontractorfix = Object.entries(datacontractor);

      var htmlcontractor = "";
      htmlcontractor += '<table>';
        htmlcontractor += '<tr>';
        var looplist = 0;
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              looplist += 1;
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);

                if (looplist > 1) {
                  htmlcontractor += '<td><b>|&nbsp;'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;|&nbsp;</b><td>';
                }else {
                  htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;</b><td>';
                }
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlpool = "";
        if (datafix.length > 0) {
          // var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html(response.romsent + " (" + totalrow + ")");
          // $("#lastcheckpoolws").html(lastcheckpoolws);
          htmlpool += '<table class="table table-striped">';
            htmlpool += '<thead>';
              htmlpool += '<tr>';
                htmlpool += '<th>No</th>';
                htmlpool += '<th>Vehicle</th>';
                htmlpool += '<th align="center">Engine</th>';
                htmlpool += '<th align="center">Speed (Kph)</th>';
                htmlpool += '<th>Coord</th>';
                htmlpool += '<th>GPS Time</th>';
              htmlpool += '</tr>';
            htmlpool += '</thead>';
          for (var i = 0; i < datafix.length; i++) {
            var engineromfix = datafix[i].auto_last_engine;
            var speedromfix = "";
              if (engineromfix == "OFF") {
                speedromfix = 0;
              }else {
                speedromfix = datafix[i].auto_last_speed;
              }

              htmlpool += '<tr>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedromfix+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_update+'</span>';
              htmlpool += '</tr>';
          }
          htmlpool += '</table>';
          $("#modalStateContent").html(htmlpool);
          modalPoolFromMasterData('modalState');
        }else {
          alert("Data Tidak Ada");
        }
      },"json");
  }

  function listVehicleOnPort(id){
    var startdate   = $("#startdate").val();
    var starttime   = $("#starttime").val();
    var contractor  = $("#contractor").val();
    var mapsOptions = $("#mapsOptions").val();
    $.post("<?php echo base_url() ?>historical/getlistinport", {
      idportfix: id,
      startdate : startdate,
      starttime : starttime,
      contractor : contractor,
      mapsOptions : mapsOptions
    }, function(response){
      console.log("response By PORT LIST : ", response);
      var datafix = response.data;

      var totalrow          = datafix.length;
      var datacontractor    = response.jumlah_contractor;
      var datacontractorfix = Object.entries(datacontractor);

      var htmlcontractor = "";
      htmlcontractor += '<table>';
        htmlcontractor += '<tr>';
        var looplist = 0;
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              looplist += 1;
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);

                if (looplist > 1) {
                  htmlcontractor += '<td><b>|&nbsp;'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;|&nbsp;</b><td>';
                }else {
                  htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
                  htmlcontractor += '<td>&nbsp;:&nbsp;<td>';
                  htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'&nbsp;</b><td>';
                }
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlpool = "";
        if (datafix.length > 0) {
          // var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html(response.portsent + " (" + totalrow + ")");
          // $("#lastcheckpoolws").html(lastcheckpoolws);
          htmlpool += '<table class="table table-striped">';
            htmlpool += '<thead>';
              htmlpool += '<tr>';
                htmlpool += '<th>No</th>';
                htmlpool += '<th>Vehicle</th>';
                htmlpool += '<th align="center">Engine</th>';
                htmlpool += '<th align="center">Speed (Kph)</th>';
                htmlpool += '<th>Coord</th>';
                htmlpool += '<th>GPS Time</th>';
              htmlpool += '</tr>';
            htmlpool += '</thead>';
          for (var i = 0; i < datafix.length; i++) {
            var engineportfix = datafix[i].auto_last_engine;
            var speedportfix = "";
              if (engineportfix == "OFF") {
                speedportfix = 0;
              }else {
                speedportfix = datafix[i].auto_last_speed;
              }

              htmlpool += '<tr>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedportfix+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_update+'</span>';
              htmlpool += '</tr>';
          }
          htmlpool += '</table>';
          $("#modalStateContent").html(htmlpool);
          modalPoolFromMasterData('modalState');
        }else {
          alert("Data Tidak Ada");
        }
      },"json");
  }



















</script>

<?php
$key = $this->config->item("GOOGLE_MAP_API_KEY");
//$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";
// echo "key nya : ". $key;

if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&libraries=visualization&callback=initMap" type="text/javascript" async></script>
  <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>
