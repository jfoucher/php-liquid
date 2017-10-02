<?php

/*
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Liquid\Tag;

use Liquid\AbstractTag;
use Liquid\Liquid;
use Liquid\LiquidException;
use Liquid\FileSystem;
use Liquid\Regexp;
use Liquid\Context;
use Liquid\Variable;

/**
 * Performs an assignment of one variable to another
 *
 * Example:
 *
 *     {% assign var = var %}
 *     {% assign var = "hello" | upcase %}
 */
class TagAssign extends AbstractTag
{
	/**
	 * @var string The variable to assign from
	 */
	private $from;

	/**
	 * @var string The variable to assign to
	 */
	private $to;

	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param FileSystem $fileSystem
	 *
	 * @throws \Liquid\LiquidException
	 */
	public function __construct($markup, array &$tokens, FileSystem $fileSystem = null)
	{
        $syntaxRegexp = new Regexp('/(\w+)\s*=\s*(.*)\s*/');

        if ($syntaxRegexp->match($markup)) {
            $this->to = $syntaxRegexp->matches[1];
            $this->from = new Variable($syntaxRegexp->matches[2]);
        } else {
            throw new LiquidException("Syntax Error in 'assign' - Valid syntax: assign [var] = [source]");
        }

	}

	/**
	 * Renders the tag
	 *
	 * @param Context $context
	 *
	 * @return string|void
	 */
	public function render(Context $context)
	{
	    if (is_object($this->from) && get_class($this->from) === 'Liquid\Variable') {
            $output = $this->from->render($context);
        } else {
            $output = $context->get($this->from);
        }

		$context->set($this->to, $output, true);
	}
}
