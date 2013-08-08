<?php

class Model_SQ_PostsList {

    /** @var integer */
    public $post_id;

    /** @var array */
    private $traffic;
    public $reportTitles;

    /** @var string */
    public $interval = 'month';

    /** @var integer */
    public $max_val_show = 99999;

    /** @var string */
    public $ctx;

    /** @var integer */
    var $local_rank;

    /** @var integer */
    var $global_rank;

    function __construct() {
        $this->ctx = __('This article', _PLUGIN_NAME_) . '|' . __('All your articles', _PLUGIN_NAME_);
        $this->reportTitles = array('day' => __('Latest', _PLUGIN_NAME_),
            'week' => __('Last 7 days', _PLUGIN_NAME_),
            'month' => __('Last 30 days', _PLUGIN_NAME_),
        );
    }

    /**
     * Get the traffic progress from loca database
     * @return array
     */
    public function getTrafficProgress() {
        return SQ_ObjController::getController('SQ_Traffic', false)->getTraffic($this->post_id, $this->interval);
    }

    /**
     * Get he global average values
     *
     * @return array
     */
    public function getGlobalAverage() {
        return SQ_ObjController::getController('SQ_Traffic', false)->getGlobalAverage();
    }

    /**
     * Get the average progress for the given interval
     * @return array
     */
    public function getAverageProgress() {
        return SQ_ObjController::getController('SQ_Traffic', false)->getAverage($this->interval);
    }

    /**
     * Pack the Brief Zone
     * @param object  $response
     * @return string
     */
    public function packBrief($response) {
        if (!isset($response->seo))
            return;

        if (isset($response->seo) && ($response->seo->optimized == '0%')) {
            $error = array('error' => 'sq_no_information',
                'message' => __('Optimize this article to start Squirrly Article Rank', _PLUGIN_NAME_));
            return $error;
        }



        //echo 'pack';
        $this->interval = 'month'; //Get the traffic for the whole month
        $this->traffic = $this->getTrafficProgress();

        $pos = 0;
        if (isset($response->global_rank) && isset($response->local_rank))
            if (!$pos = min($response->global_rank, $response->local_rank))
                $pos = max($response->global_rank, $response->local_rank);

        if (!isset($response->local_rank) && isset($response->global_rank))
            $pos = $response->global_rank;



        //add the local SERP value
        //$rank = SQ_ObjController::getController('SQ_Ranking', false)->getRank($this->post_id);
        //if ($rank['position']['global'] == 0) $rank['position']['global'] = '>100';
        $response->serp[] = array('id' => 'sq_rank_serp',
            'keyword' => $response->seo->keyword,
            'indexed' => $response->indexed,
            'pos' => $pos);
        /////////////////////////////////////////////
        $response->serp = json_decode(json_encode($response->serp), FALSE);

        if (isset($response->total) && isset($response->history) && isset($response->serp)) {
            $str = '';
            $str .= '<ul class="sq_rank_brief">';
            $str .= '<li class="sq_mobile">' . $this->packBriefTotal($response->total) . '</li>';
            $str .= '<li class="sq_mobile_graph">' . $this->packBriefProgress($response->history) . '</li>';
            $str .= '<li class="sq_mobile sq_last_col">' . $this->packBriefSERP($response->serp) . $this->packBriefSquirrly($response->squirrly) . '</li>';
            $str .= '</ul>';
            if (isset($response->global))
                $str .= $this->changeOffpage($response->global);
            return $str;
        }else {
            $error = array('error' => 'sq_no_information',
                'message' => __('There are no information for this post yet.', _PLUGIN_NAME_));
            return $error;
        }
    }

    private function changeOffpage($global) {
        if (!is_object($global))
            return false;
        if (SQ_Tools::getValue('sq_debug') !== 'on')
            return "<script>jQuery('#sq_rank_global-" . $this->post_id . "').html('" . (($global->offpage > 0) ? '+' : '') . $global->offpage . "');</script>";
    }

