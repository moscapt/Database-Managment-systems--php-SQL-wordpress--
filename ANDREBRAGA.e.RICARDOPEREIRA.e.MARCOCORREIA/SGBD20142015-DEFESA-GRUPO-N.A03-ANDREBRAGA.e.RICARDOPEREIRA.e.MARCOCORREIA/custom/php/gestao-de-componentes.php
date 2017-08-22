<?php	
	require_once("custom/php/common.php");
	
	if(!is_user_logged_in() || !current_user_can("manage_components"))
	{
?>
		<p>Nao tem autorizacao para aceder a esta pagina. Tem de efetuar <a href=><?php wp_loginout("gestao-de-componentes")?> </a>.</p>
<?php	
	}	
	else
	{
		if($_REQUEST["estado"] == "")
		{
			$query_component = "SELECT * FROM component";
			$result_component = mysql_query($query_component);
			
			if(mysql_num_rows($result_component) == 0)
			{
				echo "Nao ha componentes.";
			}			
			else
			{
?>			
				<table class = "mytable">
				 <thead>
				  <tr>
				   <th>tipo</th>
				   <th>id</th>
				   <th>nome</th>
				   <th>estado</th>
				   <th>acao</th>
				  </tr>
				 </thead>
			 
				 <tbody>
<?php
			
					$query_comp_type = "SELECT id, name FROM comp_type GROUP BY id";
					$result_comp_type = mysql_query($query_comp_type);
			
					while($array_comp_type = mysql_fetch_array($result_comp_type))
					{
						$query_componentes_final = "SELECT component.id, component.name, component.state 
												FROM component, comp_type 
												WHERE component.comp_type_id = ".$array_comp_type["id"]."
												GROUP BY component.id";
														
						$result_componentes_final = mysql_query($query_componentes_final);
				
						$rows_componentes = mysql_num_rows($result_componentes_final);
						
						if($rows_componentes > 0)
						{
?>						 
							<tr>
							 <td colspan = "1" rowspan = "<?php echo $rows_componentes; ?>"> <?php echo $array_comp_type["name"]; ?> </td>
<?php						
							while($array_componentes_final = mysql_fetch_array($result_componentes_final))
							{
?>
								<td> <?php echo $array_componentes_final["id"] ?></td>
								<td> <?php echo $array_componentes_final["name"] ?></td>
<?php						
								if($array_componentes_final["state"] == "active")
								{
?>							
									<td>ativo</td>
									<td>[editar] [desativar]</td>
<?php
								}
								else
								{
?>							
								<td>inativo</td>
								<td>[editar] [desativar]</td>
<?php
								}							
?>
							</tr>
<?php						}									
						}				
					}			
?>
				 </tbody>
				</table>			
<?php					
			}
?>			
			<h3><b>Gestao de componentes - Introducao<b></h3>
			
			<form name="gestao_de_componentes" method "POST">
			<fieldset>
			<legend>Introduzir dados:</legend>
			
			<p>
			<label><b>Nome:</b></label>
			<input type="text" name="component_name">
			</p>
			
			<p>
			<label><b>Tipo:</b></label>
			<br>
			<input type="radio" name="comp_type" value="1"> <label>Propriedade</label>
			<br>
			<input type="radio" name="comp_type" value="2"> <label>Canal de venda</label>
			<br>
			<input type="radio" name="comp_type" value="3"> <label>Fornecedor</label>
			</p>
			
			<p>
			<label><b>Estado:</b></label>
			<br>
			<input type="radio" name="component_state" value="active"> <label>Ativo</label>
			<br>
			<input type="radio" name="component_state" value="inactive"> <label>Inativo</label>
			</p>
			
			<input type="hidden" name="estado" value="inserir">
			<p>
			<input type="submit" value="Inserir componente">
			</p>
			
			</fieldset>
			</form>		
			
<?php			
		}		
		elseif($_REQUEST["estado"] == "inserir")
		{
?>		
			<h3><b>Gestao de componentes - Insercao</b></h3>
<?php	
			$component_name = $_REQUEST["component_name"];
			$comp_type = $_REQUEST["comp_type"];
			$component_state = $_REQUEST["component_state"];
			
			if(empty($component_name))
			{
?>			
				<p>Tem de inserir o nome do componente.<p>
<?php			
				back();
				
			}						
			elseif(is_null($comp_type))
			{
?>			
				<p>Tem de inserir o tipo de componente.<p>
<?php			
				back();
				
			}			
			elseif(is_null($component_state))
			{
?>			
				<p>Tem de inserir o estado do componente.<p>
<?php			
				back();
				
			}
			else
			{
			
				$query_insert_string = sprintf('INSERT INTO `component` (`name`, `comp_type_id`, `state`) VALUES ("%s", "%s", "%s");',
				mysql_real_escape_string($component_name), mysql_real_escape_string($comp_type), mysql_real_escape_string($component_state));
			
				$result_insert = mysql_query($query_insert_string);
			
?>
				<p>Inseriu os dados de novo componente com sucesso.</p>
				<p>Clique em <a href="gestao-de-componentes">Continuar</a> para avancar.</p><br>
<?php			
			}			
		}
	}
?>
