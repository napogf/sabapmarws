<?php
/**
 *
 *
 * @version $Id: praticaRiattivazione.php,v 1.1.1.1 2011-08-29 15:30:35 pratiche Exp $
 * @copyright 2003
 **/
include "login/autentication.php";
if(isSet($_POST) and $_POST['riattivaPratica'] > ''){
    try {
        $db = Db_Pdo::getInstance();
        $db->beginTransaction();
        // Se ha un fascicolo lo cancello
        $db->query('delete from pratiche_fascicoli where pratica_id = :pratica_id',[':pratica_id' => $_GET['PRATICA_ID']]);
        // mi aggancio al fascicolo
        $sospensione = $db->query('SELECT * FROM arc_sospensioni WHERE sospensione_id = :id',[ ':id' => $_POST['riattivaPratica'] ])->fetch();
        $fascicoloId = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
	       ':pratica_id' => $sospensione['PRATICA_ID'],
        ])->fetchColumn();
        // Riattivo la pratica
        $db->query('insert into pratiche_fascicoli (pratica_id, fascicolo_id, tipologia, funzione)
                                            values (:pratica_id, :fascicolo_id, "E" ,"fine_sospensione")',[
                ':pratica_id' => $_GET['PRATICA_ID'],
                ':fascicolo_id' => $fascicoloId,
                ]);

        $db->query('update arc_sospensioni set protoentrata = :protoentrata , fine = now() where sospensione_id = :id',[
                ':protoentrata' => $_GET['PRATICA_ID'],
                ':id' => $_POST['riattivaPratica']
            ]);
        $db->query('update pratiche set uscita = now() WHERE
                    pratica_id = (SELECT protouscita
                        FROM arc_sospensioni
                        WHERE arc_sospensioni.sospensione_id = :id)
                    OR pratica_id = (SELECT protoentrata
                            FROM arc_sospensioni
                            WHERE arc_sospensioni.sospensione_id = :id)',[
                ':id' => $_POST['riattivaPratica']
            ]);
        $db->commit();
        header('Location: /editPratica.php?PRATICA_ID=' . $sospensione['PRATICA_ID']);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $message = $e->getMessage();
    }
}


include("pageheader.inc");

if(isSet($message)){
    print('<h2>'.$message.'</h2>');
}
print('<FORM id="riattivaPraticaForm" encType="multipart/form-data" action="?PRATICA_ID=' . $_GET['PRATICA_ID'] . '" method="POST">');

print('<table class="altriDestinatari">');
print('<tr><td><button dojoType="dijit.form.Button" type="submit" id="RicercaSospensioneButton">Riattiva Pratica</button></td><td>');
print('<div dojoType="dojo.data.ItemFileReadStore" ' .
    'url="xml/jsonRicercaSospensione.php" ' .
    'jsId="ricercaSospensione" ' .
    '></div>');

print('<div dojoType="dijit.form.FilteringSelect"  ' .
    'store="ricercaSospensione"
	searchAttr="DESCRIPTION" ' .
//    ' pageSize="50" ' .
    ' autoComplete="false" ' .
    'name="riattivaPratica" ' .
    ' queryExpr= "*${0}*", '.
    'id="riattivaPratica" ' .
    ' value="" ' .
    ' style="width:600px;" '.
    '></div>');

print('</td></tr></table>');
print('</FORM>');
include("pagefooter.inc");
?>