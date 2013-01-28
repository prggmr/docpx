<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */



/**
 * Tokens
 *
 * A stack of the parsed tokens converted to docpx Tokens from PHP source.
 */
class Tokens implements \Iterator, \Countable {

    /**
     * The stack of token nodes generated from the PHP source.
     *
     * @var  array  Stack of docpx\Node objects.
     */
    protected $_tokens = array();

    /**
     * Indicates whether or not the current position is valid.
     *
     * @var  boolean  Indicates if the current position is valid
     */
    protected $_valid = false;

    /**
     * Parses a PHP file into tokens which then are parsed into a
     * stack of node objects.
     *
     * @param  string  $file  PHP Source file to parse.
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            logger(DOCPX_LOG)->error(sprintf(
                'Failed to locate source file "%s"; halting compiler',
                $file));
        }

        if (VALIDATE) {
            exec('php -l '.escapeshellarg($file), $output, $result);

            if ($result != 0) {
                logger(DOCPX_LOG)->warning(sprintf(
                    'The file "%s" could not be parsed as it contains errors skipping',
                    $file
                ));
                return false;
            }
        }

        $source = file_get_contents($file);
        $tokens = token_get_all($source);

        logger(DOCPX_LOG)->debug(
            sprintf(
                'Tokenizing file "%s"',
                $file
            )
        );

        foreach ($tokens as $_line => $_token) {
            if (is_array($_token)) {
                $this->_tokens[] = new Token($_token);
            }
        }

        logger(DOCPX_LOG)->debug(
            sprintf(
                'Token generation for file "%s" complete',
                $file
            )
        );
    }

    /**
     * Returns a count of the total number of token nodes.
     *
     * @return  integer  Number of token nodes in the node stack
     */
    public function count(/* ... */)
    {
        return iterator_count($this);
    }

    /**
     * Returns the current item.
     *
     * @return  mixed  The current item.
     */
    public function current(/* ... */)
    {
        return current($this->_tokens);
    }

    /**
     * Returns the key of the current element.
     *
     * @return  scalar  Key of the current array position.
     */
    public function key(/* ... */)
    {
        return key($this->_tokens);
    }

    /**
     * Checks if current position is valid
     *
     * @return  boolean  True if valid | False otherwise.
     */
    public function valid(/* ... */)
    {
        return $this->_valid;
    }

    /**
     * Rewinds the iterator to the first element
     *
     * @return  mixed  The current element after rewind.
     */
    public function rewind(/* ... */)
    {
        $this->_valid = (reset($this->_tokens) !== false);
        return $this->current();
    }

    /**
     * Moves forward to the next element
     *
     * @return  mixed  The current element after next.
     */
    public function next(/* ... */)
    {
        $this->_valid = (next($this->_tokens) !== false);
        if ($this->_valid) {
            return $this->current();
        }
        return false;
    }

    /**
     * Moves backward to the previous item.  If already at the first item,
     * moves to the last one.
     *
     * @return  mixed  The current item after moving.
     */
    public function prev(/* ... */)
    {
        if (!prev($this->_tokens)) {
            end($this->_tokens);
        }

        return $this->current();
    }

}
