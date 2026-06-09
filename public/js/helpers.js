function formatarTelefone(numero) {
    if (!numero) return '';
    
    numero = numero.toString().replace(/\D/g, '');
    
    if (numero.length === 11) {
        return numero.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (numero.length === 10) {
        return numero.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
    }
    
    return numero;
}

function formatarCNPJ(numero) {
    if (!numero) return '';
    
    numero = numero.toString().replace(/\D/g, '');
    
    if (numero.length === 14) {
        return numero.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
    }
    
    return numero;
}

function removerFormatacao(valor) {
    if (!valor) return '';
    return valor.toString().replace(/\D/g, '');
}