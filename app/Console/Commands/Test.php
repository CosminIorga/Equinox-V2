<?php

namespace Equinox\Console\Commands;

use Carbon\Carbon;
use Equinox\Events\RequestCapsuleSave;
use Equinox\Services\Data\CapsuleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equinox:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Test constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Command runner
     */
    public function handle()
    {
//        $this->testInsert();
//
//        return;

        try {
            /** @var CapsuleService $capsuleService */
            $capsuleService = app(CapsuleService::class);

            $referenceDate = new Carbon();
            $capsules = $capsuleService->createCapsulesByReferenceDate($referenceDate);

            dump("Sending event");

            foreach ($capsules as $capsule) {
                event(new RequestCapsuleSave($capsule));
            }

            //$dataService->generateCapsules($capsules);
        } catch (\Exception $exception) {
            dump($exception);
        }
    }

    protected function testInsert()
    {
        $values = [];

        foreach (range(0, 10000 - 1) as $index) {
            $index = rand(0, 3);

            $set = [
                'client' => "'A" . rand(0, 10) . "'",
                'carrier' => "'B" . rand(0, 10) . "'",
                'destination' => "'C" . rand(0, 10) . "'",
                "int0" => (int) ($index == 0),
                "int1" => (int) ($index == 1),
                "int2" => (int) ($index == 2),
                "int3" => (int) ($index == 3),
            ];

            array_unshift($set, "'" . md5($set['client'] . "_" . $set['carrier'] . "_" . $set['destination']) . "'");

            $values[] = "(" . implode(', ', $set) . ")";
        }

        $values = implode(', ' . PHP_EOL, $values);

        $query = "INSERT INTO Daily_2018_04_30_Agg_interval_cost 
            (hash_id, client, carrier, destination, interval_0, interval_1, interval_2, interval_3) VALUES $values
            ON DUPLICATE KEY UPDATE interval_0 = interval_0 + IFNULL(VALUES(interval_0), 0)
            , interval_1 = interval_1 + IFNULL(VALUES(interval_1), 0)
            , interval_2 = interval_2 + IFNULL(VALUES(interval_2), 0)
            , interval_3 = interval_3 + IFNULL(VALUES(interval_3), 0)
";

        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        DB::unprepared($query);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Elapsed $elapsed" . PHP_EOL;
    }

    protected function testInsertRep()
    {


    }

}
