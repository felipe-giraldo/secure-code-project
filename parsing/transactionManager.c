/***********************************************************************
 * File: transactionManger.c
 * Authores: Grupo 4
 * Created on 17 de junio de 2014, 22:27
 */

/***********************************************************************
 * Includes
 */
#define _XOPEN_SOURCE
#include <ctype.h>
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
 * Estado al final:  p y q point to the starting position of next parameter,
 * or to the end of the string
 *
 */
char *getParms(char **p, char **q) {

   char *r = 0;   // r: Result param
   char *i = *p;  // i: starting position
   char *j = *q;  // j: running end position
   int   l = 0;   // l: number of chars in param

   // trim  leading whitespace
   while ( *j == ' ' || *j == '\t' || *j == '\n' )
      j++;

   // if input not exhausted
   if ( *j != 0)
   {
      // search for the ending ','  or endOfString
      while (*j != ',' && *j != 0 && l <= 80)
      {
         l ++;
         j ++;
      }
      
      // Hit end of parm => get it
      r = (char *) malloc(sizeof(char) * (l + 1));
      strncpy(r, i, l);
      r[l] = 0;

      // Update pointers for the next parm
      *p = (*j == 0)? j : j + 1;
      *q = *p;
   }

   return r;
} // getParms

/***********************************************************************
 * Method for get the date time in string format
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
int insertTransaction(char *fromAccount, char *toAccount, char *value, char *token, int type) {

   MYSQL *connector;
   MYSQL_RES *resultSet;

   char *server   = "localhost";
   char *user     = "advlogin";
   char *password = "Hard+20.";
   char *database = "advlogin";
   connector = mysql_init(NULL);

   // Connect to database
   if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0))
   {
      // If error, print the error in the standar output
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 1;
   }

   // Send the SQL query
   char *queryString = (char *) malloc(200);
   char *table = "transactions";

   // Query example: INSERT INTO transactions VALUES (null, '1234567890', '2345678901', 1000, 'TOKEN-123456789', 2014-06-21, 99, 1)
   // ***  Pregunta: Por qué no se usa la fecha de transacción que viene en el movimiento ?   ***
   // ***  Pregunta: Por qué no utilizar un tipo datetime para la fecha en la tabla  MySQL ?  ***
   // ***  Pregunta: Qué significa el transaction state ?  Por  qué está quemado en 99 ?      ***
   sprintf(queryString, "INSERT INTO %s VALUES (null, %s, %s, %d, %s, '%s', %d, %d)",
           table, fromAccount, toAccount, atoi(value), token, getDate(), 99, type);
   if (mysql_query(connector, queryString))
   {
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
 * Verifica si un string es numerico
 * @param value String a verificar
 * 
 * @return int  1 si es numerico, 0 si no lo es
 */
int isNumber ( char *value)
{
   char *p = value;            // running string ptr
   int itIs = (*p == 0)? 0: 1; // Assume it is numeric
   int i = 0;                  // char count
   for (i=0; i < 10 && *p; i++, p++)
   {
      if ( !isdigit( *p))
      {
         itIs = 0;
         break;
      }
   }
   return itIs;

} // isNumber

/**********************************************************************
  returns the utc timezone offset
  (e.g. -8 hours for PST)
*/
int get_utc_offset() {

  time_t zero = 24*60*60L;
  struct tm * timeptr;
  int gmtime_hours;

  /* get the local time for Jan 2, 1900 00:00 UTC */
  timeptr = localtime( &zero );
  gmtime_hours = timeptr->tm_hour;

  /* if the local time is the "day before" the UTC, subtract 24 hours
    from the hours to get the UTC offset */
  if( timeptr->tm_mday < 2 )
    gmtime_hours -= 24;

  return gmtime_hours;

}// get_utc_offset

/********************************************************************
  the utc analogue of mktime,
  (much like timegm on some systems)
*/
time_t tm_to_time_t_utc( struct tm * timeptr ) {

  /* gets the epoch time relative to the local time zone,
  and then adds the appropriate number of seconds to make it UTC */
  return mktime( timeptr ) + get_utc_offset() * 3600;

}// tm_to_time_t_utc


