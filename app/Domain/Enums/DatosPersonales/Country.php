<?php

namespace App\Domain\Enums\DatosPersonales;

enum Country: string
{
    // Países de América del Norte
    case CANADA = 'canada';
    case ESTADOS_UNIDOS = 'estados_unidos';
    case MEXICO = 'mexico';
    
    // Países de América Central
    case BELICE = 'belice';
    case COSTA_RICA = 'costa_rica';
    case EL_SALVADOR = 'el_salvador';
    case GUATEMALA = 'guatemala';
    case HONDURAS = 'honduras';
    case NICARAGUA = 'nicaragua';
    case PANAMA = 'panama';
    
    // Países de América del Sur
    case ARGENTINA = 'argentina';
    case BOLIVIA = 'bolivia';
    case BRASIL = 'brasil';
    case CHILE = 'chile';
    case COLOMBIA = 'colombia';
    case ECUADOR = 'ecuador';
    case GUYANA = 'guyana';
    case PARAGUAY = 'paraguay';
    case PERU = 'peru';
    case SURINAM = 'surinam';
    case URUGUAY = 'uruguay';
    case VENEZUELA = 'venezuela';
    
    // Países de Europa
    case ALEMANIA = 'alemania';
    case FRANCIA = 'francia';
    case ESPANA = 'espana';
    case ITALIA = 'italia';
    case REINO_UNIDO = 'reino_unido';
    case RUSIA = 'rusia';
    case POLONIA = 'polonia';
    case UCRANIA = 'ucrania';
    case PORTUGAL = 'portugal';
    case PAISES_BAJOS = 'paises_bajos';
    case BELGICA = 'belgica';
    case SUIZA = 'suiza';
    case AUSTRIA = 'austria';
    case SUECIA = 'suecia';
    case NORUEGA = 'noruega';
    case DINAMARCA = 'dinamarca';
    case FINLANDIA = 'finlandia';
    case IRLANDA = 'irlanda';
    case GRECIA = 'grecia';
    case TURQUIA = 'turquia';
    
    // Países de Asia
    case CHINA = 'china';
    case JAPON = 'japon';
    case INDIA = 'india';
    case COREA_DEL_SUR = 'corea_del_sur';
    case COREA_DEL_NORTE = 'corea_del_norte';
    case TAILANDIA = 'tailandia';
    case VIETNAM = 'vietnam';
    case FILIPINAS = 'filipinas';
    case INDONESIA = 'indonesia';
    case MALASIA = 'malasia';
    case SINGAPUR = 'singapur';
    case PAKISTAN = 'pakistan';
    case BANGLADESH = 'bangladesh';
    case SRI_LANKA = 'sri_lanka';
    case MONGOLIA = 'mongolia';
    case KAZAJISTAN = 'kazajistan';
    case UZBEKISTAN = 'uzbekistan';
    case ISRAEL = 'israel';
    case ARABIA_SAUDITA = 'arabia_saudita';
    case IRAN = 'iran';
    case IRAK = 'irak';
    case AFGANISTAN = 'afganistan';
    
    // Países de África
    case EGIPTO = 'egipto';
    case SUDAN = 'sudan';
    case ETIOPIA = 'etiopia';
    case KENIA = 'kenia';
    case TANZANIA = 'tanzania';
    case UGANDA = 'uganda';
    case GHANA = 'ghana';
    case NIGERIA = 'nigeria';
    case SUD_AFRICA = 'sud_africa';
    case MARRUECOS = 'marruecos';
    case ARGELIA = 'argelia';
    case TUNEZ = 'tunez';
    case LIBIA = 'libia';
    case ZIMBABUE = 'zimbabue';
    case BOTSUANA = 'botsuana';
    case NAMIBIA = 'namibia';
    case ANGOLA = 'angola';
    case MOZAMBIQUE = 'mozambique';
    case MADAGASCAR = 'madagascar';
    
    // Países de Oceanía
    case AUSTRALIA = 'australia';
    case NUEVA_ZELANDA = 'nueva_zelanda';
    case FIJI = 'fiji';
    case PAPUA_NUEVA_GUINEA = 'papua_nueva_guinea';
    case SAMOA = 'samoa';
    case TONGA = 'tonga';
    case VANUATU = 'vanuatu';
    case SOLOMON = 'solomon';
    
    // Otros países importantes
    case CUBA = 'cuba';
    case REPUBLICA_DOMINICANA = 'republica_dominicana';
    case HAITI = 'haiti';
    case JAMAICA = 'jamaica';
    case TRINIDAD_Y_TOBAGO = 'trinidad_y_tobago';
    case BARBADOS = 'barbados';
    case BAHAMAS = 'bahamas';
    case PUERTO_RICO = 'puerto_rico';

