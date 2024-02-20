# Laravel Sending:
[![Laravel](https://img.shields.io/badge/Laravel-%23F9322C.svg?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Pest](https://img.shields.io/badge/Pest-%2399A8DE.svg?style=for-the-badge&logo=php&logoColor=white)](https://pestphp.com/)
[![Laravel Excel](https://img.shields.io/badge/Laravel_Excel-%23F7A81F.svg?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel-excel.com/)
[![React](https://img.shields.io/badge/React-%23087EA4.svg?style=for-the-badge&logo=react&logoColor=white)](https://reactjs.org/)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-%23846BED.svg?style=for-the-badge&logo=javascript&logoColor=white)](https://inertiajs.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-%2338BDF8.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Docker](https://img.shields.io/badge/Docker-%231D63ED.svg?style=for-the-badge&logo=docker&logoColor=white)](https://tailwindcss.com/)

This is a full project to import contacts and dispatch mass email sending

## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/natanaugusto/laravel-sendings
cd laravel-sendings
```

2. Setting up the containers
```bash
cp .env.example .env
echo "\nWWWUSER=1000\nWWWGROUP=1000" >> .env

docker-compose up -d
docker exec -it --user sail laravel-sendings_laravel.test_1 composer install --verbose
```

3. Setting app the backend application(Laravel)
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

4. Setting up the frontend
```bash
npm i --verbose
npm run build
```

Visit http://localhost in your browser to access the application.

5. Running the tests
```bash
# You can do this same steps to create a large file for tests, you just need
# to change the count from 20 to 1000, or more as you wish
./vendor/bin/sail artisan tinker
> (new App\Exports\ContactsExport(App\Models\Contact::factory(20)->make()))->store('example.xlsx', 'spreadsheets');
= true
> exit

# Running pest
./vendor/bin/sail pest # For test the backend application
```
