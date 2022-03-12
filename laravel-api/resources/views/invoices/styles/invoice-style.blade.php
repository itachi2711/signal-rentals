<style>
    @font-face {
        font-family: Roboto;
    }

    .clear-fix:after {
        content: "";
        display: table;
        clear: both;
    }

    a {
        color: #0087C3;
        text-decoration: none;
    }

    body {
        position: relative;
        /*width: 21cm;*/
       /* height: 29.7cm;*/
        margin: 0 auto;
      /*  color: #555555;*/
        color: #000000;
        background: #FFFFFF;
       /* font-family: Arial, sans-serif;*/
        font-size: 12px;
        /*font-family: SourceSansPro;*/
    }

    header {
       /* padding: 10px 0;*/
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
    }

    .header2 {

    }

    #logo {
        float: left;
        margin-top: 8px;
    }

    #logo img {
        height: 70px;

        max-width: 150px;
        max-height: 150px;
    }

    #company {
        float: right;
        text-align: right;
    }


    #details {
        margin-bottom: 50px;
    }

    #client {
        padding-left: 6px;
        /*border-left: 6px solid #0087C3;*/
        border-left: 6px solid blue;
        float: left;
    }

    #client .to {
        color: #777777;
    }

    h2.name {
        font-size: 1.4em;
        font-weight: normal;
        margin: 0;
    }

    #invoice {
        float: right;
        text-align: right;
    }

    #invoice h1 {
        color: #0087C3;
        font-size: 2.4em;
        line-height: 1em;
        font-weight: normal;
        margin: 0  0 10px 0;
    }

    #invoice .date {
        font-size: 1.1em;
        color: #777777;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
        border: none;
    }

    table th,
    table td {
       /* padding: 20px;*/
       /* background: #EEEEEE;*/
        background: white;
        text-align: center;
  /*      border-bottom: 1px solid #FFFFFF;*/
    }

 /*   .table thead th {
        vertical-align: text-bottom;
        border-bottom: 2px solid #dee2e6;
    }*/

  /*  .table td, .table th {
       !* padding: .50rem;*!
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }*/

    table th {
        white-space: nowrap;
        font-weight: normal;
        border-top: none;
    }

    table td {
        text-align: right;
    }

    table .no {
        color: #FFFFFF;
        background: #57B223;
    }

    table .desc {
        text-align: left;
    }

    table .unit {
        background: #DDDDDD;
    }

    table .qty {
    }

    table .total {
        background: #57B223;
        color: #FFFFFF;
    }

    table td.unit,
    table td.qty,
    table td.total {
        font-size: 1.2em;
    }

    table tbody tr:last-child td {
        border: none;
    }

    table tfoot td {
       /* padding: 10px 20px;*/
        background: #FFFFFF;
        border-bottom: none;
       /* font-size: 1.2em;*/
        white-space: nowrap;
      /*  border-top: 1px solid #AAAAAA;*/
    }

    table tfoot tr:first-child td {
        border-top: none;
    }

    table tfoot tr:last-child td {
        color: #57B223;
        font-size: 1.4em;
        /*border-top: 1px solid #57B223;*/
    }

    table tfoot tr td:first-child {
        border: none;
    }

    .blockquote{margin-bottom:0;font-size:.75rem;}

    #items {
        margin-top: 5px;
    }

    #items .first-cell, #items table th:first-child, #items table td:first-child {
        /*width: 40px !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        text-align: right;*/
    }

    #items table {
        border-collapse: separate;
        width: 100%;
    }
    #items table th {
        font-weight: bold;
       /* padding: 5px 8px;*/
        text-align: right;
        /*background: #B32C39;*/
       /* background: blue;*/
        background: #464a4c;
        color: white;
        text-transform: uppercase;
        border: none;
    }
    #items table th:nth-child(3) {
        text-align: left;
    }
  /*  #items table th:last-child {
        text-align: right;
    }*/
    #items table td {
       /* padding: 9px 8px;*/
        text-align: right;
        padding-bottom: 0;
        /*border-bottom: 1px solid #ddd;*/
    }
    #items table td:nth-child(3) {
        text-align: left;
    }

    #thanks{
        font-size: 2em;
       /* margin-bottom: 50px;*/
    }

    #notices{
        padding-left: 6px;
        /*border-left: 6px solid #0087C3;*/
        border-left: 2px solid #464a4c;
    }

    #notices .notice {
        font-size: 1.2em;
    }

    footer {
        color: #777777;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 0;
        border-top: 1px solid #AAAAAA;
        padding: 8px 0;
        text-align: center;
        clear: both;
    }


    #sums {
       /* margin: 0px 0px 0 0;*/
    }
    #sums table {
        float: right;
    }
    #sums table tr th, #sums table tr td {
        min-width: 100px;
        padding: 9px 8px;
        text-align: right;
    }
    #sums table tr th {
        font-weight: bold;
        text-align: left;
        /*padding-right: 35px;*/
    }
    #sums table tr td.last {
        min-width: 0 !important;
        max-width: 0 !important;
        width: 0 !important;
        padding: 0 !important;
        border: none !important;
    }
    #sums table tr.amount-total th {
        text-transform: uppercase;
    }
    #sums table tr.amount-total th, #sums table tr.amount-total td {
        font-size: 15px;
        font-weight: bold;
    }
    #sums table tr:last-child th {
        text-transform: uppercase;
    }
    #sums table tr:last-child th, #sums table tr:last-child td {
        font-size: 15px;
        font-weight: bold;
        color: white;
    }
</style>
