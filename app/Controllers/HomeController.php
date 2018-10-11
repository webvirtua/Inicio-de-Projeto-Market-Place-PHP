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
            $person->setName(Util::sanitizeString($_POST['name']));
            $person->setLastName(Util::sanitizeString($_POST['lastName']));
            $person->setCpf(Util::sanitizeInt($_POST['cpf']));
            $person->setBirth($_POST['birth']);
            $person->setSex(Util::sanitizeString($_POST['sex']));

            if (!isset($_POST['newsletter']))
                $person->setNewsletter(1);
            else
                $person->setNewsletter(Util::sanitizeInt($_POST['newsletter']));

            $user = new User();

            if (!isset($_POST['client']))
                $user->setUserType(0);
            else
                $user->setUserType(Util::sanitizeString($_POST['client']));

            $user->setEmail(Util::sanitizeEmail($_POST['email']));
            $user->setPassword($_POST['password']);

            $phones = new Phone();

            $phones->setAreaCode(Util::sanitizeInt($_POST['code']));
            $phones->setPhone(Util::sanitizeInt($_POST['phone']));
            $phones->setWhatsApp(Util::sanitizeInt($_POST['whats']));
        }

        if (isset($_POST['cnpj'])) {
            $cnpj = Validation::cnpjValidate($_POST['cnpj']);

            $social_reason = Validation::verifyEmpty($_POST['social_reason']);
            $state_registration = Validation::verifyIfInt($_POST['state_registration']);

            $corporation = new  Corporation();

            $person->setType('J');
            $corporation->setCnpj(Util::sanitizeInt($_POST['cnpj']));
            $corporation->setSocialReason(Util::sanitizeString($_POST['social_reason']));
            $corporation->setStateRegistration(Util::sanitizeString($_POST['state_registration']));

            if (!isset($_POST['responsible']))
                $corporation->setResponsible(0);
            else
                $corporation->setResponsible(Util::sanitizeInt($_POST['responsible']));
        }

        if (!($name || $lastName || $email || $password || $passwordConfirm || $birth || $sex || $code || $phone || $cnpj || $social_reason || $state_registration) && isset($_POST['cpf'])) {
            try {
                $idPerson = $person->insert($person);

                $user->setIdPerson($idPerson);
                $idUser = $user->insert($user);

                $phones->setIdPerson($idPerson);
                $idPhone = $phones->insert($phones);

                if (isset($_POST['cnpj'])) {
                    $corporation->setIdPerson($idPerson);
                    $idCorp = $corporation->insert($corporation);
                } else {
                    $idCorp = -1;
                }

                if ($idPerson == 0 || $idUser == 0 || $idPhone == 0 || $idCorp == 0) {
                    $person->setIdPerson($idPerson);
                    $person->delete($person);

                    if (isset($_POST['cnpj'])) {
                        $corporation->setIdCorporation($idCorp);
                        $corporation->delete($corporation);
                    }
                } else {
                    if ($idCorp < 0)
                        $idCorp = null;

                    User::setSessionLogin($idUser, $user->getUserType(), $idPerson, $person->getName(), $idCorp);
                }

                UtilMessage::setSuccessMessage("Cadastro realizado com sucesso!");
            } catch (\Exception $e) {
                UtilMessage::setErrorMessage("Erro ao tentar Cadastrar: ".$e->getMessage());
            }
        } else {
            $errors = [
                'name' => $name,
                'lastName' => $lastName,
                'email' => $email,
                'password' => $password,
                'passwordConfirm' => $passwordConfirm,
                'birth' => $birth,
                'sex' => $sex,
                'code' => $code,
                'phone' => $phone,
                'cpf' => $cpf,
                'cnpj' => $cnpj,
                'social_reason' => $social_reason,
                'state_registration' => $state_registration
            ];
        }

        if (isset($_POST['cep'])) {
            $address = Validation::verifyEmpty($_POST['address']);
            $cep = Validation::verifyCep($_POST['cep']);
            $street = Validation::verifyEmpty($_POST['street']);
            $number = Validation::verifyIfInt($_POST['number']);
            $district = Validation::verifyEmpty($_POST['district']);
            $city = Validation::verifyWords($_POST['city']);

            $addresses = new Address();

            if (empty($_POST['number']))
                $addresses->setNumber(0);
            else
                $addresses->setNumber(Util::sanitizeInt($_POST['number']));

            $addresses->setAddressType(Util::sanitizeString($_POST['address']));
            $addresses->setStreet(Util::sanitizeString($_POST['street']));
            $addresses->setComplement(Util::sanitizeString($_POST['complement']));
            $addresses->setDistrict(Util::sanitizeString($_POST['district']));
            $addresses->setCity(Util::sanitizeString($_POST['city']));
            $addresses->setZipcode(Util::sanitizeInt($_POST['cep']));
            $addresses->setAddressAlias(Util::sanitizeString($_POST['alias']));
            $addresses->setVerifyCompany(Util::sanitizeInt($_POST['company']));
            $addresses->setIdPerson(Util::sanitizeInt($_POST['id_person']));
            $addresses->setIdCorporation(Util::sanitizeInt($_POST['id_corporation']));

            if (!($address || $cep || $street || $number || $district || $city))
                $addresses->insert($addresses);
            else
                $errors = [
                    'address' => $address,
                    'cep' => $cep,
                    'street' => $street,
                    'number' => $number,
                    'district' => $district,
                    'city' => $city
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

            if (!($email || $password))
                $return = User::login($_POST['email'], $_POST['password']);
            else
                $errors = [
                    'email' => $email,
                    'password' => $password
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

            if (!($password || $passwordConfirm) && isset($_SESSION['code'])) {
                $user->setPassword($_POST['password']);
                $user->setIdUser($_SESSION['code']);
                $user->update($user);
            } else if (!($password || $passwordConfirm)) {
                $errors = [
                    'password' => $password,
                    'passwordConfirm' => $passwordConfirm
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
