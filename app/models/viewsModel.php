<?php
	
	namespace app\models;

	class viewsModel{

		/*---------- Modelo obtener vista ----------*/
		protected function obtenerVistasModelo($vista){

			$listaBlanca=["login","home","404","categorias","productos","usuario","recuperar","nueva_clave","variantes"];

			if(in_array($vista, $listaBlanca)){
				if(is_file("app/views/content/".$vista.".php")){
					$contenido="app/views/content/".$vista.".php";
				}else{
					$contenido="app/views/content/404.php";
				}
			}else if($vista=="index"){
				$contenido="app/views/content/home.php";
			}else{
				$contenido="app/views/content/404.php";
			}
			return $contenido;
		}

	}