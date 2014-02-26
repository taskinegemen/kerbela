<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha256.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/aes.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/mode-cfb-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/mode-ecb-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/pad-zeropadding-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/enc-base64-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/core-min.js"></script>
<script type="text/javascript">




	$(document).ready(function()
	{

		$.post("/api/authenticate/",
				{
						'user_id' : 'egemen@linden-tech.com',
						'requested_service' : 'kerbela',
						'ip' : '81.215.15.54',
						'requested_lifetime' : '6000'
				},
				function(result)
				{
					console.log(result);
					var TGT=result.TGT;
    				var TGT_client=CryptoJS.enc.Base64.parse(result.TGT_client);
    				console.log(TGT_client);
    				var key=CryptoJS.enc.Hex.parse('123456');
    				console.log(key);
    				var TGT_client_decrypted =CryptoJS.AES.decrypt(TGT_client, '123456', { padding: CryptoJS.pad.ZeroPadding});
    				console.log(TGT_client_decrypted.toString());
    				console.log(CryptoJS.enc.Utf8.stringify(TGT_client_decrypted));
					/*
    				console.log(result);
    				var TGT=result.TGT;
    				var TGT_client=atob(result.TGT_client);
    				//console.log(TGT_client);
    				//console.log(TGT_client.replace("\n",""));
    				var TGS_client_key=CryptoJS.SHA256('12548442').toString(CryptoJS.enc.Hex);
    				var ivector  =CryptoJS.enc.Utf8.parse('9056c0b252735537').toString(CryptoJS.enc.hex);
    				//console.log(CryptoJS.enc.Utf8.stringify(ivector));
    				//console.log(CryptoJS.enc.Hex.parse(ivector));
    				console.log(TGS_client_key);
    				console.log(ivector);
    				//var TGT_client =CryptoJS.AES.encrypt('egemen', TGS_client_key, { iv:ivector,mode:CryptoJS.mode.CFB, padding: CryptoJS.pad.ZeroPadding});
    				//console.log(TGT_client);
    				//var TGT_client_decrypted =CryptoJS.AES.decrypt(TGT_client, TGS_client_key, {iv:ivector,mode:CryptoJS.mode.CFB,padding: CryptoJS.pad.ZeroPadding});
    				//console.log(TGT_client_decrypted.toString());
    				//console.log(TGT_client_decrypted.toString());
    				//console.log(CryptoJS.enc.Utf8.stringify(TGT_client_decrypted));
    				//console.log(TGT_client_decrypted.toString(CryptoJS.enc.Utf8));
    				
    				//var TGT_client_decrypted =CryptoJS.AES.decrypt(TGT_client, TGS_client_key, { iv:ivector,mode:CryptoJS.mode.CFB, padding: CryptoJS.pad.ZeroPadding});
    				var TGT_client_decrypted =CryptoJS.AES.decrypt(TGT_client, TGS_client_key, {mode:CryptoJS.mode.ECB, padding: CryptoJS.pad.ZeroPadding});
    				//console.log(TGT_client_decrypted.toString());
    				console.log(CryptoJS.enc.Utf8.stringify(TGT_client_decrypted));
					//TGT_client_decrypted=CJSON::decode($this->decrypt($TGT_client,$TGS_client_key));
					*/
  				}
  		  	);
	}
	);
</script>

</head>
<body>
<?php
/* @var $this HttpServiceController */

$this->breadcrumbs=array(
	'Http Service',
);
?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>

<p>
	You may change the content of this page by modifying dsfsdfsd
	the file <tt><?php echo __FILE__; ?></tt>.
</p>
</body>
</html>