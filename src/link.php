<?php

use Lzz\Utils\UrlUtils;
use Lzz\Utils\Utils;

require_once '/var/www/lzz/html/autoload.php'; // update this to the path you are using

function badRequest() {
    header("Location: http://<base_url>");
}

if (!isset($_GET['id'])) {
    badRequest();
    exit(0);
}

$linkShortCode = trim((string)$_GET['id']);

$linkId = UrlUtils::shortCodeToId($linkShortCode);

if ($linkId === null) {
    badRequest();
    exit(0);
}

$db = Utils::getDatabase();

$query = $db->prepare('SELECT `id`, `link` FROM `links` WHERE `id` = ? LIMIT 1;');
$query->execute([$linkId]);

$linkData = $query->fetch(PDO::FETCH_ASSOC);

if (!isset($linkData['id'])) {
    badRequest();
    exit(0);
}

$destination = (string)$linkData['link'];

$browserId = 0;

if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {

    $agentString = trim((string)$_SERVER['HTTP_USER_AGENT']);

    $browserHash = md5($agentString);

    $query = $db->prepare('SELECT `id`, `agent` FROM `browsers` WHERE `hash` = ? LIMIT 1;');
    $query->execute([
        $browserHash
    ]);

    $browserData = $query->fetch(PDO::FETCH_ASSOC);

    if (isset($browserData['id']) && $browserData['agent'] === $agentString) {

        $query = $db->prepare('UPDATE `browsers` SET `count` = `count` + 1 WHERE `id` = ? LIMIT 1;');
        $query->execute([
            (int)$browserData['id']
        ]);

        $browserId = (int)$browserData['id'];

    }
    else {

        $query = $db->prepare('INSERT INTO `browsers` (`hash`, `agent`, `count`) VALUES (?,?,?);');
        $query->execute([
            $browserHash,
            $agentString,
            1
        ]);

        $browserId = $db->lastInsertId();

    }

}

$query = $db->prepare('INSERT INTO `ipLog` (`link`, `date`, `browser`, `ip`) VALUES (?,?,?,?);');
$query->execute([
    (int)$linkData['id'],
    time(),
    $browserId,
    Utils::getUserIpAddress()
]);

$query = $db->prepare('UPDATE `links` SET `accessed` = `accessed` + 1 WHERE `id` = ? LIMIT 1;');
$query->execute([
    (int)$linkData['id']
]);

header('Location: ' . $destination);
