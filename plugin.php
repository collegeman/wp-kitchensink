<?php
/*
Plugin Name: Kitchen Sink
Description: A consolidated set of examples for working with the WordPress core APIs.
Version: 1.0
Author: Aaron Collegeman, Fat Panda
Author URI: http://aaroncollegeman.com/fatpanda
Plugin URI: http://github.com/collegeman/wp-plugin
*/

/**
 * There are lots of different ways to create a namespace for your plugin.
 * My favorite is to use a class definition with a singleton-patterned
 * loader. That's OOP-speak for, "there's never more than one instance of your
 * plugin's class, and that instance has a global access point."
 * 
 * This achieves a couple of things. First, you get proper namespacing without
 * unnecessary verbosity (e.g., "myplugin_function1", "myplugin_function2").
 * Second, you ensure that some of your plugin's behavior is never
 * invoked more than once (by putting those invocations in the constructor).
 *
 * Using this pattern implies calling YourPlugin::load() somewhere after
 * the class definition is complete. It also implies using private scoping
 * on your plugin's __construct function.
 *
 * @see KitchenSink::load
 * @see KitchenSink->_construct
 * @see http://en.wikipedia.org/wiki/Singleton_pattern
 */
class KitchenSink {

  /**
   * Class constants are a good place to store values used throughout
   * your plugin. Here we are establishing several aliases for integer
   * values. Integer values are cheaper to store and faster to compare
   * than string values. String values are easier to read. Using constants,
   * then, is the best compromise between the two.
   */
  const IN_THE_MORNING = 1;
  const IN_THE_EVENING = 2;
  const AINT_WE_GOT_FUN = 4;
    
  private static $plugin;
  static function load() {
    $class = __CLASS__; 
    return ( self::$plugin ? self::$plugin : ( self::$plugin = new $class() ) );
  }

  /**
   * The constructor of the plugin should be used for attaching init hooks.
   * That is all.
   */
  private function __construct() {

     #
     # The init action is invoked by every request processed by WordPress.
     # It is fired after all WP core functionality has been loaded (routing,
     # querying, the database), after all theme functionality has been 
     # loading (i.e., after functions.php), and after the "main" script has
     # been loaded for each active plugin.
     #
     # This example explains our usage of the add_action function.
     # The callback we pass as the second argument is a method callback, using
     # $this to reference this plugin, and 'init' to reference the init method
     # of this plugin. Keeping the actions and your plugin's hooks named the
     # same, you can greatly improve upon the maintenance cycle for your plugin.
     #
     # The third and fourth arguments control the priority and number of 
     # arguments passed to your hook, respectively. The priority (which
     # defaults to 10) influences the order in which plugins are loaded 
     # (lower = earlier). Some actions produce pass more arguments than others.
     # Some, like init, pass none. You can control the number of arguments
     # your plugin receives by changing the fourth argument.
     #
     # @see http://codex.wordpress.org/Function_Reference/add_action
     #
    add_action( 'init', array( $this, 'init' ), 10, 1 );

     #
     # The admin_init action is invoked by every request to the administrative
     # sections of WordPress. If your plugin influences front- and back-end
     # functionality, you can use admin_init to cleanly separate the two
     # featuresets. 
     #
    add_action( 'admin_init', array( $this, 'admin_init' ), 10, 1 );

  }

  function init() {
    
     #
     # Some events, like admin_menu, are fired before admin_init. So to make
     # sure that our hooks are called, we set them up inside the global init
     # event hook. But to ensure that this functionality is only be invoked
     # for administrative views, we wrap these actions in the is_admin()
     # function.
     #
    if ( is_admin() ) {
      
       #
       # The admin_menu action is invoked as WordPress builds the administrative
       # menu system. You can hook here to add menu items to built-in menus,
       # or create your own menus, complete with custom icons.
       #
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    }

     #
     # Shortcodes are a way to extend macros to content creators. They 
     # range from simple (like simple token replacement), to complex
     # (like HTML tags, supporting attributes and tag content).
     #
    add_shortcode( 'today', array($this, 'today_shortcode') );

  }

