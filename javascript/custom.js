
function dialogIntegrazioni(){
	dlg='dlgAddIntegrazioni';
	formDlg = dijit.byId(dlg);
	formDlg.href = 'djDlgIntegrazioni.php' ;
	formDlg.refresh();
	formDlg.show();
}

function addIntegrazioni(value) {
	data=dojo.byId('INT_'+value);
	skPaeIntegrazioni=dojo.byId('PAE_INTEGRAZIONI');
	skPaeIntegrazioni.value = skPaeIntegrazioni.value+data.childNodes[0].data;
	    dojo.attr('INT_'+value, "style", {
            background: "#eee"
        });

}

