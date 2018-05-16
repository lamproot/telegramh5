#!/bin/sh
# account and password can be change to what you wanna!
#author ChuangLan
#修改为您的account
account=""
#修改为您的pw
password="a.123456"
#手机号码，格式(区号+手机号码)，例如：8615800000000，其中86为中国的区号
mobile="8615800000000"
#设置您要发送的内容
msg="【253云通讯】您的验证码是123456。如非本人操作，请忽略。"
echo "send sms:"

url="http://intapi.253.com/send/json"
data="{\"account\":\"$account\",\"password\":\"$password\",\"mobile\":\"$mobile\",\"msg\":\"$msg\"}"
curl -H "Content-Type:application/json" -X POST --data $data $url

