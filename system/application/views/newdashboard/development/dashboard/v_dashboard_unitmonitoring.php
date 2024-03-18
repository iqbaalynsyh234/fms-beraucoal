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

#header-realtime-alert{
  background-color: #221f1f;
  color: white;
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
  height: 300px;
  margin-top: -28%;
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

<script type="text/javascript">
  function saveMapSetting(){
    jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>maps/mapSetting", jQuery("#frmadd").serialize(),
      function(r)
      {
        jQuery("#loader2").hide();
        console.log("response : ", r);
          if (r.msg == "success") {
            if (confirm("Map Setting Successfully Updated")) {
              window.location = '<?php echo base_url() ?>maps/heatmap2';
            }
          }else {
            if (confirm("Map Setting Failed Updated")) {
              window.location = '<?php echo base_url() ?>maps/heatmap2';
            }
          }
      }, "json");
      return false;
  }
</script>

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
              <h5>Dashboard Unit Monitoring - DEVELOPMENT</h5>
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
                <select class="form-control select2" name="contractor" id="contractor" onchange="getVehicleByContractor()">
                </select>
              </div>

              <div class="col-md-3" id="showSearchNopol">
                <select class="form-control select2" name="searchnopol" id="searchnopol" onchange="forsearchinput()">
                </select>
                <input type="text" id="nopolforcheck" value="0" hidden>
                <input type="text" id="nopolforhide" value="0" hidden>
              </div>

              <div class="col-md-3" id="showSearchNOutOfHauling" style="display:none;">
                <select class="form-control select2" name="forsearchoutofhauling" id="forsearchoutofhauling" onchange="forsearchoutofhauling()" style="width:100%;">
                </select>
              </div>

              <div class="col-md-3">
                <select class="form-control select2" name="mapsOptions" id="mapsOptions" onchange="mapsOptions()">
                  <option value="0">Site Option 1</option>
                  <option value="0">Site Option 2</option>
                  <option value="0">Site Option 3</option>
                  <!-- <option value="showHeatmap">Maps</option>
                  <option value="showTableMuatan1">Quick Count Data</option> -->
                  <!-- <option value="showTableMuatan2">Consolidated Data</option> -->
                  <!-- <option value="showTableRom">ROM</option>
                  <option value="showTablePort">PORT</option>
                  <option value="showTablePool">POOL</option>
                  <option value="outofhauling">Out Of Hauling</option>
                  <option value="offlinevehicle">Offline Vehicle</option>
                  <option value="standardmaps">Maps Standard</option> -->
                </select>
              </div>

              <!-- <div class="col-md-1" id="informationQC" style="display:none;">
                <div class="btn btn-round btn-md btn-default"  onclick="informationquickcount();">
                  <span class="fa fa-info"></span>
                </div>
              </div> -->

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
            <div class="col-md-8">
              <div class="col-md-12">
                 <div class="panel">
                 <div class="panel-heading" id="header-realtime-alert">Based On Unit</div>
                 <div class="panel-body">
                   <div class="row text-center">
                     <div class="col-md-4">
                       <label for="btnonline">Online</label><br>
                       <button type="button" class="btn btn-flat" id="btnonline" style="font-size:16px;">15</button>
                     </div>

                     <div class="col-md-4">
                       <label for="btnwithoutalert">No Alert</label><br>
                       <button type="button" class="btn btn-success" id="btnwithoutalert" style="font-size:16px;">10</button>
                     </div>

                     <div class="col-md-4">
                       <label for="btnwithalert">With Alert</label><br>
                       <button type="button" class="btn btn-danger" id="btnwithalert" style="font-size:16px;">5</button>
                     </div>
                   </div>
                   <div class="row">
                     <div class="col-md-12">
                       <br>
                       <i>Offline : 18 Unit</i>
                     </div>
                   </div>
                 </div>
               </div>
            </div>
          </div>

            <div class="col-md-4" id="realtimealertshowhide">
               <!-- style="display:none;" -->
              <div class="panel" id="panel_form">
                <header class="panel-heading" id="header-realtime-alert">
                  Realtime Alert
                  Show :
                  <select class="select" name="changelimitrealtimalert" id="changelimitrealtimalert" onchange="changelimit();">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                  </select>

                  <button type="button" name="button" id="activatesound" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound();" style="display:none;">
                    <span class="fa fa-volume-up"></span>
                  </button>

                  <button type="button" name="button" id="activatesound2" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound2();">
                    <span class="fa fa-volume-off"></span>
                  </button>
                </header>
                <div class="panel-body" id="bar-parent10">
                  <div id="realtimealertcontent"></div>
                    <table class="table">
                      <!-- <div id="newalertcontent" style="font-size:10px; margin-top: 2%; background: yellow; display:none;"></div> -->
                      <div id="summaryalertcontent" style="font-size:10px; margin-top: 1%; overflow-x: auto; height: 270px; max-height: 270px;"></div>
                    </table>
                </div>
                <div class="panel-footer">
                  <div class="row">
                    <div class="col-md-12">
                      <a href="<?php echo base_url() ?>overspeedreport" type="button" class="btn btn-flat form-control">View More</a>
                    </div>
                  </div>
                </div>
            </div>
          </div>

          <div id="mapshowfix">
            <input type="hidden" id="valueMode" value="0">
              <div id="mapShow">
                 <!-- display:none; -->
                 <div id="mapsnya" class="mapsClass1"></div>
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

