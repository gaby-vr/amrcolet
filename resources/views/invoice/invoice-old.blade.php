<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ro" lang="ro">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
/* Style definitions for pdfs */

/**********************************************************************/
/* Default style definitions
/**********************************************************************/

/* General
-----------------------------------------------------------------------*/
body {
  background-color: #114C8D;
  color: #000033;
  font-family: "DejaVu Sans", sans-serif;
  margin: 0px;
  padding-top: 0px;
  font-size: 1.5em;
}

h1 {
  font-size: 1.25em;
  color: #114C8D;
  font-style: italic;
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

img.border {
  border: 1px solid #114C8D;
}

pre {
  font-family: "DejaVu Sans", sans-serif;
  color: #FFFFff;
  font-size: 0.7em;
}

ul {
  color: #BEAC8B;
  list-style-type: circle;
  list-style-position: inside;
  margin: 0px;
  padding: 3px;
}

li { 
  color: #000033;
}

li.alpha {
  list-style-type: lower-alpha;
  margin-left: 15px;
}

p {
  font-size: 0.8em;
}

a:link,
a:visited {
  text-decoration: none;
  color: #114C8D;
}

a:hover {
  text-decoration: underline;
  color: #860000;
}

hr {
  border: 0;
}

#page_header { 
  position: relative; /* required to make the z-index work */  
  z-index: 2;
}

#body { 
  background-color: #F9F0E9;
  padding: 12px 0.5% 2em 3px;
  min-height: 20em;
  margin: 0px;
  width: 100%;
}

#body pre {
  color: #000033;
}

#left_column { 
  width: 84%;
  height: auto;
  padding-right: 8px;
  padding-bottom: 30px;
}

#right_column {
/*  position: absolute;
  right: 0.5%;*/
  padding-left: 16px;
  width: 15%;
  min-width: 160px;
}




/* Content
-----------------------------------------------------------------------*/
.page_buttons {
  text-align: center;
  margin: 3px;
  font-size: 0.7em;
  white-space: nowrap;
  font-weight: bold;
  width: 74%;
}

.link_bar {
  white-space: nowrap;
  padding: 3px 0px 0px 0px;
  margin: -1px 8px 2em 0px;
  font-size: 0.7em;
  text-align: center;
}

.link_bar a {
  background-color: #E5D9C3;  
  border: 1px solid #8B7958;
  -moz-border-radius-bottomleft: 4px;
  -moz-border-radius-bottomright: 4px;
  border-top: none;
  padding: 2px 3px 3px 3px;
  margin-right: 2px;  
  white-space: nowrap;  
}

.link_bar a.selected,
.link_bar a:hover { 
  background-color: #BEAC8B;  
  color: #114C8D;
  padding-top: 3px;
  border: 1px solid #8B7958;
  border-top: none;
  text-decoration: none;
}

.page_menu li {
  margin: 5px;
  font-size: 0.8em;
}


/* Tables
-----------------------------------------------------------------------*/
table {
  empty-cells: show;
}

.head td {
  color: #8B7958;
  background-color: #fb923c;
  font-weight: bold;
  font-size: 1.25em;
  padding: 3px;
}

.head input {
  font-weight: normal;
}

.sub_head td {
  border: none;
  white-space: nowrap;
  font-size: 10px;
}

.foot td {
  color: #8B7958;
  background-color: #E5D9C3;
  font-size: 1em;
}

.label {
  color: #8B7958;
  background-color: #F8F5F2;
  padding: 3px;
  font-size: 1em;
}

.label_right {
  color: #8B7958;
  background-color: #F8F5F2;
  padding: 3px;
  font-size: 0.75em;
  text-align: right;
  padding-right: 1em;
}

.sublabel {
  color: #8B7958;
  font-size: 0.6em;
  padding: 0px;
  text-align: center;
}

.field {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 1em;
}

.field_center {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;  
  text-align: center;
}

.field_nw {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
  white-space: nowrap;
}

.field_money {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
  white-space: nowrap;
  text-align: right;
}

.field_total {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
  white-space: nowrap;
  text-align: right;
  font-weight: bold;
  border-top: 1px solid black;
}

/* Table Data
-----------------------------------------------------------------------*/
.h_scrollable { 
  overflow: -moz-scrollbars-horizontal;
}

.v_scrollable { 
  overflow: -moz-scrollbars-vertical;
}

.scrollable {
  overflow: auto;/*-moz-scrollbars-horizontal;*/
}

