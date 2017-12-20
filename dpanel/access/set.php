<?php
include_once '../../sys/inc/start.php' ;
$doc = new document(2) ;
if(!$user->access('dpanel_access_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Управление привилегиями') ;
$doc->ret(__('Привилегии'), './') ;
$doc->ret(__('Админка'), '../') ;
$filter = ini::read(H . '/sys/ini/all_access.ini') ;

if(isset($_GET['delete']) && isset($filter[$_GET['delete']])){
    $delete = ($_GET['delete']) ;
    $a = $filter[$delete] ;
    unset($filter[$delete]) ;
    if(ini::save(H . '/sys/ini/filter.ini', $filter)){
        $dcms->log('Привилегии', 'Удалил привилегию: ' . $delete . ' => ' . $a) ;
        $doc->msg('Слово успешно удалено') ;
    }
}
if(isset($_POST['add'])){
    $k = text::input_text($_POST['k']) ;
    $v = text::input_text($_POST['v']) ;
    $filter[$k] = $v ;
    if($k && ini::save(H . '/sys/ini/all_access.ini', $filter)){
        $dcms->log('Привилегии', 'Добавил новую привилегию: ' . $k . ' => ' . $v) ;
        $doc->msg('Новая привилегия добавлена') ;
        header('Refresh: 1; ?') ;
        exit ;
    }
}
$listing = new listing() ;

foreach($filter AS $k => $v){
    $post = $listing->post() ;
    $post->title = $k . ' => ' . $v ;
    $post->action('delete', '?delete=' . $k) ;
}

$listing ->display(__('Привилегии еще не добавлены')) ;

$form = new form('?') ;
$form->input('k', 'Название привилегии (english)  => Что означает?', false, 'input_text', false, 8, false) ;
$form->input('v', false, false, 'input_text', false, 8, false) ;
$form->button(__('Добавить'), 'add') ;
$form->display() ;
?>