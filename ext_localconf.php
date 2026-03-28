<?php

/**
 * Local extension configuration
 *
 * @category  Tollwerk
 * @package   Tollwerk
 * @author    Tollwerk GmbH <info@tollwerk.de>
 * @copyright 2023 Tollwerk GmbH <info@tollwerk.de>
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 * @link      https://tollwerk.de
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(
    function () {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['twicons'] = ['Tollwerk\\TwIcons\\ViewHelpers'];
    }
);
