<?php

namespace App\Interfaces;

Interface AreaInterface {

    public function index($request);

    public function delete($request);

    public function create();

    public function store($request);

    public function edit($city);

    public function update($request, $id);

}