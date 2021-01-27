---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://www.laravel.com/docs/collection.json)

<!-- END_INFO -->

#general


<!-- START_c6c5c00d6ac7f771f157dff4a2889b1a -->
## _debugbar/open
> Example request:

```bash
curl -X GET -G "http://www.laravel.com/_debugbar/open" 
```

```javascript
const url = new URL("http://www.laravel.com/_debugbar/open");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET _debugbar/open`


<!-- END_c6c5c00d6ac7f771f157dff4a2889b1a -->

<!-- START_7b167949c615f4a7e7b673f8d5fdaf59 -->
## Return Clockwork output

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/_debugbar/clockwork/1" 
```

```javascript
const url = new URL("http://www.laravel.com/_debugbar/clockwork/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET _debugbar/clockwork/{id}`


<!-- END_7b167949c615f4a7e7b673f8d5fdaf59 -->

<!-- START_01a252c50bd17b20340dbc5a91cea4b7 -->
## _debugbar/telescope/{id}
> Example request:

```bash
curl -X GET -G "http://www.laravel.com/_debugbar/telescope/1" 
```

```javascript
const url = new URL("http://www.laravel.com/_debugbar/telescope/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET _debugbar/telescope/{id}`


<!-- END_01a252c50bd17b20340dbc5a91cea4b7 -->

<!-- START_5f8a640000f5db43332951f0d77378c4 -->
## Return the stylesheets for the Debugbar

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/_debugbar/assets/stylesheets" 
```

```javascript
const url = new URL("http://www.laravel.com/_debugbar/assets/stylesheets");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET _debugbar/assets/stylesheets`


<!-- END_5f8a640000f5db43332951f0d77378c4 -->

<!-- START_db7a887cf930ce3c638a8708fd1a75ee -->
## Return the javascript for the Debugbar

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/_debugbar/assets/javascript" 
```

```javascript
const url = new URL("http://www.laravel.com/_debugbar/assets/javascript");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET _debugbar/assets/javascript`


<!-- END_db7a887cf930ce3c638a8708fd1a75ee -->

<!-- START_0973671c4f56e7409202dc85c868d442 -->
## Forget a cache key

> Example request:

```bash
curl -X DELETE "http://www.laravel.com/_debugbar/cache/1/1" 
```

```javascript
const url = new URL("http://www.laravel.com/_debugbar/cache/1/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE _debugbar/cache/{key}/{tags?}`


<!-- END_0973671c4f56e7409202dc85c868d442 -->

<!-- START_8c96493ed9b921fb5cb34c41ff1f0307 -->
## 测试控制器

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/controller" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/controller");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (302):

```json
null
```

### HTTP Request
`GET api/test/controller`


<!-- END_8c96493ed9b921fb5cb34c41ff1f0307 -->

<!-- START_5d7037e6acbcb283cbdbaa72155a771f -->
## 测试重定向

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/redirect" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/redirect");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET api/test/redirect`


<!-- END_5d7037e6acbcb283cbdbaa72155a771f -->

<!-- START_d9025e1e0e8b2ebde1005b598a2014bf -->
## 测试兜底函数

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/1" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET api/test/{fallbackPlaceholder}`


<!-- END_d9025e1e0e8b2ebde1005b598a2014bf -->

<!-- START_8b905672bc8f31b13d13944f9fb2727a -->
## 测试服务提供者

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/provider" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/provider");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/test/provider`


<!-- END_8b905672bc8f31b13d13944f9fb2727a -->

<!-- START_78bd26e1224916be3ef576013d6ec554 -->
## 测试门面类

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/facade" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/facade");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/test/facade`


<!-- END_78bd26e1224916be3ef576013d6ec554 -->

<!-- START_ee23442162a3da2ead74cdb66e390bb5 -->
## 测试表单验证

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/validate" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/validate");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/validate`


<!-- END_ee23442162a3da2ead74cdb66e390bb5 -->

<!-- START_059b468019897ae19e197884a02250b5 -->
## api/test/database
> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/database" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/database");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/test/database`

`POST api/test/database`

`PUT api/test/database`

`PATCH api/test/database`

`DELETE api/test/database`

`OPTIONS api/test/database`


