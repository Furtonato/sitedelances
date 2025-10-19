<?phpob_start();

require_once "../../../system/conecta.php";
require_once "../../../system/mysql.php";
include_once '../../../app/Funcoes/funcoes.php';

$mysql = new Mysql();

$arr = array();
$arr['html'] = '';


if (isset($_POST['id']) and $_POST['id']) {

	$mysql->colunas = 'lances, lances_cadastro, lances_plaquetas, lances_data';
	$mysql->filtro = " where " . STATUS . " AND id = '" . $_POST['id'] . "' ";
	$lotes = $mysql->read_unico('lotes');

	if (isset($lotes->lances) and $lotes->lances) {
		$arr['html'] .= '<tr class="back_hover_F7F7F7"> ';
		$arr['html'] .= '<td class="p10 tac">' . tipo_lance($lotes->lances_plaquetas) . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . lances_cadastro($lotes->lances_cadastro, $lotes->lances_plaquetas) . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . data($lotes->lances_data) . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . data($lotes->lances_data, 'H:i:s') . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . preco($lotes->lances, 1) . '</td> ';
		$arr['html'] .= '</tr>';
	}


	$mysql->colunas = '*';
	$mysql->filtro = " WHERE lotes = " . $_POST['id'] . " ORDER BY lances DESC ";
	$lotes_lances = $mysql->read('lotes_lances');

	foreach ($lotes_lances as $key => $value) {
		$arr['html'] .= '<tr class="back_hover_F7F7F7"> ';
		$arr['html'] .= '<td class="p10 tac">' . tipo_lance($value->plaquetas) . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . lances_cadastro($value->cadastro, $value->plaquetas) . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . data($value->data) . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . data($value->data, 'H:i:s') . '</td> ';
		$arr['html'] .= '<td class="p10 tac">' . preco($value->lances, 1) . '</td> ';
		$arr['html'] .= '</tr>';
	}
}

if (!$arr['html']) {
	$arr['html'] .= '<tr class="back_hover_F7F7F7"><td colspan="5" class="p10 tac">' . lang('Nenhum Lance Encontrado') . '...</td></tr>';
}


echo json_encode($arr);
