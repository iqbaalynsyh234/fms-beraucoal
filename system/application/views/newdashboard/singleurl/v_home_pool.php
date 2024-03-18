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

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
  $(document).ready(function() {
    setTimeout(function(){
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
  });

  intervalpoollist = setInterval(showTablePool, 3000);

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

function mapsOptions(){
  clearInterval(intervalpoollist);
  showTablePool();
}

function showTablePool(){
  clearInterval(intervalpoollist);
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
      intervalpoollist = setInterval(poollist_simultan_new, 15000);
    },"json");
}

function poollist_simultan_new(){
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
    $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool + " || Last Refresh : " + response.lastcheck);
    $("#jumlahtotalinpoolnew").show();
  },"json");
}

function getPoolOther(){
  clearInterval(intervalstart);
  clearInterval(intervalkmlist);
  clearInterval(intervalromlist);
  clearInterval(intervalportlist);
  clearInterval(intervalpoollist);
  clearInterval(intervalofflinevehicle);
  clearInterval(intervaloutofhauling);
  clearInterval(intervalmapsstandard);
  var companyid = $("#contractor").val();
  jQuery("#loader2").show();
  $.post("<?php echo base_url() ?>maps/getPoolOther", {companyid : companyid}, function(response){
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

function getVehicleByPool(id){
  var contractor = $("#contractor").val();
  $.post("<?php echo base_url() ?>maps/vehicleonpool", {idpool : id, contractor : contractor}, function(response){
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
          var enginekmfix  = datafix[i].auto_last_engine;
          var speedpoolfix = datafix[i].auto_last_speed;
          var lastenginefix;

            if (enginekmfix == "OFF" && speedpoolfix > 0) {
              lastenginefix = "ON";
            }else {
              lastenginefix = enginekmfix;
            }

            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+lastenginefix+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+speedpoolfix+'</span>';
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

function getPoolByParent(id){
  clearInterval(intervalpoollist);
  jQuery("#loader2").show();
  $("#poolparentID").val(id);
  var contractor = $("#contractor").val();
  $.post("<?php echo base_url() ?>maps/getChildPool", {poolparent : id, contractor : contractor}, function(response){
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

    // htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="listOutOfHaulingByContractor('+id+')" style="cursor : pointer;">';
    //   htmlpool += '<div class="info-box bg-blue">';
    //     htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
    //       htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
    //     htmlpool += '</span>';
    //     htmlpool += '<div class="info-box-content">';
    //       htmlpool += '<span class="info-box-text" style="font-size:15px;">Others</span>';//asd
    //       htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+response.dataoutofhauling.outofhauling+'</b></span>';
    //     htmlpool += '</div>';
    //   htmlpool += '</div>';
    // htmlpool += '</div>';

    $("#btnpoolback").show();

    $("#contentpoolnew").html(htmlpool);
    $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool);
    $("#jumlahtotalinpoolnew").show();
    intervalpoollist = setInterval( function() { poolinparent_simultan(id); }, 15000 );
  }, "json");
}

function poolinparent_simultan(id){
  clearInterval(intervalpoollist);
  jQuery("#loader2").show();
  var poolparentID = $("#poolparentID").val();
  var contractor   = $("#contractor").val();
  console.log("poolparent simultan : ", poolparentID);
  $.post("<?php echo base_url() ?>maps/getChildPool", {poolparent : poolparentID, contractor : contractor}, function(response){
    console.log("response getPoolByParent simultan : ", response);
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

    // htmlpool += '<div class="col-xl-3 col-md-6 col-12" onclick="listOutOfHaulingByContractor('+poolparentID+')" style="cursor : pointer;">';
    //   htmlpool += '<div class="info-box bg-blue">';
    //     htmlpool += '<span class="info-box-icon push-bottom" style="margin-top:4%;">';
    //       htmlpool += '<i class="material-icons" style="font-size:50px; padding-top:10px;">store_mall_directory</i>';
    //     htmlpool += '</span>';
    //     htmlpool += '<div class="info-box-content">';
    //       htmlpool += '<span class="info-box-text" style="font-size:15px;">Others</span>';
    //       htmlpool += '<span class="info-box-number" style="font-size:22px;"><b>'+response.dataoutofhauling.outofhauling+'</b></span>';
    //     htmlpool += '</div>';
    //   htmlpool += '</div>';
    // htmlpool += '</div>';

    $("#btnpoolback").show();

    $("#contentpoolnew").html(htmlpool);
    $("#jumlahtotalinpoolnew").html("Total : " +jumlahtotalinpool + " || Last Refresh : " + response.lastcheck);
    $("#jumlahtotalinpoolnew").show();
    // intervalpoollist = setInterval( function() { poolinparent_simultan(id); }, 15000 );
  }, "json");
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
