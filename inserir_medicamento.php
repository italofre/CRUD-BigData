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

try {
    $result = $client->putItem([
        'TableName' => 'Medicamentos',
        'Item' => [
            'id' => ['S' => uniqid()], // Chave primária (string)
            'nome' => ['S' => 'Paracetamol'],
            'fabricante' => ['S' => 'Neo Química'],
            'lote' => ['S' => 'L12345'],
            'validade' => ['S' => '2025-12-31'],
            'quantidade' => ['N' => '100']
        ],
    ]);
    echo "Medicamento inserido com sucesso!\n";
} catch (DynamoDbException $e) {
    echo "Erro ao inserir medicamento: " . $e->getMessage() . "\n";
}