<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */


if (!defined('DOCPX_INPUT')) {
    /**
     * The input path
     */
    define('DOCPX_INPUT', dirname(realpath(__FILE__)).'/..');
}

if (!defined('DOCPX_OUTPUT')) {
    /**
     * Output path for the docs
     */
    define('DOCPX_OUTPUT', getcwd());
}

if (!defined('DOCPX_DEBUG')) {
    /**
     * Enable Debug mode producing a more verbose processing.
     */
    define('DOCPX_DEBUG', false);
}

if (!defined('DOCPX_INCLUDE_SOURCE')) {
    /**
     * Include the source of files at the bottom of the document.
     */
    define('DOCPX_INCLUDE_SOURCE', true);
}

/**
 * Current version of phpdocpx
 */
define('DOCPX_VERSION', '1.0.0');
/**
 * Docpx mastermind
 */
define('DOCPX_MASTERMIND', 'Nickolas Whiting');
/**
 * Set to true to output all activity
 */
define('VERBOSE', true);
/**
 * Set to true to enable syntax error checking of files before
 * parsing the source.
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
 * Allow support for when a @ignore tag is encountered
 * docpx will ignore documentation of the next element.
 */
define('IGNORE_TAG', true);
/**
 * Sets to have the first docblock in a file found to be parsed
 * as the license doc. Note that the first comment encountered will
 * be considered the license doc for the file and the second will be
 * considered the file doc.
 */
define('HAS_LICENSE_DOC', true);
/**
 * Flag to indicate exlusion of private class members
 */
define('EXCLUDE_PRIVATE', false);
/**
 * Parameter no default value
 */
define('DOCPX_PARAM_NO_DEFAULT', 0x216e6f5f64656661756c7421);
/**
 * Docpx Logger
 */
define('DOCPX_LOG', 'docpx_logger');
/**
 * Using Windows
 */
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('WINDOWS', true);
} else {
    define('WINDOWS', false);
}