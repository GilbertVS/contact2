<?php
	header('Content-Type: text/html; charset=UTF-8');
	//inicializar variable de sesión
	session_start();
	//inocialització de constants
	const CLAVE = "zxspectrum";
	//inicialización de variables
	//array para guardar las personas
	$arrayPersonas = [];
	//si existe la variable de sesión substituyo el contenido del array
	if (isset($_SESSION['personas'])) {
		$arrayPersonas = $_SESSION['personas'];
	}	
	//ALTA DE PERSONA
	if (isset($_POST['alta'])) {
		try {
			//recuperar los datos sin espacios en blanco -trim()-
			$error = null;
			//validar que el nif no exista en la base de datos
			$nif = strtoupper(trim($_REQUEST['nif'])) ?? null;
			$dni = substr($nif, 0, 8);
			if (is_numeric($dni)) $nletr = $dni%23;
			$letr = substr($nif, 8, 1);
			$letters = "TRWAGMYFPDXBNJZSQVHLCKET";
			if (is_numeric($dni)) $letter = substr($letters, $nletr, 1);
			if (!$nif = trim(filter_input(INPUT_POST, 'nif'))) {
				$error .= "<style>.errores {color:red;}</style> Nif és obligatori.<style>#nif {border: 2px solid red;}</style>";
			}
			else if (array_key_exists(strtoupper(trim($nif)), $arrayPersonas)) {
				$error .="<style>.errores {color:red;}</style> Persona ja existeix. <style>#nif {border: 2px solid red;}</style>";
			}
			else if (!is_numeric($dni)) {
				$error .="<style>.errores {color:red;}</style> Nif no té 8 dígits numèrics. <style>#nif {border: 2px solid red;}</style>";
			}
			else if ($letr != $letter) {
				$error .="<style>.errores {color:red;}</style> Nif és incorrecte. <style>#nif {border: 2px solid red;}</style>";
			}
			//validar datos obligatorios
			if (!$nombre = trim(filter_input(INPUT_POST, 'nombre'))) {
				$error .= "<style>.errores {color:red;}</style> Nom és obligatori. <style>#nombre {border: 2px solid red;}</style>";
			}	
			if (!$direccion = trim(filter_input(INPUT_POST, 'direccion'))) {
				$error .= "<style>.errores {color:red;}</style> Adreça 
				és obligatoria. <style>#direccion{border:2px solid red;}</style>";
			}
			if (!empty($error)) {
				$codi = 10;
				throw new Exception($error, $codi);
			}
			//guardamos el nombre y dirección en minúsculas con la primera letra en mayúsculas
			$nif = strtoupper(trim($nif));
			$nombre = ucfirst(strtolower(trim($nombre)));
			$direccion = ucfirst(strtolower(trim($direccion)));
			//guardar la persona en el array
			$arrayPersonas[$nif]['nombre'] = $nombre;
			$arrayPersonas[$nif]['direccion'] = $direccion;
			//mensaje de alta efectuada
			$codi = null;
			$error = "<style>.errores {color: green;}</style>Alta realitzada amb èxit.";
			$direccion ='';
			$nombre = '';
			$nif = '';			
			//limpiar el formulario
		} catch (Exception $e) {
			$error = $e->getMessage();
			$codi = $e->getCode();
		}		
	}
	//BAJA DE TODAS LAS PERSONAS
	if (isset($_POST['baja'])) {
		//inicializar el array
		$arrayPersonas = [];
	}
	//BAJA DE LA PERSONA SELECCIONADA EN LA TABLA
	if (isset($_POST['bajaPersona'])) {
		$error = null;
		//recuperar el nif
		$nifBaja = $_POST['nifBaja'];
		//validar nif informado
		if (!$nifBaja = filter_input(INPUT_POST, 'nifBaja')^CLAVE) {
			$codi = 10;
			$error = "<style>.errores {color:red;}</style> Nif es obligatori.<style>#nif {border: 2px solid red;}</style>";
		}else if (!array_key_exists($nifBaja, $arrayPersonas)) {
			$codi = 10;	
			$error = "<style>.errores {color:red;}</style> Nif no existeix.<style>#nif {border: 2px solid red;}</style>";
		}
		//borrar la fila del array
		else {
			unset($arrayPersonas[$nifBaja]);
			//mensaje de baja efectuada
			$codi = null;
			$error = "<style>.errores {color: green;}</style>Baixa realitzada amb èxit.";
		}		
	}	
	//MODIFICACION DE LA PERSONA SELECCIONADA
	if (isset($_POST['modificarPersona'])) {
		try {
		//recuperar los datos sin espacios en blanco -trim()-
		$error = null;						
		//validar datos
		//validar que el nif no exista en la base de datos
		if (!$nif = filter_input(INPUT_POST, 'nifModi')^CLAVE) {
			$error .= "<style>.errores {color:red;}</style> Nif és obligatori.<style>#nif {border: 2px solid red;}</style>";
		}
		else if (!array_key_exists($nif, $arrayPersonas)) {
			$error .= "<style>.errores {color:red;}</style> No hi ha aquest NIF.<style>#nif {border: 2px solid red;}</style> ";
		}
		if (!$nombre = trim(filter_input(INPUT_POST, 'nombreModi'))) {
			$error .= "<style>.errores {color:red;}</style> Nom és obligatori.<style>#nombre {border: 2px solid red;}</style>";
		}	
		if (!$direccion = trim(filter_input(INPUT_POST, 'direccionModi'))) {
			$error .= "<style>.errores {color:red;}</style> Adreça és obligatoria.<style>#direccion{border:2px solid red;}</style>";
		}
		if (!empty($error)) {
			$codi = 10;
			throw new Exception($error, $codi);
		}
		//guardamos el nombre y dirección en minúsculas con la primera letra en mayúsculas
		$nif = strtoupper(trim($nif));
		$nombre = ucfirst(strtolower(trim($nombre)));
		$direccion = ucfirst(strtolower(trim($direccion)));			
		//modificar la persona en el array
		$arrayPersonas[$nif]['nombre'] = $nombre;
		$arrayPersonas[$nif]['direccion'] = $direccion;
		//print_r($arrayPersonas);			
		//mensaje de modificación efectuada
		$codi = null;
		$error = "<style>.errores {color: green;}</style>Modificació realitzada amb èxit.";
		$nif = '';
		$nombre = '';
		$direccion = '';	
		}catch (Exception $e){
			$error = $e->getMessage();
			$codi = $e->getCode();
		}
	}
	//CONSULTA DE PERSONAS
	$contanidor = '';
	$c = 0;
	//ordenar el array por nif
	ksort($arrayPersonas);
	//confeccionar la tabla con las personas del array
	foreach ($arrayPersonas as $clau=>$valor){
		$nifcifrat = $clau ^ CLAVE;
		$contanidor .= "<tr><td id = 'dni".$c."' class = 'nif' data-nif = '".$nifcifrat."' contenteditable >".$clau."</td><td><input type='text' id = 'nom".$c."' class='nombre' value='".$arrayPersonas[$clau]['nombre']."' ></td><td><input type='text' id = 'adresa".$c."' class='direccion' value='".$arrayPersonas[$clau]['direccion']."' ></td><td><form method='post' action='#'><input type='hidden' name='nifBaja' value='".$nifcifrat."'><button type='submit' class='btn btn-warning baja' name='bajaPersona'>Baixa</button><button type='button' class='btn btn-primary modi' name='modiPersona' onClick='traslladarDades(".$c.");'>Modificar</button></form></td></tr>";
		$c++;
	}
	//volcar el contenido del array en la variable de sesión
	$_SESSION['personas'] = $arrayPersonas;
