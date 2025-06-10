<?php
function plugin_version_aggiornainventario() {
    return [
        'name'           => 'Aggiorna Inventario',
        'version'        => '1.0.0',
        'author'         => 'Il tuo Nome',
        'license'       => 'GPLv2+',
        'homepage'      => '',
        'minGlpiVersion' => '9.5'
    ];
}

function plugin_init_aggiornainventario() {
    global $PLUGIN_HOOKS;
	$PLUGIN_HOOKS['csrf_compliant']['aggiornainventario'] = true;

    $PLUGIN_HOOKS['pre_item_form']['aggiornainventario'] = 'plugin_aggiornainventario_add_button';
    
}

function plugin_aggiornainventario_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '9.5', '>=')) {
        return true;
    } else {
        echo "Richiesta la versione GLPI 9.5 o superiore";
        return false;
    }
}

function plugin_aggiornainventario_check_config() {
    return true;
}

function plugin_aggiornainventario_install() {
    return true;
}

