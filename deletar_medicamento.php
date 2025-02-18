<?php
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

$client = new DynamoDbClient([
    'region' => $_ENV['AWS_REGION'],
    'version' => 'latest',
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
    ],
]);
$idMedicamento = 'ID_DO_MEDICAMENTO'; // Substitua pelo ID do medicamento que deseja excluir

try {
    $client->deleteItem([
        'TableName' => 'Medicamentos',
        'Key' => [
            'id' => ['S' => $idMedicamento],
        ],
    ]);

    echo "Medicamento deletado com sucesso!\n";
} catch (DynamoDbException $e) {
    echo "Erro ao deletar medicamento: " . $e->getMessage() . "\n";
}
