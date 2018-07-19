<?php

namespace Equinox\Console\Commands;

use Carbon\Carbon;
use Equinox\Events\RequestCapsuleGenerate;
use Equinox\Events\RequestDataMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Thumper\RpcClient;
use Thumper\RpcServer;


class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equinox:test {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var RpcServer
     */
    private $rpcServer;
    /**
     * @var RpcClient
     */
    private $rpcClient;

    /**
     * Test constructor.
     * @param RpcServer $rpcServer
     * @param RpcClient $rpcClient
     */
    public function __construct(
        RpcServer $rpcServer,
        RpcClient $rpcClient
    ) {
        parent::__construct();

        $this->rpcServer = $rpcServer;
        $this->rpcClient = $rpcClient;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        switch ($this->argument('action')) {
            case "create":
                $this->testCreate();
                break;
            case "insert":
                $this->testInsertRep();
                break;
            case "parallel-server":
                $this->testParallelServer();
                break;
            case "parallel-client":
                $this->testParallelClient();
                break;
            case "fetch":
                $this->testFetchData();
                break;
            default:
                throw new \Exception("unknown argument");
        }
    }

    protected function testCreate()
    {
        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        $referenceDate = new Carbon();

        event(new RequestCapsuleGenerate($referenceDate));

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Elapsed: $elapsed" . PHP_EOL;
    }

    protected function testInsert()
    {
        $values = [];

        foreach (range(0, 10000 - 1) as $index) {
            $index = rand(0, 1);

            $set = [
                'client' => "'A" . rand(0, 10) . "'",
                'carrier' => "'B" . rand(0, 10) . "'",
                'destination' => "'C" . rand(0, 10) . "'",
                "int0" => (int) ($index == 0),
                "int1" => (int) ($index == 1),
//                "int2" => (int) ($index == 2),
//                "int3" => (int) ($index == 3),
            ];

            array_unshift($set, "'" . md5($set['client'] . "_" . $set['carrier'] . "_" . $set['destination']) . "'");

            $values[] = "(" . implode(', ', $set) . ")";
        }

        $values = implode(', ' . PHP_EOL, $values);

        $query = "INSERT INTO Daily_2018_05_30_Agg_interval_cost 
            (hash_id, client_x, carrier_x, destination_x, interval_0, interval_1) VALUES $values
            ON DUPLICATE KEY UPDATE interval_0 = interval_0 + IFNULL(VALUES(interval_0), 0)
            , interval_1 = interval_1 + IFNULL(VALUES(interval_1), 0)
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
        $data = [];

        foreach (range(0, 10000 - 1) as $index) {
            $data[] = [
                'id' => $index,
                'start_date' => '2018-06-15 ' . rand(0, 23) . ':00:00',
                'client' => "CL_a",
                "carrier" => "CR_a",
                "destination" => "D_a",
                "duration" => 20,
                "cost" => 0.23,
            ];
        }

        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        dump($startTime);

        event(new RequestDataMap($data));

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Elapsed: $elapsed" . PHP_EOL;
    }

    protected function testParallelClient()
    {
        $client = $this->rpcClient;

        $client->initClient();
        $client->addRequest("Hello world", 'charcount', 'charcount1');
        $client->addRequest("Hello world 23", 'charcount', 'charcount2');

        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        echo "Waiting for replies…\n";
        $replies = $client->getReplies();


        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo $elapsed;
        var_dump($replies);
    }

    protected function testParallelServer()
    {
        $charCount = function ($word) {
            echo "Doing stuff now";
            sleep(2);

            return strlen($word);
        };

        $server = $this->rpcServer;

        $server->initServer('charcount');

        $server->setCallback($charCount);

        $server->start();
    }

    protected function testFetchData()
    {

    }

}
