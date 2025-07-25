<?php
include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (!Session::haveRight("computer", UPDATE)) {
    echo json_encode([
        'success' => false,
        'message' => 'Permessi insufficienti'
    ]);
    exit;
}

// Verifica ID computer
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die(json_encode([
        'success' => false,
        'message' => __('ID computer non valido', 'aggiornainventario')
    ]));
}
$type =null;
$elem= null;


$type = $_POST['type'];


if (isset($_POST['type'])){
	if ($type == "Computer"){
		$elem = new Computer();
	} else if ($type == "Monitor"){
		$elem = new Monitor();
	}  else if ($type == "Printer"){
		$elem = new Printer();
	} else {

		die(json_encode([
			'success' => false,
			'message' => __('Elemento non riconosciuto: ' . $type, 'aggiornainventario')
		]));
	}
}
if (!$elem->getFromDB($_POST['id'])) {
    die(json_encode([
        'success' => false,
        'message' => __('Dispositivo non trovato', 'aggiornainventario')
    ]));
}
try {
	//mi tengo da parte il vecchio valore solo per segnarlo nei log 
	$iterator = $DB->request([
		'SELECT' => 'inventory_date',
		'FROM'   => 'glpi_infocoms',
		'WHERE'  => [
			'items_id' => $_POST['id'],
			'itemtype' => $type
		],
		'LIMIT'  => 1 // opzionale ma esplicito: chiedi solo una riga
	]);
	
	
        if ($result = $iterator->current()) {
            $oldDate = $result['inventory_date'];
        } else {
		// nessun risultato trovato
		$oldDate = null;
	}

        // Esegui l'aggiornamento
	    $newDate = date('Y-m-d H:i:s');
        $res = $DB->update(
	    'glpi_infocoms', [
        'inventory_date'  => $newDate
		], [
        'WHERE'  => ['items_id' => $_POST['id'] , 'itemtype' => $type],
        'LIMIT'  => 1
        ]
);
	
	
	$changes = [
		"125", //125 corrisponde al codice identificativo 
		$oldDate, //questo è il vecchio valore
		$newDate
		];
    if ($res) {
        // Registra nel log
        Log::history(
            $elem->getID(),
            $type,
            $changes
			);
        
        echo json_encode([
            'success' => true,
            'message' => __('Data inventario aggiornata con successo', 'aggiornainventario'),
            'new_date' => Html::convDateTime($newDate)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => __('Errore durante l\'aggiornamento del database', 'aggiornainventario')
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => __('Errore: ', 'aggiornainventario') . $e->getMessage()
    ]);
}