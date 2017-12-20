<?php
defined('DCMS') or die;
$res = DB::me()->prepare("SELECT * FROM (SELECT COUNT( * ) cnt FROM `chat_mini` WHERE `time` > ?) q1, (SELECT COUNT( * ) users FROM `users_online` WHERE `request` LIKE ?)q2");
$res->execute(Array(NEW_TIME, '/chat_mini/%'));
$row = $res->fetch();
$new_posts = $row['cnt'];
$users = $row['users'];
$listing = new listing('title_box');
$post = $listing->post('title_box');
$post->title = __('Мини чат');
$post->url = '/chat_mini/';
$post->fa_icon = 'comments';
$post->counter = '+'.$new_posts;
$listing->display();
$listing = new listing();
$q = DB::me()->query("SELECT * FROM `chat_mini` ORDER BY `id` DESC LIMIT 3");
if ($arr = $q->fetchAll()) {
foreach ($arr AS $message) {
$ank = new user($message['id_user']);
$post = $listing->post();
$post->url = '/chat_mini/?message=' .$message['id'] . '&amp;reply';
$post->title = $ank->nick();
$post->image = $ank->ava();
$post->post = text::toOutput($message['message']);
}
}
$listing->display('Чат пуст. Начни общение первым!');
