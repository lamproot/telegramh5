<?php
include './System/curl.class.php';
date_default_timezone_set('UTC');
// function callback($response, $info, $error, $request)
// {
//     echo 'response:<br>';
//     print_r($response);
//
//     echo '<br>' . date("Y-m-d H:i:s") . '&nbsp;&nbsp;&nbsp;<br>';
//     echo '<br>' . str_repeat("-", 100) . '<br>';
// }

$USER_COOKIE = (!empty($_REQUEST['cookie'])) ? $_REQUEST['cookie'] : file_get_contents("cookie.txt");

$curl = new Curl ();

$data = array(
    array(
        'url' => 'http://dyactive2.vip.xunlei.com/com_sign/?game=qmr&type=rec_gametime&referfrom=&rt=0.42521539455332336', //秦美人
        'method' => 'POST',
        'post_data' => '',
        'header' => null,
        'options' => array(
            CURLOPT_REFERER => "http://niu.xunlei.com/entergame/?gameNo=qmr&fenQuNum=3",
            CURLOPT_COOKIE => $USER_COOKIE,
        )
    ),
    array(
        'url' => 'http://dyactive2.vip.xunlei.com/com_sign/?game=sq&type=rec_gametime&referfrom=&rt=0.42521539455332336', //神曲
        'method' => 'POST',
        'post_data' => '',
        'header' => null,
        'options' => array(
            CURLOPT_REFERER => "http://niu.xunlei.com/entergame/?gameNo=sq&fenQuNum=41",
            CURLOPT_COOKIE => $USER_COOKIE,
        )
    ),
    array(
        'url' => 'http://dyactive2.vip.xunlei.com/com_sign/?game=frxz&type=rec_gametime&referfrom=&rt=0.42521539455332336', //凡人修真
        'method' => 'POST',
        'post_data' => '',
        'header' => null,
        'options' => array(
            CURLOPT_REFERER => "http://niu.xunlei.com/entergame/?gameNo=frxz&fenQuNum=3",
            CURLOPT_COOKIE => $USER_COOKIE,
        )
    ),
    array(
        'url' => 'http://dyactive2.vip.xunlei.com/com_sign/?game=smxj&type=rec_gametime&referfrom=&rt=0.42521539455332336', //神魔仙界
        'method' => 'POST',
        'post_data' => '',
        'header' => null,
        'options' => array(
            CURLOPT_REFERER => "http://niu.xunlei.com/entergame/?gameNo=smxj&fenQuNum=2",
            CURLOPT_COOKIE => $USER_COOKIE,
        )
    ),
    array(
        'url' => 'http://dyactive2.vip.xunlei.com/com_sign/?game=qsqy&type=rec_gametime&referfrom=&rt=0.42521539455332336', //倾世情缘
        'method' => 'POST',
        'post_data' => '',
        'header' => null,
        'options' => array(
            CURLOPT_REFERER => "http://niu.xunlei.com/entergame/?gameNo=qsqy&fenQuNum=11",
            CURLOPT_COOKIE => $USER_COOKIE,
        )
    ),
);

foreach ($data as $val) {
    $request = new Curl_request ($val ['url'], $val ['method'], $val ['post_data'], $val ['header'], $val ['options']);
    $curl->add($request);
}
//
echo $curl->execute();
// echo $curl->display_errors();
