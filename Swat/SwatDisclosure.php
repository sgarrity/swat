<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A container to show and hide child widgets
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDisclosure extends SwatContainer {

	/**
	 * A visible title for the label shown beside the disclosure triangle
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * The initial state of the disclosure
	 *
	 * @var bool
	 */
	public $open = true;

	/**
	 * Initializes this disclosure container
	 *
	 * Disclosure containers need to have id's set.
	 */
	public function init()
	{
		// an id is required for this widget.
		$this->generateAutoId();
	}

	/**
	 * Displays this disclosure container
	 *
	 * Creates appropriate divs and outputs closed or opened based on the
	 * initial state.
	 */
	public function display()
	{
		$this->displayJavascript();

		$control_div = new SwatHtmlTag('div');
		$control_div->class = 'swat-disclosure-control';

		$control_div->open();

		$anchor = new SwatHtmlTag('a');
		$anchor->href =
			sprintf("javascript:toggleDisclosureWidget('%s');", $this->id);

		$anchor->open();

		$img = new SwatHtmlTag('img');
	
		if ($this->open) {
			$img->src = 'swat/images/disclosure-open.png';
			$img->alt = 'close';
		} else {
			$img->src = 'swat/images/disclosure-closed.png';
			$img->alt = 'open';
		}

		$img->width = 16;
		$img->height = 16;
		$img->id = $this->id.'_img';

		$img->display();

		if ($this->title !== null)
			echo $this->title;

		$anchor->close();
		$control_div->close();

		$container_div = new SwatHtmlTag('div');
		$container_div->id = $this->id;

		if ($this->open)
			$container_div->class = 'swat-disclosure-container-opened';
		else
			$container_div->class = 'swat-disclosure-container-closed';

		$container_div->open();
		parent::display();
		$container_div->close();
	}

	/**
	 * Outputs disclosure specific javascript
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		include('Swat/javascript/swat-disclosure.js');
		echo '</script>';
	}
}

?>