<script>
  $(document).ready(function() {
    setTimeout(function(){
      appendthevehiclelist();
      appendthecontractorlist();
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

    function appendthevehiclelist(){
      var privilegecode = '<?php echo $this->sess->user_id_role; ?>';
      var user_id       = '<?php echo $this->sess->user_id; ?>';
      var user_company  = '<?php echo $this->sess->user_company; ?>';
      var vehicle_data  = '<?php echo $vehicles; ?>';
      var html = "";
      console.log("vehicle_data : ", vehicle_data);

      if (privilegecode == 5 || privilegecode == 6) {
        html += '<option value="0">--Vehicle List</option>';
        html += '<?php for ($i=0; $i < sizeof($vehicles); $i++) {?>';
          var vCompany           = '<?php echo $vehicles[$i]['vehicle_company']; ?>';
          var vehicle_is_share   = '<?php echo $vehicles[$i]['vehicle_is_share']; ?>';
          var vehicle_id_shareto = '<?php echo $vehicles[$i]['vehicle_id_shareto']; ?>';
          if (vCompany == user_company || vehicle_id_shareto == user_company && vehicle_is_share == 1) {
            html += '<option value="<?php echo $vehicles[$i]['vehicle_no'] ?>"><?php echo $vehicles[$i]['vehicle_no'] ?></option>';
          }
        html += '<?php } ?>';
      }else {
        html += '<option value="0">--Vehicle List</option>';
        html += '<?php for ($i=0; $i < sizeof($vehicles); $i++) {?>';
          html += '<option value="<?php echo $vehicles[$i]['vehicle_no'] ?>"><?php echo $vehicles[$i]['vehicle_no'] ?></option>';
        html += '<?php } ?>';
      }

        $("#searchnopol").html(html);
    }
  });

  function changevehiclelist(){
    // console.log("masuk gan");
    var companyid = $("#contractor").val();
    $.post("<?php echo base_url() ?>maps/getvehiclebycontractor", {companyid : companyid}, function(response){
      // console.log("response : ", response);
      var data = response.data;
      var html = "";

          html += '<option value="0">--Vehicle List</option>';
          for (var i = 0; i < data.length; i++) {
              if (companyid == 0) {
                html += '<option value="'+data[i].vehicle_no+'">'+data[i].vehicle_no+'</option>';
              }else {
                html += '<option value="'+data[i].vehicle_no+'">'+(i+1) + ". " + data[i].vehicle_no+'</option>';
              }
          }
        $("#searchnopol").html(html);
    },"json");
  }

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
  // var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

  var car = "M 2 2 C 2 1 3 0 5 0 H 19 C 21 0 22 1 22 2 V 17 H 2 Z M 3 2 C 3.6667 2.6667 4.3333 3.3333 5 4 H 19 C 19.6667 3.3333 20.3333 2.6667 21 2 C 21 1 20.3333 1.3333 20 1 H 4 V 1 C 3.6667 1.3333 3 1 3 2 M 19 5 V 13 C 19.6667 13.3333 20.3333 13.6667 21 14 V 4 Z M 5 5 H 5 C 4.3333 4.6667 3.6667 4.3333 3 4 V 14 C 3.6667 13.6667 4.3333 13.3333 5 13 Z M 6 16 H 18 V 15 H 6 Z M 7 8 V 13 V 13 H 8 V 8 Z M 10 8 V 13 H 11 V 8 M 17 8 H 16 V 13 H 17 Z M 13 8 V 13 V 13 V 13 H 14 V 8 Z M 0 4 C 0 4 0 3 1 3 H 2 V 4 Z M 22 4 V 3 V 3 H 23 C 24 3 24 4 24 4 H 24 Z M -1 19 H 3 V 18 H 4 V 17 H 20 V 18 H 21 H 21 V 19 H 25 V 61 H -1 Z Z M 1 21 V 54 C 1.6667 43.6667 2.3333 33.3333 2 23 H 22 C 21.6667 33.3333 22.3333 43.6667 23 54 V 21 V 21 Z Z M 5 27 V 53 H 6 V 27 Z M 19 27 H 18 V 53 V 53 H 19 Z M 15 27 H 14 V 53 V 53 V 53 H 15 Z M 9 27 V 53 H 10 V 27 Z";

  var middle_limit = '<?php echo $mapsetting[0]['mapsetting_middle_limit'] ?>';
  var top_limit    = '<?php echo $mapsetting[0]['mapsetting_top_limit'] ?>';

  function initMap() {
    var vehicle           = '<?php echo json_encode($vehicledata); ?>';
    var poolmaster        = '<?php echo json_encode($poolmaster); ?>';

    var bounds            = new google.maps.LatLngBounds();
    var boundspool        = new google.maps.LatLngBounds();

    if (datafixnya == "") {
      try {
        var datacode  = JSON.parse(vehicle);
        objpoolmaster = JSON.parse(poolmaster);
        // console.log("disini objpoolmaster: ", objpoolmaster);
      } catch (e) {
        // console.log("e : ", e);
      }
    } else {
      var datacode  = vehicle;
      objpoolmaster = poolmaster;
    }

    obj              = datacode;
    objpoolmasterfix = objpoolmaster;
    console.log("obj : ", obj);

    map = new google.maps.Map(document.getElementById("mapsnya"), {
      zoom: 14,
      center: { lat: parseFloat(2.1193), lng: parseFloat(117.4631)},
      mapTypeId: "satellite",
      options: {
        gestureHandling: 'greedy'
      }
    });

    // for (var x = 0; x < objpoolmasterfix.length; x++) {
    //   // console.log("masuk looping", x);
    //   var positionpool = new google.maps.LatLng(parseFloat(objpoolmasterfix[x].poi_lat), parseFloat(objpoolmasterfix[x].poi_lng));
    //   boundspool.extend(positionpool);
    //   if (objpoolmasterfix[x].poi_image) {
    //     var iconpool = {
    //       url: objpoolmasterfix[x].poi_image, // url JIKA GIF
    //       // path: 'assets/images/iconpulsemarker.gif',
    //       // scale: .5,
    //       anchor: new google.maps.Point(25,10),
    //       scaledSize: new google.maps.Size(17,17)
    //     };
    //   }else {
    //     var iconpool = {
    //       url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
    //       // path: 'assets/images/iconpulsemarker.gif',
    //       // scale: .5,
    //       anchor: new google.maps.Point(25,10),
    //       scaledSize: new google.maps.Size(17,17)
    //     };
    //   }
    //
    //   markerpool = new google.maps.Marker({
    //     position: positionpool,
    //     map: map,
    //     icon: iconpool,
    //     title: objpoolmasterfix[x].poi_name,
    //     id: objpoolmasterfix[x].poi_name,
    //     optimized: false
    //   });
    //   markerpool.setIcon(iconpool);
    //   markerpools.push(markerpool);
    // }

    for (var i = 0; i < obj.length; i++) {
      arraypointheatmap.push(new google.maps.LatLng(obj[i].auto_last_lat, obj[i].auto_last_long));
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
    // gradient : gradientdefault
    // dissipating: true,
    // radius: 20,
    opacity: 50,
    maxIntensity: 8
    // data: dataposition,
  });
  heatmap.setMap(map);

  // TOOGEL BUTTON BIB MAP
    // var toggleButton = document.createElement("button");
    // toggleButton.textContent = "BIB Maps";
    // toggleButton.classList.add("custom-map-control-button");
    // map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

    //  toggleButton.addEventListener("click", () => {
    //   addoverlay(map);
    // });

    // var changegradient = document.createElement("button");
    // changegradient.textContent = "Change Gradient";
    // changegradient.classList.add("custom-map-control-button");
    // map.controls[google.maps.ControlPosition.TOP_CENTER].push(changegradient);
    //
    //  changegradient.addEventListener("click", () => {
    //   changeGradient();
    // });

    // intervalstart = setInterval(simultango, 15000);
}

  function simultango() {
    var mapsOptionsValue = $("#mapsOptions").val();
    var valuemapsoption;
      if ( mapsOptionsValue == "showHeatmap") {
        valuemapsoption = 1;
      }else if ( mapsOptionsValue == "showTableMuatan1") {
        valuemapsoption = 2;
      }else if ( mapsOptionsValue == "showTableRom") {
        valuemapsoption = 3;
      }else if ( mapsOptionsValue == "showTablePort") {
        valuemapsoption = 4;
      }else if ( mapsOptionsValue == "showTablePool") {
        valuemapsoption = 5;
      }else if ( mapsOptionsValue == "outofhauling") {
        valuemapsoption = 6;
      }else if ( mapsOptionsValue == "offlinevehicle") {
        valuemapsoption = 7;
      }else if ( mapsOptionsValue == "standardmaps") {
        valuemapsoption = 8;
      }else {
        valuemapsoption = 1;
      }

    var companyid     = $("#contractor").val();
    var nopolforcheck = $("#nopolforcheck").val();

    // console.log("nopolforcheck : ", nopolforcheck);
      // arraypointheatmap.setMap(null);
      jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>map/lastinfoall_new", {companyid : companyid, valuemapsoption : valuemapsoption},function(r) {
      jQuery("#loader2").hide();
        console.log("response simultango : ", r);
        heatmap.setMap(null);
        var arraypointheatmap2 = [];
        for (var i = 0; i < r.data.length; i++) {
          arraypointheatmap2.push(new google.maps.LatLng(r.data[i].gps_latitude_real, r.data[i].gps_longitude_real));
        }

      heatmap = new google.maps.visualization.HeatmapLayer({
        data: arraypointheatmap2,
        dissipating: true,
        // radius: 20,
        // opacity: 1,
        // data: dataposition,
        opacity: 50,
        maxIntensity: 8,
        map: map,
      });

      // UNTUK UPDATE INFOWINDOW JIKA ADA PENCARIAN NOPOL
      updateinfowindow(nopolforcheck);
      }, "json");
  }

  function changeGradient() {
    const gradient = [
      "rgba(0, 255, 255, 0)",
      "rgba(0, 255, 255, 1)",
      "rgba(0, 191, 255, 1)",
      "rgba(0, 127, 255, 1)",
      "rgba(0, 63, 255, 1)",
      "rgba(0, 0, 255, 1)",
      "rgba(0, 0, 223, 1)",
      "rgba(0, 0, 191, 1)",
      "rgba(0, 0, 159, 1)",
      "rgba(0, 0, 127, 1)",
      "rgba(63, 0, 91, 1)",
      "rgba(127, 0, 63, 1)",
      "rgba(191, 0, 31, 1)",
      "rgba(255, 0, 0, 1)",
    ];

    heatmap.set("gradient", heatmap.get("gradient") ? null : gradient);
    // heatmap.set("gradient", gradient);
  }

// Heatmap data: 500 Points
function getPoints() {
  return [
    new google.maps.LatLng(-3.7015, 115.5621),
    new google.maps.LatLng(-3.7030, 115.5643),
    new google.maps.LatLng(-3.7032, 115.5645),
    new google.maps.LatLng(-3.7034, 115.5647),
    new google.maps.LatLng(-3.7036, 115.5649),
    new google.maps.LatLng(-3.7038, 115.5651),
    new google.maps.LatLng(-3.7040, 115.5653),
    new google.maps.LatLng(-3.7042, 115.5655),
    new google.maps.LatLng(-3.7044, 115.5657),
    new google.maps.LatLng(-3.7046, 115.5659),
    new google.maps.LatLng(-3.7215, 115.6370),
    new google.maps.LatLng(-3.7217, 115.6373),
    new google.maps.LatLng(-3.7219, 115.6376),
    new google.maps.LatLng(-3.7221, 115.6379),
    new google.maps.LatLng(-3.7223, 115.6382),
    new google.maps.LatLng(-3.7225, 115.6385),
    new google.maps.LatLng(-3.7228, 115.6388),
    new google.maps.LatLng(-3.7267, 115.6454),
    new google.maps.LatLng(-3.7280, 115.6454),
    new google.maps.LatLng(-3.7290, 115.6454),
    new google.maps.LatLng(-3.7100, 115.6454),
    new google.maps.LatLng(-3.7110, 115.6454),
    new google.maps.LatLng(-3.7120, 115.6454),
    new google.maps.LatLng(-3.7130, 115.6454),
    new google.maps.LatLng(-3.6331, 115.6540),
    new google.maps.LatLng(-3.6332, 115.6540),
    new google.maps.LatLng(-3.6333, 115.6540),
    new google.maps.LatLng(-3.6334, 115.6540),
    new google.maps.LatLng(-3.6345, 115.6540),
    new google.maps.LatLng(-3.5955, 115.6550),
    new google.maps.LatLng(-3.5966, 115.6550),
    new google.maps.LatLng(-3.5977, 115.6550),
    new google.maps.LatLng(-3.5988, 115.6550),
    new google.maps.LatLng(-3.5999, 115.6550),
    new google.maps.LatLng(-3.6010, 115.6550),
    new google.maps.LatLng(-3.6020, 115.6550),
    new google.maps.LatLng(-3.6030, 115.6550),
    new google.maps.LatLng(-3.6040, 115.6550),
    new google.maps.LatLng(-3.7382, 115.6451),
    new google.maps.LatLng(-3.7387, 115.6451),
    new google.maps.LatLng(-3.7391, 115.6451),
    new google.maps.LatLng(-3.7396, 115.6451),
    new google.maps.LatLng(-3.7399, 115.6451),
    new google.maps.LatLng(-3.7402, 115.6451),
    new google.maps.LatLng(-3.7404, 115.6451),
    new google.maps.LatLng(-3.7406, 115.6451),
    new google.maps.LatLng(-3.7411, 115.6451),
    new google.maps.LatLng(-3.7414, 115.6451),
    new google.maps.LatLng(-3.7415, 115.6451),
    new google.maps.LatLng(-3.7417, 115.6450),
    new google.maps.LatLng(-3.7415, 115.6450),
    new google.maps.LatLng(-3.7417, 115.6452),
    new google.maps.LatLng(-3.7419, 115.6452),
    new google.maps.LatLng(-3.7421, 115.6452),
    new google.maps.LatLng(-3.7423, 115.6452),
    new google.maps.LatLng(-3.7426, 115.6452),
    new google.maps.LatLng(-3.7426, 115.6452),
    new google.maps.LatLng(-3.7430, 115.6452),
    new google.maps.LatLng(-3.7433, 115.6452),
    new google.maps.LatLng(-3.7435, 115.6452),
    new google.maps.LatLng(-3.7438, 115.6451),
    new google.maps.LatLng(-3.7438, 115.64521),
    new google.maps.LatLng(-3.7440, 115.6452),
    new google.maps.LatLng(-3.7442, 115.6452),
    new google.maps.LatLng(-3.7444, 115.6452),
    new google.maps.LatLng(-3.7447, 115.6452),
    new google.maps.LatLng(-3.7449, 115.6452),
    new google.maps.LatLng(-3.7449, 115.6454),
    new google.maps.LatLng(-3.7449, 115.6456),
    new google.maps.LatLng(-3.7448, 115.6458),
    new google.maps.LatLng(-3.7448, 115.6460),
    new google.maps.LatLng(-3.7444, 115.6463),
    new google.maps.LatLng(-3.7443, 115.6465),
    new google.maps.LatLng(-3.7442, 115.6466),
    new google.maps.LatLng(-3.7440, 115.6465),
    new google.maps.LatLng(-3.7438, 115.6465),
    new google.maps.LatLng(-3.7438, 115.6463),
    new google.maps.LatLng(-3.7438, 115.6462),
    new google.maps.LatLng(-3.7438, 115.6461),
    new google.maps.LatLng(-3.7437, 115.6460),
    new google.maps.LatLng(-3.7437, 115.6458),
    new google.maps.LatLng(-3.7437, 115.6458),
    new google.maps.LatLng(-3.7437, 115.6457),
    new google.maps.LatLng(-3.7436, 115.6456),
    new google.maps.LatLng(-3.7436, 115.6455),
    new google.maps.LatLng(-3.7436, 115.6454),
    new google.maps.LatLng(-3.7436, 115.6454),
  ];
}

