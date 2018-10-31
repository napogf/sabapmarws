
function protocollaMail( mailId,mid){	
	formDlg = dijit.byId('dlgProtocollaMail');
	formDlg.href = 'getDialog.php?mailId='+mailId+'&dialog=protocollaMail&mid='+mid;
	formDlg.refresh();
	formDlg.show();
}

function assegnaMail( mailId,mid){	
	formDlg = dijit.byId('dlgAssegnaMail');
	formDlg.href = 'getDialog.php?mailId='+mailId+'&dialog=assegnaMail&mid='+mid;
	formDlg.refresh();
	formDlg.show();
}

function allegaMail( mailId,mid){	
	formDlg = dijit.byId('dlgAllegaMail');
	formDlg.href = 'getDialog.php?mailId='+mailId+'&dialog=allegaMail&mid='+mid;
	formDlg.refresh();
	formDlg.show();
}

function pecProtocollazione(){
		dataregistrazione = dojo.byId('DATAREGISTRAZIONE').value;
		formDlg = dijit.byId('dlgProtocollaMail');
		if(!formDlg.isValid()){
			alert('Compila i campi obbligatori!');
			return false;
		}
		data = formDlg.attr('value');	
		dojo.xhrGet({
		    url: "djProtocollaMail.php?MAIL_ID="+data.mailId+"&NUMEROREGISTRAZIONE="
		    										+data.NUMEROREGISTRAZIONE+'&DATAREGISTRAZIONE='
		    										+dataregistrazione
		    										+'&MID='+data.mid,
		    load: function(response, ioArgs){
				if (response != 'protocollata'){
					alert(response);
				} else {
					formDlg.reset();
					formDlg.hide();					
					dijit.byId('pecWorkspace').refresh();					
				}

				//Dojo recommends that you always return(response); to propagate
		      //the response to other callback handlers. Otherwise, the error
		      //callbacks may be called in the success case.
		      return response;
		    },
		    error: function(response, ioArgs){
		      alert('errore:'+response+'ioArgs:'+ioArgs);
		      return response;
		    },
		    handleAs: "text"
		  });
		
		return;
}

function pecAssegnaUo(){
	formDlg = dijit.byId('dlgAssegnaMail');	
	if(!formDlg.isValid()){
		alert('Compila i campi obbligatori!');
		return false;
	}
	data = formDlg.attr('value');	
	dojo.xhrGet({
	    url: "djProtocollaMail.php?MAIL_ID="+data.mailId+"&ZONA="+data.ZONA+"&UFFICIO="+data.UFFICIO+'&MID='+data.mid,
	    load: function(response, ioArgs){
			if (response != 'protocollata'){
				alert(response);
			} else {
				formDlg.reset();
				formDlg.hide();					
				dijit.byId('pecWorkspace').refresh();					
			}

			//Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      console.log(ioArgs);
	      alert('errore:'+response+'ioArgs:'+ioArgs);
	      return response;
	    },
	    handleAs: "text"
	  });
	
	return;
}

function pecAttachment(){
	formDlg = dijit.byId('dlgAllegaMail');	
	if(!formDlg.isValid()){
		alert('Compila i campi obbligatori!');
		return false;
	}
	data = formDlg.attr('value');	
	dojo.xhrGet({
	    url: "djProtocollaMail.php?PRATICA_ID="+data.PRATICA_ID+'&MID='+data.mid+'&MAIL_ID='+data.mailId,
	    load: function(response, ioArgs){
			if (response != 'protocollata'){
				alert(response);
			} else {
				formDlg.reset();
				formDlg.hide();					
				dijit.byId('pecWorkspace').refresh();					
			}
	      return response;
	    },
	    error: function(response, ioArgs){
	      console.log(ioArgs);
	      alert('errore:'+response+'ioArgs:'+ioArgs);
	      return response;
	    },
	    handleAs: "text"
	  });
	
	return;
}
