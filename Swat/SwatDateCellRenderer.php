<?php

require_once('Swat/SwatCellRenderer.php');
require_once('Swat/SwatDate.php');

/**
 * A text renderer.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDateCellRenderer extends SwatCellRenderer {

	/**
	 * Date
	 *
	 * Can be either a Date object, or an ISO-formatted date.
	 * @var mixed
	 */
	public $date = null;

	/**
	 * Format
	 *
	 * Either a {@link SwatDate} format mask, or class constant. Class
	 * constants are preferable for sites that require translation.
	 * @var mixed
	 */
	public $format = SwatDate::DF_DATE_TIME;


	public function render($prefix) {
		if ($this->date !== null) {
			$date = new SwatDate($this->date);
			echo $date->format($this->format);
		}
	}
}

?>
