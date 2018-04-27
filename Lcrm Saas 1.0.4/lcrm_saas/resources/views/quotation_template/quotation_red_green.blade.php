<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <title>{{ trans('quotation.quotation') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="content-type" content="text-html; charset=utf-8">
    <style type="text/css">
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-family: DejaVu Sans;
            font-size: 100%;
            vertical-align: baseline;
        }

        html {
            line-height: 1;
        }

        ol, ul {
            list-style: none;
        }

        a img {
            border: none;
        }

        article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary {
            display: block;
        }

        body {
            font-family: DejaVu Sans;
            font-weight: 300;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #777777;
        }

        body a {
            text-decoration: none;
            color: inherit;
        }

        body a:hover {
            color: inherit;
            opacity: 0.7;
        }

        body .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }


        header {
            margin-top: 20px;
            margin-bottom: 30px;
            padding: 0 5px 0;
        }

        header img {
            width: 80px;
            margin-right: 10px;
        }

        header figure img {
            height: 40px;
        }

        header .company-info {
            color: #BDB9B9;
        }

        header .company-info .title {
            margin-bottom: 5px;
            color: #fc4141;
            font-weight: 600;
            font-size: 2em;
        }
        section table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            font-size: 0.9166em;
        }


        section table tbody.head {
            vertical-align: middle;
        }

        section table tbody.head th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
        }

        section table tbody.body tr.total .total {
            font-size: 1.18181818181818em;
            font-weight: 600;
            color: #fc4141;
        }
        table th,table td{
            width: 15%;
        }
        table .head .unit,table .head .total,table .unit,table .total{
            text-align: right;
        }
        table .head th{
            padding: 8px 10px;
            background: #fc4141;
            border-bottom: 5px solid #FFFFFF;
            border-right: 4px solid #FFFFFF;
            color: white;
            font-weight: 400;
            text-transform: uppercase;
        }
        table .body td{
            padding: 15px 10px;
            background: #b1d785;
            border-bottom: 5px solid #FFFFFF;
            border-right: 4px solid #FFFFFF;
            color: white;
        }
        table.grand-total{
            background: #b1d785;
        }
        .grand-total td{
            padding: 10px 10px;
            background: #b1d785;
            color: white;
        }
        .grand-total .no,.grand-total .desc,.grand-total .qty{
            background-color: #fff;
        }
        .bg-white{
            background-color: #fff !important;
        }
        .text-right{
            text-align: right !important;
        }
        .text-left{
            text-align: left;
        }
        .m-t-20{
            margin-top: 20px;
        }
        .m-t-30{
            margin-top: 30px;
        }
        .px-30{
            padding:0 30px;
        }
    </style>
</head>

<body>
<header class="clearfix">
    <div class="px-30">
        <div class="row">
            <div class="col-12">
                <img class="logo" src="{{ url((isset($pdf_logo)?$pdf_logo:$settings['site_logo'])) }}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="company-info col-12">
                <h2 class="title">{{$settings['site_name']}}</h2>
                <div>
                    <span>{{ config('settings.address1') }}</span>
                </div>
                <div>
                    <span>{{ config('settings.address2') }}</span>
                </div>
                <div>
                    {{ config('settings.phone') }} | {{ config('settings.fax') }}
                </div>
                <div>
                    {{ config('settings.site_email') }}
                </div>
            </div>
        </div>
    </div>
</header>

