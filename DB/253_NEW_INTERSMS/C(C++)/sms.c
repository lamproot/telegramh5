#include <arpa/inet.h>
#include <assert.h>
#include <errno.h>
#include <netinet/in.h>
#include <signal.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/wait.h>
#include <netdb.h>
//#include <accountistd.h>

#define SA struct sockaddr
#define MAXLINE 4096
#define MAXSUB  2000
#define MAXPARAM 2048

#define LISTENQ         1024


//线上线下接口宏开关

#define ONLINE  


extern int h_errno;

int sockfd;



char *hostname = "118.178.16.150";
char *send_sms_uri = "/send/json";
char *query_balance_uri = "/balance/json";



/**
 * * 发http post请求
 * */
ssize_t http_post(char *page, char *poststr)
{
    char sendline[MAXLINE + 1], recvline[MAXLINE + 1];
    ssize_t n;
	snprintf(sendline, MAXSUB,
		"POST %s HTTP/1.1\r\n"
		"Host: intapi.253.com\r\n"
		"Content-type: application/json\r\n"
		"Content-length: %zu\r\n\r\n"
		"%s", page, strlen(poststr), poststr);
		//, page, poststr);
    write(sockfd, sendline, strlen(sendline));
	printf("\n%s", sendline);
	printf("\n--------------------------\n");
    while ((n = read(sockfd, recvline, MAXLINE)) > 0) {
        recvline[n] = '\0';
        printf("%s\n", recvline);
    }
    return n;
}

/**
 * * 查账户余额
 * */
ssize_t get_balance(char *account, char *password)
{
    char params[MAXPARAM + 1];
    char *cp = params;

	sprintf(cp,"{\"account\":\"%s\",\"password\":\"%s\"}", account, password);

    return http_post(query_balance_uri, cp);
}

/**
 * * 发送短信
 * */
ssize_t send_sms(char *account, char *password, char *mobile, char *msg)
{
    char params[MAXPARAM + 1];
    char *cp = params;

	sprintf(cp,"{\"account\":\"%s\",\"password\":\"%s\",\"mobile\":\"%s\",\"msg\":\"%s\"}", account, password, mobile, msg);    

    return http_post(send_sms_uri, cp);
}

int main(void)
{
    struct sockaddr_in servaddr;
    char str[50];

    //建立socket连接
    sockfd = socket(AF_INET, SOCK_STREAM, 0);
    bzero(&servaddr, sizeof(servaddr));
    servaddr.sin_addr.s_addr = inet_addr(hostname);
    servaddr.sin_family = AF_INET;
    servaddr.sin_port = htons(80);
    inet_pton(AF_INET, str, &servaddr.sin_addr);
    connect(sockfd, (SA *) & servaddr, sizeof(servaddr));

	char *account = "";
	char *password = "a.123456987";
	//手机号码，格式(区号+手机号码)，例如：8615800000000，其中86为中国的区号
	char *mobile = "8615800000000";
	//必须带签名
	char *msg = "【253云通讯】您的验证码是123400";

    //get_balance(account, password);
    send_sms(account, password, mobile, msg);
    close(sockfd);
    exit(0);
}


















