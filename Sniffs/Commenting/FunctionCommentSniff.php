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
 * Symfony2 standard customization to PEARs FunctionCommentSniff.
 *
 * Verifies that :
 * <ul>
 *  <li>
 *      There is a &#64;return tag if a return statement exists inside
 *      the method
 * </li>
 * </ul>
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Felix Brandt <mail@felixbrandt.de>
 * @author   Xaver Loppenstedt <xaver@loppenstedt.de>
 * @author   Steffen Ritter <steffenritter1@gmail.com>
 * @license  http://spdx.org/licenses/BSD-3-Clause BSD 3-clause "New" or "Revised" License
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Symfony_Sniffs_Commenting_FunctionCommentSniff extends PEAR_Sniffs_Commenting_FunctionCommentSniff
{

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if (false === $commentEnd = $phpcsFile->findPrevious(array(T_COMMENT, T_DOC_COMMENT, T_CLASS, T_FUNCTION, T_OPEN_TAG), ($stackPtr - 1))) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $code = $tokens[$commentEnd]['code'];

        // a comment is not required on protected/private methods
        $method = $phpcsFile->getMethodProperties($stackPtr);
        $commentRequired = 'public' == $method['scope'];

        if (($code === T_COMMENT && !$commentRequired)
            || ($code !== T_DOC_COMMENT && !$commentRequired)
        ) {
            return;
        }

        parent::process($phpcsFile, $stackPtr);
    }

    /**
     * Process the return comment of this function comment.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPtr     The position of the current token
     *                                           in the stack passed in $tokens.
     * @param int                  $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processReturn(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip constructor and destructor.
        $methodName      = $phpcsFile->getDeclarationName($stackPtr);
        $isSpecialMethod = ($methodName === '__construct' || $methodName === '__destruct');

        if ($isSpecialMethod === true) {
            return;
        }

        $return = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@return') {
                if ($return !== null) {
                    $error = 'Only 1 @return tag is allowed in a function comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateReturn');
                    return;
                }

                $return = $tag;
            }
            if (preg_match('#{@inheritdoc}#i', $tokens[$tag]['content']) === 1) {
                return;
            }
        }

        if (!isset($tokens[$stackPtr]['scope_opener'])) {
            // abstract method
            return;
        }
        $start     = $tokens[$stackPtr]['scope_opener'] + 1;
        $end       = $tokens[$stackPtr]['scope_closer'] - 1;

        $hasReturnValue = false;

        $doFind = true;
        while ($doFind) {
            $returnPtr = $phpcsFile->findNext(T_RETURN, $start, $end);
            if ($returnPtr !== false) {
                // ignore nested functions / closures
                $countClosure = count(
                    array_filter(
                        $tokens[$returnPtr]['conditions'],
                        function ($type) {
                            return ($type === T_CLOSURE);
                        }
                    )
                );

                if ($countClosure === 0) {
                    $nextPtr = $phpcsFile->findNext(T_WHITESPACE, $returnPtr + 1, $end, true);
                    if ($tokens[$nextPtr]['code'] !== T_SEMICOLON) {
                        $hasReturnValue = true;
                    }
                }
                $start = $returnPtr + 1;
            } else {
                $doFind = false;
            }
        }

        if ($return !== null) {
            if ($hasReturnValue) {
                $content = $tokens[($return + 2)]['content'];
                if (empty($content) === true || $tokens[($return + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                    $error = 'Return type missing for @return tag in function comment';
                    $phpcsFile->addError($error, $return, 'MissingReturnType');
                }
            } else {
                $phpcsFile->addError(
                    'ommit @return tag if the method does not return anything',
                    $return,
                    'OmmitReturn'
                );
            }
        } else {
            if ($hasReturnValue) {
                $error = 'Missing @return tag in function comment';
                $phpcsFile->addError($error, $tokens[$commentStart]['comment_closer'], 'MissingReturn');
            }
        }//end if

    } /* end processReturn() */
}//end class
