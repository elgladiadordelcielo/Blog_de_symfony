<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Route("/noticias", name="noticias")
     */
    public function noticiasAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Noticias');

        $noticias = $repository->findAll();

        return $this->render('default/noticias.html.twig', 
            array(
                'noticias'=>$noticias,                
            )
        );
    }


     /**
     * @Route("/noticia/{id}", name="noticia", requirements={"id"="\d+"})
     */
    public function noticiaAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Noticias');

        $noticia = $repository->findOneById($id);

        $url_atras = $this->generateUrl('homepage');

        return $this->render('default/noticia_unica.html.twig', 
            array(
                'noticia'=>$noticia,
                'url_atras'=>$url_atras
            )
        );
    }

     /**
     * @Route("/noticias.{_format}",
     * name="noticias_xml",
     * requirements={"_format": "json|xml"})     
     */
    public function noticiasJsonXmlAction($_format)
    {

        $repository = $this->getDoctrine()->getRepository('AppBundle:Noticias');

        $noticias = $repository->findAllOrderedByFecha();

        $encoders = array(new XmlEncoder(), new JsonEncoder());

        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $contenido = $serializer->serialize($noticias, $_format);

        $response = new Response();

        $response->headers->set('Contente-type','application/'.$_format);
        $response->setContent($contenido);
        return $response;
    }

     /**
      * @Route("/noticia.{_format}/{id}", name="noticia_json_xml", requirements={"_format": "json|xml", "id"="\d+"})
     */
    public function noticiaJsonXmlNoticiaAction($_format, $id)
    {

        $repository = $this->getDoctrine()->getRepository('AppBundle:Noticias');
        $noticias = $repository->findOneById($id);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $json_contenido = $serializer->serialize($noticias, $_format);
        
        $response = new Response();
        $response->headers->set('Content-type', 'application/'.$_format.'');
        $response->setContent($json_contenido);
        return $response;
    }

}
