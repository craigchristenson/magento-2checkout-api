var p_method_twocheckout;
var authButtonContainer;
var authButton;
var TcoCheckoutType = {
    checkoutStepPayment: null,
    authButtonContainer: null,
    checkoutForm: null
};

function setTcoCheckoutType() {
    if ( document.getElementById("opc-payment") === null ) {
        TcoCheckoutType.checkoutStepPayment = "checkout-payment-method-load";
        TcoCheckoutType.authButtonContainer = "review-buttons-container";
        TcoCheckoutType.checkoutForm = 'onepagecheckout_orderform';
    } else if ( typeof( document.getElementById("checkout-step-payment") ) !== "undefined" ) {
        TcoCheckoutType.checkoutStepPayment = "checkout-step-payment";
        TcoCheckoutType.authButtonContainer = "checkout-step-payment";
        TcoCheckoutType.checkoutForm = 'co-payment-form';
    }
}

function successCallback(data,status,jqXHR) {
    document.getElementById("twocheckout_token").value=data.response.token.token;
    if( TcoCheckoutType.checkoutForm === 'co-payment-form' ) {
        payment.save();
    } else {
        checkout.save();
    }
}

function errorCallback(data,status,jqXHR) {
	if(data.errorCode === 200) {
		TCO.requestToken(successCallback, errorCallback, TcoCheckoutType.checkoutForm );
	} else {
		alert(data.errorMsg);
		document.getElementById('ccNo').value = '';
		document.getElementById('cvv').value = '';
		document.getElementById('expMonth').value = '';
		document.getElementById('expYear').value = '';
		authButton.removeAttribute("onclick");
		authButton.addEventListener("click", retrieveToken, false);
	}
}

function retrieveToken(form) {
    if(typeof TCO.requestToken == 'undefined'){
        infoDiv = document.getElementById("infoArea");
        infoDiv.style.display="";
        infoDiv.innerHTML = "<h5> Error </h5>" + "Error Code - 200" +
            "<br />" + " Status Message - Unable to process the request" + "<br />"
    }
    else {
        document.getElementById('ccNo').value = document.getElementById('ccNo').value.replace(/[^0-9\.]+/g,'');
        TCO.requestToken(successCallback, errorCallback, TcoCheckoutType.checkoutForm );
    }
}

Event.observe(window, "click", function() {
    if( typeof( document.getElementById('payment_form_twocheckout') ) !== "undefined" ) {
        setTcoCheckoutType();
        if(typeof(p_method_twocheckout) === "undefined" &&
            document.getElementById(TcoCheckoutType.authButtonContainer) !== "undefined") {
            p_method_twocheckout = document.getElementById("payment_form_twocheckout");
            authButtonContainer = document.getElementById(TcoCheckoutType.authButtonContainer);
            authButton = authButtonContainer.getElementsByTagName("button")[0];
        }

        if( typeof(p_method_twocheckout) !== "undefined" && authButton.hasAttribute("onclick") &&
            payment.currentMethod === 'twocheckout' ) {
            var checkoutStepPayment = document.getElementById(TcoCheckoutType.checkoutStepPayment);
            authButton.removeAttribute("onclick");
            authButton.addEventListener("click", retrieveToken, false);
        }
    }
});