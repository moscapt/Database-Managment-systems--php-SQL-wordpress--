<?php	
	require_once("custom/php/common.php");
	
	if(!is_user_logged_in() || !current_user_can("manage_custom_forms"))
	{
?>
		<p>Nao tem autorização para aceder a esta página. Tem de efetuar <a href=><?php wp_loginout("wordpress/gestao-de-formularios")?> </a>.</p>	
<?php
	
	}
	else
	{
	
		if($_REQUEST['estado'] == "")
		{
?>		
			<h3><b>Gestao de formularios customizados</b></h3>
<?php		
			$query_cform = "SELECT * FROM custom_form";
			$result_cform = mysql_query($query_cform);
			
			if (mysql_num_rows($result_cform) == 0)
			{
				echo "Nao ha formularios customizados";
			}
			else
			{
			
				$query_custom_form = "SELECT name, id
									FROM custom_form, custom_form_has_property WHERE id = custom_form_id
									GROUP BY name";
				$result_custom_form = mysql_query($query_custom_form);				
?>				
				<table class="mytable">
				<thead>
				<tr>
				<th>formulario</th>
				<th>id</th>
				<th>propriedade</th>
				<th>tipo de valor</th>
				<th>nome do campo no formulario</th>
				<th>tipo do campo no formulario</th>
				<th>tipo de unidade</th>
				<th>ordem do campo no formulario</th>
				<th>tamanho do campo no formulario</th>
				<th>obrigatorio</th>
				<th>estado</th>
				<th>acao</th>
				</tr>
				</thead>
				
				<tbody>
	
<?php	
				while($array_custom_form = mysql_fetch_array($result_custom_form))
				{
					$query_property = "SELECT property.* FROM property, custom_form_has_property
									WHERE property.id = custom_form_has_property.property_id
									AND custom_form_has_property.custom_form_id = ".$array_custom_form["id"]."
									GROUP BY id";
					$result_property = mysql_query($query_property);
					$num_rows_property = mysql_num_rows($result_property);

?>					
					
					<tr>
					
					<td colspan = "1" rowspan = "<?php echo $num_rows_property; ?>">
					<?php echo '<a href="gestao-de-formularios?estado=editar_form&id='.$array_custom_form['id'].'">
					'.$array_custom_form['name'].' </a>'; ?>					
					</td>					
<?php					
					while($array_property = mysql_fetch_array($result_property))
					{
?>
						<td><?php echo $array_property['id']; ?></td>
						<td><?php echo $array_property['name']; ?></td>
						<td><?php echo $array_property['value_type']; ?></td>
						<td><?php echo $array_property['form_field_name']; ?></td>
						<td><?php echo $array_property['form_field_type']; ?></td>
<?php					
						if($array_property['unit_type_id'] != null)
						{
							$query_unit_type = "SELECT name
												FROM prop_unit_type
												WHERE ".$array_property['unit_type_id']." = id";
							$result_unit_type = mysql_query($query_unit_type);
							$array_unit_type = mysql_fetch_array($result_unit_type);
?>								
							<td> <?php echo $array_unit_type['name']; ?> </td>
<?php	
						}						
						else
						{
?>						
							<td> - </td>
<?php							
						}
?>
						<td> <?php echo $array_property['form_field_order']; ?> </td>
<?php						
						if($array_property['form_field_size'] != null)
						{
?>
							<td> <?php echo $array_property['form_field_size']; ?> </td>
<?php							
						}
						else
						{
?>
							<td> - </td>
<?php						
						}	
						
						if($array_property['mandatory'] == 1)
						{
?>
							<td> sim </td>
<?php
						}
						else
						{
?>
							<td> nao </td>
<?php
						}
						
						if($array_property['state']== "active")
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
?>
			<h3><b>Gestao de formularios customizados - Introducao</b></h3>
			
			<form name="gestao-de-formularios" method="POST">	
			<fieldset>
			
			<input type="hidden" name="estado" value="inserir"> 
			<p>
			<label><b>Nome do Formulario:</b></label>
			<input type="text" name="form_name">
			</p>
			
			<table class = "mytable">					
			<thead>
			<tr>
			<th>Componente</th>
			<th>ID</th>
			<th>propriedade</th>
			<th>tipo de valor</th>
			<th>nome do campo no formulario</th>
			<th>tipo do campo no formulario</th>
			<th>tipo de unidade</th>
			<th>ordem do campo no formulario</th>
			<th>tamanho do campo no formulario</th>
			<th>obrigatorio</th>
			<th>estado</th>
			<th>escolher</th>
			<th>ordem</th>
			</tr>
			</thead>
					
			<tbody>
<?php		
			$query_component = " SELECT component.id, component.name
								FROM component
								GROUP BY name ";
			$result_component = mysql_query($query_component);
			
			while($array_component = mysql_fetch_array($result_component))
			{
					
				$query_comp_property = "SELECT * 
									FROM property
									WHERE component_id = ".$array_component["id"]."
									GROUP BY name";
				$result_comp_property = mysql_query($query_comp_property);
				$num_rows_comp_property = mysql_num_rows($result_comp_property);
?>					
				<tr>
				
				<td colspan ="1" rowspan="<?php echo $num_rows_comp_property; ?>" > <?php echo $array_component['name']; ?> </td> 				
<?php			
				while($array_comp_props = mysql_fetch_array($result_comp_property))
				{
?>					
					<td> <?php echo $array_comp_props['id']; ?> </td>
					<td> <?php echo $array_comp_props['name']; ?> </td>
					<td> <?php echo $array_comp_props['value_type']; ?> </td>
					<td> <?php echo $array_comp_props['form_field_name']; ?> </td>
					<td> <?php echo $array_comp_props['form_field_type']; ?> </td>
<?php				
					if($array_comp_props['unit_type_id'] != null)
					{
						$query_unit_type2 = "SELECT name
											FROM prop_unit_type
											WHERE ".$array_comp_props['unit_type_id']." = id";
													
						$result_unit_type2 = mysql_query($query_unit_type2);
						$array_unit_type2 = mysql_fetch_array($result_unit_type2);
?>								
						<td> <?php echo $array_unit_type2['name']; ?> </td>
<?php						
					}
					else
					{
?>						
						<td> - </td>
<?php												
					}
?>				
					<td> <?php echo $array_comp_props['form_field_order']; ?> </td>
<?php
					if($array_comp_props['form_field_size'] != null)
					{
?>
						<td> <?php echo $array_comp_props['form_field_size']; ?> </td>
<?php							
					}
					else
					{
?>
						<td> - </td>
<?php						
					}
					
					if($array_comp_props['mandatory'] == 1)
					{
?>
						<td> sim </td>
<?php
					}
					else
					{
?>
						<td> nao </td>
<?php
					}
					
					if($array_comp_props['state']== "active")
					{
?>						
						<td> activo </td>					 
<?php
					}
					else
					{
?>
						<td> inactivo </td>
<?php
					}
?>					
					<td><input type="checkbox" name="check[]" value="<?php echo $array_comp_props['id'];?>"></td>
					<td><input type="text" name="order_<?php echo $array_comp_props['id'];?>"></td>
				
					</tr>
<?php
				}
				
			}
?>			
			</tbody>
			</table>
	
			<p>	
			<input type="submit" value="Criar formulario">
			</p>
			</fieldset>	
			</form>			
<?php			

		}
		elseif($_REQUEST['estado'] == "inserir")
		{
			$form_name = $_REQUEST['form_name'];
			$check= $_REQUEST['check'];
			
			if(empty($form_name))
			{
?>
				<p>Tem de escolher o nome do formulario.</p>
<?php			
				back();
			}
			elseif(is_null($check))
			{
?>			
				<p>Tem de escolher pelo menos uma propriedade.</p>
<?php	
				back();				
			}
			else
			{	
				
				$inserir_cf_name = sprintf(' INSERT INTO custom_form (`name`) VALUES("%s"); ', mysql_real_escape_string($form_name));
				$result_inserir_cf_name = mysql_query($inserir_cf_name);
				$custom_form_id = mysql_insert_id(); //id gerado pelo ultimo insert
				
				foreach($check as $chave => $valor)  //percorre o array $check sendo $chave o indice do array e $valor os dados associados a esse indice
				{
					
					$order = $_REQUEST['order_'.$valor];
					if(empty($order))
					{
						$inserir_custom_form_has_property = sprintf(' INSERT INTO custom_form_has_property (`custom_form_id`,`property_id`) 
																	VALUES ("%s", "%s"); ',
																	mysql_real_escape_string($custom_form_id),
																	mysql_real_escape_string($valor));
																	
						$result_inserir_custom_form_has_property = mysql_query($inserir_custom_form_has_property);
					}
					else
					{
						$inserir_custom_form_has_property = sprintf(' INSERT INTO custom_form_has_property (`custom_form_id`, `property_id`, `field_order`) 
																	VALUES ("%s", "%s","%s"); ',
																	mysql_real_escape_string($custom_form_id),
																	mysql_real_escape_string($valor),
																	mysql_real_escape_string($order));
																	
						$result_inserir_custom_form_has_property = mysql_query($inserir_custom_form_has_property);																	
					}					
				}
				
				if($result_inserir_cf_name && result_inserir_custom_form_has_property)
				{
?>					
					<p>Inseriu os dados de novo formulario customizado com sucesso.</p>
					<p>Clique em <a href="gestao-de-formularios">Continuar</a> para avancar.</p><br>
<?php					
				}		
			}			
		}		
		elseif($_REQUEST['estado'] == "editar_form")
		{
?>		
			<h3><b>Gestao de formularios customizados - Editar</b></h3>
<?php			
			$custom_form_id = $_GET['id']; // vai buscar o id do formulario ao URL
			
			$query_custom_form_name = "SELECT name FROM custom_form WHERE id = $custom_form_id";
			$result_custom_form_name = mysql_query($query_custom_form_name);
			$array_custom_form_name = mysql_fetch_array($result_custom_form_name);
?>			
			<form name="gestao-de-formularios-editar" method="POST">	
			<fieldset>
			
			<input type="hidden" name="estado" value="atualizar_form_custom"> 
			<p>
			<label><b>Nome do Formulario:</b></label>
			<input type="text" name="form_name" value="<?php echo $array_custom_form_name['name']; ?>">
			</p>
			
			<table class = "mytable">					
			<thead>
			<tr>
			<th>Componente</th>
			<th>ID</th>
			<th>propriedade</th>
			<th>tipo de valor</th>
			<th>nome do campo no formulario</th>
			<th>tipo do campo no formulario</th>
			<th>tipo de unidade</th>
			<th>ordem do campo no formulario</th>
			<th>tamanho do campo no formulario</th>
			<th>obrigatorio</th>
			<th>estado</th>
			<th>escolher</th>
			<th>ordem</th>
			</tr>
			</thead>
					
			<tbody>

<?php		
			$query_component = " SELECT component.id, component.name
								FROM component
								GROUP BY name ";
			$result_component = mysql_query($query_component);
			
			while($array_component = mysql_fetch_array($result_component))
			{
					
				$query_comp_property = "SELECT * 
									FROM property
									WHERE component_id = ".$array_component["id"]."
									GROUP BY name";
				$result_comp_property = mysql_query($query_comp_property);
				$num_rows_comp_property = mysql_num_rows($result_comp_property);
?>					
				<tr>
				
				<td colspan ="1" rowspan="<?php echo $num_rows_comp_property; ?>" > <?php echo $array_component['name']; ?> </td> 				
<?php			
				while($array_comp_props = mysql_fetch_array($result_comp_property))
				{
?>					
					<td> <?php echo $array_comp_props['id']; ?> </td>
					<td> <?php echo $array_comp_props['name']; ?> </td>
					<td> <?php echo $array_comp_props['value_type']; ?> </td>
					<td> <?php echo $array_comp_props['form_field_name']; ?> </td>
					<td> <?php echo $array_comp_props['form_field_type']; ?> </td>
<?php				
					if($array_comp_props['unit_type_id'] != null)
					{
						$query_unit_type2 = "SELECT name
											FROM prop_unit_type
											WHERE ".$array_comp_props['unit_type_id']." = id";
													
						$result_unit_type2 = mysql_query($query_unit_type2);
						$array_unit_type2 = mysql_fetch_array($result_unit_type2);
?>								
						<td> <?php echo $array_unit_type2['name']; ?> </td>
<?php						
					}
					else
					{
?>						
						<td> - </td>
<?php												
					}
?>				
					<td> <?php echo $array_comp_props['form_field_order']; ?> </td>
<?php
					if($array_comp_props['form_field_size'] != null)
					{
?>
						<td> <?php echo $array_comp_props['form_field_size']; ?> </td>
<?php							
					}
					else
					{
?>
						<td> - </td>
<?php						
					}
					
					if($array_comp_props['mandatory'] == 1)
					{
?>
						<td> sim </td>
<?php
					}
					else
					{
?>
						<td> nao </td>
<?php
					}
					
					if($array_comp_props['state']== "active")
					{
?>						
						<td> activo </td>					 
<?php
					}
					else
					{
?>
						<td> inactivo </td>
<?php
					}
	
					$query_presets = "SELECT property_id, custom_form_id, field_order
											FROM custom_form_has_property 
											WHERE custom_form_id = $custom_form_id AND property_id = ".$array_comp_props['id']."";
					$result_presets = mysql_query($query_presets);
					$array_presets = mysql_fetch_assoc($result_presets);
					
					if($array_comp_props['id'] == $array_presets['property_id'])
					{
?>						
						<td><input type="checkbox" name="check[]" value="<?php echo $array_comp_props['id'];?>" CHECKED></td>
						<td><input type="text" name="order_<?php echo $array_comp_props['id'];?>" value="<?php echo $array_presets['field_order']; ?>"></td>					
<?php						
					}
					else
					{
?>						
						<td><input type="checkbox" name="check[]" value="<?php echo $array_comp_props['id'];?>"></td>
						<td><input type="text" name="order_<?php echo $array_comp_props['id'];?>"></td>					
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
	
			<p>	
			<input type="submit" value="Atualizar Formulario">
			</p>
			</fieldset>	
			</form>

<?php			
		}
		else if($_REQUEST['estado'] == "atualizar_form_custom")
		{
			$custom_form_id = $_GET['id'];
			$form_name = $_REQUEST['form_name'];
			$check= $_REQUEST['check'];
			
			if(empty($form_name))
			{
?>
				<p>Tem de escolher o nome do formulario.</p>
<?php			
				back();
			}
			elseif(is_null($check))
			{
?>			
				<p>Tem de escolher pelo menos uma propriedade.</p>
<?php	
				back();				
			}
			else
			{	
					
				$update_cf_name = sprintf(' UPDATE `custom_form` SET `name` = "%s" WHERE `id` = "%s" ; ',
											mysql_real_escape_string($form_name),
											mysql_real_escape_string($custom_form_id));
											
				$result_update_cf_name = mysql_query($update_cf_name);
				
				$clear_cf_has_property = sprintf(' DELETE FROM `custom_form_has_property` WHERE `custom_form_id` = "%s" ',
													mysql_real_escape_string($custom_form_id));
											
				$result_clear_cf_has_property = mysql_query($clear_cf_has_property); 
				
				foreach($check as $chave => $valor)  //percorre o array $check sendo $chave o indice do array e $valor os dados associados a esse indice
				{
					
					$order = $_REQUEST['order_'.$valor];
					if(empty($order))
					{											
						$update_cf_has_property = sprintf(' INSERT INTO custom_form_has_property (`custom_form_id`,`property_id`) 
																	VALUES ("%s", "%s"); ',
																	mysql_real_escape_string($custom_form_id),
																	mysql_real_escape_string($valor));
																	
						$result_update_cf_has_property = mysql_query($update_cf_has_property);
					}
					else
					{
						$update_cf_has_property = sprintf(' INSERT INTO custom_form_has_property (`custom_form_id`, `property_id`, `field_order`) 
																	VALUES ("%s", "%s","%s"); ',
																	mysql_real_escape_string($custom_form_id),
																	mysql_real_escape_string($valor),
																	mysql_real_escape_string($order));
																	
						$result_update_cf_has_property = mysql_query($update_cf_has_property);																	
					}								
				}
				
				if($result_update_cf_name && $result_update_cf_has_property && $result_clear_cf_has_property)
				{					
?>
					<p> Os dados do formulario customizado foram atualizados com sucesso.</p>
					<p>Clique em <a href="gestao-de-formularios">Continuar</a> para avancar.</p>
<?php										
				}	
			}					
		}
	}	
?>