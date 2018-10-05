<?php
namespace App\Models;
use App\DAO\GenericQueries;
use App\DAO\PasswordRecoveryDAO;
use App\DAO\PersonDAO;
use App\DAO\StoreDAO;
use App\DAO\UserDAO;
use App\util\Util;
use App\util\UtilMessage;

/** <b>User</b>
 *Classe CRUD User - nível de acesso dos Usuários
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
*/
class User
{
    /** @var  int $idUser - ID de Users */
    private $idUser;
    /** @var  int $userType - Define o tipo de usuário atacado, varejo, loja ou afiliado com 1 */
    private $userType;
    /** @var  string $email - E-mail do usuário com no máximo 100 caracteres */
    private $email;
    /** @var  string $password - Senha Hash do usuário com no máximo 255 caracteres */
    private $password;
    /** @var  int $id_person - FK da entidade Persons */
    private $id_person;

    /** @return int */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /** @param int $idUser */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /** @return int */
    public function getUserType()
    {
        return $this->userType;
    }

    /** @param int $userType */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /** @return string */
    public function getEmail()
    {
        return $this->email;
    }

    /** @param string $email */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /** @return string */
    public function getPassword()
    {
        return $this->password;
    }

    /** @param string $password */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /** @return int */
    public function getIdPerson()
    {
        return $this->id_person;
    }

    /** @param int $id_person */
    public function setIdPerson($id_person)
    {
        $this->id_person = $id_person;
    }

    /** @return \ArrayObject<string> Lista Usuários Cadastrados */
    public static function findAll()
    {
        $return = UserDAO::findAll();

        $return = Util::encodeIdOnArray($return);

        return $return;
    }

    /** Cadastra a tabela Users */
    public function insert($userObj)
    {
        $result = UserDAO::findRepeat($this->getEmail());

        if(count($result) > 0) {
            UtilMessage::setErrorMessage("Este E-mail já esta sendo usado em nossa base de dados! Se este e-mail é seu você pode alterar a senha!");
            return 0;
        }

        $passwordHash = Util::passwordHash($this->getPassword());
        $this->setPassword($passwordHash);

        try {
            $user = new UserDAO();
            $return = $user->insert($userObj);

            UtilMessage::setSuccessMessage("Usuário Cadastrado com Sucesso!");
            return $return;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro tentar Cadastrar Usuário: ".$e->getMessage());
            UtilMessage::setState('store');
            return 0;
        }
    }

    public function update($userObj)
    {
        $passwordHash = Util::passwordHash($this->getPassword());
        $userObj->setPassword($passwordHash);

        $result = PasswordRecoveryDAO::verifyValidity($this->getIdUser());

        foreach ($result as $row) {
            $idpassword_recovery = $row["idpassword_recovery"];
        }

        if (count($result) > 0) {
            try {
                $user = new UserDAO();
                $user->update($userObj);

                $recovery = new PasswordRecovery();
                $recovery->setIdPasswordRecovery($idpassword_recovery);
                $recovery->setIdUser($this->getIdUser());
                $recovery->update($recovery);

                UtilMessage::setSuccessMessage("Senha alterada com sucesso!");
                unset($_SESSION['code']);
            } catch (PDOException $e) {
                UtilMessage::setErrorMessage("Erro ao tentar alterar Senha: " . $e->getMessage());
            }
        } else {
            UtilMessage::setErrorMessage("Não foi possível alterar a Senha!");
        }
    }

    /** Verifica o Login */
    public static function login($email, $password)
    {
        $result = UserDAO::findRepeat($email);

        foreach ($result as $row) {
            $email = $row["email"];
            $passwordHash = $row["password"];
            $idUser = $row["iduser"];
            $typeUser = $row["type"];
            $idPerson = $row["id_person"];
        }

        if (!isset($email) || empty($email) || count($result) === 0){
            UtilMessage::setErrorMessage("Usuario inexistente ou senha inválida!");
            return 0;
        } else if (password_verify($password, $passwordHash) === true){
            $result = GenericQueries::findPessoa($idPerson);

            if (count($result) === 0) {
                $result = PersonDAO::findOne($idPerson);
            }

            foreach ($result as $row) {
                $personName = $row["name"];
                $idCorporation = $row["id_corporation"];
            }

            if ($typeUser == 2) {
                $store = StoreDAO::findUserStore($idUser);
            }

            foreach ($store as $row) {
                $idStore = $row["idstore"];
                $activated = $row["activated"];
                $nameStore = $row["name"];
            }

            User::setSessionLogin($idUser, $typeUser, $idPerson, $personName, $idCorporation);

            if ($typeUser == 2)
                User::setSessionStore($idStore, $activated, $nameStore);

            UtilMessage::setSuccessMessage("Login efetuado com sucesso!");

            if (filter_input($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['REMOTE_ADDR'];
            else
                $ip = 0;

            try {
                $log = new Log();

                $log->setIp($ip);
                $log->setSessionId(session_id());
                $log->setIdUser($idUser);
                $log->insert($log);

                UtilMessage::setSuccessMessage("Log Efeturado com Sucesso!");
                return 1;
            } catch (\Exception $e) {
                UtilMessage::setErrorMessage("Erro tentar Logar: " . $e->getMessage());
                return 0;
            }

            return 1;
        } else {
            UtilMessage::setErrorMessage("Usuario inexistente ou senha inválida!");
            return 0;
        }
    }

    /** Cria a sessão do Login */
    public static function setSessionLogin($idUser, $typeUser, $idPerson, $personName, $idCorporation)
    {
        $_SESSION["idUser"] = $idUser;
        $_SESSION["userType"] = $typeUser;
        $_SESSION["idPerson"] = $idPerson;
        $_SESSION["userName"] = $personName;
        $_SESSION["idCorporation"] = $idCorporation;
    }

    /** Altera a sessão da loja em edição */
    public static function setSessionStore($idStore, $activated, $nameStore)
    {
        $_SESSION["idstore"] = $idStore;
        $_SESSION["activated"] = $activated;
        $_SESSION["nameStore"] = $nameStore;

        UtilMessage::setSuccessMessage("Você está admininistrando agora a loja ".$_SESSION["nameStore"]);
    }

    /** @return \ArrayObject<string> da sessão do Login */
    public static function getSessionLogin()
    {
        $sessionLogin = [
            'idUser' => $_SESSION["idUser"],
            'userType' => $_SESSION["userType"],
            'idPerson' => $_SESSION["idPerson"],
            'userName' => $_SESSION["userName"],
            'idCorporation' => $_SESSION["idCorporation"],
            'idstore' => $_SESSION["idstore"],
            'activated' => $_SESSION["activated"],
            'nameStore' => $_SESSION["nameStore"]
        ];

        return $sessionLogin;
    }

    /** Destroi a sessão do Login */
    public static function logout()
    {
        unset(
            $_SESSION["idPerson"],
            $_SESSION["idUser"],
            $_SESSION["idCorporation"],
            $_SESSION["userType"],
            $_SESSION["userName"],
            $_SESSION["idstore"],
            $_SESSION["activated"],
            $_SESSION["nameStore"]
        );

        UtilMessage::setSuccessMessage("Você foi deslogado com sucesso do Sistema!");
    }
}