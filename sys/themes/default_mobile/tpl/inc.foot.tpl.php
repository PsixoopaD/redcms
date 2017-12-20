<? if ($actions) { ?>
    <div style="margin: 0 10px;" id="actions">
        <?= $this->section($actions, '<a class="act" href="{url}">{name}</a>'); ?>
    </div>
<? } ?>

<? if ($returns OR !IS_MAIN) { ?>
    <div id="returns">
        <?= $this->section($returns, '<a class="ret" href="{url}">{name}</a>'); ?>
       
    </div>
<? } ?>