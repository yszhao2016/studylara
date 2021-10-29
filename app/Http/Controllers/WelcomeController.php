<?php
/**
 * Created by PhpStorm
 * USER: Zhaoys
 * Date: 2021/10/19 11:03
 */
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Container\Container;
use Illuminate\Contracts\Database\ModelIdentifier;

class WelcomeController
{
    public function  index()
    {
//        return '<h1>控制器成功！！！</h1>';
        $student = Student::first();
        $data = $student->getAttributes();


//        Student::where();
        return "学生id=".$data['id']."学生name=".$data['name'].";  学生age=".$data['age'];
    }



    public function view()
    {
        $student = Student::first();
        $data = $student->getAttributes();

        $app = Container::getInstance();
        $factory = $app->make('view');
        return $factory->make('welcome')->with('data',$data);
    }
}