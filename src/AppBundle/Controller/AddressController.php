<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Address controller.
 *
 * @Route("address")
 */
class AddressController extends Controller
{
    /**
     * Lists all address entities.
     *
     * @Route("/", name="address_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $addresses = $em->getRepository('AppBundle:Address')->findAll();

        return $this->render('address/index.html.twig', array(
            'addresses' => $addresses,
        ));
    }

    /**
     * Creates a new address entity.
     *
     * @Route("/new", name="address_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $address = new Address();
        $form = $this->createForm('AppBundle\Form\AddressType', $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $picture */
            $picture = $form->get('picture')->getData();

            if ($picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $address->setPicture($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('address_show', array('id' => $address->getId()));
        }

        return $this->render('address/new.html.twig', array(
            'address' => $address,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a address entity.
     *
     * @Route("/{id}", name="address_show")
     * @Method("GET")
     */
    public function showAction(Address $address)
    {
        $deleteForm = $this->createDeleteForm($address);

        return $this->render('address/show.html.twig', array(
            'address' => $address,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing address entity.
     *
     * @Route("/{id}/edit", name="address_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Address $address)
    {
        $deleteForm = $this->createDeleteForm($address);
        $editForm = $this->createForm('AppBundle\Form\AddressType', $address);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('AppBundle:Address')->findAll();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var UploadedFile $picture */
            $picture = $editForm->get('picture')->getData();

            if ($picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $address->setPicture($newFilename);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('address_index', array(
                'addresses' => $addresses,
            ));
        }

        return $this->render('address/edit.html.twig', array(
            'address' => $address,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a address entity.
     *
     * @Route("/delete/{id}", name="address_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $address = $em->getRepository('AppBundle:Address')->find($id);

        $filename = $address->getPicture();
        $filesystem = new Filesystem();
        $filesystem->remove($this->get('kernel')->getRootDir().'/../web/uploads/'.$filename);
        $address->setPicture(null);

        if ($request->get('type') != 'image') {
            $em->remove($address);
        }

        $em->flush();

        return new JsonResponse(['response'=>'success']);
    }

    /**
     * Creates a form to delete a address entity.
     *
     * @param Address $address The address entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Address $address)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('address_delete', array('id' => $address->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
