<html>
  <head>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  </head>
  <body>
    <table width="100%" cellspacing="0" cellpadding="8" border="0">
      <tbody>
        <tr>
          <td width="80"><img src="https://toptoy.ru/images/tt-mail.logo.png"
              alt="Toptoy.ru" longdesc="https://toptoy.ru"
              moz-do-not-send="false" width="80" height="55"></td>
          <td><strong style="font-family:Verdana, Geneva, sans-serif;
              color:#FF6E40">Для детей и родителей</strong></td>
        </tr>
      </tbody>
    </table>
    <h2><?=$caption?></h2>
    <?=$text?>
    <table style="background:#FF6E40; color:white; font-family:Verdana,
      Geneva, sans-serif" width="100%" cellspacing="0" cellpadding="16"
      border="0">
      <tbody>
        <tr>
          <td>Служба поддержки клиентов по телефону:</td>
          <td><a style="color:#FFF" href="tel:<?=$phone?>"
              itemprop="telephone"><?=$phone?></a></td>
        </tr>
        <tr>
          <td>Служба поддержки клиентов по электронной почте:</td>
          <td><a style="color:#FFF"
              href="mailto:order@toptoy.ru?subject=Заказ №<?=$orderNumber?>.'">order@toptoy.ru</a></td>
        </tr>

        <tr>
          <td>Состояние и статус заказа в личном кабинете:</td>
          <td><a style="color:#FFF" href="<?= $buttonUrl ?>" target="_blank">Личный
              кабинет</a></td>
        </tr>
      </tbody>
    </table>
  </body>
</html>