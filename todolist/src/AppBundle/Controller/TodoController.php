<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categorisation;
use AppBundle\Entity\Category;
use AppBundle\Entity\Todo;
use AppBundle\Repository\CategoryRepository;

use Doctrine\Common\Collections\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{


    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBy([], ['dueDate' => 'ASC']);
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig', ['todos' => $todos]);
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {

        $todo = new Todo;
        /** @var CategoryRepository $catRepo */
        $catRepo = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $allCategories = $catRepo->getAllNames();
        //return new Response();
        $form = $this->createFormBuilder($todo)->add('name', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('tags', TextType::class, ['attr' => ['class' => 'form-control tokenfield', 'style' => 'margin-bottom:15px']])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('priority', ChoiceType::class, ['choices' => ['Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'], 'attr' => ['class' => 'form-control', 'style' => 'margin-botton:15px']])
            ->add('due_date', DateTimeType::class, ['attr' => ['style' => 'margin-bottom:15px']])
            ->add('save', SubmitType::class, ['label' => 'Create Todo', 'attr' => ['class' => 'btn-primary', 'style' => 'margin-bottom:15px']])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //fetching data
            $name = $form['name']->getData();

            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $categorisations = $form['tags']->getData();
            $categorisations = explode(',', $categorisations);
            $now = new\DateTime('now');
            $todo->setName($name);

            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();


            foreach ($categorisations as $key => $categorisation)
            {
                $this->addTodoToCategory($name, $categorisation);
            }
            $this->addFlash(
                'notice',
                'Todo Added'
            );
            return $this->redirectToRoute('todo_list');
        }
        // replace this example code with whatever you need
        return $this->render('todo/create.html.twig', [
            'form' => $form->createView(),
            'all_categories' => $allCategories,
        ]);
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        $allCategories = $this->getDoctrine()->getManager()->getRepository(Category::class)->getAllNames();
        $existingCategories = array_map(function ($item) {return $item->getCategory()->getName();},$todo->getCategorisations()->toArray());
        $now = new\DateTime('now');
        $todo->setCreateDate($now);
        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class,
                ['attr' => ['class' => 'form-control', 'style' => 'margin-botton:15px']])
            ->add('tags', TextType::class,
                ['attr' => ['class' => 'form-control tokenfield', 'style' => 'margin-bottom:15px']])
            ->add('description', TextareaType::class,
                ['attr' => ['class' => 'form-control', 'style' => 'margin-botton:15px']])
            ->add('priority', ChoiceType::class,
                ['choices' => ['Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'], 'attr' => ['class' => 'form-control', 'style' => 'margin-botton:15px']])
            ->add('due_date', DateTimeType::class,
                ['attr' => ['style' => 'margin-bottom:15px']])
            ->add('save', SubmitType::class,
                ['label' => 'Update Todo', 'attr' => ['class' => 'btn-primary', 'style' => 'margin-botton:15px']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //fetching data
            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $newCategories = $form['tags']->getData();
            $newCategories = explode(',', $newCategories);
            $now = new\DateTime('now');
            $em = $this->getDoctrine()->getManager();

            /** @var Todo $todo */
            $todo = $em->getRepository('AppBundle:Todo')->find($id);
            $todo->setName($name);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);
            $removedCategories = array_diff($existingCategories, $newCategories);
            foreach ($removedCategories as $key => $category) {
                $this->removeTodoFromCategory($todo, $category);
            }
            $em->flush();
            foreach ($newCategories as $key => $category)
            {
                $this->addTodoToCategory($name, $category);
            }
            $this->addFlash(
                'notice',
                'Todo Updated'
            );
            return $this->redirectToRoute('todo_list');
        }
        return $this->render('todo/edit.html.twig', [
            'todo' => $todo, 'form' => $form->createView(),
            'all_categories' => $allCategories,
            'categories' => $existingCategories
        ]);
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        return $this->render('todo/details.html.twig', ['todo' => $todo]);
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
            'Todo Removed'
        );
        return $this->redirectToRoute('todo_list');
    }


    /**
     * @param string|Todo $todo
     * @param string|Category $category
     **/
    public function addTodoToCategory($todo, $category)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_string($todo)) {
            $todo = $this->getDoctrine()->getRepository(Todo::class)->findOneBy(['name' => $todo]);
        } elseif (is_numeric($todo)) {
            $todo = $this->getDoctrine()->getRepository(Todo::class)->find($todo);
        }
        if (is_string($category)) {
            $categoryInstance = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $category]);
            if (is_null($categoryInstance)) {
                $categoryInstance = new Category();
                $categoryInstance->setName($category);
                error_log('create new category: '.$categoryInstance->getName());

                $now = new \DateTime('now');
                $categoryInstance->setCreateDate($now);
                $categoryInstance->setUpdateDate($now);
                $em->persist($categoryInstance);

            } else {
                error_log('using existing category: '.$categoryInstance->getName());

            }
            $category = $categoryInstance;

        }

        $todoCategorisations = $todo->getCategorisations();
        $categoryCategorisations = $category->getCategorisations();
        if (!is_null($todoCategorisations) && !is_null($categoryCategorisations)) {
            $matches = array_intersect($categoryCategorisations->getKeys(), $todoCategorisations->getKeys());
        } else {
            $matches = [];
        }

        if (count($matches) == 0){
            $categorisation = new Categorisation();
            $categorisation->setUpdateDate(new \DateTime());
            $categorisation->setTodo($todo)->setCategory($category);
            $em->persist($categorisation);

        }

        $em->flush();



    }

    /**
     * @param Todo $todo
     * @param string $category
     */
    private function removeTodoFromCategory($todo, $category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Category $categoryInstance */
        $categoryInstance = $em->getRepository(Category::class)->findOneBy(['name' => $category]);
        $categorisationInstances = $categoryInstance->getCategorisations()->toArray();
        foreach ($categorisationInstances as $categorisationInstance) {
            $todo->removeCategorisation($categorisationInstance);
            $em->remove($categorisationInstance);
        }
        $em->flush();
    }
}
