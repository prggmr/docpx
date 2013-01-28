<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
$path = dirname(realpath(__FILE__));
/**
 * INCLUDE
 */
require_once $path.'/../src/const.php';
require_once $path.'/../src/logger.php';
require_once $path.'/../src/data.php';
require_once $path.'/../src/doc.php';
require_once $path.'/../src/docblock.php';
require_once $path.'/../src/token.php';
require_once $path.'/../src/tokens.php';
require_once $path.'/../src/compiler.php';
require_once $path.'/../src/writer.php';
require_once $path.'/../src/utils.php';
unset($path);