<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatButton.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatActionItem.php';
require_once 'Swat/SwatActionItemDivider.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/SwatYUI.php';

/**
 * Actions widget
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatActions extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * Selected action
	 *
	 * The currently selected action item, or null.
	 *
	 * @var SwatActionItem
	 */
	public $selected = null;

	/**
	 * Show blank
	 *
	 * Whether to show an inital blank option in the flydown.
	 *
	 * @var boolean
	 */
	public $show_blank = true;

	/**
	 * Auto-reset
	 *
	 * Whether to auto reset the action flydown to the default action
	 * after processing.
	 *
	 * @var boolean
	 */
	public $auto_reset = true;

	// }}}
	// {{{ private properties

	/**
	 * A reference to an internal flydown widget that displays available
	 * actions.
	 *
	 * @var SwatFlydown
	 */
	private $action_flydown = null;

	/**
	 * A reference to an internal button that is clicked to carry out the
	 * currently selected action.
	 *
	 * @var SwatButton
	 */
	private $apply_button;

	/**
	 * The available actions for this actions selector.
	 *
	 * @var array
	 */
	private $action_items = array();

	/**
	 * The available actions for this actions selector indexed by id
	 *
	 * This array only contains actions that have a non-null id.
	 *
	 * @var array
	 */
	protected $action_items_by_id = array();


	/**
	 * An internal flag that is set to true when embedded widgets have been
	 * created
	 *
	 * @var boolean
	 *
	 * @see SwatActions::createEmbeddedWidgets()
	 */
	private $widgets_created = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new actions list
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$yui = new SwatYUI(array('dom', 'event', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-actions.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-actions.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this action item
	 *
	 * This initializes the action items contained in this actions list.
	 */
	public function init()
	{
		parent::init();

		foreach ($this->action_items as $action_item)
			$action_item->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this list of actions
	 *
	 * Internal widgets are automatically created if they do not exist.
	 * Javascript is displayed, then the display methods of the internal
	 * widgets are called.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->createEmbeddedWidgets();

		// set the flydown back to its initial state (no persistence)
		if ($this->auto_reset)
			$this->action_flydown->reset();

		// select the current action item based upon the flydown value
		if (isset($this->action_items_by_id[$this->action_flydown->value]))
			$this->selected = $this->action_items_by_id[$this->action_flydown->value];
		else
			$this->selected = null;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		echo '<div class="swat-actions-controls">';

		$label = new SwatHtmlTag('label');
		$label->for = $this->id.'_action_flydown';
		$label->setContent(sprintf('%s: ', Swat::_('Action')));
		$label->display();

		$this->action_flydown->display();
		echo ' ';
		$this->displayButton();

		echo '</div>';

		foreach ($this->action_items as $item) {
			if ($item->widget !== null) {
				$div = new SwatHtmlTag('div');

				$div->class = ($item == $this->selected) ?
					'swat-visible' : 'swat-hidden';

				$div->id = $this->id.'_'.$item->id;

				$div->open();
				$item->display();
				$div->close();
			}
		}

		echo '<div class="swat-actions-note">';
		echo Swat::_('Actions apply to checked items.');
		echo '</div>';

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Figures out what action item is selected
	 *
	 * This method creates internal widgets if they do not exist, and then
	 * determines what SwatActionItem was selected by the user by calling
	 * the process methods of the internal widgets.
	 */
	public function process()
	{
		parent::process();

		$this->createEmbeddedWidgets();

		$this->action_flydown->process();
		$selected_id = $this->action_flydown->value;

		if (isset($this->action_items_by_id[$selected_id])) {
			$this->selected = $this->action_items_by_id[$selected_id];

			if ($this->selected->widget !== null)
				$this->selected->widget->process();

		} else {
			$this->selected = null;
		}
	}

	// }}}
	// {{{ public function addActionItem()

	/**
	 * Adds an action item
	 *
	 * Adds a SwatActionItem to this SwatActions widget.
	 *
	 * @param SwatActionItem $item a reference to the item to add.
	 *
	 * @see SwatActionItem
	 */
	public function addActionItem(SwatActionItem $item)
	{
		$this->action_items[] = $item;
		$item->parent = $this;

		if ($item->id !== null)
			$this->action_items_by_id[$item->id] = $item;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add an action item to an actions object use
	 * {@link SwatActions::addActionItem()}.
	 *
	 * @param SwatActionItem $child a reference to a child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent
	 * @see SwatActions::addActionItem()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatActionItem)
			$this->addActionItem($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatActionItem objects may be nested within a '.
				'SwatAction object.', 0, $child);
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this actions list
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this actions list.
	 *
	 * @see SwatWidget::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$this->createEmbeddedWidgets();

		$set = parent::getHtmlHeadEntrySet();

		foreach ($this->action_items as $child_widget)
			$set->addEntrySet($child_widget->getHtmlHeadEntrySet());

		$set->addEntrySet($this->action_flydown->getHtmlHeadEntrySet());
		$set->addEntrySet($this->apply_button->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ public function getActionItems()

	/**
	 * Gets the array of current SwatActions
	 *
	 * @return array of SwatActionItems
	 *
	 * @see SwatActionItem
	 */
	public function getActionItems()
	{
		return $this->action_items;
	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this actions widget. If
	 *                descendant objects have identifiers, the identifier is
	 *                used as the array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if (!($class_name === null ||
			class_exists($class_name) || interface_exists($class_name)))
			return array();

		$out = array();

		foreach ($this->action_items as $action_item) {
			if ($class_name === null || $action_item instanceof $class_name) {
				if ($action_item->id === null)
					$out[] = $action_item;
				else
					$out[$action_item->id] = $action_item;
			}

			if ($action_item instanceof SwatUIParent)
				$out = array_merge($out,
					$action_item->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendant UI-object of a specific class
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant UI-object or null if no
	 *                       matching descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		foreach ($this->action_items as $action_item) {
			if ($action_item instanceof SwatUIParent) {
				$out = $action_item->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		if ($out === null) {
			foreach ($this->action_items as $action_item) {
				if ($action_item instanceof $class_name) {
					$out = $action_item;
					break;
				}
			}
		}

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this actions widget.
	 *
	 * @return array an array of UI-object states with UI-object identifiers as
	 *                array keys.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $object)
			$states[$id] = $object->getState();

		return $states;
	}

	// }}}
	// {{{ public function setDescendantStates()

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * actions widget.
	 *
	 * @param array $states an array of UI-object states with UI-object
	 *                       identifiers as array keys.
	 */
	public function setDescendantStates(array $states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $object)
			if (isset($states[$id]))
				$object->setState($states[$id]);
	}

	// }}}
	// {{{ protected function displayButton()

	/**
	 * Displays the button for this action list
	 *
	 * Subclasses may override this method to display more buttons.
	 */
	protected function displayButton()
	{
		$this->apply_button->display();
	}

	// }}}
	// {{{ protected function createEmbeddedWidgets()

	/**
	 * Creates internal widgets
	 *
	 * Widgets references are assigned to private class properties. Widgets are
	 * only created once even if this method is called multiple times.
	 */
	protected function createEmbeddedWidgets()
	{
		if ($this->widgets_created)
			return;

		$this->widgets_created = true;

		$this->action_flydown = new SwatFlydown($this->id.'_action_flydown');
		$this->action_flydown->parent = $this;
		$this->action_flydown->show_blank = $this->show_blank;

		foreach ($this->action_items as $item)
			if ($item->visible)
				if ($item instanceof SwatActionItemDivider)
					$this->action_flydown->addDivider();
				else
					$this->action_flydown->addOption($item->id, $item->title);

		$this->apply_button = new SwatButton($this->id.'_apply_button');
		$this->apply_button->parent = $this;
		$this->apply_button->setFromStock('apply');
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets inline JavaScript required to show and hide selected action items
	 *
	 * @return string inline JavaScript required to show and hide selected
	 *                 action items.
	 */
	protected function getInlineJavaScript()
	{
		static $shown = false;

		if (!$shown) {
			$javascript = $this->getInlineJavaScriptTranslations();
			$shown = true;
		} else {
			$javascript = '';
		}

		$values = array();
		if ($this->show_blank)
			$values[] = "''";

		foreach ($this->action_items as $item)
			$values[] = "'".$item->id."'";

		$selected_value = ($this->selected === null) ?
			'null' : "'".$this->selected->id."'";

		$javascript.= sprintf("var %s = new SwatActions('%s', [%s], %s);",
			$this->id,
			$this->id,
			implode(', ', $values),
			$selected_value);

		return $javascript;
	}

	// }}}
	// {{{ protected function getInlineJavaScriptTranslations()

	/**
	 * Gets translatable string resources for the JavaScript object for
	 * this widget
	 *
	 * @return string translatable JavaScript string resources for this widget.
	 */
	protected function getInlineJavaScriptTranslations()
	{
		$dismiss_text  = Swat::_('Dismiss message.');
		$select_an_action_text = Swat::_('Please select an action.');
		$select_an_item_text = Swat::_('Please select one or more items.');
		$select_an_item_and_an_action_text =
			Swat::_('Please select an action, and one or more items.');

		return sprintf(
			"SwatActions.dismiss_text = '%s';\n".
			"SwatActions.select_an_action_text = '%s';\n".
			"SwatActions.select_an_item_text = '%s';\n".
			"SwatActions.select_an_item_and_an_action_text = '%s';\n",
			$dismiss_text,
			$select_an_action_text,
			$select_an_item_text,
			$select_an_item_and_an_action_text);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this actions list
	 *
	 * @return array the array of CSS classes that are applied to this actions
	 *                list.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-actions');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
