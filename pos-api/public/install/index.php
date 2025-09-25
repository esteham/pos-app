<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

error_reporting(E_ALL);


$basePath      = dirname(__DIR__, 2);
$installedFlag = $basePath . '/storage/installed';

function base_url_pos(): array
{
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $publicBase = rtrim(dirname($scriptDir), '/');
    $apiHome = $publicBase . '/';
    $posUrl = $publicBase . '/pos/';
    return [$apiHome, $posUrl];
}

function env_write(string $root, array $vars): bool
{
    $tpl = @file_get_contents($root . '/.env.example');
    if( $tpl === false) $tpl = "";
    $map = [];

    foreach(explode("\n", $tpl) as $ln)
    {
        if(preg_match('/^\s*([A-Z0-9_]+)\s*=\s*(.*)$/', $ln, $m))
        {
             $map[$m[1]] = trim($m[2]);
        }
    }

    foreach($vars as $k=>$v) $map[$k] = is_bool($v) ? ($v? 'true' : 'false') : (string)$v;

    $out = '';

    foreach($map as $k => $v)
    {
        $needsQuote = preg_match('/\s|#|=|"/', $v);
        $v = $needsQuote ? '"'.str_replace('"','\"',$v). '"' : $v;
        $out .=$k. "=" .$v."\n";
    }

    return false !== @file_put_contents($root. '/.env', $out);

}

function artisan_call(string $root, string $command, array $params = [], ?string &$output = null): int
{
    require_once $root . '/vendor/autoload.php';
    $app = require $root . '/bootstrap/app.php';

    $kernel = $app->make(ConsoleKernel::class);
    $code = $kernel->call($command, $params);
    $output = $kernel->output();
    return $code;
}

function dev_code_generate(): string
{
    $seg = function() { return strtoupper(bin2hex(random_bytes(2)));};
    return 'DEV-'.$seg().'-'.$seg();
}