<!-- END_059b468019897ae19e197884a02250b5 -->

<!-- START_96fb3565c217b7839b68ef52289bfc95 -->
## Handle a registration request for the application.

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/register" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/register");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/register`


<!-- END_96fb3565c217b7839b68ef52289bfc95 -->

<!-- START_f00569ee46b5e5a86501049879c30e9e -->
## 自定义登录方法(token登录)

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/login" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/login`


<!-- END_f00569ee46b5e5a86501049879c30e9e -->

<!-- START_e62d1fd13ffbb644270b5f9e9dc7ba3b -->
## 自定义登出方法(token登录)

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/logout" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/logout");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/logout`


<!-- END_e62d1fd13ffbb644270b5f9e9dc7ba3b -->

<!-- START_290866b5868fe79ae176bf045e620f0d -->
## 校验认证功能

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/auth" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/auth");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/auth`


<!-- END_290866b5868fe79ae176bf045e620f0d -->

<!-- START_2e26629809c7142c4f23372cec300c8b -->
## 检查路由访问权限

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/permission" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/permission");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/permission`


<!-- END_2e26629809c7142c4f23372cec300c8b -->

<!-- START_27f805b70025da2e97e4a368892a87ca -->
## 检查用户数据更新权限

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/policy" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/policy");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/policy`


<!-- END_27f805b70025da2e97e4a368892a87ca -->

<!-- START_0500379df8e79596cbc763d843e767d9 -->
## JWT登录

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/jwt/login" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/jwt/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/jwt/login`


<!-- END_0500379df8e79596cbc763d843e767d9 -->

<!-- START_f2da0b92c60984e1c012658d82b009ea -->
## 刷新token(刷新后旧token无法使用)

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/jwt/refresh" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/jwt/refresh");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/jwt/refresh`


<!-- END_f2da0b92c60984e1c012658d82b009ea -->

<!-- START_20057331a86a7f304690eb6c4c55e5bf -->
## JWT登出

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/jwt/logout" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/jwt/logout");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/jwt/logout`


<!-- END_20057331a86a7f304690eb6c4c55e5bf -->

<!-- START_573222ea95d00905e7f0d95d274ea6bc -->
## 检查认证是否有效

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/jwt/auth" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/jwt/auth");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/jwt/auth`


<!-- END_573222ea95d00905e7f0d95d274ea6bc -->

<!-- START_db7b87ef2f13cf98a3c5c2e9c4ac48e1 -->
## 检查授权是否有效

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/test/jwt/permission" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/jwt/permission");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/test/jwt/permission`


<!-- END_db7b87ef2f13cf98a3c5c2e9c4ac48e1 -->

<!-- START_652a091af873d610e442910aac8826a6 -->
## 请求api

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/request" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/request");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
"api\/test\/request"
```

### HTTP Request
`GET api/test/request`


<!-- END_652a091af873d610e442910aac8826a6 -->

<!-- START_06c2ebedfcf470b1f80477eab4059943 -->
## 响应api

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/test/response" 
```

```javascript
const url = new URL("http://www.laravel.com/api/test/response");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/test/response`


<!-- END_06c2ebedfcf470b1f80477eab4059943 -->

<!-- START_2e633e99a6ab75cafc7a5e19864ec676 -->
## Display a listing of the resource.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/resource" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/resource`


<!-- END_2e633e99a6ab75cafc7a5e19864ec676 -->

<!-- START_078dbf21ae8cc40d876dff5e082e1a38 -->
## Show the form for creating a new resource.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/resource/create" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/resource/create`


<!-- END_078dbf21ae8cc40d876dff5e082e1a38 -->

<!-- START_c145c3b746334923c378ed5491dc4753 -->
## Store a newly created resource in storage.

> Example request:

```bash
curl -X POST "http://www.laravel.com/api/resource/store" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource/store");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/resource/store`


<!-- END_c145c3b746334923c378ed5491dc4753 -->

<!-- START_1484254391e0657398746c519175b944 -->
## Display the specified resource.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/resource/1" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/resource/{id}`


<!-- END_1484254391e0657398746c519175b944 -->

<!-- START_4505d872762e89ea1572df9d39314365 -->
## Show the form for editing the specified resource.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/api/resource/1/edit" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET api/resource/{id}/edit`