    public function getLabel(): string
    {
        return match($this) {
            // América del Norte
            self::CANADA => 'Canadá',
            self::ESTADOS_UNIDOS => 'Estados Unidos',
            self::MEXICO => 'México',
            
            // América Central
            self::BELICE => 'Belice',
            self::COSTA_RICA => 'Costa Rica',
            self::EL_SALVADOR => 'El Salvador',
            self::GUATEMALA => 'Guatemala',
            self::HONDURAS => 'Honduras',
            self::NICARAGUA => 'Nicaragua',
            self::PANAMA => 'Panamá',
            
            // América del Sur
            self::ARGENTINA => 'Argentina',
            self::BOLIVIA => 'Bolivia',
            self::BRASIL => 'Brasil',
            self::CHILE => 'Chile',
            self::COLOMBIA => 'Colombia',
            self::ECUADOR => 'Ecuador',
            self::GUYANA => 'Guyana',
            self::PARAGUAY => 'Paraguay',
            self::PERU => 'Perú',
            self::SURINAM => 'Surinam',
            self::URUGUAY => 'Uruguay',
            self::VENEZUELA => 'Venezuela',
            
            // Europa
            self::ALEMANIA => 'Alemania',
            self::FRANCIA => 'Francia',
            self::ESPANA => 'España',
            self::ITALIA => 'Italia',
            self::REINO_UNIDO => 'Reino Unido',
            self::RUSIA => 'Rusia',
            self::POLONIA => 'Polonia',
            self::UCRANIA => 'Ucrania',
            self::PORTUGAL => 'Portugal',
            self::PAISES_BAJOS => 'Países Bajos',
            self::BELGICA => 'Bélgica',
            self::SUIZA => 'Suiza',
            self::AUSTRIA => 'Austria',
            self::SUECIA => 'Suecia',
            self::NORUEGA => 'Noruega',
            self::DINAMARCA => 'Dinamarca',
            self::FINLANDIA => 'Finlandia',
            self::IRLANDA => 'Irlanda',
            self::GRECIA => 'Grecia',
            self::TURQUIA => 'Turquía',
            
            // Asia
            self::CHINA => 'China',
            self::JAPON => 'Japón',
            self::INDIA => 'India',
            self::COREA_DEL_SUR => 'Corea del Sur',
            self::COREA_DEL_NORTE => 'Corea del Norte',
            self::TAILANDIA => 'Tailandia',
            self::VIETNAM => 'Vietnam',
            self::FILIPINAS => 'Filipinas',
            self::INDONESIA => 'Indonesia',
            self::MALASIA => 'Malasia',
            self::SINGAPUR => 'Singapur',
            self::PAKISTAN => 'Pakistán',
            self::BANGLADESH => 'Bangladesh',
            self::SRI_LANKA => 'Sri Lanka',
            self::MONGOLIA => 'Mongolia',
            self::KAZAJISTAN => 'Kazajistán',
            self::UZBEKISTAN => 'Uzbekistán',
            self::ISRAEL => 'Israel',
            self::ARABIA_SAUDITA => 'Arabia Saudita',
            self::IRAN => 'Irán',
            self::IRAK => 'Irak',
            self::AFGANISTAN => 'Afganistán',
            
            // África
            self::EGIPTO => 'Egipto',
            self::SUDAN => 'Sudán',
            self::ETIOPIA => 'Etiopía',
            self::KENIA => 'Kenia',
            self::TANZANIA => 'Tanzania',
            self::UGANDA => 'Uganda',
            self::GHANA => 'Ghana',
            self::NIGERIA => 'Nigeria',
            self::SUD_AFRICA => 'Sudáfrica',
            self::MARRUECOS => 'Marruecos',
            self::ARGELIA => 'Argelia',
            self::TUNEZ => 'Túnez',
            self::LIBIA => 'Libia',
            self::ZIMBABUE => 'Zimbabue',
            self::BOTSUANA => 'Botsuana',
            self::NAMIBIA => 'Namibia',
            self::ANGOLA => 'Angola',
            self::MOZAMBIQUE => 'Mozambique',
            self::MADAGASCAR => 'Madagascar',
            
            // Oceanía
            self::AUSTRALIA => 'Australia',
            self::NUEVA_ZELANDA => 'Nueva Zelanda',
            self::FIJI => 'Fiji',
            self::PAPUA_NUEVA_GUINEA => 'Papua Nueva Guinea',
            self::SAMOA => 'Samoa',
            self::TONGA => 'Tonga',
            self::VANUATU => 'Vanuatu',
            self::SOLOMON => 'Islas Salomón',
            
            // Otros
            self::CUBA => 'Cuba',
            self::REPUBLICA_DOMINICANA => 'República Dominicana',
            self::HAITI => 'Haití',
            self::JAMAICA => 'Jamaica',
            self::TRINIDAD_Y_TOBAGO => 'Trinidad y Tobago',
            self::BARBADOS => 'Barbados',
            self::BAHAMAS => 'Bahamas',
            self::PUERTO_RICO => 'Puerto Rico',
        };
    }
    
    public static function getMainOptions(): array
    {
        return [
            ['value' => self::MEXICO->value, 'label' => self::MEXICO->getLabel()],
            ['value' => self::ESTADOS_UNIDOS->value, 'label' => self::ESTADOS_UNIDOS->getLabel()],
            ['value' => self::CANADA->value, 'label' => self::CANADA->getLabel()],
            ['value' => self::ESPANA->value, 'label' => self::ESPANA->getLabel()],
            ['value' => self::ARGENTINA->value, 'label' => self::ARGENTINA->getLabel()],
            ['value' => self::COLOMBIA->value, 'label' => self::COLOMBIA->getLabel()],
            ['value' => self::CHILE->value, 'label' => self::CHILE->getLabel()],
            ['value' => self::PERU->value, 'label' => self::PERU->getLabel()],
        ];
    }
    
    public static function getAllOptions(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->getLabel()
        ], self::cases());
    }
}