function addoverlay(map){
  console.log("masuk overlay");
  if (overlaystatus == 0) {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'satellite') ? 'hidden' : google.maps.MapTypeId.satellite);

    overlaystatus = 1;
    // INI BUAT KOORDINAT PORTBUNATI3_SMALL.PNG

    // BIB MAPS UPDATE 18 12 2021
    var latlng_roma1_1     = new google.maps.LatLng(-3.524913, 115.634923);
    var latlng_roma1_2     = new google.maps.LatLng(-3.516356, 115.650076);
    var image_roma1        = "<?php echo base_url()?>assets/images/bibmaps/rom_a1.png";
    var image_latlng_roma1 = new google.maps.LatLngBounds(latlng_roma1_1, latlng_roma1_2);
    var overlay_roma1      = new google.maps.GroundOverlay(image_roma1, image_latlng_roma1);
    overlay_roma1.setMap(map);
    overlaysarray.push(overlay_roma1);

    var latlng_romb1_1      = new google.maps.LatLng(-3.605500, 115.620359);
    var latlng_romb1_2      = new google.maps.LatLng(-3.596698, 115.634840);
    var image_romb1         = "<?php echo base_url()?>assets/images/bibmaps/ROM-B1b.png";
    var image_latlng_romb1 = new google.maps.LatLngBounds(latlng_romb1_1, latlng_romb1_2);
    var overlay_romb1      = new google.maps.GroundOverlay(image_romb1, image_latlng_romb1);
    overlay_romb1.setMap(map);
    overlaysarray.push(overlay_romb1);

	var latlng_port1b_1      = new google.maps.LatLng(-3.751666, 115.643549);
    var latlng_port1b_2      = new google.maps.LatLng(-3.739291, 115.653206);
    var image_port1b         = "<?php echo base_url()?>assets/images/bibmaps/port_1b.png";
    var image_latlng_port1b = new google.maps.LatLngBounds(latlng_port1b_1, latlng_port1b_2);
    var overlay_port1b      = new google.maps.GroundOverlay(image_port1b, image_latlng_port1b);
    overlay_port1b.setMap(map);
    overlaysarray.push(overlay_port1b);

	var latlng_port2b_1      = new google.maps.LatLng(-3.751698, 115.633984);
    var latlng_port2b_2      = new google.maps.LatLng(-3.739304, 115.644362);
    var image_port2b         = "<?php echo base_url()?>assets/images/bibmaps/Port-2b.png";
    var image_latlng_port2b = new google.maps.LatLngBounds(latlng_port2b_1, latlng_port2b_2);
    var overlay_port2b      = new google.maps.GroundOverlay(image_port2b, image_latlng_port2b);
    overlay_port2b.setMap(map);
    overlaysarray.push(overlay_port2b);

	var latlng_port3b_1      = new google.maps.LatLng(-3.751607, 115.625526);
    var latlng_port3b_2      = new google.maps.LatLng(-3.739215, 115.634854);
    var image_port3b         = "<?php echo base_url()?>assets/images/bibmaps/Port-3b.png";
    var image_latlng_port3b = new google.maps.LatLngBounds(latlng_port3b_1, latlng_port3b_2);
    var overlay_port3b      = new google.maps.GroundOverlay(image_port3b, image_latlng_port3b);
    overlay_port3b.setMap(map);
    overlaysarray.push(overlay_port3b);

	var latlng_romb2_1      = new google.maps.LatLng(-3.575043, 115.626236);
    var latlng_romb2_2      = new google.maps.LatLng(-3.566604, 115.638872);
    var image_romb2         = "<?php echo base_url()?>assets/images/bibmaps/ROM-B2b.png";
    var image_latlng_romb2 = new google.maps.LatLngBounds(latlng_romb2_1, latlng_romb2_2);
    var overlay_romb2      = new google.maps.GroundOverlay(image_romb2, image_latlng_romb2);
    overlay_romb2.setMap(map);
    overlaysarray.push(overlay_romb2);

	var latlng_haulbaru12cb_1      = new google.maps.LatLng(-3.605164, 115.615232);
    var latlng_haulbaru12cb_2      = new google.maps.LatLng(-3.591731, 115.628002);
    var image_haulbaru12cb         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12cb.png";
    var image_latlng_haulbaru12cb = new google.maps.LatLngBounds(latlng_haulbaru12cb_1, latlng_haulbaru12cb_2);
    var overlay_haulbaru12cb      = new google.maps.GroundOverlay(image_haulbaru12cb, image_latlng_haulbaru12cb);
    overlay_haulbaru12cb.setMap(map);
    overlaysarray.push(overlay_haulbaru12cb);

	var latlng_haulbaru12bb_1      = new google.maps.LatLng(-3.606645, 115.626917);
    var latlng_haulbaru12bb_2      = new google.maps.LatLng(-3.593165, 115.636931);
    var image_haulbaru12bb         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12bb.png";
    var image_latlng_haulbaru12bb = new google.maps.LatLngBounds(latlng_haulbaru12bb_1, latlng_haulbaru12bb_2);
    var overlay_haulbaru12bb      = new google.maps.GroundOverlay(image_haulbaru12bb, image_latlng_haulbaru12bb);
    overlay_haulbaru12bb.setMap(map);
    overlaysarray.push(overlay_haulbaru12bb);

	var latlng_haulbaru1b_1      = new google.maps.LatLng(-3.740537, 115.644033);
    var latlng_haulbaru1b_2      = new google.maps.LatLng(-3.727921, 115.653558);
    var image_haulbaru1b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-1b.png";
    var image_latlng_haulbaru1b = new google.maps.LatLngBounds(latlng_haulbaru1b_1, latlng_haulbaru1b_2);
    var overlay_haulbaru1b      = new google.maps.GroundOverlay(image_haulbaru1b, image_latlng_haulbaru1b);
    overlay_haulbaru1b.setMap(map);
    overlaysarray.push(overlay_haulbaru1b);

	var latlng_haulbaru2b_1      = new google.maps.LatLng(-3.728990, 115.642171);
    var latlng_haulbaru2b_2      = new google.maps.LatLng(-3.716184, 115.651708);
    var image_haulbaru2b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-2b.png";
    var image_latlng_haulbaru2b = new google.maps.LatLngBounds(latlng_haulbaru2b_1, latlng_haulbaru2b_2);
    var overlay_haulbaru2b      = new google.maps.GroundOverlay(image_haulbaru2b, image_latlng_haulbaru2b);
    overlay_haulbaru2b.setMap(map);
    overlaysarray.push(overlay_haulbaru2b);

	var latlng_haulbaru3b_1      = new google.maps.LatLng(-3.717038, 115.638428);
    var latlng_haulbaru3b_2      = new google.maps.LatLng(-3.704264, 115.648521);
    var image_haulbaru3b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-3b.png";
    var image_latlng_haulbaru3b = new google.maps.LatLngBounds(latlng_haulbaru3b_1, latlng_haulbaru3b_2);
    var overlay_haulbaru3b      = new google.maps.GroundOverlay(image_haulbaru3b, image_latlng_haulbaru3b);
    overlay_haulbaru3b.setMap(map);
    overlaysarray.push(overlay_haulbaru3b);

	var latlng_haulbaru4b_1      = new google.maps.LatLng(-3.705034, 115.638673);
    var latlng_haulbaru4b_2      = new google.maps.LatLng(-3.692296, 115.647988);
    var image_haulbaru4b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-4b.png";
    var image_latlng_haulbaru4b = new google.maps.LatLngBounds(latlng_haulbaru4b_1, latlng_haulbaru4b_2);
    var overlay_haulbaru4b      = new google.maps.GroundOverlay(image_haulbaru4b, image_latlng_haulbaru4b);
    overlay_haulbaru4b.setMap(map);
    overlaysarray.push(overlay_haulbaru4b);

	var latlng_haulbaru5b_1      = new google.maps.LatLng(-3.693190, 115.640424);
    var latlng_haulbaru5b_2      = new google.maps.LatLng(-3.680628, 115.650006);
    var image_haulbaru5b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-5b.png";
    var image_latlng_haulbaru5b = new google.maps.LatLngBounds(latlng_haulbaru5b_1, latlng_haulbaru5b_2);
    var overlay_haulbaru5b      = new google.maps.GroundOverlay(image_haulbaru5b, image_latlng_haulbaru5b);
    overlay_haulbaru5b.setMap(map);
    overlaysarray.push(overlay_haulbaru5b);

	var latlng_haulbaru6b_1      = new google.maps.LatLng(-3.681356, 115.644169);
    var latlng_haulbaru6b_2      = new google.maps.LatLng(-3.668517, 115.653257);
    var image_haulbaru6b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-6b.png";
    var image_latlng_haulbaru6b = new google.maps.LatLngBounds(latlng_haulbaru6b_1, latlng_haulbaru6b_2);
    var overlay_haulbaru6b      = new google.maps.GroundOverlay(image_haulbaru6b, image_latlng_haulbaru6b);
    overlay_haulbaru6b.setMap(map);
    overlaysarray.push(overlay_haulbaru6b);

	var latlng_haulbaru7b_1      = new google.maps.LatLng(-3.669606, 115.641915);
    var latlng_haulbaru7b_2      = new google.maps.LatLng(-3.656844, 115.651896);
    var image_haulbaru7b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-7b.png";
    var image_latlng_haulbaru7b = new google.maps.LatLngBounds(latlng_haulbaru7b_1, latlng_haulbaru7b_2);
    var overlay_haulbaru7b      = new google.maps.GroundOverlay(image_haulbaru7b, image_latlng_haulbaru7b);
    overlay_haulbaru7b.setMap(map);
    overlaysarray.push(overlay_haulbaru7b);

	var latlng_haulbaru8b_1      = new google.maps.LatLng(-3.657590, 115.641814);
    var latlng_haulbaru8b_2      = new google.maps.LatLng(-3.644907, 115.651758);
    var image_haulbaru8b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-8b.png";
    var image_latlng_haulbaru8b = new google.maps.LatLngBounds(latlng_haulbaru8b_1, latlng_haulbaru8b_2);
    var overlay_haulbaru8b      = new google.maps.GroundOverlay(image_haulbaru8b, image_latlng_haulbaru8b);
    overlay_haulbaru8b.setMap(map);
    overlaysarray.push(overlay_haulbaru8b);

	var latlng_haulbaru9b_1      = new google.maps.LatLng(-3.646049, 115.641726);
    var latlng_haulbaru9b_2      = new google.maps.LatLng(-3.632953, 115.651698);
    var image_haulbaru9b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-9b.png";
    var image_latlng_haulbaru9b = new google.maps.LatLngBounds(latlng_haulbaru9b_1, latlng_haulbaru9b_2);
    var overlay_haulbaru9b      = new google.maps.GroundOverlay(image_haulbaru9b, image_latlng_haulbaru9b);
    overlay_haulbaru9b.setMap(map);
    overlaysarray.push(overlay_haulbaru9b);

	var latlng_haulbaru10b_1      = new google.maps.LatLng(-3.641766, 115.649965);
    var latlng_haulbaru10b_2      = new google.maps.LatLng(-3.628476, 115.659876);
    var image_haulbaru10b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-10b.png";
    var image_latlng_haulbaru10b = new google.maps.LatLngBounds(latlng_haulbaru10b_1, latlng_haulbaru10b_2);
    var overlay_haulbaru10b      = new google.maps.GroundOverlay(image_haulbaru10b, image_latlng_haulbaru10b);
    overlay_haulbaru10b.setMap(map);
    overlaysarray.push(overlay_haulbaru10b);

	var latlng_haulbaru11b_1      = new google.maps.LatLng(-3.629299, 115.646989);
    var latlng_haulbaru11b_2      = new google.maps.LatLng(-3.617203, 115.656511);
    var image_haulbaru11b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-11b.png";
    var image_latlng_haulbaru11b = new google.maps.LatLngBounds(latlng_haulbaru11b_1, latlng_haulbaru11b_2);
    var overlay_haulbaru11b      = new google.maps.GroundOverlay(image_haulbaru11b, image_latlng_haulbaru11b);
    overlay_haulbaru11b.setMap(map);
    overlaysarray.push(overlay_haulbaru11b);

	var latlng_haulbaru12b_1      = new google.maps.LatLng(-3.617917, 115.644804);
    var latlng_haulbaru12b_2      = new google.maps.LatLng(-3.605271, 115.654404);
    var image_haulbaru12b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12b.png";
    var image_latlng_haulbaru12b = new google.maps.LatLngBounds(latlng_haulbaru12b_1, latlng_haulbaru12b_2);
    var overlay_haulbaru12b      = new google.maps.GroundOverlay(image_haulbaru12b, image_latlng_haulbaru12b);
    overlay_haulbaru12b.setMap(map);
    overlaysarray.push(overlay_haulbaru12b);

	var latlng_haulbaru12ab_1      = new google.maps.LatLng(-3.611290, 115.636175);
    var latlng_haulbaru12ab_2      = new google.maps.LatLng(-3.600336, 115.645520);
    var image_haulbaru12ab         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12ab.png";
    var image_latlng_haulbaru12ab = new google.maps.LatLngBounds(latlng_haulbaru12ab_1, latlng_haulbaru12ab_2);
    var overlay_haulbaru12ab      = new google.maps.GroundOverlay(image_haulbaru12ab, image_latlng_haulbaru12ab);
    overlay_haulbaru12ab.setMap(map);
    overlaysarray.push(overlay_haulbaru12ab);

	var latlng_haulbaru13b_1      = new google.maps.LatLng(-3.610264, 115.649310);
    var latlng_haulbaru13b_2      = new google.maps.LatLng(-3.597485, 115.659609);
    var image_haulbaru13b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-13b.png";
    var image_latlng_haulbaru13b = new google.maps.LatLngBounds(latlng_haulbaru13b_1, latlng_haulbaru13b_2);
    var overlay_haulbaru13b      = new google.maps.GroundOverlay(image_haulbaru13b, image_latlng_haulbaru13b);
    overlay_haulbaru13b.setMap(map);
    overlaysarray.push(overlay_haulbaru13b);

	var latlng_haulbaru13b_1      = new google.maps.LatLng(-3.610296, 115.649353);
    var latlng_haulbaru13b_2      = new google.maps.LatLng(-3.597541, 115.659512);
    var image_haulbaru13b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-13b.png";
    var image_latlng_haulbaru13b = new google.maps.LatLngBounds(latlng_haulbaru13b_1, latlng_haulbaru13b_2);
    var overlay_haulbaru13b      = new google.maps.GroundOverlay(image_haulbaru13b, image_latlng_haulbaru13b);
    overlay_haulbaru13b.setMap(map);
    overlaysarray.push(overlay_haulbaru13b);

	var latlng_haulbaru14b_1      = new google.maps.LatLng(-3.598286, 115.649202);
    var latlng_haulbaru14b_2      = new google.maps.LatLng(-3.585498, 115.660162);
    var image_haulbaru14b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-14b.png";
    var image_latlng_haulbaru14b = new google.maps.LatLngBounds(latlng_haulbaru14b_1, latlng_haulbaru14b_2);
    var overlay_haulbaru14b      = new google.maps.GroundOverlay(image_haulbaru14b, image_latlng_haulbaru14b);
    overlay_haulbaru14b.setMap(map);
    overlaysarray.push(overlay_haulbaru14b);

	var latlng_haulbaru15b_1      = new google.maps.LatLng(-3.586371, 115.649267);
    var latlng_haulbaru15b_2      = new google.maps.LatLng(-3.573542, 115.659822);
    var image_haulbaru15b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-15b.png";
    var image_latlng_haulbaru15b = new google.maps.LatLngBounds(latlng_haulbaru15b_1, latlng_haulbaru15b_2);
    var overlay_haulbaru15b      = new google.maps.GroundOverlay(image_haulbaru15b, image_latlng_haulbaru15b);
    overlay_haulbaru15b.setMap(map);
    overlaysarray.push(overlay_haulbaru15b);

	var latlng_haulbaru16b_1      = new google.maps.LatLng(-3.574349, 115.649297);
    var latlng_haulbaru16b_2      = new google.maps.LatLng(-3.561528, 115.659364);
    var image_haulbaru16b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16b.png";
    var image_latlng_haulbaru16b = new google.maps.LatLngBounds(latlng_haulbaru16b_1, latlng_haulbaru16b_2);
    var overlay_haulbaru16b      = new google.maps.GroundOverlay(image_haulbaru16b, image_latlng_haulbaru16b);
    overlay_haulbaru16b.setMap(map);
    overlaysarray.push(overlay_haulbaru16b);

	var latlng_haulbaru16ab_1      = new google.maps.LatLng(-3.574005, 115.640451);
    var latlng_haulbaru16ab_2      = new google.maps.LatLng(-3.561480, 115.650092);
    var image_haulbaru16ab         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16ab.png";
    var image_latlng_haulbaru16ab = new google.maps.LatLngBounds(latlng_haulbaru16ab_1, latlng_haulbaru16ab_2);
    var overlay_haulbaru16ab      = new google.maps.GroundOverlay(image_haulbaru16ab, image_latlng_haulbaru16ab);
    overlay_haulbaru16ab.setMap(map);
    overlaysarray.push(overlay_haulbaru16ab);

	var latlng_haulbaru16bb_1      = new google.maps.LatLng(-3.573718, 115.631792);
    var latlng_haulbaru16bb_2      = new google.maps.LatLng(-3.561473, 115.641361);
    var image_haulbaru16bb         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16bb.png";
    var image_latlng_haulbaru16bb = new google.maps.LatLngBounds(latlng_haulbaru16bb_1, latlng_haulbaru16bb_2);
    var overlay_haulbaru16bb      = new google.maps.GroundOverlay(image_haulbaru16bb, image_latlng_haulbaru16bb);
    overlay_haulbaru16bb.setMap(map);
    overlaysarray.push(overlay_haulbaru16bb);

	var latlng_haulbaru16cb_1      = new google.maps.LatLng(-3.573699, 115.626143);
    var latlng_haulbaru16cb_2      = new google.maps.LatLng(-3.561356, 115.632515);
    var image_haulbaru16cb         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16cb.png";
    var image_latlng_haulbaru16cb = new google.maps.LatLngBounds(latlng_haulbaru16cb_1, latlng_haulbaru16cb_2);
    var overlay_haulbaru16cb      = new google.maps.GroundOverlay(image_haulbaru16cb, image_latlng_haulbaru16cb);
    overlay_haulbaru16cb.setMap(map);
    overlaysarray.push(overlay_haulbaru16cb);

	var latlng_haulbaru17b_1      = new google.maps.LatLng(-3.563110, 115.645952);
    var latlng_haulbaru17b_2      = new google.maps.LatLng(-3.550431, 115.655632);
    var image_haulbaru17b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-17b.png";
    var image_latlng_haulbaru17b = new google.maps.LatLngBounds(latlng_haulbaru17b_1, latlng_haulbaru17b_2);
    var overlay_haulbaru17b      = new google.maps.GroundOverlay(image_haulbaru17b, image_latlng_haulbaru17b);
    overlay_haulbaru17b.setMap(map);
    overlaysarray.push(overlay_haulbaru17b);

	var latlng_haulbaru18b_1      = new google.maps.LatLng(-3.551394, 115.646235);
    var latlng_haulbaru18b_2      = new google.maps.LatLng(-3.537727, 115.655472);
    var image_haulbaru18b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-18b.png";
    var image_latlng_haulbaru18b = new google.maps.LatLngBounds(latlng_haulbaru18b_1, latlng_haulbaru18b_2);
    var overlay_haulbaru18b      = new google.maps.GroundOverlay(image_haulbaru18b, image_latlng_haulbaru18b);
    overlay_haulbaru18b.setMap(map);
    overlaysarray.push(overlay_haulbaru18b);

	var latlng_haulbaru19ab_1      = new google.maps.LatLng(-3.528162, 115.641481);
    var latlng_haulbaru19ab_2      = new google.maps.LatLng(-3.515174, 115.651329);
    var image_haulbaru19ab         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-19ab.png";
    var image_latlng_haulbaru19ab = new google.maps.LatLngBounds(latlng_haulbaru19ab_1, latlng_haulbaru19ab_2);
    var overlay_haulbaru19ab      = new google.maps.GroundOverlay(image_haulbaru19ab, image_latlng_haulbaru19ab);
    overlay_haulbaru19ab.setMap(map);
    overlaysarray.push(overlay_haulbaru19ab);

	var latlng_haulbaru19b_1      = new google.maps.LatLng(-3.538697, 115.644070);
    var latlng_haulbaru19b_2      = new google.maps.LatLng(-3.526777, 115.653758);
    var image_haulbaru19b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-19b.png";
    var image_latlng_haulbaru19b = new google.maps.LatLngBounds(latlng_haulbaru19b_1, latlng_haulbaru19b_2);
    var overlay_haulbaru19b      = new google.maps.GroundOverlay(image_haulbaru19b, image_latlng_haulbaru19b);
    overlay_haulbaru19b.setMap(map);
    overlaysarray.push(overlay_haulbaru19b);

	var latlng_haulbaru20b_1      = new google.maps.LatLng(-3.531550, 115.636986);
    var latlng_haulbaru20b_2      = new google.maps.LatLng(-3.519104, 115.646871);
    var image_haulbaru20b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-20b.png";
    var image_latlng_haulbaru20b = new google.maps.LatLngBounds(latlng_haulbaru20b_1, latlng_haulbaru20b_2);
    var overlay_haulbaru20b      = new google.maps.GroundOverlay(image_haulbaru20b, image_latlng_haulbaru20b);
    overlay_haulbaru20b.setMap(map);
    overlaysarray.push(overlay_haulbaru20b);

	var latlng_haulbaru21b_1      = new google.maps.LatLng(-3.524798, 115.635698);
    var latlng_haulbaru21b_2      = new google.maps.LatLng(-3.511573, 115.645818);
    var image_haulbaru21b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-21b.png";
    var image_latlng_haulbaru21b = new google.maps.LatLngBounds(latlng_haulbaru21b_1, latlng_haulbaru21b_2);
    var overlay_haulbaru21b      = new google.maps.GroundOverlay(image_haulbaru21b, image_latlng_haulbaru21b);
    overlay_haulbaru21b.setMap(map);
    overlaysarray.push(overlay_haulbaru21b);



    // BIB MAPS UPDATE 18 12 2021
  }else {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'hidden') ? google.maps.MapTypeId.satellite : 'satellite');

    clearOverlays();
    overlaystatus = 0;
  }
}

