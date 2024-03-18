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
      background-color: green;
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
      background-color: green;
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
      background-color: green;
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
      background-color: green;
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
              <h5>DEVELOPMENT</h5>
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
              </div>

              <div class="col-md-3" id="showSearchNOutOfHauling" style="display:none;">
                <select class="form-control select2" name="forsearchoutofhauling" id="forsearchoutofhauling" onchange="forsearchoutofhauling()" style="width:100%;">
                </select>
              </div>

              <div class="col-md-3">
                <select class="form-control select2" name="mapsOptions" id="mapsOptions" onchange="mapsOptions()">
                  <option value="0">--Maps Option</option>
                  <option value="showHeatmap">Maps</option>
                  <option value="showTableMuatan1">Quick Count Data</option>
                  <!-- <option value="showTableMuatan2">Consolidated Data</option> -->
                  <option value="showTableRom">ROM</option>
                  <option value="showTablePort">PORT</option>
                  <option value="showTablePool">POOL / WS</option>
                  <option value="outofhauling">Out Of Hauling</option>
                  <option value="offlinevehicle">Offline Vehicle</option>
                  <option value="standardmaps">Maps Standard</option>
                </select>
              </div>

              <div class="col-md-3">
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
        </div> <br>
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

      <div class="card" id="cardShowMap">
        <div class="card-body">
          <div class="row">

            <div id="tableShowMuatan" style="width: 100%; height: 510px; display:none;">
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
                                Sp. Bayah
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
            </div>

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

            <div id="mapshowfix">
              <input type="hidden" id="valueMode" value="0">
                <div id="mapShow">
                   <!-- display:none; -->
                   <div id="mapsnya" class="mapsClass1"></div>
                </div>
            </div>

            <div class="col-md-4" id="realtimealertshowhide">
               <!-- style="display:none;" -->
              <div class="panel" id="panel_form">
                <header class="panel-heading panel-heading-red">
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
      // console.log("masuk");
      var html = "";

          html += '<option value="0">--Vehicle List</option>';
          html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
            html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
          html += '<?php } ?>';

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
  var infowindowkedua, infowindow, infowindow2, infowindowonsimultan, intervaloutofhauling;
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
      center: { lat: parseFloat(-3.7288), lng: parseFloat(115.6452)},
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
    var toggleButton = document.createElement("button");
    toggleButton.textContent = "BIB Maps";
    toggleButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

     toggleButton.addEventListener("click", () => {
      addoverlay(map);
    });

    // var changegradient = document.createElement("button");
    // changegradient.textContent = "Change Gradient";
    // changegradient.classList.add("custom-map-control-button");
    // map.controls[google.maps.ControlPosition.TOP_CENTER].push(changegradient);
    //
    //  changegradient.addEventListener("click", () => {
    //   changeGradient();
    // });

    intervalstart = setInterval(simultango, 15000);
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

    var companyid = $("#contractor").val();

    // console.log("simultan Started");
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
    // BIB MAPS UPDATE 18 12 2021

    // INI BUAT KOORDINAT PORTBUNATI3_SMALL.PNG
    var latlngoverlay1                = new google.maps.LatLng(-3.752973, 115.627142);
    var latlngoverlay2                = new google.maps.LatLng(-3.741050, 115.651995);
    var jalanpcs1latlng1              = new google.maps.LatLng(-3.742245, 115.634781);
    var jalanpcs1latlng2              = new google.maps.LatLng(-3.731234, 115.658683);
    var jalanpcs2latlng1                = new google.maps.LatLng(-3.731293, 115.635119);
    var jalanpcs2latlng2                = new google.maps.LatLng(-3.719973, 115.658875);
    var jalanpcs3latlng1                = new google.maps.LatLng(-3.720716, 115.627392);
    var jalanpcs3latlng2                = new google.maps.LatLng(-3.709085, 115.650982);
    var jalanpcs4latlng1                = new google.maps.LatLng(-3.709082, 115.626623);
    var jalanpcs4latlng2                = new google.maps.LatLng(-3.696086, 115.650399);
    var jalanpcs5latlng1                = new google.maps.LatLng(-3.698382, 115.628647);
    var jalanpcs5latlng2                = new google.maps.LatLng(-3.686875, 115.650329);
    var jalanpcs5nikunglatlng1          = new google.maps.LatLng(-3.690207, 115.640326);
    var jalanpcs5nikunglatlng2          = new google.maps.LatLng(-3.684617, 115.651178);
    var jalanpcs5nikungpcs3barulatlng1  = new google.maps.LatLng(-3.685194, 115.642723);
    var jalanpcs5nikungpcs3barulatlng2  = new google.maps.LatLng(-3.679627, 115.653479);

    var jalanpcs5nikungpcs4barulatlng1  = new google.maps.LatLng(-3.679623, 115.643087);
    var jalanpcs5nikungpcs4barulatlng2  = new google.maps.LatLng(-3.672130, 115.658138);

    var jalanpcspatahancroplatlng1      = new google.maps.LatLng(-3.680263, 115.646667);
    var jalanpcspatahancroplatlng2      = new google.maps.LatLng(-3.675736, 115.651107);

    var jalanpcs5nikungpcs5barulatlng1  = new google.maps.LatLng(-3.672140, 115.643943);
    var jalanpcs5nikungpcs5barulatlng2  = new google.maps.LatLng(-3.665922, 115.657922);



    var jalanpcs6latlng1                = new google.maps.LatLng(-3.666083, 115.644821);
    var jalanpcs6latlng2                = new google.maps.LatLng(-3.661015, 115.656904);
    var jalanpcs7latlng1                = new google.maps.LatLng(-3.661060, 115.644165);
    var jalanpcs7latlng2                = new google.maps.LatLng(-3.653857, 115.655901);
    var jalanpcs8latlng1                = new google.maps.LatLng(-3.654903, 115.641802);
    var jalanpcs8latlng2                = new google.maps.LatLng(-3.648403, 115.653267);
    var jalanpcs9latlng1                = new google.maps.LatLng(-3.648949, 115.642282);
    var jalanpcs9latlng2                = new google.maps.LatLng(-3.642311, 115.653412);
    var jalanpcs10latlng1               = new google.maps.LatLng(-3.642658, 115.646624);
    var jalanpcs10latlng2               = new google.maps.LatLng(-3.636349, 115.655885);
    var jalanpcstekukankananataslatlng1 = new google.maps.LatLng(-3.642287, 115.647807);
    var jalanpcstekukankananataslatlng2 = new google.maps.LatLng(-3.624354, 115.659467);
    var jalanpcs15latlng1               = new google.maps.LatLng(-3.624377, 115.645157);
    var jalanpcs15latlng2               = new google.maps.LatLng(-3.613030, 115.657190);
    var jalanpcs16latlng1               = new google.maps.LatLng(-3.615390, 115.640438);
    var jalanpcs16latlng2               = new google.maps.LatLng(-3.604488, 115.664935);
    var jalanpcs17latlng1               = new google.maps.LatLng(-3.605948, 115.650892);
    var jalanpcs17latlng2               = new google.maps.LatLng(-3.591340, 115.675094);
    var jalanpcs18latlng1               = new google.maps.LatLng(-3.591364, 115.651680);
    var jalanpcs18latlng2               = new google.maps.LatLng(-3.579234, 115.666260);
    var jalanpcs19latlng1               = new google.maps.LatLng(-3.579299, 115.652807);
    var jalanpcs19latlng2               = new google.maps.LatLng(-3.574495, 115.659479);
    var jalanpcs20latlng1               = new google.maps.LatLng(-3.574582, 115.653795);
    var jalanpcs20latlng2               = new google.maps.LatLng(-3.571493, 115.657894);
    var jalanpcs21latlng1               = new google.maps.LatLng(-3.571526, 115.652658);
    var jalanpcs21latlng2               = new google.maps.LatLng(-3.568056, 115.656361);
    var jalanpcs22latlng1               = new google.maps.LatLng(-3.572094, 115.649348);
    var jalanpcs22latlng2               = new google.maps.LatLng(-3.566102, 115.657244);
    var jalanpcs23latlng1               = new google.maps.LatLng(-3.566103, 115.645197);
    var jalanpcs23latlng2               = new google.maps.LatLng(-3.558789, 115.661364);
    var jalanpcs24latlng1               = new google.maps.LatLng(-3.559233, 115.645378);
    var jalanpcs24latlng2               = new google.maps.LatLng(-3.554506, 115.656520);
    var jalanpcs25latlng1               = new google.maps.LatLng(-3.554939, 115.642562);
    var jalanpcs25latlng2               = new google.maps.LatLng(-3.547679, 115.657653);
    var jalanpcs26latlng1               = new google.maps.LatLng(-3.547848, 115.643332);
    var jalanpcs26latlng2               = new google.maps.LatLng(-3.540213, 115.657087);
    var jalanpcs27latlng1               = new google.maps.LatLng(-3.540904, 115.643003);
    var jalanpcs27latlng2               = new google.maps.LatLng(-3.535258, 115.656237);
    var jalanpcs28latlng1               = new google.maps.LatLng(-3.535323, 115.643353);
    var jalanpcs28latlng2               = new google.maps.LatLng(-3.527523, 115.656180);
    var jalanpcs29latlng1               = new google.maps.LatLng(-3.527815, 115.644625);
    var jalanpcs29latlng2               = new google.maps.LatLng(-3.523184, 115.654230);
    var jalanpcs30latlng1               = new google.maps.LatLng(-3.523197, 115.640709);
    var jalanpcs30latlng2               = new google.maps.LatLng(-3.516298, 115.655688);
    var jalanpcs32latlng1               = new google.maps.LatLng(-3.521122, 115.629392);
    var jalanpcs32latlng2               = new google.maps.LatLng(-3.514110, 115.644582);
    var jalanpcs33latlng1               = new google.maps.LatLng(-3.521091, 115.615832);
    var jalanpcs33latlng2               = new google.maps.LatLng(-3.514375, 115.629674);
    var jalanpcs34latlng1               = new google.maps.LatLng(-3.521108, 115.606516);
    var jalanpcs34latlng2               = new google.maps.LatLng(-3.515529, 115.620556);
    var jalanpcs36latlng1               = new google.maps.LatLng(-3.517714, 115.633010);
    var jalanpcs36latlng2               = new google.maps.LatLng(-3.506656, 115.648205);

    var imageportfix                    = "<?php echo base_url()?>assets/images/portbunati3_small.png";
    var imagejalanpcs1fix               = "<?php echo base_url()?>assets/images/pcs_1_new.png";
    var imagejalanpcs2fix              = "<?php echo base_url()?>assets/images/pcs_2_new.png";
    var imagejalanpcs3fix               = "<?php echo base_url()?>assets/images/pcs_3_new.png";
    var imagejalanpcs4fix               = "<?php echo base_url()?>assets/images/pcs_4_new.png";
    var imagejalanpcs5fix               = "<?php echo base_url()?>assets/images/pcs_5_new.png";
    var imagejalanpcs5nikungfix         = "<?php echo base_url()?>assets/images/pcs_2_baru.png";
    var imagejalanpcs5nikungpcs3barufix = "<?php echo base_url()?>assets/images/pcs_3_baru.png";
    var imagejalanpcs5nikungpcs4barufix = "<?php echo base_url()?>assets/images/pcs_4_baru.png";
    var imagejalanpcs5nikungpcs5barufix = "<?php echo base_url()?>assets/images/pcs_5_baru.png";

    var imagejalanpcspatahancrop5barufix = "<?php echo base_url()?>assets/images/patahan_tikungan5_baru_nobackground.png";

    var imagejalanpcs6fix                = "<?php echo base_url()?>assets/images/pcs_6_baru.png";
    var imagejalanpcs7fix                = "<?php echo base_url()?>assets/images/pcs_7_barufix.png";
    var imagejalanpcs8fix                = "<?php echo base_url()?>assets/images/pcs_8_baru.png";
    var imagejalanpcs9fix                = "<?php echo base_url()?>assets/images/pcs_9_baru.png";
    var imagejalanpcs10fix               = "<?php echo base_url()?>assets/images/pcs_10_baru.png";
    var imagejalanpcstekukankananatasfix = "<?php echo base_url()?>assets/images/pcs_tekukan_kanan_atas.png";
    var imagejalanpcs15fix               = "<?php echo base_url()?>assets/images/pcs_15_baru.png";
    var imagejalanpcs16fix               = "<?php echo base_url()?>assets/images/pcs_16_baru.png";
    var imagejalanpcs17fix               = "<?php echo base_url()?>assets/images/pcs_17_baru.png";
    var imagejalanpcs18fix               = "<?php echo base_url()?>assets/images/pcs_18_baru.png";
    var imagejalanpcs19fix               = "<?php echo base_url()?>assets/images/pcs_19_baru.png";
    var imagejalanpcs20fix               = "<?php echo base_url()?>assets/images/pcs_20_baru.png";
    var imagejalanpcs21fix               = "<?php echo base_url()?>assets/images/pcs_21_baru.png";
    var imagejalanpcs22fix               = "<?php echo base_url()?>assets/images/pcs_22_baru.png";
    var imagejalanpcs23fix               = "<?php echo base_url()?>assets/images/pcs_23_baru.png";
    var imagejalanpcs24fix               = "<?php echo base_url()?>assets/images/pcs_24_baru.png";
    var imagejalanpcs25fix               = "<?php echo base_url()?>assets/images/pcs_25_baru.png";
    var imagejalanpcs26fix               = "<?php echo base_url()?>assets/images/pcs_26_baru.png";
    var imagejalanpcs27fix               = "<?php echo base_url()?>assets/images/pcs_27_baru2.png";
    var imagejalanpcs28fix               = "<?php echo base_url()?>assets/images/pcs_28_baru.png";
    var imagejalanpcs29fix               = "<?php echo base_url()?>assets/images/pcs_29_baru.png";
    var imagejalanpcs30fix               = "<?php echo base_url()?>assets/images/pcs_30_baru.png";
    var imagejalanpcs32fix               = "<?php echo base_url()?>assets/images/pcs_32_baru.png";
    var imagejalanpcs33fix               = "<?php echo base_url()?>assets/images/pcs_33_baru.png";
    var imagejalanpcs34fix               = "<?php echo base_url()?>assets/images/pcs_34_baru.png";
    var imagejalanpcs36fix               = "<?php echo base_url()?>assets/images/pcs_36_baru.png";

    var imageport                  = new google.maps.LatLngBounds(latlngoverlay1, latlngoverlay2);
    var imagejalanpcs1             = new google.maps.LatLngBounds(jalanpcs1latlng1, jalanpcs1latlng2);
    var imagejalanpcs2               = new google.maps.LatLngBounds(jalanpcs2latlng1, jalanpcs2latlng2);
    var imagejalanpcs3               = new google.maps.LatLngBounds(jalanpcs3latlng1, jalanpcs3latlng2);
    var imagejalanpcs4               = new google.maps.LatLngBounds(jalanpcs4latlng1, jalanpcs4latlng2);
    var imagejalanpcs5               = new google.maps.LatLngBounds(jalanpcs5latlng1, jalanpcs5latlng2);
    var imagejalanpcs5nikung         = new google.maps.LatLngBounds(jalanpcs5nikunglatlng1, jalanpcs5nikunglatlng2);
    var imagejalanpcs5nikungpcs3baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs3barulatlng1, jalanpcs5nikungpcs3barulatlng2);
    var imagejalanpcs5nikungpcs4baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs4barulatlng1, jalanpcs5nikungpcs4barulatlng2);
    var imagejalanpcs5nikungpcs5baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs5barulatlng1, jalanpcs5nikungpcs5barulatlng2);

    var imagejalanpcspatahancrop    = new google.maps.LatLngBounds(jalanpcspatahancroplatlng1, jalanpcspatahancroplatlng2);


    var imagejalanpcs6                = new google.maps.LatLngBounds(jalanpcs6latlng1, jalanpcs6latlng2);
    var imagejalanpcs7                = new google.maps.LatLngBounds(jalanpcs7latlng1, jalanpcs7latlng2);
    var imagejalanpcs8                = new google.maps.LatLngBounds(jalanpcs8latlng1, jalanpcs8latlng2);
    var imagejalanpcs9                = new google.maps.LatLngBounds(jalanpcs9latlng1, jalanpcs9latlng2);
    var imagejalanpcs10               = new google.maps.LatLngBounds(jalanpcs10latlng1, jalanpcs10latlng2);
    var imagejalanpcstekukankananatas = new google.maps.LatLngBounds(jalanpcstekukankananataslatlng1, jalanpcstekukankananataslatlng2);
    var imagejalanpcs15               = new google.maps.LatLngBounds(jalanpcs15latlng1, jalanpcs15latlng2);
    var imagejalanpcs16               = new google.maps.LatLngBounds(jalanpcs16latlng1, jalanpcs16latlng2);
    var imagejalanpcs17               = new google.maps.LatLngBounds(jalanpcs17latlng1, jalanpcs17latlng2);
    var imagejalanpcs18               = new google.maps.LatLngBounds(jalanpcs18latlng1, jalanpcs18latlng2);
    var imagejalanpcs19               = new google.maps.LatLngBounds(jalanpcs19latlng1, jalanpcs19latlng2);
    var imagejalanpcs20               = new google.maps.LatLngBounds(jalanpcs20latlng1, jalanpcs20latlng2);
    var imagejalanpcs21               = new google.maps.LatLngBounds(jalanpcs21latlng1, jalanpcs21latlng2);
    var imagejalanpcs22               = new google.maps.LatLngBounds(jalanpcs22latlng1, jalanpcs22latlng2);
    var imagejalanpcs23               = new google.maps.LatLngBounds(jalanpcs23latlng1, jalanpcs23latlng2);
    var imagejalanpcs24               = new google.maps.LatLngBounds(jalanpcs24latlng1, jalanpcs24latlng2);
    var imagejalanpcs25               = new google.maps.LatLngBounds(jalanpcs25latlng1, jalanpcs25latlng2);
    var imagejalanpcs26               = new google.maps.LatLngBounds(jalanpcs26latlng1, jalanpcs26latlng2);
    var imagejalanpcs27               = new google.maps.LatLngBounds(jalanpcs27latlng1, jalanpcs27latlng2);
    var imagejalanpcs28               = new google.maps.LatLngBounds(jalanpcs28latlng1, jalanpcs28latlng2);
    var imagejalanpcs29               = new google.maps.LatLngBounds(jalanpcs29latlng1, jalanpcs29latlng2);
    var imagejalanpcs30               = new google.maps.LatLngBounds(jalanpcs30latlng1, jalanpcs30latlng2);
    var imagejalanpcs32               = new google.maps.LatLngBounds(jalanpcs32latlng1, jalanpcs32latlng2);
    var imagejalanpcs33               = new google.maps.LatLngBounds(jalanpcs33latlng1, jalanpcs33latlng2);
    var imagejalanpcs34               = new google.maps.LatLngBounds(jalanpcs34latlng1, jalanpcs34latlng2);
    var imagejalanpcs36               = new google.maps.LatLngBounds(jalanpcs36latlng1, jalanpcs36latlng2);

    // Overlay                       = new google.maps.GroundOverlay(imageportfix, imageport);
    Overlayjalanpcs_1             = new google.maps.GroundOverlay(imagejalanpcs1fix, imagejalanpcs1);
    Overlayjalanpcs_2               = new google.maps.GroundOverlay(imagejalanpcs2fix, imagejalanpcs2);
    Overlayjalanpcs_3               = new google.maps.GroundOverlay(imagejalanpcs3fix, imagejalanpcs3);
    Overlayjalanpcs_4               = new google.maps.GroundOverlay(imagejalanpcs4fix, imagejalanpcs4);
    Overlayjalanpcs_5               = new google.maps.GroundOverlay(imagejalanpcs5fix, imagejalanpcs5);
    Overlayjalanpcs_5nikung         = new google.maps.GroundOverlay(imagejalanpcs5nikungfix, imagejalanpcs5nikung);
    Overlayjalanpcs_5nikungpcs3baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs3barufix, imagejalanpcs5nikungpcs3baru);
    Overlayjalanpcs_5nikungpcs4baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs4barufix, imagejalanpcs5nikungpcs4baru);
    Overlayjalanpcs_5nikungpcs5baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs5barufix, imagejalanpcs5nikungpcs5baru);

    Overlayjalanpcs_patahancrop = new google.maps.GroundOverlay(imagejalanpcspatahancrop5barufix, imagejalanpcspatahancrop);


    Overlayjalanpcs_6                = new google.maps.GroundOverlay(imagejalanpcs6fix, imagejalanpcs6);
    Overlayjalanpcs_7                = new google.maps.GroundOverlay(imagejalanpcs7fix, imagejalanpcs7);
    Overlayjalanpcs_8                = new google.maps.GroundOverlay(imagejalanpcs8fix, imagejalanpcs8);
    Overlayjalanpcs_9                = new google.maps.GroundOverlay(imagejalanpcs9fix, imagejalanpcs9);
    Overlayjalanpcs_10               = new google.maps.GroundOverlay(imagejalanpcs10fix, imagejalanpcs10);
    // ini nanti Overlayjalanpcs_11               = new google.maps.GroundOverlay(imagejalanpcs11fix, imagejalanpcs11);
    // ini nanti Overlayjalanpcs_12               = new google.maps.GroundOverlay(imagejalanpcs12fix, imagejalanpcs12);
    Overlayjalanpcs_tekukankananatas = new google.maps.GroundOverlay(imagejalanpcstekukankananatasfix, imagejalanpcstekukankananatas);
    Overlayjalanpcs_15               = new google.maps.GroundOverlay(imagejalanpcs15fix, imagejalanpcs15);
    Overlayjalanpcs_16               = new google.maps.GroundOverlay(imagejalanpcs16fix, imagejalanpcs16);
    Overlayjalanpcs_17               = new google.maps.GroundOverlay(imagejalanpcs17fix, imagejalanpcs17);
    Overlayjalanpcs_18               = new google.maps.GroundOverlay(imagejalanpcs18fix, imagejalanpcs18);
    Overlayjalanpcs_19               = new google.maps.GroundOverlay(imagejalanpcs19fix, imagejalanpcs19);
    Overlayjalanpcs_20               = new google.maps.GroundOverlay(imagejalanpcs20fix, imagejalanpcs20);
    Overlayjalanpcs_21               = new google.maps.GroundOverlay(imagejalanpcs21fix, imagejalanpcs21);
    Overlayjalanpcs_22               = new google.maps.GroundOverlay(imagejalanpcs22fix, imagejalanpcs22);
    Overlayjalanpcs_23               = new google.maps.GroundOverlay(imagejalanpcs23fix, imagejalanpcs23);
    Overlayjalanpcs_24               = new google.maps.GroundOverlay(imagejalanpcs24fix, imagejalanpcs24);
    Overlayjalanpcs_25               = new google.maps.GroundOverlay(imagejalanpcs25fix, imagejalanpcs25);
    Overlayjalanpcs_26               = new google.maps.GroundOverlay(imagejalanpcs26fix, imagejalanpcs26);
    Overlayjalanpcs_27               = new google.maps.GroundOverlay(imagejalanpcs27fix, imagejalanpcs27);
    Overlayjalanpcs_28               = new google.maps.GroundOverlay(imagejalanpcs28fix, imagejalanpcs28);
    Overlayjalanpcs_29               = new google.maps.GroundOverlay(imagejalanpcs29fix, imagejalanpcs29);
    Overlayjalanpcs_30               = new google.maps.GroundOverlay(imagejalanpcs30fix, imagejalanpcs30);
    Overlayjalanpcs_32               = new google.maps.GroundOverlay(imagejalanpcs32fix, imagejalanpcs32);
    Overlayjalanpcs_33               = new google.maps.GroundOverlay(imagejalanpcs33fix, imagejalanpcs33);
    Overlayjalanpcs_34               = new google.maps.GroundOverlay(imagejalanpcs34fix, imagejalanpcs34);
    Overlayjalanpcs_36               = new google.maps.GroundOverlay(imagejalanpcs36fix, imagejalanpcs36);

    // Overlay.setMap(map);
    Overlayjalanpcs_1.setMap(map);
    Overlayjalanpcs_2.setMap(map);
    Overlayjalanpcs_3.setMap(map);
    Overlayjalanpcs_4.setMap(map);
    Overlayjalanpcs_5.setMap(map);
    Overlayjalanpcs_5nikung.setMap(map);
    Overlayjalanpcs_5nikungpcs3baru.setMap(map);
    Overlayjalanpcs_5nikungpcs4baru.setMap(map);
    Overlayjalanpcs_5nikungpcs5baru.setMap(map);
    Overlayjalanpcs_patahancrop.setMap(map);
    Overlayjalanpcs_6.setMap(map);
    Overlayjalanpcs_7.setMap(map);
    Overlayjalanpcs_8.setMap(map);
    Overlayjalanpcs_9.setMap(map);
    Overlayjalanpcs_10.setMap(map);
    Overlayjalanpcs_tekukankananatas.setMap(map);
    Overlayjalanpcs_15.setMap(map);
    Overlayjalanpcs_16.setMap(map);
    Overlayjalanpcs_17.setMap(map);
    Overlayjalanpcs_18.setMap(map);
    Overlayjalanpcs_19.setMap(map);
    Overlayjalanpcs_20.setMap(map);
    Overlayjalanpcs_21.setMap(map);
    Overlayjalanpcs_22.setMap(map);
    Overlayjalanpcs_23.setMap(map);
    Overlayjalanpcs_24.setMap(map);
    Overlayjalanpcs_25.setMap(map);
    Overlayjalanpcs_26.setMap(map);
    Overlayjalanpcs_27.setMap(map);
    Overlayjalanpcs_28.setMap(map);
    Overlayjalanpcs_29.setMap(map);
    // Overlayjalanpcs_30.setMap(map);
    // Overlayjalanpcs_32.setMap(map);
    // Overlayjalanpcs_33.setMap(map);
    // Overlayjalanpcs_34.setMap(map);
    // Overlayjalanpcs_36.setMap(map);

    // ARRAY overlay
    // overlaysarray.push(Overlay);
    overlaysarray.push(Overlayjalanpcs_1);
    overlaysarray.push(Overlayjalanpcs_2);
    overlaysarray.push(Overlayjalanpcs_3);
    overlaysarray.push(Overlayjalanpcs_4);
    overlaysarray.push(Overlayjalanpcs_5);
    overlaysarray.push(Overlayjalanpcs_5nikung);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs3baru);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs4baru);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs5baru);
    overlaysarray.push(Overlayjalanpcs_patahancrop);


    overlaysarray.push(Overlayjalanpcs_6);
    overlaysarray.push(Overlayjalanpcs_7);
    overlaysarray.push(Overlayjalanpcs_8);
    overlaysarray.push(Overlayjalanpcs_9);
    overlaysarray.push(Overlayjalanpcs_10);
    overlaysarray.push(Overlayjalanpcs_tekukankananatas);
    overlaysarray.push(Overlayjalanpcs_15);
    overlaysarray.push(Overlayjalanpcs_16);
    overlaysarray.push(Overlayjalanpcs_17);
    overlaysarray.push(Overlayjalanpcs_18);
    overlaysarray.push(Overlayjalanpcs_19);
    overlaysarray.push(Overlayjalanpcs_20);
    overlaysarray.push(Overlayjalanpcs_21);
    overlaysarray.push(Overlayjalanpcs_22);
    overlaysarray.push(Overlayjalanpcs_23);
    overlaysarray.push(Overlayjalanpcs_24);
    overlaysarray.push(Overlayjalanpcs_25);
    overlaysarray.push(Overlayjalanpcs_26);
    overlaysarray.push(Overlayjalanpcs_27);
    overlaysarray.push(Overlayjalanpcs_28);
    overlaysarray.push(Overlayjalanpcs_29);
    // overlaysarray.push(Overlayjalanpcs_30);
    // overlaysarray.push(Overlayjalanpcs_32);
    // overlaysarray.push(Overlayjalanpcs_33);
    // overlaysarray.push(Overlayjalanpcs_34);
    // overlaysarray.push(Overlayjalanpcs_36);
  }else {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'hidden') ? google.maps.MapTypeId.satellite : 'hidden');

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

