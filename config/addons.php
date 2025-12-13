<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'show_map' => 
    array (
      0 => '\\addons\\address\\Address',
    ),
    'upgrade' => 
    array (
      0 => '\\addons\\pay\\Pay',
    ),
    'user_sidenav_after' => 
    array (
      0 => '\\addons\\pay\\Pay',
    ),
    'ems_send' => 
    array (
      0 => '\\addons\\phpmailer\\Phpmailer',
    ),
    'ems_notice' => 
    array (
      0 => '\\addons\\phpmailer\\Phpmailer',
    ),
    'ems_check' => 
    array (
      0 => '\\addons\\phpmailer\\Phpmailer',
    ),
    'app_init' => 
    array (
      0 => '\\addons\\phpmailer\\Phpmailer',
    ),
  ),
  'route' => 
  array (
  ),
);