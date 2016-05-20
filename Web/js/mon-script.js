"use strict";

(function ($, undefined) {
    $.fn.getCursorPosition = function () {
        var el = $(this).get(0);
        var pos = 0;
        if ('selectionStart' in el) {
            pos = el.selectionStart;
        } else if ('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    }
})(jQuery);


$(document).ready(function () {


    if ($("#index").length) {
        var scrolling = true;
        var win = $(window);
        // Each time the user scrolls
        win.scroll(function () {
            // End of the document reached?
            if ($(window).scrollTop() + $(window).height() + 1 > $(document).height() && scrolling) {
                //$('#loading').show();
                $.ajax({
                    url: '/news-scroll.html',
                    dataType: 'json',
                    success: function (data) {
                        scrolling = data.next;

                        var html = '<section style="display:none" class="news">';
                        for (var i in data.contenu) {
                            html += (data.contenu[i].titre);
                            html += (data.contenu[i].contenu);
                        }
                        html += '</section>';
                        $("#main").append(html);

                        $(".news").show("slide", {direction: "up"});
                        //$('#loading').hide();
                    }
                });
            }
        });
    }

    // bind 'myForm' and provide a simple callback function
    if ($("#button_submit").length) {
        $("#display").append('<img src="images/clock.gif" />');
        $('#button_submit').click(function (event) {

            $("#form").fadeOut(500, function () {

                $("#display").fadeIn(500, function () {
                    $.ajax({
                        url: $("#news").val(),
                        method: "POST",
                        data: {
                            pseudo: $("#pseudo").val(),
                            pseudoid: $("#pseudoid").val(),
                            contenu: $("#contenu").val()
                        },
                        dataType: "json",
                        success: function (data_a) {
                            if ($("#lpseudo").length) {
                                $("#lpseudo").html($("#lpseudo").html().substring(0, $("#lpseudo").html().indexOf(":") + 2));
                            }


                            if (data_a.success) {
                                var content = $("#content").html();
                                if ($("#content fieldset").length) {
                                    $("#content").append(data_a.contenu);
                                } else {
                                    $("#content").html(data_a.contenu);
                                }

                                $(".new").show("slide", {direction: "up"}, 1000);


                                $(".link-delete").click(function (event) {
                                    return manageDeleteComment(this);
                                });

                                cleanLabel("contenu");

                                $('#form').each(function () {
                                    this.reset();
                                });

                            } else {
                                var msg = " " + data_a.form;
                                $("#lcontenu").append(msg.fontcolor("red"));
                            }

                        },
                        complete: function (un, deux) {
                            setTimeout(function () {
                                $("#display").fadeOut(500, function () {
                                    $("#form").fadeIn(500, function () {
                                        $("#contenu").focus();
                                    });
                                });
                            }, 1000);


                        }

                    });
                });
            });


            return false;
        });
    }

    if ($("#button_submit").length || $("#check-comment").length) {
        $('.link-delete').click(function (event) {
            return manageDeleteComment(this);
        });
    }

    if ($("#subscription").length) {
        $("#subscription").submit(function (event) {

            cleanLabel("email");
            cleanLabel("password2");
            cleanLabel("login");
            cleanLabel("nickname");

            $.ajax({
                url: "/signin",
                method: "POST",
                data: $('#subscription').serializeArray().reduce(function (obj, item) {
                    obj[item.name] = item.value;
                    return obj;
                }, {}),
                dataType: "json",
                success: function (data_a) {

                    if (data_a.success) {
                        window.location.replace("/admin/");
                    } else {
                        for(var i in data_a.form){
                            var t = 'label[for="'+i+'"]';
                            console.log(t);
                            var y = "<label class='error' >"+data_a.form[i]+"</label>";
                            console.log(y);
                            $(t).before("<label class='error' >"+data_a.form[i]+"</label>");
                        }

                        //$("#form").html(data_a.form);
                    }
/*
                    $("#login").on("paste keyup", function (event) {
                        if (event.keyCode == 9) {
                            return;
                        }
                        cleanLabel('login');

                        clearTimeout($(this).data('timeout'));
                        $(this).data('timeout', setTimeout(function () {
                            var value = $("#login").val();
                            if (value == '') {
                                $("label[for='login']").append(" Login vide");
                            } else {
                                $.ajax({
                                    url: "/exist-login.html",
                                    method: "POST",
                                    dataType: "json",
                                    data: {
                                        value: value
                                    },
                                    success: function (data_a) {

                                        if (data_a.success) {
                                            $("label[for='login']").append(" Login deja utilisé");
                                        }
                                    },
                                    error: function (un, deux, trois) {
                                        alert(un + ' ' + deux + ' ' + trois);
                                    }
                                });
                            }
                        }, 500));
                    });

                    $("#nickname").on("paste keyup", function (event) {
                        if (event.keyCode == 9) {
                            return;
                        }
                        cleanLabel('nickname');

                        clearTimeout($(this).data('timeout'));
                        $(this).data('timeout', setTimeout(function () {
                            var value = $("#nickname").val();
                            if (value == '') {
                                $("label[for='nickname']").append(" Pseudo vide");
                            } else if (event.keyCode == 9) {
                                return;
                            } else {
                                $.ajax({
                                    url: "/exist-nickname.html",
                                    method: "POST",
                                    dataType: "json",
                                    data: {
                                        value: value
                                    },
                                    success: function (data_a) {

                                        if (data_a.success) {
                                            $("label[for='nickname']").append(" Pseudo deja utilisé");
                                        }
                                    }
                                });
                            }
                        }, 500));
                    });

                    $("#email").on("paste keyup", function (event) {
                        if (event.keyCode == 9) {
                            return;
                        }
                        cleanLabel('email');

                        clearTimeout($(this).data('timeout'));
                        $(this).data('timeout', setTimeout(function () {
                            var value = $("#email").val();
                            if (value == '') {
                                $("label[for='email']").append(" Email vide");
                            } else if (!checkMail(value)) {
                                $("label[for='email']").append(" Ce n'est pas un email correct");
                            } else {
                                $.ajax({
                                    url: "/exist-email.html",
                                    method: "POST",
                                    dataType: "json",
                                    data: {
                                        value: value
                                    },
                                    success: function (data_a) {
                                        if (data_a.success) {
                                            $("label[for='email']").append(" Email deja utilisé");
                                        }
                                    }
                                });
                            }
                        }, 500));
                    });

                    $("#password2").on("paste keyup", function (event) {
                        cleanLabel('password2');

                        clearTimeout($(this).data('timeout'));
                        $(this).data('timeout', setTimeout(function () {
                            var value = $("#password1").val();
                            if (value != $("#password2").val()) {
                                $("label[for='password2']").append(" Mot de passe différent");
                            }
                        }, 500));
                    });
*/
                }
            });
            return false;
        });

        $("#login").on("paste keyup", function (event) {
            if (event.keyCode == 9) {
                return;
            }
            cleanLabel('login');

            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                var value = $("#login").val();
                if (value == '') {
                    $("label[for='login']").append(" Login vide");
                } else {
                    $.ajax({
                        url: "/exist-login.html",
                        method: "POST",
                        dataType: "json",
                        data: {
                            value: value
                        },
                        success: function (data_a) {

                            if (data_a.success) {
                                $("label[for='login']").append(" Login deja utilisé");
                            }
                        },
                        error: function (un, deux, trois) {
                            alert(un + ' ' + deux + ' ' + trois);
                        }
                    });
                }
            }, 500));
        });

        $("#nickname").on("paste keyup", function (event) {
            if (event.keyCode == 9) {
                return;
            }
            cleanLabel('nickname');

            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                var value = $("#nickname").val();
                if (value == '') {
                    $("label[for='nickname']").append(" Pseudo vide");
                } else if (event.keyCode == 9) {
                    return;
                } else {
                    $.ajax({
                        url: "/exist-nickname.html",
                        method: "POST",
                        dataType: "json",
                        data: {
                            value: value
                        },
                        success: function (data_a) {

                            if (data_a.success) {
                                $("label[for='nickname']").append(" Pseudo deja utilisé");
                            }
                        }
                    });
                }
            }, 500));
        });

        $("#email").on("paste keyup", function (event) {
            if (event.keyCode == 9) {
                return;
            }
            cleanLabel('email');

            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                var value = $("#email").val();
                if (value == '') {
                    $("label[for='email']").append(" Email vide");
                } else if (!checkMail(value)) {
                    $("label[for='email']").append(" Ce n'est pas un email correct");
                } else {
                    $.ajax({
                        url: "/exist-email.html",
                        method: "POST",
                        dataType: "json",
                        data: {
                            value: value
                        },
                        success: function (data_a) {
                            if (data_a.success) {
                                $("label[for='email']").append(" Email deja utilisé");
                            }
                        }
                    });
                }
            }, 500));
        });

        $("#password2").on("paste keyup", function (event) {
            cleanLabel('password2');

            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                var value = $("#password1").val();
                if (value != $("#password2").val()) {
                    $("label[for='password2']").append(" Mot de passe différent");
                }
            }, 500));
        });

    }

    if ($("#news-modify").length) {
        var data = $("#tags").val();
        var tags = data.split(" ");
        for (var i in tags) {
            if (tags[i] != '') {
                add_tags(tags[i]);
            }
        }

    }

    if ($("#tags").length) {
        var available_tags = [];
        $.ajax({
            url: "/starting-limit-0.html",
            method: "POST",
            dataType: "json",
            success: function (data_a) {
                if (data_a.success == true) {
                    available_tags = data_a.contenu;
                }

            },
            error: function (un, deux, trois) {
                alert(un + ' ' + deux + ' ' + trois);
            }
        });

        $("#news-add, #news-modify").on("click", function () {
            $("#tags").css("display", "none");

            var tags = '';
            $(".tags").each(function (ind, elem) {
                var data = elem.innerHTML.split(" ");
                tags += data[0] + " ";
                console.log(tags);
            });
            $("#tags").val(tags);
        });

        $("#tags")
        // don't navigate away from the field on tab when selecting an item
            .on("keydown", function (event) {
                if (event.keyCode == $.ui.keyCode.BACKSPACE) {
                    if ($("#tags").getCursorPosition() == 0 || $("#tags").val() == '') {
                        $("#tags").prev("p").remove();
                    }
                }
                if (event.keyCode == $.ui.keyCode.SPACE) {
                    if ($("#tags").val() == '') {
                        event.preventDefault();
                    }
                    else {
                        add_tags("#" + $("#tags").val());

                        event.preventDefault();

                    }
                }
                if (event.keyCode == $.ui.keyCode.ENTER) {
                    if ($("#tags").val() == '') {
                        //event.stopPropagation();
                        event.preventDefault();
                    } else {
                        add_tags("#" + $("#tags").val());
                        event.preventDefault();

                    }
                }
                if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete(".tags").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 0,
                source: function (request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response($.ui.autocomplete.filter(
                        available_tags, extractLast(request.term)));
                },
                focus: function () {
                    // prevent value inserted on focus
                    return false;
                },
                select: function (event, ui) {
                    add_tags("#" + ui.item.value);
                    return false;
                }
            });
    }

    if ($("#check-comment").length) {
        $('.link-before, .link-after').each(function () {
            var link = $(this);
            $.ajax({
                url: link.attr("href"),
                method: "POST",
                dataType: "json",
                success: function (data_a) {
                    if (!data_a.success) {
                        link.html("");
                    }
                },
                error: function (u, d, t) {
                    alert(u + ' ' + d + ' ' + t);
                }
            });
        });

        $('.link-before, .link-after').click(function (event) {
            var link = $(this);
            var url = $(this).attr("href");
            var direction = '';
            var position = '';

            if ($(this).hasClass("link-before")) {
                direction = "up";
                position = "before";
            } else {
                direction = "down";
                position = "after";
            }

            $.ajax({
                url: url,
                method: "POST",
                dataType: "json",
                success: function (data_a) {
                    if (data_a.success) {
                        link[position](data_a.contenu);

                        $(".comment").show("slide", {direction: direction});

                        link.attr("href", data_a.link);

                        if (!data_a.next) {
                            link.remove();
                        }

                        $(".link-delete").click(function (event) {
                            return manageDeleteComment(this);
                        });
                    } else {
                        link.html("");
                    }
                },
                error: function (u, d, t) {
                    alert(u + ' ' + d + ' ' + t);
                }
            });
            return false;
        });

        $("#check-comment").click(function () {
            $(".comments").slideToggle(500);
        });

        $("#check-created").click(function () {
            $(".created").slideToggle(500);
        });

        $("#check-modified").click(function () {
            $(".modified").slideToggle(500);
        });
    }
});

function add_tags(value) {
    $("#tags").before("<p class='tags'>" + value + " <button>x</button> </p>");
    $("#tags").prev().children().on("click", function (event) {
        $(this).parent().remove();
    });
    $("#tags").val('');
}

function cleanLabel(name) {
    var req = 'label[for=\'' + name + '\']';
    var html = $(req).html();
    var length = html.indexOf(" :");
    if (length != -1) {
        $(req).html(html.substr(0, length+2));
    }
}

function checkMail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function split(val) {
    return val.split(" ");
}
// /,\s*/
function extractLast(term) {
    return split(term).pop();
}

function manageDeleteComment(button_clicked) {
    var url = $(button_clicked).attr("href");
    var links = $('a[href="' + url + '"]');
    var $fieldset_comment = links.parents(".comment");

    $.ajax({
        url: url,
        method: "POST",
        dataType: "json",
        success: function (data_a) {
            if (data_a.success == true) {

                $fieldset_comment.hide("slide", {direction: "right"}, 1000, function () {

                    $(this).remove();
                });

            }
        },
        error: function (u, d, t) {
            alert(u + ' ' + d + ' ' + t);
        }
    });
    return false;
};