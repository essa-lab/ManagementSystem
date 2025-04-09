<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Book</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-box { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <h2>{{__('messages.available_resource')}}</h2>
        {{-- <p>Date: {{ $receiptData['date'] }}</p>
        <p>Patron: {{ $receiptData['customer'] }}</p> --}}

        <table>
            <thead>
                <tr>
                    <th>{{__('messages.title')}}</th>
                    <th>{{__('messages.type')}}</th>
                    <th>{{__('messages.locked')}}</th>
                    <th>{{__('messages.shelf_number')}}</th>
                    <th>{{__('messages.storage_location')}}</th>
                    <th>{{__('messages.barcode')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resourceCopy as $item)
                @php
                        

                       
                        
        $type =class_basename($item->resource?->resourceable_type);

                    @endphp
                    @if($item->resource)
                    <tr>

                        <td>{{$item->resource->{'title_' . app()->getLocale()}
                            }}</td>
                        <td>{{$type}}</td>
                        <td>{{$item->Locked}}</td>
                        <td>{{$item->shelf_number}}</td>
                        <td>{{$item->storage_location}}</td>
                        <td>{{$item->barcode}}</td>
                    

                    </tr>
                    @endif
                    @endforeach

            </tbody>
        </table>

    </div>
</body>
</html>
