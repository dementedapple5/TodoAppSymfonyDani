<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
    /**
     * @Route("/todos", name="todo_list")
     */
    public function listAction(Request $request)
    {
      $todos = $this -> getDoctrine()
        ->getRepository('AppBundle:Todo')
        ->findAll();
        return $this->render('todo/index.html.twig', array(
          'todos' => $todos
        ));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo;
        $form = $this->createFormBuilder($todo)
          ->add('title', TextType::class, array('label'=> 'Titulo','attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
          ->add('category', TextType::class, array('label'=> 'Categoria','attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
          ->add('description', TextareaType::class, array('label'=> 'Descripcion','attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
          ->add('priority', ChoiceType::class, array('label'=> 'Prioridad','choices'=>array('Baja'=>'Baja','Media'=>'Media','Alta'=>'Alta'),'attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
          ->add('date', DateTimeType::class, array('label'=> 'Fecha','attr' => array('style' => 'margin-bottom:15px')))
          ->add('submit', SubmitType::class, array('label'=> 'Añadir Tarea','attr' => array('class'=>'btn btn-primary', 'style' => 'margin-bottom:15px')))
          ->getForm();

          $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid()){
            $title = $form['title']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $date = $form['date']->getData();
            $createDate = new\DateTime('now');

            $todo->setTitle($title);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDate($date);
            $todo->setCreateDate($createDate);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice',
                'Tarea Añadida'
              );

              return $this->redirectToRoute('todo_list');
          }

        return $this->render('todo/create.html.twig', array(
          'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
      $todo = $this -> getDoctrine()
        ->getRepository('AppBundle:Todo')
        ->find($id);

        $todo->setTitle($todo->getTitle());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setPriority($todo->getPriority());
        $todo->setDate($todo->getDate());
        $todo->setCreateDate($todo->getCreateDate());

        $form = $this->createFormBuilder($todo)
        ->add('title', TextType::class, array('label'=> 'Titulo','attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
        ->add('category', TextType::class, array('label'=> 'Categoria','attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
        ->add('description', TextareaType::class, array('label'=> 'Descripcion','attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
        ->add('priority', ChoiceType::class, array('label'=> 'Prioridad','choices'=>array('Baja'=>'Baja','Media'=>'Media','Alta'=>'Alta'),'attr' => array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
        ->add('date', DateTimeType::class, array('label'=> 'Fecha','attr' => array('style' => 'margin-bottom:15px')))
        ->add('submit', SubmitType::class, array('label'=> 'Añadir Tarea','attr' => array('class'=>'btn btn-primary', 'style' => 'margin-bottom:15px')))
        ->getForm();

          $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid()){
            $title = $form['title']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $date = $form['date']->getData();
            $createDate = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);

            $todo->setTitle($title);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDate($date);
            $todo->setCreateDate($createDate);


            $em->flush();

            $this->addFlash(
                'notice',
                'Tarea Actualizada'
              );

              return $this->redirectToRoute('todo_list');

            }
        return $this->render('todo/edit.html.twig', array(
          'todo' => $todo,
          'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
      $todo = $this -> getDoctrine()
        ->getRepository('AppBundle:Todo')
        ->find($id);
        return $this->render('todo/details.html.twig', array(
          'todo' => $todo
        ));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $todo = $em->getRepository('AppBundle:Todo')->find($id);
      $em->remove($todo);
      $em->flush();

      $this->addFlash(
          'notice',
          'Tarea Eliminada'
        );

        return $this->redirectToRoute('todo_list');

    }


}