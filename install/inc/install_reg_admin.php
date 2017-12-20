<?php

class install_reg_admin {

    var $login = 'Administrator';
    var $pass1 = '';
    var $pass2 = '';
    var $err_login;
    var $err_pass1;
    var $err_pass2;
    var $settings = array();
    var $users_count = 0;
    var $adm_count = 0;

    function __construct() {
        db_connect();
        $this->err_login = &$_SESSION['reg_admin']['err_login'];
        $this->err_pass1 = &$_SESSION['reg_admin']['err_pass1'];
        $this->err_pass2 = &$_SESSION['reg_admin']['err_pass2'];

        $this->login = &$_SESSION['reg_admin']['login'];
        $this->pass1 = &$_SESSION['reg_admin']['pass1'];
        $this->pass2 = &$_SESSION['reg_admin']['pass2'];

        $this->settings = &$_SESSION['settings'];

        $this->users_count = DB::me()->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
        $this->adm_count = DB::me()->query("SELECT COUNT(*) FROM `users` WHERE `group` > '1'")->fetchColumn();
    }

    function actions() {
        $this->err_login = false;
        $this->err_pass1 = false;
        $this->err_pass2 = false;

        $return = false;

        if (isset($_POST['login']))
            if (is_valid::nick($_POST['login'])) {
                $this->login = $_POST['login'];
                $res = DB::me()->prepare("SELECT COUNT(*) FROM `users` WHERE `login` = ?");
                $res->execute(Array($this->login));
                if ($row = $res->fetch() AND !$row['cnt']) {
                    if (empty($_POST['password']))
                        $this->err_pass1 = true;
                    elseif (empty($_POST['password_retry']))
                        $this->err_pass2 = true;
                    elseif ($_POST['password_retry'] != $_POST['password']) {
                        $this->err_pass1 = true;
                        $this->err_pass2 = true;
                    } elseif (!is_valid::password($_POST['password'])) {
                        $this->err_pass1 = true;
                        $this->err_pass2 = true;
                    } else {
                        // если нет зарегистрированных пользователей, то генегируем новую соль
                        if (!$this->users_count)
                            $this->settings['salt'] = passgen();
                    

                        $sex = (int) !empty($_POST['sex']);
                        $this->pass2 = $this->pass1 = $_POST['password'];
                        $res = DB::me()->prepare("INSERT INTO `users` (`reg_date`, `group`, `login`, `password`, `sex`) 
                            values(?, '6', ?, ?, ?)");
                        $res->execute(Array(TIME, $this->login, crypt::hash($this->pass1, $this->settings['salt']), $sex));
                        $return = true;
                    }
                }else {
                    $return = false;
                    $this->err_login = true;
                }
            } else {
                $return = false;
                $this->err_login = true;
            }

        return $return;
    }

    function form() {
        echo "<div style='color: white; padding: 5px; background-color:" . ($this->err_login ? '#FF9498' : '#008AD5') . "'>";
        echo __('Логин') . ":<br /><input type='text' name='login' value='" . text::toValue($this->login) . "' /><br />";
        echo "</div>";
        echo "<div style='color: white; padding: 5px; background-color:" . ($this->err_pass1 ? '#FF9498' : '#008AD5') . "'>";
        echo __('Пароль') . ":<br /><input type='password' name='password' value='" . text::toValue($this->pass1) . "' /><br />";
        echo "</div>";
        echo "<div style='color: white; padding: 5px; background-color:" . ($this->err_pass2 ? '#FF9498' : '#008AD5') . "'>";
        echo __('Подтверждение') . ":<br /><input type='password' name='password_retry' value='" . text::toValue($this->pass2) . "' /><br />";
        echo "</div>";
        echo __('Пол') . ":<br /><select name='sex'><option value='1'>" . __('Мужской') . "</option><option value='0'>" . __('Женский') . "</option></select>";


        return true;
    }

}