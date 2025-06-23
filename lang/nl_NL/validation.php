<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute moet worden geaccepteerd.',
    'accepted_if' => ':attribute moet worden geaccepteerd als :other :value is.',
    'active_url' => ':attribute moet een geldige URL zijn.',
    'after' => ':attribute moet een datum na :date zijn.',
    'after_or_equal' => ':attribute moet een datum zijn op of na :date.',
    'alpha' => ':attribute mag alleen letters bevatten.',
    'alpha_dash' => ':attribute mag alleen letters, cijfers, koppeltekens en underscores bevatten.',
    'alpha_num' => ':attribute mag alleen letters en cijfers bevatten.',
    'array' => ':attribute moet een lijst zijn.',
    'ascii' => ':attribute mag alleen standaardtekens bevatten.',
    'before' => ':attribute moet een datum voor :date zijn.',
    'before_or_equal' => ':attribute moet een datum zijn op of voor :date.',
    'between' => [
        'array' => ':attribute moet tussen :min en :max items bevatten.',
        'file' => ':attribute moet tussen :min en :max kilobytes groot zijn.',
        'numeric' => ':attribute moet tussen :min en :max liggen.',
        'string' => ':attribute moet tussen :min en :max tekens lang zijn.',
    ],
    'boolean' => ':attribute moet waar of onwaar zijn.',
    'can' => ':attribute bevat een ongeldige waarde.',
    'confirmed' => ':attribute komt niet overeen met de bevestiging.',
    'current_password' => 'Het ingevoerde wachtwoord is onjuist.',
    'date' => ':attribute is geen geldige datum.',
    'date_equals' => ':attribute moet exact :date zijn.',
    'date_format' => ':attribute komt niet overeen met het formaat :format.',
    'decimal' => ':attribute moet :decimal decimalen bevatten.',
    'declined' => ':attribute moet worden afgewezen.',
    'declined_if' => ':attribute moet worden afgewezen als :other :value is.',
    'different' => ':attribute en :other moeten verschillend zijn.',
    'digits' => ':attribute moet uit :digits cijfers bestaan.',
    'digits_between' => ':attribute moet tussen :min en :max cijfers bevatten.',
    'dimensions' => ':attribute heeft ongeldige afbeeldingsafmetingen.',
    'distinct' => ':attribute bevat een dubbele waarde.',
    'doesnt_end_with' => ':attribute mag niet eindigen met een van de volgende: :values.',
    'doesnt_start_with' => ':attribute mag niet beginnen met een van de volgende: :values.',
    'email' => ':attribute moet een geldig e-mailadres zijn.',
    'ends_with' => ':attribute moet eindigen met een van de volgende: :values.',
    'enum' => 'De geselecteerde waarde voor :attribute is ongeldig.',
    'exists' => ':attribute bestaat al.',
    'file' => ':attribute moet een bestand zijn.',
    'filled' => ':attribute mag niet leeg zijn.',
    'gt' => [
        'array' => ':attribute moet meer dan :value items bevatten.',
        'file' => ':attribute moet groter zijn dan :value kilobytes.',
        'numeric' => ':attribute moet groter zijn dan :value.',
        'string' => ':attribute moet meer dan :value tekens bevatten.',
    ],
    'gte' => [
        'array' => ':attribute moet minimaal :value items bevatten.',
        'file' => ':attribute moet minimaal :value kilobytes zijn.',
        'numeric' => ':attribute moet minimaal :value zijn.',
        'string' => ':attribute moet minimaal :value tekens bevatten.',
    ],
    'image' => ':attribute moet een afbeelding zijn.',
    'in' => 'De geselecteerde waarde voor :attribute is ongeldig.',
    'in_array' => ':attribute moet voorkomen in :other.',
    'integer' => ':attribute moet een geheel getal zijn.',
    'ip' => ':attribute moet een geldig IP-adres zijn.',
    'ipv4' => ':attribute moet een geldig IPv4-adres zijn.',
    'ipv6' => ':attribute moet een geldig IPv6-adres zijn.',
    'json' => ':attribute moet een geldige JSON-string zijn.',
    'lowercase' => ':attribute moet alleen kleine letters bevatten.',
    'lt' => [
        'array' => ':attribute mag maximaal :value items bevatten.',
        'file' => ':attribute moet kleiner zijn dan :value kilobytes.',
        'numeric' => ':attribute moet kleiner zijn dan :value.',
        'string' => ':attribute moet minder dan :value tekens bevatten.',
    ],
    'lte' => [
        'array' => ':attribute mag niet meer dan :value items bevatten.',
        'file' => ':attribute mag maximaal :value kilobytes zijn.',
        'numeric' => ':attribute mag maximaal :value zijn.',
        'string' => ':attribute mag maximaal :value tekens bevatten.',
    ],
    'mac_address' => ':attribute moet een geldig MAC-adres zijn.',
    'max' => [
        'array' => ':attribute mag maximaal :max items bevatten.',
        'file' => ':attribute mag maximaal :max kilobytes zijn.',
        'numeric' => ':attribute mag niet groter zijn dan :max.',
        'string' => ':attribute mag niet langer zijn dan :max tekens.',
    ],
    'max_digits' => ':attribute mag maximaal :max cijfers bevatten.',
    'mimes' => ':attribute moet een bestand zijn van het type: :values.',
    'mimetypes' => ':attribute moet een bestand zijn van het type: :values.',
    'min' => [
        'array' => ':attribute moet minstens :min items bevatten.',
        'file' => ':attribute moet minstens :min kilobytes zijn.',
        'numeric' => ':attribute moet minstens :min zijn.',
        'string' => ':attribute moet minstens :min tekens bevatten.',
    ],
    'min_digits' => ':attribute moet minstens :min cijfers bevatten.',
    'missing' => ':attribute moet ontbreken.',
    'missing_if' => ':attribute moet ontbreken als :other ":value" is.',
    'missing_unless' => ':attribute moet ontbreken tenzij :other :value is.',
    'missing_with' => ':attribute moet ontbreken als :values aanwezig is.',
    'missing_with_all' => ':attribute moet ontbreken als :values aanwezig zijn.',
    'multiple_of' => ':attribute moet een veelvoud van :value zijn.',
    'not_in' => 'De geselecteerde waarde voor :attribute is ongeldig.',
    'not_regex' => 'Het formaat van :attribute is ongeldig.',
    'numeric' => ':attribute moet een getal zijn.',
    'password' => [
        'letters' => ':attribute moet minstens één letter bevatten.',
        'mixed' => ':attribute moet minstens één hoofdletter en één kleine letter bevatten.',
        'numbers' => ':attribute moet minstens één cijfer bevatten.',
        'symbols' => ':attribute moet minstens één speciaal teken bevatten.',
        'uncompromised' => 'Het opgegeven :attribute is aangetroffen in een datalek. Kies een andere :attribute.',
    ],
    'present' => ':attribute moet aanwezig zijn.',
    'prohibited' => ':attribute mag niet worden opgegeven.',
    'prohibited_if' => ':attribute mag niet worden opgegeven als :other ":value" is.',
    'prohibited_unless' => ':attribute mag alleen worden opgegeven als :other één van de volgende waarden heeft: :values.',
    'prohibits' => ':attribute staat niet toe dat :other aanwezig is.',
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'required' => ':attribute is verplicht.',
    'required_array_keys' => ':attribute moet waarden bevatten voor: :values.',
    'required_if' => ':attribute is verplicht als :other ":value" is.',
    'required_if_accepted' => ':attribute is verplicht als :other is geaccepteerd.',
    'required_unless' => ':attribute is verplicht tenzij :other één van de volgende waarden heeft: :values.',
    'required_with' => ':attribute is verplicht als :values aanwezig is.',
    'required_with_all' => ':attribute is verplicht als alle :values aanwezig zijn.',
    'required_without' => ':attribute is verplicht als :values niet aanwezig is.',
    'required_without_all' => ':attribute is verplicht als geen van :values aanwezig zijn.',
    'same' => ':attribute en :other moeten overeenkomen.',
    'size' => [
        'array' => ':attribute moet precies :size items bevatten.',
        'file' => ':attribute moet :size kilobytes zijn.',
        'numeric' => ':attribute moet :size zijn.',
        'string' => ':attribute moet :size tekens lang zijn.',
    ],
    'starts_with' => ':attribute moet beginnen met één van de volgende: :values.',
    'string' => ':attribute moet een tekst zijn.',
    'timezone' => ':attribute moet een geldige tijdzone zijn.',
    'unique' => ':attribute is al in gebruik.',
    'uploaded' => ':attribute kon niet worden geüpload.',
    'uppercase' => ':attribute moet alleen hoofdletters bevatten.',
    'url' => ':attribute moet een geldige URL zijn.',
    'ulid' => ':attribute moet een geldige ULID zijn.',
    'uuid' => ':attribute moet een geldige UUID zijn.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'aangepaste-bericht',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'address' => 'Adres',
        'age' => 'Leeftijd',
        'body' => 'Inhoud',
        'cell' => 'Mobiel',
        'city' => 'Stad',
        'country' => 'Land',
        'date' => 'Datum',
        'day' => 'Dag',
        'excerpt' => 'Samenvatting',
        'first_name' => 'Voornaam',
        'gender' => 'Geslacht',
        'marital_status' => 'Burgerlijke staat',
        'profession' => 'Beroep',
        'nationality' => 'Nationaliteit',
        'hour' => 'Uur',
        'last_name' => 'Achternaam',
        'message' => 'Bericht',
        'minute' => 'Minuut',
        'mobile' => 'Mobiele nummer',
        'month' => 'Maand',
        'name' => 'Naam',
        'zipcode' => 'Postcode',
        'company_name' => 'Bedrijfsnaam',
        'neighborhood' => 'Wijk',
        'number' => 'Nummer',
        'password' => 'Wachtwoord',
        'phone' => 'Telefoonnummer',
        'second' => 'Seconde',
        'sex' => 'Geslacht',
        'state' => 'Provincie',
        'street' => 'Straat',
        'subject' => 'Onderwerp',
        'text' => 'Tekst',
        'time' => 'Tijd',
        'title' => 'Titel',
        'username' => 'Gebruikersnaam',
        'year' => 'Jaar',
        'description' => 'Beschrijving',
        'password_confirmation' => 'Wachtwoordbevestiging',
        'current_password' => 'Huidig wachtwoord',
        'complement' => 'Aanvulling',
        'modality' => 'Modaliteit',
        'category' => 'Categorie',
        'blood_type' => 'Bloedgroep',
        'birth_date' => 'Geboortedatum',
    ],
];
