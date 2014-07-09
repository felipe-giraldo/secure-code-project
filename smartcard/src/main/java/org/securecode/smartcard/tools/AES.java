
package org.securecode.smartcard.tools;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.Arrays;
import javax.crypto.Cipher;
import javax.crypto.CipherInputStream;
import javax.crypto.CipherOutputStream;
import javax.crypto.KeyGenerator;
import javax.crypto.SecretKey;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import org.apache.log4j.Logger;

/**
 * Clase encargada de cifrar y descifrar usando AES
 *
 * @author Grupo 4
 * @since Creado el 20 de junio de 2014
 * @version 1.0
 */
public class AES {
    
    /**
     * Logger de clase
     */
    private static final Logger logger = Logger.getLogger(AES.class);
    /**
     * Propiedades del sistema
     */
    private static final LoadProperties props = new LoadProperties("configuracion.properties");
    
    /**
     * Metodo para hacer pruebas de cifrado y descifrado
     * 
     * @param plainText  Cadena con el texto plano a cifrar y descifrar
     */
    public static void both(String plainText) {
        
        long startEncrypt, endEncrypt, startDecrypt, endDecrypt;
        String unit = "ns";
        
        try {
            KeyGenerator kgen = KeyGenerator.getInstance("AES");
            kgen.init(Integer.parseInt(props.getPropiedad("aes.length")));
            SecretKey aesKey = kgen.generateKey();

            // Encrypt
            startEncrypt = System.nanoTime();
            Cipher cipher = Cipher.getInstance(props.getPropiedad("aes.encrypt.instance"));
            cipher.init(Cipher.ENCRYPT_MODE, aesKey);
            ByteArrayOutputStream output = new ByteArrayOutputStream();
            CipherOutputStream cipherOutput = new CipherOutputStream(output, cipher);
            cipherOutput.write(plainText.getBytes());
            cipherOutput.flush();
            cipherOutput.close();
            byte[] cipherText = output.toByteArray();
            endEncrypt = System.nanoTime();

            // Decrypt
            startDecrypt = System.nanoTime();
            IvParameterSpec ivParameterSpec = new IvParameterSpec(cipher.getIV());
            cipher.init(Cipher.DECRYPT_MODE, aesKey, ivParameterSpec);
            output = new ByteArrayOutputStream();
            ByteArrayInputStream inStream = new ByteArrayInputStream(cipherText);
            CipherInputStream cipherInputStream = new CipherInputStream(inStream, cipher);
            byte[] buf = new byte[1024];
            int bytesRead;
            while ((bytesRead = cipherInputStream.read(buf)) >= 0) {
                output.write(buf, 0, bytesRead);
            }
            String decryptText = new String(output.toByteArray());
            endDecrypt = System.nanoTime();
            
            // Print results
            logger.error("Encrypt [" + (endEncrypt - startEncrypt) + " " + unit + "]: " + cipherText.toString());
            logger.error("Decrypt [" + (endDecrypt - startDecrypt) + " " + unit + "]: " + decryptText);
        }
        catch (Exception ex) {
            logger.error("Error: " + ex.getMessage());
        }
    }
    
    /**
     * Metodo para cifrar un texto plano
     * 
     * @param plainText  Cadena con el texto plano a cifrar
     * @param encryptionKey  Llave de ciframiento
     * @return  Retorna byte[] con el texto plano cifrado
     */
    public static byte[] encrypt(String plainText, String encryptionKey) {
        
        try {
            Cipher cipher = Cipher.getInstance(props.getPropiedad("aes.encrypt.instance"), props.getPropiedad("aes.encrypt.provider"));
            SecretKeySpec key = new SecretKeySpec(encryptionKey.getBytes("UTF-8"), "AES");
            cipher.init(Cipher.ENCRYPT_MODE, key, new IvParameterSpec("AAAAAAAAAAAAAAAA".getBytes("UTF-8")));
            return cipher.doFinal(plainText.getBytes("UTF-8"));
        }
        catch (Exception ex) {
            logger.error("Error: " + ex.getMessage());
            return null;
        }
    }
    
    /**
     * Metodo para descifrar un texto
     * 
     * @param cipherText  byte[] con el texto a descifrar
     * @param encryptionKey  Llave de ciframiento
     * @return  Retorna una cadena con el texto descifrado
     */
    public static String decrypt(byte[] cipherText, String encryptionKey) {
        
        try {
            Cipher cipher = Cipher.getInstance(props.getPropiedad("aes.decrypt.instance"), props.getPropiedad("aes.decrypt.provider"));
            SecretKeySpec key = new SecretKeySpec(encryptionKey.getBytes("UTF-8"), "AES");
            cipher.init(Cipher.DECRYPT_MODE, key, new IvParameterSpec("AAAAAAAAAAAAAAAA".getBytes("UTF-8")));
            return new String(cipher.doFinal(cipherText), "UTF-8");
        }
        catch (Exception ex) {
            logger.error("Error: " + ex.getMessage());
            return null;
        }
    }
    
