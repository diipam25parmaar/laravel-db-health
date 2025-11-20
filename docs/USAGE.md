# Usage

1. Capture slow queries (use perf-audit:run or your own capture listener)
2. Run explain sampling:
   ```
   php artisan db-health:explain --folder=storage/logs/perf-audit --out=storage/app/db-health-report.json
   ```
3. View report summary:
   ```
   php artisan db-health:report --file=storage/app/db-health-report.json
   ```
