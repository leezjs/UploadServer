<?php

require_once('entry.php');

StartAPP();

function StartAPP() {
    $action = cleanInput($_REQUEST['action']);
    if (empty($action)) {
        $action = "main";
    }

    $requests = array_merge($_GET, $_POST);
    try {
        $module = new $action($requests);
        $module->run();
    } catch (Exception $e) {
        var_dump($e->getMessage(), $action);
        echo 'action does not support';
    }
}
