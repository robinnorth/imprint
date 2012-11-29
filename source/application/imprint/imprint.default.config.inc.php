<?php
/**
 * Imprint
 *
 * @copyright (c) 2012 Robin North <robin@robinnorth.co.uk>
 * <http://www.robinnorth.co.uk>
 *
 *
 * Licensed under the GNU GPLv2 (see license.txt)
 * Date: 29/11/2012
 *
 * @projectDescription A complete image cropping, resizing and caching implementation for
 * high-traffic *AMP web applications, based on an idea by Brett at Mr PHP
 * <http://mrphp.com.au/code/image-cache-using-phpthumb-and-modrewrite>
 *
 * @author Robin North <robin@robinnorth.co.uk>
 * @version 1.1.4
 *
 * @id Imprint configuration
 *
 * @desc The Imprint web application configuration file
 */


/**
 * Edit configuration below
 *--------------------------------------------------------------------
 */

// Imprint web application configuration
$config = array(
	'imprint' => array(
		'site_root_path' => $_SERVER[ 'DOCUMENT_ROOT' ] . '/',
		'imprint_path' => 'imprint/',
		'allowed_dimensions' => array(
			'50x50',
			'100x100',
			'200x200',
			'500x500'
		)
	),
	'phpthumb' => array(
		'zoom_crop' => 'C',
		'force_aspect_ratio' => 'C',
		'ignore_aspect_ratio' => 0,
		'quality' => 95,
		'background' => null,
		'temp_directory' => null, // attempt to auto-detect
		//'temp_directory'			=>	/tmp/persistent/phpthumb/cache/, // set to absolute path
		'prefer_imagemagick' => true,
		'imagemagick_use_thumbnail' => true,
		'imagemagick_path' => '/usr/bin/convert',
		'max_source_pixels' => -1,
		'nohotlink_valid_domains' => array(
			@$_SERVER[ 'HTTP_HOST' ]
		)
	)
);

// Imprint web application debug switch: DISABLE FOR PRODUCTION SITES
$debug = false;

?>