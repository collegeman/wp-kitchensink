<?php
/*
Plugin Name: Kitchen Sink Framework
Description: This version of the Kitchen Sink plugin has only the boilerplated functionality - no comments,a nd no examples
Version: 1.0
Author: Aaron Collegeman, Fat Panda
Author URI: http://aaroncollegeman.com/fatpanda
Plugin URI: http://github.com/collegeman/wp-kitchensink
*/

class KitchenSinkFramework {

  private static $plugin;
  static function load() {
    $class = __CLASS__; 
    return ( self::$plugin ? self::$plugin : ( self::$plugin = new $class() ) );
  }

  private function __construct() {
    add_action( 'init', array( $this, 'init' ), 10 );
    add_action( 'admin_init', array( $this, 'admin_init' ), 10 );

  }

  function init() {
    if ( is_admin() ) {
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    // add_shortcode( 'today', array($this, 'today_shortcode') );
  }

  function admin_init() {}

  function admin_menu() {  
    add_options_page( 'Kitchen Sink', 'Kitchen Sink', 'administrator', __CLASS__, array( $this, 'settings' ) ); 
    register_setting( __CLASS__, sprintf('%s_settings', __CLASS__), array( $this, 'sanitize_settings' ) );
  }

  function settings() {
    ?>  
      <div class="wrap">
        <?php screen_icon() ?>
        <h2>Kitchen Sink</h2>
        <form action="<?php echo admin_url('options.php') ?>" method="post">
          <?php settings_fields( __CLASS__ ) ?>
          
          <?php ### your fields go here ### ?>

          <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form>
      </div>
    <?php
  }

  function sanitize_settings($settings) {
    
    // add_settings_error( 'text_field', $this->id('text_field', false), 'foo' );

    return $settings;
  }

  // ===========================================================================
  // Helper functions - Provided to your plugin, courtesy of wp-kitchensink
  // http://github.com/collegeman/wp-kitchensink
  // ===========================================================================
  
  /**
   * This function provides a convenient way to access your plugin's settings.
   * The settings are serialized and stored in a single WP option. This function
   * opens that serialized array, looks for $name, and if it's found, returns
   * the value stored there. Otherwise, $default is returned.
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  function setting($name, $default = null) {
    $settings = get_option(sprintf('%s_settings', __CLASS__), array());
    return isset($settings[$name]) ? $settings[$name] : $default;
  }

  /**
   * Use this function in conjunction with Settings pattern #3 to generate the
   * HTML ID attribute values for anything on the page. This will help
   * to ensure that your field IDs are unique and scoped to your plugin.
   *
   * @see settings.php
   */
  function id($name, $echo = true) {
    $id = sprintf('%s_settings_%s', __CLASS__, $name);
    if ($echo) {
      echo $id;
    }
    return $id;
  }

  /**
   * Use this function in conjunction with Settings pattern #3 to generate the
   * HTML NAME attribute values for form input fields. This will help
   * to ensure that your field names are unique and scoped to your plugin, and
   * named in compliance with the setting storage pattern defined above.
   * 
   * @see settings.php
   */
  function field($name, $echo = true) {
    $field = sprintf('%s_settings[%s]', __CLASS__, $name);
    if ($echo) {
      echo $field;
    }
    return $field;
  }
  
  /**
   * A helper function. Prints 'checked="checked"' under two conditions:
   * 1. $field is a string, and $this->setting( $field ) == $value
   * 2. $field evaluates to true
   */
  function checked($field, $value = null) {
    if ( is_string($field) ) {
      if ( $this->setting($field) == $value ) {
        echo 'checked="checked"';
      }
    } else if ( (bool) $field ) {
      echo 'checked="checked"';
    }
  }

  /**
   * A helper function. Prints 'selected="selected"' under two conditions:
   * 1. $field is a string, and $this->setting( $field ) == $value
   * 2. $field evaluates to true
   */
  function selected($field, $value = null) {
    if ( is_string($field) ) {
      if ( $this->setting($field) == $value ) {
        echo 'selected="selected"';
      }
    } else if ( (bool) $field ) {
      echo 'selected="selected"';
    }
  }
  
}

#
# Initialize our plugin
#
KitchenSinkFramework::load();

#
# Load global functions (e.g., template functions)
#
require_once(dirname(__FILE__).'/globals.php');