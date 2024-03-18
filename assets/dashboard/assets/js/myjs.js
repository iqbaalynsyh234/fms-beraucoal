var modal, btn, span;
var modalpool, btnpool, spanpool, modalkm, btnkm, modalumum, violationFrequency, btnviolationfrequency;
var btnumum, modalPasswordBaru, btnPasswordBaru, modalmaintenance, btnmaintenance;

function customMymodal(modalID) {
  // Get the modal
  modal = document.getElementById(modalID);

  // Get the button that opens the modal
  // btn = document.getElementById(btnID);

  // Get the <span> element that closes the modal
  btn = document.getElementsByClassName("closethismodal")[0];

  console.log("modal : ", modal);
  console.log("btn : ", btn);

  modal.style.display = "block";

  // When the user clicks on <span> (x), close the modal
  btn.onclick = function () {
    modal.style.display = "none";
  }
}

function modalPoolFromMasterData(modalid) {
  modalpool = document.getElementById(modalid);
  btnpool = document.getElementsByClassName("closethismodalall")[0];
  modalpool.style.display = "block";
  btnpool.onclick = function () {
    modalpool.style.display = "none";
  }
}

function modalformaintenance(modalid) {
  modalmaintenance = document.getElementById(modalid);
  btnmaintenance = document.getElementsByClassName("closethismodalmaintenance")[0];
  modalmaintenance.style.display = "block";
  btnmaintenance.onclick = function () {
    modalmaintenance.style.display = "none";
  }
}

function modalChangePasswordBaru(modalid) {
  modalPasswordBaru = document.getElementById(modalid);
  btnPasswordBaru = document.getElementsByClassName("closethismodalallModalPasswordBaru")[0];
  modalPasswordBaru.style.display = "block";
  btnPasswordBaru.onclick = function () {
    modalPasswordBaru.style.display = "none";
  }
}

function modalumum(modalid) {
  modalumum = document.getElementById(modalid);
  btnumum = document.getElementsByClassName("closethismodalall")[0];
  modalumum.style.display = "block";
  btnumum.onclick = function () {
    modalumum.style.display = "none";
  }
}

function modalKmFromMasterData(modalid) {
  modalkm = document.getElementById(modalid);
  btnkm = document.getElementsByClassName("closethismodalkm")[0];
  modalkm.style.display = "block";
  btnkm.onclick = function () {
    modalkm.style.display = "none";
  }
}

function modalViolationFrequency(modalid) {
  violationFrequency = document.getElementById(modalid);
  btnviolationfrequency = document.getElementsByClassName("closethismodalall")[0];
  violationFrequency.style.display = "block";
  btnviolationfrequency.onclick = function () {
    violationFrequency.style.display = "none";
  }
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  } else if (event.target == modalpool) {
    modalpool.style.display = "none";
  } else if (event.target == modalkm) {
    modalkm.style.display = "none";
  }
}
