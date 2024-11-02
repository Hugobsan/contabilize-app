<?php

namespace App\Enums;

enum CategoryEnum: string
{
    case ALIMENTACAO = 'alimentacao';
    case MORADIA = 'moradia';
    case TRANSPORTE = 'transporte';
    case ENERGIA_ELETRICA = 'energia_eletrica';
    case AGUA_ESGOTO = 'agua_esgoto';
    case INTERNET_TELEFONIA = 'internet_telefonia';
    case EDUCACAO = 'educacao';
    case SAUDE = 'saude';
    case ENTRETENIMENTO = 'entretenimento';
    case SUPERMERCADO = 'supermercado';
    case SEGUROS = 'seguros';
    case SERVICOS_DOMESTICOS = 'servicos_domesticos';
    case MANUTENCAO_CASA = 'manutencao_casa';
    case FINANCIAMENTO = 'financiamento';
    case IMPOSTOS_TAXAS = 'impostos_taxas';
    case CUIDADOS_PESSOAIS = 'cuidados_pessoais';
    case VESTUARIO = 'vestuario';
    case LAZER_VIAGENS = 'lazer_viagens';
    case PRESENTES_DOACOES = 'presentes_doacoes';
    case OUTROS = 'outros';

    public function label(): string
    {
        return match ($this) {
            self::ALIMENTACAO => 'Alimentação',
            self::MORADIA => 'Moradia',
            self::TRANSPORTE => 'Transporte',
            self::ENERGIA_ELETRICA => 'Energia Elétrica',
            self::AGUA_ESGOTO => 'Água e Esgoto',
            self::INTERNET_TELEFONIA => 'Internet e Telefonia',
            self::EDUCACAO => 'Educação',
            self::SAUDE => 'Saúde',
            self::ENTRETENIMENTO => 'Entretenimento',
            self::SUPERMERCADO => 'Supermercado',
            self::SEGUROS => 'Seguros',
            self::SERVICOS_DOMESTICOS => 'Serviços Domésticos',
            self::MANUTENCAO_CASA => 'Manutenção da Casa',
            self::FINANCIAMENTO => 'Financiamento',
            self::IMPOSTOS_TAXAS => 'Impostos e Taxas',
            self::CUIDADOS_PESSOAIS => 'Cuidados Pessoais',
            self::VESTUARIO => 'Vestuário',
            self::LAZER_VIAGENS => 'Lazer e Viagens',
            self::PRESENTES_DOACOES => 'Presentes e Doações',
            self::OUTROS => 'Outros',
        };
    }
}
