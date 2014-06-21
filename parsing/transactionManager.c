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
#include <time.h>
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

    r = (char *) malloc(sizeof(char) * (l + 1));
    strncpy(r, i, l);
    r[l] = 0;
    *p = j + 1;
    *q = *p;

    return r;
} // getParm

/***********************************************************************
 * Method for get the date time
 *
 * Retunr: the date of the day
 */
char *getDate() {

    time_t t;
    struct tm *tm;
    char *date = (char *) malloc(100);

    t = time(NULL);
    tm = localtime(&t);
    strftime(date, 100, "%Y-%m-%d", tm);

    return date;
}

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
 * Return:
 * - 0 if insert
 * - 1 if the server conection is not successful
 * - 2 if the query return and error
 */
int insertTransaction(char *fromAccount, char *toAccount, char *value, char *token, char *type) {

	MYSQL *connector;
	MYSQL_RES *resultSet;

	char *server   = "localhost";
	char *user     = "advlogin";
	char *password = "Hard+20.";
	char *database = "advlogin";
	connector = mysql_init(NULL);

	// Connect to database
	if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0)) {
		// If error, print the error in the standar output
		fprintf(stderr, "%s\n", mysql_error(connector));
		return 1;
	}

	// Send the SQL query
	char *queryString = (char *) malloc(200);
	char *table = "transactions";

	// Query example: INSERT INTO transactions VALUES (null, '1234567890', '2345678901', 1000, 'TOKEN-123456789', 2014-06-21, 99, 1)
	sprintf(queryString, "INSERT INTO %s VALUES (null, %s, %s, %d, %s, '%s', %d, %d)",
         table, fromAccount, toAccount, atoi(value), token, getDate(), 99, atoi(type));
	if (mysql_query(connector, queryString)) {
		fprintf(stderr, "%s\n", mysql_error(connector));
		return 2;
	}
	resultSet = mysql_use_result(connector);

	// Free resources
	mysql_free_result(resultSet);
	mysql_close(connector);
	return 0;
} // insertTransaction

/***********************************************************************
 * Begin method
 */
int main (int argc, char *argv[]) {

    char line[81];
    FILE* file;
    char **param = (char **)malloc( sizeof( char *) * 5);
    char *p, *q;
    int  i = 0;
    file = fopen("movements.txt", "rt");
    if (file == NULL) {
        printf("Archivo inexistente!\n");
        exit(1);
    }

    while (!feof(file)) {
        fgets(line, 80, file);
        printf("%s", line);

        p = q = line;
        for (i = 0; i < 5; i ++) {
            param[i] = (char *)getParm(&p, &q);
        }

        // Aquí validar todos  los parámetros y proteger contra SQLi
		insertTransaction(param[0], param[1], param[2], param[3], param[4]);
		
        for (i = 0; i < 5; i ++)
            printf("%s\n", param[i]);

        for (i = 0; i < 5; i ++)
            free(param[i]);
    }
    fclose(file);
    return(0);
} // main
