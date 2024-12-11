<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();
        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employee/new', name: 'app_employee_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee');
        }

        return $this->render('employee/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/employee/{id}', name: 'app_employee_show')]
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/employee/{id}/edit', name: 'app_employee_edit')]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_employee');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
        ]);
    }

    #[Route('/employee/{id}/delete', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employee');
    }
}
