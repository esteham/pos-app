<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

error_reporting(E_ALL);


$basePath      = dirname(__DIR__, 2);
$installedFlag = $basePath . '/storage/installed';

function base_url_pos(): array {
    // current script dir:  /{project}/public/install
    $scriptDir  = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $publicBase = rtrim(dirname($scriptDir), '/');   // /{project}/public
    $apiHome    = $publicBase . '/';
    $posUrl     = $publicBase . '/pos/';
    return [$apiHome, $posUrl];
}

function env_write(string $root, array $vars): bool {
    $tpl = @file_get_contents($root . '/.env.example');
    if ($tpl === false) $tpl = ""; // fallback
    $map = [];
    foreach (explode("\n", $tpl) as $ln) {
        if (preg_match('/^\s*([A-Z0-9_]+)\s*=\s*(.*)$/', $ln, $m)) $map[$m[1]] = trim($m[2]);
    }
    foreach ($vars as $k=>$v) $map[$k] = is_bool($v) ? ($v?'true':'false') : (string)$v;

    $out = '';
    foreach ($map as $k=>$v) {
        $needsQuote = preg_match('/\s|#|=|"/', $v);
        $v = $needsQuote ? '"' . str_replace('"','\"',$v) . '"' : $v;
        $out .= $k.'='.$v."\n";
    }
    return false !== @file_put_contents($root.'/.env', $out);
}

function artisan_call(string $root, string $command, array $params = [], ?string &$output = null): int {
    require_once $root . '/vendor/autoload.php';
    $app = require $root . '/bootstrap/app.php';
   
    $kernel = $app->make(ConsoleKernel::class);
    $code   = $kernel->call($command, $params);
    $output = $kernel->output();
    return $code; // 0 = success
}

if (file_exists($installedFlag)) {
    [$apiHome, $posUrl] = base_url_pos();
    header('Content-Type: text/html; charset=utf-8');
    echo '<h3>Already installed</h3>';
    echo '<p><a href="'.htmlspecialchars($posUrl).'">Open POS Frontend</a> | <a href="'.htmlspecialchars($apiHome).'">API Home</a></p>';
    exit;
}

