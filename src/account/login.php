<?php

use Lzz\Utils\Utils;
use Lzz\Account\Account;

require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';

if ($lzz->account->loggedIn()) {
    header('Location: /account/cabinet');
    exit(0);
}

$returnLogin = '';

if (isset($_GET['name'])) {
	$returnLogin = Utils::escapeHTML($_GET['name']);
}

if (
    isset($_POST['name']) &&
    isset($_POST['password']) &&
    isset($_POST['g-recaptcha-response'])
) {

	$ip = Utils::getUserIpAddress();
	$name = Utils::escapeHTML(trim($_POST['name']));

	$secret = '<google recaptcha secret key>';
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response'].'&remoteip='.$ip);
	$responseData = json_decode($verifyResponse);

	if ($responseData->success) {

	    $db = Utils::getDatabase();
		
		$query = $db->prepare('SELECT * FROM `users` WHERE `name` = ? LIMIT 1;');
		$query->execute([$name]);

		$result = $query->fetch(PDO::FETCH_ASSOC);

		if (isset($result['id'])) {

			$password = trim($_POST['password']);

			// unknown password salted with the right salt
			$hashedPassword = Account::hashPassword($password, $result['salt']);

			// if the salted user password matches the unknown salted password
			if (hash_equals($hashedPassword, $result['password'])) {

				$userId = (int)$result['id'];

				$query = $db->prepare('UPDATE `users` SET `ipLastLogin` = ?, `dateLastLogin` = ? WHERE `id` = ?;');
				$query->execute([
                    Utils::getUserIpAddress(),
                    time(),
                    $userId
                ]);

				$jwt = Account::generateToken(json_encode([
                    'id' => $userId,
                    'name' => $result['name']
                ]));

				setcookie('zl', $jwt, time()+(86400 * 30), '/', null);

                header('Location: /account/cabinet');

                exit(0);
			}
		}

        require_once $_SERVER['DOCUMENT_ROOT'] . '/structure/header.php';
        Utils::headerError('Ошибка! Вы ввели неверный логин и/или пароль!');
	}
	else {
		$returnLogin = $name;
        require_once $_SERVER['DOCUMENT_ROOT'] . '/structure/header.php';
		Utils::headerError('Ошибка! Вы не прошли капчу. Пожалуйста, докажите, что Вы человек, а не робот!');
	}
}
else {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/structure/header.php';
}

?>
    <section>Вход:<hr></section>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <form method="POST" action="">
        Логин:<br><br><input type="text" name="name" placeholder="Ваш логин" size="35" maxlength="15" required="" value="<?php echo $returnLogin; ?>"><br>
        Пароль:<br><br><input type="password" name="password" placeholder="Ваш пароль" size="35" maxlength="100" required=""><br>
        <div class="g-recaptcha" data-sitekey="<google recaptcha public key>"></div><br>
        <input type="submit" name="submit" value="Войти" class="btn no-border" style="margin-left: 0;">
    </form>
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/structure/footer.php';