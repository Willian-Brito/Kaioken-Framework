<?php

namespace KaiokenFramework\Database;

interface IRecord
{
    public function fromArray($data);
    public function toArray();
    public function save();
    public function load($id);
    public function delete($id = NULL);
}