var intervalalert = setInterval(dataalert, 25000);
var lasttimealert = 0;
var limitalert    = 10;

function dataalert(){
  $.post("<?php echo base_url() ?>securityevidence/realtimealert", {lasttimealert : lasttimealert, limitalert : limitalert}, function(response){
    // localStorage.setItem(obj[looplasttime].vehicle_device, obj[looplasttime].auto_last_update);
    console.log("response : ", response);
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
                realtimealertarraysummary.push(data[i]);
              }
            }
          }
      }

      var realtimealertarraysummaryreverse       = realtimealertarraysummary.reverse();

      var htmlsummary = "";
      for (var i = 0; i < realtimealertarraysummaryreverse.length-1; i++) {
        // htmlsummary += '<span style="font-size:12px; color:green;">'+ realtimealertarraysummaryreverse[i].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ realtimealertarraysummaryreverse[i].gps_alert +'</span> <span style="font-size:12px;">'+ realtimealertarraysummaryreverse[i].gps_time +'</span> <span style="font-size:12px; color:black;">'+ 'Geofence : '+ realtimealertarraysummaryreverse[i].geofence +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+realtimealertarraysummaryreverse[i].gps_latitude_real+','+realtimealertarraysummaryreverse[i].gps_longitude_real+'" target="_blank">'+ " "+realtimealertarraysummaryreverse[i].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ realtimealertarraysummaryreverse[i].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ realtimealertarraysummaryreverse[i].gps_speed_limit +' Kph </span> </br>';

        htmlsummary += '<span style="font-size:11px; color:green;" onclik="forsearchfromalert("'+realtimealertarraysummaryreverse[i].vehicle_no+'")">'+ realtimealertarraysummaryreverse[i].vehicle_no +'</span> <span style="font-size:11px; color:red;">'+ realtimealertarraysummaryreverse[i].gps_alert +'</span> <span style="font-size:11px;">'+ realtimealertarraysummaryreverse[i].gps_time +'</span> </br> <a style="font-size:11px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+realtimealertarraysummaryreverse[i].gps_latitude_real+','+realtimealertarraysummaryreverse[i].gps_longitude_real+'" target="_blank">'+ " "+realtimealertarraysummaryreverse[i].position +' </a> <span style="font-size:11px; color:black;">'+ 'Speed : '+ realtimealertarraysummaryreverse[i].gps_speed +
        ' Kph </span> <span style="font-size:11px; color:red;">'+ 'Limit : '+ realtimealertarraysummaryreverse[i].gps_speed_limit +' Kph </span> '+
        '<span style="font-size:11px; color:black;">'+ realtimealertarraysummaryreverse[i].jalur_name +'</span> </br><hr>';
      }
      $("#summaryalertcontent").html(htmlsummary);

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

      // lasttimealert = data[0].gps_time;
      if (soundisactive == 1) {
        playsound();
      }
    }
  }, "json");

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

      var data = {key : deviceid};

      $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
        console.log("ini respon pencarian : ", response);
        if (response.code == 400) {
          alert("Data tidak ditemukan");
        }else {
          DeleteMarkers(response[0].vehicle_device);
          DeleteMarkerspertama(response[0].vehicle_device);
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
                titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
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
                titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
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
                titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
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
      $("#tableShowOutOfHauling").hide();
      $("#tableShowRom").hide();
      $("#valueMode").val(0);
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

function mapsOptions(){
  var mapsOptionsValue = $("#mapsOptions").val();
  console.log("mapsOptionsValue : ", mapsOptionsValue);
    if (mapsOptionsValue == "showHeatmap") {
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);
      showHeatmap();
    }else if (mapsOptionsValue == "showTableMuatan1") {
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalstart);
      showTableMuatan();
    }else if (mapsOptionsValue == "showTableRom") {
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      showTableRom();
    }else if (mapsOptionsValue == "showTablePort") {
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      showTablePort();
    }else if (mapsOptionsValue == "showTablePool") {
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      showTablePool();
    }else if (mapsOptionsValue == "outofhauling") {
      clearInterval(intervalmapsstandard);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      showOutOfHauling();
    }else if (mapsOptionsValue == "offlinevehicle") {
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      showOfflineVehicle();
    }else if (mapsOptionsValue == "standardmaps") {
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      standardMaps();
    }else {
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      showHeatmap();
    }
}

