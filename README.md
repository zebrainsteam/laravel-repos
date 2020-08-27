# Адаптер библиотеки [artem-prozorov/repos](https://github.com/artem-prozorov/repos) для Laravel

Дополняет библиотеку [artem-prozorov/repos](https://github.com/artem-prozorov/repos) рядом возможностей, предоставляемых инструментами Laravel. В частности:
1. предоставляет консольные команды для генерации интерфейсов и репозиториев;
2. адаптирует библиотеку для работы с Eloquent-моделями.

## Установка
Добавление пакета в проект
```
composer require zebrainsteam/laravel-repos
```

Регистрация сервис-провайдера в `config/app.php`
```
'providers' => [

    ...

    /*
     * Application Service Providers...
     */
    ...
    \Zebrainsteam\LaravelRepos\LaravelReposServiceProvider::class,

],
```
Публикация конфигурационного файла с помощью консольной команды
```
php artisan vendor:publish --provider="Zebrainsteam\LaravelRepos\LaravelReposServiceProvider"
```
 
## Консольные команды для генерации интерфейсов и репозиториев
Создание интерфейса, наследуемого от базового интерфейса `Prozorov\Repositories\Contracts\RepositoryInterface`:

```
php artisan make:repository-interface <MyInterfaceName>
```

Создание репозитория, наследуемого от абстрактного класса `Prozorov\Repositories\AbstractRepository`:

```
php artisan make:repository <MyRepositoryName>
```
Создание репозитория `<MyRepositoryName>`, наследуемого от абстрактного класса `Prozorov\Repositories\AbstractRepository`, и реализуемого им интерфейса с автоматической генерацией имени `<MyRepositoryName>Contract` (интерфейс будет расширять `Prozorov\Repositories\Contracts\RepositoryInterface`):

```
php artisan make:repository <MyRepositoryName> --with-interface
```

Та же операция, но с явным указанием имени интерфейса:

```
php artisan make:repository <MyRepositoryName> --with-interface <MyInterfaceName>
```

Создание репозитория `<MyRepositoryName>` и реализуемого им интерфейса с автоматической генерацией имени `<MyRepositoryName>Contract` (интерфейс будет расширять `Prozorov\Repositories\Contracts\RepositoryInterface`):

```
php artisan make:repository <MyRepositoryName> --from-interface
```

Та же операция, но с явным указанием имени интерфейса:

```
php artisan make:repository <MyRepositoryName> --from-interface <MyInterfaceName>
```

**Внимание!** При указании имени интерфейса/репозитория имеется возможность задать расположение/нэймспейс нового класса с помощью разделителя `/`. При этом действует следующее правило: если имя начинается с `/`, то нэймспейс формируется от корневого (`App\ `); иначе нэймспейс формируется от дефолтного (для репозитория - `App\Repositories\ `, для интерфейсов - `App\Contracts\Repository\ `).

## Работа с Eloquent-моделями
В библиотеку добавлены репозиторий для работы с Eloquent-моделями `Zebrainsteam\LaravelRepos\EloquentRepository`, а также  два резолвера для работы с ним: `Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver` и `Zebrainsteam\LaravelRepos\Resolvers\AutoResolver`.

#### EloquentRepository
 унаследован от абстрактного класса `Prozorov\Repositories\AbstractRepository` и содержит реализацию всех его методов посредством штатных инструментов фреймворка по работе с моделью.
 
#### EloquentAwareResolver
позволяет автоматически создавать репозиторий типа `EloquentRepository` для заданного класса модели, если последняя унаследована от `Illuminate\Database\Eloquent\Model`:
```
class User extends Illuminate\Database\Eloquent\Model
{
    ...
}

class Car extends Illuminate\Database\Eloquent\Model
{
    ...
}

$resolver = new Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver();

$userRepository = $resolver->resolve(User::class);
$firstUser = $userRepository->getById(1);

$carRepository = $resolver->resolve(Car::class);
$redCarExists = $carRepository->exists(['color' => 'red']);
```

#### AutoResolver
позволяет автоматически создавать репозиторий для заданного класса модели, если последняя содержит в себе конструктор репозитория (реализует интерфейс `Prozorov\Repositories\Contracts\HasRepositoryInterface`) либо 
унаследована от `Illuminate/Database/Eloquent/Model`:
```
class User implements Prozorov\Repositories\Contracts\HasRepositoryInterface
{
    ...

    public static function getRepository(): Prozorov\Repositories\Contracts\RepositoryInterface;
    {
        return new UsersSuperRepository();
    }
}

class Car extends Illuminate\Database\Eloquent\Model
{
    ...
}

$resolver = new Zebrainsteam\LaravelRepos\Resolvers\AutoResolver();

$userRepository = $resolver->resolve(User::class); //создаст экземпляр UsersSuperRepository

$carRepository = $resolver->resolve(Car::class); //создаст экземпляр EloquentRepository

```

## Работа с фабрикой репозиториев

С репозиториями и резолверами можно работать отдельно, однако лучшим способом создания репозиториев является использование `LaravelRepositoryFactory`. Для этого должен быть опубликован конфигурационный файл `config/repositories.php` (см. инструкцию по установке пакета выше). В нем настраивается перечень резолверов и параметров, которые будут использованы при создании репозитория:
```
<?php

return [
    'common' => [
        'resolvers' => [
            'Prozorov\Repositories\Resolvers\SelfResolver',
            'Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver',
            'Prozorov\Repositories\Resolvers\ContainerAwareResolver',
        ],
        'bindings' => [
            'users' => 'App\User',
            ...
        ],
    ],
    'custom1' => [
        'resolvers' => [
            ...
        ],
        'bindings' => [
            'anotherAlias' => ...,
        ],
    ],
    ...
];
```
Если в группе 'resolvers' указано несколько резолверов, то они будут объединены в `Prozorov\Repositories\Resolvers\ChainResolver` (этот класс позволяет использовать цепочку из загрузчиков; он принимает в конструктор массив из других классов-резолверов, и для разрешения репозитория последовательно обращается к каждому из них, пока какой-нибудь не разрешит репозиторий)
С учетом таких настроек создавать репозитории можно следующим образом:

```
$commonFactory = LaravelRepositoryFactory::init(); //по умолчанию берутся настройки из группы "common" и возвращается синглтон-класс фабрики
$usersRepository = $repositoryFactory->getRepository('users');
$luckyUser = $usersRepository->getById(13);

$customFactory = LaravelRepositoryFactory::init('custom1');
$anotherRepository = $repositoryFactory->getRepository('anotherAlias');
...
```
