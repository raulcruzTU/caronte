<?php

    function hex2rgba($color, $opacity = false) {

        $default = 'rgb(0,0,0)';

        if(empty($color))   return $default;


        if ($color[0] == '#' )  $color = substr( $color, 1 );

        if(strlen($color) == 6){
            $hex = array( $color[0].$color[1], $color[2].$color[3], $color[4].$color[5] );
        }elseif( strlen( $color ) == 3 ){
            $hex = array( $color[0].$color[0], $color[1].$color[1], $color[2].$color[2] );
        }else{
            return $default;
        }

        $rgb =  array_map('hexdec', $hex);

        if($opacity){
            if(abs($opacity) > 1)   $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        }else{
            $output = 'rgb('.implode(",",$rgb).')';
        }

        return $output;
  }
  
  function closetags($html)
  {
      preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
      $openedtags = $result[1];
      preg_match_all('#</([a-z]+)>#iU', $html, $result);
      $closedtags = $result[1];
      $len_opened = count($openedtags);
      if (count($closedtags) == $len_opened) {
          return $html;
      }
      $openedtags = array_reverse($openedtags);
      for ($i = 0; $i < $len_opened; $i++) {
          if (!in_array($openedtags[$i], $closedtags)) {
              $html .= '</' . $openedtags[$i] . '>';
          } else {
              unset($closedtags[array_search($openedtags[$i], $closedtags)]);
          }
      }
      return $html;
  }
  