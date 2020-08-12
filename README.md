# Адаптер библиотеки [artem-prozorov/repos](https://github.com/artem-prozorov/repos) для Laravel

Дополняет библиотеку [artem-prozorov/repos](https://github.com/artem-prozorov/repos) рядом возможностей, предоставляемых инструментами Laravel. В частности:
1. предоставляет консольные команды для генерации интерфейсов и репозиториев;
2. адаптирует библиотеку для работы с Eloquent-моделями.

 
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
