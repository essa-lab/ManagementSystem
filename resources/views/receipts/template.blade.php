<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-box { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <h2>{{__('messages.recipt')}} #{{ $receiptData['receipt_number'] }}</h2>
        <p>{{__('messages.recipt_date')}}: {{ $receiptData['date'] }}</p>
        <p>{{__('messages.recipt_patron')}}: {{ $receiptData['customer'] }}</p>

        <table>
            <thead>


                <tr>
                    <th>{{__('messages.title')}}</th>
                    <th>{{__('messages.borrow_date')}}</th>
                    <th>{{__('messages.due_date')}}</th>
                    <th>{{__('messages.overdue_days')}}</th>

                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td>{{ $receiptData['name'] }}</td>
                        <td>{{ $receiptData['borrow_date']  }}</td>
                        <td>{{ $receiptData['due_date']  }}</td>
                        <td>{{ $receiptData['overdue_days'] }}</td>


                    </tr>
            </tbody>
        </table>

        <h2>{{__('messages.recipt_total')}}: {{ $receiptData['total'] }}</h2>
    </div>
</body>
</html>
