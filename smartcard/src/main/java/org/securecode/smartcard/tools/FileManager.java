/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package org.securecode.smartcard.tools;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.OutputStream;
import java.io.PrintWriter;
import org.apache.log4j.Logger;

/**
 *
 * @author felipegiraldo
 */
public class FileManager {
    
    private final Logger logger = Logger.getLogger(FileManager.class);
    
    public BufferedReader readFromFile(String fileName) {
        
        File file = new File(fileName);
        BufferedReader buffer = null;
        try {
            buffer = new BufferedReader(new FileReader(file));
            return buffer;
        }
        catch (FileNotFoundException ex) {
            logger.error("Error: " + ex.getMessage());
            return null;
        }
    }
    
    public void writeToFile(String fileName, String line) {
        
        File file = new File(fileName);
        FileWriter writer = null;
        BufferedWriter buffer = null;
        PrintWriter print = null;
        try {
            writer = new FileWriter(file, true);
            buffer = new BufferedWriter(writer);
            print = new PrintWriter(buffer);
            print.write(line + "\n");
        }
        catch (IOException ex) {
            logger.error("Error: " + ex.getMessage());
        }
        finally {
            if (print != null)
                print.close();
            try {
                if (buffer != null)
                    buffer.close();
                if (writer != null)
                    writer.close();
            } catch (IOException ex) {
                logger.error("Error: " + ex.getMessage());
            }
        }
    }
    
    public byte[] readFileToByte(String fileName) {
        
        byte[] bytes = null;
        File file = new File(fileName);
        FileInputStream fis = null;
        BufferedInputStream bis = null;
        byte[] buffer = new byte[32 * 1024];
        int read = 0;
        ByteArrayOutputStream bos = new ByteArrayOutputStream();

        try {
            fis = new FileInputStream(file);
            bis = new BufferedInputStream(fis);
            while ((read = bis.read(buffer)) >= 0) {
                bos.write(buffer, 0, read);
            }
            bytes = bos.toByteArray();
            bos.reset();
            bos.close();
        }
        catch (IOException ex) {
            logger.error("Error: " + ex);
        }
        finally {
            if (bis != null) {
                try {
                    bis.close();
                } catch (IOException ex) {
                    logger.error("Error: " + ex);
                }
            }
        }
        return bytes;
    }
    
    public boolean writeByteToFile(byte[] bytes, String fileName) {
        
        boolean correcto = false;
        
        try {
            OutputStream out = new FileOutputStream(fileName);
            out.write(bytes);
            out.close();
            correcto = true;
        }
        catch (IOException ex) {
            logger.error("Error: " + ex.getMessage());
        }
        return correcto;

    }

}