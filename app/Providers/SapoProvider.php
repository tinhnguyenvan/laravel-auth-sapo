<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;

class SapoProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopeSeparator = ',';

    protected $scopes = [
        'read_orders',
        'write_orders',
        'read_customers',
        'write_customers',
        'read_content',
        'write_content',
        'read_themes',
        'write_themes',
        'read_products',
        'write_products',
        'read_script_tags',
        'write_script_tags',
        'read_price_rules',
        'write_price_rules',
        'read_draft_orders',
        'write_draft_orders',
    ];

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        $params = $this->parameters;
        return $this->buildAuthUrlFromBase('https://' . $params['store'] . '.mysapo.net/admin/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        $params = $this->parameters;
        return 'https://' . $params['store'] . '.mysapo.net/admin/oauth/access_token';
    }

    protected function getTokenFields($code)
    {
        return Arr::add(
            parent::getTokenFields($code),
            'grant_type',
            'authorization_code'
        );
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://laravel-test.mysapo.net/admin/store.json', [
            'headers' => [
                'Accept' => 'application/json',
                'X-Sapo-Access-Token' => $token,
            ],
        ]);
        $data = json_decode($response->getBody(), true);

        return $data['store'] ?? [];
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => Arr::get($user, 'id'),
            'name' => Arr::get($user, 'store_owner'),
            'email' => Arr::get($user, 'email'),
            'org_id' => (int)Arr::get($user, 'id'),
            'org_name' => Arr::get($user, 'name'),
        ]);
    }
}
