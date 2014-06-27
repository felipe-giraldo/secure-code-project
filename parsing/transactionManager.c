/***********************************************************************
 * File: transactionManger.c
 * Authores: Grupo 4
 * Created on 17 de junio de 2014, 22:27
 */

/***********************************************************************
 *  Defines and Includes
 */
#define DUMP(s, i, buf, sz)  {printf(s);                   \
                              for (i = 0; i < (sz);i++)    \
                                  printf("%02x ", buf[i]); \
                              printf("\n");}

#define _XOPEN_SOURCE
#include <ctype.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <assert.h>
#include <mysql.h>
#include "aes256.h"


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
   if ( !*j)
   {
      // search for the ending ','  or endOfString
      while (*j != ',' && *j != 0 && l <= 80)
      {
         l++;
         j++;
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
 * Return: the date of the day
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
int insertTransaction( MYSQL *connector, char *fromAccount, char *toAccount, char *value, char *token, int type) {

   MYSQL_RES *resultSet;

   // Send the SQL query
   char *queryString = (char *) malloc(200);
   char *table = "transactions";

   // Query example: INSERT INTO transactions VALUES (null, '1234567890', '2345678901', 1000, 'TOKEN-123456789', 2014-06-21, 99, 1)
   sprintf(queryString, "INSERT INTO %s VALUES (null, %s, %s, %d, %s, '%s', %d, %d)",
           table, fromAccount, toAccount, atoi(value), token, getDate(), 99, type);

   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 2;
   }
   resultSet = mysql_use_result(connector);

   // Marque el token como usado
   sprintf(queryString, "UPDATE user_token set used=1 WHERE token_id= %s", token);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 2;
   }

   // Free resources
   mysql_free_result(resultSet);
   return 0;
   
} // insertTransaction

/***********************************************************************
 * Verifica si un string es numerico
 * @param value String a verificar
 * 
 * @return int  1 si es numerico, 0 si no lo es 
 *         null strings se consideran no numericos
 *         strings de más de 15  digitos se consideran no numericos
 */
int isNumber ( char *value)
{
   char  *p  = value;   // running string ptr
   int empty = !*p;     // check field not empty
   int     i = 0;       // char count


   while (*p && i < 15 && isdigit(*p)  )
   {  
      i++; 
      p++;
   }

   return !empty && i < 15 && !*p;

} // isNumber

/***********************************************************************
 * Verifica si un string es alfanumerico
 * @param value String a verificar
 * 
 * @return int  1 si es alfanumerico, 0 si no lo es. 
 *         Null strings se consideran no alphanumeric 
 */
int isAlphaNumeric ( char *value)
{
   char   *p = value;  // running string ptr
   int empty = !*p;    // check field is not empty

   while (*p && (isalpha(*p) || isdigit( *p)) )
      p++;

   return ( !empty && !*p);

} // isAlphaNumeric

/********************************************************************** 
  Verifica si una cuenta existe en el maestro de cuentas
 
  connector - MYSQL db connector
  accCode - Código de la cuenta  a verificar
  return   0 si la cuenta no existe, 1 si la cuenta esta registrada
 */
int accountExists (MYSQL *connector,  char *accNumber)
{
   int  exists = 0;
   MYSQL_RES *resultSet;
   char *queryString = (char *)malloc( 200 * sizeof(char));

   int cuenta = atoi( accNumber);
   memset( queryString, 0, 200 * sizeof(char));
   sprintf(queryString, "SELECT  id_account FROM account WHERE id_account = %d", cuenta);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 0;
   }

   resultSet = mysql_store_result(connector);
   exists = resultSet != 0 && mysql_num_rows( resultSet) != 0;
   mysql_free_result(resultSet);
   return exists;

} // accountExists

/********************************************************************** 
  Verifica si un token ha sido utilizado
 
  connector - MYSQL db connector
  token     - Código de la cuenta  a verificar
  return   0 si el token es valido, 1 si el token es invalido
 */
int isTokenValid (MYSQL *connector,  char *token_id)
{
   int valid = 0;
   MYSQL_RES *resultSet;
   char *queryString = (char *)malloc( 200 * sizeof(char));
   memset( queryString, 0, 200 * sizeof(char));

   sprintf(queryString, "SELECT  token_id, user_id, used  FROM user_token WHERE token_id = %s", token_id);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 0;
   }

   resultSet = mysql_store_result(connector);
   if (  resultSet != 0 && mysql_num_rows( resultSet) != 0)
   {   
      MYSQL_ROW row = mysql_fetch_row ( resultSet);
      valid  = atoi(row[2]);
   }

   mysql_free_result(resultSet);
   return valid;

} // isTokenValid

