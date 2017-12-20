<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Мой профиль');
$ank = (empty($_GET['id'])) ? $user:new user((int) $_GET['id']);
if (isset($_POST ['save'])) {

    $user->realname = text::for_name(@$_POST ['realname']);
	$user->surname = text::for_name(@$_POST ['surname']);
	$user->patronymic = text::for_name(@$_POST ['patronymic']);
    $user->icq_uin = text::icq_uin(@$_POST ['icq']);

    if (isset($_POST ['ank_d_r'])) {
        if ($_POST ['ank_d_r'] == null)
            $user->ank_d_r = '';
        else {
            $ank_d_r = (int) $_POST ['ank_d_r'];
            if ($ank_d_r >= 1 && $ank_d_r <= 31)
                $user->ank_d_r = $ank_d_r;
            else
                $doc->err(__('Не корректный формат дня рождения'));
        }
    }

    if (isset($_POST ['ank_m_r'])) {
        if ($_POST ['ank_m_r'] == null)
            $user->ank_m_r = '';
        else {
            $ank_m_r = (int) $_POST ['ank_m_r'];
            if ($ank_m_r >= 1 && $ank_m_r <= 12)
                $user->ank_m_r = $ank_m_r;
            else
                $doc->err(__('Не корректный формат месяца рождения'));
        }
    }

    if (isset($_POST ['ank_g_r'])) {
        if ($_POST ['ank_g_r'] == null)
            $user->ank_g_r = '';
        else {
            $ank_g_r = (int) $_POST ['ank_g_r'];
            if ($ank_g_r >= date('Y') - 100 && $ank_g_r <= date('Y'))
                $user->ank_g_r = $ank_g_r;
            else
                $doc->err(__('Не корректный формат года рождения'));
        }
    }

    if (isset($_POST ['skype'])) {
        if (empty($_POST ['skype']))
            $user->skype = '';
        elseif (!is_valid::skype($_POST ['skype']))
            $doc->err(__('Указан не корректный %s', 'Skype login'));
        else {
            $user->skype = $_POST ['skype'];
        }
    }

    if (!empty($_POST ['wmid'])) {
        if ($user->wmid && $user->wmid != $_POST ['wmid']) {
            $doc->err(__('Активированный WMID изменять и удалять запрещено'));
        } elseif (!is_valid::wmid($_POST ['wmid'])) {
            $doc->err(__('Указан не корректный %s', 'WMID'));
        } elseif ($user->wmid != $_POST ['wmid']) {
            $user->wmid = $_POST ['wmid'];
        }
    }

    if (isset($_POST ['email'])) {
        if (empty($_POST ['email']))
            $user->email = '';
        elseif (!is_valid::mail($_POST ['email']))
            $doc->err(__('Указан не корректный %s', 'E-Mail'));
        else {
            $user->email = $_POST ['email'];
        }
    }

     $user->description = text::input_text(@$_POST ['description']);
	 $user->languages = text::input_text(@$_POST ['languages']);
$user->sex =  (int)$_POST['sex'];
    $doc->msg(__('Параметры успешно приняты'));
}

$form = new form('?' . passgen());
if (!$user->vk_id){
$form->text('realname', __('Имя'), $user->realname);
$form->text('surname', __('Фамилия'), $user->surname);}
$form->text('patronymic', __('Отчество'), $user->patronymic);

$se= array();
$se[] = array('1', __('Мужской'),  $user->sex== '1');
$se[] = array('0', __('Женский'),  $user->sex== '0');
$form->select('sex', __('Ваш пол'), $se);

$opt = array(); 
for ($i = 1; $i < 32; $i++) {
    $opt[] = array($i, $i, $user->ank_d_r == $i);
}
$form->select('ank_d_r', __('День рождения'), $opt);

$opt2 = array(); 
for ($i2 = 1; $i2 < 13; $i2++) {
    $opt2[] = array($i2, $i2, $user->ank_m_r == $i2);
}
$form->select('ank_m_r', __('Месяц рождения'), $opt2);

$opt3 = array(); 
for ($i3 = 1960; $i3 < 2007; $i3++) {
    $opt3[] = array($i3, $i3, $user->ank_g_r == $i3);
}
$form->select('ank_g_r', __('Год рождения'), $opt3);
$form->text('icq', 'ICQ UIN', $user->icq_uin);
$form->text('skype', 'Skype', $user->skype);
$form->text('email', 'E-Mail', $user->email);
$form->text('wmid', 'WMID', $user->wmid);
$form->text('languages', 'Какими языками владеете?', $user->languages);
$form->textarea('description', __('О себе') . ' [256]', $user->description);
$form->button(__('Сохранить'), 'save');
$form->display();

if (isset($_POST ['savee'])) {

    if(!empty($_POST['country']) && $_POST['country'] != $ank->country) {

    $c = $db->prepare("SELECT * FROM `countries` WHERE `code` = ?");
    $c->execute(Array(text::input_text($_POST['country'])));

        if($country = $c->fetch()) {
           $ank->region_c = 0; #fix
           $user->country = $country['code'];
        } else $doc->err(__('Country not found')); }

    if(!empty($_POST['region']) && $ank->country && (int) $_POST['region'] != $ank->region_c) {

    $r = $db->prepare("SELECT * FROM `regions` WHERE `id` = ? AND  `country` = ?");
    $r->execute(Array((int) $_POST['region'], $ank->country));

        if($region = $r->fetch()) {
           $ank->region_c = $region['id'];
           $ank->region_t = $region['name'];
        } else $doc->err(__('This Region has not been found for Your Country')); }

}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());

$elements = array();
$options = array();
$countries = $db->query("SELECT * FROM `countries` ORDER BY `english_n` ASC;");
$u_country = ($ank->country)? $ank->country:"RU"; /* Страна пользователя */
while ($country = $countries->fetch())
$options [] = array($country['code'], ($country['code'] == $user->country ? $country['country_n'] : $country['english_n']), $country['code'] == $u_country);
$elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Страна'), 'info' => array('name' => 'country', 'options' => $options, 'br' => 1));

if ($ank->country) {
$r = $db->prepare("SELECT * FROM `regions` WHERE `country` = ?");
$r ->execute(array($ank->country));
if ($r->rowCount()) {
while ($region = $r->fetch())
 $regions[] = array($region['id'], $region['name'], $region['id'] == $ank->region_c || $region['name'] == $ank->region_t);
$elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Область'), 'info' => array('name' => 'region', 'options' => $regions, 'br' => 1));
 } 
} 

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'savee', 'value' => __('Сохранить'))); 
$form->assign('el', $elements);
$form->display('input.form.tpl');
$doc->ret(__('Анкета'), '/profile.view.php');
$doc->ret(__('Личное меню'), '/menu.user.php');