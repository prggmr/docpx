<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */


/**
 * Token
 *
 * Representation of a PHP token.
 */
class Token {

    /**
     * Token array
     *
     * @var  array
     */
    public $_token = null;

    /**
     * Construct a new node object
     *
     * @param  array  $token  Token array
     */
    public function __construct(array $token)
    {
        $this->_token = $token;
    }

    /**
     * Returns the value of the token
     *
     * @return  mixed
     */
    public function getValue(/* ... */)
    {
        return $this->_token[1];
    }

    /**
     * Returns if token is a namespace.
     *
     * @return  boolean  True | False otherwise
     */
    public function isNamespace(/* ... */) {
        return $this->_token[0] === T_NAMESPACE;
    }

    /**
     * Returns if token is a namespace seperator.
     *
     * @return  boolean  True | False otherwise
     */
    public function isNamespaceSeperator(/* ... */)
    {
        return $this->_token[0] === T_NS_SEPARATOR;
    }

    /**
     * Returns if token is a docblock.
     *
     * @return  boolean  True | False otherwise
     */
    public function isDocBlock(/* ... */)
    {
        return $this->_token[0] === T_DOC_COMMENT;
    }

    /**
     * Returns if token is a class.
     *
     * @return  boolean  True | False otherwise
     */
    public function isClass(/* ... */)
    {
        return $this->_token[0] === T_CLASS;
    }

    /**
     * Returns if token is a variable.
     *
     * @return  boolean  True | False otherwise
     */
    public function isVar(/* ... */)
    {
        return $this->_token[0] === T_VARIABLE;
    }

    /**
     * Returns if token is a string.
     *
     * @return  boolean  True | False otherwise
     */
    public function isString(/* ... */)
    {
        return $this->_token[0] === T_STRING;
    }

    /**
     * Returns if token is a static declaration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isStatic(/* ... */)
    {
        return $this->_token[0] === T_STATIC;
    }

    /**
     * Returns if token is a function or class method.
     *
     * @return  boolean  True | False otherwise
     */
    public function isFunction(/* ... */)
    {
        return $this->_token[0] === T_FUNCTION;
    }

    /**
     * Returns if token is a public declaration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isPublic(/* ... */)
    {
        return $this->_token[0] === T_PUBLIC;
    }

    /**
     * Returns if token is a private declaration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isPrivate(/* ... */)
    {
        return $this->_token[0] === T_PRIVATE;
    }

    /**
     * Returns if token is a protected declaration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isProtected(/* ... */)
    {
        return $this->_token[0] === T_PROTECTED;
    }

    /**
     * Returns if token is a namespace use reference declaration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isUse(/* ... */)
    {
        return $this->_token[0] === T_USE;
    }

    /**
     * Returns if token is a interface decleration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isInterface(/* ... */)
    {
        return $this->_token[0] === T_INTERFACE;
    }

    /**
     * Returns if token is a abstract decleration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isAbstract(/* ... */)
    {
        return $this->_token[0] === T_ABSTRACT;
    }

    /**
     * Returns if token is a final decleration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isFinal(/* ... */)
    {
        return $this->_token[0] === T_FINAL;
    }

    /**
     * Returns if token is a class constant decleration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isClassConst(/* ... */)
    {
        return $this->_token[0] === T_CONST;
    }

    /**
     * Returns if token is a file constant decleration.
     *
     * @return  boolean  True | False otherwise
     */
    public function isFileConst(/* ... */)
    {
        return $this->_token[1] === 'define';
    }

    /**
     * Returns if token is an opening brace.
     *
     * @return  boolean  True | False otherwise
     */
    public function isOpenBracket(/* ... */)
    {
        return $this->_token[0] === T_CURLY_OPEN;
    }

    /**
     * Returns line number the token is on wihin the source.
     *
     * @return  integer
     */
    public function getLineNumber(/* ... */)
    {
        return $this->_token[2];
    }

    /**
     * Returns if token is whitespace.
     *
     * @return  boolean  True | False otherwise
     */
    public function isWhitespace(/* ... */)
    {
        return $this->_token[0] === T_WHITESPACE;
    }

    /**
     * Returns if token is enscaped whitespace string.
     *
     * @return  boolean  True | False otherwise
     */
    public function isEnscapedWhitespace(/* ... */)
    {
        return $this->_token[0] === T_ENCAPSED_AND_WHITESPACE;
    }

    /**
     * Returns if token is a class extension identifier.
     *
     * @return  boolean  True | False otherwise
     */
    public function isExtends(/* ... */)
    {
        return $this->_token[0] === T_EXTENDS;
    }

    /**
     * Returns if token is a interface implement identifier.
     *
     * @return  boolean  True | False otherwise
     */
    public function isImplements(/* ... */)
    {
        return $this->_token[0] === T_IMPLEMENTS;
    }
    
    /**
     * Returns if token is a enscaped string.
     *
     * @return  boolean  True | False otherwise
     */
    public function isEnscapedString(/* ... */)
    {
        return $this->_token[0] === T_CONSTANT_ENCAPSED_STRING;
    }

    /**
     * Returns the string type of the token.
     *
     * @return  string
     */
    public function getType(/* ... */)
    {
        return token_name($this->_token[0]);
    }
}
