<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
     @if(isset($footerContainQrCode) && $footerContainQrCode)
        <style>
          @page {
              margin-left: 0;
              margin-right: 0;
              margin-top: 110px;
              margin-bottom: 240px;
              footer: page-footer;
              header: page-header;
          }
        </style>
     @else
        <style>
          @page {
              margin-left: 0;
              margin-right: 0;
              margin-top: 110px;
              margin-bottom: 40px;
              footer: page-footer;
              header: page-header;
          }
        </style>
    @endif

    <style>
        .text-center{
            text-align: center !important;
        }
        
        td{
            line-height: 18px;
        }

        .header-container,
        .footer-container {
            padding: 0;
            margin: 0;
            width: 100%;
            border-collapse: collapse;
        }

        .header-container td,
        .footer-container td {
            width: 50%;
            padding: 0;
            margin: 0;
            height: 11px;
        }

        .header-container td:nth-child(2),
        .footer-container td:nth-child(1) {
            background-color: #{{isset($workspace) ? $workspace->workspace_default_color : '294791'}};
        }

        .header-container td:nth-child(1),
        .footer-container td:nth-child(2) {
            background-color: #969494;
        }

        .page-container {
            padding: 0px 36px;
        }
        .qr-container{
            padding: 0px 36px;
        }

        .content-table {
            font-size: 12px;
        }

        .content-table td {
            padding-bottom: 8px;
        }
        .invoice_header{
            width: 100%;
            border-collapse: collapse;
          }
          .invoice_header th{
            padding: 5px;
            background-color: {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            border: 1px solid {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            outline: none;
            color: white;
            font-size: 10px;
            text-align: center;
          }
          .invoice_header td{
            border: 1px solid black;
            padding: 5px;
            font-size: 10px;
            text-align: center;
          }
          .title{
            font-size: 16px;
            margin: 0 0 10px 0;
          }
          .items_table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
          }
          .items_table th{
            background-color: {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            border: 1px solid {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            outline: none;
            color: white;
            padding: 5px;
            font-size: 10px;
          }
          .items_table td{
            border: 1px solid black;
            padding: 5px;
            font-size: 10px;
            text-align: center;
          }
          .buyer_and_supplier{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
          }
          .client_table{
            direction: ltr;
            width: 100%;
            border-collapse: collapse;
          }
          .client_table th,.supplier_table th{
            padding: 5px;
            background-color: {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            border: 1px solid {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            outline: none;
            color: white;
          }
          .client_table td{
            border: 1px solid black;
            padding: 5px;
            font-size: 11px;
          }
          .supplier_table{
            width: 100%;
            border-collapse: collapse;
            direction: ltr;
          }
          .supplier_table td{
            border: 1px solid black;
            padding: 5px;
            font-size: 11px;
          }
          .ar{
            text-align: right;
            direction: rtl;
          }
          .en{
            text-align: left;
            direction: ltr;
          }
          .center{
            text-align: center;
          }
          .totals_table{
            width: 50%;
            border-collapse: collapse;
            margin-top: 10px;
            direction: ltr;
          }
          .totals_table th{
            background-color: {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            border: 1px solid {{isset($workspace) ? $workspace->workspace_default_color : '#294791'}};
            outline: none;
            color: white;
            padding: 5px;
            font-size: 10px;
          }
          .totals_table td{
            border: 1px solid black;
            padding: 5px;
            font-size: 10px;
            text-align: center;
          }
    </style>
</head>
@php
    $language = $language ?? app()->getLocale();
@endphp
<body dir="{{$language == 'ar' ? 'rtl' : 'ltr'}}">
<!-- page header begin -->
<htmlpageheader name="page-header">
    <table class="header-container">
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
    <div class="qr-container" style="text-align: left;margin-top: 20px;margin-bottom: 0px;z-index:1">
        @if($workspace?->logoBase64)
            <img src="{{$workspace->logoBase64}}" alt="Logo" width="{{isset($workspace) ? $workspace->logo_pdf_width : '180'}}px">
        @endif
    </div>
   
</htmlpageheader>
<!-- page header end -->
<!-- page content begin -->
<div class="page-container">
   @yield('content')
</div>
<!-- page content end -->

<htmlpagefooter name="page-footer">
    <div>
        @yield('footer')
    </div>
    <table style="width: 100%">
        <tr>
            <td width="30%">{{__('Printed by')}} : {{auth()->user()->fullName ?? ''}}</td>
            <td width="40%" class="center">{{__('core::messages.page')}} {PAGENO} / {nbpg}</td>
            <td width="30%"></td>
        </tr>
    </table>
    <table class="footer-container">
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
</htmlpagefooter>
<!-- page footer end -->
</body>

</html>
