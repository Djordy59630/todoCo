<?php

namespace App\Controller;

use DateTime;
use App\Entity\Task;
use App\Form\TaskType;
use DateTimeImmutable;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/ended', name: 'app_task_ended', methods: ['GET'])]
    public function ended(TaskRepository $taskRepository): Response
    {
        return $this->render('task/ended.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskRepository $taskRepository): Response
    {
       
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $task->setCreatedAt(new DateTimeImmutable());
            $task->setIsDone(false);

            if ( $form->get('user')->getData() == null ) 
            {
                $task->setUser($user);
            }
            
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        $createdBy = $task->getUser();

        if ($user == $createdBy or $this->isGranted('ROLE_ADMIN'))
        {

            if ($task->isDone() == false )
            {
                $form = $this->createForm(TaskType::class, $task);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    $taskRepository->save($task, true);
        
                    return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
                }
        
                return $this->render('task/edit.html.twig', [
                    'task' => $task,
                    'form' => $form,
                ]);
            }
            else
            {
                $this->addFlash('danger', "Erreur : Impossible d'éditer une tâche terminée");

                return $this->render('task/index.html.twig', [
                'tasks' => $taskRepository->findAll(),
                ]);
            }
        }
        else
        {
            $this->addFlash('danger', "Vous n'avez pas les autorisations pour editer cette tâche");

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }
      
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete( Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        $createdBy = $task->getUser();

        if ($user == $createdBy or $this->isGranted('ROLE_ADMIN'))
        {
            if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
                $taskRepository->remove($task, true);
            }
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }
        else
        {
            $this->addFlash('danger', "Vous n'avez pas les autorisations pour supprimer cette tâche");

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }
       
    }

    #[Route('/{id}/toggle', name: 'app_task_toggle', methods: ['GET', 'POST'])]
    public function toggleTaskAction(Task $task, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        $createdBy = $task->getUser();

        if ($user == $createdBy or $this->isGranted('ROLE_ADMIN'))
        {

            $task->toggle(!$task->isDone());

            $taskRepository->save($task, true);

            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }
        else
        {
            $this->addFlash('danger', "Vous n'avez pas les autorisations pour changer l'état de cette tâche");

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
