<?php
/**
* @var $this document
*/
?><!DOCTYPE html>
<html ng-app="Dcms">
<head>
<title><?= $title ?></title>
<link rel="shortcut icon" href="/favicon.ico"/>
<link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
<link rel="stylesheet" href="/sys/themes/.common/icons.css" type="text/css"/>
<link rel="stylesheet" href="/sys/themes/.common/theme_light.css" type="text/css"/>
<link rel="stylesheet" href="/sys/themes/.common/animate.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?= $path ?>/style.css?14"/>
<link rel="stylesheet" type="text/css" href="/sys/themes/.common/font-awesome.min.css"/>
<noscript>
<meta http-equiv="refresh" content="0; URL=/pages/bad_browser.html"/>
</noscript>
<script>
(function () {
var getIeVer = function () {
var rv = -1; // Return value assumes failure.
if (navigator.appName === 'Microsoft Internet Explorer') {
var ua = navigator.userAgent;
var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
if (re.exec(ua) !== null)
rv = parseFloat(RegExp.$1);
}
return rv;
};
var ver = getIeVer();
if (ver !== -1 && ver < 9) {
window.location.href = "/pages/bad_browser.html";
}
})();
</script>
<script charset="utf-8" src="/sys/themes/.common/jquery-2.1.1.min.js" type="text/javascript"></script>
<script charset="utf-8" src="/sys/themes/.common/angular.min.js" type="text/javascript"></script>
<script charset="utf-8" src="/sys/themes/.common/angular-animate.min.js" type="text/javascript"></script>
<script charset="utf-8" src="/sys/themes/.common/dcmsApi.js" type="text/javascript"></script>
<script charset="utf-8" src="/sys/themes/.common/elastic.js" type="text/javascript"></script>
<script charset="utf-8" src="<?= $path ?>/js.js?6" type="text/javascript"></script>
<meta name="generator" content="DCMS <?= dcms::getInstance()->version ?>"/>
<? if ($description) { ?>
<meta name="description" content="<?= $description ?>"/>
<? } ?>
<? if ($keywords) { ?>
<meta name="keywords" content="<?= $keywords ?>"/>
<? } ?>
<script>
user = <?=json_encode(current_user::getInstance()->getCustomData(array('id', 'group', 'mail_new_count', 'friend_new_count', 'nick')))?>;
    translates = {
            bbcode_b: '<?= __('Текст жирным шрифтом') ?>',
            bbcode_i: '<?= __('Текст курсивом') ?>',
            bbcode_u: '<?= __('Подчеркнутый текст') ?>',
 bbcode_red: '<?= __('Красный текст') ?>',
 bbcode_green: '<?= __('Зеленый текст') ?>',
 bbcode_blue: '<?= __('Синий текст') ?>',
 bbcode_mark: '<?= __('Выделенный текст') ?>',
 bbcode_no: '<?= __('Зачеркнутый текст') ?>',
            bbcode_img: '<?= __('Вставка изображения') ?>',
            bbcode_php: '<?= __('Выделение PHP-кода') ?>',
            bbcode_big: '<?= __('Увеличенный размер шрифта') ?>',
            bbcode_small: '<?= __('Уменьшенный размер шрифта') ?>',
            bbcode_gradient: '<?= __('Цветовой градиент') ?>',
            bbcode_hide: '<?= __('Скрытый текст') ?>',
            bbcode_spoiler: '<?= __('Свернутый текст') ?>',
            smiles: '<?= __('Смайлы') ?>',
            form_submit_error: '<?= __('Ошибка связи...') ?>',
            auth: '<?= __("Авторизация") ?>',
            reg: '<?= __("Регистрация") ?>',
            friends: '<?=__("Друзья")?>',
            mail: '<?=__("Почта")?>',
            error: '<?=__('Неизвестная ошибка')?>',
            rating_down_message: '<?=__('Подтвердите понижение рейтинга сообщения.').(dcms::getInstance()->forum_rating_down_balls?"\\n".__('Будет списано баллов: %s',dcms::getInstance()->forum_rating_down_balls):'')?>'
        };
 codes = [
            {Text: 'B', Title: translates.bbcode_b, Prepend: '[b]', Append: '[/b]'},
            {Text: 'I', Title: translates.bbcode_i, Prepend: '[i]', Append: '[/i]'},
            {Text: 'U', Title: translates.bbcode_u, Prepend: '[u]', Append: '[/u]'},
{Text: 'Red', Title: translates.bbcode_red, Prepend: '[red]', Append: '[/red]'},
{Text: 'Green', Title: translates.bbcode_green, Prepend: '[green]', Append: '[/green]'},
{Text: 'Blue', Title: translates.bbcode_blue, Prepend: '[blue]', Append: '[/blue]'},
{Text: 'Mark', Title: translates.bbcode_mark, Prepend: '[mark]', Append: '[/mark]'},
{Text: 'No', Title: translates.bbcode_no, Prepend: '[no]', Append: '[/no]'},
            {Text: 'BIG', Title: translates.bbcode_big, Prepend: '[big]', Append: '[/big]'},
            {Text: 'Small', Title: translates.bbcode_small, Prepend: '[small]', Append: '[/small]'},
            {Text: 'IMG', Title: translates.bbcode_img, Prepend: '[img]', Append: '[/img]'},
            {Text: 'PHP', Title: translates.bbcode_php, Prepend: '[php]', Append: '[/php]'},
            {Text: 'SPOILER', Title: translates.bbcode_spoiler, Prepend: '[spoiler title=""]', Append: '[/spoiler]'},
            {Text: 'HIDE', Title: translates.bbcode_hide, Prepend: '[hide group="0" balls="0"]', Append: '[/hide]'}
        ];
