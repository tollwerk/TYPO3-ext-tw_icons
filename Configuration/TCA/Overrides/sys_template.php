<?php

/**
 * TCA override for sys_template
 *
 * @category  Tollwerk
 * @package   Tollwerk\TwIcons
 * @author    tollwerk GmbH <info@tollwerk.de>
 * @copyright 2023 tollwerk GmbH <ifno@tollwerk.de>
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 * @link      https://tollwerk.de
 */

defined('TYPO3') or die();

call_user_func(
    function () {
        $extensionKey = 'tw_icons';

        /**
         * Default TypoScript
         */
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extensionKey,
            'Configuration/TypoScript',
            'Tollwerk SVG-Icons'
        );
    }
);
