<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск товаров</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Стилизуем тег em, который присылает Elastic */
        em {
            background-color: #fef08a; /* Желтый (Tailwind yellow-200) */
            font-weight: bold;
            font-style: normal;
            padding: 0 2px;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Наш Магазин 🚀</h1>

        <form action="/search" method="GET" class="mb-8 p-4 bg-white rounded shadow flex flex-wrap gap-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Что ищем?" class="flex-1 p-2 border rounded">
            
            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Мин. цена" class="w-32 p-2 border rounded">
            
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Макс. цена" class="w-32 p-2 border rounded">
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Найти</button>
        </form>


        <div class="grid gap-4">
            @foreach($products as $product)
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h2 class="text-xl font-semibold">{{ $product->title }}</h2>
                    
                    <p class="text-gray-600 my-2">
                        {!! $product->description !!} {{-- Теперь это уже либо подсветка, либо текст --}}
                    </p>

                    <span class="text-green-600 font-bold">{{ $product->price }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</body>
</html>
