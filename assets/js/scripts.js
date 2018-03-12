var dataClient = null;

$(document).ready(function() {

    setInterval(function() {
        $(".codigo.form-control.frm").each(function() {
            if ($(this).parent("div").hasClass("has-error")) {
                if ($(this).val().length < 8) {
                    $(".box-alert").addClass("active");
                } else {
                    $(".box-alert").removeClass("active");
                }
            } else {
                $(".box-alert").removeClass("active");
            }
        });
    }, 3000);


    $("input.codigo").keyup(function() {
        if ($(this).val() != "") {

            if ($(this).val().length < 8) {
                $(this).parent('div').addClass('has-error').removeClass('has-success');
                $(this).siblings('span.glyphicon').addClass('glyphicon-remove').removeClass('glyphicon-ok');
            } else {
                $(this).parent('div').addClass('has-success').removeClass('has-error');
                $(this).siblings('span.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-remove');
            }
        }

    });

    $("input#cedula").keypress(function(tecla) {
        if (tecla.charCode < 48 || tecla.charCode > 57) return false;
    });

    $("input.codigo").keypress(function(tecla) {
        if (tecla.charCode < 48 || tecla.charCode > 57) return false;
    });

    $("input#telefono").keypress(function(tecla) {
        if (tecla.charCode < 48 || tecla.charCode > 57) return false;
    });



    var maxField = 5; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.box-code'); //Input field wrapper
    var fieldHTML = `<div class="input-group form-group codigo">
                    <span class="input-group-addon">Código</span>
                    <input type="text" autocomplete="off" onpaste="return false;" class="codigo form-control frm" maxlength="8" name="code[]" placeholder="xxxxxxxx">
                    <span class="glyphicon form-control-feedback"></span>
                    <a href="javascript:void(0);" class="remove_button" title="Borrar este código"><img class="btn-frm" src="/sites/all/modules/custom/tedaMas/assets/image/remove-icon.png"></a>
                </div>`;
    var x = 1; //Initial field counter is 1
    $(addButton).click(function() { //Once add button is clicked
        if (x < maxField) { //Check maximum number of input fields
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); // Add field html
        }
        $("input.codigo").keypress(function(tecla) {
            if (tecla.charCode < 48 || tecla.charCode > 57) return false;
        });

        $("input.codigo").keyup(function() {
            if ($(this).val() != "") {

                if ($(this).val().length < 8) {
                    $(this).parent('div').addClass('has-error').removeClass('has-success');
                    $(this).siblings('span.glyphicon').addClass('glyphicon-remove').removeClass('glyphicon-ok');
                } else {
                    $(this).parent('div').addClass('has-success').removeClass('has-error');
                    $(this).siblings('span.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-remove');
                }
            }
        });
    });
    $(wrapper).on('click', '.remove_button', function(e) { //Once remove button is clicked
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });


    $("input#cedula").keyup(function() {
        verify_cedula();
    });




    $(".codigo.form-control:eq(0)").keyup(function() {
        var has_code = $(this).parent("div").hasClass("has-success");
        if ($(this).val() != "" && $("input#cedula").val() != "") {
            if (dataClient.code == 200 && dataClient.status == "success") {
                if (has_code) {
                    $(".col-xs-3.bs-wizard-step:eq(1)").addClass("complete");
                    $("#next-step").prop("disabled", false);
                    $(".input-group.form-group.btn.next").hide();
                    $(".input-group.form-group.btn.send").addClass("active");
                } else {
                    $(".input-group.form-group.btn.next").show();
                    $(".input-group.form-group.btn.send").removeClass("active");
                }
            } else {
                $("input#cedula").parent('div').removeClass('has-success');
                $(".col-xs-3.bs-wizard-step:eq(1)").removeClass("complete");
                if (has_code) {
                    $("#next-step").prop("disabled", false);
                }

            }
        }

    });

    var paso_1 = $(".col-xs-3.bs-wizard-step:eq(0)").hasClass("complete");
    var paso_2 = $(".col-xs-3.bs-wizard-step:eq(1)").hasClass("complete");
    var paso_3 = $(".col-xs-3.bs-wizard-step:eq(2)").hasClass("complete");

    if (paso_1 && paso_2) {
        $(".input-group.form-group.btn.sent").addClass("active");
    } else {
        $(".input-group.form-group.btn.sent").removeClass("active");

    }

    $(".bs-wizard-dot").click(function(e) {
        e.preventDefault();
    });

    setInterval(function() {
        verify_cedula();
    }, 1000);

    $("#next-step").click(function(e) {
        e.preventDefault();
        var step = $(this).attr("data-step");
        if (dataClient.code == 200 && dataClient.status == "success") {
            $(".step-div").each(function() { $(this).removeClass("active"); });
            $("#step-" + step).addClass("active");
            $(".input-group.form-group.btn.next").hide();
            $(".input-group.form-group.btn.send").addClass("active");
        } else if (step == "2") {
            $(".col-xs-3.bs-wizard-step:eq(1)").addClass("complete");
            $(".step-div").each(function() {
                $(this).removeClass("active");
            });
            $("#step-2").addClass("active");
            $("#next-step").prop("disabled", true);
            $("input#concursar").prop("disabled", true);
            setInterval(function() {
                verify_cedula();
            }, 1000);

            setInterval(function() {
                checkinput();
            }, 1000);
            $(".input-group.form-group.btn.next").hide();
            $(".input-group.form-group.btn.send").addClass("active");
            $(".col-xs-3.bs-wizard-step").each(function() { $(this).removeClass("here"); });
            $(".col-xs-3.bs-wizard-step:eq(1)").addClass("here");
            nextStep(3);
        } else if (step == "3") {
            /* $(".step-div").each(function() {
                 $(this).removeClass("active");
             });
             $(".col-xs-3.bs-wizard-step:eq(1)").addClass("complete");
             $("#step-3").addClass("active");
             */


        }

    });

    $("#concursar").click(function(e) {
        $(".col-xs-3.bs-wizard-step:eq(2)").addClass("complete");
        $(this).val("Concursando ...");
        $(this).prop("disabled", true);
        e.preventDefault();
        var $form = $(this),
            url = "/te-damos-mas/verify/new/" + $("input#cedula").val();
        /* Send the data using post with element id name and name2*/
        var posting = $.post(
            url, {
                cedula: $('input#cedula').val(),
                mail: $('input#mail').val(),
                nombre: $('input#nombre').val(),
                apellido: $('input#apellido').val(),
                telefono: $('input#telefono').val(),
                direccion: $('input#direccion').val(),
                code1: $('.codigo.form-control.frm:eq(0)').val(),
                code2: $('.codigo.form-control.frm:eq(1)').val(),
                code3: $('.codigo.form-control.frm:eq(2)').val(),
                code4: $('.codigo.form-control.frm:eq(3)').val(),
                code5: $('.codigo.form-control.frm:eq(4)').val(),
            });

        posting.done(function(data) {
            resul = JSON.parse(data);
            if (resul.code == "200" && resul.status == "success" && resul.message == "almacenado") {
                $(".input-group.form-group.btn.send").removeClass("active");
                $("#step-1").removeClass("active");
                $("#step-2").removeClass("active");
                $("#step-3").addClass("active");
                $("#uid_send").text($('input#cedula').val());
                $(".codigo.form-control.frm").each(function() {
                    $("#code_send").append("<li>" + $(this).val() + "</li>");
                });
                $(".col-xs-3.bs-wizard-step").each(function() { $(this).removeClass("here"); });
                $(".col-xs-3.bs-wizard-step:eq(2)").addClass("here");
            } else {
                alert("Se ha presentado una falla en el sistema, intente nuevamente.");
            }
        });

    });

    $('input#mail').keyup(function() {
        var sEmail = $(this).val();
        if (mailValidate(sEmail)) {
            $(this).parent("div").addClass("has-success").removeClass("has-error");
            $(this).siblings('span.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-warning-sign');
            $(this).attr("title", "");
        } else {
            $(this).parent("div").addClass("has-error").removeClass("has-success");
            $(this).siblings('span.glyphicon').addClass('glyphicon-warning-sign').removeClass('glyphicon-ok');
            $(this).attr("title", "Debes ingresar un Email válido Ej: example@example.com");

        }
    });

    $('.form-control.frm.person').keyup(function() {
        if ($(this).val() != "") {
            $(this).parent("div").addClass("has-success").removeClass("has-error");
            $(this).siblings('span.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-warning-sign');
            $(this).attr("title", "");
        } else {
            $(this).parent("div").addClass("has-error").removeClass("has-success");
            $(this).siblings('span.glyphicon').addClass('glyphicon-warning-sign').removeClass('glyphicon-ok');
            $(this).attr("title", "Debes ingresar un " + $(this).attr("name"));
        }
    });



});

