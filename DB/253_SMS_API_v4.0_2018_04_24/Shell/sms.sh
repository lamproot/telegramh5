#!/bin/sh
# account and password can be change to what you wanna!
#author ChuangLan
#
#参数的配置 请登录zz.253.com 获取以下API信息 ↓↓↓↓↓↓↓
account=""                       #修改为您的API账号
password=""                      #修改为您的API密码
url="http://xxx/msg/send/json"   # 创蓝发送短信接口URL

#修改为您要发送的手机号
phone="18721755342"            

#设置您要发送的内容：其中“【】”中括号为运营商签名符号，多签名内容前置添加提交
msg="【253云通讯】您的验证码是123456。如非本人操作，请忽略。"
echo "send sms:"

data="{\"account\":\"$account\",\"password\":\"$password\",\"phone\":\"$phone\",\"msg\":\"$msg\",\"report\":\"true\"}"
curl -H "Content-Type:application/json" -X POST --data $data $url

