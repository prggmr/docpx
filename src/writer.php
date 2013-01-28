<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Writer
 *
 * Writes a \docpx\Doc object into a beautiful human-readable format.
 */
class Writer {

    /**
     * Compiler ready to be written.
     *
     * @var  array
     */
    protected $_compiler = null;

    /**
     * Template style to use for writing documents.
     *
     * @var  string
     */
    protected $_template = null;

    /**
     * Prepares the writer.
     *
     * On constructing the Doc object which must be written is provided.
     *
     * Optionally the template style can be provided also.
     *
     * @param  object  $compiler  \docpx\Compiler
     * @param  string  $template  Template style to write with.
     *
     * @return  void
     */
    public function __construct($compiler, $template = 'rst')
    {
        require_once 'templates/'.$template.'/config.template';
        $this->_compiler = $compiler;
        $this->_template = $template;
    }

    /**
     * Writes the human read-able source to the given path.
     *
     * @param  string  $path  Path to write the output.
     */
    public function write($path)
    {
        foreach ($this->_compiler->getDocs() as $_file => $_doc) {
            $template = $this->template('file.template', [
                'file' => $_file,
                'doc' => $_doc
            ]);
            $write_path = $path;
            $name = explode("/", $_file);
            $file = array_pop($name);
            if (count($name) != 0) {
                $total_dir = '';
                foreach ($name as $_dir) {
                    $total_dir = '/'.$_dir;
                    if (!is_dir($write_path . $total_dir)) {
                        mkdir($write_path . $total_dir);
                    }
                }
                $write_path = $write_path . $total_dir;
            }
            $output_name = explode('.', $file);
            array_pop($output_name);
            $name = sprintf(
                '%s/%s.rst',
                $write_path,
                array_pop($output_name)
            );
            file_put_contents($name, $template);
        }
    }

    /**
     * Parses a template file and returns the contents.
     *
     * @param  string  $template  Template file to parse
     * @param  array  $vars  Variables to declare in the template context.
     *
     * @return  string  Parsed template file.
     */
    public function template($template, $vars)
    {
        extract($vars);
        ob_start();
        include 'templates/'.$this->_template.'/'.$template;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }
}