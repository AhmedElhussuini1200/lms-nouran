<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Document</title>
    <style>
        :root {
            --text-primary: #0b6b53;
            --green-strong: #006946;
            --ink: #111;
            --muted: #6b7280;
            --line: #e5e7eb;
            --warn: #eab308;
            --danger: #ef4444;
            --ok: #10b981;
            --bg: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        p {
            margin-bottom: 0 !important;
        }

        .x-small {
            font-size: 11px;
        }

        .container-2 {
            padding-left: 50px;
            padding-right: 50px;
        }

        @font-face {
            font-family: "BahijTheSansArabic";
            src: url({{ public_path('fonts/Cairo-Light.ttf') }}) format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: "cairo", sans-serif;
            background-color: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            font-size: 16px;
        }

        header {
            height: 160px;
        }

        .logo {
            width: 250px;
        }

        .pattern {
            background-image: url({{ public_path('pdf/card-pattern.svg') }});
            background-position: center;
            background-repeat: repeat-x !important;
            width: 100%;
            height: 37px;

        }

        .subject {
            border-bottom: 1px solid #fff;
        }

        .signature {
            position: relative;
        }

        .signature::after {
            content: "";
            position: absolute;
            left: 0;
            top: 80%;
            height: 50px;
            width: 1px;
            transform: translateY(-50%);
            background-color: var(--ok);
        }

        .date {
            text-align: left;
            display: block;
            font-size: 12px;
        }

        table thead tr th {
            background-color: #ccc !important;
            padding: 0.2rem 0.5rem !important;
        }

        .table-1>thead>tr>th,
        .table-1>tbody>tr>th,
        .table-1>tbody>tr>td {
            padding: 0.2rem 0.5rem;
        }

        .table-2>thead>tr>th,
        .table-2>tbody>tr>th,
        .table-2>tbody>tr>td {
            padding: 0.5rem 0.5rem;
        }

        .aside {
            height: calc(100% - 44px);
            margin-top: 27px;
            padding-left: 15px;
            padding-right: 15px;
        }

        .aside>div:not(:last-of-type) {
            border-bottom: 1px dashed #000;
        }

        /* Header Styles */
        .header-bg {
            background-color: #198754;
            padding-top: 1rem;
            width: 100%;
        }

        .header-top {
            width: 100%;
        }

        .logo-container {
            float: right;
            width: 250px;
            vertical-align: middle;
        }

        .logo-container img {
            width: 100%;
            display: block;
        }

        .pattern-container {
            float: right;
            width: 70%;
            vertical-align: middle;
            padding-right: 3rem;

        }

        .header-info-box {
            background-color: rgba(0, 0, 0, 0.25);
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .header-info-table {
            width: 100%;
            color: white;
        }

        .header-info-table td {
            vertical-align: middle;
        }

        .header-left {
            width: 16.66%;
            float: right;

        }

        .header-center {
            width: 45%;
            float: right;

        }

        .header-right {
            width: 35%;
            float: right;

        }

        .text-info {
            color: #0dcaf0;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .subject-box {
            padding-right: 1rem;
        }

        .contract-info {
            margin-top: 0.5rem;
        }

        .contract-info span {
            display: inline-block;
            margin-left: 2.5rem;
        }

        .stats-container {
            width: 100%;
        }

        .stats-box {
            width: auto;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-align: center;
            margin: 0;
        }

        .stats-box p {
           margin-bottom: 8px !important
        }

        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .stats-grid td {
            vertical-align: top;
            padding: 0;
        }

        .stats-grid .col-box {
            width: 20%;
        }

        .stats-grid .col-mini {
            width: 60%;
        }

        .stats-mini-table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
            border: 1px solid #eeeeee;
        }

        .stats-mini-table tr {
            border: 1px solid #eeeeee;
        }

        .stats-mini-table tr:last-child {
            border-bottom: none;
        }

        /* Content Layout */
        .content-wrapper {
            width: 100%;
            margin-top: 1rem;
        }

        .main-content {
            float: right;
            width: 64%;
            vertical-align: top;
            padding-left: 0.75rem;
        }

        .sidebar-content {
            float: right;
            width: 33%;
            vertical-align: top;
            padding-right: 0.75rem;
            margin-top: .5rem;
        }

        /* Table Styles */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .table-custom thead {
            background-color: #ccc;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #dee2e6;
            text-align: center;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .fs-6 {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        /* Sidebar */
        .sidebar-inner {
            background-color: #eee;
            padding: 1.5rem 0;
        }

        .sidebar-section {
            padding: 1rem 0;
        }

        .sidebar-title {
            text-align: center;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .indicator-container {
            text-align: center;
        }

        .indicator-item {
            float: right;
            width: 45%;
            vertical-align: top;
            margin: 0 2.5%;

        }

        .indicator-item p {
            margin-bottom: 0.5rem;
        }

        .indicator-badge {
            display: block;
            background-color: white;
            color: #198754;
            padding: 0 1rem;
            text-align: center;
            width: 40%;
            margin: 0.5rem auto 0;
        }

        .notes-text {
            text-align: center;
            padding: 0 1rem;
        }
    </style>
</head>

<body dir="rtl">
    <header class="header-bg">
        <div class="container-2">
            <div class="header-top">
                <div class="logo-container">
                    <img src="{{ public_path('pdf/mission.svg')}}" alt="logo-amana" />
                </div>
                <div class="pattern-container">
                    <div class="pattern"></div>
                </div>
            </div>
            <div class="header-info-box">
                <table class="header-info-table x-small">
                    <tr>
                        <td class="header-left">
                            <span class="date">7/4/2025</span>
                            <h2 class="text-info">التقرير اليومي</h2>
                        </td>
                        <td class="header-center">
                            <div class="subject-box">
                                <div class="subject" style="padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                                    <p>
                                        المشــروع:
                                        <span style="padding-right: 0.5rem;">صيانة انارة شوارع جنوب الرياض نطاق بلدية
                                            الشفا</span>
                                    </p>
                                </div>
                                <div class="contract-info">
                                    <span>رقم العقد : 10</span>
                                    <span>المقاول: مهارة الفن للمقاولات</span>
                                </div>
                            </div>
                        </td>
                        <td class="header-right">
                            <table class="stats-grid x-small">
                                <tr>
                                    <td class="col-box">
                                        <div class="stats-box">
                                            <p class="x-small">قيمة العقد</p>
                                            <span class="x-small">000000000</span>
                                        </div>
                                    </td>
                                    <td class="col-box">
                                        <div class="stats-box">
                                            <p class="x-small">تسليم الموقع</p>
                                            <span class="x-small">05/02/2025</span>
                                        </div>
                                    </td>
                                    <td class="col-mini">
                                        <table class="stats-mini-table x-small">
                                            <tr>
                                                <td>فانوس</td>
                                                <td>0000000</td>
                                            </tr>
                                            <tr>
                                                <td>عمود</td>
                                                <td>0000000</td>
                                            </tr>
                                            <tr>
                                                <td>محطة</td>
                                                <td>00</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>

                    </tr>
                </table>
            </div>
        </div>
    </header>

    <section>
        <div class="container-2">
            <div class="content-wrapper">
                <div class="main-content">
                    <h3 class="fs-6">الأعمال المنفذة خلال اليوم</h3>
                    <table class="table-1 table-striped x-small table-custom">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">البيان</th>
                                <th scope="col">الحي</th>
                                <th scope="col">الشارع</th>
                                <th scope="col">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>العمل على نظافة قواعد ودهان فلانشة محطة 15 ف</td>
                                <td>بدر</td>
                                <td>السخاء</td>
                                <td>صيانة</td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>العمل على نظافة قواعد ودهان فلانشة محطة 15 ف</td>
                                <td>بدر</td>
                                <td>السخاء</td>
                                <td>صيانة</td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td>العمل على نظافة قواعد ودهان فلانشة محطة 15 ف</td>
                                <td>بدر</td>
                                <td>السخاء</td>
                                <td>صيانة</td>
                            </tr>
                            <tr>
                                <th scope="row">4</th>
                                <td>العمل على نظافة قواعد ودهان فلانشة محطة 15 ف</td>
                                <td>بدر</td>
                                <td>السخاء</td>
                                <td>صيانة</td>
                            </tr>
                            <tr>
                                <th scope="row">5</th>
                                <td>العمل على نظافة قواعد ودهان فلانشة محطة 15 ف</td>
                                <td>بدر</td>
                                <td>السخاء</td>
                                <td>صيانة</td>
                            </tr>
                            <tr>
                                <th scope="row">6</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">7</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">8</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">9</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">10</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="fs-6">كميات الأعمال المنفذة</h3>
                    <table class="table-2 table-striped x-small table-custom">
                        <thead>
                            <tr>
                                <th scope="col">البند</th>
                                <th scope="col">الوصف</th>
                                <th scope="col">الوحدة</th>
                                <th scope="col">الكمية</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">00</th>
                                <td>صيانة فوانيس اإلنارة الترشيدية LED التابعة لترشيد حسب المواصفات</td>
                                <td>عدد</td>
                                <td>000</td>
                            </tr>
                            <tr>
                                <th scope="row">00</th>
                                <td>صيانة فوانيس اإلنارة الترشيدية LED التابعة لترشيد حسب المواصفات</td>
                                <td>عدد</td>
                                <td>000</td>
                            </tr>
                            <tr>
                                <th scope="row">00</th>
                                <td>صيانة فوانيس اإلنارة الترشيدية LED التابعة لترشيد حسب المواصفات</td>
                                <td>عدد</td>
                                <td>000</td>
                            </tr>
                            <tr>
                                <th scope="row">00</th>
                                <td>صيانة فوانيس اإلنارة الترشيدية LED التابعة لترشيد حسب المواصفات</td>
                                <td>عدد</td>
                                <td>000</td>
                            </tr>
                            <tr>
                                <th scope="row">00</th>
                                <td>صيانة فوانيس اإلنارة الترشيدية LED التابعة لترشيد حسب المواصفات</td>
                                <td>عدد</td>
                                <td>000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="sidebar-content">
                    <div class="aside sidebar-inner">
                        <div class="sidebar-section">
                            <h3 class="sidebar-title">مؤشرات السلامة</h3>
                            <div class="indicator-container">
                                <div class="indicator-item">
                                    <p>تأمين المواقع</p>
                                    <div class="indicator-badge">جيد</div>
                                </div>
                                <div class="indicator-item">
                                    <p>تأمين العاملين</p>
                                    <div class="indicator-badge">جيد</div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-section">
                            <h3 class="sidebar-title">مؤشرات الجودة</h3>
                            <div class="indicator-container">
                                <div class="indicator-item">
                                    <p>المواد المستخدمة</p>
                                    <div class="indicator-badge">جيد</div>
                                </div>
                                <div class="indicator-item">
                                    <p>جودة الأعمال</p>
                                    <div class="indicator-badge">جيد</div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-section">
                            <h3 class="sidebar-title">ملاحظات السالمة والجودة</h3>
                            <p class="notes-text">Lorem ipsum dolor sit amet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
