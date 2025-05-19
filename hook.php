<?php

function plugin_aggiornainventario_add_button($params) {
	
	
    if (in_array($params['item']->getType(),  ['Computer', 'Monitor'], true)) {
        echo "<button type='button' class='vsubmit' name='aggiorna_inventario' 
              onclick='aggiornaInventario()' style='margin-left: 10px;'>
              Aggiorna data ultimo inventario a oggi</button>";
        
        echo "<script>

			
			function aggiornaInventario() {
    if (confirm('Aggiornare la data inventario?')) {
        $.ajax({
            url: '/glpi/plugins/aggiornainventario/ajax/aggiorna.php',
            type: 'POST',
			dataType: 'json',
            data: {
                id: '".$params['item']->getID()."',
				type: '" . $params['item']->getType() ."',
                glpi_csrf_token: '".Session::getNewCSRFToken()."'
            },
            success: function(response) {
                if (response.success) {
					alert(response.message);
                    location.reload(); // Ricarica la pagina per vedere le modifiche
                } else {
                    alert('Errore: ' + response.message);
                }
            }
        });
    }
}
        </script>";
    }
	
}
