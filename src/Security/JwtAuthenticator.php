<?php

namespace App\Security;

use App\Controller\LoginController;
use App\Repository\UsuariosRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class JwtAuthenticator
 * @package App\Security
 */
class JwtAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UsuariosRepository
     */
    private $usuariosRepository;

    /**
     * JwtAuthenticator constructor.
     * @param UsuariosRepository $usuariosRepository
     */
    public function __construct(UsuariosRepository $usuariosRepository)
    {
        $this->usuariosRepository = $usuariosRepository;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse|Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'erro' => 'Cabeçalho de autenticação requerido'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->getPathInfo() !== '/api/login';
    }

    /**
     * @param Request $request
     * @return bool|mixed
     */
    public function getCredentials(Request $request)
    {
        $token = str_replace(
            'Bearer ',
            '',
            $request->headers->get('Authorization')
        );

        try {
            return JWT::decode($token, LoginController::KEY, ['HS256']);
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!is_object($credentials) || !property_exists($credentials, 'username')) {
            return null;
        }

        $username = $credentials->username;
        return $this->usuariosRepository->findOneBy([ 'username' => $username]);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return is_object($credentials) && property_exists($credentials, 'username');
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'erro' => 'Falha na autenticação'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