    /**
     * Pack the Total scores in the Brief Zone
     * @param object  $totals
     * @return boolean|string
     */
    private function packBriefTotal($totals) {
        if (!is_array($totals) || count($totals) == 0)
            return false;
        $titles = array('sq_rank_traffic' => __('Traffic to article', _PLUGIN_NAME_),
            'sq_rank_social' => __('Social impact', _PLUGIN_NAME_),
            'sq_rank_inbound' => __('Links to article', _PLUGIN_NAME_),
        );

        $show_traffic = (($this->traffic['month']['count'] < $this->max_val_show) ? $this->traffic['month']['count'] : $this->traffic['day']['count']);
        $show_traffic = number_format($show_traffic, 0, '', ',');

        $str = '<ul class="sq_rank_totals">';
        //Add the traffic information
        $str .= '<li class="sq_rank_traffic_total"><span class="sq_rank_sprite sq_rank_total_value">' . $show_traffic . '</span><span class="sq_rank_total_title">' . $titles['sq_rank_traffic'] . '</span></li>';
        foreach ($totals as $total) {
            $str .= '<li class="' . $total->id . '_total"><span class="sq_rank_sprite sq_rank_total_value">' . (($total->value > 0) ? '+' . number_format($total->value, 0, '', ',') : number_format($total->value, 0, '', ',')) . '</span><span class="sq_rank_total_title">' . $titles[$total->id] . '</span></li>';
        }
        $str .= '</ul>';

        return $str;
    }

    /**
     * Pack the Progress in the Brief Zone
     *
     * @param object  $history
     * @return boolean|string
     */
    private function packBriefProgress($history) {
        if (!is_array($history) || count($history) == 0)
            return false;


        $str = '';
        $str .= '<ul class="sq_rank_history">';
        //$str .= '<li class="sq_rank_score-icons"><span class="sq_rank_icon1 sq_rank_sprite"></span><span class="sq_rank_icon2 sq_rank_sprite"></span><span class="sq_rank_icon3 sq_rank_sprite"></span></li>';
        foreach ($history as $date) {
            if (!is_array($date->values))
                continue;

            $progress = false;
            foreach ($date->values as $value)
                if ($value > 0)
                    $progress = true;
            if ((int) $this->traffic[$date->id]['count'] > 0)
                $progress = true;

            $str .= '<li class="sq_history_icon"><span class="sq_rank_sprite sq_total_traffic_icon"></span><span class="sq_rank_sprite sq_total_social_icon"></span><span class="sq_rank_sprite sq_total_links_icon"></span></li>';
            $str .= '<li class="sq_rank_score-unit">
                        <div class="sq_rank_history_arrow"><span class="' . ((!$progress) ? 'sq_rank_progress_zero' : 'sq_rank_progress_pos') . '"></span></div>';
            //Get the traffic information
            $str .= '   <div class="' . (((int) $this->traffic[$date->id]['count'] > 0) ? 'sq_rank_history_pos' : 'sq_rank_history_zero') . '">' . $this->traffic[$date->id]['count'] . '</div>';
            foreach ($date->values as $value) {
                $str .= '   <div class="' . (((int) $value > 0) ? 'sq_rank_history_pos' : 'sq_rank_history_zero') . '"><span>' . (((int) $value > 0) ? '+' : '') . '</span>' . $value . '</div>';
            }
            $str .= '   <div class="sq_rank_history_text" ref="' . $date->id . '">' . $this->reportTitles[$date->id] . '</div>
                     </li>';
        }
        $str .= '</ul>';
        return $str;
    }

    /**
     * Pack the Squirrly Score in the Brief Zone
     * @param object $squirrly
     * @return boolean|string
     */
    private function packBriefSERP($totals) {
        if (!is_array($totals))
            return false;
        $titles = array('sq_rank_serp' => __('Google Indexed', _PLUGIN_NAME_));


        $str = '<ul class="sq_rank_totals">';
        foreach ($totals as $total) {
            $str .= '<li class="' . $total->id . '_total"><span class="sq_rank_sprite sq_rank_total_value ' . (($total->indexed == 1) ? '' : 'sq_notfound') . '">' . (($total->indexed == 1) ? (($total->pos > 0) ? $total->pos . '<span>' . __('pos', _PLUGIN_NAME_) . '</span>' : __('Yes', _PLUGIN_NAME_)) : __('No', _PLUGIN_NAME_)) . '</span><span class="sq_rank_total_title">' . $titles[$total->id] . (($total->keyword <> '' && $total->pos > 0) ? '<span class="sq_rank_total_keyword">  ' . __('for:', _PLUGIN_NAME_) . ' ' . $total->keyword . '</span>' : '<span class="sq_rank_total_keyword">  ' . __('(searched with the url)', _PLUGIN_NAME_) . '</span>') . '</span></li>';
        }
        $str .= '</ul>';

        return $str;
    }

