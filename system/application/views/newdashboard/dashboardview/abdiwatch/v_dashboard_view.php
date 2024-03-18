<!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style media="screen">
/* ////////////////////////////////////////////////////////////////////////////////////////// */
/* CSS PORT VIEW */
.material-symbols-outlined {
    font-variation-settings:
        'FILL'0,
        'wght'0,
        'GRAD'0,
        'opsz'NaN
}

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

.level-2-wrapper>li::before {
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

.level-3-wrapper>li::before {
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

.level-4-wrapper li+li {
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
    .level-2-wrapper>li::before {
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

    .level-2-wrapper>li:not(:first-child) {
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
    #contentrom {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 300px;
    }

    #contentport {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 300px;
    }

    #contentpool {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 300px;
    }

    #contentpoolnew {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 300px;
    }
}

#contentrom {
    margin-left: 0%;
}

#contentport {
    margin-left: 0%;
    padding-bottom: 50px;
}

#contentpool {
    margin-left: 0%;
    padding-bottom: 50px;
}

#contentpoolnew {
    margin-left: 0%;
    padding-bottom: 50px;
}

button.gm-ui-hover-effect {
    visibility: hidden;
}

.mapsClass1 {
    width: 100%;
    height: 400px;
}

.mapsClass2 {
    width: 100%;
    height: 300px;
}

