<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Sebastian Meyer <sebastian.meyer@slub-dresden.de>
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
 */

/**
 * Hooks and hacks for Goobi.Production.
 *
 * @author	Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @copyright	Copyright (c) 2011, Sebastian Meyer, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dlf
 * @access	public
 */
class tx_dlf_hacks {

	/**
	 * Hook for the __construct() method of dlf/common/class.tx_dlf_document.php
	 * When using Goobi.Production the record identifier is saved only in MODS, but not
	 * in METS. To get it anyway, we have to do some magic.
	 *
	 * @access	public
	 *
	 * @param	SimpleXMLElement		&$xml: The XML object
	 * @param	mixed		$record_id: The record identifier
	 *
	 * @return	mixed		The record identifier
	 */
	public function construct_postProcessRecordId(SimpleXMLElement &$xml, $record_id) {

		if (!$record_id) {

			$xml->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');

			if (($_divs = $xml->xpath('//mets:structMap[@TYPE="LOGICAL"]//mets:div[@DMDID]'))) {

				$_smLinks = $xml->xpath('//mets:structLink/mets:smLink');

				if ($_smLinks) {

					foreach ($_smLinks as $_smLink) {

						$_links[(string) $_smLink->attributes('http://www.w3.org/1999/xlink')->from][] = (string) $_smLink->attributes('http://www.w3.org/1999/xlink')->to;

					}

					foreach ($_divs as $_div) {

						if (!empty($_links[(string) $_div['ID']])) {

							$_id = (string) $_div['DMDID'];

							break;

						}

					}

				}

				if (empty($_id)) {

					$_id = (string) $_divs[0]['DMDID'];

				}

				$_recordId = $xml->xpath('//mets:dmdSec[@ID="'.$_id.'"]//mods:mods/mods:recordInfo/mods:recordIdentifier');

				if (!empty($_recordId[0])) {

					return $_recordId[0];

				}

			}

		}

		return $record_id;

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dlf/hooks/class.tx_dlf_hacks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dlf/hooks/class.tx_dlf_hacks.php']);
}

?>