function showHeatmap(){
  clearInterval(intervalmapsstandard);
  clearInterval(intervaloutofhauling);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalpoollist);
  clearInterval(intervalportlist);
  clearInterval(intervalromlist);
  clearInterval(intervalkmlist);
  $("#mapshowfix").removeClass();
  $("#mapshowfix").addClass('col-md-8');
  $("#mapsnya").removeClass();
  $("#mapsnya").addClass('mapsClass1');
  $("#cardShowMap").show();
  $("#mapShow").show();
  $("#realtimealertshowhide").show();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").show();
  $("#showSearchNOutOfHauling").hide();
  // soundisactive = 0;
  jQuery("#loader2").show();
  var companyid = $("#contractor").val();
  $.post("<?php echo base_url() ?>maps/showmapsafter", {companyid : companyid}, function(response){
    jQuery("#loader2").hide();

    console.log("response : ", response);
    // markers = [];
    heatmap.setMap(null);

    // SHOW MAPS AFTER
    var vehicle           = response.data;
    var poolmaster        = response.poolmaster;

    var bounds            = new google.maps.LatLngBounds();
    var boundspool        = new google.maps.LatLngBounds();

    var objpoolmasterfix  = poolmaster;
    var arraypointheatmap = [];

    console.log("showmapsafter : ", vehicle);

    for (var j = 0; j < response.data.length; j++) {
      // console.log("heatmap : " + j);
      DeleteMarkerspertama(vehicle[j].vehicle_device);
      DeleteMarkers(vehicle[j].vehicle_device);
      DeleteMarkerspertama(vehicle[j].vehicle_id);
      DeleteMarkers(vehicle[j].vehicle_id);
    }
    // console.log("objpoolmasterfix outofhauling: ", objpoolmasterfix);

    $("#searchnopol").html("");

        var htmlchangeVList = document.getElementById('searchnopol');
        htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List', "0");
        for (var i = 0; i < vehicle.length; i++) {
          htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
        }

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
    intervalstart = setInterval(simultango, 15000);
  },"json");
}

function showTableMuatan(){
  $("#cardShowMap").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#tableShowMuatan").show();
  soundisactive = 0;
  var companyid = $("#contractor").val();
  jQuery("#loader2").show();

  $.post("<?php echo base_url() ?>maps/km_quickcount_new", {companyid : companyid}, function(response){
    jQuery("#loader2").hide();
        console.log("response : ", response);
        var datamuatan         = response.dataMuatan;
        var datakosongan       = response.dataKosongan;
        var datafixlimitperkm  = response.datafixlimitperkm;
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

        // console.log("arraydatamuatan : ", arraydatamuatan);
        // console.log("arraydatakosongan : ", arraydatakosongan);

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
            for (var i = 0; i < sizekosongan; i++) {
              var kmlist         = arraydatakosongan[i][0];
              var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
              totalKosongan += jumlahfix;

              if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
                $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
              }else if(jumlahfix >= top_limit){
                $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
              }else {
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

            $("#port_bib_muatan").html(portbibmuatan);
            $("#port_bir_muatan").html(portbirmuatan);

            if (portbibmuatan >= middle_limit && portbibmuatan < top_limit) {
              $("#port_bib_muatan").addClass('timeline-badge5 btn btn-warning btn-md btn-circle');
            }else if(portbibmuatan >= top_limit){
              $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
            }else {
              $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
            }

            if (portbirmuatan >= middle_limit && portbirmuatan < top_limit) {
              $("#port_bir_muatan").addClass('timeline-badge7 btn btn-warning btn-md btn-circle');
            }else if(portbirmuatan >= top_limit){
              $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
            }else {
              $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
            }
          }

          // KM 2 - 30 MUATAN
          for (var i = 0; i < sizemuatan; i++) {
            var kmlist    = arraydatamuatan[i][0];
            var jumlahfix = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
            totalMuatan += jumlahfix;

            if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
            }else if(jumlahfix >= top_limit){
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
            }else {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
            }

            $("#vehicleonmuatan"+i).html(jumlahfix);
          }

            $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2) + " || Last Refresh : " + response.lastcheck) ;
            $("#lastupdateconsolidated").show();
            intervalkmlist = setInterval(km_quickcount_simultan, 35000);
      },"json");
}

function km_quickcount_simultan(){
  clearInterval(intervalmapsstandard);
  clearInterval(intervaloutofhauling);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalpoollist);
  clearInterval(intervalportlist);
  clearInterval(intervalromlist);
  clearInterval(intervalkmlist);
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#tableShowMuatan").show();
  soundisactive = 0;

  var companyid = $("#contractor").val();
  jQuery("#loader2").show();

  $.post("<?php echo base_url() ?>maps/km_quickcount_new", {companyid : companyid}, function(response){
    jQuery("#loader2").hide();
    console.log("response : ", response);
    var datamuatan         = response.dataMuatan;
    var datakosongan       = response.dataKosongan;
    var datafixlimitperkm  = response.datafixlimitperkm;
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

    // console.log("arraydatamuatan : ", arraydatamuatan);
    // console.log("arraydatakosongan : ", arraydatakosongan);

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
        for (var i = 0; i < sizekosongan; i++) {
          var kmlist         = arraydatakosongan[i][0];
          var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
          totalKosongan += jumlahfix;

          if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
          }else if(jumlahfix >= top_limit){
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
          }else {
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

        $("#port_bib_muatan").html(portbibmuatan);
        $("#port_bir_muatan").html(portbirmuatan);

        if (portbibmuatan >= middle_limit && portbibmuatan < top_limit) {
          $("#port_bib_muatan").addClass('timeline-badge5 btn btn-warning btn-md btn-circle');
        }else if(portbibmuatan >= top_limit){
          $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
        }else {
          $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
        }

        if (portbirmuatan >= middle_limit && portbirmuatan < top_limit) {
          $("#port_bir_muatan").addClass('timeline-badge7 btn btn-warning btn-md btn-circle');
        }else if(portbirmuatan >= top_limit){
          $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
        }else {
          $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
        }
      }

      // KM 2 - 30 MUATAN
      for (var i = 0; i < sizemuatan; i++) {
        var kmlist    = arraydatamuatan[i][0];
        var jumlahfix = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
        totalMuatan += jumlahfix;

        if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
          $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
          $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
        }else if(jumlahfix >= top_limit){
          $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
          $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
        }else {
          $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
          $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
        }

        $("#vehicleonmuatan"+i).html(jumlahfix);
      }

        $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2) + " || Last Refresh : " + response.lastcheck) ;
        $("#lastupdateconsolidated").show();
        intervalkmlist = setInterval(km_quickcount_simultan, 35000);
  },"json");
}

function listVehicleOnKm(idkm){
  console.log("idkm : ", idkm);
  $.post("<?php echo base_url() ?>maps/getlistinkm", {idkm : idkm}, function(response){
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
            htmlkosongan += '<th align="center">Engine</th>';
            htmlkosongan += '<th align="center">Speed (Kph)</th>';
            htmlkosongan += '<th>Coord</th>';
          htmlkosongan += '</tr>';
        htmlkosongan += '</thead>';
      for (var i = 0; i < dataKosonganfix.length; i++) {
          htmlkosongan += '<tr>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].vehicle_no+ " " +dataKosonganfix[i].vehicle_name+'</span>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_positionfix+ '</span>';
            htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_engine+'</span>';
            htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_speed+'</span>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_lat+ "," +dataKosonganfix[i].auto_last_long+'</span>';
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
            htmlmuatan += '<th align="center">Engine</th>';
            htmlmuatan += '<th align="center">Speed (Kph)</th>';
            htmlmuatan += '<th>Coord</th>';
          htmlmuatan += '</tr>';
        htmlmuatan += '</thead>';
      for (var i = 0; i < dataMuatanfix.length; i++) {
          htmlmuatan += '<tr>';
            htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
            htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].vehicle_no+ " " +dataMuatanfix[i].vehicle_name+'</span>';
            htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_positionfix+'</span>';
            htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_engine+'</span>';
            htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_speed+'</span>';
            htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_lat+ "," +dataMuatanfix[i].auto_last_long+'</span>';
          htmlmuatan += '</tr>';
      }
      htmlmuatan += '</table>';
      $("#modalStateContentMuatan").html(htmlmuatan);
    }

      modalKmFromMasterData('modalKmListQuickCount');

  }, "json");
}