function dev_code_valid(string $code): bool
{
    return (bool)preg_match('/^DEV-[A-Z0-9]{4}-[A-Z0-9]{4}$/', strtoupper(trim($code)));
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


$step = $_POST['step'] ?? $_GET['step'] ?? '1';
header('Content-Type: text/html; charset=utf-8');

if ((string)$step === '1') {
    
    $req = [
        'php>=8.0.2'          => version_compare(PHP_VERSION, '8.0.2', '>='),
        'ext_pdo_mysql'       => extension_loaded('pdo_mysql'),
        'ext_curl'            => function_exists('curl_init'),   
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

if ((string)$step === '2') {   
    $scheme     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $defaultUrl = isset($_SERVER['HTTP_HOST']) ? $scheme.$_SERVER['HTTP_HOST'] : 'http://localhost';
    echo '<h2>Step 2/3: Database & Admin $ Purchase Code</h2>
    <form method="post">
      <input type="hidden" name="step" value="3">
      <label>APP_URL</label><br><input name="APP_URL" value="'.htmlspecialchars($defaultUrl).'" style="width:360px"><br><br>
      <label>DB_HOST</label><br><input name="DB_HOST" value="127.0.0.1"><br><br>
      <label>DB_PORT</label><br><input name="DB_PORT" value="3306"><br><br>
      <label>DB_DATABASE</label><br><input name="DB_DATABASE" value="pos_db"><br><br>
      <label>DB_USERNAME</label><br><input name="DB_USERNAME" value="root"><br><br>
      <label>DB_PASSWORD</label><br><input type="password" name="DB_PASSWORD" value=""><br><br>
      <hr>

      <label>Purchase Code (Local)</label><br>
      <input name="PURCHASE_CODE" placeholder="DEV-AB12-CD34"><br><br>
      <small>Dont have a purchase code? <a href="?step=claim">Claim Purchase Code</a></small><br><br>
      <label>Licensed To (optional)</label><br>
      <input name="LICENSED_TO" placeholder="Your Name/ Company Name"><br><br>


      <label>Admin Email</label><br><input name="ADMIN_EMAIL" value="admin@pos.local"><br><br>
      <label>Admin Password</label><br><input type="password" name="ADMIN_PASSWORD" value="admin123"><br><br>
      <button>Install &raquo;</button>
    </form>';
    exit;
}

//Purchase code claim

if((string)$step === 'claim')
{
    echo '<h2>Claim Purchase Code</h2>
        <form method="post" action="?step=claim_submit">
            <label>Name</label><br><input name="name" required><br><br>
            <label>Email</label><br><input type="email" name="email" required><br><br>
            <label>Company</label><br><input name="company"><br><br>
            <label>Price(default)</label><br><input name="price" value="49" type="number" step="0.01"><br><br>
            <button type="submit">Request Code</button>
            <a href="?step=2" style="margin-left: 8px">Back</a>
        </form>';
        exit;
}

if((string)$step === 'claim_submit')
{
    $ls = 'http://127.0.0.1:8001/api/claim';
    $payload = [

       'name' => $_POST['name'] ?? '',
       'email' => $_POST['email'] ?? '',
       'company' => $_POST['company'] ?? '',
       'price' => $_POST['price'] ?? '',
    ];

    $ch = curl_init($ls);
    curl_setopt_array($ch, [

        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_TIMEOUT => 15,
    ]);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    echo '<h2>Claim Result</h2>';

    if($resp && !$err)
    {
        $json = json_decode($resp, true);
        if(!empty($json['ok']))
        {
            echo '<p>Code sent to email: <strong>'.htmlspecialchars($payload['email']).'</strong></p>';
            if(!empty($json['code']))
            {
                 echo '<p><small>Dev quick test - code: <code>'.htmlspecialchars($json['code']).'</code></small></p>';
            }

            echo '<p><a href="?step=2">Back to Installer</a></p>';
        }

        else
        {
            echo '<p class="fail">Failed to claim code.</p><pre>'.htmlspecialchars($resp).'</pre><p><a href="?step=claim">Try again</a></p>';
        }
    }

    else
    {
        echo '<p class="fail">License server not available.</p><pre>'.htmlspecialchars($err? : 'Unknown Error').'</pre><p><a href="?step=claim">Try again</a></p>';
    }

    exit;
}


if ((string)$step === '3') {

    $pc_input = strtoupper(trim($_POST['PURCHASE_CODE'] ?? ''));
    $licensedTo = trim($_POST['LICENSED_TO'] ?? '');

    $useOnline = false;
    if($pc_input !== '' && !preg_match('/^DEV-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $pc_input))
    {
        $useOnline = true;
    }

    //Online Verify

    if($useOnline)
    {
        $verifyUrl = 'http://127.0.0.1:8001/api/verify';
        $payload = [

            'purchase_code' => $pc_input,
            'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
            'app_url' => $_POST['APP_URL'] ?? 'http://localhost',
        ];

        $ch = curl_init($verifyUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT => 15,
    ]);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if(!$resp || $err)
    {
        echo '<h3 class="fail">License verification server unreachable</h3><pre>'.htmlspecialchars($err ?: 'No response').'</pre>';
        exit;
    }

    $json = json_decode($resp, true);
    if(empty($json['valid']))
    {
        echo '<h3 class="fail">Purchase code invalid</h3><pre>'.htmlspecialchars($resp).'</pre>';
    exit;
    }

    $licenseMode = 'online';
    $licenseStatus = 'valid';

    }

    else
    {
        if($pc_input === '')
        {
            $pc_input = dev_code_generate();
        }
        elseif (!dev_code_valid($pc_input)) 
        {
            echo '<h3 class="fail">Invalid DEV code format. Example: DEV-AB12-CD34</h3>';
            exit;
        }

         $licenseMode = 'offline';
         $licenseStatus = 'valid';
    }
    
   
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

        'LICENSE_MODE' => $licenseMode,

        'LICENSE_SERVER' => $useOnline ? 'http://127.0.0.1:8001/api/verify':'' ,

        'PURCHASE_CODE' =>  $pc_input,

        'LICENSED_TO' => $licensedTo,

        'LICENSE_STATUS' => $licenseStatus,

        'LICENSE_LAST_CHECK' => date('c'),
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

        if($licenseMode === 'offline')
        {
            echo '<p><strong>Your DEV code:</strong>'.htmlspecialchars($pc_input).'</p>';
        }        
        echo '<p><a href="'.htmlspecialchars($posUrl).'">Open POS Frontend</a> | <a href="'.htmlspecialchars($apiHome).'">API Home</a></p>';
        echo '<p style="color:#888">You can delete the <code>/public/install</code> folder (optional).</p>';
        echo '<pre>'.htmlspecialchars($log).'</pre>';
    } 
    else 
    {
        echo '<h3 class="fail">Installation failed</h3>';
        echo '<pre>'.htmlspecialchars($log).'</pre>';
    }
    exit;
}

header('Location: ?step=1');
exit;
