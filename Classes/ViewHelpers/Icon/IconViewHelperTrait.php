<?php

/**
 * Icon ViewHelperTrait
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwIcons
 * @subpackage Tollwerk\TwIcons\ViewHelpers\Icon
 * @author     tollwerk GmbH <info@tollwerk.de>
 * @copyright  2023 tollwerk GmbH <info@tollwerk.de>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 * @link       https://tollwerk.de
 */

namespace Tollwerk\TwIcons\ViewHelpers\Icon;

use OutOfBoundsException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Shared methods for icon viewhelpers
 *
 * @category   Tollwerk\TwIcons\ViewHelpers\Icon
 * @package    Tollwerk\TwIcions
 * @subpackage Tollwerk\TwIcions\ViewHelpers
 * @author     Tollwerk GmbH <info@tollwerk.de>
 * @license    MIT https://opensource.org/licenses/MIT
 * @link       https://tollwerk.de
 */
trait IconViewHelperTrait
{
    /**
     * Icon root paths
     *
     * @var string[]|null
     */
    protected static $iconRootPaths = null;

    /**
     * Find an icon and return the absolute icon path
     *
     * @param string $icon Icon name
     *
     * @return string Icon file path
     * @throws Exception
     * @throws InvalidConfigurationTypeException
     */
    protected function getIconFile(string $icon): string
    {
        // Search for the icon in the given icon root path order
        foreach ($this->getIconRootPaths() as $iconRootPath) {
            $iconFile = GeneralUtility::getFileAbsFileName($iconRootPath . $icon);
            if (is_file($iconFile)) {
                return $iconFile;
            }
        }

        throw new OutOfBoundsException($icon, 1549185715);
    }

    /**
     * Return the list of icon root paths
     *
     * @return string[] Icon root paths
     * @throws InvalidConfigurationTypeException
     * @throws Exception
     */
    protected function getIconRootPaths(): array
    {
        if (self::$iconRootPaths === null) {
            self::$iconRootPaths  = [];
            $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
            $settings             = $configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
                'TwIcons'
            );

            self::$iconRootPaths  = array_map(
                function ($rootPath) {
                    return rtrim($rootPath, '/') . '/';
                },
                GeneralUtility::trimExplode(',', $settings['iconRootPath'], true)
            );
        }
        return self::$iconRootPaths;
    }
}
