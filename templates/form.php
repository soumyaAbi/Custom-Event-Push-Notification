<?php
wp_enqueue_style( 'jquery-ui-datetimepickercss1', CUSTOM_PUSH_DIR_PATH . 'assets/bootstrap/css/bootstrap.min.css');
?>
<style>
<!--
.error{
    color: red;
}
-->
</style>
<div id="wrapper">
    <div class="width70 left">
        <form action="" method="post">
            <fieldset>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th colspan="5"><b>Custom Push Notification</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Events List:</td>
                            <td>
                                <select name="post_id">
                                    <option value="">-- Please Select --</option>
                                    <?php if (!(is_array($event_posts) && $event_posts)) { ?>
                                        <?php while ($event_posts->have_posts()) : $event_posts->the_post(); ?>
                                        <option value="<?php echo get_the_ID();?>" <?php echo ($form_data['post_id'] == get_the_ID()) ? 'selected="selected"' : ''; ?>><?php the_title(); ?></option>
                                        <?php endwhile;
                                    }
                                    ?>
                                </select>
                                <?php if (isset($notification_error['post_id'])) {?>
                                <br>
                                <span class="error"><?php echo $notification_error['post_id'];?></span>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td>Message:</td>
                            <td>
                                <textarea name="message" maxlength="<?php echo PUSH_MSG_CHAR_LIMIT;?>" rows="4" cols="50"><?php echo $form_data['message'];?></textarea>
                                <?php if (isset($notification_error['message'])) {?>
                                <br>
                                <span class="error"><?php echo $notification_error['message'];?></span>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td>Schedule Date:</td>
                            <td>
                                <div class="controls input-append date form_datetime" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                    <input size="16" type="text" value="<?php echo $form_data['scheduled_date'];?>" name="scheduled_date" readonly="readonly" style="height: 30px;" />
                                    <span class="add-on"><i class="icon-remove"></i></span>
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                                <?php if (isset($notification_error['scheduled_date'])) {?>
                                <span class="error"><?php echo $notification_error['scheduled_date'];?></span>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td>Time Zone:</td>
                            <td>
                                <?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
                                <select name="time_zone">
                                    <?php foreach ($tzlist as $tz_value) {?>
                                        <option value="<?php echo $tz_value;?>" <?php echo ($form_data['time_zone'] == $tz_value) ? 'selected="selected"' : ''; ?>><?php echo $tz_value; ?></option>
                                    <?php } ?>
                                </select>
                                <?php if (isset($notification_error['time_zone'])) {?>
                                <br>
                                <span class="error"><?php echo $notification_error['time_zone'];?></span>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td>
                                <?php
                                $now = strtotime(date(CN_DATE_TIME_FORMAT));
                                $cron_time = strtotime($form_data['cron_time']);

                                if ($form_data['cron_time'] && (($form_data['status'] == CN_SEND_STATUS) || ($now > $cron_time && $form_data['status'] != CN_DISABLED_STATUS))) {
                                    echo 'Send';
                                ?>
                                    <input type="hidden" name="status" value="<?php echo $form_data['status'];?>"  />
                                <?php
                                } else {?>
                                <select name="status">
                                    <option value="<?php echo CN_ACTIVE_STATUS;?>" <?php echo ($form_data['status'] == CN_ACTIVE_STATUS) ? 'selected="selected"' : ''; ?>>Active</option>
                                    <option value="<?php echo CN_DISABLED_STATUS;?>" <?php echo ($form_data['status'] == CN_DISABLED_STATUS) ? 'selected="selected"' : ''; ?>>Disabled</option>
                                </select>
                                <?php } ?>
                                <input value="<?php echo $form_data['cron_time'];?>" name="cron_time" type="hidden" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" class="button button-primary" id="Submit" value="Submit">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
</div>