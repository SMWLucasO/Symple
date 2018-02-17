<?php

namespace Symple\module;


use Symple\module\snippet\Snippet;

class Module
{


    private $link;
    private $scripts = array();
    private $contents, $snippets = array();
    private $bindings = array();

    /**
     * Module constructor.
     * @param string $moduleLink
     * @param array $moduleBindings
     * @param string $contents
     */
    public function __construct($moduleLink, $moduleBindings = array(), $contents = "")
    {
        $this->link = $moduleLink;
        $this->bindings = $moduleBindings;
        $this->contents = $contents;
    }


    /**
     * Prepare scripts to be required upon building the module.
     *
     * @param array ...$script
     */
    public function attachScripts(...$script)
    {
        foreach ((array)$script as $src) {
            array_push($this->scripts, $src);
        }
    }

    /**
     * Attach a snippet to the module
     *
     * @param string $placeholder
     * @param string $snippetLink
     * @param array $bindings
     */
    public function attachSnippet($placeholder, $snippetLink, $bindings)
    {
        $snippet = new Snippet();

        $snippet->bindContents($snippetLink);
        $this->snippets[$placeholder] = $snippet->bindValues($bindings);
    }


    /**
     * Build and show the module front/back-end
     *
     * @return mixed|string
     */
    public function build()
    {

        # Starts from the project root
        foreach ((array)$this->scripts as $script) {
            require_once $script;
        }

        foreach ((array)$this->bindings as $key => $binding) {
            $this->contents = str_replace('[' . $key . ']', $binding, $this->contents);
        }

        foreach ((array)$this->snippets as $key => $contents) {
            $this->contents = str_replace('[' . $key . ']', $contents, $this->contents);
        }

        return $this->contents;
    }

}