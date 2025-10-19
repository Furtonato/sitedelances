<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administração</title>
</head>

<body>
    <?php if(isset($_GET['cod'])){ ?>
        <?
            switch($_GET['cod']){
            case 1:
                echo "<p>Login incorreto!</p>";
            break;
            case 2:
                echo "<p>Senha Incorreta!</p>";
            break;
			case 3:
                echo "<p>Você não está logado!</p>";
            break;
            default:
                echo "<p>Entre em contato com o suporte!</p>";
            }
        ?>
    <?php  } ?>

    <h3>Administração</h3>

    <form action="autenticacao.php" method="post">
        <label>Login: &nbsp; <input type="text" id="login" name="login" /></label>
        <br><br>
        <label>Senha: &nbsp; <input type="password" id="senha" name="senha" /></label>
        <br><br>
        <button type="submit">Logar</button>
    </form>

</body>
</html>