<?php
/**
 * Parses and verifies the doc comments for classes.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ClassCommentSniff.php 301632 2010-07-28 01:57:56Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Parses and verifies the doc comments for classes.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Xaver Loppenstedt <xaver@loppenstedt.de>
 * @author    Steffen Ritter <steffenritter1@gmail.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Symfony_Sniffs_Commenting_ClassCommentSniff extends PEAR_Sniffs_Commenting_ClassCommentSniff
{
    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(
        '@category'   => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
        '@package'    => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
        '@subpackage' => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
        '@author'     => array(
            'required'       => false,
            'allow_multiple' => true,
        ),
        '@copyright'  => array(
            'required'       => false,
            'allow_multiple' => true,
        ),
        '@license'    => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
        '@version'    => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
        '@link'       => array(
            'required'       => false,
            'allow_multiple' => true,
        ),
        '@see'        => array(
            'required'       => false,
            'allow_multiple' => true,
        ),
        '@since'      => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
        '@deprecated' => array(
            'required'       => false,
            'allow_multiple' => false,
        ),
    );

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();

        $find   = PHP_CodeSniffer_Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'no');
            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'yes');
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr, 'WrongStyle');
            return;
        }

        // Check each tag.
        $this->processTags($phpcsFile, $stackPtr, $tokens[$commentEnd]['comment_opener']);
    }

    /**
     * Process the package tag.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array                $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processPackage(PHP_CodeSniffer_File $phpcsFile, array $tags)
    {
        $phpcsFile->addError('the @package annotation is not used', $tags[0], 'InvalidPackage');
    }

    /**
     * Process the package tag.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array                $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processSubPackage(PHP_CodeSniffer_File $phpcsFile, array $tags)
    {
        $phpcsFile->addError('the @subpackage annotation is not used', $tags[0], 'InvalidSubpackage');
    }
}
