// ==================================================
// Project Name  :  Quizo
// File          :  JS Base
// Version       :  1.0.0
// Author        :  jthemes (https://themeforest.net/user/jthemes)
// ==================================================

var currentTab = 0; // Current tab is set to be the first tab (0)

let totalSeconds = parseInt(time) * 60;
let timerInterval;

$("#startQuiz").click(function(){
  let name = $("#name").val();
  let surname = $("#surname").val();
  if(name == "" || surname == ""){
    iziToast.error({
        title: 'Hata',
        message: 'İsim veya soyisim boş girilemez.',
        position: "topRight"
    });
  }
  else{
    //Sınavı Başlat Time başlat
    iziToast.success({
        title: 'Başarılı',
        message: 'Deneme sınavı başladı başarılar.',
        position: "topRight"
    });
    $(".stname").val(name);
    $(".stsurname").val(surname);
    $(".login").hide();
    $(".quizContain").show();
    $("#prevBtn").show();
    $("#nextBtn").show();
    showTab(currentTab); // Display the current tab

    // Timer başlat
    startCountdown(totalSeconds);
  }
});

function startCountdown(duration) {
  let remaining = duration;

  timerInterval = setInterval(function () {
    let minutes = Math.floor(remaining / 60);
    let seconds = remaining % 60;

    if (remaining <= 300) { // 5 dakika veya daha az
      $(".timeout").addClass("error");
      $(".timeText").text(`${minutes}:${seconds < 10 ? '0' + seconds : seconds}`);
    } else {
      $(".timeText").text(`${minutes} Dakika`);
    }

    if (remaining <= 0) {
      clearInterval(timerInterval);
      $("#wizard").submit();
    }

    remaining--;
  }, 1000);
}

$(function(){
  "use strict";
  // ========== Form-select-option ========== //
  $(".step_option").on('click', function(){
    // Aynı isimli diğer aktif class'ları kaldır
    $(this).closest(".form_items").find(".step_option").removeClass("active");
    $(this).addClass("active");
  });
});



function showTab(n) {
  // This function will display the specified tab of the form ...
  var x = document.getElementsByClassName("multisteps_form_panel");
  x[n].style.display = "block";
  // ... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Denemeyi Tamamla";
    document.getElementById("nextBtn").classList.add("bg-success");
  } else {
    document.getElementById("nextBtn").innerHTML = "Sonraki Soru";
    document.getElementById("nextBtn").classList.remove("bg-success");
  }
  // ... and run a function that displays the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("multisteps_form_panel");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form... :
  if (currentTab >= x.length) {
    //...the form gets submitted:
    document.getElementById("wizard").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  var x = document.getElementsByClassName("multisteps_form_panel");
  var inputs = x[currentTab].querySelectorAll("input[type='radio']");
  var valid = false;

  // Radio butonlardan herhangi biri seçilmiş mi kontrol et
  for (var i = 0; i < inputs.length; i++) {
    if (inputs[i].checked) {
      valid = true;
      break;
    }
  }

  if (!valid) {
    iziToast.error({
        title: 'Hata',
        message: 'Lütfen işaretleme yapınız.',
        position: "topRight"
    });
  }

  return valid;
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
  x[n].className += " active";
}
