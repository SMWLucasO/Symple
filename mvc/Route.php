<?php

namespace Symple\mvc;

class Route
{

    /**
     * Return GET data or execute an action using this method
     *
     * @param $url string
     * @param $function \Callable
     * @return bool
     */
    public static function get($url, $function)
    {
        $URIComponents = explode("/", $_SERVER['REQUEST_URI']);
        $URIComponents = self::filterRootPieces($URIComponents);
        $URIComponents = self::filter($URIComponents);

        $urlPieces = self::filter(explode('/', $url));

        if (self::load($URIComponents, $urlPieces)) {
            $paramArray = self::getFunctionArguments($URIComponents, $urlPieces);
            $function(...$paramArray);
            return true;
        }

        return false;
    }

    /**
     * Return POST data or execute an action using this method
     * @param $function \Callable
     */
    public static function post($function)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $function($_POST);
        }
    }

    /**
     * Remove the URIComponents that are also contained within the root path
     *
     * @param $URIComponents array
     * @return array
     */
    private static function filterRootPieces($URIComponents)
    {
        $config = require __DIR__ . '/../config/config.php';
        $rootParts = explode('/', $config["ROOT_PATH"]);
        foreach ((array)$rootParts as $key => $value) {
            if (($key = array_search($value, $URIComponents)) !== false) {
                unset($URIComponents[$key]);
            }
        }

        return $URIComponents;
    }

    /**
     * Remove empty array pieces and reset the count
     *
     * @param $array array
     * @return array
     */
    private static function filter($array)
    {
        return array_values(array_filter($array));
    }

    /**
     * Check if the route can be executed
     *
     * @param $URIComponents array
     * @param $urlPieces array
     * @return bool
     */
    private static function load($URIComponents, $urlPieces)
    {
        if (sizeof($URIComponents) !== sizeof($urlPieces)) {
            return false;
        } else {
            foreach ((array)$urlPieces as $key => $value) {
                if (!($value === $URIComponents[$key]) && !(substr($value, 0, 1) === '{' && substr($value,
                            strlen($value) - 1, strlen($value)))) {
                    return false;
                } else {
                    if (substr($value, 0, 1) === '{' && substr($value, strlen($value) - 1, strlen($value))) {
                        if (substr($value, 0, 1) === '{' && substr($value, strlen($value) - 1, strlen($value))) {
                            if (!(isset($URIComponents[$key]))) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get the arguments to initialize the function of the route
     *
     * @param $URIComponents array
     * @param $urlPieces array
     * @return array
     */
    private static function getFunctionArguments($URIComponents, $urlPieces)
    {
        $paramArray = array();
        foreach ((array)$urlPieces as $key => $value) {
            if (substr($value, 0, 1) === '{') {
                if (substr($value, strlen($value) - 1, strlen($value))) {
                    $paramArray[] = $URIComponents[$key];
                }
            }
        }

        return $paramArray;
    }

}