</script>
<style type="text/css">
.ng-hide {
display: none !important;
}
</style>
</head>
<body class="theme_light_full theme_light" ng-controller="DcmsCtrl">
<? if ($user->notif_zvuk) { ?>
<audio id="audio_notify" preload="auto" class="ng-hide">
<source src="/sys/themes/.common/notify.mp3"/>
<source src="/sys/themes/.common/notify.ogg" />
</audio>
<? } ?>
<div id="main">
<div id="top_part">
<div id="header">
<div class="body_width_limit clearfix">
<div class="headfon" style="background: transparent url('http://www.allfons.ru/pic/201201/1920x1080/allfons.ru-6435.jpg') repeat scroll 0% 0% / cover ;">
<div id="title"><?= $title ?>
</div>
</div>
</div>
<div class="fonmenu">
<div class="body_width_limit menuhead">
<div style="width:100%;">
<a class="men" href="/news/"><i class="fa fa-rss" aria-hidden="true"></i> Новости </a>
<a class="men"  href="/forum/"><i class="fa fa-flag" aria-hidden="true"></i> Форум </a>
<a class="men" href="/chat_mini/"><i class="fa fa-comments" aria-hidden="true"></i> Мини-чат </a>
</div>
</div>
</div>
<?php $this->displaySection('header'); ?>
</div>
<div class="body_width_limit clearfix leftmenu">
<div id="left_column">
<div class="gn">
<div class="qv rc aog alu">
<?php
$fon = new user_fon($user->id);
?>
<div class="qx">
<div class="aoh" style=" background-image: url(<?if($user->id){ echo $user->ava();}else{?>/sys/images/raznoe/pol_1.png<?}?>);"> </div>
<span class="knopki">
<?if($user->id){?>
<a class="title_a" href="/my.friends.php"><div class="knop"><i class="fa fa-users" aria-hidden="true"></i></div></a>
<a class="title_a" href="/my.mail.php"><div class="knop"><i class="fa fa-envelope" aria-hidden="true"></i></div></a>
<a class="title_a" href="/menu.user.php"><div class="knop"><i class="fa fa-cogs" aria-hidden="true"></i></div></a>
<a class="title_a" href="/exit.php"><div class="knop"><i class="fa fa-power-off" aria-hidden="true"></i></div></a>
<?}?>
<?if(!$user->id){?><span class="aku">Добро пожаловать!<br/>Нас уже <?=DB::me()->query("SELECT COUNT(*) FROM `users`")->fetchColumn()?>!<br/> Давай и ты к нам!</span><?}else{?>
<span class="left_bar">
<a class="nik_bar" href="/profile.view.php"><?=$user->nick?></a>
<br/> Баллов: <?=$user->balls?><br/>
<a ng-show="+user.friend_new_count" class="title_a white_a ng-hide" href="/my.friends.php" ng-bind="str.friends"></a>
<a ng-show="+user.mail_new_count" class="title_a white_a ng-hide" href="/my.mail.php?only_unreaded" ng-bind="str.mail"></a>
</span>
<?}?>
</span>
</div>
<?if(!$user->id){?>
<div class="qw dj">
<div class="fonknop">
<div class="form" style="box-shadow: 0px 0px; margin: 0px; text-align: left; background: transparent none repeat scroll 0% 0%;">
<form class="ng-scope ng-pristine ng-valid" id="form_1" ng-controller="FormCtrl" method="post" action="/login.php">
<div class="form_title">
Логин:
</div>
<input style="width: 100%;" name="login" type="text">
<br>
<div class="form_title">
Пароль [<a href="/pass.php">забыли</a>]:
</div>
<input style="width: 100%;" name="password" type="password">
<br>
<label style="float: left;">
<input style="" class="checkbox" name="save_to_cookie" value="1" type="checkbox">
<span class="checkbox-custom"></span> Запомнить меня
</label>
<br>
<input style="width: 100%;" class="" value="Авторизация" type="submit">
<br>
</form>
</div>
<?if ($dcms->vk_auth_enable && $dcms->vk_app_id && $dcms->vk_app_secret) {?><a class="vkform" href="https://oauth.vk.com/authorize?client_id=<?=$dcms->vk_app_id?>&scope=email&response_type=code&v=5.72&redirect_uri=http://<?=$_SERVER['HTTP_HOST']?>/vk.php">VK вход</a><?}?>
<a class="regform" href="/reg.php?return={{URL}}">Регистрация</a>

</div>
</div>
<?}?>
</div>

