<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

class quericy_notice_mail
{
    private $conf_arr = array();
    private $KMMailer_obj = null;

    /**
     * Get system config
     * @return bool
     */
    public function get_config()
    {
        if (!empty($this->conf_arr)) {
            return true;
        }
        $conf_arr['mail_name'] = option::get('quericy_sign_mail_name');
        $conf_arr['mail_host'] = option::get('quericy_sign_mail_host');
        $conf_arr['mail_port'] = option::get('quericy_sign_mail_port');
        $conf_arr['mail_secure'] = option::get('quericy_sign_mail_secure');
        $conf_arr['mail_user_name'] = option::get('quericy_sign_mail_user_name');
        $conf_arr['mail_user_password'] = option::get('quericy_sign_mail_user_password');
        $conf_arr['mail_title'] = option::get('quericy_sign_mail_title');
        $conf_arr['mail_content'] = option::get('quericy_sign_mail_content');
        //check config
        if (empty($conf_arr['mail_name']) || empty($conf_arr['mail_host']) || empty($conf_arr['mail_port'])) {
            return false;
        }
        if (empty($conf_arr['mail_title']) || empty($conf_arr['mail_content'])) {
            return false;
        }
        //deal config
        $conf_arr['mail_secure'] = empty($conf_arr['mail_secure']) ? 'none' : $conf_arr['mail_secure'];
        $conf_arr['mail_user_name'] = empty($conf_arr['mail_user_name']) ? null : $conf_arr['mail_user_name'];
        $conf_arr['mail_user_password'] = empty($conf_arr['mail_user_password']) ? null : $conf_arr['mail_user_password'];
        $conf_arr['mail_title'] = $this->deal_public_template($conf_arr['mail_title']);
        $conf_arr['mail_content'] = $this->deal_public_template($conf_arr['mail_content']);
        $this->conf_arr = $conf_arr;
        return true;
    }

    /**
     * Connect to SMTP server
     * @return bool
     */
    public function connect_notice_server()
    {
        if (empty($this->conf_arr)) {
            return false;
        }
        if (!empty($this->KMMailer_obj)) {
            return true;
        }
        require 'KMMailer.php';
        $KMMailer_obj = new KMMailer($this->conf_arr['mail_host'], $this->conf_arr['mail_port'], $this->conf_arr['mail_user_name'], $this->conf_arr['mail_user_password'], $this->conf_arr['mail_secure']);
        $KMMailer_obj->charset = "\"UTF-8\"";
        $KMMailer_obj->contentType = "multipart/mixed";
        $KMMailer_obj->transferEncodeing = "quoted-printable";
        $this->KMMailer_obj = $KMMailer_obj;
        return true;
    }

    /**
     * Send sign notice mail
     * @param string $mail_to email
     * @param string $mail_title email title
     * @param string $mail_content email content
     * @return bool
     */
    public function send_notice_mail($mail_to, $mail_title, $mail_content)
    {
        if (empty($this->conf_arr)) {
            return false;
        }
        if (empty($this->KMMailer_obj)) {
            return false;
        }
        return $this->KMMailer_obj->send($this->conf_arr['mail_name'], $mail_to, $mail_title, $mail_content, null, "+0800");
    }

    /**
     * Set the link of the sign notice page
     * @param string $user_name user name
     * @param int $user_id user id
     * @param string|null $date
     * @return string
     */
    public function get_notice_url($user_name, $user_id, $date = null)
    {
        $date = empty($date) ? date('Y-m-d') : $date;
        return SYSTEM_URL . 'index.php?pub_plugin=quericy_sign_mail&username=' . $user_name . '&token=' . md5(md5($user_name . $user_id . $date) . md5($user_id));
    }

    /**
     * Get the title of the mail template
     * @return bool|mixed
     */
    public function get_template_title()
    {
        if (empty($this->conf_arr)) {
            return false;
        }
        return $this->conf_arr['mail_title'];
    }

    /**
     * Get the content of the mail template
     * @return bool|mixed
     */
    public function get_template_content()
    {
        if (empty($this->conf_arr)) {
            return false;
        }
        return $this->conf_arr['mail_content'];
    }

    /**
     * Deal the template,replace the user variable
     * @param string $template_str
     * @param string $user_name
     * @param string $notice_url link of the sign notice page
     * @return mixed
     */
    public function deal_user_template($template_str, $user_name, $notice_url)
    {
        $user_pattern_arr = array(
            '/\[link\]/',
            '/\[name\]/',
        );
        $user_replacement_arr = array(
            $notice_url,
            $user_name,
        );
        return preg_replace($user_pattern_arr, $user_replacement_arr, $template_str);
    }

    /**
     *  Deal the template,replace the public variable
     * @param string $template_str
     * @return mixed
     */
    private function deal_public_template($template_str)
    {
        $public_pattern_arr = array(
            '/\[date\]/',
            '/\[SYSTEM_URL\]/',
            '/\[SYSTEM_NAME\]/',
        );
        $public_replacement_arr = array(
            date('Y-m-d'),
            SYSTEM_URL,
            SYSTEM_NAME,
        );
        return preg_replace($public_pattern_arr, $public_replacement_arr, $template_str);
    }
}
