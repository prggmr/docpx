.. /token.php generated using docpx on 01/15/13 04:41pm


docpx\Token
===========


Token

Representation of a PHP token.



Methods
-------

__construct
===========

.. function:: __construct($token)


    Construct a new node object

    :param array $token: Token array



getValue
========

.. function:: getValue()


    Returns the value of the token

    :rtype: mixed 



isNamespace
===========

.. function:: isNamespace()


    Returns if token is a namespace.

    :rtype: boolean True | False otherwise



isNamespaceSeperator
====================

.. function:: isNamespaceSeperator()


    Returns if token is a namespace seperator.

    :rtype: boolean True | False otherwise



isDocBlock
==========

.. function:: isDocBlock()


    Returns if token is a docblock.

    :rtype: boolean True | False otherwise



isClass
=======

.. function:: isClass()


    Returns if token is a class.

    :rtype: boolean True | False otherwise



isVar
=====

.. function:: isVar()


    Returns if token is a variable.

    :rtype: boolean True | False otherwise



isString
========

.. function:: isString()


    Returns if token is a string.

    :rtype: boolean True | False otherwise



isStatic
========

.. function:: isStatic()


    Returns if token is a static declaration.

    :rtype: boolean True | False otherwise



isFunction
==========

.. function:: isFunction()


    Returns if token is a function or class method.

    :rtype: boolean True | False otherwise



isPublic
========

.. function:: isPublic()


    Returns if token is a public declaration.

    :rtype: boolean True | False otherwise



isPrivate
=========

.. function:: isPrivate()


    Returns if token is a private declaration.

    :rtype: boolean True | False otherwise



isProtected
===========

.. function:: isProtected()


    Returns if token is a protected declaration.

    :rtype: boolean True | False otherwise



isUse
=====

.. function:: isUse()


    Returns if token is a namespace use reference declaration.

    :rtype: boolean True | False otherwise



isInterface
===========

.. function:: isInterface()


    Returns if token is a interface decleration.

    :rtype: boolean True | False otherwise



isAbstract
==========

.. function:: isAbstract()


    Returns if token is a abstract decleration.

    :rtype: boolean True | False otherwise



isFinal
=======

.. function:: isFinal()


    Returns if token is a final decleration.

    :rtype: boolean True | False otherwise



isClassConst
============

.. function:: isClassConst()


    Returns if token is a class constant decleration.

    :rtype: boolean True | False otherwise



isFileConst
===========

.. function:: isFileConst()


    Returns if token is a file constant decleration.

    :rtype: boolean True | False otherwise



isOpenBracket
=============

.. function:: isOpenBracket()


    Returns if token is an opening brace.

    :rtype: boolean True | False otherwise



getLineNumber
=============

.. function:: getLineNumber()


    Returns line number the token is on wihin the source.

    :rtype: integer 



isWhitespace
============

.. function:: isWhitespace()


    Returns if token is whitespace.

    :rtype: boolean True | False otherwise



isEnscapedWhitespace
====================

.. function:: isEnscapedWhitespace()


    Returns if token is enscaped whitespace string.

    :rtype: boolean True | False otherwise



isExtends
=========

.. function:: isExtends()


    Returns if token is a class extension identifier.

    :rtype: boolean True | False otherwise



isImplements
============

.. function:: isImplements()


    Returns if token is a interface implement identifier.

    :rtype: boolean True | False otherwise



isEnscapedString
================

.. function:: isEnscapedString()


    Returns if token is a enscaped string.

    :rtype: boolean True | False otherwise



getType
=======

.. function:: getType()


    Returns the string type of the token.

    :rtype: string 





