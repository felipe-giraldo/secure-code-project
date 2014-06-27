/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package org.securecode.smartcard.tools;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import org.apache.commons.codec.binary.Hex;
import org.apache.log4j.Logger;

/**
 *
 * @author andres.giraldo
 */
public class SHA {
    
    private static final Logger logger = Logger.getLogger(SHA.class);
    
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
