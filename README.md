# 📦 Laravel Service & Repository Maker

A powerful Laravel package that automatically generates **Services**, **Repositories**, and full **Modules** with optional CRUD scaffolding.  
This package helps you apply clean architecture, avoid repeating boilerplate code, and speed up development significantly.

---

# 🚀 Features

✔ Generate **Service**, **Repository**, or both together as a **Module**  
✔ Auto‑generate CRUD logic when passing a model  
✔ Support for namespaced paths (`Admin/User`, `Api/V1/Product`, …)  
✔ Works with Laravel **8 / 9 / 10 / 11 / 12**  
✔ Zero configuration — just install and use  
✔ Generates clean, organized architecture

Folder structure produced:

```
app/
 ├── Services/
 │     └── UserService.php
 └── Repositories/
       └── UserRepository.php
```

---

# 📥 Installation

Install via Composer:

```bash
composer require fadyreda99/laravel-service-repository-maker
```

Laravel auto‑discovers the provider — no manual setup needed.

---

# 🛠 Artisan Commands

The package provides **3 main commands**:

---

## 1️⃣ Create a Repository

```
php artisan make:repository UserRepository --model=User
```

Output:

```
app/Repositories/UserRepository.php
```

### ⭐ Generated Repository (when model is provided)

```php
public function allWithCondition(array $condition = [], array $with = [], bool $paginated = false, int $limit = 10, array $orderBy = [])
{
    $query = User::with($with)->where($condition);

    foreach ($orderBy as $col => $dir) {
        $query->orderBy($col, $dir);
    }

    return $paginated ? $query->paginate($limit) : $query->get();
}

public function find(int $id, array $with = [], array $condition = [])
{
    return User::with($with)->where($condition)->where('id', $id)->first();
}

public function create(array $data)
{
    return User::create($data);
}

public function update(int $id, array $data)
{
    $record = User::findOrFail($id);
    $record->update($data);
    return $record;
}

public function delete(int $id, array $condition = [])
{
    return (bool) User::where($condition)->where('id', $id)->delete();
}
```

---

### 🧱 Repository generated WITHOUT model

If you run the command **without** `--model`:

```
php artisan make:repository ReportRepository
```

Generated file:

```php
class ReportRepository
{
    // Add repository methods here
}
```

Perfect when creating a repository with custom logic.

---

## 2️⃣ Create a Service

```
php artisan make:service UserService --model=User
```

Output:

```
app/Services/UserService.php
```

### ⭐ Generated Service (when model is provided)

```php
public function allWithCondition($request)
{
    $data = $request->all();
    $condition = [];
    $with = [];
    $paginated =  false;
    $limit = 10;
    $orderBy = [];

    return $this->repository->allWithCondition($condition, $with, $paginated, $limit, $orderBy);
}

 public function find($request)
    {
        $data = $request->all();
        $id = $data['id'];
        $condition = [];
        $with = [];
        return $this->repository->find($id, $with, $condition);
    }

public function create($request)
{
    return $this->repository->create($request->all());
}

public function update($request)
{
    return $this->repository->update($request->input('id'), $request->all());
}

 public function delete($request)
    {
        $data = $request->all();
        $id = $data['id'];
        $condition = [];
        return $this->repository->delete($id, $condition);
    }
```

---

### 🧱 Service generated WITHOUT model

```
php artisan make:service ReportService
```

Produces:

```php
class ReportService
{
    // Service methods
}
```

Use it for services that do not depend on a specific model.

---

## 3️⃣ Create a Full Module (Service + Repository)

```
php artisan make:module User --model=User
```

Output:

```
app/Services/UserService.php
app/Repositories/UserRepository.php
```

Both files include fully‑functional CRUD logic.

---

# 🧪 Example: Using the Generated Service in a Controller

```php
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request)
    {
        return $this->service->allWithCondition($request);
    }

    public function store(Request $request)
    {
        return $this->service->create($request);
    }
}
```

---

# 📌 Namespaced Paths Example

You can generate into nested folders:

```
php artisan make:module Admin/User --model=User
```

Outputs:

```
app/Services/Admin/UserService.php
app/Repositories/Admin/UserRepository.php
```

---

# 📝 Requirements

- PHP 8.0+
- Laravel 8–12

---

# 📄 License

This package is open‑source and licensed under the **MIT License**.

---

# ❤️ Contributing

Pull requests are welcome!  
Feel free to open issues for improvements and ideas.
