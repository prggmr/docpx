<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Compiler
 *
 * Compiles a PHP source file(s) into docpx\Docs.
 */
class Compiler {

    /**
     * Path to the php source files
     *
     * @var  string  Path to the php files that will be parsed
     */
    public $path = null;

    /**
     * Collection of Doc objects
     */
    public $docs = array();

    /**
     * List of files parsed
     */

    public function __construct()
    {
        info("Docpx - The PHP API Generator v".DOCPX_VERSION);
        info("---------------------------------");
        info("Author Nickolas Whiting");
    }

    /**
     * Runs the entire documentation generatation from start to finish.
     */
    public function compile($path = null) {

        // if nothing use the current path
        if (null === $path) {
            $path = realpath(__DIR__).'/';
        }

        $this->path = $path;

        // Check if we are parsing a single php source file
        if (is_dir($path)) {

            // are we recursive
            if (RECURSIVE) {

                info(
                    sprintf(
                        'Beginning Recursive Directory scan "%s"',
                        $path
                    )
                );

                $directory = new \RecursiveDirectoryIterator($path);
                $iterator = new \RecursiveIteratorIterator($directory);
                $files = new \RegexIterator($iterator, '/^.+\\'.EXTENSION.'/i', \RecursiveRegexIterator::GET_MATCH);
            } else {

                info(
                    sprintf(
                        'Beginning Directory scan "%s"',
                        $path
                    )
                );

                $directory = new \DirectoryIterator($path);
                $iterator = new \IteratorIterator($directory);
                $files = new \RegexIterator($iterator, '/^.+\\'.EXTENSION.'$/i', \RegexIterator::GET_MATCH);
            }

        } elseif (is_file($path)) {
            $files = $path;
        } else {
            // path not found
            error(sprintf(
                'Path "%s" not found'
            , $path));
        }

        if (is_object($files)) {
            $exclude = explode(',', EXCLUDE_DIR);
            foreach ($files as $_k => $_file) {
                if (!RECURSIVE) $_file[0] = $path.$_file[0];
                $cont = false;
                foreach ($exclude as $_exclude) {
                    if (false !== strpos($_file[0], $_exclude)) {
                        $cont = true;
                        break;
                    }
                }
                if ($cont) continue;
                $this->docs[$this->getRealPath($_file[0])] = new Doc(
                    $this->getRealPath($_file[0]), 
                    new Tokens($_file[0])
                );

            }
        } elseif (!isset($files)) {
            error(
                "Failed to find any php source files"
            );
        } else {
            $this->docs[$files] = new Doc(
                $this->getRealPath($files), 
                new Tokens($files)
            );
        }

        info("File parsing complete");
        task("Beginning Doc parser");

        foreach ($this->docs as $_path => $_doc) {
            $this->docs[$_path] = $_doc->parse();
        }

        info("Doc parsing complete");
    }

    /**
     * Attempts to get the source path location for a php file
     * based on the given source path.
     */
    public function getRealPath($path)
    {
        return str_replace($this->path, '', $path);
    }

    /**
     * Returns the compiled docs array.
     *
     * @return array
     */
    public function getDocs(/* ... */)
    {
        return $this->docs;
    }
}