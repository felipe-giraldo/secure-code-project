/***********************************************************************
 * File: transactionManger.c
 * Authores: Grupo 4
 * Created on 17 de junio de 2014, 22:27
 */

/***********************************************************************
 *  Defines and Includes
 */
#define _XOPEN_SOURCE


// ------------------------------------------------------------------
// Signed variables are for wimps  necesarios para SHA-2
#define uchar unsigned char  // 8-bit byte
#define uint  unsigned long  // 32-bit word

// DBL_INT_ADD treats two unsigned ints a and b as one 64-bit integer and adds c to it
#define DBL_INT_ADD(a,b,c) if (a > 0xffffffff - (c)) ++b; a += c;
#define ROTLEFT(a,b) (((a) << (b)) | ((a) >> (32-(b))))
#define ROTRIGHT(a,b) (((a) >> (b)) | ((a) << (32-(b))))

#define CH(x,y,z) (((x) & (y)) ^ (~(x) & (z)))
#define MAJ(x,y,z) (((x) & (y)) ^ ((x) & (z)) ^ ((y) & (z)))
#define EP0(x) (ROTRIGHT(x,2) ^ ROTRIGHT(x,13) ^ ROTRIGHT(x,22))
#define EP1(x) (ROTRIGHT(x,6) ^ ROTRIGHT(x,11) ^ ROTRIGHT(x,25))
#define SIG0(x) (ROTRIGHT(x,7) ^ ROTRIGHT(x,18) ^ ((x) >> 3))
#define SIG1(x) (ROTRIGHT(x,17) ^ ROTRIGHT(x,19) ^ ((x) >> 10))

typedef struct {
   uchar data[64];
   uint datalen;
   uint bitlen[2];
   uint state[8];
} SHA256_CTX;

uint k[64] = {
   0x428a2f98,0x71374491,0xb5c0fbcf,0xe9b5dba5,0x3956c25b,0x59f111f1,0x923f82a4,0xab1c5ed5,
   0xd807aa98,0x12835b01,0x243185be,0x550c7dc3,0x72be5d74,0x80deb1fe,0x9bdc06a7,0xc19bf174,
   0xe49b69c1,0xefbe4786,0x0fc19dc6,0x240ca1cc,0x2de92c6f,0x4a7484aa,0x5cb0a9dc,0x76f988da,
   0x983e5152,0xa831c66d,0xb00327c8,0xbf597fc7,0xc6e00bf3,0xd5a79147,0x06ca6351,0x14292967,
   0x27b70a85,0x2e1b2138,0x4d2c6dfc,0x53380d13,0x650a7354,0x766a0abb,0x81c2c92e,0x92722c85,
   0xa2bfe8a1,0xa81a664b,0xc24b8b70,0xc76c51a3,0xd192e819,0xd6990624,0xf40e3585,0x106aa070,
   0x19a4c116,0x1e376c08,0x2748774c,0x34b0bcb5,0x391c0cb3,0x4ed8aa4a,0x5b9cca4f,0x682e6ff3,
   0x748f82ee,0x78a5636f,0x84c87814,0x8cc70208,0x90befffa,0xa4506ceb,0xbef9a3f7,0xc67178f2
};
 
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
   if ( *j)
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

/*****************************************************************************
 * Aplica la transformación SHA-2
 * 
 * ctx  - Contexto
 * data - Datos 
 */
