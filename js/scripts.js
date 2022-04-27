//activar listener del boton de baja de todas las personas
function confirmar () {
	if (confirm("Estàs segur?")) {
		document.querySelector("#formularioBaja").submit();
	}
}
//activar listener de los botones de modificación de persona
var botonsModificar = document.querySelectorAll('.modificar');
botonsModificar.forEach(function(boto) {
	boto.onclick = traslladarDades();
});
//función para trasladar los datos de la fila seleccionada al formulario oculto
function traslladarDades(clau) {
	//situarnos en la etiqueta tr que corresponda a la fila donde se encuentra el botón
	//let tr = this.closest('tr');
	//recuperar los datos de la persona
	//let nif = tr.querySelector('.nif').innerText;
	//let nombre = tr.querySelector('.nombre').value;
	//let direccion = tr.querySelector('.direccion').value;
	var nif = document.getElementById('dni'+clau).getAttribute('data-nif');
	var nombre = document.getElementById('nom'+clau).value;
	var direccion = document.getElementById('adresa'+clau).value;
	//trasladar los datos al formulario oculto
	//document.querySelector('[name=nifModi]').value = nif;
	//document.querySelector('[name=nombreModi]').value = nombre;
	//document.querySelector('[name=direccionModi]').value = direccion;
	document.getElementById('nifModi').value = nif;
	document.getElementById('nombreModi').value = nombre;
	document.getElementById('direccionModi').value = direccion;
	//alert(document.getElementById('nifModi').value+" , "+document.getElementById('nombreModi').value+" , "+document.getElementById('direccionModi').value);
	//submit del formulario
	document.querySelector('#formularioModi').submit();
}
