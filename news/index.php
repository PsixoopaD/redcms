<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Наши новости');
$pages = new pages;
$res = $db->query("SELECT COUNT(*) FROM `news`");
$pages->posts = $res->fetchColumn(); // количество сообщений
$q = $db->query("SELECT * FROM `news` ORDER BY `id` DESC LIMIT ".$pages->limit);
$listing = new listing();
if ($arr = $q->fetchAll()) {
foreach ($arr AS $news) {
$post = $listing->post();
$ank = new user((int) $news['id_user']);
$post->icon('news');
$post->content = text::toOutput($news['text']);
$post->title = text::toValue($news['title']);
$post->url = 'comments.php?id=' . $news['id'];
$post->time = misc::when($news['time']);
$post->bottom = '<a href="/profile.view.php?id=' . $news['id_user'] . '">' . $ank->nick() . '</a>';
}
}
$listing->display(__('Новости отсутствуют'));
$pages->display('?'); // вывод страниц
