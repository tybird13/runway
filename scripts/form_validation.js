/**
 * Created by tjdal on 7/13/2017.
 */
$(function(){
    $.validator.setDefaults({
        errorClass: 'text-danger',

        highlight: function(element){
            $(element)
                .closest(".form-group")
                .addClass("has-error")
        },
        unhighlight: function(element){
            $(element)
                .closest(".form-group")
                .removeClass("has-error")
                .addClass("has-success")
        },
        errorPlacement: function (error, element) {
            if (element.prop('type') === 'checkbox') {
                error.insertAfter(element.parent());
            } else if(element.prop('id') === 'signinusername') {
                error.insertAfter('#errorTarget')
            } else {
                error.insertAfter(element);
            }
        }

    });

    $("#verify").validate({
        rules: {
            fname: {required:true},
            lname: {required:true},
            email: {email:true, required:true}
        }
    });

    $("#login").validate({
        rules: {
            UIN: {required:true, UIN:true},
            pass: {required:true}
        }
    });

    $('#create-password').validate({
        rules: {
            new_pass: {required:true},
            retype_pass: {required:true, equalTo:'#new_pass'}
        }
    });

    $.validator.addMethod('UIN', function(uin){
        return /^(\d){9}$/.test(uin);
    }, "Please enter a University Identification Number. EG: (814XXXXXX)");

    // $.validator.addMethod('is_age', function (a) {
    //     return /^([1-9][0-9])$/.test(a)
    // }, "Please enter an appropriate age. Eg: 25");
});
