# tollwerk SVG-Icons for TYPO3

Provides a ViewHelper to render SVG-Icons from a configurable source folder.

## Installation

1. Install the extension with composer.
    ```
    composer require tollwerk/tw-icons
    ```

2. Add the site set "TwIcons" to your site configuration, save it and edit the site settings

3. Change "Icon Root Paths" `[twIcons.iconRootPaths]` to the public folder(s) where your SVG-Icons are stored

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
Name of the SVG icon file found inside the folder(s) defined by `twIcons.iconRootPaths`, without the file type suffix. "Web" will be resolved to "Web.svg" or "web.svg".


