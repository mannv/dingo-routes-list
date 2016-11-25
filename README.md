# Dingo Routes List Display
Display The Lumen Registered Routes List Same As Laravel


## Installation

1. Run 
    ```
    composer require mannv/dingo-routes-list:1.0.x@dev
    ```
    
2. Add service provider into **/bootstrap/app.php** file.
    ```php
    $app->register(Mannv\DingoRoutesList\RoutesCommandServiceProvider::class);
    ```
3. Run **composer update**

## Commands

```
php artisan route:list {default version name: API_VERSION}
EX: 
- artisan route:list v1
- artisan route:list v2
```


##Author
...
