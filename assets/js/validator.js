$.validator.setDefaults({
    errorElement: "span",
    errorClass: "help-block",
    highlight: function(element) {
        $(element).parent().removeClass('has-success').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).parent().removeClass('has-error').addClass('has-success');
    },
    errorPlacement: function(error, element) {
        if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});