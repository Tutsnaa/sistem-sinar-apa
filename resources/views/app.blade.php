<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Sinar Apa</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-blue-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-5xl font-bold mb-4">Selamat Datang di Toko Sinar Apa</h1>
        <p class="text-lg mb-6">Kelola penjualan dan persediaan dengan mudah</p>
        <a href="/login" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Masuk Sekarang</a>
        <div id="app">
            <example-component></example-component>
        </div>


    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>