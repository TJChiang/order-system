<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shipment_number }}</title>
    <style>
        @font-face {
            font-weight: normal;
            font-family: 'Noto Sans TC';
            src: url({{ storage_path('fonts/NotoSansTC/NotoSansTC-Regular.ttf') }}) format('truetype');
            font-style: normal;
        }
        @font-face {
            font-weight: bold;
            font-family: 'Noto Sans TC';
            src: url({{ storage_path('fonts/NotoSansTC/NotoSansTC-Bold.ttf') }}) format('truetype');
            font-style: normal;
        }
        body {
            font-family: 'Noto Sans TC', DejaVu Sans, sans-serif;
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
            <tbody>
                <tr>
                    <th>訂單號碼:</th>
                    <td>{{ $order_number}}</td>
                    <th>訂單日期:</th>
                    <td>{{ $order_date }}</td>
                </tr>
                <tr>
                    <th>收件人:</th>
                    <td>{{ $recipient_name }}</td>
                    <th>電話:</th>
                    <td>{{ $recipient_phone }}</td>
                </tr>
                <tr>
                    <th>收件地址:</th>
                    <td colspan="3">{{ $shipping_address }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2>出貨單資訊</h2>
    <div>
        <table>
            <tbody>
                <tr>
                    <th>出貨單號碼:</th>
                    <td colspan="3">{{ $shipment_number }}</td>
                </tr>
                <tr>
                    <th>配送:</th>
                    <td>{{ $courier}}</td>
                    <th>追蹤碼:</th>
                    <td>{{ $tracking_number }}</td>
                </tr>
                <tr>
                    <th>寄送時間:</th>
                    <td>{{ $shipping_date }}</td>
                    <th>出貨狀態:</th>
                    <td>{{ $status }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3>商品資訊</h3>
    <div>
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
    </div>
</body>
</html>
