$(document).ready(function(){

    $("#stClass").change(function(){
        var thisSelect = $(this).val();
        if(thisSelect != ""){
            $(this).find('option[value=""]').remove();

            $.ajax({
                url: "controllers/Gets/getCategories.php",
                method: "GET",
                data: { id: thisSelect },
                success: function (response) {
                    $("#stCategory").html(response)
                },
                error: function (xhr, status, error) {
                    alert("Sunucu Hatası Kategori Çekilemedi")
                }
            });
        }
    });

    $('#customFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    function answerimageCheck(){
        if ($("#answerimage").is(":checked")) {
            $("#answerHidden").val("1")
            $(".answers").attr("type","file")
            $(".answerimages").show();
        } else {
            $("#answerHidden").val("0")
            $(".answers").attr("type","text")
            $(".answerimages").hide();
        }
    }
    answerimageCheck();
    $("#answerimage").change(function(){
        answerimageCheck();
    })

    $('#max-date').bootstrapMaterialDatePicker({ 
         format : 'DD/MM/YYYY HH:mm', minDate : new Date() 
    });


});