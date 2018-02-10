<?php

namespace Symple\module\snippet;

class Snippet
{

    private $contents;
    private $bindings = array();

    public function bindContents($snippet)
    {
        $config = require __DIR__ . '/../../config/config.php';
        if (is_file($config["MODULE_PATH"] . "snippets/" . $snippet . ".html")) {
            $this->contents .= file_get_contents($config["MODULE_PATH"] . "snippets/" . $snippet . ".html");
        }
    }

    public function bindValues(array $bindings)
    {
        foreach ((array)$bindings as $key => $value) {
            $this->bindings[$key] = $value;
        }

        foreach ((array)$this->bindings as $key => $binding) {
            $this->contents = str_replace('[' . $key . ']', $binding, $this->contents);
        }

        return $this->contents;
    }

    public function bindValue($key, $value)
    {
        $this->bindings[$key] = $value;
    }

    public function build()
    {
        return $this->bindValues($this->bindings);
    }

}