<section>
    <div class="px-30">
        <div class="row">
            <div class="col-12">
                <div class="details">
                    <div>
                        {{trans('quotation.quotation_to')}}
                    </div>
                    <table class="m-t-20">
                        <thead class="head">
                        <tr>
                            <th class="text-left">{{trans('quotation.company_id')}}</th>
                            <th class="text-left">{{trans('quotation.address')}}</th>
                            <th class="text-left">{{trans('quotation.email')}}</th>
                            <th class="text-left">{{trans('quotation.quotation_no')}}</th>
                            <th class="text-left">{{trans('quotation.date')}}</th>
                            <th class="text-left">{{trans('quotation.exp_date')}}</th>
                        </tr>
                        </thead>
                        <tbody class="body">
                        <tr>
                            <td class="text-left">{{ is_null($quotation->companies)?"":$quotation->companies->name }}</td>
                            <td>{{isset($quotation->companies)?$quotation->companies->address:null}}</td>
                            <td class="text-left">{{is_null($quotation->companies)?"":$quotation->companies->email}}</td>
                            <td class="text-left">{{$quotation->quotations_number}}</td>
                            <td class="text-left">{{ $quotation->start_date}}</td>
                            <td class="text-left">{{ $quotation->expire_date}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix m-t-30">
            <table>
                <thead class="head">
                <tr>
                    <th class="no text-left">No</th>
                    <th class="desc text-left">
                        {{trans('invoice.product')}}
                    </th>
                    <th class="qty text-left">
                        {{trans('invoice.quantity')}}
                    </th>
                    <th class="unit">
                        {{trans('invoice.unit_price')}}
                    </th>
                    <th class="total">
                        {{trans('invoice.subtotal')}}
                    </th>
                </tr>
                </thead>
                <tbody class="body">
                @foreach ($quotation->quotationProducts as $key => $qo_product)
                    <tr>
                        <td class="no text-left">{{($key+1)}}</td>
                        <td class="desc text-left">{{$qo_product->product_name}}</td>
                        <td class="qty text-left">{{ isset($qo_product->pivot->quantity)?$qo_product->pivot->quantity:null}}</td>
                        <td class="unit">{{ isset($qo_product->pivot->price)?$qo_product->pivot->price:null }}</td>
                        <td class="total">{{ isset($qo_product->pivot)?$qo_product->pivot->quantity*$qo_product->pivot->price:null }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="no-break">
            <table class="grand-total">
                <tbody>
                <tr>
                    <td class="no"></td>
                    <td class="desc"></td>
                    <td class="qty"></td>
                    <td class="unit">{{trans('invoice.untaxed_amount')}} :</td>
                    <td class="total">
                        {{$organizationSettings['currency'].' '.$quotation->total}}
                    </td>
                </tr>
                <tr>
                    <td class="no"></td>
                    <td class="desc"></td>
                    <td class="qty"></td>
                    <td class="unit">{{trans('invoice.discount').' '.$quotation->discount}}% :</td>
                    <td class="total">{{$quotation->total*($quotation->discount/100)}}</td>
                </tr>
                <tr>
                    <td class="no"></td>
                    <td class="desc"></td>
                    <td class="qty"></td>
                    <td class="unit">{{trans('invoice.total')}} :</td>
                    <td class="total">
                        {{$organizationSettings['currency'].' '.$quotation->grand_total}}
                    </td>
                </tr>
                <tr>
                    <td class="no"></td>
                    <td class="desc"></td>
                    <td class="qty"></td>
                    <td class="unit">{{trans('invoice.taxes')}} :</td>
                    <td class="total">{{$organizationSettings['currency'].' '.$quotation->tax_amount}}</td>
                </tr>
                <tr>
                    <td colspan="3" class="bg-white"></td>
                    <td class="unit">
                        <span>{{trans('quotation.vat_amount')}} :</span>
                    </td>
                    <td class="total">
                        {{$organizationSettings['currency'].' '.$quotation->vat_amount}}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="bg-white"></td>
                    <td class="grand-total text-right">
                        <div>
                            <span>{{trans('invoice.final_price')}} :</span>
                        </div>
                    </td>
                    <td class="total">
                        {{$organizationSettings['currency'].' '.$quotation->final_price}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="m-t-20">
            <div class="form-group">
                <div>
                    {{ trans('dashboard.terms_and_conditions') }} :
                </div>
                {{ $quotation->terms_and_conditions }}
            </div>
        </div>
    </div>
</section>
</body>

</html>
