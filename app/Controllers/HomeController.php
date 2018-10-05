<?php
namespace App\Controllers;
use App\DAO\GenericQueries;
use App\Models\Address;
use App\util\Util;
use App\util\UtilMessage;
use App\util\Validation;
use App\Models\Corporation;
use App\Models\PasswordRecovery;
use App\Models\Person;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Products;
use App\Models\User;
use App\core\Controller;

/** <b>HomeController</b>
 *Classe Renderiza os elementos a serem carregados nas Rotas
 * @version 1.0
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @since 1.0
 * @package App\Controllers
*/
class HomeController extends Controller
{
    /** @return \ArrayObject<string> rota página inicial */
    public function index($request, $response, $args)
    {
        $products = Product::findAll();

        return Controller::twig()->render('home.twig', [
            'products' => $products,
            'main' => Util::mainCall(),
            'success' => UtilMessage::getSuccessMessage(),

        ]);
    }

    /** @return \ArrayObject<string> rota página lojas */
    public function lojas($request, $response, $args)
    {
        return Controller::twig()->render('lojas.twig', [
            'meta' => [
                'title' => 'Lojas na Galeria 44'
            ],
            'main' => Util::mainCall(),
            'success' => UtilMessage::getSuccessMessage(),

        ]);
    }

    /** @return \ArrayObject<string> rota página cadastro */
    public function cadastro($request, $response, $args)
    {
        if (isset($_POST['cpf'])) {
            $name = Validation::verifyWords($_POST['name']);
            $lastName = Validation::verifyWords($_POST['lastName']);
            $email = Validation::verifyEmail($_POST['email']);
            $password = Validation::verifyPassword($_POST['password']);
            $passwordConfirm = Validation::passwordConfirm($_POST['password'], $_POST['password_confirm']);
            $cpf = Validation::cpfValidate($_POST['cpf']);
            $birth = Validation::verifyDate($_POST['birth']);
            $sex = Validation::verifyWords($_POST['sex']);
            $code = Validation::verifyDdd($_POST['code']);
            $phone = Validation::verifyPhone($_POST['phone']);

            $person = new Person();

            $person->setType('F');
            $person->setName($name[0]);
            $person->setLastName($lastName[0]);
            $person->setCpf($cpf[0]);
            $person->setBirth($birth[0]);
            $person->setSex($sex[0]);

            if (!isset($_POST['newsletter']))
                $person->setNewsletter(1);
            else
                $person->setNewsletter($_POST['newsletter']);

            $user = new User();

            if (!isset($_POST['client']))
                $user->setUserType(0);
            else
                $user->setUserType($_POST['client']);

            $user->setEmail($email[0]);
            $user->setPassword($password[0]);

            $phones = new Phone();

            $phones->setAreaCode($code[0]);
            $phones->setPhone($phone[0]);
            $phones->setWhatsApp($_POST['whats']);
        }

        if (isset($_POST['cnpj'])) {
            $cnpj = Validation::cnpjValidate($_POST['cnpj']);

            $social_reason = Validation::verifyEmpty($_POST['social_reason']);
            $state_registration = Validation::verifyIfInt($_POST['state_registration']);

            $corporation = new  Corporation();

            $person->setType('J');
            $corporation->setCnpj($cnpj[0]);
            $corporation->setSocialReason($social_reason[0]);
            $corporation->setStateRegistration($state_registration[0]);

            if (!isset($_POST['responsible']))
                $corporation->setResponsible(0);
            else
                $corporation->setResponsible($_POST['responsible']);
        }

        if (($name[1] || $lastName[1] || $email[1] || $password[1] || $passwordConfirm[1] || $birth[1] || $sex[1] || $code[1] || $phone[1] || $cnpj[1] || $social_reason[1] || $state_registration[1]) && isset($_POST['cpf'])) {
            try {
                $idPerson = $person->insert($person);

                $user->setIdPerson($idPerson);
                $idUser = $user->insert($user);

                $phones->setIdPerson($idPerson);
                $idPhone = $phones->insert($phones);

                $corporation->setIdPerson($idPerson);
                $idCorp = $corporation->insert($corporation);

                if ($idPerson == 0 || $idUser == 0 || $idPhone == 0 || $idCorp == 0) {
                    $person->setIdPerson($idPerson);
                    $person->delete($person);

                    $corporation->setIdCorporation($idCorp);
                    $corporation->delete($corporation);
                } else {
                    $log = GenericQueries::findPessoaAndCorporation($idPerson);

                    foreach ($log as $row) {
                        $idUser = $row['iduser'];
                        $typeUser = $row['type'];
                        $idPerson = $row['idperson'];
                        $personName = $row['name'];
                        $idCorporation = $row['idcorporation'];
                    }

                    User::setSessionLogin($idUser, $typeUser, $idPerson, $personName, $idCorporation);
                }

                UtilMessage::setSuccessMessage("Cadastro realizado com sucesso!");
            } catch (\Exception $e) {
                return  "Erro ao tentar Cadastrar: ".$e->getMessage();
            }
        } else {
            $errors = [
                'name' => $name[0],
                'lastName' => $lastName[0],
                'email' => $email[0],
                'password' => $password[0],
                'passwordConfirm' => $passwordConfirm[0],
                'birth' => $birth[0],
                'sex' => $sex[0],
                'code' => $code[0],
                'phone' => $phone[0],
                'cpf' => $cpf[0],
                'cnpj' => $cnpj[0],
                'social_reason' => $social_reason[0],
                'state_registration' => $state_registration[0]
            ];
        }

        if (isset($_POST['cep'])) {
            $address = Validation::verifyEmpty($_POST['address']);
            $cep = Validation::verifyCep($_POST['cep']);
            $street = Validation::verifyEmpty($_POST['street']);
            $number = Validation::verifyIfInt($_POST['number']);
            $district = Validation::verifyEmpty($_POST['district']);
            $city = Validation::verifyWords($_POST['city']);
            $company = Validation::verifyIfInt($_POST['company']);
            $id_person = Validation::verifyIfInt($_POST['id_person']);
            $id_corporation = Validation::verifyIfInt($_POST['id_corporation']);

            $addresses = new Address();

            if (empty($_POST['number']))
                $addresses->setNumber(0);
            else
                $addresses->setNumber($number[0]);

            $addresses->setAddressType($address[0]);
            $addresses->setStreet($street[0]);
            $addresses->setComplement(filter_var($_POST['complement'], FILTER_SANITIZE_STRING));
            $addresses->setDistrict($district[0]);
            $addresses->setCity($city[0]);
            $addresses->setZipcode($cep[0]);
            $addresses->setAddressAlias(filter_var($_POST['alias'], FILTER_SANITIZE_STRING));
            $addresses->setVerifyCompany($company[0]);
            $addresses->setIdPerson($id_person[0]);
            $addresses->setIdCorporation($id_corporation[0]);

            if ($address[1] || $cep[1] || $street[1] || $number[1] || $district[1] || $city[1])
                $addresses->insert($addresses);
            else
                $errors = [
                    'address' => $address[0],
                    'cep' => $cep[0],
                    'street' => $street[0],
                    'number' => $number[0],
                    'district' => $district[0],
                    'city' => $city[0]
                ];
        }

        return Controller::twig()->render('cadastro.twig', [
            'main' => Util::mainCall(),
            'failure' => UtilMessage::getErrorMessage(),
            'success' => UtilMessage::getSuccessMessage(),
            'error' => $errors
        ]);
    }

