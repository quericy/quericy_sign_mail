<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
if (ROLE !== 'admin') {
    msg('权限不足!');
    die;
}
switch ($_GET['act']) {
    case 'ok'://成功回显
        echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>插件设置成功</div>';
        break;
    case 'debug'://测试邮件发送回显
        ob_end_clean();
        if ($_POST['is_send'] != 'ok') {
            $return_arr = array('code' => 0, 'msg' => '无效请求!');
        } else {
            global $i;
            $send_mail = $i['user']['email'];
            $user_name = $i['user']['name'];
            $user_id = $i['user']['uid'];
            require 'quericy_notice_mail.class.php';
            $notice_mail_obj = new quericy_notice_mail();
            if (!$notice_mail_obj->get_config()) {
                $return_arr = array('code' => 0, 'msg' => '发送失败,请完成设置并保存后再测试!');
            } elseif (!$notice_mail_obj->connect_notice_server()) {
                $return_arr = array('code' => 0, 'msg' => '发送失败,连接服务器失败!');
            } else {
                $notice_url = $notice_mail_obj->get_notice_url($user_name, $user_id);
                $mail_title = $notice_mail_obj->deal_user_template($notice_mail_obj->get_template_title(), $user_name, $notice_url);
                $mail_content = $notice_mail_obj->deal_user_template($notice_mail_obj->get_template_content(), $user_name, $notice_url);
                $res = $notice_mail_obj->send_notice_mail($send_mail, $mail_title, $mail_content);
                if ($res) {
                    $return_arr = array('code' => 1, 'msg' => '一封测试邮件已经发送到' . $send_mail . ',请注意查收!');
                } else {
                    $return_arr = array('code' => 0, 'msg' => '发送给' . $send_mail . '的测试邮件发送失败!');
                }
            }
        }
        if ($return_arr['code'] == 1) {
            $msg_html = '<div class="text-success"><span class="glyphicon glyphicon-ok-sign"></span>';
        } else {
            $msg_html = '<div class="text-danger"><span class="glyphicon glyphicon-remove-sign"></span>';
        }
        $msg_html .= '&nbsp;' . $return_arr['msg'] . '</div>';

        echo json_encode(array('code' => $return_arr['code'], 'msg' => $msg_html));
        die;
        break;
    case 'store'://保存设置
        option::set('quericy_sign_mail_default_open', $_POST['quericy_sign_mail_default_open']);
        option::set('quericy_sign_mail_send_hour', intval($_POST['quericy_sign_mail_send_hour']));
        option::set('quericy_sign_mail_name', $_POST['quericy_sign_mail_name']);
        option::set('quericy_sign_mail_host', $_POST['quericy_sign_mail_host']);
        option::set('quericy_sign_mail_port', $_POST['quericy_sign_mail_port']);
        option::set('quericy_sign_mail_secure', $_POST['quericy_sign_mail_secure']);
        option::set('quericy_sign_mail_user_name', $_POST['quericy_sign_mail_user_name']);
        //密码非重置处理
        if ($_POST['quericy_sign_mail_user_password'] == '*********') {
            unset($_POST['quericy_sign_mail_user_password']);
        } else {
            option::set('quericy_sign_mail_user_password', $_POST['quericy_sign_mail_user_password']);
        }
        option::set('quericy_sign_mail_title', htmlspecialchars_decode($_POST['quericy_sign_mail_title']));
        option::set('quericy_sign_mail_content', htmlspecialchars_decode($_POST['quericy_sign_mail_content']));
        ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=quericy_sign_mail&act=ok');
        die;
    default:
        break;
}


