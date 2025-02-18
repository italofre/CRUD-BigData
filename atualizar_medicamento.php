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

$idMedicamento = 'ID_DO_MEDICAMENTO'; // Substitua pelo ID do medicamento
$novaQuantidade = 200;

try {
    $result = $client->updateItem([
        'TableName' => 'Medicamentos',
        'Key' => [
            'id' => ['S' => $idMedicamento],
        ],
        'UpdateExpression' => 'SET quantidade = :qtd',
        'ExpressionAttributeValues' => [
            ':qtd' => ['N' => (string)$novaQuantidade],
        ],
        'ReturnValues' => 'UPDATED_NEW',
    ]);

    echo "Medicamento atualizado com sucesso!\n";
    print_r($result['Attributes']);
} catch (DynamoDbException $e) {
    echo "Erro ao atualizar medicamento: " . $e->getMessage() . "\n";
}
