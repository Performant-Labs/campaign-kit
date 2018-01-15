function copyCurrentUrl() {

  var Url = document.getElementById("current-url");
  Url.innerHTML = window.location.href;
  Url.select();
  document.execCommand("copy");
  swal({
    title: 'Page Url has been copied!',
    //text: 'Page Url copied!',
    type: 'success',
    showConfirmButton: false,
    timer: 2500,

  });
}

//Validate Dedicate option
function hasdedication() {

  if (document.getElementById('edit-dedicate-donation-1').checked) {
    document.getElementById('edit-dedication-information').style.display ='block';
  } else {
    document.getElementById('edit-dedication-information').style.display ='none';
  }

}

// Get donation amount of selected option
function getdonationamount() {

  var donations = document.getElementsByName('donation');

  for (var i = 0, length = donations.length; i < length; i++) {

    if (donations[i].checked && donations[i].value != "other") {
      document.getElementById("edit-amount--2").value = donations[i].value;
      break;
    }
    if (donations[i].checked && donations[i].value == "other") {
      document.getElementById("edit-amount--2").value = "";
      break;
    }
  }
}
