These are the hard coded hacks that need to be monitored as plugins are updated as they will be overwritten.

\nhap.org\wp-content\plugins\s2member-pro\includes\templates\forms\paypal-checkout-form.php
\nhap.org\wp-content\plugins\s2member-pro\includes\templates\forms\paypal-registration-form.php
Add autocapitalize="none" to username fields to stop iOS capitalising first character of username eg.
<input type="text" aria-required="true" maxlength="60" autocomplete="off" autocapitalize="none" name="s2member_pro_paypal_checkout[username]" id="s2member-pro-paypal-checkout-username" class="s2member-pro-paypal-username s2member-pro-paypal-checkout-username form-control" value="%%username_value%%" tabindex="40" />

\nhap.org\wp-content\plugins\paypal-donations\views\paypal-button.php
Add input field to paypal button
	if ($amount) {
		$paypal_btn .=  '<input type="hidden" name="amount" value="' . apply_filters( 'paypal_donations_amount', $amount ) . '" />';
	} else {
		
		// Customised code added here ...
		$paypal_btn .=	'or enter an amount<br>';
		$paypal_btn .=  '<input type="text" pattern="[\d\.]*" name="amount" value="' . apply_filters( 'paypal_donations_amount', $amount ) . '" /><br>';
	}