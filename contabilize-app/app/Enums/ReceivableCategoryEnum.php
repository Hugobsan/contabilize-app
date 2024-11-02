<?php

namespace App\Enums;

enum ReceivableCategoryEnum: string
{
    case SALARIO = 'salario';
    case ALUGUEL = 'aluguel';
    case INVESTIMENTO = 'investimento';
    case BONUS = 'bonus';
    case FREELANCE = 'freelance';
    case PRESENTE = 'presente';
    case HERANCA = 'heranca';
    case VENDA = 'venda';
    case REEMBOLSO = 'reembolso';
    case OUTROS = 'outros';

    /**
     * Retorna os rótulos amigáveis para exibição.
     */
    public function label(): string
    {
        return match ($this) {
            self::SALARIO => 'Salário',
            self::ALUGUEL => 'Recebimento de Aluguel',
            self::INVESTIMENTO => 'Rendimento de Investimento',
            self::BONUS => 'Bônus',
            self::FREELANCE => 'Trabalho Freelance',
            self::PRESENTE => 'Presente',
            self::HERANCA => 'Herança',
            self::VENDA => 'Venda de Produto/Bem',
            self::REEMBOLSO => 'Reembolso',
            self::OUTROS => 'Outros',
        };
    }
}
