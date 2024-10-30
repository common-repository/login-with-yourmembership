(function($){
    $(window).on('load', function(e){
        var target_btn = $("#moym_button");
        var before_element = $("#loginform p:first");
        before_element.before(target_btn);
    });
})(jQuery);

function loginWithSSOButton(id) {
    if( id === "moym_login_sso_button")
        document.getElementById("moym_user_login_input").value = "moymsso";
    document.getElementById("loginform").submit(); 
}