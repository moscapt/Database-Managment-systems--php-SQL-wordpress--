<?php	
	require_once("custom/php/common.php");
	
	if(!is_user_logged_in() || !current_user_can("insert_values"))
	{
?>
		<p>Nao tem autorizacao para aceder a esta pagina. Tem de efetuar <a href=><?php wp_loginout("wordpress/insercao-de-valores")?> </a>.</p>	
<?php
	
	}
	else
	{
		if($_REQUEST['estado'] == "")
		{	
?>			
			<h3><b>Insercao de valores - escolher componente/formulario customizado</b></h3>
<?php
			$query_comp_type = "SELECT id, name FROM comp_type GROUP BY id";
			$result_comp_type = mysql_query($query_comp_type);
?>			
			<ul>
			<li><b>Componentes:</b></li>
			 
			<ul>
<?php		
			while($array_comp_type = mysql_fetch_array($result_comp_type))
			{
?>
				<li><?php echo $array_comp_type['name']; ?></li>
<?php				
				$query_component = "SELECT component.id, component.name
											FROM component, comp_type 
											WHERE component.comp_type_id = ".$array_comp_type["id"]."
											GROUP BY component.id";
											
				$result_component = mysql_query($query_component);
?>
				<ul>	
<?php					
				while($array_component = mysql_fetch_array($result_component))
				{
?>					
				<li>
				<a href="insercao-de-valores?estado=introducao&comp=<?php echo $array_component['id']; ?>" >
		        	<?php echo '['.$array_component['name'].']'; ?> 
		        </a>
		        </li>								
<?php
				}
?>
				</ul>
<?php				
			}
?>
			</ul>
			<li><b>Formularios customizados:<b></li>
<?php
			$query_custom_form = "SELECT id, name FROM custom_form GROUP by name";
			$result_custom_form = mysql_query($query_custom_form);
?>
			<ul>
<?php
			while($array_custom_form = mysql_fetch_array($result_custom_form))
			{
?>
				<li>
				<a href="insercao-de-valores?estado=introducao&form=<?php echo $array_custom_form['id']; ?>" >
					<?php echo '['.$array_custom_form['name'].']'; ?> 
		        </a>
				</li>
<?php				
			}
?>			
			</ul>
			</ul>		
<?php				
		}
		elseif($_REQUEST['estado'] == "introducao")
		{
			if(isset($_REQUEST['comp']))
			{
			
				//COMPONENTES
			
				$s_comp_id = $_SESSION['comp_id'] = $_REQUEST['comp'];
			
				$query_components = mysql_query("SELECT id, name, comp_type_id FROM component WHERE id = $s_comp_id");
				$array_components = mysql_fetch_array($query_components);
			
				$s_comp_name = $_SESSION['comp_name'] = $array_components['name'];

				$s_comp_type_id = $_SESSION['comp_type_id'] = $array_components['comp_type_id'];
?>
				<h3></b>Insercao de valores - <?php echo $s_comp_name; ?><b></h3>
<?php			
				$name_formulario = "comp_type_".$s_comp_type_id."_comp_".$s_comp_id;
?>			
				<form 
				name = "<?php echo $name_formulario ?>"
				method = "POST"
				action = "?insercao-de-valores?estado=validar&comp=<?php echo $s_comp_id; ?>" >				
<?php
				$query_property_component = "SELECT * FROM property WHERE component_id = $s_comp_id AND state = 'active' ";
				$result_property_component = mysql_query($query_property_component);
		
				function get_unit_type_name($unit_type_id)
				{
					$query_unit_type_name = "SELECT name FROM prop_unit_type WHERE id = $unit_type_id ";
					$result_unit_type_name = mysql_query($query_unit_type_name);
					$array_unit_type_name = mysql_fetch_array($result_unit_type_name);

					return $array_unit_type_name['name'];
				}
			
				while($array_property_component = mysql_fetch_array($result_property_component))
				{
					switch($array_property_component['value_type'])
					{
						case 'text':
				
							if($array_property_component['form_field_type'] == 'text')
							{
								echo $array_property_component['name'] . ": <input type='text' name=".$array_property_component['form_field_name']." > ". get_unit_type_name($array_property_component['unit_type_id'])." <br><br> ";
								
							}
							elseif($array_property_component['form_field_type'] == 'textbox')
							{
								echo $array_property_component['name'] . ": <input type='textbox' name=".$array_property_component['form_field_name']." > ". get_unit_type_name($array_property_component['unit_type_id'])." <br><br> ";
							}
				
						break;
				
						case 'bool' :
					
							echo $array_property_component['name'] . ": <br><input type='radio' name=".$array_property_component['form_field_name']." value = true> 
							<label>sim</label> ". get_unit_type_name($array_property_component['unit_type_id'])."
							<br>
							<input type='radio' name=".$array_property_component['form_field_name']." value = false CHECKED> 
							<label>não</label> ". get_unit_type_name($array_property_component['unit_type_id'])."
							<br><br> ";
		
						break;
				
						case 'int' :
						case 'double':
					
							echo $array_property_component['name'] . ": <input type='text' name=".$array_property_component['form_field_name']." > ". get_unit_type_name($array_property_component['unit_type_id'])." <br><br> ";
			
						break;
	
						case 'enum' :
			
							$query_prop_allowed_value = " SELECT value FROM prop_allowed_value WHERE property_id = ".$array_property_component['id']." ";
							$result_prop_allowed_value = mysql_query($query_prop_allowed_value);
							//$array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value);
					
							if($array_property_component['form_field_type'] == 'radio' && mysql_num_rows($result_prop_allowed_value) != 0)					
							{
	
								echo $array_property_component['name'].":<br>";
								echo "<input type='radio' name=".$array_property_component['form_field_name']." value='' CHECKED><label>Nenhum</label><br>";
								while($array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value))
								{
									
									echo "<input type='radio' name=".$array_property_component['form_field_name']." value=" .$array_prop_allowed_value['value']. " > <label> " . $array_prop_allowed_value['value'] ." </label> ". get_unit_type_name($array_property_component['unit_type_id'])." <br>";		
								}
								echo"<br>";	
							
							}
							elseif($array_property_component['form_field_type'] == 'checkbox' && mysql_num_rows($result_prop_allowed_value) != 0)
							{
						
								echo $array_property_component['name'].":<br>";
								while($array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value))
								{
								
									echo "<input type ='checkbox' name=".$array_property_component['form_field_name']." value=" .$array_prop_allowed_value['value']. " > <label> " . $array_prop_allowed_value['value'] ." </label> ". get_unit_type_name($array_property_component['unit_type_id'])." <br>";
					
								}
								echo"<br>";
							}
							elseif($array_property_component['form_field_type'] == 'selectbox' && mysql_num_rows($result_prop_allowed_value) != 0)
							{
						
								echo $array_property_component['name'].":<br>";
							
								echo "<select name = ".$array_property_component['form_field_name'].">";
								echo "<option value = ''></option> ";
						
								while($array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value))
								{
						
									echo "<option value= " .$array_prop_allowed_value['value']." > ".$array_prop_allowed_value['value']." </option> ";
						
								}
								echo "</select>  ". get_unit_type_name($array_property_component['unit_type_id'])." <br><br>";
						
							}
							elseif(($array_property_component['form_field_type'] == 'radio' || $array_property_component['form_field_type'] == 'checkbox' || $array_property_component['form_field_type'] == 'selectbox') && mysql_num_rows($result_prop_allowed_value) == 0)
							{
								echo "Não há valores permitidos para ".$array_property_component['name']." . <br><br>";
							}

						break; 
					} //fecha switch
				}//fecha while	
