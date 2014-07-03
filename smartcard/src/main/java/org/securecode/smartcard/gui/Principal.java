
package org.securecode.smartcard.gui;

import org.apache.log4j.PropertyConfigurator;
import org.securecode.smartcard.tools.LoadProperties;

/**
 * Clase grafica principal
 *
 * @author Ing. Felipe Giraldo
 * @since Creado el 20 de junio de 2014
 * @version 1.0
 */
public class Principal extends javax.swing.JFrame {

    /**
     * Creates new form Principal
     */
    public Principal() {
        
        initComponents();
        setLocationRelativeTo(null);
        setResizable(false);
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
        btnGenerarBatch = new javax.swing.JButton();
        btnGenerarToken = new javax.swing.JButton();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setTitle("Smart Card Simulator");
        setName("principal"); // NOI18N

        lblFormulario.setFont(new java.awt.Font("Lucida Grande", 1, 18)); // NOI18N
        lblFormulario.setText("Smart Card Simulator");

        btnGenerarBatch.setText("Generar Archivo Batch");
        btnGenerarBatch.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGenerarBatchActionPerformed(evt);
            }
        });

        btnGenerarToken.setText("Generar Token");
        btnGenerarToken.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGenerarTokenActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(lblFormulario)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(btnGenerarBatch)
                        .addGap(18, 18, 18)
                        .addComponent(btnGenerarToken)))
                .addContainerGap(12, Short.MAX_VALUE))
        );

        layout.linkSize(javax.swing.SwingConstants.HORIZONTAL, new java.awt.Component[] {btnGenerarBatch, btnGenerarToken});

        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(lblFormulario)
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnGenerarBatch, javax.swing.GroupLayout.PREFERRED_SIZE, 50, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGenerarToken, javax.swing.GroupLayout.PREFERRED_SIZE, 50, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addContainerGap(14, Short.MAX_VALUE))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void btnGenerarBatchActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGenerarBatchActionPerformed
        // TODO add your handling code here:
        
        new DatosUsuario().setVisible(true);
        this.dispose();
    }//GEN-LAST:event_btnGenerarBatchActionPerformed

    private void btnGenerarTokenActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGenerarTokenActionPerformed
        // TODO add your handling code here:
        
        new GenerarToken().setVisible(true);
        this.dispose();
    }//GEN-LAST:event_btnGenerarTokenActionPerformed

    /**
     * @param args the command line arguments
     */
    public static void main(String args[]) {
        
        /* Set the Nimbus look and feel */
        //<editor-fold defaultstate="collapsed" desc=" Look and feel setting code (optional) ">
        /* If Nimbus (introduced in Java SE 6) is not available, stay with the default look and feel.
         * For details see http://download.oracle.com/javase/tutorial/uiswing/lookandfeel/plaf.html 
         */
        try {
            for (javax.swing.UIManager.LookAndFeelInfo info : javax.swing.UIManager.getInstalledLookAndFeels()) {
                if ("Nimbus".equals(info.getName())) {
                    javax.swing.UIManager.setLookAndFeel(info.getClassName());
                    break;
                }
            }
        } catch (ClassNotFoundException ex) {
            java.util.logging.Logger.getLogger(Principal.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (InstantiationException ex) {
            java.util.logging.Logger.getLogger(Principal.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (IllegalAccessException ex) {
            java.util.logging.Logger.getLogger(Principal.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (javax.swing.UnsupportedLookAndFeelException ex) {
            java.util.logging.Logger.getLogger(Principal.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        }
        //</editor-fold>

        PropertyConfigurator.configure(new LoadProperties("log4j.properties").getArchivoPropiedades());
        
        /* Create and display the form */
        java.awt.EventQueue.invokeLater(new Runnable() {
            public void run() {
                new Principal().setVisible(true);
            }
        });
    }

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton btnGenerarBatch;
    private javax.swing.JButton btnGenerarToken;
    private javax.swing.JLabel lblFormulario;
    // End of variables declaration//GEN-END:variables

}
