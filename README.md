# laravel-db-health

Lightweight DB health package for Laravel. Features:
- EXPLAIN sampling for captured slow queries
- Basic plan analysis to detect missing indexes or full table scans
- Health summary report command
- Small, easy to extend reporter to add EXPLAIN/ANALYZE for Postgres

## Commands
- `php artisan db-health:explain` — run EXPLAIN on captured queries and save plans
- `php artisan db-health:report` — generate a summary table of issues

## Quick install
1. Place package in `packages/your-vendor/laravel-db-health` (or use composer path repo)
2. `composer require your-vendor/laravel-db-health:@dev`
3. `php artisan vendor:publish --provider="DbHealth\DbHealthServiceProvider" --tag=config`
