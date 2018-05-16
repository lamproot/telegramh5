<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<%
Function Post(url,data)
	dim Https 
	set Https=server.createobject("MSXML2.XMLHTTP")
	Https.open "POST",url,false
	Https.setRequestHeader "Content-Type","application/json"
	Https.send data
	if Https.readystate=4 then
		dim objstream 
		set objstream = Server.CreateObject("adodb.stream")
		objstream.Type = 1
		objstream.Mode =3
		objstream.Open
		objstream.Write Https.responseBody
		objstream.Position = 0
		objstream.Type = 2
		objstream.Charset = "utf-8"
		Post = objstream.ReadText
		objstream.Close
		set objstream = nothing
		set https=nothing
	end if
End Function

dim target,post_data
target = "http://intapi.253.com/send/json"
''//手机号码，格式(区号+手机号码)，例如：8615800000000，其中86为中国的区号
post_data="{""account"":""zensen"",""password"":""a."",""mobile"":""8615800000000"",""msg"":""Server.URLEncode(【253云通讯】您好，您的验证码是999999)""}"
response.Write(Post(target,post_data))
''//请自己解析Post(target,post_data)返回的json格式并实现自己的逻辑
%>