/**********************************************************************
   Valida los parametros de una transaccion
   param - Lista de parametros de la transaccion
   [0] Fecha en formato AAAA-mm-DD
   [1] Codigo  de cuenta-desde
   [2] Codigo  de cuenta-hacia
   [3] Valor de la transaccion
   [4] Token utilizado
   [5] Tipo de transaccion
   [6] APPROVED/ DECLINED
*/
char *validate( char** param){
   char        *msg = 0;    
   struct  tm  trans_date;
   time_t      now;
   time_t      trans_time;
   char        *p;


   // Ejemplo de una transaccion
   // 2014-06-17,CA-1234567890,CC-2345678901,1000,TOKEN-123456789,DEBIT,APPROVED

   // Fecha
   // La fecha debe estar bien formada
   // La fecha no puede ser futura
   // La fecha no puede ser anterior a 1 ano
   memset(&trans_date, 0, sizeof(struct tm));
   p = strptime(param[0], "%Y-%m-%d", &trans_date);

   if ( p == 0)
      msg = "Fecha de transaccion invalida";
   else
   {
	  now   = time(0);
	  trans_time = tm_to_time_t_utc( &trans_date);
      double seconds = difftime(trans_time, now);
      if (seconds > 0)
         msg ="Fecha de transaccion en el futuro";
      else 
      {
         seconds = difftime(now, trans_time);
         if (seconds > 365*24*60*60)
            msg = "Fecha de transaccion anterior a un anio";
      }
   }

   // Cuenta-desde
   // La cuenta-desde debe existir en el maestro de cuentas
   if ( msg == 0 && 1)
   {
      /*
            sql::Connection *con;                                                    
            sql::Statement *stmt;                                                    
            sql::ResultSet *res;                                                     
            // ...                                                                   
            stmt = con->createStatement();                                           
            // ...                                                                   
            res = stmt->executeQuery("SELECT id, label FROM test ORDER BY id ASC");  
            while (res->next()) {                                                    
                // You can use either numeric offsets...                                
                cout << "id = " << res->getInt(1); // getInt(1) returns the first column
                // ... or column names for accessing results.                           
                // The latter is recommended.                                           
                cout << ", label = '" << res->getString("label") << "'" << endl;        
            }
            delete res;
            delete stmt;
            delete con;
      */
	   
   }

   // Cuenta-hacia
   // La cuenta-hacia debe existir en el maestro de cuentas
   // La cuenta-hacia debe ser diferente de la cuenta-desde
   if ( msg == 0 && 1)
   {
      int len = strlen(param[1]);
      if (strncmp(param[1], param[2], len) == 0)
         msg = "Cuenta-desde no puede ser igual a cuenta-hacia";
   }

   // Valor
   // El valor debe ser numerico de menos de 10 cifras
   // El valor debe ser un numero positivo
   if (msg == 0 && strlen(param[3]) >= 10)
      msg = "Valor no puede tener más  de 9 dígitos";

   if (! isNumber(param[3]))
      msg = "Valor debe ser numérico y menor de 10 dígitos";
   

   // Token
   // El token debe estar habilitado
   if ( msg == 0 && 1)
   {
   }

   // Tipo de transaccion
   // Debe ser DEBIT o CREDIT
   if ( msg == 0 && strncmp(param[5], "DEBIT", 5) != 0 && strncmp(param[5], "CREDIT", 6) != 0)
      msg = "Tipo de transaccion invalido. Debe ser    DEBIT /   CREDIT";

   // Approved
   // Debe ser APPROVED o DECLINED
   if ( msg == 0 && strncmp(param[6], "APPROVED", 8) != 0 && strncmp(param[5], "DECLINED", 8) != 0)
      msg = "Aprobacion invalida. Debe ser    APPROVED /   DECLINED";

   return msg;
} // validate


/***********************************************************************
 * Begin method
 */
int main (int argc, char *argv[]) {

   char line[81];     // Linea de movimientos
   FILE* file;        // Archivo de movimientos
   char **param = (char **)malloc( sizeof( char *) * 7); // Parametros en la linea
   char *msg;         // Mensaje de error
   char *p, *q;       // Running pointers sobre los parametros
   int  i = 0;        // Running index de los parametros
   int  nM = 0;       // Número de transacciones en el archivo de movimientos

   // Abra el archivo de transacciones
   file = fopen("movements.txt", "rt");
   if (file == NULL)
   {
      printf("Archivo inexistente!\n");
      exit(1);
   }

   // Para cada transaccion en el batch de transacciones
   while (!feof(file))
   {
      fgets(line, 80, file);
      printf("%s", line);
      nM++;

      // Obtenga los componentes de la transaccion
      p = q = line;
      for (i = 0; i < 7; i ++)
      {
         if ((p - line) > 80) {
            printf("*** Error: Linea %d agotada, Falta parametro  %d", nM, i);
            break;
         }

         param[i] = (char *)getParms(&p, &q);
         printf("%s\n", param[i]);
         if (param[i] == 0) {
            printf("*** Error: Falta parametro  %d", i);
            break;
         }
      }
      if (i < 7)
         continue;

      // Valide cada componente
      msg = validate( param);
      if ( msg != 0) {
         printf( "*** Error: %s\n", msg);
         continue;
      }

      // Actualice la base de datos con la nueva transaccion
      int type = ( strncmp ( "CREDIT", param[3], 6) == 0)? 1 : 2;   // Credit = 1,  Debit = 2
      insertTransaction(param[1], param[2], param[3], param[4], type);

      // Libere la memoria alocada para cada transacccion
      for (i = 0; i < 7; i ++)
         free(param[i]);
   }
   free(param);
   fclose(file);
   return(0);
} // main

