<?php	
	require_once("custom/php/common.php");
	
	if(!is_user_logged_in() || !current_user_can('manage_properties'))
	{
?>
	    <p>Não tem autorização para aceder a esta página. Tem de efetuar <a href=><?php wp_loginout("wordpress/gestao-de-componentes")?> </a>.</p>
<?php
	}
	else
	{
		if($_REQUEST['estado'] == "")
		{
			$query_proper = "SELECT * FROM property";
			$result_proper = mysql_query($query_proper);
			
			if (mysql_num_rows($result_proper) == 0)
			{
				echo "Não há propiedades especificadas";
			}
			else
			{
				$query_comp = " SELECT component.id, component.name
								FROM component
								GROUP BY name";
				$result_comp = mysql_query($query_comp);
?>								
				<table class="mytable">
				<thead>
				<tr>
				<th>componente</th>
				<th>id</th>
				<th>propriedade</th>
				<th>tipo de valor</th>
				<th>nome do campo no formulario</th>
				<th>tipo do campo no formulario</th>
				<th>tipo de unidade</th>
				<th>ordem do campo no formulário</th>
				<th>tamanho do campo no formulario</th>
				<th>obrigatorio</th>
				<th>estado</th>
				<th>acao</th>
				</tr>
				</thead>
				
				<tbody>
<?php
				while($row_comp = mysql_fetch_array($result_comp))
				{
					$query_prop = " SELECT *
									FROM property
									WHERE component_id = ".$row_comp["id"]."
									GROUP BY property.id";
					$result_prop = mysql_query($query_prop);
					$num_prop = mysql_num_rows($result_prop);
				
?>
					<tr><td colspan ="1" rowspan="<?php echo $num_prop; ?>" > <?php echo $row_comp['name']; ?> </td> 
<?php

					while($row_prop = mysql_fetch_array($result_prop))
					{
?>
						<td> <?php echo $row_prop['id']; ?> </td>
						<td> <?php echo $row_prop['name']; ?> </td>
						<td> <?php echo $row_prop['value_type']; ?> </td>
						<td> <?php echo $row_prop['form_field_name']; ?> </td>
						<td> <?php echo $row_prop['form_field_type']; ?> </td>
<?php
						if($row_prop['unit_type_id'] != null)
						{
							$query_prop_unit_type = "SELECT name
												FROM prop_unit_type
												WHERE ".$row_prop['unit_type_id']." = id";
							$result_prop_unit_type = mysql_query($query_prop_unit_type);
							$array_prop_unit_type = mysql_fetch_array($result_prop_unit_type);
?>								
							<td> <?php echo $array_prop_unit_type['name']; ?> </td>
<?php	
						}						
						else
						{
?>						
							<td> - </td>
<?php							
						}
?>
						<td> <?php echo $row_prop['form_field_order']; ?> </td>
<?php 					
						if($row_prop['form_field_size'] != null)
						{
?>
							<td> <?php echo $row_prop['form_field_size']; ?> </td>
<?php							
						}
						else
						{
?>
							<td> - </td>
<?php						
						}
						
						if($row_prop['mandatory'] == 1)
						{
?>
							<td> sim </td>
<?php
						}
						else
						{
?>
							<td> não </td>
<?php
						}
						if($row_prop['state']== "active")
						{
?>						
							<td> activo </td>
							<td> [editar]<br>[desactivar]</td>						 
<?php
						}
						else
						{
?>
							<td> inactivo </td>
							<td> [editar]<br>[activar]</td>
<?php
						}
?>						
						</tr>
<?php						
					}
				}
?>
				</tbody>
				</table>
<?php
			}
		
			//inserção dos dados
?>		
			<h3><b>Gestão de propriedades - introdução</b></h3>
			
			<form name = "gestão_de_propriedades" method = "POST">
			<fieldset>
			<legend>Introduzir propriedades:</legend>
			
			<p>
			<label><b>Nome:</b></label>
			<input type = "text" name = "proper_name">
			</p>
			
			<p>
			<label><b>Tipo de valor:</b></label>
			<br>
	
<?php							
			$enum_array = getEnumValues("property", "value_type");
			foreach($enum_array as $artigo)
			{
				echo '<input type = "radio" name = "value_type" value ="'.$artigo.'">';
				echo '<label for ="'.$artigo.'"> '.$artigo.'</label><br>';				
			}				
?>	
			</p>
			
			<p>
			<label><b>Componente:</b></label>
			<br>
		
			<select name = "component_id">
<?php
			$query_component = "SELECT name, id
								FROM component";
			$resultado_component = mysql_query($query_component);					
			
			echo '<option value = ""></option>';
			
			while($row = mysql_fetch_array($resultado_component))
			{
				echo '<option value = "'.$row['id'].'"> '.$row['name'].'</option>';
			}
?>		
			</select>
			</p>
			
			<p>
			<label><b>Tipo do campo no formulário:</b></label>
			<br>		
<?php		
			$enum_array = getEnumValues("property", "form_field_type");
			foreach($enum_array as $artigo1)
			{
				echo '<input type = "radio" name = "form_field_type" value = "'.$artigo1.'">';
				echo '<label for = "'.$artigo1.'"> '.$artigo1.'</label><br>';
			}
?>	
			</p>
			
			<p>
			<label><b>Tipo de unidade:</b></label>
			<select name = "unit_type_id">
	
<?php		
			$query_unidade = "SELECT name, id FROM prop_unit_type";
			$result_unidade = mysql_query($query_unidade);
		
			echo '<option value = ""></option>';
			while($row = mysql_fetch_array($result_unidade))
			{
				echo '<option value = "'.$row['id'].'">'.$row['name'].'</option>';
			}
?>
			</select>
			</p>
	
			<p>
			<label><b>Ordem do campo no formulário:</b></label>
			<input type = "text" name = "form_field_order">
			</p>
			
			<p>
			<label><b>Tamanho do campo no formulário:</b></label>
			<input type = "text" name = "form_field_size">
			</p>
		
			<p>
			<label><b>Obrigatorio:</b></label>
			<br>
			<input type="radio" name="mandatory_prop" value="1"> <label>Sim</label>
			<br>
			<input type="radio" name="mandatory_prop" value="0"> <label>Não</label>
			</p>
	
			<input type = "hidden" name = "estado" value = "inserir">
			<p>
			<input type = "submit" value = "Inserir propriedades">
			</p>
	
			</fieldset>
			</form>
			
<?php	
		
		}
		elseif($_REQUEST['estado'] == "inserir")
		{	
			$proper_name = $_REQUEST['proper_name'];
			$value_type = $_REQUEST['value_type'];
			$component_id = $_REQUEST['component_id'];
			$form_field_type = $_REQUEST['form_field_type'];
			$unit_type_id = $_REQUEST['unit_type_id'];
			$form_field_order = $_REQUEST['form_field_order'];
			$form_field_size = $_REQUEST['form_field_size'];
			$mandatory_prop = $_REQUEST['mandatory_prop'];
			
			if(empty($proper_name))
			{
?>			
				<p>Tem de inserir o nome da propriedade.</p>
<?php			
				back();				
			}
			elseif(is_null($value_type))
			{			
?>
				<p>Tem de inserir o tipo de valor.</p>
<?php			
				back();
			}
			elseif(empty($component_id))
			{
?>			
				<p>Tem de escolher o componente.<p>
<?php			
				back();				
			}
			elseif(is_null($form_field_type))
			{
?>			
				<p>É necessário escolher o tipo do campo no formulário.</p>
<?php				
				back();			
			}
			elseif(!is_numeric($form_field_order))
			{
?>				
				<p>A ordem do campo no formulário tem de ser um número.</p>
<?php			
				back();
			}
			elseif(is_numeric($form_field_order) && $form_field_order<1)
			{
?>
				<p>A ordem do campo no formulário tem de ser maior que zero.</p>
<?php			
				back();
			}
			elseif(!($form_field_type =="text" || $form_field_type == "textbox") && (!is_numeric($form_field_size) && !empty($form_field_size)))
			{	
?>
				<p>O tamanho do campo no formulário tem de ser um número.</p>
<?php	
				back();
			}			
			elseif($form_field_type =="text" && !is_numeric($form_field_size))
			{
?>			
				<p>O tamanho do campo no formulário tem de ser um número.</p>
<?php			
				back();
			}
			elseif($form_field_type == "textbox" && !preg_match("/^[0-9]{2}x[0-9]{2}$/", $form_field_size))
			{
?>			
				<p>O tamanho do campo no formulário tem de estar na forma aaxbb, em que aa é o número de colunas e bb o número de linhas da caixa de texto.</p>
<?php			
				back(); 
				
			}
			elseif(is_null($mandatory_prop))
			{
?>
				<p>É necessario escolher a obrigatoriedade desta propriedade.</p>
<?php		
				back();
			}
			else
			{
?>		
				<h3><b>Gestão de propriedades - inserção</b></h3>
<?php			
					

					if($unit_type_id =="" && empty($form_field_size))
					{
						$inserir = sprintf(' INSERT INTO `property` (`name`, `value_type`, `form_field_type`, `form_field_order`, `mandatory`, `component_id`, `state`)
									VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s");', 					
						mysql_real_escape_string($proper_name),
						mysql_real_escape_string($value_type),
						mysql_real_escape_string($form_field_type),
						mysql_real_escape_string($form_field_order),					
						mysql_real_escape_string($mandatory_prop),
						mysql_real_escape_string($component_id),
						mysql_real_escape_string("active"));
						
					}
					elseif($unit_type_id =="" && !empty($form_field_size))
					{
						$inserir = sprintf(' INSERT INTO `property` (`name`, `value_type`, `form_field_type`, `form_field_order`, `form_field_size`, `mandatory`, `component_id`, `state`)
									VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s");', 					
						mysql_real_escape_string($proper_name),
						mysql_real_escape_string($value_type),
						mysql_real_escape_string($form_field_type),
						mysql_real_escape_string($form_field_order),
						mysql_real_escape_string($form_field_size),					
						mysql_real_escape_string($mandatory_prop),
						mysql_real_escape_string($component_id),
						mysql_real_escape_string("active"));
					
					}
					elseif(!($unit_type_id =="") && empty($form_field_size))
					{
						$inserir = sprintf(' INSERT INTO `property` (`name`, `value_type`, `form_field_type`, `unit_type_id`, `form_field_order`, `mandatory`, `component_id`, `state`)
									VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s");', 					
						mysql_real_escape_string($proper_name),
						mysql_real_escape_string($value_type),
						mysql_real_escape_string($form_field_type),
						mysql_real_escape_string($unit_type_id),
						mysql_real_escape_string($form_field_order),					
						mysql_real_escape_string($mandatory_prop),
						mysql_real_escape_string($component_id),
						mysql_real_escape_string("active"));
					
					}
					elseif(!($unit_type_id =="") && !empty($form_field_size))
					{
						$inserir = sprintf(' INSERT INTO `property` (`name`, `value_type`, `form_field_type`, `unit_type_id`, `form_field_order`, `form_field_size`, `mandatory`, `component_id`, `state`)
									VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s");', 					
						mysql_real_escape_string($proper_name),
						mysql_real_escape_string($value_type),
						mysql_real_escape_string($form_field_type),
						mysql_real_escape_string($unit_type_id),
						mysql_real_escape_string($form_field_order),
						mysql_real_escape_string($form_field_size),					
						mysql_real_escape_string($mandatory_prop),
						mysql_real_escape_string($component_id),
						mysql_real_escape_string("active"));
					
					}
																		
					if(mysql_query($inserir))
					{
						$id = mysql_insert_id();
						$query_comp_name = "SELECT name FROM component WHERE id = ".$component_id."";
						$result_comp_name = mysql_query($query_comp_name);				
						$comp_name = mysql_fetch_array($result_comp_name);
					
						$string = substr($comp_name, 0, 3)."-".$id."-".$proper_name; //cria um substring de component_name com 4 letras e concatena id e proper_name
						$form_field_name = preg_replace('/\s+/', '_', $string);
						$update = sprintf('UPDATE `property` SET `form_field_name` = "%s" WHERE id = "%s"',
						mysql_real_escape_string($form_field_name),
						mysql_real_escape_string($id));
						$result_update = mysql_query($update);
			
?>
						<p>Inseriu os dados da nova propriedade com sucesso.</p>
						<p>Clique em <a href = "gestao-de-propriedades">Continuar </a>para avançar.</p>
<?php
					}

			}

		}
	}
?>	