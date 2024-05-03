<!DOCTYPE html>
<html lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>@yield('title')</title>
	<link rel="shortcut icon" type="image/x-icon" href="{{{ URL::asset('favicon.ico')}}}">
</head>
<body>
	<div id="app"></div>

	@vite('resources/js/app.ts')
</body>
</html>