.mapsClass3 {
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

    .timeline>li {
        margin-bottom: 2px;
        position: relative;
    }

    .timeline>li:before,
    .timeline>li:after {
        content: " ";
        display: table;
    }

    .timeline>li:after {
        clear: both;
    }

    .timeline>li:before,
    .timeline>li:after {
        content: " ";
        display: table;
    }

    .timeline>li:after {
        clear: both;
    }

    .timeline>li>.timeline-panel {
        width: 46%;
        float: left;
        /* border: 1px solid #d4d4d4; */
        /* border-radius: 2px; */
        padding: 20px;
        position: relative;
        /* -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
        /* box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
    }

    .timeline>li>.timeline-badge {
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

    .timeline>li>.timeline-badge2 {
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

    .timeline>li>.timeline-badge3 {
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

    .timeline>li>.timeline-badge4 {
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

    .timeline>li>.timeline-badge5 {
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

    .timeline>li>.timeline-badge6 {
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

    .timeline>li>.timeline-badge7 {
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

    .timeline>li>.timeline-badge8 {
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

    .timeline>li>.timeline-badge9 {
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

    .timeline>li>.timeline-badge10 {
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

    .timeline>li>.timeline-badge11 {
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

    .timeline>li.timeline-inverted>.timeline-panel {
        float: center;
    }

    .timeline>li.timeline-inverted>.timeline-panel:before {
        border-left-width: 0;
        border-right-width: 15px;
        left: -15px;
        right: auto;
    }

    .timeline>li.timeline-inverted>.timeline-panel:after {
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

    .timeline-body>p,
    .timeline-body>ul {
        margin-bottom: 0;
    }

    .timeline-body>p+p {
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

    .timeline>li {
        margin-bottom: 2px;
        position: relative;
    }

    .timeline>li:before,
    .timeline>li:after {
        content: " ";
        display: table;
    }

    .timeline>li:after {
        clear: both;
    }

    .timeline>li:before,
    .timeline>li:after {
        content: " ";
        display: table;
    }

    .timeline>li:after {
        clear: both;
    }

    .timeline>li>.timeline-panel {
        width: 46%;
        float: left;
        /* border: 1px solid #d4d4d4; */
        /* border-radius: 2px; */
        padding: 20px;
        position: relative;
        /* -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
        /* box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
    }

    .timeline>li>.timeline-badge {
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

    .timeline>li>.timeline-badge2 {
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

    .timeline>li>.timeline-badge3 {
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

    .timeline>li>.timeline-badge4 {
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

    .timeline>li>.timeline-badge5 {
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

    .timeline>li>.timeline-badge6 {
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

    .timeline>li>.timeline-badge7 {
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

    .timeline>li>.timeline-badge8 {
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

    .timeline>li>.timeline-badge9 {
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

    .timeline>li>.timeline-badge10 {
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

    .timeline>li>.timeline-badge11 {
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

    .timeline>li.timeline-inverted>.timeline-panel {
        float: center;
    }

    .timeline>li.timeline-inverted>.timeline-panel:before {
        border-left-width: 0;
        border-right-width: 15px;
        left: -15px;
        right: auto;
    }

    .timeline>li.timeline-inverted>.timeline-panel:after {
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

    .timeline-body>p,
    .timeline-body>ul {
        margin-bottom: 0;
    }

    .timeline-body>p+p {
        margin-top: 5px;
    }
}

#valueTitle {
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
}

#valueonsite {
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
}

.custom-map-control-button {
    margin: 10px;
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
                    <div class="card-head" style="text-align:center; font-size:large;">
                        <header>
                            <b>
                                ABDIWATCH DASHBOARD
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
                            <div class="col-md-3">
                                <select class="form-control select2" name="abdiwatch_device" id="abdiwatch_device"
                                    onchange="getabdiwatchdata()">
                                    <option value="all">--All Abdiwatch</option>
                                    <?php
                                    if (sizeof($masterabdiwatch) > 0) {
                                    for ($i=0; $i < sizeof($masterabdiwatch); $i++) {?>
                                    <option value="<?php echo $masterabdiwatch[$i]['vehicle_device'] ?>">
                                        <?php echo $masterabdiwatch[$i]['vehicle_no'].' '.$masterabdiwatch[$i]['vehicle_name'] ?>
                                    </option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <img id="loader2" style="display:none;"
                                    src="<?php echo base_url();?>assets/images/anim_wait.gif" />
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <!-- MAPS OPTION ROM -->
                <div id="tableShowRom" style="width: 100%; max-height: 400px;">
                    <!-- display:none; -->
                    <div class="row">
                        <div class="col-md-12">
                            <p style="position: absolute;
              right: 40px;" id="jumlahtotalInRom">
                            </p>
                        </div>
                    </div> <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="lastupdate" style="font-size: 12px; position: absolute; right: 40px; top:-25px;">

                            </div>
                        </div>
                    </div>

                    <div class="row" id="contentrom">

                        <div class="col-md-2" title="Heart Rate">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon push-bottom" style="width:70px; height:70px">
                                    <!-- <i class="material-icons" style="font-size:65px; margin-top:5%;">monitor_heart</i> -->
                                    <span class="material-symbols-outlined" style="font-size:65px; margin-top:5%;">
                                        ecg_heart </span>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size:25px; margin-left:-35%;">
                                        <b>HR tes</b>
                                    </span>
                                    <span class="info-box-number" style="font-size:25px; margin-left:-35%;">
                                        <b id="show_heart_rate">
                                            00
                                        </b>
                                        <br>
                                        <div id="hr_lastdata" style="font-size:14px; margin-left:-25%;"></div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" title="Blood Pressure">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon push-bottom" style="width:70px; height:70px">
                                    <!-- <i class="material-icons" style="font-size:65px; margin-top:5%;">my_location</i> -->
                                    <span class="material-symbols-outlined" style="font-size:65px; margin-top:5%;">
                                        blood_pressure </span>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size:25px; margin-left:-35%;">
                                        <b>BP</b>
                                    </span>
                                    <span class="info-box-number" style="font-size:25px; margin-left:-35%;">
                                        <b id="show_blood_pressure">
                                            00/00
                                        </b>
                                        <br>
                                        <div id="bp_lastdata" style="font-size:14px; margin-left:-25%;"></div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" title="Body Temperature">
                            <div class="info-box bg-success">
                                <span class="info-box-icon push-bottom" style="width:70px; height:70px">
                                    <!-- <i class="material-icons" style="font-size:65px; margin-top:5%;">my_location</i> -->
                                    <span class="material-symbols-outlined" style="font-size:65px; margin-top:5%;">
                                        device_thermostat </span>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size:25px; margin-left:-35%;">
                                        <b>BT</b>
                                    </span>
                                    <span class="info-box-number" style="font-size:25px; margin-left:-35%;">
                                        <b id="show_body_temperature">
                                            00.0
                                        </b>
                                        <br>
                                        <div id="bt_lastdata" style="font-size:13px; margin-left:-35%;"></div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" title="Blood Oxygen">
                            <div class="info-box bg-info">
                                <span class="info-box-icon push-bottom" style="width:70px; height:70px">
                                    <!-- <i class="material-icons" style="font-size:65px; margin-top:5%;">my_location</i> -->
                                    <span class="material-symbols-outlined" style="font-size:65px; margin-top:5%;"> spo2
                                    </span>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size:25px; margin-left:-35%;">
                                        <b>BO</b>
                                    </span>
                                    <span class="info-box-number" style="font-size:25px; margin-left:-35%;">
                                        <b id="show_blood_oxygen">
                                            00
                                        </b>
                                        <br>
                                        <div id="bo_lastdata" style="font-size:13px; margin-left:-35%;"></div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" title="Step Counter">
                            <div class="info-box bg-success">
                                <span class="info-box-icon push-bottom" style="width:70px; height:70px">
                                    <!-- <i class="material-icons" style="font-size:65px; margin-top:5%;">my_location</i> -->
                                    <span class="material-symbols-outlined" style="font-size:65px; margin-top:5%;">
                                        steps </span>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size:25px; margin-left:-35%;">
                                        <b>SC</b>
                                    </span>
                                    <span class="info-box-number" style="font-size:25px; margin-left:-35%;">
                                        <b id="show_step_counter">
                                            00
                                        </b>
                                        <br>
                                        <div id="sc_lastdata" style="font-size:13px; margin-left:-35%;"></div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2" title="Sleep Time">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon push-bottom" style="width:70px; height:70px">
                                    <!-- <i class="material-icons" style="font-size:65px; margin-top:5%;">my_location</i> -->
                                    <span class="material-symbols-outlined" style="font-size:65px; margin-top:5%;">
                                        sleep </span>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size:25px; margin-left:-35%;">
                                        <b>ST</b>
                                    </span>
                                    <span class="info-box-number" style="font-size:25px; margin-left:-35%;">
                                        <b id="show_sleep_time">
                                            00
                                        </b>
                                        <br>
                                        <div id="st_lastdata" style="font-size:13px; margin-left:-35%;"></div>
                                    </span>
                                </div>
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

<script type="text/javascript">
var intervaldata;

function getabdiwatchdata() {
    clearInterval(intervaldata);
    var device = $("#abdiwatch_device").val();
    $.post("<?php echo base_url() ?>dashboardview/lastdata", {
        device: device
    }, function(response) {
        console.log("response : ", response);
        if (response.error == false) {
            var data = response.data;
            var totaldata = data.length;
            var gps_hr_rate = "00";
            var gps_bp_dia = "00/00";
            var gps_temp = "00";
            var gps_oxy = "00";
            var gps_step = "00";
            var gps_sleep = "00";

            // if (response.data[0].gps_hr_rate != null && response.data[0].gps_hr_rate != "") {
            //   gps_hr_rate = response.data[0].gps_hr_rate;
            // }
            //
            // if (response.data[0].gps_bp_dia != null && response.data[0].gps_bp_dia != "") {
            //   gps_bp_dia = response.data[0].gps_bp_dia;
            // }
            //
            // if (response.data[0].gps_temp != null && response.data[0].gps_temp != "") {
            //   gps_temp = response.data[0].gps_temp;
            // }
            //
            if (response.data[0].gps_oxy != null && response.data[0].gps_oxy != "") {
                gps_oxy = response.data[0].gps_oxy;
            }
            //
            // if (response.data[0].gps_step != null && response.data[0].gps_step != "") {
            //   gps_step = response.data[0].gps_step;
            // }
            //
            if (response.data[0].gps_sleep != null && response.data[0].gps_sleep != "") {
                gps_sleep = response.data[0].gps_sleep;
            }

            for (var i = 0; i < totaldata; i++) {
                if (data[i].gps_ht_code == "HRBP") {
                    $("#show_heart_rate").html(data[i].gps_hr_rate);
                    if (data[i].lastdata_status == "hist") {
                        $("#hr_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#hr_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_pressure").html(data[i].gps_bp_sys + "/" + data[i].gps_bp_dia);
                    if (data[i].lastdata_status == "hist") {
                        $("#bp_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#bp_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_oxygen").html(gps_oxy);
                    if (data[i].lastdata_status == "hist") {
                        $("#bo_lastdata").html("-");
                    } else {
                        $("#bo_lastdata").html("-");
                    }
                    $("#show_sleep_time").html(gps_sleep);
                    if (data[i].lastdata_status == "hist") {
                        $("#st_lastdata").html("-");
                    } else {
                        $("#st_lastdata").html("-");
                    }
                } else if (data[i].gps_ht_code == "TEMP") {
                    $("#show_body_temperature").html(data[i].gps_temp);
                    if (data[i].lastdata_status == "hist") {
                        $("#bt_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#bt_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_oxygen").html(gps_oxy);
                    if (data[i].lastdata_status == "hist") {
                        $("#bo_lastdata").html("-");
                    } else {
                        $("#bo_lastdata").html("-");
                    }
                    $("#show_sleep_time").html(gps_sleep);
                    if (data[i].lastdata_status == "hist") {
                        $("#st_lastdata").html("-");
                    } else {
                        $("#st_lastdata").html("-");
                    }
                } else if (data[i].gps_ht_code == "STEP") {
                    $("#show_step_counter").html(data[i].gps_step);
                    if (data[i].lastdata_status == "hist") {
                        $("#sc_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#sc_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_oxygen").html(gps_oxy);
                    if (data[i].lastdata_status == "hist") {
                        $("#bo_lastdata").html("-");
                    } else {
                        $("#bo_lastdata").html("-");
                    }
                    $("#show_sleep_time").html(gps_sleep);
                    if (data[i].lastdata_status == "hist") {
                        $("#st_lastdata").html("-");
                    } else {
                        $("#st_lastdata").html("-");
                    }
                } else {
                    // console.log("masuk");
                    // $("#show_blood_oxygen").html(gps_oxy);
                    //   if(data[i].lastdata_status == "hist"){
                    //     $("#bo_lastdata").html("--");
                    //   }else {
                    //     $("#bo_lastdata").html("no data");
                    //   }
                    // $("#show_sleep_time").html(gps_sleep);
                    //   if(data[i].lastdata_status == "hist"){
                    //     $("#st_lastdata").html("--");
                    //   }else {
                    //     $("#st_lastdata").html("no data");
                    //   }
                }
            }
            $("#lastupdate").html("Last Refresh : " + response.data[0].lastrefresh);
            intervaldata = setInterval(simultandata, 60000);
        } else {
            alert(response.message);
        }
    }, "json");
}

function simultandata() {
    var device = $("#abdiwatch_device").val();
    $.post("<?php echo base_url() ?>dashboardview/lastdata", {
        device: device
    }, function(response) {
        // console.log("response : ", response);
        if (response.error == false) {
            var data = response.data;
            var totaldata = data.length;
            var gps_hr_rate = "00";
            var gps_bp_dia = "00/00";
            var gps_temp = "00";
            var gps_oxy = "00";
            var gps_step = "00";
            var gps_sleep = "00";

            // if (response.data[0].gps_hr_rate != null && response.data[0].gps_hr_rate != "") {
            //   gps_hr_rate = response.data[0].gps_hr_rate;
            // }
            //
            // if (response.data[0].gps_bp_dia != null && response.data[0].gps_bp_dia != "") {
            //   gps_bp_dia = response.data[0].gps_bp_dia;
            // }
            //
            // if (response.data[0].gps_temp != null && response.data[0].gps_temp != "") {
            //   gps_temp = response.data[0].gps_temp;
            // }
            //
            if (response.data[0].gps_oxy != null && response.data[0].gps_oxy != "") {
                gps_oxy = response.data[0].gps_oxy;
            }
            //
            // if (response.data[0].gps_step != null && response.data[0].gps_step != "") {
            //   gps_step = response.data[0].gps_step;
            // }
            //
            if (response.data[0].gps_sleep != null && response.data[0].gps_sleep != "") {
                gps_sleep = response.data[0].gps_sleep;
            }

            for (var i = 0; i < totaldata; i++) {
                if (data[i].gps_ht_code == "HRBP") {
                    $("#show_heart_rate").html(data[i].gps_hr_rate);
                    if (data[i].lastdata_status == "hist") {
                        $("#hr_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#hr_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_pressure").html(data[i].gps_bp_sys + "/" + data[i].gps_bp_dia);
                    if (data[i].lastdata_status == "hist") {
                        $("#bp_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#bp_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_oxygen").html(gps_oxy);
                    if (data[i].lastdata_status == "hist") {
                        $("#bo_lastdata").html("-");
                    } else {
                        $("#bo_lastdata").html("-");
                    }
                    $("#show_sleep_time").html(gps_sleep);
                    if (data[i].lastdata_status == "hist") {
                        $("#st_lastdata").html("-");
                    } else {
                        $("#st_lastdata").html("-");
                    }
                } else if (data[i].gps_ht_code == "TEMP") {
                    $("#show_body_temperature").html(data[i].gps_temp);
                    if (data[i].lastdata_status == "hist") {
                        $("#bt_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#bt_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_oxygen").html(gps_oxy);
                    if (data[i].lastdata_status == "hist") {
                        $("#bo_lastdata").html("-");
                    } else {
                        $("#bo_lastdata").html("-");
                    }
                    $("#show_sleep_time").html(gps_sleep);
                    if (data[i].lastdata_status == "hist") {
                        $("#st_lastdata").html("-");
                    } else {
                        $("#st_lastdata").html("-");
                    }
                } else if (data[i].gps_ht_code == "STEP") {
                    $("#show_step_counter").html(data[i].gps_step);
                    if (data[i].lastdata_status == "hist") {
                        $("#sc_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute + "(" + data[i]
                            .lastdata_status + ")");
                    } else {
                        $("#sc_lastdata").html(data[i].gps_hour + ":" + data[i].gps_minute);
                    }

                    $("#show_blood_oxygen").html(gps_oxy);
                    if (data[i].lastdata_status == "hist") {
                        $("#bo_lastdata").html("-");
                    } else {
                        $("#bo_lastdata").html("-");
                    }
                    $("#show_sleep_time").html(gps_sleep);
                    if (data[i].lastdata_status == "hist") {
                        $("#st_lastdata").html("-");
                    } else {
                        $("#st_lastdata").html("-");
                    }
                } else {
                    // console.log("masuk");
                    // $("#show_blood_oxygen").html(gps_oxy);
                    //   if(data[i].lastdata_status == "hist"){
                    //     $("#bo_lastdata").html("--");
                    //   }else {
                    //     $("#bo_lastdata").html("no data");
                    //   }
                    // $("#show_sleep_time").html(gps_sleep);
                    //   if(data[i].lastdata_status == "hist"){
                    //     $("#st_lastdata").html("--");
                    //   }else {
                    //     $("#st_lastdata").html("no data");
                    //   }
                }
            }
            $("#lastupdate").html("Last Refresh : " + response.data[0].lastrefresh);
        } else {
            alert(response.message);
        }
    }, "json");
}
</script>