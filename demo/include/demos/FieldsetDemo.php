<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Demo.php';

/**
 * A demo using fieldsets
 *
 * @package   SwatDemo
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FieldsetDemo extends Demo
{
	// {{{ public function buildDemoUI()

	public function buildDemoUI(SwatUI $ui)
	{
		$radiolist = $ui->getWidget('radio_list');
		$radiolist->addOptionsByArray(array(
			0 => 'Apple',
			1 => 'Orange',
			2 => 'Banana',
			3 => 'Pear',
			4 => 'Pineapple',
			5 => 'Kiwi',
			6 => 'Tangerine',
			7 => 'Grapefruit',
			8 => 'Strawberry'));
	}

	// }}}
}

?>
