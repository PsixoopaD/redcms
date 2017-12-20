<?php
include_once '../../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Настройки сайта');
$doc->ret(__('Админка'), '../');
if(!$user->access('dpanel_sys_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$menu = new menu_ini('set_dpanel');
$menu->display();