function clearOverlays() {
 while(overlaysarray.length) {
   overlaysarray.pop().setMap(null);
 }
  overlaysarray.length = 0;
}

// REALTIME ALERT START
var realtimealertarray        = [];
var realtimealertarraysummary = [];
var userid                    = '<?php echo $this->sess->user_id?>';
var intervalalert2, intervalalert1, intervalalert;
var soundisactive             = 0; // VALUE DEFAULTNYA == 1. GANTI JADI 1 KALAU MAU DIBUNYIKAN
var alertloop                 = 0;

// var intervalalert = setInterval(dataalert, 15000);
var lasttimealert = 0;
var limitalert    = 10;
var intervalke    = 1;

function dataalert(){
  $.post("<?php echo base_url() ?>securityevidence/realtimealertnew", {intervalke : intervalke, lasttimealert : lasttimealert, limitalert : limitalert}, function(response){
    // localStorage.setItem(obj[looplasttime].vehicle_device, obj[looplasttime].auto_last_update);
    console.log("response realtimealert : ", response);
    intervalke = response.intervalke;
    if (response.code == 200) {
      // console.log("array length before : ", realtimealertarraysummary.length);

      if (realtimealertarraysummary.length >= limitalert) {
        realtimealertarraysummary.shift();
        // realtimealertarraysummary.slice(Math.max(realtimealertarraysummary.length - limitalert, 0))
        // realtimealertarraysummary = [];
      }
      // console.log("array length after : ", realtimealertarraysummary.length);

      var data = response.data;

      for (var i = 0; i < data.length; i++) {
        var positionalert   = data[i].position.includes("KM");
        var gps_speed       = data[i].gps_speed;
        var gps_speed_limit = data[i].gps_speed_limit;

          if (positionalert) {
            if (gps_speed_limit > 0) {
              if (gps_speed >= gps_speed_limit) {
                if (realtimealertarraysummary.length > 0) {
                  var is_founded = searchEqualism(data[i].vehicle_no, data[i].gps_time);
                  console.log("is_founded : ", is_founded);
                    if (!is_founded) {
                      realtimealertarraysummary.push(data[i]);
                    }
                }else {
                  realtimealertarraysummary.push(data[i]);
                }
              }
            }
          }
      }

      // var realtimealertarraysummaryreverse       = realtimealertarraysummary.reverse();
      // var realtimealertarraysummaryreverse       = realtimealertarraysummary;
      var realtimealertarraysummaryreverse       =   realtimealertarraysummary.sort((a, b) => parseFloat(b.gps_time) - parseFloat(a.gps_time));

      console.log("realtimealertarraysummaryreverse : ", realtimealertarraysummaryreverse);

      if (realtimealertarraysummaryreverse.length > 0) {
        var htmlsummary = "";

        // for (var i = realtimealertarraysummaryreverse.length; i > 0; i--) {
          for (var i = 0; i < realtimealertarraysummaryreverse.length; i++) {
            // htmlsummary += '<span style="font-size:12px; color:green;">'+ realtimealertarraysummaryreverse[i].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ realtimealertarraysummaryreverse[i].gps_alert +'</span> <span style="font-size:12px;">'+ realtimealertarraysummaryreverse[i].gps_time +'</span> <span style="font-size:12px; color:black;">'+ 'Geofence : '+ realtimealertarraysummaryreverse[i].geofence +
            // '</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+realtimealertarraysummaryreverse[i].gps_latitude_real+','+realtimealertarraysummaryreverse[i].gps_longitude_real+'" target="_blank">'+ " "+realtimealertarraysummaryreverse[i].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ realtimealertarraysummaryreverse[i].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ realtimealertarraysummaryreverse[i].gps_speed_limit +' Kph </span> </br>';

            htmlsummary += '<span style="font-size:11px; color:green;" onclik="forsearchfromalert("'+realtimealertarraysummaryreverse[i].vehicle_no+'")">'+ realtimealertarraysummaryreverse[i].vehicle_no +'</span> <span style="font-size:11px; color:red;">'+ realtimealertarraysummaryreverse[i].gps_alert +'</span> <span style="font-size:11px;">'+ realtimealertarraysummaryreverse[i].gps_time +
            '</span> </br> <a style="font-size:11px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+realtimealertarraysummaryreverse[i].gps_latitude_real+','+realtimealertarraysummaryreverse[i].gps_longitude_real+'" target="_blank">'+ " "+realtimealertarraysummaryreverse[i].position +' </a> <span style="font-size:11px; color:black;">'+ 'Speed : '+ realtimealertarraysummaryreverse[i].gps_speed +
            ' Kph </span> <span style="font-size:11px; color:red;">'+ 'Limit : '+ realtimealertarraysummaryreverse[i].gps_speed_limit +' Kph </span> '+
            '<span style="font-size:11px; color:black;">'+ realtimealertarraysummaryreverse[i].jalur_name +'</span> </br><hr>';
        }
        $("#summaryalertcontent").html(htmlsummary);
      }

      // var alertsimultan = "";
      // if (data[0].datasimultan == "yes") {
      //   // alertsimultan += '<span style="font-size:12px; color:green;">'+ data[0].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ data[0].gps_alert +'</span> <span style="font-size:12px;">'+ data[0].gps_time +'</span> <span style="font-size:12px; color:black;">'+ 'Geofence : '+ data[0].geofence +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+data[0].gps_latitude_real+','+data[0].gps_longitude_real+'" target="_blank">'+ " "+data[0].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ data[0].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ data[0].gps_speed_limit +' Kph </span> </br>';
      //
      //   alertsimultan += '<span style="font-size:12px; color:green;">'+ data[0].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ data[0].gps_alert +'</span> <span style="font-size:12px;">'+ data[0].gps_time +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+data[0].gps_latitude_real+','+data[0].gps_longitude_real+'" target="_blank">'+ " "+data[0].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ data[0].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ data[0].gps_speed_limit +' Kph </span> </br>';
      //
      //   $("#newalertcontent").html(alertsimultan);
      //   $("#newalertcontent").show();
      // }else {
      //   $("#newalertcontent").hide();
      // }

      lasttimealert = data[0].gps_time;
      if (soundisactive == 1) {
        playsound();
      }
    }
  }, "json");

}

