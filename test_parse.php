<?php
namespace docpx;

/**
 *  Copyright 2010 Nickolas Whiting
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Test Class
 *
 * The test class exists to test the functionality of docpx.
 *
 * This should generate into some nice RST text once compiled.
 *
 * Some examples,
 *
 * Lists
 * -----
 *
 * * Item 1
 * * Item 2
 * * Item 3
 *
 * Some code indention
 *
 * .. code-block:: php
 *
 *     <?php
 *
 *     /**
 *      * OMG!
 *      *
 *      * I can finally auto generate beautiful docs! 
 *      */
 *     function foo() {
 *     }
 *
 */
function this_does_something(){

}

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