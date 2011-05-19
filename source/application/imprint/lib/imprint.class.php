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
 * @version 1.1.0
 *
 * @id Imprint Class
 *
 * @desc The base Imprint class
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
 *
 * 18-05-2011	-	1.1.0
 *
 * - Added ability to delete specific size(s) of cached images
 * - Fixed issue that prevented all sizes of specific cached image from being deleted
 * --------------------------------------------------------------------------
 */

class Imprint {

	/**
	 * Class properties
	 */
	
	private $config = Array(
		'imprint'	=>	Array(
			'site_root_path'			=>	null,
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
			'nohotlink_valid_domains'	=>	Array()
		)
	);
	
	private $phpthumb;
	
	/**
	 * Constructor
	 */
	 
	public function __construct( $config ) {
		
		// Extend default configuration
		$this->config['imprint'] = $this->extend_config( $this->config['imprint'], $config['imprint'] );
		$this->config['phpthumb'] = $this->extend_config( $this->config['phpthumb'], $config['phpthumb'] );
		
		// Set site root, if it hasn't been specified
		if ( !isset( $this->config['imprint']['site_root_path'] ) ) {
			$this->config['imprint']['site_root_path'] = $_SERVER['DOCUMENT_ROOT'];
		}

		/**
		 * phpThumb initialisation
		 */
		
		// Include phpThumb class
		require( $this->config['imprint']['site_root_path'] . $this->config['imprint']['imprint_path'] . 'lib/phpthumb/phpthumb.class.php' );
		
		// Create new phpThumb instance
		$this->phpthumb = new phpThumb();
		
		// Add current host to nohotlink_valid_domains, if it hasn't been specified
		if ( !in_array( @$_SERVER['HTTP_HOST'], $this->config['phpthumb']['nohotlink_valid_domains'] ) ) {
			$this->config['phpthumb']['nohotlink_valid_domains'][] = $_SERVER['HTTP_HOST'];
		}
		
		// Set maximum source image area, if it hasn't been specified
		if ( $this->config['phpthumb']['max_source_pixels'] == -1 ) {

			if ( phpthumb_functions::version_compare_replacement( phpversion(), '4.3.2', '>=' ) && !defined( 'memory_get_usage' ) && !@ini_get( 'memory_limit' ) ) {
				// PHP has no memory limit
				$this->config['phpthumb']['max_source_pixels'] = 0;
			} else {
				// calculate default max_source_pixels as 1/6 of memory limit configuration
				$this->config['phpthumb']['max_source_pixels'] = round( max( intval( ini_get( 'memory_limit' ) ), intval( get_cfg_var( 'memory_limit' ) ) ) * 1048576 / 6);
			}
		}

		// Configure instance	
		$this->phpthumb->setParameter( 'temp_directory', $this->config['phpthumb']['temp_directory'] );
		$this->phpthumb->setParameter( 'prefer_imagemagick', $this->config['phpthumb']['prefer_imagemagick'] );
		$this->phpthumb->setParameter( 'imagemagick_use_thumbnail', $this->config['phpthumb']['imagemagick_use_thumbnail'] );
		$this->phpthumb->setParameter( 'imagemagick_path', $this->config['phpthumb']['imagemagick_path'] );
		$this->phpthumb->setParameter( 'max_source_pixels', $this->config['phpthumb']['max_source_pixels'] );
		$this->phpthumb->setParameter( 'nohotlink_valid_domains', $this->config['phpthumb']['nohotlink_valid_domains'] );

	}
	
	/**
	 * create_image
	 *
	 * Creates image and caches it
	 *
	 * @param	{String}	$image_url	URL of image to create
	 *
	 * @returns
	 * @uses parse_image_url, generate_image
	 */
	public function create_image( $image_url ) {
		
		// Parse image URL to extract image attributes (including source path)
		$attributes = $this->parse_image_url( $image_url );
		
		// Check image hasn't been generated in the meantime
		if ( file_exists( $this->config['imprint']['site_root_path'] . $this->config['imprint']['imprint_path'] . 'cache/' . $image_url ) ) {
			// Redirect to the image
			$this->reload_from_cache( $image_url );
		}
		
		// Generate image and cache it
		$this->generate_image( $attributes );
	
	}
	
