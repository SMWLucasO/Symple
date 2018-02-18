<?php

require_once 'route/Route.php';

\Symple\route\Route::get('users/account/{id}/action/{type}', function($id, $type) {
    echo $id . " $type";
});


\Symple\route\Route::get('users/account/', function() {
    echo 'abc';
});