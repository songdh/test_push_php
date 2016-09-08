<?php
    // Put your device token here (without spaces):
    $deviceToken = 'your device token';

//    $deviceToken = $argv[1];
    
    // Put your private key's passphrase here:
    $passphrase = 'your passphrase';
    
    // Put your alert message here:
    $message = 'My first push notification!';
    
    ////////////////////////////////////////////////////////////////////////////////w
    
    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
    
    // Open a connection to the APNS server
    //app测试push地址
    $fp = stream_socket_client(
                               'ssl://gateway.sandbox.push.apple.com:2195', $err,
                               $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    //app正式push地址，上线后修改成下面的地址
//    $fp = stream_socket_client(
    //                           'ssl://gateway.push.apple.com:2195', $err,
  //                             $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    
    
    if (!$fp){
        exit("Failed to connect: $err $errstr" . PHP_EOL);
    }
    
    echo 'Connected to APNS' . PHP_EOL;

    $message = array(
        "body" => "恭喜您中了一千万",
        "refresh" => "PLAY",
        );

    // Create the payload body
    $body['aps'] = array(
                         'alert' => $message,
                         'sound' => 'll',
                         'badge' => 3
                         );
    $body['acm'] = array("acmvalue");
    
    // Encode the payload as JSON
    $payload = json_encode($body);
    //$payload = json_encode($message);
    
    // Build the binary notification
    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;  
    
    // Send it to the server  
    $result = fwrite($fp, $msg, strlen($msg));  
    
    if (!$result)  
    echo 'Message not delivered' . PHP_EOL;  
    else  
    echo 'Message successfully delivered' . PHP_EOL;  
    
    // Close the connection to the server  
    fclose($fp);
?>
