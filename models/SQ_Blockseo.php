<?php

/**
 * The model class for SQ_Blockseo
 *
 */
class Model_SQ_Blockseo {

    /**
     * Get the advanced SEO from database
     * @global integer $sq_postID
     * @global type $wpdb
     * @return string
     */
    public function getAdvSeo() {
        global $sq_postID, $wpdb;
        $meta = array();
        $str = '';

        if ((int) $sq_postID == 0)
            return;

        $meta = array('sq_fp_title' => '',
            'sq_fp_description' => '');

        $sql = "SELECT `meta_id`, `meta_value`, `meta_key`
                    FROM `" . $wpdb->postmeta . "`
                    WHERE  `post_id`=" . (int) $sq_postID;

        $rows = $wpdb->get_results($sql);

        foreach ($rows as $row) {
            if (array_key_exists($row->meta_key, $meta))
                $meta[$row->meta_key] = $row->meta_value;
        }

        return $meta;
    }

}

?>