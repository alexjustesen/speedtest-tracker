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

    'accepted' => ':attribute muss akzeptiert werden.',
    'accepted_if' => ':attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => ':attribute muss eine gültige URL sein.',
    'after' => ':attribute muss ein Datum nach :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach oder am :date sein.',
    'alpha' => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => ':attribute darf nur Buchstaben, Zahlen, Binde- und Unterstriche enthalten.',
    'alpha_num' => ':attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => ':attribute muss eine Liste sein.',
    'ascii' => ':attribute darf nur Standardzeichen enthalten.',
    'before' => ':attribute muss ein Datum vor :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor oder am :date sein.',
    'between' => [
        'array' => ':attribute muss zwischen :min und :max Einträge haben.',
        'file' => ':attribute muss zwischen :min und :max Kilobytes groß sein.',
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'string' => ':attribute muss zwischen :min und :max Zeichen lang sein.',
    ],
    'boolean' => ':attribute muss wahr oder falsch sein.',
    'can' => ':attribute enthält einen ungültigen Wert.',
    'confirmed' => 'Die Eingabe bei :attribute stimmt nicht mit der Bestätigung überein.',
    'current_password' => 'Das eingegebene Passwort ist falsch.',
    'date' => ':attribute ist kein gültiges Datum.',
    'date_equals' => ':attribute muss genau am :date liegen.',
    'date_format' => ':attribute entspricht nicht dem erforderlichen Format (:format).',
    'decimal' => ':attribute muss :decimal Nachkommastellen haben.',
    'declined' => ':attribute muss abgelehnt werden.',
    'declined_if' => ':attribute muss abgelehnt werden, wenn :other den Wert ":value" hat.',
    'different' => ':attribute und :other müssen verschieden sein.',
    'digits' => ':attribute muss :digits Ziffern lang sein.',
    'digits_between' => ':attribute muss zwischen :min und :max Ziffern lang sein.',
    'dimensions' => ':attribute hat falsche Bildmaße.',
    'distinct' => ':attribute enthält doppelte Werte.',
    'doesnt_end_with' => ':attribute darf nicht mit folgenden Werten enden: :values.',
    'doesnt_start_with' => ':attribute darf nicht mit folgenden Werten beginnen: :values.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => ':attribute muss mit einem der folgenden Werte enden: :values.',
    'enum' => 'Die gewählte Option bei :attribute ist ungültig.',
    'exists' => ':attribute existiert bereits.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => ':attribute darf nicht leer sein.',
    'gt' => [
        'array' => ':attribute muss mehr als :value Einträge enthalten.',
        'file' => ':attribute muss größer als :value Kilobytes sein.',
        'numeric' => ':attribute muss größer als :value sein.',
        'string' => ':attribute muss länger als :value Zeichen sein.',
    ],
    'gte' => [
        'array' => ':attribute muss mindestens :value Einträge enthalten.',
        'file' => ':attribute muss mindestens :value Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :value betragen.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => ':attribute ist ungültig.',
    'in_array' => ':attribute muss in :other enthalten sein.',
    'integer' => ':attribute muss eine ganze Zahl sein.',
    'ip' => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json' => ':attribute muss ein gültiges JSON sein.',
    'lowercase' => ':attribute darf nur Kleinbuchstaben enthalten.',
    'lt' => [
        'array' => ':attribute darf maximal :value Einträge enthalten.',
        'file' => ':attribute muss kleiner als :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner als :value sein.',
        'string' => ':attribute muss kürzer als :value Zeichen sein.',
    ],
    'lte' => [
        'array' => ':attribute darf maximal :value Einträge enthalten.',
        'file' => ':attribute darf höchstens :value Kilobytes groß sein.',
        'numeric' => ':attribute darf maximal :value betragen.',
        'string' => ':attribute darf maximal :value Zeichen lang sein.',
    ],
    'mac_address' => ':attribute muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => ':attribute darf maximal :max Einträge enthalten.',
        'file' => ':attribute darf höchstens :max Kilobytes groß sein.',
        'numeric' => ':attribute darf maximal :max betragen.',
        'string' => ':attribute darf maximal :max Zeichen lang sein.',
    ],
    'max_digits' => ':attribute darf maximal :max Ziffern enthalten.',
    'mimes' => ':attribute muss eine Datei vom Typ :values sein.',
    'mimetypes' => ':attribute muss eine Datei im Format :values sein.',
    'min' => [
        'array' => ':attribute muss mindestens :min Einträge enthalten.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :min betragen.',
        'string' => ':attribute muss mindestens :min Zeichen enthalten.',
    ],
    'min_digits' => ':attribute muss mindestens :min Ziffern enthalten.',
    'missing' => ':attribute darf nicht angegeben werden.',
    'missing_if' => 'Das Feld :attribute muss fehlen, wenn :other „:value“ ist.',
    'missing_unless' => 'Das Feld :attribute muss fehlen, außer :other ist :value.',
    'missing_with' => 'Das Feld :attribute muss fehlen, wenn :values vorhanden ist.',
    'missing_with_all' => 'Das Feld :attribute muss fehlen, wenn :values vorhanden sind.',
    'multiple_of' => ':attribute muss ein Vielfaches von :value sein.',
    'not_in' => 'Die Auswahl :attribute ist ungültig.',
    'not_regex' => ':attribute hat ein ungültiges Format.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => [
        'letters' => ':attribute muss mindestens einen Buchstaben enthalten.',
        'mixed' => ':attribute muss mindestens einen Klein- und einen Großbuchstaben enthalten.',
        'numbers' => ':attribute muss mindestens eine Zahl enthalten.',
        'symbols' => ':attribute muss mindestens ein Sonderzeichen enthalten.',
        'uncompromised' => 'Das :attribute wurde in einem Datenleck gefunden. Bitte wählen Sie ein anderes :attribute.',
    ],
    'present' => ':attribute muss vorhanden sein.',
    'prohibited' => ':attribute darf nicht angegeben werden.',
    'prohibited_if' => ':attribute darf nicht angegeben werden, wenn :other ":value" ist.',
    'prohibited_unless' => ':attribute darf nur angegeben werden, wenn :other den Wert ":values" hat.',
    'prohibits' => ':attribute darf nicht gemeinsam mit :other angegeben werden.',
    'regex' => ':attribute hat ein ungültiges Format.',
    'required' => ':attribute ist ein Pflichtfeld.',
    'required_array_keys' => ':attribute muss Einträge für folgende Werte enthalten: :values.',
    'required_if' => ':attribute ist erforderlich, wenn :other ":value" ist.',
    'required_if_accepted' => ':attribute ist erforderlich, wenn :other akzeptiert wird.',
    'required_unless' => ':attribute ist erforderlich, außer wenn :other den Wert ":values" hat.',
    'required_with' => ':attribute ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all' => ':attribute ist erforderlich, wenn alle Felder :values ausgefüllt sind.',
    'required_without' => ':attribute ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => ':attribute ist erforderlich, wenn keines der Felder :values ausgefüllt ist.',
    'same' => ':attribute muss mit :other übereinstimmen.',
    'size' => [
        'array' => ':attribute muss genau :size Einträge enthalten.',
        'file' => ':attribute muss :size Kilobytes groß sein.',
        'numeric' => ':attribute muss genau :size betragen.',
        'string' => ':attribute muss genau :size Zeichen lang sein.',
    ],
    'starts_with' => ':attribute muss mit einem der folgenden Werte beginnen: :values.',
    'string' => ':attribute muss ein Text sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'unique' => ':attribute wurde bereits verwendet.',
    'uploaded' => ':attribute konnte nicht hochgeladen werden.',
    'uppercase' => ':attribute darf nur Großbuchstaben enthalten.',
    'url' => ':attribute muss eine gültige URL sein.',
    'ulid' => ':attribute muss eine gültige ULID sein.',
    'uuid' => ':attribute muss eine gültige UUID sein.',

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
            'rule-name' => 'custom-message',
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
        'address' => 'Adresse',
        'age' => 'Alter',
        'body' => 'Inhalt',
        'cell' => 'Zelle',
        'city' => 'Stadt',
        'country' => 'Land',
        'date' => 'Datum',
        'day' => 'Tag',
        'excerpt' => 'Zusammenfassung',
        'first_name' => 'Vorname',
        'gender' => 'Geschlecht',
        'marital_status' => 'Familienstand',
        'profession' => 'Beruf',
        'nationality' => 'Nationalität',
        'hour' => 'Stunde',
        'last_name' => 'Nachname',
        'message' => 'Nachricht',
        'minute' => 'Minute',
        'mobile' => 'Handynummer',
        'month' => 'Monat',
        'name' => 'Name',
        'zipcode' => 'Postleitzahl',
        'company_name' => 'Firmenname',
        'neighborhood' => 'Stadtteil',
        'number' => 'Nummer',
        'password' => 'Passwort',
        'phone' => 'Telefonnummer',
        'second' => 'Sekunde',
        'sex' => 'Geschlecht',
        'state' => 'Bundesland',
        'street' => 'Straße',
        'subject' => 'Betreff',
        'text' => 'Text',
        'time' => 'Zeit',
        'title' => 'Titel',
        'username' => 'Benutzername',
        'year' => 'Jahr',
        'description' => 'Beschreibung',
        'password_confirmation' => 'Passwort bestätigen',
        'current_password' => 'Aktuelles Passwort',
        'complement' => 'Zusatz',
        'modality' => 'Modalität',
        'category' => 'Kategorie',
        'blood_type' => 'Blutgruppe',
        'birth_date' => 'Geburtsdatum',
    ],
];
