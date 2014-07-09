#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <sys/types.h>
#include <time.h> 

void init_serv_addr(struct sockaddr_in *serv_addr)
{
	memset(serv_addr, '0', sizeof(struct sockaddr_in));
    serv_addr->sin_family = AF_INET;
    serv_addr->sin_addr.s_addr = htonl(INADDR_ANY);
    serv_addr->sin_port = htons(5000); 
}

int checkPassword(char *buf)
{
	if (strcmp(buf, "qwerty") == 0)
	  return 1;
}

int main(int argc, char *argv[])
{
    int listenfd = 0, connfd = 0;
    struct sockaddr_in serv_addr; 
    
    init_serv_addr(&serv_addr);
    
    char sendBuff[1025];
    memset(sendBuff, '0', sizeof(sendBuff)); 
    time_t ticks; 

    
	listenfd = socket(AF_INET, SOCK_STREAM, 0); //TCP
    
    if (listenfd == -1) 
    {
	  printf("Cannot create socket\n");
	  return 1;
    }
    
    int bound = bind(listenfd, (struct sockaddr*)&serv_addr, sizeof(serv_addr)); 
    
    if (bound == -1) 
    {
	  printf("Cannot bind socket\n");
	  return 1;
    }

    int listening = listen(listenfd, 10); 
    
    if (listening == -1) 
    {
	  printf("Cannot listen on socket\n");
	  return 1;
    }
    
    //while(1)
    {
        connfd = accept(listenfd, (struct sockaddr*)NULL, NULL); 
		
        snprintf(sendBuff, sizeof(sendBuff), "Welcome to the login page\nPlease provide the correct password: ");
        write(connfd, sendBuff, strlen(sendBuff)); 

		char c;
		ssize_t bytes_read = 0;
		int i = 0;
		do {
			bytes_read = read(connfd, &c, 1);
			sendBuff[i++] = c;
		} while ((bytes_read > 0) || (c == '\n') || (c == NULL));
		sendBuff[i-1] = '\0';
		
		if (checkPassword(sendBuff) == 1)
		  printf("Password is correct!\n");
		else
		  printf("Incorrect password: %s\n", sendBuff);
		
        close(connfd);
        sleep(1);
     }
}
