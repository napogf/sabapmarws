var periziaId=0;
var qeconomicoId=0;
var contrattoId=0;
var staffId=0;
var economie=false;

function setRowClass(id){
	/* setRowClassQe(0);
	setRowClassSt(0); */
		if(periziaId>0) dojo.attr('riga_perizia_'+periziaId, "style", {
	           background: ""
	       });
	periziaId=id;
	if(periziaId > 0){
		dojo.attr('riga_perizia_'+periziaId, "style", {
	           background: "#eee"
	       });
	}
}

function setRowClassQe(id,rigaE){
	if(qeconomicoId>0){
		if(economie){
			dojo.attr('riga_qe_'+qeconomicoId, "style", {
		           background: "#90EE90"
		       });
		} else {
			dojo.attr('riga_qe_'+qeconomicoId, "style", {
		           background: ""
		       });
		}
	}
	if(rigaE){
		economie=true;
	} else {
		economie=false;
	}
	qeconomicoId=id;
	if(qeconomicoId > 0){
		dojo.attr('riga_qe_'+qeconomicoId, "style", {
	           background: "#eee"
	       });
	}
	setRowClassCo(0);
}
function setRowClassCo(id){
	if(contrattoId>0) dojo.attr('riga_co_'+contrattoId, "style", {
           background: ""
       });
	contrattoId=id;
	if(contrattoId > 0){
		dojo.attr('riga_co_'+contrattoId, "style", {
	           background: "#eee"
	       });
	}
}
function setRowClassSt(id){
		if(staffId>0) dojo.attr('riga_st_'+staffId, "style", {
	           background: ""
	       });
	staffId=id;
	if(staffId > 0){
		dojo.attr('riga_st_'+staffId, "style", {
	           background: "#eee"
	       });
	}
}
function loadQeconomico(id){
	dijit.byId('CONTRATTI').href='lav_contratti.php';
	dijit.byId('CONTRATTI').refresh();
	setRowClass(id);
	setRowClassQe(0,false);
	setRowClassCo(0);
	cPane=dijit.byId('QECONOMICO');
	cPane.href='lav_qeconomico.php?PERIZIA_ID='+id;
	cPane.refresh();
}
function loadContratti(id,rigaE){
	setRowClassQe(id,rigaE);
	setRowClassCo(0);
	cPane=dijit.byId('CONTRATTI');
	cPane.href='lav_contratti.php?QECONOMICO_ID='+id;
	cPane.refresh();
}

function loadStaff(id){
	cPane=dijit.byId('STAFF');
	cPane.href='lav_staff.php?PERIZIA_ID='+id;
	cPane.refresh();
}



function resetPe(){

	dijit.byId('editPerizia').href='lav_djFormPerizie.php?mode=void';
	dijit.byId('editPerizia').refresh();
	dijit.byId('addPerizia').href='lav_djFormPerizie.php?mode=void';
	dijit.byId('addPerizia').refresh();

	dojo.attr('listaPerizie', 'style' , {
				display: "block"
				});
	dojo.attr('editPerizia', 'style' , {
				display: "none"
				});
	dojo.attr('addPerizia', 'style' , {
				display: "none"
				});
}

function resetQe(){

	dojo.attr('listaQeconomico', 'style' , {
				display: "block"
				});
	dojo.attr('editQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('addQeconomico', 'style' , {
				display: "none"
				});
}

function resetCo(){
	dojo.attr('listaContratti', 'style' , {
				display: "block"
				});
	dojo.attr('editContratti', 'style' , {
				display: "none"
				});
	dojo.attr('addContratti', 'style' , {
				display: "none"
				});
}
function resetSt(){
	dojo.attr('listaStaff', 'style' , {
				display: "block"
				});
	dojo.attr('editStaff', 'style' , {
				display: "none"
				});
	dojo.attr('addStaff', 'style' , {
				display: "none"
				});
}

