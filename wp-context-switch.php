<?php
/**
 * Plugin Name:     WP Context Switch
 * Description: Switch between WP installs for localhost development. WARNING: DO NOT USE ON PRODUCTION/PUBLIC SITES. Modifies your wp-config.php on activation
 * Author: Matthew Zalewski @ hotsource.io
 * Plugin URI: github.com/mzalewski/contextswitch
 */

 register_activation_hook(__FILE__,'env_switch_install');
 function env_switch_install() {
   $load_template = file_get_contents( dirname(__FILE__) . "/templates/load.template.php" );
   $mu_plugin = file_get_contents( dirname(__FILE__) . "/templates/mu-plugin.template.php" );
   if ( ! file_exists( dirname(__FILE__) . "/../../mu-plugins")) {
     mkdir(dirname(__FILE__) . "/../../mu-plugins");

   }

   if ( ! file_exists(ABSPATH . "/env-config/")) {
     mkdir(ABSPATH . "/env-config/");

   }
   file_put_contents(dirname(__FILE__) . "/../../mu-plugins/wp-context-switch.php", $mu_plugin);

   file_put_contents(ABSPATH . "/env-config/load.php", $load_template);

   copy(ABSPATH . "/wp-config.php", ABSPATH . "/wp-config-envbackup.php");
   $cfg_contents = file_get_contents(ABSPATH . "/wp-config.php");
   if (strpos($cfg_contents,"WP CONTEXT SWITCH INCLUDE") === false) {
     $cfg_contents = str_replace("require_once(ABSPATH . 'wp-settings.php');","// WP CONTEXT SWITCH INCLUDE\nif (file_exists( ABSPATH . 'config/load.php' )) { \n\tinclude( ABSPATH . 'config/load.php');\n}\nrequire_once(ABSPATH . 'wp-settings.php');", $cfg_contents);
     file_put_contents(ABSPATH . "/wp-config.php",$cfg_contents);
   }
 }
