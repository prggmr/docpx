.. /utils.php generated using docpx on 01/09/13 10:17pm
.. function:: $class()



.. function:: $exception()


    Exception Processr



.. function:: milliseconds()


    Returns the current time since epox in milliseconds.

    :rtype: integer 



.. function:: microseconds()


    Returns the current time since epox in microseonds.

    :rtype: integer 



.. function:: signal_exceptions()


    Transforms PHP exceptions into a signal.
    
    The signal fired is \XPSPL\processor\Signal::GLOBAL_EXCEPTION

    :param object $exception: \Exception

    :rtype: void 



.. function:: signal_errors()


    Transforms PHP errors into a signal.
    
    The signal fired is \XPSPL\processor\Signal::GLOBAL_ERROR

    :param int $errno: 
    :param string $errstr: 
    :param string $errfile: 
    :param int $errline: 

    :rtype: void 



.. function:: bin_search()


    Performs a binary search for the given node returning the index.
    
    Logic:
    
    0 - Match
    > 0 - Move backwards
    < 0 - Move forwards

    :param mixed $needle: Needle
    :param array $haystack: Hackstack
    :param closure $compare: Comparison function

    :rtype: null|integer index, null locate failure



.. function:: $node()



.. function:: get_class_name()


    Returns the name of a class using get_class with the namespaces stripped.
    This will not work inside a class scope as get_class() a workaround for
    that is using get_class_name(get_class());

    :param object|string $object: Object or Class Name to retrieve name

    :rtype: string Name of class with namespaces stripped



