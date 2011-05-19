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
 * @id Imprint application
 *
 * @desc The Imprint web application
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
	
	/* =Required libraries
	------------------------------------------------------------------- */
		include( 'imprint.config.inc.php' );
		include( 'lib/debug.inc.php' );
		
		require( 'lib/imprint.class.php' );

	/* =Application functionality
	------------------------------------------------------------------- */
		
		// Check for an image to cache
		if ( isset( $_GET['image'] ) ) {
			// Assign variable
			$image_url = $_GET['image'];
		} else {
			// End execution
			exit();
		};
		
		// Create new instance of Imprint Class
		$imprint = new Imprint( $config );

		// Create image
		$imprint->create_image( $image_url );

?>