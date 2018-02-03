<?php

namespace Symple\mvc;


class Route {

    /**
     * @param $url
     * @param $function
     * @return bool
     */
    public static function get($url, $function) {

        $URIComponents = explode("/", $_SERVER['REQUEST_URI']);

        $config = require 'config/config.php';

        $rootParts = explode('/', $config["ROOT_PATH"]);

        foreach( (array) $rootParts as $key => $value) {
            if(($key = array_search($value, $URIComponents)) !== false) {
                unset($URIComponents[$key]);
            }
        }

        $URIComponents = array_values( array_filter( $URIComponents ) );
        $urlPieces = array_values( array_filter( explode( '/', $url ) ) );


        if( sizeof( $URIComponents ) !== sizeof( $urlPieces ) ) {
            return false;
        } else {
            foreach( (array) $urlPieces as $key => $value ) {
                if( ! ( $value === $URIComponents[$key] ) && ! ( substr( $value, 0, 1 ) === '{' && substr( $value, strlen( $value ) - 1, strlen( $value ) ) ) ) {
                    return false;
                } else if( substr( $value, 0, 1 ) === '{' && substr( $value, strlen( $value ) - 1, strlen( $value ) ) ) {
                    if( substr( $value, 0, 1 ) === '{' && substr( $value, strlen( $value ) - 1, strlen( $value ) ) ) {
                        if( ! ( isset( $URIComponents[$key] ) ) ) {
                            return false;
                        }
                    }
                }
            }
        }

        $paramArray = array();
        foreach( (array) $urlPieces as $key => $value ) {
            if( substr( $value, 0, 1 ) === '{' ) {
                if( substr( $value, strlen( $value ) - 1, strlen( $value ) ) ) {
                    $paramArray[] = $URIComponents[$key];
                }
            }
        }

        $function(...$paramArray);


        return true;
    }

}
