<?php

/**
 * Funkcja odpalana podczas aktywacji wtyczki.
 * Dodaje domyślne ustawienia.
 */
function nk_fajne_activate() {
  $nk_fajne_options = array(
    'nk-fajne-color'        => 0,
    'nk-fajne-type'         => 0,
    'nk-fajne-position'     => 3,
    'nk-fajne-show-on-page'  => null,
    'nk-fajne-show-on-post'  => 1,
    'nk-fajne-show-on-home'  => 1,
    'nk-fajne-show-on-lists'=> 1,
    );
  add_option( 'nk-fajne-options', $nk_fajne_options );
}

/**
 * Dodaje nowe menu z ustawieniami.
 */
function nk_fajne_menu() {
  add_options_page('NK Fajne Ustawienia', 'NK Fajne!', 'manage_options', 'nk-fajne-settings'/*slug*/, 'nk_fajne_options');
  add_action('admin_init', 'nk_fajne_register_settings');
}
add_action('admin_menu', 'nk_fajne_menu');

/**
 * Definiowanie ustawień wtyczki
 */
function nk_fajne_register_settings() {
  $slug = 'nk-fajne-settings'; // unikalny identyfikator strony z opcjami
  register_setting( 'nk-fajne-options-group', 'nk-fajne-options' );
  add_settings_section('nk-fajne-appearance', 'Ustawienia wyglądu', 'nk_fajne_appearance_echo', $slug);
  add_settings_field('nk-fajne-color', 'Kolorystyka:', 'nk_fajne_color_echo', $slug, 'nk-fajne-appearance');
  add_settings_field('nk-fajne-type', 'Układ:', 'nk_fajne_type_echo', $slug, 'nk-fajne-appearance');
  
  add_settings_section('nk-fajne-local', 'Ustawienia położenia', 'nk_fajne_local_echo', $slug);
  add_settings_field('nk-fajne-position', 'Umiejscowienie:', 'nk_fajne_position_echo', $slug, 'nk-fajne-local');
  add_settings_field('nk-fajne-show-on-page', 'Pokazuj na stronach:', 'nk_fajne_checkbox_echo', $slug, 'nk-fajne-local', 'nk-fajne-show-on-page');
  add_settings_field('nk-fajne-show-on-post', 'Pokazuj we wpisach:', 'nk_fajne_checkbox_echo', $slug, 'nk-fajne-local', 'nk-fajne-show-on-post');
  add_settings_field('nk-fajne-show-on-home', 'Pokazuj na stronie głównej:', 'nk_fajne_checkbox_echo', $slug, 'nk-fajne-local', 'nk-fajne-show-on-home');
  add_settings_field('nk-fajne-show-on-lists', 'Pokazuj na listach (kategoria, tag, archiwum, wyszukiwarka itp.):', 'nk_fajne_checkbox_echo', $slug, 'nk-fajne-local', 'nk-fajne-show-on-lists');
}

/**
 * Wypisuje nagłówek sekcji 'Wstawienia wyglądu'
 */
function nk_fajne_appearance_echo() {
  echo '<p>Tutaj możesz skonfigurować wygląd przycisku Fajne!.</p>';
}

/**
 * Wypisuje nagłówek sekcji 'Ustawienia położenia'
 */
function nk_fajne_local_echo() {
  echo '<p>W tej sekcji określisz miejsce pojawiania się przycisku.</p>';
}

/**
 * Wypisuje kontrolkę ustawień kolorystyki.
 */
function nk_fajne_color_echo() {
  $colors = array(
    0 => 'jasna',
    1 => 'ciemna',
  );                            
  $stored = get_option('nk-fajne-options');                                                         
  echo '<select name="nk-fajne-options[nk-fajne-color]" id="nk-fajne-color">';
  foreach ($colors as $value=>$label) {
    echo '<option value="'.$value.'"'.($value==$stored['nk-fajne-color']?' selected="selected"':'').'>'.$label.'</option>';
  }
  echo '</select>';
}

/**
 * Wypisuje kontrolkę ustawień układu (typu przycisku).
 */
function nk_fajne_type_echo() {
  $stored = get_option('nk-fajne-options');
  $items = array(
    0 => '0.png',
    1 => '1.png',
    2 => '2.png',
    3 => '3.png',
    4 => '4.png',
    5 => '5.png'
  );
  foreach($items as $value => $image) {
    $checked = ($stored['nk-fajne-type']==$value) ? ' checked="checked" ' : '';
    echo "<div style='height: 41px;'><label><input style='margin-right: 10px;' ".$checked." value='$value' name='nk-fajne-options[nk-fajne-type]' type='radio' /><img style='vertical-align: middle;'  src='".WP_PLUGIN_URL.'/nk-fajne/images/'.$image."' /></label></div>";
  }
}

/**
 * Wypisuje kontrolkę ustawień lokalizacji przycisku.
 */
function nk_fajne_position_echo() {
  $stored = get_option('nk-fajne-options');
  $items = array(
    1 => 'nad treścią',
    2 => 'pod treścią',
    3 => 'nad i pod treścią',
  );
  foreach($items as $value => $label) {
    $checked = ($stored['nk-fajne-position']==$value) ? ' checked="checked" ' : '';
    echo "<div><label><input style='margin-right: 10px;' ".$checked." value='$value' name='nk-fajne-options[nk-fajne-position]' type='radio' />$label</label></div>";
  }
}

/**
 * Generyczna kontrolka (checkbox).
 * Wypisuje checkbox o podajnej nazwie zmiennej.
 *
 * @param string Nazwa opcji
 */
function nk_fajne_checkbox_echo($name) {
  $options = get_option('nk-fajne-options');

  if($options[$name]) { $checked = ' checked="checked" '; }
  echo "<input ".$checked." id='id-$name' name='nk-fajne-options[$name]' value='1' type='checkbox' />";
}


/**
 * Wypisuje treść strony z ustawieniami.
 */
function nk_fajne_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
?>
  <div class="wrap">
  <h2>NK Fajne! - ustawienia</h2>
  <form method="post" action="options.php">
  <?php settings_fields('nk-fajne-options-group' );
   do_settings_sections('nk-fajne-settings'/*slug*/);
  ?> 
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
    </p>  
  </form>
  </div>
<?php  
}
?>
