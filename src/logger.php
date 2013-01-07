<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Logs a message as a task.
 * 
 * @param  string  $message  Message to log.
 * 
 * @return  void
 */
function task($message) {
    Logger::log($message, Logger::TASK);
}

/**
 * Logs a message as a warning.
 * 
 * @param  string  $message  Message to log.
 * 
 * @return  void
 */
function warning($message) {
    Logger::log($message, Logger::WARN);
}

/**
 * Logs a message as a error.
 * 
 * @param  string  $message  Message to log.
 * 
 * @return  void
 */
function error($message) {
    Logger::log($message, Logger::ERROR);
}

/**
 * Logs a message as info.
 * 
 * @param  string  $message  Message to log.
 * 
 * @return  void
 */
function info($message) {
    Logger::log($message, Logger::INFO);
}

/**
 * Logger
 *
 * Logs all activities docpx is performing.
 *
 * You should not call this class specifically, instead use the API functions.
 *
 * - task
 * - warning
 * - error
 * - info
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

    public static $count = 0;

    /**
     * Stack of all log messages.
     *
     * @var  array  Stack of all messages sent to the log
     */
    protected static $_log = array();

    public static function log($message, $type = Log::TASK)
    {
        static::$count++;
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
                        if (!DOCPX_DEBUG) {
                            return;
                        }
                        $message = "\033[1;34m".$message."\033[0m";
                        break;
                    case Logger::WARN:
                        if (!DOCPX_DEBUG) {
                            return;
                        }
                        $message = "\033[1;33m".$message."\033[0m";
                        break;
                    case Logger::TASK:
                        $message = "\033[1;32m".$message."\033[0m";
                        break;
                    default:
                        break;
                }
            }
            echo sprintf("[%s] %s \n", /* date('Y-m-d h:i:s', time()) */static::$count, $message);
        }

        // kill the script
        if ($type === Logger::ERROR) {
            warning("Docpx halted due to compile error");
            die();
        }
    }

}