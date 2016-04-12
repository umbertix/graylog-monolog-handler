<?php
/**
 * Updated by PhpStorm.
 * User: Alex Klomin
 * Date: 19/02/2016
 * Time: 09:44
 */

namespace GraylogMonolog\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Gelf;
use Psr\Log\LogLevel;

/**
 * Base Handler class providing the Handler structure
 *
 * Classes extending it should (in most cases) only implement write($record)
 */
class GraylogHandler extends AbstractProcessingHandler
{
    protected $transport;
    protected $publisher;

    /**
     * @param bool|int $level  The minimum logging level at which this handler will be triggered
     * @param Boolean  $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        
        // We need a transport - UDP via port 12201 is standard.
        $this->transport = new Gelf\Transport\UdpTransport(config("app.graylog_url"), config("app.graylog_port_udp"), Gelf\Transport\UdpTransport::CHUNK_SIZE_LAN);
        // While the UDP transport is itself a publisher, we wrap it in a real Publisher for convenience
        // A publisher allows for message validation before transmission, and it also supports to send messages
        // to multiple backends at once
        $this->publisher = new Gelf\Publisher();
        $this->publisher->addTransport($this->transport);

        parent::__construct($level, $bubble);
    }

    private function transformArray($array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results[$key] = json_encode($value);
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        $message = $this->generateDataStream($record);
        $this->publisher->publish($message);
    }

    /**
     * Parses the record to generate and appropriate message to be logged
     *
     * @param array $record
     * @return Gelf\Message
     */
    private function generateDataStream(array $record)
    {
        $message = new Gelf\Message();
        $message
            ->setShortMessage($record['message'])
            ->setLevel($record['level_name'])
            ->setFullMessage($record['formatted'])
            ->setTimestamp($record['datetime'])
            ->setFacility($record['channel'])
            ;
        $record['context'] = $this->transformArray($record['context']);
        $record['extra'] = $this->transformArray($record['extra']);

        foreach ($record['context'] as $key => $value) {
            $message->setAdditional('context_'.$key, $value);
        }
        foreach ($record['extra'] as $key => $value) {
            $message->setAdditional('extra_'.$key, $value);
        }
        return $message;
    }

}
