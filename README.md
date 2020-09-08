[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zebrainsteam/laravel-repos/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zebrainsteam/laravel-repos/?branch=master)

# Адаптер библиотеки [zebrainsteam/repos](https://github.com/zebrainsteam/repos) для Laravel

Дополняет библиотеку [zebrainsteam/repos](https://github.com/zebrainsteam/repos) рядом возможностей, предоставляемых инструментами Laravel. В частности:
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
Создание интерфейса, наследуемого от базового интерфейса `Repositories\Core\Contracts\RepositoryInterface`:

```
php artisan make:repository-interface <MyInterfaceName>
```

Создание репозитория, наследуемого от абстрактного класса `Repositories\Core\AbstractRepository`:

```
php artisan make:repository <MyRepositoryName>
```
Создание репозитория `<MyRepositoryName>`, наследуемого от абстрактного класса `Repositories\Core\AbstractRepository`, и реализуемого им интерфейса с автоматической генерацией имени `<MyRepositoryName>Contract` (интерфейс будет расширять `Repositories\Core\Contracts\RepositoryInterface`):

```
php artisan make:repository <MyRepositoryName> --with-interface
```

Та же операция, но с явным указанием имени интерфейса:

```
php artisan make:repository <MyRepositoryName> --with-interface <MyInterfaceName>
```

Создание репозитория `<MyRepositoryName>` и реализуемого им интерфейса с автоматической генерацией имени `<MyRepositoryName>Contract` (интерфейс будет расширять `Repositories\Core\Contracts\RepositoryInterface`):

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
 унаследован от абстрактного класса `Repositories\Core\AbstractRepository` и содержит реализацию всех его методов посредством штатных инструментов фреймворка по работе с моделью.
 
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
позволяет автоматически создавать репозиторий для заданного класса модели, если последняя содержит в себе конструктор репозитория (реализует интерфейс `Repositories\Core\Contracts\HasRepositoryInterface`) либо 
унаследована от `Illuminate/Database/Eloquent/Model`:
```
class User implements Repositories\Core\Contracts\HasRepositoryInterface
{
    ...

    public static function getRepository(): Repositories\Core\Contracts\RepositoryInterface;
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

С репозиториями и резолверами можно работать отдельно, однако лучшим способом создания репозиториев является использование `Repositories\Core\RepositoryFactory`. Для этого должен быть опубликован конфигурационный файл `config/repositories.php` (см. инструкцию по установке пакета выше). В нем настраивается перечень резолверов и параметров, которые будут использованы при создании репозитория:
```
<?php

return [
    'resolvers' => [
        'Repositories\Core\Resolvers\ExistingRepositoryResolver',
        'Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver',
        'Repositories\Core\Resolvers\ContainerAwareResolver',
        ...
    ],
    'bindings' => [
        'users' => 'App\User',
        'cars' => 'App\Cars',
        ...
    ],
];
```
Если в группе 'resolvers' указано несколько резолверов, то они будут объединены в `Repositories\Core\Resolvers\ChainResolver` (этот класс позволяет использовать цепочку из загрузчиков; он принимает в конструктор массив из других классов-резолверов, и для разрешения репозитория последовательно обращается к каждому из них, пока какой-нибудь не разрешит репозиторий)
С учетом таких настроек создавать репозитории можно следующим образом:

```
$repositoryFactory = App::get(RepositoryFactory::class); //возвращается синглтон-класс фабрики
$usersRepository = $repositoryFactory->getRepository('users');
$luckyUser = $usersRepository->getById(13);

$carsRepository = $repositoryFactory->getRepository('cars');
$firstRedCar = $carsRepository->first(['color' => 'red']);
...
```
