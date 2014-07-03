
package org.securecode.smartcard.tools;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import org.apache.commons.codec.binary.Hex;
import org.apache.log4j.Logger;

/**
 * Clase encargada de generar el SHA-256 de un texto plano
 *
 * @author Grupo 4
 * @since Creado el 20 de junio de 2014
 * @version 1.0
 */
public class SHA {
    
    /**
     * Logger de clase
     */
    private static final Logger logger = Logger.getLogger(SHA.class);

    /**
     * Metodo para generar el SHA-256 de un texto plano
     * 
     * @param plainText  Cadena de texto con el texto plano
     * @return  Retorna el SHA-256 del texto plano
     */
    public static String getSHA256(String plainText) {
        
        try {
            String instance = new LoadProperties("configuracion.properties").getPropiedad("sha.instance");
            MessageDigest md = MessageDigest.getInstance(instance);
            byte[] data = md.digest(plainText.getBytes());
            return new String(Hex.encodeHex(data));
        }
        catch (NoSuchAlgorithmException ex) {
            logger.error("Error: " + ex.getMessage());
            return null;
        }
    }
    
}
