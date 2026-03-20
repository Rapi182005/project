<?php

namespace App\Http\Controllers;

use Google\Client;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
{
    $client = new Client();
    $client->setAuthConfig(storage_path('app/google/credentials.json'));
    $client->addScope(\Google\Service\Gmail::GMAIL_SEND);
    
    // THESE TWO LINES ARE CRITICAL
    $client->setAccessType('offline'); 
    $client->setApprovalPrompt('force'); 

    return redirect()->away($client->createAuthUrl());
}

    public function handleGoogleCallback(Request $request)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setRedirectUri('http://localhost:8000/google/callback');

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
            
            if (isset($token['error'])) {
                return redirect()->route('admin.dashboard')->with('error', 'Gmail Error: ' . $token['error_description']);
            }

            $tokenPath = storage_path('app/google/token.json');
            
            if (!file_exists(storage_path('app/google'))) {
                mkdir(storage_path('app/google'), 0755, true);
            }

            file_put_contents($tokenPath, json_encode($token));

            return redirect()->route('admin.dashboard')->with('success', 'Gmail connected successfully!');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Gmail connection failed.');
    }
}