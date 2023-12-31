<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\SalesPerson;
use App\Entity\Supplier;
use App\Entity\User;
use Container24h671p\getOrderDetailsRepositoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\Clock\now;

class AppFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct(private readonly UserPasswordHasherInterface $hasher, private SluggerInterface $slugger)
    {
        $this->userPasswordHasherInterface = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $this->manager = $manager;

        // --------------------------
        // ----SalesPerson
        // --------------------------

        $salesPersonFirstNames = ['Romain', 'Pierrot', 'Ju', 'Yu', 'Zak'];
        $salesPersonLastNames = ['Durant', 'Pompier', 'Malek', 'Shchedrakova', 'Teurki'];
        // generate 5 SalesPerson
        for ($i = 0; $i < 5; $i++) {
            $salesPerson = new SalesPerson();
            // assuming there's a setName method in the SalesPerson entity
            $salesPerson->setSalesPersoneFirstName($salesPersonFirstNames[$i])
                ->setSalesPersonLastName($salesPersonLastNames[$i]);

            $manager->persist($salesPerson); // add the salesPerson to be saved
        }

        // --------------------------
        // ----Supplier
        // --------------------------

        $supplierName = ['Ammo', 'Gunshop', 'TerraGun', 'KarryOn', 'DialloMunition'];
        $supplierAddress = ['11 rue de la vie, Toulouse 31000', '23 rue du Général, Paris 75020', 'Paris la Defense, 92000', '67 rue de la Street, Clignancourt 93300', '13 rue bergère, Nantes 44000'];
        // generate 5 supplier
        for ($i = 0; $i < 5; $i++) {
            $supplier = new Supplier();
            $supplier->setSupplierName($supplierName[$i])
                ->setSupplierAddress($supplierAddress[$i]);

            $manager->persist($supplier); // add the supplier to be saved
        }

        // --------------------------
        // ----User
        // --------------------------

        // generate 1 admin user
        $admin = new User();
        $admin->setEmail('admin@gmail.com')
            ->setUserName('Shchedrakova Yuliia')
            ->setUserAddress('9 rue Albert premier, Montdidiers 80400')
            ->setUserType(true)
            ->setPassword($this->userPasswordHasherInterface->hashPassword($admin, 'bonjour'))
            ->setUserReference(null)
            ->setRoles(['ROLE_ADMIN'])
            ->setUserReference('ADMIN001');
//            ->setIsVerified(false);
        $manager->persist($admin);

        // generate 4 users
        $user = new User();
        $user->setUserName('Jo Lafritte')
            ->setEmail('user1@gmail.com')
            ->setUserType(false)
            ->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'bonjour'))
            ->setUserReference('REF001')
            ->setUserCoefficient(1)
            ->setSalesPerson($salesPerson)
//            ->setIsVerified(false)
            ->setUserAddress('123 Main St, New York');
        $manager->persist($user);

        $user2 = new User();
        $user2->setUserName('Julien malek')
            ->setEmail('user2@gmail.com')
            ->setUserType(false)
            ->setPassword($this->userPasswordHasherInterface->hashPassword($user2, 'bonjour'))
            ->setUserReference('REF002')
            ->setUserCoefficient(1)
            ->setSalesPerson($salesPerson)
//            ->setIsVerified(false)
            ->setUserAddress('123 Main St, Paris 75020');
        $manager->persist($user2);


        $user3 = new User();
        $user3->setUserName('Seydina Diallo')
            ->setEmail('user3@example.com')
            ->setUserType(false)
            ->setPassword($this->userPasswordHasherInterface->hashPassword($user3, 'bonjour'))
            ->setUserReference('REF003')
            ->setUserCoefficient(1)
            ->setSalesPerson($salesPerson)
//            ->setIsVerified(false)
            ->setUserAddress('10 rue de la vie, Amiens 80000');
        $manager->persist($user3);

        $user4 = new User();
        $user4->setUserName('Vivian Fleur')
            ->setEmail('user4@gmail.com')
            ->setUserType(false)
            ->setPassword($this->userPasswordHasherInterface->hashPassword($user4, 'bonjour'))
            ->setUserReference('REF004')
            ->setUserCoefficient(1)
            ->setSalesPerson($salesPerson)
