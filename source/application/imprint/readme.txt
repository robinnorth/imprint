/**
 * Imprint
 *
 * @copyright (c) 2012 Robin North <robin@robinnorth.co.uk>
 * <robinnorth.co.uk>
 *
 * Licensed under the GNU GPLv2 (see license.txt)
 * Date: 24/05/2012
 *
 * @projectDescription A complete image cropping, resizing and caching implementation for
 * high-traffic *AMP web applications, based on an idea by Brett at Mr PHP
 * <http://mrphp.com.au/code/image-cache-using-phpthumb-and-modrewrite>
 *
 * @author Robin North <robin@robinnorth.co.uk>
 * @version 1.1.4
 *
 * @id Imprint Class
 *
 * @desc The base Imprint class
 *
 * Changes:
 *
 * (dd-mm-yyyy)
 *
 *--------------------------------------------------------------------
 *
 * 18-03-2011	-	1.0.0
 *
 * - Initial release
 *--------------------------------------------------------------------
 *
 * 18-05-2011	-	1.1.0
 *
 * - Added ability to delete specific size(s) of cached images
 * - Fixed issue that prevented all sizes of specific cached image
 *   from being deleted
 *--------------------------------------------------------------------
 *
 * 27-06-2011	-	1.1.1
 *
 * - Prevent fatal error when using multiple Imprint instances by only
 *   requiring phpThumb once
 *--------------------------------------------------------------------
 *
 * 24-05-2012	-	1.1.2
 *
 * - Added PHPDoc comments
 * - Added support for only specifying one key image dimension
 *   (the other dimension should be set to '0') to allow easier
 *   creation of proportionately-scaled images
 * - Refactored string cleaning
 *--------------------------------------------------------------------
 *
 * 04-09-2012	-	1.1.3
 *
 * - Prevent zoom crop being set when one image dimension is set to '0'
 *--------------------------------------------------------------------
 *
 * 29-11-2012	-	1.1.4
 *
 * - Refactoring
 *--------------------------------------------------------------------
 */

/**
 * Table of contents:
 *
 * =Requirements
 * =Installation
 * =Configuration
 * =Usage
 * =Cache management
 * =MODx Evo plugin
 *
 */

/**
 * Debug configuration
 *--------------------------------------------------------------------
 */

	/* =Requirements
	------------------------------------------------------------------- */

		To use Imprint, you will need the following:

			- Apache 2 webserver (httpd)
			- Ability to override Apache settings with .htaccess files
			- Ability to change directory permissions
			- PHP 5.x

		You may be able to run Imprint on any other webserver that allows the use of .htaccess files,
		or which can recreate the functionality of 'imprint/cache/.htaccess' using similar URL rewriting.
		I have not attempted to do so, however.

/**
 * Installation
 *--------------------------------------------------------------------
 */

To perform a standard install of Imprint on your website, copy the contents of the '/source/application'
directory to the root directory of your website. e.g. 'public_html/', 'http_docs/' or similar

You should end up with a '/imprint' directory at the root of your website.

You now need to make Imprint's cache directory writable by the webserver, so change the permissions of
imprint/cache/ to 777 (rwxrwxrwx)  by using 'chmod 777 /path/to/imprint/cache' from the SSH command line,
or use your favourite file transfer program's GUI.


/**
 * Configuration
 *--------------------------------------------------------------------
 */

Make a copy of the default Imprint configuration file found at 'imprint/imprint.default.config.inc.php'
and name it 'imprint.config.inc.php'. You should now have a 'imprint/imprint.config.inc.php' file.

Open this file in a text editor to edit the settings.

Imprint's settings are stored in a PHP array, and are divided into settings for the Imprint application
itself, and settings for the phpThumb class that is used to manipulate images.

All of the settings in this file are optional, as the defaults shown in 'imprint/imprint.default.config.inc.php'
will be used if you don't specify an override.

To create images that are different sizes to the 4 default allowed dimensions of 50px x 50px, 100px x 100px,
200px x 200px and 500px x 500px, you will need to specify the dimensions that you would like to use in your Imprint
configuration, in the 'allowed_dimensions' array. For example, to allow images that are 100px wide and 50px high to
be created, your Imprint config would look like this:

