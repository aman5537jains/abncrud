## ABN CRUD (Laravel Package)

ABN CRUD is a Laravel package that lets you scaffold powerful CRUD screens quickly, auto-generate forms from your database schema, and customize fields with reusable UI components.

### Key features
- **Zero-boilerplate CRUD**: RESTful routes, index table, create/edit forms, delete and status toggle.
- **Auto form generation** from DB column types, with sensible defaults.
- **Config-driven customization** using `config/crud.php` per-field overrides and view components.
- **Artisan generator** to create a model and CRUD controller from a table: `php artisan make:abncrud`.
- **Publishable assets** (JS, etc.) and packaged views.


## 1) Installation

1. Require the package (e.g., via path/VCS or Packagist):

```bash
composer require abno/abncrud
```

2. The service provider is auto-discovered via composer extra. If you disable discovery, register it manually in `config/app.php`:

```php
Aman5537jains\AbnCmsCRUD\AbnCmsCRUDServiceProvider::class,
```


## 2) Publish config and assets

```bash
# Publish config to config/crud.php
php artisan vendor:publish --provider="Aman5537jains\AbnCmsCRUD\AbnCmsCRUDServiceProvider" --tag=config

# Publish public assets to public/vendor/abncrud
php artisan vendor:publish --provider="Aman5537jains\AbnCmsCRUD\AbnCmsCRUDServiceProvider" --tag=assets
```

Assets will be available under `public/vendor/abncrud`.


## 3) Views

The package registers views under the namespace `AbnCmsCrud`.

- To use the package views directly, set the controller theme to `AbnCmsCrud::`.
- Or copy/override views into your app `resources/views/crud/*` and keep `theme` empty.

Example inside your CRUD controller:

```php
protected $theme = 'AbnCmsCrud::'; // use packaged views
```

Packaged views include table, form and single-view screens.


## 4) Quick start (generator)

Generate model and controller for a given table:

```bash
php artisan make:abncrud posts

# Options (optional directories if you don’t use defaults):
php artisan make:abncrud posts --modelPath=app/Domain/Blog/Models --controllerPath=app/Http/Controllers/Admin
```

What you get:
- Model at `app/Models/Post.php` (or the path you specified)
- Controller at `app/Http/Controllers/PostController.php` (or the path you specified)


## 5) Define your CRUD controller

Your controller should extend the base `CrudController`, set the module slug and model, and optionally the title and theme.

```php
namespace App\Http\Controllers\Admin;

use Aman5537jains\AbnCmsCRUD\CrudController;

class PostsController extends CrudController
{
    public static $module = 'posts';              // URL + route name prefix
    public static $moduleTitle = 'Posts';         // UI headings
    public $model = \App\Models\Post::class;    // Eloquent model
    protected $theme = 'AbnCmsCrud::';            // use packaged views (optional)
}
```


## 6) Register routes

There are two ways to register routes.

- Minimal (controller self-register):

```php
// routes/web.php
\App\Http\Controllers\Admin\PostsController::resource();
```

This will register:
- Resource routes for `posts`
- Extra GET routes: `/posts/changeStatus/{id}` and `/posts/{id}/delete`

- Advanced (explicit using RouteService):

```php
use Aman5537jains\AbnCmsCRUD\Lib\RouteService;
use App\Http\Controllers\Admin\PostsController;

RouteService::resource('posts', PostsController::class, function ($r) {
    // Add optional custom routes inside the posts group
    // $r->get('export');
});
```

The package also exposes a helper route for component rendering:

```text
GET /component-render  (named: component-render)
```


## 7) How forms and tables are built

ABN CRUD auto-inspects your model’s table to guess field components:
- `int`, `decimal` → number input
- `varchar` → text input (or file if the column name contains `file`)
- `enum` → select with enum options
- `date`, `time`, `datetime` → corresponding inputs
- `text` → textarea

You can override per-field behavior via `config/crud.php`.


## 8) Configure fields (config/crud.php)

When you publish config, you’ll get `config/crud.php` similar to:

```php
return [
    'view_fields' => [
        // 'status' => [ 'class' => \Aman5537jains\AbnCmsCRUD\Components\TextComponent::class, 'config' => [] ],
    ],
    'form_fields' => [
        // 'status' => [ 'class' => \Aman5537jains\AbnCmsCRUD\Components\InputComponent::class, 'config' => ['type' => 'select', 'options' => ['1' => 'Active', '0' => 'Inactive']] ],
    ],
    'components' => [
        // Optional: map friendly names to components
    ],
];
```

