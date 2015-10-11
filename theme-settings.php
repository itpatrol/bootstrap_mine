<?php
/**
 * @file
 * theme-settings.php
 *
 * Theme settings file for Bootstrap.
 */

function bootstrap_form_system_theme_settings_alter(&$form, &$form_state, $form_id = NULL) {

  if (isset($form_id)) {
    return;
  }

  $form['bootstrap'] = array(
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'js'  => array(drupal_get_path('theme', 'bootstrap') . '/js/bootstrap.admin.js'),
    ),
    '#prefix' => '<h2><small>' . t('Bootstrap Settings') . '</small></h2>',
    '#weight' => -10,
  );
  // Components.
  $form['components'] = array(
    '#type' => 'fieldset',
    '#title' => t('Components'),
    '#group' => 'bootstrap',
  );

  $form['components']['navbar'] = array(
    '#type' => 'fieldset',
    '#title' => t('Navbar'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['components']['navbar']['bootstrap_navbar_position'] = array(
    '#type' => 'select',
    '#title' => t('Navbar Position'),
    '#description' => t('Select your Navbar position.'),
    '#default_value' => theme_get_setting('bootstrap_navbar_position', 'bootstrap'),
    '#options' => array(
      'static-top' => t('Static Top'),
      'fixed-top' => t('Fixed Top'),
      'fixed-bottom' => t('Fixed Bottom'),
    ),
    '#empty_option' => t('Normal'),
  );
  
  $form['components']['navbar']['bootstrap_navbar_menu_position'] = array(
    '#type' => 'select',
    '#title' => t('Navbar Menu Position'),
    '#description' => t('Select your Navbar Menu position.'),
    '#default_value' => theme_get_setting('bootstrap_navbar_menu_position', 'bootstrap'),
    '#options' => array(
      'navbar-left' => t('Left'),
      'navbar-right' => t('Right'),
    ),
    '#empty_option' => t('Normal'),
  );
  
  $form['components']['navbar']['bootstrap_navbar_inverse'] = array(
    '#type' => 'checkbox',
    '#title' => t('Inverse navbar style'),
    '#description' => t('Select if you want the inverse navbar style.'),
    '#default_value' => theme_get_setting('bootstrap_navbar_inverse', 'bootstrap'),
  );

  $form['components']['navbar']['bootstrap_navbar_user_menu'] = array(
    '#type' => 'checkbox',
    '#title' => t('Add cog with user-menu'),
    '#description' => t('Select if you want cog style right pulled popup menu.'),
    '#default_value' => theme_get_setting('bootstrap_navbar_user_menu', 'bootstrap'),
  );
  
  $layouts = layout_get_layout_info();
   
  $wells = array(
    '' => t('None'),
    'well' => t('.well (normal)'),
    'well well-sm' => t('.well-sm (small)'),
    'well well-lg' => t('.well-lg (large)'),
  );
  $form['components']['region_wells'] = array(
    '#type' => 'fieldset',
    '#title' => t('Region wells'),
    '#description' => t('Enable the <code>.well</code>, <code>.well-sm</code> or <code>.well-lg</code> classes for specified regions. See: documentation on !wells.', array(
      '!wells' => l(t('Bootstrap Wells'), 'http://getbootstrap.com/components/#wells'),
    )),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  
  foreach ($layouts as $layout_name => $layout) {
    $form['components']['region_wells'][$layout_name]  = array(
      '#type' => 'fieldset',
      '#title' => t('!layout_title region wells', array('!layout_title' => $layout['title'])),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    
    foreach($layout['regions'] as $region_name => $region_title ){
      $variable_name = 'bootstrap_well_' . $layout_name . '_' . $region_name;
      $form['components']['region_wells'][$layout_name][$variable_name] = array(
        '#title' => $region_title,
        '#type' => 'select',
        '#attributes' => array(
          'class' => array('input-sm'),
        ),
        '#options' => $wells,
        '#default_value' => theme_get_setting($variable_name, 'bootstrap'),
      );
    }
  }
  
  backdrop_add_css(backdrop_get_path('theme', 'bootstrap') . '/css/settings.css');
  $form['bootstrap_cdn'] = array(
    '#type' => 'fieldset',
    '#title' => t('BootstrapCDN settings'),
    '#description' => t('Use !bootstrapcdn to serve the Bootstrap framework files. Enabling this setting will prevent this theme from attempting to load any Bootstrap framework files locally. !warning', array(
      '!bootstrapcdn' => l(t('BootstrapCDN'), 'http://bootstrapcdn.com', array(
        'external' => TRUE,
      )),
    '!warning' => '<div class="alert alert-info messages info"><strong>' . t('NOTE') . ':</strong> ' . t('While BootstrapCDN (content distribution network) is the preferred method for providing huge performance gains in load time, this method does depend on using this third party service. BootstrapCDN is under no obligation or commitment to provide guaranteed up-time or service quality for this theme. If you choose to disable this setting, you must provide your own Bootstrap source and/or optional CDN delivery implementation.') . '</div>',
    )),
    '#group' => 'bootstrap',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,    
  );
  // BootstrapCDN.

  $form['bootstrap_cdn']['bootstrap_cdn'] = array(
    '#type' => 'select',
    '#title' => t('BootstrapCDN version'),
    '#options' => drupal_map_assoc(array(
      '3.3.5',
    )),
    '#default_value' => theme_get_setting('bootstrap_cdn', 'bootstrap'),
    '#empty_option' => t('Disabled'),
    '#empty_value' => NULL,
  );
  
  $form['bootstrap_cdn']['bootstrap_font_awesome'] = array(
    '#type' => 'select',
    '#title' => t('Font Awesome version'),
    '#options' => drupal_map_assoc(array(
      '4.4.0',
    )),
    '#default_value' => theme_get_setting('bootstrap_font_awesome', 'bootstrap'),
    '#empty_option' => t('Disabled'),
    '#empty_value' => NULL,
  );

  $bootswatch_themes = array();
  $bootswatch_themes[''] = bootstrap_bootswatch_template(array('name' => t('Default'), 'description' => t('Pure Bootstrap CSS')));
  $request = drupal_http_request('http://api.bootswatch.com/3/');
  if ($request && $request->code === '200' && !empty($request->data)) {
    if (($api = backdrop_json_decode($request->data)) && is_array($api) && !empty($api['themes'])) {
      foreach ($api['themes'] as $bootswatch_theme) {
        $bootswatch_themes[strtolower($bootswatch_theme['name'])] = bootstrap_bootswatch_template($bootswatch_theme);
      }
    }
  }  
    
  $form['bootstrap_cdn']['bootstrap_bootswatch'] = array(
    '#type' => 'radios',
    '#title' => t('Bootswatch theme'),
    '#description' => t('Use !bootstrapcdn to serve a Bootswatch Theme. Choose Bootswatch theme here.', array(
      '!bootstrapcdn' => l(t('BootstrapCDN'), 'http://bootstrapcdn.com', array(
        'external' => TRUE,
      )),
    )),
    '#default_value' => theme_get_setting('bootstrap_bootswatch', 'bootstrap'),
    '#options' => $bootswatch_themes,
    '#empty_option' => t('Disabled'),
    '#empty_value' => NULL,
    '#prefix' => '<div class="container section-preview">',
    '#suffix' => '</div>',
  );
  if (empty($bootswatch_themes)) {
    $form['bootstrap_cdn']['bootstrap_bootswatch']['#prefix'] = '<div class="alert alert-danger messages error"><strong>' . t('ERROR') . ':</strong> ' . t('Unable to reach Bootswatch API. Please ensure the server your website is hosted on is able to initiate HTTP requests.') . '</div>';
  }
}

function bootstrap_bootswatch_template($bootswatch_theme){
  $output = '<div class="preview">';
  
  if(isset($bootswatch_theme['thumbnail'])){
    $output .= '<div class="image">
      <img src="' . $bootswatch_theme['thumbnail']. '" class="img-responsive" alt="' . $bootswatch_theme['name'] . '">
    </div>';
  }
  $output .= '<div class="options">
      <h3>' . $bootswatch_theme['name'] . '</h3>
      <p>' . $bootswatch_theme['description'] . '</p>';
  if(isset($bootswatch_theme['preview'])){
    $output .= '<div class="btn-group"><a class="btn btn-info" href="' . $bootswatch_theme['preview'] . '" target="_blank">Preview</a></div>';
  }
  $output .= '</div>
  </div>';
  return $output;
}
