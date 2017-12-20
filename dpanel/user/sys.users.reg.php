<?php

include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
if(!$user->access('dpanel_reg_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->ret(__('Упр. Польз.'), './');
$doc->ret(__('Админка'), '../');
$doc->title = __('Параметры регистрации');
if (isset($_POST['save'])) {
    $dcms->reg_open = (int) !empty($_POST['reg_open']);
    $dcms->reg_with_mail = (int) !empty($_POST['reg_with_mail']);
    $dcms->clear_users_not_verify = (int) !empty($_POST['clear_users_not_verify']);
    $dcms->reg_with_rules = (int) !empty($_POST['reg_with_rules']);
    $dcms->user_write_limit_hour = (int) $_POST['user_write_limit_hour'];     
    $dcms->vk_auth_enable = (int) !empty($_POST['vk_auth_enable']);
    $dcms->vk_reg_enable = (int) !empty($_POST['vk_reg_enable']);
    $dcms->vk_auth_email_enable = (int) !empty($_POST['vk_auth_email_enable']);
    $dcms->vk_app_id = (int) text::input_text($_POST['vk_app_id']);
    $dcms->vk_app_secret = text::input_text($_POST['vk_app_secret']);
    $dcms->vk_community = (int) text::input_text($_POST['vk_community']); 
    $dcms->save_settings($doc);
}

$form = new form('?' . passgen());
$form->checkbox('reg_open', __('Разрешить регистрацию'), $dcms->reg_open);
$form->checkbox('reg_with_mail', __('Активация по E-mail'), $dcms->reg_with_mail);
$form->checkbox('clear_users_not_verify', __('Удалять неактивированных пользователей более суток'), $dcms->clear_users_not_verify);
$form->checkbox('reg_with_rules', __('Соглашение с правилами'), $dcms->reg_with_rules);
$form->text('user_write_limit_hour', __('Разрешено писать через (часы) после регистрации'), $dcms->user_write_limit_hour);
$form->checkbox('vk_auth_enable', __('Разрешить авторизацию через VK'), $dcms->vk_auth_enable);
$form->checkbox('vk_auth_email_enable', __('Разрешить авторизацию VK зарегистрированного пользователя при совпадении e-mail'), $dcms->vk_auth_email_enable);
$form->checkbox('vk_reg_enable', __('Разрешить регистрацию VK'), $dcms->vk_reg_enable);
$form->text('vk_app_id', __('ID приложения VK'), $dcms->vk_app_id);
$form->text('vk_app_secret', __('Защищенный ключ VK'), $dcms->vk_app_secret);
$form->text('vk_community', __('ID cообщества VK'), $dcms->vk_community);
$form->button(__('Применить'), 'save');
$form->display();
