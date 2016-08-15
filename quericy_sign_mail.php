<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/*
Plugin Name: 自定义签到邮件通知
Version: 1.0
Plugin URL: https://github.com/quericy/
Description: 每日用户签到邮件通知，使用独立的SMTP配置，支持tls,ssl加密，可自定义邮件标题和内容模板，基于D丶L的版本重写
Author: quericy
Author Email: cy@quericy.me
Author URL: https://quericy.me
For: V3.8+
*/
function quericy_sign_mail_setting()
{
    if (option::get('quericy_sign_mail_default_open') == 1) {
        $is_open = option::uget('quericy_sign_mail_enable') == 'off' ? false : true;
    } else {
        $is_open = option::uget('quericy_sign_mail_enable') == 'on' ? true : false;
    }

    ?>
    <tr>
    <td>每日签到邮件报告</td>
    <td>
        <input type="radio" name="quericy_sign_mail_enable"
               value="on" <?php echo $is_open ? 'checked' : ''; ?> > 开启每日签到邮件报告<br/>
        <input type="radio" name="quericy_sign_mail_enable"
               value="off" <?php echo $is_open ? '' : 'checked'; ?> > 关闭每日签到邮件报告
    </td>
    <?php
}

function quericy_sign_mail_set()
{
    global $PostArray;
    if (!empty($PostArray)) {
        $PostArray[] = 'quericy_sign_mail_enable';
    }
}

addAction('set_save1', 'quericy_sign_mail_set');
addAction('set_2', 'quericy_sign_mail_setting');
?>