
#define uchar unsigned char  // 8-bit byte
#include <stdio.h>
#include <stdlib.h>
#include <mcrypt.h>
#include <string.h>

/*
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
   int pad =   (blocksize - buffer_len % blocksize) % blocksize;
   printf("::: pad antes = %d buffer_len =%d blocksize=%d\n",pad, buffer_len, blocksize);
   char *b = (char *)buffer+ buffer_len;
   int i;
   for ( i = 0; i < pad; i++)
   {   
      *b++ = ' ';
      buffer_len++;
   }
    *b = '\0';
    pad =  (blocksize - buffer_len % blocksize) % blocksize;
    printf("::: pad despues = %d buffer_len =%d blocksize=%d\n",pad, buffer_len, blocksize);   

   if ( pad != 0 )
   {
      return 1;
   }

   mcrypt_generic_init(td, key, key_len, IV);
   mcrypt_generic(td, buffer, buffer_len);
   mcrypt_generic_deinit (td);
   mcrypt_module_close(td);
   return buffer_len;
} // encrypt


/*
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


int main(int argc, char **argv)
{
   char *fileName     =  (char *)malloc( sizeof(char) * 200);	
   char *filePlain    =  (char *)malloc( sizeof(char) * 200);
   char *fileCiphered =  (char *)malloc( sizeof(char) * 200);
   FILE *ciphered;
   FILE *file;
   
   uchar key[16];
   char  *buffer;
   int   buffer_len = 0;
   int   keysize    = 16;
   char  *IV        = "AAAAAAAAAAAAAAAA";

   key[0]  = (uchar) 0xE6    ;
   key[1]  = (uchar) 0x60  ;
   key[2]  = (uchar) 0x3B  ;
   key[3]  = (uchar) 0x02  ;
   key[4]  = (uchar) 0xB9  ;
   key[5]  = (uchar) 0xE1  ;
   key[6]  = (uchar) 0xA5  ;
   key[7]  = (uchar) 0x38  ;
   key[8]  = (uchar) 0x32  ;
   key[9]  = (uchar) 0xBC  ;
   key[10] = (uchar) 0x20  ;
   key[11] = (uchar) 0x0E  ;
   key[12] = (uchar) 0x4C  ;
   key[13] = (uchar) 0x89  ;
   key[14] = (uchar) 0xEB  ;
   key[15] = (uchar) 0xF2  ;



   buffer = (char *) malloc(sizeof(char) * (32000));

   printf("::: argc=%d, argv[0]=%s, argv[1]=%s\n", argc, argv[0], argv[1]); fflush(stdout);
   fileName = argv[1];
   strcpy (filePlain, fileName);
   strcat (filePlain, ".asc"); 
   printf("::: filePlain= %s\n", filePlain);  
   file = fopen(filePlain, "r");
   if (file == NULL)
   {
      printf("Archivo inexistente!\n");
      exit(1);
   }

   strcpy( fileCiphered, fileName);
   strcat(fileCiphered, ".cif");
   printf("::: fileCiphered= %s\n", fileCiphered);  
   ciphered = fopen(fileCiphered, "w");
   if (ciphered == NULL)
   {
      printf("No pudo crear archivo de movimientos!\n");
      exit(1);  
   }

   buffer_len = fread( buffer, sizeof(char), 32000, file);
   fwrite(buffer, sizeof(char), buffer_len, stdout);
   buffer_len = encrypt(buffer, buffer_len, IV, (char *)key, keysize);
   printf(":::  buffer_len %d\n", buffer_len);
   fwrite( buffer, sizeof(char), buffer_len, ciphered);
   fclose(ciphered);
   ciphered = fopen("movements.cif", "r");
   if (ciphered == NULL)
   {
      printf("No pudo crear archivo de movimientos!\n");
      exit(1);  
   }
   buffer_len = fread( buffer, sizeof(char), 32000, file);
   printf(":::  buffer_len del cifrado %d\n", buffer_len);
   int ok = decrypt(buffer, buffer_len, IV, (char *)key, keysize);
   if ( ok){
      printf("No pudo descifrar correctamente archivo cifrado de movimientos!\n");
      exit(1);  
   }
   fwrite(buffer, sizeof(char), buffer_len, stdout);


   fclose(ciphered);
   fclose(file);
   printf(">>> Done!\n");
   
   return 0;
} // main



