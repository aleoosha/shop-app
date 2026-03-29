<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск товаров — Наш Магазин 🚀</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        em { background-color: #fef08a; font-weight: bold; font-style: normal; padding: 0 2px; border-radius: 2px; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Наш Магазин 🚀</h1>

        <!-- Форма поиска -->
        <form action="/search" method="GET" 
              class="mb-8 p-4 bg-white rounded shadow-md flex flex-wrap gap-4 items-end"
              x-data="{ suggestions: [], fetchSuggestions(q) { /* логика из прошлого шага */ } }">
            
            <!-- Поиск с автокомплитом -->
            <div class="flex-1 min-w-[250px] relative">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Поиск</label>
                <input 
                    type="text" name="q" value="{{ request('q') }}" placeholder="Название товара..." 
                    class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                    @input.debounce.300ms="/* fetch логика */"
                >
            </div>

            <!-- Селект категорий -->
            <div class="w-64">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Категория</label>
                <select name="category_id" class="w-full p-2 border rounded bg-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{-- Рисуем отступы в зависимости от уровня вложенности --}}
                            {{ str_repeat('  ', $category->depth) }} 
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Цена ОТ -->
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Мин. цена</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Цена ДО -->
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Макс. цена</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded hover:bg-blue-700 transition font-bold shadow-sm h-[42px]">
                Найти
            </button>
        </form>

        <!-- Результаты -->
        <div class="grid gap-6">
            @forelse($products as $product)
                <div class="bg-white p-6 rounded shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $product->title }}</h2>
                        <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded uppercase tracking-wider">
                            {{-- Категория из ViewModel --}}
                            {{ $product->category_name ?? 'Без категории' }}
                        </span>
                    </div>
                    
                    <div class="text-gray-600 my-3 leading-relaxed">
                        {!! $product->description !!}
                    </div>

                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-50">
                        <span class="text-2xl font-bold text-green-600">{{ $product->price }}</span>
                        <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline text-sm font-medium">
                            Подробнее →
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center p-12 bg-white rounded shadow text-gray-500">Ничего не нашли 😕</div>
            @endforelse
        </div>

        <div class="mt-8">{{ $products->links() }}</div>
    </div>
</body>
</html>
