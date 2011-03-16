#!/usr/bin/php
<?php
namespace docpx;
/**
 * Copyright 2010 Nickolas Whiting
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Docpx is a PHP 5.3+ API Documentation generator that aims to be
 * lightweight, fast and support all current version of PHP.
 */

define('VERBOSE', 1);
define('VALIDATE', 1);

/**
 * Log
 *
 * The Log object simply logs all activites docpx is performing.
 */
class Logger {

    /**
     * Task message, logged as a operation performed. Provides no indication
     * on wether or not it was succesfull.
     */
    const TASK = 1001;

    /**
     * Error message indicates an error during parsing, causing the doc
     * generation to halt.
     */
    const ERROR = 1002;

    /**
     * Warning found during parsing which was recoverable.
     */
    const WARN = 1003;

    /**
     * Stack of all log messages.
     *
     * @var  array  Stack of all messages sent to the log
     */
    protected static $_log = array();

    public static function log($message, $type = Log::TASK)
    {
        if (!isset(static::$_log[$type])) {
            static::$_log[$type] = array();
        }

        static::$_log[$type][] = array(
            'timestamp' => time(),
            'string'    => $message
        );

        // am I being verbose ??
        if (VERBOSE == 1) {
            echo sprintf("[%s] %s \n", date('Y-m-d h:i:s', time()), $message);
        }

        // kill the script
        if ($type === Log::ERROR) die();
    }

}

function log($message, $type = Logger::TASK) {
    Logger::log($message, $type);
}

/**
 * Node
 *
 * A node is a representation of a php token.
 */
class Node {

    public $_token = null;

    public function __construct(array $token)
    {
        $this->_token = $token;
    }

}

/**
 * Tokens
 *
 * A stack of the parsed tokens converted to nodes generated from a php
 * source file.
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
            log(sprintf(
                'Failed to locate source file "%s"; halting compiler',
                $file),
                Log::ERROR);
        }

        if (VALIDATE) {
            exec('php -l '.escapeshellarg($file), $output, $result);

            if ($result != 0) {
                log(sprintf(
                    'The file "%s" could not be parsed as it contains errors; halting compiler',
                    $file),
                    Log::ERROR);
            }
        }

        $source = file_get_contents($file);
        $tokens = token_get_all($source);

        foreach ($tokens as $_line => $_token) {
            if (is_array($_token)) {
                try {
                    $this->_tokens[$_line] = new Node($_token);
                } catch (NodeException $e) {
                    log(sprintf(
                        'Error encountered when generating nodes "%s"',
                        $e->getMessage()
                    ), Log::ERROR);
                }
            }
        }
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
     * Moves foward to the next element
     *
     * @return  mixed  The current element after next.
     */
    public function next(/* ... */)
    {
        $this->_valid = (next($this->_tokens) !== false);
        return $this->current();
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

/**
 * Doc
 *
 * A doc object is what will be passed to the writer for outputting
 * the API.
 */
class Doc {

}

/**
 * Reference
 *
 * A reference object used to reference classes and namespaces when
 * generating the docs.
 */
class References {

}

/**
 * Writer
 *
 * The object which will be responsible for generating the output.
 */
class Writer {

}

/**
 * Parser
 *
 * The object which will parse a Tokens object and generate Doc objects.
 */
class Parser {

}

/**
 * Compiler
 *
 * The object which will run and compile the documentation.
 */
class Compiler {

}