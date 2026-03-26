<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск товаров — Наш Магазин 🚀</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Подключаем Alpine.js для "живого" поиска -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        em {
            background-color: #fef08a;
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

        <!-- Форма поиска с контейнером Alpine.js -->
        <form action="/search" method="GET" 
              class="mb-8 p-4 bg-white rounded shadow flex flex-wrap gap-4 items-start"
              x-data="{ 
                suggestions: [], 
                loading: false,
                fetchSuggestions(query) {
                    if (query.length < 2) { this.suggestions = []; return; }
                    this.loading = true;
                    fetch('/api/products/autocomplete?q=' + query, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(json => {
                            this.suggestions = json.data;
                            this.loading = false;
                        });
                }
              }">
            
            <div class="relative flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Что ищем?" 
                    class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                    autocomplete="off"
                    @input.debounce.300ms="fetchSuggestions($event.target.value)"
                    @click.away="suggestions = []"
                >
                
                <!-- Выпадающий список подсказок (Autocomplete) -->
                <div x-show="suggestions.length > 0" x-cloak 
                     class="absolute z-50 w-full bg-white border rounded shadow-xl mt-1 overflow-hidden">
                    <template x-for="item in suggestions" :key="item.id">
                        <a :href="item.url" 
                           class="block p-3 hover:bg-blue-50 border-b last:border-0 text-gray-700 transition">
                            <span x-text="item.title"></span>
                        </a>
                    </template>
                </div>
            </div>
            
            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Мин. цена" class="w-32 p-2 border rounded outline-none focus:ring-2 focus:ring-blue-500">
            
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Макс. цена" class="w-32 p-2 border rounded outline-none focus:ring-2 focus:ring-blue-500">
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition shadow-sm">
                Найти
            </button>
        </form>

        <!-- Список товаров -->
        <div class="grid gap-6">
            @forelse($products as $product)
                <div class="bg-white p-6 rounded shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $product->title }}
                    </h2>
                    
                    <div class="text-gray-600 my-3 leading-relaxed">
                        {!! $product->description !!}
                    </div>

                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-50">
                        <span class="text-2xl font-bold text-green-600">
                            {{ $product->price }}
                        </span>
                        <span class="text-xs text-gray-400">ID: {{ $product->id }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center p-12 bg-white rounded shadow text-gray-500">
                    Товары не найдены 😕
                </div>
            @endforelse
        </div>

        <!-- Пагинация -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</body>
</html>
