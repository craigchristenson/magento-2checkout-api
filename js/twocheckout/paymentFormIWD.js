document.observe("dom:loaded", function() {
    if( typeof( document.getElementById('payment_form_twocheckout') ) !== 'undefined' ) {
        TCO.loadPubKey(TcoEnv);

        IWD.OPC.savePayment = IWD.OPC.savePayment.wrap(
            function(callOriginal) {
                if (IWD.OPC.Checkout.xhr!==null){
                    IWD.OPC.Checkout.xhr.abort();
                }
                IWD.OPC.Checkout.lockPlaceOrder();
                if (payment.currentMethod !== 'twocheckout') {
                    return callOriginal();
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
            }
        );
    }
});
