<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/*
Plugin Name: 自定义签到邮件通知
Version: 1.1
Plugin URL: https://github.com/quericy/quericy_sign_mail
Description: 每日用户签到邮件通知，使用独立的SMTP配置，支持tls,ssl加密，可自定义邮件标题和内容模板，基于D丶L的版本重写
Author: quericy
Author Email: cy@quericy.me
Author URL: https://quericy.me/blog/858/
For: V3.8+
*/
function quericy_sign_mail_setting()
{
    if (option::get('quericy_sign_mail_default_open') == 1) {
        $is_open = option::uget('quericy_sign_mail_enable') == 'off' ? false : true;
    } else {
        $is_open = option::uget('quericy_sign_mail_enable') == 'on' ? true : false;
    }
    global $i;
    $quericy_sign_mail_report_url = SYSTEM_URL . 'index.php?pub_plugin=quericy_sign_mail&username=' . $i['user']['name'] . '&token=' . md5(md5($i['user']['name'] . $i['user']['uid'] . date('Y-m-d')) . md5($i['user']['uid']));

    ?>
    <tr>
    <td>每日签到邮件报告</td>
    <td>
        <input type="radio" name="quericy_sign_mail_enable"
               value="on" <?php echo $is_open ? 'checked' : ''; ?> > 开启每日签到邮件报告<br/>
        <input type="radio" name="quericy_sign_mail_enable"
               value="off" <?php echo $is_open ? '' : 'checked'; ?> > 关闭每日签到邮件报告
    </td>
    </tr>
    <tr>
    <td>每日签到邮件报告地址</td>
    <td>
        <a href="<?php echo $quericy_sign_mail_report_url; ?>" target="_blank">点击查看</a>（有效期至<span style="padding: 2px 4px;color: #c7254e;background-color: #f9f2f4;border-radius: 4px;"><?php echo date('Y-m-d 23:59:59');?></span>）
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