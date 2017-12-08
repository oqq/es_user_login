<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity;

use Oqq\EsUserLogin\Domain\AggregateId;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use OqqTest\EsUserLogin\Domain\AggregateIdTestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\IdentityId
 */
class IdentityIdTest extends AggregateIdTestCase
{
    /**
     * @test
     */
    public function it_creates_identity_id_from_email_address(): void
    {
        $emailAddress = EmailAddress::fromString('foo@bar.de');
        $identityId = IdentityId::fromEmailAddress($emailAddress);

        $this->assertInstanceOf(AggregateId::class, $identityId);
        $this->assertInstanceOf(IdentityId::class, $identityId);

        $sameEmailAddress = EmailAddress::fromString('Foo@Bar.De');
        $sameIdentityId = IdentityId::fromEmailAddress($sameEmailAddress);

        $this->assertTrue($identityId->sameValueAs($sameIdentityId));
    }

    protected function getAggregateIdClassName(): string
    {
        return IdentityId::class;
    }
}