function showTableRom(){
  $("#mapShow").hide();
  $("#cardShowMap").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").show();
  $("#showSearchNOutOfHauling").hide();
  $("#tableShowRom").show();
  soundisactive = 0;
  var limittengahrom = 1000;
  var limitatasrom   = 1000;
  var companyid = $("#contractor").val();

  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:3, companyid : companyid}, function(response){
    // console.log("response : ", response);
    jQuery("#loader2").hide();
    // console.log("response : ", response);
    $("#contentrom").html("");
    var datainrom        = Object.entries(response.data);
    var totaldata        = datainrom.length;
    var allStreet        = response.allstreet;
    var jumlahtotalInRom = 0;

    var htmlrom          = "";
    // console.log("allStreet : ", allStreet);
    // console.log("datainrom : ", datainrom);
    // console.log("totaldata : ", totaldata);

    for (var i = 0; i < totaldata; i++) {
      jumlahtotalInRom += datainrom[i][1];
      htmlrom += '<div class="col-xl-3 col-md-6 col-12" onclick="listVehicleOnRom('+allStreet[i].street_id+')" style="cursor : pointer;">';
        htmlrom += '<div class="info-box bg-blue">';
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
    intervalromlist = setInterval(romlist_simultan, 15000);
  },"json");
}

function romlist_simultan(){
  clearInterval(intervalmapsstandard);
  clearInterval(intervaloutofhauling);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalpoollist);
  clearInterval(intervalportlist);
  clearInterval(intervalkmlist);
  clearInterval(intervalstart);

  var companyid        = $("#contractor").val();
  $("#mapShow").hide();
  $("#cardShowMap").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#tableShowRom").show();
  soundisactive = 0;
  var limittengahrom = 1000;
  var limitatasrom   = 1000;
  jQuery("#loader2").show();

  $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:3, companyid, companyid}, function(response){
    // console.log("response : ", response);
    jQuery("#loader2").hide();
    $("#contentrom").html("");
    var datainrom        = Object.entries(response.data);
    var totaldata        = datainrom.length;
    var allStreet        = response.allstreet;
    var jumlahtotalInRom = 0;

    var htmlrom          = "";
    // console.log("allStreet : ", allStreet);
    // console.log("datainrom : ", datainrom);
    // console.log("totaldata : ", totaldata);

    for (var i = 0; i < totaldata; i++) {
      jumlahtotalInRom += datainrom[i][1];
      htmlrom += '<div class="col-xl-3 col-md-6 col-12" onclick="listVehicleOnRom('+allStreet[i].street_id+')" style="cursor : pointer;">';
        htmlrom += '<div class="info-box bg-blue">';
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
    $("#jumlahtotalInRom").html("Total : " +jumlahtotalInRom + " || Last Refresh : " + response.lastcheck);
    $("#jumlahtotalInRom").show();
  },"json");
}

function listVehicleOnRom(id){
  $.post("<?php echo base_url() ?>maps/getlistinrom", {idrom: id}, function(response){
    console.log("response By ROM LIST : ", response);
    var datafix           = response.data;
    var totalrow          = datafix.length;
    var datacontractor    = response.jumlah_contractor;
    var datacontractorfix = Object.entries(datacontractor);

    var htmlcontractor = "";
    htmlcontractor += '<table>';
      htmlcontractor += '<tr>';
        for (var i = 0; i < datacontractorfix.length; i++) {
          if (datacontractorfix[i][1] != 0) {
            // console.log("datacontractorfix : ", datacontractorfix[i][0]);
            htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
            htmlcontractor += '<td>:<td>';
            htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
          }
        }
        htmlcontractor += '<tr>';
      htmlcontractor += '</table>';
      $("#contractorinlocation").html(htmlcontractor);

    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
        $("#modalStateTitle").html(response.romsent + " (" + totalrow + ")");
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
              htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
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

function showTablePort(){
  $("#cardShowMap").hide();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#tableShowPort").show();
  soundisactive = 0;
  var companyid = $("#contractor").val();
  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:4, companyid : companyid}, function(response){
    jQuery("#loader2").hide();
    // console.log("response : ", response);
    $("#contentrom").html("");
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

    var htmlportbib          = "";
    var htmlportbir          = "";
    var htmlportbbc          = "";
    var htmlporttia          = "";
    // console.log("allStreet : ", allStreet);
    // console.log("datainport : ", datainport);
    // console.log("totaldata : ", totaldata);

      // PORT BIB
        for (var i = 0; i < 8; i++) {
          jumlahtotalInPortBib += datainport[i][1];
        }
        htmlportbib += '<div>';
          htmlportbib += '<ol>';
            htmlportbib += '<li>';
              htmlportbib += '<ol>';
                htmlportbib += '<li>';
                  htmlportbib += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[0].street_id+')" style="cursor : pointer;">';
                  htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlportbib += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                    htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                  htmlportbib += '</span>';
                  htmlportbib += '<div class="info-box-content" style="color:white;">';
                    htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[0][0]+'</span>';
                    htmlportbib += '<span class="info-box-number" style="font-size:22px;"><b>'+jumlahtotalInPortBib+'</b></span>';
                  htmlportbib += '</div>';
                  htmlportbib += '</h3>';
                  htmlportbib += '<ol class="level-4-wrapper">';
                for (var i = 1; i < 8; i++) {
                    htmlportbib += '<li>';
                      htmlportbib += '<h4 class="level-4 rectangle" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                        htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                          htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                        htmlportbib += '</span>';
                        htmlportbib += '<div class="info-box-content" style="color:white;">';
                        htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                        htmlportbib += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[i][1]+'</b></span>';
                        htmlportbib += '</div>';
                      htmlportbib += '</h4>';
                    htmlportbib += '</li>';
                }
              htmlportbib += '</ol>';
            htmlportbib += '</li>';
          htmlportbib += '</ol>';
        htmlportbib += '</li>';
      htmlportbib += '</ol>';
    htmlportbib += '</div>';
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
                  htmlportbir += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[8].street_id+')" style="cursor : pointer;">';
                  htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlportbir += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                    htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                  htmlportbir += '</span>';
                  htmlportbir += '<div class="info-box-content" style="color:white;">';
                    htmlportbir += '<span class="info-box-text" style="font-size:15px;">'+datainport[8][0]+'</span>';
                    htmlportbir += '<span class="info-box-number" style="font-size:22px;"><b>'+jumlahtotalInPortBir+'</b></span>';
                  htmlportbir += '</div>';
                  htmlportbir += '</h3>';
                  htmlportbir += '<ol class="level-4-wrapper">';
                for (var i = 9; i < 14; i++) {
                    htmlportbir += '<li>';
                      htmlportbir += '<h4 class="level-4 rectangle" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                        htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                          htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                        htmlportbir += '</span>';
                        htmlportbir += '<div class="info-box-content" style="color:white;">';
                        htmlportbir += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                        htmlportbir += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[i][1]+'</b></span>';
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
              htmlportbbc += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[14].street_id+')" style="cursor : pointer;">';
              htmlportbbc += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                htmlportbbc += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                htmlportbbc += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
              htmlportbbc += '</span>';
              htmlportbbc += '<div class="info-box-content" style="color:white;">';
                htmlportbbc += '<span class="info-box-text" style="font-size:15px;">'+datainport[14][0]+'</span>';
                htmlportbbc += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[14][1]+'</b></span>';
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
              htmlporttia += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[15].street_id+')" style="cursor : pointer;">';
              htmlporttia += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                htmlporttia += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                htmlporttia += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
              htmlporttia += '</span>';
              htmlporttia += '<div class="info-box-content" style="color:white;">';
                htmlporttia += '<span class="info-box-text" style="font-size:15px;">'+datainport[15][0]+'</span>';
                htmlporttia += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[15][1]+'</b></span>';
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
    intervalportlist = setInterval(portlist_simultan, 15000);
  },"json");
}

function portlist_simultan(){
  clearInterval(intervalmapsstandard);
  clearInterval(intervaloutofhauling);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalpoollist);
  clearInterval(intervalromlist);
  clearInterval(intervalkmlist);
  clearInterval(intervalstart);

  $("#cardShowMap").hide();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#tableShowPort").show();
  soundisactive = 0;

  var companyid = $("#contractor").val();
  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:4, companyid : companyid}, function(response){
    jQuery("#loader2").hide();
    console.log("response simultan port : ", response);
    $("#contentrom").html("");
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

    var htmlportbib          = "";
    var htmlportbir          = "";
    var htmlportbbc          = "";
    var htmlporttia          = "";
    // console.log("allStreet : ", allStreet);
    // console.log("datainport : ", datainport);
    // console.log("totaldata : ", totaldata);

      // PORT BIB
        for (var i = 0; i < 8; i++) {
          jumlahtotalInPortBib += datainport[i][1];
        }
        htmlportbib += '<div>';
          htmlportbib += '<ol>';
            htmlportbib += '<li>';
              htmlportbib += '<ol>';
                htmlportbib += '<li>';
                  htmlportbib += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[0].street_id+')" style="cursor : pointer;">';
                  htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlportbib += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                    htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                  htmlportbib += '</span>';
                  htmlportbib += '<div class="info-box-content" style="color:white;">';
                    htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[0][0]+'</span>';
                    htmlportbib += '<span class="info-box-number" style="font-size:22px;"><b>'+jumlahtotalInPortBib+'</b></span>';
                  htmlportbib += '</div>';
                  htmlportbib += '</h3>';
                  htmlportbib += '<ol class="level-4-wrapper">';
                for (var i = 1; i < 8; i++) {
                    htmlportbib += '<li>';
                      htmlportbib += '<h4 class="level-4 rectangle" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                        htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                          htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                        htmlportbib += '</span>';
                        htmlportbib += '<div class="info-box-content" style="color:white;">';
                        htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                        htmlportbib += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[i][1]+'</b></span>';
                        htmlportbib += '</div>';
                      htmlportbib += '</h4>';
                    htmlportbib += '</li>';
                }
              htmlportbib += '</ol>';
            htmlportbib += '</li>';
          htmlportbib += '</ol>';
        htmlportbib += '</li>';
      htmlportbib += '</ol>';
    htmlportbib += '</div>';
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
                  htmlportbir += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[8].street_id+')" style="cursor : pointer;">';
                  htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlportbir += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                    htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                  htmlportbir += '</span>';
                  htmlportbir += '<div class="info-box-content" style="color:white;">';
                    htmlportbir += '<span class="info-box-text" style="font-size:15px;">'+datainport[8][0]+'</span>';
                    htmlportbir += '<span class="info-box-number" style="font-size:22px;"><b>'+jumlahtotalInPortBir+'</b></span>';
                  htmlportbir += '</div>';
                  htmlportbir += '</h3>';
                  htmlportbir += '<ol class="level-4-wrapper">';
                for (var i = 9; i < 14; i++) {
                    htmlportbir += '<li>';
                      htmlportbir += '<h4 class="level-4 rectangle" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                        htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                          htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                        htmlportbir += '</span>';
                        htmlportbir += '<div class="info-box-content" style="color:white;">';
                        htmlportbir += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                        htmlportbir += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[i][1]+'</b></span>';
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
              htmlportbbc += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[14].street_id+')" style="cursor : pointer;">';
              htmlportbbc += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                htmlportbbc += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                htmlportbbc += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
              htmlportbbc += '</span>';
              htmlportbbc += '<div class="info-box-content" style="color:white;">';
                htmlportbbc += '<span class="info-box-text" style="font-size:15px;">'+datainport[14][0]+'</span>';
                htmlportbbc += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[14][1]+'</b></span>';
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
              htmlporttia += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[15].street_id+')" style="cursor : pointer;">';
              htmlporttia += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                htmlporttia += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                htmlporttia += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
              htmlporttia += '</span>';
              htmlporttia += '<div class="info-box-content" style="color:white;">';
                htmlporttia += '<span class="info-box-text" style="font-size:15px;">'+datainport[15][0]+'</span>';
                htmlporttia += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[15][1]+'</b></span>';
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

    $("#jumlahtotalInPort").html("Total : " +jumlahtotalInPort + " || Last Refresh : " + response.lastcheck);
    $("#jumlahtotalInPort").show();
  },"json");
}

