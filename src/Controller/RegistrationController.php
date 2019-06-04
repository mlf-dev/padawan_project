<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\GithubGraphQL;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    // en paramètre un new GithubGraphQL stocké dans la variable $githubGraphQL pour pouvoir appeler sa fonction getProgileInfo
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, GithubGraphQL $githubGraphQL): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        // handleRequest récupère ce qu'a tapé l'utilisateur dans les champs, avant que cela soit stocké dans la base de données
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $github_profile = $githubGraphQL->getProfileInfo($user->getGithub());
            // dd($github_profile);
            $avatar_github = $github_profile['data']['user']['avatarUrl'];

            $user->setAvatarGithub($avatar_github);

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
