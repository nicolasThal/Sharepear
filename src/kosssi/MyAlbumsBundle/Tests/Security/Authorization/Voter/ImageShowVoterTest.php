<?php

namespace kosssi\MyAlbumsBundle\Tests\Security\Authorization\Voter;

use kosssi\MyAlbumsBundle\Security\Authorization\Voter\ImageShowVoter;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ImageShowVoterTest
 *
 * @author Simon Constans <kosssi@gmail.com>
 */
class ImageShowVoterTest extends AssertVoter
{
    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();

        $this->object = Phake::mock('kosssi\MyAlbumsBundle\Entity\Image');
        $this->attributes = ['IMAGE_SHOW'];
        $this->voter = new ImageShowVoter();
    }

    /**
     * Test access denied because no support attribute
     */
    public function testAccessDeniedNoSupportAttribute()
    {
        $this->attributes = ['NOT_IMAGE_SHOW'];

        $this->assertVoter(VoterInterface::ACCESS_DENIED);
    }

    /**
     * test access denied no support class
     */
    public function testAccessDeniedNoSupportClass()
    {
        $this->object = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->assertVoter(VoterInterface::ACCESS_DENIED);
    }

    /**
     * test access denied user not authorized
     */
    public function testAccessDeniedUserNotAuthorized()
    {
        $this->assertVoter(VoterInterface::ACCESS_DENIED, 'user1', 'user2');
    }

    /**
     * test access granted
     */
    public function testAccessGranted()
    {
        $this->assertVoter(VoterInterface::ACCESS_GRANTED, 'user1', 'user1');
    }
}