	/**
	 * parse_image_url
	 *
	 * Parses provided image url to extract image attributes
	 *
	 * @param	{String}	$image_url		URL of image to parse
	 *
	 * @returns	{Array}						Associative array of extracted attributes
	 */
	private function parse_image_url( $image_url ) {
		
		// Clean image URL
		$image_url = strip_tags( htmlspecialchars( $image_url ) );

		// Break URL into components
		$components = explode( '/', $image_url );

		// Get dimensions
		$dimensions = array_shift( $components );
		list( $width, $height ) = explode( 'x', $dimensions );
		
		// Get source image path
		$source_path = $this->config['imprint']['site_root_path'] . implode( '/', $components );
		
		// Get output image path
		$output_path = $this->config['imprint']['site_root_path'] . $this->config['imprint']['imprint_path'] . 'cache/' . $dimensions . '/' . implode( '/', $components );
		
		// Get output image URL
		$output_url = $image_url;
		
		// Get image format
		$extension = strlen( $source_path) - ( strrpos( $source_path, '.' ) + 1 ); // Get string length of extension
		$format = strtolower( substr( $source_path, -$extension, $extension ) );
		
		// Check for query string parameters to override defaults
		$format = ( isset( $_GET['f'] ) ) ? strip_tags( htmlspecialchars( $_GET['f'] ) ) : $format;
		$quality = ( isset( $_GET['q'] ) ) ? strip_tags( htmlspecialchars( $_GET['q'] ) ) : $this->config['phpthumb']['quality'];
		
		$zoom_crop = ( isset( $_GET['zc'] ) ) ? strip_tags( htmlspecialchars( $_GET['zc'] ) ) : $this->config['phpthumb']['zoom_crop'];
		$force_aspect_ratio = ( isset( $_GET['far'] ) ) ? strip_tags( htmlspecialchars( $_GET['far'] ) ) : $this->config['phpthumb']['force_aspect_ratio'];
		$ignore_aspect_ratio = ( isset( $_GET['iar'] ) ) ? strip_tags( htmlspecialchars( $_GET['iar'] ) ) : $this->config['phpthumb']['ignore_aspect_ratio'];
		$background = ( isset( $_GET['bg'] ) ) ? strip_tags( htmlspecialchars( $_GET['bg'] ) ) : $this->config['phpthumb']['background'];
		
		// Build attributes array
		$attributes = Array(
			'source_path'			=>	$source_path,
			'output_path'			=>	$output_path,
			'output_url'			=>	$output_url,
			'width'					=>	$width,
			'height'				=>	$height,
			'format'				=>	$format,
			'quality'				=>	$quality,
			'zoom_crop'				=>	$zoom_crop,
			'force_aspect_ratio'	=>	$force_aspect_ratio,
			'ignore_aspect_ratio'	=>	$ignore_aspect_ratio,
			'background'			=>	$background,
		);
		
		// Return extracted attributes
		return $attributes;

	}
	
