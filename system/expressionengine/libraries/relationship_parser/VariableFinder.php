<?php

use EllisLab\ExpressionEngine\Library\Parser\Conditional\Lexer;

/**
 * A small helper class to interweave the results of tags and
 *
 */
class VariableFinder {

	protected $regex;
	protected $flags;
	protected $delimiter;

	public function __construct($regex, $flags = '', $delimiter = '/')
	{
		$this->regex = $regex;
		$this->flags = $flags;
		$this->delimiter = $delimiter;
	}

	/**
	 * Find tags and conditional variables that match the regex
	 * and return them in template order.
	 */
	public function find($str)
	{
		$tags = $this->findInTags($str);
		$vars = $this->findInConditionals($str);

		$result = array_merge($tags, $vars);

		usort($result, function($a, $b) {
			return $a[1] - $b[1];
		});

		return $result;
	}

	/**
	 * Match regex wrapped in { ... }.
	 */
	public function findInTags($str)
	{
		$regex = $this->wrapRegex(LD, RD);

		if ( ! preg_match_all($regex, $str, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE))
		{
			return array();
		}

		$tags = array();

		foreach ($matches as $match)
		{
			$offset = $match[0][1];
			$newmatch = array_map('array_shift', $match);
			$tags[] = array($newmatch, $offset, 'tag');
		}

		return $tags;
	}

	/**
	 * Match regex on conditional variables
	 */
	public function findInConditionals($str)
	{
		$lexer = new Lexer();
		$regex = $this->wrapRegex('^', '$');

		$str = preg_replace('/{!--(.*?)--}/', '', $str);

		// Get the token stream
		$tokens = $lexer->tokenize($str);
		$variables = array();

		foreach ($tokens as $token)
		{
			if ($token->type != 'VARIABLE' || ! preg_match($regex, $token, $match))
			{
				continue;
			}

			$variables[] = array($match, $token->lineno, 'conditional');
		}

		return $this->lineToCharacterOffsets($variables, $str);
	}

	/**
	 * Convert the lexer's line offsets to character offsets
	 */
	protected function lineToCharacterOffsets($variables, $str)
	{
		$offset = 0;
		$line = 0;

		foreach ($variables as &$variable)
		{
			$line = $variable[1] - $line;

			for ($i = 0; $i < $line - 1; $i++)
			{
				if ($str[$offset] == "\n")
				{
					$offset += 1;
				}
				else
				{
					$offset += strcspn($str, "\n", $offset) + 1;
				}
			}

			$variable[1] = strpos($str, $variable[0][0], $offset);
		}

		return $variables;
	}

	/**
	 * Wrap the regular expression in a beginning and an end and then
	 * add the delimiter and flags.
	 */
	protected function wrapRegex($before, $after)
	{
		return $this->delimiter.$before.$this->regex.$after.$this->delimiter.$this->flags;
	}
}