# Laravel translatable model
Библиотека позволяет использовать Eloquent модели для удобного хранения переводов
# Установка
С помощью композера
```
composer require skelag/laravel-translatable-model
```
# Использование
## Настройка таблиц и моделей
Для использования моделей с переводами необходимо создать таблицу с основной сущностью:
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->date('written_at')->default(now());

            $table->timestamps();
        });
        
        $this->translates('books', ['author' => 'string', 'name' => 'string']);
    }

    public function down()
    {
        $this->dropTranslates('books');
        Schema::dropIfExists('books');
    }
}
```
Далее просто наследуем у основной модели класс `TranslatableModel`:
```php
namespace App\Models;

use SkelaG\LaravelTranslatableModel\Models\TranslatableModel;

class Book extends TranslatableModel
{
}
```
И модель для переводов снаследовать `TranslationModel`, указав fillable и переводимые поля (Должны быть обязательно fillable):
```php
namespace App\Models;

use SkelaG\LaravelTranslatableModel\Models\TranslationModel;

class BookTranslation extends TranslationModel
{
    protected array $translatable = ['name', 'author'];
}
```
## Использование моделей
### Создание
```php
App::setLocale('ru');
$book = \App\Models\Book::create([
    'written_at' => \Carbon\Carbon::parse('1833-01-01'),
    'author' => 'Александр Сергеевич Пушкин',
    'name' => 'Евгений Онегин'
]);
```
Или:
```php
App::setLocale('ru');
$book = new \App\Models\Book();
$book->author = 'Александр Сергеевич Пушкин';
$book->name = 'Евгений Онегин';
$book->save();
```
### Добавление переводов
Необходимо установить нужную локаль и выполнить обновление модели
```php
App::setLocale('en');
$book = \App\Models\Book::first();
$book->update(['author' => 'Alexander Sergeyevich Pushkin', 'name' => 'Eugene Onegin']);
```
или
```php
App::setLocale('en');
$book = \App\Models\Book::first();
$book->author = 'Alexander Sergeyevich Pushkin';
$book->name = 'Eugene Onegin';
```
Обновление значений в переводах происходит так же
