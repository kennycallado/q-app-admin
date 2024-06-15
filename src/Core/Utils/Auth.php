<?php declare(strict_types=1);

namespace Core\Utils;

use Exception;

class Auth
{
    private $url;
    public ?string $user_id;
    public ?string $role;
    public ?string $g_auth;
    public ?string $p_auth;
    public ?object $project;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function signin($username, $password)
    {
        $body = (object) [
            'username' => $username,
            'password' => $password
        ];

        $response = $this->requestProcessor('POST', '/login', '', json_encode($body));
        if (isset($response->error)) {
            throw new Exception($response->error->description, $response->error->code);
        }

        if (isset($response->project)) {
            $this->project = $response->project;
        }
        if (isset($response->p_token)) {
            $this->p_auth = $response->p_token;
        }
        if (isset($response->role)) {
            $this->role = $response->role;
        }

        $this->g_auth = $response->g_token;
        $this->user_id = $response->id;

        return $this;
    }

    public function signup($username, $password, $project = null)
    {
        $body = (object) [
            'username' => $username,
            'password' => $password,
            'project' => $project
        ];

        $response = $this->requestProcessor('POST', '/signup', '', json_encode($body));
        if (isset($response->error)) {
            throw new Exception($response->error->description, $response->error->code);
        }

        return $this;
    }

    public function refresh()
    {
        $response = $this->requestProcessor('GET', '/refresh', $this->g_auth, null);

        if (isset($response->error)) {
            return throw new Exception($response->error->description, $response->error->code);
        }

        // reset all values
        $this->project = null;
        $this->p_auth = null;
        $this->user_id = '';
        $this->g_auth = '';
        $this->role = '';

        if (isset($response->project)) {
            $this->project = $response->project;
        }
        if (isset($response->p_token)) {
            $this->p_auth = $response->p_token;
        }
        if (isset($response->role)) {
            $this->role = $response->role;
        }

        $this->g_auth = $response->g_token;
        $this->user_id = $response->id;

        return $this;
    }

    private function requestProcessor(
        string $method,
        string $path,
        ?string $auth,
        ?string $body,
        string $ns = 'global',
        string $db = 'main'
    ) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                'NS: ' . $ns,
                'DB: ' . $db,
                'Accept: application/json',
                'Authorization: ' . $auth,
                'Content-Type: application/json'
            ]
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
}
