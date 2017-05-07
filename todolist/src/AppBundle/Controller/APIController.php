<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categorisation;
use AppBundle\Entity\Category;
use AppBundle\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class APIController extends Controller
{
    /**
     * @Route("/addTodoToCategory")
     */
    public function addTodoToCategoryAction(Request $request)
    {
        $todo = $request->request->get('todo');
        $category = $request->request->get('category');
        try {
            static::addTodoToCategory($todo, $category);
        } catch (\InvalidArgumentException $exception) {
            return (new Response(json_encode(['message' => $exception->getMessage()]), 409));
        }
        return (new Response(json_encode(['message' => "Added $todo to category $category."]), 201));
    }
    /**
     * @param string|Todo $todo
     * @param string|Category $category
     *      */
    public function addTodoToCategory($todo, $category)
    {
        if (is_string($todo)) {
            $todo = $this->getDoctrine()->getRepository(Todo::class)->findOneBy(['name' => $todo]);
        } elseif (is_numeric($todo)) {
            $todo = $this->getDoctrine()->getRepository(Todo::class)->findOneBy(['id' => $todo]);
        }
        if (is_string($category)) {
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $category]);
        }
        if (!empty(array_intersect(
            $todo->getCategorisations()->getKeys(),
            $category->getCategorisations()->getKeys()))
        ) {
            throw new \InvalidArgumentException('Todo already belongs to this category.');
        }
        $categorisation = new Categorisation();
        $categorisation->setTodo($todo)->setCategory($category);
        $this->getDoctrine()->getManager()->persist($categorisation);

    }
}
