<?php if ($server['startovan'] == "1") { ?>
    <li style="float:right;">
        <form action="process.php?task=restart_server" method="POST">
            <input hidden type="text" name="server_id" value="<?php echo $server_id; ?>">
            <button class="restart_btn" style="background:none;border:none;color: yellow;font-size: 13px;">
                <i class="fa fa-refresh" aria-hidden="true" style=""></i> Restart
            </button>
        </form>
    </li>
    <li style="float:right;">
        <form action="process.php?task=stop_server" method="POST">
            <input hidden type="text" name="server_id" value="<?php echo $server_id; ?>">
            <button href="" class="stop_btn" style="background:none;border:none;color: #ff0000;font-size: 13px;">
                <i class="fa fa-power-off" aria-hidden="true" style=""></i> Stop
            </button>
        </form>
    </li> 
<?php } else { ?>
    <li style="float:right;margin-top: -0.5%;">
        <form action="process.php?task=start_server" method="POST">
            <input hidden type="text" name="server_id" value="<?php echo $server_id; ?>">
            <button href="" class="start_btn" style="background:none;border:none;color: #0bc400;font-size: 13px;">
                <i class="fa fa-power-off" aria-hidden="true" style=""></i> Start
            </button>
        </form>
    </li> 
    
    <?php if (is_pin() == false) { ?>
        <li style="float:right;">
            <button class="restart_btn" style="background:none;border:none;color: #ff0000;font-size: 13px;" data-toggle="modal" data-target="#pin-auth">
                <i class="fa fa-refresh" aria-hidden="true" style=""></i> Reinstall
            </button>
        </li>
    <?php } else { ?>
        <li style="float:right;">
            <form action="process.php?task=reinstall_server" method="POST">
                <input hidden type="text" name="server_id" value="<?php echo $server_id; ?>">
                <button class="restart_btn" style="background:none;border:none;color: #ff0000;font-size: 13px;">
                    <i class="fa fa-refresh" aria-hidden="true" style=""></i> Reinstall
                </button>
            </form>
        </li>
        <li style="float:right;">
            <form action="process.php?task=obrisi_sve" method="POST">
                <input hidden type="text" name="server_id" value="<?php echo $server_id; ?>">
                <button class="kill_btn" style="background:none;border:none;color: #ff0000;font-size: 13px;">
                    <i class="fa fa-power-off" aria-hidden="true" style=""></i> Kill
                </button>
            </form>
        </li>
    <?php } ?>
<?php } ?> 