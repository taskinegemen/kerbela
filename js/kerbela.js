(function ( $ ) {

	var ConfigServer;
    var AuthenticationServer;
 	var TicketGrantingServer;
 	var KerberizedServer;
 	var UserId;
 	var Password;
 	var RequestedService;
 	var RequestedHttpService;
 	var Ip;
 	var RequestedLifetime;

 	$.fn.kerbelainit=function(CS,AS,TGS,KS,UI,PWD,RS,RHS,RL){
 		$.ajaxSetup({async:false});
 		this.setCS(CS);
 		this.setAS(AS);
 		this.setTGS(TGS);
 		this.setKS(KS);
 		this.setUserId(UI);
 		this.setPassword(PWD);
 		this.setRequestedService(RS);
 		this.setRequestedHttpService(RHS);
 		this.setRequestedLifetime(RL);
 		this.setIp();
 		return this;
 	};

 	$.fn.setCS=function(CS){this.ConfigServer=CS;};
 	$.fn.getCS=function(){return this.ConfigServer;};
 	
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

 	$.fn.setPassword=function(PWD){this.Password=PWD;};
 	$.fn.getPassword=function(){return this.Password;};

 	$.fn.setIp=function(){
 		var that=this;
 		var result=new Object();
		this.makeRequest(this.getCS()+'/api/getip',
				'',function(response){that.Ip=response.ip;},'','GET');
		

 	};
 	$.fn.getIp=function(){return this.Ip;}

 	$.fn.setRequestedLifetime=function(RL){this.RequestedLifetime=RL;};
 	$.fn.getRequestedLifetime=function(){return this.RequestedLifetime;};

 	$.fn.setTicket=function(HTTP_service_session_key,HTTP_service_ticket){window.sessionStorage.ticket=JSON.stringify({HTTP_service_session_key:HTTP_service_session_key,HTTP_service_ticket:HTTP_service_ticket});}
 	$.fn.getTicket=function(){return JSON.parse(window.sessionStorage.ticket);};

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

	$.fn.decrypt=function (EncryptedData,Key,Error){
		var result;
		try{
			result =this.decoder(CryptoJS.AES.decrypt(EncryptedData, Key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		}
		catch(err){
			/*
			console.log(Error);
			if(Error.status==undefined){
				result={status:false,message:'User password is incorrect!'};
			}
			else
			{
			result=Error; 
			}*/
			result=Error;
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
	$.fn.getAuthTicket=function(){
		var ticket=this.getTicket();
		var HTTP_service_session_key=ticket.HTTP_service_session_key;
		console.log(HTTP_service_session_key);
		return CryptoJS.AES.encrypt("{user_id:"+this.getUserId()+",timestamp:"+this.getTimestamp()+"}", HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
	}
	$.fn.getSource=function(destination,data){
		console.log(this.getTicket());
		var ticket=this.getTicket();
		var HTTP_service_session_key=ticket.HTTP_service_session_key;
		var HTTP_service_ticket=ticket.HTTP_service_ticket;
		
		var AUTH=CryptoJS.AES.encrypt("{user_id:"+this.getUserId()+",timestamp:"+this.getTimestamp()+"}", HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		var result=new Object();
		var postData={
					'auth':encodeURI(AUTH),
					'http_service_ticket':encodeURI(HTTP_service_ticket),
					'type':'web'
				};
		$.extend(postData,data);		
		this.makeRequest(destination,postData,function(response){result['source']=response;},"text");
		httpservice=result.source;
		httpservice_response_decrypted=this.decrypt(httpservice,HTTP_service_session_key,result.source);
		if(httpservice_response_decrypted.status==false){return httpservice_response_decrypted;}
		console.log(httpservice_response_decrypted);
		return httpservice_response_decrypted;
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
		var TGT=result.source.TGT;
		var TGT_client=result.source.TGT_client;
		var TGS_client_key=CryptoJS.SHA256(this.getPassword()).toString(CryptoJS.enc.Hex);
		var TGT_client_decrypted =this.decrypt(TGT_client,TGS_client_key,result.source);
		if(TGT_client_decrypted.status==false){return TGT_client_decrypted;}
		console.log(TGT_client_decrypted);
		TGS_session_key=TGT_client_decrypted.TGS_session_key;
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		var AUTH=CryptoJS.AES.encrypt("{user_id:"+this.getUserId()+",timestamp:"+this.getTimestamp()+"}", TGS_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		result=new Object();
		this.makeRequest(this.getTGS(),
				{
						'requested_http_service' : this.getRequestedHttpService(),
						'requested_service' : this.getRequestedService(),
						'auth' : encodeURI(AUTH),
						'tgt':encodeURI(TGT),
						'type':'web'
				},function(response){result['source']=response;});
		HTTP_service_ticket=result.source.HTTP_service_ticket;
		HTTP_session_ticket=result.source.HTTP_session_ticket;
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		HTTP_session_ticket_decrypted=this.decrypt(HTTP_session_ticket,TGS_session_key,result.source);
		console.log(HTTP_session_ticket_decrypted.status);
		if(HTTP_session_ticket_decrypted.status==false){return HTTP_session_ticket_decrypted;}
		console.log(HTTP_session_ticket_decrypted);
		HTTP_service_session_key=HTTP_session_ticket_decrypted.HTTP_service_session_key;

		var AUTH=CryptoJS.AES.encrypt("{user_id:"+this.getUserId()+",timestamp:"+this.getTimestamp()+"}", HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.base64);
		result=new Object();
		this.makeRequest(this.getKS(),
				{
					'auth':encodeURI(AUTH),
					'http_service_ticket':encodeURI(HTTP_service_ticket),
					'type':'web'
				},function(response){result['source']=response;},"text");
		httpservice=result.source;
		httpservice_response_decrypted=this.decrypt(httpservice,HTTP_service_session_key,result.source);
		if(httpservice_response_decrypted.status==false){return httpservice_response_decrypted;}
		//this.decoder(CryptoJS.AES.decrypt(httpservice, HTTP_service_session_key,{mode:CryptoJS.mode.CBC}).toString(CryptoJS.enc.Utf8));
		console.log(httpservice_response_decrypted);

		this.setTicket(HTTP_service_session_key,HTTP_service_ticket);

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		return {status:true,message:'successfully connected to '+this.getKS()};

	}



 
}( jQuery ));