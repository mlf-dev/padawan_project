<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Project;
use App\Form\ParticipantType;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{

    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Template()
     * @Route("/projects", name="projects")
     */
    public function index()
    {
        // SELECT * FROM projects
        $projects = $this->projectRepository->findAll();
        return ['projects'=>$projects];
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
            return $this->redirectToRoute('project_show');
        }


        // affichage de la vue
        return $this->render('project/add.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @Template()
     * @Route("/project/{id}", name="project_show")
     */
    public function show(Request $request){
        // return $this->render('project/show.html.twig'); -> remplacé par @Template, car tous les fichiers on été bien nommés
        // on récupère l'id dans l'url
        $project = $this->projectRepository->find($request->get('id'));

        $participant = new Participant();
        $participant->setProject($project);
        $participant->setPadawan($this->getUser());
        $participant->setStatut('en cours');
        $participant->setDateInscription(new \DateTime());

        $form = $this->createForm(ParticipantType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();
            $this->addFlash('success','A toi de jouer petit padawan !');
            return $this->redirectToRoute('project_show',['id'=>$project->getId()]);
        }

        // on passe le formulaire à la vue (createView() permet de générer le html du formulaire
        return ['project'=>$project,'form'=>$form->createView()];

    }
}
