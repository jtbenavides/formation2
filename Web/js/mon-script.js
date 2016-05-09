$(document).ready(function() {
    // bind 'myForm' and provide a simple callback function
    $("#clock").hide();
    $('#form').submit(function(event) {

        var user = !$("#user");
        $("#form").hide();
        $("#display").append('<img src="images/clock.gif" />');
        $.ajax({
            url: $("#news").val(),
            method: "POST",
            data: {
                pseudo: $("#pseudo").val(),
                contenu: $("#contenu").val(),
                user: user
            },
            dataType: "json",
            success: function(data_a, deux, trois){
                $("#lcontenu").html($("#lcontenu").html().substring(0, $("#lcontenu").html().indexOf(":") + 2));

                if (data_a.success) {
                    var content = $("#content").html();
                    if (content.localeCompare("<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>") == 0) {
                        $("#content").html(data_a.contenu);
                    } else {
                        $("#content").append(data_a.contenu);
                    }

                    $('#form').each(function () {
                        this.reset();
                    });

                } else {
                    $("#lcontenu").append(data_a.errormessage.fontcolor("red"));
                }

            },
            complete: function(un,deux){
                $("#display").html('');
                $("#form").show();
            }

        });

        return false;
    });
});