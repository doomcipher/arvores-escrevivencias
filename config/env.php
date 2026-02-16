<?php

require_once BASE_PATH . '/vendor/autoload.php';
// Só define se ainda não foi definido
if (!defined('CLOUDINARY_CLOUD_NAME')) {
    define('CLOUDINARY_CLOUD_NAME', 'df9fueyfn');
}
if (!defined('CLOUDINARY_API_KEY')) {
    define('CLOUDINARY_API_KEY', '434173552224569');
}
if (!defined('CLOUDINARY_API_SECRET')) {
    define('CLOUDINARY_API_SECRET', '7MjI0kjIMkpge_8b7pgaFI3ZFiE');
}

// Banco (puxa do docker-compose env)
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'db');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'arvores');


if (!defined('SUAP_CLIENT_ID')) {
    define('SUAP_CLIENT_ID', 'uvoz4q2TArbGoZ3ul4JBvKdOKt1XDbyKpNHGVp3i');
}
if (!defined('SUAP_CLIENT_SECRET')) {
    define('SUAP_CLIENT_SECRET', '0mURheGFAFnSqqrIMfJpyERLaArGM3jUP1ilHfXWPaz20MxMkb8Z2kuDFntBYgljjcQRHPo14ZnFR1v33fCCWNeP7paF3qh55BKVGCApQg4rTFoZqQg8hHnmxHqcScy0');
}
if (!defined('SUAP_REDIRECT_URI')) {
    define('SUAP_REDIRECT_URI', 'http://192.168.3.10:8081/callback.html');
}
if (!defined('SUAP_URL')) {
    define('SUAP_URL', 'https://suap.ifrn.edu.br');
}
if (!defined('SUAP_SCOPE')) {
    define('SUAP_SCOPE', 'identificacao email documentos_pessoais');
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', true);
}
if (!defined('APP_URL')) {
    define('APP_URL', 'http://192.168.3.10:8081/');
}
if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'America/Fortaleza');
}


// ✅ CORRIGIDO: Importar Cloudinary SDK
$vendor_path = BASE_PATH . '/vendor/autoload.php';

if (!file_exists($vendor_path)) {
    die('❌ ERRO: Vendor não encontrado em: ' . $vendor_path . ' | BASE_PATH = ' . BASE_PATH);
}

require_once $vendor_path;
