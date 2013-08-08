<?php

/**
 * The model class for SQ_Blocksearch
 *
 */
class Model_SQ_Blocksearch {

    var $results;

    public function searchImage($get) {
        $pack = $results_free = $results = array();
        $params = array('api_key' => '8c824e0994879c3580200f2eb7d4bdd7',
            'method' => 'flickr.photos.search',
            'format' => 'php_serial',
            'tag_mode' => 'any',
            'per_page' => $get['nrb'],
            'page' => $get['page'],
            'sort' => 'interestingness-desc',
            // 'sort' => 'relevance',
            'tags' => $get['q'],
            //  'privacy_filter' => '1,2,3,4',
            'license' => '7'
        );


        $this->doFlickrSearch($params, 1);

        //Search for images with licence attributes
        $params['license'] = '1,2,3,4,5,6';
        $this->doFlickrSearch($params);

        if (is_array($this->results) && count($this->results) > 0)
            return json_encode($this->results);

        return false;
    }

    /**
     * Get the images from flicker
     * @param array $params
     * @param integer $free: 1|0
     */
    private function doFlickrSearch($params, $free = 0) {
        foreach ($params as $k => $v) {
            $pack[] = urlencode($k) . '=' . urlencode($v);
        }

        //Call Flickr
        $url = "http://api.flickr.com/services/rest/?" . implode('&', $pack);

        $rsp = wp_remote_fopen($url);
        $rsp_obj = unserialize($rsp);

        // if we have photos
        if ($rsp_obj && $rsp_obj['photos']['total'] > 0) {
            foreach ($rsp_obj['photos']['photo'] as $photo) {

                $src = 'http://farm' . $photo['farm'] . '.static.flickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'];
                $source = 'http://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'];

                $this->results['responseData']['results'][] = array('tbUrl' => $src . '_s.jpg',
                    'url' => $src . '.jpg',
                    'attribute' => ($free == 0) ? $source : '',
                    'width' => '',
                    'height' => '',
                    'contentNoFormatting' => $photo['title']);
            }
        }
    }

}

?>