    /**
     * Pack the Squirrly Score in the Brief Zone
     * @param object $squirrly
     * @return boolean|string
     */
    private function packBriefSquirrly($squirrly) {
        if (!is_object($squirrly))
            return false;
        //if ($squirrly->max == 0) return '';

        $str = '';
        $str .= '<ul class="sq_rank_squirrly">';
        $str .= '<li>
                    <div class="sq_rank_squirrly_score"><span class="sq_rank_sprite sq_rank_squirrly_icon"></span>
                    <div class="sq_rank_squirrly_progress"><img src="http://chart.googleapis.com/chart?chf=bg,s,F8FBFD&chxr=1,5,100&chxs=0,F8FBFD,0,0,_,F8FBFD|1,C2BDDD,0,0,t,F8FBFD&chxt=x,y&chbh=15,4,20&chs=150x50&cht=bhg&chco=b35a34,717171&chds=0,' . $squirrly->max . ',0,' . $squirrly->max . '&chd=t:' . $squirrly->value . '|' . $squirrly->average . '&chdl=' . __('This article|Average', _PLUGIN_NAME_) . '" style="width:150px; height:50px;" /></div>
                  </li>
                ';
        $str .= '</ul>';
        //$str .= '<script>jQuery("#sq_progressbar_'.$this->post_id.'").progressBar('.$squirrly->value.');</script>';
        return $str;
    }

    /*     * ***************** TOTALS *************************** */

    /**
     * Get the traffic information from database about the specific post
     * Pack the TRAFFIC in HTML code
     */
    private function packTraffic($interval) {
        $serp = array();
        $max = 0;

        $this->traffic = $this->getTrafficProgress();
        $this->traffic['global']['average'] = $this->getGlobalAverage();

        $max = max($this->traffic[$interval]['average']['count'], $this->traffic['global']['average']['count']);
        $max = (($max == 0) ? 1 : $max);

        // if ($this->traffic[$interval]['average']['old'] < 7) //don't show metrics in the first 7 days
        //     $max = 0;

        $serp[] = array('id' => 'sq_rank_traffic',
            'class' => 'sq_rank_traffic_header',
            'title' => __('Traffic to article', _PLUGIN_NAME_),
            'max' => $max,
            'value' => $this->traffic[$interval]['average']['count'],
            'average' => $this->traffic['global']['average']['count'],
        );

        $serp[] = array('class' => 'sq_rank_traffic_page',
            'title' => __('Visits', _PLUGIN_NAME_),
            'change' => $this->traffic[$interval]['count']);
        $serp[] = array('class' => 'sq_rank_traffic_unique',
            'title' => __('Unique', _PLUGIN_NAME_),
            'change' => $this->traffic[$interval]['unique']);
        return $serp;
    }

    public function getTrafficZone() {

        $traffic = $this->packTraffic($this->interval);

        $str = '';
        $str .= '<ul class="sq_rank_ul_values">
                <li class="sq_rank_values">
                    <table>
                        <tr>
                          <td class="sq_rank_block_list_header" colspan="4"><span class="sq_rank_sprite ' . $traffic[0]["class"] . '"></span><span class="sq_rank_block_header">' . $traffic[0]["title"] . '</span></td>
                        </tr>
                    </table>
                    ' . (($traffic[0]["max"] > 0) ? '<div class="sq_rank_block_compare_bars"><img src="http://chart.googleapis.com/chart?chf=bg,s,F8FBFD&chxr=1,5,100&chxs=0,F8FBFD,0,0,_,F8FBFD|1,C2BDDD,0,0,t,F8FBFD&chxt=x,y&chbh=15,4,20&chs=500x50&cht=bhg&chco=F7A404,717171&chds=0,' . $traffic[0]["max"] . ',0,' . $traffic[0]["max"] . '&chd=t:' . $traffic[0]["value"] . '|' . $traffic[0]["average"] . '&chdl=' . $this->ctx . '" /></div>' : '') . '
                    <table class="sq_rank_block_list">
                        <tr>
                          <th ></th>
                          <th></th>
                          <th class="sq_rank_block_list_header">' . $this->reportTitles[$this->interval] . '</th>
                        </tr>';

        foreach ($traffic as $key => $value) {
            if ($key > 0) {
                $str .= '<tr>
                                    <td><span class="sq_rank_sprite ' . $value["class"] . '"></span></td>
                                    <td class="sq_rank_block_list_title"><span>' . $value["title"] . '</span></td>
                                    <td class="sq_rank_block_list_value"><span style="' . (($value["change"] < 0) ? "color:red;" : (($value["change"] > 0) ? "color:" . (($value["class"] == 'sq_rank_traffic_unique') ? '#70bad9' : '#068cc5') . "; font-weight:bold;" : "color:gray")) . '">' . (($value["change"] > 0) ? $value["change"] : $value["change"]) . '</span></td>
                                 </tr>';
            }
        }

        $str .= '</table>

                </li>

                <li class="sq_rank_graph">
                    <div id="' . $traffic[0]["id"] . '-' . $this->post_id . '"></div>
                </li>
            </ul>
            ';
        if (SQ_Tools::getValue('sq_debug') !== 'on')
            $str .= $this->getGraph($traffic[0]["id"] . '-' . $this->post_id);
        return $str;
    }

