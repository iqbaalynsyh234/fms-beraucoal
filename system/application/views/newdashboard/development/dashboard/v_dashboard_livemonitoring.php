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
  width: 140%;
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

  #modalinterventionmanual {
    margin-top: 1.5%;
    margin-left: 2%;
    height: 520px;
    max-height: 100%;
    position: fixed;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
    /* overflow-y: auto; */
    /* width: 50%; */
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

      <div id="modalinterventionmanual" style="display: none; width:70%;">
         <!-- style="display: none;" -->
        <div class="row">
          <div class="col-md-12">
              <div class="card card-topline-yellow">
                  <div class="card-head">
                      <header id="titleheader">Form Intervensi (Manual)</header>
                      <div class="tools">
                          <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                        <button type="button" class="btn btn-danger" name="button" onclick="closemodalinterventionmanual();">X</button>
                      </div>
                  </div>
                  <div class="card-body">
                    <div id="contentpostevent">
                      <div class="row" style="overflow-y:auto; height:516px;">

                        <div class="col-md-12">
                           <!-- style="height:410px" -->
                          <p class="text-center">
                            <b>Pelaksanaan Intervensi DMS (Manual)</b>
                          </p>
                          <div class="text-center" id="notif" style="display:none;"></div>
                          <form method="post" action="" enctype="multipart/form-data">
                            <table class="table table-striped" style="font-size:12px;">
                              <tr>
                                <td>Vehicle</td>
                                <td>
                                  <select class="form-control select2" name="intervention_vehicle" id="intervention_vehicle" style="width:180px;">
                                    <?php for ($i=0; $i < sizeof($data_vehicle); $i++) {?>
                                      <option value="<?php echo $data_vehicle[$i]->vehicle_device; ?>"><?php echo $data_vehicle[$i]->vehicle_no; ?></option>
                                    <?php } ?>
                                  </select>
                                </td>
                                <td></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td>SID</td>
                                <!-- <td>True / False Alarm</td> -->
                                <td>Intervensi *Wajib Dipilih</td>
                                <td>Alert</td>
                                <td>Notes</td>
                              </tr>
                              <tr>
                                <td>
                                  <select class="form-control select2" name="intervention_sid" id="intervention_sid" style="width:180px;">
                                    <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                                      <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </td>
                                <td>
                                  <select class="form-control select2" name="intervention_category" id="intervention_category" style="width:180px;" onchange="change_type_intervention();">
                                    <?php for ($i=0; $i < sizeof($type_intervention); $i++) {?>
                                      <option value="<?php echo $type_intervention[$i]['intervention_type_id'].'|'.$type_intervention[$i]['intervention_type_name'] ?>"><?php echo $type_intervention[$i]['intervention_type_name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </td>
                                <td>
                                  <select class="form-control select2" name="alert_name" id="alert_name" style="width:180px; font-size:10px;">
                                    <option value="Call">Call</option>
                                    <option value="Car Distance">Car Distance</option>
                                    <option value="Fatigue - Mata Memejam">Fatigue - Mata Memejam</option>
                                    <option value="Fatigue - Menguap">Fatigue - Menguap</option>
                                    <option value="Fatigue - Kepala Menunduk">Fatigue - Kepala Menunduk</option>
                                    <option value="Forward Collision">Forward Collision</option>
                                    <option value="Hands Off">Hands Off</option>
                                    <option value="Smoking">Smoking</option>
                                    <option value="Unfastened Seatbelt">Unfastened Seatbelt</option>
                                  </select>
                                </td>
                                <td>
                                  <select class="form-control select2" name="intervention_note" id="intervention_note" style="width:180px;">
                                    <?php for ($i=0; $i < sizeof($type_note); $i++) {?>
                                      <option value="<?php echo $type_note[$i]['type_note_name'] ?>"><?php echo $type_note[$i]['type_note_name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </td>
                                <td></td>
                                <td></td>
                              </tr>

                              <tr>
                                <td>Judgement</td>
                                <td>
                                  <select class="form-control select2" name="intervention_judgement" id="intervention_judgement" style="width:180px;">
                                    <option value="Low Risk">Low Risk</option>
                                    <option value="Medium Risk">Medium Risk</option>
                                    <option value="High Risk">High Risk</option>
                                  </select>
                                </td>

                                <td>Supervisor</td>
                                <td>
                                  <select class="form-control select2" name="intervention_supervisor" id="intervention_supervisor" style="width:180px;">
                                    <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                                      <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>

                                <tr>
                                  <td>Evidence</td>
                                  <td>
                                    <input type="file" name="intervention_evidence" id="intervention_evidence"> <br>
                                    <p style="color:red; font-size:10px;">*File JPG/JPEG/PNG Res. 300x300 pixels</p>
                                  </td>
                                  <td>Tanggal</td>
                                  <td>
                                    <input type="text" name="intervention_date" id="intervention_date" class="form-control" value="<?php echo date("Y-m-d H:i:s") ?>" readonly>
                                  </td>
                                </tr>

                                <tr>
                                  <td>Remark</td>
                                  <td>
                                    <textarea name="manual_evidence_remarks" id="manual_evidence_remarks" rows="8" cols="40"></textarea>
                                  </td>
                                  <td></td>
                                  <td></td>
                                </tr>

                                <tr>
                                  <td>
                                    <div id="preview" style="display:none;">
                                        <img src="" id="img_preview" width="auto" height="200px">
                                        <input type="text" id="manual_image_path" hidden>
                                    </div>
                                  </td>
                                  <td></td>
                                  <td></td>
                                  <td class="text-right">
                                    <!-- <button type="button" class="btn btn-small btn-default" name="button" onclick="btnReset();">Reset</button> -->
                                    <div class="btn btn-small btn-primary" name="button" onclick="btnSubmitManual();">Submit</div>
                                  </td>
                                </tr>
                            </table>
                          </form>

                        </div>
                      </div>
                    </div>
                  </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card">
          <div class="card-head" style="background-color:#221f1f; color:white;">
            <header>
              <h5>Dashboard Live Monitoring - DEVELOPMENT</h5>
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
                <select class="form-control select2" name="contractor" id="contractor" onchange="stream_by_mitra()">
                </select>
              </div>

              <div class="col-md-2" id="showSearchNopol">
                <select class="form-control select2" name="searchnopol[]" id="searchnopol" multiple="multiple" onchange="forsearchinput()">
                </select>
                <input type="text" id="nopolforcheck" value="0" hidden>
                <input type="text" id="nopolforhide" value="0" hidden>
              </div>

              <div class="col-md-2" id="showchanel">
                <select class="form-control select2" name="chanel" id="chanel" onchange="stream_by_mitra()">
                  <option value="all">--All Chanel</option>
                  <option value="0">ADAS</option>
                  <option value="1">DSM</option>
                  <option value="2">HOD</option>
                </select>
              </div>

              <div class="col-md-2" id="showfilter_unit">
                <select class="form-control select2" name="filter_unit" id="filter_unit" onchange="stream_by_mitra()">
                  <option value="all">--All Type</option>
                  <?php for ($i=0; $i < sizeof($filter_unit); $i++) {?>
                    <option value="<?php echo $filter_unit[$i]['vehicle_name'] ?>"><?php echo $filter_unit[$i]['vehicle_name'] ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-md-2" id="showSiteOption">
                <select class="form-control select2" name="site_option" id="site_option" onchange="stream_by_mitra()">
                  <option value="all">--All Site</option>
                  <?php for ($i=0; $i < sizeof($data_site_bc); $i++) {?>
                    <option value="<?php echo $data_site_bc[$i]['site_name'] ?>"><?php echo $data_site_bc[$i]['site_name'] ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-md-1">
                <button type="button" class="btn btn-info btn-xs btn-circle" name="button" onclick="btnInfo()">
                  Info
                  <span class="fa fa-question"></span>
                </button>
              </div>

              <div class="col-md-2">
                <img id="loader2" style="display:none;" src="<?php echo base_url();?>assets/images/anim_wait.gif" />
              </div>
            </div>

            <div class="row">
              <div class="col-md-2">
                <br>
                <div type="button" class="btn btn-success btn-sm" name="button" onclick="btnInputInterventionManual()">
                  Intervention Manual
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

    <div class="row">
      <div class="col-md-12">
        <div id="resultlivemonitoring" style="width:100%; height:400px">
          <p id="textisshowvideo" style="font-size:14px; display:none;"></p>
          <!-- <iframe src="http://172.16.1.2/808gps/open/player/video.html?lang=en&devIdno=625060488255&jsession=c7480f8a558b4b71a2a7e8f7573ef53b" width="300px" height="300px"></iframe> -->
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
          html += '<option value="all">--Select Contractor</option>';
            html += '<option value="all">All Contractor</option>';
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
        html += '<option value="all">--Vehicle List</option>';
        html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
          var vCompany = '<?php echo $vehicledata[$i]['vehicle_company']; ?>';
          // console.log("masuk bang 1", vCompany + " " + user_company);

          if (vCompany == user_company) {
            html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
          }
        html += '<?php } ?>';
      }else {
        html += '<option value="all">--Vehicle List</option>';
        html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
          html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
        html += '<?php } ?>';
      }

        $("#searchnopol").html(html);
    }

    $("#intervention_evidence").on("change", function(){
      var fd = new FormData();
      var files = $('#intervention_evidence')[0].files[0];
      fd.append('file',files);
      console.log("files : ", files);
      console.log("data : ", fd);
      $.ajax({
          url: '<?php echo base_url() ?>development/manual_upload_evidence',
          type: 'post',
          data: fd,
          contentType: false,
          processData: false,
          success: function(response){
              if(response == 0){
                alert('file not uploaded');
              }else if (response == 100) {
                alert('Harap memilih file dengan tipe jpg/jpeg/png');
              }else{
                  var url = "<?php echo base_url() ?>"+response;
                  // console.log("url : ", url);
                    $("#img_preview").attr("src",url);
                    $("#manual_image_path").val(url);
                    $("#preview").show(); // Display image element
              }
          },
      });
    });

    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });

  });

  function btnSubmitManual(){
    // $("#resultreport").hide();
    // $("#loadernya").show();
    var tablenya                = 'alarm_evidence_manual';
    var user_id                 = '<?php echo $this->sess->user_id ?>';
    var user_name               = '<?php echo $this->sess->user_name ?>';
    var intervention_vehicle    = $('#intervention_vehicle').val();
    var intervention_sid        = $('#intervention_sid').val();
    var intervention_category   = $('#intervention_category').val();
    var alert_name              = $('#alert_name').val();
    var intervention_note       = $('#intervention_note').val();
    var intervention_judgement  = $('#intervention_judgement').val();
    var intervention_supervisor = $('#intervention_supervisor').val();
    var intervention_date       = $('#intervention_date').val();
    var manual_image_path       = $('#manual_image_path').val();
    var manual_evidence_remarks = $('#manual_evidence_remarks').val();

      var data = {
        tablenya                : tablenya,
        user_id                 : user_id,
        user_name               : user_name,
        intervention_vehicle    : intervention_vehicle,
        intervention_sid        : intervention_sid,
        intervention_category   : intervention_category,
        alert_name              : alert_name,
        intervention_note       : intervention_note,
        intervention_judgement  : intervention_judgement,
        intervention_supervisor : intervention_supervisor,
        intervention_date       : intervention_date,
        manual_image_path       : manual_image_path,
        manual_evidence_remarks : manual_evidence_remarks,
      };

      console.log("data : ", data);
      $.post("<?php echo base_url() ?>development/submit_manual_intervention", data, function(response){
        console.log("response : ", response);
        if (response.error) {
          $("#loader2").hide();
          var alert = response.message;
          $("#notif").html(alert);
          $("#notif").fadeIn(1000);
          $("#notif").fadeOut(3000);
        }else {
          $("#loader2").hide();
          var alert = response.message;
          $("#notif").html(alert);
          $("#notif").fadeIn(1000);
          $("#notif").fadeOut(3000);
          $("#intervention_evidence").val("");
          setTimeout(function () {
            $("#preview").hide(); // Display image element
            $("#modalinterventionmanual").hide();
          }, 3000);
          // $("#intervention_sid").val("");
          // $("#intervention_note").val("");
          // frmsearch_onsubmit();
        }
        return false;
      }, "json");
  }

  function btnInputInterventionManual(){
    $("#modalinterventionmanual").show();
  }

  function closemodalinterventionmanual(){
    $("#modalinterventionmanual").hide();
    // $("#modalinterventionmanual").fadeOut(1000);
  }

  function btnInfo(){
    var info = "Tekan tombol Ctrl untuk memilih lebih dari satu nomor lambung";
    alert(info);
  }

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
  $("#mapshowfix").addClass('col-md-12');

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

  function stream_by_mitra(){
    var id_mitra    = $("#contractor").val();
    var chanel      = $("#chanel").val();
    var vehicle     = $("#searchnopol").val();
    var filter_unit = $("#filter_unit").val();
    var site_option = $("#site_option").val();

      if (vehicle.length < 1) {
        vehicle = "all";
      }

    var data = {
      id_mitra : id_mitra,
      chanel : chanel,
      vehicle : vehicle,
      filter_unit : filter_unit,
      site_option : site_option
    };
    // console.log("vehicle : ", vehicle);
    jQuery("#loader2").show();
    $.post("<?php echo base_url() ?>development/get_stream_by_mitra", data, function(r){
      jQuery("#loader2").hide();
      // console.log("ini respon pencarian : ", r);
      if (r.isshowvideo == 1) {
        $("#textisshowvideo").hide();
        var htmllivemonitoring = r.livemonitoring;
        $("#resultlivemonitoring").html(htmllivemonitoring);
      }else {
        $("#textisshowvideo").html(r.message);
        $("#textisshowvideo").show();
      }
    }, "json");

  }

