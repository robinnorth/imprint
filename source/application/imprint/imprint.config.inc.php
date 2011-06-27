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
 * @version 1.1.1
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
				'allowed_dimensions'		=>	Array(
					'200x200',
					'600x600'
				)
			),
			'phpthumb'	=>	Array(
				'temp_directory'			=>	'/tmp/persistent/phpthumb/cache/',
				'nohotlink_valid_domains'	=>	Array( @$_SERVER['HTTP_HOST'], 'stage.phenotype.net' )
			)
		);
		
		// Imprint web application debug switch: DISABLE FOR PRODUCTION SITES
		$debug = false;
?>