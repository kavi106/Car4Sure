<?php

namespace App\Controller;
 
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Project;
use App\Entity\Coverage;
use App\Entity\Vehicle;
use App\Entity\Address;
use App\Entity\User;
use App\Entity\Policy;
use App\Entity\Person;

use App\Dto\UserDto;
use App\Dto\CoverageDto;
use App\Dto\DriverDto;
use App\Dto\PolicyDto;
use App\Dto\VehicleDto;
 
#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    // ############ Start User ########### //
    // #[Route('/user', name: 'user_list', methods:['get'] )]
    // public function list_user(ManagerRegistry $doctrine): JsonResponse
    // {
    //     $userList = $doctrine
    //         ->getRepository(User::class)
    //         ->findAll();
    
    //     $data = [];
    
    //     foreach ($userList as $user) {
    //         $data[] = [
    //             'id' => $user->getId(),
    //             'username' => $user->getUsername(),
    //             'roles' => $user->getRoles(),
    //         ];
    //     }
    
    //     return $this->json($data);
    // }

    #[Route('/user', name: 'user_create', methods:['post'] )]
    public function create_user(ManagerRegistry $doctrine, #[MapRequestPayload] UserDto $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(["username"=>$request->username]);
   
        if ($existingUser) {
            return $this->json(['code' => 401, 'message' => 'User already exist !'], 404);
        }

        $user = new User();
        $user->setUsername($request->username);
        $user->setRoles($user->getRoles());
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->password
        );
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $data =  [
            'username' => $user->getUsername(),
            'role' => $user->getRoles(),
            'password' => $user->getPassword(),
        ];
           
        return $this->json($data);
    }
    // ############ End User ########### //

    // ############ Start Address ########### //
    // #[Route('/address', name: 'address_create', methods:['post'] )]
    // public function create_address(ManagerRegistry $doctrine, Request $request): JsonResponse
    // {
    //     $entityManager = $doctrine->getManager();
   
    //     $address = new Address();
    //     $address->setStreet($request->request->get('street'));
    //     $address->setCity($request->request->get('city'));
    //     $address->setState($request->request->get('state'));
    //     $address->setZip($request->request->get('zip'));
   
    //     $entityManager->persist($address);
    //     $entityManager->flush();
   
    //     $data =  [
    //         'id' => $address->getId(),
    //         'street' => $address->getStreet(),
    //         'city' => $address->getCity(),
    //         'state' => $address->getState(),
    //         'zip' => $address->getZip(),
    //     ];
           
    //     return $this->json($data);
    // }
    // #[Route('/address', name: 'address_list', methods:['get'] )]
    // public function list_address(ManagerRegistry $doctrine): JsonResponse
    // {
    //     $addresses = $doctrine
    //         ->getRepository(Address::class)
    //         ->findAll();
   
    //     $data = [];
   
    //     foreach ($addresses as $address) {
    //        $data[] = [
    //         'id' => $address->getId(),
    //         'street' => $address->getStreet(),
    //         'city' => $address->getCity(),
    //         'state' => $address->getState(),
    //         'zip' => $address->getZip(),
    //        ];
    //     }
   
    //     return $this->json($data);
    // }

    #[Route('/coverage', name: 'coverage_create', methods:['post', 'put', 'patch'] )]
    public function create_coverage(ManagerRegistry $doctrine, #[MapRequestPayload] CoverageDto $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $entityManager = $doctrine->getManager();
    
        $user = $tokenStorage->getToken()->getUser();

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $request->policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        $vehicle = $doctrine
            ->getRepository(Vehicle::class)
            ->find($request->vehicleId);

        if (!$vehicle) {
            return $this->json(['code' => 404, 'message' => 'No vehicle found for id '.$id], 404);
        }

        if ($request->id > 0)
        {
            $coverage = $doctrine
                ->getRepository(Coverage::class)
                ->find($request->id);
   
            if (!$coverage) {
                return $this->json(['code' => 404, 'message' => 'No coverage found for id '.$id], 404);
            }
        }
        else
        {
            $coverage = new Coverage();
        }

        $coverage->setType($request->type);
        $coverage->setCoverageLimit($request->limit);
        $coverage->setDeductible($request->deductible);
        $coverage->setVehicle($vehicle);
   
        $entityManager->persist($coverage);
        $entityManager->flush();
   
        $data =  [
            'id' => $coverage->getId(),
            'type' => $coverage->getType(),
            'limit' => $coverage->getCoverageLimit(),
            'deductible' => $coverage->getDeductible(),
        ];
           
        return $this->json($data);
    }

    #[Route('/vehicle', name: 'vehicle_add_edit', methods:['post', 'put', 'patch'] )]
    public function add_edit_vehicle(ManagerRegistry $doctrine, #[MapRequestPayload] VehicleDto $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $entityManager = $doctrine->getManager();
    
        $user = $tokenStorage->getToken()->getUser();

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $request->policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        if ($request->id > 0)
        {
            $vehicle = $doctrine
                ->getRepository(Vehicle::class)
                ->find($request->id);
   
            if (!$vehicle) {
                return $this->json(['code' => 404, 'message' => 'No vehicle found for id '.$id], 404);
            }

            $address = $vehicle->getGaragingAddress();
        }
        else
        {
            $vehicle = new Vehicle();
            $policy->addVehicle($vehicle);
            $address = new Address();
            $vehicle->setGaragingAddress($address);
        }
   
        $address->setStreet($request->street);
        $address->setCity($request->city);
        $address->setState($request->state);
        $address->setZip($request->zip);

        $vehicle->setYear($request->year);
        $vehicle->setMake($request->make);
        $vehicle->setModel($request->model);
        $vehicle->setVin($request->vin);
        $vehicle->setUsage($request->usage);
        $vehicle->setPrimaryUse($request->primaryUse);
        $vehicle->setAnnualMileage($request->annualMileage);
        $vehicle->setOwnership($request->ownership);

        $entityManager->persist($address);
        $entityManager->flush();

        $entityManager->persist($vehicle);
        $entityManager->flush();

        $entityManager->persist($policy);
        $entityManager->flush();
   
        $data =  [
            'id' => $vehicle->getId(),
            'year' => $vehicle->getYear(),
            'make' => $vehicle->getMake(),
            'model' => $vehicle->getModel(),
            'vin' => $vehicle->getVin(),
            'usage' => $vehicle->getUsage(),
            'primaryUse' => $vehicle->getPrimaryUse(),
            'annualMileage' => $vehicle->getAnnualMileage(),
            'ownership' => $vehicle->getOwnership(),
            'garagingAddress' => [
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'zip' => $address->getZip(),
            ],
        ];
           
        return $this->json($data);
    }

    #[Route('/policy', name: 'policy_create', methods:['post','put', 'patch'] )]
    public function create_policy(ManagerRegistry $doctrine, #[MapRequestPayload] PolicyDto $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $entityManager = $doctrine->getManager();
    
        $user = $tokenStorage->getToken()->getUser();

        if ($request->id > 0)
        {
            $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $request->id]);
   
            if (!$policy) {
                return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
            }

            $person = $policy->getPolicyHolder();
            $address = $person->getAddress();
        }
        else
        {
            $policy = new Policy();
            $address = new Address();
            $person = new Person();
            $person->setAddress($address);
            $policy->setUser($user);
            $policy->setPolicyHolder($person);
        }

        $address->setStreet($request->street);
        $address->setCity($request->city);
        $address->setState($request->state);
        $address->setZip($request->zip);
        $entityManager->persist($address);
        $entityManager->flush();

        $person->setFirstName($request->firstName);
        $person->setLastName($request->lastName);
        
        $entityManager->persist($person);
        $entityManager->flush();

        $policy->setPolicyStatus($request->policyStatus);
        $policy->setPolicyType($request->policyType);
        $policy->setPolicyEffectiveDate($request->policyEffectiveDate != null ? \DateTime::createFromFormat('Y-m-d', $request->policyEffectiveDate) : null);
        $policy->setPolicyExpirationDate($request->policyExpirationDate != null ? \DateTime::createFromFormat('Y-m-d', $request->policyExpirationDate) : null);
    
        $entityManager->persist($policy);
        $entityManager->flush();
    
        $data =  [
            'id' => $policy->getId(),
            'policyStatus' => $policy->getPolicyStatus(),
            'policyType' => $policy->getPolicyType(),
            'policyEffectiveDate' => $policy->getPolicyEffectiveDate(),
            'policyExpirationDate' => $policy->getPolicyExpirationDate(),
            'policyHolder' => [
                'id' => $person->getId(),
                'firstName' => $person->getFirstName(),
                'lastName' => $person->getLastName(),
                'address' => [
                    'id' => $address?->getId(),
                    'street' => $address?->getStreet(),
                    'city' => $address?->getCity(),
                    'state' => $address?->getState(),
                    'zip' => $address?->getZip(),
                ],
            ],
        ];
            
        return $this->json($data);
    }

    #[Route('/driver', name: 'driver_add_edit', methods:['post','put', 'patch'] )]
    public function driver_add_edit(ManagerRegistry $doctrine, #[MapRequestPayload] DriverDto $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $entityManager = $doctrine->getManager();
    
        $user = $tokenStorage->getToken()->getUser();

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $request->policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        if ($request->id > 0)
        {
            $driver = $doctrine
                ->getRepository(Person::class)
                ->find($request->id);
   
            if (!$driver) {
                return $this->json(['code' => 404, 'message' => 'No driver found for id '.$id], 404);
            }
        }
        else
        {
            $driver = new Person();
            $policy->addDriver($driver);
        }

        $driver->setFirstName($request->firstName);
        $driver->setLastName($request->lastName);
        $driver->setDateOfBirth($request->dateOfBirth != null ? \DateTime::createFromFormat('Y-m-d', $request->dateOfBirth) : null);
        $driver->setGender($request->gender);
        $driver->setMaritalStatus($request->maritalStatus);
        $driver->setLicenseNumber($request->licenseNumber);
        $driver->setLicenseState($request->licenseState);
        $driver->setLicenseStatus($request->licenseStatus);
        $driver->setLicenseEffectiveDate($request->licenseEffectiveDate != null ? \DateTime::createFromFormat('Y-m-d', $request->licenseEffectiveDate) : null);
        $driver->setLicenseExpirationDate($request->licenseExpirationDate != null ? \DateTime::createFromFormat('Y-m-d', $request->licenseExpirationDate) : null);
        $driver->setLicenseClass($request->licenseClass);
        $entityManager->persist($driver);
        $entityManager->flush();

        $entityManager->persist($policy);
        $entityManager->flush();
   
        $data = [
            'id' => $driver->getId(),
            'firstName' => $driver->getFirstName(),
            'lastName' => $driver->getLastName(),
            'dob' => $driver->getDateOfBirth(),
            'maritalStatus' => $driver->getMaritalStatus(),
            'licenseNumber' => $driver->getLicenseNumber(),
            'licenseState' => $driver->getLicenseState(),
            'licenseStatus' => $driver->getLicenseStatus(),
            'licenseEffectiveDate' => $driver->getLicenseEffectiveDate(),
            'licenseExpirationDate' => $driver->getLicenseExpirationDate(),
            'licenseClass' => $driver->getLicenseClass(),
        ];
   
        return $this->json($data);
    }







    #[Route('/coverage/{policyId}/{vehicleId}/{id}', name: 'coverage_delete', methods:['delete'] )]
    public function delete_coverage(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $policyId, int $vehicleId, int $id): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();
        $entityManager = $doctrine->getManager();

        $policy = $entityManager
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$policyId], 404);
        }

        $vehicle = $entityManager
            ->getRepository(Vehicle::class)
            ->find($vehicleId);

        if (!$vehicle) {
            return $this->json(['code' => 404, 'message' => 'No vehicle found for id '.$id], 404);
        }

        $coverage = $entityManager
            ->getRepository(Coverage::class)
            ->find($id);

        if (!$coverage) {
            return $this->json(['code' => 404, 'message' => 'No coverage found for id '.$id], 404);
        }
   
        $vehicle->removeCoverage($coverage);
        $entityManager->persist($vehicle);
        $entityManager->flush();
   
        return $this->json(['code' => 200, 'message' => 'Deleted a coverage successfully with id ' . $id], 200);
    }

    #[Route('/coverages/{policyId}/{vehicleId}', name: 'coverages_list', methods:['get'] )]
    public function list_coverages(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $policyId, int $vehicleId): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        $vehicles = $doctrine
            ->getRepository(Vehicle::class)
            ->find($vehicleId);

        if (!$vehicles) {
            return $this->json(['code' => 404, 'message' => 'No vehicle found for id '.$id], 404);
        }
   
        $data = [];

        $coverages = $vehicles->getCoverages();
   
        foreach ($coverages as $coverage) {
            $data[] = [
                'id' => $coverage->getId(),
                'type' => $coverage->getType(),
                'limit' => $coverage->getCoverageLimit(),
                'deductible' => $coverage->getDeductible(),
            ];
        }
   
        return $this->json($data);
    }

    #[Route('/vehicle/{policyId}/{id}', name: 'vehicle_delete', methods:['delete'] )]
    public function delete_vehicle(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $policyId, int $id): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();
        $entityManager = $doctrine->getManager();

        $policy = $entityManager
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$policyId], 404);
        }

        $vehicle = $entityManager
            ->getRepository(Vehicle::class)
            ->find($id);

        if (!$vehicle) {
            return $this->json(['code' => 404, 'message' => 'No vehicle found for id '.$id], 404);
        }
   
        $policy->removeVehicle($vehicle);
        $entityManager->persist($policy);
        $entityManager->flush();
   
        return $this->json(['code' => 200, 'message' => 'Deleted a vehicle successfully with id ' . $id], 200);
    }

    // #[Route('/person', name: 'person_create', methods:['post'] )]
    // public function create_person(ManagerRegistry $doctrine, Request $request): JsonResponse
    // {
    //     $entityManager = $doctrine->getManager();
   
    //     $person = new Person();
    //     $person->setFirstName($request->request->get('firstName'));
    //     $person->setLastName($request->request->get('lastName'));
    //     $person->setDateOfBirth($request->request->get('dob', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('dob')) : null);
    //     $person->setGender($request->request->get('gender', ''));
    //     $person->setMaritalStatus($request->request->get('maritalStatus', ''));
    //     $person->setLicenseNumber($request->request->get('licenseNumber', 0));
    //     $person->setLicenseState($request->request->get('licenseState', ''));
    //     $person->setLicenseStatus($request->request->get('licenseStatus', ''));
    //     $person->setLicenseEffectiveDate($request->request->get('licenseEffectiveDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseEffectiveDate')) : null);
    //     $person->setLicenseExpirationDate($request->request->get('licenseExpirationDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseExpirationDate')) : null);
    //     $person->setLicenseClass($request->request->get('licenseClass', ''));

    //     $entityManager->persist($person);
    //     $entityManager->flush();
   
    //     $address = $person->getAddress();

    //     $data =  [
    //         'id' => $person->getId(),
    //         'firstName' => $person->getFirstName(),
    //         'lastName' => $person->getLastName(),
    //         'address' => [
    //             'id' => $address?->getId(),
    //             'street' => $address?->getStreet(),
    //             'city' => $address?->getCity(),
    //             'state' => $address?->getState(),
    //             'zip' => $address?->getZip(),
    //         ],
    //         'dob' => $person->getDateOfBirth(),
    //         'maritalStatus' => $person->getMaritalStatus(),
    //         'licenseNumber' => $person->getLicenseNumber(),
    //         'licenseState' => $person->getLicenseState(),
    //         'licenseStatus' => $person->getLicenseStatus(),
    //         'licenseEffectiveDate' => $person->getLicenseEffectiveDate(),
    //         'licenseExpirationDate' => $person->getLicenseExpirationDate(),
    //         'licenseClass' => $person->getLicenseClass(),
    //     ];
           
    //     return $this->json($data);
    // }

    #[Route('/person/{id}', name: 'person_show', methods:['get'] )]
    public function show_person(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $person = $doctrine->getRepository(Person::class)->find($id);
   
        if (!$person) {
   
            return $this->json('No person found for id ' . $id, 404);
        }
   
        $address = $person->getAddress();

        $data =  [
            'id' => $person->getId(),
            'firstName' => $person->getFirstName(),
            'lastName' => $person->getLastName(),
            'address' => [
                'id' => $address?->getId(),
                'street' => $address?->getStreet(),
                'city' => $address?->getCity(),
                'state' => $address?->getState(),
                'zip' => $address?->getZip(),
            ],
            'dob' => $person->getDateOfBirth(),
            'maritalStatus' => $person->getMaritalStatus(),
            'licenseNumber' => $person->getLicenseNumber(),
            'licenseState' => $person->getLicenseState(),
            'licenseStatus' => $person->getLicenseStatus(),
            'licenseEffectiveDate' => $person->getLicenseEffectiveDate(),
            'licenseExpirationDate' => $person->getLicenseExpirationDate(),
            'licenseClass' => $person->getLicenseClass(),
        ];
           
        return $this->json($data);
    }

    #[Route('/person', name: 'person_list', methods:['get'] )]
    public function list_person(ManagerRegistry $doctrine): JsonResponse
    {
        $personList = $doctrine
            ->getRepository(Person::class)
            ->findAll();
   
        $data = [];
   
        foreach ($personList as $person) {
            $address = $person->getAddress();
            $data[] = [
                'id' => $person->getId(),
                'firstName' => $person->getFirstName(),
                'lastName' => $person->getLastName(),
                'address' => [
                    'id' => $address?->getId(),
                    'street' => $address?->getStreet(),
                    'city' => $address?->getCity(),
                    'state' => $address?->getState(),
                    'zip' => $address?->getZip(),
                ],
                'dob' => $person->getDateOfBirth(),
                'maritalStatus' => $person->getMaritalStatus(),
                'licenseNumber' => $person->getLicenseNumber(),
                'licenseState' => $person->getLicenseState(),
                'licenseStatus' => $person->getLicenseStatus(),
                'licenseEffectiveDate' => $person->getLicenseEffectiveDate(),
                'licenseExpirationDate' => $person->getLicenseExpirationDate(),
                'licenseClass' => $person->getLicenseClass(),
            ];
        }
   
        return $this->json($data);
    }

    // #[Route('/person/{id}', name: 'person_update', methods:['put', 'patch'] )]
    // public function update_person(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    // {
    //     $entityManager = $doctrine->getManager();
    //     $person = $entityManager->getRepository(Person::class)->find($id);
   
    //     if (!$person) {
    //         return $this->json('No person found for id ' . $id, 404);
    //     }
   
    //     $person->setFirstName($request->request->get('firstName'));
    //     $person->setLastName($request->request->get('lastName'));
    //     $person->setDateOfBirth($request->request->get('dob', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('dob')) : null);
    //     $person->setGender($request->request->get('gender', ''));
    //     $person->setMaritalStatus($request->request->get('maritalStatus', ''));
    //     $person->setLicenseNumber($request->request->get('licenseNumber', 0));
    //     $person->setLicenseState($request->request->get('licenseState', ''));
    //     $person->setLicenseStatus($request->request->get('licenseStatus', ''));
    //     $person->setLicenseEffectiveDate($request->request->get('licenseEffectiveDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseEffectiveDate')) : null);
    //     $person->setLicenseExpirationDate($request->request->get('licenseExpirationDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseExpirationDate')) : null);
    //     $person->setLicenseClass($request->request->get('licenseClass', ''));
    //     $entityManager->flush();
   
    //     $address = $person->getAddress();

    //     $data =  [
    //         'id' => $person->getId(),
    //         'firstName' => $person->getFirstName(),
    //         'lastName' => $person->getLastName(),
    //         'address' => [
    //             'id' => $address?->getId(),
    //             'street' => $address?->getStreet(),
    //             'city' => $address?->getCity(),
    //             'state' => $address?->getState(),
    //             'zip' => $address?->getZip(),
    //         ],
    //         'dob' => $person->getDateOfBirth(),
    //         'maritalStatus' => $person->getMaritalStatus(),
    //         'licenseNumber' => $person->getLicenseNumber(),
    //         'licenseState' => $person->getLicenseState(),
    //         'licenseStatus' => $person->getLicenseStatus(),
    //         'licenseEffectiveDate' => $person->getLicenseEffectiveDate(),
    //         'licenseExpirationDate' => $person->getLicenseExpirationDate(),
    //         'licenseClass' => $person->getLicenseClass(),
    //     ];
           
    //     return $this->json($data);
    // }

    // #[Route('/person/{id}/linkaddress/{aid}', name: 'person_link_address', methods:['put', 'patch'] )]
    // public function link_address_person(ManagerRegistry $doctrine, Request $request, int $id, int $aid): JsonResponse
    // {
    //     $entityManager = $doctrine->getManager();
    //     $person = $entityManager->getRepository(Person::class)->find($id);
   
    //     if (!$person) {
    //         return $this->json('No person found for id ' . $id, 404);
    //     }

    //     $address = $entityManager->getRepository(Address::class)->find($aid);

    //     if (!$address) {
    //         return $this->json('No address found for id ' . $aid, 404);
    //     }
   
    //     $person->setAddress($address);
    //     $entityManager->flush();
   
    //     $data =  [
    //         'id' => $person->getId(),
    //         'firstName' => $person->getFirstName(),
    //         'lastName' => $person->getLastName(),
    //         'address' => [
    //             'id' => $address->getId(),
    //             'street' => $address->getStreet(),
    //             'city' => $address->getCity(),
    //             'state' => $address->getState(),
    //             'zip' => $address->getZip(),
    //         ],
    //     ];
           
    //     return $this->json($data);
    // }

    // #[Route('/person/{id}/unlinkaddress', name: 'person_unlink_address', methods:['put', 'patch'] )]
    // public function unlink_address_person(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    // {
    //     $entityManager = $doctrine->getManager();
    //     $person = $entityManager->getRepository(Person::class)->find($id);
   
    //     if (!$person) {
    //         return $this->json('No person found for id ' . $id, 404);
    //     }

    //     $person->setAddress(null);
    //     $entityManager->flush();

    //     $address = $person->getAddress();
   
    //     $data =  [
    //         'id' => $person->getId(),
    //         'firstName' => $person->getFirstName(),
    //         'lastName' => $person->getLastName(),
    //         'address' => [
    //             'id' => $address?->getId(),
    //             'street' => $address?->getStreet(),
    //             'city' => $address?->getCity(),
    //             'state' => $address?->getState(),
    //             'zip' => $address?->getZip(),
    //         ],
    //     ];
           
    //     return $this->json($data);
    // }

    #[Route('/policies', name: 'policy_list', methods:['get'] )]
    public function list_policies(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();

        $policies = $doctrine
            ->getRepository(Policy::class)
            ->findBy(['user' => $user->getId()], ['id' => 'ASC']);
   
        $data = [];
   
        foreach ($policies as $policy) {
            $policyHolder = $policy->getPolicyHolder();
            $policyHolderAddress = $policyHolder->getAddress();
            $data[] = [
                'id' => $policy->getId(),
                'policyStatus' => $policy->getPolicyStatus(),
                'policyType' => $policy->getPolicyType(),
                'policyEffectiveDate' => $policy->getPolicyEffectiveDate(),
                'policyExpirationDate' => $policy->getPolicyExpirationDate(),
                'policyHolder' => [
                    'id' => $policyHolder->getId(),
                    'firstName' => $policyHolder->getFirstName(),
                    'lastName' => $policyHolder->getLastName(),
                    'address' => [
                        'id' => $policyHolderAddress?->getId(),
                        'street' => $policyHolderAddress?->getStreet(),
                        'city' => $policyHolderAddress?->getCity(),
                        'state' => $policyHolderAddress?->getState(),
                        'zip' => $policyHolderAddress?->getZip(),
                    ],
                ],
            ];
        }
   
        return $this->json($data);
    }

    #[Route('/policy/{id}', name: 'policy_get', methods:['get'] )]
    public function get_policy(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $id): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();

        $data = [];

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $id]);
   
        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        $person = $policy->getPolicyHolder();
        $address = $person->getAddress();

        $data =  [
            'id' => $policy->getId(),
            'policyStatus' => $policy->getPolicyStatus(),
            'policyType' => $policy->getPolicyType(),
            'policyEffectiveDate' => $policy->getPolicyEffectiveDate(),
            'policyExpirationDate' => $policy->getPolicyExpirationDate(),
            'policyHolder' => [
                'id' => $person->getId(),
                'firstName' => $person->getFirstName(),
                'lastName' => $person->getLastName(),
                'address' => [
                    'id' => $address?->getId(),
                    'street' => $address?->getStreet(),
                    'city' => $address?->getCity(),
                    'state' => $address?->getState(),
                    'zip' => $address?->getZip(),
                ],
            ],
        ];

        return $this->json($data);
    }

    #[Route('/policy/{id}', name: 'policy_delete', methods:['delete'] )]
    public function delete_policy(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $id): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();
        $entityManager = $doctrine->getManager();

        $policy = $entityManager
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $id]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }
   
        $entityManager->remove($policy);
        $entityManager->flush();
   
        return $this->json(['code' => 200, 'message' => 'Deleted a policy successfully with id ' . $id], 200);
    }

    #[Route('/drivers/{policyId}', name: 'drivers_list', methods:['get'] )]
    public function list_drivers(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $policyId): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        $drivers = $policy->getDrivers();
   
        $data = [];
   
        foreach ($drivers as $driver) {
            $data[] = [
                'id' => $driver->getId(),
                'firstName' => $driver->getFirstName(),
                'lastName' => $driver->getLastName(),
                'dob' => $driver->getDateOfBirth(),
                'gender' => $driver->getGender(),
                'maritalStatus' => $driver->getMaritalStatus(),
                'licenseNumber' => $driver->getLicenseNumber(),
                'licenseState' => $driver->getLicenseState(),
                'licenseStatus' => $driver->getLicenseStatus(),
                'licenseEffectiveDate' => $driver->getLicenseEffectiveDate(),
                'licenseExpirationDate' => $driver->getLicenseExpirationDate(),
                'licenseClass' => $driver->getLicenseClass(),
            ];
        }
   
        return $this->json($data);
    }

    #[Route('/driver/{policyId}/{id}', name: 'driver_delete', methods:['delete'] )]
    public function delete_driver(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $policyId, int $id): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();
        $entityManager = $doctrine->getManager();

        $policy = $entityManager
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$policyId], 404);
        }

        $driver = $entityManager
            ->getRepository(Person::class)
            ->find($id);

        if (!$driver) {
            return $this->json(['code' => 404, 'message' => 'No driver found for id '.$id], 404);
        }
   
        $policy->removeDriver($driver);
        $entityManager->persist($policy);
        $entityManager->flush();
   
        return $this->json(['code' => 200, 'message' => 'Deleted a driver successfully with id ' . $id], 200);
    }

    #[Route('/vehicles/{policyId}', name: 'vehicles_list', methods:['get'] )]
    public function list_vehicles(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage, int $policyId): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();

        $policy = $doctrine
            ->getRepository(Policy::class)
            ->findOneBy(['user' => $user->getId(), 'id' => $policyId]);

        if (!$policy) {
            return $this->json(['code' => 404, 'message' => 'No policy found for id '.$id], 404);
        }

        $vehicles = $policy->getVehicles();
   
        $data = [];
   
        foreach ($vehicles as $vehicle) {
            $address = $vehicle->getGaragingAddress();
            $data[] = [
                'id' => $vehicle->getId(),
                'year' => $vehicle->getYear(),
                'make' => $vehicle->getMake(),
                'model' => $vehicle->getModel(),
                'vin' => $vehicle->getVin(),
                'usage' => $vehicle->getUsage(),
                'primaryUse' => $vehicle->getPrimaryUse(),
                'annualMileage' => $vehicle->getAnnualMileage(),
                'ownership' => $vehicle->getOwnership(),
                'garagingAddress' => [
                    'street' => $address->getStreet(),
                    'city' => $address->getCity(),
                    'state' => $address->getState(),
                    'zip' => $address->getZip(),
                ],
            ];
        }
   
        return $this->json($data);
    }

}


