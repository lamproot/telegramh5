# 说明：
# 以下代码只是为了方便客户测试而提供的示例代码，客户可以根据自己的需要另行编写
# 该代码仅供学习和研究接口使用，只是提供了一个参考

require 'net/http'  
require 'uri'  
require 'json'  

#登录zz.253.com查看API接口信息
params = {  
    #创蓝API账号
    "account" => "",  
	#创蓝API密码  
    "password" => "a.123456", 
    #手机号码	
    "phone" => "18721755342",  
    # //设置您要发送的内容：其中“【】”中括号为运营商签名符号，多签名内容前置添加提交
    "msg" =>URI::escape("【253云通讯】您好，您的验证码是999999") 
}.to_json  
  
def send_data(url,data)  
    url = URI.parse(url)  
    req = Net::HTTP::Post.new(url.path,{'Content-Type' => 'application/json'})  
    req.body = data  
    res = Net::HTTP.new(url.host,url.port).start{|http| http.request(req)}  
  
    puts res.body                                                                                                  
end  
# 地址不完整，请登录zz.253.com获取API接口信息 
send_data('http://xxx/msg/send/json',params) 