function searchEqualism(vehicleno, gpstime) {
  for (var i = 0; i < realtimealertarraysummary.length; i++) {
    if (realtimealertarraysummary[i].vehicle_no == vehicleno && realtimealertarraysummary[i].gps_time == gpstime) {
      status = true;
      return status;
    }
  }
}



function getalertnowsample(devid){
    var sampledataalert = [
      {
        gps_geofence: "Hauling 19 Road 23/27",
        gps_speed: "31",
        gps_speed_limit: "30",
        gps_speed_status: "1",
        position: "KM 25",
        vehicle_alert: "Overspeed",
        vehicle_alert_datetime: "2021-10-25 13:47:12",
        vehicle_alert_time: "13:47",
        vehicle_lat: "-3.5216",
        vehicle_lng: "115.6481",
        vehicle_name: "Hino 500",
        vehicle_no: "MKS 179"
      },
    ];

    var sampledataalert2 = [
      {
        gps_geofence: "Hauling 19 Road 23/27",
        gps_speed: "31",
        gps_speed_limit: "30",
        gps_speed_status: "1",
        position: "KM 10",
        vehicle_alert: "Overspeed",
        vehicle_alert_datetime: "2021-10-25 13:50:12",
        vehicle_alert_time: "13:50",
        vehicle_lat: "-3.5216",
        vehicle_lng: "115.6481",
        vehicle_name: "Hino 500",
        vehicle_no: "BKA 101"
      },
    ];

    // if (realtimealertarraysummary.length % 2 == 0) {
    //   realtimealertarraysummary.push(sampledataalert[0]);
    //   console.log("sikon 1");
    // }else {
    //   realtimealertarraysummary.push(sampledataalert2[0]);
    //   console.log("sikon 2");
    // }
    realtimealertarraysummary.push(sampledataalert[0]);

    var reversearraysummary = realtimealertarraysummary.reverse();

    // console.log("reversearraysummary : ", reversearraysummary);

    var htmlsummary = "";
    for (var i = 0; i < reversearraysummary.length; i++) {
      htmlsummary += '<span style="font-size:12px; color:green;">'+ reversearraysummary[0].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ reversearraysummary[0].vehicle_alert +'</span> <span style="font-size:12px;">'+ reversearraysummary[0].vehicle_alert_time +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+"-302321"+','+"1000023123"+'" target="_blank">'+ " "+reversearraysummary[i].position +' </a> <span style="font-size:12px; color:red;">'+ 'Limit : '+ reversearraysummary[0].gps_speed_limit +'</span> </br>';
    }
    $("#summaryalertcontent").html(htmlsummary);

    if (realtimealertarraysummary.length >= limitalert) {
      realtimealertarraysummary = [];
    }

    // if (soundisactive == 1) {
    //   playsound();
    // }
}


function closemodalalertrealtime(){
  // $("#modalalertrealtime").hide();
  $("#realtimealertshow").hide();
}

function alertsummary(){
  $("#modalalertsummry").show();
}

function closemodalsummaryalert(){
  $("#modalalertsummry").hide();
}

