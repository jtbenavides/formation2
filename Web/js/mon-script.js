"use strict";
var selectedElement = -1;
var lastValue = "";
var child = 0;

$(document).ready(function () {

    if($("#index").length) {
        var win = $(window);
        // Each time the user scrolls
        win.scroll(function () {
            // End of the document reached?
            if ($(window).scrollTop() + $(window).height() + 1 > $(document).height()) {
                //$('#loading').show();
                $.ajax({
                    url: '/news-scroll.html',
                    dataType: 'json',
                    success: function (data) {
                        for (var i in data) {
                            $('#main').append(data[i].titre);
                            $('#main').append(data[i].contenu);
                        }
                        //$('#loading').hide();
                    }
                });
            }
        });
    }

    // bind 'myForm' and provide a simple callback function
    $("#clock").hide();
    $('#button_submit').click(function (event) {

        var user = !$("#user");
        $("#form").hide();
        $("#display").append('<img src="images/clock.gif" />');
        $.ajax({
            url: $("#news").val(),
            method: "POST",
            data: {
                pseudo: $("#pseudo").val(),
                pseudoid: $("#pseudoid").val(),
                contenu: $("#contenu").val(),
                user: user
            },
            dataType: "json",
            success: function (data_a, deux, trois) {
                if ($("#lpseudo").length != 0) {
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
                    $("label:contains(" + capitalise(data_a.field) + ")").append(msg.fontcolor("red"));
                }

            },
            complete: function (un, deux) {
                $("#display").html('');
                $("#form").show();
            }

        });

        return false;
    });


    if ($("#subscription").length) {
        $("#subscription").submit(function (event) {


            $("label:contains('Email')").html('Email');
            $("label:contains('Confirmation')").html('Confirmation de Mot de Passe');
            $("label:contains('Login')").html('Login');
            $("label:contains('Pseudo')").html('Pseudo');


            $.ajax({
                url: "/insertAuthor",
                method: "POST",
                data: $('#subscription').serializeArray().reduce(function (obj, item) {
                    obj[item.name] = item.value;
                    return obj;
                }, {}),
                dataType: "json",
                success: function (data_a) {

                    if (data_a.success == true) {
                        window.location.replace("/admin/");
                    } else {
                        var msg = " " + data_a.form;
                        $("label:contains(" + capitalise(data_a.field) + ")").append(msg.fontcolor("red"));
                    }

                },
                error: function (un, deux, trois) {
                    alert(un + ' ' + deux + ' ' + trois);
                },
                complete: function (un, deux) {
                }

            });

            return false;
        });

        $("[name='login']").bind("keyup", function (event) {
            cleanLabel('Login');

            var value = $("[name='login']").val();
            if (value == '') {
                $("label:contains('Login')").append(" : Login vide");
            } else {
                $.ajax({
                    url: "/exist-login.html",
                    method: "POST",
                    dataType: "json",
                    data: {
                        value: value
                    },
                    success: function (data_a) {

                        if (data_a.success == true) {
                            $("label:contains('Login')").append(" : Login deja utilisé");
                        }
                    },
                    error: function (un, deux, trois) {
                        alert(un + ' ' + deux + ' ' + trois);
                    }
                });
            }
        });

        $("[name='nickname']").bind("keyup", function (event) {
            cleanLabel('Pseudo');

            var value = $("[name='nickname']").val();
            if (value == '') {
                $("label:contains('Pseudo')").append(" : Pseudo vide");
            } else {
                $.ajax({
                    url: "/exist-nickname.html",
                    method: "POST",
                    dataType: "json",
                    data: {
                        value: value
                    },
                    success: function (data_a) {

                        if (data_a.success == true) {
                            $("label:contains('Pseudo')").append(" : Pseudo deja utilisé");
                        }
                    },
                    error: function (un, deux, trois) {
                        alert(un + ' ' + deux + ' ' + trois);
                    }
                });
            }
        });

        $("[name='email']").bind("keyup", function (event) {
            cleanLabel('Email');

            var value = $("[name='email']").val();
            if (value == '') {
                $("label:contains('Email')").append(" : Email vide");
            } else if (!checkMail(value)) {
                $("label:contains('Email')").append(" : Ce n'est pas un email correct");
            } else {
                $.ajax({
                    url: "/exist-email.html",
                    method: "POST",
                    dataType: "json",
                    data: {
                        value: value
                    },
                    success: function (data_a) {


                        if (data_a.success == true) {
                            $("label:contains('Email')").append(" : Email deja utilisé");
                        }
                    },
                    error: function (un, deux, trois) {
                        alert(un + ' ' + deux + ' ' + trois);
                    }
                });
            }
        });

        $("[name='password2']").bind("keyup", function (event) {
            cleanLabel('Confirmation');

            var value = $("[name='password1']").val();
            if (value != $("[name='password2']").val()) {
                $("label:contains('Confirmation')").append(" : Mot de passe différent");
            }
        });
    }

    /*$("[name='tags']").bind('keyup',function(event) {
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
     });*/

    var availableTags = [];
    if ($("[name='tags']").length) {
        $.ajax({
            url: "/starting-limit-0.html",
            method: "POST",
            dataType: "json",
            success: function (data_a) {
                if (data_a.success == true) {
                    availableTags = data_a.contenu;
                }

            },
            error: function (un, deux, trois) {
                alert(un + ' ' + deux + ' ' + trois);
            }
        });

        $("[name='tags']")
        // don't navigate away from the field on tab when selecting an item
            .bind("keydown", function (event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).autocomplete("instance").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 0,
                source: function (request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response($.ui.autocomplete.filter(
                        availableTags, extractLast(request.term)));
                },
                focus: function () {
                    // prevent value inserted on focus
                    return false;
                },
                select: function (event, ui) {
                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push("#" + ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(" ");
                    return false;
                }
            });
    }

    if($("#check-comment").length) {
        $('a[href*="before"]').each(function () {
            var a = $(this);
            $.ajax({
                url: $(this).attr("href"),
                method: "POST",
                dataType: "json",
                success: function (data_a) {
                    if (!data_a.success) {
                        a.html("");
                    }
                },
                error: function (u, d, t) {
                    alert(u + ' ' + d + ' ' + t);
                }
            });
        });

        $('a[href*="after"]').each(function () {
            var a = $(this);
            $.ajax({
                url: $(this).attr("href"),
                method: "POST",
                dataType: "json",
                success: function (data_a) {

                    if (!data_a.success) {
                        a.html("");
                    }
                },
                error: function (u, d, t) {
                    alert(u + ' ' + d + ' ' + t);
                }
            });
        });

        $('a[href*="before"]').click(function (event) {
            var a = $(this);
            var addressValue = $(this).attr("href");
            $.ajax({
                url: addressValue,
                method: "POST",
                dataType: "json",
                success: function (data_a) {
                    if (data_a.success == true) {
                        a.after(data_a.contenu);
                        a.attr("href", data_a.link);
                        if (!data_a.next) {
                            a.html("");
                        }
                    } else {
                        a.html("");
                    }
                },
                error: function (u, d, t) {
                    alert(u + ' ' + d + ' ' + t);
                }
            });
            return false;
        });

        $('a[href*="after"]').click(function (event) {
            var a = $(this);
            var addressValue = $(this).attr("href");
            $.ajax({
                url: addressValue,
                method: "POST",
                dataType: "json",
                success: function (data_a) {

                    if (data_a.success == true) {

                        a.before(data_a.contenu);

                        a.attr("href", data_a.link);
                        if (!data_a.next) {
                            a.html("");
                        }
                    } else {
                        a.html("");
                    }
                },
                error: function (u, d, t) {
                    alert(u + ' ' + d + ' ' + t);
                }
            });
            return false;
        });

        $("#check-comment").click(function () {
            $(".comment").slideToggle(500);
        });

        $("#check-created").click(function () {
            $(".created").slideToggle(500);
        });

        $("#check-modified").click(function () {
            $(".modified").slideToggle(500);
        });
    }
});

function checkNotNull(value) {
    return value != '';
}

function cleanLabel(name) {
    var req = 'label:contains(\'' + name + '\')';
    var html = $(req).html();
    var length = html.indexOf(" :");
    if (length != -1) {
        $(req).html(html.substr(0, length));
    }
}

function checkMail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function capitalise(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

function split(val) {
    return val.split(" ");
}
// /,\s*/
function extractLast(term) {
    return split(term).pop();
}