function nextStep(step) {
    $("#next-step").attr("data-step", step);
}

function checkinput() {
    if ($('#step-2').hasClass("active")) {

        field_mail = $('input#mail').parent("div").hasClass("has-success");
        field_nombre = $('input#nombre').parent("div").hasClass("has-success");
        field_apellido = $('input#apellido').parent("div").hasClass("has-success");
        field_tel = $('input#telefono').parent("div").hasClass("has-success");
        field_dir = $('input#direccion').parent("div").hasClass("has-success");

        if (field_mail && field_nombre && field_apellido && field_tel && field_dir) {
            habeas = $("#habeas").is(':checked');
            terminos = $("#terminos").is(':checked');
            if (habeas && terminos) {
                $("input#concursar").prop("disabled", false);
            }
        } else {
            $("input#concursar").prop("disabled", true);
        }
    }
}

function verify_cedula() {
    if ($('#step-1').hasClass("active")) {
        if ($('input#cedula').val() != "") {
            if ($('input#cedula').val().length >= 5) {
                $('.codigo.form-control.frm').prop("disabled", false);
                $('#concursar').prop("disabled", false);
                $('a.add_button').addClass('active');

                if ($(".codigo.form-control:eq(0)").val().length == 8) {
                    $("#next-step").prop("disabled", false);
                } else {
                    $("#next-step").prop("disabled", true);
                }

                var has_code = $(".codigo.form-control:eq(0)").parent("div").hasClass("has-success");
                var has_cedu = $("input#cedula").parent("div").hasClass("has-success");
                if (has_code && has_cedu) {
                    $(".col-xs-3.bs-wizard-step:eq(1)").addClass("complete");
                    $(".col-xs-3.bs-wizard-step").each(function() { $(this).removeClass("here"); });
                    $(".col-xs-3.bs-wizard-step:eq(1)").addClass("here");
                } else {
                    $(".col-xs-3.bs-wizard-step:eq(1)").removeClass("complete");
                    if (has_code) {
                        if ($('input#cedula').val().length >= 5) {
                            $('#concursar').prop("disabled", true);
                            $("#next-step").addClass('active');
                            $("#next-step").prop("disabled", false);
                            $(".input-group.form-group.btn.next").show();
                        }
                    } else {
                        $("#next-step").prop("disabled", true);
                    }
                }

            } else {
                $('.codigo.form-control.frm').prop("disabled", true);
                $('#concursar').prop("disabled", true);
                if ($('.codigo.form-control.frm').val().length == 8) {
                    $("#next-step").prop("disabled", true);
                    $('a.add_button').removeClass('active');
                }
            }

            $.ajax({
                url: '/te-damos-mas/verify/idusuario/' + $('input#cedula').val(),
                success: function(response) {
                    dataClient = JSON.parse(response);
                    if (dataClient.code == 200 && dataClient.status == "success") {
                        $("input#cedula").parent('div').addClass('has-success');
                    } else {
                        $("input#cedula").parent('div').removeClass('has-success');
                        nextStep(2);
                    }

                }
            });
        } else {
            $("input#cedula").parent('div').removeClass('has-success');
            $('.codigo.form-control.frm').prop("disabled", true);
            $('a.add_button').removeClass('active');
            $("#next-step").prop("disabled", true);

        }
    }
}


function mailValidate(email) {
    var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if (filter.test(email)) {
        return true;
    } else {
        return false;
    }
}