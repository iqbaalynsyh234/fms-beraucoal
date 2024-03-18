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
              <!-- <h5>DEVELOPMENT</h5> -->
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
                <select class="form-control select2" name="contractor" id="contractor" onchange="mapsOptions()">
                </select>
              </div>

              <div class="col-md-3" id="showSearchNopol">
                <select class="form-control select2" name="searchnopol" id="searchnopol" onchange="forsearchinput()">
                </select>
                <input type="text" id="nopolforcheck" value="0" hidden>
                <input type="text" id="nopolforhide" value="0" hidden>
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

            <div id="mapshowfix">
              <input type="hidden" id="valueMode" value="0">
              <div id="mapShow" style="display:none;">
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
      var html = "";

      if (privilegecode == 5 || privilegecode == 6) {
        html += '<option value="0">--Vehicle List</option>';
        html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
          var vCompany = '<?php echo $vehicledata[$i]['vehicle_company']; ?>';
          if (vCompany == user_company) {
            html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
          }
        html += '<?php } ?>';
      }else {
        html += '<option value="0">--Vehicle List</option>';
        html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
          html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
        html += '<?php } ?>';
      }

        $("#searchnopol").html(html);
    }
  });

  intervalmapsstandard = setInterval(standardMaps, 3000);

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
      // center: { lat: parseFloat(-3.7288), lng: parseFloat(115.6452)},
      center: { lat: parseFloat(2.1193), lng: parseFloat(117.4631)},
      mapTypeId: "satellite",
      options: {
        gestureHandling: 'greedy'
      }
    });

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
    toggleButton.textContent = "OVERLAY Maps";
    toggleButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

     toggleButton.addEventListener("click", () => {
      addoverlay(map);
    });
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
        console.log("ini respon pencarian : ", response);
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

