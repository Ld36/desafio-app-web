<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function relatorioSql()
    {
        // A função json_agg do PostgreSQL agrupa os produtos em um array JSON direto no banco!
        $relatorio = DB::select("
            SELECT 
                u.id AS usuario_id,
                u.nome AS usuario_nome,
                COUNT(p.id) AS total_produtos,
                COALESCE(ROUND(AVG(p.preco), 2), 0) AS media_preco,
                COALESCE(
                    json_agg(
                        json_build_object('id', p.id, 'nome', p.nome, 'preco', p.preco)
                    ) FILTER (WHERE p.id IS NOT NULL), '[]'
                ) AS lista_produtos
            FROM users u
            LEFT JOIN products p ON u.id = p.user_id
            GROUP BY u.id, u.nome
            ORDER BY total_produtos DESC
        ");

        // O DB::select retorna um array de objetos. Como a lista_produtos volta
        // do banco como uma string JSON, fazemos o decode para o Laravel cuspir um JSON perfeito na API.
        $relatorioFormatado = array_map(function ($item) {
            $item->lista_produtos = json_decode($item->lista_produtos);
            return $item;
        }, $relatorio);

        return response()->json($relatorioFormatado, 200);
    }
}