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
    option::add('quericy_sign_mail_send_hour', 1);
    option::add('quericy_sign_mail_name', '');
    option::add('quericy_sign_mail_host', '');
    option::add('quericy_sign_mail_port', '');
    option::add('quericy_sign_mail_secure', 'tls');
    option::add('quericy_sign_mail_user_name', '');
    option::add('quericy_sign_mail_user_password', '');
    option::add('quericy_sign_mail_title', '【[date]】[name] 的签到报告');
    option::add('quericy_sign_mail_content', '<html><head><title>贴吧云签到报告</title><style type="text/css">div.wrapper * { font: 12px "Microsoft YaHei", arial, helvetica, sans-serif; word-break: break-all; }div.wrapper a { color: #15c; text-decoration: none; }div.wrapper a:active { color: #d14836; }div.wrapper a:hover { text-decoration: underline; }div.wrapper p { line-height: 20px; margin: 0 0 .5em; text-align: center; }div.wrapper .sign_title { font-size: 20px; line-height: 24px; }div.wrapper .result_table { width: 85%; margin: 0 auto; border-spacing: 0; border-collapse: collapse; }div.wrapper .result_table td { padding: 10px 5px; text-align: center; border: 1px solid #dedede; }div.wrapper .result_table tr { background: #d5d5d5; }div.wrapper .result_table tbody tr { background: #efefef; }div.wrapper .result_table tbody tr:nth-child(odd) { background: #fafafa; }</style></head><body><h4 style="text-align:center;">贴吧云签到报告</h4><div class="wrapper"><table class="result_table"><thead><tr><td style="width: 60px">项目</td><td style="width: 150px">内容</td><td style="width: 75px">备注</td></tr></thead><tbody><tr><td>签到日期</td><td>[date]</td><td>当日报告已生成</td></tr><tr><td>用户名称</td><td><a href="http://tieba.baidu.com" target="_blank">[name]</a></td><td>您是该站云签用户</td></tr><tr><td>报告地址</td><td><a href="[link]" target="_blank">[link]</a></td><td>点击链接直达,次日失效</td></tr><tr><td>云签站点</td><td><a href="[SYSTEM_URL]" target="_blank">[SYSTEM_NAME]</a></td><td>如有疑问,进站反馈</td></tr></tbody></table><br><p style="font-size: 12px; color: #9f9f9f; text-align: right; border-top: 1px solid #dedede; padding: 20px 10px 0; margin-top: 25px;">发自[SYSTEM_NAME]<br>百度贴吧云签到作者:<a href="http://kenvix.com/"> Kenvix</a>&<a href="http://www.longtings.com/">mokeyjay</a>&<a href="http://fyy.l19l.com/">FYY</a><br>邮件扩展作者:<a href="https://quericy.me">quericy</a></p>
</div></body></html>');
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
    option::del('quericy_sign_mail_send_hour');
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