
<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>  
  
<p align="center">  
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>  
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>  
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>  
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>  
</p>  
  
## Guide  
  
### Installation steps:  

1. composer install   
  
2. cp .env.example .env  
  
3. php artisan key:generate  
  
4. create a mysql database and add this user in the .env file  

5. php artisan migrate
  
### Running the app
    php artisan serve
    
 #### Served at http://127.0.0.1:8000   

###  Testing 

1.   ./vendor/bin/phpunit