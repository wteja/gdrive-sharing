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

function gdrive_sharing_download_image( $url ) {
    $time = time();
    $upload_dir = wp_get_upload_dir()['path'] . '/';
    if( !file_exists($upload_dir) ) {
        mkdir( $upload_dir );
    }
    $pdfpath = $upload_dir . $time . '.pdf';
    $imgpath = $upload_dir . $time . '.png';
    $url = gdrive_sharing_covert_url( $url );
    
    try {
        $contents = file_get_contents( $url );
        file_put_contents( $pdfpath, $contents );
        $imagick = new Imagick();
        $imagick->readImage( $pdfpath . '[0]' );
        $imagick->writeImage( $imgpath );
        return $imgpath;
    } finally {
        if( file_exists( $pdfpath ) ) {
            unlink( $pdfpath );
        }
    }
}

function gdrive_sharing_generate_thumbnail( $post_id, $url ) {
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    if( !empty( $thumbnail_id ) )
        return;

    $downloadable_url = gdrive_sharing_covert_url( $url );
    $imgpath = gdrive_sharing_download_image( $downloadable_url );
    $title = get_the_title( $post_id );
    $wp_filetype = wp_check_filetype($imgpath, null );
    $attachment_id = wp_insert_attachment( [
        'post_mime_type' => $wp_filetype['type'],
        'post_parent' => $post_id,
        'post_title' => $title,
        'post_content' => '',
        'post_status' => 'inherit'
    ], $imgpath );
    
    if (!is_wp_error($attachment_id)) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $imgpath );
        wp_update_attachment_metadata( $attachment_id,  $attachment_data );
        set_post_thumbnail( $post_id, $attachment_id );
    }
}