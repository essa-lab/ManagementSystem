<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Books</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-box { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <h2>{{__('messages.borrow_resource')}}</h2>
        {{-- <p>Date: {{ $receiptData['date'] }}</p>
        <p>Patron: {{ $receiptData['customer'] }}</p> --}}

        <table>
            <thead>
                

                <tr>
                    <th>{{__('messages.title')}}</th>
                    <th>{{__('messages.type')}}</th>
                    <th>{{__('messages.patron_report')}}</th>
                    <th>{{__('messages.borrow_date')}}</th>
                    <th>{{__('messages.due_date')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($circulation as $item)
                        @php
                        
        $type =class_basename($item->resourceCopy->resource?->resourceable_type);

@endphp
@if($item->resourceCopy->resource)

                    <tr>
                        <td>{{$item->resourceCopy->resource->{'title_' . app()->getLocale()}
                            }}</td>
                        <td>{{$type}}</td>
                        <td>{{$item->patron->name }}</td>
                        <td>{{$item->borrow_date?->format('y-m-d') }}</td>
                        <td>{{$item->due_date?->format('y-m-d')     }}</td>
                    </tr>
                    @endif
                    @endforeach

            </tbody>
        </table>

    </div>
</body>
</html>
