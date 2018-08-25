# Laravel Repositories

[![Latest Stable Version](https://poser.pugx.org/ollieread/laravel-repositories/v/stable.png)](https://packagist.org/packages/ollieread/laravel-repositories) [![Total Downloads](https://poser.pugx.org/ollieread/laravel-repositories/downloads.png)](https://packagist.org/packages/ollieread/laravel-repositories) [![Latest Unstable Version](https://poser.pugx.org/ollieread/laravel-repositories/v/unstable.png)](https://packagist.org/packages/ollieread/laravel-repositories) [![License](https://poser.pugx.org/ollieread/laravel-repositories/license.png)](https://packagist.org/packages/ollieread/laravel-repositories)

- **Laravel**: 5.6
- **PHP**: 7.1+
- **Author**: Ollie Read 
- **Author Homepage**: https://ollieread.com

Repositories don't need to be complicated, and this package is here to prove that.

## FAQ
Before we get into the specifics, here's a quick FAQ.

### What is the point of having a repository?
The repository pattern exists to provide a level of abstraction between database/datasource interactions and the logic of the codebase.

### Doesn't Eloquent already cover this?
Yes and No. Eloquent uses the Active Record pattern and as such, suffers a bit of a single concern crisis. Eloquent can do everything that you's
typically do in a repository, but that also means that there's no one place storing your queries. They're everywhere.

### What's wrong with queries everywhere?
You've got a model, you're querying this model in 15 places. 13 of those places need an extra condition. You now need to dig out every query and
update them. You can't add a global scope because not everything needs this. I promise you, more often than not you'll miss one.

### But why would I use repositories?
Repositories will group all of your database/datasource interactions into one central location. This means that as your codebase grows
you know exactly where to go, and can easily get vision on a models entire interaction with the database/datasource.

### I don't want to define loads of interfaces
Good, you shouldn't have to. Follow YAGNI. If you truly do end up switching out your database/datasource, having a bunch of interfaces isn't
going to help at all. Sure you may think it will, but trust me. It doesn't. On top of that, it's very unlikely you'd ever even do this.

### But my repository will be massive
It shouldn't be. One of the great purposes of repositories is that it can help follow DRY. If you've got 15 almost identical queries, you
probably only need one method with one or more arguments, sorted.

### If I expose all of the Eloquent functionality like relationships, doesn't the repository just become Eloquent?
Yes, that would happen. That's why you shouldn't do that. In the original definition of the repository design pattern a thing called
`Specification` was used to help add context and build up the query. In this package I use `Criteria` and provide some basic ones for use.

For example, you could have a `PostRepository::getPosts()` method. If you wanted to get posts for a specific user you'd add a criteria object
`$repository->with(new ForUser($user))->getPosts()`, perhaps you only want posts for a given category? `$repository->with(new ForCategory($category))->getPosts()`.
Immediately you're writing less code, the process is simpler and easily understandable. These criteria can also be used by anything else that has
a relationship with users or categories.

### I'm still not sold
Well it's not for everyone. Ultimately the choice is yours in whether or not use this package or even this design pattern. All I ask is
that you look at objectively and give it a go. If you go in thinking it'll just make things more difficult, you'll find a reason to dislike it.

## Installation
Super simple and super fun. Run the following command in your terminal.

    composer require ollieread/laravel-repositories

## Configuration
There is none just yet, but there may be in the future.

## Usage
To use this package, simple create yourself a repository, lets say `App\Repositories\PostRepository` and extend the base repository;

    Ollieread\Repositories\Repository

Next you define your `model()` method to return the FQN for the model this repository represents.

```php
protected function mode(): string
{
    return Post::class;
}
```

There you go, a fully functioning repository. I like to add in docblocks to hint at the parent return types.

```php
<?php
namespace App\Repositories;

use Ollieread\Repositories\Repository;

/*
 * @method Post make(array $arguments = [])
 * @method null|Post first(array $arguments = [])
 */
class PostRepository extends Repository
{
    protected function model(): string
    {
        return Post::class;
    }
}
```

Now you have access to all that the base repository provides.

### Getting all models

    Repository::get(array $arguments = []): Collection

This is the same as `Model::get()` or `Model::all()`, except that you can provide an array of `column => value` entries for a simple `=`
where clause.

### Getting first model
   
    Repository::first(array $arugments = []): ?Model
    
This is the same as `Model::first()` but just like above, you can pass in an array for a where clause.

### Getting a paginated result

    Repository::paginate(array $arguments = [], int $count = 20, string $pageName = 'page', int $page = 1, array $columns = ['*']): LengthAwarePaginator
    
The above is the same as `Model::paginate()` except with the where clause array.

    Repository::simplePaginate(array $arguments = [], int $count = 20, string $pageName = 'page', int $page = 1, array $columns = ['*']): Paginator
    
The above is the same as `Model::simplePaginate()` except with the where clause array.

## Criteria
Criteria are a way of adding extra..well..criteria to queries without having to define a load of methods inside your repository.
All criteria should extend the base class;

    Ollieread\Repositories\Criteria
    
And they should all define a `perform($query)` method.

```php
/**
 * @param \Illuminate\Database\Eloquent\Builder $query
 */
public function perform($query)
{
    // db stuff here
}
```

To add criteria to query you can use the `with()` method on the repository;

```php
$repository->with(new WithTrashed)->paginate([], 20);
```

Adding criteria will add it to the current repository instance, so if you need to call another method without this criteria, you can flush it;

```php
$repository->flushCriteria()
```

If you want to keep the criteria but just disable it, you can do this;

```php
$repository->noCriteria()->paginate([], 20);
```

If you want to enable criteria again you can do this;

```php
$repository->useCriteria()->paginate([], 20);
```


Generally, you'd use criteria in the situation where you want to add a clause, perhaps just a where, that's something other than a `=` clause.
The criteria can be as generic or as specific as you want, there are no rules. You could use the examples in the FAQ above if you really wished.
Try to remember that although the criteria has knowledge of the database, you shouldn't need knowledge of it to use the criteria. An example
of this would be something like `new For('user', $user)`. Here you have to know about the user relationship, and its name. A better approach
would be `new ForUser($user)`.

There are a couple of default criteria provided to give you an example.

### Eager load relationships
    
    new Ollieread\Repositories\Criteria\WithRelations('relation1', 'relation2')
    
This is the same as using `Model::with()`.
    
### Include soft deleted models

    new Ollieread\Repositories\Criteria\WithTrashed
    
This will only works with models that have the soft delete trait and is the same as `Model::withTrashed()`.
    
### Order by the created at column

    new Ollieread\Repositories\Criteria\OrderedByCreation(bool $descending = true)
    
This is the same as `Model::orderBy('created_at')`.
    
### Order by the updated at column

    new Ollieread\Repositories\Criteria\OrderedByModification(bool $descending = true)
    
This is the same as `Model::orderBy('updated_at')`.
    