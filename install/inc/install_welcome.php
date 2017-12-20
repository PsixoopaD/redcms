<?php
class install_welcome {
    function actions()
    {
        return true;
    }

    function form()
    {

?><div class="update">
        		      <a href="#">
<img class="update_img" src="/install/1sl.jpg"></img></a>
				      <a href="#">
<img class="update_img" src="/install/2sl.jpg"></img></a>
				      <a href="#">
<img class="update_img" src="/install/3sl.jpg"></img></a>
		    </div>
        <h3><center>Добро пожаловать в мастер установки dcms 7.5 Revolution</center></h3>
<div class="sp">Что же такое Revo Dcms?</div>
<ul>
<li type="square">Полноценная система управления сайтом для вас и ваших пользователей.</li>
<li type="square">Хороший гибкий функционал.</li>
<li type="square">Легкий удобный шаблонизатор (листинг), который вам дает легкость в настройке дизайна.</li>
<li type="square">Портал, в котором можно обмениваться личными сообщениями, файлами.</li>
<li type="square">Портал, который не даст вашим пользователям скучать, играя в разные игры сайта.</li>
</ul>
<?
        return true;
    }
}

?>