function listVehicleOnPort(id){
  $.post("<?php echo base_url() ?>maps/getlistinport", {idport: id}, function(response){
    console.log("response By PORT LIST : ", response);
    var datafix = response.data;

    var totalrow          = datafix.length;
    var datacontractor    = response.jumlah_contractor;
    var datacontractorfix = Object.entries(datacontractor);

    var htmlcontractor = "";
    htmlcontractor += '<table>';
      htmlcontractor += '<tr>';
        for (var i = 0; i < datacontractorfix.length; i++) {
          if (datacontractorfix[i][1] != 0) {
            // console.log("datacontractorfix : ", datacontractorfix[i][0]);
            htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
            htmlcontractor += '<td>:<td>';
            htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
          }
        }
        htmlcontractor += '<tr>';
      htmlcontractor += '</table>';
      $("#contractorinlocation").html(htmlcontractor);

    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
        $("#modalStateTitle").html(response.portsent + " (" + totalrow + ")");
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
              htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
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

function showTablePool(){
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
  $("#tableShowPool").show();
  soundisactive = 0;
  var companyid = $("#contractor").val();

  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:2, companyid : companyid}, function(response){
    // console.log("response : ", response);
    jQuery("#loader2").hide();

    // console.log("response : ", response);
    $("#contentrom").html("");
    var datainpool        = Object.entries(response.data);
    var totaldata         = datainpool.length;
    var allStreet         = response.allstreet;
    var jumlahtotalinpool = 0;

    var htmlpool          = "";
    // console.log("allStreet : ", allStreet);
    // console.log("datainpool : ", datainpool);
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

    $("#contentpool").html(htmlpool);
    $("#jumlahtotalinpool").html("Total : " +jumlahtotalinpool);
    $("#jumlahtotalinpool").show();
    intervalpoollist = setInterval(poollist_simultan, 15000);
  },"json");
}

function poollist_simultan(){
  clearInterval(intervalmapsstandard);
  clearInterval(intervaloutofhauling);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalportlist);
  clearInterval(intervalromlist);
  clearInterval(intervalkmlist);
  clearInterval(intervalstart);
  $("#cardShowMap").hide();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowRom").hide();
  $("#tableShowPort").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#tableShowPool").show();
  soundisactive = 0;
  var companyid = $("#contractor").val();
  jQuery("#loader2").show();

  $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:2, companyid : companyid}, function(response){
    // console.log("response : ", response);
    jQuery("#loader2").hide();
    $("#contentrom").html("");
    var datainpool        = Object.entries(response.data);
    var totaldata         = datainpool.length;
    var allStreet         = response.allstreet;
    var jumlahtotalinpool = 0;

    var htmlpool          = "";
    // console.log("allStreet : ", allStreet);
    // console.log("datainpool : ", datainpool);
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

    $("#contentpool").html(htmlpool);
    $("#jumlahtotalinpool").html("Total : " +jumlahtotalinpool + " || Last Refresh : " + response.lastcheck);
    $("#jumlahtotalinpool").show();
  },"json");
}

function getVehicleByPool(id){
  $.post("<?php echo base_url() ?>maps/vehicleonpool", {idpool : id}, function(response){
    console.log("response By Pool : ", response);
    var datafix           = response.data;
    var totalrow          = datafix.length;
    var datacontractor    = response.jumlah_contractor;
    var datacontractorfix = Object.entries(datacontractor);

    var htmlcontractor = "";
    htmlcontractor += '<table>';
      htmlcontractor += '<tr>';
        for (var i = 0; i < datacontractorfix.length; i++) {
          if (datacontractorfix[i][1] != 0) {
            // console.log("datacontractorfix : ", datacontractorfix[i][0]);
            htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
            htmlcontractor += '<td>:<td>';
            htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
          }
        }
        htmlcontractor += '<tr>';
      htmlcontractor += '</table>';
      $("#contractorinlocation").html(htmlcontractor);

    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.lastcheck + " WITA";
        $("#modalStateTitle").html(response.statesent + " (" + totalrow + ")");
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
            htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
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

function showOutOfHauling(){
  $("#cardShowMap").show();
  $("#mapshowfix").addClass('col-md-12');
  $("#mapsnya").removeClass();
  $("#mapsnya").addClass('mapsClass3');
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").hide();
  $("#showSearchNOutOfHauling").show();
  $("#mapShow").show();
  $("#tableShowOutOfHauling").show();

  soundisactive = 0;
  //STOP INTERVAL SIMULTANGO DEFAULT MAPS
  clearInterval(intervalstart);
  clearInterval(intervalmapsstandard);
  // REMOVE ALL MARKERS FROM ANOTHER MAPS
  heatmap.setMap(null);
  var companyid = $("#contractor").val();
  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/outOfHauling_quickcount", {companyid : companyid}, function(response){
    jQuery("#loader2").hide();

    console.log("response : ", response);
    var dataoutofhauling        = response.data.outofhauling;
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
    // console.log("objpoolmasterfixoutofhauling outofhauling: ", objpoolmasterfixoutofhauling);

    for (var j = 0; j < alldataforclearmaps.length; j++) {
      // console.log("outofhauling : " + j);
      DeleteMarkerspertama(alldataforclearmaps[j].vehicle_device);
      DeleteMarkers(alldataforclearmaps[j].vehicle_device);
      DeleteMarkerspertama(alldataforclearmaps[j].vehicle_id);
      DeleteMarkers(alldataforclearmaps[j].vehicle_id);
    }

    // CHANGE VEHICLE LIST
    $("#forsearchoutofhauling").html("");

        var htmlchangeVList = document.getElementById('forsearchoutofhauling');
        htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List Out Of Hauling', "0");
        for (var i = 0; i < vehicle.length; i++) {
          htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
        }

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
           $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
             infowindowkedua.close();

             var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
             var num         = Number(response[0].auto_last_speed);
             var roundstring = num.toFixed(0);
             var rounded     = Number(roundstring);

             var addresssplit = response[0].auto_last_position.split(" ");
             var inarea       = response[0].auto_last_position.split(",");
             var addressfix   = bibarea.includes(addresssplit[0]);
             if (addressfix) {
               var addressfix = inarea[0];
             }else {
               var addressfix = response[0].auto_last_position;
             }

             var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
               "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br>"+
               "Coord : <a href='https://www.google.com/maps/search/?api=1&query="+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"' target='_blank'>"+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"</a></span></br>"+
               // "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
               "Engine : " + response[0].auto_last_engine + "<br>" +
               "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
               "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";
               // "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

              infowindowkedua = new google.maps.InfoWindow({
               content: string,
               maxWidth: 300
             });

             infowindowkedua.open(map, marker);
             map.setCenter(center);
           }, "json");
         };
       })(marker, i));
     }
     intervaloutofhauling = setInterval(outofhauling_simultan, 15000);
  },"json");
}

function outofhauling_simultan(){
  clearInterval(intervalmapsstandard);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalpoollist);
  clearInterval(intervalportlist);
  clearInterval(intervalromlist);
  clearInterval(intervalkmlist);
  clearInterval(intervalstart);
  $("#cardShowMap").show();
  $("#mapshowfix").addClass('col-md-12');
  $("#mapsnya").removeClass();
  $("#mapsnya").addClass('mapsClass1');
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").hide();
  $("#showSearchNOutOfHauling").show();
  $("#mapShow").show();
  $("#tableShowOutOfHauling").show();

  soundisactive = 0;
  //STOP INTERVAL SIMULTANGO DEFAULT MAPS
  clearInterval(intervalstart);
  clearInterval(intervalmapsstandard);
  // REMOVE ALL MARKERS FROM ANOTHER MAPS
  heatmap.setMap(null);
  var companyid = $("#contractor").val();
  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/outOfHauling_quickcount", {companyid : companyid}, function(response){
    jQuery("#loader2").hide();
    console.log("response : ", response);
    var dataoutofhauling        = response.data.outofhauling;
    $("#jumlahtotaloutofhauling").html("Last Refresh : " + response.lastcheck);
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
    console.log("obj outofhauling fix simultan : ", objoutofhauling);
    // console.log("objpoolmasterfixoutofhauling outofhauling: ", objpoolmasterfixoutofhauling);

    for (var j = 0; j < alldataforclearmaps.length; j++) {
      // console.log("outofhauling : " + j);
      DeleteMarkerspertama(alldataforclearmaps[j].vehicle_device);
      DeleteMarkers(alldataforclearmaps[j].vehicle_device);
      DeleteMarkerspertama(alldataforclearmaps[j].vehicle_id);
      DeleteMarkers(alldataforclearmaps[j].vehicle_id);
    }

    // CHANGE VEHICLE LIST
    $("#forsearchoutofhauling").html("");

        var htmlchangeVList = document.getElementById('forsearchoutofhauling');
        htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List Out Of Hauling', "0");
        for (var i = 0; i < vehicle.length; i++) {
          htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
        }

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
           $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
             infowindowkedua.close();

             var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
             var num         = Number(response[0].auto_last_speed);
             var roundstring = num.toFixed(0);
             var rounded     = Number(roundstring);

             var addresssplit = response[0].auto_last_position.split(" ");
             var inarea       = response[0].auto_last_position.split(",");
             var addressfix   = bibarea.includes(addresssplit[0]);
             if (addressfix) {
               var addressfix = inarea[0];
             }else {
               var addressfix = response[0].auto_last_position;
             }

             var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
               "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br>"+
               "Coord : <a href='https://www.google.com/maps/search/?api=1&query="+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"' target='_blank'>"+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"</a></span></br>"+
               // "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
               "Engine : " + response[0].auto_last_engine + "<br>" +
               "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
               "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";
               // "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

              infowindowkedua = new google.maps.InfoWindow({
               content: string,
               maxWidth: 300
             });

             infowindowkedua.open(map, marker);
             map.setCenter(center);
           }, "json");
         };
       })(marker, i));
     }
  },"json");
}

function listOutOfHauling(){
  $.post("<?php echo base_url() ?>maps/getlistoutofhauling", {}, function(response){
    console.log("response By List Out Of Hauling : ", response);
    var datafix           = response.data;
    var totalrow          = datafix.length;
    var datacontractor    = response.jumlah_contractor;
    var datacontractorfix = Object.entries(datacontractor);

    var htmlcontractor = "";
    htmlcontractor += '<table>';
      htmlcontractor += '<tr>';
        for (var i = 0; i < datacontractorfix.length; i++) {
          if (datacontractorfix[i][1] != 0) {
            // console.log("datacontractorfix : ", datacontractorfix[i][0]);
            htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
            htmlcontractor += '<td>:<td>';
            htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
          }
        }
        htmlcontractor += '<tr>';
      htmlcontractor += '</table>';
      $("#contractorinlocation").html(htmlcontractor);

    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
        $("#modalStateTitle").html("Out Of Hauling" + " (" + totalrow + ")");
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
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
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

function showOfflineVehicle(){
  $("#cardShowMap").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/offlinevehicle_quickcount", {}, function(response){
    console.log("response offline vehicle : ", response);
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
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);
              htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
              htmlcontractor += '<td>:<td>';
              htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlofflinevehicle = "";
        if (datafix.length > 0) {
          var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html("Offline Vehicle" + " (" + totalrow + ")");
          $("#lastcheckpoolws").html(lastcheckpoolws);
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
        intervalofflinevehicle = setInterval(offlinevehicle_simultan, 15000);
  },"json");
}

function offlinevehicle_simultan(){
  $("#cardShowMap").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").show();
  soundisactive = 0;
  jQuery("#loader2").show();

  $.post("<?php echo base_url() ?>maps/offlinevehicle_quickcount", {}, function(response){
    jQuery("#loader2").hide();
    console.log("response offline vehicle : ", response);
    var sizeofflinevehicle = response.data.length;

    var labelvehicleoffline        = "Offline Vehicle";
    var vehicleoffline             = sizeofflinevehicle;
      $("#jumlahtotalOfflineVehicle").html("Last Refresh : " + response.lastcheck);
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
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);
              htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
              htmlcontractor += '<td>:<td>';
              htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlofflinevehicle = "";
        if (datafix.length > 0) {
          var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html("Offline Vehicle" + " (" + totalrow + ")");
          $("#lastcheckpoolws").html(lastcheckpoolws);
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
  },"json");
}

function standardMaps(){
  $("#mapshowfix").removeClass();
  $("#mapshowfix").addClass('col-md-12');
  $("#mapsnya").removeClass();
  $("#mapsnya").addClass('mapsClass1');
  $("#cardShowMap").show();
  $("#mapShow").show();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").show();
  $("#showSearchNOutOfHauling").hide();
  // CLEAR INTERVAL MAPS FROM HEATMAP
  clearInterval(intervalstart);
  heatmap.setMap(null);
  var companyid = $("#contractor").val();

  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/mapsstandard", {companyid : companyid}, function(response){
    jQuery("#loader2").hide();

    console.log("response mapsstandard : ", response);
    var vehicle             = response.data;
    var alldataforclearmaps = response.alldataforclearmaps;
    var bounds              = new google.maps.LatLngBounds();
    objmapsstandard         = vehicle;
    infowindow              = new google.maps.InfoWindow();

    for (var j = 0; j < alldataforclearmaps.length; j++) {
      // console.log("mapsstandard : " + j);
      DeleteMarkerspertama(alldataforclearmaps[j].vehicle_device);
      DeleteMarkers(alldataforclearmaps[j].vehicle_device);
      DeleteMarkerspertama(alldataforclearmaps[j].vehicle_id);
      DeleteMarkers(alldataforclearmaps[j].vehicle_id);
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
          $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
            console.log("Maps standard marker onclik : ", response);
            DeleteMarkers(marker.id);
            DeleteMarkerspertama(marker.id);

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            if (response[0].auto_last_road) {
              if (response[0].auto_last_road == "muatan") {
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
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
                }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
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
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
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
                }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
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

            var center            = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};

            marker = new google.maps.Marker({
              map: map,
              icon: icon,
              position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
              title: response[0].vehicle_no,
              id: response[0].vehicle_device
            });
            icon.rotation = Math.ceil(response[0].auto_last_course);
            marker.setIcon(icon);
            markers.push(marker);

            var sisaliterbensin, numfuel, roundfuel;
            if (objmapsstandard[i].vehicle_mv03 != 0000) {
              var numfuel     = Number(response[0].auto_last_fuel);
              var roundfuel   = numfuel.toFixed(2);
              sisaliterbensin = Number(roundfuel);
            }else {
              sisaliterbensin = "";
            }

            var addresssplit = response[0].auto_last_position.split(" ");
            var inarea       = response[0].auto_last_position.split(",");
            var addressfix   = bibarea.includes(addresssplit[0]);
            if (addressfix) {
              var addressfix = inarea[0];
            }else {
              var addressfix = response[0].auto_last_position;
            }

            var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
              "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
              "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
              "Engine : " + response[0].auto_last_engine + "<br>" +
              "Fuel : " + sisaliterbensin + " Kph<br>" +
              "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
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
                var numfuel     = Number(response[0].auto_last_fuel);
                var roundfuel   = numfuel.toFixed(2);
                sisaliterbensin = Number(roundfuel);
              }else {
                sisaliterbensin = "";
              }

              var num         = Number(response[0].auto_last_speed);
              var roundstring = num.toFixed(0);
              var rounded     = Number(roundstring);

              var addresssplit = response[0].auto_last_position.split(" ");
              var inarea       = response[0].auto_last_position.split(",");
              var addressfix   = bibarea.includes(addresssplit[0]);
              if (addressfix) {
                var addressfix = inarea[0];
              }else {
                var addressfix = response[0].auto_last_position;
              }

              var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                "Engine : " + response[0].auto_last_engine + "<br>" +
                "Fuel : " + sisaliterbensin + " Kph<br>" +
                "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
                "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";

               infowindowkedua = new google.maps.InfoWindow({
                content: string,
                maxWidth: 300
              });
              // DeleteMarkers(response[0].vehicle_device);
              // DeleteMarkerspertama(response[0].vehicle_device);

                var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
                infowindowkedua.setContent(string);
                map.setCenter(marker.position);
                marker.setPosition(marker.position);
                infowindowkedua.open(map, this);
            });

          }, "json");
        };
      })(marker, i));
    }
  },"json");
  intervalmapsstandard = setInterval(simultangomapsstandard, 15000);
}

