<!DOCTYPE html>
<html>
<head>
    <title>Scrap Purchases</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Scrap Purchases</h1>
    <table>
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Scrap</th>
                <th>Weight (kg)</th>
                <th>Amount/Kg</th>
                <th>Waste %</th>
                <th>Weight with waste</th>
                <th>Total Amount</th>
                <th>Waste Amount</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($scrap_purchases as $purchase)
                <tr>
                    <td>{{ $purchase->supplier->name }}</td>
                    <td>{{ $purchase->scrap_name }}</td>
                    <td>{{ $purchase->weight }}</td>
                    <td>{{ $purchase->amount_per_kg }}</td>
                    <td>{{ $purchase->waste_percentage }}%</td>
                    <td>{{ $purchase->weight_without_waste }}</td>
                    <td>{{ $purchase->total_amount }}</td>
                    <td>{{ $purchase->total_amount_with_waste }}</td>
                    <td>{{ number_format($purchase->grand_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