tr.head>td.center,
tr.list_row>td.center,
.center {
  text-align: center;
}

.left,
tr.head>td.left,
tr.list_row>td.left { 
  text-align: left;
  padding-left: 2em;
}

.total,
.right,
.list tr.head td.right,
tr.list_row td.right,
tr.foot td.right,
tr.foot td.total {
  text-align: right;
  padding-right: 2em;
}

.list tr.foot td {
  font-weight: bold;
}

.no_wrap {
  white-space: nowrap;
}

.bar {
  border-top: 1px solid black;
}

.total {
  font-weight: bold;
}

.summary_spacer_row {
  line-height: 2px;
}

.light { 
  color: #999999;
}

/* Lists
-----------------------------------------------------------------------*/
.list {
  border-collapse: collapse;
  border-spacing: 0px;
  border-top: 1px solid #8B7958;
  border-bottom: 1px solid #8B7958;
  width: 99%;
  margin-top: 3px;
}

.list tr.head td {
  font-size: 0.7em;
  white-space: nowrap;
  padding-right: 0.65em;
  border-bottom: 1px solid #8B7958;
}

.list table.sub_head td {
  border: none;
  white-space: nowrap;
  font-size: 10px;
}

.list tr.foot td {
  border-top: 1px solid #8B7958;
  font-size: 0.7em;
}

tr.list_row>td {
  background-color: #EDF2F7;
  border-bottom: 1px dotted #8B7958;
  font-size: 1.25em;
  padding: 3px;
}

tr.list_row:hover td {
  background-color: #F8EEE4;
}

tr.problem_row>td {
  background-color: #FDCCCC;
  border-bottom: 1px dotted #8B7958;
  font-size: 0.65em;
  padding: 3px;
}

tr.problem_row:hover td {
  background-color: #F8EEE4;
}

.row_form td {
  font-size: 0.7em;
  padding: 3px;
  white-space: nowrap;
/*  text-align: center; */
}

.row_form td.label {
  text-align: left;
  white-space: normal;
}

.inline_header td {
  color: #8B7958;
  font-size: 0.6em;
  white-space: nowrap;
  text-align: center;
}

/* Sub-Tables
-----------------------------------------------------------------------*/
.sub_table {
  border-spacing: 0px;
}

.sub_table tr.head td {
  font-size: 11px;
  padding: 3px;
  background-color: #F9F0E9;
}

.sub_table td {
  padding: 3px;
}

/* Some of the system messages are long and look bad with a highlighted
background... */
#system_notif_table tr.list_row:hover > td {
  background-color: #EDF2F7;
}

.notif_select_column {
  width: 2%;
  padding: 0px;
  text-align: center;
}

.notif_job_column {
  width: 8%; 
  white-space: nowrap; 
  padding-left: 0px; 
  font-weight: bold; 
  text-align: center;
}

.notif_notif_column {
  width: auto;
}

.notif_date_column { 
  width: 15%; 
  text-align: center;
  white-space: nowrap;
  padding-right: 3px;
}

/* Notes
-----------------------------------------------------------------------*/
/* Note Table */
table#topic_list { 
  border-bottom: 1px solid #E5D9C3; 
  border-collapse: separate;
}

/* Note Form */
.note_form {
  background-color: #F9F0E9;
  position: absolute;
  left: 20%;
  display: none;
  border: 2px solid #114C8D;   
}

.note_form table.form {
  margin-top: 2em;
}

.handle {
  background-color: #114C8D;
  color: #FFFFff;
  margin-bottom: 3px; 
  height: 16px;
}

.note_form_close { 
  font-weight: bold;
  font-size: 9px;
  padding: 0px 2px 0px 2px;
  margin-right: 2px;
  position: absolute;
  right: 0%;
  border: 1px solid #114C8D;
}

a.note_form_close:hover { 
  text-decoration: none;
}

