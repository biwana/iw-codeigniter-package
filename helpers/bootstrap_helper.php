<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author		Brian Iwana
 * @link		http://brianiwana.com
 */
// ------------------------------------------------------------------------

/**
 * Grid
 *
 * This renders a Bootstrap grid
 *
 * @access	public
 * @param	array
 * @return	string
 */
function grid($grid = array()) {
    $ret = '';
    if (is_array($grid)) {
        foreach ($grid as $row) {
            if (is_array($row)) {
                $rendered_row = '';
                foreach ($row as $column) {
                    $class = isset($column['class']) ? $column['class'] : '';
                    $class .= isset($column['xs']) ? " col-xs-{$column['xs']}" : '';
                    $class .= isset($column['sm']) ? " col-sm-{$column['sm']}" : '';
                    $class .= isset($column['md']) ? " col-md-{$column['md']}" : '';
                    $class .= isset($column['lg']) ? " col-lg-{$column['lg']}" : '';
                    $data = isset($column['data']) ? $column['data'] : '';
                    $rendered_row .= <<<COL
    <div class='$class'>
        {$data}
    </div>
COL;
                }
            } else {
                $rendered_row = $row;
            }
            $ret .= <<<ROW
<div class='row'>
    {$rendered_row}
</div>
ROW;
        }
    }
    return $ret;
}

/**
 * image_text_hoverover
 *
 * This renders a image text hoverover
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	string
 */
function image_text_hoverover($src = '', $alt = '', $class = '' ) {
    return <<<DIV
    <div class="text-hoverover">
        <div class="text-hoverover-title">
            <div class="text-hoverover-text">$alt</div>
            <div class="text-hoverover-icon"><span class="glyphicon glyphicon-new-window"></span></div>
        </div>
        <img src="$src" alt="$alt" class="img-responsive $class">
    </div>
DIV;
}

