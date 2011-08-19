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
    
  static $plugin;
  static function load() {
    $class = __CLASS__; 
    return ( self::$plugin ? self::$plugin : ( self::$plugin = new $class() ) );
  }

  /**
   * The constructor of the plugin should be used for attaching init hooks.
   * That is all.
   */
  private function __construct() {

    /**
     * The init action is invoked by every request processed by WordPress.
     * It is fired after all WP core functionality has been loaded (routing,
     * querying, the database), after all theme functionality has been 
     * loading (i.e., after functions.php), and after the "main" script has
     * been loaded for each active plugin.
     *
     * This example explains our usage of the add_action function.
     * The callback we pass as the second argument is a method callback, using
     * $this to reference this plugin, and 'init' to reference the init method
     * of this plugin. Keeping the actions and your plugin's hooks named the
     * same, you can greatly improve upon the maintenance cycle for your plugin.
     *
     * The third and fourth arguments control the priority and number of 
     * arguments passed to your hook, respectively. The priority (which
     * defaults to 10) influences the order in which plugins are loaded 
     * (lower = earlier). Some actions produce pass more arguments than others.
     * Some, like init, pass none. You can control the number of arguments
     * your plugin receives by changing the fourth argument.
     *
     * @see http://codex.wordpress.org/Function_Reference/add_action
     */
    add_action( 'init', array( $this, 'init' ), 10, 1 );

    /**
     * The admin_init action is invoked by every request to the administrative
     * sections of WordPress. If your plugin influences front- and back-end
     * functionality, you can use admin_init to cleanly separate the two
     * featuresets. 
     */
    add_action( 'admin_init', array( $this, 'admin_init' ), 10, 1 );

  }

  function init() {
    
    /**
     * Some events, like admin_menu, are fired before admin_init. So to make
     * sure that our hooks are called, we set them up inside the global init
     * event hook. But to ensure that this functionality is only be invoked
     * for administrative views, we wrap these actions in the is_admin()
     * function.
     */
    if ( is_admin() ) {
      
      /**
       * The admin_menu action is invoked as WordPress builds the administrative
       * menu system. You can hook here to add menu items to built-in menus,
       * or create your own menus, complete with custom icons.
       */
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    }

  }

  function admin_init() {

    // not doing anything yet...

  }

  function admin_menu() {
    /**
     * Just need to add a simple bit of functionality to an existing set
     * of features? Why not add a menu item to an existing menu.
     */
    add_submenu_page(
      
      'options-general.php',
        // The first argument determines the menu to which you are attaching
        // your own menu item.
        // for Dashboard: add_submenu_page('index.php', ...)
        // for Posts: add_submenu_page('edit.php', ...)
        // for Media: add_submenu_page('upload.php', ...)
        // for Links: add_submenu_page('link-manager.php', ...)
        // for Pages: add_submenu_page('edit.php?post_type=page', ...)
        // for Comments: add_submenu_page('edit-comments.php', ...)
        // for Appearance: add_submenu_page('themes.php', ...)
        // for Plugins: add_submenu_page('plugins.php', ...)
        // for Users: add_submenu_page('users.php', ...)
        // for Tools: add_submenu_page('tools.php', ...)
        // for Settings: add_submenu_page('options-general.php', ...)

      'PHP Info',
        // When viewing your page, what title should appear in the window's
        // title bar?

      'PHP Info',
        // What label should appear in the admin menu?

      'administrator',
        // What is the minimum capability required to be able to access this
        // feature? Users with less privielges will not be able to open
        // your menu item, and will not see it in their view of the admin.
        // Here you can use any of the built-in roles (administrator, editor,
        // contributor, author, subscriber), any of the built-in capabilities
        // (edit_posts, edit_plugins, etc.), or indeed, any custom role or
        // capability introduced by a plugin (even your plugin!).

      'wp-plugin-phpinfo',
        // This should uniquely scope your plugin's behavior. Traditional
        // convention was to pass __FILE__ here, but that has since proven
        // to be a security issue. Instead, consider using your plugin's name
        // which, if registered with WordPress.org, will be unique. And if your
        // plugin creates more than one menu or menu item, use your plugin's
        // name as a prefix.

      array( $this, 'phpinfo' )
        // The last argument is the callback that should be invoked if a user
        // accesses your menu item.
    
    ); // add_submenu_page

    // this line does exactly the same as the above, but without the comments
    //add_submenu_page( 'options-general.php', 'PHP Info', 'PHP Info', 'administrator', 'wp-plugin-phpinfo', array( $this, 'phpinfo' ) );

     //////////////////////////////////////////////////////////////////////////
    // Settings pattern #1: Add options to existing options pages
    //////////////////////////////////////////////////////////////////////////
    
    //////////////////////////////////////////////////////////////////////////
    // Settings pattern #2: Custom options page
    //////////////////////////////////////////////////////////////////////////

    add_options_page( 'Kitchen Sink', 'Kitchen Sink', 'administrator', __CLASS__, array( $this, 'settings' ) ); 
    
    /**
     * The register_setting function employs WordPress' Settings API to 
     * establish a WordPress option whose value may be updated by your plugin.
     * Settings are grouped, and it is the group name that is used later to
     * generate the form used for updating the options. 
     *
     * I have found that a good pattern for storing plugin settings is to group
     * them all into a single option. This really simplifies storage and
     * retrieval, and allows us to create a set of really useful helper 
     * functions.
     *
     * Here we continue with the convention of using the __CLASS__ constant to
     * extend the namespace anchored by our plugin's class name.
     * You can use any value you wish, especially if your plugin has more than
     * one group of settings, but if it does have more than one group of
     * settings, you're probably making your plugin less minimalist than
     * it could be.
     *
     * @see KitchenSink->setting
     * @see KitchenSink->id
     * @see KitchenSink->field
     * @see http://codex.wordpress.org/Function_Reference/register_setting
     */
    register_setting( __CLASS__, sprintf('%s_settings', __CLASS__), array( $this, 'sanitize_settings' ) );

    /**
     * The register setting_section function also employs WordPress' 
     * Settings API to create visually separate sections of form fields.
     * The most basic line of separation is "Basic" and "Advanced," which is
     * demonstrated below.
     *
     * The callback argument (argument #3) should be a funcion that produces
     * content to display in this settings section. Here we begin a convention
     * of referring to functions that don't actually exist, but are instead
     * provided dynamically by this classes's __call function.
     *
     * Again here we see the convention of using the __CLASS__ constant to 
     * keep all of these things related and scoped to our plugin.
     *
     * @see http://codex.wordpress.org/Function_Reference/add_settings_section
     * @see KitchenSink->__call
     */
    add_settings_section( 'basic', 'Basic Settings', array( $this, 'basic_settings' ), __CLASS__ );
    add_settings_section( 'advanced', 'Advanced Settings', array( $this, 'advanced_settings' ), __CLASS__ );

    add_settings_field( 'text_field', 'Text Field', array( $this, 'text_field' ), __CLASS__, 'basic' );
    
  }

  function settings() {
    ?>  
      <div class="wrap">
        <?php screen_icon() ?>
        <h2>Kitchen Sink</h2>
        <form action="<?php echo admin_url('options.php') ?>" method="post">
          <?php settings_fields( __CLASS__ ) ?>
          <?php do_settings_sections( __CLASS__ ) ?>
          <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form>
      </div>
    <?php
  }

  /**
   * This function is reserved for callbacks that are used in establishing
   * hooks for the Settings API. It is one of the PHP magic methods, and is
   * invoked any time a method is called on $this that is not explicitly
   * defined and/or is not accessible to the calling scope. 
   *
   * Ultimately the goal here is to have all of the fields generated on
   * behalf of the Settings API to be defined together, as they would if they
   * were hard-coded into a view file. As such, the order in which the dynamic
   * responses appear in the body of this method should match the order in
   * which they are to appear in final output. The actual order of the final
   * HTML, however, is not dicated by this method. Their actual order is 
   * precribed by the order in which the settings are created using 
   * the add_settings_field function.
   *
   * @see http://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
   * @see KitchenSink->admin_menu
   */
  function __call($name, $args) {

    /**
     * This is an example of content for a settings section. Note that we
     * aren't generating any form inputs here -- those are handled by the
     * callbacks registered by the add_settings_field function. No, all this
     * needs to do (if anything) is summarize with some text.
     */
    if ($name == 'basic_settings') {
      ?>
        <p>These are the most essential configuration settings. You will want
        to keep these to a minimum, all with intelligent defaults.</p>
      <?php
    }

    if ($name == 'text_field') {
      ?>
        <input type="text" class="regular-text" id="<?php $this->id($name) ?>" name="<?php $this->field($name) ?>" value="<?php echo esc_attr( $this->setting($name, 'Default value') ) ?>" />
        &nbsp; <span class="description">Extra notes are useful aids to complex decision making</span>
      <?php
    }

    if ($name == 'advanced_settings') {
      ?>
        <p>The more esoteric options go here.</p>
      <?php
    }
  }

  function sanitize_settings($settings) {
    

    /**
     * Here we validate the settings the user submits, use the Settings API to 
     * inform the user when something they've submitted is invalid.
     */

     add_settings_error( 'text_field', $this->id('text_field', false), 'foo' );

    return $settings;
  }

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


}

KitchenSink::load();