<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

require_once 'test_init.php';

import('unittest');

/**
 * Test parsing a class.
 */
unittest\suite(function($suite) {

    $suite->setup(function($test){
        $src = <<<'SRC'
<?php
/**
 * My Class
 *
 * This is my silly test class
 */
class test_Class
{
    /**
     * This is a test function
     */
    final public function __construct(array $var1 = array('Test', 'Var1')) {}
}

/**
 * This is a test 2 file containing class data for testing purposes.
 */
class test_Class2
{
    /**
     * This is a test function 2
     */
    final public function __construct(array $var1 = array('Test', 'Var1')) {}
}
SRC;
        file_put_contents('/tmp/test_class.php', $src);
    });

    $suite->teardown(function(){
        unlink('/tmp/test_class.php');
    });

    $suite->test(function(){

        $doc = new \docpx\Doc(
            '/tmp/test_class.php',
            new \docpx\Tokens('/tmp/test_class.php')
        );

        var_dump($doc->parse());
    });

});

