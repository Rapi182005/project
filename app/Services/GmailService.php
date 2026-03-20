<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class GmailService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->addScope(Gmail::GMAIL_SEND);
        
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);

            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                }
            }
        }
    }

    public function sendOrderEmail($to, $subject, $body)
    {
        // Clean the email address just in case
        $to = trim($to);

        if ($this->client->isAccessTokenExpired() || empty($to)) {
            return false;
        }

        $gmail = new Gmail($this->client);
        
        // Use a more standard email header format
        $boundary = uniqid(rand(), true);
        $rawMessageString = "MIME-Version: 1.0\r\n";
        $rawMessageString .= "To: <{$to}>\r\n"; // Added brackets for better compatibility
        $rawMessageString .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $rawMessageString .= $body;

        $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $message = new Message();
        $message->setRaw($rawMessage);

        try {
            return $gmail->users_messages->send('me', $message);
        } catch (\Exception $e) {
            \Log::error("Gmail Send Error: " . $e->getMessage());
            return false;
        }
    }
}