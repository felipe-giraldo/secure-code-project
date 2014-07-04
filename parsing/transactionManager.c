/***********************************************************************
 * File: transactionManger.c
 * Authores: Grupo 4
 * Created on 17 de junio de 2014, 22:27
 */

/***********************************************************************
 *  Defines and Includes
 */
#define _XOPEN_SOURCE


// ----------------------------------------------------------------------
                              
#include <ctype.h>
#include <stdio.h>
#include <stdint.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <assert.h>
#include <mysql.h>
#include <mcrypt.h>

/*********************************************************
 * Trims leading and trailing whitespace
 * 
 * str - String to be trimmed in place
 */
char *trim(char *str)
{
    size_t len = 0;
    char *frontp = str - 1;
    char *endp = NULL;

    if( str == NULL )
            return NULL;

    if( str[0] == '\0' )
            return str;

    len = strlen(str);
    endp = str + len;

    /* Move the front and back pointers to address
     * the first non-whitespace characters from
     * each end.
     */
    while( isspace(*(++frontp)) );
    while( isspace(*(--endp)) && endp != frontp );

    if( str + len - 1 != endp )
            *(endp + 1) = '\0';
    else if( frontp != str &&  endp == frontp )
            *str = '\0';

    /* Shift the string so that it starts at str so
     * that if it's dynamically allocated, we can
     * still free it on the returned pointer.  Note
     * the reuse of endp to mean the front of the
     * string buffer now.
     */
    endp = str;
    if( frontp != str )
    {
            while( *frontp ) *endp++ = *frontp++;
            *endp = '\0';
    }


    return str;
}// trim


/***********************************************************************
 * Splits words with a delimiter
 *
 * Parameters:
 * - p: Pointer to first character of parameter
 * - q: Pointer past the last character of the parameter
 *
 * Return: a pointer to parameter
 * Final state:  p y q point to the starting position of next parameter,
 * or to the end of the string
 *
 */
char *getParms(char **p, char **q) {

   char *r = "";   // r: Result param
   char *i = *p;  // i: starting position
   char *j = *q;  // j: running end position
   int   l = 0;   // l: number of chars in param

   // if input not exhausted
   if ( *j)
   {
      // search for the ending ','  or endOfString
      while (*j != '|' && *j != 0 && l <= 80)
      {
         l++;
         j++;
      }
      // Hit end of parm => get it   
      r = (char *) malloc(sizeof(char) * (l + 1)); *r= 0;
      strncpy(r, i, l);
      r[l] = 0;
      trim (r);

      // Update pointers for the next parm
      *p = (*j == 0)? j : j + 1;
      *q = *p;
   }

   return r;
} // getParms

/***********************************************************************
 * Gets date-time in string format
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
 * Process a batch transaction
 *
 * Parameters:
 * - fromAccount: account movement from
 * - toAccount: account movement to
 * - value: Value to movement
 * - token: Identifier to validate the transaction
 * - type: Type of transaction (CREDIT, DEBIT)
 *
 * Return:
 * - 0 if transaction processed Ok
 * - 1 if the server conection is not successful
 * - 2 if the query return and error
 */
