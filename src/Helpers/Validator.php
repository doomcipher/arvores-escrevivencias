<?php

/**
 * Helper para validações comuns
 */
class Validator {
    
    /**
     * Verificar se campos obrigatórios estão preenchidos
     * 
     * @param array $data Array com os dados
     * @param array $required Array com nomes dos campos obrigatórios
     * @throws Exception Se algum campo obrigatório estiver vazio
     */
    public static function required($data, $required = []) {
        foreach ($required as $field) {
            if (empty($data[$field] ?? null)) {
                throw new Exception("Campo obrigatório: {$field}");
            }
        }
    }

    /**
     * Validar email
     */
    public static function email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido");
        }
    }

    /**
     * Validar URL
     */
    public static function url($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("URL inválida");
        }
    }

    /**
     * Validar número inteiro
     */
    public static function integer($value) {
        if (!is_numeric($value) || $value != intval($value)) {
            throw new Exception("Deve ser um número inteiro");
        }
    }

    /**
     * Validar comprimento mínimo
     */
    public static function minLength($value, $min) {
        if (strlen($value) < $min) {
            throw new Exception("Mínimo de {$min} caracteres");
        }
    }

    /**
     * Validar comprimento máximo
     */
    public static function maxLength($value, $max) {
        if (strlen($value) > $max) {
            throw new Exception("Máximo de {$max} caracteres");
        }
    }
}

?>
