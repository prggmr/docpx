#!/usr/bin/php
<?php
namespace docpx;

/**
 * Docpx API Generator
 *
 * Docpx is a PHP 5.3+ API Documentation generator that aims to be
 * lightweight, fast and support all current version of PHP.

 * @author  Nickolas Whiting <me@nwhiting.com>
 * @package  Docpx
 * @version  0.0.1a
 * @copyright  Copyright (c) 2011, Nickolas Whiting
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
define('VALIDATE', true);
/**
 * Set to true to enable recursively scanning directories for
 * files
 */
define('RECURSIVE', false);
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
/**
 * Allow support for when a @ignore tag is encountered
 * docpx will ignore documentation of the next element.
 */
define('IGNORE_TAG');

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
                warn(sprintf(
                    'The file "%s" could not be parsed as it contains errors skipping',
                    $file
                ));
                return false;
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
     *
     * @var  string  System path to the php source represented by this doc object
     */
    protected $_file = null;

    /**
     * Tokens generated for this doc.
     *
     * @var  object  docpx\Tokens
     */
    protected $_tokens = null;

    /**
     *
     */

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
            $lastdoc = null;
            $namespace = null;

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
                    $docblock = Parser::comment($token->getValue());
                    $comments[] = $docblock;
                    $lastdoc = $docblock;
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
 *
 * @credit Union of Rad, Lithium Framework
 */
class Parser {

    /**
	 * List of supported docblock tags.
	 *
	 * @var array
	 */
	public static $doctags = array(
		'todo', 'fix', 'important', 'var',
		'param', 'return', 'throws', 'see', 'link',
		'task', 'dependencies', 'abstract',
        'access', 'author', 'copyright', 'deprecated',
        'deprec', 'example', 'exception', 'global',
        'ignore', 'internal', 'name', 'package',
        'param', 'since', 'static', 'staticvar',
        'subpackage', 'version', 'license'
	);

    /**
     * List of docblock tags which are formatted and need
     * a extra parsing.
     *
     * @var  array
     */
    public static $formattedtags = array(
        'param', 'return', 'throws', 'exception', 'var'
    );

    /**
     * Parses a docblock comment into a readable array.
     *
     * @param  string  $comment  Docblock comment to parse.
     *
     * @return  array  Array of results from the doc parsing.
     */
    public static function comment($comment)
    {
        // Turn comment into a string removing all comment chars
        // and split the comment into 2 sections description/tags
        // @credit  Union of Rad, Lithium Framework
        $comment = trim(preg_replace('/^(\s*\/\*\*|\s*\*{1,2}\/|\s*\* ?)/m', '', $comment));
		$comment = str_replace("\r\n", "\n", $comment);
        if ($items = preg_split('/\n@/ms', $comment, 2)) {
			list($description, $tags) = $items + array('', '');
		}
        $regex = '/\n@(?P<type>' . join('|', static::$doctags) . ")/msi";
        $result = preg_split($regex, "\n@$tags", -1, PREG_SPLIT_DELIM_CAPTURE);
        $tags = array();
        array_shift($result);
        for ($i = 0; $i < count($result) - 1; $i += 2) {
            $type = strtolower(trim($result[$i]));
            $text = trim($result[$i + 1]);

            if (isset($tags[$type])) {
				$tags[$type][] = $text;
			} else {
				$tags[$type] = array($text);
			}
        }

        // Parse specific formatted tags
        foreach (static::$formattedtags as $_format) {
            if (isset($tags[$_format])) {
                if ($_format == 'param') {
                    $array = $tags[$_format];
                    foreach ($array as $_k => $_v) {
                        $param = preg_split('/\s+/', $_v, 3);
                        $name = $type = $text = null;
                        foreach (array('type', 'name', 'text') as $key => $val) {
                            if (!isset($param[$key])) continue;
                            ${$val} = trim($param[$key]);
                        }
                        $tags[$_format][$_k] = compact('type', 'name', 'text');
                    }
                } else {
                    $array = $tags[$_format];
                    foreach ($array as $_k => $_v) {
                        $param = preg_split('/\s+/', $_v, 2);
                        $type = $text = null;
                        foreach (array('type', 'text') as $key => $val) {
                            if (!isset($param[$key])) continue;
                            ${$val} = trim($param[$key]);
                        }
                        $tags[$_format][$_k] = compact('type', 'text');
                    }
                }
            }
        }

        return compact('description', 'tags');
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
        warning("Docpx - The PHP 5.3 API Generator v".DOCPX_VERSION);
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
$compile->compile();