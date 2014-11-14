document.observe("dom:loaded", function() {
    if( typeof( document.getElementById('payment_form_twocheckout') ) !== 'undefined' ) {
        TCO.loadPubKey(TcoEnv);
        Event.observe('onestepcheckout-form', 'click', function(event) {

            if( payment.currentMethod === 'twocheckout') {

                $('onestepcheckout-place-order').stopObserving('click');

                Event.observe('onestepcheckout-place-order', 'click', function(event) {
                    if( payment.currentMethod === 'twocheckout') {
                        var form = new VarienForm('onestepcheckout-form');
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
                                    $$('div.onestepcheckout-error')[0].innerHTML = data.errorMsg;
                                } else {
                                    document.getElementById('twocheckout_token').value=data.response.token.token;
                                    $('onestepcheckout-form').submit();
                                }
                            });
                        }
                    } else {
                        $('onestepcheckout-form').submit();
                    }
                });
            }
        });
    }
});