<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\Parser\ParserException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.0.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.9
 */
abstract class PHPParserVersion80 extends PHPParserVersion74
{
    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param integer $tokenType The type of a parsed token.
     *
     * @return boolean
     */
    protected function isClassName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_DIR:
            case Tokens::T_USE:
            case Tokens::T_GOTO:
            case Tokens::T_NULL:
            case Tokens::T_NS_C:
            case Tokens::T_TRUE:
            case Tokens::T_CLONE:
            case Tokens::T_FALSE:
            case Tokens::T_TRAIT:
            case Tokens::T_STRING:
            case Tokens::T_TRAIT_C:
            case Tokens::T_CALLABLE:
            case Tokens::T_INSTEADOF:
            case Tokens::T_NAMESPACE:
                return true;
        }

        return false;
    }

    /**
     * This method will be called when the base parser cannot handle an expression
     * in the base version. In this method you can implement version specific
     * expressions.
     *
     * @return ASTNode
     * @throws UnexpectedTokenException
     */
    protected function parseOptionalExpressionForVersion()
    {
        return $this->parseExpressionVersion80()
            ?: parent::parseOptionalExpressionForVersion();
    }

    /**
     * In this method we implement parsing of PHP 8.0 specific expressions.
     *
     * @return ASTNode
     */
    protected function parseExpressionVersion80()
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        switch ($nextTokenType) {
        }

        return null;
    }

    /**
     * This method parses a function postfix expression. An object of type
     * {@link ASTFunctionPostfix} represents any valid php
     * function call.
     *
     * This method will delegate the call to another method that returns a
     * member primary prefix object when the function postfix expression is
     * followed by an object operator.
     *
     * @param  ASTNode $node This node represents the function
     *        identifier. An identifier can be a static string, a variable, a
     *        compound variable or any other valid php function identifier.
     * @return ASTNode
     * @throws ParserException
     */
    protected function parseFunctionPostfix(ASTNode $node)
    {
        if ($node instanceof ASTIdentifier && $node->getImage() === 'match') {
            $image = $this->extractPostfixImage($node);

            $function = $this->builder->buildAstFunctionPostfix($image);
            $function->addChild($node);

            $this->consumeComments();

            $this->tokenStack->push();

            $function->addChild(
                $this->parseArgumentsParenthesesContent(
                    $this->builder->buildAstMatchArgument()
                )
            );

            return $function;
        }

        return parent::parseFunctionPostfix($node);
    }
}
