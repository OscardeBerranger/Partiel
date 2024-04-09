<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $tmp = $serializer->deserialize($request->getContent(), User::class, 'json');

        $user = new User();
        $user->setEmail($tmp->getEmail());

        $user->setPassword(
            $hasher->hashPassword(
                $user,
                $tmp->getPassword()
            )
        );
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_product');
    }
}
