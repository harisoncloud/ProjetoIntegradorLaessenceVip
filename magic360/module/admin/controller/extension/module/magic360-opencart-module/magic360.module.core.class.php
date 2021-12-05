<?php

if(!defined('Magic360ModuleCoreClassLoaded')) {

    define('Magic360ModuleCoreClassLoaded', true);

    require_once(dirname(__FILE__) . '/magictoolbox.params.class.php');

    /**
     * Magic360ModuleCoreClass
     *
     */
    class Magic360ModuleCoreClass {

        /**
         * MagicToolboxParamsClass class
         *
         * @var   MagicToolboxParamsClass
         *
         */
        var $params;

        /**
         * Tool type
         *
         * @var   string
         *
         */
        var $type = 'circle';

        /**
         * Constructor
         *
         * @return void
         */
        function __construct() {
            $this->params = new MagicToolboxParamsClass();
            $this->params->setScope('magic360');
            $this->params->setMapping(array(
                'smoothing' => array('Yes' => 'true', 'No' => 'false'),
                'magnify' => array('Yes' => 'true', 'No' => 'false'),
                'loop-column' => array('Yes' => 'true', 'No' => 'false'),
                'loop-row' => array('Yes' => 'true', 'No' => 'false'),
                'reverse-column' => array('Yes' => 'true', 'No' => 'false'),
                'reverse-row' => array('Yes' => 'true', 'No' => 'false'),
                //'start-column' => array('auto' => '\'auto\''),
                //'start-row' => array('auto' => '\'auto\''),
                'fullscreen' => array('Yes' => 'true', 'No' => 'false'),
                'hint' => array('Yes' => 'true', 'No' => 'false'),
            ));
            $this->loadDefaults();
        }

        /**
         * Method to get headers string
         *
         * @param string $jsPath  Path to JS file
         * @param string $cssPath Path to CSS file
         *
         * @return string
         */
        function getHeadersTemplate($jsPath = '', $cssPath = null) {
            //to prevent multiple displaying of headers
            if(!defined('MAGIC360_MODULE_HEADERS')) {
                define('MAGIC360_MODULE_HEADERS', true);
            } else {
                return '';
            }
            if($cssPath == null) {
                $cssPath = $jsPath;
            }
            $headers = array();
            // add module version
            $headers[] = '<!-- Magic 360 OpenCart module version v4.5.32 [v1.6.91:v4.6.12] -->';
            $headers[] = '<script type="text/javascript">window["mgctlbx$Pltm"] = "OpenCart";</script>';
            // add tool style link
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magic360.css" rel="stylesheet" media="screen" />';
            // add module style link
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magic360.module.css" rel="stylesheet" media="screen" />';
            // add script link
            $headers[] = '<script type="text/javascript" src="' . $jsPath . '/magic360.js"></script>';
            // add options
            $headers[] = $this->getOptionsTemplate();
            return "\r\n" . implode("\r\n", $headers) . "\r\n";
        }

        /**
         * Method to get options string
         *
         * @return string
         */
        function getOptionsTemplate() {
            $addition = '';
            if($this->params->paramExists('rows')) {
                $addition .= "\n\t\t'rows':" . $this->params->getValue('rows') . ',';
            } else {
                $addition .= "\n\t\t'rows':1,";
            }
            return "<script type=\"text/javascript\">\n\tMagic360Options = {{$addition}\n\t\t" . $this->params->serialize(true, ",\n\t\t") . "\n\t}\n</script>\n" .
                   "<script type=\"text/javascript\">\n\tMagic360Lang = {" .
                   "\n\t\t'loading-text':'" . str_replace('\'', '\\\'', $this->params->getValue('loading-text')) . "'," .
                   "\n\t\t'fullscreen-loading-text':'" . str_replace('\'', '\\\'', $this->params->getValue('fullscreen-loading-text')) . "'," .
                   "\n\t\t'hint-text':'" . str_replace('\'', '\\\'', $this->params->getValue('hint-text')) . "'," .
                   "\n\t\t'mobile-hint-text':'" . str_replace('\'', '\\\'', $this->params->getValue('mobile-hint-text')) . "'" .
                   "\n\t}\n</script>";
        }

        /**
         * Check if effect is enable
         *
         * @param mixed $data Images Data
         * @param mixed $id Product ID
         *
         * @return boolean
         */
        function isEnabled($data, $id) {
            if((int)$this->params->getValue('columns') == 0) {
                return false;
            }
            if(is_array($data)) {
                $data = count($data);
            }
            if($data < (int)$this->params->getValue('columns')) {
                return false;
            }
            $ids = trim($this->params->getValue('product-ids'));
            if($ids != 'all' && !in_array($id, explode(',', $ids))) {
                return false;
            }
            return true;
        }

        /**
         * Method to get Magic360 HTML
         *
         * @param array $data Magic360 Data
         * @param array $params Additional params
         *
         * @return string
         */
        function getMainTemplate($data, $params = array()) {
            $id = '';
            $width = '';
            $height = '';

            $html = array();

            extract($params);

            // check for width/height
            if(empty($width)) {
                $width = '';
            } else {
                $width = " width=\"{$width}\"";
            }
            if(empty($height)) {
                $height = '';
            } else {
                $height = " height=\"{$height}\"";
            }

            // check ID
            if(empty($id)) {
                $id = '';
            } else {
                $id = ' id="' . addslashes($id) . '"';
            }

            $images = array();// set of small images
            $largeImages = array();// set of large images

            // add items
            foreach($data as $item) {
                //NOTE: if there are spaces in the filename
                $images[] = str_replace(' ', '%20', $item['medium']);
                $largeImages[] = str_replace(' ', '%20', $item['img']);
            }

            $defaultImageIndex = (int)$this->params->getValue('start-column') - 1;
            if(!isset($images[$defaultImageIndex])) {
                $defaultImageIndex = 0;
            }
            $src = ' src="' . $images[$defaultImageIndex] . '"';

            $rel = $this->params->serialize();
            $rel .= 'rows:' . floor(count($data) / $this->params->getValue('columns')) . ';';
            $rel .= 'images:' . implode(' ', $images) . ';';
            if($this->params->checkValue('magnify', 'Yes') || $this->params->checkValue('fullscreen', 'Yes')) {
                $rel .= 'large-images:' . implode(' ', $largeImages) . ';';
            }
            $rel = ' data-magic360-options="' . $rel . '"';

            $html[] = '<a' . $id . ' class="Magic360" href="#"' . $rel . '>';
            $html[] = '<img class="no-sirv-lazy-load" itemprop="image"' . $src . $width . $height . ' />';
            $html[] = '</a>';

            // return HTML string
            return implode('', $html);
        }

        /**
         * Method to load defaults options
         *
         * @return void
         */
        function loadDefaults() {
            $params = array(
				"page-status"=>array("id"=>"page-status","group"=>"General","order"=>"5","default"=>"No","label"=>"Enable effect","type"=>"array","subType"=>"select","values"=>array("Yes","No"),"scope"=>"module"),
				"product-ids"=>array("id"=>"product-ids","group"=>"Magic 360","order"=>"40","default"=>"all","label"=>"Product IDs (all = all products have 360)","description"=>"Choose which products has 360 images (comma separated, e.g. 1,4,5,12,14)","type"=>"text","scope"=>"module"),
				"columns"=>array("id"=>"columns","group"=>"Magic 360","order"=>"50","default"=>"36","label"=>"Number of images on X-axis","description"=>"Enter number of images used to create 360 degree spin (left/right)","type"=>"num","scope"=>"magic360"),
				"default-spin-view"=>array("id"=>"default-spin-view","group"=>"Magic 360","order"=>"59","default"=>"Spin","label"=>"Default view","description"=>"Default gallery view on page load","type"=>"array","subType"=>"radio","values"=>array("Spin","Featured image"),"scope"=>"magic360"),
				"magnify"=>array("id"=>"magnify","group"=>"Magic 360","order"=>"60","default"=>"Yes","label"=>"Magnifier appearing effect","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"magnifier-width"=>array("id"=>"magnifier-width","group"=>"Magic 360","order"=>"70","default"=>"80%","label"=>"Magnifier size","description"=>"Magnifier size in % of small image width or fixed size in px","type"=>"text","scope"=>"magic360"),
				"magnifier-shape"=>array("id"=>"magnifier-shape","group"=>"Magic 360","order"=>"71","default"=>"inner","label"=>"Shape of magnifying glass","type"=>"array","subType"=>"radio","values"=>array("inner","circle","square"),"scope"=>"magic360"),
				"fullscreen"=>array("id"=>"fullscreen","group"=>"Magic 360","order"=>"72","default"=>"Yes","label"=>"Allow full-screen mode","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"spin"=>array("id"=>"spin","group"=>"Magic 360","order"=>"110","default"=>"drag","label"=>"Spin","description"=>"Method for spinning the image","type"=>"array","subType"=>"radio","values"=>array("drag","hover"),"scope"=>"magic360"),
				"autospin-direction"=>array("id"=>"autospin-direction","group"=>"Magic 360","order"=>"111","default"=>"clockwise","label"=>"Direction of auto-spin","type"=>"array","subType"=>"radio","values"=>array("clockwise","anticlockwise","alternate-clockwise","alternate-anticlockwise"),"scope"=>"magic360"),
				"sensitivityX"=>array("id"=>"sensitivityX","group"=>"Magic 360","order"=>"120","default"=>"50","label"=>"Sensitivity on X-axis","description"=>"Drag sensitivity on X-axis (1 = very slow, 100 = very fast)","type"=>"num","scope"=>"magic360"),
				"sensitivityY"=>array("id"=>"sensitivityY","group"=>"Magic 360","order"=>"121","default"=>"50","label"=>"Sensitivity on Y-axis","description"=>"Drag sensitivity on Y-axis (1 = very slow, 100 = very fast)","type"=>"num","scope"=>"magic360"),
				"mousewheel-step"=>array("id"=>"mousewheel-step","advanced"=>"1","group"=>"Magic 360","order"=>"121","default"=>"1","label"=>"Mousewheel step","description"=>"Number of frames to spin on mousewheel","type"=>"num","scope"=>"magic360"),
				"autospin-speed"=>array("id"=>"autospin-speed","group"=>"Magic 360","order"=>"122","default"=>"3600","label"=>"Speed of auto-spin","description"=>"e.g. 1 = fast / 10000 = slow","type"=>"num","scope"=>"magic360"),
				"smoothing"=>array("id"=>"smoothing","group"=>"Magic 360","order"=>"130","default"=>"Yes","label"=>"Smoothing","description"=>"Smoothly stop the image spinning","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"autospin"=>array("id"=>"autospin","group"=>"Magic 360","order"=>"140","default"=>"once","label"=>"Duration of automatic spin","type"=>"array","subType"=>"radio","values"=>array("once","twice","infinite","off"),"scope"=>"magic360"),
				"autospin-start"=>array("id"=>"autospin-start","group"=>"Magic 360","order"=>"150","default"=>"load,hover","label"=>"Autospin starts on","description"=>"Start automatic spin on page load, click or hover","type"=>"array","subType"=>"select","values"=>array("load","hover","click","load,hover","load,click"),"scope"=>"magic360"),
				"autospin-stop"=>array("id"=>"autospin-stop","group"=>"Magic 360","order"=>"160","default"=>"click","label"=>"Autospin stops on","description"=>"Stop automatic spin on click or hover","type"=>"array","subType"=>"radio","values"=>array("click","hover","never"),"scope"=>"magic360"),
				"initialize-on"=>array("id"=>"initialize-on","group"=>"Magic 360","order"=>"170","default"=>"load","label"=>"Initialization","description"=>"When to initialize Magic 360™ (download images).","type"=>"array","subType"=>"radio","values"=>array("load","hover","click"),"scope"=>"magic360"),
				"start-column"=>array("id"=>"start-column","advanced"=>"1","group"=>"Magic 360","order"=>"220","default"=>"1","label"=>"Start column (left/right movement)","description"=>"Image from which to start spin. 'auto' means to start from the middle","type"=>"num","scope"=>"magic360"),
				"start-row"=>array("id"=>"start-row","advanced"=>"1","group"=>"Magic 360","order"=>"230","default"=>"auto","label"=>"Start row (up/down movement)","description"=>"Position from which to start spin. 'auto' means to start from the middle","type"=>"num","scope"=>"magic360"),
				"loop-column"=>array("id"=>"loop-column","advanced"=>"1","group"=>"Magic 360","order"=>"240","default"=>"Yes","label"=>"Loop column","description"=>"Continue spin after the last image on X-axis (left/right)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"loop-row"=>array("id"=>"loop-row","advanced"=>"1","group"=>"Magic 360","order"=>"250","default"=>"No","label"=>"Loop row","description"=>"Continue spin after the last image on Y-axis (up/down)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"reverse-column"=>array("id"=>"reverse-column","advanced"=>"1","group"=>"Magic 360","order"=>"260","default"=>"No","label"=>"Reverse rotation on X-axis (left/right)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"reverse-row"=>array("id"=>"reverse-row","group"=>"Magic 360","order"=>"270","default"=>"No","label"=>"Reverse rotation on Y-axis (up/down)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"column-increment"=>array("id"=>"column-increment","advanced"=>"1","group"=>"Magic 360","order"=>"280","default"=>"1","label"=>"Column increment (left/right)","description"=>"Load only every second (2) or third (3) column so that spins load faster","type"=>"num","scope"=>"magic360"),
				"row-increment"=>array("id"=>"row-increment","advanced"=>"1","group"=>"Magic 360","order"=>"290","default"=>"1","label"=>"Row increment (up/down)","description"=>"Load only every second (2) or third (3) row so that spins load faster","type"=>"num","scope"=>"magic360"),
				"thumb-max-width"=>array("id"=>"thumb-max-width","group"=>"Positioning and Geometry","order"=>"10","default"=>"500","label"=>"Maximum width of thumbnail (in pixels)","type"=>"num","scope"=>"module"),
				"thumb-max-height"=>array("id"=>"thumb-max-height","group"=>"Positioning and Geometry","order"=>"11","default"=>"500","label"=>"Maximum height of thumbnail (in pixels)","type"=>"num","scope"=>"module"),
				"square-images"=>array("id"=>"square-images","group"=>"Positioning and Geometry","order"=>"310","default"=>"disable","label"=>"Create square images","description"=>"The white/transparent padding will be added around the image or the image will be cropped.","type"=>"array","subType"=>"radio","values"=>array("extend","crop","disable"),"scope"=>"module"),
				"headers-on-every-page"=>array("id"=>"headers-on-every-page","group"=>"Miscellaneous","order"=>"6","default"=>"No","label"=>"Include headers on all pages","type"=>"array","subType"=>"select","values"=>array("Yes","No"),"scope"=>"module"),
				"original-images"=>array("id"=>"original-images","group"=>"Miscellaneous","order"=>"7","default"=>"No","label"=>"Use original OpenCart images","type"=>"array","subType"=>"select","values"=>array("Yes","No"),"scope"=>"module"),
				"z-index"=>array("id"=>"z-index","group"=>"Miscellaneous","order"=>"80","default"=>"100","label"=>"Starting z-Index","description"=>"Adjust the stack position above/below other elements","type"=>"num","scope"=>"module"),
				"loading-text"=>array("id"=>"loading-text","group"=>"Miscellaneous","order"=>"257","default"=>"Loading...","label"=>"Loading text","description"=>"Text displayed while images are loading.","type"=>"text","scope"=>"magic360-language"),
				"fullscreen-loading-text"=>array("id"=>"fullscreen-loading-text","group"=>"Miscellaneous","order"=>"258","default"=>"Loading large spin...","label"=>"Fullscreen loading text","description"=>"Text shown while full-screen images are loading.","type"=>"text","scope"=>"magic360-language"),
				"hint"=>array("id"=>"hint","group"=>"Miscellaneous","order"=>"259","default"=>"Yes","label"=>"Show hint message","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"magic360"),
				"hint-text"=>array("id"=>"hint-text","group"=>"Miscellaneous","order"=>"260","default"=>"Drag to spin","label"=>"Hint text appears on desktop","type"=>"text","scope"=>"magic360-language"),
				"mobile-hint-text"=>array("id"=>"mobile-hint-text","group"=>"Miscellaneous","order"=>"261","default"=>"Swipe to spin","label"=>"Hint text appears on iOS/Android devices","type"=>"text","scope"=>"magic360-language"),
				"imagemagick"=>array("id"=>"imagemagick","group"=>"Miscellaneous","order"=>"550","default"=>"off","label"=>"Path to Imagemagick binaries (convert tool)","description"=>"You can set 'auto' to automatically detect imagemagick location or 'off' to disable imagemagick and use php GD lib instead","type"=>"text","scope"=>"module"),
				"image-quality"=>array("id"=>"image-quality","group"=>"Miscellaneous","order"=>"560","default"=>"75","label"=>"Quality of thumbnails and watermarked images (1-100)","description"=>"1 = worst quality / 100 = best quality","type"=>"num","scope"=>"module"),
				"watermark"=>array("id"=>"watermark","group"=>"Watermark","order"=>"10","default"=>"","label"=>"Watermark image path","description"=>"Enter location of watermark image on your server. Leave field empty to disable watermark","type"=>"text","scope"=>"module"),
				"watermark-max-width"=>array("id"=>"watermark-max-width","group"=>"Watermark","order"=>"20","default"=>"30%","label"=>"Maximum width of watermark image","description"=>"pixels = fixed size (e.g. 50) / percent = relative for image size (e.g. 50%)","type"=>"text","scope"=>"module"),
				"watermark-max-height"=>array("id"=>"watermark-max-height","group"=>"Watermark","order"=>"21","default"=>"30%","label"=>"Maximum height of watermark image","description"=>"pixels = fixed size (e.g. 50) / percent = relative for image size (e.g. 50%)","type"=>"text","scope"=>"module"),
				"watermark-opacity"=>array("id"=>"watermark-opacity","group"=>"Watermark","order"=>"40","default"=>"50","label"=>"Watermark image opacity (1-100)","description"=>"0 = transparent, 100 = solid color","type"=>"num","scope"=>"module"),
				"watermark-position"=>array("id"=>"watermark-position","group"=>"Watermark","order"=>"50","default"=>"center","label"=>"Watermark position","description"=>"Watermark size settings will be ignored when watermark position is set to 'stretch'","type"=>"array","subType"=>"select","values"=>array("top","right","bottom","left","top-left","bottom-left","top-right","bottom-right","center","stretch"),"scope"=>"module"),
				"watermark-offset-x"=>array("id"=>"watermark-offset-x","advanced"=>"1","group"=>"Watermark","order"=>"60","default"=>"0","label"=>"Watermark horizontal offset","description"=>"Offset from left and/or right image borders. Pixels = fixed size (e.g. 20) / percent = relative for image size (e.g. 20%). Offset will disable if 'watermark position' set to 'center'","type"=>"text","scope"=>"module"),
				"watermark-offset-y"=>array("id"=>"watermark-offset-y","advanced"=>"1","group"=>"Watermark","order"=>"70","default"=>"0","label"=>"Watermark vertical offset","description"=>"Offset from top and/or bottom image borders. Pixels = fixed size (e.g. 20) / percent = relative for image size (e.g. 20%). Offset will disable if 'watermark position' set to 'center'","type"=>"text","scope"=>"module")
			);
            $this->params->appendParams($params);
        }
    }

}

?>
