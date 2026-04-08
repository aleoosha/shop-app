# 🛒 Shop App Roadmap & TODO

## ✅ Milestone 1: Базовая инфраструктура и Поиск (Done)
- [x] Настройка окружения: Docker (Sail), PostgreSQL, Redis, Elasticsearch.
- [x] Инфраструктура логов: `LogService` с маскированием данных и каналами.
- [x] Value Objects: Реализация `Money` и `ProductSpecs`.
- [x] Репозиторий: Полнотекстовый поиск с весами релевантности и фильтрами.
- [x] Elastic Infra: Команда `app:elastic-setup` и кастомные маппинги.
- [x] Тестирование: Unit и Feature тесты для поиска и денежной логики.

## 🚀 Milestone 2: Корзина и Highload-авторизация (Done)
- [x] Базовая корзина: Модели, миграции и логика `CartService`.
- [x] Слияние корзин: Логика миграции гостевой корзины к пользователю при Login/Register.
- [x] JSON Resources: Перевод всех ответов (Поиск, Категории, Корзина) на API-стандарт.
- [x] **Transactional Outbox:**
    - [x] Миграция таблицы `outbox_events` (с полями для ретраев и ошибок).
    - [x] Контракт и хендлеры: `OutboxHandlerContract` и `RegisteredHandler`.
    - [x] Реестр событий: Настройка `outbox.map` в `AppServiceProvider`.
    - [x] Фоновый воркер: `ProcessOutboxEvent` с атомарными блокировками (`lockForUpdate`).
- [x] **Auth API:**
    - [x] `RegisterAction` и `LoginAction` с использованием `DB::transaction`.
    - [x] Глобальная обработка `UniqueConstraintViolationException` в `bootstrap/app.php`.
- [x] **Idempotency:** Реализовать Middleware для обработки заголовка `X-Idempotency-Key`.

## 🏗 Milestone 3: Заказы и Оформление (Next Step)
- [ ] **Checkout Logic:**
    - [ ] Модели `Order` и `OrderItem`.
    - [ ] `CheckoutAction`: атомарная конвертация корзины в заказ с сохранением цен (`price_at_addition`).
- [ ] **Inventory Management:** Простая логика списания остатков со склада при оформлении.
- [ ] **Admin Panel:** Установка Filament для управления товарами и просмотра заказов.

## 🛠 Оптимизация и надежность (Backlog)
- [ ] **Horizon:** Тонкая настройка очередей (`high`, `default`, `low`, `scout`) в `config/horizon.php`.
- [ ] **Outbox CLI:** Команда `outbox:retry` для восстановления бракованных задач.
- [ ] **Elasticsearch Suggesters:** Добавление автоисправления опечаток в поисковую строку.
- [ ] **API Documentation:** Генерация Swagger/OpenAPI спецификации.
- [ ] **Frontend:** Инициализация Vue 3 + Pinia для интеграции с текущим API.

---
*Последнее обновление: 2026-04-08*
