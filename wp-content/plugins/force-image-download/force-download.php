<?php
/*
Plugin Name: Force Images Download
Description: Force the download of image media files by adding the 'forceDownload' css class to any link.
Version: 1.1
Author: Aurélien Chappard
Author URI: http://www.deefuse.fr/
License: GPL
Copyright: Aurélien Chappard
*/
if( !class_exists( 'Deefuse_Force_Images_Download' ) ) {
	
class Deefuse_Force_Images_Download {

	/**
	 * Constructor
	 */
	function Deefuse_Force_Images_Download(){
		// Actions
		add_action('init', array(&$this,'init_forceDownload_callback') );
	}
	
	/**
	 * Load jQuery and 'force image to download' script in the front-end
	 */
	function init_forceDownload_callback(){
		if(!is_admin()){
			$this->force_download_callback();
			
			wp_enqueue_script("jquery");
			wp_enqueue_script("deefuse-fi-dl-js", plugins_url('js/deefuse-forceImages-dl.js', __FILE__), array("jquery"));
		}
	}
	
	/**
	 * Check the request uri from server. 
	 * If the correct pattern is founded, the file is forced to be downloaded.
	 */
	function force_download_callback(){
		// Check pattern (added by JavaScript)
		$pattern = '/\?fileToDownload=/';
		
		if( preg_match($pattern, $_SERVER['REQUEST_URI']) === 1){
			$file=$_GET['fileToDownload'];

			if ($file != ""){
				$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$domainName = $_SERVER['HTTP_HOST'];

				$urlFileToDl = str_replace($protocol.$domainName.'/',"", $file);

				$urlFileToDl = ABSPATH.$urlFileToDl;

				if(is_file($urlFileToDl))
				{
					$mime_types = array('.png'	=> 'image/png',
										'.jpe'	=> 'image/jpeg',
										'.jpeg' => 'image/jpeg',
										'.jpg'	=> 'image/jpeg',
										'.gif'	=> 'image/gif',
										'.bmp'	=> 'image/bmp',
										'.ico'	=> 'image/vnd.microsoft.icon',
										'.tiff' => 'image/tiff',
										'.tif'	=> 'image/tiff',
										'.svg'	=> 'image/svg+xml',
										'.svgz' => 'image/svg+xml');

					$extension = strrchr(basename($urlFileToDl), "." );
					$type = "application/octet-stream";
					if(isset($mime_types[$extension]))
					{
						$type = $mime_types[$extension];
						//echo $type;
						
						header('Content-Description: File Transfer');
						header("Content-disposition: attachment; filename=".basename($urlFileToDl));
						header("Content-Type: $type");
						header("Content-Transfer-Encoding: $type\n" );
						header("Content-Length: ".filesize($urlFileToDl)); 
						ob_clean();
						flush();
						readfile( $urlFileToDl);
						exit();
					}
					else{
							wp_redirect(home_url());exit();
					}
				}
			}		
		}
	}
} /* end class Deefuse_Force_Images_Download */

/* Instantiate the plugin */
new Deefuse_Force_Images_Download();
}