function playsound() {
  var audio = new Audio('<?php echo base_url() ?>assets/sounds/alert1.mp3');
    audio.play();
  // var sound = document.getElementById(soundObj);
  // sound.Play();
}

function activatesound(){
  if (soundisactive == 1) {
    soundisactive = 0;
    $("#activatesound2").show();
    $("#activatesound").hide();
  }else {
    soundisactive = 1;
    $("#activatesound2").hide();
    $("#activatesound").show();
  }
}

function activatesound2(){
  if (soundisactive == 1) {
    soundisactive = 0;
    $("#activatesound2").show();
    $("#activatesound").hide();
  }else {
    soundisactive = 1;
    $("#activatesound2").hide();
    $("#activatesound").show();
  }
}

function changelimit(){
  var limit = $("#changelimitrealtimalert").val();
    if (limit == 10) {
      limitalert = 10;
    }else if (limit == 20) {
      limitalert = 20;
    }else {
      limitalert = 30;
    }
}

function forsearchinput(){
  var deviceid = $("#searchnopol").val();
    if (deviceid == 0) {
      alert("Silahkan pilih kendaraan terlebih dahulu");
    }else {
      console.log("device id forsearchinput : ", deviceid);
      $("#nopolforcheck").val(deviceid);
      var data = {key : deviceid};
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
        jQuery("#loader2").hide();
        console.log("ini respon forsearchinput : ", response);
        $("#nopolforhide").val(response[0].vehicle_device);
        if (response.code == 400) {
          alert("Data tidak ditemukan");
        }else {
          infowindowkedua      = new google.maps.InfoWindow();
          infowindowonsimultan = new google.maps.InfoWindow();
          infowindowkedua.close();
          infowindowonsimultan.close();
          DeleteMarkers(response[0].vehicle_id);
          DeleteMarkerspertama(response[0].vehicle_id);
          DeleteMarkersForSearchinput();
          // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
          if (response[0].devicestatusfixnya) {
            // console.log("devicestatus ada : ");
            if (response[0].devicestatusfixnya == 1) {
              var devicestatus = "Camera : Online <br>" ;
            }else {
              var devicestatus = "Camera : Offline <br>" ;
            }
          }else {
            if (response[0].devicestatusfixnya == "") {
              var devicestatus = "";
            }else if (response[0].devicestatusfixnya == 0) {
              var devicestatus = "Camera : Offline </br>";
            }else {
              var devicestatus = "";
            }
          }

          if (response[0].drivername) {
            var drivername = response[0].drivername;
           if (response[0].driverimage) {
             console.log("sikon 1");
             if (response[0].driverimage != 0) {
               console.log("sikon 2");
               // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
               var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
             }else {
               var detaildriver = drivername + ' </br> No Driver Image';
             }
           }else {
             var detaildriver = drivername + ' </br> No Driver Image';
           }
          }

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          var num         = Number(response[0].auto_last_speed);
          var roundstring = num.toFixed(0);
          var rounded     = Number(roundstring);

          var addresssplit = response[0].auto_last_position.split(" ");
          var inarea       = response[0].auto_last_position.split(",");

          var addressfix = bibarea.includes(addresssplit[0]);
          if (addressfix) {
            var addressfix = inarea[0];
          }else {
            var addressfix = response[0].auto_last_position;
          }

          var ritasefix = 0;
            if (response[0].autolastritase != 0) {
              ritasefix = response[0].autolastritase;
            }else {
              ritasefix = 0;
            }

            var lastenginefix = "OFF";

          if (response[0].auto_last_road == "muatan") {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded == 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("muatan : sikon 1");
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("muatan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
              lastenginefix = "ON";
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else {
              // console.log("muatan : sikon 3");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }else {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded > 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("kosongan : sikon 1");
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#0000FF',
                offset: '5%'
              };
            }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("kosongan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
              lastenginefix = "ON";
              // console.log("kosongan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else {
              // console.log("kosongan : sikon 3");
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }

          if (response[0].auto_last_driver_name) {
            var last_driver_id   = response[0].auto_last_driver_id;
            var last_driver_name = response[0].auto_last_driver_name;
            var last_driver_time = response[0].auto_last_driver_time;
          }else {
            var last_driver_id   = "";
            var last_driver_name = "";
            var last_driver_time = "";
          }

          titlemarker = "";
          titlemarker += '<table class="table" style="font-size:12px;">';
            titlemarker += '<tr>';
            titlemarker += '<td style="text-align:center;">';
            titlemarker += last_driver_id + ' </br> ' + last_driver_name +'</br>';
            titlemarker += last_driver_time +'</br>';
              titlemarker += '</td>';
              titlemarker += '<td>';
                titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
                titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
                titlemarker += 'Position : ' + addressfix + '</br>';
                titlemarker += 'Jalur : ' + response[0].auto_last_road + '</br>';
                titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
                titlemarker += 'Engine : ' + lastenginefix + '</br>';
                titlemarker += 'Fuel : ' + response[0].auto_last_mvd + ' Ltr</br>';
                titlemarker += 'Speed : ' + rounded + ' kph </br>';
                titlemarker +=  devicestatus;
                titlemarker += 'Ritase : ' + ritasefix + '</br>';
                if (response[0].vehicle_rom_name != null) {
                  titlemarker += 'Last Cargo : ' + response[0].vehicle_rom_name + ' ' + response[0].vehicle_rom_time + '</br>';
                }
                titlemarker += '<div onclick="tutupinformasi('+response[0].vehicle_id+')" style="color:blue;cursor:pointer;">Tutup Informasi</div>';
                // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
                // titlemarker +=   lct + imglct;
              titlemarker += '</td>';
            titlemarker += '</tr>';
          titlemarker += '</table>';

           infowindowkedua = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });

          // showmapmode();

          markernya = new google.maps.Marker({
            map: map,
            icon: icon,
            position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
            title: response[0].vehicle_no,
            // + ' - ' + value.vehicle_name + value.driver + "\n" +
            //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
            //   "Speed : " + value.gps.gps_speed + " kph",
            id: response[0].vehicle_id
          });
          markerss.push(markernya);
          icon.rotation = Math.ceil(response[0].auto_last_course);
          markernya.setIcon(icon);


          // map.setZoom(18);
          infowindowkedua.open(map, markernya);
          map.setCenter(center);
          markernya.setPosition(center);

          // ON HOVER START
          google.maps.event.addListener(markernya, 'mouseover', function(){
            var varthis = this;
            setTimeoutConst = setTimeout(function() {
              console.log("mouseover 2second on search key");
                infowindowonsimultan = new google.maps.InfoWindow({
                  content: titlemarker,
                  maxWidth: 300
                });

               infowindowonsimultan.setContent(titlemarker);
               infowindowonsimultan.open(map, varthis);
            }, 2000);
          });

          // assuming you also want to hide the infowindow when user mouses-out
          google.maps.event.addListener(markernya, 'mouseout', function(){
            console.log("mouseout on search key");
            clearTimeout(setTimeoutConst);
              infowindowonsimultan.close();
          });
          // ON HOVER END

          google.maps.event.addListener(markernya, 'click', function(evt){
            console.log("icon map di klik from search key");
            // infowindow2.close();
            // infowindowkedua.close();
            // infowindow.close();

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
            //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
            //   "Engine : " + response[0].auto_last_engine + "<br>" +
            //   "Speed : " + rounded + " kph" + "<br>" +
            //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

             infowindowkedua = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });
            // DeleteMarkers(response[0].vehicle_device);
            // DeleteMarkerspertama(response[0].vehicle_device);

              var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
              infowindowkedua.setContent(titlemarker);
              map.setCenter(markernya.position);
              markernya.setPosition(markernya.position);
              infowindowkedua.open(map, this);
          });
        }
      }, "json");
      $("#mapShow").show();
      // $("#realtimealertshowhide").show();
      $("#tableShowMuatan").hide();
      $("#tableShowKosongan").hide();
      $("#tableShowPort").hide();
      $("#tableShowPool").hide();
      $("#tableShowPoolNew").hide();
      $("#tableShowOutOfHauling").hide();
      $("#tableShowRom").hide();
      $("#valueMode").val(0);
    }
}

