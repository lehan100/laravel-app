<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
interface InterfaceController{
    public function index(Request $request);
    public function form(Request $request);
    public function delete(Request $request);
    public function status(Request $request);
    public function multiple(Request $request);
}