	/**
	 * generatate_image
	 *
	 * Generates image using phpThumb, based on supplied parameters
	 *
	 * @param	{Array}		$parameters		Associative array of parameters to give to phpThumb
	 *
	 * @returns	{Void}						
	 * @uses
	 */
	private function generate_image( $parameters ) {

		// Check for source file existence
		if ( !file_exists( $parameters['source_path'] ) ) {
			$this->error( 'No source image at ' . $parameters['source_path'] );
		}
		
		// Check that the image dimensions are valid
		if ( !in_array( $parameters['width'] . 'x' . $parameters['height'], $this->config['imprint']['allowed_dimensions'] ) ) {
			$this->error( 'The dimensions of this image do not match any in the allowed_dimensions list' );
		}
		
		/**
		 * Set phpThumb parameters
		 */
		 
		// Source image
		$this->phpthumb->setSourceFilename( $parameters['source_path'] );
		
		// Image dimensions and format
		$this->phpthumb->setParameter( 'w', $parameters['width'] );
		$this->phpthumb->setParameter( 'h', $parameters['height'] );
		$this->phpthumb->setParameter( 'f', $parameters['format'] );
		$this->phpthumb->setParameter( 'q', $parameters['quality'] );
		
		// Image cropping/resizing parameters
		
		// zoom cropping overrides all other related parameters, so we allow a value of 0 or false to disable it
		if ( $parameters['zoom_crop'] != false ) {
			$this->phpthumb->setParameter( 'zc', $parameters['zoom_crop'] );
		}	
		$this->phpthumb->setParameter( 'far', $parameters['force_aspect_ratio'] );
		$this->phpthumb->setParameter( 'iar', $parameters['ignore_aspect_ratio'] );
		$this->phpthumb->setParameter( 'bg', $parameters['background'] );
		
		// Generate the image
		if ( !$this->phpthumb->GenerateThumbnail() ) {
			$this->error( 'phpThumb cannot generate the requested image. ' . $this->phpthumb->fatalerror . '<br /><br />' . implode( '\n\n', $this->phpthumb->debugmessages ) );
		} else {
			// Check image hasn't been generated in the meantime
			if ( file_exists( $parameters['output_path'] ) ) {
				// Redirect to the image
				$this->reload_from_cache( $parameters['output_url'] );
			}

			// Create directory to cache to
			if ( !$this->make_cache_directory( dirname( $parameters['output_path'] ) ) ) {
				$this->error( 'Cannot generate cache directory. Check that the Imprint cache is writable by PHP' );
			}
			
			// Write image to cache
			if ( !$this->phpthumb->RenderToFile( $parameters['output_path'] ) ) {
				$this->error( 'phpThumb cannot render the requested image. ' . $this->phpthumb->fatalerror . '<br /><br />' . implode( '\n\n', $this->phpthumb->debugmessages ) );
			}
			
			// Redirect to the image
			$this->reload_from_cache( $parameters['output_url'] );

		}
		
	}
	
	/**
	 * extend_config
	 *
	 * Combine user-set config keys with existing config keys and fill in defaults when needed.
	 *
	 * The default should be considered to be all of the attributes which are
	 * supported by the caller. The returned attributes will
	 * only contain the attributes in the $defaults list.
	 *
	 * If the $config list has unsupported keys, then they will be ignored and
	 * removed from the final returned list.
	 *
	 * @param	{Array} 	$defaults		Entire list of supported keys and their defaults.
	 * @param	{Array} 	$user			User-set config keys
	 *
	 * @return	{Array}						Combined and filtered configuration array
	 */
	private function extend_config( $defaults, $user ) {
		$user = (array)$user;
		$config = Array();
		
		// Extend default configuration array recursively
		foreach ( $defaults as $name => $default ) {
			if ( array_key_exists( $name, $user ) ) {
				if ( is_array( $default ) ) {
					$config[ $name ] = array_merge( $default, $user[ $name ] );
				} else {
					$config[ $name ] = $user[ $name ];
				}
			} else {
				$config[ $name ] = $default;
			}
		}

		return $config;
	}

	/**
	 * make_cache_directory
	 *
	 * Makes and checks cache directory path recursively
	 *
	 * @param	{String}	$path			Path to create
	 *
	 * @returns {Boolean}					Returns 'true' if exists or made or 'false' on failure.
	 */
	private function make_cache_directory( $path ) {
		
		// Check if path exists
		if( !is_dir( $path ) ) {
			// It doesn't, so try to make it
			return mkdir( $path, 0777, true );
		} else {
			// It does
			return true;
		}

	}
	
