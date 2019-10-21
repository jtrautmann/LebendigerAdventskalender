<?php

class PostManager {

    private $SHORTCODE = "lebendiger_adventskalender";

    public function __construct() {
        // register shortcode
        add_shortcode($this->SHORTCODE, [$this,'printPost']);
    }

    public function createPost() {
        $post_arr = [
            'post_title'    => "Lebendiger Adventskalender",
            'post_content'  => "[$this->SHORTCODE]",
            'post_status'   => "draft"
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
        include(plugin_dir_path(__FILE__).'/post.php');
    }
}