.list_row:hover>td table.add_note tr.add_note_foot td,
.list_row:hover>td table.add_note tr.add_note_head td { background-color: #E5D9C3; }
.list_row:hover>td table.add_note tr td { background-color: #F9F0E9; }

.border_none {
  border: 0!important;
}

.background_none {
  background: none!important;
}

.add_note td { 
  border: none;
  padding: 3px;
  background-color: #F9F0E9;
  font-size: 9px; 
}

.add_note_head td {
  background-color: #E5D9C3;
  border-top: 1px solid #8B7958;
  border-bottom: 1px solid #8B7958;
  color: #8B7958;
  padding: 3px;
  text-align: center;
  font-weight: bold;
  font-size: 9px; 
}

.add_note input {   
  color: #114C8D;
  background-color: #FFFFff;
  border: 1px solid #114C8D;
  padding: 1px 2px 1px 2px;
  text-decoration: none;
  font-size: 9px; 
}

.add_note textarea { 
  color: #114C8D;
  background-color: #FFFFff;
  border: 1px solid #114C8D;
  padding: 1px 2px 1px 2px;
  font-family: "DejaVu Sans", sans-serif;
  font-size: 9px; 
}

.add_note select   { 
  color: #114C8D;
  background-color: #FFFFff;
  font-size: 9px; 
}

.add_note_foot td { 
  background-color: #E5D9C3;
  border-bottom: 1px solid #8B7958;
  color: #8B7958;
  padding: 3px;
  text-align: center;
  font-weight: bold;
  font-size: 9px;
}


/* Print preview
-----------------------------------------------------------------------*/
.page { 
  background-color: white;
  padding: 0px;
  border: 1px solid black;
/*  font-size: 0.7em; */
  width: 95%;
  margin-bottom: 15px;
  margin-right: 5px;
  padding: 20px;
}

.page table.header td {
  padding: 0px;
}

.page table.header td h1 { 
  padding: 0px;
  margin: 0px;
}

.page h1 {
  color: black;
  font-style: normal;
  font-size: 1.3em;
}

.page h2 {
  color: black;
}

.page h3 {
  color: black;
  font-size: 1em;
}

.page p { 
  text-align: justify;
  font-size: 0.8em;
}

.page table { 
  font-size: 0.8em;
}

.page em {
  font-weight: bold;
  font-style: normal;
  text-decoration: underline;
  margin-left: 1%;
  margin-right: 1%;
}

.page table.money_table {
  font-size: 1.1em;
  border-collapse: collapse;
  width: 85%;
  margin-left: auto;
  margin-right: auto;
}

.page table.money_table tr.foot td { 
  font-size: 1em;
  border-top: 0.4pt solid black;
  font-weight: bold;
  background-color: white;
  color: black;
}

.page table.money_table tr.foot td.right { 
  padding-right: 1px;
}

.written_field {
  border-bottom: 1px solid black;
}

.page .written_field { 
  border-bottom: 0.4pt solid black;
}

.page .indent * { margin-left: 4em; }

.checkbox { 
  border: 1px solid black;
  padding: 1px 2px;
  font-size: 7px;
  font-weight: bold;
}


table.signature_table { 
  width: 80%;
  font-size: 0.7em;
  margin: 2em auto 2em auto;
}

table.signature_table tr td { 
  padding-top: 1.5em;
  vertical-align: top;
  white-space: nowrap;
}

#special_conditions { 
  font-size: 1.3em;  
  font-style: italic;
  margin-left: 2em;
  font-weight: bold;
}

.sa_head p {
  font-size: 1em;
}


.page hr {
  border-bottom: 1px solid black;
}

.page table.detail,
.page table.fax_head {
  margin-left: auto;
  margin-right: auto;
}

.page .narrow,
.page .fax_head {
  border: none;
}

.page tr.head td {
  color: black;
  background-color: #fb923c;
}

.page td.label {
  color: black;
  background-color: white;
  width: 20%;
}

.page td.label_right {
  color: black;
  background-color: white;
}

.page td.field {
  background-color: white;
  font-weight: bold;
}

.page td.field_money {
  background-color: white;
}

.page td.field_total {
  font-weight: bold;
  background-color: white;
}

.page tr.detail_spacer_row td {
  background-color: white;
  border-top: 1px solid black;
}

.page .header { 
  border-spacing: 0px;
  border-collapse: collapse;
  padding: 0px;
}

.page .header tr td {
  border-top: 1px solid black;
  border-bottom: 1px solid black;
  background-color: #fb923c;
}
/* Style definitions for printable pages */


/* Hide non-printing stuff
-----------------------------------------------------------------------*/
#page_header,
#main_menu,
#right_column,
#footer {
  display: none;
}

/* General
-----------------------------------------------------------------------*/
@page { 
  margin: 0.25in;
}

body { 
  background-color: white;
  color: black;
}

h1 {
  color: black;
}

h2 {
  color: black;
}

pre {
  color: black;
}

ul {
  color: black;
}

