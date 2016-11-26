<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.12
 * Time: 18.47
 */

namespace AppBundle\Controller;
use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\ChangeRolesType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * @Route("/", name="adminHome")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/user", name="users")
     */
    public function userAction()
    {
        $userService = $this->get('app.user');
        $users = $userService->getAllUsers();
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/user/{id}", name="userAction")
     */
    public function userActionList($id, Request $request)
    {
        $userService = $this->get('app.user');
        $user = $userService->getUserById($id);
        $roles=null;
        $form = $this->createForm(ChangeRolesType::class, $roles);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $request->get('change_roles');
            $numberOfSelectedRoles=0;
            foreach($roles  as $role) {
                if(substr($role, 0, 4)=="ROLE"){
                    $numberOfSelectedRoles++;
                }

            }
            if($numberOfSelectedRoles==0){
                return $this->render('AppBundle:Admin:userAction.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'error' => 'Nepasirinkote rolių'
                ]);
            }
            $user->removeRole('ROLE_USER');
            $user->removeRole('ROLE_ADMIN');
            $user->removeRole('ROLE_SUPER_ADMIN');


            foreach($roles  as $role) {
                //Do something with the ID

                if(substr($role, 0, 4)=="ROLE"){


                        $user->addRole($role);


                }

            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->render('AppBundle:Admin:index.html.twig', []);
        }
        return $this->render('AppBundle:Admin:userAction.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/user/delete/{id}", name="userDelete")
     */
    public function userDelete($id)
    {
        $userService = $this->get('app.user');

        $user = $userService->getUserById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/user/changePassword/{id}", name="userChangePassword")
     */
    public function userChangePassword($id, Request $request)
    {
        $userService = $this->get('app.user');
        $user = $userService->getUserById($id);
        $userType = new User();
        $form = $this->createForm(ChangePasswordType::class, $userType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $userType->getPassword();
            if (strlen($password) < '8' || strlen($password) > '32') {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi būti didesnis tarp 8 ir 32 simbolių'
                    ]
                );
            } elseif (!preg_match("#[0-9]+#", $password)) {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną numerį'
                    ]
                );
            } elseif (!preg_match("#[A-Z]+#", $password)) {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną didžiąją raidę'
                    ]
                );
            } elseif (!preg_match("#[a-z]+#", $password)) {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną mažąją raidę'
                    ]
                );
            }

            $user->setPlainPassword($userType->getPassword());
            $userManager = $this->container->get('fos_user.user_manager');

            $userManager->updateUser($user, true);
            return $this->render('AppBundle:Admin:index.html.twig', []);
        }
        return $this->render('AppBundle:Admin:changePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/userByEmailASC", name="usersbyEmailASC")
     */
    public function userActionEmailASC()
    {
        $userService = $this->get('app.user');
        $users = $userService->getAllUsersASC();
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/userByEmailDESC", name="usersbyEmailDESC")
     */
    public function userActionEmailDESC()
    {
        $userService = $this->get('app.user');
        $users = $userService->getAllUsersDESC();
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/tests", name="tests")
     */
    public function testsAction(){

        $tests = $this->getDoctrine()->getRepository('AppBundle:Test')->findAll();
   // print_r($tests);
        return $this->render('AppBundle:Admin:tests.html.twig', [
            'tests' => $tests
        ]);
    }
    /**
     * @Route("/tests/{id}", name="testsAction")
     */
    public function testActionList($id)
    {
        $testsService = $this->get('app.tests');
        $test = $testsService->getTestById($id);


        return $this->render('AppBundle:Admin:testAction.html.twig', [
            'test' => $test
        ]);
    }
    /**
     * @Route("/tests/delete/{id}", name="testsDelete")
     */
    public function testDelete($id)
    {
        $testsService = $this->get('app.tests');

        $test = $testsService->getTestById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($test);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/publish/{id}", name="testsPublish")
     */
    public function testPublish($id)
    {
        $testsService = $this->get('app.tests');

        $test = $testsService->getTestById($id);
        $em = $this->getDoctrine()->getManager();
        $test->setPublished(1);
        $em->persist($test);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/depublish/{id}", name="testsDepublish")
     */
    public function testDepublish($id)
    {
        $testsService = $this->get('app.tests');

        $test = $testsService->getTestById($id);
        $em = $this->getDoctrine()->getManager();
        $test->setPublished(0);
        $em->persist($test);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
}