    private function getGraph($div) {
        $traffic = SQ_ObjController::getController('SQ_Traffic', false)->getHistory($this->post_id, $this->interval, 'ASC');
        $rows = array();
        $table = array();
        $table['cols'] = array(
            array('label' => 'Date', 'type' => 'string'),
            array('label' => 'Unique', 'type' => 'number'),
            array('label' => 'Visits', 'type' => 'number'),
        );

        /* Extract the information from $result */
        foreach ($traffic as $r) {

            $temp = array();

            // the following line will used to slice the Pie chart

            $temp[] = array('v' => (string) date_i18n(get_option('date_format'), strtotime($r->date)));

            //Values of the each slice
            $temp[] = array('v' => (int) $r->unique);
            $temp[] = array('v' => (int) $r->count);
            $rows[] = array('c' => $temp);
        }

        $table['rows'] = $rows;
        $jsonTable = json_encode($table);
        //echo $jsonTable;
        return '<script type="text/javascript">
                // Create our data table out of JSON data loaded from server.
                var data = new google.visualization.DataTable(' . $jsonTable . ');
                var options = {
                    colors:["#70bad9","#068cc5"],
                    title: "",
                    width: 500,
                    height: 200
                  };
                // Instantiate and draw our chart, passing in some options.
                //do not forget to check ur div ID
                var chart = new google.visualization.ColumnChart(document.getElementById("' . $div . '"));
                chart.draw(data, options);
            </script>';
    }

    public function getSerpProgress() {
        //Check if other keyword is ranked better
        SQ_ObjController::getController('SQ_Ranking', false)->getOtherKeywords($this->post_id);

        return SQ_ObjController::getController('SQ_Ranking', false)->getRank($this->post_id);
    }

    /**
     * Get the serp information from database about the specific permalink
     * Pack the SERP in HTML code
     */
    public function packSERP() {
        $serp = array();
        $rank = $this->getSerpProgress();

        $serp[] = array('id' => 'sq_rank_serp',
            'class' => 'sq_rank_flag_google_header',
            'lastcheck' => (isset($rank['change']['lastcheck']) ? $rank['change']['lastcheck'] : null),
            'title' => __('Google result for: ', _PLUGIN_NAME_),
            'keyword' => $rank['keyword']);

        $serp[] = array('class' => 'sq_rank_flag_google_com',
            'flag' => 'us',
            'title' => 'google.' . $rank['flag']['global'],
            'change' => $rank['change']['global'],
            'position' => $rank['position']['global']);

        if ($rank['flag']['local'] <> '') {
            $serp[] = array('flag' => $rank['flag']['local_ico'],
                'class' => 'sq_rank_flag_google_' . $rank['flag']['local'],
                'title' => 'google.' . $rank['flag']['local'],
                'change' => $rank['change']['local'],
                'position' => $rank['position']['local']);
        }
        return $serp;
    }