/**********************************************************************
Get the AES key based on the user id

connector - Conexion a la base de datos
usuario   - Codigo de usuario asociado a la llave
key       - LLave AES a obtener
*/
int getAESKey( MYSQL *connector, int user_id,  uint8_t *key) {
	
   char *fixKey = "abcdefghijklmnopqrstuvwxyzabcdef";
   int i = 0;

   for (i=0; i < 31; i++)
      key[i] = (uint8_t) fixKey[i];

   return 1;
   /*
   int ok = 0;
   MYSQL_RES *resultSet;
   char *queryString = (char *)malloc( 200 * sizeof(char));
   memset( queryString, 0, 200 * sizeof(char));

   sprintf(queryString, "SELECT  id, aesKey  FROM user WHERE id = %d", user_id);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 0;
   }

   resultSet = mysql_store_result(connector);
   if (  resultSet != 0 && mysql_num_rows( resultSet) != 0)
   {   
      MYSQL_ROW row = mysql_fetch_row ( resultSet);
      uint8_t *b = (uint8_t *)row[2];
      int i;
      for ( i= 0; i < 32; i++)
          key[i] = *b++;
      
      ok= 1;
   }

   mysql_free_result(resultSet);
   return ok; 
   */ 
	
}// getAESKey


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
 
   connector - Conexión a la base de datos
   param - Lista de parametros de la transaccion
   [0] Codigo  de cuenta-desde
   [1] Codigo  de cuenta-hacia
   [2] Valor de la transaccion
   [3] Token utilizado
   [4] Tipo de transaccion
 
*/
char *validate(MYSQL *connector, char** param){

   char        *msg = 0;    


   // Ejemplo de una transaccion
   // 2014-06-17,CA-1234567890,CC-2345678901,1000,TOKEN-123456789, 1

   /*
   // Fecha
   // La fecha debe estar bien formada
   // La fecha no puede ser futura
   // La fecha no puede ser anterior a 1 ano
   
   struct  tm  trans_date;
   time_t      now;
   time_t      trans_time;

   memset(&trans_date, 0, sizeof(struct tm));
   char *p = strptime(param[0], "%Y-%m-%d", &trans_date);

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
   */ 

   // Cuenta-desde
   // Cuenta debe tener 9 caracteres o menos
   // Cuenta debe ser numerica
   // La cuenta-desde debe existir en el maestro de cuentas
   printf("::: Cuenta-desde");
   if ( msg == 0)
   {
      if ( strlen( param[0]) > 9)
           msg = "Cuenta-desde debe tener de 0 a 9 digitos";
      else if ( !isNumber(param[0]) )
           msg = "Cuenta-desde debe ser numerica";
      else if ( !accountExists(connector, param[0]))
           msg = "Cuenta-desde no esta registrada";
   }

   // Cuenta-hacia
   // Cuenta-hacia debe tener 9 caracteres o menos
   // Cuenta-hacia debe ser numerica
   // La cuenta-hacia debe existir en el maestro de cuentas
   // La cuenta-hacia debe ser diferente de la cuenta-desde
   printf("::: Cuenta-hacia");
   if ( msg == 0)
   {
      if ( strlen( param[1]) > 9)
           msg = "Cuenta-hacia debe tener de 0 a 9 digitos";
      else if ( !isNumber(param[1]) )
           msg = "Cuenta-hacia debe ser numerica";
      else if ( !accountExists(connector, param[1]))
           msg = "Cuenta-hacia no esta registrada";
      else if (strncmp(param[1], param[2], strlen(param[1])) == 0)
            msg = "Cuenta-desde no puede ser igual a cuenta-hacia";
   }

   // Valor
   // El valor debe ser numerico de menos de 10 cifras
   // El valor debe ser un numero positivo
   printf("::: Valor");
   if (msg == 0)
   {
      if ( strlen(param[2]) >= 10)
         msg = "Valor no puede tener más  de 9 dígitos";
      else if (! isNumber(param[2]))
         msg = "Valor debe ser numérico y menor de 10 dígitos";
      else if ( atof(param[2]) <= 0.0)
            msg = "Valor de la transacción debe ser un número positivo";
   }
   

   // Token
   // El token debe estar habilitado
   printf( "::: Token");
   if ( msg == 0 )
   {
	   if ( ! isTokenValid( connector, param[3]))
	      msg = "Token no existe, o ya ha sido utilizado";
   }

   // Tipo de transaccion
   // El tipo de transaccion debe tener longitud 1
   // El tipo de transaccion debe ser numerico
   // El tipo de transaccion debe ser 0 o 1
   if ( msg == 0)
   {   
      if (strlen( param[4]) > 771)
         msg = "Longitud del tipo de transacción debe ser 1";
      else if (! isNumber(param[4]))
         msg = "Tipo de transaccion debe ser numerico";
      else if (atoi(param[4]) > 1)
         msg = "Tipo de transaccion debe ser 0 o 1";
   }

   return msg;
} // validate


