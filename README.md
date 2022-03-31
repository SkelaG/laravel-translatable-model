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
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
```
И таблицу с переводами:
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('book_translations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('author');
            $table->string('locale');
            $table->foreignIdFor(\App\Models\Book::class);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_translations');
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
    protected $fillable = ['name', 'author', 'locale'];
}
```
## Использование моделей
### Создание
