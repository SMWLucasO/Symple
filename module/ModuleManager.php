<?php

namespace Symple\module;

class ModuleManager
{

    /**
     * Load a module
     *
     * @param $moduleLink
     * @param array $moduleBindings
     * @return null|Module
     */
    public static function load($moduleLink, $moduleBindings = array())
    {
        $config = require __DIR__ . '/../config/config.php';
        $file = $config['MODULE_PATH'] . $moduleLink . '.html';

        if (is_file($file)) {
            return new Module($moduleLink, $moduleBindings, file_get_contents($file));
        }

        return null;
    }


}