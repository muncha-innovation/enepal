<?php
namespace App\WebSocket;

use App\Models\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class WebsocketHandler implements MessageComponentInterface
{
    private $webSocketClient;
    private $routes;
    private $expiration;
    private $token;
    private $retryCount = 0;
    private $maxRetries = 5;
    private $retryDelay = 2000; 

    public function __construct()
    {
        $this->webSocketClient = new SocketClient;
    }

    public function onOpen(ConnectionInterface $connection)
    {
        $connection->app = new \stdClass();
        $connection->app->id = 'my_app';
        $socketId = sprintf('%d.%d.%d', random_int(1, 1000000000), random_int(1, 1000000000), random_int(1, 1000000000));
        $connection->socketId = $socketId;
        $this->startConnection($connection);
    }

    public function onClose(ConnectionInterface $connection)
    {
        Log::info('Client connection closed.');
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        Log::info("WebSocket error: {$e->getMessage()}");
        $connection->close();
    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $msg)
    {
      
    }
    public function startConnection(ConnectionInterface $connection) {
        $this->expiration = now()->addMinutes(config('app.token_expiration'))->toIso8601String();
        $this->token = $this->getToken();

        if (empty($this->token)) {
            $res = ['error' => true, 'message' => 'Could not get token'];
            $connection->send(json_encode($res));
            $connection->close();
            return;
        }

        $this->connectWebSocket($connection);
    }
    private function connectWebSocket(ConnectionInterface $connection)
    {
        $sessionUrl = config('app.remote_url') . '/api/session';
        $response = Http::get($sessionUrl, ['token' => $this->token]);

        $cookie = $response->cookies()->getCookieByName('JSESSIONID');
        $cookieHeader = $cookie->getName() . '=' . $cookie->getValue();

        $this->webSocketClient->setOnMessageCallback(function ($message) use ($connection) {
            $this->retryCount = 0;
            $this->handleWebSocketMessage($connection, $message);
        });

        $this->webSocketClient->setOnCloseCallback(function () use ($connection) {
            Log::warning('WebSocket closed. Reconnecting...');
            $this->retryConnection($connection);
        });

        $this->webSocketClient->setOnErrorCallback(function ($error) use ($connection) {
            Log::info("WebSocket error: $error");
            $this->retryConnection($connection);
        });

        $this->webSocketClient->connect(config('app.remote_ws_url'), ['Cookie' => $cookieHeader]);
    }

    private function retryConnection(ConnectionInterface $connection)
    {
        if ($this->retryCount < $this->maxRetries) {
            $this->retryCount++;
            Log::info("Retrying WebSocket connection (Attempt: $this->retryCount/$this->maxRetries)...");

            // Exponential backoff: increase delay with each retry
            $delay = $this->retryDelay * (2 ** $this->retryCount);
            usleep($delay * 1000); // Convert delay to microseconds

            $this->token = $this->getToken();
            if (!empty($this->token)) {
                $this->connectWebSocket($connection);
            } else {
                Log::info('Failed to obtain token for reconnection.');
                $connection->close();
            }
        } else {
            Log::info("Max retries reached. WebSocket connection failed.");
            $connection->close();
        }
    }

    private function handleWebSocketMessage(ConnectionInterface $connection, $message)
    {
        try {
            $data = json_decode($message, true);
            $allRoutesData = $this->processRouteData($data);
            $connection->send(json_encode($allRoutesData));
        } catch (\Exception $e) {
            $res = ['error' => true, 'message' => $e->getMessage()];
            $connection->send(json_encode($res));
        }
    }

    private function processRouteData($data)
    {
        $allRoutesData = [];

        return $allRoutesData;
    }

    private function getToken(): string
    {
        $loginUrl = config('app.remote_url') . '/api/session/token';
        $username = config('app.ws_username');
        $password = config('app.ws_password');

        $response = Http::withBasicAuth($username, $password)
            ->asForm()
            ->post($loginUrl, ['expiration' => $this->expiration]);

        return $response->status() == 200 ? $response->body() : '';
    }
}
