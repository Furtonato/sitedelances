<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['html'] = '';


		if(isset($_POST['id']) and $_POST['id']){

			$mysql->filtro = " where id = '".$_POST['id']."' ";
			$pedidos = $mysql->read_unico('pedidos');

			$_POST['mobile'] = isset($_POST['mobile']) ? $_POST['mobile'] : 0;

			$arr['html'] .= '<div class="w100p h100p o-a posf pl10 pr10 z9 t0 l0 cor_fff PagSeguro_boxs" style="font-family: Arial">
								<a href="javascript:fechar_all_pagamento()" class="posf t5 r30 fz30 cor_bbb"><i class="icon icon-close"></i></a>
								<div class="w400 mobile_400_w300 m-a o-h">
									<div class="w1000">

										<div class="abas fll w400 mobile_400_w300 efeito">
											<div class="pt70 fz28 tac"> Qual a forma  de pagamento? </div>
											<div class="pt20 fz16 tac"> Total a pagar <span class="cor_0f6beb">R$'.preco($pedidos->valor_total).'</span> </div>

											<ul class="pt70">
												<li onclick="PagSeguro_abas('.A.'credito'.A.')" >
													<a class="brt6">
														<span class="nome">Cartão de crédito</span>
														<span class="seta efeito"><i class="fz30 icon icon-arrow-right"></i></span>
														<div class="clear"></div>
													</a>
												</li>
												<li onclick="PagSeguro_abas('.A.'debito'.A.'); PagSeguro_Debito_pagar('.$_POST['id'].');" >
													<a>
														<span class="nome">Transferência on-line</span>
														<span class="seta efeito"><i class="fz30 icon icon-arrow-right"></i></span>
														<div class="clear"></div>
													</a>
												</li>
												<li onclick="PagSeguro_abas('.A.'boleto'.A.'); PagSeguro_Boleto_pagar('.$_POST['id'].');" >
													<a class="brb6">
														<span class="nome">Boleto bancário</span>
														<span class="seta efeito"><i class="fz30 icon icon-arrow-right"></i></span>
														<div class="clear"></div>
													</a>
												</li>
												<div class="clear"></div>
											</ul>

										</div>


										<ul class="w400 mobile_400_w300 pagamentos fll">
											<li class="credito d">
												<form method="post" action="javascript:void(0)" onsubmit="PagSeguro_Credito('.A.$pedidos->id.A.', this)" enctype="multipart/form-data">
													<input type="hidden" id="valor_total_para_parcelamento" value="'.$pedidos->valor_total.'">
													<input type="hidden" name="bandeiras">
													<input type="hidden" name="mobile" value="'.$_POST['mobile'].'">
													<div class="pt70 fz28 tac"> Informe seus dados  de cartão </div>
													<div class="pt20 fz16 tac"> Total a pagar <span class="cor_0f6beb">R$'.preco($pedidos->valor_total).'</span> </div>
													<ul class="pt20 fz16 tac">
														<li class="dib fz42 lh36 c-p cor_333" onclick="PagSeguro_abas_ini()">•</li>
														<li class="dib fz42 lh36 c-p cor_fff">•</li>
													</ul>

													<div id="card" class="cartao w387 mobile_400_w300 h260 dn_500">
														<div class="frente">
															<p class="efeito valido_ate">Valido até</p>
															<p class="efeito numero">•••• •••• •••• ••••</p>
															<p class="efeito validade">MM/AA</p>
															<p class="efeito nome">NOME COMPLETO</p>
														</div>
														<div class="verso">
															<p class="cvv">•••</p>
														</div>
													</div>
													<div class="dados w387 mobile_400_w300">
														<input value="4985820183005026" type="text" name="numero" class="cartao_numero w100p h52 pl20 pr20 mb5 design fz18 br6" placeholder="Número" required onkeyup="PagSeguro_Credito_dados('.A.'numero'.A.', this)" onfocus="PagSeguro_Credito_dados('.A.'numero'.A.', this)" onBlur="PagSeguro_Credito_dados('.A.'numero'.A.', this)" >
														<input type="text" name="nome" class="cartao_nome w100p h52 pl20 pr20 mb5 design fz18 br6" placeholder="Nome (igual no cartão)" required onkeyup="PagSeguro_Credito_dados('.A.'nome'.A.', this)" >
														<div class="mb5">
															<input type="text" name="validade" class="cartao_validade w220 h52 fll pl20 pr20 mr4 design fz18 br6" placeholder="Validade" required onkeyup="PagSeguro_Credito_dados('.A.'validade'.A.', this)" >
															<input type="text" name="cvv" class="cartao_cvv w160 h52 fll pl15 pr15 design fz18 br6" placeholder="CVV" required onkeyup="PagSeguro_Credito_dados('.A.'cvv'.A.', this)" >
															<div class="clear"></div>
														</div>
														<div class="mb5">
															<select name="parcelas" class="cartao_parcelas w220 h52 fll mr4 design fz17 br6" required style="letter-spacing:-1px;"></select>
															<input type="text" class="cartao_cpf w160 h52 fll pl15 pr15 design fz18 br6 cpf" name="cpf" placeholder="CPF" required>
															<div class="clear"></div>
														</div>
														<button class="hover p20 bd0 br6">
															<div class="wf9"> Pagar </div>
															<div class="wf3 tar"> <i class="fz20 icon icon-arrow-right"></i> </div>
															<div class="clear"></div>
														</button>
														<div class="h20 clear"></div>
													</div>
													<script> PagSeguro_Credito_dados_focus(); </script>
												</form>
											</li>


											<li class="debito dn">
												<div class="pt70 fz28 tac"> Escolha o cartão de débito </div>
												<div class="pt20 fz16 tac"> Total a pagar <span class="cor_0f6beb">R$'.preco($pedidos->valor_total).'</span> </div>
												<ul class="pt20 fz16 tac">
													<li class="dib fz42 lh36 c-p cor_333" onclick="PagSeguro_abas_ini()">•</li>
													<li class="dib fz42 lh36 c-p cor_fff">•</li>
												</ul>
												<div class="pagar_no_debito pt20"></div>
											</li>


											<li class="boleto dn">
												<div class="pt70 fz28 tac"> Boleto bancário </div>
												<div class="pt20 fz16 tac"> Total a pagar <span class="cor_0f6beb">R$'.preco($pedidos->valor_total).'</span> </div>
												<ul class="pt20 fz16 tac">
													<li class="dib fz42 lh36 c-p cor_333" onclick="PagSeguro_abas_ini()">•</li>
													<li class="dib fz42 lh36 c-p cor_fff">•</li>
												</ul>
												<div class="gerar_boleto pt20"></div>
											</li>
										</ul>
									</div>

								</div>
							</div> ';


		}

	echo json_encode($arr); 

?>