//            ->setIsVerified(false)
            ->setUserAddress('11 rue Léonard massé, Beauvais 60000');
        $manager->persist($user4);

        // --------------------------
        // ----Category
        // --------------------------

        // parent Category
        $parent = new Category();
        $parent->setCategoryName('Arme de poing')
            ->setCategoryDescription('Armes qui peuvent êtres prisent à une seule main')
            ->setParent(null)
            ->setCategoryImage('pistolet.jpg');
        $manager->persist($parent);

        $parent2 = new Category();
        $parent2->setCategoryName("Arme d'épaule")
            ->setCategoryDescription('Armes qui se tiennent à deux mains')
            ->setParent(null)
            ->setCategoryImage('fusils_assaut.jpg');
        $manager->persist($parent2);

        $parent3 = new Category();
        $parent3->setCategoryName('Arme exotique')
            ->setCategoryDescription("Tout les autres styles d'armes pouvant exister!")
            ->setParent(null)
            ->setCategoryImage('exotiques.jpg');
        $manager->persist($parent3);

        // sous-catégories
        $cat = new Category();
        $cat->setCategoryName('Fusils de précision')
            ->setCategoryImage('fusil_précision.jpg')
            ->setCategoryDescription('Fusil permettant des tirs à longue distance.')
            ->setParent($parent2);
        $manager->persist($cat);

        $cat2 = new Category();
        $cat2->setCategoryName('Fusils à pompe')
            ->setCategoryImage('shotgun.jpg')
            ->setCategoryDescription('Fusil de type fusil de chasse se rechargeant en pompant.')
            ->setParent($parent3);
        $manager->persist($cat2);

        $cat3 = new Category();
        $cat3->setCategoryName('Mitrailleuses')
            ->setCategoryImage('exotiques.jpg')
            ->setCategoryDescription('Mitrailleuse permet de tirer des munitions de gros calibre avec une cadence de tir soutenu. Généralement utilisé pour faire baisser les têtes.')
            ->setParent($parent3);
        $manager->persist($cat3);

        $cat4 = new Category();
        $cat4->setCategoryName('Pistolets mitrailleurs')
            ->setCategoryImage('armes3.jpg')
            ->setCategoryDescription('Armes avec une cadences de tir élevée efficace à courtes portée.')
            ->setParent($parent);
        $manager->persist($cat4);

        $cat5 = new Category();
        $cat5->setCategoryName('Pistolets et Revolvers')
            ->setCategoryImage('pistolet.jpg')
            ->setCategoryDescription('Armes à une main efficace à courte portée, pratique à avoir toujours sur soi.')
            ->setParent($parent);
        $manager->persist($cat5);

        $cat6 = new Category();
        $cat6->setCategoryName("Fusils d'assaut")
            ->setCategoryImage('fusils_assaut.jpg')
            ->setCategoryDescription('Fusil souvent au coup par coup, rafale ou au coup par coup utilisé pour les combats à mi-distance.')
            ->setParent($parent2);
        $manager->persist($cat6);

        $cat7 = new Category();
        $cat7->setCategoryName("Miniguns")
            ->setCategoryImage('minigun.jpeg')
            ->setCategoryDescription('Armes souvent sur affût, avec une cadence de tir énorme. Grosse capacité de destruction.')
            ->setParent($parent3);
        $manager->persist($cat7);

        // --------------------------
        // ----Product
        // --------------------------

        $product = new Product();
        $product->setCategory($cat5)
            ->setProductLabel('Glock 17')
            ->setProductDescription("L'aspect novateur du Glock 17 est en effet qu'il s'agit tout simplement de la première arme de poing conçue en polymère.")
            ->setProductPrice(500.99)
            ->setSlug($this->slugger->slug($product->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(32)
            ->setSupplier($supplier)
            ->setProductImage('glock-17.jpg');
        $manager->persist($product);

        $product2 = new Product();
        $product2->setCategory($cat6)
            ->setProductLabel('hk 416')
            ->setProductDescription("Fusil au calibre Otan 5,56 mm, le HK 416 F dispose d'une crosse réglable et de talons de crosse permettant de s'adapter à la morphologie de chaque tireur. Disposant d'une autonomie accrue, le combattant sera muni de 10 chargeurs de 30 cartouches .")
            ->setProductPrice(899.99)
            ->setSlug($this->slugger->slug($product2->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(66)
            ->setSupplier($supplier)
            ->setProductImage('hk_416.jpg');
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setCategory($cat2)
            ->setProductLabel('Spas 12')
            ->setProductDescription("Le SPAS 12, doté d'un canon à âme lisse, tire des cartouches de calibre 12/70mm et dispose d'un magasin tubulaire. Semi-automatique, il fonctionne également en chargement manuel. ")
            ->setProductPrice(1500.99)
            ->setSlug($this->slugger->slug($product3->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(125)
            ->setSupplier($supplier)
            ->setProductImage('shotgun.jpg');
        $manager->persist($product3);

        $product4 = new Product();
        $product4->setCategory($cat5)
            ->setProductLabel('Glock 21')
            ->setProductDescription("L'aspect novateur du Glock 17 est en effet qu'il s'agit tout simplement de la première arme de poing conçue en polymère.")
            ->setProductPrice(500.99)
            ->setSlug($this->slugger->slug($product4->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(123)
            ->setSupplier($supplier)
            ->setProductImage('glock_21.jpeg');
        $manager->persist($product4);

        $product5 = new Product();
        $product5->setCategory($cat)
            ->setProductLabel('Barret 50 cal')
            ->setProductDescription("Le Barrett M82 (aussi connu comme le M82 ou le M107) est un fusil anti-matériel américain développé par la Barrett Firearms Manufacturing. Il est conçu pour pouvoir tirer parti de la puissante cartouche de calibre . 50 (12,7 × 99 mm), conçue à l'origine pour la mitrailleuse lourde Browning M2.")
            ->setProductPrice(2500)
            ->setSlug($this->slugger->slug($product5->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(45)
            ->setSupplier($supplier)
            ->setProductImage('barret_50.jpg');
        $manager->persist($product5);

        $product6 = new Product();
        $product6->setCategory($cat6)
            ->setProductLabel('Scar H')
            ->setProductDescription("Ce nouveau fusil de calibre 7,62 mm offre des performances de tir remarquables, de jour comme de nuit, jusqu'à une distance de 800 mètres (là où les fusils d'assauts comme le HK 416 ont une portée de 300 mètres).")
            ->setProductPrice(850)
            ->setSlug($this->slugger->slug($product6->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(166)
            ->setSupplier($supplier)
            ->setProductImage('scar_h.webp');
        $manager->persist($product6);

        $product7 = new Product();
        $product7->setCategory($cat6)
            ->setProductLabel('AK 47')
            ->setProductDescription("L’AK-47 est un fusil d'assaut conçu par l'ingénieur soviétique Mikhaïl Kalachnikov. L'AK-47 est le premier modèle d'une vaste famille de fusils d'assaut à emprunt de gaz, dont le modèle le plus répandu est l'AKM.")
            ->setProductPrice(450)
            ->setSlug($this->slugger->slug($product7->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(10)
            ->setSupplier($supplier)
            ->setProductImage('ak_47.jpg');
        $manager->persist($product7);

        $product8 = new Product();
        $product8->setCategory($cat5)
            ->setProductLabel('Smith & Wesson 500 8.38')
            ->setProductDescription("Qui pouvait repousser encore les limites du revolver si ce n'est le géant du secteur ? Avec son modèle 500, Smith & Wesson a conforté sa place dans l'histoire de l'armement. C'est donc avec fierté que l'Armurerie Auxerre vous propose ce monstre d'acier, ici dans sa version à canon long de 8.38. ")
            ->setProductPrice(2200)
            ->setSlug($this->slugger->slug($product8->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(3)
            ->setSupplier($supplier)
            ->setProductImage('magnum.webp');
        $manager->persist($product8);

        $product9 = new Product();
        $product9->setCategory($cat3)
            ->setProductLabel('Mag 58')
            ->setProductDescription("Les FN MAG vont équiper plus 80 armées dans le monde et les troupes semblent apprécier cette arme et elle est bien partie pour rester longtemps en service.")
            ->setProductPrice(2600)
            ->setSlug($this->slugger->slug($product9->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(19)
            ->setSupplier($supplier)
            ->setProductImage('mag_58.jpeg');
        $manager->persist($product9);

        $product10 = new Product();
        $product10->setCategory($cat3)
            ->setProductLabel('Mossberg Maverick Rayé')
            ->setProductDescription("Fabriqué aux USA, ce fusil à pompe ultra fiable et sa mécanique éprouvée en fait une arme qui ne vous décevra jamais. Cette nouvelle version du Maverick 88 est en 4+1 afin d’être répertoriée en catégorie C")
            ->setProductPrice(684)
            ->setSlug($this->slugger->slug($product10->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(34)
            ->setSupplier($supplier)
            ->setProductImage('Maverick_88.jpg');
        $manager->persist($product10);

        $product11 = new Product();
        $product11->setCategory($cat2)
            ->setProductLabel('PKM')
            ->setProductDescription("La mitrailleuse Kalachnikov, plus connue sous les initiales PK de son nom russe : пулемёт Калашникова, poulemiot Kalachnikova, est une mitrailleuse soviétique adoptée en 1961 et produite depuis lors à plus d'un million d'exemplaires.")
            ->setProductPrice(1000)
            ->setSlug($this->slugger->slug($product11->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(50)
            ->setSupplier($supplier)
            ->setProductImage('PKM.jpg');
        $manager->persist($product11);

        $product12 = new Product();
        $product12->setCategory($cat6)
            ->setProductLabel('hk 417')
            ->setProductDescription("Le HK417 est un fusil d'assaut de la firme Heckler & Koch, basé sur le modèle HK 416 mais chambré en 7,62 x 51 mm OTAN. ")
            ->setProductPrice(1799)
            ->setSlug($this->slugger->slug($product12->getProductLabel())->lower())
            ->setIsActive(1)
            ->setProductStock(200)
            ->setSupplier($supplier)
            ->setProductImage('hk_417.webp');
        $manager->persist($product12);

        // --------------------------
        // ----Order
        // --------------------------

        $order = new Order();
        $order->setUser($user)
            ->setOrderDate(new \DateTime('2023-12-03'))
            ->setOrderBillingAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDeliveryAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDiscount(null)
            ->setOrderPaymentStatus(true)
            ->setOrderShippingStatus('En cours');
        $manager->persist($order);

        $order2 = new Order();
        $order2->setUser($user4)
            ->setOrderDate(new \DateTime('2023-12-03'))
            ->setOrderBillingAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDeliveryAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDiscount(null)
            ->setOrderPaymentStatus(true)
            ->setOrderShippingStatus('En cours');
        $manager->persist($order2);

        $order3 = new Order();
        $order3->setUser($user2)
            ->setOrderDate(new \DateTime('2023-11-03'))
            ->setOrderBillingAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDeliveryAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDiscount(null)
            ->setOrderPaymentStatus(true)
            ->setOrderShippingStatus('Reçu');
        $manager->persist($order3);

        $order4 = new Order();
        $order4->setUser($user3)
            ->setOrderDate(new \DateTime('2023-07-15'))
            ->setOrderBillingAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDeliveryAddress('10 rue de la boucherie, Bordeaux')
            ->setOrderDiscount(null)
            ->setOrderPaymentStatus(true)
            ->setOrderShippingStatus('En cours');
        $manager->persist($order4);

        // --------------------------
        // ----OrderDetails
        // --------------------------

        $orderDetail = new OrderDetails();
        $orderDetail->setOrders($order)
            ->setProduct($product)
            ->setQuantity(12)
            ->setOrderDetailsPrice(($orderDetail->getQuantity()) * ($product->getProductPrice()));
        $manager->persist($orderDetail);

        $orderDetail2 = new OrderDetails();
        $orderDetail2->setOrders($order2)
            ->setProduct($product2)
            ->setQuantity(4)
            ->setOrderDetailsPrice(($orderDetail2->getQuantity()) * ($product->getProductPrice()));
        $manager->persist($orderDetail2);

        $orderDetail3 = new OrderDetails();
        $orderDetail3->setOrders($order3)
            ->setProduct($product3)
            ->setQuantity(2)
            ->setOrderDetailsPrice(($orderDetail3->getQuantity()) * ($product->getProductPrice()));
        $manager->persist($orderDetail3);

        $orderDetail4 = new OrderDetails();
        $orderDetail4->setOrders($order4)
            ->setProduct($product5)
            ->setQuantity(21)
            ->setOrderDetailsPrice(($orderDetail4->getQuantity()) * ($product->getProductPrice()));
        $manager->persist($orderDetail4);

        // --------------------------
        // ----Payment
        // --------------------------

        $payment = new Payment();
        $payment->setOrders($order)
            ->setPaymentAmount(($orderDetail->getOrderDetailsPrice()))
            ->setPaymentDate(new \DateTime('2023-12-04'))
            ->setPaymentMethod('CB');
        $manager->persist($payment);

        $payment2 = new Payment();
        $payment2->setOrders($order2)
            ->setPaymentAmount(($orderDetail2->getOrderDetailsPrice()))
            ->setPaymentDate(new \DateTime('2023-12-04'))
            ->setPaymentMethod('Virement');
        $manager->persist($payment2);

        $payment3 = new Payment();
        $payment3->setOrders($order3)
            ->setPaymentAmount(($orderDetail3->getOrderDetailsPrice()))
            ->setPaymentDate(new \DateTime('2023-11-10'))
            ->setPaymentMethod('CB');
        $manager->persist($payment3);

        $payment4 = new Payment();
        $payment4->setOrders($order4)
            ->setPaymentAmount(($orderDetail4->getOrderDetailsPrice()))
            ->setPaymentDate(new \DateTime('2023-08-20'))
            ->setPaymentMethod('Chèque');
        $manager->persist($payment4);

        $manager->flush();
    }
}