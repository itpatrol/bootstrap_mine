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

  if ($bootstrap_cdn = theme_get_setting('bootstrap_cdn')) {
    // Add CDN.
    if ($bootswatch = theme_get_setting('bootstrap_bootswatch')) {
      $cdn = '//netdna.bootstrapcdn.com/bootswatch/' . $bootstrap_cdn  . '/' . $bootswatch . '/bootstrap.min.css';
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
  if ($font_awesome = theme_get_setting('bootstrap_font_awesome')) {
    $awesome = 'https://maxcdn.bootstrapcdn.com/font-awesome/' . $font_awesome . '/css/font-awesome.min.css';
    $css[$awesome] = array(
      'data' => $awesome,
      'type' => 'external',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => FALSE,
      'group' => CSS_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -2,
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
  
  if(isset($variables['element']['#groups']) && !empty($variables['element']['#groups'])){
    return theme_fieldset($variables);
  }
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
  if(isset($variables['element']['#attributes']['class'])){
    foreach($variables['element']['#attributes']['class'] as $key => $class){
      if(FALSE !== strpos($class, 'secondary')){
        $class = $variables['element']['#attributes']['class'][$key] = str_replace('secondary', 'default', $class);
      }
      if(FALSE !== strpos($class, 'button')){
        $variables['element']['#attributes']['class'][$key] = str_replace('button', 'btn', $class);
      }
    }
  } else{
    $variables['element']['#attributes']['class'][] = 'btn-default';  
  }
  $variables['element']['#attributes']['class'][] = 'btn';
  return theme_button($variables);
}

/**
 * Returns HTML for an email form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #size, #maxlength,
 *     #placeholder, #required, #attributes, #autocomplete_path.
 *
 * @ingroup themeable
 */
function bootstrap_email($variables) {
  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_email($variables);
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
  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_textarea($variables);
}

/**
 * Returns HTML for a form element.
 *
 * Each form element is wrapped in a DIV container having the following CSS
 * classes:
 * - form-item: Generic for all form elements.
 * - form-type-#type: The internal element #type.
 * - form-item-#name: The internal form element #name (usually derived from the
 *   $form structure and set via form_builder()).
 * - form-disabled: Only set if the form element is #disabled.
 *
 * In addition to the element itself, the DIV contains a label for the element
 * based on the optional #title_display property, and an optional #description.
 *
 * The optional #title_display property can have these values:
 * - before: The label is output before the element. This is the default.
 *   The label includes the #title and the required marker, if #required.
 * - after: The label is output after the element. For example, this is used
 *   for radio and checkbox #type elements as set in system_element_info().
 *   If the #title is empty but the field is #required, the label will
 *   contain only the required marker.
 * - invisible: Labels are critical for screen readers to enable them to
 *   properly navigate through forms but can be visually distracting. This
 *   property hides the label for everyone except screen readers.
 * - attribute: Set the title attribute on the element to create a tooltip
 *   but output no label element. This is supported only for checkboxes
 *   and radios in form_pre_render_conditional_form_element(). It is used
 *   where a visual label is not needed, such as a table of checkboxes where
 *   the row and column provide the context. The tooltip will include the
 *   title and required marker.
 *
 * If the #title property is not set, then the label and any required marker
 * will not be output, regardless of the #title_display or #required values.
 * This can be useful in cases such as the password_confirm element, which
 * creates children elements that have their own labels and required markers,
 * but the parent element should have neither. Use this carefully because a
 * field without an associated label can cause accessibility challenges.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #title_display, #description, #id, #required,
 *     #children, #type, #name.
 *
 * @ingroup themeable
 */
function bootstrap_form_element($variables){
//  print_r($variables);
  if($variables['element']['#type'] == 'checkbox'){
    $variables['element']['#wrapper_attributes']['class'][] = 'checkbox';
  }
  if($variables['element']['#type'] == 'radio'){
    $variables['element']['#wrapper_attributes']['class'][] = 'radio';
  }
//  $variables['element']['#wrapper_attributes']['class'][] = 'input-group';
  
  $description = FALSE;
  if(isset($variables['element']['#description'])){
    $description = $variables['element']['#description'];
    unset($variables['element']['#description']);
  }
  $output = theme_form_element($variables);
  if($description){
    $output .= '<div class="description help-block">' . $description . "</div>\n";
  }
  return $output;
}

/**
 * Returns HTML for a password form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #size, #maxlength,
 *     #placeholder, #required, #attributes.
 *
 * @ingroup themeable
 */
function bootstrap_password($variables) {
  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_password($variables);
}

/**
 * Returns HTML for a search form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #size, #maxlength,
 *     #placeholder, #required, #attributes, #autocomplete_path.
 *
 * @ingroup themeable
 */
function bootstrap_search($variables) {
  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_search($variables);
}

/**
 * Returns HTML for a select form element.
 *
 * It is possible to group options together; to do this, change the format of
 * $options to an associative array in which the keys are group labels, and the
 * values are associative arrays in the normal $options format.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #options, #description, #extra,
 *     #multiple, #required, #name, #attributes, #size.
 *
 * @ingroup themeable
 */
function bootstrap_select($variables) {
  $variables['element']['#attributes']['class'][] = 'form-control';
  return theme_select($variables);
}

/**
 * Implements hook_preprocess_table().
 */
function bootstrap_preprocess_table(&$variables) {
  $variables['attributes']['class'][] = 'table';
  $variables['attributes']['class'][] = 'table-hover';
  if (!in_array('table-no-striping', $variables['attributes']['class'])) {
    $variables['attributes']['class'][] = 'table-striped';
  }
}

/**
 * Returns HTML for an individual permission description.
 *
 * @param $variables
 *   An associative array containing:
 *   - permission_item: An associative array representing the permission whose
 *     description is being themed. Useful keys include:
 *     - description: The text of the permission description.
 *     - warning: A security-related warning message about the permission (if
 *       there is one).
 *
 * @ingroup themeable
 */
function bootstrap_user_permission_description($variables) {
  $description = array();
  $permission_item = $variables['permission_item'];
  if (!empty($permission_item['description'])) {
    $description[] = $permission_item['description'];
  }
  if (!empty($permission_item['warning'])) {
    $description[] = '<em class="permission-warning text-danger">' . $permission_item['warning'] . '</em>';
  }
  if (!empty($description)) {
    return implode(' ', $description);
  }
}

/**
 * Returns HTML for an administrative block for display.
 *
 * @param $variables
 *   An associative array containing:
 *   - block: An array containing information about the block:
 *     - show: A Boolean whether to output the block. Defaults to FALSE.
 *     - title: The block's title.
 *     - content: (optional) Formatted content for the block.
 *     - description: (optional) Description of the block. Only output if
 *       'content' is not set.
 *
 * @ingroup themeable
 */
function bootstrap_admin_block($variables) {
  $block = $variables['block'];
  $output = '';

  // Don't display the block if it has no content to display.
  if (empty($block['show'])) {
    return $output;
  }

  $output .= '<div class="panel panel-default">';
  if (!empty($block['title'])) {
    $output .= '<div class="panel-heading"><h3 class="panel-title">' . $block['title'] . '</h3></div>';
  }
  if (!empty($block['content'])) {
    $output .= '<div class="body panel-body">' . $block['content'] . '</div>';
  }
  else {
    $output .= '<div class="description panel-body">' . $block['description'] . '</div>';
  }
  $output .= '</div>';

  return $output;
}

/**
 * Returns HTML for the output of the dashboard page.
 *
 * @param $variables
 *   An associative array containing:
 *   - menu_items: An array of modules to be displayed.
 *
 * @ingroup themeable
 */
function bootstrap_system_admin_index($variables) {
  $menu_items = $variables['menu_items'];

  $stripe = 0;
  $container = array('left' => '', 'right' => '');
  $flip = array('left' => 'right', 'right' => 'left');
  $position = 'left';

  // Iterate over all modules.
  foreach ($menu_items as $module => $block) {
    list($description, $items) = $block;

    // Output links.
    if (count($items)) {
      $block = array();
      $block['title'] = $module;
      $block['content'] = theme('admin_block_content', array('content' => $items));
      $block['description'] = t($description);
      $block['show'] = TRUE;

      if ($block_output = theme('admin_block', array('block' => $block))) {
        if (!isset($block['position'])) {
          // Perform automatic striping.
          $block['position'] = $position;
          $position = $flip[$position];
        }
        $container[$block['position']] .= $block_output;
      }
    }
  }

  $output = '<div class="admin clearfix">';
  foreach ($container as $id => $data) {
    $output .= '<div class=" col-md-6 col-sm-12 clearfix">';
    $output .= $data;
    $output .= '</div>';
  }
  $output .= '</div>';

  return $output;
}

/**
 * Returns HTML for an administrative page.
 *
 * @param $variables
 *   An associative array containing:
 *   - blocks: An array of blocks to display. Each array should include a
 *     'title', a 'description', a formatted 'content' and a 'position' which
 *     will control which container it will be in. This is usually 'left' or
 *     'right'.
 *
 * @ingroup themeable
 */
function bootstrap_admin_page($variables) {
  $blocks = $variables['blocks'];

  $stripe = 0;
  $container = array();

  foreach ($blocks as $block) {
    if ($block_output = theme('admin_block', array('block' => $block))) {
      if (empty($block['position'])) {
        // perform automatic striping.
        $block['position'] = ++$stripe % 2 ? 'left' : 'right';
      }
      if (!isset($container[$block['position']])) {
        $container[$block['position']] = '';
      }
      $container[$block['position']] .= $block_output;
    }
  }

  $output = '<div class="admin clearfix">';

  foreach ($container as $id => $data) {
    $output .= '<div class="clearfix  col-md-6 col-sm-12 ">';
    $output .= $data;
    $output .= '</div>';
  }
  $output .= '</div>';
  return $output;
}

/**
 * Returns HTML for primary and secondary local tasks.
 *
 * @param $variables
 *   An associative array containing:
 *     - primary: (optional) An array of local tasks (tabs).
 *     - secondary: (optional) An array of local tasks (tabs).
 *
 * @ingroup themeable
 * @see menu_local_tasks()
 */
function bootstrap_menu_local_tasks(&$variables) {
  $output = '';

  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] .= '<ul class="nav nav-tabs tabs-primary">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= backdrop_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $variables['secondary']['#prefix'] .= '<ul class="nav nav-pills secondary">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= backdrop_render($variables['secondary']);
  }

  return $output;
}

function bootstrap_links__dropbutton($menu){
  foreach($menu['links'] as $name => $settings){
    $menu['links'][$name]['attributes']['class'][] = 'btn';
    $menu['links'][$name]['attributes']['class'][] = 'btn-default';
  }
  return theme_links($menu);
}

/**
 * Returns rendered HTML for the local actions.
 */
function bootstrap_menu_local_actions(&$variables) {
  print_r($variables['actions']);
  foreach($variables['actions'] as $key => $link){
    switch($link['#link']['path']){
      case 'admin/people/create':
          $variables['actions'][$key]['#link']['title'] =  '<i class="fa fa-user-plus"></i>' . $link['#link']['title'];
        break;
    }
  }
  $output = backdrop_render($variables['actions']);
  if ($output) {
    $output = '<ul class="nav nav-pills action-links">' . $output . '</ul>';
  }
  return $output;
}

/**
 * Returns HTML for a breadcrumb trail.
 *
 * @param $variables
 *   An associative array containing:
 *   - breadcrumb: An array containing the breadcrumb links.
 */
function bootstrap_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  $output = '';
  if (!empty($breadcrumb)) {
    $output .= '<nav role="navigation">';
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output .= '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $output .= '<ol  class="breadcrumb" ><li>' . implode('</li><li>', $breadcrumb) . '</li></ol>';
    $output .= '</nav>';
  }
  return $output;
}

/**
 * Display a view as a table style.
 */
function bootstrap_preprocess_views_view_table(&$variables) {
//  template_preprocess_views_view_table($variables);
  $variables['classes'][] = 'table';
}