var objectnumberfix      = 1;
var objectnumber         = 0;
function simultangomapsstandard() {
  clearInterval(intervalstart);
  heatmap.setMap(null);
  var companyid = $("#contractor").val();

  jQuery("#loader2").show();
  jQuery.post("<?=base_url();?>map/mapsstandardlastinfoall", {companyid : companyid}, function(response) {
    jQuery("#loader2").hide();

    console.log("response mapsstandard simultan : ", response);
    var vehicle             = response.data;
    var bounds              = new google.maps.LatLngBounds();
    objmapsstandardsimultan = vehicle;
    infowindow              = new google.maps.InfoWindow();

    for (var j = 0; j < response.data.length; j++) {
      DeleteMarkerspertama(vehicle[j].vehicle_device);
      DeleteMarkers(vehicle[j].vehicle_device);
      DeleteMarkerspertama(vehicle[j].vehicle_id);
      DeleteMarkers(vehicle[j].vehicle_id);
    }

    for (i = 0; i < objmapsstandard.length; i++) {
      var position = new google.maps.LatLng(parseFloat(objmapsstandardsimultan[i].auto_last_lat), parseFloat(objmapsstandardsimultan[i].auto_last_long));
      bounds.extend(position);

      var nums         = Number(objmapsstandardsimultan[i].auto_last_speed);
      var roundstrings = nums.toFixed(0);
      var roundedspeed = Number(roundstrings);

      if (objmapsstandard[i].auto_last_road == "muatan") {
        if (roundedspeed == 0 && objmapsstandardsimultan[i].auto_last_engine == "ON") {
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
        }else if (roundedspeed > 0 && objmapsstandardsimultan[i].auto_last_engine == "ON") {
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
        if (roundedspeed > 0 && objmapsstandardsimultan[i].auto_last_engine == "ON") {
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
        }else if (roundedspeed == 0 && objmapsstandardsimultan[i].auto_last_engine == "ON") {
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
        title: objmapsstandardsimultan[i].vehicle_no,
        id: objmapsstandardsimultan[i].vehicle_device
      });
      icon.rotation = Math.ceil(objmapsstandardsimultan[i].auto_last_course);
      marker.setIcon(icon);
      markers.push(marker);

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          var data = {device_id : marker.id};
          $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
            console.log("Maps standard marker onclik : ", response);
            DeleteMarkers(marker.id);
            DeleteMarkerspertama(marker.id);

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            if (response[0].auto_last_road) {
              if (response[0].auto_last_road == "muatan") {
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
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
                }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
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
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
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
                }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
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

            var center            = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};

            marker = new google.maps.Marker({
              map: map,
              icon: icon,
              position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
              title: response[0].vehicle_no,
              id: response[0].vehicle_device
            });
            icon.rotation = Math.ceil(response[0].auto_last_course);
            marker.setIcon(icon);
            markers.push(marker);

            var sisaliterbensin, numfuel, roundfuel;
            if (objmapsstandard[i].vehicle_mv03 != 0000) {
              var numfuel     = Number(response[0].auto_last_fuel);
              var roundfuel   = numfuel.toFixed(2);
              sisaliterbensin = Number(roundfuel);
            }else {
              sisaliterbensin = "";
            }

            var addresssplit = response[0].auto_last_position.split(" ");
            var inarea       = response[0].auto_last_position.split(",");
            var addressfix   = bibarea.includes(addresssplit[0]);
            if (addressfix) {
              var addressfix = inarea[0];
            }else {
              var addressfix = response[0].auto_last_position;
            }

            var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
              "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
              "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
              "Engine : " + response[0].auto_last_engine + "<br>" +
              "Fuel : " + sisaliterbensin + " Kph<br>" +
              "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
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
                var numfuel     = Number(response[0].auto_last_fuel);
                var roundfuel   = numfuel.toFixed(2);
                sisaliterbensin = Number(roundfuel);
              }else {
                sisaliterbensin = "";
              }

              var num         = Number(response[0].auto_last_speed);
              var roundstring = num.toFixed(0);
              var rounded     = Number(roundstring);

              var addresssplit = response[0].auto_last_position.split(" ");
              var inarea       = response[0].auto_last_position.split(",");
              var addressfix   = bibarea.includes(addresssplit[0]);
              if (addressfix) {
                var addressfix = inarea[0];
              }else {
                var addressfix = response[0].auto_last_position;
              }

              var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                "Engine : " + response[0].auto_last_engine + "<br>" +
                "Fuel : " + sisaliterbensin + " Kph<br>" +
                "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
                "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";

               infowindowkedua = new google.maps.InfoWindow({
                content: string,
                maxWidth: 300
              });
              // DeleteMarkers(response[0].vehicle_device);
              // DeleteMarkerspertama(response[0].vehicle_device);

                var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
                infowindowkedua.setContent(string);
                map.setCenter(marker.position);
                marker.setPosition(marker.position);
                infowindowkedua.open(map, this);
            });

          }, "json");
        };
      })(marker, i));
    }
  }, "json");
}

