<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */


/**
 * Data
 *
 * Data object stores information using simply key/value pairs through an OO 
 * architecture.
 */
class Data
{
    /**
     * Registry property, information is stored as a `key` -> `value` pair.
     *
     * @var  array  Array of `key` -> `value` mappings for registry contents.
     */
    protected $__registry = array();

    public function __construct($data = null) {
        if (null != $data) {
            if (is_array($data)) {
                $this->set($data);
            }
        }
    }

    /**
     *  Sets a variable.
     *  Variables can be set using three different configurations, they can be
     *  set as an ordinary `$key`, `$value` pair, an array of `$key` => `$value`
     *  mappings which will be transversed, or finally as a "." delimited string
     *  in the format of `$key`, `$value` which will be transformed into an array,
     *  these configurations can also be combined.
     *
     *  @param  mixed  $key  A string identifier, array of key -> value mappings,
     *          or a "." delimited string.
     *  @param  mixed  $value  Value of the `$key`.
     *  @param  boolean  $overwrite  Overwrite existing key if exists
     *
     *  @return  boolean
     */
    public function set($key, $value = null, $overwrite = true) {

        if (null === $key) {
            return false;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v, $overwrite);
            }
            return true;
        }

        if (true === $this->has($key) && !$overwrite) {
            return false;
        }
        if (false !== strpos($key, '.')) {
            $nodes  = explode('.', $key);
            $data =& $this->__registry;
            $nodeCount = count($nodes) - 1;
            for ($i=0;$i!=$nodeCount;$i++) {
                // Bug caused data to not overwrite if converting from ( any ) -> array
                // and an overwrite is in order
                if (!is_array($data[$nodes[$i]])) {
                    $data[$nodes[$i]] = array();
                }
                $data =& $data[$nodes[$i]];
            }
            $data[$nodes[$nodeCount]] = $value;
            return true;
        } else {
            $this->__registry[$key] = $value;
        }

        return true;
    }

    /**
     * Returns a variable. The variable name can be
     * provided as a single string of the variable or a "." delimited string
     * which maps to the array tree storing this variable.
     *
     * @param  string  $key  A string of the variable name or a "." delimited
     *         string containing the route of the array tree.
     * @param  array   $options  An array of options to use while retrieving a
     *         variable from the cache. Avaliable options.
     *
     *         `default` - Default value to return if `$key` is not found.
     *
     *         `tree` - Not Implemented
     *
     * @return  mixed
     */
    public function get($key, $options = array()) {
        $defaults = array('default' => false, 'tree' => true);
        $options += $defaults;

        if (is_string($key)) {
            if (false !== strpos($key, '.')) {
                $keyArray = explode('.', $key);
                $count    = count($keyArray) - 1;
                $last     = $keyArray[$count];
                $data     = $this->__registry;
                for ($i=0;$i!=count($keyArray);$i++) {
                    $node = $keyArray[$i];
                    if ($node !== '') {
                        if (array_key_exists($node, $data)) {
                            if ($node == $last && $i == $count) {
                                return $data[$node];
                            }
                            if (is_array($data[$node])) {
                                $data = $data[$node];
                            }
                        }
                    }
                }
                if ($data !== $this->__registry) {
                    return $data;
                }
            }

            return (!isset($this->__registry[$key])) ? $options['default'] : $this->__registry[$key];
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Invalid arugment "$key" expected "string" received "%s"', gettype($key)
            )
        );
    }

    /**
     * Returns if a variable identified by `$key` exists in the registry.
     * `has` works just as `get` and allows for identical `$key`
     * configuration.
     * This is a mirrored shorthand of (prggmr::get($key, array('default' => false)) !== false);
     *
     * @param  $key  A string of the variable name or a "." delimited
     *         string containing the route of the array tree.
     * @return  boolean
     */
    public function has($key) {
        return ($this->get($key, array('default' => false)) !== false);
    }
}