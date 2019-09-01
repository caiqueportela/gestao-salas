<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtratorDadosRequest
{
    /**
     * @param Request $request
     * @return array
     */
    private function buscaDadosRequest(Request $request): array
    {
        $queryString = $request->query->all();
        $dadosOrdenacao = array_key_exists('sort', $queryString)
            ? $queryString['sort']
            : null;
        unset($queryString['sort']);

        return [
            'query' => $queryString,
            'ordenacao' => $dadosOrdenacao
        ];
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function buscaDadosOrdenacao(Request $request): ?array
    {
        $informacoesOrdenacao = $this->buscaDadosRequest($request);

        return $informacoesOrdenacao['ordenacao'];
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function buscaDadosFiltro(Request $request): ?array
    {
        $informacoesFiltro =  $this->buscaDadosRequest($request);

        return $informacoesFiltro['query'];
    }
}