function getVehicleByContractor(){
  var companyid        = $("#contractor").val();
  var mapsOptionsValue = $("#mapsOptions").val();
  var valuemapsoption;

  if (mapsOptionsValue == 0) {
    mapsOptionsValue = "showHeatmap";
  }

  $.post("<?php echo base_url() ?>maps/vehicleByContractor", {companyid : companyid, valuemapsoption: 9999}, function(response){
    console.log("contractor onchange to rom : ", response);
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

      valuemapsoption = 1;
      jQuery("#loader2").show();

      $.post("<?php echo base_url() ?>maps/vehicleByContractor", {companyid : companyid, valuemapsoption: valuemapsoption}, function(response){
        jQuery("#loader2").hide();

        console.log("contractor onchange showHeatmap : ", response);
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

            $("#searchnopol").html("");

                var htmlchangeVList = document.getElementById('searchnopol');
                htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List', "0");
                for (var i = 0; i < vehicle.length; i++) {
                  htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
                }

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
          intervalstart = setInterval(simultango, 15000);
      },"json");
    }else if ( mapsOptionsValue == "showTableMuatan1") {
      valuemapsoption = 2;
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);

      jQuery("#loader2").show();

      $.post("<?php echo base_url() ?>maps/km_quickcount_new", {companyid : companyid}, function(response){
        jQuery("#loader2").hide();
            console.log("response : ", response);
            var datamuatan         = response.dataMuatan;
            var datakosongan       = response.dataKosongan;
            var datafixlimitperkm  = response.datafixlimitperkm;
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

            // console.log("arraydatamuatan : ", arraydatamuatan);
            // console.log("arraydatakosongan : ", arraydatakosongan);

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
                for (var i = 0; i < sizekosongan; i++) {
                  var kmlist         = arraydatakosongan[i][0];
                  var jumlahfix      = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                  totalKosongan += jumlahfix;

                  if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
                    $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                    $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
                  }else if(jumlahfix >= top_limit){
                    $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                    $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
                  }else {
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

                $("#port_bib_muatan").html(portbibmuatan);
                $("#port_bir_muatan").html(portbirmuatan);

                if (portbibmuatan >= middle_limit && portbibmuatan < top_limit) {
                  $("#port_bib_muatan").addClass('timeline-badge5 btn btn-warning btn-md btn-circle');
                }else if(portbibmuatan >= top_limit){
                  $("#port_bib_muatan").addClass('timeline-badge5 btn btn-danger btn-md btn-circle');
                }else {
                  $("#port_bib_muatan").addClass('timeline-badge5 btn btn-success btn-md btn-circle');
                }

                if (portbirmuatan >= middle_limit && portbirmuatan < top_limit) {
                  $("#port_bir_muatan").addClass('timeline-badge7 btn btn-warning btn-md btn-circle');
                }else if(portbirmuatan >= top_limit){
                  $("#port_bir_muatan").addClass('timeline-badge7 btn btn-danger btn-md btn-circle');
                }else {
                  $("#port_bir_muatan").addClass('timeline-badge7 btn btn-success btn-md btn-circle');
                }
              }

              // KM 2 - 30 MUATAN
              for (var i = 0; i < sizemuatan; i++) {
                var kmlist    = arraydatamuatan[i][0];
                var jumlahfix = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
                totalMuatan += jumlahfix;

                if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
                  $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                  $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
                }else if(jumlahfix >= top_limit){
                  $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                  $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
                }else {
                  $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
                  $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
                }

                $("#vehicleonmuatan"+i).html(jumlahfix);
              }

                $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2) + " || Last Refresh : " + response.lastcheck) ;
                $("#lastupdateconsolidated").show();
                intervalkmlist = setInterval(km_quickcount_simultan, 35000);
          },"json");
    }else if ( mapsOptionsValue == "showTableRom") {
      valuemapsoption = 3;
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);

      $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:3, companyid : companyid}, function(response){
        console.log("getstreetautomaticbycompanyid : ", response);
        $("#contentrom").html("");
        var limittengahrom   = 1000;
        var limitatasrom     = 1000;
        var datainrom        = Object.entries(response.data);
        var totaldata        = datainrom.length;
        var allStreet        = response.allstreet;
        var jumlahtotalInRom = 0;

        var htmlrom          = "";

        for (var i = 0; i < totaldata; i++) {
          jumlahtotalInRom += datainrom[i][1];
          htmlrom += '<div class="col-xl-3 col-md-6 col-12" onclick="listVehicleOnRom('+allStreet[i].street_id+')" style="cursor : pointer;">';
            htmlrom += '<div class="info-box bg-blue">';
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
        intervalromlist = setInterval(romlist_simultan, 15000);
      },"json");
    }else if ( mapsOptionsValue == "showTablePort") {
      valuemapsoption = 4;
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);

      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:4, companyid : companyid}, function(response){
        jQuery("#loader2").hide();
        // console.log("response : ", response);
        $("#contentrom").html("");
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

        var htmlportbib          = "";
        var htmlportbir          = "";
        var htmlportbbc          = "";
        var htmlporttia          = "";
        // console.log("allStreet : ", allStreet);
        // console.log("datainport : ", datainport);
        // console.log("totaldata : ", totaldata);

          // PORT BIB
            for (var i = 0; i < 8; i++) {
              jumlahtotalInPortBib += datainport[i][1];
            }
            htmlportbib += '<div>';
              htmlportbib += '<ol>';
                htmlportbib += '<li>';
                  htmlportbib += '<ol>';
                    htmlportbib += '<li>';
                      htmlportbib += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[0].street_id+')" style="cursor : pointer;">';
                      htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                        htmlportbib += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                        htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                      htmlportbib += '</span>';
                      htmlportbib += '<div class="info-box-content" style="color:white;">';
                        htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[0][0]+'</span>';
                        htmlportbib += '<span class="info-box-number" style="font-size:22px;"><b>'+jumlahtotalInPortBib+'</b></span>';
                      htmlportbib += '</div>';
                      htmlportbib += '</h3>';
                      htmlportbib += '<ol class="level-4-wrapper">';
                    for (var i = 1; i < 8; i++) {
                        htmlportbib += '<li>';
                          htmlportbib += '<h4 class="level-4 rectangle" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                            htmlportbib += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                              htmlportbib += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                            htmlportbib += '</span>';
                            htmlportbib += '<div class="info-box-content" style="color:white;">';
                            htmlportbib += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                            htmlportbib += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[i][1]+'</b></span>';
                            htmlportbib += '</div>';
                          htmlportbib += '</h4>';
                        htmlportbib += '</li>';
                    }
                  htmlportbib += '</ol>';
                htmlportbib += '</li>';
              htmlportbib += '</ol>';
            htmlportbib += '</li>';
          htmlportbib += '</ol>';
        htmlportbib += '</div>';
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
                      htmlportbir += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[8].street_id+')" style="cursor : pointer;">';
                      htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                        htmlportbir += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                        htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                      htmlportbir += '</span>';
                      htmlportbir += '<div class="info-box-content" style="color:white;">';
                        htmlportbir += '<span class="info-box-text" style="font-size:15px;">'+datainport[8][0]+'</span>';
                        htmlportbir += '<span class="info-box-number" style="font-size:22px;"><b>'+jumlahtotalInPortBir+'</b></span>';
                      htmlportbir += '</div>';
                      htmlportbir += '</h3>';
                      htmlportbir += '<ol class="level-4-wrapper">';
                    for (var i = 9; i < 14; i++) {
                        htmlportbir += '<li>';
                          htmlportbir += '<h4 class="level-4 rectangle" onclick="listVehicleOnPort('+allStreet[i].street_id+')" style="cursor : pointer;">';
                            htmlportbir += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                              htmlportbir += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                            htmlportbir += '</span>';
                            htmlportbir += '<div class="info-box-content" style="color:white;">';
                            htmlportbir += '<span class="info-box-text" style="font-size:15px;">'+datainport[i][0]+'</span>';
                            htmlportbir += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[i][1]+'</b></span>';
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
                  htmlportbbc += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[14].street_id+')" style="cursor : pointer;">';
                  htmlportbbc += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlportbbc += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                    htmlportbbc += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                  htmlportbbc += '</span>';
                  htmlportbbc += '<div class="info-box-content" style="color:white;">';
                    htmlportbbc += '<span class="info-box-text" style="font-size:15px;">'+datainport[14][0]+'</span>';
                    htmlportbbc += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[14][1]+'</b></span>';
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
                  htmlporttia += '<h3 class="level-3 info-box" onclick="listVehicleOnPort('+allStreet[15].street_id+')" style="cursor : pointer;">';
                  htmlporttia += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
                    htmlporttia += '<!--<i class="material-icons" style="font-size:50px; padding-top:10px;">directions_boat</i>-->';
                    htmlporttia += '<img src="<?php echo base_url();?>/assets/bib/images/green.png" style="font-size:50px;">';
                  htmlporttia += '</span>';
                  htmlporttia += '<div class="info-box-content" style="color:white;">';
                    htmlporttia += '<span class="info-box-text" style="font-size:15px;">'+datainport[15][0]+'</span>';
                    htmlporttia += '<span class="info-box-number" style="font-size:22px;"><b>'+datainport[15][1]+'</b></span>';
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
        intervalportlist = setInterval(portlist_simultan, 15000);
      },"json");
    }else if ( mapsOptionsValue == "showTablePool") {
      valuemapsoption = 5;
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);

      soundisactive = 0;
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:2, companyid : companyid}, function(response){
        jQuery("#loader2").hide();

        // console.log("response : ", response);
        $("#contentrom").html("");
        var datainpool        = Object.entries(response.data);
        var totaldata         = datainpool.length;
        var allStreet         = response.allstreet;
        var jumlahtotalinpool = 0;

        var htmlpool          = "";
        // console.log("allStreet : ", allStreet);
        // console.log("datainpool : ", datainpool);
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

        $("#contentpool").html(htmlpool);
        $("#jumlahtotalinpool").html("Total : " +jumlahtotalinpool);
        $("#jumlahtotalinpool").show();
        intervalpoollist = setInterval(poollist_simultan, 15000);
      },"json");

    }else if ( mapsOptionsValue == "outofhauling") {
      valuemapsoption = 6;
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);

      heatmap.setMap(null);
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/outOfHauling_quickcount", {companyid : companyid}, function(response){
        jQuery("#loader2").hide();

        console.log("outOfHauling_quickcount contractor onchange : ", response);
        var dataoutofhauling        = response.data.outofhauling;

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

        for (var j = 0; j < alldataforclearmaps.length; j++) {
          DeleteMarkerspertama(alldataforclearmaps[j].vehicle_device);
          DeleteMarkers(alldataforclearmaps[j].vehicle_device);
          DeleteMarkerspertama(alldataforclearmaps[j].vehicle_id);
          DeleteMarkers(alldataforclearmaps[j].vehicle_id);
        }

        // CHANGE VEHICLE LIST
        $("#forsearchoutofhauling").html("");

            var htmlchangeVList = document.getElementById('forsearchoutofhauling');
            htmlchangeVList.options[htmlchangeVList.options.length] = new Option('Vehicle List Out Of Hauling', "0");
            for (var i = 0; i < vehicle.length; i++) {
              htmlchangeVList.options[htmlchangeVList.options.length] = new Option(vehicle[i].vehicle_no, vehicle[i].vehicle_no);
            }

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
               $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
                 infowindowkedua.close();

                 var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
                 var num         = Number(response[0].auto_last_speed);
                 var roundstring = num.toFixed(0);
                 var rounded     = Number(roundstring);

                 var addresssplit = response[0].auto_last_position.split(" ");
                 var inarea       = response[0].auto_last_position.split(",");
                 var addressfix   = bibarea.includes(addresssplit[0]);
                 if (addressfix) {
                   var addressfix = inarea[0];
                 }else {
                   var addressfix = response[0].auto_last_position;
                 }

                 var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br>"+
                   "Coord : <a href='https://www.google.com/maps/search/?api=1&query="+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"' target='_blank'>"+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"</a></span></br>"+
                   // "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                   "Engine : " + response[0].auto_last_engine + "<br>" +
                   "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
                   "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";
                   // "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

                  infowindowkedua = new google.maps.InfoWindow({
                   content: string,
                   maxWidth: 300
                 });

                 infowindowkedua.open(map, marker);
                 map.setCenter(center);
               }, "json");
             };
           })(marker, i));
         }
         intervaloutofhauling = setInterval(outofhauling_simultan, 15000);
      },"json");
    }else if ( mapsOptionsValue == "offlinevehicle") {
      valuemapsoption = 7;
    }else if ( mapsOptionsValue == "standardmaps") {
      valuemapsoption = 8;
      clearInterval(intervaloutofhauling);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
      clearInterval(intervalmapsstandard);

      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/mapsstandard", {companyid : companyid}, function(response){
        jQuery("#loader2").hide();

        console.log("response mapsstandard : ", response);
        var vehicle             = response.data;
        var alldataforclearmaps = response.alldataforclearmaps;
        var bounds              = new google.maps.LatLngBounds();
        objmapsstandard         = vehicle;
        infowindow              = new google.maps.InfoWindow();

        for (var j = 0; j < alldataforclearmaps.length; j++) {
          // console.log("mapsstandard : " + j);
          DeleteMarkerspertama(alldataforclearmaps[j].vehicle_device);
          DeleteMarkers(alldataforclearmaps[j].vehicle_device);
          DeleteMarkerspertama(alldataforclearmaps[j].vehicle_id);
          DeleteMarkers(alldataforclearmaps[j].vehicle_id);
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
              $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
                console.log("Maps standard marker onclik : ", response);
                DeleteMarkers(marker.id);
                DeleteMarkerspertama(marker.id);

                var num         = Number(response[0].auto_last_speed);
                var roundstring = num.toFixed(0);
                var rounded     = Number(roundstring);

                if (response[0].auto_last_road) {
                  if (response[0].auto_last_road == "muatan") {
                    if (rounded == 0 && response[0].auto_last_engine == "ON") {
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
                    }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
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
                    if (rounded == 0 && response[0].auto_last_engine == "ON") {
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
                    }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
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

                var center            = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};

                marker = new google.maps.Marker({
                  map: map,
                  icon: icon,
                  position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
                  title: response[0].vehicle_no,
                  id: response[0].vehicle_device
                });
                icon.rotation = Math.ceil(response[0].auto_last_course);
                marker.setIcon(icon);
                markers.push(marker);

                var sisaliterbensin, numfuel, roundfuel;
                if (objmapsstandard[i].vehicle_mv03 != 0000) {
                  var numfuel     = Number(response[0].auto_last_fuel);
                  var roundfuel   = numfuel.toFixed(2);
                  sisaliterbensin = Number(roundfuel);
                }else {
                  sisaliterbensin = "";
                }

                var addresssplit = response[0].auto_last_position.split(" ");
                var inarea       = response[0].auto_last_position.split(",");
                var addressfix   = bibarea.includes(addresssplit[0]);
                if (addressfix) {
                  var addressfix = inarea[0];
                }else {
                  var addressfix = response[0].auto_last_position;
                }

                var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                  "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                  "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                  "Engine : " + response[0].auto_last_engine + "<br>" +
                  "Fuel : " + sisaliterbensin + " Kph<br>" +
                  "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
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
                    var numfuel     = Number(response[0].auto_last_fuel);
                    var roundfuel   = numfuel.toFixed(2);
                    sisaliterbensin = Number(roundfuel);
                  }else {
                    sisaliterbensin = "";
                  }

                  var num         = Number(response[0].auto_last_speed);
                  var roundstring = num.toFixed(0);
                  var rounded     = Number(roundstring);

                  var addresssplit = response[0].auto_last_position.split(" ");
                  var inarea       = response[0].auto_last_position.split(",");
                  var addressfix   = bibarea.includes(addresssplit[0]);
                  if (addressfix) {
                    var addressfix = inarea[0];
                  }else {
                    var addressfix = response[0].auto_last_position;
                  }

                  var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                    "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                    "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                    "Engine : " + response[0].auto_last_engine + "<br>" +
                    "Fuel : " + sisaliterbensin + " Kph<br>" +
                    "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
                    "<div onclick='closeWindowOnMarkerOOH();' style='color:blue;cursor:pointer;'>Tutup Informasi</div>";

                   infowindowkedua = new google.maps.InfoWindow({
                    content: string,
                    maxWidth: 300
                  });
                  // DeleteMarkers(response[0].vehicle_device);
                  // DeleteMarkerspertama(response[0].vehicle_device);

                    var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
                    infowindowkedua.setContent(string);
                    map.setCenter(marker.position);
                    marker.setPosition(marker.position);
                    infowindowkedua.open(map, this);
                });

              }, "json");
            };
          })(marker, i));
        }
      },"json");
      intervalmapsstandard = setInterval(simultangomapsstandard, 15000);
    }else {
      valuemapsoption = 1;
    }
}

var positionmove;
var numDeltas = 100;
var delay     = 10; //milliseconds
var iloopnya  = 0;
var deltaLat;
var deltaLng;

