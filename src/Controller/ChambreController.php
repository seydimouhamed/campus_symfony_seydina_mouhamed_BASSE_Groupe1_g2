<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/chambre")
 */
class ChambreController extends AbstractController
{
    /**
     * @Route("/", name="chambre")
     */
    public function index(ChambreRepository $chambreRepository)
    {
        $chambre = $chambreRepository->findAll();
        return $this->render('chambre/index.html.twig', compact('chambre'),);
    }
    
    /**
     * @Route("/add", name="addChambre", methods={"POST","GET"})
     */
    public function add(Request $request, ChambreRepository $cham):Response
    {
        

        $chambre = new Chambre();
        $form = $this->createForm(ChambreType::class,$chambre);
        if($request->isMethod('POST'))
        {
            if($request->isXmlHttpRequest())
            {
                $form->handleRequest($request);
                //return new JsonResponse(['profil'  => json_encode($request->request->all())]);
                if($form->isSubmitted() && $form->isValid())
                {
                    //dd($chambre);
                    //$numbat=$form->get('numero_batimat')->getData();
                    // $idLast = $cham->findBy(array(),array('id' => 'DESC'),1 ,0);
                    // $idLast=$idLast[0];
                    $idLast = sprintf("%'.03d", rand(0,999));
                    $numBat=$form->get('numero_batimat')->getData();
                  
                   $gen=new Generator();
                    $numCham=$gen->generateNumeroChambre($idLast,$numBat);


                    $chambre->setNumero($numCham);
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($chambre);
                    $em->flush();
                    return new JsonResponse(['resultat'  => json_encode("success")]);
                   
                   //return $this->redirectToRoute('listChambre');
                }
                
                // //dump($form);

            }else
            {
                //dump($form);
                return new Response('Bad request',400);
            }
        }
        
        return $this->render('Chambre/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/list", name="listChambre")
     */
    public function list(ChambreRepository $chambreRepository)
    {
        $chambre = $chambreRepository->findAll();
       // dd($chambre);
        return $this->render('Chambre/index.html.twig', compact(('chambre')));
    }

    /**
     * @Route("/getlist/{p<[0-9]+>}", name="getChambre", defaults={"p"=1})
     */
    public function getChambreList(ChambreRepository $chambreRepository,SerializerInterface $serializer, $p)
    {
       // $chambre = $chambreRepository->findAll();

        
        // le nombre total de pages !
       $nbrePage =ceil(count($chambreRepository->findAll())/3);
       // Pgae encours $p
       $offset=  3*($p-1);
        $chambre =$chambreRepository->findBy(
            array(),
            array('id' => 'DESC'),
            3,//limit
            $offset//offset
        );

        $cham_serialize = $serializer->serialize($chambre, 'json');
       // dd($chambre);

      return new JsonResponse(json_encode(['chambre'=>$cham_serialize,'nbrPage'=>$nbrePage]), 200, [], true);
       
       // return $this->render('Chambre/index.html.twig', compact(('chambre')));
    }

    /**
     * @Route("/{id<[0-9]+>}/update", name="chambre_update", methods={"POST", "GET"})
     */
    public function update(Request $request, Chambre $chambre):Response
    {
        $form = $this->createForm(ChambreType::class,$chambre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //dd($chambre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($chambre);
            $em->flush();
            return $this->redirectToRoute('listChambre');
        }
        return $this->render('Chambre/edit.html.twig', ['chambre'=>$chambre, 'form'=>$form->createView()]);
    }
    /**
     * @Route("/update", name="chambre_update_ajx", methods={"POST", "GET"})
     */
    public function updateAjax(Request $request):Response
    {
        if($request->isXmlHttpRequest())
        {
            // $form = $this->createForm(ChambreType::class,$chambre);
            // $form->handleRequest($request);
           $idChambre= $request->request->all()['id'];
            $em = $this->getDoctrine()->getManager();
            $chambre = $em->getRepository(Chambre::class)->find($idChambre);
            if(!$chambre)
            {
                return new JsonResponse(["result"=>'error']);
            }
            $attribut=$request->request->all()['champ'];
            $val=$request->request->all()['val'];
            $chambre->{'set'.ucfirst($attribut)}($val);
            $em->persist($chambre);
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
     * @Route("/{id<[0-9]+>}/delete", name="chambre_delete")
     */
    public function delete(Chambre $chambre)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($chambre);
            $em->flush();
            return $this->redirectToRoute('listChambre');
        
    }


    // public function generateNumeroChambre(ChambreRepository $cham,$numbatimat)
    // {
    //     //$chambre->setNumero(sprintf("%'.03d-", rand(0,999)).$form->get('numero_batimat')->getData());
        
    //     $idLast = $cham->findBy(array(),array('id' => 'DESC'),1 ,0)[0];
    //     $idFormat=str_pad($idLast, 3, '0', STR_PAD_LEFT);
    //     $idBatFormat=str_pad($numbatimat, 3, '0', STR_PAD_LEFT);

    //     return $idFormat."-".$idBatFormat;
    // }
}
