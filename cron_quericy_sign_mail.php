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
    $quericy_sign_mail_name = option::get('quericy_sign_mail_name');
    $quericy_sign_mail_host = option::get('quericy_sign_mail_host');
    $quericy_sign_mail_port = option::get('quericy_sign_mail_port');
    $quericy_sign_mail_secure = option::get('quericy_sign_mail_secure');
    $quericy_sign_mail_user_name = option::get('quericy_sign_mail_user_name');
    $quericy_sign_mail_user_password = option::get('quericy_sign_mail_user_password');
    $quericy_sign_mail_title = option::get('quericy_sign_mail_title');
    $quericy_sign_mail_content = option::get('quericy_sign_mail_content');
    if (empty($quericy_sign_mail_name) || empty($quericy_sign_mail_host) || empty($quericy_sign_mail_port) || empty($quericy_sign_mail_title) || empty($quericy_sign_mail_content)) {
        $log .= date("Y-m-d H:i:s") . ' 签到邮件服务器参数必须配置完整,执行已终止! ' . PHP_EOL;
        option::set('quericy_sign_mail_log', $log);
        return $log;
    }
    $quericy_sign_mail_secure = empty($quericy_sign_mail_secure) ? 'none' : $quericy_sign_mail_secure;
    $quericy_sign_mail_user_name = empty($quericy_sign_mail_user_name) ? null : $quericy_sign_mail_user_name;
    $quericy_sign_mail_user_password = empty($quericy_sign_mail_user_password) ? null : $quericy_sign_mail_user_password;

    //check global template
    $global_pattern_arr = array(
        '/\[date\]/',
        '/\[SYSTEM_URL\]/',
        '/\[SYSTEM_NAME\]/',
    );
    $global_replacement_arr = array(
        date('Y-m-d'),
        SYSTEM_URL,
        SYSTEM_NAME,
    );
    $quericy_sign_mail_title = preg_replace($global_pattern_arr, $global_replacement_arr, $quericy_sign_mail_title);
    $quericy_sign_mail_content = preg_replace($global_pattern_arr, $global_replacement_arr, $quericy_sign_mail_content);
    global $m;
    //find max user
    $max = $m->fetch_array($m->query("select max(`id`) as id from `" . DB_NAME . "`.`" . DB_PREFIX . "users`"));
    $max_id = $max['id'];
    //continue unfinished task
    $last_do_id = $last_do_id ? $last_do_id + 1 : 0;
    //counter and log
    $send_mail_count = 0;
    $send_success_count = 0;

    //SMTP server
    require 'KMMailer.php';
    $KMMailer_obj = new KMMailer($quericy_sign_mail_host, $quericy_sign_mail_port, $quericy_sign_mail_user_name, $quericy_sign_mail_user_password, $quericy_sign_mail_secure);
    $KMMailer_obj->charset = "\"UTF-8\"";
    $KMMailer_obj->contentType = "multipart/mixed";
    $mail_from = $quericy_sign_mail_name;
    $log .= date("Y-m-d H:i:s") . ' 签到邮件服务器连接成功 ' . PHP_EOL;
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
            continue;
        }
        //set mail content
        $mail_to = $user['email'];
        $url = SYSTEM_URL . 'index.php?pub_plugin=quericy_sign_mail&username=' . $user['name'] . '&token=' . md5(md5($user['name'] . $user['id'] . date('Y-m-d')) . md5($user['id']));
        //check user template
        $user_pattern_arr = array(
            '/\[link\]/',
            '/\[name\]/',
        );
        $user_replacement_arr = array(
            $url,
            $user['name'],
        );
        preg_replace(array('/\r\n/', '/\n/'), array('<br>', '<br>'), $a);
        $user_sign_mail_title = preg_replace($user_pattern_arr, $user_replacement_arr, $quericy_sign_mail_title);
        $user_sign_mail_content = preg_replace($user_pattern_arr, $user_replacement_arr, $quericy_sign_mail_content);
        //send mail
        $res = $KMMailer_obj->send($mail_from, $mail_to, $user_sign_mail_title, $user_sign_mail_content,null,"+0800");
        //check result
        option::set('quericy_sign_mail_run_id', $ii);
        $send_mail_count++;
        $res === true ? $send_success_count++ : 0;
        $log .= date("Y-m-d H:i:s") . ' 至' . $user['email'] . '邮件发送结果:' . ($res === true ? '成功' : '失败') . ' ' . PHP_EOL;

    }

    //complete,reset counter
    if (option::get('quericy_sign_mail_run_id') >= $max_id) {
        option::set('quericy_sign_mail_run_id', 0);
    }
    option::set('quericy_sign_mail_last_date', date('Y-m-d'));
    $log .= date("Y-m-d H:i:s") . " 签到邮件通知执行结束,共计发送邮件:" . $send_mail_count . "封,发送成功" . $send_success_count . "封 " . PHP_EOL;
    option::set('quericy_sign_mail_log', $log);
    return $log;
}
