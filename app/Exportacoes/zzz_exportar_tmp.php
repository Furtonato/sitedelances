nao estou usando esse arquivo

exemplo de css no pdf

$html = ob_get_clean();
ob_end_clean(); // Finaliza o fluxo

include("../../MPDF57/mpdf.php");

// cria um novo container PDF no formato A4 com orientação customizada
$mpdf=new mPDF('pt','A4',3,'',8,8,5,14,9,9,'P');

// muda o charset para aceitar caracteres acentuados iso 8859-1 utilizados por mim no banco de dados e na geracao do conteudo PHP com HTML
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='iso-8859-1';

//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização
$rodape = '{DATE j/m/Y H:i}|{PAGENO}/{nb}| Gerado automaticamente pelo Sistema';
$mpdf->SetFooter($rodape);
//bacana este rodape, nao eh mesmo?

// carrega uma folha de estilo - MAGICA!!!
$stylesheet1 = file_get_contents('../../css/stgeral.css');
$stylesheet2 = file_get_contents('../../bootcocari/css/bootstrap.min.css');
$stylesheet3 = file_get_contents('../../bootcocari/css/bootstrap.icon-large.css');
$stylesheet4 = file_get_contents('../css/styleCurriculo.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet1,1);
$mpdf->WriteHTML($stylesheet2,1);
$mpdf->WriteHTML($stylesheet3,1);
$mpdf->WriteHTML($stylesheet4,1);

// incorpora o corpo ao PDF na posição 2 e deverá ser interpretado como footage. Todo footage é posicao 2 ou 0(padrão).
$mpdf->WriteHTML($html,2);

// define um nome para o arquivo PDF
$arquivo = date("dmy_"). '_curriculos.pdf';

// gera o relatório
$mpdf->Output($arquivo,'I');

exit();