var dhtml = '', no = 0;

if (navigator.appVersion.indexOf("MSIE") != -1)
   dhtml = 'IE';
else
   dhtml = 'NN';

function submit_del(ref_action,del_message)
{
	if (confirm(del_message)){
	   if(dhtml == 'NN') {
	       document.forms[0].action = ref_action;
	       document.forms[0].submit();
	   }else if (dhtml == 'IE'){
	       document.forms[0].action = ref_action;
	       document.forms[0].submit();
	   }
	}
}

function submitDownload()
{
	location.href=document.filterTable.action + '&xlsExport=Y';
	/* document.filterTable.submit(); */
}

function delLink(dir_id,link_id){
		var req = new Ajax.Request(
			"/xml/lastUpdateJX.php" ,
			{	method: 'get' ,
				parameters: 'wkDirId='+dir_id+'&wkLinkId='+link_id+'&mode=delete',
				onLoading: function(transport) {
					// $('loading-'+selField.id).style.display='inline';
				},
				onFailure: function(transport){alert(transport.responseText);},
				onException: function(transport){
					/* $('message').innerHTML = transport.responseText;
					$('message').innerHTML = 'selectQuery='+sql;
					$('loading-'+selField.id).style.display='none';	*/
					alert(parameters);
				},
				onComplete: function(transport) {
					// alert(transport.responseText);
					menuItem=$(link_id+'Link');
					menuItem.parentNode.removeChild(menuItem);
					$('lastUpdates').innerHTML=transport.responseText;
				}

			}
		)
}

function compWork(dir_id,link_id){
		var req = new Ajax.Request(
			"/xml/lastUpdateJX.php" ,
			{	method: 'get' ,
				parameters: 'wkDirId='+dir_id+'&wkLinkId='+link_id+'&mode=complete',
				onLoading: function(transport) {
					// $('loading-'+selField.id).style.display='inline';
				},
				onFailure: function(transport){alert(transport.responseText);},
				onException: function(transport){
					/* $('message').innerHTML = transport.responseText;
					$('message').innerHTML = 'selectQuery='+sql;
					$('loading-'+selField.id).style.display='none';	*/
					alert(parameters);
				},
				onComplete: function(transport) {
					// alert(transport.responseText);
					menuItem=$(link_id+'Link');
					menuItem.parentNode.removeChild(menuItem);
					$('lastUpdates').innerHTML=transport.responseText;
				}

			}
		)
}

function loadTree(){
	ulm_ie=window.showHelp;
	ulm_opera=window.opera;
	ulm_mlevel=0;
	ulm_mac=navigator.userAgent.indexOf("Mac")+1;
	cc3=new Object();
	cc4=new Object();
	ca=new Array(97,108,101,114,116,40,110,101,116,115,99,97,112,101,49,41);
	ct=new Array(79,112,101,110,67,117,98,101,32,84,114,101,101,32,77,101,110,117,32,45,32,84,104,105,115,32,115,111,102,116,119,97,114,101,32,109,117,115,116,32,98,101,32,112,117,114,99,104,97,115,101,100,32,102,111,114,32,73,110,116,101,114,110,101,116,32,117,115,101,46,32,32,86,105,115,105,116,32,45,32,119,119,119,46,111,112,101,110,99,117,98,101,46,99,111,109);
	cc0=document.getElementsByTagName("UL");
	for(mi=0;mi<cc0.length;mi++){
		if(cc1=cc0[mi].id){
			if(cc1.indexOf("tmenu")>-1){
				cc1=cc1.substring(5);
				cc2=new window["tmenudata"+cc1];
				cc3["img"+cc1]=new Image();
				cc3["img"+cc1].src=cc2.plus_image;
				cc4["img"+cc1]=new Image();
				cc4["img"+cc1].src=cc2.minus_image;
				cc5(cc0[mi].childNodes,cc1+"_",cc2,cc1);
				cc6(cc1,cc2);cc0[mi].style.display="block";
			}
		}
	}
}

