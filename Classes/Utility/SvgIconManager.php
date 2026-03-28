<?php

/**
 * Icon ViewHelper
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwIcons
 * @subpackage Tollwerk\TwIcons\Utility
 * @author     tollwerk GmbH <info@tollwerk.de>
 * @copyright  2023 tollwerk GmbH <info@tollwerk.de>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 * @link       https://tollwerk.de
 */

namespace Tollwerk\TwIcons\Utility;

use DOMDocument;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * SVG sprite manager
 *
 * @category   Tollwerk\TwIcons\Utility
 * @package    Tollwerk\TwIcons
 * @subpackage Tollwerk\TwIcons\ViewHelper
 * @author     Tollwerk GmbH <info@tollwerk.de>
 * @license    MIT https://opensource.org/licenses/MIT
 * @link       https://tollwerk.de
 */
class SvgIconManager implements SingletonInterface
{
    /**
     * SVG source files
     *
     * @var array
     */
    protected static $sources = [];
    /**
     * SVG use snippets
     *
     * @var array
     */
    protected static $uses = [];

    /**
     * Register a SVG source for sprite output and return the use reference key
     *
     * @param string $svgSource SVG source file path
     *
     * @return string|null Use reference key
     */
    public static function useIconReference(string $svgSource): ?string
    {
        return self::useIcon($svgSource) ? self::getUseKey($svgSource) : null;
    }

    /**
     * Return an unique use key for the given SVG source
     *
     * @param string $svgSource SVG source file path
     *
     * @return DOMDocument SVG usage
     */
    public static function useIcon(string $svgSource): DOMDocument
    {
        // Create a unique use key
        if (empty(self::$uses[$svgSource])) {
            self::$uses[$svgSource] = self::getUseSource($svgSource, self::getUseKey($svgSource));
        }

        return self::$uses[$svgSource];
    }

    /**
     * Prepare and return a single sprite SVG
     *
     * @param string $filename Filename
     * @param string $useKey   Use key
     *
     * @return string Sprite SVG
     */
    protected static function getUseSource(string $filename, string $useKey): DOMDocument
    {
        $width  = $height = 0;
        $svgDom = self::getIcon($filename, $width, $height);
        $svgDom->documentElement->setAttribute('id', $useKey);
        self::$sources[$filename] = $svgDom->saveXML($svgDom->documentElement);
        $svgUse                   = new DOMDocument();
        $svgUse->loadXML('<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" xmlns:xlink="http://www.w3.org/1999/xlink"><use xlink:href="#' . $useKey . '" /></svg>');

        return $svgUse;
    }

    /**
     * Open, pre-process and return an SVG icon
     *
     * @param string $filename Filename
     * @param int    $width    Icon width
     * @param int    $height   Icon height
     *
     * @return DOMDocument Icon DOM
     */
    public static function getIcon(string $filename, int &$width = 0, int &$height = 0): DOMDocument
    {
        $svgDom = new DOMDocument();
        $svgDom->load($filename);

        // Create the viewBox
        if ($svgDom->documentElement->hasAttribute('viewBox')) {
            list(, , $width, $height) = preg_split('/\s+/', trim($svgDom->documentElement->getAttribute('viewBox')));
        }

        if (!$svgDom->documentElement->hasAttribute('viewBox')) {
            $width  = intval($svgDom->documentElement->getAttribute('width'));
            $height = intval($svgDom->documentElement->getAttribute('height'));
            if ($width && $height) {
                $svgDom->documentElement->setAttribute('viewBox', "0 0 $width $height");
            }
        }

        $svgDom->documentElement->setAttribute('width', $width);
        $svgDom->documentElement->setAttribute('height', $height);

        return $svgDom;
    }

    /**
     * Create and return a unique use reference hash for a SVG file
     *
     * @param string $svgSource SVG source file path
     *
     * @return string Use reference key
     */
    protected static function getUseKey(string $svgSource): string
    {
        return strtolower(pathinfo($svgSource, PATHINFO_FILENAME))
               . substr(md5_file($svgSource), 0, 8);
    }

    /**
     * Return an SVG sprite
     *
     * @return string|null SVG sprite
     */
    public static function getSprite(): ?string
    {
        return count(self::$sources) ? '<!-- Auto-injected SVG sprite --><svg style="position:absolute!important;overflow:hidden!important;clip:rect(0 0 0 0)!important;height:1px!important;width:1px!important;margin:-1px!important;padding:0!important;border:0!important" aria-hidden="true">' . implode(self::$sources) . '</svg>' : null;
    }
}