?>
<html>
<head>
	<title>Manteniment de persones</title>
	<meta charset='UTF-8'>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
	<link href="./img/note.ico" rel="icon">
	<!-- Bootstrap -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP
	+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<!-- script user -->
	<script type="text/javascript" src='js/scripts.js'></script>
</head>
<body>
	<main>
		<h1 class='centrar'>MANTENIMENT DE PERSONES</h1>
		<br>
		<form method='post' action='#'>
		  <div class="row mb-2">
			<div class ="col-sm-4"></div>
		    <label for="nif" class="col-sm-1 col-form-label">Nif:</label>
		    <div class="col-sm-2">
		      <input type="text" class="form-control" id="nif" name='nif' value ='<?php if (!empty($nif)) echo $nif; ?>'>
		    </div>
		  </div>
		  <div class="row mb-2">
		  <div class ="col-sm-4"></div>
		    <label for="nombre" class="col-sm-1 col-form-label">Nom:</label>
		    <div class="col-sm-2">
		      <input type="text" class="form-control" id="nombre" name="nombre" value ='<?php if (!empty($nombre)) echo $nombre; ?>'>
		    </div>
		  </div>
		  <div class="row mb-2">
		  	<div class ="col-sm-4"></div>
		    <label for="direccion" class="col-sm-1 col-form-label">Adreça:</label>
		    <div class="col-sm-2">
		      <input type="text" class="form-control" id="direccion" name="direccion" value = '<?php if (!empty($direccion)) echo $direccion; ?>'>
		    </div>
		  </div>
		  <label for="alta" class="col-sm-5 col-form-label"></label>
		  <button type="submit" class="btn btn-success" id="alta" name='alta'>Alta persona</button>
		  <spam class = "errores"><?php if (!empty($error)) echo $codi.": ".$error; ?></spam>  
		</form><br>
		<table class="table table-striped">
			<tr class='table-dark'>
				<th scope="col">NIF</th>
				<th scope="col">Nom</th>
				<th scope="col">Adreça</th>
				<th scope="col">Accions</th>
			</tr>
			<!-- tr>
		      <td>40000000A</td>
		      <td><input type='text' value='O-Ren Ishii' class='nombre'></td>
		      <td><input type='text' value='Graveyard avenue, 66' class='direccion'></td>
		      <td>
		      	<form method='post' action='#'>
		      		<input type='hidden' name='nifBaja' value='40000000A'>
		      		<button type="submit" class="btn btn-warning bajar" name='bajaPersona'>Baja</button>
		      	</form>
		      	<button type="button" class="btn btn-primary modificar" name='modiPersona'>Modificar</button>
		      </td>
			</tr -->
			<?php echo $contanidor; ?>
		</table>

		<form method='post' action='#' id='formularioBaja'>
			<input type='hidden' name = 'baja'></input>
			<?php if (!empty($arrayPersonas)) { ?>
			<button type="button" class="btn btn-danger" id='baja' onClick="confirmar();">Baixa persones</button>
			<?php }else { ?>
			<button type="button" class="btn btn-danger" id="baja2">Baixa persones</button>
			<?php } ?>
		</form>

		<!--FORMULARIO OCULTO PARA LA MODIFICACION-->
		<form method='post' action='#' id='formularioModi'>
			<input type= 'text' hidden=''  name='nifModi' id="nifModi"></input>
			<input type='text' hidden = ''name='nombreModi' id="nombreModi"></input>
			<input type='text' hidden ='' name="direccionModi" id="direccionModi"></input>
			<input type = 'hidden' name = 'modificarPersona'></input>
		</form>
	</main>
</body>
</html>

