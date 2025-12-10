<?php
// admin.php
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

function salvarConteudos($arquivo, $conteudos) {
    file_put_contents(
        $arquivo,
        json_encode($conteudos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

$conteudos = lerConteudos($arquivo);
$mensagem = '';
$conteudoEditar = null;

// Remoção via GET (?remover=ID)
if (isset($_GET['remover'])) {
    $idRemover = (int) $_GET['remover'];
    $novoArray = [];

    foreach ($conteudos as $c) {
        if ($c['id'] != $idRemover) {
            $novoArray[] = $c;
        }
    }

    $conteudos = $novoArray;
    salvarConteudos($arquivo, $conteudos);
    header('Location: admin.php?msg=removido');
    exit;
}

// Processamento do formulário (salvar novo/edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id   = $_POST['id'] ?? '';
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($acao === 'salvar') {
        if ($titulo === '' || $descricao === '') {
            $mensagem = 'Título e descrição são obrigatórios.';
        } else {
            if ($id === '') {
                // Novo conteúdo
                $novoId = 1;
                foreach ($conteudos as $c) {
                    if ($c['id'] >= $novoId) {
                        $novoId = $c['id'] + 1;
                    }
                }

                $conteudos[] = [
                    'id' => $novoId,
                    'titulo' => $titulo,
                    'descricao' => $descricao,
                    'data' => date('Y-m-d H:i:s')
                ];
            } else {
                // Edição
                foreach ($conteudos as &$c) {
                    if ($c['id'] == $id) {
                        $c['titulo'] = $titulo;
                        $c['descricao'] = $descricao;
                        // Se quiser atualizar a data a cada edição, descomente:
                        // $c['data'] = date('Y-m-d H:i:s');
                        break;
                    }
                }
                unset($c);
            }

            salvarConteudos($arquivo, $conteudos);
            header('Location: admin.php?msg=salvo');
            exit;
        }
    }
}

// Preparar dados para edição (?editar=ID)
if (isset($_GET['editar'])) {
    $idEditar = (int) $_GET['editar'];
    foreach ($conteudos as $c) {
        if ($c['id'] == $idEditar) {
            $conteudoEditar = $c;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Guia Jurídico do Cuidador</title>
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
            max-width: 1000px;
            margin: 0 auto;
            background: linear-gradient(135deg, #f0f4ff, #ffe8f0);
            color: var(--cor-texto);
        }

        header {
            background: linear-gradient(90deg, var(--cor-primaria), var(--cor-secundaria));
            color: #ffffff;
            padding: 20px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        header h1 {
            margin: 0;
            font-size: 22px;
        }

        header p {
            margin: 4px 0 0;
            font-size: 13px;
        }

        .top-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        a.btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 20px;
            background-color: #ffffff;
            color: var(--cor-primaria);
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.18);
        }

        a.btn-voltar {
            color: #555;
        }

        main {
            padding: 20px;
        }

        .mensagem {
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            background-color: #d4edda;
            color: #155724;
            font-size: 13px;
        }

        .mensagem.erro {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: rgba(255,255,255,0.9);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 13px;
            text-align: left;
        }

        th {
            background-color: rgba(0,0,0,0.05);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .acoes a {
            margin-right: 6px;
            text-decoration: none;
            font-size: 12px;
        }

        .acoes a.editar {
            color: #007bff;
        }

        .acoes a.remover {
            color: #dc3545;
        }

        .info-vazio {
            text-align: center;
            padding: 10px;
            background-color: #fff3cd;
            color: #856404;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        form {
            background-color: rgba(255,255,255,0.95);
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }

        form h2 {
            margin-top: 0;
            font-size: 18px;
            color: #1b3a57;
        }

        label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 13px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 13px;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button[type="submit"] {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(90deg, var(--cor-destaque), var(--cor-secundaria));
            color: #ffffff;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }

        footer {
            text-align: center;
            font-size: 11px;
            padding: 10px;
            color: #666;
        }

        @media (max-width: 600px) {
            header {
                border-radius: 0 0 16px 16px;
            }
            table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Administração – Guia Jurídico do Cuidador</h1>
    <p>Gerencie os textos de orientação jurídica que aparecem na página pública.</p>
    <div class="top-links">
        <a class="btn btn-voltar" href="index.php">← Ver página pública</a>
        <a class="btn" href="admin.php">Atualizar</a>
    </div>
</header>

<main>

<?php
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'salvo') {
        echo '<div class="mensagem">Conteúdo salvo com sucesso!</div>';
    } elseif ($_GET['msg'] === 'removido') {
        echo '<div class="mensagem">Conteúdo removido com sucesso!</div>';
    }
}
if ($mensagem !== '') {
    echo '<div class="mensagem erro">'.htmlspecialchars($mensagem).'</div>';
}
?>

<h2>Conteúdos cadastrados</h2>

<?php if (empty($conteudos)): ?>
    <div class="info-vazio">Nenhuma orientação cadastrada até agora. Use o formulário abaixo para criar a primeira.</div>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Data</th>
            <th>Resumo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($conteudos as $c): ?>
            <tr>
                <td><?php echo (int)$c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['titulo']); ?></td>
                <td><?php echo htmlspecialchars($c['data']); ?></td>
                <td>
                    <?php
                        $texto = $c['descricao'] ?? '';
                        if (mb_strlen($texto) > 70) {
                            $texto = mb_substr($texto, 0, 70) . '...';
                        }
                        echo htmlspecialchars($texto);
                    ?>
                </td>
                <td class="acoes">
                    <a class="editar" href="admin.php?editar=<?php echo (int)$c['id']; ?>">Editar</a>
                    <a class="remover"
                       href="admin.php?remover=<?php echo (int)$c['id']; ?>"
                       onclick="return confirm('Tem certeza que deseja remover esta orientação?');">
                        Remover
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<form method="post" action="admin.php">
    <h2><?php echo $conteudoEditar ? 'Editar orientação' : 'Nova orientação jurídica para cuidador'; ?></h2>

    <input type="hidden" name="acao" value="salvar">
    <input type="hidden" name="id" value="<?php echo $conteudoEditar['id'] ?? ''; ?>">

    <label for="titulo">Título da orientação</label>
    <input
        type="text"
        id="titulo"
        name="titulo"
        required
        placeholder="Ex.: Direitos do cuidador em regime de plantão"
        value="<?php echo htmlspecialchars($conteudoEditar['titulo'] ?? ''); ?>"
    >

    <label for="descricao">Texto / explicação (em linguagem simples para o cuidador)</label>
    <textarea
        id="descricao"
        name="descricao"
        required
        placeholder="Explique aqui de forma prática, como se estivesse orientando um cuidador de idosos."
    ><?php echo htmlspecialchars($conteudoEditar['descricao'] ?? ''); ?></textarea>

    <button type="submit">
        <?php echo $conteudoEditar ? 'Atualizar orientação' : 'Cadastrar orientação'; ?>
    </button>
</form>

</main>

<footer>
    Sistema exemplo em PHP + JSON • Focado em cuidados com idosos e orientação ao cuidador.
</footer>

</body>
</html>
