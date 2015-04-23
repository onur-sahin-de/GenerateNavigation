/**
 * This file is part of an ADDON for use with LEPTON Core.
 * This ADDON is released under the GNU GPL.
 * Additional license terms can be seen in the info.php of this module.
 *
 * @module          generate_navigation
 * @author          Onur Sahin, www.onur-sahin.de
 * @copyright       2015 Onur Sahin, www.onur-sahin.de
 * @link            www.onur-sahin.de/downloads/generate-navigation
 * @license         http://www.gnu.org/licenses/gpl.html
 * @license_terms   please see info.php of this module
 *
 */
$("document").ready(function() {
	// Um bei der printBootstrapNavigation()-Methode die carets nachträglich zu ermöglichen
	$("a.dropdown-toggle").append('<b class="caret"></b>');
});
