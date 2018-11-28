<?php
namespace App\Tests\Unit\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    /**
     * ValidatorExtensionTrait needed for invalid_options
     * https://github.com/symfony/symfony/issues/22593
     */
    use ValidatorExtensionTrait;

    public function testSubmitValidData()
    {
        $formData = [
            'email' => 'test@example.com',
            'plainPassword' => [
                'pass' => 'anExamplePass@334',
                'pass2' => 'anExamplePass@334'
            ]
        ];

        $userToCompare = new User();

        $form = $this->factory->create(UserType::class, $userToCompare);

        $user = new User();
        $user->setEmail($formData['email']);
        $user->setPlainPassword($formData['plainPassword']['pass']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $userToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}