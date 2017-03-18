<?php
/**
 * Plugin Name:     Env Switcher
 */
if (!class_exists('WPEnvSettings')) {
  return;
}

add_action( 'admin_bar_menu', 'env_switcher_admin_bar', 100 );

function env_switcher_admin_bar($wp_admin_bar)
{

    $settings = WPEnvSettings::get();

    if (!isset($settings['environments'])){
        global $wpdb;

        $settings['environments']= array();
        $settings['environments'][$wpdb->prefix] = array('name'=>get_bloginfo());

        WPEnvSettings::write();
    }
    $args = array(
        'id' => 'env_context_switch',
        'title' => 'Switch Context',
        'meta' => array('class' => 'first-toolbar-group'),
    );
    $wp_admin_bar->add_node($args);
    $added_prefixes = array();
    foreach ($settings['environments'] as $id=>$data) {
        $args = array(
            'id' => 'env_context_' . $id,
            'title' => $data['name'],
            'meta' => array('class' => 'first-toolbar-group'),
            'parent'=> 'env_context_switch',
            'href' => add_query_arg('redir',urlencode(remove_query_arg('env_switch')),add_query_arg("env_switch", $id))
        );
        array_push($added_prefixes,$id);
        $wp_admin_bar->add_node($args);
    }


    global $wpdb;
    if (! in_array($wpdb->prefix,$added_prefixes)) {

        $settings['environments'][$wpdb->prefix] = get_bloginfo();
        file_put_contents(ABSPATH . "wp-content/env.json", json_encode($env_switch_settings));

        $args = array(
            'id' => 'env_context_' . $wpdb->prefix,
            'title' => get_bloginfo(),
            'meta' => array('class' => 'first-toolbar-group'),
            'parent'=> 'env_context_switch',
            'href' =>  add_query_arg('redir',urlencode(remove_query_arg('env_switch')),add_query_arg("env_switch", $wpdb->prefix))
        );
        array_push($added_prefixes,$wpdb->prefix);
        $wp_admin_bar->add_node($args);

    }




    $id = strtolower(wp_generate_password(3,false) . "_");
    while (in_array($id,$added_prefixes))
    {
        $id = strtolower(wp_generate_password(3,false) . "_");
    }
    $args = array(
        'id' => 'env_context_' . $id,
        'title' => "Create New Context...",
        'meta' => array('class' => 'first-toolbar-group'),
        'parent'=> 'env_context_switch',
        'href' =>  add_query_arg('redir',urlencode(remove_query_arg('env_switch')),add_query_arg("env_switch", $id))
    );

    $wp_admin_bar->add_node($args);
}
add_action('env_task_loginadmin', function() {
  wp_set_auth_cookie(1);
});
add_action('init',function() {

  if (isset($_GET['env_tasks']) && isset($_GET['envnonce'])) {
    $nonce = $_GET['envnonce'];
    $secret = md5(file_get_contents(ENV_JSON));
    $generatednonce = md5($secret . NONCE_SALT . $_GET['env_tasks']);

    if ($generatednonce != $_GET['envnonce']) {
      die('invalid nonce');return;
    }
    $context = WPEnvSettings::get_context($_GET['env_tasks']);
    foreach ($context['tasks'] as $task) {
        do_action("env_task_" . $task);
    }

    $url = remove_query_arg('env_tasks');
    $url = remove_query_arg('envnonce',$url);
    if (isset($_GET['redir'])){
      $url = $_GET['redir'];
    }

    header('Location: '.$url);
    exit;

  }

  if (isset($_GET['env_switch'])) {
      $context = WPEnvSettings::get_context($_GET['env_switch']);
      WPEnvSettings::set_current($_GET['env_switch']);

      $url = admin_url();
      if (isset($_GET['redir'])){
        $url = urldecode($_GET['redir']);
      }

      if ($context && isset($context['tasks']))
      {

        $secret = md5(file_get_contents(ENV_JSON));
        $generatednonce = md5($secret . NONCE_SALT . $_GET['env_switch']);

          $url = add_query_arg('envnonce',$generatednonce, add_query_arg('env_tasks', $_GET['env_switch'],$url));
      }
      header('Location: '. htmlspecialchars_decode($url));
      exit;
  }
});
