<?php
/**
* @var $this document
*/
?><!DOCTYPE html>
<html>
<head>
<title><?= $title ?></title>
<link rel="shortcut icon" href="/favicon.ico"/>
<link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
<link rel="stylesheet" href="/sys/themes/.common/icons.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/sys/themes/.common/font-awesome.min.css"/>
<link rel="stylesheet" href="<?= $path ?>/theme_light.css" type="text/css"/>
<link rel="stylesheet" href="<?= $path ?>/style.css?6" type="text/css"/>
<meta name="viewport" content="minimum-scale=1.0,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<?php if ($description) { ?>
<meta name="description" content="<?= $description ?>" /><?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?= $keywords ?>" /><?php } ?>
<script>
var translate = {
'user_menu': "<?= __("Личное меню") ?>",
'auth': "<?= __("Авторизация") ?>",
'reg': "<?= __("Регистрация") ?>",
rating_down_message: '<?=__('Подтвердите понижение рейтинга сообщения.').($dcms->forum_rating_down_balls?"\\n".__('Будет списано баллов: %s',$dcms->forum_rating_down_balls):'')?>'
};
var user = {
'id': "<?=$user->id?>",
'group': "<?=$user->group?>",
'friend_new_count': "<?=$user->friend_new_count?>",
'mail_new_count': "<?=$user->mail_new_count?>",
'login': "<?=$user->login?>"
};
var URL = "<?=URL?>";
</script>
<script src="/sys/themes/.common/jquery-2.1.1.min.js"></script>
<script src="/sys/themes/.common/dcmsApi.js"></script>
<script src="<?= $path ?>/js.js?6"></script>
<link rel="stylesheet" type="text/css" href="<?= $path ?>/hin.css"/>
<script> 
$(document).ready(function(){
var touch = $('#touch-menu');
var menu = $('.menu');
$(touch).on('click', function(e) {
e.preventDefault();
menu.slideToggle();
});
});    
</script>
</head>
<body class="theme_light">
<?php if ($user->notif_zvuk) { ?>
<audio id="audio_notify" preload="auto" class="ng-hide">
<source src="/sys/themes/.common/notify.mp3"/>
<source src="/sys/themes/.common/notify.ogg" />
</audio>
<?php } ?>
<div id="title"><div id="header-inner">
<a  class="icon_men" href="/">  <div id="icon_men"></div></a> <a id="touch-menu" class="icon_menu" href="#">  <div id="icon_menu"></div></a>
<center><?=$dcms->sitename?></center>
<h1 id=""><?= $title ?></h1>
<?php $this->displaySection('after_title') ?>
</div> 
</div> 
<div class="header-bottom">
<div class="wrap">
<div style="display: none;" class="menu">
<a id="login" class="vhod" href="/login.php?return=<?= URL ?>"></a>
<a id="reg" class="vhod" href="/reg.php?return=<?= URL ?>"></a>
<?php $this->displaySection('menu') ?>
</div>
<div class="clear"></div>
</div>
</div>
</div>
<?php $this->displaySection('after_title') ?>
<?php if($tabs){?><div id="tabs">
<?= $this->section($tabs, '<a class="gradient_grey border tab sel{selected}" href="{url}">{name}</a>', true); ?>
</div>
<?php }?>
<?php $this->displaySection('before_content') ?>
<div id="content">
<div id="messages">
<?= $this->section($err, '<div class="blok_msg err">{text}</div>'); ?>
<?= $this->section($msg, '<div class="blok_msg msg">{text}</div>'); ?>
</div>
 <?php if ($user->friend_new_count) { ?><div class="opovesh">
            <a id='user_friend' href='/my.friends.php'><?= __("Друзья") ?> +<span><?= $user->friend_new_count ?></span></a></div>
        <?php } ?>
        <?php if ($user->mail_new_count) { ?>
           <div class="opovesh"> <a id='user_mail' href='/my.mail.php?only_unreaded'><?= __("Почта") ?> +<span><?= $user->mail_new_count ?></span></a></div>
        <?php } ?>

<?php if(IS_MAIN){
$this->displaySection('news');
$this->displaySection('chat_mini');
$this->displaySection('forum');
}?>

<?php if(!IS_MAIN)$this->displaySection('content') ?>
</div>
<?php $this->displaySection('after_content') ?>
<?php $this->display('inc.foot.tpl') ?>
</div> 
<div id="foot">
<a class="count" href="/users.php" rel="nofollow">Нас уже <?
$res = DB::me()->query("SELECT COUNT(*) FROM `users`");
echo $res->fetchColumn();?></a>
<a class="count" href="/online.users.php" rel="nofollow">На сайте
<?php  $res = DB::me()->query("SELECT COUNT(*) FROM `users_online`");
echo $res->fetchColumn();?></a>
<a class="count" href="/online.guest.php" rel="nofollow">Гостей
<?php  $res =DB::me()->query("SELECT COUNT(*) FROM `guest_online` WHERE `conversions` >= '5'");
echo $res->fetchColumn();?></a>
<br/><?= $copyright ?>
<br><?= __("Язык") ?>:<a href='/language.php?return={{URL}}' style='background-image: url(<?= $lang->icon ?>); background-repeat: no-repeat; background-position: 5px 2px; padding-left: 23px;'><?= $lang->name ?></a>
<br><?= __("Время генерации страницы: %s сек", $document_generation_time) ?>
</div>  
</body>
</html>