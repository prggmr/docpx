.. /compiler.php generated using docpx on 01/15/13 05:02pm


docpx\\Compiler
***************


Compiler

Compiles a PHP source file(s) into docpx\Docs.



Methods
=======

__construct
-----------

.. function:: __construct()


    List of files parsed



compile
-------

.. function:: compile([$path = false])


    Runs the entire documentation generatation from start to finish.



getRealPath
-----------

.. function:: getRealPath($path)


    Attempts to get the source path location for a php file
    based on the given source path.



getDocs
-------

.. function:: getDocs()


    Returns the compiled docs array.

    :rtype: array 