<!-- END_4505d872762e89ea1572df9d39314365 -->

<!-- START_6a6ebb13a12571f2b8c5e694c454d30c -->
## Update the specified resource in storage.

> Example request:

```bash
curl -X PUT "http://www.laravel.com/api/resource/1" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/resource/{id}`


<!-- END_6a6ebb13a12571f2b8c5e694c454d30c -->

<!-- START_3a6d6fb0ee767bb49bf14b9ca572e3bc -->
## Remove the specified resource from storage.

> Example request:

```bash
curl -X DELETE "http://www.laravel.com/api/resource/1" 
```

```javascript
const url = new URL("http://www.laravel.com/api/resource/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/resource/{id}`


<!-- END_3a6d6fb0ee767bb49bf14b9ca572e3bc -->

<!-- START_66e08d3cc8222573018fed49e121e96d -->
## Show the application&#039;s login form.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/login" 
```

```javascript
const url = new URL("http://www.laravel.com/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET login`


<!-- END_66e08d3cc8222573018fed49e121e96d -->

<!-- START_ba35aa39474cb98cfb31829e70eb8b74 -->
## 自定义登录方法(token登录)

> Example request:

```bash
curl -X POST "http://www.laravel.com/login" 
```

```javascript
const url = new URL("http://www.laravel.com/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST login`


<!-- END_ba35aa39474cb98cfb31829e70eb8b74 -->

<!-- START_e65925f23b9bc6b93d9356895f29f80c -->
## 自定义登出方法(token登录)

> Example request:

```bash
curl -X POST "http://www.laravel.com/logout" 
```

```javascript
const url = new URL("http://www.laravel.com/logout");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST logout`


<!-- END_e65925f23b9bc6b93d9356895f29f80c -->

<!-- START_ff38dfb1bd1bb7e1aa24b4e1792a9768 -->
## Show the application registration form.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/register" 
```

```javascript
const url = new URL("http://www.laravel.com/register");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET register`


<!-- END_ff38dfb1bd1bb7e1aa24b4e1792a9768 -->

<!-- START_d7aad7b5ac127700500280d511a3db01 -->
## Handle a registration request for the application.

> Example request:

```bash
curl -X POST "http://www.laravel.com/register" 
```

```javascript
const url = new URL("http://www.laravel.com/register");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST register`


<!-- END_d7aad7b5ac127700500280d511a3db01 -->

<!-- START_d72797bae6d0b1f3a341ebb1f8900441 -->
## Display the form to request a password reset link.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/password/reset" 
```

```javascript
const url = new URL("http://www.laravel.com/password/reset");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET password/reset`


<!-- END_d72797bae6d0b1f3a341ebb1f8900441 -->

<!-- START_feb40f06a93c80d742181b6ffb6b734e -->
## Send a reset link to the given user.

> Example request:

```bash
curl -X POST "http://www.laravel.com/password/email" 
```

```javascript
const url = new URL("http://www.laravel.com/password/email");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST password/email`


<!-- END_feb40f06a93c80d742181b6ffb6b734e -->

<!-- START_e1605a6e5ceee9d1aeb7729216635fd7 -->
## Display the password reset view for the given token.

If no token is present, display the link request form.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/password/reset/1" 
```

```javascript
const url = new URL("http://www.laravel.com/password/reset/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET password/reset/{token}`


<!-- END_e1605a6e5ceee9d1aeb7729216635fd7 -->

<!-- START_cafb407b7a846b31491f97719bb15aef -->
## Reset the given user&#039;s password.

> Example request:

```bash
curl -X POST "http://www.laravel.com/password/reset" 
```

```javascript
const url = new URL("http://www.laravel.com/password/reset");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST password/reset`


<!-- END_cafb407b7a846b31491f97719bb15aef -->

<!-- START_cb859c8e84c35d7133b6a6c8eac253f8 -->
## Show the application dashboard.

> Example request:

```bash
curl -X GET -G "http://www.laravel.com/home" 
```

```javascript
const url = new URL("http://www.laravel.com/home");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET home`


<!-- END_cb859c8e84c35d7133b6a6c8eac253f8 -->