/***********************************************************************
 * Begin method
 */
int main (int argc, char *argv[]) {

   char line[81];     // Linea de movimientos
   FILE* ciphered;    // Archivo cifrado de transacciones
   FILE* file;        // Archivo de movimientos
   
   char **param = (char **)malloc( sizeof( char *) * 7); // Parametros en la linea
   char *msg;         // Mensaje de error
   char *p, *q;       // Running pointers sobre los parametros
   int  i = 0;        // Running index de los parametros
   int  nM = 0;       // Número de transacciones en el archivo de movimientos
   int  nError = 0;   // Numero de transacciones erradas en el batch

   MYSQL *connector;  // Conexión a la bdd

   // Connect to database
   char *server   = "localhost";
   char *user     = "advlogin";
   char *password = "Hard+20.";
   char *database = "advlogin";
   
   aes256_context  ctx; 
   uint8_t         buf[16];
   int             user_id = atoi( argv[0]);


   connector = mysql_init(NULL);
   if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0))
   {
      // If error, print the error in the standar output
      fprintf(stderr, "%s\n", mysql_error(connector));
      exit(1);
   }

   // Abra el archivo de transacciones
   ciphered = fopen("movements.aes", "rt");
   if (ciphered == NULL)
   {
      printf("Archivo inexistente!\n");
      exit(1);
   }
   
   // Descifre el archivo de movimientos
   // Obtenga la llave de cifrado con base en el usuario recibido
    uint8_t key[32];
    if ( !getAESKey( connector, user_id,  key)) {
		printf( "No pudo obtener la llave de cifrado");
		exit(1);
    } 
    
   // Descifre el archivo y guardelo en el archivo "movements.txt"
   file = fopen("movements.txt", "wt");
   if (file == NULL)
   {
      printf("No pudo crear archivo de movimientos!\n");
      exit(1);
   }
   
    while( ! feof( ciphered)) {
		fread( buf, sizeof( uint8_t), 16, ciphered);
        aes256_init(&ctx, key);
        aes256_decrypt_ecb(&ctx, buf);
        fwrite( buf, sizeof(char), 16, file);
    }
    aes256_done(&ctx);
    fclose(ciphered);
    fclose(file);
    
   // Procese el archivo descifrado
   file = fopen("movements.txt", "rt");
   if (file == NULL)
   {
      printf("Archivo claro de movimientos inexistente!\n");
      exit(1);
   }


   // Para cada transaccion en el batch de transacciones
   mysql_autocommit( connector, 0);  // Auto commit off
   while (!feof(file))
   {
      fgets(line, 80, file);
      printf("%s", line);
      nM++;

      // Obtenga los componentes de la transaccion
      p = q = line;
      for (i = 0; i < 5; i ++)
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
      if (i < 5)
         continue;

      // Valide cada componente
      printf( "::: Voy a validar");
      msg = validate( connector, param);
      if ( msg != 0) {
         nError++;
         printf( "*** Error: %s\n", msg);
         continue;
      }

      // Actualice la base de datos con la nueva transaccion
      printf("::: Voy a actualizar Bdd");
      int res = insertTransaction(connector, param[0], param[1], param[2], param[3], atoi(param[4]));
      if (res)
         nError++;

      // Libere la memoria alocada para cada transacccion
      for (i = 0; i < 5; i ++)
         free(param[i]);

   } // while ! feof() ... quedan transacciones

   // Si no hubo errores, haga commit, else rechace el batch completo
   if ( !nError){
      mysql_commit(connector);
      printf(">>> Batch de transacciones procesado correctamente. N transacciones = %d", nM);
   } else
   {   
      mysql_rollback(connector);
      printf(">>> Batch de transacciones fue rechazado. N transacciones = %d, N errores = %d", nM, nError);
   }

   mysql_close(connector);
   free(param);
   fclose(file);
   return(0);
} // main





