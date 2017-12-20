<?php
include_once '../../sys/inc/start.php';
$doc = new document (2);
if(!$user->access('dpanel_access_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$groups = groups::load_ini(); // загружаем массив групп

$doc->title = __('Привилегии');
$doc->ret(__('Админка'), '../') ;
$listing = new listing() ;
$post = $listing->post() ;
$post->title = __('Добавить/удалить привилегию');
$post->url = '/dpanel/access/set.php';
$post->icon('settings.common.png');
foreach($groups as $type => $v){
	$post = $listing->post() ;
	$post->title = $v['name'] ;
	$post->url = '/dpanel/access/group.php?id=' . $type ;
}

$listing->display() ;