<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Recipe;
use AppBundle\Entity\Ingredient;
use AppBundle\Entity\Direction;
use AppBundle\Form\RecipeType;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Recipe controller.
 *
 * @Route("/recipe")
 */
class RecipeController extends Controller
{
    /**
     * Lists all Recipe entities.
     *
     * @Route("/", name="recipe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_ADMIN')){
            if($this->isGranted('ROLE_USER')){
                return $this->redirectToRoute('homepage');
            } else {
                return $this->redirectToRoute('login');
            }
        }

        $em = $this->getDoctrine()->getManager();

        $recipes = $em->getRepository('AppBundle:Recipe')->findAll();

        return $this->render('recipe/index.html.twig', array(
            'recipes' => $recipes,
        ));
    }

    /**
     * Creates a new Recipe entity.
     *
     * @Route("/new", name="recipe_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if(!$this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('login');
        }

        $recipe = new Recipe();
        $user = $this->getUser();
        $recipe->setUser($user);
        $form = $this->createForm('AppBundle\Form\RecipeType', $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('recipe_show', array('id' => $recipe->getId()));
        }

        return $this->render('recipe/new.html.twig', array(
            'recipe' => $recipe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Recipe entity.
     *
     * @Route("/{id}", name="recipe_show")
     * @Method("GET")
     */
    public function showAction(Recipe $recipe)
    {
        $deleteForm = $this->createDeleteForm($recipe);

        return $this->render('recipe/show.html.twig', array(
            'recipe' => $recipe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{id}/edit", name="recipe_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Recipe $recipe)
    {
        if (!$this->isGranted('edit', $recipe)){
            return $this->redirectToRoute('homepage');
        }

        $originalIngredients = new ArrayCollection();
        $originalDirections = new ArrayCollection();
        foreach ($recipe->getIngredients() as $ingredient){
            $originalIngredients->add($ingredient);
        }  
        foreach ($recipe->getDirections() as $direction){
            $originalDirections->add($direction);
        }  
        $deleteForm = $this->createDeleteForm($recipe);
        $editForm = $this->createForm('AppBundle\Form\RecipeType', $recipe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($originalIngredients as $ingredient) {
                if(false === $recipe->getIngredients()->contains($ingredient)){
                    $recipe->getIngredients()->removeElement($ingredient);
                    $em->persist($ingredient);
                    $em->remove($ingredient);
                }
            }
            foreach ($originalDirections as $direction) {
                if(false === $recipe->getDirections()->contains($direction)){
                    $recipe->getDirections()->removeElement($direction);
                    $em->persist($direction);
                    $em->remove($direction);
                }
            }

            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('recipe_show', array('id' => $recipe->getId()));
        }

        return $this->render('recipe/edit.html.twig', array(
            'recipe' => $recipe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Recipe entity.
     *
     * @Route("/{id}", name="recipe_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Recipe $recipe)
    {
        $form = $this->createDeleteForm($recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recipe);
            $em->flush();
        }

        return $this->redirectToRoute('recipe_index');
    }

    /**
     * Creates a form to delete a Recipe entity.
     *
     * @param Recipe $recipe The Recipe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recipe $recipe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recipe_delete', array('id' => $recipe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     *
     * @Route("/{id}/ajaxDeleteRecipe", name="ajax_recipe_delete")
     * @Method({"GET", "POST"})
     */
    public function ajaXDeleteAction(Request $request, Recipe $recipe)
    {
        $isAjax = $this->get('Request')->isXMLHttpRequest();
        if ($isAjax) {  
            $em = $this->getDoctrine()->getManager();
            $em->remove($recipe);
            $em->flush();     
            return new JsonResponse(array('data' => 'this is a json response'));
        }
        return new Response('This is not ajax', 400);
    }
}