	/**
	 * reload_from_cache
	 *
	 * Redirects to cached image
	 *
	 * @param	{String}	$image_url		Image URL to redirect to
	 *
	 * @returns {Void}						
	 */
	public function reload_from_cache( $image_url ) {
		// NB: you need a cache-busting query string or IE won't do a redirect
		header( 'Location: ' . dirname( $_SERVER['PHP_SELF'] ) . '/cache/' . $image_url . '?' . time() );
	}
	
	/**
	 * flush_cache
	 *
	 * Deletes files from image cache
	 *
	 * @param	{String}	$source_image_path	Path of source image to remove from cache. If null, ALL source images flushed
	 * @param	{Array}		$source_image_sizes	Array of image sizes to remove from cache. If null, ALL image sizes flushed
	 *
	 * @returns {Boolean}						'true' if successful, 'false' if not		
	 */
	public function flush_cache( $source_image_path=null, $source_image_sizes=null ) {
		
		/**
		 * Get top-level cache directories, representing image sizes
		 */
		 
		// Build cache directory path
		$cache_path = $this->config['imprint']['site_root_path'] . $this->config['imprint']['imprint_path'] . 'cache/';
		
		// Init directories array
		$size_directories = Array();

		// Open cache directory
		if ( !$handle = opendir( $cache_path ) ) {
			// Show error message
			trigger_error( 'Could not open directory at: '. $cache_path, E_USER_ERROR );
			// Return
			return false;
		}
		
		// Read directories in cache
		while ( false !== ( $filename = readdir( $handle ) ) ) {
			// Exclude hidden, current, parent dirs (all start with a '.')
			if ( stripos( $filename, '.' ) !== 0 ) {
				if ( is_dir( $cache_path . $filename . '/' ) ) {
					// Add to list
					$size_directories[] = $filename;
				}
			}
		}
		
		// Close directory
		closedir( $handle );
		
		/**
		 * Flush cache for specific image, if given, or for every image
		 */
		 
		if ( isset( $source_image_path ) ) {
			
			/**
			 * Flush specific image
			 */
			
			// Loop through image size cache directories and search for the source image
			foreach ( $size_directories as $size_directory ) {
			
				// Check if we're only flushing specific image sizes or not
				if ( isset( $source_image_sizes ) ) {
					// Skip current size cache directory if it's not in the sizes to delete array
					if ( !in_array( $size_directory, $source_image_sizes, true ) ) {
						continue;
					}
				}
				
				// Build image path to look for
				$image_path = $cache_path . $size_directory . '/' . $source_image_path;
				
				if ( file_exists( $image_path ) ) {
					// Delete image
					if( !unlink( $image_path ) ) {
						// Show error message
						trigger_error( 'Could not unlink file at: ' . $image_path, E_USER_WARNING );
					}
					
					// Delete containing dir(s), if empty
					if( !$this->delete_cache_directory_tree( dirname( $image_path ), 'up', false ) ) {
						// Show error message
						trigger_error( 'Could not delete cache directory tree at: ' . dirname( $image_path ), E_USER_NOTICE );
					}

				}
			}

		} else {
			
			/**
			 * Flush entire cache
			 */
			 
			// Loop through image size cache directories and remove each of them, and their contents
			foreach ( $size_directories as $size_directory ) {
			
				// Check if we're only flushing specific image sizes or not
				if ( isset( $source_image_sizes ) ) {
					// Skip current size cache directory if it's not in the sizes to delete array
					if ( !in_array( $size_directory, $source_image_sizes, true ) ) {
						continue;
					}
				}
				
				// Build full directory path
				$size_directory_path = $cache_path . $size_directory;
				
				// Delete containing dir(s), if empty
				if( !$this->delete_cache_directory_tree( $size_directory_path, 'down', true ) ) {
					// Show error message
					trigger_error( 'Could not delete cache directory tree at: ' . $size_directory_path, E_USER_NOTICE );
				}
			
			}
		
		}
		
		// Return true if all operations completed successfully
		return true;

	}
	
