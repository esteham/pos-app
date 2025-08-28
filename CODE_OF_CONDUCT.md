Laravel-এর সব Artisan কমান্ডের গুরুত্বপূর্ণ Flags ও Options এবং বাংলা ব্যাখ্যা (সম্পূর্ণ ডক ফাইল চেকলিস্ট)

---

## Environment & Debug

* `APP_DEBUG=true/false` : ডিবাগ মোড চালু/বন্ধ।
* `APP_ENV=local/production/staging` : অ্যাপ কোন environment-এ চলছে।

## Artisan Global Flags

* `-h` বা `--help` : কমান্ডের সাহায্য তথ্য দেখায়
* `-q` বা `--quiet` : সব আউটপুট বন্ধ করে
* `-V` বা `--version` : Laravel সংস্করণ দেখায়
* `--ansi` বা `--no-ansi` : ANSI আউটপুট চালু বা বন্ধ করে
* `-n` বা `--no-interaction` : কোন ইন্টারঅ্যাকশন ছাড়া কমান্ড চালায়
* `--env` : নির্দিষ্ট environment কনফিগারেশন ব্যবহার করে
* `-v`, `-vv`, `-vvv` : আউটপুটের বিস্তারিততা বাড়ায়

## Migration

* `php artisan migrate --force` : প্রোডাকশনে migration চালানোর জন্য বাধ্য করা।
* `php artisan migrate --path=path/to/file` : নির্দিষ্ট migration ফাইল বা ফোল্ডার চালানোর জন্য।
* `php artisan migrate:rollback --step=1` : কত step rollback হবে।
* `php artisan migrate:fresh --seed` : fresh migration চালানোর পর seed করবে।

## Queue

* `php artisan queue:work --tries=3` : কাজ ব্যর্থ হলে retry সংখ্যা।
* `php artisan queue:work --timeout=60` : প্রতিটি job কত সেকেন্ডে শেষ হবে।

## Server

* `php artisan serve --host=127.0.0.1` : সার্ভার কোন IP এ চলবে।
* `php artisan serve --port=8000` : সার্ভার কোন পোর্টে চলবে।

## Cache

* `php artisan cache:clear` : ক্যাশে পরিষ্কার করে
* `php artisan config:cache` : কনফিগারেশন ক্যাশে তৈরি করে
* `php artisan route:cache` : রুট ক্যাশে তৈরি করে
* `php artisan view:cache` : ভিউ ক্যাশে তৈরি করে

## Maintenance Mode

* `php artisan down --message="Maintenance"` : maintenance message দেখানোর জন্য।
* `php artisan down --retry=60` : কত সেকেন্ড পরে স্বয়ংক্রিয়ভাবে পরীক্ষা করবে।
* `php artisan up` : মেইনটেন্যান্স মোড থেকে অ্যাপটি পুনরায় চালু করে

## Make Commands

* `php artisan make:model -a` : মডেল, মাইগ্রেশন, সিডার, ফ্যাক্টরি, কন্ট্রোলার, পলিসি, ফর্ম রিকোয়েস্ট একসাথে তৈরি
* `php artisan make:model -c` : model এর সাথে Controller তৈরি
* `php artisan make:model -f` : model এর সাথে factory তৈরি
* `php artisan make:model -m` : model এর সাথে migration তৈরি
* `php artisan make:model -p` : pivot table model তৈরি
* `php artisan make:model -s` : seeder তৈরি
* `php artisan make:model -t` : timestamps সহ model তৈরি
* `php artisan make:controller --resource` : resource controller তৈরি
* `php artisan make:controller --invokable` : invokable controller তৈরি

## Route

* `php artisan route:list --name=route_name` : নির্দিষ্ট নামের route দেখাবে
* `php artisan route:list --method=GET` : নির্দিষ্ট HTTP method-এর route দেখাবে

## Test

* `php artisan test --filter=TestName` : নির্দিষ্ট টেস্ট চলাবে
* `php artisan test --parallel` : টেস্ট parallel mode-এ চালাবে
* `php artisan test --coverage` : কোড কভারেজ রিপোর্ট তৈরি করে

## Extra/Commonly Used Flags

* `--step` : rollback/migration এর step নির্ধারণ
* `--timeout` : queue jobs এর timeout নির্ধারণ
* `--tries` : queue jobs retry সংখ্যা
* `--force` : production command execute করতে বাধ্য করা
* `--seed` : fresh migration পরে data seed করা
* `--resource` : resource controller তৈরি
* `--invokable` : invokable controller তৈরি
* `--name` : route/filter দ্বারা দেখানো
* `--method` : HTTP method অনুযায়ী route দেখানো
* `--filter` : নির্দিষ্ট টেস্ট চলানো
* `--parallel` : টেস্ট parallel mode-এ চালানো

## Custom Commands & Signature

* `php artisan make:command CommandName` : নতুন কাস্টম কমান্ড তৈরি
* `$signature = 'command:name {argument} {--option=}'` : কাস্টম কমান্ডে আর্গুমেন্ট ও অপশন সংজ্ঞায়িত করা

---

**ব্যবহার কৌশল:** একবারে সব flags reference হিসেবে ব্যবহার করা যায়। কমান্ড + Flag + বাংলা ব্যাখ্যা একসাথে দেখে দ্রুত decision নেওয়া যায়।
