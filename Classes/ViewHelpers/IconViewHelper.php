<?php

/**
 * Icon ViewHelper
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwIcons
 * @subpackage Tollwerk\TwIcons\ViewHelpers
 * @author     tollwerk GmbH <info@tollwerk.de>
 * @copyright  2026 tollwerk GmbH <info@tollwerk.de>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 * @link       https://tollwerk.de
 */

namespace Tollwerk\TwIcons\ViewHelpers;

use DOMAttr;
use DOMDocument;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;
use Tollwerk\TwIcons\Utility\SvgIconManager;
use Tollwerk\TwIcons\ViewHelpers\Icon\IconViewHelperTrait;
use TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use Exception;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Render a single SVG icon
 */
class IconViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Icon root paths
     *
     * @var string[]|null
     */
    protected static $iconRootPaths = null;

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
     * Get Request object
     *
     * @return ServerRequestInterface|null
     */
    private function getRequest(): ServerRequestInterface|null
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        return null;
    }

    /**
     * Find an icon and return the absolute icon path
     *
     * @param string $icon Icon name
     *
     * @return string Icon file path
     * @throws Exception
     * @throws InvalidConfigurationException
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
     * @throws InvalidConfigurationException
     * @throws Exception
     */
    protected function getIconRootPaths(): array
    {
        if (self::$iconRootPaths === null) {
            self::$iconRootPaths  = [];
            $request = $this->getRequest();
            $site = $request->getAttribute('site');
            self::$iconRootPaths  = array_map(
                function ($rootPath) {
                    return rtrim($rootPath, '/') . '/';
                },
                GeneralUtility::trimExplode(',', $site->getSettings()->get('twIcons.iconRootPath'), true)
            );
        }
        return self::$iconRootPaths;
    }

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
        $this->registerArgument('icon', 'string', 'Name of the icon', true);
        $this->registerArgument('type', 'string', 'Icon type (one of inline, outline or opaque)', false, 'inline');
        $this->registerArgument('theme', 'string', 'Icon theme', false, 'default');
        $this->registerArgument('debug', 'bool', 'If true, return debug information when icon could not be found', false, true);
        $this->registerArgument('class', 'string', 'CSS class', false, '');
    }

    /**
     * Render the icon
     *
     * @return string Rendered icon
     * @throws Exception
     *
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
                return '<!-- Unknown SVG icon "' . $e->getMessage() . '". Please check site setting "twIcons.iconRootPath". -->';
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
