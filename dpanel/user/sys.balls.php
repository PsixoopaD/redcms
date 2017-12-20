<?php
include_once '../../sys/inc/start.php' ;
dpanel::check_access() ;
$doc = new document(2) ;
if(!$user->access('dpanel_balls_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->ret(__('Упр. Польз.'), './');
$doc->ret(__('Админка'), '../');
$doc->title = __('Управление баллами') ;

$balls = ini::read(H . '/sys/ini/sys.balls.ini') ;
if(isset($_POST['save'])){
    foreach($balls as $k => $v){
        $dcms->$k = abs((int) $_POST[$k]) ; ;
    }
    $dcms->save_settings($doc) ;
}

$form = new form('?' . passgen());
foreach($balls as $k => $v){
    $form->text($k, __($v), $dcms->$k) ;
}
$form->button(__('Применить'), 'save') ;
$form->display() ;

