#!/usr/bin/php
<?php
namespace docpx\test\names\strin;
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
 * Current version of phpdocpx
 */
define('DOCPX_VERSION', '0.0.1a');

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
 * Set to true to enable recursively scanning directories for
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
 * Directory Names to omit from inclusion separate with ","
 */
define('EXCLUDE_DIR', '.git,.svn');
/**
 * Parse comment blocks using Mardown
 */
define('MARKDOWN_SUPPORT', false);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('WINDOWS', true);
} else {
    define('WINDOWS', false);
}


/**
 * Log
 *
 * The Log object simply logs all activities docpx is performing.
 */
class Logger {

    /**
     * Task message, logged as a operation performed. Provides no indication
     * on whether or not it was successful.
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
    
    public function isNamespaceSeperator()
    {
        return $this->_token[0] === T_NS_SEPARATOR;
    }

    public function isDocBlock()
    {
        return $this->_token[0] === T_DOC_COMMENT;
    }
    
    public function isClass()
    {
        return $this->_token[0] === T_CLASS;
    }
    
    public function isVar()
    {
        return $this->_token[0] === T_VARIABLE;
    }
    
    public function isString()
    {
        return $this->_token[0] === T_STRING;
    }
    
    public function isStatic()
    {
        return $this->_token[0] === T_STATIC;
    }
    
    public function isFunction()
    {
        return $this->_token[0] === T_FUNCTION;
    }
    
    public function isPublic()
    {
        return $this->_token[0] === T_PUBLIC;
    }
    
    public function isPrivate()
    {
        return $this->_token[0] === T_PRIVATE;
    }
    
    public function isProtected()
    {
        return $this->_token[0] === T_PROTECTED;
    }
    
    public function isUse()
    {
        return $this->_token[0] === T_USE;
    }
    
    public function isInterface()
    {
        return $this->_token[0] === T_INTERFACE;
    }
    
    public function isAbstract()
    {
        return $this->_token[0] === T_ABSTRACT;
    }
    
    public function isFinal()
    {
        return $this->_token[0] === T_FINAL;
    }
    
    public function isConst()
    {
        return $this->_token === T_CONST;
    }
    
    public function isOpenBracket()
    {
        return $this->_token[0] === T_CURLY_OPEN;
    }
    
    public function getLineNumber()
    {
        return $this->_token[2];
    }
    
    public function isWhitespace()
    {
        return $this->_token[0] === T_WHITESPACE;
    }
    
    public function getType()
    {
        return token_name($this->_token[0]);
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
                'Tokenizing file "%s"',
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
 * the API holding the contents of each parsed file with each node object
 * ready to be read by the Writer class.
 */
class Doc {
    
    /**
     * Path to this doc objects file.
     */
    protected $_file = null;
    
    /**
     * Tokens generated for this doc.
     */
    protected $_tokens = null;

    
    /**
     * Constructs a new Doc object.
     *
     * @param  string  $file  PHP source file path
     * @param  object  $tokens docpx\Tokens
     */
    public function __construct($file, Tokens $tokens)
    {
        $this->_file = $file;
        $this->_tokens = $tokens;
    }
    
    /**
     * Parses the Doc tokens.
     */
    public function parse()
    {
        $parser = new Parser();
        
        foreach ($this->_tokens as $token) {
            
            $inclass = false;
            $data = array();
            $comments = null;
            
            // get the token type
            switch (true) {
                
                case $token->isNamespace():
                    while(!$this->_tokens->next()->isString());
                    $namespace = $this->_tokens->current()->getValue();
                    while($this->_tokens->next()->isNamespaceSeperator()) {
                        $namespace .= '\\'.$this->_tokens->next()->getValue();
                    }
                    $data['namespace'] = $namespace;
                    info(
                        sprintf(
                            'Entering namespace %s',
                            $namespace
                        )
                    );
                break;
            
                case $token->isDocBlock():
                    $comments[] = $parser->docblock($token->getValue());
                    break;
            }
            
        }
    }
}

/**
 * Reference
 *
 * A reference object used to reference files, classes and namespaces when
 * generating the docs.
 */
class References {
    
}

/**
 * Writer
 *
 * The object which will be responsible for generating the output,
 * the writer is passed a group of Doc objects which it uses to
 * generate the HTML output.
 */
class Writer {

}

/**
 * Parser
 *
 * The object which will parse a Tokens object and generate Doc objects.
 */
class Parser {
    
    /**
     * Parses a PHP Doc block into a readable array.
     *
     * @credit Paul James PHPDoctor
     */
    function docblock($comment)
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
        }
        return $data;
    }
    
}

/**
 * Compiler
 *
 * The object which will run and compile the documentation
 * the main workhorse of docpx.
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
        warning("Docpx - The PHP 5.3 API Generator");
        info("---------------------------------");
        warning("Original author Nickolas Whiting http://www.nwhiting.com");
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
        
        $references = new References();

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
                $this->docs[$this->getRealPath($_file[0])] = new Doc($this->getRealPath($_file[0]), new Tokens($_file[0]));
                
            }
        } elseif (!isset($files)) {
            error(
                "Failed to find any php source files"
            );
        } else {
            $this->docs[$files] = new Doc($this->getRealPath($files), new Tokens($files));
        }

        info("File parsing complete");
        task("Beginning Doc parser");

        foreach ($this->docs as $_path => $_doc) {
            $_doc->parse();
        }

        info("Doc parsing complete");

        task("Beginning documentation generator");
    }
    
    /**
     * Attempts to get the source path location for a php file
     * based on the given source path.
     */
    public function getRealPath($path)
    {
        return str_replace($this->path, '', $path);
    }
}

$compile = new Compiler();
$compile->compile('C:\wamp\www\prggmr\lib\\');