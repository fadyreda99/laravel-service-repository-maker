# 📦 Laravel Service & Repository Maker

A lightweight Laravel package that generates **Service**, **Repository**, and full **Module** structures with optional CRUD scaffolding — following clean architecture principles.

This package helps you build consistent, modular, and maintainable applications without rewriting the same boilerplate code.

## 🚀 Features
- Generate **Service**, **Repository**, or full **Module**
- Auto-generate **CRUD methods**
- Clean folder structure
- Supports Laravel **8, 9, 10, 11, 12**
- Supports namespaced paths

## 📥 Installation
```bash
composer require fadyreda99/laravel-service-repository-maker
```

## 🛠 Artisan Commands

### Create Repository
```bash
php artisan make:repository UserRepository --model=User
```

### Create Service
```bash
php artisan make:service UserService --model=User
```

### Create Full Module
```bash
php artisan make:module User --model=User
```

## 🧩 Example Usage
```php
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function store(Request $request)
    {
        return $this->service->create($request);
    }
}
```

## 📝 License
MIT License
