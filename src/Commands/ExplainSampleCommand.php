<?php
namespace DbHealth\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use DbHealth\Report\Reporter;

class ExplainSampleCommand extends Command
{
    protected $signature = 'db-health:explain {--folder=} {--out=}';
    protected $description = 'Run EXPLAIN on sampled captured queries and save explain plans.';

    public function handle()
    {
        $this->info('Running DB health EXPLAIN sampling...');
        $folder = $this->option('folder') ?: storage_path('logs/perf-audit');
        $out = $this->option('out') ?: config('db-health.report.out', storage_path('app/db-health-report.json'));
        if (!is_dir($folder)) {
            $this->error('Capture folder not found: '.$folder);
            return 1;
        }
        $files = glob(rtrim($folder, DIRECTORY_SEPARATOR).'/*.json');
        $sample = [];
        foreach ($files as $f) {
            $data = json_decode(file_get_contents($f), true);
            if (!$data || empty($data['sql'])) continue;
            if (($data['time_ms'] ?? 0) < config('db-health.sample.min_duration_ms', 5)) continue;
            $sample[] = $data;
            if (count($sample) >= config('db-health.sample.sample_size', 50)) break;
        }
        $reporter = $this->getLaravel()->make('dbhealth.reporter');
        $plans = $reporter->explainSample($sample);
        file_put_contents($out, json_encode($plans, JSON_PRETTY_PRINT));
        $this->info('Saved explain plans to '.$out);
        return 0;
    }
}