?>
<form action="index.php?mod=admin:setplug&plug=quericy_sign_mail&act=store" method="post">
    <div class="container">
        <h3 class="align:center;">自定义签到邮件通知设置</h3>
    </div>

    <table class="table table-condensed table-hover">
        <thead>
        <tr>
            <th class="col-md-2">功能模块</th>
            <th>设置模块</th>
        </tr>
        </thead>
        <tr>
            <td>插件设置</td>
            <td>
                <div class="input-group">
                    <input type="checkbox"
                           name="quericy_sign_mail_default_open"
                           value="1" <?php echo option::get('quericy_sign_mail_default_open') ? 'checked' : ''; ?>>
                    所有用户默认开启邮件通知
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">每天</span>
                    <input class="form-control" type="number" placeholder="0点~23点,超过范围无效"
                           name="quericy_sign_mail_send_hour"
                           value="<?php echo option::get('quericy_sign_mail_send_hour') ?>">
                    <span class="input-group-addon">点发送邮件</span>
                </div>
                <br>
            </td>
        </tr>
        <tr>
            <td>邮件服务</td>
            <td>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">邮件发送模式</span>
                    <input class="form-control" type="text" disabled value="SMTP">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">发件人邮箱</span>
                    <input class="form-control" type="email" placeholder="发件人邮箱地址"
                           name="quericy_sign_mail_name"
                           value="<?php echo option::get('quericy_sign_mail_name') ?>">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">SMTP服务器地址</span>
                    <input class="form-control" type="text" placeholder="SMTP服务器ip或域名"
                           name="quericy_sign_mail_host"
                           value="<?php echo option::get('quericy_sign_mail_host') ?>">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">SMTP服务器端口</span>
                    <input class="form-control" type="number" placeholder="一般为25，465，587"
                           name="quericy_sign_mail_port"
                           value="<?php echo option::get('quericy_sign_mail_port') ?>">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">加密方式</span>
                    <select name="quericy_sign_mail_secure" class="form-control">
                        <option value="none"
                            <?php echo (empty(option::get('quericy_sign_mail_secure')) || option::get('quericy_sign_mail_secure') == 'none') ? 'selected' : ''; ?>>
                            无
                        </option>
                        <option value="tls"
                            <?php echo option::get('quericy_sign_mail_secure') == 'tls' ? 'selected' : ''; ?>>TLS
                        </option>
                        <option value="ssl"
                            <?php echo option::get('quericy_sign_mail_secure') == 'ssl' ? 'selected' : ''; ?>>SSL
                        </option>
                    </select>
                </div>
                <br>
                <div id="smtp_auth">
                    <div class="input-group">
                        <span class="input-group-addon">SMTP用户名</span>
                        <input class="form-control" type="text"
                               name="quericy_sign_mail_user_name"
                               value="<?php echo option::get('quericy_sign_mail_user_name') ?>">
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">SMTP密码</span>
                        <input class="form-control" type="password"
                               name="quericy_sign_mail_user_password"
                               value="<?php echo option::get('quericy_sign_mail_user_password') !== '' ? '*********' : ''; ?>">
                    </div>
                    <br>
                </div>

            </td>
        </tr>
        <tr>
            <td>模板设置</td>
            <td>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">模板变量</span>
                    <input class="form-control" type="text" readonly
                           value="[link]签到报告url,[date]当前日期,[name]用户名,[SYSTEM_URL]站点url,[SYSTEM_NAME]站点名称">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">邮件标题</span>
                    <input class="form-control" type="text"
                           name="quericy_sign_mail_title"
                           value="<?php echo htmlspecialchars(option::get('quericy_sign_mail_title')); ?>">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">邮件正文</span>
                        <textarea class="form-control"
                                  name="quericy_sign_mail_content"
                                  style="height:150px;"><?php echo htmlspecialchars(option::get('quericy_sign_mail_content')) ?></textarea>
                </div>
                <br>
            </td>
        </tr>
        <tr>
            <td>调试测试</td>
            <td>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">测试邮箱</span>
                    <input class="form-control" type="text" disabled value="<?php global $i;
                    echo $i['user']['email']; ?>">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">友情提示</span>
                    <input class="form-control" type="text" readonly
                           value="测试邮件只能发给自己,请先【保存设置】后再点击测试按钮!">
                </div>
                <br>
                <a id="debug_btn" href="javascript:void(0)" class="btn btn-warning">
                    点击测试
                </a>
                <span id="debug_tip"></span>
                <br>
            </td>
        </tr>
    </table>

    <button type="submit" class="btn btn-info">保存设置</button>
</form>
<script type="text/javascript">
    var click_status = 0;
    $(document).delegate('#debug_btn', 'click', function () {
        if (click_status > 0)return;
        click_status = 1;
        $('#debug_btn').attr('disabled', true);
        $('#debug_btn').text('正在发送');
        $.ajax({
            url: 'index.php?mod=admin:setplug&plug=quericy_sign_mail&act=debug',
            type: 'POST',
            dataType: 'json',
            data: {
                is_send: 'ok'
            },
            success: function (result) {
                switch (result.code) {
                    case 1:
                        alert(result.msg);
                        break;
                    case 0:
                        alert(result.msg);
                        break;
                    default:
                        alert('请求异常!请刷新页面后重试');
                        break;
                }
                $('#debug_btn').attr('disabled', false);
                $('#debug_btn').text('点击测试');
                click_status = 0;
            },
            error: function () {
                alert('网络异常!请刷新页面后重试');
                $('#debug_btn').attr('disabled', false);
                $('#debug_btn').text('点击测试');
                click_status = 0;
            }
        });
    });
</script>