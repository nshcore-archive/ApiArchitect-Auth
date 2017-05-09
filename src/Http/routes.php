<?php

$this->app->post('/auth/logout', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@logout');
$this->app->post('/auth/login', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@authenticate');

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'apiarchitect.auth']], function ($app){
    $this->app->get('/auth/user','ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@user');
});

$this->app->post('/auth/password/reset', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@reset');
$this->app->get('/auth/password/reset/{token}', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@verify');

$this->app->get('/auth/refresh', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@refresh');

$this->app->get('auth/oauth/facebook/redirect', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@redirectToProvider');
$this->app->get('auth/oauth/facebook/callback', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@handleProviderCallback');

$this->app->post('auth/register', 'ApiArchitect\Auth\Http\Controllers\User\UserController@register');

$this->app->group(['middleware' => 'jwt.auth'], function ($app){
    resource('user','ApiArchitect\Compass\Http\Controllers\User\UserController');
});

$this->app->post('auth/check/username', 'ApiArchitect\Compass\Auth\Controllers\User\UserController@checkUniqueUserName');
$this->app->post('auth/check/email', 'ApiArchitect\Compass\Auth\Controllers\User\UserController@checkUniqueEmail');