a:link,
a:visited {
  color: black;
}

a:hover {
  text-decoration: none;
  color: black;
}

p a {
  display: none;
}

#body { 
  background-color: white;
}

#body pre {
  color: black;
}

/* Tables
-----------------------------------------------------------------------*/
.head td {
  color: black;
  background-color: white;
}

.head input {
}

.foot td {
  color: black;
  background-color: white;
}

.label {
  color: black;
  background-color: white;
}

.sublabel {
  color: black;
}

.field {
  color: black;
  background-color: white;
}

.field_center {
  color: black;
  background-color: white;
}

.field_nw {
  color: black;
  background-color: white;
}

.field_money {
  color: black;
  background-color: white;
}

.field_total {
  color: black;
  background-color: white;
}

/* Lists
-----------------------------------------------------------------------*/
.list {
  border-top: 1px solid black;
  border-bottom: 1px solid black;
}

.list tr.head>td {
  border-bottom: 1px solid black;
}
.list tr.foot td {
  border-top: 1px solid black;
}

tr.list_row>td {
  background-color: white;
  border-bottom: 1px dotted #666;
}

tr.list_row:hover td {
  background-color: white;
}

/* Notifications
-----------------------------------------------------------------------*/
.notification_list {
  border-top: 1px solid black;
  border-bottom: 1px solid black;
}

.notification_list tr.head td {
  border-bottom: 1px solid black;
}

.notification_list tr.foot td {
  border-top: 1px solid black;
}

#system_notif_table tr.list_row:hover > td {
  background-color: white;
}

/* Pages
-----------------------------------------------------------------------*/
.page>*>p, .page>p { 
  font-size: 1.5em;
}

.written_field { 
  font-size: 1em;
  border-bottom: 1px solid black;
}

.page h1 {
  font-size: 1em;
}

.page h2 { 
  font-size: 0.9em;
}

@page {
  margin-bottom: 0.75in;
}
/* General
-----------------------------------------------------------------------*/
body { background-color: white; }

/* Lists
-----------------------------------------------------------------------*/
.list tr.head td { 
  background-color: #fb923c;
}

tr.list_row>td {
  background-color: white;
  border-bottom: 0.7pt solid #666;
}

.list tr.foot td { 
  background-color: #fb923c;
}

/* Pages
-----------------------------------------------------------------------*/
.page { 
  font-size: 1em;
  border: none;
  margin: none;
  width: auto;
  padding: 0px;
}

.foot td { 
  font-size: 1.25em;
}


.page>*>p, .page>p { 
  font-size: 0.8em;
}


table.signature_table { 
  width: 88%;
  font-size: 0.6em;  
}

#special_conditions { 
  font-size: 1.5em;
}

.header h1 {
  font-size: 1.5em;
}

p.small { 
  font-size: 0.8em;
}

.page td {
  padding: 1px;
}

td.label {
  font-size: 1.35em;
}

td.field {
  font-size: 1.35em;
}

td.field_money {
  font-size: 0.7em;
}</style>
<title>Factura {{ env('APP_NAME') }}</title>
</head>
<body class="page" marginwidth="0" marginheight="0">

<table style="width: 100%" class="header">
<tbody>
<tr style="border: none;">
  <td style="width: 50%; vertical-align: middle; border: none;" class="background_none border_none center" rowspan="2">
    <img src="{{ asset('img/logo_200x.png') }}" class="img-fluid" style="width: 200px; background-color: none;">
  </td>
  <td style="width: 25%; text-align: left; padding: 5px; border: none;" class="border_none">
  <h1 style="text-align: left; color: #fff;">Factura</h1>
  </td>
  <td style="width: 25%; text-align: right; padding: 5px; border: none;" class="border_none">
  <h1 style="text-align: right; color: #fff;">{{ $factura->series }} {{ $factura->number }}</h1>
  </td>
</tr>
<tr>
<td class="label background_none border_none" style="padding: 5px; border: none;">Data emiterii:</td>
<td class="field background_none border_none" style="border: none;">{{date("d.m.Y", strtotime($factura->payed_on))}}</td>
</tr>
</tbody></table>

<table class="detail border_none" style="margin: 0px;">
<tbody>

</tbody></table>

