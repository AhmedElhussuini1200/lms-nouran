<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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

        .page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            box-sizing: border-box;
            padding: 8mm 12mm;
        }


        @media print {
            .page {
                padding: 8mm 12mm;
            }

            .no-print {
                display: none;
            }
        }

        @font-face {
            font-family: "BahijTheSansArabic";
            src: url("Bahij_TheSansArabic-Bold.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: "BahijTheSansArabic", sans-serif;
            background-color: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            font-size: 16px;
        }

        .pattern {
            background-image: url(../images/card-pattern.svg);
            background-position: center;
            background-repeat: repeat-x !important;
            width: 100%;
            height: 38px;
        }

        @media print {
            .pattern {
                transform: scale(1.01);
                transform-origin: center;
            }
        }

        .report-content .date {
            background-color: #ddd;
            padding: 8px 12px;
            border-radius: 5px;
            left: 10px;
            top: 0;
        }

        .signatures {
            border-bottom: 2px dashed #888;
        }

        .img-holder .description {
            bottom: 5px;
            left: 5px;
            right: 5px;
            padding: 5px 0px;
            background-color: white;
        }

        .img-holder {
            height: 225px;
        }

        .img-holder img {
            height: 100%;
            width: 100%;
            object-fit: cover !important;
        }

        .signature img {
            height: 30px;
            object-fit: contain;
        }

        .fa-share-from-square {
            transform: rotateY(180deg);
        }

        .sector-head {
            height: 70px;
        }

        .final-signature {
            text-align: left;
        }

        .img-signature {
            width: 60px;
            margin-right: auto;
        }
    </style>
</head>

<body dir="rtl">

    <div class="report-content">
        <div class="container mt-3 position-relative">
            <h1 class="fw-bold text-center fs-2">تقرير معاملة</h1>
            <div class="date position-absolute">
                <div class="d-flex align-items-center">
                    <i class="fa-regular fa-calendar-days"></i>
                    <span class="pe-1">10/02/2025</span>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center fw-bold mt-4">
                <p>سعادة / <span>رئيس هيئة الشئون</span></p>
                <p>المحترم</p>
            </div>
            <p class="fw-bold text-center mb-2">
                السلام عليكم ورحمة الله وبركاته،،،
            </p>
            <div class="row g-1 align-items-center">
                <div class="col-9">
                    <div
                        class="content px-4 py-1 rounded-1 bg-success d-flex justify-content-between align-items-center text-white">
                        <p class="mb-0">بناء علي المعاملة:</p>
                        <p class="mb-0">رقم: <span>4600099561</span></p>
                        <div class="d-flex align-items-center">
                            <i class="fa-regular fa-calendar-days"></i>
                            <p class="mb-0 pe-1">بتاريخ: <span>11-05-1446</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div>
                        <div class="content fw-bold py-1 rounded-1 text-center text-white bg-danger">
                            هام
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row bg-success rounded-1 g-0">
                        <div class="col-9">
                            <div class="content px-4 py-3 text-white">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa-regular fa-user"></i>
                                    <p class="fs-5">
                                        اسم العميل : <span class="mx-2">تحكم</span>
                                    </p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-regular fa-share-from-square"></i>
                                    <p class="fs-5">
                                        الجهة المرسلة :
                                        <span class="mx-2">الإدارة العامة للمرور</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 py-1 px-1">
                            <div class="bg-white rounded-1 h-100 d-flex align-items-center gap-3 px-3">
                                <div class="d-flex flex-column gap-2 justify-content-center align-items-center">
                                    <i class="fa-solid fa-location-dot fa-2x"></i>
                                    <p class="fs-5">الموقع</p>
                                </div>
                                <div>
                                    <span>QR Code Image</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 shadow-sm pt-1">
                    <div class="rounded-3 d-flex flex-column gap-2">
                        <div class="rounded-2 px-3 py-2 fw-bold" style="background-color: #eee">
                            <div class="d-flex align-items-center gap-3">
                                <p class="fw-bold text-nowrap">بشأن :</p>
                                <p class="pb-2 border-bottom border-2 mb-2 w-100">
                                    إنقطاع الكهرباء
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <p class="fw-bold text-nowrap">الإفادة :</p>
                                <p>
                                    تم الخروج للموقع حسب االحداثيات المرفقة في الخطاب ولا يوجد
                                    أعامل لرشكة تحكم في الموقع وتم التواصل مع أ / عبدالله
                                    الشويمان بإن الاحداثيات خطأ وأوضح ان الموقع على شارع الخليل
                                    بن احمد، ومحطة الإنارة سليمة وحيث أن المحطة تطفئ آليا فرتة
                                    النهار
                                </p>
                            </div>
                        </div>
                        <div class="rounded-2 px-3 py-2 fw-bold" style="background-color: #eee">
                            <div class="d-flex align-items-center gap-3">
                                <p class="fw-bold text-nowrap">الإجراء المقترح :</p>
                                <p class="pb-2 border-bottom border-2 mb-2 w-100">
                                    يلزم ربط ساهر بالمصدرالرئيسي للتغذيةالكهربائية،حيث أن
                                    المحطةتطفئ آليا فترةالنهار
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <p class="fw-bold text-nowrap">الإجراء المتخذ :</p>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2 pb-3 signatures">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="img-holder position-relative">
                                <span>صورة الاثائر</span>
                                <div class="text-center position-absolute description">
                                    <p>عمود كهرباء</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center mt-4">
                                <p class="fw-bold">مهندس الاستشاري</p>
                                <span>فهدالعنزي</span>
                                <div class="signature">
                                    <span>صورة الاثائر</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="img-holder position-relative">
                                <span>صورة الاثائر</span>
                                <div class="text-center position-absolute description">
                                    <p>عمود كهرباء</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center mt-4">
                                <p class="fw-bold">مدير مشروع الاستشاري</p>
                                <span>فهدالعنزي</span>
                                <div class="signature">
                                    <span>صورة الاثائر</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="img-holder position-relative">
                                <span>صورة الاثائر</span>
                                <div class="text-center position-absolute description">
                                    <p>عمود كهرباء</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center mt-4">
                                <p class="fw-bold">مراقب الأمانة</p>
                                <span>فهدالعنزي</span>
                                <div class="signature">
                                    <span>صورة الاثائر</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sector-head mt-2" style="background-color: #eee">
                    <p class="fw-bold py-1 px-3">
                        توجية رئيس القطاع: <span class="fw-normal ms-2"></span>
                    </p>
                </div>
                <div class="final-signature ps-4">
                    <p class="fw-bold">عبدالعزيز العلياين</p>
                    <div class="img-signature">
                        <span>صورة الاثائر</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/ce78b646d5.js" crossorigin="anonymous"></script>
</body>

</html>
