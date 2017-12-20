<?php
defined('DCMS') or die;
$listing = new listing('title_box');
$post = $listing->post('title_box');
$post->title = __('Свежие новости');
$post->url = '/news/';
$post->fa_icon = 'newspaper-o';
$listing->display();
$q = DB::me()->query("SELECT * FROM `news` ORDER BY `id` DESC LIMIT " . $dcms->count_news);
while ($news = $q->fetch()) {
$listing = new listing('newslisting');
$post = $listing->post('news');
$post->title = text::toValue($news['title']);
$post->time = misc::when($news['time']);
$post->url = '/news/comments.php?id='.$news['id'];
$post->post = text::toOutput($news['text']);
$post->fa_icon = 'rss';
$post->counter = DB::me()->query("SELECT COUNT(*) FROM  `news_comments` WHERE `id_news` = '".$news['id']."'")->fetchColumn();
$listing->display('Новостей пока нет');}