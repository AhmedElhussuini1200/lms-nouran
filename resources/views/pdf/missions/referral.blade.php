<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>التقارير اليومية</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            direction: rtl;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
        }

        /* Header */
        .header {
            background-color: #003523;

            height: 20%;
            width: 100%;
        }



        /* Body Section */
        .report-body {
            text-align: center;
            background-color: #e7e6e6;
            padding-top: 30px;
            height: 50%;
        }

        .title-and-date {
            text-align: center;
            color: #006946;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .title-and-date h1 {
            display: inline-block;
            font-size: 18pt;
            margin: 0 20px 0 0;
        }

        .title-and-date p {
            display: inline-block;
            font-size: 14pt;
            background-color: #f2f2f2;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 10px;
            margin: 20px auto;
            overflow: hidden;
            width: 85%;
            display: table;
            table-layout: fixed;
        }

        .icon-box {
            background-color: #006b54;
            color: white;
            width: 80px;
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 15px 10px;
        }

        .icon-box img {
            width: 35px;
            height: 35px;
            margin-bottom: 5px;
        }

        .icon-box span {
            font-size: 9pt;
            line-height: 1.2;
        }

        .details {
            display: table-cell;
            vertical-align: middle;
            padding: 15px 20px;
        }

        .project {
            font-size: 13pt;
            margin-bottom: 8px;
        }

        hr {
            border: none;
            height: 2px;
            background-color: #0f5132;
            margin: 8px 0;
        }

        .project-details {
            font-size: 11pt;
            margin-top: 8px;
        }

        .project-details span:first-child {
            margin-left: 30px;
        }

        .report-maker {
            width: 85%;
            margin: 15px auto;
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
        }

        .sector {
            background-color: #e5a92e;
            color: #cd9119;
            font-weight: bold;
            font-size: 16pt;
            text-align: center;
            padding: 15px 0;
            margin-top: 20px;

        }

        .sector h1 {
            font-size: 18pt;
            margin: 0;
        }

        /* Footer */
        .footer {
            background-color: #003523;
            padding: 30px 0;
            text-align: center;
            height: 20%;
        }

        .bar-footer {
            text-align: center;
        }

        .upper-bar-footer,
        .lower-bar-footer {
            white-space: nowrap;
            line-height: 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <span style="width: 20%;">
            <img src="{{ public_path('pdf/mo5tabar-W.svg') }}" alt="logo" style="width: 50px; height: 60px;" />
        </span>

        <span style="width: 60%;">
            @for($i = 0; $i < 7; $i++)
                <span>
                    <img src="{{ public_path('pdf/flip-triangle.svg') }}" alt="logo" style="width: 15px; height: 15px;
                                                                                                         " />
                    <img src="{{ public_path('pdf/triangle.svg') }}" alt="logo" style="width: 15px; height: 15px;

                                                                                                        " />

                </span>
            @endfor
            <br />
            @for($i = 0; $i < 7; $i++) <span>
                    <img src="{{ public_path('pdf/flip-triangle.svg') }}" alt="logo" style="width: 15px; height: 15px;

                                                                                                        " />
                    <img src="{{ public_path('pdf/triangle.svg') }}" alt="logo" style="width: 15px; height: 15px;

                                                                                                    " />

                </span>
            @endfor
        </span>
    </div>


    <div class="report-body">
        <div class="title-and-date">
            <h1>تقرير تأخير المرتبات </h1>
            <p>{{ $date ?? '0000-00-00' }}</p>
        </div>

        <div class="card">
            <div class="icon-box">
                <img src=" {{ public_path('pdf/calendar.svg') }}" alt="calendar" style=" width: 25px; height: 25px;" />
                <span>التاريخ<br>{{ $date ?? '00.00.0000' }}</span>
            </div>
            <div class="details">
                <div class="project">
                    المشروع: <strong>{{ $project ?? ' دفع الأقساط' }}</strong>
                </div>
                <hr />
                <div class="project-details">
                    <span>المقاول: <strong>{{ $contractor ?? 'محمد النجار' }}</strong></span>
                    <span>رقم العقد: <strong>{{ $contract_number ?? '0000000000' }}</strong></span>
                </div>
            </div>
        </div>

        <div class=" report-maker"> معد التقرير: <strong>{{ $report_maker ?? 'حماده الكبير' }}</strong>
        </div>

        <div class="sector">
            <h1>{{ $sector ?? 'قطاع الغرب' }}</h1>
        </div>
    </div>


    <div class="footer">
        <div class="bar-footer">
            {{-- <div class="upper-bar-footer">
                @for($i=0;$i<25;$i++) <div class="flip-triangle">
            </div>
            <div class="triangle"></div>
            @endfor
        </div> --}}
        {{-- <div class="lower-bar-footer">
            @for($i=0;$i<25;$i++) <div class="flip-triangle">
        </div>
        <div class="triangle"></div>
        @endfor
    </div> --}}
    </div>
    </div>
</body>
//marigan

</html>