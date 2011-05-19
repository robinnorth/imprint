/* <?php */
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
 * @version 1.0.1
 *
 * @id Imprint MODx Evo Plugin
 *
 * @desc Automatically clears Imprint cache for updated images
 * @param	{String} 	$imprint_path		Path to Imprint application, relative to MODx root
 * 
 * Default plugin configuration string:
 * &imprint_path=Imprint application path (relative to MODx root);text;imprint/
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
 * 
 * 09-05-2011	-	1.0.1
 *
 * - Fix for saving documents with no template variables
 * --------------------------------------------------------------------------
 */
	
	/* =Plugin configuration
	------------------------------------------------------------------- */
	
		// Make MODx object reference
		global $modx;
		
		// Imprint path
		$imprint_path = ( isset( $imprint_path ) ) ? $imprint_path : 'imprint/';
	
	/* =Required libraries
	------------------------------------------------------------------- */
		include( $modx->config['base_path'] . $imprint_path . 'imprint.config.inc.php' );
		include( $modx->config['base_path'] . $imprint_path . 'lib/debug.inc.php' );
		
		require( $modx->config['base_path'] . $imprint_path . 'lib/imprint.class.php' );

	/* =Application functionality
	------------------------------------------------------------------- */
		
		/**
		 * Hook into MODx Manager events
		 * We need to hook 'OnSiteRefresh' and 'OnDocFormSave'
		 */
		
		// Make event reference
		$e = &$modx->Event;

		switch ( $e->name ) {
			
			case 'OnSiteRefresh':
			
				// Create new instance of Imprint Class
				$imprint = new Imprint( $config );
				
				// Flush the cache
				if( $imprint->flush_cache() ) {
					echo '<p><strong>Imprint plugin: </strong> Image cache was flushed succesfully</p>';
				} else {
					echo '<p><strong>Imprint plugin: </strong> Failed to flush image cache! Check your file and directory permissions.</p>';
				}

			break;
			
			case 'OnDocFormSave':
				
				// Create new instance of Imprint Class
				$imprint = new Imprint( $config );
				
				// Connect to database
				$modx->db->connect();
				
				// Get table names
				$content_table = $modx->getFullTableName( 'site_content' );
				$tvars_table = $modx->getFullTableName( 'site_tmplvars' );
				$tvars_content_table = $modx->getFullTableName( 'site_tmplvar_contentvalues' );

				// Find image template variables for site
				$query = $modx->db->query( 'SELECT id, name, type FROM ' . $tvars_table . ' WHERE type = \'image\'' );
				
				// Store variable ids
				$variables = Array();
				while ( $row = $modx->db->getRow( $query ) ) {
					$variables[ $row['name'] ] =  $row['id'];
				}
				
				// If image variables are found, flush the Imprint cache for them
				if ( !empty( $variables ) ) {

					// Find image template variables for current document
					$query = $modx->db->query( 'SELECT value FROM ' . $tvars_content_table . ' WHERE contentid = \''. $id . '\' AND tmplvarid IN (' . implode( ', ', $variables ) . ')' );
					
					// Clear cache
					while ( $row = $modx->db->getRow( $query ) ) {	
						// Flush the cache
						$imprint->flush_cache( $row['value'] );
					}
					
				}
	
			break;
			
			default:
			
				// Do nothing
				return false;
			
			break;

		}

/* ?> */