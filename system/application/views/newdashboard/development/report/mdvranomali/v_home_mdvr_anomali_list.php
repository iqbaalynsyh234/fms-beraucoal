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
    left: 74%;
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

  .timeline > li > .timelinebadge_rom {
    color: #fff;
    width: 112px;
    padding: 10px 7px 0px 4px;
    height: 25px;
    line-height: 2px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 50%;
    margin-left: -25px;
    z-index: 1;
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

  .timeline > li > .timeline-badge2_rom {
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
    left: 88%;
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

  .timeline > li > .timeline-badge3_rom {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 25px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 88%;
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
      left: 86%;
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
      left: 86%;
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

  .timeline > li > .timelinebadge_rom {
    color: #fff;
    width: 112px;
    padding: 10px 7px 0px 4px;
    height: 25px;
    line-height: 2px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 50%;
    margin-left: -25px;
    z-index: 1;
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

  .timeline > li > .timeline-badge2_rom {
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
    left: 72%;
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

  .timeline > li > .timeline-badge3_rom {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 25px;
    font-size: 14px;
    text-align: center;
    position: absolute;
    left: 72%;
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
      left: 76%;
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
      left: 76%;
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

  .blob {
    background: black;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(0, 0, 0, 1);
    margin: 10px;
    height: 25px;
    width: 25px;
    transform: scale(1);
    animation: pulse-black 2s infinite;
  }

  .blob.red {
    background: rgba(255, 82, 82, 1);
    box-shadow: 0 0 0 0 rgba(255, 82, 82, 1);
    animation: pulse-red 2s infinite;
  }

  @keyframes pulse-red {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(255, 82, 82, 0.7);
    }

    70% {
      transform: scale(1);
      box-shadow: 0 0 0 10px rgba(255, 82, 82, 0);
    }

    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(255, 82, 82, 0);
    }
  }

  .blob.yellow {
    background: rgba(255, 177, 66, 1);
    box-shadow: 0 0 0 0 rgba(255, 177, 66, 1);
    animation: pulse-yellow 2s infinite;
  }

  @keyframes pulse-yellow {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(255, 177, 66, 0.7);
    }

    70% {
      transform: scale(1);
      box-shadow: 0 0 0 10px rgba(255, 177, 66, 0);
    }

    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(255, 177, 66, 0);
    }
  }

  #modalinfodetail {
    margin-top: 1%;
    margin-left: 2%;
    height: 300px;
    max-height: 100%;
    position: fixed;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 99999999;
    overflow-y: auto;
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
      <div class="col-sm-12">
        <div class="card">
          <div class="card-head" style="text-align:center; font-size:large;">
            <header>
              <b>
                Dashboard MDVR Anomali -  DEVELOPMENT
              </b>
            </header>
            <div class="tools">
              <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <!-- <a class="t-close btn-color fa fa-times" href="javascript:;"></a> -->
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <input type="text" id="filtershowhide" value="0" hidden>
              <div class="col-md-2">
                <select class="form-control select2" name="contractor" id="contractor" onchange="onchangefilter()">
                </select>
              </div>

              <div class="col-lg-2 col-md-2">
                <select id="violationmasterselect" name="violationmasterselect" class="form-control select2" onchange="onchangefilter()">
                  <option value="all">--All Anomali List--</option>
                  <?php
                  $cmdvranomali = count($mdvr_anomali_list);
                  for ($i = 0; $i < $cmdvranomali; $i++) {
                    $alarm_name = explode("Start", $mdvr_anomali_list[$i]['alarm_name']);
                    echo "<option value='" . $mdvr_anomali_list[$i]['alarm_type'] . "'>" . $alarm_name[0] . "</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="col-lg-2 col-md-2">
                <select id="limitshowdata" name="limitshowdata" class="form-control select2" onchange="onchangefilter()">
                    <option value="20">-- Show Data</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                </select>
              </div>

              <div class="col-lg-2 col-md-2">
                <div class="btn btn-info btn-sm btn-circle" title="Informasi" onclick="modalinfodetail()">
                  Info ?
                </div>
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
    <div class="card">
      <div class="card-head">
        <header>
          <div id="last_refresh" style="font-size:12px;"></div>
        </header>
        <div class="tools">
          <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped" style="width:100%; font-size:12px;">
          <thead>
            <tr>
              <th>No</th>
              <th>Vehicle</th>
              <th>Time</th>
              <th>Alert</th>
              <th>Position</th>
              <!-- <th>Level</th>
              <th>Speed</th> -->
            </tr>
          </thead>
          <tbody id="data_result">

          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-2" style="display:none;">
    <div class="card">
      <div class="card-head">
        <header>
          <h5>Summary</h5>
        </header>
        <div class="tools">
          <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Overspeed" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummary('overspeed');">
            <button type="button" class="btn btn-danger">OV</button>
            <button type="button" class="btn btn-flat" id="total_ov">00</button>
          </div>
        </div><br>

        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Call to Call" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummaryfatigue('cc');">
            <button type="button" class="btn" style="background:orange; color:white;">CC</button>
            <button type="button" class="btn btn-flat" id="tv_call">00</button>
          </div>
        </div><br>

        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Car Distance" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummaryfatigue('cd');">
            <button type="button" class="btn btn-success">CD</button>
            <button type="button" class="btn btn-flat" id="tv_cardistance">00</button>
          </div>
        </div><br>

        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Distracted" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummaryfatigue('dt');">
            <button type="button" class="btn btn-primary">DT</button>
            <button type="button" class="btn btn-flat" id="tv_distracted">00</button>
          </div>
        </div><br>

        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Fatigue" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummaryfatigue('ft');">
            <button type="button" class="btn btn-warning">FT</button>
            <button type="button" class="btn btn-flat" id="tv_fatigue">00</button>
          </div>
        </div><br>

        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Smooking" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummaryfatigue('sm');">
            <button type="button" class="btn btn-pink">SM</button>
            <button type="button" class="btn btn-flat" id="tv_smoking">00</button>
          </div>
        </div><br>

        <div class="row">
          <div class="btn-group" role="group" aria-label="Basic example" title="Driver Undetected" style="margin-left:auto; margin-right:auto;" onclick="listVehicleOnKmSummaryfatigue('du');">
            <button type="button" class="btn" style="background:purple; color:white;">DU</button>
            <button type="button" class="btn btn-flat" id="tv_driverabnormal">00</button>
          </div>
        </div><br>
      </div>
    </div>
  </div>

  <div id="modalStateFrequency" class="modal_violation_frequency" style="display:none;">
    <div class="modal-content-statefrequency">
      <div class="row">
        <div class="col-md-10">
          <p class="modalTitleforAll" id="modalStateTitle">
            Violation Frequency
          </p>
          <!-- <div id="contractorinlocation" style="font-size:14px; color:black"></div> -->
          <div id="lastcheckpoolws" style="font-size:12px; color:black">
            <?php echo date("d-m-Y H:i:s") ?> <br>
            <div class="col-md-12">
              <b>
                Violation Frequency
                <div class="blob red" id="blobredpopup" style="display: none; margin-top: -4%; margin-left: 26%;"></div>
                <div class="blob yellow" id="blobyellowpopup" style="display: none; margin-top: -4%; margin-left: 26%;"></div>
              </b>
              <!-- style="display:none;" -->
              <!-- <img id="pulse_violation2" style="width:40px; height:auto;" src="<?php echo base_url();?>assets/bib/images/violation_pulse.gif" title="Violation Frequency Alert" onclick="violation_frequency();"/> -->
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="closethismodalall btn btn-danger btn-sm">X</div>
        </div>
      </div>
        <div id="modalStateContentFrequency">
          <div id="tableyellow" style="display:none;">
            <table class="table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Vehicle</th>
                  <th>Alert</th>
                  <th>Frequency</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>BBS 1234</td>
                  <td>Fatigue alarm Level 2</td>
                  <td>3</td>
                </tr>

                <tr>
                  <td>2</td>
                  <td>BKA 2212</td>
                  <td>Fatigue alarm Level 2</td>
                  <td>3</td>
                </tr>

                <tr>
                  <td>3</td>
                  <td>GEC 9034</td>
                  <td>Fatigue alarm Level 2</td>
                  <td>3</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div id="tablered" style="display:none;">
            <table class="table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Vehicle</th>
                  <th>Alert</th>
                  <th>Frequecny</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>BBS 1234</td>
                  <td>Fatigue alarm Level 2</td>
                  <td>4</td>
                </tr>

                <tr>
                  <td>2</td>
                  <td>BKA 2212</td>
                  <td>Fatigue alarm Level 2</td>
                  <td>4</td>
                </tr>

                <tr>
                  <td>3</td>
                  <td>GEC 9034</td>
                  <td>Fatigue alarm Level 2</td>
                  <td>4</td>
                </tr>
              </tbody>
            </table>
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

<div id="modalinfodetail" style="display: none; width:70%;">
  <div class="row">
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header id="titleheader">Informasi</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalinfodetail();">X</button>
                </div>
            </div>
            <div class="card-body">
              <div id="contentpostevent">
                <table class="table table-bordered table-striped" style="font-size:12px;">
                  <tr>
                    <td>No</td>
                    <td>Nama Anomali</td>
                    <td>Deskripsi</td>
                  </tr>
                  <tr>
                    <td>1</td>
                    <td>Main Power Failure</td>
                    <td>Terputus Arus</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Video Loss Alarm</td>
                    <td>Socket Kamera Terputus</td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>GNSS Module Fault</td>
                    <td>Module GPS Pada MDVR Bermasalah</td>
                  </tr>

                  <tr>
                    <td>4</td>
                    <td>GNSS Antenna Missed or Cut</td>
                    <td>Antena GPS pada MDVR terlepas/rusak</td>
                  </tr>
                </table>
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
      appendthecontractorlist();
      data_list_first();
    }, 3000);

    function appendthecontractorlist(){
      $.post("<?php echo base_url() ?>maps/getdatacontractor", {}, function(response){
        // console.log("response : ", response);
        var data = response.data;
        var html = "";

            html += '<option value="all">--All Contractor</option>';
            for (var i = 0; i < data.length; i++) {
              html += '<option value="'+data[i].company_id+'">'+data[i].company_name+'</option>';
            }
          $("#contractor").html(html);
      },"json");
    }
  });

  var intervalkmlist, current_totalov, invterval_violation_frequency;
  // invterval_violation_frequency          = setInterval(violation_frequency, 10000);
  intervalkmlist          = setInterval(data_list_first, 1000000);

  var simultantype        = 0;
  var last_time_violation = 0;
  var limit_show_data     = 20;
  var contractor          = "all";
  var violationmaster     = "all";

  function data_list_first(){
    var data = {
      simultantype : simultantype,
      last_time_violation : last_time_violation,
      contractor : contractor,
      violationmaster : violationmaster,
      limit_show_data : limit_show_data,
    };
    // console.log("data pertama : ", data);
    jQuery("#loader2").show();
    $.post("<?php echo base_url() ?>development/list_historikal_mdvr_anomali", data, function(response){
      jQuery("#loader2").hide();
      console.log("response list_historikal_mdvr_anomali : ", response);
      $("#data_result").html("");
      if (response.violationmix.length > 0) {
          var limitshowper = response.showper;
          var violationmix     = response.violationmix;
          $("#last_refresh").html("Last Refresh : " + violationmix[0].datetime);
          last_time_violation = violationmix[0].datetime;

          var htmltable = "";
          if (violationmix.length > limitshowper) {
            var limitloop = limitshowper;
          }else {
            var limitloop = violationmix.length;
          }

          for (var i = 0; i < limitloop; i++) {
            // var violation = violationmix[i].violation;
              // if (violation.includes("Fatigue")) {
              //   htmltable += '<tr style="background-color:red; color:white;">';
              // }else if (violation.includes("Overspeed")) {
              //   if (violationmix[i].violation_level == "Level 1" || violationmix[i].violation_level == "Level 2") {
              //     htmltable += '<tr style="background-color:yellow; color:black;">';
              //   }else {
              //     htmltable += '<tr style="background-color:red; color:white;">';
              //   }
              // }else if (violation.includes("Distracted")) {
              //   htmltable += '<tr style="background-color:red; color:white;">';
              // }else if (violation.includes("Undetected")) {
              //   htmltable += '<tr style="background-color:red; color:white;">';
              // }else if (violation.includes("Abnormal")) {
              //   htmltable += '<tr style="background-color:red; color:white;">';
              // }else if(violation.includes("Distance")) {
              //   htmltable += '<tr style="background-color:red; color:white;">';
              // }else if(violation.includes("Call")) {
              //   htmltable += '<tr style="background-color:yellow; color:black;">';
              // }else if(violation.includes("Smoking")) {
              //   htmltable += '<tr style="background-color:yellow; color:black;">';
              // }

              htmltable += '<tr style="background-color:yellow; color:black;">';
              htmltable += '<td>'+(i+1)+'</td>';
              htmltable += '<td>'+violationmix[i].vehicle_no+'</td>';
              htmltable += '<td>'+violationmix[i].datetime+'</td>';
              htmltable += '<td>'+violationmix[i].gps_alert+'</td>'
              htmltable += '<td>'+violationmix[i].position+'</td>';
              // htmltable += '<td>'+violationmix[i].violation_level+'</td>';
              // htmltable += '<td>'+violationmix[i].gps_speed+'</td>';
            htmltable += '</tr>';
          }
          $("#data_result").html(htmltable);
      }

    }, "json");
  }

  function onchangefilter(){
    contractor      = $("#contractor").val();
    violationmaster = $("#violationmasterselect").val();
    limit_show_data = $("#limitshowdata").val();
  }

  function data_list_simultan(){
    console.log("data_list_simultan");
  }

  function listVehicleOnKmSummary(violationtype){
    console.log("violationtype : ", violationtype);
    var contractor            = $("#contractor").val();
    var lasttime_violation    = $("#lasttime_violation").val();

    var dataviolationmixfix   = current_violationmix;
    var kmonsearch;
    var dataVehicleOnKosongan = [];
    var dataVehicleOnMuatan   = [];

    console.log("dataviolationmixfix : ", dataviolationmixfix);

    // if (idkm == 1) {
		// 	kmonsearch = ["Port BIB - Antrian", "Port BIB - Kosongan 1", "Port BIB - Kosongan 2", "Port BIR - Antrian WB", "Port BIR - Kosongan 1", "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan"];
		// }else if (idkm == 2) {
		// 	kmonsearch = ["KM 1", "KM 1.5", "KM 0.5"];
		// }else if (idkm == 3) {
		// 	kmonsearch = ["KM 2", "KM 2.5"];
		// }else if (idkm == 4) {
		// 	kmonsearch = ["KM 3", "KM 3.5"];
		// }else if (idkm == 5) {
		// 	kmonsearch = ["KM 4", "KM 4.5"];
		// }else if (idkm == 6) {
		// 	kmonsearch = ["KM 5", "KM 5.5"];
		// }else if (idkm == 7) {
		// 	kmonsearch = ["KM 6", "KM 6.5"];
		// }else if (idkm == 8) {
		// 	kmonsearch = ["KM 7", "KM 7.5"];
		// }else if (idkm == 9) {
		// 	kmonsearch = ["KM 8", "KM 8.5"];
		// }else if (idkm == 10) {
		// 	kmonsearch = ["KM 9", "KM 9.5"];
		// }else if (idkm == 11) {
		// 	kmonsearch = ["KM 10", "KM 10.5"];
		// }else if (idkm == 12) {
		// 	kmonsearch = ["KM 11", "KM 11.5"];
		// }else if (idkm == 13) {
		// 	kmonsearch = ["KM 12", "KM 12.5"];
		// }else if (idkm == 14) {
		// 	kmonsearch = ["KM 13", "KM 13.5"];
		// }else if (idkm == 15) {
		// 	kmonsearch = ["KM 14", "KM 14.5"];
		// }else if (idkm == 16) {
		// 	kmonsearch = ["KM 15", "KM 15.5"];
		// }else if (idkm == 17) {
		// 	kmonsearch = ["KM 16", "KM 16.5"];
		// }else if (idkm == 18) {
		// 	kmonsearch = ["KM 17", "KM 17.5"];
		// }else if (idkm == 19) {
		// 	kmonsearch = ["KM 18", "KM 18.5"];
		// }else if (idkm == 20) {
		// 	kmonsearch = ["KM 19", "KM 19.5"];
		// }else if (idkm == 21) {
		// 	kmonsearch = ["KM 20", "KM 20.5"];
		// }else if (idkm == 22) {
		// 	kmonsearch = ["KM 21", "KM 21.5"];
		// }else if (idkm == 23) {
		// 	kmonsearch = ["KM 22", "KM 22.5"];
		// }else if (idkm == 24) {
		// 	kmonsearch = ["KM 23", "KM 23.5"];
		// }else if (idkm == 25) {
		// 	kmonsearch = ["KM 24", "KM 24.5"];
		// }else if (idkm == 26) {
		// 	kmonsearch = ["KM 25", "KM 25.5"];
		// }else if (idkm == 27) {
		// 	kmonsearch = ["KM 26", "KM 26.5"];
		// }else if (idkm == 28) {
		// 	kmonsearch = ["KM 27", "KM 27.5"];
		// }else if (idkm == 29) {
		// 	kmonsearch = ["KM 28", "KM 28.5"];
		// }else if (idkm == 30) {
		// 	kmonsearch = ["KM 29", "KM 29.5"];
		// }

      for (var i = 0; i < dataviolationmixfix.length; i++) {
        var isfatigue      = dataviolationmixfix[i].isfatigue;
          if (isfatigue == "no") {
            var jalur_name      = dataviolationmixfix[i].jalur_name;
            var lastposition    = dataviolationmixfix[i].position;
            var vehicle_company = dataviolationmixfix[i].vehicle_company;

            // var searchkminarray = kmonsearch.includes(lastposition);

              // if (searchkminarray) {
                if (jalur_name == "kosongan") {
                  if (contractor != 0) {
                    if (contractor == vehicle_company) {
                      dataVehicleOnKosongan.push(
                          {
                            "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                            "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                            "violation" : dataviolationmixfix[i].violation,
                            "violation_type" : dataviolationmixfix[i].violation_type,
                            "auto_last_positionfix" : dataviolationmixfix[i].position,
                            "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                            "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                            "auto_last_update" : dataviolationmixfix[i].gps_time
                          }
                      );
                    }
                  }else {
                    dataVehicleOnKosongan.push(
                        {
                          "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                          "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                          "violation" : dataviolationmixfix[i].violation,
                          "violation_type" : dataviolationmixfix[i].violation_type,
                          "auto_last_positionfix" : dataviolationmixfix[i].position,
                          "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                          "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                          "auto_last_update" : dataviolationmixfix[i].gps_time
                        }
                    );
                  }
                }else {
                  if (contractor != 0) {
                    if (contractor == vehicle_company) {
                      dataVehicleOnMuatan.push(
                          {
                            "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                            "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                            "violation" : dataviolationmixfix[i].violation,
                            "violation_type" : dataviolationmixfix[i].violation_type,
                            "auto_last_positionfix" : dataviolationmixfix[i].position,
                            "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                            "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                            "auto_last_update" : dataviolationmixfix[i].gps_time
                          }
                      );
                    }
                  }else {
                    dataVehicleOnMuatan.push(
                        {
                          "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                          "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                          "violation" : dataviolationmixfix[i].violation,
                          "violation_type" : dataviolationmixfix[i].violation_type,
                          "auto_last_positionfix" : dataviolationmixfix[i].position,
                          "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                          "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                          "auto_last_update" : dataviolationmixfix[i].gps_time
                        }
                    );
                  }
                }
          }
      }

      console.log("dataVehicleOnKosongan : ", dataVehicleOnKosongan);
      console.log("dataVehicleOnMuatan : ", dataVehicleOnMuatan);

      var dataKosonganfix = dataVehicleOnKosongan;
      var dataMuatanfix   = dataVehicleOnMuatan;
      var htmlkosongan    = "";
      var htmlmuatan      = "";

      if (dataKosonganfix.length == 0) {
        $("#modalStateContentKosongan").html("Tidak ada data");
      }else {
        var lastcheckKmListQuickCount = "Last Check : "+dataKosonganfix[0].auto_last_update + " WITA";
        $("#modalKmListQuickCountTitle").html("Violation Summary");
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
        $("#modalKmListQuickCountTitle").html("Violation Summary");
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
  }

  function listVehicleOnKmSummaryfatigue(vType){
    console.log("Fatigue On Summary START ");
    console.log("violationtype : ", vType);
    var contractor            = $("#contractor").val();
    var lasttime_violation    = $("#lasttime_violation").val();

    var dataviolationmixfix   = current_violationmix;
    var kmonsearch;
    var dataVehicleOnKosongan = [];
    var dataVehicleOnMuatan   = [];

    console.log("dataviolationmixfix : ", dataviolationmixfix);

      for (var i = 0; i < dataviolationmixfix.length; i++) {
        var isfatigue = dataviolationmixfix[i].isfatigue;
          if (isfatigue == "yes") {
            var violationnya = dataviolationmixfix[i].violation;
            if (vType == "ft") {
              if (violationnya.includes("Fatigue")) {
                console.log("Violation Type : FATIGUE");
                var jalur_name      = dataviolationmixfix[i].jalur_name;
                var lastposition    = dataviolationmixfix[i].position;
                var vehicle_company = dataviolationmixfix[i].vehicle_company;

                    if (jalur_name == "kosongan") {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnKosongan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnKosongan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }else {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnMuatan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnMuatan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }
              }
            }else if (vType == "cc") {
              // CALL TO CALL ALARM
              if (violationnya.includes("Call")) {
                console.log("Violation Type : Call to Call Alrm");
                var jalur_name      = dataviolationmixfix[i].jalur_name;
                var lastposition    = dataviolationmixfix[i].position;
                var vehicle_company = dataviolationmixfix[i].vehicle_company;

                    if (jalur_name == "kosongan") {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnKosongan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnKosongan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }else {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnMuatan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnMuatan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }
              }
            }else if (vType == "cd") {
              if (violationnya.includes("Distance")) {
                console.log("Violation Type : Car Distance");
                var jalur_name      = dataviolationmixfix[i].jalur_name;
                var lastposition    = dataviolationmixfix[i].position;
                var vehicle_company = dataviolationmixfix[i].vehicle_company;

                    if (jalur_name == "kosongan") {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnKosongan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnKosongan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }else {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnMuatan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnMuatan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }
              }
            }else if (vType == "dt") {
              // DISTRACTED ALARM
              if (violationnya.includes("Distracted")) {
                console.log("Violation Type : Distracted Alarm");
                var jalur_name      = dataviolationmixfix[i].jalur_name;
                var lastposition    = dataviolationmixfix[i].position;
                var vehicle_company = dataviolationmixfix[i].vehicle_company;

                    if (jalur_name == "kosongan") {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnKosongan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnKosongan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }else {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnMuatan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnMuatan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }
              }
            }else if (vType == "sm") {
              // SMOKING ALARM
              if (violationnya.includes("Smoking")) {
                console.log("Violation Type : Smoking Alarm");
                var jalur_name      = dataviolationmixfix[i].jalur_name;
                var lastposition    = dataviolationmixfix[i].position;
                var vehicle_company = dataviolationmixfix[i].vehicle_company;

                    if (jalur_name == "kosongan") {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnKosongan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnKosongan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }else {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnMuatan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnMuatan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }
              }
            }else if (vType == "du") {
              if (violationnya.includes("Undetected")) {
                console.log("Violation Type : Driver Undetected");
                var jalur_name      = dataviolationmixfix[i].jalur_name;
                var lastposition    = dataviolationmixfix[i].position;
                var vehicle_company = dataviolationmixfix[i].vehicle_company;

                    if (jalur_name == "kosongan") {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnKosongan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnKosongan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }else {
                      if (contractor != 0) {
                        if (contractor == vehicle_company) {
                          dataVehicleOnMuatan.push(
                              {
                                "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                                "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                                "violation" : dataviolationmixfix[i].violation,
                                "violation_type" : dataviolationmixfix[i].violation_type,
                                "auto_last_positionfix" : dataviolationmixfix[i].position,
                                "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                                "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                                "auto_last_update" : dataviolationmixfix[i].gps_time
                              }
                          );
                        }
                      }else {
                        dataVehicleOnMuatan.push(
                            {
                              "vehicle_no" : dataviolationmixfix[i].vehicle_no,
                              "vehicle_name" : dataviolationmixfix[i].vehicle_name,
                              "violation" : dataviolationmixfix[i].violation,
                              "violation_type" : dataviolationmixfix[i].violation_type,
                              "auto_last_positionfix" : dataviolationmixfix[i].position,
                              "auto_last_speed" : dataviolationmixfix[i].gps_speed,
                              "auto_speed_limit" : dataviolationmixfix[i].gps_speed_limit,
                              "auto_last_update" : dataviolationmixfix[i].gps_time
                            }
                        );
                      }
                    }
              }
            }
          }
      }

      console.log("dataVehicleOnKosongan : ", dataVehicleOnKosongan);
      console.log("dataVehicleOnMuatan : ", dataVehicleOnMuatan);

      var dataKosonganfix = dataVehicleOnKosongan;
      var dataMuatanfix   = dataVehicleOnMuatan;
      var htmlkosongan    = "";
      var htmlmuatan      = "";

      if (dataKosonganfix.length == 0) {
        $("#modalStateContentKosongan").html("Tidak ada data");
      }else {
        var lastcheckKmListQuickCount = "Last Check : "+dataKosonganfix[0].auto_last_update + " WITA";
        $("#modalKmListQuickCountTitle").html("Violation Summary");
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
        $("#modalKmListQuickCountTitle").html("Violation Summary");
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

  }

  function modalinfodetail(){
    $("#modalinfodetail").show();
  }

  function closemodalinfodetail(){
    $("#modalinfodetail").hide();
  }


</script>
