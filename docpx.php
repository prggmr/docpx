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

/**
 * Set to true to output all activity
 */
define('VERBOSE', true);
/**
 * Set to true to enable syntax error checking of files before
 * parsing the source.
 *
 * Disable to improve performance
 */
define('VALIDATE', false);
/**
 * Set to true to enable recursivly scanning directories for
 * files
 */
define('RECURSIVE', true);
/**
 * Set to true to enable color output in the console based on the
 * message type.
 */
define('COLORS', true);
/**
 * File extensions to include
 */
define('EXTENSION', '.php|.php5|.php4|.inc.php');
/**
 * Directory Names to omit from inclusion seperate with ","
 */
define('EXCLUDE_DIR', '.git,.svn');

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('WINDOWS', true);
} else {
    define('WINDOWS', false);
}


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
     * INFO message
     */
    const INFO = 1004;

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
        if (VERBOSE || $type === (Logger::ERROR || Logger::WARN)) {
            // disable windows output
            if (COLORS && !WINDOWS) {
                switch($type) {
                    case Logger::ERROR:
                        $message = "\033[1;31m".$message."\033[0m";
                        break;
                    case Logger::INFO:
                        $message = "\033[1;34m".$message."\033[0m";
                        break;
                    case Logger::WARN:
                        $message = "\033[1;33m".$message."\033[0m";
                        break;
                    case Logger::TASK:
                        $message = "\033[1;32m".$message."\033[0m";
                        break;
                    default:
                        break;
                }
            }
            echo sprintf("[%s] %s \n", date('Y-m-d h:i:s', time()), $message);
        }

        //Logger::write();

        // kill the script
        if ($type === Logger::ERROR) {
            warning("Docpx halted due to compile error");
            die();
        }
    }

}

function task($message) {
    Logger::log($message, Logger::TASK);
}

function warning($message) {
    Logger::log($message, Logger::WARN);
}

function error($message) {
    Logger::log($message, Logger::ERROR);
}

function info($message) {
    Logger::log($message, Logger::INFO);
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

    public function getValue()
    {
        return $this->_token[1];
    }

    public function isNamespace() {
        return $this->_token[0] === T_NAMESPACE;
    }

    public function isDocBlock()
    {
        return $this->_token[0] === T_DOC_COMMENT;
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
    public function parse($file)
    {
        if (!file_exists($file)) {
            error(sprintf(
                'Failed to locate source file "%s"; halting compiler',
                $file));
        }

        if (VALIDATE) {
            exec('php -l '.escapeshellarg($file), $output, $result);

            if ($result != 0) {
                error(sprintf(
                    'The file "%s" could not be parsed as it contains errors; halting compiler',
                    $file
                ));
            }

            task(
                sprintf(
                    'File "%s" contains no PHP Errors',
                    $file
                )
            );
        }

        $source = file_get_contents($file);
        $tokens = token_get_all($source);

        task(
            sprintf(
                'Parsing file "%s"',
                $file
            )
        );

        foreach ($tokens as $_line => $_token) {
            if (is_array($_token)) {
                try {
                    $this->_tokens[] = new Node($_token);
                } catch (NodeException $e) {
                    error(sprintf(
                        'Error encountered when generating nodes "%s"',
                        $e->getMessage()
                    ));
                }
            }
        }

        task(
            sprintf(
                'File "%s" parsing complete',
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

    /**
     * Path to the php source files
     *
     * @var  string  Path to the php files that will be parsed
     */
    public $path = null;

    public function __construct()
    {
        $this->_tokens = new Tokens();

        warning("Docpx - The PHP 5.3 API Generator");
        info("---------------------------------");
        warning("Original author Nickolas Whiting http://www.nwhiting.com");
    }

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
            
            info("im here");
            
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
                $this->_tokens->parse($_file[0]);
                
            }
        } elseif (!isset($files)) {
            error(
                "Failed to find any php source files"
            );
        } else {
            $this->_tokens->parse($files);
        }

        info("File parsing complete");
        task("Beginning Token parser");

        foreach ($this->_tokens as $_k => $_token) {
            if ($_token->isDocBlock()) {
                $this->parseDocBlock($_token->getValue());
            }
        }

        info("Token parsing complete");

        task("Beginning documentation generator");
    }

    /**
     * Parses a PHP Doc block into a readable array.
     *
     * @credit Paul James PHPDoctor
     */
    function parseDocBlock($comment)
    {
        if (substr(trim($comment), 0, 3) != '/**') return array(); // not doc comment, abort

        $data = array(
            'docComment' => $comment,
            'tags' => array()
        );

        $explodedComment = preg_split('/\n[ \n\t\/]*\*[ \t]*@/', "\n".$comment);

        preg_match_all('/^[ \t]*[\/*]*\**( ?.*)[ \t\/*]*$/m', array_shift($explodedComment), $matches); // changed; we need the leading whitespace to detect multi-line list entries

        foreach ($explodedComment as $tag) { // process tags
            // strip whitespace, newlines and asterisks
            $tag = preg_replace('/(^[\s\n\*]+|[\s\*]*\*\/$)/m', ' ', $tag); // fixed: empty comment lines at end of docblock
            $tag = preg_replace('/\n+/', '', $tag);
            $tag = trim($tag);

            $parts = preg_split('/\s+/', $tag);
            $name = isset($parts[0]) ? array_shift($parts) : $tag;
            $text = join(' ', $parts);
            var_dump($name);

        }
        return $data;
    }

}

$compile = new Compiler();
$compile->compile();