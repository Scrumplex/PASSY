<?php

namespace PASSY;

/**
 * Class UserManager
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package PASSY
 */
class UserManager
{

    /**
     * UserManager constructor.
     * @param boolean $forceSSL if true, session will only be allowed over HTTPS
     */
    function __construct($forceSSL)
    {
        PASSY::$userManager = $this;
        ini_set('session.cookie_lifetime', 60 * 60 * 24 * 90); // 90 days
        ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 90); // 90 days
        if ($forceSSL)
            ini_set("session.cookie_secure", true);

        session_start();
    }

    /**
     * Tracks user activity. Used for logging user out after 300s.
     */
    function trackActivity()
    {
        $_SESSION["last_activity"] = time();
    }

    /**
     * If user is inactive too long he will be logged out
     */
    function checkSessionExpiration()
    {
        if (!$this->isAuthenticated())
            return;
        if ($this->getSessionExpirationTime() != 0 && (time() - $_SESSION["last_activity"]) >= $this->getSessionExpirationTime())
            $this->_logout();
    }

    /**
     * Checks user credentials. If credentials are correct it will create a valid session.
     * @param $username string
     * @param $password string
     * @return Response
     */
    function _login($username, $password)
    {
        $mysql = PASSY::$db->getInstance();
        $ps = $mysql->prepare("SELECT * FROM `users` WHERE `USERNAME` = (?)");
        $ps->bind_param("s", $username);
        $succeeded = $ps->execute();
        $result = $ps->get_result();
        $ps->close();
        if ($succeeded) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hashedPassword = hash("SHA512", $password . $row["SALT"]);
                if ($hashedPassword == $row['PASSWORD']) {
                    // Save important data in session
                    $_SESSION["username"] = $username;
                    $_SESSION["master_password"] = $password;
                    $_SESSION["userId"] = $row['USERID'];
                    $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
                    $_SESSION["session_expiration"] = 300; // 5 mins

                    $this->trackActivity();
                    PASSY::$tasks->run(); // Run tasks every time, someone logs in successfully.
                    return new Response(true, array());
                }
            }
            return new Response(false, "invalid_credentials");
        }
        return new Response(false, "database_error");
    }

    /**
     * Creates a user with specified $username and $password.
     * @param $username string
     * @param $password string
     * @return Response
     */
    function _register($username, $password)
    {
        $userId = uniqid("user_");
        $salt = hash("SHA512", uniqid());
        $hashedPassword = hash("SHA512", $password . $salt);

        $mysql = PASSY::$db->getInstance();
        $ps = $mysql->prepare("SELECT * FROM `users` WHERE `USERNAME` = (?)");
        $ps->bind_param("s", $username);
        $succeeded = $ps->execute();
        $result = $ps->get_result();
        $ps->close();
        if ($succeeded) {
            if ($result->num_rows == 0) {
                $ps = $mysql->prepare("INSERT INTO `users` (`USERNAME`, `USERID`, `PASSWORD`, `SALT`) VALUES (?, ?, ?, ?)");
                $ps->bind_param("ssss", $username, $userId, $hashedPassword, $salt);
                $succeeded = $ps->execute();
                $ps->close();
                if ($succeeded)
                    return new Response(true, array());
                return new Response(false, "database_error");
            }
            return new Response(false, "username_exists");
        }
        return new Response(false, "database_error");
    }

    /**
     * Collects data, about the current status of the user's session.
     *
     * @return Response
     */
    function _status()
    {
        if ($this->getSessionExpirationTime() != 0)
            $ttl = $this->getSessionExpirationTime() - (time() - $this->getLastActivity());
        else
            $ttl = 0;

        $twoFactor = array();

        $userId = $this->getUserID();
        $mysql = PASSY::$db->getInstance();
        $ps = $mysql->prepare("SELECT * FROM `twofactor` WHERE `USERID` = (?)");
        $ps->bind_param("s", $userId);
        $succeeded = $ps->execute();
        $result = $ps->get_result();
        $ps->close();
        if ($succeeded) {
            $enabled = $result->num_rows > 0;
            $date = null;
            if ($enabled) {
                $row = $result->fetch_assoc();
                $date = $row["DATE"];
            }
            $twoFactor = array(
                "enabled" => $enabled,
                "date" => $date
            );
        }

        $response = new Response(true, array(
            "logged_in" => $this->isAuthenticated(),
            "last_activity" => $this->getLastActivity(),
            "ttl" => $ttl,
            "user_id" => $this->getUserID(),
            "two_factor" => $twoFactor
        ));
        return $response;
    }

    /**
     * Updates username of $userId
     * @param $userId
     * @param $newUsername
     * @return Response
     */
    function _changeUsername($userId, $newUsername)
    {
        $mysql = PASSY::$db->getInstance();
        $ps = $mysql->prepare("SELECT `USERID` FROM `users` WHERE `USERNAME` = (?)");
        $ps->bind_param("s", $newUsername);
        $succeeded = $ps->execute();
        $result = $ps->get_result();
        $ps->close();
        if ($succeeded) {
            if ($result->num_rows == 0) {
                $ps = $mysql->prepare("UPDATE `users` SET `USERNAME` = (?) WHERE `USERID` = (?)");
                $ps->bind_param("ss", $newUsername, $userId);
                $succeeded = $ps->execute();
                $ps->close();
                if ($succeeded)
                    return new Response(true, array());
                return new Response(false, "database_error");
            }
            return new Response(false, "username_exists");
        }
        return new Response(false, "database_error");
    }

    /**
     * Updates password of $userId. Re encrypts all passwords with new passwords.
     * @param $userId
     * @param $masterPassword
     * @param $newPassword
     * @return Response
     */
    function _changePassword($userId, $masterPassword, $newPassword)
    {
        if (PASSY::$twoFactor->isEnabled($userId))
            return new Response(false, "2fa_enabled");

        $mysql = PASSY::$db->getInstance();
        $ps = $mysql->prepare("SELECT `SALT` FROM `users` WHERE `USERID` = (?)");
        $ps->bind_param("s", $userId);
        $succeeded = $ps->execute();
        $result = $ps->get_result();
        $ps->close();
        if ($succeeded && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $salt = $row["SALT"];
            $hashedPassword = hash("SHA512", $newPassword . $salt);

            $ps = $mysql->prepare("UPDATE `users` SET `PASSWORD` = (?) WHERE `USERID` = (?)");
            $ps->bind_param("ss", $hashedPassword, $userId);
            $succeeded = $ps->execute();
            if ($succeeded)
                return PASSY::$passwords->_reencryptPasswords($userId, $masterPassword, $newPassword);
            return new Response(true, array());
        }
        return new Response(false, "database_error");
    }

    /**
     * Destroys current session, if present.
     * @return Response
     */
    function _logout()
    {
        if (session_status() != PHP_SESSION_NONE)
            session_destroy();

        return new Response(true, array());
    }

    /**
     * Checks if given $password is the same as password used to login and en/decrypt.
     * @param $password
     * @return bool
     */
    function checkPassword($password)
    {
        if ($this->getMasterPassword() == null)
            return false;
        return $password == $this->getMasterPassword();
    }

    /**
     * Time the session will take to expire in seconds.
     * 0 = until the session cookie expires.
     * @param $seconds
     */
    function setSessionExpirationTime($seconds)
    {
        $_SESSION["session_expiration"] = $seconds;
    }

    function getSessionExpirationTime()
    {
        if ($this->isAuthenticated())
            return $_SESSION["session_expiration"];
        return null;
    }

    function getUserID()
    {
        if ($this->isAuthenticated())
            return $_SESSION["userId"];
        return null;
    }

    function getLastActivity()
    {
        if ($this->isAuthenticated())
            return $_SESSION["last_activity"];
        return null;
    }

    function getMasterPassword()
    {
        if ($this->isAuthenticated())
            return $_SESSION["master_password"];
        return null;
    }

    function isAuthenticated()
    {
        if (session_status() == PHP_SESSION_NONE)
            return false;
        if (!isset($_SESSION["username"]) || !isset($_SESSION["master_password"]) || !isset($_SESSION["userId"]) || !isset($_SESSION["ip"]) || !isset($_SESSION["last_activity"]) || !isset($_SESSION["session_expiration"]))
            return false;
        if ($_SESSION["ip"] != $_SERVER["REMOTE_ADDR"])
            return false;
        return true;
    }

}