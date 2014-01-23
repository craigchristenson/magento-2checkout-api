function successCallback(data,status,jqXHR) {
    if(data.exception != null || (data.validationErrors != null && data.validationErrors.length > 0)){
        console.log(data);
    }
    else{
        document.getElementById("twocheckout_token").value=data.response.token.token;
        payment.save();
    }
}

function errorCallback(data,status,jqXHR) {
    alert(data.errorMsg);
    document.getElementById('ccNo').value = '';
    document.getElementById('cvv').value = '';
    document.getElementById('expMonth').value = '';
    document.getElementById('expYear').value = '';
    var checkoutStepPayment = document.getElementById("checkout-step-payment");
    var authButton = checkoutStepPayment.getElementsByTagName("button")[0];
    authButton.removeAttribute("onclick");
    authButton.addEventListener("click", payment.save(), false);
}

function retrieveToken() {
    if(typeof TCO.requestToken == 'undefined'){
        infoDiv = document.getElementById("infoArea");
        infoDiv.style.display="";
        infoDiv.innerHTML = "<h5> Error </h5>" + "Error Code - 200" +
            "<br />" + " Status Message - Unable to process the request" + "<br />"
    }
    else {
        TCO.requestToken(successCallback, errorCallback, 'co-payment-form');
    }
}


Event.observe(window, "click", function() {

    if( typeof( document.getElementById('payment_form_twocheckout') ) !== "undefined" ) {

        var checkoutStepPayment = document.getElementById("checkout-step-payment");
        checkoutStepPayment.addEventListener('keyup', function () {
            if(authButton == null) {
                var authButton = checkoutStepPayment.getElementsByTagName("button")[0];
                authButton.removeAttribute("onclick");
                authButton.addEventListener("click", retrieveToken, false);
            }
        });

    }

});