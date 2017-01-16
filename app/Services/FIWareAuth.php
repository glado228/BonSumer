<?php namespace Bonsum\Services;

use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use GuzzleHttp\ClientInterface;

class FIWareAuth extends AbstractProvider {


	 /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state) {

        return $this->buildAuthUrlFromBase('https://account.lab.fiware.org/oauth2/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl() {

    	return 'https://account.lab.fiware.org/oauth2/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    protected function getUserByToken($token) {

        $response = $this->getHttpClient()->get(
        	'https://account.lab.fiware.org/user?'
        	. http_build_query([
        		'access_token' => $token
        	])
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Laravel\Socialite\User
     */
    protected function mapUserToObject(array $user) {

    	return (new User)->setRaw($user)->map([
    		'id' => $user['id'],
    		'nickname' => array_get($user, 'nickName'),
    		'name' => $user['displayName'],
            'email' => $user['email']
    	]);
    }

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
            ],
            $postKey => $this->getTokenFields($code),
        ]);

        return $this->parseAccessToken($response->getBody());
    }

	 /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return [
        	'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUrl
        ];
    }

}

