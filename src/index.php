<?php

use Lzz\Utils\Utils;

require_once $_SERVER['DOCUMENT_ROOT'] . "/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/structure/header.php";

?><div class="center"><?php

    if ($lzz->account->loggedIn()) {

        ?>
            <form method="POST" action="">
                Ваша Ссылка:<br><br><input type="url" name="link" placeholder="Ваша ссылка" maxlength="1024" required="" style="margin-left:auto;margin-right:auto;width:70%;"><br>
                <input type="submit" name="submit" value="Сократить ссылку!" class="btn no-border">
            </form>
        <?php

    }
    else {

        Utils::headerWarning('Сокращать ссылки могут только авторизованные пользователи!<br>Пожалуйста, <a href="/account/register">зарегистрируйтесь</a>, чтобы получить возможность сокращать неограниченное количество ссылок!');

    }

    if (isset($_POST['link'])) {

        $userUrl = urldecode($_POST['link']);

        if (!$lzz->account->loggedIn()) {

            Utils::headerError("Ошибка! Вы не авторизовались!");

        }
        elseif (strlen($userUrl) > 1024) {

            Utils::headerError("Ошибка! Слишком длинный URL.");

        }
        else {

            $db = Utils::getDatabase();

            $query = $db->prepare('SELECT `id`, `dateLastLink`, `linksShortenedToday` FROM `users` WHERE `id` = ? LIMIT 1;');

            $query->execute([
                $lzz->account->tokenPayload['id']
            ]);

            $user = $query->fetch(PDO::FETCH_ASSOC);

            if (!$query->rowCount()) {

                Utils::headerError("Произошла ошибка.");

            }
            else {

                $canShortenLink = false;

                if ((int)$user['linksShortenedToday'] <= 100) {

                    $canShortenLink = true;

                }

                if (!$canShortenLink) {

                    $userLastLinkDay = date('Y-m-d', (int)$user['dateLastLink']);
                    $currentDay = date('Y-m-d', time());

                    $canShortenLink = $userLastLinkDay !== $currentDay;

                    if ($canShortenLink) {

                        $query = $db->prepare('UPDATE `users` SET `linksShortenedToday` = 0 WHERE `id` = ? LIMIT 1;');

                        $query->execute([
                            $lzz->account->tokenPayload['id']
                        ]);

                    }

                }

                if (!$canShortenLink) {

                    Utils::headerError("Ошибка! Ежедневно нельзя сокращать больше 100 ссылок.");

                }
                elseif (!Utils::validateAndCleanUrl($userUrl)) {

                    Utils::headerError("Ошибка! Некорректный URL.");

                }
                else {

                    $query = $db->prepare('INSERT INTO `links` (`user`, accessed, link, `date`, edited, ip) VALUES (?,?,?,?,?,?);');

                    $query->execute([
                        $lzz->account->tokenPayload['id'],
                        0,
                        $userUrl,
                        time(),
                        0,
                        Utils::getUserIpAddress()
                    ]);

                    $linkId = $db->lastInsertId();

                    $query = $db->prepare('UPDATE `users` SET `dateLastLink` = ?, `linksShortened` = `linksShortened` + 1, `linksShortenedToday` = `linksShortenedToday` + 1 WHERE `id` = ? LIMIT 1;');

                    $query->execute([
                        time(),
                        $lzz->account->tokenPayload['id']
                    ]);

                    $linkShortCode = \Lzz\Utils\UrlUtils::linkIdToShortCode($linkId);

                    ?>
                    <input type="text" name="readyLink" style="text-align: center;font-size: 25px;border: 1px solid #fafafa;margin-left:auto;margin-right:auto;" value="lllz.ru/<?= $linkShortCode; ?>" onclick="this.select();">
                    <?php

                    Utils::headerSuccess("Ваша ссылка готова!<br>Любой пользователь, который перейдёт по ней, перейдёт автоматически по Вашей исходной ссылке.");

                }

            }

        }
    }

	?><p class="text">Link-ZZ - сервис, который позволяет сокращать ссылки так, чтобы они смотрелись красиво в любом контексте.</p>
	<p class="text">Допустим у Вас есть длинная ссылка, вроде:</p>
	<pre class="code-pre command">
        <ul class="code-ul prefixed">
			<li class="line" prefix="До:"><span class="highlight">http://example.com/example/directory/examplefile?id=3285&amp;page=76&amp;track=1</span></li>
        </ul>
    </pre>
	<p class="text">Врядли Вы захотите подобную ссылку вставлять, например, в письмо или в пост ВКонтакте.</p>
	<p class="text">Просто зайдите на наш сайт, вставьте ссылку в форму выше и Вы сразу получите её укороченную версию, которая <span class="text-underline">не отличается</span> от Вашей исходной ссылки, кроме того, что она выглядит гораздо красивее:</p>
	<pre class="code-pre command">
        <ul class="code-ul prefixed">
			<li class="line" prefix="После:"><span class="highlight">http://lllz.ru/bc6</span></li>
        </ul>
    </pre>
</div><?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/structure/footer.php";