function openFeedline() {
    $('.feedlinePopup').show(0, function(){
        $('.feedlineField:first-child').focus();
        $('.feedline').addClass('open');
    });
}

function closeFeedline() {
    $('.feedlineField').blur();
    $('.feedline').removeClass('open');
    setTimeout(function(){
        $('.feedlinePopup').hide();
    }, 400);
}

function toggleFeedline() {
    if ( $('.feedline').hasClass('open') ) {
        closeFeedline();
    } else {
        openFeedline();
    }
}

function feedlineEmailValidation(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function submitFeedline() {
    
    // validation
    
        var foundErrors = false;
    
        var inputName = $('#feedlineName').val();
        if ( inputName == '' ) {
            $('#feedlineNameValidation').html('Please enter your name').show();
            var foundErrors = true;
        } else {
            $('#feedlineNameValidation').html('').hide();
        }
        
        var inputEmail = $('#feedlineEmail').val();
        if ( inputEmail == '' ) {
            $('#feedlineEmailValidation').html('Please enter your email').show();
            var foundErrors = true;
        } else if ( !feedlineEmailValidation(inputEmail) ) {
            $('#feedlineEmailValidation').html("Sorry, your email doesn't look valid").show();
            var foundErrors = true;
        } else {
            $('#feedlineEmailValidation').html('').hide();
        }
        
        var inputMessage = $('#feedlineMessage').val();
        if ( inputMessage == '' ) {
            $('#feedlineMessageValidation').html('Please enter a message').show();
            var foundErrors = true;
        } else {
            $('#feedlineMessageValidation').html('').hide();
        }
        
    // submit
        
        if ( foundErrors == false ) {
            
            $('.feedlineLoading').fadeIn(300);
            
            $.ajax({
                method: 'POST',
                dataType: 'json',
                data: {
                    inputName: inputName,
                    inputEmail: inputEmail,
                    inputMessage: inputMessage
                },
                url: 'feedline.php',
                success: function(result) {
                    $('.feedlineLoading').fadeOut(300);
                    setTimeout(function(){
                        if ( result['status'] == 'success' ) {
                            
                            $('.feedlineSuccess').fadeIn(300);
                            $('.feedlineValidation').each(function(){
                                $(this).html('').hide();
                            });
                            
                        } else {
                            
                            if ( result['detail']['name'].length > 0 ) {
                                $('#feedlineNameValidation').html(result['detail']['name']).show();
                            } else {
                                $('#feedlineEmailValidation').html('').hide();
                            }
                                
                            if ( result['detail']['email'].length > 0 ) {
                                $('#feedlineEmailValidation').html(result['detail']['email']).show();
                            } else {
                                $('#feedlineEmailValidation').html('').hide();
                            }
                                
                            if ( result['detail']['message'].length > 0 ) {
                                $('#feedlineMessageValidation').html(result['detail']['message']).show();
                            } else {
                                $('#feedlineMessageValidation').html('').hide();
                            }
                            
                        }
                    }, 400);
                },
                error: function(result) {
                    $('.feedlineLoading').fadeOut(300);
                    alert('Sorry, something went wrong');
                }
            });
            
        }
    
}

$(document).ready(function(){
    
    $('.feedlineButton').click(function(){
        toggleFeedline();
    });
    
    $('.feedlineSubmit').click(function(){
        submitFeedline();
    });
    
    // check for prefilled fields
    $('.feedlineField').each(function(){
        if ( $(this).attr('data-prefilled') == 'true' ) {
            $(this).hide();
        }
    });
    
});