<?php
defined('DCMS') or die;
$res = DB::me()->prepare("SELECT COUNT(*) FROM `users` WHERE `a_code` = '' AND `reg_date` > ?");
$res->execute(Array(NEW_TIME));
$users = $res->fetchColumn();
$q = DB::me()->prepare("SELECT * FROM `users` WHERE `a_code` = '' AND `reg_date` > ? ORDER BY `id` DESC");
$q->execute(Array(NEW_TIME));
if ($arr = $q->fetchAll()) {
$listing = new listing('title_box');
$post = $listing->post('title_box');
$post->title = __('Новые пользователи');
$post->url = '/users.php';
$post->fa_icon = 'user-plus';
$post->counter = '+'.__('%s ' . misc::number($users, 'человек', 'человека', 'человек'), $users);
$listing->display();
$user_new = array() ;
foreach($arr AS $ank){
$p_user= new user($ank['id']) ;
$user_new[] = $p_user->show() ;
}
$user_new = implode(', ', $user_new) ;
$listing = new listing('listing_users') ;
$post = $listing->post('users_new') ;
$post->post = $user_new;
$listing->display() ;
}
$res = DB::me()->query("SELECT COUNT(*) FROM `users_online`");
$user_onl = $res->fetchColumn();
$q = DB::me()->query("SELECT `users_online`.* , `browsers`.`name` AS `browser` FROM `users_online` LEFT JOIN `browsers` ON `users_online`.`id_browser` = `browsers`.`id` ORDER BY `users_online`.`time_login` DESC");
if ($arr = $q->fetchAll()) {
$listing = new listing('title_box');
$post = $listing->post('title_box');
$post->title = __('Онлайн');
$post->url = '/online.users.php';
$post->fa_icon = 'users';
$post->counter = '+'.__('%s ' . misc::number($user_onl, 'человек', 'человека', 'человек'), $user_onl);
$listing->display();
$online = array() ;
foreach($arr AS $ank){
$p_user= new user($ank['id_user']) ;
$online[] = $p_user->show() ;
}
$online = implode(', ', $online) ;
$listing = new listing('listing_users') ;
$post = $listing->post('users') ;
$post->post = $online ;
$listing->display() ;
}

