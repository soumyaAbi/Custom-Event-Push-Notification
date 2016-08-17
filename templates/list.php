        <h3>Custom Push Notifications</h3>
        <form action="" method="post">
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Message</th>
                            <th>Scheduled Date</th>
                            <th>Date Added</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($notifications_list)) {
                        foreach ($notifications_list as $notification_data) {?>
                        <tr>
                            <td><?php echo $notification_data['post_title'];?></td>
                            <td><?php echo substr ( strip_tags ( $notification_data['message']), 0, 70 ).' ...';?></td>
                            <td><?php echo $notification_data['scheduled_date'].' ('.$notification_data['time_zone'].')';?></td>
                            <td><?php echo $notification_data['date_created'];?></td>
                            <td>
                             <?php
                                $now = strtotime(date(CN_DATE_TIME_FORMAT));
                                $cron_time = strtotime($notification_data['cron_time']);

                                if (($notification_data['status'] == CN_SEND_STATUS) || ($now > $cron_time && $notification_data['status'] != CN_DISABLED_STATUS)) {
                                    echo 'Send';
                                } else {
                                    if ($notification_data['status'] == CN_ACTIVE_STATUS) {
                                        echo 'Active';
                                    } else{
                                        echo 'Disabled';
                                    }
                                } ?>
                            </td>
                            <td>
                                <?php
                                $blog_id     = '';
                                $scheme      = '';
                                $path        = 'admin.php?page=push_notification_form&cn_id='.$notification_data['cn_id'];
                                $delete_path = 'admin.php?page=push_notifications&action=delete&cn_id='.$notification_data['cn_id'];

                                ?>
                                <a href="<?php echo get_admin_url( $blog_id, $path, $scheme );?>">[Edit]</a>
                                <a href="<?php echo get_admin_url( $blog_id, $delete_path, $scheme );?>">[Delete]</a>
                            </td>
                        </tr>
                        <?php }
                        } else {?>
                        <tr>
                            <td colspan="5" align="center">No records found!</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
        </form>