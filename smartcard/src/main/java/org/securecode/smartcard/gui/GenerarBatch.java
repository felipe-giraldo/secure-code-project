/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package org.securecode.smartcard.gui;

import java.math.BigInteger;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;
import org.apache.commons.lang.StringUtils;
import org.apache.log4j.Logger;
import org.securecode.smartcard.tools.AES;
import org.securecode.smartcard.tools.BatchTransactionModel;
import org.securecode.smartcard.tools.FileManager;
import org.securecode.smartcard.tools.LoadProperties;

/**
 *
 * @author felipegiraldo
 */
public class GenerarBatch extends javax.swing.JFrame {

    private static final Logger logger = Logger.getLogger(GenerarBatch.class);
    private static final LoadProperties props = new LoadProperties("configuracion.properties");
    private static boolean print = false;
    private String key;
    
    /**
     * Creates new form GenerarBatch
     */
    public GenerarBatch(String key) {
        
        initComponents();
        setLocationRelativeTo(null);
        setResizable(false);
        this.key = key;
    }

    /**
     * This method is called from within the constructor to initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is always
     * regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        lblFormulario = new javax.swing.JLabel();
        lblArchivoSalida = new javax.swing.JLabel();
        tFldArchivoSalida = new javax.swing.JTextField();
        btnGenerar = new javax.swing.JButton();
        btnLimpiar = new javax.swing.JButton();
        btnVolver = new javax.swing.JButton();
        scrPnlTabla = new javax.swing.JScrollPane();
        tblDatos = new javax.swing.JTable();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setTitle("Generador de Archivos Batch");

        lblFormulario.setFont(new java.awt.Font("Lucida Grande", 1, 18)); // NOI18N
        lblFormulario.setText("Generar Archivo Batch");
        lblFormulario.setFocusable(false);

        lblArchivoSalida.setText("Archivo de salida");
        lblArchivoSalida.setFocusable(false);

        tFldArchivoSalida.setText("movements.txt");
        tFldArchivoSalida.setToolTipText("Ingrese aquí la ruta absoluta del archivo de salida");
        tFldArchivoSalida.setNextFocusableComponent(btnGenerar);

        btnGenerar.setText("Generar");
        btnGenerar.setNextFocusableComponent(btnLimpiar);
        btnGenerar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGenerarActionPerformed(evt);
            }
        });

        btnLimpiar.setText("Limpiar");
        btnLimpiar.setNextFocusableComponent(btnVolver);
        btnLimpiar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnLimpiarActionPerformed(evt);
            }
        });

        btnVolver.setText("Volver");
        btnVolver.setNextFocusableComponent(tblDatos);
        btnVolver.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnVolverActionPerformed(evt);
            }
        });

        tblDatos.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null}
            },
            new String [] {
                "C. Origen", "C. Destino", "Valor", "Token"
            }
        ) {
            Class[] types = new Class [] {
                java.lang.String.class, java.lang.String.class, java.lang.Integer.class, java.lang.String.class
            };

            public Class getColumnClass(int columnIndex) {
                return types [columnIndex];
            }
        });
        tblDatos.setNextFocusableComponent(tFldArchivoSalida);
        scrPnlTabla.setViewportView(tblDatos);
        tblDatos.getColumnModel().getColumn(0).setResizable(false);
        tblDatos.getColumnModel().getColumn(1).setResizable(false);
        tblDatos.getColumnModel().getColumn(2).setResizable(false);
        tblDatos.getColumnModel().getColumn(3).setResizable(false);

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(scrPnlTabla)
                    .addComponent(lblFormulario)
                    .addComponent(lblArchivoSalida)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(tFldArchivoSalida, javax.swing.GroupLayout.PREFERRED_SIZE, 300, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(btnGenerar)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(btnLimpiar)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(btnVolver)))
                .addContainerGap())
        );

        layout.linkSize(javax.swing.SwingConstants.HORIZONTAL, new java.awt.Component[] {btnGenerar, btnLimpiar, btnVolver});

        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(lblFormulario)
                .addGap(18, 18, 18)
                .addComponent(lblArchivoSalida)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(tFldArchivoSalida, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGenerar)
                    .addComponent(btnLimpiar)
                    .addComponent(btnVolver))
                .addGap(18, 18, 18)
                .addComponent(scrPnlTabla, javax.swing.GroupLayout.DEFAULT_SIZE, 378, Short.MAX_VALUE)
                .addContainerGap())
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void btnGenerarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGenerarActionPerformed
        // TODO add your handling code here:
        
        String fileName = tFldArchivoSalida.getText();
        if (StringUtils.isBlank(fileName)) {
            JOptionPane.showMessageDialog(null, "El nombre de archivo no puede estar vacio", "Error", JOptionPane.ERROR_MESSAGE);
            return;
        }
        
        // Leer los datos de la tabla
        DefaultTableModel modelo = (DefaultTableModel) tblDatos.getModel();
        StringBuilder output = new StringBuilder();
        for (int i = 0; i < modelo.getRowCount() && modelo.getValueAt(i, 0) != null; i ++) {
            BatchTransactionModel item = new BatchTransactionModel();
            item.setCuentaOrigen(modelo.getValueAt(i, 0).toString());
            item.setCuentaDestino(modelo.getValueAt(i, 1).toString());
            item.setValor(new BigInteger(modelo.getValueAt(i, 2).toString()));
            item.setToken(modelo.getValueAt(i, 3).toString());
            output.append(item.toString());
            output.append("||");
        }
        if (print)
            logger.debug("String original: " + output.toString());
        
        // Completa la cadena hasta multiplos de 16 caracteres
        while (output.length() % 16 > 0) {
            output.append(props.getPropiedad("padding"));
        }
        if (print)
            logger.debug("String con padding: " + output.toString());
        
        // Cifra la cadena para guardar en el archivo
        byte[] cifrado = AES.encrypt(output.toString(), key);
        if (print) {
            logger.debug("String cifrada: " + cifrado);
            logger.debug("String descifrado: " + AES.decrypt(cifrado, key));
        }
        
        // Escribe la cadena cifrada en el archivo
        FileManager manager = new FileManager();
        manager.writeByteToFile(cifrado, fileName);
        
        // Recupera el contenido del archivo
        byte[] leido = manager.readFileToByte(fileName);
        if (print) {
            logger.debug("Leido del archivo: " + leido);
            logger.debug("String descifrado: " + AES.decrypt(leido, key));
        }
    }//GEN-LAST:event_btnGenerarActionPerformed

    private void btnLimpiarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnLimpiarActionPerformed
        // TODO add your handling code here:
        
        tFldArchivoSalida.setText("");
        DefaultTableModel modelo = (DefaultTableModel) tblDatos.getModel();
        modelo.getDataVector().removeAllElements();
    }//GEN-LAST:event_btnLimpiarActionPerformed

    private void btnVolverActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnVolverActionPerformed
        // TODO add your handling code here:
        
        new Principal().setVisible(true);
        this.dispose();
    }//GEN-LAST:event_btnVolverActionPerformed

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton btnGenerar;
    private javax.swing.JButton btnLimpiar;
    private javax.swing.JButton btnVolver;
    private javax.swing.JLabel lblArchivoSalida;
    private javax.swing.JLabel lblFormulario;
    private javax.swing.JScrollPane scrPnlTabla;
    private javax.swing.JTextField tFldArchivoSalida;
    private javax.swing.JTable tblDatos;
    // End of variables declaration//GEN-END:variables

}
