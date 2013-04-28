<?php

namespace Acme\StoreBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\StoreBundle\Document\Product;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();

        /** @var $repository \Acme\StoreBundle\Repository\ProductRepository */
        $repository = $dm->getRepository('AcmeStoreBundle:Product');

        $products = $repository->findAllOrderedByName();

        return $this->render('AcmeStoreBundle:Default:index.html.twig', array('products' => $products));
    }

    public function createAction($name, $price)
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($product);
        $dm->flush();

        return $this->redirect($this->generateUrl('acme_store_show', array('id' => $product->getId())));
    }

    public function showAction($id)
    {
        /** @var $product Product */
        $product = $this->get('doctrine_mongodb')
            ->getRepository('AcmeStoreBundle:Product')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        return $this->render('AcmeStoreBundle:Default:show.html.twig', array('product' => $product));
    }

    public function updateAction($id, $name)
    {
        /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();

        /** @var $product Product */
        $product = $dm->getRepository('AcmeStoreBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $product->setName($name);
        $dm->flush();

        return $this->redirect($this->generateUrl('acme_store_show', array('id' => $id)));
    }
}

