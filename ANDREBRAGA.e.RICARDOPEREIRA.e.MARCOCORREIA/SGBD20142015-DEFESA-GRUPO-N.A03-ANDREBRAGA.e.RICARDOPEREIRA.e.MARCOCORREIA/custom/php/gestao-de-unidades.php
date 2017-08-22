<?php	
	require_once("custom/php/common.php");
	
	if(!is_user_logged_in() || !current_user_can('manage_unit_types'))
	{
?>
		<p>Não tem permissão para aceder a esta página. Tem de efetuar <a href=><?php wp_loginout("gestao-de-unidades")?> </a>.</p>
<?php         
	}
	else
	{
		if(!isset($_REQUEST['state']) || $_REQUEST['state'] == "") 
		{
			$sql_prop_unit_type = "SELECT * FROM prop_unit_type";
			$res_prop_unit_type = mysql_query($sql_prop_unit_type);
			if(mysql_num_rows($res_prop_unit_type) == 0)
				echo "Não há tipos de unidades.";
			else
			{		
?>
				<table>
				 <tr>
				  <th>id</th>
				  <th>unidade</th>
				 </tr>
<?php	
					while($linha = mysql_fetch_array($res_prop_unit_type))//é guardado na variavel linha um array de resultado.
					{
?>
						<tr>
						 <td> <?php echo $linha['id']; ?> </td>
						 <td> <?php echo $linha['name']; ?> </td>
						</tr>
<?php 
					} 
?>
				</table>
<?php				//inserção de um novo tipo de unidade
			} 
?>
			    <h3><b>Gestao de Unidades - Introducao</b></h3>
			
			    <form name="gestao_de_unidades" method="POST">
			    <fieldset>
				<legend>Registar Unidade:</legend>
				
				<p>
				<label><b>Nome:</b></label>
				<input type="text" name="nome">
				</p>
				
				<input type="hidden" name="state" value="inserir">
				<p>
				<input type="submit" value="Inserir unidade">
				</p>
			    
				</fieldset>
			    </form>
			
<?php           
		}
		else if($_REQUEST['state'] == "inserir") // Verifica o estado se é igual a inserir.
		{
?>
			<h3><b>Gestao de unidades - Insercao</b></h3>
<?php
			$nome_inserido = $_REQUEST['nome'];
			
			if(empty($nome_inserido))
			{
?>			
				<p>Tem de inserir o nome da unidade.<p>
<?php			
				back();
				
			}
			else
			{
						
				$inserir_unidade = sprintf('INSERT INTO `prop_unit_type` (`name`) VALUES ("%s")', mysql_real_escape_string($nome_inserido));
				$resultado_inserido = mysql_query($inserir_unidade);
?>
				<p>Inseriu os dados de novo tipo de unidade com sucesso.</p>
				<p>Clique em <a href="gestao-de-unidades">Continuar</a> para avancar.</p>
<?php	
	
			}
		}
	}
?>