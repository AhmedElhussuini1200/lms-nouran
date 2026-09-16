<!DOCTYPE html>
<html @if(app()->getLocale() == 'ar') lang="ar" dir="rtl" @else lang="en" dir="ltr" @endif>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>@yield('code') — {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'LMS' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: "Cairo", sans-serif !important; box-sizing: border-box; }
body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f8fa; }
.card { text-align: center; background: #fff; padding: 48px 64px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,.08); max-width: 520px; }
.code { font-size: 72px; font-weight: 800; color: #1b84ff; line-height: 1; }
.msg { font-size: 20px; font-weight: 700; margin: 12px 0 8px; color: #181c32; }
.sub { color: #7e8299; margin-bottom: 24px; }
.btn { display: inline-block; background: #1b84ff; color: #fff !important; padding: 10px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; }
</style>
</head>
<body>
<div class="card">
<div class="code">@yield('code')</div>
<div class="msg">@yield('message')</div>
<div class="sub">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? '' }}</div>
<a class="btn" href="{{ url('/dashboard') }}">{{ __('رجوع للوحة التحكم') }}</a>
</div>
</body>
</html>