	/**
	 * delete_directory_tree
	 *
	 * Recursively deletes a cache directory tree
	 *
	 * @param	{String}	$directory_path		Path of directory to delete
	 * @param	{String}	$direction			Direction to traverse tree in: 'up', or 'down'
	 * @param	{String}	$delete_non_empty	'true' to delete non-empty directories when travelling up a tree, 'false' to stop
	 * @param	{String}	$end_path			Path of directory to stop traversing at, if $direction is 'up'
	 *
	 * @returns {Boolean}						'true' if successful, 'false' if not
	 */
	private function delete_cache_directory_tree( $directory_path, $direction='down', $delete_non_empty=false ) {
		
		// Trim any trailing slash
		$directory_path = rtrim( $directory_path, '/');
		
		// Check directory exists
		if ( !is_dir( $directory_path ) || is_link( $directory_path ) ) {
			// Directory doesn't exist or is a symlink, so exit with an error
			trigger_error( 'Directory doesn\'t exist or is a symlink at: ' . $directory_path, E_USER_ERROR );
			// Return
			return false;
		}
		
		// Unwanted files that are safe to delete
		$unwanted_files = Array(
			'Thumbs.db',
			'.DS_Store'
		);
		
		// Examine directory contents
		if ( !$handle = opendir( $directory_path ) ) {
			// Show error message
			trigger_error( 'Could not open directory at: '. $directory_path, E_USER_ERROR );
			// Return
			return false;
		}
		
		// Read contents
		while ( false !== ( $file_name = readdir( $handle ) ) ) {
			// Exclude current, parent dirs and cache .htaccess rules
			if ( $file_name != '.' && $file_name != '..' && $file_name != '.htaccess' ) {
		
				// Build full file path
				$file_path = $directory_path . '/' . $file_name;

				// Delete directory contents, if allowed
				if ( ( $delete_non_empty || in_array( $file_name, $unwanted_files ) ) && is_readable( $file_path ) ) {
					
					if ( is_dir( $file_path ) ) {
						// Delete directory
						$this->delete_cache_directory_tree( $file_path, $direction, $delete_non_empty );
					} else {
						// Delete file
						if ( !unlink( $file_path ) ) {
							// Show error message
							trigger_error( 'Could not unlink file at: ' . $file_path, E_USER_ERROR );
							// Return
							return false;
						}
					}

				} else {
					// Exit if we don't want to delete directory contents or we don't have permission to do so
					trigger_error( 'Directory is not empty or not readable at: ' . $directory_path, E_USER_NOTICE );
					// Return
					return false;
				}
				
			}
		}
		
		// Close directory
		closedir( $handle );
		
		// Attempt to delete current directory
		if ( !rmdir( $directory_path ) ) {
			// Show error message
			trigger_error( 'Could not delete directory at: ' . $directory_path, E_USER_ERROR );
			// Return
			return false;
		}
		
		// Traverse according to direction
		if ( $direction == 'up' ) {
			// Get parent directory path
			$parent_directory_path = dirname( $directory_path );
			
			// Trim trailing slash, if necessary
			$parent_directory_path = rtrim( $parent_directory_path, '/' );
			
			// Prevent deletion of cache directory
			if ( $parent_directory_path != $this->config['imprint']['site_root_path'] . $this->config['imprint']['imprint_path'] . 'cache' ) {
				// Delete parent directory
				$this->delete_cache_directory_tree( dirname( $directory_path ), $direction, $delete_non_empty );
			}
		}
		
		// return true if every operation was completed successfully
		return true;

	}
	
	/**
	 * error
	 *
	 * Basic error handling method
	 *
	 * @param	{String}	$error			Error message to display
	 *
	 * @returns {Void}
	 */
	private function error( $error ) {
		
		// Send 404 header
		header( 'HTTP/1.0 404 Not Found' );
		
		// Display error message
		echo '<h1>Not Found</h1>';
		echo '<p>The image you requested could not be found.</p>';
		echo '<p>An error was triggered: <b>' . $error . '</b></p>';
		
		// End execution
		exit();
	}

}

?>