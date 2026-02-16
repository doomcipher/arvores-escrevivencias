<?php
/**
 * config/env.php (compatível e mais seguro)
 *
 * Objetivos:
 * - NÃO guardar secrets no Git (sem SUAP/Cloudinary hardcoded).
 * - Manter constantes antigas (SUAP_* e CLOUDINARY_*) para não quebrar código existente.
 * - Remover scope sensível do SUAP (sem documentos_pessoais -> evita CPF).
 * - Funcionar em XAMPP e em hospedagem comum (DB_HOST localhost).
 */

if (!defined('BASE_PATH')) {
    // BASE_PATH aponta para a raiz do projeto (pasta que contém /config, /public, /src...)
    define('BASE_PATH', dirname(__DIR__));
}

/**
 * Carrega overrides locais (NÃO versionar):
 * - config/env.local.php
 *
 * Use esse arquivo para colocar SUAP_CLIENT_SECRET, DB_PASS etc. no servidor/local,
 * sem subir para o Git.
 */
$localEnv = __DIR__ . '/env.local.php';
if (file_exists($localEnv)) {
    require $localEnv;
}

/**
 * Composer autoload (só carrega se existir).
 * Isso evita quebrar em ambientes onde você ainda não rodou composer install.
 */
$vendor = BASE_PATH . '/vendor/autoload.php';
if (file_exists($vendor)) {
    require_once $vendor;
}

/** =========================
 *  Banco de dados
 *  ========================= */
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'arvores');

/** =========================
 *  App / ambiente
 *  ========================= */
if (!defined('TIMEZONE')) define('TIMEZONE', getenv('TIMEZONE') ?: 'America/Fortaleza');

// Em hospedagem, debug deve ser OFF. Para ativar local, set APP_DEBUG=1 no env.local.php
if (!defined('APP_DEBUG')) define('APP_DEBUG', (getenv('APP_DEBUG') ?: '0') === '1');

// URL base do site (ajuste no env.local.php quando hospedar)
if (!defined('APP_URL')) define('APP_URL', getenv('APP_URL') ?: 'http://localhost/');

/** =========================
 *  SUAP (opcional)
 *  ========================= */
if (!defined('SUAP_URL')) define('SUAP_URL', getenv('SUAP_URL') ?: 'https://suap.ifrn.edu.br');

// Mantém as constantes para compatibilidade.
// Se não tiver definido no env.local.php, ficam vazias (sem quebrar "define").
if (!defined('SUAP_CLIENT_ID')) define('SUAP_CLIENT_ID', getenv('SUAP_CLIENT_ID') ?: '');
if (!defined('SUAP_CLIENT_SECRET')) define('SUAP_CLIENT_SECRET', getenv('SUAP_CLIENT_SECRET') ?: '');

// Redirect deve ser o callback no seu domínio.
// Se não definido, assume APP_URL + callback.html
if (!defined('SUAP_REDIRECT_URI')) define('SUAP_REDIRECT_URI', getenv('SUAP_REDIRECT_URI') ?: (rtrim(APP_URL, '/') . '/callback.html'));

// ⚠️ MUITO IMPORTANTE: NÃO pedir documentos_pessoais (CPF etc.)
// Se precisar de email, pode usar "identificacao email". Mas CPF não.
if (!defined('SUAP_SCOPE')) define('SUAP_SCOPE', getenv('SUAP_SCOPE') ?: 'identificacao');

/** =========================
 *  Cloudinary (DESATIVADO / compatibilidade)
 *  =========================
 * Mantemos as constantes vazias para o projeto não quebrar se algum arquivo ainda referenciar.
 * Quando vocês removerem de vez o CloudinaryHelper, podem deletar este bloco.
 */
if (!defined('CLOUDINARY_CLOUD_NAME')) define('CLOUDINARY_CLOUD_NAME', getenv('CLOUDINARY_CLOUD_NAME') ?: '');
if (!defined('CLOUDINARY_API_KEY')) define('CLOUDINARY_API_KEY', getenv('CLOUDINARY_API_KEY') ?: '');
if (!defined('CLOUDINARY_API_SECRET')) define('CLOUDINARY_API_SECRET', getenv('CLOUDINARY_API_SECRET') ?: '');
