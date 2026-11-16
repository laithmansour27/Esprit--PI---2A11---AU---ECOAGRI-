<?php
class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::requireUser();
        $modules = Module::available($user);
        $counts = []; $error = null;
        try { foreach ($modules as $key=>$module) { $counts[$key] = count(Module::rows($key,$user)); } }
        catch (PDOException $exception) { $error = 'Les données sont temporairement indisponibles. Vérifiez MySQL et la configuration.'; }
        $this->render('dashboard/index', compact('user','modules','counts','error')+['title'=>'Vue d’ensemble','currentPage'=>'dashboard'], 'back');
    }
    public function module(): void
    {
        $user = Auth::requireUser();
        $modules = Module::available($user);
        $key = is_string($_GET['key'] ?? null) ? $_GET['key'] : '';
        if (!isset($modules[$key])) { http_response_code(403); $this->render('dashboard/forbidden', compact('user','modules')+['title'=>'Accès limité','currentPage'=>''], 'back'); return; }
        $module = $modules[$key]; $rows=[]; $error=null;
        try { $rows=Module::rows($key,$user); }
        catch (PDOException $exception) { $error='Impossible de charger les données. Vérifiez MySQL et le schéma ECOAGRI.'; }
        $this->render('dashboard/module', compact('user','modules','key','module','rows','error')+['title'=>$module['name'],'currentPage'=>$key], 'back');
    }
}
