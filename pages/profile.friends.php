<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Друзья');

$ank = new user(@$_GET['id']);

if (!$ank->id) {
    $doc->toReturn();
    $doc->err(__('Нет данных'));
    exit;
}

if (!$ank->is_friend($user) && !$ank->vis_friends) {
    $doc->toReturn();
    $doc->err(__('Доступ к данной странице ограничен'));
    exit;
}



$doc->title = __('Друзья %s', $ank->nick);

$posts = array();

$pages = new pages;

$res = $db->prepare("SELECT COUNT(*) FROM `friends` WHERE `id_user` = ? AND `confirm` = '1'");
$res->execute(Array($ank->id));
$pages->posts = $res->fetchColumn();

$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `confirm` = '1' ORDER BY `time` DESC LIMIT " . $pages->limit);
$q->execute(Array($ank->id));

$listing = new listing();
while ($arr = $q->fetchAll()) {
    foreach ($arr AS $friend) {
        $fr = new user($friend['id_friend']);
        $post = $listing->post();
        $post->title = $fr->nick();
$post->action('mail', '/my.mail.php?id='.$fr->id);
        $post->url = '/profile.view.php?id=' . $fr->id;
        $post->image = $fr->ava();
$name = ($fr->surname && $fr->patronymic) ? "$fr->surname $fr->realname $fr->patronymic": $fr->realname . ($fr->patronymic ? " " . $fr->patronymic:'') . ($fr->surname ? " " . $fr->surname:'');
$nn = ($fr->surname && $fr->patronymic) ? __('ФИО') : __('Имя');
$post->content[] = '[b]'.$nn.' :[/b] '.($name? $name : __('не заполнено'));
if ($fr->ank_d_r && $fr->ank_m_r && $fr->ank_g_r) {
$post->content[] = '[b]'.__('Дата рождения').':[/b] '.__('%s %s %s',$fr->ank_d_r, misc::getLocaleMonth($fr->ank_m_r),$fr->ank_g_r); 
} elseif ($fr->ank_d_r && $fr->ank_m_r) {
$post->content[] = '[b]'.__('День рождения').':[/b] '.__('%s %s', $fr->ank_d_r, misc::getLocaleMonth($fr->ank_m_r));
} else {
$post->content[] = '[b]'.__('Дата рождения').':[/b] '.__('не заполнено'); 
}
if ($fr->is_friend($user) || $fr->vis_icq) {
$post->content[] = '[b]ICQ UIN:[/b] '.($fr->icq_uin? $fr->icq_uin : __('не заполнено'));
} else {
$post->content[] = '[b]ICQ UIN:[/b] Информация скрыта [url="/faq.php?info=hide&amp;return='.URL.'"][?][/url]';
}
if ($fr->is_friend($user) || $fr->vis_skype) {
$post->content[] = '[b]Skype:[/b] '.($fr->skype? $fr->skype: __('не заполнено'));
} else {
$post->content[] = '[b]Skype:[/b] Информация скрыта [url="/faq.php?info=hide&amp;return='.URL.'"][?][/url]';
}
if ($fr->is_friend($user) || $fr->vis_email) {
$post->content[] = '[b]E-mail:[/b] '.($fr->email? $fr->email : __('не заполнено'));
} else {
$post->content[] = '[b]E-mail:[/b] Информация скрыта [url="/faq.php?info=hide&amp;return='.URL.'"][?][/url]';
}
$post->time = __('Посл. визит').': '.__('%s',misc::when($fr->last_visit));
}
    
}
$listing->display(__('У пользователя "%s" еще нет друзей', $ank->nick));

$pages->display('?id=' . $ank->id . '&amp;'); // вывод страниц

$doc->ret(__('Анкета "%s"', $ank->nick), '/profile.view.php?id=' . $ank->id);