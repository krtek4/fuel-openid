<!-- Simple OpenID Selector -->
<form action="<?php echo $url; ?>" method="get" id="openid_form">
	<input type="hidden" name="action" value="verify" />
	<fieldset>
		<legend><?php echo $title; ?></legend>
		<div id="openid_choice">
			<p><?php echo $explanation; ?></p>
			<div id="openid_btns"></div>
		</div>
		<div id="openid_input_area">
			<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
			<input id="openid_submit" type="submit" value="Sign-In"/>
		</div>
		<noscript>
			<p>OpenID is service that allows you to log-on to many different websites using a single indentity. Find out <a href="http://openid.net/get-an-openid/what-is-openid/">more about OpenID</a> and <a href="http://openid.net/get-an-openid/"> how to get an OpenID enabled account</a>.</p>
		</noscript>
	</fieldset>
</form>
<!-- /Simple OpenID Selector -->