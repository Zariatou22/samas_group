var url = window.location.origin;
jQuery(function($) {
    //Effacer les alerts
    window.setTimeout(function() {
        $(".inner-notif").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
        });
    }, 7000);
    /**
     * Afficher ou cacher le mot de passe
     */
    $('.togglePasswordView').click(function() {
        const c = $(this);
        const input = c.prev();
        const i = c.find('i.fas');
        if (i.hasClass('fa-eye-slash')) {
            i.removeClass('fa-eye-slash').addClass('fa-eye');
            input.attr('type', 'text');
        } else {
            i.removeClass('fa-eye').addClass('fa-eye-slash');
            input.attr('type', 'password');
        }
    });
    /**
     * Vérification avant de soumettre le formulaire
    */
    $('#loginForm').submit(function(e) {
        const captchaToken = $('#captchaToken').val();
        const btn = $('.submit-login');
        const siteKey = btn.data('site-key');
        const user_token = '6cbd88a5753da410eeb24778a7d1ae9503f52fe5df5b89ddfa042481c472797ca0f711df061015159293b0416091f3a264319225c163461833f25e267de235f23jGTjRpehgAc9QQ51x5x8WhmRgyXw0bqRFOGf1tEhHw=';
        if ((url.indexOf('http://') !== -1 && url.indexOf('.lan') !== -1) || siteKey.length == 0) {
            $('#captchaToken').val(user_token);
            if (captchaToken.length == 0) {
                $('#loginForm').trigger('submit');
            }
            return;
        }
        if (captchaToken.length > 0) {
            return;
        }
        if (captchaToken.length === 0) {
            e.preventDefault();
        }
        // e.preventDefault();
        const btnText = btn.html();
        btn.addClass('disabled');
        btn.attr('disabled', 'disabled');
        btn.removeClass('btn-primary').addClass('btn-light');
        btn.html(`<img src="/assets/images/loading.gif" width="16" height="16">`);
        grecaptcha.ready(function() {
            grecaptcha.execute(siteKey, {action: 'submit'}).then(function(token) {
                if (token.length === 0) {
                    window.location.href = `${url}/account/login-otp.html`;
                    return;
                }
                $.post('/ajax/verify-captcha', {token}, function(data) {
                    if (!data.error) {
                        $('#captchaToken').val(data.data);
                        $('#loginForm').trigger('submit');
                    } else {
                        display_error('Une erreur s\'est produite');
                        console.warn(data.ermsg);
                        btn.removeClass('btn-light').addClass('btn-primary');
                        btn.removeClass('disabled');
                        btn.removeAttr('disabled');
                        btn.html(btnText);
                    }
                }, "JSON")
            });
        });
    });
});

function display_error(message) {
    $('#ajaxError').removeClass('hidden').html(message);
    setTimeout(function() {
        $('#ajaxError').fadeOut('slow').addClass('hidden').html('');
    }, 5000);
}