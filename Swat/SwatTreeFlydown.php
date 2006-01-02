<?php

require_once 'Swat/SwatTreeFlydownNode.php';
require_once 'Swat/SwatFlydown.php';

/**
 * A flydown (aka combo-box) selection widget that displays a tree of flydown
 * options
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTreeFlydown extends SwatFlydown
{
	/**
	 * An array containing the branch of the selected node formed by node
	 * values
	 *
	 * The value of this flydown is the same as the last element in this array.
	 *
	 * @var array
	 */
	public $path = array();

	/**
	 * A tree collection of {@link SwatTreeFlydownNode} objects for this
	 * tree flydown
	 *
	 * This property is used in place of the {@link SwatFlydown::$options}
	 * property. The options property is ignored.
	 *
	 * @var SwatTreeFlydownNode
	 * @see SwatTreeFlydown::getOptions()
	 */
	protected $tree = null;

	/**
	 * Displays this tree flydown
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		// temporarily encode the path into the value for parent::display()
		$actual_value = $this->value;
		$this->value = implode('/', $this->path);
		parent::display();
		$this->value = $actual_value;
	}

	/**
	 * Gets this flydown's tree as a flat array used in the
	 * {@link SwatFlydown::display()} method
	 *
	 * @return array a reference to an array of {@link SwatFlydownOption}
	 *                options.
	 */
	protected function &getOptions()
	{
		$options = array();

		foreach ($this->tree->getChildren() as $child_node)
			$this->flattenTree($options, $child_node);

		return $options;
	}

	/**
	 * Flattens this flydown's tree into an array of flydown options
	 *
	 * The tree is represented by placing spaces in front of nodes on different
	 * levels.
	 *
	 * @param array $options a reference to an array to add the flattened tree
	 *                        nodes to.
	 * @param SwatTreeFlydownNode $node the tree node to flatten.
	 * @param integer $level the current level of recursion.
	 * @param string $path the current path represented as a string of tree
	 *                      node option values separated by forward slashes.
	 */
	private function flattenTree(&$options, SwatTreeFlydownNode $node,
		$level = 0, $path = '')
	{
		$tree_option = clone $node->getFlydownOption();
		$pad = str_repeat('&nbsp;', $level * 3);
		$tree_option->title = $pad.$tree_option->title;

		if (strlen($path) > 0)
			$path.= '/'.$tree_option->value;
		else
			$path = $tree_option->value;

		$options[] = $tree_option;

		foreach($node->getChildren() as $child_node)
			$this->flattenTree($options, $child_node, $level + 1, $path);
	}

	/**
	 * Sets the tree to use for display
	 *
	 * @param SwatDataTreeNode $tree the tree to use for display.
	 */
	public function setTree(SwatTreeFlydownNode $tree)
	{
		$this->tree = $tree;
	}

	/**
	 * Processes this tree flydown
	 *
	 * Populates the path property of this flydown with the path to the node
	 * selected by the user. The widget value is set to the last id in the
	 * path array.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null) {
			$this->path = array();
		} else {
			$this->path = explode('/', $this->value);
			$this->value = end($this->path);
		}
	}
}

?>
