<?php

defined( 'ROOT_PATH' ) || define( 'ROOT_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

spl_autoload_register( function ( $className ) {
    require_once ( ROOT_PATH . str_replace( array( '\\', 'Symple\\' ), array( DIRECTORY_SEPARATOR, '' ), $className ) . '.php' );
} );
