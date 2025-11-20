<?php
namespace DbHealth\Listeners;

class SlowQueryCapture
{
    protected $minMs;
    protected $outDir;

    public function __construct($minMs = 5)
    {
        $this->minMs = (int)$minMs;
        $this->outDir = storage_path('logs/perf-audit');
        if (!is_dir($this->outDir)) mkdir($this->outDir, 0755, true);
    }

    public function handle($query, $bindings = null, $time = null)
    {
        if (is_object($query) && property_exists($query,'time')) {
            $time = $query->time;
            $sql = $query->sql;
            $bindings = $query->bindings ?? [];
        } else {
            $sql = $query;
        }
        if ($time < $this->minMs) return;
        $entry = ['timestamp'=>microtime(true),'sql'=>$sql,'bindings'=>$bindings,'time_ms'=>$time];
        $fn = $this->outDir.'/capture-'.date('Ymd-His').'-'.substr(sha1($sql),0,6).'.json';
        file_put_contents($fn, json_encode($entry, JSON_PRETTY_PRINT));
    }
}
