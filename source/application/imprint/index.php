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
 * @version 1.1.4
 *
 * @id Imprint application
 *
 * @desc The Imprint web application
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
 * Required libraries
 *--------------------------------------------------------------------
 */

include( 'imprint.config.inc.php' );
include( 'lib/debug.inc.php' );

require( 'lib/imprint.class.php' );


/**
 * Application functionality
 *--------------------------------------------------------------------
 */

// Check for an image to cache
if ( isset( $_GET[ 'image' ] ) ) {
	// Assign variable
	$image_url = $_GET[ 'image' ];
} else {
	// End execution
	exit();
}

// Create new instance of Imprint Class
$imprint = new Imprint( $config );

// Create image
$imprint->create_image( $image_url );

?>