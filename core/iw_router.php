<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * MY_Router
 *
 * Extended the core CI_Router class in order to force a different naming
 * convention for controllers.
 *
 */

class IW_Router extends CI_Router {
    /*
     * Suffix in controller name
     *
     * @var String
     */

    private $_suffix = "_controller";
    var $error_controller = 'default_controller';
    var $error_method_404 = 'error_404';

    /*
     * Call the parent constructor
     *
     * This is a requirement for extending base CI core class. Just abiding by
     * the rules.
     *
     * @access  public
     * @return  void
     */

    public function __construct() {
        parent::__construct();
    }

    /**
     * Validates the supplied segments.  Attempts to determine the path to
     * the controller.
     *
     * @access   private
     * @param    array
     * @return   array
     */
    function _validate_request($segments) {
        // Retain the original segments
        $orgSegments = array_slice($segments, 0);

        // Add suffix to the end
        $segments[0] = strtolower($segments[0] . $this->_suffix);
        $segments[0] = str_replace('-', '_', $segments[0]);
        // Does the requested controller exist in the root folder?
        if (file_exists(APPPATH . 'controllers/' . $segments[0] . EXT)) {
            return $segments;
        }

        // OK, revert to the original segment
        $segments[0] = $orgSegments[0];

        // Is the controller in a sub-folder?
        if (is_dir(APPPATH . 'controllers/' . $segments[0])) {
            // Set the directory and remove it from the segment array
            $this->set_directory($segments[0]);
            $segments = array_slice($segments, 1);

            if (count($segments) > 0) {
                // Add suffix to the end
                $segments[0] = strtolower($segments[0] . $this->_suffix);

                // Does the requested controller exist in the sub-folder?
                if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $segments[0] . EXT)) {
                    error_404($this->fetch_directory() . $segments[0]);
                }
            } else {
                // Add suffix to the end
                $this->default_controller = strtolower($this->default_controller . $this->_suffix);

                $this->set_class($this->default_controller);
                $this->set_method('index');

                // Does the default controller exist in the sub-folder?
                if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $this->default_controller . EXT)) {
                    $this->directory = '';
                    return array();
                }
            }

            return $segments;
        }

        // Can't find the requested controller...
        return $this->error_404();
    }

    function error_404() {
        $this->directory = "";
        $segments = array();
        $segments[] = $this->error_controller;
        $segments[] = $this->error_method_404;
        return $segments;
    }

    function fetch_class() {
        // if method doesn't exist in class, change
        // class to error and method to error_404
        $this->check_method();

        return $this->class;
    }

    function check_method() {
        $ignore_remap = true;

        $class = $this->class;
        if (class_exists($class)) {
            // methods for this class
            $class_methods = array_map('strtolower', get_class_methods($class));

            // ignore controllers using _remap()
            if ($ignore_remap && in_array('_remap', $class_methods)) {
                return;
            }
            $this->method = str_replace('-', '_', $this->method);

            if (!in_array(strtolower($this->method), $class_methods)) {
                $this->directory = "";
                $this->class = $this->error_controller;
                $this->method = $this->error_method_404;
                include(APPPATH . 'controllers/' . $this->fetch_directory() . $this->error_controller . EXT);
            }
        }
    }

    function fetch_page_id() {
        $classname = str_replace('_controller', '', $this->fetch_class());
        $method = $this->fetch_method();
        $method = $method == 'index' ? '' : '/' . $method;
//		var_dump($classname.$method);
        return $classname . $method;
    }
    
    function fetch_page_class() {
        $classname = str_replace('_controller', '', $this->fetch_class());
        return $classname;
    }
    
    function show_404() {
        include(APPPATH . 'controllers/' . $this->fetch_directory() . $this->error_controller . EXT);
        call_user_func(array($this->error_controller, $this->error_method_404));
    }

}