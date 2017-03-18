<?php

define('ENV_JSON',  dirname(__FILE__) . "/contexts.json" );
class WPEnvSettings {
  private static $env_settings = null;
  private static $current = null;

  public static function get() {
      if (self::$env_settings == null && file_exists( ENV_JSON ) ) {

        $contents = file_get_contents( ENV_JSON );
        self::$env_settings = json_decode($contents,true);
        self::$current = isset(self::$env_settings['current']) ? self::$env_settings['current'] : null;

      }
      return self::$env_settings != null ? self::$env_settings : array();
  }
  public static function write() {
    $settings = self::get();

    global $wpdb;

    if (!isset($settings['environments'])) {
      $settings['environments'] = array();
      $settings['environments'][$wpdb->prefix] = array('name'=>get_bloginfo());
    }

    if (!isset($settings['current'])){
      $settings['current'] = $wpdb->prefix;
    }
    self::$env_settings = $settings;

    file_put_contents(ENV_JSON, json_encode($settings));
  }
  public static function get_context( $key ) {
      $settings = self::get($key);
      if (isset($settings['environments']) && isset($settings['environments'][$key]))
        return $settings['environments'][$key];
      return false;
  }
  public static function get_current_context() {
    $settings = self::get();
    if (isset($settings['current']))
      return $settings['current'];
    return false;
  }
  public static function set_current( $key ) {
    $settings = self::get();
    self::$env_settings['current'] =$key;
    self::write();

  }
}


if (WPEnvSettings::get_current_context()) {
    $table_prefix = WPEnvSettings::get_current_context();
}
