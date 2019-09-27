<?php

class PostManager {

    private $SHORTCODE = "lebendiger_adventskalender";

    public function __construct() {
        // register shortcode
        add_shortcode($SHORTCODE, [$this,'printPost']);
    }

    public function createPost() {
        $post_arr = [
            'post_title'    => "Lebendiger Adventskalender",
            'post_content'  => "[$SHORTCODE]",
            'post_status'   => "publish"
        ];
        $result = wp_insert_post($post_arr, true);
        return $result;
    }

    public function deletePost($post_id) {
        $result = wp_delete_post($post_id);
        if (!$result)
            return false;
        return true;
    }

    public function printPost() {
        include(dirname(__FILE__).'/../post.php');
    }
}
