 <?php

/**
 * All the important data is to be stored here, the config folder needs to be PRIVATE
 * inaccessible for users unless they have this specific class.
 */
return [
    "host" => "localhost",
    "dbname" => "symple",
    "dbuser" => "root",
    "dbpass" => "",
    "PDO_ERROR_MODE" => \PDO::ERRMODE_EXCEPTION,
    "defined_entities" => []
];