<?php

namespace App\Helpers;

class Formatador
{
    // Formata telefone
    public static function telefone($numero)
    {
        if (!$numero) {
            return 'Não informado';
        }

        $numero = preg_replace('/\D/', '', $numero);
        
        if (strlen($numero) == 11) {
            // Celular: (XX) XXXXX-XXXX
            return sprintf(
                '(%s) %s-%s',
                substr($numero, 0, 2),
                substr($numero, 2, 5),
                substr($numero, 7, 4)
            );
        } elseif (strlen($numero) == 10) {
            // Fixo: (XX) XXXX-XXXX
            return sprintf(
                '(%s) %s-%s',
                substr($numero, 0, 2),
                substr($numero, 2, 4),
                substr($numero, 6, 4)
            );
        }
        
        return $numero;
    }

    // Formata CNPJ
    public static function cnpj($numero)
    {
        if (!$numero) {
            return 'Não informado';
        }

        $numero = preg_replace('/\D/', '', $numero);
        
        if (strlen($numero) == 14) {
            return sprintf(
                '%s.%s.%s/%s-%s',
                substr($numero, 0, 2),
                substr($numero, 2, 3),
                substr($numero, 5, 3),
                substr($numero, 8, 4),
                substr($numero, 12, 2)
            );
        }
        
        return $numero;
    }

}