int insertTransaction( MYSQL *connector, char *fromAccount, char *toAccount, char *value, char *token, int type) {

   MYSQL_RES *resultSet;

   // SQL query string
   char *queryString = (char *) malloc(200);

   // Query example: INSERT INTO transactions VALUES (null, '1234567890', '2345678901', 1000, 'TOKEN-123456789', 2014-06-21, 99, 1)
   // transaction table columns : id	from_account	to_account	ammount	token	transaction_date	transaction_state	transaction_type
   sprintf(queryString, "INSERT INTO transactions VALUES (null, %s, %s, %d, '%s', '%s', %d, %d)",
           fromAccount, toAccount, atoi(value), token, getDate(), 99, type);

   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 2;
   }
   resultSet = mysql_use_result(connector);
   
   // Updates cuenta desde status
   sprintf(queryString, "UPDATE account SET money = (money - %d) where id_account = '%s'", atoi(value), fromAccount);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 2;
   }
   resultSet = mysql_use_result(connector);

   // Updates cuenta hacia status
   sprintf(queryString, "UPDATE account SET money = (money + %d) where id_account = '%s'", atoi(value), toAccount);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 2;
   }
   resultSet = mysql_use_result(connector);


   // Flag token as used
   sprintf(queryString, "UPDATE user_token SET used=1 WHERE token_id= '%s'", token);
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
 * Verifica si hay suficiente dinero en el saldo de la cuenta
 * 
 * connector - conexion a la bdd
 * cuenta -   Cuenta de la que se van a retirar fondos
 * valor  -   Valor a retirar
 */
 int  enoughMoney( MYSQL *connector, char *cuenta,  int valor)
 {
   int enough = 0;
   MYSQL_RES *resultSet;
   char *queryString = (char *)malloc( 200 * sizeof(char));
   memset( queryString, 0, 200 * sizeof(char));

   sprintf(queryString, "SELECT  money  FROM account WHERE id_account = '%s'", cuenta);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 0;
   }

   resultSet = mysql_store_result(connector);
   if (  resultSet != 0 && mysql_num_rows( resultSet) != 0)
   {   
      MYSQL_ROW row = mysql_fetch_row ( resultSet);
      enough  = atoi(row[0]) >= valor;
   }

   mysql_free_result(resultSet);
   return enough;
	 
	 
 }// enoughMoney

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

   sprintf(queryString, "SELECT  token_id, user_id, used  FROM user_token WHERE token_id = '%s'", token_id);
   if (mysql_query(connector, queryString))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      return 0;
   }

   resultSet = mysql_store_result(connector);
   if (  resultSet != 0 && mysql_num_rows( resultSet) != 0)
   {   
      MYSQL_ROW row = mysql_fetch_row ( resultSet);
      valid  = atoi(row[2]) != 0? 0: 1;
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
int getAESKey( MYSQL *connector, int user_id,  char *key) {
	
   key[0]  =  101    ;
   key[1]  =  54  ;
   key[2]  =  54  ;
   key[3]  =  48  ;
   key[4]  =  51  ;
   key[5]  =  98  ;
   key[6]  =  48  ;
   key[7]  =  50  ;
   key[8]  =  98  ;
   key[9]  =  57  ;
   key[10] =  101  ;
   key[11] =  49  ;
   key[12] =  97  ;
   key[13] =  53  ;
   key[14] =  51  ;
   key[15] =  56  ;
   /*
   key[0]  =  0xE6    ;
   key[1]  =  0x60  ;
   key[2]  =  0x3B  ;
   key[3]  =  0x02  ;
   key[4]  =  0xB9  ;
   key[5]  =  0xE1  ;
   key[6]  =  0xA5  ;
   key[7]  =  0x38  ;
   key[8]  =  0x32  ;
   key[9]  =  0xBC  ;
   key[10] =  0x20  ;
   key[11] =  0x0E  ;
   key[12] =  0x4C  ;
   key[13] =  0x89  ;
   key[14] =  0xEB  ;
   key[15] =  0xF2  ;
   */

   return 1;
	
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
   int   debug = 0;
   char  *msg  = 0;    


   // Ejemplo de una transaccion
   // 123456789,234567890,1000,TOKEN-123456789, 1

   // Cuenta-desde
   // Cuenta debe tener 9 caracteres o menos
   // Cuenta debe ser numerica
   // La cuenta-desde debe existir en el maestro de cuentas
   if(debug) printf("::: Cuenta-desde\n");
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
   if(debug) printf("::: Cuenta-hacia\n");
   if ( msg == 0)
   {
      if ( strlen( param[1]) > 9)
           msg = "Cuenta-hacia debe tener de 0 a 9 digitos";
      else if ( !isNumber(param[1]) )
           msg = "Cuenta-hacia debe ser numerica";
      else if ( !accountExists(connector, param[1]))
           msg = "Cuenta-hacia no esta registrada";
      else if (strncmp(param[0], param[1], strlen(param[1])) == 0)
            msg = "Cuenta-desde no puede ser igual a cuenta-hacia";
   }

   // Valor
   // El valor debe ser numerico de menos de 10 cifras
   // El valor debe ser un numero positivo
   // El valor debe ser menor o igual al saldo de la cuenta-desde
   if(debug) printf("::: Valor\n");
   if (msg == 0)
   {
      if ( strlen(param[2]) >= 10)
         msg = "Valor no puede tener más  de 9 dígitos";
      else if (! isNumber(param[2]))
         msg = "Valor debe ser numérico, positivo y menor de 10 dígitos";
      else if ( atof(param[2]) <= 0.0)
            msg = "Valor de la transacción debe ser un número positivo";
      else if ( !enoughMoney( connector, param[0], atoi(param[2])))
            msg = "Valor de la transaccion excede el saldo de la cuenta-desde";
   }
   

   // Token
   // El token debe estar habilitado
   if(debug) printf( "::: Token\n");
   if ( msg == 0 )
   {
	   if ( !strlen(param[3]))
	      msg = "Token no puede ser nulo";
	   else if ( ! isTokenValid( connector, param[3]))
	      msg = "Token no existe, o ya ha sido utilizado";
   }

   // Tipo de transaccion
   // El tipo de transaccion debe tener longitud 1
   // El tipo de transaccion debe ser numerico
   // El tipo de transaccion debe ser 0 o 1
   if(debug) if ( msg == 0)
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


/********************************************************************************
   Cifra un buffer con AES-128 en modo CBC utilizando 
   la biblioteca MCrypt de Linux 
    
   buffer     - Texto a cifrar 
   buffer_len - Longitud real del buffer 
   IV         - Vector de inicializacion de CBC 
   key        - La llave de cifrado 
   key_len    - Longitud de la llave de cifrado 
*/
int encrypt(
           void* buffer,
           int buffer_len, /* Because the plaintext could include null bytes*/
           char* IV,
           char* key,
           int key_len
           )
{
   MCRYPT td = mcrypt_module_open("rijndael-128", NULL, "cbc", NULL);
   int blocksize = mcrypt_enc_get_block_size(td);
   if ( buffer_len % blocksize != 0 )
   {
      return 1;
   }

   mcrypt_generic_init(td, key, key_len, IV);
   mcrypt_generic(td, buffer, buffer_len);
   mcrypt_generic_deinit (td);
   mcrypt_module_close(td);
   return 0;
} // encrypt


/*************************************************************************
Descifra un texto cifrado con AES-128 modo CBC utilizando
la biblioteca MCrypt de Linux 
    
   buffer     - Texto a descifrar 
   buffer_len - Longitud real del buffer 
   IV         - Vector de inicializacion de CBC 
   key        - La llave de cifrado 
   key_len    - Longitud de la llave de cifrado 
*/
int decrypt(
           void* buffer,
           int buffer_len,
           char* IV,
           char* key,
           int key_len
           )
{
   MCRYPT td = mcrypt_module_open("rijndael-128", NULL, "cbc", NULL);
   int blocksize = mcrypt_enc_get_block_size(td);
   if ( buffer_len % blocksize != 0 )
   {
      return 1;
   }
   mcrypt_generic_init(td, key, key_len, IV);
   mdecrypt_generic(td, buffer, buffer_len);
   mcrypt_generic_deinit (td);
   mcrypt_module_close(td);
   return 0;
} // decrypt


/***********************************************************************
 * Main method
 */


int main (int argc, char *argv[]) {
   int   debug=0;          // debug flag
   char line[81];         // Linea de movimientos
   char  *cipheredName;   // Nombre basico del archivo a procesar
   FILE* ciphered;        // Archivo cifrado de transacciones
   char *plainName;       // Nombre basico del archivo plano descifrado
   FILE* file;            // Archivo de movimientos
   
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
   
   int    buffer_len; 
   char   *buf;
   char   *IV     = "AAAAAAAAAAAAAAAA";
   char   key[32];
   int    key_size = 16;

   if (argc != 17)                     
   {                                   
      printf("*** Faltan parámetros");
      exit(1);                         
   }                                   
                                       
   for (i = 0; i < key_size; i++)      
      key[i] = atoi(argv[i+1]);       


   buf = (char *) malloc(sizeof(char) * (32000));

   connector = mysql_init(NULL);
   if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      exit(1);
   }

   // Abra el archivo de transacciones
   cipheredName = malloc( sizeof( char) * 200);
   strncpy( cipheredName, argv[1],100);
   strcat( cipheredName,".cif");
   ciphered = fopen(cipheredName, "r");
   if (ciphered == NULL)
   {
      printf("*** Archivo inexistente!\n");
      exit(1);
   }
   printf("::: Ciphered transaction file=%s opened\n", cipheredName);
  
   // Descifre el archivo de movimientos
   // Obtenga la llave de cifrado con base en el usuario recibido 
/*
    memset( key, 0, 32 * sizeof(char));                
    if ( !getAESKey( connector, user_id,  key)) {      
      printf( "No pudo obtener la llave de cifrado\n");
      exit(1);                                         
    }                                                  
*/
    
   // Descifre el archivo y guardelo en el archivo "movements.txt"
   plainName = malloc( sizeof(char) * 200);
   strncpy( plainName, argv[1], 100);
   strcat(plainName, ".txt");
   file = fopen(plainName, "w");
   if (file == NULL)
   {
      printf("*** No pudo crear archivo de movimientos!\n");
      exit(1);
   }
   printf("::: Plain text transaction file %s opened\n", plainName);
   
    if ( ! feof( ciphered)) {
		buffer_len = fread( buf, sizeof(char), 32000, ciphered);
        int ok = decrypt(buf, buffer_len, IV, key, key_size);
        if(debug) fwrite( buf, sizeof(char), buffer_len, stdout);
        if ( ok ){
            printf("*** No pudo descifrar correctamente");
            exit(1);
        }
        fwrite( buf, sizeof(char), buffer_len, file);
        if(debug) printf("::: file %s decrypted in %s. Length=%d\n", cipheredName, plainName, buffer_len);
    }
    fclose(ciphered);
    fclose(file);
    
   // Procese el archivo descifrado
   printf("\n");
   file = fopen(plainName, "r");
   if (file == NULL)
   {
      printf("*** Archivo claro de movimientos inexistente!\n");
      exit(1);
   }


   // Para cada transaccion en el batch de transacciones
   mysql_autocommit( connector, 0);  // Auto commit off
   while (!feof(file))
   {
      memset( line, 0, 80 * sizeof(char));
      char *b =fgets(line, 80, file);
      if ( b == NULL)
        break;
        
      nM++;
      trim(line);
      if (strlen(line) == 0 || *line == '&')
         continue;
         
      if(debug) printf("transaction [%s]\n", line); fflush(stdout);


      // Obtenga los componentes de la transaccion
      p = q = line;
      for (i = 0; i < 5; i ++)
      {
         if ((p - line) > 80) {
            printf("*** Error: Linea %d agotada, Falta parametro  %d", nM, i);
            break;
         }
         
         param[i] = (char *)getParms(&p, &q);
         if(debug)printf("field[%d]=%s\n", i, param[i]); 
         if (param[i] == 0) {
            printf("*** Error: Falta parametro  %d", i);
            break;
         }
      }
      if (i < 5)
         continue;

      // Valide cada componente
      msg = validate( connector, param);
      if ( msg != 0) {
         nError++;
         printf( "*** Linea[%d], error: %s\n", nM, msg);
         continue;
      }

      // Actualice la base de datos con la nueva transaccion
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
      printf(">>> Batch de transacciones procesado correctamente. N transacciones = %d\n", nM);
   } else
   {   
      mysql_rollback(connector);
      printf(">>> Batch de transacciones fue rechazado. N transacciones = %d, N errores = %d\n", nM, nError);
   }

   mysql_close(connector);
   free(param);
   fclose(file);
   return(0);
   
} // main









