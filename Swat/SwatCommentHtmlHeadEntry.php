<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * Stores and outputs an HTML head entry for an XML comment
 *
 * @package   Swat
 * @copyright 2006-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCommentHtmlHeadEntry extends SwatHtmlHeadEntry
{
	// {{{ protected properties

	protected $comment;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $comment the comment of this entry.
	 */
	public function __construct($comment)
	{
		parent::__construct(md5($comment));
		$this->comment = $comment;
	}

	// }}}
	// {{{ public function display()

	public function display($uri_prefix = '', $tag = null)
	{
		// double dashes are not allowed in XML comments
		$comment = str_replace('--', '—', $this->comment);
		printf('<!-- %s -->', $comment);
	}

	// }}}
	// {{{ public function displayInline()

	public function displayInline($path)
	{
		$this->display();
	}

	// }}}
}

?>
