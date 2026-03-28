<?php

/**
 * Icon ViewHelper
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwIcons
 * @subpackage Tollwerk\TwIcons\ViewHelpers
 * @author     tollwerk GmbH <info@tollwerk.de>
 * @copyright  2023 tollwerk GmbH <info@tollwerk.de>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 * @link       https://tollwerk.de
 */

namespace Tollwerk\TwIcons\ViewHelpers;

use DOMAttr;
use DOMDocument;
use OutOfBoundsException;
use Tollwerk\TwIcons\Utility\SvgIconManager;
use Tollwerk\TwIcons\ViewHelpers\Icon\IconViewHelperTrait;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Render a single SVG icon
 *
 * @category   Tollwerk\TwIcons\ViewHelpers
 * @package    Tollwerk\TwIcons
 * @subpackage Tollwerk\TwIcons\ViewHelpers
 * @author     Tollwerk GmbH <info@tollwerk.de>
 * @license    MIT https://opensource.org/licenses/MIT
 * @link       https://tollwerk.de
 */
class IconViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Use shared icon viewhelper methods
     */
    use IconViewHelperTrait;

    /**
     * Icon types
     */
    const TYPE_INLINE = 'inline';
    const TYPE_OUTLINE = 'outline';
    const TYPE_OPAQUE = 'opaque';
    const TYPES = [self::TYPE_INLINE, self::TYPE_OUTLINE, self::TYPE_OPAQUE];
    /**
     * HTML tag name
     *
     * @var string
     */
    protected $tagName = 'svg';

    /**
     * Initialize arguments
     *
     * @api
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('icon', 'string', 'Name of the icon', true);
        $this->registerArgument('type', 'string', 'Icon type (one of inline, outline or opaque)', false, 'inline');
        $this->registerArgument('theme', 'string', 'Icon theme', false, 'default');
        $this->registerArgument('debug', 'bool', 'If true, return debug information when icon could not be found', false, true);
    }

    /**
     * Render the icon
     *
     * @return string Rendered icon
     * @throws InvalidConfigurationTypeException
     * @throws Exception
     * @api
     */
    public function render(): string
    {
        try {
            $class    = empty($this->arguments['class']) ? '' : ' ' . trim($this->arguments['class']);
            $theme    = strtolower(trim($this->arguments['theme']));
            $type     = strtolower(trim($this->arguments['type']));
            $type     = in_array($type, self::TYPES) ? $type : self::TYPE_INLINE;
            $icon     = ucfirst(pathinfo($this->arguments['icon'], PATHINFO_FILENAME)) . '.svg';
            $iconFile = $this->getIconFile($icon);

            $this->setIconProperties($this->getIconDom($iconFile));
            $this->tag->addAttribute('class', 'Icon Icon--' . $type . ' Icon--theme-' . $theme . $class);
            $this->tag->addAttribute('aria-hidden', 'true');
            $this->tag->addAttribute('focusable', 'false');
            $this->tag->forceClosingTag(true);

            return $this->tag->render();
        } catch (OutOfBoundsException $e) {
            if ($this->arguments['debug']) {
                return '<!-- Unknown SVG icon "' . $e->getMessage() . '" -->';
            }

            return '';
        }
    }

    /**
     * Set the icon properties
     *
     * @param DOMDocument $iconDom Icon dom
     *
     * @return void
     */
    protected function setIconProperties(DOMDocument $iconDom): void
    {
        /**
         *  Copy attributes
         *
         * @var DOMAttr $attribute
        */
        foreach ($iconDom->documentElement->attributes as $attribute) {
            $this->tag->addAttribute($attribute->localName, $attribute->value);
        }

        // Copy children
        $content = '';
        foreach ($iconDom->documentElement->childNodes as $child) {
            $content .= $iconDom->saveXML($child);
        }
        $this->tag->setContent($content);
    }

    /**
     * Get the icon DOM
     *
     * @param string $iconFile Icon file path
     *
     * @return DOMDocument Icon DOM
     */
    protected function getIconDom(string $iconFile): DOMDocument
    {
        return SvgIconManager::getIcon($iconFile);
    }
}
