-- Avaliação de escrita de SQL

-- Query A: Listar os usuários com mais produtos.
SELECT 
    u.id, 
    u.nome, 
    COUNT(p.id) AS total_produtos
FROM users u
LEFT JOIN products p ON u.id = p.user_id
GROUP BY u.id, u.nome
ORDER BY total_produtos DESC;

-- Query B: Buscar o produto mais caro por usuário.
WITH RankingProdutos AS (
    SELECT 
        user_id, 
        nome AS produto_nome, 
        preco,
        ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY preco DESC) as rank
    FROM products
)
SELECT 
    u.nome AS usuario, 
    rp.produto_nome, 
    rp.preco
FROM users u
JOIN RankingProdutos rp ON u.id = rp.user_id
WHERE rp.rank = 1;

-- Query C: Exibir a quantidade de produtos por faixa de preço.
SELECT 
    CASE 
        WHEN preco < 50.00 THEN '1 - Abaixo de R$ 50,00'
        WHEN preco BETWEEN 50.00 AND 150.00 THEN '2 - Entre R$ 50,00 e R$ 150,00'
        ELSE '3 - Acima de R$ 150,00'
    END AS faixa_preco,
    COUNT(id) AS quantidade_produtos
FROM products
GROUP BY faixa_preco
ORDER BY faixa_preco;