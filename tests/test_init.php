<?php
/**
 * Copyright 2010-14 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

require_once realpath(dirname(__FILE__)).'/../src/load.php';

$formatter = new \logger\Formatter(
    '[{date}] [{str_code}] {message}'.PHP_EOL
);
logger(DOCPX_LOG)->add_handler(new \logger\Handler(
    $formatter, STDOUT, 1
));