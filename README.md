# Trip Planner

## Requirements
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [DDEV](https://ddev.readthedocs.io/en/stable/)

## Setup

1. Clone the repository
```bash
git clone <repo-url>
cd trip_planer
```

2. Start DDEV
```bash
ddev start
```

3. Copy environment file
```bash
cp .env.example .env
```

4. Install dependencies
```bash
ddev composer install
```

5. Generate app key
```bash
ddev artisan key:generate
```

6. Run migrations and seeders
```bash
ddev artisan migrate --seed
```

7. Open the app at **https://trip-planner.ddev.site**

## Mail (local)
Mailpit is available at **https://trip-planner.ddev.site:8026**
