<html lang="{{config('app.locale')}}">
<head>
    <style>
        body {
            font-family: "Open Sans", Arial, sans-serif;
            font-size: 14px;
            line-height: 22px;
            margin: 0;
            padding: 0;
        }

        table {
            background-color: transparent;
            border-collapse: collapse;
            border-spacing: 0;
            max-width: 100%;
        }

        .main {
            width: 1024px;
            margin: 0 auto;
        }

        .main_detail {
            width: 100%;
            margin: 10px auto;
            float: left;
        }

        .head_item_fl {
            width: 100%;
            float: left;
            margin-bottom: 30px;
            margin-top: -100px !important;
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
        }

        .logo_item {
            width: 50%;
            float: left
        }

        .lt_item {
            width: 50%;
            float: left;
            text-align: right;
            font-size: 18px;
            height: 68px;
            line-height: 68px;
        }

        .detail_view_item {
            float: left;
            height: auto;
            margin-bottom: 20px;
            width: 100%;
        }

        .view_title_bg td {
            background: #7fa637 none repeat scroll 0 0;
            color: #fff;
            font-weight: 700;
        }

        .view_frist {
            border: 0 !important;
            width: 50%;
            float: left;
            padding-left: 0 !important;
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            line-height: 24px;
        }

        .view_second {
            border: 0 !important;
            padding-left: 0 !important;
        }

        .detail_view_item td {
            color: #656565;
            padding: 4px 10px;
        }

        .detail_view_item table tr td {
            border: 1px solid #d6d6d5;
            font-size: 14px;
        }

        .view_bg_one {
            background: #f3f3f3;
        }

        .detail_head_titel {
            /*background: #f3f3f3;*/
            padding: 5px 5px 5px 0;
            width: 100%;
            font-size: 30px;
            height: 44px;
            line-height: 30px;
            box-sizing: border-box;
            margin-bottom: 20px;
            float: left;
        }

        .fl_right {
            float: right
        }
    </style>
</head>
<body>
<div class="main" style="margin-top:-90px !important">
    <div class="main_detail">
        <div class="detail_view_item">
            <div class="view_frist">
                <b>{{trans('sales_order.shipping_address')}}:</b><br/>
                {{ $saleorder->customer->address ?? null }}
            </div>
            <div class="view_frist">
                {{$saleorder->customer->address ?? null}}
            </div>
        </div>
        <div class="detail_head_titel">{{trans('sales_order.sales_order_no')}} {{$saleorder->sale_number}}</div>
        <div class="detail_view_item">
            <div style="width:250px; float:left;">
                <span><b>{{trans('quotation.customer')}}:</b><br>{{ is_null($saleorder->customer)?"":$saleorder->customer->full_name }}</span>
            </div>
            <div style="width:250px; float:left;">
                <span><b>{{trans('quotation.date')}}:</b><br>{{ $saleorder->start_date}}</span>
            </div>
            <div style="width:250px; float:left;">
                <span><b>{{trans('quotation.exp_date')}}:</b><br>{{ $saleorder->expire_date}}</span>
            </div>
            <div style="width:250px; float:left;">
                <span><b>{{trans('quotation.payment_term')}}:</b><br>{{ $saleorder->payment_term.' '.trans('quotation.days') }}</span>
            </div>
            <div style="width:250px; float:left;">
                <span><b>{{trans('quotation.sales_team_id')}}:</b><br>{{ is_null($saleorder->salesTeam)?"":$saleorder->salesTeam->salesteam }}</span>
            </div>
            <div style="width:250px; float:left;">
                <span><b>{{trans('quotation.sales_person')}}:</b><br> {{ is_null($saleorder->salesPerson)?"":$saleorder->salesPerson->full_name }}</span>
            </div>
        </div>
        <div class="detail_view_item">
            {{trans('sales_order.products')}}
            <table width="100%" cellspacing="0" cellpadding="0" border="">
                <tbody>
                <tr>
                    <td><b>{{trans('sales_order.product')}}</b></td>
                    <td><b>{{trans('sales_order.quantity')}}</b></td>
                    <td><b>{{trans('sales_order.unit_price')}}</b></td>
                    <td><b>{{trans('sales_order.taxes')}}</b></td>
                    <td><b>{{trans('sales_order.subtotal')}}</b></td>
                </tr>
                @foreach ($saleorder->products as $qo_product)
                <tr>
                    <td>{{$qo_product->product_name}}</td>
                    <td>{{ $qo_product->quantity}}</td>
                    <td>{{ $qo_product->price}}</td>
                    <td>{{ number_format($qo_product->quantity * $qo_product->price * $sales_tax / 100, 2,
                        '.', '')}}
                    </td>
                    <td>{{ $qo_product->sub_total }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="detail_view_item" style="float:right; text-align:right; width:400px;">
            <table width="100%" cellspacing="0" cellpadding="0" border="" class="pull-right;">
                <tbody>
                <tr>
                    <td style="width:50%;"><b>{{trans('sales_order.untaxed_amount')}}</b></td>
                    <td>{{ $saleorder->total }}</td>
                </tr>
                <tr>
                    <td>{{trans('sales_order.taxes')}}</td>
                    <td>{{ $saleorder->tax_amount }}</td>
                </tr>
                <tr>
                    <td><b>{{trans('sales_order.total')}}</b></td>
                    <td>{{ $saleorder->grand_total }}</td>
                </tr>
                <tr>
                    <td>{{trans('sales_order.discount')}}</td>
                    <td>{{ $saleorder->discount }}</td>
                </tr>
                <tr>
                    <td><b>{{trans('sales_order.final_price')}}</b></td>
                    <td>{{ $saleorder->final_price }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
