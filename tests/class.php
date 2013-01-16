<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

require_once '../src/load.php';

import('unittest');

/**
 * Test parsing a class.
 */
unittest\suite(function() {

    $this->setup(function(){
        $src = <<<SRC
<?php
/**
 * License DOC
 */

/**
 * This is the class doc
 */
class Foo extends \\foo\\bar\\Foo implements \\foo\\Bar, \\bar\\foo\\Bar {

    /**
     * This is a class property.
     * @var  string
     */
    protected \$_string = null;

    /**
     * This is a function.
     */
    public function __construct(\$param = null) {}

    /**
     * Another function here.
     *
     * @param  string  \$name  this is a parameter
     *
     * @return  string
     */
    public function getName() {}
}
SRC;
        file_put_contents('/tmp/test_class.php', $src);
    });

    $this->teardown(function(){
        //unlink('/tmp/test_class.php');
    });

    $this->test(function(){

        $doc = new \docpx\Doc(
            '/tmp/test_class.php', 
            new \docpx\Tokens('/tmp/test_class.php')
        );

        var_dump($doc->parse());
    });

});

