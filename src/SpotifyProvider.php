<?php

namespace Socialite\Provider;

use Socialite\Two\AbstractProvider;
use Socialite\Two\User;
use Socialite\Util\A;

class SpotifyProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(string $state)
    {
        return $this->buildAuthUrlFromBase(
            'https://accounts.spotify.com/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://accounts.spotify.com/api/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(string $token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.spotify.com/v1/me', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => null,
            'name' => $user['display_name'],
            'email' => $user['email'] ?? null,
            'avatar' => A::get($user, 'images.0.url'),
            'profileUrl' => $user['href'] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields(string $code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
