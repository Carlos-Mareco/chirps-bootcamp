<?php

// Prefixo programa de configuração
$script_prefix = 'Chirper configuration: ';

// Caminho do arquivo .env.example no mesmo diretório do script
$oldName = __DIR__ . '/.env.example';

// Caminho do novo arquivo .env no mesmo diretório do script
$newName = __DIR__ . '/.env';

// Tenta renomear o arquivo
if (rename($oldName, $newName)) {
    echo $script_prefix . "Arquivo .env criado com sucesso";
} else {
    echo $script_prefix .  "Falha ao criar o arquivo .env";
}

// Caminho para o diretório 'database' dentro do diretório do script
$databaseDir = __DIR__ . '/database';

// Caminho para o arquivo 'database.sqlite' dentro do diretório 'database'
$databaseFile = $databaseDir . '/database.sqlite';

// Verifica se o diretório 'database' existe, caso contrário, cria o diretório
if (!file_exists($databaseDir)) {
    mkdir($databaseDir, 0777, true);
    echo $script_prefix .  "Diretório 'database' criado com sucesso.\n";
}

// Tenta criar o arquivo 'database.sqlite'
$file = fopen($databaseFile, 'w');

// Verifica se o arquivo foi aberto com sucesso
if ($file === false) {
    echo $script_prefix .  "Falha ao criar o arquivo 'database.sqlite'.\n";
} else {
    // Fecha o arquivo
    fclose($file);
    echo $script_prefix .  "Arquivo 'database.sqlite' criado com sucesso.\n";
}

// Função para executar comandos do sistema operacional
function executeCommand($command) {
    $output = shell_exec($command);
    echo "<pre>$output</pre>";
}

// Instala dependências do Composer
echo $script_prefix .  "Instalando dependências do Composer...\n";
executeCommand('composer install');

// Instala dependências do NPM
echo $script_prefix .  "Instalando dependências do NPM...\n";
executeCommand('npm install');

// Executa o comando php artisan key:generate
echo $script_prefix .  "Gerando chave da aplicação...\n";
executeCommand('php artisan key:generate');

// Executa o comando php artisan migrate
echo $script_prefix .  "Executando migrações do Laravel...\n";
executeCommand('php artisan migrate');
?>
