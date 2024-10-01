<?php
namespace App\WebSocket;

use Illuminate\Support\Facades\Log;
use Ratchet\Client\WebSocket;
use React\EventLoop\Loop;

class SocketClient
{
    private $loop;
    private $connection;
    private $onMessageCallback;
    private $onErrorCallback;
    private $onCloseCallback;

    public function __construct()
    {
        $this->loop = Loop::get();
    }

    public function setOnMessageCallback(callable $onMessageCallback)
    {
        $this->onMessageCallback = $onMessageCallback;
    }

    public function setOnErrorCallback(callable $onErrorCallback)
    {
        $this->onErrorCallback = $onErrorCallback;
    }

    public function setOnCloseCallback(callable $onCloseCallback)
    {
        $this->onCloseCallback = $onCloseCallback;
    }

    public function connect(string $websocketServerUrl, array $headers = [])
    {
        \Ratchet\Client\connect($websocketServerUrl, [], $headers, $this->loop)
            ->then(function (WebSocket $conn) {
                $this->connection = $conn;

                $conn->on('message', function ($message) {
                    if (is_callable($this->onMessageCallback)) {
                        call_user_func($this->onMessageCallback, $message->getPayload());
                    }
                });

                $conn->on('close', function ($code = null, $reason = null) {
                    if (is_callable($this->onCloseCallback)) {
                        call_user_func($this->onCloseCallback, $code, $reason);
                    }
                });

            }, function ($e) {
                Log::info("WebSocket connection failed: $e");
                if (is_callable($this->onErrorCallback)) {
                    call_user_func($this->onErrorCallback, $e);
                }
                $this->close();
            });

        $this->loop->run();
    }

    public function send(string $message)
    {
        if ($this->connection) {
            $this->connection->send($message);
        }
    }

    public function close()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