function transition(value){
  console.log("vehicle : ", value.vehicle_no+" - "+value.vehicle_name);
  console.log("value in transition : ", value);

  var totalcoordinate = value.gps.length;
  // console.log("totalcoordinate : ", totalcoordinate-1);

  var voltasefull    = value.vehicle_fuel_volt;
  var tankfullcap    = value.vehicle_fuel_capacity;
  var percentagefull = 100;
  var currentvolt, percenvoltase, percenvoltase1, sisaliterbensin, numvolt, roundvolt;
  if (value.vehicle_mv03 != 0000) {
    var numvolt     = Number(value.gps[totalcoordinate-1].gps_mvd);
    var roundvolt   = numvolt.toFixed(2);
    currentvolt     = Number(roundvolt);

    if (currentvolt > voltasefull) {
      currentvolt = voltasefull
    }

    // console.log("Vehicle MV03 Not 0000 : ", value.vehicle_no+" - "+value.vehicle_name);
    percenvoltase1     = currentvolt * (percentagefull / voltasefull); // persentase yg didapat dari perubahan voltase;
    var percenvoltase2 = Number(percenvoltase1);
    var percenvoltase3 = percenvoltase2.toFixed(0);
    percenvoltase      = Number(percenvoltase3);
    sisaliterbensin    = (percenvoltase * tankfullcap) / percentagefull;

    // console.log("currentvolt : ", currentvolt);
    // console.log("percenvoltase : ", percenvoltase);
    // console.log("sisaliterbensin : ", sisaliterbensin);
  }else {
    currentvolt     = "";
    percenvoltase   = "";
    sisaliterbensin = "";
  }

  var num         = Number(value.gps[totalcoordinate-1].gps_speed_fmt);
  var roundstring = num.toFixed(0);
  var rounded     = Number(roundstring);

  var statusengine = "";
  if (value.status1 == true) {
    statusengine = "ON";
  }else {
    statusengine = "OFF";
  }

  // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
  if (value.devicestatus) {
    if (value.devicestatus == 1) {
      var devicestatus = "Camera : Online </br>" ;
    }else {
      var devicestatus = "Camera : Offline </br>" ;
    }
  }else {
    var devicestatus = "" ;
  }

    // console.log("devicestatus ", value.devicestatus);
  // console.log("devicestatus : ", devicestatus);

  var geofencestatus = "";
    if (value.geofence_location != "") {
      geofencestatus = "<span style='color:black'>Geofence : " + value.geofence_location + "</span></br>";
    }
    // console.log("geofence : ", geofencestatus);

  // UNTUK ADD COMMENT
  var comment = "<span id='comment"+value.vehicle_id+"' style='display: none'></span>";
  // console.log("comment : ", comment);

  var num         = Number(value.gps[totalcoordinate-1].gps_speed_fmt);
  var roundstring = num.toFixed(0);
  var rounded     = Number(roundstring);

  var ritasefix = 0;
  if (value.ritase) {
    if (value.ritase == 0) {
      ritasefix = 0;
    }else {
      ritasefix = value.ritase;
    }
  }

  var jalurfix;
  if (value.jalur) {
      jalurfix = value.jalur;
  }else {
    jalurfix = "";
  }

  var gps_status = "";
  if (value.gps.gps_status == "A" || value.gps.gps_status == "V") {
    gps_status = "Good";
  }else {
    gps_status = "Not Good";
  }

  var courseround   = Number(value.gps[0].gps_course);
  var coursestring  = courseround.toFixed(0);
  var courserounded = Number(coursestring);


  var titlemarker  = "";
  var addresssplit = value.gps[totalcoordinate-1].georeverse.display_name.split(" ");
  var inarea       = value.gps[totalcoordinate-1].georeverse.display_name.split(",");
  // console.log("addresssplit : ", addresssplit);

  var addressfix = bibarea.includes(addresssplit[0]);
  if (addressfix) {
    var addressfix = inarea[0];
  }else {
    var addressfix = value.gps[totalcoordinate-1].georeverse.display_name;
  }

  // SISA LITER BENSIN IN ROUND
  if (sisaliterbensin != "") {
    var ltrbensin       = Number(sisaliterbensin);
    var ltrbensinround  = ltrbensin.toFixed(0);
    var sisaliterbensin = Number(ltrbensinround)+"Ltr ("+percenvoltase+"%)";
    // var percenvoltase   = percenvoltase+"%";
  }

  var vehicle_id_ = '<a style="color:green;" onclick="forgetcenter('+value.vehicle_id+')">'+value.vehicle_no + " - " + value.vehicle_name+'</a>';
  var position_   = geofencestatus + '<span style="color:blue;">' + addressfix + "</span> <br> GPS Time : " + value.gps[totalcoordinate-1].gps_date_fmt + " " + value.gps[totalcoordinate-1].gps_time_fmt + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+parseFloat(value.gps[totalcoordinate-1].gps_latitude_real_fmt)+','+value.gps[totalcoordinate-1].gps_longitude_real_fmt+"' target='_blank'>" + value.gps[totalcoordinate-1].gps_latitude_real_fmt + ", " + value.gps[totalcoordinate-1].gps_longitude_real_fmt + "</a> <br>" + "Engine : " + statusengine + "<br>" + "Fuel : " + sisaliterbensin + "<br>" + "Speed : " + rounded + " Kph<br>" + devicestatus + "Jalur : " + jalurfix + "</br> Ritase : " + ritasefix + "</br>Direction : "+ courserounded + "</br>GPS Status : " + gps_status + "<br>" + comment;

  var descriptionvalue = "";
  if (value.dataproject) {
    descriptionvalue = value.dataproject;
  }
  var description_ = descriptionvalue;

  var cutpowerfix = "";
  if (value.cutpower) {
    cutpowerfix = "</br><b><font color='red'>Power Off : "+" "+value.cutpower+"</font></b><br/>";
  }

  var cardno_     = value.vehicle_card_no + cutpowerfix;

  // BIODATA DRIVER START
  var datadriver;
  if (value.driver){
    // console.log("sikon 0");
     datadriver   = value.driver.split('-');
    if (value.driverimage) {
      // console.log("sikon 1");
      if (value.driverimage != 0) {
        // console.log("sikon 2");
        // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
        var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+value.driverimage+'" width="100px;" height="100px;"> </br>' + datadriver[1] ;
        $("[id='driver_"+value.vehicle_device+"']").html(detaildriver);
      }else {
        var detaildriver = datadriver[1] + ' </br> No Driver Image';
        $("[id='driver_"+value.vehicle_device+"']").html(detaildriver);
      }
    }else {
      var detaildriver = datadriver[1] + ' </br> No Driver Image';
      $("[id='driver_"+value.vehicle_device+"']").html(detaildriver);
    }
  }
  // BIODATA DRIVER END

  //Get driver ID CARD
  if (value.driver_idcard){
    // var sDriver = value.driver_idcard.split('-');
    // $("[id='driver_"+value.vehicle_device+"']").html("<a style='color: black;' href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
    $("[id='driver_"+value.vehicle_device+"']").html(value.driver_sj);
  }
  //end get driver ID CARD

  //Get driver ID CARD
  if (value.driver){
    // var sDriver = value.driver.split('-');
    // $("[id='driver_"+value.vehicle_device+"']").html("<a style='color: black;' href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
    $("[id='driver_"+value.vehicle_device+"']").html(value.driver_sj);
  }
  //end get driver ID CARD
  //Get Customer Groups
  if (value.customer_groups)
  {
      jQuery("[id='customer_"+value.vehicle_device+"']").html(value.customer_groups);
  }

$("[id='pointer_"+value.vehicle_device+"']").show();
$("[id='vehicle_id_"+value.vehicle_device+"']").html(vehicle_id_);
$("[id='position_"+value.vehicle_device+"']").html(position_);
// $("[id='description_"+value.vehicle_device+"']").html(description_);
$("[id='cardno_"+value.vehicle_device+"']").html(cardno_);

// console.log("course : ", value.gps.gps_course);
// console.log("Status : ", value.gps.gps_status);
// console.log("value : ", value);
// console.log("jalur on marker new : ", value.jalur);
// console.log("speed on marker new : ", rounded);
// console.log("engine on marker new : ", value.status1);

  if (value.jalur) {
    // console.log("jalur on");
    if (value.jalur == "muatan") {
      // console.log("jalur muatan");
      if (rounded == 0 && value.status1 == true) {
        // console.log("jalur muatan 1");
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
      }else if (rounded > 0 && value.status1 == true) {
        // console.log("jalur muatan 2");
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
        // console.log("jalur muatan 3");
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
      // console.log("jalur kosongan");
      if (rounded == 0 && value.status1 == true) {
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
      }else if (rounded > 0 && value.status1 == true) {
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
      }else {
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
  }else {
    console.log("jalur off");
  }

  $("[id='laststatus_"+value.vehicle_device+"']").html(laststatus2);

  DeleteMarkers(value.vehicle_device_name + "@" + value.vehicle_device_host);
  DeleteMarkerspertama(value.vehicle_device_name + "@" + value.vehicle_device_host);
  infowindow           = new google.maps.InfoWindow();
  infowindow2          = new google.maps.InfoWindow();
  infowindowkedua      = new google.maps.InfoWindow();
  infowindowonsimultan = new google.maps.InfoWindow();

  var camdevicesfix = camdevices.includes(value.vehicle_type);
  if (camdevicesfix) {
    var lct = "Last Captured Time: " +  value.snaptime + "</br>";
    var imglct = "<img src='"+ value.snapimage+"'> </br>";
  }else {
    var lct = "";
    var imglct = "</br>";
  }

  positionmove = [parseFloat(value.gps[0].gps_latitude_real_fmt), parseFloat(value.gps[0].gps_longitude_real_fmt)];
  markernya = new google.maps.Marker({
    map: map,
    icon: icon,
    // position: new google.maps.LatLng(parseFloat(value.gps.gps_latitude_real_fmt), parseFloat(value.gps.gps_longitude_real_fmt)),
    position: new google.maps.LatLng(positionmove[0], positionmove[1]),
    title: value.vehicle_no + ' - ' + value.vehicle_name,
    // + ' - ' + value.vehicle_name + value.driver + "\n" +
    //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
    //   "Speed : " + value.gps.gps_speed + " kph",
    id: value.vehicle_device_name + "@" + value.vehicle_device_host
  });
  markerss.push(markernya);
  // INI UNTUK MOVE MARKER DGN SMOOTH
  var prevLat = parseFloat(value.gps[0].gps_latitude_real_fmt);
  var prevLng = parseFloat(value.gps[0].gps_longitude_real_fmt);
    if (totalcoordinate == 1) {
      var nextLat = parseFloat(value.gps[0].gps_latitude_real_fmt);
      var nextLng = parseFloat(value.gps[0].gps_longitude_real_fmt);
    }else {
      var nextLat = parseFloat(value.gps[totalcoordinate-1].gps_latitude_real_fmt);
      var nextLng = parseFloat(value.gps[totalcoordinate-1].gps_longitude_real_fmt);
    }

  i = 0;
  deltaLat = (nextLat - prevLat)/numDeltas;
  deltaLng = (nextLng - prevLng)/numDeltas;
  // console.log("totalvalue : ", totalvalue);
  // console.log("prevLatLng : ", prevLat+","+prevLng);
  // console.log("nextLatLng : ", nextLat+","+nextLng);
  // console.log("deltaLat : ", deltaLat);
  // console.log("deltaLng : ", deltaLng);
  moveMarker();
  icon.rotation = Math.ceil(value.gps[0].gps_course);
  markernya.setIcon(icon);
  // INI UNTUK MOVE MARKER DGN SMOOTH

  // BUAT InfoWindow
  if (value.driver) {
    var datadriversplit   = value.driver.split('-');
    var datadriveronhover = datadriver[1];
  }else {
    var datadriveronhover = "Not set yet";
  }

  if (value.driverimage) {
    var imagedriveronhover = '<img src=<?php echo base_url().$this->config->item("dir_photo");?>'+value.driverimage+' width="100px" height="100px"> </br>';
  }else {
    var imagedriveronhover = "";
  }

  // console.log("sisaliterbensin in 2 : ", sisaliterbensin);
  // console.log("percenvoltase in 2 : ", percenvoltase);
  //
  // if (sisaliterbensin != "") {
  //   var ltrbensin       = Number(sisaliterbensin);
  //   var ltrbensinround  = ltrbensin.toFixed(0);
  //   var sisaliterbensin = Number(ltrbensinround)+"Ltr";
  //   var percenvoltase   = Number(percenvoltase)+"Ltr";
  // }

      titlemarker += '<table class="table" style="font-size:12px;">';
        titlemarker += '<tr>';
          titlemarker += '<td>'+imagedriveronhover+'</td>';
          titlemarker += '<td>';
            titlemarker += value.vehicle_no + ' - ' + value.vehicle_name +'</br>';
            titlemarker += 'Driver : ' + datadriveronhover +'</br>';
            titlemarker += 'Gps Time : ' + value.gps[totalcoordinate-1].gps_date_fmt + " " + value.gps[totalcoordinate-1].gps_time_fmt + '</br>';
            titlemarker += 'Position : ' + geofencestatus + addressfix + '</br>';
            titlemarker += 'Coord : ' + value.gps[totalcoordinate-1].gps_latitude_real_fmt + ", " + value.gps[totalcoordinate-1].gps_longitude_real_fmt + '</br>';
            titlemarker += 'Engine : ' + statusengine + '</br>';
            titlemarker += 'Fuel : ' + sisaliterbensin + '</br>';
            titlemarker += 'Speed : ' + rounded + ' kph </br>';
            titlemarker += 'Odometer : ' + value.totalodometer + '</br>';
            titlemarker +=  devicestatus;
            titlemarker += 'Ritase : ' + ritasefix + '</br>';
            titlemarker += 'Card No : ' + value.vehicle_card_no + '</br>';
            titlemarker += '<div onclick="closeWindowOnMarkerOOH();" style="color:blue;cursor:pointer;">Tutup Informasi</div>';
            // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + value.vehicle_id + '"target="_blank">Tracking</a> </br>';
            titlemarker +=   lct + imglct;
          titlemarker += '</td>';
        titlemarker += '</tr>';
      titlemarker += '</table>';

  google.maps.event.addListener(markernya, 'mouseover', function(){
    var varthis = this;
    setTimeoutConst = setTimeout(function() {
      // console.log("mouseover 3second on simultan");
      // console.log("masuk titlemarker", imagedriveronhover);
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
    // console.log("mouseout on simultan");
    clearTimeout(setTimeoutConst);
      infowindowonsimultan.close();
  });

  google.maps.event.addListener(markernya, 'click', function(evt){
    // console.log("ini simultan di klik");
    infowindow2.close();
    infowindowkedua.close();
    infowindow.close();

       infowindow2 = new google.maps.InfoWindow({
        content: titlemarker,
        maxWidth: 300
      });

      var center = {lat : parseFloat(value.gps[totalcoordinate-1].gps_latitude_real_fmt), lng: parseFloat(value.gps[totalcoordinate-1].gps_longitude_real_fmt)};

      infowindow2.setContent(titlemarker);
      map.setCenter(markernya.position);
      markernya.setPosition(markernya.position);
      infowindow2.open(map, this);
  });
}

function moveMarker(){
  positionmove[0] += deltaLat;
  positionmove[1] += deltaLng;
  var latlng = new google.maps.LatLng(positionmove[0], positionmove[1]);
  // console.log("Movemarker Latitude:"+positionmove[0]+" | Longitude:"+positionmove[1]);
  // console.log("globalicon : ", globalicon);
  // console.log("globalcourse : ", globalcourse);
  markernya.setPosition(latlng);
  if(iloopnya!=numDeltas){
      iloopnya++;
      setTimeout(moveMarker, delay);
  }else {
    positionmove = "";
    deltaLat     = "";
    deltaLng     = "";
    globalicon   = "";
    globalcourse = "";
    iloopnya     = 0;
  }
}

function closeWindowOnMarkerOOH(){
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
