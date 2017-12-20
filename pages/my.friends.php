<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->ret(__('Анкета'), '/profile.view.php');
$doc->title = __('Мои друзья');

$res = $db->prepare("SELECT COUNT(*) FROM `friends` WHERE `id_user` = ? AND `confirm` = '0'");
$res->execute(Array($user->id));
$user->friend_new_count = $res->fetchColumn();

$pages = new pages;
$res = $db->prepare("SELECT COUNT(*) FROM `friends` WHERE `id_user` = ?");
$res->execute(Array($user->id));
$pages->posts = $res->fetchColumn();

$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? ORDER BY `confirm` ASC, `time` DESC LIMIT " . $pages->limit . ";");
$q->execute(Array($user->id));

$listing = new listing();
while ($friend = $q->fetch()) {
    $post = $listing->post();
    $ank = new user($friend['id_friend']);
    $post->url = '/profile.view.php?id=' . $ank->id;
    $post->title = $ank->nick();
   $post->image = $ank->ava();
$post->action('mail', '/my.mail.php?id='.$ank->id);
$post->action('delete', '/profile.view.php?id='.$ank->id.'&amp;friend=delete');
    $post->highlight = !$friend['confirm'];
    $post->content[] = $friend['confirm'] ? null : __('Хочет быть Вашим другом');
$name = ($ank->surname && $ank->patronymic) ? "$ank->surname $ank->realname $ank->patronymic": $ank->realname . ($ank->patronymic ? " " . $ank->patronymic:'') . ($ank->surname ? " " . $ank->surname:'');
$nn = ($ank->surname && $ank->patronymic) ? __('ФИО') : __('Имя');
$post->content[] = '[b]'.$nn.' :[/b] '.($name? $name : __('не заполнено'));
if ($ank->ank_d_r && $ank->ank_m_r && $ank->ank_g_r) {
$post->content[] = '[b]'.__('Дата рождения').':[/b] '.__('%s %s %s',$ank->ank_d_r, misc::getLocaleMonth($ank->ank_m_r),$ank->ank_g_r); 
} elseif ($ank->ank_d_r && $ank->ank_m_r) {
$post->content[] = '[b]'.__('День рождения').':[/b] '.__('%s %s', $ank->ank_d_r, misc::getLocaleMonth($ank->ank_m_r));
} else {
$post->content[] = '[b]'.__('Дата рождения').':[/b] '.__('не заполнено'); 
}
if ($ank->is_friend($user) || $ank->vis_icq) {
$post->content[] = '[b]ICQ UIN:[/b] '.($ank->icq_uin? $ank->icq_uin : __('не заполнено'));
} else {
$post->content[] = '[b]ICQ UIN:[/b] Информация скрыта [url="/faq.php?info=hide&amp;return='.URL.'"][?][/url]';
}
if ($ank->is_friend($user) || $ank->vis_skype) {
$post->content[] = '[b]Skype:[/b] '.($ank->skype? $ank->skype: __('не заполнено'));
} else {
$post->content[] = '[b]Skype:[/b] Информация скрыта [url="/faq.php?info=hide&amp;return='.URL.'"][?][/url]';
}
if ($ank->is_friend($user) || $ank->vis_email) {
$post->content[] = '[b]E-mail:[/b] '.($ank->email? $ank->email : __('не заполнено'));
} else {
$post->content[] = '[b]E-mail:[/b] Информация скрыта [url="/faq.php?info=hide&amp;return='.URL.'"][?][/url]';
}
$post->time = __('Посл. визит').': '.__('%s',misc::when($ank->last_visit));
}
$listing->display(__('Друзей нет'));

$pages->display('?');

$doc->ret(__('Личное меню'), '/menu.user.php');