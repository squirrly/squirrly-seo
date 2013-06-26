<?php
class SQ_Frontend extends SQ_FrontController {
        public static $options;

        function __construct() {
            parent::__construct();

            SQ_ObjController::getController('SQ_Tools', false);
            self::$options = SQ_Tools::getOptions();

            if (SQ_Tools::getValue('sq_use') == 'on')
                self::$options['sq_use'] = 1;
            elseif (SQ_Tools::getValue('sq_use') == 'off')
                self::$options['sq_use'] = 0;

        }

        /**
         * Called after plugins are loaded
         */
        function hookLoaded(){
            if ( self::$options['sq_use'] == 1 ){
               //Use buffer only for meta Title
               //if(self::$options['sq_auto_title'] == 1)
                $this->model->startBuffer();
            }
        }
        function action(){}

        /**
         * Set the unique visitor cookie for the SQ_Traffic record
         */
        function hookFrontinit(){
            $traffic = SQ_ObjController::getController('SQ_Traffic', false);
            if (is_object($traffic)) $traffic->saveCookie();
        }

        /**
         * Hook the Header load
         */
        public function hookFronthead(){
            parent::hookHead();

            SQ_Tools::dump(self::$options,  $GLOBALS['wp_query']); //output debug

            if ( isset(self::$options['sq_use']) && (int)self::$options['sq_use'] == 1 ){
                echo $this->model->setHeader();

                //Use buffer only for meta Title
                //if(self::$options['sq_auto_title'] == 1)
                $this->model->flushHeader();
            }



	}

        /**
         * Hook Footer load to save the visit and to close the buffer
         */
        function hookFrontfooter(){
            if ( isset(self::$options['sq_use']) && (int)self::$options['sq_use'] == 1 ){
                //Use buffer only for meta Title
                //if(self::$options['sq_auto_title'] == 1)
                $this->model->flushHeader();
            }
            //RECORD THE TRAFFIC
            $this->model->recordTraffic();
        }

}
?>