$config = Array(
	'imprint'	=>	Array(
		'allowed_dimensions'		=>	Array(
			'100x50',
		)
	)

If you have installed Imprint somewhere other than in a directory named 'imprint' at the root of your website, you
will need to specify the new path to Imprint using the 'imprint_path' setting in the Imprint configuration file. You
will also need to open the Imprint .htaccess file at 'imprint/cache/.htaccess' in a text editor and change the value of
'RewriteBase' from '/imprint/' to '/new/path/to/imprint/'.

/**
 * Usage
 *--------------------------------------------------------------------
 */

To use Imprint to manage images on your website, you should write your image URLs (as specified by the <img> 'src'
parameter) as follows:

'http://www.example.com/path/to/imprint/(width)x(height)/path/to/source/image.jpg'

So, to create and cache a 100px by 50px version of the image found at 'http://www.example.com/images/foo.jpg', you
would use the following Imprint URL (assuming that you have installed Imprint at 'http://www.example.com/imprint',
and that you have added a '100x50' entry in your 'allowed_dimensions' configuration value:

'http://www.example.com/imprint/100x50/images/foo.jpg'

If you specify '0' for either width or height, the image is proportionately resized instead of being hard-cropped.

You can specify a number of phpThumb parameters in your Imprint URLs to override the global phpThumb settings that
Imprint uses. The settings you can override are:

Setting name			:	Query string parameter

Zoom crop				:	zc
Ignore aspect ratio		:	iar
Force aspect ratio		:	far
Background				:	bg
Image format			:	f
Image quality (for JPGs):	q

The values that you can specify for each parameter are described here:
<http://phpthumb.sourceforge.net/demo/docs/phpthumb.readme.txt>

To specify a setting override, simply add it to your Imprint URL as a query string parameter. So, to change the image
format and zoom crop settings for our previous example, you would use the following Imprint URL:

'http://www.example.com/imprint/100x50/images/foo.jpg?zc=B&f=png'

/**
 * Cache management
 *--------------------------------------------------------------------
 */

Imprint is great for high-traffic websites, as it only performs image resizing and cropping once, and thereafter serves
images from its cache using a built-in Apache RewriteRule to ensure that images are served blazingly fast, without the
need to load PHP first.

However, this means that if you change the source images for your site, you will need to flush them from Imprint's cache
before Imprint will generate a new cached version of the image.

How you do this is down to you, and the way in which you choose to build your website. Included in the Imprint distribution
at 'source/utilities/flush-cache.php' is an example PHP file with code to flush the Imprint cache for either a specific source image
(specified by using the query string '?image=path/to/source/image.jpg') or for all images. I do not recommend using this
file as-is on your website, as it would allow any malicious person to repeatedly flush your Imprint cache simply by visiting the
URL of this file.

Instead, you should use the code contained within it to build your own cache-flushing mechanism, or upload this file to
a password-protected admin area of your website, so that only trusted users can flush the Imprint cache.

Also included with the Imprint distribution at 'source/utilities/imprint.modx-evo-plugin.php' is a plugin for the MODx Evo CMS,
which you can use on your production MODx Evo sites, or as a further example of how to build a cache-flushing plugin for another
CMS. This is described in the next section of this readme.


/**
 * MODx Evo plugin
 *--------------------------------------------------------------------
 */

The Imprint MODx Evo plugin allows you to integrated Imprint seamlessly with the MODx Evo CMS. It will clear the Imprint cache for
any images associated with a document via Template Variables when you save a document in the Manager, and will clear the entire
Imprint cache when the Site -> Clear Cache function is called.

To install it, create a new plugin at Elements -> Manage Elements -> Plugins -> New Plugin and paste the code from
'source/utilities/imprint.modx-evo-plugin.php' into the 'Plugin code' field. Name the plugin 'Imprint'.

On the 'Configuration' tab, paste '&imprint_path=Imprint application path (relative to MODx root);text;imprint/' into the 'Plugin
configuration' field and click the 'Update parameter display' button. If you have installed Imprint at a different location to the
default, you will need to specify the new path to the application in the text field that appears.

On the 'System Events' tab, make sure that the event 'OnSiteRefresh (in the 'Parser Service Events' section) and 'OnDocFormSave'
(in the 'Documents' section) are checked and then click the 'Save' button.
