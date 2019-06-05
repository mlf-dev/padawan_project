<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="project")
     */
    public function index()
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }

    /**
     * @Route("/project/add", name="project_add")
     */
    public function add(Request $request){
        // création de l'objet à persister dans la db
        $project = new Project();
        // création de l'objet form qui va générer le html dans la vue et prendre en charge la validation du formulaire et hydratation de l'objet $project avec les données de la saisie dans le formulaire
        $project->setProposePar($this->getUser());

        // dd($request->files->get('project')); // équivaut à dd($_FILES['project']);


        // création de l'objet form
        $form = $this->createForm(ProjectType::class, $project);
        // hydratation de l'objet avec les datas du formulaire (objet request)
        $form->handleRequest($request);

        // si validation du formulaire et formulaire valide :
        if ($form->isSubmitted() && $form->isValid()){

            $fichier = $request->files->get('project');
            $extensions_allowed = ['image/jpeg','image/png','image/gif'];
            $fichier_name = $fichier['imageFile']['file'];
            $mime_type = $fichier_name->getMimeType();

            if(!in_array($mime_type,$extensions_allowed)){
                $this->addFlash('error','extension non autorisée');
                return $this->redirectToRoute('project_add');
            }
            // associer le nom de l'image au projet
            $project->setImageName($fichier_name->getClientOriginalName());

            // récupérer l'entity manager de Doctrine (ORM)
            $em = $this->getDoctrine()->getManager();
            // association du user loggé et du projet qu'il est en train de créer
            // enregsitrer dans la db des datas de form (équivalant de save() dans laravel
            $em->persist($project);
            $em->flush();
            // ajouter un message flash dans la session
            $this->addFlash('success','Merci ! Votre projet a été proposé au Maitre Jedi');
            // return redireciton vers page de confirmation de création du projet
            return $this->redirectToRoute('home');
        }


        // affichage de la vue
        return $this->render('project/add.html.twig', ['form'=>$form->createView()]);
    }
}
