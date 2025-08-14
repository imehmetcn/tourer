<?php
declare(strict_types=1);

function send_system_mail(string $to, string $subject, string $htmlBody): bool {
    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    $from = 'no-reply@localhost';
    if (is_file($cfgPath)) {
        $cfg = json_decode(file_get_contents($cfgPath), true) ?: [];
        if (!empty($cfg['email_from'])) { $from = $cfg['email_from']; }
    }
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: '.$from;
    $ok = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    // Log regardless
    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
    // mask email in logs
    $toMasked = preg_replace('/(^.).+(@.+$)/', '$1***$2', $to);
    $line = date('c').' MAIL to='.$toMasked.' subj='.preg_replace('/\s+/', ' ', $subject).' result='.($ok?'OK':'FAIL')."\n";
    @file_put_contents($logDir.DIRECTORY_SEPARATOR.'mail.log', $line, FILE_APPEND);
    return $ok;
}

/**
 * Render a simple HTML template from storage/templates/$name using {{var}} placeholders
 */
function render_mail_template(string $name, array $vars): string {
    $tplDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates';
    $file = $tplDir . DIRECTORY_SEPARATOR . $name;
    if (!is_file($file)) {
        return '';
    }
    $html = file_get_contents($file) ?: '';
    $replacements = [];
    foreach ($vars as $k => $v) { $replacements['{{'.$k.'}}'] = (string)$v; }
    return strtr($html, $replacements);
}

function send_system_mail_template(string $to, string $subject, string $templateName, array $vars): bool {
    $html = render_mail_template($templateName, $vars);
    if ($html === '') {
        // fallback to simple list
        $list = '';
        foreach ($vars as $k=>$v){ $list .= '<p><b>'.htmlspecialchars((string)$k,ENT_QUOTES).':</b> '.htmlspecialchars((string)$v,ENT_QUOTES).'</p>'; }
        $html = $list;
    }
    return send_system_mail($to, $subject, $html);
}


