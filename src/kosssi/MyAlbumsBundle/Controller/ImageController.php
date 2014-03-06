<?php

namespace kosssi\MyAlbumsBundle\Controller;

use kosssi\MyAlbumsBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Image controller.
 *
 * @Config\Route("/image")
 */
class ImageController extends Controller
{
    /**
     * Show an image.
     *
     * @param Image $image
     *
     * @Config\Route("/{id}", name="image_show")
     * @Config\template()
     *
     * @return array
     */
    public function showAction(Image $image)
    {
        return [
            'image' => $image,
        ];
    }

    /**
     * Delete an image.
     *
     * @param Request $request
     * @param Image   $image
     *
     * @Config\Route("/{id}/delete", name="image_delete")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteAction(Request $request, Image $image)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new Response('ok');
        } else {
            if ($album = $image->getAlbum()) {
                return $this->redirect($this->generateUrl('album_show', array('id' => $album->getId())));
            } else {
                return $this->redirect($this->generateUrl('homepage'));
            }
        }
    }

    /**
     * Rotate an image.
     *
     * @param Request $request
     * @param Image   $image
     * @param Integer $rotation
     *
     * @Config\Route("/{id}/rotate/left", name="image_rotation_left", defaults={"rotation" = 90})
     * @Config\Route("/{id}/rotate/right", name="image_rotation_right", defaults={"rotation" = -90})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function rotationAction(Request $request, Image $image, $rotation)
    {
        // rotate
        $this->get('kosssi_my_albums.helper.image_rotate')->rotate($image, $rotation);

        // remove cache
        $this->get('kosssi_my_albums.helper.image_cache')->removeFilters($image->getPath());

        // save entity
        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();


        if ($request->isXmlHttpRequest()) {
            return new Response('ok');
        } else {
            if ($album = $image->getAlbum()) {
                return $this->redirect($this->generateUrl('album_show', array('id' => $album->getId())));
            } else {
                return $this->redirect($this->generateUrl('homepage'));
            }
        }
    }
}
