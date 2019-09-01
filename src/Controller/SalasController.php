<?php

namespace App\Controller;

use App\Entity\Salas;
use App\Helper\ExtratorDadosRequest;
use App\Repository\SalasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalasController extends AbstractController
{
    /**
     * @var SalasRepository
     */
    private $salasRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ExtratorDadosRequest
     */
    private $extratorDadosRequest;

    /**
     * SalasController constructor.
     * @param SalasRepository $salasRepository
     * @param EntityManagerInterface $entityManager
     * @param ExtratorDadosRequest $extratorDadosRequest
     */
    public function __construct(SalasRepository $salasRepository, EntityManagerInterface $entityManager, ExtratorDadosRequest $extratorDadosRequest)
    {
        $this->salasRepository = $salasRepository;
        $this->entityManager = $entityManager;
        $this->extratorDadosRequest = $extratorDadosRequest;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/salas", methods={"POST"})
     */
    public function criar(Request $request): Response
    {
        $dadosEnviados = $request->getContent();
        $sala = $this->criarEntidade($dadosEnviados);

        $this->entityManager->persist($sala);
        $this->entityManager->flush();

        return new JsonResponse($sala, Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/api/salas/{id}", methods={"GET"})
     */
    public function buscarPeloId(int $id): Response
    {
        $sala = $this->salasRepository->find($id);
        $status = is_null($sala)
            ? Response::HTTP_NO_CONTENT
            : Response::HTTP_OK;

        return new JsonResponse($sala, $status);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/salas", methods={"GEt"})
     */
    public function buscarTodas(Request $request): Response
    {
        $ordenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);
        $filtro = $this->extratorDadosRequest->buscaDadosFiltro($request);
        $salasLista = $this->salasRepository->findBy($filtro, $ordenacao);

        $status = is_null($salasLista)
            ? Response::HTTP_NO_CONTENT
            : Response::HTTP_OK;

        return new JsonResponse($salasLista, $status);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/api/salas/{id}", methods={"DELETE"})
     */
    public function remover(int $id): Response
    {
        $sala = $this->salasRepository->find($id);

        $this->entityManager->remove($sala);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @Route("/api/salas/{id}", methods={"PUT"})
     */
    public function atualizar(int $id, Request $request): Response
    {
        $dadosEnviados = $request->getContent();
        $salaEnviada = $this->criarEntidade($dadosEnviados);

        try {
            /** @var Salas $salaExistente */
            $salaExistente = $this->salasRepository->find($id);
            if (is_null($salaExistente)) {
                throw new \InvalidArgumentException();
            }

            $salaExistente
                ->setNome($salaEnviada->getNome())
                ->setDescricao($salaEnviada->getDescricao());

            $this->entityManager->flush();

            return new JsonResponse($salaExistente, Response::HTTP_OK);
        } catch (\Exception $ex) {
            return new JsonResponse([
                'erro' => 'Sala não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param string $json
     * @return Salas
     */
    private function criarEntidade(string $json): Salas
    {
        $dadosJson = json_decode($json);
        $sala = new Salas();
        $sala
            ->setNome($dadosJson->nome)
            ->setDescricao($dadosJson->descricao);

        return $sala;
    }
}
