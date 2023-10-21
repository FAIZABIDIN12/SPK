<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\Notification;

class LoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    	$session = \Config\Services::session();
        //if (isset($_SESSION['admin_logged_in'])){

        if (!session('admin_logged_in')){
	    	$this->notif = new Notification();
            $this->notif->message('Silahkan login terlebih dahulu untuk mengakses halaman ini', 'danger');
	        return redirect()->to('admin');
	    }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}