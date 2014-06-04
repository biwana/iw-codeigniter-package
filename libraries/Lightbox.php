<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author		Brian Iwana
 * @link		http://brianiwana.com
 * 
 * Uses:
 * Lightbox for Bootstrap 3 by @ashleydw
 * https://github.com/ashleydw/lightbox
 */
// ------------------------------------------------------------------------

class Lightbox {
    
    protected $CI;

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->layout->add_js('ekko-lightbox.min.js');
        $this->CI->layout->add_js('ekko-lightbox-init.js');
        $this->CI->layout->add_css('ekko-lightbox.min.css');
    }

    /**
     * Render
     *
     * This function creates a lightbox
     *
     * @access	public
     * @param	string
     * @param       bool
     * @return	string
     */
    function render($src = '', $index_page = FALSE) {
        $link = array();
        if (is_array($src)) {
            $link = $src;
            $link['class'] = isset($src['class']) ? $src['class'] . ' img-responsive' : 'img-responsive';
            $title = isset($src['title']) ? $src['title'] : '';
            $url = isset($src['src']) ? $src['src'] : '';
            $link['src'] = isset($src['thumb']) ? $src['thumb'] : $url;
            unset($link['thumb']);
        } else {
            $link['class'] = 'img-responsive';
            $link['src'] = $src;
            $title = '';
            $url = $src;
        }
        $image = img($link, $index_page);
        return <<<EOF
        <a href="$url" data-toggle="lightbox" data-title="$title" class="thumbnail">
            $image
        </a>
EOF;
    }

}