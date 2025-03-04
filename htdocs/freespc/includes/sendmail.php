<?php
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(dirname(__FILE__)).'/');
}

//config sendmail.ini
function configMailIni($smpt, $port, $emailUname, $emailPsw, $fullEmail = '') {
    $pos = strpos(ABSPATH,'htdocs');
    $root = substr(ABSPATH,0,$pos);
    
    // 使用正确的路径分隔符（适用于 Docker 环境）
    $sendmailDir = $root."sendmail/";
    $sendmailFile = $sendmailDir.'sendmail.ini';
    
    // 首先检查示例文件是否存在
    if (!file_exists($sendmailDir.'sendmail_example.ini')) {
        // 如果不存在，创建一个默认的示例文件
        $default_config = "[sendmail]\nsmtp_server=localhost\nsmtp_port=25\ndefault_domain=localhost\n";
        $default_config .= "auth_username=\nauth_password=\n";
        
        // 确保目录存在
        if (!is_dir($sendmailDir)) {
            mkdir($sendmailDir, 0755, true);
        }
        
        // 写入默认配置
        file_put_contents($sendmailDir.'sendmail_example.ini', $default_config);
    }
    
    // 现在尝试读取示例文件
    $sendmailSampleFile = @file($sendmailDir.'sendmail_example.ini');
    
    // 如果读取失败，尝试使用 Docker 容器内的路径
    if (!$sendmailSampleFile) {
        $containerSendmailDir = ABSPATH . 'includes/sendmail/';
        
        // 确保目录存在
        if (!is_dir($containerSendmailDir)) {
            mkdir($containerSendmailDir, 0755, true);
        }
        
        $containerSendmailFile = $containerSendmailDir . 'sendmail_example.ini';
        
        // 如果容器内也没有示例文件，创建一个
        if (!file_exists($containerSendmailFile)) {
            $default_config = "[sendmail]\nsmtp_server=localhost\nsmtp_port=25\ndefault_domain=localhost\n";
            $default_config .= "auth_username=\nauth_password=\n";
            file_put_contents($containerSendmailFile, $default_config);
        }
        
        $sendmailSampleFile = file($containerSendmailFile);
        $sendmailFile = $containerSendmailDir . 'sendmail.ini';
    }
    $handle = fopen($sendmailFile, 'w');
    
    // 添加 SSL 配置
    fwrite($handle, "[sendmail]\n");
    fwrite($handle, "smtp_server=$smpt\n");
    fwrite($handle, "smtp_port=$port\n");
    fwrite($handle, "auth_username=$emailUname\n");
    fwrite($handle, "auth_password=$emailPsw\n");
    fwrite($handle, "force_sender=$emailUname\n");
    
    // 针对不同端口添加不同的 SSL 配置
    if ($port == '465') {
        fwrite($handle, "ssl=ssl\n");  // 明确指定 SSL
    } elseif ($port == '587') {
        fwrite($handle, "tls=on\n");   // 使用 TLS
    } else {
        fwrite($handle, "ssl=auto\n"); // 自动检测
    }
    
    // 添加更多调试选项
    fwrite($handle, "debug=1\n");      // 启用调试
    fwrite($handle, "auth_method=LOGIN\n"); // 指定认证方法
    
    fclose($handle);
    chmod($sendmailFile, 0666);
    
    // 创建 msmtp 配置文件
    try {
        // 如果没有提供完整邮箱，尝试构建
        if (empty($fullEmail)) {
            $emailDomain = substr(strstr($smpt, '.'), 1);
            $fullEmail = $emailUname . '@' . $emailDomain;
        }
        
        // 初始化 msmtp 配置
        $msmtpConfig = "# msmtp 配置文件\n";
        $msmtpConfig .= "defaults\n";
        $msmtpConfig .= "auth on\n";
        
        // 根据端口配置 TLS/SSL
        if ($port == '465') {
            $msmtpConfig .= "tls on\n";
            $msmtpConfig .= "tls_starttls off\n";
        } elseif ($port == '587') {
            $msmtpConfig .= "tls on\n";
            $msmtpConfig .= "tls_starttls on\n";
        }
        
        $msmtpConfig .= "tls_trust_file /etc/ssl/certs/ca-certificates.crt\n";
        $msmtpConfig .= "logfile /var/log/msmtp.log\n\n";
        $msmtpConfig .= "account default\n";
        $msmtpConfig .= "host $smpt\n";
        $msmtpConfig .= "port $port\n";
        $msmtpConfig .= "from $fullEmail\n";
        $msmtpConfig .= "user $fullEmail\n";
        $msmtpConfig .= "password $emailPsw\n";
        
        // 创建 msmtp 配置文件
        $msmtpFile = '/var/www/html/freespc/includes/msmtprc';
        
        // 确保目录存在
        $dir = dirname($msmtpFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // 写入配置文件
        if (file_put_contents($msmtpFile, $msmtpConfig) === false) {
            error_log("无法写入 msmtp 配置文件: $msmtpFile");
            throw new Exception("无法写入 msmtp 配置文件");
        }
        
        // 设置权限
        if (!chmod($msmtpFile, 0600)) {
            error_log("无法设置 msmtp 配置文件权限: $msmtpFile");
            throw new Exception("无法设置配置文件权限");
        }
        
        error_log("成功创建 msmtp 配置文件: $msmtpFile");
        
    } catch (Exception $e) {
        error_log("创建 msmtp 配置文件失败: " . $e->getMessage());
    }
    
    return $sendmailFile;
}

//send mail
function sendmail($to, $subject, $body, $from){    
    // 添加调试日志
    error_log("尝试发送邮件给: $to, 发件人: $from");
    
    // 使用 msmtp 配置文件的路径
    $msmtprc = '/var/www/html/freespc/includes/msmtprc';
    
    if(empty($from)){
        require_once(ABSPATH.'load.php');        
        $fromName = "SPC Team";
        $headers = "From: $fromName <".EMAIL.">";
        $headers = $headers;
        
        // 修改编码方式，避免中文乱码
        $to = mb_convert_encoding($to, 'UTF-8', 'UTF-8');
        $subject = mb_convert_encoding($subject, 'UTF-8', 'UTF-8');
        $body = mb_convert_encoding($body, 'UTF-8', 'UTF-8');
    }else{    
        $fromName = "FreeSPC Administrator";    
        $headers = "From: $fromName <".$from.">";        
    }
    
    // 添加额外的邮件头，提高送达率
    $headers .= "\r\nMIME-Version: 1.0";
    $headers .= "\r\nContent-Type: text/plain; charset=UTF-8";
    
    // 记录发送前的信息
    error_log("准备发送邮件: 收件人=$to, 主题=$subject, 头信息=$headers");
    
    // 直接使用 msmtp 命令发送邮件，而不是依赖 PHP mail() 函数
    $tmpfile = tempnam('/tmp', 'mail');
    file_put_contents($tmpfile, "To: $to\nSubject: $subject\n$headers\n\n$body");
    
    $cmd = "/usr/bin/msmtp --file=$msmtprc -t < $tmpfile";
    exec($cmd, $output, $return_var);
    unlink($tmpfile);
    
    if($return_var === 0){
        error_log("邮件发送成功");
        return true;
    }else{
        error_log("邮件发送失败: " . implode("\n", $output));
        return false;
    }
}

//测试邮件功能
function testMail($email){
    // 添加调试信息
    error_log("开始测试邮件功能，发送到: $email");
    
    $to = $email;
    $subject = "测试您的邮件服务器设置";
    $body = "您的邮件服务器设置正确并通过测试！\n    收到此邮件说明设置正确，请返回页面并点击完成按钮。";
    
    // 尝试直接使用 PHP mail 函数发送
    $result = sendmail($to, $subject, $body, $email);
    
    if (!$result) {
        // 如果失败，记录详细错误信息
        $error = error_get_last();
        error_log("邮件测试失败: " . ($error ? $error['message'] : '未知错误'));
    }
    
    return $result;
}
?>