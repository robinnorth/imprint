<?php
/**
 * Imprint
 *
 * @copyright (c) 2012 Robin North <robin@robinnorth.co.uk>
 * <http://www.robinnorth.co.uk>
 *
 * Licensed under the GNU GPLv2 (see license.txt)
 * Date: 29/11/2012
 *
 * @projectDescription A complete image cropping, resizing and caching implementation for
 * high-traffic *AMP web applications, based on an idea by Brett at Mr PHP
 * <http://mrphp.com.au/code/image-cache-using-phpthumb-and-modrewrite>
 *
 * @author Robin North <robin@robinnorth.co.uk>
 * @version 1.0.1
 *
 * @id Imprint flush cache utility
 *
 * @desc A utility to flush the Imprint image cache
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
 * 18-05-2011	-	1.0.1
 *
 * - Updated demo to utilise new support for deleting specific image sizes
 *--------------------------------------------------------------------
 */


/**
 * Required libraries
 *--------------------------------------------------------------------
 */

include( '../application/imprint/imprint.config.inc.php' );
include( '../application/imprint/lib/debug.inc.php' );

require( '../application/imprint/lib/imprint.class.php' );


/**
 * Application functionality
 *--------------------------------------------------------------------
 */

// Check for a specific source image to flush
$image_url = ( isset( $_GET[ 'image' ] ) ) ? $_GET[ 'image' ] : null;

// Check for a specific image size to flush
$image_size = ( isset( $_GET[ 'size' ] ) ) ? array( $_GET[ 'size' ] ) : null;

// Create new instance of Imprint Class
$imprint = new Imprint( $config );

// Test flushing the cache
if ( $imprint->flush_cache( $image_url, $image_size ) )
	echo 'Cache flushed!';
else
	echo 'Cache not flushed!';

?>