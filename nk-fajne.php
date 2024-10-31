<?php
/*
Plugin Name: NK Fajne!
Plugin URI: http://wordpress.org/extend/plugins/nk-fajne
Description: The NK Fajne plugin adds the Fajne! button to your site.
Version: 1.0.0
Author: Marek Ziółkowski
Author URI: http://blog.powsinoga.pl
License: GPL2

Copyright 2012  Marek Ziółkowski

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook(__FILE__, 'nk_fajne_activate');

/**
 * W zależności od miejsca wywołania i ustawień określa, czy przycisk ma się pojawić.
 *
 * @return bool
 */
function nk_fajne_should_we_show() {
  $nk_fajne_options = get_option('nk-fajne-options');
  
  if (!in_the_loop()) {
    return false;
  }
	
  if (is_single() && $nk_fajne_options['nk-fajne-show-on-post'] != '1') {
    return false;
  }

  if (is_page() && $nk_fajne_options['nk-fajne-show-on-page'] != '1') {
    return false;
  }	

  if (is_home() && $nk_fajne_options['nk-fajne-show-on-home'] != '1') {
    return false;
  }

  if ((is_category() || is_tag() || is_search() || is_author() || is_archive()) && $nk_fajne_options['nk-fajne-show-on-lists'] != '1') {
    return false;
  }
  
  return true;
}

/**
 * Załącza skrypty potrzebne do wypisywania i używania przycisku Fajne!.
 * Uruchamiana podczas załączania skryptów JS'owych.
 */
function nk_fajne_scripts() {

  if (is_ssl()) {
    $script_url = 'https://nk.pl/script/nk_widgets/nk_widget_fajne_embed'; 
  } else {
    $script_url = 'http://0.s-nk.pl/script/nk_widgets/nk_widget_fajne_embed';    
  }                                                                           

  echo '<script type="text/javascript" src="'.$script_url.'"></script>';
}
add_action( 'wp_print_scripts', 'nk_fajne_scripts'); 

/**
 * Wzbogaca content o przycisk Fajne!. 
 * W zależności od ustawień dodaje go przed lub po treści.
 *
 * @param string $content Oryginalna treść.
 * @return string Treść wzbogacona.
 */
function nk_fajne_content($content) {
    
  if (!nk_fajne_should_we_show()) {
    return $content;
  }
  
  if (!is_admin()) {
    $link = get_permalink();
  } else {
    $link = site_url();
  }

  $nk_fajne_options = get_option('nk-fajne-options');
  
  if (has_post_thumbnail()) {
    // jeśli post ma miniaturę, wydobywamy jej URL
    $attachment_id = get_post_thumbnail_id();
    $attachement_attrs = wp_get_attachment_image_src($attachment_id);    
    $image_url = $attachement_attrs[0];  
  } else {
    // zostawiamy puste - NK samo znajdzie coś ciekawego
    $image_url = '';
  }                                  
  
  $nk_button = '<div class="nk_fajne_button"><script>
  new nk_fajne({
    url: "'.$link.'",
    type: '.$nk_fajne_options['nk-fajne-type'].',
    color: '.$nk_fajne_options['nk-fajne-color'].',
    title: "'.get_the_title().'",
    image: "'.$image_url.'",
    description: ""
  });
  </script></div>';
      
      
  switch ($nk_fajne_options['nk-fajne-position']) {
    case '1': return $nk_button . $content; break;
    case '2': return $content . $nk_button; break;
    case '3': return $nk_button . $content . $nk_button; break;
    default: return $nk_button . $content . $nk_button;   
  }
    
}
add_action( 'the_content', 'nk_fajne_content');


// Obsługa settingsów (opcji)
require_once( dirname( __FILE__ ) . '/nk-fajne-settings.php' );

?>
