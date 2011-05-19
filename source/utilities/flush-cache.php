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
 * @id Imprint flush cache utility
 *
 * @desc A utility to flush the Imprint image cache
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
		
		// Check for an image to flush
		if ( isset( $_GET['image'] ) ) {
			// Assign variable
			$image_url = $_GET['image'];
		} else {
			// Entire cache will be flushed
			$image_url = null;
		};
		
		// Create new instance of Imprint Class
		$imprint = new Imprint( $config );

		// Test flushing the cache
		$imprint->flush_cache( $image_url );

?>