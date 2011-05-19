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
 * @id Imprint debug configuration
 *
 * @desc The Imprint web application debug configuration file
 *
 * Changes:
 *
 * (dd-mm-yyyy)
 *
 * --------------------------------------------------------------------------
 *
 * 18-03-2011	-	1.0.0
 *
 * - Initial release
 * --------------------------------------------------------------------------
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