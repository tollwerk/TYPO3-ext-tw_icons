# tollwerk SVG-Icons for TYPO3

[![TYPO3](https://img.shields.io/badge/TYPO3-13-green.svg)](https://get.typo3.org/version/12)
[![TYPO3](https://img.shields.io/badge/License-GPL%203%20or%20later-lightgray.svg)](https://get.typo3.org/version/12)

Provides a ViewHelper to render SVG-Icons from a configurable source folder.

## Installation

1. Install the extension with composer.
    ```
    composer require tollwerk/tw-icons
    ```

2. Include the TypoScript template "Tollwerk SVG-Icons" into your root TypoScript template.

3. Set `plugin.tx_twicons.settings.iconRootPath` to the public folder where your SVG-Icons are stored.

## Usage

Use the following ViewHelper to render Icons.

```html
<!-- Will use the file "MyIconName.svg" and render that icon. -->
<twicons:icon icon="MyIconName" />
```

### ViewHelper arguments

**debug** bool, optional, default = `true`
If true and there is no file for the given icon (see argument "icon", below), a HTML comment like `<!-- Unknown SVG icon "IconName.svg" -->` will be returned instead.

**icon** string, required
Name of the SVG icon file found inside the folder defined by `plugin.tx_twicons.settings.iconRootPath`. So "Web" will be resolved to "Web.svg" or "web.svg".


