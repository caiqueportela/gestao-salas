<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Repository\UsuariosRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    public const KEY = "UdGTl87vDwn6ZU9dNgjfF7a40IwqbZzAnwEc1gFfLquK5Ec5216DNY-4LMGZifJGpTif3a3w6m5t9BTyu9vxOAh_4HI94lb2Gid83Wn2aXKp1NAc57pJgmzJwZmNIuH4GYPZPRzjN7YGokTGGH_FHRT-TKU32R2z42eCpphRy1xcLhjPac1L2Vk43qvn1bSQ7EyzqKLBr9D-UvKv9cqMVe-XBGz_0TNuSGTT_wZwfEbYHwDc3D6Ca5W4Tt_0fmJz86mDE_O_jAqmqyGI56FGdkiwmuRgyxF4d_iHDJ7q4X819eK5m8umVZAfitdpZlR3RtxYM3jpLEf_5qwE4ElF-A";

    /**
     * @var UsuariosRepository
     */
    private $usuariosRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * LoginController constructor.
     * @param UsuariosRepository $usuariosRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UsuariosRepository $usuariosRepository, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->usuariosRepository = $usuariosRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @Route("/api/login", name="login", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request)
    {
        $dadosEmJson = json_decode($request->getContent());
        if (is_null($dadosEmJson->usuario) || is_null($dadosEmJson->senha)) {
            return new JsonResponse([
                'erro' => 'Usuário e/ou senha não informados!'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var Usuarios $usuario */
        $usuario = $this->usuariosRepository->findOneBy([
            'username' => $dadosEmJson->usuario
        ]);

        if (is_null($usuario)) {
            return new JsonResponse([
                'erro' => 'Usuário inválido!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!($this->userPasswordEncoder->isPasswordValid($usuario, $dadosEmJson->senha))) {
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode([
                'username' => $usuario->getUsername()
            ],
            self::KEY
        );

        return new JsonResponse([
            'access_token' => $token
        ]);
    }
}
