<?php

namespace App\Controller;

use App\Entity\Agendamentos;
use App\Entity\Salas;
use App\Repository\SalasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
     * SalasController constructor.
     * @param SalasRepository $salasRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SalasRepository $salasRepository, EntityManagerInterface $entityManager)
    {
        $this->salasRepository = $salasRepository;
        $this->entityManager = $entityManager;
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
     * @Route("/api/salas", methods={"GET"})
     */
    public function buscarTodas(Request $request): Response
    {
        $nome = $request->query->get("nome");
        $disponivel = $request->query->get("disponivel");

        $classeSalas = Salas::class;
        $classeAgendamentos = Agendamentos::class;
        $dataHora = new \DateTime();

        if ($disponivel == "1") {
            $rsm = new ResultSetMappingBuilder($this->entityManager);
            $rsm->addRootEntityFromClassMetadata(Salas::class, 's');
            $query = $this->entityManager->createNativeQuery("SELECT s.* FROM salas AS s WHERE s.nome LIKE :nome AND s.id NOT IN 
                (SELECT a.sala_id FROM agendamentos AS a WHERE 
                    (a.data_inicio <= :d AND a.data_fim >= :d) AND 
                    (a.hora_inicio < :h AND a.hora_fim >= :h))", $rsm);
            $query
                ->setParameter("nome", '%'.$nome.'%')
                ->setParameter("d", $dataHora->format("Y-m-d"))
                ->setParameter("h", $dataHora->format("H:i"));
        } else {
            $classeSalas = Salas::class;
            $dql = "SELECT s FROM $classeSalas s WHERE s.nome LIKE :nome";
            $query = $this->entityManager->createQuery($dql)
                ->setParameter("nome", '%'.$nome.'%');
        }

        /** @var Salas $busca */
        $salasLista = $query->getResult();

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
        $sala->setNome($dadosJson->nome);
        if (array_key_exists('descricao', $dadosJson)) {
            $sala->setDescricao($dadosJson->descricao);
        }

        return $sala;
    }
}