?>			
				<input type="hidden" name="estado" value="validar">
				<p>
				<input type="submit" value="Submeter">
				</p>

				</form>
<?php
			}
			elseif(isset($_REQUEST['form']))
			{
				
				//FORMULARIOS
				
				$s_form_id = $_SESSION['form_id'] = $_REQUEST['form'];
			
				$query_custom_forms = mysql_query("SELECT id, name FROM custom_form WHERE id = $s_form_id");
				$array_custom_forms = mysql_fetch_array($query_custom_forms);
			
				$s_form_name = $_SESSION['form_name'] = $array_custom_forms['name'];				
?>
				<h3></b>Insercao de valores - <?php echo $s_form_name; ?><b></h3>
<?php			
				$name_formulario = "comp_".$s_form_id;
?>			
				<form 
				name = "<?php echo $name_formulario ?>"
				method = "POST"
				action = "?insercao-de-valores?estado=validar&form=<?php echo $s_form_id; ?>" >				
<?php								
				$query_property_cform = "SELECT property.* FROM property, custom_form_has_property
										WHERE property.id = custom_form_has_property.property_id
										AND custom_form_has_property.custom_form_id = $s_form_id 
										AND property.state = 'active' ";
										
				$result_property_cform = mysql_query($query_property_cform);
		
				function get_unit_type_name($unit_type_id)
				{
					$query_unit_type_name = "SELECT name FROM prop_unit_type WHERE id = $unit_type_id ";
					$result_unit_type_name = mysql_query($query_unit_type_name);
					$array_unit_type_name = mysql_fetch_array($result_unit_type_name);

					return $array_unit_type_name['name'];
				}
							
				while($array_property_cform = mysql_fetch_array($result_property_cform))
				{
					switch($array_property_cform['value_type'])
					{
						case 'text':
				
							if($array_property_cform['form_field_type'] == 'text')
							{
								echo $array_property_cform['name'] . ": <input type='text' name=".$array_property_cform['form_field_name']." > ". get_unit_type_name($array_property_cform['unit_type_id'])." <br><br> ";
								
							}
							elseif($array_property_cform['form_field_type'] == 'textbox')
							{
								echo $array_property_cform['name'] . ": <input type='textbox' name=".$array_property_cform['form_field_name']." > ". get_unit_type_name($array_property_cform['unit_type_id'])." <br><br> ";
							}
				
						break;

						case 'bool' :
					
							echo $array_property_cform['name'] . ": <br><input type='radio' name=".$array_property_cform['form_field_name']." value = true> 
							<label>sim</label> ". get_unit_type_name($array_property_cform['unit_type_id'])."
							<br>
							<input type='radio' name=".$array_property_cform['form_field_name']." value = false CHECKED> 
							<label>não</label> ". get_unit_type_name($array_property_cform['unit_type_id'])."
							<br><br> ";							
		
						break;

						case 'int' :
						case 'double':
					
							echo $array_property_cform['name'] . ": <input type='text' name=".$array_property_cform['form_field_name']." > ". get_unit_type_name($array_property_cform['unit_type_id'])." <br><br> ";
			
						break;

						case 'enum' :
			
							$query_prop_allowed_value = " SELECT value FROM prop_allowed_value WHERE property_id = ".$array_property_cform['id']." ";
							$result_prop_allowed_value = mysql_query($query_prop_allowed_value);
							//$array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value);						
				
							if($array_property_cform['form_field_type'] == 'radio' && mysql_num_rows($result_prop_allowed_value) != 0)					
							{
	
								echo $array_property_cform['name'].":<br>";
								echo "<input type='radio' name=".$array_property_component['form_field_name']." value='' CHECKED><label>Nenhum</label><br>";
								while($array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value))
								{
									echo "<input type='radio' name=".$array_property_cform['form_field_name']." value=" .$array_prop_allowed_value['value']. " > <label> " . $array_prop_allowed_value['value'] ." </label> ". get_unit_type_name($array_property_cform['unit_type_id'])." <br>";		
								}
								echo"<br>";	
							
							}
							elseif($array_property_cform['form_field_type'] == 'checkbox' && mysql_num_rows($result_prop_allowed_value) != 0)
							{
						
								echo $array_property_cform['name'].":<br>";
								while($array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value))
								{
								
									echo "<input type ='checkbox' name=".$array_property_cform['form_field_name']." value=" .$array_prop_allowed_value['value']. " > <label> " . $array_prop_allowed_value['value'] ." </label> ". get_unit_type_name($array_property_cform['unit_type_id'])." <br>";
					
								}
								echo"<br>";
							}
							elseif($array_property_cform['form_field_type'] == 'selectbox' && mysql_num_rows($result_prop_allowed_value) != 0)
							{
						
								echo $array_property_cform['name'].":<br>";
							
								echo "<select name = ".$array_property_cform['form_field_name'].">";
								echo "<option value = ''></option> ";
						
								while($array_prop_allowed_value = mysql_fetch_array($result_prop_allowed_value))
								{
						
									echo "<option value= " .$array_prop_allowed_value['value']." > ".$array_prop_allowed_value['value']." </option> ";
						
								}
								echo "</select>  ". get_unit_type_name($array_property_cform['unit_type_id'])." <br><br>";
						
							}
							elseif(($array_property_cform['form_field_type'] == 'radio' || $array_property_cform['form_field_type'] == 'checkbox' || $array_property_cform['form_field_type'] == 'selectbox') && mysql_num_rows($result_prop_allowed_value) == 0)
							{
								echo "Não há valores permitidos para ".$array_property_cform['name']." . <br><br>";
							}

						break; 			
					}// fecha switch		
				}//fecha while
