<?php

namespace App\Controller;
 
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
 
#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    // ############ Start User ########### //
    #[Route('/user', name: 'user_list', methods:['get'] )]
    public function list_user(ManagerRegistry $doctrine): JsonResponse
    {
        $userList = $doctrine
            ->getRepository(User::class)
            ->findAll();
    
        $data = [];
    
        foreach ($userList as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }
    
        return $this->json($data);
    }

    #[Route('/user', name: 'user_create', methods:['post'] )]
    public function create_user(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(["username"=>$request->request->get('username')]);
   
        if ($existingUser) {
            return $this->json(['code' => 401, 'message' => 'User already exist !'], 404);
        }

        $user = new User();
        $user->setUsername($request->request->get('username'));
        $user->setRoles($user->getRoles());
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->request->get('password')
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
    #[Route('/address', name: 'address_create', methods:['post'] )]
    public function create_address(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $address = new Address();
        $address->setStreet($request->request->get('street'));
        $address->setCity($request->request->get('city'));
        $address->setState($request->request->get('state'));
        $address->setZip($request->request->get('zip'));
   
        $entityManager->persist($address);
        $entityManager->flush();
   
        $data =  [
            'id' => $address->getId(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'zip' => $address->getZip(),
        ];
           
        return $this->json($data);
    }
    #[Route('/address', name: 'address_list', methods:['get'] )]
    public function list_address(ManagerRegistry $doctrine): JsonResponse
    {
        $addresses = $doctrine
            ->getRepository(Address::class)
            ->findAll();
   
        $data = [];
   
        foreach ($addresses as $address) {
           $data[] = [
            'id' => $address->getId(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'zip' => $address->getZip(),
           ];
        }
   
        return $this->json($data);
    }
    // ############ End Address ########### //
    // ############ Start Coverage ########### //
    #[Route('/coverage', name: 'coverage_create', methods:['post'] )]
    public function create_coverage(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $vehicle = $entityManager->getRepository(Vehicle::class)->findOneById($request->request->get('vehicleId'));

        $coverage = new Coverage();
        $coverage->setType($request->request->get('type'));
        $coverage->setCoverageLimit($request->request->get('limit'));
        $coverage->setDeductible($request->request->get('deductible'));
        $coverage->setVehicle($vehicle);
   
        $entityManager->persist($coverage);
        $entityManager->flush();
   
        $data =  [
            'id' => $coverage->getId(),
            'type' => $coverage->getType(),
            'limit' => $coverage->getCoverageLimit(),
            'deductible' => $coverage->getDeductible(),
            'vehicleId' => $coverage->getVehicle()->getId(),
        ];
           
        return $this->json($data);
    }
    // ############ End Coverage ########### //
    // ############ Start Vehicle ########### //
    #[Route('/vehicle', name: 'vehicle_create', methods:['post'] )]
    public function create_vehicle(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $address = new Address();
        $address->setStreet($request->request->get('street'));
        $address->setCity($request->request->get('city'));
        $address->setState($request->request->get('state'));
        $address->setZip($request->request->get('zip'));

        $vehicle = new Vehicle();
        $vehicle->setYear($request->request->get('year'));
        $vehicle->setMake($request->request->get('make'));
        $vehicle->setModel($request->request->get('model'));
        $vehicle->setVin($request->request->get('vin'));
        $vehicle->setUsage($request->request->get('usage'));
        $vehicle->setPrimaryUse($request->request->get('primaryUse'));
        $vehicle->setAnnualMileage($request->request->get('annualMileage'));
        $vehicle->setOwnership($request->request->get('ownership'));
        $vehicle->setGaragingAddress($address);
   
        $entityManager->persist($vehicle);
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
    // ############ End Vehicle ########### //
    // ############ Start Person ########### //
    #[Route('/person', name: 'person_create', methods:['post'] )]
    public function create_person(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $person = new Person();
        $person->setFirstName($request->request->get('firstName'));
        $person->setLastName($request->request->get('lastName'));
        $person->setDateOfBirth($request->request->get('dob', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('dob')) : null);
        $person->setGender($request->request->get('gender', ''));
        $person->setMaritalStatus($request->request->get('maritalStatus', ''));
        $person->setLicenseNumber($request->request->get('licenseNumber', 0));
        $person->setLicenseState($request->request->get('licenseState', ''));
        $person->setLicenseStatus($request->request->get('licenseStatus', ''));
        $person->setLicenseEffectiveDate($request->request->get('licenseEffectiveDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseEffectiveDate')) : null);
        $person->setLicenseExpirationDate($request->request->get('licenseExpirationDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseExpirationDate')) : null);
        $person->setLicenseClass($request->request->get('licenseClass', ''));

        $entityManager->persist($person);
        $entityManager->flush();
   
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
    #[Route('/person/{id}', name: 'person_update', methods:['put', 'patch'] )]
    public function update_person(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $person = $entityManager->getRepository(Person::class)->find($id);
   
        if (!$person) {
            return $this->json('No person found for id ' . $id, 404);
        }
   
        $person->setFirstName($request->request->get('firstName'));
        $person->setLastName($request->request->get('lastName'));
        $person->setDateOfBirth($request->request->get('dob', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('dob')) : null);
        $person->setGender($request->request->get('gender', ''));
        $person->setMaritalStatus($request->request->get('maritalStatus', ''));
        $person->setLicenseNumber($request->request->get('licenseNumber', 0));
        $person->setLicenseState($request->request->get('licenseState', ''));
        $person->setLicenseStatus($request->request->get('licenseStatus', ''));
        $person->setLicenseEffectiveDate($request->request->get('licenseEffectiveDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseEffectiveDate')) : null);
        $person->setLicenseExpirationDate($request->request->get('licenseExpirationDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('licenseExpirationDate')) : null);
        $person->setLicenseClass($request->request->get('licenseClass', ''));
        $entityManager->flush();
   
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
    #[Route('/person/{id}/linkaddress/{aid}', name: 'person_link_address', methods:['put', 'patch'] )]
    public function link_address_person(ManagerRegistry $doctrine, Request $request, int $id, int $aid): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $person = $entityManager->getRepository(Person::class)->find($id);
   
        if (!$person) {
            return $this->json('No person found for id ' . $id, 404);
        }

        $address = $entityManager->getRepository(Address::class)->find($aid);

        if (!$address) {
            return $this->json('No address found for id ' . $aid, 404);
        }
   
        $person->setAddress($address);
        $entityManager->flush();
   
        $data =  [
            'id' => $person->getId(),
            'firstName' => $person->getFirstName(),
            'lastName' => $person->getLastName(),
            'address' => [
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'zip' => $address->getZip(),
            ],
        ];
           
        return $this->json($data);
    }
    #[Route('/person/{id}/unlinkaddress', name: 'person_unlink_address', methods:['put', 'patch'] )]
    public function unlink_address_person(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $person = $entityManager->getRepository(Person::class)->find($id);
   
        if (!$person) {
            return $this->json('No person found for id ' . $id, 404);
        }

        $person->setAddress(null);
        $entityManager->flush();

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
        ];
           
        return $this->json($data);
    }
    // ############ End Person ########### //
    // ############ Start Policy ########### //
    #[Route('/policy', name: 'policy_create', methods:['post'] )]
    public function create_policy(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
    
        $user = $entityManager->getRepository(User::class)->find($request->request->get('user'));

        $address = new Address();
        $address->setStreet($request->request->get('street'));
        $address->setCity($request->request->get('city'));
        $address->setState($request->request->get('state'));
        $address->setZip($request->request->get('zip'));
        $entityManager->persist($address);
        $entityManager->flush();

        $person = new Person();
        $person->setFirstName($request->request->get('firstName'));
        $person->setLastName($request->request->get('lastName'));
        $person->setAddress($address);
        $entityManager->persist($person);
        $entityManager->flush();

        $policy = new Policy();
        $policy->setPolicyStatus($request->request->get('policyStatus'));
        $policy->setPolicyType($request->request->get('policyType'));
        $policy->setPolicyEffectiveDate($request->request->get('policyEffectiveDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('policyEffectiveDate')) : null);
        $policy->setPolicyExpirationDate($request->request->get('policyExpirationDate', null) != null ? \DateTime::createFromFormat('Y-m-d', $request->request->get('policyExpirationDate')) : null);
        $policy->setUser($user);
        $policy->setPolicyHolder($person);

        $policy->addDriver($entityManager->getRepository(Person::class)->find(1));
        $policy->addDriver($entityManager->getRepository(Person::class)->find(2));
    
        $entityManager->persist($policy);
        $entityManager->flush();

        $drivers = $policy->getDrivers();
        foreach ($drivers as $driver) {
            $driversData[] = [
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
        }
    
        $data =  [
            'id' => $policy->getId(),
            'policyStatus' => $policy->getPolicyStatus(),
            'policyType' => $policy->getPolicyType(),
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
            'drivers' => $driversData
        ];
            
        return $this->json($data);
    }
    // ############ End Policy ########### //

}


