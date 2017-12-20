<?php
include_once '../../sys/inc/start.php';
$groups = groups::load_ini(); // загружаем массив групп
$doc = new document (2);
if(!$user->access('dpanel_access_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = 'Привилегии' ;

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
	header('Location: ./') ;
	exit ;
}
$id_group = (int) $_GET['id'] ;

if(!isset($groups[$id_group])){
	header('Location: ./') ;
	exit ;
}
$doc->title .= __(' "%s"', $groups[$id_group]['name']) ;
$doc->ret(__('Вернутся'), './') ;

if(isset($_POST['access'])){
	$access = array() ;
	foreach ($_POST as $key => $value) {
		if ($value && preg_match('#^access(.+)$#ui', $key, $n)){
			$access[] = ':' . $n[1] . ':' ;
		}
	}
	$access = implode('', $access) ;
	$group = 'access_' . $id_group ;
	$dcms->$group = $access ;
	$dcms->save_settings($doc) ;
	header('Refresh: 1; ?id=' . $id_group) ;
	exit ;
}

$ini = ini::read(H . '/sys/ini/all_access.ini') ;

$listing = new listing();
$count = 0 ;
foreach($ini AS $key => $v){
    $post = $listing -> checkbox();
    if($user->access($key, $id_group))$post->checked = 1 ;
    $post->name = 'access' . $key ;
    $post -> title = $v ;
	$count++ ;
}

if($count){
	$form = new form('?id=' . $id_group);
$form->html($listing->fetch(__('Привилегий нет')));
	$form->button(__('Сохранить'), 'access', false);
	$form->display();
}