    /**
     * Metodo para cifrar un archivo
     * 
     * @param encryptionKey  Llave de ciframiento
     * @param input  File con el apuntador al archivo a cifrar
     * @param output  File con el apuntador al archivo cifrado
     */
    public static void encrypyFile(String encryptionKey, File input, File output) {
        
        char[] password = encryptionKey.toCharArray();
        Arrays.fill(password, (char) 0);
        FileInputStream in = null;
        FileOutputStream out = null;
        
        try {
            Cipher cipher = Cipher.getInstance(props.getPropiedad("aes.encrypt.instance"));
            byte[] password16 = getPassword16(password);
            Arrays.fill(password16, (byte) 0);
            cipher.init(Cipher.ENCRYPT_MODE, new SecretKeySpec(password16, "AES"), new IvParameterSpec(encryptionKey.getBytes("UTF-8")));
            in = new FileInputStream(input);
            out = new FileOutputStream(output);
            out.write(encryptionKey.getBytes("UTF-8"));
            int i;
            byte[] buffer = new byte[4096];
            while ((i = in.read(buffer)) != -1) {
                out.write(cipher.update(buffer, 0, i));
            }
            out.write(cipher.doFinal());
        }
        catch (Exception ex) {
            logger.error("Error: " + ex.getMessage());
        }
        finally {
            try {
                if (out != null)
                    out.close();
                if (in != null)
                    in.close();
            }
            catch (IOException ex) {
                logger.error("Error: " + ex.getMessage());
            }
        }
    }
    
    /**
     * Metodo para descifrar un archivo
     * 
     * @param encryptionKey  Llave de ciframiento
     * @param input  File con el apuntador al archivo a descifrar
     * @param output  File con el apuntador al archivo descifrado
     */
    public static void decryptFile(String encryptionKey, File input, File output) {

        char[] password = encryptionKey.toCharArray();
        Arrays.fill(password, (char) 0);
        FileInputStream in = null;
        FileOutputStream out = null;
        
        try {
            byte[] iv = encryptionKey.getBytes("UTF-8");
            Cipher cipher = Cipher.getInstance(props.getPropiedad("aes.decrypt.instance"));
            byte[] password16 = getPassword16(password);
            Arrays.fill(password16, (byte) 0);
            cipher.init(Cipher.DECRYPT_MODE, new SecretKeySpec(password16, "AES"), new IvParameterSpec(iv));
            in = new FileInputStream(input);
            int n;
            int total = 0;
            while (total < iv.length) {
                n = in.read(iv, total, iv.length - total);
                if (n == -1) {
                    throw new Exception("Bad operation for decrypt");
                }
                total += n;
            }
            out = new FileOutputStream(output);
            byte[] buffer = new byte[4096];
            while ((n = in.read(buffer)) != -1) {
                out.write(cipher.update(buffer, 0, n));
            }
            out.write(cipher.doFinal());
        }
        catch (Exception ex) {
            logger.error("Error: " + ex.getMessage());
        }
        finally {
            try {
                if (out != null)
                    out.close();
                if (in != null)
                    in.close();
            }
            catch (IOException ex) {
                logger.error("Error: " + ex.getMessage());
            }
        }
    }
    
    /**
     * Metdo para agregar complejidad a la llave de ciframiento
     * 
     * @param pass  char[] con el password
     * @return  Retorna byte[] con el password
     * @throws NoSuchAlgorithmException 
     */
    private static byte[] getPassword16(char[] pass) throws NoSuchAlgorithmException {
        
        MessageDigest md = MessageDigest.getInstance("SHA-1");
        
        byte[] message1 = new byte[pass.length];
        for (int i = 0; i < pass.length; ++ i) {
            message1[i] = (byte) pass[i];
        }
        md.update(message1);
        
        byte[] message2 = md.digest();
        Arrays.fill(message1, (byte) 0);
        for (int i = 0; i < 70000; ++i) {
            message1 = new byte[message2.length + 1];
            System.arraycopy(message2, 0, message1, 0, message2.length);
            message1[message2.length] = (byte) (i % 128);
            md.update(message1);
            Arrays.fill(message2, (byte) 0);
            message2 = md.digest();
            Arrays.fill(message1, (byte) 0);
        }
        byte[] pasword = new byte[16];
        for (int i = 0; i < 16; ++i) {
            pasword[i] = message2[i];
        }
        Arrays.fill(message2, (byte) 0);
        
        return pasword;
    }
    
}
