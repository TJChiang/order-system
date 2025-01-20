<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shipment_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>出貨單</h1>

    <h2>訂單資訊</h2>
    <div>
        <table>
            <tr>
                <th>訂單號碼:</th>
                <th>{{ $order_number}}</th>
                <th>訂單日期:</th>
                <th>{{ $order_date }}</th>
            </tr>
            <tr>
                <th>收件人:</th>
                <th>{{ $recipient_name }}</th>
                <th>電話:</th>
                <th>{{ $recipient_phone }}</th>
            </tr>
            <tr>
                <th>收件地址:</th>
                <th colspan="3">{{ $shipping_address }}</th>
            </tr>
        </table>
    </div>

    <h2>出貨單號碼: {{ $shipment_number }}</h2>
    <p>配送: {{ $courier}}</p>
    <p>追蹤碼: {{ $tracking_number }}</p>
    <p>寄送時間: {{ $shipping_date }}</p>
    <p>出貨狀態: {{ $status }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>名稱</th>
                <th>數量</th>
                <th>單價</th>
                <th>小計</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order_items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['price'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['subtotal'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3">總計</td>
                <td>{{ $total_quantity }}</td>
                <td>{{ $total_price }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
