<?php

namespace Symple\database;

/**
 * @author Lucas Ouwens
 * @version 0.1
 */
class SafePDO extends \PDO
{


    /**
     *  This class is a wrapper for the PDO object because of the following reason:
     *  If the php.ini file does not have a specific PDO line set, it will show the sensitive
     *  data of the database, which in no case should be allowed.
     *
     * @param $dsn string       Connection details
     * @param $username string  Database connection username
     * @param $passwd string    Database connection password
     * @param $options array    An array of PDO parameters
     */
    public function __construct($dsn, $username, $passwd, $options)
    {

        // Callback on errors, will execute the handleException method with Exception object
        //set_exception_handler( array( $this, 'handleException' ) );

        parent::__construct($dsn, $username, $passwd, $options);

    }

    /**
     *  Exception handling for PDO so that we do not get sensitive information leaked
     * (THIS IS A TEST SECURE PDO OBJECT)
     *
     * @param $exception \Exception     The exception we have received.
     */
    public function handleException($exception)
    {
        $data = require __dir__ . '/../config/config.php';

        $newMessage = $exception->getMessage();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                if (strpos($newMessage, $value) !== false) {
                    $newMessage = str_replace($value, '...', $newMessage);
                }
            }
        }
        echo $newMessage;
    }

}