function updateinfowindow(nopol){
  var deviceid = nopol;
    if (deviceid == 0) {
      // infowindowkedua.close();
      // infowindowonsimultan.close();
    }else {
      var data = {key : deviceid};
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
        jQuery("#loader2").hide();
        console.log("ini respon updateinfowindow : ", response);
        if (response.code == 400) {
          alert("Data tidak ditemukan");
        }else {
          infowindowkedua      = new google.maps.InfoWindow();
          infowindowonsimultan = new google.maps.InfoWindow();
          var nopolforhide = $("#nopolforhide").val();
          DeleteMarkers(nopolforhide);
          DeleteMarkers(response[0].vehicle_device);
          DeleteMarkers(response[0].vehicle_id);
          DeleteMarkerspertama(response[0].vehicle_id);
          // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
          if (response[0].devicestatusfixnya) {
            // console.log("devicestatus ada : ");
            if (response[0].devicestatusfixnya == 1) {
              var devicestatus = "Camera : Online <br>" ;
            }else {
              var devicestatus = "Camera : Offline <br>" ;
            }
          }else {
            if (response[0].devicestatusfixnya == "") {
              var devicestatus = "";
            }else if (response[0].devicestatusfixnya == 0) {
              var devicestatus = "Camera : Offline </br>";
            }else {
              var devicestatus = "";
            }
          }

          if (response[0].drivername) {
            var drivername = response[0].drivername;
           if (response[0].driverimage) {
             console.log("sikon 1");
             if (response[0].driverimage != 0) {
               console.log("sikon 2");
               // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
               var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
             }else {
               var detaildriver = drivername + ' </br> No Driver Image';
             }
           }else {
             var detaildriver = drivername + ' </br> No Driver Image';
           }
          }

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          var num         = Number(response[0].auto_last_speed);
          var roundstring = num.toFixed(0);
          var rounded     = Number(roundstring);

          var addresssplit = response[0].auto_last_position.split(" ");
          var inarea       = response[0].auto_last_position.split(",");

          var addressfix = bibarea.includes(addresssplit[0]);
          if (addressfix) {
            var addressfix = inarea[0];
          }else {
            var addressfix = response[0].auto_last_position;
          }

          var ritasefix = 0;
            if (response[0].autolastritase != 0) {
              ritasefix = response[0].autolastritase;
            }else {
              ritasefix = 0;
            }

            var lastenginefix = "OFF";

          if (response[0].auto_last_road == "muatan") {
            lastenginefix = "ON";
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded == 0 && response[0].auto_last_engine == "ON") {
              // console.log("muatan : sikon 1");
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("muatan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
              lastenginefix = "ON";
              // console.log("muatan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else {
              // console.log("muatan : sikon 3");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }else {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded > 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("kosongan : sikon 1");
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#0000FF',
                offset: '5%'
              };
            }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
              lastenginefix = "ON";
              // console.log("kosongan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
              lastenginefix = "ON";
              // console.log("kosongan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else {
              // console.log("kosongan : sikon 3");
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }

          titlemarker = "";
          titlemarker += '<table class="table" style="font-size:12px;">';
            titlemarker += '<tr>';
              titlemarker += '<td>'+detaildriver+'</td>';
              titlemarker += '<td>';
                titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
                titlemarker += 'Driver : ' + drivername +'</br>';
                titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
                titlemarker += 'Position : ' + addressfix + '</br>';
                titlemarker += 'Jalur : ' + response[0].auto_last_road + '</br>';
                titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
                titlemarker += 'Engine : ' + lastenginefix + '</br>';
                titlemarker += 'Fuel : ' + response[0].auto_last_mvd + ' Ltr</br>';
                titlemarker += 'Speed : ' + rounded + ' kph </br>';
                titlemarker +=  devicestatus;
                titlemarker += 'Ritase : ' + ritasefix + '</br>';
                  if (response[0].vehicle_rom_name != null) {
                    titlemarker += 'Last Cargo : ' + response[0].vehicle_rom_name + ' ' + response[0].vehicle_rom_time + '</br>';
                  }
                titlemarker += '<div onclick="tutupinformasi('+response[0].vehicle_id+')" style="color:blue;cursor:pointer;">Tutup Informasi</div>';
                // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
                // titlemarker +=   lct + imglct;
              titlemarker += '</td>';
            titlemarker += '</tr>';
          titlemarker += '</table>';

           infowindowkedua = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });

          // showmapmode();

          markernya = new google.maps.Marker({
            map: map,
            icon: icon,
            position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
            title: response[0].vehicle_no,
            // + ' - ' + value.vehicle_name + value.driver + "\n" +
            //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
            //   "Speed : " + value.gps.gps_speed + " kph",
            id: response[0].vehicle_id
          });
          markerss.push(markernya);
          icon.rotation = Math.ceil(response[0].auto_last_course);
          markernya.setIcon(icon);


          // map.setZoom(18);
          infowindowkedua.open(map, markernya);
          map.setCenter(center);
          markernya.setPosition(center);

          // ON HOVER START
          google.maps.event.addListener(markernya, 'mouseover', function(){
            var varthis = this;
            setTimeoutConst = setTimeout(function() {
              console.log("mouseover 2second on search key");
                infowindowonsimultan = new google.maps.InfoWindow({
                  content: titlemarker,
                  maxWidth: 300
                });

               infowindowonsimultan.setContent(titlemarker);
               infowindowonsimultan.open(map, varthis);
            }, 2000);
          });

          // assuming you also want to hide the infowindow when user mouses-out
          google.maps.event.addListener(markernya, 'mouseout', function(){
            console.log("mouseout on search key");
            clearTimeout(setTimeoutConst);
              infowindowonsimultan.close();
          });
          // ON HOVER END

          google.maps.event.addListener(markernya, 'click', function(evt){
            console.log("icon map di klik from search key");
            // infowindow2.close();
            // infowindowkedua.close();
            // infowindow.close();

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
            //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
            //   "Engine : " + response[0].auto_last_engine + "<br>" +
            //   "Speed : " + rounded + " kph" + "<br>" +
            //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

             infowindowkedua = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });
            // DeleteMarkers(response[0].vehicle_device);
            // DeleteMarkerspertama(response[0].vehicle_device);

              var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
              infowindowkedua.setContent(titlemarker);
              map.setCenter(markernya.position);
              markernya.setPosition(markernya.position);
              infowindowkedua.open(map, this);
          });
        }
      }, "json");
      $("#mapShow").show();
      // $("#realtimealertshowhide").show();
      $("#tableShowMuatan").hide();
      $("#tableShowKosongan").hide();
      $("#tableShowPort").hide();
      $("#tableShowPool").hide();
      $("#tableShowPoolNew").hide();
      $("#tableShowOutOfHauling").hide();
      $("#tableShowRom").hide();
      $("#valueMode").val(0);
    }
}

function forsearchoutofhauling(){
  var deviceid = $("#forsearchoutofhauling").val();
    if (deviceid == 0) {
      alert("Silahkan pilih kendaraan terlebih dahulu");
    }else {
      console.log("device id forsearchinput : ", deviceid);

      var data = {key : deviceid};

      $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
        infowindowkedua.close();
        console.log("ini respon pencarian : ", response);
        if (response.code == 400) {
          alert("Data tidak ditemukan");
        }else {
          // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
          if (response[0].devicestatusfixnya) {
            // console.log("devicestatus ada : ");
            if (response[0].devicestatusfixnya == 1) {
              var devicestatus = "Camera : Online <br>" ;
            }else {
              var devicestatus = "Camera : Offline <br>" ;
            }
          }else {
            if (response[0].devicestatusfixnya == "") {
              var devicestatus = "";
            }else if (response[0].devicestatusfixnya == 0) {
              var devicestatus = "Camera : Offline </br>";
            }else {
              var devicestatus = "";
            }
          }

          if (response[0].drivername) {
            var drivername = response[0].drivername;
           if (response[0].driverimage) {
             console.log("sikon 1");
             if (response[0].driverimage != 0) {
               console.log("sikon 2");
               // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
               var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
             }else {
               var detaildriver = drivername + ' </br> No Driver Image';
             }
           }else {
             var detaildriver = drivername + ' </br> No Driver Image';
           }
          }

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          var num         = Number(response[0].auto_last_speed);
          var roundstring = num.toFixed(0);
          var rounded     = Number(roundstring);

          var addresssplit = response[0].auto_last_position.split(" ");
          var inarea       = response[0].auto_last_position.split(",");

          var addressfix = bibarea.includes(addresssplit[0]);
          if (addressfix) {
            var addressfix = inarea[0];
          }else {
            var addressfix = response[0].auto_last_position;
          }

          DeleteMarkers(response[0].vehicle_device);
          DeleteMarkerspertama(response[0].vehicle_device);

          var ritasefix = 0;
            if (response[0].autolastritase != 0) {
              ritasefix = response[0].autolastritase;
            }else {
              ritasefix = 0;
            }

          titlemarker = "";
          titlemarker += '<table class="table" style="font-size:12px;">';
            titlemarker += '<tr>';
              titlemarker += '<td>'+detaildriver+'</td>';
              titlemarker += '<td>';
                titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
                titlemarker += 'Driver : ' + drivername +'</br>';
                titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
                titlemarker += 'Position : ' + addressfix + '</br>';
                titlemarker += 'Jalur : ' + response[0].auto_last_road + '</br>';
                titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
                titlemarker += 'Engine : ' + response[0].auto_last_engine + '</br>';
                titlemarker += 'Fuel : ' + response[0].auto_last_mvd + ' Ltr</br>';
                titlemarker += 'Speed : ' + rounded + ' kph </br>';
                titlemarker +=  devicestatus;
                titlemarker += 'Ritase : ' + ritasefix + '</br>';
                titlemarker += '<div onclick="closeWindowOnMarkerOOH();" style="color:blue;cursor:pointer;">Tutup Informasi</div>';
                // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
                // titlemarker +=   lct + imglct;
              titlemarker += '</td>';
            titlemarker += '</tr>';
          titlemarker += '</table>';

           infowindowkedua = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });

          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#800080',
            offset: '5%'
          };

          markernya = new google.maps.Marker({
            map: map,
            icon: icon,
            position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
            title: response[0].vehicle_no,
            // + ' - ' + value.vehicle_name + value.driver + "\n" +
            //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
            //   "Speed : " + value.gps.gps_speed + " kph",
            id: response[0].vehicle_id
          });
          markerss.push(markernya);
          icon.rotation = Math.ceil(response[0].auto_last_course);
          // markernya.setIcon(icon);


          infowindowkedua.open(map, markernya);

          // ON HOVER START
          google.maps.event.addListener(markernya, 'mouseover', function(){
            var varthis = this;
            setTimeoutConst = setTimeout(function() {
              console.log("mouseover 2second on search key");
                infowindowonsimultan = new google.maps.InfoWindow({
                  content: titlemarker,
                  maxWidth: 300
                });

               infowindowonsimultan.setContent(titlemarker);
               infowindowonsimultan.open(map, varthis);
            }, 2000);
          });

          // assuming you also want to hide the infowindow when user mouses-out
          google.maps.event.addListener(markernya, 'mouseout', function(){
            console.log("mouseout on search key");
            clearTimeout(setTimeoutConst);
              infowindowonsimultan.close();
          });
          // ON HOVER END

          google.maps.event.addListener(markernya, 'click', function(evt){
            console.log("icon map di klik from search key");
            // infowindow2.close();
            // infowindowkedua.close();
            // infowindow.close();

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
            //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
            //   "Engine : " + response[0].auto_last_engine + "<br>" +
            //   "Speed : " + rounded + " kph" + "<br>" +
            //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

             infowindowkedua = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });
            // DeleteMarkers(response[0].vehicle_device);
            // DeleteMarkerspertama(response[0].vehicle_device);

              var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
              infowindowkedua.setContent(titlemarker);
              map.setCenter(markernya.position);
              markernya.setPosition(markernya.position);
              infowindowkedua.open(map, this);
          });
        }
      }, "json");
      $("#mapShow").show();
      // $("#realtimealertshowhide").hide();
      $("#tableShowMuatan").hide();
      $("#tableShowKosongan").hide();
      $("#tableShowPort").hide();
      $("#tableShowPool").hide();
      $("#tableShowPoolNew").hide();
      $("#tableShowOutOfHauling").show();
      $("#tableShowRom").hide();
      $("#valueMode").val(0);
    }
}

