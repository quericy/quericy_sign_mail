<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
function cron_quericy_sign_mail()
{
    //set_time_limit(0);
    $last_run_date = option::get('quericy_sign_mail_last_date');
    if ($last_run_date >= date('Y-m-d')) {
        return option::get('quericy_sign_mail_log');
    }
    //get start_time
    $quericy_sign_mail_send_hour = option::get('quericy_sign_mail_send_hour');
    if (!empty($quericy_sign_mail_send_hour) && $quericy_sign_mail_send_hour > date('H') && $quericy_sign_mail_send_hour > 0 && $quericy_sign_mail_send_hour < 24) {
        //no start
        return option::get('quericy_sign_mail_log');
    }
    $log = date("Y-m-d H:i:s") . ' 签到邮件通知开始执行 ' . PHP_EOL;
    //get setting
    $last_do_id = option::get('quericy_sign_mail_run_id');
    $quericy_sign_mail_default_open = option::get('quericy_sign_mail_default_open');
    //Use lib
    require 'class.quericy_notice_mail.php';
    $notice_mail_obj = new quericy_notice_mail();
    if (!$notice_mail_obj->get_config()) {
        $log .= date("Y-m-d H:i:s") . ' 签到邮件服务器参数必须配置完整,执行已终止! ' . PHP_EOL;
        option::set('quericy_sign_mail_log', $log);
        return $log;
    }
    //SMTP server
    if (!$notice_mail_obj->connect_notice_server()) {
        $log .= date("Y-m-d H:i:s") . ' 签到邮件服务器连接失败! ' . PHP_EOL;
        option::set('quericy_sign_mail_log', $log);
        return $log;
    }
    $log .= date("Y-m-d H:i:s") . ' 签到邮件服务器连接成功 ' . PHP_EOL;
    //find max user
    global $m;
    $max = $m->fetch_array($m->query("select max(`id`) as id from `" . DB_NAME . "`.`" . DB_PREFIX . "users`"));
    $max_id = $max['id'];
    //continue unfinished task
    $last_do_id = $last_do_id ? $last_do_id + 1 : 0;
    //counter and log
    $send_mail_count = 0;
    $send_success_count = 0;
    //run for every user
    for ($ii = $last_do_id; $ii <= $max_id; $ii++) {
        $user = $m->fetch_array($m->query('select `id`,`email`,`name` from `' . DB_NAME . '`.`' . DB_PREFIX . 'users` where `id` = ' . $ii));
        if (empty($user) || empty($user['email']) || empty($user['id'])) {
            continue;
        }

        //find user setting
        $op = $m->fetch_array($m->query('select `value` from `' . DB_NAME . '`.`' . DB_PREFIX . 'users_options` where `uid` = ' . $user['id'] . ' and `name` = "quericy_sign_mail_enable"'));

        if ($quericy_sign_mail_default_open == 1) {
            $is_open = $op['value'] == 'off' ? false : true;
        } else {
            $is_open = $op['value'] == 'on' ? true : false;
        }
        if ($is_open == false) {
            //global and user setting comprehensive check,no send
            option::set('quericy_sign_mail_run_id', $ii);
            continue;
        }
        //deal user template
        $notice_url = $notice_mail_obj->get_notice_url($user['name'], $user['id']);
        $mail_title = $notice_mail_obj->deal_user_template($notice_mail_obj->get_template_title(), $user['name'], $notice_url);
        $mail_content = $notice_mail_obj->deal_user_template($notice_mail_obj->get_template_content(), $user['name'], $notice_url);
        //send mail
        $res = $notice_mail_obj->send_notice_mail($user['email'], $mail_title, $mail_content);
        //check result
        option::set('quericy_sign_mail_run_id', $ii);
        $send_mail_count++;
        $res === true ? $send_success_count++ : 0;
        $log .= date("Y-m-d H:i:s") . ' 至' . $user['email'] . '邮件发送结果:' . ($res === true ? '成功' : '失败') . ' ' . PHP_EOL;

    }

    //complete,reset counter
    option::set('quericy_sign_mail_run_id', 0);

    option::set('quericy_sign_mail_last_date', date('Y-m-d'));
    $log .= date("Y-m-d H:i:s") . " 签到邮件通知执行结束,共计发送邮件:" . $send_mail_count . "封,发送成功" . $send_success_count . "封 " . PHP_EOL;
    option::set('quericy_sign_mail_log', $log);
    return $log;
}
