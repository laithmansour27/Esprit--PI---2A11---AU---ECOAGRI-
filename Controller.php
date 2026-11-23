<?php
// Base class for all controllers: renders views inside the right layout.
abstract class Controller
{
    /**
     * @param string $view  e.g. "home/index"
     * @param array  $data  variables made available to the view
     * @param string $area  "front" (Front Office) or "back" (Back Office)
     */
    protected function render(string $view, array $data = [], string $area = 'front'): void
    {
        extract($data);
        $viewsDir = dirname(__DIR__) . "/Views/$area";
        $flashes  = Session::pullFlash();

        ob_start();
        require "$viewsDir/$view.php";
        $content = ob_get_clean();
        require "$viewsDir/layout.php";
    }
}
