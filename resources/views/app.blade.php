<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/ts/app.tsx','resources/css/app.css','resources/sass/app.scss')
    @inertiaHead
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  </head>
  <body>
    @inertia
  </body>
</html>
