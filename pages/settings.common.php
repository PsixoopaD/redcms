<?php

include_once '../sys/inc/start.php';
$doc = new document(1); // инициализация документа для браузера
$doc->title = __('Общие настройки');

if (isset($_POST['save'])) {
  $user->vis_email = !empty($_POST ['email']);
    $user->vis_icq = !empty($_POST ['icq']);
    $user->vis_friends = !empty($_POST ['friends']);
    $user->vis_skype = !empty($_POST ['skype']);
    $user->mail_only_friends = !empty($_POST ['mail_only_friends']);
    $user->notice_mention = !empty($_POST ['notice_mention']);
    $user->notification_forum = !empty($_POST ['notification_forum']);
    $user->notif_zvuk = !empty($_POST ['notif_zvuk']);
    // количество пунктов на страницу
    if (!empty($_POST['items_per_page'])) {
        $ipp = (int) $_POST['items_per_page'];
        if ($ipp >= 5 && $ipp <= 99)
            $user->items_per_page = $ipp;
        else
            $doc->err(__('Недопустимое количество пунктов на страницу'));
    }
    // временной сдвиг
    if (isset($_POST['time_shift'])) {
        $ipp = (int) $_POST['time_shift'];
        if ($ipp >= - 12 && $ipp <= 12) {
            $user->time_shift = $ipp;
        } else {
            $doc->err(__('Недопустимое время'));
        }
    }

    $doc->msg(__('Параметры успешно приняты'));
 header('Refresh: 1; url=/settings.common.php');
}



$form = new form('?' . passgen());

$form->checkbox('email', __('Показывать в анкете %s', 'E-Mail'), $user->vis_email);
$form->checkbox('icq', __('Показывать в анкете %s', 'ICQ'), $user->vis_icq);
$form->checkbox('skype', __('Показывать в анкете %s', 'Skype'), $user->vis_skype);
$form->checkbox('friends', __('Показывать список друзей в анкете'), $user->vis_friends);
$form->bbcode(__('Ваши друзья будут видеть все ваши данные независимо от установленных параметров'));
$form->checkbox('mail_only_friends', __('Принимать личные сообщения только от друзей'), $user->mail_only_friends);
$form->checkbox('notice_mention', __('Упоминание ника (@%s)', $user->login), $user->notice_mention);
$form->checkbox('notification_forum', __('Ответ на форуме'), $user->notification_forum);
$form->checkbox('notif_zvuk', __('Звуковое оповещение [WEB]'), $user->notif_zvuk);

$form->text('items_per_page', __('Пунктов на страницу') . ' (' . $dcms->browser_type . ') [5-99]', $user->items_per_page);
$opt = array(); // Врменной сдвиг
for ($i = - 12; $i < 12; $i++) {
    $opt[] = array($i, date('G:i', TIME + $i * 60 * 60), $user->time_shift == $i);
}
$form->select('time_shift', __('Мое время'), $opt);
$form->button(__('Сохранить настройки'), 'save');
$form->display();

if (isset($_POST['save_pass'])) {
    if (isset($_POST['password_old']) && crypt::hash($_POST['password_old'], $dcms->salt) == $user->password) {
        if (isset($_POST['password_new1']) && isset($_POST['password_new2'])) {
            if ($_POST['password_new1'] !== $_POST['password_new2'])
                $doc->err(__('Пароли не совпадают'));
            elseif (!is_valid::password($_POST['password_new1']))
                $doc->err(__('Не корректный новый пароль')); else {
                $_SESSION[SESSION_PASSWORD_USER] = $_POST['password_new1'];
                setcookie(COOKIE_USER_PASSWORD, crypt::encrypt($_POST['password_new1'], $dcms->salt_user), time() + 60 * 60 * 24 * 365);
                $user->password = crypt::hash($_POST['password_new1'], $dcms->salt);
                $doc->msg(__('Пароль успешно изменен'));
            }
        }
    } else
        $doc->err(__('Старый пароль неверен'));
}

$form = new form('?' . passgen());
$form->password('password_old', __('Старый пароль'));
$form->password('password_new1', __('Новый пароль'));
$form->password('password_new2', __('Подтверждение'));
$form->button(__('Применить новый пароль'), 'save_pass');
$form->display();


$doc->ret(__('Личное меню'), '/menu.user.php');