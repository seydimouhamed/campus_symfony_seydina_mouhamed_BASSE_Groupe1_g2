<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Required;

/**
 * @Route("/etudiant")
 */
class EtudiantController extends AbstractController
{
    /**
     * @Route("/list/{p<[0-9]+>}", name="listEtudiant",defaults={"p"=1})
     */
    public function index(EtudiantRepository $etudiantRepository,$p)
    {
        // le nombre total de pages !
       $nbrePage =ceil(count($etudiantRepository->findAll())/3);
       // Pgae encours $p
       $offset=  3*($p-1);
        $etudiant =$etudiantRepository->findBy(
            array(),
            array('id' => 'DESC'),
            3,//limit
            $offset//offset
        );

        return $this->render('Etudiant/list.html.twig', ["nbrePages"=>$nbrePage,"etudiant"=>compact(('etudiant'))]);
    }
    /**
     * @Route("/", name="addEtudiant",methods={"POST","GET"})
     */
    public function addEtudiant(Request $request):Response
    {
       // $entityManager = $this->getDoctrine()->getManager();

        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class,$etudiant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($etudiant);
            $em->flush();

            return $this->redirectToRoute('listEtudiant');
        }

        return $this->render('Etudiant/edit.html.twig',['form'=> $form->createView()]);
    }

    /**
     * @Route("/list/{p<[0-9]+>}", name="listEtudiant",defaults={"p"=1})
     */
    public function listEtudiant(EtudiantRepository $etudiantRepository,$p)
    {
        // le nombre total de pages !
       $nbrePage =ceil(count($etudiantRepository->findAll())/3);
       // Pgae encours $p
       $offset=  3*($p-1);
        $etudiant =$etudiantRepository->findBy(
            array(),
            array('id' => 'DESC'),
            3,//limit
            $offset//offset
        );

        return $this->render('Etudiant/index.html.twig', ["nbrePages"=>$nbrePage,"etudiant"=>compact(('etudiant'))]);
    }

    
    /**
     * @Route("/{id<[0-9]+>}/update", name="updatetudiant",methods={"POST","GET"})
     */
    public function update(Request $request,Etudiant $etudiant):Response
    {
       // $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(EtudiantType::class,$etudiant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($etudiant);
            $em->flush();

            return $this->redirectToRoute('listEtudiant');
        }

        return $this->render('Etudiant/edit.html.twig',['form'=> $form->createView()]);
    }

    
    /**
     * @Route("/{id<[0-9]+>}/delete", name="deleteEtudiant")
     */
    public function delete(Etudiant $etudiant)
    {
            $em=$this->getDoctrine()->getManager();
            $em->remove($etudiant);
            $em->flush();

             return $this->redirectToRoute('listEtudiant');
             
    }

    
    /**
     * @Route("/all/{p<[0-9]+>}", name="getEtudiant",defaults={"p"=1})
     */
    public function getEtudiant(Request $req,EtudiantRepository $etudiantRepository,SerializerInterface $serializer, $p){
        if($req->isXmlHttpRequest())
        {
            // le nombre total de pages !
       // $nbrePage =ceil(count($etudiantRepository->findAll())/5);
        // Pgae encours $p
          $offset=  5*($p-1);
        $etudiants =$etudiantRepository->findBy(
            array(),
            array('id' => 'DESC'),
            5,//limit
            $offset//offset
        );

        $etu_serialize = $serializer->serialize($etudiants, 'json');
    // dd($etudiants);

       return new JsonResponse(json_encode(['etudiants'=>$etu_serialize]), 200, [], true);
       

          // return new JsonResponse(['etudiants'  => json_encode($etu_serialize)]);
        }

        return new Response(" cette requte nest pas une requete ajax", 400);
    }


    /**
     * @Route("/register", name="register_etudiant")
     */
    public function regiqter_ajax(Request $request){
        
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class,$etudiant);
        if($request->isXmlHttpRequest())
        {
        //     $etudiant=$request->request->all()['etudiant'];
        // return new JsonResponse(['profil'  => json_encode($etudiant['profil' ])]);

                $form->handleRequest($request);
                //return new JsonResponse(['profil'  => json_encode($request->request->all())]);
                if($form->isSubmitted() && $form->isValid())
                {
                    
                    $prenom=$form->get('prenom')->getData();
                    $nom=$form->get('nom')->getData();
                    $profil=$form->get('profil')->getData();
                   
                    //get entity manager
                    
                   $em = $this->getDoctrine()->getManager();
                   $pension="2";
                    if($profil=="boursier" || $profil=="loge")
                    {
                        $pension=$request->request->all()['pension'];
                        if($profil=='loge')
                        {
                            //$idchambre=1;
                            
                            $idchambre=$request->request->all()['numeroChambre'];
                            $chambre = $em->getRepository(Chambre::class)->find($idchambre);
                            if($chambre)
                            {
                                $etudiant->setChambre($chambre);
                            }
                        }
                       $etudiant->setPension($pension);
                    }
                    else if($profil=="nonBoursier")
                    {
                        $adresse=$request->request->all()['adresse'];
                        $etudiant->setAdresse($adresse);
                    }
                  
                   $gen=new Generator();
                    $matricule=$gen->generateMatricule($prenom,$nom);


                   $etudiant->setMatricule($matricule);

                    
                    $em->persist($etudiant);
                    $em->flush();
                    return new JsonResponse(['resultat'  => json_encode('success')]);
                   
                }
        }

        return new Response(" cette requte nest pas une requete ajax", 400);
    }


    
    /**
     * @Route("/update", name="etudiant_update_ajx", methods={"POST", "GET"})
     */
    public function updateAjax(Request $request):Response
    {
        if($request->isXmlHttpRequest())
        {
            // $form = $this->createForm(ChambreType::class,$chambre);
            // $form->handleRequest($request);
           $idEtudiant= $request->request->all()['id'];
            $em = $this->getDoctrine()->getManager();
            $etudiant = $em->getRepository(Etudiant::class)->find($idEtudiant);
            if(!$etudiant)
            {
                return new JsonResponse(["result"=>'error']);
            }
           
            $attribut=$request->request->all()['champ'];
            if($attribut!=="profil")
            {
               $val=$request->request->all()['val'];
               $etudiant->{'set'.ucfirst($attribut)}($val);
            }
            else
            {
                // $val=$request->request->all()['val'];
                // if($val=="loge")
                // {
                //     $pension = $request->request->all()['pension'];
                //     $idchambre=$val=$request->request->all()['idChambre'];
                //     $chambre = $em->getRepository(Chambre::class)->find($idchambre);

                //     $etudiant->setPension($pension);
                //     $etudiant->setChambre($chambre);
                //     //set adresse null
                //     $etudiant->setAdresse(null);

                // }
                // else if($val=="nonBoursier")
                // {
                //     $adresse = $request->request->all()['adresse'];

                //     $etudiant->setAdresse($adresse);
                //     //set chambre null
                //     $etudiant->setChambre(null);
                //     //set pension null
                //     $etudiant->setPension(null);

                // }
                // else if($val=="boursier")
                // {
                //     $pension = $request->request->all()['pension'];

                //     $etudiant->setPension($pension);
                //     //set chambre null
                //     $etudiant->setChambre(null);
                //     //set adresse null
                //     $etudiant->setAdresse(null);

                // }
            }
            $em->persist($etudiant);
            $em->flush();

            // if($form->isSubmitted() && $form->isValid())
            // {
            //     //dd($chambre);
            //     $em = $this->getDoctrine()->getManager();
            //     $em->persist($chambre);
            //     $em->flush();
            //     return $this->redirectToRoute('listChambre');
            // }
            return new JsonResponse(["result"=>$request->request->all()['val']]);
        }
        return new Response('bad request',400);
    }

       
    /**
     * @Route("/recherche", name="rechercheEtudiant")
     */
    public function searchEtudiant(EtudiantRepository $etudiantRepository,SerializerInterface $serializer, Request $request)
    {
        
        $qr =$etudiantRepository->createQueryBuilder("n");
        $data=[];
        $params=[];
        foreach($request->request->all() as $k => $val)
        {
           if($val!=="")
           {
               $qr->andWhere('n.'.$k.' like :'.$k);
              $params+=[$k => "%".$val."%"];
           }
        }
       $qr->setParameters($params);
           
    $data=$qr->getQuery()->getResult();

        $etu_serialize = $serializer->serialize($data, 'json');

        return new JsonResponse( json_encode(['etudiants'=>$etu_serialize]), 200, [], true);
    }

}
