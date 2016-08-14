<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
function callback_init()
{
    //cron setting
    option::add('quericy_sign_mail_run_id', 0);
    option::add('quericy_sign_mail_last_date', 0);
    option::add('quericy_sign_mail_log', '无日志');
    //admin setting
    option::add('quericy_sign_mail_default_open', 1);
    option::add('quericy_sign_mail_name', '');
    option::add('quericy_sign_mail_host', '');
    option::add('quericy_sign_mail_port', '');
    option::add('quericy_sign_mail_secure', 'tls');
    option::add('quericy_sign_mail_user_name', '');
    option::add('quericy_sign_mail_user_password', '');
    option::add('quericy_sign_mail_title', '【[date]】[name]的签到报告');
    option::add('quericy_sign_mail_content', '您于[date]云签到结果可通过点击这里查看（次日自动失效）：<a href="[link]">[link]</a><br/>若有大量失败记录,请到<a href="[SYSTEM_URL]">云签到站点</a>重新设置你的Cookie</p>');
    //cron_tab setting
    cron::set('quericy_sign_mail', 'plugins/quericy_sign_mail/cron_quericy_sign_mail.php', 0, '自定义签到报告邮件发送定时任务', 0);
}

function callback_inactive()
{
    //cron_tab setting
    cron::del('quericy_sign_mail');

}

function callback_remove()
{
    //cron setting
    option::del('quericy_sign_mail_run_id');
    option::del('quericy_sign_mail_last_date');
    option::del('quericy_sign_mail_log');
    //admin setting
    option::del('quericy_sign_mail_default_open');
    option::del('quericy_sign_mail_name');
    option::del('quericy_sign_mail_host');
    option::del('quericy_sign_mail_port');
    option::del('quericy_sign_mail_secure');
    option::del('quericy_sign_mail_user_name');
    option::del('quericy_sign_mail_user_password');
    option::del('quericy_sign_mail_title');
    option::del('quericy_sign_mail_content');
    //user setting
    global $m;
    $m->query("DELETE FROM " . DB_PREFIX . "users_options WHERE NAME='quericy_sign_mail_enable'");
}

?>