?>			
				<input type="hidden" name="estado" value="validar">
				<p>
				<input type="submit" value="Submeter">
				</p>

				</form>
<?php								
			}//fecha isset request form
						
		}// fecha request estado == introducao
		elseif($_REQUEST['estado'] == "validar")
		{
			if(isset($_REQUEST['comp']))
			{ 
				$s_comp_id = $_SESSION['comp_id'];
						
				$s_comp_name = $_SESSION['comp_name'];

				$s_comp_type_id = $_SESSION['comp_type_id'];
?>
				<h3></b>Insercao de valores - <?php echo $s_comp_name; ?> - validar<b></h3>
						
				<form 
				method = "POST"
				action = "?insercao-de-valores?estado=inserir&comp=<?php echo $s_comp_id; ?>" >	

<?php			
				$next = 1;
				$query_property_component = "SELECT * FROM property WHERE component_id = $s_comp_id ";
				$result_property_component = mysql_query($query_property_component);
				
				while($array_property_component = mysql_fetch_array($result_property_component))
				{			
					foreach ($_REQUEST as $chave => $valor) 
					{		
						if($chave == $array_property_component['form_field_name'] && $array_property_component['mandatory'] == 1)
						{
							if($valor == "")
							{						
?>								
								<p>O campo <?php echo $array_property_component['name']; ?> tem de estar preenchido.<p>								
<?php
								$next = 0;
							}
						}
					}					
				}
			
				if($next == 1)
				{
?>				
					<p>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos?</p>
<?php	
					$query_property_component2 = "SELECT * FROM property WHERE component_id = $s_comp_id ";
					$result_property_component2 = mysql_query($query_property_component2);
					
					while($array_property_component2 = mysql_fetch_array($result_property_component2))
					{			
					
						foreach ($_REQUEST as $chave => $valor) 
						{		
							if($chave == $array_property_component2['form_field_name'])
							{
								if($valor == "true")
								{
									echo $array_property_component2['name'] ." : Sim <br><br>";
									echo "<input type = 'hidden' name = ".$array_property_component2['form_field_name']." value = ".$valor." >";
								
								}
								elseif($valor == "false")
								{
									echo $array_property_component2['name'] ." : Não <br><br>";
									echo "<input type = 'hidden' name = ".$array_property_component2['form_field_name']." value = ".$valor." >";
								}
								elseif($valor == "")
								{

								}
								else
								{
									echo $array_property_component2['name'] ." : ".$valor." <br><br>";
									echo "<input type = 'hidden' name = ".$array_property_component2['form_field_name']." value = ".$valor." >";
																	
								}
							}
						}	
					}
				}//fecha if next ==1
				back();
				echo "<br>";				
?>			
				<input type="hidden" name="estado" value="inserir">
<?php
				if($next == 1)
				{				
?>				
					<br>
					<p>
					<input type="submit" value="Submeter">
					</p>
<?php					
				}
?>
				</form>
<?php				
			}
			elseif(isset($_REQUEST['form']))
			{				
				$s_form_id = $_SESSION['form_id'];
						
				$s_form_name = $_SESSION['form_name'];
?>
				<h3></b>Insercao de valores - <?php echo $s_form_name; ?> - validar<b></h3>

				<form 
				method = "POST"
				action = "?insercao-de-valores?estado=inserir&form=<?php echo $s_form_id; ?>" >
				
<?php								
				$next = 1;				
				$query_property_cform = "SELECT property.* FROM property, custom_form_has_property
										WHERE property.id = custom_form_has_property.property_id
										AND custom_form_has_property.custom_form_id = $s_form_id ";
										
				$result_property_cform = mysql_query($query_property_cform);
				
				while($array_property_cform = mysql_fetch_array($result_property_cform))
				{					
					foreach ($_REQUEST as $chave => $valor) 
					{		
						if($chave == $array_property_cform['form_field_name'] && $array_property_cform['mandatory'] == 1)
						{
							if($valor == "")
							{						
?>								
								<p>O campo <?php echo $array_property_cform['name']; ?> tem de estar preenchido.<p>								
<?php
								$next = 0;
							}
						}
					}		
				}

				if($next == 1)
				{
?>				
					<p>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos?</p>
<?php					
					$query_property_cform2 = "SELECT property.* FROM property, custom_form_has_property
											WHERE property.id = custom_form_has_property.property_id
											AND custom_form_has_property.custom_form_id = $s_form_id ";
										
					$result_property_cform2 = mysql_query($query_property_cform2);

					while($array_property_cform2 = mysql_fetch_array($result_property_cform2))
					{								
						foreach ($_REQUEST as $chave => $valor) 
						{		
							if($chave == $array_property_cform2['form_field_name'])
							{
								if($valor == "true")
								{
									echo $array_property_cform2['name'] ." : Sim <br><br>";
									echo "<input type = 'hidden' name = ".$array_property_cform2['form_field_name']." value = ".$valor." >";
								
								}
								elseif($valor == "false")
								{
									echo $array_property_cform2['name'] ." : Não <br><br>";
									echo "<input type = 'hidden' name = ".$array_property_cform2['form_field_name']." value = ".$valor." >";
								}
								elseif($valor == "")
								{
									
								}								
								else
								{
									echo $array_property_cform2['name'] ." : ".$valor." <br><br>";
									echo "<input type = 'hidden' name = ".$array_property_cform2['form_field_name']." value = ".$valor." >";
																	
								}
							}
						}	
					}
				}//fecha if next ==1
				back();
				echo "<br>";								
?>			
				<input type="hidden" name="estado" value="inserir">
<?php
				if($next == 1)
				{
?>				
					<br>
					<p>
					<input type="submit" value="Submeter">
					</p>
<?php					
				}
?>
				</form>
<?php							
			}
		} //fecha request estado == validar
		elseif($_REQUEST['estado'] == "inserir")
		{			
			$date =date("Y-m-d");
			$time = date("H:i:s");
				
			global $current_user;
			get_currentuserinfo();
			$producer = $current_user->display_name;
	
			if(isset($_REQUEST['comp']))
			{
				$s_comp_id = $_SESSION['comp_id'];
						
				$s_comp_name = $_SESSION['comp_name'];

				$s_comp_type_id = $_SESSION['comp_type_id'];
								
?>								
				<h3></b>Insercao de valores - <?php echo $s_comp_name; ?> - inserção<b></h3>
<?php			
				
				$insert_comp_inst = sprintf (' INSERT INTO comp_inst (`component_id`) VALUES ("%s"); ', mysql_real_escape_string($s_comp_id));
				$result_insert_comp_inst = mysql_query();
				$comp_inst_id = mysql_insert_id(); //need testing
				
				$query_property_component = "SELECT * FROM property WHERE component_id = $s_comp_id ";
				$result_property_component = mysql_query($query_property_component);
								
				while($array_property_component = mysql_fetch_array($result_property_component))
				{			
					foreach ($_REQUEST as $chave => $valor) 
					{		
						if($chave == $array_property_component['form_field_name'])
						{
							$property_id = $array_property_component['id'];
							
							$insert_value = sprintf(" INSERT INTO value (`comp_inst_id`, `property_id`, `value`, `date`, `time`, `producer`) 
																	VALUES ('%s', '%s', '%s', '%s', '%s', '%s'); ",
																	mysql_real_escape_string($comp_inst_id),
																	mysql_real_escape_string($property_id),
																	mysql_real_escape_string($valor),
																	mysql_real_escape_string($date),
																	mysql_real_escape_string($time),
																	mysql_real_escape_string($producer));
																	
							$result_insert_value = mysql_query($insert_value);
					
						}
					}					
				}
				
				if($result_insert_value)
				{
?>				
					<p>Inseriu o(s) valor(es) com sucesso.</p>
				
					<p>
					Clique em <a href = "insercao-de-valores"> Voltar</a>  para voltar ao início da inserção de 
					valores e poder escolher outro componente ou em <a href = "insercao-de-valores?estado=introducao&comp=<?php echo $s_comp_id; ?>" > 
					Continuar a inserir valores neste componente </a>se quiser continuar a inserir valores;
					</p>
<?php							
				}
				else
				{
					echo $insert_value;
				}
				
			
			}
			elseif(isset($_REQUEST['form']))
			{
				$s_form_id = $_SESSION['form_id'];
						
				$s_form_name = $_SESSION['form_name'];
				
?>								
				<h3></b>Insercao de valores - <?php echo $s_form_name; ?> - inserção<b></h3>
<?php						
				$query_property_cform = "SELECT property.* FROM property, custom_form_has_property
										WHERE property.id = custom_form_has_property.property_id
										AND custom_form_has_property.custom_form_id = $s_form_id ";
										
				$result_property_cform = mysql_query($query_property_cform);
				
				while($array_property_cform = mysql_fetch_array($result_property_cform))
				{					
					foreach ($_REQUEST as $chave => $valor) 
					{		
						if($chave == $array_property_cform['form_field_name'])
						{
						
							$property_id = $array_property_component['id'];
							
							$insert_value = sprintf(" INSERT INTO value (`comp_inst_id`, `property_id`, `value`, `date`, `time`, `producer`) 
																	VALUES ('%s', '%s', '%s', '%s', '%s', '%s'); ",
																	mysql_real_escape_string('NULL'), // comp_inst_id devia ser NULL
																	mysql_real_escape_string($property_id),
																	mysql_real_escape_string($valor),
																	mysql_real_escape_string($date),
																	mysql_real_escape_string($time),
																	mysql_real_escape_string($producer));
																	
							$result_insert_value = mysql_query($insert_value);

						}
					}		
				}
				
				if($result_insert_value)
				{
?>				
					<p>Inseriu o(s) valor(es) com sucesso.</p>
				
					<p>
					Clique em <a href = "insercao-de-valores"> Voltar</a>  para voltar ao início da inserção de 
					valores e poder escolher outro formulário customizado ou em <a href = "insercao-de-valores?estado=introducao&form=<?php echo $s_form_id; ?>" > 
					Continuar a inserir valores neste formulário customizado </a>se quiser continuar a inserir valores;
					</p>
<?php							
				}				
			}		
		}//fecha request estado == inserir
	} //fecha else do is_user_logged_in
?>