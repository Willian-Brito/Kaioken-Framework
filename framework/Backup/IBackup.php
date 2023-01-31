<?php

namespace KaiokenFramework\Backup;

interface IBackup
{
    public function export($path);
}
