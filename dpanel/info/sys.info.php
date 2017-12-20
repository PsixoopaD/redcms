<?php

include_once '../../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Информация о системе');
$doc->ret(__('Инф. раздел'), './');
$doc->ret(__('Админка'), '../');
$check = new check_sys();

$listing = new listing();

$post = $listing->post();
$post -> icon('info');
$post -> title = __('Версия DCMS: %s', $dcms->version);

foreach ($check->oks as $ok) {    
    $post = $listing->post();
    $post -> icon('checked');
    $post -> title = $ok;
}
foreach ($check->notices as $note) {
    $post = $listing->post();
    $post -> icon('notice');
    $post -> title = $note;
    $post -> highlight = true;
}
foreach ($check->errors as $err) {
    $post = $listing->post();
    $post -> icon('error');
    $post -> title = $err;
    $post -> highlight = true;
}
$listing ->display();



$listing = new listing();
$nw = ini::read(H . '/sys/ini/chmod.ini');
$err = array();
foreach ($nw as $path) {
    $e = check_sys::getChmodErr($path, true);
    $post = $listing->post();
    $post->icon($e ? 'error' : 'checked');
    $post->title = $path;
    $err = array_merge($err, $e);
}

$listing->display();

if ($err) { 
    $form = new form();
    $form->textarea('', '', implode("\r\n", $err));
    $form->bbcode('* ' . __('В зависимости от настроек на хостинге, CHMOD для возможности записи должен быть от 644 до 666'));
    $form->display();
}else
    $doc->msg( __('Необходимые права на запись имеются'));