.. /data.php generated using docpx on 01/15/13 05:02pm


docpx\\Data
***********


Data

Data object stores information using simply key/value pairs through an OO 
architecture.



Methods
=======

__construct
-----------

.. function:: __construct([$data = false])


    Registry property, information is stored as a `key` -> `value` pair.




set
---

.. function:: set($key, [$value = false, [$overwrite = true]])


    Sets a variable.
     Variables can be set using three different configurations, they can be
     set as an ordinary `$key`, `$value` pair, an array of `$key` => `$value`
     mappings which will be transversed, or finally as a "." delimited string
     in the format of `$key`, `$value` which will be transformed into an array,
     these configurations can also be combined.
    
     @param  mixed  $key  A string identifier, array of key -> value mappings,
             or a "." delimited string.
     @param  mixed  $value  Value of the `$key`.
     @param  boolean  $overwrite  Overwrite existing key if exists
    
     @return  boolean



get
---

.. function:: get($key, [$options = false])


    Returns a variable. The variable name can be
    provided as a single string of the variable or a "." delimited string
    which maps to the array tree storing this variable.

    :param string $key: A string of the variable name or a "." delimited
        string containing the route of the array tree.
    :param array $options: An array of options to use while retrieving a
        variable from the cache. Avaliable options.

        `default` - Default value to return if `$key` is not found.

        `tree` - Not Implemented

    :rtype: mixed 



has
---

.. function:: has($key)


    Returns if a variable identified by `$key` exists in the registry.
    `has` works just as `get` and allows for identical `$key`
    configuration.
    This is a mirrored shorthand of (prggmr::get($key, array('default' => false)) !== false);

    :param $key A: string of the variable name or a "." delimited
        string containing the route of the array tree.

    :rtype: boolean 





