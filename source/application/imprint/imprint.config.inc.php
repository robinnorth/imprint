<?php
/**
 * Imprint
 *
 * @copyright (c) 2012 Robin North <robin@robinorth.co.uk>
 * <http://www.robinnorth.co.uk>
 *
 * Licensed under the GNU GPLv2 (see license.txt)
 * Date: 24/05/2012
 *
 * @projectDescription A complete image cropping, resizing and caching implementation for
 * high-traffic *AMP web applications, based on an idea by Brett at Mr PHP
 * <http://mrphp.com.au/code/image-cache-using-phpthumb-and-modrewrite>
 *
 * @author Robin North
 * @version 1.1.2
 *
 * @id Imprint configuration
 *
 * @desc The Imprint web application configuration file
 */

/* =Edit configuration below
  ------------------------------------------------------------------- */

// Imprint web application configuration
$config = array(
	'imprint' => array(
		'site_root_path' => $_SERVER[ 'DOCUMENT_ROOT' ] . '/',
		'allowed_dimensions' => array(
			'200x200',
			'600x600'
		)
	),
	'phpthumb' => array(
		'temp_directory' => '/tmp/persistent/phpthumb/cache/',
		'nohotlink_valid_domains' => array( @$_SERVER[ 'HTTP_HOST' ], 'stage.phenotype.net' )
	)
);

// Imprint web application debug switch: DISABLE FOR PRODUCTION SITES
$debug = false;

?>