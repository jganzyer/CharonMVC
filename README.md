<p align="center">
<a href="https://charonMVC.com" target="_blank_"><img src="https://i.hizliresim.com/zBvV5j.png" width="310px" /></a>
<br/>
<img src="https://img.shields.io/badge/build-passing-brightgreen.svg?style=flat-square" />
<img src="https://img.shields.io/badge/issues-0-brightgreen.svg?style=flat-square" />
<img src="https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square" />
<img src="https://img.shields.io/badge/rating-%E2%98%85%E2%98%85%E2%98%85%E2%98%85%E2%98%85-brightgreen.svg?style=flat-square" />
</p>

[CharonMVC][1] is a super simple and powerfull **PHP framework** for web applications.


## Installation

* [Download][2] CharonMVC and extract. Simple huh?

## TODO'S

* add validator

## Routing

Routing in CharonMVC is very easy. You can use routing static `Route::` or dynamic `$app->`.

```php
$pattern = '/page/[variable1:(regex)]';

$pattern = '/page/[variable2:(\d+)]';
// matches: /page/(0, 1, 2 ,3...)
$pattern = '/action/[variable3:(edit|delete|create)]';
//matches: /action/edit or /action/delete or /action/create

$methods = 'GET,POST,PUT';

$callback = function() {  };
$callback = 'Home.index';

$params = [ $variable1 => $data1, $variable2 => $data2 ];

Route::get($pattern, $callback);
Route::post($pattern, $callback);
Route::put($pattern, $callback);
Route::delete($pattern, $callback);
Route::any($pattern, $callback);
Route::map($methods, $pattern, $callback);

Route::get($pattern,$callback)->name($name);

Route::redirect($name, $params = [], $timeout = 0, $statusCode = 302);
```

## HTTP API

[CharonMVC][1]'s easy request, response and service library

```php
$this->request->
        isSecure()
        isAjax()
        request($method, $url, $params = [], $options = [], &$info = null)
        referer()
        uri($full = false)
        language()
        ip()
        method($type = null)
        variable($data = null)
        file($data = null)
        post($data = null)
        server($data = null)
        header($data = null)

$this->response->
        status($code = null)
        header($key, $value)
        redirect($url, $timeout = 0, $statusCode = 302)
        back()
        file($path, $filename = null, $mimetype = null, $download = false)
        json(array $array, $flag = null)

$this->service->
        json_encode(array $array, $flag = null)
        json_decode($json, $array = true)
        csv_encode(array $array, $delimeter = ',')
        csv_decode($csv, $delimeter = ',')
        sflash($key, $value)
        session_start()
        gflash($key, $next = false)
```

## Charon Wallpapers

<p><a href="https://i.hizliresim.com/VMpDrr.jpg" target="_blank_"><img src="https://i.hizliresim.com/VMpDrr.jpg" width="320px" />
</a></p>

<!-- ## 0.0.0 -->
  <!-- - ... -->

[1]: https://www.charonMVC.com/
[2]: https://github.com/CharonFW/CharonMVC/archive/master.zip
