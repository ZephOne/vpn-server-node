<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2021, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

require_once dirname(__DIR__).'/vendor/autoload.php';
$baseDir = dirname(__DIR__);

use LC\Node\Config;
use LC\Node\Connection;
use LC\Node\HttpClient\CurlHttpClient;
use LC\Node\Syslog;
use LC\Node\Utils;

try {
    $configFile = $baseDir.'/config/config.php';
    $config = Config::fromFile($configFile);
    $apiSecretFile = $baseDir.'/config/node.key';
    if (false === $apiSecret = file_get_contents($apiSecretFile)) {
        throw new RuntimeException('unable to read file "'.$apiSecretFile.'"');
    }
    $connection = new Connection(new CurlHttpClient($apiSecret), $config->apiUrl());
    $connection->connect(
        Utils::reqEnvString('PROFILE_ID'),
        Utils::reqEnvString('X509_0_OU'),
        Utils::reqEnvString('common_name'),
        Utils::optEnvString('trusted_ip'),
        Utils::optEnvString('trusted_ip6'),
        Utils::reqEnvString('ifconfig_pool_remote_ip'),
        Utils::reqEnvString('ifconfig_pool_remote_ip6'),
        Utils::reqEnvString('time_unix')
    );
} catch (Exception $e) {
    $log = new Syslog('client-connect');
    $log->error($e->getMessage());

    exit(1);
}
