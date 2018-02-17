<?php

namespace Symple\module\snippet;

class Snippet
{

    private $contents;
    private $bindings = array();

    /**
     * Link a snippet by it's filename (without the extension) and it has to be a .html file
     * Returns itself to be used as a builder method.
     *
     * @param string $snippet
     * @return Snippet
     */
    public function bindContents($snippet)
    {
        $config = require __DIR__ . '/../../config/config.php';
        if (is_file($config['MODULE_PATH'] . 'snippets/' . $snippet . '.html')) {
            $this->contents .= file_get_contents($config['MODULE_PATH'] . 'snippets/' . $snippet . '.html');
        }

        return $this;
    }

    /**
     * Bind all the placeholder values
     *
     * @param array $bindings
     * @return mixed
     */
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


    /**
     * Bind a placeholder value
     * Returns itself to be used as a builder method.
     *
     * @param string $key
     * @param string $value
     * @return Snippet
     */
    public function bindValue($key, $value)
    {
        $this->bindings[$key] = $value;
        return $this;
    }

    /**
     * Append custom content to the snippet.
     * Returns itself to be used as a builder method.
     *
     * @param string $content
     * @return Snippet
     */
    public function appendContent($content)
    {
        $this->contents .= $content;
        return $this;
    }

    /**
     * Build the snippet
     *
     * @return mixed
     */
    public function build()
    {
        return $this->bindValues($this->bindings);
    }

}