<?php
if (function_exists('logger')) {} else {
/**
 * Copyright 2013 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

if (!defined('LOGGER_LOG_LEVEL')) {
    /**
     * LOGGER_LOG_LEVEL
     *
     * The default log level code to use when logging messages.
     */
    define('LOGGER_LOG_LEVEL', 1);
}

/**
 * Returns a logger identified by the given name.
 *
 * If the logger does not exist it is created.
 *
 * @param  string  $name  Name of the logger
 *
 * @return   object  Logger
 */
function logger($name = null)
{
    if (null === $name) {
        return Logger::instance();
    }
    return Logger::instance()->get_logger($name);
}

/**
 * Stupid simple logging utility for PHP based on Pythons logger.
 */
class Logger {

    /**
     * DEBUG
     *
     * Detailed information, typically of interest only when diagnosing
     * problems.
     */
    const DEBUG = 1;
    /**
     * INFO
     *
     * Confirmation that things are working as expected.
     */
    const INFO = 2;
    /**
     * WARNING
     *
     * An indication that something unexpected happened, or indicative of
     * some problem in the near future (e.g. ‘disk space low’).
     *
     * The software is still working as expected.
     */
    const WARNING = 3;
    /**
     * ERROR
     *
     * Due to a more serious problem, the software has not been able to
     * perform some function.
     */
    const ERROR = 4;
    /**
     * CRITICAL
     *
     * A serious error, indicating that the program itself may be unable
     * to continue running.
     */
    const CRITICAL = 5;

    /**
     * Array of log handlers.
     *
     * @var
     */
    protected $_handlers = [];

    /**
     * @var  object|null  Instanceof the singleton
     */
    protected static $_instance = null;

    /**
     * Returns an instance of the singleton.
     *
     * Passes args to constructor
     */
    final public static function instance(/* ... */)
    {
        if (null === static::$_instance) {
            static::$_instance = new self(func_get_args());
        }

        return self::$_instance;
    }

    /**
     * Disallow cloning
     */
    final public function __clone() {
        return false;
    }

    /**
     * Adds a handler.
     *
     * @param  object  $handler  HJandler
     *
     * @return  void
     */
    public function add_handler(Handler $handler)
    {
        $this->_handlers[] = $handler;
    }

    /**
     * Logs a message.
     *
     * @param  integer  $code  Log level code.
     * @param  string  $message  Message to log.
     *
     * @return  void
     */
    public function log($code, $message)
    {
        foreach ($this->_handlers as $_handler) {
            $_handler->handle($code, $message);
        }
    }

    /**
     * Logs a debug message.
     *
     * @param  string  $message  Message to log.
     *
     * @return  void
     */
    public function debug($message)
    {
        $this->log(Logger::DEBUG, $message);
    }

    /**
     * Logs a info message.
     *
     * @param  string  $message  Message to log.
     *
     * @return  void
     */
    public function info($message)
    {
        $this->log(Logger::INFO, $message);
    }

    /**
     * Logs a warning message.
     *
     * @param  string  $message  Message to log.
     *
     * @return  void
     */
    public function warning($message)
    {
        $this->log(Logger::WARNING, $message);
    }

    /**
     * Logs a error message.
     *
     * @param  string  $message  Message to log.
     *
     * @return  void
     */
    public function error($message)
    {
        $this->log(Logger::ERROR, $message);
    }

    /**
     * Logs a critical message.
     *
     * @param  string  $message  Message to log.
     *
     * @return  void
     */
    public function critical($message)
    {
        $this->log(Logger::CRITICAL, $message);
    }

    /**
     * Returns a logger.
     *
     * @param  string  $logger  Name of the logger.
     *
     * @return  object  Logger
     */
    public function get_logger($logger)
    {
        if (!isset($this->_loggers[$logger])) {
            $this->_loggers[$logger] = new self();
        }
        return $this->_loggers[$logger];
    }
}

/**
 * Handler
 *
 * Handles a log message
 */
class Handler {

    /**
     * The message formatter
     *
     * @var  object
     */
    protected $_formatter = null;

    /**
     * Output file.
     *
     * @var  resource
     */
    protected $_output = null;

    /**
     * Level to handle log messages.
     */
    protected $_level = null;

    /**
     * Sets the formatter.
     *
     * @param  object  $formatter
     * @param  resource  $output  Output resource or file
     * @param  integer  $level  Code level to log, anything greater than the
     *                          given code will be logged.
     *
     * @return  void
     */
    public function __construct(Formatter $formatter, $output, $level = LOGGER_LOG_LEVEL)
    {
        $this->_formatter = $formatter;
        $this->_output = $output;
        $this->_level = $level;
    }

    /**
     * Handles a message.
     *
     * @param  integer  $code  Log level code.
     * @param  string  $message  Message to handle.
     *
     * @return  void
     */
    public function handle($code, $message)
    {
        if ($code >= $this->_level) {
            $message = $this->_formatter->format($code, $message);
            $this->_make_writeable();
            fwrite($this->_output, $message);
        }
    }

    /**
     * Makes the output writeable.
     *
     * This will create a non-blocking stream to the given file.
     *
     * @return  boolean
     */
    protected function _make_writeable(/* ... */)
    {
        if (!is_resource($this->_output)) {
            if (!file_exists($this->_output)) {
                touch($this->_output);
            }
            $this->_output = fopen($this->_output, 'w+');
            @stream_set_blocking($this->_output, false);
            return true;
        } else {
            @stream_set_blocking($this->_output, false);
        }
    }
}

/**
 * Formatter
 *
 * Formats a log message.
 *
 * The formatter allows for the following parameters.
 *
 * %date - Date of the log
 * %message - Log message
 * %code - Error Code Level
 * %str_code - String representation of the error code
 */
class Formatter {

    /**
     * Output file.
     *
     * @var  resource
     */
    protected $_format = null;

    /**
     * Create a formatter.
     *
     * @param  object  $format  String format to log a message
     *
     * @return  void
     */
    public function __construct($format)
    {
        $this->_format = $format;
    }

    /**
     * Handles a message.
     *
     * @param  integer  $code  Log level code.
     * @param  string  $message  Message to handle.
     *
     * @return  void
     */
    public function format($code, $message)
    {
        switch ($code) {
            case Logger::DEBUG:
                $str_code = 'DEBUG';
                break;
            case Logger::INFO:
                $str_code = 'INFO';
                break;
            case Logger::WARNING:
                $str_code = 'WARNING';
                break;
            case Logger::ERROR:
                $str_code = 'ERROR';
                break;
            case Logger::CRITICAL:
                $str_code = 'CRITICAL';
                break;
        }
        return psprintf($this->_format, [
            'date' => date('m-d-y H:i:s e'),
            'message' => $message,
            'code' => $code,
            'str_code' => $str_code
        ]);
    }
}

/**
 * Returns a formatted string. Accepts named arguments.
 */
function psprintf($str, $params) {
    return preg_replace_callback('/{(\w+)}/', function($match)use($params){
        return (isset($params[$match[1]])) ? $params[$match[1]] : '';
    }, $str);
}
}