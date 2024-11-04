<!DOCTYPE html>
<html lang="en">
@php
    $dados = $reportData;
    $balance = $dados['balance'];
    $monthlyExpenses = $dados['monthlyExpenses'];
    $monthlyRecives = $dados['monthlyRecives'];
    $balanceEvolution = $dados['balanceEvolution'];
    $transactions = $dados['transactions'];
@endphp

<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Relatório Financeiro</h1>
        <p>Usuário: {{ Auth::user()->name }}</p>
        <p>Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}</p>
        <p>Saldo Total: R$ {{ number_format($balance['totalReceivable'] - $balance['totalPayable'], 2, ',', '.') }}</p>
        <p>Receitas Totais: R$ {{ number_format($balance['totalReceivable'], 2, ',', '.') }}</p>
        <p>Despesas Totais: R$ {{ number_format($balance['totalPayable'], 2, ',', '.') }}</p>
    </div>

    <div>
        <h2 class="section-title">Despesas Mensais por Categoria</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthlyExpenses as $expense)
                    <tr>
                        <td>{{ $expense->category->label() }}</td>
                        <td>R$ {{ number_format($expense->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="section-title">Receitas Mensais por Categoria</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthlyRecives as $income)
                    <tr>
                        <td>{{ $income->category->label() }}</td>
                        <td>R$ {{ number_format($income->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="section-title">Evolução do Saldo</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Saldo (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($balanceEvolution as $entry)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                        <td>R$ {{ number_format($entry['balance'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="section-title">Transações de Cartão de Crédito</h2>
        <table>
            <thead>
                <tr>
                    <th>Cartão</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                {{-- {{dd($transactions)}} --}}
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->creditCard->nickname }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->purchase_date)->format('d/m/Y') }}</td>
                        <td>R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
