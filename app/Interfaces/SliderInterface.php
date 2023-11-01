<?php

namespace App\Interfaces;

Interface SliderInterface {

    public function index($request);

    public function delete($request);

    public function create();

    public function store($request);

    public function edit($slider);

    public function changeStatus($request);

    public function update($request, $id);

}
