<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conf = new \RdKafka\Conf();

        $conf->set('bootstrap.servers', env('KAFKA_BOOTSTRAP'));
        $conf->set('security.protocol', 'SASL_SSL');
        $conf->set('sasl.mechanism', 'PLAIN');
        $conf->set('sasl.username', env('KAFKA_USERNAME'));
        $conf->set('sasl.password', env('KAFKA_PASSWORD'));
        $conf->set('group.id', 'myGroup2');
        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new \Rdkafka\KafkaConsumer($conf);
        $consumer->subscribe(['default']);


        while (true) {
            $message = $consumer->consume(120 * 1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message->payload);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
}
