<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ro" lang="ro">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Factura {{ env('APP_NAME') }}</title>
  <style type="text/css">
    body {
      color: #000;
      font-family: "DejaVu Sans", sans-serif;
      margin: 0px;
      padding-top: 0px;
      font-size: 0.8em;
    }
    @page { 
      margin: 0.15in;
    }
    h1 {
      font-size: 1.25em;
      color: #000033;
/*      font-style: italic;*/
    }
    h2 {
      font-size: 1.05em;
      color: #114C8D;
    }
    h3 { 
      font-size: 1em;
      color: #114C8D;
    }
    img { 
      border: none;
    }
    .center {
      text-align: center;
    }
    .right {
      text-align: right;
    }
    .left {
      text-align: left;
    }
    .fw-bold {
      font-weight: 700;
    }
    .label {
      color: #8B7958;
/*      background-color: #F8F5F2;*/
      padding: 3px;
      font-weight: 700;
      width: 30%;
    }
    .field {
      color: #000;
    /*  background-color: #F9F0E9;*/
      padding: 3px;
      width: 65%;
    }
    .half .label {
      width: 15%;
      text-align:left;
    }
    .half .field {
      width: 32.5%;
      text-align:left;
    }
    .half td.field:nth-of-type(2) {
      border-right: 0.7pt dashed #8B7958;
    }
    .list-row {
      border-bottom: 0.7pt dashed #8B7958;
    }
    .bg-orange {
      background-color: #fb923c;
    }
    .bg-none {
      background-color: transparent;
    }
    .text-black {
      color: #000033;
    }
    .text-white {
      color: #fff;;
    }
    .b-collapse {
      border-collapse: collapse;
    }
    #watermark {
      position: fixed;
      top: 35%;
      width: 100%;
      text-align: center;
      opacity: .1;
      transform: rotate(30deg);
      transform-origin: 55% 60%;
      z-index: -1000;
      font-size: 10rem;
    }
  </style>
