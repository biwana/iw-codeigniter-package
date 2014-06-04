<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | Layouts
  | -------------------------------------------------------------------------
  | This file sets the parameters for the layouts
  |
 */

$layout['default']['default_js'] = array(
    '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js',
    'bootstrap.min.js',
    'layout.js',
);

$layout['default']['default_css'] = array(
    'reset.css',
    'bootstrap.min.css',
    'layout.css',
);

$layout["default"]['nav_class'] = "nav navbar-right top-bar";