  function admin_init() {

    // not doing anything yet...

  }

  function admin_menu() {
    
     #
     # Just need to add a simple bit of functionality to an existing set
     # of features? Why not add a menu item to an existing menu.
     #
    add_submenu_page(
      
      'options-general.php',
        # The first argument determines the menu to which you are attaching
        # your own menu item.
        # for Dashboard: add_submenu_page('index.php', ...)
        # for Posts: add_submenu_page('edit.php', ...)
        # for Media: add_submenu_page('upload.php', ...)
        # for Links: add_submenu_page('link-manager.php', ...)
        # for Pages: add_submenu_page('edit.php?post_type=page', ...)
        # for Comments: add_submenu_page('edit-comments.php', ...)
        # for Appearance: add_submenu_page('themes.php', ...)
        # for Plugins: add_submenu_page('plugins.php', ...)
        # for Users: add_submenu_page('users.php', ...)
        # for Tools: add_submenu_page('tools.php', ...)
        # for Settings: add_submenu_page('options-general.php', ...)

      'PHP Info',
        # When viewing your page, what title should appear in the window's
        # title bar?

      'PHP Info',
        # What label should appear in the admin menu?

      'administrator',
        # What is the minimum capability required to be able to access this
        # feature? Users with less privielges will not be able to open
        # your menu item, and will not see it in their view of the admin.
        # Here you can use any of the built-in roles (administrator, editor,
        # contributor, author, subscriber), any of the built-in capabilities
        # (edit_posts, edit_plugins, etc.), or indeed, any custom role or
        # capability introduced by a plugin (even your plugin!).

      'wp-plugin-phpinfo',
        # This should uniquely scope your plugin's behavior. Traditional
        # convention was to pass __FILE__ here, but that has since proven
        # to be a security issue. Instead, consider using your plugin's name
        # which, if registered with WordPress.org, will be unique. And if your
        # plugin creates more than one menu or menu item, use your plugin's
        # name as a prefix.

      array( $this, 'phpinfo' )
        # The last argument is the callback that should be invoked if a user
        # accesses your menu item.
    
    ); // END add_submenu_page

    // this line does exactly the same as the above, but without the comments
    //add_submenu_page( 'options-general.php', 'PHP Info', 'PHP Info', 'administrator', 'wp-plugin-phpinfo', array( $this, 'phpinfo' ) );

    // =======================================================================
    // Settings pattern #1: Add options to existing options pages
    // =======================================================================

     # ... 
    
    // =======================================================================
    // Settings pattern #2: Custom options page
    // =======================================================================

     #
     # Here we are establishing a new options page for our plugin. We begin
     # a convention here of using the __CLASS__ constant to scope custom pages
     # and custom settings. This works really well when your plugin has only
     # one custom page. If your plugin needs more, you'll need to replace
     # __CLASS__ with something equally unique, and equally reusuable 
     # (maybe prefix or suffix __CLASS__ with some other value?). 
     #
     # Keep in mind though that if your plugin requires more than one custom
     # page, you may be trying to do too much with a single plugin.
     #
    add_options_page( 'Kitchen Sink', 'Kitchen Sink', 'administrator', __CLASS__, array( $this, 'settings' ) ); 
    
     #
     # The register_setting function employs WordPress' Settings API to 
     # establish a WordPress option whose value may be updated by your plugin.
     # Settings are grouped, and it is the group name that is used later to
     # generate the form used for updating the options. 
     #
     # I have found that a good pattern for storing plugin settings is to group
     # them all into a single option. This really simplifies storage and
     # retrieval, and allows us to create a set of really useful helper 
     # functions for doing so.
     #
     # We use the __CLASS__ constant here both to provide a unique identifier
     # for referencing the setting (argument #1) and as a name for the option
     # that will be stored in the database (argument #2). The third argument
     # sets up a callback function that will be used to sanitize and validate
     # user-submited values.
     #
     # @see KitchenSink->setting
     # @see KitchenSink->id
     # @see KitchenSink->field
     # @see KitchenSink->sanitize_settings
     # @see http://codex.wordpress.org/Function_Reference/register_setting
     # @see http://codex.wordpress.org/Options_API
     #
    register_setting( __CLASS__, sprintf('%s_settings', __CLASS__), array( $this, 'sanitize_settings' ) );

    
    add_settings_section( 'basic', 'Basic Settings', array( $this, 'basic_settings' ), __CLASS__ );
    add_settings_section( 'advanced', 'Advanced Settings', array( $this, 'advanced_settings' ), __CLASS__ );

    //////////////////////////////////////////////////////////////////////////
    // Basic Settings fields
    //////////////////////////////////////////////////////////////////////////

    add_settings_field( 
      'text_field', 
      'Text Field', 
      array( $this, 'text_field' ), 
      __CLASS__, 
      'basic',
      array(
        'label_for' => $this->id('text_field', false)
      )
    );

    add_settings_field( 'h_radio_field', 'Horizontal Radio Fields', array( $this, 'h_radio_field' ), __CLASS__, 'basic' );

    add_settings_field( 'v_radio_field', 'Vertical Radio Fields', array( $this, 'v_radio_field' ), __CLASS__, 'basic' );
    
  } // END admin_menu

