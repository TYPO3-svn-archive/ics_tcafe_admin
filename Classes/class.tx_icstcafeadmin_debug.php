<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cite Solution <techbnique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_icstcafeadmin_debug
 *   54:     public static function error($message, $backlevel = 0)
 *   70:     public static function warning($message, $backlevel = 0)
 *   86:     public static function notice($message, $backlevel = 0)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class 'tx_icstcafeadmin_debug' for the 'ics_tcafe_admin' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_debug {
	/**
	 * Debug error
	 *
	 * @param	string		$message: The error message
	 * @param	int		$backlevel: The backlevel
	 * @return	void
	 */
	public static function error($message, $backlevel = 0) {
		$trace = debug_backtrace();
		trigger_error(
			$message .
			' in ' . $trace[1 + $backlevel]['file'] .
			' on line ' . $trace[1 + $backlevel]['line'],
			E_USER_ERROR);
	}

	/**
	 * Debug wraning
	 *
	 * @param	string		$message: The warning message
	 * @param	int		$backlevel: The backlevel
	 * @return	void
	 */
	public static function warning($message, $backlevel = 0) {
		$trace = debug_backtrace();
		trigger_error(
			$message .
			' in ' . $trace[1 + $backlevel]['file'] .
			' on line ' . $trace[1 + $backlevel]['line'],
			E_USER_WARNING);
	}

	/**
	 * Debug notice
	 *
	 * @param	string		$message: The notice message
	 * @param	int		$backlevel: The backlevel
	 * @return	void
	 */
	public static function notice($message, $backlevel = 0) {
		$trace = debug_backtrace();
		trigger_error(
			$message .
			' in ' . $trace[1 + $backlevel]['file'] .
			' on line ' . $trace[1 + $backlevel]['line'],
			E_USER_NOTICE);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/debug/class.tx_icstcafeadmin_debug.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/debug/class.tx_icstcafeadmin_debug.php']);
}

?>