    public function getSERPZone($serp) {
        $str = '';
        $str .= '<ul class="sq_rank_ul_values">
                <li class="sq_rank_values">
                    <table>
                        <tr>
                          <td class="sq_rank_block_list_header" colspan="4"><span class="sq_rank_sprite ' . $serp[0]["class"] . '"></span><span class="sq_rank_block_header">' . $serp[0]["title"] . (($serp[0]["keyword"] <> '') ? '<span class="sq_rank_total_keyword">  ' . ' ' . $serp[0]["keyword"] . ' ' . '</span>' : '') . '</span></td>
                        </tr>
                    </table>
                    <table class="sq_rank_block_list">
                        <tr>
                          <th ></th>

                          <th class="sq_rank_block_list_header">' . __('Change', _PLUGIN_NAME_) . (isset($serp[0]["lastcheck"]) ? '<br /><span class="sq_rank_lastcheck">' . __('since', _PLUGIN_NAME_) . ': ' . (string) date_i18n(get_option('date_format'), strtotime($serp[0]["lastcheck"])) . '</span>' : '') . '</th>
                          <th class="sq_rank_block_list_header">' . __('Current position', _PLUGIN_NAME_) . '<br /><span class="sq_rank_currentcheck">' . (string) date_i18n(get_option('date_format'), time()) . '</span>' . '</th>
                        </tr>';

        foreach ($serp as $key => $value) {
            if ($key > 0) {
                $str .= '<tr>
                                    <td><span><img src="' . _SQ_STATIC_API_URL_ . SQ_URI . '/img/flag/' . $value["flag"] . '.png" title="' . $value["title"] . '" /></span></td>

                                    <td class="sq_rank_block_list_value"><span style="' . (($value["change"] < 0) ? "color:red;" : (($value["change"] > 0) ? "color:green; font-weight:bold;" : "color:gray")) . '">' . (($value["change"] > 0) ? "+" . $value["change"] : $value["change"]) . '</span></td>
                                    <td class="sq_rank_block_list_value"><span style="' . (($value["position"] == 0) ? "color:red;" : (($value["position"] > 0 && $value["position"] <= 10) ? "color:green; font-weight:bold;" : "color:gray")) . '">' . (($value["position"] == 0) ? '>100' : $value["position"]) . '</span></td>
                                 </tr>';
            }
        }

        $str .= '</table>

                </li>

            </ul>
            ';

        return $str;
    }

    /**
     * Get the serp information from database about the specific permalink
     * Pack the SERP in HTML code
     */
    public function packOthersSERP() {
        $serp = array();
        $ranks = $this->getSerpProgress();
        //print_r($ranks);
        $serp[] = array('id' => 'sq_rank_serp_others',
            'class' => 'sq_rank_flag_google_header',
            'title' => __('Recommended by Squirrly', _PLUGIN_NAME_));

        if (!is_array($ranks['others']))
            return false;

        foreach ($ranks['others'] as $keyword => $rank) {
            $serp[] = array('class' => 'sq_rank_flag_google_com',
                'flag' => 'us',
                'title' => 'google.' . $ranks['flag']['global'],
                'position' => $rank['global'],
                'keyword' => $keyword);

            if ($ranks['flag']['local'] <> '') {
                $serp[] = array('flag' => $ranks['flag']['local_ico'],
                    'class' => 'sq_rank_flag_google_' . $ranks['flag']['local'],
                    'title' => 'google.' . $ranks['flag']['local'],
                    'position' => $rank['local'],
                    'keyword' => $keyword);
            }
        }

        return $serp;
    }

    public function getOthersSERPZone($serp) {
        $str = '';

        if (!is_array($serp))
            return $str;

        $str .= '<ul class="sq_rank_ul_values">
                <li class="sq_rank_values">
                    <table>
                        <tr>
                          <td class="sq_rank_block_list_header" colspan="4" ><span class="sq_rank_sprite sq_rank_squirrly_icon"></span><span class="sq_rank_block_header">' . $serp[0]["title"] . (($serp[0]["keyword"] <> '') ? '<span class="sq_rank_total_keyword">  ' . '(' . $serp[0]["keyword"] . ')' . '</span>' : '') . '</span></td>
                        </tr>
                    </table>
                    <table class="sq_rank_block_list">
                        <tr>
                          <th ></th>
                          <th ></th>
                          <th class="sq_rank_block_list_header">' . __('Keyword', _PLUGIN_NAME_) . '</th>
                          <th class="sq_rank_block_list_header">' . __('Current position', _PLUGIN_NAME_) . '</th>
                        </tr>';

        foreach ($serp as $key => $value) {
            if ($key > 0) {
                $str .= '<tr>
                                    <td><span><img src="' . _SQ_STATIC_API_URL_ . SQ_URI . '/img/flag/' . $value["flag"] . '.png" /></span></td>
                                    <td class="sq_rank_block_list_title"><span>' . $value["title"] . '</span></td>
                                    <td class="sq_rank_block_list_value"><strong>' . $value["keyword"] . '</strong></span></td>
                                    <td class="sq_rank_block_list_value"><span style="' . (($value["position"] == 0) ? "color:red;" : (($value["position"] > 0 && $value["position"] < 20) ? "color:green; font-weight:bold;" : "color:green;")) . '">' . (($value["position"] == 0) ? '>100' : $value["position"]) . '</span></td>
                                 </tr>';
            }
        }

        $str .= '</table>

                </li>

            </ul>
            ';

        return $str;
    }

}

?>