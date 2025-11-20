<?php
namespace DbHealth\Commands;

use Illuminate\Console\Command;

class HealthReportCommand extends Command
{
    protected $signature = 'db-health:report {--file=}';
    protected $description = 'Generate a DB health summary report from explain plans or captures.';

    public function handle()
    {
        $this->info('Generating DB health report...');
        $file = $this->option('file') ?: storage_path('app/db-health-report.json');
        if (!file_exists($file)) {
            $this->error('Report file not found: '.$file);
            return 1;
        }
        $data = json_decode(file_get_contents($file), true);
        if (empty($data)) {
            $this->info('No data to report.');
            return 0;
        }
        // summarize by table and type
        $summary = [];
        foreach ($data as $item) {
            $table = $item['table'] ?? 'unknown';
            $issue = $item['issue'] ?? 'ok';
            if (!isset($summary[$table])) $summary[$table] = ['issues'=>0,'items'=>0];
            $summary[$table]['items'] += 1;
            if ($issue !== 'ok') $summary[$table]['issues'] += 1;
        }
        $rows = [];
        foreach ($summary as $table => $s) {
            $rows[] = [$table, $s['items'], $s['issues']];
        }
        $this->table(['table','sampled','issues'], $rows);
        $this->info('Full report saved at: '.$file);
        return 0;
    }
}
