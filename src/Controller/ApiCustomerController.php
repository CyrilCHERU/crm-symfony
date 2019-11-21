<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/old")
 */
class ApiCustomerController extends AbstractController
{
    /**
     * @Route("/api/customers", name="api_customer", methods={"GET"})
     */
    public function index(CustomerRepository $customerRepository, SerializerInterface $serializer, Request $request)
    {
        $format = $request->query->get('format', 'json');

        $customers = $customerRepository->findAll();

        $json = $serializer->serialize($customers, $format, [
            'groups' => ['customers:read']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/customers", name="api_customer_create", methods={"POST"})
     *
     * @return void
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em)
    {

        $json = $request->getContent();

        try {
            $customer = $serializer->deserialize($json, Customer::class, 'json');

            $violations = $validator->validate($customer);

            if ($violations->count() > 0) {

                $violationsJson = $serializer->serialize($violations, 'json');

                return new JsonResponse($violationsJson, 400, [], true);
            }

            $em->persist($customer);
            $em->flush();

            $json = $serializer->serialize($customer, "json", [
                'groups' => ['customers:read']
            ]);

            return new JsonResponse($json, 200, [], true);
        } catch (Exception $e) {
            throw new Exception("Un problème est survenu, votre JSON n'est pas valide");
        }
    }

    /**
     * @Route("/api/customers/{id<\d+>}", name="api_customers_show", methods={"GET"})
     *
     * @return void
     */
    public function show(Customer $customer, SerializerInterface $serializer)
    {
        $json = $serializer->serialize($customer, 'json', [
            'groups' => ['customers:read']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/customers/{id<\d+>}", name="api_customers_update", methods={"PUT"})
     *
     * @return void
     */
    public function update(Customer $customer, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        // 1. Récupération du client mis a jour
        $json = $request->getContent();

        $updatedCustomer = $serializer->deserialize($json, Customer::class, 'json');

        //2 . Validation des données
        $violations = $validator->validate($updatedCustomer);

        if ($violations->count() > 0) {

            $violationsJson = $serializer->serialize($violations, 'json');

            return new JsonResponse($violationsJson, 400, [], true);
        }

        // Prise en compte des modifications
        $customer->setFirstName($updatedCustomer->getFirstName())
            ->setLastName($updatedCustomer->getLastName())
            ->setEmail($updatedCustomer->getEmail())
            ->setAvatar($updatedCustomer->getAvatar())
            ->setComment($updatedCustomer->getComment());

        // Mise à jour en BDD
        $em->flush();

        // Retour de la mise a jour du customer
        $json = $serializer->serialize($customer, 'json', [
            'groups' => ['customers:read']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/customers/{id<\d+>}", name="api_customer_delete", methods={"DELETE"})
     *
     * @return void
     */
    public function delete(Customer $customer, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $em->remove($customer);
        $em->flush();
        $json = $serializer->serialize($customer, 'json', [
            'groups' => ['customers:read']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/customers/{id<\d+>}/invoices", name="api_customer_invoices", methods={"GET"})
     *
     * @return void
     */
    public function invoices(Customer $customer, SerializerInterface $serializer)
    {
        $invoices = $customer->getInvoices();

        $data = [
            'customer' => $customer,
            'invoices' => $invoices
        ];

        $json = $serializer->serialize($data, 'json', [
            'groups' => ['invoices:read']
        ]);

        return new JsonResponse($json, 200, [], true);
    }
}
