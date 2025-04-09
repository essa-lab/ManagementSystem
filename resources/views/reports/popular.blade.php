<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Books</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-box { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <h2>{{__('messages.popular_resource')}}</h2>
        {{-- <p>Date: {{ $receiptData['date'] }}</p>
        <p>Patron: {{ $receiptData['customer'] }}</p> --}}

        <table>
            <thead>
                <tr>

                    <th>{{__('messages.title')}}</th>
                    <th>{{__('messages.type')}}</th>
                    <th>{{__('messages.circulated')}}</th>
                    


                </tr>
            </thead>
            <tbody>
                @foreach ($circulation as $item)
                        @php

        $type =class_basename($item->resourceable_type);

@endphp
                    <tr>
                        

                        

                        <td>{{$item->{'title_' . app()->getLocale()}
                            }}</td>

                        <td>{{$type}}</td>
                        <td>{{$item->circulation_count}}</td>               


                    </tr>
                    @endforeach

            </tbody>
        </table>

    </div>
</body>
</html>
