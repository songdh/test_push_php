# test_push_php
the php file to test APNs
###说明###
1. 下载此目录；
2. 将已经生成好的ck.pem文件放入push_worker.php同级目录中；如何生成ck.pem文件，可以[点此查看](http://www.jianshu.com/p/68827590e29a);
3. 修改php代码:

此处填入要接收push的devceToken，可以在APP中打断点获得
```
// Put your device token here (without spaces):
$deviceToken = 'your device token';
```
填入生成ck.pem文件时，设置的密语
```
// Put your private key's passphrase here:
$passphrase = 'your passphrase';
```
调用pem文件名，此处为`ck.pem`，可以自定义
```
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
```
push请求地址
```
    //app测试push地址
    $fp = stream_socket_client(
                               'ssl://gateway.sandbox.push.apple.com:2195', $err,
                               $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    //app正式push地址，上线后修改成下面的地址
    // $fp = stream_socket_client(
    //                           'ssl://gateway.push.apple.com:2195', $err,
    //                            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
```
push的包体结构
```
    // Create the payload body
    $body['aps'] = array(
                         'alert' => $message,
                         'sound' => 'll',
                         'badge' => 3
                         );
    $body['acm'] = array("acmvalue");
```
其中`sound`为接收push时的声音，这个可以去查阅官方文档获取。
`badge`就是在收到push时，app图标显示的数字角标

`message`的结构
```
    $message = array(
        "body" => "恭喜您中了一千万",
        "refresh" => "PLAY",
        );
```
`body`显示的是push的标题
`refresh`是自定义的包体，此字段名字也可以自定义。如果在点击push的时候需要跳转到特定的页面，那么可以在这个字段下做文章，添加自定义的业务逻辑
我们app采用了如下结构来响应业务
```
    $message = array(
        "body" => "恭喜您中了一千万",
        "refresh" => $param,
        );
        
    $param = array(
                   "schema" => "xxxxxx",
                   "host" => "baidu.com",
                   "v" => array(
                                "showfg"=>"y",
                                "type" =>"page",
                                "bizparam" =>array(
                                                   "path" => "topicDetail",
                                                   "url" => "http://xxxxxxxxxxx",
                                                   "id" => "967",
                                                   "nearby" => "y",
                                                   "schoolid" => "138",
                                                   "schoolname" => "沈阳音乐学院",
                                                   "channelname" => "心情",
                                                   "attr" => "1",
                                                   "filter" => "all",
                                                   "subject" => "新人报道",
                                                   "username" => "鸡蛋卷饼",
                                                   "uservip" => "1",
                                                   "usericon" => "xxxxxx",
                                                   ),
                                ),
                   );
```
