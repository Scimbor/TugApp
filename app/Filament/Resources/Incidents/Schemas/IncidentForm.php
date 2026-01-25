<?php

namespace App\Filament\Resources\Incidents\Schemas;

use App\Filament\Resources\Incidents\Modals\Schema\IncidentModalFormSchema;
use Filament\Schemas\Schema;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(IncidentModalFormSchema::components(false));
    }
}
