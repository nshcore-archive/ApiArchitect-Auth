<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth\Socialite;

use Socialite;
use Tymon\JWTAuth\JWTAuth;
use ApiArchitect\Auth\Entities\User;
use Laravel\Socialite\SocialiteManager;
use ApiArchitect\Auth\Contracts\SocialiteOauthContract;
use ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;

class OauthController extends AuthenticateController implements SocialiteOauthContract
{

    protected $socialiteManager;

    protected $repository;

    /**
     * OauthController constructor.
     * @param SocialiteManager $socialiteManager
     */
    public function __Construct(SocialiteManager $socialiteManager, ResourceRepository $repository)
    {
      $this->socialiteManager = $socialiteManager;
      $this->repository = $repository;
    }

    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
      return $this->socialiteManager->with($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that 
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
      $oauthUser = $this->socialiteManager->with($provider)->stateless()->user();

      $userEntity = new User(
        mt_srand(microtime(true)),
        $oauthUser->getEmail(),
        $oauthUser->getName(),
        $oauthUser->getNickname()
      );

      $providerEntity = app()
        ->make('em')
        ->getRepository('\ApiArchitect\Auth\Entities\Social\Provider')
        ->findOneBy(['name' => $provider]);

      $userEntity->setProvider($providerEntity);
      $userEntity->setProviderId($oauthUser->getId());
      $userEntity->setOTP(1);
      $userEntity->setAvatar($oauthUser->getAvatar());

      $userEntity = $this->repository->findOrCreateUser($userEntity);

    }

}
