use Acme\StoreBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

public function createAction()
{
    $user = new User();
    $user->setUser('testUser');
    $user->setLab('1');
    $user->setRole('1');

    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    return new Response('Created user id '.$user->getUser());
}