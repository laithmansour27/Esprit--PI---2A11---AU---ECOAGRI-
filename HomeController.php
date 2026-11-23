<?php
class HomeController extends Controller
{
 public function index(): void { $this->render('home/index', ['title'=>'Cultivons demain','currentPage'=>'accueil','modules'=>Module::definitions()]); }
 public function services(): void { $this->render('home/services', ['title'=>'Nos solutions','currentPage'=>'services','modules'=>Module::definitions()]); }
 public function project(): void { $this->render('home/project', ['title'=>'Notre projet','currentPage'=>'projet']); }
 public function notFound(): void { $this->render('errors/404', ['title'=>'Page introuvable','currentPage'=>'']); }
}
