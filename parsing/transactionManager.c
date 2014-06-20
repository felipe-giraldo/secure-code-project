/***********************************************************************
 * File: transactionManger.c
 * Authores: Grupo 4
 * Created on 17 de junio de 2014, 22:27
 */

/***********************************************************************
 * Includes
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>
#include <mysql.h>

/***********************************************************************
 * Method for splitting words with a delimiter
 * 
 * Parameters:
 * - p: Pointer to first character of parameter
 * - q: Pointer past the last character of the parameter
 * 
 * Return: a pointer to parameter
 */
char *getParm(char **p, char **q) {

    char *r = 0;
    char *i = *p;
    char *j = *q;
    int l = 0;
    
    while (*j != ',' && *j != 0 && l <= 80) {
       l ++;
       j ++;
    }

    r = (char *)malloc(sizeof(char) * (l + 1));
    strncpy(r, i, l);
    r[l] = 0;
    *p = j + 1;
    *q = *p;
    
    return r;
} // getParm

/***********************************************************************
 * Method for insert rows in the database
 * 
 * Parameters:
 * - date: Date of movement
 * - fromAccount: account movement from
 * - toAccount: account movement to
 * - value: Value to movement
 * - token: Identifier to validate the transaction
 * - type: Type of transaction (CREDIT, DEBIT)
 * - status: state of the transaction (APPROVE, DECLINE, IN VALIDATION)
 * 
 * Retunr: true if satisfied or false in otherwise
 */
int databaseOperation(char *date, char *fromAccount, char *toAccount, double value, char *token, char *type, char *status) {
	
	MYSQL *connector;
	MYSQL_RES *resultSet;
	
	char *server   = "localhost";
	char *user     = "root";
	char *password = "samurai";
	char *database = "parsing";
	connector = mysql_init(NULL);
	
	/* Connect to database */
	if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0)) {
		/* If error, print the error in the standar output */
		fprintf(stderr, "%s\n", mysql_error(connector));
		return 1;
	}

	/* Send the SQL query */
	char *queryString = (char *) malloc(200);
	char *table = "transactions";
	
	/* Query example: INSERT INTO transactions VALUES (null, '2014-06-18', 'CA-1234567890', 'CC-2345678901', 1000.0, 'TOKEN-123456789', 'DEBIT', 'APPROVED') */
	sprintf(queryString, "INSERT INTO %s VALUES (null, '%s', '%s', '%s', %f, '%s', '%s', '%s')", table, date, fromAccount, toAccount, value, token, type, status);
	if (mysql_query(connector, queryString)) {
		fprintf(stderr, "%s\n", mysql_error(connector));
		return 2;
	}
	resultSet = mysql_use_result(connector);

	/* Free resources */
	mysql_free_result(resultSet);
	mysql_close(connector);
	return 0;
} // databaseOperation

/***********************************************************************
 * Begin method
 */
int main (int argc, char *argv[]) {
	
	char linea[81];
    FILE* fichero;
    char **param = (char **)malloc( sizeof( char *) * 7);
    char *p, *q;
    int  i = 0;
    fichero = fopen("movements.txt", "rt");
    if (fichero == NULL) {
		printf("Archivo inexistente!\n");
		exit(1);
    }
   
    while (!feof(fichero)) {
        fgets(linea, 80, fichero);

        p = q = linea;
        for (i = 0; i < 7; i ++) {
			param[i] = (char *)getParm(&p, &q);
        }
        
      // Aquí validar todos  los parámetros y proteger contra SQLi
        
		databaseOperation(param[0], param[1], param[2], atof(param[3]), param[4], param[5], param[6]);
		
        for (i = 0; i < 4; i ++)
            free(param[i]);
     }
     fclose(fichero);
     return(0);
} // main
