<?php
/**
 * @file
 * template.php
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
//bootstrap_include('bootstrap', 'theme/alter.inc');

/**
 * Implements hook_css_alter().
 */
function bootstrap_css_alter(&$css) {
  $theme_path = drupal_get_path('theme', 'bootstrap');

  // Add Bootstrap CDN file and overrides.
  $bootstrap_cdn = theme_get_setting('bootstrap_cdn');
  if ($bootstrap_cdn) {
    // Add CDN.
    if (theme_get_setting('bootstrap_bootswatch')) {
      $cdn = '//netdna.bootstrapcdn.com/bootswatch/' . $bootstrap_cdn  . '/' . theme_get_setting('bootstrap_bootswatch') . '/bootstrap.min.css';
    }
    else {
      $cdn = '//netdna.bootstrapcdn.com/bootstrap/' . $bootstrap_cdn  . '/css/bootstrap.min.css';
    }
    $css[$cdn] = array(
      'data' => $cdn,
      'type' => 'external',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => FALSE,
      'group' => CSS_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -2,
    );
    // Add overrides.
    $override = $theme_path . '/css/overrides.css';
    $css[$override] = array(
      'data' => $override,
      'type' => 'file',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => TRUE,
      'group' => CSS_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -1,
    );
  }
}

/**
 * Implements hook_js_alter().
 */
function bootstrap_js_alter(&$js) {
  if (theme_get_setting('bootstrap_cdn')) {
    $cdn = '//netdna.bootstrapcdn.com/bootstrap/' . theme_get_setting('bootstrap_cdn')  . '/js/bootstrap.min.js';
    $js[$cdn] = backdrop_js_defaults();
    $js[$cdn]['data'] = $cdn;
    $js[$cdn]['type'] = 'external';
    $js[$cdn]['every_page'] = TRUE;
    $js[$cdn]['weight'] = -100;
  }
}

/**
 * Implements hook_preprocess_layout().
 */
function bootstrap_preprocess_layout(&$variables) {
  $layout = $variables['layout'];
  $layout_name = $layout->layout;
  
  foreach($layout->positions as $region_name => $region_value){
    if($well = theme_get_setting('bootstrap_well_' . $layout_name . '_' . $region_name)){
      backdrop_add_js('(function($){ $(".l-' . $region_name . '").addClass("' . $well . '");})(jQuery);', array('type' => 'inline', 'scope' => 'footer'));
    }
  }
  backdrop_add_js('(function($){ $(".layout").addClass("container");})(jQuery);', array('type' => 'inline', 'scope' => 'footer'));
  
}

function bootstrap_preprocess_page(&$variables){

  $no_old_ie_compatibility_modes = array(
    '#tag' => 'meta',
    '#attributes' => array(
      'http-equiv' => 'X-UA-Compatible',
      'content' => 'IE=edge',
    ),
  );
  backdrop_add_html_head($no_old_ie_compatibility_modes, 'no_old_ie_compatibility_modes');
    
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


/**
 * Returns HTML for a fieldset form element and its children.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #attributes, #children, #collapsed, #collapsible,
 *     #description, #id, #title, #value.
 *
 * @ingroup themeable
 */
function bootstrap_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element, array('form-wrapper'));
  $element['#attributes']['class'][] = 'panel';
  $element['#attributes']['class'][] = 'panel-default';
  $output = '<fieldset' . backdrop_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    // Always wrap fieldset legends in a SPAN for CSS positioning.
    $output .= '<legend class="panel-heading"><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
  }
  $output .= '<div class="fieldset-wrapper panel-body">';
  if (!empty($element['#description'])) {
    $output .= '<div class="fieldset-description">' . $element['#description'] . '</div>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= '</div>';
  $output .= "</fieldset>\n";
  return $output;
}

/**
 * Returns HTML for a button form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #attributes, #button_type, #name, #value.
 *
 * @ingroup themeable
 */
function bootstrap_button($variables) {
  foreach($variables['element']['#attributes']['class'] as $key => $class){
    if(FALSE !== strpos($class, 'button')){
      $variables['element']['#attributes']['class'][$key] = str_replace('button', 'btn', $class);
    }
  }
  $variables['element']['#attributes']['class'][] = 'btn';
  return theme_button($variables);
}

/**
 * Returns HTML for a textfield form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #size, #maxlength,
 *     #placeholder, #required, #attributes, #autocomplete_path.
 *
 * @ingroup themeable
 */
function bootstrap_textfield($variables) {
  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_textfield($variables);
}

/**
 * Returns HTML for a textarea form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #rows, #cols,
 *     #placeholder, #required, #attributes
 *
 * @ingroup themeable
 */
function bootstrap_textarea($variables) {
  print_r($variables);
//  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_textarea($variables);
}