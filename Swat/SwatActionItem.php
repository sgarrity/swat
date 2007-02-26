<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A single entry in a SwatActions widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatActions
 */
class SwatActionItem extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * A unique identifier for this action item
	 *
	 * @var string
	 */
	public $id;

	/**
	 * A human readable title displayed for this item
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * A SwatWidget associated with this action item
	 *
	 * @var SwatWidget
	 */
	public $widget = null;

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this action item
	 *
	 * This initializes the widget contained in this action item if there is
	 * one.
	 */
	public function init()
	{
		parent::init();

		if ($this->widget !== null)
			$this->widget->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this item
	 *
	 * Calls this item's widget display method.
	 */
	public function display()
	{
		$this->widget->display();
	}

	// }}}
	// {{{ public function setWidget()

	/**
	 * Sets the widget to use for this item
	 *
	 * Each SwatActionItem can have one associated SwatWidget. This method
	 * sets the widget for this item.
	 *
	 * @param SwatWidget $widget the widget associated with this action.
	 *
	 * @throws SwatException
	 */
	public function setWidget(SwatWidget $widget)
	{
		if ($this->widget != null)
			throw new SwatException('SwatUI: Only one widget can be nested '.
				'within an SwatActionItem');

		$this->widget = $widget;
		$widget->parent = $this;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To set the a widget in an action item, use 
	 * {@link SwatActionItem::setWidget()}.
	 *
	 * @param SwatWidget $child a reference to a child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent
	 * @see SwatActionItem::setWidget()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatWidget)
			$this->setWidget($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatWidget objects may be nested within a '.
				'SwatActionItem object.', 0, $child);
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this action item
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this action item.
	 *
	 * @see SwatWidget::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		if ($this->widget !== null)
			$set->addEntrySet($this->widget->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
}

?>