void sha256_transform(SHA256_CTX *ctx, uchar data[])
{  
   uint a,b,c,d,e,f,g,h,i,j,t1,t2,m[64];
      
   for (i=0,j=0; i < 16; ++i, j += 4)
      m[i] = (data[j] << 24) | (data[j+1] << 16) | (data[j+2] << 8) | (data[j+3]);
   for ( ; i < 64; ++i)
      m[i] = SIG1(m[i-2]) + m[i-7] + SIG0(m[i-15]) + m[i-16];

   a = ctx->state[0];
   b = ctx->state[1];
   c = ctx->state[2];
   d = ctx->state[3];
   e = ctx->state[4];
   f = ctx->state[5];
   g = ctx->state[6];
   h = ctx->state[7];
   
   for (i = 0; i < 64; ++i) {
      t1 = h + EP1(e) + CH(e,f,g) + k[i] + m[i];
      t2 = EP0(a) + MAJ(a,b,c);
      h = g;
      g = f;
      f = e;
      e = d + t1;
      d = c;
      c = b;
      b = a;
      a = t1 + t2;
   }
   
   ctx->state[0] += a;
   ctx->state[1] += b;
   ctx->state[2] += c;
   ctx->state[3] += d;
   ctx->state[4] += e;
   ctx->state[5] += f;
   ctx->state[6] += g;
   ctx->state[7] += h;
   
}  //sha256_transform
 
/***********************************************************
 * Inicializa el estado de SHA-2
 * 
 * ctx - Contexto
 */
void sha256_init(SHA256_CTX *ctx)
{  
   ctx->datalen = 0; 
   ctx->bitlen[0] = 0; 
   ctx->bitlen[1] = 0; 
   ctx->state[0] = 0x6a09e667;
   ctx->state[1] = 0xbb67ae85;
   ctx->state[2] = 0x3c6ef372;
   ctx->state[3] = 0xa54ff53a;
   ctx->state[4] = 0x510e527f;
   ctx->state[5] = 0x9b05688c;
   ctx->state[6] = 0x1f83d9ab;
   ctx->state[7] = 0x5be0cd19;
   
}// sha256_init
 
/**********************************************************
 * Actualiza el running value del hash SHA-2
 * 
 * ctx  - Contexto
 * data - datos
 * len  - Longitud
 */
void sha256_update(SHA256_CTX *ctx, uchar data[], uint len)
{  
   uint i;
   
   for (i=0; i < len; ++i) { 
      ctx->data[ctx->datalen] = data[i]; 
      ctx->datalen++; 
      if (ctx->datalen == 64) { 
         sha256_transform(ctx,ctx->data);
         DBL_INT_ADD(ctx->bitlen[0],ctx->bitlen[1],512); 
         ctx->datalen = 0; 
      }  
   }  
}  //sha256_update 

/*********************************************************
 * Culmina la generación del SHA-2 hash
 * 
 * ctx - Contexto
 * hash - Hash producido
 */
void sha256_final(SHA256_CTX *ctx, uchar hash[])
{  
   uint i; 
   
   i = ctx->datalen; 
   
   // Pad whatever data is left in the buffer. 
   if (ctx->datalen < 56) { 
      ctx->data[i++] = 0x80; 
      while (i < 56) 
         ctx->data[i++] = 0x00; 
   }  
   else { 
      ctx->data[i++] = 0x80; 
      while (i < 64) 
         ctx->data[i++] = 0x00; 
      sha256_transform(ctx,ctx->data);
      memset(ctx->data,0,56); 
   }  
   
   // Append to the padding the total message's length in bits and transform. 
   DBL_INT_ADD(ctx->bitlen[0],ctx->bitlen[1],ctx->datalen * 8);
   ctx->data[63] = ctx->bitlen[0]; 
   ctx->data[62] = ctx->bitlen[0] >> 8; 
   ctx->data[61] = ctx->bitlen[0] >> 16; 
   ctx->data[60] = ctx->bitlen[0] >> 24; 
   ctx->data[59] = ctx->bitlen[1]; 
   ctx->data[58] = ctx->bitlen[1] >> 8; 
   ctx->data[57] = ctx->bitlen[1] >> 16;  
   ctx->data[56] = ctx->bitlen[1] >> 24; 
   sha256_transform(ctx,ctx->data);
   
   // Since this implementation uses little endian byte ordering and SHA uses big endian,
   // reverse all the bytes when copying the final state to the output hash. 
   for (i=0; i < 4; ++i) { 
      hash[i]    = (ctx->state[0] >> (24-i*8)) & 0x000000ff; 
      hash[i+4]  = (ctx->state[1] >> (24-i*8)) & 0x000000ff; 
      hash[i+8]  = (ctx->state[2] >> (24-i*8)) & 0x000000ff;
      hash[i+12] = (ctx->state[3] >> (24-i*8)) & 0x000000ff;
      hash[i+16] = (ctx->state[4] >> (24-i*8)) & 0x000000ff;
      hash[i+20] = (ctx->state[5] >> (24-i*8)) & 0x000000ff;
      hash[i+24] = (ctx->state[6] >> (24-i*8)) & 0x000000ff;
      hash[i+28] = (ctx->state[7] >> (24-i*8)) & 0x000000ff;
   }  
}  // sha256_final

