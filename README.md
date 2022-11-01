# Currency Converter API
Currency Converter API built with Laravel 8. 

## Features
- User registration and login
- Integration with Currency API https://currencylayer.com/quickstart (Register to get an API key for free account)
- Authenticated routes for accessing Currency API
- Live currency conversion rates from currency API
- Background job for generating historical currency reports for selected currency with the given ranges: (One Year - Monthly, Six Months - Weekly, One Month - Daily)


## Tech
- Laravel 8.75
- PHPUnit 9.5.10

## Installation
Install the dependencies and devDependencies and start the server.

```sh
cd currency-converter-api
composer install

UPDATE the following in .env file

APP_DEBUG=true
QUEUE_CONNECTION=database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=**DB NAME***
DB_USERNAME=**DB USERNAME**
DB_PASSWORD=**DB PASSWORD***


CURRENCY_DATA_API_URL=**currency api url**
CURRENCY_DATA_API_KEY=**currency api key**

```

For production environments...

```sh
composer install

UPDATE the following in .env file

APP_DEBUG=false
QUEUE_CONNECTION=database
DB_CONNECTION=mysql
DB_HOST=**PRODUCTION IP**
DB_PORT=3306
DB_DATABASE=**DB NAME***
DB_USERNAME=**DB USERNAME**
DB_PASSWORD=**DB PASSWORD***

CURRENCY_DATA_API_URL=**currency api url**
CURRENCY_DATA_API_KEY=**currency api key**
```

Read more about production deployement https://laravel.com/docs/8.x/deployment

## License

MIT

**Free Software!**