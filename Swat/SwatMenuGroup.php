<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMenuItem.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A group of menu items
 *
 * SwatMenuGroup objects are added to {@link SwatGroupedMenu} objects and are
 * used to group together a set of {@link SwatMenuItem} objects.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatGroupedMenu, SwatMenuItem
 */
class SwatMenuGroup extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * The user-visible title of this group
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ protected properties

	/**
	 * The set of SwatMenuItem objects contained in this group
	 *
	 * @var array
	 */
	protected $items = array();

	// }}}
	// {{{ public function addItem()

	/**
	 * Adds a menu item to this group
	 *
	 * @param SwatMenuItem $item the item to add.
	 */
	public function addItem(SwatMenuItem $item)
	{
		$this->items[] = $item;
		$item->parent = $this;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a menu item to a menu group, use 
	 * {@link SwatMenuGroup::addItem()}.
	 *
	 * @param SwatMenuItem $child the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent, SwatUI, SwatMenuGroup::addItem()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatMenuItem)
			$this->addItem($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatMenuItem objects may be nested within a '.
				'SwatMenuGroup object.', 0, $child);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this menu group
	 *
	 * @param boolean $first optional. Whether or not this group is the first
	 *                        group in a {@link SwatGroupedMenu}. Defaults to
	 *                        false.
	 */
	public function display($first = false)
	{
		$header_tag = new SwatHtmlTag('h6');
		if ($first)
			$header_tag->class = 'first-of-type';

		$header_tag->setContent($this->title);
		$header_tag->display();

		$ul_tag = new SwatHtmlTag('ul');
		if ($first)
			$ul_tag->class = 'first-of-type';

		$ul_tag->open();

		foreach ($this->items as $item) {
			echo '<li class="yuimenuitem">';
			$item->display();
			echo '</li>';
		}

		$ul_tag->close();
	}

	// }}}
}

?>
