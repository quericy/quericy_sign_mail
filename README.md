# quericy_sign_mail - 自定义每日签到邮件通知扩展
云签到插件，用于无名智者的[百度贴吧云签到](https://github.com/MoeNetwork/Tieba-Cloud-Sign)平台。每日用户签到邮件通知，使用独立的SMTP服务器配置，支持tls、ssl加密，可自定义邮件标题和内容模板，基于D丶L的版本重写。

最新更新说明及安装方法
===
请访问本项目的release页面查看：[https://github.com/quericy/quericy_sign_mail/releases/](https://github.com/quericy/quericy_sign_mail/releases/)

功能概要
===
+ 使用独立的SMTP邮件服务器配置，从站内邮件系统配置中分离出来，互不影响，支持TLS和SSL加密方式的邮件服务器，使代发邮件拥有更多选择。

+ 邮件标题和正文模板化，可自己配置和美化所需格式，调整邮件发送内容防止进入垃圾箱。模板提供几个常用变量可供使用。

+ 添加定时任务日志输出，便于查看邮件发送成功数量统计。

+ 可全局设置每日定时发送邮件，配合每日签到时间设置，效果更佳。

+ 可设置默认开启/关闭邮件通知，当用户没有设置过邮件提醒开关时，按照管理员设置的默认开关执行。

+ 用户设置页面也可查看签到报告。

+ 美化签到报告链接页面，优化sql查询。

+ 解决发送中断后，原脚本重复发送最后一封邮件的问题。

+ 邮件混合附带HTML格式和纯文本格式，提升邮件评分，且在无法显示html格式邮件的旧客户端上也能正常显示邮件。

+ 添加自定义发件人昵称和自定义邮件发送服务器所在时区的功能。

截图说明
===

## 插件设置

### 插件管理->自定义签到邮件通知->插件设置：（需要管理员权限）
![如图所示](https://i.imgur.com/duyzD5y.png)

### 进入设置页面后可配置详细的插件功能：
![如图所示](https://i.imgur.com/yhiK0xk.png)

## 插件日志

### 计划任务->quericy_sign_mail->查看日志(或点击编辑查看日志)
![如图所示](https://i.imgur.com/kRxWsBS.png)

## 用户页面

### 每个用户的个人设置页面：
![如图所示](https://i.imgur.com/CksC6gh.png)

## 签到报告

### 发送给用户的邮件报告：
![如图所示](https://i.imgur.com/AzpaLSd.png)

### 点击跳转的详细报告：
![如图所示](https://i.imgur.com/1Dsuslq.png)

### 默认报告模板在[mail-tester](http://www.mail-tester.com)的邮件测试评分(邮件服务器正确配置DKIM等校验能提升评分,坏链不知道为啥将正常链接识别成404)：
![如图所示](https://i.imgur.com/2mvaFie.png)

问题反馈
===

欢迎在Github提交Issues和PR~


致谢
===

开源库:

[kmmailer](https://github.com/innomatic-libs/kmmailer)

许可
===
[GPL V3](https://github.com/quericy/quericy_sign_mail/blob/master/LICENSE)
