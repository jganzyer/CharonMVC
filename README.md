<p align="center"><a href="https://charonMVC.com" target="_blank">
  <img src="https://i.hizliresim.com/zBvV5j.png" width="310px" />
</a></p>

[CharonMVC][1] is a super simple and powerfull **PHP framework** for web applications.


## Installation

* [Download][2] CharonMVC and extract. Simple huh?

## Documentation

* SOON

## Charon Wallpapers

<p><a href="https://i.hizliresim.com/VMpDrr.jpg" target="_blank_"><img src="https://i.hizliresim.com/VMpDrr.jpg" width="320px" />
</a></p>

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

<!-- ## 0.0.0 -->
  <!-- - ... -->

[1]: https://www.charonMVC.com/
[2]: https://github.com/CharonFW/CharonMVC/archive/master.zip
