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

try {
    $result = $client->scan([
        'TableName' => 'Medicamentos',
    ]);

    foreach ($result['Items'] as $item) {
        echo "ID: " . $item['id']['S'] . "\n";
        echo "Nome: " . $item['nome']['S'] . "\n";
        echo "Fabricante: " . $item['fabricante']['S'] . "\n";
        echo "Lote: " . $item['lote']['S'] . "\n";
        echo "Validade: " . $item['validade']['S'] . "\n";
        echo "Quantidade: " . $item['quantidade']['N'] . "\n";
        echo "-------------------------------\n";
    }
} catch (DynamoDbException $e) {
    echo "Erro ao buscar medicamentos: " . $e->getMessage() . "\n";
}
