<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Doc
 *
 * Doc represents a parsed PHP source document.
 */
class Doc {

    /**
     * Path to this doc objects file.
     *
     * @var  string  System path to the php source represented by this doc object
     */
    protected $_file = null;

    /**
     * Tokens generated for this doc.
     *
     * @var  object  docpx\Tokens
     */
    protected $_tokens = null;

    /**
     * Documentation data for the file being parsed.
     */
    protected $_data = null;

    /**
     * Constructs a new Doc object.
     *
     * @param  string  $file  PHP source file path
     * @param  object  $tokens docpx\Tokens
     */
    public function __construct($file, Tokens $tokens)
    {
        $this->_file = $file;
        $this->_tokens = $tokens;
    }

    /**
     * Parses the Doc tokens.
     */
    public function parse(/* ... */)
    {
        $class = false;
        $trait = false;
        $function = false;
        $data = array();
        $final = false;
        $comments = null;
        $lastdoc = null;
        $namespace = null;
        $hasFileDoc = false;
        $hasLicenseDoc = false;
        $abstract = false;
        $interface = false;
        $pubic = false;
        $protected = false;
        $private = false;

        foreach ($this->_tokens as $token) {
            logger(DOCPX_LOG)->debug($token->getType());
            // get the token type
            switch (true) {

                case $token->isFinal():
                    $final = true;
                    break;

                case $token->isNamespace():
                    while(!$this->_tokens->next()->isString()){
                    }
                    $namespace = $this->_tokens->current()->getValue();
                    while($this->_tokens->next()->isNamespaceSeperator()) {
                        $namespace .= '\\'.$this->_tokens->next()->getValue();
                    }
                    $data['namespace'] = $namespace;
                    logger(DOCPX_LOG)->info(
                        sprintf(
                            'Entering namespace %s',
                            $namespace
                        )
                    );
                break;

                case $token->isDocBlock():
                    $docblock = Docblock::comment($token->getValue());
                    $comments[] = $docblock;
                    $lastdoc = $docblock;
                    // check for a license and file doc blocks as the first
                    // encountered
                    if (HAS_LICENSE_DOC && !$hasLicenseDoc) {
                        logger(DOCPX_LOG)->info(
                            'License docblock parsed'
                        );
                        // license doc
                        $data['licensedoc'] = $docblock;
                        $hasLicenseDoc = true;
                        $lastdoc = null;
                    }
                    break;

                case $token->isFileConst() || $token->isClassConst():
                    // line number
                    $line = $token->getLineNumber();
                    $name = $this->getNextNonWhitespace()->getValue();
                    $value = $this->getNextNonWhitespace()->getValue();
                    $const = new Data(array(
                        'name' => $name,
                        'doc' => $lastdoc,
                        'value' => $value,
                        'line' => $line
                    ));
                    $lastdoc = null;
                    if ($class) {
                        if (!$class->has('const')) {
                            $class->set('const', []);
                        }
                        $class->set('const', array_merge(
                            $class->get('const'), 
                            [$const]
                        ));
                    } else {
                        if (!isset($data['const'])) {
                            $data['const'] = array();
                        }
                        $data['const'][] = $const;
                    }
                    break;

                case $token->isClass():
                case $token->isTrait():
                case $token->isInterface():
                    // are we currently inside a class?
                    if ($class) {
                        logger(DOCPX_LOG)->info(
                            sprintf(
                                "Leaving class %s",
                                $class->get('name')
                            )
                        );
                        // add this class to the classes index
                        if (!isset($data['classes'])) {
                            $data['classes'] = array();
                        }
                        $data['classes'][] = $class;
                        $class = null;
                    }
                    $name = $this->getNextNonWhitespace()->getValue();
                    logger(DOCPX_LOG)->info(
                        sprintf(
                            "Entering class %s", $name
                        )
                    );

                    $classData = array(
                        'name' => (null === $namespace) ? $name :
                            $namespace.'\\'.$name,
                        'line' => $token->getLineNumber(),
                        'doc' => $lastdoc,
                        'abstract' => $abstract,
                        'interface' => $interface,
                        'final' => $final,
                        'namespace' => $namespace,
                        'trait' => $token->isTrait(),
                        'interface' => $token->isInterface()
                    );
                    $class = new Data($classData);
                    $interface = false;
                    $abstract = false;
                    $final = false;
                    $lastdoc = null;
                    break;

                case $token->isAbstract():
                    // set flag as next parsed class or method is abstract
                    $abstract = true;
                    break;

                case $token->isInterface():
                    $interface = true;
                    break;

                case $token->isExtends():
                    if ($class) {
                        $class->set('extends', $this->getNextClassName());
                    }
                    break;

                case $token->isImplements():
                    logger(DOCPX_LOG)->info('Parsing implement declaration');
                    $interfaces = array();
                    // loop through the next set of string tokens and set as interfaces
                    $interfaces[] = $this->getNextNonWhitespace()->getValue();
                    while(true) {
                        $this->_tokens->next();
                        if ($this->_tokens->valid()) {
                            if ($this->_tokens->current()->isWhitespace()) {
                                continue;
                            } elseif ($this->_tokens->current()->isString()) {
                                $interfaces[] = $this->_tokens->current()->getValue();
                                continue;
                            } elseif ($this->_tokens->current()->isNamespaceSeperator()) {
                                // del prev as it is a namespace
                                array_pop($interfaces);
                                $name = $this->_tokens->prev()->getValue();
                                // find all namespaces in interface
                                while($this->_tokens->next()->isNamespaceSeperator()) {
                                    $name .= '\\'.$this->_tokens->next()->getValue();
                                }
                                $interfaces[] = $name;
                                continue;
                            } else {
                                $this->_tokens->prev();
                                break;
                            }
                        } else {
                            $this->_tokens->prev();
                            break;
                        }
                    }
                    if (!$class) {
                        logger(DOCPX_LOG)->warning('Class Implement found with no class');
                        continue;
                    } else { 
                        $class->set('implements', $interfaces);
                    }
                    break;

                case $token->isFunction():
                    $name = $this->getNextNonWhitespace()->getValue();
                    if ($this->isAnonymous($name)) {
                        continue;
                    }
                    // are we currently inside a function?
                    logger(DOCPX_LOG)->info(
                        sprintf(
                            "Parsing function %s",
                            $name
                        )
                    );
                    $fdata = array(
                        'name' => ($class) ? $name : (null === $namespace) ?
                            $name : $namespace . '\\' . $name,
                        'doc' => $lastdoc,
                        'public' => $public,
                        'protected' => $protected,
                        'private' => $private,
                        'abstract' => $abstract,
                        'final' => $final,
                        'namespace' => $namespace
                    );
                    $abstract = false;
                    $final = false;
                    $lastdoc = null;
                    $function = new Data($fdata);
                    // add this function to the index
                    // if in class add to methods
                    if ($class) {
                        if (!$class->has('methods')) {
                            $class->set('methods', []);
                        }
                        $class->set('methods', array_merge(
                            $class->get('methods'), 
                            [$function]
                        ));
                    } else {
                        if (!isset($data['functions'])) {
                            $data['functions'] = array();
                        }
                        $data['functions'][] = $function;
                        $function = false;
                    }
                    break;
                
                case $token->isPublic():
                    $public = true;
                    break;
                
                case $token->isProtected():
                    $protected = true;
                    break;
                
                case $token->isPrivate():
                    $private = true;
                    break;
                
                default:
                    logger(DOCPX_LOG)->debug($token->getType());
                    break;
            }
        }

        if ($class) {
            if (!isset($data['classes'])) {
                $data['classes'] = array();
            }
            $data['classes'][] = $class;
        }

        return $data;
    }