function addPerizia(){
	dojo.attr('listaPerizie', 'style' , {
				display: "none"
				});
	dojo.attr('editPerizia', 'style' , {
				display: "none"
				});
	dojo.attr('addPerizia', 'style' , {
				display: "block"
				});

	dijit.byId('editPerizia').href='lav_djFormPerizie.php?mode=void';
	dijit.byId('editPerizia').refresh();
	dijit.byId('addPerizia').href='lav_djFormPerizie.php?mode=insert';
	dijit.byId('addPerizia').refresh();
}

function addQeconomico(id){
	dijit.byId('editQeconomico').href='lav_djFormQeconomico.php?mode=void';
	dijit.byId('editQeconomico').refresh();
	dijit.byId('addQeconomico').href='lav_djFormQeconomico.php?mode=insert&PERIZIA_ID='+id;
	dijit.byId('addQeconomico').refresh();

	dojo.attr('listaQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('editQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('addQeconomico', 'style' , {
				display: "block"
				});
	dijit.byId('addQeconomico').refresh();


}
function addContratti(id){
	dijit.byId('editContratti').href='lav_djFormContratti.php?mode=void';
	dijit.byId('editContratti').refresh();
	dijit.byId('addContratti').href='lav_djFormContratti.php?mode=insert&QECONOMICO_ID='+id;
	dijit.byId('addContratti').refresh();
	dojo.attr('listaContratti', 'style' , {
				display: "none"
				});
	dojo.attr('editContratti', 'style' , {
				display: "none"
				});
	dojo.attr('addContratti', 'style' , {
				display: "block"
				});
	dijit.byId('addContratti').refresh();
}
function addStaff(id){
	dijit.byId('editStaff').href='lav_djFormStaff.php?mode=void';
	dijit.byId('editStaff').refresh();
	dijit.byId('addStaff').href='lav_djFormStaff.php?mode=insert&PERIZIA_ID='+id;
	dijit.byId('addStaff').refresh();
	dojo.attr('listaStaff', 'style' , {
				display: "none"
				});
	dojo.attr('editStaff', 'style' , {
				display: "none"
				});
	dojo.attr('addStaff', 'style' , {
				display: "block"
				});
	dijit.byId('addStaff').refresh();
}

function editPerizia(id){
	dojo.attr('listaPerizie', 'style' , {
				display: "none"
				});
	dojo.attr('addPerizia', 'style' , {
				display: "none"
				});
	dojo.attr('editPerizia', 'style' , {
				display: "block"
				});
	dijit.byId('addPerizia').href='lav_djFormPerizie.php?mode=void';
	dijit.byId('addPerizia').refresh();

	dijit.byId('editPerizia').href='lav_djFormPerizie.php?mode=modify&PERIZIA_ID='+id;
	dijit.byId('editPerizia').refresh();

}
function editQeconomico(id){
	dojo.attr('listaQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('addQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('editQeconomico', 'style' , {
				display: "block"
				});
	dijit.byId('addQeconomico').href='lav_djFormQeconomico.php?mode=void';
	dijit.byId('addQeconomico').refresh();
	dijit.byId('editQeconomico').href='lav_djFormQeconomico.php?QECONOMICO_ID='+id;
	dijit.byId('editQeconomico').refresh();

}
function editContratti(id){
	dojo.attr('listaContratti', 'style' , {
				display: "none"
				});
	dojo.attr('addContratti', 'style' , {
				display: "none"
				});
	dojo.attr('editContratti', 'style' , {
				display: "block"
				});
	dijit.byId('addContratti').href='lav_djFormContratti.php?mode=void';
	dijit.byId('addContratti').refresh();
	dijit.byId('editContratti').href='lav_djFormContratti.php?CONTRATTO_ID='+id;
	dijit.byId('editContratti').refresh();

}
function editStaff(id){
	dojo.attr('listaStaff', 'style' , {
				display: "none"
				});
	dojo.attr('addStaff', 'style' , {
				display: "none"
				});
	dojo.attr('editStaff', 'style' , {
				display: "block"
				});
	dijit.byId('addStaff').href='lav_djFormStaff.php?mode=void';
	dijit.byId('addStaff').refresh();
	dijit.byId('editStaff').href='lav_djFormStaff.php?STAFF_ID='+id;
	dijit.byId('editStaff').refresh();

}


function insForm(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormPerizie.php?aggiorna=insert&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
			dojo.attr('addPerizia', 'style' , {
						display: "none"
						});
			dojo.attr('editPerizia', 'style' , {
						display: "none"
						});
			dijit.byId('listaPerizie').refresh();
			dojo.attr('listaPerizie', 'style' , {
						display: "block"
						});
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
}
function insFormQe(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormQeconomico.php?aggiorna=insert&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
		dojo.attr('addQeconomico', 'style' , {
					display: "none"
					});
		dojo.attr('editQeconomico', 'style' , {
					display: "none"
					});
		dijit.byId('listaPerizie').refresh();
		dijit.byId('listaContratti').refresh();
		dijit.byId('listaQeconomico').refresh();
		dojo.attr('listaQeconomico', 'style' , {
					display: "block"
					});
}
function insFormCo(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormContratti.php?aggiorna=insert&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
			dojo.attr('addContratti', 'style' , {
						display: "none"
						});
			dojo.attr('editContratti', 'style' , {
						display: "none"
						});
			dijit.byId('listaPerizie').refresh();
			dijit.byId('listaContratti').refresh();
			dijit.byId('listaQeconomico').refresh();
			dojo.attr('listaContratti', 'style' , {
						display: "block"
						});
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
}
function insFormSt(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormStaff.php?aggiorna=insert&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
			dojo.attr('addStaff', 'style' , {
						display: "none"
						});
			dojo.attr('editStaff', 'style' , {
						display: "none"
						});
			dijit.byId('listaStaff').refresh();
			dojo.attr('listaStaff', 'style' , {
						display: "block"
						});
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	dijit.byId('listaPerizie').refresh();
	dijit.byId('listaQeconomico').refresh();
	dijit.byId('listaContratti').refresh();
}

function modForm(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormPerizie.php?aggiorna=modify&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	dijit.byId('listaPerizie').refresh();
	dojo.attr('addPerizia', 'style' , {
				display: "none"
				});
	dojo.attr('editPerizia', 'style' , {
				display: "none"
				});
	dojo.attr('listaPerizie', 'style' , {
				display: "block"
				});
}
function modFormQe(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormQeconomico.php?aggiorna=modify&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	dijit.byId('listaPerizie').refresh();
	dijit.byId('listaQeconomico').refresh();
	dojo.attr('addQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('editQeconomico', 'style' , {
				display: "none"
				});
	dojo.attr('listaQeconomico', 'style' , {
				display: "block"
				});
}
function modFormCo(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormContratti.php?aggiorna=modify&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	dijit.byId('listaPerizie').refresh();
	dijit.byId('listaQeconomico').refresh();
	dijit.byId('listaContratti').refresh();
	dojo.attr('addContratti', 'style' , {
				display: "none"
				});
	dojo.attr('editContratti', 'style' , {
				display: "none"
				});
	dojo.attr('listaContratti', 'style' , {
				display: "block"
				});
}
function modFormSt(dbForm){
	dojo.xhrGet({
	    url: "lav_djFormStaff.php?aggiorna=modify&"+dojo.formToQuery(dojo.byId("form_"+dbForm)),
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	dijit.byId('listaStaff').refresh();
	dojo.attr('addStaff', 'style' , {
				display: "none"
				});
	dojo.attr('editStaff', 'style' , {
				display: "none"
				});
	dojo.attr('listaStaff', 'style' , {
				display: "block"
				});
	dijit.byId('listaPerizie').refresh();
	dijit.byId('listaQeconomico').refresh();
	dijit.byId('listaContratti').refresh();
}

function delPerizia(periziaId){
	if(!confirm('Vuoi cancellare la Perizia?')) return false;
	dojo.xhrGet({
	    url: "lav_djDeleteRecord.php?sqlQuery=delete from lav_perizie where perizia_id="+periziaId,
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	  dijit.byId('listaPerizie').refresh();
}

function delQeconomico(id){
	if(!confirm('Vuoi cancellare la Voce di Quadro Economico?')) return false;
	dojo.xhrGet({
	    url: "lav_djDeleteRecord.php?sqlQuery=delete from lav_quadro_economico where qeconomico_id="+id,
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	  dijit.byId('listaQeconomico').refresh();
}
function delContratto(id){
	if(!confirm('Vuoi cancellare il Contratto?')) return false;
	dojo.xhrGet({
	    url: "lav_djDeleteRecord.php?sqlQuery=delete from lav_contratti where contratto_id="+id,
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	  dijit.byId('listaContratti').refresh();
}
function delStaff(id){
	if(!confirm('Vuoi cancellare il componente lo Staff?')) return false;
	dojo.xhrGet({
	    url: "lav_djDeleteRecord.php?sqlQuery=delete from lav_staff where staff_id="+id,
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	  dijit.byId('listaStaff').refresh();
}




function liquidaContratto(contrattoId){
	formDlg = dijit.byId('dlgLiquidazione');
	formDlg.href = 'lav_dlgLiquidazioni.php?CONTRATTO_ID='+contrattoId;
	formDlg.refresh();
	formDlg.show();
}

function addLiquidazione(){
	formDlg = dijit.byId('dlgLiquidazione');
	if(!formDlg.isValid()){
		alert('Compila tutti i campi!');
		return false;
	}
	data = formDlg.attr('value');
	dojo.xhrGet({
	    url: "lav_dlgLiquidazioni.php?liquidaContratto=Y&CONTRATTO_ID="+data.CONTRATTO_ID+"&DATA_LIQUIDAZIONE="+dojo.byId('DATA_LIQUIDAZIONE').value+"&IMPORTO_LIQUIDATO="+data.IMPORTO_LIQUIDATO+"&DESCRIZIONE="+data.DESCRIZIONE,
	    load: function(response, ioArgs){
			formDlg.reset();
			formDlg.hide();

	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
}
function delLiquidazione(id){
	if(!confirm('Vuoi cancellare la liquidazione?')) return false;
	dojo.xhrGet({
	    url: "lav_djDeleteRecord.php?sqlQuery=delete from lav_liquidazioni where liquidazione_id="+id,
	    load: function(response, ioArgs){
	      //Dojo recommends that you always return(response); to propagate
	      //the response to other callback handlers. Otherwise, the error
	      //callbacks may be called in the success case.
	      return response;
	    },
	    error: function(response, ioArgs){
	      alert('error:'+response);
	      return response;
	    },
	    handleAs: "text"
	  });
	  dijit.byId('dlgLiquidazione').reset();
	  dijit.byId('dlgLiquidazione').hide();

}
function selAnnoPerizie(){
	anno=dijit.byId('SELANNO').item.VALUE[0];
	dijit.byId('listaPerizie').href='lav_perizie.php?annoPerizie='+anno;
	dijit.byId('listaPerizie').refresh();

	dijit.byId('QECONOMICO').href='lav_qeconomico.php';
	dijit.byId('QECONOMICO').refresh();
	dijit.byId('CONTRATTI').href='lav_contratti.php';
	dijit.byId('CONTRATTI').refresh();

	dijit.byId('STAFF').href='lav_staff.php';
	dijit.byId('STAFF').refresh();
	periziaId=0;
	qeconomicoId=0;
	contrattoId=0;
	staffId=0;
}

function setPerizieOrder(ordField,ordType){
	anno=dijit.byId('SELANNO').item.VALUE[0];
	if(anno>' '){
		annoFilter='&annoPerizie='+anno;
	} else {
		annoFilter='';
	}
	dijit.byId('listaPerizie').href='lav_perizie.php?ordField='+ordField+'&ordType='+ordType+annoFilter;
	dijit.byId('listaPerizie').refresh();
	dijit.byId('QECONOMICO').href='lav_qeconomico.php';
	dijit.byId('QECONOMICO').refresh();
	dijit.byId('CONTRATTI').href='lav_contratti.php';
	dijit.byId('CONTRATTI').refresh();

	dijit.byId('STAFF').href='lav_staff.php';
	dijit.byId('STAFF').refresh();
	periziaId=0;
	qeconomicoId=0;
	contrattoId=0;
	staffId=0;

}