    /** @return \ArrayObject<string> rota página login */
    public function login($request, $response, $args)
    {
        if (isset($_POST['email'])) {
            $email = Validation::verifyEmail($_POST['email']);
            $password = Validation::verifyPassword($_POST['password']);

            if ($email[1] || $password[1])
                $return = User::login($email[0], $password[0]);
            else
                $errors = [
                    'email' => $email[0],
                    'password' => $password[0]
                ];
        }

        if ($return && ($_SESSION["userType"] == 0 || $_SESSION["userType"] == 1)) {
            header("Location: " . Util::urlBase() . "/lojas");
            exit();
        } else if ($return && $_SESSION["userType"] == 2) {
            header("Location: " . Util::urlLoja());
            exit();
        } else {
            return Controller::twig()->render('login.twig', [
                'main' => Util::mainCall(),
                'failure' => UtilMessage::getErrorMessage(),
                'success' => UtilMessage::getSuccessMessage(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'session' => session_id(),
                'error' => $errors
            ]);
        }
    }

    /** Faz login e redireciona */
    public function logout($request, $response, $args)
    {
        User::logout();

        header("Location: ".Util::urlBase()."/login");
        exit();
    }

    /** Recupera a senha */
    public function passwordRecovery($request, $response, $args)
    {
        $user = new User();

        if (isset($_GET['code'])) {
            $state = true;

            $code = Util::verifyGetPostCode($_GET['code']);

            if ($code)
                $_SESSION['code'] = $code;
        } else if (isset($_POST['password'])) {
            $state = true;

            $password = Validation::verifyPassword($_POST['password']);
            $passwordConfirm = Validation::passwordConfirm($_POST['password'], $_POST['password_confirm']);

            if (($password[1] || $passwordConfirm[1]) && isset($_SESSION['code'])) {
                $user->setPassword($password[0]);
                $user->setIdUser($_SESSION['code']);
                $user->update($user);
            } else if (!($password || $passwordConfirm)) {
                $errors = [
                    'password' => $password[0],
                    'passwordConfirm' => $passwordConfirm[0]
                ];
            } else {
                UtilMessage::setErrorMessage("Não foi possível recuperar a Senha!");
            }
        } else {
            if (isset($_POST['email'])) {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $result = GenericQueries::getNome($_POST['email']);

                    if (count($result) > 0) {
                        foreach ($result as $row) {
                            $idUser = $row["iduser"];
                            $userName = $row["name"];
                            $userLastName = $row["last_name"];
                        }

                        $urlRecovery = Util::urlBase()."/recuperar-senha?code=".Util::idEncode($idUser);

                        $info = [
                            'name' => $userName,
                            'lastName' => $userLastName,
                            'urlRecovery' => $urlRecovery
                        ];

                        $sendEmail = new SendEmail();

                        $emailBody = $sendEmail->renderEmail($info);
                        $sendEmail->emailConstruct($_POST['email'], $userName . ' ' . $userLastName, 'Recuperação de Senha Lendary', $emailBody); //email, nome, assunto
                        $success = $sendEmail->send();

                        if ($success) {
                            $recovery = new PasswordRecovery();

                            $recovery->setIdUser($idUser);
                            $recovery->insert($recovery);
                        }
                    } else {
                        UtilMessage::setErrorMessage("Não foi possível recuperar a Senha, digite um e-mail válido!!!");
                    }
                } else {
                    UtilMessage::setErrorMessage("Não foi possível recuperar a Senha, digite um e-mail válido!");
                }
            }
        }

        return Controller::twig()->render('recuperar-senha.twig', [
            'main' => Util::mainCall(),
            'failure' => UtilMessage::getErrorMessage(),
            'success' => UtilMessage::getSuccessMessage(),
            'state' => $state,
            'error' => $errors
        ]);
    }
}
?>