    /**
     * Returns the next non-whitespace token found.
     *
     * @return  object  docpx\Token
     */
    public function getNextNonWhitespace(/* ... */)
    {
        if (!$this->_tokens->next()->isWhitespace()) return $this->_tokens->current();
        while(true) {
            $this->_tokens->next();
            if (is_object($this->_tokens->current())) {
                if (!$this->_tokens->current()->isWhitespace()) {
                    return $this->_tokens->current();
                }
            }
        }
    }

    /**
     * Returns the next class name found.
     *
     * @return  string
     */
    public function getNextClassName(/* ... */)
    {
        $name = '';
        while(true) {
            $this->_tokens->next();
            if ($this->_tokens->valid()) {
                if ($this->_tokens->current()->isWhitespace()) {
                    continue;
                } elseif ($this->_tokens->current()->isString() || 
                          $this->_tokens->current()->isNamespaceSeperator()) {
                    $name .= $this->_tokens->current()->getValue();
                    continue;
                } else {
                    $this->_tokens->prev();
                    break;
                }
            } else {
                $this->_tokens->prev();
                break;
            }
        }
        return $name;
    }

    /**
     * Returns if the given name belongs to an anonymous function.
     *
     * @param  string  $name  Function name
     *
     * @return  boolean
     */
    public function isAnonymous($name) {
        if ($name === 'use') {
            return true;
        }
        if (stripos($name, '$') !== false) {
            return true;
        }
    }
}
