<?php

/**
 * An error in Swat
 *
 * Unlike {@link SwatException} objects, errors do not interrupt the flow of
 * execution and can not be caught. Errors in Swat have handy methods for
 * outputting nicely formed error messages and logging errors.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatError
{
	// {{{ protected properties

	/**
	 * The message of this error
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var string
	 */
	protected $message;

	/** 
	 * The severity of this error
	 *
	 * Error severity should be one of the E_* constants defined by PHP.
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var integer
	 */
	protected $severity;

	/**
	 * The file this error occurred in
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * The line this error occurred at
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var integer
	 */
	protected $line;

	/**
	 * The backtrace of this error
	 *
	 * This should be an array of the form provided by the built-in PHP
	 * function debug_backtrace().
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var array
	 */
	protected $backtrace;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new error object
	 *
	 * Error objects contain methods to display and log all types of errors
	 * that may occur.
	 *
	 * @param integer $severity the error code of this error. This should
	 *                           be one of the E_* constants set by PHP. See
	 *                           {@link
	 *                           http://php.net/manual/en/ref.errorfunc.php
	 *                           Error Handling and Logging Functions}.
	 * @param string $message the error message of this error.
	 * @param string $file the name of the file this error occurred in.
	 * @param integer $line the line number this error occurred at.
	 */
	public function __construct($severity, $message, $file, $line)
	{
		$backtrace = debug_backtrace();
		// remove this method call and the handle() call from the backtrace
		array_shift($backtrace);
		array_shift($backtrace);

		$this->message = $message;
		$this->severity = $severity;
		$this->file = $file;
		$this->line = $line;
		$this->backtrace = &$backtrace;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this error
	 *
	 * Processing involves displaying errors, logging errors and sending
	 * error message emails.
	 *
	 * If a fatal error has occured, this calls exit to ensure no further
	 * processing is done.
	 */
	public function process()
	{
		if (ini_get('display_errors')) {
			if (isset($_SERVER['REQUEST_URI']))
				echo $this->toXHTML();
			else
				echo $this->toString();
		}

		if (ini_get('log_errors'))
			$this->log();

		if ($this->severity == E_USER_ERROR)
			exit(1);
	}

	// }}}
	// {{{ public function log()

	/**
	 * Logs this error 
	 *
	 * The error is logged to the webserver error log.
	 */
	public function log()
	{
		error_log($this->getSummary(), 0);
	}

	// }}}
	// {{{ public function getSummary()

	/**
	 * Gets a one-line short text summary of this error 
	 *
	 * This summary is useful for log entries and error email titles.
	 *
	 * @return string a one-line summary of this error.
	 */
	public function getSummary()
	{
		ob_start();

		printf("%s error in file '%s' line %s",
			$this->getSeverityString(),
			$this->file,
			$this->line);

		return ob_get_clean();
	}

	// }}}
	// {{{ public function toString()

	/**
	 * Gets this error as a nicely formatted text block
	 *
	 * This is useful for text-based logs and emails.
	 *
	 * @return string this error formatted as text.
	 */
	public function toString()
	{
		ob_start();

		printf("%s Error:\n\nMessage:\n\t%s\n\n".
			"Thrown in file '%s' on line %s.\n\n",
			$this->getSeverityString(),
			$this->message,
			$this->file,
			$this->line);

		echo "Stack Trace:\n";
		$count = count($this->backtrace);

		foreach ($this->backtrace as $entry) {

			if (array_key_exists('args', $entry))
				$arguments = $this->getArguments($entry['args']);
			else
				$arguments = '';

			printf("%s. In file '%s' on line %s.\n%sMethod: %s%s%s(%s)\n",
				str_pad(--$count, 6, ' ', STR_PAD_LEFT),
				array_key_exists('file', $entry) ? $entry['file'] : 'unknown',
				array_key_exists('line', $entry) ? $entry['line'] : 'unknown',
				str_repeat(' ', 8),
				array_key_exists('class', $entry) ? $entry['class'] : '',
				array_key_exists('type', $entry) ? $entry['type'] : '',
				$entry['function'],
				$arguments);
		}

		echo "\n";

		return ob_get_clean();
	}

	// }}}
	// {{{ public function toXHTML()

	/**
	 * Gets this error as a nicely formatted XHTML fragment
	 *
	 * This is nice for debugging errors on a staging server.
	 *
	 * @return string this error formatted as XHTML.
	 */
	public function toXHTML()
	{
		ob_start();

		$this->displayStyleSheet();

		echo '<div class="swat-exception">';

		printf('<h3>%s</h3>'.
				'<div class="swat-exception-body">'.
				'Message:<div class="swat-exception-message">%s</div>'.
				'Occurred in file <strong>%s</strong> '.
				'on line <strong>%s</strong>.<br /><br />',
				$this->getSeverityString(),
				nl2br($this->message),
				$this->file,
				$this->line);

		echo 'Stack Trace:<br /><dl>';
		$count = count($this->backtrace);

		foreach ($this->backtrace as $entry) {

			if (array_key_exists('args', $entry))
				$arguments = htmlentities($this->getArguments($entry['args']),
					null, 'UTF-8');
			else
				$arguments = '';

			printf('<dt>%s.</dt><dd>In file <strong>%s</strong> '.
				'line&nbsp;<strong>%s</strong>.<br />Method: '.
				'<strong>%s%s%s(</strong>%s<strong>)</strong></dd>',
				--$count,
				array_key_exists('file', $entry) ? $entry['file'] : 'unknown',
				array_key_exists('line', $entry) ? $entry['line'] : 'unknown',
				array_key_exists('class', $entry) ? $entry['class'] : '',
				array_key_exists('type', $entry) ? $entry['type'] : '',
				$entry['function'],
				$arguments);
		}

		echo '</dl></div></div>';

		return ob_get_clean();
	}

	// }}}
	// {{{ public static function handle()

	/**
	 * Handles an error 
	 *
	 * When an error occurs, a SwatError object is created and processed.
	 *
	 * @param integer $errno the severity code of the handled error.
	 * @param string $errstr the message of the handled error.
	 * @param string $errfile the file ther handled error occurred in.
	 * @param integer $errline the line the handled error occurred at.
	 */
	public static function handle($errno, $errstr, $errfile, $errline)
	{
		$error = new SwatError($errno, $errstr, $errfile, $errline);
		$error->process();
	}

	// }}}
	// {{{ protected function getArguments()

	/**
	 * Formats a method call's arguments
	 *
	 * @param mixed an array of arguments or a single argument.
	 *
	 * @return string the arguments formatted into a comma delimited string.
	 */
	protected function getArguments($args)
	{
		if (is_array($args)) {
			foreach ($args as &$arg) {
				if (is_object($arg)) {
					$arg = '<'.get_class($arg).' object>';
				} elseif ($arg === null) {
					$arg = '<null>';
				} elseif (is_string($arg)) {
					$arg = "'".$arg."'";
				} elseif (is_array($arg)) {
					$arg = 'array('.$this->getArguments($arg).')';
				}
			}

			return implode(', ', $args);
		} else {
			return $args;
		}
	}

	// }}}
	// {{{ protected function displayStyleSheet()

	/**
	 * Displays styles required to show XHTML error messages
	 *
	 * The styles are only output once even if multiple errors are displayed
	 * during one request.
	 */
	protected function displayStyleSheet()
	{
		static $style_sheet_displayed = false;

		if (!$style_sheet_displayed) {
			echo "<style>".
				".swat-exception { border: 1px solid #d43; margin: 1em; ".
				"font-family: sans-serif; background: #fff !important; ".
				"z-index: 9999 !important; color: #000; text-align: left; ".
				"min-width: 400px; }\n";

			echo ".swat-exception h3 { background: #e65; margin: 0; padding: ".
				"border-bottom: 2px solid #d43; color: #fff; }\n";

			echo ".swat-exception-body { padding: 0.8em; }\n";
			echo ".swat-exception-message { margin-left: 2em; padding: 1em; ".
				"}\n";

			echo ".swat-exception dt { float: left; margin-left: 1em; }\n";
			echo ".swat-exception dd { margin-bottom: 1em; }\n";
			echo '</style>';
			$style_sheet_displayed = true;
		}
	}

	// }}}
	// {{{ protected function getSeverityString()

	/**
	 * Gets a string representation of this error's severity
	 *
	 * @return string a string representation of this error's severity.
	 */
	protected function getSeverityString()
	{
		static $error_types = array(
			E_WARNING         => 'Warning',
			E_NOTICE          => 'Notice',
			E_USER_ERROR      => 'User Fatal Error',
			E_USER_WARNING    => 'User Warning',
			E_USER_NOTICE     => 'User Notice',
			E_STRICT          => 'Forward Compatibility Notice'
		);

		$out = null;
		if (isset($error_types[$this->severity]))
			$out = $error_types[$this->severity];

		return $out;
	}

	// }}}
	// {{{ public static function setupHandler()

	/**
	 * Set the PHP error handler to use SwatError
	 */
	public static function setupHandler()
	{
		/*
		 * All run-time errors as specified in the error_reporting directive
		 * are handled.
		 */
		set_error_handler(array('SwatError', 'handle'), error_level());
	}

	// }}}
}

?>
