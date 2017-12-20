<?php
include_once '../../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Информационный раздел');
$doc->ret(__('Админка'), '../');
if(!$user->access('dpanel_info_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$menu = new menu_ini('info');
$menu->display();