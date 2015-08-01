<?php

/**
 * This file is part of the Symfony2-coding-standard (phpcs standard)
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Symfony2-phpcs-authors <Symfony2-coding-standard@opensky.github.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @version  GIT: master
 * @link     https://github.com/opensky/Symfony2-coding-standard
 */

/**
 * Symfony_Sniffs_Formatting_PropertyAndMethodOrderSniff
 *
 * Throws errors if properties and methods are in wrong order.
 * Symfony coding standard specifies: "Declare class properties before methods;
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Xaver Loppenstedt <xaver@loppenstedt.de>
 * @author   Steffen Ritter <steffenritter1@gmail.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @link     http://symfony.com/doc/current/contributing/code/standards.html
 */
class Symfony_Sniffs_Formatting_PropertyAndMethodOrderSniff
    extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('PHP');

    /**
     * Current file name
     *
     * @var string
     */
    private $_currentFilename = null;

    /**
     * Function found flag
     *
     * @var bool
     */
    private $_functionFound = false;

    /**
     * Function end pointer
     *
     * @var int
     */
    private $_functionEndPtr = -1;


    /**
     * Constructs an AbstractVariableTest.
     */
    public function __construct()
    {
        $scopes = array(T_CLASS);

        $listen = array(
                   T_FUNCTION,
                   T_VARIABLE,
                  );

        parent::__construct($scopes, $listen, true);

    }//end __construct()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     * @param array                $currScope current scope opener token
     *
     * @return void
     */
    public function processTokenWithinScope(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope
    ) {
        if ($this->_currentFilename !== $phpcsFile->getFilename()) {
            $this->_currentFilename = $phpcsFile->getFilename();
            $this->_functionEndPtr  = -1;
            $this->_functionFound   = false;
        }

        if ($stackPtr < $this->_functionEndPtr) {
            return;
        } else {
            $this->_functionEndPtr = -1;
        }

        $tokens       = $phpcsFile->getTokens();
        $currentToken = $tokens[$stackPtr];

        if ($currentToken['type'] === 'T_FUNCTION') {
            $this->_functionFound = true;
            $methodProperties     = $phpcsFile->getMethodProperties($stackPtr);

            if ($methodProperties['is_abstract'] === true) {
                $this->_functionEndPtr = $phpcsFile->findNext(
                    array(T_SEMICOLON),
                    $stackPtr
                );
            } else {
                $this->_functionEndPtr = $currentToken['scope_closer'];
            }
        } else if ($this->_functionFound === true) {
            // 'T_VARIABLE'
            $phpcsFile->addError(
                'class properties must be declared before methods',
                $stackPtr,
                null,
                'DeclarePropertiesBeforeMethods'
            );
        }//end if

        return;

    }//end processTokenWithinScope()


}//end class
