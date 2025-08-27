<?php
/*
Plugin Name: Revista acaingpba 
Plugin URI: http://www.acaingpba.org.ar/
Description: 
Version: 1.0.0
Author: Manzur Ezequiel | PREBI-SEDICI
Author URI: http://sedici.unlp.edu.ar/
*/




function articulos_content_fields( $content ) {
        global $post;
        if ( is_singular() && 'articulo' == get_post_type())
        {
		$autor = get_the_terms($post->ID, 'autor');
		$numero = get_the_terms($post->ID, 'numero');
		$seccion = get_the_terms($post->ID, 'seccion');	
                $args = get_custom_fields_articulo();

		// Chequeo que no haya un WP_Error y que el articulo tenga el atributo cargado 
		if(!is_wp_error( $autor) && $autor)
			$content .=  '<div class = "autor_articulo"><span> Autores: </span>'.custom_print_taxonomy($autor).'</div>';
		else $content .=  '<div class = "autor_articulo"><span> Autores: </span> - </div>';
		
		if(!is_wp_error( $seccion) && $seccion)
			$content .=  '<div class = "seccion_articulo"><span> Sección: </span>'.custom_print_taxonomy($seccion).'</div>';
		else $content .=  '<div class = "seccion_articulo"><span> Sección: </span> - </div>';
		
		if(!is_wp_error( $numero) && $numero)
			$content .=  '<div class = "numero_articulo"><span> Número: </span>'.custom_print_taxonomy($numero).'</div>';
		else $content .=  '<div class = "numero_articulo"><span> Número: </span> - </div>';

                foreach ($args as $key => $value) {
                	if(!empty($value)){
                		if($key == 'Texto completo'){
                			$content = $content .
	                		/*'<div class ="'.strtolower(str_replace(" ", "_", $key)).'" >
	                			<span>'.$key.': </span><a href="'.$value['url'].'">Descargar Pdf
	                		</a></div>';*/
					'<embed src="'.$value['url'].'" type="application/pdf" width="100%" height="600px">';
                		}
				elseif($key == 'Lugar de publicación'){
					$lugar = $value['lugar'];
					$url= $value['url'];
					if(!empty($url))
						$lugar = '<a href="'.$url.'">'. $lugar . '</a>';
					$content .= '<div class ="'.strtolower(str_replace(" ", "_", $key)).'" >
						<span>'.$key.': </span>'.$lugar.'
                                        </div>';

				}
                		else	
	                		$content = $content .
	                		'<div class ="'.strtolower(str_replace(" ", "_", $key)).'" >
	                			<span>'.$key.': </span>'.$value.'
	                		</div>';
                	}
                }
        }
        	return $content;
}
add_filter( 'the_content', 'articulos_content_fields', 10 );



function get_custom_fields_articulo(){
	global $post;


	return array(
		'Resumen' => get_field('resumen'),
		'Fecha de publicación' => get_field('fecha_de__publicacion'),
		'Lugar de publicación' => array('lugar' => get_field('lugar_de_publicacion'), 'url' => get_field('url_del_lugar_de_publicacion')),
//		'url_del_lugar_de_publicacion' => get_field('url_del_lugar_de_publicacion'),
		'Comentarios' => get_field('comentarios'), 
		'Texto completo' => get_field('texto_completo'), 
		'Citas' => get_field('citas'), 

	);
}



function custom_print_taxonomy($terms, $separator = "|")
{
    $lista = "";
    foreach ($terms as $term) {
        $term_link = get_term_link($term, 'keywords');
        if (is_wp_error($term_link))
            continue;
        $lista.= '<a href="' . $term_link . '">' . $term->name . '</a> '. $separator. ' ' ;
    }
    return trim($lista, " \t\n\r\0\x0B | ".$separator );
}



//add_filter( 'the_title', 'articulos_add_subtitule', 10 );

function articulos_add_subtitule($title){
	global $post;
	if (($post->post_type = 'articulo') && is_singular('articulo') )
        {
		$title.= '<div> '. get_field('subtitulo').'</div>';
	}
	return $title;
}





/**
 * Alter your post layouts
 *
 * Replace is_singular( 'post' ) by the function where you want to alter the layout
 * You can also use is_page ( 'page name' ) to alter layouts on specific pages
 * @return full-width, full-screen, left-sidebar, right-sidebar or both-sidebars
 *
 */
function articulo_layout_class( $class ) {

	// Alter your layout
	if ( is_singular( 'articulo' ) ) {
		$class = 'full-width';
	}

	// Return correct class
	return $class;

}

add_filter( 'ocean_post_layout_class', 'articulo_layout_class', 20 );


