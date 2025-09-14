<?php
namespace App\Model\Enum;

/**
 * Enumeração para os tipos de contato.
 * Define constantes para diferenciar entre Telefone e Email,
 * além de fornecer métodos auxiliares para exibição e listagem.
 */
class ContatoEnum {

    /** @var bool Representa o tipo Telefone */
    const TELEFONE = false;

    /** @var bool Representa o tipo Email */
    const EMAIL = true;

    /**
     * Retorna a lista de tipos de contato disponíveis.
     *
     * @return array Lista no formato [valor => label]
     */
    public static function getTipoContatoList(): array {
        return [
            self::TELEFONE => 'Telefone',
            self::EMAIL => 'Email'
        ];
    }

    /**
     * Retorna o rótulo (label) correspondente ao valor informado.
     *
     * @param bool|null $value Valor a ser mapeado
     * @return string Label correspondente ao valor
     */
    public static function getLabel(bool $value): string {
        return match ($value) {
            true => 'Email',
            false => 'Telefone'
        };
    }
}
