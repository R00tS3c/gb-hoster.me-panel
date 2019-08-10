<?php if ($server['startovan'] == "1") { ?>
    <li style="float:right;">
        <form action="voice_process.php?task=stop" method="POST">
            <input hidden="" type="text" name="voiceid" value="<?php echo $server_id; ?>">
            <button href="" class="start_btn" style="background:none;border:none;">
                <i class="glyphicon glyphicon-stop" style="font-size: 20px;"></i> Stop
            </button>
        </form>
    </li>
<?php } else { ?>
    <li style="float:right;">
        <form action="voice_process.php?task=start" method="POST">
            <input hidden="" type="text" name="voiceid" value="<?php echo $server_id; ?>">
            <button href="" class="start_btn" style="background:none;border:none;">
                <i class="glyphicon glyphicon-play" style="font-size: 20px;"></i> Start
            </button>
        </form>
    </li>
<?php } ?>