$style = 'body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:860px;margin:24px auto;padding:0 12px}
h2,h3{margin:.2em 0 .6em}
label{font-weight:600}
input,select{padding:6px 8px;margin:.2em 0}
button{padding:8px 14px;background:#0563e7;border:0;color:#fff;border-radius:4px;cursor:pointer}
button:disabled{opacity:.6;cursor:not-allowed}
pre{background:#111;color:#eee;padding:12px;border-radius:6px;overflow:auto;max-height:420px}
.ok{color:#0a7a0a}.fail{color:#c00}
hr{border:0;border-top:1px solid #eee;margin:16px 0}';
echo '<style>'.$style.'</style>';


$step = (int)($_POST['step'] ?? ($_GET['step'] ?? 1));
header('Content-Type: text/html; charset=utf-8');

if ($step === 1) {
    
    $req = [
        'php>=8.0.2'          => version_compare(PHP_VERSION, '8.0.2', '>='),
        'ext_pdo_mysql'       => extension_loaded('pdo_mysql'),
        'vendor_autoload'     => file_exists($basePath.'/vendor/autoload.php'),
        'storage_writable'    => is_writable($basePath.'/storage'),
        'bootstrap_writable'  => is_writable($basePath.'/bootstrap/cache'),
    ];
    $allOk = !in_array(false, $req, true);

    echo '<h2>Live POS Installer — Step 1/3: Requirements</h2><ul>';
    foreach ($req as $k=>$ok) {
        echo '<li>'.htmlspecialchars($k).': '.($ok ? '<span class="ok">OK</span>' : '<span class="fail">FAIL</span>').'</li>';
    }
    echo '</ul>';

    if (!$allOk) {
        echo '<p class="fail">Fix the above and reload.</p>';
        echo '<ul><li>Ship with <code>vendor/</code> or run <code>composer install</code>.</li>
              <li>Ensure <code>storage</code> &amp; <code>bootstrap/cache</code> are writable.</li></ul>';
        exit;
    }
    echo '<form method="post"><input type="hidden" name="step" value="2"><button>Next &raquo;</button></form>';
    exit;
}

if ($step === 2) {   
    $scheme     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $defaultUrl = isset($_SERVER['HTTP_HOST']) ? $scheme.$_SERVER['HTTP_HOST'] : 'http://localhost';
    echo '<h2>Step 2/3: Database & Admin</h2>
    <form method="post">
      <input type="hidden" name="step" value="3">
      <label>APP_URL</label><br><input name="APP_URL" value="'.htmlspecialchars($defaultUrl).'" style="width:360px"><br><br>
      <label>DB_HOST</label><br><input name="DB_HOST" value="127.0.0.1"><br><br>
      <label>DB_PORT</label><br><input name="DB_PORT" value="3306"><br><br>
      <label>DB_DATABASE</label><br><input name="DB_DATABASE" value="pos_db"><br><br>
      <label>DB_USERNAME</label><br><input name="DB_USERNAME" value="root"><br><br>
      <label>DB_PASSWORD</label><br><input type="password" name="DB_PASSWORD" value=""><br><br>
      <hr>
      <label>Admin Email</label><br><input name="ADMIN_EMAIL" value="admin@pos.local"><br><br>
      <label>Admin Password</label><br><input type="password" name="ADMIN_PASSWORD" value="admin123"><br><br>
      <button>Install &raquo;</button>
    </form>';
    exit;
}

if ($step === 3) {
   
    $vars = [
        'APP_NAME'      => 'Live POS',
        'APP_ENV'       => 'local',
        'APP_KEY'       => '',
        'APP_DEBUG'     => 'true',
        'APP_URL'       => $_POST['APP_URL'] ?? 'http://localhost',
        'LOG_CHANNEL'   => 'stack',

        'DB_CONNECTION' => 'mysql',
        'DB_HOST'       => $_POST['DB_HOST'] ?? '127.0.0.1',
        'DB_PORT'       => $_POST['DB_PORT'] ?? '3306',
        'DB_DATABASE'   => $_POST['DB_DATABASE'] ?? 'pos_db',
        'DB_USERNAME'   => $_POST['DB_USERNAME'] ?? 'root',
        'DB_PASSWORD'   => $_POST['DB_PASSWORD'] ?? '',
    ];
    if (!env_write($basePath, $vars)) {
        echo '<h3 class="fail">.env write failed</h3>';
        exit;
    }

    $log = '';

   
    $c1 = artisan_call($basePath, 'key:generate', ['--force'=>true], $o1); $log .= $o1."\n";
    
    $c2 = artisan_call($basePath, 'migrate', ['--force'=>true], $o2);      $log .= $o2."\n";
    
    $c3 = artisan_call($basePath, 'db:seed', ['--force'=>true], $o3);      $log .= $o3."\n";
    
    $c4 = artisan_call($basePath, 'storage:link', [], $o4);                $log .= $o4."\n";

   
    require_once $basePath . '/vendor/autoload.php';
    $app = require $basePath . '/bootstrap/app.php';
    $k = $app->make(ConsoleKernel::class); $k->bootstrap();

    $email = $_POST['ADMIN_EMAIL'] ?? 'admin@pos.local';
    $pass  = $_POST['ADMIN_PASSWORD'] ?? 'admin123';

    try {
        \App\Models\User::updateOrCreate(
            ['email'=>$email],
            ['name'=>'Administrator','password'=>\Illuminate\Support\Facades\Hash::make($pass),'role'=>'admin']
        );
        $log .= "Admin user ensured: {$email}\n";
    } catch (\Throwable $e) {
        $log .= "Admin create failed: ".$e->getMessage()."\n";
    }

    
    $c5 = artisan_call($basePath, 'optimize:clear', [], $o5);              $log .= $o5."\n";

    
    $failed = ($c1 || $c2 || $c3);

    [$apiHome, $posUrl] = base_url_pos();

    if (!$failed) {
        @file_put_contents($installedFlag, date('c'));
        echo '<h3>Installation Completed </h3>';
        echo '<p><a href="'.htmlspecialchars($posUrl).'">Open POS Frontend</a> | <a href="'.htmlspecialchars($apiHome).'">API Home</a></p>';
        echo '<p style="color:#888">You can delete the <code>/public/install</code> folder (optional).</p>';
        echo '<pre>'.htmlspecialchars($log).'</pre>';
    } else {
        echo '<h3 class="fail">Installation failed</h3>';
        echo '<pre>'.htmlspecialchars($log).'</pre>';
    }
    exit;
    
}
