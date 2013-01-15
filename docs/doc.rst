.. /doc.php generated using docpx on 01/15/13 04:41pm


docpx\Doc
=========


Doc

Doc represents a parsed PHP source document.



Methods
-------

__construct
===========

.. function:: __construct($file, $tokens)


    Constructs a new Doc object.

    :param string $file: PHP source file path
    :param object $tokens: docpx\Tokens



parse
=====

.. function:: parse()


    Parses the Doc tokens.



getNextNonWhitespace
====================

.. function:: getNextNonWhitespace()


    Returns the next non-whitespace token found.

    :rtype: object docpx\Node





