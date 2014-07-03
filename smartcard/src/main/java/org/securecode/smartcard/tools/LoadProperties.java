
package org.securecode.smartcard.tools;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;
import org.apache.log4j.Logger;

/**
 * Clase encargada de cargar archivos de propiedades
 *
 * @author Ing. Felipe Giraldo
 * @since Creado el 20 de junio de 2014
 * @version 1.0
 */
public class LoadProperties {

    /**
     * Logger de clase
     */
    private static final Logger logger = Logger.getLogger(AES.class);
    /**
     * Atributo que representa el nombre del archivo que contiene las propiedades
     */
    private String nombreArchivo;
    /**
     * Atributo que representa las propiedades dispuestas en llaves para su uso
     */
    private Properties archivoPropiedades;

    /**
     * Crea una nueva instancia de LoadProperties
     *
     * @param nombreArchivo  Nombre del archivo que contiene las propiedades
     */
    public LoadProperties(String nombreArchivo) {

        this.nombreArchivo = nombreArchivo;
        String separador = System.getProperty("file.separator");
        String rutaProperties = System.getProperty("user.dir") + separador + "properties" + separador;
        try {
            this.archivoPropiedades = new Properties();
            this.archivoPropiedades.load(new FileInputStream(rutaProperties + this.nombreArchivo));
        } catch (FileNotFoundException ex) {
            System.out.println("ERROR AL CARGAR EL ARCHIVO DE PROPIEDADES: " + this.nombreArchivo);
            logger.error("Error: " + ex.getMessage());
        } catch (IOException ex) {
            System.out.println("ERROR AL CARGAR EL ARCHIVO DE PROPIEDADES: " + this.nombreArchivo);
            logger.error("Error: " + ex.getMessage());
        }
    }

    /**
     * Crea una nueva instancia de LoadProperties
     *
     * @param nombreArchivo  Nombre del archivo que contiene las propiedades
     * @param tipoAplicacion  Especifica el tipo de aplicacion (1 - Escritorio, 2 - Web)
     */
    public LoadProperties(String nombreArchivo, int tipoAplicacion) {

        this.nombreArchivo = nombreArchivo;
        String separador = System.getProperty("file.separator");
        try {
            if (tipoAplicacion == 1) {
                String rutaProperties = separador + "properties" + separador;
                this.archivoPropiedades = new Properties();
                this.archivoPropiedades.load(new FileInputStream(System.getProperty("user.dir") + rutaProperties + this.nombreArchivo));
            }
            else if(tipoAplicacion == 2) {
                String rutaProperties = ".." + separador + "properties" + separador + this.nombreArchivo;
                this.archivoPropiedades = new Properties();
                this.archivoPropiedades.load(this.getClass().getResourceAsStream(rutaProperties));
            }
            else {
                System.out.println("ERROR AL DEFINIR EL TIPO DE APLICACION");
            }
        } catch (FileNotFoundException ex) {
            System.out.println("ERROR AL CARGAR EL ARCHIVO DE PROPIEDADES: " + this.nombreArchivo);
            logger.error("Error: " + ex.getMessage());
        } catch (IOException ex) {
            System.out.println("ERROR AL CARGAR EL ARCHIVO DE PROPIEDADES: " + this.nombreArchivo);
            logger.error("Error: " + ex.getMessage());
        }
    }

    /**
     * Retorna el nombre del archivo de propiedades
     *
     * @return  Retorna un String con el nombre del archivo de propiedades
     */
    public String getNombreArchivo() {

        return this.nombreArchivo;
    }

    /**
     * Retorna el archivo de propiedades
     *
     * @return  Retorna un Properties con el archivo de propiedades
     */
    public Properties getArchivoPropiedades() {

        return this.archivoPropiedades;
    }

    /**
     * Retorna una propiedad especifica
     *
     * @param propiedad Nombre de la propiedad que desea obtener
     * @return  Retorna un String con el valor de la propiedad
     */
    public String getPropiedad(String propiedad) {

        return this.archivoPropiedades.getProperty(propiedad);
    }

    /**
     * Modifica una propiedad especifica
     *
     * @param propiedad Nombre de la propiedad que desea modificar
     * @param dato Dato correspondiente 
     * @return  Retorna verdadero o falso
     */
    public boolean setPropiedad(String propiedad, String dato) {

        String separador = System.getProperty("file.separator");
        String rutaProperties = System.getProperty("user.dir") + separador + "properties" + separador;
        try {
            FileOutputStream ops = new FileOutputStream(rutaProperties + this.nombreArchivo);
            this.archivoPropiedades.setProperty(propiedad, dato);
            this.archivoPropiedades.store(ops, dato);
            ops.close();
        } catch (FileNotFoundException ex) {
            System.out.println("ERROR AL CARGAR EL ARCHIVO DE PROPIEDADES: " + this.nombreArchivo);
            logger.error("Error: " + ex.getMessage());
        } catch (IOException ex) {
            System.out.println("ERROR AL CARGAR EL ARCHIVO DE PROPIEDADES: " + this.nombreArchivo);
            logger.error("Error: " + ex.getMessage());
        }
        return true;
    }

    /**
     * Retorna el archivo de propiedades dentro de un Map
     *
     * @return  Retorna un Map con los valores del archivo de propiedades
     */
    public Map<String, String> getMapDePropiedades() {

        return new HashMap<String, String>((Map) this.archivoPropiedades);
    }

}