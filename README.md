# ACCOUNT SHOP API VERSION 2

- No thing

## Requirements

- PHP version ^8.
- Laravel ^8

## Services

There are services that this project used.

- Dropbox file service.

## For developers

There are some suggests when set up env to develop this project.

- ...
  
## For production

There are some suggests to help you deploy easily this project in production.

1 Back up app

This project use `spatie/laravel-backup` to backup. With some nice command.
Firstly please declare env `BACKUP_DISKS`, `BACKUP_NOTIFIABLE_EMAILS`

``` command
    php artisan backup:run
    php artisan backup:clean
    php artisan backup:list
    php artisan backup:monitor
```
