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
 * Class parameters
 */
char *server   = "localhost";
char *user     = "advlogin";
char *password = "Hard+20.";
char *database = "advlogin";

/***********************************************************************
 * Method for splitting words with a delimiter
 *
 * Parameters:
 * - p: Pointer to first character of parameter
 * - q: Pointer past the last character of the parameter
 *
 * Return: a pointer to parameter
 */
char *getParms(char **p, char **q) {

   char *r = 0;   // r: Result param
   char *i = *p;  // i: starting position
   char *j = *q;  // j: running end position
   int l = 0;     // l: number of chars in param

   if ( *j != 0)
   {
      while (*j != ',' && *j != 0 && l <= 80)
      {
         l ++;
         j ++;
      }

      r = (char *) malloc(sizeof(char) * (l + 1));
      strncpy(r, i, l);
      r[l] = 0;
      *p = (*j == 0)? j : j + 1;
      *q = *p;
   }

   return r;
} // getParms


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
} // getDate

/***********************************************************************
 * Method for validate the params for the transaction
 * 
 * Parameters: list of parameters of transaction
 * - [0] Fecha en formato AAAA-mm-DD
 * - [1] Codigo  de cuenta-desde
 * - [2] Codigo  de cuenta-hacia
 * - [3] Valor de la transaccion
 * - [4] Token utilizado
 * - [5] Tipo de transaccion
 * - [6] APPROVED/ DECLINED
 */
char *validate(char** param){

   char    *msg = 0;    
   time_t  trans_date;
   time_t  today;
   char    *p;
   int     ok = 1;

   // Ejemplo de una transaccion
   // 2014-06-17,CA-1234567890,CC-2345678901,1000,TOKEN-123456789,DEBIT,APPROVED

   // Fecha
   // La fecha debe estar bien formada
   // La fecha no puede ser futura
   // La fecha no puede ser anterior a 1 aÃ±o
   memset(&trans_date, 0, sizeof(struct tm));
   p = strptime(param[0], "%Y-%m-%d", &trans_date);

   if ( p == 0)
   {
      msg = "Fecha de transaccion invalida";
      ok = 0;
   }
   else
   {
      time ( &today );
      double seconds = difftime(trans_date, today);
      if (seconds > 0)
      {
         msg ="Fecha de transaccion en el futuro";
         ok = 0;
      }
      else 
      {
         seconds = difftime(today, trans_date);
         if (seconds > 365*24*60*60)
         {
            msg = "Fecha de transaccion anterior a un anio";
            ok = 0;
         }
      }
   }

   // Cuenta-desde
   // La cuenta-desde debe existir en el maestro de cuentas
   if (ok == 1 && 1)
   {
   }

   // Cuenta-hacia
   // La cuenta-hacia debe existir en el maestro de cuentas
   // La cuenta-hacia debe ser diferente de la cuenta-desde
   if (ok == 1 && 1)
   {
      int len = strlen(param[1]);
      if (strncmp(param[1], param[2], len) == 0){
         msg = "Cuenta-desde no puede ser igual a cuenta-hacia";
         ok = 0;
      }
   }

   // Token
   // El token debe estar habilitado
   if (ok == 1 && 1)
   {
   }
   
   // El valor no puede ser negativo ni cero
   if (ok == 1 && atof(param[3] <= 0))
   {
	   msg = "El valor de la transaccion debe ser un numero positivo";
	   ok = 0;
   }

   // Tipo de transaccion
   // Debe ser DEBIT o CREDIT
   if ( ok == 1 && strncmp(param[5], "DEBIT", 5) != 0 && strncmp(param[5], "CREDIT", 6) != 0)
   {
      msg = "Tipo de transaccion invalido. Debe ser    DEBIT /   CREDIT";
      ok = 0;
   }

   // Approved
   // Debe ser APPROVED o DECLINED
   if ( ok == 1 && strncmp(param[6], "APPROVED", 8) != 0 && strncmp(param[5], "DECLINED", 8) != 0)
   {
      msg = "Aprobacion invalida. Debe ser    APPROVED /   DECLINED";
      ok = 0;
   }

   return msg;
} // validate


/***********************************************************************
 * Method for insert rows in the database
 *
 * Parameters:
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
	sprintf(queryString, "INSERT INTO %s VALUES (null, %d, %d, %d, %s, '%d', %d, %d)",
         table, atoi(fromAccount), atoi(toAccount), atoi(value), token, 1401596496, 99, atoi(type));
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
 * Method for search if a token is used
 *
 * Parameters:
 * - token: Identifier to validate the transaction
 *
 * Return:
 * - 0 if insert
 * - 1 if the server conection is not successful
 * - 2 if the query return and error
 */
int searchTokenIfUser(char *token) {

	MYSQL *connector;
	MYSQL_RES *resultSet;

	connector = mysql_init(NULL);

	// Connect to database
	if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0)) {
		// If error, print the error in the standar output
		fprintf(stderr, "%s\n", mysql_error(connector));
		return 1;
	}

	// Send the SQL query
	char *queryString = (char *) malloc(200);
	char *column = "used";
	char *table = "user_token";

	sprintf(queryString, "SELECT %s FROM %s WHERE token_id = '%s'", column, table, token);
	//printf(queryString);
	if (mysql_query(connector, queryString)) {
		fprintf(stderr, "%s\n", mysql_error(connector));
		return 2;
	}
	resultSet = mysql_use_result(connector);
	//printf("\nToken %s: %s", token, resultSet);

	// Free resources
	mysql_free_result(resultSet);
	mysql_close(connector);
	return 0;
} // searchTokenIfUser

/***********************************************************************
 * Begin method
 */
int main (int argc, char *argv[]) {

    char line[81];
    FILE* file;
    char **param = (char **)malloc( sizeof( char *) * 5);
    char *p, *q;
    int  i = 0;
    file = fopen("/home/samurai/Developer/secure-code-project/parsing/movements.txt", "rt");
    if (file == NULL) {
        printf("Archivo inexistente!\n");
        exit(1);
    }

	//searchTokenIfUser("2G2ngqt8sjYNtJ8");
	
    while (!feof(file)) {
        fgets(line, 80, file);
        printf("%s", line);

        p = q = line;
        for (i = 0; i < 5; i ++) {
            param[i] = (char *)getParms(&p, &q);
            printf("%s\n", param[i]);
            if (param[i] == 0) {
				printf("*** Error: falta parametro %d", i);
				break;
			}
        }
        
        if (i < 5)
			continue;

        // Aquí validar todos  los parámetros y proteger contra SQLi
//        char *msg = validate(param);
//        if (msg != 0) {
//			printf("*** Error: %s\n", msg);
//			continue;
//		}
        
        insertTransaction(param[0], param[1], param[2], param[3], param[4]);

//        for (i = 0; i < 5; i ++)
//            printf("%s\n", param[i]);

        for (i = 0; i < 5; i ++)
            free(param[i]);
    }
    free(param);
    fclose(file);
    return(0);
} // main
