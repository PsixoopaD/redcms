<?php

include_once '../sys/inc/start.php';
$doc = new document(2);
if(!$user->access('adminka')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Панель управления DCMS Revolution');
$d = new design(); 
$d->display('design.dpanel.tpl');
