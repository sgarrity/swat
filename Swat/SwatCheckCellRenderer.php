<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A renderer for a boolean value
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckCellRenderer extends SwatCellRenderer
{

	/**
	 * Value of cell
	 *
	 * The boolean value to display in this cell.
	 * @var boolean
	 */
	public $value;

	public function render($prefix)
	{
		if ((boolean)$this->value) {
			$image_tag = new SwatHtmlTag('img');
			$image_tag->src = 'swat/images/check.png';
			$image_tag->alt = _S('Yes');
			$image_tag->height = '14';
			$image_tag->width = '14';
			$image_tag->display();
		} else {
			echo '&nbsp;';
		}
	}

	public function getTdAttribs()
	{
		return array('style' => 'text-align: center;');
	}
}

?>