Example: humanize a `branch_id` in the table view and render images:

```php
'view_fields' => [
    'branch_id' => [
        'class' => \Aman5537jains\AbnCmsCRUD\Components\TextComponent::class,
        'config' => [
            'label' => 'Branch',
            'beforeRender' => function ($component) {
                $component->setValue(optional(\App\Models\Branch::find($component->getValue()))->branch_name);
            }
        ]
    ],
    'image' => [ 'class' => \Aman5537jains\AbnCmsCRUD\Components\ImageComponent::class, 'config' => [] ],
    'thumb' => [ 'class' => \Aman5537jains\AbnCmsCRUD\Components\ImageComponent::class, 'config' => ['height' => 50, 'width' => 50] ],
],
```

Example: form defaults and select options:

```php
'form_fields' => [
    'status' => [
        'class' => \Aman5537jains\AbnCmsCRUD\Components\InputComponent::class,
        'config' => [ 'type' => 'select', 'value' => '1', 'options' => ['1' => 'Active', '0' => 'Inactive'] ]
    ],
    'image' => [ 'class' => \Aman5537jains\AbnCmsCRUD\Components\FileInputComponent::class, 'config' => [] ],
],
```


## 9) Permissions

The base controller checks permissions via `getPermissions()` which you can implement on your controller. Returning `'superadmin'` or a map containing `"{module}___{action}"` keys allows the action. For example:

```php
public function getPermissions()
{
    // Allow everything for demo
    return 'superadmin';
}
```

Actions checked: `view`, `add`, `edit`, `delete`, and `status`.


## 10) Endpoints generated

Given `public static $module = 'posts'`, the following will be registered:
- `GET /posts` → index (search + table)
- `GET /posts/create` → create form
- `POST /posts` → store
- `GET /posts/{slug}` → show (single view)
- `GET /posts/{slug}/edit` → edit form
- `PUT/PATCH /posts/{slug}` → update
- `DELETE /posts/{slug}` → destroy
- `GET /posts/changeStatus/{id}` → toggle status
- `GET /posts/{id}/delete` → delete


## 11) Advanced: live updates in forms

Forms support live updates for dependent fields. Emit `live_listners`/`live_emitter` in the request; the controller will re-render target fields and return HTML snippets in JSON. See `CrudController::liveUpdate()` for details.


## 12) Component reference (selected)

Common components you can use in `config/crud.php` or when customizing builders:
- `Aman5537jains\AbnCmsCRUD\Components\InputComponent` (types: text, number, date, time, datetime, textarea, select)
- `Aman5537jains\AbnCmsCRUD\Components\FileInputComponent`
- `Aman5537jains\AbnCmsCRUD\Components\ImageComponent`
- `Aman5537jains\AbnCmsCRUD\Components\TextComponent`
- `Aman5537jains\AbnCmsCRUD\Components\LinkComponent`
- `Aman5537jains\AbnCmsCRUD\Components\ChangeStatusComponent`
- `Aman5537jains\AbnCmsCRUD\Components\MultiComponent`
- `Aman5537jains\AbnCmsCRUD\Components\SubmitButtonComponent`


## 13) Example end-to-end

1) Migration with a `slug` and `status` column (recommended by defaults):

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('body');
    $table->string('slug')->unique();
    $table->enum('status', ['0','1'])->default('1');
    $table->timestamps();
});
```

2) Model: `App\Models\Post`

3) Controller:

```php
class PostsController extends CrudController
{
    public static $module = 'posts';
    public static $moduleTitle = 'Posts';
    public $model = \App\Models\Post::class;
    protected $theme = 'AbnCmsCrud::';

    public function getPermissions() { return 'superadmin'; }
}
```

4) Routes:

```php
\App\Http\Controllers\Admin\PostsController::resource();
```

Visit `/posts` to use the CRUD UI.


## 14) Helper route for components

The package registers:

```text
GET /component-render  (named: component-render)
```

It returns rendered component HTML based on the current request and is used internally for dynamic UI behaviors.


## 15) Support

- Author: aman (ajain@abnosoftwares.co.ke)
- License: MIT

