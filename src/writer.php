<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
interface Dog {}

/**
 * Writer
 *
 * Writes out the documentation.
 */
class Writer extends \dog\cat\face\DogFace implements Dog {

    /**
     * Doc nodes ready to be written.
     *
     * @var  array
     */
    protected $_docs = null;

    /**
     * Prepares the writer.
     *
     * @param  array  $docs  Array of Doc nodes.
     *
     * @return  void
     */
    public function __construct($docs)
    {
        require_once 'templates/rst/config.php';
        $this->_docs = $docs;
    }

    /**
     * Writes the output.
     *
     * @param  string  $path  Path to write the output.
     */
    public function write($path)
    {
        foreach ($this->_docs as $_file => $_doc) {
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
     * Parses a template file.
     *
     * @param  string  $template  Template file to parse
     * @param  array  $vars  Variables to assign the template.
     *
     * @return  string  Parsed template file.
     */
    public function template($template, $vars)
    {
        extract($vars);
        ob_start();
        include 'templates/rst/'.$template;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }
}

namespace dog\cat\face;
class DogFace {}

