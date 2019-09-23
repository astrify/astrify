@php
$config = [
    'appName' => config('app.name'),
    'appUrl' => config('app.url'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>{{ config('app.name') }}</title>

  @if (app()->environment('local'))
    <link href="{{ mix('dist/css/app.css') }}" rel="stylesheet">
  @else
    <link href="{{ asset('dist/css/app.css') }}" rel="stylesheet">
  @endif
</head>
<body class="font-sans font-sans text-gray-900 antialiased">
  <div id="app" />
  {{-- Global configuration object --}}
  <script>
    window.config = @json($config);
  </script>

  {{-- Load the application scripts --}}
  @if (app()->environment('local'))
    <script src="{{ mix('dist/js/app.js') }}"></script>
  @else
    <script src="{{ asset('dist/js/app.js') }}" defer></script>
  @endif
</body>
</html>
