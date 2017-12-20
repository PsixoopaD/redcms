<div class="Profile" style="background:url(<?= $fon ?>) center center no-repeat;background-size: cover;">
  <div class="avatar_img" style="background-image:url(<?= $avatar[0] ?>);"> </div>  
 <? if ($add[0]) { ?><a href="<?= $add[0] ?>" class="addfon"><?= $add[1] ?></a><?}?>
<? if ($ava[0]) { ?><br/><br/><a href="<?= $ava[0] ?>" class="addfon"><?= $ava[1] ?></a><?}?>
<? if ($editank[0]) { ?><br/><br/><a href="<?= $editank[0] ?>" class="addfon"><?= $editank[1] ?></a><?}?>
<?if ($sms[0]){?><br/><br/><a href="<?= $sms[0] ?>" class="addfon"><?= $sms[1] ?></a><?}?>
<?if ($frend[0]){?><br/><br/><a href="<?= $frend[0] ?>" class="addfon"><?= $frend[1] ?></a><?}?>
    <div class="uname">
        <?= $login ?>
        <div class="agecity">
            <span class="<?= $on[0] ?>"><?= $on[1] ?></span>
        </div>
        <div class="ank_d_r"><?= $dr ?></div>
<div class="ank_d_r"><?= $gorod ?></div>
    </div>
</div>