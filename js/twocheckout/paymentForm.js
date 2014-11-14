document.observe("dom:loaded", function() {
    if( typeof( document.getElementById('payment_form_twocheckout') ) !== 'undefined' ) {
        TCO.loadPubKey(TcoEnv);

        payment.save = payment.save.wrap(
            function(callOriginal) {
                if( payment.currentMethod === 'twocheckout') {
                    var form = new VarienForm('co-payment-form');
                    if(!form.validator.validate())  {
                        return; 
                    } else {
                        var args = {
                            sellerId: document.getElementById('sellerId').value,
                            publishableKey: document.getElementById('publishableKey').value,
                            ccNo: document.getElementById('ccNo').value,
                            cvv: document.getElementById('cvv').value,
                            expMonth: document.getElementById('expMonth').value,
                            expYear: document.getElementById('expYear').value
                        };

                        TCO.requestToken(args, function(data) {
                            if ( typeof(data.errorCode) !== 'undefined' ) {
                                document.getElementById('ccNo').value = '';
                                document.getElementById('cvv').value = '';
                                document.getElementById('expMonth').value = '';
                                document.getElementById('expYear').value = '';
                                alert(data.errorMsg);
                                return;
                            } else {
                                document.getElementById("twocheckout_token").value=data.response.token.token;
                                return callOriginal();
                            }
                        });
                    }
                } else {
                    return callOriginal();
                }
            }
        );
    }
});
