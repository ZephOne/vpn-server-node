<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Node\Tests;

use LC\Node\ConfigCheck;
use PHPUnit\Framework\TestCase;

class ConfigCheckTest extends TestCase
{
    public function testCheckOverlap()
    {
        // IPv4
        $this->assertEmpty(ConfigCheck::checkOverlap(['192.168.0.0/24', '10.0.0.0/8']));
        $this->assertEmpty(ConfigCheck::checkOverlap(['192.168.0.0/24', '192.168.1.0/24']));
        $this->assertEmpty(ConfigCheck::checkOverlap(['192.168.0.0/25', '192.168.0.128/25']));

        $this->assertSame(
            [
                [
                    '192.168.0.0/24',
                    '192.168.0.0/24',
                ],
            ],
            ConfigCheck::checkOverlap(['192.168.0.0/24', '192.168.0.0/24'])
        );

        $this->assertSame(
            [
                [
                    '192.168.0.0/25',
                    '192.168.0.0/24',
                ],
            ],
            ConfigCheck::checkOverlap(['192.168.0.0/24', '192.168.0.0/25'])
        );

        // IPv6
        $this->assertEmpty(ConfigCheck::checkOverlap(['fd00::/8', 'fc00::/8']));
        $this->assertEmpty(ConfigCheck::checkOverlap(['fd11:1111:1111:1111::/64', 'fd11:1111:1111:1112::/64']));

        $this->assertSame(
            [
                [
                    'fd11:1111:1111:1111::/64',
                    'fd11:1111:1111:1111::/64',
                ],
            ],
            ConfigCheck::checkOverlap(['fd11:1111:1111:1111::/64', 'fd11:1111:1111:1111::/64'])
        );

        $this->assertSame(
            [
                [
                    'fd11:1111:1111:1111::/100',
                    'fd11:1111:1111:1111::/64',
                ],
            ],
            ConfigCheck::checkOverlap(['fd11:1111:1111:1111::/64', 'fd11:1111:1111:1111::/100'])
        );
    }

    public function testHostOverlap()
    {
        $this->assertEmpty(ConfigCheck::checkOverlap(['192.168.2.0/24', '192.168.1.5/32']));
        $this->assertNotEmpty(ConfigCheck::checkOverlap(['192.168.1.0/24', '192.168.1.5/32']));
    }
}
