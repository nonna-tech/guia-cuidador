<?php
// index.php
header('Content-Type: text/html; charset=utf-8');

$arquivo = 'conteudos.json';

function lerConteudos($arquivo) {
    if (!file_exists($arquivo)) {
        return [];
    }

    $json = file_get_contents($arquivo);
    if (trim($json) === '') {
        return [];
    }

    $dados = json_decode($json, true);
    if (!is_array($dados)) {
        return [];
    }

    return $dados;
}

$conteudos = lerConteudos($arquivo);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Guia Jurídico do Cuidador de Idosos</title>
    <style>
        :root {
            --cor-primaria: #00a8e8;
            --cor-secundaria: #ff7a59;
            --cor-destaque: #00c49a;
            --cor-fundo: #f0f4ff;
            --cor-card: #ffffff;
            --cor-texto: #333333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            max-width: 960px;
            margin: 0 auto;
            background: linear-gradient(135deg, #f0f4ff, #ffe8f0);
            color: var(--cor-texto);
        }

        header {
            background: linear-gradient(90deg, var(--cor-primaria), var(--cor-secundaria));
            color: #ffffff;
            padding: 24px 20px 16px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        header h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        header p {
            margin: 0;
            font-size: 14px;
            max-width: 600px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        .tag-tema {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.6);
        }

        a.btn-admin {
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 20px;
            background-color: #ffffff;
            color: var(--cor-primaria);
            font-size: 13px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.18);
        }

        main {
            padding: 20px;
        }

        .intro {
            margin-bottom: 18px;
            font-size: 14px;
        }

        .grid-conteudos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .conteudo {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            border-top: 5px solid var(--cor-destaque);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .conteudo h2 {
            margin: 0 0 8px;
            font-size: 18px;
            color: #1b3a57;
        }

        .data {
            font-size: 11px;
            color: #777777;
            margin-bottom: 8px;
        }

        .descricao {
            font-size: 14px;
            line-height: 1.4;
        }

        .vazio {
            text-align: center;
            margin-top: 40px;
            color: #555;
            background-color: rgba(255,255,255,0.8);
            padding: 16px;
            border-radius: 12px;
        }

        footer {
            text-align: center;
            font-size: 11px;
            padding: 12px;
            color: #666;
        }

        @media (max-width: 600px) {
            header {
                border-radius: 0 0 16px 16px;
            }
            header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Guia Jurídico do Cuidador de Idosos</h1>
    <p>Orientações práticas para quem cuida de idosos: direitos, deveres e dicas para se proteger juridicamente no dia a dia.</p>
    <div class="top-bar">
        <span class="tag-tema">Tema: cuidados com idosos • foco no cuidador</span>
        <a class="btn-admin" href="admin.php">Área administrativa</a>
    </div>
</header>

<main>
    <p class="intro">
        Aqui você encontra conteúdos simples, escritos em linguagem de cuidador, para entender melhor seus direitos,
        organizar provas e evitar problemas trabalhistas no futuro.
    </p>

    <?php if (empty($conteudos)): ?>
        <p class="vazio">Nenhuma orientação cadastrada ainda. Acesse a área administrativa para incluir os primeiros conteúdos.</p>
    <?php else: ?>
        <div class="grid-conteudos">
            <?php foreach ($conteudos as $c): ?>
                <article class="conteudo">
                    <div>
                        <h2><?php echo htmlspecialchars($c['titulo']); ?></h2>
                        <?php if (!empty($c['data'])): ?>
                            <div class="data">
                                Publicado em: <?php echo htmlspecialchars($c['data']); ?>
                            </div>
                        <?php endif; ?>
                        <p class="descricao"><?php echo nl2br(htmlspecialchars($c['descricao'])); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    Sistema exemplo em PHP + JSON • Uso didático • Pode ser adaptado para outros temas.
</footer>

</body>
</html>
