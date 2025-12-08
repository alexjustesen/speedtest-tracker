<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;

class ResultServerColumn extends Column
{
    protected string $view = 'filament.tables.columns.result-server-column';

    protected ?string $serverName = null;

    protected ?int $serverId = null;

    public function getServerName(): ?string
    {
        $this->serverName = $this->record->server_name;

        return $this->serverName;
    }

    public function getServerId(): ?int
    {
        $this->serverId = $this->record->server_id;

        return $this->serverId;
    }
}
