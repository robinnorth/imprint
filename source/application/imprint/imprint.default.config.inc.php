<?php
/*!
 * Imprint
 *
 * @copyright (c) 2011 Robin North - robin(at)phenotype(dot)net
 * <http://www.phenotype.net>
 *
 * Licensed under the GNU GPLv2 (see license.txt)
 * Date: 18/03/2011
 *
 * @projectDescription A complete image cropping, resizing and caching implementation for
 * high-traffic *AMP web applications, based on an idea by Brett at Mr PHP
 * <http://mrphp.com.au/code/image-cache-using-phpthumb-and-modrewrite>
 *
 * @author Robin North
 * @version 1.0.0
 *
 * @id Imprint configuration
 *
 * @desc The Imprint web application configuration file
 */
 
	/* =Edit configuration below
	------------------------------------------------------------------- */
	
		// Imprint web application configuration
		$config = Array(
			'imprint'	=>	Array(
				'site_root_path'			=>	$_SERVER['DOCUMENT_ROOT'] . '/',
				'imprint_path'				=>	'imprint/',
				'allowed_dimensions'		=>	Array(
					'50x50',
					'100x100',
					'200x200',
					'500x500'
				)
			),
			'phpthumb'	=>	Array(
				'zoom_crop'					=>	'C',
				'force_aspect_ratio'		=>	'C',
				'ignore_aspect_ratio'		=>	0,
				'quality'					=>	95,
				'background'				=>	null,
				
				'temp_directory'			=>	null, // attempt to auto-detect
				//'temp_directory'			=>	/tmp/persistent/phpthumb/cache/, // set to absolute path
				'prefer_imagemagick'		=>	true,
				'imagemagick_use_thumbnail'	=>	true,
				'imagemagick_path'			=>	'/usr/bin/convert',
				'max_source_pixels'			=>	-1,
				'nohotlink_valid_domains'	=>	Array( 
					@$_SERVER['HTTP_HOST']
				)
			)
		);
		
		// Imprint web application debug switch: DISABLE FOR PRODUCTION SITES
		$debug = false;
?>