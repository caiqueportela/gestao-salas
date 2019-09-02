<?php

namespace App\Controller;

use App\Entity\Agendamentos;
use App\Entity\Salas;
use App\Repository\AgendamentosRepository;
use App\Repository\SalasRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgendamentosController extends AbstractController
{
    /**
     * @var AgendamentosRepository
     */
    private $agendamentosRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var SalasRepository
     */
    private $salasRepository;

    /**
     * AgendamentosController constructor.
     * @param AgendamentosRepository $agendamentosRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AgendamentosRepository $agendamentosRepository,
        SalasRepository $salasRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->agendamentosRepository = $agendamentosRepository;
        $this->entityManager = $entityManager;
        $this->salasRepository = $salasRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/agendamentos", methods={"POST"})
     */
    public function criar(Request $request): Response
    {
        $dadosEnviados = $request->getContent();
        $agendamento = $this->criarEntidade($dadosEnviados);

        if (!$this->validarDisponibilidade($agendamento)) {
            return new JsonResponse([
                'erro' => 'Sala já reservada para o período solicitado'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($agendamento);
        $this->entityManager->flush();

        return new JsonResponse($agendamento, Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/api/agendamentos/{id}", methods={"GET"})
     */
    public function buscarPeloId(int $id): Response
    {
        $agendamento = $this->agendamentosRepository->find($id);
        $status = is_null($agendamento)
            ? Response::HTTP_NO_CONTENT
            : Response::HTTP_OK;

        return new JsonResponse($agendamento, $status);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/agendamentos", methods={"GEt"})
     */
    public function buscarTodas(Request $request): Response
    {
        $agendamentosLista = $this->agendamentosRepository->findAll();

        $status = is_null($agendamentosLista)
            ? Response::HTTP_NO_CONTENT
            : Response::HTTP_OK;

        return new JsonResponse($agendamentosLista, $status);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/api/agendamentos/{id}", methods={"DELETE"})
     */
    public function remover(int $id): Response
    {
        $agendamento = $this->agendamentosRepository->find($id);

        $this->entityManager->remove($agendamento);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @Route("/api/agendamentos/{id}", methods={"PUT"})
     */
    public function atualizar(int $id, Request $request): Response
    {
        $dadosEnviados = $request->getContent();
        $agendamentoEnviado = $this->criarEntidade($dadosEnviados);

        try {
            /** @var Agendamentos $agendamentoExistente */
            $agendamentoExistente = $this->agendamentosRepository->find($id);
            if (is_null($agendamentoExistente)) {
                throw new \InvalidArgumentException();
            }

            if (!$this->validarDisponibilidade($agendamentoEnviado, $id)) {
                return new JsonResponse([
                    'erro' => 'Sala já reservada para o período solicitado'
                ], Response::HTTP_BAD_REQUEST);
            }

            $agendamentoExistente
                ->setSala($agendamentoEnviado->getSala())
                ->setObservacao($agendamentoEnviado->getObservacao())
                ->setDataInicio($agendamentoEnviado->getDataInicio())
                ->setDataFim($agendamentoEnviado->getDataFim())
                ->setHoraInicio($agendamentoEnviado->getHoraInicio())
                ->setHoraFim($agendamentoEnviado->getHoraFim());

            $this->entityManager->flush();

            return new JsonResponse($agendamentoExistente, Response::HTTP_OK);
        } catch (\Exception $ex) {
            return new JsonResponse([
                'erro' => 'Agendamento não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param int $salaId
     * @return Response
     * @Route("/api/salas/{salaId}/agendamentos", methods={"GET"})
     */
    public function buscaPorSala(int $salaId): Response
    {
        $agendamentos = $this->agendamentosRepository->findBy([
            'sala' => $salaId
        ]);

        return new JsonResponse($agendamentos, Response::HTTP_OK);
    }

    /**
     * @param string $json
     * @return Agendamentos
     */
    private function criarEntidade(string $json): Agendamentos
    {
        $dadosJson = json_decode($json);
        $salaId = $dadosJson->salaId;
        $sala = $this->salasRepository->find($salaId);

        $dataInicio = DateTime::createFromFormat(Agendamentos::$DATA_FORMATO, $dadosJson->dataInicio);
        $dataFim = DateTime::createFromFormat(Agendamentos::$DATA_FORMATO, $dadosJson->dataFim);
        $horaInicio = DateTime::createFromFormat(Agendamentos::$HORA_FORMATO, $dadosJson->horaInicio);
        $horaFim = DateTime::createFromFormat(Agendamentos::$HORA_FORMATO, $dadosJson->horaFim);

        $agendamento = new Agendamentos();
        $agendamento
            ->setSala($sala)
            ->setDataInicio($dataInicio)
            ->setDataFim($dataFim)
            ->setHoraInicio($horaInicio)
            ->setHoraFim($horaFim);

        if (array_key_exists('observacao', $dadosJson)) {
            $agendamento->setObservacao($dadosJson->observacao);
        }

        return $agendamento;
    }

    /**
     * @param Agendamentos $agendamento
     * @param int|null $id
     * @return bool
     */
    private function validarDisponibilidade(Agendamentos $agendamento, int $id = null): bool
    {
        $classeSalas = Salas::class;
        $classeAgendamentos = Agendamentos::class;
        $dql = "SELECT a FROM $classeAgendamentos a JOIN $classeSalas s WHERE a.sala = s AND s.id = :salaId AND 
            (a.dataInicio BETWEEN :dataInicial AND :dataFinal OR a.dataFim BETWEEN :dataInicial AND :dataFinal) AND 
            (a.horaInicio BETWEEN :horaInicial AND :horaFinal OR a.horaFim BETWEEN :horaInicial AND :horaFinal)";
        $query = $this->entityManager->createQuery($dql)
            ->setParameter("salaId", $agendamento->getSala()->getId())
            ->setParameter("dataInicial", $agendamento->getDataInicio()->format('Y-m-d'))
            ->setParameter("dataFinal", $agendamento->getDataFim()->format('Y-m-d'))
            ->setParameter('horaInicial', $agendamento->getHoraInicio()->format('H:i'))
            ->setParameter('horaFinal', $agendamento->getHoraFim()->format('H:i'));

        /** @var Agendamentos $agendamentosLista */
        $agendamentosLista = $query->getResult();

        if (is_null($agendamentosLista)) {
            return true;
        }

        $count  = 0;
        foreach ($agendamentosLista as $agend) {
             if ($agendamento->getHoraInicio() > $agend->getHoraFim() || $agendamento->getHoraFim() < $agend->getHoraInicio()) {
                 if (!is_null($id)) {
                     if ($agend->getId() != $id && $agend->getSala()->getId() == $agendamento->getSala()->getId()) {
                         $count++;
                     }
                 } else {
                     $count++;
                 }
            }
        }

        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }
}
