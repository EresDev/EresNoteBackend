<?php
namespace App\Tests\Unit\Serializer;

use App\Entity\User;
use App\Form\UserType;
use App\Serializer\FormErrorSerializer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormErrorSerializerTest extends KernelTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
    }

    public function testConvertFormToArray_invalidData(){
        $form_data = [
            'email' => 'test',
            'plainPassword' => [
                'pass' => '1111',
                'pass2' => ''
            ]
        ];

        $user = new User();
        $user->setEmail($form_data['email']);
        $user->setPlainPassword($form_data['plainPassword']['pass']);

        $factory = self::$container->get(FormFactoryInterface::class);
        /**
         * @var FormInterface $form
         */
        $form = $factory->create(UserType::class, $user);

        $form->submit($form_data);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());

        $translator = self::$container->get(TranslatorInterface::class);
        $formErrorSerializer = new FormErrorSerializer($translator);
        $errors = $formErrorSerializer->convertFormToArray($form);

        $this->assertArrayHasKey('errors', $errors['children']['email']);
        $this->assertArrayHasKey('errors', $errors['children']['plainPassword']['children']['pass']);
    }

    public function testConvertFormToArray_validData(){
        $form_data = [
            'email' => 'test@example.com',
            'plainPassword' => [
                'pass' => 'somepassword@slkd12',
                'pass2' => 'somepassword@slkd12'
            ]
        ];

        $user = new User();
        $user->setEmail($form_data['email']);
        $user->setPlainPassword($form_data['plainPassword']['pass']);

        $factory = self::$container->get(FormFactoryInterface::class);
        /**
         * @var FormInterface $form
         */
        $form = $factory->create(UserType::class, $user);

        $form->submit($form_data);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());

        $translator = self::$container->get(TranslatorInterface::class);
        $formErrorSerializer = new FormErrorSerializer($translator);
        $errors = $formErrorSerializer->convertFormToArray($form);

        $this->assertArrayNotHasKey('errors', $errors['children']['email']);
        $this->assertArrayNotHasKey('errors', $errors['children']['plainPassword']['children']['pass']);
    }
}