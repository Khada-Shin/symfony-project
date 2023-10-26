<?php

namespace App\Controller;
use App\Form\BookType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook', name: 'addB')]
    public function addBook(ManagerRegistry $managerRegistry)
    {
        $Book = new  Book();
        $Book->setRef("123Mr4569345");
        $Book->setTitle("Jotaro");
        $Book->setPublished(True);
        #$Book->setPublicationDate("12/10/2023");
        #$em = $this->getDoctrine()->getManager();
        $em= $managerRegistry->getManager();
        $em->persist($Book);
        $em->flush();
        return $this->redirectToRoute("msg.html.twig");
    }
    #[Route('/BooksList', name: 'Book_list')]
    public function list(BookRepository $repository)
    {
        $Book= $repository->findAll();
        return $this->render("Book/ListBook.html.twig",
            array("tabBook"=>$Book));
    }
    
    #[Route('/addbook1', name: 'add_book')]
    public function addbook1(Request $request, ManagerRegistry $managerRegistry)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute("Book_list");
        }
        return $this->render("book/add.html.twig", array("formulairebook" => $form->createView()));
    }

    #[Route('/removeBook/{ref}', name: 'book_remove')]
    public function deleteBook($ref,BookRepository $repository,ManagerRegistry $managerRegistry)
    {
        $book= $repository->find($ref);
        $em= $managerRegistry->getManager();
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute("Book_list");

    }

    #[Route('/editBook/{ref}', name: 'editBook')]
    public function edit(BookRepository $bookRepository, $ref, Request $request, ManagerRegistry $managerRegistry)
    {
        $book = $bookRepository->find($ref);

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->flush();

            return $this->redirectToRoute('Book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/chercheBook/{author_id}', name: 'cherche_Book')]
    public function chercherTriBooksParAuteur(BookRepository $bookRepository, Request $request,$author_id)
    {
    
        $book = $bookRepository->findBooksByAuthor($author_id);
    
        return $this->render('book/listBook.html.twig', [
            'tabBook' => $book,
        ]);
    }
    
    

}
