let currentQuestion = 0;
let Stname = "";
let Stsurname = "";
let timerInterval;
let quizFinished = false;

window.addEventListener('beforeunload', function (event) {
    if (quizFinished) {
        return;  // Quiz tamamlandıysa uyarıyı gösterme
    }

    const message = "Sayfayı yenilemek ya da çıkmak istediğinizden emin misiniz?";
    event.returnValue = message;
    return message;
});

function renderQuestion(index) {
    const question = data[index];
    const quizCard = $(".quizpage .card");

    // Soru numarası
    $(".numberQuestion").text(index + 1);

    // Soru resmi
    quizCard.find(".card-header img").attr("src", "assets/questions/" + question.questionImage);

    // Şıkları oluştur
    let answerHTML = "";
    if (question.answers[4].answerImage) {
        // Şıklar resimli
        ["answer_a", "answer_b", "answer_c", "answer_d"].forEach((key, i) => {
            const imgSrc = question.answers[i][key];
            const activeClass = question.answer === i ? "active" : "";
            answerHTML += `<li class="answer ${key} ${activeClass}" data-index="${i}">
                              <img src="assets/answers/${imgSrc}" class="img-fluid" />
                           </li>`;
        });
    } else {
        // Şıklar yazılı
        ["answer_a", "answer_b", "answer_c", "answer_d"].forEach((key, i) => {
            const text = question.answers[i][key];
            const activeClass = question.answer === i ? "active" : "";
            answerHTML += `<li class="answer ${key} ${activeClass}" data-index="${i}">
                              <span>${text}</span>
                           </li>`;
        });
    }

    quizCard.find(".card-body ul").html(answerHTML);

    // Buton göster/gizle
    $(".buttons button:contains('Önceki Soru')").css("visibility", index === 0 ? "hidden" : "visible");
    $(".buttons button:contains('Sonraki Soru')").toggle(index !== data.length - 1);
    $(".buttons button:contains('Sınavı Bitir')").toggle(index === data.length - 1);
}

// Şık seçimi
$(document).on("click", ".answer", function () {
    const selectedIndex = parseInt($(this).data("index"));
    const answers = $(this).closest("ul").find(".answer");

    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        data[currentQuestion].answer = null;
    } else {
        answers.removeClass("active");
        $(this).addClass("active");
        data[currentQuestion].answer = selectedIndex;
    }
});

// Başlat butonu
$("#startQuiz").click(function () {
    if ($("#name").val() === "" || $("#surname").val() === "") {
        iziToast.warning({
            title: 'Uyarı',
            message: 'Lütfen adınızı ve soyadınızı giriniz.',
        });
        return;
    }
    else{
        let GetTime = $(".timeout").attr("data-time")
        if(GetTime == undefined || GetTime == null)
            GetTime = 0
        let totalSeconds = parseInt(GetTime) * 60;
        iziToast.success({
            title: 'Başarılı',
            message: 'Sınavın başladı! başarılar.'
        });
        Stname = $("#name").val();
        Stsurname = $("#surname").val();
        $(".timeout").show();
        $(".quizLogin").hide();
        $(".quizpage").show();
        startCountdown(totalSeconds);
        renderQuestion(currentQuestion);
    }
    
});

// Önceki / Sonraki butonları
$(".buttons button:contains('Önceki Soru')").click(function () {
    if (currentQuestion > 0) {
        currentQuestion--;
        renderQuestion(currentQuestion);
    }
});

$(".buttons button:contains('Sonraki Soru')").click(function () {
    if (currentQuestion < data.length - 1) {
        currentQuestion++;
        renderQuestion(currentQuestion);
    }
});

// Sınavı Bitir butonu
$(".buttons button:contains('Sınavı Bitir')").click(function () {
    const postData = {
        name: Stname,
        surname: Stsurname,
        quizID: quizID,
        answers: data
    };

    $.ajax({
        url: 'controllers/quizfinishcontroller.php',
        type: 'POST',
        data: {quizData: JSON.stringify(postData)},
        success: function (response) {
            // İsteğe göre yönlendir veya sonucu göster
            quizFinished = true;
            iziToast.success({
                title: 'Başarılı',
                message: 'Sınav başarıyla gönderildi.',
            });
            if(response == "1"){
                window.location.href = "quiz-bitti?status=true";
            }
            else{
                window.location.href = "quiz-bitti?status=false";
            }
        },
        error: function () {
            iziToast.error({
                title: 'Hata',
                message: 'Bir hata oluştu, lütfen tekrar deneyiniz.',
            });
        }
    });
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
        $(".buttons button:contains('Sınavı Bitir')").click();
      }
  
      remaining--;
    }, 1000);
  }