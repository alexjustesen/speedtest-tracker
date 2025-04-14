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

    'accepted' => 'Le champ :attribute doit être valide.',
    'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champ :attribute doit être une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure à :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale à :date.',
    'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, des chiffres, des tirets ou underscore.',
    'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'ascii' => 'Le champ :attribute ne doit contenir que des caractères alphanumériques ou des symboles ascii.',
    'before' => 'Le champ :attribute doit être une date antérieure à :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale à :date.',
    'between' => [
        'array' => 'Le champ :attribute doit contenir entre :min et :max elements.',
        'file' => 'Le champ :attribute doit être compris entre :min et :max kilo-octets.',
        'numeric' => 'Le champ :attribute doit être comprise entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed' => 'Le champ de confirmation :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute doit correspondre au format :format.',
    'decimal' => 'Le champ :attribute doit avoir :decimal chiffres décimaux.',
    'declined' => 'Le champ :attribute doit être refusé.',
    'declined_if' => 'Le champ :attribute doit être rejeté lorsque :other est :value.',
    'different' => 'Le champ :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit être composé de :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit être compris entre :min et :max.',
    'dimensions' => 'Le champ :attribute taille de la photo non valide.',
    'distinct' => 'Le champ :attribute a une valeur dupliquée.',
    'doesnt_end_with' => 'Le champ :attribute ne doit pas se terminer par l\'un des éléments suivants: :values.',
    'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par l\'un des éléments suivants: :values.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par l\'un des éléments suivants: :values.',
    'enum' => ':attribute séléctionné non valide.',
    'exists' => ':attribute existe déjà.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit contenir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit contenir plus de :value éléments.',
        'file' => 'Le champ :attribute doit être supérieur à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
        'string' => 'Le champ :attribute doit faire plus de :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit contenir au moins :value éléments.',
        'file' => 'Le champ :attribute doit être supérieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'string' => 'Le champ :attribute doit être supérieur ou égal à :value caractères.',
    ],
    'image' => 'Le champ :attribute doit être une photo.',
    'in' => ':attribute séléctionné non valide.',
    'in_array' => 'Le champ :attribute doit être contenant dans :other.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être une string JSON valide.',
    'lowercase' => 'Le champ :attribute doit être en minuscule.',
    'lt' => [
        'array' => 'Le champ :attribute doit contenir moins de :value elements.',
        'file' => 'Le champ :attribute doit être inférieur à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur à :value.',
        'string' => 'Le champ :attribute doit faire moins de :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute ne doit pas comporter plus de :value éléments.',
        'file' => 'Le champ :attribute doit être inférieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'string' => 'Le champ :attribute doit être inférieur ou égal à :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas comporter plus de :max éléments.',
        'file' => 'Le champ :attribute ne doit pas être supérieur à :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
        'string' => 'Le champ :attribute non ne doit pas faire plus de :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute ne peut avoir plus de :max chiffres.',
    'mimes' => 'Le champ :attribute doit être un fichier de type: :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type: :values.',
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file' => 'Le champ :attribute doit être au minimum de :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute deve contenir au moins :min caractères.',
    ],
    'min_digits' => 'Le champ :attribute doit avoir au moins :min chiffres.',
    'missing' => 'Le champ :attribute doit être manquant.',
    'missing_if' => 'Le champ :attribute doit être absent lorsque :other est :value.',
    'missing_unless' => 'Le champ :attribute doit être manquant, sauf si :other est :value.',
    'missing_with' => 'Le champ :attribute doit être absent lorsque :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être manquant lorsque :values sont présentes.',
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => ':attribute séléctionné non valide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être numérique.',
    'password' => [
        'letters' => 'Le champ :attribute doit cotenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit conteniur au moins un caractère minuscule et un caractère majuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole spécial.',
        'uncompromised' => 'L\':attribute est apparu dans une fuite de données. Veuillez choisir un autre :attribut',
    ],
    'present' => 'Le champ :attribute doit être présent.',
    'prohibited' => 'Le champ :attribute est interdit',
    'prohibited_if' => 'Le champ :attribute est interdit quand :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit à moins que :other ne figure dans :values.',
    'prohibits' => 'Le champ :attribute interdit à :other d\'être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour: :values.',
    'required_if' => 'Le champ :attribute est obligatoire quand :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est nécessaire lorsque :other est accepté.',
    'required_unless' => 'Le champ :attribute est obligatoire, sauf si :other figure dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_without' => 'Le champ :attribute  est requis lorsque :values n\'est pas présent',
    'required_without_all' => 'Le champ :attribute est nécessaire lorsqu\'aucune des valeurs :values n\'est présente.',
    'same' => 'Le champ :attribute ne doit pas être identique à :other',
    'size' => [
        'array' => 'Le champ :attribute doit contenir des :size éléments.',
        'file' => 'Le champ :attribute doit être :size kilo-octets.',
        'numeric' => 'Le champ :attribute doit être :size.',
        'string' => 'Le champ :attribute doit faire :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer avec: :values.',
    'string' => 'Le champ :attribute doit être une chaine de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'Il :attribute doit être unqieu.',
    'uploaded' => 'Il :attribute n\'a pas pu être téléchargé.',
    'uppercase' => 'Le champ :attribute doit être en majuscule.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'ulid' => 'Le champ :attribute doit être un ULID valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

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
        'address' => 'adresse',
        'age' => 'age',
        'body' => 'contenu',
        'cell' => 'cellule',
        'city' => 'ville',
        'country' => 'pays',
        'date' => 'date',
        'day' => 'jour',
        'excerpt' => 'résumé',
        'first_name' => 'prénom',
        'gender' => 'sexe',
        'marital_status' => 'situation familliale',
        'profession' => 'profession',
        'nationality' => 'nationalité',
        'hour' => 'heure',
        'last_name' => 'nom de famille',
        'message' => 'message',
        'minute' => 'minute',
        'mobile' => 'mobile',
        'month' => 'mois',
        'name' => 'nom',
        'zipcode' => 'code postal',
        'company_name' => 'entreprise',
        'neighborhood' => 'quartier',
        'number' => 'numéro',
        'password' => 'mot de passe',
        'phone' => 'téléphone',
        'second' => 'seconde',
        'sex' => 'sexe',
        'state' => 'région',
        'street' => 'rue',
        'subject' => 'sujet',
        'text' => 'texte',
        'time' => 'temps',
        'title' => 'titre',
        'username' => 'login',
        'year' => 'année',
        'description' => 'description',
        'password_confirmation' => 'confirmation du mot de passe',
        'current_password' => 'mot de passe actuel',
        'complement' => 'complément',
        'modality' => 'modalité',
        'category' => 'catégorie',
        'blood_type' => 'groupe sanguin',
        'birth_date' => 'date de naissance',
    ],
];
