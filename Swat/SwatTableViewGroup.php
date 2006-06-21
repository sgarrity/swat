<?php

require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A visible grouping of rows in a table view
 *
 * This is a table view column that gets its own row. It usually makes sense
 * to place it before other table view columns as it is always displayed on a
 * row by itself and never mixed with other columns. This special column is
 * only displayed when the value of the group_by field changes; it is not
 * displayed once for every row.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewGroup extends SwatTableViewColumn
{
	// {{{ public properties

	/**
	 * The field of the table store to group rows by
	 *
	 * @var string
	 */
	public $group_by = null;

	// }}}
	// {{{ private properties

	/**
	 * The current value of the group_by field of the table model for the
	 * grouping header
	 *
	 * This value is used so that this grouping header is not displayed for
	 * every row. The grouping header is only displayed when the value of the
	 * current table model row changes.
	 *
	 * @var mixed
	 */
	private $header_current = null;

	/**
	 * The current value of the group_by field of the table model for the
	 * grouping footer 
	 *
	 * This value is used so that this grouping footer is not displayed for
	 * every row. The grouping footer is only displayed when the value of the
	 * next table model row changes.
	 *
	 * @var mixed
	 */
	private $footer_current = null;

	// }}}
	// {{{ public function displayFooter()

	/**
	 * Displays the grouping footer of this table-view group
	 *
	 * The grouping footer is displayed when the group_by field is different
	 * between the given rows.
	 *
	 * @param mixed $row a data object containing the data for the first row in
	 *                    in the table store for this group.
	 * @param mixed $row a data object containing the data for the current row
	 *                    being displayed in the table-view.
	 * @param mixed $next_row a data object containing the data for the next
	 *                         row being displayed in the table-view or null if
	 *                         the current row is the last row.
	 */
	public function displayFooter($row, $next_row)
	{
		$group_by = $this->group_by;

		if ($next_row === null || $next_row->$group_by !== $row->$group_by)
			$this->displayGroupFooter($row);
	}

	// }}}
	// {{{ protected function displayGroupHeader()

	/**
	 * Displays the group header for this grouping column
	 *
	 * The grouping header is displayed at the beginning of a group.
	 *
	 * @param mixed $row a data object containing the data for the first row in
	 *                    in the table store for this group.
	 */
	protected function displayGroupHeader($row)
	{
		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();

		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('th', $first_renderer->getTdAttributes());
		$td_tag->colspan = $this->view->getVisibleColumnCount();
		$td_tag->class = 'swat-table-view-group';
		$td_tag->open();
		$this->displayRenderersInternal($row);
		$td_tag->close();
		$tr_tag->close();
	}

	// }}}
	// {{{ protected function displayGroupFooter()

	/**
	 * Displays the group footer for this grouping column
	 *
	 * The grouping footer is displayed at the end of a group. By default, no
	 * footer is displayed. Subclasses may display a grouping footer by
	 * overriding this method.
	 *
	 * @param mixed $row a data object containing the data for the last row in
	 *                    in the table store for this group.
	 */
	protected function displayGroupFooter($row)
	{
	}

	// }}}
	// {{{ protected function displayRenderers()
	
	/**
	 * Displays the renderers for this column
	 *
	 * The renderes are only displayed once for every time the value of the
	 * group_by field changes and the renderers are displayed on their own
	 * separate table row.
	 *
	 * @param mixed $row a data object containing the data for a single row
	 *                    in the table store for this group.
	 *
	 * @throws SwatException
	 */
	protected function displayRenderers($row)
	{
		if ($this->group_by === null)
			throw new SwatException("Attribute 'group_by' must be set.");

		$group_by = $this->group_by;

		// only display the group header if the value of the group-by field has
		// changed
		if ($row->$group_by !== $this->header_current) {
			$this->header_current = $row->$group_by;
			$this->displayGroupHeader($row);
		}
	}

	// }}}
	// {{{ protected function displayRenderersInternal()

	protected function displayRenderersInternal($row)
	{
		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}	
	}

	// }}}
}

?>
