<?php
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container with a decorative frame and optional title
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFrame extends SwatContainer {

	/**
	 * A visible name for this frame, or null
	 * @var string
	 */
	public $title = null;

	/**
	 * CSS class to use on the HTML div where the error message is displayed.
	 * @var string
	 */
	public $errormsg_class = 'swat-frame-errormsg';

	public function display() {
		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		$inner_div = new SwatHtmlTag('div');
		$inner_div->class = 'swat-frame-contents';

		$outer_div->open();

		if ($this->title != null) {
			/*
			 * Experimental: Header level is autocalculated based on the 
			 * level of the frame in the widget tree.  Top level frame
			 * is currently an <h2>.
			 */
			$level = 2;
			$ancestor = $this->parent;

			while ($ancestor != null) {
				if ($ancestor instanceof SwatFrame)
					$level++;

				$ancestor = $ancestor->parent;
			}

			echo "<h{$level}>{$this->title}</h{$level}>";
		}

		$inner_div->open();

		$this->displayErrorMessages();
		parent::display();

		$inner_div->close();
		$outer_div->close();
	}

	private function displayErrorMessages() {
		$error_messages = $this->gatherErrorMessages(false);

		if (count($error_messages) > 0) {
			$error_div = new SwatHtmlTag('div');
			$error_div->class = $this->errormsg_class;
			
			$error_div->open();

			foreach ($error_messages as &$err)
				echo $err->message, '<br />';

			$error_div->close();
		}
	}
}

?>
