<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Вход vk.com');

if (!$dcms->vk_auth_enable) {
    $doc->err(__('Авторизация через vk.com не доступна'));
    exit;
}

if (!empty($_GET['error'])) {
    if (!empty($_GET['error_description'])) {
        $doc->err(text::toOutput($_GET['error_description']));
    } else {
        $doc->err(__('Не удалось авторизоваться'));
    }
    exit;
}

if (empty($_GET['code'])) {
    header("Location: /");
    exit;
}

if (!$dcms->vk_app_id || !$dcms->vk_app_secret){
    header("Location: /");
    exit;
}

try{
    $vk = new vk($dcms->vk_app_id, $dcms->vk_app_secret);
    $vk->getAccessToken('http://' . $_SERVER['HTTP_HOST'] . '/vk.php', $_GET['code']);
    $vk_user = $vk->getCurrentUser();

    echo '<!--'.json_encode($vk_user).'-->';

    if ($vk->getEmail() && $dcms->vk_auth_email_enable) {
        $q = $db->prepare("SELECT * FROM `users` WHERE `reg_mail` = :email LIMIT 1");
        $q->execute(array(':email' => $vk->getEmail()));
        if ($q->rowCount()) {
            $user_data = $q->fetch();
            $res = $db->prepare("INSERT INTO `log_of_user_aut` (`id_user`,`method`,`iplong`, `time`, `id_browser`, `status`) VALUES (?,'vk',?,?,?,'1')");
            $res->execute(Array($user_data['id'], $dcms->ip_long, TIME, $dcms->browser_id));
            $_SESSION [SESSION_ID_USER] = $user_data['id'];
            $doc->msg(__("Авторизация прошла успешно"));
            exit;
        }
    }

    $q = $db->prepare("SELECT * FROM `users` WHERE `vk_id` = :id_vk LIMIT 1");
    $q->execute(array(':id_vk' => $vk_user['uid']));
    if ($q->rowCount()) {
        $user_data = $q->fetch();
        $res = $db->prepare("INSERT INTO `log_of_user_aut` (`id_user`,`method`,`iplong`, `time`, `id_browser`, `status`) VALUES (?,'vk',?,?,?,'1')");
        $res->execute(Array($user_data['id'], $dcms->ip_long, TIME, $dcms->browser_id));
        $_SESSION [SESSION_ID_USER] = $user_data['id'];
        $doc->msg(__("Авторизация прошла успешно"));
        exit;
    } else if (!$dcms->vk_reg_enable) {
        throw new Exception(__('Регистрация через vk.com запрещена'));
    }

    $res = $db->prepare("INSERT INTO `users` (`reg_date`, `login`, `password`, `sex`, `reg_mail`, `vk_id`, `vk_first_name`, `vk_last_name`)
VALUES (:reg_date, :login, :pass, :sex, :reg_mail, :vk_id, :vk_first_name, :vk_last_name)");
    $res->execute(Array(
        ':reg_date' => TIME,
        ':login' => '$vk.' . $vk_user['uid'],
        ':pass' => $vk->getAccessToken(),
        ':sex' => ($vk_user['sex'] == 0 || $vk_user['sex'] == 2) ? 1 : 0,
        ':reg_mail' => $vk->getEmail(),
        ':vk_id' => $vk_user['uid'],
        ':vk_first_name' => $vk_user['first_name'],
        ':vk_last_name' => $vk_user['last_name']
    ));

    $id = $db->lastInsertId();
    $_SESSION [SESSION_ID_USER] = $id;
    $doc->msg(__("Регистрация прошла успешно"));
}catch (Exception $e){
    $doc->err(__('Не удалось авторизоваться: %s', $e->getMessage()));
    exit;
}