<table style="width: 100%; margin: 0px;">
<tbody>
  <tr>
    <td style="width: 50%;">
      <table class="border_none" style="width: 100%; margin: 0px;">
      <tbody>
      <tr>
      <td class="label" colspan="2" style="font-size: 1.75em;">Furnizor:</td>
      </tr><tr>
      <td class="field" colspan="2"><h1 style="font-size: 1.25em;">{{ $factura->provider_name }}</h1></td>
      </tr><tr>
      <td class="label" style="width: 30%;">Reg. Com.:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_nr_reg ?: $factura->provider_nr_reg_com }}</td>
      </tr><tr>
      <td class="label" style="width: 30%;">CUI:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_cui }}</td>
      </tr><tr>
      <td class="label" style="width: 30%;">Adresa:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_address }}</td>
      </tr><tr>
      <td class="label" style="width: 30%;">Tel.:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_phone }}</td>
      </tr><tr>
      <td class="label" style="width: 30%;">Email:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_email }}</td>
      </tr><tr>
      <td class="label" style="width: 30%;">IBAN:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_iban }}</td>
      </tr><tr>
      <td class="label" style="width: 30%;">Capital social:</td>
      <td class="field" style="width: 65%;">{{ $factura->provider_cap_social }}</td>
      </tr>
      </tbody>
      </table>
    </td>
<td style="width: 50%;">
<table class="border_none" style="width: 100%; margin: 0px;">
<tbody>
<tr>
<td class="label" colspan="2" style="font-size: 1.75em;">Client:</td>
</tr>
@if($factura->client_type == '2')
<tr>
<td class="field" colspan="2"><h1 style="font-size: 1.25em;">{{ $factura->client_nume_firma }}</h1></td>
</tr><tr>
<td class="label" style="width: 30%;">Nume Pers.:</td>
<td class="field" style="width: 65%;">{{ $factura->client_last_name }} {{ $factura->client_first_name }}</td>
</tr><tr>
<td class="label" style="width: 30%;">Reg. Com.:</td>
<td class="field" style="width: 65%;">{{ $factura->client_nr_reg ?: $factura->client_nr_reg_com }}</td>
</tr><tr>
<td class="label" style="width: 30%;">{{ $factura->client_company_type == '1' ? 'CUI' : 'NIF' }}:</td>
<td class="field" style="width: 65%;">{{ $factura->client_cui_nif }}</td>
</tr>
@else
<tr rowspan="3">
<td class="field" colspan="2"><h1 style="font-size: 1.25em;">{{ $factura->client_last_name }} {{ $factura->client_first_name }}</h1></td>
</tr>
@endif
@if($factura->client_address)
<tr>
<td class="label" style="width: 30%;">Adresa:</td>
<td class="field" style="width: 65%;">{{ $factura->client_address }}, {{ $factura->client_locality }}, Jud. {{ $factura->client_county }}</td>
</tr>
@endif
<tr>
<td class="label" style="width: 30%;">Tel.:</td>
<td class="field" style="width: 65%;">{{ $factura->client_phone }}</td>
</tr><tr>
<td class="label" style="width: 30%;">Email:</td>
<td class="field" style="width: 65%;">{{ $factura->client_email }}</td>
</tr>
@if($factura->client_type != '2')
<tr>
<td class="label" style="width: 30%;">&nbsp;</td>
<td class="field" style="width: 65%;">&nbsp;</td>
</tr><tr>
<td class="label" style="width: 30%;">&nbsp;</td>
<td class="field" style="width: 65%;">&nbsp;</td>
</tr><tr>
<td class="label" style="width: 30%;">&nbsp;</td>
<td class="field" style="width: 65%;">&nbsp;</td>
</tr><tr>
<td class="label" style="width: 30%;">&nbsp;</td>
<td class="field" style="width: 65%;">&nbsp;</td>
</tr>
@endif
@if(!$factura->client_address)
<tr>
<td class="label" style="width: 30%;">&nbsp;</td>
<td class="field" style="width: 65%;">&nbsp;</td>
</tr>
@endif
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<table class="" style="width: 99%; margin-top: 1em; border-spacing: none; border-collapse: collapse;">
<tbody>
  <tr class="head">

<td class="center border_none" style="width: 9%; color: #fff; padding: 5px;">Nr.</td>
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
  @if($factura->{'product_price_'.$i}) != '')
    <tr class="list_row"><td class="center">{{ $i + 1 }}</td>
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
<strong>Total plata:</strong></td>
<td class="right" style="font-weight: bold; padding: 5px;">{{ $factura->total }} RON</td>
</tr>
</tbody></table>
</div>
</body>
</html>