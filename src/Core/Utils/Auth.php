<?php declare(strict_types=1);

namespace Core\Utils;

use App\Models\UserNew;

class Auth
{
    private string $url;
    public ?string $username;
    public ?string $user_id;
    public ?string $role;
    public ?string $g_auth;
    public ?string $p_auth;
    public ?object $project;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function signin(UserNew $new_user): Auth
    {
        $response = $this->requestProcessor('POST', '/login', '', json_encode($new_user));
        if (isset($response->error)) {
            throw new \Exception($response->error->description, $response->error->code);
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

        $this->user_id = $response->id;
        $this->g_auth = $response->g_token;
        $this->username = $new_user->username;

        return $this;
    }

    public function signup(UserNew $new_user): Auth
    {
        $response = $this->requestProcessor('POST', '/signup', '', json_encode($new_user));
        if (isset($response->error)) {
            throw new \Exception($response->error->description, $response->error->code);
        }

        return $this;
    }

    public function refresh(): Auth
    {
        $response = $this->requestProcessor('GET', '/refresh', $this->g_auth, null);

        if (isset($response->error)) {
            return throw new \Exception($response->error->description, $response->error->code);
        }

        // reset all values
        $this->project = null;
        $this->p_auth = null;
        $this->username = '';
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

        $this->user_id = $response->id;
        $this->g_auth = $response->g_token;
        $this->username = $response->username;

        return $this;
    }

    private function requestProcessor(string $method, string $path, ?string $auth, ?string $body, string $ns = 'global', string $db = 'main'): mixed
    {
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

    public function set_cookies(): bool
    {
        setcookie('username', $this->username, time() + 3600, '/');
        setcookie('project', json_encode($this->project), time() + 3600, '/');
        setcookie('user_id', $this->user_id, time() + 3600, '/');
        setcookie('g_auth', $this->g_auth, time() + 3600, '/');
        setcookie('p_auth', $this->p_auth, time() + 3600, '/');
        setcookie('role', $this->role, time() + 3600, '/');

        return true;
    }

    public function unset_cookies(): bool
    {
        setcookie('username', '', time() - 3600, '/');
        setcookie('project', '', time() - 3600, '/');
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('g_auth', '', time() - 3600, '/');
        setcookie('p_auth', '', time() - 3600, '/');
        setcookie('role', '', time() - 3600, '/');

        return true;
    }
}
