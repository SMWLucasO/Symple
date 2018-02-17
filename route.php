<?php

require_once 'mvc/Route.php';

\Symple\mvc\Route::get('users/account/{id}/action/{type}', function($id, $type) {
    echo $id . " $type";
});


\Symple\mvc\Route::get('users/account/', function() {
    echo 'abc';
});