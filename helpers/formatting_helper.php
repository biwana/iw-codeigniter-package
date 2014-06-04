<?php

function char_limit($string = '', $limit = 50, $ellipsis = '...') {
  return strlen($string) > $limit ? substr($string, 0, $limit - 3) . $ellipsis : $string;
}

function cell_data($value = null, $if_empty = NULL) {
  $trimmed = trim($value);
  $ret = empty($trimmed) ? (is_null($if_empty) ? '&nbsp;' : $if_empty) : $trimmed;
  return $ret;
}