</head>
<body class="page" marginwidth="0" marginheight="0">
  @if($factura->status == 2)
    <div id="watermark">
        {{ __('ANULATA') }}
    </div>
  @endif
  @php $data_scadenta = Carbon\Carbon::parse($factura->payed_on)->addDays(7); $scadenta = $data_scadenta && $factura->created_by_admin; @endphp
  <table style="width: 100%" class="b-collapse">
    <tbody>
      <tr style="">
        <td style="width: 50%; vertical-align: middle;" class="center" rowspan="{{ $scadenta ? '3' : '2' }}">
          <img src="{{ asset('img/logo_200x-min.png') }}" class="img-fluid" style="width: 200px;">
        </td>
        <td style="width: 25%; text-align: left; padding: 5px;" class="bg-orange">
          <h1 style="text-align: left; color: #fff;">Factura</h1>
        </td>
        <td style="width: 25%; text-align: right; padding: 5px;" class="bg-orange right">
          <h1 style="color: #fff;">{{ $factura->series }} {{ $factura->number }}</h1>
        </td>
      </tr>
      <tr>
        <td class="label text-black" style="width: 25%; padding: 5px;">Data emiterii:</td>
        <td class="field right" style="width: 25%; border: none;">
          {{ date("d.m.Y", strtotime($factura->payed_on)) }}
        </td>
      </tr>
      @if($scadenta)
        <tr>
          <td class="label text-black list-row" style="width: 25%; padding: 5px;">Data scadenta:</td>
          <td class="field right list-row" style="width: 25%;">
            {{ $data_scadenta->format('d.m.Y') }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>

  <table class="half" style="width: 100%; margin: 0px;">
    <thead>
      <tr>
        <th class="label bg-none" colspan="2" style="font-size: 1.75em;">Furnizor</th>
        <th class="label bg-none" colspan="2" style="font-size: 1.75em;">Client</th>
      </tr>
      <tr>
        <th class="label bg-none" colspan="2"><h1 style="font-size: 1.25em;">{{ $factura->provider_name }}</h1></th>
        <th class="label bg-none" colspan="2">
          <h1 style="font-size: 1.25em;">
            {{ $factura->client_type == '2' ? $factura->client_nume_firma : $factura->client_full_name }}
          </h1>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="label">Reg. Com.:</td>
        <td class="field">{{ $factura->provider_nr_reg ?: $factura->provider_nr_reg_com ?: '' }}</td>

        @if($factura->client_type == '2')
          <td class="label">Reg. Com.:</td>
          <td class="field">{{ $factura->client_nr_reg ?: $factura->client_nr_reg_com }}</td>
        @else
          <td class="label">Adresa:</td>
          <td class="field">{{ $factura->client_address ?? '' }}, {{ $factura->client_locality }}, Jud. {{ $factura->client_county }}</td>
        @endif
      </tr>
      <tr>
        <td class="label">CUI:</td>
        <td class="field">{{ $factura->provider_cui ?: '' }}</td>

        @if($factura->client_type == '2')
          <td class="label">{{ $factura->client_company_type == '1' ? 'CUI' : 'NIF' }}:</td>
          <td class="field">{{ $factura->client_cui_nif?? '' }}</td>
        @else
          <td class="label">Tel.:</td>
          <td class="field">{{ $factura->client_phone ?? '' }}</td>
        @endif
      </tr>
      <tr>
        <td class="label">Adresa:</td>
        <td class="field">{{ $factura->provider_address ?: '' }}</td>

        @if($factura->client_type == '2')
          <td class="label">Adresa:</td>
          <td class="field">{{ $factura->client_address ?? '' }}, {{ $factura->client_locality }}, Jud. {{ $factura->client_county }}</td>
        @else
          <td class="label">Email:</td>
          <td class="field">{{ $factura->client_email ?? '' }}</td>
        @endif
      </tr>
      <tr>
        <td class="label">Tel.:</td>
        <td class="field">{{ $factura->provider_phone ?: '' }}</td>

        @if($factura->client_type == '2')
          <td class="label">Tel.:</td>
          <td class="field">{{ $factura->client_phone ?: '' }}</td>
        @else
          <td class="label"></td>
          <td class="field"></td>
        @endif
      </tr>
      <tr>
        <td class="label">Email:</td>
        <td class="field">{{ $factura->provider_email ?: '' }}</td>

        @if($factura->client_type == '2')
          <td class="label">Email:</td>
          <td class="field">{{ $factura->client_email ?: '' }}</td>
        @else
          <td class="label"></td>
          <td class="field"></td>
        @endif
      </tr>
      <tr>
        <td class="label">IBAN:</td>
        <td class="field">{{ $factura->provider_iban ?: '' }}</td>

        @if($factura->client_type == '2')
          <td class="label">Nume Pers.:</td>
          <td class="field">{{ $factura->client_full_name ?: '' }}</td>
        @else
          <td class="label"></td>
          <td class="field"></td>
        @endif
      </tr>
      <tr>
        <td class="label">Capital social:</td>
        <td class="field">{{ $factura->provider_cap_social ?: '' }}</td>

        <td class="label"></td>
        <td class="field"></td>
      </tr>
    </tbody>
  </table>

  <table class="" style="width: 100%; margin-top: 1em; border-spacing: none; border-collapse: collapse;">
    <tbody>
      <tr class="head bg-orange fw-bold">
        <td class="center" style="width: 9%; color: #fff; padding: 5px;">Nr.</td>
        <td style="width: 30%; color: #fff; padding: 5px;">Denumire</td>
        <td class="center" style="width: 10%; color: #fff; padding: 5px;">U.M.</td>
        <td class="center" style="width: 10%; color: #fff; padding: 5px;">Cant.</td>
        <td class="right" style="width: 25%; color: #fff; padding: 5px;">Pret unitar<br>(RON)</td>
        <td class="right" style="width: 10%; color: #fff; padding: 5px;">TVA</td>
        <td class="right" style="width: 25%; color: #fff; padding: 5px;">Valoare TVA<br>(RON)</td>
        <td class="right" style="width: 25%; color: #fff; padding: 5px;">Valoare Totala<br>(RON)</td>
      </tr>
      @php $tva = $factura->provider_tva != '' ? $factura->provider_tva : 19; @endphp
      @for($i = 0 ; $i < $factura->product_nr_products ; $i++)
        @if($factura->{'product_price_'.$i} != '')
          <tr class="list-row">
            <td class="center">{{ $i + 1 }}</td>
            <td style="padding: 5px;">
              {{ $factura->{'product_name_'.$i} }}<br>
              <small style="display: inline-block; padding-left: 0.5rem; font-size: 0.7rem">
                {!! nl2br($factura->{'product_description_'.$i}) !!}
              </small>
            </td>
            <td class="center" style="padding: 5px;">Buc.</td>
            <td class="center" style="padding: 5px;">{{ $factura->{'product_qty_'.$i} }}</td>
            <td class="right" style="padding: 5px;">{{ round(floatval($factura->{'product_price_'.$i})/(1 + $tva/100), 2) }}</td>
            <td class="right" style="padding: 5px;">{{ $tva }}%</td>
            <td class="right" style="padding: 5px;">{{ floatval($factura->{'product_price_'.$i}) - round(floatval($factura->{'product_price_'.$i})/(1 + $tva/100), 2) }}</td>
            <td class="right" style="padding: 5px;">{{ floatval($factura->{'product_price_'.$i}) * floatval($factura->{'product_qty_'.$i}) }}</td>
          </tr>
        @endif
      @endfor
      <tr class="foot">
        <td colspan="7" class="right">
          <strong>Total plata:</strong>
        </td>
        <td class="right" style="font-weight: bold; padding: 5px;">{{ $factura->total }} RON</td>
      </tr>
    </tbody>
  </table>
</body>
</html>