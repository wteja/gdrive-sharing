<?php
class GDriveSharingShortcode {
    public function __construct() {
        add_shortcode( 'gdrive_download_button', [ $this, 'add_shortcodes' ] );
    }

    function add_shortcodes() {
        $id = get_the_ID();
        $url = get_post_meta( $id, 'gdrive_sharing_file_url', TRUE );
        $url = gdrive_sharing_covert_url( $url );
        if( empty( $url ) ) {
            echo NULL;
        } else {
            echo '<div class="gdrive-download-button"><a href="' . $url . '" target="_blank" rel="noreferrer noopener">Download</a></div>';
        }
    }
}

new GDriveSharingShortcode;