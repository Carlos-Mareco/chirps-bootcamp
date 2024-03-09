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

// URL do arquivo para download
$url = 'https://github.com/axllent/mailpit/releases/download/v1.14.1/mailpit-windows-amd64.zip';

// Caminho onde o arquivo será salvo
$savePath = __DIR__ . '/mailpit-windows-amd64.zip';

// Verifica se o sistema operacional é Windows
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Inicializa cURL
    $ch = curl_init($url);

    // Configurações de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_NOPROGRESS, false); // Habilita o acompanhamento do progresso
    curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback'); // Função de callback para acompanhar o progresso

    // Função de callback para acompanhar o progresso
    function progressCallback($resource, $download_size, $downloaded, $upload_size, $uploaded) {
        if ($download_size > 0) {
            $percentage = ($downloaded / $download_size) * 100;
            echo "Chirper config: Download do Mailpit em andamento... " . round($percentage, 2) . "%\n";
        }
        return 0;
    }

    // Executa o download
    $content = curl_exec($ch);

    // Fecha cURL
    curl_close($ch);

    // Salva o arquivo no disco
    if ($content !== false) {
        if (file_put_contents($savePath, $content)) {
            echo $script_prefix .  "Mailpit baixado com sucesso";
        } else {
            echo $script_prefix .  "Falha ao salvar o Mailpit no diretório";
        }
    } else {
        echo $script_prefix .  "Falha ao baixar o Mailpit.";
    }
} else {
    echo $script_prefix .  "Este script só pode ser executado em sistemas Windows.";
}

// Caminho do arquivo ZIP para extrair
$zipFile = __DIR__ . '/mailpit-windows-amd64.zip';

// Caminho onde o arquivo será extraído
$extractPath = __DIR__;

// Inicializa a extensão ZipArchive
$zip = new ZipArchive;

// Abre o arquivo ZIP
if ($zip->open($zipFile) === true) {
    // Itera sobre os arquivos no ZIP
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);

        // Verifica se o arquivo atual é mailpit.exe
        if (basename($filename) === 'mailpit.exe') {
            // Extrai apenas o arquivo mailpit.exe
            $zip->extractTo($extractPath, $filename);
            echo $script_prefix .  "Arquivo mailpit.exe extraído com sucesso.\n";
            break; // Interrompe o loop após encontrar e extrair o arquivo desejado
        }
    }

    // Fecha o arquivo ZIP
    $zip->close();

    // Deleta o arquivo ZIP após a extração
    if (unlink($zipFile)) {
        echo $script_prefix .  "Deletando arquivos temporários da extração.\n";
    } else {
        echo $script_prefix .  "Falha ao deletar arquivos temporários da extração.\n";
    }
} else {
    echo $script_prefix .  "Falha ao abrir o arquivo de extração.\n";
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

// Executa o comando php artisan migrate
echo $script_prefix .  "Executando migrações do Laravel...\n";
executeCommand('php artisan migrate');
?>