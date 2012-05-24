<?php
/*!
 * Imprint
 *
 * @copyright (c) 2012 Robin North - robin(at)robinnorth(dot)co(dot)uk
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
 * @version 1.1.1
 *
 * @id Imprint debug configuration
 *
 * @desc The Imprint web application debug configuration file
 */
	
	/* =Debug configuration
	------------------------------------------------------------------- */
 
		if ( $debug ) {
			error_reporting( -1 );
			//error_reporting( E_ALL );
			ini_set( 'display_errors', 'On' );
		} else {
			error_reporting( 0 );
			ini_set( 'display_errors', 'Off' );
		}
	
?>