  function settings() {
    ?>  
      <div class="wrap">
        <?php screen_icon() ?>
        <h2>Kitchen Sink</h2>
        <form action="<?php echo admin_url('options.php') ?>" method="post">
          <?php settings_fields( __CLASS__ ) ?>
          
          <h3 class="title">Basic Settings</h3>
              
          <p>These are the most essential configuration settings. You will want
            to keep these to a minimum, all with intelligent defaults.</p>

          <table class="form-table">

            <tr>
              <th>
                <label for="<?php $this->id('text_field') ?>">Text Field</label>
              </th>
              <td>
                <input type="text" class="regular-text" id="<?php $this->id('text_field') ?>" name="<?php $this->field('text_field') ?>" value="<?php echo esc_attr( $this->setting('text_field', 'Default value') ) ?>" />
                &nbsp; <span class="description">Extra notes are useful aids to complex decision making</span>
              </td>
            </tr>

            <tr>
              <th>
                <label>Horizontal Radio Fields</label>
              </th>
              <td>
                <span>
                  <input type="radio" id="<?php $this->id('h_radio_field') ?>_1" name="<?php $this->field('h_radio_field') ?>" value="1" <?php $this->checked( 'h_radio_field', 1 ) ?> />
                  <label for="<?php $this->id('h_radio_field') ?>_1">Yes</label>
                </span>
                <span style="margin-left:20px;">
                  <input type="radio" id="<?php $this->id('h_radio_field') ?>_0" name="<?php $this->field('h_radio_field') ?>" value="0" <?php $this->checked( 'h_radio_field', 0 ) ?> />
                  <label for="<?php $this->id('h_radio_field') ?>_0">No</label>
                </span>
                <span class="description" style="margin-left:50px;">It's hard to get more specific basic than "yes or no"</span>
              </td>
            </tr>

            <tr>
              <th>
                <label>Horizontal Radio Fields</label>
              </th>
              <td>
                <p>
                  <input type="radio" id="<?php $this->id('v_radio_field') ?>_1" name="<?php $this->field('v_radio_field') ?>" value="1" <?php $this->checked( 'v_radio_field', 1 ) ?> />
                  <label for="<?php $this->id('v_radio_field') ?>_1">Yes</label>
                </p>
                <p>
                  <input type="radio" id="<?php $this->id('v_radio_field') ?>_2" name="<?php $this->field('v_radio_field') ?>" value="2" <?php $this->checked( 'v_radio_field', 2) ?> />
                  <label for="<?php $this->id('v_radio_field') ?>_2">No</label>
                </p>
                <p>
                  <input type="radio" id="<?php $this->id('v_radio_field') ?>_3" name="<?php $this->field('v_radio_field') ?>" value="3" <?php $this->checked( 'v_radio_field', 3) ?> />
                  <label for="<?php $this->id('v_radio_field') ?>_3">Maybe</label>
                  <span style="margin-left:20px;">
                    <select id="<?php $this->id('v_radio_field') ?>_options" name="<?php $this->field('v_radio_field_options') ?>" disabled="disabled">
                      <option value="<?php echo self::IN_THE_MORNING ?>" <?php $this->selected( 'v_radio_field_options', self::IN_THE_MORNING ) ?>>In the morning</option>
                      <option value="<?php echo self::IN_THE_EVENING ?>" <?php $this->selected( 'v_radio_field_options', self::IN_THE_EVENING ) ?>>In the evening</option>
                      <option value="<?php echo self::AINT_WE_GOT_FUN ?>" <?php $this->selected( 'v_radio_field_options', self::AINT_WE_GOT_FUN ) ?>>Ain't we got fun?</option>
                    </select>
                  </span>
                </p>

                <script>
                  (function($) {
                    var onChange = function() {
                      var checked = $('#<?php $this->id('v_radio_field') ?>_3').attr('checked');
                      $('#<?php $this->id('v_radio_field_options') ?>').attr('disabled', !checked);
                    };
                    $('input[name="<?php $this->field('v_radio_field') ?>"]').change(onChange);
                    onChange();
                  })(jQuery);
                </script>
              </td>
            </tr>
          
          </table>
          
          <h3 class="title">Advanced Fields</h3>  
        
          <p>The more esoteric options go here.</p>
          
          <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form>
      </div>
    <?php
  }