function forsearchinput(){
  var deviceid = $("#searchnopol").val();
    // if (deviceid == 0) {
    //   alert("Silahkan pilih kendaraan terlebih dahulu");
    // }else {
      console.log("device id forsearchinput : ", deviceid);
      $("#nopolforcheck").val(deviceid);
      var filter_unit = $("#filter_unit").val();
      var site_option = $("#site_option").val();

      var data = {
        key : deviceid,
        filter_unit : filter_unit,
        site_option : site_option,
      };
      jQuery("#loader2").show();
      $.post("<?php echo base_url() ?>development/forsearchvehicle", data, function(r){
        jQuery("#loader2").hide();
        console.log("ini respon pencarian : ", r);
        if (r.isshowvideo == 1) {
          $("#textisshowvideo").hide();
          var htmllivemonitoring = r.livemonitoring;
          $("#resultlivemonitoring").html(htmllivemonitoring);
        }else {
          $("#textisshowvideo").html(r.message);
          $("#textisshowvideo").show();
        }
      }, "json");
      return false;
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
    // }
}

function change_type_intervention(){
  var intervention_category = $("#intervention_category").val();
  var interv_cat            = intervention_category.split("|");
  var data = {
    interv_type_id : interv_cat[0]
  };
  // console.log("interv_cat : ", interv_cat);
  $.post("<?php echo base_url() ?>dashboardberau/data_intervention_note", data, function(response){
    // console.log("response data_intervention_note : ", response);
      var data = response.data;
      $("#intervention_note").html("");

      var html = "";
      for (var i = 0; i < data.length; i++) {
        html += '<option value="'+data[i].type_note_name+'">'+data[i].type_note_name+'</option>';
      }
      $("#intervention_note").html(html);
  }, "json");
}

</script>
