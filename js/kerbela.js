(function ( $ ) {

    var AuthenticationServer;
 	var TicketGrantingServer;
 	var KerberizedServer;
 	var UserId;
 	var RequestedService;
 	var RequestedHttpService;
 	var Ip;
 	var RequestedLifetime;

 	$.fn.kerbelainit=function(AS,TGS,KS,UI,RS,RHS,RL){
 		$.ajaxSetup({async:false});
 		this.setAS(AS);
 		this.setTGS(TGS);
 		this.setKS(KS);
 		this.setUserId(UI);
 		this.setRequestedService(RS);
 		this.setRequestedHttpService(RHS);
 		this.setRequestedLifetime(RL);
 		this.setIp();
 		return this;
 	};
 	
 	$.fn.setAS=function(AS){this.AuthenticationServer=AS;};
 	$.fn.getAS=function(){return this.AuthenticationServer;};

 	$.fn.setTGS=function(TGS){this.TicketGrantingServer=TGS;};
 	$.fn.getTGS=function(){return this.TicketGrantingServer;};

 	$.fn.setKS=function(KS){this.KerberizedServer=KS;};
 	$.fn.getKS=function(){return this.KerberizedServer;};

 	$.fn.setUserId=function(UI){this.UserId=UI;};
 	$.fn.getUserId=function(){return this.UserId;};

 	$.fn.setRequestedService=function(RS){this.RequestedService=RS;};
 	$.fn.getRequestedService=function(){return this.RequestedService;};

 	$.fn.setRequestedHttpService=function(RHS){this.RequestedHttpService=RHS;};
 	$.fn.getRequestedHttpService=function(){return this.RequestedHttpService;};

 	$.fn.setIp=function(){
 		var that=this;
 		var result=new Object();
		this.makeRequest('/api/getip',
				'',function(response){that.Ip=response.ip;},'','GET');
		

 	};
 	$.fn.getIp=function(){return this.Ip;}

 	$.fn.setRequestedLifetime=function(RL){this.RequestedLifetime=RL;};
 	$.fn.getRequestedLifetime=function(){return this.RequestedLifetime;};

 	$.fn.decoder=function (string){
		var json=new Object();
		string=string.replace('{','');
		string=string.replace('}','');
		string=string.split(',');
		for(var i=0;i<string.length;i++){
			var item=string[i].split(':');
			json[''+item[0]]=item[1].trim();
		}
		return json;
	};

	$.fn.getTimestamp=function (){
		return Math.round((new Date()).getTime() / 1000);
	};

	$.fn.decrypt=function (EncryptedData,Key){
		try{
			var result =this.decoder(CryptoJS.AES.decrypt(EncryptedData, Key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		}
		catch(err){
			var result=EncryptedData;
		}
		console.log(result);
		return result;
	};  

	$.fn.makeRequest=function (destination,data,callback,dataType,method){
			if ((typeof dataType == "undefined") || dataType=='') dataType = 'json'
			if (typeof method == "undefined") method='POST'
			$.ajax({
		  			type: method,
		  			url: destination,
		  			data: data,
		  			success: callback,
		  			dataType: dataType
				});
	}

	$.fn.execute=function(){
		var result=new Object();

		this.makeRequest(this.getAS(),
				{
						'user_id' : this.getUserId(),
						'requested_service' : this.getRequestedService(),
						'ip' : this.getIp(),
						'requested_lifetime' : this.getRequestedLifetime(),
						'type':'web'
				},function(response){result['source']=response;});
		console.log(result);
		var TGT=result.source.TGT;
		var TGT_client=result.source.TGT_client;
		var TGS_client_key=CryptoJS.SHA256('12548442').toString(CryptoJS.enc.Hex);
		var TGT_client_decrypted =this.decrypt(TGT_client,TGS_client_key);
		console.log(TGT_client_decrypted);
		TGS_session_key=TGT_client_decrypted.TGS_session_key;
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		var AUTH=CryptoJS.AES.encrypt("{user_id:"+this.getUserId()+",timestamp:"+this.getTimestamp()+"}", TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		var AUTH_Dec=this.decoder(CryptoJS.AES.decrypt(AUTH, TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		console.log(AUTH_Dec);
		result=new Object();
		this.makeRequest(this.getTGS(),
				{
						'requested_http_service' : this.getRequestedHttpService(),
						'requested_service' : this.getRequestedService(),
						'auth' : encodeURI(AUTH),
						'tgt':encodeURI(TGT),
						'type':'web'
				},function(response){result['source']=response;});
		console.log(result.source);
		HTTP_service_ticket=result.source.HTTP_service_ticket;
		HTTP_session_ticket=result.source.HTTP_session_ticket;
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		HTTP_session_ticket_decrypted=this.decoder(CryptoJS.AES.decrypt(HTTP_session_ticket, TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		HTTP_service_session_key=HTTP_session_ticket_decrypted.HTTP_service_session_key;

		var AUTH=CryptoJS.AES.encrypt("{user_id:"+this.getUserId()+",timestamp:"+this.getTimestamp()+"}", HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		result=new Object();
		this.makeRequest("/httpservice/authenticate",
				{
					'auth':encodeURI(AUTH),
					'http_service_ticket':encodeURI(HTTP_service_ticket),
					'type':'web'
				},function(response){result['source']=response;},"text");
		httpservice=result.source;
		httpservice_response_decrypted=this.decoder(CryptoJS.AES.decrypt(httpservice, HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		console.log(httpservice_response_decrypted);
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		return {status:true,HTTP_service_session_key:HTTP_service_session_key,HTTP_session_ticket:HTTP_session_ticket};

	}



 
}( jQuery ));