function forsearchfromalert(deviceid){
      var data = {key : deviceid};
      $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
        console.log("ini respon pencarian : ", response);
        if (response.code == 400) {
          alert("Data tidak ditemukan");
        }else {
          // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
          if (response[0].devicestatusfixnya) {
            // console.log("devicestatus ada : ");
            if (response[0].devicestatusfixnya == 1) {
              var devicestatus = "Camera : Online <br>" ;
            }else {
              var devicestatus = "Camera : Offline <br>" ;
            }
          }else {
            if (response[0].devicestatusfixnya == "") {
              var devicestatus = "";
            }else if (response[0].devicestatusfixnya == 0) {
              var devicestatus = "Camera : Offline </br>";
            }else {
              var devicestatus = "";
            }
          }

          if (response[0].drivername) {
            var drivername = response[0].drivername;
           if (response[0].driverimage) {
             console.log("sikon 1");
             if (response[0].driverimage != 0) {
               console.log("sikon 2");
               // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
               var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
             }else {
               var detaildriver = drivername + ' </br> No Driver Image';
             }
           }else {
             var detaildriver = drivername + ' </br> No Driver Image';
           }
          }

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          var num         = Number(response[0].auto_last_speed);
          var roundstring = num.toFixed(0);
          var rounded     = Number(roundstring);

          var addresssplit = response[0].auto_last_position.split(" ");
          var inarea       = response[0].auto_last_position.split(",");

          var addressfix = bibarea.includes(addresssplit[0]);
          if (addressfix) {
            var addressfix = inarea[0];
          }else {
            var addressfix = response[0].auto_last_position;
          }

          var ritasefix = 0;
            if (response[0].autolastritase != 0) {
              ritasefix = response[0].autolastritase;
            }else {
              ritasefix = 0;
            }

          titlemarker = "";
          titlemarker += '<table class="table" style="font-size:12px;">';
            titlemarker += '<tr>';
              titlemarker += '<td>'+detaildriver+'</td>';
              titlemarker += '<td>';
                titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
                titlemarker += 'Driver : ' + drivername +'</br>';
                titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
                titlemarker += 'Position : ' + addressfix + '</br>';
                titlemarker += 'Jalur : ' + response[0].auto_last_road + '</br>';
                titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
                titlemarker += 'Engine : ' + response[0].auto_last_engine + '</br>';
                titlemarker += 'Fuel : ' + response[0].auto_last_mvd + ' Ltr</br>';
                titlemarker += 'Speed : ' + rounded + ' kph </br>';
                titlemarker +=  devicestatus;
                titlemarker += 'Ritase : ' + ritasefix + '</br>';
                titlemarker += '<div onclick="DeleteMarkers('+response[0].vehicle_id+')" style="color:blue;cursor:pointer;">Tutup Informasi</div>';
                // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
                // titlemarker +=   lct + imglct;
              titlemarker += '</td>';
            titlemarker += '</tr>';
          titlemarker += '</table>';

           infowindowkedua = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });

          if (response[0].auto_last_road == "muatan") {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded == 0 && response[0].auto_last_engine == "ON") {
              // console.log("muatan : sikon 1");
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
              // console.log("muatan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else {
              // console.log("muatan : sikon 3");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }else {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded > 0 && response[0].auto_last_engine == "ON") {
              // console.log("kosongan : sikon 1");
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#0000FF',
                offset: '5%'
              };
            }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
              // console.log("kosongan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else {
              // console.log("kosongan : sikon 3");
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }

          // showmapmode();

          markernya = new google.maps.Marker({
            map: map,
            icon: icon,
            position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
            title: response[0].vehicle_no,
            // + ' - ' + value.vehicle_name + value.driver + "\n" +
            //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
            //   "Speed : " + value.gps.gps_speed + " kph",
            id: response[0].vehicle_id
          });
          markerss.push(markernya);
          icon.rotation = Math.ceil(response[0].auto_last_course);
          markernya.setIcon(icon);


          // map.setZoom(18);
          infowindowkedua.open(map, markernya);
          map.setCenter(center);
          markernya.setPosition(center);

          // ON HOVER START
          google.maps.event.addListener(markernya, 'mouseover', function(){
            var varthis = this;
            setTimeoutConst = setTimeout(function() {
              console.log("mouseover 2second on search key");
                infowindowonsimultan = new google.maps.InfoWindow({
                  content: titlemarker,
                  maxWidth: 300
                });

               infowindowonsimultan.setContent(titlemarker);
               infowindowonsimultan.open(map, varthis);
            }, 2000);
          });

          // assuming you also want to hide the infowindow when user mouses-out
          google.maps.event.addListener(markernya, 'mouseout', function(){
            console.log("mouseout on search key");
            clearTimeout(setTimeoutConst);
              infowindowonsimultan.close();
          });
          // ON HOVER END

          google.maps.event.addListener(markernya, 'click', function(evt){
            console.log("icon map di klik from search key");
            // infowindow2.close();
            // infowindowkedua.close();
            // infowindow.close();

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
            //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
            //   "Engine : " + response[0].auto_last_engine + "<br>" +
            //   "Speed : " + rounded + " kph" + "<br>" +
            //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

             infowindowkedua = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });
            // DeleteMarkers(response[0].vehicle_device);
            // DeleteMarkerspertama(response[0].vehicle_device);

              var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
              infowindowkedua.setContent(titlemarker);
              map.setCenter(markernya.position);
              markernya.setPosition(markernya.position);
              infowindowkedua.open(map, this);
          });
        }
      }, "json");
      $("#mapShow").show();
      // $("#realtimealertshowhide").show();
      $("#tableShowMuatan").hide();
      $("#tableShowKosongan").hide();
      $("#tableShowPort").hide();
      $("#tableShowPool").hide();
      $("#tableShowPoolNew").hide();
      $("#tableShowOutOfHauling").hide();
      $("#tableShowRom").hide();
      $("#valueMode").val(0);
}

function tutupinformasi(id) {
  $("#nopolforcheck").val("0");
  //Loop through all the markers and remove
  // console.log("marker pertama id yg dihapus : ", id);
  for (var i = 0; i < markerss.length; i++) {
    if (markerss[i].id == id) {
      //Remove the om Map
      markerss[i].setMap(null);

      //Remove the marker from array.
      markerss.splice(i, 1);
      return;
    }
  }
}

function DeleteMarkersForSearchinput(id) {
  //Loop through all the markers and remove
  // console.log("marker pertama id yg dihapus : ", id);
  for (var i = 0; i < markerss.length; i++) {
    // if (markerss[i].id == id) {
      //Remove the om Map
      markerss[i].setMap(null);

      //Remove the marker from array.
      markerss.splice(i, 1);
      return;
    // }
  }
}

function DeleteMarkers(id) {
  //Loop through all the markers and remove
  // console.log("marker pertama id yg dihapus : ", id);
  for (var i = 0; i < markerss.length; i++) {
    if (markerss[i].id == id) {
      //Remove the om Map
      markerss[i].setMap(null);

      //Remove the marker from array.
      markerss.splice(i, 1);
      return;
    }
  }
}

function DeleteMarkerspertama(id) {
  //Loop through all the markers and remove
  // console.log("marker kedua id yg dihapus : ", id);
  for (var i = 0; i < markers.length; i++) {
    if (markers[i].id == id) {
      //Remove the marker from Map
      markers[i].setMap(null);

      //Remove the marker from array.
      markers.splice(i, 1);
      return;
    }
  }
}

function getVehicleByContractor(){
  var companyid        = $("#contractor").val();
  var mapsOptionsValue = "showHeatmap";
  var valuemapsoption;

  $.post("<?php echo base_url() ?>maps/vehicleByContractor", {companyid : companyid, valuemapsoption: 9999}, function(response){
    console.log("contractor onchange vehicleByContractor : ", response);
    $("#searchnopol").html("");
    var data = response.data;
    var html = "";

        html += '<option value="0">--Vehicle List</option>';
        for (var i = 0; i < data.length; i++) {
            if (companyid == 0) {
              html += '<option value="'+data[i].vehicle_no+'">'+data[i].vehicle_no+'</option>';
            }else {
              html += '<option value="'+data[i].vehicle_no+'">'+(i+1) + ". " + data[i].vehicle_no+'</option>';
            }
        }
      $("#searchnopol").html(html);
  },"json");

    if ( mapsOptionsValue == "showHeatmap") {
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);

      valuemapsoption = 1;
      jQuery("#loader2").show();

      $.post("<?php echo base_url() ?>maps/vehicleByContractorheatmap", {companyid : companyid, valuemapsoption: valuemapsoption}, function(response){
        jQuery("#loader2").hide();

        console.log("contractor onchange showHeatmap : ", response);
        var data = response.data;
        var html = "";
        var privilegecode = '<?php echo $this->sess->user_id_role; ?>';
        var user_company  = '<?php echo $this->sess->user_company; ?>';
        console.log("privilegecode : ", privilegecode);
        console.log("user_company : ", user_company);

        $("#searchnopol").html("");

          if (privilegecode == 5 || privilegecode == 6) {
            var loop = 1;
              html += '<option value="0">--Vehicle List</option>';
              for (var i = 0; i < data.length; i++) {
                var vCompany = data[i].vehicle_company;
                var vehicle_id_shareto = data[i].vehicle_id_shareto;
                if (vCompany == user_company || user_company == vehicle_id_shareto) {
                  console.log("vCompany : ", vCompany);
                  html += '<option value="'+data[i].vehicle_no+'">'+loop + ". " + data[i].vehicle_no+'</option>';
                  loop++;
                }
              }
        }else {
          html += '<option value="0">--Vehicle List</option>';
          for (var i = 0; i < data.length; i++) {
              if (companyid == 0) {
                html += '<option value="'+data[i].vehicle_no+'">'+data[i].vehicle_no+'</option>';
              }else {
                html += '<option value="'+data[i].vehicle_no+'">'+(i+1) + ". " + data[i].vehicle_no+'</option>';
              }
          }
        }
        $("#searchnopol").html(html);

            // SHOW MAPS AFTER
            heatmap.setMap(null);
            var vehicle           = response.datavehicle;
            var poolmaster        = response.poolmaster;

            var bounds            = new google.maps.LatLngBounds();
            var boundspool        = new google.maps.LatLngBounds();

            var objpoolmasterfix  = poolmaster;
            var arraypointheatmap = [];

            console.log( "vehicle : ", vehicle);

            for (var j = 0; j < response.data.length; j++) {
              // console.log("heatmap : " + j);
              DeleteMarkerspertama(vehicle[j].vehicle_device);
              DeleteMarkers(vehicle[j].vehicle_device);
              DeleteMarkerspertama(vehicle[j].vehicle_id);
              DeleteMarkers(vehicle[j].vehicle_id);
            }
            // console.log("objpoolmasterfix outofhauling: ", objpoolmasterfix);

            // $("#searchnopol").html("");
            //
            //     var htmlchangeVList = document.getElementById('searchnopol');
            //     htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List', "0");
            //     for (var i = 0; i < vehicle.length; i++) {
            //       htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
            //     }

            for (var i = 0; i < vehicle.length; i++) {
              arraypointheatmap.push(new google.maps.LatLng(vehicle[i].auto_last_lat, vehicle[i].auto_last_long));
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
          // intervalstart = setInterval(simultango, 15000);
      },"json");
    }
}

function closeWindowOnMarkerOOH(){
  $("#nopolforcheck").val("0");
  $("#nopolforhide").val("0");
  infowindowkedua.close();
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
