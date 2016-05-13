var selectedElement = -1;
var lastValue = "";
var child = 0;

$(document).ready(function() {
    // bind 'myForm' and provide a simple callback function
    $("#clock").hide();
    $('#button_submit').click(function(event) {

        var user = !$("#user");
        $("#form").hide();
        $("#display").append('<img src="images/clock.gif" />');
        $.ajax({
            url: $("#news").val(),
            method: "POST",
            data: {
                pseudo: $("#pseudo").val(),
                pseudoid : $("#pseudoid").val(),
                contenu: $("#contenu").val(),
                user: user
            },
            dataType: "json",
            success: function(data_a, deux, trois){
                if($("#lpseudo").length != 0) {
                    $("#lpseudo").html($("#lpseudo").html().substring(0, $("#lpseudo").html().indexOf(":") + 2));
                }

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
                    var msg = data_a.form;
                    $("label:contains("+capitalise(data_a.field)+")").append(msg.fontcolor("red"));
                }

            },
            complete: function(un,deux){
                $("#display").html('');
                $("#form").show();
            }

        });

        return false;
    });

    $("#subscription").submit(function(event){


        $("label:contains('Email')").html('Email');
        $("label:contains('Confirmation')").html('Confirmation de Mot de Passe');
        $("label:contains('Login')").html('Login');
        $("label:contains('Pseudo')").html('Pseudo');

        var login = $("[name='login']").val();
        var email =$("[name='email']").val();
        var nickname =$("[name='nickname']").val();
        var password1 = $("[name='password1']").val();
        var password2 = $("[name='password2']").val();

        var error = true;
        if(checkMail(email) == false){
            var msg =" Veuillez spécifier une adresse email correcte.";
            $("label:contains('Email')").append(msg.fontcolor("red"));
            error = false;
        }

        if(password1.localeCompare(password2) !=0 ){
            var msg = " Les deux mots de passe ne sont pas égaux.";
            $("label:contains('Confirmation')").append(msg.fontcolor("red"));
            error = false;
        }
        if(checkNotNull(login) == false){
            var msg = " Ce champ ne peut être vide.";
            $("label:contains('Login')").append(msg.fontcolor("red"));
            error = false;
        }
        if(checkNotNull(nickname) == false){
            var msg = " Ce champ ne peut être vide.";
            $("label:contains('Pseudo')").append(msg.fontcolor("red"));
            error = false;
        }

        if(error){
            ;

            $.ajax({
                url: "/insertAuthor",
                method: "POST",
                data: $('#subscription').serializeArray().reduce(function(obj, item) {
                    obj[item.name] = item.value;
                    return obj;
                },{}),
                dataType: "json",
                success: function(data_a){

                    //var data_a = jQuery.parseJSON(data_a);
                    console.log(data_a);
                    if(data_a.success == true) {
                        window.location.replace("/admin/");
                    }else{
                        var msg = " "+data_a.form;
                        $("label:contains("+capitalise(data_a.field)+")").append(msg.fontcolor("red"));
                    }

                },
                error: function(un,deux,trois){
                    alert(un+' '+deux+' '+trois);
                },
                complete: function(un,deux){
                }

            });
        }
        
        return false;
    });

    $("[name='tags']").bind('keyup',function(event) {
        child = $("#results")[0].childNodes;

        var tags = $("[name='tags']").val();
        var hashs = tags.split(" ");
        if (hashs[hashs.length - 1].length == 0) return;
        var tag = hashs[hashs.length - 1];
        if (tag.substring(0, 1) != "#" || tag.length == 1) return;
        tag = tag.substring(1);

        if (tag != lastValue) {
            lastValue = tag;
            $.ajax({
            url: "/starting-" + tag + "-limit-5.html",
            method: "POST",
            dataType: "json",
            success: function (data_a) {
                if (data_a.success == true) {
                    $("#results").html("");
                    jQuery.each(data_a.contenu, function (ind, obj) {
                        var div = $('<div></div>');
                        div.html("#" + obj);
                        div.bind('click', function (event) {

                            hashs[hashs.length - 1] = "#" + obj;
                            var results = hashs.join(" ");
                            $("[name='tags']").val(results);
                            $("[name='tags']")[0].setSelectionRange(end,results.length-1);
                            $("[name='tags']").focus();
                            $("#results").html("");

                        });
                        $("#results").append(div);

                        var end = tags.length;
                        hashs[hashs.length - 1] = "#"+data_a.contenu[0];
                        var results = hashs.join(" ");
                        $("[name='tags']").val(results);
                        $("[name='tags']")[0].setSelectionRange(end,results.length);
                        $("[name='tags']").focus();

                    });
                } else {
                    $("#results").html("");
                }

            },
            error: function (un, deux, trois) {
                alert(un + ' ' + deux + ' ' + trois);
            }
        });
        }else if(event.keyCode == 38){
            if(selectedElement == -1){
                selectedElement = child.length - 1;
                child[selectedElement].className = "select_focus";
            }else if(selectedElement > 0){
                child[selectedElement--].className = "";
                child[selectedElement].className = "select_focus";
            }
        }else if(event.keyCode == 40){
            if(selectedElement == -1){
                selectedElement = 0;
                child[selectedElement].className = "select_focus";
            }else if(selectedElement < child.length -1){
                child[selectedElement++].className = "";
                child[selectedElement].className = "select_focus";
            }
        }else if(event.keyCode == 13 ){
            if(selectedElement != -1) {
                hashs[hashs.length - 1] = child[selectedElement].innerHTML;
                var results = hashs.join(" ");
                $("[name='tags']").val(results);
                $("#results").html("");
                return false;
            }
        }
    })

});

$('a[href*="before"]').click(function(event){
    var addressValue = $(this).attr("href");
    var t = 'a[href="'+addressValue+'"]';
    $.ajax({
        url: addressValue,
        method: "POST",
        dataType: "json",
        success: function(data_a){
            if(data_a.success == true) {
                $(t).after(data_a.contenu);
                $(t).attr("href", data_a.link);
            }else{
                $(t).html("");
            }
        },
        error : function(u,d,t){
            alert(u+' '+d+' '+t);
        }
    });
    return false;
});

$('a[href*="after"]').click(function(event){
    var addressValue = $(this).attr("href");
    var t = 'a[href="'+addressValue+'"]';
    $.ajax({
        url: addressValue,
        method: "POST",
        dataType: "json",
        success: function(data_a){
            if(data_a.success == true) {
                $(t).before(data_a.contenu);
                $(t).attr("href", data_a.link);
            }else{
                $(t).html("");
            }
        },
        error : function(u,d,t){
            alert(u+' '+d+' '+t);
        }
    });
    return false;
});

function checkNotNull(value){
    return value != '';
}

function checkMail(email){
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function capitalise(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}