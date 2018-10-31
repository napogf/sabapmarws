function dialogNote(id){
	dlg='dlgAddNote_'+id;
	formDlg = dijit.byId(dlg);
	formDlg.href = 'Sys_djDlgNote.php?id='+id ;
	formDlg.refresh();
	formDlg.show();
}
function loadCpaneNote(id) {
	//console.log(dijit.byId('SEL_NOTE_'+id).get("value"));
	//console.log(dijit.byId('SEL_NOTE_'+id).item.ID[0]);
	dijit.byId('cPaneNote_'+id).href= 'Sys_djGetNote.php?TIPO='+dijit.byId('SEL_NOTE_'+id).item.ID[0]+'&id='+id ;
    dijit.byId('cPaneNote_'+id).refresh();	
}
function addNote(value,id) {
	data=dojo.byId('INT_'+value);
	skNote=dojo.byId(id);
//	console.log(skNote);
//	console.log(skNote.value);
	skNote.value = skNote.value+data.childNodes[0].data;
	    dojo.attr('INT_'+value, "style", {
            background: "#eee"
        });
}
