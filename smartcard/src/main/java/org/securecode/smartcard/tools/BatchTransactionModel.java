/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package org.securecode.smartcard.tools;

import java.math.BigInteger;
import org.apache.commons.lang.StringUtils;

/**
 *
 * @author felipegiraldo
 */
public class BatchTransactionModel {
    
    private String cuentaOrigen;
    private String cuentaDestino;
    private BigInteger valor;
    private String token;

    public BatchTransactionModel() {
    }

    public BatchTransactionModel(String cuentaOrigen, String cuentaDestino, BigInteger valor, String token) {
        this.cuentaOrigen = cuentaOrigen;
        this.cuentaDestino = cuentaDestino;
        this.valor = valor;
        this.token = token;
    }

    public String getCuentaOrigen() {
        return cuentaOrigen;
    }

    public void setCuentaOrigen(String cuentaOrigen) {
        this.cuentaOrigen = cuentaOrigen;
    }

    public String getCuentaDestino() {
        return cuentaDestino;
    }

    public void setCuentaDestino(String cuentaDestino) {
        this.cuentaDestino = cuentaDestino;
    }

    public BigInteger getValor() {
        return valor;
    }

    public void setValor(BigInteger valor) {
        this.valor = valor;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }
    
    @Override
    public String toString() {
        
        if (StringUtils.isNotBlank(cuentaOrigen) && StringUtils.isNotBlank(cuentaDestino) && 
                StringUtils.isNumeric(valor.toString()) && StringUtils.isNotBlank(token))
            return cuentaOrigen + "|" + cuentaDestino + "|" + valor.toString() + "|" + token;
        else
            return null;
    }
    
}
