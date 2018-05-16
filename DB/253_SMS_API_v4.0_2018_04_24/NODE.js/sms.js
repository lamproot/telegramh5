var http = require('http');

//参数的配置 请登录zz.253.com 获取以下API信息 ↓↓↓↓↓↓↓
// API账号
var account="";
// API密码
var password="";
//接口域名
var sms_host = ''; 

//手机号码
var phone="";

// 设置您要发送的内容：其中“【】”中括号为运营商签名符号，多签名内容前置添加提交
var msg="【253云通讯】您的验证码是123456。如非本人操作，请忽略。";



// 普通短信发送URL
var send_sms_uri = '/msg/send/json';

//余额查询的URL
var query_balance_uri = '/msg/balance/json';

send_sms(send_sms_uri,account,password,phone,msg);

query_blance(query_balance_uri,account,password);

// 发送短信方法
function send_sms(uri,account,password,phone,msg){
	
    var post_data = { // 这是需要提交的数据 
    'account': account,   
    'password': password, 
    'phone':phone,
    'msg':msg,
    'report':'false',
    };  
    var content =  JSON.stringify(post_data);  
    post(uri,content,sms_host);
	
}
  
// 查询余额方法
function query_blance(uri,content,host){
	
    var post_data = { // 这是需要提交的数据 
    'account': account,   
    'password': password, 
    };  
    var content = JSON.stringify(post_data);  
    post(uri,content,sms_host);
}
  
function post(uri,content,host){
	var options = {  
        hostname: host,
        port: 80,  
        path: uri,  
        method: 'POST',  
        headers: {  
            'Content-Type': 'application/json; charset=UTF-8', 
        }  
    };
    var req = http.request(options, function (res) {  
        console.log('STATUS: ' + res.statusCode);  
        
        res.setEncoding('utf8');  
        res.on('data', function (chunk) {  
            console.log('BODY: ' + chunk);  
        });  
    }); 
   
    req.write(content);  
  
    req.end();   
} 


