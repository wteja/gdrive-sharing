<?php

function gdrive_sharing_covert_url($url) {
    return empty( $url ) ? "" : preg_replace('/\/file\/d\/(\w+)\/view\?usp=sharing/',"/uc?export=download&id=$1", $url );
}

function gdrive_sharing_extract_links( $content ) {
    $pattern = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';

    if($num_found = preg_match_all($pattern, $content, $out))
    {
        return $out[0][0];
    } else {
        return "";
    }
}

function gdrive_sharing_generate_thumbnail( $url ) {
    $upload_dir = wp_get_upload_dir()['path'] . '/';
    if( !file_exists($upload_dir) ) {
        mkdir( $upload_dir );
    }
    $pdfpath = $upload_dir . time() . '.pdf';
    $imgpath = $upload_dir . time() . '.png';
    $url = gdrive_sharing_covert_url( $url );var_dump($pdfpath);
    
    try {
        $contents = file_get_contents( $url );
        file_put_contents( $pdfpath, $contents );
        $imagick = new Imagick();
        $imagick->readImage( $pdfpath . '[0]' );
        $imagick->writeImages( $imgpath );

        // TODO: Create thumbnail logic.
        
    } finally {
        if( file_exists( $pdfpath ) ) {
            unlink( $pdfpath );
        }
        if( file_exists( $imgpath ) ) {
            unlink( $imgpath );
        }
    }
}