<div class="qw temadn">
<div class="list-group-item-null-mini dop"><div class="left_text"><i class="fa fa-caret-down fa-fw" aria-hidden="true"></i> <a href="#" class="title_a"> Меню</a></div><div class="hr"></div></div>
<a href="/news/" class="menu"> <i class="fa fa-rss" aria-hidden="true"></i> Новости</a>
<a href="/chat_mini/" class="menu"><i class="fa fa-comments" aria-hidden="true"></i> Чат</a>
<a class="menu" href="/forum/"><i class="fa fa-flag" aria-hidden="true"></i> Форум </a>

</div>

<?$this->displaySection('chat_mini');?>

<?$this->displaySection('users');?>



</div>
</div>
<div id="content">

<div class="<?= $actions? 'action_box' : 'ng-hide' ?>">
<?= $this->section($actions, '<a class="action_a" href="{url}"><i class="fa fa-share-alt" aria-hidden="true"></i>
 {name}</a>'); ?>
</div>

<div id="navigation" class="clearfix <?= IS_MAIN ? 'ng-hide' : '' ?>">
<a class="nav_item" href='/'><i class="fa fa-home" aria-hidden="true" style="font-size: 18px;"></i></a>
<?= $this->section($returns, '<a class="nav_item" href="{url}">{name}</a>', true); ?> <span class="nav_item"><?=mb_strimwidth($title, 0, 15, "..")?></span>
</div>
<div id="tabs" class="<?= !$tabs ? 'ng-hide' : '' ?>">
<?= $this->section($tabs, '<a class="tab sel{selected}" href="{url}">{name}</a>', true); ?>
</div>
<div id="messages">
<?= $this->section($err, '<div class="error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> {text}</div>'); ?>
<?= $this->section($msg, '<div class="info"><i class="fa fa-check" aria-hdden="true"></i> {text}</div>'); ?>
</div>
<?php $this->displaySection('content'); ?>
</div>
</div>
<div id="footer">
<div class="body_width_limit footfon">
<span id="copyright">
<a href="/chat_mini/" class="white_a">Мини-чат</a> | <a href="/forum/" class="white_a">Форум</a> | <a href="/news/" class="white_a">Новости</a>
</span>
<span id="language">
<i class="fa fa-language" aria-hidden="true"></i>
<?= __("Язык") ?>:<a href="/language.php?return={{URL}}" style="background-image: url(<?= $lang->icon ?>);" class="langugefoot"><?= $lang->name ?></a>
</span>
<span id="generation">
<i class="fa fa-clock-o" aria-hidden="true"></i> <?= __("VGS: %s сек", $document_generation_time) ?>
</span>
</div>
<div class="body_width_limit">
<span id="copyright" class="copyright" >
<?= $dcms->copyrighte ?>
</span>
</div>
</div>
</div>
</div>
</body>
</html>
