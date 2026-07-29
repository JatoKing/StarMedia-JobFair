<?php
// Helper functions untuk baca & tulis config/prizes.json dengan selamat
// Guna file locking (flock) untuk elak race condition bila ramai orang spin serentak

function getPrizesFilePath()
{
    return __DIR__ . '/../config/prizes.json';
}

// Baca prizes.json — guna shared lock (LOCK_SH) sebab hanya baca
function readPrizesFile()
{
    $path = getPrizesFilePath();
    $handle = fopen($path, 'r');

    if (!$handle) {
        return null;
    }

    flock($handle, LOCK_SH);
    $content = stream_get_contents($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return json_decode($content, true);
}

// Buka fail dengan exclusive lock untuk baca + tulis (guna semasa proses spin)
// Return array [handle, data] — caller WAJIB panggil closePrizesFile() selepas selesai
function openPrizesFileForUpdate()
{
    $path = getPrizesFilePath();
    $handle = fopen($path, 'r+');

    if (!$handle) {
        return [null, null];
    }

    flock($handle, LOCK_EX); // exclusive lock — proses lain kena tunggu
    $content = stream_get_contents($handle);
    $data = json_decode($content, true);

    return [$handle, $data];
}

function writeAndClosePrizesFile($handle, $data)
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, $json);
    flock($handle, LOCK_UN);
    fclose($handle);
}