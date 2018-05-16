#include <iostream>
#include <string>
#include <Winsock2.h>
#include <Winsock2.h>
#pragma comment(lib, "wsock32")

using namespace std;

char* G2U(const char* gb2312)
{
	int len = MultiByteToWideChar(CP_ACP, 0, gb2312, -1, NULL, 0);
	wchar_t* wstr = new wchar_t[len+1];
	memset(wstr, 0, len+1);
	MultiByteToWideChar(CP_ACP, 0, gb2312, -1, wstr, len);
	len = WideCharToMultiByte(CP_UTF8, 0, wstr, -1, NULL, 0, NULL, NULL);
	char* str = new char[len+1];
	memset(str, 0, len+1);
	WideCharToMultiByte(CP_UTF8, 0, wstr, -1, str, len, NULL, NULL);
	if(wstr) delete[] wstr;
	return str;
}


//函数声明
int request(char* hostname, char* api, char* parameters);

//方法调用
int main(int argc, TCHAR* argv[], TCHAR* envp[])
{

	//修改为您的创蓝账号
	char *un = "";
	//修改为您的创蓝密码
	char *pw = "";
	//修改为您要发送的手机号
	char *phone = "";
	//设置您要发送的内容
	char *msg = "您好，您的验证码是1234456，测试DC";
	char *msg3=G2U(msg);//防止中文乱码的

    int nRetCode = 0;
	char params[2048 + 1];
	char *cp = params;

	sprintf(cp,"account=%s&pswd=%s&mobile=%s&msg=%s&needstatus=true&extno=", un, pw, phone, msg3);
	//网络请求
    request("222.73.117.158", "/msg/HttpBatchSendSM", cp);
    return nRetCode;
}

//函数实现
int request(char* hostname, char* api, char* parameters)
{
    WSADATA WsaData;
    WSAStartup(0x0101, &WsaData);

    //初始化socket
    struct hostent* host_addr = gethostbyname(hostname);
    if (host_addr == NULL)
    {
        cout<<"Unable to locate host"<<endl;
        return -103;
    }

    sockaddr_in sin;
    sin.sin_family = AF_INET;
    sin.sin_port = htons((unsigned short)80);
    sin.sin_addr.s_addr = *((int*)*host_addr->h_addr_list);

    int sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1)
    {
        return -100;
    }

    //建立连接
    if (connect(sock, (const struct sockaddr *)&sin, sizeof(sockaddr_in) ) == -1)
    {
        cout<<"connect failed"<<endl;
        return -101;
    }

	//application/json
	//application/x-www-form-urlencoded
	char send_str[4096 + 1], recvline[4096 + 1];
	size_t n;
	sprintf_s(send_str, 2000,
		"POST %s HTTP/1.0\r\n"
		"Host: %s\r\n"
		"Content-type: application/json\r\n"
		"Content-length: %d\r\n\r\n"
		"%s", api, hostname, strlen(parameters), parameters);

    if (send(sock, send_str, strlen(send_str),0) == -1)
    {
        cout<<"send failed"<<endl;
    }

    //获取返回信息
    char recv_str[4096] = {0};
    if (recv(sock, recv_str, sizeof(recv_str), 0) == -1)
    {
        cout<<"recv failed"<<endl;
    }

    cout<<recv_str<<endl;

    WSACleanup( );

    return 0;
}
