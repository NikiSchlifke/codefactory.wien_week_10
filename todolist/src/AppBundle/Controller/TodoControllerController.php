<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class TodoControllerController extends Controller
{
    /**
     * @Route("/todos", name="todo_list")
     * @param Request $request
     */
    public function listAction(Request $request)
    {
        return $this->render('todo/index.html.twig');
    }

    /**
     * @param Request $request
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        return $this->render('todo/create.html.twig');
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        return $this->render('todo/details.html.twig');
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        return $this->render('todo/edit.html.twig');
    }
}
