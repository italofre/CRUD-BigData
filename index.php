<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Aws\DynamoDb\DynamoDbClient;

$client = new DynamoDbClient([
    'region' => $_ENV['AWS_REGION'],
    'version' => 'latest',
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
    ],
]);
$mensagem = '';

// INSERIR MEDICAMENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inserir'])) {
    $nome = $_POST['nome'];
    $fabricante = $_POST['fabricante'];
    $lote = $_POST['lote'];
    $validade = $_POST['validade'];
    $quantidade = $_POST['quantidade'];

    try {
        $client->putItem([
            'TableName' => 'Medicamentos',
            'Item' => [
                'id' => ['S' => uniqid()],
                'nome' => ['S' => $nome],
                'fabricante' => ['S' => $fabricante],
                'lote' => ['S' => $lote],
                'validade' => ['S' => $validade],
                'quantidade' => ['N' => (string)$quantidade]
            ],
        ]);
        $mensagem = "Medicamento cadastrado com sucesso!";
    } catch (DynamoDbException $e) {
        $mensagem = "Erro ao cadastrar medicamento: " . $e->getMessage();
    }
}

// ATUALIZAR MEDICAMENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $fabricante = $_POST['fabricante'];
    $lote = $_POST['lote'];
    $validade = $_POST['validade'];
    $quantidade = $_POST['quantidade'];

    try {
        $client->updateItem([
            'TableName' => 'Medicamentos',
            'Key' => [
                'id' => ['S' => $id],
            ],
            'UpdateExpression' => 'SET nome = :nome, fabricante = :fabricante, lote = :lote, validade = :validade, quantidade = :quantidade',
            'ExpressionAttributeValues' => [
                ':nome' => ['S' => $nome],
                ':fabricante' => ['S' => $fabricante],
                ':lote' => ['S' => $lote],
                ':validade' => ['S' => $validade],
                ':quantidade' => ['N' => (string)$quantidade],
            ],
        ]);
        $mensagem = "Medicamento atualizado com sucesso!";
    } catch (DynamoDbException $e) {
        $mensagem = "Erro ao atualizar medicamento: " . $e->getMessage();
    }
}

// EXCLUIR MEDICAMENTO
if (isset($_GET['deletar'])) {
    $idMedicamento = $_GET['deletar'];

    try {
        $client->deleteItem([
            'TableName' => 'Medicamentos',
            'Key' => [
                'id' => ['S' => $idMedicamento],
            ],
        ]);
        $mensagem = "Medicamento excluído com sucesso!";
    } catch (DynamoDbException $e) {
        $mensagem = "Erro ao excluir medicamento: " . $e->getMessage();
    }
}

// LISTAR MEDICAMENTOS
$result = $client->scan([
    'TableName' => 'Medicamentos',
]);

$medicamentos = $result['Items'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Controle de Medicamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Controle de Medicamentos</h1>

        <?php if (!empty($mensagem)) : ?>
            <div class="alert alert-info"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <!-- Formulário de Cadastro/Edição -->
        <h3 id="formTitle">Cadastrar Medicamento</h3>
        <form method="POST" id="medicamentoForm">
            <input type="hidden" name="id" id="medicamentoId">
            <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome" id="nome" required></div>
            <div class="mb-3"><label class="form-label">Fabricante</label><input type="text" class="form-control" name="fabricante" id="fabricante" required></div>
            <div class="mb-3"><label class="form-label">Lote</label><input type="text" class="form-control" name="lote" id="lote" required></div>
            <div class="mb-3"><label class="form-label">Validade</label><input type="date" class="form-control" name="validade" id="validade" required></div>
            <div class="mb-3"><label class="form-label">Quantidade</label><input type="number" class="form-control" name="quantidade" id="quantidade" required></div>
            
            <!-- Botões separados -->
            <button type="submit" class="btn btn-primary" name="inserir" id="btnCadastrar">Cadastrar</button>
            <button type="submit" class="btn btn-success d-none" name="editar" id="btnEditar">Salvar Alterações</button>
            <button type="button" class="btn btn-secondary d-none" id="btnCancelar" onclick="cancelarEdicao()">Cancelar</button>
        </form>

        <h3 class="mt-4">Lista de Medicamentos</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Fabricante</th>
                    <th>Lote</th>
                    <th>Validade</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicamentos as $item) : ?>
                    <tr>
                        <td><?php echo $item['id']['S']; ?></td>
                        <td><?php echo $item['nome']['S']; ?></td>
                        <td><?php echo $item['fabricante']['S']; ?></td>
                        <td><?php echo $item['lote']['S']; ?></td>
                        <td><?php echo $item['validade']['S']; ?></td>
                        <td><?php echo $item['quantidade']['N']; ?></td>
                        <td>
                            <a href="?deletar=<?php echo $item['id']['S']; ?>" class="btn btn-danger btn-sm">Excluir</a>
                            <button class="btn btn-warning btn-sm" onclick="preencherFormularioEditar(
                                '<?php echo $item['id']['S']; ?>',
                                '<?php echo $item['nome']['S']; ?>',
                                '<?php echo $item['fabricante']['S']; ?>',
                                '<?php echo $item['lote']['S']; ?>',
                                '<?php echo $item['validade']['S']; ?>',
                                '<?php echo $item['quantidade']['N']; ?>'
                            )">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<script>
    function preencherFormularioEditar(id, nome, fabricante, lote, validade, quantidade) {
        document.getElementById('medicamentoId').value = id;
        document.getElementById('nome').value = nome;
        document.getElementById('fabricante').value = fabricante;
        document.getElementById('lote').value = lote;
        document.getElementById('validade').value = validade;
        document.getElementById('quantidade').value = quantidade;
        document.getElementById('btnCadastrar').classList.add('d-none');
        document.getElementById('btnEditar').classList.remove('d-none');
        document.getElementById('btnCancelar').classList.remove('d-none');
    }
    function cancelarEdicao() { location.reload(); }
</script>
</body>
</html>
