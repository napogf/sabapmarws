<?php
/*
 * Created on 10/giu/10 To change the template for this generated file go to Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";
require_once ("dbfunctions.php");
// ini_set('error_reporting', E_ALL);
$db = Db_Pdo::getInstance();
try {
    if (strpos($_SERVER['HTTP_HOST'] ,'.localnet')) {
        $ultimoProtocollo = str_pad(((integer) $db->query('
        	    SELECT max(numeroregistrazione) FROM pratiche
            WHERE numeroregistrazione NOT REGEXP "-" AND dataregistrazione >= DATE_FORMAT(NOW() ,"%Y-01-01")'
            )->fetchColumn() + 1), 7, '0', STR_PAD_LEFT);
        $db->query('update pratiche set numeroregistrazione = :numeroregistrazione,
                                                          dataregistrazione = now(),
                                                          uscita = now()
                                        where pratica_id = :pratica_id', array(
            ':numeroregistrazione' => $ultimoProtocollo,
            ':pratica_id' => $_POST['ws_pratica_id']
        ));

        $response = array(
            'status' => 'success',
            'message' => 'Creato il protocollo: ' . $ultimoProtocollo . ' - ' . Date('d-m-Y')
        );
    } else {
        function wsHtmlChars(&$item, $key)
        {
            $item = htmlspecialchars($item);
        }
        // array_walk_recursive($_POST, 'wsHtmlChars');
        $espiWs = new EspiWS();

        // Fascicolo
        $espiWs->setFascicolo($_POST[classifica2] , $_POST['fascicolo']);
        // Titolario
        if(isSet($_POST['classifica'])){
            $titolario = $db->query('SELECT
                    classificazione as ClasseTitolario, description as DesTitolario
                FROM arc_modelli WHERE modello = :modello', [
                ':modello' => $_POST['classifica']
            ])->fetch();
            $espiWs->setTitolario($titolario);
        } else {
            $espiWs->setTitolario(array(
                'ClasseTitolario' => $_POST['ClasseTitolario'],
                'DesTitolario' => $_POST['DesTitolario']
            ));
        }

        $espiWs->setTestataDocumento($_POST['clsTestataDocumento']);
        $mittenti = array();
        foreach ($_POST['clsTMittenteDestinatario'] as $mittente) {
            if (! isSet($mittente['escludi'])) {
                $mittenti[] = $mittente;
            }
        }

        $espiWs->setCodiceUfficioCompetente($_POST['CodUfficioCompetente']);

        $espiWs->setMittenteDestinatario($mittenti);

        $espiWs->protocollaDocumento();

        $wsResult = $espiWs->getWsresult();
        $espiWs->logConnection($_POST['ws_pratica_id']);
        if ($wsResult->CodError != 'OK') {

            throw new Exception('Errore nella protocollazione in uscita: ' . $wsResult->DesError);
        } else {
            // $espiWs->protocollaPraticaUscita($_POST['ws_pratica_id']);
            $response = array(
                'status' => 'success',
                'message' => 'Creato il protocollo: ' . $wsResult->NumProtocollo . ' - ' . $wsResult->AnnoProtocollo
            );
        }
        $db->query('UPDATE pratiche set numeroregistrazione = :numeroregistrazione,
                                        dataregistrazione = :dataregistrazione,
                                        oggetto = :oggetto,
                                        uscita = now()
                 WHERE pratica_id = :pratica_id',array(
                                                    ':pratica_id' => $_POST['ws_pratica_id'],
                                                    ':numeroregistrazione' => isset($espiWs) ? $wsResult->NumProtocollo : $ultimoProtocollo,
                                                    ':dataregistrazione' => date('Y-m-d'),
                                                    ':oggetto' => $_POST['clsTestataDocumento']['Oggetto'],
                                                ));
        $db->query('update arc_sospensioni set inizio = now() where protouscita = :protouscita',[':protouscita' => $_POST['ws_pratica_id']]);
    }

} catch (Exception $e) {

    $response = array(
        'status' => 'error',
        'message' => $e->getMessage(),
        'lastRequest' => isset($espiWs) ? $espiWs->getLastrequest() : '',
        'lastResponse' => isset($espiWs) ? $espiWs->getLastresponse() : '',
    );

}
echo json_encode($response);
exit();
