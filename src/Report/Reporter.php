<?php
namespace DbHealth\Report;

use Illuminate\Support\Facades\DB;

class Reporter
{
    protected $db;
    protected $log;
    protected $driver;

    public function __construct($db, $log)
    {
        $this->db = $db;
        $this->log = $log;
        $this->driver = $db->getDriverName();
    }

    public function explainSample(array $samples)
    {
        $out = [];
        foreach ($samples as $s) {
            $sql = $s['sql'] ?? '';
            $time = $s['time_ms'] ?? 0;
            try {
                // prepare a safe explain; attempt to replace bindings with NULLs
                $explainSql = 'EXPLAIN ' . $this->stripLimit($sql);
                $plans = DB::select($explainSql);
            } catch (\Exception $e) {
                $plans = [['error' => $e->getMessage()]];
            }
            // basic heuristic: if plan contains type or key = null, flag for index
            $issue = 'ok';
            $table = $this->extractFirstTable($sql);
            foreach ($plans as $p) {
                $str = json_encode($p);
                if (stripos($str, 'range') !== false || stripos($str, 'ALL') !== false || stripos($str, 'NULL') !== false) {
                    $issue = 'missing_index';
                }
            }
            $out[] = ['sql'=>$sql,'time_ms'=>$time,'table'=>$table,'issue'=>$issue,'plan'=>$plans];
        }
        return $out;
    }

    protected function stripLimit($sql)
    {
        // remove trailing LIMIT clauses to get a more stable explain
        return preg_replace('/\s+limit\s+\d+(\s*,\s*\d+)?/i', '', $sql);
    }

    protected function extractFirstTable($sql)
    {
        if (preg_match('/from\s+([`\w]+)/i', $sql, $m)) {
            return trim($m[1], '`');
        }
        return 'unknown';
    }
}
