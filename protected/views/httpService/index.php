<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha256.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/aes.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/mode-cfb-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/mode-ecb-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/pad-zeropadding-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/pad-nopadding-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/enc-base64-min.js"></script>
<script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/components/core-min.js"></script>
<script src="//kerbela.lindneo.com/js/gibberish-aes.js"></script>
<script src="//kerbela.lindneo.com/js/kerbela.js"></script>
<script type="text/javascript">


	$(document).ready(function()
	{
		var kerbela=$(window).kerbelainit('http://kerbela.lindneo.com/api/authenticate/','http://kerbela.lindneo.com/api/ticketgrant/','http://kerbela.lindneo.com/httpservice/authenticate','egemen@linden-tech.com','12548442','kerbela','koala','6000');
		var response=kerbela.execute();
		console.log(response);

		console.log(kerbela.getSource('http://kerbela.lindneo.com/httpservice/service',{name:'egemen',surname:'taskin'}));


		/*
		$.ajaxSetup({async:false});
		var result=new Object();

		makeRequest("/api/authenticate/",
				{
						'user_id' : 'egemen@linden-tech.com',
						'requested_service' : 'kerbela',
						'ip' : '81.215.15.54',
						'requested_lifetime' : '6000',
						'type':'web'
				},function(response){result['source']=response;});
		console.log(result);
		var TGT=result.source.TGT;
		var TGT_client=result.source.TGT_client;
		var TGS_client_key=CryptoJS.SHA256('12548442').toString(CryptoJS.enc.Hex);
		var TGT_client_decrypted =decoder(CryptoJS.AES.decrypt(TGT_client, TGS_client_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		console.log(TGT_client_decrypted);
		TGS_session_key=TGT_client_decrypted.TGS_session_key;
/////////////////////////////////////////////////////////////////////////////////////////////

		var AUTH=CryptoJS.AES.encrypt("{user_id:egemen@linden-tech.com,timestamp:"+getTimestamp()+"}", TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		var AUTH_Dec=decoder(CryptoJS.AES.decrypt(AUTH, TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		console.log(AUTH_Dec);
		var result=new Object();
		makeRequest("/api/ticketgrant/",
				{
						'requested_http_service' : 'koala',
						'requested_service' : TGT_client_decrypted.requested_service,
						'auth' : encodeURI(AUTH),
						'tgt':encodeURI(TGT),
						'type':'web'
				},function(response){result['source']=response;});
		console.log(result.source);
		HTTP_service_ticket=result.source.HTTP_service_ticket;
		HTTP_session_ticket=result.source.HTTP_session_ticket;
		console.log(HTTP_session_ticket);
		console.log(HTTP_service_ticket);
//////////////////////////////////////////////////////////////////////////////////////////////
		HTTP_session_ticket_decrypted=decoder(CryptoJS.AES.decrypt(HTTP_session_ticket, TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		HTTP_service_session_key=HTTP_session_ticket_decrypted.HTTP_service_session_key;

		var AUTH=CryptoJS.AES.encrypt("{user_id:egemen@linden-tech.com,timestamp:"+getTimestamp()+"}", HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		var result=new Object();
		makeRequest("/httpservice/authenticate",
				{
					'auth':encodeURI(AUTH),
					'http_service_ticket':encodeURI(HTTP_service_ticket),
					'type':'web'
				},function(response){result['source']=response;},"text");
		httpservice=result.source;
		httpservice_response_decrypted=decoder(CryptoJS.AES.decrypt(httpservice, HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		console.log(httpservice_response_decrypted);
/////////////////////////////////////////////////////////////////////////////////////////////// log in completed!
		var AUTH=CryptoJS.AES.encrypt("{user_id:egemen@linden-tech.com,timestamp:"+getTimestamp()+"}", HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		var result=new Object();
		makeRequest("/httpservice/service",
				{
					'auth':encodeURI(AUTH),
					'http_service_ticket':encodeURI(HTTP_service_ticket),
					'type':'web'
				},function(response){result['source']=response;},"text");

		httpservice=result.source;
		console.log(httpservice);

		*/
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
<!--
					/*
					console.log(result);
					TGT=result.TGT;
					TGT_client=result.TGT_client;
					var TGS_client_key=CryptoJS.SHA256('12548442').toString(CryptoJS.enc.Hex);
					console.log(TGS_client_key);
					var init_vec=CryptoJS.enc.Hex.parse('0000000000000000');

					console.log(init_vec);
					var second=CryptoJS.AES.decrypt("U2FsdGVkX1/fbuknD11t9rc2OzED2T3A3/A/MAjTvnA=", "password",{mode:CryptoJS.mode.ECB}).toString(CryptoJS.enc.Utf8);
					console.log(second);

					//console.log(GibberishAES.dec(TGT_client, TGS_client_key));
		
					var TGT_client_decrypted =CryptoJS.AES.decrypt(TGT_client, TGS_client_key="ThisIsMyPassword",{mode:CryptoJS.mode.CBC,padding: CryptoJS.pad.ZeroPadding}).toString(CryptoJS.enc.Utf8);
					*/
					//var TGT_client_decrypted_hex =CryptoJS.AES.decrypt(TGT_client, TGS_client_key,{mode:CryptoJS.mode.ECB,padding: CryptoJS.pad.ZeroPadding});
					//console.log(TGT_client_decrypted_hex.toString(CryptoJS.enc.Hex));
					/*
					console.log(result);
					//var enc = GibberishAES.enc("This sentence is super secret", "password");
    				//console.log(enc);
    				//console.log(GibberishAES.dec(result.TGT_client, "password"));
					//var TGT_client =CryptoJS.AES.encrypt('gizli', '', { mode:CryptoJS.mode.EBC, padding: CryptoJS.pad.ZeroPadding});

					var TGT_client_decrypted =CryptoJS.AES.decrypt(result.TGT_client, "password");
					console.log(TGT_client_decrypted.toString(CryptoJS.enc.Utf8));

					var TGT_client_encrypted =CryptoJS.AES.encrypt("selam dostum", "password");
					console.log(TGT_client_encrypted.toString(CryptoJS.enc.base64));*/

					/*var enc = GibberishAES.enc("selam dostum", "password");
    				console.log(enc);*/
					/*
					console.log(result);
					//var TGT_client=CryptoJS.enc.Base64.parse(result.TGT_client);
					console.log(CryptoJS.enc.Hex.parse(btoa(result.TGT_client)));
					var TGT_client=CryptoJS.enc.Hex.parse(btoa(result.TGT_client));
					var key=CryptoJS.enc.Hex.parse('000000000000000000000000000000000000000000000000');
					error.log(key);
					var TGT_client =CryptoJS.AES.encrypt('gizli', key, {mode:CryptoJS.mode.ECB, padding: CryptoJS.pad.ZeroPadding});
					console.log(TGT_client);
					console.log(TGT_client.toString(CryptoJS.enc.Latin1));
					*/
					/*
					console.log(CryptoJS.AES.decrypt(
        			TGT_client,
        			CryptoJS.enc.Latin1.parse('000000000000000000000000000000000000000000000000'),
        			{
            				iv: CryptoJS.lib.WordArray.create([0x00000000, 0x00000000, 0x00000000, 0x00000000]),
            				padding: CryptoJS.pad.ZeroPadding
        			}
    				).toString(CryptoJS.enc.Latin1));*/
					/*
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

					-->