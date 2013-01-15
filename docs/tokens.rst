.. /tokens.php generated using docpx on 01/15/13 04:41pm


docpx\Tokens
============


Tokens

A stack of the parsed tokens converted to docpx Tokens from PHP source.



Methods
-------

__construct
===========

.. function:: __construct($file)


    Parses a PHP file into tokens which then are parsed into a
    stack of node objects.

    :param string $file: PHP Source file to parse.



count
=====

.. function:: count()


    Returns a count of the total number of token nodes.

    :rtype: integer Number of token nodes in the node stack



current
=======

.. function:: current()


    Returns the current item.

    :rtype: mixed The current item.



key
===

.. function:: key()


    Returns the key of the current element.

    :rtype: scalar Key of the current array position.



valid
=====

.. function:: valid()


    Checks if current position is valid

    :rtype: boolean True if valid | False otherwise.



rewind
======

.. function:: rewind()


    Rewinds the iterator to the first element

    :rtype: mixed The current element after rewind.



next
====

.. function:: next()


    Moves forward to the next element

    :rtype: mixed The current element after next.



prev
====

.. function:: prev()


    Moves backward to the previous item.  If already at the first item,
    moves to the last one.

    :rtype: mixed The current item after moving.





