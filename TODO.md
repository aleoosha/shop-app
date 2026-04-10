# 🛒 Shop App Roadmap & TODO

## ✅ Milestone 1: Базовая инфраструктура и Поиск (Done)
- [x] Настройка окружения: Docker (Sail), PostgreSQL, Redis, Elasticsearch.
- [x] Инфраструктура логов: `LogService` с маскированием и уровнями (info/warning/error).
- [x] Value Objects: Реализация `Money` и `ProductSpecs`.
- [x] Репозиторий: Полнотекстовый поиск с весами релевантности и фильтрами.
- [x] Elastic Infra: Команда `app:elastic-setup` и кастомные маппинги.

## ✅ Milestone 2: Корзина и Highload-авторизация (Done)
- [x] Базовая корзина: Модели, миграции и логика `CartService`.
- [x] Слияние корзин: Логика миграции гостевой корзины при Login/Register.
- [x] **Transactional Outbox:** Таблица событий, хендлеры, воркер с `lockForUpdate`.
- [x] **Auth API:** Register/Login Actions с атомарными блокировками в Redis.
- [x] **Idempotency:** Middleware для `X-Idempotency-Key` с сохранением HTTP-статусов.

## ✅ Milestone 3: Заказы и Оформление (Done)
- [x] **Database Architecture:**
    - [x] Организация миграций по папкам и типизация через `strict_types`.
    - [x] Внедрение `HasUuid` через трейт и `initializeHasUuid`.
    - [x] Добавление комментариев к полям и таблицам во всей БД.
    - [x] Реализация **Soft Deletes** и фоновая команда `app:cleanup-old-carts`.
- [x] **Inventory Management:** Атомарное списание остатков `stock` через `lockForUpdate` в БД.
- [x] **Checkout Logic:** `CheckoutAction` со снапшотами цен и очисткой корзины.
- [x] **Order API:** Контроллеры, DTO и Resources для оформления и просмотра истории.

## 🏗 Milestone 4: Администрирование и UI (Next Step)
- [ ] **Filament Admin Panel:** Установка и настройка ресурсов для заказов и товаров.
- [ ] **Elasticsearch Suggesters:** Добавление автодополнения в поисковую строку.
- [ ] **API Documentation:** Генерация Swagger/OpenAPI спецификации.
- [ ] **Frontend Starter:** Инициализация Vue 3 + Pinia для интеграции.

---
*Последнее обновление: 2026-04-10*