function mapsOptions(){
  clearInterval(intervaloutofhauling);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervalpoollist);
  clearInterval(intervalportlist);
  clearInterval(intervalromlist);
  clearInterval(intervalkmlist);
  clearInterval(intervalstart);
  $("#informationQC").hide();
  standardMaps();
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
  $("#tableShowPoolNew").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").show();
  $("#showSearchNOutOfHauling").hide();
  // CLEAR INTERVAL MAPS FROM HEATMAP
  clearInterval(intervalstart);
  clearInterval(intervalmapsstandard);
  heatmap.setMap(null);
  var companyid     = $("#contractor").val();
  var nopolforcheck = $("#nopolforcheck").val();

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
      var lastenginefix = "OFF";

      if (objmapsstandard[i].auto_last_road == "muatan") {
        if (roundedspeed == 0 && objmapsstandard[i].auto_last_engine == "ON") {
          // ICON UNGU
          lastenginefix = "ON";
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
          lastenginefix = "ON";
          var icon = {
            path: car,
            scale: .5,
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#00b300',
            offset: '5%'
          };
        }else if (roundedspeed > 0 && objmapsstandard[i].auto_last_engine == "OFF") {
          // ICON HIJAU
          lastenginefix = "ON";
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
          lastenginefix = "ON";
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
          lastenginefix = "ON";
          var icon = {
            path: car,
            scale: .5,
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else if (roundedspeed == 0 && objmapsstandard[i].auto_last_engine == "ON") {
          // ICON UNGU
          lastenginefix = "ON";
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
            console.log("getdetailbydevid 3");
            console.log("Maps standard marker onclik : ", response);
            DeleteMarkers(marker.id);
            DeleteMarkerspertama(marker.id);

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            if (response[0].auto_last_road) {
              if (response[0].auto_last_road == "muatan") {
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
                  lastenginefix = "ON";
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
                  lastenginefix = "ON";
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
                }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
                  lastenginefix = "ON";
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
                  lastenginefix = "ON";
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
                  lastenginefix = "ON";
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
                }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
                  lastenginefix = "ON";
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

            var ritasefix = 0;
              if (response[0].autolastritase != 0) {
                ritasefix = response[0].auto_last_ritase;
              }else {
                ritasefix = 0;
              }

            var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
              "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
              "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
              "Engine : " + lastenginefix + "<br>" +
              "Fuel : " + sisaliterbensin + " Ltr<br>" +
              "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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

              var ritasefix = 0;
                if (response[0].autolastritase != 0) {
                  ritasefix = response[0].auto_last_ritase;
                }else {
                  ritasefix = 0;
                }

              var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                "Engine : " + lastenginefix + "<br>" +
                "Fuel : " + sisaliterbensin + " Ltr<br>" +
                "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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
  clearInterval(intervalkmlist);
  clearInterval(intervalromlist);
  clearInterval(intervalportlist);
  clearInterval(intervalpoollist);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervaloutofhauling);
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
  $("#tableShowPoolNew").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowVehicleOffline").hide();
  $("#showSearchNopol").show();
  $("#showSearchNOutOfHauling").hide();
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
      var lastenginefix = "OFF";

      if (objmapsstandard[i].auto_last_road == "muatan") {
        if (roundedspeed == 0 && objmapsstandardsimultan[i].auto_last_engine == "ON") {
          lastenginefix = "ON";
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
          lastenginefix = "ON";
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
        }else if (roundedspeed > 0 && objmapsstandardsimultan[i].auto_last_engine == "OFF") {
          lastenginefix = "ON";
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
          lastenginefix = "ON";
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
          lastenginefix = "ON";
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
        }else if (roundedspeed == 0 && objmapsstandardsimultan[i].auto_last_engine == "OFF") {
          lastenginefix = "ON";
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

      var nopolforhide = $("#nopolforhide").val();
        if (nopolforhide != objmapsstandardsimultan[i].vehicle_device) {
          marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: icon,
            title: objmapsstandardsimultan[i].vehicle_no,
            id: objmapsstandardsimultan[i].vehicle_device
          });
        }


      icon.rotation = Math.ceil(objmapsstandardsimultan[i].auto_last_course);
      marker.setIcon(icon);
      markers.push(marker);

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          var data = {device_id : marker.id};
          $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
            console.log("getdetailbydevid 4");
            console.log("Maps standard marker onclik : ", response);
            DeleteMarkers(marker.id);
            DeleteMarkerspertama(marker.id);

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            if (response[0].auto_last_road) {
              if (response[0].auto_last_road == "muatan") {
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
                  lastenginefix = "ON";
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
                  lastenginefix = "ON";
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
                }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
                  lastenginefix = "ON";
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
                  lastenginefix = "ON";
                  // ICON KUNING
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
                  lastenginefix = "ON";
                  // ICON BIRU
                  var icon = {
                    path: car,
                    scale: .5,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#0000FF',
                    offset: '5%'
                  };
                }else if (rounded > 0 && response[0].auto_last_engine == "OFF") {
                  lastenginefix = "ON";
                  // ICON BIRU
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
                  // ICON MERAH
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

            var ritasefix = 0;
              if (response[0].autolastritase != 0) {
                ritasefix = response[0].auto_last_ritase;
              }else {
                ritasefix = 0;
              }

            var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
              "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
              "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
              "Engine : " + lastenginefix + "<br>" +
              "Fuel : " + sisaliterbensin + " Ltr<br>" +
              "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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

              var ritasefix = 0;
                if (response[0].autolastritase != 0) {
                  ritasefix = response[0].auto_last_ritase;
                }else {
                  ritasefix = 0;
                }

              var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                "Engine : " + lastenginefix + "<br>" +
                "Fuel : " + sisaliterbensin + " Ltr<br>" +
                "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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
    var nopolforcheck = $("#nopolforcheck").val();
    updateinfowindow(nopolforcheck);
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

        $("#searchnopol").html("");

          if (privilegecode == 5 || privilegecode == 6) {
            var loop = 1;
              html += '<option value="0">--Vehicle List</option>';
              for (var i = 0; i < data.length; i++) {
                var vCompany = data[i].vehicle_company;
                if (vCompany == user_company) {
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
          intervalstart = setInterval(simultango, 15000);
      },"json");
    }else if ( mapsOptionsValue == "showTableMuatan1") {
      valuemapsoption = 2;
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);

      jQuery("#loader2").show();

      $.post("<?php echo base_url() ?>maps/km_quickcount_new", {companyid : companyid}, function(response){
        jQuery("#loader2").hide();
            console.log("response km_quickcount_new for companyid: ", response);
            var datamuatan         = response.dataMuatan;
            var datakosongan       = response.dataKosongan;
            var datafixlimit  = response.datafixlimit;
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

            var dataRom             = response.dataRominQuickCount;
            var dataRomfix          = Object.entries(dataRom);
            var jumlahDataInPortBIB = 0;
            var jumlahDataInPortBIR = 0;
            var jumlahDataInRom     = 0;

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

              var htmlPortinQuickCount = "";
              htmlPortinQuickCount += '<table>';
                htmlPortinQuickCount += '<tr>';
                  for (var j = 0; j < dataPortfix.length; j++) {
                    // console.log("dataRomfix : ", dataRomfix[i][0]);
                    htmlPortinQuickCount += '<td><b>'+dataPortfix[j][0]+'</b><td>';
                    htmlPortinQuickCount += '<td>&nbsp;:&nbsp;<td>';
                    if (i == (dataPortfix.length - 1)) {
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

                $("#lastupdateconsolidated").html("Kosongan : " + (totalKosongan + totalKosongan2) +" || Muatan : " + (totalMuatan + totalMuatan2) + " || Total : " + (totalKosongan + totalMuatan + totalKosongan2 + totalMuatan2) + " || Last Refresh : " + response.lastcheck) ;
                $("#lastupdateconsolidated").show();
                intervalkmlist = setInterval(km_quickcount_simultan, 15000);
          },"json");
    }else if ( mapsOptionsValue == "showTableRom") {
      valuemapsoption = 3;
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);

      $.post("<?php echo base_url() ?>maps/getstreetautomaticbycompanyid", {typeofstreet:3, companyid : companyid}, function(response){
        console.log("response In romlist_simultan By Contractor : ", response);
        $("#contentrom").html("");
        var limittengahrom      = 1000;
        var limitatasrom        = 1000;
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
        intervalromlist = setInterval(romlist_simultan, 15000);
      },"json");
    }else if ( mapsOptionsValue == "showTablePort") {
      valuemapsoption = 4;
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);

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
                          if (datainport[i][0] == dataLimitInPort[j].mapsetting_name_alias) {
                            var middle_limit_in_port = dataLimitInPort[j].mapsetting_middle_limit;
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
        intervalportlist = setInterval(portlist_simultan, 15000);
      },"json");
    }else if (mapsOptionsValue == "showTablePool") {
      valuemapsoption = 5;
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);

      soundisactive = 0;
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/getpoolnew", {typeofstreet:2, companyid : companyid}, function(response){
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
                htmlpool += '<span class="info-box-text" style="font-size:15px;">'+datainpool[i][0]+'</span>';
                htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+datainpool[i][1]+'</b></span>';
              htmlpool += '</div>';
            htmlpool += '</div>';
          htmlpool += '</div>';
        }
        $("#btnpoolback").hide();
        $("#contentpoolnew").html(htmlpool);
        $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool);
        $("#jumlahtotalinpoolnew").show();
        intervalpoollist = setInterval(poollist_simultan_new, 15000);
      },"json");
    }else if ( mapsOptionsValue == "outofhauling") {
      valuemapsoption = 6;
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalmapsstandard);

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

                 var ritasefix = 0;
                   if (response[0].autolastritase != 0) {
                     ritasefix = response[0].auto_last_ritase;
                   }else {
                     ritasefix = 0;
                   }

                 var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br>"+
                   "Coord : <a href='https://www.google.com/maps/search/?api=1&query="+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"' target='_blank'>"+response[0].auto_last_lat+ ',' +response[0].auto_last_long+"</a></span></br>"+
                   // "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                   "Engine : " + response[0].auto_last_engine + "<br>" +
                   "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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
      clearInterval(intervalmapsstandard);
      clearInterval(intervaloutofhauling);
      clearInterval(intervalpoollist);
      clearInterval(intervalportlist);
      clearInterval(intervalromlist);
      clearInterval(intervalkmlist);
      clearInterval(intervalstart);
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
      soundisactive = 0;
      var contractor = $("#contractor").val();
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>maps/offlinevehicle_quickcount", {contractor : contractor}, function(response){
        jQuery("#loader2").hide();
        console.log("response offline vehicle 1 : ", response);
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
    }else if ( mapsOptionsValue == "standardmaps") {
      valuemapsoption = 8;
      clearInterval(intervalstart);
      clearInterval(intervalkmlist);
      clearInterval(intervalromlist);
      clearInterval(intervalportlist);
      clearInterval(intervalpoollist);
      clearInterval(intervalofflinevehicle);
      clearInterval(intervaloutofhauling);
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

                var ritasefix = 0;
                  if (response[0].autolastritase != 0) {
                    ritasefix = response[0].auto_last_ritase;
                  }else {
                    ritasefix = 0;
                  }

                var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                  "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                  "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                  "Engine : " + response[0].auto_last_engine + "<br>" +
                  "Fuel : " + sisaliterbensin + " Ltr<br>" +
                  "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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

                  var ritasefix = 0;
                    if (response[0].autolastritase != 0) {
                      ritasefix = response[0].auto_last_ritase;
                    }else {
                      ritasefix = 0;
                    }

                  var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                    "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br> Jalur : " + response[0].auto_last_road + "<br>"+
                    "Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                    "Engine : " + response[0].auto_last_engine + "<br>" +
                    "Fuel : " + sisaliterbensin + " Ltr<br>" +
                    "Speed : " + rounded + " kph" + "<br> Ritase : " + ritasefix + "</br>" +
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