/* Imprime el SHA-2 generado
 * hash - Hash a imprimir
 */
void print_hash(unsigned char hash[])
{
   int idx;
   for (idx=0; idx < 32; idx++)
      printf("%02x[%d]",hash[idx], hash[idx]);
   printf("\n");
}//print_hash



/**********************************************************************
Get the AES key based on the user id

connector - Conexion a la base de datos
usuario   - Codigo de usuario asociado a la llave
key       - LLave AES a obtener
*/
int getAESKey( MYSQL *connector, int user_id,  char *key) {
	
   uchar *fixKey = (uchar *)"felipe|felipe|1234";
   
   SHA256_CTX ctx;
   unsigned char hash[32];
   sha256_init(&ctx);
   sha256_update(&ctx,fixKey,strlen((char*)fixKey));
   sha256_final(&ctx,hash);
   print_hash(hash);
 
   
   int i = 0;
   for (i=0; i < 31; i++)
      key[i] = hash[i];

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
   printf("::: Cuenta-desde\n");
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
   printf("::: Cuenta-hacia\n");
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
   printf("::: Valor\n");
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
   printf( "::: Token\n");
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
   printf("::: blocksize= %d buffer_len=%d\n", blocksize, buffer_len);
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
   
   int    buffer_len; 
   char   *buf;
   int    user_id = atoi( argv[0]);
   char   *IV     = "AAAAAAAAAAAAAAAA";
   char   key[32];
   int    key_size = 16;

   buf = (char *) malloc(sizeof(char) * (32000));

   connector = mysql_init(NULL);
   if (!mysql_real_connect(connector, server, user, password, database, 0, NULL, 0))
   {
      fprintf(stderr, "%s\n", mysql_error(connector));
      exit(1);
   }

   // Abra el archivo de transacciones
   ciphered = fopen("movements.cif", "r");
   if (ciphered == NULL)
   {
      printf("*** Archivo inexistente!\n");
      exit(1);
   }
  
   // Descifre el archivo de movimientos
   // Obtenga la llave de cifrado con base en el usuario recibido 
    memset( key, 0, 32 * sizeof(char));
    if ( !getAESKey( connector, user_id,  key)) {
		printf( "No pudo obtener la llave de cifrado\n");
		exit(1);
    } 
    
   // Descifre el archivo y guardelo en el archivo "movements.txt"
   file = fopen("./movements.txt", "w");
   if (file == NULL)
   {
      printf("*** No pudo crear archivo de movimientos!\n");
      exit(1);
   }
   
    if ( ! feof( ciphered)) {
		buffer_len = fread( buf, sizeof(char), 32000, ciphered);
        int ok = decrypt(buf, buffer_len, IV, key, key_size);
        fwrite( buf, sizeof(char), buffer_len, stdout);
        if ( ok ){
            printf("*** No pudo descifrar correctamente");
            exit(1);
        }
        fwrite( buf, sizeof(char), buffer_len, file);
    }
    fclose(ciphered);
    fclose(file);
    
   // Procese el archivo descifrado
   file = fopen("movements.txt", "r");
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






