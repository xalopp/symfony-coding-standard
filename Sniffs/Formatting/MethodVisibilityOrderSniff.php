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
 * Symfony_Sniffs_Formatting_MethodOrderSniff
 *
 * Throws errors if methods are not ordered by visibility.
 * Symfony coding standard specifies:
 * "Declare public methods first, then protected ones and finally private ones;".
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Xaver Loppenstedt <xaver@loppenstedt.de>
 * @author   Steffen Ritter <steffenritter1@gmail.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @link     http://symfony.com/doc/current/contributing/code/standards.html
 */
class Symfony_Sniffs_Formatting_MethodVisibilityOrderSniff
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
     * Function end pointer
     *
     * @var int
     */
    private $_functionEnd = -1;

    /**
     * Should be public, protected, private or null
     *
     * @var string
     */
    private $_lowestFunctionVisibility = null;

    /**
     * Allowed visibilities and order
     *
     * @var array
     */
    private $_allowedVisibilities = array(
                                     'public'    => array(
                                                     'public',
                                                     'protected',
                                                     'private',
                                                    ),
                                     'protected' => array(
                                                     'protected',
                                                     'private',
                                                    ),
                                     'private'   => array('private'),
                                    );


    /**
     * Constructs an AbstractVariableTest.
     */
    public function __construct()
    {
        $scopes = array(T_CLASS);

        $listen = array(T_FUNCTION);

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
            $this->_currentFilename          = $phpcsFile->getFilename();
            $this->_lowestFunctionVisibility = null;
            $this->_functionEnd = -1;
        }

        if ($stackPtr < $this->_functionEnd) {
            return;
        } else {
            $this->_functionEnd = -1;
        }

        $tokens       = $phpcsFile->getTokens();
        $currentToken = $tokens[$stackPtr];

        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
        $visibility       = $methodProperties['scope'];

        if ($methodProperties['is_abstract'] === true) {
            $this->_functionEnd = $phpcsFile->findNext(
                array(T_SEMICOLON),
                $stackPtr
            );
        } else {
            $this->_functionEnd = $currentToken['scope_closer'];
        }

        if ($this->_lowestFunctionVisibility === null) {
            $this->_lowestFunctionVisibility = $visibility;
        } else {
            $allowedVisibilities
                = $this->_allowedVisibilities[$this->_lowestFunctionVisibility];

            if (false === in_array($visibility, $allowedVisibilities)) {
                $phpcsFile->addError(
                    'Methods must me ordered public, protect, private',
                    $stackPtr,
                    null,
                    'OrderMethodsByVisibility'
                );
            } else {
                $this->_lowestFunctionVisibility = $visibility;
            }
        }

        return;

    }//end processTokenWithinScope()


}//end class
