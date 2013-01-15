.. /writer.php generated using docpx on 01/15/13 04:41pm


docpx\Writer
============


Writer

Writes out the documentation.



Methods
-------

__construct
===========

.. function:: __construct($docs)


    Prepares the writer.

    :param array $docs: Array of Doc nodes.

    :rtype: void 



write
=====

.. function:: write($path)


    Writes the output.

    :param string $path: Path to write the output.



template
========

.. function:: template($template, $vars)


    Parses a template file.

    :param string $template: Template file to parse
    :param array $vars: Variables to assign the template.

    :rtype: string Parsed template file.





