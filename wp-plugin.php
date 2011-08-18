<?php
/*
Plugin Name: Kitchen Sink
Description: A consolidated set of examples for working with the WordPress core APIs.
Version: 1.0
Author: Aaron Collegeman, Fat Panda
Author URI: http://aaroncollegeman.com/fatpanda
Plugin URI: http://github.com/collegeman/wp-plugin
*/

class KitchenSink {
  
  /**
   * There are lots of different ways to create a namespace for your plugin.
   * My favorite is to use a class definition with a singleton-patterned
   * loader. That's OOP-speak for there's never more than one instance of your
   * plugin's class, and that instance has a global access point. 
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
   * @see http://en.wikipedia.org/wiki/Singleton_pattern
   */
  static $plugin;
  static function load() {
    $class = __CLASS__; 
    return ( self::$plugin ? self::$plugin : ( self::$plugin = new $class() ));
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
    
  }

  function admin_init() {
    
    /**
     * The admin_menu action is invoked as WordPress builds the administrative
     * menu system. You can hook here to add menu items to built-in menus,
     * or create your own menus, complete with custom icons.
     */
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

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

  }




}

KitchenSink::load();