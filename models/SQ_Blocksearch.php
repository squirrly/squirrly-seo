<?php
/**
 * The model class for SQ_Blocksearch
 *
 */
class Model_SQ_Blocksearch{

	public function searchImage($get){
              $pack = $results = array();
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
                  'license' => '1,2,3,5,6,7'
                 );


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

                      $results['responseData']['results'][] = array('tbUrl' => $src . '_s.jpg',
                                                                    'url' => $src . '.jpg',
                                                                    'width' => '',
                                                                    'height' => '',
                                                                    'contentNoFormatting' => $photo['title'] );

                  }
                  return json_encode($results);
              }

              return false;
        }
}
?>