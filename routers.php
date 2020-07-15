<?php
global $routes;

$routes = array();

$routes['/usuarios/login'] = '/usuarios/login';
$routes['/usuarios/novo'] = '/usuarios/novousuario';
$routes['/usuarios/feed'] = '/usuarios/feed';
$routes['/usuarios/{id}'] = '/usuarios/view/:id';
$routes['/conteudos/novo'] = '/conteudos/novoconteudo';
$routes['/autores/novo'] = '/autores/novoautor';
$routes['/temas/novo'] = '/temas/novotema';
$routes['/temas'] = '/temas/listar';
$routes['/autores'] = '/autores/listar';