  function sanitize_settings($settings) {
    

     #
     # Here we validate the settings the user submits, use the Settings API to 
     # inform the user when something they've submitted is invalid.
     #

    //add_settings_error( 'text_field', $this->id('text_field', false), 'foo' );

    return $settings;
  }

  /**
   * Invoke phpinfo(), revealing details of the current PHP configuration.
   */
  function phpinfo() {
    ob_start(); phpinfo(); $phpinfo = ob_get_clean();    
    ?>
      <style>
        #phpinfo pre {  display:block; width: 740; height: 100%; overflow: hidden; }
      </style>
      <div class="wrap" id="phpinfo">
        <?php screen_icon() ?>
        <h2>PHP Info</h2>
        <pre><?php echo strip_tags(str_replace('</td>', '&nbsp;', $phpinfo)) ?></pre>
      </div>
    <?php
  }

  /**
   * This is an example of a shortcode handler. It accepts two arguments:
   * an array of name/value paired attributes, reflecting the attributes 
   * the user used in deploying the shortcode; and a string, reflecting
   * any content the user submitted between opening and closing tags.
   *
   * @param array $atts
   * @param string $content
   * @see KitchenSink->init
   */
  function today_shortcode($atts, $content = '') {
      
    #
    # The WP core provides a special function for simultaneously filtering
    # and establishing default attributes. Any attributes not defined in
    # the default list are removed from the given input, and any not existing
    # in the given input are filled-in with the default.
    #
    # Our example shortcode allows for one attribute, "format", which is
    # expected to be a valid date formatting string, suitable for passing
    # into the date() function.
    #
    extract( $atts = shortcode_atts( array(
      'format' => 'M d, Y'
    ), $atts ) );

    #
    # Shortcode functions should return the text that should replace the 
    # shortcode in the content.
    # @see http://php.net/manual/en/function.date.php
    # @see http://codex.wordpress.org/Function_Reference/current_time
    #
    return date( $format, current_time('timestamp') );

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
KitchenSink::load();

#
# Load global functions (e.g., template functions)
#
require(dirname(__FILE__).'/globals.php');