<?php
/**
 * @file
 * template.php
 *
 * This file should only contain light helper functions and stubs pointing to
 * other files containing more complex functions.
 *
 * The stubs should point to files within the `theme` folder named after the
 * function itself minus the theme prefix. If the stub contains a group of
 * functions, then please organize them so they are related in some way and name
 * the file appropriately to at least hint at what it contains.
 *
 * All [pre]process functions, theme functions and template implementations also
 * live in the 'theme' folder. This is a highly automated and complex system
 * designed to only load the necessary files when a given theme hook is invoked.
 * @see _bootstrap_theme()
 * @see theme/registry.inc
 *
 * Due to a bug in Drush, these includes must live inside the 'theme' folder
 * instead of something like 'includes'. If a module or theme has an 'includes'
 * folder, Drush will think it is trying to bootstrap core when it is invoked
 * from inside the particular extension's directory.
 * @see https://drupal.org/node/2102287
 */

/**
 * Include common functions used through out theme.
 */
include_once dirname(__FILE__) . '/theme/common.inc';

/**
 * Implements hook_theme().
 *
 * Register theme hook implementations.
 *
 * The implementations declared by this hook have two purposes: either they
 * specify how a particular render array is to be rendered as HTML (this is
 * usually the case if the theme function is assigned to the render array's
 * #theme property), or they return the HTML that should be returned by an
 * invocation of theme().
 *
 * @see _bootstrap_theme()

function bootstrap_theme(&$existing, $type, $theme, $path) {
  echo "1";
  bootstrap_include($theme, 'theme/registry.inc');
  print_r(_bootstrap_theme($existing, $type, $theme, $path));
  return _bootstrap_theme($existing, $type, $theme, $path);
}
 */
/**
 * Declare various hook_*_alter() hooks.
 *
 * hook_*_alter() implementations must live (via include) inside this file so
 * they are properly detected when drupal_alter() is invoked.
 */
bootstrap_include('bootstrap', 'theme/alter.inc');

/**
 * Implements hook_preprocess_layout().
 */
function bootstrap_preprocess_layout(&$variables) {
  $layout_name = $variables['layout']['layout'];
  $layout = layout_get_layout_info($layout_name);
  print_r($layout);
}
function bootstrap_preprocess_page(&$variables){
  if (user_access('access administration bar') && !admin_bar_suppress(FALSE)) {
    $variables['classes'][] = 'navbar-admin-bar';
  }
  if($navbar_position = theme_get_setting('bootstrap_navbar_position'))
  {
    $variables['classes'][] = 'navbar-is-' . $navbar_position;
    
    if($navbar_position == 'fixed-top' && user_access('access administration bar') && !admin_bar_suppress(FALSE)){
      backdrop_add_js(backdrop_get_path('theme', 'bootstrap') . '/js/navbar-fixed-top.js');
    }
  }
}

function bootstrap_preprocess_header(&$variables){
/*  $menu = menu_tree('main-menu');
  $variables['navigation'] = render($menu);*/
  
  $variables['navigation'] = '';
  
  if($navbar_position = theme_get_setting('bootstrap_navbar_user_menu'))
  {
    $user_menu = menu_tree('user-menu');
    $variables['navigation'] = render($user_menu);
  }
  
  $variables['navbar_classes_array'] = array('navbar');
  if($navbar_position = theme_get_setting('bootstrap_navbar_position'))
  {
    $variables['navbar_classes_array'][] = 'navbar-' . $navbar_position;
  } else {
    $variables['navbar_classes_array'][] = 'container';
  }
  
  if (theme_get_setting('bootstrap_navbar_inverse')) {
    $variables['navbar_classes_array'][] = 'navbar-inverse';
  }
  else {
    $variables['navbar_classes_array'][] = 'navbar-default';
  }
}

function bootstrap_links__header_menu($menu){
  $menu['attributes']['class'] = array('menu','nav','navbar-nav');
  if($navbar_menu_position = theme_get_setting('bootstrap_navbar_menu_position')){
    $menu['attributes']['class'][] = $navbar_menu_position;
  }
  return theme_links($menu);
}

function bootstrap_menu_tree__user_menu($variables){
  if($navbar_position = theme_get_setting('bootstrap_navbar_user_menu')){
    return '
<ul class="menu nav navbar-nav navbar-right">
  <li class="dropdown">
    <a href="#" class="user-cog-link dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-cog"></span></a>
    <ul class="dropdown-menu">
    ' . $variables['tree'] . '
    </ul>
  </li>
</ul>';
  }
  return theme_menu_tree($variables);
}
