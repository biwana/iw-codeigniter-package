<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author		Brian Iwana
 * @link		http://brianiwana.com
 */
// ------------------------------------------------------------------------

class Layout {

    protected $CI;
    protected $title = array();
    protected $app_name = '';
    protected $keywords = array();
    protected $layout = 'default';
    protected $js = array();
    protected $css = array();
    protected $config = array();
    protected $nav = array();
    protected $nav_class = '';
    
    public $page_id = null;

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('flash');
        $this->app_name = $this->CI->config->item('app_name');
        $this->nav = $this->CI->config->item('nav');
        include(APPPATH . 'third_party/iw/config/layouts' . EXT);
        if (isset($layout)) {
            $this->config = $layout;
            $this->nav_class = isset($this->config[$this->layout]['nav_class']) ? $this->config[$this->layout]['nav_class'] : '';
        }
    }

    /**
     * Title
     *
     * This function sets the title
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function title($title) {
        $this->title[] = $title;
    }

    /**
     * Get Title
     *
     * This function retrieves the title
     *
     * @access	public
     * @return	string
     */
    public function get_title() {
        return implode(' | ', array_merge($this->title, array($this->app_name)));
    }
    
    /**
     * Keywords
     *
     * This function sets the keywords
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function keywords($keywords) {
        foreach ($keywords as $i => $k) {
            $this->keywords[$i] = $k;
        }
    }    

    /**
     * Get Keywords
     *
     * This function retrieves the keywords
     *
     * @access	public
     * @return	string
     */
    public function get_keywords() {
        return implode(', ', $this->keywords);
    }

    /**
     * Set Layout
     *
     * This function sets the layout
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function layout($layout) {
        $this->layout = $layout;
    }

    /**
     * Set Menu
     *
     * This function sets the menu
     *
     * @access	public
     * @param	array
     * @return	void
     */
    public function set_nav($nav = array()) {
        foreach ($nav as $i => $k) {
            $this->nav[$i] = $k;
        }
    }    
    /**
     * Unset Menu
     *
     * This function sets the menu
     *
     * @access	public
     * @param	array
     * @return	void
     */
    public function unset_nav($item) {
        unset($this->nav[$item]);
    }    
    /**
     * Render Menu 
     *
     * This function renders the menu
     *
     * @access	public
     * @param	string
     * @param	array
     * @return	void
     */
    public function render_nav($nav = array()) {
        $this->CI->load->library('nav');
        $this->CI->nav->initialize(array(
            'ul_class' => $this->nav_class,
                ));
        $page_id = isset($this->page_id) ? $this->page_id : $this->CI->router->fetch_page_id();
        return $this->CI->nav->render($nav, $page_id);
    }

    /**
     * Render Layout
     *
     * This function renders the final layout
     *
     * @access	public
     * @param	string
     * @param	array
     * @return	void
     */
    public function render_layout($args = array()) {

        $args['title'] = $this->get_title();
        $args['keywords'] = $this->get_keywords();
        $args['css'] = $this->get_css();
        $args['js'] = $this->get_js();
        $args['content'] = isset($args['content']) ? $args['content'] : '';
        $args['menu'] = $this->render_nav($this->nav);

        $this->CI->load->view('layout/' . $this->layout, $args);
    }

    /**
     * Render
     *
     * This function renders the view
     *
     * @access	public
     * @param	string
     * @param	array
     * @return	void
     */
    public function render($page = 'simple/default', $args = array()) {
        $data = $args;
        $data['content'] = $this->CI->load->view($page, $args, TRUE);
        $flash = $this->CI->flash->get();
        $data['message'] = isset($data['message']) ? $data['message'].$flash : $flash;
        $this->render_layout($data);
    }

    /**
     * Add Javascript
     *
     * This function adds Javascript to be rendered
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function add_js($js) {
        $this->js[] = $js;
    }

    /**
     * Add CSS
     *
     * This function add CSS to be rendered
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function add_css($css) {
        $this->css[] = $css;
    }

    /**
     * Get Javascript
     *
     * This function renders Javascript
     *
     * @access	public
     * @return	string
     */
    public function get_js() {
        $ret = '';
        $default_js = isset($this->config[$this->layout]['default_js']) ? $this->config[$this->layout]['default_js'] : array();
        $scripts = array_merge($default_js, $this->js);
        foreach ($scripts as $js) {
            $src = $this->get_asset($js, 'js');
            $ret .= "<script src='$src' type='text/javascript'></script>\n";
        }
        return $ret;
    }

    /**
     * Get CSS
     *
     * This function renders CSS
     *
     * @access	public
     * @return	string
     */
    public function get_css() {
        $ret = '';
        $default_css = isset($this->config[$this->layout]['default_css']) ? $this->config[$this->layout]['default_css'] : array();
        $scripts = array_merge($default_css, $this->css);
        foreach ($scripts as $css) {
            $src = $this->get_asset($css, 'css');
            $ret .= "<link href='$src' rel='stylesheet' type='text/css' />\n";
        }
        return $ret;
    }

    /**
     * Get Asset
     *
     * This function finds the asset
     *
     * @access	public
     * @param   string
     * @param   string
     * @return	string
     */
    public function get_asset($file, $type) {
        $src = '';
        if (file_exists(FCPATH . "/assets/$type/$file")) {
            $src = base_url("/assets/$type/$file");
        } elseif (file_exists(FCPATH . "/assets/layout/{$this->layout}/$type/{$file}")) {
            $src = base_url("assets/layout/{$this->layout}/$type/{$file}");
        } else {
            $src = $file;
        }
        return $src;
    }

}