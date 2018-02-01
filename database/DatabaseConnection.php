<?php

namespace Symple\database;

/**
 * @author Lucas Ouwens
 * @version 0.1
 */
class DatabaseConnection {


    private $connection;
    private static $instance = null;

    /**
     *  Get access to the DatabaseConnection class
     * @return DatabaseConnection
     */
    public static function getInstance() {
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a connection with the database so you can execute queries
     * @return SafePDO
     */
    public function getConnection() {

        $data = require __DIR__ . '/../config/config.php';

        if( is_null( $this->connection ) ) {
            $options = [
                \PDO::ATTR_ERRMODE => $data["PDO_ERROR_MODE"],
                \PDO::MYSQL_ATTR_FOUND_ROWS => true,
                \PDO::ATTR_EMULATE_PREPARES => false
            ];

            $this->connection = new SafePDO("mysql:host=" . $data["host"] . ";dbname=" . $data["dbname"] . ";charset=utf8", $data["dbuser"], $data["dbpass"], $options );
        }

        return $this->connection;
    }

}