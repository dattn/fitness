<?php

namespace Controller;
class Usermanager extends Backend {

    protected $types = array('admin');

    public function before() {
        parent::before();

        $this->View->assign('subMenu', array(
            'left' => array(
                array('link', \Config::BASEURL.'usermanager', 'List'),
                array('link', \Config::BASEURL.'usermanager/edit', 'Create')
            )
        ));
    }

    public function index() {
        $this->redirect('usermanager/listing');
    }

	public function listing() {
        $result = $this->DB->execute('
            SELECT iduser, dtlast_name, dtfirst_name, dttype, dtemail, dtbirthdate
            FROM tblfitness_user
            ORDER BY dtlast_name ASC, dtfirst_name ASC
        ');
        $users = $result->fetchAll();

        $this->View->assign(array(
            'users' => $users
        ));
        return $this->View->fetch('usermanager/list.tpl');
    }

    public function edit($iduser = null) {
        if (isset($_SESSION['user_edit'])) {
            $user = $_SESSION['user_edit'];
            $user['iduser'] = $iduser;
            unset($_SESSION['user_edit']);
        } else if ($iduser) {
            $result = $this->DB->execute('
                SELECT *
                FROM tblfitness_user
                WHERE iduser = :user
            ', array(
                'user' => $iduser
            ));
            $users = $result->fetchAll();
            if (isset($users[0])) {
                $user = $users[0];
                if ($user['dtbirthdate']) {
                    $user['dtbirthdate'] = implode('/', array_reverse(explode('-', $user['dtbirthdate'])));
                }
            } else {
                $user = false;
            }
        } else {
            $user = false;
        }

        $types = array(
            'customer' => 'Customer',
            'coach' => 'Coach',
            'admin' => 'Admin'
        );

        $this->View->assign(array(
            'user' => $user,
            'types' => $types
        ));
        return $this->View->fetch('usermanager/edit.tpl');
    }

    public function save($iduser = null) {
        if (empty($_POST))
            $this->redirect('usermanager');

        if (empty($_POST['dtlast_name'])
                || empty($_POST['dtfirst_name'])
                || empty($_POST['dttype'])
                || empty($_POST['dtemail'])) {
            \Message::add('You need to fill in all fields marked with an *');
            $_SESSION['user_edit'] = $_POST;
            $this->redirect('usermanager/edit/'.$iduser);
        }

        if (!filter_var($_POST['dtemail'], FILTER_VALIDATE_EMAIL)) {
            \Message::add('Invalid email format');
            $_SESSION['user_edit'] = $_POST;
            $this->redirect('usermanager/edit/'.$iduser);
        }

        if (!empty($_POST['dtbirthdate']) && !preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $_POST['dtbirthdate'])) {
            \Message::add('Invalid birthdate format');
            $_SESSION['user_edit'] = $_POST;
            $this->redirect('usermanager/edit/'.$iduser);
        }

        if (!empty($_POST['dtpassword']) || !empty($_POST['dtpassword2'])) {
            if ($_POST['dtpassword'] != $_POST['dtpassword2']) {
                \Message::add('The passwords do not match');
                $_SESSION['user_edit'] = $_POST;
                $this->redirect('usermanager/edit/'.$iduser);
            }

            if (strlen($_POST['dtpassword']) < 6) {
                \Message::add('The password is to short. Minimum length is 6');
                $_SESSION['user_edit'] = $_POST;
                $this->redirect('usermanager/edit/'.$iduser);
            }
        } else {
            if (!$iduser) {
                \Message::add('You need to fill in all fields marked with an *');
                $_SESSION['user_edit'] = $_POST;
                $this->redirect('usermanager/edit/'.$iduser);
            }
        }

        $birthdate = $_POST['dtbirthdate']? implode('-', array_reverse(explode('/', $_POST['dtbirthdate']))) : NULL;

        if ($iduser) {
            $this->DB->execute('
                UPDATE tblfitness_user
                SET dtlast_name = :last_name,
                    dtfirst_name = :first_name,
                    dttype = :type,
                    dtemail = :email,
                    dtbirthdate = :birthdate
                WHERE iduser = :user
            ', array(
                'last_name' => $_POST['dtlast_name'],
                'first_name' => $_POST['dtlast_name'],
                'type' => $_POST['dttype'],
                'email' => $_POST['dtemail'],
                'birthdate' => $birthdate,
                'user' => $iduser
            ));
            if ($_POST['dtpassword']) {
                $this->DB->execute('
                    UPDATE tblfitness_user
                    SET dtpassword = :password
                    WHERE iduser = :user
                ', array(
                    'password' => password_hash($_POST['dtpassword'], PASSWORD_DEFAULT),
                    'user' => $iduser
                ));
            }
        } else {
            $this->DB->execute('
                INSERT
                INTO tblfitness_user
                  (dtlast_name, dtfirst_name, dtpassword, dttype, dtemail, dtbirthdate)
                VALUES
                  (:last_name, :first_name, :password, :type, :email, :birthdate)
            ', array(
                'last_name' => $_POST['dtlast_name'],
                'first_name' => $_POST['dtlast_name'],
                'type' => $_POST['dttype'],
                'email' => $_POST['dtemail'],
                'birthdate' => $birthdate,
                'password' => password_hash($_POST['dtpassword'], PASSWORD_DEFAULT)
            ));
        }
        $this->redirect('usermanager');
    }

    public function delete($iduser) {
        $this->DB->execute('
            DELETE
            FROM tblfitness_user2event
            WHERE fiuser = :user
        ', array(
            'user' => $iduser
        ));
        $this->DB->execute('
            DELETE
            FROM tblfitness_user
            WHERE iduser = :user
        ', array(
            'event' => $idevent
        ));
        $this->redirect('eventmanager');
    }
}